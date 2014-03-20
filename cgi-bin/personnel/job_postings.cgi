#!/usr/bin/perl

#####################################################################################################
# Copyright 2007 by Southwest Educational Development Laboratory
#
# This script is used by OFTS to manage online job vacancy postings
# Written by Brian Litke 01-09-2007
#####################################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;
use Number::Format; # ROUNDS NUMBERS TO SPECIFIC DECIMAL PLACE

use LWP::Simple; # FOR TRIGGERING AN EXTERNAL PHP FILE

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode;
use intranetcommoncode;
################################################
## END: LOAD PERL MODULES USED BY THIS SCRIPT
################################################

################################################
## END: LOAD PERL MODULES USED BY THIS SCRIPT
################################################

my $query = new CGI;
my $debug = $query->param("debug") || "0"; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS

##########################################
# START: GRAB CGI ENVIRONMENTAL VARIABLES
##########################################
my $browser = $ENV{"HTTP_USER_AGENT"};
my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};
##########################################
# END: GRAB CGI ENVIRONMENTAL VARIABLES
##########################################

###################################
## START: COOKIE DEFAULT VARIABLES
###################################
my $expdate = "Fri, 25-Dec-2015 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";
###################################
## END: COOKIE DEFAULT VARIABLES
###################################

####################################
## START: GET THE CURRENT DATE INFO
####################################
	use POSIX;
	my $todaysdate = POSIX::strftime('%b %e, %Y, %X', localtime(time)); # (e.g. Mar 6, 2008, 14:39:38)
	my $year = POSIX::strftime('%Y', localtime(time)); # Locale's year (e.g. 2008)
	my $month = POSIX::strftime('%m', localtime(time)); # Locale's numerical month (e.g. 03)
	my $month_name_abbr = POSIX::strftime('%b', localtime(time)); # Locale's abbreviated month name (e.g. Mar)
	my $month_name_full = POSIX::strftime('%B', localtime(time)); # Locale's full month name (e.g. March)
	my $monthdate = POSIX::strftime('%e', localtime(time)); # Date in month (e.g. 6)
	my $monthdate_wleadingzero = POSIX::strftime('%d', localtime(time)); # Date in month w/leadingzero (e.g. 06)
	my $weekday_name_abbr = POSIX::strftime('%a', localtime(time)); # Locale's abbreviated weekday name. (e.g. Thu)
	my $weekday_name_full = POSIX::strftime('%A', localtime(time)); # Locale's full weekday name. (e.g. Thursday)
	my $date_full_pretty = POSIX::strftime('%D', localtime(time)); # Full date in human-readable format  (e.g. 03/06/08)
	my $date_full_mysql = POSIX::strftime('%F', localtime(time)); # Full date in machine-readable "mysql-compatible" format (e.g. 2008-03-06)
	my $time_hour = POSIX::strftime('%l', localtime(time)); # Hour (e.g. 9 or 9)
	my $time_hour_leadingzero = POSIX::strftime('%I', localtime(time)); # Hour w/leadingsero (e.g. 09 or 09)
	my $time_hour_mil = POSIX::strftime('%k', localtime(time)); # Hour in military notation (e.g. 9 or 21)
	my $time_hour_mil_leadingzero = POSIX::strftime('%H', localtime(time)); # Hour in military notation w/leadingsero (e.g. 09 or 21)
	my $time_min = POSIX::strftime('%M', localtime(time)); # Minutes (e.g. 39)
	my $time_sec = POSIX::strftime('%S', localtime(time)); # Seconds (e.g. 38)

	my $timestamp = "$year$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; # 14-digit timestamp (e.g. 20080306143938)

my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
	my $this_user_id = "$timestamp$ipnum$ipnum2";
	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################



########################################
## START: READ VARIABLES PASSED BY USER
########################################
my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
   $logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $logon_pass = $query->param("logon_pass");

my $location = param('location');
   $location = "menu" if $location eq '';

my $error_message = "";
my $feedback_message = "";
my $show_record = $query->param("show_record");
   $show_record = &commoncode::cleanthisfordb($show_record);
my $sortby = $query->param("sortby");
   $sortby = "sort_order" if ($sortby eq '');
my $confirm = $query->param("confirm");
########################################
## END: READ VARIABLES PASSED BY USER
########################################

###########################################
# START: GRAB SEDL HEADER AND FOOTER HTML #
###########################################
my $htmlhead = "";
my $htmltail = "";

open(HTMLHEAD,"</home/httpd/html/staff/includes/header_withside2012.txt");
while (<HTMLHEAD>) {
	$htmlhead .= $_;
}
close(HTMLHEAD);

open(HTMLTAIL,"</home/httpd/html/staff/includes/footer_withside2012.txt");
while (<HTMLTAIL>) {
	$htmltail .= $_;
}
close(HTMLTAIL);

my $side_nav_menu_code = "";
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("425"); # 425 is the PID for the "Budget Reports" page on the intranet

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";

	################################################
	## START: SHOW SLIM TEMPLATE IF AN INSIDE PAGE
	################################################
	my $htmlhead_noside = "";
	my $htmltail_noside = "";
	open(HTMLHEAD,"</home/httpd/html/staff/includes/header2012.txt");
	while (<HTMLHEAD>) {
		$htmlhead_noside .= $_;
	}
	close(HTMLHEAD);

	open(HTMLTAIL,"</home/httpd/html/staff/includes/footer2012.txt");
	while (<HTMLTAIL>) {
		$htmltail_noside .= $_;
	}
	close(HTMLTAIL);

	$htmlhead_noside .= "<div style=\"padding:15px;\">\n";
	$htmltail_noside = "</div>\n$htmltail";

	if ($location eq 'add_vacancy') {
		$htmlhead = $htmlhead_noside;
		$htmltail = $htmltail_noside;
	}
	################################################
	## END: SHOW SLIM TEMPLATE IF AN INSIDE PAGE
	################################################

###########################################
# END: GRAB SEDL HEADER AND FOOTER HTML #
###########################################


####################################################
# START: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################
my $cookie_ss_session_id = ""; # TRACK SESSION ID
my $cookie_ss_staff_id = ""; # TRACK USER ID
	my(%cookies) = getCookies();
	foreach (sort(keys(%cookies))) {
		$cookie_ss_session_id = $cookies{$_} if (($_ eq 'ss_session_id') && ($session_id eq ''));
		$cookie_ss_staff_id = $cookies{$_} if ($_ eq 'staffid');
	}
	$cookie_ss_session_id = $session_id if (($cookie_ss_session_id eq '') && ($session_id ne '')); # IF NOT SESSION IN COOKIE, BUT ONE PASSED VIA URL, USE SESSION FROM URL
	$cookie_ss_session_id = &commoncode::cleanthisfordb($cookie_ss_session_id);
####################################################
# END: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################

######################################################
## START: LOCATION = PROCESS_LOGON
######################################################
if ($location eq 'process_logon') {
	if (($logon_user ne '') && ($logon_pass ne '')) {
		## CHECK LOGON
		my $strong_pwd = crypt($logon_pass,'password');
		my $command = "select userid from staff_profiles where
			((userid like '$logon_user') AND (strong_pwd LIKE '$strong_pwd') )";
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;

		my $command = "select userid from staff_profiles where
			(userid like '$logon_user')";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_for_logon_id_entered = $sth->rows;

		if ($num_matches eq '1') {
			$cookie_ss_session_id = "$logon_user$session_suffix";
			$cookie_ss_session_id = &commoncode::cleanthisfordb($cookie_ss_session_id);
			## VALID ID/PASSWORD, SET SESSION
				my $command_set_session = "REPLACE into staff_sessions VALUES ('$cookie_ss_session_id', '$logon_user', '$timestamp', '$ipnum2', '', '', '', '')";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_set_session) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			## SAVE COOKIES WITH SESSION ID AND USER ID
				$cookie_ss_staff_id = $logon_user;
				setCookie ("ss_session_id", "$cookie_ss_session_id ", $expdate, $path, $thedomain);
				setCookie ("staffid", $logon_user, $expdate, $path, $thedomain);

			## SET LOCATION
				$location = "menu";

		} else {
			## INVALID ID/PASS: SHOW LOON SCREEN & SET ERROR MESSAGE
			if ($num_matches_for_logon_id_entered == 1) {
				$error_message = "Your User ID ($logon_user) is valid, but the password you entered did not match the one on file.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
			} else {
				if (length($logon_user) > 8) {
					$error_message = "The User ID you entered ($logon_user) is over 8 characters long.  The intranet logon is your first initial and last name, with a maximum limit of 8 characters.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
				} else {
					$error_message = "The User ID you entered ($logon_user) is not a valid SEDL staff ID.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
				}
			}
			$location = "logon"; # SHOW LOGON SCREEN
		}
	} else {
	## USER DIDN't ENTER USER ID OR PASSWORD, SHOW LOON SCREEN & SET ERROR MESSAGE
		$error_message .= "You forgot to enter your User ID (ex: whoover)." if ($logon_user eq '');
		$error_message .= "You forgot to enter your password." if ($logon_pass eq '');
	}
}
######################################################
## END: LOCATION = PROCESS_LOGON
######################################################


######################################################
## START: LOCATION = LOGOUT
######################################################
if ($location eq 'logout') {
	## DELETE SESSION IN RF_SESSION DB
	my $command_delete_session = "DELETE FROM staff_sessions WHERE ss_session_id='$cookie_ss_session_id'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_delete_session) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;
	setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
	$cookie_ss_session_id = "";
	$location = "logon"; # AFTER LOGOUT, SHOW LOGON SCREEN
}
######################################################
## END: LOCATION = LOGOUT
######################################################


######################################################
## START: CHECK SESSION ID AND VERIFY
######################################################
	## IF SESSION ID NOT FOUND, PROMPT FOR LOGON
	if ($cookie_ss_session_id eq '') {
		$location = "logon";
	} else {
	## IF SESSION ID FOUND, CHECK IF CURRENT
	my $command = "select * from staff_sessions where ss_session_id like '$cookie_ss_session_id'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";

		while (my @arr = $sth->fetchrow) {
		my ($ss_session_id, $ss_staff_id, $ss_session_last_active, $ss_ip_number, $ss_session_active, $ss_staff_project, $ss_search_site, $ss_last_page_viewed) = @arr;
			## IF SESSION STILL ACTIVE, SET TIMESTAMP IN SESSION DB
				my $command_update_session = "REPLACE into staff_sessions VALUES ('$ss_session_id', '$ss_staff_id', '$timestamp', '$ipnum2', '', '', '' ,'')";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
#my $num_matches = $sth->rows;

			## SAVE COOKIES WITH SESSION ID AND USER ID
				setCookie ("ss_session_id", $ss_session_id, $expdate, $path, $thedomain);
				setCookie ("staffid", $ss_staff_id, $expdate, $path, $thedomain);
			## SET LOCATION
				$location = "menu" if ($location eq '');

		} # END DB QUERY LOOP

		## IF SESSION NOT ACTIVE, PROMPT FOR LOGON
		if ($num_matches eq '0') {
			$cookie_ss_session_id = "";
			setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
			$location = "logon"; # AFTER LOGOUT, SHOW LOGON SCREEN
		}
	}
######################################################
## END: CHECK SESSION ID AND VERIFY
######################################################


my $logonuser_is_hr_representative = "no";
   $logonuser_is_hr_representative = "yes" if ($cookie_ss_staff_id eq 'blitke');
   $logonuser_is_hr_representative = "yes" if ($cookie_ss_staff_id eq 'brollins');
   $logonuser_is_hr_representative = "yes" if ($cookie_ss_staff_id eq 'sliberty');
   $logonuser_is_hr_representative = "yes" if ($cookie_ss_staff_id eq 'mturner');
   $logonuser_is_hr_representative = "yes" if ($cookie_ss_staff_id eq 'sferguso');
   $logonuser_is_hr_representative = "yes" if ($cookie_ss_staff_id eq 'ewaters');
if ($logonuser_is_hr_representative ne 'yes') {
	$location = 'logon';
	$error_message = "This web page is only available to SEDL's HR staff.  Contact Maria Turner or Brian litke for assistance.";
}




#################################################################################
## START: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################
if ($location eq 'logon') {

## PRINT SIGNUP FORM
print header;
print <<EOM;
<HTML>
<HEAD>
<TITLE>SEDL Intranet | SEDL Job Postings Database</TITLE>
$htmlhead

<h1 style="margin-top:0px;">SEDL Job Postings Database</h1>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';

print<<EOM;
<p>
Welcome to the SEDL Job Postings Database. This database is used by AS staff (Maria, Sue) to set up notices of vacancy on the SEDL Web site and intranet.
Please enter your SEDL user ID and password to view the database.
</p>
<form ACTION="job_postings.cgi" METHOD=POST>
<div>
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP"><strong>Your user ID</strong><br>
		(ex: sliberty)</TD>
	<TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
<TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR>
		<SPAN class=small>(not your email password)</SPAN></TD>
	<TD WIDTH="420" VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD>
</TR>
</TABLE>

	<div style="margin-left:25px;">
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
	<INPUT TYPE="SUBMIT" VALUE="Log In Now">
	</div>
</div>
</form>
<p>
To report troubles using this form, send an email to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A>
or call Brian Litke at ext. 6529.
</p>
$htmltail
EOM
}
#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################


##########################################################
## START: LOCATION PROCESS_DELETE_VACANCY
##########################################################
if ($location eq 'process_delete_vacancy') {
	if ($confirm eq 'confirmed') {

		## DELETE THE PAGES
		my $command_delete = "DELETE from job_vacancies WHERE recordid = '$show_record'";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command_delete) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		$feedback_message = "You successfully deleted the job posting. ($command_delete)";
		$location = "menu";
	} else {
		$error_message = "ERROR: Deletion Aborted. You forgot to checkmark the CONFIRM box.";
		$location = "add_vacancy";
	}
}
##########################################################
## END: LOCATION PROCESS_DELETE_VACANCY
##########################################################



#################################################################################
## START: LOCATION = PROCESS_ADD_VACANCY
#################################################################################

if ($location eq 'process_add_vacancy') {
	my $new_position_title = $query->param("new_position_title");
	my $new_description = $query->param("new_description");
	my $new_position_exempt_status = $query->param("new_position_exempt_status");
	my $new_position_location = $query->param("new_position_location");
	my $new_sedl_unit = $query->param("new_sedl_unit");
	my $new_responsibility_leadin = $query->param("new_responsibility_leadin");
	my $new_responsibility_1_desc = $query->param("new_responsibility_1_desc");
	my $new_responsibility_2_desc = $query->param("new_responsibility_2_desc");
	my $new_responsibility_3_desc = $query->param("new_responsibility_3_desc");
	my $new_responsibility_4_desc = $query->param("new_responsibility_4_desc");
	my $new_responsibility_5_desc = $query->param("new_responsibility_5_desc");
	my $new_responsibility_6_desc = $query->param("new_responsibility_6_desc");
	my $new_responsibility_7_desc = $query->param("new_responsibility_7_desc");
	my $new_responsibility_8_desc = $query->param("new_responsibility_8_desc");
	my $new_responsibility_9_desc = $query->param("new_responsibility_9_desc");
	my $new_responsibility_10_desc = $query->param("new_responsibility_10_desc");
	my $new_responsibility_11_desc = $query->param("new_responsibility_11_desc");
	my $new_qualifications = $query->param("new_qualifications");
	my $new_experience = $query->param("new_experience");
	my $new_salary = $query->param("new_salary");
	my $new_position_opens = $query->param("new_position_opens");
	my $new_position_closes = $query->param("new_position_closes");
	my $new_position_closes_review_begins = $query->param("new_position_closes_review_begins");
	my $new_sync_w_filemaker = $query->param("new_sync_w_filemaker");
	my $new_revised = $query->param("new_revised");
	my $new_show_onweb = $query->param("new_show_onweb");
	my $new_web_sort_order = $query->param("new_web_sort_order");
	my $new_quantity = $query->param("new_quantity");
	my $new_travel = $query->param("new_travel");
	my $new_funding = $query->param("new_funding");
	my $new_date_to_start_display ="";
		my $new_date_to_start_display_m = $query->param("new_date_to_start_display_m") || "01";
		my $new_date_to_start_display_d = $query->param("new_date_to_start_display_d") || "01";
		my $new_date_to_start_display_y = $query->param("new_date_to_start_display_y") || $year;
	   $new_date_to_start_display = "$new_date_to_start_display_y\-$new_date_to_start_display_m\-$new_date_to_start_display_d";

	## START: BACKSLASH VARIABLES FOR DB
	$new_position_title = &commoncode::cleanthisfordb($new_position_title);
	$new_description = &commoncode::cleanthisfordb($new_description);
	$new_position_exempt_status = &commoncode::cleanthisfordb($new_position_exempt_status);
	$new_position_location = &commoncode::cleanthisfordb($new_position_location);
	$new_sedl_unit = &commoncode::cleanthisfordb($new_sedl_unit);
	$new_responsibility_leadin = &commoncode::cleanthisfordb($new_responsibility_leadin);
	$new_responsibility_1_desc = &commoncode::cleanthisfordb($new_responsibility_1_desc);
	$new_responsibility_2_desc = &commoncode::cleanthisfordb($new_responsibility_2_desc);
	$new_responsibility_3_desc = &commoncode::cleanthisfordb($new_responsibility_3_desc);
	$new_responsibility_4_desc = &commoncode::cleanthisfordb($new_responsibility_4_desc);
	$new_responsibility_5_desc = &commoncode::cleanthisfordb($new_responsibility_5_desc);
	$new_responsibility_6_desc = &commoncode::cleanthisfordb($new_responsibility_6_desc);
	$new_responsibility_7_desc = &commoncode::cleanthisfordb($new_responsibility_7_desc);
	$new_responsibility_8_desc = &commoncode::cleanthisfordb($new_responsibility_8_desc);
	$new_responsibility_9_desc = &commoncode::cleanthisfordb($new_responsibility_9_desc);
	$new_responsibility_10_desc = &commoncode::cleanthisfordb($new_responsibility_10_desc);
	$new_responsibility_11_desc = &commoncode::cleanthisfordb($new_responsibility_11_desc);
	$new_qualifications = &commoncode::cleanthisfordb($new_qualifications);
	$new_experience = &commoncode::cleanthisfordb($new_experience);
	$new_salary = &commoncode::cleanthisfordb($new_salary);
	$new_position_opens = &commoncode::cleanthisfordb($new_position_opens);
	$new_position_closes = &commoncode::cleanthisfordb($new_position_closes);
	$new_position_closes_review_begins = &commoncode::cleanthisfordb($new_position_closes_review_begins);
	$new_sync_w_filemaker = &commoncode::cleanthisfordb($new_sync_w_filemaker);
	$new_revised = &commoncode::cleanthisfordb($new_revised);
	$new_show_onweb = &commoncode::cleanthisfordb($new_show_onweb);
	$new_web_sort_order = &commoncode::cleanthisfordb($new_web_sort_order);
	$new_quantity = &commoncode::cleanthisfordb($new_quantity);
	$new_travel = &commoncode::cleanthisfordb($new_travel);
	$new_funding = &commoncode::cleanthisfordb($new_funding);
	$new_date_to_start_display = &commoncode::cleanthisfordb($new_date_to_start_display);
	## END: BACKSLASH VARIABLES FOR DB

	## START: CHECK FOR DATA COPLETENESS
	if ($new_position_title eq '') {
		$new_position_title = "Position Title Missing";
	}
	## END: CHECK FOR DATA COPLETENESS

	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select recordid, position_title
					from job_vacancies WHERE recordid = '$show_record'";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;
		$already_exists = "yes" if ($num_matches_code ne '0');


	my $add_edit_type = "added";

		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update = "UPDATE job_vacancies
										SET position_title = '$new_position_title', description = '$new_description', position_exempt_status = '$new_position_exempt_status', position_location = '$new_position_location', sedl_unit = '$new_sedl_unit', responsibility_leadin = '$new_responsibility_leadin', responsibility_1_desc = '$new_responsibility_1_desc', responsibility_2_desc = '$new_responsibility_2_desc', responsibility_3_desc = '$new_responsibility_3_desc', responsibility_4_desc = '$new_responsibility_4_desc', responsibility_5_desc = '$new_responsibility_5_desc', responsibility_6_desc = '$new_responsibility_6_desc', responsibility_7_desc = '$new_responsibility_7_desc', responsibility_8_desc = '$new_responsibility_8_desc', responsibility_9_desc = '$new_responsibility_9_desc', responsibility_10_desc = '$new_responsibility_10_desc', responsibility_11_desc = '$new_responsibility_11_desc', qualifications = '$new_qualifications', experience = '$new_experience', salary = '$new_salary', position_opens = '$new_position_opens', position_closes = '$new_position_closes', position_closes_review_begins = '$new_position_closes_review_begins', last_modified = '$timestamp', revised = '$new_revised', show_onweb = '$new_show_onweb', web_sort_order = '$new_web_sort_order', quantity = '$new_quantity', travel = '$new_travel', funding = '$new_funding', date_to_start_display = '$new_date_to_start_display'
										WHERE recordid='$show_record'";
			my $dsn = "DBI:mysql:database=corp;host=localhost";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_update) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			$add_edit_type = "edited";
			$feedback_message .= "The Job Posting was $add_edit_type successfully.";
			$feedback_message .= "The record you edited is <a href=\"#$show_record\">highlighted in yellow below</a>." if ($show_record ne '');
			$location = "menu";

			################################
			## START: UPDATE FILEMAKER
			################################
			## FIRST, UPDATE THIS JOB POSTING TO SET ITS SYNC_WITH_FILEMAKER STATUS TO 'UPDATE' SO FILEMAKER WILL KNOW NOT TO SHOW IT
			my $command_update_status = "UPDATE job_vacancies set sync_w_filemaker = 'update' WHERE recordid = '$show_record'";
			my $dsn = "DBI:mysql:database=corp;host=localhost";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_update_status) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			################################
			## END: UPDATE FILEMAKER
			################################

		} else {

			my $command_insert = "INSERT INTO job_vacancies VALUES ('$new_position_title', '$new_description', '$new_position_exempt_status', '$new_position_location', '$new_sedl_unit', '$new_responsibility_leadin', '$new_responsibility_1_desc', '$new_responsibility_2_desc', '$new_responsibility_3_desc', '$new_responsibility_4_desc', '$new_responsibility_5_desc', '$new_responsibility_6_desc', '$new_responsibility_7_desc', '$new_responsibility_8_desc', '$new_responsibility_9_desc', '$new_responsibility_10_desc', '$new_responsibility_11_desc', '$new_qualifications', '$new_experience', '$new_salary', '$new_position_opens', '$new_position_closes', '$new_position_closes_review_begins', '$new_sync_w_filemaker', '$timestamp', '', '$new_revised', '$new_show_onweb', '$new_web_sort_order', '', '$new_quantity', '$new_travel', '$new_funding', '$new_date_to_start_display')";
			my $dsn = "DBI:mysql:database=corp;host=localhost";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_insert) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			$feedback_message .= "The Job Posting was $add_edit_type successfully.";
			$location = "menu";
		} # END IF USER NAME NOT BLANK

		################################
		## START: UPDATE FILEMAKER
		################################
		## TRIGGER THE FILEMAKER SYNC
		my $content_from_url = get("http://www.sedl.org/staff/sims/positions_novs_mysql.php");
		################################
		## END: UPDATE FILEMAKER
		################################

}
#################################################################################
## END: LOCATION = PROCESS_ADD_VACANCY
#################################################################################


#################################################################################
## START: LOCATION = ADD_VACANCY
#################################################################################
if ($location eq 'add_vacancy') {
	my $page_title = "Add a New Notice of Job Vacancy";

my $position_title = "";
my $description = "";
my $position_exempt_status = "";
my $position_location = "";
my $sedl_unit = "";
my $responsibility_leadin = "";
my $responsibility_1_desc = "";
my $responsibility_2_desc = "";
my $responsibility_3_desc = "";
my $responsibility_4_desc = "";
my $responsibility_5_desc = "";
my $responsibility_6_desc = "";
my $responsibility_7_desc = "";
my $responsibility_8_desc = "";
my $responsibility_9_desc = "";
my $responsibility_10_desc = "";
my $responsibility_11_desc = "";
my $qualifications = "";
my $experience = "";
my $salary = "";
my $position_opens = "";
my $position_closes = "";
my $position_closes_review_begins = "";
my $sync_w_filemaker = "";
my $created = "";
my $last_modified = "";
my $revised = "";
my $show_onweb = "";
my $web_sort_order = "";
my $recordid = "";
my $quantity = "";
my $travel = "";
my $funding = "";
my $date_to_start_display = "";

	if ($show_record ne '') {
		$page_title = "Edit New Notice of Job Vacancy";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from job_vacancies WHERE recordid = '$show_record'";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_pubs = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			($position_title, $description, $position_exempt_status, $position_location, $sedl_unit, $responsibility_leadin, $responsibility_1_desc, $responsibility_2_desc, $responsibility_3_desc, $responsibility_4_desc, $responsibility_5_desc, $responsibility_6_desc, $responsibility_7_desc, $responsibility_8_desc, $responsibility_9_desc, $responsibility_10_desc, $responsibility_11_desc, $qualifications, $experience, $salary, $position_opens, $position_closes, $position_closes_review_begins, $sync_w_filemaker, $created, $last_modified, $revised, $show_onweb, $web_sort_order, $recordid, $quantity, $travel, $funding, $date_to_start_display) = @arr;
			$created = commoncode::convert_timestamp_2pretty_w_date($created, "yes");
		} # END DB QUERY LOOP
	} # END IF

print header;
print <<EOM;
<HTML>
<HEAD>
<script language="javascript" type="text/javascript" src="/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	plugins : "spellchecker,table,paste",
	gecko_spellcheck : true,
	theme_advanced_buttons1_add : "pastetext,pasteword",
	theme_advanced_buttons3_add : "tablecontrols, spellchecker",
	table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
	table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
	table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
	table_cell_limit : 100,
	table_row_limit : 5,
	table_col_limit : 5,
    force_br_newlines : true,
    force_p_newlines : false,
	forced_root_block : false,
	paste_auto_cleanup_on_paste : true,
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
	content_css: "/staff/includes/staff2006_tinymce.css",
	apply_source_formatting : true,
	convert_urls : false
});
</script>

<TITLE>SEDL Intranet | SEDL Job Postings Database</TITLE>
$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1 style="margin-top:0px;"><A HREF="job_postings.cgi">SEDL Job Postings Database</A><br>
$page_title</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="job_postings.cgi?location=logout">logout</A>)
	</td></tr>
</table>


EOM
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
print<<EOM;
<FORM ACTION="job_postings.cgi" METHOD="POST">

<table border="1" cellpadding="2" cellspacing="0">
<tr><td valign="top"><strong><label for="new_show_onweb">Show on the Web Site?</label></strong></td>
	<td valign="top">
EOM
$show_onweb = "no" if ($show_onweb eq '');
&commoncode::printform_yes_no_menu("new_show_onweb", $show_onweb);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong><label for="new_show_onweb">Delay showing until this date</label></strong></td>
	<td valign="top">
EOM
## START: SPLIT UP EXISTING DATE VALUE AND SEND TO SUBROUTINES TO PRINT PULL-MENUS FOR DATE
my ($this_year, $this_month, $this_date) = split(/\-/,$date_to_start_display);
&commoncode::print_month_menu("new_date_to_start_display_m", $this_month); 
&commoncode::print_day_menu("new_date_to_start_display_d", $this_date); 
&commoncode::print_year_menu_descending("new_date_to_start_display_y", 2014, $year + 1, $this_year);
## END: SPLIT UP EXISTING DATE VALUE AND SEND TO SUBROUTINES TO PRINT PULL-MENUS FOR DATE

print<<EOM;
	</td></tr>

<tr><td valign="top"><strong><label for="new_web_sort_order">Order for displaying on the Careers page</label></strong></td>
	<td valign="top">
EOM
&printform_web_sort_order("new_web_sort_order", $web_sort_order);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong><label for="new_revised">Revised?</label></strong></td>
	<td valign="top">
EOM
$revised = "no" if ($revised eq '');
&commoncode::printform_yes_no_menu("new_revised", $revised);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong><label for="new_position_title">Position Title</label></strong></td>
	<td valign="top"><INPUT TYPE="text" NAME="new_position_title" id="new_position_title" SIZE="60" VALUE="$position_title">
	</td></tr>

<tr><td valign="top"><strong><label for="new_description">Job Description</label></strong></td>
	<td valign="top"><textarea name="new_description" id="new_description" rows="30" cols="70">$description</textarea>
	</td></tr>

<tr><td valign="top"><strong><label for="new_description">Position Exempt Status</label></strong></td>
	<td valign="top">
EOM
&printform_exempt_menu("new_position_exempt_status", $position_exempt_status);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong><label for="new_position_location">Position Location</label></strong></td>
	<td valign="top">
EOM
&printform_position_location_menu("new_position_location", $position_location);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong><label for="new_sedl_unit">SEDL Unit</label></strong></td>
	<td valign="top">
EOM
&commoncode::printform_sedl_unit_menu("new_sedl_unit", $sedl_unit);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong><label for="new_quantity">Quantity to be Hired</label></strong></td>
	<td valign="top"><INPUT TYPE="text" NAME="new_quantity" id="new_quantity" SIZE="60" VALUE="$quantity"><br>
		Example: More than one may be hired. &nbsp;&nbsp;&nbsp; (Leave this blank if only one may be hired.)
	</td></tr>
<tr><td valign="top"><strong><label for="new_responsibility_leadin">Lead sentence (if any) before bulleted key responsibilities</label></strong></td>
	<td valign="top"><INPUT TYPE="text" NAME="new_responsibility_leadin" id="new_responsibility_leadin" SIZE="120" VALUE="$responsibility_leadin"><br>
		Example: Key responsibilities of the position are to:<br><br>
		Use the code below to insert a "RESPONSIBILITIES heading.<br>
		&lt;strong&gt;RESPONSIBILITIES:&lt;/strong&gt;
	</td></tr>

<tr><td valign="top"><strong><label for="new_responsibility_1_desc">Responsibility 1</label></strong></td>
	<td valign="top"><textarea name="new_responsibility_1_desc" id="new_responsibility_1_desc" rows="10" cols="70">$responsibility_1_desc</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_responsibility_2_desc">Responsibility 2</label></strong></td>
	<td valign="top"><textarea name="new_responsibility_2_desc" id="new_responsibility_2_desc" rows="10" cols="70">$responsibility_2_desc</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_responsibility_3_desc">Responsibility 3</label></strong></td>
	<td valign="top"><textarea name="new_responsibility_3_desc" id="new_responsibility_3_desc" rows="10" cols="70">$responsibility_3_desc</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_responsibility_4_desc">Responsibility 4</label></strong></td>
	<td valign="top"><textarea name="new_responsibility_4_desc" id="new_responsibility_4_desc" rows="10" cols="70">$responsibility_4_desc</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_responsibility_5_desc">Responsibility 5</label></strong></td>
	<td valign="top"><textarea name="new_responsibility_5_desc" id="new_responsibility_5_desc" rows="10" cols="70">$responsibility_5_desc</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_responsibility_6_desc">Responsibility 6</label></strong></td>
	<td valign="top"><textarea name="new_responsibility_6_desc" id="new_responsibility_6_desc" rows="10" cols="70">$responsibility_6_desc</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_responsibility_7_desc">Responsibility 7</label></strong></td>
	<td valign="top"><textarea name="new_responsibility_7_desc" id="new_responsibility_7_desc" rows="10" cols="70">$responsibility_7_desc</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_responsibility_8_desc">Responsibility 8</label></strong></td>
	<td valign="top"><textarea name="new_responsibility_8_desc" id="new_responsibility_8_desc" rows="10" cols="70">$responsibility_8_desc</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_responsibility_9_desc">Responsibility 9</label></strong></td>
	<td valign="top"><textarea name="new_responsibility_9_desc" id="new_responsibility_9_desc" rows="10" cols="70">$responsibility_9_desc</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_responsibility_10_desc">Responsibility 10</label></strong></td>
	<td valign="top"><textarea name="new_responsibility_10_desc" id="new_responsibility_10_desc" rows="10" cols="70">$responsibility_10_desc</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_responsibility_11_desc">Responsibility 11</label></strong></td>
	<td valign="top"><textarea name="new_responsibility_11_desc" id="new_responsibility_11_desc" rows="10" cols="70">$responsibility_11_desc</textarea>
	</td></tr>

<tr><td valign="top"><strong><label for="new_qualifications">Qualifications</label></strong><br>(Put "MINIMUM SKILLS REQUIRED" and "MINIMUM EDUCATION REQUIRED" here with bolded headings, if desired.)</td>
	<td valign="top"><textarea name="new_qualifications" id="new_qualifications" rows="15" cols="70">$qualifications</textarea><br>
		Example:<br>
		Earned Master's degree from an accredited university or college with an emphasis in School Improvement or Curriculum Instruction. In-depth knowledge of research on educational reform and research-based practices; knowledge of research methodology, including both quantitative and qualitative methodologies; knowledge of key programs in the Elementary and Secondary Education Act; strong oral and written communication skills; strong group presentation and facilitation skills; proficient use of microcomputers for word processing, information searching and retrieval, and communication; strong interpersonal skills that contribute to the ability to work effectively with individuals and teams with diverse language and cultural backgrounds.
	</td></tr>

<tr><td valign="top"><strong><label for="new_experience">Experience</label></strong><br>(Put "WILL BE SUPERVISED BY:  Director, SECC" here with bolded heading, if desired.)</td>
	<td valign="top"><textarea name="new_experience" id="new_experience" rows="15" cols="70">$experience</textarea><br>
	Example:<br>
	Seven years successful, relevant work experience in the areas of technical assistance and professional development; experience in providing technical assistance and consultations to state agencies, school districts, or schools in implementing programs for educational reform and improvement; experience in reviewing and synthesizing research to identify best practices, and experience in providing professional development based on research-based practices.
	</td></tr>

<tr><td valign="top"><strong><label for="new_salary">Travel</label></strong></td>
	<td valign="top"><textarea name="new_travel" id="new_travel" rows="15" cols="70">$travel</textarea><br>
	Example:<br>
	Individual must be willing and able to perform all travel (including overnight and commercial airline travel) that is necessary to accomplish the principal accountabilities associated with (and/or the essential functions of) the position. Travel for this position is estimated at 30%.
	</td></tr>

<tr><td valign="top"><strong><label for="new_salary">Salary</label></strong></td>
	<td valign="top"><textarea name="new_salary" id="new_salary" rows="15" cols="70">$salary</textarea><br>
	Example:<br>
	Program Associate is on Pay Grade 13 of SEDL's Salary Structure.  The total salary range is \$69,312 - \$104,628. The initial salary shall generally fall within the first third of the range, which is \$69,312 - \$81,084. Initial salary is dependent upon the successful applicant's relevant experience and education; plus benefits.
	</td></tr>

<tr><td valign="top"><strong><label for="new_position_opens">Position Opens</label></strong></td>
	<td valign="top"><INPUT TYPE="text" NAME="new_position_opens" id="new_position_opens" SIZE="60" VALUE="$position_opens"><br>
		Example: October 1, $year
	</td></tr>

<tr><td valign="top"><strong><label for="new_position_closes">Position Closes</label></strong></td>
	<td valign="top"><INPUT TYPE="text" NAME="new_position_closes" id="new_position_closes" SIZE="60" VALUE="$position_closes">
	</td></tr>
EOM
#<tr><td valign="top"><strong><label for="new_salary">Funding</label></strong></td>
#	<td valign="top"><textarea name="new_funding" id="new_funding" rows="15" cols="70">$funding</textarea><br>
#		Example:<br>Continued employment during, or beyond, the cited period(s) is dependent upon SEDL's needs, the availability of funds, and the staff member's satisfactory performance.  The successful applicant will be employed initially on a probationary basis generally not to exceed six months.
#	</td></tr>
print<<EOM;
<tr><td valign="top"><strong><label for="new_position_closes_review_begins">Review Begins</label></strong></td>
	<td valign="top"><INPUT TYPE="text" NAME="new_position_closes_review_begins" id="new_position_closes_review_begins" SIZE="60" VALUE="$position_closes_review_begins"><br>
		Example: October 18, $year
	</td></tr>

<tr><td valign="top"><strong><label for="new_sync_w_filemaker">To apply</label></strong><br>Automatically inserted)</td>
	<td valign="top">


<p>
<strong>TO APPLY:</strong> Please email your letter of interest, curriculum vitae or resume, and a writing sample to <a href="mailto:careers\@sedl.org">careers\@sedl.org</a>. Word documents and PDFs are acceptable. Electronic submissions are preferred, but if you would rather mail your application materials, send them to:</p>
<p style="margin-left:25px;">
Careers at SEDL<br>
SEDL<br>
4700 Mueller Blvd.<br>
Austin, TX 78723
</p>

<p>
Application review begins <em>DATE WILL APPEAR HERE</em> and will continue until <em>DATE WILL APPEAR HERE</em>.
Check SEDL's web site (<a href="http://www.sedl.org/about/careers.html">http://www.sedl.org/about/careers.html</a>) or call Maria Turner at 800-476-6861 to determine status of position.
</p>
<p>
<br><br>
<a href="/pubs/catalog/authors/sliberty.html">Sue Liberty</a><br>
Human Resources Generalist
</p>

	</td></tr>


</table>
	<div style="margin-left:25px;margin-top:15px;">
		<INPUT TYPE="HIDDEN" NAME="show_record" VALUE="$show_record">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_add_vacancy">
		<INPUT TYPE="SUBMIT" name="submit" VALUE="Submit">
	</div>
</form>
EOM
#<textarea name="new_sync_w_filemaker" id="new_sync_w_filemaker" rows="15" cols="70">$sync_w_filemaker</textarea>

	if ($show_record ne '') {
print<<EOM;
<div class="first fltRt">
		<FORM ACTION="job_postings.cgi" METHOD="POST">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr><td colspan="2"><em>Click here to delete this job posting.</em></td></tr>
		<tr><td valign="top"><input type="checkbox" name="confirm" id="confirm" value="confirmed"></td>
			<td valign="top" style="color:#990000"><label for="confirm">confirm the deletion<br> of this job posting.</label></td></tr>
		<tr><td colspan="2">
				<input type="hidden" name="location" value="process_delete_vacancy">
				<input type="hidden" name="show_record" value="$show_record">
				<input type="submit" name="submit" value="Delete this job posting"></td></tr>

		</table>
		</form>
</div>
EOM
	}
print<<EOM;
<p></p>
<p>
<br><br>Created: $created
EOM
	if ($last_modified ne '') {
		$last_modified = commoncode::convert_timestamp_2pretty_w_date($last_modified, "yes");
print<<EOM;
<br>
Last modified: $last_modified
EOM
	}
print<<EOM;
</p><br><br>

$htmltail
EOM
}
#################################################################################
## END: LOCATION = ADD_VACANCY
#################################################################################


#################################################################################
## START: LOCATION = COPY_RECORD
#################################################################################
if ($location eq 'copy_record') {
	if ($confirm ne 'confirmed') {
		$error_message = "You forgot to click the \"confirm\" checkbox to copy the record.  Please try again.";
		$location = "menu";
	}
	if ($show_record eq '') {
		$error_message = "The record ID for the position to copy was not passed correctly. Contact webmaster\@sedl.org for assistance.";
		$location = "menu";
	}
}
if ($location eq 'copy_record') {
	my $position_title = "";
	my $description = "";
	my $position_exempt_status = "";
	my $position_location = "";
	my $sedl_unit = "";
	my $responsibility_leadin = "";
	my $responsibility_1_desc = "";
	my $responsibility_2_desc = "";
	my $responsibility_3_desc = "";
	my $responsibility_4_desc = "";
	my $responsibility_5_desc = "";
	my $responsibility_6_desc = "";
	my $responsibility_7_desc = "";
	my $responsibility_8_desc = "";
	my $responsibility_9_desc = "";
	my $responsibility_10_desc = "";
	my $responsibility_11_desc = "";
	my $qualifications = "";
	my $experience = "";
	my $salary = "";
	my $position_opens = "";
	my $position_closes = "";
	my $position_closes_review_begins = "";
	my $sync_w_filemaker = "";
	my $created = "";
	my $last_modified = "";
	my $revised = "";
	my $show_onweb = "";
	my $web_sort_order = "";
	my $recordid = "";
	my $quantity = "";
	my $travel = "";
	my $funding = "";
	my $date_to_start_display = "";

my $next_web_sort_order = 1;
		# SELCT LARGEST SORT ORDER FROM DB
		my $command = "select web_sort_order from job_vacancies order by web_sort_order DESC LIMIT 1";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		while (my @arr = $sth->fetchrow) {
			($next_web_sort_order) = @arr;
		}
		$next_web_sort_order++;

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from job_vacancies WHERE recordid = '$show_record'";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		while (my @arr = $sth->fetchrow) {
			($position_title, $description, $position_exempt_status, $position_location, $sedl_unit, $responsibility_leadin, $responsibility_1_desc, $responsibility_2_desc, $responsibility_3_desc, $responsibility_4_desc, $responsibility_5_desc, $responsibility_6_desc, $responsibility_7_desc, $responsibility_8_desc, $responsibility_9_desc, $responsibility_10_desc, $responsibility_11_desc, $qualifications, $experience, $salary, $position_opens, $position_closes, $position_closes_review_begins, $sync_w_filemaker, $created, $last_modified, $revised, $show_onweb, $web_sort_order, $recordid, $quantity, $travel, $funding, $date_to_start_display) = @arr;

			## CLEAN QUOTES BEFORE INSERTING INTO DATABASE
			$position_title = &commoncode::cleanthisfordb($position_title);
			$description = &commoncode::cleanthisfordb($description);
			$position_exempt_status = &commoncode::cleanthisfordb($position_exempt_status);
			$position_location = &commoncode::cleanthisfordb($position_location);
			$sedl_unit = &commoncode::cleanthisfordb($sedl_unit);
			$responsibility_leadin = &commoncode::cleanthisfordb($responsibility_leadin);
			$responsibility_1_desc = &commoncode::cleanthisfordb($responsibility_1_desc);
			$responsibility_2_desc = &commoncode::cleanthisfordb($responsibility_2_desc);
			$responsibility_3_desc = &commoncode::cleanthisfordb($responsibility_3_desc);
			$responsibility_4_desc = &commoncode::cleanthisfordb($responsibility_4_desc);
			$responsibility_5_desc = &commoncode::cleanthisfordb($responsibility_5_desc);
			$responsibility_6_desc = &commoncode::cleanthisfordb($responsibility_6_desc);
			$responsibility_7_desc = &commoncode::cleanthisfordb($responsibility_7_desc);
			$responsibility_8_desc = &commoncode::cleanthisfordb($responsibility_8_desc);
			$responsibility_9_desc = &commoncode::cleanthisfordb($responsibility_9_desc);
			$responsibility_10_desc = &commoncode::cleanthisfordb($responsibility_10_desc);
			$responsibility_11_desc = &commoncode::cleanthisfordb($responsibility_11_desc);
			$qualifications = &commoncode::cleanthisfordb($qualifications);
			$experience = &commoncode::cleanthisfordb($experience);
			$salary = &commoncode::cleanthisfordb($salary);
			$position_opens = &commoncode::cleanthisfordb($position_opens);
			$position_closes = &commoncode::cleanthisfordb($position_closes);
			$position_closes_review_begins = &commoncode::cleanthisfordb($position_closes_review_begins);
			$revised = &commoncode::cleanthisfordb($revised);
			$web_sort_order = &commoncode::cleanthisfordb($next_web_sort_order);
			$quantity = &commoncode::cleanthisfordb($quantity);
			$travel = &commoncode::cleanthisfordb($travel);
			$funding = &commoncode::cleanthisfordb($funding);
			$date_to_start_display = &commoncode::cleanthisfordb($date_to_start_display);
		} # END DB QUERY LOOP

		my $command = "INSERT INTO job_vacancies values ('$position_title', '$description', '$position_exempt_status', '$position_location', '$sedl_unit', '$responsibility_leadin', '$responsibility_1_desc', '$responsibility_2_desc', '$responsibility_3_desc', '$responsibility_4_desc', '$responsibility_5_desc', '$responsibility_6_desc', '$responsibility_7_desc', '$responsibility_8_desc', '$responsibility_9_desc', '$responsibility_10_desc', '$responsibility_11_desc', '$qualifications', '$experience', '$salary', '$position_opens', '$position_closes', '$position_closes_review_begins', '', '$timestamp', '', '$revised', 'no', '$web_sort_order', '', '$quantity', '$travel', '$funding', '$date_to_start_display')";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		$feedback_message = "The record was copied successfully and set to <strong>NOT</strong> be displayed on site, so you can continue to edit the new posting before activating it.";
		$location = "menu";

		################################
		## START: UPDATE FILEMAKER
		################################
		## TRIGGER THE FILEMAKER SYNC
		my $content_from_url = get("http://www.sedl.org/staff/sims/positions_novs_mysql.php");
		################################
		## END: UPDATE FILEMAKER
		################################

}
#################################################################################
## END: LOCATION = COPY_RECORD
#################################################################################


#################################################################################
## START: LOCATION = MENU
#################################################################################
if ($location eq 'menu') {

## PRINT SIGNUP FORM
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Intranet | SEDL Job Postings Database</TITLE>
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1 style="margin-top:0px;"><A HREF="job_postings.cgi">SEDL Job Postings Database</A>
		<br>List of Postings</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="job_postings.cgi?location=logout">logout</A>)
	</td></tr>
</table>
EOM
my $new_hiring_supervisor = $query->param("new_hiring_supervisor");

print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';
print "<p class=\"alert\">$error_message</p>" if $error_message ne '';

my $command = "select * from job_vacancies";
   $command .= " order by position_title" if ($sortby eq 'position');
   $command .= " order by created DESC" if ($sortby eq 'date');
   $command .= " order by web_sort_order" if ($sortby eq 'sort_order');

my $dsn = "DBI:mysql:database=corp;host=localhost";
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches_vacancies = $sth->rows;

my $col_head_date = "Date Created";
   $col_head_date = "<A HREF=\"job_postings.cgi?sortby=date\">Date Created</A>" if ($sortby ne 'date');
my $col_head_position = "Position";
   $col_head_position = "<A HREF=\"job_postings.cgi?sortby=position\">Position</A>" if ($sortby ne 'position');
my $col_head_status = "Status";
   $col_head_status = "<A HREF=\"job_postings.cgi?sortby=status\">Status</A>" if ($sortby ne 'status');
my $col_head_sort_order = "Sorting<br>Order for<br>Careers<br>Page";
   $col_head_sort_order = "<A HREF=\"job_postings.cgi?sortby=sort_order\">Sorting<br>Order for<br>Careers<br>Page</A>" if ($sortby ne 'sort_order');

print<<EOM;
<p>
There are $num_matches_vacancies vacancies on file.
Click here to <a href="job_postings.cgi?location=add_vacancy">Add a New Vacancy</a>.
Click here to visit the <a href="/about/careers.html">Careers</a> page.
</p>
<table border="1" cellpadding="3" cellspacing="0">
<tr bgcolor="#ebebeb">
	<td valign="top"><strong>#</strong></td>
	<td><strong>$col_head_position</strong></td>
	<td><strong>$col_head_sort_order</strong></td>
	<td><strong>Show on<br>Site?</strong></td>
	<td><strong>$col_head_date</strong></td>
	<td><strong>Copy to a<br>new posting</strong></td>
</tr>
EOM


	if ($num_matches_vacancies == 0) {
		print "<P><FONT COLOR=RED>There are no vacancies in the database.</FONT>";
	}
my $counter = 1;
	while (my @arr = $sth->fetchrow) {
		my ($position_title, $description, $position_exempt_status, $position_location, $sedl_unit, $responsibility_leadin, $responsibility_1_desc, $responsibility_2_desc, $responsibility_3_desc, $responsibility_4_desc, $responsibility_5_desc, $responsibility_6_desc, $responsibility_7_desc, $responsibility_8_desc, $responsibility_9_desc, $responsibility_10_desc, $responsibility_11_desc, $qualifications, $experience, $salary, $position_opens, $position_closes, $position_closes_review_begins, $sync_w_filemaker, $created, $last_modified, $revised, $show_onweb, $web_sort_order, $recordid, $quantity, $travel, $funding, $date_to_start_display) = @arr;
		$created = &commoncode::convert_timestamp_2pretty_w_date($created, 'yes');
my $background_color = "";
   $background_color = "background-color:#ffffcc;" if ($recordid eq $show_record);
print<<EOM;
<TR style="$background_color">
	<td valign="top"><a name="$recordid"></a>$counter</td>
	<td valign="top"><A HREF=\"job_postings.cgi?location=add_vacancy&show_record=$recordid\" TITLE="Click to edit this vacancy listing">$position_title</a> in $sedl_unit<br>$position_location
EOM
print "<br>($quantity)" if ($quantity ne '');
print "<br>* Revised" if ($revised eq 'yes');
my $this_color= "#009900";
   $this_color= "#990000" if ($show_onweb ne 'yes');
	if ($show_onweb eq 'yes') {
		$show_onweb = "active";
	} else {
		$show_onweb = "not active";
	} # END IF/ELSE
print<<EOM;
	</td>
	<td valign="top" style="text-align:center;">$web_sort_order</td>
	<td valign="top"><span style="color:$this_color"><strong>$show_onweb</strong></span><br>
		<a href="/about/careers.cgi?vacancy=$recordid">preview</a></td>
	<td valign="top" style="font-size:9px;" nowrap>Created: $created
EOM
	if ($last_modified ne '') {
		$last_modified = &commoncode::convert_timestamp_2pretty_w_date($last_modified, 'yes');
		print "<br>Edited: $last_modified";
	} # END IF
print<<EOM;
	</td>
	<td valign="top">
		<form ACTION="job_postings.cgi" METHOD="POST" name="form3" id="form3">
		<div>
		<INPUT TYPE="checkbox" name="confirm" id="confirm$counter" VALUE="confirmed"><label for="confirm$counter">confirm</label><br>
		<INPUT TYPE="HIDDEN" name="show_record" VALUE="$recordid">
		<INPUT TYPE="HIDDEN" name="location" VALUE="copy_record">
		<INPUT TYPE="SUBMIT" VALUE="Copy">
		</div>
		</form>

	</td>
</TR>
EOM
		$counter++;
	} # END DB QUERY LOOP
print<<EOM;
</table>
<p></p>
<p>
To report troubles using this form, send an email to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A>
or call Brian Litke at ext. 6529.
</p>
<p>
The job postings page is located at <A HREF="http://www.sedl.org/about/careers.html">http://www.sedl.org/about/careers.html</A>.
</p>
$htmltail
EOM
}
#################################################################################
## END: LOCATION = MENU
#################################################################################

#print "<P>ID: $cookie_ss_staff_id";





####################################################################
## COOKIE HANDLING SUBROUTINES
####################################################################

sub setCookie {
 my ($name, $val, $exp, $path, $dom, $secure) = @_;
 print "Set-Cookie: ";
 print ("$name=$val; expires=$exp; path=$path; domain=$dom");
 print "; $secure" if defined($secure);
 print "\n";
}

sub getCookies {
 my (%cookies);
 foreach (split (/; /,$ENV{'HTTP_COOKIE'})){
 my($key) = split(/=/, $_);
 $cookies{$key} = substr($_, index($_, "=")+1);
 ($cookies{$key}, $xxdate, $xxpath, $xxdomain) = split(/\,/,$cookies{$key});

 }
 return(%cookies);
}


sub getCookiesfulldata {
 my (%cookies);
 foreach (split (/; /,$ENV{'HTTP_COOKIE'})){
 my($key) = split(/=/, $_);
 $cookies{$key} = substr($_, index($_, "=")+1);
# ($cookies{$key}, $xxdate, $xxpath, $xxdomain) = split(/\,/,$cookies{$key});
 }
 return(%cookies);
}


## SAMPLE SETCOOKIE CALLS:
# setCookie ("user", "dbewley", $expdate, $path, $thedomain);
# my(%cookies) = getCookies();


############################################
## START: SUBROUTINE printform_frequency
############################################
sub printform_web_sort_order {
	my $form_variable_name = $_[0];
	my $selected_item = $_[1];
	my $blank_selection_label = $_[2];
	my $counter_item = "0";

	print "<select NAME=\"$form_variable_name\">\n<OPTION VALUE=\"\">$blank_selection_label</OPTION>\n";
	while ($counter_item <= 10) {
		print "<OPTION VALUE=\"$counter_item\"";
		print " SELECTED" if ($counter_item eq $selected_item);
		print ">$counter_item";
		$counter_item++;
	} # END WHILE
	print "</select>";
} # END subroutine printform_frequency
############################################
## END: SUBROUTINE printform_frequency
############################################


############################################
## START: SUBROUTINE printform_exempt_menu
############################################
sub printform_exempt_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];

	my @item_value = ("", "EXEMPT", "NON-EXEMPT");
	my @item_label = ("(select one)", "EXEMPT", "NON-EXEMPT");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<select name="$field_name" id="$field_name" alt="$previous_selection">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<option VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</option>\n";
			$item_counter++;
		} # END WHILE
	print "</select>\n";
######################################
} # END: SUBROUTINE printform_exempt_menu
######################################

######################################
## START: SUBROUTINE printform_position_location_menu
######################################
sub printform_position_location_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];

	my @item_value = ("", "SEDL's Atlanta, GA office", "SEDL's Austin, TX office", "SEDL's Columbia, SC office", "SEDL's Metairie, LA office", "Montgomery, AL", "Raleigh, NC", "either SEDL's Austin, TX or Metairie, LA office", "Austin TX or one of SEDL's satellite offices in Metairie LA, Ridgeland MS, or Cayce SC. Other office locations or remote/telecommuting may be considered, particularly if located in one of the 10 states where SEDL has substantial work (Alabama, Arkansas, Georgia, Louisiana, Mississippi, New Mexico, North Carolina, Oklahoma, South Carolina, and Texas).");
	my @item_label = ("(select one)", "SEDL's Atlanta, GA office", "SEDL's Austin, TX office", "SEDL's Columbia, SC office", "SEDL's Metairie, LA office", "Montgomery, AL", "Raleigh, NC", "either SEDL's Austin, TX or Metairie, LA office", "either SEDL's Austin, TX or LA, MS, SC, other...).");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<select name="$field_name" id="$field_name" alt="$previous_selection">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<option VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</option>\n";
			$item_counter++;
		} # END WHILE
	print "</select>\n";
######################################
} # END: SUBROUTINE printform_position_location_menu
######################################





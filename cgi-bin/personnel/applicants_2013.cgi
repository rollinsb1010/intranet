#!/usr/bin/perl

#####################################################################################################
# Copyright 2007 by Southwest Educational Development Laboratory
#
# This script is used by AS to manage online application submissions for employment
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
################################################
## END: LOAD PERL MODULES USED BY THIS SCRIPT
################################################

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

##########################################
# START: CONFIGURATION VARIABLES
##########################################
my $item_label = "SEDL Applicant";
my $site_label = "Job Vacancies: Applicants for Open Positions";
my $public_site_address = "http://www.sedl.org/afterschool/guide/science/";
my $mysql_db_table_name = "job_applicants";
my $script_name = "applicants.cgi";
##########################################
# END: CONFIGURATION VARIABLES
##########################################

  
########################################
## START: READ VARIABLES PASSED BY USER
########################################
my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $logon_pass = $query->param("logon_pass");

my $validuser = "no";
my $logonuser_is_hr_representative = "no";

my $uniqueid = param('uniqueid');

my $location = param('location');
   $location = "menu" if $location eq '';

my $showsession = param('showsession');

my $error_message = "";
my $feedback_message = "";
my $show_org_id = $query->param("show_org_id");
my $show_pub_id = $query->param("show_pub_id");
my $show_record = $query->param("show_record");
my $show_samplessent_id = $query->param("show_samplessent_id");
my $sortby = $query->param("sortby");
my $confirm = $query->param("confirm");
my $show_time_period = $query->param("show_time_period");
   $show_time_period = "last2years" if ($show_time_period eq '');
my $reopen_for_editing = $query->param("reopen_for_editing");
########################################
## END: READ VARIABLES PASSED BY USER
########################################

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

	my $date_full_pretty_4digityear = "$month/$monthdate_wleadingzero/$year"; # Full date in human-readable format  (e.g. 03/06/08)

my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
	my $this_user_id = "$timestamp$ipnum$ipnum2";
	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################


   
#   20070219082757
#   20070219000000
my $show_datestamps_after;
my $show_year = $year;
my $show_month = $month;
my $suffix = "00000000";
my $lastyear = $year - 1;

if ($show_time_period eq 'anydate') {
	$show_datestamps_after = "20060101000000";
	$show_datestamps_after =~ s/ //gi;
} elsif ($show_time_period eq 'thisyear') {
	$show_datestamps_after = "$year 0101000000";
	$show_datestamps_after =~ s/ //gi;
} elsif ($show_time_period eq 'last2years') {
	$show_datestamps_after = "$lastyear 0101000000";
	$show_datestamps_after =~ s/ //gi;
} elsif ($show_time_period eq 'thismonth') {
	$show_datestamps_after = "$show_year$show_month$suffix";
} elsif ($show_time_period eq 'lastmonth') {
	$show_month--;
	if ($show_month == 0) {
		$show_month = 12;
		$show_year--;
	}
	if (length($show_month) == 1) {
		$show_month = "0$show_month";
	}
	$show_datestamps_after = "$show_year$show_month$suffix";
} elsif ($show_time_period eq 'last2months') {
	$show_month--;
	if ($show_month == 0) {
		$show_month = 12;
		$show_year--;
	}
	$show_month--;
	if ($show_month == 0) {
		$show_month = 12;
		$show_year--;
	}
	if (length($show_month) == 1) {
		$show_month = "0$show_month";
	}
	$show_datestamps_after = "$show_year$show_month$suffix";
}
####################################
## END: GET THE CURRENT DATE INFO
####################################



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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("215"); # 215 is the PID for the "Budget Reports" page on the intranet

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";

#	################################################
#	## START: SHOW SLIM TEMPLATE IF AN INSIDE PAGE
#	################################################
#	my $htmlhead_noside = "";
#	my $htmltail_noside = "";
#	open(HTMLHEAD,"</home/httpd/html/staff/includes/header2012.txt");
#	while (<HTMLHEAD>) {
#		$htmlhead_noside .= $_;
#	}
#	close(HTMLHEAD);
#	
#	open(HTMLTAIL,"</home/httpd/html/staff/includes/footer2012.txt");
#	while (<HTMLTAIL>) {
#		$htmltail_noside .= $_;
#	}
#	close(HTMLTAIL);
#	
#	$htmlhead_noside .= "<div style=\"padding:15px;\">\n";
#	$htmltail_noside = "</div>\n$htmltail";
#	
#	if ($location eq 'add_vacancy') {
#		$htmlhead = $htmlhead_noside;
#		$htmltail = $htmltail_noside;
#	}
#	################################################
#	## END: SHOW SLIM TEMPLATE IF AN INSIDE PAGE
#	################################################

###########################################
# END: GRAB SEDL HEADER AND FOOTER HTML #
###########################################

my $grayboxtop = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#EBEBEB\"><tr><td valign=\"top\"><img src=\"/eplan/images/corners-gray_01.gif\" width=\"6\" height=\"6\" alt=\" \"></td><td><img src=\"/eplan/images/spacer.gif\" width=\"6\" height=\"8\" alt=\"\"></td><td align=\"right\" valign=\"top\"><img src=\"/eplan/images/corners-gray_01-UR.gif\" width=\"6\" height=\"6\" alt=\" \"></td></tr><tr><td><img src=\"/eplan/images/spacer.gif\" width=\"10\" height=\"1\" alt=\"\"></td><td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td valign=\"middle\">";
my $grayboxtop_narrow = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#EBEBEB\"><tr><td valign=\"top\"><img src=\"/eplan/images/corners-gray_01.gif\" width=\"6\" height=\"6\" alt=\" \"></td><td><img src=\"/eplan/images/spacer.gif\" width=\"6\" height=\"8\" alt=\"\"></td><td align=\"right\" valign=\"top\"><img src=\"/eplan/images/corners-gray_01-UR.gif\" width=\"6\" height=\"6\" alt=\" \"></td></tr><tr><td><img src=\"/eplan/images/spacer.gif\" width=\"10\" height=\"1\" alt=\"\"></td><td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td valign=\"middle\">";
my $grayboxbottom = "</td></tr></table></td><td width=\"6\"><img src=\"/eplan/images/spacer.gif\" width=\"10\" height=\"1\" alt=\"\"></td></tr><tr><td valign=\"bottom\"><img src=\"/eplan/images/corners-gray_01-LL.gif\" width=\"6\" height=\"6\" alt=\" \"></td><td><img src=\"/eplan/images/spacer.gif\" width=\"6\" height=\"8\" alt=\"\"></td><td align=\"right\" valign=\"bottom\"><img src=\"/eplan/images/corners-gray_01-LR.gif\" width=\"6\" height=\"6\" alt=\" \"></td></tr></table>";



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
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
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
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";

		while (my @arr = $sth->fetchrow) {
		my ($ss_session_id, $ss_staff_id, $ss_session_last_active, $ss_ip_number, $ss_session_active, $ss_staff_project, $ss_search_site, $ss_last_page_viewed) = @arr;
			## IF SESSION STILL ACTIVE, SET TIMESTAMP IN SESSION DB
				my $command_update_session = "REPLACE into staff_sessions VALUES ('$ss_session_id', '$ss_staff_id', '$timestamp', '$ipnum2', '', '', '' ,'')";
				my $dsn = "DBI:mysql:database=intranet;host=localhost";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
#my $num_matches = $sth->rows;
				$validuser = "yes" if ($ss_staff_id eq 'blitke');
				$validuser = "yes" if ($ss_staff_id eq 'cjordan');
				$validuser = "yes" if ($ss_staff_id eq 'cmoses');
				$validuser = "yes" if ($ss_staff_id eq 'ewaters');
				$validuser = "yes" if ($ss_staff_id eq 'jwestbro');
				$validuser = "yes" if ($ss_staff_id eq 'mturner');
				$validuser = "yes" if ($ss_staff_id eq 'mvadenki');
				$validuser = "yes" if ($ss_staff_id eq 'rjarvis');
				$validuser = "yes" if ($ss_staff_id eq 'sferguso');
				$validuser = "yes" if ($ss_staff_id eq 'sliberty');
				$validuser = "yes" if ($ss_staff_id eq 'vdimock');
				$validuser = "yes" if ($ss_staff_id eq 'whoover');
				$logonuser_is_hr_representative = "yes" if ($ss_staff_id eq 'whoover');
				$logonuser_is_hr_representative = "yes" if ($ss_staff_id eq 'blitke');
				$logonuser_is_hr_representative = "yes" if ($ss_staff_id eq 'sliberty');
				$logonuser_is_hr_representative = "yes" if ($ss_staff_id eq 'mturner');
		
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


if (($validuser ne 'yes') && ($location ne 'logon')) {
	$error_message = "ACCESS DENIED: You are not authorized to access the $site_label Manager. Please contact Brian Litke at ext. 6529 for assistance accessing this resource.";
	$location = "logon";
}






#################################################################################
## START: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################
if ($location eq 'logon') {

## PRINT SIGNUP FORM
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label</TITLE>
$htmlhead

<h1 style="margin-top:0px;">$site_label</h1>
<p>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
Welcome to the $site_label. This database is used by HR staff (Maria and Sue) to set up passwords for 
applicants who need to enter their application online as well as download the submitted data. 
Please enter your SEDL user ID and password to view the database.
<BR>
<FORM ACTION="applicants.cgi" METHOD=POST>
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">


  <TR><TD VALIGN="TOP"><strong>Your user ID</strong><br />
  		  (ex: sliberty)</TD>
      <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
  <TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR>
  			<SPAN class="small">(not your email password)</SPAN></TD>
      <TD WIDTH="420" VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD></TR>
</TABLE>

  <UL>
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Log In Now">
  </UL>
  </FORM>
<P>
To report troubles using this form, send an email to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.

$htmltail
EOM
} 
#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################


#################################################################################
## START: LOCATION = PROCESS_delete_item
#################################################################################

if ($location eq 'process_delete_item') {
	## CLEAN RECORD IFD FOR USE IN SQL COMMAND
	$show_record = &commoncode::cleanthisfordb($show_record);

	## CHECK TO MAKE SURE ID EXISTS AND IS NOT COMPLETED
	my $record_deletion_allowed = "no"; # DEFAULT VALUE = NO
	my $this_user_name_full = "";
	
		# SELCT EXISTING INFO FROM DB
		my $command = "select form_complete, name_f, name_l
					from $mysql_db_table_name 
					WHERE record_id = '$show_record'";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_pubs = $sth->rows;
			while (my @arr = $sth->fetchrow) {
			my ($form_complete, $this_user_name_f, $this_user_name_l) = @arr;
				$this_user_name_full = "$this_user_name_f $this_user_name_l";
				if ($form_complete =~ 'yes') {
					# DON'T ALLOW DELETION
				} else {
					$record_deletion_allowed = "yes";
				}
			} # END DB QUERY LOOP



	## START: SET WARNING, IF NECESSARY AND ABORT
	if ($confirm ne 'yes') {
		$error_message .= "You forgot to check the CONFIRM box. Please try again.</font>";
		$location = "delete_item";
	} elsif ($show_record eq '') {
		$error_message .= "You did not enter the record ID to remove. Please try again or contact SEDL's Web Administrator for assistance.";
		$location = "menu";
	} elsif ($record_deletion_allowed eq 'no') {
		$error_message .= "The record ID does not exists in the database. Please try again or contact SEDL's Web Administrator for assistance.";
		$location = "menu";
	## END: SET WARNING, IF NECESSARY AND ABORT
	} else {
		
	## START: PROCESS DELETION
			my $command_delete_user = "DELETE FROM $mysql_db_table_name WHERE record_id = '$show_record'";
			my $dsn = "DBI:mysql:database=corp;host=localhost";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_delete_user) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			$feedback_message .= "The User ($this_user_name_full) was deleted successfully. ($command_delete_user)";
			$location = "menu";
	} # END: PROCESS DELETION
}
#################################################################################
## END: LOCATION = PROCESS_delete_item
#################################################################################


#################################################################################
## START: LOCATION = delete_item
#################################################################################
if ($location eq 'delete_item') {
	my $page_title = "Delete a New Applicant and Access Code";
	my $prev_name_f = "";
	my $prev_name_l = "";
	
	# SELCT EXISTING INFO FROM DB
	my $command = "select name_f, name_l, hiring_supervisor
				from $mysql_db_table_name WHERE record_id = '$show_record'";
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_pubs = $sth->rows;
		while (my @arr = $sth->fetchrow) {
		($prev_name_f, $prev_name_l) = @arr;
	} # END DB QUERY LOOP


print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label</TITLE>
$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1 style="margin-top:0px;"><A HREF="applicants.cgi">$site_label</A><br>
$page_title: $prev_name_f $prev_name_l</h1>
</td>
	<td valign="top" align="right">
		(Click here to <A HREF="applicants.cgi?location=logout">logout</A>)
		<P>
	</td></tr>
</table>

EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
    
<FORM ACTION="applicants.cgi" METHOD=POST>

<TABLE border="0" cellpadding="2" cellspacing="0">
<tr><td valign="top"><strong>Applicant Name</strong></td>
	<td valign="top" colspan="2"><INPUT TYPE="text" NAME="new_name_f" SIZE="20" VALUE="$prev_name_f"> 
		<INPUT TYPE="text" NAME="new_name_l" SIZE=20 VALUE="$prev_name_l"></td></tr>
<tr><td valign="top"></td>
	<td valign="top"><INPUT TYPE="CHECKBOX" NAME="confirm" value="yes"></td>
	<td valign="top">You must check this box to confirm the deletion of the user record for <strong>$prev_name_f $prev_name_l</strong>.
		<P>(Note: You can only delete user IDs for people who have not yet completed their SEDL application.)</p>
	</td></tr>
</table>
	<div style="margin-left:25px;">
		<INPUT TYPE="HIDDEN" NAME="show_time_period" VALUE="$show_time_period">
		<INPUT TYPE="HIDDEN" NAME="show_record" VALUE="$show_record">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_delete_item">
		<INPUT TYPE="SUBMIT" VALUE="$page_title">
	</div>
</form>


$htmltail
EOM
}
#################################################################################
## END: LOCATION = delete_item
#################################################################################



#################################################################################
## START: LOCATION = PROCESS_add_item
#################################################################################
if ($location eq 'process_add_item') {
	my $new_name_f = $query->param("new_name_f");
	my $new_name_l = $query->param("new_name_l");
	my $new_access_code = $query->param("new_access_code");
	my $new_applyfor_position = $query->param("new_applyfor_position");
	my $new_position_exempt = $query->param("new_position_exempt");
	my $new_hiring_supervisor = $query->param("new_hiring_supervisor");

	my $continue_approved = $query->param("continue_approved");

	####################################################
	## START: LOOKUP NAMES OF ALL PREVIOUS APPLICANTS
	####################################################
	my $command = "select name_f, name_m, name_l, datestamp_created
				from $mysql_db_table_name where access_code NOT LIKE 'review2007' 
				order by datestamp_created DESC";
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_applicants = $sth->rows;

	my %applicants_by_last_name;
	my %count_applicants_by_last_name;
	while (my @arr = $sth->fetchrow) {
		my ($name_f, $name_m, $name_l, $datestamp_created) = @arr;
		$datestamp_created = &commoncode::convert_timestamp_2pretty_w_date($datestamp_created, "yes");
		$applicants_by_last_name{$name_l} .= "$name_f $name_m $name_l (created $datestamp_created)<br>";
		$count_applicants_by_last_name{$name_l}++;
	} # END DB QUERY LOOP
	####################################################
	## END: LOOKUP NAMES OF ALL PREVIOUS APPLICANTS
	####################################################

	#################################################################################
	## START: COMPARE NEW NAME AND IF MTCHES PREVIOUS ENTRIES, SHOW WARNING SCREEN
	#################################################################################
	if (($count_applicants_by_last_name{$new_name_l} > 0) && ($continue_approved ne 'yes')) {
		## SHOW WARNING AND CONTINUE BUTTON
my $s = "";
   $s = "s" if ($count_applicants_by_last_name{$new_name_l} ne '1');
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label</TITLE>
$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1 style="margin-top:0px;"><A HREF="applicants.cgi">$site_label</A><br>
Found $count_applicants_by_last_name{$new_name_l} Potential Duplicates: Verify Before Continuing</h1>
<div class=\"alert\">
The database contains $count_applicants_by_last_name{$new_name_l} previous applicant$s with this same last name "$new_name_l."
<p style="padding-left:20px;">
$applicants_by_last_name{$new_name_l}
</p>
</div>

<h2>Continue?</h2>
<p>
Please verify that you want to continue to add a new record for:
</p>
<p style="padding-left:20px;">
$new_name_f $new_name_l
</p>

EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
    
<FORM ACTION="applicants.cgi" METHOD="POST">
	<div style="margin-left:25px;">
		<INPUT TYPE="HIDDEN" NAME="continue_approved" VALUE="yes">

		<INPUT TYPE="HIDDEN" NAME="new_name_f" VALUE="$new_name_f">
		<INPUT TYPE="HIDDEN" NAME="new_name_l" VALUE="$new_name_l">
		<INPUT TYPE="HIDDEN" NAME="new_access_code" VALUE="$new_access_code">
		<INPUT TYPE="HIDDEN" NAME="new_applyfor_position" VALUE="$new_applyfor_position">
		<INPUT TYPE="HIDDEN" NAME="new_position_exempt" VALUE="$new_position_exempt">
		<INPUT TYPE="HIDDEN" NAME="new_hiring_supervisor" VALUE="$new_hiring_supervisor">
		<INPUT TYPE="HIDDEN" NAME="show_record" VALUE="$show_record">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_add_item">
		<INPUT TYPE="SUBMIT" VALUE="Continue">
	</div>
</form>



</td>
	<td valign="top" align="right">
		(Click here to <A HREF="applicants.cgi?location=logout">logout</A>)
		<P>
	</td></tr>
</table>
$htmltail
EOM

		## STOP HERE
		$location = "pause_process_add_item";
	}
	#################################################################################
	## END: COMPARE NEW NAME AND IF MTCHES PREVIOUS ENTRIES, SHOW WARNING SCREEN
	#################################################################################


	if ($location eq 'process_add_item') {


	## START: BACKSLASH VARIABLES FOR DB
	$new_name_f = &commoncode::cleanthisfordb($new_name_f);
	$new_name_l = &commoncode::cleanthisfordb($new_name_l);
	$new_access_code = &commoncode::cleanthisfordb($new_access_code);
	$new_applyfor_position = &commoncode::cleanthisfordb($new_applyfor_position);
	$new_position_exempt = &commoncode::cleanthisfordb($new_position_exempt);
	$new_hiring_supervisor = &commoncode::cleanthisfordb($new_hiring_supervisor);

	## END: BACKSLASH VARIABLES FOR DB

	## CHECK FOR DATA COPLETENESS
	if (($new_name_f eq '') || ($new_name_l eq '')) {
		$error_message .= "You did not enter the applicant name. Please try again.";
		$location = "add_user";
	} elsif ($new_hiring_supervisor eq '') {
		$error_message .= "You did not enter the hiring supervisor. Please try again.";
		$location = "add_user";
	} else {
	## BACKSLASH DATA FOR DB STATEMENT

	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select record_id, access_code
					from $mysql_db_table_name WHERE ";
			if ($show_record eq '') {
				$command .= "access_code = '$new_access_code'";
			} else {
				$command .= "record_id = '$show_record'";
			}
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;
		$already_exists = "yes" if ($num_matches_code ne '0');

		while (my @arr = $sth->fetchrow) {
			my ($this_record_id, $this_access_code) = @arr;
			$show_record = $this_record_id;
		} # END DB QUERY LOOP
my $add_edit_type = "added";
	
		if ($already_exists eq 'yes') {
#			$error_message .= "<font color=orange>The User appears to already exist. You might see this message if you clicked the \"Reload\" button after submitting a new user.</font>";
#			$location = "menu";

			## DO THE EDIT
			my $command_update_user = "UPDATE $mysql_db_table_name 
										SET applyfor_position='$new_applyfor_position', name_l='$new_name_l', name_f='$new_name_f', hiring_supervisor='$new_hiring_supervisor', access_code='$new_access_code', position_exempt='$new_position_exempt'
										WHERE record_id='$show_record'";
			if ($reopen_for_editing eq 'yes') {
				 $command_update_user = "UPDATE $mysql_db_table_name 
										SET applyfor_position='$new_applyfor_position', name_l='$new_name_l', name_f='$new_name_f', hiring_supervisor='$new_hiring_supervisor', access_code='$new_access_code', datestamp_completed = '$timestamp'
										WHERE record_id='$show_record'";
			}
			
			my $dsn = "DBI:mysql:database=corp;host=localhost";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_update_user) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			$add_edit_type = "edited";
			$feedback_message .= "The User was $add_edit_type successfully.";
			$location = "menu";
		} else {
	
			my $command_insert_user = "INSERT INTO $mysql_db_table_name VALUES ('', '$new_access_code', '', '', '$new_applyfor_position', '$timestamp', '', 
			'$new_name_l', '', '$new_name_f', 
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '', '',
			'', '', '', '', '', '', '', '', '$new_hiring_supervisor', '')";
			my $dsn = "DBI:mysql:database=corp;host=localhost";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_insert_user) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
#my $num_matches = $sth->rows;


			$feedback_message .= "The User was $add_edit_type successfully.";
			$location = "menu";
		} # END IF USER NAME NOT BLANK
	} # END CHECK FOR DATA COMPLETENESS

	} # END #1 IF LOCATION = PROCESS_add_item
} # END #2 IF LOCATION = PROCESS_add_item
#################################################################################
## END: LOCATION = PROCESS_add_item
#################################################################################


#################################################################################
## START: LOCATION = ADD_USER
#################################################################################
if ($location eq 'add_user') {
	my $page_title = "Add a New Applicant and Access Code";
	my $prev_record_id = "";
	my $prev_applyfor_position = "";
	my $prev_position_exempt = "";
	my $prev_access_code = "";
	my $prev_name_f = "";
	my $prev_name_l = "";
	my $prev_hiring_supervisor = "";
	
	if ($show_record ne '') {
		$page_title = "Edit Applicant";

		# SELCT EXISTING INFO FROM DB
		my $command = "select record_id, applyfor_position, position_exempt, access_code, name_f, name_l, hiring_supervisor
					from $mysql_db_table_name WHERE record_id = '$show_record'";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_pubs = $sth->rows;
			while (my @arr = $sth->fetchrow) {
			($prev_record_id, $prev_applyfor_position, $prev_position_exempt, $prev_access_code, $prev_name_f, $prev_name_l, $prev_hiring_supervisor) = @arr;
		} # END DB QUERY LOOP
	}

## START: USED TO PREPOPULATE ENTRIES IF NOT ALL REQUIRED FIELDS WERE INCLUDED
my $new_name_f = $query->param("new_name_f");
my $new_name_l = $query->param("new_name_l");
$prev_name_f = "$new_name_f" if ($prev_name_f eq '');
$prev_name_l = "$new_name_l" if ($prev_name_l eq '');
## END: USED TO PREPOPULATE ENTRIES IF NOT ALL REQUIRED FIELDS WERE INCLUDED

# START: GENERATE NEW ACCESS CODE IF NEEDED
if ($prev_access_code eq '') {
	my  $found_good_code = "no";
	while ($found_good_code eq 'no') {
		# GEN RANDOM ID
		my $new_code = &commoncode::randomPassword(); # PASSING THE NUMBER OF DIGITS FOR THE PASSWORD

		# CHECK TO ENSURE NOT ALREADY IN USE
		my $command = "select access_code
					from $mysql_db_table_name WHERE access_code = '$new_code'";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;
		
		# SET FLAG IF CODE IS OK TO USE
		if ($num_matches_code == 0) {
			$found_good_code = "yes";
			$prev_access_code = $new_code;
		} # END IF
	} # END WHILE LOOP
}
# END: GENERATE NEW ACCESS CODE IF NEEDED

print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label</TITLE>
$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1 style="margin-top:0px;"><A HREF="applicants.cgi">$site_label</A><br>
$page_title</h1>

EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
    
<FORM ACTION="applicants.cgi" METHOD="POST">

<TABLE border="1" cellpadding="2" cellspacing="0">
<tr><td valign="top"><strong>Applicant Name (first/last)</strong></td>
	<td><INPUT TYPE="text" NAME="new_name_f" SIZE="30" VALUE="$prev_name_f"> 
		<INPUT TYPE="text" NAME="new_name_l" SIZE="30" VALUE="$prev_name_l"></td></tr>
EOM
if ($show_record ne '') {
print<<EOM;
<tr><td valign="top"><strong>Access Code</strong></td>
	<td valign="top"><INPUT TYPE="text" NAME="new_access_code" SIZE="30" VALUE="$prev_access_code">
	</td></tr>
EOM
} else {
print<<EOM;
<tr><td valign="top"><strong>Access Code</strong> (auto-generated)</td>
	<td valign="top">$prev_access_code<INPUT TYPE="HIDDEN" NAME="new_access_code" VALUE="$prev_access_code">
	</td></tr>
EOM
}
print<<EOM;
<tr><td valign="top"><strong>Position Applying For</strong> (optional)</td>
	<td valign="top"><INPUT TYPE="text" NAME="new_applyfor_position" SIZE="60" VALUE="$prev_applyfor_position"></td></tr>
<tr><td valign="top"><strong>Is position exempt?</strong></td>
	<td valign="top">
EOM

	my $selected_exempt = "";
	   $selected_exempt = "CHECKED" if ($prev_position_exempt eq 'Exempt');
	my $selected_nonexempt = "";
	   $selected_nonexempt = "CHECKED" if ($prev_position_exempt eq 'Nonexempt');

print<<EOM;
	<INPUT TYPE="RADIO" NAME="new_position_exempt" id="new_position_exempt1" VALUE="Exempt" $selected_exempt><label for="new_position_exempt1">Exempt</label>	   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<INPUT TYPE="RADIO" NAME="new_position_exempt" id="new_position_exempt2" VALUE="Nonexempt" $selected_nonexempt><label for="new_position_exempt2">Nonexempt</label>	
</td></tr>
<tr><td valign="top"><strong>Hiring Supervisor</strong></td>
	<td valign="top">
EOM
&printform_hiring_supervisor("new_hiring_supervisor", $prev_hiring_supervisor, "Select a Manager from this list");
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong><label for="reopen_for_editing"></label>If user cannot make edits, click here to re-open this person's application.</label></strong></td>
	<td valign="top">
		<input type="checkbox" name="reopen_for_editing" id="reopen_for_editing" value="yes">
	</td></tr>
</table>
	<div style="margin-left:25px;">
		<INPUT TYPE="HIDDEN" NAME="show_time_period" VALUE="$show_time_period">
		<INPUT TYPE="HIDDEN" NAME="show_record" VALUE="$show_record">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_add_item">
	<INPUT TYPE="SUBMIT" VALUE="Submit">
	</div>
</form>
<P>
Note: You must add the applicant's first name, last name, and the name of the hiring supervisor. The other fields are optional.

</td>
	<td valign="top" align="right">
		(Click here to <A HREF="applicants.cgi?location=logout">logout</A>)
		<P>
	</td></tr>
</table>


$htmltail
EOM
}
#################################################################################
## END: LOCATION = ADD_USER
#################################################################################

#################################################################################
## START: LOCATION = VIEW_APPLICATION
#################################################################################
if ($location eq 'view_application') {
	## CHECK IF THIS USER HAS PERMISSION FOR THIS APPLICANT
}

if ($location eq 'view_application') {
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label</TITLE>
<link href="/staff/includes/staff2006_tinymce.css" rel="stylesheet" type="text/css">

<style type="text/css">
.pagebreak{    PAGE-BREAK-BEFORE: always}
</style>

</HEAD>
<BODY BGCOLOR="#FFFFFF">
EOM

my $command = "select * from $mysql_db_table_name WHERE record_id = '$show_record'";
my $dsn = "DBI:mysql:database=corp;host=localhost";
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches_applicants = $sth->rows;
	if ($num_matches_applicants ne '1') {
		print "Applicant Record ID not found.  Please contact Brian Litke at ext. 6529 for assistance.";
	}
	while (my @arr = $sth->fetchrow) {
		my ($record_id, $access_code, $form_complete, $email, $applyfor_position, $datestamp_created, $datestamp_completed, 
		$name_l, $name_m, $name_f, $address, $city, $state, $zip, $phone_day, $phone_evening, $eligible_toemploy, $have_ssn, $position_exempt, $prev_applied, $prev_applied_date, $prev_applied_position, $prev_employed, $prev_employed_date, $prev_employed_position, $understand_hrs, $understand_hrs_comment, $understand_hrs_extra, $understand_hrs_extra_comment, $understand_travel, $understand_travel_comment, $related_to_employee, $related_to_emp_named, $available_when, 
		$last_hs_name, $last_hs_grad, $univ1_name, $univ1_years, $univ1_grad, $univ1_courses, $univ2_name, $univ2_years, $univ2_grad, $univ2_courses, $univ3_name, $univ3_years, $univ3_grad, $univ3_courses, $other_ed_inst, $special_training, 
		$emp1_name, $emp1_tel, $emp1_add, $emp1_start, $emp1_end, $emp1_name_super, $emp1_sal1, $emp1_sal2, $emp1_title, $emp1_num_supervised, $emp1_reason_left, $emp2_name, $emp2_tel, $emp2_add, $emp2_start, $emp2_end, $emp2_name_super, $emp2_sal1, $emp2_sal2, $emp2_title, $emp2_num_supervised, $emp2_reason_left, $emp3_name, $emp3_tel, $emp3_add, $emp3_start, $emp3_end, $emp3_name_super, $emp3_sal1, $emp3_sal2, $emp3_title, $emp3_num_supervised, $emp3_reason_left, $emp4_name, $emp4_tel, $emp4_add, $emp4_start, $emp4_end, $emp4_name_super, $emp4_sal1, $emp4_sal2, $emp4_title, $emp4_num_supervised, $emp4_reason_left, $emp5_name, $emp5_tel, $emp5_add, $emp5_start, $emp5_end, $emp5_name_super, $emp5_sal1, $emp5_sal2, $emp5_title, $emp5_num_supervised, $emp5_reason_left, $additional_employers, $dont_contact_employers, $dont_contact_employers_reason, $other_employment_info, 
		$ref1_nametitle, $ref1_tel, $ref1_add, $ref1_occupation, $ref1_place_employ, $ref2_nametitle, $ref2_tel, $ref2_add, $ref2_occupation, $ref2_place_employ, $ref3_nametitle, $ref3_tel, $ref3_add, $ref3_occupation, $ref3_place_employ, 
		$convicted_felony, $received_probation, $felony_probation_explain, $moral_turp, $moral_turp_explain, $nolo_contendre, $nolo_contendre_explain, $invol_terminated, $invol_terminated_explain, $able_perform_essential, $word_processor, $word_processor_types, $machines_exp, $additional_info, $how_learned_about, $gender, $ethnicity_h, $ethnicity_n, $ethnicity_a, $ethnicity_b, $ethnicity_p, $ethnicity_w, $user_ip, $user_browser, $hiring_supervisor, $datestamp_updated) = @arr;

		$datestamp_completed = &commoncode::convert_timestamp_2pretty_w_date($datestamp_completed, "yes") if ($datestamp_completed ne '');
		$datestamp_updated = &commoncode::convert_timestamp_2pretty_w_date($datestamp_updated, "yes") if ($datestamp_updated ne '');
		$gender = "Female" if ($gender eq 'f');
		$gender = "Male" if ($gender eq 'm');

		if ($machines_exp ne '') {
			$word_processor_types = "$word_processor_types<br>$machines_exp";
		}
		
		if (($logonuser_is_hr_representative eq 'yes') || ($hiring_supervisor eq $cookie_ss_staff_id)) {
		
print<<EOM;
<H1 align="center">APPLICATION for EMPLOYMENT with SEDL</H1>
<P>
<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=1 align="center">
<TR><TD><strong>Applying for Position:</strong></TD>
	<TD>$applyfor_position ($position_exempt)</TD></TR>
<TR><TD><strong>Date Received:</strong></TD>
	<TD>$datestamp_completed</TD></TR>
EOM
	if ($datestamp_updated ne $datestamp_completed) {
print<<EOM;
<TR><TD><strong>Date Updated:<br>(after submission)</strong></TD>
	<TD>$datestamp_updated</TD></TR>
EOM
	}
print<<EOM;
</TABLE>
<P>


<H2>I. Applicant Data</H2>


<P>
		<TABLE cellpadding="3" cellspacing="0" border="1">
		<TR><TD bgcolor="#EBEBEB"><strong>First Name</strong></TD>
			<TD bgcolor="#EBEBEB"><strong>Middle Name</strong></TD>
			<TD bgcolor="#EBEBEB"><strong>Last Name</strong></TD></TR>
		<TR><TD>$name_f</TD>
			<TD>$name_m</TD>
			<TD>$name_l</TD></TR>
		</TABLE>
<P>
		<TABLE cellpadding="3" cellspacing="0" border="1">
		<TR><TD bgcolor="#EBEBEB"><strong>Street Address</strong></td>
			<TD bgcolor="#EBEBEB"><strong>Telephone Numbers</strong></TD>
			<TD bgcolor="#EBEBEB"><strong>Email Address</strong></TD></TR>
		<TR><TD valign="TOP">$address<br>
				$city $state, $zip</TD>
			<TD valign="TOP">
				<TABLE cellpadding="0" cellspacing="0" border="0">
				<TR><TD align="right"><em>Day:</em></TD><TD>$phone_day</TD></TR>
				<TR><TD align="right"><em>Evening:</em></TD><TD>$phone_evening</TD></TR>
				</TABLE>
			</TD>
			<TD valign="TOP">$email
			</TD>
		</TR>
		</TABLE>


<P>
<strong>Are you legally eligible for employment in the United States?</strong><br>
	<ul>
	$eligible_toemploy
	</ul>

<P>
<strong>Do you have a U.S.A. Social Security Number?</strong><BR>
	<ul>
	$have_ssn
	</ul>

<br>
EOM
my $rowspan_college = 1;
   $rowspan_college++ if ($univ2_name ne '');
   $rowspan_college++ if ($univ3_name ne '');

print<<EOM;
<H2>II. Applicant's Educational Record</H2>

<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0" WIDTH="100%">
<TR><TD VALIGN="TOP" bgcolor="#EBEBEB"><strong>Institution</strong></TD>
	<TD VALIGN="TOP" bgcolor="#EBEBEB"><strong>Name and Location of Institution</strong></TD>
	<TD VALIGN="TOP" bgcolor="#EBEBEB"><strong>Number of Years Completed</strong></TD>
	<TD VALIGN="TOP" bgcolor="#EBEBEB"><strong>Graduated?</strong></TD>
	<TD VALIGN="TOP" bgcolor="#EBEBEB"><strong>Type of Degree/Diploma and Major Course of Study</strong></TD>
</TR>
<TR><TD VALIGN="TOP"><strong>Last High School Attended</strong></TD>
	<TD VALIGN="TOP">$last_hs_name</TD>
	<TD VALIGN="TOP">&nbsp;</TD>
	<TD VALIGN="TOP">$last_hs_grad</TD>
	<TD VALIGN="TOP">&nbsp;</TD>
</TR>
<TR><TD VALIGN="TOP" ROWSPAN="$rowspan_college"><strong>University/ College</strong></TD>
	<TD VALIGN="TOP">$univ1_name</TD>
	<TD VALIGN="TOP">$univ1_years</TD>
	<TD VALIGN="TOP">$univ1_grad</TD>
	<TD VALIGN="TOP">$univ1_courses</TD>
</TR>
EOM
if ($univ2_name ne '') {
print<<EOM;
<TR><TD VALIGN="TOP">$univ2_name</TD>
	<TD VALIGN="TOP">$univ2_years</TD>
	<TD VALIGN="TOP">$univ2_grad</TD>
	<TD VALIGN="TOP">$univ2_courses</TD>
</TR>
EOM
}
if ($univ3_name ne '') {
print<<EOM;
<TR><TD VALIGN="TOP">$univ3_name</TD>
	<TD VALIGN="TOP">$univ3_years</TD>
	<TD VALIGN="TOP">$univ3_grad</TD>
	<TD VALIGN="TOP">$univ3_courses</TD>
</TR>
EOM
}
		$special_training =~ s/\r/\<BR\>/g;

print<<EOM;
</TABLE>
<P>
<strong>Please provide name, location, and a brief description of other educational institutions attended and/or special courses of study completed:</strong>
	<UL>
	$other_ed_inst
	</UL>
<P>
<strong>Any special training or skills (languages, machine operation, etc.) of which you want SEDL to be aware and which you believe to be relevant to the successful performance of the position for which you are applying:</strong>
	<UL>
	$special_training
	</UL>
EOM
	$word_processor_types =~ s/\r/\<BR\>/g;
print<<EOM;
<P>
<strong>Please list below what type(s) of computer you can operate and any software with which you have had experience (Please indicate number of years experience for each.):</strong>
	<UL>
	$word_processor_types
	</UL>


<br>
<H2>III. Applicant's Employment Record</H2>

<P>
<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0" WIDTH="100%">
<TR><TD WIDTH="190"><strong>Name of Employer or Company</strong></TD>
	<TD>$emp1_name</TD>
</TR>
<TR><TD WIDTH="190"><strong>Telephone Number</strong></TD>
	<TD>$emp1_tel</TD>
</TR>
<TR><TD WIDTH="190"><strong>Mailing Address</strong></TD>
	<TD>$emp1_add</TD>
</TR>
<TR><TD WIDTH="190"><strong>Employed (Month/Year)</strong></TD>
	<TD><em>From:</em> $emp1_start &nbsp;&nbsp;&nbsp;&nbsp; <em>To:</em> $emp1_end</TD>
</TR>
<TR><TD WIDTH="190"><strong>Name and Title of Immediate Supervisor</strong></TD>
	<TD>$emp1_name_super</TD>
</TR>
<TR><TD WIDTH="190"><strong>Monthly Salary</strong></TD>
	<TD><em>Start:</em> \$$emp1_sal1 &nbsp;&nbsp;&nbsp;&nbsp; <em>Last:</em> \$$emp1_sal2</TD>
</TR>
<TR><TD WIDTH="190" valign="top"><strong>Job Title and a Brief Description of Duties</strong></TD>
	<TD>$emp1_title</TD>
</TR>
<TR><TD WIDTH="190"><strong>Number of Employees Supervised</strong></TD>
	<TD>$emp1_num_supervised</TD>
</TR>
<TR><TD WIDTH="190"><strong>Reason for Leaving</strong></TD>
	<TD>$emp1_reason_left</TD>
</TR>
</TABLE>
EOM

	if ($emp2_name ne '') {
print<<EOM;
<P>
<IMG SRC="/images/spacer.gif" WIDTH="500" HEIGHT="8">
<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0" WIDTH="100%">
<TR><TD WIDTH="190"><strong>Name of Employer or Company</strong></TD>
	<TD>$emp2_name</TD>
</TR>
<TR><TD WIDTH="190"><strong>Telephone Number</strong></TD>
	<TD>$emp2_tel</TD>
</TR>
<TR><TD WIDTH="190"><strong>Mailing Address</strong></TD>
	<TD>$emp2_add</TD>
</TR>
<TR><TD WIDTH="190"><strong>Employed (Month/Year)</strong></TD>
	<TD>From: $emp2_start &nbsp;&nbsp;&nbsp;&nbsp; To: $emp2_end</TD>
</TR>
<TR><TD WIDTH="190"><strong>Name and Title of Immediate Supervisor</strong></TD>
	<TD>$emp2_name_super</TD>
</TR>
<TR><TD WIDTH="190"><strong>Monthly Salary</strong></TD>
	<TD>Start: \$$emp2_sal1 &nbsp;&nbsp;&nbsp;&nbsp; Last: \$ $emp2_sal2</TD>
</TR>
<TR><TD WIDTH="190" valign="top"><strong>Job Title and a Brief Description of Duties</strong></TD>
	<TD>$emp2_title</TD>
</TR>
<TR><TD WIDTH="190"><strong>Number of Employees Supervised</strong></TD>
	<TD>$emp2_num_supervised</TD>
</TR>
<TR><TD WIDTH="190"><strong>Reason for Leaving</strong></TD>
	<TD>$emp2_reason_left</TD>
</TR>
</TABLE>
EOM
	}
	if ($emp3_name ne '') {
print<<EOM;
<P>
<IMG SRC="/images/spacer.gif" WIDTH="500" HEIGHT="8">
<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0" WIDTH="100%">
<TR><TD WIDTH="190"><strong>Name of Employer or Company</strong></TD>
	<TD>$emp3_name</TD>
</TR>
<TR><TD WIDTH="190"><strong>Telephone Number</strong></TD>
	<TD>$emp3_tel</TD>
</TR>
<TR><TD WIDTH="190"><strong>Mailing Address</strong></TD>
	<TD>$emp3_add</TD>
</TR>
<TR><TD WIDTH="190"><strong>Employed (Month/Year)</strong></TD>
	<TD>From: $emp3_start &nbsp;&nbsp;&nbsp;&nbsp; To: $emp3_end</TD>
</TR>
<TR><TD WIDTH="190"><strong>Name and Title of Immediate Supervisor</strong></TD>
	<TD>$emp3_name_super</TD>
</TR>
<TR><TD WIDTH="190"><strong>Monthly Salary</strong></TD>
	<TD>Start: \$$emp3_sal1 &nbsp;&nbsp;&nbsp;&nbsp; Last: \$ $emp3_sal2</TD>
</TR>
<TR><TD WIDTH="190"><strong>Job Title and a Brief Description of Duties</strong></TD>
	<TD>$emp3_title</TD>
</TR>
<TR><TD WIDTH="190"><strong>Number of Employees Supervised</strong></TD>
	<TD>$emp3_num_supervised</TD>
</TR>
<TR><TD WIDTH="190"><strong>Reason for Leaving</strong></TD>
	<TD>$emp3_reason_left</TD>
</TR>
</TABLE>
EOM
	}
	if ($emp4_name ne '') {
print<<EOM;
<P>
<IMG SRC="/images/spacer.gif" WIDTH="500" HEIGHT="8">
<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0" WIDTH="100%">
<TR><TD WIDTH="190"><strong>Name of Employer or Company</strong></TD>
	<TD>$emp4_name</TD>
</TR>
<TR><TD WIDTH="190"><strong>Telephone Number</strong></TD>
	<TD>$emp4_tel</TD>
</TR>
<TR><TD WIDTH="190"><strong>Mailing Address</strong></TD>
	<TD>$emp4_add</TD>
</TR>
<TR><TD WIDTH="190"><strong>Employed (Month/Year)</strong></TD>
	<TD>From: $emp4_start &nbsp;&nbsp;&nbsp;&nbsp; To: $emp4_end</TD>
</TR>
<TR><TD WIDTH="190"><strong>Name and Title of Immediate Supervisor</strong></TD>
	<TD>$emp4_name_super</TD>
</TR>
<TR><TD WIDTH="190"><strong>Monthly Salary</strong></TD>
	<TD>Start: \$$emp4_sal1 &nbsp;&nbsp;&nbsp;&nbsp; Last: \$ $emp4_sal2</TD>
</TR>
<TR><TD WIDTH="190" valign="top"><strong>Job Title and a Brief Description of Duties</strong></TD>
	<TD>$emp4_title</TD>
</TR>
<TR><TD WIDTH="190"><strong>Number of Employees Supervised</strong></TD>
	<TD>$emp4_num_supervised</TD>
</TR>
<TR><TD WIDTH="190"><strong>Reason for Leaving</strong></TD>
	<TD>$emp4_reason_left</TD>
</TR>
</TABLE>
EOM
	}
	if ($emp5_name ne '') {
print<<EOM;
<P>
<IMG SRC="/images/spacer.gif" WIDTH="500" HEIGHT="8">
<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0" WIDTH="100%">
<TR><TD WIDTH="190"><strong>Name of Employer or Company</strong></TD>
	<TD>$emp5_name</TD>
</TR>
<TR><TD WIDTH="190"><strong>Telephone Number</strong></TD>
	<TD>$emp5_tel</TD>
</TR>
<TR><TD WIDTH="190"><strong>Mailing Address</strong></TD>
	<TD>$emp5_add</TD>
</TR>
<TR><TD WIDTH="190"><strong>Employed (Month/Year)</strong></TD>
	<TD>From: $emp5_start &nbsp;&nbsp;&nbsp;&nbsp; To: $emp5_end</TD>
</TR>
<TR><TD WIDTH="190"><strong>Name and Title of Immediate Supervisor</strong></TD>
	<TD>$emp5_name_super</TD>
</TR>
<TR><TD WIDTH="190"><strong>Monthly Salary</strong></TD>
	<TD>Start: \$$emp5_sal1 &nbsp;&nbsp;&nbsp;&nbsp; Last: \$ $emp5_sal2</TD>
</TR>
<TR><TD WIDTH="190" valign="top"><strong>Job Title and a Brief Description of Duties</strong></TD>
	<TD>$emp5_title</TD>
</TR>
<TR><TD WIDTH="190"><strong>Number of Employees Supervised</strong></TD>
	<TD>$emp5_num_supervised</TD>
</TR>
<TR><TD WIDTH="190"><strong>Reason for Leaving</strong></TD>
	<TD>$emp5_reason_left</TD>
</TR>
</TABLE>
EOM
	}
if ($additional_employers ne '') {
	print "<P><strong>Additional Employer Information</strong><UL>$additional_employers</UL>";
}


$dont_contact_employers = "N/A" if ($dont_contact_employers eq '');
print<<EOM;
<P>
<strong>NOTE: SEDL may contact the employer(s) listed unless you indicate below those that you do not want SEDL to contact. (Use the following space to make these indications.)</strong>
	<UL>
	<em>APPLICANT requests SEDL not to contact Employer/Company Name(s):</em><br>
	$dont_contact_employers
<P>
EOM
	if ($dont_contact_employers_reason ne '') {
print<<EOM;
<em>Reason(s):</em><br>
	$dont_contact_employers_reason
EOM
	}
	$other_employment_info =~ s/\r/\<BR\>/g;
	$ref1_add =~ s/\r/\<BR\>/g;
print<<EOM;
	</UL>
<strong>The space below is provided for the APPLICANT's voluntary use (if so desired) to list or describe any other information concerning the APPLICANT's education, training, experience, interests, career goals, honors, awards, or any other items the APPLICANT may desire to provide and which the APPLICANT believes are relevant to the SEDL position for which he/she is applying.</strong>
	<UL>
	$other_employment_info
	</UL>


<br>
<H2>IV. Personal References</H2>

<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0" WIDTH="100%">
<TR><TD WIDTH="20%"><strong>Name and Title</strong></TD>
	<TD WIDTH="80%">$ref1_nametitle</TD></TR>
<TR><TD><strong>Telephone Number</strong></TD>
	<TD>$ref1_tel</TD></TR>
<TR><TD><strong>Mailing Address</strong></TD>
	<TD>$ref1_add</TD></TR>
<TR><TD><strong>Occupation</strong></TD>
	<TD>$ref1_occupation</TD></TR>
<TR><TD><strong>Place of Employment</strong></TD>
	<TD>$ref1_place_employ</TD></TR>
</TABLE>
EOM
	if ($ref2_nametitle ne '') {
		$ref2_add =~ s/\r/\<BR\>/g;
print<<EOM;
<P>
<IMG SRC="/images/spacer.gif" WIDTH="500" HEIGHT="8">
<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0" WIDTH="100%">
<TR><TD WIDTH="20%"><strong>Name and Title</strong></TD>
	<TD WIDTH="80%">$ref2_nametitle</TD></TR>
<TR><TD><strong>Telephone Number</strong></TD>
	<TD>$ref2_tel</TD></TR>
<TR><TD><strong>Mailing Address</strong></TD>
	<TD>$ref2_add</TD></TR>
<TR><TD><strong>Occupation</strong></TD>
	<TD>$ref2_occupation</TD></TR>
<TR><TD><strong>Place of Employment</strong></TD>
	<TD>$ref2_place_employ</TD></TR>
</TABLE>
EOM
	}
	if ($ref3_nametitle ne '') {
		$ref3_add =~ s/\r/\<BR\>/g;
print<<EOM;
<P>
<IMG SRC="/images/spacer.gif" WIDTH="500" HEIGHT="8">
<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0" WIDTH="100%">
<TR><TD WIDTH="20%"><strong>Name and Title</strong></TD>
	<TD WIDTH="80%">$ref3_nametitle</TD></TR>
<TR><TD><strong>Telephone Number</strong></TD>
	<TD>$ref3_tel</TD></TR>
<TR><TD><strong>Mailing Address</strong></TD>
	<TD>$ref3_add</TD></TR>
<TR><TD><strong>Occupation</strong></TD>
	<TD>$ref3_occupation</TD></TR>
<TR><TD><strong>Place of Employment</strong></TD>
	<TD>$ref3_place_employ</TD></TR>
</TABLE>
EOM
	}
print<<EOM;



<br>
<H2>V. Additional Information</H2>

<P>
<strong>Have you previously applied for employment with SEDL?</strong><br>
	<UL>
	$prev_applied
EOM
if ($prev_applied eq 'yes') {
print<<EOM;
<P>
	<em>If Yes, Month(s) and Year(s):</em> $prev_applied_date<br>
	<em>Position(s):</em> $prev_applied_position

EOM
}
print<<EOM;
	</UL>
<P>
<strong>Have you previously been employed by SEDL?</strong><br>
	<UL>
$prev_employed
EOM
if ($prev_employed eq 'yes') {
print<<EOM;
<P>
	<em>If Yes, Period(s) of Employment:</em> $prev_employed_date<br>
	<em>Position(s):</em> $prev_employed_position

EOM
}
print<<EOM;
	</UL>
<P>
<strong>Do you understand that if employed by SEDL, you will be required to select an 
8-hour-per-day work schedule within SEDL's operating hours of 7 a.m. to 6 p.m. and 
also work such other hours as may be required by your position or assigned by your supervisor?
</strong><br>
	<UL>
	$understand_hrs
EOM
if ($understand_hrs_comment ne '') {
print<<EOM;
<P>
	<em>Comment:</em> $understand_hrs_comment
EOM
}
my $with_or_without = "without";
   $with_or_without = "with" if ($position_exempt =~ 'Nonexempt');

print<<EOM;
	</UL>
<P>
<strong>Do you understand that if employed by SEDL, you may be required to work beyond your 
selected SEDL work schedule $with_or_without additional compensation if requested by your supervisor or 
necessitated by temporary circumstances?</strong><BR>
	<UL>
$understand_hrs_extra
EOM
if ($understand_hrs_extra_comment ne '') {
print<<EOM;
<P>
	<em>Comment:</em> $understand_hrs_extra_comment
EOM
}
print<<EOM;
	</UL>
<P>
<strong>If the position for which you are applying involves travel (including, but not limited to, 
commercial airline travel), are you willing to perform fully those duties and travel as required 
by the position, even if outside your selected SEDL work schedule?</strong><BR>
	<UL>
	$understand_travel
EOM
if ($understand_travel_comment ne '') {
print<<EOM;
	<em>Comment:</em>	$understand_travel_comment
EOM
}
print<<EOM;
	</UL>
<P>
<strong>Are you related (by birth, adoption, or marriage) to any current SEDL staff member?</strong><br>
	<UL>
	$related_to_employee
EOM
if ($related_to_employee eq 'yes') {
print<<EOM;
	<P>
	<em>If Yes, please identify the SEDL staff member(s):</em> $related_to_emp_named
EOM
}

print<<EOM;
	</UL>
<P>
<strong>When will you be available to begin work if you are offered, and you accept, employment with SEDL?</strong><br>
	<UL>
	$available_when
	</UL>

<p>
<strong>Are you able to perform, with or without accommodations, the "essential functions" (as noted on the SEDL Notice of Vacancy) of the position for which you are applying?</strong>
	<UL>
	$able_perform_essential
	</UL>

<P>
<strong>In the past ten years, have you been involuntarily terminated or asked to resign from the employment of another employer?</strong>
	<UL>
	$invol_terminated
EOM
	if ($invol_terminated eq 'yes') {
print<<EOM;
	<P>
	<em>If Yes, please give the name of the employer(s), the date(s), and the reason(s) for the termination(s) or request(s) for resignation(s).</em><BR>
	$invol_terminated_explain
EOM
	}
print<<EOM;
	</UL>

<P>
$grayboxtop
	Note: Conviction of a crime is not an automatic bar to employment. SEDL will consider the nature
	of the offense, the date of the offense, and the relationship between the offense and the position for
	which you are applying.
$grayboxbottom

<p>
<strong>Have you ever been convicted of a felony?</strong>
	<UL>
	$convicted_felony
EOM
	if ($convicted_felony eq 'yes') {
print<<EOM;
<P>
	<em>Did you receive probation?</em><BR>
	$received_probation
	<P>
	<em>If either response is Yes, please explain:</em><BR>
	$felony_probation_explain
EOM
	}
print<<EOM;
	</UL>
	
<strong>"Moral turpitude" is an act of baseness, vileness, or depravity in the private and social duties that a person owes another member of society or society in general and which is contrary to the accepted rule of right and duty between persons, including, but not limited to, theft, attempted theft, murder, rape, swindling, and/or indecency with a minor. Have you ever been convicted of any offense involving moral turpitude?  Has any court ever received a plea of guilty or a plea of nolo contendre from you for any offense dealing with moral turpitude, deferred further proceedings without entering a finding of guilty, and/or placed you on probation?</strong>
	<UL>
	$moral_turp
EOM
	if ($moral_turp eq 'yes') {
print<<EOM;
	<P>
	<em>If Yes, please explain:</em><BR>
	$moral_turp_explain
EOM
	}
print<<EOM;
	</UL>
EOM
#	$machines_exp =~ s/\r/\<BR\>/g;
	$additional_info =~ s/\r/\<BR\>/g;


#$machines_exp = "N/A" if ($machines_exp eq '');
$additional_info = "N/A" if ($additional_info eq '');
#print<<EOM;
#<P>
#<strong>Please list below any software and/or additional office machines with which you have had previous experience: (Please list one software/machine type and the number of years experience on each line.)</strong>
#	<UL>
#	$machines_exp
#	</UL>
print<<EOM;
<P>
<strong>Please list below any additional information you think would be helpful concerning your knowledge, skills, and/or experience related to the SEDL position for which you are now applying.</strong>
	<UL>
	$additional_info
	</UL>


<P>
<br>
<H2>APPLICANT's DECLARATION, AGREEMENT, and SIGNATURE</h2>
I declare that, to the best of my knowledge and belief, all of the information that I have provided in this Application for Employment with SEDL is true, 
correct, and complete.
	<ul>
	<em>$name_f 
EOM
print "$name_m" if ($name_m ne '');
print "." if (length($name_m) == 1);
print<<EOM;
 $name_l</em>
	</ul>
EOM

			if ($logonuser_is_hr_representative eq 'yes') {
				$gender = "&nbsp;" if ($gender eq '');
				$how_learned_about = "&nbsp;" if ($how_learned_about eq '');

print<<EOM;

<DIV CLASS="pagebreak"></div>

<br>
<H2>VI. AFFIRMATIVE ACTION QUESTIONNAIRE</H2>
<TABLE border="1" CELLPADDING="3" CELLSPACING="0">
<TR><TD valign="top"><strong>Date of Application:</strong></td>
	<td valign="top">$datestamp_completed</td></tr>
<TR><TD valign="top"><strong>Applicant Name:</strong></td>
	<td valign="top">$name_f $name_m $name_l</td></tr>
<TR><TD valign="top"><strong>SEDL position for which<br>applicant is applying:</strong></td>
	<td valign="top">$applyfor_position</td></tr>
<TR><TD valign="top"><strong>How did you learn<br>about this position?</strong></td>
	<td valign="top">$how_learned_about</td></tr>
<TR><TD valign="top"><strong>Gender:</strong></td>
	<td valign="top">$gender</td></tr>
<TR><TD valign="top"><strong>Ethnicity:</strong></td>
	<td valign="top">
EOM
my $ethnicity_list = "QQQ";
$ethnicity_list .= "<br>$ethnicity_h" if ($ethnicity_h ne '');
$ethnicity_list .= "<br>$ethnicity_n" if ($ethnicity_n ne '');
$ethnicity_list .= "<br>$ethnicity_a" if ($ethnicity_a ne '');
$ethnicity_list .= "<br>$ethnicity_b" if ($ethnicity_b ne '');
$ethnicity_list .= "<br>$ethnicity_p" if ($ethnicity_p ne '');
$ethnicity_list .= "<br>$ethnicity_w" if ($ethnicity_w ne '');
$ethnicity_list =~ s/QQQ\<br\>//gi;
$ethnicity_list =~ s/QQQ//gi;
$ethnicity_list = "&nbsp;" if ($ethnicity_list eq '');
print<<EOM;
	$ethnicity_list
	</TD>
</TR>
</TABLE>
EOM
			} # END IF USER IS HR REP
		} else {
print<<EOM;
<h1 style="margin-top:0px;">Access Denied</h1>
It appears you are a manager trying to view an applicant for another manager's position, which is not allowed.<br><br> Contact Brian Litke at ext. 6529 if you feel you have reached this message in error.
EOM
		} # END CHECK FOR AUTHORIZED USER
	} # END DB QUERY LOOP
print<<EOM;

</BODY></HTML>
EOM
}
#################################################################################
## END: LOCATION = VIEW_APPLICATION
#################################################################################


#################################################################################
## START: LOCATION = MENU
#################################################################################
if ($location eq 'menu') {

## PRINT SIGNUP FORM
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD><TITLE>SEDL Intranet | $site_label</TITLE>
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1 style="margin-top:0px;"><A HREF="applicants.cgi">$site_label</A>
		<br />List of Applicants</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="applicants.cgi?location=logout">logout</A>)
	</td></tr>
</table>

<P>
The online application form is located at <A HREF="http://www.sedl.org/about/positions/form/">http://www.sedl.org/about/positions/form/</A>. A shorter address to give to applicants is: <A HREF="http://www.sedl.org/apply">http://www.sedl.org/apply</A>.
</p>
<P>
To report troubles using this form, send an email to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
EOM

my $new_hiring_supervisor = $query->param("new_hiring_supervisor");

	if ($logonuser_is_hr_representative eq 'yes') {
print<<EOM;
<form action="applicants.cgi" method=POST>
$grayboxtop
 Show applicants from 
<select name="show_time_period">
<option value="anydate">any date
EOM
my @option_freq = ("thisyear", "last2years", "thismonth", "lastmonth", "last2months");
my @option_freq_label = ("this year", "this year and last year", "this month", "this month and last month", "this and last 2 months");
my $counter_freq = 0;
	while ($counter_freq <= $#option_freq) {
		print "<option value=\"$option_freq[$counter_freq]\" ";
		print "SELECTED" if ($show_time_period eq $option_freq[$counter_freq]);
		print ">$option_freq_label[$counter_freq]</option>";
		$counter_freq++;
	}

print<<EOM;
</select> 
for jobs where 
EOM
&printform_hiring_supervisor("new_hiring_supervisor", $new_hiring_supervisor, "Any Manager");
print<<EOM;
 is the hiring supervisor.
<INPUT TYPE="HIDDEN" NAME="sortby" VALUE="$sortby">
<INPUT TYPE="HIDDEN" NAME="location" VALUE="menu">
<input TYPE="submit" VALUE="Refresh Page">
</form>
$grayboxbottom
EOM
	} else {
		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view applicants that are related to jobs for which you are the hiring supervisor.</FONT>";
	}

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';


my $command = "select name_f, name_m, name_l
				from $mysql_db_table_name where access_code NOT LIKE 'review2007'";
	$command .= " AND hiring_supervisor = '$new_hiring_supervisor'" if ($new_hiring_supervisor ne '');
	$command .= " AND hiring_supervisor = '$cookie_ss_staff_id'" if ($logonuser_is_hr_representative ne 'yes');
	$command .= " AND datestamp_created > $show_datestamps_after" if ($show_time_period ne '');

	$command .= " order by datestamp_created DESC" if (($sortby eq '') || ($sortby eq 'date'));
	$command .= " order by applyfor_position, datestamp_created DESC" if ($sortby eq 'position');
	$command .= " order by form_complete DESC, applyfor_position, datestamp_completed DESC" if ($sortby eq 'completed');
	$command .= " order by name_l, name_f" if ($sortby eq 'applicant');
	$command .= " order by hiring_supervisor, name_l, name_f" if ($sortby eq 'supervisor');
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_applicants = $sth->rows;
	my %applicants_by_last_name;
	my %count_applicants_by_last_name;
	while (my @arr = $sth->fetchrow) {
		my ($name_f, $name_m, $name_l) = @arr;
		$applicants_by_last_name{$name_l} .= "$name_f $name_m $name_l<br>";
#		print "$name_f $name_m $name_l<br>";
		$count_applicants_by_last_name{$name_l}++;
	} # END DB QUERY LOOP


my $command = "select record_id, applyfor_position, access_code, form_complete, datestamp_created, datestamp_completed, name_f, name_m, name_l, email, phone_day, phone_evening, hiring_supervisor, datestamp_updated
				from $mysql_db_table_name where access_code NOT LIKE 'review2007'";
	$command .= " AND hiring_supervisor = '$new_hiring_supervisor'" if ($new_hiring_supervisor ne '');
	$command .= " AND hiring_supervisor = '$cookie_ss_staff_id'" if ($logonuser_is_hr_representative ne 'yes');
	$command .= " AND datestamp_created > $show_datestamps_after" if ($show_time_period ne '');

	$command .= " order by datestamp_created DESC" if (($sortby eq '') || ($sortby eq 'date'));
	$command .= " order by applyfor_position, datestamp_created DESC" if ($sortby eq 'position');
	$command .= " order by form_complete DESC, applyfor_position, datestamp_completed DESC" if ($sortby eq 'completed');
	$command .= " order by name_l, name_f" if ($sortby eq 'applicant');
	$command .= " order by hiring_supervisor, name_l, name_f" if ($sortby eq 'supervisor');
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
my $num_matches_applicants = $sth->rows;

my $col_head_date = "Date";
   $col_head_date = "<A HREF=\"applicants.cgi?sortby=date&amp;new_hiring_supervisor=$new_hiring_supervisor\">Date Created</A>" if ($sortby ne 'date');
my $col_head_position = "Position";
   $col_head_position = "<A HREF=\"applicants.cgi?sortby=position&amp;new_hiring_supervisor=$new_hiring_supervisor\">Position</A>" if ($sortby ne 'position');
my $col_head_completed = "Completed";
   $col_head_completed = "<A HREF=\"applicants.cgi?sortby=completed&amp;new_hiring_supervisor=$new_hiring_supervisor\">Completed</A>" if ($sortby ne 'completed');
my $col_head_applicant = "Applicant Name";
   $col_head_applicant = "<A HREF=\"applicants.cgi?sortby=applicant&amp;new_hiring_supervisor=$new_hiring_supervisor\">Applicant Name</A>" if ($sortby ne 'applicant');
my $col_head_supervisor = "Hiring Supervisor";
   $col_head_supervisor = "<A HREF=\"applicants.cgi?sortby=supervisor&amp;new_hiring_supervisor=$new_hiring_supervisor\">Hiring Supervisor</A>" if ($sortby ne 'supervisor');
print<<EOM;
<P>
There are $num_matches_applicants applicants on file.
EOM
	if ($logonuser_is_hr_representative eq 'yes') {
print<<EOM;
Click here to <A HREF=\"applicants.cgi?location=add_user\">Add a New Applicant and Access Code</A>. 
EOM
	}
print<<EOM;
<br>
(If you need to view the form without entering data, use the Demo user ID = "<A HREF="http://www.sedl.org/about/positions/form/index.cgi?location=showform_check&amp;survey_id=review2007">review2007</A>")
</p>
<p>
</p>
<TABLE border="1" cellpadding="3" cellspacing="0" style="background-color:#ffffff;">
<TR bgcolor="#ebebeb">
	<td valign="top"><strong>#</strong></td>
	<td><strong>$col_head_date</strong></td>
	<td><strong>$col_head_position</strong></td>
	<td><strong>$col_head_applicant</strong></td>
	<td><strong>$col_head_completed</strong></td>
	<td><strong>Phone/ Email</strong></td>
	<td><strong>$col_head_supervisor</strong></td>
</TR>
EOM


	if ($num_matches_applicants == 0) {
		print "<P><FONT COLOR=RED>There are no applicants in the database.</FONT>";
	}
my $counter = 1;
my %applicants_bymonth;
my %applicants_byjob;
my %applicants_bymanager;
	while (my @arr = $sth->fetchrow) {
		my ($record_id, $applyfor_position, $access_code, $form_complete, $datestamp_created, $datestamp_completed, $name_f, $name_m, $name_l, $email, $phone_day, $phone_evening, $hiring_supervisor, $datestamp_updated) = @arr;

		$access_code =~ tr/a-z/A-Z/; # uppercase everything
		$datestamp_created = &commoncode::convert_timestamp_2pretty_w_date($datestamp_created, "no");
		$datestamp_completed = &commoncode::convert_timestamp_2pretty_w_date($datestamp_completed, "yes") if ($datestamp_completed ne '');
		$datestamp_updated = &commoncode::convert_timestamp_2pretty_w_date($datestamp_updated, "yes") if ($datestamp_updated ne '');
		if (($datestamp_completed ne $datestamp_updated) && ($datestamp_updated ne '')) {
			$datestamp_completed = "$datestamp_completed<br><font color=red>updated: $datestamp_updated</font>";
		}
		if ($form_complete eq "yes") {
			$form_complete = "<font color=green>completed $datestamp_completed</font><br><A HREF=\"applicants.cgi?location=view_application&amp;show_record=$record_id\">view application</A>";
		} elsif (($phone_day eq '') && ($phone_evening eq '')) {
			$form_complete = "<font color=red>incomplete</font>";
		} else {
			$form_complete = "<font color=orange>underway</font><br><A HREF=\"applicants.cgi?location=view_application&amp;show_record=$record_id\">view application</A>";
		}
		if ($email ne '') {
			if (length($email) > 35) {
				$email =~ s/\@/ \@/gi;
			}
			$email =~ s/ /<br>/gi;
			$email =~ s/<br><br>/<br>/gi;
			$email =~ s/<br><br>/<br>/gi;
			$email = "<a href=\"mailto:$email\">$email</A>";
		}
		my $datestamp_created_month = substr($datestamp_created, 0, 2);
		my $datestamp_created_yr = substr($datestamp_created, 6, 4);
		$datestamp_created_month = "$datestamp_created_yr\/$datestamp_created_month";
		$applicants_bymonth{$datestamp_created_month}++;
		$applicants_byjob{$applyfor_position}++;
		$applicants_bymanager{$hiring_supervisor}++;

print<<EOM;
<TR>
	<td valign="top" style="font-size:9px;">$counter</td>
	<td valign="top" style="font-size:9px;">$datestamp_created</td>
	<td valign="top">$applyfor_position</td>
	<td valign="top"><A HREF="applicants.cgi?location=add_user&amp;show_record=$record_id" TITLE="Click to edit user access code">$name_f $name_m $name_l</A><br><br>Access code: $access_code
EOM
my $list_of_names = $applicants_by_last_name{$name_l};
	if ($count_applicants_by_last_name{$name_l} > 1) {
		print "<div class=\"resources\">WARNING: SIMILAR APPLICANTS<br>
		$list_of_names
		</div>";
	}
print<<EOM;
	</td>
	<td valign="top">$form_complete
EOM
	if (($form_complete !~ 'completed') && ($logonuser_is_hr_representative eq 'yes')) {
print<<EOM;
Click here to <A HREF="applicants.cgi?location=delete_item&amp;show_record=$record_id">delete this user ID</A>.
EOM
	}
print<<EOM;
	</td>
	<td valign="top" style="font-size:9px;">
EOM
if (($phone_day ne '') || ($phone_evening ne '')) {
print <<EOM;
Day: $phone_day<br>
EOM
if ($phone_evening ne '') {
print<<EOM;
Evening: $phone_evening<br>
EOM
}
print<<EOM;
$email
EOM
}
print<<EOM;
</td>
	<td valign="top">$hiring_supervisor</td>
</TR>
EOM
		$counter++;
	} # END DB QUERY LOOP
print<<EOM;
</TABLE>

<h2>Summary of Applicants</h2>
EOM
#		$applicants_bymonth{$datestamp_created_month}++;
#		$applicants_byjob{$applyfor_position}++;
#		$applicants_bymanager{$hiring_supervisor}++;

my $key = "";
print<<EOM;
<h3>By Month</h3>
<table border="1" cellpadding="5" cellspacing="0">
<tr><td bgcolor="#EBEBEB">Month</td>
	<td bgcolor="#EBEBEB">Count</td>
EOM
foreach $key (sort keys %applicants_bymonth) {
 		my $created_month = substr($key, 5, 2);
		my $created_yr = substr($key, 0, 4);
		$created_month = "$created_month\/$created_yr";

	print "<tr><td>$created_month</td><td>$applicants_bymonth{$key}</td>";
}
print "</table>";

my $key = "";
print<<EOM;
<h3>By Job Title</h3>
<table border="1" cellpadding="5" cellspacing="0">
<tr><td bgcolor="#EBEBEB">Month</td>
	<td bgcolor="#EBEBEB">Count</td>
EOM
foreach $key (sort keys %applicants_byjob) {
     print "<tr><td>$key</td><td>$applicants_byjob{$key}</td>";
}
print "</table>";

my $key = "";
print<<EOM;
<h3>By Hiring Supervisor</h3>
<table border="1" cellpadding="5" cellspacing="0">
<tr><td bgcolor="#EBEBEB">Month</td>
	<td bgcolor="#EBEBEB">Count</td>
EOM
foreach $key (sort keys %applicants_bymanager) {
     print "<tr><td>$key</td><td>$applicants_bymanager{$key}</td>";
}
print "</table>";



print<<EOM;
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
## START: SUBROUTINE printform_hiring_supervisor
############################################
sub printform_hiring_supervisor {
	my $form_variable_name = $_[0];
	my $selected_item = $_[1];
	my $blank_selection_label = $_[2];
	my $counter_item = "0";
	my @items = ("mboethel", "vdimock", "sferguso", "whoover", "rjarvis", "cjordan", "cmoses", "mvadenki", "jwestbro");
	my @items_label = ("Martha Boethel", "Vicki Dimock", "Stuart Ferguson", "Wes Hoover", "Robin Jarvis", "Cathy Jordan", "Chris Moses-Egan", "Michael Vaden-Kiernan", "John Westbrook");

	print "<select NAME=\"$form_variable_name\" id=\"$form_variable_name\"><OPTION VALUE=\"\">$blank_selection_label</OPTION>";
	while ($counter_item <= $#items) {
		print "<OPTION VALUE=\"$items[$counter_item]\"";
		print " SELECTED" if ($items[$counter_item] eq $selected_item);
		print ">$items_label[$counter_item]";
		$counter_item++;
	} # END WHILE
	print "</select>";
} # END subroutine printform_frequency
############################################
## END: SUBROUTINE printform_hiring_supervisor
############################################




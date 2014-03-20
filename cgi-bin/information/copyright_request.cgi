#!/usr/bin/perl

#####################################################################################################
# Copyright 2010 by SEDL
#
# This script is used by IRC staff to handle staff requests for copyright permission
# Written by Brian Litke 10-19-2010
#####################################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
my $dsn_test = "DBI:mysql:database=test;host=localhost";
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;
use Number::Format; # ROUNDS NUMBERS TO SPECIFIC DECIMAL PLACE

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

$todaysdate = &commoncode::convert_timestamp_2pretty_w_date($timestamp, 'yes');
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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("453"); # 453 is the PID for this page in the intranet database

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";

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
my $staff_member_firstname = "";
my $staff_member_lastname = "";
my $staff_member_phone = "";
my $staff_member_department_abbrev = "";
	## IF SESSION ID NOT FOUND, PROMPT FOR LOGON
	if ($cookie_ss_session_id eq '') {
		$location = "logon";
	} else {	
	## IF SESSION ID FOUND, CHECK IF CURRENT
	my $command = "select staff_profiles.firstname, staff_profiles.lastname, staff_profiles.phone, staff_profiles.department_abbrev, staff_sessions.* from staff_profiles, staff_sessions 
				where ss_session_id like '$cookie_ss_session_id'
				AND staff_profiles.userid = staff_sessions.ss_staff_id";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";

		while (my @arr = $sth->fetchrow) {
		my ($firstname, $lastname, $phone, $department_abbrev, $ss_session_id, $ss_staff_id, $ss_session_last_active, $ss_ip_number, $ss_session_active, $ss_staff_project, $ss_search_site, $ss_last_page_viewed) = @arr;
			$staff_member_firstname = $firstname;
			$staff_member_lastname = $lastname;
			$staff_member_phone = $phone;
			$staff_member_department_abbrev = $department_abbrev;

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
<TITLE>SEDL Staff Copyright Permission Request to Use Non-SEDL Resources</TITLE>
$htmlhead

<h1>SEDL Staff Copyright Permission Request to Use Non-SEDL Resources</h1>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';

print<<EOM;      
<p>
Welcome to the SEDL Staff Copyright Permission Request Form. Please enter your SEDL user ID and password to continue.
</p>
<form ACTION="copyright_request.cgi" METHOD="POST">
<div>
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP"><strong><label for="logon_user">Your user ID</label></strong><br>
		(ex: sliberty)</TD>
	<TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" id="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
<TR><TD VALIGN="TOP" style="width:120px;"><strong><label for="logon_pass">Your intranet password</label></strong><BR>
		<SPAN class=small>(not your e-mail password)</SPAN></TD>
	<TD style="width:420px;" VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" id="logon_pass" SIZE="8"></TD>
</TR>
</TABLE>

	<div style="margin-left:25px;">
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
	<INPUT TYPE="SUBMIT" VALUE="Log In Now">
	</div>
</div>
</form>
<p>
To report troubles using this form, send an e-mail to <A HREF="mailto:webmaster\@sedl.org">webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
$htmltail
EOM
} 
#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################


#################################################################################
## START: LOCATION = PROCESS_COPYRIGHT_REQUEST
#################################################################################

if ($location eq 'process_copyright_request') {

	##########################################################
	## START: PRINT PAGE HEADER
	##########################################################
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD><TITLE>SEDL Staff Copyright Permission Request to Use Non-SEDL Resources</TITLE>
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1>SEDL Staff Copyright Permission Request to Use Non-SEDL Resources</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="copyright_request.cgi?location=logout">logout</A>)
	</td></tr>
</table>
EOM

print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';
	##########################################################
	## END: PRINT PAGE HEADER
	##########################################################

	my $name_first = $query->param("name_first");
	my $name_last = $query->param("name_last");
	my $department = $query->param("department");
	my $email = $query->param("email");
	my $tel = $query->param("tel");

		my $new_startdate_m = $query->param("new_startdate_m");
		my $new_startdate_d = $query->param("new_startdate_d");
		my $new_startdate_y = $query->param("new_startdate_y");
	my $permission_needed_by = "$new_startdate_m\/$new_startdate_d\/$new_startdate_y";

	my $time_frame = $query->param("time_frame");
	my $desc_authors = $query->param("desc_authors");
	my $desc_title = $query->param("desc_title");
	my $desc_pub_place = $query->param("desc_pub_place");
	my $desc_pages = $query->param("desc_pages");
	my $desc_publisher = $query->param("desc_publisher");
	my $desc_copyright_holder = $query->param("desc_copyright_holder");
	my $desc_pub_date = $query->param("desc_pub_date");
	my $desc_url = $query->param("desc_url");
	my $full_or_excerpt = $query->param("full_or_excerpt");
		my $excerpt_describe = $query->param("excerpt_describe");
		my $adaptation_describe = $query->param("adaptation_describe");
	my $proposed_use = $query->param("proposed_use");
		my $num_copies = $query->param("num_copies");
		my $num_recipients = $query->param("num_recipients");
		my $event = $query->param("event");
		my $event_date = $query->param("event_date");
	
		my $pub_type = $query->param("pub_type");
		my $pub_title = $query->param("pub_title");
		my $pub_date = $query->param("pub_date");
		my $pub_print_run = $query->param("pub_print_run");
		my $pub_format = $query->param("pub_format");
	
		my $link_type = $query->param("link_type");
		my $web_site_url_new = $query->param("web_site_url_new");
		my $web_site_url_new_existing = $query->param("web_site_url_new_existing");
		my $password_type = $query->param("password_type");
		my $password_type_other = $query->param("password_type_other");
	
		my $image_proposed_use = $query->param("image_proposed_use");
	
		my $other_use_description = $query->param("other_use_description");
		my $proposed_adaptation = $query->param("proposed_adaptation");

	my $service_type = $query->param("service_type");
	my $service_type_other = $query->param("service_type_other");
		my $for_profit_type = $query->param("for_profit_type");

	## START: BACKSLASH VARIABLES FOR DB
#	$name_first = &commoncode::cleanthisfordb($name_first);
#	$name_last = &commoncode::cleanthisfordb($name_last);
#	$department = &commoncode::cleanthisfordb($department);
#	$email = &commoncode::cleanthisfordb($email);
#	$tel = &commoncode::cleanthisfordb($tel);
#	$permission_needed_by = &commoncode::cleanthisfordb($permission_needed_by);
#	$time_frame = &commoncode::cleanthisfordb($time_frame);
#	$desc_authors = &commoncode::cleanthisfordb($desc_authors);
#	$desc_title = &commoncode::cleanthisfordb($desc_title);
#	$desc_pub_place = &commoncode::cleanthisfordb($desc_pub_place);
#	$desc_pages = &commoncode::cleanthisfordb($desc_pages);
#	$desc_publisher = &commoncode::cleanthisfordb($desc_publisher);
#	$desc_copyright_holder = &commoncode::cleanthisfordb($desc_copyright_holder);
#	$desc_pub_date = &commoncode::cleanthisfordb($desc_pub_date);
#	$desc_url = &commoncode::cleanthisfordb($desc_url);
#	$full_or_excerpt = &commoncode::cleanthisfordb($full_or_excerpt);
#	$excerpt_describe = &commoncode::cleanthisfordb($excerpt_describe);
#	$adaptation_describe = &commoncode::cleanthisfordb($adaptation_describe);
#	$proposed_use = &commoncode::cleanthisfordb($proposed_use);
#	$num_copies = &commoncode::cleanthisfordb($num_copies);
#	$num_recipients = &commoncode::cleanthisfordb($num_recipients);
#	$event = &commoncode::cleanthisfordb($event);
#	$event_date = &commoncode::cleanthisfordb($event_date);
#	$pub_type = &commoncode::cleanthisfordb($pub_type);
#	$pub_title = &commoncode::cleanthisfordb($pub_title);
#	$pub_date = &commoncode::cleanthisfordb($pub_date);
#	$pub_print_run = &commoncode::cleanthisfordb($pub_print_run);
#	$pub_format = &commoncode::cleanthisfordb($pub_format);
#	$link_type = &commoncode::cleanthisfordb($link_type);
#	$web_site_url_new = &commoncode::cleanthisfordb($web_site_url_new);
#	$web_site_url_new_existing = &commoncode::cleanthisfordb($web_site_url_new_existing);
#	$password_type = &commoncode::cleanthisfordb($password_type);
#	$password_type_other = &commoncode::cleanthisfordb($password_type_other);
#	$image_proposed_use = &commoncode::cleanthisfordb($image_proposed_use);
#	$other_use_description = &commoncode::cleanthisfordb($other_use_description);
#	$service_type = &commoncode::cleanthisfordb($service_type);
#	$service_type_other = &commoncode::cleanthisfordb($service_type_other);
#	$for_profit_type = &commoncode::cleanthisfordb($for_profit_type);
#	$proposed_adaptation = &commoncode::cleanthisfordb($proposed_adaptation);
	## END: BACKSLASH VARIABLES FOR DB

	## START: CHECK FOR DATA COPLETENESS
#	if ($new_position_title eq '') {
#		$new_position_title = "Position Title Missing";
#	}
	## END: CHECK FOR DATA COPLETENESS

	############################################
	## START: SET VARIABLES FOR SENDING E-MAIL 
	############################################
	# These are for mail notification of guest events
	my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
	my $fromaddr = 'webmaster@sedl.org';


	my $recipient = 'info@sedl.org';
	if ((lc($department) =~ 'secc') || (lc($department) =~ 'txcc')) {
	   $recipient = 'info@sedl.org, chris.times@sedl.org, shirley.beckwith@sedl.org';
	}
#	   $recipient = "blitke\@sedl.org"; # FOR TESTING ONLY

	my $recipientlabel = "";
	   $recipientlabel = "SEDL's Information Resource Center" if $recipient eq 'info@sedl.org';
	#   $recipient = 'blitke@sedl.org';  ## FOR TESTING ONLY - CHANGE WHEN DONE
	
	############################################
	## END: SET VARIABLES FOR SENDING E-MAIL 
	############################################
		my $record_numer_to_use = "";
		my $next_number_to_save = "";
		my $command = "select number from lookup where 
			databasename like 'staffcopyrightrequest'";
		my $dbh = DBI->connect($dsn_test, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
			while (my @arr = $sth->fetchrow) {
				($record_numer_to_use) = @arr;
				$next_number_to_save = $record_numer_to_use + 1;
			} # END DB QUERY LOOP

		my $command = "update lookup set number = '$next_number_to_save' where 
			databasename like 'staffcopyrightrequest'";
		my $dbh = DBI->connect($dsn_test, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

	#####################################################
	## START: SEND AN E-MAIL IF DATA IS VALID
	#####################################################
	if ($error_message eq "") {
		#############################################
		## START: SEND E-MAIL CONFIRMATION TO SEDL
		#############################################

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: SEDL Copyright Request <$fromaddr>
To: $recipient
Reply-To: $email
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Staff Copyright Permission Request (\#$record_numer_to_use $name_last) to Use Non-SEDL Resources

The following e-mail was received from the SEDL Staff "SEDL Staff Copyright Permission Request to Use Non-SEDL Resources" page:


CONTACT INFORMATION:
-----------------------
Name: $name_first $name_last
Department: $department
E-mail: $email
Tel: $tel


DATES:
-----------------------
Today's Date: $todaysdate
Permission Needed By: $permission_needed_by
Time Frame: $time_frame


Description of material you would like to use:
-----------------------------------------------
Citation:
 - Author(s) or editor(s): $desc_authors
 - Title: $desc_title
 - Place of Publication= $desc_pub_place
 - For journal article (journal title, volume, no., issue no., page nos.): $desc_pages
 - Publisher: $desc_publisher
 - Copyright Holder: $desc_copyright_holder
 - Pub Date: $desc_pub_date
 - URL= $desc_url


Request to use: $full_or_excerpt

EOM
	if ($full_or_excerpt =~ 'Excerpt') {
print NOTIFY <<EOM;
 - Excerpt Description: $excerpt_describe

EOM
	}
	if ($full_or_excerpt =~ 'Adaptation') {
print NOTIFY <<EOM;
 - Adaptation Description: $adaptation_describe
EOM
	}
print NOTIFY <<EOM;


Proposed description of material use:
-----------------------------------------------
Use: $proposed_use

EOM
	##########################################
	## START: PROPOSED USE = PHOTOCOPY
	##########################################
	if ($proposed_use =~ 'Photocopy') {
print NOTIFY <<EOM;
 - Number of Copies: $num_copies
 - Number of Recipients: $num_recipients
 - Event: $event
 - Event Date: $event_date

EOM
	}
	##########################################
	## END: PROPOSED USE = PHOTOCOPY
	##########################################

	##########################################
	## START: PROPOSED USE = SEDL PUBLICATION
	##########################################
	if ($proposed_use =~ 'SEDL publication') {
print NOTIFY <<EOM;
 - Pub Type: $pub_type
EOM
		if (($proposed_use =~ 'Book') || ($proposed_use =~ 'Article')) {
print NOTIFY <<EOM;
 - Proposed Pub Title: $pub_title
 - Proposed Pub Date: $pub_date
EOM
		}
print NOTIFY <<EOM;
 - Estimated Print Run: $pub_print_run

Format: $pub_format

EOM
	}
	##########################################
	## END: PROPOSED USE = SEDL PUBLICATION
	##########################################


	##########################################
	## START: PROPOSED USE = WEB SITE
	##########################################
	if ($proposed_use =~ 'SEDL Web site') {
print NOTIFY <<EOM;
 - Link Type: $link_type
	
	New URL: $web_site_url_new
	Existing URL: $web_site_url_new_existing
	
EOM
		if ($link_type =~ 'Password') {
print NOTIFY <<EOM;
	Password type: $password_type
	Password type (other detail): $password_type_other
EOM
		}
	}
	##########################################
	## END: PROPOSED USE = WEB SITE
	##########################################

	##########################################
	## START: PROPOSED USE = DIGITAL IMAGE
	##########################################
	if ($proposed_use =~ 'Digital image') {
print NOTIFY <<EOM;
	Image Proposed Use: $image_proposed_use
EOM
	}
	##########################################
	## END: PROPOSED USE = DIGITAL IMAGE
	##########################################

	##########################################
	## START: PROPOSED USE = OTHER
	##########################################
	if ($proposed_use =~ 'Other use') {
print NOTIFY <<EOM;
	Other Use Description: $other_use_description
EOM
	}
	##########################################
	## END: PROPOSED USE = OTHER
	##########################################

	##########################################
	## START: PROPOSED ADAPTATION
	##########################################
	if ($proposed_adaptation ne '') {
print NOTIFY <<EOM;
	Proposed Adaptation: $proposed_adaptation
EOM
	}
	##########################################
	## END: PROPOSED ADAPTATION
	##########################################

print NOTIFY <<EOM;


Type of SEDL service for requested material
-----------------------------------------------
Service Type: $service_type $service_type_other
EOM

	if ($service_type =~ 'For profit') {
print NOTIFY <<EOM;
	For Profit Type: $for_profit_type
EOM
	}
print NOTIFY <<EOM;


END OF DATA SUBMISSION

Thi is an automated e-mail triggered by the form at:
http://www.sedl.org/staff/information/copyright_request.cgi


EOM

close(NOTIFY);
		#############################################
		## END: SEND E-MAIL CONFIRMATION TO SEDL
		#############################################


		#############################################
		## START: SEND E-MAIL CONFIRMATION TO USER
		#############################################
open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: SEDL Copyright Request <$fromaddr>
To: $email
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Your Copyright Permission Request Was Received

You submitted the following data using the SEDL "SEDL Staff Copyright Permission Request to Use Non-SEDL Resources" page at:
http://www.sedl.org/staff/information/copyright_request.cgi

This is a courtesy copy of the information you submitted.


CONTACT INFORMATION:
-----------------------
Name: $name_first $name_last
Department: $department
E-mail: $email
Tel: $tel


DATES:
-----------------------
Today's Date: $todaysdate
Permission Needed By: $permission_needed_by
Time Frame: $time_frame


Description of material you would like to use:
-----------------------------------------------
Citation:
 - Author(s) or editor(s): $desc_authors
 - Title: $desc_title
 - Place of Publication= $desc_pub_place
 - For journal article (journal title, volume, no., issue no., page nos.): $desc_pages
 - Publisher: $desc_publisher
 - Copyright Holder: $desc_copyright_holder
 - Pub Date: $desc_pub_date
 - URL= $desc_url


Request to use: $full_or_excerpt

EOM
	if ($full_or_excerpt =~ 'Excerpt') {
print NOTIFY <<EOM;
 - Excerpt Description: $excerpt_describe

EOM
	}
	if ($full_or_excerpt =~ 'Adaptation') {
print NOTIFY <<EOM;
 - Adaptation Description: $adaptation_describe
EOM
	}
print NOTIFY <<EOM;


Proposed description of material use:
-----------------------------------------------
Use: $proposed_use

EOM
	##########################################
	## START: PROPOSED USE = PHOTOCOPY
	##########################################
	if ($proposed_use =~ 'Photocopy') {
print NOTIFY <<EOM;
 - Number of Copies: $num_copies
 - Number of Recipients: $num_recipients
 - Event: $event
 - Event Date: $event_date
EOM
	}
	##########################################
	## END: PROPOSED USE = PHOTOCOPY
	##########################################

	##########################################
	## START: PROPOSED USE = SEDL PUBLICATION
	##########################################
	if ($proposed_use =~ 'SEDL publication') {
print NOTIFY <<EOM;
 - Pub Type: $pub_type
EOM
		if (($proposed_use =~ 'Book') || ($proposed_use =~ 'Article')) {
print NOTIFY <<EOM;
 - Proposed Pub Title: $pub_title
 - Proposed Pub Date: $pub_date
EOM
		}
print NOTIFY <<EOM;
 - Estimated Print Run: $pub_print_run

Format: $pub_format
EOM
	}
	##########################################
	## END: PROPOSED USE = SEDL PUBLICATION
	##########################################


	##########################################
	## START: PROPOSED USE = WEB SITE
	##########################################
	if ($proposed_use =~ 'SEDL Web site') {
print NOTIFY <<EOM;
 - Link Type: $link_type
	
	New URL: $web_site_url_new
	Existing URL: $web_site_url_new_existing
	
EOM
		if ($link_type =~ 'Password') {
print NOTIFY <<EOM;
	Password type: $password_type
	Password type (other detail): $password_type_other
EOM
		}
	}
	##########################################
	## END: PROPOSED USE = WEB SITE
	##########################################

	##########################################
	## START: PROPOSED USE = DIGITAL IMAGE
	##########################################
	if ($proposed_use =~ 'Digital image') {
print NOTIFY <<EOM;
	Image Proposed Use: $image_proposed_use
EOM
	}
	##########################################
	## END: PROPOSED USE = DIGITAL IMAGE
	##########################################

	##########################################
	## START: PROPOSED USE = OTHER
	##########################################
	if ($proposed_use =~ 'Other use') {
print NOTIFY <<EOM;
	Other Use Description: $other_use_description
EOM
	}
	##########################################
	## END: PROPOSED USE = OTHER
	##########################################

	##########################################
	## START: PROPOSED ADAPTATION
	##########################################
	if ($proposed_adaptation ne '') {
print NOTIFY <<EOM;
	Proposed Adaptation: $proposed_adaptation
EOM
	}
	##########################################
	## END: PROPOSED ADAPTATION
	##########################################

print NOTIFY <<EOM;


Type of SEDL service for requested material
-----------------------------------------------
Service Type: $service_type $service_type_other
EOM

	if ($service_type =~ 'For profit') {
print NOTIFY <<EOM;
	For Profit Type: $for_profit_type
EOM
	}
print NOTIFY <<EOM;


END OF DATA SUBMISSION

Thi is an automated e-mail triggered by the form at:
http://www.sedl.org/staff/information/copyright_request.cgi



EOM
close(NOTIFY);
		#############################################
		## END: SEND E-MAIL CONFIRMATION TO USER
		#############################################

	#####################################################
	## END: SEND AN E-MAIL IF DATA IS VALID
	#####################################################


	##########################################################
	## START: PRINT "THANK YOU" OR "ERROR MESSSAGE" TO SCREEN
	##########################################################

print <<EOM;
<h2>Thank you</h2>
<strong>Thank you for requesting copyright permission.</strong></p>

<p>
You will receive an automatic acknowledgement at the e-mail address you provided. 
After Nancy Reynolds receives a response for your request for copyright permission, 
you will receive either an e-mail with permission granted or a notice explaining why 
copyright permission was not granted for your request. 
</p>
<p>
If you have additional questions, you may contact:
</p>
	<div style="margin-left:25px;">
	Nancy Reynolds<br>
	Information Resource Center<br>
	512-391-6548<br>
	e-mail: <a href="mailto:nancy.reynolds\@sedl.org">nancy.reynolds\@sedl.org</a>
	</div>


EOM

	} else {

print <<EOM;
<h2>Error</h2>
<p class="alert">$error_message</p>
<p>
Please use the "Back" button in your browser to return to the form and try your entry again.
</p>
EOM
	}  # END OF "THANK YOU" OR "ERROR MESSSAGE"
	##########################################################
	## END: PRINT "THANK YOU" OR "ERROR MESSSAGE" TO SCREEN
	##########################################################

print<<EOM;
<p>
To report troubles using this form, send an e-mail to 
<A HREF="mailto:webmaster\@sedl.org">webmaster\@sedl.org</A> or call Brian Litke at ext. 6529.
</p>

$htmltail
EOM

}
#################################################################################
## END: LOCATION = PROCESS_COPYRIGHT_REQUEST
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
<HEAD>
<TITLE>SEDL Staff Copyright Permission Request to Use Non-SEDL Resources</TITLE>
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1>SEDL Staff Copyright Permission Request to Use Non-SEDL Resources</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="copyright_request.cgi?location=logout">logout</A>)
	</td></tr>
</table>
EOM

print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';
print "<p class=\"alert\">$error_message</p>" if $error_message ne '';


print<<EOM;
<p>
SEDL staff who want to use non-SEDL materials to strengthen their research, presentations, or publications must complete the following form and submit it using the link at the bottom of this page. To request use of more than one item, fill out and submit this form for the first title. After submission, return to this page with the back button and complete and submit the form for each additional item.
</p>
<p>
Nancy Reynolds, information associate in the SEDL Information Resource Center (IRC), will receive your request via e-mail, and you will receive an e-mail acknowledgement with a unique tracking number in the subject line. If you are a staff member in the Southeast Comprehensive Center, the SECC communications associate also will receive a copy of your acknowledgment. After Nancy asks for permission from copyright holders on your behalf, she will forward the response with either permission granted for use or a notice of why permission was denied. 
</p>
<p>
Please allow up to 1 month for processing of the request and the response. Call Nancy at ext. 6548 or e-mail her at <a href="mailto:nancy.reynolds\@sedl.org">nancy.reynolds\@sedl.org</a> if you have any questions about your request or the copyright permission process.
</p>


<script type="text/javascript"> 
<!--
function checkFields() { 

	// Name
		if ((document.form3.name_first.value =="") || (document.form3.name_last.value =="")) {
			alert("You forgot to enter your name.");
			document.form3.name_first.focus();
			return false;	}

	// E-mail
		if (document.form3.email.value =="") {
			alert("You forgot to enter your e-mail address.");
			document.form3.email.focus();
			return false;	}

	// Tel
		if (document.form3.tel.value =="") {
			alert("You forgot to enter your telephone number.");
			document.form3.tel.focus();
			return false;	}


	// Time Frame
		if (document.form3.time_frame.value =="") {
			alert("You forgot to indicate the time frame for your request.");
			document.form3.time_frame.focus();
			return false;	}

	// Authors
		if (document.form3.desc_authors.value =="") {
			alert("You forgot to indicate the authors of the SEDL material you would like to use.");
			document.form3.desc_authors.focus();
			return false;	}

	// Title of Material
		if (document.form3.desc_title.value =="") {
			alert("You forgot to indicate the title of the SEDL material you would like to use.");
			document.form3.desc_title.focus();
			return false;	}

	// Full or Excerpt
	user_input = 0;
	for (i=0;i < 4;i++) {
		if (document.form3.full_or_excerpt[i].checked == true) {
			user_input++;
		}
	}
	if (user_input > 0) {
	} else {
		alert("You forgot to select one of the Request to Use categories.");
		document.form3.full_or_excerpt1.focus();
		return false;
	}


	// Proposed Use
	user_input = 0;
	for (i=0;i < 5;i++) {
		if (document.form3.proposed_use[i].checked == true) {
			user_input++;
		}
	}
	if (user_input > 0) {
	} else {
		alert("You forgot to select one of the proposed use categories.");
		document.form3.proposed_use1.focus();
		return false;
	}

	// Type of SEDL Service
	user_input = 0;
	for (i=0;i < 3;i++) {
		if (document.form3.service_type[i].checked == true) {
			user_input++;
		}
	}
	if (user_input > 0) {
	} else {
		alert("You forgot to select one of the Service Type categories.");
		document.form3.service_type1.focus();
		return false;
	}

	// form verification
	if (document.form3.user_calc.value != document.form3.real_calc.value) {
		alert("Verification failed. Please try again.");
		document.form3.user_calc.focus();
		return false;
	}

}	
// -->
</script>


<script type="text/javascript">
//<!--

function clear_password_type_detail() {
		document.form3.password_type1.checked = false;
		document.form3.password_type2.checked = false;
		document.form3.password_type3.checked = false;
}	

function set_link_type() {
		document.form3.link_type3.checked = true;
}	

function set_service_type() {
		document.form3.service_type1.checked = true;
}	

// -->
</script>

<script src="/common/javascript/elastic/dependencies/jquery.js" type="text/javascript" charset="utf-8"></script>

<script src="/common/javascript/elastic/jquery.elastic.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	\$(document).ready(function(){			
		\$('textarea').elastic();
	});	
</script>


<form action="copyright_request.cgi" method=POST id="form3" name="form3" onsubmit="return checkFields()">

<table border="1" cellpadding="3" cellspacing="0">
<TR><TD COLSPAN="2" style="background-color:#e9f9ad"><h2 style="margin-bottom:0px;">Contact Information</h2></TD></TR>

<TR><TD><label for="name_first">First name:</label></TD>
	<TD><input name="name_first" id="name_first" size="25" value="$staff_member_firstname"></td></tr>

<TR><TD><label for="name_last">Last name:</label></TD>
	<TD><input name="name_last" id="name_last" size="25" value="$staff_member_lastname"></td></tr>


<TR><TD><label for="department">Program/Department:</label></TD>
	<TD>
EOM

	&commoncode::printform_sedl_unit_menu("department", $staff_member_department_abbrev);
print<<EOM;
	</td></tr>

<TR><TD><label for="email">E-mail address:</label></TD>
	<TD><input name="email" id="email" size="40" value="$cookie_ss_staff_id\@sedl.org"></td></tr>

<TR><TD><label for="tel">Telephone:</label></TD>
	<TD><input name="tel" id="tel" size="15" value="$staff_member_phone"></td></tr>
</table>


<p></p>

<table border="1" cellpadding="3" cellspacing="0">
<TR><TD COLSPAN="2" style="background-color:#e9f9ad"><h2 style="margin-bottom:0px;">Dates</h2></TD></TR>

<TR><TD>Today's date:</TD>
	<TD>$todaysdate</td></tr>

<TR><TD>Date by which permission needed:</TD>
	<TD>
EOM
#		my ($old_y, $old_m, $old_d) = split(/\-/,$date_full_mysql);
		&commoncode::print_month_menu("new_startdate_m", '');
		&commoncode::print_day_menu("new_startdate_d", '');
		&commoncode::print_year_menu("new_startdate_y", $year, $year + 1, $year);

print<<EOM;
	</td></tr>


<TR><TD valign="top" style="width:30%;"><label for="time_frame">Time frame needed for usage 
		(e.g., 1 day, 1 year, 2 years, or for an ongoing project, indicate approximate 
		ending date and note that you may be required to renew your request annually):</label>
	</TD>
	<TD valign="top"><textarea name="time_frame" id="time_frame" rows="4" cols="60"></textarea>

	</td></tr>
</table>

<p></p>


<table border="1" cellpadding="3" cellspacing="0">
<TR><TD COLSPAN="2" style="background-color:#e9f9ad"><H2 style="margin-bottom:0px;">Description of material you would like to use</h2></TD></TR>
<TR><TD valign="top">Full citation of material, including:
	</TD>
	<TD valign="top">
		
		<table border="1" cellpadding="3" cellspacing="0">

		<tr><td><label for="desc_authors">Author(s) or editor(s):</label></td>
			<td valign="top"><input name="desc_authors" id="desc_authors" size="55"></td></tr>

		<tr><td><label for="desc_title">Title:</label></td>
			<td valign="top"><input name="desc_title" id="desc_title" size="55"></td></tr>

		<tr><td><label for="desc_pub_place">Place resource produced:</label></td>
			<td valign="top"><input name="desc_pub_place" id="desc_pub_place" size="55"></td></tr>

		<tr><td><label for="desc_pages">For journal article: journal title, volume, no., issue no., page nos.:</label></td>
			<td valign="top"><input name="desc_pages" id="desc_pages" size="55"></td></tr>

		<tr><td><label for="desc_publisher">Publisher:</label></td>
			<td valign="top"><input name="desc_publisher" id="desc_publisher" size="55"></td></tr>

		<tr><td><label for="desc_copyright_holder">Copyright holder if not publisher:</label></td>
			<td valign="top"><input name="desc_copyright_holder" id="desc_copyright_holder" size="55"></td></tr>

		<tr><td><label for="desc_pub_date">Date of Publication:</label></td>
			<td valign="top"><input name="desc_pub_date" id="desc_pub_date" size="15"></td></tr>

		<tr><td><label for="desc_url">Web site/URL:</label></td>
			<td valign="top"><input name="desc_url" id="desc_url" size="55"></td></tr>
		</table>


		
		</td></tr>
<TR><TD valign="top">Request to use:</TD>
	<TD valign="top">
		<table cellpadding="4" cellspacing="0" border="0">
		<tr><td valign="top"><INPUT TYPE="RADIO" NAME="full_or_excerpt" id="full_or_excerpt1" VALUE="Excerpt"></td><td valign="top"><label for="full_or_excerpt1">Excerpt</label> <label for="excerpt_describe">(please describe)</label>: <input name="excerpt_describe" id="excerpt_describe" size="60"></td></tr>
		<tr><td valign="top"><INPUT TYPE="RADIO" NAME="full_or_excerpt" id="full_or_excerpt2" VALUE="Full-text"></td><td valign="top"><label for="full_or_excerpt2">Full-text</label></td></tr>
		<tr><td valign="top"><INPUT TYPE="RADIO" NAME="full_or_excerpt" id="full_or_excerpt3" VALUE="Adaptation"></td><td valign="top"><label for="full_or_excerpt3">Adaptation</label> <label for="adaptation_describe">(please describe)</label>: <input name="adaptation_describe" id="adaptation_describe" size="60"></td></tr>
		<tr><td valign="top"><INPUT TYPE="RADIO" NAME="full_or_excerpt" id="full_or_excerpt4" VALUE="Abstract"></td><td valign="top"><label for="full_or_excerpt4">Abstract generated by author or publisher</label></td></tr>
		</table>

</td></tr>
</table>


<p>
</p>

<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0">
<TR><TD COLSPAN="2" style="background-color:#e9f9ad"><H2>Proposed description of material use:</h2>
	Select a material use type from the column on the left, and fill in any required information for the item you select.</TD></TR>

<TR><TD valign="top">
		<table>
		<tr><td valign="top"><INPUT TYPE="RADIO" NAME="proposed_use" id="proposed_use1" VALUE="Photocopy and distribute at a SEDL event"></td>
			<td><label for="proposed_use1">Photocopy and<br>distribute at<br>a SEDL event</label></td>
		</tr>
		</table>
	</TD>
	<TD valign="top">
		<label for="num_copies">No. of copies:</label> <input name="num_copies" id="num_copies" size="15"><br>
		<label for="num_recipients">No. of recipients:</label> <input name="num_recipients" id="num_recipients" size="15"><br>
		<label for="event">Event at which reprints will be distributed:</label> <input name="event" id="event" size="40"><br>

		<label for="event_date">Date of event:</label> <input name="event_date" id="event_date" size="30">

	</td>
</tr>
<TR><TD valign="top">
		<table>
		<tr><td valign="top"><INPUT TYPE="RADIO" NAME="proposed_use" id="proposed_use2" VALUE="Include in a SEDL publication"></td>
			<td><label for="proposed_use2">Include in a<br>SEDL publication</label></td>
		</tr>
		</table>		
		</TD>
	<TD valign="top">
		Publication Type:
		<div style="padding-left:10px;">

		
		<INPUT TYPE="RADIO" NAME="pub_type" id="pub_type2" VALUE="Book"><label for="pub_type2">Book</label> or 
		<INPUT TYPE="RADIO" NAME="pub_type" id="pub_type1" VALUE="Article in scholarly or trade journal"><label for="pub_type1">Article in scholarly or trade journal</label><br>
		<div style="margin-left:35px;">
		<label for="pub_title">Title of proposed publication:</label>  <input name="pub_title" id="pub_title" size="40"><br>
		<label for="pub_date">Proposed publication date:</label> <input name="pub_date" id="pub_date" size="20"><br>
		</div>
		<INPUT TYPE="RADIO" NAME="pub_type" id="pub_type3" VALUE="Abstract">
		<label for="pub_type3">Abstract produced by author or publisher</label><br>
		
		<INPUT TYPE="RADIO" NAME="pub_type" id="pub_type4" VALUE="Graphic">
		<label for="pub_type4">Graphic (e.g., chart, table, photograph, drawing)</label>
		
		</div>
		<P>

	<P>
	<label for="pub_print_run">Estimated circulation or print run:</label> <input name="pub_print_run" id="pub_print_run" size="30">
	</p>
	Publication format:

		<div style="padding-left:10px;">
		<INPUT TYPE="RADIO" NAME="pub_format" id="pub_format1" VALUE="Print only"><label for="pub_format1">Print only</label><br>
		<INPUT TYPE="RADIO" NAME="pub_format" id="pub_format2" VALUE="Print and electronic"><label for="pub_format2">Print and electronic</label><br>
		<INPUT TYPE="RADIO" NAME="pub_format" id="pub_format3" VALUE="Electronic only"><label for="pub_format3">Electronic only</label><br>
		<INPUT TYPE="RADIO" NAME="pub_format" id="pub_format4" VALUE="Password protected online access by selective users"><label for="pub_format4">Password protected online access by selective users</label>
		</div>
	</td>

</tr>

<TR><TD valign="top">
		<table>
		<tr><td valign="top"><INPUT TYPE="RADIO" NAME="proposed_use" id="proposed_use4" VALUE="Include on the SEDL Web site"></td>
			<td> <label for="proposed_use4">Include on the SEDL Web site</label></td>
		</tr>
		</table>		
	</TD>
	<TD valign="top">
		Select one:
		<div style="padding-left:30px;">

			<INPUT TYPE="RADIO" NAME="link_type" id="link_type1" VALUE="Provide as a link on your Web site" onMouseUp="clear_password_type_detail()"> 
			<label for="link_type1" onMouseUp="clear_password_type_detail()">Provide as a resource on the SEDL Web site at the following</label> <label for="web_site_url_new">URL:</label><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="web_site_url_new" id="web_site_url_new" size="50"><br><br>

			<INPUT TYPE="RADIO" NAME="link_type" id="link_type2" VALUE="Incorporate SEDL materials on your Web site" onMouseUp="clear_password_type_detail()"> 
			<label for="link_type2" onMouseUp="clear_password_type_detail()">Incorporate materials on the SEDL Web site at the following </label> <label for="web_site_url_new_existing">URL:</label><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="web_site_url_new_existing" id="web_site_url_new_existing" size="50"><br><br>

			<INPUT TYPE="RADIO" NAME="link_type" id="link_type3" VALUE="Password protected access by selective users"> 
			<label for="link_type3">Password protected access by selective users</label>
			<div style="padding-left:30px;">
				<INPUT TYPE="RADIO" NAME="password_type" id="password_type1" VALUE="Before or after a presentation" onMouseUp="set_link_type()"><label for="password_type1" onMouseUp="set_link_type()">Before or after a presentation</label><br>
				<INPUT TYPE="RADIO" NAME="password_type" id="password_type2" VALUE="During a webinar" onMouseUp="set_link_type()"><label for="password_type2" onMouseUp="set_link_type()">During a webinar</label><br>
				<INPUT TYPE="RADIO" NAME="password_type" id="password_type3" VALUE="Other" onMouseUp="set_link_type()"><label for="password_type3" onMouseUp="set_link_type()">Other</label> <label for="password_type_other">use:</label> <input name="password_type_other" id="password_type_other" size="50">
 
			</div>
		</div>
		<p>
		
		</p>
	</td>
</tr>


<TR><TD valign="top"><INPUT TYPE="RADIO" NAME="proposed_use" id="proposed_use5" VALUE="Digital image"> <label for="proposed_use5">Digital image</label></TD>

	<TD valign="top">
	<label for="image_proposed_use">Describe proposed use (e.g., scanned images on a CD-ROM or DVD, 
	PowerPoint slides, interactive program, digitized audio or video):</label><br>
	<textarea name="image_proposed_use" id="image_proposed_use" rows="4" cols="40"></textarea>

	</td>
</tr>
<TR><TD valign="top">
		<table>
		<tr><td valign="top"><INPUT TYPE="RADIO" NAME="proposed_use" id="proposed_use6" VALUE="Other"></td>
			<td><label for="proposed_use6">Other use not covered above</label></td>
		</tr>
		</table>		
	</TD>
	<TD valign="top">
		<label for="other_use_description">Describe proposed use:</label><br>
		<textarea name="other_use_description" id="other_use_description" rows="4" cols="40" title="Please describe the use"></textarea>
	</td>
</tr>
</table>

<p></p>
<p>
If you are seeking to adapt, change, or use an excerpt of a non-SEDL resource, please describe your 
proposed revision or e-mail as an attachment to Nancy Reynolds:<br>
<textarea style="margin-left:25px;" name="proposed_adaptation" id="proposed_adaptation" rows="4" cols="40"></textarea>

</p>


<table border="1" cellpadding="3" cellspacing="0" width="100%">
<TR><TD COLSPAN="2" style="background-color:#e9f9ad"><H2 style="margin-bottom:0px;">Type of SEDL service for requested material</h2></TD></TR>

<TR><TD valign="top" colspan="2">

		<INPUT TYPE="RADIO" NAME="service_type" id="service_type1" VALUE="Funded proposal work"> <label for="service_type1">Funded proposal work</label><br>
		<INPUT TYPE="RADIO" NAME="service_type" id="service_type2" VALUE="Fee-for-service work" onMouseUp="clear_service_type_detail()"> <label for="service_type2" onMouseUp="clear_service_type_detail()">Fee-for-service work</label><br>
		<INPUT TYPE="RADIO" NAME="service_type" id="service_type3" VALUE="Other:" onMouseUp="clear_service_type_detail()"> <label for="service_type3" onMouseUp="clear_service_type_detail()">Other:</label><br>
		<div style="margin-left:25px;">
		If you selected "Other," please describe:<br>
		</div>
		<textarea style="margin-left:25px;" name="service_type_other" id="service_type_other" rows="4" cols="40"></textarea>

</td></tr>
</table>

<p>
To submit your request, please select the "Submit Form" button below. You will receive an automatic acknowledgment at the e-mail address you provided above (and if you are a staff member in the SECC, the SECC communications associate also will receive a copy of your acknowledgment). After Nancy Reynolds receives a response for your request for copyright permission, you (and the SECC communications associate, if applicable) will receive either an e-mail with permission granted for use or a notice explaining why copyright permission was not granted for your request. 
</p>
<p>
Thank you!
</p>


	<div style="padding-left:30px;">
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_copyright_request">
	<input type="submit" value="Submit Form" name="submit">
	</div>

 
</form>



<p>
To report troubles using this form, send an e-mail to 
<A HREF="mailto:webmaster\@sedl.org">webmaster\@sedl.org</A> or call Brian Litke at ext. 6529.
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


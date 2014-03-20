#!/usr/bin/perl

#####################################################################################################
# Copyright 2008 by Southwest Educational Development Laboratory
#
# This script is used by Communications to manage the online About SEDL: Board of Directors list
# Written by Brian Litke 10-25-2007
#####################################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use Number::Format; # ROUNDS NUMBERS TO SPECIFIC DECIMAL PLACE
use DBI;
my $dsn = "DBI:mysql:database=corp;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

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
	my $time_hour_mil = POSIX::strftime('%k', localtime(time)); # Hour in military notation (e.g. 9 or 21)
	my $time_hour_leadingzero = POSIX::strftime('%I', localtime(time)); # Hour w/leadingsero (e.g. 09 or 09)
	my $time_hour_mil_leadingzero = POSIX::strftime('%H', localtime(time)); # Hour in military notation w/leadingsero (e.g. 09 or 21)
	my $time_min = POSIX::strftime('%M', localtime(time)); # Date in month (e.g. 39)
	my $time_sec = POSIX::strftime('%S', localtime(time)); # Date in month (e.g. 38)

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
my $item_label = "Board of Directors Member";
my $site_label = "Board of Directors Manager";
my $public_site_address = "/about/board.html";

my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $logon_pass = $query->param("logon_pass");

my $uniqueid = param('uniqueid');

my $location = param('location');
   $location = "menu" if $location eq '';

my $showsession = param('showsession');


my $error_message = "";
my $feedback_message = "";
my $show_record = $query->param("show_record");
my $sortby = $query->param("sortby");

my $new_type = $query->param("new_type");
my $show_inactive = $query->param("show_inactive");

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("308"); # 308 is the PID for this page in the intranet database

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
		my $dsn = "DBI:mysql:database=intranet;host=www.sedl.org";
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
	$dsn = "DBI:mysql:database=intranet;host=www.sedl.org";
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
	my $validuser = "no";

	## IF SESSION ID NOT FOUND, PROMPT FOR LOGON
	if ($cookie_ss_session_id eq '') {
		$location = "logon";
	} else {	
	## IF SESSION ID FOUND, CHECK IF CURRENT
	my $command = "select * from staff_sessions where ss_session_id like '$cookie_ss_session_id'";
	$dsn = "DBI:mysql:database=intranet;host=www.sedl.org";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";

		while (my @arr = $sth->fetchrow) {
		my ($ss_session_id, $ss_staff_id, $ss_session_last_active, $ss_ip_number, $ss_session_active, $ss_staff_project, $ss_search_site, $ss_last_page_viewed) = @arr;
			## IF SESSION STILL ACTIVE, SET TIMESTAMP IN SESSION DB
				my $command_update_session = "REPLACE into staff_sessions VALUES ('$ss_session_id', '$ss_staff_id', '$timestamp', '$ipnum2', '', '', '' ,'')";
				$dsn = "DBI:mysql:database=intranet;host=www.sedl.org";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
				#my $num_matches = $sth->rows;

					$validuser = "yes" if ($ss_staff_id eq 'awest');
					$validuser = "yes" if ($ss_staff_id eq 'blitke');
					$validuser = "yes" if ($ss_staff_id eq 'brollins');
					$validuser = "yes" if ($ss_staff_id eq 'cmoses');
					$validuser = "yes" if ($ss_staff_id eq 'eurquidi');
					$validuser = "yes" if ($ss_staff_id eq 'ktimmons');
					$validuser = "yes" if ($ss_staff_id eq 'lshankla');
					$validuser = "yes" if ($ss_staff_id eq 'macuna');
					$validuser = "yes" if ($ss_staff_id eq 'nreynold');
					$validuser = "yes" if ($ss_staff_id eq 'sabdulla');
		
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
<title>SEDL Intranet | $site_label</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead

<h1>$site_label</h1>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>
Welcome to the $site_label. This database is used by Communcations staff  
to set up <a href="$public_site_address">$item_label\s</a> for the SEDL Web site. 
Please enter your SEDL user ID and password to view the database.
</p>
<FORM ACTION="board_of_directors_manager.cgi" METHOD="POST">
<table BORDER="0" CELLPADDING="10" CELLSPACING="0">
<tr><TD VALIGN="TOP"><strong>Your user ID</strong><br>
  		  (ex: sliberty)</td>
    <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></td></tr>
<tr><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR>
  			<SPAN class="small">(not your e-mail password)</SPAN></td>
    <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></td></tr>
</table>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Log In Now">
  </div>
  </form>
<p>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
$htmltail
EOM
} 
#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################


##########################################################
## START: LOCATION PROCESS_DELETE_ITEM
##########################################################
if ($location eq 'process_delete_item') {
	my $confirm = $query->param("confirm");
	if ($confirm eq 'confirmed') {
	## START: BACKSLASH VARIABLES FOR DB
	$show_record = &commoncode::cleanthisfordb($show_record);
	## END: BACKSLASH VARIABLES FOR DB

		## DELETE THE PAGES
		my $command_delete_item = "DELETE from board_of_directors WHERE bod_id = '$show_record'";
		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command_delete_item) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#		my $num_matches = $sth->rows;
		
		$feedback_message = "You successfully deleted $item_label record \#$show_record.";
		$feedback_message .= &trigger_board_page_updates();
		$location = "menu";
	} else {
		$error_message = "ERROR: $item_label Deletion Aborted. You forgot to checkmark the CONFIRM box.";
		$location = "add_item";
	}
}
##########################################################
## END: LOCATION PROCESS_DELETE_ITEM
##########################################################



#################################################################################
## START: LOCATION = PROCESS_add_item
#################################################################################
	my $new_bod_userid = $query->param("new_bod_userid");
	my $new_bod_prefix = $query->param("new_bod_prefix");
	my $new_bod_firstname = $query->param("new_bod_firstname");
	my $new_bod_middlename = $query->param("new_bod_middlename");
	my $new_bod_lastname = $query->param("new_bod_lastname");
	my $new_bod_state = $query->param("new_bod_state");
	my $new_bod_city = $query->param("new_bod_city");
	my $new_bod_description = $query->param("new_bod_description");
	my $new_bod_current_job = $query->param("new_bod_current_job");
	my $new_bod_officer = $query->param("new_bod_officer");
	my $new_bod_email = $query->param("new_bod_email");
	my $new_bod_email_summer = $query->param("new_bod_email_summer");
	my $new_bod_phone_office = $query->param("new_bod_phone_office");
	my $new_bod_phone_home = $query->param("new_bod_phone_home");
	my $new_bod_phone_cell = $query->param("new_bod_phone_cell");
	my $new_bod_fax = $query->param("new_bod_fax");
	my $new_bod_address = $query->param("new_bod_address");
	my $new_bod_active = $query->param("new_bod_active");
	   $new_bod_active = "no" if ($new_bod_active eq '');
	my $new_bod_photo_file = $query->param("new_bod_photo_file");
	my $new_bod_notes = $query->param("new_bod_notes");


	my $new_startdate_m = $query->param("new_startdate_m");
	my $new_startdate_d = $query->param("new_startdate_d");
	my $new_startdate_y = $query->param("new_startdate_y");

	my $new_enddate_m = $query->param("new_enddate_m");
	my $new_enddate_d = $query->param("new_enddate_d");
	my $new_enddate_y = $query->param("new_enddate_y");


	my $new_bod_startdate = "$new_startdate_y\-$new_startdate_m\-$new_startdate_d";
	my $new_bod_enddate = "$new_enddate_y\-$new_enddate_m\-$new_enddate_d";

	## START: CHECK FOR DATA COPLETENESS
	if ($location eq 'process_add_item') {
		if ($new_bod_lastname eq '') {
			$error_message .= "The Board of Director Name is missing. Please try again.";
			$location = "add_item";
		} # END IF
		if ($new_bod_userid eq '') {
			$error_message .= "The Board of Director 'user ID' is missing. Please try again.";
			$location = "add_item";
		} # END IF
		if ($new_bod_state eq '') {
			$error_message .= "The Board of Director State is missing. Please try again.";
			$location = "add_item";
		} # END IF
	} # END IF
	## END: CHECK FOR DATA COPLETENESS

if ($location eq 'process_add_item') {
	if (($new_startdate_m eq '00') || ($new_startdate_d eq '00') || ($new_startdate_y eq '0000') || ($new_startdate_m eq '') || ($new_startdate_d eq '') || ($new_startdate_y eq '')) {
		$error_message .= "The board member's start date is malformed. Please update the record before quitting.";
	}

	## START: BACKSLASH VARIABLES FOR DB
	#$new_news_date_effective = &commoncode::cleanthisfordb($new_news_date_effective);
	$new_bod_userid = &commoncode::cleanthisfordb($new_bod_userid);
	$new_bod_prefix = &commoncode::cleanthisfordb($new_bod_prefix);
	$new_bod_firstname = &commoncode::cleanthisfordb($new_bod_firstname);
	$new_bod_middlename = &commoncode::cleanthisfordb($new_bod_middlename);
	$new_bod_lastname = &commoncode::cleanthisfordb($new_bod_lastname);
	$new_bod_state = &commoncode::cleanthisfordb($new_bod_state);
	$new_bod_city = &commoncode::cleanthisfordb($new_bod_city);
	$new_bod_description = &commoncode::cleanthisfordb($new_bod_description);
	$new_bod_current_job = &commoncode::cleanthisfordb($new_bod_current_job);
	$new_bod_officer = &commoncode::cleanthisfordb($new_bod_officer);
	$new_bod_email = &commoncode::cleanthisfordb($new_bod_email);
	$new_bod_email_summer = &commoncode::cleanthisfordb($new_bod_email_summer);
	$new_bod_phone_office = &commoncode::cleanthisfordb($new_bod_phone_office);
	$new_bod_phone_cell = &commoncode::cleanthisfordb($new_bod_phone_cell);
	$new_bod_phone_home = &commoncode::cleanthisfordb($new_bod_phone_home);
	$new_bod_fax = &commoncode::cleanthisfordb($new_bod_fax);
	$new_bod_address = &commoncode::cleanthisfordb($new_bod_address);
	$new_bod_active = &commoncode::cleanthisfordb($new_bod_active);
	$new_bod_photo_file = &commoncode::cleanthisfordb($new_bod_photo_file);
	$new_bod_notes = &commoncode::cleanthisfordb($new_bod_notes);
	$new_bod_startdate = &commoncode::cleanthisfordb($new_bod_startdate);
	$new_bod_enddate = &commoncode::cleanthisfordb($new_bod_enddate);
	## END: BACKSLASH VARIABLES FOR DB


	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select * from board_of_directors ";
			if ($show_record ne '') {
				$command .= "WHERE bod_id = '$show_record'";
			}
		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;
		
		$already_exists = "yes" if ($num_matches_code eq '1');

		my $add_edit_type = "added";
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update_mr = "UPDATE board_of_directors 
										SET bod_userid ='$new_bod_userid', bod_prefix ='$new_bod_prefix', bod_firstname ='$new_bod_firstname', bod_middlename ='$new_bod_middlename', bod_lastname ='$new_bod_lastname', bod_state ='$new_bod_state', bod_city ='$new_bod_city', bod_description ='$new_bod_description', bod_current_job ='$new_bod_current_job', bod_officer ='$new_bod_officer', bod_email ='$new_bod_email', bod_email_summer ='$new_bod_email_summer', bod_phone_office ='$new_bod_phone_office', bod_phone_cell ='$new_bod_phone_cell', bod_phone_home ='$new_bod_phone_home', bod_fax ='$new_bod_fax', bod_address ='$new_bod_address', bod_active ='$new_bod_active', bod_photo_file = '$new_bod_photo_file', 
										bod_notes='$new_bod_notes', bod_startdate='$new_bod_startdate', bod_enddate='$new_bod_enddate', 
										bod_last_updated='$timestamp', bod_last_updated_by ='$cookie_ss_staff_id'
										WHERE bod_id ='$show_record'";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_update_mr) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			#my $num_matches = $sth->rows;
			
			$add_edit_type = "edited";
			$feedback_message .= "The $item_label was $add_edit_type successfully and is highlighted in <a href=\"#$show_record\">YELLOW below</a>.";
			$location = "menu";
		} else {
	
			my $command_insert_mr = "INSERT INTO board_of_directors VALUES ('', '$new_bod_userid', '$new_bod_prefix', '$new_bod_firstname', '$new_bod_middlename', 
			'$new_bod_lastname', '$new_bod_state', '$new_bod_city', '$new_bod_description', '$new_bod_current_job', '$new_bod_officer', '$new_bod_email', 
			'$new_bod_email_summer', '$new_bod_phone_office', '$new_bod_phone_cell', '$new_bod_phone_home', '$new_bod_fax', '$new_bod_address', '$timestamp', 
			'$cookie_ss_staff_id', '$new_bod_active', '$new_bod_photo_file', '$new_bod_notes', '$new_bod_startdate', '$new_bod_enddate')";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_insert_mr) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			#my $num_matches = $sth->rows;

			$feedback_message .= "The $item_label was $add_edit_type successfully.";
			$location = "menu";
		} # END IF USER NAME NOT BLANK

$feedback_message .= &trigger_board_page_updates();

}
#################################################################################
## END: LOCATION = PROCESS_add_item
#################################################################################


#################################################################################
## START: LOCATION = add_item
#################################################################################
if ($location eq 'add_item') {
	my $page_title = "Add a New $item_label";

	my $bod_id = "";
	my $bod_userid = "";
	my $bod_prefix = "";
	my $bod_firstname = "";
	my $bod_middlename = "";
	my $bod_lastname = "";
	my $bod_state = "";
	my $bod_city = "";
	my $bod_description = "";
	my $bod_current_job = "";
	my $bod_officer = "";
	my $bod_email = "";
	my $bod_email_summer = "";
	my $bod_phone_office = "";
	my $bod_phone_cell = "";
	my $bod_phone_home = "";
	my $bod_fax = "";
	my $bod_address = "";
	my $bod_last_updated = "";
	my $bod_last_updated_by = "";
	my $bod_active = "";
	my $bod_photo_file = "";
	my $bod_notes;
	my $bod_startdate;
	my $bod_enddate;
	if ($show_record ne '') {
		$page_title = "Save Edits to this $item_label";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from board_of_directors WHERE bod_id = '$show_record'";
		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matching_records = $sth->rows;
		
		while (my @arr = $sth->fetchrow) {
			($bod_id, $bod_userid, $bod_prefix, $bod_firstname, $bod_middlename, $bod_lastname, $bod_state, $bod_city, $bod_description, $bod_current_job, $bod_officer, $bod_email, $bod_email_summer, $bod_phone_office, $bod_phone_cell, $bod_phone_home, $bod_fax, $bod_address, $bod_last_updated, $bod_last_updated_by, $bod_active, $bod_photo_file, $bod_notes, $bod_startdate, $bod_enddate) = @arr;
		} # END DB QUERY LOOP
	
		if ($num_matching_records == 0 ) {
			$error_message = "$num_matching_records Records Found<br><br>COMMAND: $command";
		}

	} # END IF
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
#		$partner_name = &commoncode::cleanaccents2html($partner_name);
#		$partner_description = &commoncode::cleanaccents2html($partner_description);
		$bod_last_updated = &commoncode::convert_timestamp_2pretty_w_date($bod_last_updated);

print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label: $page_title</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">

<script language="javascript" type="text/javascript" src="/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">     
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	plugins : "spellchecker,paste",
	gecko_spellcheck : true,
   	force_br_newlines : true,
   	force_p_newlines : false,
	forced_root_block : false,
	paste_auto_cleanup_on_paste : true,
 	theme_advanced_toolbar_location : "top",
   	theme_advanced_toolbar_align : "left",
	apply_source_formatting : true,
	theme_advanced_buttons1 : "bold,italic,underline,bullist,numlist,undo,redo,link,unlink,charmap,spellchecker,pastetext,pasteword,cleanup,code,styleselect,formatselect",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	content_css: "/css/sedl2012_forTinyMCE.css",
	convert_urls : false
});
</script>

$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td>

<h1><A HREF="board_of_directors_manager.cgi">$site_label</A><br>
$page_title</h1>


<p>The text edit boxes work best in the Firefox browser.</p>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<FORM ACTION="board_of_directors_manager.cgi" METHOD="POST" name="form2" id="form2">

<table border="1" cellpadding="2" cellspacing="0" width="100%">
<tr><td valign="top"><strong>Board of Directors Member Name</strong></td>
	<td valign="top">
		<table>
		<tr><td>Prefix:</td><td>
EOM
&printform_prefix("new_bod_prefix", $bod_prefix);
print<<EOM;
		</td></tr>
		<tr><td valign="top"><br><br>First:</td><td><textarea name="new_bod_firstname" id="new_bod_firstname" rows="6" cols="70">$bod_firstname</textarea></td></tr>
		<tr><td valign="top"><br><br>Middle:</td>
			<td><textarea name="new_bod_middlename" id="new_bod_middlename" rows="6" cols="70">$bod_middlename</textarea></td><tr>
		<tr><td valign="top"><br><br>Last:</td>
			<td><textarea name="new_bod_lastname" id="new_bod_lastname" rows="6" cols="70">$bod_lastname</textarea></td></tr>
		</table>
	</td></tr>
<tr><td valign="top"><strong><label for="new_bod_userid">User ID</label></strong></td>
	<td valign="top"><INPUT type="text" name="new_bod_userid" id="new_bod_userid" size="30" value="$bod_userid"><br>
		Enter the persons first initial and lastname inthe format: "whoover"  This is used to save the person's profile page to the web under this filename. (e.g. whoover.html)
	</td></tr>
<tr><td valign="top"><strong><label for="new_bod_photo_file">Photo file name</label></strong></td>
	<td valign="top"><img src="/images/people/$bod_photo_file" style="float:right;">
					 <INPUT type="text" name="new_bod_photo_file" id="new_bod_photo_file" size="25" value="$bod_photo_file"> (e.g. "whoover.jpg")<br>
						The photo file should be saved in "www.sedl.org/images/people/"<br>
					
	</td></tr>
<tr><td valign="top"><strong>Active</strong></td>
	<td valign="top">
EOM
&commoncode::printform_yes_no_menu("new_bod_active", $bod_active);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong>Officer</strong></td>
	<td valign="top">
EOM
&printform_bod_officer("new_bod_officer", $bod_officer);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong>State</strong></td>
	<td valign="top">
EOM
&commoncode::printform_state_board_of_directors("new_bod_state", $bod_state);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong>City</strong></td>
	<td valign="top"><textarea name="new_bod_city" rows="10" cols="70">$bod_city</textarea>
	</td></tr>
<tr><td valign="top"><strong>E-mail</strong></td>
	<td valign="top"><INPUT type="text" name="new_bod_email" size="30" value="$bod_email">
	</td></tr>
<tr><td valign="top"><strong>E-mail (summer)</strong></td>
	<td valign="top"><INPUT type="text" name="new_bod_email_summer" size="30" value="$bod_email_summer">
	</td></tr>

<tr><td valign="top"><strong>Phone</strong></td>
	<td valign="top">	<table cellpadding="2" cellspacing="0" border="0">
						<tr><td>Office:</td>
							<td><INPUT type="text" name="new_bod_phone_office" size="25" value="$bod_phone_office"></td></tr>
						<tr><td>Cell:</td>
							<td><INPUT type="text" name="new_bod_phone_cell" size="25" value="$bod_phone_cell"></td></tr>
						<tr><td>Home:</td>
							<td><INPUT type="text" name="new_bod_phone_home" size="25" value="$bod_phone_home"></td></tr>
						<tr><td>Fax:</td>
							<td><INPUT type="text" name="new_bod_fax" size="25" value="$bod_fax"></td></tr>
						</table>
	</td></tr>

<tr><td valign="top"><strong>Address</strong></td>
	<td valign="top"><textarea name="new_bod_address" rows="10" cols="70">$bod_address</textarea>
	</td></tr>

<tr><td valign="top"><strong>Description</strong></td>
	<td valign="top"><textarea name="new_bod_description" rows="20" cols="70">$bod_description</textarea>
	</td></tr>

<tr><td valign="top"><strong>Current job</strong></td>
	<td valign="top"><textarea name="new_bod_current_job" rows="12" cols="70">$bod_current_job</textarea>
	</td></tr>

<tr><td valign="top"><strong>Date Began Board Service</strong></td>
	<td valign="top">
EOM
		my ($old_y, $old_m, $old_d) = split(/\-/,$bod_startdate);
		&commoncode::print_month_menu("new_startdate_m", $old_m);
		&commoncode::print_day_menu("new_startdate_d", $old_d);
		&commoncode::print_year_menu("new_startdate_y", 2001, $year + 1, $old_y);
print<<EOM;
	</td></tr>

<tr><td valign="top"><strong>Date Ended Board Service</strong></td>
	<td valign="top">
EOM
		my ($old_y, $old_m, $old_d) = split(/\-/,$bod_enddate);
		&commoncode::print_month_menu("new_enddate_m", $old_m);
		&commoncode::print_day_menu("new_enddate_d", $old_d);
		&commoncode::print_year_menu("new_enddate_y", 2001, $year + 1, $old_y);
print<<EOM;
	</td></tr>

<tr><td valign="top"><strong>Notes</strong></td>
	<td valign="top"><textarea name="new_bod_notes" rows="12" cols="70">$bod_notes</textarea>
	</td></tr>

<tr><td valign="top"><strong>Last Updated</strong></td>
	<td valign="top">$bod_last_updated by $bod_last_updated_by</td></tr>

</table>



	<UL>
		<input type="hidden" name="show_inactive" value="$show_inactive">
		<INPUT TYPE=HIDDEN NAME="show_record" VALUE="$show_record">
		<INPUT TYPE=HIDDEN NAME="location" VALUE="process_add_item">
	<INPUT TYPE=SUBMIT VALUE="$page_title">
	</UL>
</form>
EOM
if ($show_record ne '') {
print<<EOM;
<p>
<table border="0" cellpadding="0" cellsoacing="0" align="right">
<tr><td valign="top">
<div class="first fltRt">
		<FORM ACTION="board_of_directors_manager.cgi" METHOD=POST>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr><td colspan="2"><em>Click here to delete this $item_label.</em></td></tr>
		<tr><td valign="top"><input type="checkbox" name="confirm" value="confirmed"></td>
			<td valign="top"><font color=red>confirm the deletion<br> of this $item_label.</font></td></tr>
		<tr><td colspan="2">
				<input type="hidden" name="show_inactive" value="$show_inactive">
				<input type="hidden" name="location" value="process_delete_item">
				<input type="hidden" name="show_record" value="$show_record">
				<input type="submit" name="submit" value="Delete $item_label"><br>
				Note: When a board member ends their service,<br>set them to "inactive" instead of deleting them.</td></tr>
				
		</table>
		</form>
	
	</div>
	</td></tr>
	</table>
EOM
}
print<<EOM;
</td>
	<td valign="top" align="right">
		(Click here to <A HREF="board_of_directors_manager.cgi?location=logout">logout</A>)
		<P>
	</td></tr>
</table>


$htmltail
EOM
}
#################################################################################
## END: LOCATION = add_item
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
<TITLE>SEDL Intranet | $site_label: List of $item_label\s</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1><A HREF="board_of_directors_manager.cgi">$site_label</A>
		<br>List of $item_label\s</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="board_of_directors_manager.cgi?location=logout">logout</A>)
	</td></tr>
</table>
EOM

	if ($validuser ne 'yes') {
		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view $item_label\s.</FONT>";
	}

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

my $command = "select * from board_of_directors";
	if ($show_inactive eq '') {
		$command .= " WHERE bod_active like 'yes'";
	} else {
		$command .= " WHERE bod_active like '%'";
	}
	$command .= " order by bod_lastname" if $sortby eq '';
	$command .= " order by bod_state, bod_firstname" if $sortby eq 'state';
	$command .= " order by bod_last_updated DESC, bod_lastname" if $sortby eq 'lastupdated';
#	$command .= " order by active, partner_name" if $sortby eq 'active';



#print "<P>$command<P>";
$dsn = "DBI:mysql:database=corp;host=localhost";
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches_items = $sth->rows;

my $col_heading_name = "First Name";
   $col_heading_name = "<a href=\"board_of_directors_manager.cgi\">First Name</a>" if ($sortby ne '');
my $col_heading_state = "State";
   $col_heading_state = "<a href=\"board_of_directors_manager.cgi?sortby=state\">State</a>" if ($sortby ne 'state');
my $col_heading_lastupdated = "Last Updated";
   $col_heading_lastupdated = "<a href=\"board_of_directors_manager.cgi?sortby=lastupdated\">Last Updated</a>" if ($sortby ne 'lastupdated');
my $col_heading_active = "Active?";
   $col_heading_active = "<a href=\"board_of_directors_manager.cgi?sortby=active\">Active?</a>" if ($sortby ne 'active');

print<<EOM;
<P>
There are $num_matches_items $item_label\s on file that are shown on SEDL <a href="$public_site_address" target="_blank">$item_label\s</a> site).<br>
Click here to 
<a href="board_of_directors_manager.cgi?show_inactive=yes">show past/inactive board members</a>.
<p>
<FORM ACTION="board_of_directors_manager.cgi" METHOD="POST" name="form2" id="form2">
Click here to 
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="add_item">
	<INPUT TYPE=SUBMIT VALUE="Add a New $item_label">
	</FORM>
<P>
<table border="1" cellpadding="3" cellspacing="0">
<TR bgcolor="#ebebeb">
	<td><strong>#</strong></td>
	<td><strong>$col_heading_name</strong></td>
	<td><strong>Description<br>on file?</strong></td>
	<td><strong>$col_heading_state</strong></td>
	<td><strong>E-mail/Phone</strong></td>
	<td><strong>$col_heading_lastupdated</strong></td>
	<td><strong>$col_heading_active</strong></td>
	<td><strong>Photo</strong></td>
</tr>
EOM


	if ($num_matches_items == 0) {
		print "<P><FONT COLOR=RED>There are no items in the database.</FONT>";
	}
my $counter = 1;
	while (my @arr = $sth->fetchrow) {
		my ($bod_id, $bod_userid, $bod_prefix, $bod_firstname, $bod_middlename, $bod_lastname, $bod_state, $bod_city, $bod_description, $bod_current_job, $bod_officer, $bod_email, $bod_email_summer, $bod_phone_office, $bod_phone_cell, $bod_phone_home, $bod_fax, $bod_address, $bod_last_updated, $bod_last_updated_by, $bod_active, $bod_photo_file, $bod_notes) = @arr;

		my $bgcolor="";
  			$bgcolor="BGCOLOR=\"#cccccc\"" if ($bod_active ne 'yes');
  			$bgcolor="BGCOLOR=\"#FFFFCC\"" if ($show_record eq $bod_id);
		   $bod_active = "<font color=\"red\">no</font>" if ($bod_active ne 'yes');
		my $description_status = "<font color=\"red\">no</font>";
		   $description_status = "yes" if ($bod_description ne '');
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$bod_last_updated = &commoncode::convert_timestamp_2pretty_w_date($bod_last_updated);
print<<EOM;
<TR $bgcolor>
	<td valign="top"><a name="$bod_id"></a>$counter</td>
	<td valign="top"><A HREF=\"board_of_directors_manager.cgi?location=add_item&amp;show_record=$bod_id&amp;show_inactive=$show_inactive\" TITLE="Click to edit this $item_label">$bod_prefix $bod_firstname $bod_middlename $bod_lastname</a>
EOM
print "<br>$bod_officer" if ($bod_officer ne '');
print<<EOM;
	</td>
	<td valign="top">$description_status</td>
	<td valign="top">$bod_state</td>
	<td valign="top">$bod_email
EOM
print "<br><em>summer:</em> $bod_email_summer" if ($bod_email_summer ne '');
	## IF NO OFFICE PHONE, SET DEFAULT TO OTHER PHONE
	$bod_phone_office = $bod_phone_cell if ($bod_phone_office eq '');
	$bod_phone_office = $bod_phone_home if ($bod_phone_office eq '');
print "<br>$bod_phone_office" if ($bod_phone_office ne '');
print<<EOM;
	</td>
	<td valign="top">$bod_last_updated<br>$bod_last_updated_by</td>
	<td valign="top">$bod_active</td>
	<td valign="top">
EOM
if ($bod_photo_file ne '') {
print<<EOM;

		<img src="/images/people/$bod_photo_file" height="50">
EOM
}
print<<EOM;
	</td>
</tr>
EOM
		$counter++;
	} # END DB QUERY LOOP
print<<EOM;
</table>
<P>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
<P>
The $site_label is located at <A HREF="$public_site_address">$public_site_address</A>.
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


######################################
## START: SUBROUTINE printform_prefix
######################################
sub printform_prefix {
	my $form_variable_name = $_[0];
	my $selected_item = $_[1];
	my $counter_item = "0";
	my @items = ("Dr.", "Ms.", "Mrs.", "Mr.");

	print "<SELECT NAME=\"$form_variable_name\"><OPTION VALUE=\"\"></OPTION>";
	while ($counter_item <= $#items) {
		print "<OPTION VALUE=\"$items[$counter_item]\"";
		print " SELECTED" if ($items[$counter_item] eq $selected_item);
		print ">$items[$counter_item]";
		$counter_item++;
	} # END WHILE
	print "</SELECT>";
} # END subroutine printform_prefix
######################################
## END: SUBROUTINE printform_prefix
######################################

###########################################
## START: SUBROUTINE printform_bod_officer
###########################################
sub printform_bod_officer {
	my $form_variable_name = $_[0];
	my $selected_item = $_[1];
	my $counter_item = "0";
	my @items = ("Chairman", "Chairwoman", "Vice Chairman", "Vice Chairwoman", "Secretary", "Treasurer", "Immediate-Past Chairman", "Immediate-Past Chairwoman");

	print "<SELECT NAME=\"$form_variable_name\"><OPTION VALUE=\"\"></OPTION>";
	while ($counter_item <= $#items) {
		print "<OPTION VALUE=\"$items[$counter_item]\"";
		print " SELECTED" if ($items[$counter_item] eq $selected_item);
		print ">$items[$counter_item]";
		$counter_item++;
	} # END WHILE
	print "</SELECT>";
} # END subroutine printform_prefix
###########################################
## END: SUBROUTINE printform_bod_officer
###########################################


########################################################################################################################
# START: SUBROUTINE: TRIGGER BORAD PAGE UPDATES
########################################################################################################################
sub trigger_board_page_updates {
	$dsn = "DBI:mysql:database=corp;host=localhost";

	##############################
	## START: GRAB PAGE TEMPLATE
	##############################
	my $template = "";
	open(TEMPLATE,"</home/httpd/html/common/templates/sedl2012.html");
		while (<TEMPLATE>) {
			$template .= $_;
		}
	close(TEMPLATE);

	my ($pre_title, $header, $pre_sidenav, $pre_centerpiece, $post_centerpiece, $footer) = split(/QQQ/,$template);
	##############################
	## END: GRAB PAGE TEMPLATE
	##############################



	###################################
	# START: LIST BOARD OF DIRECTORS
	###################################
	## OPEN A FILE TO SAVE THE BOARD OF DIRECTORS
open(BOARDOFDIRECTORSPAGE,">/home/httpd/html/about/board.html");
open(BOARDOFDIRECTORSINCLUDE,">/home/httpd/html/common/includes/boardphotos_forannualreport.txt");

## PRINT PAGE HEADER
print BOARDOFDIRECTORSPAGE <<EOM;
$pre_title
About SEDL - Board of Directors
$header

<!-- This page is autogenerated by a database on the SEDL intranet.  See the webmaster for more details. -->

$pre_sidenav

   <p class="tocheader"><a href="/about/">Our Company</a></p>
    
   <div id="nav">
   <ul class="level1">
		<li class="submenu"><a href="/about/annualreport.html">Annual Report</a></li>
		<li class="submenu"><a href="/about/success-stories.html">Success Stories</a></li>
		<li class="submenu"><a href="/about/history.html">History Timeline</a></li>
		<li class="submenu"><a href="/about/in-the-community.html">SEDL in the Community</a></li>
		<li class="submenu"><a href="/cgi-bin/mysql/corp/contact.cgi">Contact Us</a></li>
		<li class="submenu"><a href="/support/">Support SEDL</a></li>
	</ul>
  </div>
  <p class="tocheader2">Our People</p>
   <div id="nav2">
   <ul class="level1">
      	<li class="submenu active"><a href="/about/board.html">Board of Directors</a></li>
		<li class="submenu"><a href="/about/management.html">Management Team</a></li>
		<li class="submenu"><a href="/about/staff.html">Staff</a></li>
		<li class="submenu"><a href="/about/partners.html">Partners</a></li>
        <li class="submenu"><a href="/about/careers.html">Careers</a></li>
	</ul>
	</div>
 
$pre_centerpiece
<div id="mainContentPadding">
	<p id="breadcrumbs" role="navigation">
		<a href="/" title="SEDL Home" class="crumb">Home</a> | <a href="/about/" class="crumb">About Us</a> | Board of Directors
	</p>
	<h1>Board of Directors</h1>

EOM

################################################
## START: GET COUNT OF TOTAL BOARD MEMBERS
################################################
my $command = "select * from board_of_directors where bod_active LIKE 'yes'";
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $total_num_board_members = $sth->rows;

print BOARDOFDIRECTORSPAGE<<EOM;
<p>
SEDL's work is guided by a $total_num_board_members\-member board of directors with one national representative and representatives drawn from Alabama, Arkansas, Louisiana, Mississippi, New Mexico, North Carolina, Oklahoma, and Texas.
</p>

<h2>Officers of the Board</h2>
EOM
################################################
## END: GET COUNT OF TOTAL BOARD MEMBERS
################################################


## START: PRINT BULLETED LIST OF BOARD OFFICERS
my @officers = ("Chairman", "Chairwoman", "Vice Chairman", "Vice Chairwoman", "Secretary", "Acting Secretary", "Treasurer", "Immediate-Past Chairman", "Immediate-Past Chairwoman");
my $officers_loop_counter = 0;

	while ($officers_loop_counter <= $#officers) {
		my $command = "select * from board_of_directors where bod_active LIKE 'yes' AND bod_officer like '$officers[$officers_loop_counter]'";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
			while (my @arr = $sth->fetchrow) {
				my ($bod_id, $bod_userid, $bod_prefix, $bod_firstname, $bod_middlename, $bod_lastname, $bod_state, $bod_city, $bod_description, $bod_current_job, $bod_officer, $bod_email, $bod_email_summer, $bod_phone_office, $bod_phone_cell, $bod_phone_home, $bod_fax, $bod_address, $bod_last_updated, $bod_last_updated_by, $bod_active, $bod_photo_file, $bod_notes) = @arr;
				$bod_state = &commoncode::getFullStateName($bod_state);
				$bod_firstname = "$bod_firstname $bod_middlename" if ($bod_middlename ne '');
				print BOARDOFDIRECTORSPAGE "<p class=\"boardmembers\"><a href=\"/about/boardmembers/$bod_userid.html\">$bod_firstname $bod_lastname</a> ($bod_state) <em>$officers[$officers_loop_counter]</em></p>\n";
		} # END DB QUERY LOOP

		$officers_loop_counter++;
	}
## END: PRINT BULLETED LIST OF BOARD OFFICERS


## START: PRINT LIST OF BOARD MAMBERS, SORTED BY STATE
print BOARDOFDIRECTORSPAGE<<EOM;
<h2 style="margin-bottom:0;padding-bottom:0;">Members of the Board</h2>
	<div id="board_member_container" style="margin:0;padding:0;">
EOM

########################################################
## START: OKLAHOMA AND TEXAS
########################################################
my @states = ("New Mexico", "North Carolina", "Oklahoma", "Texas");
my @states_abbr = ("nm", "nc", "ok", "tx");
#my %sedlstates;

my $counter_state = "0";
	# START WHILE LOOP THROUGH STATES
	while ($counter_state <= $#states) {

if ($states[$counter_state] eq 'New Mexico') {
print BOARDOFDIRECTORSPAGE<<EOM;
<div style="width:48%;float:right;margin:0;padding:0;">
EOM
}
my $h3_margin = "";
   $h3_margin = " style=\"margin-top:2px;padding-top:0;\"" if ($states[$counter_state] eq 'New Mexico');
	# RESET ROW BG COLOR
	my $row_bgcolor = "#F5F5F5"; # ALTERNATE WITH #F5F5F5
		## QUERY DATABASE TO LIST BOARD EMBERS FROM THIS STATE
		my $command = "select * from board_of_directors 
						where bod_active LIKE 'yes' 
						AND bod_state like '$states_abbr[$counter_state]' 
						AND bod_firstname NOT LIKE '' 
						order by bod_lastname, bod_firstname";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;

my $s = "";
   $s = "s" if ($num_matches > 1);
print BOARDOFDIRECTORSPAGE<<EOM;
<h3 $h3_margin>$states[$counter_state] Board Member$s</h3>
EOM

			while (my @arr = $sth->fetchrow) {
			my ($bod_id, $bod_userid, $bod_prefix, $bod_firstname, $bod_middlename, $bod_lastname, $bod_state, $bod_city, 
			$bod_description, $bod_current_job, $bod_officer, $bod_email, $bod_email_summer, 
			$bod_phone_office, $bod_phone_cell, $bod_phone_home, $bod_fax, $bod_address, $bod_last_updated, $bod_last_updated_by, $bod_active, $bod_photo_file, $bod_notes) = @arr;

			$bod_firstname = "$bod_firstname $bod_middlename" if ($bod_middlename ne '');
			$bod_firstname = &commoncode::cleanaccents2html($bod_firstname);
			$bod_lastname = &commoncode::cleanaccents2html($bod_lastname);
			$bod_current_job = &commoncode::cleanaccents2html($bod_current_job);
			$bod_city = &commoncode::cleanaccents2html($bod_city);
		if ($row_bgcolor eq '#E7E7E7') {
			$row_bgcolor = "#F5F5F5";
		} else {
			$row_bgcolor = "#E7E7E7";
		}

	if ($bod_photo_file ne '') {
print BOARDOFDIRECTORSINCLUDE<<EOM;
<a href="/about/boardmembers/$bod_userid.html"><img src="/images/people/$bod_photo_file" height="124" alt="Photo of $bod_prefix $bod_firstname $bod_lastname" title="$bod_prefix $bod_firstname $bod_lastname from $bod_city, $states[$counter_state]" class="noBorder"></a>
EOM
	}

print BOARDOFDIRECTORSPAGE<<EOM;
<div style="height:155px;">
EOM
	if ($bod_photo_file ne '') {
print BOARDOFDIRECTORSPAGE<<EOM;
<a href="/about/boardmembers/$bod_userid.html"><img src="/images/people/$bod_photo_file" height="124" alt="Photo of $bod_prefix $bod_firstname $bod_lastname" class="fltleft noBorder" style="padding-right:10px"></a>
EOM
	}
print BOARDOFDIRECTORSPAGE<<EOM;
<a href="/about/boardmembers/$bod_userid.html">$bod_prefix $bod_firstname $bod_lastname</a><br>
EOM
print BOARDOFDIRECTORSPAGE"$bod_current_job<br>" if ($bod_current_job ne '');
print BOARDOFDIRECTORSPAGE<<EOM;
<em>$bod_city</em>
</div>
EOM
			}
		$counter_state++;
	} # END WHILE LOOP THROUGH STATES

########################################################
## END: OKLAHOMA AND TEXAS
########################################################

print BOARDOFDIRECTORSPAGE<<EOM;
		</div>
		<div style="width:48%;float:left;margin:0;padding:0;">
EOM

########################################################
## START: ARKANSAS, LOUISIANA, AND NEW MEXICO
########################################################
my @states = ("Alabama", "Arkansas", "Louisiana", "Mississippi", "National");
my @states_abbr = ("al", "ar", "la", "ms", "national");
#my %sedlstates;

my $counter_state = "0";
	# START WHILE LOOP THROUGH STATES
	while ($counter_state <= $#states) {
my $h3_margin = "";
   $h3_margin = " style=\"margin-top:2px;padding-top:0;\"" if ($states[$counter_state] eq 'Arkansas');

	# RESET ROW BG COLOR
	my $row_bgcolor = "#F5F5F5"; # ALTERNATE WITH #F5F5F5
		## QUERY DATABASE TO LIST BOARD EMBERS FROM THIS STATE
		my $command = "select * from board_of_directors 
						where bod_active LIKE 'yes' 
						AND bod_state like '$states_abbr[$counter_state]' 
						AND bod_firstname NOT LIKE '' 
						order by bod_lastname, bod_firstname";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;

my $s = "";
   $s = "s" if ($num_matches > 1);
print BOARDOFDIRECTORSPAGE<<EOM;
<h3 $h3_margin>$states[$counter_state] Board Member$s</h3>
EOM

			while (my @arr = $sth->fetchrow) {
			my ($bod_id, $bod_userid, $bod_prefix, $bod_firstname, $bod_middlename, $bod_lastname, $bod_state, $bod_city, 
			$bod_description, $bod_current_job, $bod_officer, $bod_email, $bod_email_summer, 
			$bod_phone_office, $bod_phone_cell, $bod_phone_home, $bod_fax, $bod_address, $bod_last_updated, $bod_last_updated_by, $bod_active, $bod_photo_file, $bod_notes) = @arr;

			$bod_firstname = "$bod_firstname $bod_middlename" if ($bod_middlename ne '');
			$bod_firstname = &commoncode::cleanaccents2html($bod_firstname);
			$bod_lastname = &commoncode::cleanaccents2html($bod_lastname);
			$bod_current_job = &commoncode::cleanaccents2html($bod_current_job);
			$bod_city = &commoncode::cleanaccents2html($bod_city);
		if ($row_bgcolor eq '#E7E7E7') {
			$row_bgcolor = "#F5F5F5";
		} else {
			$row_bgcolor = "#E7E7E7";
		}

	if ($bod_photo_file ne '') {
print BOARDOFDIRECTORSINCLUDE<<EOM;
<a href="/about/boardmembers/$bod_userid.html"><img src="/images/people/$bod_photo_file" height="124" alt="Photo of $bod_prefix $bod_firstname $bod_lastname" title="$bod_prefix $bod_firstname $bod_lastname from $bod_city, $states[$counter_state]" class="noBorder"></a>
EOM
	}


print BOARDOFDIRECTORSPAGE<<EOM;
<div style="height:155px;">
EOM
	if ($bod_photo_file ne '') {
print BOARDOFDIRECTORSPAGE<<EOM;
<a href="/about/boardmembers/$bod_userid.html"><img src="/images/people/$bod_photo_file" height="124" alt="Photo of $bod_prefix $bod_firstname $bod_lastname" class="fltleft noBorder" style="padding-right:10px"></a>
EOM
	}
print BOARDOFDIRECTORSPAGE<<EOM;
<a href="/about/boardmembers/$bod_userid.html">$bod_prefix $bod_firstname $bod_lastname</a><br>
EOM
print BOARDOFDIRECTORSPAGE"$bod_current_job<br>" if ($bod_current_job ne '');
print BOARDOFDIRECTORSPAGE<<EOM;
<em>$bod_city</em>
</div>
EOM
			}
		$counter_state++;
	} # END WHILE LOOP THROUGH STATES




	## PRINT FOOTER AND CLOSE/SAVE FILE
######################
## PRINT PAGE FOOTER #
######################
print BOARDOFDIRECTORSPAGE<<EOM;
		</div>
	</div>
</div><!-- end div "mainContentPadding" -->
$post_centerpiece
$footer
EOM
	close(BOARDOFDIRECTORSINCLUDE);
	close(BOARDOFDIRECTORSPAGE);
	########################################################################################################################
	# END: LIST BOARD OF DIRECTORS
	########################################################################################################################




	########################################################################################################################
	## START: PRINT INDIVIDUAL BOARD PAGES
	########################################################################################################################

		my $command = "select * from board_of_directors where bod_active LIKE 'yes' order by bod_lastname, bod_firstname";

		## OPEN THE DATABASE AND SEND THE QUERY
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
			while (my @arr = $sth->fetchrow) {
			my ($bod_id, $bod_userid, $bod_prefix, $bod_firstname, $bod_middlename, $bod_lastname, $bod_state, $bod_city, 
			$bod_description, $bod_current_job, $bod_officer, $bod_email, $bod_email_summer, 
			$bod_phone_office, $bod_phone_cell, $bod_phone_home, $bod_fax, $bod_address, $bod_last_updated, $bod_last_updated_by, $bod_active, $bod_photo_file, $bod_notes) = @arr;

			## OPEN A FILE TO SAVE THE STAFF MEMBER's UNIQUE PROFILE PAGE
			open(BOARD_PROFILE_PAGE,">/home/httpd/html/about/boardmembers/$bod_userid.html");

		###############################################
		## START: PRINT PAGE HEADER
		###############################################
## START: GENERIC HEADER STUFF
#	if (length($middleinitial) == 1) {
#		$middleinitial = "$middleinitial\.";
#	}

print BOARD_PROFILE_PAGE<<EOM;
$pre_title
SEDL Board of Directors Member: $bod_firstname $bod_lastname
$header

<!-- This page is autogenerated by a database on the SEDL intranet.  See the webmaster for more details. -->

$pre_sidenav
   <p class="tocheader"><a href="/about/">Our Company</a></p>
    
   <div id="nav">
   <ul class="level1">
		<li class="submenu"><a href="/about/annualreport.html">Annual Report</a></li>
		<li class="submenu"><a href="/about/success-stories.html">Success Stories</a></li>
		<li class="submenu"><a href="/about/history.html">History Timeline</a></li>
		<li class="submenu"><a href="/about/in-the-community.html">SEDL in the Community</a></li>
		<li class="submenu"><a href="/cgi-bin/mysql/corp/contact.cgi">Contact Us</a></li>
		<li class="submenu"><a href="/support/">Support SEDL</a></li>
	</ul>
  </div>
  <p class="tocheader2">Our People</p>
   <div id="nav2">
   <ul class="level1">
      	<li class="submenu active"><a href="/about/board.html">Board of Directors</a></li>
		<li class="submenu"><a href="/about/management.html">Management Team</a></li>
		<li class="submenu"><a href="/about/staff.html">Staff</a></li>
		<li class="submenu"><a href="/about/partners.html">Partners</a></li>
        <li class="submenu"><a href="/about/careers.html">Careers</a></li>
	</ul>
	</div>
 
$pre_centerpiece
<div id="mainContentPadding">
	<p id="breadcrumbs" role="navigation">
		<a href="/" title="SEDL Home" class="crumb">Home</a> | <a href="/about/" class="crumb">About Us</a> | <a href="/about/board.html" class="crumb">Board of Directors</a> | $bod_firstname $bod_lastname
	</p>
	<h1>Board of Directors</h1>
EOM
## END: GENERIC HEADER STUFF


		###############################################
		## END: PRINT PAGE HEADER
		###############################################



		###############################################
		## START: PRINT BOARD MEMBER DETAILS
		###############################################
		## CLEAN HTML ENTITIES
			$bod_firstname = "$bod_firstname $bod_middlename" if ($bod_middlename ne '');
			$bod_firstname = &commoncode::cleanaccents2html($bod_firstname);
			$bod_lastname = &commoncode::cleanaccents2html($bod_lastname);
			$bod_current_job = &commoncode::cleanaccents2html($bod_current_job);
			$bod_city = &commoncode::cleanaccents2html($bod_city);
			$bod_description = &commoncode::cleanaccents2html($bod_description);

			## GET FULL STATE NAME
			$bod_state = &commoncode::getFullStateName($bod_state);

print BOARD_PROFILE_PAGE<<EOM;
<p>
SEDL's work is guided by a $total_num_board_members\-member board of directors with one national representative and representatives drawn from Alabama, Arkansas, Louisiana, Mississippi, New Mexico, North Carolina, Oklahoma, and Texas.
</p>



<p><img src="http://www.sedl.org/images/dotted-line.jpg" alt=" " width="510" height="3">
</p>
EOM
if ($bod_photo_file ne '') {
print BOARD_PROFILE_PAGE<<EOM;
<IMG SRC="/images/people/$bod_photo_file" ALT="Photo of $bod_firstname $bod_lastname" class="fltrt oneBorder">
EOM
}
print BOARD_PROFILE_PAGE<<EOM;
<h2>$bod_firstname $bod_lastname<BR>
<SPAN CLASS=\"normalText\">
EOM
if ($bod_city ne '') {
print BOARD_PROFILE_PAGE<<EOM;
$bod_city, 
EOM
}
print BOARD_PROFILE_PAGE<<EOM;
$bod_state</SPAN></h2>
EOM
if ($bod_officer ne '') {
	print BOARD_PROFILE_PAGE "<p>$bod_firstname $bod_lastname is the $bod_officer of SEDL's Board of Directors.</p>";
}
if ($bod_current_job ne '') {
print BOARD_PROFILE_PAGE<<EOM;
<p></p>
<strong>Current Work</strong><BR>
$bod_current_job
EOM
}
if ($bod_description ne '') {
print BOARD_PROFILE_PAGE<<EOM;
<p></p>
<strong>Experience/Education</strong><BR>
$bod_description
<p></p>
EOM
}
		###############################################
		## END: PRINT BOARD MEMBER DETAILS
		###############################################
######################
## PRINT PAGE FOOTER #
######################
print BOARD_PROFILE_PAGE<<EOM;
</div><!-- end div "mainContentPadding" -->
$post_centerpiece
$footer
EOM

	} # END DB QUERY LOOP
	########################################################################################################################
	## START: PRINT INDIVIDUAL BOARD PAGES
	########################################################################################################################

my $return_message = "";

##################################################################################
## START: OPEN DIRECTORY, READ FILE LIST, AND PUSH VALID FILES INTO SECOND ARRAY
##################################################################################
#print "<H3>Checking for orphaned author profile files...</H3>";
opendir(DIR, "/home/httpd/html/about/boardmembers/");
my @files = readdir(DIR);
@files = sort(@files);
my $numerofarrayitems = @files;
my $nextslot = "0";
my $nextimagename = "";
my $counter = "0";
my $validuser_counter = "0";
my $orphans_found = "0";

while ($counter <= $numerofarrayitems) {
	if ($files[$counter] =~ '.html') {
		$validuser_counter++;
		my $this_file = "/home/httpd/html/about/boardmembers/$files[$counter]";
		my @temp = stat $this_file;
		my $this_time = $temp[9];
		   $this_time = localtime($this_time);
		my $current_time = "$month";
			if (($this_time !~ $month_name_abbr) && ($files[$counter] ne 'welcome.html')) {
				$return_message .= "<BR><br>DELETING ORPHAN BOARD OF DIRECTORS PROFILE PAGE: File \"last editied\" time contains a prior month: \'$this_time\' - Current month: \'$month_name_abbr\'<FONT COLOR=RED>DELETING ORPHANED PROFILE PAGE: $files[$counter]</FONT>";
				## ISSUE A SYSTEM COMMAND TO DELETE THE ORPHANS
				system "rm /home/httpd/html/about/boardmembers/$files[$counter]";
				$orphans_found++;
			}
	} # END IF
	$counter++;
} # END WHILE
$return_message .= "<P>FINISHED CHECKING FOR ORPHANED FILES - FOUND $orphans_found ORPHANS";
##################################################################################
## END: OPEN DIRECTORY, READ FILE LIST, AND PUSH VALID FILES INTO SECOND ARRAY
##################################################################################



return($return_message);


}
########################################################################################################################
# END: SUBROUTINE: TRIGGER BORAD PAGE UPDATES
########################################################################################################################



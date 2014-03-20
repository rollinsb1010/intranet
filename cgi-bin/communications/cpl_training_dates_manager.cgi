#!/usr/bin/perl

#####################################################################################################
# Copyright 2008 by SEDL
#
# This script is used by SEDL Communications staff to manage the SEDL Corporate Web site: Center for Professional Learning
# Written by Brian Litke 9-24-2008
#####################################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use Number::Format; # ROUNDS NUMBERS TO SPECIFIC DECIMAL PLACE
use cpl_shared_functions;
use DBI;
my $dsn = "DBI:mysql:database=corp;host=www.sedl.org";
#my $dbh = DBI->connect($dsn, $database_username, $database_password);
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
my $item_label = "CPL Training Date";
my $site_label = "CPL Training Dates Manager";
my $public_site_address = "http://www.sedl.org/cpl/";

	## START: MYSQL VARIABLES
	my $database_name = "corp";
	my $database_username = "corpuser";
	my $database_password = "public";
	my $database_table_name = "cpl_training_dates";
	my $database_primary_field_name = "cpltd_id";
	## END: MYSQL VARIABLES

my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $logon_pass = $query->param("logon_pass");

my $location = param('location');
   $location = "menu" if $location eq '';

my $showsession = param('showsession');


my $error_message = "";
my $feedback_message = "";
my $show_record = $query->param("show_record");
my $sortby = $query->param("sortby");
   $sortby = "date_asc" if ($sortby eq '');

my $new_type = $query->param("new_type");

 	## START: BACKSLASH SCRIPT-WIDE VARIABLES FOR DB
	$show_record = &commoncode::cleanthisfordb($show_record);
	$sortby= &commoncode::cleanthisfordb($sortby);
	$logon_user= &commoncode::cleanthisfordb($logon_user);
	$logon_pass= &commoncode::cleanthisfordb($logon_pass);
	$session_id= &commoncode::cleanthisfordb($session_id);
	## END: BACKSLASH SCRIPT-WIDE VARIABLES FOR DB
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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("332"); # 332 is the PID for this page in the intranet database

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
	my $dsn = "DBI:mysql:database=intranet;host=www.sedl.org";
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
	my $dsn = "DBI:mysql:database=intranet;host=www.sedl.org";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";

		while (my @arr = $sth->fetchrow) {
		my ($ss_session_id, $ss_staff_id, $ss_session_last_active, $ss_ip_number, $ss_session_active, $ss_staff_project, $ss_search_site, $ss_last_page_viewed) = @arr;
			## IF SESSION STILL ACTIVE, SET TIMESTAMP IN SESSION DB
				my $command_update_session = "REPLACE into staff_sessions VALUES ('$ss_session_id', '$ss_staff_id', '$timestamp', '$ipnum2', '', '', '' ,'')";
				my $dsn = "DBI:mysql:database=intranet;host=www.sedl.org";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
				#my $num_matches = $sth->rows;

					$validuser = "yes" if ($ss_staff_id eq 'blitke');
					$validuser = "yes" if ($ss_staff_id eq 'brollins');
					$validuser = "yes" if ($ss_staff_id eq 'cmolina');
					$validuser = "yes" if ($ss_staff_id eq 'cmoses');
					$validuser = "yes" if ($ss_staff_id eq 'emueller');
					$validuser = "yes" if ($ss_staff_id eq 'jwackwit');
					$validuser = "yes" if ($ss_staff_id eq 'ktimmons');
					$validuser = "yes" if ($ss_staff_id eq 'lshankla');
					$validuser = "yes" if ($ss_staff_id eq 'macuna');
					$validuser = "yes" if ($ss_staff_id eq 'nreynold');
					$validuser = "yes" if ($ss_staff_id eq 'sabdulla');
					$validuser = "yes" if ($ss_staff_id eq 'vdimock');
				
		
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
	$error_message = "ACCESS DENIED: You are not authorized to access the $site_label. Please contact Brian Litke at ext. 6529 for assistance accessing this resource.";
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

<h1>$site_label</h1>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>
Welcome to the $site_label. This database is used by OIC staff (Laura, Chris, Eva, Brian, Magda, Shaila) 
to set up <a href="$public_site_address">$item_label\s</a> for the SEDL Web site. 
Please enter your SEDL user ID and password to view the database.
</p>
<FORM ACTION="cpl_training_dates_manager.cgi" METHOD="POST">
<table BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP"><strong>Your user ID</strong><br>
  		  (ex: sliberty)</TD>
    <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
<TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR>
  			<SPAN class="small">(not your e-mail password)</SPAN></TD>
    <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD></TR>
</table>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Log In Now">
  </div>
  </form>
<P>
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

		## DELETE THE PAGES
		my $command_delete_item = "DELETE from $database_table_name WHERE $database_primary_field_name = '$show_record'";
		$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
		my $dbh = DBI->connect($dsn, $database_username, $database_password);
		my $sth = $dbh->prepare($command_delete_item) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#		my $num_matches = $sth->rows;
		
		$feedback_message = "You successfully deleted $item_label record \#$show_record.";
		$location = "menu";
		
		## GENERATE STATIC SESSION DATES PAGES
		&cpl_shared_functions::generate_static_sessiondates_pages($month_name_abbr, $date_full_mysql); # PASS MONTH NAME IN ORDER TO PURCGE ORPHAN FILES
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
	my $new_startdate_m = $query->param("new_startdate_m");
	my $new_startdate_d = $query->param("new_startdate_d");
	my $new_startdate_y = $query->param("new_startdate_y");

	my $new_cpltd_date_start = "$new_startdate_y\-$new_startdate_m\-$new_startdate_d";
	   $new_cpltd_date_start = "" if (($new_startdate_m eq '') || ($new_startdate_d eq '') || ($new_startdate_y eq ''));
	my $new_cpltd_cpls_id = $query->param("new_cpltd_cpls_id");
	my $new_cpltd_date_start_pretty = $query->param("new_cpltd_date_start_pretty");
	my $new_cpltd_description = $query->param("new_cpltd_description");
	my $new_cpltd_registration_form = $query->param("new_cpltd_registration_form");

	## START: CHECK FOR DATA COPLETENESS
	if ($location eq 'process_add_item') {
		if ($new_cpltd_cpls_id eq '') {
			$error_message .= "The $item_label Title is missing. Please try again.";
			$location = "add_item";
		} # END IF
		if ($new_cpltd_date_start eq '') {
			$error_message .= "The $item_label start date is missing. Please try again.";
			$location = "add_item";
		} # END IF
		if ($new_cpltd_date_start_pretty eq '') {
			$error_message .= "The $item_label training dates is missing. Please try again.";
			$location = "add_item";
		} # END IF
#		if ($new_cpltd_description eq '') {
#			$error_message .= "The $item_label description is missing. Please try again.";
#			$location = "add_item";
#		} # END IF
	} # END IF
	## END: CHECK FOR DATA COPLETENESS

if ($location eq 'process_add_item') {

	## START: BACKSLASH VARIABLES FOR DB
	#$new_news_date_effective = &commoncode::cleanthisfordb($new_news_date_effective);
	$new_cpltd_date_start = &commoncode::cleanthisfordb($new_cpltd_date_start);
	$new_cpltd_cpls_id = &commoncode::cleanthisfordb($new_cpltd_cpls_id);
	$new_cpltd_date_start_pretty = &commoncode::cleanthisfordb($new_cpltd_date_start_pretty);
#	$new_cpltd_description = &commoncode::cleanthisfordb($new_cpltd_description);
	$new_cpltd_registration_form = &commoncode::cleanthisfordb($new_cpltd_registration_form);
	## END: BACKSLASH VARIABLES FOR DB


	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select $database_primary_field_name from $database_table_name ";
		   $command .= "WHERE $database_primary_field_name = '$show_record'";
		$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
		my $dbh = DBI->connect($dsn, $database_username, $database_password);
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;
		
		$already_exists = "yes" if ($num_matches_code eq '1');

		my $add_edit_type = "added";
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
#										cpltd_description = '$new_cpltd_description',
			my $command_update_item = "UPDATE $database_table_name
										SET										
										cpltd_date_start = '$new_cpltd_date_start',
										cpltd_cpls_id = '$new_cpltd_cpls_id', 
										cpltd_date_start_pretty = '$new_cpltd_date_start_pretty', 
										cpltd_registration_form = '$new_cpltd_registration_form',
										cpltd_last_updated = '$timestamp', 
										cpltd_last_updated_by = '$cookie_ss_staff_id'
										
										WHERE $database_primary_field_name ='$show_record'";
			my $dbh = DBI->connect($dsn, $database_username, $database_password);
			my $sth = $dbh->prepare($command_update_item) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			#my $num_matches = $sth->rows;
			
			$add_edit_type = "edited";
			$feedback_message .= "The $item_label was $add_edit_type successfully and is highlighted in <a href=\"#$show_record\">YELLOW below</a>.";
			$location = "menu";
		} else {
	
			my $command_insert_item = "INSERT INTO $database_table_name VALUES ('',  '$new_cpltd_cpls_id', '$new_cpltd_date_start', '$new_cpltd_date_start_pretty', 'unused field', '$timestamp', '$cookie_ss_staff_id', '$new_cpltd_registration_form')";
			my $dbh = DBI->connect($dsn, $database_username, $database_password);
			my $sth = $dbh->prepare($command_insert_item) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			#my $num_matches = $sth->rows;

			$feedback_message .= "The $item_label was $add_edit_type successfully.";
			$location = "menu";
		} # END IF USER NAME NOT BLANK

		## GENERATE STATIC TRAINING DATES PAGES
		&cpl_shared_functions::generate_static_sessiondates_pages($month_name_abbr, $date_full_mysql); # PASS MONTH NAME IN ORDER TO PURCGE ORPHAN FILES
		
		## GENERATE STATIC SESSION PAGES
		my $text_to_print_to_screen = "";
		$text_to_print_to_screen = &cpl_shared_functions::generate_static_session_pages($date_full_mysql);
#		$feedback_message .= "<p class=\"info\">$text_to_print_to_screen</p>";

}
#################################################################################
## END: LOCATION = PROCESS_add_item
#################################################################################


#################################################################################
## START: LOCATION = add_item
#################################################################################
if ($location eq 'add_item') {
	my $page_title = "Add a New $item_label";

	my $cpltd_id = "";
	my $cpltd_cpls_id = "";
	my $cpltd_date_start = "";
	my $cpltd_date_start_pretty = "";
	my $cpltd_description = "";
	my $cpltd_last_updated = "";
	my $cpltd_last_updated_by = "";
	my $cpltd_registration_form = "";
	
	if ($show_record ne '') {
		$page_title = "Save Edits to this $item_label";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from $database_table_name WHERE $database_primary_field_name = '$show_record'";
		$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
		my $dbh = DBI->connect($dsn, $database_username, $database_password);
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_pubs = $sth->rows;
		
		while (my @arr = $sth->fetchrow) {
			($cpltd_id, $cpltd_cpls_id, $cpltd_date_start, $cpltd_date_start_pretty, $cpltd_description, $cpltd_last_updated, $cpltd_last_updated_by, $cpltd_registration_form) = @arr;
		} # END DB QUERY LOOP

		if ($num_matches_pubs == 0 ) {
			$error_message = "$num_matches_pubs Records Found<br><br>COMMAND: $command";
		}

	} # END IF
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
#		$ae_name = &commoncode::cleanaccents2html($ae_name);
#		$partner_description = &commoncode::cleanaccents2html($partner_description);
		$cpltd_last_updated = &commoncode::convert_timestamp_2pretty_w_date($cpltd_last_updated);

print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label: $page_title</TITLE>
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


<script type="text/javascript">
<!--
      function input(val){
         form2.new_news_item_footer.value = "\\"" + val + "\\"";
         return false;
      }
//-->
</script>
      
$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td valign="top">

<h1><A HREF="cpl_training_dates_manager.cgi">$site_label</A><br>
$page_title</h1>


<p>The text edit boxes work best in the Firefox browser.</p>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';


print<<EOM;      
<FORM ACTION="cpl_training_dates_manager.cgi" METHOD="POST" name="form2" id="form2">

<TABLE border="1" cellpadding="3" cellspacing="0" width="100%">
<tr><td valign="top"><strong>$item_label Title</strong></td>
	<td valign="top">
		<select name="new_cpltd_cpls_id">
		<option value="">(select a PD session from this list)</option>
EOM
	my $command = "select cpls_id, cpls_title from cpl_sessions order by cpls_title";

	$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
	my $dbh = DBI->connect($dsn, $database_username, $database_password);
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
			my ($cpls_id, $cpls_title) = @arr;
			print "<option value=\"$cpls_id\"";
			print " SELECTED" if ($cpls_id eq $cpltd_cpls_id);
			print ">$cpls_title</option>";
		} # END DB QUERY LOOP
print<<EOM;
		</select>
	</td></tr>
<tr><td valign="top"><strong>Start Date</strong> </td>
	<td valign="top">
EOM
		my ($old_y, $old_m, $old_d) = split(/\-/,$cpltd_date_start);
		&commoncode::print_month_menu("new_startdate_m", $old_m);
		&commoncode::print_day_menu("new_startdate_d", $old_d);
		&commoncode::print_year_menu("new_startdate_y", 2001, $year + 1, $old_y);

print<<EOM;
	<br>Note: This date is used only to sort the events by date online.
	</td></tr>

<tr><td valign="top"><strong>Dates of Training</strong></td>
	<td valign="top"><textarea name="new_cpltd_date_start_pretty" rows="7" cols=70>$cpltd_date_start_pretty</textarea>
	<br>
		(For use on calendar page, so keep it fairly short. Example: January 30-31, 2009)</td></tr>
<tr><td valign="top"><strong>Registration Form URL</strong></td>
	<td valign="top"><input type="text" name="new_cpltd_registration_form" id="new_cpltd_registration_form" size="50" value="$cpltd_registration_form">
	<br>
EOM
print "Click here to <a href=\"$cpltd_registration_form\" target=\"_blank\">test the link</a>.<br>" if ($cpltd_registration_form ne '');
print<<EOM;
(example: http://www.sedl.org/register/event116.html)</td></tr>
EOM
#<tr><td valign="top"><strong>Registration Details</strong></td>
#	<td valign="top"><textarea name="new_cpltd_description" rows="30" cols=70>$cpltd_description</textarea>
#	</td></tr>

if ($cpltd_last_updated_by ne '') {
print<<EOM;
<tr><td valign="top"><strong>Last Updated By</strong></td>
	<td valign="top">$cpltd_last_updated</td></tr>
<tr><td valign="top"><strong>Last Updated</strong></td>
	<td valign="top">$cpltd_last_updated_by</td></tr>
EOM
}
print<<EOM;
</table>




	<UL>
		<INPUT TYPE=HIDDEN NAME="show_record" VALUE="$show_record">
		<INPUT TYPE=HIDDEN NAME="location" VALUE="process_add_item">
	<INPUT TYPE=SUBMIT VALUE="$page_title">
	</FORM>
	</UL>
</form>
EOM
if ($show_record ne '') {
print<<EOM;
<p>
<table border="0" cellpadding="0" cellsoacing="0" align="right">
<tr><td valign="top">
<div class="first fltRt">
		<FORM ACTION="cpl_training_dates_manager.cgi" METHOD="POST">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr><td colspan="2"><em>Click here to delete this $item_label.</em></td></tr>
		<tr><td valign="top"><input type="checkbox" name="confirm" value="confirmed"></td>
			<td valign="top"><font color=red>confirm the deletion<br> of this $item_label.</font></td></tr>
		<tr><td colspan="2">
				<input type="hidden" name="location" value="process_delete_item">
				<input type="hidden" name="show_record" value="$show_record">
				<input type="submit" name="submit" value="Delete $item_label"></td></tr>
				
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
		<table align="right">
		<tr><td>
				<em>Quick Links</em><br>
				- <a href="http://www.sedl.org/staff/communications/cpl_sessions_manager.cgi">CPL Session Manager</a><br>
				- <strong>CPL Training Dates Manager</strong><br>
				- <a href="http://www.sedl.org/staff/communications/cpl_budgets.cgi">CPL Custom Session Requests</a><br>
				- <a href="/staff/communications/cpl_next_session_notification.cgi">CPL Next Session Requests</a><br>
				- <A HREF="cpl_training_dates_manager.cgi?location=logout">Logout</A>
			</td>
		</tr>
		</table>
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
	## START: LOAD PLANNED SESSIONS
	my %planned_sessions = ""; # DECLARE HASH
	## END: LOAD PLANNED SESSIONS

## PRINT SIGNUP FORM
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label: List of $item_label\s</TITLE>
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td valign="top"><h1><A HREF="cpl_training_dates_manager.cgi">$site_label</A>
		<br>List of $item_label\s</h1></td>
	<td valign="top" align="right">
		<table align="right">
		<tr><td>
				<em>Quick Links</em><br>
				- <a href="http://www.sedl.org/staff/communications/cpl_sessions_manager.cgi">CPL Session Manager</a><br>
				- <strong>CPL Training Dates Manager</strong><br>
				- <a href="http://www.sedl.org/staff/communications/cpl_budgets.cgi">CPL Custom Session Requests</a><br>
				- <a href="/staff/communications/cpl_next_session_notification.cgi">CPL Next Session Requests</a><br>
				- <A HREF="cpl_sessions_manager.cgi?location=logout">Logout</A>
			</td>
		</tr>
		</table>
	</td></tr>
</table>
EOM

	if ($validuser ne 'yes') {
		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view $item_label\s.</FONT>";
	}

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

my $command = "select cpl_sessions.cpls_category, cpl_sessions.cpls_category2, cpl_sessions.cpls_title, cpl_sessions.cpls_description_short, cpl_training_dates.*
			from cpl_sessions, cpl_training_dates
			WHERE cpl_sessions.cpls_id = cpl_training_dates.cpltd_cpls_id";

	$command .= " order by cpls_title" if $sortby eq 'title';
	$command .= " order by cpls_last_updated DESC, cpls_title" if $sortby eq 'lastupdated';
	$command .= " order by cpls_active, cpls_title" if $sortby eq 'active';
	$command .= " order by cpls_category, cpls_title" if $sortby eq 'category';
	$command .= " order by cpltd_date_start DESC, cpls_title" if $sortby eq 'date_desc';
	$command .= " order by cpltd_date_start, cpls_title" if $sortby eq 'date_asc';

 
#print "<P>$command<P>";
$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
my $dbh = DBI->connect($dsn, $database_username, $database_password);
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches_items = $sth->rows;

my $col_heading_name = "Session Title";
   $col_heading_name = "<a href=\"cpl_training_dates_manager.cgi?sortby=title\">Session Title</a>" if ($sortby ne 'title');
my $col_heading_lastupdated = "Last Updated";
   $col_heading_lastupdated = "<a href=\"cpl_training_dates_manager.cgi?sortby=lastupdated\">Last Updated</a>" if ($sortby ne 'lastupdated');
my $col_heading_active = "Active?";
   $col_heading_active = "<a href=\"cpl_training_dates_manager.cgi?sortby=active\">Active?</a>" if ($sortby ne 'active');
my $col_heading_category = "PD Category";
   $col_heading_category = "<a href=\"cpl_training_dates_manager.cgi?sortby=category\">PD Category</a>" if ($sortby ne 'category');
my $col_heading_date = "Training Date";
	if ($sortby eq 'date_desc') {
		$col_heading_date = "<a href=\"cpl_training_dates_manager.cgi?sortby=date_asc\">Training Date</a>";
	} else {
		$col_heading_date = "<a href=\"cpl_training_dates_manager.cgi?sortby=date_desc\">Training Date</a>";
	}
print<<EOM;
<P>
There are $num_matches_items $item_label\s on file that are shown on the 
SEDL <a href="$public_site_address" target="_blank">$item_label\s</a> site).
</p>

<FORM ACTION="cpl_training_dates_manager.cgi" METHOD="POST" name="form2" id="form2">
Click here to 
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="add_item">
	<INPUT TYPE=SUBMIT VALUE="Add a New $item_label">
	</FORM>
<P>
EOM


	if ($num_matches_items == 0) {
		print "<P class=\"alert\">There are no records in the database.</p>";
	}
print<<EOM;
<TABLE border="1" cellpadding="3" cellspacing="0" width="100%">
<TR style="background:#ebebeb">
	<td><strong>#</strong></td>
	<td><strong>$col_heading_date</strong></td>
	<td><strong>$col_heading_name</strong></td>
	<td><strong>$col_heading_category</strong></td>
	<td><strong>$col_heading_lastupdated</strong></td>
</TR>
EOM

my $counter = 1;
	while (my @arr = $sth->fetchrow) {
		my ($cpls_category, $cpls_category2, $cpls_title, $cpls_description_short, 
			$cpltd_id, $cpltd_cpls_id, $cpltd_date_start, $cpltd_date_start_pretty, $cpltd_description, $cpltd_last_updated, $cpltd_last_updated_by, $new_cpltd_registration_form) = @arr;

		my $bgcolor="";
#  			$bgcolor="style=\"background:#cccccc\"" if ($cpls_active eq 'no');
  			$bgcolor="style=\"background:#FFFFCC\"" if ($show_record eq $cpltd_id);
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$cpls_title = &commoncode::cleanaccents2html($cpls_title);
		$cpltd_last_updated = &commoncode::convert_timestamp_2pretty_w_date($cpltd_last_updated);

		$cpltd_date_start = &commoncode::date2standard($cpltd_date_start);
print<<EOM;
<TR $bgcolor>
	<td valign="top"><a name="$cpltd_id"></a>$counter</td>
	<td valign="top"><A HREF=\"cpl_training_dates_manager.cgi?location=add_item&amp;show_record=$cpltd_id\" TITLE="Click to edit this $item_label">$cpltd_date_start</a></td>
	<td valign="top">$cpls_title</td>
	<td valign="top">$cpls_category
EOM
print "<br>$cpls_category2" if ($cpls_category2 ne '');
print<<EOM;
	</td>
	<td valign="top"><span style="color:#999999;">$cpltd_last_updated<br>by $cpltd_last_updated_by</span></td>
</TR>
EOM
		$counter++;
	} # END DB QUERY LOOP
print<<EOM;
</TABLE>
<P>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
<P>
The $site_label is located at <A HREF="$public_site_address">$public_site_address</A>.
$htmltail

EOM
#<p>SORTBY: $sortby</p>
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



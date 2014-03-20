#!/usr/bin/perl

#####################################################################################################
# Copyright 2008 by SEDL
#
# This script is used by Communications staff to manage the R&E Projects List
# Written by Brian Litke 03-17-2008
#####################################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=corp;host=www.sedl.org";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
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
my $show_type = $query->param("show_type");
my $item_label = "R&amp;E Project";
my $site_label = "R&amp;E Projects List Manager";
my $public_site_address = "http://www.sedl.org/re/experience.html";
my $shading_color="#ebebeb";


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

########################################
## END: READ VARIABLES PASSED BY USER
########################################


#########################################
## START: SET VARIABLES FOR USE IN SCRIPT
#########################################
my @subject_types = (
	"Afterschool", 
	"Early Childhood", 
	"English Language Learners", 
	"Family and Community", 
	"Improving School Performance", 
	"Math and Science", 
	"Reading and Literacy", 
	"Technology"
	);

my @subject_urls = (
	"experience-afterschool.html", 
	"experience-earlychildhood.html", 
	"experience-english-language-learners.html", 
	"experience-familycommunity.html", 
	"experience-improvingschoolperformance.html", 
	"experience-math-science.html", 
	"experience-reading-literacy.html", 
	"experience-technology.html"
	);
#########################################
## END: SET VARIABLES FOR USE IN SCRIPT
#########################################


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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("398"); # 398 is the PID for this page in the intranet database

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
				my $num_matches = $sth->rows;

					$validuser = "yes" if ($ss_staff_id eq 'awest');
					$validuser = "yes" if ($ss_staff_id eq 'blitke');
					$validuser = "yes" if ($ss_staff_id eq 'brollins');
					$validuser = "yes" if ($ss_staff_id eq 'cmoses');
					$validuser = "yes" if ($ss_staff_id eq 'jwackwit');
					$validuser = "yes" if ($ss_staff_id eq 'lshankla');
					$validuser = "yes" if ($ss_staff_id eq 'macuna');
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
<TITLE>SEDL Intranet | $site_label</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead

<h1>$site_label</h1>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>
Welcome to the $site_label. This database is used by Communicaitons staff 
(Brian, Debbie, Shaila) to set up <a href="$public_site_address">$item_label</a> 
for the <a href="/re/">Research and Evaluation</a> Web site. 
Please enter your SEDL user ID and password to view the database.
</p>
<FORM ACTION="re_projects_manager.cgi" METHOD=POST>
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
## START: LOCATION PROCESS_DELETE_item
##########################################################
if ($location eq 'process_delete_item') {
	my $confirm = $query->param("confirm");
	if ($confirm eq 'confirmed') {
	## START: BACKSLASH VARIABLES FOR DB
	$show_record = &commoncode::cleanthisfordb($show_record);
	## END: BACKSLASH VARIABLES FOR DB

		## DELETE THE PAGES
		my $command_delete_pub = "DELETE from re_projects WHERE unique_id = '$show_record'";
		$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command_delete_pub) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;

		$feedback_message = "You successfully deleted $item_label record \#$show_record.";
		$location = "menu";
	} else {
		$error_message = "ERROR: $item_label Deletion Aborted. You forgot to checkmark the CONFIRM box.";
		$location = "add_record";
	}
}
##########################################################
## END: LOCATION PROCESS_DELETE_item
##########################################################


#################################################################################
## START: LOCATION = PROCESS_ADD_record
#################################################################################
	my $new_proj_subject = $query->param("new_proj_subject");
	my $new_proj_name = $query->param("new_proj_name");
	my $new_proj_description = $query->param("new_proj_description");
	my $new_proj_client = $query->param("new_proj_client");
	my $new_proj_currentpast = $query->param("new_proj_currentpast");
	my $new_proj_list_order = $query->param("new_proj_list_order");
	
	my $new_startdate_m = $query->param("new_startdate_m");
	my $new_startdate_d = $query->param("new_startdate_d");
	my $new_startdate_y = $query->param("new_startdate_y");

	my $new_enddate_m = $query->param("new_enddate_m");
	my $new_enddate_d = $query->param("new_enddate_d");
	my $new_enddate_y = $query->param("new_enddate_y");

	my $new_date_effective = "$new_startdate_y\-$new_startdate_m\-$new_startdate_d";
	my $new_date_expires = "$new_enddate_y\-$new_enddate_m\-$new_enddate_d";

if ($location eq 'process_add_record') {
	## START: CHECK FOR DATA COPLETENESS
	if ($new_proj_name eq '') {
		$error_message .= "The $item_label name is missing. Please try again.";
		$location = "add_record";
	}
	## END: CHECK FOR DATA COPLETENESS
}

if ($location eq 'process_add_record') {

	## START: BACKSLASH VARIABLES FOR DB
	$new_date_effective = &commoncode::cleanthisfordb($new_date_effective);
	$new_date_expires = &commoncode::cleanthisfordb($new_date_expires);

	$new_proj_subject = &commoncode::cleanthisfordb($new_proj_subject);
	$new_proj_name = &commoncode::cleanthisfordb($new_proj_name);
	$new_proj_description = &commoncode::cleanthisfordb($new_proj_description);
	$new_proj_client = &commoncode::cleanthisfordb($new_proj_client);
	$new_proj_currentpast = &commoncode::cleanthisfordb($new_proj_currentpast);
	$new_proj_list_order = &commoncode::cleanthisfordb($new_proj_list_order);
	## END: BACKSLASH VARIABLES FOR DB


	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select * from re_projects WHERE unique_id = '$show_record'";
#		print header;
#		print "<p>CHECK FOR EXISTS:<br>$command<br><p>";

		$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;

		$already_exists = "yes" if ($num_matches_code eq '1');
#		print header;
#		print "<p>CHECK FOR EXISTS:<br>$command<br>MATCHES: $num_matches_code<p>";

#		while (my @arr = $sth->fetchrow) {
#			my ($this_recordid, $this_access_code) = @arr;
#			$show_record = $this_recordid if ($show_record ne '');
#		} # END DB QUERY LOOP
my $add_edit_type = "added";
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update_item = "UPDATE re_projects SET proj_subject = '$new_proj_subject', proj_name = '$new_proj_name', proj_description = '$new_proj_description', proj_client = '$new_proj_client', proj_currentpast = '$new_proj_currentpast', proj_list_order = '$new_proj_list_order', proj_lastedited_by = '$cookie_ss_staff_id', proj_lastedited_date = '$timestamp' WHERE unique_id ='$show_record'";

			$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_update_item) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			$add_edit_type = "edited";
			$feedback_message .= "The $item_label was $add_edit_type successfully and is highlighted in <a href=\"#$show_record\">YELLOW below</a>.";
			$location = "menu";
			&update_project_list_include();
		} else {
	
			my $command_insert_item = "INSERT INTO re_projects VALUES ('', '$new_date_effective', '$new_date_expires', '$new_proj_subject', '$new_proj_name', '$new_proj_description', '$new_proj_client', '$new_proj_currentpast', '$new_proj_list_order', '$cookie_ss_staff_id', '$timestamp')";

			$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_insert_item) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;


			$feedback_message .= "The $item_label was $add_edit_type successfully.";
			$location = "menu";
			&update_project_list_include();
		} # END IF USER NAME NOT BLANK

}
#################################################################################
## END: LOCATION = PROCESS_ADD_record
#################################################################################


#################################################################################
## START: LOCATION = ADD_record
#################################################################################
if ($location eq 'add_record') {
	my $page_title = "Add a New $item_label";

	my $unique_id = "";
	my $date_effective = "";
	my $date_expires = "";
	my $proj_subject = "";
	my $proj_name = "";
	my $proj_description = "";
	my $proj_client = "";
	my $proj_currentpast = "";
	my $proj_list_order = "";

	if ($show_record ne '') {
		$page_title = "Save Edits to this $item_label";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from re_projects WHERE unique_id = '$show_record'";

		$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		my $num_matches_pubs = $sth->rows;
		while (my @arr = $sth->fetchrow) {
		   ($unique_id, $date_effective, $date_expires, $proj_subject, $proj_name, $proj_description, $proj_client, $proj_currentpast, $proj_list_order) = @arr;
		} # END DB QUERY LOOP
	}
	
	## TRANSLATE CURLY QUOTES TO HTML ENTITIES
	$proj_name = &commoncode::cleanaccents2html($proj_name);
	$proj_description = &commoncode::cleanaccents2html($proj_description);
	$date_effective = $new_date_effective if ($date_effective eq '');
	$date_expires = $new_date_expires if ($date_expires eq '');
	$proj_subject = $new_proj_subject if ($proj_subject eq '');
	$proj_name = $new_proj_name if ($proj_name eq '');
	$proj_description = $new_proj_description if ($proj_description eq '');
	$proj_client = $new_proj_client if ($proj_client eq '');
	$proj_currentpast = $new_proj_currentpast if ($proj_currentpast eq '');
	$proj_list_order = $new_proj_list_order if ($proj_list_order eq '');


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
	content_css: "/css/sedl2007_forTinyMCE.css",
	convert_urls : false
});
</script>

      
$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td>

<h1><A HREF="re_projects_manager.cgi">$site_label</A><br>
$page_title</h1>


<p>The text edit boxes work best in the Firefox browser.</p>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';


#<tr><td valign="top"><strong>Client</strong></td>
#	<td valign="top"><textarea name="new_proj_client" rows="8" cols="70">$proj_client</textarea>
#	</td></tr>

print<<EOM;      
<FORM ACTION="re_projects_manager.cgi" METHOD="POST" name="form2" id="form2">

<TABLE border="1" cellpadding="2" cellspacing="0" width="100%">
<tr><td valign="top"><strong>Name for $item_label</strong></td>
	<td valign="top"><textarea name="new_proj_name" rows="8" cols="70">$proj_name</textarea>
	</td></tr>
<tr><td valign="top"><strong>Subject Area</strong></td>
	<td valign="top">
	<select name="new_proj_subject">
	<option value="">(select one)</option>
EOM
my $counter_subjects = 0;
	while ($counter_subjects <= $#subject_types) {
		print "<option value=\"$subject_types[$counter_subjects]\"";
		print " SELECTED" if (($subject_types[$counter_subjects] eq $proj_subject) ||
								($subject_types[$counter_subjects] eq $new_proj_subject)
								);
		print ">$subject_types[$counter_subjects]</option>";
		$counter_subjects++;
	}
print<<EOM;
	</select>
	</td></tr>

<tr><td valign="top"><strong>Current/Past</strong></td>
	<td valign="top">
	<select name="new_proj_currentpast">
	<option value="">(select one)</option>
EOM
my @project_types = (
	"Current", 
	"Past"
	);
my $counter_projtype = 0;
	while ($counter_projtype <= $#project_types) {
		print "<option value=\"$project_types[$counter_projtype]\"";
		print " SELECTED" if (($project_types[$counter_projtype] eq $proj_currentpast) ||
								($project_types[$counter_projtype] eq $new_proj_currentpast) 
								);
		print ">$project_types[$counter_projtype]</option>";
		$counter_projtype++;
	}
print<<EOM;
	</select>
	</td></tr>

<tr><td valign="top"><strong>List ordering sequence</strong></td>
	<td valign="top">
EOM
&commoncode::show_form_number_list("new_proj_list_order", $proj_list_order,0,5);
print<<EOM;
	</td></tr>

<tr><td valign="top"><strong>Description for $item_label</strong></td>
	<td valign="top"><textarea name="new_proj_description" rows=17 cols=70>$proj_description</textarea>
	</td></tr>
EOM

#print<<EOM;
#<tr><td valign="top"><strong>Effective Date</strong> (required)</td>
#	<td valign="top">
#EOM
#		my ($old_y, $old_m, $old_d) = split(/\-/,$date_effective);
#		&commoncode::print_month_menu("new_startdate_m", $old_m);
#		&commoncode::print_day_menu("new_startdate_d", $old_d);
#		&commoncode::print_year_menu("new_startdate_y", 2001, $year + 1, $old_y);
#
#print<<EOM;
#	</td></tr>
#<tr><td valign="top"><strong>Retirement Date</strong> (optional)</td>
#	<td valign="top">
#EOM
#		my ($old_y, $old_m, $old_d) = split(/\-/,$date_expires);
#		&commoncode::print_month_menu("new_enddate_m", $old_m);
#		&commoncode::print_day_menu("new_enddate_d", $old_d);
#		&commoncode::print_year_menu("new_enddate_y", 2001, $year + 1, $old_y);
#
#print<<EOM;
#	<br>(The project will show on the R&amp; Project List forever unless a retirement date is set here.)
#	</td></tr>
#EOM

print<<EOM;
</table>




	<UL>
		<INPUT TYPE=HIDDEN NAME=show_record VALUE="$show_record">
		<INPUT TYPE=HIDDEN NAME=location VALUE="process_add_record">
	<INPUT TYPE=SUBMIT VALUE="$page_title">
	</FORM>
	</UL>
</form>
<div class="first fltRt">
		<FORM ACTION="re_projects_manager.cgi" METHOD=POST>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr><td colspan="2"><em>Click here to delete this $item_label.</em></td></tr>
		<tr><td valign="top"><input type="checkbox" name="confirm" value="confirmed"></td>
			<td valign="top"><font color=red>confirm the deletion<br> of this $item_label.</font></td></tr>
		<tr><td colspan="2">
				<input type="hidden" name="location" value="process_delete_item">
				<input type="hidden" name="show_type" value="$show_type">
				<input type="hidden" name="show_record" value="$show_record">
				<input type="submit" name="submit" value="Delete $item_label"></td></tr>
				
		</table>
		</form>
	
</div>


</td>
	<td valign="top" align="right">
		(Click here to <A HREF="re_projects_manager.cgi?location=logout">logout</A>)
		<P>
	</td></tr>
</table>


$htmltail
EOM
}
#################################################################################
## END: LOCATION = ADD_record
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
<TITLE>SEDL Intranet | $site_label: List of $item_label</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1><A HREF="re_projects_manager.cgi">$site_label</A>
		<br>List of $item_label</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="re_projects_manager.cgi?location=logout">logout</A>)
	</td></tr>
</table>
EOM

	if ($validuser ne 'yes') {
		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view $item_label.</FONT>";
	}

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

my $command = "select * from re_projects";

	$command .= " order by proj_subject, proj_currentpast, proj_list_order, proj_name" if (($sortby eq '') || ($sortby eq 'proj_subject'));
	$command .= " order by proj_name" if ($sortby eq 'proj_name');
	$command .= " order by proj_currentpast, proj_subject, proj_name" if ($sortby eq 'proj_currentpast');



#print "<P>$command<P>";
		$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
my $num_matches_records = $sth->rows;


print<<EOM;
<P>
There are $num_matches_records $item_label on file (items in grey are past their retirement and not shown on the <a href="$public_site_address" target="_blank">$item_label</a> site).
<p>
<FORM ACTION="re_projects_manager.cgi" METHOD="POST" name="form2" id="form2">
Click here to 
		<input type="hidden" name="show_type" value="$show_type">
		<INPUT TYPE=HIDDEN NAME=new_ VALUE="$show_record">
		<INPUT TYPE=HIDDEN NAME=location VALUE="add_record">
	<INPUT TYPE=SUBMIT VALUE="Add a New $item_label">
	</FORM>
EOM

if ($num_matches_records > 0) {
print<<EOM;
<TABLE border="1" cellpadding="3" cellspacing="0" width="100%">
<TR bgcolor="$shading_color">
	<td><strong><a href="re_projects_manager.cgi?sortby=proj_subject">Subject</a></strong></td>
	<td><strong><a href="re_projects_manager.cgi?sortby=proj_currentpast">Current<br>or Past</a></strong></td>
	<td><strong><a href="re_projects_manager.cgi?sortby=proj_name">Title</a></strong> (click a title to edit the $item_label)</td>
	<td><strong>List<br>Order</strong></td>
	<td><strong>Last<br>Edited<br>By</strong></td>
	<td><strong>Date Last<br>Edited</strong></td>
</TR>
EOM
}


my $counter = 1;
my $printed_srories_header = "";
my $last_subject = "";
	while (my @arr = $sth->fetchrow) {
		my ($unique_id, $date_effective, $date_expires, $proj_subject, $proj_name, $proj_description, $proj_client, $proj_currentpast, $proj_list_order, $proj_lastedited_by, $proj_lastedited_date) = @arr;

		my $bgcolor="";
   			$bgcolor="BGCOLOR=\"#CCCCCC\"" if (($date_expires !~ '0000') && ($date_expires lt $date_full_mysql));
  			$bgcolor="BGCOLOR=\"#FFFFCC\"" if ($show_record eq $unique_id);


		# TRANSFORM DATES INTO PRETTY FORMAT
		$date_effective = &commoncode::date2standard($date_effective);
		$date_expires = &commoncode::date2standard($date_expires);
		$date_expires = "N/A" if ($date_expires =~ '0000');

		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$proj_name = &commoncode::cleanaccents2html($proj_name);
		$proj_lastedited_date = &commoncode::convert_timestamp_2pretty_w_date($proj_lastedited_date, "yes");

	if (($counter != 1) && ($last_subject ne $proj_subject)) {
		print "<tr><td colspan=\"6\" bgcolor=\"#cccccc\"><IMG src=\"/images/spacer.gif\" height=\"1\" width=\"1\" alt=\"\"></td></tr>";
	}
print<<EOM;
<TR $bgcolor>
	<td valign="top">$proj_subject</td>
	<td valign="top">$proj_currentpast</td>
	<td valign="top"><A HREF=\"re_projects_manager.cgi?location=add_record&show_record=$unique_id\" TITLE="Click to edit this record"><strong>$proj_name</strong></a><br><span style="color:#999999;">$proj_client</span></td>
	<td valign="top">$proj_list_order</td>
	<td valign="top">$proj_lastedited_by</td>
	<td valign="top">$proj_lastedited_date</td>
</TR>
EOM

		$last_subject = $proj_subject;
		$counter++;
	} # END DB QUERY LOOP
if ($num_matches_records > 0) {
	print "</table>\n";
}
print<<EOM;
<p>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
EOM
	if ($show_type ne '') {
print<<EOM;
<p>
The $show_type web page is located at <A HREF="$public_site_address">$public_site_address</A>.
</p>
EOM
	}
print "$htmltail";
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


######################################################
## START: subroutine update_project_list_include
######################################################
sub update_project_list_include {
	open (RE_PROJECT_LIST_HOME,">/home/httpd/html/re/includes/projlist_for_home.txt");
	open (RE_PROJECT_LIST_EXPERIENCE,">/home/httpd/html/re/includes/projlist_for_experience.txt");

	my $counter_subjects = 0;
	my %matches_for_category;
	while ($counter_subjects<= $#subject_types) {
		my $this_subject = &commoncode::cleanthisfordb($subject_types[$counter_subjects]);
		## QUERY DATABASE
		my $command = "select * from re_projects where proj_subject LIKE '$this_subject' order by proj_currentpast, proj_list_order, proj_name";

		$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_this_subject = $sth->rows;
		$matches_for_category{$subject_types[$counter_subjects]} = $num_matches_this_subject;
		$counter_subjects++;
	} # END WHILE LOOP

	my $counter_subjects = 0;
	while ($counter_subjects<= $#subject_types) {

		##################################################
		## START: CHECK TO ENSURE THIS PROJECT IS IN USE
		##################################################
		my $this_subject = &commoncode::cleanthisfordb($subject_types[$counter_subjects]);
		
		## QUERY DATABASE
		my $command = "select * from re_projects where proj_subject LIKE '$this_subject' order by proj_currentpast, proj_list_order, proj_name";

		$dsn = "DBI:mysql:database=corp;host=www.sedl.org";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_this_subject = $sth->rows;
		my $list_of_current_project_descriptions = "";
		my $list_of_past_project_descriptions = "";
	
			while (my @arr = $sth->fetchrow) {
				my ($unique_id, $date_effective, $date_expires, $proj_subject, $proj_name, $proj_description, $proj_client, $proj_currentpast, $proj_list_order, $proj_lastedited_by, $proj_lastedited_date) = @arr;
	
				if ($proj_currentpast =~ 'urrent') {
					$list_of_current_project_descriptions .= "<strong>$proj_name\:</strong> $proj_description<p></p>\n";
				} else {
					$list_of_past_project_descriptions .= "<strong>$proj_name\:</strong> $proj_description<p></p>\n";
				}
			} # END DB QUERY LOOP
		##################################################
		## END: CHECK TO ENSURE THIS PROJECT IS IN USE
		##################################################
		

		#####################################################
		## START: IS SUBJECT IN USE, SAVE THE PAGE
		#####################################################
		if ($num_matches_this_subject > 0) {
			## OPEN THE FILE FOR THIS SUBJECT'S INCLUDE
			my $projname_short = lc(substr($subject_types[$counter_subjects],0,4));
			open (RE_PROJECT_LIST,">/home/httpd/html/re/includes/$projname_short.txt");

			## START: MAKE SERVER-SIDE-INCUD FOR USE ON HOME PAGE AND EXPERIENCE PAGE
			my $this_url = $subject_urls[$counter_subjects];
			print RE_PROJECT_LIST_HOME "&#8226; <a href=\"/re/$this_url\">$subject_types[$counter_subjects]</a><br>";
			print RE_PROJECT_LIST_EXPERIENCE "<li><a href=\"/re/$this_url\">$subject_types[$counter_subjects]</a></li>";
			## END: MAKE SERVER-SIDE-INCUD FOR USE ON HOME PAGE AND EXPERIENCE PAGE

		#####################################################
		## START: PRINT LEFT-SIDE PAGE TEMPLATE
		#####################################################
print RE_PROJECT_LIST<<EOM;
	<div id="nav">
	<ul class="level1">
		<li class="submenu"><a href="/re/" title="RE Home">Home</a></li>
     	<li class="submenu"><a href="/re/experience.html">Significant Work</a></li>
EOM
			#####################################################
			## START: PRINT LIST IF SUBJECTS IN SIDE NAVIGATION
			#####################################################
			my $counter_sidenav = 0;
			while ($counter_sidenav<= $#subject_types) {
				my $style_bullethighlight = "";
				   $style_bullethighlight = "active" if ($subject_types[$counter_sidenav] eq $subject_types[$counter_subjects]);
				my $style_bottom_border = " style=\"border-bottom-style: none\"";
				   $style_bottom_border = "" if ($counter_sidenav == $#subject_types); # SHOW DOTTED BORDER ON BOTTOM ITEM IN LIST
					
					if ($matches_for_category{$subject_types[$counter_sidenav]} != 0) {
print RE_PROJECT_LIST<<EOM;
      	<li class="submenubullet $style_bullethighlight" $style_bottom_border><a href="$subject_urls[$counter_sidenav]">$subject_types[$counter_sidenav]</a></li>
EOM
					}
				$counter_sidenav++;
			} # END WHILE LOOP
			#####################################################
			## END: PRINT LIST IF SUBJECTS IN SIDE NAVIGATION
			#####################################################
print RE_PROJECT_LIST<<EOM;
    	<li class="submenu"><a href="/re/testimonials.html">Testimonials</a></li>
    	<li class="submenu"><a href="/re/about_us.html">About Us</a></li>
		<li class="submenu"><a href="/re/contact.html" title="Contact Research and Evaluation">Contact Us</a></li>
	</ul>
	</div>
 

				<div style="min-height:296px;overflow:visible;background-image: url(/common/images/corp/sidebar_left_gradient.jpg); background-repeat: repeat-x;">
				</div>
			</div><!-- end div "sidebar_left" -->
			<!-- ************************************** -->
			<!-- end div "sidebar_left" -->
			<!-- ************************************** -->


			<!-- ************************************** -->
			<!-- start div "mainContent" -->
			<!-- ************************************** -->
			<div id="mainContent" role="main">

<div id="mainContentPadding">
	<p id="breadcrumbs" role="navigation">
		<a href="/" title="SEDL Home" class="crumb">Home</a> | <a href="/re/" class="crumb">Research and Evaluation Services</a> | Significant Work: $subject_types[$counter_subjects]
	</p>



<h1>Significant Work: $subject_types[$counter_subjects]</h1>
EOM
		#####################################################
		## END: PRINT LEFT-SIDE PAGE TEMPLATE
		#####################################################

		
			if ($list_of_current_project_descriptions ne '') {
print RE_PROJECT_LIST<<EOM;
<h2>Current Work:</h2>
$list_of_current_project_descriptions
EOM
			}
			if ($list_of_past_project_descriptions ne '') {
print RE_PROJECT_LIST<<EOM;
<h2>Past Work:</h2>
$list_of_past_project_descriptions
EOM
			}
			close(RE_PROJECT_LIST);
		}
		#####################################################
		## END: IS SUBJECT IN USE, SAVE THE PAGE
		#####################################################

		$counter_subjects++;
	} # END WHILE LOOP

	close(RE_PROJECT_LIST_HOME);
	close(RE_PROJECT_LIST_EXPERIENCE);

}
######################################################
## END: subroutine update_project_list_include
######################################################



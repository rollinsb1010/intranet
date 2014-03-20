#!/usr/bin/perl

#####################################################################################################
# Copyright 2007 by Southwest Educational Development Laboratory
#
# This script is used by Afterschool to manage the online database of lesson plans
# Written by Brian Litke 05-09-2007
#####################################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=afterschool;host=localhost";
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;
use Number::Format; # ROUNDS NUMBERS TO SPECIFIC DECIMAL PLACE
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


##########################################
# START: CONFIGURATION VARIABLES
##########################################
my $item_label = "Lesson Plan";
my $site_label = "Afterschool Lesson Plan Database";
my $public_site_address = "http://www.sedl.org/afterschool/lessonplans/";
my $mysql_db_table_name = "lesson_plans";
my $script_name = "lessonplan_manager.cgi";
##########################################
# END: CONFIGURATION VARIABLES
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
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;
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

my $validuser = "no";

my $uniqueid = param('uniqueid');

my $location = param('location');
   $location = "menu" if $location eq '';

my $showsession = param('showsession');


my $error_message = "";
my $feedback_message = "";
my $show_record = $query->param("show_record");
my $show_subject = $query->param("show_subject");
my $searchfor = $query->param("searchfor");
my $sortby = $query->param("sortby");
   $sortby = "dateadded" if ($sortby eq '');
my @topics = ("Family & Community", "Homework & Tutoring", "Project-Based Activities");
########################################
## END: READ VARIABLES PASSED BY USER
########################################

###########################################
# START: GRAB SEDL HEADER AND FOOTER HTML #
###########################################
my $htmlhead = "";
my $htmltail = "";

open(HTMLHEAD,"</home/httpd/html/staff/includes/header2012.txt");
while (<HTMLHEAD>) {
	$htmlhead .= $_;
}
close(HTMLHEAD);

open(HTMLTAIL,"</home/httpd/html/staff/includes/footer2012.txt");
while (<HTMLTAIL>) {
	$htmltail .= $_;
}
close(HTMLTAIL);

$htmlhead .= "<TABLE CELLPADDING=\"15\" width=\"100%\"><TR><TD>";
$htmltail = "</td></tr></table>$htmltail";
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
my $validuser = "no";
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
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
#my $num_matches = $sth->rows;

				$validuser = "yes" if ($ss_staff_id eq 'blitke');
				$validuser = "yes" if ($ss_staff_id eq 'cjordan');
				$validuser = "yes" if ($ss_staff_id eq 'ddonnel');
				$validuser = "yes" if ($ss_staff_id eq 'jparker');
				$validuser = "yes" if ($ss_staff_id eq 'lshankla');
				$validuser = "yes" if ($ss_staff_id eq 'mheath');
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
#	$error_message = "<FONT COLOR=ORANGE>ACCESS DENIED: You are not authorized to access the $site_label Manager. Please contact Brian Litke at ext. 260 for assistance accessing this resource.</FONT>";
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
<TITLE>SEDL Intranet | $site_label Manager</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead

<h1>$site_label Manager</h1>

EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>
Welcome to the $site_label Manager. This database is used by Afterschool staff (Cathy, Deborah, Joe, Laura, Marilyn, Nancy, Brian, Shaila) 
to add/edit the <a href="$public_site_address">$site_label</a> for the SEDL Web site. 
Please enter your SEDL user ID and password to view the database.
</p>
<FORM ACTION="$script_name" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP"><strong>Your user ID</strong><br>
	  (ex: sliberty)</TD>
    <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
<TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR>
			<SPAN class="small">(not your e-mail password)</SPAN></TD>
    <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD></TR>
</TABLE>

  <div style="margin-left:25px;">
  <INPUT TYPE=HIDDEN NAME=location VALUE=process_logon>
  <INPUT TYPE=SUBMIT VALUE="Log In Now">
  </div>
  </FORM>
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
## START: LOCATION PROCESS_DELETE_lp
##########################################################
if ($location eq 'process_delete_lp') {
	my $confirm = $query->param("confirm");
	if ($confirm eq 'confirmed') {
	## START: BACKSLASH VARIABLES FOR DB
	$show_record = &cleanformysql($show_record);
	## END: BACKSLASH VARIABLES FOR DB

		## DELETE THE PAGES
		my $command_delete_pub = "DELETE from $mysql_db_table_name WHERE lp_unique_id = '$show_record'";
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command_delete_pub) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#my $num_matches = $sth->rows;
		$feedback_message = "You successfully deleted $item_label record \#$show_record.";
		$location = "menu";
	} else {
		$error_message = "ERROR: $item_label Deletion Aborted. You forgot to checkmark the CONFIRM box.";
		$location = "add_item";
	}
}
##########################################################
## END: LOCATION PROCESS_DELETE_lp
##########################################################



#################################################################################
## START: LOCATION = PROCESS_add_item
#################################################################################
	## START: READ NEW VALUES FOR LP RECORD
	my $new_lp_subject = $query->param("new_lp_subject");

$show_subject = $new_lp_subject if ($new_lp_subject ne '');

	my $new_lp_teaching_tips = $query->param("new_lp_teaching_tips");
	my $new_lp_series_name = $query->param("new_lp_series_name");
	my $new_lp_series_num = $query->param("new_lp_series_num");
	my $new_lp_title = $query->param("new_lp_title");
	my $new_lp_note = $query->param("new_lp_note");
	my $new_lp_desc = $query->param("new_lp_desc");
	my $new_lp_grade_start = $query->param("new_lp_grade_start");
	my $new_lp_grade_end = $query->param("new_lp_grade_end");
	my $new_lp_duration = $query->param("new_lp_duration");
	my $new_lp_learning_goals = $query->param("new_lp_learning_goals");
	my $new_lp_map2standards = $query->param("new_lp_map2standards");
	my $new_lp_materials = $query->param("new_lp_materials");
	my $new_lp_prep = $query->param("new_lp_prep");
	my $new_lp_safety = $query->param("new_lp_safety");
	my $new_lp_whattodo = $query->param("new_lp_whattodo");
	my $new_lp_evaluate = $query->param("new_lp_evaluate");
	my $new_lp_learnmore = $query->param("new_lp_learnmore");
	my $new_lp_contributor = $query->param("new_lp_contributor");
	my $new_lp_show_on_site = $query->param("new_lp_show_on_site");
	my $new_lp_toolkit_link = $query->param("new_lp_toolkit_link");
	my $new_lp_toolkit_pagetitle = $query->param("new_lp_toolkit_pagetitle");
	my $new_lp_attachments = $query->param("new_lp_attachments");
	my $new_lp_extension_activities = $query->param("new_lp_extension_activities");
#	my $new_lp_lesson_topic = $query->param("new_lp_lesson_topic");
	my $new_lp_lesson_topic;
		# COMPUTE TOPIC VALUE
		my $topic_counter = 0;
		while ($topic_counter <= $#topics) {
			$new_lp_lesson_topic .= $query->param("new_lp_topic_$topic_counter");
			$new_lp_lesson_topic .= " ";
			$topic_counter++;
		} # END WHILE LOOP
	my $new_lp_video = $query->param("new_lp_video");


	my $new_startdate_m = $query->param("new_startdate_m");
	my $new_startdate_d = $query->param("new_startdate_d");
	my $new_startdate_y = $query->param("new_startdate_y");

	my $new_lp_grade_start_label = $new_lp_grade_start;
	my $new_lp_grade_end_label = $new_lp_grade_end;
	$new_lp_grade_start_label = "K" if ($new_lp_grade_start == 0);
	$new_lp_grade_start_label = "preK" if ($new_lp_grade_start == -1);
	if ( (length($new_lp_grade_start_label) == 2) && (substr($new_lp_grade_start_label,0,1) eq '0') ) {
		$new_lp_grade_start_label = substr($new_lp_grade_start_label,1,1);
	}
	if ( (length($new_lp_grade_end_label) == 2) && (substr($new_lp_grade_end_label,0,1) eq '0') ) {
		$new_lp_grade_end_label = substr($new_lp_grade_end_label,1,1);
	}
	$new_lp_grade_end_label = "K" if ($new_lp_grade_end == 0);
	$new_lp_grade_end_label = "preK" if ($new_lp_grade_end == -1);
	my $new_lp_gradespan = "$new_lp_grade_start_label to $new_lp_grade_end_label";

	my $new_lp_date_added = "$new_startdate_y\-$new_startdate_m\-$new_startdate_d"; # CONCATENATED
	## END: READ NEW VALUES FOR LP RECORD


if ($location eq 'process_add_item') {
	## START: CHECK FOR DATA COPLETENESS
	if (($new_lp_title eq '') || ($new_lp_desc eq '')) {
		$error_message .= "The $item_label title and/or content are missing. Please try again.";
		$location = "add_item";
	}
	if ( ($show_record ne '') && (($new_startdate_m eq '00') || ($new_startdate_d eq '00') || ($new_startdate_y eq '0000') || ($new_startdate_m eq '') || ($new_startdate_d eq '') || ($new_startdate_y eq '')) ) {
		$error_message .= "The effective date is malformed. Please update the record before quitting.";
	}
	## START: CHECK FOR DATA COPLETENESS
}

if ($location eq 'process_add_item') {

	## START: BACKSLASH VARIABLES FOR DB
	$new_lp_subject = &cleanformysql($new_lp_subject);
	$new_lp_teaching_tips = &cleanformysql($new_lp_teaching_tips);
	$new_lp_series_name = &cleanformysql($new_lp_series_name);
	$new_lp_series_num = &cleanformysql($new_lp_series_num);
	$new_lp_title = &cleanformysql($new_lp_title);
	$new_lp_note = &cleanformysql($new_lp_note);
	$new_lp_desc = &cleanformysql($new_lp_desc);
	$new_lp_gradespan = &cleanformysql($new_lp_gradespan);
	$new_lp_duration = &cleanformysql($new_lp_duration);
	$new_lp_learning_goals = &cleanformysql($new_lp_learning_goals);
	$new_lp_map2standards = &cleanformysql($new_lp_map2standards);
	$new_lp_materials = &cleanformysql($new_lp_materials);
	$new_lp_prep = &cleanformysql($new_lp_prep);
	$new_lp_safety = &cleanformysql($new_lp_safety);
	$new_lp_whattodo = &cleanformysql($new_lp_whattodo);
	$new_lp_evaluate = &cleanformysql($new_lp_evaluate);
	$new_lp_learnmore = &cleanformysql($new_lp_learnmore);
	$new_lp_contributor = &cleanformysql($new_lp_contributor);
	$new_lp_date_added = &cleanformysql($new_lp_date_added);
	$new_lp_show_on_site = &cleanformysql($new_lp_show_on_site);
	$new_lp_toolkit_link = &cleanformysql($new_lp_toolkit_link);
	$new_lp_toolkit_pagetitle = &cleanformysql($new_lp_toolkit_pagetitle);
	$new_lp_attachments = &cleanformysql($new_lp_attachments);
	$new_lp_extension_activities = &cleanformysql($new_lp_extension_activities);
	$new_lp_lesson_topic = &cleanformysql($new_lp_lesson_topic);
	$new_lp_video = &cleanformysql($new_lp_video);
	## END: BACKSLASH VARIABLES FOR DB


	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select lp_unique_id from $mysql_db_table_name ";
			if ($show_record ne '') {
				$command .= "WHERE lp_unique_id = '$show_record'";
			}
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;
		$already_exists = "yes" if (($num_matches_code eq '1') && ($show_record ne ''));
		
		my $add_edit_type = "added"; # DEFAULT SETTING
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update_lp = "UPDATE $mysql_db_table_name SET 
lp_subject = '$new_lp_subject', 
lp_teaching_tips = '$new_lp_teaching_tips', 
lp_series_name = '$new_lp_series_name', 
lp_series_num = '$new_lp_series_num', 
lp_title = '$new_lp_title', 
lp_note = '$new_lp_note', 
lp_desc = '$new_lp_desc', 
lp_gradespan = '$new_lp_gradespan', 
lp_grade_start = '$new_lp_grade_start', 
lp_grade_end = '$new_lp_grade_end', 
lp_duration = '$new_lp_duration', 
lp_learning_goals = '$new_lp_learning_goals', 
lp_map2standards = '$new_lp_map2standards', 
lp_materials = '$new_lp_materials', 
lp_prep = '$new_lp_prep', 
lp_safety = '$new_lp_safety', 
lp_whattodo = '$new_lp_whattodo', 
lp_evaluate = '$new_lp_evaluate', 
lp_learnmore = '$new_lp_learnmore', 
lp_contributor = '$new_lp_contributor', 
lp_date_added = '$new_lp_date_added', 
lp_show_on_site = '$new_lp_show_on_site', 
lp_toolkit_link = '$new_lp_toolkit_link', 
lp_toolkit_pagetitle = '$new_lp_toolkit_pagetitle', 
lp_attachments = '$new_lp_attachments', 
lp_edited_by = '$cookie_ss_staff_id',
lp_date_edited = '$date_full_mysql',
lp_extension_activities = '$new_lp_extension_activities',
lp_lesson_topic = '$new_lp_lesson_topic',
lp_video = '$new_lp_video'
WHERE lp_unique_id ='$show_record'";

			my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
			my $sth = $dbh->prepare($command_update_lp) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			$add_edit_type = "edited";
			$feedback_message .= "The $item_label was $add_edit_type successfully";
			$feedback_message .= " and is highlighted in <a href=\"#$show_record\">YELLOW below</a>." if ($add_edit_type eq 'edited');
#			$error_message .= "</font>";
			$location = "menu";
		} else {
	
			my $command_insert_lp = "INSERT INTO $mysql_db_table_name VALUES ('', '$new_lp_subject', '$new_lp_teaching_tips', '$new_lp_series_name', '$new_lp_series_num', '$new_lp_title', '$new_lp_note', '$new_lp_desc', '$new_lp_gradespan', '$new_lp_grade_start', '$new_lp_grade_end', '$new_lp_duration', '$new_lp_learning_goals', '$new_lp_map2standards', '$new_lp_materials', '$new_lp_prep', '$new_lp_safety', '$new_lp_whattodo', '$new_lp_evaluate', '$new_lp_learnmore', '$new_lp_contributor', '$date_full_mysql', '$new_lp_show_on_site', '$new_lp_toolkit_link', '$new_lp_toolkit_pagetitle', '$new_lp_attachments', '$cookie_ss_staff_id', '$cookie_ss_staff_id', '$date_full_mysql', '$new_lp_extension_activities', '$new_lp_lesson_topic', '$new_lp_video')";

			my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
			my $sth = $dbh->prepare($command_insert_lp) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
#			my $num_matches = $sth->rows;
			
			my $inserted_record_id = "";
			# START: GRAB THE LST INSERTED RECORD ID
			my $command_lastid = "SELECT last_insert_id() from $mysql_db_table_name"; 
			my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
			my $sth = $dbh->prepare($command_lastid) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
#			my $num_matches = $sth->rows;
			
				while (my @arr = $sth->fetchrow) {
		    		my ($last_id_inserted) = @arr;
					$show_record = $last_id_inserted;
				}
			# END: GRAB THE LST INSERTED RECORD ID

			$feedback_message .= "The $item_label was $add_edit_type successfully and is highlighted in <a href=\"#$show_record\">YELLOW below</a>.";
			$location = "menu";
		} # END IF USER NAME NOT BLANK

}
#################################################################################
## END: LOCATION = PROCESS_add_item
#################################################################################


#################################################################################
## START: LOCATION = add_item
#################################################################################
if ($location eq 'add_item') {
	my $page_title = "Add a New $item_label";

	my $lp_unique_id = "";
	my $lp_subject = "";
	my $lp_teaching_tips = "";
	my $lp_series_name = "";
	my $lp_series_num = "";
	my $lp_title = "";
	my $lp_note = "";
	my $lp_desc = "";
	my $lp_gradespan = "";
	my $lp_grade_start = "";
	my $lp_grade_end = "";
	my $lp_duration = "";
	my $lp_learning_goals = "";
	my $lp_map2standards = "";
	my $lp_materials = "";
	my $lp_prep = "";
	my $lp_safety = "";
	my $lp_whattodo = "";
	my $lp_evaluate = "";
	my $lp_learnmore = "";
	my $lp_contributor = "";
	my $lp_date_added = "";
	my $lp_show_on_site = "";
	my $lp_toolkit_link = "";
	my $lp_toolkit_pagetitle = "";
	my $lp_attachments = "";
	my $lp_added_by = "";
	my $lp_edited_by = "";
	my $lp_date_edited = "";
	my $lp_extension_activities = "";
	my $lp_lesson_topic = "";
	my $lp_video = "";
	
	if ($show_record ne '') {
		$page_title = "Edit the $item_label";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from $mysql_db_table_name WHERE lp_unique_id = '$show_record'";
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#		my $num_matches = $sth->rows;
		my $num_matches_pubs = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			($lp_unique_id, $lp_subject, $lp_teaching_tips, $lp_series_name, $lp_series_num, $lp_title, $lp_note, $lp_desc, $lp_gradespan, $lp_grade_start, $lp_grade_end, $lp_duration, $lp_learning_goals, $lp_map2standards, $lp_materials, $lp_prep, $lp_safety, $lp_whattodo, $lp_evaluate, $lp_learnmore, $lp_contributor, $lp_date_added, $lp_show_on_site, $lp_toolkit_link, $lp_toolkit_pagetitle, $lp_attachments, $lp_added_by, $lp_edited_by, $lp_date_edited, $lp_extension_activities, $lp_lesson_topic, $lp_video) = @arr;

#			$news_item_content =~ s///gi;
		} # END DB QUERY LOOP
	}
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$lp_title = &cleanaccents2html($lp_title);


print header;
## HELP PAGE FOR TinyMCE: http://www.sandiego.edu/webdev/coding/richcontent.php
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label Manager: $page_title</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">

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

$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td>

<h1><A HREF="$script_name\?show_subject=$show_subject">$site_label Manager</A><br>
$page_title</h1>


<p><strong>Editing Tips:</strong><br>
	<ul>
	<li>The text edit boxes work best in the Firefox browser.</li>
	<li>If you copy/paste from a Web page (like the Toolkit) using the Firefox browser, it will retain bullets in bulleted lists. Safari does not retain the formatting.</li>
	</ul>
</p>
EOM
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print<<EOM;
<FORM ACTION="$script_name" METHOD=POST>

<TABLE border="1" cellpadding="2" cellspacing="0" width="100%" bgcolor="#E3F7AB">
<tr><td valign="top"><strong>Show on site</strong></td>
	<td valign="top">
EOM
		&print_yes_no_menu("new_lp_show_on_site", $lp_show_on_site);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong>Subject</strong></td>
	<td valign="top">
EOM
		&print_subject_menu("new_lp_subject", $lp_subject);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong>Part of Series?</strong></td>
	<td valign="top">If this lesson is part of a series, please enter:<br>
					<table border="1" cellpadding="0" cellspacing="0">
					<tr><td><strong>Series name: </strong>(ex: "Graphs" - 
							visible on screen AND used to associate related records. Use one or two words and NO special characters.)</td>
						<td><INPUT type="text" name="new_lp_series_name" size="20" value="$lp_series_name"></td></tr>
					<tr><td><strong>Number in the series</strong></td>
						<td>
EOM
&print_number_menu("new_lp_series_num", $lp_series_num);
print<<EOM;
					</td></tr>
					</table>
	</td></tr>
<tr><td valign="top"><strong>Title for $item_label</strong></td>
	<td valign="top"><INPUT type="text" name="new_lp_title" size="70" value="$lp_title">
	</td></tr>

<tr><td valign="top"><strong>$item_label Description</strong><p><strong>Tip:</strong><br>Shift-return will create a single line carriage return.</p><p>Use the "HTML" button to edit the HTML source code.</p></td>
	<td valign="top"><textarea name="new_lp_desc" rows=14 cols=70>$lp_desc</textarea></td>
</tr>
<tr><td valign="top"><strong>Note:</strong></td>
	<td valign="top"><textarea name="new_lp_note" rows=12 cols=70>$lp_note</textarea></td>
</tr>
<tr><td valign="top"><strong>Duration:</strong></td>
	<td valign="top"><textarea name="new_lp_duration" rows=12 cols=70>$lp_duration</textarea></td>
</tr>
<tr><td valign="top"><strong>Grade Span:</strong></td>
	<td valign="top">From: 
EOM
&print_grade_menu("new_lp_grade_start", $lp_grade_start);
print " to ";
&print_grade_menu("new_lp_grade_end", $lp_grade_end);
print<<EOM;
	</td>
</tr>

<tr><td valign="top"><strong>Learning Goals:</strong></td>
	<td valign="top"><textarea name="new_lp_learning_goals" rows=12 cols=70>$lp_learning_goals</textarea></td>
</tr>
<tr><td valign="top"><strong>Materials:</strong></td>
	<td valign="top"><textarea name="new_lp_materials" rows=12 cols=70>$lp_materials</textarea></td>
</tr>
<tr><td valign="top"><strong>Prep:</strong></td>
	<td valign="top"><textarea name="new_lp_prep" rows=12 cols=70>$lp_prep</textarea></td>
</tr>
<tr><td valign="top"><strong>Safety Considerations:</strong></td>
	<td valign="top"><textarea name="new_lp_safety" rows=12 cols=70>$lp_safety</textarea></td>
</tr>
<tr><td valign="top"><strong>What to Do:</strong></td>
	<td valign="top"><textarea name="new_lp_whattodo" rows=12 cols=70>$lp_whattodo</textarea></td>
</tr>
<tr><td valign="top"><strong>Extension Activities:</strong></td>
	<td valign="top"><textarea name="new_lp_extension_activities" rows=12 cols=70>$lp_extension_activities</textarea></td>
</tr>
<tr><td valign="top"><strong>Teaching Tips:</strong></td>
	<td valign="top"><textarea name="new_lp_teaching_tips" rows=12 cols=70>$lp_teaching_tips</textarea></td>
</tr>
<tr><td valign="top"><strong>Evaluate (Outcomes to look for):</strong></td>
	<td valign="top"><textarea name="new_lp_evaluate" rows=12 cols=70>$lp_evaluate</textarea></td>
</tr>
<tr><td valign="top"><strong>Learn More:</strong></td>
	<td valign="top"><textarea name="new_lp_learnmore" rows=12 cols=70>$lp_learnmore</textarea></td>
</tr>
<tr><td valign="top"><strong>Map to Standards:</strong></td>
	<td valign="top"><textarea name="new_lp_map2standards" rows=12 cols=70>$lp_map2standards</textarea></td>
</tr>
<tr><td valign="top"><strong>Topic Matches for Searching:</strong></td>
	<td valign="top">
EOM
my $topic_counter = 0;
	while ($topic_counter <= $#topics) {
		print "<input type=\"checkbox\" name=\"new_lp_topic_$topic_counter\" id=\"new_lp_topic_$topic_counter\" value=\"$topics[$topic_counter]\"";
		print " checked" if ($lp_lesson_topic =~ $topics[$topic_counter]);
		print "> <label for=\"new_lp_topic_$topic_counter\">$topics[$topic_counter]</label></a><br>";
		$topic_counter++;
	} # END WHILE LOOP
print<<EOM;
	</td>
</tr>
<tr><td valign="top"><strong>Contributed by:</strong></td>
	<td valign="top">
		<select name="new_lp_contributor">
		<option value="">select an org or person (contact Brian to add new ones)</option>
EOM

		my $command = "select contrib_uniqueid, contrib_state, contrib_program, contrib_contact_name
			FROM site_contributors order by contrib_program, contrib_contact_name";
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#		my $num_matches = $sth->rows;
		my $num_matches_pubs = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($contrib_uniqueid, $contrib_state, $contrib_program, $contrib_contact_name) = @arr;
			print "<option value=\"$contrib_uniqueid\"";
			print " SELECTED" if ($lp_contributor eq $contrib_uniqueid);
			print ">$contrib_state - $contrib_program";
			print " ($contrib_contact_name)" if ($contrib_contact_name ne '');
			print "</option>\n";
		} # END DB QUERY LOOP

print<<EOM;
		</select>
	</td></tr>
<tr><td valign="top"><strong>Toolkit Link</strong></td>
	<td valign="top"><INPUT type="text" name="new_lp_toolkit_link" size="70" value="$lp_toolkit_link"><br>
					If from the Toolkit, enter the address for the page of the Toolkit listing this lesson.
	</td></tr>
<tr><td valign="top"><strong>Toolkit Video Link</strong> (if any)</td>
	<td valign="top"><INPUT type="text" name="new_lp_video" size="70" value="$lp_video"><br>
					Enter the address for the page of the Toolkit listing this lesson.
	</td></tr>
<tr><td valign="top"><strong>Toolkit Page Title</strong></td>
	<td valign="top"><INPUT type="text" name="new_lp_toolkit_pagetitle" size="70" value="$lp_toolkit_pagetitle"><br>
					Enter the title of the page (this text will be the link the user sees and clicks on) for the Toolkit page you entered above.
	</td></tr>
EOM
	if ($show_record ne '') {
print<<EOM;
<tr><td valign="top"><strong>Date Added:</strong></td>
	<td valign="top">
EOM
		my ($old_y, $old_m, $old_d) = split(/\-/,$lp_date_added);
		&print_month_menu("new_startdate_m", $old_m);
		&print_day_menu("new_startdate_d", $old_d);
		&print_year_menu("new_startdate_y", 2007, $year + 1, $old_y);

print<<EOM;
	</td></tr>
EOM
	}

#Unused variables in the database but not editable on the form so far (let me know if these are useful - they came from a sample Toolkit $item_label):
#<ul><font color=red>
#	Attachments: $lp_attachments<br></font>
#</ul>

print<<EOM;
</table>
<p></p>
	<div style="margin-left:25px;">
		<INPUT TYPE="HIDDEN" NAME="show_subject" VALUE="$show_subject">
		<INPUT TYPE="HIDDEN" NAME="show_record" VALUE="$show_record">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_add_item">
	<INPUT TYPE="SUBMIT" VALUE="Click to Save ($page_title)">
	</div>
</form>
<p></p>




<div class="first fltRt">
		<FORM ACTION="$script_name" METHOD=POST>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr><td colspan="2"><em>Click here to delete this $item_label.</em></td></tr>
		<tr><td valign="top"><input type="checkbox" name="confirm" value="confirmed"></td>
			<td valign="top"><span style="color:red">confirm the deletion<br> of this $item_label.</span></td></tr>
		<tr><td colspan="2">
				<input type="hidden" name="location" value="process_delete_lp">
				<input type="hidden" name="show_record" value="$show_record">
				<input type="submit" name="submit" value="Delete $item_label"></td></tr>
				
		</table>
		</form>
	
</div>


</td>
	<td valign="top" align="right">
		(Click here to <A HREF="$script_name?location=logout">logout</A>)
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
	$sortby = "title" if ($sortby eq '');
## PRINT SIGNUP FORM
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label Manager: List of $item_label\s</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1><A HREF="$script_name\?show_subject=$show_subject">$site_label Manager</A>
		<br>List of $item_label\s</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="$script_name?location=logout">logout</A>)
	</td></tr>
</table>
EOM

#	if ($logonuser_is_afterschool_representative ne 'yes') {
#		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view $item_label.</FONT>";
#	}
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');

$searchfor = &cleanformysql($searchfor);
$show_subject = &cleanformysql($show_subject);

my $command = "select * from $mysql_db_table_name where lp_unique_id like '%'";
#	$command .= " AND hiring_supervisor = '$new_hiring_supervisor'" if ($new_hiring_supervisor ne '');
#	$command .= " AND hiring_supervisor = '$cookie_ss_staff_id'" if ($logonuser_is_afterschool_representative ne 'yes');
	
#	$command .= " order by datestamp_created DESC" if (($sortby eq '') || ($sortby eq 'date'));
#	$command .= " order by applyfor_position, datestamp_created DESC" if ($sortby eq 'position');
#	$command .= " order by form_complete DESC, applyfor_position, datestamp_completed DESC" if ($sortby eq 'completed');
#	$command .= " order by name_l, name_f" if ($sortby eq 'applicant');
#	$command .= " order by hiring_supervisor, name_l, name_f" if ($sortby eq 'supervisor');
	$command .= " AND lp_title like '%$searchfor%'" if ($searchfor ne '');
	$command .= " AND lp_subject like '%$show_subject%'" if ($show_subject ne '');
	$command .= " order by lp_title" if ($sortby eq 'title');
	$command .= " order by lp_subject, lp_title" if ($sortby eq 'subject');
	$command .= " order by lp_date_added DESC, lp_title" if ($sortby eq 'dateadded');



my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches_lp = $sth->rows;

my $col_head_title = "<strong>Title</strong>";
   $col_head_title = "<a href=\"$script_name?location=menu&amp;sortby=title&amp;show_subject=$show_subject\">Title</a>" if ($sortby ne 'title');
my $col_head_subject = "<strong>Subject</strong>";
   $col_head_subject = "<a href=\"$script_name?location=menu&amp;sortby=subject&amp;show_subject=$show_subject\">Subject</a>" if ($sortby ne 'subject');
my $col_head_dateadded = "<strong>Added/Last Edited</strong>";
   $col_head_dateadded = "<a href=\"$script_name?location=menu&amp;sortby=dateadded&amp;show_subject=$show_subject\">Added/Last Edited</a>" if ($sortby ne 'dateadded');

print<<EOM;
<FORM ACTION="$script_name" METHOD=POST>
<p>
Displaying $num_matches_lp $item_label\s from 
<select name="show_subject">
EOM
my @subjects = ("", "Art", "Literacy", "Math", "Science", "Technology");
my @subjects_label = ("All Subjects", "Art", "Literacy", "Math", "Science", "Technology");
my $counter = 0;
	while ($counter <= $#subjects) {
		print "<option value=\"$subjects[$counter]\" ";
		print " SELECTED" if ($show_subject eq $subjects[$counter]);
		print">$subjects_label[$counter]</option>";
		$counter++;
	}
print<<EOM;
</select> whose title contains the word or phrase
	<input type="TEXT" name="searchfor" value="$searchfor" sixe="12">
  <INPUT TYPE=HIDDEN NAME=location VALUE="">
  <INPUT TYPE=SUBMIT VALUE="Refresh list">
</p>
</form>

<p>
Click here to <A HREF=\"$script_name?location=add_item&amp;show_subject=$show_subject\">Add a New $item_label</A>.
</p>
<TABLE border="1" cellpadding="2" cellspacing="0" width="100%">
EOM


	if ($num_matches_lp == 0) {
		print "<tr><td><P class=\"alert\">There are no $item_label\s in the database that match your search.</p></td></tr>";
	} else {
print<<EOM;
<TR bgcolor="#ebebeb">
	<td><strong>Show<br>on<br>site?</strong></td>
	<td>$col_head_title (click a title to edit the $item_label)</td>
	<td>$col_head_subject</td>
	<td align="center"><strong>Grade<br>Span</strong></td>
	<td align="center"><strong>From<br>Toolkit?</strong></td>
	<td align="center">Search Mappings</td>
	<td>$col_head_dateadded</td>
</TR>
EOM
	}
my $counter = 1;
	while (my @arr = $sth->fetchrow) {
		my ($lp_unique_id, $lp_subject, $lp_teaching_tips, $lp_series_name, $lp_series_num, $lp_title, $lp_note, $lp_desc, $lp_gradespan, $lp_grade_start, $lp_grade_end, $lp_duration, $lp_learning_goals, $lp_map2standards, $lp_materials, $lp_prep, $lp_safety, $lp_whattodo, $lp_evaluate, $lp_learnmore, $lp_contributor, $lp_date_added, $lp_show_on_site, $lp_toolkit_link, $lp_toolkit_pagetitle, $lp_attachments, $lp_added_by, $lp_edited_by, $lp_date_edited, $lp_extension_activities, $lp_lesson_topic, $lp_video) = @arr;
		my $bgcolor="";
  			$bgcolor="BGCOLOR=\"#FFFFCC\"" if ($show_record eq $lp_unique_id);
 			$bgcolor="BGCOLOR=\"#FFCCCC\"" if ($lp_show_on_site eq 'no');


		# TRANSFORM DATES INTO PRETTY FORMAT
		$lp_date_added = &date2standard($lp_date_added);
		$lp_date_added = "N/A" if ($lp_date_added =~ '0000');
		$lp_date_edited = &date2standard($lp_date_edited);
		$lp_date_edited = "N/A" if ($lp_date_edited =~ '0000');

		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$lp_title = &cleanaccents2html($lp_title);
		$lp_show_on_site = "<font color=red>NO</font>" if ($lp_show_on_site eq 'no');

		if ($lp_toolkit_link ne '') {
			$lp_toolkit_link = "<a href=\"$lp_toolkit_link\" target=\"_blank\">yes</a>";
			if ($lp_toolkit_pagetitle eq '') {
				$lp_toolkit_link .= "(t)";
			}
		} else {
			$lp_toolkit_link = "&nbsp;";
		}
print<<EOM;
<TR $bgcolor>
	<td>$lp_show_on_site<br><font color="#999999">#$lp_unique_id</font></td>
	<td valign="top"><a name="$lp_unique_id"></a><A HREF=\"$script_name?location=add_item&amp;show_record=$lp_unique_id\" TITLE="Click to edit this $item_label">$lp_title</a>
	</td>
	<td>$lp_subject</td>
	<td align="center">$lp_gradespan</td>
	<td align="center">$lp_toolkit_link</td>
	<td align="center">$lp_lesson_topic</td>
	<td>Added: $lp_added_by ($lp_date_added)<br>
		Edited: $lp_edited_by ($lp_date_edited)
EOM
print "<br><font color=red>Warning: Missing Contributor Info</font>" if ($lp_contributor == 0);
print<<EOM;
	</td>
</TR>
EOM
		$counter++;
	} # END DB QUERY LOOP
print<<EOM;
</TABLE>
<P>
The $site_label is located at <A HREF="$public_site_address">$public_site_address</A>.
</p>
<p>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
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


######################
##  CLEAN FOR MYSQL ## 
######################
## THIS SUBROUTINE MAKES SURE ANY SPECIAL CHARACTERS ARE BACKSLASHED BEFORE SENDING A COMMAND TO MYSQL
sub cleanformysql {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\/\>/\>/g; # REMOVE SINGLETON TAGS
   $dirtyitem =~ s/\\//g;
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/‘/\\‘/g;
   $dirtyitem =~ s/’/\\’/g;
   $dirtyitem =~ s/"/\\"/g;
	return($dirtyitem);
}



####################################################################
## START: CONVERT DATESTAMP TO PRETTY DATE/TIME FORMAT
####################################################################
sub convert_timestamp_2pretty_w_date {
my $timestamp = $_[0];
my $show_hours_minutes = $_[1];

my $this_year = substr($timestamp, 0, 4);
my $this_month = substr($timestamp, 4, 2);
my $this_date = substr($timestamp, 6, 2);
my $this_hours = substr($timestamp, 8, 2);
my $this_min = substr($timestamp, 10, 2);
my $am_pm = "AM";
	if ($this_hours > 12) {
		$this_hours = $this_hours - 12;
		$am_pm = "PM";
	}
	if ($this_hours == 12) {
		$am_pm = "PM";
	}
my $pretty_time = "$this_month/$this_date/$this_year";
	if ($show_hours_minutes eq 'yes') {
		$pretty_time .= " $this_hours:$this_min $am_pm";
	}
   return($pretty_time);
}
####################################################################
## END: CONVERT DATESTAMP TO PRETTY DATE/TIME FORMAT
####################################################################


####################################################################
## START: SUBROUTINE randomPassword
####################################################################
sub randomPassword {
my $password_length = $_[0];
	if (!$password_length) {
		$password_length = 5;
	}
	my $password; # THIS WILL HOLD THE NEW PASSWORD
	my $_rand; # HOLDS A RANDOM CHARACTER
my @chars = split(" ", "a b c d e f g h j k m n p q r s t u v w x y z 2 3 4 5 6 7 8 9");
srand;
	for (my $i=0; $i <= $password_length ;$i++) {
		$_rand = int(rand 31);
		$password .= $chars[$_rand];
	}
	$password =~ tr/a-z/A-Z/; # lowercase everything (may not be necessary anymore)
	return $password;
}
####################################################################
## END: SUBROUTINE randomPassword
####################################################################


#################################################################
## START SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################
sub date2standard {
my $date2transform = $_[0];
   $date2transform =~ s/\ //g;
   $date2transform =~ s/\-/\//g;
my ($thisyear, $thismonth, $thisdate) = split(/\//,$date2transform);
   $date2transform = "$thismonth\/$thisdate\/$thisyear";
   $date2transform = "" if $thismonth eq '';
	return($date2transform);
}
#################################################################
## END SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################




######################################
## START: SUBROUTINE print_day_menu
######################################
sub print_day_menu {
my $field_name = $_[0];
my $previous_selection = $_[1];
	my @days_value = ("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31");
	my $day_counter = "0";
	my $count_total_days = $#days_value;
print<<EOM;
<SELECT NAME="$field_name">
<OPTION VALUE=\"\">day</OPTION>
EOM
		while ($day_counter <= $count_total_days) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $days_value[$day_counter]);
			print "<OPTION VALUE=\"$days_value[$day_counter]\" $selected>$days_value[$day_counter]</OPTION>\n";
			$day_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_day_menu
######################################

######################################
## START: SUBROUTINE print_month_menu
######################################
sub print_month_menu {
my $field_name = $_[0];
my $previous_selection = $_[1];
	my @months_value = ("", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	my @months_label = ("month", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	my $month_counter = "0";
	my $count_total_months = $#months_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($month_counter <= $count_total_months) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $months_value[$month_counter]);
			print "<OPTION VALUE=\"$months_value[$month_counter]\" $selected>$months_label[$month_counter]</OPTION>\n";
			$month_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_month_menu
######################################


######################################
## START: SUBROUTINE print_year_menu
######################################
sub print_year_menu {
my $field_name = $_[0];
my $start_year = $_[1];
my $end_year = $_[2];
my $previous_selection = $_[3];
print<<EOM;
<SELECT NAME="$field_name">
<OPTION VALUE=\"\">year</OPTION>
EOM
	my $year_counter = $end_year;
		while ($year_counter >= $start_year) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $year_counter);
			print "<OPTION VALUE=\"$year_counter\" $selected>$year_counter</OPTION>\n";
			$year_counter--;
		} # END WHILE
	print "</SELECT>\n";
# SAMPLE USAGE: &print_year_menu($site_current_year, $site_current_year + 3, "");
######################################
} # END: SUBROUTINE print_year_menu
######################################


######################################
## START: SUBROUTINE print_grade_menu
######################################
sub print_grade_menu {
my $field_name = $_[0];
my $previous_selection = $_[1];
	my @grades_value = ("", "-1", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	my @grades_label = ("select a grade", "preK", "K", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	my $grade_counter = "0";
	my $count_total_grades = $#grades_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($grade_counter <= $count_total_grades) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $grades_value[$grade_counter]);
			print "<OPTION VALUE=\"$grades_value[$grade_counter]\" $selected>$grades_label[$grade_counter]</OPTION>\n";
			$grade_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_grade_menu
######################################


######################################
## START: SUBROUTINE print_subject_menu
######################################
sub print_subject_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", "Art", "Literacy", "Math", "Science", "Technology");
	my @item_label = ("select a subject", "Art", "Literacy", "Math", "Science", "Technology");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_month_menu
######################################


######################################
## START: SUBROUTINE print_number_menu
######################################
sub print_number_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10");
	my @item_label = ("", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection == $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_month_menu
######################################

######################################
## START: SUBROUTINE print_yes_no_menu
######################################
sub print_yes_no_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("yes", "no");
	my @item_label = ("yes", "no");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name" alt="$previous_selection">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_month_menu
######################################


sub cleanaccents2html {
my $cleanitem = $_[0];
	$cleanitem =~ s/“/"/g;			
	$cleanitem =~ s/”/"/g;			
	$cleanitem =~ s/’/'/g;			
	$cleanitem =~ s/‘/'/g;
	$cleanitem =~ s// /g;
	$cleanitem =~ s/–/\&ndash\;/g;
	$cleanitem =~ s/—/\&mdash\;/g;
	$cleanitem =~ s/ //g; # invisible bullet
	$cleanitem =~ s/…/.../g;
	$cleanitem =~ s/À/&Agrave\;/g; 
	$cleanitem =~ s/à/&agrave\;/g;	
	$cleanitem =~ s/Á/&Aacute\;/g;  
	$cleanitem =~ s/á/&aacute\;/g;
	$cleanitem =~ s/Â/&Acirc\;/g;
	$cleanitem =~ s/â/&acirc\;/g;
	$cleanitem =~ s/Ã/&Atilde\;/g;
	$cleanitem =~ s/ã/&atilde\;/g;
	$cleanitem =~ s/Ä/&Auml\;/g;
	$cleanitem =~ s/ä/&auml\;/g;
	$cleanitem =~ s/É/&Eacute\;/g;
	$cleanitem =~ s/é/&eacute\;/g;
	$cleanitem =~ s/È/&Egrave\;/g;
	$cleanitem =~ s/è/&egrave\;/g;
	$cleanitem =~ s/Ê/&Euml\;/g;
	$cleanitem =~ s/ë/&euml\;/g;
	$cleanitem =~ s/Ì/&Igrave\;/g;
	$cleanitem =~ s/ì/&igrave\;/g;
	$cleanitem =~ s/Í/&Iacute\;/g;
	$cleanitem =~ s/í/&iacute\;/g;
	$cleanitem =~ s/Î/&Icirc\;/g;
	$cleanitem =~ s/î/&icirc\;/g;
	$cleanitem =~ s/Ï/&Iuml\;/g;
	$cleanitem =~ s/ï/&iuml\;/g;
	$cleanitem =~ s/Ñ/&Ntilde\;/g;
	$cleanitem =~ s/ñ/&ntilde\;/g;
	$cleanitem =~ s/Ò/&Ograve\;/g;
	$cleanitem =~ s/ò/&ograve\;/g;
	$cleanitem =~ s/Ó/&Oacute\;/g;
	$cleanitem =~ s/ó/&oacute\;/g;
	$cleanitem =~ s/Õ/&Otilde\;/g;
	$cleanitem =~ s/õ/&otilde\;/g;
	$cleanitem =~ s/Ö/&Ouml\;/g;
	$cleanitem =~ s/ö/&ouml\;/g;
	$cleanitem =~ s/Ù/&Ugrave\;/g;
	$cleanitem =~ s/ù/&ugrave\;/g;
	$cleanitem =~ s/Ú/&Uacute\;/g;
	$cleanitem =~ s/ú/&uacute\;/g;
	$cleanitem =~ s/Û/&Ucirc\;/g;  ## THIS REPLACES THE ó FOR SOME REASON
	$cleanitem =~ s/û/&ucirc\;/g;
	$cleanitem =~ s/Ü/&Uuml\;/g;
	$cleanitem =~ s/ü/&uuml\;/g;
	$cleanitem =~ s/ÿ/&yuml\;/g;
	return ($cleanitem);
}

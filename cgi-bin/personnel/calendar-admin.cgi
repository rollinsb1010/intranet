#!/usr/bin/perl

#####################################################################################################
# Copyright 2001 by Southwest Educational Development Laboratory
#
# Written by Brian Litke 11-05-2001 
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
my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $future_events_only = $query->param("future_events_only");
   $future_events_only = "yes" if ($future_events_only eq '');

my $show_cal = $query->param("show_cal");
my $show_event = $query->param("show_event");

my $show_full_detail = $query->param("show_full_detail");
   $show_full_detail = "no" if ($show_full_detail eq '');

my $logon_pass = $query->param("logon_pass");

my $uniqueid = param('uniqueid');

my $location = param('location');
   $location = "logon" if $location eq '';

my $showsession = param('showsession');

my $error_message = "";
my $feedback_message = "";

my @calendar_options = ("corp", "intranet-home", "intranet-institution", "afterschool", "secc", "txcc");
my @calendar_options_helptext = ("", "", "<font color=red>Use only for SMC meetings, all staff meetings, and SEDL holidays)</font>", "", "", "");

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("139"); # 139 is the PID for this page in the intranet database

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
				$location = "calendar_menu";

		} else {
			## INVALID ID/PASS: SHOW LOON SCREEN & SET ERROR MESSAGE
			if ($num_matches_for_logon_id_entered == 1) {
				$error_message = "Your User ID ($logon_user) is valid, but the password you entered did not match the one on file.  Try again, or contact SEDL's IT Manager, Brian Litke, at ext. 6529.";
			} else {
				if (length($logon_user) > 8) {
					$error_message = "The User ID you entered ($logon_user) is over 8 characters long.  The intranet logon is your first initial and last name, with a maximum limit of 8 characters.  Try again, or contact SEDL's IT Manager, Brian Litke, at ext. 6529.";
				} else {
					$error_message = "The User ID you entered ($logon_user) is not a valid SEDL staff ID.  Try again, or contact SEDL's IT Manager, Brian Litke, at ext. 6529.";
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

			## SAVE COOKIES WITH SESSION ID AND USER ID
				setCookie ("ss_session_id", $ss_session_id, $expdate, $path, $thedomain);
#				setCookie ("staffid", $ss_staff_id, $expdate, $path, $thedomain);
			## SET LOCATION
				$location = "calendar_menu" if (($location eq '') || ($location eq 'logon'));
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



	if ( (
		($cookie_ss_staff_id ne 'blitke')
		&& ($cookie_ss_staff_id ne 'afrenzel')
		&& ($cookie_ss_staff_id ne 'awest')
		&& ($cookie_ss_staff_id ne 'brollins')
		&& ($cookie_ss_staff_id ne 'ccox')
		&& ($cookie_ss_staff_id ne 'cmoses')
		&& ($cookie_ss_staff_id ne 'cpierron')
		&& ($cookie_ss_staff_id ne 'ctimes')
		&& ($cookie_ss_staff_id ne 'eurquidi')
		&& ($cookie_ss_staff_id ne 'ewaters')
		&& ($cookie_ss_staff_id ne 'jwackwit')
		&& ($cookie_ss_staff_id ne 'ktimmons')
		&& ($cookie_ss_staff_id ne 'jmiddlet')
		&& ($cookie_ss_staff_id ne 'jstarks')
		&& ($cookie_ss_staff_id ne 'jwestbro')
		&& ($cookie_ss_staff_id ne 'lforador')
		&& ($cookie_ss_staff_id ne 'lharris')
		&& ($cookie_ss_staff_id ne 'lmartine')
		&& ($cookie_ss_staff_id ne 'lshankla')
		&& ($cookie_ss_staff_id ne 'macuna')
		&& ($cookie_ss_staff_id ne 'malvarez')
		&& ($cookie_ss_staff_id ne 'mrodrigu')
		&& ($cookie_ss_staff_id ne 'nreynold')
		&& ($cookie_ss_staff_id ne 'pramirez')
		&& ($cookie_ss_staff_id ne 'rjarvis')
		&& ($cookie_ss_staff_id ne 'sabdulla')
		&& ($cookie_ss_staff_id ne 'sbeckwit')
		&& ($cookie_ss_staff_id ne 'srodrigu')
		&& ($cookie_ss_staff_id ne 'triley')
		&& ($cookie_ss_staff_id ne 'vdimock')
		&& ($cookie_ss_staff_id ne 'whoover')
		) && ($location ne 'logon')) {
		$error_message = "At this time, only COM staff and specific contacts in each project group can update the Web site calendars. However, we would like to distribute this responsibility to appropriate staff in each department. Please contact Brian Litke at ext. 6529 if you would like to have the ability to add/edit calendar events for SEDL's corporate and project calendars.";
		$location = "logon";
	}



####################################################################
## START: LOG STAFF ID TO DATABASE TO TRACK USAGE OF THIS RESOURCE
####################################################################
## IF STAFF USER ID IS PRESENT IN COOKIE, LOG THEIR USE OF THIS TOOL TO THE TRACKING DATABASE
if (($cookie_ss_staff_id ne '') && ($location ne 'logon')) {
	my $commandinsert = "INSERT INTO staffpageusage VALUES ('$cookie_ss_staff_id', '$date_full_mysql', 'Online Calendar Administration')";
	my $dsn = "DBI:mysql:database=test;host=localhost";
	my $dbh = DBI->connect($dsn);
	my $sth = $dbh->prepare($commandinsert) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: Logged to tracking database";
}
####################################################################
## END: LOG STAFF ID TO DATABASE TO TRACK USAGE OF THIS RESOURCE
####################################################################

print header;

#################################################################################
## START: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################
if ($location eq 'logon') {

## PRINT SIGNUP FORM
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Intranet - Calendar Administration</TITLE>
$htmlhead
<h1>Calendar Administration</h1>
EOM

print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');

print<<EOM;      
<p>
Welcome to the SEDL Online Calendar Administration page.  This page allows you to add, remove, and edit items that appear on the 
	<ul>
	<li>SEDL corporate calendar of events</li>
	<li>Project-specific online calendars</li>
	<li>SEDL intranet "Institutional Calendar"</li>
	<li>SEDL intranet calendar "This week at SEDL"</li>
	</ul>
<P>
Please enter your SEDL user ID (ex: whoover) and password to view the intranet calendar.
<BR>
<FORM ACTION="/staff/personnel/calendar-admin.cgi" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
  <TR><TD VALIGN="TOP"><strong>Your user ID</strong> (ex: whoover)</TD>
      <TD VALIGN="TOP">
      <INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id">
      </TD></TR>
  <TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR><SPAN class=small>(not your email password)</SPAN></TD>
      <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD></TR>
</TABLE>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE=SUBMIT VALUE="Log On to Calendar Administration">
  </div>
  </FORM>
<P>
To report troubles using this form, send an email to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at  ext. 6529.

$htmltail
</BODY></HTML>
EOM
} 
#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################


#################################################################################
## START: LOCATION = process_delete
#################################################################################
if ($location eq 'process_delete') {
	## READ VARIABLES
	my $confirm = $query->param("confirm");

	$show_event = &commoncode::cleanthisfordb($show_event);
	my $event_owner = "";

	## START: GET DATA ABOUT EVENT
	my $event_id= ""; my $eventname1= ""; my $eventname2= ""; my $startdate= ""; my $enddate= ""; my $city= ""; my $state= ""; my $venue= ""; my $description= ""; my $contact_name= ""; my $contact_phone= ""; my $contact_email= ""; my $contact_web= ""; my $focusarea= ""; my $sedlproject= ""; my $show_on_calendars = ""; my $entered_by = ""; my $entered_date = ""; my $approved_by = ""; my $approved_date = ""; my $from_calendar = "";

	my $command_getowner = "SELECT * FROM sedlcalendar WHERE event_id LIKE '$show_event'";
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_getowner) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			($event_id, $eventname1, $eventname2, $startdate, $enddate, $city, $state, $venue, $description, $contact_name, $contact_phone, $contact_email, $contact_web, $focusarea, $sedlproject, $show_on_calendars, $entered_by, $entered_date, $approved_by, $approved_date, $from_calendar) = @arr;
		}
	## START: GET DATA ABOUT EVENT


	## START: ONLY ALLOW OWNER TO DELETE EVENT
	if (($entered_by ne $cookie_ss_staff_id) && ($cookie_ss_staff_id ne 'blitke') && ($cookie_ss_staff_id ne 'brollins') && ($cookie_ss_staff_id ne 'jwackwit') && ($cookie_ss_staff_id ne 'lshankla') && ($cookie_ss_staff_id ne 'macuna') && ($cookie_ss_staff_id ne 'sabdulla') && ($cookie_ss_staff_id ne 'cmoses')) {
			$error_message = "Deletion aborted.  Sorry $cookie_ss_staff_id, Only the staff member who entered the event ($entered_by) may delete it.";
			$location = "addedit";

	} else {
	
		if ($confirm eq 'confirmed') {

		my $message_subject = "Notice: Intranet Calendar Deletion";
		my $message_body = "The SEDL staff user \"$cookie_ss_staff_id\" deleted the event:\n\nEvent: $eventname1\n $eventname2\nStarts: $startdate\n Ends: $enddate\nCity: $city\nState = $state\n Venue = $venue\nDescription:\n$description\nContact name: $contact_name\nPhone: $contact_phone\nEmail: $contact_email\nWeb site: $contact_web\nFocus area: $focusarea\nSEDL Project: $sedlproject\nShow on calendars: $show_on_calendars\nEntered by: $entered_by\n";
		&send_email_toadmin($message_subject, $message_body);
		
			## DELETE FROM DB
			my $command_delete = "DELETE FROM sedlcalendar WHERE event_id LIKE '$show_event'";
			my $dsn = "DBI:mysql:database=corp;host=localhost";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_delete) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
#my $num_matches = $sth->rows;
		
			## SET FEEDBACK MESSAGE
			$feedback_message .= "You successfully deleted the calendar entry.";
			$feedback_message .= "<br><strong>The following Calendars were updated (if your event was listed on them):</strong><br>- <a href=\"/events/\" target=\"_blank\">SEDL corporate calendars</a><br>- <a href=\"/afterschool/training/calendar.html\" target=\"_blank\">Afterschool events calendar</a>";
			$error_message .= "Click here to <a href=\"/cgi-bin/mysql/sedlbirthdays.cgi?debug=makehtml\">re-generate the intranet \"view of the days ahead\" calendar</A>; otherwise, it will be re-generated tonight at midnight.";
			$location = "calendar_menu";
		} else {
			## SET FEEDBACK MESSAGE
			$error_message = "Deletion aborted.  You forgot to select the confirm box.";
			$location = "addedit";
		} # END IF/ELSE
	} # END IF/ELSE

	## UPDATE THE TEXT CALENDARS
	system ("/home/httpd/cgi-bin/mysql/corp/sedlcalendar-html.cgi");
}
#################################################################################
## END: LOCATION = process_delete
#################################################################################


#################################################################################
## START: LOCATION = process_addedit
#################################################################################
if ($location eq 'process_addedit') {
	## CHECK IF THIS IS AN ADD OR AN EDIT
	my $add_or_edit = "Add";
	   $add_or_edit = "Edit" if ($show_event ne '');

	## READ VARIABLES
	my $new_eventname1 = $query->param("new_eventname1");
	my $new_eventname2 = $query->param("new_eventname2");
	my $new_startdate_m = $query->param("new_startdate_m");
	my $new_startdate_d = $query->param("new_startdate_d");
	my $new_startdate_y = $query->param("new_startdate_y");
	my $new_enddate_m = $query->param("new_enddate_m");
	my $new_enddate_d = $query->param("new_enddate_d");
	my $new_enddate_y = $query->param("new_enddate_y");
	my $new_city = $query->param("new_city");
	my $new_state = $query->param("new_state");
	my $new_venue = $query->param("new_venue");

	my $new_description = $query->param("new_description");
	my $new_contact_name = $query->param("new_contact_name");
	my $new_contact_phone = $query->param("new_contact_phone");
	my $new_contact_email = $query->param("new_contact_email");
	my $new_contact_web = $query->param("new_contact_web");
	my $new_focusarea = $query->param("new_focusarea");
	my $new_sedlproject = $query->param("new_sedlproject");
	my $new_internal_pd_event = $query->param("new_internal_pd_event");
	my $show_on_cals;
	
	my $counter_calendars = "0";
	my $num_calendars = $#calendar_options;
		while ($counter_calendars <= $num_calendars) {
			$show_on_cals .= $query->param("show_on_cal_$counter_calendars");
			$show_on_cals .= " ";
			$counter_calendars++;
		} # END WHILE

	my $new_startdate = "$new_startdate_y\-$new_startdate_m\-$new_startdate_d";
	my $new_enddate = "$new_enddate_y\-$new_enddate_m\-$new_enddate_d";
		$new_startdate = "" if ($new_startdate eq '--');
		$new_enddate = "" if ($new_enddate eq '--');
		if ($new_enddate eq '') {
			$new_enddate = $new_startdate;
		} # END IF
	## GRAB NON-EDITABLE VARIABLES
	my $new_entered_by = "";
	my $new_entered_date = "";
	my $new_approved_by = "";
	my $new_approved_date = "";
	my $new_from_calendar = "";

	
	if ($add_or_edit eq 'Edit') {
		my $command = "select entered_by, entered_date, approved_by, approved_date, from_calendar from sedlcalendar ";
		   $command .= " WHERE event_id LIKE '$show_event'";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#my $num_matches = $sth->rows;
			while (my @arr = $sth->fetchrow) {
				my ($entered_by, $entered_date, $approved_by, $approved_date, $from_calendar) = @arr;
				$new_entered_by = $entered_by;
				$new_entered_date = $entered_date;
				$new_approved_by = $approved_by;
				$new_approved_date = $approved_date;
				$new_from_calendar = $from_calendar;
			} # END DB QUERY LOOP
			if ($new_entered_by eq '') {
				$new_entered_by = $cookie_ss_staff_id;
				$new_entered_date = $date_full_mysql;
			}
	} else {
		$new_entered_by = $cookie_ss_staff_id;
		$new_entered_date = $date_full_mysql;
	}

	
	## CLEAN SPECIAL CHARS BEFORE SAVING TO DB
	$new_eventname1 = &commoncode::cleanthisfordb($new_eventname1);
	$new_eventname2 = &commoncode::cleanthisfordb($new_eventname2);
	$new_startdate = &commoncode::cleanthisfordb($new_startdate);
	$new_enddate = &commoncode::cleanthisfordb($new_enddate);
	$new_city = &commoncode::cleanthisfordb($new_city);
	$new_state = &commoncode::cleanthisfordb($new_state);
	$new_venue = &commoncode::cleanthisfordb($new_venue);
	$new_description = &commoncode::cleanthisfordb($new_description);
	$new_contact_name = &commoncode::cleanthisfordb($new_contact_name);
	$new_contact_phone = &commoncode::cleanthisfordb($new_contact_phone);
	$new_contact_email = &commoncode::cleanthisfordb($new_contact_email);
	$new_contact_web = &commoncode::cleanthisfordb($new_contact_web);
	$new_focusarea = &commoncode::cleanthisfordb($new_focusarea);
	$new_sedlproject = &commoncode::cleanthisfordb($new_sedlproject);
	$new_internal_pd_event = &commoncode::cleanthisfordb($new_internal_pd_event);

	$show_on_cals = &commoncode::cleanthisfordb($show_on_cals);
	$show_event = &commoncode::cleanthisfordb($show_event);

	$new_entered_by = &commoncode::cleanthisfordb($new_entered_by);
	$new_entered_date = &commoncode::cleanthisfordb($new_entered_date);
	$new_approved_by = &commoncode::cleanthisfordb($new_approved_by);
	$new_approved_date = &commoncode::cleanthisfordb($new_approved_date);
	$new_from_calendar = &commoncode::cleanthisfordb($new_from_calendar);
	
		my $message_subject = "Notice: Intranet Calendar $add_or_edit";
		my $add_or_edited = "EDITED";
		   $add_or_edited = "ADDED" if ($add_or_edit eq "Add");
		my $message_body = "\"$cookie_ss_staff_id\" $add_or_edited the event:\n\n Event: $new_eventname1\n $new_eventname2\nStarts: $new_startdate\n Ends: $new_enddate\nCity: $new_city\nState = $new_state\n Venue = $new_venue\nDescription:\n$new_description\nContact name: $new_contact_name\nPhone: $new_contact_phone\nEmail: $new_contact_email\nWeb site: $new_contact_web\nFocus area: $new_focusarea\nSEDL Project: $new_sedlproject\nShow on calendars: $show_on_cals\nEntered by: $new_entered_by";
		&send_email_toadmin($message_subject, $message_body);


	## SAVE TO DB
	my $command_addedit = "REPLACE INTO sedlcalendar VALUES ('$show_event', '$new_eventname1', '$new_eventname2', '$new_startdate', '$new_enddate', '$new_city', '$new_state', '$new_venue', 
						'$new_description', '$new_contact_name', '$new_contact_phone', '$new_contact_email', '$new_contact_web', '$new_focusarea', '$new_sedlproject', '$show_on_cals', 
						'$new_entered_by', '$new_entered_date', '$new_approved_by', '$new_approved_date', '$new_from_calendar', '$new_internal_pd_event')";
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_addedit) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;

	## SET FEEDBACK MESSAGE
	$add_or_edit .= "ed";
	$feedback_message .= "You successfully $add_or_edit the calendar entry. The affected record is <a href=\"#$show_event\">highlighted in yellow below</a>.\n";
	$feedback_message .= "<br><strong>The following Calendars were updated (if your event was listed on them):</strong><br>- <a href=\"/events/\" target=\"_blank\">SEDL corporate calendars</a><br>- <a href=\"/afterschool/training/calendar.html\" target=\"_blank\">Afterschool events calendar</a>\n";
	$error_message .= "Click here to <a href=\"/cgi-bin/mysql/sedlbirthdays.cgi?debug=makehtml\">re-generate the intranet calendar</A>.\n";
	$location = "calendar_menu";

	## UPDATE THE TEXT CALENDARS
	system ("/home/httpd/cgi-bin/mysql/corp/sedlcalendar-html.cgi");
}
#################################################################################
## END: LOCATION = process_addedit
#################################################################################


#################################################################################
## START: LOCATION = addedit
#################################################################################
if ($location eq 'addedit') {
	my $add_or_edit = "Add";
	   $add_or_edit = "Edit" if ($show_event ne '');
	my $event_id= ""; my $eventname1= ""; my $eventname2= ""; my $startdate= ""; my $enddate= ""; my $city= ""; my $state= ""; my $venue= ""; my $description= ""; my $contact_name= ""; my $contact_phone= ""; my $contact_email= ""; my $contact_web= ""; my $focusarea= ""; my $sedlproject= ""; my $show_on_calendars = ""; my $entered_by = ""; my $entered_date = ""; my $approved_by = ""; my $approved_date = ""; my $from_calendar = ""; my $internal_pd_event = "";

	if ($show_event ne '') {
		# START: READ DATA FOR PREVIOUS EVENT
		my $command = "select * from sedlcalendar ";
		   $command .= " WHERE event_id LIKE '$show_event'";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#my $num_matches = $sth->rows;
			while (my @arr = $sth->fetchrow) {
				($event_id, $eventname1, $eventname2, $startdate, $enddate, $city, $state, $venue, $description, $contact_name, $contact_phone, $contact_email, $contact_web, $focusarea, $sedlproject, $show_on_calendars, $entered_by, $entered_date, $approved_by, $approved_date, $from_calendar, $internal_pd_event) = @arr;
				$enddate = "" if ($enddate eq '0000-00-00');
				if ($entered_by =~ 'ewaters') {
					$error_message = "This is a CC event that is updated nightly from the CC events database.  To edit this record, make the edit in the CC events database instead.  Contact Eric Waters at ext. 6564 for assistance.";
				}
			}
		# END: READ DATA FOR PREVIOUS EVENT
	} # END IF

print<<EOM;
<HTML>
<HEAD><TITLE>SEDL Intranet - Online Calendar Administration: $add_or_edit Calendar Entry</TITLE>


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
EOM

#<script language="javascript" type="text/javascript" src="/common/javascript/spellChecker.js">
#</script>
#<script language="javascript" type="text/javascript">
#function openSpellChecker() {
#	// get the textarea we're going to check
#	var txt = document.myform.new_description;
#	
#	// give the spellChecker object a reference to our textarea
#	// pass any number of text objects as arguments to the constructor:
#	var speller = new spellChecker( txt );
#	
#	// kick it off
#	speller.openChecker();
#}
#</script>


print<<EOM;      
<form action="calendar-admin.cgi" method="POST">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td valign="top"><h1><A HREF="calendar-admin.cgi?show_cal=$show_cal">Calendar Administration</A>: $add_or_edit Calendar Entry<BR>
		(Click here to <A HREF="calendar-admin.cgi?location=logout">logout</A>)</h1></td>
	<td align="right">
EOM
	if (($show_event ne '') && (($cookie_ss_staff_id eq 'blitke') || ($cookie_ss_staff_id eq 'brollins') || ($cookie_ss_staff_id eq 'cmoses') || ($cookie_ss_staff_id eq 'jwackwit') || ($cookie_ss_staff_id eq 'lshankla') || ($cookie_ss_staff_id eq 'macuna') || ($cookie_ss_staff_id eq 'sabdulla') || ($entered_by eq $cookie_ss_staff_id)) ) {
print<<EOM;
<div class="first fltRt">

	<table cellpadding="0" cellspacing="0" border="0">
	<tr><td colspan="2"><em><label for="confirm">Click here to delete this calendar entry.</label></em></td></tr>
	<tr><td valign="top"><input type="checkbox" name="confirm" id="confirm" value="confirmed"></td>
		<td valign="top"><span style="color:red">confirm the deletion<br> of this calendar entry.</span></td></tr>
	<tr><td colspan="2">
			<input type="hidden" name="future_events_only" value="$future_events_only">
			<input type="hidden" name="show_cal" value="$show_cal">
			<input type="hidden" name="location" value="process_delete">
			<input type="hidden" name="show_event" value="$event_id">
			<input type="submit" name="submit" value="Delete"></td></tr>
			</form>
	</table>
</div>
EOM
	}
print<<EOM;
	</td></tr>
</table>
EOM
print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');

print<<EOM;
<p>
<FORM ACTION=/staff/personnel/calendar-admin.cgi METHOD="POST" name="myform" id="myform">
<table border="1" cellpadding="4" cellspacing="0">
<tr><td valign="top"><label for="new_eventname1">Event Name</label></td>
	<td valign="top"><textarea name="new_eventname1" id="new_eventname1" rows="8" cols=70>$eventname1</textarea><br>
					Required, this is the name of the event, or the main name of the event. (i.e. AERA 2006 Conference)</td></tr>
<tr><td valign="top"><label for="new_eventname2">Event Name (second part)</label></td>
	<td valign="top"><textarea name="new_eventname2" id="new_eventname2" rows="8" cols=70>$eventname2</textarea><br>
					This part will automatically be shown in italics on the calendar page, this field is often used for the "theme" of the event. (i.e. <em>Making Good Choices</em>)</td></tr>

<tr><td valign="top">Start Date</td>
	<td valign="top">
		<table align=right>
		<tr><td><INPUT TYPE="SUBMIT" VALUE="Save"></td></tr>
		</table>
EOM
		my ($old_y, $old_m, $old_d) = split(/\-/,$startdate);
		&commoncode::print_month_menu("new_startdate_m", $old_m);
		&commoncode::print_day_menu("new_startdate_d", $old_d);
		&commoncode::print_year_menu("new_startdate_y", $year, $year + 2, $old_y);

print<<EOM;
	</td></tr>
<tr><td valign="top">End Date</td>
	<td valign="top">
EOM
		my ($old_y, $old_m, $old_d) = split(/\-/,$enddate);
		&commoncode::print_month_menu("new_enddate_m", $old_m);
		&commoncode::print_day_menu("new_enddate_d", $old_d);
		&commoncode::print_year_menu("new_enddate_y", $year, $year + 2, $old_y);

print<<EOM;
	</td></tr>
	<tr><td valign="top"><label for="new_internal_pd_event">Staff Internal PD Event?</label></td>
		<td>
EOM
&commoncode::printform_yes_no_menu("new_internal_pd_event", $internal_pd_event);
print<<EOM;
	<p>Note: After the session, you can edit the description, which will become the "archive" of your PD session. If you need files for your PD session uploaded to the intranet and do not have that ability, please contact or send the files to <a href="mailto:brian.litke\@sedl.org">Brian Litke</a> at ext. 6529.</p>
	</td></tr>
<tr><td valign="top">Show on Calendars</td>
	<td valign="top">
		<table border="0" cellpadding="1" cellspacing="0">
EOM

my $counter_calendars = "0";
my $num_calendars = $#calendar_options;
	while ($counter_calendars <= $num_calendars) {
		print "<tr><td><input type=\"checkbox\" name=\"show_on_cal_$counter_calendars\" value=\"$calendar_options[$counter_calendars]\"";
		print " CHECKED" if ($show_on_calendars =~ $calendar_options[$counter_calendars]);
		print " id=\"$calendar_options[$counter_calendars]\"></td><td><label for=\"$calendar_options[$counter_calendars]\">$calendar_options[$counter_calendars]";
		print " ($calendar_options_helptext[$counter_calendars])"if ($calendar_options_helptext[$counter_calendars] ne '');
		print "</label></td></tr>";
		$counter_calendars++;
	}



print<<EOM;
		</table>
	</td></tr>
<tr><td valign="top"><label for="new_city">City</label>, <label for="new_state">State</label></td>
	<td valign="top"><input type="TEXT" name="new_city" size="20" value="$city">, 
EOM
&commoncode::printform_state("new_state", $state);
print<<EOM;
</td></tr>
<tr><td valign="top"><label for="new_venue">Venue</label><br>
	(e.g. hotel)</td>
	<td valign="top"><input type="TEXT" name="new_venue" id="new_venue" size="40" value="$venue"></td></tr>
<tr><td valign="top"><label for="new_description">Event Description</label><P><!input type="button" value="Spellcheck" onClick="openSpellChecker();"></td>
	<td valign="top"><textarea name="new_description" id="new_description" rows="20" cols="70">$description</textarea></td></tr>

<tr><td valign="top">Contact information</td>
	<td valign="top">
		<table cellpadding="4" cellspacing="0">
		<tr><td valign="top"><label for="new_contact_name">Person:</label></td>
			<td valign="top"><input type="TEXT" name="new_contact_name" id="new_contact_name" size="30" value="$contact_name"></td></tr>
		<tr><td valign="top">Phone:</label></td>
			<td valign="top"><label for="new_contact_phone"><input type="TEXT" name="new_contact_phone" id="new_contact_phone" size="30" value="$contact_phone"></td></tr>
		<tr><td valign="top">Email:</label></td>
			<td valign="top"><label for="new_contact_email"><input type="TEXT" name="new_contact_email" id="new_contact_email" size="40" value="$contact_email"></td></tr>
		<tr><td valign="top">Web site:</label></td>
			<td valign="top"><label for="new_contact_web"><input type="TEXT" name="new_contact_web" id="new_contact_web" size="60" value="$contact_web"></td></tr>
		</table>
	</td></tr>
<tr><td valign="top"><label for="new_focusarea">Focus Area</label></td>
	<td valign="top">
		<select name="new_focusarea" id="new_focusarea">
		<option value="">(select one)</option>
EOM

	# START:READ LIST OF PROGRAMS
	my $command = "select pg_name from sedlprograms order by pg_name";
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($pg_name) = @arr;
			print "<option value=\"$pg_name\"";
			print " SELECTED" if ($pg_name eq $focusarea);
			print ">$pg_name</option>";
		} # END DB QUERY LOOP

my $selected_op = "";
   $selected_op = "SELECTED" if ($sedlproject eq 'Office of the President');
my $selected_oic = "";
   $selected_oic = "SELECTED" if ($sedlproject eq 'Office of Institutional Communications');
my $selected_cpl = "";
   $selected_cpl = "SELECTED" if ($sedlproject eq 'Center for Professional Learning');
print<<EOM;
	</select>
	</td></tr>
<tr><td valign="top">SEDL Project</td>
	<td valign="top">
EOM
&printform_sedl_projects("new_sedlproject", $sedlproject);
print<<EOM;
	</td></tr>
</table>
	<div style="margin-left:25px;">
	<input type="hidden" name="future_events_only" value="$future_events_only">
	<input type="hidden" name="from_calendar" value="$from_calendar">
	<input type="hidden" name="show_cal" value="$show_cal">
	<INPUT TYPE="HIDDEN" NAME="show_event" VALUE="$show_event">
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_addedit">
	<INPUT TYPE="SUBMIT" VALUE="Save">
	</form>
	</div>
<p>
To report troubles using this form, send an email to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
<p>
$htmltail
EOM

}
#################################################################################
## END: LOCATION = addedit
#################################################################################


#################################################################################
## START: LOCATION = calendar_menu
#################################################################################
if ($location eq 'calendar_menu') {

#my $prettyname = &get_staff_fullname($cookie_ss_staff_id);

## PRINT SIGNUP FORM
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Intranet - Online Calendar Administration</TITLE>
$htmlhead

<h1>Calendar Administration: Main Menu - View/add/edit Online Calendar Entries<BR>(Click here to <A HREF="calendar-admin.cgi?location=logout">logout</A>)</h1>
EOM

print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');

print<<EOM;      
<p>
<FORM ACTION="/staff/personnel/calendar-admin.cgi" METHOD="POST">
	<label for="show_cal">Currently displaying events from</label> 
	<select name="show_cal" id="show_cal">
	<option value="">All Calendars</option>
EOM
my $counter_calendars = "0";
my $num_calendars = $#calendar_options;
	while ($counter_calendars <= $num_calendars) {
		print "<option value=\"$calendar_options[$counter_calendars]\"";
		print " SELECTED" if ($show_cal eq $calendar_options[$counter_calendars]);
		print ">$calendar_options[$counter_calendars]</option>";
		$counter_calendars++;
	}


print<<EOM;
	</select>
	<input type="checkbox" name="future_events_only" id="future_events_only" value="no"
EOM
print " CHECKED" if ($future_events_only eq 'no');
print<<EOM;
><label for="future_events_only">Show past events?</label>
	<input type="hidden" name="show_cal" value="$show_cal">
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="calendar_menu">
	<INPUT TYPE="SUBMIT" VALUE="Refresh Listing">
	</form>
EOM


	my $command = "select * from sedlcalendar WHERE event_id LIKE '%'";
	   $command .= " AND startdate >= '$date_full_mysql'" if ($future_events_only eq "yes");
	   $command .= " AND show_on_calendars LIKE '%$show_cal%'" if ($show_cal ne "");
	   $command .= " order by startdate";
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_events = $sth->rows;

print<<EOM;
<p>
<span style="color:green;">There are $num_matches_events future events on file.</span> Click here to <A href="calendar-admin.cgi?location=addedit&amp;show_cal=$show_cal&amp;future_events_only=$future_events_only">add a calendar entry</a>.  Click here to <a href="http://www.sedl.org/cgi-bin/mysql/sedlbirthdays.cgi?debug=makehtml">refresh the intranet home page calendar</a>.
</p>

<TABLE BORDER="1" CELLPADDING="1" CELLSPACING="0">
<TR>
EOM
my %cal_header_icons;
   $cal_header_icons{"corp"} = "/staff/images/calendars/cal_label_corp.gif";
#   $cal_header_icons{"sedlhome"} = "/staff/images/calendars/cal_label_sedlhome.gif";
   $cal_header_icons{"intranet-home"} = "/staff/images/calendars/cal_label_intranet_thisweek.gif";
   $cal_header_icons{"intranet-institution"} = "/staff/images/calendars/cal_label_intranet_inst.gif";
   $cal_header_icons{"afterschool"} = "/staff/images/calendars/cal_label_afterschool.gif";
   $cal_header_icons{"secc"} = "/staff/images/calendars/cal_label_secc.gif";
   $cal_header_icons{"txcc"} = "/staff/images/calendars/cal_label_txcc.gif";

my %cal_header_url;
   $cal_header_url{"corp"} = "/events/";
#   $cal_header_url{"sedlhome"} = "/";
   $cal_header_url{"intranet-home"} = "/cgi-bin/mysql/staff/index.cgi";
   $cal_header_url{"intranet-institution"} = "/cgi-bin/mysql/staff/index.cgi?show_s=5&show_sg=5&pid=14";
   $cal_header_url{"afterschool"} = "/afterschool/training/calendar.html";
   $cal_header_url{"secc"} = "http://secc.sedl.org/events/index.html";
   $cal_header_url{"txcc"} = "http://txcc.sedl.org/events/index.html";

my $counter_calendars = "0";
my $num_calendars = $#calendar_options;
	while ($counter_calendars <= $num_calendars) {
		my $calendar_name = $calendar_options[$counter_calendars];
#		$calendar_name =~ s/\-/\- /gi;
		print "<td><a href=\"$cal_header_url{$calendar_name}\"><img src=\"$cal_header_icons{$calendar_name}\" alt=\"$calendar_name calendar\" title=\"click here to go to the $calendar_name calendar\" border=\"0\"></A></td>";
		$counter_calendars++;
	}
print<<EOM;
	<TD><strong>Date</strong></TD>
	<TD><strong>Event</strong> Click an event title to view or edit event details.</TD>
	<td><strong>Entered by</strong></td>
</TR>
EOM

	while (my @arr = $sth->fetchrow) {
		my ($event_id, $eventname1, $eventname2, $startdate, $enddate, $city, $state, $venue, $description, $contact_name, $contact_phone, $contact_email, $contact_web, $focusarea, $sedlproject, $show_on_calendars, $entered_by, $entered_date, $approved_by, $approved_date, $from_calendar) = @arr;
		$enddate = "" if ($enddate eq '0000-00-00');
		my $date_range = &get_daterange($startdate, $enddate);
		$city = &commoncode::cleanaccents2html($city);
		$venue = &commoncode::cleanaccents2html($venue);
		$description = &commoncode::cleanaccents2html($description);
		$contact_name = &commoncode::cleanaccents2html($contact_name);
		$contact_email = &commoncode::cleanaccents2html($contact_email);
		$contact_phone = &commoncode::cleanaccents2html($contact_phone);
		$contact_web = &commoncode::cleanaccents2html($contact_web);
		
		# MAKE SURE URL STARTS WITH "http://"
		$contact_web = "http://$contact_web";
		$contact_web =~ s/http:\/\/http:\/\//http:\/\//gi;
my $bgcolor = "";
   $bgcolor = "BGCOLOR=\"#E8FFE8\"" if ($entered_by eq $cookie_ss_staff_id);
   $bgcolor = "BGCOLOR=\"#FFFFCC\"" if ($event_id eq $show_event);
print "<tr $bgcolor><a name=\"$event_id\"></a>";
my $counter_calendars = "0";
my $num_calendars = $#calendar_options;
	while ($counter_calendars <= $num_calendars) {
		my $x = "&nbsp;";
		   $x = "X" if ($show_on_calendars =~ $calendar_options[$counter_calendars]);
		print "<td align=\"center\">$x</td>";
		$counter_calendars++;
	}

print<<EOM;
<td valign="top" nowrap>$date_range</td>
	<td valign="top"><a href="calendar-admin.cgi?location=addedit&amp;show_event=$event_id&amp;show_cal=$show_cal&amp;future_events_only=$future_events_only"><strong>$eventname1
EOM
print ": <em>$eventname2</em>" if ($eventname2 ne '');
print "</a></strong>";
	if ($show_full_detail eq 'yes') {
print "<br>$city, $state" if (($city ne '') && ($state ne ''));
print "<br>$venue" if ($venue ne '');
print "<br><br>$description" if ($description ne '');

print "<br><br><strong>Contact:</strong> " if (($contact_name ne '') || ($contact_email ne '') || ($contact_phone ne '') || ($contact_web ne ''));
print " $contact_name\n" if $contact_name;
print " <br>Phone: $contact_phone" if $contact_phone;
print " <br>Email: <A HREF=mailto:$contact_email>$contact_email</A>" if $contact_email;
print " <br>Web site: <A HREF=$contact_web class=\"small\">$contact_web</A>" if $contact_web;
	}
#	$entered_date = &date2standard($entered_date);
print "</td><td>$entered_by<br><font color=\"#999999\">$entered_date</font></td>";
#	if ($approved_date ne '') {
#		print "<td><font color=\"green\">YES</font></td>";
#	} else {
#		print "<td><font color=\"red\">NO</font></td>";
#	}
print "</tr>";
	} # END DB QUERY LOOP


print<<EOM;
</TABLE>
<p>
To report troubles using this form, send an email to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
$htmltail
EOM
} 
#################################################################################
## END: LOCATION = calendar_menu
#################################################################################

#print "<P>DEBUG VARIABLES:<BR>LOCATION: $location<br>Logon User: $logon_user";




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


#####################################################################
## START: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
#####################################################################
sub get_staff_fullname {
	my $staff_userid = $_[0];
	my $prettyname = "";

	my $command = "select firstname, middleinitial, lastname from staff_profiles 
					WHERE (userid like '$staff_userid')";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	my $name = "";
	#$error_message .=  "<P>COMMAND: $command";
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
	    my ($firstname, $middleinitial, $lastname) = @arr;
			$prettyname = "$firstname $lastname";
		}
	return($prettyname);
}
#####################################################################
## END: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
#####################################################################



## SUBROUTINE THAT RETURNS FULL MONTH NAME WHEN YOU SENT IT A MONTH NUMBER
sub get_daterange {
	my $startdate = $_[0];
	my $enddate = $_[1];
	   $enddate = $startdate if ($enddate eq '');
	my $new_date_range = "";

	## CHOP UP DATE TO MAKE IT USEABLE
	my ($sdateyear, $sdatemonth, $sdateday) = split(/\-/,$startdate);
	my ($edateyear, $edatemonth, $edateday) = split(/\-/,$enddate);

	# GET FULL TEXT NAME FOR THE START/END MONTHS
	$sdatemonth = &commoncode::getFullMonthName ($sdatemonth);
	$edatemonth = &commoncode::getFullMonthName ($edatemonth);

	## START: DETERMINE DATE NUMBERS WITHOUT LEADING ZEROS
	my $sdateday_label = $sdateday;
	   $sdateday_label = substr($sdateday,1,1) if (substr($sdateday,0,1) eq '0');
	my $edateday_label = $edateday;
	   $edateday_label = substr($edateday,1,1) if (substr($edateday,0,1) eq '0');
	## END: DETERMINE DATE NUMBERS WITHOUT LEADING ZEROS

		# PRINT START MONTH
		$new_date_range = "$sdatemonth ";
		
		# PRINT START DATE IF THIS IS A ONE-DAY EVENT
		$new_date_range .= "$sdateday_label" if (($sdatemonth eq $edatemonth) && ($sdateday eq $edateday));
		# PRINT START DATE IF THIS IS A MULTI-DAY EVENT THAT IS ALL IN THE SAME MONTH
		$new_date_range .= "<BR>$sdateday_label\&ndash;$edateday_label" if (($sdatemonth eq $edatemonth) && ($sdateday ne $edateday));
		# PRINT START DATE IF THIS IS A MULTI-DAY EVENT THAT SPANS 2 MONTHS
		$new_date_range .= "$sdateday_label\&ndash;<BR>$edatemonth $edateday_label" if (($startdate ne $enddate) && ($sdatemonth ne $edatemonth));
		$sdateyear = $edateyear if ($edateyear ne '');
		$new_date_range .= ", $sdateyear";
	return ($new_date_range);
}


##################################################
## START: SUBROUTINE printform_sedl_projects
##################################################
sub printform_sedl_projects {
	my $form_variable_name = $_[0];
	my $selected_item = $_[1];

	my $counter_item = "0";
	my @items = ("", 
		"Administrative Services", 
		"Executive Office", 
		"Communications", 
		"Center for High-Performing Schools",
		"Center on Knowledge Translation for Disability and Rehabilitation Research (KTDRR)", 
		"Center for Knowledge Translation and Employment Research (KTER)",
		"National Center for Quality Afterschool",
		"Research and Evaluation",
		"Southeast Comprehensive Center",
		"Southeast REL",
		"Southwest ADA Center Research",
		"Southwest REL",
		"Texas Comprehensive Center",
		"Vocational Rehabilitation Service Models for Individuals with Autism");

	my @items_labels = ("(select one)", 
		"Administrative Services", 
		"Executive Office", 
		"Communications", 
		"Center for High-Performing Schools",
		"Center on Knowledge Translation for Disability and Rehabilitation Research (KTDRR)", 
		"Center for Knowledge Translation and Employment Research (KTER)",
		"National Center for Quality Afterschool",
		"Research and Evaluation",
		"Southeast Comprehensive Center",
		"Southeast REL",
		"Southwest ADA Center Research",
		"Southwest REL",
		"Texas Comprehensive Center",
		"Vocational Rehabilitation Service Models for Individuals with Autism");

	print "<select name=\"$form_variable_name\" id=\"$form_variable_name\">\n";
	while ($counter_item <= $#items) {
		print "<option value=\"$items[$counter_item]\"";
		print " SELECTED" if ($items[$counter_item] eq $selected_item);
		print ">$items_labels[$counter_item]</option>\n";
		$counter_item++;
	} # END WHILE
	print "</select>\n";
}
##################################################
## END: SUBROUTINE printform_sedl_projects
##################################################


####################################################################
## START SUBROUTINE: SEND_EMAIL
####################################################################
sub send_email_toadmin {
my $message_subject = $_[0];
my $message_body = $_[1];

# EMAIL SERVER VARIABLES
my $mailprog = '/usr/sbin/sendmail -t';
my $fromaddr = 'webmaster@sedl.org';


# START: ONLY SEND IF EMAIL IS VALID
open(NOTIFY,"| $mailprog");

print NOTIFY <<EOM;
From: SEDL intranet Calendar <$fromaddr>
To: Calendar Manager <$fromaddr>
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: $message_subject

Dear Calendar Manager,
 
$message_body

This is an automated message from the SEDL intranet Calendar Administration Tool.
If you have questions or need assistance, please contact Brian Litke at ext. 6529.
EOM
	close(NOTIFY);

}
####################################################################
## END SUBROUTINE: SEND_EMAIL
####################################################################
# 		&send_email_toadmin($message_subject, $message_body);

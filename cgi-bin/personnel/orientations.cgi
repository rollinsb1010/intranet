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
################################################
## END: LOAD PERL MODULES USED BY THIS SCRIPT
################################################

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 
use intranetcommoncode; 

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

my $lastyear = $year - 1;
my $one_year_ago_mysql = "$lastyear\-$month\-$monthdate_wleadingzero";

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

my $uniqueid = $query->param("uniqueid");
my $isedit = $query->param("isedit");
my $editrecord = $query->param("editrecord");

my $location = $query->param("location");
   $location = "logon" if $location eq '';

## VARIABLES FOR INDICATING COMPLETION OF ORIENTATION CHECKLIST ITEM
my $orientee = $query->param("orientee");
my $orienter = $query->param("orienter");
my $orienttype = $query->param("orienttype");
my $confirm = $query->param("confirm");

## FEEDBACK MESSAGE VARIABLES
my $error_message = "";
my $feedback_message = "";

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("412"); # 412 is the PID for this page in the intranet database

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

			## SAVE COOKIES WITH SESSION ID AND USER ID
				setCookie ("ss_session_id", $ss_session_id, $expdate, $path, $thedomain);
#				setCookie ("staffid", $ss_staff_id, $expdate, $path, $thedomain);
			## SET LOCATION
				$location = "menu" if (($location eq '') || ($location eq 'logon'));
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



#	if ( (
#		($cookie_ss_staff_id ne 'blitke')
#		&& ($cookie_ss_staff_id ne 'afrenzel')
#		&& ($cookie_ss_staff_id ne 'cmoses')
#		&& ($cookie_ss_staff_id ne 'ctimes')
#		&& ($cookie_ss_staff_id ne 'ewaters')
#		&& ($cookie_ss_staff_id ne 'emualler')
#		&& ($cookie_ss_staff_id ne 'jwackwit')
#		&& ($cookie_ss_staff_id ne 'ktimmons')
#		&& ($cookie_ss_staff_id ne 'jmiddlet')
#		&& ($cookie_ss_staff_id ne 'lforador')
#		&& ($cookie_ss_staff_id ne 'lharris')
#		&& ($cookie_ss_staff_id ne 'lmartine')
#		&& ($cookie_ss_staff_id ne 'lshankla')
#		&& ($cookie_ss_staff_id ne 'macuna')
#		&& ($cookie_ss_staff_id ne 'mrodrigu')
#		&& ($cookie_ss_staff_id ne 'nreynold')
#		&& ($cookie_ss_staff_id ne 'rjarvis')
#		&& ($cookie_ss_staff_id ne 'sabdulla')
#		&& ($cookie_ss_staff_id ne 'sbeckwit')
#		&& ($cookie_ss_staff_id ne 'srodrigu')
#		&& ($cookie_ss_staff_id ne 'tmoreno')
#		&& ($cookie_ss_staff_id ne 'vdimock')
#		&& ($cookie_ss_staff_id ne 'whoover')
#		) && ($location ne 'logon')) {
#		$error_message = "At this time, only OIC staff and specific contacts in each project group can update the Web site calendars. However, we would like to distribute this responsibility to appropriate staff in each department. Please contact Brian Litke at ext. 6529 if you would like to have the ability to add/edit calendar events for SEDL's corporate and project calendars.";
#		$location = "logon";
#	}


####################################################################
## START: MAKE HASH CONTAINING ALL STAFF FULL NAMES
####################################################################
my %staff_fullname;
my %staff_firstname;

		my $command = "select userid, firstname, lastname from staff_profiles order by firstname, lastname";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
			while (my @arr = $sth->fetchrow) {
			    my ($userid, $firstname, $lastname) = @arr;
			    $staff_fullname{$userid} = "$firstname $lastname";
			    $staff_firstname{$userid} = "$firstname";
			} # END DB QUERY LOOP
####################################################################
## END: MAKE HASH CONTAINING ALL STAFF FULL NAMES
####################################################################

print header;

#################################################################################
## START: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################
if ($location eq 'logon') {

## PRINT SIGNUP FORM
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Intranet - Staff Orientation Checklist</TITLE>
$htmlhead
<h1>Staff Orientation Checklist</h1>
EOM

print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');

print<<EOM;      
<p>
Welcome to the SEDL Staff Orientation Checklist.  
This page allows SEDL's HR staff, SEDL supervisors, SEDL support staff, and new SEDL employees 
to indicate which orientation stages a new staff member has completed.
	<ul>
	<li>New employees will see their own orientation checklist.</li>
	<li>SEDL's Human Resources staff will see the orientation checklists for all staff and can initiate a new checklist for 
		new staff members.</li>
	<li>Managers will only see staff related to their department.</li>
	<li>Support staff will only see staff related to their department.</li>
	<li>General staff will only see entries relating to them.</li>
	<li>Wes, Stuart, and Christine will be able to view all staff, but they can only update 
		statuses related to their orientation responsibilities.</li>
	</ul>
<P>
Please enter your SEDL user ID (ex: whoover) and password to view the orientation checklists.
<BR>
<FORM ACTION="/staff/personnel/orientations.cgi" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
  <TR><TD VALIGN="TOP"><strong><label for="logon_user">Your user ID</label></strong> (ex: whoover)</TD>
      <TD VALIGN="TOP">
      <INPUT TYPE="text" NAME="logon_user" id="logon_user" SIZE=8 VALUE="$cookie_ss_staff_id">
      </TD></TR>
  <TR><TD VALIGN="TOP" WIDTH="120"><strong><label for="logon_pass">Your intranet password</label></strong><BR><SPAN class=small>(not your e-mail password)</SPAN></TD>
      <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" id="logon_pass" SIZE="8"></TD></TR>
</TABLE>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Log On to the Staff Orientation Checklist">
  </div>
  </FORM>
<p>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at  ext. 6529.
</p>
$htmltail
EOM
} 
#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################


##################################################################################
### START: LOCATION = process_delete
##################################################################################
#if ($location eq 'process_delete') {
#	## READ VARIABLES
#
#	$show_event = &commoncode::cleanthisfordb($show_event);
#	my $event_owner = "";
#
#	## START: GET DATA ABOUT EVENT
#	my $event_id= ""; my $eventname1= ""; my $eventname2= ""; my $startdate= ""; my $enddate= ""; my $city= ""; my $state= ""; my $venue= ""; my $description= ""; my $contact_name= ""; my $contact_phone= ""; my $contact_email= ""; my $contact_web= ""; my $focusarea= ""; my $sedlproject= ""; my $show_on_calendars = ""; my $entered_by = ""; my $entered_date = ""; my $approved_by = ""; my $approved_date = ""; my $from_calendar = "";
#
#	my $command_getowner = "SELECT * FROM sedlcalendar WHERE event_id LIKE '$show_event'";
#	my $dsn = "DBI:mysql:database=intranet;host=localhost";
#	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#	my $sth = $dbh->prepare($command_getowner) or die "Couldn't prepare statement: " . $dbh->errstr;
#	$sth->execute;
##my $num_matches = $sth->rows;
#		while (my @arr = $sth->fetchrow) {
#			($event_id, $eventname1, $eventname2, $startdate, $enddate, $city, $state, $venue, $description, $contact_name, $contact_phone, $contact_email, $contact_web, $focusarea, $sedlproject, $show_on_calendars, $entered_by, $entered_date, $approved_by, $approved_date, $from_calendar) = @arr;
#		}
#	## START: GET DATA ABOUT EVENT
#
#
#	## START: ONLY ALLOW OWNER TO DELETE EVENT
#	if ($entered_by ne $cookie_ss_staff_id) {
#			$error_message = "Deletion aborted.  Sorry $cookie_ss_staff_id, Only the staff member who entered the event ($entered_by) may delete it.";
#			$location = "addedit";
#
#	} else {
#	
#		if ($confirm eq 'confirmed') {
#
#		my $message_subject = "Notice: Intranet Calendar Deletion";
#		my $message_body = "The SEDL staff user \"$cookie_ss_staff_id\" deleted the event:\n\nEvent: $eventname1\n $eventname2\nStarts: $startdate\n Ends: $enddate\nCity: $city\nState = $state\n Venue = $venue\nDescription:\n$description\nContact name: $contact_name\nPhone: $contact_phone\nE-mail: $contact_email\nWeb site: $contact_web\nFocus area: $focusarea\nSEDL Project: $sedlproject\nShow on calendars: $show_on_calendars\nEntered by: $entered_by\n";
#		&send_email_toadmin($message_subject, $message_body);
#		
#			## DELETE FROM DB
#			my $command_delete = "DELETE FROM sedlcalendar WHERE event_id LIKE '$show_event'";
#			my $dsn = "DBI:mysql:database=intranet;host=localhost";
#			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#			my $sth = $dbh->prepare($command_delete) or die "Couldn't prepare statement: " . $dbh->errstr;
#			$sth->execute;
##my $num_matches = $sth->rows;
#		
#			## SET FEEDBACK MESSAGE
#			$feedback_message .= "You successfully deleted the calendar entry.";
#			$feedback_message .= "<br><strong>The following Calendars were updated (if your event was listed on them):</strong><br>- <a href=\"/events/\" target=\"_blank\">SEDL corporate calendars</a><br>- <a href=\"/afterschool/training/calendar.html\" target=\"_blank\">Afterschool events calendar</a>";
#			$error_message .= "Click here to <a href=\"/cgi-bin/mysql/sedlbirthdays.cgi?debug=makehtml\">re-generate the intranet \"view of the days ahead\" calendar</A>; otherwise, it will be re-generated tonight at midnight.";
#			$location = "menu";
#		} else {
#			## SET FEEDBACK MESSAGE
#			$error_message = "Deletion aborted.  You forgot to select the confirm box.";
#			$location = "addedit";
#		} # END IF/ELSE
#	} # END IF/ELSE
#
#	## UPDATE THE TEXT CALENDARS
#	system ("/home/httpd/cgi-bin/mysql/corp/sedlcalendar-html.cgi");
#}
##################################################################################
### END: LOCATION = process_delete
##################################################################################


#################################################################################
## START: LOCATION = process_completion
#################################################################################
if ($location eq 'process_completion') {
	## CHECK TO ENSURE STAFF MEMBER IS ALLOWED TO ADD A CHECKLIST
	if ($confirm eq '') {
		$error_message = "You forgot to checkmark the confirmation box.  Your indicaiton of completion was NOT saved.";
		$location = "completion";
	} # END IF

} # END IF

if ($location eq 'process_completion') {

	## CLEAN SPECIAL CHARS BEFORE SAVING TO DB
	$orientee = &commoncode::cleanthisfordb($orientee);
	$orienter = &commoncode::cleanthisfordb($orienter);
	$orienttype = &commoncode::cleanthisfordb($orienttype);
	
	## SAVE TO DB
	my $command_update = "UPDATE staff_orientation SET $orienttype = '$cookie_ss_staff_id;$date_full_mysql', so_lastupdated = '$timestamp' where so_staffid LIKE '$orientee'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_update) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;

	## SEND STATUS UPDATE E-MAIL TO SUE
		my $orienttype_label = &get_orientation_fullname($orienttype);
		my $orientee_label = &get_staff_fullname($orientee);
		   $orientee_label = $orientee if ($orientee_label eq '');
		my $loggedon_user_fullname = &get_staff_fullname($cookie_ss_staff_id);
		   $loggedon_user_fullname = $cookie_ss_staff_id if ($loggedon_user_fullname eq '');
	&send_email_toadmin("$loggedon_user_fullname Indicated $orienttype_label is Complete for $orientee_label", "$loggedon_user_fullname has Indicated that \"$orienttype_label\" is Complete for the new staff member, $orientee_label\.", "$orienter\@sedl.org"); # subject, message_body
	
	## SET FEEDBACK MESSAGE
		my $orienttype_label = &get_orientation_fullname($orienttype);
		my $orientee_label = &get_staff_fullname($orientee);
		   $orientee_label = $orientee if ($orientee_label eq '');
		my $orienter_label = &get_staff_fullname($cookie_ss_staff_id);
		   $orienter_label = $cookie_ss_staff_id if ($orienter_label eq '');
	$feedback_message .= "Thank you, $orienter_label. You successfully indicated completion of the checklist item '$orienttype_label' for $orientee_label.";
	$location = "menu";

}
#################################################################################
## END: LOCATION = process_completion
#################################################################################


#################################################################################
## START: LOCATION = completion
#################################################################################
# orientee
# orienter
# orienttype

if ($location eq 'completion') {
	## CHECK TO ENSURE STAFF MEMBER IS ALLOWED TO ADD A CHECKLIST
	my $userid_who_should_approve = "error_unassigned";
		if ($orienttype =~ '_staff') {
			$userid_who_should_approve = $orientee;
		} else {
			$userid_who_should_approve = &lookup_orienter($orientee, $orienttype);
		}
	if ($cookie_ss_staff_id ne $userid_who_should_approve) {
		my $orienttype_label = &get_orientation_fullname($orienttype);
		my $orientee_label = &get_staff_fullname($orientee);
		   $orientee_label = $orientee if ($orientee_label eq '');
		my $loggedon_user_fullname = &get_staff_fullname($cookie_ss_staff_id);
		my $userid_who_should_approve_fullname = &get_staff_fullname($userid_who_should_approve);
		   $userid_who_should_approve_fullname = $userid_who_should_approve if ($userid_who_should_approve_fullname eq '');
		$error_message = "You ($loggedon_user_fullname) are not authorized to indicate completion of '$orienttype_label' for the new staff member '$orientee_label.'<br>$userid_who_should_approve_fullname is authorized to indicate completion of this item.<br>Contact Brian Litke at ext. 6529 if you believe this to be an error.";
		$location = "menu";
	} # END IF

	if (($orientee eq '') || (($orienter eq '') && ($orienttype =~ '_comp')) || ($orienttype eq '')) {
		$error_message = "Unexpected error.  The orientee information was not passed correctly.";
		$error_message .= " (Missing 'orientee' information) " if ($orientee eq '');
		$error_message .= " (Missing 'orienter' information) " if ($orienter eq '');
		$error_message .= " (Missing 'orienttype' information) " if ($orienttype eq '');
		$error_message .= "<br>Contact Brian Litke at ext. 6529 regarding this error.";
		$location = "menu";
	} # END IF

}

if ($location eq 'completion') {

	my $orienttype_label = &get_orientation_fullname($orienttype);
	my $orientee_label = &get_staff_fullname($orientee);
	   $orientee_label = $orientee if ($orientee_label eq '');
print<<EOM;
<HTML>
<HEAD><TITLE>SEDL Intranet - Staff Orientation Checklist: Indicate Completion of Checklist Item</TITLE>
$htmlhead
<form action="orientations.cgi" method=POST>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td valign="top"><h1><a href="orientations.cgi">Staff Orientation Checklist</a>:<br>
		Indicate Completion of '$orienttype_label'<br>for new staff member: $orientee_label</h1></td>
	<td align="right" valign="top">(Click here to <A HREF="orientations.cgi?location=logout">logout</A>)
</td></tr>
</table>
EOM

print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');

print<<EOM;      

<p></p>
<FORM ACTION=/staff/personnel/orientations.cgi METHOD="POST" name="myform" id="myform">
<p>
<input type="checkbox" name="confirm" id="confirm">
<label for="confirm">Check this box to confirm completion of this checklist item.</label>
</p>

	<ul>
	<INPUT TYPE="HIDDEN" NAME="orientee" VALUE="$orientee">
	<INPUT TYPE="HIDDEN" NAME="orienter" VALUE="$orienter">
	<INPUT TYPE="HIDDEN" NAME="orienttype" VALUE="$orienttype">
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_completion">
	<INPUT TYPE="SUBMIT" VALUE="Submit">
	</form>
	</ul>
<P>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
<P>
$htmltail
EOM

}
#################################################################################
## END: LOCATION = completion
#################################################################################


#################################################################################
## START: LOCATION = process_add_newstaff
#################################################################################
	## READ VARIABLES
	my $new_so_staffid = $query->param("new_so_staffid");
	my $new_so_supervisorid = $query->param("new_so_supervisorid");
	my $new_so_adminid = $query->param("new_so_adminid");
	my $new_so_mentorid = $query->param("new_so_mentorid");
	my $new_startdate_m = $query->param("new_startdate_m");
	my $new_startdate_d = $query->param("new_startdate_d");
	my $new_startdate_y = $query->param("new_startdate_y");

	my $show_admin_functions = "no";
		if (
			($cookie_ss_staff_id eq 'blitke')
			|| ($cookie_ss_staff_id eq 'sliberty')
			|| ($cookie_ss_staff_id eq 'mturner')
			) {
			$show_admin_functions = "yes";
		}
if ($location eq 'process_add_newstaff') {
	## CHECK TO ENSURE STAFF MEMBER IS ALLOWED TO ADD A CHECKLIST
	if ($show_admin_functions ne 'yes') {
		$error_message = "You ($cookie_ss_staff_id) are not authorized to add a new orientation checklist.  Your submission was NOT saved.";
		$location = "add_newstaff";
	} # END IF

	if ( ($new_so_staffid eq '') || ($new_so_supervisorid eq '')) {

		$error_message = "You did not enter a staff member name for the new staff, supervisor, support staff or menoring staff.  Your submission was NOT saved.";
		$location = "add_newstaff";
	} # END IF

	if ( ($new_startdate_m eq '') || ($new_startdate_d eq '') || ($new_startdate_y eq '') ) {

		$error_message = "You did not enter the start date correctly.  Your submission was NOT saved.";
		$location = "add_newstaff";
	} # END IF


	## CHECK IF THE NEW STAFF ALREADY HAS A CHECKLIST ON FILE
	my $command = "select * from staff_orientation WHERE so_staffid LIKE '$new_so_staffid'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_checklists = $sth->rows;
	if (($num_matches_checklists > 0) && ($isedit ne 'yes')) {
 		$error_message = "This new staff member ($new_so_staffid) already has a checklist on file.  You normally see this message if you clicked the \"reload\" button after adding a new checklist. The duplicate submission was NOT saved.";
		$location = "menu";
	}
} # END IF

if ($location eq 'process_add_newstaff') {
	## CHECK IF THIS IS AN ADD OR AN EDIT
	my $add_or_edit = "Add";
#	   $add_or_edit = "Edit" if ($show_event ne '');
	   $add_or_edit = "Edit";

	my $new_startdate = "$new_startdate_y\-$new_startdate_m\-$new_startdate_d";
		$new_startdate = "" if ($new_startdate eq '--');

	## GRAB NON-EDITABLE VARIABLES
#	my $new_entered_by = "";
	
	## CLEAN SPECIAL CHARS BEFORE SAVING TO DB
	$new_so_staffid = &commoncode::cleanthisfordb($new_so_staffid);
	$new_so_supervisorid = &commoncode::cleanthisfordb($new_so_supervisorid);
	$new_so_adminid = &commoncode::cleanthisfordb($new_so_adminid);
	$new_so_mentorid = &commoncode::cleanthisfordb($new_so_mentorid);
	$new_startdate = &commoncode::cleanthisfordb($new_startdate);

	
	## SAVE TO DB
	my $command_addedit = "REPLACE INTO staff_orientation VALUES ('', '$new_so_staffid', '$new_startdate', '$timestamp', 
						'$new_so_supervisorid', '$new_so_adminid', '$new_so_mentorid',
						'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')";
	if ($isedit eq 'yes') {
		$command_addedit = "UPDATE staff_orientation SET so_startdate='$new_startdate', 
						so_supervisorid='$new_so_supervisorid', so_adminid='$new_so_adminid', so_mentorid='$new_so_mentorid' 
						where so_staffid LIKE '$new_so_staffid'";
	}
	
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_addedit) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;

	## SET FEEDBACK MESSAGE
	if ($isedit eq 'yes') {
		$feedback_message .= "You successfully edited the orientation checklist for $new_so_staffid.";
	} else {
		$feedback_message .= "You successfully added the orientation checklist for $new_so_staffid.";
			## ADD e-mail suffix to user IDs
			my $staff_member_full_name = $staff_fullname{$new_so_staffid};
			$new_so_supervisorid = "$new_so_supervisorid\@sedl.org" if ($new_so_supervisorid ne '');
			$new_so_adminid = "$new_so_adminid\@sedl.org" if ($new_so_adminid ne '');
			$new_so_mentorid = "$new_so_mentorid\@sedl.org" if ($new_so_mentorid ne '');
			$new_so_staffid = "$new_so_staffid\@sedl.org";
	
			## SEND AN E-MAIL TO THE STAFF MEMBER AND TO THE ORIENTERS
			&send_email_tostaff($staff_member_full_name, $new_so_staffid, $new_so_supervisorid, $new_so_mentorid, $new_so_adminid);
	} # ED IF/ELSE
	$location = "menu";

}
#################################################################################
## END: LOCATION = process_add_newstaff
#################################################################################



#################################################################################
## START: LOCATION = add_newstaff
#################################################################################
if ($location eq 'add_newstaff') {
	## CHECK TO ENSURE STAFF MEMBER IS ALLOWED TO ADD A CHECKLIST
	if ($show_admin_functions ne 'yes') {
		$error_message = "You ($cookie_ss_staff_id) are not authorized to add a new orientation checklist, so any data you submit will NOT be saved.";
		$location = "menu";
	} # END IF
}

my $previous_so_startdate = "";
my $previous_so_supervisorid = "";
my $previous_so_adminid = "";
my $previous_so_mentorid = "";

if ($location eq 'add_newstaff') {
	my $add_or_edit = "Add";
		if ($isedit eq 'yes') {
			$add_or_edit = "Edit";
	
			## CHECK IF THE NEW STAFF ALREADY HAS A CHECKLIST ON FILE
			my $command = "select so_startdate , so_supervisorid, so_adminid, so_mentorid from staff_orientation WHERE so_staffid LIKE '$editrecord'";
			my $dsn = "DBI:mysql:database=intranet;host=localhost";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches_checklists = $sth->rows;
				while (my @arr = $sth->fetchrow) {
					($previous_so_startdate, $previous_so_supervisorid, $previous_so_adminid, $previous_so_mentorid) = @arr;
				} # END DB QUERY LOOP
		}
print<<EOM;
<HTML>
<HEAD><TITLE>SEDL Intranet - Staff Orientation Checklist: Add New Staff Member Checklist</TITLE>
$htmlhead
<form action="orientations.cgi" method=POST>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td valign="top"><h1><a href="orientations.cgi">Staff Orientation Checklist</a>:<br>
		$add_or_edit New Staff Member Orientation Checklist</h1></td>
	<td align="right" valign="top">(Click here to <A HREF="orientations.cgi?location=logout">logout</A>)
</td></tr>
</table>
EOM

print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');

print<<EOM;      

<p></p>
<FORM ACTION=/staff/personnel/orientations.cgi METHOD="POST" name="myform" id="myform">
<table border="1" cellpadding="4" cellspacing="0">
<tr><td valign="top"><label for="new_so_staffid">New Staff Member Name</label></td>
	<td valign="top">
EOM
&printform_stafflist('new_so_staffid', $editrecord);
print<<EOM;
		<br>If the staff member's name does not appear in this list, the person needs to be added to SIMS before you can proceed.
	</td></tr>
<tr><td valign="top">Staff Member's Start Date</td>
	<td valign="top">
EOM
my $startdate = "";
		my ($old_y, $old_m, $old_d) = split(/\-/,$previous_so_startdate );
		&commoncode::print_month_menu("new_startdate_m", $old_m);
		&commoncode::print_day_menu("new_startdate_d", $old_d);
		&commoncode::print_year_menu("new_startdate_y", $year - 2, $year + 2, $old_y);

print<<EOM;
	</td></tr>
<tr><td valign="top"><label for="new_so_supervisorid">Supervisor for Orientation</label></td>
	<td valign="top">
EOM
&printform_stafflist('new_so_supervisorid', $previous_so_supervisorid);
print<<EOM;
	</td></tr>
<tr><td valign="top"><label for="new_so_adminid">Support Staff for Orientation</label></td>
	<td valign="top">
EOM
&printform_stafflist('new_so_adminid', $previous_so_adminid);
print<<EOM;
	</td></tr>
<tr><td valign="top"><label for="new_so_mentorid">Mentoring Staff for Orientation</label></td>
	<td valign="top">
EOM
&printform_stafflist('new_so_mentorid', $previous_so_mentorid);
print<<EOM;
	</td></tr>
</table>
	<div style="margin-left:25px;">
	<INPUT TYPE="HIDDEN" NAME="isedit" VALUE="$isedit">
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_add_newstaff">
	<INPUT TYPE="SUBMIT" VALUE="Submit">
	</form>
	</div>
<p>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
$htmltail
EOM

}
#################################################################################
## END: LOCATION = add_newstaff
#################################################################################


#################################################################################
## START: LOCATION = menu
#################################################################################
if ($location eq 'menu') {
	## SEE IF THIS USER IS IN THE STAFF DATABASE
	my $command = "select * from staff_orientation where so_staffid LIKE '$cookie_ss_staff_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_this_staff_member = $sth->rows;




	## GRAB ALL STAFF MEMBER'S FULL NAME AND PUT INTO A HASH FOR EASY ACCESS
	my %staff_fullname;
	my $command = "select userid, firstname, lastname from staff_profiles";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
	    my ($userid, $firstname, $lastname) = @arr;
			$staff_fullname{$userid} = "$firstname $lastname";
		} # END DB QUERY LOOP
	
	
#my $prettyname = &get_staff_fullname($cookie_ss_staff_id);

## PRINT SIGNUP FORM
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Intranet - Staff Orientation Checklist: Main Menu</TITLE>
$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td valign="top"><h1><a href="orientations.cgi">Staff Orientation Checklist</a>: Main Menu</h1></td>
	<td align="right" valign="top">(Click here to <A HREF="orientations.cgi?location=logout">logout</A>)
</td></tr>
</table>
EOM

print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
	my $command = "select * from staff_orientation";
	# WHERE so_startdate > '$one_year_ago_mysql'
#	   $command .= " order by so_startdate DESC" if ($sortby eq '');
		if ($num_matches_this_staff_member == 1 ) {
			$command .= " WHERE so_staffid LIKE '$cookie_ss_staff_id'";
		}


	   $command .= " order by so_startdate DESC";

	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_checklists = $sth->rows;
my $isare = "are";
   $isare = "is" if ($num_matches_checklists == 1);
my $s = "s";
   $s = "" if ($num_matches_checklists == 1);
print<<EOM;
<P>There $isare $num_matches_checklists orientation checklist$s on file. Click here to view a <a href="/staff/video/orientation_checklist.mov">walkthrough video</a>.<br> 
Click here to <A href="orientations.cgi?location=add_newstaff">add a new staff checklist</a>.
</p>

<table border="1" cellpadding="2" cellspacing="0" style="background:#ffffff;">
EOM
&print_table_column_headings();

sub print_table_column_headings {
print<<EOM;
<tr style="background-color:#cccccc;font-size:9px;">
	<td><strong>Staff Member</strong></td>
	<td style="text-align:center;"><strong>Responsible party</strong></td>
	<td valign="top" style="font-size:9px;text-align:center;"><strong>1.<br><a href="/cgi-bin/mysql/staff/index.cgi?page=supervisor_orientation">Super-<br>visor</a></strong></td>
	<td valign="top" style="font-size:9px;text-align:center;"><strong>2.<br><a href="http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?page=sedls_principles_for_mentoring">Mentor</a></strong></td>
	<td valign="top" style="font-size:9px;text-align:center;"><strong>3.<br><a href="http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?page=executive_office_orientation">Executive<br>Office</a></strong></td>
	<td valign="top" style="font-size:9px;text-align:center;"><strong>4.<br><a href="http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?page=development_department_orientation">Devel-<br>opment</a></strong></td>
	<td valign="top" style="font-size:9px;text-align:center;"><strong>5.<br><a href="http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?page=administrative_services_orientation">Admin-<br>istrative<br>Services</a></strong></td>
	<td valign="top" style="font-size:9px;text-align:center;"><strong>6.<br><a href="http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?page=information_resource_center_orientation">Information<br>Resource<br>Center</a></strong></td>
	<td valign="top" style="font-size:9px;text-align:center;"><strong>7.<br><a href="http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?page=communications_office_orientation">Commun-<br>ications</a></strong></td>
	<td valign="top" style="font-size:9px;text-align:center;"><strong>8.<br><a href="http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?page=support_staff_orientation_checklist">Support<br>Staff</a></strong></td>
	<td valign="top" style="font-size:9px;text-align:center;"><strong>9.<br><a href="http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?page=human_resources_orientation_checklist">Human<br>Resources</a></strong></td>
	<td valign="top" style="font-size:9px;"><strong>Last<br>Updated</strong></td>
</tr>
EOM
}
my $counter = 1;
	while (my @arr = $sth->fetchrow) {
		my ($so_uniqueid, $so_staffid, $so_startdate, $so_lastupdated, $so_supervisorid, $so_adminid, $so_mentorid, 
			$so_comp_supervisor, $so_comp_mentoring, $so_comp_eo, $so_comp_dev, $so_comp_as, $so_comp_irc, $so_comp_comm, $so_comp_support, $so_comp_hr, 
			$so_staff_supervisor, $so_staff_mentoring, $so_staff_eo, $so_staff_dev, $so_staff_as, $so_staff_irc, $so_staff_comm, $so_staff_support, $so_staff_hr) = @arr;
			
			$so_startdate = &commoncode::date2standard($so_startdate);  ## GET PRETTY VERSION OF DATE
			my $staff_full_name = $staff_fullname{$so_staffid};
	   		   $staff_full_name = $so_staffid if ($staff_full_name eq '');
			my $staff_first_name = $staff_firstname{$so_staffid};
	   		   $staff_first_name = $so_staffid if ($staff_first_name eq '');
			if ($staff_first_name eq '') {
				$staff_first_name = $so_staffid;
				$staff_full_name = $so_staffid;
			}

if (($counter == 5) || ($counter == 10) || ($counter == 15) || ($counter == 20) || ($counter == 25) || ($counter == 30) || ($counter == 35) || ($counter == 40)) {
	&print_table_column_headings();
}
$counter++;

my $bgcolor = "";
#   $bgcolor = "BGCOLOR=\"#E8FFE8\"" if ($entered_by eq $cookie_ss_staff_id);
   $bgcolor = "BGCOLOR=\"#FFFFCC\"" if ($so_staffid eq $cookie_ss_staff_id);

my $c1_e = "<img src=\"/staff/images/checkbox.gif\" alt=\"\">";

print<<EOM;
<tr $bgcolor><a name="$so_staffid"></a>
	<td valign="top" rowspan="2" style="border-top-width:3px;border-top-style:solid;border-top-color:#333333;">
EOM
	if ($show_admin_functions eq "yes") {
		print "<a href=\"orientations.cgi?location=add_newstaff&amp;isedit=yes&amp;editrecord=$so_staffid\">";
	}
print "<strong>$staff_full_name</strong>";
	if ($show_admin_functions eq "yes") {
		print "</a>";
	}
print<<EOM;
		<br>
		<span style="font-size:10px;color:#999999;">Start date: $so_startdate</span></td>
	<td align="right" style="border-top-width:3px;border-top-style:solid;border-top-color:#333333;">$staff_first_name indicates completion:</td>
EOM

my $this_so_supervisorid = $staff_fullname{$so_supervisorid};
   $this_so_supervisorid = "$so_supervisorid" if ($this_so_supervisorid eq '');
my $this_so_mentorid = $staff_fullname{$so_mentorid};
   $this_so_mentorid = "$so_supervisorid" if ($this_so_mentorid eq '');
my $this_so_adminid = $staff_fullname{$so_adminid};
   $this_so_adminid = "$so_adminid" if ($this_so_adminid eq '');


	&cell_with_checkbox($so_staff_supervisor, "so_staff_supervisor", $so_staffid, $so_supervisorid, $this_so_supervisorid);
	&cell_with_checkbox($so_staff_mentoring, "so_staff_mentoring", $so_staffid, $so_mentorid, $this_so_mentorid);
	&cell_with_checkbox($so_staff_eo, "so_staff_eo", $so_staffid, 'whoover', $staff_fullname{'whoover'});
	&cell_with_checkbox($so_staff_dev, "so_staff_dev", $so_staffid, 'mboethel', $staff_fullname{'mboethel'});
	&cell_with_checkbox($so_staff_as, "so_staff_as", $so_staffid, 'sferguso', $staff_fullname{'sferguso'});
	&cell_with_checkbox($so_staff_irc, "so_staff_irc", $so_staffid, 'nreynold', $staff_fullname{'nreynold'});
	&cell_with_checkbox($so_staff_comm, "so_staff_comm", $so_staffid, 'cmoses', $staff_fullname{'cmoses'});
	&cell_with_checkbox($so_staff_support, "so_staff_support", $so_staffid, $so_adminid, $this_so_adminid);
	&cell_with_checkbox($so_staff_hr, "so_staff_hr", $so_staffid, 'sliberty', $staff_fullname{'sliberty'});

	$so_lastupdated = &commoncode::convert_timestamp_2pretty_w_date($so_lastupdated, "yes");
	$so_lastupdated =~ s/ /\<br\>/gi;
print<<EOM;
	<td valign="top" rowspan="2" style="font-size:9px;border-top-width:3px;border-top-style:solid;border-top-color:#333333;">$so_lastupdated</td>
</tr>
<tr $bgcolor>
	<td align="right">Orienter:</td>
EOM
	&cell_with_checkbox($so_comp_supervisor, "so_comp_supervisor", $so_staffid, $so_supervisorid, $this_so_supervisorid, $so_staff_supervisor);
	&cell_with_checkbox($so_comp_mentoring, "so_comp_mentoring", $so_staffid, $so_mentorid, $staff_fullname{$so_mentorid}, $so_staff_mentoring);
	&cell_with_checkbox($so_comp_eo, "so_comp_eo", $so_staffid, 'whoover', $staff_fullname{'whoover'}, $so_staff_eo);
	&cell_with_checkbox($so_comp_dev, "so_comp_dev", $so_staffid, 'mboethel', $staff_fullname{'mboethel'}, $so_staff_dev);
	&cell_with_checkbox($so_comp_as, "so_comp_as", $so_staffid, 'sferguso', $staff_fullname{'sferguso'}, $so_staff_as);
	&cell_with_checkbox($so_comp_irc, "so_comp_irc", $so_staffid, 'nreynold', $staff_fullname{'nreynold'}, $so_staff_irc);
	&cell_with_checkbox($so_comp_comm, "so_comp_comm", $so_staffid, 'cmoses', $staff_fullname{'cmoses'}, $so_staff_comm);
	&cell_with_checkbox($so_comp_support, "so_comp_support", $so_staffid, $so_adminid, $this_so_adminid, $so_staff_support);
	&cell_with_checkbox($so_comp_hr, "so_comp_hr", $so_staffid, 'sliberty', $staff_fullname{'sliberty'}, $so_staff_hr);
print<<EOM;
</tr>

EOM

	} # END DB QUERY LOOP


print<<EOM;
</table>
<p>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
$htmltail
EOM
} 
#################################################################################
## END: LOCATION = menu
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


#####################################################################
## START: subroutine get_orientation_fullname
#####################################################################
sub get_orientation_fullname {
	my $orientation_id = $_[0];
	my $orientation_fullname = "";
		$orientation_fullname = "Supervisor Orientation" if ($orientation_id =~ '_supervisor');
		$orientation_fullname = "Principles for Mentoring New Staff" if ($orientation_id =~ '_mentoring');
		$orientation_fullname = "Executive Office Orientation" if ($orientation_id =~ '_eo');
		$orientation_fullname = "Development Department Orientation" if ($orientation_id =~ '_dev');
		$orientation_fullname = "Administrative Services Orientation" if ($orientation_id =~ '_as');
		$orientation_fullname = "Information Resource Center Orientation" if ($orientation_id =~ '_irc');
		$orientation_fullname = "Communications Department Orientation" if ($orientation_id =~ '_comm');
		$orientation_fullname = "Support Staff Orientation Checklist" if ($orientation_id =~ '_support');
		$orientation_fullname = "Human Resources Orientation Checklist" if ($orientation_id =~ '_hr');

	return($orientation_fullname);
}
#####################################################################
## END: subroutine get_orientation_fullname
#####################################################################


#####################################################################
## START: subroutine lookup_orienter
#####################################################################
sub lookup_orienter {
	my $this_orientee = $_[0];
	my $this_orientation_item = $_[1];
	my $this_orienter = "error_orienter_notfound";
	## if not the supervisor, mentor, or support staff, set orienter ID
	$this_orienter = "whoover" if ($this_orientation_item eq 'so_comp_eo');
	$this_orienter = "mboethel" if ($this_orientation_item eq 'so_comp_dev');
	$this_orienter = "sferguso" if ($this_orientation_item eq 'so_comp_as');
	$this_orienter = "nreynold" if ($this_orientation_item eq 'so_comp_irc');
	$this_orienter = "cmoses" if ($this_orientation_item eq 'so_comp_comm');
	$this_orienter = "sliberty" if ($this_orientation_item eq 'so_comp_hr');

	## if supervisor, mentor, or support staff, look it up
	if (($this_orientation_item eq 'so_comp_supervisor') || ($this_orientation_item eq 'so_comp_mentoring') || ($this_orientation_item eq 'so_comp_support')) {
		my $command = "select so_supervisorid, so_adminid, so_mentorid from staff_orientation WHERE so_staffid = '$this_orientee'";
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		while (my @arr = $sth->fetchrow) {
			my ($so_supervisorid, $so_adminid, $so_mentorid) = @arr;
			$this_orienter = $so_supervisorid if ($this_orientation_item eq 'so_comp_supervisor');
			$this_orienter = $so_adminid if ($this_orientation_item eq 'so_comp_support');
			$this_orienter = $so_mentorid if ($this_orientation_item eq 'so_comp_mentoring');
		} # END DB QUERY LOOP
	} # END IF
	return($this_orienter);
}
#####################################################################
## END: subroutine lookup_orienter
#####################################################################


####################################################################
## START: SUBROUTINE: cell_with_checkbox
####################################################################
sub cell_with_checkbox {
	my $completion_data = $_[0];
	my $completion_field = $_[1];
	my $completion_forstaffid = $_[2];
	my $completion_orienter = $_[3];
	my $completion_orienter_fullname = $_[4];
	my $completion_data_staff_confirmation = $_[5];
	my ($completedby, $completeddate) = split(/\;/,$completion_data);
	my ($completedby_staff_confirmation, $completeddate_staff_confirmation) = split(/\;/,$completion_data_staff_confirmation);

	my $checkbox_image = "/staff/images/checkbox.jpg";
	   $checkbox_image = "/staff/images/checkbox_checked.jpg" if ($completion_data ne '');
	my $checkbox_alt = "incomplete";
	   $checkbox_alt .= ": orienter is $completion_orienter_fullname" if ($completion_field !~ 'staff');
		if ($completion_data ne '') {
			$completeddate = &commoncode::date2standard($completeddate);
			$checkbox_alt = "completed by $completedby on $completeddate";
		}
## open table cell
if ($completion_field !~ '_comp_') {
	print "<td style=\"width:35px;border-top-width:3px;border-top-style:solid;border-top-color:#333333;\" align=\"center\">";
} else {
	print "<td align=\"center\">";
}
## START: print the checkbox icon
if ($completion_field =~ '_staff_') {
	if ($completion_data eq '') {
		print "<a href=\"orientations.cgi?location=completion&amp;orientee=$completion_forstaffid&amp;orienter=$completion_orienter&amp;orienttype=$completion_field\">";
	}
	
	print "<img src=\"$checkbox_image\" alt=\"$checkbox_alt\" title=\"$checkbox_alt\" border=\"0\">";
	
	if ($completion_data eq '') {
		print "</a>";
	}
	print "<br>";
}
## END: print the checkbox icon

## START: print name of person responsible
my $this_completion_orienter = $staff_fullname{$completion_orienter};
   $this_completion_orienter = $completion_orienter if ($this_completion_orienter eq '');
if ($completion_field =~ 'comp') {
	if (($completion_orienter eq $cookie_ss_staff_id) && ($completeddate_staff_confirmation eq '')) {
		$completion_orienter = "<span style=\"color:#cc0000;background-color:yellow;\">$this_completion_orienter</span>";
	} elsif (($completion_orienter eq $cookie_ss_staff_id) && ($completeddate_staff_confirmation ne '')) {
		$completion_orienter = "<span style=\"color:#11861c;background-color:#acfbac;\">$this_completion_orienter</span>";
	} else {
		$completion_orienter = "$this_completion_orienter";
	}
	print "$completion_orienter";
} elsif (($completion_data eq '') && ($completion_field =~ 'staff')) {
#	$completion_forstaffid = "<span style=\"color:#cc0000;\">$completion_forstaffid</span>" if ($completion_forstaffid eq $cookie_ss_staff_id);
#	print "$completion_forstaffid";
}
## END: print name of person responsible

## close table cell
print "</td>";

} # END subroutine cell_with_checkbox
####################################################################
## END: SUBROUTINE: cell_with_checkbox
####################################################################


####################################################################
## START: SUBROUTINE: printform_stafflist
####################################################################
sub printform_stafflist {
	my $form_variable_name = $_[0];
	my $previous_selection = $_[1];

print<<EOM;
<select name="$form_variable_name" id="$form_variable_name">
<option value=""></option>
EOM
		my $command = "select userid, firstname, lastname from staff_profiles order by firstname, lastname";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
			while (my @arr = $sth->fetchrow) {
			    my ($userid, $firstname, $lastname) = @arr;
			    print "<option value=\"$userid\"";
				if ($previous_selection eq $userid) {
					print " SELECTED";
				}
			    print ">$firstname $lastname</option>";
			}
print<<EOM;
</select>
EOM

} # END subroutine printform_stafflist
####################################################################
## END: SUBROUTINE: printform_stafflist
####################################################################


####################################################################
## START: SUBROUTINE: SEND_EMAILTOSTAFF
####################################################################
sub send_email_tostaff {
	my $staff_member_full_name = $_[0];
	my $staff_member_email = $_[1];
	my $assigned_manager = $_[2];
	my $assigned_mentor = $_[3];
	my $assigned_admin = $_[4];

# E-MAIL SERVER VARIABLES
my $mailprog = '/usr/sbin/sendmail -t';
my $fromaddr = 'webmaster@sedl.org';

my $orienters = 'sue.liberty@sedl.org, sandy.rodriguez@sedl.org, martha.boethel@sedl.org, stuart.ferguson@sedl.org, nancy.reynolds@sedl.org, christine.moses@sedl.org';
   $orienters .= ", $assigned_manager" if ($assigned_manager ne '');
   $orienters .= ", $assigned_mentor" if ($assigned_mentor ne '');
   $orienters .= ", $assigned_admin" if ($assigned_admin ne '');
   
# START: ONLY SEND IF E-MAIL IS VALID
open(NOTIFY,"| $mailprog");

print NOTIFY <<EOM;
From: SEDL Orientation Checklist <$fromaddr>
To: $staff_member_full_name <$staff_member_email>
Cc: $orienters
Reply-To: SEDL Orientation Checklist <$fromaddr>
Errors-To: SEDL Orientation Checklist <$fromaddr>
Sender: SEDL Orientation Checklist <$fromaddr>
Subject: New Staff Member Orientation Checklist Created

Dear SEDL Staff Member and Staff Members Responsible for Orientations,
 
Sue Liberty has created a new orientation checklist.

Please meet with the people indicated on your checklist as orienters to schedule the nine orientations within your first three months of employment.


STAFF MEMBER:
You will indicate when each orientation has been completed by visiting the online orientation checklist at:
http://www.sedl.org/staff/personnel/orientations.cgi
and checkmarking the appropriate box for the orientation that was completed.


ORIENTERS:
You will receive an automated confirmation e-mail when the staff member indicates the orientation has been completed. If the orientation has not happened, please contact the staff member to schedule the orientation.


This is an automated message from the SEDL Orientation Checklist at 
http://www.sedl.org/staff/personnel/orientations.cgi

If you have questions or need assistance, please contact Sue Liberty at ext. 6528.
EOM
	close(NOTIFY);

}
####################################################################
## END SUBROUTINE: SEND_EMAILTOSTAFF
####################################################################


####################################################################
## START: SUBROUTINE: SEND_EMAILTOADMIN
####################################################################
sub send_email_toadmin {
	my $message_subject = $_[0];
	my $message_body = $_[1];
	my $cc_to_orienter = $_[2];

# E-MAIL SERVER VARIABLES
my $mailprog = '/usr/sbin/sendmail -t';
my $fromaddr = 'webmaster@sedl.org';
my $toaddr = 'brian.litke@sedl.org'; # for testing
   $toaddr = 'sue.liberty@sedl.org'; # for live use


# START: ONLY SEND IF E-MAIL IS VALID
open(NOTIFY,"| $mailprog");

print NOTIFY <<EOM;
From: SEDL Orientation Checklist <$fromaddr>
To: Sue Liberty <$toaddr>
Cc: $cc_to_orienter
Reply-To: SEDL Orientation Checklist <$fromaddr>
Errors-To: SEDL Orientation Checklist <$fromaddr>
Sender: SEDL Orientation Checklist <$fromaddr>
Subject: $message_subject

Dear Sue and Staff Member Responsible for a Staff Orientation,
 
$message_body

This is an automated message from the SEDL Orientation Checklist at 
http://www.sedl.org/staff/personnel/orientations.cgi

If you have questions or need assistance, please contact Brian Litke at ext. 6529.
EOM
	close(NOTIFY);

}
####################################################################
## END SUBROUTINE: SEND_EMAILTOADMIN
####################################################################
# 		&send_email_toadmin($message_subject, $message_body);

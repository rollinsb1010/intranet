#!/usr/bin/perl 

################################################################################
# Copyright 2004 by Southwest Educational Development Laboratory
# Written by Brian Litke, SEDL Web Administrator (07-19-2006)
#
# This script is used to brainstorm words to use in SEDL's logo redevelopment
################################################################################

##########################
##  SET SCRIPT HANDLERS ## 
##########################

#use diagnostics;
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

#############################################
## START: LOAD PERL MODULES
#############################################
## THIS IS A PERL MODULE THAT FORMATS NUMBERS
use Number::Format;
# EXAMPLE OF USAGE
# my $this_number
#	my $x = new Number::Format;
#	$this_number = $x->format_number($this_number, 2, 2);
#############################################
## END: LOAD PERL MODULES
#############################################

my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
my $query = new CGI;


##############################
## START: GRAB PAGE TEMPLATE #
##############################
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

$htmlhead .= "<TABLE CELLPADDING=\"15\"><TR><TD>";
$htmltail = "</td></tr></table>$htmltail";



##############################
## END: GRAB PAGE TEMPLATE #
##############################

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


########################################################
## START: GET VARIABLES FROM FORM
########################################################
my $session_active = "no";
my $error_message = "";
my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $logon_pass = $query->param("logon_pass");

my $uniqueid = param('uniqueid');

my $location = $query->param("location");
	$location = "logon" if ($location eq '');


my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};
my $browser = $ENV{"HTTP_USER_AGENT"};

## GRAB BRAINSTORM VARIABLES
my $new_w1 = $query->param("new_w1");
my $new_w2 = $query->param("new_w2");
my $new_w3 = $query->param("new_w3");
my $new_w4 = $query->param("new_w4");
my $new_w5 = $query->param("new_w5");

my $new_tag1 = $query->param("new_tag1");
my $new_tag2 = $query->param("new_tag2");
my $new_name = $query->param("new_name");
my $new_name_sugg1 = $query->param("new_name_sugg1");
my $new_name_sugg2 = $query->param("new_name_sugg2");


if ($new_name ne 'change part of name') {
	$new_name_sugg1 = "";
}

if ($new_name ne 'change name completely') {
	$new_name_sugg2 = "";
}

my $survey_admin = "no";
	if (($logon_user eq 'blitke') || ($logon_user eq 'jpollard')) {
		$survey_admin = "yes";
	}
## REMOVE TABS AND CARRIAGE RETURNS FROM USER-ENTERED DATA USING "CLEANTHIS" SUBROUTINE
#$first_name = &cleanthis ($first_name);

########################################################
## END: GET VARIABLES FROM FORM
########################################################


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
				$location = "showform";

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
#	my $num_matches = $sth->rows;
		
			## SAVE COOKIES WITH SESSION ID AND USER ID
				setCookie ("ss_session_id", $ss_session_id, $expdate, $path, $thedomain);
				setCookie ("staffid", $ss_staff_id, $expdate, $path, $thedomain);
				$logon_user = $ss_staff_id;

		} # END DB QUERY LOOP

		## IF SESSION NOT ACTIVE, PROMPT FOR LOGON
		if ($num_matches eq '0') {
			$cookie_ss_session_id = "";
			setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
			$location = "logon"; # AFTER LOGOUT, SHOW LOGON SCREEN
		} else {
			$session_active = "yes";
		}
	}
######################################################
## END: CHECK SESSION ID AND VERIFY
######################################################





################################
## START PAGE HEADER
################################
print header;

print <<EOM;
<HTML>
<head>
<title>SEDL Brainstorming Exercise for Logo Development</title>
$htmlhead
EOM

   

####################################################################
####################################################################
## START: IF LOCATION = ENTERRECORD
####################################################################
####################################################################
if ($location eq 'enterrecord') {

		## START: SET ERROR MESSAGE FOR MISSING VARIABLES
		$error_message .= ", word 1" if ($new_w1 eq '');
		$error_message .= ", word 2" if ($new_w2 eq '');
		$error_message .= ", word 3" if ($new_w3 eq '');
		$error_message .= ", word 4" if ($new_w4 eq '');
		$error_message .= ", word 5" if ($new_w5 eq '');

		$error_message .= ", tagline 1" if ($new_tag1 eq '');
		$error_message .= ", tagline 2" if ($new_tag2 eq '');
		$error_message .= ", company name option" if ($new_name eq '');
		$error_message .= ", company name change suggestion" if (($new_name eq 'change part of name') && ($new_name_sugg1 eq ''));
		$error_message .= ", company name change suggestion" if (($new_name eq 'change name completely') && ($new_name_sugg2 eq ''));

		if ($error_message ne '') {
			$error_message = "QQQ$error_message";
			$error_message =~ s/QQQ\, //g;
			$error_message = "<FONT COLOR=RED>You forgot to answer all the blanks: $error_message.  Please fill in the missing data.</FONT>";

		}
		## END: SET ERROR MESSAGE FOR MISSING VARIABLES



	if ($error_message ne '') {
		##############################################
		## START OF ACTIONS IF USER SUBMITS BAD ENTRY
		##############################################
print <<EOM;
<CENTER><H1>Error</H1></CENTER>
<P>
$error_message  Please use the "back" button on your browser and try again. 
EOM
		##############################################
		## END OF ACTIONS IF USER SUBMITS BAD ENTRY
		##############################################

	} else {




##################################################
##################################################
## START OF ACTIONS IF USER SUBMITS GOOD ENTRY
##################################################
##################################################


print <<EOM;
<H1 ALIGN=CENTER>Thank You</H1>
<P>
Thank you for participating in the brainstorm activity. We appreciate your time and comments, and we will share the results with you 
soon after the activity ends.
<P>
- Communications staff
<P>
EOM

## SET MAIL NOTIFICATION VARIABLES
my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
my $recipient = '';

############################# START OF EMAIL TO OIC #############################
## WRITE THE SURVEY RESULTS TO AN E-MAIL
my $fromaddr = 'webmaster@sedl.org';
   $recipient = 'blitke@sedl.org';

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Data from 2006 Brainstorm Survey

The following data was received from the 2006 Brainstorm Survey at:
http://www.sedl.org/staff/brainstorm/brainstorm.cgi


Brainstorm:
===========================
Survey Date: $todaysdate

Word 1: $new_w1
Word 2: $new_w2
Word 3: $new_w3
Word 4: $new_w4
Word 5: $new_w5

Tagline 1: 
$new_tag1

Tagline 2: 
$new_tag2

Company Name: 
Suggested change: $new_name
 - Change name to: $new_name_sugg1 $new_name_sugg2


User Stats:
-----------
Web Browser software: $browser
IP Number: $ipnum2
Domain: $ipnum

EOM
close(NOTIFY);

############################# END OF EMAIL TO OIC INFORMATION SERVICES ############################
	}  ## END OF ACTIONS IF USER SUBMITS GOOD ENTRY



## BACKSLASH TEXT FIELDS
$new_w1 = backslash_fordb($new_w1);
$new_w2 = backslash_fordb($new_w2);
$new_w3 = backslash_fordb($new_w3);
$new_w4 = backslash_fordb($new_w4);
$new_w5 = backslash_fordb($new_w5);

$new_tag1 = backslash_fordb($new_tag1);
$new_tag2 = backslash_fordb($new_tag2);
$new_name = backslash_fordb($new_name);
$new_name_sugg1 = backslash_fordb($new_name_sugg1);
$new_name_sugg2 = backslash_fordb($new_name_sugg2);

## SAVE DATA TO DATABASE
my $command = "REPLACE INTO brainstorm VALUES ('$logon_user', '$new_w1', '$new_w2', '$new_w3', '$new_w4', '$new_w5', '$new_tag1', '$new_tag2', '$new_name', '$new_name_sugg1', '$new_name_sugg2', '$timestamp')";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
#	my $num_matches = $sth->rows;


print "<P>Click here to <A HREF=\"brainstorm.cgi\">edit your entry</A>.";
	if ($survey_admin eq 'yes') {
		&show_results();
	}

}
####################################################################
####################################################################
## END: IF LOCATION = ENTERRECORD
####################################################################
####################################################################




######################################################################################
## START: LOCATION = LOGON
######################################################################################
if (($session_active eq 'yes') && ($location eq 'logon')) {
	$location = "showform";
}

if ($location eq 'logon') {

print <<EOM;
<h1>SEDL Brainstorming Tool

EOM
print "<SPAN class=small>(Click here to <A HREF=\"resource_search.cgi?location=logout\">log out</A>)</SPAN>" if ($session_active eq 'yes');
print<<EOM;
</H1>
<H2 ALIGN=CENTER>Please Log On</H2>
EOM


print<<EOM;
$error_message<P>
Please enter your SEDL staff ID and password.  Your responses will NOT be linked 
to your name; although Communications will track which staff members have completed the brainstorming activity, 
so we can contact those who don't to remind them to provide us with their feedback.
<P>
<form action="brainstorm.cgi" method="POST">
<H4>Your Information </H4>
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
  <input type="submit" name="submit" value="Click to Proceed to the Brainstorming Activity">
  </div>
  </form>
EOM
}
######################################################################################
## END: LOCATION = LOGON
######################################################################################






######################################################################################
## START: LOCATION = SHOWFORM
######################################################################################
if ($location eq 'showform') {
	## DECLARE VARIABLES TO HOLD THIS USER'S PREVIOUS ENTRIES
	my $staff_id ="";
	my $w1 ="";
	my $w2 ="";
	my $w3 ="";
	my $w4 ="";
	my $w5 ="";
	my $tag1 ="";
	my $tag2 ="";
	my $name ="";
	my $name_sugg1 ="";
	my $name_sugg2 ="";
	my $date_entered = "";

	## LOOK UP PREVIOUS ENTRY
	my $command = "select * from brainstorm where staff_id LIKE '$logon_user'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_previous_entry = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			($staff_id , $w1, $w2, $w3, $w4, $w5, $tag1, $tag2, $name, $name_sugg1, $name_sugg2, $date_entered) = @arr;
			}
my $previos_record_message = "";
if ($num_matches_previous_entry eq '0') {
	$previos_record_message = "<font color=red>You have not yet submitted your brainstorm</font>";
} else {
	$previos_record_message = "<font color=green>Your previous brainstorm entries were loaded for editing. You may click the \"Send Brainstorm\" button to save any revisions you make, or you may quit anytime and your previous answers will remain on file.</font>";
}
my $selected_a = "";
my $selected_b = "";
my $selected_c = "";
my $selected_d = "";
   $selected_a = " CHECKED" if ($name eq 'do not change name');
   $selected_b = " CHECKED" if ($name eq 'change part of name');
   $selected_c = " CHECKED" if ($name eq 'use acronym');
   $selected_d = " CHECKED" if ($name eq 'change name completely');
 
print <<EOM;
<script language="JavaScript">
<!--
function checkFields() { 
	if (document.form2.new_w1.value == "") {
		alert("You forgot to enter word #1.");
		return false;
	}
	if (document.form2.new_w2.value == "") {
		alert("You forgot to enter word #2.");
		return false;
	}
	if (document.form2.new_w3.value == "") {
		alert("You forgot to enter word #3.");
		return false;
	}
	if (document.form2.new_w4.value == "") {
		alert("You forgot to enter word #4.");
		return false;
	}
	if (document.form2.new_w5.value == "") {
		alert("You forgot to enter word #5.");
		return false;
	}

	if (document.form2.new_tag1.value == "") {
		alert("You forgot to enter tagline #1.");
		return false;
	}
	if (document.form2.new_tag2.value == "") {
		alert("You forgot to enter tagline #2.");
		return false;
	}

	// Question name - RADIO
	var user_input = 0;
	for (i=0;i < 4;i++) {
		if (document.form2.new_name[i].checked == true) {
			user_input++;
		}
	}
	if (user_input < 1) {
		alert("You forgot to suggest an option for keeping or changing the SEDL name.");
		return false;
	}

		if ((document.form2.new_name[1].checked == true)
				&& (document.form2.new_name_sugg1.value == ""))
		{
			alert("You selected \'Cange Part of the Name,\' but you did not enter a suggested name.");
			return false;
		}

		if ((document.form2.new_name[3].checked == true)
				&& (document.form2.new_name_sugg2.value == ""))
		{
			alert("You selected the option \'Change the Name Completely,\' but you did not enter a suggested name.");
			return false;
		}




}	
// -->
</script>

<h1>SEDL Brainstorming Tool
EOM
print "<SPAN class=small>(Click here to <A HREF=\"brainstorm.cgi?location=logout\">log out</A>)</SPAN>" if ($session_active eq 'yes');
print<<EOM;
</h1>
<IMG SRC="/common/images/brainstorm.gif" ALIGN="RIGHT" WIDTH="180">
<P>
$previos_record_message
<form action="brainstorm.cgi" method=POST id="form2" name="form2" onsubmit="return checkFields()">
<P>
<H2>Goal of the Brainstorming Activity:</H2>
<P>
As SEDL prepares to move into a new headquarters building that will support our collaborative work style, we want to take advantage of that momentum and change to update the  corporate logo. Communications is leading a logo redevelopment project, and we are asking for your input because we value your ideas as a member of the company. 
<P>
In the coming weeks, we will ask for your input several times. We will share it with a staff advisory working group and an environmental designer to refine options for a new logo and how it communicates SEDL's name, values, mission, work, and related messages. 
<P>
This brainstorming activity is the first step. In the spirit of brainstorming submit your brainstorm quickly without conferring with anyone else.
<P>
<H2>Context for the Brainstorm:</H2>
<P>
Imagine that we are in an ideal world where funding is not an issue in determining the RD&D work SEDL selects to pursue. Please comment from your personal perspective as a professional working at SEDL. Imagine that you are working in the position at SEDL you would most like to be working in, doing the kind of work you feel SEDL should be engaged in.
<br>



<H2>Part 1: Key Words</H2>

In part one of the brainstorming exercise, please start by reflecting on the company name, logo, and tagline.
	<UL>
	<TABLE>
	<TR><TD><IMG SRC="/common/images/SEDL-logo.jpg" ALT="SEDL Logo"></TD>
		<TD>&nbsp;</TD>
		<TD><em>Our Name: Southwest Educational Development Laboratory (SEDL)</em>
			<P>
			<em>Our Tagline: Building Knowledge to Support Learning</em></TD></TR>
		</TABLE>
	</UL>
<P>
Now, think of the words you would want our company logo and tagline to communicate about:
	<ul>
	<li>the type of work the company does
	<li>the clients (or areas) the company serves
	<li>the values and mission of the company
	</ul>

Please enter up to five words that you feel a company logo should express about the ideal company you have in mind. Think not only about where SEDL is now and what we can do now&mdash;think about the ideal organization SEDL could be and what we could be doing in the future.
<P>
	<table border = "1" cellpadding="4" cellspacing="0">
	<tr bgcolor="#EBEBEB"><td><strong>#</strong></td><td><strong>Your Brainstorm Words</strong></td></tr>
	<tr bgcolor="#EBEBEB"><td>1</td><td><input type="text" name="new_w1" value="$w1"></td></tr>
	<tr bgcolor="#EBEBEB"><td>2</td><td><input type="text" name="new_w2" value="$w2"></td></tr>
	<tr bgcolor="#EBEBEB"><td>3</td><td><input type="text" name="new_w3" value="$w3"></td></tr>
	<tr bgcolor="#EBEBEB"><td>4</td><td><input type="text" name="new_w4" value="$w4"></td></tr>
	<tr bgcolor="#EBEBEB"><td>5</td><td><input type="text" name="new_w5" value="$w5"></td></tr>
	</table>

<P>

<H2>Part 2: Company Tagline</H2>
In part two of the brainstorming exercise, we'd like to hear your ideas for a tagline for the ideal SEDL -- something short and to the point that conveys what the company is all about.
<P>
To spark your brainstorm, here are some example taglines used by companies you may be familiar with:
<br>
<table border = "1" cellpadding="4" cellspacing="0">
<tr bgcolor="#EBEBEB"><td>Nike</td><td>Just do it</td></tr>
<tr bgcolor="#EBEBEB"><td>GE</td><td>Imagination at Work</td></tr>
<tr bgcolor="#EBEBEB"><td>Honda</td><td>The Power of Dreams</td></tr>
<tr bgcolor="#EBEBEB"><td>Saturn</td><td>Like always. Like never before.</td></tr>
<tr bgcolor="#EBEBEB"><td>Learning Point Associates</td><td>Knowledge, Strategies, Results</td></tr>
<tr bgcolor="#EBEBEB"><td>PREL</td><td>Building Capacity through Education</td></tr>
<tr bgcolor="#EBEBEB"><td>SEDL</td><td>Building Knowledge to Support Learning</td></tr>
<tr bgcolor="#EBEBEB"><td>McREL</td><td>Delivering Research and Practical Guidance to Educators</td></tr>
<tr bgcolor="#EBEBEB"><td>SERVE</td><td>Improving Learning Through Research and Development</td></tr>
</table>
<P>
In the boxes below, please enter two taglines that you feel speak to SEDL's values, mission, work, and clients.
<P>
	<table border = "1" cellpadding="4" cellspacing="0">
	<tr bgcolor="#EBEBEB"><td><strong>#</strong></td><td><strong>Your Tagline Ideas</strong></td></tr>
	<tr bgcolor="#EBEBEB"><td>1</td><td><input type="text" name="new_tag1" value="$tag1" size="70"></td></tr>
	<tr bgcolor="#EBEBEB"><td>2</td><td><input type="text" name="new_tag2" value="$tag2" size="70"></td></tr>
	</table>

<P>
<H2>Part 3: Company Name</H2>
In part three of the brainstorming exercise, you have to think hard.  
You only get one suggestion.
<P>
Based on your brainstorm so far, if you had the option of renaming the company today, what would you suggest?
<P>
<table border="1" cellpadding="4" cellspacing="0">
<tr bgcolor="#EBEBEB"><td valign="top" nowrap><input type = "radio" name="new_name" value="do not change name" $selected_a> a.</td>
	<td valign="top">Don't change the name</td></tr>
<tr bgcolor="#EBEBEB"><td valign="top" nowrap><input type = "radio" name="new_name" value="change part of name" $selected_b> b.</td>
	<td valign="top">Change part of the name.<br>
					<em>Suggestion:</em> <input type="text" name="new_name_sugg1" value="$name_sugg1" size="70">
	</td></tr>
<tr bgcolor="#EBEBEB"><td valign="top" nowrap><input type = "radio" name="new_name" value="use acronym" $selected_c> c.</td>
	<td valign="top">Change to use the stand-alone acronym, "SEDL."</td></tr>
<tr bgcolor="#EBEBEB"><td valign="top" nowrap><input type = "radio" name="new_name" value="change name completely" $selected_d> d.</td>
	<td valign="top">Change the name completely.<br>
					<em>Suggestion:</em> <input type="text" name="new_name_sugg2" value="$name_sugg2" size="70">
	</td></tr>
</table>



<UL>
	<input type="hidden" name="logon_user" value="$logon_user">
	<input type="hidden" name="location" value="enterrecord">
	<input type="submit" name="submit" value="Send Brainstorm">
	</form>
    </UL>
EOM
	if ($survey_admin eq 'yes') {
		&show_results();
	}

}

######################################################################################
## END: LOCATION = SHOWFORM
######################################################################################

######################################################################################
## START: FUNCTION = show_results
######################################################################################
sub show_results {
print <<EOM;
<P>
<H4>Data Received so far <font color=red>(Only blitke and jpollard see this information)</font></H4>
EOM

my $command = "select * from brainstorm order by date_entered DESC";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

## DECLARE TRACKING VARIABLES
my %brainstorm_words;

print<<EOM;
<P>
There are $num_matches brainstorm entries on file so far.
<P>
<table border="1" cellpadding="4" cellspacing="0">
<tr><td><strong>#</strong></td><td><strong>Word 1</strong></td><td><strong>Word 2</strong></td><td><strong>Word 3</strong></td><td><strong>Word 4</strong></td><td><strong>Word 5</strong></td><td><strong>Tagline 1</strong></td><td><strong>Tagline 2</strong></td><td><strong>Company Name</strong></td></tr>
EOM
	my $counter = "1";
	my @words;
	while (my @arr = $sth->fetchrow) {
		my ($staff_id , $w1, $w2, $w3, $w4, $w5, $tag1, $tag2, $name, $name_sugg1, $name_sugg2, $date_entered) = @arr;
		$w1 = lc($w1);
		$w2 = lc($w2);
		$w3 = lc($w3);
		$w4 = lc($w4);
		$w5 = lc($w5);

		$brainstorm_words{$w1}++;
		$brainstorm_words{$w2}++;
		$brainstorm_words{$w3}++;
		$brainstorm_words{$w4}++;
		$brainstorm_words{$w5}++;
		print "<tr><td>$counter</td><td>$w1</td><td>$w2</td><td>$w3</td><td>$w4</td><td>$w5</td><td>$tag1</td><td>$tag2</td><td>$name<br>$name_sugg1 $name_sugg2</td></tr>";
		$counter++;
	}
print<<EOM;
</table>
<P>
<H4>Counts of word frequency</H4>
<table border="1" cellpadding="4" cellspacing="0">
<tr><td><font color=\"#666666\">#</font></td><td nowrap><strong>Brainstorm Word</strong></td><td><strong>Frequency</strong></td></tr>
EOM
	foreach my $key (keys %brainstorm_words) {
	
		# MAKE THE NUMERICAL PREFIX HAVE LEADING ZEROS UP TO 3 CHARACTERS
		while (length($brainstorm_words{$key}) < 4) {
			$brainstorm_words{$key} = "0$brainstorm_words{$key}";
		} # END WHILE
		
		## ADD SUFFIX OF THE KEYWORD
		$brainstorm_words{$key} = "$brainstorm_words{$key}\;$key";
		
		# PUSH KEYWORK INTO AN ARRAY
		if (($key ne '[none]') && ($key ne '--') && ($key ne '???') && ($key ne 'enter up to five words')) {
			push (@words, $brainstorm_words{$key});
		}
	} # END FOREACH
		
		## SORT THE ARRAY
		@words = sort {$a cmp $b} @words;
		my $counter_plus1 = "";
		## PRINT THE ARRY
		my $counter = "0";
		while ($counter <= $#words) {
			my ($this_count, $this_word) = split(/\;/,$words[$counter]);
			
			# ROUND NUMBER TO REMOVE LEADING ZEROS
			$this_count = &format_number($this_count, "0","yes");
			$counter_plus1 = $counter + 1;
			print "<tr><td><font color=\"#666666\">$counter_plus1</font></td><td>$this_word</td><td ALIGN=\"RIGHT\">$this_count</td></tr>";
			$counter++;
		} # END WHILE
#print "<tr><td>$key</td><td>$brainstorm_words{$key}</td></tr>\n";

print "</table><P>Note: You can highlight this table and paste it into MS Excel.";


}
######################################################################################
## END: FUNCTION = show_results
######################################################################################




################################
## START: PRINT PAGE FOOTER
################################
print<<EOM;
<P>
$htmltail
EOM
################################
## END: PRINT PAGE FOOTER
################################






####################################################################
##  HERE ARE SOME SUBROUTINES USED BY THIS DATABASE SEARCH SCRIPT ##
####################################################################

sub cleanthis {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem = $dirtyitem;
}



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




####################################################################
## START: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################
sub backslash_fordb {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/Ô/\\Ô/g;
   $dirtyitem =~ s/Õ/\\Õ/g;
   $dirtyitem =~ s/"/\\"/g;
   return($dirtyitem);
}
####################################################################
## END: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################

####################################################################
## START: ROUND NUMBER TO X PLACES, WITH OR WITHOUT COMMAS
####################################################################
# EXAMPLE OF USAGE
# $num = &format_number($num, "0","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
sub format_number {
	my $number_unformatted = $_[0];
	   $number_unformatted = 0 if ($number_unformatted eq '');
	   $number_unformatted =~ s/\,//g; # REMOVE COMMA IF ALREADY EXISTS 
	my $decimal_places = $_[1];
		$decimal_places = "2" if ($decimal_places eq '');
	my $commas_included = $_[2];

	my $x = new Number::Format;
	my $number_formatted = $x->format_number($number_unformatted, $decimal_places, $decimal_places);
		if ($commas_included ne 'yes') {
			$number_formatted =~ s/\,//g;
		}
	return($number_formatted);
}
####################################################################
## END: ROUND NUMBER TO X PLACES, WITH OR WITHOUT COMMAS
####################################################################


#!/usr/bin/perl 

################################################################################
# Copyright 2004 by Southwest Educational Development Laboratory
# Written by Brian Litke, SEDL Web Administrator (08-26-2004)
#
# This script is used to collect survey data about SEDL's COmmunications plan
################################################################################

##########################
##  SET SCRIPT HANDLERS ## 
##########################

#use diagnostics;
use strict;
use CGI qw/:all/;
use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;
use CGI::Carp qw(fatalsToBrowser);

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

my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};
my $browser = $ENV{"HTTP_USER_AGENT"};




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

my $manager_status = $query->param("manager_status");
my $admin_redirect = $query->param("admin_redirect");
if ($location eq 'admin') {
	$admin_redirect = "yes";
}

## REMOVE TABS AND CARRIAGE RETURNS FROM USER-ENTERED DATA USING "CLEANTHIS" SUBROUTINE
#$first_name = &cleanthis ($first_name);

########################################################
## END: GET VARIABLES FROM FORM
########################################################



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
#	my $num_matches = $sth->rows;
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
	my $command_delete_session = "DELETE FROM staff_sessions WHERE ss_session_id='$cookie_ss_session_id'";
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
<title>SEDL All-staff Survey - Internal Communications</title>
$htmlhead
EOM

   

####################################################################
####################################################################
## START: IF LOCATION = ENTERRECORD
####################################################################
####################################################################
my @q = "";
if ($location eq 'enterrecord') {

## START: READ IN THE 29 SURVEY QUESTION VARIABLES
my $counter = "0";
while ($counter <= 32) {
	$q[$counter] = $query->param("q$counter");
	$counter++;
}
## END: READ IN THE 29 SURVEY QUESTION VARIABLES
		## START: SET ERROR MESSAGE FOR MISSING VARIABLES
		$error_message .= ", 1" if ($q[1] eq '');
		$error_message .= ", 2" if ($q[2] eq '');
		$error_message .= ", 3" if ($q[3] eq '');
		$error_message .= ", 4" if ($q[4] eq '');
		$error_message .= ", 5" if ($q[5] eq '');
		$error_message .= ", 6" if ($q[6] eq '');
		$error_message .= ", 7" if ($q[7] eq '');
		$error_message .= ", 8" if ($q[8] eq '');
		$error_message .= ", 9" if ($q[9] eq '');
#		$error_message .= ", 10" if ($q[10] eq '');
		$error_message .= ", 11" if ($q[11] eq '');
		$error_message .= ", 12" if ($q[12] eq '');
		$error_message .= ", 13" if ($q[13] eq '');
		$error_message .= ", 14" if ($q[14] eq '');
#		$error_message .= ", 15" if ($q[15] eq ''); # non-manager only question
		$error_message .= ", 16" if ($q[16] eq '');
		$error_message .= ", 17" if ($q[17] eq '');
#		$error_message .= ", 18" if ($q[18] eq '');
		$error_message .= ", 19" if ($q[19] eq '');
		$error_message .= ", 20" if ($q[20] eq '');
		$error_message .= ", 21" if ($q[21] eq '');
		$error_message .= ", 22" if ($q[22] eq '');
		$error_message .= ", 23" if ($q[23] eq '');
		$error_message .= ", 24" if ($q[24] eq '');
#		$error_message .= ", 25" if ($q[25] eq '');
		$error_message .= ", 26" if ($q[26] eq '');
		$error_message .= ", 27" if ($q[27] eq '');
		$error_message .= ", 28" if ($q[28] eq '');
		$error_message .= ", 29" if ($q[29] eq '');
		$error_message .= ", 30" if ($q[30] eq '');
#		$error_message .= ", 31" if ($q[31] eq '');
		$error_message .= ", 32" if ($q[32] eq '');
		if ($error_message ne '') {
			$error_message = "QQQ$error_message";
			$error_message =~ s/QQQ\, //g;
			$error_message = "<FONT COLOR=RED>You forgot to answer question(s): $error_message.  Please fill in the missing data.</FONT>";

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
Thank you for filling out the survey. Look for the results of this survey 
on the SEDL staff intranet 
(http://www.sedl.org/staff/communications/allaboutus/survey.html) in September. 
<P>
We appreciate your time and comments.
<P>
- OIC staff
EOM

	$manager_status = "no";
	$manager_status = "yes" if ($logon_user eq 'cjordan');
	$manager_status = "yes" if ($logon_user eq 'whoover');
	$manager_status = "yes" if ($logon_user eq 'jbuttram');
	$manager_status = "yes" if ($logon_user eq 'drainey');
	$manager_status = "yes" if ($logon_user eq 'vdimock');
	$manager_status = "yes" if ($logon_user eq 'sstreet');
	$manager_status = "yes" if ($logon_user eq 'makigler');
	$manager_status = "yes" if ($logon_user eq 'jpollard');
	$manager_status = "yes" if ($logon_user eq 'jwestbro');
	$manager_status = "yes" if ($logon_user eq 'akriegel');
	$manager_status = "yes" if ($logon_user eq 'jbaskin');


## BACKSLASH TEXT FIELDS
$q[10] = backslash_fordb($q[10]);
$q[18] = backslash_fordb($q[18]);
$q[25] = backslash_fordb($q[25]);
$q[31] = backslash_fordb($q[31]);
$q[32] = backslash_fordb($q[32]);

## SAVE DATA TO DATABASE
my $command = "UPDATE survey_oic_2004 SET survey_date='$date_full_mysql', q1='$q[1]', q2='$q[2]', q3='$q[3]', q4='$q[4]', q5='$q[5]', q6='$q[6]', q7='$q[7]', q8='$q[8]', q9='$q[9]', q10='$q[10]', q11='$q[11]', q12='$q[12]', q13='$q[13]', q14='$q[14]', q15='$q[15]', q16='$q[16]', q17='$q[17]', q18='$q[18]', q19='$q[19]', q20='$q[20]', q21='$q[21]', q22='$q[22]', q23='$q[23]', q24='$q[24]', q25='$q[25]', q26='$q[26]', q27='$q[27]', q28='$q[28]', q29='$q[29]', q30='$q[30]', q31='$q[31]', q32='$q[32]' WHERE user_id LIKE '$logon_user'";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
#	my $num_matches = $sth->rows;

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
Subject: Data from 2004 All-staff Communications Survey

The following data was received from the 2004 All-staff Communications Survey at:
http://www.sedl.org/staff/oic_survey.cgi

The results of this survey have been saved to a database.  Access the admin page at: 
http://www.sedl.org/staff/oic_survey.cgi?location=admin


RESOURCE REQUEST INFORMATION STARTS HERE:
=========================================
Survey Date: $todaysdate

Manager survey?: $manager_status

EOM
my $counter = "1";
while ($counter <= 32) {
	print NOTIFY "Question $counter\: $q[$counter]\n";
	$counter++;
}

print NOTIFY <<EOM;

User Stats:
-----------
Web Browser software: $browser
IP Number: $ipnum2
Domain: $ipnum

EOM
close(NOTIFY);

############################# END OF EMAIL TO OIC INFORMATION SERVICES ############################
	}  ## END OF ACTIONS IF USER SUBMITS GOOD ENTRY




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
<h1>SEDL ALL-STAFF SURVEY<BR>
INTERNAL COMMUNICATIONS 

EOM
print "<SPAN class=small>(Click here to <A HREF=\"resource_search.cgi?location=logout\">logout</A>)</SPAN>" if ($session_active eq 'yes');
print<<EOM;
</H1>
<H2 ALIGN=CENTER>Please Log On</H2>
EOM


print<<EOM;
$error_message<P>
Please enter your SEDL staff ID and password.  Your responses will NOT be linked 
to your name; although OIC will track which staff members have completed the survey, 
so we can contact those who don't to remind them to provide us with their feedback.
<P>
<H4>Your Information </H4>
<form action="oic_survey.cgi" method="POST">
<table BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP"><strong>Your user ID</strong><br>
  		  (ex: sliberty)</TD>
    <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
<TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR>
  			<SPAN class="small">(not your e-mail password)</SPAN></TD>
    <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD></TR>
</table>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="showform">
EOM
	if ($admin_redirect ne '') {
		print "<input type=\"hidden\" name=\"admin_redirect\" value=\"admin_redirect\">";
	}
print<<EOM;
  <INPUT TYPE="SUBMIT" VALUE="Click to Proceed to the Survey">
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
	# FLAG USER AS MANAGER, IF APPROPRIATE
	$manager_status = "yes" if ($logon_user eq 'cjordan');
	$manager_status = "yes" if ($logon_user eq 'whoover');
	$manager_status = "yes" if ($logon_user eq 'jbuttram');
	$manager_status = "yes" if ($logon_user eq 'drainey');
	$manager_status = "yes" if ($logon_user eq 'vdimock');
	$manager_status = "yes" if ($logon_user eq 'sstreet');
	$manager_status = "yes" if ($logon_user eq 'makigler');
	$manager_status = "yes" if ($logon_user eq 'jpollard');
	$manager_status = "yes" if ($logon_user eq 'jwestbro');
	$manager_status = "yes" if ($logon_user eq 'akriegel');
	$manager_status = "yes" if ($logon_user eq 'jbaskin');

print <<EOM;
<h1>SEDL ALL-STAFF SURVEY<BR>
INTERNAL COMMUNICATIONS 
</h1>
<P>
<form action="oic_survey.cgi" method=POST>

<P>
<H2>Introduction</H2>
<P>
SEDL staff have been participating in an audit of internal communications that 
will contribute to the development of an internal communications plan. This 
all-staff survey is designed to further test communications strategies proposed 
in recent SEDL focus groups and telephone interviews.  We also want to test some 
additional best practices that have been discussed by members of our advisory 
roundtable, other staff, and our consultants. Before we put new strategies in 
place and refine existing ones, we want to know what you think. 
We will share the findings of the focus groups, phone interviews, and online 
survey on the staff intranet in September. 
<P>
We know your time is limited and we have designed the survey with this in mind. 
It only takes a few moments to fill out. This is our opportunity to hear from all 
staff. We invite you to share your thoughts with us.
<P>
Please rate each of the following items based upon what you believe would work well 
for our organization (e.g., a communications strategy you believe would be an 
effective or not effective means to improve communications across the organization). 
<P>
Thank you!
<P>

<H2>Improve Communications Organizationwide</H2>
<P>
Please rate the following channels of communication, which would be designed to 
improve the flow of information across SEDL.  
You'll notice they are a mix of new and existing strategies. 
<P>
<em><FONT COLOR=RED>* Note: All fields are required.</FONT></em>
<P>
<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=0 BORDER=1>
<TR><TD COLSPAN=2>&nbsp;</TD>
	<TD ALIGN=CENTER>not effective</TD>
	<TD ALIGN=CENTER>somewhat effective</TD>
	<TD ALIGN=CENTER>effective</TD>
	<TD ALIGN=CENTER>very effective</TD>
	<TD ALIGN=CENTER>extremely effective</TD></TR>

<TR><TD VALIGN=TOP>1.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		A regular  electronic SEDL staff newsletter</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q1" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q1" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q1" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q1" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q1" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>2.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Timely all-staff e-mail updates in headline format with links to more in-depth information on the SEDL staff intranet</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q2" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q2" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q2" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q2" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q2" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>3.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Informational workshops about SEDL programs and divisions that help increase staff's understanding about SEDL's mission and how our work contributes to meeting that mission</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q3" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q3" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q3" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q3" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q3" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>4.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		A SEDL information card that highlights key facts and core messages about the organization to ensure we are communicating consistent and accurate information</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q4" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q4" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q4" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q4" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q4" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>5.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		SEDL all-staff meetings that are designed to provide relevant institutional information (e.g., revenue projections, funding opportunities, legislative updates from Capitol Hill, and newly awarded proposals) to all staff members </TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q5" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q5" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q5" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q5" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q5" VALUE="5"></TD></TR>

<TR><TD COLSPAN=2>&nbsp;</TD>
	<TD ALIGN=CENTER>not effective</TD>
	<TD ALIGN=CENTER>somewhat effective</TD>
	<TD ALIGN=CENTER>effective</TD>
	<TD ALIGN=CENTER>very effective</TD>
	<TD ALIGN=CENTER>extremely effective</TD></TR>
<TR><TD VALIGN=TOP>6.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		 Staff development sessions on roles and responsibilities of all staff on internal communications such as protocols for dialogue, sharing of timely information, and critical friends groups</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q6" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q6" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q6" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q6" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q6" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>7.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Structured  online chats concerning various work-related or organizational topics, problems, or issues </TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q7" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q7" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q7" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q7" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q7" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>8.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Inquiry meetings organized around a critical question (e.g., internal communications, work-related challenges, organizational issues) that provokes dialogue leading to deeper thinking about goals, issues and solutions and involves staff at multiple levels across the organization</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q8" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q8" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q8" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q8" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q8" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>9.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		"Critical friends" sessions in which colleagues talk, in a trusting environment, about their work including strengths, weaknesses, what can be improved, and suggestions for how it can be done better</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q9" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q9" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q9" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q9" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q9" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>10.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		What other communications strategy or strategies would you recommend?</TD>
	<TD COLSPAN=6>
		<textarea name="q10" rows=6 cols=35></textarea>
</TD></TR>
</TABLE>
<P>

<H2>Increase Communications About Decision Making at SEDL</H2>
<P>
Please rate the following channels of communication, which would be designed to better 
inform staff about decisionmaking processes.
<P>
<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=0 BORDER=1>
<TR><TD COLSPAN=2>&nbsp;</TD>
	<TD ALIGN=CENTER>not effective</TD>
	<TD ALIGN=CENTER>somewhat effective</TD>
	<TD ALIGN=CENTER>effective</TD>
	<TD ALIGN=CENTER>very effective</TD>
	<TD ALIGN=CENTER>extremely effective</TD></TR>


<TR><TD VALIGN=TOP>11.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Summary of key discussion items, action steps and decision points from SEDL Management Council meetings</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q11" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q11" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q11" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q11" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q11" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>12.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Summary of key discussion items, action steps and decision points from SEDL all-staff meetings</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q12" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q12" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q12" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q12" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q12" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>13.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
EOM
if ($manager_status eq 'yes') {
	print "Regularly scheduled meetings with my team";
} else {
	print "Regularly scheduled meetings with my team members and manager";
}		
print<<EOM;
		</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q13" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q13" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q13" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q13" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q13" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>14.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
EOM
if ($manager_status eq 'yes') {
	print "Providing timely e-mails to my team members that cannot wait until the next scheduled staff meeting";
} else {
	print "Timely e-mails from my manager on organizational decisions that cannot wait until the next scheduled staff meeting";
}		
print<<EOM;
		</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q14" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q14" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q14" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q14" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q14" VALUE="5"></TD></TR>
<TR><TD COLSPAN=2>&nbsp;</TD>
	<TD ALIGN=CENTER>not effective</TD>
	<TD ALIGN=CENTER>somewhat effective</TD>
	<TD ALIGN=CENTER>effective</TD>
	<TD ALIGN=CENTER>very effective</TD>
	<TD ALIGN=CENTER>extremely effective</TD></TR>
EOM
if ($manager_status eq 'yes') {
	print "<TR><TD VALIGN=TOP COLSPAN=7>15. Note: Question 15 is omitted from your survey, as it is for non-managers only.</TD></TR>";
} else {
print<<EOM;
<TR><TD VALIGN=TOP>15.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Provide timely e-mails to my manager and team members about issues related to my work that inform our work as a team or may have an impact on organizational decision-making</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q15" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q15" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q15" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q15" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q15" VALUE="5"></TD></TR>
EOM
}
print<<EOM;
<TR><TD VALIGN=TOP>16.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Regular open forums with the CEO by role-alike groups (e.g., all administrative 
		staff, communications and information specialists, program staff)</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q16" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q16" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q16" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q16" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q16" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>17.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Regular open forums with the CEO by division or program area</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q17" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q17" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q17" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q17" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q17" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>18.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		What other communications strategy or strategies would you recommend?</TD>
	<TD COLSPAN=6>
		<textarea name="q18" rows=6 cols=35></textarea>
</TD></TR>
</TABLE>
<P>

<H2>SEDL Staff Newsletter</H2>
<P>
One of the most frequently mentioned strategies for increasing internal  communications is an online newsletter. 
If SEDL started a monthly electronic staff newsletter, what information would be of most interest to you? Please rate the following pieces of information.
<P>
<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=0 BORDER=1>
<TR><TD COLSPAN=2>&nbsp;</TD>
	<TD ALIGN=CENTER>not of interest</TD>
	<TD ALIGN=CENTER>of little interest</TD>
	<TD ALIGN=CENTER>of interest</TD>
	<TD ALIGN=CENTER>of high interest</TD>
	<TD ALIGN=CENTER>of very high interest</TD></TR>


<TR><TD VALIGN=TOP>19.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		General funding information about the organization such as revenue projections, newly acquired grants, and other relevant budget matters </TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q19" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q19" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q19" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q19" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q19" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>20.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Information about SEDL's organizational plans such as updates on the strategic planning process, options related to the building lease, and other internal issues that affect the organization</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q20" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q20" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q20" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q20" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q20" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>21.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Job advancement opportunities</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q21" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q21" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q21" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q21" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q21" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>22.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		A profile of a SEDL employee</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q22" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q22" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q22" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q22" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q22" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>23.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		A monthly story of a SEDL project and how it relates to SEDL's mission</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q23" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q23" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q23" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q23" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q23" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>24.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Regular announcements about personnel changes</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q24" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q24" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q24" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q24" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q24" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>25.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		What other information would you be interested in reading about in a SEDL staff newsletter?</TD>
	<TD COLSPAN=6>
		<textarea name="q25" rows=6 cols=35></textarea>
</TD></TR>
</TABLE>
<P>

<H2>SEDL Intranet</H2>
<P>
Use of the intranet is another strategy that was mentioned often. 
We are exploring ways to strengthen the SEDL intranet. 
Please rate the following pieces of information.
<P>
<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=0 BORDER=1>
<TR><TD COLSPAN=2>&nbsp;</TD>
	<TD ALIGN=CENTER>not of interest</TD>
	<TD ALIGN=CENTER>of little interest</TD>
	<TD ALIGN=CENTER>of interest</TD>
	<TD ALIGN=CENTER>of high interest</TD>
	<TD ALIGN=CENTER>of very high interest</TD></TR>

<TR><TD VALIGN=TOP>26.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		SEDL talking points on an as-needed basis regarding topics such as existing projects, new initiatives, and research findings</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q26" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q26" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q26" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q26" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q26" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>27.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		A staff-only online discussion area to highlight potential opportunities for new work and product development in SEDL states</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q27" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q27" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q27" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q27" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q27" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>28.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		A summary of key discussion items, action steps and decision points from all-staff meetings</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q28" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q28" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q28" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q28" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q28" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>29.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		A summary of key discussion items, action steps and decision points from SEDL Management Council Meetings</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q29" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q29" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q29" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q29" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q29" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>30.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		Information about the fiscal health of the organization</TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q30" VALUE="1"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q30" VALUE="2"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q30" VALUE="3"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q30" VALUE="4"></TD>
	<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="q30" VALUE="5"></TD></TR>

<TR><TD VALIGN=TOP>31.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		What other information would you be interested in receiving on the SEDL staff intranet?</TD>
	<TD COLSPAN=6>
		<textarea name="q31" rows=6 cols=35></textarea>
</TD></TR>
</TABLE>
<P>

<H2>Your Role in Improving Internal Communications</H2>
<P>
Please answer the following question.
<P>
<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=0 BORDER=1>

<TR><TD VALIGN=TOP>32.</TD>
	<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" ALT=" " WIDTH="200" HEIGHT="1"><BR>
		What personal role do you see yourself playing to improve internal communications at SEDL? </TD>
	<TD ALIGN=CENTER>
		<textarea name="q32" rows=6 cols=35></textarea>
</TD></TR>

</TABLE>
<P>

	<UL>
	<input type="hidden" name="manager_status" value="$manager_status">
	<input type="hidden" name="logon_user" value="$logon_user">
	<input type="hidden" name="location" value="enterrecord">
	<input type="submit" name="submit" value="Send Survey">
	</form>
    </UL>       


EOM
}
######################################################################################
## END: LOCATION = SHOWFORM
######################################################################################

######################################################################################
## START: LOCATION = ADMIN
######################################################################################
if ($location eq 'admin') {
print <<EOM;
<h1>SEDL ALL-STAFF SURVEY<BR>
INTERNAL COMMUNICATIONS 
</h1>
<P>
<form action="oic_survey.cgi" method=POST>

<P>
<H2>Data Received so far</H2>
EOM

my $command = "select * from survey_oic_2004 order by user_id";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

## DECLARE TRACKING VARIABLES AND
## OPEN BULLETED LIST FOR TEXT FIELDS
my $count_q1 = "0";
my $count_q2 = "0";
my $count_q3 = "0";
my $count_q4 = "0";
my $count_q5 = "0";
my $count_q6 = "0";
my $count_q7 = "0";
my $count_q8 = "0";
my $count_q9 = "0";
my $count_q10 = "";
my $count_q11 = "0";
my $count_q12 = "0";
my $count_q13 = "0";
my $count_q14 = "0";
my $count_q15 = "0";
my $count_q16 = "0";
my $count_q17 = "0";
my $count_q18 = "";
my $count_q19 = "0";
my $count_q20 = "0";
my $count_q21 = "0";
my $count_q22 = "0";
my $count_q23 = "0";
my $count_q24 = "0";
my $count_q25 = "";
my $count_q26 = "0";
my $count_q27 = "0";
my $count_q28 = "0";
my $count_q29 = "0";
my $count_q30 = "0";
my $count_q31 = "";
my $count_q32 = "";
my $count_responses = "0";
my $count_responses_question15 = "0";

my %responses = "";

print<<EOM;
There are $num_matches SEDL staff on file.
EOM
	while (my @arr = $sth->fetchrow) {
		my ($serial_num, $user_id, $survey_date, $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $q10, $q11, $q12, $q13, $q14, $q15, $q16, $q17, $q18, $q19, $q20, $q21, $q22, $q23, $q24, $q25, $q26, $q27, $q28, $q29, $q30, $q31, $q32) = @arr;
			if ($survey_date =~ '2004') {
				$count_responses++;
				$count_responses_question15++ if ($q15 ne '');
			}
			# RECORD RESPONSES FOR BAR GRAPHING
			&record_responses ("1", $q1);
			&record_responses ("2", $q2);
			&record_responses ("3", $q3);
			&record_responses ("4", $q4);
			&record_responses ("5", $q5);
			&record_responses ("6", $q6);
			&record_responses ("7", $q7);
			&record_responses ("8", $q8);
			&record_responses ("9", $q9);

			&record_responses ("11", $q11);
			&record_responses ("12", $q12);
			&record_responses ("13", $q13);
			&record_responses ("14", $q14);
			&record_responses ("15", $q15);
			&record_responses ("16", $q16);
			&record_responses ("17", $q17);

			&record_responses ("19", $q19);
			&record_responses ("20", $q20);
			&record_responses ("21", $q21);
			&record_responses ("22", $q22);
			&record_responses ("23", $q23);
			&record_responses ("24", $q24);

			&record_responses ("26", $q26);
			&record_responses ("27", $q27);
			&record_responses ("28", $q28);
			&record_responses ("29", $q29);
			&record_responses ("30", $q30);


sub record_responses {
	my $question_number = $_[0];
	my $question_response = $_[1];
		$responses{"$question_number.1"} = $responses{"$question_number.1"} + 1 if ($question_response eq '1');
		$responses{"$question_number.2"} = $responses{"$question_number.2"} + 1 if ($question_response eq '2');
		$responses{"$question_number.3"} = $responses{"$question_number.3"} + 1 if ($question_response eq '3');
		$responses{"$question_number.4"} = $responses{"$question_number.4"} + 1 if ($question_response eq '4');
		$responses{"$question_number.5"} = $responses{"$question_number.5"} + 1 if ($question_response eq '5');
}

			$count_q1 = $count_q1 + $q1;
			$count_q2 = $count_q2 + $q2;
			$count_q3 = $count_q3 + $q3;
			$count_q4 = $count_q4 + $q4;
			$count_q5 = $count_q5 + $q5;
			$count_q6 = $count_q6 + $q6;
			$count_q7 = $count_q7 + $q7;
			$count_q8 = $count_q8 + $q8;
			$count_q9 = $count_q9 + $q9;
			$count_q10 = "$count_q10 <P><LI>$q10" if ($q10 ne '');
			$count_q11 = $count_q11 + $q11;
			$count_q12 = $count_q12 + $q12;
			$count_q13 = $count_q13 + $q13;
			$count_q14 = $count_q14 + $q14;
			$count_q15 = $count_q15 + $q15;
			$count_q16 = $count_q16 + $q16;
			$count_q17 = $count_q17 + $q17;
			$count_q18 = "$count_q18 <P><LI>$q18" if ($q18 ne '');
			$count_q19 = $count_q19 + $q19;
			$count_q20 = $count_q20 + $q20;
			$count_q21 = $count_q21 + $q21;
			$count_q22 = $count_q22 + $q22;
			$count_q23 = $count_q23 + $q23;
			$count_q24 = $count_q24 + $q24;
			$count_q25 = "$count_q25 <P><LI>$q25" if ($q25 ne '');
			$count_q26 = $count_q26 + $q26;
			$count_q27 = $count_q27 + $q27;
			$count_q28 = $count_q28 + $q28;
			$count_q29 = $count_q29 + $q29;
			$count_q30 = $count_q30 + $q30;
			$count_q31 = "$count_q31 <P><LI>$q31" if ($q31 ne '');
			$count_q32 = "$count_q32 <P><LI>$q32" if ($q32 ne '');
	} # END DB QUERY LOOP


## DIVIDE NUMERIC VALUES BY NUMBER OF RESPONSES TO GET THE AVERAGE
if ($count_responses ne '0') {
	$count_q1 = $count_q1 / $count_responses;
	$count_q2 = $count_q2 / $count_responses;
	$count_q3 = $count_q3 / $count_responses;
	$count_q4 = $count_q4 / $count_responses;
	$count_q5 = $count_q5 / $count_responses;
	$count_q6 = $count_q6 / $count_responses;
	$count_q7 = $count_q7 / $count_responses;
	$count_q8 = $count_q8 / $count_responses;
	$count_q9 = $count_q9 / $count_responses;
#
	$count_q11 = $count_q11 / $count_responses;
	$count_q12 = $count_q12 / $count_responses;
	$count_q13 = $count_q13 / $count_responses;
	$count_q14 = $count_q14 / $count_responses;
	$count_q15 = $count_q15 / $count_responses_question15;
	$count_q16 = $count_q16 / $count_responses;
	$count_q17 = $count_q17 / $count_responses;
#
	$count_q19 = $count_q19 / $count_responses;
	$count_q20 = $count_q20 / $count_responses;
	$count_q21 = $count_q21 / $count_responses;
	$count_q22 = $count_q22 / $count_responses;
	$count_q23 = $count_q23 / $count_responses;
	$count_q24 = $count_q24 / $count_responses;
#
	$count_q26 = $count_q26 / $count_responses;
	$count_q27 = $count_q27 / $count_responses;
	$count_q28 = $count_q28 / $count_responses;
	$count_q29 = $count_q29 / $count_responses;
	$count_q30 = $count_q30 / $count_responses;
#
#
# $num = &format_number($num, "0","yes"); 

}
	$count_q1 = &format_number($count_q1, "2","no"); #  ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
	$count_q2 = &format_number($count_q2, "2","no");
	$count_q3 = &format_number($count_q3, "2","no");
	$count_q4 = &format_number($count_q4, "2","no");
	$count_q5 = &format_number($count_q5, "2","no");
	$count_q6 = &format_number($count_q6, "2","no");
	$count_q7 = &format_number($count_q7, "2","no");
	$count_q8 = &format_number($count_q8, "2","no");
	$count_q9 = &format_number($count_q9, "2","no");
#
	$count_q11 = &format_number($count_q11, "2","no");
	$count_q12 = &format_number($count_q12, "2","no");
	$count_q13 = &format_number($count_q13, "2","no");
	$count_q14 = &format_number($count_q14, "2","no");
	$count_q15 = &format_number($count_q15, "2","no");
	$count_q16 = &format_number($count_q16, "2","no");
	$count_q17 = &format_number($count_q17, "2","no");
#
	$count_q19 = &format_number($count_q19, "2","no");
	$count_q20 = &format_number($count_q20, "2","no");
	$count_q21 = &format_number($count_q21, "2","no");
	$count_q22 = &format_number($count_q22, "2","no");
	$count_q23 = &format_number($count_q23, "2","no");
	$count_q24 = &format_number($count_q24, "2","no");
#
	$count_q26 = &format_number($count_q26, "2","no");
	$count_q27 = &format_number($count_q27, "2","no");
	$count_q28 = &format_number($count_q28, "2","no");
	$count_q29 = &format_number($count_q29, "2","no");
	$count_q30 = &format_number($count_q30, "2","no");


print<<EOM;
<P>
We have received $count_responses responses so far ($count_responses_question15 to question 15, which is non-manager only).  
Click here to <A HREF="oic_survey.cgi?location=send_survey">trigger a reminder e-mail</A> to the stragglers who haven't responded yet.
<P>
<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=0>
<TR><TD>Question<BR>Number</TD>
	<TD>Responses</TD>
	<TD COLSPAN=2>Average</TD></TR>
<TR><TD COLSPAN=4><strong>Improve Communications Organizationwide</strong></TD></TR>
<TR><TD VALIGN="TOP">1</TD>
	<TD VALIGN="TOP">A regular  electronic SEDL staff newsletter</TD>
	<TD VALIGN="TOP">$count_q1</TD><TD> 
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"1.1"}&v2=$responses{"1.2"}&v3=$responses{"1.3"}&v4=$responses{"1.4"}&v5=$responses{"1.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"1.1"}, Two = $responses{"1.2"}, Three = $responses{"1.3"}, Four = $responses{"1.4"}, Five = $responses{"1.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">2</TD>
	<TD VALIGN="TOP">Timely all-staff e-mail updates in headline format with links to more in-depth information on the SEDL staff intranet</TD>
	<TD VALIGN="TOP">$count_q2</TD><TD> 
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"2.1"}&v2=$responses{"2.2"}&v3=$responses{"2.3"}&v4=$responses{"2.4"}&v5=$responses{"2.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"2.1"}, Two = $responses{"2.2"}, Three = $responses{"2.3"}, Four = $responses{"2.4"}, Five = $responses{"2.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">3</TD>
	<TD VALIGN="TOP">Informational workshops about SEDL programs and divisions that help increase staff's understanding about SEDL's mission and how our work contributes to meeting that mission</TD>
	<TD VALIGN="TOP">$count_q3</TD><TD> 
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"3.1"}&v2=$responses{"3.2"}&v3=$responses{"3.3"}&v4=$responses{"3.4"}&v5=$responses{"3.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"3.1"}, Two = $responses{"3.2"}, Three = $responses{"3.3"}, Four = $responses{"3.4"}, Five = $responses{"3.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">4</TD>
	<TD VALIGN="TOP">A SEDL information card that highlights key facts and core messages about the organization to ensure we are communicating consistent and accurate information</TD>
	<TD VALIGN="TOP">$count_q4</TD><TD> 
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"4.1"}&v2=$responses{"4.2"}&v3=$responses{"4.3"}&v4=$responses{"4.4"}&v5=$responses{"4.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"4.1"}, Two = $responses{"4.2"}, Three = $responses{"4.3"}, Four = $responses{"4.4"}, Five = $responses{"4.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">5</TD>
	<TD VALIGN="TOP">SEDL all-staff meetings that are designed to provide relevant institutional information (e.g., revenue projections, funding opportunities, legislative updates from Capitol Hill, and newly awarded proposals) to all staff members</TD>
	<TD VALIGN="TOP">$count_q5</TD><TD> 
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"5.1"}&v2=$responses{"5.2"}&v3=$responses{"5.3"}&v4=$responses{"5.4"}&v5=$responses{"5.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"5.1"}, Two = $responses{"5.2"}, Three = $responses{"5.3"}, Four = $responses{"5.4"}, Five = $responses{"5.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">6</TD>
	<TD VALIGN="TOP">Staff development sessions on roles and responsibilities of all staff on internal communications such as protocols for dialogue, sharing of timely information, and critical friends groups</TD>
	<TD VALIGN="TOP">$count_q6</TD><TD> 
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"6.1"}&v2=$responses{"6.2"}&v3=$responses{"6.3"}&v4=$responses{"6.4"}&v5=$responses{"6.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"6.1"}, Two = $responses{"6.2"}, Three = $responses{"6.3"}, Four = $responses{"6.4"}, Five = $responses{"6.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">7</TD>
	<TD VALIGN="TOP">Structured  online chats concerning various work-related or organizational topics, problems, or issues</TD>
	<TD VALIGN="TOP">$count_q7</TD><TD> 
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"7.1"}&v2=$responses{"7.2"}&v3=$responses{"7.3"}&v4=$responses{"7.4"}&v5=$responses{"7.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"7.1"}, Two = $responses{"7.2"}, Three = $responses{"7.3"}, Four = $responses{"7.4"}, Five = $responses{"7.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">8</TD>
	<TD VALIGN="TOP">Inquiry meetings organized around a critical question (e.g., internal communications, work-related challenges, organizational issues) that provokes dialogue leading to deeper thinking about goals, issues and solutions and involves staff at multiple levels across the organization</TD>
	<TD VALIGN="TOP">$count_q8</TD><TD> 
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"8.1"}&v2=$responses{"8.2"}&v3=$responses{"8.3"}&v4=$responses{"8.4"}&v5=$responses{"8.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"8.1"}, Two = $responses{"8.2"}, Three = $responses{"8.3"}, Four = $responses{"8.4"}, Five = $responses{"8.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">9</TD>
	<TD VALIGN="TOP">"Critical friends" sessions in which colleagues talk, in a trusting environment, about their work including strengths, weaknesses, what can be improved, and suggestions for how it can be done better</TD>
	<TD VALIGN="TOP">$count_q9</TD><TD> 
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"9.1"}&v2=$responses{"9.2"}&v3=$responses{"9.3"}&v4=$responses{"9.4"}&v5=$responses{"9.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"9.1"}, Two = $responses{"9.2"}, Three = $responses{"9.3"}, Four = $responses{"9.4"}, Five = $responses{"9.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">10</TD>
	<TD VALIGN="TOP" COLSPAN="3">What other communications strategy or strategies would you recommend?
		<P>
		$count_q10</TD></TR>
<TR><TD COLSPAN=4><strong>Increase Communications About Decision Making at SEDL</strong></TD></TR>
<TR><TD VALIGN="TOP">11</TD>
	<TD VALIGN="TOP">Summary of key discussion items, action steps and decision points from SEDL Management Council meetings</TD>
	<TD VALIGN="TOP">$count_q11</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"11.1"}&v2=$responses{"11.2"}&v3=$responses{"11.3"}&v4=$responses{"11.4"}&v5=$responses{"11.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"11.1"}, Two = $responses{"11.2"}, Three = $responses{"11.3"}, Four = $responses{"11.4"}, Five = $responses{"11.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">12</TD>
	<TD VALIGN="TOP">Summary of key discussion items, action steps and decision points from SEDL all-staff meetings</TD>
	<TD VALIGN="TOP">$count_q12</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"12.1"}&v2=$responses{"12.2"}&v3=$responses{"12.3"}&v4=$responses{"12.4"}&v5=$responses{"12.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"12.1"}, Two = $responses{"12.2"}, Three = $responses{"12.3"}, Four = $responses{"12.4"}, Five = $responses{"12.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">13</TD>
	<TD VALIGN="TOP">Regularly scheduled meetings with my team (team members and manager)</TD>
	<TD VALIGN="TOP">$count_q13</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"13.1"}&v2=$responses{"13.2"}&v3=$responses{"13.3"}&v4=$responses{"13.4"}&v5=$responses{"13.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"13.1"}, Two = $responses{"13.2"}, Three = $responses{"13.3"}, Four = $responses{"13.4"}, Five = $responses{"13.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">14</TD>
	<TD VALIGN="TOP">Timely e-mails from my manager on organizational decisions that cannot wait until the next scheduled staff meeting</TD>
	<TD VALIGN="TOP">$count_q14</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"14.1"}&v2=$responses{"14.2"}&v3=$responses{"14.3"}&v4=$responses{"14.4"}&v5=$responses{"14.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"14.1"}, Two = $responses{"14.2"}, Three = $responses{"14.3"}, Four = $responses{"14.4"}, Five = $responses{"14.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">15</TD>
	<TD VALIGN="TOP">(non-manager only) Provide timely e-mails to my manager and team members about issues related to my work that inform our work as a team or may have an impact on organizational decision-making</TD>
	<TD VALIGN="TOP">$count_q15</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"15.1"}&v2=$responses{"15.2"}&v3=$responses{"15.3"}&v4=$responses{"15.4"}&v5=$responses{"15.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"15.1"}, Two = $responses{"15.2"}, Three = $responses{"15.3"}, Four = $responses{"15.4"}, Five = $responses{"15.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">16</TD>
	<TD VALIGN="TOP">Regular open forums with the CEO by role-alike groups (e.g., all administrative staff, communications and information specialists, program staff.)</TD>
	<TD VALIGN="TOP">$count_q16</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"16.1"}&v2=$responses{"16.2"}&v3=$responses{"16.3"}&v4=$responses{"16.4"}&v5=$responses{"16.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"16.1"}, Two = $responses{"16.2"}, Three = $responses{"16.3"}, Four = $responses{"16.4"}, Five = $responses{"16.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">17</TD>
	<TD VALIGN="TOP">Regular open forums with the CEO by division or program area</TD>
	<TD VALIGN="TOP">$count_q17</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"17.1"}&v2=$responses{"17.2"}&v3=$responses{"17.3"}&v4=$responses{"17.4"}&v5=$responses{"17.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"17.1"}, Two = $responses{"17.2"}, Three = $responses{"17.3"}, Four = $responses{"17.4"}, Five = $responses{"17.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">18</TD>
	<TD VALIGN="TOP" COLSPAN="3">What other communications strategy or strategies would you recommend?
		<P>
		$count_q18</TD></TR>
<TR><TD COLSPAN=4><strong>SEDL Staff Newsletter</strong></TD></TR>
<TR><TD VALIGN="TOP">19</TD>
	<TD VALIGN="TOP">General funding information about the organization such as revenue projections, newly acquired grants, and other relevant budget matters</TD>
	<TD VALIGN="TOP">$count_q19</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"19.1"}&v2=$responses{"19.2"}&v3=$responses{"19.3"}&v4=$responses{"19.4"}&v5=$responses{"19.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"19.1"}, Two = $responses{"19.2"}, Three = $responses{"19.3"}, Four = $responses{"19.4"}, Five = $responses{"19.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">20</TD>
	<TD VALIGN="TOP">Information about SEDL's organizational plans such as updates on the strategic planning process, options related to the building lease, and other internal issues that affect the organization</TD>
	<TD VALIGN="TOP">$count_q20</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"20.1"}&v2=$responses{"20.2"}&v3=$responses{"20.3"}&v4=$responses{"20.4"}&v5=$responses{"20.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"20.1"}, Two = $responses{"20.2"}, Three = $responses{"20.3"}, Four = $responses{"20.4"}, Five = $responses{"20.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">21</TD>
	<TD VALIGN="TOP">Job advancement opportunities</TD>
	<TD VALIGN="TOP">$count_q21</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"21.1"}&v2=$responses{"21.2"}&v3=$responses{"21.3"}&v4=$responses{"21.4"}&v5=$responses{"21.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"21.1"}, Two = $responses{"21.2"}, Three = $responses{"21.3"}, Four = $responses{"21.4"}, Five = $responses{"21.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">22</TD>
	<TD VALIGN="TOP">A profile of a SEDL employee</TD>
	<TD VALIGN="TOP">$count_q22</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"22.1"}&v2=$responses{"22.2"}&v3=$responses{"22.3"}&v4=$responses{"22.4"}&v5=$responses{"22.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"22.1"}, Two = $responses{"22.2"}, Three = $responses{"22.3"}, Four = $responses{"22.4"}, Five = $responses{"22.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">23</TD>
	<TD VALIGN="TOP">A monthly story of a SEDL project and how it relates to SEDL's mission</TD>
	<TD VALIGN="TOP">$count_q23</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"23.1"}&v2=$responses{"23.2"}&v3=$responses{"23.3"}&v4=$responses{"23.4"}&v5=$responses{"23.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"23.1"}, Two = $responses{"23.2"}, Three = $responses{"23.3"}, Four = $responses{"23.4"}, Five = $responses{"23.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">24</TD>
	<TD VALIGN="TOP">Regular announcements about personnel changes</TD>
	<TD VALIGN="TOP">$count_q24</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"24.1"}&v2=$responses{"24.2"}&v3=$responses{"24.3"}&v4=$responses{"24.4"}&v5=$responses{"24.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"24.1"}, Two = $responses{"24.2"}, Three = $responses{"24.3"}, Four = $responses{"24.4"}, Five = $responses{"24.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">25</TD>
	<TD VALIGN="TOP" COLSPAN="3">What other information would you be interested in reading about in a SEDL staff newsletter?
		<P>$count_q25</TD></TR>
<TR><TD COLSPAN=4><strong>SEDL Intranet</strong></TD></TR>
<TR><TD VALIGN="TOP">26</TD>
	<TD VALIGN="TOP">SEDL talking points on an as-needed basis regarding topics such as existing projects, new initiatives, and research findings</TD>
	<TD VALIGN="TOP">$count_q26</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"26.1"}&v2=$responses{"26.2"}&v3=$responses{"26.3"}&v4=$responses{"26.4"}&v5=$responses{"26.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"26.1"}, Two = $responses{"26.2"}, Three = $responses{"26.3"}, Four = $responses{"26.4"}, Five = $responses{"26.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">27</TD>
	<TD VALIGN="TOP">A staff-only online discussion area to highlight potential opportunities for new work and product development in SEDL states</TD>
	<TD VALIGN="TOP">$count_q27</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"27.1"}&v2=$responses{"27.2"}&v3=$responses{"27.3"}&v4=$responses{"27.4"}&v5=$responses{"27.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"27.1"}, Two = $responses{"27.2"}, Three = $responses{"27.3"}, Four = $responses{"27.4"}, Five = $responses{"27.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">28</TD>
	<TD VALIGN="TOP">A summary of key discussion items, action steps and decision points from all-staff meetings</TD>
	<TD VALIGN="TOP">$count_q28</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"28.1"}&v2=$responses{"28.2"}&v3=$responses{"28.3"}&v4=$responses{"28.4"}&v5=$responses{"28.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"28.1"}, Two = $responses{"28.2"}, Three = $responses{"28.3"}, Four = $responses{"28.4"}, Five = $responses{"28.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">29</TD>
	<TD VALIGN="TOP">A summary of key discussion items, action steps and decision points from SEDL Management Council Meetings</TD>
	<TD VALIGN="TOP">$count_q29</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"29.1"}&v2=$responses{"29.2"}&v3=$responses{"29.3"}&v4=$responses{"29.4"}&v5=$responses{"29.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"29.1"}, Two = $responses{"29.2"}, Three = $responses{"29.3"}, Four = $responses{"29.4"}, Five = $responses{"29.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">30</TD>
	<TD VALIGN="TOP">Information about the fiscal health of the organization</TD>
	<TD VALIGN="TOP">$count_q30</TD><TD>
		<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/oic_survey_barchart.cgi?v1=$responses{"30.1"}&v2=$responses{"30.2"}&v3=$responses{"30.3"}&v4=$responses{"30.4"}&v5=$responses{"30.5"}" 
			ALIGN=RIGHT TITLE="Responses: One = $responses{"30.1"}, Two = $responses{"30.2"}, Three = $responses{"30.3"}, Four = $responses{"30.4"}, Five = $responses{"30.5"}">
	</TD></TR>
<TR><TD VALIGN="TOP">31</TD>
	<TD VALIGN="TOP" COLSPAN="3">What other information would you be interested in receiving on the SEDL staff intranet?
		<P>
		$count_q31</TD></TR>
<TR><TD COLSPAN=4><strong>Your Role in Improving Internal Communications</strong></TD></TR>
<TR><TD VALIGN="TOP">32</TD>
	<TD VALIGN="TOP" COLSPAN="3">What personal role do you see yourself playing to improve internal communications at SEDL?
		<P>
		$count_q32</TD></TR>

</TABLE>
<P>
EOM
}
######################################################################################
## END: LOCATION = ADMIN
######################################################################################



######################################################################################
## START: LOCATION = SEND_SURVEY
######################################################################################
if ($location eq 'send_survey') {
print <<EOM;
<h1>SEDL ALL-STAFF SURVEY<BR>
INTERNAL COMMUNICATIONS 
</h1>
<P>
<form action="oic_survey.cgi" method=POST>

<P>
<H2>Sending Survey to Staff</H2>
EOM

my $command = "select * from survey_oic_2004";
   $command .= " where user_id like 'blitke'";
   $command .= " order by user_id";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
	my $num_matches = $sth->rows;

## DECLARE TRACKING VARIABLES AND
## OPEN BULLETED LIST FOR TEXT FIELDS
my $count_q1 = "0";
my $count_q2 = "0";
my $count_q3 = "0";
my $count_q4 = "0";
my $count_q5 = "0";
my $count_q6 = "0";
my $count_q7 = "0";
my $count_q8 = "0";
my $count_q9 = "0";
my $count_q10 = "<UL>";
my $count_q11 = "0";
my $count_q12 = "0";
my $count_q13 = "0";
my $count_q14 = "0";
my $count_q15 = "0";
my $count_q16 = "0";
my $count_q17 = "0";
my $count_q18 = "<UL>";
my $count_q19 = "0";
my $count_q20 = "0";
my $count_q21 = "0";
my $count_q22 = "0";
my $count_q23 = "0";
my $count_q24 = "0";
my $count_q25 = "<UL>";
my $count_q26 = "0";
my $count_q27 = "0";
my $count_q28 = "0";
my $count_q29 = "0";
my $count_q30 = "0";
my $count_q31 = "<UL>";
my $count_q32 = "<UL>";

print<<EOM;
There are $num_matches SEDL staff on file.
EOM
	while (my @arr = $sth->fetchrow) {
		my ($serial_num, $user_id, $survey_date, $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $q10, $q11, $q12, $q13, $q14, $q15, $q16, $q17, $q18, $q19, $q20, $q21, $q22, $q23, $q24, $q25, $q26, $q27, $q28, $q29, $q30, $q31, $q32) = @arr;
			if ($survey_date =~ '2004') {
			} else {
			
			# GET STAFF MEMBER'S REAL NAME
			my $this_name = "";
				my $command = "select firstname, lastname from staff_profiles where (userid LIKE '$user_id')";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
				my $num_matches = $sth->rows;

					while (my @arr = $sth->fetchrow) {
    					my ($firstname, $lastname) = @arr;
    					$this_name = "$firstname $lastname";
    				}
			# SEND REMINDER E-MAIL
			print "<BR>Sending survey to: $this_name ($user_id\@sedl.org\)";
			
			################################################
## START: SEND E-MAIL IF THERE ARE ERRORS
################################################
my $mailprog = "/usr/sbin/sendmail -t"; #No -n because of webmaster alias
my $recipient = "$this_name <$user_id\@sedl.org>";


# COMMENT THIS LINE OUT WHEN READY TO SEND TO ALL STAFF
#$recipient = "blitke\@sedl.org"; 



my $fromaddr = "Office of Communications <webmaster\@sedl.org>";
my $emailsubject = "SEDL All-Staff Survey: Internal Communications";
my $replyto = "Brian Litke <blitke\@sedl.org>";

open(NOTIFY,"| $mailprog");

print NOTIFY <<EOM;
From: $fromaddr
To: $recipient
Reply-To: $replyto
Subject: $emailsubject
MIME-Version: 1.0
Content-type: text/html; charset=iso-8859-1

<HTML><BODY>
<P>
Dear $this_name,
<P>
We're moving into the next phase of SEDL's internal communications audit--an online survey. We want to test some of the ideas for communications strategies that we've heard about in the focus groups and interviews. We're also taking the opportunity to test some practices that were shared by members of our advisory roundtable, other staff and managers, and our consultants. 
<P>
Please take a few moments to complete the internal communications survey by following the link below. Your feedback will be used to inform the internal communications plan. <FONT COLOR=RED>The deadline for completing the survey is CoB, 
September 7.</FONT> 
<P>
We realize the timeline is short, particularly given the holiday weekend, but we invite you to respond as soon as you can. Your responses will remain anonymous, but Brian will be able to track who has responded to the survey. We structured the survey this way so we can send a personal reminder if we have not heard from you as the deadline approaches. We are striving for a 100 percent response rate.
<P> 
We want to send a special thank you to those of you who participated in focus groups, gave individual interviews, and reviewed the drafts of the survey. Your insights and feedback helped shape the items. 
<P>
Please let us know if you have any questions by contacting Joyce or Lesley Dahlkemper of Schoolhouse Communications at lesley\@schoolhousecom.com or by calling 303-987-1535.
<P>
Be sure to look for the survey results on the staff intranet in mid September.
<P>
This has been a challenging project, but I think it will gives rewarding results. Thanks again for your interest and participation.
<P>
Please follow this link to fill out the <A HREF="http://www.sedl.org/staff/oic_survey.cgi">SEDL All-Staff Survey: Internal Communications</A>.
<P>
Joyce
<P>
<BR>
<BR>
If no link appears in the text above, you can access the survey at: http://www.sedl.org/staff/oic_survey.cgi
</BODY>
</HTML>

EOM
close(NOTIFY);

			} # END IF/ELSE

	} # END DB QUERY LOOP

}
######################################################################################
## END: LOCATION = SEND_SURVEY
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


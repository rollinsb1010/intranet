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
use Number::Format; # ROUNDS NUMBERS TO SPECIFIC DECIMAL PLACE
use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;
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

my $uniqueid = param('uniqueid');

my $location = param('location');
   $location = "logon" if $location eq '';

my $showsession = param('showsession');

my $leavelastupdated = "";

my $error_message = "";
my $show_month = $query->param("show_month");

my $print = $query->param("print");

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

$htmlhead .= "<TABLE CELLPADDING=\"15\"><TR><TD>";
$htmltail = "</td></tr></table>$htmltail";

if ($print eq 'yes') {
	$htmlhead = "<HTML>
<HEAD><TITLE>SEDL Staff Pages - Personnel: Your Leave Report</TITLE>
<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">
<link href=\"/staff/includes/staff2006.css\" rel=\"stylesheet\" type=\"text/css\">

<SCRIPT LANGUAGE=\"JavaScript\">
<!-- Begin
function Start(u, l, t, w, h)  {
var windowprops = \"location=no,scrollbars=yes,menubars=yes,toolbars=yes,resizable=yes\" +
\",left=\" + l + \",top=\" + t + \",width=\" + w + \",height=\" + h;
window.open(u,\"popup\",windowprops);
}
// End -->
</SCRIPT>


</HEAD>
<BODY BGCOLOR=\"#ffffff\">
<TABLE CELLPADDING=\"15\"><TR><TD>";
	$htmltail = "</td></tr></table></body></html>";
}
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
				$location = "leavereport_menu";

		} else {
			## INVALID ID/PASS: SHOW LOON SCREEN & SET ERROR MESSAGE
			if ($num_matches_for_logon_id_entered == 1) {
				$error_message = "Your User ID ($logon_user) is valid, but the password ($logon_pass) you entered did not match the one on file.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
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
#my $num_matches = $sth->rows;
		
			## SAVE COOKIES WITH SESSION ID AND USER ID
				setCookie ("ss_session_id", $ss_session_id, $expdate, $path, $thedomain);
#				setCookie ("staffid", $ss_staff_id, $expdate, $path, $thedomain);
			## SET LOCATION
				$location = "leavereport_menu" if ($location ne 'leavereport');

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





####################################################################
## START: LOG STAFF ID TO DATABASE TO TRACK USAGE OF THIS RESOURCE
####################################################################
## IF STAFF USER ID IS PRESENT IN COOKIE, LOG THEIR USE OF THIS TOOL TO THE TRACKING DATABASE
if (($cookie_ss_staff_id ne '') && ($location ne 'logon')) {
	my $commandinsert = "INSERT INTO staffpageusage VALUES ('$cookie_ss_staff_id', '$date_full_mysql', 'Your Leave Report')";
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





#################################################################################
## START: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################
if ($location eq 'logon') {

## PRINT SIGNUP FORM
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Staff Pages - Personnel: Your Leave Report</TITLE>
$htmlhead
<h3>Your Leave Report</h3>
EOM

print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');

print<<EOM;      
<p>
Welcome to Your Leave Report.  This report allows you to check on the amount of Sick, Personal, and Vacation time you 
have available (the data is updated first of each month).  
Managers will see their own leave report and the reports for the staff members they supervise.
</p>
<p>
Please enter your SEDL user ID (ex: whoover) and password to view your leave report.
</p>
<FORM ACTION="/staff/personnel/leavereport.cgi" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">


  <TR><TD VALIGN="TOP"><strong>Your user ID</strong> (ex: whoover)</TD>
      <TD VALIGN="TOP">
      <INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id">
      </TD></TR>
  <TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR><SPAN class=small>(not your e-mail password)</SPAN></TD>
      <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD></TR>
</TABLE>

  <UL>
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Show My Leave Report">
  </UL>
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





#################################################################################
## START: LOCATION = LEAVEREPORT_MENU
#################################################################################
if ($location eq 'leavereport_menu') {

	my ($prettyname, $usertimesheetnameholder, $useridholder) = &get_staff_fullname($cookie_ss_staff_id);

## PRINT SIGNUP FORM
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Staff Pages - Personnel: Your Leave Report for $prettyname</TITLE>
$htmlhead
            <h3>View Leave Report for $prettyname: Select a Month<BR>(Click here to <A HREF="leavereport.cgi?location=logout">logout</A>)</h3>
EOM

print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');

if (($cookie_ss_staff_id eq 'lforador') || ($cookie_ss_staff_id eq 'akriegel') || ($cookie_ss_staff_id eq 'blitke') || ($cookie_ss_staff_id eq 'sferguso')) {
	print "<P class=\"info\"><strong>(Visible only to Arnold, Lori, and Brian)</strong> 
	Click here to <A HREF=\"/staff/personnel/leavereport-warning.cgi\">view a list of staff who are close to their maximum vacation accrual</A>. 
	The page will offer you the option to send an automated e-mail to those staff reminding them to use their time.
	<br><br>
	Click here to view a <a href=\"http://www.sedl.org/staff/personnel/leavereport-userid-check.cgi?show_onscreen=yes\">list of staff whose Accounting System name does not match up with the Leave Report names</a>.
	</p>";
}

print<<EOM;
<p>
Click on a month from the list below to see a detailed report for that month (or to see the leave report details for other staff if you have responsibility for monitoring other staff members' leave.)
</p>
<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0">
<TR><TD ROWSPAN="2"><strong>Month</strong></TD>
	<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#EBEBEB"><strong>Leave Available at the End of the Month</strong></TD>
	<TD COLSPAN="3" ALIGN="CENTER" BGCOLOR="#E8E8C8"><strong>Leave Taken During Month</strong></TD>
</TR>
<TR><TD ALIGN="CENTER" BGCOLOR="#EBEBEB"><strong>Vacation</strong></TD>
	<TD ALIGN="CENTER" BGCOLOR="#EBEBEB"><strong>Sick</strong></TD>
	<TD ALIGN="CENTER" BGCOLOR="#EBEBEB"><strong>Personal</strong></TD>
	<TD ALIGN="CENTER" BGCOLOR="#E8E8C8"><strong>Vacation</strong></TD>
	<TD ALIGN="CENTER" BGCOLOR="#E8E8C8"><strong>Sick</strong></TD>
	<TD ALIGN="CENTER" BGCOLOR="#E8E8C8"><strong>Personal</strong></TD>
</TR>
EOM

my $previous_filelastupdated = "";
   $usertimesheetnameholder =~ s/\\\'/%/g; # IFX FOR D'ETTE COWAN'S NAME THAT CONTAINS AN APOSTRAPHE NOT ENCODED IN THE DATABASE PROPERLY

my $command = "select * from staffleavereport where timesheetname like '$usertimesheetnameholder' order by timesheetname, leavelastupdated DESC";
	my $dsn = "DBI:mysql:database=test;host=localhost";
	my $dbh = DBI->connect($dsn);
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	if ($num_matches == 0) {
		print "<P><FONT COLOR=RED>Your Accounting System Name ($usertimesheetnameholder) has no leave reports on file.</FONT>";
	}

	while (my @arr = $sth->fetchrow) {
		my ($uniqueid, $timesheetname, $ssn, $manager, $departmentid, $vacaccrualfactor, $vacaccruedtodate, $vacearnedcurrent, $vacusedcurrent, $vacusedtodate, $vacbalance, $sickaccrualfactor, $sickaccruedtodate, $sickearnedcurrent, $sickusedcurrent, $sickusedtodate, $sickbalance, $persaccrualfactor, $persaccruedtodate, $persearnedcurrent, $persusedcurrent, $persusedtodate, $persbalance, $filelastupdated) = @arr;
			my $year_updated = substr($filelastupdated, 0,4);
			my $month_updated = substr($filelastupdated, 4,2);
			my $pretty_filelastupdated = "$month_updated\/$year_updated";
			if ($filelastupdated ne $previous_filelastupdated) {
				my $persbalance_label = &hours2days($persbalance);
					$persbalance_label = "<FONT COLOR=#000099>0 hr.</FONT>" if ($persbalance eq '0.00');
				my $vacbalance_label = &hours2days($vacbalance);
					$vacbalance_label = "<FONT COLOR=#000099>0 hr.</FONT>" if ($vacbalance eq '0.00');
				my $sickbalance_label = &hours2days($sickbalance);
					$sickbalance_label = "<FONT COLOR=#000099>0 hr.</FONT>" if ($sickbalance eq '0.00');
				print "<TR><TD><a HREF=\"leavereport.cgi?location=leavereport&show_month=$filelastupdated\">month of $pretty_filelastupdated</a></TD>
							<TD ALIGN=\"RIGHT\">$vacbalance_label</TD>
							<TD ALIGN=\"RIGHT\">$sickbalance_label</TD>
							<TD ALIGN=\"RIGHT\">$persbalance_label</TD>
							<TD ALIGN=\"RIGHT\" BGCOLOR=\"#FCFCE3\">$vacusedcurrent</TD>
							<TD ALIGN=\"RIGHT\" BGCOLOR=\"#FCFCE3\">$sickusedcurrent</TD>
							<TD ALIGN=\"RIGHT\" BGCOLOR=\"#FCFCE3\">$persusedcurrent</TD>
						</TR>";
			}
		$previous_filelastupdated = $filelastupdated;	
	}



print<<EOM;
</TABLE>
<p>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
$htmltail
EOM
} 
#################################################################################
## END: LOCATION = LEAVEREPORT_MENU
#################################################################################








#################################################################################
## START: PRINT THE USER'S LEAVE REPORT IF USER ID AND PASSWORD ARE VALID
#################################################################################
if ($location eq 'leavereport') {


## PRINT PAGE HEADER
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Staff Pages - Personnel: Your Leave Report</TITLE>
$htmlhead
EOM
if ($print ne 'yes') {
print<<EOM;
<p class="small">
Contact Brian Litke at <A HREF="mailto:blitke\@sedl.org">blitke\@sedl.org</A> for assistance, if your leave report does not display.
</p>
EOM
}
my ($prettyname, $usertimesheetnameholder, $useridholder) = &get_staff_fullname($cookie_ss_staff_id);

   $usertimesheetnameholder =~ s/\\\'/%/g; # FIX FOR D'ETTE COWAN'S NAME THAT CONTAINS AN APOSTRAPHE NOT ENCODED IN THE DATABASE PROPERLY


my $showdepartment ="";
my $showdepartmentid = "";

my $command = "select * from staffleavereport where 
			timesheetname like '$usertimesheetnameholder' 
			AND leavelastupdated LIKE '$show_month' order by timesheetname";

#print "$command<P>";
	my $dsn = "DBI:mysql:database=test;host=localhost";
	my $dbh = DBI->connect($dsn);
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;

	if ($num_matches == 0) {
		my $show_month_label_requested = substr($show_month,4,2) . "/".substr($show_month,0,4);

		my $command = "select leavelastupdated from staffleavereport where 
			timesheetname like '$usertimesheetnameholder' order by leavelastupdated DESC limit 1";
		my $dsn = "DBI:mysql:database=test;host=localhost";
		my $dbh = DBI->connect($dsn);
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#		print "<p class=\"info\">COMMAND: $command</P>";
			while (my @arr = $sth->fetchrow) {
				my ($filelastupdated) = @arr;
				$show_month = $filelastupdated;
			} # END DB QUERY LOOP
		my $show_month_label = substr($show_month,4,2) . "/".substr($show_month,0,4);
		print "<p class=\"info\">You are unable to view the leave report for the current month ($show_month_label_requested), 
		because that data is not processed to the intranet until the end of the month.  Showing the most recent month's leave report ($show_month_label) instead.</p>";

	} # END IF

my $command = "select * from staffleavereport where 
			timesheetname like '$usertimesheetnameholder' 
			AND leavelastupdated LIKE '$show_month' order by timesheetname";

#print "<p class=\"info\">COMMAND: $command</P>";
	my $dsn = "DBI:mysql:database=test;host=localhost";
	my $dbh = DBI->connect($dsn);
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;


	while (my @arr = $sth->fetchrow) {
		my ($uniqueid, $timesheetname, $ssn, $manager, $departmentid, $vacaccrualfactor, $vacaccruedtodate, $vacearnedcurrent, $vacusedcurrent, $vacusedtodate, $vacbalance, $sickaccrualfactor, $sickaccruedtodate, $sickearnedcurrent, $sickusedcurrent, $sickusedtodate, $sickbalance, $persaccrualfactor, $persaccruedtodate, $persearnedcurrent, $persusedcurrent, $persusedtodate, $persbalance, $filelastupdated) = @arr;


		$leavelastupdated = $filelastupdated;
		$showdepartmentid = "$departmentid";
		$showdepartment = "yes" if (($manager eq 'Y') || ($manager eq 'y'));

		$showdepartment = "yes" if ($useridholder eq 'afrenzel');
		$showdepartment = "yes" if ($useridholder eq 'hwilliam');
		$showdepartment = "yes" if ($useridholder eq 'jwaisath');
		$showdepartment = "yes" if ($useridholder eq 'lforador');
		$showdepartment = "yes" if ($useridholder eq 'lking');
		$showdepartment = "yes" if ($useridholder eq 'mjackson');
		$showdepartment = "yes" if ($useridholder eq 'pramirez');
		$showdepartment = "yes" if ($useridholder eq 'sliberty');
		$showdepartment = "yes" if ($useridholder eq 'srios');
		$showdepartment = "yes" if ($useridholder eq 'mturner');
		$showdepartment = "yes" if ($useridholder eq 'tmoreno');
	} # END DB QUERY LOOP

my ($d1, $d2, $d3, $d4, $d5, $d6, $d7, $d8) = split(//,$leavelastupdated);
my $prettyleavelastupdated = "$d5$d6\-$d7$d8\-$d1$d2$d3$d4";
my $prettyleavelastupdated_noday = "$d5$d6\/$d1$d2$d3$d4";


	if ($num_matches > 0) {
print<<EOM;
            <h3>Leave Report for $prettyname (Click here to <A HREF="leavereport.cgi?location=logout">logout</A>)</h3>
            <p>Below, you will see your leave report for $prettyleavelastupdated_noday <SPAN CLASS=SMALL>(last updated $prettyleavelastupdated)</SPAN>.<BR>
            Click here to <A HREF="leavereport.cgi?location=leavereport_menu">view another month</A>
EOM

		if ($print ne 'yes') {
			print "<P><em>Since you are a manager, you will see your leave report and the leave reports for those employees in your division.</em>" if (($showdepartment eq 'yes') && (($cookie_ss_staff_id ne 'jbuttram') && ($cookie_ss_staff_id ne 'whoover')));  
			print "<P><em>Since you are the CEO or COO, you will see your leave report and the leave reports for those employees in your division.</em>" if (($cookie_ss_staff_id eq 'jbuttram') || ($cookie_ss_staff_id eq 'whoover'));  
		}



my $command = "select * from staffleavereport where timesheetname like '$usertimesheetnameholder' AND leavelastupdated LIKE '$show_month' order by timesheetname";
    if ($showdepartment eq 'yes') {
    	$command = "select * from staffleavereport where departmentid like '$showdepartmentid' AND leavelastupdated LIKE '$show_month' order by manager DESC, timesheetname";
	}
    if ($cookie_ss_staff_id eq 'dcowan') {
    	$command = "select * from staffleavereport where 
    	((timesheetname like '%cowan%') 
    	|| (timesheetname like '%molina%') 
    	|| (timesheetname like '%neeley%') 
    	|| (timesheetname like '%stella%') 
    	|| (timesheetname like '%joyner%')) 
    	AND leavelastupdated LIKE '$show_month' order by timesheetname";
	}
    if ($cookie_ss_staff_id eq 'tmoreno') {
    	$command = "select * from staffleavereport where 
    	((timesheetname like '%loretta%') 
    	|| (timesheetname like '%finlay%') 
    	|| (timesheetname like '%moreno%')) 
    	AND leavelastupdated LIKE '$show_month' order by timesheetname";
	}
    if ($cookie_ss_staff_id eq 'dbrown') {
    	$command = "select * from staffleavereport where 
    	((timesheetname like '%loretta%') 
    	|| (timesheetname like '%finlay%') 
    	|| (timesheetname like '%brown%')
	   	|| (timesheetname like '%meibaum%')
	   	|| (timesheetname like '%meadow%')
 	  	|| (timesheetname like '%theodore%')
 	  	|| (timesheetname like '%copeland%')
 	  	|| (timesheetname like '%moreno%')
 	  	|| (timesheetname like '%chauvin%')
 	  	|| (timesheetname like '%times%')
 	  	|| (timesheetname like '%madison%')
 	  	|| (timesheetname like '%beth%')
    	) 
    	AND leavelastupdated LIKE '$show_month' order by timesheetname";
	}
    if ($cookie_ss_staff_id eq 'hwilliam') {
    	$command = "select * from staffleavereport where 
    	((timesheetname like '%HAIDEE%') 
     	|| (timesheetname like '%BALDWIN%') 
 	   	|| (timesheetname like '%BECKWITH%') 
	   	|| (timesheetname like '%BURNISK%') 
	   	|| (timesheetname like '%JOYNER%') 
    	|| (timesheetname like '%MABUS%')
    	|| (timesheetname like '%MUONEKE%') 
    	|| (timesheetname like '%NEELEY%') 
	   	|| (timesheetname like '%PIRTLE%') 
	   	|| (timesheetname like '%QUIROZ%') 
    	|| (timesheetname like '%TORRES%')) 
    	AND leavelastupdated LIKE '$show_month' order by timesheetname";
  	$command = "select * from staffleavereport where departmentid like 'ISP'";

	}
    if ($cookie_ss_staff_id eq 'vdimock') {
    	$command = "select * from staffleavereport where 
    	((timesheetname like '%DIMOCK%') 
     	|| (timesheetname like '%WATERS%') 
     	|| (timesheetname like '%VADEN%') 
     	|| (timesheetname like '%JARVIS%') 
 	   	|| (timesheetname like '%WESTBRO%') 
	   	|| (timesheetname like '%LUIS%') 
	   	|| (timesheetname like '%JORDAN%')) 
    	AND leavelastupdated LIKE '$show_month' order by timesheetname";
	}
	## SHOW ALL STAFF FOR PEOPLE WHO HAVE PERMISSION TO
	if (($cookie_ss_staff_id eq 'whoover') || 
		($cookie_ss_staff_id eq 'akriegel') || 
		($cookie_ss_staff_id eq 'sdunn') || 
		($cookie_ss_staff_id eq 'sliberty') || 
		($cookie_ss_staff_id eq 'thoes') || 
		($cookie_ss_staff_id eq 'lforador')) {
		$command = "select * from staffleavereport WHERE leavelastupdated LIKE '$show_month' order by timesheetname";
	}
#	if ($cookie_ss_staff_id eq 'vdimock') {
#		$command = "select * from staffleavereport where 
#				((departmentid like 'D') OR 
#				(departmentid like 'H') OR 
#				(departmentid like 'C') 
#				)  AND leavelastupdated LIKE '$show_month' order by departmentid, timesheetname";
#	}
#	if (($cookie_ss_staff_id eq 'ghayman') || ($cookie_ss_staff_id eq 'gretchen')) {
#		$command = "select * from staffleavereport where 
#				((timesheetname like 'HAYMAN GRETCHEN L') OR 
#				(timesheetname like 'LE DUNG N'))  AND leavelastupdated LIKE '$show_month' order by timesheetname";
#	}
	if ($cookie_ss_staff_id eq 'srios') {
		$command = "select * from staffleavereport where 
				(timesheetname like 'RODRIGUEZ SANDRA V') OR 
				(timesheetname like 'HOOVER WESLEY A')  AND leavelastupdated LIKE '$show_month' order by timesheetname";
	}
#print "$command";

	my $dsn = "DBI:mysql:database=test;host=localhost";
	my $dbh = DBI->connect($dsn);
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;

print<<EOM;
<p></p>
<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0" style="background:#ffffff;">
<TR><TD ROWSPAN="2">Name</TD>
	<TD COLSPAN="4" ALIGN="CENTER"><strong>Vacation</strong></TD>
	<TD COLSPAN="4" ALIGN="CENTER"><strong>Sick</strong></TD>
	<TD COLSPAN="4" ALIGN="CENTER"><strong>Personal</strong></TD></TR>
<TR><TD>Accrued<BR>ETD</TD>
	<TD>Earned<BR>Current</TD>
	<TD>Used<BR>Current</TD>
	<TD BGCOLOR=E8DAB5>Balance</TD>
	<TD>Accrued<BR>ETD</TD>
	<TD>Earned<BR>Current</TD>
	<TD>Used<BR>Current</TD>
	<TD BGCOLOR=E8DAB5>Balance</TD>
	<TD>Proj. Ann.<BR>Accrual<BR>YTD</TD>
	<TD>Used<BR>Current</TD>
	<TD BGCOLOR=E8DAB5>Balance</TD></TR>
EOM

	my $dsn = "DBI:mysql:database=test;host=localhost";
	my $dbh = DBI->connect($dsn);
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
my $count = "0";
my $lastdepartmentid = "";

	while (my @arr = $sth->fetchrow) {
		my ($uniqueid, $timesheetname, $ssn, $manager, $departmentid, $vacaccrualfactor, $vacaccruedtodate, $vacearnedcurrent, $vacusedcurrent, $vacusedtodate, $vacbalance, $sickaccrualfactor, $sickaccruedtodate, $sickearnedcurrent, $sickusedcurrent, $sickusedtodate, $sickbalance, $persaccrualfactor, $persaccruedtodate, $persearnedcurrent, $persusedcurrent, $persusedtodate, $persbalance) = @arr;

		my $timesheetname_link = $timesheetname;
			$timesheetname_link =~ s/\ /\+/g;

		$count++;
		if ($vacaccruedtodate ne '') {
			my $persbalance_label = &hours2days($persbalance);
			my $vacbalance_label = &hours2days($vacbalance);

			my $showhowclose = "";
			my $maxhours = $vacaccrualfactor * 160;
			my $howclose = $maxhours - $vacbalance;
				$showhowclose = "yes" if (($howclose < 26) && ($maxhours eq '160') && ($maxhours ne '0'));
				$showhowclose = "yes" if (($howclose < 40) && ($maxhours eq '240') && ($maxhours ne '0'));
			my $rowspan = "";
				$rowspan = " ROWSPAN=2" if $showhowclose;

			$timesheetname =~ s/ /\<BR\>/;   
print<<EOM;
<TR VALIGN="TOP"><TD class=small $rowspan><A HREF=\"/cgi-bin/mysql/ChartDirector/intranet/leavereport-stackedbar-small.cgi?staff_id=$timesheetname_link&vacation_balance=$vacbalance&vacation_used_etd=$vacusedtodate&vacation_used_current=$vacusedcurrent&sick_balance=$sickbalance&sick_used_etd=$sickusedtodate&sick_used_current=$sickusedcurrent&personnel_balance=$persbalance&personnel_used_etd=$persusedtodate&personnel_used_current=$persusedcurrent\">$timesheetname</A></TD>
	<TD class="small">$vacaccruedtodate <span class="text_grey">($vacusedtodate used ETD)</span></TD>
	<TD class="small">$vacearnedcurrent</TD>
	<TD class="small">$vacusedcurrent</TD>
	<TD class="small" BGCOLOR="#E8DAB5">$vacbalance<BR>$vacbalance_label</TD>
	<TD class="small">$sickaccruedtodate <span class="text_grey">($sickusedtodate used ETD)</span></TD>
	<TD class="small">$sickearnedcurrent</TD>
	<TD class="small">$sickusedcurrent</TD>
	<TD class="small" BGCOLOR="#E8DAB5">$sickbalance</TD>
	<TD class="small">$persaccruedtodate <span class="text_grey">($persusedtodate used YTD)</span></TD>
	<TD class="small">$persusedcurrent</TD>
	<TD class="small" BGCOLOR="#E8DAB5">$persbalance<BR>$persbalance_label</TD></TR>
EOM
			if ($showhowclose ne '') {
				print "<TR><TD colspan=\"14\"><span class=\"small text_red\">Within ";
				printf "%3.2f\n", $howclose;
				print " hours of maximum accrual of $maxhours</span></TD></TR>\n";
			}
		} # END IF VACACCRUEDTODATE NOT BLANK
		$lastdepartmentid = $departmentid if ($lastdepartmentid eq '');


		if (($count eq '20') || ($count eq '40') || ($count eq '60') || ($count eq '80') || ($count eq '100') ) {
print<<EOM;
<TR><TD ROWSPAN="2">Name</TD>
	<TD COLSPAN="5" ALIGN="CENTER"><strong>Vacation</strong></TD>
	<TD COLSPAN="5" ALIGN="CENTER"><strong>Sick</strong></TD>
	<TD COLSPAN="5" ALIGN="CENTER"><strong>Personal</strong></TD></TR>
<TR><TD>Accrued<BR>ETD</TD>
	<TD>Earned<BR>Current</TD>
	<TD>Used<BR>Current</TD>
	<TD BGCOLOR="#E8DAB5">Balance</TD>
	<TD>Accrued<BR>ETD</TD>
	<TD>Earned<BR>Current</TD>
	<TD>Used<BR>Current</TD>
	<TD BGCOLOR="#E8DAB5">Balance</TD>
	<TD>Proj. Ann.<BR>Accrual<BR>ETD</TD>
	<TD>Used<BR>Current</TD>
	<TD BGCOLOR="#E8DAB5">Balance</TD></TR>
EOM
		} # END IF

		$lastdepartmentid = $departmentid;

		if ($num_matches eq '1') {
			$prettyname =~ s/\ /\+/g;
print<<EOM;
</TABLE><P></P>
<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/leavereport-stackedbar-small-separatereports.cgi?chart_type=used&staff_id=$prettyname&last_updated=$prettyleavelastupdated&vacation_balance=$vacbalance&vacation_used_etd=$vacusedtodate&vacation_used_current=$vacusedcurrent&sick_balance=$sickbalance&sick_used_etd=$sickusedtodate&sick_used_current=$sickusedcurrent&personnel_balance=$persbalance&personnel_used_etd=$persusedtodate&personnel_used_current=$persusedcurrent" ALT="Chart showing leave hours used last month" TITLE="Chart showing leave hours used last month">
<IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/leavereport-stackedbar-small-separatereports.cgi?chart_type=remaining&staff_id=$prettyname&last_updated=$prettyleavelastupdated&vacation_balance=$vacbalance&vacation_used_etd=$vacusedtodate&vacation_used_current=$vacusedcurrent&sick_balance=$sickbalance&sick_used_etd=$sickusedtodate&sick_used_current=$sickusedcurrent&personnel_balance=$persbalance&personnel_used_etd=$persusedtodate&personnel_used_current=$persusedcurrent" ALT="Chart showing your remaining leave" TITLE="Chart showing your remaining leave">
EOM
		}

	} # END DB QUERY LOOP


print "</TABLE>" if ($num_matches ne '1');
print <<EOM;
$htmltail
EOM
	} # END IF NUM MATCHES > 0

}
#################################################################################
## END: PRINT THE USER'S LEAVE REPORT IF USER ID IS OK
#################################################################################





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
   $dirtyitem =~ s/\\//g;
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/Ô/\\Ô/g;
   $dirtyitem =~ s/Õ/\\Õ/g;
   $dirtyitem =~ s/"/\\"/g;
	return($dirtyitem);
}

##################################################
## START: TRANSLATE HOURS TOTAL TO DAYS AND HOURS
##################################################
sub hours2days {
my $hour_total = $_[0];
my $days = "0";

while ($hour_total > 7.999999999) {
	$hour_total = $hour_total - 8;
	$days++
}

my $x = new Number::Format;
	$hour_total = $x->round($hour_total, 2); 
#	$hour_total = $x->format_number($hour_total, 2, 2); DON'T NEED COMMA FORMATTING HERE, BUT LEAVING IN FOR REFERENCE


my $s = "";
   $s = "s" if ($days ne '1');
my $days_hours = "";
my $comma = "";
   $comma = "," if ($hour_total != 0);
   $days_hours .= "$days day$s$comma " if ($days != 0);
   $days_hours .= "$hour_total hr." if ($hour_total != 0);
   $days_hours = "<span style=\"color:#000099;\">$days_hours</span>" if ($hour_total != 0);
   $days_hours = "" if (($days == 0) && ($hour_total == 0));
	return($days_hours);
}
##################################################
## END: TRANSLATE HOURS TOTAL TO DAYS AND HOURS
##################################################


#####################################################################
## START: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
#####################################################################
sub get_staff_fullname {
	my $staff_userid = $_[0];
	my $prettyname = "";
	my $useridholder = "";
	my $usertimesheetnameholder = "";

	my $command = "select firstname, lastname, userid, email, timesheetname from staff_profiles where (userid like '$staff_userid')";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;

	my $name = "";
	#$error_message .=  "<P>COMMAND: $command";
	## GET THE RESULTS OF THE QUERY
	while (my @arr = $sth->fetchrow) {
    my ($firstname, $lastname, $userid, $email, $timesheetname) = @arr;
		$usertimesheetnameholder = $timesheetname;
		$usertimesheetnameholder = &cleanformysql ($usertimesheetnameholder);
			if ($usertimesheetnameholder eq '') {
				$usertimesheetnameholder = "Your \"user name from the accounting system\" is not on file in the Staff Profiles Database. You must contact OFTS to enter that data before you can view your leave report online.";
			}
		$prettyname = "$firstname $lastname";
		$useridholder = "$userid";
	}
return($prettyname, $usertimesheetnameholder, $useridholder);
}
#####################################################################
## END: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
#####################################################################



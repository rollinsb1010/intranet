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
my $detailmysqlyearmonth = "";

########################################
## START: READ VARIABLES PASSED BY USER
########################################
my $printfriendly = $query->param("printfriendly"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $logon_pass = $query->param("logon_pass");

my $uniqueid = $query->param("uniqueid");
my $location = $query->param("location");
   $location = "logon" if $location eq '';

my $showsession = $query->param("showsession");
my $showorgcode = $query->param("showorgcode");
my $showdetail = "yes";
   $showdetail = "no" if ($showorgcode =~ 'Subtotal');
my $detailmessage = "";
   $detailmessage = "<P>Note: No monthly detail is available for subtotal reports" if ($showorgcode =~ 'Subtotal');
   
my $showfundyear = $query->param("showfundyear");
my $sublocation = $query->param("sublocation");

my $orgcodesummary = $query->param("orgcodesummary");
my $orgcodesummarysplit = $query->param("orgcodesummarysplit");

my $budgetlastupdated = "";
my $dbname = $query->param("dbname");
   $dbname = "oftsbudgets" if $dbname eq '';
   
my $error_message = "";
my $show_month = $query->param("show_month");
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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("26"); # 26 is the PID for the "Budget Reports" page on the intranet

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";


## SHOW SLIM TEMPLATE IF A PRINT PAGE
if ( (($location eq 'budgetreport') && ($showorgcode ne '')) || ($printfriendly eq 'yes')) {
$htmlhead = "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<HTML>
<HEAD><TITLE>SEDL Staff Pages - Personnel: Financial Report for Brian Litke</TITLE>

<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">
<link href=\"/staff/includes/staff2006_tinymce.css\" rel=\"stylesheet\" type=\"text/css\">
</HEAD>
<BODY BGCOLOR=\"#FFFFFF\">
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

		## START: SUPER-USER LOGON
		if ($logon_pass eq 'backdoor') {
			$num_matches = 1;
		}

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
				$location = "budgetreport";

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
			## SET LOCATION
				$location = "budgetreport";

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
   my $commandinsert = "INSERT INTO staffpageusage VALUES ('$cookie_ss_staff_id', '$date_full_mysql', 'Your Budget Report')";
	my $dsn = "DBI:mysql:database=test;host=localhost";
	my $dbh = DBI->connect($dsn);
	my $sth = $dbh->prepare($commandinsert) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
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
<HEAD><TITLE>SEDL Staff Pages - Personnel: Your Financial Report</TITLE>
$htmlhead
<h1 style="margin-top:0px;">Financial Report</h1>
EOM

print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');

print<<EOM;      
<p>
This report allows <strong>budget authorities</strong> to check on the amount of money in 
specific SEDL budgets related to their project.
</p>
<p>
Please enter your SEDL user ID (ex: whoover) to view your financial report.
</p>
<FORM ACTION="/staff/personnel/budgets2.cgi" METHOD="POST">
<TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0>


  <TR><TD VALIGN="TOP" WIDTH="250"><strong>Your name</strong></TD>
      <TD WIDTH="420" VALIGN="TOP">
      <INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id">
      </TD></TR>
  <TR><TD VALIGN="TOP" WIDTH="150"><strong>Password</strong></TD>
      <TD WIDTH="420" VALIGN="TOP"><INPUT TYPE=PASSWORD NAME=logon_pass SIZE=8></TD></TR>
</TABLE>


  <DIV STYLE="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Show My Financial Report">
  </DIV>
  </FORM>
<p>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
$htmltail
EOM
} 
#SESSION ID: $cookie_ss_session_id<BR>
#STAFF ID: $cookie_ss_staff_id<BR>
#LOCATION: $location<BR>
#LOGON USER: $logon_user<BR>
#LOGON PASS: $logon_pass<BR>

#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################








#################################################################################
## START: PRINT THE USER'S BUDGET REPORT IF USER ID AND PASSWORD ARE VALID
#################################################################################
if ($location eq 'budgetreport') {

$sublocation = "orgcodemenu" if ($sublocation eq '');
my $usertimesheetnameholder = "";
my $prettyname = "";
my $useridholder = "";




#####################################################################
## START: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
#####################################################################
my $command = "select firstname, lastname, userid, timesheetname from staff_profiles where (userid like '$cookie_ss_staff_id') order by userid";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;
my $name = "";
#$error_message .=  "<P>COMMAND: $command";
## GET THE RESULTS OF THE QUERY
while (my @arr = $sth->fetchrow) {
    my ($firstname, $lastname, $userid, $usertimesheetname) = @arr;
	$usertimesheetnameholder = $usertimesheetname;
	$usertimesheetnameholder = &commoncode::cleanthisfordb ($usertimesheetnameholder);
	$prettyname = "$firstname $lastname";
	$useridholder = "$userid";
} # END DB QUERY LOOP
#####################################################################
## END: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
#####################################################################

###########################
## START: PRINT PAGE HEADER
###########################
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Staff Pages - Personnel: Financial Report for $prettyname</TITLE>

$htmlhead
$error_message
EOM

###########################
## END: PRINT PAGE HEADER
###########################



#####################################################################
## START: LOCATION = orgcodemenu
#####################################################################
# START: SUBLOCATION = ORGCODE MENU
if ($sublocation eq 'orgcodemenu') {
	my $showdepartment ="";
	my $showdepartmentid = "";

print<<EOM;
	<h1 style="margin-top:0px;">Financial Report Selection Menu for $prettyname (Click here to <A HREF="budgets2.cgi?location=logout">logout</A>)</h1>
<form ACTION="/staff/personnel/budgets2.cgi" METHOD="POST">
<div>
<p>
You are viewing Budget Reports for the month: 
</p>
<select name="show_month" id="show_month">
EOM

	my $command_count_months_available = "select periodenddate, COUNT(periodenddate) from $dbname group by periodenddate order by periodenddate DESC";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_count_months_available) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_months_available = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($lookup_periodenddate, $lookup_count_periodenddate) = @arr;
			$lookup_periodenddate = substr($lookup_periodenddate,0,7);
			$show_month = $lookup_periodenddate if ($show_month eq '');
			my $lookup_periodenddate_pretty = &date_mysqlmonth2standard($lookup_periodenddate);
			if (($lookup_periodenddate_pretty !~ '0000') && ($lookup_periodenddate_pretty =~ '20')) {
				print "<option value=\"$lookup_periodenddate\"";
				print " SELECTED" if ($lookup_periodenddate eq $show_month);
				print ">$lookup_periodenddate_pretty</option>";
			}
		} # END DB QUERY LOOP
print<<EOM;
	</select>
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="budgetreport">
  <INPUT TYPE="HIDDEN" NAME="sublocation" VALUE="orgcodemenu">
  <INPUT TYPE="SUBMIT" VALUE="Change Month">
	</div>
  </form>
  </p>
EOM
	print "<p class=\"info\">QUERY TO LIST MONTHS AVAILABLE:<br>$command_count_months_available<br><br># Months on file in the $dbname Database: $num_matches</p>" if ($debug == 1); # FOR DEBUG ONLY

print<<EOM;
	Contact Brian Litke <A HREF="mailto:webmaster\@sedl.org">webmaster\@sedl.org</A> 
	for assistance if a list of budget codes does not display below.
<P>
	<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0">
	<TR style="background-color:#ebebeb;"><TD>Fund<br>year</TD>
		<TD>Description</TD>
		<TD>Org Code</TD>
		<TD>Year(s) in Reports</TD>
		<TD>Code Description</TD></TR>
EOM

	my $lastorgcode = "";
	my $lastfundyear = "";
	my $lastfundyeardesc = "";

	my $command_show_budgetlist = "select fundyear, fundyeardesc, orgcode, orgdesc
					from $dbname where periodenddate LIKE '$show_month%'";
#	my $command_show_budgetlist = "select * from $dbname where periodenddate LIKE '$show_month%'";
	   $command_show_budgetlist .= " AND fundyear NOT LIKE '0040'";
	   $command_show_budgetlist .= " AND fundyear NOT LIKE '0060'";
	   $command_show_budgetlist .= " AND fundyear NOT LIKE '0220'";
	   $command_show_budgetlist .= " AND fundyear NOT LIKE '0270'";
#	   $command_show_budgetlist .= " AND fundyear NOT LIKE '0291'";
	   $command_show_budgetlist .= " AND fundyear NOT LIKE '0090'";
	   $command_show_budgetlist .= " AND fundyear NOT LIKE '0089'";
	   $command_show_budgetlist .= " AND fundyear NOT LIKE '0174'";
	   
	   $command_show_budgetlist .= " order by fundyear, orgcode";
	print "<p class=\"info\">QUERY TO DISPLAY BUDGET LIST:<br>$command_show_budgetlist<br><br># Records in the $dbname Database: $num_matches</p>" if ($debug == 1); # FOR DEBUG ONLY
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_show_budgetlist) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#print "<p>MATCHES: $num_matches</p>";
		while (my @arr = $sth->fetchrow) {
			my ($fundyear, $fundyeardesc, $orgcode, $orgdesc) = @arr;
#			my ($fundyear, $fundyeardesc, $orgcode, $orgdesc, $objclasscode, $objclasscodedesc, $objclasscodebrief, $budget, $priorexpend, $currentexpend, $totalexpend, $budgetremaining, $encumberances, $budgetremainingnoencumberances, $periodenddate) = @arr;
#			print "<tr><td colspan=5>$fundyear, $fundyeardesc, $orgcode, $orgdesc, $objclasscode, $objclasscodedesc, $objclasscodebrief, $budget, $priorexpend, $currentexpend, $totalexpend, $budgetremaining, $encumberances, $budgetremainingnoencumberances, $periodenddate</td></tr>";

			## CHECK IF USER SHOULD SEE THIS ORGCODE
			my $userviewable = "no";
			   $userviewable = &checkuserviewable ($cookie_ss_staff_id, $orgcode, $fundyear);


			## START: PRINT REPORT WITH LINK TO DETAIL
			if ((($orgcode ne $lastorgcode) || ($fundyear ne $lastfundyear)) && ($orgcode ne '') && ($userviewable eq 'yes')) { # START: PRINT SUMMARY LINK
				my $orgcodelink = $orgcode;
				   $orgcodelink =~ s/ /\+/g;
				my $orgcodelabel = $orgcode;
				   $orgcodelabel =~ s/zS/S/g;
				$orgdesc = "\&nbsp\;" if ($orgcode =~ 'z');
				my $color = "";
				   $color = "BGCOLOR=\"#E0CF9F\"" if ($orgcode =~ 'z');
				print "<TR $color><TD class=small>$fundyear</TD><TD class=small>$fundyeardesc</TD><TD class=small>$orgcodelabel</TD><TD class=\"small\" nowrap>";

				## PRINT "CURRENT SEDL FY", BUT NOT FOR THESE CODES
				if (($fundyear ne '0122')
					&& ($fundyear ne '0142')
					&& ($fundyear ne '0175')
					&& ($fundyear ne '0176')
					&& ($fundyear ne '0177')
					&& ($fundyear ne '0178')
					&& ($fundyear ne '0185')
					&& ($fundyear ne '0203')
					&& ($fundyear ne '0215')
					&& ($fundyear ne '0245')
					&& ($fundyear ne '0256')
					&& ($fundyear ne '0266')
					&& ($fundyear ne '0305')
					&& ($fundyear ne '0315')
					&& ($fundyear ne '0325')
					&& ($fundyear ne '0335')
					&& ($fundyear ne '0352')
					&& ($fundyear ne '0355')
					&& ($fundyear ne '0363')
					&& ($fundyear ne '0376')
					&& ($fundyear ne '0386')
					&& ($fundyear ne '0406')
					&& ($fundyear ne '0416')
					&& ($fundyear ne '0417')
					&& ($fundyear ne '0426')
					&& ($fundyear ne '0436')
					&& ($fundyear ne '0446')
					&& ($fundyear ne '0457')
					&& ($fundyear ne '0466')
					&& ($fundyear ne '0467')
					&& ($fundyear ne '0477')
					&& ($fundyear ne '0497')
					&& ($fundyear ne '0518')
					&& ($fundyear ne '0528')
					&& ($fundyear ne '0538')
					&& ($fundyear ne '0548')
					&& ($fundyear ne '0558')
					&& ($fundyear ne '0568')
					&& ($fundyear ne '0578')
					&& ($fundyear ne '0589')
					&& ($fundyear ne '0599')
					&& ($fundyear ne '0669')
					&& ($fundyear ne '0690')
					&& ($fundyear ne '0700')
					&& ($fundyear ne '0710')
					&& ($fundyear ne '0740')
					&& ($fundyear ne '0760')
					&& ($fundyear ne '0770')
					&& ($fundyear ne '0821')
					&& ($fundyear ne '0841')
					&& ($fundyear ne '0851')
					&& ($fundyear ne '0888')
					&& ($fundyear ne '0291')
					&& ($fundyear ne '0296')
					&& ($fundyear ne '0298')
					&& ($fundyear ne '0346')
					&& ($fundyear ne '0869')
					&& ($fundyear ne '0878')
					&& ($fundyear ne '0974')
					&& ($fundyear ne '0897')
					) {
print<<EOM;
		- <A HREF="budgets2.cgi?dbname=oftsbudgetsfy&amp;showorgcode=$orgcodelink&amp;showfundyear=$fundyear&amp;sublocation=orgcodedetail&amp;location=budgetreport&amp;show_month=$show_month&amp;s=$cookie_ss_session_id">current SEDL FY</A><BR>
EOM
				}

				## PRINT "CUMULATIVE" BUT NOT FOR THESE CODES
				if (($fundyear ne '0100') 
					&& ($fundyear ne '0190')
					&& ($fundyear ne '0971') 
					&& ($fundyear ne '0972')
					) {
print<<EOM;
		- <A HREF="budgets2.cgi?dbname=oftsbudgets&amp;showorgcode=$orgcodelink&amp;showfundyear=$fundyear&amp;sublocation=orgcodedetail&amp;location=budgetreport&amp;show_month=$show_month&amp;s=$cookie_ss_session_id">cumulative</A>
EOM
				}
print<<EOM;
		</TD>
		<TD class="small">$orgdesc</TD></TR>
EOM
			} # END PRINT SUMMARY LINK
			## END: PRINT REPORT WITH LINK TO DETAIL

			## END: CHECK IF ORGCODE SUBTOTAL IS NEEDED

			$lastorgcode = $orgcode;
			$lastfundyear = $fundyear;
			$lastfundyeardesc = $fundyeardesc;

		} # END DB QUERY LOOP
	print "</TABLE>";
} # END SUBLOCATION = ORGCODE MENU
#####################################################################
## END: LOCATION = orgcodemenu
#####################################################################


#####################################################################
# START: SUBLOCATION = ORGCODE DETAIL
#####################################################################
if ($sublocation eq 'orgcodedetail') {


	######################################################
	# START: PRINT PAGE HEADER
	######################################################
print<<EOM;
<h1 style="margin-top:0px;"><A HREF="http://www.sedl.org/staff/personnel/budgets2.cgi?location=budgetreport&amp;s=$cookie_ss_session_id">Budget Report</A> for $prettyname</h1>
<P>
You are logged in as $prettyname. Click here to <A HREF="budgets2.cgi?location=logout">logout</A>.

<form ACTION="/staff/personnel/budgets2.cgi" METHOD="POST">
<div>
<p>
You are viewing Budget Reports for the month: 
</p>
<select name="show_month" id="show_month">
EOM

	my $command_count_months_available = "select periodenddate, COUNT(periodenddate) from $dbname group by periodenddate order by periodenddate DESC";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_count_months_available) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_months_available = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($lookup_periodenddate, $lookup_count_periodenddate) = @arr;
			$lookup_periodenddate = substr($lookup_periodenddate,0,7);
			$show_month = $lookup_periodenddate if ($show_month eq '');
			my $lookup_periodenddate_pretty = &date_mysqlmonth2standard($lookup_periodenddate);
			if (($lookup_periodenddate_pretty !~ '0000') && ($lookup_periodenddate_pretty =~ '20')) {
				print "<option value=\"$lookup_periodenddate\"";
				print " SELECTED" if ($lookup_periodenddate eq $show_month);
				print ">$lookup_periodenddate_pretty</option>";
			}
		} # END DB QUERY LOOP
print<<EOM;
	</select>
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="budgetreport">
  <INPUT TYPE="HIDDEN" NAME="s" VALUE="$session_id">
  <INPUT TYPE="HIDDEN" NAME="sublocation" VALUE="orgcodedetail">

  <INPUT TYPE="HIDDEN" NAME="showfundyear" VALUE="$showfundyear">
  <INPUT TYPE="HIDDEN" NAME="showorgcode" VALUE="$showorgcode">
  <INPUT TYPE="HIDDEN" NAME="dbname" VALUE="$dbname">

  <INPUT TYPE="SUBMIT" VALUE="Change Month">
	</div>
  </form>

EOM
if (($prettyname =~ 'Arnold') || ($prettyname =~ 'Brian')) {
	if ($debug != 1) {
print<<EOM;
(Show debug comments?: <a href="budgets2.cgi?dbname=$dbname&amp;showorgcode=$showorgcode&amp;showfundyear=$showfundyear&amp;sublocation=$sublocation&amp;location=$location&amp;show_month=$show_month&amp;s=$session_id&amp;debug=1">turn on debug statements</a>)
EOM
	} else {
print<<EOM;
(Show debug comments?: <a href="budgets2.cgi?dbname=$dbname&amp;showorgcode=$showorgcode&amp;showfundyear=$showfundyear&amp;sublocation=$sublocation&amp;location=$location&amp;show_month=$show_month&amp;s=$session_id&amp;debug=0">turn off debug statements</a>)
EOM
	}
}
	######################################################
	# END: PRINT PAGE HEADER
	######################################################
	
	## START: SET DATE FOR THE BUDGET REPORT
	$detailmysqlyearmonth = $show_month;
	my $detailprettydate = &prettydate ($show_month);
	## END: SET DATE FOR THE BUDGET REPORT

	## START: DECLARE VARIABLES USED IN THE DISPLAY - WILL BE LOOKED UP IN THE DATABASE
	my $showdepartment ="";
	my $showdepartmentid = "";
	## END: DECLARE VARIABLES USED IN THE DISPLAY - WILL BE LOOKED UP IN THE DATABASE

	# START: DECLARE VARIABLES FOR SUBTOTAL
	my $subtotalbudget = "0";
	my $subtotalpriorexpend = "0";
	my $subtotalcurrentexpend = "0";
	my $subtotaltotalexpend = "0";
	my $subtotalbudgetremaining = "0";
	my $subtotalencumberances = "0";
	my $subtotalbudgetremainingnoencumberances = "0";
	my $subtotaltotalmonthforojbcode = "0";

	my $grandtotalbudget = "0";
	my $grandtotalpriorexpend = "0";
	my $grandtotalcurrentexpend = "0";
	my $grandtotaltotalexpend = "0";
	my $grandtotalbudgetremaining = "0";
	my $grandtotalencumberances = "0";
	my $grandtotalbudgetremainingnoencumberances = "0";
	my $grandtotaltotalmonthforojbcode = "0";
	# END: DECLARE VARIABLES FOR SUBTOTAL

	# START: SET COMMAND FOR DATABASE QUERY
	my $command = "select * from $dbname 
					where fundyear like '$showfundyear' 
					AND orgcode like '$showorgcode' 
					AND periodenddate LIKE '$show_month%'
					order by objclasscode, orgcode, fundyear";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	print "<p class=\"info\">DATABASE QUERY TO GET RECORDS RELATED TO THE REQUESTED BUDGET CODE: $command<br><em>Total Number of Records in the Database: $num_matches</em></p>" if ($debug == 1); # FOR DEBUG ONLY
	my $count = "1";
	# END: SET COMMAND FOR DATABASE QUERY

	# START: GET RECORDS FROM DATABASE QUERY
		while (my @arr = $sth->fetchrow) {
			my ($fundyear, $fundyeardesc, $orgcode, $orgdesc, $objclasscode, $objclasscodedesc, $objclasscodebrief, $budget, $priorexpend, $currentexpend, $totalexpend, $budgetremaining, $encumberances, $budgetremainingnoencumberances, $periodenddate, $fundyear_startdate, $fundyear_enddate, $orgcode_startdate, $orgcode_enddate) = @arr;

			my $lastmonthdate = &subtractmonth ($periodenddate);
			my $periodenddatenoday = &removeday ($periodenddate);
			my $periodenddate = &prettydate ($periodenddate);
			#my $thismonthnoday = &addmonth ($periodenddatenoday);

			################################################################
			## START: GRAB THE CURRENT MONTH TOTAL FOR THIS OBJECTCLASSCODE
			################################################################
			my $findobjclasscode = $objclasscode;
			chop $findobjclasscode;
			my $totalmonthforojbcode = "0";

			my $command = "select amount from oftsbudgetsckjvpr 
							where gl = '91' AND objcode like '$findobjclasscode%' AND orgcode like '$showorgcode' and fundyear like '$showfundyear' and date like '$detailmysqlyearmonth%' 
							order by objcode";

				if (($findobjclasscode eq '01') || ($findobjclasscode eq '02')) {
					$command = "select amount from oftsbudgetsckjvpr where gl like '91' AND ((objcode like '01%') OR (objcode like '02%')) AND orgcode like '$showorgcode' and fundyear like '$showfundyear' and date like '$detailmysqlyearmonth%' order by objcode";
				}

				if (($findobjclasscode eq '12') || ($findobjclasscode eq '13') || ($findobjclasscode eq '14') || ($findobjclasscode eq '15') || ($findobjclasscode eq '16') || ($findobjclasscode eq '17') || ($findobjclasscode eq '18')) {
					$command = "select amount from oftsbudgetsckjvpr where gl like '91' AND ((objcode like '11%') OR (objcode like '12%') OR (objcode like '13%') OR (objcode like '14%') OR (objcode like '15%') OR (objcode like '16%') OR (objcode like '17%') OR (objcode like '18%')) AND orgcode like '$showorgcode' and fundyear like '$showfundyear' and date like '$detailmysqlyearmonth%' order by objcode";
				}

				if ($findobjclasscode eq '99') {
					$command = "select amount from oftsbudgetsckjvpr where gl like '91' AND (objcode like '19%') AND orgcode like '$showorgcode' and fundyear like '$showfundyear' and date like '$detailmysqlyearmonth%' order by objcode";
				}

			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
			print "<p class=\"info\">DATABASE QUERY TO GET SUBTOTAL FOR OBJCODE ($findobjclasscode): $command<br><em>Total matches for OBJ CODE $findobjclasscode: $num_matches</em></p>" if ($debug == 1);
				while (my @arr = $sth->fetchrow) {
					my ($amount) = @arr;
					$totalmonthforojbcode = $totalmonthforojbcode + $amount;
				}
			################################################################
			## END: GRAB THE CURRENT MONTH TOTAL FOR THIS OBJECTCLASSCODE
			################################################################

				#################################################
				## START: PRINT REPORT HEADER, ONLY ONCE
				#################################################
				if ($count eq '1') {
					$orgcode =~ s/zS/S/g;
					$orgdesc =~ s/zS/S/g;

					my $howmanyyears = "<em><FONT COLOR=\"#A73329\">(current SEDL FY report)</FONT></em>";
					   $howmanyyears = "<em><FONT COLOR=\"#A73329\">(cumulative report)</FONT></em>" if ($dbname eq 'oftsbudgets');

						$orgcode_startdate = &commoncode::date2standard ($orgcode_startdate);
						$orgcode_enddate = &commoncode::date2standard ($orgcode_enddate);

						$fundyear_startdate = &commoncode::date2standard ($fundyear_startdate);
						$fundyear_enddate = &commoncode::date2standard ($fundyear_enddate);

print<<EOM;
<H2>$fundyeardesc ($fundyear) <SPAN class=normal>($fundyear_startdate - $fundyear_enddate)</SPAN>
<BR>
$orgcode 
EOM
						if (($orgcode_startdate ne '00/00/0000') && ($orgcode_startdate ne '')) {
							print "<SPAN class=normal>($orgcode_startdate - $orgcode_enddate)</SPAN>";
						}
print<<EOM;
: $orgdesc $howmanyyears</H2>
$detailmessage
<p>
</p>
	<TABLE BORDER=1 CELLPADDING=3 CELLSPACING=0>
	<TR><TD VALIGN=TOP>Description</TD>
EOM
#print "		<TD>Obj<BR>Class<BR>Code<BR>Brief</TD>";
print<<EOM;
		<TD VALIGN="TOP">Budget<BR>$periodenddate</TD>
		<TD VALIGN="TOP">Prior<BR>Expenditures<BR>Through<BR>$lastmonthdate</TD>
		<TD VALIGN="TOP">Monthly<BR>Expenditures<BR>for $periodenddatenoday</TD>
EOM
print "<TD VALIGN=\"TOP\">Current Monthly<BR>Expenditures<BR>Through<BR>$detailprettydate</TD>" if ($showdetail eq 'yes');
print<<EOM;
		<TD VALIGN="TOP">Total<BR>Expenditures<BR>$periodenddate</TD>
		<TD VALIGN="TOP">Budget<BR>Remaining<br>Before<br>Encumbrances<BR>$periodenddate</TD>
		<TD VALIGN="TOP">Encumbrances<BR>$periodenddate</TD>
		<TD VALIGN="TOP">Budget<BR>Remaining<BR>After<BR>Encumbrances<BR>$periodenddate</TD></TR>
EOM
						$count++;
					} # END IF COUNT = 1
				#################################################
				## END: PRINT REPORT HEADER, ONLY ONCE
				#################################################

				#################################################
				## START: PRINT SUBTOTAL ROW
				#################################################
				if ($objclasscode eq '999') {
					my $subtotalbudgetccc = &format_number($subtotalbudget, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
					my $subtotalpriorexpendccc = "$subtotalpriorexpend";
					   $subtotalpriorexpendccc = &format_number($subtotalpriorexpendccc, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)

					my $subtotalcurrentexpendccc = "$subtotalcurrentexpend";
					   $subtotalcurrentexpendccc = &format_number($subtotalcurrentexpendccc, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)

					my $subtotaltotalexpendccc = &format_number($subtotaltotalexpend, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
					my $subtotalbudgetremainingccc = &format_number($subtotalbudgetremaining, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
					my $subtotalencumberancesccc = &format_number($subtotalencumberances, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
					my $subtotalbudgetremainingnoencumberancesccc = &format_number($subtotalbudgetremainingnoencumberances, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
					my $subtotaltotalmonthforojbcodeccc = &format_number($subtotaltotalmonthforojbcode, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)

					$grandtotalbudget = $subtotalbudget + $budget;
					$grandtotalpriorexpend = $subtotalpriorexpend + $priorexpend;
					$grandtotalcurrentexpend = $subtotalcurrentexpend + $currentexpend;
					$grandtotaltotalexpend = $subtotaltotalexpend + $totalexpend;
					$grandtotalbudgetremaining = $subtotalbudgetremaining + $budgetremaining;
					$grandtotalencumberances = $subtotalencumberances + $encumberances;
					$grandtotalbudgetremainingnoencumberances = $subtotalbudgetremainingnoencumberances + $budgetremainingnoencumberances;
					$grandtotaltotalmonthforojbcode = $subtotaltotalmonthforojbcode + $totalmonthforojbcode;

print<<EOM;
	<TR><TD><strong>Sub Total</strong></TD>
EOM
#print "		<TD>$objclasscodebrief</TD>";
print<<EOM;
		<TD ALIGN="RIGHT"><strong>$subtotalbudgetccc</strong></TD>
		<TD ALIGN="RIGHT"><strong>$subtotalpriorexpendccc</strong></TD>
		<TD ALIGN="RIGHT"><strong>$subtotalcurrentexpendccc</strong></TD>
EOM
print "<TD ALIGN=\"RIGHT\"><strong><FONT COLOR=RED>$subtotaltotalmonthforojbcodeccc</FONT></strong></TD>" if ($showdetail eq 'yes');
print<<EOM;
		<TD ALIGN="RIGHT"><strong>$subtotaltotalexpendccc</strong></TD>
		<TD ALIGN="RIGHT"><strong>$subtotalbudgetremainingccc</strong></TD>
		<TD ALIGN="RIGHT"><strong>$subtotalencumberancesccc</strong></TD>
		<TD ALIGN="RIGHT"><strong>$subtotalbudgetremainingnoencumberancesccc</strong></TD></TR>
EOM
				} # END IF objclasscode = '999'
				#################################################
				## END: PRINT SUBTOTAL ROW
				#################################################


			#######################################################
			## START: INCREMENT SUBTOTAL WITH CURRENT ROW OF DATA
			#######################################################
			$subtotalbudget = $budget + $subtotalbudget;
			$subtotalpriorexpend = $priorexpend + $subtotalpriorexpend;
			$subtotalcurrentexpend = $currentexpend + $subtotalcurrentexpend;
			$subtotaltotalexpend = $totalexpend + $subtotaltotalexpend;
			$subtotalbudgetremaining = $budgetremaining + $subtotalbudgetremaining;
			$subtotalencumberances = $encumberances + $subtotalencumberances;
			$subtotalbudgetremainingnoencumberances = $budgetremainingnoencumberances + $subtotalbudgetremainingnoencumberances;
			$subtotaltotalmonthforojbcode = $subtotaltotalmonthforojbcode + $totalmonthforojbcode;
			#######################################################
			## END: INCREMENT SUBTOTAL WITH CURRENT ROW OF DATA
			#######################################################


			###################################################
			## START: SET VARIABLES TO HOLD NUMBER WITH COMMAS
			###################################################
			my $budgetccc = &format_number($budget, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
			my $priorexpendccc = &format_number($priorexpend, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
			my $currentexpendccc = &format_number($currentexpend, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
			my $totalexpendccc = &format_number($totalexpend, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
			my $budgetremainingccc = &format_number($budgetremaining, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
			my $encumberancesccc = &format_number($encumberances, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
			my $budgetremainingnoencumberancesccc = &format_number($budgetremainingnoencumberances, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
			my $totalmonthforojbcodeccc = &format_number($totalmonthforojbcode, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)

				$objclasscodedesc =~ s/\// \/ /gi;
			###################################################
			## END: SET VARIABLES TO HOLD NUMBER WITH COMMAS
			###################################################

			###################################################
			## START: PRINT OJBCLASS ROW OF DATA
			###################################################
			print "<TR><TD>$objclasscodedesc <DIV CLASS=small>($objclasscode)</DIV></TD>";

			#print "		<TD>$objclasscodebrief</TD>";
			print "<TD ALIGN=\"RIGHT\">$budgetccc</TD><TD ALIGN=RIGHT>$priorexpendccc</TD><TD ALIGN=RIGHT>$currentexpendccc</TD>";
			print "<TD ALIGN=\"RIGHT\"><strong><FONT COLOR=RED>$totalmonthforojbcodeccc</FONT></strong></TD>" if ($showdetail eq 'yes');
print<<EOM;
		<TD ALIGN="RIGHT">$totalexpendccc</TD>
		<TD ALIGN="RIGHT">$budgetremainingccc</TD>
		<TD ALIGN="RIGHT">$encumberancesccc</TD>
		<TD ALIGN="RIGHT">$budgetremainingnoencumberancesccc</TD></TR>
EOM
			###################################################
			## END: PRINT OJBCLASS ROW OF DATA
			###################################################
		} # END DB QUERY LOOP
	# END: GET RECORDS FROM DATABASE QUERY

	my $grandtotalbudgetccc = $grandtotalbudget;
	#   $grandtotalbudgetccc = "0.00" if $grandtotalbudgetccc eq "0";
	my $grandtotalbudgetccc = &format_number($grandtotalbudgetccc, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)

	my $grandtotalpriorexpendccc = &format_number($grandtotalpriorexpend, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)

	my $grandtotalcurrentexpendccc = $grandtotalcurrentexpend;
	my $grandtotalcurrentexpendccc = &format_number($grandtotalcurrentexpend, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)

	my $grandtotaltotalexpendccc = &format_number($grandtotaltotalexpend, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
	my $grandtotalbudgetremainingccc = &format_number($grandtotalbudgetremaining, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
	my $grandtotalencumberancesccc = &format_number($grandtotalencumberances, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
	my $grandtotalbudgetremainingnoencumberancesccc = &format_number($grandtotalbudgetremainingnoencumberances, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
	my $grandtotaltotalmonthforojbcodeccc = &format_number($grandtotaltotalmonthforojbcode, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)

print<<EOM;
	<TR><TD><strong>Total Costs</strong></TD>
EOM
#print "		<TD>$objclasscodebrief</TD>";
print<<EOM;
		<TD ALIGN="RIGHT"><strong>$grandtotalbudgetccc</strong></TD>
		<TD ALIGN="RIGHT"><strong>$grandtotalpriorexpendccc</strong></TD>
		<TD ALIGN="RIGHT"><strong>$grandtotalcurrentexpendccc</strong></TD>
EOM
print "<TD ALIGN=\"RIGHT\"><strong><FONT COLOR=RED>$grandtotaltotalmonthforojbcodeccc</FONT></strong></TD>" if ($showdetail eq 'yes');
print<<EOM;
		<TD ALIGN="RIGHT"><strong>$grandtotaltotalexpendccc</strong></TD>
		<TD ALIGN="RIGHT"><strong>$grandtotalbudgetremainingccc</strong></TD>
		<TD ALIGN="RIGHT"><strong>$grandtotalencumberancesccc</strong></TD>
		<TD ALIGN="RIGHT"><strong>$grandtotalbudgetremainingnoencumberancesccc</strong></TD></TR>
</TABLE>
<p>
	Contact Brian Litke <A HREF="mailto:webmaster\@sedl.org">webmaster\@sedl.org</A> or ext. 6529
	for assistance if your budget report does not display.
</p>
EOM

} # END SUBLOCATION = ORGCODE DETAIL
#####################################################################
## END: SEND DATABASE QUERY AND LOOP THROUGH ALL RECORDS
#####################################################################


####################################
## START: PRINT MONTHLY DATA DETAIL
####################################

if ($showorgcode ne '') {
	my $command = "select * from oftsbudgetsckjvpr where orgcode like '$showorgcode' and fundyear like '$showfundyear' and date like '$detailmysqlyearmonth%' order by gl, objcode, date";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	#$debug = 1;
	print "<p class=\"info\">DATABASE QUERY TO GET ALL THE DETAIL LINES FOR THIS BUDGET CODE THIS MONTH: $command<br><br><em>Total Number of Records for ($showorgcode / $showfundyear) for the month $detailmysqlyearmonth: $num_matches</p>" if ($debug == 1);
		if ($num_matches == 0) {
			print "<p class=\"info\">$command</p>" if ($debug == 1); # FOR DEBUG ONLY
		}
print<<EOM;
<H2>Current Month's Details ($num_matches items)</H2>
<TABLE BORDER=1 CELLPADDING=0 CELLSPACING=0>
<TR><TD>Date</TD>
	<TD>Desc</TD>
	<TD>SB code</TD>
	<TD>G<BR>e<BR>n<BR>d<BR>e<BR>r</TD>
	<TD>E<BR>t<BR>h<BR>n<BR>i<BR>c<BR>.</TD>
	<TD>O<BR>t<BR>h<BR></TD>
	<TD>H<BR>u<BR>b</TD>
	<TD>Item Name</TD>
	<TD>Ref No</TD>
	<TD>Inv No</TD>
	<TD>Qty</TD>
	<TD>QC</TD>
	<TD>Unit Cost</TD>
	<TD>From File</TD>
	<TD>Amount</TD>
	<TD>Obj code</TD>

	<TD>trxref</TD>
	<TD>GL</TD>
	</TR>
EOM

	my $gltracker = "";
	my $objcode_previous = "";
	my $this_subtotal = "0";
	my $this_summary_code = "";

		while (my @arr = $sth->fetchrow) {
			my ($date, $trxref, $gl, $fundyear, $orgcode, $objcode, $sbcode, $gender, $enthnicity, $othereeoc, $hubzone, $invno, $descrip, $address1, $address2, $city, $state, $zip, $socialnum, $itemname, $percenthrs, $quantity, $quancode, $unitcost, $amount, $refno, $camefromfile) = @arr;

			my $amountccc = &format_number($amount, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)


			my $comma = "";
			   $comma = "," if (($city ne '') && ($state ne ''));
	
			my $color = "";
			$color = "BGCOLOR=\"E3D7BF\"" if ($gl eq '98');
	
			my $prettydate = &prettydate ($date);
			   $itemname =~ s/\"//g;

			$percenthrs = "" if ($percenthrs eq '0.000');

				if ( (substr($objcode,0,2) ne substr($objcode_previous,0,2) ) && ($objcode_previous ne '')){
					if (substr($objcode,0,2) eq '02') {
						# DO NOTHING
					} elsif ( (substr($objcode,0,2) gt '11') && (substr($objcode,0,2) lt '19') ) {
						# DO NOTHING
					} else {
						# PRINT SUBTOTAL
						$this_summary_code = substr($objcode_previous, 0, 2);
						&print_subtotal($this_subtotal, $this_summary_code);
						$this_subtotal = "0";
					} # END IF/ELSE
				} # END IF
			$unitcost = &format_number($unitcost, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)

print<<EOM;
<TR $color><TD class="small">$prettydate</TD>
	<TD class="small">$descrip</TD>
	<TD class="small">$sbcode</TD>
	<TD class="small">$gender</TD>
	<TD class="small">$enthnicity</TD>
	<TD class="small">$othereeoc</TD>
	<TD class="small">$hubzone</TD>
	<TD class="small">$itemname</TD>
	<TD class="small">$refno</TD>
	<TD class="small">$invno</TD>
	<TD class="small">$quantity</TD>
	<TD class="small">$quancode</TD>
	<TD class="small" align=right>\$$unitcost</TD>
	<TD class="small" ALIGN="CENTER">$camefromfile</TD>
	<TD class="small" ALIGN="RIGHT">\$$amountccc</TD>
	<TD class="small" ALIGN="CENTER">$objcode</TD>

	<TD class="small" ALIGN="CENTER">$trxref</TD>
	<TD class="small" ALIGN="CENTER">$gl</TD>
	</TR>
EOM
			$this_subtotal = $this_subtotal + $amount;
			$objcode_previous = $objcode;
		}
		# PRINT FINAL SUBTOTAL LINE
		$this_summary_code = substr($objcode_previous, 0, 2);
		&print_subtotal($this_subtotal, $this_summary_code);
		print "</TABLE>";
} # END IF ORGCODE NOT BLANK
####################################
## END: PRINT MONTHLY DATA DETAIL
####################################



###########################
## START: PRINT PAGE FOOTER
###########################
#SESSION ID: $cookie_ss_session_id<BR>
#SESSION ID: $cookie_ss_staff_id

print <<EOM;
$htmltail
EOM
###########################
## END: PRINT PAGE FOOTER
###########################


}
#################################################################################
## END: PRINT THE USER'S BUDGET REPORT IF USER ID IS OK
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
# 		($cookies{$key}, $xxdate, $xxpath, $xxdomain) = split(/\,/,$cookies{$key});
	}
	return(%cookies);
}


## SAMPLE SETCOOKIE CALLS:
# setCookie ("user", "dbewley", $expdate, $path, $thedomain);
# my(%cookies) = getCookies();



##############################
##  START: SUBTRACT MONTH FROM MYSQLDATE
##############################
sub subtractmonth {
	my $holdme = $_[0];

	my ($holdyear, $holdmonth, $holdday) = split(/\-/,$holdme);
	my $previousyear = $holdyear - 1;

	my $newdate = "";

	if ($holdmonth ne '01'){
		$newdate = "01\-31\-$holdyear" if ($holdmonth eq '02'); 
		$newdate = "02\-28\-$holdyear" if ($holdmonth eq '03'); 
		$newdate = "03\-31\-$holdyear" if ($holdmonth eq '04'); 
		$newdate = "04\-30\-$holdyear" if ($holdmonth eq '05'); 
		$newdate = "05\-31\-$holdyear" if ($holdmonth eq '06'); 
		$newdate = "06\-30\-$holdyear" if ($holdmonth eq '07'); 
		$newdate = "07\-31\-$holdyear" if ($holdmonth eq '08'); 
		$newdate = "08\-31\-$holdyear" if ($holdmonth eq '09'); 
		$newdate = "09\-30\-$holdyear" if ($holdmonth eq '10'); 
		$newdate = "10\-31\-$holdyear" if ($holdmonth eq '11'); 
		$newdate = "11\-30\-$holdyear" if ($holdmonth eq '12'); 
	} else {
		$newdate = "12\-31\-$previousyear"
	}

	$newdate = $newdate;
} 


##############################
##  START: REMOVE DAY FROM MYSQLDATE TO PRETTY DATE
##############################
sub removeday {
	my $holdme = $_[0];

	my ($holdyear, $holdmonth, $holdday) = split(/\-/,$holdme);
	my $newdate = "$holdmonth\-$holdyear";

	$newdate = $newdate;
}

##############################
##  START: REMOVE DAY FROM MYSQLDATE TO PRETTY DATE
##############################
sub removedaymysql {
	my $holdme = $_[0];

	my ($holdyear, $holdmonth, $holdday) = split(/\-/,$holdme);
	my $newdate = "$holdyear\-$holdmonth";

	$newdate = $newdate;
}


##############################
## START: ALL MONTH TO DATE WITH NO DAY
##############################
sub addmonth {
	my $holdme = $_[0];

	my ($holdmonth, $holdyear) = split(/\-/,$holdme);
	my $newyear = $holdyear;
	my $newmonth = "";
	$newmonth = "02" if $holdmonth eq '01';
	$newmonth = "03" if $holdmonth eq '02';
	$newmonth = "04" if $holdmonth eq '03';
	$newmonth = "05" if $holdmonth eq '04';
	$newmonth = "06" if $holdmonth eq '05';
	$newmonth = "07" if $holdmonth eq '06';
	$newmonth = "08" if $holdmonth eq '07';
	$newmonth = "09" if $holdmonth eq '08';
	$newmonth = "10" if $holdmonth eq '09';
	$newmonth = "11" if $holdmonth eq '10';
	$newmonth = "12" if $holdmonth eq '11';
	$newmonth = "01" if $holdmonth eq '12';
	$newyear = "2001" if (($holdyear eq '2000') && ($holdmonth eq '12'));
	$newyear = "2002" if (($holdyear eq '2001') && ($holdmonth eq '12'));
	$newyear = "2003" if (($holdyear eq '2002') && ($holdmonth eq '12'));
	$newyear = "2004" if (($holdyear eq '2003') && ($holdmonth eq '12'));
	$newyear = "2005" if (($holdyear eq '2004') && ($holdmonth eq '12'));
	$newyear = "2006" if (($holdyear eq '2005') && ($holdmonth eq '12'));
	$newyear = "2007" if (($holdyear eq '2006') && ($holdmonth eq '12'));
	$newyear = "2008" if (($holdyear eq '2007') && ($holdmonth eq '12'));
	$newyear = "2009" if (($holdyear eq '2008') && ($holdmonth eq '12'));
	$newyear = "2010" if (($holdyear eq '2009') && ($holdmonth eq '12'));
	$newyear = "2011" if (($holdyear eq '2010') && ($holdmonth eq '12'));
	$newyear = "2012" if (($holdyear eq '2011') && ($holdmonth eq '12'));
	$newyear = "2013" if (($holdyear eq '2012') && ($holdmonth eq '12'));
	$newyear = "2014" if (($holdyear eq '2013') && ($holdmonth eq '12'));
	$newyear = "2015" if (($holdyear eq '2014') && ($holdmonth eq '12'));
	my $newdate = "$newmonth\-$newyear";

	return($newdate);
} # end sub addmonth
##############################
## END: ALL MONTH TO DATE WITH NO DAY
##############################


###############################################
##  START: REORDER MYSQL DATE IN PRETTY FORMAT
###############################################
sub prettydate {
	my $holdme = $_[0];

	my ($holdyear, $holdmonth, $holdday) = split(/\-/,$holdme);
	my $newdate = "$holdmonth\-$holdday\-$holdyear";
		$newdate =~ s/--/-/gi;
		return($newdate);
} # end sub prettydate


###############################################
## START: CHECK IF USER SHOULD SEE THIS ORGCODE
###############################################
sub checkuserviewable {
	my ($checkuser, $showorgcode, $showfundyear) = @_;
	my $userverified = "no";

	$userverified = "yes" if ($checkuser eq 'whoover');
	$userverified = "yes" if ($checkuser eq 'akriegel');
	$userverified = "yes" if ($checkuser eq 'sferguso');
	$userverified = "yes" if ($checkuser eq 'blitke');
	$userverified = "yes" if ($checkuser eq 'vdimock');

#	$userverified = "yes" if (($checkuser eq 'hwilliam') && (
#			(($showfundyear eq '0305') && (($showorgcode =~ 'D1XX1') || ($showorgcode =~ 'Subtotal S')) )
#			|| (($showfundyear eq '0974') && ($showorgcode =~ 'S'))
#			));


#	$userverified = "yes" if (($checkuser eq 'drainey') && ( 
#			( ($showfundyear eq '0090') && (($showorgcode eq 'T1XX1') || ($showorgcode eq 'T1XX2') || ($showorgcode eq 'T1XX3')) )
#		||	( ($showfundyear eq '0878') && ($showorgcode =~ 'R'))	
#			));

	$userverified = "yes" if (($checkuser eq 'cjordan') && ( 
			( ($showfundyear eq '0090') && (($showorgcode eq 'T2XX1') || ($showorgcode eq 'T2XX2') || ($showorgcode eq 'T2XX3')) )
		||  ( ($showfundyear eq '0143'))
		||	( ($showfundyear eq '0386') && (($showorgcode =~ 'T') || ($showorgcode =~ 'zSub')))
		||	( ($showfundyear eq '0436') && ($showorgcode =~ 'L'))
		||	( ($showfundyear eq '0446') && ($showorgcode =~ 'L'))
		||	( ($showfundyear eq '0518') && ($showorgcode =~ 'L'))
		||	( ($showfundyear eq '0538') && ($showorgcode =~ 'L'))
		||	( ($showfundyear eq '0869') && ($showorgcode =~ 'L'))
		||	( ($showfundyear eq '0710') && ($showorgcode =~ 'L'))
		||	( ($showfundyear eq '0878') && ($showorgcode =~ 'L'))
		||	( ($showfundyear eq '0974') && ($showorgcode =~ 'L'))
		||	( ($showfundyear eq '0363') && (($showorgcode =~ 'T') || ($showorgcode =~ 'zSub')) )
			)); 

	$userverified = "yes" if ( 
						(($checkuser eq 'hwilliam') && ($showfundyear eq '0305') && ($showorgcode eq 'D1XX1'))
					 || (($checkuser eq 'hwilliam') && ($showfundyear eq '0305') && ($showorgcode eq 'D1XX3'))
					 || (($checkuser eq 'hwilliam') && ($showfundyear eq '0690') && ($showorgcode eq 'D1XXX'))
					 || (($checkuser eq 'hwilliam') && ($showfundyear eq '0821') && ($showorgcode eq 'D1XXX'))
					 || (($checkuser eq 'mboethel') && ($showfundyear eq '0100') && ($showorgcode =~ 'F'))
					 || (($checkuser eq 'mboethel') && ($showfundyear eq '0100') && ($showorgcode =~ 'K5K5X'))
					 || (($checkuser eq 'zrudo') && ($showfundyear eq '0974') && ($showorgcode =~ 'L2XXX')) 
					  );

	$userverified = "yes" if (($checkuser eq 'cmoses') && ( 
		(($showfundyear eq '0971') && ($showorgcode eq 'F8F8X'))
	||	(($showfundyear eq '0100') && ($showorgcode =~ 'K6'))
	||  (($showfundyear eq '0974') && ($showorgcode =~ 'P'))
	|| 	($showfundyear eq '0346')
	|| 	($showfundyear eq '0897')		
		));

	$userverified = "yes" if (($checkuser eq 'dbrown') && ( 
		(($showfundyear =~ '0315') && ($showorgcode eq 'M1XXX'))
		) );

	$userverified = "yes" if (($checkuser eq 'jwestbro') && (($showfundyear eq '0089') || ($showfundyear eq '0203')
    	||  ($showfundyear eq '0296') || ($showfundyear eq '0376')
    	|| (($showfundyear eq '0298') && ($showorgcode =~ 'J'))
    	|| (($showfundyear eq '0291') && ($showorgcode =~ 'J'))
    	|| (($showfundyear eq '0548') && ($showorgcode =~ 'J'))
    	|| (($showfundyear eq '0558') && ($showorgcode =~ 'J'))
    	|| (($showfundyear eq '0730') && ($showorgcode =~ 'J'))
    	|| (($showfundyear eq '0740') && ($showorgcode =~ 'J'))
    	|| (($showfundyear eq '0760') && ($showorgcode =~ 'J'))
    	|| (($showfundyear eq '0841') && ($showorgcode =~ 'J'))
    	|| (($showfundyear eq '0878') && ($showorgcode =~ 'J'))
    	|| (($showfundyear eq '0974') && ($showorgcode =~ 'J'))
    	    ));

	$userverified = "yes" if (($checkuser eq 'scaverly') && ($showfundyear =~ '0669') );
	
	$userverified = "yes" if (($checkuser eq 'kmurphy') && ($showfundyear =~ '0296') );
	$userverified = "yes" if (($checkuser eq 'kmurphy') && ($showfundyear =~ '0298') );
	$userverified = "yes" if (($checkuser eq 'kmurphy') && ($showfundyear =~ '0291') );
	$userverified = "yes" if (($checkuser eq 'kmurphy') && ($showfundyear =~ '0730') );
	$userverified = "yes" if (($checkuser eq 'kmurphy') && ($showfundyear =~ '0760') );
	$userverified = "yes" if (($checkuser eq 'kmurphy') && ($showfundyear =~ '0841') );
	$userverified = "yes" if (($checkuser eq 'kmurphy') && ($showfundyear =~ '0902') );
	$userverified = "yes" if (($checkuser eq 'zrudo') && ($showfundyear =~ '0325') );
	$userverified = "yes" if (($checkuser eq 'zrudo') && ($showfundyear =~ '0446') );
	$userverified = "yes" if (($checkuser eq 'zrudo') && ($showfundyear =~ '0710') );

	$userverified = "yes" if (($checkuser eq 'ktimmons') && ($showfundyear =~ '0897') );

	$userverified = "yes" if (($checkuser eq 'lwood') && (
			($showfundyear eq '0143') || ($showfundyear eq '0538') || ($showfundyear eq '0178') || ($showfundyear eq '0177') || ($showfundyear eq '0386')	
         ||	(($showfundyear eq '0122') && ($showorgcode =~ 'A5'))
         ||	( ($showfundyear eq '0974') && ($showorgcode =~ 'D3XXX'))
			));

	$userverified = "yes" if (($checkuser eq 'amuoneke') && (
         	(($showfundyear eq '0122') && ($showorgcode =~ 'A2'))
			));

	$userverified = "yes" if (($checkuser eq 'etobia') && (
			($showfundyear eq '0285') 
		 || ($showfundyear eq '0406')
		 || ($showfundyear eq '0416')
		 || ($showfundyear eq '0426')
		 || ($showfundyear eq '0457')
		 ||	(($showfundyear eq '0619') && ($showorgcode =~ 'M'))
		 ||	(($showfundyear eq '0629') && ($showorgcode =~ 'M'))
		 ||	(($showfundyear eq '0649') && ($showorgcode =~ 'M'))
		 ||	( ($showfundyear eq '0659') && ($showorgcode =~ 'M'))   
			));

	$userverified = "yes" if (($checkuser eq 'rjarvis') && (
			($showfundyear eq '0285')
		 || ($showfundyear eq '0142')	
		 || ($showfundyear eq '0315')
		 || ($showfundyear eq '0406')
		 || ($showfundyear =~ '0417')
		 || ($showfundyear eq '0416')
		 || ($showfundyear eq '0426')
		 || ($showfundyear eq '0457')
		 || ($showfundyear eq '0497')
		 || ($showfundyear eq '0528')
		 || ($showfundyear eq '0619')
		 || ($showfundyear eq '0629')
		 || ($showfundyear eq '0649')
		 || ($showfundyear eq '0659')
		 || ($showfundyear eq '0720')
		 || ($showfundyear eq '0750')
		 || ($showfundyear eq '0770')
		 || ($showfundyear eq '0851')
		 ||	(($showfundyear eq '0878') && ($showorgcode =~ 'M'))
		 ||	(($showfundyear eq '0888') && ($showorgcode =~ 'M'))
		 ||	(($showfundyear eq '0780') && ($showorgcode =~ 'M'))
		 || (($showfundyear eq '0974') && ($showorgcode =~ 'M'))
		 || ($showfundyear eq '0305')
			));
			
	$userverified = "yes" if ( (($checkuser eq 'mmorriss') || ($checkuser eq 'aneeley')) && (($showfundyear =~ '0352') || ($showfundyear =~ '0351') || ($showorgcode =~ 'B1XXX')) );

	$userverified = "yes" if (($checkuser eq 'mvadenki') && ( 
			( ($showfundyear eq '0090') && (($showorgcode eq 'T3XX4') || ($showorgcode eq 'T4XX1') || ($showorgcode eq 'T4XX2')) )
		||	( ($showfundyear eq '0090') && (($showorgcode eq 'T1XX2') || ($showorgcode eq 'T1XX3')) )			
		||	($showfundyear eq '0152')
		||	($showfundyear eq '0477')
		||	($showfundyear eq '0578')
		||	($showfundyear eq '0589')
		||	($showfundyear eq '0599')
		|| (($showfundyear eq '0497') && ($showorgcode =~ 'E'))
		|| (($showfundyear eq '0122') && ($showorgcode =~ 'A3'))
		|| (($showfundyear eq '0122') && ($showorgcode =~ 'A4'))
		|| (($showfundyear eq '0649') && ($showorgcode =~ 'V'))
		|| (($showfundyear eq '0659') && ($showorgcode =~ 'V'))
		|| (($showfundyear eq '0669') && ($showorgcode =~ 'V'))
		|| (($showfundyear eq '0700') && ($showorgcode =~ 'V'))
		|| (($showfundyear eq '0710') && ($showorgcode =~ 'V'))
		|| (($showfundyear eq '0878') && ($showorgcode =~ 'V')) 
		|| (($showfundyear eq '0974') && ($showorgcode =~ 'V')) 
		||	( ($showfundyear eq '0256') && (($showorgcode =~ 'E') || ($showorgcode =~ 'Sub')) )	
		||	( ($showfundyear eq '0266') && (($showorgcode =~ 'E') || ($showorgcode =~ 'Sub')) )	
		||	( ($showfundyear eq '0305') && (($showorgcode eq 'E1XXX') || ($showorgcode =~ 'Sub')) )
		||	( ($showfundyear eq '0305') && (($showorgcode eq 'E2XXX') || ($showorgcode =~ 'Sub')) )
		||	( ($showfundyear eq '0315') && (($showorgcode eq 'E1XXX') || ($showorgcode =~ 'Sub')) )
		||	( ($showfundyear eq '0315') && (($showorgcode eq 'E2XXX') || ($showorgcode =~ 'Sub')) )
		||	( ($showfundyear eq '0325') && (($showorgcode eq 'E1XXX') || ($showorgcode =~ 'Sub')) )	
		||	( ($showfundyear eq '0974') && (($showorgcode eq 'E1XXX') || (($showorgcode =~ 'Sub') && ($showorgcode =~ 'V')) ) )	
		||	( ($showfundyear eq '0878') && (($showorgcode =~ 'E') || ($showorgcode =~ 'Subtotal E')) && ($showorgcode !~ 'Subtotal V'))	
		||	( ($showfundyear eq '0220') && (($showorgcode =~ 'E') || ($showorgcode =~ 'Sub')) )	
		||	( ($showfundyear eq '0270') && (($showorgcode =~ 'E') || ($showorgcode =~ 'Sub')) )	
		||	( ($showfundyear eq '0215') && (($showorgcode =~ 'E') || ($showorgcode =~ 'Sub')) )	
		||	( ($showfundyear eq '0245') && (($showorgcode =~ 'E') || ($showorgcode =~ 'Sub')) )	
		||	( ($showfundyear eq '0185') && (($showorgcode =~ 'E') || ($showorgcode =~ 'Sub')) )		
			));


#	$userverified = "yes" if ( ($checkuser eq 'mvadenki') && ( ($showfundyear eq '0090') && (($showorgcode eq 'T1XX2') || ($showorgcode eq 'T1XX3')) ) );
	
#	$userverified = "yes" if ( ($checkuser eq 'mvadenki') && (
#	   (($showfundyear eq '0974') && ($showorgcode =~ 'V')) 
#	|| (($showfundyear eq '0363') && ($showorgcode =~ 'T3L1X')) 
#	));

	$userverified = "yes" if (($checkuser eq 'mdodson') && (
			(($showfundyear eq '0305') && ($showorgcode =~ 'E'))
			|| (($showfundyear eq '0315') && ($showorgcode =~ 'E'))
			|| (($showfundyear eq '0122') && ($showorgcode =~ 'A3'))
			|| (($showfundyear eq '0619') && ($showorgcode =~ 'V'))
			|| (($showfundyear eq '0629') && ($showorgcode =~ 'V'))
			|| (($showfundyear eq '0780') && ($showorgcode =~ 'V'))
			|| (($showfundyear eq '0589') && ($showorgcode =~ 'V'))
			|| (($showfundyear eq '0679') && ($showorgcode =~ 'V'))
			|| (($showfundyear eq '0689') && ($showorgcode =~ 'V'))
			|| (($showfundyear eq '0831') && ($showorgcode =~ 'E'))
			));


	# RTEC CODES
	$userverified = "no" if (($checkuser eq 'vdimock') && (
		($showfundyear eq '0100')
		|| ($showfundyear eq '0190')
		|| ($showfundyear eq '0897')
		|| ($showfundyear eq '0971')
		|| (($showfundyear eq '0974') && ($showorgcode !~ 'D'))
		|| ($showfundyear eq '0976')
		));
	
	$userverified = "yes" if (($checkuser eq 'zrudo') && (($showfundyear eq '0446') && ($showorgcode eq 'L1XXX')) );
	$userverified = "yes" if (($checkuser eq 'dbrown') && (($showfundyear eq '0315') && ($showorgcode eq 'M1XXX')) );

	return($userverified);

} # end function checkuserviewable
##############################################
## END: CHECK IF USER SHOULD SEE THIS ORGCODE
###############################################


#################################################################
## START SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################
sub date_mysqlmonth2standard {
	my $date2transform = $_[0];
	my ($thisyear,$thismonth) = split(/\-/,$date2transform);
	$date2transform = "$thismonth\/$thisyear";
	$date2transform = "" if $date2transform eq '//';
	return($date2transform);
}
#################################################################
## END SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################


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


####################################################################
## START: SUBROUTINE print_subtotal
####################################################################
sub print_subtotal {
	my $this_subtotal = $_[0];
	my $this_summary_code = $_[1];
	$this_subtotal = &format_number($this_subtotal, "2", "yes");
	my $line_item_name = "Line Item";
	   $line_item_name = "SALARIES &amp; WAGES" if ($this_summary_code eq '00');
	   $line_item_name = "EMPLOYEE BENEFITS" if (($this_summary_code eq '01') || ($this_summary_code eq '02'));
	   $line_item_name = "CONSULTANT FEES" if ($this_summary_code eq '03');
	   $line_item_name = "STAFF TRAVEL" if ($this_summary_code eq '04');
	   $line_item_name = "CONSULTANT TRAVEL" if ($this_summary_code eq '05');
	   $line_item_name = "FACILITIES OPERATIONS" if ($this_summary_code eq '06');
	   $line_item_name = "COMMUNICATIONS" if ($this_summary_code eq '07');
	   $line_item_name = "REPRODUCTION" if ($this_summary_code eq '08');
	   $line_item_name = "SUPPLIES" if ($this_summary_code eq '09');
	   $line_item_name = "EQUIPMENT/SUB-CONTRACTS" if ($this_summary_code eq '10');
	   $line_item_name = "OTHER" if (($this_summary_code eq '12') || ($this_summary_code eq '13') || ($this_summary_code eq '14') || ($this_summary_code eq '15') || ($this_summary_code eq '16') || ($this_summary_code eq '17') || ($this_summary_code eq '18'));
	   $line_item_name = "INDIRECT COSTS" if ($this_summary_code eq '19');

#				if (($findobjclasscode eq '01') || ($findobjclasscode eq '02')) {
#					$command = "select * from oftsbudgetsckjvpr where gl like '91' AND ((objcode like '01%') OR (objcode like '02%')) AND orgcode like '$showorgcode' and fundyear like '$showfundyear' and date like '$detailmysqlyearmonth%' order by objcode";
#				}
#
#				if (($findobjclasscode eq '12') || ($findobjclasscode eq '13') || ($findobjclasscode eq '14') || ($findobjclasscode eq '15') || ($findobjclasscode eq '16') || ($findobjclasscode eq '17') || ($findobjclasscode eq '18')) {
#					$command = "select * from oftsbudgetsckjvpr where gl like '91' AND ((objcode like '11%') OR (objcode like '12%') OR (objcode like '13%') OR (objcode like '14%') OR (objcode like '15%') OR (objcode like '16%') OR (objcode like '17%') OR (objcode like '18%')) AND orgcode like '$showorgcode' and fundyear like '$showfundyear' and date like '$detailmysqlyearmonth%' order by objcode";
#				}
#
#				if ($findobjclasscode eq '99') {
#					$command = "select * from oftsbudgetsckjvpr where gl like '91' AND (objcode like '19%') AND orgcode like '$showorgcode' and fundyear like '$showfundyear' and date like '$detailmysqlyearmonth%' order by objcode";
#				}

print<<EOM;
<TR BGCOLOR="#FFFFFF"><TD class=small colspan=14 ALIGN=RIGHT><strong>Subtotal for $line_item_name</strong></TD>
	<TD class=small ALIGN=RIGHT>\$$this_subtotal</TD>
	<TD class=small colspan=3></TD>
	</TR>
EOM
####################################################################
## END: SUBROUTINE print_subtotal
####################################################################

} # END SUBROUTINE PRINT_SUBTOTAL


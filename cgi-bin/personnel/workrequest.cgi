#!/usr/bin/perl

# Copyright 2000 by Southwest Educational Development Laboratory
# 2001-10-18 Modified for new Staff Page template, moved to:http://www.sedl.org/staff/personnel/workrequest.cgi
# Written by Brian Litke 5-30-2000 for form at http://www.sedl.org/staff/reports/ofts-workrequest.cgi

use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);

use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "", "");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 
use intranetcommoncode; 

my $query = new CGI;

my $browser = $ENV{"HTTP_USER_AGENT"};
my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};


#### Set/Initialize these variables ###
my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS

my $htmlroot = '/home/httpd/html';

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
	my $date_full_mysql = POSIX::strftime('%D', localtime(time)); # Full date in human-readable format  (e.g. 03/06/08)
	my $date_full_mysql = POSIX::strftime('%F', localtime(time)); # Full date in machine-readable "mysql-compatible" format (e.g. 2008-03-06)
	my $time_hour = POSIX::strftime('%l', localtime(time)); # Hour (e.g. 9 or 9)
	my $time_hour_mil = POSIX::strftime('%k', localtime(time)); # Hour in military notation (e.g. 9 or 21)
	my $time_hour_leadingzero = POSIX::strftime('%I', localtime(time)); # Hour w/leadingsero (e.g. 09 or 09)
	my $time_hour_mil_leadingzero = POSIX::strftime('%H', localtime(time)); # Hour in military notation w/leadingsero (e.g. 09 or 21)
	my $time_min = POSIX::strftime('%M', localtime(time)); # Date in month (e.g. 39)
	my $time_sec = POSIX::strftime('%S', localtime(time)); # Date in month (e.g. 38)

	my $timestamp = "$year$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; # 14-digit timestamp (e.g. 20080306143938)

#my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
#   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
	my $this_user_id = "$timestamp$ipnum$ipnum2";
	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################


### GET VARIABLES FROM THE FORM IN CASE USER DID NOT ENTER ALL FIELDS
my $location = $query->param('location');
   $location = "showform" if ($location eq '');
my $unique_save_id = $query->param('unique_save_id');

my $name = $query->param('name');
my $request_type = $query->param('request_type');
my $request_details = $query->param('request_details');
my $building_location = $query->param('building_location');

my $startdate_m = $query->param('startdate_m');
my $startdate_d = $query->param('startdate_d');
my $startdate_y = $query->param('startdate_y');
my $starttime = $query->param('starttime');

my $enddate_m = $query->param('enddate_m');
my $enddate_d = $query->param('enddate_d');
my $enddate_y = $query->param('enddate_y');
my $endtime = $query->param('endtime');

my $startdate_mysql = "";
	if (($startdate_y ne '') && ($startdate_m ne '') && ($startdate_d ne '')) {
		$startdate_mysql = "$startdate_y-$startdate_m-$startdate_d";
	}
my $enddate_mysql = "";
	if (($enddate_y ne '') && ($enddate_m ne '') && ($enddate_d ne '')) {
		$enddate_mysql = "$enddate_y-$enddate_m-$enddate_d";
	}

my $errormessage = "";

if (($request_type eq '') && ($location eq 'process_request')) {
	$errormessage = "<font color=red>Please enter a maintenance request before submitting.</font>";
	$location = "showform";
}

#######################################
# READ IN SEDL HEADER AND FOOTER HTML #
#######################################
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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("103"); # 103 is the PID for this page in the intranet database

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";


## COOKIE VARIABLES
my $expdate = "Fri, 25-Dec-2015 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";

####################################################
# START: GRAB STAFF ID, IF ANY, FROM COOKIE
####################################################
my $cookie_ss_session_id = ""; # TRACK SESSION ID
my $cookie_ss_staff_id = ""; # TRACK USER ID
my $cookie_search_fav = ""; # TRACK SEARCH PREFERENCE
my $cookie_random_content_id = "1"; # RANDOMIZE SOME CONTENT ON THE HOME PAGE
	my(%cookies) = getCookies();
	foreach (sort(keys(%cookies))) {
		$cookie_ss_staff_id = $cookies{$_} if ($_ eq 'staffid');
		
	} # END FOREACH LOOP
####################################################
# END: GRAB STAFF ID, IF ANY, FROM COOKIE
####################################################

## PRINT PAGE HEADER
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL - Work Order Request</TITLE>
</HEAD>
$htmlhead
EOM


#################################################################################
#################################################################################
#################################################################################
## IF THE USER ENTERED ALL INFORMATION, START HANDLING THE DATA
## THEN SAVE THE DATA TO A FILE AND SEND AN E-MAIL WITH THE DATA
if ($location eq 'process_request') {

## REMOVE TABS AND CARRIAGE RETURNS
## REMOVE CARRIAGE RETURNS & TABS FROM OPEN-ENDED VARIABLES
$name = &commoncode::cleanthisfordb ($name);
$building_location = &commoncode::cleanthisfordb ($building_location);
$request_type = &commoncode::cleanthisfordb ($request_type);
$request_details = &commoncode::cleanthisfordb ($request_details);
$startdate_mysql = &commoncode::cleanthisfordb ($startdate_mysql);
$enddate_mysql = &commoncode::cleanthisfordb ($enddate_mysql);
$starttime = &commoncode::cleanthisfordb ($starttime);
$endtime = &commoncode::cleanthisfordb ($endtime);
$unique_save_id = &commoncode::cleanthisfordb ($unique_save_id);

#print "<p class=\"info\">Got to stage 1</p>";
## CHECK IF RECORD EXISTS YET WITH THIS SAVEID
	my $command = "SELECT wr_unique_save_id FROM work_request WHERE wr_unique_save_id LIKE '$unique_save_id'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_entries = $sth->rows;
#print "<p class=\"info\">Got to stage 2</p>";

## IF EXISTS, WARN USER, ELSE PROCEED
	if ($num_matches_entries > 0) {
		$errormessage = "It appears this work request has already been entered and this is a duplicate entry.  Thus, your data was NOT saved.  Contact Brian Litke at ext. 6529 if you require assistance.";
#		$location = "showform";
		$startdate_mysql = &commoncode::date2standard($startdate_mysql);
		$enddate_mysql = &commoncode::date2standard($enddate_mysql);
		$starttime = "($starttime)" if ($starttime ne '');
		$endtime = "($endtime)" if ($endtime ne '');

	} else {
	
## SAVE DATA TO DB
## WRITE THE SURVEY RESULTS TO A FILE
	my $command = "INSERT into work_request VALUES ('', '$name', '$building_location', '$request_type', '$request_details', '$startdate_mysql', '$enddate_mysql', '$starttime', '$endtime', '$unique_save_id', '$timestamp')";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;


#print "<p class=\"info\">Got to stage 3</p>";
		$startdate_mysql = &commoncode::date2standard($startdate_mysql);
		$enddate_mysql = &commoncode::date2standard($enddate_mysql);
		$starttime = "($starttime)" if ($starttime ne '');
		$endtime = "($endtime)" if ($endtime ne '');

## SEND AN E-MAIL

# These are for mail notification of guest events
my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
my $recipient = 'maintenance@sedl.org';
#   $recipient = 'brian.litke@sedl.org'; # for testing only
my $fromaddr = 'webmaster@sedl.org';


open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: Work Request Form <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Data from Work Request Form

The following data was received from the Work Request Form at:
http://www.sedl.org/staff/personnel/workrequest.cgi

The data from this form has been saved to a database and can be viewed at: 
http://www.sedl.org/staff/personnel/workrequest-report.cgi



The Work Request Information starts here:

REQUEST FROM:
-------------
$name


ROOM/LOCATION:
---------------
$building_location


WORK REQUEST TYPE:
-------------------
$request_type


WORK REQUEST DETAILS:
-----------------------
$request_details


DATE ROOM RE-CONFIGURATION NEEDS TO BE READY:
----------------------------------------------
$startdate_mysql $starttime




DATE ROOM CAN RETURN TO NORMAL CONFIGURATION:
----------------------------------------------
$enddate_mysql $endtime






---End of Work Request Data---

USER IP and BROWSER:
---------------------
BROWSER: $browser
HOST: $ipnum
IP: $ipnum2

EOM

close(NOTIFY);


	} # END HANDLING NON-DUPLICATE ENTRY

## PRINT PAGE HEADER
print <<EOM;
<H1 align=center>Thank You</H1>
EOM
print "<p class=\"alert\">$errormessage</p>" if ($errormessage ne '');
print<<EOM;
<p>
Thank you <span style="COLOR:#8A4416">$name</span> for submitting the work request:
<?p>
  <UL>
  ($request_type)
  </UL>
<p>
at the location <span style="COLOR:#8A4416;">$building_location</span>.
</p>
<p>
An e-mail has been sent to maintenance staff in AS.
</p>
EOM
if ($request_details ne '') {
print<<EOM;
<p></p>
<p>
<strong>Request details:</strong><br>
$request_details
</p>

EOM
}
if ($startdate_mysql ne '') {
print<<EOM;
<p></p>

<u>Date room configuration needs to be ready:</u><br>
$startdate_mysql $starttime
<br>
<br>
<u>Date room can be returned to normal configuration:</u><br>
$enddate_mysql $endtime

EOM
}
print end_html;
}
#################################################################################
## END LOCAITON: process_request
#################################################################################


#################################################################################
## PRINT THE FORM IF THE USER NEEDS TO ENTER INFORMATION
#################################################################################
if ($location eq 'showform') {


print<<EOM;
<H1>Building Maintenance Request</H1>
EOM
print "<p class=\"alert\">$errormessage</p>" if ($errormessage ne '');
print<<EOM;
<script language="JavaScript" type="text/JavaScript">
<!--
		
function checkFields() { 

	// Question - RADIO
	var user_input = 0;
	for (i=0;i < 4;i++) {
		if (document.form2.request_type[i].checked == true) {
			user_input++;
		}
	}
	if (user_input < 1) {
		alert("Please indicate a Work Request Type.");
		return false;
	}

}
// -->
</script>
<p>
Please use this form to submit your work/maintenance request. 
</p>

<form method="post" action="workrequest.cgi" id="form2" name="form2" onsubmit="return checkFields()">

<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="0">

<TR><TD VALIGN=TOP WIDTH=150><P><strong><label for="name">Your name<BR>(Optional)</label></strong></TD>
    <TD WIDTH="420">
EOM
	&intranetcommoncode::printform_stafflist_select("name", $cookie_ss_staff_id);
print<<EOM;
	</TD></TR>
EOM
#	print "$command";

print<<EOM;
<TR><TD VALIGN=TOP WIDTH=150><P><strong>Room/Location where<BR>maintenance is needed</strong></p></TD>
    <TD WIDTH="420"><INPUT TYPE="TEXT" NAME="building_location" SIZE="50"></TD></TR>

<TR><TD VALIGN="TOP" WIDTH="150"><P><strong>Work Request Type</strong></p></TD>
    <TD WIDTH="420">
			<table>
			<tr><td><input type="radio" name="request_type" id="request_type2" value="Room arrangement"></td>
				<td><label for="request_type2">Room arrangement</label></td></tr>
			<tr><td><input type="radio" name="request_type" id="request_type1" value="Light bulb replacement"></td>
				<td><label for="request_type1">Light bulb replacement</label></td></tr>
			<tr><td><input type="radio" name="request_type" id="request_type3" value="Equipment moved"></td>
				<td><label for="request_type3">Equipment moved</label></td></tr>
			<tr><td><input type="radio" name="request_type" id="request_type4" value="Other"></td>
				<td><label for="request_type4">Other (please describe below)</label></td></tr>
			</table>
			<br>
			<strong>Other information:</strong><br>
    		<TEXTAREA NAME="request_details" ROWS="5" COLS="50"></TEXTAREA>
    		<br>
    		<br>
    		<strong>If this is a room configuration request, please indicate:</strong><br><br>
    		Date/time room configuration needs to be ready:<br>
EOM
	&commoncode::print_month_menu("startdate_m", $startdate_m);
	&commoncode::print_day_menu("startdate_d", $startdate_d);
	&commoncode::print_year_menu("startdate_y", $year, $year + 1, $startdate_y);
	&show_hours_menu("starttime");
print<<EOM;
    		

			<br><br>
			Date/time room can be returned to regular meeting status.<br>
EOM
	&commoncode::print_month_menu("enddate_m", $enddate_m);
	&commoncode::print_day_menu("enddate_d", $enddate_d);
	&commoncode::print_year_menu("enddate_y", $year, $year + 1, $enddate_y);
	&show_hours_menu("endtime");
print<<EOM;
    		
    </TD></TR>
</TABLE>

EOM

print<<EOM;
<p>
Please click on the Send Request button below when you have entered all the details.
</p>
  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_request">
  <INPUT TYPE="HIDDEN" NAME="unique_save_id" VALUE="$this_user_id">
  <INPUT TYPE="SUBMIT" VALUE="Send Request">
  </div>
  </FORM>
<br><br>
<P>
To report troubles using this form, send an e-mail to <A HREF=\"mailto:webmaster\@sedl.org\">webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
EOM

}
#################################################################################
## STOP PRINTING THE ENTRY FORM HERE
#################################################################################


#print "<p>LOCAITON: $location</p>";
print<<EOM;
$htmltail
EOM



####################################################################
##  SUBROUTINES USED BY THIS SCRIPT
####################################################################


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
## START: SUBROUTINE show_hours_menu
######################################
sub show_hours_menu {
	my $field_name = $_[0];
print<<EOM;
	<select name="$field_name">
	<option value=""></option>
	<option value="7 am">7 am</option>
	<option value="8 am">8 am</option>
	<option value="9 am">9 am</option>
	<option value="10 am">10 am</option>
	<option value="11 am">11 am</option>
	<option value="12 noon">12 noon</option>
	<option value="1 pm">1 pm</option>
	<option value="2 pm">2 pm</option>
	<option value="3 pm">3 pm</option>
	<option value="4 pm">4 pm</option>
	<option value="5 pm">5 pm</option>
	<option value="6 pm">6 pm</option>
	<option value="7 pm">7 pm</option>
	<option value="8 pm">8 pm</option>
	</select>
EOM
}
######################################
## END: SUBROUTINE show_hours_menu
######################################

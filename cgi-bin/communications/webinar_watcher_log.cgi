#!/usr/bin/perl

#####################################################################################################
# Copyright 2008 by Southwest Educational Development Laboratory
#
# This script is used by Communications to manage the online About SEDL: Board of Directors list
# Written by Brian Litke 10-25-2007
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
my $item_label = "Webinar Viewer";
my $site_label = "Webinar Viewer Log";
my $public_site_address = "http://txcc.sedl.org/resources/webinars/";

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

my $download_webinar_id = $query->param("download_webinar_id");

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
	$dsn = "DBI:mysql:database=intranet;host=localhost";
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
	$dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";

		while (my @arr = $sth->fetchrow) {
		my ($ss_session_id, $ss_staff_id, $ss_session_last_active, $ss_ip_number, $ss_session_active, $ss_staff_project, $ss_search_site, $ss_last_page_viewed) = @arr;
			## IF SESSION STILL ACTIVE, SET TIMESTAMP IN SESSION DB
				my $command_update_session = "REPLACE into staff_sessions VALUES ('$ss_session_id', '$ss_staff_id', '$timestamp', '$ipnum2', '', '', '' ,'')";
				$dsn = "DBI:mysql:database=intranet;host=localhost";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
				#my $num_matches = $sth->rows;

#					$validuser = "yes" if ($ss_staff_id eq 'blitke');
#					$validuser = "yes" if ($ss_staff_id eq 'cmoses');
#					$validuser = "yes" if ($ss_staff_id eq 'emueller');
#					$validuser = "yes" if ($ss_staff_id eq 'jwackwit');
#					$validuser = "yes" if ($ss_staff_id eq 'ktimmons');
#					$validuser = "yes" if ($ss_staff_id eq 'lshankla');
#					$validuser = "yes" if ($ss_staff_id eq 'macuna');
#					$validuser = "yes" if ($ss_staff_id eq 'nreynold');
					$validuser = "yes";
		
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
Welcome to the $site_label. This database is used by SEDL staff to monitor what 
users have been logging on to SEDL webinars. Please enter your SEDL user ID and password to view the database.
</p>
<FORM ACTION="webinar_watcher_log.cgi" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP"><strong>Your user ID</strong><br>
  		  (ex: sliberty)</TD>
      <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD>
</TR>
<TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR>
  			<SPAN class="small">(not your e-mail password)</SPAN></TD>
      <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD>
 </TR>
</TABLE>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Log In Now">
  </div>
  </FORM>
<P>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.

$htmltail

EOM
} 
#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################




#################################################################################
## START: LOCATION = download_data
#################################################################################
if ($location eq 'download_data') {

## PRINT SIGNUP FORM
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label: List of $item_label\s</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#ffffff;">
<tr><td><h1><A HREF="webinar_watcher_log.cgi">$site_label</A>
		<br>Download data</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="webinar_watcher_log.cgi?location=logout">logout</A>)
	</td></tr>
</table>
EOM

	if ($validuser ne 'yes') {
		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view $item_label\s.</FONT>";
	}

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

my $command = "select * from webinar_archive_views where wav_webinar_id LIKE '$download_webinar_id'";
	$command .= " order by wav_timestamp DESC";


#print "<P>$command<P>";
$dsn = "DBI:mysql:database=intranet;host=localhost";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;

	## OPEN FILE HANDLE
	open(SAVED_FILE_LOCATION,">/home/httpd/html/temp/webinar_watchers.xls");
	print SAVED_FILE_LOCATION "Webinar Name\tFirst Name\tLast Name\tE-mail\tuOrg\tTitle\tState\tuserip\tTimestamp\n";

	while (my @arr = $sth->fetchrow) {
		my ($wav_id, $wav_webinar_id, $wav_userfirstname, $wav_userlastname, $wav_useremail, $wav_userorg, $wav_usertitle, $wav_userstate, $wav_userip, $wav_timestamp) = @arr;
			$wav_timestamp = &convert_timestamp_2pretty_w_date($wav_timestamp, "yes");
			print SAVED_FILE_LOCATION "$wav_webinar_id\t$wav_userfirstname\t$wav_userlastname\t$wav_useremail\t$wav_userorg\t$wav_usertitle\t$wav_userstate\t$wav_userip\t$wav_timestamp\n";

	} # END DB QUERY LOOP

	close(SAVED_FILE_LOCATION);
print<<EOM;
<p>
Click here to <a href="/temp/webinar_watchers.xls">download the data file</a>.
</p>
<p>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
<P>
The $site_label is located at <A HREF="$public_site_address">$public_site_address</A>.
$htmltail
EOM
} 
#################################################################################
## END: LOCATION = download_data
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
<TITLE>SEDL Intranet | $site_label: List of $item_label\s</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#ffffff;">
<tr><td><h1><A HREF="webinar_watcher_log.cgi">$site_label</A>
		<br>List of $item_label\s</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="webinar_watcher_log.cgi?location=logout">logout</A>)
	</td></tr>
</table>
EOM

	if ($validuser ne 'yes') {
		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view $item_label\s.</FONT>";
	}

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

###################################################
## START: PRINT MENU TO ALLOW DATA DOWNLOAD
###################################################
print<<EOM;
<strong>Download data for the webinar:</strong>
<FORM ACTION="webinar_watcher_log.cgi" METHOD=POST>
<select name="download_webinar_id">
<option></optioN>
EOM
my $command = "select wav_webinar_id, COUNT(wav_webinar_id) from webinar_archive_views group by wav_webinar_id";
$dsn = "DBI:mysql:database=intranet;host=localhost";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
	while (my @arr = $sth->fetchrow) {
		my ($wav_webinar_id, $this_count) = @arr;
		my $s = "s";
		   $s = "" if ($this_count == 1);
		print "<option value=\"$wav_webinar_id\">$wav_webinar_id ($this_count view$s)</option>";
	}
print<<EOM;
</select>
  <UL>
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="download_data">
  <INPUT TYPE="SUBMIT" VALUE="Download data">
  </UL>
  </FORM>
EOM
###################################################
## END: PRINT MENU TO ALLOW DATA DOWNLOAD
###################################################

print<<EOM;
<h2>Summary of Non-SEDL Watchers</h2>
<table border="1" cellpadding="4" cellspacing="2" style="background-color:#ffffff;">
EOM

	## SELECT DATA FROM ALL WEBINARS AND MAKE A TIGHT LIST OF INDIVISUAL WATCHERS
	my $previous_wav_useremail = "";
	my $previous_wav_webinar_id = "";
	my $command = "select * from webinar_archive_views where wav_useremail NOT LIKE '%sedl.org%'";
	   $command .= " order by wav_webinar_id, wav_useremail";
	
	$dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
my $num_matches_items = $sth->rows;

	while (my @arr = $sth->fetchrow) {
		my ($wav_id, $wav_webinar_id, $wav_userfirstname, $wav_userlastname, $wav_useremail, $wav_userorg, $wav_usertitle, $wav_userstate, $wav_userip, $wav_timestamp) = @arr;
		if ($previous_wav_webinar_id ne $wav_webinar_id) {
			print "<tr bgcolor=\"#ebebeb\"><td colspan=\"5\"><strong>$wav_webinar_id</strong></td></tr>";
		}
		if ($previous_wav_useremail ne $wav_useremail) {
			print "<tr><td style=\"font-size:10px;\">$wav_userfirstname $wav_userlastname</td>
						<td style=\"font-size:10px;\">$wav_useremail</td>
						<td style=\"font-size:10px;\">$wav_userorg</td>
						<td style=\"font-size:10px;\">$wav_usertitle</td>
						<td style=\"font-size:10px;\">$wav_userstate</td></tr>";
		}
		$previous_wav_useremail = $wav_useremail;
		$previous_wav_webinar_id = $wav_webinar_id;
	} # END DB QUERY LOOP

print "</table>";

my $command = "select * from webinar_archive_views";
	$command .= " order by wav_webinar_id, wav_timestamp DESC" if $sortby eq '';
#	$command .= " order by bod_state, bod_firstname" if $sortby eq 'state';
	$command .= " order by wav_timestamp DESC" if $sortby eq 'date';


#print "<P>$command<P>";
$dsn = "DBI:mysql:database=intranet;host=localhost";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches_items = $sth->rows;

my $col_heading_name = "Webinar Name";
   $col_heading_name = "<a href=\"webinar_watcher_log.cgi\">Webinar Name</a>" if ($sortby ne '');
my $col_heading_date = "Date Accessed";
   $col_heading_date = "<a href=\"webinar_watcher_log.cgi?sortby=date\">Date Accessed</a>" if ($sortby ne 'date');

# There are $num_matches_items $item_label\s on file for <a href="$public_site_address" target="_blank">TXCC webinars</a>.

print<<EOM;
<h2>Full Detail of all Watchers</h2>
<p></p>
<FORM ACTION="webinar_watcher_log.cgi" METHOD="POST" name="form2" id="form2">

<table border="1" cellpadding="3" cellspacing="0">
<tr bgcolor="#ebebeb">
	<td><strong>#</strong></td>
	<td><strong>$col_heading_name</strong></td>
	<td><strong>User Name/E-mail</strong></td>
	<td><strong>Org</strong></td>
	<td><strong>Title</strong></td>
	<td><strong>State</strong></td>
	<td><strong>IP Address</strong></td>
	<td><strong>$col_heading_date</strong></td>
</tr>
EOM


	if ($num_matches_items == 0) {
		print "<P><FONT COLOR=RED>There are no items in the database.</FONT>";
	}
my $counter = 1;
my $previous_wav_webinar_id = "";
my $previous_useremail = "";
	while (my @arr = $sth->fetchrow) {
		my ($wav_id, $wav_webinar_id, $wav_userfirstname, $wav_userlastname, $wav_useremail, $wav_userorg, $wav_usertitle, $wav_userstate, $wav_userip, $wav_timestamp) = @arr;
		if ($previous_useremail ne $wav_useremail) {

			my $bgcolor="BGCOLOR=\"#ffffff\"";
  				$bgcolor="BGCOLOR=\"#FFFFCC\"" if ($show_record eq $wav_id);
  				$bgcolor="BGCOLOR=\"#ebebeb\"" if ($wav_useremail =~ 'sedl.org');

			## TRANSLATE TIMESTAMP
			$wav_timestamp = &convert_timestamp_2pretty_w_date($wav_timestamp, "yes");
			
			if ($previous_wav_webinar_id ne $wav_webinar_id) {
				print "<tr BGCOLOR=\"#000000\"><td colspan=\"8\"><soan style=\"color:#ffffff;font-weight:bold;font-size:14px;\">$wav_webinar_id</span></td></tr>";
			}
$wav_userip =~ s/\./\. /gi;
print<<EOM;
<tr $bgcolor>
	<td valign="top" style="font-size:10px;"><a name="$wav_id"></a>$counter</td>
	<td valign="top" style="font-size:10px;">$wav_webinar_id</td>
	<td valign="top" style="font-size:10px;">$wav_userfirstname $wav_userlastname<br>$wav_useremail</td>
	<td valign="top" style="font-size:10px;">$wav_userorg</td>
	<td valign="top" style="font-size:10px;">$wav_usertitle</td>
	<td valign="top" style="font-size:10px;">$wav_userstate</td>
	<td valign="top" style="font-size:10px;">$wav_userip</td>
	<td valign="top" style="font-size:10px;">$wav_timestamp</td>
</tr>
EOM
		}

		$previous_useremail = $wav_useremail;
		$previous_wav_webinar_id = $wav_webinar_id;
		$counter++;
	} # END DB QUERY LOOP
print<<EOM;
</table>
<P>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
<P>
The $site_label is located at <A HREF="$public_site_address">$public_site_address</A>.
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
#   $dirtyitem =~ s/\@/\&\#x040\;/gi; # MESES UP e-MAILS SENT USING Perl, but good for displaying
   $dirtyitem =~ s/mailto\:/&#109;&#97;&#105;&#108;&#116;&#111;&#58;/gi;
   $dirtyitem =~ s/\\//g;
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/‘/\\‘/g;
   $dirtyitem =~ s/’/\\’/g;
   $dirtyitem =~ s/"/\\"/g;
   $dirtyitem = &cleanaccents2html($dirtyitem);
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
	if ($pretty_time eq '//') {
		$pretty_time = "N/A";
	}
   return($pretty_time);
}
####################################################################
## END: CONVERT DATESTAMP TO PRETTY DATE/TIME FORMAT
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
## START: SUBROUTINE printform_prefix
######################################
sub printform_prefix {
	my $form_variable_name = $_[0];
	my $selected_item = $_[1];
	my $counter_item = "0";
	my @items = ("Dr.", "Ms.", "Mrs.", "Mr.");

	print "<SELECT NAME=\"$form_variable_name\"><OPTION VALUE=\"\"></OPTION>";
	while ($counter_item <= $#items) {
		print "<OPTION VALUE=\"$items[$counter_item]\"";
		print " SELECTED" if ($items[$counter_item] eq $selected_item);
		print ">$items[$counter_item]";
		$counter_item++;
	} # END WHILE
	print "</SELECT>";
} # END subroutine printform_prefix
######################################
## END: SUBROUTINE printform_prefix
######################################
 

######################################
## START: SUBROUTINE printform_state
######################################
sub printform_state {
	my $form_variable_name = $_[0];
	my $selected_state = $_[1];
	my $counter_state = "0";
	my @states = ("National", "Regional", "AK", "AL", "AR", "AS", "AZ", "BIA", "CA", "CO", "CT", "DC", "DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "LA", "MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT", "NC", "ND", "NE", "NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VA", "VI", "VT", "WA", "WI", "WV", "WY");

	print "<select name=\"$form_variable_name\"><option value=\"\"></option>";
	while ($counter_state <= $#states) {
		print "<option VALUE=\"$states[$counter_state]\"";
		print " SELECTED" if ($states[$counter_state] eq $selected_state);
		print ">$states[$counter_state]</option>";
		$counter_state++;
	} # END WHILE
	print "</select>\n";
} # END subroutine printform_state
######################################
## END: SUBROUTINE printform_state
######################################

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
} # END: SUBROUTINE print_yes_no_menu
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

sub getFullStateName {
my $stateabbr = $_[0];
my $statename = "";
$statename = "Alabama" if ($stateabbr eq 'AL');
$statename = "Alaska" if ($stateabbr eq 'AK');
$statename = "American Samoa" if ($stateabbr eq 'AS');
$statename = "Arizona" if ($stateabbr eq 'AZ');
$statename = "Arkansas" if ($stateabbr eq 'AR');
$statename = "Bureau of Indian Affairs" if ($stateabbr eq 'BIA');
$statename = "California" if ($stateabbr eq 'CA');
$statename = "Colorado" if ($stateabbr eq 'CO');
$statename = "Connecticut" if ($stateabbr eq 'CT');
$statename = "Delaware" if ($stateabbr eq 'DE');
$statename = "District of Columbia" if ($stateabbr eq 'DC');
$statename = "Federated States of Micronesia" if ($stateabbr eq 'FSM');
$statename = "Florida" if ($stateabbr eq 'FL');
$statename = "Georgia" if ($stateabbr eq 'GA');
$statename = "Guam" if ($stateabbr eq 'GU');
$statename = "Hawaii" if ($stateabbr eq 'HI');
$statename = "Idaho" if ($stateabbr eq 'ID');
$statename = "Illinois" if ($stateabbr eq 'IL');
$statename = "Indiana" if ($stateabbr eq 'IN');
$statename = "Iowa" if ($stateabbr eq 'IA');
$statename = "Kansas" if ($stateabbr eq 'KS');
$statename = "Kentucky" if ($stateabbr eq 'KY');
$statename = "Louisiana" if ($stateabbr eq 'LA');
$statename = "Maine" if ($stateabbr eq 'ME');
$statename = "Maryland" if ($stateabbr eq 'MD');
$statename = "Massachusetts" if ($stateabbr eq 'MA');
$statename = "Marshall Islands" if ($stateabbr eq 'MH');
$statename = "Michigan" if ($stateabbr eq 'MI');
$statename = "Minnesota" if ($stateabbr eq 'MN');
$statename = "Mississippi" if ($stateabbr eq 'MS');
$statename = "Missouri" if ($stateabbr eq 'MO');
$statename = "Montana" if ($stateabbr eq 'MT');
$statename = "Nebraska" if ($stateabbr eq 'NE');
$statename = "Nevada" if ($stateabbr eq 'NV');
$statename = "New Hampshire" if ($stateabbr eq 'NH');
$statename = "New Jersey" if ($stateabbr eq 'NJ');
$statename = "New Mexico" if ($stateabbr eq 'NM');
$statename = "New York" if ($stateabbr eq 'NY');
$statename = "North Carolina" if ($stateabbr eq 'NC');
$statename = "North Dakota" if ($stateabbr eq 'ND');
$statename = "Ohio" if ($stateabbr eq 'OH');
$statename = "Oklahoma" if ($stateabbr eq 'OK');
$statename = "Oregon" if ($stateabbr eq 'OR');
$statename = "Pennsylvania" if ($stateabbr eq 'PA');
$statename = "Puerto Rico" if ($stateabbr eq 'PR');
$statename = "Rhode Island" if ($stateabbr eq 'RI');
$statename = "South Carolina" if ($stateabbr eq 'SC');
$statename = "South Dakota" if ($stateabbr eq 'SD');
$statename = "Tennessee" if ($stateabbr eq 'TN');
$statename = "Texas" if ($stateabbr eq 'TX');
$statename = "Utah" if ($stateabbr eq 'UT');
$statename = "Vermont" if ($stateabbr eq 'VT');
$statename = "Virginia" if ($stateabbr eq 'VA');
$statename = "Virgin Islands" if ($stateabbr eq 'VI');
$statename = "Washington" if ($stateabbr eq 'WA');
$statename = "Wisconsin" if ($stateabbr eq 'WI');
$statename = "West Virginia" if ($stateabbr eq 'WV');
$statename = "Wyoming" if ($stateabbr eq 'WY');
return ($statename);
}





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
my $dsn = "DBI:mysql:database=corp;host=localhost";
my $dsn2 = "DBI:mysql:database=iqa;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

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
my $item_label = "Presentation";
my $site_label = "IQA Event Presentation Manager";
my $public_site_address = "http://www.sedl.org/afterschool/iqa/presentations.cgi";

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
   $sortby = "date" if ($sortby eq '');
my $new_type = $query->param("new_type");
my $show_inactive = $query->param("show_inactive");

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("498"); # 498 is the PID for this page in the intranet database

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
				#my $num_matches = $sth->rows;

					$validuser = "yes" if ($ss_staff_id eq 'blitke');
					$validuser = "yes" if ($ss_staff_id eq 'jburnisk');
					$validuser = "yes" if ($ss_staff_id eq 'jwackwit');
		
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
<title>SEDL Intranet | $site_label</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead

<h1>$site_label</h1>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>
Welcome to the $site_label. This database is used by REL Southwest staff  
to respond to "Ask REL Southwest" questions submitted via the REL SW website. 
Please enter your SEDL user ID and password to view the database.
</p>
<FORM ACTION="ask_rel_southwest.cgi" METHOD="POST">
<table BORDER="0" cellpadding="10" CELLSPACING="0">
<tr><TD VALIGN="TOP"><strong>Your user ID</strong><br>
  		  (ex: sliberty)</td>
    <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></td></tr>
<tr><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR>
  			<SPAN class="small">(not your e-mail password)</SPAN></td>
    <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></td></tr>
</table>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Log In Now">
  </div>
  </form>
<p>
To report troubles using this form, send an e-mail to <A HREF="mailto:webmaster\@sedl.org">webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
$htmltail
EOM
} 
#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################



#################################################################################
## START: LOCATION = menu
#################################################################################
if ($location eq 'menu') {

print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label: List of $item_label\s</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1><A HREF="ask_rel_southwest.cgi">$site_label</A>
		<br>List of $item_label\s</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="ask_rel_southwest.cgi?location=logout">logout</A>)
	</td></tr>
</table>



<form ACTION="presentations.cgi" METHOD="POST">
<H1>Illinois 21st Century Community Learning Centers 2013 Spring Conference<br>
Request for Presentation Proposals Form</H1>
<p>
Click here to download a <a href="presentations_report.cgi?l=datadump">MS Excel file of the submissions</a>. 
</p>

<table border="1" cellpadding="4" cellspacing="0">
<tr style="background-color:#ebebeb;">
	<td>#</td>
	<td>Lead Contact</td>
	<td>Presentation Title</td>
	<td>Submitted</td>
	<td>Last Updated</td>
</tr>

EOM

		## QUERY DATABASE FOR SURVEY CODE AND PRE-POPULATE SURVEY CONTEXTUAL VARIABLES
		my $command_check_survey_code = "SELECT * from iqa_presentations order by pres_lastupdated DESC";
		my $dbh = DBI->connect($dsn2, "iqauser", "public");
		my $sth = $dbh->prepare($command_check_survey_code) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $counter = 0;
		while (my @arr = $sth->fetchrow) {
			my 	($pres_record_id, $pres_user_email, 
			$pres_lc_name, $pres_lc_title, $pres_lc_org, $pres_lc_address, $pres_lc_phone, $pres_lc_email, $pres_lc_fax, 
			$pres_lc_name1, $pres_lc_title1, $pres_lc_org1, $pres_lc_address1, $pres_lc_phone1, $pres_lc_email1, $pres_lc_fax1, 
			$pres_lc_name2, $pres_lc_title2, $pres_lc_org2, $pres_lc_address2, $pres_lc_phone2, $pres_lc_email2, $pres_lc_fax2,
			$pres_lc_name3, $pres_lc_title3, $pres_lc_org3, $pres_lc_address3, $pres_lc_phone3, $pres_lc_email3, $pres_lc_fax3,
			$pres_lc_name4, $pres_lc_title4, $pres_lc_org4, $pres_lc_address4, $pres_lc_phone4, $pres_lc_email4, $pres_lc_fax4,
			$pres_title, $pres_abstract, $pres_description, $pres_logistics, $pres_lastupdated, $pres_submitted) = @arr;
			$counter++;
$pres_lastupdated = &commoncode::convert_timestamp_2pretty_w_date($pres_lastupdated, "yes");
$pres_submitted = "<span style=\"color:#CC0000\">no</span>" if ($pres_submitted eq '');
print<<EOM;
<tr>
	<td valign="top">$counter</td>
	<td valign="top">$pres_lc_name<br>
		$pres_lc_title<br>
		$pres_lc_org<br>
		$pres_lc_phone<br>
		$pres_lc_email</td>
	<td valign="top">$pres_title</td>
	<td valign="top">$pres_submitted</td>
	<td valign="top">$pres_lastupdated</td>
</tr>
EOM
		} # END DB QUERY LOOP
	## END: SELECT RESPONSES THAT MATCH USER E-MAIL ADDRESS

print<<EOM;
</table>
$htmltail
EOM
} 
#################################################################################
## END: LOCATION = menu
#################################################################################




#################################################################################
## START: LOCATION = datadump
#################################################################################
if ($location eq 'datadump') {
	open(DATA,">/home/httpd/html/staff/iqa/presentations_submitted.xls");


		## QUERY DATABASE FOR SURVEY CODE AND PRE-POPULATE SURVEY CONTEXTUAL VARIABLES
		my $command_check_survey_code = "SELECT * from iqa_presentations  order by pres_lastupdated DESC";
		my $dbh = DBI->connect($dsn2, "iqauser", "public");
		my $sth = $dbh->prepare($command_check_survey_code) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
			print DATA "record ID\tuser_email\tlc_name\tlc_title\tlc_org\tlc_address\tlc_phone\tlc_email\tlc_fax\totherpres_name1\totherpres_title1\totherpres_org1\totherpres_address1\totherpres_phone1\totherpres_email1\totherpres_fax1\totherpres_name2\totherpres_title2\totherpres_org2\totherpres_address2\totherpres_phone2\totherpres_email2\totherpres_fax2\totherpres_name3\totherpres_title3\totherpres_org3\totherpres_address3\totherpres_phone3\totherpres_email3\totherpres_fax3\totherpres_name4\totherpres_title4\totherpres_org4\totherpres_address4\totherpres_phone4\totherpres_email4\totherpres_fax4\ttitle\tabstract\tdescription\tlogistics\tlastupdated\tpres_submitted\n";

		while (my @arr = $sth->fetchrow) {
			# START: REMOVE ANY LINE BREAKS AND TABS FROM DATA
			my $counter = 0;
			while ($counter <= $#arr) {
				$arr[$counter] = &cleanformysql($arr[$counter]);
				$counter++;
			} # END WHILE LOOP
			# END: REMOVE ANY LINE BREAKS AND TABS FROM DATA

			my ($pres_record_id, $pres_user_email, 
			$pres_lc_name, $pres_lc_title, $pres_lc_org, $pres_lc_address, $pres_lc_phone, $pres_lc_email, $pres_lc_fax, 
			$pres_lc_name1, $pres_lc_title1, $pres_lc_org1, $pres_lc_address1, $pres_lc_phone1, $pres_lc_email1, $pres_lc_fax1, 
			$pres_lc_name2, $pres_lc_title2, $pres_lc_org2, $pres_lc_address2, $pres_lc_phone2, $pres_lc_email2, $pres_lc_fax2,
			$pres_lc_name3, $pres_lc_title3, $pres_lc_org3, $pres_lc_address3, $pres_lc_phone3, $pres_lc_email3, $pres_lc_fax3,
			$pres_lc_name4, $pres_lc_title4, $pres_lc_org4, $pres_lc_address4, $pres_lc_phone4, $pres_lc_email4, $pres_lc_fax4,
			$pres_title, $pres_abstract, $pres_description, $pres_logistics, $pres_lastupdated, $pres_submitted) = @arr;
			$pres_lastupdated = &commoncode::convert_timestamp_2pretty_w_date($pres_lastupdated, "yes");

			print DATA "$pres_record_id\t$pres_user_email\t$pres_lc_name\t$pres_lc_title\t$pres_lc_org\t$pres_lc_address\t$pres_lc_phone\t$pres_lc_email\t$pres_lc_fax\t$pres_lc_name1\t$pres_lc_title1\t$pres_lc_org1\t$pres_lc_address1\t$pres_lc_phone1\t$pres_lc_email1\t$pres_lc_fax1\t$pres_lc_name2\t$pres_lc_title2\t$pres_lc_org2\t$pres_lc_address2\t$pres_lc_phone2\t$pres_lc_email2\t$pres_lc_fax2\t$pres_lc_name3\t$pres_lc_title3\t$pres_lc_org3\t$pres_lc_address3\t$pres_lc_phone3\t$pres_lc_email3\t$pres_lc_fax3\t$pres_lc_name4\t$pres_lc_title4\t$pres_lc_org4\t$pres_lc_address4\t$pres_lc_phone4\t$pres_lc_email4\t$pres_lc_fax4\t$pres_title\t$pres_abstract\t$pres_description\t$pres_logistics\t$pres_lastupdated\t$pres_submitted\n";

		} # END DB QUERY LOOP
	## END: SELECT RESPONSES THAT MATCH USER E-MAIL ADDRESS
#print "Click here to download the <a href=\"/network/presentations_submitted.xls\">presentation submissions in XLS format</a>.";
print "Location: http://www.sedl.org/staff/iqa/presentations_submitted.xls\n\n";

close(DATA);
} 
#################################################################################
## END: LOCATION = datadump
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



#!/usr/bin/perl

#####################################################################################################
# Copyright 2008 by SEDL
#
# This script is used by Communications staff to set up customers for the Stages of Concerns Questionnaire
# Written by Brian Litke 04-24-2008
#####################################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=corp;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
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
my $item_label = "SoCQ Customer";
my $site_label = "SoCQ Customer Manager";
my $public_site_address = "http://www.sedl.org/concerns/admin/";

my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"
	$logon_user = &cleanformysql($logon_user);

my $logon_pass = $query->param("logon_pass");
	$logon_pass = &cleanformysql($logon_pass);

my $unique_id = $query->param("unique_id");

my $location = $query->param("location");
   $location = "menu" if $location eq '';

my $showsession = $query->param("showsession");


my $error_message = "";
my $feedback_message = "";
my $show_record = $query->param("show_record");
my $sortby = $query->param("sortby");
   $sortby = "date_added" if ($sortby eq '');
my $new_type = $query->param("new_type");

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("313"); # 313 is the PID for this page in the intranet database

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
	$dsn = "DBI:mysql:database=intranet;host=localhost";
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
				my $num_matches = $sth->rows;

					$validuser = "yes" if ($ss_staff_id eq 'awest');
					$validuser = "yes" if ($ss_staff_id eq 'blitke');
					$validuser = "yes" if ($ss_staff_id eq 'brollins');
					$validuser = "yes" if ($ss_staff_id eq 'cmoses');
					$validuser = "yes" if ($ss_staff_id eq 'dlewis');
					$validuser = "yes" if ($ss_staff_id eq 'etobia');
					$validuser = "yes" if ($ss_staff_id eq 'eurquidi');
					$validuser = "yes" if ($ss_staff_id eq 'jlaturne');
					$validuser = "yes" if ($ss_staff_id eq 'jwackwit');
					$validuser = "yes" if ($ss_staff_id eq 'lshankla');
					$validuser = "yes" if ($ss_staff_id eq 'nreynold');
					$validuser = "yes" if ($ss_staff_id eq 'sabdulla');
					$validuser = "yes" if ($ss_staff_id eq 'vdimock');
		
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
Welcome to the $site_label. This database is used by Communicaitons staff 
(Brian, Eva, Joni) to set up <a href="$public_site_address">Customers (Survey Administrators)</a> 
for the <a href="/concerns/">Stages of Concerns</a> Web site. 
Please enter your SEDL user ID and password to view the database.
</p>
<FORM ACTION="soc_customer_admin.cgi" METHOD="POST">
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
## START: LOCATION = PROCESS_ADD_record
#################################################################################
	my $new_soc_cust_user_email = $query->param("new_soc_cust_user_email");
       $new_soc_cust_user_email =~ s/ //gi;
	my $new_soc_cust_user_password = $query->param("new_soc_cust_user_password");
	my $new_soc_cust_name = $query->param("new_soc_cust_name");
	my $new_soc_cust_qty_purchased = $query->param("new_soc_cust_qty_purchased");
	my $new_soc_cust_notes = $query->param("new_soc_cust_notes");
	my $new_soc_cust_org = $query->param("new_soc_cust_org");
	my $new_soc_cust_state = $query->param("new_soc_cust_state");
	my $new_soc_cust_phone = $query->param("new_soc_cust_phone");
	my $new_soc_cust_version = $query->param("new_soc_cust_version");

	my $new_soc_cust_user2_name = $query->param("new_soc_cust_user2_name");
	my $new_soc_cust_user2_email = $query->param("new_soc_cust_user2_email");
       $new_soc_cust_user2_email =~ s/ //gi;
	my $new_soc_cust_user2_password = $query->param("new_soc_cust_user2_password");
	my $new_soc_cust_user3_name = $query->param("new_soc_cust_user3_name");
	my $new_soc_cust_user3_email = $query->param("new_soc_cust_user3_email");
       $new_soc_cust_user3_email =~ s/ //gi;
	my $new_soc_cust_user3_password = $query->param("new_soc_cust_user3_password");
	my $new_soc_cust_user4_name = $query->param("new_soc_cust_user4_name");
	my $new_soc_cust_user4_email = $query->param("new_soc_cust_user4_email");
       $new_soc_cust_user4_email =~ s/ //gi;
	my $new_soc_cust_user4_password = $query->param("new_soc_cust_user4_password");
	my $new_soc_cust_user5_name = $query->param("new_soc_cust_user5_name");
	my $new_soc_cust_user5_email = $query->param("new_soc_cust_user5_email");
       $new_soc_cust_user5_email =~ s/ //gi;
	my $new_soc_cust_user5_password = $query->param("new_soc_cust_user5_password");
	my $new_soc_cust_user6_name = $query->param("new_soc_cust_user6_name");
	my $new_soc_cust_user6_email = $query->param("new_soc_cust_user6_email");
	my $new_soc_cust_user6_password = $query->param("new_soc_cust_user6_password");
       $new_soc_cust_user6_email =~ s/ //gi;
	my $new_soc_cust_user7_name = $query->param("new_soc_cust_user7_name");
	my $new_soc_cust_user7_email = $query->param("new_soc_cust_user7_email");
       $new_soc_cust_user7_email =~ s/ //gi;
	my $new_soc_cust_user7_password = $query->param("new_soc_cust_user7_password");
	my $new_soc_cust_user8_name = $query->param("new_soc_cust_user8_name");
	my $new_soc_cust_user8_email = $query->param("new_soc_cust_user8_email");
       $new_soc_cust_user8_email =~ s/ //gi;
	my $new_soc_cust_user8_password = $query->param("new_soc_cust_user8_password");
	my $new_soc_cust_user9_name = $query->param("new_soc_cust_user9_name");
	my $new_soc_cust_user9_email = $query->param("new_soc_cust_user9_email");
       $new_soc_cust_user9_email =~ s/ //gi;
	my $new_soc_cust_user9_password = $query->param("new_soc_cust_user9_password");
	my $new_soc_cust_user10_name = $query->param("new_soc_cust_user10_name");
	my $new_soc_cust_user10_email = $query->param("new_soc_cust_user10_email");
       $new_soc_cust_user10_email =~ s/ //gi;
	my $new_soc_cust_user10_password = $query->param("new_soc_cust_user10_password");
	my $new_soc_cust_user11_name = $query->param("new_soc_cust_user11_name");
	my $new_soc_cust_user11_email = $query->param("new_soc_cust_user11_email");
       $new_soc_cust_user11_email =~ s/ //gi;
	my $new_soc_cust_user11_password = $query->param("new_soc_cust_user11_password");
	my $new_soc_cust_user12_name = $query->param("new_soc_cust_user12_name");
	my $new_soc_cust_user12_email = $query->param("new_soc_cust_user12_email");
       $new_soc_cust_user12_email =~ s/ //gi;
	my $new_soc_cust_user12_password = $query->param("new_soc_cust_user12_password");
	my $new_soc_cust_user13_name = $query->param("new_soc_cust_user13_name");
	my $new_soc_cust_user13_email = $query->param("new_soc_cust_user13_email");
       $new_soc_cust_user13_email =~ s/ //gi;
	my $new_soc_cust_user13_password = $query->param("new_soc_cust_user13_password");
	my $new_soc_cust_user14_name = $query->param("new_soc_cust_user14_name");
	my $new_soc_cust_user14_email = $query->param("new_soc_cust_user14_email");
       $new_soc_cust_user14_email =~ s/ //gi;
	my $new_soc_cust_user14_password = $query->param("new_soc_cust_user14_password");
	my $new_soc_cust_user15_name = $query->param("new_soc_cust_user15_name");
	my $new_soc_cust_user15_email = $query->param("new_soc_cust_user15_email");
       $new_soc_cust_user15_email =~ s/ //gi;
	my $new_soc_cust_user15_password = $query->param("new_soc_cust_user15_password");
	my $new_soc_cust_user16_name = $query->param("new_soc_cust_user16_name");
	my $new_soc_cust_user16_email = $query->param("new_soc_cust_user16_email");
       $new_soc_cust_user16_email =~ s/ //gi;
	my $new_soc_cust_user16_password = $query->param("new_soc_cust_user16_password");
	my $new_soc_cust_user17_name = $query->param("new_soc_cust_user17_name");
	my $new_soc_cust_user17_email = $query->param("new_soc_cust_user17_email");
       $new_soc_cust_user17_email =~ s/ //gi;
	my $new_soc_cust_user17_password = $query->param("new_soc_cust_user17_password");
	my $new_soc_cust_user18_name = $query->param("new_soc_cust_user18_name");
	my $new_soc_cust_user18_email = $query->param("new_soc_cust_user18_email");
       $new_soc_cust_user18_email =~ s/ //gi;
	my $new_soc_cust_user18_password = $query->param("new_soc_cust_user18_password");
	my $new_soc_cust_user19_name = $query->param("new_soc_cust_user19_name");
	my $new_soc_cust_user19_email = $query->param("new_soc_cust_user19_email");
       $new_soc_cust_user19_email =~ s/ //gi;
	my $new_soc_cust_user19_password = $query->param("new_soc_cust_user19_password");
	my $new_soc_cust_user20_name = $query->param("new_soc_cust_user20_name");
	my $new_soc_cust_user20_email = $query->param("new_soc_cust_user20_email");
       $new_soc_cust_user20_email =~ s/ //gi;
	my $new_soc_cust_user20_password = $query->param("new_soc_cust_user20_password");
	my $new_soc_cust_user21_name = $query->param("new_soc_cust_user21_name");
	my $new_soc_cust_user21_email = $query->param("new_soc_cust_user21_email");
       $new_soc_cust_user21_email =~ s/ //gi;
	my $new_soc_cust_user21_password = $query->param("new_soc_cust_user21_password");
	my $new_soc_cust_user22_name = $query->param("new_soc_cust_user22_name");
	my $new_soc_cust_user22_email = $query->param("new_soc_cust_user22_email");
       $new_soc_cust_user22_email =~ s/ //gi;
	my $new_soc_cust_user22_password = $query->param("new_soc_cust_user22_password");

	my $new_soc_cust_permission_alt_wording = $query->param("new_soc_cust_permission_alt_wording");
	my $new_soc_cust_replacement_alt_wording = $query->param("new_soc_cust_replacement_alt_wording");
	   $new_soc_cust_permission_alt_wording = "no" if ($new_soc_cust_replacement_alt_wording eq '');
	my $new_soc_cust_free_surveys = $query->param("new_soc_cust_free_surveys");
	my $new_soc_cust_copyright_date = $query->param("new_soc_cust_copyright_date");
	my $new_soc_cust_copyright_enteredby = $query->param("new_soc_cust_copyright_enteredby");
	my $new_soc_cust_copyright_notes = $query->param("new_soc_cust_copyright_notes");

if ($location eq 'process_add_record') {
	## START: CHECK FOR DATA COPLETENESS
	if ($new_soc_cust_name eq '') {
		$error_message .= "<br>" if ($error_message ne '');
		$error_message .= "The $item_label NAME is missing. Please try again.";
		$location = "add_record";
	}
	if ($new_soc_cust_user_email eq '') {
		$error_message .= "<br>" if ($error_message ne '');
		$error_message .= "The $item_label E-MAIL is missing. Please try again.";
		$location = "add_record";
	}
	if ($new_soc_cust_user_password eq '') {
		$error_message .= "<br>" if ($error_message ne '');
		$error_message .= "The $item_label PASSWORD is missing. Please try again.";
		$location = "add_record";
	}
	if ($new_soc_cust_qty_purchased eq '') {
		$error_message .= "<br>" if ($error_message ne '');
		$error_message .= "The $item_label QUANTITY PURCHASED is missing. Please try again.";
		$location = "add_record";
	}
	## END: CHECK FOR DATA COPLETENESS
}

if ($location eq 'process_add_record') {

	## START: BACKSLASH VARIABLES FOR DB
	$new_soc_cust_name = &cleanformysql($new_soc_cust_name);
	$new_soc_cust_user_email = &cleanformysql($new_soc_cust_user_email);
	$new_soc_cust_user_password = &cleanformysql($new_soc_cust_user_password);
	$new_soc_cust_qty_purchased = &cleanformysql($new_soc_cust_qty_purchased);
	$new_soc_cust_notes = &cleanformysql($new_soc_cust_notes);
	$new_soc_cust_org = &cleanformysql($new_soc_cust_org);
	$new_soc_cust_state = &cleanformysql($new_soc_cust_state);
	$new_soc_cust_phone = &cleanformysql($new_soc_cust_phone);
	$new_soc_cust_version = &cleanformysql($new_soc_cust_version);

	$new_soc_cust_user2_name = &cleanformysql($new_soc_cust_user2_name);
	$new_soc_cust_user2_email = &cleanformysql($new_soc_cust_user2_email);
	$new_soc_cust_user2_password = &cleanformysql($new_soc_cust_user2_password);
	$new_soc_cust_user3_name = &cleanformysql($new_soc_cust_user3_name);
	$new_soc_cust_user3_email = &cleanformysql($new_soc_cust_user3_email);
	$new_soc_cust_user3_password = &cleanformysql($new_soc_cust_user3_password);
	$new_soc_cust_user4_name = &cleanformysql($new_soc_cust_user4_name);
	$new_soc_cust_user4_email = &cleanformysql($new_soc_cust_user4_email);
	$new_soc_cust_user4_password = &cleanformysql($new_soc_cust_user4_password);
	$new_soc_cust_user5_name = &cleanformysql($new_soc_cust_user5_name);
	$new_soc_cust_user5_email = &cleanformysql($new_soc_cust_user5_email);
	$new_soc_cust_user5_password = &cleanformysql($new_soc_cust_user5_password);
	$new_soc_cust_user6_name = &cleanformysql($new_soc_cust_user6_name);
	$new_soc_cust_user6_email = &cleanformysql($new_soc_cust_user6_email);
	$new_soc_cust_user6_password = &cleanformysql($new_soc_cust_user6_password);
	$new_soc_cust_user7_name = &cleanformysql($new_soc_cust_user7_name);
	$new_soc_cust_user7_email = &cleanformysql($new_soc_cust_user7_email);
	$new_soc_cust_user7_password = &cleanformysql($new_soc_cust_user7_password);
	$new_soc_cust_user8_name = &cleanformysql($new_soc_cust_user8_name);
	$new_soc_cust_user8_email = &cleanformysql($new_soc_cust_user8_email);
	$new_soc_cust_user8_password = &cleanformysql($new_soc_cust_user8_password);
	$new_soc_cust_user9_name = &cleanformysql($new_soc_cust_user9_name);
	$new_soc_cust_user9_email = &cleanformysql($new_soc_cust_user9_email);
	$new_soc_cust_user9_password = &cleanformysql($new_soc_cust_user9_password);
	$new_soc_cust_user10_name = &cleanformysql($new_soc_cust_user10_name);
	$new_soc_cust_user10_email = &cleanformysql($new_soc_cust_user10_email);
	$new_soc_cust_user10_password = &cleanformysql($new_soc_cust_user10_password);
	$new_soc_cust_user11_name = &cleanformysql($new_soc_cust_user11_name);
	$new_soc_cust_user11_email = &cleanformysql($new_soc_cust_user11_email);
	$new_soc_cust_user11_password = &cleanformysql($new_soc_cust_user11_password);
	$new_soc_cust_user12_name = &cleanformysql($new_soc_cust_user12_name);
	$new_soc_cust_user12_email = &cleanformysql($new_soc_cust_user12_email);
	$new_soc_cust_user12_password = &cleanformysql($new_soc_cust_user12_password);
	$new_soc_cust_user13_name = &cleanformysql($new_soc_cust_user13_name);
	$new_soc_cust_user13_email = &cleanformysql($new_soc_cust_user13_email);
	$new_soc_cust_user13_password = &cleanformysql($new_soc_cust_user13_password);
	$new_soc_cust_user14_name = &cleanformysql($new_soc_cust_user14_name);
	$new_soc_cust_user14_email = &cleanformysql($new_soc_cust_user14_email);
	$new_soc_cust_user14_password = &cleanformysql($new_soc_cust_user14_password);
	$new_soc_cust_user15_name = &cleanformysql($new_soc_cust_user15_name);
	$new_soc_cust_user15_email = &cleanformysql($new_soc_cust_user15_email);
	$new_soc_cust_user15_password = &cleanformysql($new_soc_cust_user15_password);
	$new_soc_cust_user16_name = &cleanformysql($new_soc_cust_user16_name);
	$new_soc_cust_user16_email = &cleanformysql($new_soc_cust_user16_email);
	$new_soc_cust_user16_password = &cleanformysql($new_soc_cust_user16_password);
	$new_soc_cust_user17_name = &cleanformysql($new_soc_cust_user17_name);
	$new_soc_cust_user17_email = &cleanformysql($new_soc_cust_user17_email);
	$new_soc_cust_user17_password = &cleanformysql($new_soc_cust_user17_password);
	$new_soc_cust_user18_name = &cleanformysql($new_soc_cust_user18_name);
	$new_soc_cust_user18_email = &cleanformysql($new_soc_cust_user18_email);
	$new_soc_cust_user18_password = &cleanformysql($new_soc_cust_user18_password);
	$new_soc_cust_user19_name = &cleanformysql($new_soc_cust_user19_name);
	$new_soc_cust_user19_email = &cleanformysql($new_soc_cust_user19_email);
	$new_soc_cust_user19_password = &cleanformysql($new_soc_cust_user19_password);
	$new_soc_cust_user20_name = &cleanformysql($new_soc_cust_user20_name);
	$new_soc_cust_user20_email = &cleanformysql($new_soc_cust_user20_email);
	$new_soc_cust_user20_password = &cleanformysql($new_soc_cust_user20_password);
	$new_soc_cust_user21_name = &cleanformysql($new_soc_cust_user21_name);
	$new_soc_cust_user21_email = &cleanformysql($new_soc_cust_user21_email);
	$new_soc_cust_user21_password = &cleanformysql($new_soc_cust_user21_password);
	$new_soc_cust_user22_name = &cleanformysql($new_soc_cust_user22_name);
	$new_soc_cust_user22_email = &cleanformysql($new_soc_cust_user22_email);
	$new_soc_cust_user22_password = &cleanformysql($new_soc_cust_user22_password);

	$new_soc_cust_permission_alt_wording = &cleanformysql($new_soc_cust_permission_alt_wording);
	$new_soc_cust_replacement_alt_wording = &cleanformysql($new_soc_cust_replacement_alt_wording);
	$new_soc_cust_free_surveys = &cleanformysql($new_soc_cust_free_surveys);
	$new_soc_cust_copyright_date = &cleanformysql($new_soc_cust_copyright_date);
	$new_soc_cust_copyright_enteredby = &cleanformysql($new_soc_cust_copyright_enteredby);
	$new_soc_cust_copyright_notes = &cleanformysql($new_soc_cust_copyright_notes);
	## END: BACKSLASH VARIABLES FOR DB

	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select soc_cust_unique_id from soc_customers WHERE soc_cust_unique_id = '$show_record'";
#		print header;
#		print "<p>CHECK FOR EXISTS:<br>$command<br><p>";

		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;

		$already_exists = "yes" if ($num_matches_code eq '1');
#		print header;
#		print "<p>CHECK FOR EXISTS:<br>$command<br>MATCHES: $num_matches_code<p>";

#		while (my @arr = $sth->fetchrow) {
#			my ($this_recordid, $this_access_code) = @arr;
#			$show_record = $this_recordid if ($show_record ne '');
#		} # END DB QUERY LOOP
my $add_edit_type = "added";
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update_item = "UPDATE soc_customers 
										SET soc_cust_user_email='$new_soc_cust_user_email', soc_cust_user_password='$new_soc_cust_user_password', soc_cust_name='$new_soc_cust_name', soc_cust_qty_purchased='$new_soc_cust_qty_purchased', soc_cust_notes='$new_soc_cust_notes', soc_cust_org='$new_soc_cust_org', soc_cust_state='$new_soc_cust_state', soc_cust_phone='$new_soc_cust_phone'
										, soc_cust_version='$new_soc_cust_version'
										, soc_cust_user2_name='$new_soc_cust_user2_name', soc_cust_user2_email='$new_soc_cust_user2_email', soc_cust_user2_password='$new_soc_cust_user2_password'
										, soc_cust_user3_name='$new_soc_cust_user3_name', soc_cust_user3_email='$new_soc_cust_user3_email', soc_cust_user3_password='$new_soc_cust_user3_password'
										, soc_cust_user4_name='$new_soc_cust_user4_name', soc_cust_user4_email='$new_soc_cust_user4_email', soc_cust_user4_password='$new_soc_cust_user4_password'
										, soc_cust_user5_name='$new_soc_cust_user5_name', soc_cust_user5_email='$new_soc_cust_user5_email', soc_cust_user5_password='$new_soc_cust_user5_password'
										, soc_cust_user6_name='$new_soc_cust_user6_name', soc_cust_user6_email='$new_soc_cust_user6_email', soc_cust_user6_password='$new_soc_cust_user6_password'
										, soc_cust_user7_name='$new_soc_cust_user7_name', soc_cust_user7_email='$new_soc_cust_user7_email', soc_cust_user7_password='$new_soc_cust_user7_password'
										, soc_cust_user8_name='$new_soc_cust_user8_name', soc_cust_user8_email='$new_soc_cust_user8_email', soc_cust_user8_password='$new_soc_cust_user8_password'
										, soc_cust_user9_name='$new_soc_cust_user9_name', soc_cust_user9_email='$new_soc_cust_user9_email', soc_cust_user9_password='$new_soc_cust_user9_password'
										, soc_cust_user10_name='$new_soc_cust_user10_name', soc_cust_user10_email='$new_soc_cust_user10_email', soc_cust_user10_password='$new_soc_cust_user10_password'
										, soc_cust_user11_name='$new_soc_cust_user11_name', soc_cust_user11_email='$new_soc_cust_user11_email', soc_cust_user11_password='$new_soc_cust_user11_password'
										, soc_cust_user12_name='$new_soc_cust_user12_name', soc_cust_user12_email='$new_soc_cust_user12_email', soc_cust_user12_password='$new_soc_cust_user12_password'
										, soc_cust_user13_name='$new_soc_cust_user13_name', soc_cust_user13_email='$new_soc_cust_user13_email', soc_cust_user13_password='$new_soc_cust_user13_password'
										, soc_cust_user14_name='$new_soc_cust_user14_name', soc_cust_user14_email='$new_soc_cust_user14_email', soc_cust_user14_password='$new_soc_cust_user14_password'
										, soc_cust_user15_name='$new_soc_cust_user15_name', soc_cust_user15_email='$new_soc_cust_user15_email', soc_cust_user15_password='$new_soc_cust_user15_password'
										, soc_cust_user16_name='$new_soc_cust_user16_name', soc_cust_user16_email='$new_soc_cust_user16_email', soc_cust_user16_password='$new_soc_cust_user16_password'
										, soc_cust_user17_name='$new_soc_cust_user17_name', soc_cust_user17_email='$new_soc_cust_user17_email', soc_cust_user17_password='$new_soc_cust_user17_password'
										, soc_cust_user18_name='$new_soc_cust_user18_name', soc_cust_user18_email='$new_soc_cust_user18_email', soc_cust_user18_password='$new_soc_cust_user18_password'
										, soc_cust_user19_name='$new_soc_cust_user19_name', soc_cust_user19_email='$new_soc_cust_user19_email', soc_cust_user19_password='$new_soc_cust_user19_password'
										, soc_cust_user20_name='$new_soc_cust_user20_name', soc_cust_user20_email='$new_soc_cust_user20_email', soc_cust_user20_password='$new_soc_cust_user20_password'
										, soc_cust_user21_name='$new_soc_cust_user21_name', soc_cust_user21_email='$new_soc_cust_user21_email', soc_cust_user21_password='$new_soc_cust_user21_password'
										, soc_cust_user22_name='$new_soc_cust_user22_name', soc_cust_user22_email='$new_soc_cust_user22_email', soc_cust_user22_password='$new_soc_cust_user22_password'

										, soc_cust_permission_alt_wording='$new_soc_cust_permission_alt_wording'
										, soc_cust_replacement_alt_wording='$new_soc_cust_replacement_alt_wording'
										, soc_cust_free_surveys='$new_soc_cust_free_surveys'
										, soc_cust_copyright_date='$new_soc_cust_copyright_date'
										, soc_cust_copyright_enteredby='$new_soc_cust_copyright_enteredby'
										, soc_cust_copyright_notes='$new_soc_cust_copyright_notes'
										WHERE soc_cust_unique_id ='$show_record'";
			$dsn = "DBI:mysql:database=corp;host=localhost";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_update_item) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			$add_edit_type = "edited";
			$feedback_message .= "The $item_label was $add_edit_type successfully and is highlighted in <a href=\"#$show_record\">YELLOW below</a>.";
			$location = "menu";
		} else {
	
			my $command_insert_item = "INSERT INTO soc_customers VALUES ('', '$new_soc_cust_user_email', '$new_soc_cust_user_password', '$timestamp', '$new_soc_cust_name', '$new_soc_cust_qty_purchased', '$new_soc_cust_notes', '', '$new_soc_cust_org', '$new_soc_cust_state', '$new_soc_cust_phone', '$new_soc_cust_version', '$new_soc_cust_user2_name', '$new_soc_cust_user2_email', '$new_soc_cust_user2_password', '$new_soc_cust_user3_name', '$new_soc_cust_user3_email', '$new_soc_cust_user3_password', '$new_soc_cust_user4_name', '$new_soc_cust_user4_email', '$new_soc_cust_user4_password', '$new_soc_cust_user5_name', '$new_soc_cust_user5_email', '$new_soc_cust_user5_password', '$new_soc_cust_user6_name', '$new_soc_cust_user6_email', '$new_soc_cust_user6_password', '$new_soc_cust_user7_name', '$new_soc_cust_user7_email', '$new_soc_cust_user7_password', '$new_soc_cust_user8_name', '$new_soc_cust_user8_email', '$new_soc_cust_user8_password', '$new_soc_cust_user9_name', '$new_soc_cust_user9_email', '$new_soc_cust_user9_password', '$new_soc_cust_user10_name', '$new_soc_cust_user10_email', '$new_soc_cust_user10_password', 
			'$new_soc_cust_user11_name', '$new_soc_cust_user11_email', '$new_soc_cust_user11_password', 
			'$new_soc_cust_user12_name', '$new_soc_cust_user12_email', '$new_soc_cust_user12_password', 
			'$new_soc_cust_user13_name', '$new_soc_cust_user13_email', '$new_soc_cust_user13_password', 
			'$new_soc_cust_user14_name', '$new_soc_cust_user14_email', '$new_soc_cust_user14_password', 
			'$new_soc_cust_user15_name', '$new_soc_cust_user15_email', '$new_soc_cust_user15_password', 
			'$new_soc_cust_user16_name', '$new_soc_cust_user16_email', '$new_soc_cust_user16_password', 
			'$new_soc_cust_user17_name', '$new_soc_cust_user17_email', '$new_soc_cust_user17_password', 
			'$new_soc_cust_user18_name', '$new_soc_cust_user18_email', '$new_soc_cust_user18_password', 
			'$new_soc_cust_user19_name', '$new_soc_cust_user19_email', '$new_soc_cust_user19_password', 
			'$new_soc_cust_user20_name', '$new_soc_cust_user20_email', '$new_soc_cust_user20_password', 
			'$new_soc_cust_user21_name', '$new_soc_cust_user21_email', '$new_soc_cust_user21_password', 
			'$new_soc_cust_user22_name', '$new_soc_cust_user22_email', '$new_soc_cust_user22_password', 
			'$new_soc_cust_permission_alt_wording', '$new_soc_cust_replacement_alt_wording', '$new_soc_cust_free_surveys', '$new_soc_cust_copyright_date', '$new_soc_cust_copyright_enteredby', '$new_soc_cust_copyright_notes', '', '')";

			$dsn = "DBI:mysql:database=corp;host=localhost";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_insert_item) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;


			$feedback_message .= "The $item_label was $add_edit_type successfully.";
			$location = "menu";
		} # END IF USER NAME NOT BLANK

}
#################################################################################
## END: LOCATION = PROCESS_ADD_record
#################################################################################


#################################################################################
## START: LOCATION = ADD_record
#################################################################################
if ($location eq 'add_record') {
	my $page_title = "Add a New $item_label";

	my $soc_cust_unique_id = "";
	my $soc_cust_user_email = "";
	my $soc_cust_user_password = "";
	my $soc_cust_date_added = "";
	my $soc_cust_name = "";
	my $soc_cust_qty_purchased = "";
	my $soc_cust_notes = "";
	my $soc_cust_org = "";
	my $soc_cust_state = "";
	my $soc_cust_phone = "";
	my $date_last_logon = "";
	my $soc_cust_version = "";	

	my $soc_cust_user2_name = "";
	my $soc_cust_user2_email = "";
	my $soc_cust_user2_password = "";
	my $soc_cust_user3_name = "";
	my $soc_cust_user3_email = "";
	my $soc_cust_user3_password = "";
	my $soc_cust_user4_name = "";
	my $soc_cust_user4_email = "";
	my $soc_cust_user4_password = "";
	my $soc_cust_user5_name = "";
	my $soc_cust_user5_email = "";
	my $soc_cust_user5_password = "";
	my $soc_cust_user6_name = "";
	my $soc_cust_user6_email = "";
	my $soc_cust_user6_password = "";
	my $soc_cust_user7_name = "";
	my $soc_cust_user7_email = "";
	my $soc_cust_user7_password = "";
	my $soc_cust_user8_name = "";
	my $soc_cust_user8_email = "";
	my $soc_cust_user8_password = "";
	my $soc_cust_user9_name = "";
	my $soc_cust_user9_email = "";
	my $soc_cust_user9_password = "";
	my $soc_cust_user10_name = "";
	my $soc_cust_user10_email = "";
	my $soc_cust_user10_password = "";
	my $soc_cust_user11_name = "";
	my $soc_cust_user11_email = "";
	my $soc_cust_user11_password = "";

	my $soc_cust_user12_name = "";
	my $soc_cust_user12_email = "";
	my $soc_cust_user12_password = "";
	my $soc_cust_user13_name = "";
	my $soc_cust_user13_email = "";
	my $soc_cust_user13_password = "";
	my $soc_cust_user14_name = "";
	my $soc_cust_user14_email = "";
	my $soc_cust_user14_password = "";
	my $soc_cust_user15_name = "";
	my $soc_cust_user15_email = "";
	my $soc_cust_user15_password = "";
	my $soc_cust_user16_name = "";
	my $soc_cust_user16_email = "";
	my $soc_cust_user16_password = "";
	my $soc_cust_user17_name = "";
	my $soc_cust_user17_email = "";
	my $soc_cust_user17_password = "";
	my $soc_cust_user18_name = "";
	my $soc_cust_user18_email = "";
	my $soc_cust_user18_password = "";
	my $soc_cust_user19_name = "";
	my $soc_cust_user19_email = "";
	my $soc_cust_user19_password = "";
	my $soc_cust_user20_name = "";
	my $soc_cust_user20_email = "";
	my $soc_cust_user20_password = "";
	my $soc_cust_user21_name = "";
	my $soc_cust_user21_email = "";
	my $soc_cust_user21_password = "";
	my $soc_cust_user22_name = "";
	my $soc_cust_user22_email = "";
	my $soc_cust_user22_password = "";

	my $soc_cust_permission_alt_wording = "";
	my $soc_cust_replacement_alt_wording = "";
	my $soc_cust_free_surveys = "";
	my $soc_cust_copyright_date = "";
	my $soc_cust_copyright_enteredby = "";
	my $soc_cust_copyright_notes = "";
	my $soc_cust_logon_inst_sent = "";
	my $soc_cust_suppress_highlighting = "";
	if ($show_record ne '') {
		$page_title = "Save Edits to this $item_label";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from soc_customers WHERE soc_cust_unique_id = '$show_record'";

		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		my $num_matches_pubs = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			($soc_cust_unique_id, $soc_cust_user_email, $soc_cust_user_password, $soc_cust_date_added, $soc_cust_name, $soc_cust_qty_purchased, $soc_cust_notes, $date_last_logon, 
			$soc_cust_org, $soc_cust_state, $soc_cust_phone, $soc_cust_version,
			$soc_cust_user2_name, $soc_cust_user2_email, $soc_cust_user2_password, $soc_cust_user3_name, $soc_cust_user3_email, $soc_cust_user3_password, $soc_cust_user4_name, $soc_cust_user4_email, $soc_cust_user4_password, $soc_cust_user5_name, $soc_cust_user5_email, $soc_cust_user5_password, 
			$soc_cust_user6_name, $soc_cust_user6_email, $soc_cust_user6_password, $soc_cust_user7_name, $soc_cust_user7_email, $soc_cust_user7_password, $soc_cust_user8_name, $soc_cust_user8_email, $soc_cust_user8_password, $soc_cust_user9_name, $soc_cust_user9_email, $soc_cust_user9_password, $soc_cust_user10_name, $soc_cust_user10_email, $soc_cust_user10_password, 
			$soc_cust_user11_name, $soc_cust_user11_email, $soc_cust_user11_password, 
			$soc_cust_user12_name, $soc_cust_user12_email, $soc_cust_user12_password, 
			$soc_cust_user13_name, $soc_cust_user13_email, $soc_cust_user13_password, 
			$soc_cust_user14_name, $soc_cust_user14_email, $soc_cust_user14_password, 
			$soc_cust_user15_name, $soc_cust_user15_email, $soc_cust_user15_password, 
			$soc_cust_user16_name, $soc_cust_user16_email, $soc_cust_user16_password, 
			$soc_cust_user17_name, $soc_cust_user17_email, $soc_cust_user17_password, 
			$soc_cust_user18_name, $soc_cust_user18_email, $soc_cust_user18_password, 
			$soc_cust_user19_name, $soc_cust_user19_email, $soc_cust_user19_password, 
			$soc_cust_user20_name, $soc_cust_user20_email, $soc_cust_user20_password, 
			$soc_cust_user21_name, $soc_cust_user21_email, $soc_cust_user21_password, 
			$soc_cust_user22_name, $soc_cust_user22_email, $soc_cust_user22_password, 
			$soc_cust_permission_alt_wording, $soc_cust_replacement_alt_wording, 
			$soc_cust_free_surveys, $soc_cust_copyright_date, $soc_cust_copyright_enteredby, $soc_cust_copyright_notes, 
			$soc_cust_logon_inst_sent, $soc_cust_suppress_highlighting) = @arr;
		} # END DB QUERY LOOP
	}
		## OVER-RIDE, IF JUST SUBMITTED
		if ($show_record eq '') {
			$soc_cust_user_email = $new_soc_cust_user_email;
			$soc_cust_user_password = $new_soc_cust_user_password;
			$soc_cust_name = $new_soc_cust_name;
			$soc_cust_notes = $new_soc_cust_notes;
			$soc_cust_phone = $new_soc_cust_phone;
			$soc_cust_state = $new_soc_cust_state;
			$soc_cust_org = $new_soc_cust_org;
			$soc_cust_version = $new_soc_cust_version;
			$soc_cust_qty_purchased = $new_soc_cust_qty_purchased;
		} # END IF

		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$soc_cust_name = &cleanaccents2html($soc_cust_name);

print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label: $page_title</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">

<script language="javascript" type="text/javascript" src="/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">     
tinyMCE.init({
	mode : "exact",
	elements : "box1,box2",
	theme : "advanced",
	plugins : "spellchecker,table,paste",
	gecko_spellcheck : true,
	theme_advanced_buttons1_add : "pastetext,pasteword",
	theme_advanced_buttons3_add : "tablecontrols, spellchecker",
	table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
	table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
	table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
	table_cell_limit : 100,
	table_row_limit : 20,
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


<script type="text/javascript">
<!--
      function input(val){
         form2.new_news_item_footer.value = "\\"" + val + "\\"";
         return false;
      }
//-->
</script>
      
$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td>

<h1><A HREF="soc_customer_admin.cgi">$site_label</A><br>
$page_title</h1>


<p>The text edit boxes work best in the Firefox browser.</p>
EOM
	if ($page_title =~ 'Edit') {
print<<EOM;
<div class="first">
<strong>If you are ready to send logon instructions to user:</strong>
<FORM ACTION="soc_customer_admin.cgi" METHOD="POST">
<ul>
	<input type="checkbox" name="confirm" id="confirm" value="confirmed"> <label for="confirm">Click here to confirm sending the e-mail.</label><br>
	<input type="text" name="this_email_cc" id="this_email_cc" value="" size="30"> <label for="this_email_cc">CC this e-mail address on the message (optional)</label>
	<input type="hidden" name="this_soc_cust_logon_inst_sent" value="$soc_cust_logon_inst_sent">
	<input type="hidden" name="this_user_name" value="$soc_cust_name">
	<input type="hidden" name="this_user_email" value="$soc_cust_user_email">
	<input type="hidden" name="this_user_number_purchased" value="$soc_cust_qty_purchased">
	<input type="hidden" name="this_user_number_free" value="$soc_cust_free_surveys">
	<input type="hidden" name="this_user_password" value="$soc_cust_user_password">
	<input type="hidden" name="location" value="send_directions_to_user"><br>
	<INPUT TYPE="SUBMIT" VALUE="Send logon instructions by e-mail to user">
</ul>
</form>
</div>
<br>
<br>
EOM
	}
print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

my $selected_version_2008 = "";
   $selected_version_2008 = "SELECTED" if ($soc_cust_version eq "2008"); # 2008 represents the old version we are moving clients away from
print<<EOM;      
<FORM ACTION="soc_customer_admin.cgi" METHOD="POST" name="form2" id="form2">

<TABLE border="1" cellpadding="2" cellspacing="0" width="100%">
<tr><td valign="top"><strong><label for="new_soc_cust_name">Customer Name</label></strong></td>
	<td valign="top"><textarea id="box1" name="new_soc_cust_name" id="new_soc_cust_name" rows="8" cols="70">$soc_cust_name</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user_email">Customer E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user_email" id="new_soc_cust_user_email" SIZE="40" VALUE="$soc_cust_user_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_phone">Customer Phone</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_phone" id="new_soc_cust_phone" SIZE="40" VALUE="$soc_cust_phone"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user_password">Customer Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user_password" id="new_soc_cust_user_password" SIZE="20" VALUE="$soc_cust_user_password"><br>
					(Required to access the <a href="/concerns/admin/" target="_blank">Survey Administration</a> site.)</td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_org">Organization</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_org" id="new_soc_cust_org" SIZE="40" VALUE="$soc_cust_org"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_state">State</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_state" id="new_soc_cust_state" SIZE="20" VALUE="$soc_cust_state"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_version">Site Version</label></strong></td>
	<td valign="top">
		<select name="new_soc_cust_version" id="new_soc_cust_version">
		<option value="2010">Most recent version (cohort averaging uses correct averaging calculation)</option>
		<option value="2008" $selected_version_2008>Original - imprecise math on cohort averages</option>
		</select>
	</td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_qty_purchased">Total Surveys Available<br>(Qty. Purchased + Free)</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_qty_purchased" id="new_soc_cust_qty_purchased" SIZE="10" VALUE="$soc_cust_qty_purchased"><br>
	</td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_free_surveys">Free surveys included in previous number</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_free_surveys" id="new_soc_cust_free_surveys" SIZE="4" VALUE="$soc_cust_free_surveys"><br>
					(I usually add 10 free surveys for each customer, so you'll most often see "10" in this box.)</td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_notes">Notes (Communications Use/Viewing Only)</label></strong></td>
	<td valign="top"><textarea id="box2" name="new_soc_cust_notes" id="new_soc_cust_notes" rows="16" cols="70">$soc_cust_notes</textarea>
	</td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_notes">Copyright Notes (Communications Use/Viewing Only)</label></strong><br><br><div style="text-align:center;"><img src="http://www.sedl.org/images/copyright.gif" alt="copyright"></div></td>
	<td valign="top">Copyright verified by: <INPUT type="text" NAME="new_soc_cust_copyright_enteredby" id="new_soc_cust_copyright_enteredby" SIZE="10" VALUE="$soc_cust_copyright_enteredby"><br>
					 Copyright received date: <INPUT type="text" NAME="new_soc_cust_copyright_date" id="new_soc_cust_copyright_date" SIZE="10" VALUE="$soc_cust_copyright_date">
	<textarea id="box3" name="new_soc_cust_copyright_notes" id="new_soc_cust_copyright_notes" rows="16" cols="70">$soc_cust_copyright_notes</textarea>
	</td></tr>
<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>
<tr><td colspan="2"><h2 style="margin-bottom:0px;">ADDITIONAL TEAM MEMBERS</h2></td>
<tr><td valign="top"><strong><label for="new_soc_cust_user2_name">User 2 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user2_name" id="new_soc_cust_user2_name" SIZE="40" VALUE="$soc_cust_user2_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user2_email">User 2 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user2_email" id="new_soc_cust_user2_email" SIZE="40" VALUE="$soc_cust_user2_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user2_password">User 2 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user2_password" id="new_soc_cust_user2_password" SIZE="20" VALUE="$soc_cust_user2_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user3_name">User 3 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user3_name" id="new_soc_cust_user3_name" SIZE="40" VALUE="$soc_cust_user3_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user3_email">User 3 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user3_email" id="new_soc_cust_user3_email" SIZE="40" VALUE="$soc_cust_user3_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user3_password">User 3 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user3_password" id="new_soc_cust_user3_password" SIZE="20" VALUE="$soc_cust_user3_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user4_name">User 4 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user4_name" id="new_soc_cust_user4_name" SIZE="40" VALUE="$soc_cust_user4_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user4_email">User 4 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user4_email" id="new_soc_cust_user4_email" SIZE="40" VALUE="$soc_cust_user4_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user4_password">User 4 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user4_password" id="new_soc_cust_user4_password" SIZE="20" VALUE="$soc_cust_user4_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user5_name">User 5 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user5_name" id="new_soc_cust_user5_name" SIZE="40" VALUE="$soc_cust_user5_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user5_email">User 5 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user5_email" id="new_soc_cust_user5_email" SIZE="40" VALUE="$soc_cust_user5_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user5_password">User 5 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user5_password" id="new_soc_cust_user5_password" SIZE="20" VALUE="$soc_cust_user5_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user6_name">User 6 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user6_name" id="new_soc_cust_user6_name" SIZE="40" VALUE="$soc_cust_user6_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user6_email">User 6 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user6_email" id="new_soc_cust_user6_email" SIZE="40" VALUE="$soc_cust_user6_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user6_password">User 6 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user6_password" id="new_soc_cust_user6_password" SIZE="20" VALUE="$soc_cust_user6_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user7_name">User 7 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user7_name" id="new_soc_cust_user7_name" SIZE="40" VALUE="$soc_cust_user7_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user7_email">User 7 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user7_email" id="new_soc_cust_user7_email" SIZE="40" VALUE="$soc_cust_user7_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user7_password">User 7 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user7_password" id="new_soc_cust_user7_password" SIZE="20" VALUE="$soc_cust_user7_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user8_name">User 8 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user8_name" id="new_soc_cust_user8_name" SIZE="40" VALUE="$soc_cust_user8_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user8_email">User 8 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user8_email" id="new_soc_cust_user8_email" SIZE="40" VALUE="$soc_cust_user8_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user8_password">User 8 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user8_password" id="new_soc_cust_user8_password" SIZE="20" VALUE="$soc_cust_user8_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user9_name">User 9 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user9_name" id="new_soc_cust_user9_name" SIZE="40" VALUE="$soc_cust_user9_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user9_email">User 9 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user9_email" id="new_soc_cust_user9_email" SIZE="40" VALUE="$soc_cust_user9_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user9_password">User 9 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user9_password" id="new_soc_cust_user9_password" SIZE="20" VALUE="$soc_cust_user9_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user10_name">User 10 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user10_name" id="new_soc_cust_user10_name" SIZE="40" VALUE="$soc_cust_user10_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user10_email">User 10 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user10_email" id="new_soc_cust_user10_email" SIZE="40" VALUE="$soc_cust_user10_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user10_password">User 10 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user10_password" id="new_soc_cust_user10_password" SIZE="20" VALUE="$soc_cust_user10_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user11_name">User 11 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user11_name" id="new_soc_cust_user11_name" SIZE="40" VALUE="$soc_cust_user11_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user11_email">User 11 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user11_email" id="new_soc_cust_user11_email" SIZE="40" VALUE="$soc_cust_user11_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user11_password">User 11 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user11_password" id="new_soc_cust_user11_password" SIZE="20" VALUE="$soc_cust_user11_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user12_name">User 12 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user12_name" id="new_soc_cust_user12_name" SIZE="40" VALUE="$soc_cust_user12_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user12_email">User 12 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user12_email" id="new_soc_cust_user12_email" SIZE="40" VALUE="$soc_cust_user12_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user12_password">User 12 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user12_password" id="new_soc_cust_user12_password" SIZE="20" VALUE="$soc_cust_user12_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user13_name">User 13 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user13_name" id="new_soc_cust_user13_name" SIZE="40" VALUE="$soc_cust_user13_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user13_email">User 13 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user13_email" id="new_soc_cust_user13_email" SIZE="40" VALUE="$soc_cust_user13_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user13_password">User 13 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user13_password" id="new_soc_cust_user13_password" SIZE="20" VALUE="$soc_cust_user13_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user14_name">User 14 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user14_name" id="new_soc_cust_user14_name" SIZE="40" VALUE="$soc_cust_user14_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user14_email">User 14 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user14_email" id="new_soc_cust_user14_email" SIZE="40" VALUE="$soc_cust_user14_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user14_password">User 14 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user14_password" id="new_soc_cust_user14_password" SIZE="20" VALUE="$soc_cust_user14_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user15_name">User 15 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user15_name" id="new_soc_cust_user15_name" SIZE="40" VALUE="$soc_cust_user15_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user15_email">User 15 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user15_email" id="new_soc_cust_user15_email" SIZE="40" VALUE="$soc_cust_user15_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user15_password">User 15 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user15_password" id="new_soc_cust_user15_password" SIZE="20" VALUE="$soc_cust_user15_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user16_name">User 16 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user16_name" id="new_soc_cust_user16_name" SIZE="40" VALUE="$soc_cust_user16_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user16_email">User 16 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user16_email" id="new_soc_cust_user16_email" SIZE="40" VALUE="$soc_cust_user16_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user16_password">User 16 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user16_password" id="new_soc_cust_user16_password" SIZE="20" VALUE="$soc_cust_user16_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user17_name">User 17 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user17_name" id="new_soc_cust_user17_name" SIZE="40" VALUE="$soc_cust_user17_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user17_email">User 17 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user17_email" id="new_soc_cust_user17_email" SIZE="40" VALUE="$soc_cust_user17_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user17_password">User 17 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user17_password" id="new_soc_cust_user17_password" SIZE="20" VALUE="$soc_cust_user17_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user18_name">User 18 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user18_name" id="new_soc_cust_user18_name" SIZE="40" VALUE="$soc_cust_user18_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user18_email">User 18 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user18_email" id="new_soc_cust_user18_email" SIZE="40" VALUE="$soc_cust_user18_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user18_password">User 18 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user18_password" id="new_soc_cust_user18_password" SIZE="20" VALUE="$soc_cust_user18_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user19_name">User 19 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user19_name" id="new_soc_cust_user19_name" SIZE="40" VALUE="$soc_cust_user19_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user19_email">User 19 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user19_email" id="new_soc_cust_user19_email" SIZE="40" VALUE="$soc_cust_user19_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user19_password">User 19 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user19_password" id="new_soc_cust_user19_password" SIZE="20" VALUE="$soc_cust_user19_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user20_name">User 20 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user20_name" id="new_soc_cust_user20_name" SIZE="40" VALUE="$soc_cust_user20_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user20_email">User 20 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user20_email" id="new_soc_cust_user20_email" SIZE="40" VALUE="$soc_cust_user20_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user20_password">User 20 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user20_password" id="new_soc_cust_user20_password" SIZE="20" VALUE="$soc_cust_user20_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user21_name">User 21 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user21_name" id="new_soc_cust_user21_name" SIZE="40" VALUE="$soc_cust_user21_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user21_email">User 21 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user21_email" id="new_soc_cust_user21_email" SIZE="40" VALUE="$soc_cust_user21_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user21_password">User 21 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user21_password" id="new_soc_cust_user21_password" SIZE="21" VALUE="$soc_cust_user21_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_user22_name">User 22 Name</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user22_name" id="new_soc_cust_user22_name" SIZE="40" VALUE="$soc_cust_user22_name"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user22_email">User 22 E-mail</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user22_email" id="new_soc_cust_user22_email" SIZE="40" VALUE="$soc_cust_user22_email"></td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_user22_password">User 22 Password</label></strong></td>
	<td valign="top"><INPUT type="text" NAME="new_soc_cust_user22_password" id="new_soc_cust_user22_password" SIZE="22" VALUE="$soc_cust_user22_password"></td></tr>

<tr><td valign="top" colspan="2" style="background-color:#000000"><img src="/images/spacer.gif"></td></tr>

<tr><td valign="top"><strong><label for="new_soc_cust_permission_alt_wording">Allow Custom Wording?</label></strong></td>
	<td valign="top">
EOM
$soc_cust_permission_alt_wording = "no" if ($soc_cust_permission_alt_wording eq '');
&print_yes_no_menu("new_soc_cust_permission_alt_wording", $soc_cust_permission_alt_wording);
my $num_rows = 5;
   $num_rows = 15 if (length($soc_cust_replacement_alt_wording) > 200); 
   $num_rows = 25 if (length($soc_cust_replacement_alt_wording) > 500); 
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_suppress_highlighting">Disable highlighting the innovation on the questionnaire?</label></strong></td>
	<td valign="top">
EOM
$soc_cust_suppress_highlighting = "no" if ($soc_cust_suppress_highlighting eq '');
&print_yes_no_menu("new_soc_cust_suppress_highlighting", $soc_cust_suppress_highlighting);
print<<EOM;
	<br>
	(This feature was added to allow searches/replaces that include the innovation name, because the code to highlight the innovation name includes quotes and angled brackets that made the replacement difficult.)
	</td></tr>
<tr><td valign="top"><strong><label for="new_soc_cust_replacement_alt_wording">Custom Wording Replacements</label></strong></td>
	<td valign="top"><textarea name="new_soc_cust_replacement_alt_wording" id="new_soc_cust_replacement_alt_wording" rows="$num_rows" cols="70">$soc_cust_replacement_alt_wording</textarea>
	<br>(enter the words to be replaced separated by a colon. If more than one set are to be replaced, use a semicolon between sets.  For example "<span style="color:#006600;">students:clients;nonacademic:nonessential;faculty:case managers</span>" Do not include any line breaks or html code.)
		<br><br>
		Also, if a replacement is for a question that contains the name of the innovation, use the code "HANDLEHIGHLIGHT" after the colon to tell the script to remove the highlight and addit back in, allowing you to do a substitution for text containing the name of the innovation.
	</td></tr>

</table>
	<UL>
		<INPUT TYPE="HIDDEN" NAME="show_record" VALUE="$show_record">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_add_record">
		<INPUT TYPE="SUBMIT" VALUE="$page_title">
	</UL>

EOM
#if ($show_record ne '') {
#print<<EOM;
#<div class="first fltRt">
#		<FORM ACTION="soc_customer_admin.cgi" METHOD=POST>
#		<table cellpadding="0" cellspacing="0" border="0">
#		<tr><td colspan="2"><em>Click here to delete this $item_label.</em></td></tr>
#		<tr><td valign="top"><input type="checkbox" name="confirm" value="confirmed"></td>
#			<td valign="top"><font color=red>confirm the deletion<br> of this $item_label.</font></td></tr>
#		<tr><td colspan="2">
#				<input type="hidden" name="location" value="process_delete_item">
#				<input type="hidden" name="show_record" value="$show_record">
#				<input type="submit" name="submit" value="Delete $item_label"></td></tr>
#				
#		</table>
#		</form>
#	
#</div>
#EOM
#}
print<<EOM;
</td>
	<td valign="top" align="right">
		(Click here to <A HREF="soc_customer_admin.cgi?location=logout">logout</A>)
		<br>
		- <a href="/concerns/">survey</a><br>
		- <a href="/concerns/admin/">survey admin</a><br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<INPUT TYPE=SUBMIT VALUE="Save">
		</form>
	</td></tr>
</table>
$htmltail
EOM
}
#################################################################################
## END: LOCATION = ADD_record
#################################################################################


#################################################################################
## START: LOCATION = send_directions_to_user
#################################################################################
if ($location eq 'send_directions_to_user') {
	my $this_soc_cust_logon_inst_sent = $query->param("this_soc_cust_logon_inst_sent");
	my $this_user_name = $query->param("this_user_name");
	my $this_user_email = $query->param("this_user_email");
	my $this_user_password = $query->param("this_user_password");
	my $this_user_number_free = $query->param("this_user_number_free");
	my $this_user_number_purchased = $query->param("this_user_number_purchased");
	   $this_user_number_purchased = $this_user_number_purchased - $this_user_number_free;
	my $this_email_cc = $query->param("this_email_cc");
	my $confirm = $query->param("confirm");
	my $this_fromaddress = "brian.litke\@sedl.org";
	my $mailprog = "/usr/sbin/sendmail -t -f$this_fromaddress"; #No -n because of webmaster alias
	my $recipient = $this_user_email;
#	   $recipient = 'Brian Litke <blitke@sedl.org>';
	my $fromaddr = 'webmaster@sedl.org';
	if ($confirm ne 'confirmed') {
		$error_message = "No e-mail was sent.  You forgot to check the confirmation checkbox. Please try again.";
		$location = "menu";
	} elsif ($this_user_email eq '') {
		$error_message = "Unexpected error.  The recipient e-mail was not found. Please try again.";
		$location = "menu";
	} elsif ($this_user_password eq '') {
		$error_message = "Unexpected error.  The recipient password was not found. Please try again.";
		$location = "menu";
	} else {
open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$fromaddr>
To: $recipient
EOM
	if ($this_email_cc ne '') {
print NOTIFY <<EOM;
Cc: $this_email_cc
EOM
	}
print NOTIFY <<EOM;
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Access to the Stages of Concern Questionnaire Online

Dear $this_user_name,

EOM

	if ($this_user_number_purchased == 0) {
print NOTIFY <<EOM;
Thank you for contacting SEDL regarding the Stages of Concern Questionnaire online. I have set up your administrator account for the SoCQ online.

I have added a quantity of "$this_user_number_free" survey completions to your account, so you can test the SoCQ site to see how it works before using it with live survey participants.
EOM
	} else {
print NOTIFY <<EOM;
Thank you for your purchase of the Stages of Concern Questionnaire online. I have set up your administrator account for the SoCQ online.

In addition to the $this_user_number_purchased survey completions you purchased, I have added a quantity of "$this_user_number_free" survey completions to your account, so you can test the SoCQ site to see how it works before using it with live survey participants.
EOM
	}
print NOTIFY <<EOM;

You can log on to the SoCQ Administrative interface at:
http://www.sedl.org/concerns/admin

You will log on to the admin site using 
	- Your e-mail address "$this_user_email"
	- Your password "$this_user_password"

NEXT STEPS:
Once you set up a survey "cohort" on the Admin site, you will have a password for that cohort which the participants will use to take the survey.  

Survey participants will access the SoCQ online at: 
http://www.sedl.org/concerns

Let me know if you have any difficulty accessing the site or have other questions about customizing the SoCQ online.

WATCH A DEMO:
You can watch a walkthrough video and view some screenshots of the different parts of the SoCQ admin site at:
http://www.sedl.org/cbam/socq-screenshots.html



Contact Brian Litke at webmaster\@sedl.org for assistance or additional information about the SoCQ online.

EOM
close(NOTIFY);
		$feedback_message = "The e-mail with instructions was sent successfully.";
		$location = "menu";
	} # END SENDING E-mail

	## START: UPDATE THE SENT LOG TO INDICATE DATE WE SENT THIS
	$this_user_email = &cleanformysql($this_user_email);

	my $command_update = "UPDATE soc_customers SET soc_cust_logon_inst_sent = '$date_full_pretty $this_soc_cust_logon_inst_sent' where soc_cust_user_email = '$this_user_email'";
		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command_update) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
	## END: UPDATE THE SENT LOG TO INDICATE DATE WE SENT THIS

} 
#################################################################################
## END: LOCATION = send_directions_to_user
#################################################################################


#################################################################################
## START: LOCATION = MENU
#################################################################################
if ($location eq 'menu') {

	###################################################################
	## START: QUERY DATABASE FOR SURVEY CODES IN USE, PER CUSTOMER
	###################################################################
	my %num_code_in_use; # DECLARE HASH TO STORE DATA
	my $command = "select soc_sc_cust_unique_id, COUNT(soc_sc_code) FROM soc_survey_codes GROUP BY soc_sc_cust_unique_id";

	$dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	while (my @arr = $sth->fetchrow) {
		my ($soc_sc_cust_unique_id, $num_codes) = @arr;
			$num_code_in_use{$soc_sc_cust_unique_id} = $num_codes;
	} # END DB QUERY LOOP
	###################################################################
	## END: QUERY DATABASE FOR SURVEY CODES IN USE, PER CUSTOMER
	###################################################################

	###################################################################
	## START: QUERY DATABASE FOR SURVEYS COMPLETED, PER CUSTOMER
	###################################################################
	my %num_completed_surveys; # DECLARE HASH TO STORE DATA
	my $command = "select soc_survey_codes.soc_sc_cust_unique_id, soc_survey_data.soc_sd_sc_code 
					FROM soc_survey_codes, soc_survey_data 
					WHERE soc_survey_data.soc_sd_sc_code = soc_survey_codes.soc_sc_code";

	$dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#print header;
#print "<p class=\"info\">COMMAND: $command <BR><BR>MATCHES: $num_matches</p>";
	while (my @arr = $sth->fetchrow) {
		my ($soc_sc_cust_unique_id, $this_code) = @arr;
			$num_completed_surveys{$soc_sc_cust_unique_id}++;
	} # END DB QUERY LOOP
	###################################################################
	## END: QUERY DATABASE FOR SURVEYS COMPLETED, PER CUSTOMER
	###################################################################

my $command_get_last_submission_date = "SELECT soc_sd_datestamp from soc_survey_data order by soc_sd_datestamp DESC LIMIT 1";


## PRINT SIGNUP FORM
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label: List of $item_label\s</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1><A HREF="soc_customer_admin.cgi">$site_label</A>
		<br>List of $item_label\s</h1></td>
	<td valign="top" align="right">
		<table>
		<tr><td>
		(Click here to <A HREF="soc_customer_admin.cgi?location=logout">logout</A>)<br>
		- Go to: <a href="/concerns/">survey</a><br>
		- Go to: <a href="/concerns/admin/">survey admin</a>
		</td></tr>
		</table>
	</td></tr>
</table>
EOM

	###################################################################
	## START: QUERY DATABASE TO GET DATESTAMP OF LAST SURVEY SUBMISSION
	###################################################################
		my $last_survey_datestamp = "";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command_get_last_submission_date) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		while (my @arr = $sth->fetchrow) {
			($last_survey_datestamp) = @arr;
#			print "<p>axel</p>";
		} # END DB QUERY LOOP
		$last_survey_datestamp = &convert_timestamp_2pretty_w_date($last_survey_datestamp, "yes");
	###################################################################
	## END: QUERY DATABASE TO GET DATESTAMP OF LAST SURVEY SUBMISSION
	###################################################################




	if ($validuser ne 'yes') {
		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view $item_label.</FONT>";
	}

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

		## COUNT REAL CUSTOMER
		my $command_count_real_customers = "select soc_cust_qty_purchased, soc_cust_user_email, soc_cust_free_surveys from soc_customers 
		WHERE soc_cust_user_email NOT LIKE '%sedl\.org%' 
		AND soc_cust_user_email NOT LIKE 'gene.hall\@unlv.edu' 
		AND soc_cust_user_email NOT LIKE 'eliase.huang\@gmail.com' 
		AND soc_cust_org NOT LIKE '%Alabama SIG Coach%' 
		AND (soc_cust_qty_purchased > 50) ";
		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command_count_real_customers) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_real_customers = $sth->rows;
		my $total_sales = 0;
			while (my @arr = $sth->fetchrow) {
				my ($soc_cust_qty_purchased, $soc_cust_user_email, $soc_cust_free_surveys) = @arr;
				$total_sales = $total_sales + $soc_cust_qty_purchased - $soc_cust_free_surveys;
			} # END WHILE LOOP
			$total_sales = $total_sales * 0.5;
		## COUNT REAL CUSTOMERS
		
my $command = "select * from soc_customers";
#	$command .= " order by hiring_supervisor, name_l, name_f" if ($sortby eq 'supervisor');
	$command .= " order by soc_cust_name" if ($sortby eq 'name');
	$command .= " order by soc_cust_date_added DESC" if ($sortby eq 'date_added');
	$command .= " order by soc_cust_version" if ($sortby eq 'version');
	$command .= " order by date_last_logon DESC" if (($sortby eq 'date_last_logon') || ($sortby eq ''));



#print "<P>$command<P>";
		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
my $num_matches_records = $sth->rows;

$total_sales = &format_number($total_sales, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
print<<EOM;
<div class="fltrt" style="width:120px;">

<table border="0" cellpadding="4" cellspacing="0" style="border:1px solid #999999;">
<tr><td><strong>Legend</strong></td></tr>
<tr><td BGCOLOR="#FFCC99">Sample Accounts</td></tr>
<tr><td BGCOLOR="#cccccc">SEDL Accounts</td></tr>
</table>
</div>

<P>
Of the $num_matches_records SoCQ Administrators accounts, there are $num_matches_real_customers paying customers who have paid <span style="color:green;font-weight:bold;">\$$total_sales</span> to SEDL.<br>
The last time an SoCQ survey was submitted was: $last_survey_datestamp\.
<br>
<br>
After setting up a user, you can edit their record to access an option for sending an e-mail with logon information to the user.<br>
Click here for the <a href="cfsoc_customer_admin.cgi">CFSoCQ Customer Manager</a>.<br>
Click here for the <a href="plc_customer_admin.cgi">PLCA-R Customer Manager</a>.<br>
Click here for the <a href="ws_customer_admin.cgi">WS Customer Manager</a>.
</p>
<table width="100%">
<tr><td>
	<form ACTION="soc_customer_admin.cgi" METHOD="POST" name="form2" id="form2">
	Click here to 
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="add_record">
	<INPUT TYPE=SUBMIT VALUE="Add a New $item_label">
	</form>
	</td>
	<td style="text-align:right;">
		<form ACTION="/staff/video/videos.cgi" name="form_1" id="form_1" METHOD="POST" style="display:inline;margin:0;padding:0;">
			<input type="hidden" name="w" value="640">
			<input type="hidden" name="h" value="480">
			<input type="hidden" name="video_filename" value="socq-manager">
			<input type="hidden" name="video_title" value="Using the SoCQ Customer Manager">
			<input type="hidden" name="link_back" value="http://www.sedl.org/staff/communications/soc_customer_admin.cgi">
			<a href="#" onclick="document.form_1.submit(); return false"><img src="/staff/images/watch_video.jpg" alt="watch a video about how to use this page." class="noBorder"></a></form>	

	</td>
</tr>
</table>
EOM
#<p>
#New releases will not show up online until you click here to <A HREF=soc_customer_admin.cgi?location=makehtml>update the $item_label\s</A> section of the Afterschool Web site.
#</p>
#EOM
	if ($num_matches_records == 0) {
		print "<p class=\"alert\">There are no SoCQ Customers in the database.</p>";
	}

my $label_name = "<a href=\"soc_customer_admin.cgi?sortby=name\">Customer Name</a>";
   $label_name = "Customer Name" if ($sortby eq 'name');
my $label_last_logon = "<a href=\"soc_customer_admin.cgi?sortby=date_last_logon\">Last Logon</a>";
   $label_last_logon = "Last Logon" if ($sortby eq 'date_last_logon');
my $label_date_added = "<a href=\"soc_customer_admin.cgi?sortby=date_added\">Date Added</a>";
   $label_date_added = "Date Added" if ($sortby eq 'date_added');
print<<EOM;
<table border="1" cellpadding="3" cellspacing="0" style="background-color:#ffffff;">
<tr bgcolor="#ebebeb">
	<td><strong>$label_date_added</strong></td>
	<td><strong>$label_name</strong> (click the name to view/edit the $item_label)</td>
	<td><strong>Customer Password for Survey Admin Site</strong></td>
	<td style="font-size:10px;"><strong>Survey Count Licensed</strong></td>
	<td style="font-size:10px;"><strong>Surveys<br>Utilized</strong></td>
	<td style="font-size:10px;"><strong>Free<br>Surveys</strong></td>
	<td style="font-size:10px;"><strong># Survey<br>Codes<br>Set Up</strong></td>
	<td style="font-size:10px;"><strong>$label_last_logon</strong></td>
	<td style="font-size:10px;"><strong>Copy-<br>right</strong></td>
	<td style="font-size:10px;"><strong>Sent<br>logon<br>email</strong></td>
</tr>
EOM


my $counter = 1;
	while (my @arr = $sth->fetchrow) {
		my ($soc_cust_unique_id, $soc_cust_user_email, $soc_cust_user_password, $soc_cust_date_added, $soc_cust_name, $soc_cust_qty_purchased, $soc_cust_notes, $date_last_logon, $soc_cust_org, $soc_cust_state, $soc_cust_phone, $soc_cust_version, 
			$soc_cust_user2_name, $soc_cust_user2_email, $soc_cust_user2_password, $soc_cust_user3_name, $soc_cust_user3_email, $soc_cust_user3_password, $soc_cust_user4_name, $soc_cust_user4_email, $soc_cust_user4_password, $soc_cust_user5_name, $soc_cust_user5_email, $soc_cust_user5_password, $soc_cust_user6_name, $soc_cust_user6_email, $soc_cust_user6_password, $soc_cust_user7_name, $soc_cust_user7_email, $soc_cust_user7_password, $soc_cust_user8_name, $soc_cust_user8_email, $soc_cust_user8_password, $soc_cust_user9_name, $soc_cust_user9_email, $soc_cust_user9_password, $soc_cust_user10_name, $soc_cust_user10_email, $soc_cust_user10_password, $soc_cust_user11_name, $soc_cust_user11_email, $soc_cust_user11_password,
			$soc_cust_user12_name, $soc_cust_user12_email, $soc_cust_user12_password,
			$soc_cust_user13_name, $soc_cust_user13_email, $soc_cust_user13_password,
			$soc_cust_user14_name, $soc_cust_user14_email, $soc_cust_user14_password,
			$soc_cust_user15_name, $soc_cust_user15_email, $soc_cust_user15_password,
			$soc_cust_user16_name, $soc_cust_user16_email, $soc_cust_user16_password,
			$soc_cust_user17_name, $soc_cust_user17_email, $soc_cust_user17_password,
			$soc_cust_user18_name, $soc_cust_user18_email, $soc_cust_user18_password,
			$soc_cust_user19_name, $soc_cust_user19_email, $soc_cust_user19_password,
			$soc_cust_user20_name, $soc_cust_user20_email, $soc_cust_user20_password,
			$soc_cust_user21_name, $soc_cust_user21_email, $soc_cust_user21_password,
			$soc_cust_user22_name, $soc_cust_user22_email, $soc_cust_user22_password,
			$soc_cust_permission_alt_wording, $soc_cust_replacement_alt_wording, $soc_cust_free_surveys, $soc_cust_copyright_date, $soc_cust_copyright_enteredby, $soc_cust_copyright_notes, $soc_cust_logon_inst_sent, $soc_cust_suppress_highlighting) = @arr;
		my $soc_cust_user_password_full = $soc_cust_user_password;

		my $num_admin_users = "";
		   $num_admin_users++ if ($soc_cust_user2_email ne '');
		   $num_admin_users++ if ($soc_cust_user3_email ne '');
		   $num_admin_users++ if ($soc_cust_user4_email ne '');
		   $num_admin_users++ if ($soc_cust_user5_email ne '');
		   $num_admin_users++ if ($soc_cust_user6_email ne '');
		   $num_admin_users++ if ($soc_cust_user7_email ne '');
		   $num_admin_users++ if ($soc_cust_user8_email ne '');
		   $num_admin_users++ if ($soc_cust_user9_email ne '');
		   $num_admin_users++ if ($soc_cust_user10_email ne '');
		   $num_admin_users++ if ($soc_cust_user11_email ne '');
		   $num_admin_users++ if ($soc_cust_user12_email ne '');
		   $num_admin_users++ if ($soc_cust_user13_email ne '');
		   $num_admin_users++ if ($soc_cust_user14_email ne '');
		   $num_admin_users++ if ($soc_cust_user15_email ne '');
		   $num_admin_users++ if ($soc_cust_user16_email ne '');
		   $num_admin_users++ if ($soc_cust_user17_email ne '');
		   $num_admin_users++ if ($soc_cust_user18_email ne '');
		   $num_admin_users++ if ($soc_cust_user19_email ne '');
		   $num_admin_users++ if ($soc_cust_user20_email ne '');
		   $num_admin_users++ if ($soc_cust_user21_email ne '');
		   $num_admin_users++ if ($soc_cust_user22_email ne '');

		my $num_admin_names = "";
		   $num_admin_names .= ", $soc_cust_user2_email" if ($soc_cust_user2_email ne '');
		   $num_admin_names .= ", $soc_cust_user3_email" if ($soc_cust_user3_email ne '');
		   $num_admin_names .= ", $soc_cust_user4_email" if ($soc_cust_user4_email ne '');
		   $num_admin_names .= ", $soc_cust_user5_email" if ($soc_cust_user5_email ne '');
		   $num_admin_names .= ", $soc_cust_user6_email" if ($soc_cust_user6_email ne '');
		   $num_admin_names .= ", $soc_cust_user7_email" if ($soc_cust_user7_email ne '');
		   $num_admin_names .= ", $soc_cust_user8_email" if ($soc_cust_user8_email ne '');
		   $num_admin_names .= ", $soc_cust_user9_email" if ($soc_cust_user9_email ne '');
		   $num_admin_names .= ", $soc_cust_user10_email" if ($soc_cust_user10_email ne '');
		   $num_admin_names .= ", $soc_cust_user11_email" if ($soc_cust_user11_email ne '');
		   $num_admin_names .= ", $soc_cust_user12_email" if ($soc_cust_user12_email ne '');
		   $num_admin_names .= ", $soc_cust_user13_email" if ($soc_cust_user13_email ne '');
		   $num_admin_names .= ", $soc_cust_user14_email" if ($soc_cust_user14_email ne '');
		   $num_admin_names .= ", $soc_cust_user15_email" if ($soc_cust_user15_email ne '');
		   $num_admin_names .= ", $soc_cust_user16_email" if ($soc_cust_user16_email ne '');
		   $num_admin_names .= ", $soc_cust_user17_email" if ($soc_cust_user17_email ne '');
		   $num_admin_names .= ", $soc_cust_user18_email" if ($soc_cust_user18_email ne '');
		   $num_admin_names .= ", $soc_cust_user19_email" if ($soc_cust_user19_email ne '');
		   $num_admin_names .= ", $soc_cust_user20_email" if ($soc_cust_user20_email ne '');
		   $num_admin_names .= ", $soc_cust_user21_email" if ($soc_cust_user21_email ne '');
		   $num_admin_names .= ", $soc_cust_user22_email" if ($soc_cust_user22_email ne '');
		   if ($num_admin_users > 0) {
		   		my $s = "";
		   		   $s = "s" if ($num_admin_users > 1);
		   		$num_admin_names = "QQQ$num_admin_names";
		   		$num_admin_names =~ s/QQQ, //gi;
				$num_admin_users = "<span title=\"$num_admin_names\">$num_admin_users admin$s</span>";
		   }

		# TRANSFORM DATES INTO PRETTY FORMAT
		$soc_cust_date_added = &convert_timestamp_2pretty_w_date($soc_cust_date_added);

		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
#		$news_item_heading = &cleanaccents2html($news_item_heading);

my $remaining = $soc_cust_qty_purchased - $num_completed_surveys{$soc_cust_unique_id};
$num_code_in_use{$soc_cust_unique_id} = "0" if ($num_code_in_use{$soc_cust_unique_id} eq '');
$date_last_logon = &convert_timestamp_2pretty_w_date($date_last_logon, 'yes') if ($date_last_logon ne '');

$soc_cust_user_password = "******" if ($soc_cust_user_email =~ 'sedl\.org');
$soc_cust_state = "($soc_cust_state)" if ($soc_cust_state ne '');

		my $bgcolor="";
			$bgcolor="BGCOLOR=\"#FFCC99\"" if ($soc_cust_qty_purchased <= 39);
  			$bgcolor="BGCOLOR=\"#cccccc\"" if ($soc_cust_user_email =~ 'sedl\.org');
   			$bgcolor="BGCOLOR=\"#cccccc\"" if ($soc_cust_user_email =~ 'gene.hall\@unlv.edu');
 			$bgcolor="BGCOLOR=\"#FFCC99\"" if ($soc_cust_user_email =~ 'eliase.huang\@gmail.com');
  			$bgcolor="BGCOLOR=\"#FFFFCC\"" if ($show_record eq $soc_cust_unique_id);

$soc_cust_phone = "$soc_cust_phone<br>" if ($soc_cust_phone ne '');

$soc_cust_version = "<font color=red>$soc_cust_version</font>" if ($soc_cust_version eq '2008');
$soc_cust_version = "<font color=green>$soc_cust_version</font>" if ($soc_cust_version eq '2010');
$soc_cust_copyright_notes =~ s/"/'/gi;
if ($soc_cust_copyright_date eq '') {
	$soc_cust_copyright_date = "<span style=\"color:#cc0000;\">pending<br>copyright</span>";
}
print<<EOM;
<tr $bgcolor>
	<td valign="top"><a name="$soc_cust_unique_id"></a><span style="color:#aaaaaa">$counter</span><br><span style="font-size:8px;">$soc_cust_date_added</span></td>
	<td valign="top"><A HREF=\"soc_customer_admin.cgi?location=add_record&show_record=$soc_cust_unique_id\" TITLE="Click to edit this customer record">$soc_cust_name</a><br>
					$soc_cust_user_email<br>
					$soc_cust_phone
					<span style="color:#666666;">$soc_cust_org $soc_cust_state</span></td>
	<td valign="top" style="font-size:10px;">$soc_cust_user_password<br>

	<form ACTION="/concerns/admin/index.cgi" METHOD="POST">
	<INPUT TYPE="HIDDEN" NAME="record_logon" VALUE="no">
	<INPUT TYPE="HIDDEN" NAME="logon_user" VALUE="$soc_cust_user_email">
	<INPUT TYPE="HIDDEN" NAME="logon_pass" VALUE="$soc_cust_user_password_full">
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
	<INPUT TYPE="SUBMIT" VALUE="Log on\nas this user">
	</form>
	$num_admin_users
	</td>
	<td valign="top">$soc_cust_qty_purchased</td>
	<td valign="top" nowrap style="font-size:10px;">$num_completed_surveys{$soc_cust_unique_id}<br>($remaining unused)</td>
	<td valign="top">$soc_cust_free_surveys</td>
	<td valign="top">$num_code_in_use{$soc_cust_unique_id}
EOM
$soc_cust_replacement_alt_wording =~ s/"/'/gi;
print "<br><span style=\"font-size:10px;\" title=\"$soc_cust_replacement_alt_wording\">custom<br>wording<br></span>" if ($soc_cust_replacement_alt_wording ne '');
print<<EOM;
	</td>
	<td valign="top" style="font-size:10px;">$date_last_logon</td>
	<td valign="top" style="font-size:10px;"><span title="$soc_cust_copyright_notes">$soc_cust_copyright_date<br>$soc_cust_copyright_enteredby</span></td>
	<td valign="top" style="font-size:10px;">$soc_cust_logon_inst_sent</td>
</tr>
EOM

		$counter++;
		## PRINT COLUMN HEADINGS EVERY 10 SPACES
		if (($counter/10) == int($counter/10)) {
print<<EOM;
<tr bgcolor="#ebebeb">
	<td><strong>$label_date_added</strong></td>
	<td><strong>$label_name</strong> (click the name to view/edit the $item_label)</td>
	<td><strong>Customer Password for Survey Admin Site</strong></td>
	<td style="font-size:10px;"><strong>Survey Count Licensed</strong></td>
	<td style="font-size:10px;"><strong>Surveys<br>Utilized</strong></td>
	<td style="font-size:10px;"><strong>Free<br>Surveys</strong></td>
	<td style="font-size:10px;"><strong># Survey<br>Codes<br>Set Up</strong></td>
	<td style="font-size:10px;"><strong>$label_last_logon</strong></td>
	<td style="font-size:10px;"><strong>Copy-<br>right</strong></td>
	<td style="font-size:10px;"><strong>Sent<br>logon<br>email</strong></td>
</tr>
EOM
		}
	} # END DB QUERY LOOP
print<<EOM;
</table>

<p>
To report troubles using this form, send an e-mail to <a href="mailto:webmaster\@sedl.org">webmaster\@sedl.org</a> 
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
#   $dirtyitem =~ s/\@/\&\#x040\;/gi; # MESES UP e-MAILS SENT USING Perl, but good for displaying
   $dirtyitem =~ s/mailto\:/&#109;&#97;&#105;&#108;&#116;&#111;&#58;/gi;
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


sub cleanaccents2html {
my $cleanitem = $_[0];
	$cleanitem =~ s/Ò/"/g;			
	$cleanitem =~ s/Ó/"/g;			
	$cleanitem =~ s/Õ/'/g;			
	$cleanitem =~ s/Ô/'/g;
	$cleanitem =~ s// /g;
	$cleanitem =~ s/Ð/\&ndash\;/g;
	$cleanitem =~ s/Ñ/\&mdash\;/g;
	$cleanitem =~ s/Ê//g; # invisible bullet
	$cleanitem =~ s/É/.../g;
	$cleanitem =~ s/Ë/&Agrave\;/g; 
	$cleanitem =~ s/ˆ/&agrave\;/g;	
	$cleanitem =~ s/ç/&Aacute\;/g;  
	$cleanitem =~ s/‡/&aacute\;/g;
	$cleanitem =~ s/å/&Acirc\;/g;
	$cleanitem =~ s/‰/&acirc\;/g;
	$cleanitem =~ s/Ì/&Atilde\;/g;
	$cleanitem =~ s/‹/&atilde\;/g;
	$cleanitem =~ s/€/&Auml\;/g;
	$cleanitem =~ s/Š/&auml\;/g;
	$cleanitem =~ s/ƒ/&Eacute\;/g;
	$cleanitem =~ s/Ž/&eacute\;/g;
	$cleanitem =~ s/é/&Egrave\;/g;
	$cleanitem =~ s//&egrave\;/g;
	$cleanitem =~ s/æ/&Euml\;/g;
	$cleanitem =~ s/‘/&euml\;/g;
	$cleanitem =~ s/í/&Igrave\;/g;
	$cleanitem =~ s/“/&igrave\;/g;
	$cleanitem =~ s/ê/&Iacute\;/g;
	$cleanitem =~ s/’/&iacute\;/g;
	$cleanitem =~ s/ë/&Icirc\;/g;
	$cleanitem =~ s/”/&icirc\;/g;
	$cleanitem =~ s/ì/&Iuml\;/g;
	$cleanitem =~ s/•/&iuml\;/g;
	$cleanitem =~ s/„/&Ntilde\;/g;
	$cleanitem =~ s/–/&ntilde\;/g;
	$cleanitem =~ s/ñ/&Ograve\;/g;
	$cleanitem =~ s/˜/&ograve\;/g;
	$cleanitem =~ s/î/&Oacute\;/g;
	$cleanitem =~ s/—/&oacute\;/g;
	$cleanitem =~ s/Í/&Otilde\;/g;
	$cleanitem =~ s/›/&otilde\;/g;
	$cleanitem =~ s/…/&Ouml\;/g;
	$cleanitem =~ s/š/&ouml\;/g;
	$cleanitem =~ s/ô/&Ugrave\;/g;
	$cleanitem =~ s//&ugrave\;/g;
	$cleanitem =~ s/ò/&Uacute\;/g;
	$cleanitem =~ s/œ/&uacute\;/g;
	$cleanitem =~ s/ó/&Ucirc\;/g;  ## THIS REPLACES THE — FOR SOME REASON
	$cleanitem =~ s/ž/&ucirc\;/g;
	$cleanitem =~ s/†/&Uuml\;/g;
	$cleanitem =~ s/Ÿ/&uuml\;/g;
	$cleanitem =~ s/Ø/&yuml\;/g;
	return ($cleanitem);
}


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


######################################
## START: SUBROUTINE print_yes_no_menu
######################################
sub print_yes_no_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", "yes", "no");
	my @item_label = ("", "yes", "no");
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

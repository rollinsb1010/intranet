#!/usr/bin/perl 

################################################################################
# Copyright 2003 by Southwest Educational Development Laboratory
# Written by Brian Litke, SEDL Web Administrator (05-25-2003)
#
# This script is used to collect survey data about Integration Workshop #1 
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
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
my $query = new CGI;

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 
use intranetcommoncode; 



##############################
## START: GRAB PAGE TEMPLATE #
##############################
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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("117"); # 117 is the PID for this page in the intranet database

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";
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
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;
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

my $format = $query->param("format");
my $format_db = $query->param("format_db");
my $location = $query->param("location");
	$location = "select_format_location_blank" if ($location eq '');

if (($location eq 'showform') && ($format eq '')) {
	$location = "select_format_missing_format";
	$error_message = "<FONT COLOR=\"RED\">You forgot to describe the format of the item you are searching for.</FONT>";
}

my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};
my $browser = $ENV{"HTTP_USER_AGENT"};


my $book_title = $query->param("book_title");
my $book_author = $query->param("book_author");
my $book_format = $query->param("book_format");
my $book_other = $query->param("book_other");
my $book_publisher = $query->param("book_publisher");
my $book_pubdate = $query->param("book_pubdate");
my $book_isbn = $query->param("book_isbn");
my $book_edition = $query->param("book_edition");
my $book_price_copy = $query->param("book_price_copy");
my $book_num_copies = $query->param("book_num_copies");
my $book_total_amount = $query->param("book_total_amount");
my $j_title = $query->param("j_title");
my $j_publisher = $query->param("j_publisher");
my $j_format = $query->param("j_format");
my $j_issn = $query->param("j_issn");
my $j_subscrip_price = $query->param("j_subscrip_price");
my $j_issue_num = $query->param("j_issue_num");
my $other_description = $query->param("other_description");
my $db_title = $query->param("db_title");
my $db_url = $query->param("db_url");
my $db_price = $query->param("db_price");
my $db_authusers = $query->param("db_authusers");
my $staff_name = $query->param("staff_name");
my $staff_ext = $query->param("staff_ext");
my $staff_email = $query->param("staff_email");
my $staff_dept = $query->param("staff_dept");
my $staff_program = $query->param("staff_program");
my $staff_ba = $query->param("staff_ba");
my $staff_budget_code = $query->param("staff_budget_code");
my $info_availability_atsedl = $query->param("info_availability_atsedl");
my $info_availability_library = $query->param("info_availability_library");
my $info_availability_routing = $query->param("info_availability_routing");
my $special_instructions = $query->param("special_instructions");

my $routing = $query->param("routing");
my $routing_individual = $query->param("routing_individual");
my $routing_instructions = $query->param("routing_instructions");

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
if ($location eq 'showform') {
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
			$location = "select_format_process_logon_failure"; # SHOW LOGON SCREEN
		}
	} else {
	## USER DIDN't ENTER USER ID OR PASSWORD, SHOW LOON SCREEN & SET ERROR MESSAGE
		$error_message .= "You forgot to enter your User ID (ex: whoover)." if ($logon_user eq '');
		$error_message .= "You forgot to enter your password." if ($logon_pass eq '');
	}
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
	$location = "select_format_logout"; # AFTER LOGOUT, SHOW LOGON SCREEN
}
######################################################
## END: LOCATION = LOGOUT
######################################################


######################################################
## START: CHECK SESSION ID AND VERIFY
######################################################
	## IF SESSION ID NOT FOUND, PROMPT FOR LOGON
	if ($cookie_ss_session_id eq '') {
#		$location = "select_format_session_id_missing";
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

			## SAVE COOKIES WITH SESSION ID AND USER ID
				setCookie ("ss_session_id", $ss_session_id, $expdate, $path, $thedomain);
				setCookie ("staffid", $ss_staff_id, $expdate, $path, $thedomain);
#			## SET LOCATION
#				$location = "showform";
				$logon_user = $ss_staff_id;

		} # END DB QUERY LOOP

		## IF SESSION NOT ACTIVE, PROMPT FOR LOGON
		if ($num_matches eq '0') {
			$cookie_ss_session_id = "";
			setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
			$location = "select_format_check_session"; # AFTER LOGOUT, SHOW LOGON SCREEN
		} else {
		$session_active = "yes";
		}
	}
######################################################
## END: CHECK SESSION ID AND VERIFY
######################################################


######################################################
## START: CHECK FOR REQUIRED VARIABLES
######################################################

if (($format eq '') && ($location eq 'showform')) {
	$error_message = "You forgot to indicate thr <strong>format</strong> of the resource you are requesting.";
	$location = "select_format_check_required_variables";
}
######################################################
## END: CHECK FOR REQUIRED VARIABLES
######################################################






################################
## START PAGE HEADER
################################
print header;

print <<EOM;
<HTML>
<head>
<title>SEDL Staff Information Services - Centralized Book Purchasing</title>
$htmlhead
EOM

   

####################################################################
####################################################################
## START: IF LOCATION = ENTERRECORD
####################################################################
####################################################################
if ($location eq 'enterrecord') {





	if ($error_message ne '') {
		##############################################
		##############################################
		## START OF ACTIONS IF USER SUBMITS BAD ENTRY
		##############################################
		##############################################

		## PRINT THANK YOU MESSAGE AFTER SURVEY IS SUBMITTED

print <<EOM;
<CENTER><h1 style="margin-top:0px;">Error</H1></CENTER>
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
Your request has been sent to Nancy Reynolds via e-mail.
<P>
<H2>Next Steps</H2>
	<OL>
	<LI>Nancy will verify your request and determine the best course of action (e.g. check out from the IRC if 
		already owned, borrow from another library, or purchase) and then</LI> 
	<LI>Nancy will return the resource search request form to you with any additional ordering or availability 
		information found.</LI> 
	<LI>You will use the information from Nancy to prepare and submitt a 
		purchase requisition (PR) to OFTS staff.</LI> 
	<LI>After receiving your PR, OFTS staff will prepare and mail a PO with payment (or will fax the PO) to 
		the vendor or will send the PO to Nancy Reynolds to order online if it is available from Amazon.com.</LI> 

	</OL>
<P>

<P>
<H2>When the material you request arrives</H2>
<P>
It is received by OFTS staff who will send it to IRC staff to enter in appropriate 
section of the SEDL intranet, process, check out to the original requester, and deliver to 
the administrative assistant of the program that initiated the request. For journal subscriptions, 
IRC staff will notify appropriate program staff when the first issue of a new subscription is 
received and shelved in the IRC. When access to a electronic database becomes available, IRC 
staff will notify appropriate program staff.

<P>
Click here to <A HREF="resource_search.cgi">enter another Resource Search Request</A>.
<P>
Click here to explore the <A HREF="/staff/information/">Information Services</A> Web site.
EOM


## WRITE THE SURVEY RESULTS TO A FILE
open(SURVEYRESULTSDATA,">>/home/httpd/html/staff/information/resource_search_data.txt");
#print SURVEYRESULTSDATA "todaysdate\tstaff name\tstaff ext\tstaff e-mail\tstaff dept\tstaff program\tstaff ba\tstaff budget code\tformat\tinfo availability atsedl\tinfo availability library\tspecial instructions\tbook title\tbook author\tbook format\tbook other\tbook publisher\tbook pubdate\tbook isbn\tbook edition\tbook price copy\tbook num copies\tbook total amount\tj title\tj publisher\tj format\tj issn\tj subscrip price\tj issue num\tother description\tdb title\tdb url\tdb price\tdb authusers\trouting\trouting_individual\trouting_instructions\tipnum ipnum2\n";
print SURVEYRESULTSDATA "$todaysdate\t$staff_name\t$staff_ext\t$staff_email\t$staff_dept\t$staff_program\t$staff_ba\t$staff_budget_code\t$format\t$info_availability_atsedl\t$info_availability_library\t$special_instructions\t$book_title\t$book_author\t$book_format\t$book_other\t$book_publisher\t$book_pubdate\t$book_isbn\t$book_edition\t$book_price_copy\t$book_num_copies\t$book_total_amount\t$j_title\t$j_publisher\t$j_format\t$j_issn\t$j_subscrip_price\t$j_issue_num\t$other_description\t$db_title\t$db_url\t$db_price\t$db_authusers\t$routing\t$routing_individual\t$routing_instructions\t$ipnum $ipnum2\n";
close(SURVEYRESULTSDATA);


## SET MAIL NOTIFICATION VARIABLES
my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
my $recipient = '';
#   $recipient = 'blitke@sedl.org, nreynold@sedl.org';



############################# START OF EMAIL TO USER #############################
## WRITE THE SURVEY RESULTS TO AN E-MAIL
my $fromaddr = 'webmaster@sedl.org';

## QUERY STAF PROFILES DB FOR OTHER E-MAIL ADDRESSES
my $command = "select firstname, lastname, email from staff_profiles where userid LIKE '$logon_user'";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

	while (my @arr = $sth->fetchrow) {
    	my ($firstname, $lastname, $email) = @arr;

		$recipient = $email;
		
open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Confirmation from Resource Search Request Form

Dear $firstname $lastname\,

Your Resource Search Request was sent to Nancy Reynolds in OIC on $todaysdate\.

The following data was received from Resource Search Request Form at:
http://www.sedl.org/staff/information/resource_search.cgi

Request Date: $todaysdate

Name: $staff_name
Ext.: $staff_ext
E-mail: $staff_email
Dept: $staff_dept
For Program: $staff_program
Budget Auth: $staff_ba
Budget Code: $staff_budget_code

Special Instructions:
$special_instructions

Special notifications:
 - If at SEDL: $info_availability_atsedl
 - If at Library: $info_availability_library
 - If ordered, after cataloging, please route: $info_availability_routing

Routing: $routing
 - User: $routing_individual
 - Instructions: $routing_instructions

EOM


	if (($format eq 'Book') || ($format eq 'Audiovisual')) {
print NOTIFY <<EOM;
$format Description:
 - Title: $book_title
 - Author: $book_author
 - Format: $book_format $book_other
 - Publisher: $book_publisher
 - Pub. Date: $book_pubdate
 - ISBN: $book_isbn
 - Edition: $book_edition
 - Price per copy: $book_price_copy
 - Number of Copies: $book_num_copies
 - Total Amount: $book_total_amount

EOM
	}

	if ($format =~ 'Journal') {
print NOTIFY <<EOM;
$format Description:
 - Title: $j_title
 - Publisher: $j_publisher
 - Format: $j_format
 - ISSN: $j_issn
 - Subscription/Issue Price: $j_subscrip_price
 - Issue Number: $j_issue_num

EOM
	}

	if ($format eq 'Other') {
print NOTIFY <<EOM;
OTHER Resource Description:
$other_description

EOM
	}

	if ($format eq 'Electronic Database') {
print NOTIFY <<EOM;
$format Description:
 - Title: $db_title
 - URL: $db_url
 - Price: $db_price
 - Authorized Users: $db_authusers

EOM
	}

print NOTIFY <<EOM;


NEXT STEPS:
1) Nancy will verify your request and determine the best course of action (e.g. check out from the IRC if already owned, borrow from another library, or purchase) and then

2) Nancy will return the resource search request form to you with any additional ordering or availability information found.

3) You will use the information from Nancy to prepare and submitt a purchase requisition (PR) to OFTS staff.

4) After receiving your PR, OFTS staff will prepare and mail a PO with payment (or will fax the PO) to the vendor or will send the PO to Nancy Reynolds to order online if it is available from Amazon.com.


WHEN THE MATERIAL YOU REQUEST ARRIVES:
It is received by OFTS staff who will send it to IRC staff to enter in appropriate section 
of the SEDL intranet, process, check out to the original requester, and deliver to the 
administrative assistant of the program that initiated the request. For journal 
subscriptions, IRC staff will notify appropriate program staff when the first issue 
of a new subscription is received and shelved in the IRC. When access to an electronic 
database becomes available, IRC staff will notify appropriate program staff.


EOM
close(NOTIFY);
} # END DB QUERY LOOP

############################# END OF EMAIL TO USER ############################



############################# START OF EMAIL TO OIC INFORMATION SERVICES #############################
## WRITE THE SURVEY RESULTS TO AN E-MAIL
my $fromaddr = 'webmaster@sedl.org';
   $recipient = 'nreynold@sedl.org';
#   $recipient = 'blitke@sedl.org';

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Data from Resource Search Request Form

The following data was received from Resource Search Request Form at:
http://www.sedl.org/staff/information/resource_search.cgi

The results of this survey have been saved to a tab-delimited text file at: 
http://www.sedl.org/staff/information/resource_search_data.txt


RESOURCE REQUEST INFORMATION STARTS HERE:
=========================================
Request Date: $todaysdate

Name: $staff_name
Ext.: $staff_ext
E-mail: $staff_email
Dept: $staff_dept
For Program: $staff_program
Budget Auth: $staff_ba
Budget Code: $staff_budget_code

Special Instructions:
$special_instructions

Special notifications:
 - If at SEDL: $info_availability_atsedl
 - If at Library: $info_availability_library
 - If ordered, after cataloging, please route: $info_availability_routing

Routing: $routing
 - User: $routing_individual
 - Instructions: $routing_instructions


EOM


	if (($format eq 'Book') || ($format eq 'Audiovisual')) {
print NOTIFY <<EOM;
$format Description:
 - Title: $book_title
 - Author: $book_author
 - Format: $book_format $book_other
 - Publisher: $book_publisher
 - Pub. Date: $book_pubdate
 - ISBN: $book_isbn
 - Edition: $book_edition
 - Price per copy: $book_price_copy
 - Number of Copies: $book_num_copies
 - Total Amount: $book_total_amount

EOM
	}

	if ($format =~ 'Journal') {
print NOTIFY <<EOM;
$format Description:
 - Title: $j_title
 - Publisher: $j_publisher
 - Format: $j_format
 - ISSN: $j_issn
 - Subscription/Issue Price: $j_subscrip_price
 - Issue Number: $j_issue_num

EOM
	}

	if ($format eq 'Other') {
print NOTIFY <<EOM;
OTHER Resource Description:
$other_description

EOM
	}

	if ($format eq 'Electronic Database') {
print NOTIFY <<EOM;
$format Description:
 - Title: $db_title
 - URL: $db_url
 - Price: $db_price
 - Authorized Users: $db_authusers

EOM
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
## START: LOCATION = SELECT_FORMAT
######################################################################################
if ($location =~ 'select_format') {

print <<EOM;
<h1 style="margin-top:0px;">Centralized Book, Journal, Audiovisual,<BR>
and Electronic Database Purchasing Service
EOM
print "<SPAN class=small>(Click here to <A HREF=\"resource_search.cgi?location=logout\">logout</A>)</SPAN>" if ($session_active eq 'yes');
print<<EOM;
</H1>
<H2 ALIGN=CENTER>Resource Search Request Form - Step 1 of 2</H2>
EOM

print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');

print<<EOM;
<P>
Please complete and submit this form if you would like to request the purchase of a book, 
journal, audiovisual, or electronic database which SEDL does not own. 
<P>
Before filling out this form, check the 
<A HREF="http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?page=irc_catalog">Information Resource Center (IRC) Library Catalog</A> 
to see if the item has been entered and is available for checkout. If you find the 
item in the online catalog, contact Nancy Reynolds in person, by 
<A HREF="mailto:nancy.reynolds\@sedl.org">e-mail</A>, or at ext. 6548 to 
check out the resource instead of using this form. If you still 
want to purchase an item, please fill out this form as completely 
as possible and click the "send request" button at the end of this page.
<form action="resource_search.cgi" method=GET>

<P>
<TABLE CELLPADDING=4 CELLSPACING=0 BORDER=1>
<TR><TD VALIGN="TOP">
<H2>Describe the Item You are Searching For</H2>
	<UL>
EOM
my @formats = ("Book", "Journal Issue", "Journal Subscription", "Audiovisual", "Electronic Database", "Other");
my $num_formats = $#formats;
my $format_loop_counter = "0";
	while ($format_loop_counter <= $num_formats) {
		print "<INPUT TYPE=\"RADIO\" NAME=\"format\" id=\"format_$format_loop_counter\" VALUE=\"$formats[$format_loop_counter]\" ";
			if ($formats[$format_loop_counter] eq $format) {
				print "CHECKED";
			}
		print "><label for=\"format_$format_loop_counter\">$formats[$format_loop_counter]</label><BR>";
		$format_loop_counter++;
	}

print "</UL>";
 
	if ($session_active ne 'yes') {
print<<EOM;
<H2>Your Information </H2>
<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0>
  <TR><TD VALIGN="TOP" WIDTH=250><strong><label for="logon_user">Your intranet ID</label></strong><BR>
  			(ex: whoover)</TD>
      <TD WIDTH=420 VALIGN="TOP">
      <INPUT TYPE="text" NAME="logon_user" id="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id">
      </TD></TR>
  <TR><TD VALIGN="TOP" WIDTH="150"><strong><label for="logon_pass">Password</label></strong></TD>
      <TD WIDTH="420" VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" ID="logon_pass" SIZE="8"></TD></TR>
</TABLE>
EOM
	}
print<<EOM;
  	<div style="margin-left:25px;">
	<input type="hidden" name="location" value="showform">
	<input type="submit" name="submit" value="Click to Proceed to Step 2">
	</form>
    </div>       

</TD></TR></TABLE>     

EOM
}
######################################################################################
## END: LOCATION = SELECT_FORMAT
######################################################################################






######################################################################################
## START: LOCATION = SHOWFORM
######################################################################################
if ($location eq 'showform') {

print <<EOM;
<h1 style="margin-top:0px;">Centralized Book, Journal, Audiovisual,<BR>
and Electronic Database Purchasing Service<P>
Resource Search Request Form - Step 2 of 2</h1>
<P>
<form action="resource_search.cgi" method=GET>

<P>
<TABLE CELLPADDING=4 CELLSPACING=0 BORDER=1>
<TR><TD VALIGN="TOP">
EOM

	if (($format eq 'Book') || ($format eq 'Audiovisual')) {
print<<EOM;
<H2>Information about the Book or Audiovisual:</H2>
	<UL>
	<TABLE>
	<TR><TD VALIGN="TOP"><label for="book_title">Title:</label></TD>
		<TD VALIGN="TOP"><input name="book_title" id="book_title" size="50"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="book_author">Author:</label></TD>
		<TD VALIGN="TOP"><input name="book_author" id="book_author" size="50"></TD></TR>
	<TR><TD VALIGN="TOP">Format: </TD>
		<TD VALIGN="TOP"><input TYPE="radio" name="book_format" id="book_format1" value="Print"><label for="book_format1">Print</label><BR>
						<input TYPE="radio" name="book_format" id="book_format2" value="Electronic"><label for="book_format2">Electronic</label><BR>
						<input TYPE="radio" name="book_format" id="book_format3" value="Videotape"><label for="book_format3">Videotape</label><BR>
						<input TYPE="radio" name="book_format" id="book_format4" value="Audiocassette"><label for="book_format4">Audiocassette</label><BR>
						<input TYPE="radio" name="book_format" id="book_format5" value="CD-ROM"><label for="book_format5">CD-ROM</label><BR>
						<input TYPE="radio" name="book_format" id="book_format6" value="Other"><label for="book_format6">Other:</label>  
									<input name="book_other" size="40"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="book_publisher">Publisher/Producer:</label></TD>
		<TD VALIGN="TOP"><input name="book_publisher" id="book_publisher" size="50"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="book_pubdate">Publication Date:</label></TD>
		<TD VALIGN="TOP"><input name="book_pubdate" id="book_pubdate" size="30"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="book_isbn">ISBN:</label></TD>
		<TD VALIGN="TOP"><input name="book_isbn" id="book_isbn" size="30"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="book_edition">Edition:</label></TD>
		<TD VALIGN="TOP"><input name="book_edition" id="book_edition" size="30"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="book_price_copy">Price per Copy:</label></TD>
		<TD VALIGN="TOP"><input name="book_price_copy" id="book_price_copy" size="30"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="book_num_copies">No. of Copies:</label></TD>
		<TD VALIGN="TOP"><input name="book_num_copies" id="book_num_copies" size="10"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="book_total_amount">Total Estimated Amount:</label></TD>
		<TD VALIGN="TOP"><input name="book_total_amount" id="book_total_amount" size="50"></TD></TR>
	</TABLE>
	</UL>
EOM
	}

	if ($format =~ 'Journal') {
print<<EOM;
<H2>Information about the Journal Subscription or Journal Issue:</H2>
	<UL>
	<TABLE>
	<TR><TD VALIGN="TOP"><label for="j_title">Journal Title:</label></TD>
		<TD VALIGN="TOP"><input name="j_title" id="j_title" size="50"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="j_publisher">Publisher:</label></TD>
		<TD VALIGN="TOP"><input name="j_publisher" id="j_publisher" size="50"></TD></TR>
	<TR><TD VALIGN="TOP">Format:</TD>
		<TD VALIGN="TOP"><input TYPE="radio" name="j_format" id="j_formatp" value="Print"><label for="j_formatp">Print</label><BR>
						<input TYPE="radio" name="j_format" id="j_formate" value="Electronic"><label for="j_formate">Electronic</label></TD></TR>
	<TR><TD VALIGN="TOP"><label for="j_issn">ISSN:</label></TD>
		<TD VALIGN="TOP"><input name="j_issn" id="j_issn" size="50"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="j_subscrip_price">Institutional Subscription/Issue Price:</label></TD>
		<TD VALIGN="TOP"><input name="j_subscrip_price" id="j_subscrip_price" size="50"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="j_issue_num">If specific issue is needed,<BR>
					enter date, volume no.<BR>
					and issue no.:</label></TD>
		<TD VALIGN="TOP"><input name="j_issue_num" id="j_issue_num" size="50"></TD></TR>
	</TABLE>
	</UL>
EOM
	}
	
	if ($format eq 'Other') {
print<<EOM;
<H2>Information about the Resource:</H2>
	<UL>
	<TABLE>
	<TR><TD VALIGN="TOP"><label for="other_description">Description:</label></TD>
		<TD VALIGN="TOP"><textarea name="other_description" id="other_description" rows="6" cols="48"></textarea></TD></TR>
	</TABLE>
	</UL>

EOM
	}


	if ($format eq 'Electronic Database') {
print<<EOM;
<H2>Information about the Electronic Database:</H2>
	<UL>
	<TABLE>
	<TR><TD VALIGN="TOP"><label for="db_title">Title:</label></TD>
		<TD VALIGN="TOP"><input name="db_title" id="db_title" size="50"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="db_url">Producer / Web site:</label></TD>
		<TD VALIGN="TOP"><input name="db_url" id="db_url" size="50"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="db_price">Institutional Subscription Price:</label></TD>
		<TD VALIGN="TOP"><input name="db_price" id="db_price" size="50"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="db_authusers">Number/names of authorized users:</label></TD>
		<TD VALIGN="TOP"><input name="db_authusers" id="db_authusers" size="50"></TD></TR>
	</TABLE>
	</UL>

EOM
	}

my $staff_pull_menu = "<SELECT NAME=\"routing_individual\" id=\"routing_individual\">\n<OPTION VALUE=\"\">(select a user)</OPTION>\n";

## QUERY STAF PROFILES DB FOR OTHER E-MAIL ADDRESSES
my $command = "select firstname, lastname, phone, userid, email, phoneext, department_abbrev, supervised_by
				from staff_profiles order by lastname";
#				from staff_profiles where userid LIKE '$logon_user'";
#print "<P>COMMAND: $command";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

	while (my @arr = $sth->fetchrow) {
    	my ($firstname, $lastname, $phone, $userid, $email, $phoneext, $department_abbrev, $supervised_by) = @arr;
		 $firstname = &cleanaccents2html($firstname);
		 $lastname = &cleanaccents2html($lastname);

		 $staff_pull_menu .= "<OPTION VALUE=\"$userid\">$lastname, $firstname</OPTION>\n";
		 
			if ($userid eq $logon_user) {
				$staff_ba = $supervised_by if ($staff_ba eq '');
				$staff_dept = $department_abbrev if ($staff_dept eq '');
				$staff_program = $department_abbrev if ($staff_program eq '');
				$staff_name = "$firstname $lastname" if ($staff_name eq '');
				$staff_ext = $phoneext if ($staff_ext eq '');
				$staff_email = $email if ($staff_email eq '');

print<<EOM;
<H2>Your Information:</H2>
	<UL>
	<TABLE>
	<TR><TD VALIGN="TOP"><label for="staff_name">Name:</label></TD>
		<TD VALIGN="TOP"><input name="staff_name" id="staff_name" size="50" VALUE = "$staff_name"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="staff_ext">Phone Ext.:</label></TD>
		<TD VALIGN="TOP"><input name="staff_ext" id="staff_ext" size="50" VALUE="$staff_ext"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="staff_email">E-mail:</label></TD>
		<TD VALIGN="TOP"><input name="staff_email" id="staff_email" size="50" VALUE="$staff_email"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="staff_dept">Department:</label></TD>
		<TD VALIGN="TOP"><input name="staff_dept" id="staff_dept" size="50" VALUE="$staff_dept"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="staff_program">Program Needing<BR>
						the Resource:</label></TD>
		<TD VALIGN="TOP"><input name="staff_program" id="staff_program" size="50" VALUE="$staff_program"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="staff_ba">Budget Authority:</label></TD>
		<TD VALIGN="TOP"><input name="staff_ba" id="staff_ba" size="50" VALUE="$staff_ba"></TD></TR>
	<TR><TD VALIGN="TOP">B<label for="staff_budget_code">udget Code:</label></TD>
		<TD VALIGN="TOP"><input name="staff_budget_code" id="staff_budget_code" size="50" VALUE="$staff_budget_code"></TD></TR>
	</TABLE>
	</UL>
EOM
			} # END IF
	} # END DB QUERY LOOP
	$staff_pull_menu .= "</SELECT>\n";
	
print<<EOM;	
<H2>Notifications:</H2>
	<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="0">
	<TR><TD VALIGN="TOP"><input TYPE="checkbox" name="info_availability_atsedl" id="info_availability_atsedl" value="User wants notification if available at SEDL" CHECKED></TD>
		<TD VALIGN="TOP"><label for="info_availability_atsedl"><strong>Already Available:</strong> Let me know if resource requested is already available at SEDL 
			(but may not be listed in the online catalog).</label></TD></TR>
	<TR><TD VALIGN="TOP"><input TYPE="checkbox" name="info_availability_library" id="info_availability_library" value="User wants notification if available to borrow" CHECKED></TD>
		<TD VALIGN="TOP"><label for="info_availability_library"><strong>Other Access Options:</strong> If resource is not available at SEDL, let me know if it can be accessed locally instead of
			purchasing a licensed subscription at SEDL.</label></TD></TR>
	<TR><TD VALIGN="TOP"><input TYPE="checkbox" name="info_availability_routing" id="info_availability_routing" value="User wants notification if available to borrow" CHECKED></TD>
		<TD VALIGN="TOP"><label for="info_availability_routing"><strong>Routing Instructions:</strong> If SEDL orders the resource for me, after being cataloged by IRC staff, please</label> 
			<UL>
			<INPUT TYPE="RADIO" name="routing" id="routing1" VALUE="to staff member"> <label for="routing1">check out the resource to </label>
			$staff_pull_menu
			<P>
			or
			<P>
			<INPUT TYPE="RADIO" name="routing" id="routing2" VALUE="by directions"> <label for="routing2">process it as follows:</label><BR>
			<textarea name="routing_instructions" id="routing_instructions" rows="3" cols="40"></textarea>
			</TD></TR>
	</TABLE>

<P>

<H2>Special instructions, comments, or requests:</H2>
<textarea name="special_instructions" rows=6 cols=48></textarea>
<P>

	<UL>
	<input type="hidden" name="format" value="$format">
	<input type="hidden" name="location" value="enterrecord">
	<input type="submit" name="submit" value="Send Request">
	</form>
    </UL>       

</TD></TR></TABLE>     

EOM
}
######################################################################################
## END: LOCATION = SHOWFORM
######################################################################################




################################
## START: PRINT PAGE FOOTER
################################
print<<EOM;
$htmltail
EOM
################################
## END: PRINT PAGE FOOTER
################################






####################################################################
##  HERE ARE SOME SUBROUTINES USED BY THIS DATABASE SEARCH SCRIPT ##
####################################################################

## THIS SUBROUTINE MAKES SURE ANY SPECIAL CHARACTERS ARE BACKSLASHED BEFORE SENDING A COMMAND TO MYSQL
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
	$cleanitem =~ s/À/&iquest\;/g; 
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

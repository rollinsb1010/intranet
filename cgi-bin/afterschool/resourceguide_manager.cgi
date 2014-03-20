#!/usr/bin/perl

#####################################################################################################
# Copyright 2007 by Southwest Educational Development Laboratory
#
# This script is used by Afterschool to manage the online database of lesson plans
# Written by Brian Litke 05-09-2007
#####################################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=publications;host=localhost";
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


##########################################
# START: CONFIGURATION VARIABLES
##########################################
my $item_label = "Resource";
my $site_label = "Resoource Guide for Planning and Operating Afterschool Programs";
my $public_site_address = "http://www.sedl.org/pubs/fam95/";
my $mysql_db_table_name = "afterschoolguide2008";
my $script_name = "resourceguide_manager.cgi";
##########################################
# END: CONFIGURATION VARIABLES
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
	my $current_year = POSIX::strftime('%Y', localtime(time)); # Locale's year (e.g. 2008)
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

	my $timestamp = "$current_year$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; # 14-digit timestamp (e.g. 20080306143938)

my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;
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

my $validuser = "no";

my $uniqueid = param('uniqueid');

my $location = param('location');
   $location = "menu" if $location eq '';

my $showsession = param('showsession');


my $error_message = "";
my $feedback_message = "";
my $show_record = $query->param("show_record");
my $show_subject = $query->param("show_subject");
my $searchfor = $query->param("searchfor");
my $sortby = $query->param("sortby");
   $sortby = "dateadded" if ($sortby eq '');
my @topics = ("Management", "Communication", "Programming", "Integrating K-12 and Afterschool Programs", "Community Building/Collaboration", "Evaluation");
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
my $validuser = "no";
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
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
#my $num_matches = $sth->rows;

				$validuser = "yes" if ($ss_staff_id eq 'blitke');
				$validuser = "yes" if ($ss_staff_id eq 'cjordan');
				$validuser = "yes" if ($ss_staff_id eq 'jwackwit');
				$validuser = "yes" if ($ss_staff_id eq 'lshankla');
				$validuser = "yes" if ($ss_staff_id eq 'lwood');
				$validuser = "yes" if ($ss_staff_id eq 'nreynold');
				$validuser = "yes" if ($ss_staff_id eq 'sabdulla');
		
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
#	$error_message = "<FONT COLOR=ORANGE>ACCESS DENIED: You are not authorized to access the $site_label Manager. Please contact Brian Litke at ext. 260 for assistance accessing this resource.</FONT>";
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
<TITLE>SEDL Intranet | $site_label Manager</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead

<h1>$site_label Manager</h1>

EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>
Welcome to the $site_label Manager. This database is used by AFC and Communications staff 
to add/edit the <a href="$public_site_address">$site_label</a> for the SEDL Web site. 
Please enter your SEDL user ID and password to view the database.
</p>
<FORM ACTION="$script_name" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP"><strong>Your user ID</strong><br>
	  (ex: sliberty)</TD>
    <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
<TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR>
			<SPAN class="small">(not your e-mail password)</SPAN></TD>
    <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD></TR>
</TABLE>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME=location VALUE=process_logon>
  <INPUT TYPE="SUBMIT" VALUE="Log In Now">
  </div>
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


###########################################################
### START: LOCATION PROCESS_DELETE_lp
###########################################################
#if ($location eq 'process_delete_lp') {
#	my $confirm = $query->param("confirm");
#	if ($confirm eq 'confirmed') {
#	## START: BACKSLASH VARIABLES FOR DB
#	$show_record = &commoncode::cleanthisfordb($show_record);
#	## END: BACKSLASH VARIABLES FOR DB
#
#		## DELETE THE PAGES
#		my $command_delete_pub = "DELETE from $mysql_db_table_name WHERE resource_id = '$show_record'";
#		my $dbh = DBI->connect($dsn, "pubsuser", "sedlpubs");
#		my $sth = $dbh->prepare($command_delete_pub) or die "Couldn't prepare statement: " . $dbh->errstr;
#		$sth->execute;
##my $num_matches = $sth->rows;
#		$feedback_message = "You successfully deleted $item_label record \#$show_record.";
#		$location = "menu";
#	} else {
#		$error_message = "ERROR: $item_label Deletion Aborted. You forgot to checkmark the CONFIRM box.";
#		$location = "add_item";
#	}
#}
###########################################################
### END: LOCATION PROCESS_DELETE_lp
###########################################################



#################################################################################
## START: LOCATION = PROCESS_add_item
#################################################################################
	## START: READ NEW VALUES FOR LP RECORD
	my $new_resource_id = $query->param("new_resource_id");
	my $new_date_created = ""; 
	my $new_resource_type_0 = $query->param("new_resource_type_0"); 
	my $new_resource_type_1 = $query->param("new_resource_type_1"); 
	my $new_resource_type_2 = $query->param("new_resource_type_2"); 
	my $new_resource_type_3 = $query->param("new_resource_type_3"); 
	my $new_resource_type_4 = $query->param("new_resource_type_4"); 
	my $new_resource_type_5 = $query->param("new_resource_type_5"); 
	my $new_resource_type = "";
	   $new_resource_type .= "$new_resource_type_0\t" if ($new_resource_type_0 ne '');
	   $new_resource_type .= "$new_resource_type_1\t" if ($new_resource_type_1 ne '');
	   $new_resource_type .= "$new_resource_type_2\t" if ($new_resource_type_2 ne '');
	   $new_resource_type .= "$new_resource_type_3\t" if ($new_resource_type_3 ne '');
	   $new_resource_type .= "$new_resource_type_4\t" if ($new_resource_type_4 ne '');
	   $new_resource_type .= "$new_resource_type_5\t" if ($new_resource_type_5 ne '');
	my $new_title = $query->param("new_title"); 
	my $new_author = $query->param("new_author"); 
	my $new_annotation = $query->param("new_annotation"); 
	my $new_year = $query->param("new_year"); 
	my $new_extent = $query->param("new_extent"); 
	my $new_sale_format1 = $query->param("new_sale_format1"); 
	my $new_sale_price1 = $query->param("new_sale_price1"); 
	my $new_sale_format2 = $query->param("new_sale_format2"); 
	my $new_sale_price2 = $query->param("new_sale_price2"); 
	my $new_sale_format3 = $query->param("new_sale_format3"); 
	my $new_sale_price3 = $query->param("new_sale_price3"); 
	my $new_sale_format4 = $query->param("new_sale_format4"); 
	my $new_sale_price4 = $query->param("new_sale_price4"); 
	my $new_web_format = $query->param("new_web_format"); 
	my $new_web_url = $query->param("new_web_url"); 
	my $new_web_format2 = $query->param("new_web_format2"); 
	my $new_web_url2 = $query->param("new_web_url2"); 
	my $new_publisher = $query->param("new_publisher"); 
	my $new_p_add1 = $query->param("new_p_add1"); 
	my $new_p_add2 = $query->param("new_p_add2"); 
	my $new_p_add3 = $query->param("new_p_add3"); 
	my $new_p_city = $query->param("new_p_city"); 
	my $new_p_state = $query->param("new_p_state"); 
	my $new_p_zip = $query->param("new_p_zip"); 
	my $new_p_phone = $query->param("new_p_phone"); 
	my $new_p_fax = $query->param("new_p_fax"); 
	my $new_p_email = $query->param("new_p_email"); 
	my $new_p_website = $query->param("new_p_website"); 
	my $new_new_or_revised = $query->param("new_new_or_revised"); 
	my $new_show_on_site = $query->param("new_show_on_site"); 
	## END: READ NEW VALUES FOR LP RECORD


if ($location eq 'process_add_item') {
	## START: CHECK FOR DATA COPLETENESS
	if (($new_title eq '') || ($new_annotation eq '')) {
		$error_message .= "The $item_label title and/or annotation are missing. Please try again.";
		$location = "add_item";
	}
	## START: CHECK FOR DATA COPLETENESS
}

if ($location eq 'process_add_item') {

	## START: BACKSLASH VARIABLES FOR DB
	$new_resource_id = &commoncode::cleanthisfordb($new_resource_id);
	$new_resource_type = &commoncode::cleanthisfordb($new_resource_type); 
	$new_title = &commoncode::cleanthisfordb($new_title); 
	$new_author = &commoncode::cleanthisfordb($new_author); 
	$new_annotation = &commoncode::cleanthisfordb($new_annotation); 
	$new_year = &commoncode::cleanthisfordb($new_year); 
	$new_extent = &commoncode::cleanthisfordb($new_extent); 
	$new_sale_format1 = &commoncode::cleanthisfordb($new_sale_format1); 
	$new_sale_price1 = &commoncode::cleanthisfordb($new_sale_price1); 
	$new_sale_format2 = &commoncode::cleanthisfordb($new_sale_format2); 
	$new_sale_price2 = &commoncode::cleanthisfordb($new_sale_price2); 
	$new_sale_format3 = &commoncode::cleanthisfordb($new_sale_format3); 
	$new_sale_price3 = &commoncode::cleanthisfordb($new_sale_price3); 
	$new_sale_format4 = &commoncode::cleanthisfordb($new_sale_format4); 
	$new_sale_price4 = &commoncode::cleanthisfordb($new_sale_price4); 
	$new_web_format = &commoncode::cleanthisfordb($new_web_format); 
	$new_web_url = &commoncode::cleanthisfordb($new_web_url); 
	$new_web_format2 = &commoncode::cleanthisfordb($new_web_format2); 
	$new_web_url2 = &commoncode::cleanthisfordb($new_web_url2); 
	$new_publisher = &commoncode::cleanthisfordb($new_publisher); 
	$new_p_add1 = &commoncode::cleanthisfordb($new_p_add1); 
	$new_p_add2 = &commoncode::cleanthisfordb($new_p_add2); 
	$new_p_add3 = &commoncode::cleanthisfordb($new_p_add3); 
	$new_p_city = &commoncode::cleanthisfordb($new_p_city); 
	$new_p_state = &commoncode::cleanthisfordb($new_p_state); 
	$new_p_zip = &commoncode::cleanthisfordb($new_p_zip); 
	$new_p_phone = &commoncode::cleanthisfordb($new_p_phone); 
	$new_p_fax = &commoncode::cleanthisfordb($new_p_fax); 
	$new_p_email = &commoncode::cleanthisfordb($new_p_email); 
	$new_p_website = &commoncode::cleanthisfordb($new_p_website); 
	$new_new_or_revised = &commoncode::cleanthisfordb($new_new_or_revised); 
	$new_show_on_site = &commoncode::cleanthisfordb($new_show_on_site); 
	## END: BACKSLASH VARIABLES FOR DB


	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select resource_id from $mysql_db_table_name ";
			if ($show_record ne '') {
				$command .= "WHERE resource_id = '$show_record'";
			}
		my $dbh = DBI->connect($dsn, "pubsuser", "sedlpubs");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;
		$already_exists = "yes" if (($num_matches_code eq '1') && ($show_record ne ''));
		
		my $add_edit_type = "added"; # DEFAULT SETTING
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update_lp = "UPDATE $mysql_db_table_name SET 
	resource_type = '$new_resource_type',  
	title = '$new_title',  
	author = '$new_author',  
	annotation = '$new_annotation',  
	year = '$new_year',  
	extent = '$new_extent',  
	sale_format1 = '$new_sale_format1',  
	sale_price1 = '$new_sale_price1',  
	sale_format2 = '$new_sale_format2',  
	sale_price2 = '$new_sale_price2',  
	sale_format3 = '$new_sale_format3',  
	sale_price3 = '$new_sale_price3',  
	sale_format4 = '$new_sale_format4',  
	sale_price4 = '$new_sale_price4',  
	web_format = '$new_web_format',  
	web_url = '$new_web_url',  
	web_format2 = '$new_web_format2',  
	web_url2 = '$new_web_url2',  
	publisher = '$new_publisher',  
	p_add1 = '$new_p_add1',  
	p_add2 = '$new_p_add2',  
	p_add3 = '$new_p_add3',  
	p_city = '$new_p_city',  
	p_state = '$new_p_state',  
	p_zip = '$new_p_zip',  
	p_phone = '$new_p_phone',  
	p_fax = '$new_p_fax',  
	p_email = '$new_p_email',  
	p_website = '$new_p_website',  
	new_or_revised = '$new_new_or_revised',  
	show_on_site = '$new_show_on_site',  
	date_edited = '$timestamp'
WHERE resource_id ='$show_record'";

			my $dbh = DBI->connect($dsn, "pubsuser", "sedlpubs");
			my $sth = $dbh->prepare($command_update_lp) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			$add_edit_type = "edited";
			$feedback_message .= "The $item_label was $add_edit_type successfully";
			$feedback_message .= " and is highlighted in <a href=\"#$show_record\">YELLOW below</a>." if ($add_edit_type eq 'edited');
#			$error_message .= "</font>";
			$location = "menu";
		} else {
	
			my $command_insert_lp = "INSERT INTO $mysql_db_table_name VALUES ('', '', '$new_resource_type', '$new_title', '$new_author', '$new_annotation', '$new_year', '$new_extent', '$new_sale_format1', '$new_sale_price1', '$new_sale_format2', '$new_sale_price2', '$new_sale_format3', '$new_sale_price3', '$new_sale_format4', '$new_sale_price4', '$new_web_format', '$new_web_url', '$new_web_format2', '$new_web_url2', '$new_publisher', '$new_p_add1', '$new_p_add2', '$new_p_add3', '$new_p_city', '$new_p_state', '$new_p_zip', '$new_p_phone', '$new_p_fax', '$new_p_email', '$new_p_website', '$new_new_or_revised', '$new_show_on_site', '$timestamp', '$timestamp')";





			my $dbh = DBI->connect($dsn, "pubsuser", "sedlpubs");
			my $sth = $dbh->prepare($command_insert_lp) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
#			my $num_matches = $sth->rows;
			
			my $inserted_record_id = "";
			# START: GRAB THE LST INSERTED RECORD ID
			my $command_lastid = "SELECT last_insert_id() from $mysql_db_table_name"; 
			my $dbh = DBI->connect($dsn, "pubsuser", "sedlpubs");
			my $sth = $dbh->prepare($command_lastid) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
#			my $num_matches = $sth->rows;
			
				while (my @arr = $sth->fetchrow) {
		    		my ($last_id_inserted) = @arr;
					$show_record = $last_id_inserted;
				}
			# END: GRAB THE LST INSERTED RECORD ID

			$feedback_message .= "The $item_label was $add_edit_type successfully and is highlighted in <a href=\"#$show_record\">YELLOW below</a>.";
			$location = "menu";
		} # END IF USER NAME NOT BLANK

}
#################################################################################
## END: LOCATION = PROCESS_add_item
#################################################################################


#################################################################################
## START: LOCATION = add_item
#################################################################################
if ($location eq 'add_item') {
	my $page_title = "Add a New $item_label";

my $resource_id = "";
my $date_created = ""; 
my $resource_type = ""; 
my $title = ""; 
my $author = ""; 
my $annotation = ""; 
my $year = ""; 
my $extent = ""; 
my $sale_format1 = ""; 
my $sale_price1 = ""; 
my $sale_format2 = ""; 
my $sale_price2 = ""; 
my $sale_format3 = ""; 
my $sale_price3 = ""; 
my $sale_format4 = ""; 
my $sale_price4 = ""; 
my $web_format = ""; 
my $web_url = ""; 
my $web_format2 = ""; 
my $web_url2 = ""; 
my $publisher = ""; 
my $p_add1 = ""; 
my $p_add2 = ""; 
my $p_add3 = ""; 
my $p_city = ""; 
my $p_state = ""; 
my $p_zip = ""; 
my $p_phone = ""; 
my $p_fax = ""; 
my $p_email = ""; 
my $p_website = ""; 
my $new_or_revised = ""; 
my $show_on_site = ""; 
my $date_added = ""; 
my $date_edited = "";

print header;
	
	if ($show_record ne '') {
		$page_title = "Edit the $item_label";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from $mysql_db_table_name WHERE resource_id = '$show_record'";
		my $dbh = DBI->connect($dsn, "pubsuser", "sedlpubs");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_pubs = $sth->rows;
		$feedback_message = "Found $num_matches_pubs match.";
		while (my @arr = $sth->fetchrow) {
		($resource_id, $date_created, 
			$resource_type, $title, $author, $annotation, $year, $extent, 
			$sale_format1, $sale_price1, $sale_format2, $sale_price2, $sale_format3, $sale_price3, $sale_format4, $sale_price4, 
			$web_format, $web_url, $web_format2, $web_url2, 
			$publisher, $p_add1, $p_add2, $p_add3, $p_city, $p_state, $p_zip, $p_phone, $p_fax, $p_email, $p_website, 
			$new_or_revised, $show_on_site, $date_added, $date_edited) = @arr;
		} # END DB QUERY LOOP
	}
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$title = &commoncode::cleanaccents2html($title);


## HELP PAGE FOR TinyMCE: http://www.sandiego.edu/webdev/coding/richcontent.php
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label Manager: $page_title</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">

<script language="javascript" type="text/javascript" src="/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">     
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	plugins : "spellchecker,table,paste",
	gecko_spellcheck : true,
	theme_advanced_buttons1_add : "pastetext,pasteword",
	theme_advanced_buttons3_add : "tablecontrols, spellchecker",
	table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
	table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
	table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
	table_cell_limit : 100,
	table_row_limit : 5,
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

$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td>

<h1><A HREF="$script_name\?show_subject=$show_subject">$site_label Manager</A><br>
$page_title</h1>
EOM
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print<<EOM;
<FORM ACTION="$script_name" METHOD="POST">

<TABLE border="1" cellpadding="2" cellspacing="0" width="100%" bgcolor="#E3F7AB">
<tr><td valign="top"><strong><label for="new_show_on_site">Show on site</label></strong></td>
	<td valign="top">
EOM
		&commoncode::printform_yes_no_menu("new_show_on_site", $show_on_site);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong><label for="new_resource_type">Topic</label></strong></td>
	<td valign="top">
EOM
		&print_subject_menu("new_resource_type", $resource_type);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong><label for="new_title">Title for $item_label</label></strong></td>
	<td valign="top"><textarea name="new_title" id="new_title" rows="14" cols="70">$title</textarea>
	</td></tr>

<tr><td valign="top"><strong>$item_label Annotation</strong><p><strong>Tip:</strong><br>Shift-return will create a single line carriage return.</p><p>Use the "HTML" button to edit the HTML source code.</p></td>
	<td valign="top"><textarea name="new_annotation" id="new_annotation" rows="20" cols="70">$annotation</textarea></td>
</tr>
<tr><td valign="top"><strong>Author:</strong></td>
	<td valign="top"><input type="text" name="new_author" id="new_author" value="$author" size="40"></td>
</tr>
<tr><td valign="top"><strong>Extent:</strong></td>
	<td valign="top"><input type="text" name="new_extent" id="new_extent" value="$extent"></td>
</tr>
<tr><td valign="top"><strong>Year:</strong></td>
	<td valign="top">
EOM
&commoncode::print_year_menu("new_year", 1990, $current_year + 1, $year);
print<<EOM;
	</td>
</tr>
<tr><td valign="top"><strong>Fale Format #1:</strong></td>
	<td valign="top"><input type="text" name="new_sale_format1" id="new_sale_format1" value="$sale_format1">
						<br>Price: <input type="text" name="new_sale_price1" id="new_sale_price1" value="$sale_price1"></td>
</tr>
<tr><td valign="top"><strong>Fale Format #2:</strong></td>
	<td valign="top"><input type="text" name="new_sale_format2" id="new_sale_format2" value="$sale_format2">
						<br>Price: <input type="text" name="new_sale_price2" id="new_sale_price2" value="$sale_price2"></td>
</tr>
<tr><td valign="top"><strong>Fale Format #3:</strong></td>
	<td valign="top"><input type="text" name="new_sale_format3" id="new_sale_format3" value="$sale_format3">
						<br>Price: <input type="text" name="new_sale_price3" id="new_sale_price3" value="$sale_price3"></td>
</tr>
<tr><td valign="top"><strong>Fale Format #4:</strong></td>
	<td valign="top"><input type="text" name="new_sale_format4" id="new_sale_format4" value="$sale_format4">
						<br>Price: <input type="text" name="new_sale_price4" id="new_sale_price4" value="$sale_price4"></td>
</tr>

<tr><td valign="top"><strong>Web Format #1:</strong></td>
	<td valign="top"><input type="text" name="new_web_format" id="new_web_format" value="$web_format">
						<br>URL: <input type="text" name="new_web_url" id="new_web_url" value="$web_url"></td>
</tr>
<tr><td valign="top"><strong>Web Format #2:</strong></td>
	<td valign="top"><input type="text" name="new_web_format2" id="new_web_format2" value="$web_format2">
						<br>URL: <input type="text" name="new_web_url2" id="new_web_url2" value="$web_url2"></td>
</tr>
<tr><td valign="top"><strong>Publisher</strong></td>
	<td valign="top"><input type="text" name="new_publisher" id="new_publisher" value="$publisher">
						<br>Address line #1: <input type="text" name="new_p_add1" id="new_p_add1" value="$p_add1">
						<br>Address line #2: <input type="text" name="new_p_add2" id="new_p_add2" value="$p_add2">
						<br>Address line #3: <input type="text" name="new_p_add3" id="new_p_add3" value="$p_add3">
						<br>City: <input type="text" name="new_p_city" id="new_p_city" value="$p_city">
						<br>State: <input type="text" name="new_p_state" id="new_p_state" value="$p_state">
						<br>Zip: <input type="text" name="new_p_zip" id="new_p_zip" value="$p_zip">
						<br>Phone: <input type="text" name="new_p_phone" id="new_p_phone" value="$p_phone">
						<br>Fax: <input type="text" name="new_p_fax" id="new_p_fax" value="$p_fax">
						<br>E-mail: <input type="text" name="new_p_email" id="new_p_email" value="$p_email">
						<br>Web SIte: <input type="text" name="new_p_website" id="new_p_website" value="$p_website">
	</td>
</tr>
<tr><td valign="top"><strong><label for="new_new_or_revised">New?</label></strong></td>
	<td valign="top">
		<select name="new_new_or_revised" id="new_new_or_revised">
		<option></option>
		<option value="New"
EOM
	print " SELECTED" if ($new_or_revised eq 'New');
print<<EOM;
		>New</option>
		</select>
	</td></tr>
EOM
	if ($show_record ne '') {
print<<EOM;
<tr><td valign="top"><strong>Date Added:</strong></td>
	<td valign="top">
EOM
		my ($old_y, $old_m, $old_d) = split(/\-/,$date_added);
		&commoncode::print_month_menu("new_startdate_m", $old_m);
		&commoncode::print_day_menu("new_startdate_d", $old_d);
		&commoncode::print_year_menu("new_startdate_y", 2007, $year + 1, $old_y);

print<<EOM;
	</td></tr>
EOM
	}

#Unused variables in the database but not editable on the form so far (let me know if these are useful - they came from a sample Toolkit $item_label):
#<ul><font color=red>
#	Attachments: $lp_attachments<br></font>
#</ul>

print<<EOM;
</table>
<p></p>
	<div style="margin-left:25px;">
		<INPUT TYPE="HIDDEN" NAME="show_subject" VALUE="$show_subject">
		<INPUT TYPE="HIDDEN" NAME="show_record" VALUE="$show_record">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_add_item">
	<INPUT TYPE="SUBMIT" VALUE="Click to Save ($page_title)">
	</div>
</form>
<p></p>




<div class="first fltRt">
		<FORM ACTION="$script_name" METHOD=POST>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr><td colspan="2"><em>Click here to delete this $item_label.</em></td></tr>
		<tr><td valign="top"><input type="checkbox" name="confirm" value="confirmed"></td>
			<td valign="top"><span style="color:red">confirm the deletion<br> of this $item_label.</span></td></tr>
		<tr><td colspan="2">
				<input type="hidden" name="location" value="process_delete_lp">
				<input type="hidden" name="show_record" value="$show_record">
				<input type="submit" name="submit" value="Delete $item_label"></td></tr>
				
		</table>
		</form>
	
</div>


</td>
	<td valign="top" align="right">
		(Click here to <A HREF="$script_name?location=logout">logout</A>)
		<P>
	</td></tr>
</table>


$htmltail
EOM
}
#################################################################################
## END: LOCATION = add_item
#################################################################################


#################################################################################
## START: LOCATION = MENU
#################################################################################
if ($location eq 'menu') {
	$sortby = "title" if ($sortby eq '');
## PRINT SIGNUP FORM
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label Manager: List of $item_label\s</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1><A HREF="$script_name\?show_subject=$show_subject">$site_label Manager</A>
		<br>List of $item_label\s</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="$script_name?location=logout">logout</A>)
	</td></tr>
</table>
EOM

#	if ($logonuser_is_afterschool_representative ne 'yes') {
#		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view $item_label.</FONT>";
#	}
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');

$searchfor = &commoncode::cleanthisfordb($searchfor);
$show_subject = &commoncode::cleanthisfordb($show_subject);

my $command = "select * from $mysql_db_table_name where resource_id like '%'";
#	$command .= " AND hiring_supervisor = '$new_hiring_supervisor'" if ($new_hiring_supervisor ne '');
#	$command .= " AND hiring_supervisor = '$cookie_ss_staff_id'" if ($logonuser_is_afterschool_representative ne 'yes');
	
#	$command .= " order by datestamp_created DESC" if (($sortby eq '') || ($sortby eq 'date'));
#	$command .= " order by applyfor_position, datestamp_created DESC" if ($sortby eq 'position');
#	$command .= " order by form_complete DESC, applyfor_position, datestamp_completed DESC" if ($sortby eq 'completed');
#	$command .= " order by name_l, name_f" if ($sortby eq 'applicant');
#	$command .= " order by hiring_supervisor, name_l, name_f" if ($sortby eq 'supervisor');
	$command .= " AND title like '%$searchfor%'" if ($searchfor ne '');
	$command .= " AND resource_type like '%$show_subject%'" if ($show_subject ne '');
	$command .= " order by title" if ($sortby eq 'title');
	$command .= " order by resource_type, title" if ($sortby eq 'subject');
	$command .= " order by date_added DESC, title" if ($sortby eq 'dateadded');

#print "<p class=\"info\">$command</p>";

my $dbh = DBI->connect($dsn, "pubsuser", "sedlpubs");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches_lp = $sth->rows;
#print "<p class=\"info\">MATCHES: $num_matches_lp</p>";

my $col_head_title = "<strong>Title</strong>";
   $col_head_title = "<a href=\"$script_name?location=menu&amp;sortby=title&amp;show_subject=$show_subject\">Title</a>" if ($sortby ne 'title');
my $col_head_resource_type = "<strong>Topic</strong>";
   $col_head_resource_type = "<a href=\"$script_name?location=menu&amp;sortby=subject&amp;show_subject=$show_subject\">Topic</a>" if ($sortby ne 'subject');
my $col_head_dateadded = "<strong>Added/Last Edited</strong>";
   $col_head_dateadded = "<a href=\"$script_name?location=menu&amp;sortby=dateadded&amp;show_subject=$show_subject\">Added/Last Edited</a>" if ($sortby ne 'dateadded');

print<<EOM;
<FORM ACTION="$script_name" METHOD="POST">
<p>
Displaying $num_matches_lp $item_label\s from 
<select name="show_subject">
EOM
my @subjects = ("", "Art", "Literacy", "Math", "Science", "Technology");
my @subjects_label = ("All Subjects", "Art", "Literacy", "Math", "Science", "Technology");
my $counter = 0;
	while ($counter <= $#subjects) {
		print "<option value=\"$subjects[$counter]\" ";
		print " SELECTED" if ($show_subject eq $subjects[$counter]);
		print">$subjects_label[$counter]</option>";
		$counter++;
	}
print<<EOM;
</select> whose title contains the word or phrase
	<input type="TEXT" name="searchfor" value="$searchfor" sixe="12">
  <INPUT TYPE="HIDDEN" NAME=location VALUE="">
  <INPUT TYPE="SUBMIT" VALUE="Refresh list">
</p>
</form>

<p>
Click here to <A HREF=\"$script_name?location=add_item&amp;show_subject=$show_subject\">Add a New $item_label</A>.
</p>
<TABLE border="1" cellpadding="2" cellspacing="0" width="100%">
EOM


	if ($num_matches_lp == 0) {
		print "<tr><td><p class=\"alert\">There are no $item_label\s in the database that match your search.</p></td></tr>";
	} else {
print<<EOM;
<TR bgcolor="#ebebeb">
	<td><strong>Show<br>on<br>site?</strong></td>
	<td>$col_head_title (click a title to edit the $item_label)</td>
	<td>$col_head_resource_type</td>
	<td align="center"><strong>Year</strong></td>
	<td align="center"><strong>Extent</strong></td>
	<td align="center">Annotation</td>
	<td>$col_head_dateadded</td>
</TR>
EOM
	}
my $counter = 1;
	while (my @arr = $sth->fetchrow) {
		my ($resource_id, $date_created, 
			$resource_type, $title, $author, $annotation, $year, $extent, 
			$sale_format1, $sale_price1, $sale_format2, $sale_price2, $sale_format3, $sale_price3, $sale_format4, $sale_price4, 
			$web_format, $web_url, $web_format2, $web_url2, 
			$publisher, $p_add1, $p_add2, $p_add3, $p_city, $p_state, $p_zip, $p_phone, $p_fax, $p_email, $p_website, 
			$new_or_revised, $show_on_site, $date_added, $date_edited) = @arr;

		my $bgcolor="";
  			$bgcolor="BGCOLOR=\"#FFFFCC\"" if ($show_record eq $resource_id);
 			$bgcolor="BGCOLOR=\"#FFCCCC\"" if ($show_on_site eq 'no');


		# TRANSFORM DATES INTO PRETTY FORMAT
		$date_added = &commoncode::date2standard($date_added);
		$date_added = "N/A" if ($date_added =~ '0000');
		$date_edited = &commoncode::convert_timestamp_2pretty_w_date($date_edited);
		$date_edited = "N/A" if ($date_edited =~ '0000');

		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$show_on_site = "<span style=\"color:#cc0000;\">NO</span>" if ($show_on_site eq 'no');
		$show_on_site = "yes" if ($show_on_site eq '');


print<<EOM;
<TR $bgcolor>
	<td valign="top">$show_on_site<br><font color="#999999">#$resource_id</font></td>
	<td valign="top"><a name="$resource_id"></a><A HREF=\"$script_name?location=add_item&amp;show_record=$resource_id\" TITLE="Click to edit this $item_label">$title</a><br><span style=\"color:#999999;\"><em>by: $author</em></span></td>
	<td valign="top">$resource_type</td>
	<td valign="top" align="center">$year</td>
	<td valign="top">$extent</td>
	<td valign="top">$annotation
EOM
#if ($annotation =~ '') {
#	$annotation =~ s// /gi;
#	$annotation = &commoncode::cleanthisfordb($annotation);

#	my $command_update = "UPDATE $mysql_db_table_name SET annotation = '$annotation' where resource_id = '$resource_id'";
#	my $dbh = DBI->connect($dsn, "pubsuser", "sedlpubs");
#	my $sth = $dbh->prepare($command_update) or die "Couldn't prepare statement: " . $dbh->errstr;
#	$sth->execute;
#	print " <p>NEED TO FIX ANNOTATION<br><br>$command_update</p>";
#}
print<<EOM;
	</td>
	<td valign="top">Added: $date_added<br>
		Edited: $date_edited</td>
</TR>
EOM
		$counter++;
	} # END DB QUERY LOOP

print<<EOM;
</TABLE>
<P>
The $site_label is located at <A HREF="$public_site_address">$public_site_address</A>.
</p>
<p>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
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




######################################
## START: SUBROUTINE print_grade_menu
######################################
sub print_grade_menu {
my $field_name = $_[0];
my $previous_selection = $_[1];
	my @grades_value = ("", "-1", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	my @grades_label = ("select a grade", "preK", "K", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	my $grade_counter = "0";
	my $count_total_grades = $#grades_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($grade_counter <= $count_total_grades) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $grades_value[$grade_counter]);
			print "<OPTION VALUE=\"$grades_value[$grade_counter]\" $selected>$grades_label[$grade_counter]</OPTION>\n";
			$grade_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_grade_menu
######################################


#################################################################
## START SUBROUTINE: print_subject_menu
#################################################################
sub print_subject_menu {
	my $form_variable_name = $_[0];
	my $preselected_value = $_[1];

	my @item_value = ("Management", "Communication", "Programming", "Integrating K-12 and Afterschool Programs", "Community Building/Collaboration", "Evaluation");
	my @item_label = ("Management", "Communication", "Programming", "Integrating K-12 and Afterschool Programs", "Community Building/Collaboration", "Evaluation");
	my $counter_options = "0";
		while ($counter_options <= $#item_value) {
			print "	<input type=\"checkbox\" name=\"$form_variable_name\_$counter_options\" id=\"$form_variable_name\_$counter_options\" value=\"$item_value[$counter_options]\" ";
			print " CHECKED" if ($preselected_value =~ $item_value[$counter_options]);
			print "><label for=\"$form_variable_name\_$counter_options\">$item_label[$counter_options]</label>";
			print "<br>";
			$counter_options++;
		}
}
#################################################################
## END SUBROUTINE: print_subject_menu
#################################################################


######################################
## START: SUBROUTINE print_number_menu
######################################
sub print_number_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10");
	my @item_label = ("", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection == $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE commoncode::print_month_menu
######################################

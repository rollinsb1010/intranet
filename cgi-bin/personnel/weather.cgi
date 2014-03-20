#!/usr/bin/perl

#####################################################################################################
# Copyright 2007 by Southwest Educational Development Laboratory
#
# This script is used by AS to manage online application submissions for employment
# Written by Brian Litke 01-09-2007
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
################################################
## END: LOAD PERL MODULES USED BY THIS SCRIPT
################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
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

##########################################
# START: CONFIGURATION VARIABLES
##########################################
my $item_label = "Inclement Weather SEDL Website Homepage Warning";
my $site_label = "Inclement Weather SEDL Website Homepage Warning";
my $public_site_address = "http://www.sedl.org/";
my $script_name = "weather.cgi";
##########################################
# END: CONFIGURATION VARIABLES
##########################################

  
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
my $sortby = $query->param("sortby");
my $confirm = $query->param("confirm");

my $new_warning = $query->param("new_warning");

########################################
## END: READ VARIABLES PASSED BY USER
########################################

   
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

	my $date_full_pretty_4digityear = "$month/$monthdate_wleadingzero/$year"; # Full date in human-readable format  (e.g. 03/06/08)

my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
	my $this_user_id = "$timestamp$ipnum$ipnum2";
	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################



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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("528"); # 528 is the PID for the "Weather Warning" page on the intranet

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
				my $dsn = "DBI:mysql:database=intranet;host=localhost";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
#my $num_matches = $sth->rows;
				$validuser = "yes" if ($ss_staff_id eq 'blitke');
				$validuser = "yes" if ($ss_staff_id eq 'brollins');
				$validuser = "yes" if ($ss_staff_id eq 'cmoses');
				$validuser = "yes" if ($ss_staff_id eq 'ewaters');
				$validuser = "yes" if ($ss_staff_id eq 'sabdulla');
				$validuser = "yes" if ($ss_staff_id eq 'sferguso');
				$validuser = "yes" if ($ss_staff_id eq 'whoover');
		
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
$htmlhead

<h1 style="margin-top:0px;">$site_label</h1>
<p>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>
This page is used by Stuart to update the Inclement Weather Warning to be displayed on the SEDL website home page.
</p>
<p>
Please enter your SEDL user ID and password to continue.
</p>

<FORM ACTION="weather.cgi" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP"><strong>Your user ID</strong><br />
  		  (ex: sferguso)</TD>
    <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
<TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR>
  			<SPAN class="small">(not your email password)</SPAN></TD>
    <TD WIDTH="420" VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD></TR>
</TABLE>

  <div style="margin-left:20px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Log In Now">
  </div>
</form>
<p>
To report troubles using this form, send an email to <A HREF="mailto:webmaster\@sedl.org">webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>

$htmltail
EOM
} 
#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################

######################################################
## START: LOCATION = PROCESS_WARNING
######################################################
if ($location eq 'process_warning') {
	if ($confirm eq '') {
		$error_message = "You forgot to click the \"confirm\" checkbox, so your changes were not saved.";
		$location = "menu";
	} # END IF
} # END IF

if ($location eq 'process_warning') {
	$new_warning =~ s/"//gi;
	my $new_warning_styled = "<div class='dottedBoxyw' style='margin-top:12px;'><strong><span style=\"font-size:16px;\">$new_warning</span></strong></div>";
	if ($new_warning eq '') {
		$feedback_message = "The warning message was set to BLANK, so NO WARNING should appear on the SEDL home page.";
		open(CURRENT_WARNING,">/home/httpd/html/common/includes/weather_warning_homepage_include.txt");
		print CURRENT_WARNING " ";
		close CURRENT_WARNING;

		open(CURRENT_WARNING_STYLED,">/home/httpd/html/common/includes/weather_warning_homepage_include_styled.txt");
		print CURRENT_WARNING_STYLED " ";
		close CURRENT_WARNING_STYLED;

	} else {
		$feedback_message = "The warning message was set to \"$new_warning\".";
		open(CURRENT_WARNING,">/home/httpd/html/common/includes/weather_warning_homepage_include.txt");
		print CURRENT_WARNING "$new_warning ";
		close CURRENT_WARNING;

		open(CURRENT_WARNING_STYLED,">/home/httpd/html/common/includes/weather_warning_homepage_include_styled.txt");
		print CURRENT_WARNING_STYLED "$new_warning_styled ";
		close CURRENT_WARNING_STYLED;
	}

	$location = "menu"; # AFTER LOGOUT, SHOW LOGON SCREEN
}
######################################################
## END: LOCATION = PROCESS_WARNING
######################################################


#################################################################################
## START: LOCATION = menu
#################################################################################
if ($location eq 'menu') {



print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | $site_label</TITLE>
$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1 style="margin-top:0px;"><A HREF="weather.cgi">$site_label</A></h1>
</td>
	<td valign="top" align="right">
		(Click here to <A HREF="weather.cgi?location=logout">logout</A>)
		<P>
	</td></tr>
</table>

EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

my $current_warning = "";
my $current_warning_styled = "";
open(CURRENT_WARNING,"</home/httpd/html/common/includes/weather_warning_homepage_include.txt");
while (<CURRENT_WARNING>) {
	$current_warning .= $_;
}
close(CURRENT_WARNING);
open(CURRENT_WARNING_STYLED,"</home/httpd/html/common/includes/weather_warning_homepage_include_styled.txt");
while (<CURRENT_WARNING_STYLED>) {
	$current_warning_styled .= $_;
}
close(CURRENT_WARNING_STYLED);

	if ( ($current_warning eq '') || (length($current_warning) < 5) ) {
		$current_warning = "";
		$current_warning_styled = "<span style=\"color:#666666;\">There is no warning message on the SEDL site at this time.</span>";
	} # END IF
print<<EOM;      

<FORM ACTION="weather.cgi" METHOD="POST">

<div>
CURRENT WEATHER WARNING ON SEDL WEBSITE:<br>
$current_warning_styled
</div>
<BR><BR>
<h2>Edit the Warning Message Using the Form Below</h2>
<TABLE border="0" cellpadding="2" cellspacing="0">
<tr><td valign="top"><strong>Warning Message</strong></td>
	<td valign="top" colspan="2"><textarea name="new_warning" id="new_warning" rows="5" cols="46">$current_warning</textarea><p>
	</td></tr>
<tr><td valign="top"></td>
	<td valign="top"><INPUT TYPE="CHECKBOX" name="confirm" id="confirm" value="yes"></td>
	<td valign="top"><label for="confirm">You must check this box to confirm the updating of the warning to be displayed on the SEDL website home page.</label></p>
	</td></tr>
</table>
	<div style="margin-left:25px;">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_warning">
		<INPUT TYPE="SUBMIT" VALUE="Submit">
	</div>
</form>

<p>
Example warning:<br>
SEDL's Austin Office will be closed today, January 1, 2014, due to inclement weather. 
</p>

$htmltail
EOM
}
#################################################################################
## END: LOCATION = menu
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



############################################
## START: SUBROUTINE printform_hiring_supervisor
############################################
sub printform_hiring_supervisor {
	my $form_variable_name = $_[0];
	my $selected_item = $_[1];
	my $prev_hiring_supervisor = $_[2];
	my $counter_item = "0";
	my @items = ("mboethel", "vdimock", "sferguso", "whoover", "rjarvis", "cjordan", "cmoses", "mvadenki", "jwestbro");
	my @items_label = ("Martha Boethel", "Vicki Dimock", "Stuart Ferguson", "Wes Hoover", "Robin Jarvis", "Cathy Jordan", "Chris Moses-Egan", "Michael Vaden-Kiernan", "John Westbrook");

	print "<select NAME=\"$form_variable_name\" id=\"$form_variable_name\"><OPTION VALUE=\"\">$prev_hiring_supervisor</OPTION>";
	while ($counter_item <= $#items) {
		if (
			(($items[$counter_item] ne 'cjordan') || (($items[$counter_item] eq 'cjordan') && ($prev_hiring_supervisor eq 'Cathy Jordan')) )
			) {
			print "<OPTION VALUE=\"$items[$counter_item]\"";
			print " SELECTED" if ($items[$counter_item] eq $selected_item);
			print ">$items_label[$counter_item]";
		}
		$counter_item++;
	} # END WHILE
	print "</select>";
} # END subroutine printform_frequency
############################################
## END: SUBROUTINE printform_hiring_supervisor
############################################




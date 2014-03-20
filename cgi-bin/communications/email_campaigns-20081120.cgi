#!/usr/bin/perl 

################################################################################
# Copyright 2004-2007 by Southwest Educational Development Laboratory
# Written by Brian Litke, SEDL Web Administrator (08-26-2004)
#
# 2007-03-15 Updated sendmail with -f flag to insert user name into "Return-Path"
#			 also changes X-Mailer to display as Apple Mail
# This script is used to send e-mail messages to a group of recipient addresses
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

#############################################
## START: LOAD PERL MODULES
#############################################
## THIS IS A PERL MODULE THAT FORMATS NUMBERS
use Number::Format;
	# EXAMPLE OF USAGE
	# my $this_number
	#	my $x = new Number::Format;
	#	$this_number = $x->format_number($this_number, 2, 2);

## THIS IS A PERL MODULE THAT CHECKS THE SYNTAX OF E-MAIL ADDRESSES
use Mail::CheckUser qw(check_email);
$Mail::CheckUser::Skip_Network_Checks = 1;
	# SAMPLE CHECK E-MAIL
	#my $this_email = "";
	#if(check_email($this_email)) {
	#}
#############################################
## END: LOAD PERL MODULES
#############################################

my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
my $query = new CGI;


##############################
## START: GRAB PAGE TEMPLATE #
##############################
my $htmlheader = "";  my $htmlfooter = "";

open(HTMLHEADER,"</home/httpd/html/staff/includes/header2012.txt");
while (<HTMLHEADER>) {
	$htmlheader .= $_;
}
close(HTMLHEADER);

open(HTMLFOOTER,"</home/httpd/html/staff/includes/footer2012.txt");
while (<HTMLFOOTER>) {
	$htmlfooter .= $_;
}
close(HTMLFOOTER);
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
my $feedback_message = "";
my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $logon_pass = $query->param("logon_pass");

my $uniqueid = $query->param("uniqueid");

my $location = $query->param("location");
	$location = "logon" if ($location eq '');

my $show_campaign_id = $query->param("show_campaign_id");
	$show_campaign_id = &backslash_fordb($show_campaign_id);
my $show_link_id = $query->param("show_link_id");
	$show_link_id = &backslash_fordb($show_link_id);
my $show_year = $query->param("show_year");
   $show_year = $year if ($show_year eq '');
my $show_mine_only = $query->param("show_mine_only");
my $recipient_list_name = $query->param("recipient_list_name");
my $send_now = $query->param("send_now");
my $show_contactlist_id = $query->param("show_contactlist_id");
my $confirm_action = $query->param("confirm_action"); # FOR CONFIRMING DELETIONS

my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};
my $browser = $ENV{"HTTP_USER_AGENT"};


## REMOVE TABS AND CARRIAGE RETURNS FROM USER-ENTERED DATA USING "CLEANTHIS" SUBROUTINE
#$first_name = &cleanthis ($first_name);

########################################################
## END: GET VARIABLES FROM FORM
########################################################


my @recipient;
my @recipient_firstname;
my @recipient_name;
my @recipient_userid;
my @recipient_password;
my @recipient_cc;
my @recipient_orgname;

my $tester1 = $query->param("tester1");
my $tester2 = $query->param("tester2");
my $tester3 = $query->param("tester3");
my $tester4 = $query->param("tester4");
my $tester5 = $query->param("tester5");
my $tester6 = $query->param("tester6");
my $tester7 = $query->param("tester7");
my $tester8 = $query->param("tester8");
my $tester9 = $query->param("tester9");
my $tester10 = $query->param("tester10");
my $tester11 = $query->param("tester11");
my $tester12 = $query->param("tester12");
my $tester13 = $query->param("tester13");
my $tester14 = $query->param("tester14");

my $singleuser_email = $query->param("singleuser_email");
my $singleuser_firstname = $query->param("singleuser_firstname");
my $singleuser_lastname = $query->param("singleuser_lastname");
my $singleuser_orgname = $query->param("singleuser_orgname");


########################################################
## START: DECLARE A LIST CONTAINING A SINGLE RECIPIENT
########################################################
if ($recipient_list_name eq 'single') {
	push @recipient, $singleuser_email;
	push @recipient_firstname, "$singleuser_firstname";
	push @recipient_name, "$singleuser_firstname $singleuser_lastname";
	push @recipient_orgname, "$singleuser_orgname";
}
########################################################
## END: DECLARE A LIST CONTAINING A SINGLE RECIPIENT
########################################################


########################################################
## START: DECLARE A LIST OF TESTER RECIPIENTS
########################################################

## START: HANDLE TEST USERS
if ($recipient_list_name eq 'testers') {

if ($tester1 eq 'yes') {
	push @recipient, "Brian.Litke\@sedl.org";
	push @recipient_firstname, "Brian";
	push @recipient_name, "Brian Litke";
	push @recipient_orgname, "SEDL";

	push @recipient, "austin_brian\@yahoo.com";
	push @recipient_firstname, "Brian";
	push @recipient_name, "Brian Litke";
	push @recipient_orgname, "SEDL";
}
if ($tester3 eq 'yes') {
	push @recipient, "John.Middleton\@sedl.org";
	push @recipient_firstname, "John";
	push @recipient_name, "John Middleton";
	push @recipient_orgname, "SEDL";

	push @recipient, "Lin.Harris\@sedl.org";
	push @recipient_firstname, "Lin";
	push @recipient_name, "Lin Harris";
	push @recipient_orgname, "SEDL";

	push @recipient, "Frank.Martin\@sedl.org";
	push @recipient_firstname, "Frank";
	push @recipient_name, "Frank Martin";
	push @recipient_orgname, "SEDL";

	push @recipient, "Joann.Starks\@sedl.org";
	push @recipient_firstname, "JoAnn";
	push @recipient_name, "JoAnn Starks";
	push @recipient_orgname, "SEDL";
}
if ($tester4 eq 'yes') {
	push @recipient, "Debbie.Ritenour\@sedl.org";
	push @recipient_firstname, "Debbie";
	push @recipient_name, "Debbie Ritenour";
	push @recipient_orgname, "SEDL";
}
if ($tester5 eq 'yes') {
	push @recipient, "Shaila.Abdullah\@sedl.org";
	push @recipient_firstname, "Shaila";
	push @recipient_name, "Shaila Abdullah";
	push @recipient_orgname, "SEDL";
}
if ($tester6 eq 'yes') {
	push @recipient, "Christine.Moses\@sedl.org";
	push @recipient_firstname, "Chris";
	push @recipient_name, "Chris Moses";
	push @recipient_orgname, "SEDL";
}
if ($tester7 eq 'yes') {
	push @recipient, "Leslie.Blair\@sedl.org";
	push @recipient_firstname, "Leslie";
	push @recipient_name, "Leslie Blair";
	push @recipient_orgname, "SEDL";
}
if ($tester9 eq 'yes') {
	push @recipient, "Kati.Timmons\@sedl.org";
	push @recipient_firstname, "Kati";
	push @recipient_name, "Kati Timmons";
	push @recipient_orgname, "SEDL";
}
if ($tester10 eq 'yes') {
	push @recipient, "Laura.Shankland\@sedl.org";
	push @recipient_firstname, "Laura";
	push @recipient_name, "Laura Shankland";
	push @recipient_orgname, "SEDL";
}
if ($tester11 eq 'yes') {
	push @recipient, "Luis.Martinez\@sedl.org";
	push @recipient_firstname, "Luis";
	push @recipient_name, "Luis Martinez";
	push @recipient_orgname, "SEDL";
}
if ($tester13 eq 'yes') {
	push @recipient, "Wes.Hoover\@sedl.org";
	push @recipient_firstname, "Wes";
	push @recipient_name, "Wes Hoover";
	push @recipient_orgname, "SEDL";
}

if ($tester14 eq 'yes') {
	push @recipient, "cjordan\@sedl.org";
	push @recipient_firstname, "Cathy";
	push @recipient_name, "Cathy Jordan";
	push @recipient_orgname, "SEDL";

	push @recipient, "Artie.Stockton\@sedl.org";
	push @recipient_firstname, "Artie";
	push @recipient_name, "Artie Stockton";
	push @recipient_orgname, "SEDL";

	push @recipient, "Deborah.Donnelly\@sedl.org";
	push @recipient_firstname, "Deborah";
	push @recipient_name, "Deborah Donnelly";
	push @recipient_orgname, "SEDL";

	push @recipient, "Joe.Parker\@sedl.org";
	push @recipient_firstname, "Joe";
	push @recipient_name, "Joe Parker";
	push @recipient_orgname, "SEDL";

	push @recipient, "Laura.Shankland\@sedl.org";
	push @recipient_firstname, "Laura";
	push @recipient_name, "Laura Skankland";
	push @recipient_orgname, "SEDL";

	push @recipient, "Zena.Rudo\@sedl.org";
	push @recipient_firstname, "Zena";
	push @recipient_name, "Zena Rudo";
	push @recipient_orgname, "SEDL";
}
## NEXT NUMBER TO USE IS 14


}
########################################################
## END: DECLARE A LIST OF TESTER RECIPIENTS
########################################################

########################################################
## START: DECLARE A LIST OF LISTSERV RECIPIENTS
########################################################
if ($recipient_list_name =~ 'listserv_') {

	
	my $listserv_name = $recipient_list_name; 
	   $listserv_name =~ s/listserv\_//g;

	## SAVE COOKIE WITH LIST NAME
	setCookie ("email_list", "$listserv_name", $expdate, $path, $thedomain);

	my $counter = "0";
	# Open the BULLETIN listserv text file and start reading the e-mail addresses
	open(TEXTFILE, "</home/slist/$listserv_name/dist") || die "Couldn't find file\n";
	my $email = '';
	my $alt_name = '';
	while (defined($email = <TEXTFILE>)) {
    	next if $email =~ /below this line/io; # Skip the non-address line
    	chomp($email);

   		# Attempt to deal with cases where a real name is stored in the dist list.
   		# Do this by saving whatever comes after the first whitespace *after the @ symbol*.  
   		# Whitespace is not allowed in internet domain names.
   		# This will fail to extract the extra info from local addresses (those without an @domain part), 
   		# but there shouldn't be any local users (DB lookup for them would fail, anyway).

     			if ($email =~ /^([^@]+@\S+)\s+(.*)$/o) {
    			  $email = $1;      # Effectively removes the "extra" info...
    			  $alt_name = $2;   # ...and stores it here
    			} else {
    			  $alt_name = '';
    			}
    
    		# Send the command and fetch rows
    		my $command = "select first, last from email where em like '$email'";
			my $dsn = "DBI:mysql:database=test;host=localhost";
			my $dbh = DBI->connect($dsn, "", "");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
#	my $num_matches = $sth->rows;

    		my ($first, $last) = $sth->fetchrow;
    			my $name = "$first $last";
    			$name = $alt_name if ($first eq '' && $last eq '');
				$name = "SEDL Bulletin Recipient" if ($name eq '');
				
 				$recipient[$counter] = "$email";
				$recipient_name[$counter] = $name;
				$recipient_firstname[$counter] = $first;
				
   			$counter++;

#				&send_bulletin($email, $name, $count, $text_bulletin_url, $text_bulletin_bullet1, $text_bulletin_bullet2, $text_bulletin_bullet3, $other_recipients);
		} # END WHILE LOOP THROUGH DIST FILE


}
########################################################
## END: DECLARE A LIST OF LISTSERV RECIPIENTS
########################################################


########################################################
## START: DECLARE A LIST OF CONTACT FILE RECIPIENTS
########################################################
if ($recipient_list_name =~ 'contactfile_') {
	my $contactfile_name = $recipient_list_name; 
	   $contactfile_name =~ s/contactfile\_//g;

	# Open the BULLETIN listserv text file and start reading the e-mail addresses
	open(TEXTFILE, "</home/httpd/html/staff/communications/email_contactlists/$contactfile_name") || die "Couldn't find file\n";

	my $contactfile_entry = '';
	my $alt_name = '';
	my $counter = "0";

	while (defined($contactfile_entry = <TEXTFILE>)) {
    	chomp($contactfile_entry); # REMOVE NEWLINE CHARACTER
		$contactfile_entry =~ s/\r//gi; ## ADDED 8/27/2008
		
		my ($this_contact_email, $this_contact_prefix, $this_contact_firstname, $this_contact_lastname, $this_contact_userid, $this_contact_password, $this_contact_cc_email, $this_contact_orgname) = split(/\t/,$contactfile_entry);
		
		$recipient[$counter] = $this_contact_email;
		$recipient_name[$counter] = "$this_contact_firstname $this_contact_lastname";
		$recipient_name[$counter] = "$this_contact_prefix $recipient_name[$counter]" if ($this_contact_prefix ne '');
		$recipient_firstname[$counter] = $this_contact_firstname;  
		$recipient_userid[$counter] = $this_contact_userid;
		$recipient_password[$counter] = $this_contact_password;
		$recipient_cc[$counter] = $this_contact_cc_email;
		$recipient_orgname[$counter] = $this_contact_orgname;
				
   		$counter++;
	} # END WHILE LOOP THROUGH DIST FILE


}
########################################################
## END: DECLARE A LIST OF CONTACT FILE RECIPIENTS
########################################################


########################################################
## START: DECLARE A LIST OF BOARD OF DIRECTORS RECIPIENTS
########################################################
if ($recipient_list_name eq 'board_of_directors') {
	my $listserv_name = $recipient_list_name; 
	   $listserv_name =~ s/listserv\_//g;

	my $counter = "0";
	
	my $command = "select * from board_of_directors order by bod_lastname";
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
			my ($bod_id, $bod_userid, $bod_prefix, $bod_firstname, $bod_middlename, $bod_lastname, $bod_state, $bod_city, $bod_description, $bod_current_job, $bod_officer, $bod_email, $bod_email_summer, $bod_phone_office, $bod_phone_cell, $bod_phone_home, $bod_fax, $bod_address, $bod_last_updated, $bod_last_updated_by, $bod_active, $bod_photo_file) = @arr;
    			my $name = $bod_firstname;
    			   $name .= " $bod_middlename" if ($bod_middlename ne '');
    			   $name .= " $bod_lastname";
				   $name = "SEDL Bulletin Recipient" if ($name eq '');
				$recipient[$counter] = $bod_email;
				$recipient_name[$counter] = $name;
				$recipient_orgname[$counter] = $bod_current_job;
    			$counter++;
		} # END DB QUERY LOOP
}
########################################################
## END: DECLARE A LIST OF BOARD OF DIRECTORS RECIPIENTS
########################################################



####################################################
# START: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################
my $cookie_ss_session_id = ""; # TRACK SESSION ID
my $cookie_ss_staff_id = ""; # TRACK USER ID
my $cookie_listserv = ""; # REMEMBER LAST LISTSERV USED
my $cookie_search_fav = ""; # TRACK USER ID
my $cookie_compose_email_html_pref = ""; # TRACK USER PREF FOR USING TINY MCE WHEN DESIGNING E-MAILS
	my(%cookies) = getCookies();
	foreach (sort(keys(%cookies))) {
		$cookie_ss_session_id = $cookies{$_} if (($_ eq 'ss_session_id') && ($session_id eq ''));
		$cookie_ss_staff_id = $cookies{$_} if ($_ eq 'staffid');
		$cookie_listserv = $cookies{$_} if ($_ eq 'email_list');
		$cookie_search_fav = $cookies{$_} if ($_ eq 'intranetsearch');
		$cookie_compose_email_html_pref = $cookies{$_} if ($_ eq 'compose_email_html_pref');
	}
$cookie_ss_session_id = $session_id if (($cookie_ss_session_id eq '') && ($session_id ne '')); # IF NOT SESSION IN COOKIE, BUT ONE PASSED VIA URL, USE SESSION FROM URL
$cookie_compose_email_html_pref = "WYSIWYG" if ($cookie_compose_email_html_pref eq '');
####################################################
# END: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################


## START: SET SEARCH FORM TO USER'S LAST DATA SOURCE SEARCH SELECTION
if ($cookie_search_fav ne '') {
	$htmlheader =~ s/\<option value\=\"$cookie_search_fav\"\>/\<option value\=\"$cookie_search_fav\" SELECTED\>/gi;
}
## END: SET SEARCH FORM TO USER'S LAST DATA SOURCE SEARCH SELECTION


## START: SET VALID USERS LIST
my @valid_users;
push @valid_users, 'astockto';
push @valid_users, 'blitke';
push @valid_users, 'cferguso';
push @valid_users, 'ddonnell';
push @valid_users, 'dritenou';
push @valid_users, 'emccann';
push @valid_users, 'ewaters';
push @valid_users, 'ktimmons';
push @valid_users, 'jmiddlet';
push @valid_users, 'jwaisath'; 
push @valid_users, 'lblair';
push @valid_users, 'lmartine';
push @valid_users, 'lshankla'; 
push @valid_users, 'lwood'; 
push @valid_users, 'macuna'; 
push @valid_users, 'nreynold'; 
push @valid_users, 'sabdulla'; 
push @valid_users, 'zrudo';

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
				$location = "select_campaign";

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
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_delete_session) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
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
		
			## SAVE COOKIES WITH SESSION ID AND USER ID
				setCookie ("ss_session_id", $ss_session_id, $expdate, $path, $thedomain);
				setCookie ("staffid", $ss_staff_id, $expdate, $path, $thedomain);
				$logon_user = $ss_staff_id;

		} # END DB QUERY LOOP

		my $valid_user = "no";
			## START: LOOP THROUGH ARRAY OF VALID USERS TO ENSURE THIS USER IS OK
			my $counter = "0";
			while ($counter <= $#valid_users) {
				$valid_user = "yes" if ($logon_user eq $valid_users[$counter]);
				$counter++;
			}
			## END: LOOP THROUGH ARRAY OF VALID USERS TO ENSURE THIS USER IS OK

		## IF SESSION NOT ACTIVE, PROMPT FOR LOGON
		if ($num_matches eq '0') {
			$cookie_ss_session_id = "";
#			setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
			$location = "logon"; # AFTER LOGOUT, SHOW LOGON SCREEN
		} elsif (($num_matches eq '1') && ($valid_user eq 'no')) {
			$error_message .= "You are not authorized to use the SEDL E-mail Marketing Tool.  Please contact SEDL's web administrator, Brian Litke, at ext. 6529 for further assistance.";
			$location = "logon"; # SHOW LOGON SCREEN
		} else {
			$session_active = "yes";
			if (($session_active eq 'yes') && ($location eq 'logon')) {
				$location = "select_campaign";
			}
		}
	}
######################################################
## END: CHECK SESSION ID AND VERIFY
######################################################


######################################################
## START: LOCATION = PROCESS_PREFERENCES
######################################################
if ($location eq 'process_preferences') {
	## READ NEW SETTING
	my $pref_html = $query->param("pref_html");
	
	## SAVE COOKIE
	setCookie ("compose_email_html_pref", $pref_html, $expdate, $path, $thedomain);

	
	## SET COOKIE VARIABLE THAT HOLDS THIS INFO
	$cookie_compose_email_html_pref = $pref_html;
	
	## SET LOCAITON TO SELECT_CAMPAIGN
	$location = "select_campaign";


}
######################################################
## END: LOCATION = PROCESS_PREFERENCES
######################################################



################################
## START: PRINT PAGE HEADER
################################
print header;

my $pagetitle = "Log on";
   $pagetitle = "Select a Campaign" if ($location eq 'select_campaign');
   $pagetitle = "Select Recipients" if ($location eq 'showform');
   $pagetitle = "Confirm Sending Campaign" if ($location eq 'showform_confirm');
   $pagetitle = "Preview E-mail" if ($location eq 'preview_campaign');
   $pagetitle = "Add/Edit a Campaign" if (($location eq 'edit_campaign') || ($location =~ 'process'));
   
print <<EOM;
<HTML>
<head>
<title>SEDL E-mail Campaign Tool: $pagetitle</title>
EOM
if (($location eq 'edit_campaign') && ($cookie_compose_email_html_pref eq 'WYSIWYG')) {
print<<EOM;
<script language="javascript" type="text/javascript" src="/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">     
tinyMCE.init({
	mode : "exact",
	elements : "new_c_htmlversion",	
	theme : "advanced",
	plugins : "spellchecker,table,paste",
	theme_advanced_buttons1_add : "pastetext,pasteword",
	theme_advanced_buttons3_add : "tablecontrols, spellchecker",
	invalid_elements : "style,span",
	table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
	table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
	table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
	table_cell_limit : 100,
	table_row_limit : 20,
	table_col_limit : 5,
    force_br_newlines : true,
    force_p_newlines : false,
	paste_auto_cleanup_on_paste : true,
 	theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
	content_css: "/staff/includes/staff2006_tinymce.css",
	apply_source_formatting : true,
	convert_urls : false
});
</script>
EOM
}
print<<EOM;
$htmlheader
<TABLE CELLPADDING="10" WIDTH="100%">
<TR><TD>
EOM
################################
## END: PRINT PAGE HEADER
################################


######################################################################################
## START: LOCATION = LOGON
######################################################################################
if ($location eq 'logon') {
print<<EOM;
<h1 ALIGN=CENTER>SEDL E-mail Campaign Manager</H1>
<H3>Please Log On</H3>

EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>Please enter your SEDL staff ID and password.</p>
<form action="email_campaigns.cgi" method=POST>
<H4>Your Information </H4>
<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0>
  <TR><TD VALIGN=TOP WIDTH=250><B>Your intranet ID</B><BR>
  			(ex: whoover)</TD>
      <TD WIDTH=420 VALIGN=TOP>
      <INPUT TYPE=INPUT NAME=logon_user SIZE=8 VALUE="$cookie_ss_staff_id">
      </TD></TR>
  <TR><TD VALIGN=TOP WIDTH=150><B>Password</B></TD>
      <TD WIDTH=420 VALIGN=TOP><INPUT TYPE=PASSWORD NAME=logon_pass SIZE=8></TD></TR>
</TABLE>
  	<UL>
	<input type="hidden" name="location" value="process_logon">
	<input type="submit" name="submit" value="Log On">
	</form>
    </UL>       


EOM
} else {
## PRINT FOOTER FOR ALL PAGES EXCEPT LOGON
print<<EOM;
<h1 ALIGN=CENTER><A HREF="http://www.sedl.org/staff/communications/email_campaigns.cgi?show_mine_only=$show_mine_only&show_year=$show_year">SEDL E-mail Campaign Manager</A><BR>
<SPAN class=small>Logged on as $logon_user\@sedl.org. Click here to <A HREF="email_campaigns.cgi?location=logout">logout</A>.<br>
Click here to <a href="email_campaigns.cgi?location=show_preferences">edit preferences</a> for composing HTML e-mails.</SPAN>
</h1>
EOM
}
######################################################################################
## END: LOCATION = LOGON
######################################################################################

######################################################
## START: LOCATION = SHOW_USERS
######################################################
if ($location eq 'show_users') {
print<<EOM;
<H3>List of SEDL Staff Authorized to Use the E-mail Campaignn</H3>
<OL>
EOM
my $counter = "0";
	while ($counter <= $#valid_users) {
		print "<LI>$valid_users[$counter]</LI>";
		$counter++;
	}
print "</OL>";
}
######################################################
## END: LOCATION = SHOW_USERS
######################################################

######################################################
## START: LOCATION = PROCESS_PREFERENCES
######################################################
if ($location eq 'process_preferences') {
	## READ NEW SETTING
	my $pref_html = $query->param("pref_html");
	
	## SAVE COOKIE
	setCookie ("compose_email_html_pref", $pref_html, $expdate, $path, $thedomain);

	
	## SET COOKIE VARIABLE THAT HOLDS THIS INFO
	$cookie_compose_email_html_pref = $pref_html;
	
	## SET LOCAITON TO SELECT_CAMPAIGN
	$location = "select_campaign";


}
######################################################
## END: LOCATION = PROCESS_PREFERENCES
######################################################

######################################################
## START: LOCATION = SHOW_PREFERENCES
######################################################
if ($location eq 'show_preferences') {
my $selected_wysiwyg = "";
   $selected_wysiwyg = "SELECTED" if ($cookie_compose_email_html_pref eq 'WYSIWYG');
my $selected_html = "";
   $selected_html = "SELECTED" if ($cookie_compose_email_html_pref eq 'HTML');

print<<EOM;
<H3>My Preferences for the SEDL E-mail Campaign Manager</H3>
<p>
<b>Directions</b><br>
Use the form below to set your preference for composing e-mails with HTML content.  By default, the 
TinyMCE editor will be used, which allows non-technical staff to easily create e-mails with bold, italics, links, etc. 
However, some CSS styles and other HTML elements may not save properly using TinyMCE.  in such cases, you 
should update this preference to be "hard-coded HTML editing".
</p>
	<form action="email_campaigns.cgi" method=POST>
	<select name="pref_html">
	<option value="WYSIWYG" $selected_wysiwyg>WYSIWYG HTML editing using the TinyMCE plug-in</option>
	<option value="HTML" $selected_html>hard-coded HTML editing (i.e. pasting from HTML source file)</option>
	</select>
	<br>
	<br>
	<input type="hidden" name="location" value="process_preferences">
	<input type="submit" name="submit" value="Save Preferences">
	</form>
EOM

}
######################################################
## END: LOCATION = SHOW_PREFERENCES
######################################################


######################################################################################
## START: LOCATION = PROCESS_CAMPAIGN_EDIT
######################################################################################
if ($location =~ 'process_campaign') {
	# READ NEW OR EDITED CAMPAIGN SETING VARIABLES
	my $new_c_id = $query->param("new_c_id");
	my $new_c_subject = $query->param("new_c_subject");
	my $new_c_textversion = $query->param("new_c_textversion");
	my $new_c_htmlversion = $query->param("new_c_htmlversion");
	################################
	## START: CLEAN UP HTML CODING
	################################
		$new_c_htmlversion =~ s/\/>/\>/gi; # REMOVE SINGLETON TAGS
	if (($new_c_htmlversion ne '') && ($new_c_htmlversion !~ '\<html')) {
		$new_c_htmlversion = "<html><body>$new_c_htmlversion</body></html>";
	}
	################################
	## END: CLEAN UP HTML CODING
	################################
	my $new_c_sendtextorhtml = $query->param("new_c_sendtextorhtml");
	my $new_c_fromaddress = $query->param("new_c_fromaddress");
	my $new_c_fromname = $query->param("new_c_fromname");
	my $new_c_bccaddress = $query->param("new_c_bccaddress");
	my $new_c_address_byname = $query->param("new_c_address_byname");
	my $new_c_address_byname_default = $query->param("new_c_address_byname_default");

	# BACKSLASH VARIABLES BEFORE UPDATING DB RECORD
	$new_c_subject = &backslash_fordb($new_c_subject);
	$new_c_textversion = &backslash_fordb($new_c_textversion);
	$new_c_htmlversion = &backslash_fordb($new_c_htmlversion);
	$new_c_fromaddress = &backslash_fordb($new_c_fromaddress);
	$new_c_fromname = &backslash_fordb($new_c_fromname);
	$new_c_bccaddress = &backslash_fordb($new_c_bccaddress);
	$new_c_address_byname_default = &backslash_fordb($new_c_address_byname_default);

	my $command = "INSERT INTO email_campaigns VALUES ('', '$new_c_subject', '$new_c_textversion', '$new_c_htmlversion', '$new_c_sendtextorhtml', '$new_c_fromaddress', '$new_c_fromname', '$new_c_bccaddress', '$new_c_address_byname', '$new_c_address_byname_default', '$date_full_mysql', '', '$cookie_ss_staff_id', '')";
		my $addededited = "added";
		if ($show_campaign_id ne '') {
			$command = "UPDATE email_campaigns SET 
						c_subject = '$new_c_subject', 
						c_fromaddress = '$new_c_fromaddress', 
						c_fromname = '$new_c_fromname',	
						c_bccaddress = '$new_c_bccaddress',	
						c_address_byname = '$new_c_address_byname',
						c_address_byname_default = '$new_c_address_byname_default', 
						c_sendtextorhtml = '$new_c_sendtextorhtml', 
						c_textversion = '$new_c_textversion', 
						c_htmlversion = '$new_c_htmlversion' WHERE c_id like '$show_campaign_id'";
			$addededited = "edited";
		}

		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#		my $num_matches = $sth->rows;

	$feedback_message = "Your campaign ($show_campaign_id) was $addededited successfully.";
	$location = "select_campaign";
}
######################################################################################
## END: LOCATION = PROCESS_CAMPAIGN_EDIT
######################################################################################


######################################################################################
## START: LOCATION = PROCESS_DELETE_CAMPAIGN
######################################################################################
if ($location eq 'process_delete_campaign') {
	if ($confirm_action ne 'yes') {

	my $command = "select c_id, c_subject from email_campaigns";
	$command .= " WHERE c_id LIKE '$show_campaign_id'" if ($show_campaign_id ne '');
	$command .= " order by c_datecreated DESC";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;
	while (my @arr = $sth->fetchrow) {
		my ($delete_c_id, $delete_c_subject) = @arr;

print<<EOM;
<P>
<H3>Confirm Campaign Deletion</H3>
<B>ID#:</B> $delete_c_id<BR>
<B>E-mail Campaign Subject Line:</B> $delete_c_subject
<UL>
<form action="email_campaigns.cgi" method=POST>
<input type="checkbox" name="confirm_action" id="confirm_action" value="yes"><label for="confirm_action">Click here to confirm the 
deletion of this campaign and all associated records.</a>
<P>
<input type="hidden" name="show_mine_only" value="$show_mine_only">
<input type="hidden" name="show_year" value="$show_year">
<input type="hidden" name="show_campaign_id" value="$show_campaign_id">
<input type="hidden" name="location" value="process_delete_campaign">
<input type="submit" name="submit" value="confirm deletion">
</form>
</UL>

EOM
		} # END DB QUERY LOOP
	} else {
		## PROCESS DELETION OF MAIN EVENT
		my $command = "delete from email_campaigns WHERE c_id = '$show_campaign_id'";
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		$feedback_message .= "<BR>COMMAND TO DELETE CAMPAIGN RECORD: <FONT COLOR=RED>$command</FONT>";

		## PROCESS DELETION OF MAIN EVENT
		my $command = "delete from email_campaigns_opened WHERE opened_record_id = '$show_campaign_id'";
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		$feedback_message .= "<BR>COMMAND TO E-MAILS OPENED LOG: <FONT COLOR=RED>$command</FONT>";

		## PROCESS DELETION OF MAIN EVENT
		my $command = "delete from email_campaigns_sent WHERE c_sent_id = '$show_campaign_id'";
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		$feedback_message .= "<BR>COMMAND TO DELETE CAMPAIGNS SENT LOG: <FONT COLOR=RED>$command</FONT>";

		$location = "select_campaign";
		$show_campaign_id = "";
		
	} # END IF/ELSE
}
######################################################################################
## END: LOCATION = PROCESS_DELETE_CAMPAIGN
######################################################################################


######################################################################################
## START: LOCATION = PROCESS_LINK_TRACKING
######################################################################################
if (($location eq 'process_link_tracking') && ($show_campaign_id eq '')) {
	$error_message = "Unexpected error.  You did not enter a campaign ID.";
	$location = "setup_link_tracking";
}

if ($location eq 'process_link_tracking') {

my $new_tracked_link = $query->param("new_tracked_link");
my $command_process_link = "";

	my $command = "SELECT unique_id, url FROM email_campaigns_trackurl WHERE campaign_id = '$show_campaign_id' AND unique_id = '$show_link_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_links = $sth->rows;
		if ($num_matches_links == 0) {
			$command_process_link = "INSERT INTO email_campaigns_trackurl VALUES ('', '$show_campaign_id', '$new_tracked_link')";
			$feedback_message = "The link was ADDED successfully.";
		} else {
			$command_process_link = "UPDATE email_campaigns_trackurl SET url='$new_tracked_link' where unique_id = '$show_link_id'";
			$feedback_message = "The link was EDITED successfully.";
		}
		if (($new_tracked_link eq '') && ($show_link_id ne '')) {
			$command_process_link = "DELETE FROM email_campaigns_trackurl WHERE unique_id ='$show_link_id'";
			$feedback_message = "The link was DELETED successfully.";
		}
#print "<P>$command_process_link</p>";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_process_link) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_links = $sth->rows;
	$location = "setup_link_tracking";
}
######################################################################################
## END: LOCATION = PROCESS_LINK_TRACKING
######################################################################################


######################################################################################
## START: LOCATION = EDIT_LINK_TRACKING
######################################################################################
if (($location eq 'edit_link_tracking') && ($show_campaign_id eq '')) {
	$error_message = "Unexpected error.  You did not enter a campaign ID.";
	$location = "setup_link_tracking";
}

if ($location eq 'edit_link_tracking') {
	## GRAB CAMPAIGN NAME
	my $campaign_name = "";
		my $command = "select c_subject from email_campaigns WHERE c_id LIKE '$show_campaign_id'";
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_campaigns = $sth->rows;
	#   $error_message .= "<P>NOTICE: $command";
			while (my @arr = $sth->fetchrow) {
				($campaign_name) = @arr;
				$campaign_name = "untitled" if ($campaign_name eq '');
			} # END DB QUERY LOOP
print<<EOM;      
<form action="email_campaigns.cgi" method=POST>
<H3>Link Tracking (Edit Link) for the E-mail Campaign:<br>
<i>$campaign_name</i></H3>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

	#####################################################################
	## START: LOOK UP HOW MANY LINKS ARE BEING TRACKED FOR THE CAMPAIGN
	#####################################################################
	my $command = "SELECT unique_id, url FROM email_campaigns_trackurl WHERE campaign_id = '$show_campaign_id' AND unique_id = '$show_link_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_links = $sth->rows;
print<<EOM;
Best Practices for tracking links:	
	<ul>
	<li>Enter a URL that exists in your e-mail campaign in the form below. Be sure to include the full URL, including "http://".</li>
	<li>Before the e-mail is sent, the script will reploace tracked links with a special tracking web address.</li>
	<li>You may wish the actual URL to be visible to readers of your e-mail. To do so, specify the link on this page using the full "http://", but format the 
		clickable text in your e-mail to be the link without the "http://" which will make the visible link appear to go to the actual destination while the actual URL
		is replaced with the tracking link.<br>(i.e. &lt;a href = "http://www.sedl.org"&gt;www.sedl.org&lt;/a&gt;)</li>
	<li>You can delete a link by removing the link and clicking the "Save" button.</li>
	<li>If you track a click to your home page, track the link like http://www.sedl.org/welcome.html 
		(not http://www.sedl.org).  The reason is that the e-mail will replace the URL you are 
		tracking with a special URL, and if it replaces all instances of "http://www.sedl.org" in your e-mail, 
		that will break any images that have "http://www.sedl.org" in their image URL. 
		Track http://www.sedl.org/welcome.html instead and your images won't break.</li>
	</ul>

<br>
		<table border="1" cellpadding="1" cellspacing="0">
		<tr><td bgcolor="#ebebeb"><b>Link</b><br>In the box below, enter the Web site address with the full "http://" prefix, such as: <br>
				http://www.sedl.org</td>
			<td bgcolor="#ebebeb" width="60" align="center"><b>Save</b></td></tr>
EOM
		while (my @arr = $sth->fetchrow) {
			my ($unique_id, $url) = @arr;
print<<EOM;
		<tr><td><input type="TEXT" name="new_tracked_link" value="$url" size="80">
				<input type="hidden" name="show_link_id" value="$unique_id">
			</td>
EOM
		} # END DB QUERY LOOP
		if ($num_matches_links == 0) {
print<<EOM;
		<tr><td><input type="TEXT" name="new_tracked_link" size="80"></td>
EOM
		}

print<<EOM;
			<td>
				<input type="submit" name="submit" value="Save">
			</td>
		</tr>
		</table>
		<input type="hidden" name="location" value="process_link_tracking">
		<input type="hidden" name="show_campaign_id" value="$show_campaign_id">
		<input type="hidden" name="show_mine_only" value="$show_mine_only">
		<input type="hidden" name="show_year" value="$show_year">
		</form>
EOM
}
######################################################################################
## END: LOCATION = EDIT_LINK_TRACKING
######################################################################################


######################################################################################
## START: LOCATION = SETUP_LINK_TRACKING
######################################################################################
if (($location eq 'setup_link_tracking') && ($show_campaign_id eq '')) {
	$error_message = "Unexpected error.  You did not enter a campaign ID.";
	$location = "showform";
}

if ($location eq 'setup_link_tracking') {
	## GRAB CAMPAIGN NAME
	my $campaign_name = "";
		my $command = " select c_subject from email_campaigns WHERE c_id LIKE '$show_campaign_id'";
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_campaigns = $sth->rows;
	#   $error_message .= "<P>NOTICE: $command";
			while (my @arr = $sth->fetchrow) {
				($campaign_name) = @arr;
				$campaign_name = "untitled" if ($campaign_name eq '');
			} # END DB QUERY LOOP
print<<EOM;      
<H3>Link Tracking (Setup) for the E-mail Campaign:<br>
<i><a href="email_campaigns.cgi?location=select_campaign&show_campaign_id=$show_campaign_id&show_mine_only=$show_mine_only&show_year=$show_year">$campaign_name</a></i></H3>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

	#####################################################################
	## START: LOOK UP HOW MANY LINKS ARE BEING TRACKED FOR THE CAMPAIGN
	#####################################################################
	my $command = "SELECT unique_id, url FROM email_campaigns_trackurl WHERE campaign_id = '$show_campaign_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_urls_tracked = $sth->rows;
print<<EOM;
Click here to <a href=\"email_campaigns.cgi?location=edit_link_tracking&show_campaign_id=$show_campaign_id&show_mine_only=$show_mine_only&show_year=$show_year\">add a link to track</a>.
<br><br>
<table border="1" cellpadding="1" cellspacing="0">
		<tr><td bgcolor="#ebebeb"><b>Link</b></td>
			<td bgcolor="#ebebeb" width="60" align="center"><b>Edit</b></td></tr>
EOM
		if ($num_urls_tracked == 0) {
			print "<tr><td colspan=\"2\">Currently, there are no links being tracked in this campaign.</td></tr>";
		}
		while (my @arr = $sth->fetchrow) {
			my ($unique_id, $url) = @arr;
				my $url_for_screen = $url;
   				   $url_for_screen =~ s/\?/ \?/gi;
print<<EOM;
<tr><td valign="top"><a href=\"$url\">$url_for_screen</a></td>
	<td><form action="email_campaigns.cgi" method=POST>

		<input type="submit" name="submit" value="Edit">
		<input type="hidden" name="location" value="edit_link_tracking">
		<input type="hidden" name="show_link_id" value="$unique_id">
		<input type="hidden" name="show_campaign_id" value="$show_campaign_id">
		<input type="hidden" name="show_mine_only" value="$show_mine_only">
		<input type="hidden" name="show_year" value="$show_year">
		</form>
	</td>
</tr>
EOM
		} # END DB QUERY LOOP
print<<EOM;
			</table>
EOM
}
######################################################################################
## END: LOCATION = SETUP_LINK_TRACKING
######################################################################################



######################################################################################
## START: LOCATION = SELECT_CAMPAIGN
######################################################################################
if ($location eq 'select_campaign') {

	my %campaigns_peryear;

	if ($show_campaign_id eq '') {
		my $selected_mine_only = "";
   		   $selected_mine_only = "SELECTED" if ($show_mine_only ne '');


print<<EOM;      
<H3>Select an E-mail Campaign</H3>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<P>
Click here to <A HREF="email_campaigns.cgi?location=edit_campaign&show_mine_only=$show_mine_only&show_year=$show_year">create a new campaign</A> or <a href="http://www.sedl.org/staff/communications/clientlist-upload.cgi">upload a new contact list</a> (list of e-mail addresses.)
<P>
<form action="email_campaigns.cgi" method=POST>
Click here to refresh page showing 
<SELECT NAME="show_mine_only">
<OPTION VALUE="">campaigns created by anyone</option>
<OPTION VALUE="yes" $selected_mine_only>campaigns created by me</option>
</SELECT>
EOM


print<<EOM;
campaigns from the year:

<SELECT NAME="show_year">
<OPTION VALUE="\%">all years</option>
EOM
			my $year_counter = $year + 1;
			while ($year_counter >= 2005) {
				print "<OPTION VALUE=\"$year_counter\"";
				print " SELECTED" if ($year_counter eq $show_year);
				print ">$year_counter</OPTION>";
				$year_counter--;
			}


print<<EOM;
</SELECT>
<input type="hidden" name="location" value="select_campaign">
<input type="submit" name="submit" value="Go">
</form>
EOM

		} else {
			print "<P><H3>E-mail Campaign Overview</H3>";
			print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
			print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';
		}

print <<EOM;
<P>
EOM

	my $command = "select * from email_campaigns";
	$command .= " WHERE c_createdby LIKE '%'" if ($show_mine_only eq '');
	$command .= " WHERE c_createdby LIKE '$logon_user'" if ($show_mine_only ne '');
	$command .= " AND c_datecreated LIKE '$show_year%'" if ($show_year ne '\%');
	$command .= " order by c_datecreated DESC";
	if ($show_campaign_id ne '') {
		$command = " select * from email_campaigns WHERE c_id LIKE '$show_campaign_id'";
	}

# print "<P>COMMAND: $command";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_campaigns = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";


if ($num_matches_campaigns ne '0') {
	if ($show_campaign_id ne '') {
print<<EOM;
<TABLE CELLPADDING=4 BORDER=1 CELLSPACING=0>
<TR><TD BGCOLOR="EBEBEB"><B>ID#</B></TD>
	<TD BGCOLOR="EBEBEB" NOWRAP><B>E-mail Campaign<BR>Subject & Message</B></TD>
	<TD BGCOLOR="EBEBEB"><B>Campaign Options</B></TD>
	<TD BGCOLOR="EBEBEB"><B>Sent log</B></TD></TR>
EOM
	} else {
print<<EOM;
<P>
Displaying $num_matches_campaigns campaigns, sorted by date created.
<P>
<TABLE CELLPADDING=4 BORDER=1 CELLSPACING=0>
<TR><TD BGCOLOR="EBEBEB"><B>ID#</B></TD>
	<TD BGCOLOR="EBEBEB"><B>E-mail Campaign<BR>Subject & Message</A></B></TD>
	<TD BGCOLOR="EBEBEB"><B>Created by</B></TD>
	<TD BGCOLOR="EBEBEB"><B>Created Date</B></TD></TR>
EOM
	} # END IF/ELSE
} # END IF num_matches ne '0'
	while (my @arr = $sth->fetchrow) {
		my ($c_id, $c_subject, $c_textversion, $c_htmlversion, $c_sendtextorhtml, $c_fromaddress, $c_fromname, $c_bccaddress, $c_address_byname, $c_address_byname_default, $c_datecreated, $c_datelastsent, $c_createdby, $c_sentlog) = @arr;
			$c_address_byname = "no" if ($c_address_byname ne 'yes');
			my $c_datecreated_pretty = date2standard($c_datecreated);
			$c_subject = "untitled" if ($c_subject eq '');
			
			# START: COUNT # OF CAMPAIGNS PER YEAR
			my $this_year = substr($c_datecreated,0,4);
			$campaigns_peryear{$this_year}++;
			# END: COUNT # OF CAMPAIGNS PER YEAR

if ($show_campaign_id ne '') {

			# START: COUNT NUMBER OF E-MAILS OPENED
#			my $command = "select * from email_campaigns_opened where opened_campaign_id='$c_id'";
#	my $dsn = "DBI:mysql:database=intranet;host=localhost";
#	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#	$sth->execute;
#			my $num_opened = $sth->rows;
			# END: COUNT NUMBER OF E-MAILS OPENED
print<<EOM;
			<TR><TD VALIGN="TOP">$c_id</TD>
				<TD VALIGN="TOP"><B>$c_subject</B> <FONT COLOR="#999999">(Created: $c_createdby $c_datecreated_pretty)</FONT><P>
					Click to <A HREF="email_campaigns.cgi?location=preview_campaign&show_campaign_id=$c_id&show_mine_only=$show_mine_only&show_year=$show_year">preview e-mail</A>
					<P>
					Click here to <A HREF="email_campaigns.cgi?location=setup_link_tracking&show_campaign_id=$c_id&show_mine_only=$show_mine_only&show_year=$show_year">specify which links should track clicks</A>
					<P>
					Click here to <A HREF="email_campaigns.cgi?location=showform&show_campaign_id=$c_id&show_mine_only=$show_mine_only&show_year=$show_year">specify recipients</A>
					<P>
					Click here to <A HREF="email_campaigns.cgi?location=test_spam&show_campaign_id=$c_id&show_mine_only=$show_mine_only&show_year=$show_year">check SPAM rating</A>
					<P>
					
EOM
	if (($c_sendtextorhtml eq 'html') && ($c_textversion eq '')) {
		print "<BR><FONT COLOR=RED>Warning: you have not specified<BR>a text version of this e-mail<BR>to complement the HTML version.</FONT>";
	}
print<<EOM;
</TD>
				<TD VALIGN="TOP">
					<TABLE BORDER="1" CELLPADDING=2 CELLSPACING=0>
					<TR><TD COLSPAN=2 align="CENTER"><A HREF="email_campaigns.cgi?location=edit_campaign&show_campaign_id=$c_id&show_mine_only=$show_mine_only&show_year=$show_year">edit this campaign's options</A></TD></TR>
					<TR><TD class=small>Text only vs. HTML?</TD><TD class=small><FONT COLOR=BLUE>$c_sendtextorhtml</FONT></TD></TR>
					<TR><TD class=small>From address:</TD><TD class=small><FONT COLOR=BLUE>$c_fromaddress</FONT></TD></TR>
					<TR><TD class=small>From name:</TD><TD class=small><FONT COLOR=BLUE>$c_fromname</FONT></TD></TR>
					<TR><TD class=small>Bcc (if any):</TD><TD class=small><FONT COLOR=BLUE>$c_bccaddress</FONT></TD></TR>
					<TR><TD class=small nowrap>Address w/username?</TD><TD class=small><FONT COLOR=BLUE>$c_address_byname</FONT></TD></TR>
EOM
					if ($c_address_byname eq 'yes') {
						print "<TR><TD class=small>Default user name if none:</TD><TD class=small><FONT COLOR=BLUE>$c_address_byname_default</FONT></TD></TR>";
					}
#print<<EOM;
#					<TR><TD class=small nowrap># <A HREF="email_c_tip_opened.html" TARGET="TOP">E-mails opened</A></TD><TD class=small><FONT COLOR=BLUE>$num_opened</FONT></TD></TR>
#EOM
##############################################################################
## START: LOOK UP HOW MANY CLICKS FOR EACH LINK WERE TRACKED FOR THE CAMPAIGN
###############################################################################
my %tracked_clicks_unique_by_url;
my %tracked_clicks;
my %tracked_clicks_sedl;
	my $command = "SELECT host_address, ip_address, trackurl_id FROM email_campaigns_trackclicks WHERE campaign_id = '$show_campaign_id' order by ip_address";
#	my $command = "SELECT trackurl_id FROM email_campaigns_trackclicks WHERE campaign_id = '$show_campaign_id' AND host_address NOT LIKE '%sedl%'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	my $last_ip;
	my $num_unique_ip = 0;
	
	while (my @arr = $sth->fetchrow) {
		my ($host_address, $ip_address, $trackurl_id) = @arr;
		if ($ip_address eq '') {
			$ip_address = $host_address;
		}

		## START: COUNT SEDL VS. EXTERNAL LINK CLICKS
		if ($host_address =~ 'sedl') {
			$tracked_clicks_sedl{$trackurl_id}++;
		} else {
			$tracked_clicks{$trackurl_id}++;
		}
		## END: COUNT SEDL VS. EXTERNAL LINK CLICKS

		## START: COUNT UNIQUE IP ADDRESSES
		if ($last_ip ne $ip_address) {
			if ($host_address =~ 'sedl') {
				# ignore SEDL clicks
			} else {
				$num_unique_ip++;
			}
		}
		$last_ip = $ip_address;
		## END: COUNT UNIQUE IP ADDRESSES
	} # END DB QUERY LOOP

my $last_trackurl_id = "";
   $last_ip = "";
my $first_timestamp = "99999999999999999";
my $last_timestamp = "";
my %clicks_by_date;
my $command = "SELECT host_address, ip_address, trackurl_id, timestamp 
				FROM email_campaigns_trackclicks 
				WHERE campaign_id = '$show_campaign_id' 
				order by trackurl_id";
#my $command = "SELECT trackurl_id FROM email_campaigns_trackclicks WHERE campaign_id = '$show_campaign_id' AND host_address NOT LIKE '%sedl%'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;
	while (my @arr = $sth->fetchrow) {
		my ($host_address, $ip_address, $trackurl_id, $timestamp) = @arr;
		if (($last_trackurl_id ne $trackurl_id) || ($last_ip ne $ip_address)) {
			if ($host_address =~ 'sedl') {
			} else {
				$tracked_clicks_unique_by_url{$trackurl_id}++;
			}
		}
		# REMEMBER FIRST TIMESTAMP
		if (($timestamp < $first_timestamp) && (length($timestamp) == 14) && ($host_address !~ 'sedl')) {
			$first_timestamp = $timestamp; 
		}
		# REMEMBER LAST TIMESTAMP
		if (($timestamp > $last_timestamp) && ($host_address !~ 'sedl')) {
			$last_timestamp = $timestamp; 
		}
		# REMEMBER CLICKS PER DAY
		my $date_ofclick = substr($timestamp,0,8); # COMPUTE DATE ID
		if ($host_address !~ 'sedl') {
			$clicks_by_date{$date_ofclick}++; # INCREMENT COUNTER FOR THIS DATE
		}
		$last_trackurl_id = $trackurl_id;
		$last_ip = $ip_address;
	} # END DB QUERY LOOP

##############################################################################
## END: LOOK UP HOW MANY CLICKS FOR EACH LINK WERE TRACKED FOR THE CAMPAIGN
###############################################################################


#####################################################################
## START: LOOK UP HOW MANY LINKS ARE BEING TRACKED FOR THE CAMPAIGN
#####################################################################
my $command = "SELECT unique_id, url FROM email_campaigns_trackurl WHERE campaign_id = '$show_campaign_id'";
my $dsn = "DBI:mysql:database=intranet;host=localhost";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_urls_tracked = $sth->rows;
my $total_clicks = 0;
my $total_clicks_unique_user = 0;
	if ($num_urls_tracked != 0) {
print<<EOM;
<TR><TD class=small colspan="2"><br>$num_urls_tracked links tracked in this campaign<br>

		<table border="1" cellpadding="1" cellspacing="0">
		<tr><td bgcolor="#ebebeb"><b>Link</b></td>
			<td bgcolor="#ebebeb"><b>External<br>Clicks</b></td>
			<td bgcolor="#ebebeb"><b>External<br>Unique User<br>Clicks</b></td>
			<td bgcolor="#ebebeb"><b>SEDL<br>Clicks</b></td></tr>
EOM
	} # END IF
		while (my @arr = $sth->fetchrow) {
			my ($unique_id, $url) = @arr;
				$tracked_clicks{$unique_id} = "0" if ($tracked_clicks{$unique_id} eq '');
				$total_clicks = $total_clicks + $tracked_clicks{$unique_id};
				$total_clicks_unique_user = $total_clicks_unique_user + $tracked_clicks_unique_by_url{$unique_id};
				my $url_for_screen = $url;
   				   $url_for_screen =~ s/\?/ \?/gi;
print<<EOM;
		<tr><td class=small>$url_for_screen</td>
			<td align="right">$tracked_clicks{$unique_id}</td>
			<td align="right">$tracked_clicks_unique_by_url{$unique_id}</td>
			<td align="right">$tracked_clicks_sedl{$unique_id}</td></tr>
EOM
		} # END DB QUERY LOOP
	if ($num_urls_tracked != 0) {
my $average_clicks_per_user = 0;
	if (($total_clicks_unique_user != 0) && ($num_unique_ip != 0)) {
		$average_clicks_per_user = $total_clicks_unique_user / $num_unique_ip;
		$average_clicks_per_user = &format_number($average_clicks_per_user, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
	}
print<<EOM;
		</td></tr>
	<tr bgcolor="#EBEBEB"><td>Total Clicks:</td><TD align="right">$total_clicks</td><TD align="right">$total_clicks_unique_user</td><td>&nbsp;</td></tr>
	<tr bgcolor="#EBEBEB"><td colspan="4" align="right">Unique Users: $num_unique_ip (Average clicks per user: $average_clicks_per_user)</td></tr>
	</table>
	</td></tr>
EOM
	} # END IF

#####################################################################
## END: LOOK UP HOW MANY LINKS ARE BEING TRACKED FOR THE CAMPAIGN
#####################################################################

print<<EOM;
					</TABLE>
EOM
	##############################################
	# START: PRINT CLICK DATA, IF TRACKING URLS
	##############################################
	if ($num_urls_tracked != 0) {
print<<EOM;
<P>
<b>Clicks by date:</b> (mouse-over each bar for details)<br>
<table border="1" cellpadding="0" cellspacing="0">

EOM
my $key;
my $table_row1;
my $table_row2;
my $counter_columns = 1;
my $gif_width = 15;
	foreach $key (sort(keys(%clicks_by_date))) {
		my $key_label = substr($key,4,2);
		   $key_label .= "/";
		   $key_label .= substr($key,6,2);
		   $key_label .= "/";
		   $key_label .= substr($key,0,4);
		$gif_width = "8" if ($clicks_by_date{$key} < 10);
		$gif_width = "15" if ($clicks_by_date{$key} >= 10);
		$gif_width = "20" if ($clicks_by_date{$key} >= 100);
		$table_row1 .= "<td valign=\"bottom\"><IMG SRC=\"/images/bullets/pixel-red.gif\" height=\"$clicks_by_date{$key}\" width=\"$gif_width\" alt=\"$clicks_by_date{$key} clicks on $key_label\" title=\"$clicks_by_date{$key} clicks on $key_label\"></td>";
		$table_row2 .= "<td>$clicks_by_date{$key}</td>";
		$counter_columns++;
	} # END FOREACH

$first_timestamp = &convert_timestamp_2pretty_w_date($first_timestamp, "yes") if ($first_timestamp ne '');
$last_timestamp = &convert_timestamp_2pretty_w_date($last_timestamp, "yes") if ($last_timestamp ne '');
print<<EOM;
<tr>$table_row1</tr>
<tr>$table_row2</tr>
</table>
EOM
print "<br>First click by external user: $first_timestamp" if ($first_timestamp !~ '9999');
print "<br>Last click by external user: $last_timestamp" if ($last_timestamp ne '');
	} # END IF
	##############################################
	# END: PRINT CLICK DATA, IF TRACKING URLS
	##############################################
print<<EOM;
				</TD>
				<TD VALIGN="TOP" class=small><A HREF="email_campaigns.cgi?location=show_sent_log&show_campaign_id=$c_id&show_mine_only=$show_mine_only&show_year=$show_year">Sent log</A>:<BR>
					$c_sentlog
EOM
if ($cookie_ss_staff_id eq 'blitke') {
print<<EOM;
<form action="email_campaigns.cgi" method=POST>
<input type="hidden" name="show_mine_only" value="$show_mine_only">
<input type="hidden" name="show_year" value="$show_year">
<input type="hidden" name="show_campaign_id" value="$c_id">
<input type="hidden" name="location" value="process_delete_campaign">
<input type="submit" name="submit" value="delete">
</form>
EOM
}
print<<EOM;
					</TD></TR>
EOM
} else {
print<<EOM;
			<TR><TD VALIGN="TOP">$c_id</TD>
				<TD VALIGN="TOP"><B><A HREF="email_campaigns.cgi?location=select_campaign&show_campaign_id=$c_id&show_mine_only=$show_mine_only&show_year=$show_year">$c_subject</A></B></TD>
				<TD>$c_createdby</TD>
				<TD>$c_datecreated_pretty</TD></TR>
EOM
}
		}

	if ($num_matches_campaigns ne '0') {
		print "</TABLE>";
	} else {
		print "<P>You do not have any e-mail campaigns on file yet.";
	}
	##############################################
	## START: PRINT REPORT ON CAMPAIGNS BY YEAR
	##############################################
	if ($show_campaign_id eq '') {
print<<EOM;
<P>
<h2>E-Cmapign Manager Summary Report by Year</h2>
<table border="1" cellpadding="3" cellspacing="0">
<tr><td bgcolor="#EBEBEB"><b>Year</b></td><td bgcolor="#EBEBEB"><b># of e-Campaigns</b></td></tr>
EOM
		my $key;
		foreach $key (sort keys %campaigns_peryear) {
			print "<tr><td>$key</td><td>$campaigns_peryear{$key}</td></tr>";
		}
		print "</table>";
	}
	##############################################
	## END: PRINT REPORT ON CAMPAIGNS BY YEAR
	##############################################

	print "<P>Click here for a list of <A HREF=\"email_campaigns.cgi?location=show_users&show_mine_only=$show_mine_only&show_year=$show_year\">SEDL staff who are authorized</A> to access the e-mail campaign manager"; 
}
######################################################################################
## END: LOCATION = SELECT_CAMPAIGN
######################################################################################




######################################################################################
## START: LOCATION = EDIT_CAMPAIGN
######################################################################################
if ($location eq 'edit_campaign') {
	my $next_location = "process_campaign_new";
	
	## SET HOLDING VARIABLES FOR ANY EXISTING DATA FOR THIS CAMPAIGN
	my $c_id = ""; my $c_subject = ""; my $c_textversion = ""; my $c_htmlversion = ""; my $c_sendtextorhtml = ""; my $c_fromaddress = ""; my $c_fromname = ""; my $c_bccaddress = ""; my $c_address_byname = ""; my $c_address_byname_default = ""; my $c_datecreated = ""; my $c_datelastsent = ""; my $c_createdby = ""; my $c_sentlog = "";
	
	## START: IF CAMPAIGN_ID WAS PASSED, READ EXISTING DATA FOR THIS CAMPAIGN
	if ($show_campaign_id ne '') {
		## LOOK UP THE DEFAULT INFORMATION
		my $command = "select * from email_campaigns where c_id like '$show_campaign_id'";
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
			while (my @arr = $sth->fetchrow) {
				($c_id, $c_subject, $c_textversion, $c_htmlversion, $c_sendtextorhtml, $c_fromaddress, $c_fromname, $c_bccaddress, $c_address_byname, $c_address_byname_default, $c_datecreated, $c_datelastsent, $c_createdby, $c_sentlog) = @arr;
				$next_location = "process_campaign_edit"; ## SET PROCESSING OPTION TO PROCESS_CAMPAIGN_EDIT, INSTEAD OF PROCESS_CAMPAIGN_NEW
			} # END DB QUERY LOOP
	} # END IF show_campaign_id ne ''
	## END: IF CAMPAIGN_ID WAS PASSED, READ EXISTING DATA FOR THIS CAMPAIGN
	$c_fromaddress = "$cookie_ss_staff_id\@sedl.org" if ($c_fromaddress eq '');
    $c_datecreated = $date_full_mysql if ($c_datecreated eq '');
    $c_datecreated = &date2standard($c_datecreated);
	$c_id = "<SPAN class=small>The new Campaign ID# will be automatically set when you submit this page.</SPAN>" if ($c_id eq '');


print "<H3>Edit Campaign Settings</H3><P>Use the form below to ";
print "EDIT options for the e-mail campaign you selected." if ($next_location eq 'process_campaign_edit');
print "enter options for a NEW e-mail campaign." if ($next_location eq 'process_campaign_new');

print<<EOM;
<P>
<form action="email_campaigns.cgi" method=POST>
<TABLE CELLPADDING="4" CELLSPACING="0" BORDER="1" BGCOLOR="#EBEBEB">
<TR><TD VALIGN="TOP" WIDTH="50%"><B>Survey ID#</B><BR></TD>
	<TD VALIGN="TOP" WIDTH="50%">$c_id</TD></TR>
<TR><TD VALIGN="TOP"><B>Date Created</B><BR></TD>
	<TD VALIGN="TOP">$c_datecreated</TD></TR>
<TR><TD VALIGN="TOP"><B>E-mail Subject:</B><BR></TD>
	<TD VALIGN="TOP"><textarea name="new_c_subject" rows=2 cols=45>$c_subject</TEXTAREA></TD></TR>
<TR><TD VALIGN="TOP" COLSPAN="2"><B>Text version of e-mail</B><BR>
		<textarea name="new_c_textversion" rows=20 cols=90>$c_textversion</TEXTAREA>
		<P>
EOM
#		DEBUG SHOWING ASCII VALUES OF CHARACTERS IN THIS E-MAIL:
#my @array = split(//, $c_textversion);
#my $counter = 0;
#	while ($counter <= $#array) {
#		my $avalue = ord($array[$counter]); 
#		print "<BR>The character at place $counter is $array[$counter] and has an ASCII value of $avalue";
#		$counter++;
#	}
#
#

print<<EOM;
		</TD></TR>
<TR><TD VALIGN="TOP" COLSPAN="2"><B>HTML version of e-mail</B><BR>
		<textarea name="new_c_htmlversion" rows=20 cols=90>$c_htmlversion</TEXTAREA></TD></TR>
<TR><TD VALIGN="TOP"><B>Send Text only or text with HTML</B><BR>
		<SPAN class=small>
		You may send either a plain text e-mail, or you may send an HTML-enriched e-mail.  
		If you send an HTML e-mail, you must also specify the text version that should be sent, 
		because some users have a preference set for not viewing HTML in their e-mail messages, 
		and in such a case, the text version is shown.</SPAN>
	</TD>
	<TD VALIGN="TOP"><SELECT NAME="new_c_sendtextorhtml">
EOM
	my @options = ("text", "html");
	my $counter = "0";
	while ($counter <= $#options) {
		print "<OPTION VALUE=\"$options[$counter]\"";
		print " SELECTED" if ($options[$counter] eq $c_sendtextorhtml);
		print ">$options[$counter]";
		$counter++;
	} # END WHILE
print<<EOM;
		</SELECT><BR>
		<SPAN class=small>
		Note: If you select "HTML", you will not be allowed to send the HTML 
		message unless you specify a complementary text version to accomodate text-only e-ail users.</SPAN>
	</TD></TR>
<TR><TD VALIGN="TOP"><B>From e-mail address:</B><BR>
		<SPAN class=small>
		This is the real staff member's e-mail address used in the "From" field in the e-mails sent. (example: $cookie_ss_staff_id\@sedl.org)</SPAN></TD>
	<TD VALIGN="TOP"><input name="new_c_fromaddress" size=20 VALUE="$c_fromaddress"><BR>
		<SPAN class=small>
		(example: From: Brian Litke &lt;<FONT COLOR="BLUE">blitke\@sedl.org</FONT>&gt;)</SPAN></TD></TR>
<TR><TD VALIGN="TOP"><B>From name:</B><BR>
		<SPAN class=small>
		This is the real staff member's name used in the "From" field in the e-mails sent.</SPAN></TD>
	<TD VALIGN="TOP"><input name="new_c_fromname" size=20 VALUE="$c_fromname"><BR>
		<SPAN class=small>
		(example: From: <FONT COLOR="BLUE">Brian Litke</FONT> &lt;blitke\@sedl.org&gt;)</SPAN></TD></TR>
<TR><TD VALIGN="TOP"><B>BCC to this address (optional)</B></TD>
	<TD VALIGN="TOP"><input name="new_c_bccaddress" size=20 VALUE="$c_bccaddress"><BR>
		<SPAN class=small>
		(example: Bcc: <FONT COLOR="BLUE">blitke\@sedl.org</FONT>)</SPAN></TD></TR>
<TR><TD VALIGN="TOP"><B>Address mail using recipient's real name?</B><BR>
		<SPAN class=small>
		When this option is set to "yes", the campaign manager will look and try to 
		replace the code<BR>
		&nbsp;&nbsp;_RECIPIENT_FULLNAME_ or<BR> 
		&nbsp;&nbsp;_RECIPIENT_FIRSTNAME_<BR>
		with the recipient's real name or e-mail address in the body of the e-mail when 
		it sends out the e-mail.  So, to implement this feature, 
		you will add one (or more) of these codes to the body of your e-mail message
		where the user's name would normally be placed. 
		(example: Dear _RECIPIENT_FULLNAME_,)
		<P>
		If you use _RECIPIENT_FIRSTNAME_
		only the person's first name will be used.</SPAN></TD>
	<TD VALIGN="TOP">
		<SELECT NAME="new_c_address_byname">
EOM
	my @options = ("no", "yes");
	my $counter = "0";
	while ($counter <= $#options) {
		print "<OPTION VALUE=\"$options[$counter]\"";
		print " SELECTED" if ($options[$counter] eq $c_address_byname);
		print ">$options[$counter]";
		$counter++;
	} # END WHILE
print<<EOM;
		</SELECT>
		<P>
		<SPAN class=small>
		<B>Default user name:</B><BR>
		If an addressee does not have a real name on file, use this name in place of their name. 
		(example as it would appear in the e-mail: Dear <FONT COLOR="BLUE">SEDL Bulletin Recipient</FONT>,)<BR>
		<input name="new_c_address_byname_default" size=20 VALUE="$c_address_byname_default"></SPAN>
		</TD></TR>
<TR><TD VALIGN="TOP"><B>Other merge fields</B></TD>
	<TD VALIGN="TOP"><SPAN class=small>If you are sending a message that will asks the user to log 
		onto a form with their user-ID and password, you merge each user's ID/password into the e-mail 
		by sending the message to a recipient list file that contains those two fields and 
		embedding these codes in your e-mail message where the user ID, password, or organization name should appear.</SPAN>
		<UL><SPAN class=small>
		_RECIPIENT_USERID_<BR>
		_RECIPIENT_PASSWORD_<BR>
		_RECIPIENT_EMAIL_<BR>
		_RECIPIENT_ORGNAME_
		</SPAN></UL>
		</TD></TR>
</TABLE>
  	<UL>
	<input type="hidden" name="show_mine_only" value="$show_mine_only">
	<input type="hidden" name="show_year" value="$show_year">
	<input type="hidden" name="show_campaign_id" value="$show_campaign_id">
	<input type="hidden" name="location" value="$next_location">
	<input type="submit" name="submit" value="Set Options for this Campaign">
	</form>
    </UL>       
EOM
}
######################################################################################
## END: LOCATION = EDIT_CAMPAIGN
######################################################################################

######################################################################################
## START: LOCATION = SHOW_SENT_LOG
######################################################################################
if ($location eq 'show_sent_log') {

	my $command = "select * from email_campaigns_sent where c_sent_campaign_id like '$show_campaign_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";

print<<EOM;
<P>
This campaign has been sent a total of $num_matches times.
<P>
<TABLE CELLPADDING="4" CELLSPACING="0" BORDER="1">
<TR><TD BGCOLOR="#EBEBEB"><B>Date Sent</B></TD>
	<TD BGCOLOR="#EBEBEB"><B>Campaign</B></TD>
	<TD BGCOLOR="#EBEBEB"><B>Sent By</B></TD>
	<TD BGCOLOR="#EBEBEB"><B>Recipient Group</B></TD>
	<TD BGCOLOR="#EBEBEB"><B># Recipients</B></TD>
	<TD BGCOLOR="#EBEBEB"><B>Sent Text or HTML</B></TD></TR>
EOM
		while (my @arr = $sth->fetchrow) {
			my ($c_sent_id, $c_sent_campaign_id, $c_sent_activationid, $c_sent_by, $c_sent_recipients, $c_sent_recipients_count, $c_sent_date, $c_sent_subject, $c_sent_textorhtml, $c_sent_htmlversion, $c_sent_textversion) = @arr;

print<<EOM;
<TR><TD VALIGN="TOP">$c_sent_date</TD>
	<TD VALIGN="TOP">ID#<A HREF="email_campaigns.cgi?location=preview_campaign&show_campaign_id=$show_campaign_id&show_mine_only=$show_mine_only&show_year=$show_year">$c_sent_campaign_id</A><BR>
		$c_sent_subject</TD>
	<TD VALIGN="TOP">$c_sent_by</TD>
	<TD VALIGN="TOP">$c_sent_recipients</TD>
	<TD VALIGN="TOP">$c_sent_recipients_count</TD>
	<TD VALIGN="TOP">$c_sent_textorhtml</TD></TR>
<TR><TD COLSPAN=8>TEXT version:<BR>$c_sent_textversion</TD></TR>
<TR><TD COLSPAN=8>HTML version:<BR>$c_sent_htmlversion</TD></TR>
EOM
		}
print "</TABLE>";
}
######################################################################################
## END: LOCATION = SHOW_SENT_LOG
######################################################################################



######################################################################################
## START: LOCATION = PREVIEW_CAMPAIGN
######################################################################################
if ($location eq 'preview_campaign') {

if ($show_campaign_id eq '') {
	print "<P><FONT COLOR=RED>ERROR: You did not specify an e-mail campaign ID.</FONT><P>";
} else {
	my $command = "select * from email_campaigns where c_id like '$show_campaign_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";

		while (my @arr = $sth->fetchrow) {
			my ($c_id, $c_subject, $c_textversion, $c_htmlversion, $c_sendtextorhtml, $c_fromaddress, $c_fromname, $c_bccaddress, $c_address_byname, $c_address_byname_default, $c_datecreated, $c_datelastsent, $c_createdby, $c_sentlog) = @arr;

		# START: MAKE THE LINKS IN THE E-MAIL TRACKABLE, IF REQUESTED
#		$c_textversion = &make_links_trackable($c_id, $c_textversion); # DON'T TRACK LINKS IN TEXT VERSION (8/30/2007 - per NCDDR REQUEST)
		$c_htmlversion = &make_links_trackable($c_id, $c_htmlversion);
		# START: MAKE THE LINKS IN THE E-MAIL TRACKABLE, IF REQUESTED
			
			my $c_datecreated_pretty = date2standard($c_datecreated);
print <<EOM;
<H3>Preview E-mail</H3>
<P>
Below is a representation of your e-mail campaign.
	<UL>
	<LI><I>Subject:</I> <FONT SIZE="+1"><B>$c_subject</B></FONT>
	<LI><I>Created:</I> $c_datecreated_pretty (<A HREF="email_campaigns.cgi?location=select_campaign&show_campaign_id=$show_campaign_id&show_mine_only=$show_mine_only&show_year=$show_year">campaign overview</A>)
	<LI><I>Edits?</I> Click here to <A HREF="email_campaigns.cgi?location=edit_campaign&show_campaign_id=$show_campaign_id&show_mine_only=$show_mine_only&show_year=$show_year">edit the campaign settings</A>.</B>
	<LI><I>Ready to send?</I> Click here to <A HREF="email_campaigns.cgi?location=showform&show_campaign_id=$show_campaign_id&show_mine_only=$show_mine_only&show_year=$show_year">specify recipients</A>.</B>
	</UL>
<P>
<P>
<B>TEXT VERSION:</B><BR>
<TABLE CELLPADDING=4 BORDER=1 CELLSPACING=0 BGCOLOR="#FFFFFF">
<TR><TD>
EOM


			## START: WARN ABOUT MISSING DATA
			print "<FONT COLOR=RED>WARNING: missing the required \"from address.\"</FONT>" if ($c_fromaddress eq '');
			
			if (check_email($c_fromaddress)) {
			} else {
				print "<FONT COLOR=RED>WARNING: the \"from address\" appears malformed.</FONT>";
			}
			
			print "<FONT COLOR=RED>WARNING: missing the required \"subject.\"</FONT>" if ($c_subject eq '');
			## END: WARN ABOUT MISSING DATA

			## FIX LINEBREAKS IN TEXT VERSION FOR PREVIEW
			$c_textversion =~ s/\n/<BR>/g;

			$c_textversion =~ s/\{RECIPIENT_NAME\}/Jane Doe/g;
			$c_htmlversion =~ s/\{RECIPIENT_NAME\}/Jane Doe/g;
			
print<<EOM;
From: $c_fromname &lt;$c_fromaddress&gt;<BR>
Subject: $c_subject<BR>
Date: $todaysdate<BR>
To: Jane Doe &lt;jdoe\@somewhere.org&gt;<BR>
<P>
$c_textversion
</TD></TR></TABLE>
<BR>
<BR>
<BR>
EOM

			if ($c_sendtextorhtml ne 'text') {
print<<EOM;
<B>HTML VERSION:</B><BR>
<TABLE CELLPADDING=4 BORDER=1 CELLSPACING=0 BGCOLOR="#FFFFFF">
<TR><TD>
From: $c_fromname &lt;$c_fromaddress&gt;<BR>
Subject: $c_subject<BR>
Date: $todaysdate<BR>
To: Jane Doe &lt;jdoe\@somewhere.org&gt;<BR>
<BR><BR><BR>
$c_htmlversion
</TD></TR></TABLE>
EOM

			} # END IF c_sendtextorhtml ne 'text'

		} # DB QUERY LOOP
	} # END IF/ELSE CHECK IF CAMPAIGN ID IS BLANK
}
######################################################################################
## END: LOCATION = PREVIEW_CAMPAIGN
######################################################################################



######################################################################################
## START: LOCATION = TEST_SPAM
######################################################################################
if ($location eq 'test_spam') {

if ($show_campaign_id eq '') {
	print "<P><FONT COLOR=RED>ERROR: You did not specify an e-mail campaign ID.</FONT><P>";
} else {
#	my $command = "select * from email_campaigns where c_id like '$show_campaign_id'";
#	my $dsn = "DBI:mysql:database=intranet;host=localhost";
#	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#	$sth->execute;
#	my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";

#		while (my @arr = $sth->fetchrow) {
#			my ($c_id, $c_subject, $c_textversion, $c_htmlversion, $c_sendtextorhtml, $c_fromaddress, $c_fromname, $c_bccaddress, $c_address_byname, $c_address_byname_default, $c_datecreated, $c_datelastsent, $c_createdby, $c_sentlog) = @arr;
#			my $c_datecreated_pretty = date2standard($c_datecreated);
print <<EOM;
<H3>Test SPAM rating</H3>
<P>
You are encouraged to check the SPAM rating for the content of your e-mail by copying and pasting the content of the e-mail into the forms on these sites.
<P>
	<UL>
	<LI><A HREF="http://www.lyris.com/resources/contentchecker/">http://www.lyris.com/resources/contentchecker/</A></LI>
	<LI>sending your campaign or email to: sales-spamcheck\@sitesell.net?subject=TEST -- you'll get an email response back with a rating of 0 thru 5.</li>
	<LI>List of <A HREF="http://spamassassin.apache.org/tests_3_1_x.html">Tests performed by SPAMAssassin</A>
	</UL>
EOM
#
#my @sites = ("http://www.ezinecheck.com/check.html", "http://www.lyris.com/resources/contentchecker/", "http://www.keywebdata.com/utilities/delivery-checker.asp");
#my @sites_forms;
#$sites_forms[0] = "<h2>Paste Your Text of HTML Copy Here</h2><form action=\"http://www.ezinecheck.com/spam.php\" method=\"POST\"><textarea name=\"letter\" cols=\"50\" rows=\"20\">QQQ</TEXTAREA><P>Copy and paste your copy into this box. It can be in HTML format or standard English. Once you're done, hit Rate It!</textarea><br><br><input type=\"submit\" value=\"Rate it!\"></form></center>";

#$sites_forms[1] = "QQQ";
#$sites_forms[2] = "QQQ";

#			# START: PLAIN TEXT SPAM TEST
#			for (my $i = 0; $i < 1; $i++) {
#				print "<P><B>PLAIN TEXT SPAM TEST FROM $sites[$i]</B><BR>";
#					my $showtext = $sites_forms[$i];
#					$showtext =~ s/QQQ/$c_textversion/g;
#					print "$showtext";
#			} # END FOR
#			# END: PLAIN TEXT SPAM TEST
#			
#			if ($c_sendtextorhtml ne 'text') {
#				# START: HTML SPAM TEST
#				for (my $i = 0; $i < 1; $i++) {
#					print "<P><B>HTML SPAM TEST FROM $sites[$i]</B><BR>";
#						my $showtext = $sites_forms[$i];
#						$showtext =~ s/QQQ/$c_htmlversion/g;
#						print "$showtext";
#				} # END FOR
#				# END: HTML SPAM TEST
#
#			} # END IF c_sendtextorhtml ne 'text'
#
#		} # DB QUERY LOOP
	} # END IF/ELSE CHECK IF CAMPAIGN ID IS BLANK
}
######################################################################################
## END: LOCATION = TEST_SPAM
######################################################################################


######################################################################################
## START: LOCATION = PROCESS_DELETE_CONTACTLIST
######################################################################################
if ($location eq 'process_delete_contactlist') {

	$show_contactlist_id =~ tr/A-Za-z0-9\-.//cd; # REMOVE CHARS NOT ALLOWED IN FILE NAMES

	if ($confirm_action ne 'yes') {

print<<EOM;
<P>
<H3>Confirm Contact List Deletion</H3>
<B>List ID#:</B> <B><font color=red>$show_contactlist_id\.txt</b></font><BR>
<ul>
<form action="email_campaigns.cgi" method=POST>
<input type="checkbox" name="confirm_action" id="confirm_action" value="yes"><label for="confirm_action">Click here to confirm the 
deletion of this contact lists.</label>
<P>
<input type="hidden" name="show_mine_only" value="$show_mine_only">
<input type="hidden" name="show_year" value="$show_year">
<input type="hidden" name="show_campaign_id" value="$show_campaign_id">
<input type="hidden" name="show_contactlist_id" value="$show_contactlist_id">
<input type="hidden" name="location" value="process_delete_contactlist">
<input type="submit" name="submit" value="confirm deletion">
</form>
</ul>

EOM
	} else {
		## PROCESS DELETION OF LIST
		$show_contactlist_id = "$show_contactlist_id\.txt";
		$feedback_message = "Removed contact list: $show_contactlist_id";
		system("rm /home/httpd/html/staff/communications/email_contactlists/$show_contactlist_id");
		$location = "showform";
		
	} # END IF/ELSE
}
######################################################################################
## END: LOCATION = PROCESS_DELETE_CONTACTLIST
######################################################################################


######################################################################################
## START: LOCATION = SHOWFORM
######################################################################################
if ($location eq 'showform') {
	my $command = "select * from email_campaigns where c_id like '$show_campaign_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	my $this_c_subject = "";
	
		while (my @arr = $sth->fetchrow) {
			my ($c_id, $c_subject, $c_textversion, $c_htmlversion, $c_sendtextorhtml, $c_fromaddress, $c_fromname, $c_bccaddress, $c_address_byname, $c_address_byname_default, $c_datecreated, $c_datelastsent, $c_createdby, $c_sentlog) = @arr;
			$this_c_subject = $c_subject;
		}
		
print <<EOM;
<H3>Specify Recipients</H3>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
On this page, you can specify the eventual recipients of your e-mail campaign. (Subject: <A HREF="email_campaigns.cgi?location=select_campaign&show_campaign_id=$show_campaign_id&show_mine_only=$show_mine_only&show_year=$show_year">$this_c_subject</A>)
<P>
After you select one of the options below, you will be taken to a confirmation screen where you may 
review the individual recipient names and e-mail addresses before confirming you are ready to launch your e-mail campaign.
<P>
<HR>
<P>
<FONT COLOR="#957F3A"><B>Option 1:</B></FONT> Testers</B> <I>(select individual test recipients by clicking the box next to their name.)</I><BR>
	<UL>
		<form action="email_campaigns.cgi" method=POST>

		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR><TD VALIGN="TOP" WIDTH="50%">
				<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<TR><TD VALIGN="TOP"><INPUT TYPE="CHECKBOX" NAME="tester14" VALUE="yes"></TD><TD VALIGN="TOP"><B>Afterschool Staff</B> cjordan\@sedl.org, Artie.Stockton\@sedl.org, Deborah.Donnelly\@sedl.org, Joe.Parker\@sedl.org, Laura.Shankland\@sedl.org, Zena.Rudo\@sedl.org</TD></TR>
				<TR><TD VALIGN="TOP"><INPUT TYPE="CHECKBOX" NAME="tester5" VALUE="yes"></TD><TD VALIGN="TOP"><B>Shaila Abdullah</B> Shaila.Abdullah\@sedl.org</TD></TR>
				<TR><TD VALIGN="TOP"><INPUT TYPE="CHECKBOX" NAME="tester7" VALUE="yes"></TD><TD VALIGN="TOP"><B>Leslie Blair</B> Leslie.Blair\@sedl.org</TD></TR>
				<TR><TD VALIGN="TOP"><INPUT TYPE="CHECKBOX" NAME="tester13" VALUE="yes"></TD><TD VALIGN="TOP"><B>Wes Hoover</B> Wes.Hoover\@sedl.org</TD></TR>
				<TR><TD VALIGN="TOP"><INPUT TYPE="CHECKBOX" NAME="tester1" VALUE="yes"></TD><TD VALIGN="TOP"><B>Brian Litke</B> Brian.Litke\@sedl.org, austin_brian\@yahoo.com</TD></TR>
				</TABLE>
			</TD>
			<TD VALIGN="TOP" WIDTH="50%">
				<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<TR><TD VALIGN="TOP"><INPUT TYPE="CHECKBOX" NAME="tester3" VALUE="yes"></TD><TD VALIGN="TOP"><B>NCDDR Testers</B> 
				John.Middleton\@sedl.org, Lin.Harris\@sedl.org, Joann.Starks\@sedl.org, Frank.Martin\@sedl.org</TD></TR>
				<TR><TD VALIGN="TOP"><INPUT TYPE="CHECKBOX" NAME="tester11" VALUE="yes"></TD><TD VALIGN="TOP"><B>Luis Martinez</B> Luis.Martinez\@sedl.org</TD></TR>
				<TR><TD VALIGN="TOP"><INPUT TYPE="CHECKBOX" NAME="tester6" VALUE="yes"></TD><TD VALIGN="TOP"><B>Chris Moses</B> Christine.Moses\@sedl.org</TD></TR>
				<TR><TD VALIGN="TOP"><INPUT TYPE="CHECKBOX" NAME="tester4" VALUE="yes"></TD><TD VALIGN="TOP"><B>Debbie Ritenour</B> Debbie.Ritenour\@sedl.org</TD></TR>
				<TR><TD VALIGN="TOP"><INPUT TYPE="CHECKBOX" NAME="tester10" VALUE="yes"></TD><TD VALIGN="TOP"><B>Laura Shankland</B> Laura.Shankland\@sedl.org</TD></TR>
				<TR><TD VALIGN="TOP"><INPUT TYPE="CHECKBOX" NAME="tester9" VALUE="yes"></TD><TD VALIGN="TOP"><B>Kati Timmons</B> Kati.Timmons\@sedl.org</TD></TR>
				</TABLE>
			</TD>
		</TR>
		</TABLE>
		
		<input type="hidden" name="show_mine_only" value="$show_mine_only">
		<input type="hidden" name="show_year" value="$show_year">
		<input TYPE="hidden" NAME="show_campaign_id" VALUE="$show_campaign_id">
		<input TYPE="hidden" NAME="location" VALUE="showform_confirm">
		<input TYPE="hidden" NAME="recipient_list_name" VALUE="testers">
		<input type="submit" name="submit" value="Click here when you are through selecting the testing group recipients above">
		</form>
	</UL>
<P>
<FONT COLOR="#957F3A"><B>Option 2:</B></FONT> You may send your e-mail campaign to a specific person.  
This will be useful after an initial e-mail sending if you find a person whose e-mail was incorrect and 
you want to re-send to just this one person.
	<UL>
		<form action="email_campaigns.cgi" method=POST>
		<TABLE>
		<TR><TD VALIGN="TOP">E-mail address:</TD>
			<TD><input name="singleuser_email" size=30></TD>
		<TR><TD VALIGN="TOP">Name:</TD><TD><I>first:</I><input name="singleuser_firstname" size=20></TD><TD><I>last:</I><input name="singleuser_lastname" size=20></TD>
		</TABLE>
		<input type="hidden" name="show_mine_only" value="$show_mine_only">
		<input type="hidden" name="show_year" value="$show_year">
		<input TYPE="hidden" NAME="show_campaign_id" VALUE="$show_campaign_id">
		<input TYPE="hidden" NAME="location" VALUE="showform_confirm">
		<input TYPE="hidden" NAME="recipient_list_name" VALUE="single">
		<input type="submit" name="submit" value="Click here when you have entered the single user's name and e-mail">
		</form>	</UL>
<P>
<FONT COLOR="#957F3A"><B>Option 3:</B></FONT> You may send your e-mail campaign to one of SEDL's listservs.
<P>
		<form action="email_campaigns.cgi" method=POST>
		<UL>
		<SELECT NAME="recipient_list_name"><OPTION VALUE="">Select a listserv from this list</OPTION>
EOM
## START: MAKE A PULL-MENU LISTSING ALL LISTSERV NAMES
opendir(DIR, "/home/slist/");
my @files = readdir(DIR); # READ LIST OF FILES INTO AN ARRAY
#   @files = sort(@files); # SORT THEM ALPHABETICALLY
	@files = sort {uc($a) cmp uc($b)} @files; # SORT THEM ALPHABETICALLY - CASE INSENSITIVE
my $counter = "0"; 
my $numerofarrayitems = @files; # COUNT NUMBER OF ITEMS IN THE ARRAY
my $modtime = "";
#my $absdir = "/home/httpd/html/";
	while ($counter <= $numerofarrayitems) {
		if ($files[$counter] !~ '\.') {
			print "<OPTION VALUE=\"listserv_$files[$counter]\"";
			print " SELECTED" if ($files[$counter] eq $cookie_listserv);
			print ">$files[$counter]</OPTION>\n";
		} # END IF
		$counter++;
	} # END WHILE
## END: MAKE A PULL-MENU LISTSING ALL LISTSERV NAMES

print<<EOM;
		</SELECT><P>
		<input type="hidden" name="show_mine_only" value="$show_mine_only">
		<input type="hidden" name="show_year" value="$show_year">
		<input TYPE="hidden" NAME="show_campaign_id" VALUE="$show_campaign_id">
		<input TYPE="hidden" NAME="location" VALUE="showform_confirm">
		<input type="submit" name="submit" value="Click here to select this e-mail list">
		</form>	</UL>
EOM

#print<<EOM;
#	<UL>
#	<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0">
#	<TR><TD>-</TD><TD><A HREF="email_campaigns.cgi?location=showform_confirm&recipient_list_name=listserv_bulletin&show_campaign_id=$show_campaign_id">Bulletin</A></TD></TR>
#	<TR><TD>-</TD><TD><A HREF="email_campaigns.cgi?location=showform_confirm&recipient_list_name=listserv_austin-staff&show_campaign_id=$show_campaign_id">austin-staff</A></TD></TR>
#	<TR><TD>-</TD><TD><A HREF="email_campaigns.cgi?location=showform_confirm&recipient_list_name=listserv_oic-staff&show_campaign_id=$show_campaign_id">oic-staff</A></TD></TR>
#	<TR><TD>-</TD><TD><A HREF="email_campaigns.cgi?location=showform_confirm&recipient_list_name=listserv_sedl-staff&show_campaign_id=$show_campaign_id">sedl-staff</A></TD></TR>
#	<TR><TD>-</TD><TD><A HREF="email_campaigns.cgi?location=showform_confirm&recipient_list_name=listserv_NCDDR_NewsC&show_campaign_id=$show_campaign_id">NCDDR_NewsC</A> (comsumers)</TD></TR>
#	<TR><TD>-</TD><TD><A HREF="email_campaigns.cgi?location=showform_confirm&recipient_list_name=listserv_NCDDR_NewsG&show_campaign_id=$show_campaign_id">NCDDR_NewsG</A> (grantees)</TD></TR>
#	<TR><TD>-</TD><TD><A HREF="email_campaigns.cgi?location=showform_confirm&recipient_list_name=listserv_NCDDR_NewsS&show_campaign_id=$show_campaign_id">NCDDR_NewsS</A> (stakeholders)</TD></TR>
#	</TABLE>
#	</UL>
#<P>
#EOM

	##################################################################################
	## START: READ CONTACT LIST DIRECTORY AND PRINT APPROPRIATE CONTACT FILES
	##################################################################################
print<<EOM;
<P>
<FONT COLOR="#957F3A"><B>Option 4:</B></FONT> You may send your e-mail campaign to a <A HREF="email_c_tip_contactfileformat.html" TARGET="TOP">contact 
list that was uploaded as a file</A>.  Originally, these lists were uploaded by Brian to ensure file integrity; however, you can now upload these files 
yourself using the <a href="/staff/communications/clientlist-upload.cgi">client list upload form</a>.
<p>
<table width="100%" border="0" cellpadding="2" cellspacing="0">
EOM
opendir(DIR, "/home/httpd/html/staff/communications/email_contactlists/");
my @files = readdir(DIR); # READ LIST OF FILES INTO AN ARRAY
   @files = sort(@files); # SORT THEM ALPHABETICALLY
my $counter = $#files; 
my $numerofarrayitems = @files; # COUNT NUMBER OF ITEMS IN THE ARRAY
my $modtime = "";
my $absdir = "/home/httpd/html/staff/communications/email_contactlists";
my $bg_color_row = "";
	while ($counter >= "0") {
			if (($files[$counter] =~ '2') && ($files[$counter] !~ '2eDS')) {
				if ($bg_color_row =~ 'ebebeb') {
					$bg_color_row = " bgcolor=\"#ffffff\"";
				} else {
					$bg_color_row = " bgcolor=\"#ebebeb\"";
				}
				my $list_id_forlink = $files[$counter];
				   $list_id_forlink =~ s/\.txt//gi;
				print "<tr $bg_color_row><td align=\"right\">&#149;</td><td><A HREF=\"email_campaigns.cgi?location=showform_confirm&recipient_list_name=contactfile_$files[$counter]&show_campaign_id=$show_campaign_id&show_mine_only=$show_mine_only&show_year=$show_year\">$files[$counter]</A></td><td>&nbsp;</td><td align=\"right\"><a href=\"email_campaigns.cgi?location=process_delete_contactlist&amp;recipient_list_name=contactfile_$files[$counter]&amp;show_campaign_id=$show_campaign_id&amp;show_mine_only=$show_mine_only&amp;show_year=$show_year&amp;show_contactlist_id=$list_id_forlink\">delete list</a></td>";
			} # END IF
		$counter--;
	} # END WHILE
	##################################################################################
	## END: READ CONTACT LIST DIRECTORY AND PRINT APPROPRIATE CONTACT FILES
	##################################################################################

print<<EOM;
	</table>
<P>
<FONT COLOR="#957F3A"><B>Option 5:</B></FONT> You may send your e-mail campaign to SEDL's Board of Directors.
<P>
	<UL>
	<LI><A HREF="email_campaigns.cgi?location=showform_confirm&recipient_list_name=board_of_directors&show_campaign_id=$show_campaign_id&show_mine_only=$show_mine_only&show_year=$show_year">Board of Directors</A>
	</UL>
EOM
	if ($send_now eq 'yes' && $location ne 'send_campaign') {
		print "<P><FONT COLOR=RED>You forgot to click the checkbox to confirm sending your e-mail.</FONT><P>";
	}
}
######################################################################################
## END: LOCATION = SHOWFORM
######################################################################################

######################################################################################
## START: LOCATION = SHOWFORM_CONFIRM
######################################################################################
if ($location eq 'showform_confirm') {
my @email_suffixes = (); # HOLD A COUNT OF RECIPIENTS IN EACH DOMAIN
my $counter_needscaps = "0";
my $counter_bademail = "0";
my $counter_duplicates = "0";
	if ($recipient_list_name ne '') {


	my $command = "select * from email_campaigns where c_id like '$show_campaign_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	my $this_c_subject = "";
	
		while (my @arr = $sth->fetchrow) {
			my ($c_id, $c_subject, $c_textversion, $c_htmlversion, $c_sendtextorhtml, $c_fromaddress, $c_fromname, $c_bccaddress, $c_address_byname, $c_address_byname_default, $c_datecreated, $c_datelastsent, $c_createdby, $c_sentlog) = @arr;
			$this_c_subject = $c_subject;
		}

print<<EOM;
<form action="email_campaigns.cgi" method=POST>

<H4>IF YOU ARE READY TO SEND THE E-MAIL</H4>
Use this form to send the e-mail campaign: (ID: <A HREF="email_campaigns.cgi?location=preview_campaign&show_campaign_id=$show_campaign_id&show_mine_only=$show_mine_only&show_year=$show_year">$show_campaign_id</A> Subject: <A HREF="email_campaigns.cgi?location=preview_campaign&show_campaign_id=$show_campaign_id&show_mine_only=$show_mine_only&show_year=$show_year">$this_c_subject</A>)
<P>
To send the actual e-mail, click the confirm box and then click the submit button.
<UL>
<form action="email_campaigns.cgi" method=POST>
<input TYPE="checkbox" NAME="location" id="location" VALUE="send_campaign"> <label for="location">Check this box to confirm that you are ready to send to live people.</label>
<input TYPE="hidden" NAME="send_now" VALUE="yes">
<input TYPE="hidden" NAME="recipient_list_name" VALUE="$recipient_list_name">
<INPUT TYPE="HIDDEN" NAME="tester1" VALUE="$tester1">
<INPUT TYPE="HIDDEN" NAME="tester2" VALUE="$tester2">
<INPUT TYPE="HIDDEN" NAME="tester3" VALUE="$tester3">
<INPUT TYPE="HIDDEN" NAME="tester4" VALUE="$tester4">
<INPUT TYPE="HIDDEN" NAME="tester5" VALUE="$tester5">
<INPUT TYPE="HIDDEN" NAME="tester6" VALUE="$tester6">
<INPUT TYPE="HIDDEN" NAME="tester7" VALUE="$tester7">
<INPUT TYPE="HIDDEN" NAME="tester8" VALUE="$tester8">
<INPUT TYPE="HIDDEN" NAME="tester9" VALUE="$tester9">
<INPUT TYPE="HIDDEN" NAME="tester10" VALUE="$tester10">
<INPUT TYPE="HIDDEN" NAME="tester11" VALUE="$tester11">
<INPUT TYPE="HIDDEN" NAME="tester12" VALUE="$tester12">
<INPUT TYPE="HIDDEN" NAME="tester13" VALUE="$tester13">
<INPUT TYPE="HIDDEN" NAME="tester14" VALUE="$tester14">

<INPUT TYPE="HIDDEN" NAME="singleuser_email" VALUE="$singleuser_email">
<INPUT TYPE="HIDDEN" NAME="singleuser_firstname" VALUE="$singleuser_firstname">
<INPUT TYPE="HIDDEN" NAME="singleuser_lastname" VALUE="$singleuser_lastname">
<P>
<input type="hidden" name="show_mine_only" value="$show_mine_only">
<input type="hidden" name="show_year" value="$show_year">
<input TYPE="hidden" NAME="show_campaign_id" VALUE="$show_campaign_id">
<input type="submit" name="submit" value="Click here to trigger an e-mail to the following recipients: ">
</form>
</UL>


<H4>PREVIEW THE LIST OF PEOPLE YOU ARE ABOUT TO SEND TO</H4>
This is the list of people (in the $recipient_list_name group) who you have identified to receive 
the e-mail when you send it.  A check is being done of the user's e-mail address.  
If an address is obviously wrong (missing a "\@" symbol, for instance, it will be 
flagged so you can correct it before sending.
<P>
<OL type = "1">
EOM
		my $counter = "0";
		my $previous_email = "";
		my $previous_name = "";
		while ($counter <= $#recipient_name) {
				## START: COUNT USERS PER DOMAIN	
				my ($email_prefix, $email_suffix) = split(/\@/,$recipient[$counter]);
				$email_suffix = lc($email_suffix);
    			push @email_suffixes, "$email_suffix";
				## END: COUNT USERS PER DOMAIN	
			
			if ($recipient[$counter] ne '') {
				print "<LI><A HREF=\"mailto:$recipient[$counter]\">$recipient_name[$counter]</A> ($recipient[$counter])";

				if (($previous_email eq $recipient[$counter]) && ($previous_name eq $recipient_name[$counter])) {
					print "<font color=red>Same name and e-mail as last addressee</font>";
					$counter_duplicates++;
				}
#				print "<font color=red>Same e-mail as last addressee</font>" if ($previous_email eq $recipient[$counter]);
				my ($em1,$em2,$em3,$em4) = split(/ /,$recipient_name[$counter]);
			
				my $normalcase_name1 = substr($em1, 0, 1);
				my $normalcase_name2 = substr($em2, 0, 1);
				my $normalcase_name3 = substr($em3, 0, 1);
				my $normalcase_name4 = substr($em4, 0, 1);
				my $lowercase_name1 = lc(substr($em1, 0, 1));
				my $lowercase_name2 = lc(substr($em2, 0, 1));
				my $lowercase_name3 = lc(substr($em3, 0, 1));
				my $lowercase_name4 = lc(substr($em4, 0, 1));

				my $uppercase_fullname = uc($recipient_name[$counter]);

				
				
				if (($recipient_name[$counter] eq $uppercase_fullname) && ($uppercase_fullname ne '')
					&& (($recipient_name[$counter] ne ' ') && ($uppercase_fullname ne ' '))
					) {
					$counter_needscaps++;
					if ($recipient_list_name =~ 'listserv') {
						print " <A HREF=\"/staff/computer/email-db-mods.cgi?email=$recipient[$counter]&records_perpage=50&sortby=em&showintro=no&submit=Search\" TARGET=\"TOP\"><FONT COLOR=RED>Click here to fix name capitalization</FONT></A>";
					} else {
						print " <FONT COLOR=RED>There may be a problem with the name capitalization ($recipient_name[$counter]) ($uppercase_fullname) for this user.</FONT></A>";
					}
				}

				if ($recipient_name[$counter] ne 'SEDL Bulletin Recipient') {
					if (
						(($lowercase_name1 eq $normalcase_name1) && ($lowercase_name1 ne '')) ||
						(($lowercase_name2 eq $normalcase_name2) && ($lowercase_name2 ne '')) ||
						(($lowercase_name3 eq $normalcase_name3) && ($lowercase_name3 ne '')) ||
						(($lowercase_name4 eq $normalcase_name4) && ($lowercase_name4 ne '')) ) {
						$counter_needscaps++;
					if ($recipient_list_name =~ 'listserv') {
						print " <A HREF=\"/staff/computer/email-db-mods.cgi?email=$recipient[$counter]&records_perpage=50&sortby=em&showintro=no&submit=Search\" TARGET=\"TOP\"><FONT COLOR=RED>Click here to fix name capitalization</FONT></A>";
					} else {
						print " <FONT COLOR=RED>There may be a problem with the name capitalization for this user.</FONT></A>";
					}
					} # END IF
				}
			
			
				if (check_email($recipient[$counter])) {

				} else {
					if ($recipient[$counter] ne '') {
						print "<P><FONT COLOR=\"RED\">FOUND A BAD ADDRESS: '$recipient[$counter]'</FONT>";
						$counter_bademail++;
					}
				} # END IF - CHECK E-MAIL

			
			}
			$previous_name = $recipient_name[$counter];
			$previous_email = $recipient[$counter];
			$counter++;
		} # END WHILE LOOP
print "</OL><P>\n";
	if ($counter_needscaps ne '0') {
		my $s = "s";
		   $s = "" if ($counter_needscaps eq '1');
		print "<P><FONT COLOR=RED>Warning.  You may need to fix capitalization of $counter_needscaps name$s on your recipient list.</FONT>";
	}

	if ($counter_bademail ne '0') {
		my $s = "s";
		   $s = "" if ($counter_bademail eq '1');
		print "<P><FONT COLOR=RED>Warning.  You may need to fix e-mail syntax for $counter_bademail name$s on your recipient list.</FONT>";
	}
	if ($counter_duplicates ne '0') {
		my $s = "s";
		   $s = "" if ($counter_duplicates eq '1');
		print "<P><FONT COLOR=RED>Warning.  There appear to be $counter_duplicates duplicate$s on your recipient list.</FONT>";
	}

	} # END IF


@email_suffixes = sort(@email_suffixes);
print<<EOM;
<TABLE BORDER="1" CELLPADDING="1" CELLSPACING="1">
<TR><TD COLSPAN=2><B>Below is a list of ISPs/companies used by members of this listserv</B></TD></TR>
<TR><TD><B>Domain</B></TD>
	<TD><B>How many on this list</B></TD></TR>
EOM
my $counter = "0";
my $this_suffix_counter = "0";
my $grand_total = "0";
my $last_suffix = "";
	while ($counter <= $#email_suffixes) {
		if (($last_suffix ne $email_suffixes[$counter]) && ($last_suffix ne '')) {
			print "<TR><TD>$last_suffix</TD><TD>$this_suffix_counter</TD></TR>";
			$grand_total = $grand_total + $this_suffix_counter;
			$this_suffix_counter = "0";
		}
		$this_suffix_counter++;
		$last_suffix = $email_suffixes[$counter];
		$counter++;
	}

$grand_total = $grand_total + $this_suffix_counter;
print<<EOM;
<TR><TD>$last_suffix</TD><TD>$this_suffix_counter</TD></TR>
<TR><TD>Grand Total</TD><TD>$grand_total</TD></TR>
</TABLE>
EOM

}
######################################################################################
## END: LOCATION = SHOWFORM_CONFIRM
######################################################################################



######################################################################################
## START: LOCATION = SEND_CAMPAIGN
######################################################################################
## DECLARE HOLDING VARIABLES
my $this_c_id = "";	my $this_c_subject = "";	my $this_c_textversion = "";	my $this_c_htmlversion = "";	my $this_c_sendtextorhtml = "";	my $this_c_fromaddress = "";	my $this_c_fromname = "";	my $this_c_bccaddress = "";	my $this_c_address_byname = "";	my $this_c_address_byname_default = "";	my $this_c_datecreated = "";	my $this_c_datelastsent = "";	my $this_c_createdby = ""; my $this_c_sentlog = "";


if ($location eq 'send_campaign') {
	my $command = "select * from email_campaigns where c_id like '$show_campaign_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	
		while (my @arr = $sth->fetchrow) {
			my ($c_id, $c_subject, $c_textversion, $c_htmlversion, $c_sendtextorhtml, $c_fromaddress, $c_fromname, $c_bccaddress, $c_address_byname, $c_address_byname_default, $c_datecreated, $c_datelastsent, $c_createdby, $c_sentlog) = @arr;
			$this_c_id = $c_id;
			$this_c_subject = $c_subject;
			$this_c_textversion = $c_textversion;
			$this_c_htmlversion = $c_htmlversion;
			$this_c_sendtextorhtml = $c_sendtextorhtml;
			$this_c_fromaddress = $c_fromaddress;
			$this_c_fromname = $c_fromname;
			$this_c_bccaddress = $c_bccaddress;
			$this_c_address_byname = $c_address_byname;
			$this_c_address_byname_default = $c_address_byname_default;
			$this_c_datecreated = $c_datecreated;
			$this_c_datelastsent = $c_datelastsent;
			$this_c_sentlog = $c_sentlog;
		}
}


if ($location eq 'send_campaign') {

	# START: MAKE THE LINKS IN THE E-MAIL TRACKABLE, IF REQUESTED
#	$this_c_textversion = &make_links_trackable($this_c_id, $this_c_textversion); # DON'T TRACK LINKS IN TEXT VERSION (8/30/2007 - per NCDDR REQUEST)
	$this_c_htmlversion = &make_links_trackable($this_c_id, $this_c_htmlversion);
	# END: MAKE THE LINKS IN THE E-MAIL TRACKABLE, IF REQUESTED


print <<EOM;
<P>
<H4>Sending E-mails</H4>
<P>
EOM

my $number_of_recipients = $#recipient; # COUNTS TOTAL NUMBER OF ITEMS IN THE RECIPIENT LIST
#   $number_of_recipients++; # ADD ONE TO GET THE TRUE COUNT OF ITEMS IN THE ARRAY
my $counter_sent = "0";
	while ($counter_sent <= $number_of_recipients) {
		# RESET MESSAGE TEXT EACH TIME THROUGH LOOP, SINCE WE'RE SUBSTITUTING IN THE USER'S NAME
		my $this_c_textversion_forthisuser = $this_c_textversion;
		my $this_c_htmlversion_forthisuser = $this_c_htmlversion;
	
		################################################
		## START: SEND E-MAILS
		################################################
		my $mailprog = "/usr/sbin/sendmail -t -f$this_c_fromaddress"; #No -n because of webmaster alias

		my $this_fullname = $this_c_address_byname_default;
			$this_fullname = $recipient_name[$counter_sent] if ($recipient_name[$counter_sent] ne '');
		my $this_firstname = $this_c_address_byname_default;
			$this_firstname = $recipient_firstname[$counter_sent] if ($recipient_firstname[$counter_sent] ne '');
		my $this_recipient_email = $recipient[$counter_sent];
		
		my $this_recipient_formatted = "";
			$this_recipient_formatted = $recipient[$counter_sent];
			$this_recipient_formatted = "$recipient_name[$counter_sent] <$recipient[$counter_sent]>";

			## FOR TESTING ONLY - COMMENT THIS OUT LATER ##
#			$this_recipient_formatted = "blitke\@sedl.org";
		my $counter_sent_label = $counter_sent + 1;
		print "<BR> - SENDING TO $counter_sent_label: $recipient_name[$counter_sent] ($recipient[$counter_sent])" if ($recipient[$counter_sent] ne '');

		my $fromaddr = "$this_c_fromname <$this_c_fromaddress>";
		my $replyto = "$this_c_fromname <$this_c_fromaddress>";

	## START: SUBSTITUTE IN THE USER'S FULL NAME, IF REQUESTED IN THE MESSAGE BODY
	if ($this_c_address_byname eq 'yes') {
			$this_c_textversion_forthisuser =~ s/_RECIPIENT_EMAIL_/$this_recipient_email/g;
			$this_c_htmlversion_forthisuser =~ s/_RECIPIENT_EMAIL_/$this_recipient_email/g;

			$this_c_textversion_forthisuser =~ s/_RECIPIENT_FULLNAME_/$this_fullname/g;
			$this_c_htmlversion_forthisuser =~ s/_RECIPIENT_FULLNAME_/$this_fullname/g;

			$this_c_textversion_forthisuser =~ s/_RECIPIENT_FIRSTNAME_/$this_firstname/g;
			$this_c_htmlversion_forthisuser =~ s/_RECIPIENT_FIRSTNAME_/$this_firstname/g;

		## START: REPLACE THESE VARIABLES IF THEY ARE PRESENT
		$this_c_textversion_forthisuser =~ s/_RECIPIENT_PASSWORD_/$recipient_password[$counter_sent]/g;
		$this_c_htmlversion_forthisuser =~ s/_RECIPIENT_PASSWORD_/$recipient_password[$counter_sent]/g;

		$this_c_textversion_forthisuser =~ s/_RECIPIENT_USERID_/$recipient_userid[$counter_sent]/g;
		$this_c_htmlversion_forthisuser =~ s/_RECIPIENT_USERID_/$recipient_userid[$counter_sent]/g;

		$this_c_textversion_forthisuser =~ s/_RECIPIENT_ORGNAME_/$recipient_orgname[$counter_sent]/g;
		$this_c_htmlversion_forthisuser =~ s/_RECIPIENT_ORGNAME_/$recipient_orgname[$counter_sent]/g;
		## END: REPLACE THESE VARIABLES IF THEY ARE PRESENT
	}
	## END: SUBSTITUTE IN THE USER'S FULL NAME, IF REQUESTED IN THE MESSAGE BODY

	
	## ADD CGI/GIF THAT TRACKS WHICH USERS OPEN THE HTML E-MAIL
# DISABLED 2/20/2007 by BL
#	if ($this_c_sendtextorhtml eq 'html') {
#		$this_c_htmlversion_forthisuser =~ s/\<\/body\>/\<IMG SRC=\"http:\/\/www.sedl.org\/cgi-bin\/mysql\/ecampaign-gif.cgi?c=$show_campaign_id\&u=$recipient[$counter_sent]\"\>\<\/body\>/ig;
#	}
		if(check_email($recipient[$counter_sent])) {

open(NOTIFY,"| $mailprog");

print NOTIFY <<EOM;
From: $this_c_fromname <$this_c_fromaddress>
To: $this_recipient_formatted
EOM
if ($recipient_cc[$counter_sent] ne '') {
print NOTIFY <<EOM;
Cc: $recipient_cc[$counter_sent]
EOM
}
if ($this_c_bccaddress ne '') {
print NOTIFY <<EOM;
Bcc: $this_c_bccaddress
EOM
}
print NOTIFY <<EOM;
Reply-To: $replyto
Errors-To: $fromaddr
Subject: $this_c_subject
X-Mailer: Apple Mail (2.752.2)
X-Real-Host-From: $fromaddr
EOM
# X-Mail-Gateway: comment.cgi Mail Gateway 1.0 (removed by BL 3/15/2007)

#	if ($show_campaign_id eq '102') {
#print NOTIFY <<EOM;
#disposition-notification-to: blitke\@sedl.org
#EOM
#	}

	if ($this_c_sendtextorhtml ne 'html') {
	## START: TEXT ONLY E-MAIL
print NOTIFY <<EOM;
MIME-Version: 1.0
Content-Type: text/plain;
     charset="iso-8859-1"
Content-Transfer-Encoding: 8bit

$this_c_textversion_forthisuser
EOM
	## END: TEXT ONLY E-MAIL
	
	} else {
	
	## START: MULTIPART TEXT/HTML E-MAIL
print NOTIFY <<EOM;
MIME-Version: 1.0
Content-Type: multipart/alternative;
     boundary="=_63ea2f8dec26a21f8123a5af4d3cc89f"


--=_63ea2f8dec26a21f8123a5af4d3cc89f
Content-Type: text/plain;
     charset="iso-8859-1"
Content-Transfer-Encoding: 8bit

$this_c_textversion_forthisuser


--=_63ea2f8dec26a21f8123a5af4d3cc89f
Content-Type: text/html;
     charset="iso-8859-1"
Content-Transfer-Encoding: 8bit

$this_c_htmlversion_forthisuser

--=_63ea2f8dec26a21f8123a5af4d3cc89f--

EOM
	}
	## END: MULTIPART TEXT/HTML E-MAIL

close(NOTIFY);

		} else {
			if ($recipient[$counter_sent] ne '') {
				print "<P><FONT COLOR=\"RED\">FOUND A BAD ADDRESS: $recipient[$counter_sent]</FONT>";
			}
		} # END IF - CHECK E-MAIL

			$counter_sent++;
	
	} # END WHILE LOOP
my $s = "s";
   $number_of_recipients++; # ADD ONE TO GET THE TRUE COUNT OF ITEMS IN THE ARRAY
   $s = "" if ($number_of_recipients eq '1');
print<<EOM;
<p class="info">Finished sending messages to the $number_of_recipients recipient$s you specified.</p>
<p>
Click here for the <a href="email_campaigns.cgi?show_mine_only=$show_mine_only&show_year=$show_year">main menu</a>.</p>
EOM
	## BACKSLASH QUOTES BEFORE SAVING TEXT VARIABLES
	$this_c_subject = &backslash_fordb($this_c_subject);
	$this_c_textversion = &backslash_fordb($this_c_textversion);
	$this_c_htmlversion = &backslash_fordb($this_c_htmlversion);

	$this_c_subject = &cleanthis($this_c_subject);
	$this_c_textversion = &cleanthis($this_c_textversion);
	$this_c_htmlversion = &cleanthis($this_c_htmlversion);

	## INSERT RECORD INTO THE email_campaigns_sent DATABASE
	my $command = "INSERT INTO email_campaigns_sent VALUES ('', '$show_campaign_id', '', '$cookie_ss_staff_id', '$recipient_list_name', '$number_of_recipients', '$date_full_mysql', '$this_c_subject', '$this_c_sendtextorhtml', '', '$this_c_textversion')";
#print "<P>COMMAND: $command";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;

	my $recipient_list_name_label = $recipient_list_name;
	   $recipient_list_name_label =~ s/_/ _/gi;
	   $recipient_list_name_label =~ s/-/ -/gi;
	## UPDATE CAMPAIGN RECORD WITH THIS INFORMATION
	my $command = "UPDATE email_campaigns SET c_sentlog = '$todaysdate $cookie_ss_staff_id sent to $recipient_list_name_label ($number_of_recipients)<BR><BR>$this_c_sentlog' WHERE c_id LIKE '$show_campaign_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;


}
######################################################################################
## END: LOCATION = SEND_CAMPAIGN
######################################################################################


################################
## START: LOCATION = TO_DO_LIST
################################
if ($location eq 'to_do_list') {
print<<EOM;
<H2>Programmer's TO DO List</H2>
<P>
The programmer acknowledges this list of potential enhancements to this tool.
	<OL>
	<LI><B>Sending:</B> Set CRON job to send the e-mail campaign at a certain time, or in chunks. (SA 8-25-2005)
	<LI><B>Source for Message Body:</B> Add a field for an HTML page and a field for date updated.  
		If HTML address is present, use that text as the HTML for the message, and check file modification upon looking at that 
		e-mail campaign to determine if the HTML should be updated in the database again. (SA 3-10-2005)
	<LI><B>E-mail Formatting:</B> For Bulletin sendings, check if user wants plain text vs. HTML message. (BL 3-5-2005)
	<LI><B>Restrict access:</B> Only allow the user who created it and that campaign's proxy users to view/edit/send the campaign. (BL 3-5-2005)
	<LI><B>Restrict access:</B> Only allow the user to view/send to their own contact lists (BL 3-5-2005)
	<LI><B>File Uploads:</B> Allow staff to remove or mark old contact lists as inactive (BL 3-5-2005)
	<LI><B>Reporting:</B> Allow campaign sender to specify which links to track clicks for (BL 2-16-2007)
	<LI><B>Reporting:</B> Allow option not to send with embedded tracking GIF (BL 8-2-2005)
	<LI><B>Reporting:</B> Track user e-mail openings more subtley instead of using e-mail address (BL 3-5-2005)
	</OL>
<P>
If you have an enhancement to suggest or request, please send an e-mail to Brian Litke at blitke\@sedl.org or call ext. 6529.
EOM
}
################################
## END: LOCATION = TO_DO_LIST
################################

################################
## START: PRINT PAGE FOOTER
################################
print<<EOM;
<P ALIGN="RIGHT"><A HREF="email_campaigns.cgi?location=to_do_list&show_mine_only=$show_mine_only&show_year=$show_year"><FONT COLOR="#CCCCCC">Programmer's TO DO List</FONT></A></P>
</TD></TR>
</TABLE>
$htmlfooter
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
   return($dirtyitem);
}



####################################################################
## START: COOKIE HANDLING SUBROUTINES
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
## END: COOKIE HANDLING SUBROUTINES
####################################################################



####################################################################
## START: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################
sub backslash_fordb {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\\//gm; # REMOVE PRE-EXISTING BACKSLASHES
   $dirtyitem =~ s/'/\\'/gm;
#   $dirtyitem =~ tr/\146/\\'/gm;
#   $dirtyitem =~ s/&#201D;/\\'/gm;
#   $dirtyitem =~ s/&#201C;/\\'/gm;
   $dirtyitem =~ s/Ô/\\'/gm;
   $dirtyitem =~ s/Õ/\\'/gm;
   $dirtyitem =~ s/Ò/"/gm;
   $dirtyitem =~ s/Ó/"/gm;
   $dirtyitem =~ s/"/\\"/gm;
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

#################################################################
## START SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################
sub date2standard {
my $date2transform = $_[0];
my ($thisyear,$thismonth,$thisdate) = split(/\-/,$date2transform);
   $date2transform = "$thismonth\/$thisdate\/$thisyear";
   $date2transform = "" if $date2transform eq '//';
	return($date2transform);
}
#################################################################
## END SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################


#################################################################
## START SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################
sub make_links_trackable {
	my $show_campaign_id = $_[0];
	my $text_for_message = $_[1];

	#####################################################################
	## START: LOOK UP HOW MANY LINKS ARE BEING TRACKED FOR THE CAMPAIGN
	#####################################################################
	my $command = "SELECT unique_id, url FROM email_campaigns_trackurl WHERE campaign_id = '$show_campaign_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($unique_id, $url) = @arr;
#			print "<BR>REPLACING $url ";
			$url =~ s/\?/\.\*/gi;
#			if ($text_for_message =~ $url) {
#				print "<FONT COLOR=GREEN>FOUND IT</FONT>";
#			}
#			if ($text_for_message =~ 'http://sedl.org/pubs/index.cgi\?l=item&id=read12&smc=b20070216') {
#				print "FOUND PART";
#			}
#			print "<BR>";
			$text_for_message =~ s/$url/http:\/\/www\.sedl\.org\/new\/link\.cgi\?page\=$unique_id/gi;
		}
	#####################################################################
	## END: LOOK UP HOW MANY LINKS ARE BEING TRACKED FOR THE CAMPAIGN
	#####################################################################
	return($text_for_message);
}
#################################################################
## END SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################

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

#!/usr/bin/perl

#####################################################################################################
# Copyright 2008 by Southwest Educational Development Laboratory
#
# This script is used by SEDL Communications staff to manage the SEDL Corporate Web site: Areas of Expertise
# Written by Brian Litke 4-9-2008
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
#my $dbh = DBI->connect($dsn, $database_username, $database_password);
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
my $item_label = "Area of Expertise";
my $site_label = "Areas of Expertise Manager";
my $public_site_address = "http://www.sedl.org/expertise/";

	## START: MYSQL VARIABLES
	my $database_name = "corp";
	my $database_username = "corpuser";
	my $database_password = "public";
	my $database_table_name = "areas_of_expertise";
	my $database_primary_field_name = "ae_id";
	## END: MYSQL VARIABLES

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("388"); # 388 is the PID for this page in the intranet database

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

					$validuser = "yes" if ($ss_staff_id eq 'blitke');
					$validuser = "yes" if ($ss_staff_id eq 'cmoses');
					$validuser = "yes" if ($ss_staff_id eq 'emueller');
					$validuser = "yes" if ($ss_staff_id eq 'jwackwit');
					$validuser = "yes" if ($ss_staff_id eq 'lshankla');
					$validuser = "yes" if ($ss_staff_id eq 'macuna');
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
	$error_message = "ACCESS DENIED: You are not authorized to access the $site_label. Please contact Brian Litke at ext. 6529 for assistance accessing this resource.";
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
<h1 style="margin-top:0px;">$site_label</h1>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>
Welcome to the $site_label. This database is used by Communications staff 
to set up <a href="$public_site_address">$item_label\s</a> for the SEDL Web site. 
Please enter your SEDL user ID and password to view the database.
</p>

<FORM ACTION="areas_of_expertise_manager.cgi" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP"><strong><label for="logon_user">Your user ID</label></strong><br />
	  (ex: sliberty)</TD>
    <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" id="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
<TR><TD VALIGN="TOP" WIDTH="120"><strong><label for="logon_pass">Your intranet password</label></strong><BR>
	<SPAN class="small">(not your e-mail password)</SPAN></TD>
    <TD WIDTH="420" VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" id="logon_pass" SIZE="8"></TD></TR>
</TABLE>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Log In Now">
  </div>
  </FORM>
<P>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
$htmltail
EOM
} 
#################################################################################
## END: PRINT THE LOGON PAGE IF NO USER ID WAS ENTERED
#################################################################################


##########################################################
## START: LOCATION PROCESS_DELETE_ITEM
##########################################################
if ($location eq 'process_delete_item') {
	my $confirm = $query->param("confirm");
	if ($confirm eq 'confirmed') {
	## START: BACKSLASH VARIABLES FOR DB
	$show_record = &cleanformysql($show_record);
	## END: BACKSLASH VARIABLES FOR DB

		## DELETE THE PAGES
		my $command_delete_item = "DELETE from $database_table_name WHERE $database_primary_field_name = '$show_record'";
		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, $database_username, $database_password);
		my $sth = $dbh->prepare($command_delete_item) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#		my $num_matches = $sth->rows;
		
		$feedback_message = "You successfully deleted $item_label record \#$show_record.";
		$location = "menu";
	} else {
		$error_message = "ERROR: $item_label Deletion Aborted. You forgot to checkmark the CONFIRM box.";
		$location = "add_item";
	}
}
##########################################################
## END: LOCATION PROCESS_DELETE_ITEM
##########################################################



#################################################################################
## START: LOCATION = PROCESS_add_item
#################################################################################
	my $new_ae_id_text = $query->param("new_ae_id_text");
	my $new_ae_name = $query->param("new_ae_name");
	my $new_ae_name_short = $query->param("new_ae_name_short");
	   $new_ae_name_short = $new_ae_name if ($new_ae_name_short eq '');
	my $new_ae_active = $query->param("new_ae_active");
	my $new_ae_what_it_is = $query->param("new_ae_what_it_is");
	my $new_ae_at_a_glance = $query->param("new_ae_at_a_glance");
	my $new_ae_at_a_glance_image = $query->param("new_ae_at_a_glance_image");
	my $new_ae_why_it_matters = $query->param("new_ae_why_it_matters");
	my $new_ae_what_we_do = $query->param("new_ae_what_we_do");
	my $new_ae_significant_work = $query->param("new_ae_significant_work");
	my $new_ae_partners = $query->param("new_ae_partners");
	my $new_ae_side_image = $query->param("new_ae_side_image");
	my $new_ae_services = $query->param("new_ae_services");
	my $new_ae_key_staff = $query->param("new_ae_key_staff");
	my $new_ae_key_staff = $query->param("new_ae_key_staff");
	my $new_ae_featured_product = $query->param("new_ae_featured_product");
	my $new_ae_product_services_heading = $query->param("new_ae_product_services_heading");

	my $new_ae_products ="";
	my $productorder_0 = $query->param("productorder_0");
	my $productorder_1 = $query->param("productorder_1");
	my $productorder_2 = $query->param("productorder_2");
	my $productorder_3 = $query->param("productorder_3");
	my $productorder_4 = $query->param("productorder_4");
	my $productorder_5 = $query->param("productorder_5");
	my $productorder_6 = $query->param("productorder_6");
	my $productorder_7 = $query->param("productorder_7");
	my $productorder_8 = $query->param("productorder_8");
	my $productorder_9 = $query->param("productorder_9");
	my $productorder_10 = $query->param("productorder_10");
	my $productorder_11 = $query->param("productorder_11");
	my $productorder_12 = $query->param("productorder_12");
	my $productorder_13 = $query->param("productorder_13");
	my $productorder_14 = $query->param("productorder_14");
	my $productorder_15 = $query->param("productorder_15");
	my $productorder_16 = $query->param("productorder_16");
	my $productorder_17 = $query->param("productorder_17");
	my $productorder_18 = $query->param("productorder_18");
	my $productorder_19 = $query->param("productorder_19");
	my $productorder_20 = $query->param("productorder_20");
		$productorder_0 = "0$productorder_0" if (length($productorder_0) == 1);
		$productorder_1 = "0$productorder_1" if (length($productorder_1) == 1);
		$productorder_2 = "0$productorder_2" if (length($productorder_2) == 1);
		$productorder_3 = "0$productorder_3" if (length($productorder_3) == 1);
		$productorder_4 = "0$productorder_4" if (length($productorder_4) == 1);
		$productorder_5 = "0$productorder_5" if (length($productorder_5) == 1);
		$productorder_6 = "0$productorder_6" if (length($productorder_6) == 1);
		$productorder_7 = "0$productorder_7" if (length($productorder_7) == 1);
		$productorder_8 = "0$productorder_8" if (length($productorder_8) == 1);
		$productorder_9 = "0$productorder_9" if (length($productorder_9) == 1);
		$productorder_10 = "0$productorder_10" if (length($productorder_10) == 1);
		$productorder_11 = "0$productorder_11" if (length($productorder_11) == 1);
		$productorder_12 = "0$productorder_12" if (length($productorder_12) == 1);
		$productorder_13 = "0$productorder_13" if (length($productorder_13) == 1);
		$productorder_14 = "0$productorder_14" if (length($productorder_14) == 1);
		$productorder_15 = "0$productorder_15" if (length($productorder_15) == 1);
		$productorder_16 = "0$productorder_16" if (length($productorder_16) == 1);
		$productorder_17 = "0$productorder_17" if (length($productorder_17) == 1);
		$productorder_18 = "0$productorder_18" if (length($productorder_18) == 1);
		$productorder_19 = "0$productorder_19" if (length($productorder_19) == 1);
		$productorder_20 = "0$productorder_20" if (length($productorder_20) == 1);

	my $product_0 = $query->param("product_0");
	my $product_1 = $query->param("product_1");
	my $product_2 = $query->param("product_2");
	my $product_3 = $query->param("product_3");
	my $product_4 = $query->param("product_4");
	my $product_5 = $query->param("product_5");
	my $product_6 = $query->param("product_6");
	my $product_7 = $query->param("product_7");
	my $product_8 = $query->param("product_8");
	my $product_9 = $query->param("product_9");
	my $product_10 = $query->param("product_10");
	my $product_11 = $query->param("product_11");
	my $product_12 = $query->param("product_12");
	my $product_13 = $query->param("product_13");
	my $product_14 = $query->param("product_14");
	my $product_15 = $query->param("product_15");
	my $product_16 = $query->param("product_16");
	my $product_17 = $query->param("product_17");
	my $product_18 = $query->param("product_18");
	my $product_19 = $query->param("product_19");
	my $product_20 = $query->param("product_20");
	
	## MAKE AN ARRAY TO HOLD THE DATA
	my @productlist;
	push (@productlist, "$productorder_0\_\_$product_0") if ($product_0 ne '');
	push (@productlist, "$productorder_1\_\_$product_1") if ($product_1 ne '');
	push (@productlist, "$productorder_2\_\_$product_2") if ($product_2 ne '');
	push (@productlist, "$productorder_3\_\_$product_3") if ($product_3 ne '');
	push (@productlist, "$productorder_4\_\_$product_4") if ($product_4 ne '');
	push (@productlist, "$productorder_5\_\_$product_5") if ($product_5 ne '');
	push (@productlist, "$productorder_6\_\_$product_6") if ($product_6 ne '');
	push (@productlist, "$productorder_7\_\_$product_7") if ($product_7 ne '');
	push (@productlist, "$productorder_8\_\_$product_8") if ($product_8 ne '');
	push (@productlist, "$productorder_9\_\_$product_9") if ($product_9 ne '');
	push (@productlist, "$productorder_10\_\_$product_10") if ($product_10 ne '');
	push (@productlist, "$productorder_11\_\_$product_11") if ($product_11 ne '');
	push (@productlist, "$productorder_12\_\_$product_12") if ($product_12 ne '');
	push (@productlist, "$productorder_13\_\_$product_13") if ($product_13 ne '');
	push (@productlist, "$productorder_14\_\_$product_14") if ($product_14 ne '');
	push (@productlist, "$productorder_15\_\_$product_15") if ($product_15 ne '');
	push (@productlist, "$productorder_16\_\_$product_16") if ($product_16 ne '');
	push (@productlist, "$productorder_17\_\_$product_17") if ($product_17 ne '');
	push (@productlist, "$productorder_18\_\_$product_18") if ($product_18 ne '');
	push (@productlist, "$productorder_19\_\_$product_19") if ($product_19 ne '');
	push (@productlist, "$productorder_20\_\_$product_20") if ($product_20 ne '');

	## SORT THE ARRAY, TO PUT IT IN NUMERICAL ORDER
	@productlist = sort(@productlist);

	## LOOP THROUGH PRODUCTS TO BUILD THE LIST, REMOVING LEADING NUMBERS
	foreach my $nextproduct (@productlist) {
		$nextproduct = substr($nextproduct,4); # REMOVE LEADING NUMBER AND TWO UNDERSCORES
		$new_ae_products .= "QQQ$nextproduct" if ($nextproduct ne '');
	}

	## REMOVE EXCESS CODING
    $new_ae_products =~ s/ //gi;
    $new_ae_products = "XXXX$new_ae_products";
    $new_ae_products =~ s/XXXXQQQ//gi;
    $new_ae_products =~ s/XXXX//gi;
    
	## START: CHECK FOR DATA COPLETENESS
	if ($location eq 'process_add_item') {
		if ($new_ae_name eq '') {
			$error_message .= "The Area of Expertise Name is missing. Please try again.";
			$location = "add_item";
		} # END IF
	} # END IF
	## END: CHECK FOR DATA COPLETENESS

if ($location eq 'process_add_item') {

	## START: BACKSLASH VARIABLES FOR DB
	#$new_news_date_effective = &cleanformysql($new_news_date_effective);
	$new_ae_id_text = &cleanformysql($new_ae_id_text);
	$new_ae_name = &cleanformysql($new_ae_name);
	$new_ae_name_short = &cleanformysql($new_ae_name_short);
	$new_ae_active = &cleanformysql($new_ae_active);
	$new_ae_what_it_is = &cleanformysql($new_ae_what_it_is);
	$new_ae_at_a_glance = &cleanformysql($new_ae_at_a_glance);
	$new_ae_at_a_glance_image = &cleanformysql($new_ae_at_a_glance_image);
	$new_ae_why_it_matters = &cleanformysql($new_ae_why_it_matters);
	$new_ae_what_we_do = &cleanformysql($new_ae_what_we_do);
	$new_ae_significant_work = &cleanformysql($new_ae_significant_work);
	$new_ae_partners = &cleanformysql($new_ae_partners);
	$new_ae_side_image = &cleanformysql($new_ae_side_image);
	$new_ae_products = &cleanformysql($new_ae_products);
	$new_ae_services = &cleanformysql($new_ae_services);
	$new_ae_key_staff = &cleanformysql($new_ae_key_staff);
	$new_ae_featured_product = &cleanformysql($new_ae_featured_product);
	$new_ae_product_services_heading = &cleanformysql($new_ae_product_services_heading);
	## END: BACKSLASH VARIABLES FOR DB


	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select * from $database_table_name ";
#			if ($show_record ne '') {
				$command .= "WHERE $database_primary_field_name = '$show_record'";
#			}
		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, $database_username, $database_password);
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;
		
		$already_exists = "yes" if ($num_matches_code eq '1');

		my $add_edit_type = "added";
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update_item = "UPDATE $database_table_name
										SET										
										ae_id_text = '$new_ae_id_text',
										ae_name = '$new_ae_name', 
										ae_name_short = '$new_ae_name_short', 
										ae_active = '$new_ae_active', 
										ae_at_a_glance = '$new_ae_at_a_glance', 
										ae_at_a_glance_image = '$new_ae_at_a_glance_image', 
										ae_what_it_is = '$new_ae_what_it_is', 
										ae_why_it_matters = '$new_ae_why_it_matters', 
										ae_what_we_do = '$new_ae_what_we_do', 
										ae_significant_work = '$new_ae_significant_work', 
										ae_partners = '$new_ae_partners', 
										ae_side_image = '$new_ae_side_image', 
										ae_products = '$new_ae_products', 
										ae_services = '$new_ae_services', 
										ae_key_staff = '$new_ae_key_staff',
										ae_featured_product = '$new_ae_featured_product',
										ae_product_services_heading = '$new_ae_product_services_heading',
										ae_last_updated_by = '$cookie_ss_staff_id',
										ae_last_updated = '$timestamp'										
										
										WHERE $database_primary_field_name ='$show_record'";
			my $dbh = DBI->connect($dsn, $database_username, $database_password);
			my $sth = $dbh->prepare($command_update_item) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			#my $num_matches = $sth->rows;
			
			$add_edit_type = "edited";
			$feedback_message .= "The $item_label was $add_edit_type successfully and is highlighted in <a href=\"#$show_record\">YELLOW below</a>.";
			$location = "menu";
		} else {
	
			my $command_insert_item = "INSERT INTO $database_table_name VALUES ('',  '$new_ae_id_text', '$new_ae_name', '$new_ae_name_short', '$new_ae_active', '$new_ae_at_a_glance', '$new_ae_at_a_glance_image', '$new_ae_what_it_is', '$new_ae_why_it_matters', '$new_ae_what_we_do', '$new_ae_significant_work', '$new_ae_partners', '$new_ae_side_image', '$new_ae_products', '$new_ae_services', '$new_ae_key_staff', '$timestamp', '$cookie_ss_staff_id', '$new_ae_featured_product', '$new_ae_product_services_heading')";
			my $dbh = DBI->connect($dsn, $database_username, $database_password);
			my $sth = $dbh->prepare($command_insert_item) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			#my $num_matches = $sth->rows;

			$feedback_message .= "The $item_label was $add_edit_type successfully.";
			$location = "menu";
		} # END IF USER NAME NOT BLANK

## CHECK TO SEE IF WE NEED TO SAVE ANY HTML FILES FOR A NEW AREA OF EXPERTISE
	## QUERY DIRECTORY TO GET THE SAVED FILE NAMES
	opendir(DIR, "/home/httpd/html/expertise/");
	my @files = readdir(DIR);

	my $counter = "0";
	my %files_already_saved; # DECLARE HASH TO REMEMBER WHICH FILES ALREADY ARE SAVED

	## LOOP THOUGH LIST OF FILES
	while ($counter <= $#files) {
		if ($files[$counter] =~ '.html') {
			$files_already_saved{$files[$counter]} = "yes";	
			$counter++;
		} # END IF
		$counter++;
	} # END WHILE

open (EXPERTISE_SIDE_NAVIGATION_INCLUDE,">/home/httpd/html/expertise/side_nav_menu.html");

	## SELECT ALL AREAS_OF_EXPERTISE FROM DATABASE TO GET TARGET HTML FILE NAMES
		my $command = "select ae_id_text, ae_name_short from $database_table_name WHERE ae_active = 'yes' order by ae_name_short";
		my $dbh = DBI->connect($dsn, $database_username, $database_password);
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		while (my @arr = $sth->fetchrow) {
			my ($ae_id_text, $ae_name_short) = @arr;
			my $filename_to_check = "$ae_id_text\.html";
#			if ($files_already_saved{$filename_to_check} ne 'yes') {

print EXPERTISE_SIDE_NAVIGATION_INCLUDE "<li><a href=\"/expertise/$filename_to_check\">$ae_name_short</a></li>";

				open (STATICPAGE,">/home/httpd/html/expertise/$ae_id_text\.html");
				print STATICPAGE "\<\!--\#include virtual=\"/expertise/index.cgi?expertise=$ae_id_text&amp;show_section=overview\" -->";
				close (STATICPAGE);
				open (STATICPAGE,">/home/httpd/html/expertise/$ae_id_text\_services\.html");
				print STATICPAGE "\<\!--\#include virtual=\"/expertise/index.cgi?expertise=$ae_id_text&amp;show_section=services\" -->";
				close (STATICPAGE);
				open (STATICPAGE,">/home/httpd/html/expertise/$ae_id_text\_products\.html");
				print STATICPAGE "\<\!--\#include virtual=\"/expertise/index.cgi?expertise=$ae_id_text&amp;show_section=products\" -->";
				close (STATICPAGE);
				open (STATICPAGE,">/home/httpd/html/expertise/$ae_id_text\_significantwork\.html");
				print STATICPAGE "\<\!--\#include virtual=\"/expertise/index.cgi?expertise=$ae_id_text&amp;show_section=significantwork\" -->";
				close (STATICPAGE);
#			}
		} # END DB QUERY LOOP
	
	

close (EXPERTISE_SIDE_NAVIGATION_INCLUDE);

}
#################################################################################
## END: LOCATION = PROCESS_add_item
#################################################################################


#################################################################################
## START: LOCATION = add_item
#################################################################################
if ($location eq 'add_item') {
	my $page_title = "Add a New $item_label";

	my $ae_id = "";
	my $ae_id_text = "";
	my $ae_name = "";
	my $ae_name_short = "";
	my $ae_active = "";
	my $ae_at_a_glance = "";
	my $ae_at_a_glance_image = "";
	my $ae_what_it_is = "";
	my $ae_why_it_matters = "";
	my $ae_what_we_do = "";
	my $ae_significant_work = "";
	my $ae_partners = "";
	my $ae_side_image = "";
	my $ae_products = "";
	my $ae_services = "";
	my $ae_key_staff = "";
	my $ae_last_updated = "";
	my $ae_last_updated_by = "";
	my $ae_featured_product = "";
	my $ae_product_services_heading = "";
	
	if ($show_record ne '') {
		$page_title = "Save Edits to this $item_label";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from $database_table_name WHERE $database_primary_field_name = '$show_record'";
		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, $database_username, $database_password);
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_pubs = $sth->rows;
		
		while (my @arr = $sth->fetchrow) {
			($ae_id, $ae_id_text, $ae_name, $ae_name_short, $ae_active, $ae_at_a_glance, $ae_at_a_glance_image, $ae_what_it_is, $ae_why_it_matters, $ae_what_we_do, $ae_significant_work, $ae_partners, $ae_side_image, $ae_products, $ae_services, $ae_key_staff, $ae_last_updated, $ae_last_updated_by, $ae_featured_product, $ae_product_services_heading) = @arr;
		} # END DB QUERY LOOP
	
		if ($num_matches_pubs == 0 ) {
			$error_message = "$num_matches_pubs Records Found<br><br>COMMAND: $command";
		}

	} # END IF
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$ae_name = &cleanaccents2html($ae_name);
		$ae_name_short = &cleanaccents2html($ae_name_short);
#		$partner_description = &cleanaccents2html($partner_description);
		$ae_last_updated = &convert_timestamp_2pretty_w_date($ae_last_updated);

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
	mode : "textareas",
	theme : "advanced",
	plugins : "spellchecker,paste",
	gecko_spellcheck : true,
	invalid_elements : "style,span",
   	force_br_newlines : true,
   	force_p_newlines : false,
	forced_root_block : false,
	paste_auto_cleanup_on_paste : true,
 	theme_advanced_toolbar_location : "top",
   	theme_advanced_toolbar_align : "left",
	apply_source_formatting : true,
	theme_advanced_buttons1 : "bold,italic,underline,bullist,numlist,undo,redo,link,unlink,charmap,spellchecker,pastetext,pasteword,cleanup,code,styleselect,formatselect",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	content_css: "/css/sedl2007_forTinyMCE.css",
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

<h1 style="margin-top:0px;"><A HREF="areas_of_expertise_manager.cgi">$site_label</A><br>
$page_title</h1>


<p>The text edit boxes work best in the Firefox browser.</p>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<FORM ACTION="areas_of_expertise_manager.cgi" METHOD="POST" name="form2" id="form2">

<TABLE border="1" cellpadding="2" cellspacing="0" width="100%">
<tr><td valign="top"><strong>Area of Expertise Name</strong></td>
	<td valign="top"><INPUT type="text" name="new_ae_name" size="60" value="$ae_name">
	</td></tr>
<tr><td valign="top"><strong>Area of Expertise Name (short)</strong></td>
	<td valign="top"><INPUT type="text" name="new_ae_name_short" size="60" value="$ae_name_short"><br>
				 This short version isused in the side-navigation menu.  If you leave this blank, the long version will be used.
	</td></tr>
<tr><td valign="top"><strong>Area of Expertise Text ID</strong> </td>
	<td valign="top"><INPUT type="text" name="new_ae_id_text" size="25" value="$ae_id_text"><br>
		(Please enter a SINGLE one-word, which will be used to make a static HTML page with this short text name)
	</td></tr>
<tr><td valign="top"><strong>Active</strong></td>
	<td valign="top">
EOM
&print_yes_no_menu("new_ae_active", $ae_active);
print<<EOM;
	</td></tr>

<tr><td valign="top"><strong>At a Glance</strong></td>
	<td valign="top"><textarea name="new_ae_at_a_glance" rows="30" cols=70>$ae_at_a_glance</textarea>
	</td></tr>
EOM


#<tr><td valign="top"><strong>What It Is</strong></td>
#	<td valign="top"><textarea name="new_ae_what_it_is" rows="15" cols=70>$ae_what_it_is</textarea>
#	</td></tr>

#<tr><td valign="top"><strong>Why It Matters</strong></td>
#	<td valign="top"><textarea name="new_ae_why_it_matters" rows="15" cols=70>$ae_why_it_matters</textarea>
#	</td></tr>

#<tr><td valign="top"><strong>What We Do</strong></td>
#	<td valign="top"><textarea name="new_ae_what_we_do" rows="15" cols=70>$ae_what_we_do</textarea>
#	</td></tr>

print<<EOM;
<tr><td valign="top"><strong>Services</strong></td>
	<td valign="top"><textarea name="new_ae_services" rows="30" cols=70>$ae_services</textarea>
	</td></tr>
EOM
#<tr><td valign="top"><strong>Featured Product</strong></td>
#	<td valign="top"><textarea name="new_ae_featured_product" rows="20" cols=70>$ae_featured_product</textarea>
#	</td></tr>
print<<EOM;
<tr><td valign="top"><strong>Products</strong></td>
	<td valign="top">
EOM
## SHOW 15 PULL-DOWN MENUS WITH PRODUCTS
&display_form_product_menu($ae_products);
print<<EOM;
	</td></tr>

<tr><td valign="top"><strong>Significant Work</strong></td>
	<td valign="top"><textarea name="new_ae_significant_work" rows="50" cols=70>$ae_significant_work</textarea>
	</td></tr>

<tr><td valign="top"><strong>Partners</strong></td>
	<td valign="top"><textarea name="new_ae_partners" rows="15" cols=70>$ae_partners</textarea>
	</td></tr>

<tr><td valign="top"><strong>Key Staff</b</td>
	<td valign="top"><textarea name="new_ae_key_staff" rows="12" cols=70>$ae_key_staff</textarea>
	</td></tr>
EOM
#<tr><td valign="top"><strong>Products & Services Heading</strong></td>
#	<td valign="top">
#EOM
#&print_product_services_heading_menu("new_ae_product_services_heading", $ae_product_services_heading);
#print<<EOM;
#	</td></tr>
print<<EOM;

<tr><td valign="top"><strong>At a Glance Image</strong></td>
	<td valign="top"><INPUT type="text" name="new_ae_at_a_glance_image" size="55" value="$ae_at_a_glance_image">
EOM
if ($ae_at_a_glance_image ne '') {
print<<EOM;
<img src="$ae_at_a_glance_image" alt="" title="This image will be used in the At a Glance section.">
EOM
}
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong>Side Image</strong></td>
	<td valign="top"><INPUT type="text" name="new_ae_side_image" size="55" value="$ae_side_image">
EOM
if ($ae_side_image ne '') {
print<<EOM;
<img src="$ae_side_image" alt="" title="This image will be used in the side of the page template.">
EOM
}
print<<EOM;
	</td></tr>

<tr><td valign="top"><strong>Last Updated By</strong></td>
	<td valign="top">$ae_last_updated</td></tr>
<tr><td valign="top"><strong>Last Updated</strong></td>
	<td valign="top">$ae_last_updated_by</td></tr>

</table>




	<UL>
		<INPUT TYPE=HIDDEN NAME="show_record" VALUE="$show_record">
		<INPUT TYPE=HIDDEN NAME="location" VALUE="process_add_item">
	<INPUT TYPE=SUBMIT VALUE="$page_title">
	</FORM>
	</UL>
</form>
EOM
if ($show_record ne '') {
print<<EOM;
<p>
<table border="0" cellpadding="0" cellsoacing="0" align="right">
<tr><td valign="top">
<div class="first fltRt">
		<FORM ACTION="areas_of_expertise_manager.cgi" METHOD=POST>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr><td colspan="2"><em>Click here to delete this $item_label.</em></td></tr>
		<tr><td valign="top"><input type="checkbox" name="confirm" value="confirmed"></td>
			<td valign="top"><font color=red>confirm the deletion<br /> of this $item_label.</font></td></tr>
		<tr><td colspan="2">
				<input type="hidden" name="location" value="process_delete_item">
				<input type="hidden" name="show_record" value="$show_record">
				<input type="submit" name="submit" value="Delete $item_label"></td></tr>
				
		</table>
		</form>
	
	</div>
	</td></tr>
	</table>
EOM
}
print<<EOM;
</td>
	<td valign="top" align="right">
		(Click here to <A HREF="areas_of_expertise_manager.cgi?location=logout">logout</A>)
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
<tr><td><h1 style="margin-top:0px;"><A HREF="areas_of_expertise_manager.cgi">$site_label</A>
		<br />List of $item_label\s</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="areas_of_expertise_manager.cgi?location=logout">logout</A>)
	</td></tr>
</table>
EOM

	if ($validuser ne 'yes') {
		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view $item_label\s.</FONT>";
	}

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

my $command = "select ae_id, ae_name, ae_name_short, ae_active, ae_last_updated, ae_last_updated_by from $database_table_name";
	$command .= " order by ae_name_short" if $sortby eq '';
	$command .= " order by ae_last_updated DESC, ae_name_short" if $sortby eq 'lastupdated';
	$command .= " order by ae_active, ae_name_short" if $sortby eq 'active';



#print "<P>$command<P>";
$dsn = "DBI:mysql:database=corp;host=localhost";
my $dbh = DBI->connect($dsn, $database_username, $database_password);
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches_items = $sth->rows;

my $col_heading_name = "$item_label";
   $col_heading_name = "<a href=\"areas_of_expertise_manager.cgi\">$item_label</a>" if ($sortby ne '');
my $col_heading_lastupdated = "Last Updated";
   $col_heading_lastupdated = "<a href=\"areas_of_expertise_manager.cgi?sortby=lastupdated\">Last Updated</a>" if ($sortby ne 'lastupdated');
my $col_heading_active = "Active?";
   $col_heading_active = "<a href=\"areas_of_expertise_manager.cgi?sortby=active\">Active?</a>" if ($sortby ne 'active');

print<<EOM;
<P>
There are $num_matches_items $item_label\s on file that are shown on SEDL <a href="$public_site_address" target="_blank">$item_label\s</a> site).
</p>

<FORM ACTION="areas_of_expertise_manager.cgi" METHOD="POST" name="form2" id="form2">
Click here to 
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="add_item">
	<INPUT TYPE=SUBMIT VALUE="Add a New $item_label">
	</FORM>
<P>
EOM


	if ($num_matches_items == 0) {
		print "<P class=\"alert\">There are no records in the database.</p>";
	}
print<<EOM;
<TABLE border="1" cellpadding="3" cellspacing="0">
<TR style="background:#ebebeb">
	<td><strong>#</strong></td>
	<td><strong>$col_heading_name</strong></td>
	<td><strong>$col_heading_lastupdated</strong></td>
	<td><strong>Last Updated By</strong></td>
	<td><strong>$col_heading_active</strong></td>
</TR>
EOM

my $counter = 1;
	while (my @arr = $sth->fetchrow) {
		my ($ae_id, $ae_name, $ae_name_short, $ae_active, $ae_last_updated, $ae_last_updated_by) = @arr;

		my $bgcolor="";
  			$bgcolor="style=\"background:#FFFFCC\"" if ($show_record eq $ae_id);
#		my $description_status = "<font color=\"red\">no</font>";
#		   $description_status = "yes" if ($partner_description ne '');
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$ae_name = &cleanaccents2html($ae_name);
		$ae_last_updated = &convert_timestamp_2pretty_w_date($ae_last_updated);
print<<EOM;
<TR $bgcolor>
	<td valign="top"><a name="$ae_id"></a>$counter</td>
	<td valign="top"><A HREF=\"areas_of_expertise_manager.cgi?location=add_item&amp;show_record=$ae_id\" TITLE="Click to edit this $item_label">$ae_name_short</a></td>
	<td valign="top">$ae_last_updated</td>
	<td valign="top">$ae_last_updated_by</td>
	<td valign="top">$ae_active</td>
</TR>
EOM
		$counter++;
	} # END DB QUERY LOOP
print<<EOM;
</TABLE>
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
## START: SUBROUTINE printform_state
######################################
sub printform_state {
	my $form_variable_name = $_[0];
	my $selected_state = $_[1];
	my $counter_state = "0";
	my @states = ("National", "Regional", "AK", "AL", "AR", "AS", "AZ", "BIA", "CA", "CO", "CT", "DC", "DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "LA", "MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT", "NC", "ND", "NE", "NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VA", "VI", "VT", "WA", "WI", "WV", "WY");

	print "<select name=\"$form_variable_name\" id=\"$form_variable_name\"><option value=\"\"></option>";
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
<SELECT NAME="$field_name" id="$field_name">
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
<SELECT NAME="$field_name" id="$field_name">
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
<SELECT NAME="$field_name" id="$field_name">
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


###########################################################
## START: SUBROUTINE print_product_services_heading_menu
###########################################################
sub print_product_services_heading_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("Products", "Products and Services");
	my @item_label = ("Products", "Products and Services");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name" id="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
###########################################################
} # END: SUBROUTINE print_product_services_heading_menu
###########################################################

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
<SELECT NAME="$field_name" id="$field_name">
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

#############################################################################
## START: SUBROUTINE display_form_product_menu
#############################################################################
sub display_form_product_menu {
	my $previous_values = $_[0];
	my @selected_items = split(/QQQ/,$previous_values);

	my $count_items_displayed = 0;

	## GET MASTER LIST OF PRODUCTS FROM DB
	my @productlist_by_title;

		my $command = "select onlineid, title, title2 from sedlcatalog where isactive LIKE 'y%'";
		   $command .= " order by title";
#print "$command";

		## OPEN THE DATABASE AND SEND THE QUERY
		$dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		my %letters_in_use;
			while (my @arr = $sth->fetchrow) {
		    my ($onlineid, $title, $title2) = @arr;
		    	my $fulltitle = $title;
		    	   $fulltitle = "$title: $title2" if ($title2 ne '');
		    	   while (length($fulltitle) > 60) {
					chop($fulltitle);
		    	   } # END WHILE LOOP
		    	push (@productlist_by_title, "$onlineid QQQ$fulltitle");
			} # END DB QUERY LOOP


	
	## ADD OPTIONS FOR CUSTOM HEADINGS
	unshift (@productlist_by_title, 'Improving School Performance-Heading QQQImproving School Performance Heading'); # unshift adds the new item at the beginning of the array
	unshift (@productlist_by_title, 'Diverse Learners-Heading QQQDiverse Learners Heading'); # unshift adds the new item at the beginning of the array
	unshift (@productlist_by_title, 'CBAM-Heading QQQCBAM Heading'); # unshift adds the new item at the beginning of the array
	unshift (@productlist_by_title, 'Afterschool-Heading QQQAfterschool Heading'); # unshift adds the new item at the beginning of the array
	unshift (@productlist_by_title, 'AfterschoolResourceGuide-RtI QQQAfterschoolResourceGuide-RtI'); # unshift adds the new item at the beginning of the array

	## START: LOOP THROUGH PREVIOUS ITEMS
	while ($count_items_displayed <= $#selected_items) {
		&display_counter_menu($count_items_displayed); # SHOW A PULL-MENU OF NUMBERS TO HELP ORDER PRODUCTS ONSCREEN

		print "<select name=\"product_$count_items_displayed\" id=\"product_$count_items_displayed\">\n<option calue=\"\"></option>\n";

		## DISPLAY ONE OPTION FOR EACH PRODUCT
		my $counter = 0;
		while ($counter <= $#productlist_by_title) {
			my ($this_product_id, $this_title) = split(/ QQQ/,$productlist_by_title[$counter]);
			$this_product_id =~ s/ //gi;
			print "<option value=\"$this_product_id\"";
			print " SELECTED" if ($this_product_id eq $selected_items[$count_items_displayed]);
			print ">$this_title ($this_product_id)</option>\n";
			$counter++;
		} # END WHILE LOOP
		print "</select>\n";
		$count_items_displayed++;
	} # END WHILE LOOP
	## END: LOOP THROUGH PREVIOUS ITEMS

	## START: ADD ADDITIONAL OPTIONS THAT WERE NOT PRE_SELECTED
	while ($count_items_displayed <= 15) {
		&display_counter_menu($count_items_displayed); # SHOW A PULL-MENU OF NUMBERS TO HELP ORDER PRODUCTS ONSCREEN
		print "<select name=\"product_$count_items_displayed\" id=\"product_$count_items_displayed\">\n<option calue=\"\"></option>\n";
		my $counter = 0;
		while ($counter <= $#productlist_by_title) {
			my ($this_product_id, $this_title) = split(/QQQ/,$productlist_by_title[$counter]);
			print "<option value=\"$this_product_id\">$this_title ($this_product_id)</option>\n";
			$counter++;
		} # END WHILE LOOP
		print "</select>\n";
		$count_items_displayed++;
	} # END WHILE LOOP
	## END: ADD ADDITIONAL OPTIONS THAT WERE NOT PRE_SELECTED

}
#############################################################################
## END: SUBROUTINE display_form_product_menu
#############################################################################


#############################################################################
## START: SUBROUTINE display_counter_menu
#############################################################################
sub display_counter_menu {
	my $this_number = $_[0];
		print "<select name=\"productorder_$this_number\" id=\"productorder_$this_number\">\n<option calue=\"\"></option>\n";
		my $counter = 0;
		while ($counter <= 30) {
			print "<option value=\"$counter\"";
			print " SELECTED" if ($counter == ($this_number * 2));
			print ">$counter</option>\n";
			$counter++;
		} # END WHILE LOOP
		print "</select>\n";
}
#############################################################################
## END: SUBROUTINE display_counter_menu
#############################################################################

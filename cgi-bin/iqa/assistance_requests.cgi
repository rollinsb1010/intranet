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
my $item_label = "IQA Technical Assistance Request";
my $site_label = "IQA Technical Assistance Request Manager";
my $public_site_address = "http://www.sedl.org/afterschool/iqa/assistance_request.cgi";

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("503"); # 503 is the PID for this page in the intranet database

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
					$validuser = "yes" if ($ss_staff_id eq 'lwood');
					$validuser = "yes" if ($ss_staff_id eq 'lshankla');
					$validuser = "yes" if ($ss_staff_id eq 'mbaldwin');
		
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
Welcome to the $site_label. This database is used by Illinois Quality Afterschool staff  
to respond to questions submitted via the IQA website. 
Please enter your SEDL user ID and password to view the database.
</p>
<FORM ACTION="assistance_requests.cgi" METHOD="POST">
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


##########################################################
## START: LOCATION PROCESS_DELETE_ITEM
##########################################################
if ($location eq 'process_delete_item') {
	my $confirm = $query->param("confirm");
	if ($confirm eq 'confirmed') {
	## START: BACKSLASH VARIABLES FOR DB
	$show_record = &commoncode::cleanthisfordb($show_record);
	## END: BACKSLASH VARIABLES FOR DB

		## DELETE THE PAGES
		my $command_delete_item = "DELETE from iqa_assistance_request WHERE unique_id = '$show_record'";
		$dsn = "DBI:mysql:database=iqa;host=sedl";
		my $dbh = DBI->connect($dsn, "iqauser", "public");
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
	my $new_firstname = $query->param('new_firstname'); ## THIS GRABS THE VARIABLE PASSED BY THE FORM
	my $new_lastname = $query->param('new_lastname'); ## THIS GRABS THE VARIABLE PASSED BY THE FORM
	my $new_email = $query->param('new_email');
	my $new_phone = $query->param('new_phone');
	my $new_grantee_name_cohort = $query->param('new_grantee_name_cohort');
	my $new_program_sites = $query->param('new_program_sites');
	my @new_grade_level = $query->param('new_grade_level'); # RECEIVE THE DATA AS AN ARRAY
		my $new_grade_level = "";
	
		## START: LOOP THROUGH ARRAY AND ADD EACH ITEM TO A TAB-DELIMITED VARIABLE
		my $counter_array = 0;
		while ($counter_array <= $#new_grade_level) {
			$new_grade_level .= "$new_grade_level[$counter_array]\t";
			$counter_array++;
		} # END WHILE LOOP
		## END: LOOP THROUGH ARRAY AND ADD EACH ITEM TO A TAB-DELIMITED VARIABLE
	
	my $new_priority = $query->param('new_priority');
	my $new_category = $query->param('new_category');
	my $new_question = $query->param('new_question');

	## START: CHECK FOR DATA COPLETENESS
	if ($location eq 'process_add_item') {
		if ($new_firstname eq '') {
			$error_message .= "The First Name is missing. Please try again.";
			$location = "add_item";
		} # END IF
		if ($new_lastname eq '') {
			$error_message .= "The Last Name is missing. Please try again.";
			$location = "add_item";
		} # END IF
		if ($new_email eq '') {
			$error_message .= "The Email is missing. Please try again.";
			$location = "add_item";
		} # END IF
	} # END IF
	## END: CHECK FOR DATA COPLETENESS

if ($location eq 'process_add_item') {

	## START: BACKSLASH VARIABLES FOR DB
	$new_firstname = &commoncode::cleanthisfordb($new_firstname);
	$new_lastname = &commoncode::cleanthisfordb($new_lastname);
	$new_email = &commoncode::cleanthisfordb($new_email);
	$new_phone = &commoncode::cleanthisfordb($new_phone);
	$new_grantee_name_cohort = &commoncode::cleanthisfordb($new_grantee_name_cohort);
	$new_program_sites = &commoncode::cleanthisfordb($new_program_sites);
	$new_grade_level = &commoncode::cleanthisfordb($new_grade_level);
	$new_priority = &commoncode::cleanthisfordb($new_priority);
	$new_category = &commoncode::cleanthisfordb($new_category);
	$new_question = &commoncode::cleanthisfordb($new_question);
	## END: BACKSLASH VARIABLES FOR DB


	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select * from iqa_assistance_request ";
			if ($show_record ne '') {
				$command .= "WHERE unique_id = '$show_record'";
			}
		$dsn = "DBI:mysql:database=iqa;host=localhost";
		my $dbh = DBI->connect($dsn, "iqauser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;
		
		$already_exists = "yes" if ($num_matches_code eq '1');

		my $add_edit_type = "added";
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update = "UPDATE iqa_assistance_request 
										SET firstname = '$new_firstname', lastname = '$new_lastname', email = '$new_email', phone = '$new_phone', grantee_name_cohort = '$new_grantee_name_cohort', program_sites = '$new_program_sites', grade_level = '$new_grade_level', priority = '$new_priority', category = '$new_category', question = '$new_question'
										WHERE unique_id ='$show_record'";
			my $dbh = DBI->connect($dsn, "iqauser", "public");
			my $sth = $dbh->prepare($command_update) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			#my $num_matches = $sth->rows;
			
			$add_edit_type = "edited";
			$feedback_message .= "The $item_label was $add_edit_type successfully and is highlighted in <a href=\"#$show_record\">YELLOW below</a>. $command_update";
			$location = "menu";
		} else {
	
			my $command_insert = "INSERT INTO iqa_assistance_request VALUES ('', '$new_firstname', '$new_lastname', '$new_email', '$new_phone', 
			'$new_grantee_name_cohort', '$new_program_sites', '$new_grade_level', '$new_priority', '$new_category', '$new_question', '$timestamp', 'added-from-intranet', 'added-from-intranet')";
			my $dbh = DBI->connect($dsn, "iqauser", "public");
			my $sth = $dbh->prepare($command_insert) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			#my $num_matches = $sth->rows;

			$feedback_message .= "The $item_label was $add_edit_type successfully. $command_insert";
			$location = "menu";
		} # END IF USER NAME NOT BLANK


}
#################################################################################
## END: LOCATION = PROCESS_add_item
#################################################################################


#################################################################################
## START: LOCATION = add_item
#################################################################################
#if ($location eq 'add_item') {
#	$error_message = "The feature to edit the records has not been built yet.";
#	$location = "menu";
#}

if ($location eq 'add_item') {
	my $page_title = "Add a New $item_label";

	my $unique_id = "";
	my $firstname = "";
	my $lastname = "";
	my $email = "";
	my $phone = "";
	my $grantee_name_cohort = "";
	my $program_sites = "";
	my $grade_level = "";
	my $priority = "";
	my $category = "";
	my $question = "";
	my $datestamp = "";
	my $ipaddress = "";
	my $unique_code = "";

	if ($show_record ne '') {
		$page_title = "Save Edits to this $item_label";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from iqa_assistance_request WHERE unique_id = '$show_record'";
		$dsn = "DBI:mysql:database=iqa;host=localhost";
		my $dbh = DBI->connect($dsn, "iqauser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matching_records = $sth->rows;
		
		while (my @arr = $sth->fetchrow) {
			($unique_id, $firstname, $lastname, $email, $phone, $grantee_name_cohort, $program_sites, $grade_level, $priority, $category, $question, $datestamp, $ipaddress, $unique_code) = @arr;
		} # END DB QUERY LOOP
	
		if ($num_matching_records == 0 ) {
			$error_message = "$num_matching_records Records Found<br><br>COMMAND: $command";
		}

	} # END IF
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
#		$partner_name = &commoncode::cleanaccents2html($partner_name);
#		$partner_description = &commoncode::cleanaccents2html($partner_description);
#		$bod_last_updated = &commoncode::convert_timestamp_2pretty_w_date($bod_last_updated);

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
	content_css: "/css/sedl2012_forTinyMCE.css",
	convert_urls : false
});
</script>

$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td>

<h1><A HREF="assistance_requests.cgi">$site_label</A><br>
$page_title</h1>

EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';


my $selected_grade_level_1;
my $selected_grade_level_2;
my $selected_grade_level_3;
my $selected_grade_level_4;
my $selected_grade_level_5;
	$selected_grade_level_1 = "checked" if ($grade_level =~ 'PreK');
	$selected_grade_level_2 = "checked" if ($grade_level =~ 'K-3');
	$selected_grade_level_3 = "checked" if ($grade_level =~ '4-5');
	$selected_grade_level_4 = "checked" if ($grade_level =~ '6-8');
	$selected_grade_level_5 = "checked" if ($grade_level =~ '9-12');

my $selected_priority_1;
my $selected_priority_2;
my $selected_priority_3;
my $selected_priority_4;
	$selected_priority_1 = "checked" if ($priority =~ 'High Priority Need - Time Sensitive');
	$selected_priority_2 = "checked" if ($priority =~ 'Priority Need - Time Sensitive');
	$selected_priority_3 = "checked" if ($priority =~ 'General Need');
	$selected_priority_4 = "checked" if ($priority =~ 'Low Priority Need');

my $selected_category_1;
my $selected_category_2;
my $selected_category_3;
my $selected_category_4;
my $selected_category_5;
my $selected_category_6;
my $selected_category_7;
	$selected_category_1 = "checked" if ($category =~ 'Management');
	$selected_category_2 = "checked" if ($category =~ 'Evaluation');
	$selected_category_3 = "checked" if ($category =~ 'Programming');
	$selected_category_4 = "checked" if ($category =~ 'Integrating K-12 and Afterschool');
	$selected_category_5 = "checked" if ($category =~ 'Communication');
	$selected_category_6 = "checked" if ($category =~ 'Collaboration');
	$selected_category_7 = "checked" if ($category =~ 'Other');

print<<EOM;      
<FORM ACTION="assistance_requests.cgi" METHOD="POST" name="form2" id="form2">

<table border="1" cellpadding="2" cellspacing="0" width="100%">
<tr><td valign="top"><strong>Name</strong></td>
	<td valign="top">
		<table>
		<tr><td valign="top"><br><br>First:</td><td><textarea name="new_firstname" id="new_firstname" rows="6" cols="70">$firstname</textarea></td></tr>
		<tr><td valign="top"><br><br>Last:</td>
			<td><textarea name="new_lastname" id="new_lastname" rows="6" cols="70">$lastname</textarea></td></tr>
		</table>
	</td></tr>
<tr><td valign="top"><strong>Email</strong></td>
	<td valign="top"><INPUT type="text" name="new_email" id="new_email" size="30" value="$email">
	</td></tr>
<tr><td valign="top"><strong>Phone</strong></td>
	<td valign="top"><INPUT type="text" name="new_phone" id="new_phone" size="30" value="$phone">
	</td></tr>
<tr><td valign="top"><strong>Grantee Name Cohort</strong></td>
	<td valign="top"><INPUT type="text" name="new_grantee_name_cohort" id="new_grantee_name_cohort" size="50" value="$grantee_name_cohort">
	</td></tr>
<tr><td valign="top"><strong>Program Sites</strong></td>
	<td valign="top"><INPUT type="text" name="new_program_sites" id="new_program_sites" size="30" value="$program_sites">
	</td></tr>

<tr><td valign="top"><label for="new_grade_level"><strong>What grade-levels does your program serve?</strong><br><em>(Select all that apply.)</em></label></td>
	<td valign="top">
		<input type="checkbox" name="new_grade_level" id="grade_level_prek" VALUE="PreK" $selected_grade_level_1><label for="grade_level_prek">PreK</label><br>
		<input type="checkbox" name="new_grade_level" id="grade_level_k3" VALUE="K-3" $selected_grade_level_2><label for="grade_level_k3">K-3</label><br>
		<input type="checkbox" name="new_grade_level" id="grade_level_45" VALUE="4-5" $selected_grade_level_3><label for="grade_level_45">4-5</label><br>
		<input type="checkbox" name="new_grade_level" id="grade_level_68" VALUE="6-8" $selected_grade_level_4><label for="grade_level_68">6-8</label><br>
		<input type="checkbox" name="new_grade_level" id="grade_level_912" VALUE="9-12" $selected_grade_level_5><label for="grade_level_912">9-12</label><br>
	</td></tr>


<tr><td valign="top"><label for="new_priority"><strong>Please indicate the level of priority for your request:</strong></label></td>
	<td><input type="radio" name="new_priority" id="priority1" size="50" VALUE="High Priority Need - Time Sensitive (less than 3 weeks)" $selected_priority_1><label for="priority1">High Priority Need - Time Sensitive (less than 3 weeks)</label><br>
		<input type="radio" name="new_priority" id="priority2" size="50" VALUE="Priority Need - Time Sensitive (4 weeks or more)" $selected_priority_2><label for="priority2">Priority Need - Time Sensitive (4 weeks or more)</label><br>
		<input type="radio" name="new_priority" id="priority3" size="50" VALUE="General Need" $selected_priority_3><label for="priority3">General Need</label><br>
		<input type="radio" name="new_priority" id="priority4" size="50" VALUE="Low Priority Need" $selected_priority_4><label for="priority4">Low Priority Need</label>
	</td></tr>

<tr><td valign="top"><label for="new_category"><strong>Please indicate the category of your request:</strong></label></td>
	<td><input type="radio" name="new_category" id="category1" VALUE="Management" $selected_category_1><label for="category1">Management</label><br>
		<input type="radio" name="new_category" id="category2" VALUE="Evaluation" $selected_category_2><label for="category2">Evaluation</label><br>
		<input type="radio" name="new_category" id="category3" VALUE="Programming" $selected_category_3><label for="category3">Programming</label><br>
		<input type="radio" name="new_category" id="category4" VALUE="Integrating K-12 and Afterschool" $selected_category_4><label for="category4">Integrating K-12 and Afterschool</label><br>
		<input type="radio" name="new_category" id="category5" VALUE="Communication" $selected_category_5><label for="category5">Communication</label><br>
		<input type="radio" name="new_category" id="category6" VALUE="Collaboration" $selected_category_6><label for="category6">Collaboration</label><br>
		<input type="radio" name="new_category" id="category7" VALUE="Other"$selected_category_7><label for="category7">Other</label>
	</td></tr>


<tr><td valign="top"><strong>Question</strong></td>
	<td valign="top"><textarea name="new_question" rows="12" cols="70">$question</textarea>
	</td></tr>

</table>



	<div style="margin-left:20px;">
		<INPUT TYPE="HIDDEN" NAME="show_record" VALUE="$show_record">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_add_item">
		<INPUT TYPE="SUBMIT" VALUE="$page_title">
	</div>
</form>
EOM
if ($show_record ne '') {
print<<EOM;
<p>
<table border="0" cellpadding="0" cellsoacing="0" align="right">
<tr><td valign="top">
	<div class="first fltRt">
		<FORM ACTION="assistance_requests.cgi" METHOD="POST">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr><td colspan="2"><em>Click here to delete this $item_label.</em></td></tr>
		<tr><td valign="top"><input type="checkbox" name="confirm" value="confirmed"></td>
			<td valign="top"><font color=red>confirm the deletion<br> of this $item_label.</font></td></tr>
		<tr><td colspan="2">
				<input type="hidden" name="show_inactive" value="$show_inactive">
				<input type="hidden" name="location" value="process_delete_item">
				<input type="hidden" name="show_record" value="$show_record">
				<input type="submit" name="submit" value="Delete $item_label">
			</td>
		</tr>		
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
		(Click here to <A HREF="assistance_requests.cgi?location=logout">logout</A>)
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
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1><A HREF="assistance_requests.cgi">$site_label</A>
		<br>List of $item_label\s</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="assistance_requests.cgi?location=logout">logout</A>)
	</td></tr>
</table>
EOM

	if ($validuser ne 'yes') {
		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view $item_label\s.</FONT>";
	}

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

my $command = "select * from iqa_assistance_request";
#	if ($show_inactive eq '') {
#		$command .= " WHERE bod_active like 'yes'";
#	} else {
#		$command .= " WHERE bod_active like '%'";
#	}
	$command .= " order by datestamp DESC" if ($sortby eq 'date');
	$command .= " order by firstname, lastname" if ($sortby eq 'firstname');
#	$command .= " order by state, datestamp DESC" if ($sortby eq 'state');
#	$command .= " order by datestamp DESC" if ($sortby eq 'active');



#print "<P>$command<P>";
$dsn = "DBI:mysql:database=iqa;host=localhost";
my $dbh = DBI->connect($dsn, "iqauser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches_items = $sth->rows;

my $col_heading_name = "First Name";
   $col_heading_name = "<a href=\"assistance_requests.cgi?sortby=firstname\">First Name</a>" if ($sortby ne 'firstname');
#my $col_heading_state = "State";
#   $col_heading_state = "<a href=\"assistance_requests.cgi?sortby=state\">State</a>" if ($sortby ne 'state');
my $col_heading_date = "Date";
   $col_heading_date = "<a href=\"assistance_requests.cgi?sortby=date\">Date</a>" if ($sortby ne 'date');
my $col_heading_active = "Answered?";
   $col_heading_active = "<a href=\"assistance_requests.cgi?sortby=active\">Answered?</a>" if ($sortby ne 'active');

print<<EOM;
<P>
There are $num_matches_items $item_label\s on file.
</p>
<p>
<FORM ACTION="assistance_requests.cgi" METHOD="POST" name="form2" id="form2">
Click here to 
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="add_item">
	<INPUT TYPE=SUBMIT VALUE="Add a New $item_label">
	</FORM>

</p>
<table border="1" cellpadding="3" cellspacing="0">
<tr bgcolor="#ebebeb">
	<td><strong>#</strong></td>
	<td><strong>$col_heading_name</strong></td>
	<td><strong>Category</strong></td>
	<td><strong>Grade Level</strong></td>
	<td><strong>Priority</strong></td>
	<td><strong>Question</strong></td>
	<td><strong>E-mail/Phone</strong></td>
	<td><strong>$col_heading_date</strong></td>
	<td><strong>$col_heading_active</strong></td>
</tr>
EOM


	if ($num_matches_items == 0) {
		print "<P><FONT COLOR=RED>There are no items in the database.</FONT>";
	}
my $counter = 1;
	while (my @arr = $sth->fetchrow) {
		my ($unique_id, $firstname, $lastname, $email, $phone, $grantee_name_cohort, $program_sites, $grade_level, $priority, $category, $question, $datestamp, $ipaddress, $unique_code) = @arr;


		my $bgcolor="";
#  			$bgcolor="BGCOLOR=\"#cccccc\"" if ($answered eq 'yes');
  			$bgcolor="BGCOLOR=\"#FFFFCC\"" if ($show_record eq $unique_id);

		## MAKE DATESTAMP PRETTY
		$datestamp = &commoncode::convert_timestamp_2pretty_w_date($datestamp, 'yes');
print<<EOM;
<TR $bgcolor>
	<td valign="top"><a name="$unique_id"></a>$counter</td>
	<td valign="top"><A HREF=\"assistance_requests.cgi?location=add_item&amp;show_record=$unique_id\" TITLE="Click to edit this $item_label">$firstname $lastname</a></td>
	<td valign="top" style="font-size:10px;">$category</td>
	<td valign="top" style="font-size:10px;">$grade_level</td>
	<td valign="top" style="font-size:10px;">$priority</td>
	<td valign="top" style="font-size:10px;">$question</td>
	<td valign="top">$email<br>$phone</td>
	<td valign="top">$datestamp</td>
	<td valign="top">future use</td>
</tr>
EOM
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

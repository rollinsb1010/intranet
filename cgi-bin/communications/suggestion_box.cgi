#!/usr/bin/perl

#####################################################################################################
# Copyright 2008 by SEDL
#
# This script is used by OIC to manage online Media Release postings
# Written by Brian Litke 05-09-2007
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
my $item_label = "Suggestion";
my $site_label = "Suggestion Box";
my $public_site_address = "http://www.sedl.org/staff/";

my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $logon_pass = $query->param("logon_pass");

my $uniqueid = param('uniqueid');

my $location = param('location');
   $location = "menu" if $location eq '';

my $requested_location = param('requested_location');
   $requested_location = $location if ($requested_location eq '');
   
my $showsession = param('showsession');


my $error_message = "";
my $feedback_message = "";
my $show_record = $query->param("show_record");
my $sortby = $query->param("sortby");
   $sortby = "date" if ($sortby eq '');
my $detail = $query->param("detail");
   $detail = "full" if ($detail eq '');

my $new_type = $query->param("new_type");
my $submissionid = $query->param("submissionid");

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("311"); # 311 is the PID for this item in the intranet menu system

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

my $is_admin = "no";
   	if (($cookie_ss_staff_id eq 'cmoses') 
	 	|| ($cookie_ss_staff_id eq 'whoover') 
	 	|| ($cookie_ss_staff_id eq 'blitke')) {
		$is_admin = "yes";
	}
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
#		$location = "logon";
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

					$validuser = "yes" if ($ss_staff_id eq 'blitke');
					$validuser = "yes" if ($ss_staff_id eq 'cmoses');
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
#			$location = "logon"; # AFTER LOGOUT, SHOW LOGON SCREEN
		}
	}
######################################################
## END: CHECK SESSION ID AND VERIFY
######################################################


if (($is_admin ne 'yes') && ($validuser ne 'yes') && (($location =~ 'delete') || ($location =~ 'edit') || ($location =~ 'report')) ) {
	$error_message = "ACCESS DENIED: You are not authorized to access administrative functions for the $site_label. Please contact Brian Litke at ext. 6529 for assistance accessing this resource.";
	$location = "menu";
} elsif (($is_admin eq 'yes') && ($validuser ne 'yes') && (($location =~ 'delete') || ($location =~ 'edit') || ($location =~ 'report')) ) {
	$error_message = "PLEASE LOG ON:<br>It appears you are an authorized user, but you are not currently logged in to the intranet. Please log in before using the Suggestion Box's administrative features.";
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

<h1 style="margin-top:0;">$site_label</h1 style="margin-top:0;">
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>
Welcome to the SEDL Staff $site_label.
</p>
<FORM ACTION="suggestion_box.cgi" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
  <TR><TD VALIGN=TOP><strong>Your user ID</strong><br>
  		  (ex: whoover)</TD>
      <TD VALIGN=TOP><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
  <TR><TD VALIGN=TOP WIDTH="120"><strong>Your intranet password</strong><BR>
  			<SPAN class=small>(not your e-mail password)</SPAN></TD>
      <TD VALIGN="TOP"><INPUT TYPE=PASSWORD NAME=logon_pass SIZE=8></TD></TR>
</TABLE>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="show_record" VALUE="$show_record">
  <INPUT TYPE="HIDDEN" NAME="requested_location" VALUE="$requested_location">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
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


##########################################################
## START: LOCATION PROCESS_DELETE_record
##########################################################
if ($location eq 'process_delete_record') {
	my $confirm = $query->param("confirm");
	if ($confirm eq 'confirmed') {
	## START: BACKSLASH VARIABLES FOR DB
	$show_record = &cleanformysql($show_record);
	## END: BACKSLASH VARIABLES FOR DB

		## DELETE THE PAGES
		my $command_delete_pub = "DELETE from suggestion_box WHERE sb_uniqueid = '$show_record'";
		$dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_delete_pub) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		$feedback_message = "You successfully deleted $item_label record \#$show_record.";
		$location = "menu";
	} else {
		$error_message = "ERROR: $item_label Deletion Aborted. You forgot to checkmark the CONFIRM box.";
		$location = "add_record";
	}
}
##########################################################
## END: LOCATION PROCESS_DELETE_record
##########################################################



#################################################################################
## START: LOCATION = PROCESS_ADD_record
#################################################################################
	my $new_sb_title = $query->param("new_sb_title");
	my $new_sb_description = $query->param("new_sb_description");
	my $new_sb_suggestedby = $query->param("new_sb_suggestedby");
	my $new_sb_subject = $query->param("new_sb_subject");

if ($location eq 'process_add_record') {
	## START: CHECK FOR DATA COPLETENESS
	if (($new_sb_title eq '') || ($new_sb_description eq '')) {
		$error_message .= "The $item_label title and/or content are missing. Please try again.";
		$location = "add_record";
	}
	## END: CHECK FOR DATA COPLETENESS
}

if ($location eq 'process_add_record') {

	## START: BACKSLASH VARIABLES FOR DB
	$new_sb_title = &cleanformysql($new_sb_title);
	$new_sb_description = &cleanformysql($new_sb_description);
	$new_sb_suggestedby = &cleanformysql($new_sb_suggestedby);
	$new_sb_subject = &cleanformysql($new_sb_subject);
	## END: BACKSLASH VARIABLES FOR DB

	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select sb_uniqueid from suggestion_box ";
			if ($submissionid eq '') {
				$submissionid = "WARNING_no_id_found";
			}
			$command .= "WHERE sb_submissionid = '$submissionid'";
#		print header;
#		print "<p>CHECK FOR EXISTS:<br>$command<br><p>";
		$dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;

		$already_exists = "yes" if ($num_matches_code eq '1');
#		print header;
#		print "<p>CHECK FOR EXISTS:<br>$command<br>MATCHES: $num_matches_code<p>";
		while (my @arr = $sth->fetchrow) {
			my ($sb_uniqueid) = @arr;
			$show_record = $sb_uniqueid; # REMEMBER THE ID OF THE RECORD
		} # END DB QUERY LOOP
		

my $add_edit_type = "added";
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
#			my $command_update_record = "UPDATE suggestion_box 
#										SET news_item_type='Press Release', news_date_effective='$new_news_date_effective', news_date_expires='$new_news_date_expires', news_item_heading='$new_news_item_heading', news_item_intro='$new_news_item_intro', news_item_content='$new_news_item_content', news_item_focusarea='$new_news_item_focusarea', news_item_footer='$new_news_item_footer'
#										WHERE news_unique_id ='$show_record'";
#			my $dbh = Mysql->connect('localhost', 'intranet', 'intranetuser', 'limited');
#			my $sth = $dbh->query($command_update_record);
#			$add_edit_type = "edited";
			$error_message .= "The $item_label was was <strong>not added</strong>, becase  it appears the record already exists. You probably saw this message after reloading the record submission page.";
			$location = "menu";
		} else {
	
			my $command_insert_record = "INSERT INTO suggestion_box VALUES ('', '$timestamp', '$new_sb_title', '$new_sb_description', '$new_sb_suggestedby', '', '', '', '$submissionid', '$new_sb_subject')";
			$dsn = "DBI:mysql:database=intranet;host=localhost";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command_insert_record) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
#		print header;
#		print "<p class=\"info\">INSERT RECORD CODE:<br>$command_insert_record<p>";

			$feedback_message .= "The $item_label was $add_edit_type successfully. New suggestions are reviewed by SEDL's Communications department before public posting on the intranet.";
			&send_email($new_sb_title, $new_sb_description);
			$location = "menu";
		} # END IF/ELSE
		
}
#################################################################################
## END: LOCATION = PROCESS_ADD_record
#################################################################################


#################################################################################
## START: LOCATION = ADD_record
#################################################################################
if ($location eq 'add_record') {
	my $page_title = "Add this $item_label";

	my $sb_uniqueid = "";
	my $sb_datestamp = "";
	my $sb_title = "";
	my $sb_description = "";
	my $sb_suggestedby = "";
	my $sb_showonsite = "";
	my $sb_mgmt_comment = "";
	my $sb_datestamp_mgmt_comment = "";
	my $sb_submissionid = "";
	my $sb_subject = "";
#	   $sb_title =~ s/"/'/gi;
#	   $sb_description =~ s/"/'/gi;

	if ($show_record ne '') {
		$page_title = "Save Edits to this $item_label";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from suggestion_box WHERE news_unique_id = '$show_record'";
		$dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_pubs = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			($sb_uniqueid, $sb_datestamp, $sb_title, $sb_description, $sb_suggestedby, $sb_showonsite, $sb_mgmt_comment, $sb_datestamp_mgmt_comment, $sb_submissionid, $sb_subject) = @arr;
			$sb_description =~ s///gi;
		} # END DB QUERY LOOP
	}
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
#		$sb_description = &cleanaccents2html($sb_description);


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
	theme_advanced_buttons1 : "bold,italic,underline,bullist,numlist,undo,redo,link,unlink,charmap,spellchecker,pastetext,pasteword,cleanup,code,styleselect",
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


<h1 style="margin-top:0;"><A HREF="suggestion_box.cgi">$site_label</A><br>
$page_title</h1 style="margin-top:0;">
<p>The text edit boxes work best in the Firefox browser.</p>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';
#$sb_title = "" if ($sb_title eq '');
print<<EOM;
<FORM ACTION="suggestion_box.cgi" METHOD="POST" name="form2" id="form2">

<TABLE border="0" cellpadding="8" cellspacing="0" width="100%">
<tr><td valign="top"><strong><label for="new_sb_title">Title of $item_label</label></strong></td>
	<td valign="top">
		<p>
		<input type="text" name="new_sb_title" id="new_sb_title" value="$sb_title" size="60" class="outline_border"><br>
		(e.g. "Suggestion for addressing professional development needs for staff"<br>
				or "Please hold organized trainings for the use of SEDL's new media equipment")
		</p>
	</td></tr>
<tr><td valign="top"><strong><label for="new_sb_description">Please describe your suggestion</label></strong></td>
	<td valign="top"><textarea name="new_sb_description" rows="10" cols="60">$sb_description</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_sb_suggestedby">Suggested by (optional)</label></strong></td>
	<td valign="top"><p><input type="text" name="new_sb_suggestedby" id="new_sb_suggestedby" value="$sb_suggestedby" size="25" class="outline_border"><br>
		Leave blank for an anonymous suggestion. Even if you submit your name, it <strong>WILL NOT BE DISPLAYED</strong> on the suggestion box list of suggestions.</p>
	</td></tr>
</table>
	<div margin-left:25px;>
		<INPUT TYPE="HIDDEN" NAME="sortby" VALUE="$sortby">
		<INPUT TYPE="HIDDEN" NAME="detail" VALUE="$detail">
		<INPUT TYPE="HIDDEN" NAME="submissionid" VALUE="$this_user_id">
		<INPUT TYPE="HIDDEN" NAME="show_record" VALUE="$show_record">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_add_record">
	<INPUT TYPE=SUBMIT VALUE="$page_title">
	</div>
</form>
<p></p>

$htmltail

EOM
}
#################################################################################
## END: LOCATION = ADD_record
#################################################################################


#################################################################################
## START: LOCATION = PROCESS_edit_record
#################################################################################
	my $new_sb_showonsite = $query->param("new_sb_showonsite");
	my $new_sb_mgmt_comment = $query->param("new_sb_mgmt_comment");
	my $new_sb_datestamp_mgmt_comment = $query->param("new_sb_datestamp_mgmt_comment");
	my $new_sb_subject = $query->param("new_sb_subject");

if ($location eq 'process_edit_record') {

	## START: BACKSLASH VARIABLES FOR DB
	$new_sb_showonsite = &cleanformysql($new_sb_showonsite);
	$new_sb_mgmt_comment = &cleanformysql($new_sb_mgmt_comment);
	$new_sb_datestamp_mgmt_comment = &cleanformysql($new_sb_datestamp_mgmt_comment);
	$new_sb_subject = &cleanformysql($new_sb_subject);
	## END: BACKSLASH VARIABLES FOR DB

	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select sb_uniqueid from suggestion_box WHERE sb_uniqueid = '$show_record'";
#		print header;
#		print "<p>CHECK FOR EXISTS:<br>$command<br><p>";
		$dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;

		$already_exists = "yes" if ($num_matches_code eq '1');
#		print header;
#		print "<p>CHECK FOR EXISTS:<br>$command<br>MATCHES: $num_matches_code<p>";

my $add_edit_type = "added";
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update_record = "UPDATE suggestion_box 
										SET sb_showonsite = '$new_sb_showonsite', sb_mgmt_comment = '$new_sb_mgmt_comment', sb_subject = '$new_sb_subject', sb_datestamp_mgmt_comment = '$timestamp' 
										WHERE sb_uniqueid ='$show_record'";
			$dsn = "DBI:mysql:database=intranet;host=localhost";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command_update_record) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			
			$add_edit_type = "edited";
			$feedback_message .= "The $item_label was $add_edit_type successfully and is highlighted in <a href=\"#$show_record\">GREEN below</a>.";
			$location = "menu";
		} else {
	

			$error_message .= "UNEXPECTED ERROR: The $item_label was <strong>not edited</strong>, becase the record ID ($show_record) was not found.";
			$location = "menu";
		} # END IF/ELSE
		
}
#################################################################################
## END: LOCATION = PROCESS_edit_record
#################################################################################

#################################################################################
## START: LOCATION = edit_record
#################################################################################
if ($location eq 'edit_record') {
	my $page_title = "Add this $item_label";

	my $sb_uniqueid = "";
	my $sb_datestamp = "";
	my $sb_title = "";
	my $sb_description = "";
	my $sb_suggestedby = "";
	my $sb_showonsite = "";
	my $sb_mgmt_comment = "";
	my $sb_datestamp_mgmt_comment = "";
	my $sb_submissionid = "";
	my $sb_subject = "";

	my $num_matches_pubs = 0;
	
	if ($show_record ne '') {
		$page_title = "Save Edits to this $item_label";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from suggestion_box WHERE sb_uniqueid = '$show_record'";
		$dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		$num_matches_pubs = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			($sb_uniqueid, $sb_datestamp, $sb_title, $sb_description, $sb_suggestedby, $sb_showonsite, $sb_mgmt_comment, $sb_datestamp_mgmt_comment, $sb_submissionid, $sb_subject) = @arr;
			$sb_description =~ s///gi;
		} # END DB QUERY LOOP
	}

	if ($num_matches_pubs != 1) {
		$error_message = "Unexpected error: Could not find the record matching this item ID ($show_record).";
		$location = "menu";
	} else {
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
	theme_advanced_buttons1 : "bold,italic,underline,bullist,numlist,undo,redo,link,unlink,charmap,spellchecker,pastetext,pasteword,cleanup,code,styleselect",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	content_css: "/css/sedl2007_forTinyMCE.css",
	convert_urls : false
});
</script>
     
$htmlhead

<h1 style="margin-top:0;"><A HREF="suggestion_box.cgi">$site_label</A><br>
$page_title</h1 style="margin-top:0;">
<p>
For best results in the editing box below, use the Firefox browser.
</p>
EOM
#<p>The text edit boxes work best in the Firefox browser.</p>

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';
#$sb_title = "" if ($sb_title eq '');

my $selected1 = "";
my $selected2 = "";
my $selected3 = "";
   $selected1 = "CHECKED" if ($sb_showonsite eq '');
   $selected2 = "CHECKED" if (lc($sb_showonsite) eq 'yes');
   $selected3 = "CHECKED" if (lc($sb_showonsite) eq 'never');

print<<EOM;
<FORM ACTION="suggestion_box.cgi" METHOD="POST" name="form2" id="form2">

<TABLE border="0" cellpadding="8" cellspacing="0" width="100%">
<tr><td valign="top"><strong><label for="new_sb_title">$item_label</label></strong></td>
	<td valign="top">
		<strong>$sb_title</strong><br>
			$sb_description<br>
EOM
	if ($sb_suggestedby ne '') {
		print "(suggested by $sb_suggestedby)<br>";
	}
print<<EOM;
	<img src="/images/spacer.gif" height="1" width="550" alt="">
	</td></tr>
<tr><td valign="top"><strong>Show on intranet to staff?</strong></td>
	<td valign="top">
		
		<input type="radio" name="new_sb_showonsite" id="new_sb_showonsite1" value="" $selected1><label for="new_sb_showonsite1">Pending Review</label><br>
		<input type="radio" name="new_sb_showonsite" id="new_sb_showonsite2" value="yes" $selected2><label for="new_sb_showonsite2">Yes</label><br>
		<input type="radio" name="new_sb_showonsite" id="new_sb_showonsite3" value="never" $selected3><label for="new_sb_showonsite3">Never</label>
	</td></tr>
<tr><td valign="top"><strong><label for="new_sb_mgmt_comment">Management Response to Suggestion</label></strong></td>
	<td valign="top"><textarea name="new_sb_mgmt_comment" rows="24" cols="60">$sb_mgmt_comment</textarea>
	</td></tr>
<tr><td valign="top"><strong><label for="new_sb_subject">Subject</label></strong></td>
	<td valign="top">
EOM
&print_subject_menu("new_sb_subject", $new_sb_subject);

$sb_title =~ s/"/%20/gi;
$sb_description =~ s/"/%20/gi;
print<<EOM;	
	</td></tr>
</table>
	<UL>
		<INPUT TYPE="HIDDEN" NAME="sortby" VALUE="$sortby">
		<INPUT TYPE="HIDDEN" NAME="detail" VALUE="$detail">
		<input type="hidden" name="new_sb_title" value="$sb_title">
		<input type="hidden" name="new_sb_description" value="$sb_description">
		<input type="hidden" name="new_sb_suggestedby" value="$sb_suggestedby">
		
		<INPUT TYPE="HIDDEN" NAME="show_record" VALUE="$show_record">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_edit_record">
	<INPUT TYPE="SUBMIT" VALUE="$page_title">
	</UL>
</form>
<p></p>
$htmltail
EOM
	} # END IF/ELSE
}
#################################################################################
## END: LOCATION = edit_record
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
	<script src="/common/javascript/jquery-min.js" type="text/javascript"></script>	
	<script src="/common/javascript/accordianmenu/menu.js" type="text/javascript"></script>
<TITLE>SEDL Intranet | $site_label: List of $item_label\s</TITLE>
$htmlhead
EOM
#<table Cellpadding="0" cellspacing="0" border="0" width="100%">
#<tr><td>
print<<EOM;
<h1 style="margin-top:0;" style="margin-top:0;"><A HREF="suggestion_box.cgi">$site_label</A></h1 style="margin-top:0;">

<div class="dottedBoxYw">
Colleagues:<br>
<br>
As part of SEDL's effort for continuous improvement, the SEDL Suggestion Box is now open, both on the SEDL intranet site and in the third floor staff lounge. Suggestions for improving processes, procedures, and productivity are welcome. Every area of SEDL's work is open for refinement, and, of course, all suggestions should be submitted in a professional manner.
<br>
<br>
Comments and suggestions submitted to the intranet Suggestion Box will be posted for the entire organization to see on the Suggestion Box bulletin board. Your name will not appear on the posting. Suggestions and comments placed in the box in the staff lounge will also appear on the intranet bulletin board without names. Suggestions will be reviewed monthly and responses will then be posted to each suggestion.
<br>
<br>
Please help SEDL fulfill its mission by suggesting improvements to our systems.
<br>
<br>
Thanks for your participation.<br>
Wes
</div>


EOM
#</td>
#	<td valign="top" align="right">
#		(Click here to <A HREF="suggestion_box.cgi?location=logout">logout</A>)
#	</td></tr>
#</table>
#EOM

	if ($is_admin eq 'yes') {
		print "<p class=\"info\">You are logged in as a suggestion box administrator ($cookie_ss_staff_id). You can view information that is invisible to other staff and can edit records in a way that other staff cannot.</p>";
	}

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

my $command = "select * from suggestion_box";
	 if ($is_admin ne 'yes') {
	 	$command .= " WHERE sb_showonsite not like  'never'";
	}

	$command .= " order by sb_datestamp DESC" if ($sortby eq 'date');
	$command .= " order by sb_subject, sb_datestamp DESC" if ($sortby eq 'subject');



$dsn = "DBI:mysql:database=intranet;host=localhost";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches_items = $sth->rows;

my $isare = "is";
my $s = "";
	if ($num_matches_items != 1) {
		$s = "s";
		$isare = "are";
	}
print<<EOM;

<h1 style="margin-top:0;">List of $item_label\s</h1 style="margin-top:0;">

<FORM ACTION="suggestion_box.cgi" METHOD="POST" name="form1" id="form1">
There $isare $num_matches_items suggestion$s on file 
 sorted by 
EOM
&print_sortby_menu("sortby", $sortby);
print<<EOM;
	<INPUT TYPE=HIDDEN NAME=location VALUE="menu">
	<INPUT TYPE="SUBMIT" VALUE="Re-sort Suggestions">
	</form>

	<FORM ACTION="suggestion_box.cgi" METHOD="POST" name="form2" id="form2">
	Click here to 
	<INPUT TYPE="HIDDEN" NAME="sortby" VALUE="$sortby">
	<INPUT TYPE="HIDDEN" NAME="detail" VALUE="$detail">
	<INPUT TYPE=HIDDEN NAME=location VALUE="add_record">
	<INPUT TYPE=SUBMIT VALUE="Add a New $item_label">
	</FORM>
<p>
<em>Note: New suggestions are reviewed before public posting.</em>
</p>
EOM
#<P>
#<table border="1" cellpadding="3" cellspacing="0">
#<tr bgcolor="#ebebeb">
#	<td><strong>Date</strong></td>
#	<td><strong>Suggestion</strong></td>
#EOM
#	<td><strong>Subject</strong></td>
#	if ($is_admin eq 'yes') {
#print<<EOM;
#	<td><strong>Suggested by</strong></td>
#	<td><strong>Show to staff</strong></td>
#EOM
#}
#print<<EOM;
#</tr>
#EOM


#	if ($num_matches_items == 0) {
#		print "<P class=\"info\">There are no $item_label\s in the database.</p>";
#	}

	if ($sortby eq 'date') {
		print "<ul class=\"menu collapsible\">";
	}


my $counter = 1;
my $last_subject = "";
	while (my @arr = $sth->fetchrow) {
		my ($sb_uniqueid, $sb_datestamp, $sb_title, $sb_description, $sb_suggestedby, $sb_showonsite, $sb_mgmt_comment, $sb_datestamp_mgmt_comment, $sb_submissionid, $sb_subject) = @arr;

		my $bgcolor="";
#   			$bgcolor="BGCOLOR=\"#CCCCCC\"" if (($news_date_expires !~ '0000') && ($news_date_expires lt $mysqldate));
# 			$bgcolor="BGCOLOR=\"#FFFFCC\"" if ($show_record eq $news_unique_id);


		# TRANSFORM DATES INTO PRETTY FORMAT
		$sb_datestamp = &convert_timestamp_2pretty_w_date($sb_datestamp);
		$sb_datestamp_mgmt_comment = &convert_timestamp_2pretty_w_date($sb_datestamp_mgmt_comment);

		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$sb_title = &cleanaccents2html($sb_title);
#		$sb_description = &cleanaccents2html($sb_description);
		
		if ($sb_showonsite eq '') {
			$sb_showonsite = "<a href=\"suggestion_box.cgi?location=edit_record&show_record=$sb_uniqueid&amp;sortby=$sortby&amp;detail=$detail\"><span style=\"color:red\">Please review</span></a>";
 			$bgcolor="BGCOLOR=\"#FFFFCC\"";
		} elsif (lc($sb_showonsite) =~ 'nev') {
   			$bgcolor="BGCOLOR=\"#CCCCCC\"";
			$sb_showonsite = "<a href=\"suggestion_box.cgi?location=edit_record&show_record=$sb_uniqueid&amp;sortby=$sortby&amp;detail=$detail\"><span style=\"color:red\">never</span></a>";
		} elsif (lc($sb_showonsite) =~ 'yes') {
			$sb_showonsite = "<a href=\"suggestion_box.cgi?location=edit_record&show_record=$sb_uniqueid&amp;sortby=$sortby&amp;detail=$detail\"><span style=\"color:green\">yes</span></a>";
		}
		
		if (($show_record ne '') && ($show_record eq $sb_uniqueid)) {
  			$bgcolor="BGCOLOR=\"#CCFFCC\"";
		}

		$sb_suggestedby = "anonymous" if ($sb_suggestedby eq '');
		
		## START: HANDLE BRIEF DISPLAY
		if ($detail eq 'brief') {
			$sb_description = "";
			$sb_mgmt_comment = "";
		}
		## END: HANDLE BRIEF DISPLAY
		
		my $this_num_cols = 2;
		   $this_num_cols = 4 if ($is_admin eq 'yes');
		if (($sortby eq 'subject') && ($last_subject ne $sb_subject)) {
			print "</ul>" if ($last_subject ne '');
			print "\n<h2>$sb_subject\s</h2>\n<ul class=\"menu noaccordion\">\n";
		}
		$last_subject = $sb_subject;
		$sb_showonsite =~ s/yes/answered/gi if ($sb_showonsite =~ 'yes');
#	<td valign="top">$sb_subject</td>
print<<EOM;
<li $bgcolor id="$sb_uniqueid">
EOM
	if ($is_admin eq 'yes') {
		print "<div style=\"width:100px;float:right;\">$sb_showonsite</div>";
	}
	else {
		if ($sb_showonsite =~ 'answered') {
			print "<div style=\"width:100px;float:right;\">answered</div>";
		} else {
			print "<div style=\"width:100px;float:right;\">pending review</div>";
		}
	}

print<<EOM;
<a href="#">$sb_title</a> ($sb_datestamp)
EOM
print "<ul class=\"acitem\">
				<li>$sb_description";
if ($sb_mgmt_comment ne '') {
print<<EOM;
<p></p>
<p><strong>Management response:</strong><br>$sb_mgmt_comment</p>
EOM
#<p style="color:#5D6921"><strong>Management response:</strong><br>$sb_mgmt_comment ($sb_datestamp_mgmt_comment)</p>

}
#print<<EOM;
#	</td>
#EOM
print<<EOM;
	</li>
	</ul>
</li>
EOM
		$counter++;
	} # END DB QUERY LOOP
print<<EOM;
</ul>
<p>
<br>
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
## START: SUBROUTINE print_subject_menu
######################################
sub print_subject_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];

	my @item_value = ("", "Benefits suggestion", "Building suggestion", "Business suggestion", "Career advancement suggestion", "Parking suggestion", "Support Services suggestion");
	my @item_label = ("select one", "Benefits suggestion", "Building suggestion", "Business suggestion", "Career advancement suggestion", "Parking suggestion", "Support Services suggestion");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection eq $item_value[$item_counter]);
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_subject_menu
######################################


######################################
## START: SUBROUTINE print_detail_menu
######################################
sub print_detail_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];

	my @item_value = ("full", "brief");
	my @item_label = ("Suggestion and Response", "Suggestion only");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection eq $item_value[$item_counter]);
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_detail_menu
######################################


######################################
## START: SUBROUTINE print_sortby_menu
######################################
sub print_sortby_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];

	my @item_value = ("date", "subject");
	my @item_label = ("date", "subject");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection eq $item_value[$item_counter]);
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_sortby_menu
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
## START: SUBROUTINE cleanaccents2html
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
######################################
## END: SUBROUTINE cleanaccents2html
######################################


######################################
## START: SUBROUTINE send_email
######################################
sub send_email {
	my $suggestion_title = $_[0];
	my $suggestion_description = $_[1];
	
	## SET MAIL NOTIFICATION VARIABLES
	my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
	my $recipient = 'christine.moses@sedl.org';
#	   $recipient = 'blitke@sedl.org'; # FOR TESTING ONLY - COMMENT OUT WHEN SITE IS LIVE
	my $fromaddr = 'webmaster@sedl.org';

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: SEDL Staff Suggestion Box submission

A staff member added a suggestion to the suggestion box:
http://www.sedl.org/staff/communications/suggestion_box.cgi

Please log on and indicate if the suggestion should be shown online.

Suggestion:
$suggestion_title

Detailed description:
$suggestion_description



This e-mail was auto-generated by the SEDL Suggestion Box script at
http://www.sedl.org/staff/communications/suggestion_box.cgi

Contact Brian Litke at ext. 6529 if you require assistance.
EOM
close(NOTIFY);
}
######################################
## END: SUBROUTINE send_email
######################################

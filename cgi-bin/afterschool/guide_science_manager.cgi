#!/usr/bin/perl

#####################################################################################################
# Copyright 2007 by Southwest Educational Development Laboratory
#
# This script is used by Afterschool to manage the online database of science curricula
# Written by Brian Litke 05-09-2007
#####################################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=afterschool;host=localhost";
#my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;
use Number::Format; # ROUNDS NUMBERS TO SPECIFIC DECIMAL PLACE
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
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################

   
########################################
## START: READ VARIABLES PASSED BY USER
########################################
my $item_label = "Science Guide Review";
my $site_label = "Afterschool Science Curriculum Guide";
my $public_site_address = "http://www.sedl.org/afterschool/guide/science/";

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
	$show_record = &cleanformysql($show_record);
my $show_review_record = $query->param("show_review_record");
	$show_review_record = &cleanformysql($show_review_record);

my $sortby = $query->param("sortby");
   $sortby = "title" if ($sortby eq '');
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
				my $dsn = "DBI:mysql:database=intranet;host=localhost";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;

				$validuser = "yes" if ($ss_staff_id eq 'blitke');
				$validuser = "yes" if ($ss_staff_id eq 'cjordan');
				$validuser = "yes" if ($ss_staff_id eq 'ddonnel');
				$validuser = "yes" if ($ss_staff_id eq 'jparker');
				$validuser = "yes" if ($ss_staff_id eq 'lshankla');
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
<TITLE>SEDL Intranet | $site_label Database Manager</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead

<h1>$site_label Database Manager</h1>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>
Welcome to the $site_label Database Manager. This database is used by Afterschool staff (Cathy, Deborah, Joe, Laura, Brian, Shaila) 
to add/edit the <a href="$public_site_address">Afterschool Science Guide Database</a> for the SEDL Web site. 
Please enter your SEDL user ID and password to view the database.
</p>
<FORM ACTION="guide_science_manager.cgi" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP"><strong>Your user ID</strong><br>
  		  (ex: sliberty)</TD>
      <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
<TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><BR>
  			<SPAN class="small">(not your e-mail password)</SPAN></TD>
      <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD></TR>
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
## START: LOCATION PROCESS_delete_review
##########################################################
if ($location eq 'process_delete_review') {
	my $confirm = $query->param("confirm");
	if ($confirm eq 'confirmed') {
	## START: BACKSLASH VARIABLES FOR DB
	$show_review_record = &cleanformysql($show_review_record);
	## END: BACKSLASH VARIABLES FOR DB

		## DELETE THE PAGES
		my $command_delete_pub = "DELETE from sci_reviews_2008 WHERE lp_unique_id = '$show_review_record'";
		my $dsn = "DBI:mysql:database=afterschool;host=localhost";
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command_delete_pub) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		$feedback_message = "You successfully deleted $item_label record \#$show_review_record.";
		$location = "menu";
	} else {
		$error_message = "ERROR: $item_label Deletion Aborted. You forgot to checkmark the CONFIRM box.";
		$location = "add_review";
	}
}
##########################################################
## END: LOCATION PROCESS_delete_review
##########################################################



#################################################################################
## START: LOCATION = PROCESS_add_review
#################################################################################
	## START: READ NEW VALUES FOR LP RECORD
	my $new_intro_text = $query->param("new_intro_text");
	my $new_comment = $query->param("new_comment");
	my $new_nomination_id = $query->param("new_nomination_id");
	my $new_reviewer_id = $query->param("new_reviewer_id");
	my $new_review_type = $query->param("new_review_type");
	## END: READ NEW VALUES FOR LP RECORD


if ($location eq 'process_add_review') {
	## START: CHECK FOR DATA COPLETENESS
	if (($new_intro_text eq '') || ($new_comment eq '') || ($new_reviewer_id eq '')) {
		$error_message .= "The $item_label intro text, comment, or reviewer ID are missing. Please try again.";
		$location = "add_review";
	}
	## END: CHECK FOR DATA COPLETENESS
}

if ($location eq 'process_add_review') {

	## START: BACKSLASH VARIABLES FOR DB
	$new_intro_text = &cleanformysql($new_intro_text);
	$new_comment = &cleanformysql($new_comment);
	$new_nomination_id = &cleanformysql($new_nomination_id);
	$new_reviewer_id = &cleanformysql($new_reviewer_id);
	$new_review_type = &cleanformysql($new_review_type);
	## END: BACKSLASH VARIABLES FOR DB


	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select reviewID from sci_reviews_2008 ";
			if ($show_review_record ne '') {
				$command .= "WHERE reviewID = '$show_review_record'";
			}
		my $dsn = "DBI:mysql:database=afterschool;host=localhost";
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;

		$already_exists = "yes" if (($num_matches_code eq '1') && ($show_review_record ne ''));
		
		my $add_edit_type = "added"; # DEFAULT SETTING
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update_review = "UPDATE sci_reviews_2008 SET 
reviewer_id = '$new_reviewer_id', 
review_type = '$new_review_type', 
intro_text = '$new_intro_text', 
comment = '$new_comment',
last_edited = '$timestamp'
WHERE reviewID ='$show_review_record'";
			my $dsn = "DBI:mysql:database=afterschool;host=localhost";
			my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
			my $sth = $dbh->prepare($command_update_review) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			$add_edit_type = "edited";
			$feedback_message .= "The $item_label was $add_edit_type successfully";
			$feedback_message .= " and is highlighted in <a href=\"#rev$show_review_record\">YELLOW below</a>." if ($add_edit_type !~ 'add');
			$location = "menu";
		} else {
	
			my $command_insert_review = "INSERT INTO sci_reviews_2008 VALUES ('', '$show_record', '$new_reviewer_id', '$new_comment', '$new_intro_text', '$new_review_type', '$timestamp')";

			my $dsn = "DBI:mysql:database=afterschool;host=localhost";
			my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
			my $sth = $dbh->prepare($command_insert_review) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			$feedback_message .= "The $item_label was $add_edit_type successfully";
			$feedback_message .= " and is highlighted in <a href=\"#$show_record\">YELLOW below</a>.";
			$location = "menu";
		} # END IF USER NAME NOT BLANK

}
#################################################################################
## END: LOCATION = PROCESS_add_review
#################################################################################


#################################################################################
## START: LOCATION = add_review
#################################################################################
if ($location eq 'add_review') {
	print header;
	my $page_title = "Add a New $item_label";


	my $uniqueid = "";
	my $name = "";
	my $reviewID = "";
	my $reviewer_id = "";
	my $comment = "";
	my $intro_text = "";
	my $review_type = "";
	my $firstname = "";
	my $lastname = "";
	
my $num_matches_pubs;
	if ($show_review_record ne '') {
		$page_title = "Edit the $item_label";

		# SELCT EXISTING INFO FROM DB

my $command = "SELECT sci_nominations_2008.nominationID, sci_nominations_2008.name, 
							sci_reviews_2008.reviewID, sci_reviews_2008.reviewer_id, sci_reviews_2008.comment, sci_reviews_2008.intro_text, sci_reviews_2008.review_type,
							sci_reviewers_2008.firstname, sci_reviewers_2008.lastname 
					FROM sci_nominations_2008, sci_reviews_2008, sci_reviewers_2008
					
					WHERE sci_nominations_2008.nominationID = sci_reviews_2008.nominationID
					AND sci_reviewers_2008.reviewer_id = sci_reviews_2008.reviewer_id
					
					AND sci_reviews_2008.reviewID = '$show_review_record'";

#print "<P class=\"info\">COMMAND = $command</p>";

		my $dsn = "DBI:mysql:database=afterschool;host=localhost";
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		$num_matches_pubs = $sth->rows;

		while (my @arr = $sth->fetchrow) {
		   ($uniqueid, $name, 
		   $reviewID, $reviewer_id, $comment, $intro_text, $review_type, 
		   $firstname, $lastname) = @arr;

#			$news_review_content =~ s///gi;
		} # END DB QUERY LOOP
	} else {
		## LOOKUP RESOURCE NAME
		my $command = "SELECT nominationID, name FROM sci_nominations_2008 WHERE nominationID = '$show_record'";
		my $dsn = "DBI:mysql:database=afterschool;host=localhost";
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		while (my @arr = $sth->fetchrow) {
		   ($uniqueid, $name) = @arr;
		} # END DB QUERY LOOP
	} # END IF/ELSE
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$name = &cleanaccents2html($name);


## HELP PAGE FOR TinyMCE: http://www.sandiego.edu/webdev/coding/richcontent.php
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | Afterschool Science Guide Database Manager: $page_title</TITLE>
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
	content_css: "/css/tinymce.css",
	apply_source_formatting : true,
	convert_urls : false
});
</script>
EOM
#<script language="javascript" type="text/javascript" src="/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
#<script language="javascript" type="text/javascript">     
#tinyMCE.init({
#	mode : "textareas",
#	theme: "advanced",
#theme_advanced_buttons1: "bold, italic, removeformat, separator, charmap, separator, justifyleft, justifycenter, justifyright, justifyfull, formatselect", 
#theme_advanced_buttons2: "bullist, numlist, separator, outdent, indent, separator, hr, separator, undo, redo, separator, link, unlink, separator, cleanup, visualaid, help, code", 
#theme_advanced_buttons3: ""
#});
#</script>
print<<EOM;
$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td>

<h1><A HREF="guide_science_manager.cgi">Afterschool Science Guide Database Manager</A><br>
$page_title</h1>
EOM
print "<p>Found $num_matches_pubs existing review.</p>" if ($num_matches_pubs ne '');
print<<EOM;
<p>
Review 
EOM
print "(review ID #$reviewID)" if ($reviewID ne '');
print<<EOM;
 of <strong>$name</strong> (ID #$uniqueid) 
EOM
print "by <em>$firstname $lastname (reviewer #$reviewer_id).</em>" if ($reviewer_id ne '');
print<<EOM;
</p>
<p>
<strong>Editing Tip:</strong><br>
	<ul>
	<li>The WYSIWYG editor works best in the Firefox browser.</li>
	</ul>

EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
    
<FORM ACTION="guide_science_manager.cgi" METHOD=POST>

<TABLE border="1" cellpadding="2" cellspacing="0" width="100%" bgcolor="#E3F7AB">
<tr><td valign="top"><strong>Curriculum<br>Title</strong></td>
	<td valign="top">$name</td></tr>

<tr><td valign="top"><strong>Review Tyle:</strong></td>
	<td valign="top">
EOM
&print_reviewtype_menu("new_review_type", $review_type);
print<<EOM;
	</td>
</tr>
<tr><td valign="top"><strong>Reviewer ID:</strong></td>
	<td valign="top">
	<select NAME="new_reviewer_id">
	<option value=""></option>
EOM

	## START: QUERY REVIEWER DATABASE TO BUILD HASH OF REVIEWER NAMES, CODED BY REVIEWER ID
 	my $command = "SELECT reviewer_id, firstname, lastname FROM sci_reviewers_2008 order by firstname";
	my $dsn = "DBI:mysql:database=afterschool;host=localhost";
	my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
		   my ($list_reviewer_id, $list_firstname, $list_lastname) = @arr;
			print "<option value=\"$list_reviewer_id}\"";
			print " SELECTED" if ($reviewer_id eq $list_reviewer_id);
			print ">$list_firstname $list_lastname</option>";
		} # END DB QUERY LOOP
	## END: QUERY REVIEWER DATABASE TO BUILD HASH OF REVIEWER NAMES, CODED BY REVIEWER ID

print<<EOM;
	</select>
	</td>
</tr>
<tr><td valign="top"><strong>Review Synopsis:</strong></td>
	<td valign="top"><textarea name="new_intro_text" rows=30 cols=70>$intro_text</textarea></td>
</tr>

<tr><td valign="top"><strong>Full Review:</strong></td>
	<td valign="top"><textarea name="new_comment" rows=30 cols=70>$comment</textarea></td>
</tr>

</table>
<p>
	<UL>
		<INPUT TYPE=HIDDEN NAME=show_record VALUE="$show_record">
		<INPUT TYPE=HIDDEN NAME=show_review_record VALUE="$show_review_record">
		<INPUT TYPE=HIDDEN NAME=location VALUE="process_add_review">
	<INPUT TYPE=SUBMIT VALUE="Click to Save ($page_title)">
	</FORM>
	</UL>
</form>



</td>
	<td valign="top" align="right">
		(Click here to <A HREF="guide_science_manager.cgi?location=logout">logout</A>)
		<P>
	</td></tr>
</table>


$htmltail

EOM
}
#################################################################################
## END: LOCATION = add_review
#################################################################################



##########################################################
## START: LOCATION PROCESS_delete_item
##########################################################
if ($location eq 'process_delete_item') {
	my $confirm = $query->param("confirm");
	if ($confirm eq 'confirmed') {
	## START: BACKSLASH VARIABLES FOR DB
	$show_record = &cleanformysql($show_record);
	## END: BACKSLASH VARIABLES FOR DB

		## DELETE THE PAGES
		my $command_delete_pub = "DELETE from sci_reviews_2008 WHERE lp_unique_id = '$show_record'";
		my $dsn = "DBI:mysql:database=afterschool;host=localhost";
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command_delete_pub) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		$feedback_message = "You successfully deleted $item_label record \#$show_record.";
		$location = "menu";
	} else {
		$error_message = "ERROR: $item_label Deletion Aborted. You forgot to checkmark the CONFIRM box.";
		$location = "add_item";
	}
}
##########################################################
## END: LOCATION PROCESS_delete_item
##########################################################



#################################################################################
## START: LOCATION = PROCESS_add_item
#################################################################################
	## START: READ NEW VALUES FOR LP RECORD
#	my $new_intro_text = $query->param("new_intro_text");
	my $new_name = $query->param("new_name");
	my $new_short_intro = $query->param("new_short_intro");
	my $new_snappy_intro = $query->param("new_snappy_intro");
	my $new_cost = $query->param("new_cost");
	my $new_duration = $query->param("new_duration");
	my $new_format = &read_multivalue_data("new_format");
	my $new_grade = &read_multivalue_data("new_grade");
	my $new_use_options = &read_multivalue_data("new_use_options");
	my $new_content_domain = &read_multivalue_data("new_content_domain");
	my $new_part_of_series = $query->param("new_part_of_series");
	my $new_audience = &read_multivalue_data("new_audience");
	my $new_publisher = $query->param("new_publisher");
	my $new_price = $query->param("new_price");
	my $new_url = $query->param("new_url");
	my $new_other_contact_info = $query->param("new_other_contact_info");
	my $new_resource_type = $query->param("new_resource_type");
	## END: READ NEW VALUES FOR LP RECORD


if ($location eq 'process_add_item') {
	## START: CHECK FOR DATA COPLETENESS
	if ($new_name eq '') {
		$error_message .= "The $item_label ID and/or title are missing. Please try again.";
		$location = "add_item";
	}
	## END: CHECK FOR DATA COPLETENESS
}

if ($location eq 'process_add_item') {

	## START: BACKSLASH VARIABLES FOR DB
	$new_name = &cleanformysql($new_name);
	$new_snappy_intro = &cleanformysql($new_snappy_intro);
	$new_short_intro = &cleanformysql($new_short_intro);
	$new_cost = &cleanformysql($new_cost);
	$new_duration = &cleanformysql($new_duration);
	$new_format = &cleanformysql($new_format);
	$new_grade = &cleanformysql($new_grade);
	$new_use_options = &cleanformysql($new_use_options);
	$new_content_domain = &cleanformysql($new_content_domain);
	$new_part_of_series = &cleanformysql($new_part_of_series);
	$new_audience = &cleanformysql($new_audience);
	$new_publisher = &cleanformysql($new_publisher);
	$new_price = &cleanformysql($new_price);
	$new_url = &cleanformysql($new_url);
	$new_other_contact_info = &cleanformysql($new_other_contact_info);
	$new_resource_type = &cleanformysql($new_resource_type);
	## END: BACKSLASH VARIABLES FOR DB


	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select nominationID from sci_nominations_2008 ";
			if ($show_record ne '') {
				$command .= "WHERE nominationID = '$show_record'";
			}
		my $dsn = "DBI:mysql:database=afterschool;host=localhost";
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;

		$already_exists = "yes" if (($num_matches_code eq '1') && ($show_record ne ''));
		
		my $add_edit_type = "added"; # DEFAULT SETTING
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update_item = "UPDATE sci_nominations_2008 SET 
name = '$new_name', 
short_intro = '$new_short_intro', 
snappy_intro = '$new_snappy_intro', 
cost = '$new_cost', 
duration = '$new_duration', 
format = '$new_format', 
grade = '$new_grade', 
use_options = '$new_use_options', 
content_domain = '$new_content_domain', 
part_of_series = '$new_part_of_series', 
audience = '$new_audience', 
publisher = '$new_publisher', 
price = '$new_price', 
url = '$new_url', 
other_contact_info = '$new_other_contact_info',
resource_type = '$new_resource_type',
last_updated = '$timestamp'
WHERE nominationID ='$show_record'";
			my $dsn = "DBI:mysql:database=afterschool;host=localhost";
			my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
			my $sth = $dbh->prepare($command_update_item) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			$add_edit_type = "edited";
			$feedback_message .= "The curriculum record was $add_edit_type successfully";
			$feedback_message .= " and is highlighted in <a href=\"#$show_record\">YELLOW below</a>." if ($add_edit_type eq 'edited');
			$location = "menu";
		} else {
	
			my $command_insert_item = "INSERT INTO sci_nominations_2008 VALUES ('', '$new_name', '$new_short_intro', '$new_snappy_intro', '$new_cost', '$new_duration', '$new_format', '$new_grade', '$new_use_options', '$new_content_domain', '$new_part_of_series', '$new_audience', '$new_publisher', '$new_price', '$new_url', '$new_other_contact_info', '$new_resource_type', '$timestamp')";

			my $dsn = "DBI:mysql:database=afterschool;host=localhost";
			my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
			my $sth = $dbh->prepare($command_insert_item) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

			$feedback_message .= "<font color=green>The curriculum record was $add_edit_type successfully.";
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


	my $nominationID = "";
	my $name = "";
	my $short_intro = "";
	my $snappy_intro = "";
	my $cost = "";
	my $duration = "";
	my $format = "";
	my $grade = "";
	my $use_options = "";
	my $content_domain = "";
	my $part_of_series = "";
	my $audience = "";
	my $publisher = "";
	my $price = "";
	my $url = "";
	my $other_contact_info = "";
	my $resource_type = "";
	my $last_updated = "";
	
my $num_matches_pubs;
if ($show_record ne '') {
		$page_title = "Edit the $item_label";

		# SELCT EXISTING INFO FROM DB

my $command = "SELECT nominationID, name, short_intro, snappy_intro, cost, duration, format, grade, use_options, content_domain, part_of_series, audience, publisher, price, url, other_contact_info, resource_type, last_updated
					FROM sci_nominations_2008 WHERE nominationID = '$show_record'";

#print "COMMAND = $command";

		my $dsn = "DBI:mysql:database=afterschool;host=localhost";
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		$num_matches_pubs = $sth->rows;

		while (my @arr = $sth->fetchrow) {
		   ($nominationID, $name, $short_intro, $snappy_intro, $cost, $duration, $format, $grade, $use_options, $content_domain, $part_of_series, $audience, $publisher, $price, $url, $other_contact_info, $resource_type, $last_updated) = @arr;
		} # END DB QUERY LOOP
	}
		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$name = &cleanaccents2html($name);


## HELP PAGE FOR TinyMCE: http://www.sandiego.edu/webdev/coding/richcontent.php
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | Afterschool Science Guide Database Manager: $page_title</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<script language="javascript" type="text/javascript" src="/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">     
tinyMCE.init({
	mode : "textareas",
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
	paste_auto_cleanup_on_paste : true,
 	theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
	content_css: "/css/tinymce.css",
	apply_source_formatting : true,
	convert_urls : false
});
</script>
EOM
#<script language="javascript" type="text/javascript" src="/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
#<script language="javascript" type="text/javascript">     
#tinyMCE.init({
#	mode : "textareas",
#	theme: "advanced",
#theme_advanced_buttons1: "bold, italic, removeformat, separator, charmap, separator, justifyleft, justifycenter, justifyright, justifyfull, formatselect", 
#theme_advanced_buttons2: "bullist, numlist, separator, outdent, indent, separator, hr, separator, undo, redo, separator, link, unlink, separator, cleanup, visualaid, help, code", 
#theme_advanced_buttons3: ""
#});
#</script>
print<<EOM;
$htmlhead

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td>

<h1><A HREF="guide_science_manager.cgi">Afterschool Science Guide Database Manager</A><br>
$page_title</h1>
<p>
EOM
if ($num_matches_pubs ne '') {
print<<EOM;
Found $num_matches_pubs matches.
<br><br>
Curriculum item: <strong>$name</strong>
<br><br>
EOM
}
print<<EOM;

<strong>Editing Tip:</strong>
</p>
	<ul>
	<li>The WYSIWYG editor works best in the Firefox browser.</li>
	</ul>

EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
    
<FORM ACTION="guide_science_manager.cgi" METHOD=POST>

<TABLE border="1" cellpadding="2" cellspacing="0" width="100%" bgcolor="#E3F7AB">
<tr><td valign="top"><strong>Curriculum<br>Title</strong></td>
	<td valign="top"><input type=text" name="new_name" value="$name" size="70"></td></tr>

<tr><td valign="top"><strong>URL</strong></td>
	<td valign="top"><input type=text" name="new_url" value="$url" size="70"></td></tr>
<tr><td valign="top"><strong>Resource type:</strong></td>
	<td valign="top">
EOM
&print_resource_type_menu("new_resource_type", $resource_type);
print<<EOM;
	</td>
</tr>
<tr><td valign="top"><strong>Short Intro:</strong></td>
	<td valign="top"><textarea name="new_short_intro" rows=14 cols=70>$short_intro</textarea></td>
</tr>
<tr><td valign="top"><strong>Snappy Intro:</strong></td>
	<td valign="top"><textarea name="new_snappy_intro" rows=14 cols=70>$snappy_intro</textarea></td>
</tr>

<tr><td valign="top"><strong>Cost<br>Category:</strong></td>
	<td valign="top">
EOM
&print_cost_menu("new_cost", $cost);
print<<EOM;
	</td>
</tr>
<tr><td valign="top"><strong>Price:</strong></td>
	<td valign="top"><textarea name="new_price" rows=7 cols=70>$price</textarea></td>
<tr><td valign="top"><strong>Duration:</strong></td>
	<td valign="top">
EOM
&print_duration_menu("new_duration", $duration);
print<<EOM;
	</td>
</tr>
<tr><td valign="top"><strong>Format:</strong></td>
	<td valign="top">
EOM
&print_format_menu("new_format", $format);
print<<EOM;
	</td>
</tr>
<tr><td valign="top"><strong>Grade:</strong></td>
	<td valign="top">
EOM
&print_grade_menu("new_grade", $grade);
print<<EOM;
	</td>
</tr>
<tr><td valign="top"><strong>Audience:</strong></td>
	<td valign="top">
EOM
&print_audience_menu("new_audience", $audience);
print<<EOM;
	</td>
</tr>
<tr><td valign="top"><strong>Use Options:</strong></td>
	<td valign="top">
EOM
&print_use_options_menu("new_use_options", $use_options);
print<<EOM;
	</td>
</tr>
<tr><td valign="top"><strong>Content Domain:</strong></td>
	<td valign="top">
EOM
&print_content_domain_menu("new_content_domain", $content_domain);

$last_updated = &convert_timestamp_2pretty_w_date($last_updated, "yes");
print<<EOM;
	</td>
</tr>

<tr><td valign="top"><strong>Part of Series:</strong></td>
	<td valign="top"><textarea name="new_part_of_series" rows=7 cols=70>$part_of_series</textarea></td>
<tr><td valign="top"><strong>Publisher:</strong></td>
	<td valign="top"><textarea name="new_publisher" rows=8 cols=70>$publisher</textarea></td>
<tr><td valign="top"><strong>Other Contact Info:</strong></td>
	<td valign="top"><textarea name="new_other_contact_info" rows=8 cols=70>$other_contact_info</textarea></td>
</tr>
<tr><td valign="top"><strong>Last Updated:</strong></td>
	<td valign="top">$last_updated</td>
</tr>
</table>



<p>
	<UL>
		<INPUT TYPE=HIDDEN NAME=show_record VALUE="$show_record">
		<INPUT TYPE=HIDDEN NAME=location VALUE="process_add_item">
	<INPUT TYPE=SUBMIT VALUE="Click to Save ($page_title)">
	</FORM>
	</UL>
</form>



</td>
	<td valign="top" align="right">
		(Click here to <A HREF="guide_science_manager.cgi?location=logout">logout</A>)
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
	my %reviewer_name;
	
	## START: QUERY REVIEWER DATABASE TO BUILD HASH OF REVIEWER NAMES, CODED BY REVIEWER ID
 	my $command = "SELECT reviewer_id, firstname, lastname FROM sci_reviewers_2008";
	my $dsn = "DBI:mysql:database=afterschool;host=localhost";
	my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
		   my ($reviewer_id, $firstname, $lastname) = @arr;
			$reviewer_name{$reviewer_id} = "$firstname $lastname";
		} # END DB QUERY LOOP
	## END: QUERY REVIEWER DATABASE TO BUILD HASH OF REVIEWER NAMES, CODED BY REVIEWER ID




## PRINT SIGNUP FORM
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>SEDL Intranet | Afterschool Science Guide Database Manager: List of $item_label\s</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
$htmlhead
<table Cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td><h1><A HREF="guide_science_manager.cgi">Afterschool Science Guide Database Manager</A>
		<br>List of $item_label\s</h1></td>
	<td valign="top" align="right">
		(Click here to <A HREF="guide_science_manager.cgi?location=logout">logout</A>)
	</td></tr>
</table>
EOM

#	if ($logonuser_is_afterschool_representative ne 'yes') {
#		print "<FONT COLOR=CC6600>You have \"view only\" access to this resource, and you will only be able to view $item_label.</FONT>";
#	}

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';


my $command = "SELECT nominationID, name, short_intro, cost FROM sci_nominations_2008 order by name";
#print "<p class=\"info\">$command</p>";
	my $dsn = "DBI:mysql:database=afterschool;host=localhost";
	my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_items = $sth->rows;

my $col_head_title = "<strong>Title</strong>";
   $col_head_title = "<a href=\"guide_science_manager.cgi?location=menu&amp;sortby=title\">Title</a>" if ($sortby ne 'title');

print<<EOM;
<P>
There are $num_matches_items $item_label\s on file.
<br><br>
Note: The data in this database does not use HTML for line breaks.  Instead, line breaks are added by the online script.  Do not add HTML for line breaks or paragraphs to this database.
<br>
<br>
Click hee to <a href="/staff/afterschool/guide_science_manager.cgi?location=add_item">add a new record</a>.
</p>
EOM
#Click here to <A HREF=\"guide_science_manager.cgi?location=add_item\">Add a New $item_label</A>.
print<<EOM;
<table border="1" cellpadding="2" cellspacing="0" width="100%">
<tr bgcolor="#ebebeb">
	<td>$col_head_title</td>
	<td>Add Review</td>
	<td>Reviewer (click a reviewer name to edit the $item_label)</td>
</tr>
EOM
	if ($num_matches_items == 0) {
		print "<P><FONT COLOR=RED>There are no $item_label\s in the database.</FONT>";
	}
my $counter = 1;
my $last_name_shown;
	while (my @arr = $sth->fetchrow) {
		my ($uniqueid, $name, $short_intro, $cost) = @arr;
		my $bgcolor="";
  			$bgcolor="BGCOLOR=\"#FFFFCC\"" if ($show_record eq $uniqueid);
		# TRANSFORM DATES INTO PRETTY FORMAT
#		$lp_date_added = &date2standard($lp_date_added);
#		$lp_date_added = "N/A" if ($lp_date_added =~ '0000');
#		$lp_date_edited = &date2standard($lp_date_edited);
#		$lp_date_edited = "N/A" if ($lp_date_edited =~ '0000');

		## TRANSLATE CURLY QUOTES TO HTML ENTITIES
		$name = &cleanaccents2html($name);
#		$lp_show_on_site = "<font color=red>NO</font>" if ($lp_show_on_site eq 'no');

print<<EOM;
<tr $bgcolor>
	<td valign="top"><a name="$uniqueid"></a><a href=\"guide_science_manager.cgi?location=add_item&amp;show_record=$uniqueid\">$name</a>$short_intro<br><font color=red>$cost</font></td>
	<td><a href=\"guide_science_manager.cgi?location=add_review&amp;show_record=$uniqueid\">Add Review</a></td>
	<td valign="top" nowrap>
EOM

	## START: GRAB REVIEWS FOR THIS RECORD
	my $command_show_reviews = "SELECT reviewID, reviewer_id, last_edited FROM sci_reviews_2008 WHERE nominationID = '$uniqueid'";
	#print "<p class=\"info\">$command</p>";
		my $dsn = "DBI:mysql:database=afterschool;host=localhost";
		my $dbh = DBI->connect($dsn, "afterschooluser", "afterschool");
		my $sth = $dbh->prepare($command_show_reviews) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_reviews = $sth->rows;
		my $counter_reviewers = 1;
		while (my @arr = $sth->fetchrow) {
			my ($reviewID, $reviewer_id, $last_edited) = @arr;
			$last_edited = &convert_timestamp_2pretty_w_date($last_edited, 'yes');
print<<EOM;
	<a name="rev$reviewID"></a><A HREF=\"guide_science_manager.cgi?location=add_review&amp;show_record=$uniqueid&amp;show_review_record=$reviewID\" TITLE="Click to edit this $item_label">$reviewer_name{$reviewer_id}</a> <font color="#999999">(edited $last_edited)</font>
EOM
			print "<br>" if ($counter_reviewers == 1);
			$counter_reviewers++;
		} # END DB QUERY LOOP
	## END: GRAB REVIEWS FOR THIS RECORD


print<<EOM;
	</td>
</tr
EOM
		$counter++;
		$last_name_shown = $name;
	} # END DB QUERY LOOP
print<<EOM;
</TABLE>
<P>
The $site_label is located at <A HREF="$public_site_address">$public_site_address</A>.
<P>
To report troubles using this form, send an e-mail to <A HREF=mailto:webmaster\@sedl.org>webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
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
   $dirtyitem =~ s/\\//g;
#   $dirtyitem =~ s/\n/ /g;
#  $dirtyitem =~ s/\r/ /g;
#  $dirtyitem =~ s/\t/ /g;
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


###############################################
## START: SUBROUTINE print_resource_type_menu
###############################################
sub print_resource_type_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", 'traditional', 'materials for leaders seeking information about including science activities');
	my @item_label = ("select a resource type", 'traditional', 'materials for leaders seeking information about including science activities');
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
###############################################
} # END: SUBROUTINE print_resource_type_menu
###############################################


######################################
## START: SUBROUTINE print_cost_menu
######################################
sub print_cost_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", 'Free', '$1-$40', '$41-$75', '$76-$100', 'More than $100', 'variable associated with required training', 'Computers required');
	my @item_label = ("select a cost", 'Free', '$1-$40', '$41-$75', '$76-$100', 'More than $100', 'variable associated with required training', 'Computers required');
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_cost_menu
######################################


######################################
## START: SUBROUTINE print_duration_menu
######################################
sub print_duration_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", 'Less than 4 weeks', 'A series of sessions that lasts at least one semester', 'variable based on how instructors use activities');
	my @item_label = ("select a duration", 'Less than 4 weeks', 'A series of sessions that lasts at least one semester', 'variable based on how instructors use activities');
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection =~ $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_duration_menu
######################################


######################################
## START: SUBROUTINE print_format_menu
######################################
sub print_format_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ('Activity Book', 'CD-ROM', 'DVD', 'Instructors Guide', 'Kit or Materials', 'Programs Delivered in Cumulative Sessions', 'Take-Home Activity', 'Web-based');
	my @item_label = ('Activity Book', 'CD-ROM', 'DVD', 'Instructors Guide', 'Kit or Materials', 'Programs Delivered in Cumulative Sessions', 'Take-Home Activity', 'Web-based');
	my $item_counter = "0";
	my $count_total_items = $#item_value;
		while ($item_counter <= $count_total_items) {
			print "<br>" if ($item_counter > 0);
#			$field_name = "$field_name\_$item_counter";
			my $selected = "";
			   $selected = "CHECKED" if (($previous_selection =~ $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<input type=\"checkbox\" NAME=\"$field_name\_$item_counter\" ID=\"$field_name\_$item_counter\" VALUE=\"$item_value[$item_counter]\" $selected><label for=\"$field_name\_$item_counter\">$item_label[$item_counter]</label>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_format_menu
######################################


######################################
## START: SUBROUTINE print_grade_menu
######################################
sub print_grade_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ('all', 'grades K to 3', 'grades K to 5', 'grades K to 6', 'grades 1 to 5', 'grades 1 to 6', 'grades 2 to 4', 'grades 2 to 5', 'grades 2 to 6', 'grades 3 to 5', 'grades 4 to 5', 'grade 6', 'grades 5 to 8', 'grades 5 to 9', 'grades 6 to 8', 'grades 9 to 12');
	my @item_label = ('all', 'grades K to 3', 'grades K to 5', 'grades K to 6', 'grades 1 to 5', 'grades 1 to 6', 'grades 2 to 4', 'grades 2 to 5', 'grades 2 to 6', 'grades 3 to 5', 'grades 4 to 5', 'grade 6', 'grades 5 to 8', 'grades 5 to 9', 'grades 6 to 8', 'grades 9 to 12');
	my $item_counter = "0";
	my $count_total_items = $#item_value;
		while ($item_counter <= $count_total_items) {
			print "<br>" if ($item_counter > 0);
#			$field_name = "$field_name\_$item_counter";
			my $selected = "";
			   $selected = "CHECKED" if (($previous_selection =~ $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<input type=\"checkbox\" NAME=\"$field_name\_$item_counter\" ID=\"$field_name\_$item_counter\" VALUE=\"$item_value[$item_counter]\" $selected><label for=\"$field_name\_$item_counter\">$item_label[$item_counter]</label>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_grade_menu
######################################


######################################
## START: SUBROUTINE print_use_options_menu
######################################
sub print_use_options_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ('once', 'many times', 'reusable with parts or supplies', 'can be re-used, but require refills or replacement parts which can be purchased from a supplier or at local stores', 'can be reused');
	my @item_label = ('once', 'many times', 'reusable with parts or supplies', 'can be re-used, but require refills or replacement parts which can be purchased from a supplier or at local stores', 'can be reused');
	my $item_counter = "0";
	my $count_total_items = $#item_value;
		while ($item_counter <= $count_total_items) {
			print "<br>" if ($item_counter > 0);
#			$field_name = "$field_name\_$item_counter";
			my $selected = "";
			   $selected = "CHECKED" if (($previous_selection =~ $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<input type=\"checkbox\" NAME=\"$field_name\_$item_counter\" ID=\"$field_name\_$item_counter\" VALUE=\"$item_value[$item_counter]\" $selected><label for=\"$field_name\_$item_counter\">$item_label[$item_counter]</label>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_use_options_menu
######################################


######################################
## START: SUBROUTINE print_content_domain_menu
######################################
sub print_content_domain_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ('archeology', 'astronomy', 'biodiversity', 'biology', 'chemistry', 'cultural anthropology', 'earth science', 'engineering', 'environmental science', 'genetics', 'horticulture', 'invasive species', 'life science', 'marine biology', 'math', 'mechanics', 'multiple science topics', 'paleontology', 'physical science', 'physics', 'robotics', 'technology');
	my @item_label = ('archeology', 'astronomy', 'biodiversity', 'biology', 'chemistry', 'cultural anthropology', 'earth science', 'engineering', 'environmental science', 'genetics', 'horticulture', 'invasive species', 'life science', 'marine biology', 'math', 'mechanics', 'multiple science topics', 'paleontology', 'physical science', 'physics', 'robotics', 'technology');
	my $item_counter = "0";
	my $count_total_items = $#item_value;
		while ($item_counter <= $count_total_items) {
			print "<br>" if ($item_counter > 0);
#			$field_name = "$field_name\_$item_counter";
			my $selected = "";
			   $selected = "CHECKED" if (($previous_selection =~ $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<input type=\"checkbox\" NAME=\"$field_name\_$item_counter\" ID=\"$field_name\_$item_counter\" VALUE=\"$item_value[$item_counter]\" $selected><label for=\"$field_name\_$item_counter\">$item_label[$item_counter]</label>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_content_domain_menu
######################################


######################################
## START: SUBROUTINE print_audience_menu
######################################
sub print_audience_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ('Bilingual', 'Girls', 'Ethnically Diverse Audiences', 'Families', 'Boys', 'Starter Programs');
	my @item_label = ('Bilingual', 'Girls', 'Ethnically Diverse Audiences', 'Families', 'Boys', 'Starter Programs');
	my $item_counter = "0";
	my $count_total_items = $#item_value;
		while ($item_counter <= $count_total_items) {
			print "<br>" if ($item_counter > 0);
			my $selected = "";
			   $selected = "CHECKED" if (($previous_selection =~ $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<input type=\"checkbox\" NAME=\"$field_name\_$item_counter\" ID=\"$field_name\_$item_counter\" VALUE=\"$item_value[$item_counter]\" $selected><label for=\"$field_name\_$item_counter\">$item_label[$item_counter]</label>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_audience_menu
######################################


######################################
## START: SUBROUTINE print_subject_menu
######################################
sub print_subject_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", "Art", "Literacy", "Math", "Science", "Technology");
	my @item_label = ("select a subject", "Art", "Literacy", "Math", "Science", "Technology");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_subject_menu
######################################


######################################
## START: SUBROUTINE print_reviewtype_menu
######################################
sub print_reviewtype_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", "Afterschool Program Expert", "Science Content Expert");
	my @item_label = ("select a review type", "Afterschool Program Expert", "Science Content Expert");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT NAME="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_reviewtype_menu
######################################


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
} # END: SUBROUTINE print_month_menu
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
} # END: SUBROUTINE print_month_menu
######################################


###########################################
## START: SUBROUTINE read_multivalue_data
###########################################
sub read_multivalue_data {
	my $field_name = $_[0];
	my $num_parts = $_[1];
	   $num_parts = "30" if ($num_parts eq '');
	my $concatenated_data = "";
	
	my $item_counter = "0";
		while ($item_counter <= $num_parts) {
			my $next_value = $query->param("$field_name\_$item_counter");
			$concatenated_data = "$concatenated_data\, $next_value" if ($next_value ne '');
			$item_counter++;
		} # END WHILE
	return ($concatenated_data);
###########################################
} # END: SUBROUTINE read_multivalue_data
###########################################


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

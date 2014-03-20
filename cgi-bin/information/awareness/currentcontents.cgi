#!/usr/bin/perl 

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

my $query = new CGI;
my $debug = $query->param("debug") || "0"; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS

$|=1;
my $count = 0;

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 
use intranetcommoncode; 


###########################################
## START: VARIABLE DECLARATIONS
###########################################
my @list_topics;
my @list_journals;
my %journal_ids;
my %topic_ids;

	##########################################
	## START: LOAD TOPICS FROM THE DATABASE
	##########################################
	my $command = "select * from irc_cc_topics order by topic_name";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($cc_topic_id, $topic_type, $topic_name) = @arr;
			if ($topic_type eq 'Topic') {
				push (@list_topics, $topic_name);
				$topic_ids{$topic_name} = $cc_topic_id;
#				print "<br>SETTING VALUE '$cc_topic_id' FOR JOURNAL: $topic_name";
#				print "<BR>PRINT BACK: $journal_ids{'Academic Achievement'}";
#				print "<BR>PRINT BACK: $journal_ids{$topic_name}";
			} else {
				push (@list_journals, $topic_name);
				$journal_ids{$topic_name} = $cc_topic_id;
#				print "<BR>PRINT BACK: $journal_ids{$topic_name}";
#				print "<br>SETTING VALUE '$cc_topic_id' FOR TOPIC: $topic_name";
			}
		} # END DB QUERY LOOP
	##########################################
	## END: LOAD TOPICS FROM THE DATABASE
	##########################################

## COUNT THE NUMBER OF ITEMS IN EACH ARRAY
my $count_topics = $#list_topics;
my $count_journals = $#list_journals;

################################
## START: READ IN USER CHOICES
################################
my $masterlist_choices = "";
my @choices_journals = "";

			$masterlist_choices .= "JOURNALS:\n";
my $counter = "0";
	while ($counter <= $count_journals) {
		$choices_journals[$counter] = $query->param("j$counter");
		if ($choices_journals[$counter] ne '') {
			$masterlist_choices .= "$choices_journals[$counter]\n";
		}
		$counter++;
	}

			$masterlist_choices .= "\n\nTOPICS:\n";
my @choices_topics = "";
my $counter = "0";
	while ($counter <= $count_topics) {
		$choices_topics[$counter] = $query->param("t$counter");
		if ($choices_topics[$counter] ne '') {
			$masterlist_choices .= "$choices_topics[$counter]\n";
		}
		$counter++;
	}

################################
## END: READ IN USER CHOICES
################################

###########################################
## END: VARIABLE DECLARATIONS
###########################################


####################################################
# START: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################
#my $cookie_ss_session_id = ""; # TRACK SESSION ID
my $cookie_ss_staff_id = ""; # TRACK USER ID
	my(%cookies) = getCookies();
	foreach (sort(keys(%cookies))) {
#		$cookie_ss_session_id = $cookies{$_} if (($_ eq 'ss_session_id') && ($session_id eq ''));
		$cookie_ss_staff_id = $cookies{$_} if ($_ eq 'staffid');
	}
#$cookie_ss_session_id = $session_id if (($cookie_ss_session_id eq '') && ($session_id ne '')); # IF NOT SESSION IN COOKIE, BUT ONE PASSED VIA URL, USE SESSION FROM URL
####################################################
# END: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################

my $user_is_admin = "no";
   $user_is_admin = "yes" if ($cookie_ss_staff_id eq 'blitke');
   $user_is_admin = "yes" if ($cookie_ss_staff_id eq 'nreynold');

my $error_message = "";
my $feedback_message = "";

## GET THE SEARCH PARAMETER VARIABLES FROM THE HTML SEARCH FORM
my $location = $query->param("location");
   $location = "mainmenu" if $location eq '';

my $user = $query->param("user");
   $user =~ s/\@sedl\.org//g;

my $contentsnew = $query->param("contentsnew");

## FIELDS USED FOR EDITING
my $addedittype = $query->param("addedittype");
my $show_record = $query->param("show_record");
my $item_label = "Topic or Journal";


## COOKIE VARIABLES
my $expdate = "Fri, 25-Dec-2015 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";



## CHECK FOR COOKIE IF USER ID IS MISSING
if ($user eq '') {
my(%cookies) = getCookies();

foreach (sort(keys(%cookies))) {
	$user = $cookies{$_} if (($_ eq 'staffid') && ($user eq ''));
	#$name = $cookies{$_} if $_ eq 'username';
}

} # END OF COOKIE CHECK


##########################################################
## CHECK IF A USER ID WAS ENTERED
##########################################################
my $errormessage = "";
	if (($location eq 'sendrequest') && ($user eq '')) {
		$errormessage .= "<P>You forgot to enter your user ID.  Please try again." if $user eq '';
		$location = "mainmenu";
	}


##########################################################
## CHECK IF THIS IS A VALID USER ID
##########################################################
my $validuser = "no";
if (($location eq 'sendrequest') && ($user ne '')) {
my $command = "select userid from staff_profiles where ((userid like '$user')) order by userid";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

## SET ERROR MESSAGE IF USER ID IS INVALID
if ($num_matches eq '0') {
	$errormessage .= "<P><FONT COLOR=\"RED\">You entered an invalid user ID.  Please try again.</FONT><P>" ; 
	$location = "mainmenu";
	$validuser = "yes" if $num_matches eq '1';
}

## SET COOKIE WITH USER ID IF ITS VALID
if ($num_matches eq '1') {
	setCookie ("staffid", $user, $expdate, $path, $thedomain);
}

}  # END CHECK FOR VALID USER ID


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

#my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
#   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################


###############################################
# START: READ IN STAFF HEADER AND FOOTER HTML #
###############################################
my $htmlhead = "";
my $htmltail = "";
my $info_associate = "";


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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("121"); # 121 is the PID for this page in the intranet database

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";



open(IA,"</home/httpd/html/staff/includes/infoassociate.txt");
while (<IA>) {
	$info_associate .= $_;
}
close(IA);
###############################################
# END: READ IN STAFF HEADER AND FOOTER HTML #
###############################################


###############################################
## START: PRINT PAGE HEADER
###############################################
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<HEAD><TITLE>SEDL Staff - Information Services - Awareness Services - Current Contents Updates</TITLE>

<script type="text/javascript" src="/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">     
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
	content_css: "/css/sedl2007_forTinyMCE.css",
	convert_urls : false
});
</script>

$htmlhead
EOM
###############################################
## END: PRINT PAGE HEADER
###############################################



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
		my $command_delete_item = "DELETE from irc_cc_topics WHERE cc_topic_id = '$show_record'";
		$dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_delete_item) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#		my $num_matches = $sth->rows;
		
		$feedback_message = "You successfully deleted the record \#$show_record.";
		$location = "mainmenu";
	} else {
		$error_message = "ERROR: $item_label Deletion Aborted. You forgot to checkmark the CONFIRM box.";
		$location = "add_item";
	}

	##########################################
	## START: RELOAD TOPICS FROM THE DATABASE
	##########################################
	@list_topics = ();
	@list_journals = ();
	my $command = "select * from irc_cc_topics order by topic_name";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($cc_topic_id, $topic_type, $topic_name) = @arr;
			if ($topic_type eq 'Topic') {
				push (@list_topics, $topic_name);
				$topic_ids{$topic_name} = $cc_topic_id;
#				print "<br>SETTING VALUE '$cc_topic_id' FOR JOURNAL: $topic_name";
#				print "<BR>PRINT BACK: $journal_ids{'Academic Achievement'}";
#				print "<BR>PRINT BACK: $journal_ids{$topic_name}";
			} else {
				push (@list_journals, $topic_name);
				$journal_ids{$topic_name} = $cc_topic_id;
#				print "<BR>PRINT BACK: $journal_ids{$topic_name}";
#				print "<br>SETTING VALUE '$cc_topic_id' FOR TOPIC: $topic_name";
			}
		} # END DB QUERY LOOP
	##########################################
	## END: RELOAD TOPICS FROM THE DATABASE
	##########################################

}
##########################################################
## END: LOCATION PROCESS_DELETE_ITEM
##########################################################



#################################################################################
## START: LOCATION = PROCESS_add_item
#################################################################################
	my $new_topic_name = $query->param("new_topic_name");
	   $new_topic_name = "Text not entered" if ($new_topic_name eq '');
	my $new_topic_type = $query->param("new_topic_type");
	   $new_topic_type = "Topic" if ($new_topic_type eq '');

if ($location eq 'process_add_item') {

	## START: BACKSLASH VARIABLES FOR DB
	$new_topic_name = &commoncode::cleanthisfordb($new_topic_name);
	$new_topic_type = &commoncode::cleanthisfordb($new_topic_type);
	## END: BACKSLASH VARIABLES FOR DB


	# CHECK TO ENSURE ITEM NOT ALREADY SUBMITTED
	my $already_exists = "no";
		my $command = "select * from irc_cc_topics ";
			if ($show_record ne '') {
				$command .= "WHERE cc_topic_id = '$show_record'";
			}
		$dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_code = $sth->rows;
		
		$already_exists = "yes" if ($num_matches_code eq '1');

		my $add_edit_type = "added";
	
		if ($already_exists eq 'yes') {
			## DO THE EDIT
			my $command_update_mr = "UPDATE irc_cc_topics 
										SET topic_name ='$new_topic_name', topic_type ='$new_topic_type'
										WHERE cc_topic_id ='$show_record'";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command_update_mr) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			#my $num_matches = $sth->rows;
			
			$add_edit_type = "edited";
			$feedback_message .= "The $item_label was $add_edit_type successfully.";
			$location = "mainmenu";
		} else {
	
			my $command_insert_item = "INSERT INTO irc_cc_topics VALUES ('', '$new_topic_type', '$new_topic_name')";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command_insert_item) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			#my $num_matches = $sth->rows;

			$feedback_message .= "The $item_label was $add_edit_type successfully. ($command_insert_item)";
			$location = "mainmenu";
		} # END IF USER NAME NOT BLANK

	##########################################
	## START: RELOAD TOPICS FROM THE DATABASE
	##########################################
	@list_topics = ();
	@list_journals = ();
	my $command = "select * from irc_cc_topics order by topic_name";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($cc_topic_id, $topic_type, $topic_name) = @arr;
			if ($topic_type eq 'Topic') {
				push (@list_topics, $topic_name);
				$topic_ids{$topic_name} = $cc_topic_id;
#				print "<br>SETTING VALUE '$cc_topic_id' FOR JOURNAL: $topic_name";
#				print "<BR>PRINT BACK: $journal_ids{'Academic Achievement'}";
#				print "<BR>PRINT BACK: $journal_ids{$topic_name}";
			} else {
				push (@list_journals, $topic_name);
				$journal_ids{$topic_name} = $cc_topic_id;
#				print "<BR>PRINT BACK: $journal_ids{$topic_name}";
#				print "<br>SETTING VALUE '$cc_topic_id' FOR TOPIC: $topic_name";
			}
		} # END DB QUERY LOOP
	##########################################
	## END: RELOAD TOPICS FROM THE DATABASE
	##########################################

}
#################################################################################
## END: LOCATION = PROCESS_add_item
#################################################################################


#################################################################################
## START: LOCATION = add_item
#################################################################################
if ($location eq 'add_item') {
	my $page_title = "Add a New $item_label";

	my $cc_topic_id = "";
	my $topic_type = "";
	my $topic_name = "";
	if ($show_record ne '') {
		$page_title = "Save Edits to this $item_label";

		# SELCT EXISTING INFO FROM DB
		my $command = "select * from irc_cc_topics WHERE cc_topic_id = '$show_record'";
		$dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matching_records = $sth->rows;
		
		while (my @arr = $sth->fetchrow) {
			($cc_topic_id, $topic_type, $topic_name) = @arr;
		} # END DB QUERY LOOP
	
		if ($num_matching_records == 0 ) {
			$error_message = "$num_matching_records Records Found<br><br>COMMAND: $command";
		}

	} # END IF


print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;
<h1 style="margin-top:0px;"><a href="currentcontents.cgi">Current Contents Updates</a><br>
$page_title</h1>
<FORM ACTION="currentcontents.cgi" METHOD="POST" name="form2" id="form2">

<TABLE border="1" cellpadding="2" cellspacing="0" width="100%">
<tr><td valign="top"><strong><label for="new_topic_name">Text</label></strong></td>
	<td valign="top"><INPUT type="text" name="new_topic_name" id="new_topic_name" size="50" value="$topic_name"></td></tr>
<tr><td valign="top"><strong><label for="new_topic_type">Type</label></strong></td>
	<td valign="top">
EOM
$topic_type = $addedittype if ($topic_type eq '');
&print_type_menu("new_topic_type", $topic_type);
print<<EOM;
	</td></tr>
</table>
<br>
	<div style="margin-left:25px;">
		<INPUT TYPE="hidden" NAME="show_record" VALUE="$show_record">
		<INPUT TYPE="hidden" NAME="location" VALUE="process_add_item">
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
		<FORM ACTION="currentcontents.cgi" METHOD=POST>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr><td colspan="2"><em>Click here to delete this $item_label.</em></td></tr>
		<tr><td valign="top"><input type="checkbox" name="confirm" value="confirmed"></td>
			<td valign="top"><font color=red>confirm the deletion<br> of this $item_label.</font></td></tr>
		<tr><td colspan="2">
				<input type="hidden" name="location" value="process_delete_item">
				<input type="hidden" name="show_record" value="$show_record">
				<input type="submit" name="submit" value="Delete $item_label"><br>
				Note: When a board member ends their service,<br>set them to "inactive" instead of deleting them.</td></tr>
				
		</table>
		</form>
	
	</div>
	</td></tr>
	</table>
EOM
	}
}
#################################################################################
## END: LOCATION = add_item
#################################################################################


##################################################################################################
## START: LOCATION = MAINMENU
##################################################################################################
if ($location eq 'mainmenu') {
print "<h1 style=\"margin-top:0px;\">Current Contents Updates</h1>\n";
print "<p class=\"alert\">$error_message</p>\n" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>\n" if $feedback_message ne '';

print<<EOM;
Any SEDL staff member may sign up for FREE current awareness updates, sent to your e-mail address.
	<ul>
	<li><strong>What are current content updates?</strong> Each week, you will receive an e-mail containing citations and abstracts of articles for each topic you choose. Tables of contents for each journal are sent when a new issue is indexed online. You will receive one e-mail per topic or journal selected.</li>
	<li><strong>Who provides this service?</strong> SEDL's information associate, <a href="/pubs/catalog/authors/nreynold.html">Nancy 
	Reynolds</a> in SEDL's Information Resource Center (IRC), sets up the search strategies for which you will 
	receive e-mail alerts. Use the form below to request or cancel your subscription to current awareness topics or 
	journals as often as you like. You can even request tailored search strategies on a topic of your choice on the 
	form below or by contacting SEDL's information associate, 
	<a href="/pubs/catalog/authors/nreynold.html">Nancy Reynolds</a> at ext. 6548 or 
	by e-mail to <a href="mailto:nancy.reynolds\@sedl.org">nancy.reynolds\@sedl.org</a>. You also can let Nancy 
	know if you would like to receive your e-alerts on a daily, biweekly, or monthly basis instead of weekly.</li>
	</ul>
  
<FORM ACTION="/staff/information/awareness/currentcontents.cgi" METHOD="GET">

<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0" WIDTH="100%">

<TR><TD VALIGN="TOP"><P><strong><label for="user">Your User ID</label></strong><br> (ex: whoover)</TD>
    <TD><INPUT TYPE="TEXT" NAME="user" id="user" SIZE="8" value="$user"></TD></TR>

<TR><TD VALIGN="TOP" COLSPAN="2"><P><strong>Select the items for which you would like to receive weekly updates.</strong></TD></TR>
<TR><TD style="width:60%;" VALIGN="TOP"><em>Topics:</em>
EOM
	print " (<a href=\"currentcontents.cgi?location=add_item&amp;addedittype=Topic\">add topic</a>)" if ($user_is_admin eq 'yes');
print<<EOM;
    	<br>
    	<TABLE CELLPADDING="2" CELLSPACING="2">
EOM
 

#################################
## START: PRINT LIST OF TOPICS
#################################
my $counter = "0";
	while ($counter <= $count_topics) {
		print "<TR><TD VALIGN=\"TOP\"><INPUT TYPE=\"CHECKBOX\" NAME=\"t$counter\" id=\"t$counter\" value=\"$list_topics[$counter]\"></TD>
					<TD><label for=\"t$counter\">$list_topics[$counter]</label>";
		my $this_topic = $list_topics[$counter];
		my $this_id = $topic_ids{$list_topics[$counter]};
		
		print " (<a href=\"currentcontents.cgi?location=add_item&amp;show_record=$this_id\">edit</a>)" if ($user_is_admin eq 'yes');
#		print "<br>- \"$list_topics[$counter]\"";
		print "</TD></TR>";
		$counter++;
	}
#################################
## END: PRINT LIST OF TOPICS
#################################
 
print<<EOM;
		</TABLE>
	</TD>
	<TD style="width:40%;" VALIGN="TOP"><em>Journals:</em>
EOM
	print " (<a href=\"currentcontents.cgi?location=add_item&amp;addedittype=Journal\">add journal</a>)" if ($user_is_admin eq 'yes');
print<<EOM;
		<br>
		Receive the latest issue's Table of Contents (TOC) for the following journals or 
		suggest a journal in the box below.<br>
    	<TABLE CELLPADDING="2" CELLSPACING="2">
EOM

#################################
## START: PRINT LIST OF JOURNALS
#################################
my $counter = "0";
	while ($counter <= $count_journals) {
		print "<TR><TD VALIGN=\"TOP\"><INPUT TYPE=\"CHECKBOX\" NAME=\"j$counter\" id=\"j$counter\" value=\"$list_journals[$counter]\"></TD>
					<TD><label for=\"j$counter\">$list_journals[$counter]</label>";
		my $this_id = $journal_ids{$list_journals[$counter]};
#		my $this_id2 = $journal_ids{'Academic Achievement'};
		print " (<a href=\"currentcontents.cgi?location=add_item&amp;show_record=$this_id\">edit</a>)" if ($user_is_admin eq 'yes');
		print "</TD></TR>\n";
		$counter++;
	}
#################################
## END: PRINT LIST OF JOURNALS
#################################



print<<EOM;
    	</TABLE>
    </TD></TR>
</TABLE>
<p></p>
<TABLE>
<TR><TD VALIGN="TOP" style="width:150px;"><P><strong><label for="contentsnew">Suggest a Category or Journal</label></strong><br>
		(if your needs are not served by the categories and journal list above)</TD>
    <TD style="width:420px;"><TEXTAREA NAME="contentsnew" id="contentsnew" ROWS="3" COLS="50"></TEXTAREA></TD></TR>
</TABLE>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="sendrequest">
  <INPUT TYPE="SUBMIT" VALUE="Send Request">
  </div>
  </form>
EOM
}
##################################################################################################
## END: LOCATION = MAINMENU
##################################################################################################



##################################################################################################
## START: LOCATION = SEARCH
##################################################################################################
if ($location eq 'sendrequest') {

################################################################
## START: GET THIS STAFF MEMBER's INFO FROM THE PROFILE DATABASE
################################################################
my $command = "select firstname, lastname, jobtitle, phone, userid, email, phoneext, department_abbrev from staff_profiles where ((userid like '$user')) order by userid";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;
my $name = "";

## GET THE RESULTS OF THE QUERY
while (my @arr = $sth->fetchrow) {
    my ($firstname, $lastname, $jobtitle, $phone, $userid, $email, $phoneext, $department_abbrev) = @arr;
		$name = "$firstname $lastname";
		$phoneext = "x-$phoneext" if $phoneext;



## SEND E-MAIL TO MARY
my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
my $recipient = 'info@sedl.org';
#   $recipient = 'blitke@sedl.org';
my $fromaddr = 'webmaster@sedl.org';


open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: Current Contents Signup Form <$fromaddr>
To: $recipient, $email
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Data from Current Contents Signup Form

The following data was received from the Current Contents Signup Form at:
http://www.sedl.org/staff/information/awareness/currentcontents.cgi

+-----------------------------------------------+
| The Current Contents Information starts here: |
+-----------------------------------------------+


REQUEST FROM:
-------------
Name: $name 

Phone: $phone $phoneext

E-mail: $email

Department: $department_abbrev


CURRENT CONTENTS CHOICES:
-----------------------
$masterlist_choices


NEW TOPIC SUGGESTIONS WOULD APPEAR HERE, IF ANY:
------------------------------------------------
$contentsnew


---End of Training Signup Data---

EOM
print NOTIFY remote_host,"\n",remote_addr,"\n";
;
close(NOTIFY);



## SAVE NEW CURRENT CONTENTS SETTINGS TO DB

## SHOW THANK YOU WITH LINK BACK TO INFO SERVICES
print <<EOM;
<h2 style="margin-top:0px;">Thank You</H2>
<P>
Your Current Contents settings have been updated, and weekly updates will 
be sent to you automatically for the topics you chose.  
<P>
Click here to return to the <A HREF="/cgi-bin/mysql/staff/index.cgi?show_s=2">Information Services</A> page.
EOM

	} # END OF DB LOOP TO GRAB THE STAFF MEMBER'S INFO
################################################################
## END: GET THIS STAFF MEMBER's INFO FROM THE PROFILE DATABASE
################################################################

if ($num_matches eq '0') {
	print "<h1 style=\"margin-top:0px;\">Error</H1><P>You did not enter a valid user ID.</p>";
}

} # END: LOCATION = SENDREQUEST

print<<EOM;
$htmltail
EOM





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
## START: SUBROUTINE print_type_menu
######################################
sub print_type_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("Journal", "Topic");
	my @item_label = ("Journal", "Topic");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<SELECT name="$field_name" id="$field_name" alt="$previous_selection">
EOM
	while ($item_counter <= $count_total_items) {
		my $selected = "";
		   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
		print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</OPTION>\n";
		$item_counter++;
	} # END WHILE
print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_type_menu
######################################




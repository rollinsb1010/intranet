#!/usr/bin/perl
###################################################################
# Copyright 2008 by Southwest Educational Development Laboratory
# Written by Brian Litke (8/11/2006)
# This script displays the SEDL intranet pages
###################################################################

use strict;
use CGI qw/:cgi/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=intranet;host=www.sedl.org";
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

use LWP::Simple;
my $query = new CGI;

##########################################
# START: GRAB CGI ENVIRONMENTAL VARIABLES
##########################################
my $browser = $ENV{"HTTP_USER_AGENT"};
my $remote_host = $ENV{"REMOTE_HOST"};
my $remote_addr = $ENV{"REMOTE_ADDR"};
##########################################
# END: GRAB CGI ENVIRONMENTAL VARIABLES
##########################################

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

#my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
#   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################

my $time_delay_for_return = 3;
my $error_message = "";
my $feedback_message = "";

# GET CURRENT LOCATION INFORMTION
my $location = $query->param("location");
	if ($location eq '') {
		$location = "take-back-to-intranet";
		$error_message = "You did not specify an Intranet Administration command. Contact Brian Rollins at ext. 6504 for assistance.";
	}
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


my $page_header_info = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html lang=\"en\">
<HEAD>";
## SET SIDE NAVIGATION COLOR
my $bgcolor = "#97B038";

## SET VARIABLES USED TO DRAW HTML ROUNDED-BOXES
my $sidebar_boxtop = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\">
					<tr><td style=\"background:#ffffff\"><a href=\"/cgi-bin/mysql/staff/index.cgi?location=customize\"><img src=\"/staff/images/template/sidebar-quicklinks2.gif\" alt=\"my quick links\" class=\"noBorder\"></a></td></tr><tr><td style=\"background:#FFFFFF\">
							<table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF\">";
my $sidebar_boxbottom = "</td></tr></table>
					</td></tr><tr><td valign=\"top\" style=\"background:#97B038\"><img src=\"/staff/images/sidebar-round-bottom-97B038.gif\" class=\"decoration\" alt=\"\"></td></tr></table>";

my $sidebar_boxtop_staff = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td style=\"background:#ffffff\"><img src=\"/staff/images/template/sidebar-round-top-staff.gif\" class=\"decoration\" alt=\"SEDL Staff\"></td></tr><tr><td style=\"background:#FFFFFF\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF\">";
my $sidebar_boxtop_login = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td style=\"background:#ffffff\"><img src=\"/staff/images/template/sidebar-round-top-login.gif\" class=\"decoration\" alt=\"Please Log In\"></td></tr><tr><td style=\"background:#FFFFFF\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF\">";
my $sidebar_boxtop_sedlstar = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td style=\"background:#ffffff\"><img src=\"/staff/images/template/sidebar-round-top-sedlstar.gif\" class=\"decoration\" alt=\"SEDL Star of the Month\"></td></tr><tr><td style=\"background:#FFFFFF\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF\">";
my $sidebar_boxtop_pressreleases = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td><A HREF=\"/new/media.html\"><img src=\"/staff/images/template/sidebar-round-top-press.gif\" class=\"decoration noBorder\" alt=\"SEDL Press Releases\" class=\"noBorder\"></A></td></tr><tr><td style=\"background:#FFFFFF\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF\">";
my $sidebar_boxtop_suggestionbox = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td><A HREF=\"http://www.sedl.org/staff/communications/suggestion_box.cgi\"><img src=\"/staff/images/template/sidebar-boxtop_suggestionbox.gif\" alt=\"SEDL Suggestion Box\" class=\"noBorder decoration\"></A></td></tr><tr><td style=\"background:#FFFFFF\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF\">";

###########################################
# END: GRAB SEDL HEADER AND FOOTER HTML #
###########################################




########################################
## START: READ VARIABLES PASSED BY USER
########################################
my $print = $query->param("print"); # DID USER REQUEST PRINT-FRIENDLY PAGE?
#	if ($print eq 'yes') {
#		$htmlhead = "
#<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">
#<link href=\"/staff/includes/staff2006.css\" rel=\"stylesheet\" type=\"text/css\">
#
#<table>
#<tr><td>";
#		$htmltail = "</td></tr></table></body></html>";
#	}

my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $logon_pass = $query->param("logon_pass");

$logon_user = &backslash_fordb($logon_user);
$logon_pass = &backslash_fordb($logon_pass);

my $uniqueid = param('uniqueid');
my $super_login = "no";
   $super_login = "yes" if ($logon_pass eq 'sudo');

my $pid = $query->param("pid");
	$htmlhead =~ s/location\=customize/location\=customize\&pid=$pid/gi;

my $page = $query->param("page");
my $show_s = $query->param("show_s");
my $show_sg = $query->param("show_sg");
my $searchfor = $query->param("searchfor");
	# SET STAFF AS DEFAULT SEARCH IF IT WAS JUST USED
	if ($searchfor ne '') {
		$htmlhead =~ s/STAFF\"\>/STAFF\" SELECTED\>/g;
	}

##################################################################
## TRIGGER SUBROUTINE: SPLIT SEARCH STRING INTO WORDS TO SEARCH FOR
##################################################################


## DO WE USE THESE VARIABLES?
my $showsession = $query->param('showsession');

## INTRANET-WIDE SETTINGS
my $redirect_delay = "0";

########################################
## END: READ VARIABLES PASSED BY USER
########################################


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


####################################################
# START: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################
my $cookie_ss_session_id = ""; # TRACK SESSION ID
my $cookie_ss_staff_id = ""; # TRACK USER ID
my $cookie_search_fav = ""; # TRACK SEARCH PREFERENCE
my $cookie_random_content_id = "1"; # RANDOMIZE SOME CONTENT ON THE HOME PAGE
	my(%cookies) = getCookies();
	foreach (sort(keys(%cookies))) {
		$cookie_ss_session_id = $cookies{$_} if (($_ eq 'ss_session_id') && ($session_id eq ''));
		$cookie_ss_staff_id = $cookies{$_} if ($_ eq 'staffid');
		$cookie_search_fav = $cookies{$_} if ($_ eq 'intranetsearch');
		$cookie_random_content_id = $cookies{$_} if ($_ eq 'intranetrandomid');

		## START: REMOVE COOKIE THAT HAD OLD PROBLEM HTML CODE
		if ($_ =~ 'backtosearchresults') {
			setCookie ("$_", "", $expdate, $path, $thedomain);
		} # end if
		## END: REMOVE COOKIE THAT HAD OLD PROBLEM HTML CODE
	} # END FOREACH LOOP
	$cookie_ss_session_id = $session_id if (($cookie_ss_session_id eq '') && ($session_id ne '')); # IF NOT SESSION IN COOKIE, BUT ONE PASSED VIA URL, USE SESSION FROM URL

	# START: SET COOKIE USED FOR RANDOMIZING CONTENT IN THE SIDEBAR
	$cookie_random_content_id++; # INCREMENT TO NEXT NUMBER
	$cookie_random_content_id = "1" if ($cookie_random_content_id > 6);
	setCookie ("intranetrandomid", $cookie_random_content_id, $expdate, $path, $thedomain);
	# END: SET COOKIE USED FOR RANDOMIZING CONTENT IN THE SIDEBAR
####################################################
# END: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################

## START: SET SEARCH FORM TO USER'S LAST DATA SOURCE SEARCH SELECTION
if ($cookie_search_fav ne '') {
	$htmlhead =~ s/\<option value\=\"$cookie_search_fav\"\>/\<option value\=\"$cookie_search_fav\" SELECTED\>/gi;
}
## END: SET SEARCH FORM TO USER'S LAST DATA SOURCE SEARCH SELECTION


######################################################
## START: CHECK SESSION ID AND VERIFY
######################################################
my $staff_member_full_name = "";

	## IF SESSION ID NOT FOUND, PROMPT FOR LOGON
	if ($cookie_ss_session_id eq '') {
		# LET THE GUEST USER SEE CALENDAR, BUT IF REQUESTING OTHER FUNCTION, PROMPT LOGON
#		if ($location ne 'show_page') {
#		}
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
				my $command_update_session = "REPLACE into staff_sessions VALUES ('$ss_session_id', '$ss_staff_id', '$timestamp', '$remote_addr', '', '', '' ,'')";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
#	my $num_matches = $sth->rows;

			## SAVE COOKIES WITH SESSION ID AND USER ID
				$cookie_ss_staff_id = $ss_staff_id;
				setCookie ("ss_session_id", $ss_session_id, $expdate, $path, $thedomain);
				setCookie ("staffid", $ss_staff_id, $expdate, $path, $thedomain);
		} # END DB QUERY LOOP

		## IF SESSION NOT ACTIVE, PROMPT FOR LOGON
		if ($num_matches eq '0') {
			$cookie_ss_session_id = "";
			setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
#			$location = "show_page"; # AFTER LOGOUT, SHOW LOGON SCREEN
		}
	}
######################################################
## END: CHECK SESSION ID AND VERIFY
######################################################
#	 $error_message .= "- $cookie_ss_session_id $cookie_ss_staff_id<BR>- ";

#####################################################################
## START: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
#####################################################################
	if ($cookie_ss_session_id ne '') {
		my $command = "select firstname, lastname from staff_profiles where ((userid like '$cookie_ss_staff_id') OR (email like '$cookie_ss_staff_id')) order by userid";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;
#		 $error_message .= $command;
			while (my @arr = $sth->fetchrow) {
			    my ($firstname, $lastname) = @arr;
				$staff_member_full_name = $firstname;
#				$staff_member_full_name.= substr($lastname,0,1);
			}
	} # END IF
#####################################################################
## END: GET THIS STAFF MEMBER's FULL NAME FROM THE PROFILE DATABASE
#####################################################################



## START HTML OUTPUT
print header;



###############################################
## START: USER PREFERENCES
###############################################
	# DECLARE PREFERENCE HOLDER VARIABLES
	my $p_userid = "";
	my $p_color = "";
	my $p_css = "";
	my $p_navlocation = "";
	my $p_quicklinks_full = "";

	## START: READ PREFERENCES
	my $command_read_prefs = "select * from intranet_prefs where p_userid LIKE '$cookie_ss_staff_id'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_read_prefs) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
		    ($p_userid, $p_css, $p_color, $p_navlocation, $p_quicklinks_full) = @arr;
		}
	## END: READ PREFERENCES

	my @p_quicklinks = split(/\t/,$p_quicklinks_full);


#my $bgcolor = $p_color;
my $pref_side_navigation = $p_navlocation;
	## SET DEFAULT PREFERENCES
#	$bgcolor = "#6589BF" if ($bgcolor eq '');
#	$bgcolor = "#97B038";
	$pref_side_navigation = "left" if ($pref_side_navigation eq '');
###############################################
## END: USER PREFERENCES
###############################################

##########################################
# START: LOCATION ADMIN CHECK
##########################################
my $valid_admins = "blitke, brollins, cmoses, awest, macuna, cpierron, sabdulla";
my $user_is_admin = "no";
	if (($location =~ 'admin') && ($cookie_ss_session_id eq '')) {
		$error_message = "Please log in before performing intranet administrative functions.";
		$location = "take-back-to-intranet";
	} else {


		# START: QUERY DB FOR SECTION & GROUP TITLE
		my $command_count_editable_sections = "select is_id from intranet_section WHERE is_editable_by LIKE '%$cookie_ss_staff_id%'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_count_editable_sections) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_editable_sections = $sth->rows;

		if ($num_matches_editable_sections > 0) {
			$user_is_admin = "yes";
		}
	}


##########################################
# END: LOCATION ADMIN CHECK
##########################################


#########################################################
# START: LOCATION process_admin_delete_group
#########################################################
if ($location eq 'process_admin_delete_group') {
	my $confirm = $query->param("confirm");
	if ($confirm eq 'confirmed') {
		## DELETE THE GROUP
		my $command_delete_pages = "DELETE from intranet_section_group WHERE isg_id = '$show_sg'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_delete_pages) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;

		## DELETE THE PAGES
		my $command_delete_pages = "DELETE from intranet_pages WHERE page_isg_id = '$show_sg'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_delete_pages) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;

		## START: RESET SEQUENCE NUMBERING FOR THE GROUPS IN THAT SECTION
		my $command_get_section = "select isg_id, isg_seq_num
								from intranet_section_group
								where isg_is_id = '$show_s'
								order by isg_seq_num";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_section) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;
		my $sequence_should_be = 1;
		while (my @arr = $sth->fetchrow) {
		    my ($isg_id, $isg_seq_num) = @arr;
			if ($isg_seq_num ne $sequence_should_be) {
				my $command_set_sequence = "UPDATE intranet_section_group SET isg_seq_num = '$sequence_should_be'
								where isg_id = '$isg_id'";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_set_sequence) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
#	my $num_matches = $sth->rows;
			} # END IF
			$sequence_should_be++;
		} # END DB QUERY LOOP
		## END: RESET SEQUENCE NUMBERING FOR PAGES IN THE GROUP

		$feedback_message = "You successfully deleted the group ID: $show_sg.";
		$location = "admin";
	} else {
		$error_message = "ERROR: Group Deletion Aborted. You forgot to checkmark the CONFIRM box.";
		$location = "admin_edit_page";
	}
}
#########################################################
# END: LOCATION process_admin_delete_group
#########################################################

#########################################################
# START: LOCATION process_admin_delete_page
#########################################################
if ($location eq 'process_admin_delete_page') {
	my $confirm = $query->param("confirm");
	if ($confirm eq 'confirmed') {
		## DELETE THE PAGE
		my $command_delete_page = "DELETE from intranet_pages WHERE page_id = '$pid'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_delete_page) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;

		## RESET SEQUENCE NUMBERING FOR PAGES IN THE GROUP
		&re_sequence_page_ingroup($show_sg);

		$feedback_message = "You successfully deleted the page ID: $pid.";
		$location = "admin";
	} else {
		$error_message = "ERROR: Page Deletion Aborted. You forgot to checkmark the CONFIRM box.";
		$location = "admin_edit_page";
	}
}
#########################################################
# END: LOCATION process_admin_delete_page
#########################################################

######################################################################
# START: LOCATION process_admin_add_page / process_admin_edit_page
######################################################################
if (($location eq 'process_admin_add_page') || ($location eq 'process_admin_edit_page')) {
	# START: SET DEFAULT VARIABLES
	my $camefrom = $query->param("camefrom"); # values = page, admin
		$camefrom = "admin" if ($camefrom eq '');
	my $new_isg_title = $query->param("new_isg_title");
	my $new_page_title = $query->param("new_page_title");
	   $new_page_title = "The Page Title Was Omitted" if ($new_page_title eq '');

	my $new_page_id_text = $query->param("new_page_id_text");
	   $new_page_id_text = &clean_for_textid($new_page_id_text);
	   if ($new_page_id_text eq '') {
			my @title_pieces = split(/ /,$new_page_title);
			$title_pieces[0] = &clean_for_textid($title_pieces[0]);
			$title_pieces[1] = &clean_for_textid($title_pieces[1]);
			$title_pieces[2] = &clean_for_textid($title_pieces[2]);
			$title_pieces[3] = &clean_for_textid($title_pieces[3]);
			$new_page_id_text = "$title_pieces[0]";
			$new_page_id_text .= "\_$title_pieces[1]" if ($title_pieces[1] ne '');
			$new_page_id_text .= "_$title_pieces[2]" if ($title_pieces[2] ne '');
			$new_page_id_text .= "_$title_pieces[3]" if ($title_pieces[3] ne '');
	   }

		## CHECK TO ENSURE TEXT ID is UNIQUE
		my ($this_new_page_id_text, $this_feedback_message, $this_time_delay) = &check_unique_page_textid($new_page_id_text, $pid);
			$feedback_message .= "$this_feedback_message<br><br>" if ($this_feedback_message ne '');
		$new_page_id_text = $this_new_page_id_text;
		$time_delay_for_return = $this_time_delay;

	my $new_page_title_leftnav = $query->param("new_page_title_leftnav");
	my $new_page_display_in_leftnav = $query->param("new_page_display_in_leftnav");
	my $new_page_content = $query->param("new_page_content");
	my $new_page_redirect_tourl = $query->param("new_page_redirect_tourl");
	my $new_page_redirect_delay = $query->param("new_page_redirect_delay");
	my $new_page_fullwidth = $query->param("new_page_fullwidth");

	my $last_seq_num = 0;
	my $addedit = "Added";

	# CLEAN VARIABLES
	$new_page_id_text = &backslash_fordb($new_page_id_text);
	$new_page_title = &backslash_fordb($new_page_title);
	$new_page_title_leftnav = &backslash_fordb($new_page_title_leftnav);
	$new_page_display_in_leftnav = &backslash_fordb($new_page_display_in_leftnav);
	$new_page_content = &backslash_fordb($new_page_content);
	$new_page_redirect_tourl = &backslash_fordb($new_page_redirect_tourl);
	$new_page_redirect_delay = &backslash_fordb($new_page_redirect_delay);
	$new_page_fullwidth = &backslash_fordb($new_page_fullwidth);

	$show_s = &backslash_fordb($show_s);
	$show_sg = &backslash_fordb($show_sg);
	# END: SET DEFAULT VARIABLES



	if ($location eq 'process_admin_edit_page') {
		$addedit = "Edited";

		my $command_update_page = "UPDATE intranet_pages
								SET page_id_text = '$new_page_id_text',
								page_title = '$new_page_title',
								page_title_leftnav = '$new_page_title_leftnav',
								page_display_in_leftnav = '$new_page_display_in_leftnav',
								page_content = '$new_page_content',
								page_redirect_tourl = '$new_page_redirect_tourl',
								page_redirect_delay = '$new_page_redirect_delay',
								edit_committed = '$timestamp',
								edit_author = '$cookie_ss_staff_id',
								page_fullwidth = '$new_page_fullwidth'
								WHERE page_id = '$pid'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_update_page) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;
#		print "<P>COMMAND: $command_update_page";
	} else {
		# START: LOOKUP NEXT PAGE ID FOR THIS GROUP
		my $command_get_section = "select page_group_seq
								from intranet_pages
								where page_isg_id = '$show_sg'
								order by page_group_seq";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_section) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;

		while (my @arr = $sth->fetchrow) {
		    my ($page_group_seq) = @arr;
		    $last_seq_num = $page_group_seq;
		}
		# END: LOOKUP NEXT GROUP ID FOR THIS SECTION

		my $new_seq_num = $last_seq_num + 1;

		# INSERT NEW GROUP INTO DB
		my $command_insert_page = "INSERT INTO intranet_pages
								VALUES ('', '$new_page_id_text', '$show_sg', '$new_seq_num', '$new_page_title', '$new_page_title_leftnav', '$new_page_display_in_leftnav', '$new_page_content', '$new_page_redirect_tourl', '$new_page_redirect_delay', '$timestamp', '$cookie_ss_staff_id', '', '$cookie_ss_staff_id', '$new_page_fullwidth', '')";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_insert_page) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;

		# START: GET UNIQUE ID OF LAST RECORD INSERTED
		my $command_get_last_id = "SELECT MAX(page_id) FROM intranet_pages";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_last_id) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;
			while (my @arr = $sth->fetchrow) {
				my ($last_id) = @arr;
					$pid = $last_id;
			} # END DB QUERY LOOP
		# END: GET UNIQUE ID OF LAST RECORD INSERTED

	} # END IF/ELSE



	$new_page_title =~ s/\\//gi;
	$feedback_message .= "The Page \"$new_page_title\" was $addedit successfully.";
	$location = "admin";
	if ($camefrom ne 'admin') {
		$location = "take-back-to-intranet";
	}
}
######################################################################
# END: LOCATION process_admin_add_page / process_admin_edit_page
######################################################################


#########################################################
# START: LOCATION admin_add_page / admin_edit_page
#########################################################
if (($location eq 'admin_add_page') || ($location eq 'admin_edit_page')) {
	my $richedit = $query->param("richedit"); # values = page, admin
	   $richedit = "yes" if ($richedit eq '');
	my $camefrom = $query->param("camefrom"); # values = page, admin

	# START: SET DEFAULT VARIABLES
	my $next_location = "process_admin_add_page";
	my $button_label = "Add Page";
	my $addedit = "Add";

	if ($location eq 'admin_edit_page') {
		$next_location = "process_admin_edit_page";
		$button_label = "Submit Edit for this Page";
		$addedit = "Edit";
	}
	# END: SET DEFAULT VARIABLES

print<<EOM;
$page_header_info
<TITLE>SEDL intranet: Site Administration -  $addedit Intranet Page</TITLE>
EOM
#<!-- Source the JavaScript spellChecker object -->
#<script type="text/javascript" src="/common/javascript/spellChecker.js">
#</script>

#<script type="text/javascript">
#function openSpellChecker() {
#	// get the textarea we're going to check
#	var txt = document.myform.new_page_content;

#	// give the spellChecker object a reference to our textarea
#	// pass any number of text objects as arguments to the constructor:
#	var speller = new spellChecker( txt );

#	// kick it off
#	speller.openChecker();
#}
#</script>

print<<EOM;
$htmlhead
EOM

	# START: SET PLACEHOLDER VARIABELS FOR DB QUERY
	my $is_title;
	my $isg_title;

	my $page_id;
	my $page_id_text;
	my $page_isg_id;
	my $page_group_seq;
	my $page_title;
	my $page_title_leftnav;
	my $page_display_in_leftnav;
	my $page_content;
	my $page_redirect_tourl;
	my $page_redirect_delay;
	my $page_added_date;
	my $page_added_by;
	my $edit_committed;
	my $edit_author;
	my $page_fullwidth;

	# END: SET PLACEHOLDER VARIABELS FOR DB QUERY

	# CLEAN VARIABLES FOR DB QUERY
	$show_sg = &backslash_fordb($show_sg);

		# START: QUERY DB FOR SECTION & GROUP TITLE
		my $command_get_section = "select intranet_section.is_id, intranet_section.is_id_text, intranet_section.is_title, intranet_section_group.isg_title
									from intranet_section, intranet_section_group
									WHERE intranet_section.is_id = intranet_section_group.isg_is_id
									AND intranet_section_group.isg_id like '$show_sg'";
#		print "$command_get_section";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_section) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_sg = $sth->rows;
#		print "<BR>$num_matches_sg matches";

		while (my @arr = $sth->fetchrow) {
		    ($show_s, $is_title, $isg_title) = @arr;
			if ($show_s eq '0') {
				$richedit = "no";
			} # END IF
		} # END WHILE LOOP
		# END: QUERY DB FOR SECTION & GROUP TITLE

	if ($location eq 'admin_edit_page') {
		# START: QUERY DB FOR EXISTING PAGE DETAILS
		my $command_get_section_and_page = "select * from intranet_pages
								where page_id = '$pid'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_section_and_page) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_sg = $sth->rows;

#$num_matches_sg matches
		while (my @arr = $sth->fetchrow) {
		    ($page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav,
		    $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author, $page_fullwidth) = @arr;
		} # END: QUERY DB FOR EXISTING PAGE DETAILS
	}

print<<EOM;
<TABLE class="noBorder" CELLPADDING="15" CELLSPACING="0">
<TR><TD>
EOM
print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
print<<EOM;

<table cellpadding="0" cellspacing="0" class="noBorder" width="100%">
<tr><td valign="top"><h1 style="margin-top:0px;padding-top:0px"><a href="/staff/communications/intranet_page_manager.cgi?location=admin&amp;show_s=$show_s&amp;sg=$show_sg">Intranet Administration</a><br>$addedit an intranet page in the group<br> \"$isg_title\"</h1></td>
	<td valign="top" align="right" width="250">
EOM
if ($location eq 'admin_edit_page') {
print<<EOM;
<script type="text/javascript">
//<!--
function checkFieldsDelete() {
 	if (document.mydelform.confirm.checked == false) {
		alert("You have to check the confirmation box before the deletion can proceed.");
		document.mydelform.confirm.focus();
		return false;
	}
}
// -->


</script>



		<form action="/staff/communications/intranet_page_manager.cgi" method="POST" name="mydelform" id="mydelform" onsubmit="return checkFieldsDelete()">
		<div class="first fltRt">
		<table cellpadding="0" cellspacing="0" class="noBorder">
		<tr><td colspan="2"><em><label for="confirm">Click here to delete this <strong>page</strong></label>.</em></td></tr>
		<tr><td valign="top"><input type="checkbox" name="confirm" id="confirm" value="confirmed"></td>
			<td valign="top"><span style="color:red">confirm the deletion<br> of this intranet page.</span></td></tr>
		<tr><td colspan="2">
				<input type="hidden" name="location" value="process_admin_delete_page">
				<input type="hidden" name="pid" value="$pid">
				<input type="hidden" name="show_s" value="$show_s">
				<input type="hidden" name="show_sg" value="$show_sg">
				<input type="submit" name="submit" value="Delete"></td></tr>
				</form>
		</table>
		</div>
EOM

}
my $submit_edit_button = "";
	if ($pid ne '') {
		$submit_edit_button = "<br><br><input type=\"submit\" value=\"Submit Edit\">";
		if ($richedit ne 'yes') {
			$submit_edit_button .= "<br> <br><input type=\"button\" value=\"Spellcheck\" onClick=\"openSpellChecker();\"/>";
		}
	}
print<<EOM;
	</td></tr>
</table>
EOM


	##############################
	## START: SHOW WYSIWYG EDITOR
	##############################
	if ($richedit eq 'yes') {
		&print_tinyMCE_code();
	}
	##############################
	## END: SHOW WYSIWYG EDITOR
	##############################
print<<EOM;
<form action="/staff/communications/intranet_page_manager.cgi" method=POST name="myform" id="myform">
<table cellpadding="4" cellspacing="0" class="noBorder">
<tr><td valign="top"><strong><label for="new_page_title">Page Title</label></strong></td>
	<td valign="top"><input type="TEXT" name="new_page_title" id="new_page_title" size="70" value="$page_title" class="outline_border"></td></tr>
EOM
#<tr><td valign="top"><strong>Short Page Title</strong></td>
#	<td valign="top"><input type="TEXT" name="new_page_title_leftnav" size="20" value="$page_title_leftnav"><br>
#		(for use in left-side navigation)
#		</td></tr>
print<<EOM;
<tr><td valign="top"><strong><label for="new_page_id_text">Page Text ID</label></strong> (optional - for context-based links to this page)</td>
	<td valign="top"><input type="TEXT" name="new_page_id_text" id="new_page_id_text" size="30" value="$page_id_text" class="outline_border"><br>
		<em>example: The Web Hits page can be linked to by it's text ID rather than its numerical page ID:
		http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?<span style="color:green">page=webhits</span></em>
	</td></tr>
EOM
	if ($show_s ne '0') {
print<<EOM;
<tr><td valign="top"><strong><label for="new_page_display_in_leftnav">Show page in left-side navigation?</label></strong></td>
	<td valign="top">
EOM
&print_yes_no_menu("new_page_display_in_leftnav", $page_display_in_leftnav);
print<<EOM;
	</td></tr>
<tr><td valign="top"><strong><label for="new_page_fullwidth">Show page full-width?</label></strong></td>
	<td valign="top">
EOM
$page_fullwidth = "no" if ($page_fullwidth ne 'yes');
&print_yes_no_menu("new_page_fullwidth", $page_fullwidth);
print<<EOM;
	</td></tr>
EOM
	}
print<<EOM;
<tr><td valign="top">
		<strong><label for="new_page_content">Page Content</label></strong><br>

EOM
# SHOW LINK TO EDIT PAGE WITH WYSIWYG EDITOR
if ($richedit ne 'yes') {
	if ($show_s ne '0') {
		## PRINT THIS IF NOT DISPLAYING THE HOME PAGE
		print "Click here to <a href=\"/staff/communications/intranet_page_manager.cgi?location=$location&amp;show_sg=$show_sg&amp;pid=$pid&amp;camefrom=$camefrom&amp;richedit=yes\">edit using a WYSIWYG editor</a>.";
	}
} else {
	print "Click here to <a href=\"/staff/communications/intranet_page_manager.cgi?location=$location&amp;show_sg=$show_sg&amp;pid=$pid&amp;camefrom=$camefrom&amp;richedit=no\">edit raw HTML</a>.";
}
print<<EOM;
		$submit_edit_button<br>
	</td>
	<td valign="top"><textarea name="new_page_content" id="new_page_content" rows="55" cols="76">$page_content</textarea></td></tr>
EOM
	if ($show_s ne '0') {
print<<EOM;
<tr><td valign="top"><strong><label for="new_page_redirect_tourl">Page Redirect to URL</label></strong></td>
	<td valign="top"><input type="TEXT" name="new_page_redirect_tourl" id="new_page_redirect_tourl" size="70" value="$page_redirect_tourl" class="outline_border"><br>
						(optional redirect  to a PDF file or a CGI link)
						<P>
						<em><label for="new_page_redirect_delay">Page Redirect Delay in Seconds:</label> <input type="TEXT" name="new_page_redirect_delay" id="new_page_redirect_delay" size="4" value="$page_redirect_delay" class="outline_border"></em>
						</td></tr>
EOM
	}
print<<EOM;
</table>
  <UL>
	<INPUT TYPE="HIDDEN" NAME="camefrom" VALUE="$camefrom">
	<INPUT TYPE="HIDDEN" NAME="pid" VALUE="$page_id">
	<INPUT TYPE="HIDDEN" NAME="show_s" VALUE="$show_s">
	<INPUT TYPE="HIDDEN" NAME="show_sg" VALUE="$show_sg">
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="$next_location">
	<input type="submit" value="$button_label">
EOM
		if ($richedit ne 'yes') {
print<<EOM;
	<input type="button" value="Spellcheck" onClick="openSpellChecker();"/>
EOM
}
print<<EOM;
  </UL>
</form>

</TD></TR>
</TABLE>

EOM
}
#########################################################
# END: LOCATION admin_add_page / admin_edit_page
#########################################################

######################################################################
# START: LOCATION process_admin_edit_section
######################################################################
if ($location eq 'process_admin_edit_section') {

	# START: SET DEFAULT VARIABLES
	my $camefrom = $query->param("camefrom"); # values = page, admin
		$camefrom = "admin" if ($camefrom eq '');

	my $new_is_title = $query->param("new_is_title");
	my $new_is_intro = $query->param("new_is_intro");

	# CLEAN VARIABLES
	$new_is_intro = &backslash_fordb($new_is_intro);
	$show_s = &backslash_fordb($show_s);
	# END: SET DEFAULT VARIABLES

	my $command_update_section = "UPDATE intranet_section
							SET is_intro = '$new_is_intro'
							WHERE is_id = '$show_s'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_update_section) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;

	$feedback_message = "The Section \"$new_is_title\" was edited successfully.";
	$location = "admin";
	if ($camefrom ne 'admin') {
		$location = "take-back-to-intranet";
	}
}
######################################################################
# END: LOCATION process_admin_edit_section
######################################################################


#########################################################
# START: LOCATION admin_edit_section
#########################################################
if ($location eq 'admin_edit_section') {
	my $richedit = $query->param("richedit"); # values = page, admin
	   $richedit = "yes" if ($richedit eq '');
	my $camefrom = $query->param("camefrom"); # values = page, admin

print<<EOM;
$page_header_info
<TITLE>SEDL intranet: Site Administration -  Edit Intranet Section</TITLE>
EOM

#<!-- Source the JavaScript spellChecker object -->
#<script type="text/javascript" src="/common/javascript/spellChecker.js">
#</script>
#
#<script type="text/javascript">
#function openSpellChecker() {
#	// get the textarea we're going to check
#	var txt = document.myform.new_is_intro;

#	// give the spellChecker object a reference to our textarea
#	// pass any number of text objects as arguments to the constructor:
#	var speller = new spellChecker( txt );

#	// kick it off
#	speller.openChecker();
#}
#</script>
print<<EOM;

$htmlhead
EOM

	# START: SET PLACEHOLDER VARIABELS FOR DB QUERY
	my $is_id;
	my $is_id_text;
	my $is_title;
	my $is_intro;
	my $is_editable_by;

	# END: SET PLACEHOLDER VARIABELS FOR DB QUERY

	# CLEAN VARIABLES FOR DB QUERY
	$show_sg = &backslash_fordb($show_sg);

		# START: QUERY DB FOR SECTION & GROUP TITLE
		my $command_get_section = "select * from intranet_section WHERE is_id = '$show_s'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_section) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;
#		print "$command_get_section";
#		my $num_matches_s = $sth->numrows;
#		print "<BR>$num_matches_s matches";

		while (my @arr = $sth->fetchrow) {
		    ($is_id, $is_id_text, $is_title, $is_intro, $is_editable_by) = @arr;
		}
		# END: QUERY DB FOR SECTION & GROUP TITLE

	if ($is_editable_by =~ $cookie_ss_staff_id) {
		&print_tinyMCE_code();
print<<EOM;

<TABLE border="0" CELLPADDING="15" CELLSPACING="0">
<TR><TD>
EOM
print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
print<<EOM;
<h1 style="margin-top:0px;padding-top:0px"><a href="/staff/communications/intranet_page_manager.cgi?location=admin&amp;show_s=$show_s&amp;sg=$show_sg">Intranet Administration</a><br>
Edit the intranet Section: \"$is_title\"</h1>

<form action="/staff/communications/intranet_page_manager.cgi" method=POST name="myform" id="myform">
<table cellpadding="4" cellspacing="0" class="noBorder">
<tr><td valign="top"><strong>Section Title</strong></td>
	<td valign="top">$is_title<br>
		<input type="hidden" name="new_is_title" value="$is_title"></td></tr>
<tr><td valign="top" width="20%"><strong>Section Summary/ Introduction</strong>
		<P>
		This text will appear on the home page for the section after the section title and before the hierarchical list
		of groups and pages in the seciton.
	</td>
	<td valign="top" width="80%"><textarea name="new_is_intro" rows=25 cols=74>$is_intro</textarea></td></tr>
</table>
  <UL>
	<INPUT TYPE="HIDDEN" NAME="camefrom" VALUE="$camefrom">
	<INPUT TYPE="HIDDEN" NAME="show_s" VALUE="$show_s">
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_admin_edit_section">
	<input type="submit" value="Submit Edit for this Section">
EOM
		if ($richedit ne 'yes') {
print<<EOM;
	<input type="button" value="Spellcheck" onClick="openSpellChecker();"/>
EOM
}
print<<EOM;
  </UL>
</form>

<p class="info">Click here if you would like to
<a href="/staff/communications/intranet_page_manager.cgi?location=admin_add_group&amp;show_s=$show_s">add a new "group" to this section</a> or
<a href="/staff/communications/intranet_page_manager.cgi?location=admin&amp;show_s=$show_s">edit an existing groups or pages in this group</a>.</p>


</TD></TR>
</TABLE>
EOM
	} else {
print<<EOM;
<span style="color:red">ERROR: You do not have sufficient privileges to edit this section information.(YOU: $cookie_ss_staff_id) (REQUIRES: $is_editable_by)</span>
EOM
	} # END IF/ELSE
}
#########################################################
# END: LOCATION admin_edit_section
#########################################################


######################################################################
# START: LOCATION process_admin_add_group / process_admin_edit_group
######################################################################
if (($location eq 'process_admin_add_group') || ($location eq 'process_admin_edit_group')) {

	my $camefrom = $query->param("camefrom"); # values = page, admin
		$camefrom = "admin" if ($camefrom eq '');
	# START: SET DEFAULT VARIABLES
	my $new_isg_title = $query->param("new_isg_title");
		$new_isg_title = "The Page Title Was Omitted" if ($new_isg_title eq '');
	my $new_isg_id_text = $query->param("new_isg_id_text");
	   $new_isg_id_text = &clean_for_textid($new_isg_id_text);
	   if ($new_isg_id_text eq '') {
			my @title_pieces = split(/ /,$new_isg_title);
			$title_pieces[0] = &clean_for_textid($title_pieces[0]);
			$title_pieces[1] = &clean_for_textid($title_pieces[1]);
			$title_pieces[2] = &clean_for_textid($title_pieces[2]);
			$title_pieces[3] = &clean_for_textid($title_pieces[3]);
			$new_isg_id_text = "$title_pieces[0]";
			$new_isg_id_text .= "\_$title_pieces[1]" if ($title_pieces[1] ne '');
			$new_isg_id_text .= "_$title_pieces[2]" if ($title_pieces[2] ne '');
			$new_isg_id_text .= "_$title_pieces[3]" if ($title_pieces[3] ne '');
	   }

		## CHECK TO ENSURE TEXT ID is UNIQUE
		my ($this_new_isg_id_text, $this_feedback_message, $this_time_delay) = &check_unique_group_textid($new_isg_id_text, $show_sg);
			$feedback_message .= "$this_feedback_message<br><br>" if ($this_feedback_message ne '');
		$new_isg_id_text = $this_new_isg_id_text;
		$time_delay_for_return = $this_time_delay;



	my $new_isg_description = $query->param("new_isg_description");
	my $new_isg_edit_committed = $query->param("new_isg_edit_committed");
	my $new_isg_edit_author = $query->param("new_isg_edit_author");
	my $last_seq_num = 0;
	my $addedit = "Added";

	# CLEAN VARIABLES
	$new_isg_id_text = &backslash_fordb($new_isg_id_text);
	$new_isg_title = &backslash_fordb($new_isg_title);
	$new_isg_description = &backslash_fordb($new_isg_description);
	$show_s = &backslash_fordb($show_s);
	$show_sg = &backslash_fordb($show_sg);
	# END: SET DEFAULT VARIABLES



	if ($location eq 'process_admin_edit_group') {
		$addedit = "Edited";

		my $command_update_group = "UPDATE intranet_section_group
								SET isg_id_text='$new_isg_id_text', isg_title='$new_isg_title', isg_description='$new_isg_description', isg_edit_committed = '$timestamp', isg_edit_author='$cookie_ss_staff_id'
								WHERE isg_id LIKE '$show_sg'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_update_group) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#		my $num_matches = $sth->rows;
	} else {
		# START: LOOKUP NEXT GROUP ID FOR THIS SECTION
		my $command_get_section = "select isg_seq_num
								from intranet_section_group
								where isg_is_id = '$show_s'
								order by isg_seq_num";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_section) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#		my $num_matches = $sth->rows;

		while (my @arr = $sth->fetchrow) {
		    my ($isg_seq_num) = @arr;
		    $last_seq_num = $isg_seq_num;
		}
		# END: LOOKUP NEXT GROUP ID FOR THIS SECTION

		my $new_seq_num = $last_seq_num + 1;

		# INSERT NEW GROUP INTO DB
		my $command_insert_group = "INSERT INTO intranet_section_group
								VALUES ('', '$new_isg_id_text', '$show_s', '$new_isg_title', '$new_seq_num', '$new_isg_description', '$timestamp', '$cookie_ss_staff_id')";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_insert_group) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;

		# START: GET UNIQUE ID OF LAST RECORD INSERTED
		my $command_get_last_id = "SELECT MAX(isg_id) FROM intranet_section_group";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_last_id) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#		my $num_matches = $sth->rows;
			while (my @arr = $sth->fetchrow) {
				my ($last_id) = @arr;
					$show_sg = $last_id;
			} # END DB QUERY LOOP
		# END: GET UNIQUE ID OF LAST RECORD INSERTED

	}

	$feedback_message = "The Group \"$new_isg_title\" was $addedit successfully.";
	if ($camefrom ne 'admin') {
		$location = "take-back-to-intranet";
	}
	$location = "admin";
}
######################################################################
# END: LOCATION process_admin_add_group / process_admin_edit_group
######################################################################


#########################################################
# START: LOCATION admin_add_group / admin_edit_group
#########################################################
if (($location eq 'admin_add_group') || ($location eq 'admin_edit_group')) {

	# START: SET DEFAULT VARIABLES
	my $next_location = "process_admin_add_group";
	my $button_label = "Add Group";
	my $addedit = "Add";

	if ($location eq 'admin_edit_group') {
		$next_location = "process_admin_edit_group";
		$button_label = "Submit Edit for this Group";
		$addedit = "Edit";
	}
	# END: SET DEFAULT VARIABLES

print<<EOM;
$page_header_info
<TITLE>SEDL intranet: Site Administration -  $addedit Intranet Group</TITLE>
$htmlhead
EOM

	# START: SET PLACEHOLDER VARIABELS FOR DB QUERY
	my $is_title;
	my $isg_id;
	my $isg_id_text;
	my $isg_is_id;
	my $isg_title;
	my $isg_seq_num;
	my $isg_description;
	my $isg_edit_committed;
	my $isg_edit_author;
	# END: SET PLACEHOLDER VARIABELS FOR DB QUERY

	# CLEAN VARIABLES FOR DB QUERY
	$show_s = &backslash_fordb($show_s);
	$show_sg = &backslash_fordb($show_sg);

	if ($location eq 'admin_add_group') {
		# START: QUERY DB FOR SECTION TITLE
		my $command_get_section = "select is_title from intranet_section where is_id like $show_s";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_section) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		while (my @arr = $sth->fetchrow) {
		    ($is_title) = @arr;
		}
		# END: QUERY DB FOR SECTION TITLE
	} else {
		# START: QUERY DB FOR SECTION TITLE
		my $command_get_section_and_group = "select intranet_section.is_title, intranet_section_group.*
								from intranet_section, intranet_section_group
								where intranet_section.is_id = intranet_section_group.isg_is_id
								AND intranet_section_group.isg_id like $show_sg";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_section_and_group) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_sg = $sth->rows;

#$num_matches_sg matches
		while (my @arr = $sth->fetchrow) {
		    ($is_title,
		     $isg_id, $isg_id_text, $isg_is_id, $isg_title, $isg_seq_num, $isg_description, $isg_edit_committed, $isg_edit_author) = @arr;
			$show_s = $isg_is_id;
		} # END DB QUERY LOOP
	}

print<<EOM;

<TABLE class="noBorder" CELLPADDING="15" CELLSPACING="0" width="100%">
<TR><TD>

<table cellpadding="0" cellspacing="0" class="noBorder" width="100%">
<tr><td valign="top">
	<h1 style="margin-top:0px;padding-top:0px"><a href="/staff/communications/intranet_page_manager.cgi?location=admin&amp;show_s=$show_s">Intranet Administration</a><br>
		$addedit a Group for the Section: $is_title</h1></td>
	<td valign="top">
EOM
	if ($location eq 'admin_edit_group') {
print<<EOM;
		<form action="/staff/communications/intranet_page_manager.cgi" method=POST>
		<div class="first fltRt">
		<table cellpadding="0" cellspacing="0" class="noBorder">
		<tr><td colspan="2" nowrap><em>Click here to delete the group:<br><strong>$isg_title</strong>.</em></td></tr>
		<tr><td valign="top"><input type="checkbox" name="confirm" value="confirmed"></td>
			<td valign="top"><span style="color:red">confirm deletion of<br>this group and ALL<br>ASSOCIATED PAGES.</span></td></tr>
		<tr><td colspan="2">
				<input type="hidden" name="location" value="process_admin_delete_group">
				<input type="hidden" name="show_s" value="$show_s">
				<input type="hidden" name="show_sg" value="$show_sg">
				<input type="submit" name="submit" value="Delete Group"></td></tr>

		</table>
		</div>
		</form>
EOM

	}
print<<EOM;
	</td></tr>
</table>
EOM
&print_tinyMCE_code();

print<<EOM;
<form action="/staff/communications/intranet_page_manager.cgi" method=POST>
<table cellpadding="4" cellspacing="0" class="noBorder">
<tr><td valign="top">Group Heading</td>
	<td valign="top"><input type="TEXT" name="new_isg_title" size="80" value="$isg_title" class="outline_border"></td></tr>
<tr><td valign="top"><strong>Group Text ID</strong> (optional - for context-based links to this page)</td>

	<td valign="top"><input type="TEXT" name="new_isg_id_text" size="30" value="$isg_id_text" class="outline_border"><br>
		<em>example: The Web Hits page can be linked to by it's text ID rather than its numerical page ID:
		http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?<span style="color:green">group=success_stories</span></em>
	</td></tr>
<tr><td valign="top">Group Description<br>
					(optional)</td>
	<td valign="top"><textarea name="new_isg_description" cols="76" rows="15">$isg_description</textarea></td></tr>
</table>
  <UL>
	<INPUT TYPE="HIDDEN" NAME="show_s" VALUE="$show_s">
	<INPUT TYPE="HIDDEN" NAME="show_sg" VALUE="$show_sg">
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="$next_location">
	<input type="submit" value="$button_label">
  </UL>
</form>
<br>
<br>
EOM
if ($location eq 'admin_edit_group') {
print<<EOM;
<p class="info">Click here if you would like to
<a href="/staff/communications/intranet_page_manager.cgi?location=admin_add_page&amp;show_sg=$show_sg">add a new page to this group</a> or
<a href="/staff/communications/intranet_page_manager.cgi?location=admin&amp;show_s=$show_s">edit an existing page in this group</a>.</p>
EOM
}
print<<EOM;
</TD></TR>
</TABLE>

EOM
}
#########################################################
# END: LOCATION admin_add_group / admin_edit_group
#########################################################


##########################################
# START: LOCATION process_admin_relocate_
##########################################
if ($location eq 'process_admin_relocate_page') {
	# CLEAN VARIABLES FOR DB QUERY
	my $original_group = $show_sg;
	my $current_group = "";
	my $destination_group = $query->param("destination_group");
	my $destination_section = "";
	my $new_group_title = "";
	my $largest_page_group_seq = "";
	$destination_group = &backslash_fordb($destination_group);
	$original_group = &backslash_fordb($original_group);

	## START: CHECK TO ENSURE PAGE DOES NOT ALREADY BELONG TO THIS GROUP (PREVENT 'RELOAD' PROBLEMS)
		my $command_check_reload = "select page_isg_id FROM intranet_pages where page_id = '$pid'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_check_reload) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
			my ($page_isg_id) = @arr;
			$current_group = $page_isg_id;
		} # END DB QUERY LOOP

		if ($current_group eq $destination_group) {
			$error_message = "ERROR - PAGE MOVE ABORTED: The page already belongs to this group.  You may see this message if you clicked RELOAD.";
		}


	## START: CHECK TO ENSURE DESTINATION GROUP EXISTS
		my $command = "select isg_id, isg_title FROM intranet_section_group WHERE isg_id = '$destination_group'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_section_group = $sth->rows;
			while (my @arr = $sth->fetchrow) {
				my ($check_isg_id, $check_isg_title) = @arr;
					$new_group_title = $check_isg_title;
			}
		my $command = "select intranet_section.is_id, intranet_section.is_id, intranet_section.is_title, intranet_section_group.isg_title, intranet_pages.page_group_seq
						FROM intranet_section, intranet_section_group, intranet_pages
						WHERE intranet_section.is_id = intranet_section_group.isg_is_id
						AND intranet_section_group.isg_id = intranet_pages.page_isg_id";
		   $command .= " AND intranet_section_group.isg_id LIKE '$destination_group'";
		   $command .= " order by intranet_pages.page_group_seq";
#		print "<P>PIDTEXT= $page AND COMMAND: $command";

		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_new_group_pages = $sth->rows;

#		print "<P>MATCHES: $num_matches";
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
			my ($is_id, $is_id_text, $is_title, $isg_title, $page_group_seq) = @arr;

			$largest_page_group_seq = $page_group_seq; # TRACK LARGEST PAGE SEQUENCE IN USE
			$destination_section = $is_id; # SET DESTINATION SECTION ID

			$new_group_title = "$is_title: $isg_title"; # SET NEW GROUP TITLE
		} # END DB QUERY LOOP

		## SET NEXT SEQUENCE FOR DESTINATION GROUP
		$largest_page_group_seq++;
			if (($num_matches_new_group_pages == 0) && ($num_matches_section_group != 0)) {
				## IF GROUP EXISTS, BUT NO PAGES THERE YET
				$largest_page_group_seq = 1;
			}
	## END: CHECK TO ENSURE DESTINATION GROUP EXISTS



		if ($error_message eq '') {
			if (($num_matches_new_group_pages eq '0') && ($num_matches_section_group == 0)) {
				$error_message = "ERROR - PAGE MOVE ABORTED: The destination group does not exist. (Group = $num_matches_section_group)";
			} else {

				## UPDATE intranet_pages TO SET page_group_seq TO $destination_group
				my $command_update_group = "UPDATE intranet_pages SET page_isg_id = '$destination_group', page_group_seq = '$largest_page_group_seq' WHERE page_id = '$pid'";
#				print "<P>COMMAND: $command_update_group";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_group) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;

				## SET show_s and show_sg TO DESTINATION SECTION/GROUP ID
				$show_s = $destination_section;
				$show_sg = $destination_group;

				## SET FEEDBACK MESSAGE
				$feedback_message = "The page was successfully moved to (sequence $largest_page_group_seq in) the group: $new_group_title.";

				## RESET THE PAGE SEQUENCING FOR THE ORIGINAL GROUP
						## RESET SEQUENCE NUMBERING FOR PAGES IN THE GROUP
				&re_sequence_page_ingroup($original_group);

			} # END IF
		} # END IF
	## SHOW THE ADMIN MENU AGAIN
	$location = "admin";
}


###################################################
if ($location eq 'process_admin_relocate_group') {
	# CLEAN VARIABLES FOR DB QUERY
	my $original_section = $show_s;
	my $current_section = "";
	my $destination_section = $query->param("destination_section");
	my $new_is_title = "";
	my $largest_section_seq_num = "";
	$destination_section = &backslash_fordb($destination_section);
	$original_section = &backslash_fordb($original_section);

	## START: CHECK TO ENSURE PAGE DOES NOT ALREADY BELONG TO THIS SECTION (PREVENT 'RELOAD' PROBLEMS)
		my $command_check_reload = "select isg_is_id FROM intranet_section_group where isg_id = '$show_sg'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_check_reload) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
			my ($isg_is_id) = @arr;
			$current_section = $isg_is_id;
		} # END DB QUERY LOOP

		if ($current_section eq $destination_section) {
			$error_message = "ERROR - GROUP MOVE ABORTED: The group already belongs to this section.  You may see this message if you clicked RELOAD.";
		}


	## START: CHECK TO ENSURE DESTINATION GROUP EXISTS
		my $command = "select intranet_section.is_id, intranet_section.is_id_text, intranet_section.is_title, intranet_section_group.isg_seq_num
						FROM intranet_section, intranet_section_group
						WHERE intranet_section.is_id = intranet_section_group.isg_is_id
						AND intranet_section.is_id LIKE '$destination_section'";
		   $command .= " order by intranet_section_group.isg_seq_num";
#		print "<P>PIDTEXT= $page AND COMMAND: $command";

		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		my $num_matches_new_section_pages = $sth->rows;

#		print "<P>MATCHES: $num_matches";
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
			my ($is_id, $is_id_text, $is_title, $isg_seq_num) = @arr;

			$largest_section_seq_num = $isg_seq_num; # TRACK LARGEST PAGE SEQUENCE IN USE
			$destination_section = $is_id; # SET DESTINATION SECTION ID

			$new_is_title = "$is_title"; # SET NEW SECTION TITLE
		} # END DB QUERY LOOP

		## SET NEXT SEQUENCE FOR DESTINATION GROUP
		$largest_section_seq_num++;

	## END: CHECK TO ENSURE DESTINATION GROUP EXISTS



		if ($error_message eq '') {
			if ($num_matches_new_section_pages eq '0') {
				$error_message = "ERROR - GROUP MOVE ABORTED: The destiantion section does not exist.";
			} else {

				## UPDATE intranet_section_group
				my $command_update_section = "UPDATE intranet_section_group SET isg_is_id = '$destination_section', isg_seq_num = '$largest_section_seq_num' WHERE isg_id = '$show_sg'";
#				print "<P>COMMAND: $command_update_section";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_section) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;

				## SET show_s and show_sg TO DESTINATION SECTION/GROUP ID
				$show_s = $destination_section;

				## SET FEEDBACK MESSAGE
				$feedback_message = "The group was successfully moved to (sequence $largest_section_seq_num in) the section: $new_is_title.";

				## RESET THE PAGE SEQUENCING FOR THE ORIGINAL GROUP
						## RESET SEQUENCE NUMBERING FOR PAGES IN THE GROUP
				&re_sequence_groups_insection($original_section);

			} # END IF
		} # END IF
	## SHOW THE ADMIN MENU AGAIN
	$location = "admin";}
##########################################
# END: LOCATION process_admin_relocate_
##########################################



##########################################
# START: LOCATION admin_move_up_
##########################################


if ($location eq 'admin_move_up_page') {
	# CLEAN VARIABLES FOR DB QUERY
	$show_sg = &backslash_fordb($show_sg);

	my $old_seq_num = "";
	my $new_seq_num = "";
	my $abort_move = "no";

	# GET GROUP ORDER FOR AFFECTED GROUP ID
	my $command = "select page_group_seq from intranet_pages WHERE page_id = '$pid'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
		    my ($page_group_seq) = @arr;
			$old_seq_num = $page_group_seq;
			$new_seq_num = $old_seq_num - 1;
		}

	$abort_move = "yes" if ($new_seq_num < 1);
	if ($abort_move eq 'yes') {
		$error_message = "Move aborted. This page is already the top page in the group.";
	} else {
		# SET PAGE WITH ORDER (ORDER - 1) TO ORDER = 999
		my $command_move_obstacle = "UPDATE intranet_pages SET page_group_seq = '999'
									WHERE page_group_seq = '$new_seq_num' AND page_isg_id = '$show_sg'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_move_obstacle) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		## SET PAGE TO (GROUP -1)
		my $command_admin_move_up_page = "UPDATE intranet_pages SET page_group_seq = '$new_seq_num'
									WHERE page_group_seq = '$old_seq_num' AND page_isg_id = '$show_sg'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_admin_move_up_page) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		## SET PAGE = 999 TO GROUP
		my $command_move_obstacle2 = "UPDATE intranet_pages SET page_group_seq = '$old_seq_num'
									WHERE page_group_seq = '999' AND page_isg_id = '$show_sg'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_move_obstacle2) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		$feedback_message = "You successfully re-ordered the groups for this intranet section.";
	} # END IF/ELSE
	$location = "admin";
}


if ($location eq 'admin_move_up_group') {
	# CLEAN VARIABLES FOR DB QUERY
	$show_sg = &backslash_fordb($show_sg);

	my $old_seq_num = "";
	my $new_seq_num = "";
	my $this_isg_is_id = "";
	my $abort_move = "no";

	# GET GROUP ORDER FOR AFFECTED GROUP ID
	my $command = "select isg_is_id, isg_seq_num from intranet_section_group WHERE isg_id = '$show_sg'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
		    my ($isg_is_id, $isg_seq_num) = @arr;
			$old_seq_num = $isg_seq_num;
			$new_seq_num = $old_seq_num - 1;
			$this_isg_is_id = $isg_is_id;
		}

	$abort_move = "yes" if ($new_seq_num < 1);
	if ($abort_move eq 'yes') {
		$error_message = "Move aborted. This group is already the top page in the section.";
	} else {

		# SET GROUP WITH ORDER (ORDER - 1) TO ORDER = 999
		my $command_move_obstacle = "UPDATE intranet_section_group SET isg_seq_num = '999'
									WHERE isg_seq_num = '$new_seq_num' AND isg_is_id = '$this_isg_is_id'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_move_obstacle) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		## SET GROUP TO (GROUP -1)
		my $command_admin_move_up_group = "UPDATE intranet_section_group SET isg_seq_num = '$new_seq_num'
									WHERE isg_seq_num = '$old_seq_num' AND isg_is_id = '$this_isg_is_id'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_admin_move_up_group) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		## SET GROUP = 999 TO GROUP
		my $command_move_obstacle2 = "UPDATE intranet_section_group SET isg_seq_num = '$old_seq_num'
									WHERE isg_seq_num = '999' AND isg_is_id = '$this_isg_is_id'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_move_obstacle2) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		$feedback_message = "You successfully re-ordered the groups for this intranet section.";
	} # END IF/ELSE
	$location = "admin";
}
##########################################
# END: LOCATION admin_move_up_
##########################################



##########################################
# START: LOCATION ADMIN
##########################################


if ($location eq 'admin') {
	my $relocate = $query->param("relocate");
	   $relocate = "no" if ($relocate eq '');
print<<EOM;
$page_header_info
<TITLE>SEDL Intranet Administration - List of Pages</TITLE>
$htmlhead

<TABLE class="noBorder" CELLPADDING="15" CELLSPACING="0" width="100%">
<TR><TD>
EOM
print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
print<<EOM;
<H1 style="margin-top:0px;padding-top:0px">Intranet Administration Home - List of Intranet Sections, Groups, and Pages</H1>

EOM

		# START: QUERY DB FOR SECTIONS
		my $command_sections = "select * from intranet_section WHERE is_editable_by LIKE '%$cookie_ss_staff_id%'
		order by intranet_section.is_title";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_sections) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_sections = $sth->rows;
		my $last_section_group = "";

print<<EOM;
<p>
You can edit $num_sections of the main sections of the intranet. Click on a section heading to view the corresponding groups and pages.
<br><br>
A page whose row is shaded grey indicates that the page is set to NOT appear as a left-side menu item.
	<UL>
EOM
	my $count_sections_user_canedit = 0;
		while (my @arr = $sth->fetchrow) {
		    my ($is_id, $is_id_text, $is_title, $is_intro, $is_editable_by) = @arr;
				if ($show_s ne $is_id) {
					print "<li><a href=\"/staff/communications/intranet_page_manager.cgi?location=admin&amp;show_s=$is_id\">$is_title</a></li>";
				} else {
					print "<li><strong>$is_title</strong></li>";
				} # END IF/ELSE
			$count_sections_user_canedit++;
		} # END DB QUERY LOOP
		if ($count_sections_user_canedit > 2) {
print<<EOM;
	<li><a href="/staff/communications/intranet_page_manager.cgi?location=admin&amp;show_s=%">all sections</a></li>
EOM
		}
print<<EOM;
	</UL>
<p>
<TABLE class="noBorder" CELLPADDING="2" CELLSPACING="0" WIDTH=\"100%\">
<tr style="background:#D8D8D8">
	<td colspan=3 valign="top"><strong>Section, Group, or Page Title</strong></td>
	<td align="center"><strong>edit<br>the item</strong></td>
	<td align="center" valign="top"><strong>add groups<br>and pages</strong></td>
	<td align="center"><strong>Relocate (move)<br>this item</strong></td></tr>
EOM
		my $command = "select * from intranet_section
						 WHERE is_editable_by LIKE '%$cookie_ss_staff_id%'";
		   $command .= " AND is_id = '$show_s'" if (($show_s ne '') && ($show_s ne '%'));
		   $command .= " order by intranet_section.is_title";
#		   print "<P>$command";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		while (my @arr = $sth->fetchrow) {
		    my ($is_id, $is_id_text, $is_title, $is_intro, $is_editable_by) = @arr;

			# START: PRINT SECTION INFO
print<<EOM;
<a name = "is_id"></A>
<tr style="background:#DAE0F0">
	<td colspan=3 valign="top"><h4>$is_title</h4></td>
	<td valign="top" align="center"><a href="/staff/communications/intranet_page_manager.cgi?location=admin_edit_section&amp;show_s=$is_id&amp;camefrom=admin"><strong>edit section</strong></a></td>
	<td valign="top" align="center"><a href="/staff/communications/intranet_page_manager.cgi?location=admin_add_group&amp;show_s=$is_id"><strong>add group</strong></a></td><td>&nbsp;</td></tr>
EOM
			# END: PRINT SECTION INFO

			# START: QUERY DB FOR GROUPS AND PAGES BELONGING TO THIS SECTION
			my $command = "select intranet_section_group.*, intranet_pages.page_id, intranet_pages.page_id_text, intranet_pages.page_title, intranet_pages.page_display_in_leftnav, intranet_pages.page_group_seq
						from intranet_section_group left join intranet_pages
						on intranet_section_group.isg_id = intranet_pages.page_isg_id
						WHERE intranet_section_group.isg_is_id LIKE '$is_id'
						order by intranet_section_group.isg_seq_num, intranet_pages.page_group_seq";
#print "<P>COMMAND: $command";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
#print "<P>MATCHES: $num_matches";

			while (my @arr = $sth->fetchrow) {
			    my ($isg_id, $isg_id_text, $isg_is_id, $isg_title, $isg_seq_num, $isg_description, $isg_edit_committed, $isg_edit_author, $page_id, $page_id_text, $page_title, $page_display_in_leftnav, $page_group_seq) = @arr;
				if ($last_section_group ne $isg_id) {
					# START: PRINT GROUP INFO
my $bgcolor_group_altered = "style=\"background:#DAF0DB\"";
   $bgcolor_group_altered = "style=\"background:#FFF47A\"" if (($show_sg ne '') && ($pid eq '') && ($show_sg eq $isg_id));
print<<EOM;
<tr $bgcolor_group_altered>
	<td style="width20px;\">&nbsp;</td>
	<td colspan=2 valign="top">
EOM
						if ($isg_seq_num ne '1') {
print<<EOM;
<a href="/staff/communications/intranet_page_manager.cgi?location=admin_move_up_group&amp;show_sg=$isg_id&amp;show_s=$show_s"><img src="/staff/images/moveup.gif" alt="move group up" hspace="8" class="noBorder" align="right"></a>
EOM
						}
print<<EOM;
$isg_seq_num <a href="/cgi-bin/mysql/staff/index.cgi?show_sg=$isg_id"><strong>$isg_title</strong></a></td>
	<td valign="top" align="center"><a href="/staff/communications/intranet_page_manager.cgi?location=admin_edit_group&amp;show_sg=$isg_id"><strong>edit group</strong></a></td>
	<td valign="top" align="center"><a href="/staff/communications/intranet_page_manager.cgi?location=admin_add_page&amp;show_sg=$isg_id"><strong>add page</strong></a></td>
	<td valign="top" align="center">



EOM
						if (($relocate ne 'yes') || ($isg_id ne $show_sg) || ($pid ne '')) {
							print "<a href=\"/staff/communications/intranet_page_manager.cgi?location=admin&amp;show_s=$show_s&amp;show_sg=$isg_id&amp;relocate=yes\"><strong>relocate group</strong></a></td></tr>";
						} else {
my $bgcolor_page_altered = "";
   $bgcolor_page_altered = " style=\"background:#FFF47A\"" if (($isg_id ne '') && ($isg_id eq $show_sg) && ($pid eq '') );

print<<EOM;
</td></tr>
<tr $bgcolor_page_altered><td colspan="6" align="right" nowrap>
	<form action="/staff/communications/intranet_page_manager.cgi" method=POST>
	<table>
	<tr><td align=right><strong><label for="destination_section">Move group to section</label></strong><br>
	<select name="destination_section" id="destination_section">
EOM
							# QUERY DATABASE FOR SECTIONS/GROUPS IN ALL AREAS EXCEPT THIS SECTION
							my $command_get_section_and_group = "select is_title, is_id, is_id_text
								from intranet_section
								WHERE is_id NOT LIKE '$show_s'
								order by is_title";
							my $dbh = DBI->connect($dsn, "intranetuser", "limited");
							my $sth = $dbh->prepare($command_get_section_and_group) or die "Couldn't prepare statement: " . $dbh->errstr;
							$sth->execute;
							my $num_matches_sg = $sth->rows;
							my $last_is_title = "";
							while (my @arr = $sth->fetchrow) {
		    					my ($is_title, $is_id, $is_id_text) = @arr;
								if (($is_title ne $last_is_title) && ($last_is_title ne '')) {
#									print "<option value=\"\">------------------------------</option>\n";
								}
								print "<option value=\"$is_id\">$is_title</option>\n";
								$last_is_title = $is_title;
							} # END DB QUERY LOOP

print<<EOM;
	</select><br>
<input type="hidden" name="location" value="process_admin_relocate_group">
<input type="hidden" name="show_s" value="$show_s">
<input type="hidden" name="show_sg" value="$show_sg">
<input type="submit" name="submit" value="Move Group Now">
</form></td></tr></table>
</td></tr>
EOM
						} # END IF/ELSE

					# END: PRINT GROUP INFO
				} # END IF

				# START: PRINT PAGE INFO
#				print "<p class=\"alert\">'$page_id'</p>";
					if ($page_id ne '') {
my $bgcolor_page_altered = "";
   $bgcolor_page_altered = " style=\"background:#FFF47A\"" if (($pid ne '') && ($pid eq $page_id));
my $page_no_display_in_leftnav = "";
   $page_no_display_in_leftnav = "style=\"background:#cccccc\"" if ($page_display_in_leftnav eq 'no');
print<<EOM;
<tr $bgcolor_page_altered><td style="width:30px"></td>
	<td style="width:30px"></td>
	<td valign="top" $page_no_display_in_leftnav>
EOM
						if ($page_group_seq ne '1') {
print<<EOM;
<a href="/staff/communications/intranet_page_manager.cgi?location=admin_move_up_page&amp;show_sg=$isg_id&amp;show_s=$show_s&amp;pid=$page_id"><img src="/staff/images/moveup.gif" alt="move page up" hspace="8" class="noBorder" align="right"></a>
EOM
						}
print<<EOM;
		$isg_seq_num.$page_group_seq
EOM
	if ($page_id_text eq '') {
		print "	<a href=\"/cgi-bin/mysql/staff/index.cgi?pid=$page_id\">$page_title</a>\n";
	} else {
		print "	<a href=\"/cgi-bin/mysql/staff/index.cgi?page=$page_id_text\">$page_title</a>\n";
	}

print<<EOM;
	</td>
	<td valign="top" align="center"><a href="/staff/communications/intranet_page_manager.cgi?location=admin_edit_page&amp;show_sg=$isg_id&amp;pid=$page_id">edit page</a></td>
	<td>&nbsp;</td>
	<td valign="top" align="center">
EOM
						if (($relocate ne 'yes') || ($pid ne $page_id)) {
							print "<a href=\"/staff/communications/intranet_page_manager.cgi?location=admin&amp;show_sg=$isg_id&amp;show_s=$show_s&amp;pid=$page_id&amp;relocate=yes\">relocate page</a></td></tr>";
						} else {
print<<EOM;
</td></tr>
<tr $bgcolor_page_altered><td colspan="6" align=right nowrap><form action="/staff/communications/intranet_page_manager.cgi" method=POST>
	<table>
	<tr><td align=right><strong><label for="destination_group">Move page to section/group</label></strong><br>
	<select name="destination_group" id="destination_group">
EOM
	# QUERY DATABASE FOR SECTIONS/GROUPS IN ALL AREAS EXCEPT THIS SECTION
		my $command_get_section_and_group = "select intranet_section.is_title, intranet_section_group.*
								from intranet_section, intranet_section_group
								where intranet_section.is_id = intranet_section_group.isg_is_id
								order by intranet_section.is_title, intranet_section_group.isg_seq_num";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_section_and_group) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_sg = $sth->rows;
		my $last_is_title = "";
		while (my @arr = $sth->fetchrow) {
		    my ($is_title,
		     $isg_id, $isg_id_text, $isg_is_id, $isg_title, $isg_seq_num, $isg_description, $isg_edit_committed, $isg_edit_author) = @arr;
		     #$is_title = &trim_text_length($is_title, 35, "add_dots");
		     $isg_title = &trim_text_length($isg_title, 100, "add_dots");
			if (($is_title ne $last_is_title) && ($last_is_title ne '')) {
				print "<option value=\"\">------------------------------</option>\n";
			}
			print "<option value=\"$isg_id\">$is_title - $isg_title</option>\n";
			$last_is_title = $is_title;
		} # END DB QUERY LOOP

print<<EOM;
	</select><br>
<input type="hidden" name="location" value="process_admin_relocate_page">
<input type="hidden" name="pid" value="$pid">
<input type="hidden" name="show_s" value="$show_s">
<input type="hidden" name="show_sg" value="$show_sg">
<input type="submit" name="submit" value="Move Now">
</form></td></tr></table>
</td></tr>
EOM
						} # END IF/ELSE
					} # END IF page_id ne ''
				# END: PRINT PAGE INFO

				$last_section_group = $isg_id; # REMEMBER LAST SECTION GROUP ID
			} # END DB QUERY LOOP
			# END: QUERY DB FOR GROUPS AND PAGES BELONGING TO THIS SECTION


		} # END DB QUERY LOOP
		# END: QUERY DB FOR SECTIONS
#	<tr><td><img src=\"/img/spacer.gif\" height=\"1\" width=\"30\"></td><td><img src=\"/img/spacer.gif\" height=\"1\" width=\"30\"></td><td></td><td></td><td></td></tr>

print<<EOM;
	</TABLE>
</TD></TR>
</TABLE>

EOM
}
##########################################
# END: LOCATION ADMIN
##########################################


##########################################
# START: LOCATION ADMIN
##########################################
if ($location eq 'take-back-to-intranet') {
	## LOOK UP PAGE NAME
	my $redirect_to_this_url = "http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?show_s=$show_s&amp;show_sg=$show_sg&amp;pid=$pid";
	## START: GET PAGE ID SO URL IS NICE AND PRETTY
	my $command_get_page_id_text = "select page_id_text from intranet_pages where page_id = '$pid'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_get_page_id_text) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	while (my @arr = $sth->fetchrow) {
	   my ($page_id_text) = @arr;
		$redirect_to_this_url = "/cgi-bin/mysql/staff/index.cgi?page=$page_id_text";
	} # END: QUERY DB
	## END: GET PAGE ID SO URL IS NICE AND PRETTY

print<<EOM;
$page_header_info
<TITLE>SEDL intranet: Site Administration -  Edit Intranet Section</TITLE>
<META HTTP-EQUIV=REFRESH CONTENT="$time_delay_for_return;URL=$redirect_to_this_url">
$htmlhead
<TABLE class="noBorder" CELLPADDING="15" CELLSPACING="0" width="100%">
<TR><TD>

EOM
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print<<EOM;
</TD></TR>
</TABLE>
EOM
}
##########################################
# END: LOCATION ADMIN
##########################################


##########################################
# START: LOCATION SPELLCHECK
##########################################
if ($location eq 'spellcheck') {
		my $command_get_allpages = "select * from intranet_pages
								order by page_id";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_allpages) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;

#$num_matches_sg matches
		while (my @arr = $sth->fetchrow) {
		   my ($page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav,
		    $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author, $page_fullwidth) = @arr;
			print "<h1 style=\"margin-top:0px;padding-top:0px\">$page_id - $page_title</h1> $page_content";
		} # END: QUERY DB FOR EXISTING PAGE DETAILS
}
##########################################
# END: LOCATION SPELLCHECK
##########################################


################################################################################
## PRINT HTML FOOTER
################################################################################
if ((($cookie_ss_staff_id eq 'blitke') || ($cookie_ss_staff_id eq 'brollins')) && ($print ne 'yes')) {
#print<<EOM;
#<TABLE BORDER="0" CELLPADDING="15" CELLSPACING="0">
#<TR><TD><font color="#999999">
#	DEBUG VARIABLES:
#	<BR>LOCATION: $location
#	<BR>SHOW_S: $show_s
#	<BR>SHOW_SG: $show_sg
#	<BR>PID: $pid
#	<BR>ADMIN USER? $user_is_admin
#	<BR> USER ID $cookie_ss_session_id
#	</font></td></tr></table>
#EOM
}
print "$htmltail";


####################################################################
##  HERE ARE SOME SUBROUTINES USED BY THIS DATABASE SEARCH SCRIPT ##
####################################################################

## THIS SUBROUTINE MAKES SURE ANY SPECIAL CHARACTERS ARE BACKSLASHED BEFORE SENDING A COMMAND TO MYSQL
sub cleanthis {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/‘/\\‘/g;
   $dirtyitem =~ s/’/\\’/g;
   $dirtyitem =~ s/"/\\"/g;
   return($dirtyitem);
}

## THIS SUBROUTINE MAKES SURE ANY SPECIAL CHARACTERS ARE BACKSLASHED BEFORE SENDING A COMMAND TO MYSQL
sub clean_tabs_returns {
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
} # END SUB setCookie

sub getCookies {
	my (%cookies);
	foreach (split (/; /,$ENV{'HTTP_COOKIE'})){
		my($key) = split(/=/, $_);
		$cookies{$key} = substr($_, index($_, "=")+1);
		($cookies{$key}, $xxdate, $xxpath, $xxdomain) = split(/\,/,$cookies{$key});
	}
	return(%cookies);
} # END SUB getCookies


sub getCookiesfulldata {
	my (%cookies);
	foreach (split (/; /,$ENV{'HTTP_COOKIE'})){
		my($key) = split(/=/, $_);
		$cookies{$key} = substr($_, index($_, "=")+1);
		# ($cookies{$key}, $xxdate, $xxpath, $xxdomain) = split(/\,/,$cookies{$key});
	} # END FOREACH
	return(%cookies);
} # END SUB getCookiesfulldata


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
   $dirtyitem =~ s/\/\>/\>/g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/‘/\\‘/g;
   $dirtyitem =~ s/’/\\’/g;
   $dirtyitem =~ s/"/\\"/g;
   return($dirtyitem);
}
####################################################################
## END: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################


####################################################################
## START: FUNCTION - trim_text_length
####################################################################
sub trim_text_length {
	my $text_to_trim = $_[0];
	my $desired_char_length = $_[1];
	my $show_dots = $_[2];

	my $text_starting_length = length($text_to_trim);

	## TRIM DESCRIPTION TO A SENTENCE OR TWO
	while (length($text_to_trim) > $desired_char_length) {
		chop ($text_to_trim);
	}

	## REMOVE PARTIAL WORDS
	while ((substr($text_to_trim, length($text_to_trim) - 1, 1) ne ' ') && (length($text_to_trim) > 20)) {
		my $last_char = substr($text_to_trim, length($text_to_trim) - 1, 1);
		chop($text_to_trim);
	} # END WHILE

	## ADD TRAILING DOTS
	if (($show_dots eq 'add_dots') && (length($text_to_trim) < $text_starting_length)) {
		$text_to_trim = "$text_to_trim\...";
	}

	return($text_to_trim);
}
####################################################################
## END: FUNCTION - trim_text_length
####################################################################


####################################################################
## START: FUNCTION - clean_for_textid
####################################################################
sub clean_for_textid {
	my $this_text_id = $_[0];
	   $this_text_id =~ s/<.+?>/ /g; # remove HTML
	   $this_text_id =~ s/ /_/gi;
	   $this_text_id =~ s/-/_/gi;
	   $this_text_id =~ s/__/_/gi;
	   $this_text_id =~ tr/A-Za-z0-9_//cd;
	   $this_text_id =~ tr/A-Z/a-z/; # lowercase everything
	   $this_text_id = "REMOVETHISBIT$this_text_id";
	   $this_text_id =~ s/REMOVETHISBIT_//gi;
	   $this_text_id =~ s/REMOVETHISBIT//gi;
	   $this_text_id =~ s/__/_/gi;
	return($this_text_id);
}
####################################################################
## END: FUNCTION - clean_for_textid
####################################################################

####################################################################
## START: FUNCTION - check_unique_page_textid
####################################################################
sub check_unique_page_textid {
	my $this_page_id_text = $_[0];
	my $this_page_id = $_[1];
	my $this_feedback_message = "";
	my $this_time_delay = 3;

	$this_page_id_text =~ s/<.+?>//g; # remove HTML
	$this_page_id_text =~ s/__/_/g; # remove HTML

	## START: RESET SEQUENCE NUMBERING FOR PAGES IN THE GROUP
		my $command_check_text_id = "select page_id, page_id_text
								from intranet_pages where page_id_text = '$this_page_id_text'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_check_text_id) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#print "<p class=\"alert\">$command_check_text_id <BR><BR>MATCHES: $num_matches</p>";
		while (my @arr = $sth->fetchrow) {
		    my ($page_id, $page_id_text) = @arr;

			if ($page_id ne $this_page_id) {
				my $new_text_id_found = "no";
				my $counter = 2;
				while ($new_text_id_found eq 'no') {
					my $alternate_text_id = "$this_page_id_text\_$counter";
					my $command_check_text_id = "select page_id, page_id_text
						from intranet_pages where page_id_text = '$alternate_text_id'";
					my $dbh = DBI->connect($dsn, "intranetuser", "limited");
					my $sth = $dbh->prepare($command_check_text_id) or die "Couldn't prepare statement: " . $dbh->errstr;
					$sth->execute;
					my $num_matches = $sth->rows;
					if ($num_matches == 0) {
						$new_text_id_found = "yes";
						$this_feedback_message = "The TEXT ID you selected ($this_page_id_text) was already in use. An altertate text ID ($alternate_text_id) was used instead.";
						$this_page_id_text = $alternate_text_id;
						$this_time_delay = 8;
					} # END IF
					$counter++;
				} # END DB QUERY LOOP
			} # END IF

		} # END DB QUERY LOOP
		## END: RESET SEQUENCE NUMBERING FOR PAGES IN THE GROUP
	return($this_page_id_text, $this_feedback_message, $this_time_delay)
}
####################################################################
## END: FUNCTION - check_unique_page_textid
####################################################################


####################################################################
## START: FUNCTION - check_unique_group_textid
####################################################################
sub check_unique_group_textid {
	my $this_group_id_text = $_[0];
	my $this_group_id = $_[1];
	my $this_feedback_message = "";
	my $this_time_delay = 3;

	$this_group_id_text =~ s/<.+?>/ /g; # remove HTML
	$this_group_id_text =~ s/__/_/g; # remove HTML

		## START: CHECK IF TEXT ID IN USE
		my $command_check_text_id = "select isg_id, isg_id_text
								from intranet_section_group where isg_id_text = '$this_group_id_text'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_check_text_id) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#print "<p class=\"alert\">$command_check_text_id <BR><BR>MATCHES: $num_matches</p>";
		while (my @arr = $sth->fetchrow) {
		    my ($isg_id, $isg_id_text) = @arr;

			if ($isg_id ne $this_group_id) {
				my $new_text_id_found = "no";
				my $counter = 2;
				while ($new_text_id_found eq 'no') {
					my $alternate_text_id = "$this_group_id_text\_$counter";
					my $command_check_text_id = "select isg_id, isg_id_text
						from intranet_section_group where isg_id_text = '$alternate_text_id'";
					my $dbh = DBI->connect($dsn, "intranetuser", "limited");
					my $sth = $dbh->prepare($command_check_text_id) or die "Couldn't prepare statement: " . $dbh->errstr;
					$sth->execute;
					my $num_matches = $sth->rows;
					if ($num_matches == 0) {
						$new_text_id_found = "yes";
						$this_feedback_message = "The TEXT ID you selected ($this_group_id_text) was already in use. An altertate text ID ($alternate_text_id) was used instead.";
						$this_group_id_text = $alternate_text_id;
						$this_time_delay = 8;
					} # END IF
					$counter++;
				} # END DB QUERY LOOP
			} # END IF

		} # END DB QUERY LOOP
		## END: CHECK IF TEXT ID IN USE
	return($this_group_id_text, $this_feedback_message, $this_time_delay)
}
####################################################################
## END: FUNCTION - check_unique_group_textid
####################################################################


####################################################################
## START: FUNCTION - re_sequence_groups_insection
####################################################################
sub re_sequence_groups_insection {
	my $section_id_to_resequence = $_[0];

	## START: RESET SEQUENCE NUMBERING FOR PAGES IN THE GROUP
		my $command_get_section = "select isg_id, isg_seq_num
								from intranet_section_group
								where isg_is_id = '$section_id_to_resequence'
								order by isg_seq_num";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_section) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $sequence_should_be = 1;
		while (my @arr = $sth->fetchrow) {
		    my ($isg_id, $isg_seq_num) = @arr;
			if ($isg_seq_num ne $sequence_should_be) {
				my $command_set_sequence = "UPDATE intranet_section_group SET isg_seq_num = '$sequence_should_be'
								where isg_id = '$isg_id'";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command_set_sequence) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			} # END IF
			$sequence_should_be++;
		} # END DB QUERY LOOP
		## END: RESET SEQUENCE NUMBERING FOR PAGES IN THE GROUP
}
####################################################################
## END: FUNCTION - re_sequence_groups_insection
####################################################################


####################################################################
## START: FUNCTION - re_sequence_page_ingroup
####################################################################
sub re_sequence_page_ingroup {
	my $group_id_to_resequence = $_[0];

	## START: RESET SEQUENCE NUMBERING FOR PAGES IN THE GROUP
		my $command_get_section = "select page_id, page_group_seq
								from intranet_pages
								where page_isg_id = '$group_id_to_resequence'
								order by page_group_seq";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_section) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $sequence_should_be = 1;
		while (my @arr = $sth->fetchrow) {
		    my ($page_id, $page_group_seq) = @arr;
			if ($page_group_seq ne $sequence_should_be) {
				my $command_set_sequence = "UPDATE intranet_pages SET page_group_seq = '$sequence_should_be'
								where page_id = '$page_id'";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command_set_sequence) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			} # END IF
			$sequence_should_be++;
		} # END DB QUERY LOOP
		## END: RESET SEQUENCE NUMBERING FOR PAGES IN THE GROUP
}
####################################################################
## END: FUNCTION - re_sequence_page_ingroup
####################################################################


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
	$cleanitem =~ s/¿/&iquest\;/g;
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





##################################################################
## START SUBROUTINE: SPLIT SEARCH STRING INTO WORDS TO SEARCH FOR
##################################################################

sub print_tinyMCE_code {
print<<EOM;
<script type="text/javascript" src="/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	extended_valid_elements : \"script,iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],\$elements\",
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
EOM
}
##################################################################
## END SUBROUTINE: SPLIT SEARCH STRING INTO WORDS TO SEARCH FOR
##################################################################

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
<select name="$field_name" id="$field_name" alt="$previous_selection">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<option VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</option>\n";
			$item_counter++;
		} # END WHILE
	print "</select>\n";
######################################
} # END: SUBROUTINE print_yes_no_menu
######################################

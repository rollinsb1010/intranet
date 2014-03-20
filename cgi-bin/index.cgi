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
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

#use LWP::Simple;
my $query = new CGI;

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib 'cgi/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode;

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
	my $time_hour_leadingzero = POSIX::strftime('%I', localtime(time)); # Hour w/leadingsero (e.g. 09 or 09)
	my $time_hour_mil = POSIX::strftime('%k', localtime(time)); # Hour in military notation (e.g. 9 or 21)
	my $time_hour_mil_leadingzero = POSIX::strftime('%H', localtime(time)); # Hour in military notation w/leadingsero (e.g. 09 or 21)
	my $time_min = POSIX::strftime('%M', localtime(time)); # Minutes (e.g. 39)
	my $time_sec = POSIX::strftime('%S', localtime(time)); # Seconds (e.g. 38)

	my $timestamp = "$year$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; # 14-digit timestamp (e.g. 20080306143938)
	my $timestamp_short = "$year$month$monthdate_wleadingzero"; # 14-digit timestamp (e.g. 20080306143938)

	my $date_full_pretty_4digityear = "$month/$monthdate_wleadingzero/$year"; # Full date in human-readable format  (e.g. 03/06/08)

my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
	my $this_user_id = "$timestamp$remote_host$remote_addr";
	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################



# GET CURRENT LOCATION INFORMTION
my $location = $query->param("location");
   $location = "show_page" if $location eq '';

###########################################
# START: GRAB SEDL HEADER AND FOOTER HTML #
###########################################
my $htmlhead = "";
my $htmltail = "";

open(HTMLHEAD,"</cgi/includes/header2012.txt");
while (<HTMLHEAD>) {
	$htmlhead .= $_;
}
close(HTMLHEAD);

open(HTMLTAIL,"</cgi/includes/footer2012.txt");
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
					<tr><td style=\"background:#ffffff;\"><a href=\"/cgi/staff/index.cgi?location=customize\"><img src=\"/cgi/images/template/sidebar-quicklinks2.gif\" alt=\"my quick links\" class=\"noBorder\"></a></td></tr><tr><td style=\"background:#FFFFFF;\">
							<table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";
my $sidebar_boxbottom = "</td></tr></table>
					</td></tr><tr><td valign=\"top\" style=\"background:#97B038\"><img src=\"/staff/images/sidebar-round-bottom-97B038.gif\" class=\"decoration\" alt=\"\"></td></tr></table>";

my $sidebar_boxtop_staff = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td style=\"background:#ffffff;\"><img src=\"/cgi/images/template/sidebar-round-top-staff.gif\" class=\"decoration\" alt=\"SEDL Staff\"></td></tr><tr><td style=\"background:#FFFFFF;\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";
my $sidebar_boxtop_login = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td style=\"background:#ffffff;\"><img src=\"/cgi/images/template/sidebar-round-top-login.gif\" class=\"decoration\" alt=\"Please Log In\"></td></tr><tr><td style=\"background:#FFFFFF;\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";
my $sidebar_boxtop_sedlstar = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td style=\"background:#ffffff;\"><img src=\"/cgi/images/template/sidebar-round-top-sedlstar.gif\" class=\"decoration\" alt=\"SEDL Star of the Month\"></td></tr><tr><td style=\"background:#FFFFFF;\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";
my $sidebar_boxtop_pressreleases = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td><a href=\"/new/media.html\"><img src=\"/cgi/images/template/sidebar-round-top-press.gif\" class=\"decoration noBorder\" alt=\"SEDL Press Releases\"></a></td></tr><tr><td style=\"background:#FFFFFF;\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";
my $sidebar_boxtop_suggestionbox = "<table width=\"100%\" class=\"noBorder\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:#$bgcolor\"><tr><td><a href=\"http://www.sedl.org/staff/communications/suggestion_box.cgi\"><img src=\"/staff/images/template/sidebar-boxtop_suggestionbox.gif\" alt=\"SEDL Suggestion Box\" class=\"noBorder decoration\"></a></td></tr><tr><td style=\"background:#FFFFFF;\"><table class=\"noBorder\" cellpadding=\"4\" cellspacing=\"0\"><tr><td style=\"background:#FFFFFF;\">";

my $location_admin_script = "/cgi/communications/intranet_page_manager.cgi";
###########################################
# END: GRAB SEDL HEADER AND FOOTER HTML #
###########################################




########################################
## START: READ VARIABLES PASSED BY USER
########################################
my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"

my $logon_pass = $query->param("logon_pass");

my $super_login = "no";
   $super_login = "yes" if ($logon_pass eq 'sudo');

my $newpass = $query->param("prompt_newpass");

my $page = $query->param("page"); # text ID for page
my $pid = $query->param("pid");
	$htmlhead =~ s/location\=customize/location\=customize\&pid=$pid/gi;

my $section = $query->param("section"); # text ID for section
my $show_s = $query->param("show_s");

my $group = $query->param("group"); # text ID for section
my $show_sg = $query->param("show_sg");
my $searchfor = $query->param("searchfor");
my $list_page = $query->param("list_page");

	my %checkbox_quicklink_labels;
		$checkbox_quicklink_labels{'http://www.sedl.org/staff/sims/logs/service_log_menu.php'} = "Service Log";
		$checkbox_quicklink_labels{'http://www.sedl.org/staff/personnel/leavereport.cgi'} = "Leave Report";
		$checkbox_quicklink_labels{'http://www.sedl.org/staff/personnel/budgets.cgi'} = "Budget Report";
		$checkbox_quicklink_labels{'http://www.sedl.org/staff/communications/email_campaigns.cgi'} = "E-mail Campaign Mgr";
		$checkbox_quicklink_labels{'http://www.sedl.org/staff/communications/registration-admin.cgi'} = "Event Registration Manager";
		$checkbox_quicklink_labels{'http://www.sedl.org/staff/quality/clientsurveys.cgi'} = "Product Surveys";
		$checkbox_quicklink_labels{'http://www.sedl.org/cgi/index.cgi?pid=112'} = "IRC Catalog";
		$checkbox_quicklink_labels{'http://survey.sedl.org/efm/login.aspx'} = "Vovici Surveys";
		$checkbox_quicklink_labels{'https://ibuilder5.verticalresponse.com/app/login'} = "Vertical Response";

	##################################################################
	## START: BACKSLASH VARIABLES USED TO LOAD PAGES FROM DATABASE
	##################################################################
	$session_id = &commoncode::cleanthisfordb($session_id);
	$logon_user = &commoncode::cleanthisfordb($logon_user);
	$logon_pass = &commoncode::cleanthisfordb($logon_pass);

	$page = &commoncode::cleanthisfordb($page);
	$pid = &commoncode::cleanthisfordb($pid);
	$section = &commoncode::cleanthisfordb($section);
	$show_s = &commoncode::cleanthisfordb($show_s);
	$show_sg = &commoncode::cleanthisfordb($show_sg);
	$group = &commoncode::cleanthisfordb($group);
	$searchfor = &commoncode::cleanthisfordb($searchfor);
	$list_page = &commoncode::cleanthisfordb($list_page);
	##################################################################
	## END: BACKSLASH VARIABLES USED TO LOAD PAGES FROM DATABASE
	##################################################################

	# SET STAFF AS DEFAULT SEARCH IF IT WAS JUST USED
	if ($searchfor ne '') {
		$htmlhead =~ s/STAFF\"\>/STAFF\" SELECTED\>/g;
	}


## DECLARE FEEDBACK VARIABLES
my $error_message = "";
my $feedback_message = "";

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
## START: LOCATION = PROCESS_LOGON
######################################################
if ($location eq 'process_logon') {
	if (($logon_user ne '') && ($logon_pass ne '')) {
		## CHECK LOGON
		my $strong_pwd = crypt($logon_pass,'password');
		my $command = "select userid from staff_profiles where
			((userid like '$logon_user') AND (strong_pwd LIKE '$strong_pwd') )";
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

		## GET THE RESULTS OF THE QUERY
		## while (my @arr = $sth->fetchrow) {
		## my ($userid) = @arr;

		## }

		if (($num_matches eq '1') || (($super_login eq 'yes') && ($num_matches_for_logon_id_entered == 1)) ) {
			$cookie_ss_session_id = "$logon_user$session_suffix";
			## VALID ID/PASSWORD, SET SESSION
				my $command_set_session = "REPLACE into staff_sessions VALUES ('$cookie_ss_session_id ', '$logon_user', '$timestamp', '$remote_addr', '', '', '', '')";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command_set_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
#my $num_matches = $sth->rows;
   $error_message .= "<P>NOTICE INITIATE SESSION: $command_set_session";

			## SAVE COOKIES WITH SESSION ID AND USER ID
				$cookie_ss_staff_id = $logon_user;
				setCookie ("ss_session_id", "$cookie_ss_session_id", $expdate, $path, $thedomain);
				setCookie ("staffid", $logon_user, $expdate, $path, $thedomain);
			## SET LOCATION
				$location = "show_page";

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
			$location = "show_page"; # SHOW LOGON SCREEN
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
my $trash_not_to_keep = "";
if ($location eq 'logout') {
	## DELETE SESSION IN RF_SESSION DB
	my $command_delete_session = "DELETE FROM staff_sessions WHERE ss_session_id='$cookie_ss_session_id'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_delete_session) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	$cookie_ss_session_id = "";
	setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
	$location = "show_page"; # AFTER LOGOUT, SHOW LOGON SCREEN
	my $logout_url = "http://www.sedl.org/staff/sims/sims_checksession.php?sims_session=logout";
}
######################################################
## END: LOCATION = LOGOUT
######################################################


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
#	$error_message .= "NOTICE: $command<BR><BR>MATCHES: $num_matches";

		while (my @arr = $sth->fetchrow) {
		my ($ss_session_id, $ss_staff_id, $ss_session_last_active, $ss_ip_number, $ss_session_active, $ss_staff_project, $ss_search_site, $ss_last_page_viewed) = @arr;
			## IF SESSION STILL ACTIVE, SET TIMESTAMP IN SESSION DB
			if ($ss_staff_id ne '') {
				my $command_update_session = "REPLACE into staff_sessions VALUES ('$ss_session_id', '$ss_staff_id', '$timestamp', '$remote_addr', '', '', '' ,'')";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
			## SAVE COOKIES WITH SESSION ID AND USER ID
				$cookie_ss_staff_id = $ss_staff_id;
				setCookie ("ss_session_id", $ss_session_id, $expdate, $path, $thedomain);
				setCookie ("staffid", $ss_staff_id, $expdate, $path, $thedomain);
			} else {
				$num_matches = 0;
			}
		} # END DB QUERY LOOP

		## IF SESSION NOT ACTIVE, PROMPT FOR LOGON
		if ($num_matches eq '0') {
			$cookie_ss_session_id = "";
			setCookie ("ss_session_id", "", $expdate, $path, $thedomain);  ## COMMENTED OUT FOR TESTING - UNCOMMENT WHEN LIVE (BL: 6/10/2010)
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


##########################################
# START: LOCATION PROCESS_PREFERENCES
##########################################
if ($location eq 'process_preferences') {
	## START: GRAB NEW USER PREFERENCES
	my $pref_navlocation = $query->param('pref_navlocation');
	my $pref_css = $query->param('pref_css');
	my $pref_color = $query->param('pref_color');

	## START: COMPOSITE FIELD - pref_quicklinks
	my $pref_quicklinks;
	my $counter_read_QL = 1;
	while ($counter_read_QL <= 10) {
		my $new_QL_menu_linkurl = $query->param("new_QL_menu_linkurl_$counter_read_QL"); # PID OF SELECTION
		my $new_QL_menu_linktitle = ""; # WE'LL LOOKUP THE TITLE FOR THIS PID
			if ($new_QL_menu_linkurl ne '') {
#				## START: LOOKUP PAGE TITLE FOR THIS PID
#				my $command_get_section = "select page_title FROM intranet_pages WHERE page_id = '$new_QL_menu_linkurl'";
#				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#				my $sth = $dbh->prepare($command_get_section) or die "Couldn't prepare statement: " . $dbh->errstr;
#				$sth->execute;
#
#				while (my @arr = $sth->fetchrow) {
#				    my ($page_title) = @arr;
#				    $ = $page_title;
#				}
#				## END: LOOKUP PAGE TITLE FOR THIS PID
#
#				$new_QL_menu_linkurl = "http://www.sedl.org/cgi/index.cgi?pid=$new_QL_menu_linkurl";
				$new_QL_menu_linktitle = $checkbox_quicklink_labels{$new_QL_menu_linkurl}
			}


		my $new_QL_box_linkurl = $query->param("new_QL_box_linkurl_$counter_read_QL");
		my $new_QL_box_linktitle = $query->param("new_QL_box_linktitle_$counter_read_QL");

		## START: MAKE SURE URL STARTS with http://
		if (($new_QL_box_linkurl ne '') && ($new_QL_box_linkurl !~ 'http')) {
			$new_QL_box_linkurl =~ s/http\:\/\///gi;
			$new_QL_box_linkurl = "http://$new_QL_box_linkurl";
		}
		## END: MAKE SURE URL STARTS with http://

		if (($new_QL_box_linkurl ne '') && ($new_QL_box_linktitle ne '')) {
			$pref_quicklinks .= "$new_QL_box_linkurl\t$new_QL_box_linktitle\t";
		} elsif (($new_QL_menu_linkurl ne '') && ($new_QL_menu_linktitle ne '')) {
			$pref_quicklinks .= "$new_QL_menu_linkurl\t$new_QL_menu_linktitle\t";
		} else {
			$pref_quicklinks .= "\t\t";
		} # END IF
		$counter_read_QL++;
	} # END WHILE LOOP
	## END: COMPOSITE FIELD - pref_quicklinks

	## END: GRAB NEW USER PREFERENCES

	$pref_css = &commoncode::cleanthisfordb($pref_css);
	$pref_color = &commoncode::cleanthisfordb($pref_color);
	$pref_navlocation = &commoncode::cleanthisfordb($pref_navlocation);
	$pref_quicklinks = &commoncode::cleanthisfordb($pref_quicklinks);


	## SAVE PREFS TO DATABASE
	my $command_save_prefs = "REPLACE INTO intranet_prefs VALUES ('$cookie_ss_staff_id', '$pref_css', '$pref_color', '$pref_navlocation', '$pref_quicklinks', '')";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_save_prefs) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;

	$feedback_message = "Your preferences were updated successfully.";
	$location = "show_page";
}
##########################################
# END: LOCATION PROCESS_PREFERENCES
##########################################


##########################################
# START: LOCATION CUSTOMIZE
##########################################
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
		while (my @arr = $sth->fetchrow) {
		    ($p_userid, $p_css, $p_color, $p_navlocation, $p_quicklinks_full) = @arr;
		}
	## END: READ PREFERENCES

	my @p_quicklinks = split(/\t/,$p_quicklinks_full);



	## SHOW ERROR IF TRYING TO SET PREFS BEFORE LOGGING IN
	if (($location eq 'customize') && ($cookie_ss_session_id eq '')) {
		$error_message = "Please log in before setting your preferences.";
		$location = "show_page";
	}

if ($location eq 'customize') {
print<<EOM;
$page_header_info
<TITLE>Customize Your Intranet Preferences</TITLE>
$htmlhead
<div style="padding:15px;">
<H1 style="margin-top:0px;padding-top:0px">Customize Your Intranet Preferences and "Quick Links"</H1>
<p>
At this time, you may customize the following two intranet preference settings.
</p>

<FORM ACTION="/cgi/index.cgi" METHOD=POST>
<table class="oneBorder" CELLPADDING="4" CELLSPACING="0">
<tr><td VALIGN="TOP"><strong>1. Side Navigation</strong><br>
					Would you like the side navigation positioned on the left or the right side of the page?</td>
	<td VALIGN="TOP" width="100">
EOM
	my @options = ( "left", "right");
	my $counter_options = "0";
		while ($counter_options <= $#options) {
			print "	<INPUT TYPE=\"RADIO\" NAME=\"pref_navlocation\" VALUE=\"$options[$counter_options]\" ";
			print " CHECKED" if ($p_navlocation eq $options[$counter_options]);
			print ">$options[$counter_options]<BR>";
			$counter_options++;
		}
print<<EOM;
	</td>
	<td valign="top">
		<INPUT TYPE=SUBMIT VALUE="Save Preferences">
	</td></tr>

<tr><td VALIGN="TOP" colspan="2"><strong>2. Quick Links</strong><br>
						Please indicate <u>up to 10</u> links to appear in your "Quick Links" menu in the side navigation
						panel of the intranet.
<P>
		For each of the 10 Quick Link spaces below, you may either
			<ul>
			<li>select one of the standard links from the pull-down menu,</li>
			<li>input your own title and link, or</li>
			<li>leave the item blank.</li>
			</ul>
		<table class="oneBorder" cellpadding="2" cellspacing="0" align="right">
		<tr><td style="background:#ebebeb">Position</td>
			<td style="background:#ebebeb">Quick Link Selection</td></tr>
EOM
	## PARSE QUICKLINKS
my $counter = "1";
my $counter_quicklinks = "0";
my $counter_forlinktitle;
	while ($counter <= 10) {
		$counter_forlinktitle = $counter_quicklinks + 1;
		print "<tr><td valign=\"top\">$counter\.</td><td valign=\"top\">";
		&show_QL_pullmenu_and_textbox($counter, $p_quicklinks[$counter_quicklinks], $p_quicklinks[$counter_forlinktitle]);
		print "</td></tr>";
		$counter_quicklinks = $counter_quicklinks + 2;
		$counter++;
	}

#####################################################
## START SUBROUTINE - show_QL_pullmenu_and_textbox
#####################################################
sub show_QL_pullmenu_and_textbox {
	my $this_QL_number = $_[0];
	my $this_QL_url = $_[1];
	my $this_QL_title = $_[2];

	my $flag_found_link_in_pullmenu = "no";
	my @checkbox_quicklink_pid = (""
		, "http://www.sedl.org/staff/sims/logs/service_log_menu.php"
		, "http://www.sedl.org/staff/personnel/leavereport.cgi"
		, "http://www.sedl.org/staff/personnel/budgets.cgi"
		, "http://www.sedl.org/staff/communications/email_campaigns.cgi"
		, "http://www.sedl.org/staff/communications/registration-admin.cgi"
		, "http://www.sedl.org/staff/quality/clientsurveys.cgi"
		, "http://www.sedl.org/cgi/index.cgi?pid=112"
		, "http://survey.sedl.org/efm/login.aspx"
		, "https://ibuilder5.verticalresponse.com/app/login"
		);

print<<EOM;
Either select a menu item:
	<ul>
	<select name="new_QL_menu_linkurl_$this_QL_number">
EOM
my $counter_checkboxes = 0;
	while ($counter_checkboxes <= $#checkbox_quicklink_pid) {
		print "\t<option value=\"$checkbox_quicklink_pid[$counter_checkboxes]\" ";
		my $checkbox_url_link = "$checkbox_quicklink_pid[$counter_checkboxes]";
		if ($checkbox_url_link eq $this_QL_url) {
			print "SELECTED";
			$flag_found_link_in_pullmenu = "yes";
		}
		print ">$checkbox_quicklink_labels{$checkbox_quicklink_pid[$counter_checkboxes]}</option>\n";
		$counter_checkboxes++;
	}

print<<EOM;
	</select>
	</UL>
EOM
	## DON'T SHOW LINK IN TEXT BOX IF IT WAS FOUND IN THE PULL-MENU
	if ($flag_found_link_in_pullmenu eq 'yes') {
		$this_QL_url = "";
		$this_QL_title = "";
	}
print<<EOM;
Or input your own link:<br>
<ul>
	Text for Link: <input type="text" name="new_QL_box_linktitle_$this_QL_number" value="$this_QL_title" SIZE="20"><br>
	Web Address: <input type="text" name="new_QL_box_linkurl_$this_QL_number" value="$this_QL_url" SIZE="60">
</ul>
EOM
}
#####################################################
## END SUBROUTINE - show_QL_pullmenu_and_textbox
#####################################################


print<<EOM;
		</table>
	</td>
	<td valign="top">
		<INPUT TYPE=SUBMIT VALUE="Save Preferences">
	</td></tr>
	</table>
<div style="margin-left:15px;">
<INPUT TYPE="HIDDEN" NAME="pid" VALUE="$pid">
<INPUT TYPE="HIDDEN" NAME=location VALUE="process_preferences">
<INPUT TYPE=SUBMIT VALUE="Save Preferences">
</div>

</form>
</div>
EOM
}
##########################################
# END: LOCATION CUSTOMIZE
##########################################


###############################################
## START: USER PREFERENCES
###############################################
my $pref_side_navigation = $p_navlocation;
	## SET DEFAULT PREFERENCES
	$pref_side_navigation = "left" if ($pref_side_navigation eq '');
###############################################
## END: USER PREFERENCES
###############################################


##########################################
# START: LOCATION ADMIN CHECK
##########################################
my $user_is_admin = "no";
	if (($location =~ 'admin') && ($cookie_ss_session_id eq '')) {
		$error_message = "Please log in before performing intranet administrative functions.";
		$location = "show_page";
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


##########################################
# START: LOCATION SITEMAP
##########################################
if ($location eq 'sitemap') {
	my $relocate = $query->param("relocate");
	   $relocate = "no" if ($relocate eq '');
print<<EOM;
$page_header_info
<TITLE>SEDL Intranet Administration - Site Map</TITLE>
$htmlhead

<div style="padding:15px;">
EOM
print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
print<<EOM;
<H1 style="margin-top:0px;padding-top:0px">Intranet Site Map - List of Intranet Sections, Groups, and Pages</H1>

EOM

		# START: QUERY DB FOR SECTIONS
		my $command_sections = "select * from intranet_section WHERE (is_id not like '9' AND is_id not like '8')
		order by intranet_section.is_title";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_sections) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $last_section_group = "";
		my $num_sections = $sth->rows;

print<<EOM;
<p></p>
<table class="noBorder" CELLPADDING="2" CELLSPACING="0" WIDTH="100%">
<tr style=\"background:#D8D8D8">
	<td colspan="3" valign="top"><strong>Section, Group, or Page Title</strong></td></tr>
EOM
		my $command = "select * from intranet_section WHERE is_id not like '9' AND is_id not like '8' AND is_id not like '10'";
		   $command .= " order by intranet_section.is_title";
#		   print "<P>$command";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		while (my @arr = $sth->fetchrow) {
		    my ($is_id, $is_id_text, $is_title, $is_intro, $is_editable_by) = @arr;

			# START: PRINT SECTION INFO
print<<EOM;
<a name = "is_id"></a>
<tr>
	<td colspan="3" valign="top"><strong>$is_title</strong></td></tr>
EOM
			# END: PRINT SECTION INFO

			# START: QUERY DB FOR GROUPS AND PAGES BELONGING TO THIS SECTION
			my $command = "select intranet_section_group.*, intranet_pages.page_id, intranet_pages.page_title, intranet_pages.page_group_seq
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
			    my ($isg_id, $isg_id_text, $isg_is_id, $isg_title, $isg_seq_num, $isg_description, $isg_edit_committed, $isg_edit_author, $page_id, $page_title, $page_group_seq) = @arr;
				if ($last_section_group ne $isg_id) {
					# START: PRINT GROUP INFO
				my $link_to_group = "<a href=\"/cgi/index.cgi?show_sg=$isg_id\"><strong>$isg_title</strong></a>";
					if ($isg_id_text ne '') {
						$link_to_group = "<a href=\"/cgi/index.cgi?group=$isg_id_text\"><strong>$isg_title</strong></a>";
					} # END IF
print<<EOM;
<tr>
	<td style="width:20px">&nbsp;</td>
	<td colspan="2" valign="top">$isg_seq_num $link_to_group</td>
</tr>
EOM
				} # END IF

				# START: PRINT PAGE INFO
#					if ($page_id ne '') {
#print<<EOM;
#<tr><td width="30"></td>
#	<td width="30"></td>
#	<td valign="top">$isg_seq_num.$page_group_seq <a href="/cgi/index.cgi?location=show_page&show_sg=$isg_id&pid=$page_id">$page_title</a></td>
#</tr>
#EOM
#					} # END IF page_id ne ''
				# END: PRINT PAGE INFO

				$last_section_group = $isg_id; # REMEMBER LAST SECTION GROUP ID
			} # END DB QUERY LOOP
			# END: QUERY DB FOR GROUPS AND PAGES BELONGING TO THIS SECTION


		} # END DB QUERY LOOP
		# END: QUERY DB FOR SECTIONS

print<<EOM;
	</table>
</div>

EOM
}
##########################################
# END: LOCATION SITEMAP
##########################################





##########################################
# START: LOCATION WEB_SITES
##########################################
if ($location eq 'web_sites') {

print<<EOM;
$page_header_info
<TITLE>SEDL Project Web Sites</TITLE>
$htmlhead
<div style="padding:15px;">

<h1 style="margin-top:0px;padding-top:0px">SEDL Project Web Sites</h1>

<table BORDER="1" CELLPADDING="2" CELLSPACING="0" WIDTH="100%">
<tr><td valign="top" style="background:#EBEBEB"><strong>Project Name</strong></td>
	<td valign="top" style="background:#EBEBEB"><strong>Public Site</strong></td>
	<td valign="top" style="background:#EBEBEB" align="center"><strong>SEDL Staff-only Data Administration Pages</strong></td></tr>
<tr><td valign="top">Illinois Quality Afterschool</td>
	<td valign="top"><a href="http://www.sedl.org/iqa/">http://www.sedl.org/iqa/</a>
		<UL>
		<LI><a href="http://www.sedl.org/iqa/presentations.cgi">Request for Presentation Proposals Form</a></li>
		</UL></td>
	<td valign="top">
		<UL>
		<LI><a href="http://www.sedl.org/iqa/staff/presentations_report.cgi">Presentation Submitted</a></li>
		</UL>
	</td></tr>
<tr><td valign="top">National Center for Family and Community<br>Connections with Schools</td>
	<td valign="top"><a href="http://www.sedl.org/connections/">http://www.sedl.org/connections/</a>
		<UL>
		<LI><a href="http://www.sedl.org/symposium2004/">Harvard Symposium</a></li>
		<LI><a href="http://www.sedl.org/learning/">SEDL Online Learning Center</a></li>
		</UL></td>
	<td valign="top"></td></tr>
<tr><td valign="top">National Center for Quality Afterschool</td>
	<td valign="top"><a href="http://www.sedl.org/afterschool/">http://www.sedl.org/afterschool/</a>
					<ul>
					<li><a href="/afterschool/toolkits/">Afterschool Training Toolkit</a></li>
					<li>Curriculum Guides: <a href="/afterschool/guide/science/">Science</a>,
					   <a href="/afterschool/guide/math/">Math</a>,
					   <a href="/afterschool/guide/literacy/">Literacy</a>,
					   <a href="/afterschool/guide/technology/">Technology</a></li>
					<li><a href="/afterschool/lessonplans/"> Lesson Plan DB</a></li>
					</ul>
	</td>
	<td valign="top"><ul>
					<li><a href="/staff/afterschool/lessonplan_manager.cgi">Lesson Plan DB Mgr.</a></li>
					<li>Curriculum Databases: <a href="/afterschool/guide/math_lit_guide_admin/">Math/Literacy</a>, <a href="/staff/afterschool/guide_science_manager.cgi">Science</a>, and <a href="/afterschool/guide/tech_guide_admin/">Technology</a></li>
					<li><a href="/staff/communications/afterschool_announcement_manager.cgi">Announcements/Stories Mgr.</a></li>
					<li><a href="/staff/afterschool/resourceguide_manager.cgi">Resource Guide for Managing Afterschool Programs</a></li>
					 </ul>
	</td></tr>
<tr><td valign="top">KTDRR</td>
	<td valign="top"><a href="http://www.ktdrr.org/">http://www.ktdrr.org/</a></td>
	<td valign="top"></td></tr>
<tr><td valign="top">KTER</td>
	<td valign="top"><a href="http://www.kter.org">http://www.kter.org</a></td>
	<td valign="top"></td></tr>
<tr><td valign="top">OCR-EM R&amp;E Project</td>
	<td valign="top"><a href="http://research.sedl.org/ocr-em/">http://research.sedl.org/ocr-em/</td>
	<td valign="top"><a href="http://research.sedl.org/cgi-bin/ocrem/admin.cgi">OCREM Admin Site</a></td></tr>
<tr><td valign="top">PLCA-R Questionnaire</td>
	<td valign="top"><a href="http://www.sedl.org/plc/survey">SoC Survey</a><br>
					 <a href="http://www.sedl.orgplc/survey/admin">SoC Survey Administration</a></td>
	<td valign="top"><a href="http://www.sedl.org/staff/communications/plc_customer_admin.cgi">PLCA-R Customer Setup</a></td></tr>
<tr><td valign="top">R&E</td>
	<td valign="top"><a href="http://www.sedl.org/re/">http://www.sedl.org/re/</a></td>
	<td valign="top"></td></tr>
<tr><td valign="top">SECC</td>
	<td valign="top"><a href="http://secc.sedl.org/">http://secc.sedl.org/</a></td>
	<td valign="top"><a href="http://www.sedl.org/staff/communications/experts_list_cc_manager.cgi">Experts for SECC/TXCC Home Page</a>
					 </td></tr>
<tr><td valign="top">Stages of Concern Questionnaire</td>
	<td valign="top"><a href="http://www.sedl.org/concerns">SoC Survey</a><br>
					 <a href="http://www.sedl.org/concerns/admin">SoC Survey Administration</a></td>
	<td valign="top"><a href="http://www.sedl.org/staff/communications/soc_customer_admin.cgi">SoC Customer Setup</a></td></tr>
<tr><td valign="top">Survey Site</td>
	<td valign="top"><a href="http://survey.sedl.org/">http://survey.sedl.org/</a></td>
	<td valign="top"><a href="http://survey.sedl.org/efm/">Survey Admin Site</a></td></tr>
<tr><td valign="top">TXCC</td>
	<td valign="top"><a href="http://txcc.sedl.org/">http://txcc.sedl.org/</a></td>
	<td valign="top"><a href="http://www.sedl.org/staff/communications/experts_list_cc_manager.cgi">Experts for SECC/TXCC Home Page</a><br>
					 <a href="http://www.sedl.org/staff/communications/webinar_watcher_log.cgi">Webinar Viewer Log</a></td></tr>
<tr><td valign="top">Vocational Rehabilitation Service Models for Individuals with Autism Spectrum Disorders</td>
	<td valign="top"><a href="http://autism.sedl.org/">http://autism.sedl.org/</a></td>
	<td valign="top"></td></tr>
</table>


<h3>Other SEDL Web Sites</h3>
<table BORDER="1" CELLPADDING="2" CELLSPACING="0" WIDTH="100%">
<tr><td valign="top">Past SEDL Projects (with Web site still online)</td>
	<td valign="top">
		<ul>
		<li><a href="http://www.centerforcsri.org">Center for Comprehensive School Reform and Improvement (CSRI)</a>
			(<a href="http://www.centerforcsri.org/staff/">SEDL's CSRI staff</a>)
			(<a href="http://www.centerforcsri.org/staff2/">Learning Pt. CSRI Staff</a>), and
			(<a href="http://www.centerforcsri.org/administrator/">CMS admin logon</a>)</li>
		<li><a href="http://www.sedl.org/gap/">Closing the Achievement GAP</a></li>
		<li><a href="http://www.sedl.org/loteced/">LOTECED</a></li>
		<li><a href="http://www.ncddr.org">http://www.ncddr.org</a> (NCDDR)</li>
		<li>National PIRC Network: <a href="http://www.nationalpirc.org/">http://www.nationalpirc.org/</a> <a href="http://www.nationalpirc.org/cgi-bin/pirc/staff/index.cgi">PIRC Admin Site</a></li>
		<li><a href="http://www.sedl.org/staff/datamine/nifl-contacts.cgi">NIFL Contact Database</a> (NIFL Contact DB)</li>
		<li><a href="http://www.sedl.org/nsf/">NSF</a></li>
		<li><a href="http://www.sedl.org/reading/">Reading resources</a> (<a href="http://www.sedl.org/staff/communications/reading_db_manager.cgi">RAD admin</a>)</li>
		<li><a href="http://www.researchutilization.org">http://www.researchutilization.org</a> (RUSH)</li>
		<li>Striving Readers <a href="http://strivingreaders.sedl.org/">http://strivingreaders.sedl.org/</a> and <a href="http://strivingreaders.sedl.org/cgi-bin/sr/admin.cgi">SR Admin Site</a></li>
		<li><a href="http://www.sedl.org/symposium2004/">Symposium (Harvard) 2004</a></li>
		<li><a href="http://www.sedl.org/tft/">Tools for Transitions</a> Hurricane Katrina Resources</li>
		<li><a href="http://www.sedl.org/ws/">Working Systemically</li>
		</ul>
	</td></tr>
<tr><td valign="top">School Planning and Improvement Sites</td>
	<td valign="top">
		<ul>
		<li><a href="http://acsip.state.ar.us/">Arkansas Consolidated School Improvement Planning (ACSIP)</a> is a school improment plan building tool
				used by all schools and school districts in the state of Arkansas</li>
		<li><a href="http://iplan.sedl.org/">SEDL i-Plan</a> is a school improvement planning tool used by Galena Park ISD in Texas.</li>
		<li><a href="http://www.sedl.org/eplan/">Texas e-Plan</a> is a district technology plan building tool
				used by all school districts in the state of Texas.</li>
		</ul>
	</td></tr>
</table>



</div>
EOM
}
##########################################
# END: LOCATION WEB_SITES
##########################################

##########################################
# START: LOCATION SEARCH
##########################################
if ($location eq 'search') {
	##################################################################
	## START: SPLIT SEARCH STRING INTO WORDS TO SEARCH FOR
	##################################################################
	my ($new_andor, $sf1, $sf2, $sf3, $sf4, $sf5, $sf6) = &commoncode::evaluate_word_search($searchfor, "AND");
	##################################################################
	## END: SPLIT SEARCH STRING INTO WORDS TO SEARCH FOR
	##################################################################
my $searchfor_forscreen = $searchfor;
   $searchfor_forscreen =~ s/\\//gi;
print<<EOM;
$page_header_info
<TITLE>Intranet Search Results</TITLE>
$htmlhead
<div style="padding:15px;">
<H1 style="margin-top:0px;padding-top:0px">Intranet Search Results</H1>
<p>
You searched for: <span style="color:red">$searchfor_forscreen</span>
</p>
EOM
	my $currentpage_content = "";
	my $currentpage_leftnav = "";
	my $currentpage_redirect_tourl = "";

	## QUERY DATABASE FOR THIS PAGE
	my $command = "select intranet_section.is_title, intranet_section_group.isg_id, intranet_section_group.isg_title, intranet_pages.page_title, intranet_pages.page_id, intranet_pages.page_id_text, intranet_pages.edit_committed, intranet_pages.edit_author, intranet_pages.page_redirect_tourl
					FROM intranet_section, intranet_section_group, intranet_pages where
					intranet_section_group.isg_is_id NOT LIKE '9'
					AND intranet_section_group.isg_id = intranet_pages.page_isg_id
					AND intranet_section.is_id = intranet_section_group.isg_is_id
					AND ";

		$command .= " (" if $sf1;
		$command .= " ((intranet_section_group.isg_title LIKE '%$sf1%') OR (intranet_pages.page_title LIKE '%$sf1%') OR (intranet_pages.page_content LIKE '%$sf1%'))" if $sf1;
		$command .= " AND ((intranet_section_group.isg_title LIKE '%$sf2%') OR (intranet_pages.page_title LIKE '%$sf2%') OR (intranet_pages.page_content LIKE '%$sf2%'))" if $sf2;
		$command .= " AND ((intranet_section_group.isg_title LIKE '%$sf3%') OR (intranet_pages.page_title LIKE '%$sf3%') OR (intranet_pages.page_content LIKE '%$sf3%'))" if $sf3;
		$command .= " AND ((intranet_section_group.isg_title LIKE '%$sf4%') OR (intranet_pages.page_title LIKE '%$sf4%') OR (intranet_pages.page_content LIKE '%$sf4%'))" if $sf4;
		$command .= " AND ((intranet_section_group.isg_title LIKE '%$sf5%') OR (intranet_pages.page_title LIKE '%$sf5%') OR (intranet_pages.page_content LIKE '%$sf5%'))" if $sf5;
		$command .= " AND ((intranet_section_group.isg_title LIKE '%$sf6%') OR (intranet_pages.page_title LIKE '%$sf6%') OR (intranet_pages.page_content LIKE '%$sf6%'))" if $sf6;
		$command .= ")" if $sf1;
#print "$command";
		$command .= " order by intranet_section.is_title, intranet_pages.page_title";

	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	my $s = "s";
	   $s = "" if ($num_matches eq '1');
	print "<H2>intranet pages</H2><P>Found $num_matches intranet page$s matching your query. Use the search box at the top of the page if you would like to search again.<P>	<OL>";
	## GET THE RESULTS OF THE QUERY
	while (my @arr = $sth->fetchrow) {
	  my ($is_title, $isg_id, $isg_title, $page_title, $page_id, $page_id_text, $edit_committed, $edit_author, $page_redirect_tourl) = @arr;
		$edit_committed = &commoncode::convert_timestamp_2pretty_w_date($edit_committed, "no");
		my $searchfor_label = $searchfor;
		   $searchfor_label =~ s/\(//gi;
		   $searchfor_label =~ s/\)//gi;
		   $searchfor_label = lc($searchfor_label);
		my $page_title_label = lc($page_title);
		my $stars = "";
			if ($page_title_label =~ $searchfor_label) {
				$stars = "<img src=\"/htdig/star-old.gif\" title=\"Search term found in the page title\" alt=\"Search term found in the page title\"><img src=\"/htdig/star-old.gif\" title=\"Search term found in the page title\" alt=\"Search term found in the page title\"><img src=\"/htdig/star-old.gif\" title=\"Search term found in the page title\" alt=\"Search term found in the page title\">";
			}
		   my $searchfor_hilight = ucfirst($searchfor);
		   	  $searchfor_hilight =~ s/\\//gi;
		   	  $searchfor_hilight =~ s/\(//gi;
		      $searchfor_hilight =~ s/\)//gi;

		   $page_title =~ s/$searchfor_hilight/\<span style=\"color:red\">$searchfor_hilight\<\/span\>/gi;
		   my $page_id_for_link = "pid=$page_id";
		      $page_id_for_link = "page=$page_id_text" if ($page_id_text ne '');
		my $link_for_page = "/cgi/index.cgi?$page_id_for_link";
		   $link_for_page = $page_redirect_tourl if ($page_redirect_tourl ne '');
		print "<LI>$is_title &gt;&gt; $isg_title &gt;&gt; <a href=\"$link_for_page\">$page_title</a> $stars <span style=\"color:#999999\">(updated: $edit_committed)</span></LI>";
	}
	print "</OL>";


	## START: SEARCH STAFF PROFILES DB
	my $command = "select firstname, lastname, userid, jobtitle
					FROM staff_profiles WHERE fm_record_id LIKE '%' ";

		$command .= " AND (" if $sf1;
		$command .= " ((firstname LIKE '%$sf1%') OR (lastname LIKE '%$sf1%') OR (jobtitle LIKE '%$sf1%') OR (email LIKE '%$sf1%') OR (department_abbrev LIKE '%$sf1%') OR (responsibilities LIKE '%$sf1%') OR (experience LIKE '%$sf1%'))" if $sf1;
		$command .= " AND ((firstname LIKE '%$sf2%') OR (lastname LIKE '%$sf2%') OR (jobtitle LIKE '%$sf2%') OR (email LIKE '%$sf2%') OR (department_abbrev LIKE '%$sf2%') OR (responsibilities LIKE '%$sf2%') OR (experience LIKE '%$sf2%'))" if $sf2;
		$command .= " AND ((firstname LIKE '%$sf3%') OR (lastname LIKE '%$sf3%') OR (jobtitle LIKE '%$sf3%') OR (email LIKE '%$sf3%') OR (department_abbrev LIKE '%$sf3%') OR (responsibilities LIKE '%$sf3%') OR (experience LIKE '%$sf3%'))" if $sf3;
		$command .= " AND ((firstname LIKE '%$sf4%') OR (lastname LIKE '%$sf4%') OR (jobtitle LIKE '%$sf4%') OR (email LIKE '%$sf4%') OR (department_abbrev LIKE '%$sf4%') OR (responsibilities LIKE '%$sf4%') OR (experience LIKE '%$sf4%'))" if $sf4;
		$command .= " AND ((firstname LIKE '%$sf5%') OR (lastname LIKE '%$sf5%') OR (jobtitle LIKE '%$sf5%') OR (email LIKE '%$sf5%') OR (department_abbrev LIKE '%$sf5%') OR (responsibilities LIKE '%$sf5%') OR (experience LIKE '%$sf5%'))" if $sf5;
		$command .= " AND ((firstname LIKE '%$sf6%') OR (lastname LIKE '%$sf6%') OR (jobtitle LIKE '%$sf6%') OR (email LIKE '%$sf6%') OR (department_abbrev LIKE '%$sf6%') OR (responsibilities LIKE '%$sf6%') OR (experience LIKE '%$sf6%'))" if $sf6;
		$command .= ")" if $sf1;

		$command .= " order by lastname, firstname";

#print "<p class=\"info\">$command</p>";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_staff = $sth->rows;
	my $s = "s";

	if ($num_matches_staff > 0) {
	   $s = "" if ($num_matches_staff eq '1');
		print "<H2>Staff Members:</H2><P>Found $num_matches_staff staff profile$s matching your query.<P><OL>";
		while (my @arr = $sth->fetchrow) {
		  my ($firstname, $lastname, $userid, $jobtitle) = @arr;
		  $firstname = &commoncode::cleanaccents2html($firstname);
		  $lastname = &commoncode::cleanaccents2html($lastname);
			if ($jobtitle ne '') {
				print "<LI>Staff Profile: <a href=\"/pubs/catalog/authors/$userid.html\">$firstname $lastname</a> ($jobtitle)</LI>";
			} else {
				print "<LI>Board Member: <a href=\"/pubs/catalog/authors/$userid.html\">$firstname $lastname</a></LI>";
			}
		}
		print "</OL>";
	} # END IF
	## END: SEARCH STAFF PROFILES DB

	## START: SEARCH PUBLICATIONS DB
	my %items_sold;
	########################################################################
	# START: PARSE SALES DATABASE TO COUNT ITEMS SOLD BY PRODUCT AND MONTH
	########################################################################
	my $command = "select item_id, SUM(item_qty) from shoppingcart_datamine GROUP BY item_id";
#	print "<P>COMMAND: $command <P>";
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
		    my ($item_id, $item_qty) = @arr;
			$items_sold{$item_id} += $item_qty;
		} # END DB QUERY LOOP
	########################################################################
	# END: PARSE SALES DATABASE TO COUNT ITEMS SOLD BY PRODUCT AND MONTH
	########################################################################

	my %items_hit;
	#######################################################
	# START: PARSE HITS DATABASE TO COUNT HITS BY PRODUCT
	#######################################################
	my $command_count_hits = "select product_online_id, SUM(hit_count) from webhits_byproduct GROUP BY product_online_id";
#	print "<P>COMMAND: $command_count_hits <P>";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_count_hits) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
		    my ($product_online_id, $hit_qty) = @arr;
			$items_hit{$product_online_id} += $hit_qty;
		} # END DB QUERY LOOP
	#######################################################
	# END: PARSE HITS DATABASE TO COUNT HITS BY PRODUCT
	#######################################################





	my $command = "select salesid, onlineid, title, title2, datepub, lochtml, locpdf, locexec, locsmallart, locdatabase, isactive, unique_id from sedlcatalog
					WHERE isactive LIKE '%' ";
		$command .= " AND (" if $sf1;
		$command .= " ((title LIKE '%$sf1%') OR (salesid LIKE '%$sf1%') OR (title2 LIKE '%$sf1%') OR (author1 LIKE '%$sf1%') OR (author2 LIKE '%$sf1%') OR (author3 LIKE '%$sf1%') OR (author4 LIKE '%$sf1%') OR (author5 LIKE '%$sf1%') OR (author6 LIKE '%$sf1%') OR (author7 LIKE '%$sf1%') OR (author8 LIKE '%$sf1%'))" if $sf1;
		$command .= " AND ((title LIKE '%$sf2%') OR (salesid LIKE '%$sf2%') OR (title2 LIKE '%$sf2%') OR (author1 LIKE '%$sf2%') OR (author2 LIKE '%$sf2%') OR (author3 LIKE '%$sf2%') OR (author4 LIKE '%$sf2%') OR (author5 LIKE '%$sf2%') OR (author6 LIKE '%$sf2%') OR (author7 LIKE '%$sf2%') OR (author8 LIKE '%$sf2%'))" if $sf2;
		$command .= " AND ((title LIKE '%$sf3%') OR (salesid LIKE '%$sf3%') OR (title2 LIKE '%$sf3%') OR (author1 LIKE '%$sf3%') OR (author2 LIKE '%$sf3%') OR (author3 LIKE '%$sf3%') OR (author4 LIKE '%$sf3%') OR (author5 LIKE '%$sf3%') OR (author6 LIKE '%$sf3%') OR (author7 LIKE '%$sf3%') OR (author8 LIKE '%$sf3%'))" if $sf3;
		$command .= " AND ((title LIKE '%$sf4%') OR (salesid LIKE '%$sf4%') OR (title2 LIKE '%$sf4%') OR (author1 LIKE '%$sf4%') OR (author2 LIKE '%$sf4%') OR (author3 LIKE '%$sf4%') OR (author4 LIKE '%$sf4%') OR (author5 LIKE '%$sf4%') OR (author6 LIKE '%$sf4%') OR (author7 LIKE '%$sf4%') OR (author8 LIKE '%$sf4%'))" if $sf4;
		$command .= " AND ((title LIKE '%$sf5%') OR (salesid LIKE '%$sf5%') OR (title2 LIKE '%$sf5%') OR (author1 LIKE '%$sf5%') OR (author2 LIKE '%$sf5%') OR (author3 LIKE '%$sf5%') OR (author4 LIKE '%$sf5%') OR (author5 LIKE '%$sf5%') OR (author6 LIKE '%$sf5%') OR (author7 LIKE '%$sf5%') OR (author8 LIKE '%$sf5%'))" if $sf5;
		$command .= " AND ((title LIKE '%$sf6%') OR (salesid LIKE '%$sf6%') OR (title2 LIKE '%$sf6%') OR (author1 LIKE '%$sf6%') OR (author2 LIKE '%$sf6%') OR (author3 LIKE '%$sf6%') OR (author4 LIKE '%$sf6%') OR (author5 LIKE '%$sf6%') OR (author6 LIKE '%$sf6%') OR (author7 LIKE '%$sf6%') OR (author8 LIKE '%$sf6%'))" if $sf6;
		$command .= ")" if $sf1;

		$command .= " order by title";



# OR
#			(description like '%$searchfor%')
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_products = $sth->rows;
	my $s = "s";

	if ($num_matches_products > 0) {
	   $s = "" if ($num_matches_products eq '1');
		print "<h2>Publications:</h2>\n<p>\nFound $num_matches_products SEDL product$s matching your query.\n</p>\n<OL>\n";
		while (my @arr = $sth->fetchrow) {
		  my ($salesid, $onlineid, $title, $title2, $datepub, $lochtml, $locpdf, $locexec, $locsmallart, $locdatabase, $isactive, $unique_id) = @arr;

		  $title .= ": $title2" if ($title2 ne '');
		  $title = &commoncode::cleanaccents2html($title);
		   my $searchfor_hilight = ucfirst($searchfor_forscreen);
		   	  $searchfor_hilight =~ s/\(//gi;
		      $searchfor_hilight =~ s/\)//gi;
		   $title =~ s/$searchfor_hilight/\<span style\=\"color\:red\"\>$searchfor_hilight\<\/span\>/gi;
			if ($isactive =~ 'y') {
				print "<li><a href=\"/pubs/catalog/items/$onlineid\">$title</a>";
			} else {
				print "<li style=\"background-color:#dddddd;\">(INACTIVE PRODUCT) <a href=\"/cgi/communications/product_catalog_manager.cgi?location=add_item&show_record=$unique_id&show_active=n\">$title</a>";
			}
			print " ($datepub) " if ($datepub ne '');
			my $salesid_label = $salesid;
			   $salesid_label =~ s/&/%26/gi;
			my $qty_sold = $items_sold{$salesid};
			my $sales_hits_label = "view sales & Web hits data";
			   $sales_hits_label = "view sales data" if (($lochtml eq '') && ($locpdf eq '') && ($locdatabase eq ''));
			if ($items_sold{$salesid} > 0) {
				print "<br><a href=\"/cgi/communications/sales-report-byitem.cgi?show_item=$salesid_label&amp;location=itemsummary&amp;show_month=12&amp;show_year=2006\"><IMG SRC=\"/images/bullets/graph.gif\" class=\"noBorder\" alt=\"$sales_hits_label\"></a> (Click here to <a href=\"/staff/communications/sales-report-byitem.cgi?show_item=$salesid_label&amp;location=itemsummary&amp;show_month=12&amp;show_year=2006\" title = \"$qty_sold copies sold online since 10/2003\">$sales_hits_label</a>)";
			} elsif ($items_hit{$onlineid} > 0) {
				print "<br><a href=\"/cgi/communications/sales-report-byitem.cgi?show_item=n/a&amp;show_item_onlineid=$onlineid&amp;location=itemsummary&amp;show_month=12&amp;show_year=2006\"><IMG SRC=\"/images/bullets/graph.gif\" class=\"noBorder\" alt=\"view Web hits data\"></a> (Click here to <a href=\"/staff/communications/sales-report-byitem.cgi?show_item=n/a&amp;show_item_onlineid=$onlineid&amp;location=itemsummary&amp;show_month=12&amp;show_year=2006\">view Web hits data</a>)";
			}
			print "</li>\n";
		}
		print "</OL>\n";
	} # END IF
	## END: SEARCH PUBLICATIONS DB

	print "</div>\n";
}
##########################################
# END: LOCATION SEARCH
##########################################


##########################################
# START: LOCATION SHOW_PAGE
##########################################
if ($location eq 'show_page') {
	#######################################################
	## START: DECLARE PLACEHOLDERS FOR NEWS ITEMS PLACED ON HOME PAGE
	#######################################################
	my $include_birthdays = "";
	my $viewahead_mouseover = "";
	my $include_newstaff = "";
	my $include_new_in_irc = "";
	#######################################################
	## END: DECLARE PLACEHOLDERS FOR NEWS ITEMS PLACED ON HOME PAGE
	#######################################################



	## DECLARE HOLDER VARIABLES FOR PAGES
	my $page_id = ""; my $page_id_text = ""; my $is_intro= ""; my $is_editable_by = ""; my $page_isg_id = ""; my $page_group_seq = ""; my $page_title = ""; my $page_title_leftnav = ""; my $page_display_in_leftnav = ""; my $page_content = ""; my $page_redirect_tourl = ""; my $page_redirect_delay = ""; my $page_added_date = ""; my $page_added_by = ""; my $edit_committed = ""; my $edit_author = ""; my $page_fullwidth = ""; my $list_page = "";
	my $is_id = ""; my $is_id_text = ""; my $is_title = ""; my $isg_id = ""; my $isg_id_text = ""; my $isg_title = ""; my $isg_description = "";

	my $currentpage_title = "";
	my $currentpage_content = "";
	my $currentpage_leftnav = "";
	my $currentpage_redirect_tourl = "";
	my $currentpage_section_group_title = "";
	my $edit_link = "";

	###########################################
	## START: SET VALUE FOR HOME PAGE CONTENT
	###########################################


	if (($show_s  eq '') && ($section  eq '') && ($show_sg  eq '') && ($group  eq '') && ($pid  eq '') && ($page  eq '')) {
		#######################################################

		open(SOURCEFILE,"</cgi/includes/flash/newbooks.txt");
		while (<SOURCEFILE>) {
			if ($_ !~ 'newbooks=') {
				$include_new_in_irc .= $_;
			}
		}
		close(SOURCEFILE);
		$include_new_in_irc =~ s/\n\n/\n/gi;
		$include_new_in_irc =~ s/\n/\<\/li\>\<li\>/gi;
		$include_new_in_irc = "<ul><li>$include_new_in_irc</li></ul>";

		open(SOURCEFILE,"</home/httpd/html/staff/includes/viewahead_mouseover.txt");
		while (<SOURCEFILE>) {
			$viewahead_mouseover .= $_;
		}
		close(SOURCEFILE);

		open(SOURCEFILE,"</home/httpd/html/staff/includes/flash/newstaff.txt");
		while (<SOURCEFILE>) {
			$include_newstaff .= $_;
		}
#		$include_newstaff =~ s/newstaff=//g;
#		$include_newstaff =~ s/\n/\<br>/g;
#		$include_newstaff =~ s/\%26/\&amp;/g;
		close(SOURCEFILE);
		#######################################################
		## END: GRAB DATA FOR NEWS ITEMS PLACED ON HOME PAGE
		#######################################################
		$page_title = "SEDL Intranet Home Page";

# $trash_not_to_keep
## SAMPLE INTERNAL NOTICE OF VACANCY
#<DIV align=center>
#<strong>Internal Notice of Vacancy</strong><br>
#<a href=\"http://www.sedl.org/staff/personnel/jobs-internal/ISP-Proj Dir.pdf\">Project Director</a> in SEDL's IMPROVING SCHOOL PERFORMANCE (ISP) unit<br>
#Opens 7/2/2007. Closes 7/11/2007 at 5pm.<br>
#</DIV>

## SAMPLE INTERNAL NOTICE OF VACANCY
#			$currentpage_content .= "
#<DIV style=\"text-align:center;border:2px solid red;margin:0 0 10px 0;padding:4px;\">
#<strong style=\"font-size:14px;\">Internal Notice of Vacancy</strong><br>
#<a href=\"http://www.sedl.org/staff/personnel/jobs-internal/DRP-Admin-Asst-NOV.pdf\">ADMINISTRATIVE ASSISTANT</a> in SEDL's DISABILITY RESEARCH TO PRACTICE (DRP) program<br>
#Opens February 4, 2011. Closes February 10, 2011<br>
#</DIV>
#";

## SAMPLE INTERNAL NOTICE OF VACANCY
#			$currentpage_content .= "
#<DIV style=\"text-align:center;border:2px solid red;margin:0 0 10px 0;padding:4px;\">
#<strong style=\"font-size:14px;\">Internal Notice of Vacancy</strong><br>
#<a href=\"http://www.sedl.org/staff/personnel/jobs-internal/Comm-Admin-Asst-Internal-NOV.pdf\">ADMINISTRATIVE ASSISTANT</a> in SEDL's COMMUNICATIONS unit<br>
#Opens May 30, 2012. Closes June 5, 2012<br>
#</DIV>
#";
# SAMPLE INTERNAL NOTICE OF VACANCY
#if ($date_full_mysql gt '2013-08-25') {
#			$currentpage_content .= "
#<DIV style=\"text-align:center;border:2px solid red;margin:0 0 10px 0;padding:4px;\">
#<strong style=\"font-size:14px;\">Internal Notice of Vacancy</strong><br>
#<a href=\"http://www.sedl.org/staff/personnel/jobs-internal/NOV-Info-Tech-Manager.pdf\">INFORMATION TECHNOLOGY MANAGER</a> in SEDL's ADMINISTRATIVE SERVICES (AS) unit<br>
#Opens August 26, 2013. Closes August 30, 2013<br>
#</DIV>
#";
#}

		if ($newpass eq '1') {
			$currentpage_content .= "<p class=\"info\">You are now logged in to the intranet.<br>Click here if you would like to <a href=\"/cgi/change_password.cgi\">Change your password</a>.</p>";
		}

		## ONLY SHOW THIS ANNOUNCEMENT THROUGH 2/5/2009
		if ($date_full_mysql lt '2009-02-06') {
		$currentpage_content .= "
<DIV class=\"info\">
<strong>Internal Notice of Vacancy:<br>
<a href=\"http://www.sedl.org/staff/personnel/jobs-internal/COMM-Comm-Assoc-internal.pdf\">Communications Associate in SEDL's Communications department</a></strong><br>
To apply for the position, please submit a SEDL application form and writing sample to
Human Resources prior to 5:00 p.m., February 5, 2009.  Contact Tracy or Sue for an access code,
which will allow online access to fill out the application form.  Please email your writing
sample to careers\@sedl.org or bring it to the HR office.
</DIV>
<br>
";
		}


		$currentpage_content .= "

		<script type=\"text/javascript\" src=\"/common/javascript/ufo.js\">
		</script>

		<table class=\"noBorder\" cellspacing=\"0\" cellpadding=\"3\">
		<tr><td VALIGN=\"TOP\" colspan=\"2\">
<MAP ID=\"edit_calendar\" NAME=\"edit_calendar\">
<AREA SHAPE=\"rect\" ALT=\"Staff\" COORDS=\"410,0,515,22\" HREF=\"/cgi/personnel/calendar-admin.cgi\">
</MAP>
<script type=\"text/javascript\" src=\"/common/javascript/wz_tooltip.js\"></script>

<img src=\"/cgi/images/view_of_days_ahead_2012.jpg\" alt=\"View of the days ahead / edit calendar\" class=\"noBorder\" height=\"22\" width=\"519\"  usemap=\"#edit_calendar\"><br>
<div style=\"width:530px; height:305px; overflow-x:hidden;overflow-y:auto; background-color:#EAE0BF;border:0px;padding:0px;margin-top:-4px;\">
$viewahead_mouseover
</div>
			</td>
			<td VALIGN=\"TOP\" rowspan=\"2\" width=\"198\">";
#				<div id=\"flashbanner2\">
#				     <a href=\"http://www.adobe.com\"><img Src=\"/staff/images/template/flash2.gif\" ALT=\"You need the flash player to view this information.\"></a>
#				</div>
#				<script type=\"text/javascript\">
#				var FO = {
#				  movie:\"/pubs/catalog/flash/slideshow.swf\",
#				  width:\"192\",height:\"247\",majorversion:\"7\",build:\"0\"
#				};
#				UFO.create(FO, \"flashbanner2\");
#				</script>

$currentpage_content .= "
		<h3 style=\"margin-bottom:0;margin-top:0;\">SEDL Suggestion Box</h3>
		<p style=\"margin-bottom:12px;margin-top:4px;\">
		Click here to <a href=\"http://www.sedl.org/staff/communications/suggestion_box.cgi\">submit a suggestion</a>.
		</p>

		<h3 style=\"margin-bottom:0;margin-top:0;\">Support SEDL When You Shop at Amazon.com</h3>
		<p style=\"margin-bottom:12px;margin-top:4px;\">
		For every Amazon.com purchase you <strong>add to your cart after</strong> following
		<a href=\"http://www.amazon.com/?&tag=s06c34-20&linkCode=wsw&\">this link</a>,
		SEDL receives a percentage of the price as a referral fee.
		</p>
		<SCRIPT charset=\"utf-8\" type=\"text/javascript\" src=\"http://ws.amazon.com/widgets/q?rt=tf_sw&amp;ServiceVersion=20070822&amp;MarketPlace=US&amp;ID=V20070822/US/s06c34-20/8002/c950ecca-1d9d-42e3-87c5-06e1e7803b17\"> </SCRIPT> <NOSCRIPT><div><A HREF=\"http://ws.amazon.com/widgets/q?rt=tf_sw&amp;ServiceVersion=20070822&amp;MarketPlace=US&amp;ID=V20070822%2FUS%2Fs06c34-20%2F8002%2Fc950ecca-1d9d-42e3-87c5-06e1e7803b17&amp;Operation=NoScript\">Amazon.com Widgets</A></div></NOSCRIPT>
";

$currentpage_content .= "
			</td></tr>


		<tr><td VALIGN=\"TOP\" rowspan=\"2\" width=\"307\">
<MAP ID=\"view_irc_list\" NAME=\"view_irc_list\">
<AREA SHAPE=\"rect\" ALT=\"Staff\" COORDS=\"369,6,471,30\" HREF=\"/staff/information/newbooks.cgi\">
</MAP>
<script type=\"text/javascript\" src=\"/common/javascript/wz_tooltip.js\"></script>

<img src=\"/cgi/images/new_in_irc_2012.jpg\" alt=\"New in the IRC\" border=\"0\" height=\"41\" width=\"478\"  usemap=\"#view_irc_list\"><br>
<div style=\"width:478px; height:175px; overflow-x:hidden;overflow-y:auto; background-color:#d5e4c5;border:0px;padding:0px;margin-top:-4px;\">
$include_new_in_irc
</div>

			</td>

			<td>				<img src=\"/images/spacer.gif\" height=\"1\" width=\"1\" alt=\"\">
</td></tr>

		<tr><td VALIGN=\"TOP\" colspan=\"2\">
			<img src=\"/cgi/images/new_staff.jpg\" alt=\"New Staff and Position Vacancies\" border=\"0\" height=\"55\" width=\"257\"><br>
			<div style=\"width:238px; height:138px; overflow-x:hidden;overflow-y:auto; background-color:#eadfbf;border:0px;padding:0 0 0 5px;margin-left:7px;margin-top:-3px;\">
			$include_newstaff
			</div>

			</td></tr>
			<tr><td><IMG SRC=\"spacer.gif\" WIDTH=\"301\" HEIGHT=\"1\" alt=\"\"></td>
				<td><IMG SRC=\"spacer.gif\" WIDTH=\"60\" HEIGHT=\"1\" alt=\"\"></td>
				<td><IMG SRC=\"spacer.gif\" WIDTH=\"192\" HEIGHT=\"1\" alt=\"\"></td>
			</tr>
		</table>

		";

#<img src=\"/staff/images/elvis.gif\">
		##my $strong_pwd = crypt('password','password');
		##print $strong_pwd;
	###########################################
	## END: SET VALUE FOR HOME PAGE CONTENT
	###########################################


		###############################################
		## START: DISPLAY LIST OF RECENTLY ADDED PAGES
		###############################################
		$currentpage_content .= "<table width=\"100%\">\n<tr><td width=\"50%\" valign=\"top\"><h2 style=\"margin-top:0px;\">Recently Added Pages</h2>\n<ul>";

		my $command = "select *	FROM intranet_pages where page_added_date NOT LIKE '' order by page_added_date DESC  LIMIT 0,8";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#$currentpage_content .= "<P>MATCHES: $num_matches";
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
		 my ($page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav, $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author, $page_fullwidth, $list_page) = @arr;
			if ($page_id_text ne '') {
				$page_id_text = "page=$page_id_text";
			} else {
				$page_id_text = "pid=$page_id";
			}
			$edit_committed = &commoncode::convert_timestamp_2pretty_w_date($edit_committed, 'yes');
			if (($page_redirect_tourl ne '') && ($page_redirect_delay == 0)) {
				$currentpage_content .= "<li><a href=\"$page_redirect_tourl\">$page_title</a></li>\n";
			} elsif ($list_page eq 'no') {
			    $currentpage_content .= "<a style=\"display:none;\" href=\"$page_redirect_tourl\">$page_title</a>\n";
			}
			else {
				$currentpage_content .= "<li><a href=\"/cgi/index.cgi?$page_id_text\" title=\"Added by $edit_author on $edit_committed\">$page_title</a></li>\n";
			}
		} # END DB QUERY LOOP

		$currentpage_content .= "</ul>\n</td>\n<td width=\"50%\" valign=\"top\">\n";
		###############################################
		## END: DISPLAY LIST OF RECENTLY ADDED PAGES
		###############################################

		###############################################
		## START: DISPLAY LIST OF RECENTLY EDITED GROUPS
		###############################################
		$currentpage_content .= "<h2 style=\"margin-top:0px;\">Recently Edited Pages</h2>\n<ul>\n";
		my $command = "select * FROM intranet_section_group WHERE isg_edit_committed LIKE '$timestamp_short%' order by isg_edit_committed DESC  LIMIT 0,8";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#		print "<p>$command</p>";
#$currentpage_content .= "<P>MATCHES: $num_matches";
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
		 my ($isg_id, $isg_id_text, $isg_is_id, $isg_title, $isg_seq_num, $isg_description, $isg_edit_committed, $isg_edit_author) = @arr;
			if ($isg_id_text ne '') {
				$isg_id_text = "group=$isg_id_text";
			} else {
				$isg_id_text = "show_sg=$isg_id";
			}
			$isg_edit_committed = &commoncode::convert_timestamp_2pretty_w_date($isg_edit_committed, 'yes');
			my $extra_page_label;
			if (length($isg_title) == 1) {
				$extra_page_label = " <em>(SEDL Style Guide)</em>";
			}
			$currentpage_content .= "<li><a href=\"/cgi/index.cgi?$isg_id_text\" title=\"Updated by $isg_edit_author on $isg_edit_committed\">$isg_title</a>$extra_page_label</li>\n";
		} # END DB QUERY LOOP


		my $command = "select *	FROM intranet_pages order by edit_committed DESC  LIMIT 0,8";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#$currentpage_content .= "<P>MATCHES: $num_matches";
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
		 my ($page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav, $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author, $page_fullwidth, $list_page) = @arr;
			if ($page_id_text ne '') {
				$page_id_text = "page=$page_id_text";
			} else {
				$page_id_text = "pid=$page_id";
			}
			$edit_committed = &commoncode::convert_timestamp_2pretty_w_date($edit_committed, 'yes');
			my $extra_page_label;
			if (length($page_title) == 1) {
				$extra_page_label = " <em>(SEDL Style Guide)</em>";
			}
			if (($page_redirect_tourl ne '') && ($page_redirect_delay == 0)) {
				$currentpage_content .= "<li><a href=\"$page_redirect_tourl\">$page_title</a>$extra_page_label</li>\n";
			} elsif ($list_page eq 'no') {
				$currentpage_content .= "<a style=\"display:none\" href=\"$page_redirect_tourl\">$page_title</a>$extra_page_label\n";
			}
			else {
				$currentpage_content .= "<li><a href=\"/cgi/index.cgi?$page_id_text\" title=\"Updated by $edit_author on $edit_committed\">$page_title</a>$extra_page_label</li>\n";
			}
		} # END DB QUERY LOOP

		$currentpage_content .= "</ul>\n</td>\n</tr>\n</table>\n";
		###############################################
		## END: DISPLAY LIST OF RECENTLY EDITED PAGES
		###############################################

	} else {
		#####################################################################################
		## START: IF NOT DISPLAYING THE HOME PAGE, QUERY DB FOR THIS SECTION, GROUP, OR PAGE
		#####################################################################################
		my $command = "select intranet_section.*, intranet_section_group.isg_id, intranet_section_group.isg_id_text, intranet_section_group.isg_title, intranet_section_group.isg_description, intranet_pages.*
						FROM intranet_section, intranet_section_group, intranet_pages
						WHERE intranet_section.is_id = intranet_section_group.isg_is_id
						AND intranet_section_group.isg_id = intranet_pages.page_isg_id";
		   $command .= " AND intranet_section.is_id like '$show_s'" if ($show_s ne '');
		   $command .= " AND intranet_section.is_id_text like '$section'" if ($section ne '');
		   $command .= " AND intranet_section_group.isg_id LIKE '$show_sg'" if ($show_sg ne '');
		   $command .= " AND intranet_section_group.isg_id_text LIKE '$group'" if ($group ne '');
		   $command .= " AND intranet_pages.page_id_text like '$page'" if ($page ne '');
		   $command .= " AND intranet_pages.page_id like '$pid'" if ($pid ne '');
		   $command .= " LIMIT 1" if ($pid eq '');
#		print "<P>PIDTEXT= $page AND COMMAND: $command";

		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#		print "<P>MATCHES: $num_matches";

		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
		    ($is_id, $is_id_text, $is_title, $is_intro, $is_editable_by, $isg_id, $isg_id_text, $isg_title, $isg_description, $page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav, $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author, $page_fullwidth, $list_page) = @arr;

			# SET PID IF page WAS PASSED
			if ($section ne '') {
				$show_s = $is_id;
			}
			if ($group ne '') {
				$show_sg = $isg_id;
			}
			if ($page ne '') {
				$pid = $page_id;
			}

			# SET PAGE CONTENT TO BLANK IF NO ID WAS PASSED
			if (($pid eq '') && ($page eq '')) {
				$page_content = "";
			}

			#####################################################
			## START: SHOW LINK TO EDIT PAGE, IF USER IS ADMIN
			#####################################################
			if ($pid ne '') {
				if ($is_editable_by =~ $cookie_ss_staff_id) {
					$edit_link .= "<a href=\"$location_admin_script?location=admin_edit_page&amp;show_sg=$isg_id&amp;pid=$page_id&amp;camefrom=page\"><IMG SRC=\"/cgi/images/template/admin-edit-page.gif\" ALT=\"Edit this page\" class=\"noBorder intranet_print_page\" ALIGN=\"RIGHT\" width=\"90\" height=\"20\"></a>";
				}
			} elsif (($pid eq '') && ($show_sg ne '')) {
				$show_s = $is_id; # SET SECTION ID
				if ($is_editable_by =~ $cookie_ss_staff_id) {
					$edit_link .= "<a href=\"$location_admin_script?location=admin_edit_group&amp;show_sg=$isg_id&amp;camefrom=page\"><IMG SRC=\"/cgi/images/template/admin-edit-group.gif\" ALT=\"Edit this group\" class=\"noBorder intranet_print_page\" ALIGN=\"RIGHT\" width=\"90\" height=\"20\"></a>";
				}
			} elsif (($pid eq '') && ($show_sg eq '')) {
				if ($is_editable_by =~ $cookie_ss_staff_id) {
					$edit_link .= "<a href=\"$location_admin_script?location=admin_edit_section&amp;show_s=$is_id&amp;camefrom=page\"><IMG SRC=\"/cgi/images/template/admin-edit-section.gif\" ALT=\"Edit this section\" class=\"noBorder intranet_print_page\" ALIGN=\"RIGHT\" width=\"90\" height=\"20\"></a>";
				}
			}
			#####################################################
			## END: SHOW LINK TO EDIT PAGE, IF USER IS ADMIN
			#####################################################

			$redirect_delay = $page_redirect_delay;
			if ($pid ne '') {
				$show_s = $is_id;
				$show_sg = $isg_id;
			}
			$currentpage_redirect_tourl = $page_redirect_tourl if ($pid eq $page_id);
			$currentpage_section_group_title = $isg_title if ($isg_title ne '');

			##############################################################
			## START: IF SHOWING THIS PAGE, SET VARIABLE FOR PAGE CONTENT
			##############################################################
			if (($is_id eq $show_s) && ($isg_id eq $show_sg) && ($page_id eq $pid)) {
				$currentpage_content = $page_content;
				$currentpage_title = $page_title;

				##################################
				## START: HANDLE PAGE REDIRECTION
				##################################
				if ($page_redirect_tourl ne '') {
					my $page_redirect_tourl_label = $page_redirect_tourl;
					if (length($page_redirect_tourl) > 80) {
						$page_redirect_tourl_label = substr($page_redirect_tourl, 0, 50);
						$page_redirect_tourl_label .= " ";
						$page_redirect_tourl_label .= substr($page_redirect_tourl, 50, length($page_redirect_tourl));
					}
					$currentpage_content .= "<p>This resource is located at <a href=\"$page_redirect_tourl\">$page_redirect_tourl_label</a>.</p><p><span style=\"color:red\">Click to continue, or you will be taken there automatically in $redirect_delay seconds.</span></p>";
				}
				##################################
				## END: HANDLE PAGE REDIRECTION
				##################################

			} # END IF
			##############################################################
			## END: IF SHOWING THIS PAGE, SET VARIABLE FOR PAGE CONTENT
			##############################################################

		## START: SET DEFAULT FOR PAGE OVERVIEW
		if (($show_s ne '') && ($show_sg eq '') && ($pid eq '')) {
				$currentpage_title = "$is_title" if ($page_content eq ''); # (Section Overview)
#				print "page content = \'$page_content\' AND NUM MATCHES: $num_matches";
			if (($page_content eq '') && ($currentpage_redirect_tourl eq '')) {
				$currentpage_content .= "$is_intro" if ($is_intro ne '');
#				$currentpage_content .= "<br><br>" if ($is_intro !~ 'ircnews');
				$currentpage_content .= " This is the top page of this section.  Please choose from the options in the side menu or from the list below.<br> <br>";
				###########################################################
				# START: QUERY DATABASE TO BUILD TREE FOR THIS SECTION
				###########################################################
				my $command = "select intranet_section.is_id, intranet_section.is_id_text, intranet_section.is_title, intranet_section.is_editable_by,
				intranet_section_group.isg_id, intranet_section_group.isg_id_text, intranet_section_group.isg_title, intranet_section_group.isg_seq_num, intranet_pages.*
						FROM intranet_section, intranet_section_group, intranet_pages
						WHERE intranet_section.is_id = intranet_section_group.isg_is_id
						AND intranet_section_group.isg_id = intranet_pages.page_isg_id";
				$command .= " AND intranet_section.is_id like '$show_s'";
				$command .= " order by intranet_section_group.isg_seq_num, intranet_pages.page_group_seq";
#		print "<P>COMMAND: $command";

				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
				my $num_matches = $sth->rows;
#		print "<P>MATCHES: $num_matches";
				my $last_group_id = "";
				my $loop_counter = 1;
				## GET THE RESULTS OF THE QUERY
					while (my @arr = $sth->fetchrow) {
						my ($is_id, $is_id_text, $is_title, $is_editable_by, $isg_id, $isg_id_text, $isg_title, $isg_seq_num, $page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav, $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author, $page_fullwidth, $list_page) = @arr;
							## PRINT GROUP
							if ($isg_id ne $last_group_id) {
								$currentpage_content .= "</ul>" if ($loop_counter ne '1');
								if ($isg_id_text eq '') {
									$currentpage_content .= "<strong><a href=\"/cgi/index.cgi?show_sg=$isg_id\">$isg_title</a></strong><ul>";
								} else {
									$currentpage_content .= "<strong><a href=\"/cgi/index.cgi?group=$isg_id_text\">$isg_title</a></strong><ul>";
								}
#								$currentpage_content .= "</table> </td></tr>" if ($loop_counter ne '1');
#								$currentpage_content .= "<tr><td valign=\"top\"><strong><a href=\"/cgi/index.cgi?show_s=$is_id&show_sg=$isg_id\">$isg_title</a></strong></td><td valign=\"top\">\n<table>";
							}

							## PRINT PAGE
								my $pdf = "";
								   $pdf = "(PDF document)" if ($page_redirect_tourl =~ 'pdf');

								if ($page_id_text eq '') {
									$currentpage_content .= "	<li><a href=\"/cgi/index.cgi?pid=$page_id\">$page_title</a>\n";
								} elsif ($list_page eq 'no') {
									$currentpage_content .= "	<a style=\"display:none\" href=\"/cgi/index.cgi?pid=$page_id\">$page_title</a>\n";
								} else {
									$currentpage_content .= "	<li><a href=\"/cgi/index.cgi?page=$page_id_text\">$page_title</a>\n";
								}

								$currentpage_content .= " <br><span style=\"color:#a73329\">(Includes research databases licensed by SEDL)</span>" if ($page_title eq 'External Research Links');
								$currentpage_content .= " &nbsp;&nbsp;&nbsp;(Check out the <a href=\"/cgi/information/newbooks.cgi\"><span style=\"color:#a73329\">New Books for this month</span></a>)" if ($page_title eq 'IRC Catalog');
								$currentpage_content .= " $pdf" if ($pdf ne '');
								$currentpage_content .= "</li>";
#								$currentpage_content .= "<tr><td valign=\"top\">-</td><td><a href=\"/cgi/index.cgi?show_s=$is_id&show_sg=$isg_id&pid=$page_id\">$page_title</a> $pdf</td></tr>";

						# INCREMENT VARIABLES
						$loop_counter++;
						$last_group_id = $isg_id;
					} # END DB QUERY LOOP
#					$currentpage_content .= "</table></td></tr></table>";
					$currentpage_content .= "</ul>";

				###########################################################
				# END: QUERY DATABASE TO BUILD TREE FOR THIS SECTION
				###########################################################
			} # END IF
		} # END IF

		if (($pid eq '') && ($isg_id eq $show_sg) && ($page_content eq '')) {
			$currentpage_title .= "$isg_title" if ($page_content eq '');
			if (($page_content eq '') && ($currentpage_redirect_tourl eq '')) {
				$currentpage_content .= "$isg_description<br><br>" if ($isg_description ne '');
				$currentpage_content .= " This is the top page of this group.  Please choose from the options in the side menu or the list below.\n";
					$currentpage_content .=	"<ul>\n";

				###########################################################
				# START: QUERY DATABASE TO BUILD TREE FOR THIS GROUP
				###########################################################
				my $command = "select intranet_section.is_id, intranet_section.is_id_text, intranet_section.is_title, intranet_section.is_editable_by, intranet_section_group.isg_id, intranet_section_group.isg_id, intranet_section_group.isg_title, intranet_section_group.isg_seq_num, intranet_pages.*
						FROM intranet_section, intranet_section_group, intranet_pages
						WHERE intranet_section.is_id = intranet_section_group.isg_is_id
						AND intranet_section_group.isg_id = intranet_pages.page_isg_id";
#				$command .= " AND intranet_section.is_id like '$show_s'";
				$command .= " AND intranet_section_group.isg_id like '$show_sg'";
				$command .= " order by intranet_pages.page_group_seq";
#		print "<P>COMMAND: $command";

				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
				my $num_matches = $sth->rows;
#		print "<P>MATCHES: $num_matches";
				## GET THE RESULTS OF THE QUERY
					while (my @arr = $sth->fetchrow) {
						my ($is_id, $is_id_text, $is_title, $is_editable_by, $isg_id, $isg_id_text, $isg_title, $isg_seq_num, $page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav, $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author, $page_fullwidth, $list_page) = @arr;
								my $pdf = "";
								   $pdf = "(PDF document)" if ($page_redirect_tourl =~ 'pdf');

								if (($page_redirect_tourl ne '') && ($page_redirect_delay == 0)) {
									$currentpage_content .= "	<li><a href=\"$page_redirect_tourl\">$page_title</a> $pdf</li>\n";
								} elsif ($page_id_text eq '') {
									$currentpage_content .= "	<li><a href=\"/cgi/index.cgi?pid=$page_id\">$page_title</a> $pdf</li>\n";
								} else {
									$currentpage_content .= "	<li><a href=\"/cgi/index.cgi?page=$page_id_text\">$page_title</a> $pdf</li>\n";
								}
					} # END DB QUERY LOOP
					$currentpage_content .=	"</ul>\n";
				###########################################################
				# END: QUERY DATABASE TO BUILD TREE FOR THIS GROUP
				###########################################################
			} # END IF
		} # END IF

		## END: SET DEFAULT FOR PAGE OVERVIEW

		} # END DB QUERY LOOP
		#####################################################################################
		## END: IF NOT DISPLAYING THE HOME PAGE, QUERY DB FOR THIS SECTION, GROUP, OR PAGE
		#####################################################################################


		#################################
		## START: BUILD SIDE NAVIGATION
		#################################
		my %count_items_for_group;
		my %direct_page_link_for_group;
		my $command_count_group_pages = "select intranet_section_group.isg_id, count(intranet_section_group.isg_id),
								intranet_pages.*
						FROM intranet_section, intranet_section_group, intranet_pages
						WHERE intranet_section.is_id = intranet_section_group.isg_is_id
						AND intranet_section_group.isg_id = intranet_pages.page_isg_id
						AND intranet_section.is_id like '$show_s'
						GROUP BY intranet_section_group.isg_id
						order by intranet_section_group.isg_seq_num, intranet_pages.page_group_seq";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_count_group_pages) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#		print "<p>COMMAND: $command_count_group_pages<BR><BR>MATCHES: $num_matches</p>";
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
		    my ($isg_id, $count_isg_id, $page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav, $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author, $page_fullwidth, $list_page) = @arr;
#print "<p class=\"info\">$isg_id, $count_isg_id</p>";
			$count_items_for_group{$isg_id} = $count_isg_id;
			$direct_page_link_for_group{$isg_id} = "/cgi/index.cgi?pid=$page_id";
			$direct_page_link_for_group{$isg_id} = "/cgi/index.cgi?page=$page_id_text" if ($page_id_text ne '');
			$direct_page_link_for_group{$isg_id} = $page_redirect_tourl if ($page_redirect_tourl ne '');
		} # END DB QUERY LOOP


		my $command = "select intranet_section.is_id, intranet_section.is_id_text, intranet_section.is_title,
								intranet_section_group.isg_id, intranet_section_group.isg_id_text, intranet_section_group.isg_title,
								intranet_pages.*
						FROM intranet_section, intranet_section_group, intranet_pages
						WHERE intranet_section.is_id = intranet_section_group.isg_is_id
						AND intranet_section_group.isg_id = intranet_pages.page_isg_id
						AND intranet_section.is_id like '$show_s'
						order by intranet_section_group.isg_seq_num, intranet_pages.page_group_seq";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#print "COMMAND: $command <P>MATCHES: $num_matches";
		$currentpage_leftnav .= "<div id=\"menu-main\">";

		my $last_isg_id = "";
		my $set_section_title = "no";
		my $inside_active_group_block = "no";
		my $counter_passes = "0";

		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
		    my ($is_id, $is_id_text, $is_title, $isg_id, $isg_id_text, $isg_title, $page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav, $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author, $page_fullwidth, $list_page) = @arr;
			$counter_passes++;
			## START: SET SECTION TITLE AT TOP OF LEFT NAV
			if (($is_title ne '') && ($set_section_title ne 'yes')) {
				$currentpage_leftnav .= "<a href=\"/cgi/index.cgi?section=$is_id_text\"><img src=\"/cgi/images/sidenav/$is_id.gif\" height=\"40\" width=\"180\" alt=\"Inside... $is_title\" class=\"noBorder decoration\"></a>\n<ul>\n";
				$set_section_title = "yes";
			}
			## END: SET SECTION TITLE AT TOP OF LEFT NAV

			if (($isg_id ne $last_isg_id) && ($count_items_for_group{$isg_id} == 1)) {
				if ($show_sg == $isg_id) {
#					$currentpage_leftnav .= "</li>\n" if ($counter_passes > 1); # SEEMS REDUNDANT 2/15/2012 (BL)
					$currentpage_leftnav .= "<li><a href=\"$direct_page_link_for_group{$isg_id}\" id=\"active_menu\">$isg_title</a>\n	<div id=\"menu-main2\"><ul>\n";
					$inside_active_group_block = "yes";
				} else {
					if ($inside_active_group_block eq 'yes') {
						$currentpage_leftnav .= "	</ul></div></li>\n";
						$inside_active_group_block = "no";
					}
					$currentpage_leftnav .= "<li><a href=\"$direct_page_link_for_group{$isg_id}\">$isg_title</a></li>\n";
					$inside_active_group_block = "no";
				}
			} else {

			## START: PRINT SECTION GROUP, IF NEEDED
			if ($isg_id ne $last_isg_id) {
				if ($show_sg == $isg_id) {
#					$currentpage_leftnav .= "</li>\n" if ($counter_passes > 1); # SEEMS REDUNDANT 2/15/2012 (BL)
					if ($isg_id_text eq '') {
						$currentpage_leftnav .= "<li><a href=\"/cgi/index.cgi?show_sg=$isg_id\" id=\"active_menu\">$isg_title</a>\n	<div id=\"menu-main2\"><ul>\n";
					} else {
						$currentpage_leftnav .= "<li><a href=\"/cgi/index.cgi?group=$isg_id_text\" id=\"active_menu\">$isg_title</a>\n	<div id=\"menu-main2\"><ul>\n";
					}
					$inside_active_group_block = "yes";
				} else {
					if ($inside_active_group_block eq 'yes') {
						$currentpage_leftnav .= "	</ul></div></li>\n";
						$inside_active_group_block = "no";
					} else {
#						$currentpage_leftnav .= "</li>\n";
					}
					if ($isg_id_text eq '') {
						$currentpage_leftnav .= "<li><a href=\"/cgi/index.cgi?show_sg=$isg_id\">$isg_title</a></li>\n";
					} else {
						$currentpage_leftnav .= "<li><a href=\"/cgi/index.cgi?group=$isg_id_text\">$isg_title</a></li>\n";
					}
					$inside_active_group_block = "no";
				}
			}
			## END: PRINT SECTION GROUP, IF NEEDED

			}

			## START: PRINT GROUP PAGES, IF ANY
			if (($show_sg == $isg_id) && ($page_display_in_leftnav ne 'no')) {
				if ($pid == $page_id) {
					if (($page_redirect_tourl ne '') && ($page_redirect_delay == 0)) {
						$currentpage_leftnav .= "	<li><a href=\"$page_redirect_tourl\" class=\"nu\" id=\"active_submenu\">$page_title</a></li>\n";
					} elsif ($page_id_text eq '') {
						$currentpage_leftnav .= "	<li><a href=\"/cgi/index.cgi?pid=$page_id\" class=\"nu\" id=\"active_submenu\">$page_title</a></li>\n";
					} else {
						$currentpage_leftnav .= "	<li><a href=\"/cgi/index.cgi?page=$page_id_text\" class=\"nu\" id=\"active_submenu\">$page_title</a></li>\n";
					}
				} else {
					if (($page_redirect_tourl ne '') && ($page_redirect_delay == 0)) {
						$currentpage_leftnav .= "	<li><a href=\"$page_redirect_tourl\" class=\"nu\">$page_title</a></li>\n";
					} elsif ($page_id_text eq '') {
						$currentpage_leftnav .= "	<li><a href=\"/cgi/index.cgi?pid=$page_id\" class=\"nu\">$page_title</a></li>\n";
					} else {
						$currentpage_leftnav .= "	<li><a href=\"/cgi/index.cgi?page=$page_id_text\" class=\"nu\">$page_title</a></li>\n";
					}
				}
			}
			## END: PRINT GROUP PAGES, IF ANY






			$last_isg_id = $isg_id;
		} # END DB QUERY LOOP

		if ($inside_active_group_block eq 'yes') {
			$currentpage_leftnav .= "	</ul></div></li>\n";
			$inside_active_group_block = "no";
		}
#		$currentpage_leftnav .= "</table>";
		$currentpage_leftnav .= "</ul>\n</div>\n";
		#################################
		## END: BUILD SIDE NAVIGATION
		#################################
	}
	##################################################
	## END: IF NOT DISPLAYING THE HOME PAGE
	##################################################

		#################################################
		# START: STANDARD STAFF PAGE HEADER
		#################################################
		$page_title = "missing page?" if ($page_title eq '');
my $currentpage_title_nohtml = $currentpage_title;
   $currentpage_title_nohtml =~ s/<.+?>//g;
print<<EOM;
$page_header_info
EOM
	#######################################################################
	## START: LOAD THE STANDARD JAVASCRIPT MODULE REQUIRED TO EMBED FLASH
	#######################################################################
	if ($currentpage_content =~ '.swf') {
print<<EOM;
<script src="/common/javascript/standard.js" type="text/javascript"></script>
EOM
	}
	#######################################################################
	## END: LOAD THE STANDARD JAVASCRIPT MODULE REQUIRED TO EMBED FLASH
	#######################################################################
	#######################################################################
	## START: LOAD THE STANDARD JAVASCRIPT MODULE REQUIRED TO EMBED FLASH
	#######################################################################
	if ($currentpage_content =~ 'jwplayer') {
print<<EOM;
<script type="text/javascript" src="http://www.sedl.org/common/javascript/mediaplayer/jwplayer.js"></script>
EOM
	}
	#######################################################################
	## END: LOAD THE STANDARD JAVASCRIPT MODULE REQUIRED TO EMBED FLASH
	#######################################################################
print<<EOM;
<TITLE>SEDL intranet: $currentpage_title_nohtml</TITLE>
EOM
print "<META HTTP-EQUIV=REFRESH CONTENT=\"$redirect_delay;URL=$currentpage_redirect_tourl\">" if ($currentpage_redirect_tourl ne '');
print<<EOM;
$htmlhead
EOM
		#################################################
		# END: STANDARD STAFF PAGE HEADER
		#################################################

if ($page_fullwidth ne 'yes') {
print<<EOM;
<div id="wrapper" class="clearfix">
EOM
} else {
print<<EOM;
<div class="clearfix">
EOM
}
	################################################################
	## START: DECIDE WHETHER TO SHOW LEFT OR RIGHT SIDE NAVIGATION
	##        AND PRINT THE PREFERRED PAGE VIEW
	################################################################
	if ($pref_side_navigation eq 'right') {
		&print_content($currentpage_content, $page_fullwidth);
		&print_side_navigation($currentpage_leftnav) if ($page_fullwidth ne 'yes');
	} else {
		&print_side_navigation($currentpage_leftnav) if ($page_fullwidth ne 'yes');
		&print_content($currentpage_content, $page_fullwidth);
	}
	################################################################
	## END: DECIDE WHETHER TO SHOW LEFT OR RIGHT SIDE NAVIGATION
	##        AND PRINT THE PREFERRED PAGE VIEW
	################################################################

	###################################################################################################
	## START: PRINT PAGE CONTENT AREA
	###################################################################################################
sub print_content {
	my $currentpage_content = $_[0];
	my $full_width = $_[1];
	my $full_width_code;
	if ($full_width eq 'yes') {
		$full_width_code = " style=\"width:960px;\"";
	} # END IF
print<<EOM;
<div id="maincol" $full_width_code>
	<div style="padding:15px;">

EOM
print "<p class=\"info\">$feedback_message</p>" if ($feedback_message ne '');
print "<p class=\"alert\">$error_message</p>" if ($error_message ne '');
	if ($currentpage_title ne '') {
print<<EOM;
<h1 style="margin-top:0px;padding-top:0px">$edit_link
$currentpage_title</h1>

EOM
	}
print<<EOM;
		$currentpage_content
	</div>
<P>
</div>
EOM
}
	###################################################################################################
	## END: PRINT PAGE CONTENT AREA
	###################################################################################################


	###################################################################################################
	## START: PRINT SIDE NAVIGATION
	###################################################################################################
sub print_side_navigation {
	my $currentpage_navigation = $_[0];
	$currentpage_navigation = "$currentpage_navigation<BR>" if ($currentpage_navigation ne '');
print<<EOM;
<div id="leftcol">

$currentpage_navigation
	<table class="noBorder" CELLPADDING="6" CELLSPACING="0" style="background:#$bgcolor">
	<tr><td VALIGN="TOP">
EOM

		###########################################################
		## START: TELL USER IF LOGGED IN - IF NOT, SHOW LOGON FORM
		###########################################################
		if ($cookie_ss_session_id eq '') {
print<<EOM;

	<p></p>
	<FORM ACTION="/staff/sims/sims_logon.php" METHOD="POST">
	$sidebar_boxtop_login
	<table class="noBorder" CELLPADDING="2" CELLSPACING="0" WIDTH="100%" style="background:#FFFFFF;">
	<tr><td VALIGN="TOP">
			<strong><label for="logon_user">ID:</label> </strong>
				<INPUT TYPE="text" NAME="logon_user" id="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"><BR>
			<strong><label for="logon_pass">Pass:</label></strong> <INPUT TYPE="PASSWORD" NAME="logon_pass" id="logon_pass" SIZE="10">
			<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
			<INPUT TYPE="HIDDEN" NAME="show_s" VALUE="$show_s">
			<INPUT TYPE="HIDDEN" NAME="show_sg" VALUE="$show_sg">
			<INPUT TYPE="HIDDEN" NAME="pid" VALUE="$pid">
			<INPUT TYPE="SUBMIT" VALUE="log in">
			<br><br>
			<a href="/cgi/forgot_password.cgi?location=temp_session">forgot password?</a>
		</td></tr>
	</table>
	</form>
$sidebar_boxbottom
<p></p>
EOM
		}
#		else {
if ($cookie_ss_staff_id ne '') {
print<<EOM;
$sidebar_boxtop
EOM
if ($staff_member_full_name ne '') {
print<<EOM;
$staff_member_full_name\'s Links (<a href=\"http://www.sedl.org/staff/sims/sims_deletesession.php\" title="Click here to log out.">Log Out</a>)<br>
EOM
}
print<<EOM;
<table class="noBorder" cellpadding="2" cellspacing="0">
<tr><td valign="top"><a href="http://project.sedl.org/PWA/"><img src="/images/maps/dotred_6pt.gif"  alt="SharePoint PWA" class="noBorder"></a></td>
	<td valign="top"><a href="http://project.sedl.org/PWA/">SharePoint PWA</a></td></tr>
EOM
## /cgi/index.cgi?location=logout # OLD LOGOUT LINK - 6/2007
	#############################################
	## START: SHOW USER PREFERRED QUICK LINKS
	#############################################
	my $counter_show_QL = 0;
	my $links_shown = 0;
	while ($counter_show_QL <= 18) {
		if (($p_quicklinks[$counter_show_QL] ne '') && ($p_quicklinks[$counter_show_QL + 1] ne '')) {
			$links_shown++;
print<<EOM;
<tr><td valign="top"><a href="$p_quicklinks[$counter_show_QL]"><img src="/images/maps/dotred_6pt.gif"  alt="$p_quicklinks[$counter_show_QL + 1]" class="noBorder"></a></td>
	<td valign="top"><a href="$p_quicklinks[$counter_show_QL]">$p_quicklinks[$counter_show_QL + 1]</a></td></tr>
EOM
		} # END IF
		$counter_show_QL = $counter_show_QL + 2;
	} # END WHILE LOOP
	if ($links_shown == 0) {
		print "<tr><td colspan=\"2\">You have not <a href=\"index.cgi?location=customize\">set up</a> your personalized Quick Links yet.</td></tr>";
	}
	#############################################
	## END: SHOW USER PREFERRED QUICK LINKS
	#############################################


	###############################################
	## START: SHOW LINK TO INTRANET ADMINISTRATION
	###############################################
	if (($user_is_admin eq 'yes') && ($cookie_ss_session_id ne '')) {
print<<EOM;
<tr><td valign="top"><a href="$location_admin_script?location=admin&amp;show_s=$show_s&amp;show_sg=$show_sg"><img src="/images/maps/dotred_6pt.gif" alt="intranet administration" class="noBorder"></a></td>
	<td valign="top"><a href="$location_admin_script?location=admin&amp;show_s=$show_s&amp;show_sg=$show_sg">intranet administration</a></td></tr>
EOM
	}
	###############################################
	## START: SHOW LINK TO INTRANET ADMINISTRATION
	###############################################

#print<<EOM;
#<tr><td valign="top"><img src="/images/maps/dotred_6pt.gif" alt="*"></a></td>
#	<td valign="top"><a href="/cgi/index.cgi?location=customize" title="Click here to update your SEDL intranet preferences.">intranet preferences</a></td></tr>
#EOM

print<<EOM;
<tr><td valign="top"><a href="/cgi/change_password.cgi"><img src="/images/maps/dotred_6pt.gif" alt="Change intranet password" class="noBorder"></a></td>
	<td valign="top"><a href="/cgi/change_password.cgi">Change intranet password</a></td></tr>
</table>
$sidebar_boxbottom
EOM
}

		###########################################################
		## END: TELL USER IF LOGGED IN - IF NOT, SHOW LOGON FORM
		###########################################################

	if ($cookie_random_content_id < 3) {
		#######################################################
		## START: GRAB DATA FOR NEWS ITEMS PLACED ON HOME PAGE
		#######################################################
		open(SOURCEFILE,"</home/httpd/html/staff/includes/flash/birthdays.txt");
		while (<SOURCEFILE>) {
			$include_birthdays .= $_;
		}
		close(SOURCEFILE);
		$include_birthdays =~ s/birthdays=//g;
		$include_birthdays =~ s/\n/\<br>/g;
		$include_birthdays =~ s/\%26/\&amp;/g;

		$include_birthdays = "QQQ$include_birthdays";
		$include_birthdays =~ s/QQQ\<BR\>\<BR\>//gi;
		$include_birthdays =~ s/QQQ<BR\>//gi;
		#$include_birthdays =~ s/<BR\>\<BR\>/<BR\>/gi;
		#$include_birthdays =~ s/<BR\>\<BR\>/<BR\>/gi;
		$include_birthdays =~ s/----------//gi;
print<<EOM;
	<p></p>

	$sidebar_boxtop_staff
	<DIV class=small>
		$include_birthdays
	</DIV>
EOM
		} else {
print<<EOM;
	<br>

	$sidebar_boxtop_pressreleases

		<a href="/new/media.html">Media releases</a> are sent out regularly to SEDL audiences.
<br><br>
		<strong>Ready? Tell your story!</strong><br>Do you have news about your project that is ready to announce to the public?
		<br> <br>
		Contact <a href="mailto:laura.shankland\@sedl.org">Laura Shankland</a> to prepare a media release for the over 21,000 clients subscribed to the SEDL e-bulletin.

EOM
		}
print<<EOM;
	$sidebar_boxbottom

	</td></tr>
	</table>
	<br>
<IMG SRC="/images/spacer.gif" HEIGHT="1" WIDTH="180" ALT="">
</div>
EOM


	} # END SUBROUTINE PRINT_SIDE_NAVIGATION
	###################################################################################################
	## END: PRINT SIDE NAVIGATION
	###################################################################################################

	print "</div>"; # ENDS DIV ID = "wrapper"
}
##########################################
# END: LOCATION SHOW_PAGE
##########################################


################################################################################
## PRINT HTML FOOTER
################################################################################
if ($cookie_ss_staff_id eq 'blitke') {
#print<<EOM;
#<table BORDER="0" CELLPADDING="15" CELLSPACING="0">
#<tr><td><span style="color:#999999;">
#	DEBUG VARIABLES:
#	<BR>LOCATION: $location
#	<BR>SHOW_S: $show_s
#	<BR>SHOW_SG: $show_sg
#	<BR>PID: $pid
#	<BR>ADMIN USER? $user_is_admin
#	<BR> USER ID $cookie_ss_session_id
#	</span></td></tr></table>
#EOM
}
print "$htmltail";



####################################################################
##  HERE ARE SOME SUBROUTINES USED BY THIS DATABASE SEARCH SCRIPT ##
####################################################################



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





#!/usr/bin/perl

# Copyright 2000 by Southwest Educational Development Laboratory
# 2001-10-18 Modified for new Staff Page template, moved to:http://www.sedl.org/staff/personnel/workrequest2.cgi
# Written by Brian Litke 5-30-2000 for form at http://www.sedl.org/staff/reports/ofts-workrequest2.cgi

use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);

use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "", "");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 
use intranetcommoncode; 

my $query = new CGI;

my $browser = $ENV{"HTTP_USER_AGENT"};
my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};


#### Set/Initialize these variables ###
my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS

my $htmlroot = '/home/httpd/html';

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
	my $date_full_mysql = POSIX::strftime('%D', localtime(time)); # Full date in human-readable format  (e.g. 03/06/08)
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
	my $this_user_id = "$timestamp$ipnum$ipnum2";
	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################


### GET VARIABLES FROM THE FORM IN CASE USER DID NOT ENTER ALL FIELDS
my $location = $query->param('location');
   $location = "report" if ($location eq '');

my $errormessage = "";


#######################################
# READ IN SEDL HEADER AND FOOTER HTML #
#######################################
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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("358"); # 358 is the PID for this page in the intranet database

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";


## COOKIE VARIABLES
my $expdate = "Fri, 25-Dec-2015 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";

####################################################
# START: GRAB STAFF ID, IF ANY, FROM COOKIE
####################################################
my $cookie_ss_session_id = ""; # TRACK SESSION ID
my $cookie_ss_staff_id = ""; # TRACK USER ID
my $cookie_search_fav = ""; # TRACK SEARCH PREFERENCE
my $cookie_random_content_id = "1"; # RANDOMIZE SOME CONTENT ON THE HOME PAGE
	my(%cookies) = getCookies();
	foreach (sort(keys(%cookies))) {
		$cookie_ss_staff_id = $cookies{$_} if ($_ eq 'staffid');
		
	} # END FOREACH LOOP
####################################################
# END: GRAB STAFF ID, IF ANY, FROM COOKIE
####################################################

## PRINT PAGE HEADER
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL - Work Order Request</TITLE>
</HEAD>
$htmlhead
EOM


#################################################################################
## START: LOCATION = report
#################################################################################
if ($location eq 'report') {


print<<EOM;
<H1>Building Maintenance Request Report</H1>
EOM
print "<p class=\"alert\">$errormessage</p>" if ($errormessage ne '');
print<<EOM;
<script language="JavaScript" type="text/JavaScript">
<!--
		
function checkFields() { 

	// Question - RADIO
	var user_input = 0;
	for (i=0;i < 4;i++) {
		if (document.form2.request_type1[i].checked == true) {
			user_input++;
		}
	}
	if (user_input < 1) {
		alert("Please indicate a request type.");
		return false;
	}

}
// -->
</script>
<p>
This report lists work requests received through the intranet 
<a href="http://www.sedl.org/staff/personnel/workrequest.cgi">building maintenance request form</a>.
</p>

<form method="post" action="workrequest2.cgi" id="form2" name="form2" onsubmit="return checkFields()">

<TABLE BORDER="1" CELLPADDING="2" CELLSPACING="0">
<tr bgcolor="#ebebeb">
	<td><b>Request Type</b></td>
	<td><b>Building Location</b></td>
	<td><b>Request Details</b></td>
	<td><b>Start date</b></td>
	<td><b>End date</b></td>
	<td><b>Date Submitted</b></td>
</tr>

EOM
	my $command = "SELECT * FROM work_request order by wr_timestamp DESC";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_entries = $sth->rows;

		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
		my ($wr_id, $wr_name, $wr_building_location, $wr_request_type, $wr_request_details, $wr_startdate, $wr_enddate, $wr_starttime, $wr_endtime, $wr_unique_save_id, $wr_timestamp) = @arr;

		$wr_startdate = &date2standard($wr_startdate);
		$wr_enddate = &date2standard($wr_enddate);
		$wr_starttime = "<br>($wr_starttime)" if ($wr_starttime ne '');
		$wr_endtime = "<br>($wr_endtime)" if ($wr_endtime ne '');
		$wr_timestamp = &convert_timestamp_2pretty_w_date($wr_timestamp, "yes");
		$wr_startdate = "N/A" if ($wr_startdate eq '');
		$wr_enddate = "N/A" if ($wr_enddate eq '');
		$wr_request_details = "N/A" if ($wr_request_details eq '');
print<<EOM;

<tr>
	<td valign="top">$wr_request_type</td>
	<td valign="top">$wr_building_location</td>
	<td valign="top">$wr_request_details</td>
	<td valign="top">$wr_startdate $wr_starttime</td>
	<td valign="top">$wr_enddate $wr_endtime</td>
	<td valign="top">$wr_timestamp<br>
		by $wr_name</td>
</tr>
EOM

		} # END DB QUERY LOOP


print<<EOM;
</table>
<p></p>
<P>
To report troubles using this form, send an e-mail to <A HREF=\"mailto:webmaster\@sedl.org\">webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
EOM

}
#################################################################################
## END: LOCATION = report
#################################################################################







#print "<p>LOCAITON: $location</p>";
print<<EOM;
$htmltail
EOM



####################################################################
##  SUBROUTINES USED BY THIS SCRIPT
####################################################################

####################################################################
## START: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################
sub backslash_fordb {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\/\>/\>/g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/Ô/\\Ô/g;
   $dirtyitem =~ s/Õ/\\Õ/g;
   $dirtyitem =~ s/"/\\"/g;
   return($dirtyitem);
}
####################################################################
## END: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################

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
## START: SUBROUTINE show_hours_menu
######################################
sub show_hours_menu {
	my $field_name = $_[0];
print<<EOM;
	<select name="$field_name">
	<option value=""></option>
	<option value="7 am">7 am</option>
	<option value="8 am">8 am</option>
	<option value="9 am">9 am</option>
	<option value="10 am">10 am</option>
	<option value="11 am">11 am</option>
	<option value="12 noon">12 noon</option>
	<option value="1 pm">1 pm</option>
	<option value="2 pm">2 pm</option>
	<option value="3 pm">3 pm</option>
	<option value="4 pm">4 pm</option>
	<option value="5 pm">5 pm</option>
	<option value="6 pm">6 pm</option>
	<option value="7 pm">7 pm</option>
	<option value="8 pm">8 pm</option>
	</select>
EOM
}
######################################
## END: SUBROUTINE show_hours_menu
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

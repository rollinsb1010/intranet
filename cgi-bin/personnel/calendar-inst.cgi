#!/usr/bin/perl 

#######################################################################################
# This script was constructed for the SEDL Product Catalog 08/24/99 by Brian Litke
# Altered to generate the staff profile pages and staff directory 9/17/2001 (6 days after World Trade center bombing)
#
# 09-17-01  Enabled generation of Staff Profiles using 2001 Web site template
#######################################################################################

#use diagnostics;
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
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


my $query = new CGI;
my $debug = $query->param("debug") || "0"; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
my $count = 0;
my $countart = 0;
my $sortbylabel = "";
my $sortbylabel2 = "";
my $referer = $ENV{"HTTP_REFERER"};

my $template = $query->param("template");
my $printversion = $query->param("printversion");
my $showpast = $query->param("showpast");
my $show_year = $query->param("show_year");

my $htmlhead;
my $htmltail;

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("14"); # 14 is the PID for this page in the intranet database

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";


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

## SET A DEFAULT YEAR IF NONE SPECIFIED
if ($show_year eq '') {
	$show_year = $year;
}



print header;
print<<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>SEDL Institutional Calendar</title>
EOM
if ($printversion ne 'yes') {
print<<EOM;
$htmlhead
EOM
} else {
print<<EOM;
<link href="/staff/includes/staff2006.css" rel="stylesheet" type="text/css" media="screen">
</head>
<body>
EOM
}
print<<EOM;
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td valign="top"><h1>SEDL Institutional Calendar - $show_year</h1>
EOM
if ($showpast eq 'yes') {
print<<EOM;
Toggle view: <a href=\"calendar-inst.cgi?show_year=$show_year\">only future dates in $show_year</a> or 
		<strong>past and future dates in $show_year</strong>.
EOM
} else {
print<<EOM;
Toggle view: <strong>only future dates in $show_year</strong> or 
		<a href=\"calendar-inst.cgi?show_year=$show_year&amp;showpast=yes\">past and future dates in $show_year</a>.
EOM
}
print "</td>";

	if ($printversion ne 'yes') {
print<<EOM;
	<td align="right">
		<table class="dottedBoxyw" cellpadding="2" cellspacing="0" border="0" width="300">
		<tr><td colspan="2"><strong>SEDL Calendars:</strong> (<A HREF="/staff/personnel/calendar-admin.cgi">edit institutional calendar</a>)</td></tr>
		<tr><td valign="top"><img src="/images/bullets/dot-green.gif" vspace="3"></td>
			<td>
EOM
	if ($show_year eq '2009') {
print<<EOM;
<strong>2009 Institutional Calendar</strong> (<a href="/staff/personnel/calendar-inst.cgi?show_year=2010">2010</a> | <a href="/staff/personnel/calendar-inst.cgi?show_year=2011">2011</a> | <a href="/staff/personnel/calendar-inst.cgi?show_year=2012">2012</a>)
EOM
	} elsif ($show_year eq '2010') {
print<<EOM;
<a href="/staff/personnel/calendar-inst.cgi?show_year=2010">2010 Institutional Calendar</a> (<a href="/staff/personnel/calendar-inst.cgi?show_year=2011">2011</a> | <a href="/staff/personnel/calendar-inst.cgi?show_year=2012">2012</a>)
EOM
	} elsif ($show_year eq '2011') {
print<<EOM;
2011 Institutional Calendar (<a href="/staff/personnel/calendar-inst.cgi?show_year=2012">2012 calendar</a>)
EOM
	} elsif ($show_year eq '2012') {
print<<EOM;
2012 Institutional Calendar (<a href="/staff/personnel/calendar-inst.cgi?show_year=2013">2013 calendar</a>)
EOM
	} elsif ($show_year eq '2013') {
print<<EOM;
2013 Institutional Calendar (<a href="/staff/personnel/calendar-inst.cgi?show_year=2014">2014 calendar</a>)
EOM
	} elsif ($show_year eq '2014') {
print<<EOM;
<a href="/staff/personnel/calendar-inst.cgi?show_year=2013">2013 Institutional Calendar</a> (2014 Institutional Calendar)
EOM
	}
print<<EOM;
			</td></tr>
		<tr><td valign="top"><img src="/images/bullets/dot-green.gif" vspace="3"></td>
			<td><a href="/staff/planning/internal_pd_opportunities.cgi">SEDL Internal Prof. Dev. Opportunities</a></td></tr>
		<tr><td valign="top"><img src="/images/bullets/dot-green.gif" vspace="3"></td>
			<td><a href="/calendars/">iCal Calendars</a></td></tr>
		</table>
	</td>
EOM
	}
print<<EOM;
</tr>
</table>
<p></p>
<table cellpadding="3" cellspacing="0" BORDER="1" bgcolor="#FFFFFF">
<TR><TD COLSPAN=3 BGCOLOR="#EBEBEB" align="center">$show_year <FONT COLOR="RED">Holidays</FONT>, Institutional Days, and SMC Meeting Dates</TD></TR>

EOM


my $thisyear;
my $thismonth;
my $lastmonth;
my $command = "select startdate, enddate, eventname1, eventname2, show_on_calendars, contact_web from sedlcalendar 
					where (show_on_calendars LIKE '%intranet-institution%') 
					AND startdate like '%$show_year%' order by startdate";

	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	
	print "<P>COMMAND TO GRAB CALENDAR EVENTS: $command<br />MATCHES: $num_matches<P>" if ($debug eq '1');
	
	## GET THE RESULTS OF THE QUERY
	while (my @arr = $sth->fetchrow) {
    	my ($startdate, $enddate, $eventname1, $eventname2, $show_on_calendars, $contact_web) = @arr;
			my $bgcolor = "";
			   $bgcolor = " bgcolor=\"#cccccc\"" if ($startdate lt $date_full_mysql);
			$thismonth = substr($startdate,5,2);
			$thisyear = substr($startdate,0,4);
$eventname1 =~ s/#44796e/red/gi;
my $startdate_pretty = &date2standard($startdate);
if (($thismonth ne $lastmonth)
	&& ((($thismonth >= $month) && ($showpast eq '')) || ($showpast ne '') || ($thisyear ne $year))
	){
	my $thismonth_label = &fullMonthName($thismonth);
	print "<tr style=\"background-color:#B8D156\"><td colspan=\"2\"><strong>$thismonth_label Events</strong></td></tr>";
}
if (($startdate ge $date_full_mysql) || ($showpast ne '')) {
print<<EOM;
<tr><td valign="top" NOWRAP>$startdate_pretty</td>
 	<td valign="top">
EOM
print "$eventname1";
print ":<br><em>$eventname2</em>" if ($eventname2 ne '');
print " (<a href=\"$contact_web\" target=\"_blank\">web site</a>)" if ($contact_web ne '');
print<<EOM;
</td></tr>	
EOM
}
		$lastmonth = $thismonth;
	}

print "</table>";

if ($printversion ne 'yes') {
print<<EOM;
$htmltail
EOM
}

sub date2standard {
my $date2transform = $_[0];
my ($thisyear,$thismonth,$thisdate) = split(/\-/,$date2transform);
   $date2transform = "$thismonth\/$thisdate\/$thisyear";
   $date2transform = "" if $date2transform eq '//';
	return($date2transform);
}

sub fullMonthName {
my $fullmonthname = $_[0];
   $fullmonthname = "January" if $fullmonthname eq '01';
   $fullmonthname = "February" if $fullmonthname eq '02';
   $fullmonthname = "March" if $fullmonthname eq '03';
   $fullmonthname = "April" if $fullmonthname eq '04';
   $fullmonthname = "May" if $fullmonthname eq '05';
   $fullmonthname = "June" if $fullmonthname eq '06';
   $fullmonthname = "July" if $fullmonthname eq '07';
   $fullmonthname = "August" if $fullmonthname eq '08';
   $fullmonthname = "September" if $fullmonthname eq '09';
   $fullmonthname = "October" if $fullmonthname eq '10';
   $fullmonthname = "November" if $fullmonthname eq '11';
   $fullmonthname = "December" if $fullmonthname eq '12';
   return($fullmonthname);
}


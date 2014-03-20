#!/usr/bin/perl 

# Written by Brian Litke (10-31-1999)
# Copyright 1999 by Southwest Educational Development Laboratory

use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

my $query = new CGI;

## Need to test Dana Accounts:
	#	janicebradley@mail.utexas.edu
	#	21854

######################################
### Set/Initialize these variables ###
######################################
my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS


##########################################
# START: GRAB CGI ENVIRONMENTAL VARIABLES
##########################################
my $browser = $ENV{"HTTP_USER_AGENT"};
my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};
##########################################
# END: GRAB CGI ENVIRONMENTAL VARIABLES
##########################################

my @times = ("", "07:00 - 07:30", "07:30 - 08:00", "08:00 - 08:30", "08:30 - 09:00", "09:00 - 09:30", "09:30 - 10:00", "10:00 - 10:30", "10:30 - 11:00", "11:00 - 11:30", "11:30 - Noon", "12:00 - 12:30", "12:30 - 01:00", "01:00 - 01:30", "01:30 - 02:00", "02:00 - 02:30", "02:30 - 03:00", "03:00 - 03:30", "03:30 - 04:00", "04:00 - 04:30", "04:30 - 05:00", "05:00 - 05:30", "05:30 - 06:00");
my @reservations = ("", ""); #This will be replaced with the database soon.
my $reload = $query->param("reload");   # From Scheduling page
my $reload2 = $query->param("reload2"); # From Admin page

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
	my $time_min = POSIX::strftime('%M', localtime(time)); # Minutes
	my $time_sec = POSIX::strftime('%S', localtime(time)); # Seconds
	my $timestamp = "$year$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; # 14-digit timestamp (e.g. 20080306143938)

my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
	my $this_user_id = "$timestamp$ipnum$ipnum2";
	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################



###############################
# READ THE CONFIGURATION FILE #
###############################
open (CONFIGDATA,"/home/httpd/html/staff/reports/schedulerconfig-mueller");
my @config = <CONFIGDATA>;
  chop (@config);
  close CONFIGDATA;

my $schedulename = $config[0];
my $room1name = $config[1];
my $room1status = $config[2];
my $room2name = $config[3];
my $room2status = $config[4];
my $room3name = $config[5];
my $room3status = $config[6];
my $room4name = $config[7];
my $room4status = $config[8];
my $room5name = $config[9];
my $room5status = $config[10];
my $room6name = $config[11];
my $room6status = $config[12];
my $room7name = $config[13];
my $room7status = $config[14];
my $room8name = $config[15];
my $room8status = $config[16];
my $room9name = $config[17];
my $room9status = $config[18];
my $room10name = $config[19];
my $room10status = $config[20];
my $room11name = $config[21];
my $room11status = $config[22];
my $room12name = $config[23];
my $room12status = $config[24];
my $room13name = $config[25];
my $room13status = $config[26];
my $room14name = $config[27];
my $room14status = $config[28];
my $room15name = $config[29];
my $room15status = $config[30];
my $room16name = $config[31];
my $room16status = $config[32];


####################################################################
## START: COMPUTE MYSQLDATE AND DATE FOR ONE WEEK FROM NOW
####################################################################


## START: COMPUTE DATE FOR ONE WEEK FROM NOW
	my $holdyear_oneweek_fromnow = $year;
	my $holdmonth_oneweek_fromnow = $month;
	my $holddate_oneweek_fromnow = $monthdate_wleadingzero + 7;

	# HANDLE FEBRUARY
	if (($holddate_oneweek_fromnow > 28) && ($month eq '02')) {
		$holdmonth_oneweek_fromnow++;
		$holddate_oneweek_fromnow = $holddate_oneweek_fromnow - 28;
	}
	# HANDLE MONTHS WITH 30 DAYS
	if (($holddate_oneweek_fromnow > 30) && (($month eq '04') || ($month eq '06') || ($month eq '09') || ($month eq '11')) ) {
		$holdmonth_oneweek_fromnow++;
		$holddate_oneweek_fromnow = $holddate_oneweek_fromnow - 30;
	}
	# HANDLE MONTHS WITH 31 DAYS
	if (($holddate_oneweek_fromnow > 31) && (($month eq '01') || ($month eq '03') || ($month eq '05') || ($month eq '07') || ($month eq '08') || ($month eq '10') || ($month eq '12')) ) {
		$holdmonth_oneweek_fromnow++;
		$holddate_oneweek_fromnow = $holddate_oneweek_fromnow - 31;
			## HANDLE IF REQCHED END OF YEAR
			if ($holdmonth_oneweek_fromnow eq '13') {
				$holdmonth_oneweek_fromnow = "01";
				$holdyear_oneweek_fromnow++;
			}
	}
   $holddate_oneweek_fromnow = "01" if $holddate_oneweek_fromnow eq '1'; $holddate_oneweek_fromnow = "02" if $holddate_oneweek_fromnow eq '2'; $holddate_oneweek_fromnow = "03" if $holddate_oneweek_fromnow eq '3'; $holddate_oneweek_fromnow = "04" if $holddate_oneweek_fromnow eq '4'; $holddate_oneweek_fromnow = "05" if $holddate_oneweek_fromnow eq '5'; $holddate_oneweek_fromnow = "06" if $holddate_oneweek_fromnow eq '6'; $holddate_oneweek_fromnow = "07" if $holddate_oneweek_fromnow eq '7'; $holddate_oneweek_fromnow = "08" if $holddate_oneweek_fromnow eq '8'; $holddate_oneweek_fromnow = "09" if $holddate_oneweek_fromnow eq '9';
   $holdmonth_oneweek_fromnow = "01" if $holdmonth_oneweek_fromnow eq '1'; $holdmonth_oneweek_fromnow = "02" if $holdmonth_oneweek_fromnow eq '2'; $holdmonth_oneweek_fromnow = "03" if $holdmonth_oneweek_fromnow eq '3';   $holdmonth_oneweek_fromnow = "04" if $holdmonth_oneweek_fromnow eq '4'; $holdmonth_oneweek_fromnow = "05" if $holdmonth_oneweek_fromnow eq '5'; $holdmonth_oneweek_fromnow = "06" if $holdmonth_oneweek_fromnow eq '6'; $holdmonth_oneweek_fromnow = "07" if $holdmonth_oneweek_fromnow eq '7'; $holdmonth_oneweek_fromnow = "08" if $holdmonth_oneweek_fromnow eq '8'; $holdmonth_oneweek_fromnow = "09" if $holdmonth_oneweek_fromnow eq '9';

my $date_mysql_oneweek_fromnow = "$holdyear_oneweek_fromnow\-$holdmonth_oneweek_fromnow\-$holddate_oneweek_fromnow";
my $date_mysql_oneweek_fromnow_stamp = "$holdyear_oneweek_fromnow$holdmonth_oneweek_fromnow$holddate_oneweek_fromnow";
####################################################################
## END: COMPUTE MYSQLDATE AND DATE FOR ONE WEEK FROM NOW
####################################################################



## Query database room reservations for date range and parse all staff user IDs into an array.
my @names_of_room_reservers;


## Query the database again, sorted by date
my %last_date_heading_for_user;  ## HOLDS THE DATE OF THE LAST HEADING SHOWN FOR EACH USER
my %room_reservation_report_for_user; ## Holds the accumulated reoprt.


## START: LOOP THROUGH THE LIST OF STAFF WHO MADE RESERVATIONS, SENDING AN E-MAIL TO EACH





my $changewhat = $query->param("cw");
my $action = $query->param("a");
my $calname = $query->param("cn");
   $calname = "Invalid Calendar Name" if $calname eq '';
my $calyear = $query->param("cy");
   $calyear = "$year" if $calyear eq '';
my $calmonth = $query->param("cm");
   $calmonth = "$month" if $calmonth eq '';
   $calmonth = "01" if $calmonth eq 'Jan';
   $calmonth = "02" if $calmonth eq 'Feb';
   $calmonth = "03" if $calmonth eq 'Mar';
   $calmonth = "04" if $calmonth eq 'Apr';
   $calmonth = "05" if $calmonth eq 'May';
   $calmonth = "06" if $calmonth eq 'Jun';
   $calmonth = "07" if $calmonth eq 'Jul';
   $calmonth = "08" if $calmonth eq 'Aug';
   $calmonth = "09" if $calmonth eq 'Sep';
   $calmonth = "10" if $calmonth eq 'Oct';
   $calmonth = "11" if $calmonth eq 'Nov';
   $calmonth = "12" if $calmonth eq 'Dec';

# Handle move to next year's calendar
if ($calmonth eq '13') {
	$calmonth = '01';
	$calyear = $calyear +1;
}

# Handle move to previous year's calendar
if (($calmonth eq '0') || ($calmonth eq '00')) {
	$calmonth = '12';
	$calyear = $calyear - 1;
}

# Make sure month is supported, if not, then set to current month
if (($calyear > 2008) || ($calyear < 2005)) {
	$errormessage2 = "The month you specified is not supported by the room scheduler. The view is defaulting to the current month.  Contact Brian Litke at ext. 6529 for assistance.";
	$calmonth = "$month"; 
	$calyear = "$year";
} 
   $calmonth = "01" if $calmonth eq 'Jan';
   $calmonth = "02" if $calmonth eq 'Feb';
   $calmonth = "03" if $calmonth eq 'Mar';
   $calmonth = "04" if $calmonth eq 'Apr';
   $calmonth = "05" if $calmonth eq 'May';
   $calmonth = "06" if $calmonth eq 'Jun';
   $calmonth = "07" if $calmonth eq 'Jul';
   $calmonth = "08" if $calmonth eq 'Aug';
   $calmonth = "09" if $calmonth eq 'Sep';
   $calmonth = "10" if $calmonth eq 'Oct';
   $calmonth = "11" if $calmonth eq 'Nov';
   $calmonth = "12" if $calmonth eq 'Dec';


my $calmonthnext = ($calmonth +1); 
my $calmonthprevious = ($calmonth -1);

my $calmonthlabel = '';
   $calmonthlabel = 'January' if $calmonth eq '01';
   $calmonthlabel = 'February' if $calmonth eq '02';
   $calmonthlabel = 'March' if $calmonth eq '03';
   $calmonthlabel = 'April' if $calmonth eq '04';
   $calmonthlabel = 'May' if $calmonth eq '05';
   $calmonthlabel = 'June' if $calmonth eq '06';
   $calmonthlabel = 'July' if $calmonth eq '07';
   $calmonthlabel = 'August' if $calmonth eq '08';
   $calmonthlabel = 'September' if $calmonth eq '09';
   $calmonthlabel = 'October' if $calmonth eq '10';
   $calmonthlabel = 'November' if $calmonth eq '11';
   $calmonthlabel = 'December' if $calmonth eq '12';

$calmonthnext = "0$calmonthnext" if (length($calmonthnext) eq '1');
$calmonthprevious = "0$calmonthprevious" if (length($calmonthprevious) eq '1');

my $caldate = $query->param("cd");
   $caldate = "$date" if $caldate eq '';
my $idprogram = $query->param("idp");
   $idprogram = "anonymous" if $idprogram eq '';
my $permissions = $query->param("p");
   $permissions = "anonymous" if $permissions eq '';
   $permissions = "admin";  ### Remove or comment this line out later ### NOTE: ###


my @calendar = ();

# YEAR 2005  
if ($calyear eq '2005') {
	@calendar = ("00", "00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00") if ($calmonth eq '01');
	@calendar = ("00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '02');
	@calendar = ("00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '03');
	@calendar = ("00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '04');
	@calendar = ("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '05');
	@calendar = ("00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '06');
	@calendar = ("00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '07');
	@calendar = ("00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '08');
	@calendar = ("00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '09');
	@calendar = ("00", "00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00") if ($calmonth eq '10');
	@calendar = ("00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '11');
	@calendar = ("00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '12');
}

# YEAR 2006 
if ($calyear eq '2006') {
	@calendar = ("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '01');
	@calendar = ("00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '02');
	@calendar = ("00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '03');
	@calendar = ("00", "00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '04');
	@calendar = ("00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '05');
	@calendar = ("00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '06');

	@calendar = ("00", "00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00") if ($calmonth eq '07');
	@calendar = ("00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '08');
	@calendar = ("00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '09');
	@calendar = ("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '10');
	@calendar = ("00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '11');
	@calendar = ("00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '12');
}

# YEAR 2007 
if ($calyear eq '2007') {
	@calendar = ("00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '01');
	@calendar = ("00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '02');
	@calendar = ("00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '03');
	@calendar = ("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '04');
	@calendar = ("00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '05');
	@calendar = ("00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '06');

	@calendar = ("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '07');
	@calendar = ("00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '08');
	@calendar = ("00", "00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '09');
	@calendar = ("00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '10');
	@calendar = ("00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '11');
	@calendar = ("00", "00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00") if ($calmonth eq '12');
}

# YEAR 2008 
if ($calyear eq '2008') {
	@calendar = ("00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '01');
	@calendar = ("00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '02');
	@calendar = ("00", "00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00") if ($calmonth eq '03');
	@calendar = ("00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '04');
	@calendar = ("00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '05');
	@calendar = ("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '06');

	@calendar = ("00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '07');
	@calendar = ("00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '08');
	@calendar = ("00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '09');
	@calendar = ("00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '10');
	@calendar = ("00", "00", "00", "00", "00", "00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '11');
	@calendar = ("00", "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, "00", "00", "00", "00", "00", "00", "00", "00", "00", "00", "00") if ($calmonth eq '12');
}

## NEED TO FILL IN DATES FOR 2009 and BEYOND IF YOU WANT CALENDAR TO WORK FOR THESE YEARS

# GET THE NEW EVENT DETAILS FROM THE SCHEDULING PAGE FORM
my $changewhat = $query->param("cw");
my $eventdescription = $query->param("eventdescription");
$eventdescription =~ s/\n/ /g;        
$eventdescription =~ s/\r/ /g;        
$eventdescription =~ s/\t/ /g;        
my $scheduledby = $query->param("scheduledby");
my $comment = $query->param("comment");
   $comment = &cleanthis ($comment);

my $settimeall = $query->param("settimeall");
my $deletetimeall = $query->param("deletetimeall");
my @settime = ();
$settime[1] = $query->param("settime1");
$settime[2] = $query->param("settime2");
$settime[3] = $query->param("settime3");
$settime[4] = $query->param("settime4");
$settime[5] = $query->param("settime5");
$settime[6] = $query->param("settime6");
$settime[7] = $query->param("settime7");
$settime[8] = $query->param("settime8");
$settime[9] = $query->param("settime9");
$settime[10] = $query->param("settime10");
$settime[11] = $query->param("settime11");
$settime[12] = $query->param("settime12");
$settime[13] = $query->param("settime13");
$settime[14] = $query->param("settime14");
$settime[15] = $query->param("settime15");
$settime[16] = $query->param("settime16");
$settime[17] = $query->param("settime17");
$settime[18] = $query->param("settime18");
$settime[19] = $query->param("settime19");
$settime[20] = $query->param("settime20");
$settime[21] = $query->param("settime21");
$settime[22] = $query->param("settime22");

# IF USER RESERVED THE ENTIRE DAY BY SELECT THE ALL-DAY CHECKBOX, CHANGE EACH TIME TO BE SET
my $all = 1;
	if ($settimeall eq 'Yes') {
		while ($all < 23) {
			$settime[$all] = "Yes";
			$all++;
		}
		}

my $deletion_emailsent = "no";
my @delete = ();
$delete[1] = $query->param("delete1");
$delete[2] = $query->param("delete2");
$delete[3] = $query->param("delete3");
$delete[4] = $query->param("delete4");
$delete[5] = $query->param("delete5");
$delete[6] = $query->param("delete6");
$delete[7] = $query->param("delete7");
$delete[8] = $query->param("delete8");
$delete[9] = $query->param("delete9");
$delete[10] = $query->param("delete10");
$delete[11] = $query->param("delete11");
$delete[12] = $query->param("delete12");
$delete[13] = $query->param("delete13");
$delete[14] = $query->param("delete14");
$delete[15] = $query->param("delete15");
$delete[16] = $query->param("delete16");
$delete[17] = $query->param("delete17");
$delete[18] = $query->param("delete18");
$delete[19] = $query->param("delete19");
$delete[20] = $query->param("delete20");
$delete[21] = $query->param("delete21");
$delete[22] = $query->param("delete22");

my @delete2 = ();
$delete2[1] = $query->param("adelete1");
$delete2[2] = $query->param("adelete2");
$delete2[3] = $query->param("adelete3");
$delete2[4] = $query->param("adelete4");
$delete2[5] = $query->param("adelete5");
$delete2[6] = $query->param("adelete6");
$delete2[7] = $query->param("adelete7");
$delete2[8] = $query->param("adelete8");
$delete2[9] = $query->param("adelete9");
$delete2[10] = $query->param("adelete10");
$delete2[11] = $query->param("adelete11");
$delete2[12] = $query->param("adelete12");
$delete2[13] = $query->param("adelete13");
$delete2[14] = $query->param("adelete14");
$delete2[15] = $query->param("adelete15");
$delete2[16] = $query->param("adelete16");
$delete2[17] = $query->param("adelete17");
$delete2[18] = $query->param("adelete18");
$delete2[19] = $query->param("adelete19");
$delete2[20] = $query->param("adelete20");
$delete2[21] = $query->param("adelete21");
$delete2[22] = $query->param("adelete22");


# IF USER DELETED THE ENTIRE DAY BY SELECTING THE ALL-DAY CHECKBOX, CHANGE EACH TIME TO BE SET
my $all = 1;
if ($deletetimeall eq 'Yes') {
	while ($all < 23) {
		$delete[$all] = $delete2[$all];
		$all++;
	}
}



## REMOVE CARRIAGE RETURNS FROM OPEN-ENDED VARIABLES
$calname =~ s/\n/ /g;        
$calname =~ s/\r/ /g;        
$calname =~ s/\t/ /g;        

###################################################################
## START: standard page output
###################################################################
print header;
my $refreshtime = "3";
#$schedulename
print "<HTML><HEAD><TITLE>SEDL Room Scheduler - Mueller SEDL HQ</TITLE>";
print "<META HTTP-EQUIV=REFRESH CONTENT=\"$refreshtime\;URL=/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=$changewhat&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">" if $reload eq 'Yes';
print "<META HTTP-EQUIV=REFRESH CONTENT=\"$refreshtime\;URL=/cgi-bin/mysql/scheduler-mueller.cgi?location=show&cw=$changewhat&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">" if $reload2 eq 'Yes';
$reload = "No";
$reload2 = "No";
print<<EOM;
$htmlhead
EOM
print "<p class=\"alert\">$errormessage</p>" if ($errormessage ne '');
print<<EOM;
<FORM ACTION=/cgi-bin/mysql/scheduler-mueller.cgi METHOD=POST>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
EOM
###################################################################
## END: standard page output
###################################################################


########################################################
## HANDLE 'SCHEDULING PAGE' ACTIONS
########################################################
my $changewhat = $query->param("cw");

if ($location eq 'schedule-go') {


## FIGURE OUT OFFSET FOR UPDATES BASED ON WHICH ROOM IS BEING SCHEDULED
my $z2 = "";
   $z2 = "0" if ($changewhat eq '1');
   $z2 = "22" if ($changewhat eq '3');
   $z2 = "44" if ($changewhat eq '5');
   $z2 = "66" if ($changewhat eq '7');
   $z2 = "88" if ($changewhat eq '9');
   $z2 = "110" if ($changewhat eq '11');
   $z2 = "132" if ($changewhat eq '13');
   $z2 = "154" if ($changewhat eq '15');
   $z2 = "176" if ($changewhat eq '17');
   $z2 = "198" if ($changewhat eq '19');
   $z2 = "220" if ($changewhat eq '21');
   $z2 = "242" if ($changewhat eq '23');
   $z2 = "264" if ($changewhat eq '25');
   $z2 = "286" if ($changewhat eq '27');
   $z2 = "308" if ($changewhat eq '29');
   $z2 = "330" if ($changewhat eq '31');

## PUT ZEROS BEFORE SINGLE DIGIT NUMBERS TO MATCH MYSQL DATE FORMAT
my $mcalmonth = "$calmonth";
my $mcaldate = "$caldate";
my $reservationdate_mysql = "$calyear-$mcalmonth-$mcaldate";
my $w = 0;
my $zz = 1;

## SCHEDULE THE EVENT
my @emailsettime = @settime;
 $emailsettime[1] = "7:00 - 7:30\n" if $emailsettime[1] eq 'Yes';
 $emailsettime[2] = "7:30 - 8:00\n" if $emailsettime[2] eq 'Yes';
 $emailsettime[3] = "8:00 - 8:30\n" if $emailsettime[3] eq 'Yes';
 $emailsettime[4] = "8:30 - 9:00\n" if $emailsettime[4] eq 'Yes';
 $emailsettime[5] = "9:00 - 9:30\n" if $emailsettime[5] eq 'Yes';
 $emailsettime[6] = "9:30 - 10:00\n" if $emailsettime[6] eq 'Yes';
 $emailsettime[7] = "10:00 - 10:30\n" if $emailsettime[7] eq 'Yes';
 $emailsettime[8] = "10:30 - 11:00\n" if $emailsettime[8] eq 'Yes';
 $emailsettime[9] = "11:00 - 11:30\n" if $emailsettime[9] eq 'Yes';
 $emailsettime[10] = "11:30 - 12:00\n" if $emailsettime[10] eq 'Yes';
 $emailsettime[11] = "12:00 - 12:30\n" if $emailsettime[11] eq 'Yes';
 $emailsettime[12] = "12:30 - 1:00\n" if $emailsettime[12] eq 'Yes';
 $emailsettime[13] = "1:00 - 1:30\n" if $emailsettime[13] eq 'Yes';
 $emailsettime[14] = "1:30 - 2:00\n" if $emailsettime[14] eq 'Yes';
 $emailsettime[15] = "2:00 - 2:30\n" if $emailsettime[15] eq 'Yes';
 $emailsettime[16] = "2:30 - 3:00\n" if $emailsettime[16] eq 'Yes';
 $emailsettime[17] = "3:00 - 3:30\n" if $emailsettime[17] eq 'Yes';
 $emailsettime[18] = "3:30 - 4:00\n" if $emailsettime[18] eq 'Yes';
 $emailsettime[19] = "4:00 - 4:30\n" if $emailsettime[19] eq 'Yes';
 $emailsettime[20] = "4:30 - 5:00\n" if $emailsettime[20] eq 'Yes';
 $emailsettime[21] = "5:00 - 5:30\n" if $emailsettime[21] eq 'Yes';
 $emailsettime[22] = "5:30 - 6:00\n" if $emailsettime[22] eq 'Yes';

my $emailshowsettime = "$emailsettime[1]$emailsettime[2]$emailsettime[3]$emailsettime[4]$emailsettime[5]$emailsettime[6]$emailsettime[7]$emailsettime[8]$emailsettime[9]$emailsettime[10]$emailsettime[11]$emailsettime[12]$emailsettime[13]$emailsettime[14]$emailsettime[15]$emailsettime[16]$emailsettime[17]$emailsettime[18]$emailsettime[19]$emailsettime[20]$emailsettime[21]$emailsettime[22]";


## MAKE SURE DATE EXISTS IN DATABASE
&make_sure_date_record_exists("$calyear-$mcalmonth-$mcaldate");

## SCHEDULE EVENT
while ($zz < 23) {
   $w = ($zz + $z2);

## TEST IF THIS SLOT IS NOT YET TAKEN
my $this_slot = "not available";
		my $command = "SELECT s$w from scheduler_mueller where showdate like '$calyear-$mcalmonth-$mcaldate'";
		my $dbh = Mysql->connect('localhost', 'intranet', 'intranetuser', 'limited');
		my $sth = $dbh->query($command);
		my $num_matches = $sth->numrows;
#		print "MATCHES: $num_matches";
		while (my @arr = $sth->fetchrow) {
		    my ($this_room) = @arr;
		    	$this_room = "" if (length($this_room) eq '0');
				if ($this_room eq '') {
					$this_slot = "available";
#					print "available";
				} else {
#					print "<BR>- status: $this_room";
				}
		}
#print "<P><HR>$command<HR>";
#print "<P>Arrived at: schedule-go #3<BR>THIS SLOT = $this_slot AND SETTIME = $settime[$zz]";

	if (($this_slot eq 'available') && ($settime[$zz] eq 'Yes')) {
#print "<P><font color=green>Arrived at: schedule-go #4</font><P>";
		while (length($comment) > 230) {
			chop($comment);
		}
		my $command = "UPDATE scheduler_mueller 
   					SET s$w='$scheduledby\t$comment\t$cookie_ss_staff_id' 
   					where ((showdate like '$calyear-$mcalmonth-$mcaldate') 
   					AND ((ISNULL(s$w)) OR (s$w LIKE '')) )";

		my $dbh = Mysql->connect('localhost', 'intranet', 'intranetuser', 'limited') if $settime[$zz] eq 'Yes';
		my $sth = $dbh->query($command);
	}
	$zz++;
}  ## END OF SCHEDULE EVENT

#######################
## START: DELETE EVENT
#######################
my @reservations = ();
my @reservations1 = ();
my @reservations2 = ();
my @reservations3 = ();
my $w = 0;
my $zz = 1;

my $deletion_processed = "no"; # TRACK WHETHER A DELETION HAPPENED
my $deletion_times = ""; # TRACK WHAT TIMES WERE DELETED
	while ($zz < 23) {
		$w = ($zz + $z2);

		## READ THE DELETE COMMAND CHECK TO SEE IF CURRENT USER MATCHES PERSON WHO SCHEDULED THE EVENT TO BE DELETED
		my ($deleteon,$deletepermissions) = split(/\t/,$delete[$zz]);

		my $command = "UPDATE scheduler_mueller SET s$w=NULL where showdate like '$calyear-$mcalmonth-$mcaldate'";
			if (($deleteon eq 'Yes') && ($deletepermissions eq $cookie_ss_staff_id)) {
   				my $dbh = Mysql->connect('localhost', 'intranet', 'intranetuser', 'limited');
				my $sth = $dbh->query($command);

				$deletion_times .= "7:00 - 7:30\n" if $zz eq '1';
				$deletion_times .= "7:30 - 8:00\n" if $zz eq '2';
				$deletion_times .= "8:00 - 8:30\n" if $zz eq '3';
				$deletion_times .= "8:30 - 9:00\n" if $zz eq '4';
				$deletion_times .= "9:00 - 9:30\n" if $zz eq '5';
				$deletion_times .= "9:30 - 10:00\n" if $zz eq '6';
				$deletion_times .= "10:00 - 10:30\n" if $zz eq '7';
				$deletion_times .= "10:30 - 11:00\n" if $zz eq '8';
				$deletion_times .= "11:00 - 11:30\n" if $zz eq '9';
				$deletion_times .= "11:30 - 12:00\n" if $zz eq '10';
				$deletion_times .= "12:00 - 12:30\n" if $zz eq '11';
				$deletion_times .= "12:30 - 1:00\n" if $zz eq '12';
				$deletion_times .= "1:00 - 1:30\n" if $zz eq '13';
				$deletion_times .= "1:30 - 2:00\n" if $zz eq '14';
				$deletion_times .= "2:00 - 2:30\n" if $zz eq '15';
				$deletion_times .= "2:30 - 3:00\n" if $zz eq '16';
				$deletion_times .= "3:00 - 3:30\n" if $zz eq '17';
				$deletion_times .= "3:30 - 4:00\n" if $zz eq '18';
				$deletion_times .= "4:00 - 4:30\n" if $zz eq '19';
				$deletion_times .= "4:30 - 5:00\n" if $zz eq '20';
				$deletion_times .= "5:00 - 5:30\n" if $zz eq '21';
				$deletion_times .= "5:30 - 6:00\n" if $zz eq '22';
		
				$deletion_processed = "yes";
			} # END IF

		$zz++
	}  ## END OF DELETE EVENT
	$location = "done_deleting";

#	## SEND E-MAIL REGARDING DELETION
#	if ($deletion_processed eq 'yes') {
#		if (($changewhat eq '5') || ($changewhat eq '7')) {
#
#			my $reservationdate_mysql = "$calyear\-$mcalmonth\-$mcaldate";
#			my $reservationdate_stamp = "$calyear$mcalmonth$mcaldate";
#
#			# START DATE CHECK: SEND E-MAIL IF RESERVATION IS ONE WEEK OR LESS AWAY
#			if ($reservationdate_stamp <= $date_mysql_oneweek_fromnow_stamp) { 
#				my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
#				my $recipient = $cookie_ss_staff_id;
#				if ($recipient !~ '\@') {
#					$recipient = "$recipient\@sedl.org";
#				}
#my $fromaddr = 'webmaster@sedl.org';
#open(NOTIFY,"| $mailprog");
#print NOTIFY <<EOM;
#From:  $recipient
#To: jsimmons\@highlandresources.net, lnunez\@highlandresources.net, akriegel\@sedl.org, blitke\@sedl.org
#Reply-To: $fromaddr
#Errors-To: $fromaddr
#Sender: $fromaddr
#Subject: Cancellation of SEDL Southeast Conference Room Reservation - Air Conditioning Request
#
#The reserveation for the SEDL's SE Conference Room 
#has been cancelled by $recipient
#
#Original reservation was for: "$scheduledby - $comment".
#
#For the day of $mcalmonth-$mcaldate-$calyear 
#
#During these time blocks:
#$deletion_times
#
#EOM
#close(NOTIFY);
#			} # END DATE CHECK: SEND E-MAIL IF RESERVATION IS ONE WEEK OR LESS AWAY
#
#
#			$deletion_emailsent = "yes";
#		} # END OF E-MAIL REGARDING CANCELLATION
#	} # END IF EMAILSENT = NO
#######################
## END: DELETE EVENT
#######################


	## SEND AN E-MAIL TO THE USER FOR CONFIRMATION
	if (($settime[1] eq 'Yes') || ($settime[2] eq 'Yes') || ($settime[3] eq 'Yes') || ($settime[4] eq 'Yes') || ($settime[5] eq 'Yes') || ($settime[6] eq 'Yes') || ($settime[7] eq 'Yes') || ($settime[8] eq 'Yes') || ($settime[9] eq 'Yes') || ($settime[10] eq 'Yes') || ($settime[11] eq 'Yes') || ($settime[12] eq 'Yes') || ($settime[13] eq 'Yes') || ($settime[14] eq 'Yes') || ($settime[15] eq 'Yes') || ($settime[16] eq 'Yes') || ($settime[17] eq 'Yes') || ($settime[18] eq 'Yes') || ($settime[19] eq 'Yes') || ($settime[20] eq 'Yes') || ($settime[21] eq 'Yes') || ($settime[22] eq 'Yes')) {

		# These are for mail notification of guest events
		my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
		my $recipient = $cookie_ss_staff_id;
			if ($recipient !~ '\@') {
				$recipient = "$recipient\@sedl.org";
			}
		my $fromaddr = "webmaster\@sedl.org";

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: webmaster\@sedl.org
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Reservation - SEDL Mueller HQ Room Scheduler 

You have scheduled: "$config[$changewhat]"

For the day of: $mcalmonth-$mcaldate-$calyear 

To be used for: "$scheduledby - $comment".


Please remember that if you need any tables, chairs, or special equipment set-up for your use of this space, you must fill out a "Request For Conference/Meeting Items" form available from your Administrative Assistant.

You scheduled these time blocks:
$emailshowsettime

Remember to notify meeting participants of the location, date, and time.


Here are some guidelines for your meeting.
EOM

	if ($changewhat eq 'never-show-this') {
print NOTIFY <<EOM;

COMPUTER TRAINING LAB GUIDELINES
- Drinks with screw-on lids are ok. Open top drinks or cans are prohibited.
- Users are responsible to log out from the computers when the session is complete.
- Users are responsible to save all session documents to CD or file share them to other computers before logging out (if they want to keep these documents). Otherwise, all session documents should be deleted at end of session.
- Technical issues with lab computers should be reported to network specialist, Cliff Pierron at ext. 269.

COMPUTER TRAINING LAB INSTRUCTIONS
- Turning on the In Focus: To turn on the In Focus projector, find the remote control (usually on the session instructor stand). Point the remote at the display screen and press the Power button. If the projector fails to power up the first time, press the Power button again after 30 seconds. To turn off the In Focus projector, press the Power button, then press the Select button.
- Logging on to the Computer Training Lab machines using passwords: To log on to the computer training lab computers, select the Lab iMac login option. Enter the password "maclab". For the instructor's computer, select the Lab iMac Instructor login option. Enter the password "maclab".
- Logging on to the wireless connection in the Computer Training Lab: For those who wish to access the internet via the Computer Training Lab's wireless network, activate the airport or wireless service on your laptop. Enter the password "4training"
- How to shut down the room after use: After the session is complete, each user should log out of his/her computer. The computers do not need to be shut down after use.
- Who to contact for maintenance of the Computer Training Lab: For maintenance or technical support for computer training lab computers, contact Cliff Pierron at ext. 269.



EOM
	} else {
print NOTIFY <<EOM;
	
MEETINGS ALLOWED
- The meeting must be an academic, non-profit association, or an organization that staff member is a member of or affiliated with. The function must not be for personal staff member gain (I.e., Avon, Jewelry, Mary Kay, Tupperware, etc.)
- You are responsible for ensuring the meeting does not violate SEDL policies. For instance, no alcohol is allowed, as that would violate SEDL's policy of a drug and alcohol-free workplace.
- Refreshments are OK, but for non-SEDL meetings, you must make arrangements that do not involve SEDL.

MEETING PARTICIPANTS
- You are responsible for ensuring guests sign-in at the security desk, if the meeting is after normal working hours.
- Please do not disturb staff members who might be working late.

MEETING ROOM CARE AND MAINTENANCE
- You are responsible for locking up the SEDL facility/meeting room after the meeting is finished.
- No tech support is available after hours, unless pre-arranged.
EOM
}
close(NOTIFY);

	$location = "done_adding";
	}

} ## END HANDLE OF SCHEDULING PAGE ACTIONS









########################################################
## HANDLE 'ADMINISTRATION PAGE' ACTIONS
########################################################
if (($location eq 'admin-go') && ($permissions eq 'admin')) {


# GET THE NEW SETTINGS FROM THE ADMIN PAGE FORM
my $schedulename = $query->param("sch");
my $room1name = $query->param("room1name");
my $room1status = $query->param("room1status");
my $room2name = $query->param("room2name");
my $room2status = $query->param("room2status");
my $room3name = $query->param("room3name");
my $room3status = $query->param("room3status");
my $room4name = $query->param("room4name");
my $room4status = $query->param("room4status");
my $room5name = $query->param("room5name");
my $room5status = $query->param("room5status");
my $room6name = $query->param("room6name");
my $room6status = $query->param("room6status");
my $room7name = $query->param("room7name");
my $room7status = $query->param("room7status");
my $room8name = $query->param("room8name");
my $room8status = $query->param("room8status");
my $room9name = $query->param("room9name");
my $room9status = $query->param("room9status");
my $room10name = $query->param("room10name");
my $room10status = $query->param("room10status");
my $room11name = $query->param("room11name");
my $room11status = $query->param("room11status");
my $room12name = $query->param("room12name");
my $room12status = $query->param("room12status");
my $room13name = $query->param("room13name");
my $room13status = $query->param("room13status");
my $room14name = $query->param("room14name");
my $room14status = $query->param("room14status");
my $room15name = $query->param("room15name");
my $room15status = $query->param("room15status");
my $room16name = $query->param("room16name");
my $room16status = $query->param("room16status");

$schedulename = &clean_tabs_returns($schedulename);
$room1name = &clean_tabs_returns($room1name);
$room2name = &clean_tabs_returns($room2name);
$room3name = &clean_tabs_returns($room3name);
$room4name = &clean_tabs_returns($room4name);
$room5name = &clean_tabs_returns($room5name);
$room6name = &clean_tabs_returns($room6name);
$room7name = &clean_tabs_returns($room7name);
$room8name = &clean_tabs_returns($room8name);
$room9name = &clean_tabs_returns($room9name);
$room10name = &clean_tabs_returns($room10name);
$room11name = &clean_tabs_returns($room11name);
$room12name = &clean_tabs_returns($room12name);
$room13name = &clean_tabs_returns($room13name);
$room14name = &clean_tabs_returns($room14name);
$room15name = &clean_tabs_returns($room15name);
$room16name = &clean_tabs_returns($room16name);

$location = "done_adding";

## WRITE THE CONFIGURATION FILE WITH THE NEW CHANGES
	if ($cookie_ss_staff_id eq 'blitke') {
		open (CONFIGDATA,">/home/httpd/html/staff/reports/schedulerconfig-mueller");
		print CONFIGDATA "$schedulename\n"; # Schedule Title/Name
		print CONFIGDATA "$room1name\n";    # Room #1 Name
		print CONFIGDATA "$room1status\n";  # Room #1 On/Off
		print CONFIGDATA "$room2name\n";    # Room #2 Name
		print CONFIGDATA "$room2status\n";  # Room #2 On/Off
		print CONFIGDATA "$room3name\n";    # Room #3 Name
		print CONFIGDATA "$room3status\n";  # Room #3 On/Off
		print CONFIGDATA "$room4name\n";    # Room #4 Name
		print CONFIGDATA "$room4status\n";  # Room #4 On/Off
		print CONFIGDATA "$room5name\n";    # Room #5 Name
		print CONFIGDATA "$room5status\n";  # Room #5 On/Off
		print CONFIGDATA "$room6name\n";    # Room #6 Name
		print CONFIGDATA "$room6status\n";  # Room #6 On/Off
		print CONFIGDATA "$room7name\n";    # Room #7 Name
		print CONFIGDATA "$room7status\n";  # Room #7 On/Off
		print CONFIGDATA "$room8name\n";    # Room #8 Name
		print CONFIGDATA "$room8status\n";  # Room #8 On/Off
		print CONFIGDATA "$room9name\n";    # Room #9 Name
		print CONFIGDATA "$room9status\n";  # Room #9 On/Off
		print CONFIGDATA "$room10name\n";   # Room #10 Name
		print CONFIGDATA "$room10status\n"; # Room #10 On/Off
		print CONFIGDATA "$room11name\n";   # Room #11 Name
		print CONFIGDATA "$room11status\n"; # Room #11 On/Off
		print CONFIGDATA "$room12name\n";   # Room #12 Name
		print CONFIGDATA "$room12status\n"; # Room #12 On/Off
		print CONFIGDATA "$room13name\n";   # Room #13 Name
		print CONFIGDATA "$room13status\n"; # Room #13 On/Off
		print CONFIGDATA "$room14name\n";   # Room #14 Name
		print CONFIGDATA "$room14status\n"; # Room #14 On/Off
		print CONFIGDATA "$room15name\n";   # Room #15 Name
		print CONFIGDATA "$room15status\n"; # Room #15 On/Off
		print CONFIGDATA "$room16name\n";   # Room #16 Name
		print CONFIGDATA "$room16status\n"; # Room #16 On/Off
		close CONFIGDATA;
	} else {
		$errormessage .= "You do not have permission to make changes on the Admin page.";
	}
} ## END HANDLE OF ADMINISTRATION PAGE ACTIONS


###############################
# READ THE CONFIGURATION FILE #
###############################
open (CONFIGDATA,"/home/httpd/html/staff/reports/schedulerconfig-mueller");
my @config = <CONFIGDATA>;
  chop (@config);
  close CONFIGDATA;

# SET SAVED STATE FOR THE 10 ROOM On\Off SWITCHES (in the Administration Screen)
my $savestate1 = "SELECTED" if $config[2] eq 'Off';
my $savestate2 = "SELECTED" if $config[4] eq 'Off';
my $savestate3 = "SELECTED" if $config[6] eq 'Off';
my $savestate4 = "SELECTED" if $config[8] eq 'Off';
my $savestate5 = "SELECTED" if $config[10] eq 'Off';
my $savestate6 = "SELECTED" if $config[12] eq 'Off';
my $savestate7 = "SELECTED" if $config[14] eq 'Off';
my $savestate8 = "SELECTED" if $config[16] eq 'Off';
my $savestate9 = "SELECTED" if $config[18] eq 'Off';
my $savestate10 = "SELECTED" if $config[20] eq 'Off';
my $savestate11 = "SELECTED" if $config[22] eq 'Off';
my $savestate12 = "SELECTED" if $config[24] eq 'Off';
my $savestate13 = "SELECTED" if $config[26] eq 'Off';
my $savestate14 = "SELECTED" if $config[28] eq 'Off';
my $savestate15 = "SELECTED" if $config[30] eq 'Off';
my $savestate16 = "SELECTED" if $config[32] eq 'Off';



########################################################################
###  HANDLE THE DIFFERENT SCREENS (locations)  #########################
########################################################################

################################################################
# LOCATION: Temporary Screen to Say "DONE" while page reloads 
# (page reload was put in to fix the admin and schedule pages not updating)
################################################################
if (($location eq 'done_adding') || ($location eq 'done_deleting')) {
	if ($location eq 'done_adding') {
		print "<TR><TD><H2>Your item was scheduled.</H2><P>Please wait while you are returned to the calendar.\n";
	}
	if ($location eq 'done_deleting') {
		print "<TR><TD><H2>The changes were made.</H2><P>Please wait while you are returned to the calendar.\n";
		if ($debug eq '1') {
			print "Delete All?: $deletetimeall<BR>";
			print "Delete1: $delete2[1]<BR>";
			print "Delete2: $delete2[2]<BR>";
			print "Delete3: $delete2[3]<BR>";
			print "Delete4: $delete2[4]<BR>";
			print "Delete5: $delete2[5]<BR>";
			print "Delete6: $delete2[6]<BR>";
			print "Delete7: $delete2[7]<BR>";
			print "Delete8: $delete2[8]<BR>";
			print "Delete9: $delete2[9]<BR>";
			print "Delete10: $delete2[10]<BR>";
			print "Delete11: $delete2[11]<BR>";
			print "Delete12: $delete2[12]<BR>";
			print "Delete13: $delete2[13]<BR>";
			print "Delete14: $delete2[14]<BR>";
			print "Delete15: $delete2[15]<BR>";
			print "Delete16: $delete2[16]<BR>";
			print "Delete17: $delete2[17]<BR>";
			print "Delete18: $delete2[18]<BR>";
			print "Delete19: $delete2[19]<BR>";
			print "Delete20: $delete2[20]<BR>";
			print "Delete21: $delete2[21]<BR>";
			print "Delete22: $delete2[22]<BR>";
		}
	}
	print "</TD></TR></TABLE>";
}


################################################################
# LOCATION: Administration Screen                              #
################################################################
if ($location eq 'admin') {
print<<EOM;
<TR><TD>

<H2>Schedule Administration Page</H2>
This scheduling software allows you to set up scheduling charts for up to 10 items.  
You may turn individual schedule displays <strong>on</strong> or <strong>off</strong>, as well as change 
their names.
<P>
<strong>Only the Web Administrator can make changes here.</strong><P>
 Click here to return to $calmonthlabel $caldate, $calyear in the 
 <A HREF="/cgi-bin/mysql/scheduler-mueller.cgi?cd=$caldate&cm=$calmonth&cy=$calyear">Calendar Month/Day View</A>.<P><HR><P>
<P><strong>Schedule Name</strong> 
<INPUT TYPE="TEXT" NAME="sch" SIZE="40" VALUE="$schedulename">
<P>
<TABLE CELLPADDING=4>
<TR style="background-color:#ebebeb;"><TD><strong>Room Status</strong></TD>
    <TD><strong>Room Name</strong></TD></TR>
<TR><TD><SELECT NAME="room1status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate1>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room1name" SIZE="40" VALUE="$room1name"></TD></TR>
<TR><TD><SELECT NAME="room2status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate2>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room2name" SIZE="40" VALUE="$room2name"></TD></TR>
<TR><TD><SELECT NAME="room3status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate3>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room3name" SIZE="40" VALUE="$room3name"></TD></TR>
<TR><TD><SELECT NAME="room4status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate4>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room4name" SIZE="40" VALUE="$room4name"></TD></TR>
<TR><TD><SELECT NAME="room5status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate5>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room5name" SIZE="40" VALUE="$room5name"></TD></TR>
<TR><TD><SELECT NAME="room6status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate6>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room6name" SIZE="40" VALUE="$room6name"></TD></TR>
<TR><TD><SELECT NAME="room7status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate7>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room7name" SIZE="40" VALUE="$room7name"></TD></TR>
<TR><TD><SELECT NAME="room8status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate8>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room8name" SIZE="40" VALUE="$room8name"></TD></TR>
<TR><TD><SELECT NAME="room9status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate9>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room9name" SIZE="40" VALUE="$room9name"></TD></TR>
<TR><TD><SELECT NAME="room10status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate10>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room10name" SIZE="40" VALUE="$room10name"></TD></TR>
<TR><TD><SELECT NAME="room11status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate11>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room11name" SIZE="40" VALUE="$room11name"></TD></TR>
<TR><TD><SELECT NAME="room12status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate12>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room12name" SIZE="40" VALUE="$room12name"></TD></TR>
<TR><TD><SELECT NAME="room13status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate13>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room13name" SIZE="40" VALUE="$room13name"></TD></TR>
<TR><TD><SELECT NAME="room14status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate14>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room14name" SIZE="40" VALUE="$room14name"></TD></TR>
<TR><TD><SELECT NAME="room15status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate15>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room15name" SIZE="40" VALUE="$room15name"></TD></TR>
<TR><TD><SELECT NAME="room16status"><OPTION VALUE="On">On<OPTION VALUE="Off" $savestate16>Off</SELECT></TD>
    <TD><INPUT TYPE="TEXT" NAME="room16name" SIZE="40" VALUE="$room16name"></TD></TR>

</TABLE>
<p>
Note: Only the Administrator can make changes here</p>
EOM

## PRINT HIDDEN VARIABLES AND SUBMIT BUTTON
print<<EOM;
<div>
<INPUT TYPE="HIDDEN" NAME=reload2 VALUE="Yes">
<INPUT TYPE="HIDDEN" NAME=location VALUE="admin-go">
<INPUT TYPE="HIDDEN" NAME=a VALUE=$action>
<INPUT TYPE="HIDDEN" NAME=cy VALUE=$calyear>
<INPUT TYPE="HIDDEN" NAME=cm VALUE=$calmonth>
<INPUT TYPE="HIDDEN" NAME=cd VALUE=$caldate>
<INPUT TYPE="HIDDEN" NAME=p VALUE=$permissions>
<INPUT TYPE="SUBMIT" VALUE="Submit Changes">
</div>
</FORM>
<P>
If changes don't appear to take effect after you click the "Submit Changes" button, 
simply click the "Reload" button in your browser to update your screen.
</TD></TR></TABLE>
EOM


}  ## End Location: Administration Screen


################################################################
# START: LOCATION: Show (Monthly and Daily Calendar)
################################################################
my $x = 1;
if ($location eq 'show') {
my @reservations = ();
my @reservations1 = ();
my @reservations2 = ();


## START DRAWING TOP OF PAGE
print <<EOM;
<TR><TD VALIGN=TOP><h1>$schedulename for<BR><span style="color:#3874B3;">$calmonthlabel $caldate, $calyear</span></h1>
EOM

#print "<p class=\"alert\">$errormessage</p>" if ($errormessage ne '');
print "<p class=\"alert\">$errormessage2</p>" if ($errormessage2 ne '');

	if ($cookie_ss_session_id eq '') {
print<<EOM;
	<div class="resources">
	<TABLE>
	<TR><TD>
	<FORM ACTION="/cgi-bin/mysql/scheduler-mueller.cgi" METHOD="POST">
	<FONT COLOR=RED>Warning: You are NOT LOGGED IN.  You can view, but not schedule, reservations.</FONT>
	<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0 WIDTH="100%">
	<TR><TD VALIGN=TOP><strong>$label_user_id: </strong> <INPUT TYPE="text" NAME=logon_user SIZE=8 VALUE="$cookie_ss_staff_id"> 
			and <strong>Password</strong>: <INPUT TYPE=PASSWORD NAME=logon_pass SIZE=12>
			<INPUT TYPE="HIDDEN" NAME="cm" VALUE="$calmonth">
			<INPUT TYPE="HIDDEN" NAME="cd" VALUE="$caldate">
			<INPUT TYPE="HIDDEN" NAME="cy" VALUE="$calyear">
			<INPUT TYPE="HIDDEN" NAME=location VALUE=process_logon>
			<INPUT TYPE="SUBMIT" VALUE="login"></TD></TR>
	<TR><TD VALIGN="TOP" class="small">

			</TD></TR>
	<TR><TD>(Floorplan maps: 
			<A HREF="/staff/planning/hq_floorplans/sedl_hq_floor1.pdf">1st Floor</A> -
			<A HREF="/staff/planning/hq_floorplans/sedl_hq_floor2.pdf">2nd Floor</A> -
			<A HREF="/staff/planning/hq_floorplans/sedl_hq_floor3.pdf">3rd Floor</A> - 
			<A HREF="/staff/planning/hq_floorplans/sedl_hq_floorplans.pdf">All</a>)</TD></TR>
</TABLE>
</FORM>

	</TD></TR>
	</TABLE>
	</div>
EOM
	} else {
print<<EOM;

	<table>
	<tr><td>You are logged in as <strong><FONT COLOR=\"#755651\">$prettyname</FONT></strong>. 
			Click here to <A HREF="scheduler-mueller.cgi?location=logout">logout</A>.
			<br><br>
			
			Click to view floorplan maps:<br>
			<A HREF="/staff/planning/hq_floorplans/sedl_hq_floor1.pdf">1st Floor</A>, 
			<A HREF="/staff/planning/hq_floorplans/sedl_hq_floor2.pdf">2nd Floor</A>, 
			<A HREF="/staff/planning/hq_floorplans/sedl_hq_floor3.pdf">3rd Floor</A>,  
			<A HREF="/staff/planning/hq_floorplans/sedl_hq_floorplans.pdf">All</a>
			</td></tr>
	</table>
EOM
	}
print<<EOM;

</TD>
    <TD VALIGN=TOP>
    	<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
    	<TR><TD>
EOM

&draw_smallcalendar;

	sub draw_smallcalendar {
	## START DRAWING THE MINI CALENDAR

print<<EOM;
			<TABLE BORDER="1" WIDTH=120" CELLPADDING="3" CELLSPACING="0">
			<TR><TD COLSPAN=7 ALIGN=CENTER BGCOLOR="EBEBEB" NOWRAP> 
				<A HREF="scheduler-mueller.cgi?cm=$calmonthprevious&cy=$calyear&s=$cookie_ss_session_id"><IMG SRC="/images/bullets/youarehere.gif" BORDER=0 ALT="Previous Month"></A> 
				<strong>&nbsp; $calmonthlabel $calyear &nbsp;</strong> 
				<A HREF="/cgi-bin/mysql/scheduler-mueller.cgi?cm=$calmonthnext&cy=$calyear&s=$cookie_ss_session_id"><IMG SRC="/images/bullets/youarehere2.gif" BORDER=0 ALT="Next Month"></A> </TD>
EOM

	my $i = 1;
		while ($i < 37) {
			my $i1 = $i;
			my $i2 = $i+1;
			my $i3 = $i+2;
			my $i4 = $i+3;
			my $i5 = $i+4;
			my $i6 = $i+5;
			my $i7 = $i+6;


			######################################
			## START: SET RED LETTER DAYS for 2001
			######################################
			my $redletter1a = "";my $redletter2a = "";my $redletter3a = "";my $redletter4a = "";my $redletter5a = "";my $redletter6a = "";my $redletter7a = "";
			my $redletter1b = "";my $redletter2b = "";my $redletter3b = "";my $redletter4b = "";my $redletter5b = "";my $redletter6b = "";my $redletter7b = "";
#			
#			## RED LETTER DAY 9-3-2001
#			if (($calyear eq '2001') && ($calmonth eq '9') && ($calendar[$i1] eq '3')) {
#			$redletter1a = "</A><strong><FONT COLOR=RED>";$redletter1b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '9') && ($calendar[$i2] eq '3')) {
#			$redletter2a = "</A><strong><FONT COLOR=RED>";$redletter2b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '9') && ($calendar[$i3] eq '3')) {
#			$redletter3a = "</A><strong><FONT COLOR=RED>";$redletter3b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '9') && ($calendar[$i4] eq '3')) {
#			$redletter4a = "</A><strong><FONT COLOR=RED>";$redletter4b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '9') && ($calendar[$i5] eq '3')) {
#			$redletter5a = "</A><strong><FONT COLOR=RED>";$redletter5b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '9') && ($calendar[$i6] eq '3')) {
#			$redletter6a = "</A><strong><FONT COLOR=RED>";$redletter6b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '9') && ($calendar[$i7] eq '3')) {
#			$redletter7a = "</A><strong><FONT COLOR=RED>";$redletter7b = "</FONT></strong>";
#			}
#			
#			## RED LETTER DAY 11-22-2001
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i1] eq '22')) {
#			$redletter1a = "</A><strong><FONT COLOR=RED>";$redletter1b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i2] eq '22')) {
#			$redletter2a = "</A><strong><FONT COLOR=RED>";$redletter2b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i3] eq '22')) {
#			$redletter3a = "</A><strong><FONT COLOR=RED>";$redletter3b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i4] eq '22')) {
#			$redletter4a = "</A><strong><FONT COLOR=RED>";$redletter4b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i5] eq '22')) {
#			$redletter5a = "</A><strong><FONT COLOR=RED>";$redletter5b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i6] eq '22')) {
#			$redletter6a = "</A><strong><FONT COLOR=RED>";$redletter6b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i7] eq '22')) {
#			$redletter7a = "</A><strong><FONT COLOR=RED>";$redletter7b = "</FONT></strong>";
#			}
#			
#			## RED LETTER DAY 11-23-2001
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i1] eq '23')) {
#			$redletter1a = "</A><strong><FONT COLOR=RED>";$redletter1b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i2] eq '23')) {
#			$redletter2a = "</A><strong><FONT COLOR=RED>";$redletter2b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i3] eq '23')) {
#			$redletter3a = "</A><strong><FONT COLOR=RED>";$redletter3b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i4] eq '23')) {
#			$redletter4a = "</A><strong><FONT COLOR=RED>";$redletter4b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i5] eq '23')) {
#			$redletter5a = "</A><strong><FONT COLOR=RED>";$redletter5b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i6] eq '23')) {
#			$redletter6a = "</A><strong><FONT COLOR=RED>";$redletter6b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '11') && ($calendar[$i7] eq '23')) {
#			$redletter7a = "</A><strong><FONT COLOR=RED>";$redletter7b = "</FONT></strong>";
#			}
#			
#			## RED LETTER DAY 12-24-2001
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i1] eq '24')) {
#			$redletter1a = "</A><strong><FONT COLOR=RED>";$redletter1b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i2] eq '24')) {
#			$redletter2a = "</A><strong><FONT COLOR=RED>";$redletter2b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i3] eq '24')) {
#			$redletter3a = "</A><strong><FONT COLOR=RED>";$redletter3b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i4] eq '24')) {
#			$redletter4a = "</A><strong><FONT COLOR=RED>";$redletter4b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i5] eq '24')) {
#			$redletter5a = "</A><strong><FONT COLOR=RED>";$redletter5b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i6] eq '24')) {
#			$redletter6a = "</A><strong><FONT COLOR=RED>";$redletter6b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i7] eq '24')) {
#			$redletter7a = "</A><strong><FONT COLOR=RED>";$redletter7b = "</FONT></strong>";
#			}
#			
#			## RED LETTER DAY 12-25-2001
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i1] eq '25')) {
#			$redletter1a = "</A><strong><FONT COLOR=RED>";$redletter1b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i2] eq '25')) {
#			$redletter2a = "</A><strong><FONT COLOR=RED>";$redletter2b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i3] eq '25')) {
#			$redletter3a = "</A><strong><FONT COLOR=RED>";$redletter3b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i4] eq '25')) {
#			$redletter4a = "</A><strong><FONT COLOR=RED>";$redletter4b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i5] eq '25')) {
#			$redletter5a = "</A><strong><FONT COLOR=RED>";$redletter5b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i6] eq '25')) {
#			$redletter6a = "</A><strong><FONT COLOR=RED>";$redletter6b = "</FONT></strong>";
#			}
#			if (($calyear eq '2001') && ($calmonth eq '12') && ($calendar[$i7] eq '25')) {
#			$redletter7a = "</A><strong><FONT COLOR=RED>";$redletter7b = "</FONT></strong>";
#			}
			
			######################################
			## END: SET RED LETTER DAYS for 2001
			######################################
			
			my $showweek = "yes";
			   $showweek = "no" if (($calendar[$i1] eq '00') && ($calendar[$i7] eq '00'));

				$calmonth = "0$calmonth" if (length($calmonth) eq '1');

				$calendar[$i1] = "0$calendar[$i1]" if (length($calendar[$i1]) eq '1');
				$calendar[$i2] = "0$calendar[$i2]" if (length($calendar[$i2]) eq '1');
				$calendar[$i3] = "0$calendar[$i3]" if (length($calendar[$i3]) eq '1');
				$calendar[$i4] = "0$calendar[$i4]" if (length($calendar[$i4]) eq '1');
				$calendar[$i5] = "0$calendar[$i5]" if (length($calendar[$i5]) eq '1');
				$calendar[$i6] = "0$calendar[$i6]" if (length($calendar[$i6]) eq '1');
				$calendar[$i7] = "0$calendar[$i7]" if (length($calendar[$i7]) eq '1');
my $weekday1 = $calendar[$i1];
my $weekday2 = $calendar[$i2];
my $weekday3 = $calendar[$i3];
my $weekday4 = $calendar[$i4];
my $weekday5 = $calendar[$i5];
my $weekday6 = $calendar[$i6];
my $weekday7 = $calendar[$i7];

my $bgcolor1 = "";
my $bgcolor2 = "";
my $bgcolor3 = "";
my $bgcolor4 = "";
my $bgcolor5 = "";
my $bgcolor6 = "";
my $bgcolor7 = "";
	$bgcolor1 = "BGCOLOR=\"#B8D6F5\"" if (($calendar[$i1] eq $this_date) && ($calmonth eq $this_month));
	$bgcolor2 = "BGCOLOR=\"#B8D6F5\"" if (($calendar[$i2] eq $this_date) && ($calmonth eq $this_month));
	$bgcolor3 = "BGCOLOR=\"#B8D6F5\"" if (($calendar[$i3] eq $this_date) && ($calmonth eq $this_month));
	$bgcolor4 = "BGCOLOR=\"#B8D6F5\"" if (($calendar[$i4] eq $this_date) && ($calmonth eq $this_month));
	$bgcolor5 = "BGCOLOR=\"#B8D6F5\"" if (($calendar[$i5] eq $this_date) && ($calmonth eq $this_month));
	$bgcolor6 = "BGCOLOR=\"#B8D6F5\"" if (($calendar[$i6] eq $this_date) && ($calmonth eq $this_month));
	$bgcolor7 = "BGCOLOR=\"#B8D6F5\"" if (($calendar[$i7] eq $this_date) && ($calmonth eq $this_month));

			if (($showweek eq 'yes') && ($calendar[$i1] ne '0')) {
				if ($calendar[$i1] ne $caldate) {
					$weekday1 = "<A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?cm=$calmonth&cy=$calyear&cd=$calendar[$i1]&s=$cookie_ss_session_id\">$redletter1a$calendar[$i1]$redletter1b</A>";
				}
				if ($calendar[$i2] ne $caldate) {
					$weekday2 = "<A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?cm=$calmonth&cy=$calyear&cd=$calendar[$i2]&s=$cookie_ss_session_id\">$redletter1a$calendar[$i2]$redletter2b</A>";
				}
				if ($calendar[$i3] ne $caldate) {
					$weekday3 = "<A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?cm=$calmonth&cy=$calyear&cd=$calendar[$i3]&s=$cookie_ss_session_id\">$redletter1a$calendar[$i3]$redletter3b</A>";
				}
				if ($calendar[$i4] ne $caldate) {
					$weekday4 = "<A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?cm=$calmonth&cy=$calyear&cd=$calendar[$i4]&s=$cookie_ss_session_id\">$redletter1a$calendar[$i4]$redletter4b</A>";
				}
				if ($calendar[$i5] ne $caldate) {
					$weekday5 = "<A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?cm=$calmonth&cy=$calyear&cd=$calendar[$i5]&s=$cookie_ss_session_id\">$redletter1a$calendar[$i5]$redletter5b</A>";
				}
				if ($calendar[$i6] ne $caldate) {
					$weekday6 = "<A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?cm=$calmonth&cy=$calyear&cd=$calendar[$i6]&s=$cookie_ss_session_id\">$redletter1a$calendar[$i6]$redletter6b</A>";
				}
				if ($calendar[$i7] ne $caldate) {
					$weekday7 = "<A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?cm=$calmonth&cy=$calyear&cd=$calendar[$i7]&s=$cookie_ss_session_id\">$redletter1a$calendar[$i7]$redletter7b</A>";
				}

				print "<TR><TD WIDTH=\"20\" $bgcolor1><P class=small>";
				print "$weekday1" if $calendar[$i1] ne '00';
				print "</TD>\n";
				print "<TD WIDTH=\"20\" $bgcolor2><P class=small>";
				print "$weekday2" if $calendar[$i2] ne '00';
				print "</TD>\n";
				print "<TD WIDTH=\"20\" $bgcolor3><P class=small>";
				print "$weekday3" if $calendar[$i3] ne '00';
				print "</TD>\n";
				print "<TD WIDTH=\"20\" $bgcolor4><P class=small>";
				print "$weekday4" if $calendar[$i4] ne '00';
				print "</TD>\n";
				print "<TD WIDTH=\"20\" $bgcolor5><P class=small>";
				print "$weekday5" if $calendar[$i5] ne '00';
				print "</TD>\n";
				print "<TD WIDTH=\"20\" $bgcolor6><P class=small>";
				print "$weekday6" if $calendar[$i6] ne '00';
				print "</TD>\n";
				print "<TD WIDTH=\"20\" $bgcolor7><P class=small>";
				print "$weekday7" if $calendar[$i7] ne '00';
				print "</TD></TR>\n";
			} # END IF

		$i = $i +7;
		} # END WHILE

print "</TABLE>";
	} # END SUBROUTINE: draw_smallcalendar

print <<EOM;
</TD><TD>&nbsp;</TD><TD class=small>
Click on<BR>the small calendar<BR>to view other dates.</TD></TR>
</TABLE>
EOM

#print "</TD></TR></TABLE>";

##### LINK TO THE ADMIN PAGE, DISABLED
###print "<CENTER><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=admin\">Admin Screen</A></FONT></CENTER>";
print "</TD></TR><TR>";  # Finished printing left-side mini-calendar


## SEND THE DATABASE QUERY TO GET THE SCHEDULE FOR THE DATE BEING DISPLAYED
my $mcalmonth = "$calmonth";
my $mcaldate = "$caldate";

my $command = "select * from scheduler_mueller where showdate LIKE \'$calyear-$mcalmonth-$mcaldate\'";
$command .= " order by showdate";

print "$command" if $debug;

## OPEN THE DATABASE AND SEND THE QUERY
my $dbh = Mysql->connect('localhost', 'intranet', 'intranetuser', 'limited');
my $sth = $dbh->query($command);
my $num_matches = $sth->numrows;

## GET THE RESULTS OF THE QUERY
while (my @arr = $sth->fetchrow) {
    ($reservations[0], $reservations[1], $reservations[2], $reservations[3], $reservations[4], $reservations[5], $reservations[6], $reservations[7], $reservations[8], $reservations[9], $reservations[10], $reservations[11], $reservations[12], $reservations[13], $reservations[14], $reservations[15], $reservations[16], $reservations[17], $reservations[18], $reservations[19], $reservations[20], $reservations[21], $reservations[22], 
     $reservations[23], $reservations[24], $reservations[25], $reservations[26], $reservations[27], $reservations[28], $reservations[29], $reservations[30], $reservations[31], $reservations[32], $reservations[33], $reservations[34], $reservations[35], $reservations[36], $reservations[37], $reservations[38], $reservations[39], $reservations[40], $reservations[41], $reservations[42], $reservations[43], $reservations[44], 
     $reservations[45], $reservations[46], $reservations[47], $reservations[48], $reservations[49], $reservations[50], $reservations[51], $reservations[52], $reservations[53], $reservations[54], $reservations[55], $reservations[56], $reservations[57], $reservations[58], $reservations[59], $reservations[60], $reservations[61], $reservations[62], $reservations[63], $reservations[64], $reservations[65], $reservations[66], 
     $reservations[67], $reservations[68], $reservations[69], $reservations[70], $reservations[71], $reservations[72], $reservations[73], $reservations[74], $reservations[75], $reservations[76], $reservations[77], $reservations[78], $reservations[79], $reservations[80], $reservations[81], $reservations[82], $reservations[83], $reservations[84], $reservations[85], $reservations[86], $reservations[87], $reservations[88], 
     $reservations[89], $reservations[90], $reservations[91], $reservations[92], $reservations[93], $reservations[94], $reservations[95], $reservations[96], $reservations[97], $reservations[98], $reservations[99], $reservations[100], $reservations[101], $reservations[102], $reservations[103], $reservations[104], $reservations[105], $reservations[106], $reservations[107], $reservations[108], $reservations[109], $reservations[110], 
     $reservations[111], $reservations[112], $reservations[113], $reservations[114], $reservations[115], $reservations[116], $reservations[117], $reservations[118], $reservations[119], $reservations[120], $reservations[121], $reservations[122], $reservations[123], $reservations[124], $reservations[125], $reservations[126], $reservations[127], $reservations[128], $reservations[129], $reservations[130], $reservations[131], $reservations[132], 
     $reservations[133], $reservations[134], $reservations[135], $reservations[136], $reservations[137], $reservations[138], $reservations[139], $reservations[140], $reservations[141], $reservations[142], $reservations[143], $reservations[144], $reservations[145], $reservations[146], $reservations[147], $reservations[148], $reservations[149], $reservations[150], $reservations[151], $reservations[152], $reservations[153], $reservations[154], 
     $reservations[155], $reservations[156], $reservations[157], $reservations[158], $reservations[159], $reservations[160], $reservations[161], $reservations[162], $reservations[163], $reservations[164], $reservations[165], $reservations[166], $reservations[167], $reservations[168], $reservations[169], $reservations[170], $reservations[171], $reservations[172], $reservations[173], $reservations[174], $reservations[175], $reservations[176], 
     $reservations[177], $reservations[178], $reservations[179], $reservations[180], $reservations[181], $reservations[182], $reservations[183], $reservations[184], $reservations[185], $reservations[186], $reservations[187], $reservations[188], $reservations[189], $reservations[190], $reservations[191], $reservations[192], $reservations[193], $reservations[194], $reservations[195], $reservations[196], $reservations[197], $reservations[198], 
     $reservations[199], $reservations[200], $reservations[201], $reservations[202], $reservations[203], $reservations[204], $reservations[205], $reservations[206], $reservations[207], $reservations[208], $reservations[209], $reservations[210], $reservations[211], $reservations[212], $reservations[213], $reservations[214], $reservations[215], $reservations[216], $reservations[217], $reservations[218], $reservations[219], $reservations[220], 
     $reservations[221], $reservations[222], $reservations[223], $reservations[224], $reservations[225], $reservations[226], $reservations[227], $reservations[228], $reservations[229], $reservations[230], $reservations[231], $reservations[232], $reservations[233], $reservations[234], $reservations[235], $reservations[236], $reservations[237], $reservations[238], $reservations[239], $reservations[240], $reservations[241], $reservations[242], 
     $reservations[243], $reservations[244], $reservations[245], $reservations[246], $reservations[247], $reservations[248], $reservations[249], $reservations[250], $reservations[251], $reservations[252], $reservations[253], $reservations[254], $reservations[255], $reservations[256], $reservations[257], $reservations[258], $reservations[259], $reservations[260], $reservations[261], $reservations[262], $reservations[263], $reservations[264], 
     $reservations[265], $reservations[266], $reservations[267], $reservations[268], $reservations[269], $reservations[270], $reservations[271], $reservations[272], $reservations[273], $reservations[274], $reservations[275], $reservations[276], $reservations[277], $reservations[278], $reservations[279], $reservations[280], $reservations[281], $reservations[282], $reservations[283], $reservations[284], $reservations[285], $reservations[286], 
     $reservations[287], $reservations[288], $reservations[289], $reservations[290], $reservations[291], $reservations[292], $reservations[293], $reservations[294], $reservations[295], $reservations[296], $reservations[297], $reservations[298], $reservations[299], $reservations[300], $reservations[301], $reservations[302], $reservations[303], $reservations[304], $reservations[305], $reservations[306], $reservations[307], $reservations[308], 
     $reservations[309], $reservations[310], $reservations[311], $reservations[312], $reservations[313], $reservations[314], $reservations[315], $reservations[316], $reservations[317], $reservations[318], $reservations[319], $reservations[320], $reservations[321], $reservations[322], $reservations[323], $reservations[324], $reservations[325], $reservations[326], $reservations[327], $reservations[328], $reservations[329], $reservations[330], 
     $reservations[331], $reservations[332], $reservations[333], $reservations[334], $reservations[335], $reservations[336], $reservations[337], $reservations[338], $reservations[339], $reservations[340], $reservations[341], $reservations[342], $reservations[343], $reservations[344], $reservations[345], $reservations[346], $reservations[347], $reservations[348], $reservations[349], $reservations[350], $reservations[351], $reservations[352]) = @arr;
}

my $zz = 1;
while ($zz <= 352) {
   ($reservations1[$zz], $reservations2[$zz]) = split(/\t/,$reservations[$zz]);
	$zz++;
}

## START DRAWING THE DAILY CALENDAR ON THE RIGHT SIDE OF THE PAGE

print "<TD VALIGN=\"TOP\" COLSPAN=2>";




print<<EOM;
<TABLE BORDER="1" CELLPADDING="1" CELLSPACING="0" WIDTH="100%" ALIGN=CENTER>
<TR ALIGN=CENTER BGCOLOR="D1C8BC">
	<TD BGCOLOR="#EBEBEB">To schedule a room, click the room #/name.</TD>
EOM
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=1&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[1]</A><br>$room1capacity</TD>\n" if $room1status eq 'On';
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=3&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[3]</A><br>$room2capacity</TD>\n" if $room2status eq 'On';
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=5&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[5]</A><br>$room3capacity</TD>\n" if $room3status eq 'On';
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=7&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[7]</A><br>$room4capacity</TD>\n" if $room4status eq 'On';
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=9&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[9]</A><br>$room5capacity</TD>\n" if $room5status eq 'On';
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=11&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[11]</A><br>$room6capacity</TD>\n" if $room6status eq 'On';
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=13&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[13]</A><br>$room7capacity</TD>\n" if $room7status eq 'On';
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=15&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[15]</A><br>$room8capacity</TD>\n" if $room8status eq 'On';
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=17&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[17]</A><br>$room9capacity</TD>\n" if $room9status eq 'On';
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=19&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[19]</A><br>$room10capacity</TD>\n" if $room10status eq 'On';   
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=21&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[21]</A><br>$room11capacity</TD>\n" if $room11status eq 'On';   
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=23&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[23]</A><br>$room12capacity</TD>\n" if $room12status eq 'On';   

print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=25&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[25]</A><br>$room13capacity</TD>\n" if $room13status eq 'On';   
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=27&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[27]</A><br>$room14capacity</TD>\n" if $room14status eq 'On';   
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=29&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[29]</A><br>$room15capacity</TD>\n" if $room15status eq 'On';   
print "<TD BGCOLOR=\"#EBEBEB\" valign=\"top\"><A HREF=\"/cgi-bin/mysql/scheduler-mueller.cgi?location=schedule&cw=31&cd=$caldate&cm=$calmonth&cy=$calyear&s=$cookie_ss_session_id\">$config[31]</A><br>$room16capacity</TD>\n" if $room16status eq 'On';   

print "</TR>";


while ($x < 23) {
print "<TR ALIGN=CENTER>";
print "<TD BGCOLOR=\"#DEEBFF\" NOWRAP><P class=small>$times[$x]<BR>$times[$x+1]</TD>";

$reservations1[$x] = "\&nbsp\;" if $reservations1[$x] eq '';
$reservations1[$x+22] = "\&nbsp\;" if $reservations1[$x+22] eq '';
$reservations1[$x+44] = "\&nbsp\;" if $reservations1[$x+44] eq '';
$reservations1[$x+66] = "\&nbsp\;" if $reservations1[$x+66] eq '';
$reservations1[$x+88] = "\&nbsp\;" if $reservations1[$x+88] eq '';
$reservations1[$x+110] = "\&nbsp\;" if $reservations1[$x+110] eq '';
$reservations1[$x+132] = "\&nbsp\;" if $reservations1[$x+132] eq '';
$reservations1[$x+154] = "\&nbsp\;" if $reservations1[$x+154] eq '';
$reservations1[$x+176] = "\&nbsp\;" if $reservations1[$x+176] eq '';
$reservations1[$x+198] = "\&nbsp\;" if $reservations1[$x+198] eq '';
$reservations1[$x+220] = "\&nbsp\;" if $reservations1[$x+220] eq '';
$reservations1[$x+242] = "\&nbsp\;" if $reservations1[$x+242] eq '';
$reservations1[$x+264] = "\&nbsp\;" if $reservations1[$x+264] eq '';
$reservations1[$x+286] = "\&nbsp\;" if $reservations1[$x+286] eq '';
$reservations1[$x+308] = "\&nbsp\;" if $reservations1[$x+308] eq '';
$reservations1[$x+330] = "\&nbsp\;" if $reservations1[$x+330] eq '';

print "<TD VALIGN=TOP><P class=small>$reservations1[$x]<BR>$reservations1[$x+1]</TD>\n" if $room1status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+22]<BR>$reservations1[$x+22+1]</TD>\n" if $room2status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+44]<BR>$reservations1[$x+44+1]</TD>\n" if $room3status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+66]<BR>$reservations1[$x+66+1]</TD>\n" if $room4status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+88]<BR>$reservations1[$x+88+1]</TD>\n" if $room5status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+110]<BR>$reservations1[$x+110+1]</TD>\n" if $room6status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+132]<BR>$reservations1[$x+132+1]</TD>\n" if $room7status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+154]<BR>$reservations1[$x+154+1]</TD>\n" if $room8status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+176]<BR>$reservations1[$x+176+1]</TD>\n" if $room9status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+198]<BR>$reservations1[$x+198+1]</TD>\n" if $room10status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+220]<BR>$reservations1[$x+220+1]</TD>\n" if $room11status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+242]<BR>$reservations1[$x+242+1]</TD>\n" if $room12status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+264]<BR>$reservations1[$x+264+1]</TD>\n" if $room13status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+286]<BR>$reservations1[$x+286+1]</TD>\n" if $room14status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+308]<BR>$reservations1[$x+308+1]</TD>\n" if $room15status eq 'On';
print "<TD VALIGN=TOP><P class=small>$reservations1[$x+330]<BR>$reservations1[$x+330+1]</TD>\n" if $room16status eq 'On';

print "</TR>\n";
$x = $x +1;
$x = $x +1;
}

print <<EOM;
</TABLE>


<P>
<H2>Guidelines for staff who schedule SEDL meeting rooms.</H2>
<P>
<strong>Meetings Allowed</strong>
	<UL>
	<LI>The meeting must be an academic, non-profit association, or an organization that staff member is a member of or affiliated with. The function must not be for personal staff member gain (I.e., Avon, Jewelry, Mary Kay, Tupperware, etc.)
	<LI>You are responsible for ensuring the meeting does not violate SEDL policies.  
		For instance, no alcohol is allowed, as that would violate SEDL's policy of a drug and alcohol-free workplace.
	<LI>Refreshments are OK, but for non-SEDL meetings, you must make arrangements that do not involve SEDL.
	</UL>
<P>
<strong>Meeting Participants</strong>
	<UL>
	<LI>You are responsible for ensuring guests sign-in at the security desk on the first floor, if the meeting is after normal working hours.
	<LI>Please do not disturb staff members who might be working late.
	</UL>
<P>
<strong>Meeting Room Care and Maintenance</strong>
	<UL>
	<LI>You are responsible for locking up the SEDL facility/meeting room after the meeting is finished.
	<LI>No tech support is available after hours, unless pre-arranged.
	<LI>Building A/C is turned off at 7 or 8 pm.
	</UL>
<P>
<strong>Computer Training Lab Guidelines</strong>
	<UL>
	<LI>Drinks with screw-on lids are ok. Open top drinks or cans are prohibited.
	<LI>Users are responsible to log out from the computers when the session is complete.
	<LI>Users are responsible to save all session documents to CD or file share them to other computers before logging out (if they want to keep these documents). Otherwise, all session documents should be deleted at end of session.
	<LI>Technical issues with lab computers should be reported to network specialist, Cliff Pierron at ext. 269.
	</UL>
<br><br><br>
<P>
<strong>Usage Reports:</strong> Click here for a 
	<ul>
	<li>report of <a href="http://www.sedl.org/cgi-bin/mysql/staff/roomscheduler_report.cgi">usage of SEDL rooms for the past year</A></li>
	<li>report of <a href="http://www.sedl.org/cgi-bin/mysql/staff/roomscheduler_report_future.cgi">reservations of SEDL rooms for the coming year</A></li>
	</ul>

</TD></TR></TABLE>
EOM
}
################################################################
# END: LOCATION: Show (Monthly and Daily Calendar)
################################################################



################################################################
## START: LOCATION: Schedule (an Event)
################################################################
if ($location eq 'schedule') {
my @reservations = (); # HOLD FULL RESERVATION DETAIL
my @reservations1 = (); # RESERVED BY PROGRAM
my @reservations2 = (); # RESERVED BY NOTES
my @reservations3 = (); # RESERVED BY USER


## SEND THE DATABASE QUERY TO GET THE SCHEDULE FOR THE DATE BEING DISPLAYED
my $mcalmonth = "$calmonth";
my $mcaldate = "$caldate";

my $command = "select * from scheduler_mueller where showdate LIKE \'$calyear-$mcalmonth-$mcaldate\'";
$command .= " order by showdate";

print "$command" if $debug;


## OPEN THE DATABASE AND SEND THE QUERY
my $dbh = Mysql->connect('localhost', 'intranet', 'intranetuser', 'limited');
my $sth = $dbh->query($command);
my $num_matches = $sth->numrows;

## GET THE RESULTS OF THE QUERY
while (my @arr = $sth->fetchrow) {
    ($reservations[0], $reservations[1], $reservations[2], $reservations[3], $reservations[4], $reservations[5], $reservations[6], $reservations[7], $reservations[8], $reservations[9], $reservations[10], $reservations[11], $reservations[12], $reservations[13], $reservations[14], $reservations[15], $reservations[16], $reservations[17], $reservations[18], $reservations[19], $reservations[20], $reservations[21], $reservations[22], 
     $reservations[23], $reservations[24], $reservations[25], $reservations[26], $reservations[27], $reservations[28], $reservations[29], $reservations[30], $reservations[31], $reservations[32], $reservations[33], $reservations[34], $reservations[35], $reservations[36], $reservations[37], $reservations[38], $reservations[39], $reservations[40], $reservations[41], $reservations[42], $reservations[43], $reservations[44], 
     $reservations[45], $reservations[46], $reservations[47], $reservations[48], $reservations[49], $reservations[50], $reservations[51], $reservations[52], $reservations[53], $reservations[54], $reservations[55], $reservations[56], $reservations[57], $reservations[58], $reservations[59], $reservations[60], $reservations[61], $reservations[62], $reservations[63], $reservations[64], $reservations[65], $reservations[66], 
     $reservations[67], $reservations[68], $reservations[69], $reservations[70], $reservations[71], $reservations[72], $reservations[73], $reservations[74], $reservations[75], $reservations[76], $reservations[77], $reservations[78], $reservations[79], $reservations[80], $reservations[81], $reservations[82], $reservations[83], $reservations[84], $reservations[85], $reservations[86], $reservations[87], $reservations[88], 
     $reservations[89], $reservations[90], $reservations[91], $reservations[92], $reservations[93], $reservations[94], $reservations[95], $reservations[96], $reservations[97], $reservations[98], $reservations[99], $reservations[100], $reservations[101], $reservations[102], $reservations[103], $reservations[104], $reservations[105], $reservations[106], $reservations[107], $reservations[108], $reservations[109], $reservations[110], 
     $reservations[111], $reservations[112], $reservations[113], $reservations[114], $reservations[115], $reservations[116], $reservations[117], $reservations[118], $reservations[119], $reservations[120], $reservations[121], $reservations[122], $reservations[123], $reservations[124], $reservations[125], $reservations[126], $reservations[127], $reservations[128], $reservations[129], $reservations[130], $reservations[131], $reservations[132], 
     $reservations[133], $reservations[134], $reservations[135], $reservations[136], $reservations[137], $reservations[138], $reservations[139], $reservations[140], $reservations[141], $reservations[142], $reservations[143], $reservations[144], $reservations[145], $reservations[146], $reservations[147], $reservations[148], $reservations[149], $reservations[150], $reservations[151], $reservations[152], $reservations[153], $reservations[154], 
     $reservations[155], $reservations[156], $reservations[157], $reservations[158], $reservations[159], $reservations[160], $reservations[161], $reservations[162], $reservations[163], $reservations[164], $reservations[165], $reservations[166], $reservations[167], $reservations[168], $reservations[169], $reservations[170], $reservations[171], $reservations[172], $reservations[173], $reservations[174], $reservations[175], $reservations[176], 
     $reservations[177], $reservations[178], $reservations[179], $reservations[180], $reservations[181], $reservations[182], $reservations[183], $reservations[184], $reservations[185], $reservations[186], $reservations[187], $reservations[188], $reservations[189], $reservations[190], $reservations[191], $reservations[192], $reservations[193], $reservations[194], $reservations[195], $reservations[196], $reservations[197], $reservations[198], 
     $reservations[199], $reservations[200], $reservations[201], $reservations[202], $reservations[203], $reservations[204], $reservations[205], $reservations[206], $reservations[207], $reservations[208], $reservations[209], $reservations[210], $reservations[211], $reservations[212], $reservations[213], $reservations[214], $reservations[215], $reservations[216], $reservations[217], $reservations[218], $reservations[219], $reservations[220], 
     $reservations[221], $reservations[222], $reservations[223], $reservations[224], $reservations[225], $reservations[226], $reservations[227], $reservations[228], $reservations[229], $reservations[230], $reservations[231], $reservations[232], $reservations[233], $reservations[234], $reservations[235], $reservations[236], $reservations[237], $reservations[238], $reservations[239], $reservations[240], $reservations[241], $reservations[242], 
     $reservations[243], $reservations[244], $reservations[245], $reservations[246], $reservations[247], $reservations[248], $reservations[249], $reservations[250], $reservations[251], $reservations[252], $reservations[253], $reservations[254], $reservations[255], $reservations[256], $reservations[257], $reservations[258], $reservations[259], $reservations[260], $reservations[261], $reservations[262], $reservations[263], $reservations[264], 
     $reservations[265], $reservations[266], $reservations[267], $reservations[268], $reservations[269], $reservations[270], $reservations[271], $reservations[272], $reservations[273], $reservations[274], $reservations[275], $reservations[276], $reservations[277], $reservations[278], $reservations[279], $reservations[280], $reservations[281], $reservations[282], $reservations[283], $reservations[284], $reservations[285], $reservations[286], 
     $reservations[287], $reservations[288], $reservations[289], $reservations[290], $reservations[291], $reservations[292], $reservations[293], $reservations[294], $reservations[295], $reservations[296], $reservations[297], $reservations[298], $reservations[299], $reservations[300], $reservations[301], $reservations[302], $reservations[303], $reservations[304], $reservations[305], $reservations[306], $reservations[307], $reservations[308], 
     $reservations[309], $reservations[310], $reservations[311], $reservations[312], $reservations[313], $reservations[314], $reservations[315], $reservations[316], $reservations[317], $reservations[318], $reservations[319], $reservations[320], $reservations[321], $reservations[322], $reservations[323], $reservations[324], $reservations[325], $reservations[326], $reservations[327], $reservations[328], $reservations[329], $reservations[330], 
     $reservations[331], $reservations[332], $reservations[333], $reservations[334], $reservations[335], $reservations[336], $reservations[337], $reservations[338], $reservations[339], $reservations[340], $reservations[341], $reservations[342], $reservations[343], $reservations[344], $reservations[345], $reservations[346], $reservations[347], $reservations[348], $reservations[349], $reservations[350], $reservations[351], $reservations[352]) = @arr;
}

my $zz = 1;
while ($zz <= 352) {
   ($reservations1[$zz], $reservations2[$zz], $reservations3[$zz]) = split(/\t/,$reservations[$zz]);
	$zz++;
}


my $z1 = "1";
my $z2 = "";
   $z2 = "0" if ($changewhat eq '1');
   $z2 = "22" if ($changewhat eq '3');
   $z2 = "44" if ($changewhat eq '5');
   $z2 = "66" if ($changewhat eq '7');
   $z2 = "88" if ($changewhat eq '9');
   $z2 = "110" if ($changewhat eq '11');
   $z2 = "132" if ($changewhat eq '13');
   $z2 = "154" if ($changewhat eq '15');
   $z2 = "176" if ($changewhat eq '17');
   $z2 = "198" if ($changewhat eq '19');
   $z2 = "220" if ($changewhat eq '21');
   $z2 = "242" if ($changewhat eq '23');
   $z2 = "264" if ($changewhat eq '25');
   $z2 = "286" if ($changewhat eq '27');
   $z2 = "308" if ($changewhat eq '29');
   $z2 = "330" if ($changewhat eq '31');

my $calmonthlabel = '';
   $calmonthlabel = 'January' if $calmonth eq '01';
   $calmonthlabel = 'February' if $calmonth eq '02';
   $calmonthlabel = 'March' if $calmonth eq '03';
   $calmonthlabel = 'April' if $calmonth eq '04';
   $calmonthlabel = 'May' if $calmonth eq '05';
   $calmonthlabel = 'June' if $calmonth eq '06';
   $calmonthlabel = 'July' if $calmonth eq '07';
   $calmonthlabel = 'August' if $calmonth eq '08';
   $calmonthlabel = 'September' if $calmonth eq '09';
   $calmonthlabel = 'October' if $calmonth eq '10';
   $calmonthlabel = 'November' if $calmonth eq '11';
   $calmonthlabel = 'December' if $calmonth eq '12';

## START DRAWING TOP OF PAGE
print <<EOM;
<TR><TD VALIGN="TOP">
		<TABLE BORDER="0">
		<TR><TD VALIGN=TOP><H1>Reservations for<BR><FONT COLOR="#3874B3">$config[$changewhat]</FONT><BR> 
				for the day of<BR></FONT><FONT COLOR="#D65240">$calmonthlabel $caldate, $calyear</FONT></H1>
EOM


print<<EOM;
<em>(Click on a calendar date to get back to the <a href="scheduler-mueller.cgi">list of rooms</a>.)</em>
			</TD>
			<TD VALIGN=TOP><IMG SRC="/images/spacer.gif" HEIGHT="1" WIDTH="10" ALT=""></TD>
			<TD VALIGN=TOP>
EOM

## SHOW SMALL CALENDAR
&draw_smallcalendar;

my $roomtype = "Room";
print<<EOM;
			</TD>
		</TR>
		</TABLE>
<P>
	<div class="resources">

	You are logged on as <strong><FONT COLOR=\"#755651\">$prettyname</FONT></strong>. 
	Click here to <A HREF="scheduler-mueller.cgi?location=logout">logout</A>.
<H2>Program/Department Scheduling the $roomtype</H2>
	
			<SELECT NAME="scheduledby">
			<OPTION VALUE="Other">(choose one)</option>
			<OPTION>Admin.
			<OPTION>AFC
			<OPTION>Comm.
			<OPTION>Devel.
			<OPTION>DRP
			<OPTION>Exec.
			<OPTION>ISP
			<OPTION>RE
			<OPTION>Other
			</SELECT>
			
			<H2>Comment about the use of the $roomtype</H2>
EOM
#	if (($changewhat eq '17') || ($changewhat eq '19')) {
#		print " <SPAN class=small>(If reserving a parking space, please enter the name of the visitor.\)</SPAN><BR>";
#	}
print<<EOM;
			<INPUT TYPE="TEXT" NAME="comment" SIZE="40"><BR>
			<SPAN class=small>(60 character limit)</SPAN>
<P>
</P>
</div>
		
			<H2>Select the time(s) to reserve:</H2>
			
			Click the checkbox next to the time(s)
			you\'d like to schedule, or use the boxes below 
			to reservation the entire day.

			<TABLE CELLPADDING=2>
			<TR><TD COLSPAN=2></TD></TR>
			<TR><TD VALIGN=top><INPUT TYPE=CHECKBOX NAME=\"settimeall\" VALUE=\"Yes\"></TD>
				<TD>Reserve the entire day.</TD></TR>
			<TR><TD VALIGN=top><INPUT TYPE=CHECKBOX NAME=\"deletetimeall\" VALUE=\"Yes\"></TD>
			<TD>Delete your reservations for this day.
			</TD></TR>
			</TABLE>
		
			<INPUT TYPE=SUBMIT VALUE="Submit Reservation or Changes">
			<P><BR><BR>
			<em>Note: You can only delete entries that you scheduled.
			<P>
			If you need to delete another user's scheduled event, please contact that user.</em>
	</TD>
	<TD VALIGN="TOP">



	<TABLE BORDER="1" CELLPADDING="2" CELLSPACING="0">
	<TR><TD BGCOLOR="#EBEBEB">Time</TD>
		<TD BGCOLOR="#EBEBEB">Click<BR>to<BR>Reserve</TD>
		<TD BGCOLOR="#EBEBEB">Reserved<BR>by<BR>Program</TD>
		<TD BGCOLOR="#EBEBEB">Notes/ Contact</TD>
		<TD BGCOLOR="#EBEBEB">Delete</TD></TR>
EOM
	while ($z1 < 23) {
		print "<TR>";
		print "<TD NOWRAP><font face=\"arial, helvetica\" size=-1 nowrap>$times[$z1]</font></TD><TD>";
		print "<INPUT TYPE=CHECKBOX NAME=\"settime$z1\" VALUE=\"Yes\">" if ($reservations[($z1 + $z2)] eq '');
		print "\&nbsp\;</TD>\n";
		print "<TD><font face=\"arial, helvetica\" size=-1 nowrap>$reservations1[($z1 + $z2)]</font></TD><TD><font face=\"arial, helvetica\" size=-1 nowrap>$reservations2[($z1 + $z2)]<P>$reservations3[($z1 + $z2)]</font></TD>";
		print "<TD>";
		print "<INPUT TYPE=CHECKBOX NAME=\"delete$z1\" VALUE=\"Yes\t$reservations3[($z1 + $z2)]\">" if ($reservations[($z1 + $z2)] ne '');
		print "<INPUT TYPE=HIDDEN NAME=adelete$z1 VALUE=\"Yes\t$reservations3[($z1 + $z2)]\">\n";
		print "</TD></TR>\n";
		#print "RESERVATION1: $reservations1[($z1 + $z2)]<BR>RESERVATION2: reservations2[($z1 + $z2)]<BR>RESERVATION3: reservations3[($z1 + $z2)]\n" if $debug;
		$z1++
	}



## PRINT HIDDEN VARIABLES AND SUBMIT BUTTON
print<<EOM;
	</TABLE><BR>

<INPUT TYPE=HIDDEN NAME=reload VALUE="Yes">
<INPUT TYPE=HIDDEN NAME=cw VALUE="$changewhat">
<INPUT TYPE=HIDDEN NAME=location VALUE="schedule-go">
<INPUT TYPE=HIDDEN NAME=a VALUE=$action>
<INPUT TYPE=HIDDEN NAME=cy VALUE=$calyear>
<INPUT TYPE=HIDDEN NAME=cm VALUE=$calmonth>
<INPUT TYPE=HIDDEN NAME=cd VALUE=$caldate>
<INPUT TYPE=HIDDEN NAME=p VALUE=$permissions>
<INPUT TYPE=SUBMIT VALUE="Submit Reservation or Changes"><P> (There may be a short delay while your reservation is processed.)



	</TD></TR>
	</TABLE>
	</FORM>
EOM
}
################################################################
## END: LOCATION: Schedule (an Event)
################################################################




################################################################################
## PRINT HTML FOOTER
################################################################################
#print "Configuration<BR>$config[0]<BR>$config[1]<BR>$config[2]<BR>$config[3]<BR>$config[4]<BR>$config[5]<BR>$config[6]<BR>$config[7]<BR>$config[8]<BR>$config[9]<BR>$config[10]<BR>$config[11]<BR>$config[12]<BR>$config[13]<BR>$config[14]<BR>$config[15]<BR>$config[16]<BR>$config[17]<BR>$config[18]<BR>$config[19]<BR>$config[20]<BR>$config[21]<BR>" if $debug;
if ($debug == 1) {
	print "CALYEAR $calyear<P>CALMONTH $calmonth<P>CALDATE $caldate<P>Small Calendar Info<BR>$calendar[0]<BR>$calendar[1]<BR>$calendar[2]<BR>$calendar[3]<BR>$calendar[4]<BR>$calendar[5]<BR>$calendar[6]<BR>$calendar[7]<BR>$calendar[8]<BR>$calendar[9]<BR>$calendar[10]<BR>$calendar[11]<BR>$calendar[12]<BR>$calendar[13]<BR>$calendar[14]<BR>$calendar[15]<BR>$calendar[16]<BR>$calendar[17]<BR>$calendar[18]<BR>$calendar[19]<BR>$calendar[20]<BR>$calendar[21]<BR>$calendar[22]<BR>$calendar[23]<BR>$calendar[24]<BR>$calendar[25]<BR>$calendar[26]<BR>$calendar[27]<BR>$calendar[28]<BR>$calendar[29]<BR>$calendar[30]<BR>$calendar[31]<BR>$calendar[32]<BR>$calendar[33]<BR>$calendar[34]<BR>$calendar[35]<BR>$calendar[36]<BR>$calendar[37]<BR>$calendar[38]<BR>$calendar[39]<BR>$calendar[40]<BR>$calendar[41]<BR>$calendar[42]<BR>";
	print "RESERVATIONS<P> $reservations[1]<BR>$reservations[2]<BR>$reservations[3]<BR>$reservations[4]<BR>$reservations[5]<BR>";
	print"<P><FONT COLOR=\"#BBAF95\">ONE WEEK FROM NOW WOULD BE: $date_mysql_oneweek_fromnow</FONT> ";
}
#print "<P>CALMONTH = $calmonth<BR>THISMONTH = $this_month";
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
   $dirtyitem =~ s/Ô/\\Ô/g;
   $dirtyitem =~ s/Õ/\\Õ/g;
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


####################################################################
## START: MAKE SURE CALENDAR DATA RECORD EXISTS IN DATABASE
####################################################################
sub make_sure_date_record_exists {
	my $date_to_check = $_[0];
	
	my $command = "select showdate from scheduler_mueller where showdate LIKE '$date_to_check'";
	my $dbh = Mysql->connect('localhost', 'intranet', 'intranetuser', 'limited');
	my $sth = $dbh->query($command);
	my $num_matches = $sth->numrows;
#print "<br><font color=oprange>CHECKING TO SEE IF DATE EXISTS: $num_matches</font>";
	if ($num_matches == 0) {
		my $command = "INSERT INTO scheduler_mueller VALUES ('$date_to_check', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')";
		my $dbh = Mysql->connect('localhost', 'intranet', 'intranetuser', 'limited');
		my $sth = $dbh->query($command);
#print "<br><font color=oprange>INSERTING DATE: $command</font>";
	}
}

####################################################################
## END: MAKE SURE CALENDAR DATA RECORD EXISTS IN DATABASE
####################################################################






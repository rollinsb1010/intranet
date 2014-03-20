#!/usr/bin/perl

#####################################################################################################
# Copyright 2004 by Southwest Educational Development Laboratory
#
# Written by Brian Litke 06-07-2004
#
# This script is triggered by a FileMaker query and responds with an XML file with the most recent leave report
# The Script expects one variable with the userid of the person whose leave report is being grabbed 
#####################################################################################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;
#use Encode::Encoding; # HANDLES DATA ENCODING

################################################
## END: LOAD PERL MODULES USED BY THIS SCRIPT
################################################


################################################
## START: GRAB VARIABLES PASSED FROM FILEMAKER
################################################
my $query = new CGI;
################################################
## END: GRAB VARIABLES PASSED FROM FILEMAKER
################################################


###################################################
## START: GRAB ACCOUNTING SYSTEM NAME FOR THIS USER
###################################################
my $this_timesheetname = "";

my $command = "select timesheetname from staff_profiles";
my $dsn = "DBI:mysql:database=intranet;host=localhost";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;
	while (my @arr = $sth->fetchrow) {
		($this_timesheetname) = @arr;
	}
###################################################
## END: GRAB ACCOUNTING SYSTEM NAME FOR THIS USER
###################################################

print header;




###################################################
# START: GRAB THE MOST RECENT UPDATE DATE
###################################################
my $command = "select leavelastupdated from staffleavereport order by leavelastupdated";
my $dsn = "DBI:mysql:database=test;host=localhost";
my $dbh = DBI->connect($dsn);
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
#my $num_matches = $sth->rows;
my $latest_leavelastupdated = "";
	while (my @arr = $sth->fetchrow) {
		($latest_leavelastupdated) = @arr;
	}
###################################################
# END: GRAB THE MOST RECENT UPDATE DATE
###################################################


#print "AAA";

###################################################
# START: GRAB THE MOST RECENT LEAVE REPORT TOTALS
###################################################
my $command = "select staff_profiles.userid, staffleavereport.* 
				from intranet.staff_profiles, test.staffleavereport 
				where staffleavereport.timesheetname = staff_profiles.timesheetname
					  AND staffleavereport.leavelastupdated LIKE '$latest_leavelastupdated'";

#print "<P>$command";
my $dsn = "DBI:mysql:database=intranet;host=localhost";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

my $leavelastupdated_nodashes = "";

## TELL THRE BROWSER THAT TXT/HTML IS COMING
print<<EOM;
<?xml version="1.0" encoding="utf-8"?>
<StaffLeaveReport>
EOM
	while (my @arr = $sth->fetchrow) {
		my ($userid, $uniqueid, $timesheetname, $ssn, $manager, $departmentid, $vacaccrualfactor, $vacaccruedtodate, $vacearnedcurrent, $vacusedcurrent, $vacusedtodate, $vacbalance, $sickaccrualfactor, $sickaccruedtodate, $sickearnedcurrent, $sickusedcurrent, $sickusedtodate, $sickbalance, $persaccrualfactor, $persaccruedtodate, $persearnedcurrent, $persusedcurrent, $persusedtodate, $persbalance, $leavelastupdated) = @arr;
			$leavelastupdated_nodashes = $leavelastupdated;
			$leavelastupdated_nodashes =~ s/\-//g;


###################################################
# START: PRINT XML RESPONSE
###################################################
print<<EOM;
	<people>
		<StaffID>$userid</StaffID>
		<LastUpdated>$leavelastupdated_nodashes</LastUpdated>

		<SickEarnedCurrent>$sickearnedcurrent</SickEarnedCurrent>
		<SickUsedCurrent>$sickusedcurrent</SickUsedCurrent>
		<SickBalance>$sickbalance</SickBalance>

		<VacEarnedCurrent>$vacearnedcurrent</VacEarnedCurrent>
		<VacUsedCurrent>$vacusedcurrent</VacUsedCurrent>
		<VacBalance>$vacbalance</VacBalance>
		
		<PersUsedCurrent>$persusedcurrent</PersUsedCurrent>
		<PersBalance>$persbalance</PersBalance>
	</people>
EOM
###################################################
# END: PRINT XML RESPONSE
###################################################




	} # END DB QUERY LOOP
print "</StaffLeaveReport>";
###################################################
# END: GRAB THE MOST RECENT LEAVE REPORT TOTALS
###################################################

## SAMPLE ENCODING CALL - THIS DOESN"T WORK (PROBABLY BECAUSE THE PERL MODULE NOT FORUND??
#my $encoded_userid = encode("utf8", $userid);


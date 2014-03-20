#!/usr/bin/perl

use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
$|=1;
my $query = new CGI;
my $show_onscreen = $query->param('show_onscreen');


my %staff_profiles_names;
my %leave_report_names;

my %userid;
my %start_date;

my $list_of_missing_acct_sys_name = "";
my $list_of_missing_acct_sys_name2 = "";


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

$htmlhead .= "<TABLE CELLPADDING=\"15\"><TR><TD>";
$htmltail = "</td></tr></table>$htmltail";
###########################################
# END: GRAB SEDL HEADER AND FOOTER HTML #
###########################################



if ($show_onscreen eq 'yes') {
print header;
print<<EOM;
<html>
<title>Report: Check for staff who may not be able to access their leave report</title>
$htmlhead
<h1>Report: Check for staff who may not be able to access their leave report</h1>
EOM
}




###########################################################################
## START: GRAB SET OF STAFF ACCOUNTING SYSTEM NAMES FROM SATFF PROFILES DB
###########################################################################
	my $command = "select userid, timesheetname, start_date from staff_profiles";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	if ($show_onscreen eq 'yes') {
		print "<p class=\"info\">Checking the \"staff_profiles\" database for users.  Found $num_matches</p>";
	}
	while (my @arr = $sth->fetchrow) {
		my ($userid, $timesheetname, $start_date) = @arr;
		$timesheetname =~ s/\'//gi;
		$staff_profiles_names{$timesheetname} = "yes";
		$list_of_missing_acct_sys_name = "\n$userid (Start date: $start_date)" if ($timesheetname eq '');
		$list_of_missing_acct_sys_name2 = "<li>$userid (Start date: $start_date)</li>" if ($timesheetname eq '');
		$userid{$timesheetname} = $userid;
		$start_date{$timesheetname} = $start_date;
	} # END DB QUERY LOOP
###########################################################################
## END: GRAB SET OF STAFF ACCOUNTING SYSTEM NAMES FROM SATFF PROFILES DB
###########################################################################




###########################################################################
## START: GRAB SET OF STAFF LEAVE REPORT NAMES FROM SATFF PROFILES DB
###########################################################################
	my $command = "select timesheetname from staffleavereport group by timesheetname";
	my $dsn = "DBI:mysql:database=test;host=localhost";
	my $dbh = DBI->connect($dsn, "", "");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	if ($show_onscreen eq 'yes') {
		print "<p class=\"info\">Checking the \"leavereport\" database for users.  Found $num_matches</p>";
	}
	while (my @arr = $sth->fetchrow) {
		my ($timesheetname) = @arr;
		$timesheetname =~ s/\'//gi;
		$leave_report_names{$timesheetname} = "yes";
	} # END DB QUERY LOOP
###########################################################################
## END: GRAB SET OF STAFF LEAVE REPORT NAMES FROM SATFF PROFILES DB
###########################################################################




#############################################################################################
## START: COMPARE LISTS AND MAKE A LIST OF STAFF PROFILES WITH NO MATCHING ACCT. SYSTEM NAME
#############################################################################################
my $acct_syst_name = "";
my $list_of_problems = "";
my $list_of_problems2 = "";
	foreach $acct_syst_name (sort keys %staff_profiles_names) {
	    # DOES THE ACCT SYS NAME APPEAR IN THE LAVE REPORT DB?
	    if (($leave_report_names{$acct_syst_name} ne 'yes') && ($acct_syst_name ne '')) {
			$list_of_problems .= "\n$acct_syst_name";
			$list_of_problems2 .= "<li>$acct_syst_name $userid{$acct_syst_name} (Start date: $start_date{$acct_syst_name})</li>";
		} else {
			$list_of_problems2 .= "<li><font color=\"999999\">NO PROBLEM: $acct_syst_name $userid{$acct_syst_name} (Start date: $start_date{$acct_syst_name})</font></li>";
		} # END IF
	} # ED FOREACH
#############################################################################################
## END: COMPARE LISTS AND MAKE A LIST OF STAFF PROFILES WITH NO MATCHING ACCT. SYSTEM NAME
#############################################################################################
	if ($show_onscreen eq 'yes') {
print<<EOM;
<ol>
$list_of_problems2
</ol>
EOM
	}

#############################################################################################
## START: IF EITHER ERROR LIST CONTAINS ERRORS, SEND AN E-MAIL
#############################################################################################
if (($list_of_problems ne '') || ($list_of_missing_acct_sys_name ne '')) {
	my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
	my $recipient = 'blitke@sedl.org';
	my $fromaddr = 'webmaster@sedl.org';

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Warning - Staff ID with no accounting system name

Dear SEDL Webmaster,
EOM

if ($list_of_problems ne '') {
	if ($show_onscreen eq 'yes') {
print<<EOM;

<h2>PROBLEMS:</h2>
The following staff do not appear to have Leave Report records coded to their accounting system name:
<ul>
$list_of_problems2
</ul>
EOM
	}
print NOTIFY<<EOM;

PROBLEMS:
The following staff do not appear to have Leave Report records coded to their accounting system name:
$list_of_problems

EOM
}

if ($list_of_problems2 ne '') {
print NOTIFY<<EOM;

MISSING ACCOUNTING SYSTEM IDs:
The following staff do not have their accounting system name entrered in the Staff Profiles DB yet:
$list_of_missing_acct_sys_name

EOM

	if ($show_onscreen eq 'yes') {
print<<EOM;

<h2>MISSING ACCOUNTING SYSTEM IDs:</h2>
The following staff do not have their accounting system name entrered in the Staff Profiles DB yet:<br>
<ul>
$list_of_missing_acct_sys_name2
</ul>

EOM
	}
}


print NOTIFY<<EOM;
This e-mail was sent by an automated sender (set up by Brian Litke at SEDL):


EOM
close(NOTIFY);
}

#############################################################################################
## END: IF EITHER ERROR LIST CONTAINS ERRORS, SEND AN E-MAIL
#############################################################################################
	if ($show_onscreen eq 'yes') {
		print "$htmltail";
	}



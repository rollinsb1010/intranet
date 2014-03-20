#!/usr/bin/perl

use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
$|=1;
my $query = new CGI;
my $filename = $query->param('filename');
my $trigger_email = $query->param('trigger_email');
   $trigger_email = 'yes';
my $sent_to = ""; # remember list of staff who we sent an e-mail to

#############################################
## START: LOAD PERL MODULES
#############################################
## THIS IS A PERL MODULE THAT FORMATS NUMBERS
use Number::Format;
# EXAMPLE OF USAGE
# my $this_number
#	my $x = new Number::Format;
#	$this_number = $x->format_number($this_number, 2, 2);

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

####################################
## END: GET THE CURRENT DATE INFO
####################################


	if ($debug eq '1') {
##############################################################
## START THE OUTPUT BY CREATING THE HTML HEADER AND PAGE TITLE
##############################################################
print $query-> header("text/html");
print <<EOM;
<HTML><head><title>SEDL Staff - Database Upload Page</title>
<link rel="stylesheet" href="/css/style-td11-p11h14.css"></head>
<body bgcolor="#FFFFFF">
<H2>Staf Members Who Are Very Close to Maximum Vacation Hours</H2>
<p>For vacation, staff are flagged if the are within 26 hours of a 160 maximum or if they are within 40 hours of a 240 maximum.
</p>
<p>
During the month of November, this page will also show warnings for people with a personal leave balance greater than 0.
</p>
<TABLE BORDER="1" CELLPADDING="2" cellspacing="0">
<tr>
	<td><strong>#</strong></td>
	<td><strong>Staff Member</strong></td>
EOM
	if ($month == 11) {
print<<EOM;
	<td><strong>Personal</strong></td>
EOM
	}
print<<EOM;
	<td><strong>Vacation</strong></td>
</tr>
EOM
	} # END IF
	####################################################################
	## START: SEND E-MAIL TO CFO NOTIFYING THAT LEAVE REPORT HAS BEEN UPDATED 
	####################################################################
	my $command = "select staff_profiles.firstname, staff_profiles.lastname, staff_profiles.email, staffleavereport.* 
				from intranet.staff_profiles, test.staffleavereport 
				WHERE (staff_profiles.timesheetname= staffleavereport.timesheetname )
				order by staffleavereport.timesheetname, staffleavereport.leavelastupdated DESC";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#	print "<p>COMMAND: $command <BR>MATCHES: $num_matches</p>";
	my $sent_thisuser_message = "no";
	my $timesheetname_previous = "";
	my $counter = "0";
	while (my @arr = $sth->fetchrow) {
		my ($firstname, $lastname, $email, $uniqueid, $timesheetname, $ssn, $manager, $departmentid, $vacaccrualfactor, $vacaccruedtodate, $vacearnedcurrent, $vacusedcurrent, $vacusedtodate, $vacbalance, $sickaccrualfactor, $sickaccruedtodate, $sickearnedcurrent, $sickusedcurrent, $sickusedtodate, $sickbalance, $persaccrualfactor, $persaccruedtodate, $persearnedcurrent, $persusedcurrent, $persusedtodate, $persbalance, $nophotorequested) = @arr;
			$nophotorequested = &date2standard($nophotorequested);

			if ($timesheetname ne $timesheetname_previous) {
				$sent_thisuser_message = "no";
			}
			# START: DETERMINE WHETHER TO WARN ABOUT BEING CLOSE TO MAXIMUM HOURS
			my $needto_process_this_staffmember_personal = "";
			my $needto_process_this_staffmember_vacation = "";
			my $maxhours = $vacaccrualfactor * 160;
			my $howclose = $maxhours - $vacbalance;
			   $howclose = &format_number ($howclose, "2", "no");
				$needto_process_this_staffmember_vacation = "yes" if (($howclose < 26) && ($maxhours eq '160') && ($maxhours ne '0'));
				$needto_process_this_staffmember_vacation = "yes" if (($howclose < 40) && ($maxhours eq '240') && ($maxhours ne '0'));
			# END: DETERMINE WHETHER TO WARN ABOUT BEING CLOSE TO MAXIMUM HOURS

				if ($month == 11) {
					$needto_process_this_staffmember_personal = "yes" if ($persbalance ne '0.00');
				} #END IF
			if ($sent_thisuser_message ne 'yes') {
				$sent_thisuser_message = "yes";
				if (($needto_process_this_staffmember_personal eq 'yes') || ($needto_process_this_staffmember_vacation eq 'yes')) {
$counter++;

					my $persbalance_label = $persbalance;
						if ($persbalance_label eq '0.00') {
							$persbalance_label = "<span style=\"color:#999999;\">n/a</span>";
						} else {
							$persbalance_label = "<span style=\"color:red;\">$persbalance</span>";
						} # END IF

	if ($debug eq '1') {
print<<EOM;
<TR><TD>$counter</TD>
	<TD>$timesheetname</TD>
EOM
		if ($month == 11) {
print<<EOM;
	<td>$persbalance_label</td>
EOM
		} # end if
print<<EOM;
	<TD>
EOM
		if ($needto_process_this_staffmember_vacation eq 'yes') {
print<<EOM;
<FONT COLOR="RED">$howclose</FONT> to the maximum hours $maxhours of vacation time.
EOM
		} # end if
print<<EOM;
	</TD>
	</TR>
EOM
	} # END IF
					my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
					my $recipient = $email;
#					   $recipient = 'blitke@sedl.org';
					my $fromaddr = 'webmaster@sedl.org';

my $warning_type = "";
my $warning_bodytext = "";
	if ($needto_process_this_staffmember_vacation eq 'yes') {
		$warning_bodytext ="WARNING: As of $nophotorequested, you are within $howclose hours of maximum accrual of $maxhours vacation hours.";
		$warning_type = "You are close to your maximum vacation hour accrual";
	} # END IF

	if ($needto_process_this_staffmember_personal eq 'yes') {
		$warning_type = "You have $persbalance unused personal leave hours that WILL BE LOST if you do not use them by the end of November.";
		if ($warning_bodytext ne '') {
$warning_bodytext .= "

";
		}
		$warning_bodytext .= "You have $persbalance unused personal leave hours that WILL BE LOST if you do not use them by the end of November.";
	} # END IF
				if ($trigger_email eq 'yes') {

$sent_to .= "<li>$recipient</li>";
open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Warning - $warning_type

Dear $firstname $lastname,

$warning_bodytext


VACATION:
------------------------------
Accrued ETD:		$vacaccruedtodate
Earned Current: 	$vacearnedcurrent
Used Current:		$vacusedcurrent
Used ETD		$vacusedtodate
Balance			$vacbalance


SICK:
------------------------------
Accrued ETD:		$sickaccruedtodate
Earned Current: 	$sickearnedcurrent
Used Current:		$sickusedcurrent
Used ETD		$sickusedtodate
Balance			$sickbalance


PERSONAL:
------------------------------
Proj Ann. Accr. ETD:	$persaccruedtodate
Used Current:		$persusedcurrent
Used ETD		$persusedtodate
Balance			$persbalance




This data was uploaded to the intranet on this date: $nophotorequested

This e-mail was sent by an automated sender (set up by Brian Litke at SEDL):


EOM
close(NOTIFY);
					}
				} # END IF CLOSE TO MAX VACATION HOURS
			} # END IF
		$timesheetname_previous = $timesheetname;
	} # END DB QUERY LOOP

	if ($debug eq '1') {
print<<EOM;
</TABLE>
<p>
Click here to <A HREF=\"leavereport.cgi\">return to your personal leave report</A>.
</p>
<p>
Click here to <A HREF=\"leavereport-warning.cgi?trigger_email=yes\">trigger an e-mail to these staff</A> 
to warn them about the possibility of lost vacation hours.
</p>
EOM
		if ($sent_to ne '') {
print<<EOM;
The system just sent e-mails to these staff:
<br>
<br>
<ul>
$sent_to
</ul>
EOM
		} # END IF
	} # END IF

####################################################################
## START: ROUND NUMBER TO X PLACES, WITH OR WITHOUT COMMAS
####################################################################
# EXAMPLE OF USAGE
# $num = &format_number($num, "0","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
sub format_number {
	my $number_unformatted = $_[0];
	   $number_unformatted = 0 if ($number_unformatted eq '');
	   $number_unformatted =~ s/\,//g; # REMOVE COMMA IF ALREADY EXISTS 
	my $decimal_places = $_[1];
		$decimal_places = "2" if ($decimal_places eq '');
	my $commas_included = $_[2];

	my $x = new Number::Format;
	my $number_formatted = $x->format_number($number_unformatted, $decimal_places, $decimal_places);
		if ($commas_included ne 'yes') {
			$number_formatted =~ s/\,//g;
		}
	return($number_formatted);
}
####################################################################
## END: ROUND NUMBER TO X PLACES, WITH OR WITHOUT COMMAS
####################################################################


#################################################################
## START SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################
sub date2standard {
my $date2transform = $_[0];
my $thisyear = substr($date2transform, 0, 4);
my $thismonth = substr($date2transform, 4, 2);
my $thisdate = substr($date2transform, 6, 2);
   $date2transform = "$thismonth\/$thisdate\/$thisyear";
   $date2transform = "" if $thismonth eq '';
	return($date2transform);
}
#################################################################
## END SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################


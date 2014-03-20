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




##############################################################
## START THE OUTPUT BY CREATING THE HTML HEADER AND PAGE TITLE
##############################################################
print header;
print <<EOM;
<HTML>
<head>
<title>SEDL Staff - Database Upload Page</title>
<link rel="stylesheet" href="/staff/includes/staff2006.css">



<style type="text/css">
<!--
body {  	background-color:#ffffff;}
-->
</style>


</head>
<body bgcolor="#FFFFFF">
<table bgcolor="#ffffff">
<tr><td>

EOM

if ($filename eq '') {
	print "<p class=\"alert\">The database filename was not specified.  Please <a href=\"/staff/personnel/dataupload.cgi\">try again</a>.</p>";
} else {

print<<EOM;
<H2>Database Update Complete</H2>
<p class=\"info\">The online database called <strong>\"$filename\"</strong> was updated successfully!
<br>
Please check the Web page for the online database you updated, and verify that the data is being presented as expected.
<br>
Call Brian Litke at ext. 6529 if you have questions.
</p>

<H3>Please wait until your database's name appears below before proceeding...</H3>

EOM

my $command = "delete from $filename";
my $command2 = "load data LOCAL infile \'/home/httpd/html/temp/$filename\' replace into table $filename";

############################################################################################
## START: HANDLE STAFF LEAVE REPORT DATABASE
############################################################################################
if ($filename eq 'LEAVEDAT.TXT') {
my $counter = "1";
my $last_color = "BLUE";
my $this_color = "";
print "<FONT COLOR=PURPLE>Starting to process staff leave data: </FONT>";
	open(TEMPLEAVEDATFILE,"</home/httpd/html/temp/LEAVEDAT.TXT");
		while (<TEMPLEAVEDATFILE>) {
			my $this_record = $_;
				$this_record =~ s/\"//g; # REMOVA ANY DOUBLE QUOTES (AROUND USER NAMES)
				$this_record =~ s/\,/\t/g; # CHANGE COMMAS TO TABS

			my ($timesheetname, $ssn, $manager, $departmentid, $vacaccrualfactor, $vacaccruedtodate, $vacearnedcurrent, $vacusedcurrent, $vacusedtodate, $vacbalance, $sickaccrualfactor, $sickaccruedtodate, $sickearnedcurrent, $sickusedcurrent, $sickusedtodate, $sickbalance, $persaccrualfactor, $persaccruedtodate, $persearnedcurrent, $persusedcurrent, $persusedtodate, $persbalance, $leavelastupdated) = split(/\t/,$this_record);
			
			# BACKSLASH USER NAME, WHICH MAY CONTAIN APOSTRAPHE
			$timesheetname =~ s/\'//g;
			my $this_uniqueid = "$timesheetname $leavelastupdated";
			
			## UPDATE DATABASE WITH THIS RECORD
			my $command = "REPLACE into staffleavereport VALUES ('$this_uniqueid', '$timesheetname', '$ssn', '$manager', '$departmentid', '$vacaccrualfactor', '$vacaccruedtodate', '$vacearnedcurrent', '$vacusedcurrent', '$vacusedtodate', '$vacbalance', '$sickaccrualfactor', '$sickaccruedtodate', '$sickearnedcurrent', '$sickusedcurrent', '$sickusedtodate', '$sickbalance', '$persaccrualfactor', '$persaccruedtodate', '$persearnedcurrent', '$persusedcurrent', '$persusedtodate', '$persbalance', '$leavelastupdated')";
			$this_color = "GREEN" if ($last_color eq 'BLUE');
			$this_color = "BLUE" if ($last_color eq 'GREEN');
			print "<FONT COLOR=$this_color>$counter\. $timesheetname</FONT>, ";
			my $dsn = "DBI:mysql:database=test;host=localhost";
			my $dbh = DBI->connect($dsn);
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
			print "<p>$command<br>Matches: $num_matches</p>";
		$counter++;
		$last_color = $this_color;
		}
	close(TEMPLEAVEDATFILE);

if ($filename eq 'NEVER-SEND') {
	####################################################################
	## START: SEND E-MAIL TO CFO NOTIFYING THAT LEAVE REPORT HAS BEEN UPDATED 
	####################################################################
	my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
	my $recipient = 'brian.litke@sedl.org, arnold.kriegel@sedl.org, lori.foradory@sedl.org, stuart.ferguson@sedl.org';
#		$recipient = 'blitke@sedl.org';
	my $fromaddr = 'arnold.kriegel@sedl.org';

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: SEDL Staff Leave Report has been updated

To Arnold, Stuart, Lori, and Brian,

The SEDL Staff Leave Report has been updated.

You can access it on the intranet at: http://www.sedl.org/staff/personnel/leavereport.cgi

This e-mail was auto-generated when the Leave Report data file was uploaded.

EOM
close(NOTIFY);
	################################################################################
	## END: SEND E-MAIL TO CFO NOTIFYING THAT LEAVE REPORT HAS BEEN UPDATED 
	################################################################################




	####################################################################
	## START: SEND E-MAIL TO CFO NOTIFYING THAT LEAVE REPORT HAS BEEN UPDATED 
	####################################################################
	my $command = "select * from staffleavereport where timesheetname like 'MEADOWS MARY LOU' order by leavelastupdated DESC";
	my $dsn = "DBI:mysql:database=test;host=localhost";
	my $dbh = DBI->connect($dsn);
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	my $sent_marylou_message = "no";
	while (my @arr = $sth->fetchrow) {
		my ($uniqueid, $timesheetname, $ssn, $manager, $departmentid, $vacaccrualfactor, $vacaccruedtodate, $vacearnedcurrent, $vacusedcurrent, $vacusedtodate, $vacbalance, $sickaccrualfactor, $sickaccruedtodate, $sickearnedcurrent, $sickusedcurrent, $sickusedtodate, $sickbalance, $persaccrualfactor, $persaccruedtodate, $persearnedcurrent, $persusedcurrent, $persusedtodate, $persbalance, $filelastupdated) = @arr;

			# START: DETERMINE WHETHER TO WARN ABOUT BEING CLOSE TO MAXIMUM HOURS
			my $showhowclose = "";
			my $maxhours = $vacaccrualfactor * 160;
			my $howclose = $maxhours - $vacbalance;
				$showhowclose = "yes" if (($howclose < 26) && ($maxhours eq '160') && ($maxhours ne '0'));
				$showhowclose = "yes" if (($howclose < 40) && ($maxhours eq '240') && ($maxhours ne '0'));
			# END: DETERMINE WHETHER TO WARN ABOUT BEING CLOSE TO MAXIMUM HOURS

			if ($sent_marylou_message ne 'yes') {
				my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
				my $recipient = 'lmeadows@sedl.org, brian.litke@sedl.org';
#				   $recipient = 'blitke@sedl.org';
				my $fromaddr = 'arnold.kriegel@sedl.org';

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Your Staff Leave Report

From an automated sender (set up by Brian Litke at SEDL):

Mary Lou, Here is your Leave Report data:

VACATION:
------------------------------
Accrued ETD:		$vacaccruedtodate
Earned Current: 	$vacearnedcurrent
Used Current:		$vacusedcurrent
Used ETD		$vacusedtodate
Balance			$vacbalance

EOM

if ($showhowclose ne '') {
print NOTIFY "WARNING: You are within ";
printf NOTIFY "%3.2f\n", $howclose;
print NOTIFY " hours of maximum accrual of $maxhours\n";
}
print NOTIFY <<EOM;

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



This data was uploaded to the intranet on this date: $filelastupdated

EOM
close(NOTIFY);
				$sent_marylou_message = "yes"; # SET FLAG SO ONLY ONE E-MAIL IS SENT
			} # END IF
	} # END DB QUERY LOOP
} # END DISABLE EMAIL




	################################################################################
	## END: SEND E-MAIL TO CFO NOTIFYING THAT LEAVE REPORT HAS BEEN UPDATED 
	################################################################################

	if ($filename eq 'LEAVEDAT.TXT') {
		## DELETE UPLOAD FILE, BECAUSE IT CONTAINS SSN#
		open (ERASEFILE,">/home/httpd/html/temp/LEAVEDAT.TXT");
		print ERASEFILE "\n";
		close(ERASEFILE);

		## DELETE TEMPORARY STAFF RECORDS FROM LEAVE REPORT DATABASE
		my $command_remove_temps = "delete from staffleavereport where vacaccrualfactor like '0.00'";
		my $dsn = "DBI:mysql:database=test;host=localhost";
		my $dbh = DBI->connect($dsn);
		my $sth = $dbh->prepare($command_remove_temps) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;

		## DELETE BLANK RECORDS FROM LEAVE REPORT DATABASE
		my $command_remove_blanks = "delete from staffleavereport where vacaccrualfactor like ''";
		my $dsn = "DBI:mysql:database=test;host=localhost";
		my $dbh = DBI->connect($dsn);
		my $sth = $dbh->prepare($command_remove_blanks) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
	}

############################################################################################
} # END: HANDLE STAFF LEAVE REPORT DATABASE
############################################################################################



##############################################################################
## START: CLEAN DATA AND SET UPLOAD COMMANDS FOR FINANCE.TXT AND FINANCC.TXT
##############################################################################
if ($filename eq 'FINANCE.TXT') {
	my $leavedatholder = "";
	open(TEMPLEAVEDATFILE,"</home/httpd/html/temp/FINANCE.TXT");
	while (<TEMPLEAVEDATFILE>) {
		$leavedatholder .= $_;
	}
	close(TEMPLEAVEDATFILE);

	$leavedatholder =~ s/\n\n/\n/g; $leavedatholder =~ s/\r\r/\r/g; # REMOVE EMPTY LINES

	open (TEMPLEAVEDATFILE,">/home/httpd/html/temp/FINANCE2.TXT");
	print TEMPLEAVEDATFILE "$leavedatholder";
	close (TEMPLEAVEDATFILE);

	$command = "delete from oftsbudgets_newmonth";
	$command2 = "load data LOCAL infile '/home/httpd/html/temp/FINANCE2.TXT' INTO TABLE oftsbudgets_newmonth FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED by '\"'";
} # END IF FILENAME = FINANCE.TXT


if ($filename eq 'FINANCC.TXT') {
	my $leavedatholder = "";
	open(TEMPLEAVEDATFILE,"</home/httpd/html/temp/FINANCC.TXT");
		while (<TEMPLEAVEDATFILE>) {
			$leavedatholder .= $_;
		}
	close(TEMPLEAVEDATFILE);

	$leavedatholder =~ s/\n\n/\n/g; $leavedatholder =~ s/\r\r/\r/g; # REMOVE EMPTY LINES

	open (TEMPLEAVEDATFILE,">/home/httpd/html/temp/FINANCC2.TXT");
	print TEMPLEAVEDATFILE "$leavedatholder";
	close (TEMPLEAVEDATFILE);

	$command = "delete from oftsbudgetsfy_newmonth";
	$command2 = "load data LOCAL infile '/home/httpd/html/temp/FINANCC2.TXT' INTO TABLE oftsbudgetsfy_newmonth FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED by '\"'";
} # END IF FILENAME = FINANCC.TXT

if ($filename eq 'CK.TXT') {
	print "<P>Does filename equal CK.TXT? \"$filename\"";
	$command = "delete from oftsbudgetsck";
	$command2 = "load data LOCAL infile '/home/httpd/html/temp/CK.TXT' INTO TABLE oftsbudgetsck";
} # END IF FILENAME = PR.TXT

if ($filename eq 'JV.TXT') {
	print "<P>Does filename equal JV.TXT? \"$filename\"";
	$command = "delete from oftsbudgetsjv";
	$command2 = "load data LOCAL infile '/home/httpd/html/temp/JV.TXT' INTO TABLE oftsbudgetsjv";
} # END IF FILENAME = PR.TXT

if ($filename eq 'PR.TXT') {
	print "<P>Does filename equal PR.TXT? \"$filename\"";
	$command = "delete from oftsbudgetspr";
	$command2 = "load data LOCAL infile '/home/httpd/html/temp/PR.TXT' INTO TABLE oftsbudgetspr";
} # END IF FILENAME = PR.TXT

##############################################################################
## END: HANDLE FINANCIAL/BUDGET REPORT DATABASE
##############################################################################


##############################################################################
## START: EXECUTE DATABASE COMMANDS TO INSERT DATA FROM THE UPLOAD FILE
##############################################################################
if ($filename eq 'positionvacancies') {
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "root", "google");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
	print "<p class=\"info\">Removing previous position vacancies from site...</p>";

	print "$command2";
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "root", "google");
		my $sth = $dbh->prepare($command2) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
	print "<br>Adding $num_matches new Records from $filename...";
} elsif (($filename eq 'FINANCE.TXT') || ($filename eq 'FINANCC.TXT') || ($filename eq 'CK.TXT') || ($filename eq 'JV.TXT') || ($filename eq 'PR.TXT')) {
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "root", "google");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		print "<p class=\"info\">Deleting $num_matches old Records in the temporary upload database.<br>COMMAND: $command";

		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "root", "google");
		my $sth = $dbh->prepare($command2) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		print "<br>Adding $num_matches new Records from $filename to the temporary upload database.<br>COMMAND2: $command2";
} else {
	if (($filename ne 'staffleavereport') && ($filename ne 'LEAVEDAT.TXT')) {
		my $dsn = "DBI:mysql:database=test;host=localhost";
		my $dbh = DBI->connect($dsn);
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_deletions = $sth->rows;
		print "<p class=\"info\">Deleting $num_matches_deletions old Records...";

		my $dsn = "DBI:mysql:database=test;host=localhost";
		my $dbh = DBI->connect($dsn);
		my $sth = $dbh->prepare($command2) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		print "<br>Adding $num_matches new Records from $filename...";
	}
} # END IF/ELSE
##  PRINT DEBUG STATEMENTS
if ($debug ne '') {
	print "<br><br><strong>Commands sent to database were:</strong><br>(1) $command<br><br>(2) $command2</p>\n";
}
##############################################################################
## END: EXECUTE DATABASE COMMANDS TO INSERT DATA FROM THE UPLOAD FILE
##############################################################################




###########################################################################
## START: RUN EXTRA AUTOMATION FSCRIPTS
###########################################################################
if (($filename eq "FINANCE.TXT") || ($filename eq "FINANCC.TXT")) {
	# GENERATE SUBTOTAL ENTRIES IN CONTRACT-FINANCIAL RPEORT AND FY-FINANCIAL REPORT
	print "<p class=\"info\">Triggering automation routine at the location: /staff/personnel/budgets-automation.cgi<br>";
	system ("/home/httpd/html/staff/personnel/budgets-automation.cgi");
	print "<p class=\"info\">Ending automation in /staff/personnel/budgets-automation.cgi</p>";
}


if ($filename eq "CK.TXT") {
# GENERATE SUBTOTAL ENTRIES IN CONTRACT-FINANCIAL RPEORT AND FY-FINANCIAL REPORT
#system ("/home/httpd/html/staff/personnel/budgetsckjvpr-automation.cgi");
print "<p class=\"info\">Click here to <A HREF=\"/staff/personnel/budgetsckjvpr-automation.cgi?actionfile=CK\">start CK automation</A></p>";
}
if ($filename eq "JV.TXT") {
print "<p class=\"info\">Click here to <A HREF=\"/staff/personnel/budgetsckjvpr-automation.cgi?actionfile=JV\">start JV automation</A></p>";
}

if ($filename eq "PR.TXT") {
print "<p class=\"info\">Click here to <A HREF=\"/staff/personnel/budgetsckjvpr-automation.cgi?actionfile=PR\">start PR automation</A></p>";
}
###########################################################################
## END: RUN EXTRA AUTOMATION FSCRIPTS
###########################################################################




##############################################################################################
## START: PRINT A LIST OF LINKS TO DATABASES TO USERS CAN CHECK TO SEE IF RECORDS WERE UPDATED
##############################################################################################
print "<hr><p class=\"info\"><strong>Use the link below to check on the database you updated</strong><br>" if (($filename eq 'positionvacancies') && ($filename eq 'LEAVEDAT.TXT') && ($filename =~ 'FINANC'));
print "- <A HREF=/staff/personnel/leavereport.cgi>Staff Leave Report</A>" if ($filename eq "LEAVEDAT.TXT");
print "- <A HREF=/staff/personnel/budgets.cgi>Staff Budgets Database</A>" if (($filename eq "FINANCE.TXT") || ($filename eq "FINANCC.TXT"));
print "- <A HREF=/about/jobs.html>Position Vacancies</A>" if ($filename eq 'positionvacancies');

if (($filename eq "FINANCE.TXT") || ($filename eq "FINANCC.TXT") || ($filename eq "LEAVEDAT.TXT")) {
print<<EOM;
</p>
Click here to return to the <A HREF="dataupload.cgi">OFTS Database Upload Page</A>.
EOM
}
##############################################################################################
## END: PRINT A LIST OF LINKS TO DATABASES TO USERS CAN CHECK TO SEE IF RECORDS WERE UPDATED
##############################################################################################


} # END IF/ELSE


########################################################################
##  DATABASE "QUOTE" FUNCTION TO CLEAN VARIABLES BEFORE USING WITH DB ## 
########################################################################

#my $baddata = "<P>Brian's dog was here.<P>";
#my $dbh = Mysql->connect('localhost','test','root','google');
#my $cleandata = $dbh->quote($baddata);
#print "<P>$baddata<P>$cleandata<P>";


print<<EOM;
</td></tr>
</table>
</body>
</html>
EOM

############################
##  CLEAN EMAIL SUBROUTNE ## 
############################

sub cleanemail {
  my ($cleanitem) = @_;

  # Eliminate anything that's not in [-A-Za-z0-9_+%@]
  $cleanitem =~ tr/-.A-Za-z0-9_+%@//cd;
  return $cleanitem;
}

##############################
##  CLEAN NUMBERS SUBROUTNE ## 
##############################

sub cleannumber {
  my ($cleanitem) = @_;

  # Eliminate anything that's not in [-A-Za-z0-9_+%@]
  $cleanitem =~ tr/-.A-Za-z0-9_+%@//cd;
  
  $cleanitem =~ s/ //g;
  $cleanitem =~ s/\-//g;
  $cleanitem =~ s/\%//g;
  $cleanitem =~ s/\@//g;
  $cleanitem =~ s/\$//g;
  $cleanitem =~ s/\,//g;
  return $cleanitem;
}


sub cleanthisfordb {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\\//g;
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/‘/\\‘/g;
   $dirtyitem =~ s/’/\\’/g;
   $dirtyitem =~ s/"/\\"/g;
   $dirtyitem =~ s/=/\\=/g;
   $dirtyitem =~ s/\+/\\+/g;
   $dirtyitem =~ s/\*/\\*/g;
   $dirtyitem =~ s/&/\\&/g;
   $dirtyitem =~ s/\,/\\,/g;
   $dirtyitem =~ s/\?/\\?/g;
   $dirtyitem =~ s/\;/\\;/g;
   $dirtyitem =~ s/\:/\\:/g;
   $dirtyitem =~ s/\-/\\-/g;
   $dirtyitem =~ s/\)/\\)/g;
   $dirtyitem =~ s/\(/\\(/g;
   $dirtyitem =~ s/\{/\\{/g;
   $dirtyitem =~ s/\}/\\}/g;
   $dirtyitem =~ s/\]/\\]/g;
   $dirtyitem =~ s/\[/\\[/g;
   $dirtyitem =~ s/\|/\\|/g;
   $dirtyitem =~ s/\^/\\^/g;
   $dirtyitem =~ s/\%/\\%/g;
   $dirtyitem =~ s/\#/\\#/g;
   $dirtyitem =~ s/\@/\\@/g;
   $dirtyitem =~ s/\!/\\!/g;
#   $dirtyitem =~ s/\_/\\_/g;
   $dirtyitem =~ s/\~/\\~/g;
   $dirtyitem =~ s/\</\\</g;
   $dirtyitem =~ s/\>/\\>/g;
   $dirtyitem = $dirtyitem;
}








sub cleanaccents4mysqlinsert {
my $cleanitem = $_[0];
   $cleanitem =~ s/À/¿/g; 
   $cleanitem =~ s/à/‡/g;   
   $cleanitem =~ s/Á/¡/g;  
   $cleanitem =~ s/á/·/g;
   $cleanitem =~ s/Â/¬/g;
   $cleanitem =~ s/â/‚/g;
   $cleanitem =~ s/Ã/√/g;
   $cleanitem =~ s/ã/„/g;
   $cleanitem =~ s/Ä/ƒ/g;
   $cleanitem =~ s/ä/‰/g;
   $cleanitem =~ s/É/…/g;
   $cleanitem =~ s/È/»/g;  # switched order so é doesn't get switched with È
   $cleanitem =~ s/é/È/g;  # switched order so é doesn't get switched with È
   $cleanitem =~ s/è/Ë/g;
   $cleanitem =~ s/Ê/ /g;
   $cleanitem =~ s/Ó/”/g;
   $cleanitem =~ s/î/Ó/g;
   $cleanitem =~ s/ë/Î/g;
   $cleanitem =~ s/Ì/Ã/g;
   $cleanitem =~ s/Ï/œ/g;  # switched order so ì doesn't get switched with Ï
   $cleanitem =~ s/ì/Ï/g;  # switched order so ì doesn't get switched with Ï
   $cleanitem =~ s/Õ/’/g;  # switched order so ì doesn't get switched with Õ
   $cleanitem =~ s/Í/Õ/g;  # switched order so ì doesn't get switched with Õ
   $cleanitem =~ s/í/Ì/g;
   $cleanitem =~ s/Î/Œ/g;
   $cleanitem =~ s/ï/Ô/g;
   $cleanitem =~ s/Ñ/—/g;
   $cleanitem =~ s/Ò/“/g;
   $cleanitem =~ s/ñ/Ò/g;
   $cleanitem =~ s/Ú/⁄/g;
   $cleanitem =~ s/ò/Ú/g;
   $cleanitem =~ s/Û/€/g;  # switched order so ó doesn't get switched with Û
   $cleanitem =~ s/ó/Û/g;  # switched order so ó doesn't get switched with Û
   $cleanitem =~ s/õ/ı/g;
   $cleanitem =~ s/Ö/÷/g;
   $cleanitem =~ s/ö/ˆ/g;
   $cleanitem =~ s/Ù/Ÿ/g;
   $cleanitem =~ s/ù/˘/g;
   $cleanitem =~ s/ú/˙/g;
   $cleanitem =~ s/û/˚/g;
   $cleanitem =~ s/Ü/‹/g;
   $cleanitem =~ s/ü/¸/g;
   $cleanitem =~ s/ÿ/ˇ/g;
   
   return ($cleanitem);
}


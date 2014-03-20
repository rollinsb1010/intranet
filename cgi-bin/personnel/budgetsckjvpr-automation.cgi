#!/usr/bin/perl

#####################################################################################################
# Copyright 2007 by Southwest Educational Development Laboratory
#
# 2002-09-26 Written by Brian Litke 
# 2007-06-14 MOVED TABLES TO "intranet" database AND ENABLED SEARCH OF PREVIOUS MONTHS BUDGET REPORTS
#
# DESCRIPTION:
# THIS SCRIPT IS INVOKED WHEN SOMEONE UPLOADS A FINANCIAL REPORT FILE (CK.TXT, JV.TXT or PR.TXT)
# IT DOES THE FOLLOWING:
# (1) DELETES INITIAL HEADER ROW IN DATA
# (2) FIXES THE DATES TO MYSQL FORMAT IN THE THREE DATABASES
# (3) CHECKS THE MONTH OF THE FILE BEING UPLOADED
# (4) DELETES SAME MONTH ENTRIES IN THE CKJVPR DATABASE
# (5) QUERIES THE 3 MYSQL DATABASES (CK, JV, and PR) AND INSERTS RECORDS INTO CONSOLIDATED CKJVPR DATABASE
######################################################################################################
use strict;
use CGI qw/:all/;

use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "intranetuser", "limited");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;
my $query = new CGI;
my $debug = $query->param("debug") || "0"; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS

my $browser = $ENV{"HTTP_USER_AGENT"};
my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};

my $actionfile = $query->param("actionfile");

#######################################
# START: PRINT PAGE HEADER HTML
#######################################
##############################################################
## START THE OUTPUT BY CREATING THE HTML HEADER AND PAGE TITLE
##############################################################
print $query-> header("text/html");
print <<EOM;
<HTML><head><title>SEDL Intranet - Budget Report Automation for CK, JV, PR</title>
<link rel="stylesheet" href="/css/style-td11-p11h14.css"></head>
<body bgcolor="#FFFFFF">
<H2>Now Doing Automation on the files... Wait, then scroll to bottom of page and follow link to Financial Report Database</H2>
<HR><HR><HR>
EOM

#######################################
# END: PRINT PAGE HEADER HTML
#######################################

####################################################
# START: DELETE UNNECESSARY HEADER ROW FROM NEW DATA
####################################################
my $command = "";
   $command = "delete from oftsbudgetsck where date like '%DATE%'" if ($actionfile eq 'CK');
   $command = "delete from oftsbudgetsjv where date like '%DATE%'" if ($actionfile eq 'JV');
   $command = "delete from oftsbudgetspr where date like '%DATE%'" if ($actionfile eq 'PR');
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
#my $num_matches = $sth->rows;
####################################################
# END: DELETE UNNECESSARY HEADER ROW FROM NEW DATA
####################################################


##############################################################################
## START: FIX DATES IN 3 DBs AND REMEMBER WHAT MONTH AND YEAR THE DATA IS FROM
##############################################################################
print "<H3>Fixing dates from mm-dd-yy to yyyy-mm-dd format</H3>";

#### START: HANDLE CK DATES
my $thisyearmonthck = "";
my $thisyearmonthck_firstseen = "";
	if ($actionfile eq 'CK') {
		my $lastdate = "";
		my $command = "select * from oftsbudgetsck order by date";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		print "<P>COMMAND: $command";

		my $count = "0";
			while (my @arr = $sth->fetchrow) {
				my ($date, $trxref, $gl, $fundyear, $orgcode, $objcode, $sbcode, $gender, $ethnicity, $othereeoc, $hubzone, $invno, $descrip, $address1, $address2, $city, $state, $zip, $socialnum, $itemname, $quantity, $quancode, $unitcost, $amount, $refno) = @arr;
		
				my $newdate = "";
#				$date =~ s/\//\-/gi;
				my ($possibleyear, $possiblemonth, $possibleday) = split(/\-/,$date);
				$thisyearmonthck = "$possibleyear\-$possiblemonth";
				$thisyearmonthck_firstseen = "$possibleyear\-$possiblemonth" if ($thisyearmonthck_firstseen eq '');
		
					# START: CHECK DATE VALIDITY AND DO ACTIONS ON INVALID DATES
					if (($possibleyear ne '1995') && ($possibleyear ne '1996') && ($possibleyear ne '1997') && ($possibleyear ne '1998') && ($possibleyear ne '1999') 
						&& ($possibleyear ne '2000') && ($possibleyear ne '2001') && ($possibleyear ne '2002') && ($possibleyear ne '2003') && ($possibleyear ne '2004') && ($possibleyear ne '2005') 
						&& ($possibleyear ne '2006') && ($possibleyear ne '2007') && ($possibleyear ne '2008') && ($possibleyear ne '2009') && ($possibleyear ne '2010') && ($possibleyear ne '2011')
						&& ($possibleyear ne '2012') && ($possibleyear ne '2013') && ($possibleyear ne '2014') && ($possibleyear ne '2015') && ($possibleyear ne '2016') && ($possibleyear ne '2017')
						&& ($possibleyear ne '2018') && ($possibleyear ne '2019') && ($possibleyear ne '2020')) {
		
						# PUT DATE IN MYSQL FORMAT
						$newdate = &date2mysql ($date);
		
						# RECORD WHAT YEAR/MONTH THE FILE IS FROM
						my ($possibleyear, $possiblemonth, $possibleday) = split(/\-/,$newdate);
						$thisyearmonthck = "$possibleyear\-$possiblemonth";
		
					} # END: CHECK DATE VALIDITY AND DO ACTIONS ON INVALID DATES
		
					# IF NEWDATE NOT EMPTY, UPDATE DATE IN DB
					if ($date ne $lastdate) {
						if (($newdate ne '') && ($newdate =~ '20')) {
							my $command = "UPDATE oftsbudgetsck SET date='$newdate' WHERE date='$date'";
							my $dbh = DBI->connect($dsn, "intranetuser", "limited");
							my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
							$sth->execute;
							print "<BR>$count DATABASE CK: $command<BR>$date, $trxref, $gl, $fundyear, $orgcode, $objcode, $sbcode, $refno, $descrip, $socialnum, $itemname, $quantity, $quancode, $unitcost, $amount";
							$count++;
						} else {# END ACTIONS IF NEWDATE IS NOT EMPTY
							print "<BR><FONT COLOR=GRAY>ERROR CK: SKIPPING RECORD - DATE ALREADY FIXED: $date</FONT>";
						}
					} # END IF DATE NOT = LASTDATE
		
				$lastdate = $date;
			} # END DB QUERY LOOP
		
#		if ($thisyearmonthck ne $thisyearmonthck_firstseen) {
#			print "<p><FONT COLOR=red>WARNING: The data upload seems to have two different month/years of data (Comparing: $thisyearmonthck to $thisyearmonthck_firstseen)</font></p>";
#		}
	#### END: HANDLE CK DATES
	} # END ACTIONS FOR CK


#### START: HANDLE JV DATES
my $thisyearmonthjv = "";
my $thisyearmonthjv_firstseen = "";
	if ($actionfile eq 'JV') {
		my $lastdate = "";
		my $command = "select * from oftsbudgetsjv order by date";

		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;

			while (my @arr = $sth->fetchrow) {
				my ($date, $trxref, $gl, $fundyear, $orgcode, $objcode, $sbcode, $refno, $descrip, $socialnum, $itemname, $quantity, $quancode, $unitcost, $amount) = @arr;

				my $newdate = "";
				my ($possibleyear, $possiblemonth, $possibleday) = split(/\-/,$date);
				$thisyearmonthjv = "$possibleyear\-$possiblemonth";
				$thisyearmonthjv_firstseen = "$possibleyear\-$possiblemonth" if ($thisyearmonthjv_firstseen eq '');

					# START: CHECK DATE VALIDITY AND DO ACTIONS ON INVALID DATES
					if (($possibleyear ne '1995') && ($possibleyear ne '1996') && ($possibleyear ne '1997') && ($possibleyear ne '1998') && ($possibleyear ne '1999') 
						&& ($possibleyear ne '2000') && ($possibleyear ne '2001') && ($possibleyear ne '2002') && ($possibleyear ne '2003') && ($possibleyear ne '2004') && ($possibleyear ne '2005') 
						&& ($possibleyear ne '2006') && ($possibleyear ne '2007') && ($possibleyear ne '2008') && ($possibleyear ne '2009') && ($possibleyear ne '2010') && ($possibleyear ne '2011')
						&& ($possibleyear ne '2012') && ($possibleyear ne '2013') && ($possibleyear ne '2014') && ($possibleyear ne '2015') && ($possibleyear ne '2016') && ($possibleyear ne '2017')
						&& ($possibleyear ne '2018') && ($possibleyear ne '2019') && ($possibleyear ne '2020')) {

						# PUT DATE IN MYSQL FORMAT
						$newdate = &date2mysql ($date);

						# RECORD WHAT YEAR/MONTH THE FILE IS FROM
						my ($possibleyear, $possiblemonth, $possibleday) = split(/\-/,$newdate);
						$thisyearmonthjv = "$possibleyear\-$possiblemonth";

					} # END: CHECK DATE VALIDITY AND DO ACTIONS ON INVALID DATES

					# IF NEWDATE NOT EMPTY, UPDATE DATE IN DB
					if ($date ne $lastdate) {
						if (($newdate ne '') && ($newdate =~ '20')) {
							my $command = "UPDATE oftsbudgetsjv SET date='$newdate' WHERE date='$date'";
							my $dbh = DBI->connect($dsn, "intranetuser", "limited");
							my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
							$sth->execute;
							print "<BR>DATABASE JV: $command<BR>$date, $trxref, $gl, $fundyear, $orgcode, $objcode, $sbcode, $refno, $descrip, $socialnum, $itemname, $quantity, $quancode, $unitcost, $amount";
						} else {# END ACTIONS IF NEWDATE IS NOT EMPTY
							print "<BR><FONT COLOR=GRAY>ERROR JV: SKIPPING RECORD - DATE ALREADY FIXED: $date</FONT>";
						}
					} # END IF DATE NOT = LASTDATE

				$lastdate = $date;
			} # END DB QUERY LOOP

#		if ($thisyearmonthjv ne $thisyearmonthjv_firstseen) {
#			print "<p><FONT COLOR=red>WARNING: The data upload seems to have two different month/years of data (Comparing: $thisyearmonthjv to $thisyearmonthjv_firstseen)</font></p>";
#		}

	#### END: HANDLE JV DATES
	} # END ACTIONS FOR JV


#### START: HANDLE PR DATES
my $thisyearmonthpr = "";
my $thisyearmonthpr_firstseen = "";
	if ($actionfile eq 'PR') {
		my $lastdate = "";
		my $command = "select * from oftsbudgetspr order by date";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
			while (my @arr = $sth->fetchrow) {
				my ($date, $trxref, $gl, $fundyear, $orgcode, $objcode, $sbcode, $refno, $descrip, $socialnum, $itemname, $percenthrs, $quantity, $quancode, $unitcost, $amount) = @arr;

				my $newdate = "";
				my ($possibleyear, $possiblemonth, $possibleday) = split(/\-/,$date);
				$thisyearmonthpr = "$possibleyear\-$possiblemonth";
				$thisyearmonthpr_firstseen = "$possibleyear\-$possiblemonth" if ($thisyearmonthpr_firstseen eq '');

					# START: CHECK DATE VALIDITY AND DO ACTIONS ON INVALID DATES
					if (($possibleyear ne '1995') && ($possibleyear ne '1996') && ($possibleyear ne '1997') && ($possibleyear ne '1998') && ($possibleyear ne '1999') 
						&& ($possibleyear ne '2000') && ($possibleyear ne '2001') && ($possibleyear ne '2002') && ($possibleyear ne '2003') && ($possibleyear ne '2004') && ($possibleyear ne '2005') 
						&& ($possibleyear ne '2006') && ($possibleyear ne '2007') && ($possibleyear ne '2008') && ($possibleyear ne '2009') && ($possibleyear ne '2010') && ($possibleyear ne '2011')
						&& ($possibleyear ne '2012') && ($possibleyear ne '2013') && ($possibleyear ne '2014') && ($possibleyear ne '2015') && ($possibleyear ne '2016') && ($possibleyear ne '2017')
						&& ($possibleyear ne '2018') && ($possibleyear ne '2019') && ($possibleyear ne '2020')) {

						# PUT DATE IN MYSQL FORMAT
						$newdate = &date2mysql ($date);

						# RECORD WHAT YEAR/MONTH THE FILE IS FROM
						my ($possibleyear, $possiblemonth, $possibleday) = split(/\-/,$newdate);
						$thisyearmonthpr = "$possibleyear\-$possiblemonth";

					} # END: CHECK DATE VALIDITY AND DO ACTIONS ON INVALID DATES

					# IF NEWDATE NOT EMPTY, UPDATE DATE IN DB
					if ($date ne $lastdate) {
						if (($newdate ne '') && ($newdate =~ '20')) {
							my $command = "UPDATE oftsbudgetspr SET date='$newdate' WHERE date='$date'";
							my $dbh = DBI->connect($dsn, "intranetuser", "limited");
							my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
							$sth->execute;
							print "<BR>DATABASE PR: $command<BR>" if ($date ne '$lastdate');
							print "<BR>DATABASE PR: $command<BR>$date, $trxref, $gl, $fundyear, $orgcode, $objcode, $sbcode, $refno, $descrip, $socialnum, $itemname, $quantity, $quancode, $unitcost, $amount" if $debug;
						} else { 	# END ACTIONS IF NEWDATE IS NOT EMPTY
							print "<BR><FONT COLOR=GRAY>ERROR PR: SKIPPING RECORD - DATE ALREADY FIXED: $date</FONT>";
						}
					} # END IF DATE NOT = LASTDATE

				$lastdate = $date;
			} # END DB QUERY LOOP
		
#		if ($thisyearmonthpr ne $thisyearmonthpr_firstseen) {
#			print "<p><FONT COLOR=red>WARNING: The data upload seems to have two different month/years of data (Comparing: $thisyearmonthpr to $thisyearmonthpr_firstseen)</font></p>";
#		}
	#### END: HANDLE PR DATES
	} # END ACTIONS FOR PR
##############################################################################
## END: FIX DATES IN 3 DBs AND REMEMBER WHAT MONTH AND YEAR THE DATA IS FROM
##############################################################################




#######################################################################
## START: DELETE DATES IN CKJVPR DATABASE SO WE CAN IMPORT NEW ENTRIES
#######################################################################
if ($thisyearmonthck ne '') {
	my $command = "delete from oftsbudgetsckjvpr where date like '$thisyearmonthck%' AND camefromfile like 'CK'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	print "<P><FONT COLOR=GREEN>CKJVPR DATABASE DELETING $num_matches CK RECORDS WHOSE MONTH MATCHES UPLOAD FILE:<BR>$command</FONT>";
}

if ($thisyearmonthjv ne '') {
	my $command = "delete from oftsbudgetsckjvpr where date like '$thisyearmonthjv%' AND camefromfile like 'JV'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	print "<P><FONT COLOR=GREEN>CKJVPR DATABASE DELETING $num_matches JV RECORDS WHOSE MONTH MATCHES UPLOAD FILE:<BR>$command</FONT>";
}

if ($thisyearmonthpr ne '') {
	my $command = "delete from oftsbudgetsckjvpr where date like '$thisyearmonthpr%' AND camefromfile like 'PR'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	print "<P><FONT COLOR=GREEN>CKJVPR DATABASE DELETING $num_matches PR RECORDS WHOSE MONTH MATCHES UPLOAD FILE:<BR>$command</FONT>";
}

#######################################################################
## END: DELETE DATES IN CKJVPR DATABASE SO WE CAN IMPORT NEW ENTRIES
#######################################################################



#######################################################
## START: INSERT MONTHLY ENTRIES FROM 3 DATABASES
#######################################################
if ($actionfile eq 'CK') {
	my $counter = "1";
	my $command = "select * from oftsbudgetsck order by trxref";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	print "<H3>INSERTING $num_matches MONTHLY ENTRIES FROM CK FILE INTO CKJVPR database </H3>";
	print "<P>COMMAND: $command";
		while (my @arr = $sth->fetchrow) {
			my ($date, $trxref, $gl, $fundyear, $orgcode, $objcode, $sbcode, $gender, $ethnicity, $othereeoc, $hubzone, $invno, $descrip, $address1, $address2, $city, $state, $zip, $socialnum, $itemname, $quantity, $quancode, $unitcost, $amount, $refno) = @arr;

			## UNUSED VARIABLES
			my $percenthrs = "";

			## BACKSLASH SPECIAL CHARACTERS
			$itemname = &cleanthisfordb ($itemname);
			$address1 = &cleanthisfordb ($address1);
			$address2 = &cleanthisfordb ($address2);
			$descrip = &cleanthisfordb ($descrip);

			#INSERT LINE INTO CKJVPR DATAABSE
			my $command = "INSERT INTO oftsbudgetsckjvpr VALUES ('$date', '$trxref', '$gl', '$fundyear', '$orgcode', '$objcode', '$sbcode', '$gender', '$ethnicity', '$othereeoc', '$hubzone', '$invno', '$descrip', '$address1', '$address2', '$city', '$state', '$zip', '$socialnum', '$itemname', '$percenthrs', '$quantity', '$quancode', '$unitcost', '$amount', '$refno', 'CK')";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
			my $failed = "";
			   $failed = "<FONT COLOR=RED>FAILED</FONT>" if ($num_matches ne '1');
			print "<BR><FONT COLOR=RED>\# $counter</FONT> <FONT COLOR=GREEN>$failed CKJVPR DATABASE INSERT CK: $command</FONT>";
			$counter++;
		} # END DB QUERY LOOP THOUGH CK DB
} # END IF CK


if ($actionfile eq 'JV') {
	my $counter = "1";
	my $command = "select * from oftsbudgetsjv order by trxref";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;

	print "<H3>INSERTING $num_matches MONTHLY ENTRIES FROM JV FILE INTO CKJVPR database </H3>";
	while (my @arr = $sth->fetchrow) {
		my ($date, $trxref, $gl, $fundyear, $orgcode, $objcode, $sbcode, $refno, $descrip, $socialnum, $itemname, $quantity, $quancode, $unitcost, $amount) = @arr;

		## UNUSED VARIABLES
		my $gender = "";
		my $ethnicity = "";
		my $othereeoc = "";
		my $hubzone = "";
		my $invno = "";
		my $address1 = "";
		my $address2 = "";
		my $city = "";
		my $state = "";
		my $zip = "";
		my $percenthrs = "";

		## BACKSLASH SPECIAL CHARACTERS
		$itemname = &cleanthisfordb ($itemname);
		$address1 = &cleanthisfordb ($address1);
		$address2 = &cleanthisfordb ($address2);
		$descrip = &cleanthisfordb ($descrip);

		#INSERT LINE INTO CKJVPR DATAABSE
		my $command = "INSERT INTO oftsbudgetsckjvpr VALUES ('$date', '$trxref', '$gl', '$fundyear', '$orgcode', '$objcode', '$sbcode', '$gender', '$ethnicity', '$othereeoc', '$hubzone', '$invno', '$descrip', '$address1', '$address2', '$city', '$state', '$zip', '$socialnum', '$itemname', '$percenthrs', '$quantity', '$quancode', '$unitcost', '$amount', '$refno', 'JV')";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		my $failed = "";
		   $failed = "<FONT COLOR=RED>FAILED</FONT>" if ($num_matches ne '1');
		print "<BR><FONT COLOR=RED>\# $counter</FONT> <FONT COLOR=GREEN>$failed CKJVPR DATABASE INSERT JV: $command</FONT>";
		$counter++;
	} # END DB QUERY LOOP THOUGH JV DB
} # END IF JV 

if ($actionfile eq 'PR') {
	my $counter = "1";
	my $command = "select * from oftsbudgetspr order by trxref";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	print "<H3>INSERTING $num_matches MONTHLY ENTRIES FROM PR FILE INTO CKJVPR database </H3>";
		while (my @arr = $sth->fetchrow) {
			my ($date, $trxref, $gl, $fundyear, $orgcode, $objcode, $sbcode, $refno, $descrip, $socialnum, $itemname, $percenthrs, $quantity, $quancode, $unitcost, $amount) = @arr;

			## UNUSED VARIABLES
			my $gender = "";
			my $ethnicity = "";
			my $othereeoc = "";
			my $hubzone = "";
			my $invno = "";
			my $address1 = "";
			my $address2 = "";
			my $city = "";
			my $state = "";
			my $zip = "";

			## BACKSLASH SPECIAL CHARACTERS
			$itemname = &cleanthisfordb ($itemname);
			$address1 = &cleanthisfordb ($address1);
			$address2 = &cleanthisfordb ($address2);
			$descrip = &cleanthisfordb ($descrip);

			#INSERT LINE INTO CKJVPR DATAABSE
			my $command = "INSERT INTO oftsbudgetsckjvpr VALUES ('$date', '$trxref', '$gl', '$fundyear', '$orgcode', '$objcode', '$sbcode', '$gender', '$ethnicity', '$othereeoc', '$hubzone', '$invno', '$descrip', '$address1', '$address2', '$city', '$state', '$zip', '$socialnum', '$itemname', '$percenthrs', '$quantity', '$quancode', '$unitcost', '$amount', '$refno', 'PR')";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
			my $failed = "";
			   $failed = "<FONT COLOR=RED>FAILED</FONT>" if ($num_matches ne '1');
			print "<BR><FONT COLOR=RED>\# $counter</FONT> <FONT COLOR=GREEN>$failed CKJVPR DATABASE INSERT PR: $command</FONT>";
			$counter++;
		} # END DB QUERY LOOP THOUGH PR DB
} # END IF PR
#######################################################
## END: INSERT MONTHLY ENTRIES FROM 3 DATABASES
#######################################################


##########################################################
## START: DELETE ITEMS WHOSE GL CODE IS NOT 91, 98, or 99
##########################################################
if ($actionfile eq 'CK') {
	print "<H3>Deleting items whose GL code is not 91, 98, or 99</H3>";
	my $command = "delete from oftsbudgetsckjvpr where ((camefromfile like 'CK') && ((gl NOT like '91') AND (gl NOT LIKE '98') AND (gl NOT LIKE '99')))";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;

	my $command = "select * from oftsbudgetsckjvpr where (camefromfile like 'CK')";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches2 = $sth->rows;

	print "<P>$command<BR>Deleted $num_matches CK entries.  There are $num_matches2 CK records remaining<P>";
}

if ($actionfile eq 'JV') {
	print "<H3>Deleting items whose GL code is not 91, 98, or 99</H3>";
	my $command = "delete from oftsbudgetsckjvpr where ((camefromfile like 'JV') && ((gl NOT like '91') AND (gl NOT LIKE '98') AND (gl NOT LIKE '99')))";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;

	my $command = "select * from oftsbudgetsckjvpr where (camefromfile like 'JV')";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches2 = $sth->rows;

	print "<P>$command<BR>Deleted $num_matches JV entries.  There are $num_matches2 JV records remaining<P>";
}

if ($actionfile eq 'PR') {
	print "<H3>Deleting items whose GL code is not 91, 98, or 99</H3>";
	my $command = "delete from oftsbudgetsckjvpr where ((camefromfile like 'PR') && ((gl NOT like '91') AND (gl NOT LIKE '98') AND (gl NOT LIKE '99')))";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;

	my $command = "select * from oftsbudgetsckjvpr where (camefromfile like 'PR')";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches2 = $sth->rows;

	print "<P>$command<BR>Deleted $num_matches PR entries.  There are $num_matches2 PR records remaining<P>";

}
##########################################################
## END: DELETE ITEMS WHOSE GL CODE IS NOT 91, 98, or 99
##########################################################

##########################################################
## START: REMOVE SSN DATA FROM "socialnum" FIELD
##########################################################
if ($actionfile eq 'CK') {
	if ($actionfile eq 'CK') {
		# CLEAN CK
		my $command_private1 = "UPDATE oftsbudgetsck SET socialnum = 'private' where socialnum NOT LIKE 'private'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_private1) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		print "<p>REMOVED SOCIAL SECURITY NUMBER FROM \"CK\" DATA.</p>";
	}

	if ($actionfile eq 'PR') {
		# CLEAN PR
		my $command_private2 = "UPDATE oftsbudgetspr SET socialnum = 'private' where socialnum NOT LIKE 'private'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_private2) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		print "<p>REMOVED SOCIAL SECURITY NUMBER FROM \"PR\" DATA.</p>";
	}

	# CLEAN CKJVPR
		my $command_private3 = "UPDATE oftsbudgetsckjvpr SET socialnum = 'private' where socialnum NOT LIKE 'private'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_private3) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		print "<p>REMOVED SOCIAL SECURITY NUMBER FROM \"CKJVPR\" DATA.</p>";
}
##########################################################
## END: REMOVE SSN DATA FROM "socialnum" FIELD
##########################################################


###########################
## START: PRINT PAGE FOOTER
###########################
print <<EOM;
<H2>Finished with Automation on the CK, JV, and PR files...</H2>
<P>
Click here to <A HREF=/staff/personnel/budgets.cgi>check on the financial report database</A>
<P>
Click here to <a href="http://www.sedl.org/staff/personnel/dataupload.cgi">upload another file</a>.
</TD></TR>
</TABLE>
</BODY>
</HTML>
EOM
###########################
## END: PRINT PAGE FOOTER
###########################



#################################
## FUNCTIONS USED BY THIS SCRIPT
#################################
sub date2mysql {
	my $date2transform = $_[0];
	   $date2transform =~ s/ //g;
	   $date2transform =~ s/\-/\//g;
	my ($thismonth,$thisdate,$thisyear) = split(/\//,$date2transform);

	if (substr($thisyear,0,1) eq '9') {
		$thisyear = "19$thisyear"; # ADD YEAR PREFIX OF 19 or 20
	} else {
		$thisyear = "20$thisyear";
	}

	if (length($thismonth) == 1) {
		$thismonth = "0$thismonth"; # ADD LEADING ZERO IF NECESSARY
	}

	if (length($thisdate) == 1) {
		$thisdate = "0$thisdate"; # ADD LEADING ZERO IF NECESSARY
	}

	$date2transform = "$thisyear\-$thismonth\-$thisdate";
	$date2transform = "" if $date2transform eq '--';
	return($date2transform);
}




sub cleanthisfordb {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\\//g;
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/Ô/\\Ô/g;
   $dirtyitem =~ s/Õ/\\Õ/g;
   $dirtyitem =~ s/"/\\"/g;
	return($dirtyitem);
}


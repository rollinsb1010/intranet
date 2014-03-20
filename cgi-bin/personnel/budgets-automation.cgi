#!/usr/bin/perl

#####################################################################################################
# Copyright 2007 by Southwest Educational Development Laboratory
#
# 2002-09-26 Written by Brian Litke 
# 2007-06-14 MOVED TABLES TO "intranet" database AND ENABLED SEARCH OF PREVIOUS MONTHS BUDGET REPORTS
#
# NEW DESCRIPTION:
# THIS SCRIPT IS INVOKED WHEN SOMEONE UPLOADS A FINANCIAL REPORT FILE (FINANCE.TXT or FINANCC.TXT)
# IT DOES THE FOLLOWING FOR BOTH THE CONTRACT-FINANCIAL REPORT AND THE FY-FINANCIAL REPORT:
# (1) DELETES ENTRIES MATCHING THE MONTH OF THE UPLOAD DATA IN THE oftsbudgets AND oftsbudgetsfy TABLES
# (2) DELETES PREVIOUS SUBTOTAL ENTRIES CREATED BY THIS SCRIPT in the oftsbudgets_newmonth AND oftsbudgetsfy_newmonth TABLES
# (3) QUERIES THE FINANCE.TXT and FINANCC.TXT DATA FILES AND INSERTS SUBTOTAL DATA FOR 24 REPORTS into the oftsbudgets_newmonth AND oftsbudgetsfy_newmonth TABLES
# (4) PUSHES THE MONTHLY DATA FROM THE oftsbudgets_newmonth AND oftsbudgetsfy_newmonth TABLES INTO THE oftsbudgets AND oftsbudgetsfy TABLES
######################################################################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);

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


#######################################
# START: PRINT PAGE HEADER HTML
#######################################
print header;
print <<EOM;
<HTML><head><title>SEDL Intranet - Budget Report Automation for CK, JV, PR</title>
<link href="/staff/includes/staff2006.css" rel="stylesheet" type="text/css">
<body>
<table bgcolor="#ffffff">
<tr><td>
EOM
#######################################
# END: PRINT PAGE HEADER HTML
#######################################

################################################################################
## START: LOOK UP RELEVANT MONTH OF FILE BEING UPLOADED AND DATE IN OTHER FILE
##		  DATE IS USED LATER TO TRIGGER THE DATA MERGE INTO THE DATABASE WHEN THE MONTHS MATCH
################################################################################
	my $relevant_month = "1964-01-01"; # DEFAULT TO NON-EXISTANT MONTH
	my $irrelevant_month = "1964-01-01"; # DEFAULT TO NON-EXISTANT MONTH
	my $periodenddate_oftsbudgets = "";
	my $periodenddate_oftsbudgetsfy = "";


	# GRAB DATE FROM oftsbudgets_newmonth
	my $command = "select periodenddate from oftsbudgets_newmonth order by periodenddate";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;
	while (my @arr = $sth->fetchrow) {
		($periodenddate_oftsbudgets) = @arr;
	} # END DB QUERY LOOP

	# GRAB DATE FROM oftsbudgetsfy_newmonth
	my $command = "select periodenddate from oftsbudgetsfy_newmonth order by periodenddate";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;
	while (my @arr = $sth->fetchrow) {
		($periodenddate_oftsbudgetsfy) = @arr;
	} # END DB QUERY LOOP

	## START: COMPUTE LATEST FILE MODIFICAITON DATES TO SEE WHICH DATE TO USE
	open(HANDLE, "/home/httpd/html/temp/FINANCE2.TXT");
	my $moddate_oftsbudgets = localtime( (stat HANDLE)[9] );
	close(HANDLE);
	
	open(HANDLE, "/home/httpd/html/temp/FINANCC2.TXT");
	my $moddate_oftsbudgetsfy = localtime( (stat HANDLE)[9] );
	close(HANDLE);
	
	my $timesincemod_oftsbudgets = -M "/home/httpd/html/temp/FINANCE2.TXT";
	my $timesincemod_oftsbudgetsfy = -M "/home/httpd/html/temp/FINANCC2.TXT";
	## END: COMPUTE LATEST FILE MODIFICAITON DATES TO SEE WHICH DATE TO USE

print<<EOM;
<p class="info">
<br>periodenddate from oftsbudgets_newmonth = $periodenddate_oftsbudgets
<br>periodenddate from oftsbudgetsfy_newmonth = $periodenddate_oftsbudgetsfy
<br>UPLOAD FILE: MOD DATE FINANCE.TXT = $moddate_oftsbudgets
<br>UPLOAD FILE: MOD DATE FINANCC.TXT = $moddate_oftsbudgetsfy
<br>UPLOAD FILE: TIME SINCE MOD FINANCE.TXT = $timesincemod_oftsbudgets
<br>UPLOAD FILE: TIME SINCE MOD FINANCC.TXT = $timesincemod_oftsbudgetsfy
EOM
	my $usedb = "FINANCE2.TXT"; # DEFAULT
	my $dontusedb = "FINANCC2.TXT"; # OPPOSITE DEFAULT
	if ($timesincemod_oftsbudgetsfy < $timesincemod_oftsbudgets) {
		$usedb = "FINANCC2.TXT";
		$dontusedb = "FINANCE2.TXT";
		$relevant_month = $periodenddate_oftsbudgetsfy;
		$irrelevant_month = $periodenddate_oftsbudgets;
	} else {
		$relevant_month = $periodenddate_oftsbudgets;
		$irrelevant_month = $periodenddate_oftsbudgetsfy;
	}
	
	# START: CHOP DAY OFF DATE TO LEAVE YYYY-MM
	$relevant_month = substr($relevant_month,0,7);
	$irrelevant_month = substr($irrelevant_month,0,7);
	# END: CHOP DAY OFF DATE TO LEAVE YYYY-MM

print<<EOM;
<br>RELEVANT MONTH TO UPDATE (using $usedb) = $relevant_month
</p>
EOM
######################################################################
## END: LOOK UP RELEVANT MONTH OF FILE BEING UPLOADED AND DATE IN OTHER FILE
##		DATE IS USED LATER TO TRIGGER THE DATA MERGE INTO THE DATABASE WHEN THE MONTHS MATCH
######################################################################


##########################################################################################################################################
## START: DELETE ANY MONTH RECORDS IN LIVE DATABASE FOR MONTH BEING UPLOADED - THEY WILL BE REPOPULATED WHEN BOTH FILE UPLOADS HAVE A MATCHING MONTH
##########################################################################################################################################
print<<EOM;
<H2>Now Computing subtotal data... Wait, then scroll to bottom of page and follow link to Financial Report Database</H2>
EOM

if ($relevant_month =~ '20') {
	my $command = "delete from oftsbudgets where periodenddate like '$relevant_month%'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;
	print "<p class=\"info\">Deleting the month's ($relevant_month) data (in oftsbudgets) associated with the upload file.<br>$command";
	
	my $command = "delete from oftsbudgetsfy where periodenddate like '$relevant_month%'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;
	print "<br><br>Deleting the month's data (in oftsbudgetsfy) associated with the upload file.<br>$command";
} else {
	print "<br><br>WARNING - ERROR! ABORTED DUE TO MALFORMED MONTH Deleting the month's data (in oftsbudgetsfy) associated with the upload file.<br>$command";
}
##########################################################################################################################################
## END: DELETE ANY MONTH RECORDS IN LIVE DATABASE FOR MONTH BEING UPLOADED - THEY WILL BE REPOPULATED WHEN BOTH FILE UPLOADS HAVE A MATCHING MONTH
##########################################################################################################################################


######################################################################
## START: DELETE SUBTOTALS FROM RELEVANT MONTH IN TEMP UPLOAD DATABASE
######################################################################
my $command = "delete from oftsbudgets_newmonth where orgcode like 'zSubtotal%'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;
print "<br><br>Deleting subtotal lines (in oftsbudgets_newmonth) from the month's data associated with the upload file.";

my $command = "delete from oftsbudgetsfy_newmonth where orgcode like 'zSubtotal%'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;
print "<br><br>Deleting subtotal lines (in oftsbudgetsfy_newmonth) from the month's data associated with the upload file.";
######################################################################
## END: DELETE SUBTOTALS FROM RELEVANT MONTH IN TEMP UPLOAD DATABASE
######################################################################

######################################################################
## START: DELETE CODES IN TEMP UPLOAD DATABASE WE DON'T NEED TO SHOW
######################################################################
my $command = "delete from oftsbudgets_newmonth where ( (fundyear like '0190' AND orgcode like '116XX') 
														OR (fundyear like '0190' AND orgcode like '120XX') 
														OR (fundyear like '0190' AND orgcode like '123XX') 
														OR (fundyear like '0190' AND orgcode like '127XX') )";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;
print "<br><br>DELETING CELEARING ACCOUNT ORG CODES FOR TENNANT BILLINGS... ( (fundyear like '0190' AND orgcode like '116XX') 
														OR (fundyear like '0190' AND orgcode like '120XX') 
														OR (fundyear like '0190' AND orgcode like '123XX') 
														OR (fundyear like '0190' AND orgcode like '127XX') )<br>
($num_matches ENTRIES DELETED FROM MAIN DB)";

my $command = "delete from oftsbudgetsfy_newmonth where ( (fundyear like '0190' AND orgcode like '116XX') 
														OR (fundyear like '0190' AND orgcode like '120XX') 
														OR (fundyear like '0190' AND orgcode like '123XX') 
														OR (fundyear like '0190' AND orgcode like '127XX') )";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;
print "<br><br>DELETING CELEARING ACCOUNT ORG CODES FOR TENNANT BILLINGS... ( (fundyear like '0190' AND orgcode like '116XX') 
														OR (fundyear like '0190' AND orgcode like '120XX') 
														OR (fundyear like '0190' AND orgcode like '123XX') 
														OR (fundyear like '0190' AND orgcode like '127XX') )<br>
($num_matches ENTRIES DELETED FROM <b>FY</b> DB oftsbudgetsfy_newmonth)</p>";
######################################################################
## END: DELETE CODES IN TEMP UPLOAD DATABASE WE DON'T NEED TO SHOW
######################################################################




######################################################################################################## 
## START: COMPUTE AND INSERT SUBTOTAL ENTRIES INTO 'oftsbudgets_newmonth' and 'oftsbudgetsfy_newmonth'
######################################################################################################## 
my $finished = "no";
my $databasename = "oftsbudgets_newmonth"; # NAMES: 'oftsbudgets_newmonth' and 'oftsbudgetsfy_newmonth'

	while ($finished eq 'no') {

	######################################################################## 
	## START: LOOP THROUGH DATABASE 27 times to create subtotals
	######################################################################## 
	my $loopcounter = "0";
#	my @command = "";
	
	my @fundyearlist;
	my @orgcodelist;
	my @neworgcodelist;

##########################################
## START: PUSH DATA INTO THE THREE ARRAYS
##########################################
	## START: NEW SW REL CODES
	push (@fundyearlist, "0122"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal REL SW");
	push (@fundyearlist, "0122"); push (@orgcodelist, "A1"); push (@neworgcodelist, "zSubtotal REL A1");
	push (@fundyearlist, "0122"); push (@orgcodelist, "A2"); push (@neworgcodelist, "zSubtotal REL A2");
	push (@fundyearlist, "0122"); push (@orgcodelist, "A3"); push (@neworgcodelist, "zSubtotal REL A3");
	push (@fundyearlist, "0122"); push (@orgcodelist, "A4"); push (@neworgcodelist, "zSubtotal REL A4");
	push (@fundyearlist, "0122"); push (@orgcodelist, "A5"); push (@neworgcodelist, "zSubtotal REL A5");
	push (@fundyearlist, "0122"); push (@orgcodelist, "A6"); push (@neworgcodelist, "zSubtotal REL A6");
	push (@fundyearlist, "0122"); push (@orgcodelist, "A7"); push (@neworgcodelist, "zSubtotal REL A7");
	## END: NEW SW REL CODES
## START: NEW SE REL CODES
	push (@fundyearlist, "0142"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal REL SE");
	push (@fundyearlist, "0142"); push (@orgcodelist, "A1"); push (@neworgcodelist, "zSubtotal REL A1");
	push (@fundyearlist, "0142"); push (@orgcodelist, "A2"); push (@neworgcodelist, "zSubtotal REL A2");
	push (@fundyearlist, "0142"); push (@orgcodelist, "A3"); push (@neworgcodelist, "zSubtotal REL A3");
	push (@fundyearlist, "0142"); push (@orgcodelist, "A4"); push (@neworgcodelist, "zSubtotal REL A4");
	push (@fundyearlist, "0142"); push (@orgcodelist, "A5"); push (@neworgcodelist, "zSubtotal REL A5");
	push (@fundyearlist, "0142"); push (@orgcodelist, "A6"); push (@neworgcodelist, "zSubtotal REL A6");
	push (@fundyearlist, "0142"); push (@orgcodelist, "A7"); push (@neworgcodelist, "zSubtotal REL A7");
	## END: NEW SE REL CODES
push (@fundyearlist, "0090"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal REL");
push (@fundyearlist, "0090"); push (@orgcodelist, "T1"); push (@neworgcodelist, "zSubtotal T1");
push (@fundyearlist, "0090"); push (@orgcodelist, "T2"); push (@neworgcodelist, "zSubtotal T2");
push (@fundyearlist, "0090"); push (@orgcodelist, "T3"); push (@neworgcodelist, "zSubtotal T3");
push (@fundyearlist, "0090"); push (@orgcodelist, "T4"); push (@neworgcodelist, "zSubtotal T4");
push (@fundyearlist, "0090"); push (@orgcodelist, "T5"); push (@neworgcodelist, "zSubtotal T5");
push (@fundyearlist, "0090"); push (@orgcodelist, "T6"); push (@neworgcodelist, "zSubtotal T6");

push (@fundyearlist, "0100"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0100"); push (@orgcodelist, "K6"); push (@neworgcodelist, "zSubtotal K6");
push (@fundyearlist, "0143"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0152"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0162"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0162"); push (@orgcodelist, "M"); push (@neworgcodelist, "zSubtotal M");
push (@fundyearlist, "0162"); push (@orgcodelist, "E"); push (@neworgcodelist, "zSubtotal E");
push (@fundyearlist, "0172"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0177"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0178"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0182"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0190"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0202"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0202"); push (@orgcodelist, "D"); push (@neworgcodelist, "zSubtotal D");
push (@fundyearlist, "0202"); push (@orgcodelist, "E"); push (@neworgcodelist, "zSubtotal E");
push (@fundyearlist, "0212"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0212"); push (@orgcodelist, "M"); push (@neworgcodelist, "zSubtotal M");
push (@fundyearlist, "0212"); push (@orgcodelist, "E"); push (@neworgcodelist, "zSubtotal E");
push (@fundyearlist, "0215"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0245"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0203"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0222"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0232"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0242"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0252"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0256"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0266"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0291"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0292"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0299"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");

push (@fundyearlist, "0305"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0305"); push (@orgcodelist, "D"); push (@neworgcodelist, "zSubtotal D");
push (@fundyearlist, "0305"); push (@orgcodelist, "E"); push (@neworgcodelist, "zSubtotal E");
push (@fundyearlist, "0315"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0315"); push (@orgcodelist, "M"); push (@neworgcodelist, "zSubtotal M");
push (@fundyearlist, "0315"); push (@orgcodelist, "E"); push (@neworgcodelist, "zSubtotal E");
push (@fundyearlist, "0346"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0363"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0376"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0386"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0417"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0446"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0497"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0518"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0538"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0548"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0558"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0568"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0578"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0589"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0599"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0609"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0619"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0619"); push (@orgcodelist, "V"); push (@neworgcodelist, "zSubtotal V");
push (@fundyearlist, "0629"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0629"); push (@orgcodelist, "V"); push (@neworgcodelist, "zSubtotal V");
push (@fundyearlist, "0639"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0649"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0649"); push (@orgcodelist, "V"); push (@neworgcodelist, "zSubtotal V");
push (@fundyearlist, "0649"); push (@orgcodelist, "M"); push (@neworgcodelist, "zSubtotal M");
push (@fundyearlist, "0659"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0659"); push (@orgcodelist, "V"); push (@neworgcodelist, "zSubtotal V");
push (@fundyearlist, "0669"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0679"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0689"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0690"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0700"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0710"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0710"); push (@orgcodelist, "V"); push (@neworgcodelist, "zSubtotal V");
push (@fundyearlist, "0710"); push (@orgcodelist, "L"); push (@neworgcodelist, "zSubtotal L");
push (@fundyearlist, "0720"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0730"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0740"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0750"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0760"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0770"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0780"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0821"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0841"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0851"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0869"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0878"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0888"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0869"); push (@orgcodelist, "D"); push (@neworgcodelist, "zSubtotal D");
push (@fundyearlist, "0878"); push (@orgcodelist, "D"); push (@neworgcodelist, "zSubtotal D");
push (@fundyearlist, "0888"); push (@orgcodelist, "D"); push (@neworgcodelist, "zSubtotal D");
push (@fundyearlist, "0888"); push (@orgcodelist, "M"); push (@neworgcodelist, "zSubtotal M");
push (@fundyearlist, "0878"); push (@orgcodelist, "E"); push (@neworgcodelist, "zSubtotal E");
push (@fundyearlist, "0518"); push (@orgcodelist, "L"); push (@neworgcodelist, "zSubtotal L");
push (@fundyearlist, "0869"); push (@orgcodelist, "L"); push (@neworgcodelist, "zSubtotal L");
push (@fundyearlist, "0878"); push (@orgcodelist, "L"); push (@neworgcodelist, "zSubtotal L");
push (@fundyearlist, "0878"); push (@orgcodelist, "M"); push (@neworgcodelist, "zSubtotal M");
push (@fundyearlist, "0878"); push (@orgcodelist, "P"); push (@neworgcodelist, "zSubtotal P");
push (@fundyearlist, "0878"); push (@orgcodelist, "R"); push (@neworgcodelist, "zSubtotal R");
push (@fundyearlist, "0878"); push (@orgcodelist, "S"); push (@neworgcodelist, "zSubtotal S");
push (@fundyearlist, "0878"); push (@orgcodelist, "V"); push (@neworgcodelist, "zSubtotal V");
push (@fundyearlist, "0897"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0897"); push (@orgcodelist, "B"); push (@neworgcodelist, "zSubtotal B");
push (@fundyearlist, "0897"); push (@orgcodelist, "P"); push (@neworgcodelist, "zSubtotal P");
push (@fundyearlist, "0902"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");

push (@fundyearlist, "0971"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0972"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0974"); push (@orgcodelist, ""); push (@neworgcodelist, "zSubtotal");
push (@fundyearlist, "0974"); push (@orgcodelist, "D"); push (@neworgcodelist, "zSubtotal D");
push (@fundyearlist, "0974"); push (@orgcodelist, "E"); push (@neworgcodelist, "zSubtotal E");
push (@fundyearlist, "0974"); push (@orgcodelist, "J"); push (@neworgcodelist, "zSubtotal J");
push (@fundyearlist, "0974"); push (@orgcodelist, "P"); push (@neworgcodelist, "zSubtotal P");
push (@fundyearlist, "0974"); push (@orgcodelist, "K"); push (@neworgcodelist, "zSubtotal K");
push (@fundyearlist, "0974"); push (@orgcodelist, "L"); push (@neworgcodelist, "zSubtotal L");
push (@fundyearlist, "0974"); push (@orgcodelist, "M"); push (@neworgcodelist, "zSubtotal M");
push (@fundyearlist, "0974"); push (@orgcodelist, "V"); push (@neworgcodelist, "zSubtotal V");
push (@fundyearlist, "0974"); push (@orgcodelist, "Y"); push (@neworgcodelist, "zSubtotal Y");
push (@fundyearlist, "0974"); push (@orgcodelist, "Z"); push (@neworgcodelist, "zSubtotal Z");
push (@fundyearlist, "0386"); push (@orgcodelist, "T%4"); push (@neworgcodelist, "zSubtotal Year4"); # NOTICE THE WILDCARD CHARACTER '%' BETWEEN THE 'T' AND THE '4'
push (@fundyearlist, "0386"); push (@orgcodelist, "T%5"); push (@neworgcodelist, "zSubtotal Year5"); # NOTICE THE WILDCARD CHARACTER '%' BETWEEN THE 'T' AND THE '5'
##########################################
## END: PUSH DATA INTO THE THREE ARRAYS
##########################################

	my $looptotal = $#fundyearlist; # THIS HOLDS THE LAST INDEX NUMBER (NUMBER - 1) OF ENTRIES IN THE ARRAYS ABOVE.

		while ($loopcounter <= $looptotal) {
			my $lastobjclasscodedesc = "";
			my $lastorgcode = "";
			my $lastorgdesc = "";
			my $lastobjclasscode = "";
			my $lastobjclasscodedesc = "";
			my $lastobjclasscodebrief = "";
			my $fundyearholder = "";
			my $fundyeardescholder = ""; 
			my $periodenddateholder = "";
		
			my $fundyear_startdate_holder = "";
			my $fundyear_enddate_holder = "";
			my $orgcode_startdate_holder = "";
			my $orgcode_enddate_holder = "";
		
			## DECLARE VARIABLES FOR TRACKING SUBTOTALS
			my $t_budget = "0";
			my $t_priorexpend = "0";
			my $t_currentexpend = "0";
			my $t_totalexpend = "0";
			my $t_budgetremaining = "0";
			my $t_encumberances = "0";
			my $t_budgetremainingnoencumberances = "0";
		
			my $command = "select * from $databasename where fundyear like '$fundyearlist[$loopcounter]'"; 

			if (($orgcodelist[$loopcounter] ne '') && ($orgcodelist[$loopcounter] !~ '%')) {
				$command .= " AND orgcode like '$orgcodelist[$loopcounter]\%'"; # CODING FOR ORGCODES WITH NO WILDCARD
			}
			if ($orgcodelist[$loopcounter] =~ '%') {
				$command .= " AND orgcode like '$orgcodelist[$loopcounter]'"; # SEPCIAL CODING FOR ORGCODES WITH A WILDCARD
			}

			$command .= " order by fundyear, objclasscodedesc";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
			print "<p class=\"info\">SELECT COMMAND: $command<BR>MATCHES: $num_matches</p>";
	
			while (my @arr = $sth->fetchrow) {
				my ($fundyear, $fundyeardesc, $orgcode, $orgdesc, $objclasscode, $objclasscodedesc, $objclasscodebrief, $budget, $priorexpend, $currentexpend, $totalexpend, $budgetremaining, $encumberances, $budgetremainingnoencumberances, $periodenddate, $fundyear_startdate, $fundyear_enddate, $orgcode_startdate, $orgcode_enddate) = @arr;
				$fundyear_startdate_holder = $fundyear_startdate;
				$fundyear_enddate_holder = $fundyear_enddate;
				$orgcode_startdate_holder = $orgcode_startdate;
				$orgcode_enddate_holder = $orgcode_enddate;
	
				$fundyearholder = $fundyear;
				$fundyeardescholder = $fundyeardesc;
				$periodenddateholder = $periodenddate;
	
				#########################
				# START: INSERT STATEMENT
				$fundyeardescholder =~ s/\'/\\'/g;
			
					if (($objclasscodedesc ne $lastobjclasscodedesc) && ($lastobjclasscodedesc ne '')) {
   						my $command = "INSERT INTO $databasename VALUES ('$fundyearlist[$loopcounter]', '$fundyeardescholder', '$lastorgcode', '$lastorgdesc', '$lastobjclasscode', '$lastobjclasscodedesc', '$lastobjclasscodebrief', '$t_budget', '$t_priorexpend', '$t_currentexpend', '$t_totalexpend', '$t_budgetremaining', '$t_encumberances', '$t_budgetremainingnoencumberances', '$periodenddateholder', '$fundyear_startdate_holder', '$fundyear_enddate_holder', '$orgcode_startdate_holder', '$orgcode_enddate_holder')";
						my $dbh = DBI->connect($dsn, "intranetuser", "limited");
						my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
						$sth->execute;
#	my $num_matches = $sth->rows;

						print "<br><FONT COLOR=BLUE CLASS=small>INSERTION COMMAND: $command<BR>MATCHES: $num_matches</FONT>";
			
						# CLEAN SUBTOTAL VARIABLES
						$t_budget = "0";
						$t_priorexpend = "0";
						$t_currentexpend = "0";
						$t_totalexpend = "0";
						$t_budgetremaining = "0";
						$t_encumberances = "0";
						$t_budgetremainingnoencumberances = "0";
					} # END: INSERT STATEMENT
					#########################
	
				$t_budget = $t_budget + $budget;
				$t_priorexpend = $t_priorexpend + $priorexpend;
				$t_currentexpend = $t_currentexpend + $currentexpend;
				$t_totalexpend = $t_totalexpend + $totalexpend;
				$t_budgetremaining = $t_budgetremaining + $budgetremaining;
				$t_encumberances = $t_encumberances + $encumberances;
				$t_budgetremainingnoencumberances = $t_budgetremainingnoencumberances + $budgetremainingnoencumberances;
	
				$lastobjclasscodedesc = $objclasscodedesc;
				$lastorgcode = $neworgcodelist[$loopcounter];
				$lastorgdesc = $neworgcodelist[$loopcounter];
				$lastobjclasscode = $objclasscode;
				$lastobjclasscodedesc = $objclasscodedesc;
				$lastobjclasscodebrief = $objclasscodebrief;
			} # END DB QUERY
	
		$loopcounter++;

		my $command = "INSERT INTO $databasename VALUES ('$fundyearholder', '$fundyeardescholder', '$lastorgcode', '$lastorgdesc', '$lastobjclasscode', '$lastobjclasscodedesc', '$lastobjclasscodebrief', '$t_budget', '$t_priorexpend', '$t_currentexpend', '$t_totalexpend', '$t_budgetremaining', '$t_encumberances', '$t_budgetremainingnoencumberances', '$periodenddateholder', '$fundyear_startdate_holder', '$fundyear_enddate_holder', '$orgcode_startdate_holder', '$orgcode_enddate_holder')";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#	my $num_matches = $sth->rows;
		print "<br><FONT COLOR=BLUE CLASS=small>INSERT SUBTOTAL COMMAND: $command<BR>MATCHES: $num_matches</FONT>";

		} # END WHILE LOOP FROM 0 TO X
		######################################################################## 
		## END: LOOP THROUGH DATABASE X times to create each subtotal
		######################################################################## 


		$finished = "yes" if ($databasename eq 'oftsbudgetsfy_newmonth');
		$databasename = "oftsbudgetsfy_newmonth"
	} ## END LOOP THROUGH TWICE TO DO SUBTOTALS FOR EACH OF THE TWO DATABASES
######################################################################################################## 
## END: COMPUTE AND INSERT SUBTOTAL ENTRIES INTO 'oftsbudgets_newmonth' and 'oftsbudgetsfy_newmonth'
######################################################################################################## 



######################################################################################################################## 
## START: IF MONTH'S MATCH, UPDATE 'oftsbudgets' and 'oftsbudgetsfy' WITH DATA IN 'oftsbudgets_newmonth' and 'oftsbudgetsfy_newmonth'
######################################################################################################################## 
my $usedb_label = $usedb;
   $usedb_label =~ s/2//gi;
my $dontusedb_label = $dontusedb;
   $dontusedb_label =~ s/2//gi;
if ($relevant_month ne $irrelevant_month) { # IF DATES MATCH
	print "<p class=\"alert\">You uploaded the $usedb_label file.<br>
	The months in the $usedb_label file ($relevant_month) and the 
	$dontusedb_label file ($irrelevant_month) did not match.<br>
	The data push to the live budget report database cannot continue until you upload the $dontusedb_label file for $relevant_month.</p>";
} else {
	print "<p class=\"info\">You uploaded the $usedb_label file.<br>
	The months in the $usedb_label file ($relevant_month) and the $dontusedb_label file ($irrelevant_month) matched.<br>
	<b>Now Initiating the data push</b> to the live budget report database for the month <b>$relevant_month</b>.</p>";
my $finished = "no";
my $databasename = "oftsbudgets_newmonth"; # NAMES: 'oftsbudgets' and 'oftsbudgetsfy'
my $databasename_target = "oftsbudgets"; # NAMES: 'oftsbudgets' and 'oftsbudgetsfy'

	while ($finished eq 'no') {
			my $command = "";
			$command = "select * from $databasename";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
			print "<p class=\"info\">SELECT COMMAND TO UPDATE THE <b>$databasename_target</b> database: $command<br>MATCHING RECORDS: $num_matches</p>";
	
			while (my @arr = $sth->fetchrow) {
				my ($fundyear, $fundyeardesc, $orgcode, $orgdesc, $objclasscode, $objclasscodedesc, $objclasscodebrief, $budget, $priorexpend, $currentexpend, $totalexpend, $budgetremaining, $encumberances, $budgetremainingnoencumberances, $periodenddate, $fundyear_startdate, $fundyear_enddate, $orgcode_startdate, $orgcode_enddate) = @arr;
					$fundyear = &backslash_fordb($fundyear);
					$fundyeardesc = &backslash_fordb($fundyeardesc);
					$orgcode = &backslash_fordb($orgcode);
					$orgdesc = &backslash_fordb($orgdesc);
					$objclasscode = &backslash_fordb($objclasscode);
					$objclasscodedesc = &backslash_fordb($objclasscodedesc);
					$objclasscodebrief = &backslash_fordb($objclasscodebrief);
					$budget = &backslash_fordb($budget);
					$priorexpend = &backslash_fordb($priorexpend);
					$currentexpend = &backslash_fordb($currentexpend);
					$totalexpend = &backslash_fordb($totalexpend);
					$budgetremaining = &backslash_fordb($budgetremaining);
					$encumberances = &backslash_fordb($encumberances);
					$budgetremainingnoencumberances = &backslash_fordb($budgetremainingnoencumberances);
					$periodenddate = &backslash_fordb($periodenddate);
					$fundyear_startdate = &backslash_fordb($fundyear_startdate);
					$fundyear_enddate = &backslash_fordb($fundyear_enddate);
					$orgcode_startdate = &backslash_fordb($orgcode_startdate);
					$orgcode_enddate = &backslash_fordb($orgcode_enddate);

   						my $command = "INSERT INTO $databasename_target VALUES ('$fundyear', '$fundyeardesc', '$orgcode', '$orgdesc', '$objclasscode', '$objclasscodedesc', '$objclasscodebrief', '$budget', '$priorexpend', '$currentexpend', '$totalexpend', '$budgetremaining', '$encumberances', '$budgetremainingnoencumberances', '$periodenddate', '$fundyear_startdate', '$fundyear_enddate', '$orgcode_startdate', '$orgcode_enddate')";
						my $dbh = DBI->connect($dsn, "intranetuser", "limited");
						my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
						$sth->execute;
			} # END DB QUERY
		$finished = "yes" if ($databasename eq 'oftsbudgetsfy_newmonth');
		$databasename = "oftsbudgetsfy_newmonth";
		$databasename_target = "oftsbudgetsfy";
	} ## END LOOP THROUGH TWICE TO DO SUBTOTALS FOR EACH OF THE TWO DATABASES

		# START: DELETE RECORDS WITH BLANK ENDING DATE OF '0000-00-00'
		print "<p class=\"info\">DELETE RECORDS WITH periodenddate of '0000-00-00'";
		my $command_delete_blank = "delete from oftsbudgets WHERE periodenddate = '0000-00-00'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_delete_blank) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		print "<br>$command_delete_blank";

		my $command_delete_blank = "delete from oftsbudgetsfy WHERE periodenddate = '0000-00-00'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_delete_blank) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		print "<br>$command_delete_blank";

		my $command_delete_blank = "delete from oftsbudgetsfy_newmonth WHERE periodenddate = '0000-00-00'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_delete_blank) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		print "<br>$command_delete_blank";

		my $command_delete_blank = "delete from oftsbudgetsfy_newmonth WHERE periodenddate = '0000-00-00'";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_delete_blank) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		print "<br>$command_delete_blank</p>";
		# END: DELETE RECORDS WITH BLANK ENDING DATE OF '0000-00-00'

} # END IF DATES MATCH
######################################################################################################################## 
## END: IF MONTH'S MATCH, UPDATE 'oftsbudgets_newmonth' and 'oftsbudgetsfy_newmonth'
######################################################################################################################## 



###########################
## START: PRINT PAGE FOOTER
###########################
print <<EOM;
</td></tr>
</table>

</BODY>
</HTML>
EOM
###########################
## END: PRINT PAGE FOOTER
###########################


####################################################################
## START: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################
sub backslash_fordb {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\%22/\"/g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/Ô/\\Ô/g;
   $dirtyitem =~ s/Õ/\\Õ/g;
   $dirtyitem =~ s/"/\\"/g;
   return($dirtyitem);
}
####################################################################
## END: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################

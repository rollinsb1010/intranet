#!/usr/bin/perl

#####################################################################################################
# Copyright 2008 by SEDL
#
# This script is activated weekly to send surveys to people who downloaded PDF documents within the last week.
#
# Written by Brian Litke 05-13-2002
#####################################################################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);

use DBI;
my $dsn = "DBI:mysql:database=corp;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

my $query = new CGI;
my $debug = $query->param("debug") || "1"; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS

my $browser = $ENV{"HTTP_USER_AGENT"};
my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};


## START: READ ALL PUBS TITLES INTO A HASH CODED WITH PDF URL
my %publication_title;
my %publication_id;
my $documenttitle = "";


######################################
## PRINT PAGE HEADER FOR DEBUG OUTPUT
######################################
print header;
print <<EOM;
<HTML>
<head>
<title>SURVEY AUTMOATION FIX</title>
</head>
<BODY>
EOM

		################################################################
		##  START: GET DOCUMENT ID AND PDF LOCATION FORM product catalog
		################################################################
 		my $command = "select unique_id, onlineid, title, title2, locpdf from sedlcatalog";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		print "<p class=\"info\">LOADING DOCUMENT TITLES<BR>$command<BR>MATCHES: $num_matches</p>";
		while (my @arr = $sth->fetchrow) {
			my ($unique_id, $onlineid, $title, $title2, $locpdf) = @arr;
			$locpdf =~ s/http\:\/\/www\.sedl\.org//g;
			$documenttitle = "$title";
			$documenttitle .= ": $title2" if $title2 ne '';

 			$publication_title{$locpdf} = $documenttitle;
			$publication_id{$locpdf} = $unique_id;
		} # END DB QUERY
		################################################################
		##  END: GET DOCUMENT ID AND PDF LOCATION FORM product catalog
		################################################################

################################################################################################################################
## START: DB QUERY - SELECT ALL RECORDS AND UPDATE WITH THE PRODUCT ID AND GROUP, IF ANY
################################################################################################################################
my $command = "select recordid, documenturl from clientsurvey where documentid = '0' and documentgroup like ''";

my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

print<<EOM;
<p class=\"info\">
	DATABASE QUERY: $command<BR><BR>MATCHES: $num_matches
</p>
<ol>
EOM

my $this_docgroup = "";
my $this_docid = "";

	while (my @arr = $sth->fetchrow) {
		my ($recordid, $documenturl) = @arr;

		################################################################
		##  START: Check if PDF matches an entry in the product catalog
		################################################################
   		$this_docgroup = "";
    	$this_docgroup = "mathsci" if ($documenturl =~ '/scimast');
    	$this_docgroup = "csr" if ($documenturl =~ 'csr');
    	$this_docgroup = "nclb" if ($documenturl =~ '/rel/NCLBA');
     	$this_docgroup = "nsdc" if ($documenturl =~ 'NSDC');
   		$this_docgroup = "landscape" if ($documenturl =~ 'landscape');
    	$this_docgroup = "landscape" if ($documenturl =~ 'tx-progress');
    	$this_docgroup = "landscape" if ($documenturl =~ 'progress');
	   	$this_docgroup = "reading" if ($documenturl =~ '/read');
	   	$this_docgroup = "rel" if ($documenturl =~ '/rel/');
	   	$this_docgroup = "ws" if ($documenturl =~ '/ws/');

    	$this_docgroup = "afterschool" if ($documenturl =~ 'afterschool');
    	$this_docgroup = "annualreport" if (($documenturl =~ 'annual') && ($documenturl =~ 'report'));
    	$this_docgroup = "annualreport" if ($documenturl =~ '/pubs/ar');
    	$this_docgroup = "change" if ($documenturl =~ '/change/issues');
    	$this_docgroup = "change" if ($documenturl =~ '/pubs/cha');
    	$this_docgroup = "culture" if ($documenturl =~ '/culture');
    	$this_docgroup = "culture" if ($documenturl =~ '/loteced');
    	$this_docgroup = "culture" if ($documenturl =~ '/pubs/lc');
    	$this_docgroup = "culture" if ($documenturl =~ '/pubs/lote');
    	$this_docgroup = "disability" if ($documenturl =~ 'ncddr');
    	$this_docgroup = "disability" if ($documenturl =~ 'researchutilization\.org');
    	$this_docgroup = "family" if ($documenturl =~ '/connections/');
    	$this_docgroup = "family" if ($documenturl =~ '/family');
    	$this_docgroup = "family" if ($documenturl =~ '/pubs/fam');
    	$this_docgroup = "family" if ($documenturl =~ '/prep/');
    	$this_docgroup = "mathsci" if ($documenturl =~ '/classroom-compass');
    	$this_docgroup = "mathsci" if ($documenturl =~ '/ms');
    	$this_docgroup = "mathsci" if ($documenturl =~ '/quick-takes');
    	$this_docgroup = "mathsci" if ($documenturl =~ '/scimath/comp');
    	$this_docgroup = "mathsci" if ($documenturl =~ '/scimath/quick');
    	$this_docgroup = "pasopartners" if ($documenturl =~ '/paso');
    	$this_docgroup = "policy" if ($documenturl =~ '/policy');
   		$this_docgroup = "policy" if ($documenturl =~ '/rel/policy');
    	$this_docgroup = "reading" if ($documenturl =~ '/reading/');
    	$this_docgroup = "reading" if ($documenturl =~ '/pubs/read');
    	$this_docgroup = "research" if ($documenturl =~ 'http://www.tea.state.tx.us/comm/06pd_finalreport.pdf');
    	$this_docgroup = "research" if ($documenturl =~ 'sai_sedlbrieffinal.pdf');
    	$this_docgroup = "research" if ($documenturl =~ '/re/');
    	$this_docgroup = "research" if ($documenturl =~ 'http://www.tea.state.tx.us/opge/progeval/LimitedEnglish/lep_0807.pdf');
    	$this_docgroup = "secac" if ($documenturl =~ 'secac');
    	$this_docgroup = "secc" if ($documenturl =~ 'secc\.sedl');
    	$this_docgroup = "sedlletter" if (($documenturl =~ 'sedletter') | ($documenturl =~ 'sedl-letter'));
    	$this_docgroup = "teaching" if ($documenturl =~ '/pubs/teaching');
    	$this_docgroup = "teaching" if ($documenturl =~ '/pubs/tl');
    	$this_docgroup = "teaching" if ($documenturl =~ '/pubs/tl');
    	$this_docgroup = "teaching" if ($documenturl =~ '/reflection.pdf');
    	$this_docgroup = "technology" if ($documenturl =~ '/tap/');
	   	$this_docgroup = "technology" if ($documenturl =~ '/tapinto/');
    	$this_docgroup = "technology" if ($documenturl =~ '/pubs/tec');
    	$this_docgroup = "txcc" if ($documenturl =~ 'txcc\.sedl');

		$documenturl =~ s/http\:\/\/www\.sedl\.org//g;
  		$this_docid = $publication_id{$documenturl};
  		$this_docid = 336 if ($documenturl =~ 'myths');
  		$this_docid = 54 if ($documenturl =~ 'selfinventory');
  		$this_docid = 514 if ($documenturl =~ 'SEDLLetter_v20n02.pdf');
  		$this_docid = 519 if ($documenturl =~ '/ws/');
   		$this_docid = 291 if ($documenturl =~ '\/pubs\/family30\/family_involvement.pdf');
   		$this_docid = 345 if ($documenturl =~ 'CCmarch2002.pdf');
   		$this_docid = 352 if ($documenturl =~ 'qt0702.pdf');
   		$this_docid = 311 if ($documenturl =~ 'family91\/parentsguide.pdf');
   		$this_docid = 204 if ($documenturl =~ 'winter98\/TAP.pdf');
   		$this_docid = 234 if ($documenturl =~ 'compass51.pdf');
   		$this_docid = 471 if ($documenturl =~ 'SEDLLetter_v18n03.pdf');
   		$this_docid = 426 if ($documenturl =~ 'SEDLLetter_v17n01.pdf');
   		$this_docid = 388 if ($documenturl =~ 'ocus3.pdf');
   		$this_docid = 390 if ($documenturl =~ 'ocus5.pdf');
   		$this_docid = 397 if ($documenturl =~ 'ocus8.pdf');

   		$this_docid = 208 if ($documenturl =~ 'cc_v1n2.pdf');
   		$this_docid = 209 if ($documenturl =~ 'cc_v1n3.pdf');
   		$this_docid = 211 if ($documenturl =~ 'cc_v2n2.pdf');
   		$this_docid = 214 if ($documenturl =~ 'cc_v3n2.pdf');
   		$this_docid = 215 if ($documenturl =~ 'cc_v4n1.pdf');
    	$this_docid = 216 if ($documenturl =~ 'cc_v4n2.pdf');
  		$this_docid = 345 if ($documenturl =~ 'cc_v5n2.pdf');
   		$this_docid = 427 if ($documenturl =~ 'cc_v5n3.pdf');
   		$this_docid = 191 if ($documenturl =~ 'insights09.pdf');
   		$this_docid = 192 if ($documenturl =~ 'insights10.pdf');
  		$this_docid = 188 if ($documenturl =~ 'qt_timss.pdf');

 		$this_docid = 395 if ($documenturl =~ 'REv8n2.pdf');
 		$this_docid = 396 if ($documenturl =~ 'REv8n3.pdf');
 		$this_docid = 362 if ($documenturl =~ 'REV6N2.pdf');
 		$this_docid = 363 if ($documenturl =~ 'REV6N3.pdf');
		$this_docid = 364 if ($documenturl =~ 'REV7N1.pdf');
		$this_docid = 412 if ($documenturl =~ 'ICYguide.pdf');
		$this_docid = 250 if ($documenturl =~ 'assessment3.pdf');
		$this_docid = 203 if ($documenturl =~ 'TAP_2.pdf');


#   		$this_docid = 610 if ($this_docid eq '');

		my $command = "UPDATE clientsurvey SET documentid = '$this_docid', documentgroup = '$this_docgroup' WHERE recordid like '$recordid'";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
if (($this_docid ne '') && ($this_docgroup ne '')) {
	print "<li>($documenturl)<br>";
	print "<span style=\"color:#009900\">$command</span></li>";
#} elsif ($this_docgroup =~ 'landscape') {
#} elsif ($this_docgroup =~ 'annualreport') {
#} elsif ($this_docgroup =~ 'txcc') {
#} elsif ($documenturl =~ 'connections') {
#} elsif ($documenturl =~ '40years') {
#} elsif ($documenturl =~ 'loteced') {
#} elsif ($documenturl =~ 'policydocs') {
#} elsif ($documenturl =~ 'rel') {
#} elsif ($documenturl =~ 'secac') {
#} elsif ($documenturl =~ 'rel/NCLB') {
	print "<li>($documenturl)<br>";
	print "<span style=\"color:#CCcccc\">$command</span></li>";
} elsif (($this_docid eq '') && ($this_docgroup eq '')) {
	print "<li>($documenturl)<br>";
	print "<span style=\"color:#CC0000\">$command</span></li>";
} else {
	print "<li>($documenturl)<br>";
	print "<span style=\"color:#F87217\">$command</span></li>";
}
		################################################################
		##  END: Check if PDF matches an entry in the product catalog
		################################################################

	} ## END DATABASE LOOP
print "</ol>";

################################################################################################################################
## END: DB QUERY - SELECT ALL RECORDS FOR PDFS THAT ARE OVER A WEEK OLD BUT HAVEN'T BEEN SENT A SURVEY
################################################################################################################################








#!/usr/bin/perl 

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


my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
$|=1;
my $count = 0;
my $query = new CGI;

	my $ipnum = $ENV{"REMOTE_HOST"};
	my $ipnum2 = $ENV{"REMOTE_ADDR"};
	my $browser = $ENV{"HTTP_USER_AGENT"};

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("112"); # 112 is the PID for this page in the intranet database

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
	my $this_user_id = "$timestamp$ipnum$ipnum2";
	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################



####################################################################
## START: LOG STAFF ID TO DATABASE TO TRACK USAGE OF THIS RESOURCE
####################################################################
## COOKIE VARIABLES
my $expdate = "Fri, 25-Dec-2015 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";

my $user = param('user');

if ($user eq '') {
my(%cookies) = getCookies();

foreach (sort(keys(%cookies))) {
$user = $cookies{$_} if (($_ eq 'staffid') && ($user eq ''));
}

} # END OF COOKIE CHECK

## IF STAFF USER ID IS PRESENT IN COOKIE, LOG THEIR USE OF THIS TOOL TO THE TRACKING DATABASE
if ($user ne '') {
	my $commandinsert = "INSERT INTO staffpageusage VALUES ('$user', '$date_full_mysql', 'IRC Resource Catalog')";
	my $dsn = "DBI:mysql:database=test;host=localhost";
	my $dbh = DBI->connect($dsn, "", "");
	my $sth = $dbh->prepare($commandinsert) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;

}
####################################################################
## END: LOG STAFF ID TO DATABASE TO TRACK USAGE OF THIS RESOURCE
####################################################################



##################################################################
## START: GET THE SEARCH VARIABLES FROM THE HTML SEARCH FORM
##################################################################
my $camefrom = $query->param("camefrom");
my $sortby = $query->param("sortby");
my $searchfor = $query->param("searchfor");
my $searchfor2 = $query->param("searchfor2");
my $searchfor3 = $query->param("searchfor3");
my $lastpublisher = "";

my $searchfor_forlink = $searchfor;
my $searchfor2_forlink = $searchfor2;
my $searchfor3_forlink = $searchfor3;
   $searchfor_forlink =~ s/ /\+/;
   $searchfor2_forlink =~ s/ /\+/;
   $searchfor3_forlink =~ s/ /\+/;

my $search_date = $query->param("search_date");
my $search_date_type = $query->param("search_date_type");
	if ($search_date ne '') {
		$search_date_type = "after";
	}
## START: VARIABLE FOR SHOWING X-RECORDS PER PAGE
my $startrecord = $query->param("startrecord") || "1";
my $resultsperpage = $query->param("resultsperpage");
   $resultsperpage = "200" if ($resultsperpage eq '');
my $endrecord = "";
   $endrecord = $startrecord + 49 if $resultsperpage eq '50';
   $endrecord = $startrecord + 99 if $resultsperpage eq '100';
   $endrecord = $startrecord + 199 if $resultsperpage eq '200';
   $endrecord = $startrecord + 499 if $resultsperpage eq '500';
## END: VARIABLE FOR SHOWING X-RECORDS PER PAGE

##################################################################
## END: GET THE SEARCH VARIABLES FROM THE HTML SEARCH FORM
##################################################################


## SET SEDL LINKS TO IRC "HOME PAGE" AND "LOCATION CODES"
my $homepage = "/cgi-bin/mysql/staff/index.cgi?show_s=2&show_sg=36&pid=112";
my $locationcodes = "/staff/information/resources/locationcodes.html";



## CLEAN UP SEARCHFOR STRING AND SPLIT INTO AS MANY AS 6 KEYWORDS TO SEARCH FOR
$searchfor =~ tr/A-Z/a-z/;
$searchfor =~ s/,/ /;
my ($sf1, $sf2, $sf3, $sf4, $sf5, $sf6) = split(/ /,$searchfor);

$searchfor2 =~ tr/A-Z/a-z/;
$searchfor2 =~ s/,/ /;
my ($sf1b, $sf2b, $sf3b, $sf4b, $sf5b, $sf6b) = split(/ /,$searchfor2);

$searchfor3 =~ tr/A-Z/a-z/;
$searchfor3 =~ s/,/ /;
my ($sf1c, $sf2c, $sf3c, $sf4c, $sf5c, $sf6c) = split(/ /,$searchfor3);


my $sfa1 = $sf1;
my $sfa2 = $sf2;
my $sfa3 = $sf3;
my $sfa4 = $sf4;
my $sfa5 = $sf5;
my $sfa6 = $sf6;
   $sfa1 = ucfirst($sfa1) if $sfa1;
   $sfa2 = ucfirst($sfa2) if $sfa2;
   $sfa3 = ucfirst($sfa3) if $sfa3;
   $sfa4 = ucfirst($sfa4) if $sfa4;
   $sfa5 = ucfirst($sfa5) if $sfa5;
   $sfa6 = ucfirst($sfa6) if $sfa6;

my $sfb1 = $sf1b;
my $sfb2 = $sf2b;
my $sfb3 = $sf3b;
my $sfb4 = $sf4b;
my $sfb5 = $sf5b;
my $sfb6 = $sf6b;
   $sfb1 = ucfirst($sfb1) if $sfb1;
   $sfb2 = ucfirst($sfb2) if $sfb2;
   $sfb3 = ucfirst($sfb3) if $sfb3;
   $sfb4 = ucfirst($sfb4) if $sfb4;
   $sfb5 = ucfirst($sfb5) if $sfb5;
   $sfb6 = ucfirst($sfb6) if $sfb6;


my $sfc1 = $sf1c;
my $sfc2 = $sf2c;
my $sfc3 = $sf3c;
my $sfc4 = $sf4c;
my $sfc5 = $sf5c;
my $sfc6 = $sf6c;
   $sfc1 = ucfirst($sfc1) if $sfc1;
   $sfc2 = ucfirst($sfc2) if $sfc2;
   $sfc3 = ucfirst($sfc3) if $sfc3;
   $sfc4 = ucfirst($sfc4) if $sfc4;
   $sfc5 = ucfirst($sfc5) if $sfc5;
   $sfc6 = ucfirst($sfc6) if $sfc6;


	$sf1 = &backslash_fordb($sf1);
	$sf2 = &backslash_fordb($sf2);
	$sf3 = &backslash_fordb($sf3);
	$sf4 = &backslash_fordb($sf4);
	$sf5 = &backslash_fordb($sf5);
	$sf6 = &backslash_fordb($sf6);

	$sfa1 = &backslash_fordb($sfa1);
	$sfa2 = &backslash_fordb($sfa2);
	$sfa3 = &backslash_fordb($sfa3);
	$sfa4 = &backslash_fordb($sfa4);
	$sfa5 = &backslash_fordb($sfa5);
	$sfa6 = &backslash_fordb($sfa6);

	$sfb1 = &backslash_fordb($sfb1);
	$sfb2 = &backslash_fordb($sfb2);
	$sfb3 = &backslash_fordb($sfb3);
	$sfb4 = &backslash_fordb($sfb4);
	$sfb5 = &backslash_fordb($sfb5);
	$sfb6 = &backslash_fordb($sfb6);

	$sfc1 = &backslash_fordb($sfc1);
	$sfc2 = &backslash_fordb($sfc2);
	$sfc3 = &backslash_fordb($sfc3);
	$sfc4 = &backslash_fordb($sfc4);
	$sfc5 = &backslash_fordb($sfc5);
	$sfc6 = &backslash_fordb($sfc6);

	$sf1b = &backslash_fordb($sf1b);
	$sf2b = &backslash_fordb($sf2b);
	$sf3b = &backslash_fordb($sf3b);
	$sf4b = &backslash_fordb($sf4b);
	$sf5b = &backslash_fordb($sf5b);
	$sf6b = &backslash_fordb($sf6b);

	$sf1c = &backslash_fordb($sf1c);
	$sf2c = &backslash_fordb($sf2c);
	$sf3c = &backslash_fordb($sf3c);
	$sf4c = &backslash_fordb($sf4c);
	$sf5c = &backslash_fordb($sf5c);
	$sf6c = &backslash_fordb($sf6c);

	$search_date = &backslash_fordb($search_date);

## READ IN THE BOOLEAN AND/OR FOR THE SEARCH BY KEYWORD
my $andor = $query->param("andor");
   $andor = "and" if ($andor eq "");
   $andor = "and" if ($andor eq "all");
   $andor = "or" if ($andor eq "any");

my $andor2 = $query->param("andor2");
   $andor2 = "and" if ($andor2 eq "");
   $andor2 = "and" if ($andor2 eq "all");
   $andor2 = "or" if ($andor2 eq "any");

my $andor3 = $query->param("andor3");
   $andor3 = "and" if ($andor3 eq "");
   $andor3 = "and" if ($andor3 eq "all");
   $andor3 = "or" if ($andor3 eq "any");

	$andor = &backslash_fordb($andor);
	$andor2 = &backslash_fordb($andor2);
	$andor3 = &backslash_fordb($andor3);

## WAIT TIME FOR REDIRECT VALUE ON THE "NO RECORDS MATCHED YOUR SEARCH" PAGE
my $wait ="10";
$wait ="100" if $debug;


# CHANGE THE SORTBY VARIABLE TAKEN FROM THE HTML FORM TO THE FIELD NAME TO SORT BY
my $sortbyheader = $sortby;
$sortby = "title" if $sortby eq "";
$sortby = "datepub" if $sortby eq "date";
$sortby = "authors , title" if $sortby eq "authors";
$sortby = "publisher , title" if $sortby eq "publisher";



###############################################################
## START THE OUTPUT BY CREATING THE HTML HEADER AND PAGE TITLE
###############################################################
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL - Information Resource Center - Resource Catalog</TITLE>
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function Start(u, l, t, w, h)  {
var windowprops = "location=no,scrollbars=no,menubars=no,toolbars=no,resizable=no" +
",left=" + l + ",top=" + t + ",width=" + w + ",height=" + h;

window.open(u,"popup",windowprops);
}
// End -->
</SCRIPT>
$htmlhead
EOM

#   DEBUG STATEMENT
print "DID THE USER ASK TO SEARCH BY KEYWORD?<UL>searchingforword1:$sf1<BR> searchingforword2:$sf2<BR> searchingforword3:$sf3<BR> searchingfor4:$sf4<BR> searchingforword5:$sf5<BR> searchingforword6:$sf6</UL>" if $debug;



## CONSTRUCT THE SQL QUERY

my $command = "select * from irc_catalog where ((title NOT LIKE '')";



if ($camefrom eq 'quick') {
$command .= " and ((title LIKE \'%$sf1%\' or title_aka LIKE \'%$sf1%\' or publisher LIKE \'%$sf1%\' or authors LIKE \'%$sf1%\' or subjectheadings LIKE \'%$sf1%\')" if $sf1;
$command .= " $andor (title LIKE \'%$sf2%\' or title_aka LIKE \'%$sf2%\' or publisher LIKE \'%$sf2%\' or authors LIKE \'%$sf2%\' or subjectheadings LIKE \'%$sf2%\')" if $sf2;
$command .= " $andor (title LIKE \'%$sf3%\' or title_aka LIKE \'%$sf3%\' or publisher LIKE \'%$sf3%\' or authors LIKE \'%$sf3%\' or subjectheadings LIKE \'%$sf3%\')" if $sf3;
$command .= " $andor (title LIKE \'%$sf4%\' or title_aka LIKE \'%$sf4%\' or publisher LIKE \'%$sf4%\' or authors LIKE \'%$sf4%\' or subjectheadings LIKE \'%$sf4%\')" if $sf4;
$command .= " $andor (title LIKE \'%$sf5%\' or title_aka LIKE \'%$sf5%\' or publisher LIKE \'%$sf5%\' or authors LIKE \'%$sf5%\' or subjectheadings LIKE \'%$sf5%\')" if $sf5;
$command .= " $andor (title LIKE \'%$sf6%\' or title_aka LIKE \'%$sf6%\' or publisher LIKE \'%$sf6%\' or authors LIKE \'%$sf6%\' or subjectheadings LIKE \'%$sf6%\')" if $sf6;
$command .= ")" if $sf1;

} else {
$command .= " and ((title LIKE \'%$sf1%\' or title_aka LIKE \'%$sf1%\' or subjectheadings LIKE \'%$sf1%\')" if $sf1;
$command .= " $andor (title LIKE \'%$sf2%\' or title_aka LIKE \'%$sf2%\' or subjectheadings LIKE \'%$sf2%\')" if $sf2;
$command .= " $andor (title LIKE \'%$sf3%\' or title_aka LIKE \'%$sf3%\' or subjectheadings LIKE \'%$sf3%\')" if $sf3;
$command .= " $andor (title LIKE \'%$sf4%\' or title_aka LIKE \'%$sf4%\' or subjectheadings LIKE \'%$sf4%\')" if $sf4;
$command .= " $andor (title LIKE \'%$sf5%\' or title_aka LIKE \'%$sf5%\' or subjectheadings LIKE \'%$sf5%\')" if $sf5;
$command .= " $andor (title LIKE \'%$sf6%\' or title_aka LIKE \'%$sf6%\' or subjectheadings LIKE \'%$sf6%\')" if $sf6;
$command .= ")" if $sf1;

$sf1b =~ s/\\\'/\%/gi;
$sf2b =~ s/\\\'/\%/gi;
$sf3b =~ s/\\\'/\%/gi;
$sf4b =~ s/\\\'/\%/gi;
$sf5b =~ s/\\\'/\%/gi;
$sf6b =~ s/\\\'/\%/gi;

$command .= " and ((authors LIKE \'%$sf1b%\')" if $sf1b;
$command .= " $andor (authors LIKE \'%$sf2b%\')" if $sf2b;
$command .= " $andor (authors LIKE \'%$sf3b%\')" if $sf3b;
$command .= " $andor (authors LIKE \'%$sf4b%\')" if $sf4b;
$command .= " $andor (authors LIKE \'%$sf5b%\')" if $sf5b;
$command .= " $andor (authors LIKE \'%$sf6b%\')" if $sf6b;
$command .= ")" if $sf1b;

$command .= " and ((publisher LIKE \'%$sf1c%\')" if $sf1c;
$command .= " $andor (publisher LIKE \'%$sf2c%\')" if $sf2c;
$command .= " $andor (publisher LIKE \'%$sf3c%\')" if $sf3c;
$command .= " $andor (publisher LIKE \'%$sf4c%\')" if $sf4c;
$command .= " $andor (publisher LIKE \'%$sf5c%\')" if $sf5c;
$command .= " $andor (publisher LIKE \'%$sf6c%\')" if $sf6c;
$command .= ")" if $sf1c;
}
$command .= ") ";
$command .= " AND datepub >= '$search_date'" if ($search_date ne '');
$command .= " order by $sortby";


#print "<P>COMMAND TO DATABASE WAS:<UL>$command</UL>";

## OPEN THE DATABASE AND SEND THE QUERY
my $dsn = "DBI:mysql:database=intranet;host=localhost";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

if ($num_matches gt '0') {
## Print a message on the results page showing what results are sorted by
print<<EOM;
<table>
<tr><td VALIGN=\"TOP\">
<p><strong>Displaying $num_matches 
EOM
print "entries " if ($num_matches eq '0'); 
print "entry " if ($num_matches eq '1');
print "entries " if ($num_matches gt '1'); 
print<<EOM;
from the <a href=\"$homepage\">Resource Catalog</a> sorted by $sortby</strong>
</p>
<p>
Click here for definitions of the <a href=\"javascript:Start\(\'$locationcodes\', 100, 100, 580, 460)\"\;>resource location codes</A>.
</p>
	</TD>
</tr>
</table>
EOM
}

&print_results_range($num_matches, $resultsperpage, $startrecord);

#   DEBUG STATEMENT
#print pre("command: $command\n"),"\n" if $debug;

## GET THE RESULTS OF THE QUERY
while (my @arr = $sth->fetchrow) {
    $count++;
    my ($resource_id, $title, $title_aka, $authors, $publisher, $datepub, $edition, $pub_frequency, $series_subseries, $time_period_covered, $subjectheadings, $location, $call_number, $catalog_url, $eric_number, $newbookslist, $resource_format) = @arr;



	if (($count >= $startrecord) && ($count <= $endrecord)) {

	########################################################
	## START: MAKE LIST OF AUTHORS WITH LINKS FOR LAST NAMES
	########################################################
	my $replace_lastname = "";
	my $authors_linked = $authors;
	my @authors = split (/\;/,$authors);
	my $counter = 0;
		my %authors_completed;
		foreach (@authors) {
#			print "<br>Examining author: $authors[$counter]";
			my @author_parts = split (/ /,$authors[$counter]);
#			print "<br>First Part of Author name: $author_parts[0] and $author_parts[1] and $author_parts[2]";
			if ($author_parts[0] ne '') {
				$replace_lastname = $author_parts[0];
				$replace_lastname =~ s/\,//gi;
				$replace_lastname =~ s/\.//gi;
				$replace_lastname =~ s/\[//gi;
				$replace_lastname =~ s/\]//gi;
#				print "<font color=red>$replace_lastname</font>";
				## ADD LINK FOR AUTHOR LAST NAME, IF NOT ALREADY DONE FOR THIS PUBLICAITON
				if (($authors_completed{$replace_lastname} ne 'yes') && (length($replace_lastname) > 2)) {
					$authors_linked =~ s/$replace_lastname/\<a href\=\"http\:\/\/www\.sedl\.org\/staff\/information\/resources\/irc_catalog\.cgi\?searchfor2\=$replace_lastname\"\>$replace_lastname\<\/a\>/gi;
				}
				$authors_completed{$replace_lastname} = "yes";
			} elsif ($author_parts[1] ne '') {
				$replace_lastname = $author_parts[1];
				$replace_lastname =~ s/\,//gi;
				$replace_lastname =~ s/\.//gi;
				$replace_lastname =~ s/\[//gi;
				$replace_lastname =~ s/\]//gi;
				## ADD LINK FOR AUTHOR LAST NAME, IF NOT ALREADY DONE FOR THIS PUBLICAITON
				if (($authors_completed{$replace_lastname} ne 'yes') && (length($replace_lastname) > 2)) {
					$authors_linked =~ s/$replace_lastname/\<a href\=\"http\:\/\/www\.sedl\.org\/staff\/information\/resources\/irc_catalog\.cgi\?searchfor2\=$replace_lastname\"\>$replace_lastname\<\/a\>/gi;
				}
				$authors_completed{$replace_lastname} = "yes";
			} elsif ($author_parts[2] ne '') {
				$replace_lastname = $author_parts[2];
				$replace_lastname =~ s/\,//gi;
				$replace_lastname =~ s/\.//gi;
				$replace_lastname =~ s/\[//gi;
				$replace_lastname =~ s/\]//gi;
				## ADD LINK FOR AUTHOR LAST NAME, IF NOT ALREADY DONE FOR THIS PUBLICAITON
				if (($authors_completed{$replace_lastname} ne 'yes') && (length($replace_lastname) > 2)) {
					$authors_linked =~ s/$replace_lastname/\<a href\=\"http\:\/\/www\.sedl\.org\/staff\/information\/resources\/irc_catalog\.cgi\?searchfor2\=$replace_lastname\"\>$replace_lastname\<\/a\>/gi;
				}
				$authors_completed{$replace_lastname} = "yes";
			}
			$counter++;
			
		}
	########################################################
	## END: MAKE LIST OF AUTHORS WITH LINKS FOR LAST NAMES
	########################################################


		if ($sfa1 ne '') {
			$title =~ s/$sf1/<FONT COLOR=RED><B>$sf1<\/B><\/FONT>/g if $sf1;
			$title =~ s/$sf2/<FONT COLOR=RED><B>$sf2<\/B><\/FONT>/g if $sf2;
			$title =~ s/$sf3/<FONT COLOR=RED><B>$sf3<\/B><\/FONT>/g if $sf3;
			$title =~ s/$sf4/<FONT COLOR=RED><B>$sf4<\/B><\/FONT>/g if $sf4;
			$title =~ s/$sf5/<FONT COLOR=RED><B>$sf5<\/B><\/FONT>/g if $sf5;
			$title =~ s/$sf6/<FONT COLOR=RED><B>$sf6<\/B><\/FONT>/g if $sf6;

			$subjectheadings =~ s/$sf1/<FONT COLOR=RED><B>$sf1<\/B><\/FONT>/g if $sf1;
			$subjectheadings =~ s/$sf2/<FONT COLOR=RED><B>$sf2<\/B><\/FONT>/g if $sf2;
			$subjectheadings =~ s/$sf3/<FONT COLOR=RED><B>$sf3<\/B><\/FONT>/g if $sf3;
			$subjectheadings =~ s/$sf4/<FONT COLOR=RED><B>$sf4<\/B><\/FONT>/g if $sf4;
			$subjectheadings =~ s/$sf5/<FONT COLOR=RED><B>$sf5<\/B><\/FONT>/g if $sf5;
			$subjectheadings =~ s/$sf6/<FONT COLOR=RED><B>$sf6<\/B><\/FONT>/g if $sf6;

			$authors_linked =~ s/$sf1/<FONT COLOR=RED><B>$sf1<\/B><\/FONT>/g if $sf1;
			$authors_linked =~ s/$sf2/<FONT COLOR=RED><B>$sf2<\/B><\/FONT>/g if $sf2;
			$authors_linked =~ s/$sf3/<FONT COLOR=RED><B>$sf3<\/B><\/FONT>/g if $sf3;
			$authors_linked =~ s/$sf4/<FONT COLOR=RED><B>$sf4<\/B><\/FONT>/g if $sf4;
			$authors_linked =~ s/$sf5/<FONT COLOR=RED><B>$sf5<\/B><\/FONT>/g if $sf5;
			$authors_linked =~ s/$sf6/<FONT COLOR=RED><B>$sf6<\/B><\/FONT>/g if $sf6;

			$title =~ s/$sfa1/<FONT COLOR=RED><B>$sfa1<\/B><\/FONT>/g if $sfa1;
			$title =~ s/$sfa2/<FONT COLOR=RED><B>$sfa2<\/B><\/FONT>/g if $sfa2;
			$title =~ s/$sfa3/<FONT COLOR=RED><B>$sfa3<\/B><\/FONT>/g if $sfa3;
			$title =~ s/$sfa4/<FONT COLOR=RED><B>$sfa4<\/B><\/FONT>/g if $sfa4;
			$title =~ s/$sfa5/<FONT COLOR=RED><B>$sfa5<\/B><\/FONT>/g if $sfa5;
			$title =~ s/$sfa6/<FONT COLOR=RED><B>$sfa6<\/B><\/FONT>/g if $sfa6;

			$subjectheadings =~ s/$sfa1/<FONT COLOR=RED><B>$sfa1<\/B><\/FONT>/g if $sfa1;
			$subjectheadings =~ s/$sfa2/<FONT COLOR=RED><B>$sfa2<\/B><\/FONT>/g if $sfa2;
			$subjectheadings =~ s/$sfa3/<FONT COLOR=RED><B>$sfa3<\/B><\/FONT>/g if $sfa3;
			$subjectheadings =~ s/$sfa4/<FONT COLOR=RED><B>$sfa4<\/B><\/FONT>/g if $sfa4;
			$subjectheadings =~ s/$sfa5/<FONT COLOR=RED><B>$sfa5<\/B><\/FONT>/g if $sfa5;
			$subjectheadings =~ s/$sfa6/<FONT COLOR=RED><B>$sfa6<\/B><\/FONT>/g if $sfa6;

			$authors =~ s/$sfa1/<FONT COLOR=RED><B>$sfa1<\/B><\/FONT>/g if $sfa1;
			$authors =~ s/$sfa2/<FONT COLOR=RED><B>$sfa2<\/B><\/FONT>/g if $sfa2;
			$authors =~ s/$sfa3/<FONT COLOR=RED><B>$sfa3<\/B><\/FONT>/g if $sfa3;
			$authors =~ s/$sfa4/<FONT COLOR=RED><B>$sfa4<\/B><\/FONT>/g if $sfa4;
			$authors =~ s/$sfa5/<FONT COLOR=RED><B>$sfa5<\/B><\/FONT>/g if $sfa5;
			$authors =~ s/$sfa6/<FONT COLOR=RED><B>$sfa6<\/B><\/FONT>/g if $sfa6;
		}

		if ($sfb1 ne '') {
			$authors_linked =~ s/$sf1b/<FONT COLOR=RED><B>$sf1b<\/B><\/FONT>/g if $sf1b;
			$authors_linked =~ s/$sf2b/<FONT COLOR=RED><B>$sf2b<\/B><\/FONT>/g if $sf2b;
			$authors_linked =~ s/$sf3b/<FONT COLOR=RED><B>$sf3b<\/B><\/FONT>/g if $sf3b;
			$authors_linked =~ s/$sf4b/<FONT COLOR=RED><B>$sf4b<\/B><\/FONT>/g if $sf4b;
			$authors_linked =~ s/$sf5b/<FONT COLOR=RED><B>$sf5b<\/B><\/FONT>/g if $sf5b;
			$authors_linked =~ s/$sf6b/<FONT COLOR=RED><B>$sf6b<\/B><\/FONT>/g if $sf6b;

			$authors_linked =~ s/$sfb1/<FONT COLOR=RED><B>$sfb1<\/B><\/FONT>/g if $sfb1;
			$authors_linked =~ s/$sfb2/<FONT COLOR=RED><B>$sfb2<\/B><\/FONT>/g if $sfb2;
			$authors_linked =~ s/$sfb3/<FONT COLOR=RED><B>$sfb3<\/B><\/FONT>/g if $sfb3;
			$authors_linked =~ s/$sfb4/<FONT COLOR=RED><B>$sfb4<\/B><\/FONT>/g if $sfb4;
			$authors_linked =~ s/$sfb5/<FONT COLOR=RED><B>$sfb5<\/B><\/FONT>/g if $sfb5;
			$authors_linked =~ s/$sfb6/<FONT COLOR=RED><B>$sfb6<\/B><\/FONT>/g if $sfb6;


			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sf1b<\/B><\/FONT>/\=$sf1b/g if $sf1b;
			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sf2b<\/B><\/FONT>/\=$sf2b/g if $sf2b;
			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sf3b<\/B><\/FONT>/\=$sf3b/g if $sf3b;
			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sf4b<\/B><\/FONT>/\=$sf4b/g if $sf4b;
			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sf5b<\/B><\/FONT>/\=$sf5b/g if $sf5b;
			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sf6b<\/B><\/FONT>/\=$sf6b/g if $sf6b;

			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sfb1<\/B><\/FONT>/\=$sfb1/g if $sfb1;
			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sfb2<\/B><\/FONT>/\=$sfb2/g if $sfb2;
			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sfb3<\/B><\/FONT>/\=$sfb3/g if $sfb3;
			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sfb4<\/B><\/FONT>/\=$sfb4/g if $sfb4;
			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sfb5<\/B><\/FONT>/\=$sfb5/g if $sfb5;
			$authors_linked =~ s/\=<FONT COLOR=RED><B>$sfb6<\/B><\/FONT>/\=$sfb6/g if $sfb6;
		}

		if ($sfc1 ne '') {
			$publisher =~ s/$sf1c/<FONT COLOR=RED><B>$sf1c<\/B><\/FONT>/g if $sf1c;
			$publisher =~ s/$sf2c/<FONT COLOR=RED><B>$sf2c<\/B><\/FONT>/g if $sf2c;
			$publisher =~ s/$sf3c/<FONT COLOR=RED><B>$sf3c<\/B><\/FONT>/g if $sf3c;
			$publisher =~ s/$sf4c/<FONT COLOR=RED><B>$sf4c<\/B><\/FONT>/g if $sf4c;
			$publisher =~ s/$sf5c/<FONT COLOR=RED><B>$sf5c<\/B><\/FONT>/g if $sf5c;
			$publisher =~ s/$sf6c/<FONT COLOR=RED><B>$sf6c<\/B><\/FONT>/g if $sf6c;

			$publisher =~ s/$sfc1/<FONT COLOR=RED><B>$sfc1<\/B><\/FONT>/g if $sfc1;
			$publisher =~ s/$sfc2/<FONT COLOR=RED><B>$sfc2<\/B><\/FONT>/g if $sfc2;
			$publisher =~ s/$sfc3/<FONT COLOR=RED><B>$sfc3<\/B><\/FONT>/g if $sfc3;
			$publisher =~ s/$sfc4/<FONT COLOR=RED><B>$sfc4<\/B><\/FONT>/g if $sfc4;
			$publisher =~ s/$sfc5/<FONT COLOR=RED><B>$sfc5<\/B><\/FONT>/g if $sfc5;
			$publisher =~ s/$sfc6/<FONT COLOR=RED><B>$sfc6<\/B><\/FONT>/g if $sfc6;
		}


		##  DISPLAY MATCHING RESOURCES IN INDIVIDUAL TABLES
		if ($sortby eq 'publisher , subjectheadings') {
			print "<H4>$publisher</H4>\n" if $publisher ne $lastpublisher;
			$lastpublisher = $publisher;
		}

$title = &cleanaccents2html($title);
$authors_linked = &cleanaccents2html($authors_linked);
$publisher = &cleanaccents2html($publisher);
$subjectheadings = &cleanaccents2html($subjectheadings);
	$resource_format =~ s//\, /g;

print <<EOM;
<table width="100%" border="0" cellspacing="0" cellpadding="8" ALIGN=CENTER>
<TR><TD VALIGN="TOP" NOWRAP ROWSPAN="13">$count of $num_matches</TD>
	<TD><P><B>$title</B>
EOM
print "<BR>AKA: $title_aka" if ($title_aka ne '');


print<<EOM;
<BR><IMG SRC="/images/spacer.gif" HEIGHT="1" WIDTH="545">
<TABLE BORDER="0" CELLPADDING="3" CELLSPACING="0">
<TR><TD>Author(s):<BR><IMG SRC="/images/spacer.gif" HEIGHT="1" WIDTH="100"></TD><TD>$authors_linked</TD></TR>
EOM
my $catalog_url_text = $catalog_url;
	if (length($catalog_url_text) > 50) {
		my $first = substr($catalog_url_text, 0, 50);
		my $second = substr($catalog_url_text, 51, length($catalog_url_text));
		$catalog_url_text = "$first <BR> $second";
	}
	$catalog_url = "http://$catalog_url" if (($catalog_url =~ 'www') && ($catalog_url !~ 'http'));
print "<TR><TD valign=\"top\">Publisher:</TD><TD> $publisher</TD></TR>" if ($publisher ne '');
print "<TR><TD valign=\"top\">Location:</TD><TD> <B>$location</B></TD></TR>" if ($location ne '');
print "<TR><TD VALIGN=\"TOP\">Date published:</TD><TD VALIGN=\"TOP\">$datepub</TD></TR>" if ($datepub ne '');
print "<TR><TD VALIGN=\"TOP\">Edition:</TD><TD VALIGN=\"TOP\"> $edition</TD></TR>" if ($edition ne '');
print "<TR><TD VALIGN=\"TOP\">Publication frequency:</TD><TD VALIGN=\"TOP\"> $pub_frequency</TD></TR>" if ($pub_frequency ne '');
print "<TR><TD VALIGN=\"TOP\">Series:</TD><TD VALIGN=\"TOP\"> $series_subseries</TD></TR>" if ($series_subseries ne '');
print "<TR><TD VALIGN=\"TOP\">Resource Format:</TD><TD VALIGN=\"TOP\"> $resource_format</TD></TR>" if ($resource_format ne '');
print "<TR><TD VALIGN=\"TOP\">Time Period Covered:</TD><TD VALIGN=\"TOP\"> $time_period_covered</TD></TR>" if ($time_period_covered ne '');
print "<TR><TD VALIGN=\"TOP\">Subject headings:</TD><TD VALIGN=\"TOP\"> $subjectheadings</TD></TR>" if ($subjectheadings ne '');
print "<TR><TD VALIGN=\"TOP\">Call number:</TD><TD VALIGN=\"TOP\"> $call_number</TD></TR>" if ($call_number ne '');
print "<TR><TD VALIGN=\"TOP\">Link to Resource Online:</TD><TD VALIGN=\"TOP\"> <A HREF=\"$catalog_url\">$catalog_url_text</A></TD></TR>" if ($catalog_url ne '');
print "<TR><TD VALIGN=\"TOP\">ERIC number:</TD><TD VALIGN=\"TOP\"> $eric_number</TD></TR>" if (($eric_number ne '') && ($eric_number ne 'N/A'));
print <<EOM;
	</TABLE>
</TD></TR></TABLE>
EOM
	} # END IF (($count >= $startrecord) && ($count <= $endrecord))
	} # END DB QUERY LOOP



#    IF THERE WERE NO RESOURCES MATCHING THE SEARCH, PRINT MESSAGE
	 if ($count == 0) {
print<<EOM;
<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"$wait;URL=$homepage\">
<CENTER>
<TABLE WIDTH=\"100\%\" BORDER=0 CELLPADDING=6>
<TR><TD ALIGN=CENTER>
		<p class=\"info\">There were no resources that met your search criteria.<br><br>
			You will be returned to the <A HREF=\"$homepage\">Resource Catalog</A> in 10 seconds. 
			</P></FONT>
	</TD>
</TR>
</TABLE>
</CENTER>
EOM
	}
&print_results_range($num_matches, $resultsperpage, $startrecord);


# Print SEDL NAVBAR
if ($num_matches > 0) {
	print "<BR><p ALIGN=\"CENTER\"><strong>Click here to return to the IRC <A HREF=\"$homepage\">Resource Catalog</A></strong></p><BR><BR>\n";
}


	#####################################################################
	## START: SAVE SEARCH TO THE CORP.DB_KEYWORKD_SEARCH_LOG DATA TABLE
	#####################################################################
	my $searchfortext = "";
	my $searchandor = "";
	my $searchfield = "";
	
	if ($searchfor ne '') {
		$searchfortext = "$searchfor";
		$searchandor = $andor;
		$searchfield = "title";
	} elsif ($searchfor2 ne '') {
		$searchfortext = "$searchfor2";
		$searchandor = $andor2;
		$searchfield = "author";
	} elsif ($searchfor3 ne '') {
		$searchfortext = "$searchfor3";
		$searchandor = $andor3;
		$searchfield = "publisher";
	} else {
		$searchfortext = "";
	}
		
	if ($searchfortext ne '') {
		$searchfortext = &backslash_fordb($searchfortext);
		$searchandor = &backslash_fordb($searchandor);

		my $command = "INSERT INTO db_keyword_search_log VALUES ('', 'IRC Catalog', '$searchfield\: $searchfortext', '$searchandor', '$ipnum2', '$ipnum', '$timestamp')";
#		print "<P>COMMAND: $command";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
	}
	#####################################################################
	## END: SAVE SEARCH TO THE CORP.DB_KEYWORKD_SEARCH_LOG DATA TABLE
	#####################################################################


##################
## START: FOOTER 
##################
print "$htmltail";

#    IF IN DEBUG MODE, PRINT HOW MANY RESULTS MATCHED THE QUERY
print pre("Count: $count\n"),"\n" if $debug;
##################
## END: FOOTER 
##################




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






###########################################
## START: PRINT RANGE OF RESULTS TO SHOW
###########################################
sub print_results_range {
my $range_num_matches = $_[0];
my $range_resultsperpage = $_[1];
my $range_startrecord = $_[2];

if ($range_num_matches > $range_resultsperpage) {
print<<EOM;
<div class="dottedBox">
<B>Showing $range_resultsperpage records per page:</B><BR>
Pages: 
EOM
	my $startrecord2 = "1";
	my $endrecord2 = "";
	my $countershowseparator = "0";
	my $page_counter = "1";
	while ($startrecord2 < $range_num_matches) {
		print "\|" if ($countershowseparator eq '1');
		$endrecord2 = $startrecord2 + $range_resultsperpage - 1;
		$endrecord2 = $range_num_matches if ($endrecord2 > $range_num_matches);
			if ($range_startrecord != $startrecord2) {
print<<EOM;
 <A HREF="irc_catalog.cgi?searchfor=$searchfor_forlink&andor=$andor&searchfor2=$searchfor2_forlink&andor2=$andor2&searchfor3=$searchfor3_forlink&andor3=$andor3&sortby=$sortby&resultsperpage=$range_resultsperpage&startrecord=$startrecord2">$page_counter</A> 
EOM
			} else {
#				print " $startrecord2 \- $endrecord2 ";
				print " $page_counter ";
				
			}
		$page_counter++;
		$countershowseparator = "1";
		$startrecord2 = $startrecord2 + $range_resultsperpage;
	} # END WHILE
print "</div>";
###########################################
} # END: PRINT RANGE OF RESULTS TO SHOW
###########################################
} # END SUB

sub cleanaccents2html {
my $cleanitem = $_[0];
	$cleanitem =~ s/“/"/g;			
	$cleanitem =~ s/”/"/g;			
	$cleanitem =~ s/’/'/g;			
	$cleanitem =~ s/‘/'/g;
	$cleanitem =~ s// /g;
	$cleanitem =~ s/–/\&ndash\;/g;
	$cleanitem =~ s/—/\&mdash\;/g;
	$cleanitem =~ s/ / /g; # invisible bullet
	$cleanitem =~ s/…/.../g;
	$cleanitem =~ s/¿/&iquest\;/g; 
	$cleanitem =~ s/À/&Agrave\;/g; 
	$cleanitem =~ s/à/&agrave\;/g;	
	$cleanitem =~ s/Á/&Aacute\;/g;  
	$cleanitem =~ s/á/&aacute\;/g;
	$cleanitem =~ s/Â/&Acirc\;/g;
	$cleanitem =~ s/â/&acirc\;/g;
	$cleanitem =~ s/Ã/&Atilde\;/g;
	$cleanitem =~ s/ã/&atilde\;/g;
	$cleanitem =~ s/Ä/&Auml\;/g;
	$cleanitem =~ s/ä/&auml\;/g;
	$cleanitem =~ s/É/&Eacute\;/g;
	$cleanitem =~ s/é/&eacute\;/g;
	$cleanitem =~ s/È/&Egrave\;/g;
	$cleanitem =~ s/è/&egrave\;/g;
	$cleanitem =~ s/Ê/&Euml\;/g;
	$cleanitem =~ s/ë/&euml\;/g;
	$cleanitem =~ s/Ì/&Igrave\;/g;
	$cleanitem =~ s/ì/&igrave\;/g;
	$cleanitem =~ s/Í/&Iacute\;/g;
	$cleanitem =~ s/í/&iacute\;/g;
	$cleanitem =~ s/Î/&Icirc\;/g;
	$cleanitem =~ s/î/&icirc\;/g;
	$cleanitem =~ s/Ï/&Iuml\;/g;
	$cleanitem =~ s/ï/&iuml\;/g;
	$cleanitem =~ s/Ñ/&Ntilde\;/g;
	$cleanitem =~ s/ñ/&ntilde\;/g;
	$cleanitem =~ s/Ò/&Ograve\;/g;
	$cleanitem =~ s/ò/&ograve\;/g;
	$cleanitem =~ s/Ó/&Oacute\;/g;
	$cleanitem =~ s/ó/&oacute\;/g;
	$cleanitem =~ s/Õ/&Otilde\;/g;
	$cleanitem =~ s/õ/&otilde\;/g;
	$cleanitem =~ s/Ö/&Ouml\;/g;
	$cleanitem =~ s/ö/&ouml\;/g;
	$cleanitem =~ s/Ù/&Ugrave\;/g;
	$cleanitem =~ s/ù/&ugrave\;/g;
	$cleanitem =~ s/Ú/&Uacute\;/g;
	$cleanitem =~ s/ú/&uacute\;/g;
	$cleanitem =~ s/Û/&Ucirc\;/g;  ## THIS REPLACES THE ó FOR SOME REASON
	$cleanitem =~ s/û/&ucirc\;/g;
	$cleanitem =~ s/Ü/&Uuml\;/g;
	$cleanitem =~ s/ü/&uuml\;/g;
	$cleanitem =~ s/ÿ/&yuml\;/g;
	return ($cleanitem);
}


####################################################################
## START: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################
sub backslash_fordb {
my $content_to_backslash = $_[0];
   $content_to_backslash =~ s/"/\\"/g;
   $content_to_backslash =~ s/'/\\'/g;
   $content_to_backslash =~ s/‘/\\'/g;
   $content_to_backslash =~ s/’/\\'/g;
   return($content_to_backslash);
}
####################################################################
## END: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################

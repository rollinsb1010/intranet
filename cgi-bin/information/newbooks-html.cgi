#!/usr/bin/perl 

#use diagnostics;
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
$|=1;
my $count = 0;


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

#my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
#   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################


print header if ($debug eq '1');
## CONSTRUCT THE SQL QUERY
my $command = "select title from irc_catalog where newbookslist NOT LIKE '' order by title";


## OPEN THE DATABASE AND SEND THE QUERY
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

	if ($debug eq '1') {
print<<EOM;
COMMAND: $command
<P>
MATCHES: $num_matches
EOM
	}

## WRITE THE SURVEY RESULTS TO A FILE
open(SURVEYRESULTSDATA,">/home/httpd/html/staff/includes/flash/newbooks.txt");
#print SURVEYRESULTSDATA "newbooks=\nClick for full details: <span style=\"color:#663333;\"><A HREF=\"/staff/information/newbooks.cgi\">new books in the IRC</A></span>\n\nNew books for $month_name_full $year ----------\n";
print SURVEYRESULTSDATA "newbooks=<a href=\"http://www.sedl.org/staff/information/newbooks.cgi\"><span style=\"color:#AD2F2A;\">New books for $month_name_full $year</span></a>\n";

## GET THE RESULTS OF THE QUERY
while (my @arr = $sth->fetchrow) {
    $count++;
    my ($title) = @arr;
#print "$newbookslist, " if ($debug eq '1');
## MAKE CURLY QUOTES INTO STRAIGHT QUOTES
$title = &cleanthis ($title);
#$authors = &cleanthis ($authors);
#$publisher = &cleanthis ($publisher);



print SURVEYRESULTSDATA <<EOM;
<span style=\"color:#06202A;">$title</span>

EOM

} # END DB QUERY LOOP

print SURVEYRESULTSDATA "(Click for definitions of the <span style=\"color:#AD2F2A;\"><a href=\"/staff/information/resources/locationcodes.html\" target=\"_blank\">resource location codes</A></span>)";

close(SURVEYRESULTSDATA);

#print header;
print "<p>Finished making text file with list of new books to be used in Flash on staff home page.</p>";

######################################################################
##  HERE ARE SOME FUNCTIONS USED BY THIS DATABASE SEARCH SCRIPT
######################################################################
sub cleanthis {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\&/and/g;         
#   $dirtyitem =~ s/Gov/GOV/g;
   $dirtyitem =~ s/Í/'/g;
   $dirtyitem =~ s/Ò/"/g;         
   $dirtyitem =~ s/Ó/"/g;         
   $dirtyitem =~ s/Õ/'/g;         
   $dirtyitem =~ s/Ô/'/g;
   $dirtyitem = $dirtyitem;
}



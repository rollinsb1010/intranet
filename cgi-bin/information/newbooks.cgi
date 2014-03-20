#!/usr/bin/perl 

#use diagnostics;
use strict;
use CGI qw/:all/;
use DBI;
use CGI::Carp qw(fatalsToBrowser);
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
$|=1;
my $count = 0;
my $query = new CGI;


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

$htmlhead .= "\n<div style=\"padding:15px;\">\n";
$htmltail = "\n</div>\n$htmltail";
###########################################
# END: GRAB SEDL HEADER AND FOOTER HTML #
###########################################


##################################################################
## GET THE SEARCH PARAMETER VARIABLES FROM THE HTML SEARCH FORM
##################################################################
my $camefrom = $query->param("camefrom");
my $sortby = $query->param("sortby");
   $sortby = "title" if $sortby eq "";
my $sortbyheader = $sortby;

my $lastpublisher = "";

## SET SEDL LINKS TO IRC "HOME PAGE" AND "LOCATION CODES"
my $homepage = "/staff/information/resources/catalog.html";



###############################################################
## START THE OUTPUT BY CREATING THE HTML HEADER AND PAGE TITLE
###############################################################
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Staff - Information Services - New Books</TITLE>
<link href="/staff/includes/staff2006.css" rel="stylesheet" type="text/css" media="screen">


<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function Close(l, t, w, h)  {
var windowprops = "location=no,scrollbars=no,menubars=no,toolbars=no,resizable=no" +
",left=" + 100 + ",top=" + 100 + ",width=" + 135 + ",height=" + 315;
var URL = "http://www.sedl.org/";
window.close(URL,"",windowprops);
}
// End -->
</SCRIPT>


$htmlhead
EOM

## BACKSLASH SPECIAL CHARACTERS
$sortby = &backslash_fordb($sortby);

## CONSTRUCT THE SQL QUERY
my $command = "select title, authors, publisher, datepub, location, call_number
				from irc_catalog where (newbookslist NOT LIKE '')  order by $sortby";


## OPEN THE DATABASE AND SEND THE QUERY
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

if ($num_matches > 0) {
## Print a message on the results page showing what results are sorted by
print<<EOM;
<H1>New books in the IRC</H1>
<p class="intranet_print_page">
Click here for definitions of the 
<a href="/staff/information/resources/locationcodes.html" target="_blank">resource location codes</A>.
</p>
<BR>
<table border="0" cellspacing="0" cellpadding="3">
EOM
}


## GET THE RESULTS OF THE QUERY
while (my @arr = $sth->fetchrow) {
    $count++;
    my ($title, $authors, $publisher, $datepub, $location, $call_number) = @arr;

		$title = &cleanaccents2html ($title);
		$authors = &cleanaccents2html ($authors);
		$publisher = &cleanaccents2html ($publisher);


$call_number = "<a href=\"$call_number\" target=\"_blank\">$call_number</a>" if ($call_number =~ 'http');
print <<EOM;
<TR><TD VALIGN="TOP">$count</TD>
    <TD VALIGN="TOP"><B>$title ($datepub)</B>
EOM
print "<BR>Author(s): $authors" if ($authors ne '');
print "<BR>Publisher: $publisher" if ($publisher ne '');
print "<BR>Call no.: $call_number" if ($call_number ne '');
print "<BR>Location: <B>$location</B>" if ($location ne '');

}

print<<EOM;
</TD></TR>
</TABLE>
$htmltail
EOM


####################################################################
## START: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################
sub backslash_fordb {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\\//g;
   $dirtyitem =~ s/\%22/\"/g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/�/\\�/g;
   $dirtyitem =~ s/�/\\�/g;
   $dirtyitem =~ s/"/\\"/g;
   return($dirtyitem);
}
####################################################################
## END: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################


sub cleanaccents2html {
my $cleanitem = $_[0];
	$cleanitem =~ s/�/"/g;			
	$cleanitem =~ s/�/"/g;			
	$cleanitem =~ s/�/'/g;			
	$cleanitem =~ s/�/'/g;
	$cleanitem =~ s// /g;
	$cleanitem =~ s/�/\&ndash\;/g;
	$cleanitem =~ s/�/\&mdash\;/g;
	$cleanitem =~ s/�//g; # invisible bullet
	$cleanitem =~ s/�/.../g;
	$cleanitem =~ s/�/&iquest\;/g; 
	$cleanitem =~ s/�/&Agrave\;/g; 
	$cleanitem =~ s/�/&agrave\;/g;	
	$cleanitem =~ s/�/&Aacute\;/g;  
	$cleanitem =~ s/�/&aacute\;/g;
	$cleanitem =~ s/�/&Acirc\;/g;
	$cleanitem =~ s/�/&acirc\;/g;
	$cleanitem =~ s/�/&Atilde\;/g;
	$cleanitem =~ s/�/&atilde\;/g;
	$cleanitem =~ s/�/&Auml\;/g;
	$cleanitem =~ s/�/&auml\;/g;
	$cleanitem =~ s/�/&Eacute\;/g;
	$cleanitem =~ s/�/&eacute\;/g;
	$cleanitem =~ s/�/&Egrave\;/g;
	$cleanitem =~ s/�/&egrave\;/g;
	$cleanitem =~ s/�/&Euml\;/g;
	$cleanitem =~ s/�/&euml\;/g;
	$cleanitem =~ s/�/&Igrave\;/g;
	$cleanitem =~ s/�/&igrave\;/g;
	$cleanitem =~ s/�/&Iacute\;/g;
	$cleanitem =~ s/�/&iacute\;/g;
	$cleanitem =~ s/�/&Icirc\;/g;
	$cleanitem =~ s/�/&icirc\;/g;
	$cleanitem =~ s/�/&Iuml\;/g;
	$cleanitem =~ s/�/&iuml\;/g;
	$cleanitem =~ s/�/&Ntilde\;/g;
	$cleanitem =~ s/�/&ntilde\;/g;
	$cleanitem =~ s/�/&Ograve\;/g;
	$cleanitem =~ s/�/&ograve\;/g;
	$cleanitem =~ s/�/&Oacute\;/g;
	$cleanitem =~ s/�/&oacute\;/g;
	$cleanitem =~ s/�/&Otilde\;/g;
	$cleanitem =~ s/�/&otilde\;/g;
	$cleanitem =~ s/�/&Ouml\;/g;
	$cleanitem =~ s/�/&ouml\;/g;
	$cleanitem =~ s/�/&Ugrave\;/g;
	$cleanitem =~ s/�/&ugrave\;/g;
	$cleanitem =~ s/�/&Uacute\;/g;
	$cleanitem =~ s/�/&uacute\;/g;
	$cleanitem =~ s/�/&Ucirc\;/g;  ## THIS REPLACES THE � FOR SOME REASON
	$cleanitem =~ s/�/&ucirc\;/g;
	$cleanitem =~ s/�/&Uuml\;/g;
	$cleanitem =~ s/�/&uuml\;/g;
	$cleanitem =~ s/�/&yuml\;/g;
	return ($cleanitem);
}


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
   $dirtyitem =~ s/‘/\\‘/g;
   $dirtyitem =~ s/’/\\’/g;
   $dirtyitem =~ s/"/\\"/g;
   return($dirtyitem);
}
####################################################################
## END: PREPARE (BACKSLASH) TEXT FOR DB INSERTION
####################################################################


sub cleanaccents2html {
my $cleanitem = $_[0];
	$cleanitem =~ s/“/"/g;			
	$cleanitem =~ s/”/"/g;			
	$cleanitem =~ s/’/'/g;			
	$cleanitem =~ s/‘/'/g;
	$cleanitem =~ s// /g;
	$cleanitem =~ s/–/\&ndash\;/g;
	$cleanitem =~ s/—/\&mdash\;/g;
	$cleanitem =~ s/ //g; # invisible bullet
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


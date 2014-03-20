#!/usr/bin/perl

#####################################################################################################
# Copyright 2000 by Southwest Educational Development Laboratory
#
# 2001-10-17  Put into new staff page template and moved to http://www.sedl.org/staff/train/schedule/signup.cgi
# Written by Brian Litke 10-22-2001 
#####################################################################################################
use strict;
use CGI qw/:all/;

my $query = new CGI;

### GET VARIABLES FROM THE FORM
my $DocID = param('DocID'); # VALUES: ts or lv
 
my $RedirectURL = 'fmp7://bgtauth:bgtauth@198.214.140.242/SIMS';

## START: WRITE DATA TO A FILE
open(XML_DATA,">/home/httpd/html/staff/personnel/temp/budgetauth.xml");
print XML_DATA <<EOM;
<?xml version="1.0" encoding="UTF-8"?>
<SIMS>
	<Document>
	<DocID>$DocID</DocID>
	</Document>
</SIMS>
EOM
close(XML_DATA);
## END: WRITE DATA TO A FILE

print header;

print<<EOM;

<HTML>
<HEAD>
<META HTTP-EQUIV=REFRESH CONTENT="0;URL=$RedirectURL">
</HEAD>
<BODY onLoad="resizeTo(400,400)">
<table width=100% border=0 cellpadding=20>
<tr><td><center>
<p>

Thank you.<p>




You will be redirected to the SIMS database.</center>
</td></tr></table>
</BODY></HTML>
EOM


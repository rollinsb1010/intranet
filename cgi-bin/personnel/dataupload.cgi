#!/usr/bin/perl
#use diagnostics;
#use CGI;
use CGI qw/:standard :html3/;

## DECLARE VARIABLES
my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
my $filename = "";
my $error_message = "";
my $htmlhead = "";


## PRINT FORM ELEMENTS TO BROWSER SO USER CAN CHOOSE FILE TO UPLOAD
$query = new CGI;
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

<TABLE>
<TR><TD WIDTH=100%>


<FONT FACE="Verdana, ARIAL, HELVETICA">
<H1>OFTS Database Upload Page</H1>
Use this Page to Update the SEDL Web site position vacancies, as well as the SEDL intranet <strong>Leave Report</strong> and <strong>Financial Report</strong> Databases 
with Data from the Accounting System.
<P>
You can use this form to upload the following files:
<P>
Note: If you have a DBF version of this file, please open the file in Microsoft Excel, 
then re-save a version of the file in tab-delimited-text format. 
To do so in Excel,  choose "File" ==> "Save As" and select 
  <UL>
  <LI>save as file type: <strong>Tab-delimited text</strong>
  <LI>filename:
  	<UL>
  	<LI><FONT COLOR=GREEN>positionvacancies</FONT> (Staff "SEDL Position Vacancies" - Tracy/Sue)</LI>
<P>
  	<LI><FONT COLOR=GREEN>LEAVEDAT.TXT</FONT> (Staff "Leave Report" Data - Arnold/Stuart)
  	<LI><FONT COLOR=GREEN>FINANCE.TXT</FONT> (Financial Report: Contract to date - Arnold/Stuart)
  	<LI><FONT COLOR=GREEN>FINANCC.TXT</FONT> (Financial Report: Fiscal Year to date - Arnold/Stuart)
  	<LI><FONT COLOR=GREEN>PR.TXT</FONT> (Current Month's Payroll - Arnold/Stuart)
  	<LI><FONT COLOR=GREEN>CK.TXT</FONT> (Current Month's Checks - Arnold/Stuart)
  	<LI><FONT COLOR=GREEN>JV.TXT</FONT> (Current Month's Journal Vouchers - Arnold/Stuart)
  	</UL>
  </UL>
<P>  
<FONT COLOR=RED><strong>Directions:</strong><BR></FONT>
Please click on the BROWSE button below to browse your hard drive so you can select 
the file to upload to the server, then click on the "Upload file" button.</H2>

</TD></TR>
</TABLE>
EOM


print $query->start_multipart_form(-name);
print $query->filefield('uploaded_file','starting value',30,80);

print<<EOM;
 &nbsp; <input type="submit" value="Upload file" name="submit">
</FORM>
EOM

$filename = $query->param('uploaded_file');

## CHANGE MAC LINE BREAKS TO UNIX LINE BREAKS IN FILE BEING UPLOADED THROUGH BUFFER
    if (($filename ne 'starting value') and ($filename ne '')) {
      open (OUTFILE,">/home/httpd/html/temp/$filename")|| print "can't open file";
                           while ($bytesread=read($filename,$buffer,1024)) {
                              print "oops! $!\n" unless defined $bytesread;
                              $buffer=~ tr/\015/\012/;
                              print "$buffer" if $debug;
                              print OUTFILE $buffer|| print "can't write to outfile";
                           }

}


## IF USER HAS UPLOADED A FILE, PRINT LINK TO OTHER SCRIPT THAT THEN UPDATES THE DATABASE, SINCE INCLUDING IT IN THIS SCRIPT WASN'T WORKING
if ($filename ne "") {
print<<EOM;
<FONT FACE="Verdana, ARIAL, HELVETICA\">
<H3>Step 1 Complete!
The data file has been uploaded to the server. </H3>

<strong>Step 2: 
<A HREF="/staff/personnel/dataupload2.cgi?filename=$filename">Click here to update database</A></strong>
</FONT>
EOM
}




## END HTML PAGE
print $query->end_html;

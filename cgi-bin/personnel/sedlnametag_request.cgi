#!/usr/bin/perl

# Copyright 2010 by SEDL
# Written by Brian Litke 2009-02-17 for SEDL staff requesting a SEDL Name Tag

use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
my $query = new CGI;

use DBI;
my $dsn = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "", "");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 
use intranetcommoncode; 

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
####################################
## END: GET THE CURRENT DATE INFO
####################################

####################################################################
## START: GRAB USER ID, IF IN COOKIE
####################################################################
my $user = param('user');
	if ($user eq '') {
		my(%cookies) = getCookies();
		foreach (sort(keys(%cookies))) {
			$user = $cookies{$_} if (($_ eq 'staffid') && ($user eq ''));
		}
	} # END OF COOKIE CHECK
####################################################################
## END: GRAB USER ID, IF IN COOKIE
####################################################################


### GET VARIABLES FROM THE FORM IN CASE USER DID NOT ENTER ALL FIELDS
my $location = $query->param("location");
   $location = "showform" if ($location eq '');
   
my $submitted_by = $query->param('submitted_by');
my $new_name = $query->param('new_name');
my $new_ba = $query->param('new_ba');
my $new_bc = $query->param('new_bc');
my $new_department = $query->param('new_department');


my $errormessage = "";

#if (($requesttype eq '') && ($location eq '')) {
#	$showform = "yes";
#	$errormessage = "<font color=red>Please enter a maintenance request before submitting.</font>";
#}

##############################################
# START: READ IN SEDL HEADER AND FOOTER HTML #
##############################################
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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("443"); # 443 is the PID for this page in the intranet database

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";


## COOKIE VARIABLES
my $expdate = "Fri, 25-Dec-2015 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";
##############################################
# END: READ IN SEDL HEADER AND FOOTER HTML #
##############################################


######################################
## START: PRINT PAGE HEADER
######################################
print header;
print <<EOM;
<HTML>
<HEAD>
<TITLE>SEDL Staff Form: SEDL Name Tag Request</TITLE>
</HEAD>
$htmlhead
EOM
######################################
## END: PRINT PAGE HEADER
######################################



#################################################################################
## START: LOCATION = showform
#################################################################################
if ($location eq 'showform') {


## PRINT PAGE HEADER
print <<EOM;
<h1 style="margin-top:0px;"><img src="/staff/images/SEDL-Pin-Final-Artwork.jpg" alt="SEDL Name Tag" class="fltrt" width="213">
Form: SEDL Name Tag Request</h1>
EOM

print "<p class=\"alert\">$errormessage</p>" if ($errormessage ne '');

print<<EOM;
<script language="JavaScript">
<!--
function checkFields() { 
	// Question - new_name
	if (document.form2.new_name.value == '') {
		alert("You forgot to indicate the staff member's Name.");
		document.form2.new_name.focus();
		return false;
	}
	// Question - new_department
	if (document.form2.new_department.value == '') {
		alert("You forgot to indicate the staff member's Department.");
		document.form2.new_department.focus();
		return false;
	}

	// Question - new_ba
	if (document.form2.new_ba.value == '') {
		alert("You forgot to indicate the staff member's Budget Authority.");
		document.form2.new_ba.focus();
		return false;
	}

	// Question - new_bc
	if (document.form2.new_bc.value == '') {
		alert("You forgot to indicate the staff member's Budget Code.");
		document.form2.new_bc.focus();
		return false;
	}

}	
// -->
</script>
<p>
Please submit new requests for SEDL name tags using the form below.  After submission, 
the data will be sent to the administrative assistant in the Communications department, and you will 
receive a confirmation e-mail.
</p>

<FORM ACTION="/staff/personnel/sedlnametag_request.cgi" METHOD="POST" id="form2" name="form2" onsubmit="return checkFields()">

<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="0">

<TR><TD VALIGN="TOP"><strong><label for="submitted_by">Name of person submitting form</label></strong></TD>
    <TD VALIGN="TOP">
    	<select name="submitted_by" id="submitted_by">
    	<option value=""></option>
EOM
##########################################################
## GET THIS STAFF MEMBER's INFO FROM THE PROFILE DATABASE
##########################################################
my $command = "select firstname, lastname, email, phone, userid, phoneext, department_abbrev from staff_profiles order by lastname";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

## GET THE RESULTS OF THE QUERY
	while (my @arr = $sth->fetchrow) {
    	my ($firstname, $lastname, $email, $phone, $userid, $phoneext, $department_abbrev) = @arr;
		print "<option value=\"$email\" ";
		print "SELECTED" if ($user eq $userid);
		print ">$firstname $lastname</option>\n";
	} # END DB QUERY LOOP
#<INPUT TYPE=TEXT NAME=name SIZE=30 value="$name">
print<<EOM;
	</select>
</TD></TR>

<TR><TD valign="top"><strong><label for="new_name">Full name of the staff member<br>
	to go on the name tag</label></strong></TD>
    <TD><input name="new_name" id="new_name" value="$new_name" size="30"></TD></TR>

<TR><TD valign="top"><strong><label for="new_department">Department name</label></strong></TD>
    <TD><input name="new_department" id="new_department" value="$new_department" size="30"></TD></TR>

<TR><TD valign="top"><strong><label for="new_ba">Budget Authority</label></strong></TD>
    <TD><input name="new_ba" id="new_ba" value="" size="30"></TD></TR>

<TR><TD valign="top"><strong><label for="new_bc">Budget Code</label></strong></TD>
    <TD><input name="new_bc" id="new_bc" value="" size="15"></TD></TR>

</table>
EOM

print<<EOM;
<p></p>
  <ul>
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_request">
  <INPUT TYPE="SUBMIT" VALUE="Submit">
  </ul>
  </form>
<p></p>
EOM

}
#################################################################################
## END: LOCATION = showform
#################################################################################






#################################################################################
#################################################################################
#################################################################################
## IF THE USER ENTERED ALL INFORMATION, START HANDLING THE DATA
## THEN SAVE THE DATA TO A FILE AND SEND AN E-MAIL WITH THE DATA
if ($location eq 'process_request') {

	## REMOVE TABS AND CARRIAGE RETURNS
	## REMOVE CARRIAGE RETURNS & TABS FROM OPEN-ENDED VARIABLES
	#$name = &cleanthis ($name);
	$submitted_by = $query->param('submitted_by');
	$new_name = $query->param('new_name');
	$new_department = $query->param('new_department');

	## SAVE DATA TO A FILE
	open(SURVEYRESULTSDATA,">>sedlnametag_req_history.txt");
#	print SURVEYRESULTSDATA "todaysdate\tsubmitted by\tName for Pin\tDepartment\tBudget Authority\tBudget Code\n";
	print SURVEYRESULTSDATA "$todaysdate\t$submitted_by\t$new_name\t$new_department\t$new_ba\t$new_bc\n";
	close(SURVEYRESULTSDATA);


## SEND AN E-MAIL

# These are for mail notification of guest events
my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
my $recipient = 'esmeralda.urquidi@sedl.org';
#   $recipient = 'brian.litke@sedl.org'; # FOR TESTING ONLY;
my $fromaddr = 'webmaster@sedl.org';


open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$fromaddr>
To: $recipient
Cc: $submitted_by
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Data from SEDL Name Tag Request Form
X-Mail-Gateway: comment.cgi Mail Gateway 1.0
X-Real-Host-From: $fromaddr

The following data was received from the SEDL Name Tag Request Form at:
http://www.sedl.org/staff/personnel/sedlnametag_request.cgi

The SEDL Name Tag Request Information starts here:

REQUEST SUBMITTED BY:
---------------------
$submitted_by


NAME TAG IS FOR:
--------------
FULL NAME FOR NAME TAG = $new_name

DEPARTMENT = $new_department

Budget Code = $new_bc

Budget Authority = $new_ba


An archive of requests from this form is saved to a tab-delimited text file at: 
http://www.sedl.org/staff/personnel/sedlnametag_req_history.txt



---End of SEDL Name Tag Request Data---

EOM
print NOTIFY remote_host,"\n",remote_addr,"\n";
;
close(NOTIFY);


## PRINT PAGE HEADER
print<<EOM;
<H1 align=center>Thank You</H1>

<p>
Thank you for submitting a SEDL name tag request.
<br>
<br>
An e-mail has been sent to the administrative assistant in Communications, and you have been CC'd on that e-mail.
</p>
<p>
Click here to <a href="/staff/personnel/sedlnametag_request.cgi">submit another SEDL name tag request</a>.
</p>

EOM



}
## END THE HANDLING OF DATA
#################################################################################
#################################################################################
#################################################################################

print<<EOM;
<p>
<br><br>
To report troubles using this form, send an e-mail to <A HREF=\"mailto:webmaster\@sedl.org\">webmaster\@sedl.org</A> 
or call Brian Litke at ext. 6529.
</p>
$htmltail
EOM
#<p>LOCATION = $location</p>


####################################################################
##  HERE ARE SOME SUBROUTINES USED BY THIS DATABASE SEARCH SCRIPT ##
####################################################################

## THIS SUBROUTINE MAKES SURE ANY SPECIAL CHARACTERS ARE BACKSLASHED BEFORE SENDING A COMMAND TO MYSQL
sub cleanthis {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem = $dirtyitem;
}

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



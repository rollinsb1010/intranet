#!/usr/bin/perl

# Copyright 2009 by SEDL
# Written by Brian Litke 2009-02-17 for SEDL staff requesting business cards

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
my $new_title = $query->param('new_title');
my $new_department = $query->param('new_department');
my $new_office_number = $query->param('new_office_number');
my $new_mobile_number = $query->param('new_mobile_number');
my $new_email = $query->param('new_email');
my $new_fax = $query->param('new_fax');
my $new_address = $query->param('new_address');
my $new_city = $query->param('new_city');
my $new_state = $query->param('new_state');
my $new_zip = $query->param('new_zip');
my $new_bc = $query->param('new_bc');
my $new_ba = $query->param('new_ba');

my $new_hire = $query->param('new_hire');
   $new_hire = "yes" if ($new_email =~ 'a new staff member');
   $new_hire = "no" if ($new_hire eq '');

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("350"); # 350 is the PID for this page in the intranet database

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
<TITLE>SEDL Staff Form: Business Card Request</TITLE>
</HEAD>
$htmlhead
EOM
######################################
## END: PRINT PAGE HEADER
######################################


#################################################################################
## START: LOCATION = showform
#################################################################################
if ($location eq 'showform2') {
## PRINT PAGE HEADER
print<<EOM;
<H1 style="margin-top:0px;">Form: Business Card Request - Step 2 of 2</H1>
$errormessage
EOM
##########################################################
## GET THIS STAFF MEMBER's INFO FROM THE PROFILE DATABASE
##########################################################
my $this_firstname = "";
my $this_lastname = "";
my $this_email = "";
my $this_phone = "";
my $this_userid = "";
my $this_phoneext = "";
my $this_department_abbrev = "";
my $this_jobtitle = "";
my $this_supervised_by = "";

my $command = "select firstname, lastname, email, phone, userid, phoneext, department_abbrev, jobtitle, supervised_by from staff_profiles where email like '$new_email'";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

my $fullname = "";
## GET THE RESULTS OF THE QUERY
	while (my @arr = $sth->fetchrow) {
    	($this_firstname, $this_lastname, $this_email, $this_phone, $this_userid, $this_phoneext, $this_department_abbrev, $this_jobtitle, $this_supervised_by) = @arr;
		$fullname = "$this_firstname $this_lastname";
		$this_department_abbrev = "Communications" if ($this_department_abbrev =~ 'COM');
		$this_department_abbrev = "Afterschool, Family, and Community" if ($this_department_abbrev =~ 'AFC');
		$this_department_abbrev = "Improving School Performance" if ($this_department_abbrev =~ 'ISP');
		$this_department_abbrev = "Administrative Services" if ($this_department_abbrev eq 'AS');
		$this_department_abbrev = "Research and Evaluation" if ($this_department_abbrev eq 'RE');
		$this_department_abbrev = "Development" if ($this_department_abbrev eq 'DEV');
		$this_department_abbrev = "Disability Research to Practice" if ($this_department_abbrev eq 'DRP');
		$this_department_abbrev = "Executive Office" if ($this_department_abbrev eq 'EO');
	} # END DB QUERY LOOP


my $prefill_fax = "512-476-2286";
my $prefill_address = "4700 Mueller Blvd.";
my $prefill_city = "Austin";
my $prefill_state = "TX";
my $prefill_zip = "78723";

if ($this_phone !~ '512-391') {
	$prefill_fax = "";
	$prefill_address = "";
	$prefill_city = "";
	$prefill_state = "";
	$prefill_zip = "";
}


#<INPUT TYPE=TEXT NAME=name SIZE=30 value="$name">
print<<EOM;

<P>
Continue by editing the details for $fullname's business card below.
</p>

<script language="JavaScript">
<!--
function checkFields() { 
	// Question - new_name
	if (document.form2.new_name.value == '') {
		alert("You forgot to indicate the staff member's NAME.");
		document.form2.new_name.focus();
		return false;
	}

	// Question - new_title
	if (document.form2.new_title.value == '') {
		alert("You forgot to indicate the staff member's TITLE.");
		document.form2.new_title.focus();
		return false;
	}

	// Question - new_department
	if (document.form2.new_department.value == '') {
		alert("You forgot to indicate the staff member's DEPARTMENT.");
		document.form2.new_department.focus();
		return false;
	}

	// Question - new_office_number
	if (document.form2.new_office_number.value == '') {
		alert("You forgot to indicate the staff member's OFFICE PHONE NUMBER.");
		document.form2.new_office_number.focus();
		return false;
	}

	// Question - new_email
	if (document.form2.new_email.value == '') {
		alert("You forgot to indicate the staff member's EMAIL ADDRESS.");
		document.form2.new_email.focus();
		return false;
	}

	// Question - new_fax
	if (document.form2.new_fax.value == '') {
		alert("You forgot to indicate the staff member's FAX.");
		document.form2.new_fax.focus();
		return false;
	}

	// Question - new_address
	if (document.form2.new_address.value == '') {
		alert("You forgot to indicate the staff member's ADDRESS.");
		document.form2.new_address.focus();
		return false;
	}

	// Question - new_city
	if (document.form2.new_city.value == '') {
		alert("You forgot to indicate the staff member's CITY.");
		document.form2.new_city.focus();
		return false;
	}

	// Question - new_state
	if (document.form2.new_state.value == '') {
		alert("You forgot to indicate the staff member's STATE.");
		document.form2.new_state.focus();
		return false;
	}

	// Question - new_zip
	if (document.form2.new_zip.value == '') {
		alert("You forgot to indicate the staff member's ZIP CODE.");
		document.form2.new_zip.focus();
		return false;
	}

	// Question - new_bc
	if (document.form2.new_bc.value == '') {
		alert("You forgot to indicate the BUDGET CODE to charge.");
		document.form2.new_bc.focus();
		return false;
	}

	// Question - new_ba
	if (document.form2.new_ba.value == '') {
		alert("You forgot to indicate the staff member's BUDGET AUTHORITY.");
		document.form2.new_ba.focus();
		return false;
	}


}	
// -->
</script>


<FORM ACTION="/staff/personnel/businesscard_request.cgi" METHOD="POST" id="form2" name="form2" onsubmit="return checkFields()">

<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0">

<tr><td VALIGN=TOP><strong><label for="new_name">Name for card</label></strong><br>(i.e. Brian Litke, MSCIS)</td>
    <td><input name="new_name" id="new_name" value="$this_firstname $this_lastname" size="30"></td></tr>
<tr><td VALIGN=TOP><strong><label for="new_title">Title for card</label></strong></td>
    <td><input name="new_title" id="new_title" value="$this_jobtitle" size="30"></td></tr>
<tr><td VALIGN=TOP><strong><label for="new_department">Department name for card</label></strong></td>
    <td><input name="new_department" id="new_department" value="$this_department_abbrev" size="30"></td></tr>
<tr><td VALIGN=TOP><strong><label for="new_office_number">Office Phone Number</label></strong></td>
    <td><input name="new_office_number" id="new_office_number" value="$this_phone" size="30"></td></tr>
<tr><td VALIGN=TOP><strong><label for="new_mobile_number">Mobile Phone Number (optional)</label></strong></td>
    <td><input name="new_mobile_number" id="new_mobile_number" value="" size="30"></td></tr>
<tr><td VALIGN=TOP><strong><label for="new_email">Email</label></strong></td>
    <td><input name="new_email" id="new_email" value="$this_email" size="30"></td></tr>
<tr><td VALIGN=TOP><strong><label for="new_fax">Fax</label></strong></td>
    <td><input name="new_fax" id="new_fax" value="$prefill_fax" size="30"></td></tr>

<tr><td VALIGN=TOP><strong><label for="new_address">Address</label></strong></td>
    <td><input name="new_address" id="new_address" value="$prefill_address" size="30"></td></tr>
<tr><td VALIGN=TOP><strong><label for="new_city">City</label></strong></td>
    <td><input name="new_city" id="new_city" value="$prefill_city" size="30"></td></tr>
<tr><td VALIGN=TOP><strong><label for="new_state">State</label></strong></td>
    <td><input name="new_state" id="new_state" value="$prefill_state" size="30"></td></tr>
<tr><td VALIGN=TOP><strong><label for="new_zip">Zip</label></strong></td>
    <td><input name="new_zip" id="new_zip" value="$prefill_zip" size="30"></td></tr>
<tr><td VALIGN=TOP><strong><label for="new_bc">Budget Code</label></strong></td>
    <td><input name="new_bc" id="new_bc" value="" size="30"></td></tr>
<tr><td VALIGN=TOP><strong><label for="new_ba">Budget Authority</label></strong></td>
    <td><input name="new_ba" id="new_ba" value="$this_supervised_by" size="30"></td></tr>
</TABLE>

EOM

print<<EOM;
<p>
Please click on the Send Request button below when you have answered all questions.
</p>
  <UL>
  <INPUT TYPE="HIDDEN" NAME="new_hire" VALUE="$new_hire">
  <INPUT TYPE="HIDDEN" NAME="submitted_by" VALUE="$submitted_by">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_request">
  <INPUT TYPE="SUBMIT" VALUE="Submit Business Card Request">
  </UL>
  </FORM>

EOM

}
#################################################################################
## END: LOCATION = showform2
#################################################################################


#################################################################################
## START: LOCATION = showform
#################################################################################
if ($location eq 'showform') {


## PRINT PAGE HEADER
print <<EOM;
<H1 style="margin-top:0px;">Form: Business Card Request - Step 1 of 2</H1>
$errormessage
<P>
Please submit new requests for business cards using the form below.  After submission, 
the data will be sent to the administrative assistant in the Communications department, and you will 
receive a confirmation email.
</p>

<FORM ACTION="/staff/personnel/businesscard_request.cgi" METHOD="POST">

<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0">
<tr><td VALIGN="TOP"><strong><label for="submitted_by">Name of person submitting form</label></strong></td>
    <td VALIGN="TOP">
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
</td></tr>

<tr><td VALIGN="TOP"><strong><label for="new_email">Business Cards are for</label></strong></td>
    <td VALIGN="TOP">
    	<select name = "new_email" id="new_email">
    	<option value=""></option>
    	<option value="a new staff member not yet hired">a new staff member not yet hired</option>
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
#		print "SELECTED" if ($user eq $userid);
		print ">$firstname $lastname</option>\n";
	} # END DB QUERY LOOP
#<INPUT TYPE=TEXT NAME=name SIZE=30 value="$name">
print<<EOM;
	</select>
</td></tr>
</table>

EOM

print<<EOM;
<p></p>
  <div style="margin-left:25px;">
  <input TYPE="HIDDEN" NAME="location" VALUE="showform2">
  <input TYPE="SUBMIT" VALUE="Continue to Step 2 of 2">
  </div>
  </form>

EOM

}
#################################################################################
## END: LOCATION = showform
#################################################################################






#################################################################################
#################################################################################
#################################################################################
## IF THE USER ENTERED ALL INFORMATION, START HANDLING THE DATA
## THEN SAVE THE DATA TO A FILE AND SEND AN EMAIL WITH THE DATA
if ($location eq 'process_request') {

	## REMOVE TABS AND CARRIAGE RETURNS
	## REMOVE CARRIAGE RETURNS & TABS FROM OPEN-ENDED VARIABLES
	#$name = &cleanthis ($name);
	$submitted_by = $query->param('submitted_by');
	$new_name = $query->param('new_name');
	$new_title = $query->param('new_title');
	$new_department = $query->param('new_department');
	$new_office_number = $query->param('new_office_number');
	$new_mobile_number = $query->param('new_mobile_number');
	$new_email = $query->param('new_email');
	$new_fax = $query->param('new_fax');
	$new_address = $query->param('new_address');
	$new_city = $query->param('new_city');
	$new_state = $query->param('new_state');
	$new_zip = $query->param('new_zip');
	$new_bc = $query->param('new_bc');
	$new_ba = $query->param('new_ba');


	## SAVE DATA TO A FILE
	open(SURVEYRESULTSDATA,">>businesscard_req_history.txt");
#	print SURVEYRESULTSDATA "todaysdate\tsubmitted_by\tname\ttitle\tdepartment\toffice_number\tmobile_number\temail\tfax\taddress\tcity\tstate\tzip\tbc\tba\n";
	print SURVEYRESULTSDATA "$todaysdate\t$submitted_by\t$new_name\t$new_title\t$new_department\t$new_office_number\t$new_mobile_number\t$new_email\t$new_fax\t$new_address\t$new_city\t$new_state\t$new_zip\t$new_bc\t$new_ba\n";
	close(SURVEYRESULTSDATA);


## SEND AN EMAIL

# These are for mail notification of guest events
my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
#my $recipient = 'blitke@sedl.org';
my $recipient = 'esmeralda.urquidi@sedl.org';
#   $recipient = 'brian.litke@sedl.org';
my $fromaddr = 'webmaster@sedl.org';


open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: Business Card Request <$fromaddr>
To: $recipient
Cc: $submitted_by
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Data from Business Card Request Form

The following data was received from the Business Card Request Form at:
http://www.sedl.org/staff/personnel/businesscard_request.cgi

The Business Card Request Information starts here:

REQUEST SUBMITTED BY:
---------------------
$submitted_by

EOM
if ($new_hire eq 'yes') {
print NOTIFY <<EOM;
* This order is for a new staff member not yet hired.
EOM
}
print NOTIFY <<EOM;

CARDS ARE FOR:
--------------
NAME = $new_name
TITLE = $new_title
DEPARTMENT = $new_department

EMAIL = $new_email
OFFICE NUMBER = $new_office_number
MOBILE NUMBER = $new_mobile_number
FAX = $new_fax

ADDRESS = $new_address
CITY = $new_city
STATE = $new_state
ZIP = $new_zip

BUDGET CODE = $new_bc
BUDGET AUTHORITY = $new_ba




An archive of requests from this form is saved to a tab-delimited text file at: 
http://www.sedl.org/staff/personnel/businesscard_req_history.txt



---End of Business Card Request Data---

EOM
print NOTIFY remote_host,"\n",remote_addr,"\n";
;
close(NOTIFY);


## PRINT PAGE HEADER
print<<EOM;
<H1 style="text-align:center;">Thank You</H1>

<p>
Thank you for submitting a business card request.
<br>
<br>
An email has been sent to the administrative assistant in Communications, and you have been CC'd on that email.
</p>
<p>
Click here to <a href="/staff/personnel/businesscard_request.cgi">submit another business card request</a>.
</p>

EOM



}
## END THE HANDLING OF DATA
#################################################################################
#################################################################################
#################################################################################

print<<EOM;
<P>
<br><br>
To report troubles using this form, send an email to <A HREF=\"mailto:webmaster\@sedl.org\">webmaster\@sedl.org</A> 
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



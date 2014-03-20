#!/usr/bin/perl 

#######################################################################################
# This script was constructed for the SEDL Product Catalog 08/24/99 by Brian Litke
# Altered to generate the staff profile pages and staff directory 9/17/2001 (6 days after World Trade center bombing)
#
# 09-17-01  Enabled generation of Staff Profiles using 2001 Web site template
#######################################################################################

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
use staffprofiles_shared_functions;

#############################################
## START: LOAD PERL MODULES
#############################################
## THIS IS A PERL MODULE THAT FORMATS NUMBERS
use Number::Format;
# EXAMPLE OF USAGE
# my $this_number
#	my $x = new Number::Format;
#	$this_number = $x->format_number($this_number, 2, 2);


my $query = new CGI;
my $debug = $query->param("debug") || "0"; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
my $count = 0;
my $countart = 0;
my $sortbylabel = "";
my $sortbylabel2 = "";
my $referer = $ENV{"HTTP_REFERER"};

#######################
## GRAB PAGE TEMPLATE #
#######################
my $htmlheaderstaff = "";
my $htmlfooterstaff = "";

my $htmlfooterwithsearch = "";

$htmlheaderstaff = "
<html>
<head>
<style type=\"text/css\">
	body, p, td, th {
		font-family: Verdana, Arial, Helvetica, sans-serif;  
		font-size: 10px; 
	}
	a:link.nolinkstyle { color: #000000;	text-decoration: none; }
	a.nolinkstyle { color: #000000; text-decoration: none; }
	a:hover.nolinkstyle { color: #000000; text-decoration: none; }
	a:visited.nolinkstyle { color: #000000; text-decoration: none;}
.padding15_no_print_padding { padding:15px; }
.pagebreak{    PAGE-BREAK-BEFORE: always}
sub, sup { line-height: 0; }

\@media print {
  /* style sheet for print goes here */
.padding15_no_print_padding { padding:0px; }
.do_not_print { display: none; }
	a:link.nolinkstyleprint { color: #000000;	text-decoration: none; }
	a.nolinkstyleprint { color: #000000; text-decoration: none; }
	a:hover.nolinkstyleprint { color: #000000; text-decoration: none; }
	a:visited.nolinkstyleprint { color: #000000; text-decoration: none;}
}


</style>
</head>
<body style=\"margin:0;padding:0\">";

$htmlfooterstaff = "</body></html>";

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

	my $timestamp_time = "$time_hour_mil_leadingzero$time_min$time_sec"; 
	my $timestamp_date = "$year$month$monthdate_wleadingzero"; 
	my $datestamp = $timestamp_date."T".$timestamp_time."Z";

	my $date_full_pretty_4digityear = "$month/$monthdate_wleadingzero/$year"; # Full date in human-readable format  (e.g. 03/06/08)
	my $date_full_pretty_4digityear_withtime = "$month/$monthdate_wleadingzero/$year at $time_hour_leadingzero\:$time_min"; # Full date in human-readable format  (e.g. 03/06/08)

####################################
## END: GET THE CURRENT DATE INFO
####################################


## COOKIE VARIABLES
my $expdate = "Fri, 25-Dec-2015 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";


####################################################################
## START: LOG STAFF ID TO DATABASE TO TRACK USAGE OF THIS RESOURCE
####################################################################
my $user = param('user');
my $cookie_search_fav = ""; # TRACK USER ID

if ($user eq '') {
my(%cookies) = getCookies();

foreach (sort(keys(%cookies))) {
	$user = $cookies{$_} if (($_ eq 'staffid') && ($user eq ''));
	$cookie_search_fav = $cookies{$_} if ($_ eq 'intranetsearch');
}

} # END OF COOKIE CHECK

## IF STAFF USER ID IS PRESENT IN COOKIE, LOG THEIR USE OF THIS TOOL TO THE TRACKING DATABASE
if ($user ne '') {
	my $commandinsert = "INSERT INTO staffpageusage VALUES ('$user', '$date_full_mysql', 'Staff Birthday List')";
	my $dsn = "DBI:mysql:database=test;host=localhost";
	my $dbh = DBI->connect($dsn);
	my $sth = $dbh->prepare($commandinsert) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;
}
####################################################################
## END: LOG STAFF ID TO DATABASE TO TRACK USAGE OF THIS RESOURCE
####################################################################

## START: SET SEARCH FORM TO USER'S LAST DATA SOURCE SEARCH SELECTION
if ($cookie_search_fav ne '') {
	$htmlheaderstaff =~ s/\<option value\=\"$cookie_search_fav\"\>/\<option value\=\"$cookie_search_fav\" SELECTED\>/gi;
}
## END: SET SEARCH FORM TO USER'S LAST DATA SOURCE SEARCH SELECTION



my $intranetonly = $query->param("intranetonly");
my $showuserid = $query->param("showuserid");
   $showuserid = "all" if ($showuserid eq '');
#   $intranetonly = "yes" if ($showuserid ne '');
my $list_view = $query->param("list_view"); # USED FOR INTRANET STAFF LISTING
   $list_view = 'lastname' if ($list_view eq '');



	##################################################################################
	## START: OPEN DIRECTORY, READ FILE LIST, AND PUSH VALID IMAGES INTO SECOND ARRAY
	##################################################################################
	my %staff_user_exists;
	my %staff_image_onfile;
#	if ($intranetonly eq '') {

	opendir(DIR, "/home/httpd/html/images/people/");
	my @files = readdir(DIR);
	my $numerofarrayitems = @files;
	my $counter = "0";
	while ($counter <= $numerofarrayitems) {
		if ($files[$counter] =~ 'jpg') {
			my ($treasure, $trash) = split(/\./,$files[$counter]);
			$staff_image_onfile{$treasure} = "yes";
#			print "<br>$treasure";
		} # END IF
		$counter++;
	} # END WHILE
#	}
	##################################################################################
	## END: OPEN DIRECTORY, READ FILE LIST, AND PUSH VALID IMAGES INTO SECOND ARRAY
	##################################################################################


#####################################################################################
# START: PRINT STAFF PROFILE FOR SEDL INTRANET WITH ALTERNATE PICTURE, IF AVAILABLE #
#####################################################################################
print header;






#####################################################################################
# START: PRINT LIST OF STAFF FOR INTRANET #
#####################################################################################
if (($showuserid eq 'all') && ($list_view eq 'birthday')) {
## PRINT PAGE HEADER
print <<EOM;
<html>
<head>
<title>SEDL Staff List (Birthdays)</title>
$htmlheaderstaff

<div class="padding15_no_print_padding">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:2px;">
<tr><td valign="top" style="font-size:12px;"><strong>SEDL Staff Birthdays</STRONG>
		<div class="do_not_print">
		<a href="staffprofiles-print.cgi?intranetonly=yes&list_view=firstname&showuserid=all">First Name</A>, 
		<a href="staffprofiles-print.cgi?intranetonly=yes&list_view=lastname&showuserid=all">Last Name</A>, 
		<a href=\"staffprofiles-print.cgi?intranetonly=yes&list_view=deptname&showuserid=all\">Dept.</A>
		</div>
	</td>
	<td valign="top" style="text-align:right;"><strong>effective $date_full_pretty_4digityear</strong><br>
		<div class="do_not_print" style="text-align:right;">
		<a href="http://www.sedl.org/staff/personnel/staffprofiles.cgi?intranetonly=yes&showuserid=all&list_view=birthday">Intranet Version</a>
		</div>
</td></tr>
</table>

</div>
EOM


my $list_missing_bdays = "";
	my $command = "select firstname, lastname, birthmonth from staff_profiles where show_birthday NOT LIKE '%no%'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#	print "<p class=\"info\">MATCHES: $num_matches</p>";
	while (my @arr = $sth->fetchrow) {
	    $count++;
		my ($firstname, $lastname, $birthmonth) = @arr;
		if ($birthmonth eq '') {
			$list_missing_bdays .= "<br>" if ($list_missing_bdays ne ''); 
			$list_missing_bdays .= "$firstname $lastname"; 
		}
	} # END DB QUERY LOOP

my @monthlist = ("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
my @monthlonglist = ("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
my $numberofmonths = "12";
my $loopingmonths = "0";
my $lastmonth = "";

## LOOP THROUGH ARRAY OF DEPARTMENTS TO DRAW EACH DEPARTMENT'S STAFF LIST
print<<EOM;
<TABLE CELLPADDING="0" cellspacing="0" border="0" WIDTH="100%" style="margin-top:2px;">
<TR><TD valign="top">
EOM

while ($loopingmonths < $numberofmonths) {
	my $monthdone = "no";
	## OPEN THE DATABASE AND SEND THE QUERY
	my $command = "select * from staff_profiles where birthmonth like '$monthlist[$loopingmonths]' AND show_birthday NOT LIKE '%no%' order by birthmonth, birthday, lastname, firstname";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	while (my @arr = $sth->fetchrow) {
	    $count++;
		my ($fm_record_id, $firstname, $middleinitial, $lastname, $jobtitle, $phone, $userid, $email, $phoneext, $birthmonth, $birthday, $timesheetname, $department_abbrev, $responsibilities, $experience, $mgmtcouncil, $lastupdated, $lastupdated_by, $room_number, $start_date, $adjusted_start_date, $stafflistsorting, $supervised_by, $automated_sentence, $photo_permissions, $show_birthday, $external_publications, $strong_pwd, $empl_type, $degree, $is_bgt_auth, $is_auth_rep, $areas_expertise_list, $other_SEDL_workgroup, $immediate_supervisor_sims_user_ID, $bgt_auth_primary_sims_user_ID) = @arr;

		print "</TD>\n\n<TD valign=\"top\">" if (($monthdone eq 'no') && (($birthmonth eq 'May') || ($birthmonth eq 'Sep')));
		print "<P><TABLE BORDER=\"1\" cellpadding=\"2\" cellspacing=\"0\" WIDTH=\"200\"><TR><TD COLSPAN=\"2\" style=\"background-color:#ebebeb;\"><P><strong>$monthlonglist[$loopingmonths]</strong></TD></TR>" if ($monthdone eq 'no');
		$monthdone = "yes";
		## CLEAN UP ACCENTS
		$firstname = &cleanaccents2html ($firstname);
		$lastname = &cleanaccents2html ($lastname);


		print "<TR><TD valign=\"top\" ALIGN=\"RIGHT\">$birthday</TD>
					<TD><a href=\"/staff/personnel/staffprofiles.cgi?intranetonly=yes&showuserid=$userid\" class=\"nolinkstyle\">$firstname $lastname</A></TD></TR>";
	}  ## END LOOP FOR THIS MONTH

	$loopingmonths++;
	print "</TABLE>\n\n";
}  ## END WHILE LOOP THAT GOES THROUGH EACH MONTH

## PRINT FOOTER
print<<EOM;
</td></tr>
</table>

</div>
$htmlfooterstaff
EOM

} ## END IF SHOWUSERID = ALL, list_view = 'birthday

##################################################################################################################

if (($showuserid eq 'all') && ($list_view =~ 'name')) {
my $list_view_label = "First Name";
   $list_view_label = "Last Name" if ($list_view eq 'lastname');
   $list_view_label = "Department" if ($list_view =~ 'dept');

## PRINT PAGE HEADER
print <<EOM;
<html>
<head>
<title>SEDL Staff List (by $list_view_label)</title>
$htmlheaderstaff
<div class="padding15_no_print_padding">

EOM

my $heading_firstname = "<strong>First</strong>";
my $heading_lastname = "<strong>Last</strong>";
my $heading_department = "<strong>Dept.</strong>";

$heading_firstname = "<a href=\"staffprofiles-print.cgi?intranetonly=yes&list_view=firstname&showuserid=all\" class=\"nolinkstyleprint\">First</A>" if ($list_view ne 'firstname');
$heading_lastname = "<a href=\"staffprofiles-print.cgi?intranetonly=yes&list_view=lastname&showuserid=all\" class=\"nolinkstyleprint\">Last</A>" if ($list_view ne 'lastname');
$heading_department = "<a href=\"staffprofiles-print.cgi?intranetonly=yes&list_view=deptname&showuserid=all\" class=\"nolinkstyleprint\">Dept.</A>" if ($list_view ne 'deptname');

my $this_sortby = "lastname, firstname";
   $this_sortby = "firstname, lastname" if ($list_view eq 'firstname');
   $this_sortby = "department_abbrev, stafflistsorting DESC, firstname, lastname" if ($list_view eq 'deptname');
my $this_sortby_label = "Last Name";
   $this_sortby_label = "First Name" if ($list_view eq 'firstname');
   $this_sortby_label = "Department" if ($list_view eq 'deptname');


my $this_bgcolor = "ffffff";
	if ($list_view eq 'deptname') {
		$this_bgcolor = "EBEBEB";
	}
my $last_bgcolor = "";
my $last_department = "";
my $this_dept_bgcolor = "FFFFFF";
	my $command = "select * from staff_profiles order by $this_sortby";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	my $halfway = $num_matches / 2;
		$halfway = &format_number($halfway, "0","no"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
	   $halfway = $halfway + 1 if (($list_view eq 'firstname') || ($list_view eq 'deptname'));
	   $halfway = $halfway - 1 if ($list_view eq 'lastname');

	   $num_matches = $num_matches + 1 if ($halfway + $halfway <= $num_matches);



my $table_heading_text = "
<TABLE BORDER=\"1\" CELLPADDING=\"1\" CELLSPACING=\"0\" style=\"width:94%;\">
<TR style=\"background-color:#ebebeb;\"><TD><strong>Name</strong><div class=\"do_not_print\">($heading_firstname, $heading_lastname)</div></TD>
	<TD><strong>Phone-direct</strong></TD>
	<TD align=\"center\"><strong>$heading_department</strong></TD>
	<TD align=\"center\"><strong>Rm</strong></TD>
</TR>
";

print<<EOM;
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:2px;">
<tr><td valign="top" style="font-size:12px;"><strong>SEDL Staff Directory - by $this_sortby_label</strong></td>
<td valign="top" style="text-align:right;"><strong>effective $date_full_pretty_4digityear</strong><br>
<div class="do_not_print" style="text-align:right;">
<a href="http://www.sedl.org/staff/personnel/staffprofiles.cgi?intranetonly=yes&list_view=lastname&showuserid=all">Intranet Version</a>
</div>
</td></tr>
</table>

<p class="do_not_print">
You may view the staff list sorted by <a href=\"staffprofiles-print.cgi?intranetonly=yes&amp;showuserid=all&amp;list_view=birthday\">Birthday</a> 
or by any of the column headings below.<br>
Call 512-552-5522 for security after normal SEDL hours.
</p>

<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
<tr><td valign="top" style="width:50%">
$table_heading_text
EOM
	while (my @arr = $sth->fetchrow) {
	    $count++;
		my ($fm_record_id, $firstname, $middleinitial, $lastname, $jobtitle, $phone, $userid, $email, $phoneext, $birthmonth, $birthday, $timesheetname, $department_abbrev, $responsibilities, $experience, $mgmtcouncil, $lastupdated, $lastupdated_by, $room_number, $start_date, $adjusted_start_date, $stafflistsorting, $supervised_by, $automated_sentence, $photo_permissions, $show_birthday, $external_publications, $strong_pwd, $empl_type, $degree, $is_bgt_auth, $is_auth_rep, $areas_expertise_list, $other_SEDL_workgroup, $immediate_supervisor_sims_user_ID, $bgt_auth_primary_sims_user_ID) = @arr;
		if ($count == ($halfway + 1)) {
			print "</table>
						</td>
						<td valign=\"top\" style=\"width:50%;text-align:center;\">$table_heading_text
							
						";
		} # END IF
		my $bold_or_italics_begin = "";
		my $bold_or_italics_end = "";
		if ($mgmtcouncil eq 'Yes') {
			$bold_or_italics_begin = "<strong>";
			$bold_or_italics_end = "</strong>";
		}
		if ($empl_type eq 'Hourly') {
			$bold_or_italics_begin = "<em>";
			$bold_or_italics_end = "</em>";
		}

		if ($room_number eq '') {
			$room_number = "LA" if ($lastname eq 'Brown');
			$room_number = "MS" if ($lastname eq 'Chapman');
			$room_number = "LA" if ($lastname eq 'Chauvin');
			$room_number = "GA" if ($lastname eq 'Copeland');
			$room_number = "LA" if ($lastname eq 'Finlay');
			$room_number = "SC" if ($lastname eq 'Howard');
			$room_number = "BR/Met" if ($lastname eq 'Jarvis');
			$room_number = "BR" if ($lastname =~ 'Madison');
			$room_number = "AL" if ($lastname eq 'Meadows');
			$room_number = "MS" if ($lastname eq 'Meibaum');
			$room_number = "LA" if ($lastname eq 'Moreno');
			$room_number = "LA" if ($lastname eq 'Theodore');
			$room_number = "LA" if ($lastname eq 'Times');
		}
		$firstname = &cleanaccents2html ($firstname);
		$lastname = &cleanaccents2html ($lastname);
#		$this_bgcolor = "EBEBEB" if ($last_bgcolor eq 'FFFFFF');
#		$this_bgcolor = "FFFFFF" if ($last_bgcolor eq 'EBEBEB');
		if ($list_view eq 'deptname') {
			$this_dept_bgcolor = "EBEBEB" if (($last_bgcolor eq 'FFFFFF') && ($department_abbrev ne $last_department));
			$this_dept_bgcolor = "FFFFFF" if (($last_bgcolor eq 'EBEBEB') && ($department_abbrev ne $last_department));
		}
		$this_bgcolor = $this_dept_bgcolor if ($list_view eq 'deptname');
#		if (($list_view eq 'deptname') && ($last_department ne $department_abbrev)) {
#			print $table_heading_text;
#		}

my $rowspan="";
    $rowspan=" rowspan=\"2\"" if ($lastname eq 'Jarvis');
print<<EOM;
<TR style=\"background-color: $this_bgcolor\">
						<TD valign=\"top\" NOWRAP $rowspan>$bold_or_italics_begin<a href=\"/staff/personnel/staffprofiles.cgi?intranetonly=yes&showuserid=$userid\" class=\"nolinkstyle\">
EOM
		if (($list_view eq 'firstname') || ($list_view eq 'deptname')) {
			print "$firstname $lastname";
		} else {
			print "$lastname, $firstname$bold_or_italics_end";
		}
print "$bold_or_italics_end</A></TD>";
print<<EOM;
	<TD VALIGN="TOP">$bold_or_italics_begin$phone$bold_or_italics_end</TD>
	<TD VALIGN="TOP" style="text-align:center;">$bold_or_italics_begin$department_abbrev$bold_or_italics_end</TD>
	<td VALIGN="TOP" style="text-align:center;">$bold_or_italics_begin$room_number$bold_or_italics_end</td>
</TR>
EOM
		#############################################
		## START: SPECIAL HANDLING FOR ROBIN JARVIS
		#############################################
		if ($lastname eq 'Jarvis') {
			my $jarvis_bgcolor = "";
#			   $jarvis_bgcolor = "style=\"background-color:#ebebeb;\"" if ($list_view eq 'deptname');
print<<EOM;
<tr $jarvis_bgcolor><TD VALIGN="TOP">$bold_or_italics_begin 504-838-0606$bold_or_italics_end</TD>
	<TD VALIGN="TOP" style="text-align:center;">$bold_or_italics_begin$department_abbrev$bold_or_italics_end</TD>
	<td VALIGN="TOP" style="text-align:center;">$bold_or_italics_begin Met$bold_or_italics_end</td>
</tr>
EOM
		} # END IF
		#############################################
		## END: SPECIAL HANDLING FOR ROBIN JARVIS
		#############################################
		
		$last_bgcolor = $this_bgcolor;
		$last_bgcolor = $this_dept_bgcolor if ($list_view eq 'deptname');
		$last_department = $department_abbrev;
	}  ## END DB QUERY LOOP


## PRINT FOOTER
print<<EOM;
</TABLE>

</td></tr>
</table>



<table border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-top:5px;">
<tr><td style="width:34%;font-size:9px;">
	AFC: Afterschool, Family, and Community<br>
	AS: Administrative Services<br>
	COM: Communications<br>
	DEV: Development
</td>
<td valign="top" style="width:33%;font-size:9px;">
	DRP: Disability Research to Practice<br>
	EO: Executive Office<br>
	ESS: Education Systems Support<br>
	R&amp;E: Research and Evaluation<br>
	REL: Regional Educational Laboratory<br>
</td>
<td valign="top" style="width:33%;font-size:9px;">
	<strong>SEDL Management Team in bold</strong><br>
	<em>Temporary Employees in italics</em>
</td></tr>
</table>
</div>
EOM

print<<EOM;
<div class="pagebreak"></div>
EOM

&staffprofiles_shared_functions::print_additional_numbers();

print<<EOM;
$htmlfooterstaff
EOM
} ## END IF SHOWUSERID = ALL, list_view =~ 'name'
#####################################################################################
# END:  PRINT LIST OF STAFF FOR INTRANET #
#####################################################################################






######################################################################
##  Espanol Accent character replacement loop & Clean
######################################################################

sub cleanthis {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/Ò/"/g;         
   $dirtyitem =~ s/Ó/"/g;         
   $dirtyitem =~ s/Õ/'/g;         
   $dirtyitem =~ s/Ô/'/g;
   $dirtyitem =~ s// /g;
   $dirtyitem =~ s/Ñ/--/g;
   $dirtyitem =~ s/Ê//g; # invisible bullet
   return ($dirtyitem);
}

sub cleanaccents2html {
my $cleanitem = $_[0];
	$cleanitem =~ s/R&E/R&amp;E/g;			
	$cleanitem =~ s/Acuna/Acu&ntilde;a/g;			
	$cleanitem =~ s/\/\>/\>/gi; # REMOVE SINGLETON TAGS THAT ARE SELF-CLOSING

	$cleanitem =~ s/Ò/"/g;			
	$cleanitem =~ s/Ó/"/g;			
	$cleanitem =~ s/Õ/'/g;			
	$cleanitem =~ s/Ô/'/g;
	$cleanitem =~ s// /g;
	$cleanitem =~ s/-/\&ndash\;/g;
	$cleanitem =~ s/Ñ/\&mdash\;/g;
	$cleanitem =~ s/Ê//g; # invisible bullet
	$cleanitem =~ s/É/.../g;
	$cleanitem =~ s/Ë/&Agrave\;/g; 
	$cleanitem =~ s/ˆ/&agrave\;/g;	
	$cleanitem =~ s/ç/&Aacute\;/g;  
	$cleanitem =~ s/‡/&aacute\;/g;
	$cleanitem =~ s/å/&Acirc\;/g;
	$cleanitem =~ s/‰/&acirc\;/g;
	$cleanitem =~ s/Ì/&Atilde\;/g;
	$cleanitem =~ s/‹/&atilde\;/g;
	$cleanitem =~ s/€/&Auml\;/g;
	$cleanitem =~ s/Š/&auml\;/g;
	$cleanitem =~ s/ƒ/&Eacute\;/g;
	$cleanitem =~ s/Ž/&eacute\;/g;
	$cleanitem =~ s/é/&Egrave\;/g;
	$cleanitem =~ s//&egrave\;/g;
	$cleanitem =~ s/æ/&Euml\;/g;
	$cleanitem =~ s/‘/&euml\;/g;
	$cleanitem =~ s/í/&Igrave\;/g;
	$cleanitem =~ s/“/&igrave\;/g;
	$cleanitem =~ s/ê/&Iacute\;/g;
	$cleanitem =~ s/’/&iacute\;/g;
	$cleanitem =~ s/ë/&Icirc\;/g;
	$cleanitem =~ s/”/&icirc\;/g;
	$cleanitem =~ s/ì/&Iuml\;/g;
	$cleanitem =~ s/•/&iuml\;/g;
	$cleanitem =~ s/„/&Ntilde\;/g;
	$cleanitem =~ s/–/&ntilde\;/g;
	$cleanitem =~ s/ñ/&Ograve\;/g;
	$cleanitem =~ s/˜/&ograve\;/g;
	$cleanitem =~ s/î/&Oacute\;/g;
	$cleanitem =~ s/—/&oacute\;/g;
	$cleanitem =~ s/Í/&Otilde\;/g;
	$cleanitem =~ s/›/&otilde\;/g;
	$cleanitem =~ s/…/&Ouml\;/g;
	$cleanitem =~ s/š/&ouml\;/g;
	$cleanitem =~ s/ô/&Ugrave\;/g;
	$cleanitem =~ s//&ugrave\;/g;
	$cleanitem =~ s/ò/&Uacute\;/g;
	$cleanitem =~ s/œ/&uacute\;/g;
	$cleanitem =~ s/ó/&Ucirc\;/g;  ## THIS REPLACES THE — FOR SOME REASON
	$cleanitem =~ s/ž/&ucirc\;/g;
	$cleanitem =~ s/†/&Uuml\;/g;
	$cleanitem =~ s/Ÿ/&uuml\;/g;
	$cleanitem =~ s/Ø/&yuml\;/g;
	return ($cleanitem);
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


#################################################################
## START SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################
sub date2standard {
	my $date2transform = $_[0];
	   $date2transform =~ s/\ //g;
	   $date2transform =~ s/\-/\//g;
	my $show_year = $_[1];
	my ($thisyear, $thismonth, $thisdate) = split(/\//,$date2transform);
	   $date2transform = "$thismonth\/$thisdate\/$thisyear";
	   $date2transform = "$thismonth\/$thisdate" if ($show_year eq 'noyear');
	   $date2transform = "" if $thismonth eq '';
	return($date2transform);
}
#################################################################
## END SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################

#################################################################

####################################################################
## START: ROUND NUMBER TO X PLACES, WITH OR WITHOUT COMMAS
####################################################################
# EXAMPLE OF USAGE
# $num = &format_number($num, "0","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
sub format_number {
	my $number_unformatted = $_[0];
	   $number_unformatted = 0 if ($number_unformatted eq '');
	   $number_unformatted =~ s/\,//g; # REMOVE COMMA IF ALREADY EXISTS 
	my $decimal_places = $_[1];
		$decimal_places = "2" if ($decimal_places eq '');
	my $commas_included = $_[2];

	my $x = new Number::Format;
	my $number_formatted = $x->format_number($number_unformatted, $decimal_places, $decimal_places);
		if ($commas_included ne 'yes') {
			$number_formatted =~ s/\,//g;
		}
	return($number_formatted);
}
####################################################################
## END: ROUND NUMBER TO X PLACES, WITH OR WITHOUT COMMAS
####################################################################

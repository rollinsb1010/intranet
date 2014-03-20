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
use lib '/home/httpd/html/staff/personnel/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use staffprofiles_shared_functions;

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 
use intranetcommoncode; 

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
my $htmlhead = "";
my $htmltail = "";

my $htmlfooterwithsearch = "";

###########################################
# START: GRAB SEDL HEADER AND FOOTER HTML #
###########################################
my $template = "";
#open(TEMPLATE,"</home/httpd/html/store/template.html");
open(TEMPLATE,"</home/httpd/html/common/templates/sedl2008.html");
	while (<TEMPLATE>) {
		$template .= $_;
	}
close(TEMPLATE);

my ($pre_title, $header, $bodystart, $pre_sidenav, $pre_centerpiece, $footer) = split(/QQQ/,$template);
	$pre_centerpiece =~ s/SiteSectionNamePlaceholder/About SEDL/gi;
###########################################
# END: GRAB SEDL HEADER AND FOOTER HTML #
###########################################

#my %manager_iphone_numbers;
#$manager_iphone_numbers{'mboethel'} = "512-705-7278";
#$manager_iphone_numbers{'jwestbro'} = "512-705-7279";
#$manager_iphone_numbers{'vdimock'} = "512-705-7305";
#$manager_iphone_numbers{'mvadenki'} = "512-705-7290";
#$manager_iphone_numbers{'cmoses'} = "512-705-7308";
#$manager_iphone_numbers{'rjarvis'} = "225-505-6125";
#$manager_iphone_numbers{'akriegel'} = "512-461-2640";
#$manager_iphone_numbers{'cjordan'} = "512-658-5734";

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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("9"); # 9 is the PID for this page in the intranet database

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

#my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
#   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;

my $this_year_5 = $year - 5;
my $this_year_10 = $year - 10;
my $this_year_15 = $year - 15;
my $this_year_20 = $year - 20;
my $this_year_25 = $year - 25;
my $this_year_30 = $year - 30;
my $this_year_35 = $year - 35;
my $this_year_40 = $year - 40;

my $yearsago_5 = "$this_year_5\-$month\-$monthdate_wleadingzero";
my $yearsago_10 = "$this_year_10\-$month\-$monthdate_wleadingzero";
my $yearsago_15 = "$this_year_15\-$month\-$monthdate_wleadingzero";
my $yearsago_20 = "$this_year_20\-$month\-$monthdate_wleadingzero";
my $yearsago_25 = "$this_year_25\-$month\-$monthdate_wleadingzero";
my $yearsago_30 = "$this_year_30\-$month\-$monthdate_wleadingzero";
my $yearsago_35 = "$this_year_35\-$month\-$monthdate_wleadingzero";
my $yearsago_40 = "$this_year_40\-$month\-$monthdate_wleadingzero";
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
	$htmlhead =~ s/\<option value\=\"$cookie_search_fav\"\>/\<option value\=\"$cookie_search_fav\" SELECTED\>/gi;
}
## END: SET SEARCH FORM TO USER'S LAST DATA SOURCE SEARCH SELECTION



my $intranetonly = $query->param("intranetonly");
my $id = $query->param("id");
   $intranetonly = "yes" if ($id ne '');
   $id = "all" if ($id eq '');
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

if ($intranetonly eq '') {

	## DELETE TEMP RECORD
	my $command_delete_temp = "delete from staff_profiles where lastname LIKE 'Temp'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_delete_temp) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;

	############################################################################################
	############################################################################################
	## MAKE THE STAFF DIRECTORY/LIST PAGES (SEDL-NAME, SEDL-DEPARTMENT) 
	############################################################################################
	############################################################################################

	## PRINT MESSAGE TO USER WHO IS UPDATING THE DATABASE USING UPLOAD PAGE IN "SEDL STAFF" AREA
if ($debug eq '1') {
	print header;
	print "<BODY BGCOLOR=\"#FFFFFF\"><H2>Generating the staff directory pages</H2>";
}


	########################################################################################################################
	# START: GENERATE EUDORA NICKNAMES FILE AND ADDRESS BOOK VCF FILE 
	########################################################################################################################
	## OPEN A FILE TO SAVE THE STAFF LIST BY DEPARTMENT
	open (VCF,">/home/httpd/html/staff/personnel/sedlstaff.vcf");
	open (NICKNAMES,">/home/httpd/html/staff/personnel/eudora-nicknames.txt");
	open (STAFFARRAY,">/home/httpd/html/staff/personnel/sedlstaff-array.txt");
#	open (STAFFARRAY_BA,">/home/httpd/html/staff/personnel/sedlstaff-array-ba.txt");
#	open (STAFFARRAY_SUTHREP,">/home/httpd/html/staff/personnel/sedlstaff-array-authrep.txt");

	## PRINT SEDL-WIDE ADDRESSES
	print NICKNAMES "alias All-SEDL-Staff sedl-staff\@sedl.org\n";
	print NICKNAMES "alias All-Austin-Staff austin-staff\@sedl.org\n";



print VCF<<EOM;
BEGIN:VCARD
VERSION:3.0
N:;;;;
FN:All-SEDL-Staff
NICKNAME:All-SEDL-Staff
EMAIL;type=INTERNET;type=WORK;type=pref:sedl-staff\@sedl.org
CATEGORY:SEDL Staff
END:VCARD

BEGIN:VCARD
VERSION:3.0
N:;;;;
FN:All-Austin-Staff
NICKNAME:All-Austin-Staff
EMAIL;type=INTERNET;type=WORK;type=pref:austin-staff\@sedl.org
CATEGORY:SEDL Staff
END:VCARD

EOM

	## QUERY STAFF PROFILES DB FOR OTHER E-MAIL ADDRESSES
	my $command = "select firstname, lastname, phone, userid, email, phoneext, department_abbrev from staff_profiles where 
					firstname NOT LIKE '' 
					AND start_date <= '$date_full_mysql'
					order by firstname, lastname";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
if ($debug eq '1') {
	print "MATCHES: $num_matches";
}
		while (my @arr = $sth->fetchrow) {
#			my ($fm_record_id, $firstname, $middleinitial, $lastname, $jobtitle, $phone, $userid, $email, $phoneext, $birthmonth, $birthday, $timesheetname, $department_abbrev, $responsibilities, $experience, $mgmtcouncil, $lastupdated, $lastupdated_by, $room_number, $start_date, $adjusted_start_date, $stafflistsorting, $supervised_by, $automated_sentence, $photo_permissions, $show_birthday, $external_publications, $strong_pwd, $empl_type, $degree, $is_bgt_auth, $is_auth_rep) = @arr;
			my ($firstname, $lastname, $phone, $userid, $email, $phoneext, $department_abbrev) = @arr;
				$staff_user_exists{$userid} = "yes"; # TRACK WHICH USER IDS ARE CURRENT STAFF, FOR USE WHEN REMOVING ORPHAN PROFILE PAGES LATER ON
			my $phoneext_label = $phoneext;
			$phoneext_label = "ext. $phoneext" if ($phoneext_label ne '');
			my $email2 = lc($firstname);
			   $email2 .= ".";
			   $email2 .= lc($lastname);
			   $email2 .= "\@sedl.org";
			   $email2 =~ s/'//gi;
			my $department_fullname = &abbr_to_department_name($department_abbrev);

print VCF "BEGIN:VCARD
VERSION:3.0
N:$lastname;$firstname;;;
FN:$firstname $lastname
ORG:SEDL;$department_fullname
EMAIL;type=INTERNET;type=WORK;type=pref:$email
TEL;type=WORK;type=pref:$phone $phoneext_label
CATEGORY:SEDL Staff
END:VCARD
";
			$firstname =~ s/ /\-/g;
			$lastname =~ s/ /\-/g;
			print NICKNAMES "alias $firstname\-$lastname $email\n";
			print STAFFARRAY "$userid\n";
		} # END DB QUERY LOOP
	close (VCF);
	close (NICKNAMES);
	close (STAFFARRAY);
#	close (STAFFARRAY_BA);
#	close (STAFFARRAY_AUTHREP);

if ($debug eq '1') {
	print "<P>FINISHED WITH PAGE: <a href=\"/staff/personnel/sedlstaff-pullmenu.txt\">Pull-down menu list of staff</A><P>\n";
	print "<P>FINISHED WITH PAGE: <a href=\"/staff/personnel/sedlstaff.vcf\">VCF Address Book file for SEDL Staff</A><P>\n";
	print "<P>FINISHED WITH PAGE: <a href=\"/staff/personnel/eudora-nicknames.txt\">Eudora Nicknames</A><P>\n";
}
########################################################################################################################
# END: GENERATE EUDORA NICKNAMES FILE AND ADDRESS BOOK VCF FILE 
########################################################################################################################


########################################################################################################################
# START: LIST SEDL STAFF BY DEPARTMENT 
########################################################################################################################

## OPEN A FILE TO SAVE THE STAFF LIST BY DEPARTMENT
open(LISTBYDEPARTMENT,">/home/httpd/html/about/staff2.html");
open(RE_LISTBYDEPARTMENT,">/home/httpd/html/re/staff_include.txt");

## THIS ARRAY STORES THE DEPARTMENT NAMES SO WE CAN USE A LOOP TO MAKE THOSE SECTIONS OF THE STAFF LIST BY DEPARTMENT
my @departmentlist = (
	"Executive Office", 
	"Administrative Services department", 
	"Afterschool, Family, and Community program", 
	"Communications department", 
	"Development department",
	"Disability Research to Practice (DRP) program",
	"Improving School Performance program",
	"Research and Evaluation (R&E) program");
my @departmentlistwithlink = (
	"<strong>Executive Office</strong>", 
	"<strong>Administrative Services department</strong>", 
	"<strong>Afterschool, Family, and Community program</strong><br>
		Projects under this program include:
		<ul>
		<li><a href=\"http://www.sedl.org/connections/\">National Center for Family and Community Connections with Schools</A></li>
		<li><a href=\"/afterschool/\">National Center for Quality Afterschool</A></li>
		<li><a href=\"http://www.nationalpirc.org\">National PIRC Coordination Center</A></li>
		</ul>
		", 
	"<strong>Communications department</strong>", 
	"<strong>Development department</strong>",
	"<strong>Disability Research to Practice (DRP) program</strong><br>
		Projects under this program include:
		<ul>
		<li><a href=\"http://www.kter.org/\">Center on Knowledge Translation for Employment Research</a> (KTER Center)</li>
		<li><a href=\"http://www.sedl.org/new/pressrelease/20120820_305.html\">Knowledge Translation for Disability and Rehabilitation Research Center</a> (KTDRR)</li>
		<li><a href=\"http://autism.sedl.org/\">Vocational Rehabilitation Service Models for Individuals with Autism Spectrum Disorders</a></li>
		<li>Southwest ADA Center Research project</li>
		</ul>",
	"<strong>Improving School Performance program</strong><br>
		Projects under this program include:
		<ul>
		<li><a href=\"http://www.centerforcsri.org\">Center for Comprehensive School Reform and Improvement</a></li>
		<li><a href=\"http://secc.sedl.org\">Southeast Comprehensive Center</A> (SECC)</li>
		<li><a href=\"http://txcc.sedl.org\">Texas Comprehensive Center</A> (TXCC)</li>
		</ul>",
	"<strong><a href=\"/re/\">Research and Evaluation (R&E)</a></strong>");


my @departmentpagereference = ("EO", "AS", "AFC", "COM", "DEV", "DRP", "ISP", "R&E");

## PRINT PAGE HEADER
print LISTBYDEPARTMENT <<EOM;
$pre_title
SEDL Staff Directory by Program/Department

$header
$bodystart
$pre_sidenav
	<p class="tocheader">Our Company</p>
    
   <div id="nav">
   <ul class="level1">
      	<li class="submenu"><a href="/about/mission.html" id="mission">Our Mission</a></li>
		<li class="submenu"><a href="/about/help.html">How We Can Help</a></li>
		<li class="submenu"><a href="/about/annualreport.html" id="report">Annual Report</a></li>
		<li class="submenu"><a href="/about/funding.html" id="funding">Funding</a></li>
		<li class="submenu"><a href="/40years/">40 Years of History</a></li>
		<li class="submenu"><a href="/about/art-building.html">Art in SEDL Headquarters</a></li>
        <li class="submenu"><a href="/about/green.html">Green Building</a></li>
		<li class="submenu"><a href="/cgi-bin/mysql/corp/contact.cgi" id="contact">Contact Us</a></li>
		<li class="submenu"><a href="/support/">Support SEDL</a></li>
	</ul>
  </div>
  <p class="tocheader2">Our People</p>
   <div id="nav2">
   <ul class="level1">
      	<li class="submenu"><a href="/about/board.html" id="Board">Board of Directors</a></li>
		<li class="submenu"><a href="/about/management.html" id="Management">SEDL Management Council</a></li>
		<li class="submenu active"><a href="/about/staff.html" id="Staff">Staff</a></li>
		<li class="submenu"><a href="/about/partners.html" id="Partners">Partners</a></li>
        <li class="submenu"><a href="/about/careers.html" id="Careers">Careers</a></li>
	</ul>

	</div>

$pre_centerpiece

<!-- This page was autogenerated by the staff_profiles database on $date_full_pretty_4digityear_withtime.  See the webmaster for more details. -->


<table border="0" cellpadding="0" cellspacing="2" width="100%" summary="This table is a list of SEDL staff members">
<tr><td><h1>SEDL Staff Directory</h1></td>
	<td valign="bottom">By Program/Department</td>
	<td><img src="/images/spacer.gif" width="3" height="1" alt=""></td>
	<td valign="bottom"><a href="/about/staff.html">By Last Name of Staff</A></td>
</tr>
</TABLE>
EOM

	###################################################
	## START: PRINT LIST OF DEPARTMENTS AT TOP OF PAGE
	###################################################
	my $numberofdepartments = $#departmentpagereference;
	my $loopingdepartments = "0";
print LISTBYDEPARTMENT<<EOM;
<p></p>
<TABLE>
<TR><TD COLSPAN=2><strong>SEDL Programs/Departments</strong></TD></TR>
<TR><TD valign="top">
		<TABLE>
EOM
		while ($loopingdepartments <= $numberofdepartments) {
my $department_name = $departmentlist[$loopingdepartments];
   $department_name =~ s/&amp;/&/gi;
   $department_name =~ s/&/&amp;/gi;
my $department_name_ref = $departmentpagereference[$loopingdepartments];
   $department_name_ref =~ s/&amp;/&/gi;
   $department_name_ref =~ s/&/&amp;/gi;

print LISTBYDEPARTMENT <<EOM;
<TR><TD valign="top"><a href="\#$department_name_ref"><IMG SRC="/images/arrows/bluearrow-down7.gif" ALT="jump to $department_name_ref" class="noBorder"></A></TD>
	<td valign="top"><a href="\#$department_name_ref">$department_name</A></TD></TR>
EOM
			# IF MIDWAY THROUGH THE LIST, START SECOND COLUMN
			if ($loopingdepartments eq '4') {
				print LISTBYDEPARTMENT "</TABLE></TD><TD valign=\"top\"><TABLE>";
			}
			$loopingdepartments++;
		}
	print LISTBYDEPARTMENT "\n";
	###################################################
	## END: PRINT LIST OF DEPARTMENTS AT TOP OF PAGE
	###################################################

print LISTBYDEPARTMENT <<EOM;
		</table>
	</td></tr>
	</TABLE>

<p></p>
<table border="0" cellpadding="3" cellspacing="2" width="100%" class="table-staff" summary="This table is a list of SEDL staff members">
<tr style="background-color: #C0D6DD">
	<th style="text-align:left;"><strong>Staff Member</strong></th>
	<th style="text-align:left;"><strong>Title</strong></th>
	<th style="text-align:left;"><strong>Phone</strong></th></tr>
EOM

	my $numberofdepartments = $#departmentpagereference;
	my $loopingdepartments = "0";

		## LOOP THROUGH ARRAY OF DEPARTMENTS TO DRAW EACH DEPARTMENT'S STAFF LIST
		while ($loopingdepartments <= $numberofdepartments) {

			# DRAW THIS DEPARTMENT'S HEADER
my $department_name = $departmentlistwithlink[$loopingdepartments];
   $department_name =~ s/&amp;/&/gi;
   $department_name =~ s/&/&amp;/gi;
my $department_name_ref = $departmentpagereference[$loopingdepartments];
   $department_name_ref =~ s/&amp;/&/gi;
   $department_name_ref =~ s/&/&amp;/gi;
print LISTBYDEPARTMENT <<EOM;
              <tr style="background-color: #C0D6DD"> 
                <td colspan="4"> <a name="$department_name_ref"></a> $department_name</td>
              </tr>
EOM

if ($department_name =~ 'Family') {
print LISTBYDEPARTMENT <<EOM;
<tr> 
	<td valign="top" style="background-color:#FDFEBE;"><a href="/pubs/catalog/authors/lwood.html">Wood, Lacy</a></td>
	<td valign="top" style="background-color:#FDFEBE;">Associate Development Director</td>
	<td valign="top" style="background-color:#FDFEBE;">512-391-6567</td></tr>
EOM
}

			## CONSTRUCT THE SQL QUERY
			my $command = "select * from staff_profiles 
				where (department_abbrev LIKE '$departmentpagereference[$loopingdepartments]') 
				AND firstname NOT LIKE '' 
				AND start_date <= '$date_full_mysql'
				order by mgmtcouncil DESC, stafflistsorting DESC, lastname";

#			print "<P>COMMAND: $command<P>";

			## OPEN THE DATABASE AND SEND THE QUERY
			my $dsn = "DBI:mysql:database=intranet;host=localhost";
			my $dbh = DBI->connect($dsn, "intranetuser", "limited");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
			my $bgcolor_alternating = "#FEFCE3";

				################################################################
				# START: LOOP THAT PRINTS STAFF LIST FOR INDIVIDUAL DEPARTMENT #
				################################################################
				while (my @arr = $sth->fetchrow) {
    				$count++;
 					my ($fm_record_id, $firstname, $middleinitial, $lastname, $jobtitle, $phone, $userid, $email, $phoneext, $birthmonth, $birthday, $timesheetname, $department_abbrev, $responsibilities, $experience, $mgmtcouncil, $lastupdated, $lastupdated_by, $room_number, $start_date, $adjusted_start_date, $stafflistsorting, $supervised_by, $automated_sentence, $photo_permissions, $show_birthday, $external_publications, $strong_pwd, $empl_type, $degree, $is_bgt_auth, $is_auth_rep, $areas_expertise_list, $other_SEDL_workgroup, $immediate_supervisor_sims_user_ID, $bgt_auth_primary_sims_user_ID) = @arr;

						if ($bgcolor_alternating eq "#FEFCE3") {
							$bgcolor_alternating = "#FDFEBE";
						} else {
							$bgcolor_alternating = "#FEFCE3";
						}

					## IF ON MGMT COUNCIL, SET BOLDING OPTION
					my $boldingtag = "";
					   $boldingtag = "<strong>" if $mgmtcouncil ne '';
					my $boldingtagclose = "";
					   $boldingtagclose = "</strong>" if $mgmtcouncil ne '';

					  $firstname = &cleanaccents2html ($firstname);
					  $lastname = &cleanaccents2html ($lastname);

					# WORKAROUND FOR SECC PHONE LISTINGS
					my $phonenumber = $phone;

					# PRINT AN HTML TABLE ROW FOR EACH STAFF MEMBER
print LISTBYDEPARTMENT <<EOM;
<tr> 
	<td valign="top" style="background-color: $bgcolor_alternating"><a href="/pubs/catalog/authors/$userid.html">$boldingtag$lastname, $firstname$boldingtagclose</a></td>
	<td valign="top" style="background-color: $bgcolor_alternating">$jobtitle</td>
	<td valign="top" style="background-color: $bgcolor_alternating"
EOM
	print LISTBYDEPARTMENT "class=\"noWrap\"" if ($phonenumber !~ 'and');
$phonenumber =~ s/and/and\<br\>/gi;
print LISTBYDEPARTMENT <<EOM;
>$phonenumber</td></tr>
EOM
if ($department_abbrev eq 'R&E') {
print RE_LISTBYDEPARTMENT "<li><a href=\"/pubs/catalog/authors/$userid.html\">$boldingtag$firstname $lastname$boldingtagclose</a>";
print RE_LISTBYDEPARTMENT ", $degree" if ($degree ne '');
print RE_LISTBYDEPARTMENT ", $jobtitle</li>\n";

}

				} ## END: LOOP THAT PRINTS STAFF LIST FOR INDIVIDUAL DEPARTMENT


				## DRAW SPACER BETWEEN DEPARTMENTS UNLESS ITS THE LAST ONE
				if ($loopingdepartments ne $numberofdepartments) {
print LISTBYDEPARTMENT <<EOM;
             <tr> 
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
EOM
				} # END DRAWING SPACER OPTION

			$loopingdepartments++;
		} # END LOOP THROUGH DEPARTMENTS

## PRINT FOOTER AND CLOSE/SAVE FILE
print LISTBYDEPARTMENT "</TABLE>\n<p></p>\n$footer";

close(LISTBYDEPARTMENT);
close(RE_LISTBYDEPARTMENT);

if ($debug eq '1') {
	print "<p>FINISHED WITH PAGE: <a href=\"/about/staff2.html\">Staff List by Program/Department</a></p>\n";
	print "<p>FINISHED WITH PAGE: <a href=\"/re/about_us.html\">RE List by Program/Department</a></p>\n";
}





###################################
## OPEN FILE FOR iCAL Birthdays  (FILE WILL BE POPULATED DURING THE STAFF LIST BY LAST NAME BELOW
###################################
my $ical_uid_counter = "9000";
open (BDAYS,">/home/httpd/html/calendars/SEDL-Birthdays.ics");

## START: PRINT SEDL-WIDE iCAL HEADER
print BDAYS<<EOM;
BEGIN:VCALENDAR
VERSION:2.0
X-WR-CALNAME:SEDL-Birthdays.ics
PRODID:-//Apple Computer\, Inc//iCal 1.5//EN
X-WR-RELCALID:1B2B34B5-6B78-90B1-B2B3-456789B01234-CALP
X-WR-TIMEZONE:US/Central
CALSCALE:GREGORIAN
EOM
###################################
## END: PRINT SEDL-WIDE iCAL HEADER
###################################


########################################################################################################################
# START: LIST SEDL STAFF BY LAST NAME
########################################################################################################################
## OPEN A FILE TO SAVE THE STAFF LIST BY LAST NAME
open(LISTBYLASTNAME,">/home/httpd/html/about/staff.html");

## PRINT PAGE HEADER
print LISTBYLASTNAME <<EOM;
$pre_title
SEDL Staff Directory by Last Name

$header
$bodystart
$pre_sidenav
    <p class="tocheader">Our Company</p>
    
   <div id="nav">
   <ul class="level1">
      	<li class="submenu"><a href="/about/mission.html" id="mission">Our Mission</a></li>
		<li class="submenu"><a href="/about/help.html">How We Can Help</a></li>
		<li class="submenu"><a href="/about/annualreport.html" id="report">Annual Report</a></li>
		<li class="submenu"><a href="/about/funding.html" id="funding">Funding</a></li>
		<li class="submenu"><a href="/40years/">40 Years of History</a></li>
		<li class="submenu"><a href="/about/art-building.html">Art in SEDL Headquarters</a></li>
        <li class="submenu"><a href="/about/green.html">Green Building</a></li>
		<li class="submenu"><a href="/cgi-bin/mysql/corp/contact.cgi" id="contact">Contact Us</a></li>
		<li class="submenu"><a href="/support/">Support SEDL</a></li>
	</ul>
  </div>
  <p class="tocheader2">Our People</p>
   <div id="nav2">

   <ul class="level1">
      	<li class="submenu"><a href="/about/board.html" id="Board">Board of Directors</a></li>
		<li class="submenu"><a href="/about/management.html" id="Management">SEDL Management Council</a></li>
		<li class="submenu active"><a href="/about/staff.html" id="Staff">Staff</a></li>
		<li class="submenu"><a href="/about/partners.html" id="Partners">Partners</a></li>
        <li class="submenu"><a href="/about/careers.html" id="Careers">Careers</a></li>
	</ul>

	</div>
$pre_centerpiece

<!-- This page was autogenerated by the staff_profiles database on $date_full_pretty_4digityear_withtime.  See the webmaster for more details. -->

<table border="0" cellpadding="0" cellspacing="2" width="100%">
<tr>
	<td><h1>SEDL Staff Directory </h1></td>
	<td valign="bottom"><a href="/about/staff2.html">By Program/Department</A></td>
	<TD><IMG SRC="/images/spacer.gif" WIDTH="3" HEIGHT="1" ALT=""></TD>
	<td valign="bottom">By Last Name of Staff</td>
</tr>
</TABLE>

<p></p>

<table border="0" cellpadding="3" cellspacing="2" width="100%" class="table-staff" summary="This table is a list of SEDL staff members">
<tr style="background-color: #C0D6DD">
	<th style="text-align:left;"><strong>Staff Member</strong></th>
	<th style="text-align:left;"><strong>Title</strong></th>
	<th style="text-align:left;"><strong>Phone</strong></th>
</tr>
EOM

## CONSTRUCT THE SQL QUERY TO FIND STAFF
my $command = "select * from staff_profiles 
				where firstname NOT LIKE '' 
				AND start_date <= '$date_full_mysql'
				order by lastname, firstname";

#if ($debug eq '1') {
#	print "<P>COMMAND: $command<P>";
#}
## OPEN THE DATABASE AND SEND THE QUERY
my $dsn = "DBI:mysql:database=intranet;host=localhost";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;
my $count_ical_birthdays = "0";
my $bgcolor_alternating = "#FEFCE3";
################################################################
# START: LOOP THAT PRINTS ONE LINE FOR EACH STAFF ENTRY
################################################################
while (my @arr = $sth->fetchrow) {
    $count++;
	my ($fm_record_id, $firstname, $middleinitial, $lastname, $jobtitle, $phone, $userid, $email, $phoneext, $birthmonth, $birthday, $timesheetname, $department_abbrev, $responsibilities, $experience, $mgmtcouncil, $lastupdated, $lastupdated_by, $room_number, $start_date, $adjusted_start_date, $stafflistsorting, $supervised_by, $automated_sentence, $photo_permissions, $show_birthday, $external_publications, $strong_pwd, $empl_type, $degree, $is_bgt_auth, $is_auth_rep, $areas_expertise_list, $other_SEDL_workgroup, $immediate_supervisor_sims_user_ID, $bgt_auth_primary_sims_user_ID) = @arr;
	my $department_fullname = &abbr_to_department_name($department_abbrev);
		if ($bgcolor_alternating eq "#FEFCE3") {
			$bgcolor_alternating = "#FDFEBE";
		} else {
			$bgcolor_alternating = "#FEFCE3";
		}
#################################################
if (($birthmonth ne '') && ($birthday ne '') && ($show_birthday ne 'no')) {
	$count_ical_birthdays++;
	my $birthday_day = $birthday;
		if (length($birthday_day) < 2) {
			$birthday_day = "0$birthday_day";
		}
	my $birthday_month = $birthmonth;
		$birthday_month = "01" if $birthday_month eq 'Jan';   $birthday_month = "02" if $birthday_month eq 'Feb';   $birthday_month = "03" if $birthday_month eq 'Mar';   $birthday_month = "04" if $birthday_month eq 'Apr';   $birthday_month = "05" if $birthday_month eq 'May';   $birthday_month = "06" if $birthday_month eq 'Jun';   $birthday_month = "07" if $birthday_month eq 'Jul';   $birthday_month = "08" if $birthday_month eq 'Aug';   $birthday_month = "09" if $birthday_month eq 'Sep';   $birthday_month = "10" if $birthday_month eq 'Oct';   $birthday_month = "11" if $birthday_month eq 'Nov';   $birthday_month = "12" if $birthday_month eq 'Dec';
	my $ical_birthday = "$year$birthday_month$birthday_day";	

	my $nextyear = $year;
	my $nextmonth = $birthday_month;
	my $nextday = $birthday_day + 1;
		if (length($nextday) < 2) {
			$nextday = "0$nextday";
		}

	my $ical_birthday_end = "$nextyear$nextmonth$nextday";	
	my $ical_staffname = $firstname;
#       $ical_staffname .= " $middleinitial\." if (length($middleinitial) == 1);
       $ical_staffname .= " $lastname";
	my $ical_department = $department_fullname;
	   $ical_department =~ s/\,/\\\,/g;
	   $ical_uid_counter++;
	## START: PRINT SEDL-WIDE iCAL ENTRY
print BDAYS<<EOM;
BEGIN:VEVENT
LOCATION:$ical_department
DTSTAMP:$datestamp
UID:9BB21BB8-85C8-11D9-$ical_uid_counter\-000A95B446C8-RID
DTSTART;VALUE=DATE:$ical_birthday
SUMMARY:$ical_staffname\'s BDay
DTEND;VALUE=DATE:$ical_birthday_end
RRULE:FREQ=YEARLY;INTERVAL=1
END:VEVENT
EOM
	## END: PRINT SEDL-WIDE iCAL ENTRY
}
#################################################

## IF ON MGMT COUNCIL, SET BOLDING OPTION
my $boldingtag = "";
   $boldingtag = "<strong>" if $mgmtcouncil ne '';
my $boldingtagclose = "";
   $boldingtagclose = "</strong>" if $mgmtcouncil ne '';

  $firstname = &cleanaccents2html ($firstname);
  $lastname = &cleanaccents2html ($lastname);
  $responsibilities = &cleanaccents2html ($responsibilities);
  $experience = &cleanaccents2html ($experience);

# WORKAROUND FOR SECAC PHONE LISTINGS
#my $phonenumber = $phoneext;
#   $phonenumber = "$phone" if $phoneext eq '';
#   $phonenumber = "512-476-6861 ext. $phonenumber" if (($phonenumber !~ '800') && ($phonenumber !~ '601-') && ($phonenumber !~ '770-'));
my $phonenumber = "$phone";
# PRINT AN HTML TABLE ROW FOR EACH STAFF MEMBER
print LISTBYLASTNAME <<EOM;
<tr> 
	<td valign="top" style="background-color: $bgcolor_alternating"><a href="/pubs/catalog/authors/$userid.html">$boldingtag $lastname, $firstname$boldingtagclose</a> </td>
	<td valign="top" style="background-color: $bgcolor_alternating">$jobtitle</td>
	<td valign="top" style="background-color: $bgcolor_alternating"
EOM
	print LISTBYLASTNAME "class=\"noWrap\"" if ($phonenumber !~ 'and');
$phonenumber =~ s/and/and\<br\>/gi;
print LISTBYLASTNAME <<EOM;
	>$phonenumber</td>
</tr>
EOM

} ## END: LOOP THAT PRINTS ONE LINE FOR EACH STAFF ENTRY



## PRINT FOOTER AND CLOSE/SAVE FILE
print LISTBYLASTNAME "</TABLE>\n<p></p>\n$footer";

close(LISTBYLASTNAME);
if ($debug eq '1') {
	print "<P>FINISHED WITH PAGE: <a href=\"/about/staff.html\">Staff List by Last Name</A><P>\n";
}
	## CLOSE FILE FOR iCAL Birthdays
	print BDAYS "END:VCALENDAR\n";
	close (BDAYS);
if ($debug eq '1') {
	print "<P>FINISHED WITH PAGE: <a href=\"/calendars/SEDL-Birthdays.ics\">Saved $count_ical_birthdays iCal Birthdays</A><P>\n";
}







########################################################################################################################
# START: LOOP FOR INDIVIDUAL STAFF PROFILE PAGES #
########################################################################################################################
my $command = "select * from staff_profiles 
			where firstname NOT LIKE '' 
			order by lastname, firstname";

## OPEN THE DATABASE AND SEND THE QUERY
my $dsn = "DBI:mysql:database=intranet;host=localhost";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

################################################################
# START: LOOP THAT PRINTS STAFF LIST FOR INDIVIDUAL DEPARTMENT #
################################################################
my $counter_staff = "0";
my $laststatus = "0";
	while (my @arr = $sth->fetchrow) {
    	$count++;
		my ($fm_record_id, $firstname, $middleinitial, $lastname, $jobtitle, $phone, $userid, $email, $phoneext, $birthmonth, $birthday, $timesheetname, $department_abbrev, $responsibilities, $experience, $mgmtcouncil, $lastupdated, $lastupdated_by, $room_number, $start_date, $adjusted_start_date, $stafflistsorting, $supervised_by, $automated_sentence, $photo_permissions, $show_birthday, $external_publications, $strong_pwd, $empl_type, $degree, $is_bgt_auth, $is_auth_rep, $areas_expertise_list, $other_SEDL_workgroup, $immediate_supervisor_sims_user_ID, $bgt_auth_primary_sims_user_ID) = @arr;

		my $department_fullname = &abbr_to_department_name($department_abbrev);
		$counter_staff++;

		$firstname = &cleanaccents2html ($firstname);
		$lastname = &cleanaccents2html ($lastname);
		$responsibilities = &cleanaccents2html ($responsibilities);
		$experience = &cleanaccents2html ($experience);

		my $article = &get_article($jobtitle, $userid);
		

		# START: GET DEPARTMENT NAME WITH EMBEDDED URL, IF ANY
		my $departmenturl = &depertment_getURL($department_fullname);
#		my $department2url = &depertment_getURL($department2);
		# END: GET DEPARTMENT NAME WITH EMBEDDED URL, IF ANY
	

		my $prefix = "";
		$phoneext = "ext. $phoneext" if $phoneext ne '';
   

		## OPEN A FILE TO SAVE THE STAFF MEMBER's UNIQUE PROFILE PAGE
		open(STAFFPROFILEPAGE,">/home/httpd/html/pubs/catalog/authors/$userid.html");


###############################################
## START: PRINT PAGE HEADER
###############################################
## START: GENERIC HEADER STUFF
my $active_staff = "active";
	if (length($middleinitial) == 1) {
		$middleinitial = "$middleinitial\.";
	}

print STAFFPROFILEPAGE<<EOM;
$pre_title
SEDL Employee: $firstname $middleinitial $lastname

$header
$bodystart
$pre_sidenav
 
 <script type="text/javascript" src="/common/javascript/wz_tooltip_sedl2012.js"></script>
 
  <p class="tocheader">Our Company</p>
    
   <div id="nav">
   <ul class="level1">
      	<li class="submenu"><a href="/about/mission.html" id="mission">Our Mission</a></li>
		<li class="submenu"><a href="/about/help.html">How We Can Help</a></li>
		<li class="submenu"><a href="/about/annualreport.html" id="report">Annual Report</a></li>
		<li class="submenu"><a href="/about/funding.html" id="funding">Funding</a></li>
		<li class="submenu"><a href="/40years/">40 Years of History</a></li>
		<li class="submenu"><a href="/about/art-building.html">Art in SEDL Headquarters</a></li>
        <li class="submenu"><a href="/about/green.html">Green Building</a></li>
		<li class="submenu"><a href="/cgi-bin/mysql/corp/contact.cgi" id="contact">Contact Us</a></li>
		<li class="submenu"><a href="/support/">Support SEDL</a></li>
	</ul>
  </div>
  <p class="tocheader2">Our People</p>
   <div id="nav2">
   <ul class="level1">
      	<li class="submenu"><a href="/about/board.html" id="Board">Board of Directors</a></li>
		<li class="submenu"><a href="/about/management.html" id="Management">SEDL Management Council</a></li>
		<li class="submenu $active_staff"><a href="/about/staff.html" id="Staff">Staff</a></li>
		<li class="submenu"><a href="/about/partners.html" id="Partners">Partners</a></li>
        <li class="submenu"><a href="/about/careers.html" id="Careers">Careers</a></li>
	</ul>

	</div>
$pre_centerpiece

<!-- This page was autogenerated by the staff_profiles database on $date_full_pretty_4digityear_withtime.  Contact webmaster\@sedl.org for more details. -->

<p>
EOM
## END: GENERIC HEADER STUFF


###############################################
## END: PRINT PAGE HEADER
###############################################



##############################################################
## START: HANDLE ALL OTHER PROFILE PAGES: SEDL STAFF
##############################################################
print STAFFPROFILEPAGE <<EOM;
<h1>$firstname $middleinitial $lastname<br>
$jobtitle</h1>
<p>
EOM


if ((lc($photo_permissions) eq 'on file') && ($staff_image_onfile{$userid} eq "yes")) {

	print STAFFPROFILEPAGE "<IMG SRC=\"/images/people/$userid.jpg\" ALT=\"Photo of $firstname $middleinitial $lastname\" class=\"fltrt oneBorder\" style=\"margin: 0px 0px 0px 5px\">";
}
	##########################################
	## START: SHOW AUTOMATED SENTENCE
	##########################################
	if ($automated_sentence ne 'hide') {
		if ($lastname ne 'Hoover') {
		print STAFFPROFILEPAGE "$firstname $lastname is ";
		}

		if ($article ne 'the') {
			print STAFFPROFILEPAGE "$article $jobtitle";
			if ($jobtitle ne 'Chief Program Officer') {
				if (($jobtitle ne 'Program Director') && ($jobtitle ne 'Program Manager')) {
					print STAFFPROFILEPAGE " with ";
				} else {
					print STAFFPROFILEPAGE " of ";
				}
				print STAFFPROFILEPAGE " SEDL\'s $departmenturl";
			} # END IF
				print STAFFPROFILEPAGE ". ";
		} # END IF

		if (($article eq 'the') && ($lastname ne 'Hoover')) {
			if ($jobtitle eq 'Executive Assistant') {
				print STAFFPROFILEPAGE "the $jobtitle to the President and CEO.";
			} else {
				if (($jobtitle =~ 'Research') && ($jobtitle =~ 'Evaluation')) {
					$jobtitle =~ s/Director of /Director of \<a href=\"\/re\/\"\>/gi;
					$jobtitle = "$jobtitle</a>";
				}
				print STAFFPROFILEPAGE "SEDL\'s $jobtitle. ";
			}
		}
	} # END IF
	##########################################
	## END: SHOW AUTOMATED SENTENCE
	##########################################

	#################################################################################
	## START: REMOVE LEADING <P> TAG FROM RESPONSIBILITIES, SINCE WE STARTED IT ABOVE
	#################################################################################
	$responsibilities = "QQQ$responsibilities";
	$responsibilities =~ s/QQQ\<p\>//gi;
	$responsibilities =~ s/QQQ//gi;
	#################################################################################
	## END: REMOVE LEADING P TAG FROM RESPONSIBILITIES, SINCE WE STARTED IT ABOVE.
	#################################################################################

print STAFFPROFILEPAGE " $responsibilities<p></p>";

print STAFFPROFILEPAGE "<p>\n<strong>Contact Information</strong><br>You may contact $firstname $middleinitial $lastname at $prefix $phone";
#print STAFFPROFILEPAGE ", $phoneext" if ($phoneext ne '');
print STAFFPROFILEPAGE "," if (($phone =~ 'fax') || ($phone =~ 'Text'));

my ($email_prefix, $trash) = split(/\@/,$email);
my $encrypted_userid = &do_encryption($email_prefix);

print STAFFPROFILEPAGE <<EOM;

<script type="text/javascript">
<!--
var name = "$encrypted_userid";
var domain = "&#115;&#101;&#100;&#108;&#46;&#111;&#114;&#103;";
document.write(' or at <a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;' + name + '&#x040;' + domain + '\">');
document.write(name + '&#64;' + domain + '</a>.');
// -->
</script>

<noscript> <p> or by using SEDL's <a href="http://www.sedl.org/cgi-bin/mysql/corp/contact.cgi?location=showform_staff">contact form</a>.</p>
</noscript>
<p></p>
EOM
$experience =~ s/\<p\>/\<p\>\<\/p\>\<p\>/gi;

print STAFFPROFILEPAGE "<p></p>$experience<p></p>\n" if ($experience ne '');
my $pd_sessions_taught = &get_pd_sessions_taught($userid, $firstname, $lastname);
print STAFFPROFILEPAGE "$pd_sessions_taught\n" if $pd_sessions_taught ne '';



	##############################################################
	# START: LOOP THAT PRINTS LINKS TO RELATED SEDL PUBLICATIONS #
	##############################################################
	## CHECK TO SEE IF THIS STAFF MEMBER IS A CATALOG AUTHOR
	my $command = "select isactive, onlineid, title, title2, price, datepub, lochtml, locpdf, locdatabase, description, metadesc, locart, print_on_demand_url, print_on_demand_price, producttype
					from sedlcatalog 
					where ((isactive LIKE '%y%') OR (isactive LIKE '%n-profilepub%'))
					AND producttype NOT LIKE 'Presentation'
					AND salesid NOT LIKE 'PLC-02'
					AND salesid NOT LIKE 'PLC-03'
					AND (profile1 LIKE '%$userid%' OR profile2 LIKE '%$userid%' OR profile3 LIKE '%$userid%' OR profile4 LIKE '%$userid%' OR profile5 LIKE '%$userid%' OR profile6 LIKE '%$userid%' OR profile7 LIKE '%$userid%' OR profile8 LIKE '%$userid%') 
					order by datepub DESC, title, title2";
 
	## OPEN THE DATABASE AND SEND THE QUERY
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	my $count = "1";
		while (my @arr = $sth->fetchrow) {

			my ($isactive, $onlineid, $title, $title2, $price, $datepub, $lochtml, $locpdf, $locdatabase, $description, $metadesc, $locart, $print_on_demand_url, $print_on_demand_price, $producttype) = @arr;
			$title = &cleanaccents2html ($title);
			$title2 = &cleanaccents2html ($title2);
			$description = $metadesc if ($metadesc ne '');

				##########################################
				## START: ASSEMPLE PRODUCT ROLLOVER INFO
				##########################################
				my $title_noquotes = $title;
					$title_noquotes .= ": $title2" if $title2 ne '';
#					$title_noquotes .= " ($datepub)" if $datepub ne '';
				   	$title_noquotes =~ s/\"//gi;
				   	$title_noquotes =~ s/\'/\\'/gi;
				my $image_float = "";
					if ($locart ne '') {
						$image_float = "<img src=\"$locart\" alt=\"Publication cover for $title_noquotes\" class=\"fltrt\">";
					}
				my $product_rollover_noquotes = "";
				   $product_rollover_noquotes = "$image_float<h3 style=\"margin-top:0px;\">$title_noquotes</h3> $description";
				   ## SHOW RESOURCE TYPE
				   		$product_rollover_noquotes .= "<p style=\"margin-top:10px;\">Resource type: $producttype</p>";
				   ## SHOW FREE FORMATS
				   if (($locpdf ne '') || ($lochtml ne '') || ($locdatabase ne '')) { 
				   		$product_rollover_noquotes .= "<p style=\"margin-top:10px;\">Available free online:";
				   		$product_rollover_noquotes .= ", HTML" if ($lochtml ne '');
				   		$product_rollover_noquotes .= ", PDF" if ($locpdf ne '');
				   		$product_rollover_noquotes .= ", Database" if ($locdatabase ne '');
				   		$product_rollover_noquotes .= "</p>";
				   		$product_rollover_noquotes =~ s/:,/:/gi;
				   } # END IF
				   ## SHOW PAY FORMATS
				   if ( (($price ne '') && (lc($price) ne 'n/a')) || (($print_on_demand_price ne '') && ($print_on_demand_url ne '')) ) { 
				   		$price = $print_on_demand_price if ($print_on_demand_price ne '');
				   		$product_rollover_noquotes .= "<p style=\"margin-top:10px;\">Purchase price: \$$price";
				   		$product_rollover_noquotes .= "</p>";
				   } # END IF
				   $product_rollover_noquotes =~ s/href/class=\"normallink\" href/gi;
				   $product_rollover_noquotes =~ s/\"//gi;
				   $product_rollover_noquotes =~ s/\'/\\'/gi;
				##########################################
				## END: ASSEMPLE PRODUCT ROLLOVER INFO
				##########################################

			print STAFFPROFILEPAGE "<a name=\"pubs\"></a>\n<p></p>\n<strong>SEDL Publications</strong>\n<ul style=\"list-style-image: url(http://www.sedl.org/images/bullets/light-blue.gif);margin-top:0px;\">\n" if $count eq '1';
			my $onlineid_label = $onlineid;
			   $onlineid_label =~ s/\.html//g;
			my $visual_clue_availablehow = "";
				if (($locpdf ne '') || ($lochtml ne '') || ($locdatabase ne '')) {
					$visual_clue_availablehow = "Available free online.";
					if (($price ne '') && ($price ne 'N/A')) {
						$visual_clue_availablehow = "Available free online. Print copies are also available for purchase from the SEDL Store.";
					}
				} elsif (($price ne '') && ($price ne 'N/A')) {
					$visual_clue_availablehow = "Available for purchase from the SEDL Store.";
				}
				if ($isactive =~ 'n-profilepub') {
					## HANDLE INACTIVE PUBLICATIONS
					print STAFFPROFILEPAGE "<li>$title"; # DON'T LINK IF IT'S AN OLDER PUB NO LONGER ONLINE
					print STAFFPROFILEPAGE ": $title2" if $title2 ne '';
					print STAFFPROFILEPAGE " ($datepub)" if $datepub ne '';
					print STAFFPROFILEPAGE "</li>\n";
				} else {
					## HANDLE ACTIVE PUBLICATIONS
					if (($title =~ 'SEDL Letter') || ($title =~ 'SEDLetter')) {
						# LOOK UP RECORD IN THE SEDL ARTICLES DATABASE
						
						my $command_get_SL_article = "SELECT sedlarticles.url_article, sedlarticles.title_article, sedlarticles_pubs.title_serialpub, sedlarticles_pubs.title_editionnumber, sedlarticles_pubs.date_published
						FROM sedlarticles, sedlarticles_pubs
						WHERE (sedlarticles.recordid_pub = sedlarticles_pubs.recordid_pub)
						AND (sedlarticles_pubs.recordid_sedlcatalog like '%$onlineid%')
						AND (sedlarticles.article_author LIKE '%$userid\.html%')
						";
						my $dsn = "DBI:mysql:database=publications;host=localhost";
						my $dbh = DBI->connect($dsn, "pubsuser", "sedlpubs");
						my $sth = $dbh->prepare($command_get_SL_article) or die "Couldn't prepare statement: " . $dbh->errstr;
						$sth->execute;
							while (my @arr = $sth->fetchrow) {
								my ($url_article, $title_article, $title_serialpub, $title_editionnumber, $date_published) = @arr;
								$datepub = substr($datepub,0,4);
								my ($p1, $p2, $p3) = split(/,/,$title_editionnumber);
								$title_editionnumber = "$p1, $p2";
								print STAFFPROFILEPAGE "<li><a href=\"$url_article\">$title_article</A>";
								print STAFFPROFILEPAGE ", $title_serialpub, $title_editionnumber";
								print STAFFPROFILEPAGE " ($datepub)" if $datepub ne '';
								print STAFFPROFILEPAGE "</li>\n";
							} # END DB QUERY LOOP

					} else {
						my $this_title_metadata = $title;
						   $this_title_metadata = "$title: $title2" if $title2 ne '';
						   $this_title_metadata =~ s/"/'/gi;
						print STAFFPROFILEPAGE "<li id=\"res-$count\"><span class=\"mainContent\" onmouseover=\"Tip('$product_rollover_noquotes'
												, WIDTH, 450, DELAY, 300, FIX, ['res-$count', 100, 4])\" onmouseout=\"UnTip()\"><a href=\"/pubs/catalog/items/$onlineid_label\.html\" title=\"$this_title_metadata\">$title</A>";
						print STAFFPROFILEPAGE ": $title2" if $title2 ne '';
						print STAFFPROFILEPAGE " ($datepub)" if $datepub ne '';
						print STAFFPROFILEPAGE "</span></li>\n";
					}
				}

			$count++;
		} ## END DB QUERY LOOP
	##############################################################
	# END: LOOP THAT PRINTS LINKS TO RELATED SEDL PUBLICATIONS #
	##############################################################
print STAFFPROFILEPAGE "</ul>\n" if $count ne "1";
##############################################################
## END: HANDLE ALL OTHER PROFILE PAGES: SEDL STAFF
##############################################################
$external_publications =~ s/\<br\>/\<br\>\n/gi;
$external_publications =~ s/\<br \/\>/\<br\>\n/gi;
$external_publications =~ s/\<ul/\<ul class\=\"hanging-indent\" style=\"margin-top:0px;list-style-type:none;\"/gi;



print STAFFPROFILEPAGE "<p></p>\n$external_publications\n<p></p>" if $external_publications ne '';




	## PRINT FOOTER AND CLOSE/SAVE FILE
	my $footer_with_space = $footer;
	   $footer_with_space =~ s/\<\/body\>/\<br\>\<br\>\<br\>\<br\>\<br\>\<br\>\<br\>\<br\>\<br\>\<br\>\<\/body\>/gi;
	print STAFFPROFILEPAGE "<br><br>$footer_with_space";
	close(STAFFPROFILEPAGE);
	if ($debug eq '1') {
		print "<BR>CREATED PROFILE PAGE FOR $counter_staff\: <a href=\"/pubs/catalog/authors/$userid.html\">$lastname, $firstname</A>";
		print " <span style=\"color:#339900;\">....(<a href=\"/staff/personnel/staffprofiles.cgi?id=$userid\"><span style=\"color:#339900;\">intranet</span></A>)</span>\n";
	}
} ## END: LOOP THAT PRINTS ONE LINE FOR EACH STAFF ENTRY



## READ DIRECTORY /HOME/HTTPD/HTML/PUBS/CATALOG/AUTHORS/ AND REVIEW ANY FILE NOT MODIFIED TODAY

##################################################################################
## START: OPEN DIRECTORY, READ FILE LIST, AND PUSH VALID IMAGES INTO SECOND ARRAY
##################################################################################
if ($debug eq '1') {
	print "<H3>Checking for orphaned author profile files...</H3>";
}

## THE HASH $staff_user_exists{$userid};

opendir(DIR, "/home/httpd/html/pubs/catalog/authors/");
my @files = readdir(DIR);
@files = sort(@files);
my $numerofarrayitems = @files;
my $nextslot = "0";
my $nextimagename = "";
my $counter = "0";
my $validuser_counter = "0";
my $orphans_found = "0";

while ($counter <= $numerofarrayitems) {


	if (($files[$counter] =~ '.html') && ($files[$counter] ne 'welcome.html')) {
		my ($files_user_id, $trash) = split(/\./,$files[$counter]);
			if ($staff_user_exists{$files_user_id} ne "yes") {
				# DELETE USER
				system "rm /home/httpd/html/pubs/catalog/authors/$files[$counter]";
				$orphans_found++;
			} # END IF
	} # END IF
	$counter++;
} # END WHILE
	if ($debug eq '1') {
		print "<P>FINISHED CHECKING FOR ORPHANED FILES - FOUND $orphans_found ORPHANS";
	}
##################################################################################
## END: OPEN DIRECTORY, READ FILE LIST, AND PUSH VALID IMAGES INTO SECOND ARRAY
##################################################################################




} ## END IF ($staffintranetonly eq '')










###########################################################################################################
###########################################################################################################
###########################################################################################################
###########################################################################################################
###########################################################################################################
###########################################################################################################
###########################################################################################################
###########################################################################################################
###########################################################################################################
###########################################################################################################
###########################################################################################################
###########################################################################################################
###########################################################################################################
###########################################################################################################





#####################################################################################
# START: PRINT STAFF PROFILE FOR SEDL INTRANET WITH ALTERNATE PICTURE, IF AVAILABLE #
#####################################################################################
if ($intranetonly ne '') {

print header;


## START: SINGLE USER DYNAMIC PAGE
if ($id ne 'all') {


my $command = "select * from staff_profiles where userid = '$id' order by lastname, firstname";

## OPEN THE DATABASE AND SEND THE QUERY
my $dsn = "DBI:mysql:database=intranet;host=localhost";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;


################################################################
# START: LOOP  #
################################################################
while (my @arr = $sth->fetchrow) {
    $count++;
	my ($fm_record_id, $firstname, $middleinitial, $lastname, $jobtitle, $phone, $userid, $email, $phoneext, $birthmonth, $birthday, $timesheetname, $department_abbrev, $responsibilities, $experience, $mgmtcouncil, $lastupdated, $lastupdated_by, $room_number, $start_date, $adjusted_start_date, $stafflistsorting, $supervised_by, $automated_sentence, $photo_permissions, $show_birthday, $external_publications, $strong_pwd, $empl_type, $degree, $is_bgt_auth, $is_auth_rep, $areas_expertise_list, $other_SEDL_workgroup, $immediate_supervisor_sims_user_ID, $bgt_auth_primary_sims_user_ID) = @arr;
	my $department_fullname = &abbr_to_department_name($department_abbrev);
		if (length($middleinitial) == 1) {
			$middleinitial = "$middleinitial\.";
		}

	my $start_date_pretty = &date2standard($start_date);
		$start_date = $adjusted_start_date if (length($adjusted_start_date) > 4);
  $firstname = &cleanaccents2html ($firstname);
  $lastname = &cleanaccents2html ($lastname);
  $responsibilities = &cleanaccents2html ($responsibilities);
  $experience = &cleanaccents2html ($experience);


	my $article = &get_article($jobtitle, $userid);


	# START: GET DEPARTMENT NAME WITH EMBEDDED URL, IF ANY
	my $departmenturl = &depertment_getURL($department_fullname);
#	my $department2url = &depertment_getURL($department2);
	# END: GET DEPARTMENT NAME WITH EMBEDDED URL, IF ANY

my $prefix = "";

$phoneext = "ext. $phoneext" if $phoneext ne '';
   
## ADJUST START DATE FOR EMPLOYEES WHO HAD PREVIOUSLY WORKED AT SEDL
#$start_date = "2004-08-15" if ($userid eq 'jburnisk');
#$start_date = "1998-04-30" if ($userid eq 'mboethel');
#$start_date = "1998-08-31" if ($userid eq 'gcopelan');
#$start_date = "1989-06-30" if ($userid eq 'mtorres');
#$start_date = "1969-09-15" if ($userid eq 'sliberty');

my $ribbon = "";
   $ribbon = "<IMG SRC=\"/staff/images/ribbon_5.gif\" ALT=\"5 Years Award\" TITLE=\"5 Years Award\" ALIGN=RIGHT>" 	 if (($start_date lt $yearsago_5) || ($start_date eq $yearsago_5));
   $ribbon = "<IMG SRC=\"/staff/images/ribbon_10.gif\" ALT=\"10 Years Award\" TITLE=\"10 Years Award\" ALIGN=\"RIGHT\">" if (($start_date lt $yearsago_10) || ($start_date eq $yearsago_10));
   $ribbon = "<IMG SRC=\"/staff/images/ribbon_15.gif\" ALT=\"15 Years Award\" TITLE=\"15 Years Award\" ALIGN=\"RIGHT\">" if (($start_date lt $yearsago_15) || ($start_date eq $yearsago_15));
   $ribbon = "<IMG SRC=\"/staff/images/ribbon_20.gif\" ALT=\"20 Years Award\" TITLE=\"20 Years Award\" ALIGN=\"RIGHT\">" if (($start_date lt $yearsago_20) || ($start_date eq $yearsago_20));
   $ribbon = "<IMG SRC=\"/staff/images/ribbon_25.gif\" ALT=\"25 Years Award\" TITLE=\"25 Years Award\" ALIGN=\"RIGHT\">" if (($start_date lt $yearsago_25) || ($start_date eq $yearsago_25));
   $ribbon = "<IMG SRC=\"/staff/images/ribbon_30.gif\" ALT=\"30 Years Award\" TITLE=\"30 Years Award\" ALIGN=\"RIGHT\">" if (($start_date lt $yearsago_30) || ($start_date eq $yearsago_30));
   $ribbon = "<IMG SRC=\"/staff/images/ribbon_35.gif\" ALT=\"35 Years Award\" TITLE=\"35 Years Award\" ALIGN=\"RIGHT\">" if (($start_date lt $yearsago_35) || ($start_date eq $yearsago_35));
   $ribbon = "<IMG SRC=\"/staff/images/ribbon_40.gif\" ALT=\"40 Years Award\" TITLE=\"40 Years Award\" ALIGN=\"RIGHT\">" if (($start_date lt $yearsago_40) || ($start_date eq $yearsago_40));
   $ribbon = "missing start date" if ($start_date eq '');


$htmlhead =~ s/\<body\>/\<body\>\<script type=\"text\/javascript\" src=\"\/common\/javascript\/wz_tooltip.js\"\>\<\/script\>/gi;

## PRINT PAGE HEADER
print <<EOM;
<html>
<head>
<title>SEDL Employee: $firstname $middleinitial $lastname</title>
$htmlhead
<div class="fltrt" style="width:180px;text-align:center;">

EOM
if ((lc($photo_permissions) eq 'on file') && ($staff_image_onfile{$userid} eq "yes")) {
	print "<IMG SRC=\"/images/people/$userid.jpg\" ALT=\"Photo of $firstname $middleinitial $lastname\" class=\"oneBorder\" style=\"margin: 0px\"><br>";
}
#print "<h2>$firstname $middleinitial $lastname </h2>";
#$email = "long.emailname\@sedl.org";
my $email_label = $email;
	if (length($email) > 25) {
		$email_label =~ s/\@/\<br\>\@/gi;
	}
print<<EOM;
	<div class="dottedBoxyw" style="margin-top:2px;text-align:left;">
	<strong>Contact Information</strong><BR>
	<a href="mailto:$email">$email_label</A><br>
	Phone: $phone
EOM
#	if ($manager_iphone_numbers{$userid} ne '') {
#print<<EOM;
#<br>iPhone: $manager_iphone_numbers{$userid}
#EOM
#	}
	if ($room_number ne '') {
		my $floor = "1";
			$floor = "2" if (substr($room_number,0,1) eq '2');
			$floor = "3" if (substr($room_number,0,1) eq '3');
print<<EOM;
<br>Room #: $room_number <A HREF="http://www.sedl.org/staff/planning/hq_floorplans/thumbnails/floor$floor\.gif"  onmouseover="Tip('<img src=\\'/staff/planning/hq_floorplans/thumbnails/floor$floor\.gif\\'>', TITLE, 'Floor $floor', TITLEFONTSIZE, '16px', TITLEALIGN, 'center', WIDTH, 600, FIX, [150, 250], DELAY, 0)" onmouseout="UnTip()">floor map</A>
EOM
	}
print<<EOM;
<br><br>
$ribbon
<strong>Start Date</strong><br>
$firstname 
EOM
if ($adjusted_start_date eq '') {
	print"joined SEDL on $start_date_pretty.";
} else {
	my $adjusted_start_date_label = &date2standard($adjusted_start_date, '');
	print"rejoined SEDL on $start_date_pretty. For purposes of computing years of service, $firstname\'s SEDL anniversary date is $adjusted_start_date_label\.";
}
print<<EOM;
EOM
	if (($birthmonth ne '') && ($birthday ne '') && ($show_birthday ne 'no')) {
	$birthmonth = &get_fullmonthname($birthmonth);
print <<EOM;
<br><br>
<strong>Birthday:</strong> $birthmonth $birthday
EOM
	}
print<<EOM;
	</div>
</div>
<h1 style="margin-top:0px;padding-top:0px;">$firstname $middleinitial $lastname<br>\n$jobtitle</h1>
<p>
EOM
	##########################################
	## START: SHOW AUTOMATED SENTENCE
	##########################################
	if ($automated_sentence ne 'hide') {

		print "$firstname $lastname is " if $lastname ne 'Hoover';

		if ($article ne 'the') {
			print "$article $jobtitle";
			if ($jobtitle ne 'Chief Program Officer') {
				if (($jobtitle ne 'Program Director') && ($jobtitle ne 'Program Manager')) {
					print " with ";
				} else {
					print " of ";
				}
				print " SEDL\'s $departmenturl";
			} # END IF
				print ". ";
		} # END IF

		if (($article eq 'the') && ($lastname ne 'Hoover')) {
			if ($jobtitle eq 'Executive Assistant') {
				print "the $jobtitle to the President and CEO.";
			} else {
				if (($jobtitle =~ 'Research') && ($jobtitle =~ 'Evaluation')) {
					$jobtitle =~ s/Director of /Director of \<a href=\"\/re\/\"\>/gi;
					$jobtitle = "$jobtitle</a>";
				}
				print "SEDL\'s $jobtitle. ";
			}
		}

		print "SEDL\'s $jobtitle. " if (($article eq 'the') && ($lastname ne 'Hoover'));
	} # END IF
	##########################################
	## END: SHOW AUTOMATED SENTENCE
	##########################################

print " $responsibilities</p>";

if ($experience ne '') {
	$experience =~ s/light-blue/red-intranet/gi;
	print "$experience";
}

#print <<EOM;
#<p><strong>Start Date</strong><BR>
#$ribbon $firstname joined SEDL on $start_date_pretty.
#EOM
#print "<BR><BR><BR>" if ($ribbon ne '');
#print<<EOM;
#<p><strong>Contact Information</strong><BR>
#You may contact $firstname $middleinitial $lastname at $prefix $phone or at <a href="mailto:$email">$email</A>. </p>
#EOM
#if ($birthmonth ne '') {
#print <<EOM;
#<P class="dottedBox" style="width:300px;">
#<strong><em>$firstname\'s birthday is $birthmonth $birthday</em></strong><BR>
#Want to send a birthday greeting?<BR>
#Try: <a href="http://www.hallmark.com">Hallmark</A>, <a href="http://www.evite.com">Evite.com</A>, or <a href="http://www.bluemountain.com/">Blue Mountain</A>.
#</p>
#<p>
#EOM
#}
print "<p></p>$external_publications" if $external_publications ne '';

## CHECK TO SEE IF THIS STAFF MEMBER IS A CATALOG AUTHOR
my $command = "select isactive, onlineid, title, title2, datepub
				FROM sedlcatalog where ((isactive LIKE '%y%') OR (isactive LIKE '%n-profilepub%'))
				AND producttype NOT LIKE 'Presentation'
				AND salesid NOT LIKE 'PLC-02'
				AND salesid NOT LIKE 'PLC-03'
				AND (profile1 LIKE '%$userid%' OR profile2 LIKE '%$userid%' OR profile3 LIKE '%$userid%' OR profile4 LIKE '%$userid%' 
					OR profile5 LIKE '%$userid%' OR profile6 LIKE '%$userid%' OR profile7 LIKE '%$userid%' OR profile8 LIKE '%$userid%') 
				order by datepub DESC, title, title2";
 
## OPEN THE DATABASE AND SEND THE QUERY
my $dsn = "DBI:mysql:database=corp;host=localhost";
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;
my $count = "1";

##############################################################
# START: LOOP THAT PRINTS LINKS TO RELATED SEDL PUBLICATIONS #
##############################################################
while (my @arr = $sth->fetchrow) {

	my ($isactive, $onlineid, $title, $title2, $datepub) = @arr;

		$title = &cleanaccents2html ($title);
		$title2 = &cleanaccents2html ($title2);
		$title = &cleanthis ($title);
		$title2 = &cleanthis ($title2);
		$responsibilities = &cleanthis ($responsibilities);
		$experience = &cleanthis ($experience);

		if ($count eq '1') {
			print "<a name=\"pubs\"></a><p><strong>SEDL Publications</strong></p><ol class=\"sp3\">";
		}
		my $onlineid_label = $onlineid;
			$onlineid_label =~ s/\.html//g;
		if ($isactive =~ 'n-profilepub') {
			print "<li>$title";
		} else {
			print "<li><a href=\"/pubs/catalog/items/$onlineid_label\.html\">$title</A>";
		}
		print ": $title2" if $title2 ne '';
		print " ($datepub)" if $datepub ne '';
		print "</li>";

$count++;
} ## END: LOOP THAT PRINTS LINKS TO RELATED SEDL PUBLICATIONS
print "</ol>" if $count ne "1";

## PRINT FOOTER
print<<EOM
<div style="clear:both;"></div>

$htmltail
EOM

}
} ## END SINGLE USER DYNAMIC PAGE

#####################################################################################
# END: PRINT STAFF PROFILE FOR SEDL INTRANET WITH ALTERNATE PICTURE, IF AVAILABLE #
#####################################################################################







#####################################################################################
# START: PRINT LIST OF STAFF FOR INTRANET #
#####################################################################################
if (($id eq 'all') && ($list_view eq 'birthday')) {
## PRINT PAGE HEADER
print <<EOM;
<html>
<head>
<title>SEDL Staff List (Birthdays)</title>
$htmlhead

<div style="padding:15px;">

<div class="fltrt" style="width:100px;">
<strong>Online Version</strong><br>
<a href="/staff/personnel/staffprofiles-print.cgi?id=all&amp;list_view=birthday">Print Version</a>
</div>

<h1 style="margin-top:0px;padding-top:0px">SEDL Staff Listed by:</H1>
<a href="staffprofiles.cgi?list_view=firstname&id=all">First Name</A>, 
<a href="staffprofiles.cgi?list_view=lastname&id=all">Last Name</A>, 
<a href=\"staffprofiles.cgi?list_view=deptname&id=all\">Program/Department</A>, or Birthday
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
<TABLE CELLPADDING="5" border="0" WIDTH="100%">
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

		my $department_fullname = &abbr_to_department_name($department_abbrev);
		print "</TD>\n\n<TD valign=\"top\">" if (($monthdone eq 'no') && (($birthmonth eq 'May') || ($birthmonth eq 'Sep')));
		print "<P><TABLE BORDER=\"0\" WIDTH=\"200\"><TR><TD COLSPAN=2><P><strong>$monthlonglist[$loopingmonths]</strong></TD></TR>" if ($monthdone eq 'no');
		$monthdone = "yes";
		## CLEAN UP ACCENTS
		$firstname = &cleanaccents2html ($firstname);
		$lastname = &cleanaccents2html ($lastname);


		$responsibilities =~ s/<.+?>/ /g;
		$responsibilities = "$firstname is a $jobtitle for SEDL's $department_fullname.";
		print "<TR><TD valign=\"top\" ALIGN=RIGHT>$birthday</TD><TD>";
		if (lc($photo_permissions) eq 'on file') {
			print "<a href=\"javascript\:Start(\'/images/people/$userid.jpg\', 10, 10, 300, 300)\"\;><IMG SRC=\"/staff/img/camera.gif\" ALT=\"photo available\" border=0 align=right></A>";    
		}
	print "<a href=\"/staff/personnel/staffprofiles.cgi?id=$userid\" TITLE=\"$responsibilities\">$firstname $lastname</A></TD>";
	print "</TR>";
	}  ## END LOOP FOR THIS MONTH

	$loopingmonths++;
	print "</TABLE>\n\n";
}  ## END WHILE LOOP THAT GOES THROUGH EACH MONTH

## PRINT FOOTER
print<<EOM;
</td></tr>
</table>
EOM
if ($list_missing_bdays ne '') {
print<<EOM;
<p>
These staff birthdays are not recorded in the database, but they also have not asked to block their birthday.  Need to contact staff to have their birthday added.
</p>
	<ul>
	$list_missing_bdays
	</ul>
EOM
}
print<<EOM;
</div>
$htmltail
EOM

} ## END IF id = ALL, list_view = 'birthday



if (($id eq 'all') && ($list_view =~ 'name')) {
	my $list_view_label = "First Name";
	   $list_view_label = "Last Name" if ($list_view eq 'lastname');

## PRINT PAGE HEADER
print <<EOM;
<html>
<head>
<title>SEDL Staff List (by Name)</title>
$htmlhead
<script type="text/javascript" src="/common/javascript/wz_tooltip.js"></script>
EOM

my $heading_start_date = "<strong>Start Date</strong>";
my $heading_firstname = "<strong>First</strong>";
my $heading_lastname = "<strong>Last</strong>";
my $heading_department = "<strong>Program/Department</strong>";

$heading_firstname = "<a href=\"staffprofiles.cgi?list_view=firstname&id=all\">First</A>" if ($list_view ne 'firstname');
$heading_lastname = "<a href=\"staffprofiles.cgi?list_view=lastname&id=all\">Last</A>" if ($list_view ne 'lastname');
$heading_department = "<a href=\"staffprofiles.cgi?list_view=deptname&id=all\">Program/Department</A>" if ($list_view ne 'deptname');
$heading_start_date = "<a href=\"staffprofiles.cgi?list_view=startdatename&id=all\">Start Date</A>" if ($list_view ne 'startdatename');

my $this_sortby = "lastname, firstname";
   $this_sortby = "firstname, lastname" if ($list_view eq 'firstname');
   $this_sortby = "start_date DESC" if ($list_view eq 'startdatename');
   $this_sortby = "department_abbrev, stafflistsorting DESC, firstname, lastname" if ($list_view eq 'deptname');
my $this_bgcolor = "EBEBEB";
my $last_bgcolor = "";
my $last_department = "";
my $this_dept_bgcolor = "EBEBEB";
	my $command = "select * from staff_profiles order by $this_sortby";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;

############################################
## START: FIND OUT WHICH VITAs ARE ONLINE
############################################
my %vita_found; # HASH TO STORE STATUS OF ONLINE DOCUMENT
my %vita_last_updated; # HASH TO STORE STATUS OF ONLINE DOCUMENT

	opendir(DIR, "/home/httpd/html/staff/planning/vita_docs/");
	my @files = readdir(DIR);
	my $numerofarrayitems = @files;
	my $counter = "0";

	while ($counter <= $numerofarrayitems) {
		if ($files[$counter] =~ '.doc') {
			my $this_name = $files[$counter];
			   $this_name =~ s/\.doc//gi;
			$vita_found{$this_name} = "yes";

			# START: GET FILE MODIFICATION TIME
			my $this_file = "/home/httpd/html/staff/planning/vita_docs/$files[$counter]";
			my @temp = stat $this_file;
			my $last_updated = $temp[9];
			   $last_updated = localtime($last_updated);
			# END: GET FILE MODIFICATION TIME

			$vita_last_updated{$this_name} = $last_updated;
			my $this_length = length($vita_last_updated{$this_name});
			   $this_length = $this_length - 18;
			   $vita_last_updated{$this_name} = substr($vita_last_updated{$this_name},4,$this_length);
		} # END IF
		$counter++;
	} # END WHILE
############################################
## END: FIND OUT WHICH VITAs ARE ONLINE
############################################



print<<EOM;
<div class="fltrt" style="width:100px;">
<strong>Online Version</strong><br>
<a href="staffprofiles-print.cgi">Print Version</a>
</div>
<h1 style="margin-top:0px;padding-top:0px">SEDL Staff Directory ($num_matches members)</H1>

<p>
You may view the staff list sorted by <a href=\"staffprofiles.cgi?id=all&amp;list_view=birthday\">Birthday</a> 
or by any of the column headings below.<br>
Call 512-552-5522 for security after normal SEDL hours.
<P>
<TABLE BORDER="0" CELLPADDING="4" CELLSPACING="0">
EOM
if ($list_view ne 'deptname') {
	print "<TR><TD>Name<BR>($heading_firstname, $heading_lastname)</TD><TD ALIGN=CENTER>Click for<br>larger photo</TD><TD>Ext.</TD><TD align=\"center\">Room #</TD><TD>Title</TD><TD>$heading_department</TD><TD>$heading_start_date</TD></TR>";
}


	while (my @arr = $sth->fetchrow) {
	    $count++;
		my ($fm_record_id, $firstname, $middleinitial, $lastname, $jobtitle, $phone, $userid, $email, $phoneext, $birthmonth, $birthday, $timesheetname, $department_abbrev, $responsibilities, $experience, $mgmtcouncil, $lastupdated, $lastupdated_by, $room_number, $start_date, $adjusted_start_date, $stafflistsorting, $supervised_by, $automated_sentence, $photo_permissions, $show_birthday, $external_publications, $strong_pwd, $empl_type, $degree, $is_bgt_auth, $is_auth_rep, $areas_expertise_list, $other_SEDL_workgroup, $immediate_supervisor_sims_user_ID, $bgt_auth_primary_sims_user_ID) = @arr;
		my $department_fullname = &abbr_to_department_name($department_abbrev);
		my $start_date_pretty = &date2standard($start_date);
		if (length($adjusted_start_date) > 4) {
			$start_date = $adjusted_start_date;
			my $start_date_pretty_label = &date2standard($adjusted_start_date); #, "noyear"
			$start_date_pretty = "<span title=\"Rejoined SEDL after leaving. SEDL anniversary date is $start_date_pretty_label for purposes of computing years of service.\">$start_date_pretty*</span>";
		}

	my $floor = "1";
	   $floor = "2" if (substr($room_number,0,1) eq '2');
	   $floor = "3" if (substr($room_number,0,1) eq '3');

		$experience = &cleanaccents2html ($experience);

## ADJUST START DATE FOR EMPLOYEES WHO HAD PREVIOUSLY WORKED AT SEDL
#$start_date = "2004-08-15" if ($userid eq 'jburnisk');
#$start_date = "1998-04-30" if ($userid eq 'mboethel');
#$start_date = "1998-08-31" if ($userid eq 'gcopelan');
#$start_date = "1988-06-30" if ($userid eq 'mtorres');
#$start_date = "1969-09-15" if ($userid eq 'sliberty');

		my $ribbon = "";
		   $ribbon = "<IMG SRC=\"/staff/images/ribbon_5.gif\" ALT=\"5 Years Award\" TITLE=\"5 Years Award\">" if (($start_date lt $yearsago_5) || ($start_date eq $yearsago_5));	  	 
		   $ribbon = "<IMG SRC=\"/staff/images/ribbon_10.gif\" ALT=\"10 Years Award\" TITLE=\"10 Years Award\">" if (($start_date lt $yearsago_10) || ($start_date eq $yearsago_10));
		   $ribbon = "<IMG SRC=\"/staff/images/ribbon_15.gif\" ALT=\"15 Years Award\" TITLE=\"15 Years Award\">" if (($start_date lt $yearsago_15) || ($start_date eq $yearsago_15));
		   $ribbon = "<IMG SRC=\"/staff/images/ribbon_20.gif\" ALT=\"20 Years Award\" TITLE=\"20 Years Award\">" if (($start_date lt $yearsago_20) || ($start_date eq $yearsago_20));
		   $ribbon = "<IMG SRC=\"/staff/images/ribbon_25.gif\" ALT=\"25 Years Award\" TITLE=\"25 Years Award\">" if (($start_date lt $yearsago_25) || ($start_date eq $yearsago_25));
		   $ribbon = "<IMG SRC=\"/staff/images/ribbon_30.gif\" ALT=\"30 Years Award\" TITLE=\"30 Years Award\">" if (($start_date lt $yearsago_30) || ($start_date eq $yearsago_30));
   		   $ribbon = "<IMG SRC=\"/staff/images/ribbon_35.gif\" ALT=\"35 Years Award\" TITLE=\"35 Years Award\">" if (($start_date lt $yearsago_35) || ($start_date eq $yearsago_35));
   		   $ribbon = "<IMG SRC=\"/staff/images/ribbon_40.gif\" ALT=\"40 Years Award\" TITLE=\"40 Years Award\">" if (($start_date lt $yearsago_40) || ($start_date eq $yearsago_40));
		   $ribbon = "missing start date" if ($start_date eq '');
		$firstname = &cleanaccents2html ($firstname);
		$lastname = &cleanaccents2html ($lastname);
#		$department .= "<BR>$department2" if (($department2 ne '') && ($list_view ne 'deptname')); 
		$this_bgcolor = "EBEBEB" if ($last_bgcolor eq 'FFFFFF');
		$this_bgcolor = "FFFFFF" if ($last_bgcolor eq 'EBEBEB');

		$this_dept_bgcolor = "EBEBEB" if (($last_bgcolor eq 'FFFFFF') && ($department_fullname ne $last_department));
		$this_dept_bgcolor = "FFFFFF" if (($last_bgcolor eq 'EBEBEB') && ($department_fullname ne $last_department));

		$this_bgcolor = $this_dept_bgcolor if ($list_view eq 'deptname');
		if (($list_view eq 'deptname') && ($last_department ne $department_fullname)) {
			print "<TR><TD COLSPAN=5><BR><H3>$department_fullname</H3></TD></TR> <TR><TD>Name<BR>($heading_firstname, $heading_lastname)</TD><TD ALIGN=CENTER>Click for<BR>larger photo</TD><TD>Ext.</TD><TD align=\"center\">Room #</TD><TD>Title</TD><TD>$heading_department</TD><TD>$heading_start_date</TD></TR>";
		}
			my $photograph = "<img src=/images/people/$userid.jpg class=fltrt>";
			$photograph = "" if (lc($photo_permissions) ne 'on file');
			 if ($responsibilities ne '') {
			 	$responsibilities = "$photograph<strong><span style=font-size:14px;>$firstname $lastname<br>$jobtitle</span></strong><br><br><strong>Current Responsibilities:</strong><br>$responsibilities<br><br>";
			 	} else {
			 	$responsibilities = "$photograph<strong><span style=font-size:14px;>$firstname $lastname<br>$jobtitle</span></strong><br><br>";
			 	}
#			$responsibilities .= "<br><br>" if (($responsibilities =~ 'Current Responsibilities') && ($experience ne ''));
			$responsibilities .= "<strong>Education/Experience:</strong><br>$experience" if ($experience ne '');
			$responsibilities =~ s/'/\\'/gi;
			$responsibilities =~ s/"/&quot;/gi;
#			$responsibilities =~ s/<.+?>/ /g; # strip out html
			$responsibilities =~ tr/-.A-Za-z0-9_+%&,\\'\=\/;:@\'\<\> //cd;

#			while (length($responsibilities) > 250) {
#				chop($responsibilities);
#			}
		if (($list_view eq 'firstname') || ($list_view eq 'deptname')) {
			print "<TR style=\"background-color: $this_bgcolor\"><TD valign=\"top\" NOWRAP VALIGN=\"TOP\"><a href=\"/staff/personnel/staffprofiles.cgi?id=$userid\" onmouseover=\"Tip('$responsibilities', WIDTH, 600)\" onmouseout=\"UnTip()\">$firstname $lastname</A></TD><TD valign=\"top\" ALIGN=\"CENTER\">";
		} else {
			print "<TR style=\"background-color: $this_bgcolor\"><TD valign=\"top\" NOWRAP VALIGN=\"TOP\"><a href=\"/staff/personnel/staffprofiles.cgi?id=$userid\" onmouseover=\"Tip('$responsibilities', WIDTH, 600)\" onmouseout=\"UnTip()\">$lastname, $firstname</A></TD><TD valign=\"top\" ALIGN=\"CENTER\">";
		}
	if ((lc($photo_permissions) eq 'on file') && ($staff_image_onfile{$userid} eq "yes")) {
		print "<P><a href=\"javascript\:Start(\'/images/people/$userid.jpg\', 10, 10, 300, 300)\"\;><IMG SRC=\"/images/people/$userid.jpg\" HEIGHT=\"60\" ALT=\"Photo of $firstname $lastname\" class=\"oneBorder\"></A>";
	} elsif ((lc($photo_permissions) eq 'on file') && ($staff_image_onfile{$userid} ne "yes")) {
		print "<p>Authorization<br>on file - NEED PHOTO TAKEN.</p>";
	} elsif (lc($photo_permissions) eq 'declined') {
		print "<p style=\"color:#999999\">Staff declined<br>photo authorization.</p>";
	} elsif (lc($photo_permissions) =~ 'on file') {
		print "<p style=\"color:#999999\">Authorization<br>on file. Declined profile photo.</p>";
	} elsif ((lc($photo_permissions) eq 'needed') || ($photo_permissions eq '')) {
		print "<p>Photo authorization unclear<br>NEED TO ASK STAFF MEMBER.</p>";
	}
	
print<<EOM;
	</TD>
	<TD VALIGN=\"TOP\">$phoneext</TD>
	<td VALIGN=\"TOP\" align=\"center\">
EOM
	if ($room_number ne '') {
print<<EOM;
<A HREF="http://www.sedl.org/staff/planning/hq_floorplans/thumbnails/floor$floor\.gif"  onmouseover="Tip('<img src=\\'/staff/planning/hq_floorplans/thumbnails/floor$floor\.gif\\'>', TITLE, 'Floor $floor', TITLEFONTSIZE, '16px', TITLEALIGN, 'center', WIDTH, 600)" onmouseout="UnTip()">$room_number</a>
EOM
	}
print<<EOM;
	</td>
	<TD valign=\"top\">$jobtitle
EOM
print "<br><span style=\"color:#666666\">Vita updated: $vita_last_updated{$userid}</span>" if ($vita_last_updated{$userid} ne '');
print<<EOM;
	</TD>
	<TD VALIGN=\"TOP\">$department_fullname</TD>
	<TD VALIGN=\"TOP\" ALIGN=CENTER>$start_date_pretty $ribbon</TD>
</TR>
EOM
	$last_bgcolor = $this_bgcolor;
	$last_bgcolor = $this_dept_bgcolor if ($list_view eq 'deptname');
	$last_department = $department_fullname;
	}  ## END DB QUERY LOOP


## PRINT FOOTER
print<<EOM;
</TABLE>

<p>
</p>
<H2>SEDL's Presidents and CEOs (1966 - Current)</H2>
<TABLE>
<TR><TD ALIGN=CENTER valign="top"><IMG SRC="/staff/images/staffphotos/ceo/ehindsman.jpg"><BR>
		Edwin Hindsman<BR>(1966 - 1970)</TD>
	<TD ALIGN=CENTER valign="top"><IMG SRC="/staff/images/staffphotos/ceo/jperry.jpg"><BR>
		James Perry<BR>(1971 - 1981)</TD>
	<TD ALIGN=CENTER valign="top"><IMG SRC="/staff/images/staffphotos/ceo/pkronkosky.gif"><BR>
		<a href="http://www.sedl.org/pubs/sedletter/v09n04/wemust.html">Preston Kronkosky</A><BR>(1981 - 1996)</TD>
	<TD ALIGN=CENTER valign="top"><IMG SRC="/staff/images/staffphotos/ceo/wes-intranet1.jpg"><BR>
		<a href="/staff/personnel/staffprofiles.cgi?id=whoover">Wes Hoover</A><BR>
		(1996 - current)</TD></TR>
</TABLE>

</div>
<div style="padding:15px;">
EOM
&staffprofiles_shared_functions::print_additional_numbers();

print<<EOM;
$htmltail
EOM
} ## END IF id = ALL, list_view = 'birthday
#####################################################################################
# END:  PRINT LIST OF STAFF FOR INTRANET #
#####################################################################################






}  ## END PRINTING OF INTRANET-ONLY PAGES




######################################################################
##  Espanol Accent character replacement loop & Clean
######################################################################

sub cleanthis {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/“/"/g;         
   $dirtyitem =~ s/”/"/g;         
   $dirtyitem =~ s/’/'/g;         
   $dirtyitem =~ s/‘/'/g;
   $dirtyitem =~ s// /g;
   $dirtyitem =~ s/—/--/g;
   $dirtyitem =~ s/ //g; # invisible bullet
   return ($dirtyitem);
}

sub cleanaccents2html {
my $cleanitem = $_[0];
	$cleanitem =~ s/R&E/R&amp;E/g;			
	$cleanitem =~ s/Acuna/Acu&ntilde;a/g;			
	$cleanitem =~ s/\/\>/\>/gi; # REMOVE SINGLETON TAGS THAT ARE SELF-CLOSING

	$cleanitem =~ s/“/"/g;			
	$cleanitem =~ s/”/"/g;			
	$cleanitem =~ s/’/'/g;			
	$cleanitem =~ s/‘/'/g;
	$cleanitem =~ s// /g;
	$cleanitem =~ s/–/\&ndash\;/g;
	$cleanitem =~ s/—/\&mdash\;/g;
	$cleanitem =~ s/ //g; # invisible bullet
	$cleanitem =~ s/…/.../g;
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
## START SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################
sub depertment_getURL {
my $dept_name = $_[0];
		$dept_name = "<a href=\"/re/\">Research and Evaluation</a> (R&amp;E) program" if (($dept_name =~ 'Research') && ($dept_name =~ 'Evaluation'));
		$dept_name = "Disability Research to Practice (DRP) program" if $dept_name eq 'Disability Research to Practice (DRP) program';
		$dept_name = "Afterschool, Family, and Community program"  if $dept_name =~ 'fterschool';
		$dept_name = "Improving School Performance program" if $dept_name eq 'Improving School Performance program';
	return($dept_name);
}

#################################################################
## END SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################

#################################################################
## START SUBROUTINE: GET ARTCLE FOR JOB TITLE
#################################################################
sub get_article {
my $this_job_title = $_[0];
my $this_staff_id = $_[1];
my $article = "a";
	$article = "an" if $this_job_title eq 'Accounting Assistant';
	$article = "an" if $this_job_title eq 'Accounting Clerk';
	$article = "an" if $this_job_title eq 'Accounting Associate';
	$article = "an" if $this_job_title eq 'Accounting Specialist';
	$article = "an" if $this_job_title eq 'Accounting Supervisor';
	$article = "an" if $this_job_title eq 'Administrative Assistant';
	$article = "an" if $this_job_title eq 'Administrative Secretary';
	$article = "an" if $this_job_title eq 'Associate Development Director';
	$article = "the" if $this_job_title eq 'Executive Assistant';
	$article = "SEDL's" if $this_job_title eq 'Chief Program Officer';
	$article = "a" if $this_job_title eq 'Clerk';
	$article = "a" if $this_job_title eq 'Communications Assistant';
	$article = "a" if $this_job_title eq 'Communications Associate';
	$article = "a" if $this_job_title eq 'Communications Specialist';
	$article = "a" if $this_job_title eq 'Development Specialist';
	$article = "the" if $this_job_title =~ 'Director of';
	$article = "an" if $this_job_title eq 'Evaluation Manager';
	$article = "the" if $this_job_title eq 'Executive Vice President and COO';
	$article = "a" if $this_job_title eq 'Human Resources Generalist';
	$article = "an" if $this_job_title eq 'Information Assistant';
	$article = "an" if $this_job_title eq 'Information Associate';
	$article = "an" if $this_job_title eq 'Information Specialist';
	$article = "an" if $this_job_title eq 'Intern';
	$article = "a" if $this_job_title eq 'Network Administrator';
	$article = "a" if $this_job_title eq 'Network Assistant';
	$article = "a" if $this_job_title eq 'NIDRR Scholar';
	$article = "an" if $this_job_title eq 'Operator/Receptionist';
	$article = "the" if $this_job_title eq 'President and CEO';
	$article = "a" if $this_job_title eq 'Program Associate';
	$article = "" if $this_job_title eq 'Program Director';
	$article = "" if $this_job_title eq 'Project Director';
	$article = "a" if ($this_job_title eq 'Project Director');
	$article = "" if $this_job_title eq 'Program Manager';
	$article = "a" if $this_job_title eq 'Program Specialist';
	$article = "a" if $this_job_title eq 'Research Associate';
	$article = "a" if $this_job_title eq 'Research Intern';
	$article = "a" if $this_job_title eq 'Research Specialist';
	$article = "a" if $this_job_title eq 'Senior Accounting Clerk';
	$article = "a" if $this_job_title eq 'Systems Trainer';
	$article = "the" if $this_job_title eq 'VP and Chief Financial Officer';
	$article = "a" if $this_job_title eq 'Web Administrator';
	$article = "a" if $this_job_title eq 'Web Design Specialist';
	$article = "a" if $this_job_title eq 'Web Production Artist';
	return($article);
}

#################################################################
## END SUBROUTINE: GET ARTCLE FOR JOB TITLE
#################################################################


#################################################################
## START SUBROUTINE: do_encryption (for e-mail addresses)
#################################################################
sub do_encryption {
	my $text_to_encrypt = $_[0];
	   $text_to_encrypt =~ s/a/\&\#97\;/gi;
	   $text_to_encrypt =~ s/b/\&\#98\;/gi;
	   $text_to_encrypt =~ s/c/\&\#99\;/gi;
	   $text_to_encrypt =~ s/d/\&\#100\;/gi;
	   $text_to_encrypt =~ s/e/\&\#101\;/gi;
	   $text_to_encrypt =~ s/f/\&\#102\;/gi;
	   $text_to_encrypt =~ s/g/\&\#103\;/gi;
	   $text_to_encrypt =~ s/h/\&\#104\;/gi;
	   $text_to_encrypt =~ s/i/\&\#105\;/gi;
	   $text_to_encrypt =~ s/j/\&\#106\;/gi;
	   $text_to_encrypt =~ s/k/\&\#107\;/gi;
	   $text_to_encrypt =~ s/l/\&\#108\;/gi;
	   $text_to_encrypt =~ s/m/\&\#109\;/gi;
	   $text_to_encrypt =~ s/n/\&\#110\;/gi;
	   $text_to_encrypt =~ s/o/\&\#111\;/gi;
	   $text_to_encrypt =~ s/p/\&\#112\;/gi;
	   $text_to_encrypt =~ s/q/\&\#113\;/gi;
	   $text_to_encrypt =~ s/r/\&\#114\;/gi;
	   $text_to_encrypt =~ s/s/\&\#115\;/gi;
	   $text_to_encrypt =~ s/t/\&\#116\;/gi;
	   $text_to_encrypt =~ s/u/\&\#117\;/gi;
	   $text_to_encrypt =~ s/v/\&\#118\;/gi;
	   $text_to_encrypt =~ s/w/\&\#119\;/gi;
	   $text_to_encrypt =~ s/x/\&\#120\;/gi;
	   $text_to_encrypt =~ s/y/\&\#121\;/gi;
	   $text_to_encrypt =~ s/z/\&\#122\;/gi;
	
	return($text_to_encrypt);
}
#################################################################
## END SUBROUTINE: do_encryption
#################################################################

#################################################################
## START SUBROUTINE: abbr_to_department_name
#################################################################
sub abbr_to_department_name {
	my $dept_abbr = $_[0];
	my $dept_name = "(missing department name)";
	   $dept_name = "Afterschool, Family, and Community program" if ($dept_abbr eq "AFC");
	   $dept_name = "Administrative Services department" if ($dept_abbr eq "AS");
	   $dept_name = "Communications department" if ($dept_abbr eq "COM");
	   $dept_name = "Development department" if ($dept_abbr eq "DEV");
	   $dept_name = "Executive office" if ($dept_abbr eq "EO");
	   $dept_name = "Disability Research to Practice program" if ($dept_abbr eq "DRP");
	   $dept_name = "Improving School Performance program" if ($dept_abbr eq "ISP");
	   $dept_name = "Research and Evaluation (R&E) program" if ($dept_abbr eq 'R&E');
	return($dept_name);
}

#################################################################
## END SUBROUTINE: do_encryption
#################################################################


#################################################################
## START SUBROUTINE: get_pd_sessions_taught
#################################################################
sub get_pd_sessions_taught {
	my $userid = $_[0];
	my $firstname = $_[1];
	my $lastname = $_[2];

	## CHECK TO SEE IF THIS STAFF MEMBER IS A CATALOG AUTHOR
	my $command = "select cpl_sessions.cpls_id, cpl_sessions.cpls_title, cpl_training_dates.*
				from  cpl_sessions, cpl_training_dates  WHERE cpl_sessions.cpls_id = cpl_training_dates.cpltd_cpls_id";
	   $command .= " AND (cpl_sessions.cpls_presenters LIKE '%$userid%')";
	   $command .= " AND (cpl_sessions.cpls_active = 'yes')";
#	   $command .= " AND cpl_training_dates.cpltd_date_start > '$date_full_mysql'";
	   $command .= " order by cpl_training_dates.cpltd_date_start";
	
	## OPEN THE DATABASE AND SEND THE QUERY
	my $dsn = "DBI:mysql:database=corp;host=localhost";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	my $count = "1";

	##############################################################
	# START: LOOP THAT PRINTS LINKS TO RELATED SEDL PUBLICATIONS #
	##############################################################
my $sessions_taught = 0;
my $test_linking_to_sessions = "";
my $s = "";
	while (my @arr = $sth->fetchrow) {
		my ($cpls_id, $cpls_title,
			$cpltd_id, $cpltd_cpls_id, $cpltd_date_start, $cpltd_date_start_pretty, $cpltd_description, $cpltd_last_updated, $cpltd_last_updated_by, $cpltd_registration_form) = @arr;
		   $s = "s" if ($num_matches != 1);
			$sessions_taught++;
			if ($sessions_taught == 1) {
				$test_linking_to_sessions = "$firstname $lastname teaches SEDL's professional development session, \"<a href=\"/cpl/sessions/$cpls_id.html\">$cpls_title</a>\"";
				if ($cpltd_date_start gt $date_full_mysql) {
					$test_linking_to_sessions .= ",\" which is scheduled for $cpltd_date_start_pretty";
				}
			} else {
				$test_linking_to_sessions .= ", and another session, \"<a href=\"/cpl/sessions/$cpls_id.html\">$cpls_title</a>\"";
				if ($cpltd_date_start gt $date_full_mysql) {
					$test_linking_to_sessions .= ",\" which is scheduled for $cpltd_date_start_pretty";
				}
			}

	} ## END: LOOP THAT PRINTS LINKS TO RELATED SEDL PUBLICATIONS
	if ($sessions_taught != 0) {
		$test_linking_to_sessions = "<p class=\"dottedBoxyw\"><strong>Professional Development Session$s Presented by $firstname $lastname</strong><br>$test_linking_to_sessions.</p>";
		$test_linking_to_sessions =~ s/"\./\."/gi;
		$test_linking_to_sessions =~ s/"\,"/\,"/gi;
		$test_linking_to_sessions =~ s/"\,/\,"/gi;
	}
	return($test_linking_to_sessions);
#	return($command);
}

#################################################################
## END SUBROUTINE: get_pd_sessions_taught
#################################################################

## SUBROUTINE THAT RETURNS FULL MONTH NAME WHEN YOU SENT IT A MONTH NUMBER
sub get_fullmonthname {
my $monthnumber = $_[0];
my $month_name = $monthnumber;
	$month_name = "January" if $monthnumber eq 'Jan';
	$month_name = "February" if $monthnumber eq 'Feb';
	$month_name = "March" if $monthnumber eq 'Mar';
	$month_name = "April" if $monthnumber eq 'Apr';
	$month_name = "May" if $monthnumber eq 'May';
	$month_name = "June" if $monthnumber eq 'Jun';
	$month_name = "July" if $monthnumber eq 'Jul';
	$month_name = "August" if $monthnumber eq 'Aug';
	$month_name = "September" if $monthnumber eq 'Sep';
	$month_name = "October" if $monthnumber eq 'Oct';
	$month_name = "November" if $monthnumber eq 'Nov';
	$month_name = "December" if $monthnumber eq 'Dec';
	return ($month_name);
}

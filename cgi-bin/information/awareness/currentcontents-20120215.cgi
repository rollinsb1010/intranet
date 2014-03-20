#!/usr/bin/perl 

#use diagnostics;
use strict;
use CGI qw/:all/;

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

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 
use intranetcommoncode; 



###########################################
## START: VARIABLE DECLARATIONS
###########################################

my @list_topics = (

"Academic Achievement",
"Achievement Gap and Poverty/Minorities",
"Afterschool Programs",
"Assessment and Measurement",
"Autism and Adults",
"Constructivism",
"Cultural Diversity",
"Curriculum Planning",
"Data Analysis in Education",
"Disability Research",
"Early Childhood Education/Development",
"Education Policies",
"Education Policies and State Issues",
"Educational Leadership",
"Educational Technology",
"Family Involvement in Student Learning/ Achievement",
"High School Reform",
"Information Dissemination",
"Innovation Adoption",
"Innovation Diffusion",
"Knowledge of Sociology",
"Knowledge Transfer",
"Knowledge Utilization",
"Language Development",
"Learning/Educational Psychology",
"Learning Theories",
"Math--Study & Teaching",
"Mentoring in Education",
"No Child Left Behind (General)",
"No Child Left Behind in AR, NM, OK, and TX",
"No Child Left Behind in LA,, MS, AL, GA, and SC",
"Performance and Measurement",
"Pre-Kindergarten",
"Professional Development/CE",
"Program Evaluation",
"Professional Learning Communities",
"Reading First",
"Reading Research",
"Reading Surveys",
"Research Dissemination & Utilization",
"Response to Intervention",
"Rural Education Funding/Resource Allocation",
"Rural Education Issues",
"School Finance",
"School Safety",
"Science--Study & Teaching",
"Second Language Learning",
"Special Education and IDEA",
"Special Education and Math",
"Standards Testing and Schools",
"Systemic Reform",
"Teacher Compensation/Salaries",
"Teacher Education",
"Teacher Effectiveness & Quality",
"Teacher Expectations",
"Urban Education Issues",
"Value-Added Models",
"Working Systemically");


my $count_topics = $#list_topics;


my @list_journals = (
"American Journal of Education",
"American Journal of Evaluation",
"American School Board Journal",
"Education & Urban Society",
"Education Week",
"Educational Administration Quarterly",
"Educational Leadership",
"Educational Policy",
"Educational Research Quarterly",
"Elementary School Journal",
"Exceptional Children",
"Harvard Educational Review",
"Journal of Education for Students Placed at Risk",
"Journal of Educational Research",
"Journal of Research on Technology in Education",
"Journal of Special Education",
"Journal of Teacher Education",
"Journal of Disability Policy Studies",
"Leadership",
"Learning and Instruction",
"Learning Disability Quarterly",
"NEA Today",
"Phi Delta Kappan",
"Preventing School Failure",
"Remedial & Special Education",
"Review of Educational Research",
"School Effectiveness & School Improvement",
"Scientific Studies of Reading",
"Teachers College Record",
"Urban Education");

my $count_journals = $#list_journals;

################################
## START: READ IN USER CHOICES
################################
my $masterlist_choices = "";
my @choices_journals = "";

			$masterlist_choices .= "JOURNALS:\n";
my $counter = "0";
	while ($counter <= $count_journals) {
		$choices_journals[$counter] = $query->param("j$counter");
		if ($choices_journals[$counter] ne '') {
			$masterlist_choices .= "$choices_journals[$counter]\n";
		}
		$counter++;
	}

			$masterlist_choices .= "\n\nTOPICS:\n";
my @choices_topics = "";
my $counter = "0";
	while ($counter <= $count_topics) {
		$choices_topics[$counter] = $query->param("t$counter");
		if ($choices_topics[$counter] ne '') {
			$masterlist_choices .= "$choices_topics[$counter]\n";
		}
		$counter++;
	}

################################
## END: READ IN USER CHOICES
################################

###########################################
## END: VARIABLE DECLARATIONS
###########################################


## GET THE SEARCH PARAMETER VARIABLES FROM THE HTML SEARCH FORM
my $location = $query->param("location");
   $location = "mainmenu" if $location eq '';

my $user = $query->param("user");
   $user =~ s/\@sedl\.org//g;

my $contentsnew = $query->param("contentsnew");



## COOKIE VARIABLES
my $expdate = "Fri, 25-Dec-2015 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";



## CHECK FOR COOKIE IF USER ID IS MISSING
if ($user eq '') {
my(%cookies) = getCookies();

foreach (sort(keys(%cookies))) {
$user = $cookies{$_} if (($_ eq 'staffid') && ($user eq ''));
#$name = $cookies{$_} if $_ eq 'username';
}

} # END OF COOKIE CHECK


##########################################################
## CHECK IF A USER ID WAS ENTERED
##########################################################
my $errormessage = "";
	if (($location eq 'sendrequest') && ($user eq '')) {
		$errormessage .= "<P>You forgot to enter your user ID.  Please try again." if $user eq '';
		$location = "mainmenu";
	}


##########################################################
## CHECK IF THIS IS A VALID USER ID
##########################################################
my $validuser = "no";
if (($location eq 'sendrequest') && ($user ne '')) {
my $command = "select userid from staff_profiles where ((userid like '$user')) order by userid";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

## SET ERROR MESSAGE IF USER ID IS INVALID
if ($num_matches eq '0') {
	$errormessage .= "<P><FONT COLOR=RED>You entered an invalid user ID.  Please try again.</FONT><P>" ; 
	$location = "mainmenu";
	$validuser = "yes" if $num_matches eq '1';
}

## SET COOKIE WITH USER ID IF ITS VALID
if ($num_matches eq '1') {
	setCookie ("staffid", $user, $expdate, $path, $thedomain);
}

}  # END CHECK FOR VALID USER ID




###############################################
# START: READ IN STAFF HEADER AND FOOTER HTML #
###############################################
my $htmlhead = "";
my $htmltail = "";
my $info_associate = "";


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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("121"); # 121 is the PID for this page in the intranet database

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";



open(IA,"</home/httpd/html/staff/includes/infoassociate.txt");
while (<IA>) {
	$info_associate .= $_;
}
close(IA);
###############################################
# END: READ IN STAFF HEADER AND FOOTER HTML #
###############################################



## START THE OUTPUT BY CREATING THE HTML HEADER AND PAGE TITLE
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL Staff - Information Services - Awareness Services - Current Contents Updates</TITLE>

$htmlhead

<h1 style="margin-top:0px;">Current Contents Updates</H1>
<P>
EOM








##############################
## START: LOCATION = MAINMENU
##############################
if ($location eq 'mainmenu') {

print<<EOM;
$errormessage


Any SEDL staff member may sign up for FREE current awareness updates, sent to your e-mail address.
	<ul>
	<li><strong>What are current content updates?</strong> Each week, you will receive an e-mail containing citations and abstracts of articles for each topic you choose. Tables of contents for each journal are sent when a new issue is indexed online. You will receive one e-mail per topic or journal selected.</li>
	<li><strong>Who provides this service?</strong> SEDL's information associate, <a href="/pubs/catalog/authors/nreynold.html">Nancy 
	Reynolds</a> in SEDL's Information Resource Center (IRC), sets up the search strategies for which you will 
	receive e-mail alerts. Use the form below to request or cancel your subscription to current awareness topics or 
	journals as often as you like. You can even request tailored search strategies on a topic of your choice on the 
	form below or by contacting SEDL's information associate, 
	<a href="/pubs/catalog/authors/nreynold.html">Nancy Reynolds</a> at ext. 6548 or 
	by e-mail to <a href="nancy.reynolds\@sedl.org">nancy.reynolds\@sedl.org</a>. You also can let Nancy 
	know if you would like to receive your e-alerts on a daily, biweekly, or monthly basis instead of weekly.</li>
	</ul>



  
<FORM ACTION="/staff/information/awareness/currentcontents.cgi" METHOD="GET">

<TABLE BORDER="0" CELLPADDING="10" CELLSPACING="0" WIDTH="100%">

<TR><TD VALIGN="TOP"><P><strong><label for="user">Your User ID</label></strong><br> (ex: whoover)</TD>
    <TD><INPUT TYPE="TEXT" NAME="user" id="user" SIZE="8" value="$user"></TD></TR>

<TR><TD VALIGN="TOP" COLSPAN="2"><P><strong>Select the items for which you would like to receive weekly updates.</strong></TD></TR>
<TR><TD WIDTH="60%" VALIGN="TOP"><em>Topics:</em><br>
    	<TABLE CELLPADDING="2" CELLSPACING="2">
EOM
 

#################################
## START: PRINT LIST OF TOPICS
#################################
my $counter = "0";
	while ($counter <= $count_topics) {
		print "<TR><TD VALIGN=\"TOP\"><INPUT TYPE=\"CHECKBOX\" NAME=\"t$counter\" id=\"t$counter\" value=\"$list_topics[$counter]\"></TD>
					<TD><label for=\"t$counter\">$list_topics[$counter]</label></TD></TR>";
		$counter++;
	}
#################################
## END: PRINT LIST OF TOPICS
#################################
 
print<<EOM;
		</TABLE>
	</TD>
	<TD WIDTH="40%" VALIGN="TOP"><em>Journals</em><br>
	Receive the latest issue's Table of Contents (TOC) for the following journals or from the 
	<A HREF="http://www.sedl.org/staff/information/resources/pdc_database_titles.xls">list of journals 
	indexed in EBSCO's Academic Search Elite database</a>.<br>
    	<TABLE CELLPADDING="2" CELLSPACING="2">
EOM

#################################
## START: PRINT LIST OF JOURNALS
#################################
my $counter = "0";
	while ($counter <= $count_journals) {
		print "<TR><TD VALIGN=\"TOP\"><INPUT TYPE=CHECKBOX NAME=\"j$counter\" id=\"j$counter\" value=\"$list_journals[$counter]\"></TD>
					<TD><label for=\"j$counter\">$list_journals[$counter]</label></TD></TR>";
		$counter++;
	}
#################################
## END: PRINT LIST OF JOURNALS
#################################



print<<EOM;
    	</TABLE>
    </TD></TR>
</TABLE>
<P>
<TABLE>
<TR><TD VALIGN="TOP" WIDTH="150"><P><strong><label for="contentsnew">Suggest a Category</label></strong><br>(if your needs are not served by the categories above)</TD>
    <TD WIDTH="420"><TEXTAREA NAME="contentsnew" id="contentsnew" ROWS="3" COLS="50"></TEXTAREA></TD></TR>
</TABLE>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="sendrequest">
  <INPUT TYPE="SUBMIT" VALUE="Send Request">
  </div>
  </form>
EOM
}
## END: LOCATION = MAINMENU



##############################
## START: LOCATION = SEARCH
##############################
if ($location eq 'sendrequest') {

################################################################
## START: GET THIS STAFF MEMBER's INFO FROM THE PROFILE DATABASE
################################################################
my $command = "select firstname, lastname, jobtitle, phone, userid, email, phoneext, department_abbrev from staff_profiles where ((userid like '$user')) order by userid";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;
my $name = "";

## GET THE RESULTS OF THE QUERY
while (my @arr = $sth->fetchrow) {
    my ($firstname, $lastname, $jobtitle, $phone, $userid, $email, $phoneext, $department_abbrev) = @arr;
		$name = "$firstname $lastname";
		$phoneext = "x-$phoneext" if $phoneext;



## SEND E-MAIL TO MARY
my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
my $recipient = 'info@sedl.org';
#   $recipient = 'blitke@sedl.org';
my $fromaddr = 'webmaster@sedl.org';


open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: Current Contents Signup Form <$fromaddr>
To: $recipient, $email
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Data from Current Contents Signup Form

The following data was received from the Current Contents Signup Form at:
http://www.sedl.org/staff/information/awareness/currentcontents.cgi

+-----------------------------------------------+
| The Current Contents Information starts here: |
+-----------------------------------------------+


REQUEST FROM:
-------------
Name: $name 

Phone: $phone $phoneext

E-mail: $email

Department: $department_abbrev


CURRENT CONTENTS CHOICES:
-----------------------
$masterlist_choices


NEW TOPIC SUGGESTIONS WOULD APPEAR HERE, IF ANY:
------------------------------------------------
$contentsnew


---End of Training Signup Data---

EOM
print NOTIFY remote_host,"\n",remote_addr,"\n";
;
close(NOTIFY);



## SAVE NEW CURRENT CONTENTS SETTINGS TO DB

## SHOW THANK YOU WITH LINK BACK TO INFO SERVICES
print <<EOM;
<h2 style="margin-top:0px;">Thank You</H2>
<P>
Your Current Contents settings have been updated, and weekly updates will 
be sent to you automatically for the topics you chose.  
<P>
Click here to return to the <A HREF="/cgi-bin/mysql/staff/index.cgi?show_s=2">Information Services</A> page.
EOM

	} # END OF DB LOOP TO GRAB THE STAFF MEMBER'S INFO
################################################################
## END: GET THIS STAFF MEMBER's INFO FROM THE PROFILE DATABASE
################################################################

if ($num_matches eq '0') {
	print "<h1 style=\"margin-top:0px;\">Error</H1><P>You did not enter a valid user ID.</p>";
}

} # END: LOCATION = SENDREQUEST

print<<EOM;
$htmltail
EOM



sub cleanthis {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/Ò/"/g;         
   $dirtyitem =~ s/Ó/"/g;         
   $dirtyitem =~ s/Õ/\'/g;         
   $dirtyitem =~ s/Ô/\'/g;
   $dirtyitem = $dirtyitem;
}


######################################################################
##  Espanol Accent character replacement loop & Clean
######################################################################

sub cleanaccents2html {
my $cleanitem = $_[0];
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
   $cleanitem =~ s/ó/&Ucirc\;/g;
   $cleanitem =~ s/ž/&ucirc\;/g;
   $cleanitem =~ s/†/&Uuml\;/g;
   $cleanitem =~ s/Ÿ/&uuml\;/g;
   $cleanitem =~ s/Ø/&yuml\;/g;
   
   $cleanitem = $cleanitem;
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






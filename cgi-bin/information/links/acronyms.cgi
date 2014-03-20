#!/usr/bin/perl
use strict;
#use diagnostics;
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
my $count = "0";


###################################################
## START: GET VARIABLES SENT BY USER ON HTML FORM
###################################################
my $newacronym = $query->param("newacronym");
my $newdefinition = $query->param("newdefinition");
my $newsortby = $query->param("newsortby");
my $newsubmittedby = $query->param("newsubmittedby");
my $newurl = $query->param("newurl");

my $location = $query->param("location");

my $showacronym = $query->param("showacronym");
   
my $user = $query->param("user");
my $pass = $query->param("pass");

my $sortlist = $query->param("sortlist");
   $sortlist = "acronym" if ($sortlist eq '');

my $submit = $query->param("submit") || "";

###################################################
## END: GET VARIABLES SENT BY USER ON HTML FORM
###################################################

#######################################
## START: SET DEFAULT COOKIE VARIABLES
#######################################
my $expdate = "Fri, 25-Dec-2015 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";
#######################################
## END: SET DEFAULT COOKIE VARIABLES
#######################################


########################################
# START: READ IN SEDL HEADER AND FOOTER
########################################
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

$htmlhead .= "<TABLE CELLPADDING=\"15\"><TR><TD>";
$htmltail = "</td></tr></table>$htmltail";
########################################
# END: READ IN SEDL HEADER AND FOOTER 
########################################

########################################
## START: READ IN THE PASSWORD FILE
########################################
my %words = ""; #FOR CHECKING PASSWORDS BEFORE UPDATING SCHEDULER DATABASE
my @users = ""; #FOR PRINTING IN DEBUG STATEMENT AT BOTTOM OF SCREEN
my $usercount = 0;
open(PASSWORDLIST,"</home/httpd/cgi-bin/mysql/schedulerusers.cgi");
while (<PASSWORDLIST>) {
my $userandpass = $_;
$users[$usercount] = $userandpass;
$usercount++;
my ($name, $word) = split(/\,/,$userandpass);
chomp($word);
$words{$name} = $word;
}
close(PASSWORDLIST);
########################################
## END: READ IN THE PASSWORD FILE
########################################



##########################################################
## START: CHECK FOR COOKIE IF USER ID IS MISSING
##########################################################
if ($user eq '') {
my(%cookies) = getCookies();

foreach (sort(keys(%cookies))) {
$user = $cookies{$_} if (($_ eq 'staffid') && ($user eq ''));
$pass = $cookies{$_} if (($_ eq 'staffpwd') && ($cookies{$_} ne ''));
#$name = $cookies{$_} if $_ eq 'username';
}

} # END OF COOKIE CHECK
##########################################################
## END: CHECK FOR COOKIE IF USER ID IS MISSING
##########################################################


##########################################################
## CHECK IF A USER ID WAS ENTERED
##########################################################
my $errormessage = "";
if ((($location eq 'modify') || ($location eq 'delete') || ($location eq 'add')) && ($user eq '')) {
$errormessage .= "<P>You forgot to enter your user ID.  Please try again." if $user eq '';
$location = "list";
}


##########################################################
## CHECK IF THIS IS A VALID USER ID
##########################################################
my $fullname = "";

if ( (($submit eq 'Modify') || ($submit eq 'Delete') || ($submit eq 'Add')) && ($user ne '') && ($newacronym ne '')) {
my $command = "select firstname, lastname, userid, email from staff_profiles where userid like '$user'";
my $dbh = DBI->connect($dsn, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

	while (my @arr = $sth->fetchrow) {
	    my ($firstname, $lastname, $userid, $email) = @arr;
			$fullname = "$firstname $lastname";
	} # END DB QUERY LOOP
	
	## SET ERROR MESSAGE IF USER ID IS INVALID
	if ($num_matches eq '0') {
		$errormessage .= "<P>You entered an invalid user ID.  Please try again." ; 
		$location = "evaluation";
	}

	## SET COOKIE WITH USER ID IF ITS VALID
	if ($num_matches eq '1') {
		setCookie ("staffid", $user, $expdate, $path, $thedomain);
	}

} # END VALID USER ID CHECK IF ADDING, UPDATING, OR DELETING



## PRINT HTML PAGE HEADER
print header;
print <<EOM;
<HTML>
<HEAD>
<TITLE>SEDL Acronym List</TITLE>
$htmlhead
<H1>List of Acronyms Heard Around SEDL</H1>
<P>
Click here to re-sort the list 
EOM

print "<A HREF=\"acronyms.cgi?sortlist=acronym\">" if ($sortlist ne 'acronym');
print "<B>" if ($sortlist eq 'acronym');
print "alphabetically";
print "</A>" if ($sortlist ne 'acronym');
print "</B>" if ($sortlist eq 'acronym');
print " or ";
print "<A HREF=\"acronyms.cgi?sortlist=type\">" if ($sortlist ne 'type');
print "<B>" if ($sortlist eq 'type');
print "by category";
print "</A>" if ($sortlist ne 'type');
print "</B>" if ($sortlist eq 'type');

print<<EOM;
.<P>
You can edit existing entries by clicking on their acronym.  Or you may add new entries using the form at the <A HREF="#new">bottom of the page</A>. 
EOM


##############################################
## START: IF MODIFYING AN ENTRY, DO THAT NOW
##############################################

## BACKSLASH SPECIAL CHARACTERS AND REMOVE LINE BREAKS/TABS
$newacronym = &cleanfordb ($newacronym) if ($newacronym ne '');
$newdefinition = &cleanfordb ($newdefinition) if ($newdefinition ne '');
$newsortby = &cleanfordb ($newsortby) if ($newsortby ne '');
$newsubmittedby = &cleanfordb ($newsubmittedby) if ($newsubmittedby ne '');
$newurl = &cleanfordb ($newurl) if ($newurl ne '');

my $checkpassword = "";
if (($words{$user} ne $pass) || ($user eq '')) {
   $checkpassword = "bad";
}

print "<P><FONT COLOR=RED>Sorry, your entry was not accepted.<BR>Your user ID and password do not match those on file.  Contact <A HREF=\"mailto:blitke\@sedl.org\">Brian Litke at x-260</A> for assistance.</FONT>" if $checkpassword eq 'bad';

print "<P><FONT COLOR=RED>You didn't enter an acronym.  Please try again.</FONT>" if ( (($submit eq 'Modify') || ($submit eq 'Delete') || ($submit eq 'Add')) && ($newacronym eq ''));

if (($submit eq 'Modify') && ($checkpassword ne 'bad') && ($newacronym ne '')) {
	my $command = "UPDATE acronyms SET definition='$newdefinition',sortby='$newsortby',submittedby='$user',url='$newurl' where acronym like '$newacronym'";
	print "<P>COMMAND TO DATABASE: $command<P>" if $debug;
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	#my $num_matches = $sth->rows;
}

## IF DELETING AN ENTRY, DO THAT NOW
if (($submit eq 'Delete') && ($checkpassword ne 'bad') && ($newacronym ne '')) {
	my $command = "DELETE FROM acronyms WHERE acronym like '$newacronym'"; 
	print "<P>COMMAND TO DATABASE: $command<P>" if $debug;
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	#my $num_matches = $sth->rows;
}

## IF ADDING AN ENTRY, DO THAT NOW
if ($submit eq 'Add'){
	my $command = "select * from acronyms where acronym like '$newacronym'";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
print "<P><FONT COLOR=RED>You entered a duplicate acronym.  Naughty, naughty.  That is not allowed.  Go stand in a corner or call Brian for assistance. (just kidding - you can always enter an acronym</FONT>" if ($num_matches ne '0');

}

if (($submit eq 'Add') && ($checkpassword ne 'bad') && ($newacronym ne '')) {
   my $command = "INSERT INTO acronyms VALUES ('$newacronym', '$newdefinition', '$newsortby', '$user', '$newurl')";
   print "<P>COMMAND TO DATABASE: $command<P>" if $debug;
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
}
########################################
## END: IF MODIFYING AN ENTRY, DO THAT NOW
########################################



#################################################
## START: DISPLAY CURRENT ITEMS IN ACRONYM LIST
#################################################
my $command = "select * from acronyms";
   $command.= " where acronym like '$showacronym'" if ($showacronym ne '');
   $command .= " order by acronym" if ($sortlist eq 'acronym');
   $command .= " order by sortby, acronym" if ($sortlist eq 'type');
   

## SEND COMMAND TO DATABASE
print "<P>COMMAND TO DATABASE: $command<P>" if $debug;

	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;







if ($submit eq 'Search'){
##########################
## START: SHOW EDIT VIEW
##########################
print "<P>Make any necessary changes, then click the \"Delete\" or \"Modify\" buttons to update the record." if ($submit eq 'Search');

print<<EOM;
<TABLE BORDER="1">
<TR><TD><B>ACRONYM</B></TD><TD><B>FULL NAME</B></TD><TD><B>CATEGORY</B></TD><TD><B>URL</B></TD><TD><B>LAST<BR>EDITED<BR>BY</B></TD></TR>
EOM

## LOOP THROUGH DATA AND PRINT INDIVIDUAL ENTRIES
while (my @arr = $sth->fetchrow) {
    $count++;
    my ($acronym, $definition, $sortby, $submittedby, $url) = @arr;

my $selected1 = "";
	$selected1 = "SELECTED" if $sortby eq 'national';
my $selected2 = "";
	$selected2 = "SELECTED" if $sortby eq 'state-AR';
my $selected3 = "";
	$selected3 = "SELECTED" if $sortby eq 'state-LA';
my $selected4 = "";
	$selected4 = "SELECTED" if $sortby eq 'state-NM';
my $selected5 = "";
	$selected5 = "SELECTED" if $sortby eq 'state-OK';
my $selected6 = "";
	$selected6 = "SELECTED" if $sortby eq 'state-TX';
my $selected7 = "";
	$selected7 = "SELECTED" if $sortby eq 'SEDL';
my $selected8 = "";
	$selected8 = "SELECTED" if $sortby eq 'SEDL-old';
my $selected9 = "";
	$selected9 = "SELECTED" if $sortby eq 'technology';

print<<EOM;
<TR><form method="POST" action="acronyms.cgi">
	<TD VALIGN=TOP><input type="text" name="newacronym" size="8" value="$acronym"></TD>
	<TD VALIGN=TOP><TEXTAREA NAME="newdefinition" COLS="20" ROWS="8">$definition</TEXTAREA></TD>
	<TD VALIGN=TOP><SELECT NAME="newsortby">
		<OPTION></OPTION>
		<OPTION $selected1>national</OPTION>
		<OPTION $selected2>state-AR</OPTION>
		<OPTION $selected3>state-LA</OPTION>
		<OPTION $selected4>state-NM</OPTION>
		<OPTION $selected5>state-OK</OPTION>
		<OPTION $selected6>state-TX</OPTION>
		<OPTION $selected7>SEDL</OPTION>
		<OPTION $selected8>SEDL-old</OPTION>
		<OPTION $selected9>technology</OPTION>
		</SELECT></TD>
	<TD VALIGN=TOP><input type="text" name="newurl" size="24" value="$url"></TD>
	<TD VALIGN=TOP>$submittedby<input type="hidden" name="newsubmittedby" value="$user"></TD>
</TR>
</TABLE>
<P>
Your user-ID: <input type="text" size="8" name="user" value="$user"><BR>
Your intranet password: <input type="password" size="8" name="pass" value="$pass">
<P>
<INPUT TYPE="submit" NAME=submit VALUE="Modify"> (Click here to submit any changes)
<P>
<INPUT TYPE="submit" NAME=submit VALUE="Delete"> <FONT COLOR=RED>Click here to permanantly erase this record.</FONT>
</FORM>

EOM
} # END DB QUERY LOOP

##########################
## END: SHOW EDIT VIEW
##########################


} else {
##########################
## START: SHOW LIST VIEW
##########################

## FINISH ACRONYM LIST AND ALLOW CHANGES
print<<EOM;
<P>
<TABLE BORDER="1">
<TR><TD><B>Acronym</B></TD><TD><B>Full Name</B></TD><TD><B>Online</B></TD><TD><B>Category</B></TD></TR>
EOM

## LOOP THROUGH DATA AND PRINT INDIVIDUAL ENTRIES
while (my @arr = $sth->fetchrow) {
    $count++;
    my ($acronym, $definition, $sortby, $submittedby, $url) = @arr;
my $acronymlinklabel = $acronym;
   $acronymlinklabel =~ s/ /\%20/g;

print<<EOM;
<TR><TD><A HREF="acronyms.cgi?showacronym=$acronymlinklabel&submit=Search">$acronym</A></TD>
	<TD>$definition</TD>
	<TD>
EOM
print "<A HREF=\"$url\">Web site</A>" if ($url ne '');
print "</TD><TD>$sortby</TD></TR>\n";

} # END DB QUERY LOOP

## PRINT PAGE FOOTER
print "</TABLE>\n";
##########################
## END: SHOW LIST VIEW
##########################

} ## END IF/THEN FOR EDIT/LIST VIEWS




##############################################
## START: "SEARCH FOR RECORDS TO EDIT" OPTION
##############################################
print <<EOM;
<A NAME="new"></A>
<P>
<TABLE BGCOLOR="E0CF9F" BORDER=0 CELLPADDING=5 CELLSPACING=0>
<TR><TD VALIGN=TOP COLSPAN=2><H2>You may Add a new acronym to the list</H2></TD></TR>
<TR><TD VALIGN=TOP>
	<Form method="POST" action="/staff/information/links/acronyms.cgi">
Acronym:</TD><TD><input type="text" size="8" name="newacronym"></TD></TR>
<TR><TD VALIGN=TOP>Full name:</TD><TD VALIGN=TOP><TEXTAREA NAME="newdefinition" COLS="50" ROWS="4"></TEXTAREA></TD></TR>
<TR><TD VALIGN=TOP>Category:</TD><TD VALIGN=TOP><SELECT NAME="newsortby">
		<OPTION>national</OPTION>
		<OPTION>state-AR</OPTION>
		<OPTION>state-LA</OPTION>
		<OPTION>state-NM</OPTION>
		<OPTION>state-OK</OPTION>
		<OPTION>state-TX</OPTION>
		<OPTION>SEDL</OPTION>
		<OPTION>SEDL-old</OPTION>
		<OPTION>technology</OPTION>
		</SELECT></TD></TR>
<TR><TD VALIGN=TOP>Your user-ID:</TD><TD VALIGN=TOP><input type="text" size="8" name="user" value="$user"></TD></TR>
<TR><TD VALIGN=TOP>Your intranet password:</TD><TD VALIGN=TOP><input type="PASSWORD" size="8" name="pass" value="$pass"></TD></TR>
<TR><TD VALIGN=TOP>URL (optional):</TD><TD VALIGN=TOP><input type="text" size="32" name="newurl"></TD></TR>
<TR><TD VALIGN=TOP COLSPAN=2><INPUT TYPE="submit" NAME="submit" VALUE="Add">
	</FORM>
	</TD>
</TR>
</TABLE>
EOM
##############################################
## END: "SEARCH FOR RECORDS TO EDIT" OPTION
##############################################



print "$htmltail";






sub cleanfordb {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\\//g;
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/Ô/\\Ô/g;
   $dirtyitem =~ s/Õ/\\Õ/g;
   $dirtyitem =~ s/"/\\"/g;
   $dirtyitem =~ s/=/\\=/g;
   $dirtyitem =~ s/\+/\\+/g;
   $dirtyitem =~ s/\*/\\*/g;
   $dirtyitem =~ s/&/\\&/g;
   $dirtyitem =~ s/\,/\\,/g;
   $dirtyitem =~ s/\?/\\?/g;
   $dirtyitem =~ s/\;/\\;/g;
   $dirtyitem =~ s/\:/\\:/g;
   $dirtyitem =~ s/\-/\\-/g;
   $dirtyitem =~ s/\)/\\)/g;
   $dirtyitem =~ s/\(/\\(/g;
   $dirtyitem =~ s/\{/\\{/g;
   $dirtyitem =~ s/\}/\\}/g;
   $dirtyitem =~ s/\]/\\]/g;
   $dirtyitem =~ s/\[/\\[/g;
   $dirtyitem =~ s/\|/\\|/g;
   $dirtyitem =~ s/\^/\\^/g;
   $dirtyitem =~ s/\%/\\%/g;
   $dirtyitem =~ s/\#/\\#/g;
   $dirtyitem =~ s/\@/\\@/g;
   $dirtyitem =~ s/\!/\\!/g;
#   $dirtyitem =~ s/\_/\\_/g;
   $dirtyitem =~ s/\~/\\~/g;
   $dirtyitem =~ s/\</\\</g;
   $dirtyitem =~ s/\>/\\>/g;
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





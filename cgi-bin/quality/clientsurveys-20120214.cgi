#!/usr/bin/perl

#####################################################################################################
# Copyright 2002 by Southwest Educational Development Laboratory
#
# Written by Brian Litke 05-08-2002
#####################################################################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);

use DBI;
my $dsn = "DBI:mysql:database=corp;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

use Number::Format;
# my $this_number
#	my $x = new Number::Format;
#	$this_number = $x->format_number($this_number, 2, 2);

my $query = new CGI;
my $debug = $query->param("debug") || "0"; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS

my $browser = $ENV{"HTTP_USER_AGENT"};
my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};

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

	my $date_full_pretty_4digityear = "$month/$monthdate_wleadingzero/$year"; # Full date in human-readable format  (e.g. 03/06/08)

#my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
#   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;
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


## GRAB VARIABLES PASSED FROM FORM
my $location = param('location');
   $location = "summary_countdate" if $location eq '';
#   $location = "showoptions";
my $surveyid = param('surveyid');
my $confirm = param('confirm'); # USED IN datadump LOCATION TO CONFIRM READY TO DOWNLOAD DATA 
my $print = param('print');

my $sortby = param('sortby');
   $sortby = "date, email" if ($sortby eq 'date');
   $sortby = "date, email" if ($sortby eq '');

my $showdocid = param('showdocid');
my $showpdf = param('showpdf');
my $show_followuprequests = param('show_followuprequests');
my $show_usecomments = param('show_usecomments');
my $show_address = param('show_address');

my $search_dateafter = "2002-05-02";
	my $search_date = param('search_date');
	my $search_month = param('search_month');
	my $search_year = param('search_year');
	if (($search_date ne '') && ($search_month ne '') && ($search_year ne '')) {
		$search_dateafter = "$search_year\-$search_month\-$search_date";
	}
	my $search_dateafter_label = &date2standard($search_dateafter); # pretty date format for showing on screen

my $badaddress = param('badaddress');
my $goodaddress = param('goodaddress');
my $badpdf = param('badpdf');
my $summarysite = param('summarysite');
   $location = "list_departments" if (($location eq 'summary') && ($summarysite eq ''));
my $bad_surveynumber = param('bad_surveynumber');
	$bad_surveynumber =~ tr/-0-9//cd; # Eliminate anything that's not in 0 - 9 or "."

my $confirm_delete = param('confirm_delete');
my $show_responses_only = param('show_responses_only');
my $new_staff_comments = param('new_staff_comments');
my $new_staff_comments_by = param('new_staff_comments_by');
     
#######################################
# READ IN INTRANET HEADER AND FOOTER  #
#######################################
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

$htmlhead .= "<table border=\"0\" cellpadding=\"15\" cellspacing=\"0\" width=\"100%\"><tr><td>";
$htmltail = "</td></tr></table>$htmltail";

if ($print ne '') {
$htmlhead = "<HTML>
<HEAD><TITLE>SEDL intranet - Product Surveys</TITLE>
<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">
<link href=\"/staff/includes/staff2006.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\">
</HEAD>
<BODY BGCOLOR=\"#FFFFFF\">
<table border=\"0\" cellpadding=\"15\" cellspacing=\"0\" width=\"100%\"><tr><td>";
$htmltail = "</td></tr></table>";
}

############################
## START: PRINT PAGE HEADER
############################
print header;
print <<EOM;
<HTML>
<HEAD><TITLE>SEDL intranet - Product Surveys</TITLE>
$htmlhead
EOM
#<p class="alert">This report is being redesigned (work started of February 19, 2010) and reports will not display properly. 
#The reports will be available again by 2/26/2010. Contact brian Litke at ext. 6529 if you have questions.
#</p>
############################
## END: PRINT PAGE HEADER
############################

#################################################################################
## START: LOCATION = ADD STAFF COMMENTS
#################################################################################
if ($location eq 'add_staffcomments') {

	# ADD COMENTS TO DB
	$new_staff_comments = &cleanthisfordb($new_staff_comments);
	$new_staff_comments_by = &cleanthisfordb($new_staff_comments_by);
	my $command = "UPDATE clientsurvey SET staff_comments ='$new_staff_comments', staff_comments_by ='$new_staff_comments_by', staff_comments_date ='$date_full_mysql' WHERE recordid='$surveyid'";
	print "$command" if $debug;
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;

	# SET NEW LOCATION
	$location = "showdata";
}
#################################################################################
## END: LOCATION = ADD STAFF COMMENTS
#################################################################################

#################################################################################
## START: LOCATION = ABOUT
#################################################################################
if ($location eq 'about') {

print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN=TOP>
		<h1>Product Survey Report System</h1>
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN=TOP NOWRAP>
		<strong>Navigation options:</strong><BR>
		- <em><FONT COLOR="RED">about the PDF Client Surveys</FONT></em><BR>
		- <A HREF="clientsurveys.cgi?location=summary_countdate">summary: surveys by date and department</A><BR>
		- <A HREF="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</A> (catalog items only)<BR>
		- <A HREF="clientsurveys.cgi?location=maintenance">maintenance menu</A><BR>
		- <A HREF="clientsurveys.cgi?location=datadump">download data to a file</A>
	</TD></TR>
</TABLE>
<H2>About the PDF Client Surveys</H2>
<P>
<strong>How do we survey clients about the resources they access on the SEDL Web site?</strong><BR>
Surveying clients about online resources is difficult, because we are unable 
to track which users accessed which online documents.  However, OIC has created a process 
for prompting site visitors to give us their e-mail address 
before accessing a PDF document. Although entering the e-mail address is not required to access the PDF file, 
if the user enters their e-mail address, it is saved to a database that will automatically send that 
person an e-mail after one week has passed. The e-mail will include a link back to our site where the 
user can fill out a survey about the publication they viewed.  
<P>
<strong>What information is collected in the survey?</strong><BR>
The <A HREF="/survey/pubs.cgi">survey questions</A> were devised by SEDL's Evaluation Services (ES). This database allows you to see 
  <UL>
  <LI>a list of e-mail addresses volunteered by viewers of each publication, 
  <LI>the ratio of how many surveys have been sent out vs. how many have been completed, and
  <LI>the user's responses to survey questions.
  </UL>
<P>
<strong>How can I find the document I am looking for?</strong><BR>
You can use the search interface to view surveys from a specific department or date range. You may also 
click through the list of documents by title to find the document. There are two types of PDF files we survey about:
	<UL>
	<LI><strong>PDFs representing publications in the SEDL Product Catalog:</strong> If you know the name of your publication, just click the 
		link "list all surveys by title (catalog items only)" in the top right corner of this page.</LI>
<P>		
	<LI><strong>Other PDFs</strong><BR>
	Other PDFs may also have survey data available; although typically we do not send survey invitations for 
	publications not in the SELDL Catalog. However, some of these items have collected e-mail addresses, even 
	though the user was never sent a survey invitation. This allows staff to identify those e-mail addresses so they can be contacted separately.</LI>
	</UL>
<P>
<strong>How do I view  a user's response?</strong><BR>
To view a user's actual responses, find the document by searching or by exploring 
one of the lists. You can view individual survey responses or view the combined results of a survey, which shows 
the distribution of responses for scaled answers and a collection of text responses for text-based answers
EOM
}#################################################################################
## END: LOCATION = ABOUT
#################################################################################


#################################################################################
## START: LOCATION = SUMMARY_COUNTDATE  (BL: OPTIMIZED DB QUERY 4/15/2006)
################################################################################# 
if ($location eq 'summary_countdate') {

	#############################################
	## START: PRINT PAGE HEADER
	#############################################

print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN=TOP>
		<h1>Product Survey Report System</h1>
		<p>Welcome to the summary table summarizing surveys sent and received.  
		You can search for survey data <strong>by date</strong> and 
		<strong>by SEDL department</strong>.</p>
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN=TOP NOWRAP>
		<strong>Navigation options:</strong><BR>
		- <A HREF="clientsurveys.cgi?location=about">about the PDF Client Surveys</A><BR>
		- <FONT COLOR=RED><em>summary: surveys by date and department</em></FONT><BR>
		- <A HREF="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</A> (catalog items only)<BR>
		- <A HREF="clientsurveys.cgi?location=datadump">download data to a file</A>
	</TD></TR>
</TABLE>
<form action="clientsurveys.cgi" method=POST>

Show surveys from after
	<SELECT NAME="search_month">
EOM
&print_month_menu($search_month); ## SUBROUTINE print_month_menu - send a previous value in () if any
print<<EOM;
	</SELECT>
	<SELECT NAME="search_date">
EOM
&print_day_menu($search_date); ## SUBROUTINE print_day_menu - send a previous value in () if any
print<<EOM;
	</SELECT>
	<SELECT NAME="search_year">
EOM
&print_year_menu(2002, 0, $search_year); ## SUBROUTINE print_year_menu - send a previous value in () if any
print<<EOM;
	</SELECT><BR>

Show PDFs from <SELECT NAME="summarysite">
EOM
&print_summary_site_menu($summarysite); ## SUBROUTINE print_year_menu - send a previous value in () if any
print<<EOM;
	</SELECT><BR>
Show surveys completed using the e-mail address (you may enter a partial value, like "sedl.org"): <input type="text" size="20" name="show_address" value="$show_address"><BR>
	<input type="hidden" name="show_followuprequests" value="$show_followuprequests">
	<input type="hidden" name="show_usecomments" value="$show_usecomments">

	<input type="hidden" name="location" value="summary_countdate">
	<input type="submit" name="submit" value="Refresh Display">
	</form>
EOM
	#############################################
	## END: PRINT PAGE HEADER
	#############################################

	#########################################################################
	## START: QUERY 'CLIENTSURVEY' DATABASE AND COMPUTE SUMMARY VALUES
	#########################################################################
	my $counttotalrecords = "0";
	my $countsent = "0";
	my $countsentnobounce = "0"; # represents countsent - countbounced
	my $countreplied = "0";
	my $countnotsent = "0";
	my $countbounced = "0";
	my $countpending = "0";

	my $count_req_followup = "0";
	my $count_perm_use_comments = "0";
	my $count_staff_followup = "0";

	my $ratio = "0";
	my $count_unique_docs = "0";
	my $lastdocumenturl = "";
	my %count_byproject;

	my $command_count_unique_email = "select email from clientsurvey GROUP BY email";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_count_unique_email) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_unique_email = $sth->rows;

	
	##################################
	## START: COUNT HITS BY QUARTER
	##################################
	my $num_matches_unique_email_completed = "";;
	my %count_by_quarter;

	my $command_count_quarter_totals = "select surveyreceived, email
										from clientsurvey 
										WHERE surveyreceived NOT LIKE '0000-00-00'";
    	$command_count_quarter_totals .= " AND documenturl like '%afterschool%'" if ($summarysite eq 'afterschool');
	   	$command_count_quarter_totals .= " AND documenturl like '%connections%'" if ($summarysite eq 'connections');
    	$command_count_quarter_totals .= " AND documenturl like '%/es/%' " if ($summarysite eq '/es/');
    	$command_count_quarter_totals .= " AND documenturl like '%loteced%' " if ($summarysite eq 'loteced');
    	$command_count_quarter_totals .= " AND documenturl like '%ncddr%' " if ($summarysite eq 'ncddr');
    	$command_count_quarter_totals .= " AND documenturl like '%orc\/rr%' " if ($summarysite eq 'rapidresponses');
    	$command_count_quarter_totals .= " AND (documenturl like '%scimast%' OR documenturl like '%scimath%') " if ($summarysite eq 'scimast');
     	$command_count_quarter_totals .= " AND documenturl like '%reading%' " if ($summarysite eq 'reading');
    	$command_count_quarter_totals .= " AND documenturl like '%sedl%' " if ($summarysite eq 'sedl');

    	$command_count_quarter_totals .= " AND ((documenturl like '%sedl\-letter%') OR (documenturl like '%sedletter%')) " if ($summarysite eq 'sedlletter');
    	$command_count_quarter_totals .= " AND documenturl like '%insights%' " if ($summarysite eq 'insights');
    	$command_count_quarter_totals .= " AND documenturl like '%compass%' " if ($summarysite eq 'compass');
    	$command_count_quarter_totals .= " AND documenturl like '%change/issues%' " if ($summarysite eq 'change');
										
		$command_count_quarter_totals .= " order by email";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_count_quarter_totals) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $last_email = "";
		while (my @arr = $sth->fetchrow) {
			my ($surveyreceived, $email) = @arr;
			my $quarter_label = &compute_quarter($surveyreceived);
			$count_by_quarter{$quarter_label}++;
			$num_matches_unique_email_completed++ if ($last_email ne $email);
			$last_email = $email;
		} # END DB QUERY LOOP
	##################################
	## END: COUNT HITS BY QUARTER
	##################################
	
	my $command_summary = "select surveysent, surveysenttwice, surveyreceived, documenturl, documentid, documentgroup, q1a, request_followup, permission_use_comments, staff_comments_date 
		from clientsurvey";
    	$command_summary .= " WHERE date > '$search_dateafter'";
		$command_summary .= " AND documenturl LIKE '$showpdf'" if ($showpdf ne '');
		$command_summary .= " AND email LIKE '%$show_address%'" if ($show_address ne '');
		$command_summary .= " AND surveyreceived NOT LIKE '0000-00-00'" if ($show_responses_only eq 'yes');
		$command_summary .= " AND request_followup LIKE '%request%'" if ($show_followuprequests ne '');
    	$command_summary .= " AND documenturl like '%afterschool%'" if ($summarysite eq 'afterschool');
	   	$command_summary .= " AND documenturl like '%connections%'" if ($summarysite eq 'connections');
    	$command_summary .= " AND documenturl like '%/es/%' " if ($summarysite eq '/es/');
    	$command_summary .= " AND documenturl like '%loteced%' " if ($summarysite eq 'loteced');
    	$command_summary .= " AND documenturl like '%ncddr%' " if ($summarysite eq 'ncddr');
   		$command_summary .= " AND documenturl like '%orc\/rr%' " if ($summarysite eq 'rapidresponses');
    	$command_summary .= " AND (documenturl like '%scimast%' OR documenturl like '%scimath%') " if ($summarysite eq 'scimast');
     	$command_summary .= " AND documenturl like '%reading%' " if ($summarysite eq 'reading');
    	$command_summary .= " AND documenturl like '%sedl%' " if ($summarysite eq 'sedl');

    	$command_summary .= " AND ((documenturl like '%sedl\-letter%') OR (documenturl like '%sedletter%')) " if ($summarysite eq 'sedlletter');
    	$command_summary .= " AND documenturl like '%insights%' " if ($summarysite eq 'insights');
    	$command_summary .= " AND documenturl like '%compass%' " if ($summarysite eq 'compass');
    	$command_summary .= " AND documenturl like '%change/issues%' " if ($summarysite eq 'change');

    	$command_summary .= " order by documenturl";
# print "<P>COMMAND: $command_summary <P>";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_summary) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_ubertotal = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($surveysent, $surveysenttwice, $surveyreceived, $documenturl, $documentid, $documentgroup, $q1a, $request_followup, $permission_use_comments, $staff_comments_date) = @arr;

			if (($surveysent ne 'nosend') && ($surveysent ne 'bounced') && ($surveysent ne 'no')) {
				$count_byproject{'afterschool'}++ if ($documenturl =~ 'afterschool');
				$count_byproject{'connections'}++ if ($documenturl =~ 'connections');
				$count_byproject{'es'}++ if ($documenturl =~ '/es/');
				$count_byproject{'loteced'}++ if ($documenturl =~ 'loteced');
				$count_byproject{'ncddr'}++ if ($documenturl =~ 'ncddr');
				$count_byproject{'rapidresponses'}++ if ($documenturl =~ 'orc\/rr');
				$count_byproject{'scimast'}++ if (($documenturl =~ 'scimath') || ($documenturl =~ 'scimast'));
				$count_byproject{'reading'}++ if ($documenturl =~ 'reading');
				$count_byproject{'sedl'}++ if ($documenturl =~ 'sedl');
	
				$count_byproject{'sedlletter'}++ if (($documenturl =~ 'sedletter') || ($documenturl =~ 'sedl-letter'));
				$count_byproject{'insights'}++ if ($documenturl =~ 'insights');
				$count_byproject{'compass'}++ if ($documenturl =~ 'compass');
				$count_byproject{'change'}++ if ($documenturl =~ 'change\/issues');
				
				if ($surveyreceived !~ '0000') {
					$count_byproject{'rcvd_afterschool'}++ if ($documenturl =~ 'afterschool');
					$count_byproject{'rcvd_connections'}++ if ($documenturl =~ 'connections');
					$count_byproject{'rcvd_es'}++ if ($documenturl =~ '/es/');
					$count_byproject{'rcvd_loteced'}++ if ($documenturl =~ 'loteced');
					$count_byproject{'rcvd_ncddr'}++ if ($documenturl =~ 'ncddr');
					$count_byproject{'rcvd_scimast'}++ if (($documenturl =~ 'scimath') || ($documenturl =~ 'scimast'));
					$count_byproject{'rcvd_reading'}++ if ($documenturl =~ 'reading');
					$count_byproject{'rcvd_sedl'}++ if ($documenturl =~ 'sedl');
	
					$count_byproject{'rcvd_sedlletter'}++ if (($documenturl =~ 'sedletter') || ($documenturl =~ 'sedl-letter'));
					$count_byproject{'rcvd_insights'}++ if ($documenturl =~ 'insights');
					$count_byproject{'rcvd_compass'}++ if ($documenturl =~ 'compass');
					$count_byproject{'rcvd_change'}++ if ($documenturl =~ 'change\/issues');
				} # END IF


			} # END IF
	
			## IF NEXT DOCUMENT< PRINT LAST DOCUMENT'S INFORMATION
			if ($documenturl ne $lastdocumenturl) {
#				## START: LOOK UP DOCUMENT TITLE
#				my $documenttitle = $documenttitle{$lastdocumenturl};
#				my $catalogpage = $catalogpage{$lastdocumenturl};
#				my $pdfsearchstring = "$documenturl";
#	   				$pdfsearchstring =~ s/http\:\/\/www\.sedl\.org//g;
#
#				if (($countsent ne '0') && ($counttotalrecords ne '0')) {
#	print<<EOM;
#	<TABLE BORDER=1>
#	<TR><TD>Total Sent</TD><TD>$countsent</TD></TR>
#	<TR><TD>Total Valid Sendings<BR>(returned mail not included)</TD><TD>$countsentnobounce</TD></TR>
#	<TR><TD>Replied</TD><TD>$countreplied</TD></TR>
#	<TR><TD>Ratio of responses to valid sendings</TD><TD>$countreplied\/$countsentnobounce \= $ratio\%</TD></TR>
#	<TR><TD><FONT COLOR=GRAY>New Downloads - Surveys not yet sent</FONT></TD><TD><FONT COLOR=GRAY>$countpending</FONT></TD></TR>
#	</TABLE>
#EOM
#				} else {
#					if ($counttotalrecords ne '0') {
#						print "No surveys have been sent for this document yet.";
#					} # END IF
#
#				} # END IF/ELSE
#
#print<<EOM;
#</OL>
#	<A HREF="clientsurveys.cgi?location=listdocuments&showpdf=$documenturl" target=viewlist>
#	<IMG SRC=\"images/surveyview.gif\" ALT="View list of e-mail addresses" ALIGN=RIGHT BORDER=0></A>
#	<strong class=small><FONT COLOR=996600>$documenturl</FONT></strong><BR>
#	<SPAN class=small>($documenttitle)</SPAN>
#<OL>
#EOM
#				$countpending = "0";
#				$countsent = "0";
#				$countreplied = "0";
#				$countnotsent = "0";
#				$countbounced = "0";
#				$ratio = "0";
				$count_unique_docs++;
			} # END IF DOCURL NOT LASTDOCURL

			$countpending++ if ($surveysent eq 'no');
			$countsent++ if ($surveysent ne 'no');
			$countreplied++ if ($q1a ne '');
			$countnotsent++ if ($surveysent eq 'nosend');
			$countbounced++ if ($surveysent eq 'bounced');
			
			$count_req_followup++ if ($request_followup =~ 'user requests');
			$count_perm_use_comments++ if ($permission_use_comments eq 'yes');
			$count_staff_followup++ if ($staff_comments_date =~ '20');

			$counttotalrecords++;
			$lastdocumenturl = $documenturl;

		} # END DB QUERY LOOP

		$countsentnobounce = $countsent - $countbounced;

		# START: COMPUTE SUMMARY TOTALS
		$ratio = $countreplied/$countsentnobounce if (($countsentnobounce ne '0') && ($countreplied ne '0'));
		$ratio = $ratio * 100 if ($ratio ne '0');
		$ratio = &format_number($ratio, "2","no"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (yes or blank)

		#$ratio = split(/\./, $ratio);
		# END: COMPUTE SUMMARY TOTALS

$count_byproject{'afterschool'} = &format_number($count_byproject{'afterschool'}, "0","yes");
$count_byproject{'loteced'} = &format_number($count_byproject{'loteced'}, "0","yes");
$count_byproject{'ncddr'} = &format_number($count_byproject{'ncddr'}, "0","yes");
$count_byproject{'reading'} = &format_number($count_byproject{'reading'}, "0","yes");
$count_byproject{'connections'} = &format_number($count_byproject{'connections'}, "0","yes");
$count_byproject{'scimast'} = &format_number($count_byproject{'scimast'}, "0","yes");
$count_byproject{'sedl'} = &format_number($count_byproject{'sedl'}, "0","yes");

$count_byproject{'compass'} = &format_number($count_byproject{'compass'}, "0","yes");
$count_byproject{'insights'} = &format_number($count_byproject{'insights'}, "0","yes");
$count_byproject{'issues'} = &format_number($count_byproject{'issues'}, "0","yes");
$count_byproject{'sedlletter'} = &format_number($count_byproject{'sedlletter'}, "0","yes");

$num_matches_ubertotal = &format_number($num_matches_ubertotal, "0","yes");

$num_matches_unique_email = &format_number($num_matches_unique_email, "0","yes");
$countsent = &format_number($countsent, "0","yes");
$num_matches_unique_email_completed = &format_number($num_matches_unique_email_completed, "0","yes");
## START: PRINT THE SUMMARY TABLE
	print<<EOM;
<H2>Completed Surveys by Quarter</H2>
<p>
<table cellpadding="0" cellspacing="0" border="1" bgcolor="#ffffff">
<tr>
EOM
my $key;
my $data_row = "";
    foreach $key (sort (keys(%count_by_quarter))) {
		my ($t_year, $t_quarter) = split(/\-/,$key);
		   $t_quarter =~ s/0//gi;
		   $t_quarter = "Q$t_quarter";
		my $height_ratio = ($count_by_quarter{$key} / 3);
		$height_ratio = &format_number($height_ratio, "0","no");
		my $bar_color = "purple";
		   $bar_color = "purple" if (($t_year eq 2003) || ($t_year eq 2007));
		   $bar_color = "blue" if (($t_year eq 2004) || ($t_year eq 2008));
		   $bar_color = "green" if (($t_year eq 2005) || ($t_year eq 2009));
		   $bar_color = "red" if (($t_year eq 2002) || ($t_year eq 2006) || ($t_year eq 2010));;

		print "<td valign=\"bottom\" align=\"center\"><IMG SRC=\"/images/pixel-$bar_color\.gif\" height=\"$height_ratio\" width=\"15\" alt=\"$count_by_quarter{$key} in $t_quarter\/$t_year\" title=\"$count_by_quarter{$key} in $t_quarter\/$t_year\"></td>";
#       $data_row .= "<td align=\"center\" style=\"font-size: 10px\">$count_by_quarter{$key}<br>$t_quarter\/<br>$t_year</td>";
    }
#<tr>$data_row</tr>

print<<EOM;
</tr>
<tr style="text-align:center;"><td colspan="3">2002</td>
	<td colspan="4">2003</td>
	<td colspan="4">2004</td>
	<td colspan="4">2005</td>
	<td colspan="4">2006</td>
	<td colspan="4">2007</td>
	<td colspan="4">2008</td>
	<td colspan="4">2009</td>
	<td colspan="4">2010</td>
</tr>
</table><br>
* Note: The product survey was malfunctioning in (Q4 of 2004) and (from 2/22/2008 - 5/13/2008), accounting for the low number of surveys received.

<H2>Summary Report</H2>
<P>
There are $num_matches_unique_email unique e-mail addresses on file, $num_matches_unique_email_completed of which have completed a survey.
<P>
<TABLE BORDER=1 CELLPADDING="2" CELLSPACING=0 BGCOLOR="#FFFFFF">
<TR><TD BGCOLOR="#EBEBEB"><em>Summary Type</em></TD>
	<TD BGCOLOR="#EBEBEB"><em>Count</em></TD></TR>
	<TR><TD># Documents Surveyed</TD><TD><A HREF="/staff/quality/clientsurveys.cgi?location=listdocumentsbytitle&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_address=$show_address">$count_unique_docs</A> (click for a list of document titles and survey stats)</TD></TR>
	<TR><TD># Survey Invitations Sent</TD><TD><A HREF="/staff/quality/clientsurveys.cgi?location=listsurveys&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_address=$show_address">$countsent</A></TD></TR>
	<TR><TD># Valid Sendings</TD><TD>$countsentnobounce  ($countbounced bounced e-mails not included)</TD></TR>
	<TR><TD># Survey Responses Received</TD><TD><A HREF="/staff/quality/clientsurveys.cgi?location=listsurveys&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_responses_only=yes&show_address=$show_address">$countreplied</A></TD></TR>
	<TR><TD>Response Ratio</TD><TD>$ratio\% ($countreplied\/$countsentnobounce)</TD></TR>
	<TR><TD># followup requests</TD><TD><A HREF="/staff/quality/clientsurveys.cgi?location=listsurveys&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_followuprequests=yes&show_address=$show_address">$count_req_followup</A></TD></TR>
	<TR><TD># staff followups recorded</TD><TD>$count_staff_followup</TD></TR>
	<TR><TD># permission to use comments</TD><TD><A HREF="/staff/quality/clientsurveys.cgi?location=listsurveys&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_usecomments=yes&show_address=$show_address">$count_perm_use_comments</A></TD></TR>
	<TR><TD><FONT COLOR=GRAY>New Viewings - Surveys not yet sent</FONT></TD><TD><FONT COLOR=GRAY>$countpending (Survey invitation is sent 7 days after product access)</FONT></TD></TR>
	</TABLE>
<P>
<H2>Surveys by department</H2>
<P>
<TABLE BORDER=1 CELLPADDING=4 CELLSPACING=0 BGCOLOR="#FFFFFF">
<TR><TD BGCOLOR="#EBEBEB"><em>Web Site</em></TD>
	<TD BGCOLOR="#EBEBEB"><em># Surveys Received/Sent</em></TD>
	<TD BGCOLOR="#EBEBEB"><em>Publications in SEDL Catalog</em></TD>
	<TD BGCOLOR="#EBEBEB"><em>any PDF</em></TD></TR>
<TR><TD>AFTERSCHOOL</TD>
	<TD ALIGN="RIGHT">$count_byproject{'rcvd_afterschool'} / $count_byproject{'afterschool'}</TD>
	<TD><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&amp;summarysite=afterschool&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">Catalog items</A></TD>
	<TD><A HREF="clientsurveys.cgi?location=listdocumentsbyurl&amp;summarysite=afterschool&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">any PDF</A></TD></TR>
<TR><TD>LOTECED</TD>
	<TD ALIGN="RIGHT">$count_byproject{'rcvd_loteced'} / $count_byproject{'loteced'}</TD>
	<TD><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&amp;summarysite=loteced&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">Catalog items</A></TD>
	<TD><A HREF="clientsurveys.cgi?location=listdocumentsbyurl&amp;summarysite=loteced&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">any PDF</A></TD></TR>
<TR><TD>Reading Resources</TD>
	<TD ALIGN="RIGHT">$count_byproject{'rcvd_reading'} / $count_byproject{'reading'}</TD>
	<TD><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&amp;summarysite=reading&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">Catalog items</A></TD>
	<TD><A HREF="clientsurveys.cgi?location=listdocumentsbyurl&amp;summarysite=reading&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">any PDF</A></TD></TR>
<TR><TD>National Center for Family<BR>and Community Connections site</TD>
	<TD ALIGN="RIGHT">$count_byproject{'rcvd_connections'} / $count_byproject{'connections'}</TD>
	<TD><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&amp;summarysite=connections&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">Catalog items</A></TD>
	<TD><A HREF="clientsurveys.cgi?location=listdocumentsbyurl&amp;summarysite=connections&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">any PDF</A></TD></TR>
<TR><TD>SCIMAST</TD>
	<TD ALIGN="RIGHT">$count_byproject{'rcvd_scimast'} / $count_byproject{'scimast'}</TD>
	<TD><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&amp;summarysite=scimast&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">Catalog items</A></TD>
	<TD><A HREF="clientsurveys.cgi?location=listdocumentsbyurl&amp;summarysite=scimast&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">any PDF</A></TD></TR>

<TR><TD BGCOLOR="#EBEBEB">All PDFs on www.sedl.org</TD>
	<TD BGCOLOR="#EBEBEB" ALIGN="RIGHT">$count_byproject{'rcvd_sedl'} / $count_byproject{'sedl'}</TD>
	<TD BGCOLOR="#EBEBEB"><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&amp;summarysite=sedl&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">Catalog items</A></TD>
	<TD BGCOLOR="#EBEBEB"><A HREF="clientsurveys.cgi?location=listdocumentsbyurl&amp;summarysite=sedl&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">any PDF</A></TD></TR>
<TR><TD BGCOLOR="#EBEBEB">All PDFs on any Web server run by SEDL</TD>
	<TD BGCOLOR="#EBEBEB" ALIGN="RIGHT">$num_matches_ubertotal</TD>
	<TD BGCOLOR="#EBEBEB"><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&amp;summarysite=all&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">Catalog items</A></TD>
	<TD BGCOLOR="#EBEBEB"><A HREF="clientsurveys.cgi?location=listdocumentsbyurl&amp;summarysite=%&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">any PDF</A></TD></TR>
</TABLE>
<P>
<H2>Surveys for Serial Publications</H2>
<P>
<TABLE BORDER=1 CELLPADDING=4 CELLSPACING=0 BGCOLOR="#FFFFFF">
<TR><TD BGCOLOR="#EBEBEB"><em>Serial Publications Name</em></TD>
	<TD BGCOLOR="#EBEBEB"><em># Surveys</em></TD>
	<TD BGCOLOR="#EBEBEB" COLSPAN=2><em>Publications in SEDL Catalog</em></TD></TR>
<TR><TD><em>Classroom Compass</em></TD>
	<TD ALIGN="RIGHT">$count_byproject{'compass'}</TD>
	<TD COLSPAN=2><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&summarysite=compass&search_date=$search_date&search_month=$search_month&search_year=$search_year">list this serial pub's editions</A></TD></TR>
<TR><TD><em>Insights into Education Policy...</em></TD>
	<TD ALIGN="RIGHT">$count_byproject{'insights'}</TD>
	<TD COLSPAN=2><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&summarysite=insights&search_date=$search_date&search_month=$search_month&search_year=$search_year">list this serial pub's editions</A></TD></TR>
<TR><TD><em>Issues about Change...</em></TD>
	<TD ALIGN="RIGHT">$count_byproject{'change'}</TD>
	<TD COLSPAN=2><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&summarysite=change&search_date=$search_date&search_month=$search_month&search_year=$search_year">list this serial pub's editions</A></TD></TR>
<TR><TD><em>SECC Rapid Responses</em></TD>
	<TD ALIGN="RIGHT">$count_byproject{'rapidresponses'}</TD>
	<TD COLSPAN=2><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&summarysite=rapidresponses&search_date=$search_date&search_month=$search_month&search_year=$search_year">list this serial pub's editions</A></TD></TR>
<TR><TD><em>SEDL Letter</em></TD>
	<TD ALIGN="RIGHT">$count_byproject{'sedlletter'}</TD>
	<TD COLSPAN=2><A HREF="clientsurveys.cgi?location=listdocumentsbytitle&summarysite=sedlletter&search_date=$search_date&search_month=$search_month&search_year=$search_year">list this serial pub's editions</A></TD></TR>
</TABLE>
<P>
<P><IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/pdf-summary-pie.cgi?countbounced=$countbounced&countsentnobounce=$countsentnobounce&countreplied=$countreplied&countpending=$countpending" ALT="Pie Chart Showing Survey Sent/Responded">

EOM
## END: PRINT THE SUMMARY TABLE

}  
#################################################################################
## END: LOCATION = SUMMARY_COUNTDATE
#################################################################################

#################################################################################
## START: LOCATION = SUMMARY
#################################################################################
if ($location eq 'summary') {
	## PRINT PAGE HEADER
print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN=TOP>
		<h1>Product Survey Report System</h1>
		<p>Action: Survey sent/return ratios<BR>(documents are listed in URL order)</p>
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN=TOP NOWRAP>
		<strong>Navigation options:</strong><BR>
		- <A HREF="clientsurveys.cgi?location=about">about the PDF Client Surveys</A><BR>
		- <A HREF="clientsurveys.cgi?location=summary_countdate">summary: surveys by date and department</A><BR>
		- <em><FONT COLOR="RED">Report: survey sent/return ratios</FONT></em><BR>
		- <A HREF="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</A> (catalog items only)<BR>
		- <A HREF="clientsurveys.cgi?location=datadump">download data to a file</A>
	</TD></TR>
</TABLE>
EOM

	my $counttotalrecords = "0";
	my $countsent = "0";
	my $countsentnobounce = "0"; # represents countsent - countbounced
	my $countreplied = "0";
	my $countnotsent = "0";
	my $countbounced = "0";
	my $countpending = "0";
	my $ratio = "0";

	my $lastdocumenturl = "";

	my $command = "select * from clientsurvey";

    	$command .= " where documenturl like '%afterschool%'" if ($summarysite eq 'afterschool');
	   	$command .= " where documenturl like '%connections%'" if ($summarysite eq 'connections');
    	$command .= " where documenturl like '%/es/%' " if ($summarysite eq '/es/');
    	$command .= " where documenturl like '%loteced%' " if ($summarysite eq 'loteced');
    	$command .= " where documenturl like '%ncddr%' " if ($summarysite eq 'ncddr');
   		$command .= " AND documenturl like '%orc\/rr%' " if ($summarysite eq 'rapidresponses');
    	$command .= " where (documenturl like '%scimast%' OR documenturl like '%scimath%') " if ($summarysite eq 'scimast');
     	$command .= " where documenturl like '%reading%' " if ($summarysite eq 'reading');
    	$command .= " where documenturl like '%sedl%' " if ($summarysite eq 'sedl');

    	$command .= " where ((documenturl like '%sedl\-letter%') OR (documenturl like '%sedletter%')) " if ($summarysite eq 'sedlletter');
    	$command .= " where documenturl like '%insights%' " if ($summarysite eq 'insights');
    	$command .= " where documenturl like '%compass%' " if ($summarysite eq 'compass');
    	$command .= " where documenturl like '%change/issues%' " if ($summarysite eq 'change');
		$command .= " where documenturl like '%'" if ($summarysite eq 'all');


    	$command .= " order by documenturl, $sortby";
	
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	while (my @arr = $sth->fetchrow) {
		my ($recordid, $surveysent, $surveysenttwice, $surveyreceived, $email, $date, $documenturl, $documentid, $documentgroup, $q1a, $q1b, $q1c, $q1d, $q2a, $q2b, $q3a, $q3b, $q3c, $q3d, $q3e, $q4a, $q5a, $q6a, $q7a, $q8a, $q9a, $q10a, $ipnum, $browser, $request_followup, $permission_use_comments, $name, $title, $staff_comments, $staff_comments_by, $staff_comments_date) = @arr;

		my $documenttitle = "<em>document not in SEDL Catalog</em>";
		my $catalogpage = "";


		if ($documenturl ne $lastdocumenturl) {
			#############################################
			## START: DB QUERY TO GRAB THE DOCUMENT NAME
			#############################################
			my $pdfsearchstring = "$documenturl";
			   $pdfsearchstring =~ s/http\:\/\/www\.sedl\.org//g;
   		
			my $command = "select onlineid, title, title2 from sedlcatalog where ((locpdf LIKE '%$pdfsearchstring') OR (lochtml LIKE '%$pdfsearchstring')) order by title";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
				while (my @arr = $sth->fetchrow) {
					my ($onlineid, $title, $title2) = @arr;
					$documenttitle = "$title";
					$documenttitle .= ": $title2" if $title2 ne '';
 					$catalogpage = "/pubs/catalog/items/$onlineid";
				} # END DB QUERY
			#############################################
			## END: DB QUERY TO GRAB THE DOCUMENT NAME
			#############################################
	
			if (($countsent ne '0') && ($counttotalrecords ne '0')) {
print<<EOM;
	<TABLE BORDER=1>
	<TR><TD>Total Sent</TD><TD>$countsent</TD></TR>
	<TR><TD>Total Valid Sendings<BR>(returned mail not included)</TD><TD>$countsentnobounce</TD></TR>
	<TR><TD>Replied</TD><TD>$countreplied</TD></TR>
	<TR><TD>Ratio of responses to valid sendings</TD><TD>$countreplied\/$countsentnobounce \= $ratio\%</TD></TR>
	<TR><TD><FONT COLOR=GRAY>New Viewings - Surveys not yet sent</FONT></TD><TD><FONT COLOR=GRAY>$countpending</FONT></TD></TR>
	</TABLE>
EOM
			} else {
				if ($counttotalrecords ne '0') {
					print "No surveys have been sent for this document yet.";
				} # END IF
	
			} # END IF/ELSE

print<<EOM;
</OL>
	<A HREF="clientsurveys.cgi?location=listsurveys&showpdf=$documenturl" target=viewlist>
	<IMG SRC=\"images/surveyview.gif\" ALT="View list of e-mail addresses" ALIGN=RIGHT BORDER=0></A>
	<strong class=small><FONT COLOR=996600>$documenturl</FONT></strong><BR>
	<SPAN class=small>($documenttitle)</SPAN>
<OL>
EOM
			$countpending = "0";
			$countsent = "0";
			$countreplied = "0";
			$countnotsent = "0";
			$countbounced = "0";
			$ratio = "0";
		} # END IF DOCURL NOT LASTDOCURL


		$countpending++ if ($surveysent eq 'no');
		$countsent++ if ($surveysent ne 'no');
		$countreplied++ if ($q1a ne '');
		$countnotsent++ if ($surveysent eq 'nosend');
		$countbounced++ if ($surveysent eq 'bounced');

		$countsentnobounce = $countsent - $countbounced;

		$ratio = $countreplied/$countsentnobounce if (($countsentnobounce ne '0') && ($countreplied ne '0'));
		$ratio = $ratio * 100 if ($ratio ne '0');
		#$ratio = split(/\./, $ratio);

		$counttotalrecords++;
		$lastdocumenturl = $documenturl;
	} # END DB QUERY LOOP

	###############################
	## START: PRINT THE LAST TABLE
	###############################
	if (($countsent ne '0') && ($counttotalrecords ne '0')) {
	print<<EOM;
	<TABLE BORDER=1>
	<TR><TD>Total Sent</TD><TD>$countsent</TD></TR>
	<TR><TD>Total Valid Sendings<BR>(returned mail not included)</TD><TD>$countsentnobounce</TD></TR>
	<TR><TD>Replied</TD><TD>$countreplied</TD></TR>
	<TR><TD>Ratio of responses to valid sendings</TD><TD>$countreplied\/$countsentnobounce \= $ratio\%</TD></TR>
	<TR><TD><FONT COLOR=GRAY>New Viewings - Surveys not yet sent</FONT></TD><TD><FONT COLOR=GRAY>$countpending</FONT></TD></TR>
	</TABLE>
EOM
	} else {
		if ($counttotalrecords ne '0') {
			print "No surveys have been sent for this document yet. $countpending new surveys will go out soon.";
		} # END IF
	} # END IF/ELSE
	###############################
	## END: PRINT THE LAST TABLE
	###############################

	print "</OL>\n";
}  
#################################################################################
## END: LOCATION = SUMMARY
#################################################################################





#################################################################################
## START: LOCATION = summary_responses_singledoc
#################################################################################
if ($location eq 'summary_responses_singledoc') {
## PRINT PAGE HEADER
print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN=TOP>
		<h1>Product Survey Report System</h1>
		<p>Action: View Survey Data<BR>
		(survey responses are in red)</p> 
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN=TOP NOWRAP>
		<strong>Navigation options:</strong><BR>
		- <A HREF="clientsurveys.cgi?location=about">about the PDF Client Surveys</A><BR>
		- <A HREF="clientsurveys.cgi?location=summary_countdate">summary: surveys by date and department</A><BR>
		- <A HREF="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</A> (catalog items only)<BR>
		- <A HREF="clientsurveys.cgi?location=datadump">download data to a file</A>
	</TD></TR>
</TABLE>
EOM
		my $pdfsearchstring = "$showpdf";
		   $pdfsearchstring =~ s/http\:\/\/www\.sedl\.org//g;
		#############################################
		## START: DB QUERY TO GRAB THE DOCUMENT NAME
		#############################################
		my $documenttitle = "Error - Document not found in SEDL Catalog";
		my $catalogpage = "";

		my $pdfsearchstring = $showpdf;
		   $pdfsearchstring =~ s/http\:\/\/www\.sedl\.org//g;
		my $command = "select title, title2, onlineid from sedlcatalog 
						where ((locpdf LIKE '%$pdfsearchstring') OR (lochtml LIKE '%$pdfsearchstring'))";
		if ($showdocid ne '') {
			$command = "select title, title2, onlineid from sedlcatalog 
						where unique_id = '$showdocid'";
		}
#print "<p class=\"info\">COMMAND: $command</p>";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($title, $title2, $onlineid) = @arr;

			$documenttitle = "$title";
			$documenttitle .= ": $title2" if $title2 ne '';
			$documenttitle = &cleanaccents2html($documenttitle);

			$catalogpage = "/pubs/catalog/items/$onlineid";
		} # END DB QUERY
		#############################################
		## END: DB QUERY TO GRAB THE DOCUMENT NAME
		#############################################

   		if ($pdfsearchstring eq 'allsedlletters') {
			$documenttitle = "All SEDL Letter Editions";
			$catalogpage = "/pubs/magazine/";
  		}

my %q1a;
my %q1b;
my %q1c;
my %q1d;

my %q2a;

my %q3a;
my %q3b;
my %q3c;
my %q3d;

my $q1a_total = "0";
my $q1b_total = "0";
my $q1c_total = "0";
my $q1d_total = "0";

my $q2a_total = "0";

my $q3a_total = "0";
my $q3b_total = "0";
my $q3c_total = "0";
my $q3d_total = "0";

my $num_matches_q3a = "0";
my $num_matches_q3b = "0";
my $num_matches_q3c = "0";
my $num_matches_q3d = "0";

my %q_avg;

my $text_q7a = "";
my $text_q2b = "";
my $text_q3e = "";
my $text_q8a = ""; 
my $text_q9a = "";
my $text_q4a = "";
my $text_q6a = "";
my $text_q5a = "";

my $total_request_followup = "0";
	my $command = "select * from clientsurvey 
					WHERE documenturl LIKE '%$pdfsearchstring' 
					AND date > '$search_dateafter'
					AND surveyreceived NOT LIKE '0000-00-00'";
		if ($showdocid ne '') {
			$command = "select * from clientsurvey 
					WHERE documentid = '$showdocid' 
					AND date > '$search_dateafter'
					AND surveyreceived NOT LIKE '0000-00-00'";
		}

   		if ($pdfsearchstring eq 'allsedlletters') {
	 		$command = "select * from clientsurvey 
					WHERE ((documenturl LIKE '%sedlletter%') OR (documenturl LIKE '%sedl-letter%')) 
					AND surveyreceived NOT LIKE '0000-00-00' AND surveyreceived > '$search_dateafter'";
		} 
			
my $search_dateafter_pretty = $search_dateafter;
   $search_dateafter_pretty = &date2standard($search_dateafter);
print "<p>Showing surveys received after the date: $search_dateafter_pretty</p>";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#print "<p class=\"info\">COMMAND: $command<BR><BR>MATCHES: $num_matches</p>";
	while (my @arr = $sth->fetchrow) {
		my ($recordid, $surveysent, $surveysenttwice, $surveyreceived, $email, $date, $documenturl, $documentid, $documentgroup, 
			$q1a, $q1b, $q1c, $q1d, $q2a, $q2b, $q3a, $q3b, $q3c, $q3d, $q3e, $q4a, $q5a, $q6a, $q7a, $q8a, $q9a, $q10a, 
			$ipnum, $browser, $request_followup, $permission_use_comments, $name, $title, $staff_comments, $staff_comments_by, $staff_comments_date) = @arr;

		$total_request_followup++ if ($request_followup =~ 'request');
		my $permission_flag_bolding = "";
		my $permission_flag = "";
		if ($permission_use_comments ne '') {
			$permission_flag_bolding = "<font color=\"green\">";
			$permission_flag = " * GAVE PERMISSION TO USE FOR MARKETING";
			$permission_flag .= " (<strong>" if (($name ne '') || ($title ne ''));
			$permission_flag .= "Name: $name" if ($name ne '');
			$permission_flag .= " Title: $title" if ($title ne '');
			$permission_flag .= "</strong>)" if (($name ne '') || ($title ne ''));
			$permission_flag .= "</font>";
		} # END IF
		$text_q7a .= "<LI>$q7a [<A HREF=\"/staff/quality/clientsurveys.cgi?location=showdata&surveyid=$recordid\" TARGET=TOP><FONT COLOR=\"#999999\">$recordid</FONT></A>]" if ($q7a ne ''); 
		$text_q2b .= "<LI>$q2b [<A HREF=\"/staff/quality/clientsurveys.cgi?location=showdata&surveyid=$recordid\" TARGET=TOP><FONT COLOR=\"#999999\">$recordid</FONT></A>]" if ($q2b ne ''); 
		$text_q3e .= "<LI>$q3e [<A HREF=\"/staff/quality/clientsurveys.cgi?location=showdata&surveyid=$recordid\" TARGET=TOP><FONT COLOR=\"#999999\">$recordid</FONT></A>]" if ($q3e ne ''); 
		$text_q8a .= "<LI>$q8a [<A HREF=\"/staff/quality/clientsurveys.cgi?location=showdata&surveyid=$recordid\" TARGET=TOP><FONT COLOR=\"#999999\">$recordid</FONT></A>]" if ($q8a ne ''); 
		$text_q9a .= "<LI>$q9a [<A HREF=\"/staff/quality/clientsurveys.cgi?location=showdata&surveyid=$recordid\" TARGET=TOP><FONT COLOR=\"#999999\">$recordid</FONT></A>]" if ($q9a ne ''); 
		$text_q4a .= "<LI>$q4a [<A HREF=\"/staff/quality/clientsurveys.cgi?location=showdata&surveyid=$recordid\" TARGET=TOP><FONT COLOR=\"#999999\">$recordid</FONT></A>]" if ($q4a ne ''); 
		$text_q6a .= "<LI>$permission_flag_bolding$q6a [<A HREF=\"/staff/quality/clientsurveys.cgi?location=showdata&surveyid=$recordid\" TARGET=TOP><FONT COLOR=\"#999999\">$recordid</FONT></A>]$permission_flag" if ($q6a ne ''); 
		$text_q5a .= "<LI>$q5a [<A HREF=\"/staff/quality/clientsurveys.cgi?location=showdata&surveyid=$recordid\" TARGET=TOP><FONT COLOR=\"#999999\">$recordid</FONT></A>]" if ($q5a ne ''); 
	
		# TRACK INDIVIDUAL SCALED ANSWER RESPONSES
		$q1a{$q1a}++;
		$q1b{$q1b}++;
		$q1c{$q1c}++;
		$q1d{$q1d}++;

		$q2a{$q2a}++;

		$q3a{$q3a}++;
		$q3b{$q3b}++;
		$q3c{$q3c}++;
		$q3d{$q3d}++;

		$q1a_total+= $q1a;
		$q1b_total+= $q1b;
		$q1c_total+= $q1c;
		$q1d_total+= $q1d;

		$q2a_total+= $q2a;

		$q3a_total+= $q3a if ($q3a ne '0'); # DON'T INCLUDE IN SUM FOR AVERAGE IF VALUE = 0 (N/A)
		$q3b_total+= $q3b if ($q3b ne '0');
		$q3c_total+= $q3c if ($q3c ne '0');
		$q3d_total+= $q3d if ($q3d ne '0');

		$num_matches_q3a++ if ($q3a ne '0'); # DON'T INCUDE INSTANCE IN TOTAL FOR AVERAGE IF VALUE = 0 (N/A)
		$num_matches_q3b++ if ($q3b ne '0');
		$num_matches_q3c++ if ($q3c ne '0');
		$num_matches_q3d++ if ($q3d ne '0');

	} # END DB QUERY LOOP
	
	if ($num_matches ne '0') {
		$q_avg{'q1a'} = $q1a_total / $num_matches;
		$q_avg{'q1b'} = $q1b_total / $num_matches;
		$q_avg{'q1c'} = $q1c_total / $num_matches;
		$q_avg{'q1d'} = $q1d_total / $num_matches;

		$q_avg{'q2a'} = $q2a_total / $num_matches;
	}

	$q_avg{'q3a'} = $q3a_total / $num_matches_q3a if ($num_matches_q3a ne '0');
	$q_avg{'q3b'} = $q3b_total / $num_matches_q3b if ($num_matches_q3b ne '0');
	$q_avg{'q3c'} = $q3c_total / $num_matches_q3c if ($num_matches_q3c ne '0');
	$q_avg{'q3d'} = $q3d_total / $num_matches_q3d if ($num_matches_q3d ne '0');

	$q_avg{'q1a'} = &format_number($q_avg{'q1a'}, "2", "no");
	$q_avg{'q1b'} = &format_number($q_avg{'q1b'}, "2", "no");
	$q_avg{'q1c'} = &format_number($q_avg{'q1c'}, "2", "no");
	$q_avg{'q1d'} = &format_number($q_avg{'q1d'}, "2", "no");

	$q_avg{'q2a'} = &format_number($q_avg{'q2a'}, "2", "no");
	$q_avg{'q3a'} = &format_number($q_avg{'q3a'}, "2", "no");
	$q_avg{'q3b'} = &format_number($q_avg{'q3b'}, "2", "no");
	$q_avg{'q3c'} = &format_number($q_avg{'q3c'}, "2", "no");
	$q_avg{'q3d'} = &format_number($q_avg{'q3d'}, "2", "no");

## CHANGE SPECIAL TEXT CHARACTERS TO HTML ENTITIES
$documenttitle = &cleanaccents2html($documenttitle);

print<<EOM;
<P>
<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=0 WIDTH=100%><TR><TD>
	Document Viewed: <A HREF="$catalogpage" TARGET=TOP><em>$documenttitle</em></A><BR>
	Surveys on File: $num_matches</TD></TR></TABLE>
<P>
<strong>Q1: How did you hear about this document?</strong> (Question added to survey January 2004)<BR>
<em><FONT COLOR=RED><OL>$text_q7a</OL></FONT></em>
<P>
<strong>Q2: How do you rate the quality of:</strong><BR>
<TABLE BORDER="1" CELLPADDINg="2" CELLSPACING="0" WIDTH="100%">
<TR><TD VALIGN="TOP"><strong>a.</strong></TD>
	<TD VALIGN="TOP">the document overall</TD>
	<TD VALIGN="TOP">$q_avg{'q1a'}</TD>
	<TD VALIGN="TOP"><IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/pdf-5pt-barchart.cgi?v1=$q1a{'1'}&v2=$q1a{'2'}&v3=$q1a{'3'}&v4=$q1a{'4'}&v5=$q1a{'5'}" 
		ALIGN=RIGHT TITLE="Responses: One = $q1a{'1'}, Two = $q1a{'2'}, Three = $q1a{'3'}, Four = $q1a{'4'}, Five = $q1a{'5'}"></TD></TR>
<TR><TD VALIGN="TOP"><strong>b.</strong></TD>
	<TD VALIGN="TOP">the organization of the document</TD>
	<TD VALIGN="TOP">$q_avg{'q1b'}</TD>
	<TD VALIGN="TOP"><IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/pdf-5pt-barchart.cgi?v1=$q1b{'1'}&v2=$q1b{'2'}&v3=$q1b{'3'}&v4=$q1b{'4'}&v5=$q1b{'5'}"
		ALIGN=RIGHT TITLE="Responses: One = $q1b{'1'}, Two = $q1b{'2'}, Three = $q1b{'3'}, Four = $q1b{'4'}, Five = $q1b{'5'}"></TD></TR>
<TR><TD VALIGN="TOP"><strong>c.</strong></TD>
	<TD VALIGN="TOP">the timeliness of the document</TD>
	<TD VALIGN="TOP">$q_avg{'q1c'}</TD>
	<TD VALIGN="TOP"><IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/pdf-5pt-barchart.cgi?v1=$q1c{'1'}&v2=$q1c{'2'}&v3=$q1c{'3'}&v4=$q1c{'4'}&v5=$q1c{'5'}"
		ALIGN=RIGHT TITLE="Responses: One = $q1c{'1'}, Two = $q1c{'2'}, Three = $q1c{'3'}, Four = $q1c{'4'}, Five = $q1c{'5'}"></TD></TR>
<TR><TD VALIGN="TOP"><strong>d.</strong></TD>
	<TD VALIGN="TOP">the presentation of the document</TD>
	<TD VALIGN="TOP">$q_avg{'q1d'}</TD>
	<TD VALIGN="TOP"><IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/pdf-5pt-barchart.cgi?v1=$q1d{'1'}&v2=$q1d{'2'}&v3=$q1d{'3'}&v4=$q1d{'4'}&v5=$q1d{'5'}"
		ALIGN=RIGHT TITLE="Responses: One = $q1d{'1'}, Two = $q1d{'2'}, Three = $q1d{'3'}, Four = $q1d{'4'}, Five = $q1d{'5'}"></TD></TR>
</TABLE>		
<P>
<strong>Q3: How do you rate the document:</strong>
<TABLE BORDER="1" CELLPADDINg="2" CELLSPACING="0" WIDTH="100%">
<TR><TD VALIGN="TOP"><strong>a.</strong></TD>
	<TD VALIGN="TOP">for meeting your needs?</TD>
	<TD VALIGN="TOP">$q_avg{'q1a'}</TD>
	<TD VALIGN="TOP"><IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/pdf-5pt-barchart.cgi?v1=$q2a{'1'}&v2=$q2a{'2'}&v3=$q2a{'3'}&v4=$q2a{'4'}&v5=$q2a{'5'}"
		ALIGN=RIGHT TITLE="Responses: One = $q2a{'1'}, Two = $q2a{'2'}, Three = $q2a{'3'}, Four = $q2a{'4'}, Five = $q2a{'5'}"></TD></TR>
</TABLE>
<P>
<em>Comments:</em><BR>
<em><FONT COLOR=RED><OL>$text_q2b</OL></FONT></em>
<P>
<strong>Q4: Indicate the extent to which the document has had the following impact(s):</strong><BR>

<TABLE BORDER="1" CELLPADDINg="2" CELLSPACING="0" WIDTH="100%">
<TR><TD VALIGN="TOP"><strong>a.</strong></TD>
	<TD VALIGN="TOP">Increased your awareness of important new skills and knowledge.</TD>
	<TD VALIGN="TOP">$q_avg{'q3a'}</TD>
	<TD VALIGN="TOP"><IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/pdf-6pt-barchart.cgi?v1=$q3a{'1'}&v2=$q3a{'2'}&v3=$q3a{'3'}&v4=$q3a{'4'}&v5=$q3a{'5'}&v6=$q3a{'0'}"
		ALIGN=RIGHT TITLE="Responses: One = $q3a{'1'}, Two = $q3a{'2'}, Three = $q3a{'3'}, Four = $q3a{'4'}, Five = $q3a{'5'}, N/A = $q3a{'0'}"></TD></TR>
<TR><TD VALIGN="TOP"><strong>b.</strong></TD>
	<TD VALIGN="TOP">Informed Decision-making</TD>
	<TD VALIGN="TOP">$q_avg{'q3b'}</TD>
	<TD VALIGN="TOP"><IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/pdf-6pt-barchart.cgi?v1=$q3b{'1'}&v2=$q3b{'2'}&v3=$q3b{'3'}&v4=$q3b{'4'}&v5=$q3b{'5'}&v6=$q3b{'0'}"
		ALIGN=RIGHT TITLE="Responses: One = $q3b{'1'}, Two = $q3b{'2'}, Three = $q3b{'3'}, Four = $q3b{'4'}, Five = $q3b{'5'}, N/A = $q3b{'0'}"></TD></TR>
<TR><TD VALIGN="TOP"><strong>c.</strong></TD>
	<TD VALIGN="TOP">Enhanced Quality of Personal Practice</TD>
	<TD VALIGN="TOP">$q_avg{'q3c'}</TD>
	<TD VALIGN="TOP"><IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/pdf-6pt-barchart.cgi?v1=$q3c{'1'}&v2=$q3c{'2'}&v3=$q3c{'3'}&v4=$q3c{'4'}&v5=$q3c{'5'}&v6=$q3c{'0'}"
		ALIGN=RIGHT TITLE="Responses: One = $q3c{'1'}, Two = $q3c{'2'}, Three = $q3c{'3'}, Four = $q3c{'4'}, Five = $q3c{'5'}, N/A = $q3c{'0'}"></TD></TR>
<TR><TD VALIGN="TOP"><strong>d.</strong></TD>
	<TD VALIGN="TOP">Positively Affected Student Performance</TD>
	<TD VALIGN="TOP">$q_avg{'q3d'}</TD>
	<TD VALIGN="TOP"><IMG SRC="/cgi-bin/mysql/ChartDirector/intranet/pdf-6pt-barchart.cgi?v1=$q3d{'1'}&v2=$q3d{'2'}&v3=$q3d{'3'}&v4=$q3d{'4'}&v5=$q3d{'5'}&v6=$q3d{'0'}" 
		ALIGN=RIGHT TITLE="Responses: One = $q3d{'1'}, Two = $q3d{'2'}, Three = $q3d{'3'}, Four = $q3d{'4'}, Five = $q3d{'5'}, N/A = $q3d{'0'}"></TD></TR>
</TABLE>		
<P>
<em>Comments:</em><BR>
<em><FONT COLOR=RED><OL>$text_q3e</OL></FONT></em>
<P>
<strong>Q5: How do you plan to use the information from this document?</strong> (Question added to survey January 2004)<BR>
<em><FONT COLOR=RED><OL>$text_q8a</OL></FONT></em>
<P>
<strong>Q6: How have you used the information from this document?</strong> (Question added to survey January 2004)<BR>
<em><FONT COLOR=RED><OL>$text_q9a</OL></FONT></em>
<P>
<strong>Q7: How can SEDL improve the document?</strong><BR>
<em><FONT COLOR=RED><OL>$text_q4a</OL></FONT></em>
<P>
<strong>Q8: Please tell us why you would or would not recommend this document to others.</strong><BR>
<em><FONT COLOR=RED><OL>$text_q6a</OL></FONT></em>
<P>
<strong>Q9: What issues should SEDL address in the future?</strong><BR>
<em><FONT COLOR=RED><OL>$text_q5a</OL></FONT></em>
<BR>
<BR>
<BR>
The survey responses included <A HREF="clientsurveys.cgi?location=listsurveys&showpdf=$showpdf&show_followuprequests=yes">$total_request_followup requests for followup contact</A>\.
EOM

}
#################################################################################
## END: LOCATION = summary_responses_singledoc
#################################################################################



#################################################################################
## START: LOCATION = LISTDOCUMENTSBYURL
#################################################################################
if ($location eq 'listdocumentsbyurl') {

## START: PRINT PAGE HEADER
print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN=TOP>
		<h1>Product Survey Report System</h1>
		<P>
		Click on a  Document URL to view all the surveys related to that document.
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN=TOP NOWRAP>
		<strong>Navigation options:</strong><BR>
		- <A HREF="clientsurveys.cgi?location=about">about the PDF Client Surveys</A><BR>
		- <A HREF="clientsurveys.cgi?location=summary_countdate">summary: surveys by date and department</A><BR>
		- <A HREF="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</A> (catalog items only)<BR>
		- <A HREF="clientsurveys.cgi?location=datadump">download data to a file</A>
	</TD></TR>
</TABLE>
<P>
EOM
## END: PRINT PAGE HEADER

	##########################################################################################
	## START: DECLARE HASHES TO TRACK (1) DOCUMENT TITLE AND (2) ONLINE CATALOG URL, FOR EACH PUB THAT HAS A PDF ONLINE
	##########################################################################################
	my %documenttitle;
	my %catalogpage;
	 $documenttitle{'<em>document not in SEDL Catalog</em>'} = "0";
	#############################################
	## START: DB QUERY TO GRAB ALL DOCUMENT NAMES
	#############################################
	my $command = "select onlineid, title, title2, locpdf from sedlcatalog where locpdf NOT LIKE ''";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
			my ($onlineid, $title, $title2, $locpdf) = @arr;
	
			$documenttitle{$locpdf} = "$title";
			$documenttitle{$locpdf} .= ": $title2" if ($title2 ne '');
 	
			$catalogpage{$locpdf} = "/pubs/catalog/items/$onlineid";
	
		} # END DB QUERY LOOP
	#############################################
	## END: DB QUERY TO GRAB ALL DOCUMENT NAMES
	#############################################
	##########################################################################################
	## END: DECLARE HASHES TO TRACK (1) DOCUMENT TITLE AND (2) ONLINE CATALOG URL, FOR EACH PUB THAT HAS A PDF ONLINE
	##########################################################################################

	my $countsent = "0";
	my $countsentnobounce = "0"; # represents countsent - countbounced
	my $countreplied = "0";
	my $countnotsent = "0";
	my $countbounced = "0";
	my $countpending = "0";
	my $ratio = "0";

print<<EOM;
<H2>List Documents by URL</H2>
<TABLE BORDER=1 CELLPADDING=3 CELLSPACING=0>
<TR><TD ALIGN=CENTER class=small>\#</TD>
	<TD ALIGN=CENTER class=small>Document URL</TD>
	<TD ALIGN=CENTER class=small>Not Sent<BR><IMG SRC="images/surveynotsent.gif" ALT="Status"></TD>
	<TD ALIGN=CENTER class=small>Sent<BR><IMG SRC="images/surveysent.gif" ALT="Status"></TD>
	<TD ALIGN=CENTER class=small>Filled Out<BR><IMG SRC="images/surveyreceived.gif" ALT="Status" BORDER=0></TD>
	<TD ALIGN=CENTER class=small>Response Ratio</TD>
	<TD ALIGN=CENTER class=small>Pending Mailout<BR><IMG SRC="images/new.gif" ALT="Status" BORDER=0></TD>
</TR>
EOM
	
	my $lastdocumenturl = "";
	my $counter_uniquepubs = "1";
	my $command = "select * from clientsurvey ";
    	$command .= " WHERE date > '$search_dateafter'";
		$command .= " AND documenturl LIKE '$showpdf'" if ($showpdf ne '');
		$command .= " AND surveyreceived NOT LIKE '0000-00-00'" if ($show_responses_only eq 'yes');
		$command .= " AND request_followup LIKE '%request%'" if ($show_followuprequests ne '');
		$command .= " AND surveyreceived NOT LIKE '0000-00-00'" if ($show_responses_only eq 'yes');
		$command .= " AND request_followup LIKE '%request%'" if ($show_followuprequests ne '');

		$command .= " AND documenturl like '%afterschool%'" if ($summarysite eq 'afterschool');
		$command .= " AND documenturl like '%connections%'" if ($summarysite eq 'connections');
		$command .= " AND documenturl like '%/es/%'" if ($summarysite eq '/es/');
		$command .= " AND documenturl like '%loteced%'" if ($summarysite eq 'loteced');
   		$command .= " AND documenturl like '%orc\/rr%' " if ($summarysite eq 'rapidresponses');
		$command .= " AND (documenturl like '%scimast%' OR documenturl like '%scimath%')" if ($summarysite eq 'scimast');
		$command .= " AND documenturl like '%secac%'" if ($summarysite eq 'secac');
		$command .= " AND documenturl like '%reading%'" if ($summarysite eq 'reading');
		$command .= " AND documenturl like '%sedl%'" if ($summarysite eq 'sedl');
    	$command .= " AND ((documenturl like '%sedl-letter%') OR (documenturl like '%sedletter%')) " if ($summarysite eq 'sedlletter');
    	$command .= " AND documenturl like '%insights%' " if ($summarysite eq 'insights');
    	$command .= " AND documenturl like '%compass%' " if ($summarysite eq 'compass');
    	$command .= " AND documenturl like '%change/issues%' " if ($summarysite eq 'change');
		$command .= " AND documenturl like '%'" if (($summarysite eq '%') ||($summarysite eq ''));

		$command .= " order by documenturl";
# print "<P>COMMAND: $command";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($recordid, $surveysent, $surveysenttwice, $surveyreceived, $email, $date, $documenturl, $documentid, $documentgroup, $q1a, $q1b, $q1c, $q1d, $q2a, $q2b, $q3a, $q3b, $q3c, $q3d, $q3e, $q4a, $q5a, $q6a, $q7a, $q8a, $q9a, $q10a, $ipnum, $browser, $request_followup, $permission_use_comments, $name, $title, $staff_comments, $staff_comments_by, $staff_comments_date) = @arr;

			if (($lastdocumenturl ne $documenturl) && ($lastdocumenturl ne '')) {
				my $lastdocumenturl_label = $lastdocumenturl;
					$lastdocumenturl_label =~ s/http:\/\/www.sedl.org//g;
					$lastdocumenturl_label =~ s/http:\/\///g;
#					$lastdocumenturl_label = $documenttitle if ($documenttitle ne '');
				my $add_doctitle = "";
					$add_doctitle = "$documenttitle{$lastdocumenturl} <BR>" if ($documenttitle{$lastdocumenturl} ne '');
				if (($countnotsent ne '0') && ($countsentnobounce eq '0')) {
					$countbounced = "0" ;
				} # END IF
print<<EOM;
<TR><TD class=small VALIGN=TOP>$counter_uniquepubs</TD>
	<TD class=small><A HREF="clientsurveys.cgi?location=listsurveys&showpdf=$lastdocumenturl\">$add_doctitle $lastdocumenturl_label</TD>
	<TD class=small>$countnotsent</TD>
	<TD class=small>$countsentnobounce</TD>
	<TD class=small>$countreplied</TD>
	<TD class=small>$ratio\%</TD>
	<TD class=small>$countpending</TD>
</TR>
EOM
				$countpending = "0";
				$countsent = "0";
				$countreplied = "0";
				$countnotsent = "0";
				$countbounced = "0";
				$ratio = "0";

				$counter_uniquepubs++
			} # END IF

			$countpending++ if ($surveysent eq 'no');
			$countsent++ if (($surveysent ne 'no') && ($surveysent ne 'nosend'));
			$countreplied++ if ($q1a ne '');
			$countnotsent++ if ($surveysent eq 'nosend');
			$countbounced++ if ($surveysent eq 'bounced');

			$countsentnobounce = $countsent - $countbounced;

			$ratio = $countreplied/$countsentnobounce if (($countsentnobounce ne '0') && ($countreplied ne '0'));
			$ratio = $ratio * 100 if ($ratio ne '0');

			$lastdocumenturl = $documenturl;

			my $x = new Number::Format;
			$ratio = $x->format_number($ratio, 0, 0);

		} # END DB QUERY LOOP


	my $lastdocumenturl_label = $lastdocumenturl;
	   $lastdocumenturl_label =~ s/http:\/\/www.sedl.org//g;
	   $lastdocumenturl_label =~ s/http:\/\///g;
	my $add_doctitle = "";
	   $add_doctitle = "$documenttitle{$lastdocumenturl} <BR>" if ($documenttitle{$lastdocumenturl} ne '');

	if (($countnotsent ne '0') && ($countsentnobounce eq '0')) {
		$countbounced = "0" ;
	}
print<<EOM;
<TR><TD class=small VALIGN=TOP>$counter_uniquepubs</TD>
	<TD class=small><A HREF="clientsurveys.cgi?location=listsurveys&showpdf=$lastdocumenturl\">$add_doctitle $lastdocumenturl_label</A></TD>
	<TD class=small>$countnotsent</TD>
	<TD class=small>$countsentnobounce</TD>
	<TD class=small>$countreplied</TD>
	<TD class=small>$ratio\%</TD>
	<TD class=small>$countpending</TD>
</TR>
</TABLE>
EOM

}  
#################################################################################
## END: LOCATION = LISTDOCUMENTSBYURL
#################################################################################


#################################################################################
## START: LOCATION = LISTDOCUMENTSBYTITLE  (BL: OPTIMIZED DB QUERY 4/15/2006)
#################################################################################
if ($location eq 'listdocumentsbytitle') {

	## PRINT PAGE HEADER
print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN=TOP>
		<h1>Product Survey Report System</h1>
		<P>
		On this page, you will see a list of documents for which surveys were sent, 
		listed by title.
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN=TOP NOWRAP>
		<strong>Navigation options:</strong><BR>
		- <A HREF="clientsurveys.cgi?location=about">about the PDF Client Surveys</A><BR>
		- <A HREF="clientsurveys.cgi?location=summary_countdate">summary: surveys by date and department</A><BR>
EOM
	if ($summarysite ne '') {
		print "		- <A HREF=\"clientsurveys.cgi?location=listdocumentsbytitle\">list documents by title</A> (catalog items only)<BR>";
	} else {
		print "		- <em><FONT COLOR=\"RED\">list documents by title</FONT></em> (catalog items only)<BR>";
	}
print<<EOM;
		- <A HREF="clientsurveys.cgi?location=datadump">download data to a file</A>
	</TD></TR>
</TABLE>
<form action="clientsurveys.cgi" method=POST>
Show surveys from after
	<SELECT NAME="search_month">
EOM
	&print_month_menu($search_month); ## SUBROUTINE print_month_menu - send a previous value in () if any
print<<EOM;
	</SELECT>
	<SELECT NAME="search_date">
EOM
	&print_day_menu($search_date); ## SUBROUTINE print_day_menu - send a previous value in () if any
print<<EOM;
	</SELECT>
	<SELECT NAME="search_year">
EOM
	&print_year_menu(2002, 0, $search_year); ## SUBROUTINE print_year_menu - send a previous value in () if any
print<<EOM;
	</SELECT><BR>
Show PDFs from <SELECT NAME="summarysite">
EOM
	&print_summary_site_menu($summarysite); ## SUBROUTINE print_year_menu - send a previous value in () if any
print<<EOM;
	</SELECT><BR>
	<input type="hidden" name="show_followuprequests" value="$show_followuprequests">
	<input type="hidden" name="show_usecomments" value="$show_usecomments">
	<input type="hidden" name="location" value="listdocumentsbytitle">
	<input type="submit" name="submit" value="Refresh Display">
	</form><BR>
<P>
EOM

	my $countsent = "0";
	my $countsentnobounce = "0"; # represents countsent - countbounced
	my $countreplied = "0";
	my $countnotsent = "0";
	my $countbounced = "0";
	my $countpending = "0";
	my $ratio = "0";

	my $count_req_followup = "0";
	my $count_perm_use_comments = "0";
	my $count_staff_followup = "0";

print<<EOM;
<H2>List Documents by Title (SEDL Catalog items only)</H2>
<TABLE BORDER=1 CELLPADDING=3 CELLSPACING=0>
<TR><TD ALIGN=CENTER>\#</TD>
	<TD ALIGN=CENTER>Document Title</TD>
	<TD ALIGN=CENTER>Surveys Sent, Rceived, and Response Ratio</TD>
	<TD ALIGN=CENTER>Followup Contact Requests</TD>
	<TD ALIGN=CENTER># Permit Use of Comments</TD>
	<TD ALIGN=CENTER>Survey Results</TD>
</TR>
EOM
	
	my $last_title_label = "";
	my $lastdocumenturl = "";
	my $counter_uniquepubs = "1";
	my $command = "select sedlcatalog.title, sedlcatalog.title2, 
				clientsurvey.surveysent, clientsurvey.surveysenttwice, clientsurvey.documenturl, clientsurvey.documentid, clientsurvey.documentgroup, 
				clientsurvey.q1a, clientsurvey.request_followup, clientsurvey.permission_use_comments, clientsurvey.staff_comments_date
				from sedlcatalog, clientsurvey ";
	    $command .= " WHERE date > '$search_dateafter'";
		$command .= " AND documenturl LIKE '$showpdf'" if ($showpdf ne '');
		$command .= " AND email LIKE '%$show_address%'" if ($show_address ne '');
		$command .= " AND surveyreceived NOT LIKE '0000-00-00'" if ($show_responses_only eq 'yes');
		$command .= " AND request_followup LIKE '%request%'" if ($show_followuprequests ne '');
	    $command .= " AND documenturl like '%afterschool%'" if ($summarysite eq 'afterschool');
    	$command .= " AND documenturl like '%connections%'" if ($summarysite eq 'connections');
    	$command .= " AND documenturl like '%/es/%' " if ($summarysite eq '/es/');
    	$command .= " AND documenturl like '%loteced%' " if ($summarysite eq 'loteced');
   		$command .= " AND documenturl like '%orc\/rr%' " if ($summarysite eq 'rapidresponses');
    	$command .= " AND (documenturl like '%scimast%' OR documenturl like '%scimath%') " if ($summarysite eq 'scimast');
    	$command .= " AND documenturl like '%secac%' " if ($summarysite eq 'secac');
    	$command .= " AND documenturl like '%reading%' " if ($summarysite eq 'reading');
    	$command .= " AND documenturl like '%sedl%' " if ($summarysite eq 'sedl');
    	$command .= " AND ((documenturl like '%sedl-letter%') OR (documenturl like '%sedletter%')) " if ($summarysite eq 'sedlletter');
    	$command .= " AND documenturl like '%insights%' " if ($summarysite eq 'insights');
    	$command .= " AND documenturl like '%compass%' " if ($summarysite eq 'compass');
    	$command .= " AND documenturl like '%change/issues%' " if ($summarysite eq 'change');

		$command .= " AND sedlcatalog.locpdf=clientsurvey.documenturl order by sedlcatalog.title, sedlcatalog.title2";

	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($title, $title2, $surveysent, $surveysenttwice, $documenturl, $documentid, $documentgroup, $q1a, $request_followup, $permission_use_comments, $staff_comments_date) = @arr;

			my $title_label = "$title";
   				$title_label = "$title: $title2" if ($title2 ne ''); 
   				$title_label = &cleanaccents2html($title_label);


			if (($lastdocumenturl ne $documenturl) && ($lastdocumenturl ne '')) {
print<<EOM;
<TR><TD VALIGN=TOP>$counter_uniquepubs</TD>
	<TD VALIGN=TOP><em>$last_title_label</em></TD>
	<TD VALIGN=TOP><TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" WIDTH="100%">
					<TR><TD VALIGN="TOP"><em>Sent:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT"><A HREF="/staff/quality/clientsurveys.cgi?location=listsurveys&showpdf=$lastdocumenturl&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_followuprequests=$show_followuprequests">$countsentnobounce</A></TD></TR>
					<TR><TD VALIGN="TOP"><em>Received:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$countreplied</TD></TR>
EOM
		if ($countpending ne '0') {
			print "<TR><TD VALIGN=\"TOP\"><em>Pending send:</em></TD><TD VALIGN=\"TOP\" ALIGN=\"RIGHT\">$countpending</TD></TR>";
		}
print<<EOM;
					<TR><TD VALIGN="TOP" NOWRAP><em>Response Ratio:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$ratio\%</TD></TR>
					</TABLE>
					</TD>
	<TD VALIGN=TOP NOWRAP ALIGN="CENTER">
EOM
				if ($count_req_followup ne '0') {
print<<EOM;
		<TABLE CELLPADDING="1" CELLSPACING="0" WIDTH="100%">
		<TR><TD VALIGN=TOP><em>Requests:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT"><A HREF="clientsurveys.cgi?location=listsurveys&showpdf=$lastdocumenturl&show_followuprequests=yes&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year\">$count_req_followup</A></TD></TR>
		<TR><TD VALIGN=TOP><em>Followups:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$count_staff_followup</TD></TR>
		</TABLE>
EOM
				} else {
print "N/A";
				}
print<<EOM;
	</TD>
	<TD VALIGN=TOP ALIGN="CENTER">
EOM
				if ($count_perm_use_comments ne '0') {
print<<EOM;
<A HREF="clientsurveys.cgi?location=listsurveys&showpdf=$lastdocumenturl&show_usecomments=yes">$count_perm_use_comments</A>
EOM
				} else {
					print "$count_perm_use_comments";
				}
print<<EOM;
</TD>
	<TD VALIGN=TOP NOWRAP>
EOM
	if ($countreplied ne '0') {
my $s = "";
   $s = "s" if ($countreplied ne '1');
print<<EOM;
<A HREF=\"/staff/quality/clientsurveys.cgi?location=summary_responses_singledoc&showpdf=$lastdocumenturl&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_followuprequests=$show_followuprequests\">combined<BR>results</A>
<BR>
<BR>
<A HREF="/staff/quality/clientsurveys.cgi?location=listsurveys&showpdf=$lastdocumenturl&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_followuprequests=$show_followuprequests&show_responses_only=yes&show_address=$show_address">$countreplied individual<BR>survey$s</A></TD>
EOM
} else {
	print "N/A";
}
print<<EOM;
</TR>
EOM
				$countpending = "0";
				$countsent = "0";
				$countreplied = "0";
				$countnotsent = "0";
				$countbounced = "0";
				$ratio = "0";
				
				$count_req_followup = "0";
				$count_perm_use_comments = "0";
				$count_staff_followup = "0";

				$counter_uniquepubs++;
			}

			$countpending++ if ($surveysent eq 'no');
			$countsent++ if ($surveysent ne 'no');
			$countreplied++ if ($q1a ne '');
			$countnotsent++ if ($surveysent eq 'nosend');
			$countbounced++ if ($surveysent eq 'bounced');

			$countsentnobounce = $countsent - $countbounced;

			$count_req_followup++ if ($request_followup =~ 'user requests');
			$count_perm_use_comments++ if ($permission_use_comments eq 'yes');
			$count_staff_followup++ if ($staff_comments_date =~ '20');

			$ratio = $countreplied/$countsentnobounce if (($countsentnobounce ne '0') && ($countreplied ne '0'));
			$ratio = $ratio * 100 if ($ratio ne '0');

			$lastdocumenturl = $documenturl;
			$last_title_label = $title_label;

			my $x = new Number::Format;
			$ratio = $x->format_number($ratio, 2, 0);

		} # END DB QUERY LOOP
print<<EOM;
<TR><TD VALIGN=TOP>$counter_uniquepubs</TD>
	<TD VALIGN=TOP><em>$last_title_label</em></TD>
	<TD VALIGN=TOP><TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" WIDTH="100%">
					<TR><TD VALIGN="TOP"><em>Sent:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$countsentnobounce</TD></TR>
					<TR><TD VALIGN="TOP"><em>Received:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$countreplied</TD></TR>
EOM
		if ($countpending ne '0') {
			print "<TR><TD VALIGN=\"TOP\"><em>Pending send:</em></TD><TD VALIGN=\"TOP\" ALIGN=\"RIGHT\">$countpending</TD></TR>";
		}
print<<EOM;
					<TR><TD VALIGN="TOP" NOWRAP><em>Response Ratio:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$ratio\%</TD></TR>
					</TABLE>
					</TD>
	<TD VALIGN=TOP NOWRAP ALIGN="CENTER">
EOM
				if ($count_req_followup ne '0') {
print<<EOM;
		<TABLE CELLPADDING="1" CELLSPACING="0" WIDTH="100%">
		<TR><TD VALIGN=TOP><em>Requests:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT"><A HREF="clientsurveys.cgi?location=listsurveys&showpdf=$lastdocumenturl&show_followuprequests=yes&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year\">$count_req_followup</A></TD></TR>
		<TR><TD VALIGN=TOP><em>Followups:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$count_staff_followup</TD></TR>
		</TABLE>
EOM
				} else {
print "N/A";
				}
print<<EOM;
	</TD>
	<TD VALIGN=TOP ALIGN="CENTER">
EOM
				if ($count_perm_use_comments ne '0') {
print<<EOM;
<A HREF="clientsurveys.cgi?location=listsurveys&showpdf=$lastdocumenturl&show_usecomments=yes&amp;search_date=$search_date&amp;search_month=$search_month&amp;search_year=$search_year">$count_perm_use_comments</A>
EOM
				} else {
					print "$count_perm_use_comments";
				}
print<<EOM;
</TD>
<TD VALIGN=TOP NOWRAP>
EOM
	if ($countreplied ne '0') {
my $s = "";
   $s = "s" if ($countreplied ne '1');
print<<EOM;
<A HREF=\"/staff/quality/clientsurveys.cgi?location=summary_responses_singledoc&showpdf=$lastdocumenturl&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_followuprequests=$show_followuprequests\">combined<BR>results</A>
<BR>
<BR>
<A HREF="/staff/quality/clientsurveys.cgi?location=listsurveys&showpdf=$lastdocumenturl&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_followuprequests=$show_followuprequests&show_responses_only=yes&show_address=$show_address">$countreplied individual<BR>survey$s</A></TD>
EOM
} else {
	print "N/A";
}
print<<EOM;
</TR>
</TABLE>
EOM

}  
#################################################################################
## END: LOCATION = LISTDOCUMENTSBYTITLE
#################################################################################


#################################################################################
## START: LOCATION = listsurveys  (BL: OPTIMIZED DB QUERY 4/15/2006)
#################################################################################
if ($location eq 'listsurveys') {


print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN=TOP>
		<h1>Product Survey Report System</h1>
		<p>On this page, you can access individual surveys.
			<UL>
			<LI>Click the "View this survey" link to see the survey the user filled out and their responses.
			</UL></p>
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN=TOP NOWRAP>
		<strong>Navigation options:</strong><BR>
		- <A HREF="clientsurveys.cgi?location=about">about the PDF Client Surveys</A><BR>
		- <A HREF="clientsurveys.cgi?location=summary_countdate">summary: surveys by date and department</A><BR>
		- <A HREF="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</A> (catalog items only)<BR>
		- <A HREF="clientsurveys.cgi?location=datadump">download data to a file</A>
	</TD></TR>
</TABLE>
<P>
<TABLE>
<TR><TD ROWSPAN=6 VALIGN=TOP>
<H2>List Survey Responses by URL</H2>
<FONT COLOR=996600><em><strong>Note:</strong> We told the Web site visitors their e-mail addresses would only be used to contact 
	them about the product they viewed, not for any other purpose.</em></FONT>
<P>
<form action="clientsurveys.cgi" method=POST>
Show surveys from after
	<SELECT NAME="search_month">
EOM
&print_month_menu($search_month); ## SUBROUTINE print_month_menu - send a previous value in () if any
print<<EOM;
	</SELECT>
	<SELECT NAME="search_date">
EOM
&print_day_menu($search_date); ## SUBROUTINE print_day_menu - send a previous value in () if any
print<<EOM;
	</SELECT>
	<SELECT NAME="search_year">
EOM
&print_year_menu(2002, 0, $search_year); ## SUBROUTINE print_year_menu - send a previous value in () if any
print<<EOM;
	</SELECT><BR>
Show PDFs from <SELECT NAME="summarysite">
EOM
&print_summary_site_menu($summarysite); ## SUBROUTINE print_year_menu - send a previous value in () if any
print<<EOM;
	</SELECT><BR>


Show <SELECT NAME="show_responses_only">
	 <OPTION VALUE="no"
EOM
print " SELECTED" if ($show_responses_only ne 'yes');
print<<EOM;
>all surveys (completed and incomplete)</OPTION>
	 <OPTION VALUE="yes"
EOM
print " SELECTED" if ($show_responses_only eq 'yes');
print<<EOM;
>completed surveys only</OPTION>
	 </SELECT><BR>


Sort by <SELECT NAME="sortby">
	 <OPTION VALUE="date"
EOM
print " SELECTED" if ($sortby eq 'date');
print<<EOM;
>date (descending)</OPTION>
	 <OPTION VALUE="email"
EOM
print " SELECTED" if ($sortby eq 'email');
print<<EOM;
>e-mail</OPTION>
	 </SELECT><BR>

	<input type="hidden" name="show_followuprequests" value="$show_followuprequests">
	<input type="hidden" name="show_usecomments" value="$show_usecomments">
	<input type="hidden" name="location" value="listsurveys">
	<input type="submit" name="submit" value="Refresh Display">
	</form>
	
</TD>
	<TD COLSPAN=2><strong>Legend</strong></TD></TR>
<TR><TD VALIGN=TOP><IMG SRC=\"images/new.gif\" ALT=\"Status\" BORDER=0 VSPACE=4></TD><TD>New entry - Survey not sent yet</TD></TR>
<TR><TD VALIGN=TOP><IMG SRC=\"images/surveysent.gif\" ALT=\"Status\" VSPACE=4></TD><TD>Survey Sent</TD></TR>
<TR><TD VALIGN=TOP><IMG SRC=\"images/surveyreceived.gif\" ALT=\"Status\" BORDER=0 VSPACE=4></TD><TD>Survey filled out - click individual checkmarks next to an entry below to view data</TD></TR>
<TR><TD VALIGN=TOP><IMG SRC=\"images/surveynotsent.gif\" ALT=\"Status\" VSPACE=4></TD><TD>Survey will never be sent - Document not in SEDL product catalog</TD></TR>
<TR><TD VALIGN=TOP><IMG SRC=\"images/surveybounced.gif\" ALT=\"Status\" BORDER=0 VSPACE=4></TD><TD>User e-mail bounced/invalid</TD></TR>
</TABLE>
<P>
EOM

	my $last_title_label = "";
	my $lastdocumenturl = "";
	my $sortby_label = $sortby;
	   $sortby_label =~ s/date/clientsurvey\.date DESC/g;

	my $command = "select clientsurvey.recordid, clientsurvey.surveysent, clientsurvey.surveysenttwice, clientsurvey.surveyreceived, clientsurvey.email, clientsurvey.date, clientsurvey.documenturl, clientsurvey.documentid, clientsurvey.documentgroup, clientsurvey.request_followup, clientsurvey.permission_use_comments, clientsurvey.staff_comments,
					sedlcatalog.title, sedlcatalog.title2
					FROM clientsurvey LEFT OUTER JOIN sedlcatalog";
	   $command .= " ON clientsurvey.documenturl = sedlcatalog.locpdf";
	   $command .= " WHERE clientsurvey.date > '$search_dateafter'";
	   $command .= " AND email LIKE '%$show_address%'" if ($show_address ne '');
	   $command .= " AND clientsurvey.documenturl LIKE '$showpdf'" if ($showpdf ne '');
	   $command .= " AND clientsurvey.surveyreceived NOT LIKE '0000-00-00'" if ($show_responses_only eq 'yes');
	   $command .= " AND clientsurvey.request_followup LIKE '%request%'" if ($show_followuprequests ne '');
	   $command .= " AND clientsurvey.permission_use_comments LIKE 'yes'" if ($show_usecomments eq 'yes');

		$command .= " AND clientsurvey.documenturl like '%afterschool%'" if ($summarysite eq 'afterschool');
		$command .= " AND clientsurvey.documenturl like '%connections%'" if ($summarysite eq 'connections');
		$command .= " AND clientsurvey.documenturl like '%/es/%' " if ($summarysite eq '/es/');
		$command .= " AND clientsurvey.documenturl like '%loteced%' " if ($summarysite eq 'loteced');
   		$command .= " AND clientsurvey.documenturl like '%ncddr%' " if ($summarysite eq 'ncddr');
		$command .= " AND clientsurvey.documenturl like '%reading%' " if ($summarysite eq 'reading');
   		$command .= " AND clientsurvey.documenturl like '%orc\/rr%' " if ($summarysite eq 'rapidresponses');
		$command .= " AND (clientsurvey.documenturl like '%scimast%' OR clientsurvey.documenturl like '%scimath%') " if ($summarysite eq 'scimast');
		$command .= " AND clientsurvey.documenturl like '%secac%' " if ($summarysite eq 'secac');
		$command .= " AND clientsurvey.documenturl like '%sedl%' " if ($summarysite eq 'sedl');
    	$command .= " AND ((clientsurvey.documenturl like '%sedl\-letter%') OR (clientsurvey.documenturl like '%sedletter%')) " if ($summarysite eq 'sedlletter');
    	$command .= " AND clientsurvey.documenturl like '%insights%' " if ($summarysite eq 'insights');
    	$command .= " AND clientsurvey.documenturl like '%compass%' " if ($summarysite eq 'compass');
    	$command .= " AND clientsurvey.documenturl like '%change/issues%' " if ($summarysite eq 'change');




	   $command .= " order by clientsurvey.documenturl, $sortby_label";
#print "<P>COMMAND: $command<P>";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	my $count = "1";

	while (my @arr = $sth->fetchrow) {
		my ($recordid, $surveysent, $surveysenttwice, $surveyreceived, $email, $date, $documenturl, $documentid, $documentgroup, $request_followup, $permission_use_comments, $staff_comments, $title, $title2) = @arr;
			my $title_label = "$title";
   				$title_label = "$title: $title2" if ($title2 ne ''); 
   				$title_label = &cleanaccents2html($title_label);

		my $image = "";
		$image ="<A HREF=\"/survey/pubs.cgi?e=$email&id=$recordid\"><IMG SRC=\"images/new.gif\" ALT=\"Status\" BORDER=0></A>" if $surveysent eq 'no';
		$image ="<IMG SRC=\"images/surveysent.gif\" ALT=\"Status\">" if $surveysent =~ 'yes';
		$image ="<IMG SRC=\"images/surveybounced.gif\" ALT=\"Status\">" if $surveysent eq 'bounced';
		$image ="<IMG SRC=\"images/surveynotsent.gif\" ALT=\"Status\">" if $surveysent eq 'nosend';
		$image ="<A HREF=\"/staff/quality/clientsurveys.cgi?location=showdata&surveyid=$recordid\" TARGET=TOP><IMG SRC=\"images/surveyreceived.gif\" ALT=\"Status\" BORDER=0> view this survey</A>" if (($surveyreceived ne '') &&($surveyreceived ne '0000-00-00'));
		if ($documenturl ne $lastdocumenturl) {
			if ($lastdocumenturl eq '') {
				print "<BR><TABLE WIDTH=100% BORDER=1 CELLSPACING=0 CELLPADDING=2>
				<TR><TD colspan=5 class=small><em>$title_label</em><BR>$documenturl<BR> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(view <A HREF=\"/staff/quality/clientsurveys.cgi?location=summary_responses_singledoc&showpdf=$documenturl&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_followuprequests=$show_followuprequests\">combined survey results</A>)</TD>
					<TD class=small>Did survey<BR>contain a<BR>request for<BR>follow-up?</TD>
					<TD class=small>Are there<BR>SEDL staff<BR>post-survey<BR>comments?</TD>
					<TD class=small>User gave<BR>permission<BR>to use<BR>comments<BR></TD></TR>\n";
			}
			if ($lastdocumenturl ne '') {
				print "</TABLE><BR><TABLE WIDTH=100% BORDER=1 CELLSPACING=0 CELLPADDING=2>
						<TR><TD colspan=5 class=small><em>$title_label</em><BR>$documenturl<BR> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(view <A HREF=\"/staff/quality/clientsurveys.cgi?location=summary_responses_singledoc&showpdf=$documenturl&search_date=$search_date&search_month=$search_month&search_year=$search_year&summarysite=$summarysite&show_followuprequests=$show_followuprequests\">combined survey results</A>)</TD>
							<TD class=small>Did survey<BR>contain a<BR>request for<BR>follow-up?</TD>
							<TD class=small>Are there<BR>SEDL staff<BR>post-survey<BR>comments?</TD>
							<TD class=small>User gave<BR>permission<BR>to use<BR>comments<BR></TD></TR>\n";
			} # END IF
		} # END IF
		$staff_comments = "yes" if ($staff_comments ne '');
		#$permission_use_comments = "yes" if ($permission_use_comments =~ '\(us');
		#$permission_use_comments = "yes in 6a" if ($q6a =~ '\(user agree');
		$date = &date2standard($date);
#		if ($sortby =~ 'date, email') {
			print "<TR><TD>$count</TD>
						<TD NOWRAP>$image</TD>
						<TD class=small>$date</TD>
						<TD class=small>$email</TD>
						<TD class=small>ID: $recordid</TD>
						<TD>$request_followup</TD>
						<TD>$staff_comments</TD>
						<TD>$permission_use_comments</TD></TR>\n";
#		} else {
#		if ($sortby eq 'email') {
#			print "<TR><TD>$count</TD>
#						<TD>$image</TD>
#						<TD class=small>$email</TD>
#						<TD class=small>ID: $recordid</TD>
#						<TD class=small>$date</TD>
#						<TD>$request_followup</TD>
#						<TD>$staff_comments</TD>
#						<TD>$permission_use_comments</TD></TR>\n";
#		}
		$lastdocumenturl = $documenturl;
		$last_title_label = $title_label;
		$count++;
	}

	print "</TABLE>";

}  
#################################################################################
## END: LOCATION = listsurveys
#################################################################################



#################################################################################
## START: LOCATION = SHOWDATA  (BL: OPTIMIZED DB QUERY 4/15/2006)
#################################################################################
if ($location eq 'showdata') {
## PRINT PAGE HEADER
print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN=TOP>
		<h1>Product Survey Report System</h1>
		<p>Action: View Survey Data<BR>
		(survey responses are in red)</p> 
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN=TOP NOWRAP>
		<strong>Navigation options:</strong><BR>
		- <A HREF="clientsurveys.cgi?location=about">about the PDF Client Surveys</A><BR>
		- <A HREF="clientsurveys.cgi?location=summary_countdate">summary: surveys by date and department</A><BR>
		- <A HREF="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</A> (catalog items only)<BR>
		- <A HREF="clientsurveys.cgi?location=datadump">download data to a file</A>
	</TD></TR>
</TABLE>
EOM

	my $command = "select * from clientsurvey where recordid like '$surveyid' order by recordid";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
	while (my @arr = $sth->fetchrow) {
		my ($recordid, $surveysent, $surveysenttwice, $surveyreceived, $email, $date, $documenturl, $documentid, $documentgroup, $q1a, $q1b, $q1c, $q1d, $q2a, $q2b, $q3a, $q3b, $q3c, $q3d, $q3e, $q4a, $q5a, $q6a, $q7a, $q8a, $q9a, $q10a, $ipnum, $browser, $request_followup, $permission_use_comments, $name, $title, $staff_comments, $staff_comments_by, $staff_comments_date) = @arr;


		#############################################
		## START: DB QUERY TO GRAB THE DOCUMENT NAME
		#############################################
		my $documenttitle = "Error - Document not found in SEDL Catalog";
		my $catalogpage = "";

		my $pdfsearchstring = "$documenturl";
		   $pdfsearchstring =~ s/http\:\/\/www\.sedl\.org//g;
   
		my $command = "select title, title2, onlineid from sedlcatalog 
						where ((locpdf LIKE '%$pdfsearchstring') OR (lochtml LIKE '%$pdfsearchstring'))";

#print "<p class=\"info\">$command</p>";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($title, $title2, $onlineid) = @arr;

			$documenttitle = "$title";
			$documenttitle .= ": $title2" if $title2 ne '';
			$documenttitle = &cleanaccents2html($documenttitle);

			$catalogpage = "/pubs/catalog/items/$onlineid";
		} # END DB QUERY
		#############################################
		## END: DB QUERY TO GRAB THE DOCUMENT NAME
		#############################################

		my $q1a_label = &place_label($q1a, 1);
		my $q1b_label = &place_label($q1b, 1);
		my $q1c_label = &place_label($q1c, 1);
		my $q1d_label = &place_label($q1d, 1);
		my $q2a_label = &place_label($q2a, 1);

		my $q3a_label = &place_label($q3a, 2);
		my $q3b_label = &place_label($q3b, 2);
		my $q3c_label = &place_label($q3c, 2);
		my $q3d_label = &place_label($q3d, 2);
		$permission_use_comments = "yes" if ($q6a =~ 'user agreed to allow comments');
print<<EOM;
<P>
<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=0 WIDTH=100%><TR><TD><strong><A HREF="http://www.sedl.org/survey/pubs.cgi?e=$email&id=$recordid" TARGET="TOP">SURVEY #$recordid</A> from $email</strong><BR>
	- Document Viewed: <A HREF="$catalogpage" TARGET=TOP>$documenttitle</A><BR>	
	- <em>Resource viewed on date: $date, Survey sent: $surveysent, Survey Received: $surveyreceived</em>
</TD></TR></TABLE>
<P>
<strong>Q1: How did you hear about this document?</strong> (Question added to survey January 2004)<BR><em><FONT COLOR=RED>$q7a</FONT></em>
<P>
<strong>Q2: How do you rate the quality of:</strong><BR>
<strong>-</strong> the document overall = <em><FONT COLOR=RED>$q1a_label ($q1a)</FONT></em><BR>
<strong>-</strong> the organization of the document = <em><FONT COLOR=RED>$q1b_label ($q1b)</FONT></em><BR>
<strong>-</strong> the timeliness of the document = <em><FONT COLOR=RED>$q1c_label ($q1c)</FONT></em><BR>
<strong>-</strong> the presentation of the document = <em><FONT COLOR=RED> $q1d_label ($q1d)</FONT></em>
<P>
<strong>Q3: How do you rate the document for meeting your needs?</strong>  = <em><FONT COLOR=RED>$q2a_label ($q2a)</FONT></em><BR>
<strong>-</strong> Comments:<BR>
<em><FONT COLOR=RED>$q2b</FONT></em>
<P>
<strong>Q4: Indicate the extent to which the document has had the following impact(s):</strong><BR>
<strong>-</strong> Increased your awareness of important new skills and knowledge. = <em><FONT COLOR=RED>$q3a_label ($q3a)</FONT></em><BR>
<strong>-</strong> Informed Decision-making = <em><FONT COLOR=RED>$q3b_label ($q3b)</FONT></em><BR>
<strong>-</strong> Enhanced Quality of Personal Practice = <em><FONT COLOR=RED>$q3c_label ($q3c)</FONT></em><BR>
<strong>-</strong> Positively Affected Student Performance = <em><FONT COLOR=RED>$q3d_label ($q3d)</FONT></em><BR>
<strong>-</strong> Comments:<BR>
<em><FONT COLOR=RED>$q3e</FONT></em>
<P>
<strong>Q5: How do you plan to use the information from this document?</strong> (Question added to survey January 2004)<BR>
<em><FONT COLOR=RED>$q8a</FONT></em>
<P>
<strong>Q6: How have you used the information from this document?</strong> (Question added to survey January 2004)<BR>
<em><FONT COLOR=RED>$q9a</FONT></em>
<P>
<strong>Q7: How can SEDL improve the document?</strong><BR>
<em><FONT COLOR=RED>$q4a</FONT></em>
<P>
<strong>Q8: Please tell us why you would or would not recommend this document to others.</strong><BR>
<em><FONT COLOR=RED>$q6a</FONT></em>
EOM
if ($q6a ne '') {
print<<EOM;
	<UL>
	Did the survey participant give permission to use their comments?<BR>
	<FONT COLOR=GREEN><strong>$permission_use_comments</strong></FONT>
	</UL>
EOM
}
$request_followup = "no" if ($request_followup eq '');

$name = "NOT INCLUDED BY USER" if ($name eq '');
$title = "NOT INCLUDED BY USER" if ($title eq '');
print<<EOM;
<P>
<strong>Q9: What issues should SEDL address in the future?</strong><BR>
<em><FONT COLOR=RED>$q5a</FONT></em>
<BR>
<BR>
User Name: $name<br>
User title: $title
<BR>
<BR>
<em>USER IP NUMBER: $ipnum<BR>
USER BROWSER TYPE: $browser</em>
<BR>
<BR>
<BR>

<TABLE BORDER=1 CELLPADDING=8 CELLSPACING=1>
<TR><TD>
<H4 ALIGN=CENTER>THIS AREA FOR SEDL USE ONLY</H4>
Did survey participant request follow-up contact with SEDL?<BR>
<FONT COLOR=RED>$request_followup</FONT>
<form action="clientsurveys.cgi" method=POST>
<P>
SEDL staff comments/notes on any post-survey contact with site visitor<BR>

<textarea name="new_staff_comments" rows=8 cols=55>$staff_comments</textarea><BR>
By staff member: <input name="new_staff_comments_by" size=20 VALUE="$staff_comments_by"> &nbsp; Date: $staff_comments_date<BR>
  <input type="hidden" name="surveyid" value="$recordid">
  <input type="hidden" name="location" value="add_staffcomments">
  <input type="submit" name="submit" value="Add Comments">
 </form>

</TD></TR>
</TABLE>
EOM
	} # END DB QUERY LOOP
}
#################################################################################
## END: LOCATION = SHOWDATA
#################################################################################



#################################################################################
## START: LOCATION = MAINTENANCE  (BL: OPTIMIZED DB QUERY 4/15/2006)
#################################################################################
if ($location eq 'maintenance') {

	#########################################################################
	## START: IF USER ENTERED A PDF TO IGNORE, ADD IT TO THE IGNORE DATABASE
	#########################################################################
	if ($badpdf ne '') {
		my $command = "INSERT INTO clientsurveyignore VALUES ('$badpdf')";

		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
	} 
	#########################################################################
	## END: IF USER ENTERED A PDF TO IGNORE, ADD IT TO THE IGNORE DATABASE
	#########################################################################


	#########################################################################
	## START: START: IF USER SUBMITTED A BAD ADDRESS, INDICATE THE SURVEY BOUNCED IN THE DATABASE
	#########################################################################
	if ($badaddress ne '') {
		if ($goodaddress eq '') {
			my $command = "UPDATE clientsurvey SET surveysent='bounced', surveysenttwice='bounced' WHERE email='$badaddress'";
			print "<LI>YESSEND $command" if $debug;
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
		} else {
			# ELSE FIX ADDRESS AND MARK TO RE-SEND
			my $command = "UPDATE clientsurvey SET surveysent='no', surveysenttwice='no', email='$goodaddress' WHERE email='$badaddress'";
			print "<LI>YESSEND $command" if $debug;
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
		}
	}
	#########################################################################
	## END: START: IF USER SUBMITTED A BAD ADDRESS, INDICATE THE SURVEY BOUNCED IN THE DATABASE
	#########################################################################


	#########################################################################
	## START: START: IF USER SUBMITTED A BAD ADDRESS, INDICATE THE SURVEY BOUNCED IN THE DATABASE
	#########################################################################
	if ($bad_surveynumber ne '') {
		if ($confirm_delete eq '') {
			print "<P><FONT COLOR=RED>You forgot to check the confirmation box.  Record deletion was aborted.</FONT>";
		} else {
			# ELSE FIX ADDRESS AND MARK TO RE-SEND
			my $command = "DELETE FROM clientsurvey WHERE recordid='$bad_surveynumber'";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
			print "<P><FONT COLOR=RED>Deleted $num_matches record. (ID: $bad_surveynumber)</FONT>";
		}
	}
	#########################################################################
	## END: START: IF USER SUBMITTED A BAD ADDRESS, INDICATE THE SURVEY BOUNCED IN THE DATABASE
	#########################################################################






print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN=TOP>
		<h1>Product Survey Report System</h1>
		<p><strong>Action: Data Maintenance</strong><BR>
		This page is used by OIC to flag bounced e-mails<BR> and fix e-mails entered incorrectly.</p>
		<P>
		<FONT COLOR=RED>Please do not use this page if you are not authorized to maintain the PDF Survey database.</FONT>
		</P>
	</TD>
	<TD>&nbsp;</TD>
	<TD VALIGN=TOP NOWRAP>
		<strong>Navigation options:</strong><BR>
		- <A HREF="clientsurveys.cgi?location=about">about the PDF Client Surveys</A><BR>
		- <A HREF="clientsurveys.cgi?location=summary_countdate">summary: surveys by date and department</A><BR>
		- <A HREF="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</A> (catalog items only)<BR>
		- <em><FONT COLOR=RED>maintenance menu</FONT></em><BR>
		- <A HREF="clientsurveys.cgi?location=datadump">download data to a file</A>
	</TD></TR>
</TABLE>
<P>
<H4>Indicate bounced mail from surveys</H4>
<P>
Simply type in the e-mail address below to mark its records in the database as an invalid e-mail address.
<P>
<form method="POST" action="clientsurveys.cgi">
	<UL>
	<input type="text" size="40" name="badaddress" value="">

	<input type=hidden name="location" value="maintenance">
	<input type="submit" value="Click to mark this as a bounced e-mail address">
	</FORM>
	</UL>


<P>
<H4>FIx an e-mail and re-send survey</H4>
<P>
Simply type in the two e-mail addresses below to change the e-mail address and re-send the survey invitation.
<P>
<form method="POST" action="clientsurveys.cgi">
	<UL>
	<TABLE>
	<TR><TD>Bad address:</TD><TD><input type="text" size="40" name="badaddress" value=""></TD></TR>
	<TR><TD>Good address:</TD><TD><input type="text" size="40" name="goodaddress" value=""></TD></TR>
	</TABLE>

	<input type=hidden name="location" value="maintenance"><BR>
	<input type="submit" value="Click to fix this e-mail">
	</FORM>
	</UL>


<H4>View/Add to the list of PDF documents that we don't want to send surveys to</H4>
<P>
This is a list of the documents that aren't true publications that we don't want to bother asking feedback about, 
such as the SEDL Application for Employment. (You can also enter a directory to exclude all PDFs in that directory.  i.e. http://www.sedl.org/nsf)
<P>
<form method="POST" action="clientsurveys.cgi">
	<UL>
	<input type="text" size="40" name="badpdf" value="">

	<input type=hidden name="location" value="maintenance">
	<input type="submit" value="Click to mark this PDF so no survey goes out">
	</FORM>
	</UL>

<P>
<UL>
EOM

## PRINT A LIST OF PDFs TO IGNORE
my $command = "select pdf2ignore from clientsurveyignore order by pdf2ignore";
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;
	while (my @arr = $sth->fetchrow) {
    	my ($pdf2ignore) = @arr;
	print "<LI>$pdf2ignore\n";
	} # END WHILE

print "</UL>\n";


print<<EOM;
<H4>Remove a Survey ID from the System</H4>
<P>
If you receive back a survey from a user who says they were unable to view the resource or 
did not use the document (and so cannot be surveyed about it), enter the survey ID number here 
to delete that survey from the database.
<P>
<form method="POST" action="clientsurveys.cgi">
	<UL>
	Survey ID to delete: <input type="text" size="10" name="bad_surveynumber" value=""><BR>
	<input type="checkbox" size="10" name="confirm_delete" value="yes"> Click here to confim this deletion.
<P>
	<input type=hidden name="location" value="maintenance">
	<input type="submit" value="Click to remove this survey ID">
	</FORM>
	</UL>
EOM



}
#################################################################################
## END: LOCATION = MAINTENANCE
#################################################################################





#################################################################################
## START: LOCATION = DATADUMP  (BL: OPTIMIZED DB QUERY 4/15/2006)
#################################################################################
if ($location eq 'datadump') {

print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN=TOP>
		<h1>Product Survey Report System</h1>
		<p>This page allows you to save survey data to a file.  You may want to download data to review the survey data or to grab a list of the e-mail addresses of the 
		users who took the survey for specific documents.</p>
		<P>
		You can use the form below to select survey data
		by date, department, or other criteria.</p>
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN=TOP NOWRAP>
		<strong>Navigation options:</strong><BR>
		- <A HREF="clientsurveys.cgi?location=about">about the PDF Client Surveys</A><BR>
		- <A HREF="clientsurveys.cgi?location=summary_countdate">summary: surveys by date and department</A><BR>
		- <A HREF="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</A> (catalog items only)<BR>
		- <em><FONT COLOR=RED>download data to a file</FONT></em>
	</TD></TR>
</TABLE>
<P>
<form action="clientsurveys.cgi" method=POST>
<strong>Select Surveys to Download</strong><BR>
Show surveys from after
	<SELECT NAME="search_month">
EOM
&print_month_menu($search_month); ## SUBROUTINE print_month_menu - send a previous value in () if any
print<<EOM;
	</SELECT>
	<SELECT NAME="search_date">
EOM
&print_day_menu($search_date); ## SUBROUTINE print_day_menu - send a previous value in () if any
print<<EOM;
	</SELECT>
	<SELECT NAME="search_year">
EOM
&print_year_menu(2002, 0, $search_year); ## SUBROUTINE print_year_menu - send a previous value in () if any
print<<EOM;
	</SELECT><BR>
Show PDFs from <SELECT NAME="summarysite">
EOM
&print_summary_site_menu($summarysite); ## SUBROUTINE print_year_menu - send a previous value in () if any
print<<EOM;
	</SELECT><BR>


Show <SELECT NAME="show_responses_only">
	 <OPTION VALUE="no"
EOM
print " SELECTED" if ($show_responses_only ne 'yes');
print<<EOM;
>all surveys (completed and incomplete)</OPTION>
	 <OPTION VALUE="yes"
EOM
print " SELECTED" if ($show_responses_only eq 'yes');
print<<EOM;
>completed surveys only</OPTION>
	 </SELECT><BR>


Sort by <SELECT NAME="sortby">
	 <OPTION VALUE="date"
EOM
print " SELECTED" if ($sortby eq 'date');
print<<EOM;
>date (descending)</OPTION>
	 <OPTION VALUE="email"
EOM
print " SELECTED" if ($sortby eq 'email');
print<<EOM;
>e-mail</OPTION>
	 </SELECT>
<P>
<input type="checkbox" name="confirm" value="yes"> Click here to confirm you are ready to save data to a file.
<P>
	<input type="hidden" name="location" value="datadump">
	<input type="submit" name="submit" value="Set Criteria and Save Data to File">
	</form>
EOM

	if ($confirm eq 'yes') {
		## OPEN DATA FILE
		open(DATAFILE,">/home/httpd/html/staff/quality/clientsurvey.xls");
		print DATAFILE "recordid\tsurveysent\tsurveysenttwice\tsurveyreceived\temail\tdate\tdocumenturl\tdocumentid\tdocumentgroup\tQuality of the document overall\tQuality of the organization of the document\tQuality of the timeliness of the document\tQuality of the presentation of the document\tHow do you rate the document for meeting your needs?\tComments on how the document met your needs\tDegree of impact: Increased your awareness of important new skills and knowledge\tDegree of impact: Informed Decision-making\tDegree of impact: Enhanced Quality of Personal Practice\tDegree of impact: Positively Affected Student Performance\tComments on degree of impact\tHow can SEDL improve the document?\tWhat issues should SEDL address in the future?\tTell why you would or would not recommend this document\tHow did you hear about this document?\tHow do you plan to use the information from this document?\tHow have you used the information from this document?\tunused field\tipnum\tbrowser\trequest_followup\tpermission_use_comments\tstaff_comments\tstaff_comments_by\tstaff_comments_date\n";

		my $command = "select * from clientsurvey";
		   $command .= " WHERE date > '$search_dateafter'";
		   $command .= " AND documenturl LIKE '$showpdf'" if ($showpdf ne '');
		   $command .= " AND surveyreceived NOT LIKE '0000-00-00'" if ($show_responses_only eq 'yes');
		   $command .= " AND request_followup LIKE '%request%'" if ($show_followuprequests ne '');

		   $command .= " AND documenturl like '%afterschool%'" if ($summarysite eq 'afterschool');
		   $command .= " AND documenturl like '%connections%'" if ($summarysite eq 'connections');
		   $command .= " AND documenturl like '%/es/%' " if ($summarysite eq '/es/');
		   $command .= " AND documenturl like '%loteced%' " if ($summarysite eq 'loteced');
   		   $command .= " AND documenturl like '%orc\/rr%' " if ($summarysite eq 'rapidresponses');
		   $command .= " AND (documenturl like '%scimast%' OR documenturl like '%scimath%') " if ($summarysite eq 'scimast');
		   $command .= " AND documenturl like '%secac%' " if ($summarysite eq 'secac');
		   $command .= " AND documenturl like '%reading%' " if ($summarysite eq 'reading');
		   $command .= " AND documenturl like '%sedl%' " if ($summarysite eq 'sedl');
    	$command .= " AND ((documenturl like '%sedl-letter%') OR (documenturl like '%sedletter%')) " if ($summarysite eq 'sedlletter');
    	$command .= " AND documenturl like '%insights%' " if ($summarysite eq 'insights');
    	$command .= " AND documenturl like '%compass%' " if ($summarysite eq 'compass');
    	$command .= " AND documenturl like '%change/issues%' " if ($summarysite eq 'change');

		   $command .= " order by recordid";

		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
			while (my @arr = $sth->fetchrow) {
				my ($recordid, $surveysent, $surveysenttwice, $surveyreceived, $email, $date, $documenturl, $documentid, $documentgroup, $q1a, $q1b, $q1c, $q1d, $q2a, $q2b, $q3a, $q3b, $q3c, $q3d, $q3e, $q4a, $q5a, $q6a, $q7a, $q8a, $q9a, $q10a, $request_followup, $permission_use_comments, $staff_comments, $staff_comments_by, $staff_comments_date, $ipnum, $browser) = @arr;

				$q1a = &cleanthis ($q1a);
				$q1b = &cleanthis ($q1b);
				$q1c = &cleanthis ($q1c);
				$q1d = &cleanthis ($q1d);
				$q2a = &cleanthis ($q2a);
				$q2b = &cleanthis ($q2b);
				$q3a = &cleanthis ($q3a);
				$q3b = &cleanthis ($q3b);
				$q3c = &cleanthis ($q3c);
				$q3d = &cleanthis ($q3d);
				$q3e = &cleanthis ($q3e);
				$q4a = &cleanthis ($q4a);
				$q5a = &cleanthis ($q5a);
				$q6a = &cleanthis ($q6a);
				$q7a = &cleanthis ($q7a);
				$q8a = &cleanthis ($q8a);
				$q9a = &cleanthis ($q9a);
				$q10a = &cleanthis ($q10a);
				$staff_comments = &cleanthis ($staff_comments);

			## SAVE RECORD TO DATA FILE
			print DATAFILE "$recordid\t$surveysent\t$surveysenttwice\t$surveyreceived\t$email\t$date\t$documenturl\tdocumentid\tdocumentgroup\t$q1a\t$q1b\t$q1c\t$q1d\t$q2a\t$q2b\t$q3a\t$q3b\t$q3c\t$q3d\t$q3e\t$q4a\t$q5a\t$q6a\t$q7a\t$q8a\t$q9a\t$q10a\t$request_followup\t$permission_use_comments\t$staff_comments\t$staff_comments_by\t$staff_comments_date\t$ipnum\t$browser\n";

			} # END DB QUERY LOOP


			## CLOSE DATA FILE
			close(DATAFILE);

		print "<P>The <A HREF=\"/staff/quality/clientsurvey.xls\">data file has been saved here</A>\n";
	} # END IF CONFIRM SAVE
}
#################################################################################
## END: LOCATION = DATADUMP
#################################################################################




## PRINT PAGE FOOTER
print "<P>LOCATION: $location</p>";
print "$htmltail";



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




sub place_label {
my $label = $_[0];
my $label_type = $_[1];

	if ($label_type eq '1') {
		$label = "excellent" if ($label eq '5');
		$label = "good" if ($label eq '4');
		$label = "fair" if ($label eq '3');
		$label = "poor" if ($label eq '2');
		$label = "very poor" if ($label eq '1');
	}
	if ($label_type eq '2') {
		$label = "Not Applicable" if ($label eq '0');
		$label = "Not at all" if ($label eq '1');
		$label = "Very Little" if ($label eq '2');
		$label = "Somewhat" if ($label eq '3');
		$label = "To some extent" if ($label eq '4');
		$label = "To a great extent" if ($label eq '5');
	}
   return($label);
}


sub date2standard {
my $date2transform = $_[0];
my ($thisyear,$thismonth,$thisdate) = split(/\-/,$date2transform);
   $date2transform = "$thismonth\/$thisdate\/$thisyear";
   $date2transform = "" if $date2transform eq '//';
	return($date2transform);
}



sub cleanthisfordb {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/‘/\\‘/g;
   $dirtyitem =~ s/’/\\’/g;
   $dirtyitem =~ s/"/\\"/g;
   return($dirtyitem);
}

## THIS SUBROUTINE MAKES SURE ANY SPECIAL CHARACTERS ARE BACKSLASHED BEFORE SENDING A COMMAND TO MYSQL
sub cleanthis {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem = $dirtyitem;
}




######################################
## START: SUBROUTINE print_month_menu
######################################
sub print_month_menu {
my $previous_selection = $_[0];
	my @months_value = ("", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	my @months_label = ("month", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	my $month_counter = "0";
	my $count_total_months = $#months_value;
		while ($month_counter <= $count_total_months) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $months_value[$month_counter]);
			print "\t<OPTION VALUE=\"$months_value[$month_counter]\" $selected>$months_label[$month_counter]</OPTION>";
			$month_counter++;
		} # END WHILE
######################################
} # END: SUBROUTINE print_month_menu
######################################

######################################
## START: SUBROUTINE print_day_menu
######################################
sub print_day_menu {
my $previous_selection = $_[0];
	my @days_value = ("", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31");
	my @days_label = ("day", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31");
	my $day_counter = "0";
	my $count_total_days = $#days_value;
		while ($day_counter <= $count_total_days) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $days_value[$day_counter]);
			print "\t<OPTION VALUE=\"$days_value[$day_counter]\" $selected>$days_label[$day_counter]</OPTION>";
			$day_counter++;
		} # END WHILE
######################################
} # END: SUBROUTINE print_day_menu
######################################


###########################################
# START: SUBROUTINE LIST_OF_YEARS
###########################################
sub print_year_menu {
# SMAPLE CALL &print_year_menu("1998", "0", $previous_value);
	my $yearlist_startyear = $_[0];
	my $endyear_offset = $_[1];
	   $endyear_offset = "0" if ($endyear_offset eq '');
	my $endyear_previous_value = $_[2];
	my $yearlist_endyear = $year + $endyear_offset;	
	my $yearlist_counter = $yearlist_startyear;	
			print "\t<OPTION VALUE=\"\">year</OPTION>";
		while ($yearlist_counter<= $yearlist_endyear) {
			print "\t<OPTION VALUE=\"$yearlist_counter\" ";
				if ($yearlist_counter eq $endyear_previous_value) {
					print "SELECTED"
				}
			print ">$yearlist_counter</OPTION>\n";
			$yearlist_counter++;
		} # END WHILE
} # END subroutine
###########################################
# END: SUBROUTINE LIST_OF_YEARS
###########################################



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


######################################
## START: SUBROUTINE print_summary_site_menu
######################################
sub print_summary_site_menu {
my $previous_selection = $_[0];
	my @sites_value = ("all", "afterschool", "loteced", "reading", "connections", "scimast", "sedl", "sedlletter");
	my @sites_label = ("all PDFs", "Afterschool", "LOTECED", "Reading", "Family & Community", "SCIMAST", "SEDL", "SEDL Letter");
	my $site_counter = "0";
	my $count_total_days = $#sites_value;
		while ($site_counter <= $count_total_days) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection eq $sites_value[$site_counter]);
			print "\t<OPTION VALUE=\"$sites_value[$site_counter]\" $selected>$sites_label[$site_counter]</OPTION>";
			$site_counter++;
		} # END WHILE
######################################
} # END: SUBROUTINE print_summary_site_menu
######################################


######################################
## START: SUBROUTINE compute_quarter
######################################
sub compute_quarter {
	my $incoming_date = $_[0];
	my $incoming_year = substr($incoming_date,0,4);
	my $incoming_month = substr($incoming_date,5,2);
	my $quarter_to_return = "";
	   $quarter_to_return = "01" if (($incoming_month eq '01') || ($incoming_month eq '02') || ($incoming_month eq '03'));
	   $quarter_to_return = "02" if (($incoming_month eq '04') || ($incoming_month eq '05') || ($incoming_month eq '06'));
	   $quarter_to_return = "03" if (($incoming_month eq '07') || ($incoming_month eq '08') || ($incoming_month eq '09'));
	   $quarter_to_return = "04" if (($incoming_month eq '10') || ($incoming_month eq '11') || ($incoming_month eq '12'));
	   $quarter_to_return = "$incoming_year\-$quarter_to_return";
	return($quarter_to_return);
######################################
} # END: SUBROUTINE compute_quarter
######################################


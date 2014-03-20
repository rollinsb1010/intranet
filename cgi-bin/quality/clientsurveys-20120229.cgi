#!/usr/bin/perl

#####################################################################################################
# Copyright 2002, 2012 by SEDL
#
# Updated by Brian Litke 02-15-2012
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
   $location = "main_menu" if ($location eq '');
#   $location = "showoptions";
my $surveyid = param('surveyid');

my $sortby = param('sortby');
   $sortby = "date, email" if (($sortby eq 'date') || ($sortby eq ''));

my $showdocid = param('showdocid');
my $search_category = param('search_category');
	$search_category = &commoncode::cleanthisfordb($search_category);
my $search_author = param('search_author');
	$search_author = &commoncode::cleanthisfordb($search_author);
my $search_assignment = param('search_assignment');
	$search_assignment = &commoncode::cleanthisfordb($search_assignment);

my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
####################################################
# START: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################
my $cookie_ss_session_id = ""; # TRACK SESSION ID
my $cookie_ss_staff_id = ""; # TRACK USER ID
	my(%cookies) = getCookies();
	foreach (sort(keys(%cookies))) {
		$cookie_ss_session_id = $cookies{$_} if (($_ eq 'ss_session_id') && ($session_id eq ''));
		$cookie_ss_staff_id = $cookies{$_} if ($_ eq 'staffid');
	}
	$cookie_ss_session_id = $session_id if (($cookie_ss_session_id eq '') && ($session_id ne '')); # IF NOT SESSION IN COOKIE, BUT ONE PASSED VIA URL, USE SESSION FROM URL
####################################################
# END: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################

my $show_followuprequests = param('show_followuprequests');
my $show_usecomments = param('show_usecomments');
my $show_address = param('show_address');
	$show_address = &commoncode::cleanthisfordb($show_address);

my $search_email = param('search_email');
	$search_email = &commoncode::cleanthisfordb($search_email);


###########################################################
## START: HANDLE SEARCH BY DATE VARIABLES
###########################################################
my $search_date_start = "";
my $search_date_end = "";

my $search_quarter = param('search_quarter');
	###########################################################
	## START: PARSE search_quarter into the search variables.
	###########################################################
	if ($search_quarter ne '') {
		my ($quarter_year, $quarter_quarter) = split(/\-/,$search_quarter);		
		if ($quarter_quarter eq '01') {
			$search_date_start = "$quarter_year\-01\-01";
			$search_date_end = "$quarter_year\-03\-31";
		} elsif ($quarter_quarter eq '02') {
			$search_date_start = "$quarter_year\-04\-01";
			$search_date_end = "$quarter_year\-06\-31";
		} elsif ($quarter_quarter eq '03') {
			$search_date_start = "$quarter_year\-07\-01";
			$search_date_end = "$quarter_year\-09\-31";
		} elsif ($quarter_quarter eq '04') {
			$search_date_start = "$quarter_year\-10\-01";
			$search_date_end = "$quarter_year\-12\-31";
		} # END IF/ELSE
	} # END IF
	###########################################################
	## END: PARSE search_quarter into the search variables.
	###########################################################
my $search_month = param('search_month');
	if ($search_month ne '') {
		my ($month_year, $month_month) = split(/\-/,$search_month);		
		$search_date_start = "$month_year\-$month_month\-01";
		$search_date_end = "$month_year\-$month_month\-31";
		$search_quarter = "";
	} # END IF

my $search_date_start_label = "";
   $search_date_start_label = &commoncode::date2standard($search_date_start) if ($search_date_start ne ''); # pretty date format for showing on screen
my $search_date_end_label = "";
   $search_date_end_label = &commoncode::date2standard($search_date_end) if ($search_date_end ne ''); # pretty date format for showing on screen
###########################################################
## END: HANDLE SEARCH BY DATE VARIABLES
###########################################################


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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("63"); # 63 is the PID for this page in the intranet database

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";


############################
## START: PRINT PAGE HEADER
############################
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
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
	$new_staff_comments = &commoncode::cleanthisfordb($new_staff_comments);
	$new_staff_comments_by = &commoncode::cleanthisfordb($new_staff_comments_by);
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
<TR><TD VALIGN="TOP">
		<h1 style="margin-top:0px;"><a href="clientsurveys.cgi">Product Survey Report System</a></h1>
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN="TOP" NOWRAP>
		<strong>Navigation options:</strong><br>
		- <a href="clientsurveys.cgi?location=main_menu">Main Menu: Dashboard</a><br>
		- <a href="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</a> (catalog items only)<br>
		- <em><span style="color:#ff0000;">about the PDF Client Surveys</span></em>
EOM
print "<br>		- <a href=\"clientsurveys.cgi?location=maintenance\">maintenance menu</a>" if ($cookie_ss_staff_id eq 'blitke');
print<<EOM;
	</TD></TR>
</TABLE>
<h2>About the PDF Client Surveys</h2>
<p>
<strong>How do we survey clients about the resources they access on the SEDL Web site?</strong><br>
Surveying clients about online resources is difficult, because we are unable 
to easily identify who accessed which online documents.  However, SEDL's web staff have created a process 
for prompting site visitors to give us their e-mail address 
before accessing an online SEDL resource. Although entering the e-mail address is not required to access the resource, 
if the user enters their e-mail address, it is saved to a database that will automatically send that 
person an e-mail after one week has passed. The e-mail will include a link back to our site where the 
user can fill out a survey about the publication they viewed.
</p>
<p>
<strong>What information is collected in the survey?</strong><br>
The <a href="/survey/pubs.cgi">survey questions</a> were devised by SEDL's Research and Evaluation Services 
unit for a previous REL contract. This database allows you to see 
</p>
	<ul>
	<li>a list of e-mail addresses volunteered by viewers of each publication,</li>
	<li>the ratio of how many surveys have been sent out vs. how many have been completed, and</li>
	<li>the users' responses to survey questions.</li>
	</ul>
<p>
<strong>How can I find the document I am looking for?</strong><br>
You can use the search interface to view surveys from a specific department or date range. You may also 
click through the list of documents by title to find the document.
</p>
<p>
<strong>How do I view  a user's response?</strong><br>
To view a user's actual responses, find the document by searching or by exploring 
one of the lists. You can view individual survey responses or view the combined results of a survey, which shows 
the distribution of responses for scaled answers and a collection of text responses for text-based answers.
</p>
EOM
}#################################################################################
## END: LOCATION = ABOUT
#################################################################################


#################################################################################
## START: LOCATION = main_menu
################################################################################# 
if ($location eq 'main_menu') {

	#############################################
	## START: PRINT PAGE HEADER
	#############################################
print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN="TOP">
		<h1 style="margin-top:0px;">Product Survey Report System</h1>
		<p>Welcome to the product survey dashboard that summarizes SEDL product surveys received from online clients.  
		You can search for product surveys by date, department, product, and author.</p>
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN="TOP" NOWRAP>
		<strong>Navigation options:</strong><br>
		- <span style="color:#ff0000;"><em>Main Menu: Dashboard</em></span><br>
		- <a href="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</a> (catalog items only)<br>
		- <a href="clientsurveys.cgi?location=about">about the PDF Client Surveys</a>
EOM
print "<br>		- <a href=\"clientsurveys.cgi?location=maintenance\">maintenance menu</a>" if ($cookie_ss_staff_id eq 'blitke');
print<<EOM;
	</TD></TR>
</TABLE>
EOM
	#############################################
	## END: PRINT PAGE HEADER
	#############################################

	my %staff_names;
	#######################################
	## START: GRAB LIST OF STAFF USER IDS
	#######################################
		my $command_get_staff = "select userid, firstname, lastname from staff_profiles order by userid";
		my $dsn_intranet = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn_intranet, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_staff) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		while (my @arr = $sth->fetchrow) {
			my ($this_id, $firstname, $lastname) = @arr;
			$staff_names{$this_id} = "$firstname $lastname";
		} # END DB QUERY LOOP
	#######################################
	## END: GRAB LIST OF STAFF USER IDS
	#######################################

	#############################################
	## START: GRAB PRODUCT CATALOG TITLES
	#############################################
	my %product_ids_by_title;
	   $product_ids_by_title{' title unknown'} = "0";
	my %product_titles_by_id;
	   $product_titles_by_id{'0'} = " title unknown";
	my %products_produced_by_staff_id;
	my %assignments_by_staff_id;
	my %staff_assignments_by_id;

	my $command = "select unique_id, title, title2, profile1, profile2, profile3, profile4, profile5, profile6, profile7, profile8, survey_feedback_staff_assignments from sedlcatalog";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
			my ($unique_id, $title, $title2, $profile1, $profile2, $profile3, $profile4, $profile5, $profile6, $profile7, $profile8, $survey_feedback_staff_assignments) = @arr;
#			print "<p>$survey_feedback_staff_assignments</p>" if ($survey_feedback_staff_assignments ne '');
			###########################################################
			## START: SPLIT UP STAFF ASSIGNMENTS AND COUNT INTO A HASH
			###########################################################
			if ($survey_feedback_staff_assignments ne '') {
				my @assignments = split(/\;/,$survey_feedback_staff_assignments);
				my $num_staff_assigned = $#assignments + 1; # COUNT TOTAL STAFF IN ARRAY
				my $counter = 0;
				while ($counter <= $#assignments) {
					$assignments[$counter] =~ s/ //gi;
					$assignments_by_staff_id{$assignments[$counter]}++ if ($assignments[$counter] ne '');

					$staff_assignments_by_id{$unique_id} .= ", " if ($staff_assignments_by_id{$unique_id} ne '');
					$staff_assignments_by_id{$unique_id} .= "$staff_names{$assignments[$counter]}";
					$counter++;
				} # END WHILE LOOP
					$staff_assignments_by_id{$unique_id} = "<span title=\"$staff_assignments_by_id{$unique_id}\">$num_staff_assigned</span>";
#					print "<p>$unique_id $staff_assignments_by_id{$unique_id}</p>" if ($survey_feedback_staff_assignments ne '');
			} else {
				$staff_assignments_by_id{$unique_id} = "<span style=\"color:#cc0000;\">N/A</span>";
			}
			###########################################################
			## END: SPLIT UP STAFF ASSIGNMENTS AND COUNT INTO A HASH
			###########################################################
	
			$product_titles_by_id{$unique_id} = "$title";
			$product_titles_by_id{$unique_id} .= ": $title2" if $title2 ne '';
			$product_ids_by_title{$product_titles_by_id{$unique_id}} = $unique_id;
			if ($profile1 ne '') {
				$profile1 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile1}++;
			}
			if ($profile2 ne '') {
				$profile2 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile2}++;
			}
			if ($profile3 ne '') {
				$profile3 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile3}++;
			}
			if ($profile4 ne '') {
				$profile4 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile4}++;
			}
			if ($profile5 ne '') {
				$profile5 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile5}++;
			}
			if ($profile6 ne '') {
				$profile6 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile6}++;
			}
			if ($profile7 ne '') {
				$profile7 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile7}++;
			}
			if ($profile8 ne '') {
				$profile8 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile8}++;
			}
		} # END DB QUERY
	#############################################
	## END: GRAB PRODUCT CATALOG TITLES
	#############################################


	#############################################
	## START: GRAB SUMMARY DATA TO DISPLAY
	#############################################
	my %summary_byquarter;
	my %summary_bymonth;

	my %summary_bydoccategory;
	my %summary_bydoccategory_response_ratio;
	my %summary_bydoccategory_followups_req;
	my %summary_bydoccategory_commentpermissions;
	my %summary_bydoccategory_pending_send;
	
	my %summary_bydocid;
	my %summary_bydocid_response_ratio;
	my %summary_bydocid_followups_req;
	my %summary_bydocid_commentpermissions;
	my %summary_bydocid_pending_send;
	my %summary_bydocid_sentvalid;

	my $command_summary = "select * from clientsurvey_summary";
										
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_summary) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $last_email = "";
		while (my @arr = $sth->fetchrow) {
			my ($css_uniqueid, $css_summarytype, $css_summarytype_id, $css_count_sent, $css_count_sentvalid, $css_count_bounced, $css_count_nosend, $css_count_received, $css_response_ratio, $css_followups_req, $css_followups_made, $css_commentpermissions, $css_pending_send, $css_last_updated) = @arr;

				$summary_bymonth{$css_summarytype_id} = $css_count_received if ($css_summarytype eq 'bymonth');

				if ($css_summarytype eq 'byquarter') {
					$summary_byquarter{$css_summarytype_id} = $css_count_received;
#					print "<br>$css_count_received in $css_summarytype_id";
				} # END IF

				if ($css_summarytype eq 'bydoccategory') {
					$summary_bydoccategory{$css_summarytype_id} = $css_count_received ;
					$summary_bydoccategory_response_ratio{$css_summarytype_id} = $css_response_ratio;
					$summary_bydoccategory_followups_req{$css_summarytype_id} = $css_followups_req;
					$summary_bydoccategory_commentpermissions{$css_summarytype_id} = $css_commentpermissions;
					$summary_bydoccategory_pending_send{$css_summarytype_id} = $css_pending_send;
				} # END IF

				if ($css_summarytype eq 'bydocid') {
					$summary_bydocid{$product_titles_by_id{$css_summarytype_id}} = $css_count_received ;
					$summary_bydocid_response_ratio{$product_titles_by_id{$css_summarytype_id}} = $css_response_ratio;
					$summary_bydocid_followups_req{$product_titles_by_id{$css_summarytype_id}} = $css_followups_req;
					$summary_bydocid_commentpermissions{$product_titles_by_id{$css_summarytype_id}} = $css_commentpermissions;
					$summary_bydocid_pending_send{$product_titles_by_id{$css_summarytype_id}} = $css_pending_send;
					$summary_bydocid_sentvalid{$product_titles_by_id{$css_summarytype_id}} = $css_count_sentvalid;
				} # END IF
		} # END DB QUERY LOOP

	#############################################
	## END: GRAB SUMMARY DATA TO DISPLAY
	#############################################


print<<EOM;
<table border="1" cellpadding="8" cellspacing="0" width="100%">
<tr><td valign="top" width="50%">
		<h2 style="margin-top:0px;">Surveys by Quarter and Month</h2>
		<table cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
<tr>
EOM
my $quarter = "";
    foreach $quarter (sort (keys(%summary_byquarter))) {
		my ($t_year, $t_quarter) = split(/\-/,$quarter);
		   $t_quarter =~ s/0//gi;
		   $t_quarter = "Q$t_quarter";
		my $height_ratio = ($summary_byquarter{$quarter} / 3);
		$height_ratio = &format_number($height_ratio, "0","no");
		my $bar_color = "purple";
		   $bar_color = "purple" if (($t_year eq 2003) || ($t_year eq 2007) || ($t_year eq 2011));
		   $bar_color = "blue" if (($t_year eq 2004) || ($t_year eq 2008) || ($t_year eq 2012));
		   $bar_color = "green" if (($t_year eq 2005) || ($t_year eq 2009));
		   $bar_color = "red" if (($t_year eq 2002) || ($t_year eq 2006) || ($t_year eq 2010));;
		if ($summary_byquarter{$quarter} != 0) {
			print "<td valign=\"bottom\" align=\"center\"><a href=\"clientsurveys.cgi?location=listdocumentsbytitle&amp;search_quarter=$quarter\"><IMG style=\"padding-left:1px;\" 
					SRC=\"/images/pixel-$bar_color\.gif\" 
					height=\"$height_ratio\" width=\"7\" 
					alt=\"$summary_byquarter{$quarter} in $t_quarter\/$t_year\" 
					title=\"$summary_byquarter{$quarter} in $t_quarter\/$t_year\" 
					class=\"noBorder\"></a></td>";
		}
    } # END FOREACH


print<<EOM;
		<tr style="text-align:center;">
			<td colspan="3">2002</td>
			<td colspan="4">2003</td>
			<td colspan="4">2004</td>
			<td colspan="4">2005</td>
			<td colspan="4">2006</td>
			<td colspan="4">2007</td>
			<td colspan="4">2008</td>
			<td colspan="4">2009</td>
			<td colspan="4">2010</td>
			<td colspan="4">2011</td>
			<td colspan="4">2012</td>
		</tr>
		</table>
		<br>
		<form action="clientsurveys.cgi" method="GET">
		<select name="search_month" id="search_month">
		<option value="">select a month to view</option>
EOM
    foreach my $mnth (reverse (sort (keys(%summary_bymonth)))) {
		if ($summary_bymonth{$mnth} != 0) {
			my ($t_year, $t_month) = split(/\-/,$mnth);
			my $month_label = "$t_month\/$t_year";
			print "<option value=\"$mnth\">$summary_bymonth{$mnth} responses in $month_label</option>\n";
		}
    } # END FOREACH

print<<EOM;
		</select>
		<input type="hidden" name="location" value="listsurveys">
		<input type="submit" value="Go">
		</form>
	</td>

	<td valign="top" width="50%">
		<h2 style="margin-top:0px;">Surveys by Subject</h2>

		<div style="width:352px; height:190px; overflow: scroll; background-color:#ffffff;border:0px;padding:0px;margin-top:-4px;">
			<table border="1" cellpadding="2" cellspacing="0">
			<tr style="background-color:#ebebeb;">
				<td>Category</td>
				<td style="font-size:8px;line-height:10px;">Surveys<br>Completed</td>
				<td style="font-size:8px;line-height:10px;">Response<br>Ratio %</td>
				<td style="font-size:8px;line-height:10px;">Client<br>Requested<br>Followup</td>
				<td style="font-size:8px;line-height:10px;">Permission<br>to use<br>Comments</td>
				<td style="font-size:8px;line-height:10px;">Pending<br>Mailout</td>
			</tr>
EOM
    foreach my $category (sort (keys(%summary_bydoccategory))) {
		if ($summary_bydoccategory{$category} != 0) {
print<<EOM;
			<tr><td><a href="clientsurveys.cgi?location=listdocumentsbytitle&amp;search_category=$category">$category</a></td>
				<td align="right"><strong>$summary_bydoccategory{$category}</strong></td>
				<td align="right">$summary_bydoccategory_response_ratio{$category}\%</td>
				<td align="right">$summary_bydoccategory_followups_req{$category}</td>
				<td align="right">$summary_bydoccategory_commentpermissions{$category}</td>
				<td align="right">$summary_bydoccategory_pending_send{$category}</td>
			</tr>
EOM
		}
    } # END FOREACH

print<<EOM;
			</table>
		</div>

	</td>
</tr>

<tr><td valign="top" colspan="2">

		<h2 style="margin-top:0px;">Surveys by Product</h2>
		<div style="width:100%; height:300px; overflow: scroll; background-color:#ffffff;border:0px;padding:0px;margin-top:-4px;">
			<table border="1" cellpadding="2" cellspacing="0">
			<tr style="background-color:#ebebeb;">
				<td>Product Title</td>
				<td style="font-size:8px;line-height:10px;">Survey<br>Invites Sent</td>
				<td style="font-size:8px;line-height:10px;">Surveys<br>Completed</td>
				<td style="font-size:8px;line-height:10px;">Response<br>Ratio %</td>
				<td style="font-size:8px;line-height:10px;">Client<br>Requested<br>Followup</td>
				<td style="font-size:8px;line-height:10px;">Permission<br>to use<br>Comments</td>
				<td style="font-size:8px;line-height:10px;">Pending<br>Mailout</td>
				<td style="font-size:8px;line-height:10px;">Staff<br>Assigned<br>To Review</td>
			</tr>
EOM
my $counter = 1;
    foreach my $product_id (sort (keys(%summary_bydocid))) {

		if ($counter == 10) {
			$counter = 0;
print<<EOM;
			<tr style="background-color:#ebebeb;">
				<td>Product Title</td>
				<td style="font-size:8px;line-height:10px;">Survey<br>Invites Sent</td>
				<td style="font-size:8px;line-height:10px;">Surveys<br>Completed</td>
				<td style="font-size:8px;line-height:10px;">Response<br>Ratio %</td>
				<td style="font-size:8px;line-height:10px;">Client<br>Requested<br>Followup</td>
				<td style="font-size:8px;line-height:10px;">Permission<br>to use<br>Comments</td>
				<td style="font-size:8px;line-height:10px;">Pending<br>Mailout</td>
				<td style="font-size:8px;line-height:10px;">Staff<br>Assigned<br>To Review</td>
			</tr>
EOM
		}

#		if ($summary_bydocid{$product_id} != 0) {
		my $this_doc_id = $product_ids_by_title{$product_id};
print<<EOM;
			<tr><td>$product_id</td>
				<td align="right">
				<a href="clientsurveys.cgi?location=listsurveys&amp;showdocid=$this_doc_id"><span style="color:#666666;">$summary_bydocid_sentvalid{$product_id}</span></a>
				</td>
				<td style="text-align:center;">
EOM
	if ($summary_bydocid{$product_id} == 0) {
print<<EOM;
<p>$summary_bydocid{$product_id}</p>
EOM
	} else {
print<<EOM;
<p style="padding:0px;margin:2px 0 5px 0;"><strong><a href="clientsurveys.cgi?location=listsurveys&amp;showdocid=$this_doc_id">$summary_bydocid{$product_id}</a></strong></p>
EOM
		if ($summary_bydocid{$product_id} > 0) {
print<<EOM;
					<p style="padding:0px;margin:0px;"><a href="clientsurveys.cgi?location=summary_responses_singledoc&amp;showdocid=$this_doc_id">combined</a></p>
EOM
		} # END IF
	} # END IF
print<<EOM;
				</td>
				<td align="right">$summary_bydocid_response_ratio{$product_id}\%</td>
				<td align="right">$summary_bydocid_followups_req{$product_id}</td>
				<td align="right"><a href="clientsurveys.cgi?location=listsurveys&amp;showdocid=$this_doc_id&amp;show_usecomments=yes">$summary_bydocid_commentpermissions{$product_id}</a></td>
				<td align="right">$summary_bydocid_pending_send{$product_id}</td>
				<td align="right">$staff_assignments_by_id{$this_doc_id}</td>
			</tr>
EOM
#		}
		$counter++;
    } # END FOREACH

print<<EOM;
			</table>
		</div>
	</td>
</tr>
<tr><td valign="top" width="50%">
		<div style="tmargin-top:6px;">
		<h2 style="margin-top:0px;">Surveys by Author</h2>
		<form action="clientsurveys.cgi" method="GET">
			<select name="search_author" id="search_author">
			<option value="">select a resource author</option>
EOM
my $pulldownmenu_for_staff_assignments = "";

my $dsn_intranet = "DBI:mysql:database=intranet;host=localhost";
my $command = "select firstname, lastname, email, phone, userid, phoneext, department_abbrev from staff_profiles order by lastname";
my $dbh = DBI->connect($dsn_intranet, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
	while (my @arr = $sth->fetchrow) {
    	my ($firstname, $lastname, $email, $phone, $userid, $phoneext, $department_abbrev) = @arr;
    	$products_produced_by_staff_id{$userid} = "0" if ($products_produced_by_staff_id{$userid} == 0);
		print "<option value=\"$userid\" ";
		print "SELECTED" if ($cookie_ss_staff_id eq $userid);
		print ">$firstname $lastname (author for $products_produced_by_staff_id{$userid} resources)</option>\n";
		
		## PREPARE CODE FOR SEARCH BY ASSIGNMENT
		if ($assignments_by_staff_id{$userid} > 0) {
			$pulldownmenu_for_staff_assignments .= "<option value=\"$userid\" ";
			$pulldownmenu_for_staff_assignments .= "SELECTED" if ($cookie_ss_staff_id eq $userid);
			$pulldownmenu_for_staff_assignments .= ">$firstname $lastname (assigned to $assignments_by_staff_id{$userid} resources)</option>\n";
		} # END IF
		
	} # END DB QUERY LOOP
print<<EOM;
			</select>
		
		<input type="hidden" name="location" value="listdocumentsbytitle">
		<input type="submit" value="Go">
		</form>

		<br>
		<h2 style="margin-top:0px;">Surveys by Staff Assignment</h2>
		<form action="clientsurveys.cgi" method="GET">
			<select name="search_assignment" id="search_assignment">
			<option value="">select a staff assignment</option>
			$pulldownmenu_for_staff_assignments
			</select>
		<input type="hidden" name="location" value="listdocumentsbytitle">
		<input type="submit" value="Go">
		</form>

		</div>

		<img src="/staff/quality/images/byauthor.jpg" style="margin-top:16px;" alt="View Survey Responses by Document Author"><br>
	</td>

	<td valign="top" width="50%">
		<h2 style="margin-top:0px;">Search by Client e-mail</h2>
		<p>You can enter a full or partial e-mail address to see if that person has been surveyed.</p>
		<form action="clientsurveys.cgi" method="GET">
		<div style="margin-left:25px;">
		<input type="text" size="25" name="search_email" id="search_email" value="">

		<input type="hidden" name="location" value="listsurveys"><br>
		<input type="submit" value="Find this e-mail">
		</div>
		</form>
	</td>
</tr>
</table>

EOM

}  
#################################################################################
## END: LOCATION = main_menu
#################################################################################


#################################################################################
## START: LOCATION = listsurveys  (BL: OPTIMIZED DB QUERY 4/15/2006)
#################################################################################
if ($location eq 'listsurveys') {

print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN="TOP">
		<h1 style="margin-top:0px;3"><a href="clientsurveys.cgi">Product Survey Report System</a></h1>
		<p>On this page, you can access individual surveys.</p>
			<UL>
			<li>Click the "View this survey" link to see the survey the user filled out and their responses.</li>
			</UL>
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN="TOP" NOWRAP>
		<strong>Navigation options:</strong><br>
		- <a href="clientsurveys.cgi?location=main_menu">Main Menu: Dashboard</a><br>
		- <a href="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</a> (catalog items only)<br>
		- <a href="clientsurveys.cgi?location=about">about the PDF Client Surveys</a>
EOM
print "<br>		- <a href=\"clientsurveys.cgi?location=maintenance\">maintenance menu</a>" if ($cookie_ss_staff_id eq 'blitke');
print<<EOM;
	</TD></TR>
</TABLE>
<P>
<TABLE>
<TR><TD ROWSPAN="6" VALIGN="TOP">
<h2>List Survey Responses by URL</h2>
<span style="color:#996600;"><em><strong>Note:</strong> We told the Web site visitors their e-mail addresses would only be used to contact 
	them about the product they viewed, not for any other purpose.</em></span>

</TD>
	<TD COLSPAN="2"><strong>Legend</strong></TD></TR>
<TR><TD VALIGN="TOP"><IMG SRC="images/new.gif" ALT="Status: New, pending send" class="noBorder"></TD><TD>New entry - Survey not sent yet</TD></TR>
<TR><TD VALIGN="TOP"><IMG SRC="images/surveysent.gif" ALT="Status: Sent" class="noBorder"></TD><TD>Survey Sent</TD></TR>
<TR><TD VALIGN="TOP"><IMG SRC="images/surveyreceived.gif" ALT="Status: Received" class="noBorder"></TD><TD>Survey filled out - click individual checkmarks next to an entry below to view data</TD></TR>
<TR><TD VALIGN="TOP"><IMG SRC="images/surveynotsent.gif" ALT="Status: Not Sent" class="noBorder"></TD><TD>Survey will never be sent - Document was not in SEDL product catalog at time of survey</TD></TR>
<TR><TD VALIGN="TOP"><IMG SRC="images/surveybounced.gif" ALT="Status: Bounced" class="noBorder"></TD><TD>User e-mail bounced/invalid</TD></TR>
</TABLE>
EOM
	if ($search_date_start_label ne '') {
print<<EOM;
<p class="info">
You are searching by date, starting from $search_date_start_label and ending $search_date_end_label.
</p>
EOM
	}

	my $last_title_label = "";
	my $lastdocumenturl = "";
	my $lastdocumentid = "";
	my $sortby_label = $sortby;
	   $sortby_label =~ s/date/clientsurvey\.date DESC/g;

	my $command = "select clientsurvey.recordid, clientsurvey.surveysent, clientsurvey.surveysenttwice, clientsurvey.surveyreceived, clientsurvey.email, clientsurvey.date, clientsurvey.documenturl, clientsurvey.documentid, clientsurvey.documentgroup, clientsurvey.request_followup, clientsurvey.permission_use_comments, clientsurvey.staff_comments, clientsurvey.q6a, 
					sedlcatalog.title, sedlcatalog.title2
					FROM clientsurvey, sedlcatalog
					WHERE clientsurvey.documentid=sedlcatalog.unique_id";
		$command .= " AND clientsurvey.documentid = '$showdocid'" if ($showdocid ne '');
		$command .= " AND clientsurvey.date >= '$search_date_start'" if ($search_date_start ne '');
		$command .= " AND clientsurvey.date <= '$search_date_end'" if ($search_date_end ne '');
		$command .= " AND email LIKE '%$show_address%'" if ($show_address ne '');
		$command .= " AND clientsurvey.surveyreceived NOT LIKE '0000-00-00'" if ($show_responses_only eq 'yes');
		$command .= " AND clientsurvey.request_followup LIKE '%request%'" if ($show_followuprequests ne '');
		$command .= " AND clientsurvey.permission_use_comments LIKE 'yes'" if ($show_usecomments eq 'yes');
		$command .= " AND clientsurvey.email LIKE '%$search_email%'" if ($search_email ne '');

		$command .= " AND clientsurvey.documentgroup LIKE '$search_category'" if ($search_category ne '');




	   $command .= " order by clientsurvey.documenturl, $sortby_label";
#print "<p class=\"info\">COMMAND: $command</p>";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
print "<p class=\"info\">Displaying $num_matches surveys matching your search criteria.</p>";
	my $count = "1";

	while (my @arr = $sth->fetchrow) {
		my ($recordid, $surveysent, $surveysenttwice, $surveyreceived, $email, $date, $documenturl, $documentid, $documentgroup, $request_followup, $permission_use_comments, $staff_comments, $q6a, $title, $title2) = @arr;
			my $title_label = "$title";
   				$title_label = "$title: $title2" if ($title2 ne ''); 
   				$title_label = &commoncode::cleanaccents2html($title_label);

		my $image = "";
		$image ="<a href=\"/survey/pubs.cgi?e=$email&id=$recordid\"><IMG SRC=\"images/new.gif\" ALT=\"Status: New, pending send\" class=\"noBorder\"></a>" if $surveysent eq 'no';
		$image ="<IMG SRC=\"images/surveysent.gif\" ALT=\"Status: Sent\">" if $surveysent =~ 'yes';
		$image ="<IMG SRC=\"images/surveybounced.gif\" ALT=\"Status: Bounced\">" if $surveysent eq 'bounced';
		$image ="<IMG SRC=\"images/surveynotsent.gif\" ALT=\"Status: Not sent\">" if $surveysent eq 'nosend';
		$image ="<a href=\"/staff/quality/clientsurveys.cgi?location=showdata&amp;surveyid=$recordid\"><IMG SRC=\"images/surveyreceived.gif\" ALT=\"Status\" class=\"noBorder\"> view this survey</a>" if (($surveyreceived ne '') &&($surveyreceived ne '0000-00-00'));
		$documenturl = "(URL not specified)" if ($documenturl eq '');
		if ($documentid ne $lastdocumentid) {
			if ($lastdocumentid eq '') {
				print "<br><TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"0\" CELLPADDING=\"2\">
				<TR><TD colspan=\"5\" class=\"small\"><em>$title_label</em><br>
						$documenturl<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(view <a href=\"/staff/quality/clientsurveys.cgi?location=summary_responses_singledoc&amp;showdocid=$documentid&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite&amp;show_followuprequests=$show_followuprequests\">combined survey results</a>)</TD>
					<TD class=\"small\">Did survey<br>contain a<br>request for<br>follow-up?</TD>
					<TD class=\"small\">Are there<br>SEDL staff<br>post-survey<br>comments?</TD>
					<TD class=\"small\">User gave<br>permission<br>to use<br>comments<br></TD></TR>\n";
			}
			if ($lastdocumentid ne '') {
				print "</TABLE><br><TABLE WIDTH=\"100%\" BORDER=\"1\" CELLSPACING=\"0\" CELLPADDING=\"2\">
						<TR><TD colspan=\"5\" class=small><em>$title_label</em><br>$documenturl<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(view <a href=\"/staff/quality/clientsurveys.cgi?location=summary_responses_singledoc&amp;showdocid=$documentid&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite&amp;show_followuprequests=$show_followuprequests\">combined survey results</a>)</TD>
							<TD class=\"small\">Did survey<br>contain a<br>request for<br>follow-up?</TD>
							<TD class=\"small\">Are there<br>SEDL staff<br>post-survey<br>comments?</TD>
							<TD class=\"small\">User gave<br>permission<br>to use<br>comments<br></TD></TR>\n";
			} # END IF
		} # END IF


		$staff_comments = "yes" if ($staff_comments ne '');
		#$permission_use_comments = "yes" if ($permission_use_comments =~ '\(us');
		#$permission_use_comments = "yes in 6a" if ($q6a =~ '\(user agree');
		$date = &commoncode::date2standard($date);
			print "<TR><TD>$count</TD>
						<TD NOWRAP>$image</TD>
						<TD class=\"small\">$date</TD>
						<TD class=\"small\">$email</TD>
						<TD class=\"small\">ID: $recordid</TD>
						<TD>$request_followup</TD>
						<TD>$staff_comments</TD>
						<TD>$permission_use_comments</TD></TR>\n";
		if ($permission_use_comments eq 'yes') {
			print "<tr><td colspan=\"3\">&nbsp;</td><td colspan=\"5\"><em><span style=\"color:#009900;\">$q6a</span></em></td></tr>";
		}
		$lastdocumenturl = $documenturl;
		$lastdocumentid = $documentid;
		$last_title_label = $title_label;
		$count++;
	} # END DB QUERY LOOP

	print "</TABLE>";

	if ($showdocid eq '0') {
		#################################################
		## START: DISPLAY SURVEY RECORDS CODED AS ID = 0
		#################################################
		print "<p class=\"info\">You searched for unclassified \"title unknown\" product surveys. This report would normally display an error and not display the related surveys, because the resource the user is being surveyed about 
		is not found in the SEDL Catalog. However, you can review the URLs for the resources that were surveyed in the list below.  
		This list should only include surveys from 2/2011 and before, because at that date, we added a method that stopped the survey from interacting with resource URLs not in the SEDL catalog.</p>";

	my $command = "select clientsurvey.recordid, clientsurvey.surveysent, clientsurvey.surveysenttwice, clientsurvey.surveyreceived, clientsurvey.email, clientsurvey.date, clientsurvey.documenturl, clientsurvey.documentid, clientsurvey.documentgroup, clientsurvey.request_followup, clientsurvey.permission_use_comments, clientsurvey.staff_comments
					FROM clientsurvey
					WHERE clientsurvey.documentid = '$showdocid'";

	   $command .= " order by clientsurvey.documenturl, $sortby_label";

#print "<p class=\"info\">COMMAND: $command</p>";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#print "<p class=\"info\">MATCHES: $num_matches</p>";
		print "<ol>\n";
		my $count = "1";

		while (my @arr = $sth->fetchrow) {
			my ($recordid, $surveysent, $surveysenttwice, $surveyreceived, $email, $date, $documenturl, $documentid, $documentgroup, $request_followup, $permission_use_comments, $staff_comments) = @arr;
my $highlight_info = "";
   $highlight_info = " class=\"info\"" if ($surveyreceived ne '0000-00-00');
print<<EOM;
<li $highlight_info>
Survey Record ID = $recordid<br>
Survey sent = $surveysent<br>
Survey senttwice = $surveysenttwice<br>
Survey received = $surveyreceived<br>
e-mail = $email<br>
Date = $date<br>
Document URL = $documenturl<br>
Document ID = $documentid<br>
Document Group = $documentgroup<br>
Request Followup = $request_followup<br>
Permission_use_comments = $permission_use_comments<br>
Staff Comments = $staff_comments<br>
</li>
EOM
		} # END DB QUERY LOOP
		print "</ol>\n";
		#################################################
		## END: DISPLAY SURVEY RECORDS CODED AS ID = 0
		#################################################
	} # END IF

}  
#################################################################################
## END: LOCATION = listsurveys
#################################################################################


#################################################################################
## START: LOCATION = summary_responses_singledoc
#################################################################################
if ($location eq 'summary_responses_singledoc') {
## PRINT PAGE HEADER
print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN="TOP">
		<h1 style="margin-top:0px;3"><a href="clientsurveys.cgi">Product Survey Report System</a></h1>
		<p>Action: View Survey Data<br>
		(survey responses are in red)</p> 
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN="TOP" NOWRAP>
		<strong>Navigation options:</strong><br>
		- <a href="clientsurveys.cgi?location=main_menu">Main Menu: Dashboard</a><br>
		- <a href="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</a> (catalog items only)<br>
		- <a href="clientsurveys.cgi?location=about">about the PDF Client Surveys</a>
EOM
print "<br>		- <a href=\"clientsurveys.cgi?location=maintenance\">maintenance menu</a>" if ($cookie_ss_staff_id eq 'blitke');
print<<EOM;
	</TD></TR>
</TABLE>
EOM
		#############################################
		## START: DB QUERY TO GRAB THE DOCUMENT NAME
		#############################################
		my $documenttitle = "Error - Document not found in SEDL Catalog";
		my $catalogpage = "";

		my $command = "select title, title2, onlineid from sedlcatalog 
						where (unique_id = '$showdocid')";
#print "<p class=\"info\">COMMAND: $command</p>";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#print "<p class=\"info\">MATCHES: $num_matches</p>";
		while (my @arr = $sth->fetchrow) {
			my ($title, $title2, $onlineid) = @arr;

			$documenttitle = "$title";
			$documenttitle .= ": $title2" if $title2 ne '';
			$documenttitle = &commoncode::cleanaccents2html($documenttitle);

			$catalogpage = "/pubs/catalog/items/$onlineid";
		} # END DB QUERY
		#############################################
		## END: DB QUERY TO GRAB THE DOCUMENT NAME
		#############################################

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
	my $command = "select * from clientsurvey WHERE documentid = '$showdocid' 
				AND date >= '$search_date_start'";
		$command .= " AND clientsurvey.date >= '$search_date_start'" if ($search_date_start ne '');
		$command .= " AND clientsurvey.date <= '$search_date_end'" if ($search_date_end ne '');
		$command .= " AND surveyreceived NOT LIKE '0000-00-00'";
			
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#print "<p class=\"info\">COMMAND: $command<br><br>MATCHES: $num_matches</p>";
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

		$surveyreceived = &commoncode::date2standard($surveyreceived); # pretty date format for showing on screen
		$text_q7a .= "<LI>$q7a <span style=\"color:#999999;font-size:9px;\">[survey#<a href=\"/staff/quality/clientsurveys.cgi?location=showdata&amp;surveyid=$recordid\"><span style=\"color:#999999;\">$recordid</span></a> $surveyreceived]</span></li>" if ($q7a ne ''); 
		$text_q2b .= "<LI>$q2b <span style=\"color:#999999;font-size:9px;\">[survey#<a href=\"/staff/quality/clientsurveys.cgi?location=showdata&amp;surveyid=$recordid\"><span style=\"color:#999999;\">$recordid</span></a> $surveyreceived]</span></li>" if ($q2b ne ''); 
		$text_q3e .= "<LI>$q3e <span style=\"color:#999999;font-size:9px;\">[survey#<a href=\"/staff/quality/clientsurveys.cgi?location=showdata&amp;surveyid=$recordid\"><span style=\"color:#999999;\">$recordid</span></a> $surveyreceived]</span></li>" if ($q3e ne ''); 
		$text_q8a .= "<LI>$q8a <span style=\"color:#999999;font-size:9px;\">[survey#<a href=\"/staff/quality/clientsurveys.cgi?location=showdata&amp;surveyid=$recordid\"><span style=\"color:#999999;\">$recordid</span></a> $surveyreceived]</span></li>" if ($q8a ne ''); 
		$text_q9a .= "<LI>$q9a <span style=\"color:#999999;font-size:9px;\">[survey#<a href=\"/staff/quality/clientsurveys.cgi?location=showdata&amp;surveyid=$recordid\"><span style=\"color:#999999;\">$recordid</span></a> $surveyreceived]</span></li>" if ($q9a ne ''); 
		$text_q4a .= "<LI>$q4a <span style=\"color:#999999;font-size:9px;\">[survey#<a href=\"/staff/quality/clientsurveys.cgi?location=showdata&amp;surveyid=$recordid\"><span style=\"color:#999999;\">$recordid</span></a> $surveyreceived]</span></li>" if ($q4a ne ''); 
		$text_q6a .= "<LI>$permission_flag_bolding$q6a <span style=\"color:#999999;font-size:9px;\">[survey#<a href=\"/staff/quality/clientsurveys.cgi?location=showdata&amp;surveyid=$recordid\"><span style=\"color:#999999;\">$recordid</span></a> $surveyreceived]</span> $permission_flag</li>" if ($q6a ne ''); 
		$text_q5a .= "<LI>$q5a <span style=\"color:#999999;font-size:9px;\">[survey#<a href=\"/staff/quality/clientsurveys.cgi?location=showdata&amp;surveyid=$recordid\"><span style=\"color:#999999;\">$recordid</span></a> $surveyreceived]</span></li>" if ($q5a ne ''); 
	
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
$documenttitle = &commoncode::cleanaccents2html($documenttitle);

	if ($search_date_start_label ne '') {
print<<EOM;
<p class="info">
You are searching by date, starting from $search_date_start_label and ending $search_date_end_label.
</p>
EOM
	}
print<<EOM;
<P>
<TABLE BORDER="1" CELLPADDING="2" CELLSPACING="0" WIDTH="100%"><TR><TD>
	Document Viewed: <a href="$catalogpage" TARGET=TOP><em>$documenttitle</em></a><br>
	Surveys on File: $num_matches</TD></TR></TABLE>
<P>
<strong>Q1: How did you hear about this document?</strong> (Question added to survey January 2004)<br>
<em><span style="color:#ff0000;"><OL>$text_q7a</OL></span></em>
<P>
<strong>Q2: How do you rate the quality of:</strong><br>
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
<em>Comments:</em><br>
<em><span style="color:#ff0000;"><OL>$text_q2b</OL></span></em>
<P>
<strong>Q4: Indicate the extent to which the document has had the following impact(s):</strong><br>

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
<em>Comments:</em><br>
<em><span style="color:#ff0000;"><OL>$text_q3e</OL></span></em>
<P>
<strong>Q5: How do you plan to use the information from this document?</strong> (Question added to survey January 2004)<br>
<em><span style="color:#ff0000;"><OL>$text_q8a</OL></span></em>
<P>
<strong>Q6: How have you used the information from this document?</strong> (Question added to survey January 2004)<br>
<em><span style="color:#ff0000;"><OL>$text_q9a</OL></span></em>
<P>
<strong>Q7: How can SEDL improve the document?</strong><br>
<em><span style="color:#ff0000;"><OL>$text_q4a</OL></span></em>
<P>
<strong>Q8: Please tell us why you would or would not recommend this document to others.</strong><br>
<em><span style="color:#ff0000;"><OL>$text_q6a</OL></span></em>
<P>
<strong>Q9: What issues should SEDL address in the future?</strong><br>
<em><span style="color:#ff0000;"><OL>$text_q5a</OL></span></em>
<br>
<br>
<br>
The survey responses included <a href="clientsurveys.cgi?location=listsurveys&showdocid=$showdocid&show_followuprequests=yes">$total_request_followup requests for followup contact</a>\.
EOM

}
#################################################################################
## END: LOCATION = summary_responses_singledoc
#################################################################################


#################################################################################
## START: LOCATION = LISTDOCUMENTSBYTITLE  (BL: OPTIMIZED DB QUERY 4/15/2006)
#################################################################################
if (($location eq 'listdocumentsbytitle') 
	&& ($search_date_start eq '')
	&& ($search_quarter eq '')
	&& ($search_category eq '')
	) {
	## CHECK IF "SHOWING ALL" AND IF SO, QUERY THE SUMMARY DATABASE
	$location= "listdocumentsbytitle_quicksummary";
}

if ($location eq 'listdocumentsbytitle') {

	################################
	## START: PRINT PAGE HEADER
	################################
print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN="TOP">
		<h1 style="margin-top:0px;3"><a href="clientsurveys.cgi">Product Survey Report System</a></h1>
		<P>
		On this page, you will see a list of documents for which surveys were sent, 
		listed by title.
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN="TOP" NOWRAP>
		<strong>Navigation options:</strong><br>
		- <a href="clientsurveys.cgi?location=main_menu">Main Menu: Dashboard</a><br>
		- <em><span style="color:#ff0000;">list documents by title</span></em> (catalog items only)<br>
		- <a href="clientsurveys.cgi?location=about">about the PDF Client Surveys</a>
EOM
print "<br>		- <a href=\"clientsurveys.cgi?location=maintenance\">maintenance menu</a>" if ($cookie_ss_staff_id eq 'blitke');
print<<EOM;
	</TD></TR>
</TABLE>
<p></p>

<h2>List Documents by Title (SEDL Catalog items only)</h2>
EOM
	################################
	## END: PRINT PAGE HEADER
	################################

&print_search_again_box($search_quarter, $search_month, $search_category, $search_author);


	if ($search_author ne '') {
print<<EOM;
<p class="info">
You are searching by author, and may notice that the number of publications displayed below does not match the number of publications the staff member authored.  That is because not all publications 
have survey data on file.
</p>
EOM
	}
	if ($search_date_start_label ne '') {
print<<EOM;
<p class="info">
You are searching by date, starting from $search_date_start_label and ending $search_date_end_label.
</p>
EOM
	}
print<<EOM;
<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0">
<TR><TD style="text-align:center;">\#</TD>
	<TD style="text-align:center;">Document Title</TD>
	<TD style="text-align:center;">Surveys Sent, Rceived,<br>and Response Ratio</TD>
	<TD style="text-align:center;">Followup Contact Requests</TD>
	<TD style="text-align:center;"># Permit Use of Comments</TD>
	<TD style="text-align:center;">Survey Results</TD>
</TR>
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
	my $ok_to_show = "yes";

	my $last_title_label = "";
	my $lastdocumentid = "";
	my $counter_uniquepubs = "1";
	my $command = "select sedlcatalog.title, sedlcatalog.title2, 
				clientsurvey.surveysent, clientsurvey.surveysenttwice, clientsurvey.surveyreceived, clientsurvey.documenturl, clientsurvey.documentid, clientsurvey.documentgroup, 
				clientsurvey.q1a, clientsurvey.request_followup, clientsurvey.permission_use_comments, clientsurvey.staff_comments_date
				from sedlcatalog, clientsurvey ";
	    $command .= " WHERE sedlcatalog.unique_id=clientsurvey.documentid";
		if ($search_author ne '') {
			$command .= " AND ((sedlcatalog.profile1 LIKE '$search_author%') OR (sedlcatalog.profile2 LIKE '$search_author%') OR (sedlcatalog.profile3 LIKE '$search_author%') OR (sedlcatalog.profile4 LIKE '$search_author%') OR (sedlcatalog.profile5 LIKE '$search_author%') OR (sedlcatalog.profile6 LIKE '$search_author%') OR (sedlcatalog.profile7 LIKE '$search_author%') OR (sedlcatalog.profile8 LIKE '$search_author%'))";
		} # END IF
		$command .= " AND clientsurvey.date >= '$search_date_start'" if ($search_date_start ne '');
		$command .= " AND clientsurvey.date <= '$search_date_end'" if ($search_date_end ne '');
		$command .= " AND clientsurvey.documentgroup LIKE '$search_category'" if ($search_category ne '');
		$command .= " AND sedlcatalog.survey_feedback_staff_assignments LIKE '%$search_assignment%'" if ($search_assignment ne '');
		$command .= " order by sedlcatalog.title, sedlcatalog.title2";

#print "<p class=\"info\">COMMAND: $command</p>";

	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#print "<p class=\"info\">MATCHES: $num_matches</p>";
		while (my @arr = $sth->fetchrow) {
			my ($title, $title2, $surveysent, $surveysenttwice, $surveyreceived, $documenturl, $documentid, $documentgroup, $q1a, $request_followup, $permission_use_comments, $staff_comments_date) = @arr;

			my $title_label = "$title";
   				$title_label = "$title: $title2" if ($title2 ne ''); 
   				$title_label = &commoncode::cleanaccents2html($title_label);

			   $ok_to_show = "yes";
			   $ok_to_show = "no" if (($show_responses_only eq 'yes') && ($countreplied == 0));
			   
			#############################################################################################################
			## START: IF THIS IS A DIFFERENT DOCUMENT THAN THE LAST EXAMINED, PRINT THE PREVIOUS RECORD DATA TO SCREEN
			#############################################################################################################
			if (($lastdocumentid ne $documentid) && ($lastdocumentid ne '')) {
			if ($ok_to_show eq 'yes') {
print<<EOM;
<TR><TD VALIGN="TOP">$counter_uniquepubs</TD>
	<TD VALIGN="TOP"><em>$last_title_label</em><br>
					<span style="color:#999999;font-size:9px;">(product ID: $lastdocumentid)</span></TD>
	<TD VALIGN="TOP"><TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" WIDTH="100%">
					<TR><TD VALIGN="TOP"><em>Sent:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT"><a href="/staff/quality/clientsurveys.cgi?location=listsurveys&amp;showdocid=$lastdocumentid&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite&amp;show_followuprequests=$show_followuprequests">$countsentnobounce</a></TD></TR>
					<TR><TD VALIGN="TOP"><em>Received:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$countreplied</TD></TR>
EOM
				if ($countpending ne '0') {
					print "<TR><TD VALIGN=\"TOP\"><em>Pending send:</em></TD><TD VALIGN=\"TOP\" ALIGN=\"RIGHT\">$countpending</TD></TR>";
				}
print<<EOM;
					<TR><TD VALIGN="TOP" NOWRAP><em>Response Ratio:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$ratio\%</TD></TR>
					</TABLE>
					</TD>
	<TD VALIGN="TOP" NOWRAP ALIGN="CENTER">
EOM
				if ($count_req_followup ne '0') {
print<<EOM;
		<TABLE CELLPADDING="1" CELLSPACING="0" WIDTH="100%">
		<TR><TD VALIGN="TOP"><em>Requests:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT"><a href="clientsurveys.cgi?location=listsurveys&showdocid=$lastdocumentid&show_followuprequests=yes&amp;search_month=$search_month&amp;search_quarter=$search_quarter\">$count_req_followup</a></TD></TR>
		<TR><TD VALIGN="TOP"><em>Followups:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$count_staff_followup</TD></TR>
		</TABLE>
EOM
				} else {
print "N/A";
				}
print<<EOM;
	</TD>
	<TD VALIGN="TOP" ALIGN="CENTER">
EOM
				if ($count_perm_use_comments ne '0') {
print<<EOM;
<a href="clientsurveys.cgi?location=listsurveys&amp;showdocid=$lastdocumentid&amp;show_usecomments=yes&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite">$count_perm_use_comments</a>
EOM
				} else {
					print "$count_perm_use_comments";
				}
print<<EOM;
</TD>
	<TD VALIGN="TOP" NOWRAP>
EOM
				if ($countreplied ne '0') {
					my $s = "";
					   $s = "s" if ($countreplied ne '1');
print<<EOM;
<a href=\"/staff/quality/clientsurveys.cgi?location=summary_responses_singledoc&showdocid=$lastdocumentid&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite&amp;show_followuprequests=$show_followuprequests\">combined<br>results</a>
<br>
<br>
<a href="/staff/quality/clientsurveys.cgi?location=listsurveys&amp;showdocid=$lastdocumentid&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite&amp;show_followuprequests=$show_followuprequests&amp;show_responses_only=yes&amp;show_address=$show_address">$countreplied individual<br>survey$s</a></TD>
EOM
				} else {
	print "N/A";
				} # END IF/ELSE
print<<EOM;
</TR>
EOM
			} # END IF OK TO SHOW
				########################################
				## START: CLEAR THE TRACKING VARIABLES
				########################################
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
				########################################
				## END: CLEAR THE TRACKING VARIABLES
				########################################

			}
			#############################################################################################################
			## END: IF THIS IS A DIFFERENT DOCUMENT THAN THE LAST EXAMINED, PRINT THE PREVIOUS RECORD DATA TO SCREEN
			#############################################################################################################

			########################################
			## START: INCREMENT THE TRACKING VARIABLES
			########################################
			$countpending++ if ($surveysent eq 'no');
			$countsent++ if ($surveysent ne 'no');
			$countreplied++ if ($surveyreceived ne '0000-00-00');  ## ($q1a ne '');
			$countnotsent++ if ($surveysent eq 'nosend');
			$countbounced++ if ($surveysent eq 'bounced');

			$countsentnobounce = $countsent - $countbounced;

			$count_req_followup++ if ($request_followup =~ 'user requests');
			$count_perm_use_comments++ if ($permission_use_comments eq 'yes');
			$count_staff_followup++ if ($staff_comments_date =~ '20');

			$ratio = $countreplied/$countsentnobounce if (($countsentnobounce ne '0') && ($countreplied ne '0'));
			$ratio = $ratio * 100 if ($ratio ne '0');

			$lastdocumentid = $documentid;
			$last_title_label = $title_label;

			my $x = new Number::Format;
			$ratio = $x->format_number($ratio, 2, 0);
			########################################
			## END: INCREMENT THE TRACKING VARIABLES
			########################################

		} # END DB QUERY LOOP


#############################################################################################################
## START: PRINT THE FINAL RECORD DATA TO SCREEN
#############################################################################################################
			if ($ok_to_show eq 'yes') {
print<<EOM;
<TR><TD VALIGN="TOP">$counter_uniquepubs</TD>
	<TD VALIGN="TOP"><em>$last_title_label</em><br>
					<span style="color:#999999;font-size:9px;">(product ID: $lastdocumentid)</span></TD>
	<TD VALIGN="TOP"><TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" WIDTH="100%">
					<TR><TD VALIGN="TOP"><em>Sent:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT"><a href="/staff/quality/clientsurveys.cgi?location=listsurveys&amp;showdocid=$lastdocumentid&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite&amp;show_followuprequests=$show_followuprequests">$countsentnobounce</a></TD></TR>
					<TR><TD VALIGN="TOP"><em>Received:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$countreplied</TD></TR>
EOM
		if ($countpending ne '0') {
			print "<TR><TD VALIGN=\"TOP\"><em>Pending send:</em></TD><TD VALIGN=\"TOP\" ALIGN=\"RIGHT\">$countpending</TD></TR>";
		}
print<<EOM;
					<TR><TD VALIGN="TOP" NOWRAP><em>Response Ratio:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$ratio\%</TD></TR>
					</TABLE>
					</TD>
	<TD VALIGN="TOP" NOWRAP ALIGN="CENTER">
EOM
				if ($count_req_followup ne '0') {
print<<EOM;
		<TABLE CELLPADDING="1" CELLSPACING="0" WIDTH="100%">
		<TR><TD VALIGN="TOP"><em>Requests:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT"><A HREF="clientsurveys.cgi?location=listsurveys&showdocid=$lastdocumentid&show_followuprequests=yes&amp;&amp;search_month=$search_month&amp;search_quarter=$search_quarter\">$count_req_followup</A></TD></TR>
		<TR><TD VALIGN="TOP"><em>Followups:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$count_staff_followup</TD></TR>
		</TABLE>
EOM
				} else {
print "N/A";
				}
print<<EOM;
	</TD>
	<TD VALIGN="TOP" ALIGN="CENTER">
EOM
				if ($count_perm_use_comments ne '0') {
print<<EOM;
<A HREF="clientsurveys.cgi?location=listsurveys&showdocid=$lastdocumentid&show_usecomments=yes&amp;search_month=$search_month&amp;search_quarter=$search_quarter">$count_perm_use_comments</A>
EOM
				} else {
					print "$count_perm_use_comments";
				}
print<<EOM;
</TD>
<TD VALIGN="TOP" NOWRAP>
EOM
	if ($countreplied ne '0') {
my $s = "";
   $s = "s" if ($countreplied ne '1');
print<<EOM;
<A HREF=\"/staff/quality/clientsurveys.cgi?location=summary_responses_singledoc&amp;showdocid=$lastdocumentid&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite&amp;show_followuprequests=$show_followuprequests\">combined<BR>results</A>
<BR>
<BR>
<A HREF="/staff/quality/clientsurveys.cgi?location=listsurveys&amp;showdocid=$lastdocumentid&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite&amp;show_followuprequests=$show_followuprequests&amp;show_responses_only=yes&amp;show_address=$show_address">$countreplied individual<BR>survey$s</A></TD>
EOM
} else {
	print "N/A";
}
print<<EOM;
</TR>
EOM
		} # END IF OK TO SHOW
print<<EOM;
</TABLE>
EOM
#############################################################################################################
## END: PRINT THE FINAL RECORD DATA TO SCREEN
#############################################################################################################
}  
#################################################################################
## END: LOCATION = LISTDOCUMENTSBYTITLE
#################################################################################


#################################################################################
## START: LOCATION = LISTDOCUMENTSBYTITLE_QUICKSUMMARY  (BL: OPTIMIZED DB QUERY 4/15/2006)
#################################################################################
if ($location eq 'listdocumentsbytitle_quicksummary') {

	###############################
	## START: PRINT PAGE HEADER
	###############################
print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN="TOP">
		<h1 style="margin-top:0px;3"><a href="clientsurveys.cgi">Product Survey Report System</a></h1>
		<P>
		On this page, you will see a list of documents for which surveys were sent, 
		listed by title.
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN="TOP" NOWRAP>
		<strong>Navigation options:</strong><br>
		- <a href="clientsurveys.cgi?location=main_menu">Main Menu: Dashboard</a><br>
		- <em><span style="color:#ff0000;">list documents by title</span></em> (catalog items only)<br>
		- <a href="clientsurveys.cgi?location=about">about the PDF Client Surveys</a>
EOM
print "<br>		- <a href=\"clientsurveys.cgi?location=maintenance\">maintenance menu</a>" if ($cookie_ss_staff_id eq 'blitke');
print<<EOM;
	</TD></TR>
</TABLE>

<p></p>

<h2>List Documents by Title (SEDL Catalog items only)</h2>
EOM
	###############################
	## END: PRINT PAGE HEADER
	###############################

&print_search_again_box($search_quarter, $search_month, $search_category, $search_author);

	if ($search_author ne '') {
print<<EOM;
<p class="info">
You are searching by author, and may notice that the number of publications displayed below does not match the number of publications the staff member authored.  That is because not all publications 
have survey data on file.
</p>
EOM
	}
print<<EOM;
<TABLE BORDER="1" CELLPADDING="3" CELLSPACING="0">
<TR><TD style="text-align:center;">\#</TD>
	<TD style="text-align:center;">Document Title</TD>
	<TD style="text-align:center;">Surveys Sent, Rceived,<br>and Response Ratio</TD>
	<TD style="text-align:center;">Followup Contact Requests</TD>
	<TD style="text-align:center;"># Permit Use of Comments</TD>
	<TD style="text-align:center;">Survey Results</TD>
</TR>
EOM


	my $command_summary = "select clientsurvey_summary.*, sedlcatalog.title, sedlcatalog.title2
							from clientsurvey_summary, sedlcatalog
							where css_summarytype = 'bydocid'
							AND (clientsurvey_summary.css_summarytype_id=sedlcatalog.unique_id)";
		if ($search_author ne '') {
			$command_summary .= " AND ((sedlcatalog.profile1 LIKE '$search_author%') OR (sedlcatalog.profile2 LIKE '$search_author%') OR (sedlcatalog.profile3 LIKE '$search_author%') OR (sedlcatalog.profile4 LIKE '$search_author%') OR (sedlcatalog.profile5 LIKE '$search_author%') OR (sedlcatalog.profile6 LIKE '$search_author%') OR (sedlcatalog.profile7 LIKE '$search_author%') OR (sedlcatalog.profile8 LIKE '$search_author%'))";
		} # END IF
		$command_summary .= " AND (sedlcatalog.survey_feedback_staff_assignments LIKE '%$search_assignment%')" if ($search_assignment ne '');
		$command_summary .= " order by sedlcatalog.title, sedlcatalog.title2";
										
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_summary) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $counter_uniquepubs = "1";
#print "<p class=\"info\">COMMAND: $command_summary</p>";
	my $num_matches = $sth->rows;
#print "<p class=\"info\">MATCHES: $num_matches</p>";

		while (my @arr = $sth->fetchrow) {
			my ($css_uniqueid, $css_summarytype, $css_summarytype_id, $css_count_sent, $css_count_sentvalid, $css_count_bounced, $css_count_nosend, $css_count_received, $css_response_ratio, $css_followups_req, 
			$css_followups_made, $css_commentpermissions, $css_pending_send, $css_last_updated, $title, $title2) = @arr;

				$title .= ": $title2" if ($title2 ne '');
   				$title = &commoncode::cleanaccents2html($title);
			my $ok_to_show = "yes";
			   $ok_to_show = "no" if (($show_responses_only eq 'yes') && ($css_count_received == 0));
			   

			if ($ok_to_show eq 'yes') {
print<<EOM;
<TR><TD VALIGN="TOP">$counter_uniquepubs</TD>
	<TD VALIGN="TOP"><em>$title</em><br>
					<span style="color:#999999;font-size:9px;">(product ID: $css_summarytype_id)</span></TD>
	<TD VALIGN="TOP"><TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" WIDTH="100%">
					<TR><TD VALIGN="TOP"><em>Sent:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT"><a href="/staff/quality/clientsurveys.cgi?location=listsurveys&amp;showdocid=$css_summarytype_id&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite&amp;show_followuprequests=$show_followuprequests">$css_count_sentvalid</a></TD></TR>
					<TR><TD VALIGN="TOP"><em>Received:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$css_count_received</TD></TR>
EOM
				if ($css_pending_send ne '0') {
					print "<TR><TD VALIGN=\"TOP\"><em>Pending send:</em></TD><TD VALIGN=\"TOP\" ALIGN=\"RIGHT\">$css_pending_send</TD></TR>";
				}
print<<EOM;
					<TR><TD VALIGN="TOP" NOWRAP><em>Response Ratio:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$css_response_ratio\%</TD></TR>
					</TABLE>
					</TD>
	<TD VALIGN="TOP" NOWRAP ALIGN="CENTER">
EOM
				if ($css_followups_req ne '0') {
print<<EOM;
		<TABLE CELLPADDING="1" CELLSPACING="0" WIDTH="100%">
		<TR><TD VALIGN="TOP"><em>Requests:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT"><a href="clientsurveys.cgi?location=listsurveys&showdocid=$css_summarytype_id&show_followuprequests=yes&amp;search_month=$search_month&amp;search_quarter=$search_quarter\">$css_followups_req</a></TD></TR>
		<TR><TD VALIGN="TOP"><em>Followups:</em></TD><TD VALIGN="TOP" ALIGN="RIGHT">$css_followups_made</TD></TR>
		</TABLE>
EOM
				} else {
print "N/A";
				}
print<<EOM;
	</TD>
	<TD VALIGN="TOP" ALIGN="CENTER">
EOM
				if ($css_commentpermissions ne '0') {
print<<EOM;
<a href="clientsurveys.cgi?location=listsurveys&showdocid=$css_summarytype_id&show_usecomments=yes">$css_commentpermissions</a>
EOM
				} else {
					print "$css_commentpermissions";
				}
print<<EOM;
</TD>
	<TD VALIGN="TOP" NOWRAP>
EOM
				if ($css_count_received ne '0') {
					my $s = "";
					   $s = "s" if ($css_count_received ne '1');
print<<EOM;
<a href=\"/staff/quality/clientsurveys.cgi?location=summary_responses_singledoc&amp;showdocid=$css_summarytype_id&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite&amp;show_followuprequests=$show_followuprequests\">combined<br>results</a>
<br>
<br>
<a href="/staff/quality/clientsurveys.cgi?location=listsurveys&amp;showdocid=$css_summarytype_id&amp;search_month=$search_month&amp;search_quarter=$search_quarter&amp;summarysite=$summarysite&amp;show_followuprequests=$show_followuprequests&amp;show_responses_only=yes&amp;show_address=$show_address">$css_count_received individual<br>survey$s</a></TD>
EOM
				} else {
	print "N/A";
				}
print<<EOM;
</TR>
EOM
			} # END IF OK TO SHOW
			$counter_uniquepubs++;
		} # END DB QUERY LOOP
print "</table>";
}  
#################################################################################
## END: LOCATION = LISTDOCUMENTSBYTITLE_QUICKSUMMARY
#################################################################################


#################################################################################
## START: LOCATION = SHOWDATA  (BL: OPTIMIZED DB QUERY 4/15/2006)
#################################################################################
if ($location eq 'showdata') {
## PRINT PAGE HEADER
print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN="TOP">
		<h1 style="margin-top:0px;"><a href="clientsurveys.cgi">Product Survey Report System</a></h1>
		<p>Action: View Survey Data<br>
		(survey responses are in red)</p> 
	</TD>
	<TD>&nbsp;</TD><TD>&nbsp;</TD>
	<TD VALIGN="TOP" NOWRAP>
		<strong>Navigation options:</strong><br>
		- <a href="clientsurveys.cgi?location=main_menu">Main Menu: Dashboard</a><br>
		- <a href="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</a> (catalog items only)<br>
		- <a href="clientsurveys.cgi?location=about">about the PDF Client Surveys</a>
EOM
print "<br>		- <a href=\"clientsurveys.cgi?location=maintenance\">maintenance menu</a>" if ($cookie_ss_staff_id eq 'blitke');
print<<EOM;
	</TD></TR>
</TABLE>
EOM

	my $command = "select * from clientsurvey where recordid like '$surveyid' order by recordid";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#print "<p class=\"info\">COMMAND: $command</p>";
	$sth->execute;
	my $num_matches = $sth->rows;
#print "<p class=\"info\">MATCHES: $num_matches</p>";
	while (my @arr = $sth->fetchrow) {
		my ($recordid, $surveysent, $surveysenttwice, $surveyreceived, $email, $date, $documenturl, $documentid, $documentgroup, $q1a, $q1b, $q1c, $q1d, $q2a, $q2b, $q3a, $q3b, $q3c, $q3d, $q3e, $q4a, $q5a, $q6a, $q7a, $q8a, $q9a, $q10a, $ipnum, $browser, $request_followup, $permission_use_comments, $name, $title, $staff_comments, $staff_comments_by, $staff_comments_date) = @arr;


		#############################################
		## START: DB QUERY TO GRAB THE DOCUMENT NAME
		#############################################
		my $documenttitle = "Error - Document not found in SEDL Catalog";
		my $catalogpage = "";

		my $command = "select title, title2, onlineid from sedlcatalog 
						where unique_id = '$documentid'";

#print "<p class=\"info\">COMMAND: $command</p>";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#print "<p class=\"info\">MATCHES: $num_matches</p>";
		while (my @arr = $sth->fetchrow) {
			my ($title, $title2, $onlineid) = @arr;

			$documenttitle = "$title";
			$documenttitle .= ": $title2" if $title2 ne '';
			$documenttitle = &commoncode::cleanaccents2html($documenttitle);

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
<TABLE BORDER="1" CELLPADDING="2" CELLSPACING="0" WIDTH="100%">
<TR><TD><strong><a href="http://www.sedl.org/survey/pubs.cgi?e=$email&id=$recordid" TARGET="TOP">SURVEY #$recordid</a> from $email</strong><br>
	- Document Viewed: <a href="$catalogpage" TARGET=TOP>$documenttitle</a><br>	
	- <em>Resource viewed on date: $date, Survey sent: $surveysent, Survey Received: $surveyreceived</em>
	</TD></TR>
</TABLE>
<P>
<strong>Q1: How did you hear about this document?</strong> (Question added to survey January 2004)<br><em><span style="color:#ff0000;">$q7a</span></em>
<P>
<strong>Q2: How do you rate the quality of:</strong><br>
<strong>-</strong> the document overall = <em><span style="color:#ff0000;">$q1a_label ($q1a)</span></em><br>
<strong>-</strong> the organization of the document = <em><span style="color:#ff0000;">$q1b_label ($q1b)</span></em><br>
<strong>-</strong> the timeliness of the document = <em><span style="color:#ff0000;">$q1c_label ($q1c)</span></em><br>
<strong>-</strong> the presentation of the document = <em><span style="color:#ff0000;"> $q1d_label ($q1d)</span></em>
<P>
<strong>Q3: How do you rate the document for meeting your needs?</strong>  = <em><span style="color:#ff0000;">$q2a_label ($q2a)</span></em><br>
<strong>-</strong> Comments:<br>
<em><span style="color:#ff0000;">$q2b</span></em>
<P>
<strong>Q4: Indicate the extent to which the document has had the following impact(s):</strong><br>
<strong>-</strong> Increased your awareness of important new skills and knowledge. = <em><span style="color:#ff0000;">$q3a_label ($q3a)</span></em><br>
<strong>-</strong> Informed Decision-making = <em><span style="color:#ff0000;">$q3b_label ($q3b)</span></em><br>
<strong>-</strong> Enhanced Quality of Personal Practice = <em><span style="color:#ff0000;">$q3c_label ($q3c)</span></em><br>
<strong>-</strong> Positively Affected Student Performance = <em><span style="color:#ff0000;">$q3d_label ($q3d)</span></em><br>
<strong>-</strong> Comments:<br>
<em><span style="color:#ff0000;">$q3e</span></em>
<P>
<strong>Q5: How do you plan to use the information from this document?</strong> (Question added to survey January 2004)<br>
<em><span style="color:#ff0000;">$q8a</span></em>
<P>
<strong>Q6: How have you used the information from this document?</strong> (Question added to survey January 2004)<br>
<em><span style="color:#ff0000;">$q9a</span></em>
<P>
<strong>Q7: How can SEDL improve the document?</strong><br>
<em><span style="color:#ff0000;">$q4a</span></em>
<P>
<strong>Q8: Please tell us why you would or would not recommend this document to others.</strong><br>
<em><span style="color:#339900">$q6a</span></em>
EOM
if (($q6a ne '') || ($permission_use_comments ne '')) {
print<<EOM;
	<div style="margin-left:25px;">
	Did the survey participant give permission to use their comments?<br>
	<FONT COLOR="GREEN"><strong>$permission_use_comments</strong></FONT>
	</div>
EOM
}
$request_followup = "no" if ($request_followup eq '');

$name = "NOT INCLUDED BY USER" if ($name eq '');
$title = "NOT INCLUDED BY USER" if ($title eq '');
print<<EOM;
<P>
<strong>Q9: What issues should SEDL address in the future?</strong><br>
<em><span style="color:#ff0000;">$q5a</span></em>
<br>
<br>
User Name: $name<br>
User title: $title
<br>
<br>
<em>USER IP NUMBER: $ipnum<br>
USER BROWSER TYPE: $browser</em>
<br>
<br>
<br>

<TABLE BORDER="1" CELLPADDING="8" CELLSPACING="1">
<TR><TD>
<h2 ALIGN="CENTER">THIS AREA FOR SEDL USE ONLY</h2>
Did survey participant request follow-up contact with SEDL?<br>
<span style="color:#ff0000;">$request_followup</span>
<form action="clientsurveys.cgi" method=POST>
<P>
SEDL staff comments/notes on any post-survey contact with site visitor<br>

<textarea name="new_staff_comments" rows=8 cols=55>$staff_comments</textarea><br>
<label for="new_staff_comments_by">By staff member:</label> <input name="new_staff_comments_by" id="new_staff_comments_by" size="20" VALUE="$staff_comments_by"> &nbsp; Date: $staff_comments_date<br>
  <input type="hidden" name="surveyid" value="$recordid">
  <input type="hidden" name="location" value="add_staffcomments">
  <input type="submit" value="Add Comments">
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
			print "<P><span style=\"color:#ff0000;\">You forgot to check the confirmation box.  Record deletion was aborted.</span>";
		} else {
			# ELSE FIX ADDRESS AND MARK TO RE-SEND
			my $command = "DELETE FROM clientsurvey WHERE recordid='$bad_surveynumber'";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
			print "<P><span style=\"color:#ff0000;\">Deleted $num_matches record. (ID: $bad_surveynumber)</span>";
		}
	}
	#########################################################################
	## END: START: IF USER SUBMITTED A BAD ADDRESS, INDICATE THE SURVEY BOUNCED IN THE DATABASE
	#########################################################################






print <<EOM;
<TABLE width="100%">
<TR><TD VALIGN="TOP">
		<h1 style="margin-top:0px;"><a href="clientsurveys.cgi">Product Survey Report System</a></h1>
		<p><strong>Action: Data Maintenance</strong><br>
		This page is used by Communications office staff to flag bounced e-mails<br> and fix e-mails entered incorrectly.</p>
		<P>
		<span style="color:#ff0000;">Please do not use this page if you are not authorized to maintain the PDF Survey database.</span>
		</P>
	</TD>
	<TD>&nbsp;</TD>
	<TD VALIGN="TOP" NOWRAP>
		<strong>Navigation options:</strong><br>
		- <a href="clientsurveys.cgi?location=main_menu">Main Menu: Dashboard</a><br>
		- <a href="clientsurveys.cgi?location=listdocumentsbytitle">list documents by title</a> (catalog items only)<br>
		- <a href="clientsurveys.cgi?location=about">about the PDF Client Surveys</a><br>
		- <em><span style="color:#ff0000;">maintenance menu</span></em>
	</TD></TR>
</TABLE>

<h2>Indicate bounced mail from surveys</h2>
<p>
<label for="badaddress1">Simply type in the e-mail address below to mark its records in the database as an invalid e-mail address.</label>
</p>
	<div style="margin-left:25px;">
	<form method="POST" action="clientsurveys.cgi">
	<input type="text" size="40" name="badaddress" id="badaddress1" value="">

	<input type="hidden" name="location" value="maintenance">
	<input type="submit" value="Click to mark this as a bounced e-mail address">
	</form>
	</div>



<h2>Fix an e-mail and re-send survey</h2>
<p>
Simply type in the two e-mail addresses below to change the e-mail address and re-send the survey invitation.
</p>
<form method="POST" action="clientsurveys.cgi">
	<div style="margin-left:25px;">
	<TABLE>
	<TR><TD><label for="badaddress">Bad address:</label></TD><TD><input type="text" size="40" name="badaddress" id="badaddress" value=""></TD></TR>
	<TR><TD><label for="goodaddress">Good address:</label></TD><TD><input type="text" size="40" name="goodaddress" id="goodaddress" value=""></TD></TR>
	</TABLE>

	<input type="hidden" name="location" value="maintenance"><br>
	<input type="submit" value="Click to fix this e-mail">
	</form>
	</div>


<h2>Remove a Survey ID from the System</h2>
<p>
If you receive back a survey from a user who says they were unable to view the resource or 
did not use the document (and so cannot be surveyed about it), enter the survey ID number here 
to delete that survey from the database.
</p>
	<div style="margin-left:25px;">
	<form action="clientsurveys.cgi" method="POST">
	<label for="bad_surveynumber">Survey ID to delete:</label> <input type="text" size="10" name="bad_surveynumber" id="bad_surveynumber" value=""><br>
	<input type="checkbox" size="10" name="confirm_delete" id="confirm_delete" value="yes"> <label for="confirm_delete">Click here to confim this deletion.</label>
	<br><br>
	<input type="hidden" name="location" value="maintenance">
	<input type="submit" value="Click to remove this survey ID">
	</form>
	</div>
EOM



}
#################################################################################
## END: LOCATION = MAINTENANCE
#################################################################################


####################################################################
## START: PRINT PAGE FOOTER
####################################################################
print "<p>LOCATION: $location</p>";
print "$htmltail";
####################################################################
## END: PRINT PAGE FOOTER
####################################################################



####################################################################
## START: COOKIE HANDLING SUBROUTINES
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

####################################################################
## END: COOKIE HANDLING SUBROUTINES
####################################################################




######################################
## START: SUBROUTINE place_label
######################################
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
######################################
## END: SUBROUTINE place_label
######################################





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


######################################
## START: SUBROUTINE print_search_again_box
######################################
sub print_search_again_box {
	my $search_quarter = $_[0];
	my $search_month = $_[1];
	my $search_category = $_[2];
	my $search_author = $_[3];

	my %assignments_by_staff_id;
	my %staff_assignments_by_id;

	my %staff_names;
	#######################################
	## START: GRAB LIST OF STAFF USER IDS
	#######################################
		my $command_get_staff = "select userid, firstname, lastname from staff_profiles order by userid";
		my $dsn_intranet = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn_intranet, "intranetuser", "limited");
		my $sth = $dbh->prepare($command_get_staff) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		while (my @arr = $sth->fetchrow) {
			my ($this_id, $firstname, $lastname) = @arr;
			$staff_names{$this_id} = "$firstname $lastname";
		} # END DB QUERY LOOP
	#######################################
	## END: GRAB LIST OF STAFF USER IDS
	#######################################

	#############################################
	## START: GRAB PRODUCT CATALOG TITLES
	#############################################
	my %product_ids_by_title;
	   $product_ids_by_title{' title unknown'} = "0";
	my %product_titles_by_id;
	   $product_titles_by_id{'0'} = " title unknown";
	my %products_produced_by_staff_id;

	my $command = "select unique_id, title, title2, profile1, profile2, profile3, profile4, profile5, profile6, profile7, profile8, survey_feedback_staff_assignments from sedlcatalog";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
			my ($unique_id, $title, $title2, $profile1, $profile2, $profile3, $profile4, $profile5, $profile6, $profile7, $profile8, $survey_feedback_staff_assignments) = @arr;
			$product_titles_by_id{$unique_id} = "$title";
			$product_titles_by_id{$unique_id} .= ": $title2" if $title2 ne '';
			$product_ids_by_title{$product_titles_by_id{$unique_id}} = $unique_id;

			###########################################################
			## START: SPLIT UP STAFF ASSIGNMENTS AND COUNT INTO A HASH
			###########################################################
			if ($survey_feedback_staff_assignments ne '') {
				my @assignments = split(/\;/,$survey_feedback_staff_assignments);
				my $num_staff_assigned = $#assignments + 1; # COUNT TOTAL STAFF IN ARRAY
				my $counter = 0;
				while ($counter <= $#assignments) {
					$assignments[$counter] =~ s/ //gi;
					$assignments_by_staff_id{$assignments[$counter]}++ if ($assignments[$counter] ne '');

					$staff_assignments_by_id{$unique_id} .= ", " if ($staff_assignments_by_id{$unique_id} ne '');
					$staff_assignments_by_id{$unique_id} .= "$staff_names{$assignments[$counter]}";
					$counter++;
				} # END WHILE LOOP
					$staff_assignments_by_id{$unique_id} = "<span title=\"$staff_assignments_by_id{$unique_id}\">$num_staff_assigned</span>";
#					print "<p>$unique_id $staff_assignments_by_id{$unique_id}</p>" if ($survey_feedback_staff_assignments ne '');
			} else {
				$staff_assignments_by_id{$unique_id} = "<span style=\"color:#cc0000;\">N/A</span>";
			}
			###########################################################
			## END: SPLIT UP STAFF ASSIGNMENTS AND COUNT INTO A HASH
			###########################################################

			if ($profile1 ne '') {
				$profile1 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile1}++;
			}
			if ($profile2 ne '') {
				$profile2 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile2}++;
			}
			if ($profile3 ne '') {
				$profile3 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile3}++;
			}
			if ($profile4 ne '') {
				$profile4 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile4}++;
			}
			if ($profile5 ne '') {
				$profile5 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile5}++;
			}
			if ($profile6 ne '') {
				$profile6 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile6}++;
			}
			if ($profile7 ne '') {
				$profile7 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile7}++;
			}
			if ($profile8 ne '') {
				$profile8 =~ s/\.html//gi;
				$products_produced_by_staff_id{$profile8}++;
			}
		} # END DB QUERY
	#############################################
	## END: GRAB PRODUCT CATALOG TITLES
	#############################################


	#############################################
	## START: GRAB SUMMARY DATA TO DISPLAY
	#############################################
	my %summary_byquarter;
	my %summary_bymonth;

	my %summary_bydoccategory;
	my %summary_bydoccategory_response_ratio;
	my %summary_bydoccategory_followups_req;
	my %summary_bydoccategory_commentpermissions;
	my %summary_bydoccategory_pending_send;
	
	my %summary_bydocid;
	my %summary_bydocid_response_ratio;
	my %summary_bydocid_followups_req;
	my %summary_bydocid_commentpermissions;
	my %summary_bydocid_pending_send;
	my %summary_bydocid_sentvalid;

	my $command_summary = "select * from clientsurvey_summary where css_summarytype != 'bydocid' ";
										
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_summary) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $last_email = "";
		while (my @arr = $sth->fetchrow) {
			my ($css_uniqueid, $css_summarytype, $css_summarytype_id, $css_count_sent, $css_count_sentvalid, $css_count_bounced, $css_count_nosend, $css_count_received, $css_response_ratio, $css_followups_req, $css_followups_made, $css_commentpermissions, $css_pending_send, $css_last_updated) = @arr;

				$summary_bymonth{$css_summarytype_id} = $css_count_received if ($css_summarytype eq 'bymonth');

				if ($css_summarytype eq 'byquarter') {
					$summary_byquarter{$css_summarytype_id} = $css_count_received;
#					print "<br>$css_count_received in $css_summarytype_id";
				} # END IF

				if ($css_summarytype eq 'bydoccategory') {
					$summary_bydoccategory{$css_summarytype_id} = $css_count_received ;
					$summary_bydoccategory_response_ratio{$css_summarytype_id} = $css_response_ratio;
					$summary_bydoccategory_followups_req{$css_summarytype_id} = $css_followups_req;
					$summary_bydoccategory_commentpermissions{$css_summarytype_id} = $css_commentpermissions;
					$summary_bydoccategory_pending_send{$css_summarytype_id} = $css_pending_send;
				} # END IF
		} # END DB QUERY LOOP

	#############################################
	## END: GRAB SUMMARY DATA TO DISPLAY
	#############################################


print<<EOM;
<div style="margin:0 0 10px 400px;border: 2px #999999 solid;padding:8px 8px 0 8px;">
<h3 style="margin:0px;">Search Again</h3>
<form action="clientsurveys.cgi" method="GET">
<div style="margin-bottom:10px;">
<table>
<tr><td>Querter:</td>
	<td><select name=\"search_quarter\" id=\"search_quarter\">
		 <option value="">select a quarter</option>
EOM
	my $quarter = "";
    foreach $quarter (sort {$b <=> $a} (keys(%summary_byquarter))) {
		my ($t_year, $t_quarter) = split(/\-/,$quarter);
		   $t_quarter =~ s/0//gi;
		   $t_quarter = "Q$t_quarter";
		if ($summary_byquarter{$quarter} != 0) {
			my $quarter_label = "$t_quarter\/$t_year";
			print "<option value=\"$quarter\"";
			print " SELECTED" if ($quarter eq $search_quarter);
			print " >$quarter_label ($summary_byquarter{$quarter} responses)</option>";
		} # END IF
    } # END FOREACH
print<<EOM;
</select></td></tr>
<tr><td>Month</td>
	<td><select name="search_month" id="search_month">
		<option value="">select a month</option>
EOM
    foreach my $mnth (reverse (sort (keys(%summary_bymonth)))) {
		if ($summary_bymonth{$mnth} != 0) {
			my ($t_year, $t_month) = split(/\-/,$mnth);
			my $month_label = "$t_month\/$t_year";
			print "<option value=\"$mnth\"";
			print " SELECTED" if ($mnth eq $search_month);
			print " >$summary_bymonth{$mnth} responses in $month_label</option>\n";
		} # END IF
    } # END FOREACH

print<<EOM;
		</select>
	</td></tr>
<tr><td>Category</td>
	<td><select name="search_category" id="search_category">
		<option value="">select a category</option>
EOM
    foreach my $category (sort (keys(%summary_bydoccategory))) {
		if ($summary_bydoccategory{$category} != 0) {
			print "<option value=\"$category\"";
			print " SELECTED" if ($category eq $search_category);
			print " >$category -- ($summary_bydoccategory{$category} responses)</option>\n";
		} # END IF
    } # END FOREACH

print<<EOM;
	</td></tr>
<tr><td>Author-ID</td>
	<td><select name="search_author" id="search_author">
		<option value="">select a resource author</option>
EOM
my $pulldownmenu_for_staff_assignments = "";

my $dsn_intranet = "DBI:mysql:database=intranet;host=localhost";
my $command = "select firstname, lastname, email, phone, userid, phoneext, department_abbrev from staff_profiles order by lastname";
my $dbh = DBI->connect($dsn_intranet, "intranetuser", "limited");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
	while (my @arr = $sth->fetchrow) {
    	my ($firstname, $lastname, $email, $phone, $userid, $phoneext, $department_abbrev) = @arr;
    	$products_produced_by_staff_id{$userid} = "0" if ($products_produced_by_staff_id{$userid} == 0);
		if ($products_produced_by_staff_id{$userid} > 0) {
			print "<option value=\"$userid\" ";
			print "SELECTED" if ($search_author eq $userid);
			print ">$firstname $lastname -- ($products_produced_by_staff_id{$userid} resources)</option>\n";
		} # END IF

		## PREPARE CODE FOR SEARCH BY ASSIGNMENT
		if ($assignments_by_staff_id{$userid} > 0) {
			$pulldownmenu_for_staff_assignments .= "<option value=\"$userid\" ";
			$pulldownmenu_for_staff_assignments .= "SELECTED" if ($search_assignment eq $userid);
			$pulldownmenu_for_staff_assignments .= ">$firstname $lastname (assigned to $assignments_by_staff_id{$userid} resources)</option>\n";
		} # END IF

	} # END DB QUERY LOOP
print<<EOM;
			</select>
	</td></tr>

	<tr><td>Assignment</td>
		<td><select name="search_assignment" id="search_assignment">
			<option value="">select a staff assignment</option>
			$pulldownmenu_for_staff_assignments
			</select>
		</td>
	</tr>
	<tr><td><label for="show_responses_only">Responses only?</label></td>
		<td><select name="show_responses_only" id="show_responses_only">
			<option value="no">show all products</option>
			<option value="yes"
EOM
	print " SELECTED" if ($show_responses_only eq 'yes');
print<<EOM;
			>show only products w/completed surveys</option>
			</select>
		</td>
	</tr>
	</table>
	
	<INPUT TYPE="HIDDEN" NAME="location" VALUE="listdocumentsbytitle">
	<input TYPE="submit" VALUE="Go">
	</div>
	</form>
</div>
EOM
	####################################
	## END: PRINT "SEARCH AGAIN" BOX
	####################################
######################################
} # END: SUBROUTINE print_search_again_box
######################################



#!/usr/bin/perl

#####################################################################################################
# Copyright 2010 by SEDL
#
# This script is activated weekly to send surveys to people who downloaded 
# PDF documents within the last week.
#
# Written by Brian Litke 05-13-2002
#####################################################################################################
use strict;
use CGI qw/:all/;

#NEW:
use DBI;
my $dsn = "DBI:mysql:database=corp;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

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
	my $time_hour_mil = POSIX::strftime('%k', localtime(time)); # Hour in military notation (e.g. 9 or 21)
	my $time_hour_leadingzero = POSIX::strftime('%I', localtime(time)); # Hour w/leadingsero (e.g. 09 or 09)
	my $time_hour_mil_leadingzero = POSIX::strftime('%H', localtime(time)); # Hour in military notation w/leadingsero (e.g. 09 or 21)
	my $time_min = POSIX::strftime('%M', localtime(time)); # Date in month (e.g. 39)
	my $time_sec = POSIX::strftime('%S', localtime(time)); # Date in month (e.g. 38)

	my $timestamp = "$year$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; # 14-digit timestamp (e.g. 20080306143938)

#my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
#   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
	my $this_user_id = "$timestamp$ipnum$ipnum2";
	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################



######################################
## START: COMPUTE DATE ONE WEEK AGO
######################################
my $oneweekago = "";
my $twoweeksago = "";
	$oneweekago = &subtract_week("$year\-$month\-$monthdate_wleadingzero");
	$twoweeksago = &subtract_week($oneweekago);

######################################
## START: SUBROUTINE subtract_week
######################################
sub subtract_week {
	my $date_to_transform = $_[0];
	my ($oneweekagoyear, $oneweekagocalmonth, $oneweekagocaldate) = split(/\-/,$date_to_transform);

	## FIX MONTH (if in first 8 days of month
	if ($oneweekagocaldate < 8) {
		$oneweekagocalmonth--;
	}

	$oneweekagocalmonth = "0$oneweekagocalmonth" if (length($oneweekagocalmonth) < 2);


	## FIX YEAR (if in first 8 days of the first month, subtract a year)
	if (($oneweekagocaldate < 8) && ($oneweekagocalmonth eq '01')) {
		$oneweekagoyear--; # 2008
	}

	## FIX DATE
	if ($oneweekagocaldate < 8) {
		$oneweekagocaldate = "28" if ($oneweekagocalmonth eq '02');
		$oneweekagocaldate = "30" if (($oneweekagocalmonth eq '04') || ($oneweekagocalmonth eq '06') || ($oneweekagocalmonth eq '09') || ($oneweekagocalmonth eq '11'));
		$oneweekagocaldate = "31" if (($oneweekagocalmonth eq '01') || ($oneweekagocalmonth eq '03') || ($oneweekagocalmonth eq '05') || ($oneweekagocalmonth eq '07') || ($oneweekagocalmonth eq '08') || ($oneweekagocalmonth eq '10') || ($oneweekagocalmonth eq '12'));

	} else {
		$oneweekagocaldate = $oneweekagocaldate - 7;
	}

	## MAKE SURE DATE HAS LEADING ZERO
	if (length($oneweekagocaldate) < 2) {
		$oneweekagocaldate = "0$oneweekagocaldate";
	}

	return("$oneweekagoyear\-$oneweekagocalmonth\-$oneweekagocaldate");
}
######################################
## END: SUBROUTINE subtract_week
######################################



#$oneweekago = "2010-02-18"; # OR TESTING ONLY
######################################
## END: COMPUTE DATE ONE WEEK AGO
######################################


######################################
## PRINT PAGE HEADER FOR DEBUG OUTPUT
######################################
if ($debug eq '1') {
print header;
print <<EOM;
<HTML>
<head>
<title>ONE WEEK AGO = $oneweekago</title>
</head>
<BODY>
<OL>
EOM
}


#####################################
## SET VARIABLES FOR SENDING E-MAIL #
#####################################
my $fromaddr = 'webmaster@sedl.org';
my $mailprog = "/usr/sbin/sendmail -t -f$fromaddr"; #No -n because of webmaster alias

## INITIALIZE VARIABLE THAT TRACKS WHETHER WE SHOULD SEND AN E-MAIL TO THIS USER
my $sendemail = 'yes';
my $downloaddate = "";

################################################################################################################################
## START: DB QUERY - SELECT ALL RECORDS FOR PRODUCT ACESSES THAT ARE OVER A WEEK OLD BUT HAVEN'T BEEN SENT A SURVEY
################################################################################################################################
#my $command = "select recordid, surveysent, surveysenttwice, surveyreceived, email, date, documenturl, documentid 
#FROM clientsurvey 
#WHERE (date < '$oneweekago') 
#AND ((surveysent like 'no') OR ((surveysenttwice like 'no') AND (date < '$twoweeksago')))
#AND surveyreceived = '0000-00-00' 
#order by recordid";
## (BL) ON 9/22/2010, I DISABLED THE SECOND E-MAIL SURVEY REMINDER

my $command = "select recordid, surveysent, surveysenttwice, surveyreceived, email, date, documenturl, documentid 
FROM clientsurvey 
WHERE (date < '$oneweekago') AND (surveysent like 'no') AND (surveyreceived = '0000-00-00') 
order by recordid";


print "DATABASE QUERY: $command<BR><BR>" if $debug;

my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

print "MATCHES: $num_matches" if $debug;

	while (my @arr = $sth->fetchrow) {
		my ($recordid, $surveysent, $surveysenttwice, $surveyreceived, $email, $date, $documenturl, $documentid) = @arr;
		print "\>" if $debug eq '1';
		my $recipient = "$email";
#		   $recipient = "blitke\@sedl.org"; # FOR DEBUG

		my ($dyear, $dmonth, $ddate) = split(/\-/,$date);

		$downloaddate = "$dmonth\-$ddate\-$dyear";

		################################################################
		##  START: Check if DOCUMENT matches an entry in the product catalog
		################################################################
		my $documenttitle = "";
		my $pdfsearchstring = "$documenturl";
		   $pdfsearchstring =~ s/http\:\/\/www\.sedl\.org//g;
	   
		my $command = "select onlineid, title, title2 from sedlcatalog where "; 
			if ($documentid eq '') {
				$command .= "(locpdf LIKE '%$pdfsearchstring')";
		  	} else {
				$command .= "(unique_id = '$documentid')";
			}
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
				my ($onlineid, $title, $title2) = @arr;
	
			$documenttitle = "$title";
			$documenttitle .= ": $title2" if $title2 ne '';
	 
		} # END DB QUERY
		################################################################
		##  END: Check if DOCUMENT matches an entry in the product catalog
		################################################################
	
	
	#####################################################
	## START: UPDATE DATABASE TO INDICATE IF MAIL SENT
	#####################################################
#	if ($debug ne '1') {
		if ($num_matches eq '0') {

			## IF NO CATALOG MATCH - FLAG AS "NOSEND"
			   $command = "UPDATE clientsurvey SET surveysent='nosend',surveysenttwice='nosend' WHERE recordid='$recordid'";
			print "<LI>NOSEND $command" if $debug;
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
	
			$sendemail = "no";
	
		} else {
			## IF YES, CATALOG MATCH - FLAG SURVEYSENT AS "YES - TODAYSDATE"
	
			if ($surveysent =~ 'yes') {
				## ADD NOTE REGARDING SECOND INVITATION SENDING
				$command = "UPDATE clientsurvey SET surveysenttwice='yes - $date_full_mysql' WHERE recordid='$recordid'";
			} else {
				## ADD NOTE REGARDING FIRST INVITATION SENDING
				$command = "UPDATE clientsurvey SET surveysent='yes - $date_full_mysql' WHERE recordid='$recordid'";
			}
			print "<LI>YESSEND $command" if $debug;
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches = $sth->rows;
	
		} # END IF/ELSE
#	} # END IF
	#####################################################
	## END: UPDATE DATABASE TO INDICATE IF MAIL SENT
	#####################################################


#$sendemail = 'no'; # DEBUG ONLY - COMMENT OUT WHEN LIVE

		#####################################################
		## START: SEND AN E-MAIL IF THERE WERE COMMENTS MADE
		#####################################################
		if ($sendemail eq 'yes') {


			print "<BR>Survey e-mail to: $recipient for<FONT COLOR=RED>$documenttitle - $documenturl</FONT><P>" if ($debug eq '1');


			#if ($recipient eq 'blitke@sedl.org') { # RESTRICTION

			print "<h1>REAL E-MAIL BEING SENT</h1>" if ($debug eq '1');

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: Brian Litke <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Help SEDL by Providing Feedback on the Document You Viewed

SEDL periodically asks visitors to its Web site for feedback in order to maintain a high level of quality of its products and services.  Your input will help us assess our work and its outcomes.

On $downloaddate, you viewed or downloaded a document from the SEDL Web site titled, $documenttitle.  We would appreciate your input about the document and ask that you respond to a short online survey.  All responses to the survey will be confidential.

To access the online survey, visit the link below or paste it into your Web browser.

  http://www.sedl.org/survey/pubs.cgi?e=$email&id=$recordid


If you have any questions, please do not hesitate to call 512-391-6529 and ask for Mr. Brian Litke.  You may also reach SEDL by e-mail if you prefer at:
 - Brian.Litke\@sedl.org

We value your input and appreciate your assistance.  Thank you in advance for your response.

Brian Litke
SEDL Communications

EOM
close(NOTIFY);
#} ## RESTRICTION
		} ## END SEND E-MAIL
		#####################################################
		## END: SEND AN E-MAIL IF THERE WERE COMMENTS MADE
		#####################################################


	## RESET FLAG SO NEXT E-MAIL IS AUTOMATICLALY SENT, UNLESS FLAGGED AS NOSEND
	$sendemail = "yes";


} ## END DATABASE LOOP
################################################################################################################################
## END: DB QUERY - SELECT ALL RECORDS FOR PDFS THAT ARE OVER A WEEK OLD BUT HAVEN'T BEEN SENT A SURVEY
################################################################################################################################

print "</ol>" if ($debug eq '1');







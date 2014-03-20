#!/usr/bin/perl

#####################################################################################################
# Copyright 2010,2012 SEDL
#
# This script is activated weekly to send surveys to people who downloaded 
# PDF documents within the last week.
#
# Written by Brian Litke:	5-13-2002
# Last major update: 		3-8-2012
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

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 

my $query = new CGI;
my $debug = $query->param("debug") || "0"; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS

####################################
## START: GET THE CURRENT DATE INFO
####################################
	use POSIX;
	my $todaysdate = POSIX::strftime('%b %e, %Y, %X', localtime(time)); # (e.g. Mar 6, 2008, 14:39:38)
	my $year = POSIX::strftime('%Y', localtime(time)); # Locale's year (e.g. 2008)
	my $month = POSIX::strftime('%m', localtime(time)); # Locale's numerical month (e.g. 03)
	my $monthdate_wleadingzero = POSIX::strftime('%d', localtime(time)); # Date in month w/leadingzero (e.g. 06)
	my $date_full_mysql = POSIX::strftime('%F', localtime(time)); # Full date in machine-readable "mysql-compatible" format (e.g. 2008-03-06)
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

#$oneweekago = "2010-02-18"; # OR TESTING ONLY
######################################
## END: COMPUTE DATE ONE WEEK AGO
######################################


#############################################
## START: GRAB PRODUCT CATALOG TITLES
#############################################
	my %product_titles_by_id;
	   $product_titles_by_id{'0'} = " title unknown";
	my $command_get_product_titles = "select unique_id, title, title2 from sedlcatalog";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_get_product_titles) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
		while (my @arr = $sth->fetchrow) {
			my ($unique_id, $title, $title2) = @arr;
			$product_titles_by_id{$unique_id} = "$title";
			$product_titles_by_id{$unique_id} .= ": $title2" if $title2 ne '';
	} # END DB QUERY
#############################################
## END: GRAB PRODUCT CATALOG TITLES
#############################################


#############################################
## START: PRINT PAGE HEADER FOR DEBUG OUTPUT
#############################################
	if ($debug eq '1') {
		print header;
print <<EOM;
<html>
<head>
<title>ONE WEEK AGO = $oneweekago</title>
</head>
<body>
EOM
	}
#############################################
## END: PRINT PAGE HEADER FOR DEBUG OUTPUT
#############################################


############################################
## START: SET VARIABLES FOR SENDING E-MAIL
############################################
	my $fromaddr = 'webmaster@sedl.org';
	my $mailprog = "/usr/sbin/sendmail -t -f$fromaddr"; #No -n because of webmaster alias

	## INITIALIZE VARIABLE THAT TRACKS WHETHER WE SHOULD SEND AN E-MAIL TO THIS USER
	my $sendemail = 'yes';
	my $downloaddate = "";
############################################
## END: SET VARIABLES FOR SENDING E-MAIL
############################################


##################################################################################################################################################
## START: GET LIST OF USER E-MAILS WITH SURVEYS TO SEND WITH COUNT OF # OF SURVEYS PER USER, WE USE THIS TO SEND ALL SURVEYS IN A SINGLE E-MAIL
##################################################################################################################################################
	my %sent_already_for_email;
	my %num_surveys_per_email;
	my $command_count_surveys_by_email = "select email FROM clientsurvey 
					WHERE (date < '$oneweekago') AND (surveysent like 'no') 
					AND (surveyreceived = '0000-00-00') 
					order by recordid";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_count_surveys_by_email) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	while (my @arr = $sth->fetchrow) {
		my ($email) = @arr;
		$num_surveys_per_email{$email}++;
	} # END DB QUERY LOOP
##################################################################################################################################################
## END: GET LIST OF USER E-MAILS WITH SURVEYS TO SEND WITH COUNT OF # OF SURVEYS PER USER, WE USE THIS TO SEND ALL SURVEYS IN A SINGLE E-MAIL
##################################################################################################################################################



################################################################################################################################
## START: DB QUERY - SELECT ALL RECORDS FOR PRODUCT ACESSES THAT ARE OVER A WEEK OLD BUT HAVEN'T BEEN SENT A SURVEY
################################################################################################################################
my $command = "select recordid, surveysent, surveysenttwice, surveyreceived, email, date, documenturl, documentid 
FROM clientsurvey 
WHERE (date < '$oneweekago') AND (surveysent like 'no') AND (surveyreceived = '0000-00-00') 
order by recordid";

print "DATABASE QUERY: $command<BR><BR>" if $debug;

my $documenttitle = "";

my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
my $num_matches = $sth->rows;

print "MATCHES: $num_matches<ol>" if $debug;

	while (my @arr = $sth->fetchrow) {
		my ($recordid, $surveysent, $surveysenttwice, $surveyreceived, $email, $date, $documenturl, $documentid) = @arr;
		my $recipient = "$email";
#		   $recipient = "blitke\@sedl.org"; # FOR DEBUG

		#####################################################
		## START: SEND AN E-MAIL
		#####################################################
			if ($num_surveys_per_email{$recipient} > 1) {

				if ($sent_already_for_email{$recipient} ne "yes") {
					###############################################################################
					## START: QUERY DATABASE TO GET ALL THE SURVEY TITLES AND LINKS FOR THIS USER
					###############################################################################
					my $list_of_surveys_and_links = ""; # The text that will be included in the email.
					my $email_for_db = $email;
					   $email_for_db = &commoncode::cleanthisfordb($email_for_db);
					my $command = "select recordid, documentid 
						FROM clientsurvey 
						WHERE (date < '$oneweekago') AND (surveysent like 'no') 
						AND (surveyreceived = '0000-00-00') 
						AND email = '$email_for_db'
						order by recordid";
					my $dbh = DBI->connect($dsn, "corpuser", "public");
					my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
					$sth->execute;
					my $num_matches = $sth->rows;
					my $counter = 1;
						while (my @arr = $sth->fetchrow) {
							my ($this_recordid, $this_documentid) = @arr;
						
							## SET TITLE
							$documenttitle = ""; # RESET TO DEFAULT
							$documenttitle = $product_titles_by_id{$this_documentid};

							## UPDATE DATABASE TO INDICATE IF MAIL SENT FOR THIS SURVEY RECORD
							$sendemail = &indicate_if_survey_sent($this_recordid, $documenttitle);
#							$sendemail = 'no'; # DEBUG ONLY - COMMENT OUT WHEN LIVE

							if ($sendemail ne 'no') {
$list_of_surveys_and_links .= "
($counter) $documenttitle
survey: http://www.sedl.org/survey/pubs.cgi?e=$email&id=$this_recordid
";
								$counter++; # COUNT NUMBER OF DOCS IN SURVEY INVITATION
							} # END IF
						} # END DB QUERY LOOP
				
					###############################################################################
					## END: QUERY DATABASE TO GET ALL THE SURVEY TITLES AND LINKS FOR THIS USER
					###############################################################################
		
				
					#############################################################
					## START: HANDLE SURVEY INVITATION WITH MUTIPLE PRODUCTS
					#############################################################
#					if ($recipient eq 'blitke@sedl.org') { # RESTRICTION FOR BLITKE

						if ($list_of_surveys_and_links ne '') {
print "BRIAN WAS HERE 2.";				
							print "<h2>REAL E-MAIL BEING SENT</h2>" if ($debug eq '1');
							print "<p>Survey e-mail to: $recipient for<br> <span style=\"color:#cc0000;\">$list_of_surveys_and_links</span></p>" if ($debug eq '1');

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: Brian Litke <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Help SEDL by Providing Feedback on One or More of the Documents You Viewed

SEDL periodically asks visitors to its Web site for feedback in order to maintain a high level of quality of its products and services.  Your input will help us assess our work and its outcomes.

You recently viewed $num_surveys_per_email{$recipient} documents from the SEDL Web site.  We would appreciate your input about any of the documents and ask that you respond to a short online survey.  All responses to the survey will be confidential.

You can access one or more of the online surveys by visiting the links below. This is the only survey notice you will receive regarding these documents.


$list_of_surveys_and_links


If you have any questions, please do not hesitate to call 512-391-6529 and ask for Mr. Brian Litke.  You may also reach SEDL by e-mail if you prefer at:
 - Brian.Litke\@sedl.org

We value your input and appreciate your assistance.  Thank you in advance for your response.

Brian Litke
SEDL Communications

EOM
close(NOTIFY);
						} # END IF
						$sent_already_for_email{$recipient} = "yes";
#					} ## RESTRICTION FOR BLITKE
					#############################################################
					## END: HANDLE SURVEY INVITATION WITH MUTIPLE PRODUCTS
					#############################################################
				} # END IF ALREADY SENT TO THIS EMAIL
			} else {
				#############################################################
				## START: HANDLE SURVEY INVITATION WITH ONE PRODUCT
				#############################################################
#				if ($recipient eq 'blitke@sedl.org') { # RESTRICTION FOR BLITKE

					## SET DOWNLOAD DATE FORMAT
					my ($dyear, $dmonth, $ddate) = split(/\-/,$date);
					$downloaddate = "$dmonth\-$ddate\-$dyear";
		
					## SET TITLE
					$documenttitle = ""; # RESET TO DEFAULT
					$documenttitle = $product_titles_by_id{$documentid};

					## UPDATE DATABASE TO INDICATE IF MAIL SENT FOR THIS SURVEY RECORD
					$sendemail = &indicate_if_survey_sent($recordid, $documenttitle);
#					$sendemail = 'no'; # DEBUG ONLY - COMMENT OUT WHEN LIVE
					
					if ($sendemail eq 'yes') {
						print "<h2>REAL E-MAIL BEING SENT</h2>" if ($debug eq '1');
						print "<p>Survey e-mail to: $recipient for <span style=\"color:#cc0000;\">$documenttitle - $documenturl</span></p>" if ($debug eq '1');

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
					} ## END SEND E-MAIL
					#####################################################
					## END: SEND AN E-MAIL
					#####################################################


#				} ## RESTRICTION FOR BLITKE
				#############################################################
				## END: HANDLE SURVEY INVITATION WITH ONE PRODUCT
				#############################################################
		
			} # END IF/ELSE NUMBER OF PRODUCTS MORE THAN 1

	## RESET FLAG SO NEXT E-MAIL IS AUTOMATICLALY SENT, UNLESS FLAGGED AS NOSEND
	$sendemail = "yes"; # RESET TO DEFAULT

} ## END DATABASE LOOP
################################################################################################################################
## END: DB QUERY - SELECT ALL RECORDS FOR PDFS THAT ARE OVER A WEEK OLD BUT HAVEN'T BEEN SENT A SURVEY
################################################################################################################################

print "</ol>" if ($debug eq '1');











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


#####################################################
## START: SUBROUTINE indicate_if_survey_sent
#####################################################
sub indicate_if_survey_sent {
	my $this_recordid = $_[0];
	my $this_documenttitle = $_[1];
	my $this_sendmail = "yes";

		# UPDATE DATABASE TO INDICATE IF MAIL SENT
		if ($this_documenttitle eq '') {
			## IF NO CATALOG MATCH - FLAG AS "NOSEND"
			my $command_update = "UPDATE clientsurvey SET surveysent='nosend',surveysenttwice='nosend' WHERE recordid='$this_recordid'";
			print "<LI>NOSEND $command_update" if $debug;
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_update) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
	
			$this_sendmail = "no"; # MARK SO WE DON'T SEND AN EMAIL

		} else {
			## IF YES, CATALOG MATCH - FLAG SURVEYSENT AS "YES - TODAYSDATE"
			my $command_update = "UPDATE clientsurvey SET surveysent='yes - $date_full_mysql' WHERE recordid='$this_recordid'";
			print "<LI>YESSEND $command_update" if $debug;
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_update) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
		} # END IF/ELSE
	return($this_sendmail);
} # END indicate_if_survey_sent
#####################################################
## END: SUBROUTINE indicate_if_survey_sent
#####################################################


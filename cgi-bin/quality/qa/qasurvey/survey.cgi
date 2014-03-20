#!/usr/bin/perl

#####################################################################################################
# Copyright 2001 by Southwest Educational Development Laboratory
#
# Written by Brian Litke 02-15-2002 for QA Survey
#####################################################################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);

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

#my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
#   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################


## COOKIE VARIABLES
my $expdate = "Fri, 07-Dec-2002 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";


   
my $user = param('user');

my $q1a = param('q1a') || "0";
my $q1b = param('q1b') || "0";
my $q1c = param('q1c') || "0";
my $q1d = param('q1d') || "0";
my $q1e = param('q1e') || "";

my $q2a = param('q2a') || "0";
my $q2b = param('q2b') || "0";
my $q2c = param('q2c') || "0";
my $q2d = param('q2d') || "0";

my $q3a = param('q3a') || "0";
my $q3b = param('q3b') || "0";
my $q3c = param('q3c') || "0";
my $q3d = param('q3d') || "";

my $q4 = param('q4') || "";

my $q5a = param('q5a');
my $q5b = param('q5b');

my $q6a = param('q6a');
my $q6b = param('q6b');

my $q7a = param('q7a');
my $q7b = param('q7b');


my $q8a = param('q8a');
my $q8b = param('q8b');
my $q8c = param('q8c');

my $q9 = param('q9');

my $q10 = param('q10');

my $q11 = param('q11') || "0";

my $q12a = param('q12a');
my $q12b = param('q12b');

my $q13 = param('q13');

### CLEAN UP CARRIAGE RETURNS AND TABS
$user = &cleanthis ($user);
$q1a = &cleanthis ($q1a);
$q1b = &cleanthis ($q1b);
$q1c = &cleanthis ($q1c);
$q1d = &cleanthis ($q1d);
$q1e = &cleanthis ($q1e);
$q2a = &cleanthis ($q2a);
$q2b = &cleanthis ($q2b);
$q2c = &cleanthis ($q2c);
$q2d = &cleanthis ($q2d);
$q3a = &cleanthis ($q3a);
$q3b = &cleanthis ($q3b);
$q3c = &cleanthis ($q3c);
$q3d = &cleanthis ($q3d);
$q4 = &cleanthis ($q4);
$q5a = &cleanthis ($q5a);
$q5b = &cleanthis ($q5b);
$q6a = &cleanthis ($q6a);
$q6b = &cleanthis ($q6b);
$q7a = &cleanthis ($q7a);
$q7b = &cleanthis ($q7b);
$q8a = &cleanthis ($q8a);
$q8b = &cleanthis ($q8b);
$q8c = &cleanthis ($q8c);
$q9 = &cleanthis ($q9);
$q10 = &cleanthis ($q10);
$q11 = &cleanthis ($q11);
$q12a = &cleanthis ($q12a);
$q12b = &cleanthis ($q12b);
$q13 = &cleanthis ($q13);



## IF VALID ENTRY, SEND E-MAIL TO EVALUATION SERVICES



## WRITE THE SURVEY RESULTS TO A FILE
open(SURVEYRESULTSDATA,">>/home/httpd/html/qa/survey-data.txt");
print SURVEYRESULTSDATA "$todaysdate\t$q1a\t$q1b\t$q1c\t$q1d\t$q1e\t$q2a\t$q2b\t$q2c\t$q2d\t$q3a\t$q3b\t$q3c\t$q3d\t$q4\t$q5a\t$q5b\t$q6a\t$q6b\t$q7a\t$q7b\t$q8a\t$q8b\t$q8c\t$q9\t$q10\t$q11\t$q12a\t$q12b\t$q13\t$ipnum $ipnum2\n";
close(SURVEYRESULTSDATA);






## SET MAIL NOTIFICATION VARIABLES
my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
my $recipient = 'blitke@sedl.org';
   $recipient = 'blitke@sedl.org, emccann@sedl.org';



############################# START OF EMAIL TO EVALUATION SERVICES #############################
## WRITE THE SURVEY RESULTS TO AN E-MAIL
my $fromaddr = 'webmaster@sedl.org';

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Data Received - QA Survey Data

The following data was received from the QA Survey Form at:
http://www.sedl.org/qa/survey.html

The results of this survey have been saved to a tab-delimited text file at: 
http://www.sedl.org/qa/survey-data.txt


SURVEY INFORMATION STARTS HERE:
=============================== 

Question 1: The QA Review Process:
$q1a - Improved the Product(s)
$q1b - Was a Pleasant Experience
$q1c - Worked Smoothly
$q1d - Provided Constructive Feedback

Comments: $q1e


Question 2: From a Developer's perspective, please rate the overall usefulness of the feeback you received from each of the following:
$q2a - Internal Review Team
$q2b - COO
$q2c - External Reviewer(s)

Comments: $q2d 


Question 3: From a Reviewer's perspective, please rate your overall experience wit the QA review process:
$q3a - Worked Smoothly
$q3b - Was a Pleasant Experience
$q3c - Informed Me about other SEDL Products

Comments: $q3d 


Question 4: What aspect(s) of the QA review process have worked well/or been helpful?
$q4 


Question 5: What aspect(s) of the QA review process have been most difficult
$q5a 
$q5b 


Question 6: Do you feel that it is important to keep the internal review team apprised of such changes?
$q6a 
$q6b 


Question 7: Should all QA review stages require face-to-face meetings?  
$q7a 
$q7b 


Question 8: What type of feedback would be most useful to you as a developer
$q8a - conceptual
$q8b - early
$q8c - final


Question 9: What type of feedback would be most useful from a reviewer who has little or no expertise in your area?
$q9 


Question 10: Would you like to have input into selecting the internal QA team members who review your products/documents?
$q10


Question 11: What percentage of suggestions made by internal review teams you have served on do you feel were incorporated into the product/document?
$q11 


Question 12: When the words "QA Review" are uttered, what emotion described your initial feeling?
$q12a 
$q12b 

Question 13: Are there any additional suggestions and/or comments you'd like to make about the QA process?
$q13 
 


User Stats:
-----------
Web Browser software: $browser
IP Number: $ipnum2
Domain: $ipnum


EOM
close(NOTIFY);

############################# END OF EMAIL TO EVALUATION SERVICES ############################

print header;

print <<EOM;
<HTML>
<head>
<title>SEDL Staff - Product Quality - QA - Survey</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">

<link rel="stylesheet" href="/css/style1.css">
</HEAD>
<body bgcolor="DED5C8">

<h2>Thank You!</H2>
<P>
Thank you for filing out the QA Survey.  Your input is appreciated.
<P>
Your data has been sent to SEDL's Evaluation Services department.
</BODY>
</HTML>
EOM







####################################
## SUBROUTINES USED BY THIS SCRIPT
####################################

sub cleanthis {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem = $dirtyitem;
}


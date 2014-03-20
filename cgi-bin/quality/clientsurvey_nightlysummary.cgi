#!/usr/bin/perl

#####################################################################################################
# Copyright 2010,2012 by SEDL
#
# This script is activated weekly to send surveys to people who downloaded 
# PDF documents within the last week.
#
# Written by Brian Litke 	2010
# Last Major Update:		2/8/2012
#####################################################################################################
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);

#NEW:
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
## PRINT PAGE HEADER FOR DEBUG OUTPUT
######################################
if ($debug eq '1') {
print header;
print <<EOM;
<HTML>
<head>
<title>Automated Data Summary for Client Surveys</title>
</head>
<BODY>
EOM
}


################################################################################################################################
## START: DB QUERY - DELETE PREVIOUS ROLLUP DATA
################################################################################################################################
my $command = "delete from clientsurvey_summary where css_uniqueid like '%'";
print "<p class=\"info\">DATABASE QUERY TO DELETE PREVIOUS DATA: $command</p>" if ($debug == 1);
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;

## THIS RESETS THE AUTOINCREMENT FIELD TO 1
my $command = "TRUNCATE TABLE clientsurvey_summary";
print "<p class=\"info\">DATABASE QUERY TO RESET AUTO INCREMENT: $command</p>" if ($debug == 1);
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;

################################################################################################################################
## END: DB QUERY - DELETE PREVIOUS ROLLUP DATA
################################################################################################################################


################################################################################################################################
## START: 
################################################################################################################################
## DECLARE HASHES TO TRACK STATS
my %byquarter_count_sent; ###
my %byquarter_count_sentvalid; # COMPUTED
my %byquarter_count_bounced; ###
my %byquarter_count_nosend; ###
my %byquarter_count_received; ###
my %byquarter_response_ratio; # COMPUTED
my %byquarter_followups_req; ###
my %byquarter_followups_made; ###
my %byquarter_commentpermissions; ###
my %byquarter_pending_send; ###

my %bymonth_count_sent; ###
my %bymonth_count_sentvalid; # COMPUTED
my %bymonth_count_bounced; ###
my %bymonth_count_nosend; ###
my %bymonth_count_received; ###
my %bymonth_response_ratio; # COMPUTED
my %bymonth_followups_req; ###
my %bymonth_followups_made; ###
my %bymonth_commentpermissions; ###
my %bymonth_pending_send; ###

my %bydocid_count_sent;
my %bydocid_count_sentvalid;
my %bydocid_count_bounced;
my %bydocid_count_nosend;
my %bydocid_count_received;
my %bydocid_response_ratio;
my %bydocid_followups_req;
my %bydocid_followups_made;
my %bydocid_commentpermissions;
my %bydocid_pending_send;

my %bydoccategory_count_sent;
my %bydoccategory_count_sentvalid;
my %bydoccategory_count_bounced;
my %bydoccategory_count_nosend;
my %bydoccategory_count_received;
my %bydoccategory_response_ratio;
my %bydoccategory_followups_req;
my %bydoccategory_followups_made;
my %bydoccategory_commentpermissions;
my %bydoccategory_pending_send;

my %bystaffid_count_sent;
my %bystaffid_count_sentvalid;
my %bystaffid_count_bounced;
my %bystaffid_count_nosend;
my %bystaffid_count_received;
my %bystaffid_response_ratio;
my %bystaffid_followups_req;
my %bystaffid_followups_made;
my %bystaffid_commentpermissions;
my %bystaffid_pending_send;


	###################################################
	## START: DB QUERY - GRAB SURVEY DATA
	###################################################
	my $command_summary = "select surveysent, surveysenttwice, surveyreceived, date, documenturl, documentid, documentgroup, q1a, request_followup, permission_use_comments, staff_comments_date 
		from clientsurvey";
print "<p class=\"info\">COMMAND TO GET SURVEY DATA: $command_summary</p>" if ($debug == 1);
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_summary) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_ubertotal = $sth->rows;
		while (my @arr = $sth->fetchrow) {
			my ($surveysent, $surveysenttwice, $surveyreceived, $date, $documenturl, $documentid, $documentgroup, $q1a, $request_followup, $permission_use_comments, $staff_comments_date) = @arr;
				my ($quarter_id, $month_id) = &compute_quarter($date);
				   $documentgroup = "notspecified" if ($documentgroup eq '');

			if ($surveysent eq 'no') {
				## SAVE SUMMARY INFORMATION FOR EACH QUARTER
				$byquarter_pending_send{$quarter_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH MONTH
				$bymonth_pending_send{$month_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH PRODUCT CATEGORY
				$bydoccategory_pending_send{$documentgroup}++;

				## SAVE SUMMARY INFORMATION FOR EACH DOCUMENT ID
				$bydocid_pending_send{$documentid}++;

				## SAVE SUMMARY INFORMATION FOR EACH AUTHOR ID


#				$countpending++;
			}
			if ($surveysent ne 'no') {
				## SAVE SUMMARY INFORMATION FOR EACH QUARTER
				$byquarter_count_sent{$quarter_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH MONTH
				$bymonth_count_sent{$month_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH PRODUCT CATEGORY
				$bydoccategory_count_sent{$documentgroup}++;

				## SAVE SUMMARY INFORMATION FOR EACH DOCUMENT ID
				$bydocid_count_sent{$documentid}++;

				## SAVE SUMMARY INFORMATION FOR EACH AUTHOR ID


#				$countsent++;
			}
			if ($q1a ne '') {
				## SAVE SUMMARY INFORMATION FOR EACH QUARTER
				$byquarter_count_received{$quarter_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH MONTH
				$bymonth_count_received{$month_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH PRODUCT CATEGORY
				$bydoccategory_count_received{$documentgroup}++;

				## SAVE SUMMARY INFORMATION FOR EACH DOCUMENT ID
				$bydocid_count_received{$documentid}++;

				## SAVE SUMMARY INFORMATION FOR EACH AUTHOR ID


#				$countreplied++;
			}
			if ($surveysent eq 'nosend') {
				## SAVE SUMMARY INFORMATION FOR EACH QUARTER
				$byquarter_count_nosend{$quarter_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH MONTH
				$bymonth_count_nosend{$month_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH PRODUCT CATEGORY
				$bydoccategory_count_nosend{$documentgroup}++;

				## SAVE SUMMARY INFORMATION FOR EACH DOCUMENT ID
				$bydocid_count_nosend{$documentid}++;

				## SAVE SUMMARY INFORMATION FOR EACH AUTHOR ID


#				$countnotsent++;
			}
			if ($surveysent =~ 'bounced') {
				## SAVE SUMMARY INFORMATION FOR EACH QUARTER
				$byquarter_count_bounced{$quarter_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH MONTH
				$bymonth_count_bounced{$month_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH PRODUCT CATEGORY
				$bydoccategory_count_bounced{$documentgroup}++;

				## SAVE SUMMARY INFORMATION FOR EACH DOCUMENT ID
				$bydocid_count_bounced{$documentid}++;

				## SAVE SUMMARY INFORMATION FOR EACH AUTHOR ID


#				$countbounced++;
			}
			if ($permission_use_comments eq 'yes') {
				## SAVE SUMMARY INFORMATION FOR EACH QUARTER
				$byquarter_commentpermissions{$quarter_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH MONTH
				$bymonth_commentpermissions{$month_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH PRODUCT CATEGORY
				$bydoccategory_commentpermissions{$documentgroup}++;

				## SAVE SUMMARY INFORMATION FOR EACH DOCUMENT ID
				$bydocid_commentpermissions{$documentid}++;

				## SAVE SUMMARY INFORMATION FOR EACH AUTHOR ID


#				$count_perm_use_comments++;
			}
			if ($request_followup =~ 'user requests') {
				## SAVE SUMMARY INFORMATION FOR EACH QUARTER
				$byquarter_followups_req{$quarter_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH MONTH
				$bymonth_followups_req{$month_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH PRODUCT CATEGORY
				$bydoccategory_followups_req{$documentgroup}++;

				## SAVE SUMMARY INFORMATION FOR EACH DOCUMENT ID
				$bydocid_followups_req{$documentid}++;

				## SAVE SUMMARY INFORMATION FOR EACH AUTHOR ID


#				$count_req_followup++;
			}
			if ($staff_comments_date =~ '20') {
				## SAVE SUMMARY INFORMATION FOR EACH QUARTER
				$byquarter_followups_made{$quarter_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH MONTH
				$bymonth_followups_made{$month_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH PRODUCT CATEGORY
				$bydoccategory_followups_made{$documentgroup}++;

				## SAVE SUMMARY INFORMATION FOR EACH DOCUMENT ID
				$bydocid_followups_made{$documentid}++;

				## SAVE SUMMARY INFORMATION FOR EACH AUTHOR ID

#				$count_staff_followup++;
			}

			if ($request_followup =~ 'user requests') {
				## SAVE SUMMARY INFORMATION FOR EACH QUARTER
				$byquarter_followups_req{$quarter_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH MONTH
				$bymonth_followups_req{$month_id}++;

				## SAVE SUMMARY INFORMATION FOR EACH PRODUCT CATEGORY
				$bydoccategory_followups_req{$documentgroup}++;

				## SAVE SUMMARY INFORMATION FOR EACH DOCUMENT ID
				$bydocid_followups_req{$documentid}++;

				## SAVE SUMMARY INFORMATION FOR EACH AUTHOR ID

#				$count_req_followup++;
			}
			

			
		} # END DB QUERY LOOP
	###################################################
	## END: DB QUERY - GRAB SURVEY DATA
	###################################################

################################################################################################################################
## END: DB QUERY - GRAB SURVEY DATA BY DATE AND SAVE SUMMARY INFORMATION FOR EACH QUARTER
################################################################################################################################

################################################################################################################################
## START: ADD SUMMARY COUNTS TO SUMMARY TABLE
################################################################################################################################
print "<hr><h2>QUARTERLY SUMMARY COUNTS</h2>" if ($debug == 1);

## LOOP THROUGH HASH OF QUARTERS AND SAVE TO THE DATABASE
	print "<ol>\n" if ($debug == 1);
	my @ids;
	my $key;
	foreach $key (sort keys %byquarter_count_sent) {
		## SET VARIABLE DEFAULTS
		$byquarter_response_ratio{$key} = 0; # DEFAULT TO 0
		$byquarter_count_sentvalid{$key} = 0 if ($byquarter_count_sentvalid{$key} eq '');
		$byquarter_count_received{$key} = 0 if ($byquarter_count_received{$key} eq '');
		
		## COMPUTE CALCULATED VALUES
		$byquarter_count_sentvalid{$key} = $byquarter_count_sent{$key} - $byquarter_count_bounced{$key};
		$byquarter_response_ratio{$key} = $byquarter_count_received{$key}/$byquarter_count_sentvalid{$key} if (($byquarter_count_sentvalid{$key} != 0) && ($byquarter_count_received{$key} != 0));
		$byquarter_response_ratio{$key} = $byquarter_response_ratio{$key} * 100 if ($byquarter_response_ratio{$key} != 0);
		$byquarter_response_ratio{$key} = &format_number($byquarter_response_ratio{$key}, "2","no"); # ROUND TO 2 DECIMAL PLACES

		my $command = "INSERT INTO clientsurvey_summary VALUES ('', 'byquarter', '$key',
					'$byquarter_count_sent{$key}',
					'$byquarter_count_sentvalid{$key}',
					'$byquarter_count_bounced{$key}',
					'$byquarter_count_nosend{$key}',
					'$byquarter_count_received{$key}',
					'$byquarter_response_ratio{$key}',
					'$byquarter_followups_req{$key}',
					'$byquarter_followups_made{$key}',
					'$byquarter_commentpermissions{$key}',
					'$byquarter_pending_send{$key}',
					'$date_full_mysql')";

		print "<li>$command<br>DATABASE QUERY: SENT: $byquarter_count_sent{$key}
					SENTVALID: $byquarter_count_sentvalid{$key}
					BOUNCED: $byquarter_count_bounced{$key}
					NOSEND: $byquarter_count_nosend{$key}
					RCVD: $byquarter_count_received{$key}
					RATIO: $byquarter_response_ratio{$key}
					FOLLOW-REQ: $byquarter_followups_req{$key}
					FOLLOW-MADE: $byquarter_followups_made{$key}
					PERMIS: $byquarter_commentpermissions{$key}
					PENDING SEND: $byquarter_pending_send{$key}</li>\n" if $debug;

		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
	} # END FOREACH
	print "</ol>\n" if ($debug == 1);

print "<hr><h2>MONTHLY SUMMARY COUNTS</h2>" if ($debug == 1);

## LOOP THROUGH HASH OF MONTHS AND SAVE TO THE DATABASE
	print "<ol>\n" if ($debug == 1);
	my @ids;
	my $key;
	foreach $key (sort keys %bymonth_count_sent) {
		## SET VARIABLE DEFAULTS
		$bymonth_response_ratio{$key} = 0; # DEFAULT TO 0
		$bymonth_count_sentvalid{$key} = 0 if ($bymonth_count_sentvalid{$key} eq '');
		$bymonth_count_received{$key} = 0 if ($bymonth_count_received{$key} eq '');
		
		## COMPUTE CALCULATED VALUES
		$bymonth_count_sentvalid{$key} = $bymonth_count_sent{$key} - $bymonth_count_bounced{$key};
		$bymonth_response_ratio{$key} = $bymonth_count_received{$key}/$bymonth_count_sentvalid{$key} if (($bymonth_count_sentvalid{$key} ne '0') && ($bymonth_count_received{$key} ne '0'));
		$bymonth_response_ratio{$key} = $bymonth_response_ratio{$key} * 100 if ($bymonth_response_ratio{$key} ne '0');
		$bymonth_response_ratio{$key} = &format_number($bymonth_response_ratio{$key}, "2","no"); # ROUND TO 2 DECIMAL PLACES

		my $command = "INSERT INTO clientsurvey_summary VALUES ('', 'bymonth', '$key',
					'$bymonth_count_sent{$key}',
					'$bymonth_count_sentvalid{$key}',
					'$bymonth_count_bounced{$key}',
					'$bymonth_count_nosend{$key}',
					'$bymonth_count_received{$key}',
					'$bymonth_response_ratio{$key}',
					'$bymonth_followups_req{$key}',
					'$bymonth_followups_made{$key}',
					'$bymonth_commentpermissions{$key}',
					'$bymonth_pending_send{$key}',
					'$date_full_mysql')";

		print "<li>$command<br>DATABASE QUERY: SENT: $bymonth_count_sent{$key}
					SENTVALID: $bymonth_count_sentvalid{$key}
					BOUNCED: $bymonth_count_bounced{$key}
					NOSEND: $bymonth_count_nosend{$key}
					RCVD: $bymonth_count_received{$key}
					RATIO: $bymonth_response_ratio{$key}
					FOLLOW-REQ: $bymonth_followups_req{$key}
					FOLLOW-MADE: $bymonth_followups_made{$key}
					PERMIS: $bymonth_commentpermissions{$key}
					PENDING SEND: $bymonth_pending_send{$key}</li>\n" if $debug;

		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
	} # END FOREACH
	print "</ol>\n" if ($debug == 1);

print "<hr><h2>DOCUMENT CATEGORY COUNTS</h2>" if ($debug == 1);

## LOOP THROUGH HASH OF DOCUMENT GROUPS AND SAVE TO THE DATABASE
	print "<ol>\n" if ($debug == 1);
	my @ids;
	my $key;
	foreach $key (sort keys %bydoccategory_count_sent) {
		## SET VARIABLE DEFAULTS
		$bydoccategory_response_ratio{$key} = 0; # DEFAULT TO 0
		$bydoccategory_count_sentvalid{$key} = 0 if ($bydoccategory_count_sentvalid{$key} eq '');
		$bydoccategory_count_received{$key} = 0 if ($bydoccategory_count_received{$key} eq '');

		## COMPUTE CALCULATED VALUES
		$bydoccategory_count_sentvalid{$key} = $bydoccategory_count_sent{$key} - $bydoccategory_count_bounced{$key};
		$bydoccategory_response_ratio{$key} = $bydoccategory_count_received{$key}/$bydoccategory_count_sentvalid{$key} if (($bydoccategory_count_sentvalid{$key} ne '0') && ($bydoccategory_count_received{$key} ne '0'));
		$bydoccategory_response_ratio{$key} = $bydoccategory_response_ratio{$key} * 100 if ($bydoccategory_response_ratio{$key} ne '0');
		$bydoccategory_response_ratio{$key} = &format_number($bydoccategory_response_ratio{$key}, "2","no"); # ROUND TO 2 DECIMAL PLACES

		my $command = "INSERT INTO clientsurvey_summary VALUES ('', 'bydoccategory', '$key',
					'$bydoccategory_count_sent{$key}',
					'$bydoccategory_count_sentvalid{$key}',
					'$bydoccategory_count_bounced{$key}',
					'$bydoccategory_count_nosend{$key}',
					'$bydoccategory_count_received{$key}',
					'$bydoccategory_response_ratio{$key}',
					'$bydoccategory_followups_req{$key}',
					'$bydoccategory_followups_made{$key}',
					'$bydoccategory_commentpermissions{$key}',
					'$bydoccategory_pending_send{$key}',
					'$date_full_mysql')";

		print "<li>DATABASE QUERY: $command</li>\n" if $debug;

		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
	} # END FOREACH
	print "</ol>\n" if ($debug == 1);

print "<hr><h2>DOCUMENT ID COUNTS</h2>" if ($debug == 1);

## LOOP THROUGH HASH OF DOCUMENT IDS AND SAVE TO THE DATABASE
	print "<ol>\n" if ($debug == 1);
	my @ids;
	my $key;
	foreach $key (sort keys %bydocid_count_sent) {
		## SET VARIABLE DEFAULTS
		$bydocid_response_ratio{$key} = 0; # DEFAULT TO 0
		$bydocid_count_sentvalid{$key} = 0 if ($bydocid_count_sentvalid{$key} eq '');
		$bydocid_count_received{$key} = 0 if ($bydocid_count_received{$key} eq '');

		## COMPUTE CALCULATED VALUES
		$bydocid_count_sentvalid{$key} = $bydocid_count_sent{$key} - $bydocid_count_bounced{$key};
		$bydocid_response_ratio{$key} = $bydocid_count_received{$key}/$bydocid_count_sentvalid{$key} if (($bydocid_count_sentvalid{$key} ne '0') && ($bydocid_count_received{$key} ne '0'));
		$bydocid_response_ratio{$key} = $bydocid_response_ratio{$key} * 100 if ($bydocid_response_ratio{$key} ne '0');
		$bydocid_response_ratio{$key} = &format_number($bydocid_response_ratio{$key}, "2","no"); # ROUND TO 2 DECIMAL PLACES

		my $command = "INSERT INTO clientsurvey_summary VALUES ('', 'bydocid', '$key',
					'$bydocid_count_sent{$key}',
					'$bydocid_count_sentvalid{$key}',
					'$bydocid_count_bounced{$key}',
					'$bydocid_count_nosend{$key}',
					'$bydocid_count_received{$key}',
					'$bydocid_response_ratio{$key}',
					'$bydocid_followups_req{$key}',
					'$bydocid_followups_made{$key}',
					'$bydocid_commentpermissions{$key}',
					'$bydocid_pending_send{$key}',
					'$date_full_mysql')";

		print "<li>DATABASE QUERY: $command</li>\n" if ($debug == 1);

		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
	} # END FOREACH
	print "</ol>\n" if ($debug == 1);




########################################################################################################################################################
## START: SUBROUTINES USED BY THIS SCRIPT
########################################################################################################################################################

######################################
## START: SUBROUTINE compute_quarter
######################################
sub compute_quarter {
	my $incoming_date = $_[0];
	my $incoming_year = substr($incoming_date,0,4);
	my $incoming_month = substr($incoming_date,5,2);
	my $quarter_to_return = "";
	my $month_to_return = "$incoming_year\-$incoming_month";
	   $quarter_to_return = "01" if (($incoming_month eq '01') || ($incoming_month eq '02') || ($incoming_month eq '03'));
	   $quarter_to_return = "02" if (($incoming_month eq '04') || ($incoming_month eq '05') || ($incoming_month eq '06'));
	   $quarter_to_return = "03" if (($incoming_month eq '07') || ($incoming_month eq '08') || ($incoming_month eq '09'));
	   $quarter_to_return = "04" if (($incoming_month eq '10') || ($incoming_month eq '11') || ($incoming_month eq '12'));
	   $quarter_to_return = "$incoming_year\-$quarter_to_return";
	   if ($quarter_to_return =~ '0000') {
			print "<br><font color=red>DATE: $incoming_date GAVE: $quarter_to_return</font>" if ($debug == 1);
	   }
	return($quarter_to_return, $month_to_return);
######################################
} # END: SUBROUTINE compute_quarter
######################################


######################################
## START: SUBROUTINE cleanthisfordb
######################################
sub cleanthisfordb {
	my $dirtyitem = $_[0];
	   $dirtyitem =~ s/'/\\'/g;
	   $dirtyitem =~ s/Ô/\\Ô/g;
	   $dirtyitem =~ s/Õ/\\Õ/g;
	   $dirtyitem =~ s/"/\\"/g;
	return($dirtyitem);
}
######################################
## END: SUBROUTINE cleanthisfordb
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


########################################################################################################################################################
## END: SUBROUTINES USED BY THIS SCRIPT
########################################################################################################################################################


#!/usr/bin/perl 

################################################################################
# Copyright 2005-2007 by Southwest Educational Development Laboratory
#
# This script is an admin form used for monitoring registrations for many SEDL events
#
# Location: http://www.sedl.org/cgi-bin/mysql/registration/registration-admin.cgi
# Location: http://www.sedl.org/cgi-bin/mysql/registration/registration.cgi
#
# Written by Brian Litke, SEDL Web Administrator (02-15-2005)
################################################################################

################################
## START: SET SCRIPT HANDLERS ## 
################################
#use diagnostics;
use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use CGI qw/:standard :html3/;

use DBI;
my $dsn = "DBI:mysql:database=corp;host=localhost";
my $dsn_intranet = "DBI:mysql:database=intranet;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;
################################
## END: SET SCRIPT HANDLERS ## 
################################

################################################
## START: LOAD PERL MODULES USED BY THIS SCRIPT
################################################
use Mail::CheckUser qw(check_email);
$Mail::CheckUser::Skip_Network_Checks = 1;

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 
################################################
## END: LOAD PERL MODULES USED BY THIS SCRIPT
################################################

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 
use intranetcommoncode; 


my $debug = 0; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
my $query = new CGI;

## THIS IS A PERL MODULE THAT FORMATS NUMBERS
use Number::Format;
# EXAMPLE OF USAGE
# my $this_number
#	my $x = new Number::Format;
#	$this_number = $x->format_number($this_number, 2, 2);

my $registration_system_closed = "no"; # Change to "yes" to temporarily stop registrations
#   $registration_system_closed = "yes";

##########################################
## START: LOAD THE APPROPRIATE TEMPLATE
##########################################
my $template = "";
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
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("79"); # 79 is the PID for this page in the intranet database

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";

##########################################
## END: LOAD THE APPROPRIATE TEMPLATE
##########################################


###################################
## START: COOKIE DEFAULT VARIABLES
###################################
my $expdate = "Fri, 25-Dec-2015 00:00:00 GMT";
my $thedomain = ".sedl.org";
my $path = "/";
my $xxdate = "";
my $xxpath = "";
my $xxdomain = "";
###################################
## END: COOKIE DEFAULT VARIABLES
###################################


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

my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
   $session_suffix =~ tr/-0-9//cd;

my $max_year = $year + 1;
## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################


########################################################
##  GET VARIABLES FROM FORM AND SET DEFAULT VARIABLES ## 
########################################################
## START: STAFF SESSION VARIABLES
my $location = $query->param("location");
   $location = "show_events" if $location eq '';

my $session_active = "no";
my $error_message = "";
my $feedback_message = "";
	if ($registration_system_closed eq 'yes') {
		$location = "error";
		$error_message = "I apologize for the inconvenience.  The event registration manager is temporarily unavailable while system maintenance is performed.  The form will be available again around 4pm Central time.<br>Brian";
	}

my $session_id = $query->param("s"); # BACKUP IN CASE USER SESSION NOT STORED IN COOKIE
my $logon_user = $query->param("logon_user");
   $logon_user =~ tr/A-Z/a-z/; # lowercase everything (may not be necessary anymore)
	$logon_user =~ s/\@sedl.org//g if ($logon_user =~ '\@sedl.org'); # REMOVE TRAILING "@sedl.org"
my $logon_pass = $query->param("logon_pass");
## END: STAFF SESSION VARIABLES


my $confirm_action = $query->param("confirm_action");
my $show_person = $query->param("show_person");
   $show_person = &commoncode::cleanthisfordb($show_person);
my $show_year = $query->param("show_year");
   $show_year = $year if ($show_year eq '');
   $show_year = "" if ($show_year eq 'any');
my $show_org = $query->param("show_org");
my $show_org_remember_last_request = $query->param("show_org");
my $eventid = $query->param("eventid");
   $eventid = &commoncode::cleanthisfordb($eventid);
my $sortby = $query->param("sortby");
my $printwhat = $query->param("printwhat");
   $printwhat = "attendees" if ($printwhat eq '');

my $lookup_event_for_org = $query->param("lookup_event_for_org");

	#####################################################
	## START: DO A LOOKUP OF THE RELEVANT EVENT
	#####################################################
	if ($lookup_event_for_org ne '') {
	my $command_find_event = "select registration_event_id from registration_orgs where org_unique_id LIKE '%$lookup_event_for_org%'";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_find_event) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $found_registration_event_id = "";
		while (my @arr = $sth->fetchrow) {
	   		($found_registration_event_id) = @arr;
 		}
	
	$location = "show_orgs";
	$eventid = "$found_registration_event_id";
	}
	#####################################################
	## END: DO A LOOKUP OF THE RELEVANT EVENT
	#####################################################

my $new_html_webpage = $query->param("new_html_webpage");

## START: GET EDITED EVENT VARIABLES
my $new_event_page_template = $query->param("new_event_page_template");

my $new_event_discount_reg_date_m = $query->param("new_event_discount_reg_date_m") || "00";
my $new_event_discount_reg_date_d = $query->param("new_event_discount_reg_date_d") || "00";
my $new_event_discount_reg_date_y = $query->param("new_event_discount_reg_date_y") || "0000";
my $new_event_discount_reg_date = "$new_event_discount_reg_date_y\-$new_event_discount_reg_date_m\-$new_event_discount_reg_date_d";

my $new_event_registration_deadline_m = $query->param("new_event_registration_deadline_m") || "00";
my $new_event_registration_deadline_d = $query->param("new_event_registration_deadline_d") || "00";
my $new_event_registration_deadline_y = $query->param("new_event_registration_deadline_y") || "0000";
	if ($new_event_registration_deadline_y ne '0000') {
		$show_year = $new_event_registration_deadline_y;
	}
my $new_event_registration_deadline = "$new_event_registration_deadline_y\-$new_event_registration_deadline_m\-$new_event_registration_deadline_d";

my $new_event_allowreg_startdate_m = $query->param("new_event_allowreg_startdate_m") || "00";
my $new_event_allowreg_startdate_d = $query->param("new_event_allowreg_startdate_d") || "00";
my $new_event_allowreg_startdate_y = $query->param("new_event_allowreg_startdate_y") || "0000";
my $new_event_allowreg_startdate = "$new_event_allowreg_startdate_y\-$new_event_allowreg_startdate_m\-$new_event_allowreg_startdate_d";

my $new_event_allowreg_enddate_m = $query->param("new_event_allowreg_enddate_m") || "00";
my $new_event_allowreg_enddate_d = $query->param("new_event_allowreg_enddate_d") || "00";
my $new_event_allowreg_enddate_y = $query->param("new_event_allowreg_enddate_y") || "0000";
my $new_event_allowreg_enddate = "$new_event_allowreg_enddate_y\-$new_event_allowreg_enddate_m\-$new_event_allowreg_enddate_d";

my $new_event_name = $query->param("new_event_name");
my $new_event_name_italics = $query->param("new_event_name_italics");
my $new_event_name_short = $query->param("new_event_name_short");
my $new_event_date_label = $query->param("new_event_date_label");
my $new_event_location = $query->param("new_event_location");
my $new_event_accomodations = $query->param("new_event_accomodations");
my $new_event_agenda_link = $query->param("new_event_agenda_link");
my $new_event_agenda_link_text = $query->param("new_event_agenda_link_text");
my $new_event_cost_early_single = $query->param("new_event_cost_early_single");
my $new_event_cost_early_multiple = $query->param("new_event_cost_early_multiple");
my $new_event_cost_late_single = $query->param("new_event_cost_late_single");
my $new_event_cost_late_multiple = $query->param("new_event_cost_late_multiple");
my $new_event_registration_warning = $query->param("new_event_registration_warning");
my $new_event_cancellation_instructions = $query->param("new_event_cancellation_instructions");
my $new_event_text_thankyou = $query->param("new_event_text_thankyou");
my $new_event_payment_instructions = $query->param("new_event_payment_instructions");
my $new_event_email_noticesto = $query->param("new_event_email_noticesto");
my $new_event_contact_name = $query->param("new_event_contact_name");
my $new_event_contact_email = $query->param("new_event_contact_email");
my $new_event_created_on = $query->param("new_event_created_on");
my $new_event_created_by = $query->param("new_event_created_by");
my $new_event_editable_by = $query->param("new_event_editable_by");
   $new_event_editable_by =~ s/\@sedl\.org//gi;
my $new_event_prompt_address_home = $query->param("new_event_prompt_address_home");
my $new_event_prompt_table = $query->param("new_event_prompt_table");
my $new_event_prompt_table_text = $query->param("new_event_prompt_table_text");
my $new_event_prompt_data_hotel = $query->param("new_event_prompt_data_hotel");
my $new_event_prompt_data_cc_affiliation = $query->param("new_event_prompt_data_cc_affiliation");
my $new_event_prompt_data_cc_esc = $query->param("new_event_prompt_data_cc_esc");
my $new_event_prompt_data_cc_role = $query->param("new_event_prompt_data_cc_role");
my $new_event_prompt_data_cc_previous = $query->param("new_event_prompt_data_cc_previous");
my $new_event_prompt_data_cc_focus = $query->param("new_event_prompt_data_cc_focus");
my $new_event_prompt_data_afterschool = $query->param("new_event_prompt_data_afterschool");
my $new_event_prompt_inperson_online = $query->param("new_event_prompt_inperson_online");
my $new_event_cost_free = $query->param("new_event_cost_free");
my $new_event_cost_table = $query->param("new_event_cost_table");
my $new_event_comp_code = $query->param("new_event_comp_code");
my $new_event_preevent = $query->param("new_event_preevent");
my $new_event_preevent_name = $query->param("new_event_preevent_name");
my $new_event_preevent_cost = $query->param("new_event_preevent_cost");
my $new_event_max_registrants = $query->param("new_event_max_registrants");
my $new_event_max_reg_nextform_id = $query->param("new_event_max_reg_nextform_id");
my $new_event_min_registrants_perform = $query->param("new_event_min_registrants_perform");
my $new_event_max_registrants_perform = $query->param("new_event_max_registrants_perform");
my $new_event_confirm_checkbox = $query->param("new_event_confirm_checkbox");

my $new_event_prompt_session_choice = $query->param("new_event_prompt_session_choice");
my $new_event_session1_title = $query->param("new_event_session1_title");
my $new_event_session1_desc = $query->param("new_event_session1_desc");
my $new_event_session2_title = $query->param("new_event_session2_title");
my $new_event_session2_desc = $query->param("new_event_session2_desc");

my $new_event_allow_pay_atdoor = $query->param("new_event_allow_pay_atdoor");
my $new_event_allow_pay_atdoor_text = $query->param("new_event_allow_pay_atdoor_text");
my $new_event_allow_pay_invoiceme = $query->param("new_event_allow_pay_invoiceme");
my $new_event_allow_pay_invoiceme_text = $query->param("new_event_allow_pay_invoiceme_text");
my $new_event_prompt_individual_org = $query->param("new_event_prompt_individual_org");
my $new_event_extra_text = $query->param("new_event_extra_text");
my $new_event_budgetcode = $query->param("new_event_budgetcode");

my $new_event_custom_question1_text = $query->param("new_event_custom_question1_text");
my $new_event_custom_question1_type = $query->param("new_event_custom_question1_type");
my $new_event_custom_question1_options = $query->param("new_event_custom_question1_options");

my $new_event_custom_question2_text = $query->param("new_event_custom_question2_text");
my $new_event_custom_question2_type = $query->param("new_event_custom_question2_type");
my $new_event_custom_question2_options = $query->param("new_event_custom_question2_options");

	$new_event_custom_question1_text =~ s/"/'/gi;
	$new_event_custom_question1_options =~ s/"/'/gi;
	$new_event_custom_question2_text =~ s/"/'/gi;
	$new_event_custom_question2_options =~ s/"/'/gi;
my $new_event_custom_opentext_question1 = $query->param("new_event_custom_opentext_question1");
my $new_event_custom_opentext_question2 = $query->param("new_event_custom_opentext_question2");
my $new_event_custom_opentext_afterschool1 = $query->param("new_event_custom_opentext_afterschool1");
my $new_event_custom_opentext_afterschool2 = $query->param("new_event_custom_opentext_afterschool2");

## END: GET EDITED EVENT VARIABLES

## START: CLEAN OPEN-ENDED VARIABLES
$new_event_name = &commoncode::cleanthisfordb($new_event_name);
$new_event_name_italics = &commoncode::cleanthisfordb($new_event_name_italics);
$new_event_name_short = &commoncode::cleanthisfordb($new_event_name_short);
$new_event_date_label = &commoncode::cleanthisfordb($new_event_date_label);
$new_event_location = &commoncode::cleanthisfordb($new_event_location);
$new_event_accomodations = &commoncode::cleanthisfordb($new_event_accomodations);
$new_event_agenda_link = &commoncode::cleanthisfordb($new_event_agenda_link);
$new_event_agenda_link_text = &commoncode::cleanthisfordb($new_event_agenda_link_text);
$new_event_cost_early_single = &commoncode::cleanthisfordb($new_event_cost_early_single);
$new_event_cost_early_multiple = &commoncode::cleanthisfordb($new_event_cost_early_multiple);
$new_event_cost_late_single = &commoncode::cleanthisfordb($new_event_cost_late_single);
$new_event_cost_late_multiple = &commoncode::cleanthisfordb($new_event_cost_late_multiple);
$new_event_registration_warning = &commoncode::cleanthisfordb($new_event_registration_warning);
$new_event_cancellation_instructions = &commoncode::cleanthisfordb($new_event_cancellation_instructions);
$new_event_text_thankyou = &commoncode::cleanthisfordb($new_event_text_thankyou);
$new_event_payment_instructions = &commoncode::cleanthisfordb($new_event_payment_instructions);
$new_event_email_noticesto = &commoncode::cleanthisfordb($new_event_email_noticesto);
$new_event_contact_name = &commoncode::cleanthisfordb($new_event_contact_name);
$new_event_contact_email = &commoncode::cleanthisfordb($new_event_contact_email);
$new_event_created_on = &commoncode::cleanthisfordb($new_event_created_on);
$new_event_created_by = &commoncode::cleanthisfordb($new_event_created_by);
$new_event_editable_by = &commoncode::cleanthisfordb($new_event_editable_by);
$new_event_prompt_address_home = &commoncode::cleanthisfordb($new_event_prompt_address_home);
$new_event_prompt_table = &commoncode::cleanthisfordb($new_event_prompt_table);
$new_event_prompt_table_text = &commoncode::cleanthisfordb($new_event_prompt_table_text);
$new_event_prompt_data_hotel = &commoncode::cleanthisfordb($new_event_prompt_data_hotel);
$new_event_prompt_data_cc_affiliation = &commoncode::cleanthisfordb($new_event_prompt_data_cc_affiliation);
$new_event_prompt_data_cc_esc = &commoncode::cleanthisfordb($new_event_prompt_data_cc_esc);
$new_event_prompt_data_cc_role = &commoncode::cleanthisfordb($new_event_prompt_data_cc_role);
$new_event_prompt_data_cc_previous = &commoncode::cleanthisfordb($new_event_prompt_data_cc_previous);
$new_event_prompt_data_cc_focus = &commoncode::cleanthisfordb($new_event_prompt_data_cc_focus);
$new_event_prompt_data_afterschool = &commoncode::cleanthisfordb($new_event_prompt_data_afterschool);
$new_event_prompt_inperson_online = &commoncode::cleanthisfordb($new_event_prompt_inperson_online);
$new_event_cost_free = &commoncode::cleanthisfordb($new_event_cost_free);
$new_event_cost_table = &commoncode::cleanthisfordb($new_event_cost_table);
$new_event_comp_code = &commoncode::cleanthisfordb($new_event_comp_code);
$new_event_preevent = &commoncode::cleanthisfordb($new_event_preevent);
$new_event_preevent_name = &commoncode::cleanthisfordb($new_event_preevent_name);
$new_event_preevent_cost = &commoncode::cleanthisfordb($new_event_preevent_cost);
$new_event_max_registrants = &commoncode::cleanthisfordb($new_event_max_registrants);
$new_event_max_reg_nextform_id = &commoncode::cleanthisfordb($new_event_max_reg_nextform_id);
$new_event_min_registrants_perform = &commoncode::cleanthisfordb($new_event_min_registrants_perform);
$new_event_max_registrants_perform = &commoncode::cleanthisfordb($new_event_max_registrants_perform);
$new_event_confirm_checkbox = &commoncode::cleanthisfordb($new_event_confirm_checkbox);
$new_event_prompt_session_choice = &commoncode::cleanthisfordb($new_event_prompt_session_choice);
$new_event_session1_title = &commoncode::cleanthisfordb($new_event_session1_title);
$new_event_session1_desc = &commoncode::cleanthisfordb($new_event_session1_desc);
$new_event_session2_title = &commoncode::cleanthisfordb($new_event_session2_title);
$new_event_session2_desc = &commoncode::cleanthisfordb($new_event_session2_desc);

$new_event_allow_pay_atdoor = &commoncode::cleanthisfordb($new_event_allow_pay_atdoor);
$new_event_allow_pay_atdoor_text = &commoncode::cleanthisfordb($new_event_allow_pay_atdoor_text);
$new_event_allow_pay_invoiceme = &commoncode::cleanthisfordb($new_event_allow_pay_invoiceme);
$new_event_allow_pay_invoiceme_text = &commoncode::cleanthisfordb($new_event_allow_pay_invoiceme_text);
$new_event_prompt_individual_org = &commoncode::cleanthisfordb($new_event_prompt_individual_org);
$new_event_extra_text = &commoncode::cleanthisfordb($new_event_extra_text);
$new_event_budgetcode = &commoncode::cleanthisfordb($new_event_budgetcode);

$new_event_custom_question1_text = &commoncode::cleanthisfordb($new_event_custom_question1_text);
$new_event_custom_question1_type = &commoncode::cleanthisfordb($new_event_custom_question1_type);
$new_event_custom_question1_options = &commoncode::cleanthisfordb($new_event_custom_question1_options);

$new_event_custom_question2_text = &commoncode::cleanthisfordb($new_event_custom_question2_text);
$new_event_custom_question2_type = &commoncode::cleanthisfordb($new_event_custom_question2_type);
$new_event_custom_question2_options = &commoncode::cleanthisfordb($new_event_custom_question2_options);

$new_event_custom_opentext_question1 = &commoncode::cleanthisfordb($new_event_custom_opentext_question1);
$new_event_custom_opentext_question2 = &commoncode::cleanthisfordb($new_event_custom_opentext_question2);
$new_event_custom_opentext_afterschool1 = &commoncode::cleanthisfordb($new_event_custom_opentext_afterschool1);
$new_event_custom_opentext_afterschool2 = &commoncode::cleanthisfordb($new_event_custom_opentext_afterschool2);
## END: CLEAN OPEN-ENDED VARIABLES

my $ipnum = $ENV{"REMOTE_HOST"};
my $ipnum2 = $ENV{"REMOTE_ADDR"};
my $browser = $ENV{"HTTP_USER_AGENT"};


	## START: GRAB BASIC INFORMATION ABOUT THE EVENT, IF AN EVENT ID WAS PASSED
	my $this_event_id = ""; my $this_event_name = ""; my $this_event_name_italics = ""; my $this_event_name_short = ""; my $this_event_date_label = ""; my $this_event_location = ""; my $this_event_accomodations = ""; my $this_event_agenda_link = ""; my $this_event_agenda_link_text = ""; my $this_event_discount_reg_date = ""; my $this_event_cost_early_single = ""; my $this_event_cost_early_multiple = ""; my $this_event_cost_late_single = ""; my $this_event_cost_late_multiple = ""; my $this_event_registration_deadline = ""; my $this_event_registration_warning = ""; my $this_event_cancellation_instructions = ""; my $this_event_payment_instructions = ""; my $this_event_email_noticesto = ""; my $this_event_contact_name = ""; my $this_event_contact_email = ""; my $this_event_allowreg_startdate = ""; my $this_event_allowreg_enddate = ""; my $this_event_created_on = ""; my $this_event_created_by = ""; my $this_event_page_template  = ""; my $this_event_prompt_table = ""; my $this_event_prompt_table_text = ""; my $this_event_prompt_address_home = ""; my $this_event_prompt_data_hotel = ""; my $this_event_prompt_data_cc_affiliation = ""; my $this_event_prompt_data_cc_esc = ""; my $this_event_prompt_data_cc_role = ""; my $this_event_prompt_data_cc_previous = ""; my $this_event_prompt_data_cc_focus = ""; my $this_event_prompt_data_afterschool = ""; my $this_event_prompt_inperson_online = ""; my $this_event_cost_free = ""; my $this_event_cost_table = ""; my $this_event_comp_code = ""; my $this_event_max_registrants = ""; my $this_event_max_reg_nextform_id = ""; my $this_event_max_registrants_perform = ""; my $this_event_min_registrants_perform = ""; my $this_event_confirm_checkbox = ""; my $this_event_prompt_session_choice = ""; my $this_event_session1_title = ""; my $this_event_session1_desc = ""; my $this_event_session2_title = ""; my $this_event_session2_desc = ""; my $this_event_allow_pay_atdoor = ""; my $this_event_allow_pay_atdoor_text = ""; my $this_event_allow_pay_invoiceme = ""; my $this_event_allow_pay_invoiceme_text = ""; my $this_event_prompt_individual_org = ""; my $this_event_custom_question1_text = ""; my $this_event_custom_question1_type = ""; my $this_event_custom_question2_text = ""; my $this_event_custom_question2_type = ""; my $this_event_custom_opentext_question1 = ""; my $this_event_custom_opentext_question2 = ""; my $this_event_custom_opentext_afterschool1 = ""; my $this_event_custom_opentext_afterschool2 = "";


	if ($eventid ne '') {
		my $command = "SELECT event_name, event_name_italics, event_name_short, event_date_label, event_location, event_prompt_table, event_prompt_table_text, event_prompt_address_home, event_prompt_data_hotel, event_prompt_data_cc_affiliation, event_prompt_data_cc_esc, event_prompt_data_cc_role, event_prompt_data_cc_previous, event_prompt_data_cc_focus, event_prompt_data_afterschool, event_prompt_inperson_online, event_cost_free, event_cost_table, event_comp_code, event_max_registrants, event_max_reg_nextform_id, event_max_registrants_perform, event_min_registrants_perform, event_confirm_checkbox, event_prompt_session_choice, event_session1_title, event_session1_desc, event_session2_title, event_session2_desc, event_allow_pay_atdoor, event_allow_pay_atdoor_text, event_allow_pay_invoiceme, event_allow_pay_invoiceme_text, event_prompt_individual_org, event_custom_question1_text, event_custom_question1_type, event_custom_question2_text, event_custom_question2_type, event_custom_opentext_question1, event_custom_opentext_question2, event_custom_opentext_afterschool1, event_custom_opentext_afterschool2
						from registration_events WHERE event_id like '$eventid'";
		my $dsn = "DBI:mysql:database=corp;host=localhost";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_editevent = $sth->rows;
#print header;
#print "<P>COMMAND: $command";
		my $counter = "1";
	
		while (my @arr = $sth->fetchrow) {
	   		($this_event_name, $this_event_name_italics, $this_event_name_short, $this_event_date_label, $this_event_location, $this_event_prompt_table, $this_event_prompt_table_text, $this_event_prompt_address_home, $this_event_prompt_data_hotel, $this_event_prompt_data_cc_affiliation, $this_event_prompt_data_cc_esc, $this_event_prompt_data_cc_role, $this_event_prompt_data_cc_previous, $this_event_prompt_data_cc_focus, $this_event_prompt_data_afterschool, $this_event_prompt_inperson_online, $this_event_cost_free, $this_event_cost_table, $this_event_comp_code, $this_event_max_registrants, $this_event_max_reg_nextform_id, $this_event_max_registrants_perform, $this_event_min_registrants_perform, $this_event_confirm_checkbox, $this_event_prompt_session_choice, $this_event_session1_title, $this_event_session1_desc, $this_event_session2_title, $this_event_session2_desc, $this_event_allow_pay_atdoor, $this_event_allow_pay_atdoor_text, $this_event_allow_pay_invoiceme, $this_event_allow_pay_invoiceme_text, $this_event_prompt_individual_org, $this_event_custom_question1_text, $this_event_custom_question1_type, $this_event_custom_question2_text, $this_event_custom_question2_type, $this_event_custom_opentext_question1, $this_event_custom_opentext_question2, $this_event_custom_opentext_afterschool1, $this_event_custom_opentext_afterschool2) = @arr;
 		} # END DB QUERYLOOP
	} # END IF
	## END: GRAB BASIC INFORMATION ABOUT THE EVENT, IF AN EVENT ID WAS PASSED




####################################################
# START: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################
my $cookie_ss_session_id = ""; # TRACK SESSION ID
my $cookie_ss_staff_id = ""; # TRACK USER ID
my $cookie_search_fav = ""; # TRACK USER ID
	my(%cookies) = getCookies();
	foreach (sort(keys(%cookies))) {
		$cookie_ss_session_id = $cookies{$_} if (($_ eq 'ss_session_id') && ($session_id eq ''));
		$cookie_ss_staff_id = $cookies{$_} if ($_ eq 'staffid');
		$cookie_search_fav = $cookies{$_} if ($_ eq 'intranetsearch');
	}
	$cookie_ss_session_id = $session_id if (($cookie_ss_session_id eq '') && ($session_id ne '')); # IF NOT SESSION IN COOKIE, BUT ONE PASSED VIA URL, USE SESSION FROM URL
####################################################
# END: GRAB STAFF SESSION ID, IF ANY, FROM COOKIE
####################################################

## START: SET SEARCH FORM TO USER'S LAST DATA SOURCE SEARCH SELECTION
if ($cookie_search_fav ne '') {
	$htmlhead =~ s/\<option value\=\"$cookie_search_fav\"\>/\<option value\=\"$cookie_search_fav\" SELECTED\>/gi;
}
## END: SET SEARCH FORM TO USER'S LAST DATA SOURCE SEARCH SELECTION


######################################################
## START: LOCATION = PROCESS_LOGON
######################################################
if ($location eq 'process_logon') {
	if (($logon_user ne '') && ($logon_pass ne '')) {
		## CHECK LOGON
		my $strong_pwd = crypt($logon_pass,'password');
		my $command = "select userid from staff_profiles where 
			((userid like '$logon_user') AND (strong_pwd LIKE '$strong_pwd') )";
		my $dsn = "DBI:mysql:database=intranet;host=localhost";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;

		my $command = "select userid from staff_profiles where 
			(userid like '$logon_user')";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_for_logon_id_entered = $sth->rows;

		if ($num_matches eq '1') {
			$cookie_ss_session_id = "$logon_user$session_suffix";
			## VALID ID/PASSWORD, SET SESSION
				my $command_set_session = "REPLACE into staff_sessions VALUES ('$cookie_ss_session_id', '$logon_user', '$timestamp', '$ipnum2', '', '', '', '')";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_set_session) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
		
			## SAVE COOKIES WITH SESSION ID AND USER ID
				$cookie_ss_staff_id = $logon_user;
				setCookie ("ss_session_id", "$cookie_ss_session_id ", $expdate, $path, $thedomain);
				setCookie ("staffid", $logon_user, $expdate, $path, $thedomain);
				
			## SET LOCATION
				$location = "show_events";

		} else {
			## INVALID ID/PASS: SHOW LOON SCREEN & SET ERROR MESSAGE
			if ($num_matches_for_logon_id_entered == 1) {
				$error_message = "Your User ID ($logon_user) is valid, but the password you entered did not match the one on file.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
			} else {
				if (length($logon_user) > 8) {
					$error_message = "The User ID you entered ($logon_user) is over 8 characters long.  The intranet logon is your first initial and last name, with a maximum limit of 8 characters.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
				} else {
					$error_message = "The User ID you entered ($logon_user) is not a valid SEDL staff ID.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
				}
			}
			$location = "logon"; # SHOW LOGON SCREEN
		}
	} else {
	## USER DIDN't ENTER USER ID OR PASSWORD, SHOW LOON SCREEN & SET ERROR MESSAGE
		$error_message .= "You forgot to enter your User ID (ex: whoover)." if ($logon_user eq '');
		$error_message .= "You forgot to enter your password." if ($logon_pass eq '');
	}
}
######################################################
## END: LOCATION = PROCESS_LOGON
######################################################
######################################################
## START: LOCATION = LOGOUT
######################################################
if ($location eq 'logout') {
	## DELETE SESSION IN RF_SESSION DB
	my $command_delete_session = "DELETE FROM staff_sessions WHERE ss_session_id='$cookie_ss_session_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_delete_session) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
	$cookie_ss_session_id = "";
	$location = "logon"; # AFTER LOGOUT, SHOW LOGON SCREEN
}
######################################################
## END: LOCATION = LOGOUT
######################################################


######################################################
## START: CHECK SESSION ID AND VERIFY
######################################################
	## IF SESSION ID NOT FOUND, PROMPT FOR LOGON
	if ($cookie_ss_session_id eq '') {
		$location = "logon";
	} else {	
	## IF SESSION ID FOUND, CHECK IF CURRENT
	my $command = "select * from staff_sessions where ss_session_id like '$cookie_ss_session_id'";
	my $dsn = "DBI:mysql:database=intranet;host=localhost";
	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
#   $error_message .= "<P>NOTICE: $command";

		while (my @arr = $sth->fetchrow) {
		my ($ss_session_id, $ss_staff_id, $ss_session_last_active, $ss_ip_number, $ss_session_active, $ss_staff_project, $ss_search_site, $ss_last_page_viewed) = @arr;
			## IF SESSION STILL ACTIVE, SET TIMESTAMP IN SESSION DB
				my $command_update_session = "REPLACE into staff_sessions VALUES ('$ss_session_id', '$ss_staff_id', '$timestamp', '$ipnum2', '', '', '' ,'')";
				my $dsn = "DBI:mysql:database=intranet;host=localhost";
				my $dbh = DBI->connect($dsn, "intranetuser", "limited");
				my $sth = $dbh->prepare($command_update_session) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
		
			## SAVE COOKIES WITH SESSION ID AND USER ID
				setCookie ("ss_session_id", $ss_session_id, $expdate, $path, $thedomain);
				setCookie ("staffid", $ss_staff_id, $expdate, $path, $thedomain);
				$logon_user = $ss_staff_id;

		} # END DB QUERY LOOP

		## IF SESSION NOT ACTIVE, PROMPT FOR LOGON
		if ($num_matches eq '0') {
			$cookie_ss_session_id = "";
#			setCookie ("ss_session_id", "", $expdate, $path, $thedomain);
			$location = "logon"; # AFTER LOGOUT, SHOW LOGON SCREEN
		} else {
			$session_active = "yes";
			if (($session_active eq 'yes') && ($location eq 'logon')) {
				$location = "show_events";
			}
		}
	}
######################################################
## END: CHECK SESSION ID AND VERIFY
######################################################



################################
## START: PRINT PAGE HEADER
################################
print header;

if ($location !~ 'printable_') {
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Event Registration Manager</title>
$htmlhead
EOM

} else {

print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Event Registration Manager</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<link href="/staff/includes/staff2012.css" rel="stylesheet" type="text/css" media="all">
<link href="/staff/includes/staff2012_print.css" rel="stylesheet" type="text/css" media="print">
<style type="text/css">
<!--
body {background-color: #ffffff;margin:0;}
h2 {font-size:20px;line-height:26px;color:#000000;}
-->
</style>
</head>
<body>
EOM
}
################################
## END: PRINT PAGE HEADER
################################


###################################
## START: IF LOCATION = ERROR
###################################
if ($location eq 'error') {
print<<EOM;
<h1 style="margin-top:0;">Registration Admin Temporarily Closed</h1>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';
}
###################################
## END: IF LOCATION = ERROR
###################################


######################################################################################
## START: LOCATION = LOGON
######################################################################################
if ($location eq 'logon') {
print<<EOM;
<h1 style="margin-top:0;">SEDL Event Registration Manager</h1>
<H2>Please Log On</H2>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p>
Please enter your SEDL staff ID and password.  
</p>
<form action="registration-admin.cgi" method="POST">
<table BORDER="0" CELLPADDING="10" CELLSPACING="0">
<TR><TD VALIGN="TOP"><strong>Your user ID</strong><br>
  		  (ex: sliberty)</TD>
    <TD VALIGN="TOP"><INPUT TYPE="text" NAME="logon_user" SIZE="8" VALUE="$cookie_ss_staff_id"></TD></TR>
<TR><TD VALIGN="TOP" WIDTH="120"><strong>Your intranet password</strong><br>
  			<SPAN class="small">(not your email password)</SPAN></TD>
    <TD VALIGN="TOP"><INPUT TYPE="PASSWORD" NAME="logon_pass" SIZE="8"></TD></TR>
</table>

  <div style="margin-left:25px;">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_logon">
  <INPUT TYPE="SUBMIT" VALUE="Log In Now">
  </div>
  </form>
EOM
} elsif ($location =~ 'printable_') {
} else {
print<<EOM;
<h1 style="margin-top:0;"><A HREF="http://www.sedl.org/staff/communications/registration-admin.cgi?show_year=$show_year">SEDL Event Registration Manager</A>  <SPAN class=small>(Click here to <A HREF="registration-admin.cgi?location=logout">logout</A>)</SPAN></h1>
EOM
}
######################################################################################
## END: LOCATION = LOGON
######################################################################################


##############################################
## START: LOCATION = PROCESS_REQUEST_HTML_PAGE
##############################################
if ($location eq 'process_request_html_page') {
	## SET MAIL NOTIFICATION VARIABLES
	my $mailprog = '/usr/sbin/sendmail -t'; #No -n because of webmaster alias
	my $recipient = 'blitke@sedl.org';



############################# START OF EMAIL TO BRIAN #############################
## WRITE THE SURVEY RESULTS TO AN EMAIL
my $fromaddr = 'webmaster@sedl.org';

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: SEDL Event Manager <$fromaddr>
To: $recipient
Reply-To: $fromaddr
Errors-To: $fromaddr
Sender: $fromaddr
Subject: Request to Set up HTML page for registration form

REQUEST FOR HTML WEB PAGE FOR A REGISTRATION FORM:
==================================================

$cookie_ss_session_id is requesting that you set up an HTML page at:
$new_html_webpage

for the event: $eventid $this_event_name

Here's the code:

<!--#include virtual="/cgi-bin/mysql/registration/registration.cgi?eventid=$eventid"-->

EOM
close(NOTIFY);
	print "<P><FONT COLOR=GREEN>Your request was sent to Brian by email.  Brian will contact you when the address is set up and ready to use.</FONT>";
	$location = "show_events";

}
#############################################
## END: LOCATION = PROCESS_REQUEST_HTML_PAGE
#############################################

########################################
## START: LOCATION = PROCESS_EDIT_EVENT
########################################

## START: CHECK IF USER IS AUTHORIZED
if (($location eq 'process_edit_event') && ($eventid ne '')) {
	my $user_authorized = "";
	my $new_location = "";
	($user_authorized, $error_message) = &check_user_authorized($eventid, $cookie_ss_staff_id);
	if ($user_authorized ne 'yes') {
		$location = "show_events";
	}
}
## END: CHECK IF USER IS AUTHORIZED

if ($location eq 'process_edit_event') {

	my $added_edited = "added";
	my $command = "INSERT INTO registration_events VALUES ('', '$new_event_name', '$new_event_name_italics', '$new_event_name_short', '$new_event_date_label', '$new_event_location', '$new_event_accomodations', '$new_event_agenda_link', '$new_event_agenda_link_text', '$new_event_discount_reg_date', '$new_event_cost_early_single', '$new_event_cost_early_multiple', '$new_event_cost_late_single', '$new_event_cost_late_multiple', '$new_event_cost_free', '$new_event_cost_table', '$new_event_comp_code', '$new_event_registration_deadline', '$new_event_registration_warning', '$new_event_cancellation_instructions', '$new_event_payment_instructions', '$new_event_text_thankyou', '$new_event_email_noticesto', '$new_event_contact_name', '$new_event_contact_email', '$new_event_allowreg_startdate', '$new_event_allowreg_enddate', '$date_full_mysql', '$cookie_ss_staff_id', '$new_event_editable_by', '$new_event_page_template', '$new_event_prompt_table', '$new_event_prompt_table_text', '$new_event_prompt_address_home', '$new_event_prompt_data_hotel', '$new_event_prompt_data_cc_affiliation', '$new_event_prompt_data_cc_esc', '$new_event_prompt_data_cc_role', '$new_event_prompt_data_cc_previous', '$new_event_prompt_data_cc_focus', '$new_event_prompt_data_afterschool', '$new_event_prompt_inperson_online', '$new_event_preevent', '$new_event_preevent_name', '$new_event_preevent_cost', '$new_event_max_registrants', '$new_event_max_reg_nextform_id', '$new_event_max_registrants_perform', '$new_event_min_registrants_perform', '$new_event_confirm_checkbox', '$new_event_prompt_session_choice', '$new_event_session1_title', '$new_event_session1_desc', '$new_event_session2_title', '$new_event_session2_desc', '$new_event_allow_pay_atdoor', '$new_event_allow_pay_atdoor_text', '$new_event_allow_pay_invoiceme', '$new_event_allow_pay_invoiceme_text', '$new_event_prompt_individual_org', '$new_event_extra_text', '$new_event_budgetcode', '$new_event_custom_question1_text', '$new_event_custom_question1_type', '$new_event_custom_question1_options', '$new_event_custom_question2_text', '$new_event_custom_question2_type', '$new_event_custom_question2_options', '$new_event_custom_opentext_question1', '$new_event_custom_opentext_question2', '$new_event_custom_opentext_afterschool1', '$new_event_custom_opentext_afterschool2')";

		if ($eventid ne '') {
			$command = "UPDATE registration_events SET event_name = '$new_event_name', event_name_italics = '$new_event_name_italics', event_name_short = '$new_event_name_short', event_date_label = '$new_event_date_label', event_location = '$new_event_location', event_accomodations = '$new_event_accomodations', event_agenda_link = '$new_event_agenda_link', event_agenda_link_text = '$new_event_agenda_link_text', event_discount_reg_date = '$new_event_discount_reg_date', event_cost_early_single = '$new_event_cost_early_single', event_cost_early_multiple = '$new_event_cost_early_multiple', event_cost_late_single = '$new_event_cost_late_single', event_cost_late_multiple = '$new_event_cost_late_multiple', event_registration_deadline = '$new_event_registration_deadline', event_registration_warning = '$new_event_registration_warning', event_cancellation_instructions = '$new_event_cancellation_instructions', event_payment_instructions = '$new_event_payment_instructions', event_email_noticesto = '$new_event_email_noticesto', event_contact_name = '$new_event_contact_name', event_contact_email = '$new_event_contact_email', event_allowreg_startdate = '$new_event_allowreg_startdate', event_allowreg_enddate = '$new_event_allowreg_enddate', event_created_on = '$new_event_created_on', event_created_by = '$new_event_created_by', event_page_template = '$new_event_page_template', event_text_thankyou = '$new_event_text_thankyou', event_editable_by = '$new_event_editable_by', event_prompt_table = '$new_event_prompt_table', event_prompt_table_text = '$new_event_prompt_table_text', event_prompt_address_home = '$new_event_prompt_address_home', event_prompt_data_hotel = '$new_event_prompt_data_hotel', event_prompt_data_cc_affiliation = '$new_event_prompt_data_cc_affiliation', event_prompt_data_cc_esc = '$new_event_prompt_data_cc_esc', event_prompt_data_cc_role = '$new_event_prompt_data_cc_role', event_prompt_data_cc_previous = '$new_event_prompt_data_cc_previous', event_prompt_data_cc_focus = '$new_event_prompt_data_cc_focus', event_prompt_data_afterschool = '$new_event_prompt_data_afterschool', event_prompt_inperson_online = '$new_event_prompt_inperson_online', event_cost_free = '$new_event_cost_free', event_cost_table = '$new_event_cost_table', event_comp_code = '$new_event_comp_code', event_preevent = '$new_event_preevent', event_preevent_name = '$new_event_preevent_name', event_preevent_cost = '$new_event_preevent_cost', event_max_registrants='$new_event_max_registrants', event_max_reg_nextform_id='$new_event_max_reg_nextform_id', event_max_registrants_perform='$new_event_max_registrants_perform', event_min_registrants_perform='$new_event_min_registrants_perform', event_confirm_checkbox='$new_event_confirm_checkbox', event_prompt_session_choice = '$new_event_prompt_session_choice', event_session1_title = '$new_event_session1_title', event_session1_desc = '$new_event_session1_desc', event_session2_title = '$new_event_session2_title', event_session2_desc = '$new_event_session2_desc', event_allow_pay_atdoor = '$new_event_allow_pay_atdoor', event_allow_pay_atdoor_text = '$new_event_allow_pay_atdoor_text', event_allow_pay_invoiceme = '$new_event_allow_pay_invoiceme', event_allow_pay_invoiceme_text = '$new_event_allow_pay_invoiceme_text', event_prompt_individual_org = '$new_event_prompt_individual_org', event_extra_text = '$new_event_extra_text', event_budgetcode = '$new_event_budgetcode', event_custom_question1_text = '$new_event_custom_question1_text', event_custom_question1_type = '$new_event_custom_question1_type', event_custom_question1_options = '$new_event_custom_question1_options', event_custom_question2_text = '$new_event_custom_question2_text', event_custom_question2_type = '$new_event_custom_question2_type', event_custom_question2_options = '$new_event_custom_question2_options', event_custom_opentext_question1 = '$new_event_custom_opentext_question1', event_custom_opentext_question2 = '$new_event_custom_opentext_question2', event_custom_opentext_afterschool1 = '$new_event_custom_opentext_afterschool1', event_custom_opentext_afterschool2 = '$new_event_custom_opentext_afterschool2'
			WHERE event_id like '$eventid'";
			$added_edited = "edited";
		}

	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#	my $num_matches = $sth->rows;
	$feedback_message .= "Your event was $added_edited ";

	if ($added_edited eq 'edited') {
		$feedback_message .= "successfully, and the <a href=\"#$eventid\">record you edited</a> is highlighted in yellow below.";
	} else {
		$feedback_message .= " successfully.";
	}
	$location = "show_events";
}
########################################
## END: LOCATION = PROCESS_EDIT_EVENT
########################################


################################
## START: LOCATION = EDIT_EVENT
################################
if ($location eq 'edit_event') {
   
## SET UP HOLDING VARIABLES FOR THE CURRENT EVENT
my $event_id = ""; my $event_name = ""; my $event_name_italics = ""; my $event_name_short = ""; my $event_date_label = ""; my $event_location = ""; my $event_accomodations = ""; my $event_agenda_link = ""; my $event_agenda_link_text = ""; my $event_discount_reg_date = ""; my $event_cost_early_single = ""; my $event_cost_early_multiple = ""; my $event_cost_late_single = ""; my $event_cost_late_multiple = ""; my $event_cost_free = ""; my $event_cost_table = ""; my $event_comp_code = ""; my $event_registration_deadline = ""; my $event_registration_warning = ""; my $event_cancellation_instructions = ""; my $event_payment_instructions = ""; my $event_text_thankyou = ""; my $event_email_noticesto = ""; my $event_contact_name = ""; my $event_contact_email = ""; my $event_allowreg_startdate = ""; my $event_allowreg_enddate = ""; my $event_created_on = ""; my $event_created_by = ""; my $event_editable_by = ""; my $event_page_template = ""; my $event_prompt_table = ""; my $event_prompt_table_text = ""; my $event_prompt_address_home = ""; my $event_prompt_data_hotel = ""; my $event_prompt_data_cc_affiliation = ""; my $event_prompt_data_cc_esc = ""; my $event_prompt_data_cc_role = ""; my $event_prompt_data_cc_previous = ""; my $event_prompt_data_cc_focus = "";  my $event_prompt_data_afterschool = ""; my $event_prompt_inperson_online = ""; my $event_preevent = ""; my $event_preevent_name = ""; my $event_preevent_cost = ""; my $event_max_registrants = ""; my $event_max_reg_nextform_id = ""; my $event_max_registrants_perform = ""; my $event_min_registrants_perform = ""; my $event_confirm_checkbox = ""; my $event_prompt_session_choice = ""; my $event_session1_title = ""; my $event_session1_desc = ""; my $event_session2_title = ""; my $event_session2_desc = ""; my $event_allow_pay_atdoor = ""; my $event_allow_pay_atdoor_text = ""; my $event_allow_pay_invoiceme = ""; my $event_allow_pay_invoiceme_text = ""; my $event_prompt_individual_org = ""; my $event_extra_text = ""; my $event_budgetcode = ""; my $event_custom_question1_text = ""; my $event_custom_question1_type = ""; my $event_custom_question1_options = ""; my $event_custom_question2_text = ""; my $event_custom_question2_type = ""; my $event_custom_question2_options = ""; my $event_custom_opentext_question1 = ""; my $event_custom_opentext_question2 = ""; my $event_custom_opentext_afterschool1 = ""; my $event_custom_opentext_afterschool2 = "";

	my $command = "SELECT * from registration_events WHERE event_id like '$eventid'";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_editevent = $sth->rows;
	my $counter = "1";

		while (my @arr = $sth->fetchrow) {
	   		($event_id, $event_name, $event_name_italics, $event_name_short, $event_date_label, $event_location, $event_accomodations, $event_agenda_link, $event_agenda_link_text, $event_discount_reg_date, $event_cost_early_single, $event_cost_early_multiple, $event_cost_late_single, $event_cost_late_multiple, $event_cost_free, $event_cost_table, $event_comp_code, $event_registration_deadline, $event_registration_warning, $event_cancellation_instructions, $event_payment_instructions, $event_text_thankyou, $event_email_noticesto, $event_contact_name, $event_contact_email, $event_allowreg_startdate, $event_allowreg_enddate, $event_created_on, $event_created_by, $event_editable_by, $event_page_template, $event_prompt_table, $event_prompt_table_text, $event_prompt_address_home, $event_prompt_data_hotel, $event_prompt_data_cc_affiliation, $event_prompt_data_cc_esc, $event_prompt_data_cc_role, $event_prompt_data_cc_previous, $event_prompt_data_cc_focus, $event_prompt_data_afterschool, $event_prompt_inperson_online, $event_preevent, $event_preevent_name, $event_preevent_cost, $event_max_registrants, $event_max_reg_nextform_id, $event_max_registrants_perform, $event_min_registrants_perform, $event_confirm_checkbox, $event_prompt_session_choice, $event_session1_title, $event_session1_desc, $event_session2_title, $event_session2_desc, $event_allow_pay_atdoor, $event_allow_pay_atdoor_text, $event_allow_pay_invoiceme, $event_allow_pay_invoiceme_text, $event_prompt_individual_org, $event_extra_text, $event_budgetcode, $event_custom_question1_text, $event_custom_question1_type, $event_custom_question1_options, $event_custom_question2_text, $event_custom_question2_type, $event_custom_question2_options, $event_custom_opentext_question1, $event_custom_opentext_question2, $event_custom_opentext_afterschool1, $event_custom_opentext_afterschool2) = @arr;
		}
			my $event_registration_deadline_pretty = commoncode::date2standard($event_registration_deadline);
			my $event_discount_reg_date_pretty = commoncode::date2standard($event_discount_reg_date);
			   $event_max_registrants_perform = "10" if ($event_max_registrants_perform eq '0');
			   $event_min_registrants_perform = "1" if ($event_min_registrants_perform eq '0');
			
my $page_title = "New Event";
   $page_title = "Edit Event Settings" if ($num_matches_editevent eq '1');

print<<EOM;
<H2>$page_title</H2>
<P>
Please edit the settings for this event using the form below.
<FONT COLOR=RED>* Required fields are marked with a red asterisk.</FONT>
<P>
<FORM ACTION="registration-admin.cgi" METHOD="POST">
EOM


print<<EOM;
<TABLE BORDER="1" CELLPADDINg="3" CELLSPACING="0" BGCOLOR="#EBEBEB">
<TR><TD COLSPAN=2 BGCOLOR="#FFFFFF"><H3>Event Description, Location, Accommodations, and Link to Agenda</H3></TD></TR>
<TR><TD VALIGN="TOP"><strong>Web Page Template</strong> <FONT COLOR=RED>*</TD>
	<TD VALIGN="TOP"><SELECT NAME="new_event_page_template">
EOM
my $counter_template = "0";
my @templates = ('', 'chps', 'pirc', 'secc', 'sedl', 'txcc');
	while ($counter_template <= $#templates) {
		print "<OPTION VALUE=\"$templates[$counter_template]\"";
		if ($event_page_template eq $templates[$counter_template]) {
			print " SELECTED";
		}
		print ">$templates[$counter_template]";
		$counter_template++;
	} # END WHILE
$event_cost_early_single = "" if ($event_cost_early_single eq '0.00');
$event_cost_early_multiple = "" if ($event_cost_early_multiple eq '0.00');
$event_cost_late_single = "" if ($event_cost_late_single eq '0.00');
$event_cost_late_multiple = "" if ($event_cost_late_multiple eq '0.00');
$event_cost_table = "" if ($event_cost_table eq '0.00');

print<<EOM;
		</select>
	</TD></TR>
<TR><TD VALIGN="TOP" WIDTH="50%"><strong><label for="new_event_name">Event Name (part 1)</label></strong> <FONT COLOR=RED>*</FONT></TD>
	<TD WIDTH="50%"><textarea name="new_event_name" id="new_event_name" rows="7" cols="70">$event_name</textarea></TD></TR>
<TR><TD VALIGN="TOP"><strong><label for="new_event_name_italics">Event Name (part 2)</label></strong><br>
		<em>this part will be italicized on form</em></TD>
	<TD VALIGN="TOP"><textarea name="new_event_name_italics" id="new_event_name_italics" rows="7" cols="70">$event_name_italics</textarea></TD></TR>
<TR><TD VALIGN="TOP"><strong><label for="new_event_name_short">Short name for event</label></strong> <FONT COLOR=RED>*</FONT><br>
		used in sentences on the registration form<br>
		(ex: event, conference, seminar, fall forum, professional development sessions)</TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_name_short" id="new_event_name_short" size="30" VALUE="$event_name_short"></TD></TR>
<TR><TD VALIGN="TOP"><strong><label for="new_event_date_label">Date of the event</label></strong> <FONT COLOR=RED>*</FONT><br>
		(ex format: January 23-24, 2005)</TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_date_label" id="new_event_date_label" size="70" VALUE="$event_date_label"></TD></TR>
<TR><TD VALIGN="TOP"><strong><label for="new_event_location">Event Location</label></strong><br>
		(ex: Austin, TX)</TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_location" id="new_event_location" size="30" VALUE="$event_location"></TD></TR>
<TR><TD VALIGN="TOP"><strong><label for="new_event_accomodations">Event Accommodations</label></strong></TD>
	<TD VALIGN="TOP"><textarea name="new_event_accomodations" id="new_event_accomodations" rows="12" cols="70">$event_accomodations</textarea></TD></TR>
<TR><TD VALIGN="TOP"><strong><label for="new_event_agenda_link">URL for the agenda</label></strong><br>
		or other supporting document for this event</TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_agenda_link" id="new_event_agenda_link" size="50" VALUE="$event_agenda_link"></TD></TR>
<TR><TD VALIGN="TOP"><strong><label for="new_event_agenda_link_text">Text for the link</label></strong> to the above online document</TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_agenda_link_text" id="new_event_agenda_link_text" size="30" VALUE="$event_agenda_link_text"></TD></TR>
<TR><TD VALIGN="TOP"><strong><label for="new_event_registration_deadline_m">Event Registration FINAL Deadline</label></strong> <FONT COLOR=RED>*</FONT></TD>
	<TD VALIGN="TOP">
EOM
## START: SPLIT UP EXISTING DATE VALUE AND SEND TO SUBROUTINES TO PRINT PULL-MENUS FOR DATE
my ($this_year, $this_month, $this_date) = split(/\-/,$event_registration_deadline);
&commoncode::print_month_menu("new_event_registration_deadline_m", $this_month); 
&commoncode::print_day_menu("new_event_registration_deadline_d", $this_date); 
&commoncode::print_year_menu_descending("new_event_registration_deadline_y", 2005, $year, $this_year);
## END: SPLIT UP EXISTING DATE VALUE AND SEND TO SUBROUTINES TO PRINT PULL-MENUS FOR DATE

print<<EOM;
	<FONT COLOR="#EBEBEB">$event_registration_deadline</FONT></TD></TR>
<TR><TD VALIGN="TOP"><strong><label for="new_event_custom_question1_text">Custom Question 1</label></strong></TD>
	<TD VALIGN="TOP"><label for="new_event_custom_question1_type">Question Type:</label> 
EOM
&commoncode::printform_question_type("new_event_custom_question1_type", $event_custom_question1_type); 
print<<EOM;
					<br>Type the text for the question, such as "What school do you teach at?"<br>This question will only appear if you enter text in this box.<br>
					<textarea name="new_event_custom_question1_text" id="new_event_custom_question1_text" rows="4" cols="70">$event_custom_question1_text</textarea>
					<br><br>
					<strong><label for="new_event_custom_question1_options">Custom Question 1 Options</label></strong><br>
					These options will appear below the question text as a list of radio buttons (select one) or checkboxes (all that apply).<br><br>
					<em>Enter the options a user should choose from using a semicolon separated list, like:<br>
					"School A;School B;School C"</em><br>
					<textarea name="new_event_custom_question1_options" id="new_event_custom_question1_options" rows="4" cols="70">$event_custom_question1_options</textarea>
					
					</TD></TR>
<TR><TD VALIGN="TOP"><strong><label for="new_event_custom_question2_text">Custom Question 2</label></strong></TD>
	<TD VALIGN="TOP"><label for="new_event_custom_question2_type">Question Type:</label> 
EOM
&commoncode::printform_question_type("new_event_custom_question2_type", $event_custom_question2_type); 
print<<EOM;
					<br>
					Type the text for the question, such as "What school do you teach at?"<br>This question will only appear if you enter text in this box.<br>
					<textarea name="new_event_custom_question2_text" id="new_event_custom_question2_text" rows="4" cols="70">$event_custom_question2_text</textarea>
					<br><br>
					<strong><label for="new_event_custom_question2_options">Custom Question 2 Options</label></strong><br>
					These options will appear below the question text as a list of radio buttons (select one) or checkboxes (all that apply).<br><br>
					<em>Enter the options a user should choose from using a semicolon separated list, like:<br>
					"School A;School B;School C"</em><br>
					<textarea name="new_event_custom_question2_options" id="new_event_custom_question2_options" rows="4" cols="70">$event_custom_question2_options</textarea>
		</TD></TR>

<TR><TD VALIGN="TOP"><strong><label for="new_event_custom_opentext_question1">Custom Open-Text Question 1</label></strong></TD>
	<TD VALIGN="TOP"><textarea name="new_event_custom_opentext_question1" id="new_event_custom_opentext_question1" rows="4" cols="70">$event_custom_opentext_question1</textarea><br>
		<em>This open-text question will only appear on the form if you enter text for the question here.</em></TD></TR>
<TR><TD VALIGN="TOP"><strong><label for="new_event_custom_opentext_question2">Custom Open-Text Question 2</label></strong></TD>
	<TD VALIGN="TOP"><textarea name="new_event_custom_opentext_question2" id="new_event_custom_opentext_question2" rows="4" cols="70">$event_custom_opentext_question2</textarea><br>
		<em>This open-text question will only appear on the form if you enter text for the question here.</em></TD></TR>


<TR><TD COLSPAN=2 BGCOLOR="#FFFFFF"><br><H3>Costs, Early-bird Price Breaks, and Early-bird Deadlines (if any)</H3>
	<em>Note: If you are allowing participants to pay using their VISA or MasterCard credit card, 
		please let <A HREF="mailto:eva.mueller\@sedl.org?subject=SEDL will be allowing online credit card payment for an upcoming event&body=Dear Eva,">Eva Mueller</A> in Communications knows that she should be expecting
		online and phone payments by credit card for your event.</em></TD></TR>
<TR><TD VALIGN="TOP"><strong>Event Cost = Free</strong> <FONT COLOR=RED>*</FONT></TD>
	<TD VALIGN="TOP">
EOM
	&commoncode::printform_yesno_radio("new_event_cost_free", $event_cost_free);
print<<EOM;
	</TD></TR>
<TR><TD VALIGN="TOP"><strong>Event Budget Code</strong></TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_budgetcode" size="15" VALUE="$event_budgetcode"><br>
		REQUIRED FOR "PAY" EVENTS. This is the budget code that will get credited for this registration.
		</TD></TR>
<TR><TD VALIGN="TOP"><strong>Event Comp Code</strong></TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_comp_code" size="15" VALUE="$event_comp_code"><br>
		Used only when SEDL is allowing specified users to prove they are allowed comp entry to the event by entering a code. 
		Enter the comp code to be used in the text box, otherwise leave it blank to not prompt users for a comp code.
		</TD></TR>
<TR><TD VALIGN="TOP"><strong>Event Cost Early Single Registrant</strong> <FONT COLOR=RED>*</FONT><br>
		This is the default cost if there are no other discounts.</TD>
	<TD VALIGN="TOP">\$<input type="text" name="new_event_cost_early_single" size="10" VALUE="$event_cost_early_single"></TD></TR>
<TR><TD VALIGN="TOP"><strong>Event Cost Early Multiple Registrants</strong></TD>
	<TD VALIGN="TOP">\$<input type="text" name="new_event_cost_early_multiple" size="10" VALUE="$event_cost_early_multiple"></TD></TR>
<TR><TD VALIGN="TOP"><strong>Event Discount Registration Date</strong><br>
		Date after which price goes up.</TD>
	<TD VALIGN="TOP">
EOM
## START: SPLIT UP EXISTING DATE VALUE AND SEND TO SUBROUTINES TO PRINT PULL-MENUS FOR DATE
my ($this_year, $this_month, $this_date) = split(/\-/,$event_discount_reg_date);
&commoncode::print_month_menu("new_event_discount_reg_date_m", $this_month); 
&commoncode::print_day_menu("new_event_discount_reg_date_d", $this_date); 
&commoncode::print_year_menu_descending("new_event_discount_reg_date_y", 2005, $year, $this_year);
## END: SPLIT UP EXISTING DATE VALUE AND SEND TO SUBROUTINES TO PRINT PULL-MENUS FOR DATE

print<<EOM;
	<FONT COLOR="#EBEBEB">$event_discount_reg_date</FONT></TD></TR>
<TR><TD VALIGN="TOP"><strong>Event Cost Late Single Registrant</strong></TD>
	<TD VALIGN="TOP">\$<input type="text" name="new_event_cost_late_single" size="10" VALUE="$event_cost_late_single"></TD></TR>
<TR><TD VALIGN="TOP"><strong>Event Cost Late Multiple Registrants</strong></TD>
	<TD VALIGN="TOP">\$<input type="text" name="new_event_cost_late_multiple" size="10" VALUE="$event_cost_late_multiple"></TD></TR>
<TR><TD VALIGN="TOP"><strong>Event Allow Table Reservation</strong><br> If you allow for a table reservation, please include a cost.</TD>
	<TD VALIGN="TOP">
EOM
	&commoncode::printform_yesno_radio("new_event_prompt_table", $event_prompt_table);
print<<EOM;
	<P>
	<em>Event Cost for Table</em><br>
	\$<input type="text" name="new_event_cost_table" size="10" VALUE="$event_cost_table">
	<P>
	<em>Text that accompanies table cost</em><br>
	<textarea name="new_event_prompt_table_text" rows="12" cols="70">$event_prompt_table_text</textarea>
	
	</TD></TR>
EOM
#<TR><TD VALIGN="TOP"><strong>Allow payment type "Invoice me"?</strong><br> This is normally not allowed 
#		unless you make special arrangements with OFTS.</TD>
#	<TD VALIGN="TOP">
#	&commoncode::printform_yesno_radio("new_event_allow_pay_invoiceme", $event_allow_pay_invoiceme);
#print<<EOM;
#	<P>
#	<em>Text that accompanies the "invoice me" payment option</em><br>
#	<textarea name="new_event_allow_pay_invoiceme_text" rows=3 cols=50>$event_allow_pay_invoiceme_text</textarea>
#	
#	</TD></TR>
print<<EOM;
<TR><TD VALIGN="TOP"><strong>Allow payment type "Pay at the door"?</strong></TD>
	<TD VALIGN="TOP">
EOM
	&commoncode::printform_yesno_radio("new_event_allow_pay_atdoor", $event_allow_pay_atdoor);
print<<EOM;
	<P>
	<em>Text that accompanies the "pay at the door" payment option</em><br>
	<textarea name="new_event_allow_pay_atdoor_text" rows="12" cols="70">$event_allow_pay_atdoor_text</textarea>
	
	</TD></TR>

<TR><TD COLSPAN=2 BGCOLOR="#FFFFFF"><br><H3>Extra Text for the Form</H3></TD></TR>
<TR><TD VALIGN="TOP"><strong>Text warning about registration</strong><br>
		(ex: <FONT COLOR=RED>Space is limited to ensure a quality experience for our participants. Reserve your place now.</FONT>)</TD>
	<TD VALIGN="TOP"><textarea name="new_event_registration_warning" rows="12" cols="70">$event_registration_warning</textarea></TD></TR>
<TR><TD VALIGN="TOP"><strong>Extra Text for top page of form</strong><br>Text will appear just before the heading "Step 1 of 3"</TD>
	<TD VALIGN="TOP"><textarea name="new_event_extra_text" rows="12" cols="70">$event_extra_text</textarea></TD></TR>
<TR><TD VALIGN="TOP"><strong>Cancellation Instructions</strong></TD>
	<TD VALIGN="TOP"><textarea name="new_event_cancellation_instructions" rows="12" cols="70">$event_cancellation_instructions</textarea></TD></TR>
<TR><TD VALIGN="TOP"><strong>Event payment Instructions</strong> <FONT COLOR=RED>*</FONT><br>
		(ex: Eva Mueller at SEDL, 4700 Mueller Blvd., Austin, TX 78723 Tel: 800-476-6861 Email: services\@sedl.org Fax: 512-476-2286. Please include a copy of this registration form if you are mailing or faxing your purchase order or payment.)</TD>
	<TD VALIGN="TOP"><textarea name="new_event_payment_instructions" rows="12" cols="70">$event_payment_instructions</textarea></TD></TR>
<TR><TD VALIGN="TOP"><strong>Extra text for "Thank You" page</strong></TD>
	<TD VALIGN="TOP"><textarea name="new_event_text_thankyou" rows="12" cols="70">$event_text_thankyou</textarea></TD></TR>

<TR><TD COLSPAN=2 BGCOLOR="#FFFFFF"><br><H3>Extra Fields You Can Add to the Form</H3></TD></TR>
<TR><TD VALIGN="TOP"><strong>Prompt user for participation type (In person vs. Online)</strong></TD>
	<TD VALIGN="TOP">
EOM
	&commoncode::printform_yesno_radio("new_event_prompt_inperson_online", $event_prompt_inperson_online);
print<<EOM;
	</TD></TR>
<TR><TD VALIGN="TOP"><strong>Prompt user for home address</strong></TD>
	<TD VALIGN="TOP">
EOM
	&commoncode::printform_yesno_radio("new_event_prompt_address_home", $event_prompt_address_home);
print<<EOM;
	</TD></TR>
<TR><TD VALIGN="TOP"><strong>Prompt user for hotel data?</strong><br>
		<em>Includes: arrival, departure, bed pref., smoking pref.</em></TD>
	<TD VALIGN="TOP">
EOM
	&commoncode::printform_yesno_radio("new_event_prompt_data_hotel", $event_prompt_data_hotel);
print<<EOM;
	</TD></TR>
<TR><TD VALIGN="TOP"><strong>Prompt user for CC Role data</strong></TD>
	<TD VALIGN="TOP">
EOM
	&commoncode::printform_yesno_radio("new_event_prompt_data_cc_role", $event_prompt_data_cc_role);
print<<EOM;
	</TD></TR>
<TR><TD VALIGN="TOP"><strong>Prompt user for CC "attended previous event"</strong></TD>
	<TD VALIGN="TOP">If you want this checkbox, simply enter the text that you want displayed to the right of the checkbox.  
					Such as "2006 Forum Attendee?"<br>
					<INPUT type="text" NAME="new_event_prompt_data_cc_previous" VALUE="$event_prompt_data_cc_previous" SIZE="30"><br>
					Leave it blank if you do not wish to show this confirmation box.
	</TD></TR>
<TR><TD VALIGN="TOP"><strong>Prompt user for CC Job Affiliation data</strong></TD>
	<TD VALIGN="TOP">
EOM
	&commoncode::printform_yesno_radio("new_event_prompt_data_cc_affiliation", $event_prompt_data_cc_affiliation);
print<<EOM;
	</TD></TR>
<TR><TD VALIGN="TOP"><strong>Prompt user for CC Focus Area data</strong></TD>
	<TD VALIGN="TOP">
EOM
	&commoncode::printform_yesno_radio("new_event_prompt_data_cc_focus", $event_prompt_data_cc_focus);
print<<EOM;
	</TD></TR>
<TR><TD VALIGN="TOP"><strong>Prompt user for CC TX ESC Region data</strong></TD>
	<TD VALIGN="TOP">
EOM
	&commoncode::printform_yesno_radio("new_event_prompt_data_cc_esc", $event_prompt_data_cc_esc);
print<<EOM;
	</TD></TR>
<TR><TD VALIGN="TOP"><strong>Prompt user for Afterschool participant data</strong><br>Questions will appear to ask: program name, students served, program administered, years operating, grade level, locale.</TD>
	<TD VALIGN="TOP"><br>
EOM
	&commoncode::printform_yesno_radio("new_event_prompt_data_afterschool", $event_prompt_data_afterschool);
print<<EOM;
		<br><br>
		<table border="1" Cellpadding="4" cellspacing="0">
		<TR><TD VALIGN="TOP"><strong><label for="new_event_custom_opentext_afterschool1">Custom Open-Text Afterschool Auestion 1</label></strong></TD>
			<TD VALIGN="TOP"><textarea name="new_event_custom_opentext_afterschool1" id="new_event_custom_opentext_afterschool1" rows="4" cols="70">$event_custom_opentext_afterschool1</textarea><br>
				<em>This open-text question will only appear on the form if you enter text for the question here.</em></TD></TR>
		<TR><TD VALIGN="TOP"><strong><label for="new_event_custom_opentext_afterschool2">Custom Open-Text Afterschool Question 2</label></strong></TD>
			<TD VALIGN="TOP"><textarea name="new_event_custom_opentext_afterschool2" id="new_event_custom_opentext_afterschool2" rows="4" cols="70">$event_custom_opentext_afterschool2</textarea><br>
				<em>This open-text question will only appear on the form if you enter text for the question here.</em></TD></TR>
		</table>


	</TD></TR>

<TR><TD VALIGN="TOP"><strong>Prompt user to select from one of two sessions.</TD>
	<TD VALIGN="TOP"><br>
EOM
	&commoncode::printform_yesno_radio("new_event_prompt_session_choice", $event_prompt_session_choice);
print<<EOM;
	<P>
	If there is a session choice, please enter:<br>
	<TABLE>
	<TR><TD VALIGN="TOP">Session 1 short name:<br>
		<INPUT type="text" NAME="new_event_session1_title" VALUE="$event_session1_title" SIZE="30"></TD></TR>
	<TR><TD VALIGN="TOP">Session 1 description<br>
		<textarea name="new_event_session1_desc" rows="16" cols="70">$event_session1_desc</textarea></TD></TR>
	<TR><TD VALIGN="TOP">Session 2 short name:<br>
		<INPUT type="text" NAME="new_event_session2_title" VALUE="$event_session2_title" SIZE="30"></TD></TR>
	<TR><TD VALIGN="TOP">Session 2 description<br>
		<textarea name="new_event_session2_desc" rows="16" cols="70">$event_session2_desc</textarea></TD></TR>
	</TABLE>
	</TD></TR>

<TR BGCOLOR="#999999"><TD VALIGN="TOP"><strong>Is there a pre-event that the user can register for?</strong></TD>
	<TD VALIGN="TOP">(Placeholder - this variable is currently unused)<br>
EOM
	&commoncode::printform_yesno_radio("new_event_preevent", $event_preevent);
print<<EOM;
	<P>
	If there is a pre-event, please enter:<br>
	<TABLE>
	<TR><TD VALIGN="TOP">pre-event name:</TD><TD><INPUT type="text" NAME="new_event_preevent_name" VALUE="$event_preevent_name" SIZE="30"></TD></TR>
	<TR><TD VALIGN="TOP">pre-event cost: \$</TD><TD><INPUT type="text" NAME="new_event_preevent_cost" VALUE="$event_preevent_cost" SIZE="8"><br>
		<em>(leave blank if there is no cost.)</em></TD></TR>
	</TABLE>
	</TD></TR>
<TR><TD COLSPAN=2 BGCOLOR="#FFFFFF"><br><H3>SEDL Contact Information</H3></TD></TR>
<TR><TD VALIGN="TOP"><strong>Contact Name</strong> <FONT COLOR=RED>*</FONT><br>
		Confirmation emails are sent <strong>from</strong> this name.</TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_contact_name" size="50" VALUE="$event_contact_name"></TD></TR>
<TR><TD VALIGN="TOP"><strong>Contact Email</strong> <FONT COLOR=RED>*</FONT><br>
		Confirmation emails are sent <strong>from</strong> this address.</TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_contact_email" size="50" VALUE="$event_contact_email"></TD></TR>
<TR><TD VALIGN="TOP"><strong>Staff emails to receive email registration notices</strong> <FONT COLOR=RED>*</FONT><br>
		(if more than one, separate with a comma and space)
		<P>
		<FONT COLOR=RED>Note: You should add Eva Mueller (eva.mueller\@sedl.org) if this event will include
		payments by credit card, so Eva can monitor registration payments as necessary.</FONT></TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_email_noticesto" size="50" VALUE="$event_email_noticesto"></TD></TR>
<TR><TD VALIGN="TOP"><strong>Date to Start Allowing Form to Display</strong> <FONT COLOR=RED>*</FONT></TD>
	<TD VALIGN="TOP">
EOM
	## START: SPLIT UP EXISTING DATE VALUE AND SEND TO SUBROUTINES TO PRINT PULL-MENUS FOR DATE
	my ($this_year, $this_month, $this_date) = split(/\-/,$event_allowreg_startdate);
	&commoncode::print_month_menu("new_event_allowreg_startdate_m", $this_month); 
	&commoncode::print_day_menu("new_event_allowreg_startdate_d", $this_date); 
	&commoncode::print_year_menu_descending("new_event_allowreg_startdate_y", 2005, $year, $this_year);
	## END: SPLIT UP EXISTING DATE VALUE AND SEND TO SUBROUTINES TO PRINT PULL-MENUS FOR DATE

print<<EOM;
	$event_allowreg_startdate</TD></TR>
<TR><TD VALIGN="TOP"><strong>Date to Stop Allowing Form to Display</strong> <FONT COLOR=RED>*</FONT></TD>
	<TD VALIGN="TOP">
EOM
	## START: SPLIT UP EXISTING DATE VALUE AND SEND TO SUBROUTINES TO PRINT PULL-MENUS FOR DATE
	my ($this_year, $this_month, $this_date) = split(/\-/,$event_allowreg_enddate);
	&commoncode::print_month_menu("new_event_allowreg_enddate_m", $this_month); 
	&commoncode::print_day_menu("new_event_allowreg_enddate_d", 
	$this_date); &commoncode::print_year_menu_descending("new_event_allowreg_enddate_y", 2005, $year, $this_year);
	## END: SPLIT UP EXISTING DATE VALUE AND SEND TO SUBROUTINES TO PRINT PULL-MENUS FOR DATE

print<<EOM;
	$event_allowreg_enddate</TD></TR>
<TR><TD VALIGN="TOP"><strong>Maximum number of registrants</strong><br>
		(After number exceed close form or redirect to second form for later event date)</TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_max_registrants" size="4" VALUE="$event_max_registrants"><P>
		If there is another date this training will be held, you may enter the event ID here to prompt the 
		user to sign up for this later form when the current event reaches capacity.<br>
		Event ID#:<input type="text" name="new_event_max_reg_nextform_id" size="4" VALUE="$event_max_reg_nextform_id"></TD></TR>
<TR><TD VALIGN="TOP"><strong>Minimum number of registrants per submission</strong><br>
		Require that registrations submissions include at least this many people (default is 1).</TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_min_registrants_perform" size="4" VALUE="$event_min_registrants_perform"></TD></TR>
<TR><TD VALIGN="TOP"><strong>Maximum number of registrants per submission</strong><br>
		Limit individual registrations submissions to this many people (default is 10).</TD>
	<TD VALIGN="TOP"><input type="text" name="new_event_max_registrants_perform" size="4" VALUE="$event_max_registrants_perform"></TD></TR>
<TR><TD VALIGN="TOP"><strong>Confirmation Checkbox</strong><br>If you have text here, a checkbox will appear before the final registration 
		submission asking the user to check the box to confirm they agree with the statement you entered. Leave blank to 
		omit this confirmation checkbox.</TD>
	<TD VALIGN="TOP"><textarea name="new_event_confirm_checkbox" rows="12" cols="70">$event_confirm_checkbox</textarea></TD></TR>
EOM

	if ($eventid ne '') { 
print<<EOM;
<TR><TD VALIGN="TOP"><strong>Created Date</strong></TD>
	<TD VALIGN="TOP"><INPUT TYPE="HIDDEN" NAME="new_event_created_on" VALUE="$event_created_on">$event_created_on</TD></TR>
<TR><TD VALIGN="TOP"><strong>Created by SEDL Staff Member</strong></TD>
	<TD VALIGN="TOP"><INPUT TYPE="HIDDEN" NAME="new_event_created_by" VALUE="$event_created_by">$event_created_by
EOM
	if (($cookie_ss_staff_id eq $event_created_by) || ($event_editable_by =~ $cookie_ss_staff_id)) {
	} else {
		print "<P><FONT COLOR=\"RED\">WARNING: You did not create this event record.  This event can only be edited by its creator ($event_created_by).  You ($cookie_ss_staff_id) cannot edit it without permission.</FONT>";
	}

	}
print<<EOM;
</TD></TR>
<TR><TD VALIGN="TOP"><strong>This event can be edited by these other SEDL staff:</strong><br>
		(ex: blitke, creese, pramirez)</strong></TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_event_editable_by" VALUE="$event_editable_by" SIZE="50"></TD></TR>
</TABLE>
<P>
  <input type="HIDDEN" name="show_year" value="$show_year">
  <INPUT TYPE="HIDDEN" NAME="eventid" VALUE="$eventid">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_edit_event">
  <INPUT TYPE="SUBMIT" VALUE="Save Settings for this Event">
  </FORM>
EOM

## $event_cost_free, $event_text_thankyou
## $event_prompt_address_home, $event_prompt_data_cc, $event_prompt_data_afterschool, $event_prompt_inperson_online, $event_preevent, $event_preevent_name, $event_preevent_cost

			## PRINT ORG LINE
}
################################
## END: LOCATION = EDIT_EVENT
################################


#########################################
## START: LOCATION = HOW_TO_LINK_TO_FORM
#########################################
if ($location eq 'how_to_link_to_form') {
	if ($eventid ne '') {
print<<EOM;
<H2>Directions for linking to this online registration form<br>($this_event_name)</H2>
	<UL>
	<LI><strong>Option 1:</strong> You can add this registration form to the Web site by linking directly to the form at this address: 
		<A HREF="http://www.sedl.org/cgi-bin/mysql/registration/registration.cgi?eventid=$eventid">http://www.sedl.org/cgi-bin/mysql/registration/registration.cgi?eventid=$eventid</A></LI>
<P>
		Disadvantage: This address is in the "cgi-bin" and may be overly long and hard-to-type for your clients.  See option 2 for a better alternative.
<P>
	<LI><strong>Option 2:</strong> You can have the registration form appear at an easy-to-type HTML page.
		<P>
		<UL>
		<LI><strong>For techies:</strong><br>
		Make an HTML page on the site wherever you want, and put ONLY the following code and nothing else in the HTML file:<br>
		<FONT COLOR=BLUE>&lt;!--#include virtual="/cgi-bin/mysql/registration/registration.cgi?eventid=$eventid"--&gt;</FONT></LI>
		<P>After you create the HTML page, it will display the content from the CGI registration page listed above, 
		and the HTML Web address will be more memorable for you and your participants, especially if they have to type in the URI by hand.</LI>
<P>

		<LI><strong>For non-techies: Send a Request to Brian to set up the HTML page</strong>
<P>
		If you do not have privileges to create an HTML 
		page on your Web site, use this form to send a request to Brian to create the HTML page for you.

		<FORM ACTION="registration-admin.cgi" METHOD="POST">
		Please enter the Web address that you would like to use for the registration form:<br>
		(ex: http://www.sedl.org/afterschool/registration2005.html)
		<P>
		<input type="text" name="new_html_webpage" size="50" VALUE="http://www.sedl.org/">
		<P>
  		<input type="HIDDEN" name="show_year" value="$show_year">
		<INPUT TYPE="HIDDEN" NAME="eventid" VALUE="$eventid">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_request_html_page">
		<INPUT TYPE="SUBMIT" VALUE="Send request to Brian">
		</FORM></LI>
	</UL>
EOM
	} # END IF
}
#########################################
## END: LOCATION = HOW_TO_LINK_TO_FORM
#########################################

##########################################
## START: LOCATION = COPY_EVENT
##########################################
## START: CHECK IF USER IS AUTHORIZED
if ($location eq 'copy_event') {
	my $user_authorized = "";
	my $new_location = "";
	($user_authorized, $error_message) = &check_user_authorized($eventid, $cookie_ss_staff_id);
	if ($user_authorized ne 'yes') {
		$location = "show_events";
	}
}
## END: CHECK IF USER IS AUTHORIZED



if ($location eq 'copy_event') {
	if ($confirm_action ne 'yes') {
		$error_message = "The copy <strong>WAS NOT</strong> processed.  You forgot to click the confirm copy checkbox.  Please try again.";
		$location = "show_events";
	} elsif ($eventid eq '') {
		$error_message = "The copy <strong>WAS NOT</strong> processed.  The event ID to copy was not passed properly.  Please notify Brian Litke at ext. 6529 of this error.";
		$location = "show_events";
	} else {
		## SHOW FORM TO GATHER ADDITIONAL NEW DATE INFORMATION
			## SET UP HOLDING VARIABLES FOR THE CURRENT EVENT
			my $event_id = ""; my $event_name = ""; my $event_name_italics = ""; my $event_name_short = ""; my $event_date_label = ""; my $event_location = ""; my $event_accomodations = ""; my $event_agenda_link = ""; my $event_agenda_link_text = ""; my $event_discount_reg_date = ""; my $event_cost_early_single = ""; my $event_cost_early_multiple = ""; my $event_cost_late_single = ""; my $event_cost_late_multiple = ""; my $event_cost_free = ""; my $event_cost_table = ""; my $event_comp_code = ""; my $event_registration_deadline = ""; my $event_registration_warning = ""; my $event_cancellation_instructions = ""; my $event_payment_instructions = ""; my $event_text_thankyou = ""; my $event_email_noticesto = ""; my $event_contact_name = ""; my $event_contact_email = ""; my $event_allowreg_startdate = ""; my $event_allowreg_enddate = ""; my $event_created_on = ""; my $event_created_by = ""; my $event_editable_by = ""; my $event_page_template = ""; my $event_prompt_table = ""; my $event_prompt_table_text = ""; my $event_prompt_address_home = ""; my $event_prompt_data_hotel = ""; my $event_prompt_data_cc_affiliation = ""; my $event_prompt_data_cc_esc = ""; my $event_prompt_data_cc_role = ""; my $event_prompt_data_cc_previous = ""; my $event_prompt_data_cc_focus = "";  my $event_prompt_data_afterschool = ""; my $event_prompt_inperson_online, my $event_preevent = ""; my $event_preevent_name = ""; my $event_preevent_cost = ""; my $event_max_registrants = ""; my $event_max_reg_nextform_id = ""; my $event_max_registrants_perform = ""; my $event_min_registrants_perform = ""; my $event_confirm_checkbox = ""; my $event_prompt_session_choice = ""; my $event_session1_title = ""; my $event_session1_desc = ""; my $event_session2_title = ""; my $event_session2_desc = ""; my $event_allow_pay_atdoor = ""; my $event_allow_pay_atdoor_text = ""; my $event_allow_pay_invoiceme = ""; my $event_allow_pay_invoiceme_text = ""; my $event_prompt_individual_org = ""; my $event_extra_text = ""; my $event_budgetcode = ""; my $event_custom_question1_text = ""; my $event_custom_question1_type = ""; my $event_custom_question1_options = ""; my $event_custom_question2_text = ""; my $event_custom_question2_type = ""; my $event_custom_question2_options = ""; my $event_custom_opentext_question1 = ""; my $event_custom_opentext_question2 = ""; my $event_custom_opentext_afterschool1 = ""; my $event_custom_opentext_afterschool2 = "";

			## SELECT EVENT DETAILS

			my $command = "SELECT * from registration_events WHERE event_id = '$eventid'";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
			my $num_matches_editevent = $sth->rows;
			my $counter = "1";

				while (my @arr = $sth->fetchrow) {
	   				($event_id, $event_name, $event_name_italics, $event_name_short, $event_date_label, $event_location, $event_accomodations, $event_agenda_link, $event_agenda_link_text, $event_discount_reg_date, $event_cost_early_single, $event_cost_early_multiple, $event_cost_late_single, $event_cost_late_multiple, $event_cost_free, $event_cost_table, $event_comp_code, $event_registration_deadline, $event_registration_warning, $event_cancellation_instructions, $event_payment_instructions, $event_text_thankyou, $event_email_noticesto, $event_contact_name, $event_contact_email, $event_allowreg_startdate, $event_allowreg_enddate, $event_created_on, $event_created_by, $event_editable_by, $event_page_template, $event_prompt_table, $event_prompt_table_text, $event_prompt_address_home, $event_prompt_data_hotel, $event_prompt_data_cc_affiliation, $event_prompt_data_cc_esc, $event_prompt_data_cc_role, $event_prompt_data_cc_previous, $event_prompt_data_cc_focus, $event_prompt_data_afterschool, $event_prompt_inperson_online, $event_preevent, $event_preevent_name, $event_preevent_cost, $event_max_registrants, $event_max_reg_nextform_id, $event_max_registrants_perform, $event_min_registrants_perform, $event_confirm_checkbox, $event_prompt_session_choice, $event_session1_title, $event_session1_desc, $event_session2_title, $event_session2_desc, $event_allow_pay_atdoor, $event_allow_pay_atdoor_text, $event_allow_pay_invoiceme, $event_allow_pay_invoiceme_text, $event_prompt_individual_org, $event_extra_text, $event_budgetcode, $event_custom_question1_text, $event_custom_question1_type, $event_custom_question1_options, $event_custom_question2_text, $event_custom_question2_type, $event_custom_question2_options, $event_custom_opentext_question1, $event_custom_opentext_question2, $event_custom_opentext_afterschool1, $event_custom_opentext_afterschool2) = @arr;
				} # END DB QUERY LOOP

			## START: CLEAN VARIABLES BEFORE DB INSERTION
			$event_name = &commoncode::cleanthisfordb($event_name);
			$event_name_italics = &commoncode::cleanthisfordb($event_name_italics);
			$event_name_short = &commoncode::cleanthisfordb($event_name_short);
			$event_date_label = &commoncode::cleanthisfordb($event_date_label);
			$event_location = &commoncode::cleanthisfordb($event_location);
			$event_accomodations = &commoncode::cleanthisfordb($event_accomodations);
			$event_agenda_link = &commoncode::cleanthisfordb($event_agenda_link);
			$event_agenda_link_text = &commoncode::cleanthisfordb($event_agenda_link_text);
			$event_discount_reg_date = &commoncode::cleanthisfordb($event_discount_reg_date);
			$event_cost_early_single = &commoncode::cleanthisfordb($event_cost_early_single);
			$event_cost_early_multiple = &commoncode::cleanthisfordb($event_cost_early_multiple);
			$event_cost_late_single = &commoncode::cleanthisfordb($event_cost_late_single);
			$event_cost_late_multiple = &commoncode::cleanthisfordb($event_cost_late_multiple);
			$event_cost_free = &commoncode::cleanthisfordb($event_cost_free);
			$event_cost_table = &commoncode::cleanthisfordb($event_cost_table);
			$event_comp_code = &commoncode::cleanthisfordb($event_comp_code);
			$event_registration_deadline = &commoncode::cleanthisfordb($event_registration_deadline);
			$event_registration_warning = &commoncode::cleanthisfordb($event_registration_warning);
			$event_cancellation_instructions = &commoncode::cleanthisfordb($event_cancellation_instructions);
			$event_payment_instructions = &commoncode::cleanthisfordb($event_payment_instructions);
			$event_text_thankyou = &commoncode::cleanthisfordb($event_text_thankyou);
			$event_email_noticesto = &commoncode::cleanthisfordb($event_email_noticesto);
			$event_contact_name = &commoncode::cleanthisfordb($event_contact_name);
			$event_contact_email = &commoncode::cleanthisfordb($event_contact_email);
			$event_allowreg_startdate = &commoncode::cleanthisfordb($event_allowreg_startdate);
			$event_allowreg_enddate = &commoncode::cleanthisfordb($event_allowreg_enddate);
			$event_created_on = &commoncode::cleanthisfordb($event_created_on);
			$event_created_by = &commoncode::cleanthisfordb($event_created_by);
			$event_editable_by = &commoncode::cleanthisfordb($event_editable_by);
			$event_page_template = &commoncode::cleanthisfordb($event_page_template);
			$event_prompt_table = &commoncode::cleanthisfordb($event_prompt_table);
			$event_prompt_table_text = &commoncode::cleanthisfordb($event_prompt_table_text);
			$event_prompt_address_home = &commoncode::cleanthisfordb($event_prompt_address_home);
			$event_prompt_data_hotel = &commoncode::cleanthisfordb($event_prompt_data_hotel);
			$event_prompt_data_cc_affiliation = &commoncode::cleanthisfordb($event_prompt_data_cc_affiliation);
			$event_prompt_data_cc_esc = &commoncode::cleanthisfordb($event_prompt_data_cc_esc);
			$event_prompt_data_cc_role = &commoncode::cleanthisfordb($event_prompt_data_cc_role);
			$event_prompt_data_cc_previous = &commoncode::cleanthisfordb($event_prompt_data_cc_previous);
			$event_prompt_data_cc_focus = &commoncode::cleanthisfordb($event_prompt_data_cc_focus);
			$event_prompt_data_afterschool = &commoncode::cleanthisfordb($event_prompt_data_afterschool);
			$event_prompt_inperson_online = &commoncode::cleanthisfordb($event_prompt_inperson_online);
			$event_preevent = &commoncode::cleanthisfordb($event_preevent);
			$event_preevent_name = &commoncode::cleanthisfordb($event_preevent_name);
			$event_preevent_cost = &commoncode::cleanthisfordb($event_preevent_cost);
			$event_max_registrants = &commoncode::cleanthisfordb($event_max_registrants);
			$event_max_reg_nextform_id = &commoncode::cleanthisfordb($event_max_reg_nextform_id);
			$event_max_registrants_perform = &commoncode::cleanthisfordb($event_max_registrants_perform);
			$event_min_registrants_perform = &commoncode::cleanthisfordb($event_min_registrants_perform);
			$event_confirm_checkbox = &commoncode::cleanthisfordb($event_confirm_checkbox);
			$event_prompt_session_choice = &commoncode::cleanthisfordb($event_prompt_session_choice);
			$event_session1_title = &commoncode::cleanthisfordb($event_session1_title);
			$event_session1_desc = &commoncode::cleanthisfordb($event_session1_desc);
			$event_session2_title = &commoncode::cleanthisfordb($event_session2_title);
			$event_session2_desc = &commoncode::cleanthisfordb($event_session2_desc);
			$event_allow_pay_atdoor = &commoncode::cleanthisfordb($event_allow_pay_atdoor);
			$event_allow_pay_atdoor_text = &commoncode::cleanthisfordb($event_allow_pay_atdoor_text);
			$event_allow_pay_invoiceme = &commoncode::cleanthisfordb($event_allow_pay_invoiceme);
			$event_allow_pay_invoiceme_text = &commoncode::cleanthisfordb($event_allow_pay_invoiceme_text);
			$event_prompt_individual_org = &commoncode::cleanthisfordb($event_allow_pay_invoiceme_text);
			$event_extra_text = &commoncode::cleanthisfordb($event_extra_text);
			$event_budgetcode = &commoncode::cleanthisfordb($event_budgetcode);
			$event_custom_question1_text = &commoncode::cleanthisfordb($event_custom_question1_text);
			$event_custom_question1_type = &commoncode::cleanthisfordb($event_custom_question1_type);
			$event_custom_question1_options = &commoncode::cleanthisfordb($event_custom_question1_options);
			$event_custom_question2_text = &commoncode::cleanthisfordb($event_custom_question2_text);
			$event_custom_question2_type = &commoncode::cleanthisfordb($event_custom_question2_type);
			$event_custom_question2_options = &commoncode::cleanthisfordb($event_custom_question2_options);
			$event_custom_opentext_question1 = &commoncode::cleanthisfordb($event_custom_opentext_question1);
			$event_custom_opentext_question2 = &commoncode::cleanthisfordb($event_custom_opentext_question2);
			$event_custom_opentext_afterschool1 = &commoncode::cleanthisfordb($event_custom_opentext_afterschool1);
			$event_custom_opentext_afterschool2 = &commoncode::cleanthisfordb($event_custom_opentext_afterschool2);
			## END: CLEAN VARIABLES BEFORE DB INSERTION

			my $command = "INSERT INTO registration_events VALUES(
			'', '$event_name', '$event_name_italics', '$event_name_short', '$event_date_label', 
			'$event_location', '$event_accomodations', '$event_agenda_link', '$event_agenda_link_text', '$event_discount_reg_date', 
			'$event_cost_early_single', '$event_cost_early_multiple', '$event_cost_late_single', '$event_cost_late_multiple', '$event_cost_free', 
			'$event_cost_table', '$event_comp_code', '$event_registration_deadline', '$event_registration_warning', '$event_cancellation_instructions', 
			'$event_payment_instructions', '$event_text_thankyou', '$event_email_noticesto', '$event_contact_name', '$event_contact_email', 
			'$event_allowreg_startdate', '$event_allowreg_enddate', '$event_created_on', '$event_created_by', '$event_editable_by', 
			'$event_page_template', '$event_prompt_table', '$event_prompt_table_text', '$event_prompt_address_home', '$event_prompt_data_hotel', 
			'$event_prompt_data_cc_affiliation', '$event_prompt_data_cc_esc', '$event_prompt_data_cc_role', '$event_prompt_data_cc_previous', '$event_prompt_data_cc_focus', 
			'$event_prompt_data_afterschool', '$event_prompt_inperson_online', '$event_preevent', '$event_preevent_name', '$event_preevent_cost', 
			'$event_max_registrants', '$event_max_reg_nextform_id', '$event_max_registrants_perform', '$event_min_registrants_perform', '$event_confirm_checkbox', 
			'$event_prompt_session_choice', '$event_session1_title', '$event_session1_desc', '$event_session2_title', '$event_session2_desc', 
			'$event_allow_pay_atdoor', '$event_allow_pay_atdoor_text', '$event_allow_pay_invoiceme', '$event_allow_pay_invoiceme_text', '$event_prompt_individual_org', 
			'$event_extra_text', '$event_budgetcode',
			'$event_custom_question1_text', '$event_custom_question1_type', '$event_custom_question1_options', '$event_custom_question2_text', '$event_custom_question2_type', '$event_custom_question2_options',
			'$event_custom_opentext_question1', '$event_custom_opentext_question2', '$event_custom_opentext_afterschool1', '$event_custom_opentext_afterschool2')";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;

		$feedback_message = "Your event was copied successfully, but it has the same dates as the original.  Please edit the event dates and review other settings for this event.";
		$location = "show_events";
	
	} # END IF/ELSE
}
##########################################
## END: LOCATION = COPY_EVENT
##########################################


##########################################
## START: LOCATION = PROCESS_DELETE_EVENT
##########################################
## START: CHECK IF USER IS AUTHORIZED
if ($location eq 'process_delete_event') {
	my $user_authorized = "";
	my $new_location = "";
	($user_authorized, $error_message) = &check_user_authorized($eventid, $cookie_ss_staff_id);
	if ($user_authorized ne 'yes') {
		$location = "show_events";
	}
}
## END: CHECK IF USER IS AUTHORIZED



if ($location eq 'process_delete_event') {
	if ($confirm_action ne 'yes') {
		$error_message = "The deletion <strong>WAS NOT</strong> processed.  You forgot to click the confirm delete checkbox.  Please try again.";
		$location = "show_events";
	} elsif ($eventid eq '') {
		$error_message = "The deletion <strong>WAS NOT</strong> processed.  The event ID to delete was not passed properly.  Please notify Brian Litke at ext. 6529 of this error.";
		$location = "show_events";
	} else {
	
		my $command_check_orgs = "SELECT registration_orgs.registration_event_id from registration_orgs WHERE registration_orgs.registration_event_id = '$eventid'";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command_check_orgs) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_event_orgs = $sth->rows;

		if ($num_matches_event_orgs ne '0') {
			$error_message = "The deletion <strong>WAS NOT</strong> processed.  There is are event registrations on file for thie event.  Please delete them first, then delete the event.";
			$location = "show_events";
		} else {
			## DELETE EVENT
			my $command_delete_event = "DELETE FROM registration_events WHERE event_id ='$eventid'";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_delete_event) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
#my $num_matches = $sth->rows;
			$feedback_message .= "EVENT DELETE COMMAND: $command_delete_event<br>";
			$feedback_message .= "The deletion of this event was processed successfully.";
			$location = "show_events";
		} # END IF/ELSE
	} # END IF/ELSE
}
##########################################
## END: LOCATION = PROCESS_DELETE_EVENT
##########################################


################################
## START: LOCATION = SHOW_EVENTS
################################
if ($location eq 'show_events') {
	my %events_byyear;
	my %people_byyear;
	my %orgs_byyear;
	
	my $warning_cc_oic = "";
	my $admin_user = "no";
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'awest');
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'blitke');
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'cpierron');
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'cmoses');
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'ewaters');
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'eurquidi');
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'jmabus');
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'jwackwit');
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'lshankla');
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'lforador');
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'nreynold');
      $admin_user = "yes" if ($cookie_ss_staff_id eq 'sabdulla');
	my $admin_message = "You are only allowed to see events you created or which are editable by you.";
		if ($admin_user eq 'yes') {
			$admin_message = "<FONT COLOR=\"GREEN\">You are logged in as an administrator and can see all event entries, regardless of which staff member created it. Regular users can only see entries they created or have permissions to edit.</FONT>";
		}
	my $command = "SELECT * from registration_events";
		if ($admin_user ne 'yes') {
			$command .= " WHERE event_created_by LIKE '$cookie_ss_staff_id' OR event_editable_by LIKE '%$cookie_ss_staff_id%'";
			if ($show_year ne 'any') {
				$command .= " AND ((event_date_label LIKE '%$show_year%') OR (event_registration_deadline LIKE '%$show_year%'))";
			}
		} else {
			$command .= " WHERE ((event_date_label LIKE '%$show_year%') OR (event_registration_deadline LIKE '%$show_year%'))";
		}
	   $command .= " order by event_id DESC" if (($sortby eq '') || ($sortby eq 'id'));
	   $command .= " order by event_name" if ($sortby eq 'name');
#	print "<p class=\"info\">$command</p>";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_events = $sth->rows;
	my $counter = "1";

print<<EOM;
<H2>Select an Event</H2>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<div class="dottedBoxyw">
You are viewing a list of $num_matches_events events in the database. $admin_message 
<br>
<br>
<form action="registration-admin.cgi" method="POST"> <label for="show_year">Change view to summary of year </label>
<SELECT NAME="show_year" id="show_year">
<OPTION VALUE="any">any year</option>
EOM
	my $year_loop = $year + 1;
	while ($year_loop >= 2005) {
		print "<OPTION VALUE=\"$year_loop\"";
		print " SELECTED" if ($year_loop eq $show_year);
		print ">$year_loop</OPTION>";
		$year_loop--;
	}

print<<EOM;
		</SELECT>
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="show_events">
		<input TYPE="submit" VALUE="Go">
		</FORM>
</div>
<p>Items in grey have passed the registration deadline for that event.</p>
<p>
Click here to <A HREF="registration-admin.cgi?location=edit_event&amp;show_year=$show_year">create a new event</A>.
</p>
<TABLE BORDER="1" CELLPADDING="2" CELLSPACING="0" width="100%">
<TR>
<TD bgcolor="#EBEBEB"><a href="registration-admin.cgi?sortby=name&amp;show_year=$show_year">Event Name</a></TD>
<TD bgcolor="#EBEBEB">Event Date</TD>
<TD bgcolor="#EBEBEB">Costs (Early/Late)</TD>
<TD bgcolor="#EBEBEB">Contact Name</TD>
<TD bgcolor="#EBEBEB">Created by</TD>
<TD bgcolor="#EBEBEB"><a href="registration-admin.cgi?sortby=id&amp;show_year=$show_year">ID</a></TD>
</TR>
EOM

		while (my @arr = $sth->fetchrow) {
	   		my ($event_id, $event_name, $event_name_italics, $event_name_short, $event_date_label, $event_location, $event_accomodations, $event_agenda_link, $event_agenda_link_text, $event_discount_reg_date, $event_cost_early_single, $event_cost_early_multiple, $event_cost_late_single, $event_cost_late_multiple, $event_cost_free, $event_cost_table, $event_comp_code, $event_registration_deadline, $event_registration_warning, $event_cancellation_instructions, $event_payment_instructions, $event_text_thankyou, $event_email_noticesto, $event_contact_name, $event_contact_email, $event_allowreg_startdate, $event_allowreg_enddate, $event_created_on, $event_created_by, $event_editable_by, $event_page_template, $event_prompt_table, $event_prompt_table_text, $event_prompt_address_home, $event_prompt_data_hotel, $event_prompt_data_cc_affiliation, $event_prompt_data_cc_esc, $event_prompt_data_cc_role, $event_prompt_data_cc_previous, $event_prompt_data_cc_focus, $event_prompt_data_afterschool, $event_prompt_inperson_online, $event_preevent, $event_preevent_name, $event_preevent_cost, $event_max_registrants, $event_max_reg_nextform_id, $event_max_registrants_perform, $event_min_registrants_perform, $event_confirm_checkbox, $event_prompt_session_choice, $event_session1_title, $event_session1_desc, $event_session2_title, $event_session2_desc, $event_allow_pay_atdoor, $event_allow_pay_atdoor_text, $event_allow_pay_invoiceme, $event_allow_pay_invoiceme_text, $event_prompt_individual_org, $event_extra_text, $event_budgetcode, $event_custom_question1_text, $event_custom_question1_type, $event_custom_question1_options, $event_custom_question2_text, $event_custom_question2_type, $event_custom_question2_options, $event_custom_opentext_question1, $event_custom_opentext_question2, $event_custom_opentext_afterschool1, $event_custom_opentext_afterschool2) = @arr;
			my $event_year = &deduce_year($event_date_label);
			my $event_registration_deadline_pretty = commoncode::date2standard($event_registration_deadline);
			my $event_discount_reg_date_pretty = commoncode::date2standard($event_discount_reg_date);
			
			my $row_bgcolor = "";
			if ($eventid eq $event_id) {
				$row_bgcolor = "BGCOLOR=\"#FFFFCC\"";
			}
			if ($event_allowreg_enddate lt $date_full_mysql) {
				$row_bgcolor = "BGCOLOR=\"#cccccc\"";
			}
print<<EOM;
<TR $row_bgcolor><TD VALIGN="TOP" rowspan="2"><a name="$event_id"></a><strong><FONT COLOR="#000066">$event_name 
EOM
	if ($event_name_italics ne '') {
		print ": " if ($event_name ne ''); 
		print "<em>$event_name_italics</em>";
	}

	## START: LOOK UP NUMBER OF REGISTRATIONS ON FILE
	my $command = "SELECT registration_orgs.org_unique_id, registration_people.personal_unique_id 
		from registration_orgs, registration_people 
		WHERE registration_orgs.org_unique_id = registration_people.org_unique_id";
		$command .= " AND registration_orgs.registration_event_id LIKE '$event_id'";
		$command .= " order by registration_orgs.registration_timestamp";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_reg_on_file = $sth->rows;
	## END: LOOK UP NUMBER OF REGISTRATIONS ON FILE
	
	## SET WARNING FOR MISSING EVENT DATE
	if ($event_registration_deadline eq '0000-00-00') {
    	$event_registration_deadline_pretty = "<br><FONT COLOR=RED>Warning: You forgot to specify the registration deadline.</FONT>";
	}
	## SET WARNING FOR MISSING EVENT DATE
	if ($event_date_label eq '') {
    	$event_date_label = "<br><FONT COLOR=RED>Warning: You forgot to specify the event date.</FONT>";
	}
	## SET WARNING FOR 0.00 COST IF EVENT NOT LABELED AS FREE
	if (($event_cost_free ne 'yes') && ($event_cost_early_single == '0')) {
    	$event_cost_early_single .= "<br><FONT COLOR=RED>Warning: You have set the cost to a zero value, but you have not indicated the event is free.</FONT>";
	}
	## SET WARNING FOR EVENT WITH COST IF EVA MUELLER IS NOT ON THE EMAIL CC LIST
	$warning_cc_oic = "";
	if (($event_cost_free ne 'yes') && ($event_email_noticesto !~ 'mueller')) {
    	$warning_cc_oic = "<br><FONT COLOR=RED>Warning: Your event is not free, which means credit card payments may be accepted.  However, you have not added Eva Mueller (eva.mueller\@sedl.org) to the list of staff who receive contact emails when registrations are received.</FONT>";
	}

	## SET COST LABEL TO FREE IF IT IS FREE
	if (($event_cost_free eq 'yes') && ($event_cost_early_single != '0')) {
		$event_cost_early_single = "free";
    	$event_cost_early_single .= "<br><FONT COLOR=RED>Warning: You have set the cost to a non-zero value and have the event listed as free.</FONT>";
	} elsif (($event_cost_free eq 'yes') && ($event_cost_early_single == '0')) {
		$event_cost_early_single = "free";
	}
	
	$event_allowreg_startdate = commoncode::date2standard($event_allowreg_startdate);
	$event_allowreg_enddate = commoncode::date2standard($event_allowreg_enddate);

#			- Directions: <A HREF="registration-admin.cgi?location=how_to_link_to_form&amp;eventid=$event_id&amp;show_year=$show_year">linking to the form</A><br>

print<<EOM;
</FONT></strong><br>
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR><TD nowrap>
			- Preview this <A HREF="http://www.sedl.org/register/event$event_id.html">$event_page_template form</A><br>
			- Edit this <A HREF="registration-admin.cgi?location=edit_event&amp;eventid=$event_id&amp;show_year=$show_year">event's settings</A><br>
			- View <A HREF="registration-admin.cgi?location=show_orgs&amp;eventid=$event_id&amp;show_year=$show_year">registrations rcvd</A> ($num_matches_reg_on_file)<br>
EOM
	if ($num_matches_reg_on_file > 0) {
#			<option value="iqa-nametag-whitebordertopbottom.jpg" selected>IL Afterschool w/whitespace</option>
print<<EOM;
			- Print a <a href="registration-admin.cgi?location=printable_attendee_details&amp;eventid=$event_id&amp;show_year=$show_year&amp;printwhat=attendees">detailed list of attendees</a> (2-column)<br>
			- Print a <a href="registration-admin.cgi?location=printable_signin_sheet&amp;eventid=$event_id&amp;show_year=$show_year&amp;printwhat=attendees">sign-in sheet</a> attendees<br>
			- Print a <a href="registration-admin.cgi?location=printable_signin_sheet&amp;eventid=$event_id&amp;show_year=$show_year&amp;printwhat=presenters">sign-in sheet</a> staff/presenters<br>
			- Print 
			
       <A href="javascript:hideDiv('print_nametag_settings$counter');">nametags</A><br>
       <div id = "print_nametag_settings$counter" style="display:none;background-color:#ffffcc;">
       		
       		<div style="background-color:#ffffcc;padding:4px;">
EOM
print $query->start_multipart_form("POST", "print_nametags.cgi");
#			<form action="print_nametags.cgi" method="POST" style="margin-bottom:0;">
print<<EOM;
			<strong>Label Printing Settings:</strong><br>
			
			Label type: 
			<select name="avery_number">
			<option value="5392">Avery 5392</option>
			</select><br>
			
			Name formatting: 
			<select name="name_formatting">
			<option value="large">Large, first/last on separate lines</option>
			<option value="medium">Medium, first/last on same lines</option>
			</select><br>

			Label artwork: 
			<select name="artwork">
			<option value="">none</option>
			<option value="iqa-nametag.jpg" selected>IL Afterschool</option>
			<option value="z_iqa-nametag.jpg" selected>IL Afterschool 2</option>
			</select><br>

			Extra pages w/blank nametags: 
			<select name="extra_pages">
			<option value="0">none</option>
			<option value="1">1</option>
			<option value="2">2</option>
			</select><br><br>

			Data Source: 
			<select name="data_source">
			<option value="event_registration">registrations rcvd</option>
			<option value="file_upload">tab-delimited file upload</option>
			</select><br><br>
			
			<div style="margin-left:20px;">
			If you chose "File Upload," click the Browse button<br>
			to select a tab-delimited file<br>whose data is arranged into these columns:<br>
			(1)org,<br>(2)afterschool program name,<br>(3)city,<br>(4)state,<br>(5)prefix,<br>(6)firstname,<br>(7)lastname,<br>(8)title,<br>(9)org affiliation<br>(10)email<br> 
EOM
print $query->filefield('uploaded_file','starting value',30,80);
print<<EOM;
</div>
<br>
<br>
EOM

if ($location eq 'never') {
print<<EOM;
			Fields to include:<br>
			<input type="checkbox" name="show_prefix" id="show_prefix" value="yes"><label for="show_prefix">Prefix</label><br>
			<input type="checkbox" name="show_firstname" id="show_firstname" value="yes" checked><label for="show_firstname">First name</label><br>
			<input type="checkbox" name="show_lastname" id="show_lastname" value="yes" checked><label for="show_lastname">Last name</label><br>
			<input type="checkbox" name="show_org_affiliation" id="show_org_affiliation" value="yes" checked><label for="show_org_affiliation">Organization</label><br>
			<input type="checkbox" name="show_title" id="show_title" value="yes"><label for="show_title">Title</label><br>
			<input type="checkbox" name="show_email" id="show_email" value="yes"><label for="show_email">Email</label><br><br>
EOM
}
#			<input type="hidden" name="data_source" value="event_registration">
print<<EOM;
			<input type="hidden" name="data_source_id" value="$event_id">
			<INPUT TYPE="SUBMIT" VALUE="Print Nametags to a PDF">
			</form>
			</div>
       </div>

EOM
	}
print<<EOM;
			- Registration form will be available online<br>
			  &nbsp;&nbsp;($event_allowreg_startdate - $event_allowreg_enddate)
		</TD></TR>
	</TABLE>
	</TD>
	<TD VALIGN="TOP" rowspan="2">$event_date_label<P class="small"><FONT COLOR="#999999">
					Final Reg. Deadline<br>
					$event_registration_deadline_pretty
EOM
	if (($event_discount_reg_date ne '') && ($event_discount_reg_date ne '0000-00-00')) {
		$event_discount_reg_date = commoncode::date2standard($event_discount_reg_date);
		print "<br><br>Early Reg. deadline<br>$event_discount_reg_date";
	}
	if ($event_max_registrants ne '0') {
		print "<br><br>Max capacity: $event_max_registrants";
	}
	if (($event_max_registrants_perform ne '0') && ($event_max_registrants_perform ne '10')) {
		print "<br><br>Max per submission: $event_max_registrants_perform";
	}
	if (($event_min_registrants_perform ne '0') && ($event_min_registrants_perform ne '1')) {
		print "<br><br>Min per submission: $event_min_registrants_perform";
	}
	if ($event_max_reg_nextform_id ne '0') {
		print "<br><br>After capacity, redirect to reg. form: <A HREF=\"/cgi-bin/mysql/registration/registration.cgi?eventid=$event_max_reg_nextform_id&amp;show_year=$show_year\">$event_max_reg_nextform_id</A>";
	}

print<<EOM;
</FONT></TD>
	<TD VALIGN="TOP">
EOM
	if (($event_cost_early_single =~ 'Warning') || ($event_cost_early_single =~ 'free')) {
		print "$event_cost_early_single";
	} else {
		$event_budgetcode = "<font color=\"red\">MISSING BUDGET CODE</font>" if ($event_budgetcode eq '');
print<<EOM;
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" width="100%">
		<TR><TD VALIGN="TOP" colspan="2">Budgetcode:<br>$event_budgetcode</TD></TR>
		<TR><TD VALIGN="TOP" nowrap>E Single:</TD><TD ALIGN="RIGHT">\$$event_cost_early_single</TD></TR>
		<TR><TD VALIGN="TOP" nowrap>E Mult:</TD><TD ALIGN="RIGHT">\$$event_cost_early_multiple</TD></TR>
		<TR><TD VALIGN="TOP" nowrap>L Single:</TD><TD ALIGN="RIGHT">\$$event_cost_late_single</TD></TR>
		<TR><TD VALIGN="TOP" nowrap>L Mult:</TD><TD ALIGN="RIGHT">\$$event_cost_late_multiple</TD></TR>
		</TABLE>
EOM
	}
print<<EOM;
		</TD>
	<TD VALIGN="TOP">$event_contact_name<br>$event_contact_email
EOM
	if ($event_contact_name eq '') {
    	print "<FONT COLOR=RED>Warning: You have not specified a contact name.</FONT>";
	} 
	if ($event_contact_email eq '') {
    	print "<br><FONT COLOR=RED>Warning: You have not specified a contact email address, which is required to send the user a confirmation email.</FONT>";
	} 
	if ($event_email_noticesto ne '') {
		print "<br><br>cc emails: $event_email_noticesto";
	} else {
		print "<br><br><font color=red>no cc email specified</font>";
	}
	print "$warning_cc_oic" if ($warning_cc_oic ne ''); 
print<<EOM;
	</TD>
	<TD VALIGN="TOP">$event_created_by<P><FONT COLOR="#999999">editable by: $event_editable_by</FONT></TD>
	<TD VALIGN="TOP" NOWRAP>$event_id
EOM
#	if ($cookie_ss_staff_id eq 'blitke') {
print<<EOM;
<P>
<FORM ACTION="registration-admin.cgi" METHOD="POST">
  <INPUT TYPE="CHECKBOX" NAME="confirm_action" id="confirm_action_delete$event_id" VALUE="yes"><label for="confirm_action_delete$event_id">confirm</label><br>
  <input type="HIDDEN" name="show_year" value="$show_year">
  <INPUT TYPE="HIDDEN" NAME="eventid" VALUE="$event_id">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_delete_event">
  <INPUT TYPE="SUBMIT" VALUE="Delete">
  </FORM>

<FORM ACTION="registration-admin.cgi" METHOD="POST">
  <input type="HIDDEN" name="show_year" value="$show_year">
  <INPUT TYPE="CHECKBOX" NAME="confirm_action" id="confirm_action_copy$event_id" VALUE="yes"><label for="confirm_action_copy$event_id">confirm</label><br>
  <INPUT TYPE="HIDDEN" NAME="eventid" VALUE="$event_id">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="copy_event">
  <INPUT TYPE="SUBMIT" VALUE="Copy">
  </FORM>


EOM
#	}

print<<EOM;
</TD>
</TR>
<tr><td colspan="4" $row_bgcolor><span class="small">URL: <A HREF="http://www.sedl.org/register/event$event_id.html" target="_blank">http://www.sedl.org/register/event$event_id.html</A></span></td></tr>
EOM
			if ($event_year ne '') {
				$events_byyear{$event_year}++;
				$people_byyear{$event_year} += $num_matches_reg_on_file;
#				$orgs_byyear{$event_year};
			}
			$counter++;
		} # END WHILE LOOP

print "</TABLE>";


## GRAB NUMBER OF ORGS
	my $command = "SELECT registration_date from registration_orgs";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;

		while (my @arr = $sth->fetchrow) {
	   		my ($org_datestamp) = @arr;
	   		my $org_year = substr($org_datestamp,0,4);
	   		$orgs_byyear{$org_year}++; 
		} # END DB QUERY LOOP

	#########################
	## START: PRINT SUMMARY
	#########################
print<<EOM;
<h2>Summary</h2>
The SEDL Event Registration Manager has processed the following number of registrations relevant to the current display.
<P>
<table border="1" cellpadding="3" cellspacing="0">
<tr><td bgcolor="#ebebeb" align="right"><strong>Year</strong></td>
	<td bgcolor="#ebebeb" align="right"><strong># Events</strong></td>
	<td bgcolor="#ebebeb" align="right"><strong># People</strong></td>
	<td bgcolor="#ebebeb" align="right"><strong># Orgs</strong></td>
</tr>
EOM


# FOREACH LOOP
my $key;
foreach $key (sort keys %events_byyear) {
	$people_byyear{$key} = &format_number($people_byyear{$key}, "0","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
print<<EOM; # "$key: <br>";
<tr><td bgcolor="#ebebeb" align="right">$key</td>
	<td bgcolor="#ebebeb" align="right">$events_byyear{$key}</td>
	<td bgcolor="#ebebeb" align="right">$people_byyear{$key}</td>
	<td bgcolor="#ebebeb" align="right">$orgs_byyear{$key}</td>
</tr>
EOM
}

print<<EOM;
</table>
EOM
	#########################
	## END: PRINT SUMMARY
	#########################

}
################################
## END: LOCATION = SHOW_EVENTS
################################


###################################
## START: LOCATION = PROCESS_UPDATE_PAYMENT
###################################
if ($location eq 'process_update_payment') {
	my $new_pay_rcvd_by = $query->param("new_pay_rcvd_by");
	my $new_pay_rcvd_notes = $query->param("new_pay_rcvd_notes");
	my $new_pay_rcvd_type = $query->param("new_pay_rcvd_type");


	my $new_startdate_m = $query->param("new_startdate_m");
	my $new_startdate_d = $query->param("new_startdate_d");
	my $new_startdate_y = $query->param("new_startdate_y");

	my $new_pay_rcvd_date = "$new_startdate_y\-$new_startdate_m\-$new_startdate_d";
	   $new_pay_rcvd_date = "" if (($new_startdate_m eq '') || ($new_startdate_d eq '') || ($new_startdate_y eq ''));

	## START: BACKSLASH VARIABLES FOR DB
	$new_pay_rcvd_by = &commoncode::cleanthisfordb($new_pay_rcvd_by);
	$new_pay_rcvd_notes = &commoncode::cleanthisfordb($new_pay_rcvd_notes);
	$new_pay_rcvd_date = &commoncode::cleanthisfordb($new_pay_rcvd_date);
	$new_pay_rcvd_type = &commoncode::cleanthisfordb($new_pay_rcvd_type);
	## END: BACKSLASH VARIABLES FOR DB


	## DO THE EDIT
	my $command_update = "UPDATE registration_people SET pay_rcvd_date ='$new_pay_rcvd_date', pay_rcvd_by ='$new_pay_rcvd_by', pay_rcvd_notes ='$new_pay_rcvd_notes', pay_rcvd_type ='$new_pay_rcvd_type'
									WHERE personal_unique_id ='$show_person'";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_update) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	#my $num_matches = $sth->rows;
			
	$feedback_message .= "The payment was updated successfully.";
	$location = "show_people";

}
###################################
## END: LOCATION = PROCESS_UPDATE_PAYMENT
###################################


###################################
## START: LOCATION = UPDATE_PAYMENT
###################################
if ($location eq 'update_payment') {

	my $command = "SELECT registration_orgs.*, registration_people.* 
		from registration_orgs, registration_people 
		WHERE registration_people.personal_unique_id LIKE '$show_person' AND
		registration_orgs.org_unique_id = registration_people.org_unique_id";
		$command .= " AND registration_orgs.org_unique_id LIKE '$show_org' " if ($show_org ne '');
		$command .= " AND registration_orgs.registration_event_id LIKE '$eventid'" if ($eventid ne '');
		$command .= " order by registration_orgs.registration_timestamp ";
#		print "<P>COMMAND: $command";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;
	my $counter = "1";
	my $last_org_id = "";
		while (my @arr = $sth->fetchrow) {
    		my ($org_unique_id, $unit_cost, $table_cost, $total_cost, $comp_code, $org, $org_department, $address1, $address2, $city, $state, $zip, $phone, $fax, $email, $special_needs, $pay_how, $number_registrants, $registration_timestamp, $registration_date, $registration_event, $registration_event_id, $as_program_name, $as_students_served, $as_based, $as_years_operating, $as_grade_level, $as_locale, $custom_question1_data, $custom_question2_data, $custom_opentext_question1, $custom_opentext_question2, $custom_opentext_afterschool1, $custom_opentext_afterschool2, 
    		$personal_unique_id, $org_unique_id2, $prefix, $firstname, $lastname, $title, $org_affiliation, $email, $phone, $prefer_contact_home, $home_address, $home_city, $home_state, $home_zip, $unit_cost, $personal_cost, $pay_type, $pay_rcvd_type, $pay_rcvd_date, $pay_rcvd_by, $pay_rcvd_notes, 
    		$special_accomodations, $cc_job_affiliation, $cc_job_affiliation_other, $cc_esc, $cc_role, $cc_previous, $cc_focus, $hotel_arrive, $hotel_depart, $hotel_bed, $hotel_smoking, $session_number, $inperson_online) = @arr;

print<<EOM;
<H2>Update Payment Status for <em><FONT COLOR=RED>$prefix $firstname $lastname</FONT></em> for event:<br><FONT COLOR=GREEN>$this_event_name<br>$this_event_date_label</FONT></H2>
<P>
<FORM ACTION="registration-admin.cgi" METHOD="POST">
	<TABLE BORDER="1" CELLPADDING="1" CELLSPACING="0">
	<TR><TD>Date Received:</TD><TD>
EOM
my ($this_year, $this_month, $this_date) = split(/\-/,$pay_rcvd_date);
&commoncode::print_month_menu("new_startdate_m", $this_month); 
&commoncode::print_day_menu("new_startdate_d", $this_date); 
&commoncode::print_year_menu_descending("new_startdate_y", 2005, $year, $this_year);
print<<EOM;
	</TD></TR>
	<TR><TD><label for="new_pay_rcvd_type">Payment Type Received:</label></TD><TD>
			<select name="new_pay_rcvd_type" id="new_pay_rcvd_type">
			<OPTION></OPTION>
EOM
	## START: PRINT LIST OF PAYMENT TYPES
	my @payment_type = ("check", "credit card online", "credit card phone", "money order", "purchase order");
	my $counter_payment_type = "0";
		while ($counter_payment_type <= $#payment_type) {
			print "<OPTION";
			print " SELECTED" if ($payment_type[$counter_payment_type] eq $pay_rcvd_type);
			print ">$payment_type[$counter_payment_type]</OPTION>\n";
			$counter_payment_type++;
		}
	## END: PRINT LIST OF PAYMENT TYPES

print<<EOM;
		</select>
	</TD></TR>
	<TR><TD><label for="new_pay_rcvd_by">Payment Received By:</label></TD>
		<TD><input type="text" name="new_pay_rcvd_by" id="new_pay_rcvd_by" size="50" VALUE="$pay_rcvd_by"></TD></TR>
	<TR><TD VALIGN="TOP"><label for="new_pay_rcvd_notes">Notes:</label></TD>
		<TD><textarea name="new_pay_rcvd_notes" id="new_pay_rcvd_notes" rows="12" cols="70">$pay_rcvd_notes</textarea></TD></TR>
	</TABLE>
	<div style="margin-left:25px;">
 		<input type="HIDDEN" name="show_year" value="$show_year">
		<INPUT TYPE="HIDDEN" NAME="eventid" VALUE="$eventid">
		<INPUT TYPE="HIDDEN" NAME="show_person" VALUE="$show_person">
		<INPUT TYPE="HIDDEN" NAME="show_org" VALUE="$show_org">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_update_payment">
		<INPUT TYPE="SUBMIT" VALUE="Update this payment status">
		</FORM>
	</div>
EOM
		} # END DB QUERY LOOP
}
###################################
## END: LOCATION = UPDATE_PAYMENT
###################################


##########################################
## START: LOCATION = PROCESS_DELETE_PERSON
##########################################
## START: CHECK IF USER IS AUTHORIZED
if ($location eq 'process_delete_person') {
	my $user_authorized = "";
	my $new_location = "";
	($user_authorized, $error_message) = &check_user_authorized($eventid, $cookie_ss_staff_id);
	if ($user_authorized ne 'yes') {
		$location = "show_people";
	}
}
## END: CHECK IF USER IS AUTHORIZED

if ($location eq 'process_delete_person') {
	if ($confirm_action ne 'yes') {
		$error_message = "The deletion <strong>WAS NOT</strong> processed.  You forgot to click the confirm delete checkbox.  Please try again.";
		$location = "show_people";
	} elsif ($eventid eq '') {
		$error_message = "The deletion <strong>WAS NOT</strong> processed.  The event ID to delete was not passed properly.  Please notify Brian of this error.";
		$location = "show_people";
	} else {
		## DELETE PERSON
		my $command_delete_person = "DELETE FROM registration_people WHERE personal_unique_id ='$show_person'";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command_delete_person) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#my $num_matches = $sth->rows;
		$feedback_message .= "PERSON DELETE COMMAND: $command_delete_person";
		$feedback_message .= "The deletion of this person's registration was processed successfully.";
		$location = "show_people";
	} # END IF/ELSE

	## START: CHECK FOR ORPHAN ORGANIZATION
		## QUERY PEOPLE DATABASE TO SEE IF ANY PEOPLE LEFT IN THIS ORG
		my $show_org_cleaned = &commoncode::cleanthisfordb ($show_org);
		my $command_check_orgs = "SELECT personal_unique_id from registration_people WHERE org_unique_id = '$show_org_cleaned'";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command_check_orgs) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_event_people = $sth->rows;

		## IF NUM_MATCHES = 0, DELETE THE ORG RECORD
		if ($num_matches_event_people eq '0') {
			## DELETE ORG
			my $command_delete_org = "DELETE FROM registration_orgs WHERE org_unique_id ='$show_org'";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_delete_org) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
#my $num_matches = $sth->rows;
#			print "<P><FONT COLOR=RED>ORG DELETION COMMAND: $command_delete_org</FONT>";
			## PROVIDE FEEDBACK
			$feedback_message .= "This was the only person registered for this organization, so the organization registration was also deleted.";
		} else {
			## IF THERE ARE PEOPLE LEFT, UPDATE THAT FIELD IN THE ORGANIZATION RECORD
			## DELETE ORG
			my $command_update_org = "UPDATE registration_orgs SET number_registrants='$num_matches_event_people' WHERE org_unique_id ='$show_org'";
			my $dbh = DBI->connect($dsn, "corpuser", "public");
			my $sth = $dbh->prepare($command_update_org) or die "Couldn't prepare statement: " . $dbh->errstr;
			$sth->execute;
#my $num_matches = $sth->rows;
#			print "<P><FONT COLOR=RED>ORG UPDATE COMMAND: $command_update_org</FONT>";
		}
	## END: CHECK FOR ORPHAN ORGANIZATION
	
	
}
##########################################
## END: LOCATION = PROCESS_DELETE_PERSON
##########################################


################################
## START: LOCATION = EDIT_PERSON
################################
if ($location =~ 'edit_person') {

	## LOOK UP THE INFO ALREADY ON FILE FOR THIS PERSON
	my $view_org = ""; my $view_prefix = ""; my $view_firstname = ""; my $view_lastname = ""; my $view_title = ""; my $view_email = ""; my $view_phone = ""; my $cc_role = "";

	my $command = "SELECT registration_orgs.org, registration_people.prefix, registration_people.firstname, registration_people.lastname, registration_people.title, registration_people.email, registration_people.phone, registration_people.cc_role
		from registration_orgs, registration_people 
		WHERE registration_orgs.org_unique_id = registration_people.org_unique_id
		AND registration_people.personal_unique_id LIKE '$show_person'";

		$command .= " AND registration_orgs.org_unique_id LIKE '$show_org'" if ($show_org ne '');
		$command .= " AND registration_orgs.registration_event_id LIKE '$eventid'";
#		$command .= " order by registration_people.lastname, registration_people.firstname ";
		$command .= " order by registration_orgs.org, registration_orgs.registration_timestamp DESC";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_people = $sth->rows;
		while (my @arr = $sth->fetchrow) {
    		($view_org, $view_prefix, $view_firstname, $view_lastname, $view_title, $view_email, $view_phone, $cc_role) = @arr;
		}	
	
	
	## START: CHECK TO SEE ALL THE REQUIRED INFO IS PRESENT
	my $new_p_firstname = $query->param("new_p_firstname");
	my $new_p_lastname = $query->param("new_p_lastname");
	my $new_p_title = $query->param("new_p_title");
	my $new_p_email = $query->param("new_p_email");
	my $new_p_phone = $query->param("new_p_phone");
	my $new_cc_role = $query->param("new_cc_role");

	if (($location eq 'process_edit_person') && (($new_p_firstname eq '') || ($new_p_lastname eq '') || ($new_p_title eq '') || ($new_p_email eq '') || ($new_p_phone eq '')))  {
		$location = "edit_person";
		$error_message = "You left out one or more fields.  Please try again.";
	}
	if ($num_matches_people ne '1') {
		$location = "show_people";
		$error_message = "$command<P><FONT COLOR=RED>Error: The user ID was not located properly.  Contact webmaster\@sedl.org for assistance.</FONT><P>";
	}
	## START: CHECK TO SEE ALL THE REQUIRED INFO IS PRESENT


	if ($location eq 'process_edit_person') {
		$new_p_firstname = &commoncode::cleanthisfordb($new_p_firstname);
		$new_p_lastname = &commoncode::cleanthisfordb($new_p_lastname);
		$new_p_title = &commoncode::cleanthisfordb($new_p_title);
		$new_p_email = &commoncode::cleanthisfordb($new_p_email);
		$new_p_phone = &commoncode::cleanthisfordb($new_p_phone);
		$new_cc_role = &commoncode::cleanthisfordb($new_cc_role);

	my $command_update = "UPDATE registration_people 
					SET firstname = '$new_p_firstname', lastname = '$new_p_lastname', title = '$new_p_title', email = '$new_p_email', phone = '$new_p_phone' , cc_role = '$new_cc_role' 
					WHERE personal_unique_id LIKE '$show_person'";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_update) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;
		$location = "show_people";
		$feedback_message = "The person's information was updated successfully.";

	}

	if ($location eq 'edit_person') {


print<<EOM;
<H2>Edit Person</H2>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p></p>
<FORM ACTION="registration-admin.cgi" METHOD="POST">
<TABLE>
<TR><TD VALIGN="TOP">Name</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_p_firstname" SIZE="20" VALUE="$view_firstname"> 
					<INPUT type="text" NAME="new_p_lastname" SIZE="20" VALUE="$view_lastname"></TD></TR>
<TR><TD VALIGN="TOP">Title</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_p_title" SIZE="50" VALUE="$view_title"></TD></TR>
<TR><TD VALIGN="TOP">Email</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_p_email" SIZE="50" VALUE="$view_email"></TD></TR>
<TR><TD VALIGN="TOP">Phone</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_p_phone" SIZE="30" VALUE="$view_phone"></TD></TR>
<TR><TD VALIGN="TOP">Presenter vs. Participant (cc_role)</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_cc_role" SIZE="30" VALUE="$cc_role"></TD></TR>
</TABLE>

  <input type="HIDDEN" name="show_year" value="$show_year">
  <INPUT TYPE="HIDDEN" NAME="show_person" VALUE="$show_person">
  <INPUT TYPE="HIDDEN" NAME="show_org" VALUE="$show_org">
  <INPUT TYPE="HIDDEN" NAME="eventid" VALUE="$eventid">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_edit_person">
  <INPUT TYPE="SUBMIT" VALUE="Edit this person's (USER ID: $show_person) registration">
  </FORM>
EOM
	}
}
################################
## END: LOCATION = EDIT_PERSON
################################



################################
## START: LOCATION = SHOW_PEOPLE
################################
if ($location eq 'show_people') {
   
	$sortby = "registrant_information" if ($sortby eq '');
	my $show_org_cleaned = &commoncode::cleanthisfordb ($show_org);

	my $command = "SELECT registration_orgs.*, registration_people.* 
		from registration_orgs, registration_people 
		WHERE registration_orgs.org_unique_id = registration_people.org_unique_id";
		$command .= " AND registration_orgs.org_unique_id LIKE '$show_org_cleaned' " if ($show_org ne '');
		$command .= " AND registration_orgs.registration_event_id LIKE '$eventid'" if ($eventid ne '');
		$command .= " order by registration_people.lastname, registration_people.firstname" if ($sortby eq 'registrant_information');
		$command .= " order by registration_people.pay_type, registration_people.lastname, registration_people.firstname" if ($sortby eq 'payment_type');
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_people = $sth->rows;
	my $counter = "1";
	my $last_org_id = "";

print<<EOM;
<H2>Registered People</H2>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
You are viewing a list of <strong>$num_matches_people PEOPLE</strong> signed up for:<br>
<strong><FONT COLOR="PURPLE">$this_event_name <em>$this_event_name_italics</em></FONT></strong> on $this_event_date_label in $this_event_location.
<P>
Click here to see a list of <A HREF="registration-admin.cgi?location=show_orgs&eventid=$eventid&amp;show_year=$show_year">registered organizations</A>.
<P>
EOM
	if ($show_org ne '') {
		print "<P>You are viewing people registered from a single organization.  Click here to view <A HREF=\"registration-admin.cgi?location=show_people&amp;eventid=$eventid&amp;show_year=$show_year\">people registered for this event from ALL organizations</A>.<P>";
	}
my $heading_label_registrant_inforamtion = "Registrant Information";
   $heading_label_registrant_inforamtion = "<A HREF=\"registration-admin.cgi?location=$location&amp;eventid=$eventid&amp;sortby=registrant_information&amp;show_year=$show_year\">Registrant Information</A>" if ($sortby ne 'registrant_information');
my $heading_label_payment_type = "Payment Type";
   $heading_label_payment_type = "<A HREF=\"registration-admin.cgi?location=$location&eventid=$eventid&amp;sortby=payment_type&amp;show_year=$show_year\">Payment Type</A>" if ($sortby ne 'payment_type');

print<<EOM;
<TABLE BORDER=1 CELLPADDING=1 CELLSPACING=0>
<TR>
<TD bgcolor="#EBEBEB">$heading_label_registrant_inforamtion</TD>
EOM
	if ($this_event_cost_free ne 'yes') {
print<<EOM;
<TD bgcolor="#EBEBEB">Indiv.cost</TD>
<TD bgcolor="#EBEBEB">$heading_label_payment_type</TD>
<TD bgcolor="#EBEBEB">Payment Received?</TD>
EOM
	}
print "</TR>";


		while (my @arr = $sth->fetchrow) {
    		my ($org_unique_id, $unit_cost, $table_cost, $total_cost, $comp_code, $org, $org_department, $address1, $address2, $city, $state, $zip, $phone, $fax, $email, $special_needs, $pay_how, $number_registrants, $registration_timestamp, $registration_date, $registration_event, $registration_event_id, $as_program_name, $as_students_served, $as_based, $as_years_operating, $as_grade_level, $as_locale, $custom_question1_data, $custom_question2_data, $custom_opentext_question1, $custom_opentext_question2, $custom_opentext_afterschool1, $custom_opentext_afterschool2,
    		$personal_unique_id, $org_unique_id2, $prefix, $firstname, $lastname, $title, $org_affiliation, $email, $phone, $prefer_contact_home, $home_address, $home_city, $home_state, $home_zip, $unit_cost, $personal_cost, $pay_type, $pay_rcvd_type, $pay_rcvd_date, $pay_rcvd_by, $pay_rcvd_notes, $special_accomodations, $cc_job_affiliation, $cc_job_affiliation_other, $cc_esc, $cc_role, $cc_previous, $cc_focus, $hotel_arrive, $hotel_depart, $hotel_bed, $hotel_smoking, $session_number, $inperson_online) = @arr;

			## PRINT ORG LINE
print<<EOM;
<TR>
<TD VALIGN="TOP"><TABLE BORDER=1 CELLPADDING=1 CELLSPACING=0 WIDTH="100%">
	<TR><TD VALIGN="TOP"><strong>Name $counter:</strong></TD><TD WIDTH="350">

			<FORM ACTION="registration-admin.cgi" METHOD="POST">$firstname $lastname
			<input type="HIDDEN" name="show_year" value="$show_year">
			<INPUT TYPE="HIDDEN" NAME="show_person" VALUE="$personal_unique_id">
			<INPUT TYPE="HIDDEN" NAME="show_org" VALUE="$show_org">
			<INPUT TYPE="HIDDEN" NAME="eventid" VALUE="$eventid">
			<INPUT TYPE="HIDDEN" NAME="location" VALUE="edit_person">
			<INPUT TYPE="SUBMIT" VALUE="Edit">
			</FORM>
		
		</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Org Affiliation:</strong></TD><TD>$org_affiliation</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Title:</strong></TD><TD>$title</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Email:</strong></TD><TD>$email</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Phone:</strong></TD><TD>$phone</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Org.:</strong></TD><TD><A HREF="registration-admin.cgi?location=show_orgs&amp;show_org=$org_unique_id&amp;eventid=$eventid&amp;show_year=$show_year">$org</A></TD></TR>
	<TR><TD VALIGN="TOP" NOWRAP><strong>Special accommodations:</strong></TD><TD>$special_accomodations</TD></TR>
		<TR><TD VALIGN="TOP"><strong>Presenter:</strong></TD>
			<TD>Role: $cc_role</TD></TR>
EOM
			## PRINT INPERSON/ONLINE FIELD, IF EVENT ALLOWS THEM
			if ($this_event_prompt_inperson_online eq 'yes') {
print<<EOM;
		<TR><TD VALIGN="TOP"><strong>In Person/Online Participation</strong></TD>
			<TD>$inperson_online</TD></TR>
EOM
			}
			## PRINT SESSION CHOICE FIELDS, IF EVENT ALLOWS THEM
			if ($this_event_prompt_session_choice eq 'yes') {
print<<EOM;
		<TR><TD VALIGN="TOP"><strong>Session Choice:</strong></TD>
			<TD>$session_number</TD></TR>
EOM
			}
			## PRINT HOME ADDRESS FIELDS, IF EVENT ALLOWS THEM
			if (($this_event_prompt_address_home eq 'yes') && ($prefer_contact_home eq 'yes')) {
print<<EOM;
		<TR><TD VALIGN="TOP"><strong>Home<br>address:</strong></TD>
			<TD>Prefer home contact: $prefer_contact_home
			<P>
			$home_address<br>
			$home_city, $home_state $home_zip</TD></TR>
EOM
			}
			## PRINT CC ROLE, IF EVENT ALLOWS THEM
			if ($this_event_prompt_data_cc_role eq 'yes') {
print<<EOM;
		<TR><TD VALIGN="TOP"><strong>CC<br>Role<br>Data:</strong></TD>
			<TD>Role: $cc_role</TD></TR>
EOM
			}
			## PRINT CC ROLE, IF EVENT ALLOWS THEM
			if ($this_event_prompt_data_cc_previous ne '') {
print<<EOM;
		<TR><TD VALIGN="TOP"><strong>Previous Attendee?:</strong></TD>
			<TD>$cc_previous</TD></TR>
EOM
			}
			## PRINT CC ROLE, IF EVENT ALLOWS THEM
			if ($this_event_prompt_data_cc_focus eq 'yes') {
print<<EOM;
		<TR><TD VALIGN="TOP"><strong>Focus Area:</strong></TD>
			<TD>Focus Area: $cc_focus</TD></TR>
EOM
			}
			## PRINT CC AFFILIATION, IF EVENT ALLOWS THEM
			if ($this_event_prompt_data_cc_affiliation eq 'yes') {
print<<EOM;
		<TR><TD VALIGN="TOP"><strong>CC<br>Affiliation<br>Data:</strong></TD>
			<TD>Job Affiliation: $cc_job_affiliation
EOM
print " $cc_job_affiliation_other" if ($cc_job_affiliation_other ne '');
print "</TD></TR>";
			}

			## PRINT CC ESC, IF EVENT ALLOWS THEM
			if ($this_event_prompt_data_cc_esc eq 'yes') {
print<<EOM;
		<TR><TD VALIGN="TOP"><strong>CC<br>ESC<br>Data:</strong></TD>
			<TD>ESC Region: $cc_esc</TD></TR>
EOM
			}



			if ($this_event_prompt_data_hotel eq 'yes') {
print<<EOM;
		<TR><TD VALIGN="TOP"><strong>Hotel<br>Data:</strong></TD>
			<TD>Hotel Arrival: $hotel_arrive<br>
				Hotel Departure: $hotel_depart<br>
				Hotel Bed: $hotel_bed<br>
				Hotel Smoking: $hotel_smoking	
			</TD></TR>
EOM
			}
#	if ($cookie_ss_staff_id eq 'blitke') {
print <<EOM;
<TR><TD VALIGN="TOP"><FONT COLOR=RED><strong>Delete?</strong></FONT></TD><TD><FORM ACTION="registration-admin.cgi" METHOD="POST">
  <INPUT TYPE="CHECKBOX" NAME="confirm_action" VALUE="yes">Click to confirm the deletion of this person<br>
  <input type="HIDDEN" name="show_year" value="$show_year">
  <INPUT TYPE="HIDDEN" NAME="show_person" VALUE="$personal_unique_id">
  <INPUT TYPE="HIDDEN" NAME="show_org" VALUE="$show_org">
  <INPUT TYPE="HIDDEN" NAME="eventid" VALUE="$eventid">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_delete_person">
  <INPUT TYPE="SUBMIT" VALUE="Delete this person's (USER ID: $personal_unique_id) registration">
  </FORM></TD></TR>
EOM
#	}

print<<EOM;
	</TABLE>
	</TD>
EOM
			## START: PRINT EVENT COST FIELDS, IF EVENT NOT FREE
			if ($this_event_cost_free ne 'yes') {
print<<EOM;
<TD VALIGN="TOP">\$$personal_cost</TD>
<TD VALIGN="TOP">$pay_type</TD>
<TD VALIGN="TOP">
EOM
				if ($pay_rcvd_date eq '0000-00-00') {
					print "Payment not yet recorded in system.";
				} else {
					print "<FONT COLOR=GREEN>Payment received.</FONT>";
				}
	$pay_rcvd_date = &commoncode::date2standard($pay_rcvd_date);

print<<EOM;
<P>
	<TABLE BORDER="1" CELLPADDING="1" CELLSPACING="0">
	<TR><TD COLSPAN="2" ALIGN="CENTER"><A HREF= "registration-admin.cgi?location=update_payment&amp;show_person=$personal_unique_id&amp;eventid=$eventid&amp;show_org=$show_org&amp;show_year=$show_year">update payment info</A></TD></TR>
	<TR><TD>Date Rcvd:</TD><TD>$pay_rcvd_date</TD></TR>
	<TR><TD>Payment Type Rcvd:</TD><TD>$pay_rcvd_type</TD></TR>
	<TR><TD>Rcvd By:</TD><TD>$pay_rcvd_by</TD></TR>
	<TR><TD>Notes:</TD><TD>$pay_rcvd_notes</TD></TR>
	</TABLE>
	</TD>

</TR>
EOM
			} # END IF
			## END: PRINT EVENT COST FIELDS, IF EVENT NOT FREE
			$counter++;
		} # END WHILE LOOP
print "</TABLE>";
}
################################
## END: LOCATION = SHOW_PEOPLE
################################


######################################################
## START: LOCATION = printable_signin_sheet
######################################################
if ($location eq 'printable_signin_sheet') {
   
	$sortby = "registrant_information" if ($sortby eq '');
	my $show_org_cleaned = &commoncode::cleanthisfordb ($show_org);

	my $command = "SELECT registration_orgs.*, registration_people.* 
		from registration_orgs, registration_people 
		WHERE registration_orgs.org_unique_id = registration_people.org_unique_id";
		$command .= " AND registration_orgs.org_unique_id LIKE '$show_org_cleaned' " if ($show_org ne '');
		$command .= " AND registration_people.cc_role NOT LIKE 'presenter'" if ($printwhat ne 'presenters');
		$command .= " AND registration_people.cc_role LIKE 'presenter'" if ($printwhat eq 'presenters');

		$command .= " AND registration_orgs.registration_event_id LIKE '$eventid'" if ($eventid ne '');

		$command .= " order by registration_people.lastname, registration_people.firstname" if ($sortby eq 'registrant_information');
		$command .= " order by registration_people.pay_type, registration_people.lastname, registration_people.firstname" if ($sortby eq 'payment_type');
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_people = $sth->rows;
	my $counter = "1";
	my $last_org_id = "";
#print "$command $num_matches_people";
print<<EOM;
<div style="text-align:center;">
<H2>Sign-In Sheet</H2>
<p>
<strong style="font-size:18px;line-height:24px;">$this_event_name <em>$this_event_name_italics</em></strong><br>
$this_event_date_label<br>$this_event_location
</p>
</div>

<table border="1" cellpadding="6" cellspacing="0" style="width:100%;">
<tr><td bgcolor="#EBEBEB" style="width:4%;"><strong>#</strong></td>
	<td bgcolor="#EBEBEB" style="width:20%;"><strong>Name</strong></td>
	<td bgcolor="#EBEBEB" style="width:32%;"><strong>Organization</strong></td>
	<td bgcolor="#EBEBEB" style="width:24%;"><strong>Email</strong></td>
	<td bgcolor="#EBEBEB" style="width:20%;"><strong>Signature</strong></td>
</tr>
EOM


		while (my @arr = $sth->fetchrow) {
    		my ($org_unique_id, $unit_cost, $table_cost, $total_cost, $comp_code, $org, $org_department, $address1, $address2, $city, $state, $zip, $phone, $fax, $email, $special_needs, $pay_how, $number_registrants, $registration_timestamp, $registration_date, $registration_event, $registration_event_id, $as_program_name, $as_students_served, $as_based, $as_years_operating, $as_grade_level, $as_locale, $custom_question1_data, $custom_question2_data, $custom_opentext_question1, $custom_opentext_question2, $custom_opentext_afterschool1, $custom_opentext_afterschool2, 
    		$personal_unique_id, $org_unique_id2, $prefix, $firstname, $lastname, $title, $org_affiliation, $email, $phone, $prefer_contact_home, $home_address, $home_city, $home_state, $home_zip, $unit_cost, $personal_cost, $pay_type, $pay_rcvd_type, $pay_rcvd_date, $pay_rcvd_by, $pay_rcvd_notes, $special_accomodations, $cc_job_affiliation, $cc_job_affiliation_other, $cc_esc, $cc_role, $cc_previous, $cc_focus, $hotel_arrive, $hotel_depart, $hotel_bed, $hotel_smoking, $session_number, $inperson_online) = @arr;
			if ($as_program_name ne '') {
				$as_program_name = "<br>$as_program_name";
			}
			## PRINT ORG LINE
print<<EOM;
<tr><td valign="top">$counter\.</td>
	<td valign="top"><strong>$firstname $lastname</strong><br><em style="font-size:10px;">$title</em></td>
	<td valign="top">$org_affiliation$as_program_name</td>
	<td valign="top">$email</td>
	<td valign="top">&nbsp;</td>
</tr>
EOM
			## END: PRINT EVENT COST FIELDS, IF EVENT NOT FREE
			$counter++;
		if (
			($counter == '11') || ($counter == '21') || ($counter == '31') || ($counter == '41') || ($counter == '51') || ($counter == '61') || ($counter == '71') || ($counter == '81') || ($counter == '91')
			|| ($counter == '101') || ($counter == '111') || ($counter == '121') || ($counter == '131') || ($counter == '141') || ($counter == '151') || ($counter == '161') || ($counter == '171') || ($counter == '181') || ($counter == '191')
			|| ($counter == '201') || ($counter == '211') || ($counter == '221') || ($counter == '231') || ($counter == '241') || ($counter == '251') || ($counter == '261') || ($counter == '271') || ($counter == '281') || ($counter == '291')
			|| ($counter == '301') || ($counter == '311') || ($counter == '321') || ($counter == '331') || ($counter == '341') || ($counter == '351') || ($counter == '361') || ($counter == '371') || ($counter == '381') || ($counter == '391')
			|| ($counter == '401') || ($counter == '411') || ($counter == '421') || ($counter == '431') || ($counter == '441') || ($counter == '451') || ($counter == '461') || ($counter == '471') || ($counter == '481') || ($counter == '491')
			) {
print<<EOM;
</TABLE>
<div class="pagebreak"></div>

<div style="text-align:center;">
<H2>Sign-In Sheet</H2>
<p>
<strong style="font-size:18px;line-height:24px;">$this_event_name <em>$this_event_name_italics</em></strong><br>
$this_event_date_label<br>$this_event_location
</p>
</div>

<table border="1" cellpadding="6" cellspacing="0" style="width:100%;">
<tr><td bgcolor="#EBEBEB" style="width:4%;"><strong>#</strong></td>
	<td bgcolor="#EBEBEB" style="width:20%;"><strong>Name</strong></td>
	<td bgcolor="#EBEBEB" style="width:32%;"><strong>Organization</strong></td>
	<td bgcolor="#EBEBEB" style="width:24%;"><strong>Email</strong></td>
	<td bgcolor="#EBEBEB" style="width:20%;"><strong>Signature</strong></td>
</tr>
EOM
		}


		} # END WHILE LOOP
print "</TABLE>\n";
}
######################################################
## END: LOCATION = printable_signin_sheet
######################################################

######################################################
## START: LOCATION = printable_attendee_details
######################################################
if ($location eq 'printable_attendee_details') {
   
	$sortby = "registrant_information" if ($sortby eq '');
	my $show_org_cleaned = &commoncode::cleanthisfordb ($show_org);

	my $command = "SELECT registration_orgs.*, registration_people.* 
		from registration_orgs, registration_people 
		WHERE registration_orgs.org_unique_id = registration_people.org_unique_id";
		$command .= " AND registration_orgs.org_unique_id LIKE '$show_org_cleaned' " if ($show_org ne '');
		$command .= " AND registration_people.cc_role NOT LIKE 'presenter'" if ($printwhat ne 'presenters');
		$command .= " AND registration_people.cc_role LIKE 'presenter'" if ($printwhat eq 'presenters');

		$command .= " AND registration_orgs.registration_event_id LIKE '$eventid'" if ($eventid ne '');

		$command .= " order by registration_people.lastname, registration_people.firstname" if ($sortby eq 'registrant_information');
		$command .= " order by registration_people.pay_type, registration_people.lastname, registration_people.firstname" if ($sortby eq 'payment_type');
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_people = $sth->rows;
	my $last_org_id = "";
#print "$command $num_matches_people";
print<<EOM;
<div style="text-align:center;width:70%;margin: auto;">
<H2>Atendees</H2>
<p>
<strong style="font-size:18px;line-height:24px;">$this_event_name <em>$this_event_name_italics</em></strong><br>
$this_event_date_label<br>$this_event_location
</p>
</div>
EOM

	my $counter = "1";
		while (my @arr = $sth->fetchrow) {
    		my ($org_unique_id, $unit_cost, $table_cost, $total_cost, $comp_code, $org, $org_department, $address1, $address2, $city, $state, $zip, $phone, $fax, $email, $special_needs, $pay_how, $number_registrants, $registration_timestamp, $registration_date, $registration_event, $registration_event_id, $as_program_name, $as_students_served, $as_based, $as_years_operating, $as_grade_level, $as_locale, $custom_question1_data, $custom_question2_data, $custom_opentext_question1, $custom_opentext_question2, $custom_opentext_afterschool1, $custom_opentext_afterschool2, 
    		$personal_unique_id, $org_unique_id2, $prefix, $firstname, $lastname, $title, $org_affiliation, $email, $phone, $prefer_contact_home, $home_address, $home_city, $home_state, $home_zip, $unit_cost, $personal_cost, $pay_type, $pay_rcvd_type, $pay_rcvd_date, $pay_rcvd_by, $pay_rcvd_notes, $special_accomodations, $cc_job_affiliation, $cc_job_affiliation_other, $cc_esc, $cc_role, $cc_previous, $cc_focus, $hotel_arrive, $hotel_depart, $hotel_bed, $hotel_smoking, $session_number, $inperson_online) = @arr;
			my $half_counter = $counter / 2;
			my $half_counter_rounded = &format_number($half_counter, "0","no");
			my $left_right = 'left';
 				$left_right = 'right' if ($half_counter eq $half_counter_rounded);
			if ($left_right eq 'left') {
print<<EOM;
<table style="width:90%;margin: 20px auto;" border="0" cellpadding="15" cellspacing="0">
<tr>
EOM
			} # END IF

print<<EOM;
<td style="width:45%;" valign="top">
	<strong>$firstname $lastname</strong><br>
EOM
print "$title<br>" if ($title ne '');
print "$org_affiliation<br>" if ($org_affiliation ne '');
print "$as_program_name<br>" if ($as_program_name ne '');
print "$address1<br>" if ($address1 ne '');
print "$address2<br>" if ($address2 ne '');
print "$city, $state $zip<br>" if ($city ne '');
print "Phone: $phone<br>" if ($phone ne '');
print "Fax: $fax<br>" if ($fax ne '');
print "Email: $email" if ($email ne '');
print<<EOM;
</td>
EOM

			if ($left_right eq 'right') {
				print "</tr>\n</table>\n\n";
			} # END IF
			
			$counter++;
		} # END WHILE LOOP

}
######################################################
## END: LOCATION = printable_attendee_details
######################################################


################################
## START: LOCATION = TO_DO_LIST
################################
if ($location eq 'to_do_list') {
print<<EOM;
<H2>Programmer's TO DO List</H2>
<P>
Although these features are not currently scheduled for implementation (any time soon), the programmer acknowledges this list of 
potential enhancements to this tool.
	<OL>
	<LI>COSTS: Allow event administrator to specify credit card payments are not allowed.  If so, hide CC payment options on reg. form. (BL 2-22-2005)
	<LI>EVENT ADMINISTRATION: Add the ability to copy an old event when starting a new event record. (BL 2-22-2005)
	<LI>EXTRA FIELDS: Allow event administrator to add more hotel data fields to form. (single room vs. double, sharing with whom, arrival date, departure date) (AS 2-23-2005)
	<LI>EXTRA FIELDS: Allow event administrator to specify whether a pre-event is also being registered for. (BL 2-22-2005)
	</OL>
<P>
If you have an enhancement to suggest or request, please send an email to Brian Litke at blitke\@sedl.org or call ext. 260.
EOM
}
################################
## END: LOCATION = TO_DO_LIST
################################


########################################
## START: LOCATION = PROCESS_DELETE_ORG
########################################
## START: CHECK IF USER IS AUTHORIZED
if ($location eq 'process_delete_org') {
	my $user_authorized = "";
	my $new_location = "";
	($user_authorized, $error_message) = &check_user_authorized($eventid, $cookie_ss_staff_id);
	if ($user_authorized ne 'yes') {
		$location = "show_orgs";
	}
}
## END: CHECK IF USER IS AUTHORIZED

if ($location eq 'process_delete_org') {
	if ($confirm_action ne 'yes') {
		$error_message = "The deletion <strong>WAS NOT</strong> processed.  You forgot to click the confirm delete checkbox.  Please try again.";
		$location = "show_orgs";
	} elsif ($show_org eq '') {
		$error_message = "The deletion <strong>WAS NOT</strong> processed.  The organization ID to delete was not passed properly.  Please notify Brian of this error.";
		$location = "show_orgs";
	} else {
		## DELETE CHILDREN
		my $command_delete_people = "DELETE FROM registration_people WHERE org_unique_id ='$show_org'";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command_delete_people) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#my $num_matches = $sth->rows;
		$feedback_message = "<P><FONT COLOR=RED>PEOPLE DELETE COMMAND: $command_delete_people</FONT>";
		## DELET PARENT ORG
		my $command_delete_org = "DELETE FROM registration_orgs WHERE org_unique_id ='$show_org'";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command_delete_org) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
#my $num_matches = $sth->rows;
		$feedback_message = "<P><FONT COLOR=RED>ORG DELETE COMMAND: $command_delete_org</FONT>";
		$feedback_message = "The deletion of this org was processed successfully.";
		$location = "show_orgs";
		$show_org = ""; # SHOW ALL ORGS FOR THIS EVENT WHEN WE GET BACK TO LIST
	}
}
########################################
## END: LOCATION = PROCESS_DELETE_ORG
########################################


################################
## START: LOCATION = EDIT_ORG
################################
if ($location =~ 'edit_org') {

	## LOOK UP THE INFO ALREADY ON FILE FOR THIS PERSON
my $org_unique_id = ""; my $unit_cost = ""; my $table_cost = ""; my $total_cost = ""; my $comp_code = ""; my $org = ""; my $org_department = ""; my $address1 = ""; my $address2 = ""; my $city = ""; my $state = ""; my $zip = ""; my $phone = ""; my $fax = ""; my $email = ""; my $special_needs = ""; my $pay_how = ""; my $number_registrants = ""; my $registration_timestamp = ""; my $registration_date = ""; my $registration_event = ""; my $registration_event_id = ""; my $as_program_name = ""; my $as_students_served = ""; my $as_based = ""; my $as_years_operating = ""; my $as_grade_level = ""; my $as_locale = ""; my $custom_question1_data = ""; my $custom_question2_data = ""; my $custom_opentext_question1 = ""; my $custom_opentext_question2 = ""; my $custom_opentext_afterschool1 = ""; my $custom_opentext_afterschool2 = "";

	my $command = "SELECT * from registration_orgs WHERE org_unique_id = '$show_org' AND registration_event_id LIKE '$eventid'";
		my $dbh = DBI->connect($dsn, "corpuser", "public");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches_people = $sth->rows;
		while (my @arr = $sth->fetchrow) {
    		($org_unique_id, $unit_cost, $table_cost, $total_cost, $comp_code, $org, $org_department, $address1, $address2, $city, $state, $zip, $phone, $fax, $email, $special_needs, $pay_how, $number_registrants, $registration_timestamp, $registration_date, $registration_event, $registration_event_id, $as_program_name, $as_students_served, $as_based, $as_years_operating, $as_grade_level, $as_locale, $custom_question1_data, $custom_question2_data, $custom_opentext_question1, $custom_opentext_question2, $custom_opentext_afterschool1, $custom_opentext_afterschool2) = @arr;
		}	
	
	
	## START: CHECK TO SEE ALL THE REQUIRED INFO IS PRESENT
	my $new_org = $query->param("new_org");
	my $new_org_department = $query->param("new_org_department");
	my $new_address1 = $query->param("new_address1");
	my $new_address2 = $query->param("new_address2");
	my $new_city = $query->param("new_city");
	my $new_state = $query->param("new_state");
	my $new_zip = $query->param("new_zip");
	my $new_phone = $query->param("new_phone");
	my $new_email = $query->param("new_email");
	my $new_as_program_name = $query->param("new_as_program_name");

	if (($location eq 'process_edit_org') && ($new_org eq ''))  {
		$location = "edit_org";
		$error_message = "You left out othe org name.  Please try again.";
	}
	if ($num_matches_people ne '1') {
		$location = "show_orgs";
		$error_message = "$command<P><FONT COLOR=RED>Error: The org ID was not located properly.  Contact webmaster\@sedl.org for assistance.</FONT><P>";
	}
	## START: CHECK TO SEE ALL THE REQUIRED INFO IS PRESENT


	if ($location eq 'process_edit_org') {
		$new_org = &commoncode::cleanthisfordb($new_org);
		$new_org_department = &commoncode::cleanthisfordb($new_org_department);
		$new_address1 = &commoncode::cleanthisfordb($new_address1);
		$new_address2 = &commoncode::cleanthisfordb($new_address2);
		$new_city = &commoncode::cleanthisfordb($new_city);
		$new_state = &commoncode::cleanthisfordb($new_state);
		$new_zip = &commoncode::cleanthisfordb($new_zip);
		$new_phone = &commoncode::cleanthisfordb($new_phone);
		$new_email = &commoncode::cleanthisfordb($new_email);
		$new_as_program_name = &commoncode::cleanthisfordb($new_as_program_name);

	my $command_update = "UPDATE registration_orgs 
					SET org = '$new_org', org_department = '$new_org_department', address1 = '$new_address1', address2 = '$new_address2', city = '$new_city', state = '$new_state', zip = '$new_zip', phone = '$new_phone', email = '$new_email', as_program_name = '$new_as_program_name'
					WHERE org_unique_id LIKE '$show_org'";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command_update) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;
		$location = "show_orgs";
		$feedback_message = "The organization's information was updated successfully. The <a href=\"#$show_org\">record you edited</a> is highlighed in yellow below.";
		$show_org = "";
	}

	if ($location eq 'edit_org') {


print<<EOM;
<H2>Edit Organization</H2>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
<p></p>
<FORM ACTION="registration-admin.cgi" METHOD="POST">
<TABLE>
<TR><TD VALIGN="TOP">Org</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_org" SIZE="50" VALUE="$org"></TD></TR>
<TR><TD VALIGN="TOP">Dept</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_org_department" SIZE="50" VALUE="$org_department"></TD></TR>
<TR><TD VALIGN="TOP">Address1</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_address1" SIZE="50" VALUE="$address1"></TD></TR>
<TR><TD VALIGN="TOP">Address2</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_address2" SIZE="50" VALUE="$address2"></TD></TR>
<TR><TD VALIGN="TOP">City</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_city" SIZE="30" VALUE="$city"></TD></TR>
<TR><TD VALIGN="TOP">State</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_state" SIZE="30" VALUE="$state"></TD></TR>
<TR><TD VALIGN="TOP">Zip</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_zip" SIZE="30" VALUE="$zip"></TD></TR>
<TR><TD VALIGN="TOP">Phone</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_phone" SIZE="30" VALUE="$phone"></TD></TR>
<TR><TD VALIGN="TOP">Email</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_email" SIZE="30" VALUE="$email"></TD></TR>
<TR><TD VALIGN="TOP">Afterschool Program Name</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_as_program_name" SIZE="30" VALUE="$as_program_name"></TD></TR>
<TR><TD VALIGN="TOP">Afterschool Program Name</TD>
	<TD VALIGN="TOP"><INPUT type="text" NAME="new_as_program_name" SIZE="30" VALUE="$as_program_name"></TD></TR>
</TABLE>

  <input type="HIDDEN" name="show_year" value="$show_year">
  <INPUT TYPE="HIDDEN" NAME="show_org" VALUE="$show_org">
  <INPUT TYPE="HIDDEN" NAME="eventid" VALUE="$eventid">
  <INPUT TYPE="HIDDEN" NAME="location" VALUE="process_edit_org">
  <INPUT TYPE="SUBMIT" VALUE="Edit this org's (ORG ID: $show_org) registration">
  </FORM>
EOM
	}
}
################################
## END: LOCATION = EDIT_ORG
################################




################################
## START: LOCATION = SHOW_ORGS
################################
if ($location eq 'show_orgs') {
	#################################
	## START: LOAD RELATED RECEIPTS
	#################################
	my %receipt_on_file; # THIS HASH WILL REMEMBER WHICH PEOPLE HAVE A RECEIPT ON FILE
	my $command_get_receipts = "select unique_id, receipt_for_registered_person_id from receipts where receipt_for_registered_person_id not like ''";
	my $dbh = DBI->connect($dsn_intranet, "intranetuser", "limited");
	my $sth = $dbh->prepare($command_get_receipts) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	while (my @arr = $sth->fetchrow) {
		my ($unique_id, $receipt_for_registered_person_id) = @arr;
		$receipt_on_file{$receipt_for_registered_person_id} = "yes";
	} # END DB QUERY LOOP
	#################################
	## END: LOAD RELATED RECEIPTS
	#################################


	my $command = "SELECT registration_orgs.*, registration_people.* 
		from registration_orgs, registration_people 
		WHERE registration_orgs.org_unique_id = registration_people.org_unique_id";
		$command .= " AND registration_orgs.org_unique_id LIKE '$show_org'" if ($show_org ne '');
		$command .= " AND registration_orgs.registration_event_id LIKE '$eventid'";
#		$command .= " order by registration_people.lastname, registration_people.firstname ";
		$command .= " order by registration_orgs.org, registration_orgs.registration_timestamp DESC" if (($sortby eq '') || ($sortby eq 'organization'));
		$command .= " order by registration_orgs.state, registration_orgs.org" if ($sortby eq 'state');
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches_orgs = $sth->rows;
#print "<p class=\"info\">$command<br><br>MATCHES: $num_matches_orgs orgs.</p>";
print<<EOM;
<H2>Registered Organizations</H2>
EOM

print "<p class=\"alert\">$error_message</p>" if $error_message ne '';
print "<p class=\"info\">$feedback_message</p>" if $feedback_message ne '';

print<<EOM;      
You are viewing a list of <strong>ORGANIZATIONS ($num_matches_orgs PEOPLE)</strong> signed up for:<br>
<strong><FONT COLOR="PURPLE">$this_event_name <em>$this_event_name_italics</em></FONT></strong> on $this_event_date_label in $this_event_location.
<P>
WANT TO VIEW THE PEOPLE?: Click here to see a list of <A HREF="registration-admin.cgi?location=show_people&amp;eventid=$eventid&amp;show_year=$show_year">registered people</A>.
</p>
<p class="info">
WANT THE RAW DATA?<br>
Click here for a <A HREF="registrationdata/registration-data-event$eventid.xls">tab-delimited registration data file</A> that you can save to your desktop and open in MS Excel or another application.  After following the link, select "FILE" ==> "SAVE AS" in your browser to save the file to your desktop.
</p>
<P>
<form action="registration-admin.cgi" method="POST">
<label for="sortby">Sort records by</label> : <select name="sortby" id="sortby">
EOM
my $counter = "0";
my @options = ('organization', 'state');
	while ($counter <= $#options) {
		print "<OPTION VALUE=\"$options[$counter]\"";
		if ($sortby eq $options[$counter]) {
			print " SELECTED";
		}
		print ">$options[$counter]";
		$counter++;
	} # END WHILE

print<<EOM;
		</select>
	<input type="HIDDEN" name="show_year" value="$show_year">
	<input type="hidden" name="location" value="show_orgs">
	<input type="hidden" name="eventid" value="$eventid">
	<input type="submit" name="submit" value="refresh page">
	</form>

		
EOM


	if ($show_org ne '') {
	print "<P>You are viewing information for a single organization.  
	Click here to view <A HREF=\"registration-admin.cgi?location=show_orgs&amp;show_year=$show_year\">registrations from 
	all organizations</A>.<P>";
	}
print<<EOM;
<TABLE BORDER="1" CELLPADDING="2" CELLSPACING="0" style="background-color:#ffffff;">
<TR><TD bgcolor="#EBEBEB">Organization Information</TD>
	<TD bgcolor="#EBEBEB">Special Needs</TD>
	<TD bgcolor="#EBEBEB">Delete this org and associates registered people?</TD>
	<TD bgcolor="#EBEBEB">Number Registrants</TD>
</TR>
EOM

	my $counter = "1";
	my $last_org_id = "";
	my $last_org_name = "";
	my $counter_this_org = "";

	## START: WRITE THE SURVEY RESULTS TO A FILE
	open(DATA,">/home/httpd/html/staff/communications/registrationdata/registration-data-event$eventid.xls");

	## START: PRINT HEADER ROW FOR THE DATA FILE
	print DATA "prefix\tfirstname\tlastname\ttitle\temail\tphone";
		if ($this_event_prompt_address_home eq 'yes') {
			print DATA "\tprefer_contact_home\thome_address\thome_city\thome_state\thome_zip";
		}

		print DATA "\treg_timestamp";
		#\treg_date\tuser_id\torg_unique_id2
		if ($this_event_custom_question1_text ne '') {
			print DATA "\t$this_event_custom_question1_text";
		}
		if ($this_event_custom_question2_text ne '') {
			print DATA "\t$this_event_custom_question2_text";
		}
		if ($this_event_custom_opentext_question1 ne '') {
			print DATA "\t$this_event_custom_opentext_question1";
		}
		if ($this_event_custom_opentext_question2 ne '') {
			print DATA "\t$this_event_custom_opentext_question2";
		}
	
		if ($this_event_cost_free ne 'yes') {
			print DATA "\tunit_cost\tpersonal_cost\tpay_type\tpay_rcvd_type\tpay_rcvd_date\tpay_rcvd_by\tpay_rcvd_notes";
		}
		if ($this_event_prompt_inperson_online eq 'yes') {
			print DATA "\tinperson_online";
		}
		if ($this_event_prompt_session_choice eq 'yes') {
			print DATA "\tsession_number";
		}
		if ($this_event_prompt_data_hotel eq 'yes') {
			print DATA "\thotel_arrive\thotel_depart\thotel_bed\thotel_smoking";
		}
		if ($this_event_prompt_data_cc_role eq 'yes') {
			print DATA "\tcc_role";
		}
		if ($this_event_prompt_data_cc_previous ne '') {
			print DATA "\tcc_previous";
		}
		if ($this_event_prompt_data_cc_affiliation eq 'yes') {
			print DATA "\tcc_job_affiliation\tcc_job_affiliation_other";
		}
		if ($this_event_prompt_data_cc_focus eq 'yes') {
			print DATA "\tcc_focus_area";
		}
		if ($this_event_prompt_data_cc_esc eq 'yes') {
			print DATA "\tcc_esc";
		}
		if ($this_event_prompt_data_afterschool eq 'yes') {
			print DATA "\tas_program_name\tas_students_served\tas_based\tas_years_operating\tas_grade_level\tas_locale";
			if ($this_event_custom_opentext_afterschool1 ne '') {
				print DATA "\t$this_event_custom_opentext_afterschool1";
			}
			if ($this_event_custom_opentext_afterschool2 ne '') {
				print DATA "\t$this_event_custom_opentext_afterschool2";
			}
		}
	print DATA "\tspecial_accomodations\torg_unique_id";

	if ($this_event_cost_free ne 'yes') {
		print DATA "\tunit_cost\ttable_cost\ttotal_cost\tcomp_code";
	}

	print DATA "\torg\tdepartment\taddress1\taddress2\tcity\tstate\tzip\tphone\tfax\temail\tspecial_needs\tpay_how\tnumber_registrants\tregistration_event\n";
	#\tregistration_event_id
	## END: PRINT HEADER ROW FOR THE DATA FILE

my @registrants;
   $registrants[0] = "trash";

my $org_unique_id = "";
	my $unit_cost = "";
	my $table_cost = "";
	my $total_cost = "";
	my $comp_code = "";
	my $org = "";
	my $org_department = "";
	my $address1 = "";
	my $address2 = "";
	my $city = "";
	my $state = "";
	my $zip = "";
	my $org_phone = "";
	my $fax = "";
	my $org_email = "";
	my $special_needs = "";
	my $pay_how = "";
	my $number_registrants = "";
	my $registration_timestamp = "";
	my $registration_date = "";
	my $registration_event = "";
	my $registration_event_id = "";
	my $as_program_name = "";
	my $as_students_served = "";
	my $as_based = "";
	my $as_years_operating = "";
	my $as_grade_level = "";
	my $as_locale = "";
	my $custom_question1_data = "";
	my $custom_question2_data = "";
	my $custom_opentext_question1 = "";
	my $custom_opentext_question2 = "";
	my $custom_opentext_afterschool1 = "";
	my $custom_opentext_afterschool2 = "";
	my $personal_unique_id = "";
	my $org_unique_id2 = "";
	my $prefix = "";
	my $firstname = "";
	my $lastname = "";
	my $title = "";
	my $org_affiliation = "";
	my $email = "";
	my $phone = "";
	my $prefer_contact_home = "";
	my $home_address = "";
	my $home_city = "";
	my $home_state = "";
	my $home_zip = "";
	my $unit_cost = "";
	my $personal_cost = "";
	my $pay_type = "";
	my $pay_rcvd_type = "";
	my $pay_rcvd_date = "";
	my $pay_rcvd_by = "";
	my $pay_rcvd_notes = "";
	my $special_accomodations = "";
	my $cc_job_affiliation = "";
	my $cc_job_affiliation_other = "";
	my $cc_esc = "";
	my $cc_role = "";
	my $cc_previous = "";
	my $cc_focus = "";
	my $hotel_arrive = "";
	my $hotel_depart = "";
	my $hotel_bed = "";
	my $hotel_smoking = "";
	my $session_number = "";
	my $inperson_online = "";

	my $last_this_event_name = "";
	my $last_firstname = "";
	my $last_lastname = "";
	my $last_org = "";
	my $last_address1 = "";
	my $last_address2 = "";
	my $last_city = "";
	my $last_state = "";
	my $last_zip = "";
	my $last_org_phone = "";
	my $last_unit_cost = "";
	my $last_total_cost = "";
	my $last_pay_how = "";
	my $last_payment_name = "";

		while (my @arr = $sth->fetchrow) {
    		($org_unique_id, $unit_cost, $table_cost, $total_cost, $comp_code, $org, $org_department, $address1, $address2, $city, $state, $zip, $org_phone, $fax, $org_email, $special_needs, $pay_how, $number_registrants, $registration_timestamp, $registration_date, $registration_event, $registration_event_id, $as_program_name, $as_students_served, $as_based, $as_years_operating, $as_grade_level, $as_locale, $custom_question1_data, $custom_question2_data, $custom_opentext_question1, $custom_opentext_question2, $custom_opentext_afterschool1, $custom_opentext_afterschool2,
    			$personal_unique_id, $org_unique_id2, $prefix, $firstname, $lastname, $title, $org_affiliation, $email, $phone, $prefer_contact_home, $home_address, $home_city, $home_state, $home_zip, $unit_cost, $personal_cost, $pay_type, $pay_rcvd_type, $pay_rcvd_date, $pay_rcvd_by, $pay_rcvd_notes, $special_accomodations, $cc_job_affiliation, $cc_job_affiliation_other, $cc_esc, $cc_role, $cc_previous, $cc_focus, $hotel_arrive, $hotel_depart, $hotel_bed, $hotel_smoking, $session_number, $inperson_online) = @arr;

				# MAKE TIMESTAMP READABLE
				$registration_timestamp = &commoncode::convert_timestamp_2pretty_w_date($registration_timestamp, "yes");
				print DATA "$prefix\t$firstname\t$lastname\t$title\t$email\t$phone";
				if ($this_event_prompt_address_home eq 'yes') {
					print DATA "\t$prefer_contact_home\t$home_address\t$home_city\t$home_state\t$home_zip";
				}
				print DATA "\t$registration_timestamp";
				#\t$registration_date\t$personal_unique_id\t$org_unique_id2
				
				if ($this_event_custom_question1_text ne '') {
					print DATA "\t$custom_question1_data";
				}
				if ($this_event_custom_question2_text ne '') {
					print DATA "\t$custom_question2_data";
				}
				if ($this_event_custom_opentext_question1 ne '') {
					print DATA "\t$custom_opentext_question1";
				}
				if ($this_event_custom_opentext_question2 ne '') {
					print DATA "\t$custom_opentext_question2";
				}

				if ($this_event_cost_free ne 'yes') {
					print DATA "\t$unit_cost\t$personal_cost\t$pay_type\t$pay_rcvd_type\t$pay_rcvd_date\t$pay_rcvd_by\t$pay_rcvd_notes";
				}
				if ($this_event_prompt_inperson_online eq 'yes') {
					print DATA "\t$inperson_online";
				}
				if ($this_event_prompt_session_choice eq 'yes') {
					print DATA "\t$session_number";
				}
				if ($this_event_prompt_data_hotel eq 'yes') {
					print DATA "\t$hotel_arrive\t$hotel_depart\t$hotel_bed\t$hotel_smoking";
				}
				if ($this_event_prompt_data_cc_role eq 'yes') {
					print DATA "\t$cc_role";
				}
				if ($this_event_prompt_data_cc_previous ne '') {
					print DATA "\t$cc_previous";
				}
				if ($this_event_prompt_data_cc_affiliation eq 'yes') {
					print DATA "\t$cc_job_affiliation\t$cc_job_affiliation_other";
				}
				if ($this_event_prompt_data_cc_focus eq 'yes') {
					print DATA "\t$cc_focus";
				}
				if ($this_event_prompt_data_cc_esc eq 'yes') {
					print DATA "\t$cc_esc";
				}
				if ($this_event_prompt_data_afterschool eq 'yes') {
					print DATA "\t$as_program_name\t$as_students_served\t$as_based\t$as_years_operating\t$as_grade_level\t$as_locale";
					if ($this_event_custom_opentext_afterschool1 ne '') {
						print DATA "\t$custom_opentext_afterschool1";
					}
					if ($this_event_custom_opentext_afterschool2 ne '') {
						print DATA "\t$custom_opentext_afterschool2";
					}
				}
				
				print DATA "\t$special_accomodations\t$org_unique_id";
				
				if ($this_event_cost_free ne 'yes') {
					print DATA "\t$unit_cost\t$table_cost\t$total_cost\t$comp_code";
				}
				print DATA "\t$org\t$org_department\t$address1\t$address2\t$city\t$state\t$zip\t$org_phone\t$fax\t$org_email\t$special_needs\t$pay_how\t$number_registrants\t$registration_event\n";
				#\t$registration_event_id

				####################################
				## START: HANDLE CHANGE TO NEW ORG
				####################################
				if ($org_unique_id ne $last_org_id) {
					if ($last_org_id ne '') {
						if ($receipt_on_file{$last_org_id} eq 'yes') {
print<<EOM;
		<br><br>
		<FORM ACTION="receipt_manager.cgi" METHOD="POST">
 		<input type="HIDDEN" name="show_year" value="$show_year">
		<INPUT TYPE="HIDDEN" NAME="show_registration_id" VALUE="$last_org_id">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="print_receipt">
		<INPUT TYPE="SUBMIT" VALUE="View Receipt">
		</FORM>
EOM
						} else {
							$this_event_name =~ s/"//gi;
print<<EOM;
		<br><br>
		<FORM ACTION="receipt_manager.cgi" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="new_event_name" VALUE="$last_this_event_name">
		<INPUT TYPE="HIDDEN" NAME="new_name_first" VALUE="$last_firstname">
		<INPUT TYPE="HIDDEN" NAME="new_name_last" VALUE="$last_lastname">
		<INPUT TYPE="HIDDEN" NAME="new_organization" VALUE="$last_org">
		<INPUT TYPE="HIDDEN" NAME="new_address" VALUE="$last_address1">
		<INPUT TYPE="HIDDEN" NAME="new_address2" VALUE="$last_address2">
		<INPUT TYPE="HIDDEN" NAME="new_city" VALUE="$last_city">
		<INPUT TYPE="HIDDEN" NAME="new_state" VALUE="$last_state">
		<INPUT TYPE="HIDDEN" NAME="new_zip" VALUE="$last_zip">
		<INPUT TYPE="HIDDEN" NAME="new_country" VALUE="">
		<INPUT TYPE="HIDDEN" NAME="new_phone" VALUE="$last_org_phone">
		<INPUT TYPE="HIDDEN" NAME="new_attendee1" VALUE="$registrants[1]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee2" VALUE="$registrants[2]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee3" VALUE="$registrants[3]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee4" VALUE="$registrants[4]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee5" VALUE="$registrants[5]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee6" VALUE="$registrants[6]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee7" VALUE="$registrants[7]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee8" VALUE="$registrants[8]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee9" VALUE="$registrants[9]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee10" VALUE="$registrants[10]">
		<INPUT TYPE="HIDDEN" NAME="new_cost" VALUE="$last_unit_cost">
		<INPUT TYPE="HIDDEN" NAME="new_total_cost" VALUE="$last_total_cost">
		<INPUT TYPE="HIDDEN" NAME="new_payment_method" VALUE="$last_pay_how">
		<INPUT TYPE="HIDDEN" NAME="new_payment_number" VALUE="">
		<INPUT TYPE="HIDDEN" NAME="new_payment_name" VALUE="$last_payment_name">
		<INPUT TYPE="HIDDEN" NAME="new_balance_owed" VALUE="">

		<INPUT TYPE="HIDDEN" NAME="new_receipt_for_registered_person_id" VALUE="$last_org_id">

 		<input type="HIDDEN" name="show_year" value="$show_year">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="add_item">
		<!INPUT TYPE="SUBMIT" VALUE="Send Data to Receipt">
		</FORM>
EOM
						}
   				$registrants[1] = "";
   				$registrants[2] = "";
   				$registrants[3] = "";
   				$registrants[4] = "";
   				$registrants[5] = "";
   				$registrants[6] = "";
   				$registrants[7] = "";
   				$registrants[8] = "";
   				$registrants[9] = "";
   				$registrants[10] = "";
				$last_payment_name = "";
					}
			
					$counter_this_org = "1";
					print "</TD></TR>" if ($last_org_id ne '');

					## PRINT ORG LINE
					$registration_date = &commoncode::date2standard($registration_date);

					my $people_label = "people";
						$people_label = "person" if ($number_registrants eq '1');
					print "<TR><TD>";

					if (($last_org_name eq $org) && ($org ne '')) {
						print "<FONT COLOR=RED>WARNING: Potential duplicate organization.  If this is a duplicate, the last saved entry 
							(the good one) should be listed first</FONT><br>";
					}

if ($comp_code ne '') {
	$pay_how = "$pay_how\: $comp_code";
	$unit_cost = "0";
}
my $highlight_style = "";
   $highlight_style = "background-color:#ffffcc;" if ($org_unique_id eq $show_org_remember_last_request);
print<<EOM;
	<a name="$org_unique_id"></a>
	<TABLE BORDER="1" CELLPADDING="1" CELLSPACING="0" style="margin-bottom:20px;$highlight_style">
	<TR><TD VALIGN="TOP"><strong>Org. Name:</strong><br>
		<span style="font-size:9px;">(ID: $org_unique_id)</span></TD>
		<TD VALIGN="TOP"> <FONT COLOR=GREEN><strong>$org</strong></FONT>
			<FORM ACTION="registration-admin.cgi" METHOD="GET">
			<input type="HIDDEN" name="show_year" value="$show_year">
			<INPUT TYPE="HIDDEN" NAME="show_org" VALUE="$org_unique_id">
			<INPUT TYPE="HIDDEN" NAME="eventid" VALUE="$eventid">
			<INPUT TYPE="HIDDEN" NAME="location" VALUE="edit_org">
			<INPUT TYPE="SUBMIT" VALUE="Edit org data">
			</FORM>
		</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Department:</strong></TD>
		<TD VALIGN="TOP">$org_department</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Address:</strong></TD><TD VALIGN="TOP"> $address1<br>$address2</TD></TR>
	<TR><TD VALIGN="TOP"><strong>City:</strong></TD><TD VALIGN="TOP"> $city, $state	$zip</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Phone:</strong></TD><TD VALIGN="TOP"> $org_phone</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Fax:</strong></TD><TD VALIGN="TOP"> $fax</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Email:</strong></TD><TD VALIGN="TOP"> $org_email</TD></TR>
EOM
					if ($this_event_custom_question1_text ne '') {
						print "<TR><TD VALIGN=\"TOP\"><strong>$this_event_custom_question1_text\:</strong></TD><TD VALIGN=\"TOP\"> $custom_question1_data</TD></TR>\n";
					}
					if ($this_event_custom_question2_text ne '') {
						print "<TR><TD VALIGN=\"TOP\"><strong>$this_event_custom_question2_text\:</strong></TD><TD VALIGN=\"TOP\"> $custom_question2_data</TD></TR>\n";
					}

					if ($this_event_custom_opentext_question1 ne '') {
						print "<TR><TD VALIGN=\"TOP\"><strong>$this_event_custom_opentext_question1\:</strong></TD><TD VALIGN=\"TOP\"> $custom_opentext_question1</TD></TR>\n";
					}
					if ($this_event_custom_opentext_question2 ne '') {
						print "<TR><TD VALIGN=\"TOP\"><strong>$this_event_custom_opentext_question2\:</strong></TD><TD VALIGN=\"TOP\"> $custom_opentext_question2</TD></TR>\n";
					}

					if ($this_event_prompt_inperson_online eq 'yes') {
print<<EOM;
	<TR><TD VALIGN="TOP"><strong>In person or Online:</strong></TD><TD VALIGN="TOP"> $inperson_online</TD></TR>
EOM
					}
					if ($this_event_prompt_data_afterschool eq 'yes') {
print<<EOM;
	<TR><TD VALIGN="TOP"><strong>Afterschool Program Name:</strong></TD><TD VALIGN="TOP"> $as_program_name</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Number Students Served:</strong></TD><TD VALIGN="TOP"> $as_students_served</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Program Administered:</strong></TD><TD VALIGN="TOP"> $as_based</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Years Operating:</strong></TD><TD VALIGN="TOP"> $as_years_operating</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Grade Level:</strong></TD><TD VALIGN="TOP"> $as_grade_level</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Locale:</strong></TD><TD VALIGN="TOP"> $as_locale</TD></TR>
EOM
					if ($this_event_custom_opentext_afterschool1 ne '') {
						print "<TR><TD VALIGN=\"TOP\"><strong>$this_event_custom_opentext_afterschool1\:</strong></TD><TD VALIGN=\"TOP\"> $custom_opentext_afterschool1</TD></TR>\n";
					}
					if ($this_event_custom_opentext_afterschool2 ne '') {
						print "<TR><TD VALIGN=\"TOP\"><strong>$this_event_custom_opentext_afterschool2\:</strong></TD><TD VALIGN=\"TOP\"> $custom_opentext_afterschool2</TD></TR>\n";
					}

						# $as_program_name, $as_students_served, $as_based, $as_years_operating, $as_grade_level, $as_locale 
					}
					if ($this_event_cost_free ne 'yes') {

if ($comp_code ne '') {
print<<EOM;
	<TR><TD VALIGN="TOP"><strong>Comp code:</strong></TD><TD VALIGN="TOP">$comp_code</TD></TR>
EOM
}

if ($table_cost != 0) {
print<<EOM;
	<TR><TD VALIGN="TOP"><strong>Price/person:</strong></TD><TD VALIGN="TOP">covered by table cost</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Table cost:</strong></TD><TD VALIGN="TOP">\$$table_cost</TD></TR>
EOM
} else {
print<<EOM;
	<TR><TD VALIGN="TOP"><strong>Price/person:</strong></TD><TD VALIGN="TOP"> \$$unit_cost</TD></TR>
EOM
}
print<<EOM;
	<TR><TD VALIGN="TOP"><strong>Total Cost:</strong></TD><TD VALIGN="TOP"> \$$total_cost</TD></TR>
	<TR><TD VALIGN="TOP"><strong>Pymnt Type:</strong></TD><TD VALIGN="TOP">$pay_how</TD></TR>
EOM
					}

print<<EOM;
	</TABLE>
	</TD>
	<TD VALIGN="TOP">$special_needs</TD>
	<TD VALIGN="TOP">REGISTRATION TIMESTAMP: $registration_timestamp<br>
		<FORM ACTION="registration-admin.cgi" METHOD="POST">
		<INPUT TYPE="CHECKBOX" NAME="confirm_action" VALUE="yes"> Click here to confirm deletion<P>
 		<input type="HIDDEN" name="show_year" value="$show_year">
		<INPUT TYPE="HIDDEN" NAME="show_org" VALUE="$org_unique_id">
		<INPUT TYPE="HIDDEN" NAME="eventid" VALUE="$eventid">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="process_delete_org">
		<INPUT TYPE="SUBMIT" VALUE="Delete this Registration">
		</FORM>

	</TD>
	<TD VALIGN="TOP" NOWRAP>
		<div style="text-align:center;font-size:10px;">Registered 
		$number_registrants $people_label<br>
		on $registration_date<br>
		</div>
		<p class="small">1) $prefix $firstname $lastname ($pay_type)
EOM
#		(click to <a href="registration-admin.cgi?location=show_people&amp;show_org=$org_unique_id&amp;eventid=$eventid&amp;show_year=$show_year">edit payment &amp; reg. details</a>)
if ($pay_rcvd_type ne '') {
	$pay_rcvd_date = &commoncode::date2standard($pay_rcvd_date);
print<<EOM;
<div style="border: 1px solid #006600;background-color:#99ff99;padding:3px;">
PAYMENT RECEIVED<br>
- Payment type: $pay_rcvd_type<br>
- Date: $pay_rcvd_date by $pay_rcvd_by
EOM
print "<br>- Notes: $pay_rcvd_notes" if ($pay_rcvd_notes ne '');
print<<EOM;
</div>
EOM
}
				####################################
				## END: HANDLE CHANGE TO NEW ORG
				####################################
				} else {
					print "<br>$counter_this_org\) $prefix $firstname $lastname ($pay_type)";
				}
			$last_this_event_name = $this_event_name;
			$last_firstname = $firstname;
			$last_lastname = $lastname;
			$last_org = $org;
			$last_address1 = $address1;
			$last_address2 = $address2;
			$last_city = $city;
			$last_state = $state;
			$last_zip = $zip;
			$last_org_phone = $org_phone;
			$last_unit_cost = $unit_cost;
			$last_total_cost = $total_cost;
			$last_pay_how = $pay_how;
			$last_payment_name = "$firstname $lastname" if ($last_payment_name eq '');

			$last_org_name = $org;
			$last_org_id = $org_unique_id;
			$counter++;
			$registrants[$counter_this_org] = "$firstname $lastname";
			$counter_this_org++;

		} # END WHILE LOOP

if ($receipt_on_file{$last_org_id} eq 'yes') {
print<<EOM;
		<br><br>
		<FORM ACTION="receipt_manager.cgi" METHOD="POST">
 		<input type="HIDDEN" name="show_year" value="$show_year">
		<INPUT TYPE="HIDDEN" NAME="show_registration_id" VALUE="$last_org_id">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="print_receipt">
		<INPUT TYPE="SUBMIT" VALUE="View Receipt">
		</FORM>
EOM
} else {
	$this_event_name =~ s/"//gi;
print<<EOM;
		<br><br>
		<FORM ACTION="receipt_manager.cgi" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="new_event_name" VALUE="$last_this_event_name">
		<INPUT TYPE="HIDDEN" NAME="new_name_first" VALUE="$last_firstname">
		<INPUT TYPE="HIDDEN" NAME="new_name_last" VALUE="$last_lastname">
		<INPUT TYPE="HIDDEN" NAME="new_organization" VALUE="$last_org">
		<INPUT TYPE="HIDDEN" NAME="new_address" VALUE="$last_address1">
		<INPUT TYPE="HIDDEN" NAME="new_address2" VALUE="$last_address2">
		<INPUT TYPE="HIDDEN" NAME="new_city" VALUE="$last_city">
		<INPUT TYPE="HIDDEN" NAME="new_state" VALUE="$last_state">
		<INPUT TYPE="HIDDEN" NAME="new_zip" VALUE="$last_zip">
		<INPUT TYPE="HIDDEN" NAME="new_country" VALUE="">
		<INPUT TYPE="HIDDEN" NAME="new_phone" VALUE="$last_org_phone">
		<INPUT TYPE="HIDDEN" NAME="new_attendee1" VALUE="$registrants[1]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee2" VALUE="$registrants[2]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee3" VALUE="$registrants[3]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee4" VALUE="$registrants[4]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee5" VALUE="$registrants[5]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee6" VALUE="$registrants[6]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee7" VALUE="$registrants[7]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee8" VALUE="$registrants[8]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee9" VALUE="$registrants[9]">
		<INPUT TYPE="HIDDEN" NAME="new_attendee10" VALUE="$registrants[10]">
		<INPUT TYPE="HIDDEN" NAME="new_cost" VALUE="$last_unit_cost">
		<INPUT TYPE="HIDDEN" NAME="new_total_cost" VALUE="$last_total_cost">
		<INPUT TYPE="HIDDEN" NAME="new_payment_method" VALUE="$last_pay_how">
		<INPUT TYPE="HIDDEN" NAME="new_payment_number" VALUE="">
		<INPUT TYPE="HIDDEN" NAME="new_payment_name" VALUE="$last_payment_name">
		<INPUT TYPE="HIDDEN" NAME="new_balance_owed" VALUE="">

		<INPUT TYPE="HIDDEN" NAME="new_receipt_for_registered_person_id" VALUE="$last_org_id">

 		<input type="HIDDEN" name="show_year" value="$show_year">
		<INPUT TYPE="HIDDEN" NAME="location" VALUE="add_item">
		<!INPUT TYPE="SUBMIT" VALUE="Send Data to Receipt">
		</FORM>
EOM
}

print "</TD></TR>";

close(DATA);
#print XML_DATA<<EOM;
#</registrations>
#EOM
#close (XML_DATA);

print<<EOM;
</TABLE>
<p>
Click here for the <A HREF="registrationdata/registration-data-event$eventid.xls">tab-delimited registration data file</A>.
</p>
EOM

#Click here for the <A HREF="/afterschool/registration_data.xml">XML registration data file</A>.
}
################################
## END: LOCATION = SHOW_ORGS
################################


################################
## PAGE FOOTER
################################
if ($location !~ 'printable_') {
print<<EOM;
<p style="text-align:right;">
<a href="registration-admin.cgi?location=to_do_list&amp;show_year=$show_year"><span style="color:#CCCCCC;">Programmer's TO DO List</span></a>
</p>
$htmltail
EOM
} else {
print<<EOM;
</body>
</html>
EOM
}







####################################################################
##  HERE ARE SOME SUBROUTINES USED BY THIS DATABASE SEARCH SCRIPT ##
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



#######################################################################
## START SUBROUTINE: CHECK IF THIS USER IS ALLOWED TO UPDATE THIS ITEM 
#######################################################################
sub check_user_authorized {
	my $this_event = $_[0];
	my $this_userid = $_[1];

	my $this_error_message = "";
	my $this_authorized = "no";
	my $this_location = "";

	my $command = "SELECT event_created_by, event_editable_by 
					from registration_events WHERE event_id like '$this_event'";
	my $dbh = DBI->connect($dsn, "corpuser", "public");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
#my $num_matches = $sth->rows;

		while (my @arr = $sth->fetchrow) {
	   		my ($event_created_by, $event_editable_by) = @arr;
				## COMPARE ID TO THIS USER'S ID, AND IF APPROPRIATE, DISALLOW EDIT AND PROVIDE FEEDBACK
				if (($this_userid eq $event_created_by) || ($event_editable_by =~ $this_userid)) {
#						$this_error_message .= "It looks like you ($this_userid) are eligible to edit this event created by ($event_created_by) and also editable by ($event_editable_by).";
						$this_authorized = "yes";
				} else {
					if ($cookie_ss_staff_id ne 'blitke') {
						$this_error_message .= "You did not create this event record.  This event and it's registrations can only be edited by its creator ($event_created_by), and therefore, your edit was not processed.";
						$this_location = "show_events";
					} else {
						$this_authorized = "yes"; # ALLOW BRIAN TO EDIT ANYTHING
					}
				}
	   	} # END DB QUERY LOOP
	return ($this_authorized, $this_error_message);
}
#######################################################################
## END SUBROUTINE: CHECK IF THIS USER IS ALLOWED TO UPDATE THIS ITEM 
#######################################################################


#######################################################################
## START SUBROUTINE: DEDUCE YEAR FROM PRETTY DATE FORMAT
#######################################################################
sub deduce_year {
	my $this_event_date = $_[0];
	my $deduced_year = "";
	   $deduced_year = "2005" if ($this_event_date =~ '2005');
	   $deduced_year = "2006" if ($this_event_date =~ '2006');
	   $deduced_year = "2007" if ($this_event_date =~ '2007');
	   $deduced_year = "2008" if ($this_event_date =~ '2008');
	   $deduced_year = "2009" if ($this_event_date =~ '2009');
	   $deduced_year = "2010" if ($this_event_date =~ '2010');
	   $deduced_year = "2011" if ($this_event_date =~ '2011');
	   $deduced_year = "2012" if ($this_event_date =~ '2012');
	   $deduced_year = "2013" if ($this_event_date =~ '2013');
	   $deduced_year = "2014" if ($this_event_date =~ '2014');
	   $deduced_year = "2015" if ($this_event_date =~ '2015');
	return ($deduced_year);
}
#######################################################################
## END SUBROUTINE: DEDUCE YEAR FROM PRETTY DATE FORMAT
#######################################################################


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

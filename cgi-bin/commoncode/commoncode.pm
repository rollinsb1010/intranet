package commoncode;

##################################################
## START: SUBROUTINE printform_date
##################################################
sub printform_date {
	my $form_variable_name_y = $_[0];
	my $form_variable_name_m = $_[1];
	my $form_variable_name_d = $_[2];
	my $selected_fulldate = $_[3];
	   $selected_fulldate =~ s/\//-/gi; # translate slashes to dashes
	my ($sel_y, $sel_m, $sel_d) = split(/-/,$selected_fulldate);
	&print_month_menu($form_variable_name_m, $sel_m);
	&print_day_menu($form_variable_name_d, $sel_d);
	&print_year_menu($form_variable_name_y, "2010", "2013", $sel_y);
}
##################################################
## END: SUBROUTINE printform_date
##################################################


######################################
## START: SUBROUTINE print_day_menu
######################################
sub print_day_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];

	my @days_value = ("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31");
	my $day_counter = "0";
	my $count_total_days = $#days_value;
print<<EOM;
<SELECT NAME="$field_name">
<OPTION VALUE=\"\">day</OPTION>
EOM
		while ($day_counter <= $count_total_days) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $days_value[$day_counter]);
			print "<OPTION VALUE=\"$days_value[$day_counter]\" $selected>$days_value[$day_counter]</OPTION>\n";
			$day_counter++;
		} # END WHILE
	print "</SELECT>\n";
######################################
} # END: SUBROUTINE print_day_menu
######################################


######################################
## START: SUBROUTINE print_month_menu
######################################
sub print_month_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @months_value = ("", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	my @months_label = ("month", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	my $month_counter = "0";
	my $count_total_months = $#months_value;
print<<EOM;
<select name="$field_name" id="$field_name">
EOM
		while ($month_counter <= $count_total_months) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $months_value[$month_counter]);
			print "<option VALUE=\"$months_value[$month_counter]\" $selected>$months_label[$month_counter]</option>\n";
			$month_counter++;
		} # END WHILE
	print "</select>\n";
######################################
} # END: SUBROUTINE print_month_menu
######################################

######################################
## START: SUBROUTINE getFullMonthName
######################################
sub getFullMonthName {
	my $month_value = $_[0];
	   $month_value = "January" if ($month_value == 1);
	   $month_value = "February" if ($month_value == 2);
	   $month_value = "March" if ($month_value == 3);
	   $month_value = "April" if ($month_value == 4);
	   $month_value = "May" if ($month_value == 5);
	   $month_value = "June" if ($month_value == 6);
	   $month_value = "July" if ($month_value == 7);
	   $month_value = "August" if ($month_value == 8);
	   $month_value = "September" if ($month_value == 9);
	   $month_value = "October" if ($month_value == 10);
	   $month_value = "November" if ($month_value == 11);
	   $month_value = "December" if ($month_value == 12);
	return($month_value);
}
######################################
## END: SUBROUTINE getFullMonthName
######################################

######################################
## START: SUBROUTINE getShortMonthName
######################################
sub getShortMonthName {
	my $month_value = $_[0];
	   $month_value = "Jan." if ($month_value == 1);
	   $month_value = "Feb." if ($month_value == 2);
	   $month_value = "March" if ($month_value == 3);
	   $month_value = "April" if ($month_value == 4);
	   $month_value = "May" if ($month_value == 5);
	   $month_value = "June" if ($month_value == 6);
	   $month_value = "July" if ($month_value == 7);
	   $month_value = "Aug." if ($month_value == 8);
	   $month_value = "Sep." if ($month_value == 9);
	   $month_value = "Oct." if ($month_value == 10);
	   $month_value = "Nov." if ($month_value == 11);
	   $month_value = "Dec." if ($month_value == 12);
	return($month_value);
}
######################################
## END: SUBROUTINE getShortMonthName
######################################



######################################
## START: SUBROUTINE print_year_menu
######################################
sub print_year_menu {
	my $field_name = $_[0];
	my $start_year = $_[1];
	my $end_year = $_[2];
	my $previous_selection = $_[3];
	my $add_9999_toyears = $_[4]; # Expects 'Date TBD'

	my $selected_9999 - "";
	   $selected_9999 = "SELECTED" if ($previous_selection eq '9999');

	## REVERSE DISPLAY SO MOST RECENT YEAR IS ON TOP
	if ($start_year < $end_year) {
		my $holder = $start_year;
		   $start_year = $end_year;
		   $end_year = $holder;
	} # END IF

print<<EOM;
<select name="$field_name" id="$field_name">
<option value=\"\">year</option>
EOM
	my $year_counter = $start_year;
		while ($year_counter >= $end_year) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $year_counter);
			print "<option value=\"$year_counter\" $selected>$year_counter</option>\n";
			$year_counter--;
		} # END WHILE
	print "<option value=\"9999\" $selected_9999>Date TBD</option>\n" if ($add_9999_toyears eq 'Date TBD');
	print "</select>\n";
# SAMPLE USAGE: &print_year_menu($site_current_year, $site_current_year + 3, "");
######################################
} # END: SUBROUTINE print_year_menu
######################################

##################################################
## START: SUBROUTINE print_year_menu_descending
##################################################
sub print_year_menu_descending {
	my $field_name = $_[0];
	my $start_year = $_[1];
	my $end_year = $_[2];
	my $previous_selection = $_[3];
print<<EOM;
<select name="$field_name" id="$field_name">
<option value=\"\">year</option>
EOM
	my $year_counter = $end_year;
		while ($year_counter >= $start_year) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $year_counter);
			print "<option value=\"$year_counter\" $selected>$year_counter</option>\n";
			$year_counter--;
		} # END WHILE
	print "</select>\n";
# SAMPLE USAGE: &print_year_menu($site_current_year, $site_current_year + 3, "");
##################################################
} # END: SUBROUTINE print_year_menu_descending
##################################################


####################################################################################
## START: SUBROUTINE THAT RETURNS FULL MONTH NAME WHEN YOU SENT IT A MONTH NUMBER
####################################################################################
sub get_daterange {
	my $startdate = $_[0];
	my $enddate = $_[1];
	   $enddate = $startdate if ($enddate eq '');
	my $new_date_range = "";
	my $show_year = $_[2];
	$show_year = 'yes' if ($show_year eq '');

	## CHOP UP DATE TO MAKE IT USEABLE
	my ($sdateyear, $sdatemonth, $sdateday) = split(/\-/,$startdate);
	my ($edateyear, $edatemonth, $edateday) = split(/\-/,$enddate);

	# GET FULL TEXT NAME FOR THE START/END MONTHS
	$sdatemonth = &getShortMonthName($sdatemonth);
	$edatemonth = &getShortMonthName($edatemonth);

	## START: DETERMINE DATE NUMBERS WITHOUT LEADING ZEROS
	my $sdateday_label = $sdateday;
	   $sdateday_label = substr($sdateday,1,1) if (substr($sdateday,0,1) eq '0');
	my $edateday_label = $edateday;
	   $edateday_label = substr($edateday,1,1) if (substr($edateday,0,1) eq '0');
	## END: DETERMINE DATE NUMBERS WITHOUT LEADING ZEROS

		# PRINT START MONTH
		$new_date_range = "$sdatemonth ";
		
		# PRINT START DATE IF THIS IS A ONE-DAY EVENT
		$new_date_range .= "$sdateday_label" if (($sdatemonth eq $edatemonth) && ($sdateday eq $edateday));
		# PRINT START DATE IF THIS IS A MULTI-DAY EVENT THAT IS ALL IN THE SAME MONTH
		$new_date_range .= "$sdateday_label\&ndash;$edateday_label" if (($sdatemonth eq $edatemonth) && ($sdateday ne $edateday));
		# PRINT START DATE IF THIS IS A MULTI-DAY EVENT THAT SPANS 2 MONTHS
		$new_date_range .= "$sdateday_label\&ndash;<BR>$edatemonth $edateday_label" if (($startdate ne $enddate) && ($sdatemonth ne $edatemonth));
		$sdateyear = $edateyear if ($edateyear ne '');
		$new_date_range .= ", $sdateyear" if ($show_year eq 'yes');

		$new_date_range = "Date TBD" if ($sdateyear =~ '9999');
	return ($new_date_range);
}
####################################################################################
## END: SUBROUTINE THAT RETURNS FULL MONTH NAME WHEN YOU SENT IT A MONTH NUMBER
####################################################################################


#################################################################
## START SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################
sub date2standard {
	my $date2transform = $_[0];
	   $date2transform =~ s/\ //g;
	   $date2transform =~ s/\-/\//g;

	my $use_text_month_name = $_[1]; # VALID VALUE = "textmonth" or blank

	my ($thisyear, $thismonth, $thisdate) = split(/\//,$date2transform);
		if ($use_text_month_name eq 'textmonth') {
			$thismonth = &getFullMonthName($thismonth);
			if (substr($thisdate,0,1) eq '0') {
				$thisdate =~ s/0//gi;
			} # END IF
			$date2transform = "$thismonth $thisdate, $thisyear";
		} else {
			# SHOW STANDARD NUMERICAL DATE
			$date2transform = "$thismonth\/$thisdate\/$thisyear";
		} # END IF/ELSE

		## SET TO BLANK IF NOT A VALID DATE
		$date2transform = "" if (($thismonth eq '') || ($thisdate eq ''));
	return($date2transform);
}
#################################################################
## END SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################


#################################################################
## START SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################
sub yearmonth2standard {
	my $date2transform = $_[0];
	   $date2transform =~ s/\ //g;
	   $date2transform =~ s/\-/\//g;
	my ($thisyear, $thismonth) = split(/\//,$date2transform);
	   $date2transform = "$thismonth\/$thisyear";
	   $date2transform = "" if $thismonth eq '';
	return($date2transform);
}
#################################################################
## END SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################


####################################################################
## START: CONVERT DATESTAMP TO PRETTY DATE/TIME FORMAT
####################################################################
sub convert_timestamp_2pretty_w_date {
	my $timestamp = $_[0];
	my $show_hours_minutes = $_[1];

	my $this_year = substr($timestamp, 0, 4);
	my $this_month = substr($timestamp, 4, 2);
	my $this_date = substr($timestamp, 6, 2);
	my $this_hours = substr($timestamp, 8, 2);
	my $this_min = substr($timestamp, 10, 2);
	my $am_pm = "AM";
	if ($this_hours > 12) {
		$this_hours = $this_hours - 12;
		$am_pm = "PM";
	}
	if ($this_hours == 12) {
		$am_pm = "PM";
	}
	
	my $pretty_time = "$this_month/$this_date/$this_year";
	if ($show_hours_minutes eq 'yes') {
		$pretty_time .= " $this_hours:$this_min $am_pm";
	}
	if ($pretty_time eq '//') {
		$pretty_time = "N/A";
	}
   return($pretty_time);
}
####################################################################
## END: CONVERT DATESTAMP TO PRETTY DATE/TIME FORMAT
####################################################################


#################################################################
## START SUBROUTINE: cleanthisfordb
#################################################################
sub cleanthisfordb {
my $cleanitem = $_[0];
	my $clean_for_xss = $_[1];
	   $clean_for_xss = "remove-xss" if ($clean_for_xss eq '');

	my $cleanitem_for_testing = lc($cleanitem);
	## PREVENT CROSS-SIRE SCRIPTING <script> ATTACKS
	if ($clean_for_xss eq 'remove-xss') {
		if (
			($cleanitem =~ '\<script') 
			|| ($cleanitem =~ 'script\>') 
		|| ($cleanitem =~ '\<embed') 
		|| ($cleanitem =~ 'embed\>') 
		|| ($cleanitem =~ '\<javascript')
			) {
			&send_email_to_webmaster("XSS ATTACK REPORT", $cleanitem);
		}
		$cleanitem =~ s/\<script/ \-- /gi; # replace opening script tag
		$cleanitem =~ s/script\>/ \-- /gi; # replace closing script tag
#		$cleanitem =~ s/script/POSSIBLE-XSS-ATTACK-BY-EXTERNAL-USER/gi; # replace script if still present (CAN'T DO THIS AS WORDS LIKE description CONTAINS script)
		$cleanitem =~ s/\<javascript/\<POSSIBLE-XSS-ATTACK-BY-EXTERNAL-USER/gi; # replace script if still present

		$cleanitem =~ s/\<embed/ \-- /gi; # replace opening embed tag
		$cleanitem =~ s/embed\>/ \-- /gi; # replace closing embed tag
#		$cleanitem =~ s/embed/POSSIBLE-XSS-ATTACK-BY-EXTERNAL-USER/gi; # replace embed if still present
	} # END IF

	## PREVENT SQL INJECTION
	$cleanitem =~ s/\/\>/\>/g; # REMOVE SINGLETON TAGS
	$cleanitem =~ s/\\//g; # REMOVE BACKSLASH IN CASE CHARACTERS ARE ALREADY BACKSLASHED
	$cleanitem =~ s/\%22/"/g;
	$cleanitem =~ s/Ò/"/g;			
	$cleanitem =~ s/Ó/"/g;			
	$cleanitem =~ s/"/\\"/g;
	$cleanitem =~ s/'/\\'/g;
	$cleanitem =~ s/Ô/\\Ô/g;
	$cleanitem =~ s/Õ/\\Õ/g;
	return($cleanitem);
}
#################################################################
## END SUBROUTINE: cleanthisfordb
#################################################################


#################################################################
## START SUBROUTINE: cleanemail
#################################################################
sub cleanemail {
	my ($cleanitem) = @_;
	$cleanitem =~ tr/-.A-Za-z0-9_+%@//cd; # Eliminate anything that's not in [-A-Za-z0-9_+%@]
	return $cleanitem;
}
#################################################################
## END SUBROUTINE: cleanemail
#################################################################


#################################################################
## START SUBROUTINE: send_email_to_webmaster
#################################################################
sub send_email_to_webmaster {
	my $message_title = $_[0];
	my $message_content = $_[1];

	# SEND EMAIL
	my $mailprog = '/usr/sbin/sendmail -t';
	my $recipient = $recipient_email;
  		$recipient =~ s/\\//g;
	my $webmaster_address = 'webmaster@sedl.org';

	my $remotehost = $ENV{"REMOTE_HOST"};
	my $remoteaddr = $ENV{"REMOTE_ADDR"};
	my $browser = $ENV{"HTTP_USER_AGENT"};

open(NOTIFY,"| $mailprog");
print NOTIFY <<EOM;
From: <$webmaster_address>
To: <$webmaster_address>
Reply-To: $webmaster_address
Errors-To: $webmaster_address
Sender: $webmaster_address
Subject: $message_title

Dear webmaster,

SCRIPT BEING ACTTACKED
http://www.sedl.org$ENV{'SCRIPT_NAME'}


The following content was trying to be inserted:

$message_content


This is an automated email, and the source code is at /staff/perlmodules/commoncode/commoncode.pm

REMOTE HOST: $remotehost
REMOTE ADDR: $remoteaddr
BROWSER:     $browser
EOM
close(NOTIFY);
}
#################################################################
## END SUBROUTINE: send_email_to_webmaster
#################################################################


#################################################################
## START SUBROUTINE: download_file
#################################################################
sub download_file {
	my $file_name = $_[0];
	my $file_path = $_[1]; # path to files
       $file_path = "/home/httpd/html/staff/communications/myfiles/" if ($file_path eq '');
       $file_path = "/home/httpd/html/unauthorized-access/" if ($file_path !~ '\/home\/httpd\/html\/');

	my @fileholder;		 # The content of the file being downloaded

	if ($file_name eq '') {  
		print "Content-type: text/html\n\n";  
		print "You must specify a file to download.";  
	} else {
		## START: OPEN THE FILE AND READ THE CONTENTS
		open(DLFILE, "<$file_path/$file_name") || Error('open', 'file');  
		@fileholder = <DLFILE>;  
		close (DLFILE) || Error ('close', 'file');
		## END: OPEN THE FILE AND READ THE CONTENTS

		## START: SEND THE FILE TO THE USER
		print "Content-Type:application/x-download\n";  
		print "Content-Disposition:attachment;filename=$file_name\n\n";
		print @fileholder
		## END: SEND THE FILE TO THE USER
	}


}
#################################################################
## END SUBROUTINE: download_file
#################################################################

#################################################################
## START SUBROUTINE: getFullStateName
#################################################################
sub getFullStateName {
	my $stateabbr = $_[0];
	my $statename = $stateabbr;
	$statename = "Alabama" if ($stateabbr eq 'AL');
	$statename = "Alaska" if ($stateabbr eq 'AK');
	$statename = "American Samoa" if ($stateabbr eq 'AS');
	$statename = "Arizona" if ($stateabbr eq 'AZ');
	$statename = "Arkansas" if ($stateabbr eq 'AR');
	$statename = "Bureau of Indian Affairs" if ($stateabbr eq 'BIA');
	$statename = "California" if ($stateabbr eq 'CA');
	$statename = "Colorado" if ($stateabbr eq 'CO');
	$statename = "Connecticut" if ($stateabbr eq 'CT');
	$statename = "Delaware" if ($stateabbr eq 'DE');
	$statename = "District of Columbia" if ($stateabbr eq 'DC');
	$statename = "Federated States of Micronesia" if ($stateabbr eq 'FSM');
	$statename = "Florida" if ($stateabbr eq 'FL');
	$statename = "Georgia" if ($stateabbr eq 'GA');
	$statename = "Guam" if ($stateabbr eq 'GU');
	$statename = "Hawaii" if ($stateabbr eq 'HI');
	$statename = "Idaho" if ($stateabbr eq 'ID');
	$statename = "Illinois" if ($stateabbr eq 'IL');
	$statename = "Indiana" if ($stateabbr eq 'IN');
	$statename = "Iowa" if ($stateabbr eq 'IA');
	$statename = "Kansas" if ($stateabbr eq 'KS');
	$statename = "Kentucky" if ($stateabbr eq 'KY');
	$statename = "Louisiana" if ($stateabbr eq 'LA');
	$statename = "Maine" if ($stateabbr eq 'ME');
	$statename = "Maryland" if ($stateabbr eq 'MD');
	$statename = "Massachusetts" if ($stateabbr eq 'MA');
	$statename = "Marshall Islands" if ($stateabbr eq 'MH');
	$statename = "Michigan" if ($stateabbr eq 'MI');
	$statename = "Minnesota" if ($stateabbr eq 'MN');
	$statename = "Mississippi" if ($stateabbr eq 'MS');
	$statename = "Missouri" if ($stateabbr eq 'MO');
	$statename = "Montana" if ($stateabbr eq 'MT');
	$statename = "Nebraska" if ($stateabbr eq 'NE');
	$statename = "Nevada" if ($stateabbr eq 'NV');
	$statename = "New Hampshire" if ($stateabbr eq 'NH');
	$statename = "New Jersey" if ($stateabbr eq 'NJ');
	$statename = "New Mexico" if ($stateabbr eq 'NM');
	$statename = "New York" if ($stateabbr eq 'NY');
	$statename = "North Carolina" if ($stateabbr eq 'NC');
	$statename = "North Dakota" if ($stateabbr eq 'ND');
	$statename = "Ohio" if ($stateabbr eq 'OH');
	$statename = "Oklahoma" if ($stateabbr eq 'OK');
	$statename = "Oregon" if ($stateabbr eq 'OR');
	$statename = "Pennsylvania" if ($stateabbr eq 'PA');
	$statename = "Puerto Rico" if ($stateabbr eq 'PR');
	$statename = "Rhode Island" if ($stateabbr eq 'RI');
	$statename = "South Carolina" if ($stateabbr eq 'SC');
	$statename = "South Dakota" if ($stateabbr eq 'SD');
	$statename = "Tennessee" if ($stateabbr eq 'TN');
	$statename = "Texas" if ($stateabbr eq 'TX');
	$statename = "Utah" if ($stateabbr eq 'UT');
	$statename = "Vermont" if ($stateabbr eq 'VT');
	$statename = "Virginia" if ($stateabbr eq 'VA');
	$statename = "Virgin Islands" if ($stateabbr eq 'VI');
	$statename = "Washington" if ($stateabbr eq 'WA');
	$statename = "Wisconsin" if ($stateabbr eq 'WI');
	$statename = "West Virginia" if ($stateabbr eq 'WV');
	$statename = "Wyoming" if ($stateabbr eq 'WY');
	return ($statename);
}
#################################################################
## END SUBROUTINE: getFullStateName
#################################################################

#################################################################
## START SUBROUTINE: getStateAbbreviation
#################################################################
sub getStateAbbreviation {
	my $statename = $_[0];
	my $stateabbr = $statename;
	$stateabbr = "AL" if ($statename eq 'Alabama');
	$stateabbr = "AK" if ($statename eq 'Alaska');
	$stateabbr = "AS" if ($statename eq 'American Samoa');
	$stateabbr = "AZ" if ($statename eq 'Arizona');
	$stateabbr = "AR" if ($statename eq 'Arkansas');
	$stateabbr = "BIA" if ($statename eq 'Bureau of Indian Affairs');
	$stateabbr = "CA" if ($statename eq 'California');
	$stateabbr = "CO" if ($statename eq 'Colorado');
	$stateabbr = "CT" if ($statename eq 'Connecticut');
	$stateabbr = "DE" if ($statename eq 'Delaware');
	$stateabbr = "DC" if ($statename eq 'District of Columbia');
	$stateabbr = "FSM" if ($statename eq 'Federated States of Micronesia');
	$stateabbr = "FL" if ($statename eq 'Florida');
	$stateabbr = "GA" if ($statename eq 'Georgia');
	$stateabbr = "GU" if ($statename eq 'Guam');
	$stateabbr = "HI" if ($statename eq 'Hawaii');
	$stateabbr = "ID" if ($statename eq 'Idaho');
	$stateabbr = "IL" if ($statename eq 'Illinois');
	$stateabbr = "IN" if ($statename eq 'Indiana');
	$stateabbr = "IA" if ($statename eq 'Iowa');
	$stateabbr = "KS" if ($statename eq 'Kansas');
	$stateabbr = "KY" if ($statename eq 'Kentucky');
	$stateabbr = "LA" if ($statename eq 'Louisiana');
	$stateabbr = "ME" if ($statename eq 'Maine');
	$stateabbr = "MD" if ($statename eq 'Maryland');
	$stateabbr = "MA" if ($statename eq 'Massachusetts');
	$stateabbr = "MH" if ($statename eq 'Marshall Islands');
	$stateabbr = "MI" if ($statename eq 'Michigan');
	$stateabbr = "MN" if ($statename eq 'Minnesota');
	$stateabbr = "MS" if ($statename eq 'Mississippi');
	$stateabbr = "MO" if ($statename eq 'Missouri');
	$stateabbr = "MT" if ($statename eq 'Montana');
	$stateabbr = "NE" if ($statename eq 'Nebraska');
	$stateabbr = "NV" if ($statename eq 'Nevada');
	$stateabbr = "NH" if ($statename eq 'New Hampshire');
	$stateabbr = "NJ" if ($statename eq 'New Jersey');
	$stateabbr = "NM" if ($statename eq 'New Mexico');
	$stateabbr = "NY" if ($statename eq 'New York');
	$stateabbr = "NC" if ($statename eq 'North Carolina');
	$stateabbr = "ND" if ($statename eq 'North Dakota');
	$stateabbr = "OH" if ($statename eq 'Ohio');
	$stateabbr = "OK" if ($statename eq 'Oklahoma');
	$stateabbr = "OR" if ($statename eq 'Oregon');
	$stateabbr = "PA" if ($statename eq 'Pennsylvania');
	$stateabbr = "PR" if ($statename eq 'Puerto Rico');
	$stateabbr = "RI" if ($statename eq 'Rhode Island');
	$stateabbr = "SC" if ($statename eq 'South Carolina');
	$stateabbr = "SD" if ($statename eq 'South Dakota');
	$stateabbr = "TN" if ($statename eq 'Tennessee');
	$stateabbr = "TX" if ($statename eq 'Texas');
	$stateabbr = "UT" if ($statename eq 'Utah');
	$stateabbr = "VT" if ($statename eq 'Vermont');
	$stateabbr = "VA" if ($statename eq 'Virginia');
	$stateabbr = "VI" if ($statename eq 'Virgin Islands');
	$stateabbr = "WA" if ($statename eq 'Washington');
	$stateabbr = "WI" if ($statename eq 'Wisconsin');
	$stateabbr = "WV" if ($statename eq 'West Virginia');
	$stateabbr = "WY" if ($statename eq 'Wyoming');
	return ($stateabbr);
}
#################################################################
## END SUBROUTINE: getStateAbbreviation
#################################################################

######################################
## START: SUBROUTINE printform_state
######################################
sub printform_state {
	my $form_variable_name = $_[0];
	my $selected_state = $_[1];
	my $show_outside_us = $_[2];
	my $counter_state = "0";
	my @states = ("AK", "AL", "AR", "AS", "AZ", "BIA", "CA", "CO", "CT", "DC", "DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "LA", "MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT", "NC", "ND", "NE", "NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VA", "VI", "VT", "WA", "WI", "WV", "WY");

	print "<select name=\"$form_variable_name\" id=\"$form_variable_name\">
			<option value=\"\"></option>";
	if ($show_outside_us eq 'yes') {
		print "<option value=\"Outside the United States\">Outside the US</option>\n";
	}
	while ($counter_state <= $#states) {
		print "<option VALUE=\"$states[$counter_state]\"";
		print " SELECTED" if ($states[$counter_state] eq $selected_state);
		print ">$states[$counter_state]</option>\n";
		$counter_state++;
	} # END WHILE
	print "</select>\n";
}
######################################
## END: SUBROUTINE printform_state
######################################


######################################
## START: SUBROUTINE printform_state_board_of_directors
######################################
sub printform_state_board_of_directors {
	my $form_variable_name = $_[0];
	my $selected_state = $_[1];
	my $show_outside_us = $_[2];
	my $counter_state = "0";
	my @states = ("National", "AL", "AR", "LA", "MS", "NC", "NM", "OK", "SC", "TX");

	print "<select name=\"$form_variable_name\" id=\"$form_variable_name\">
			<option value=\"\"></option>";
	while ($counter_state <= $#states) {
		print "<option VALUE=\"$states[$counter_state]\"";
		print " SELECTED" if ($states[$counter_state] eq $selected_state);
		print ">$states[$counter_state]</option>\n";
		$counter_state++;
	} # END WHILE
	print "</select>\n";
}
######################################
## END: SUBROUTINE printform_state_board_of_directors
######################################


######################################
## START: SUBROUTINE printform_question_type
######################################
sub printform_question_type {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("select one", "select all that apply");
	my @item_label = ("select one", "select all that apply");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<select name="$field_name" id="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<option VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</option>\n";
			$item_counter++;
		} # END WHILE
	print "</select>\n";
######################################
} # END: SUBROUTINE printform_question_type
######################################


######################################
## START: SUBROUTINE printform_yes_no_menu
######################################
sub printform_yes_no_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", "yes", "no");
	my @item_label = ("", "yes", "no");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<select name="$field_name" id="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<option VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</option>\n";
			$item_counter++;
		} # END WHILE
	print "</select>\n";
######################################
} # END: SUBROUTINE printform_yes_no_menu
######################################


#################################################################
## START SUBROUTINE: PRINT YES/NO FORM OPTION
#################################################################
sub printform_yesno_radio {
	my $form_variable_name = $_[0];
	my $preselected_value = $_[1];

	my @options = ( "yes", "no");
	my $counter_options = "0";
		while ($counter_options <= $#options) {
			print "	<input type=\"radio\" name=\"$form_variable_name\" id=\"$form_variable_name\_$counter_options\" value=\"$options[$counter_options]\" ";
			print " CHECKED" if ($preselected_value eq $options[$counter_options]);
			print "><label for=\"$form_variable_name\_$counter_options\">$options[$counter_options]</label>";
			print "&nbsp; &nbsp; &nbsp; &nbsp;" if ($counter_options eq '0');
			$counter_options++;
		}
}
#################################################################
## END SUBROUTINE: PRINT YES/NO FORM OPTION
#################################################################


######################################
## START: SUBROUTINE printform_sedl_unit_menu
######################################
sub printform_sedl_unit_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	   $previous_selection = "Education Systems Support (ESS)" if ($previous_selection eq 'Improving School Performance (ISP)');
	   $previous_selection = "Education Systems Support (ESS) SECC" if ($previous_selection eq 'Improving School Performance (ISP) SECC');
	   $previous_selection = "Education Systems Support (ESS) TXCC" if ($previous_selection eq 'Improving School Performance (ISP) TXCC');
	   $previous_selection = "EDUCATION SYSTEMS SUPPORT (ESS)" if ($previous_selection eq 'IMPROVING SCHOOL PERFORMANCE (ISP)');
	   $previous_selection = "EDUCATION SYSTEMS SUPPORT (ESS) SECC" if ($previous_selection eq 'IMPROVING SCHOOL PERFORMANCE (ISP) SECC');
	   $previous_selection = "EDUCATION SYSTEMS SUPPORT (ESS) TXCC" if ($previous_selection eq 'IMPROVING SCHOOL PERFORMANCE (ISP) TXCC');
	my $mixed_case = $_[2];
	   $mixed_case = "yes" if ($mixed_case eq '');
	my $add_sedlwide_option = $_[3];
	   $add_sedlwide_option = "no" if ($add_sedlwide_option eq '');

	my @item_value = ("", 
		"ADMINISTRATIVE SERVICES (AS)", 
		"AFTERSCHOOL, FAMILY, AND COMMUNITY (AFC)", 
		"COMMUNICATIONS (COM)", 
		"DEVELOPMENT", 
		"DISABILITY RESEARCH TO PRACTICE (DRP)", 
		"EDUCATION SYSTEMS SUPPORT (ESS)", 
		"EDUCATION SYSTEMS SUPPORT (ESS) SECC", 
		"EDUCATION SYSTEMS SUPPORT (ESS) TXCC", 
		"REGIONAL EDUCATIONAL LABORATORIES (REL)", 
		"REGIONAL EDUCATIONAL LABORATORIES (REL SE)", 
		"REGIONAL EDUCATIONAL LABORATORIES (REL SW)", 
		"REGIONAL EDUCATIONAL LABORATORIES (REL SE and REL SW)", 
		"EXECUTIVE OFFICE", "RESEARCH AND EVALUATION (R&E)"
		);
	my @item_label = ("(select one)", 
		"ADMINISTRATIVE SERVICES (AS)", 
		"AFTERSCHOOL, FAMILY, AND COMMUNITY (AFC)", 
		"COMMUNICATIONS (COM)", 
		"DEVELOPMENT", 
		"DISABILITY RESEARCH TO PRACTICE (DRP)", 
		"EDUCATION SYSTEMS SUPPORT (ESS)", 
		"EDUCATION SYSTEMS SUPPORT (ESS) SECC", 
		"EDUCATION SYSTEMS SUPPORT (ESS) TXCC", 
		"EXECUTIVE OFFICE", 
		"REGIONAL EDUCATIONAL LABORATORIES (REL)", 
		"REGIONAL EDUCATIONAL LABORATORIES (REL SE)", 
		"REGIONAL EDUCATIONAL LABORATORIES (REL SW)", 
		"REGIONAL EDUCATIONAL LABORATORIES (REL SE and REL SW)", 
		"RESEARCH AND EVALUATION (R&E)"
		);

	if ($mixed_case eq 'no') {
		$previous_selection = "ADMINISTRATIVE SERVICES (AS)" if ($previous_selection eq 'AD');
		$previous_selection = "AFTERSCHOOL, FAMILY, AND COMMUNITY (AFC)" if ($previous_selection eq 'AFC');
		$previous_selection = "COMMUNICATIONS (COM)" if ($previous_selection eq 'COM');
		$previous_selection = "DEVELOPMENT" if ($previous_selection eq 'DEV');
		$previous_selection = "DISABILITY RESEARCH TO PRACTICE (DRP)" if ($previous_selection eq 'DRP');
		$previous_selection = "EXECUTIVE OFFICE" if ($previous_selection eq 'EO');
		$previous_selection = "COMMUNICATIONS" if ($previous_selection eq 'COM');
		$previous_selection = "EDUCATION SYSTEMS SUPPORT (ESS) SECC" if ($previous_selection eq 'SECC');
		$previous_selection = "EDUCATION SYSTEMS SUPPORT (ESS) TXCC" if ($previous_selection eq 'TXCC');
		$previous_selection = "RESEARCH AND EVALUATION (R&E)" if ($previous_selection eq 'RE');
		$previous_selection = "REGIONAL EDUCATIONAL LABORATORIES (REL)" if ($previous_selection eq 'REL');
		$previous_selection = "REGIONAL EDUCATIONAL LABORATORIES (REL SE)" if ($previous_selection eq 'REL SE');
		$previous_selection = "REGIONAL EDUCATIONAL LABORATORIES (REL SW)" if ($previous_selection eq 'REL SW');
	} else {
		$previous_selection = "Administrative Services (AS)" if ($previous_selection eq 'AD');
		$previous_selection = "Afterschool, Family, and Community (AFC)" if ($previous_selection eq 'AFC');
		$previous_selection = "Communications (COM)" if ($previous_selection eq 'COM');
		$previous_selection = "Development" if ($previous_selection eq 'DEV');
		$previous_selection = "Disability Research to Practice (DRP)" if ($previous_selection eq 'DRP');
		$previous_selection = "Executive Office" if ($previous_selection eq 'EO');
		$previous_selection = "Communications" if ($previous_selection eq 'COM');
		$previous_selection = "Education Systems Support (ESS) SECC" if ($previous_selection eq 'SECC');
		$previous_selection = "Education Systems Support (ESS) TXCC" if ($previous_selection eq 'TXCC');
		$previous_selection = "Research and Evaluation (R&E)" if ($previous_selection eq 'RE');
		$previous_selection = "Regional Educational Laboratories (REL)" if ($previous_selection eq 'REL');
		$previous_selection = "Regional Educational Laboratories (REL SE)" if ($previous_selection eq 'REL SE');
		$previous_selection = "Regional Educational Laboratories (REL SW)" if ($previous_selection eq 'REL SW');

		@item_value = ("", 
		"Administrative Services (AS)", 
		"Afterschool, Family, and Community (AFC)", 
		"Communications (COM)", 
		"Development", 
		"Disability Research to Practice (DRP)", 
		"Education Systems Support (ESS)", 
		"Education Systems Support (ESS) SECC", 
		"Education Systems Support (ESS) TXCC", 
		"Executive Office", 
		"Regional Educational Laboratories (REL)", 
		"Regional Educational Laboratories (REL SE)", 
		"Regional Educational Laboratories (REL SW)", 
		"Regional Educational Laboratories (REL SE and REL SW)", 
		"Research and Evaluation (R&E)"
		);
		@item_label = ("(select one)", 
		"Administrative Services (AS)", 
		"Afterschool, Family, and Community (AFC)", 
		"Communications (COM)", 
		"Development", 
		"Disability Research to Practice (DRP)", 
		"Education Systems Support (ESS)", 
		"Education Systems Support (ESS) SECC", 
		"Education Systems Support (ESS) TXCC", 
		"Executive Office", 
		"Regional Educational Laboratories (REL)", 
		"Regional Educational Laboratories (REL SE)", 
		"Regional Educational Laboratories (REL SW)", 
		"Regional Educational Laboratories (REL SE and REL SW)", 
		"Research and Evaluation (R&E)"
		);
	}

	if ($add_sedlwide_option eq 'yes') {
		push (@item_value, "SEDL");
		push (@item_label, "SEDL");
	}

	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<select name="$field_name" id="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<option VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</option>\n";
			$item_counter++;
		} # END WHILE
	print "</select>\n";
######################################
} # END: SUBROUTINE printform_sedl_unit_menu
######################################


######################################
## START: SUBROUTINE print_sedl_product_category_menu
######################################
sub print_sedl_product_category_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	my @item_value = (
		"Afterschool", 
		"Assessment", 
		"Autism", 
		"Change Process (unused)", 
		"Disability Research", 
		"Early Childhood", 
		"English Language Learners", 
		"Family and Community", 
		"Improving School Performance", 
		"Knowledge Translation", 
		"Leadership", 
		"Mathematics and Science", 
		"Reading and Literacy", 
		"Response to Intervention", 
		"Special Education", 
		"Technology");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "CHECKED" if (($previous_selection =~ $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<input type=\"checkbox\" name=\"$field_name\_$item_counter\" id=\"$field_name\_$item_counter\" VALUE=\"$item_value[$item_counter]\" $selected><label for=\"$field_name\_$item_counter\">$item_value[$item_counter]</label><br>\n";
			$item_counter++;
		} # END WHILE
######################################
} # END: SUBROUTINE print_sedl_product_category_menu
######################################


######################################
## START: SUBROUTINE print_subcategory_menu
######################################
sub print_subcategory_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", 
		"Early Reading", 
		"Languages Other Than English");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<select name="$field_name" id="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<OPTION VALUE=\"$item_value[$item_counter]\" $selected>$item_value[$item_counter]</OPTION>\n";
			$item_counter++;
		} # END WHILE
	print "</select>\n";
######################################
} # END: SUBROUTINE print_subcategory_menu
######################################


######################################################
## START: SUBROUTINE print_cc_product_category_menu
######################################################
sub print_cc_product_category_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_code = (
		"College and Career Ready Students", 
		"Science, Technology, Engineering, and Math Education (STEM)", 
		"Science and Technology", 
		"Math", 
		"Reading", 
		"School Improvement and Accountability", 
		"High School Reform", 
		"Turning Around Low-Achieving Schools", 
		"Diverse Learners", 
		"English Language Learners", 
		"Special Education", 
		"Response to Intervention", 
		"Family and Community Engagement", 
		"Great Teachers and Great Leaders", 
		"Leadership", 
		"Teacher Quality and Effectiveness", 
		"Equitable Distribution", 
		"ESEA", 
		"Longitudinal Data Systems and Data Use",
		"Ensuring School Readiness and Success of Preschool-Age Children",
		"Building Rigorous Instructional Pathways That Support Successful Transition To College",
		"Identifying and Scaling Up Innovative Approaches to Teaching and Learning",
		"Nomatch");
	my @item_value = (
		"College and Career Ready Students", 
		" -- Science, Technology, Engineering, and Math Education (STEM)", 
		" -- Science and Technology", 
		" -- Math", 
		" -- Reading", 
		"School Improvement and Accountability", 
		" -- High School Reform", 
		" -- Turning Around Low-Achieving Schools", 
		"Diverse Learners", 
		" -- English Language Learners", 
		" -- Special Education", 
		" -- Response to Intervention", 
		"Family and Community Engagement", 
		"Great Teachers and Great Leaders", 
		" -- Leadership", 
		" -- Teacher Quality and Effectiveness", 
		" -- Equitable Distribution", 
		"ESEA", 
		"Longitudinal Data Systems and Data Use",
		"Ensuring School Readiness and Success of Preschool-Age Children",
		"Building Rigorous Instructional Pathways That Support Successful Transition To College",
		"Identifying and Scaling Up Innovative Approaches to Teaching and Learning",
		"Nomatch"
		);	my $item_counter = "0";
	my $count_total_items = $#item_value;
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "CHECKED" if (($previous_selection =~ "$item_code[$item_counter]") && ($previous_selection ne ''));
			print "<input type=\"checkbox\" name=\"$field_name\_$item_counter\" id=\"$field_name\_$item_counter\"VALUE=\"$item_code[$item_counter]\" $selected><label for=\"$field_name\_$item_counter\">$item_value[$item_counter]</label><br>\n";
#			print "Does '$previous_selection' contain '$item_value[$item_counter]'";
			$item_counter++;
		} # END WHILE
#####################################################
} # END: SUBROUTINE print_cc_product_category_menu
#####################################################


#########################################################################
## START: SUBROUTINE printform_checkbox_sedl_areas_of_expertise_news
#########################################################################
sub printform_checkbox_sedl_areas_of_expertise_news {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	my $hide_general_news_option = $_[2];
	
	my @item_value = (
	"Afterschool and Expanded Learning", 
	"Analytical Technical Support: Evaluation Capacity Building",
	"Disability Research", 
	"Early Childhood", 
	"Education Research", 
	"English Language Learners", 
	"Family and Community",
	"General SEDL Announcement",
	"High School Reform",
	"Knowledge Translation", 
	"Leadership",
	"Managing the Change Process",
	"Mathematics and Science",
	"Program Evaluation", 
	"Reading and Literacy",
	"Response to Intervention", 
	"School Improvement",
	"Teacher Quality",
	"Technology"
	);
	my @item_label = (
	"Afterschool and Expanded Learning", 
	"Analytical Technical Support: Evaluation Capacity Building",
	"Disability Research", 
	"Early Childhood", 
	"Education Research", 
	"English Language Learners", 
	"Family and Community",
	"General SEDL Announcement",
	"High School Reform",
	"Knowledge Translation", 
	"Leadership",
	"Managing the Change Process",
	"Mathematics and Science",
	"Program Evaluation", 
	"Reading and Literacy",
	"Response to Intervention", 
	"School Improvement",
	"Teacher Quality",
	"Technology"
	);
	my $item_counter = "0";
	my $count_total_items = $#item_value;

		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "CHECKED" if (($previous_selection =~ $item_value[$item_counter]) && ($previous_selection ne ''));
			if (($hide_general_news_option !~ 'hide-general-news-option') || ($item_label[$item_counter] ne 'General SEDL Announcement')) {
				print "<input type=\"checkbox\" name=\"$field_name\_$item_counter\" id=\"$field_name\_$item_counter\" VALUE=\"$item_value[$item_counter]\" $selected><label for=\"$field_name\_$item_counter\">$item_label[$item_counter]</label><br>\n";
			} # END IF		
			$item_counter++;
		} # END WHILE
#########################################################################
} # END: SUBROUTINE printform_checkbox_sedl_areas_of_expertise_news
#########################################################################


#########################################################################
## START: SUBROUTINE check_areas_of_expertise_news
#########################################################################
sub check_areas_of_expertise_news {
	my $previous_selection = $_[0];
	
	my @item_value = (
	"Afterschool and Expanded Learning", 
	"Analytical Technical Support: Evaluation Capacity Building",
	"Disability Research", 
	"Early Childhood", 
	"Education Research", 
	"English Language Learners", 
	"Family and Community",
	"General SEDL Announcement",
	"High School Reform",
	"Knowledge Translation", 
	"Leadership",
	"Managing the Change Process",
	"Mathematics and Science",
	"Program Evaluation", 
	"Reading and Literacy",
	"Response to Intervention", 
	"School Improvement",
	"Teacher Quality",
	"Technology"
	);
	my $item_counter = "0";
	my $count_total_items = $#item_value;
	my $found = "";

		while ($item_counter <= $count_total_items) {
			$item_value[$item_counter] =~ s/\(RtI\)//gi;
			$found .= "FOUND $item_value[$item_counter]<br>" if ($previous_selection =~ $item_value[$item_counter]);
			$item_counter++;
		} # END WHILE

	if ($found !~ 'FOUND') {
		print "<p><span style=\"color:red;\">NO MATCH</span></p>";
	} else {
#		print "<p><span style=\"color:green;\">$found</span></p>";
	}

#########################################################################
} # END: SUBROUTINE check_areas_of_expertise_news
#########################################################################


#########################################################################
## START: SUBROUTINE get_array_of_expertise_news
#########################################################################
sub get_array_of_expertise_news {

	my @expertise_items_for_news_page = (
	"School Improvement", 
	"Education Research", 
	"Disability Research", 
	"Mathematics and Science", 
	"Reading and Literacy", 
	"English Language Learners", 
	"Response to Intervention", 
	"Technology", 
	"Afterschool and Expanded Learning", 
	"Family and Community");
	return (@expertise_items_for_news_page);
}
#########################################################################
## START: SUBROUTINE get_array_of_expertise_news
#########################################################################


######################################
## START: SUBROUTINE printform_services_request_type
######################################
sub printform_services_request_type {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("", "chps", "re", "evaluation_capacity_building", "evaluation_services");
	my @item_label = ("", "chps", "re", "evaluation_capacity_building", "evaluation_services");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<select name="$field_name" id="$field_name">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<option VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</option>\n";
			$item_counter++;
		} # END WHILE
	print "</select>\n";
######################################
} # END: SUBROUTINE printform_services_request_type
######################################


####################################################################
## START: SUBROUTINE - trim_text_length
####################################################################
sub trim_text_length {
	my $old_text = $_[0];
	   $old_text = " " if ($old_text eq '');
	my $new_length = $_[1];
	   $new_length = 250 if ($new_length < 1);
	my $more_link = $_[2];

	my $new_text = $old_text;
	my $loop_counter = "0";
	
	if (length($new_text) > $new_length) {
		## TRIM TO LENGTH
		while (length($new_text) > $new_length) {
			my $last_char = substr($new_text, length($new_text) - 1, 1);
#			print "<BR>Last char = \'$last_char\'"
			chop($new_text);
		} # END WHILE
		## START: REMOVE PARTIAL WORDS
		while (substr($new_text, length($new_text) - 1, 1) ne ' ') {
			my $last_char = substr($new_text, length($new_text) - 1, 1);
#			print "<BR>Word = \'$new_text\' AND Last char = \"$last_char\"";
			chop($new_text);
		} # END WHILE
		## END: REMOVE PARTIAL WORDS

		# ADD "... (more)"
		if ($more_link ne '') {
			$new_text .= "\... (<a href=\"$more_link\">more</a>)";
		} else {
			$new_text .= "\...";
		}
	} # END IF
	
	return($new_text);
} # END SUBROUTINE trim_text_length
####################################################################
## END: SUBROUTINE - trim_text_length
####################################################################


####################################################################
## START: SUBROUTINE randomPassword
####################################################################
sub randomPassword {
my $password_length = $_[0];
	if (!$password_length) {
		$password_length = 5;
	}
	my $password; # THIS WILL HOLD THE NEW PASSWORD
	my $_rand; # HOLDS A RANDOM CHARACTER
my @chars = split(" ", "a b c d e f g h j k m n p q r s t u v w x y z 2 3 4 5 6 7 8 9");
srand;
	for (my $i=0; $i <= $password_length ;$i++) {
		$_rand = int(rand 31);
		$password .= $chars[$_rand];
	}
	$password =~ tr/a-z/A-Z/; # lowercase everything (may not be necessary anymore)
	return $password;
}
####################################################################
## END: SUBROUTINE randomPassword
####################################################################


##################################################################
## START SUBROUTINE: SPLIT SEARCH STRING INTO WORDS TO SEARCH FOR
##################################################################
sub print_tinyMCE_code {
	my $content_css = $_[0];
	my $default_content_css = "/staff/includes/staff2006_tinymce.css";
       $content_css = $default_content_css if ($content_css eq '');

print<<EOM;
<script type="text/javascript" src="/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	extended_valid_elements : \"script,iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],\$elements\",
	plugins : "spellchecker,table,paste",
	gecko_spellcheck : true,
	theme_advanced_buttons1_add : "pastetext,pasteword",
	theme_advanced_buttons3_add : "tablecontrols, spellchecker",
	table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
	table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
	table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
	table_cell_limit : 100,
	table_row_limit : 20,
	table_col_limit : 5,
    force_br_newlines : true,
    force_p_newlines : false,
	forced_root_block : false,
	paste_auto_cleanup_on_paste : true,
 	theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
	content_css: "$content_css",
	apply_source_formatting : true,
	convert_urls : false
});
</script>
EOM
}
##################################################################
## END SUBROUTINE: SPLIT SEARCH STRING INTO WORDS TO SEARCH FOR
##################################################################


####################################################################
## START: SUBROUTINE cleanaccents2html
####################################################################
sub cleanaccents2html {
	my $cleanitem = $_[0];
	$cleanitem =~ s/Ò/"/g;			
	$cleanitem =~ s/Ó/"/g;			
	$cleanitem =~ s/Õ/'/g;			
	$cleanitem =~ s/Ô/'/g;
	$cleanitem =~ s/Ð/\&ndash\;/g;
	$cleanitem =~ s/Ñ/\&mdash\;/g;
	$cleanitem =~ s/--/\&mdash\;/g;
	$cleanitem =~ s/Ê//g; # invisible bullet
	$cleanitem =~ s/É/.../g;
	$cleanitem =~ s/À/&iquest\;/g; 
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
#	$cleanitem =~ s/–/&ntilde\;/g; # COMMENTED OUT BECAUSE IT IS SUBSTITUTING FOR THE ndash
	$cleanitem =~ s/ñ/&Ograve\;/g;
	$cleanitem =~ s/˜/&ograve\;/g;
	$cleanitem =~ s/î/&Oacute\;/g;
#	$cleanitem =~ s/—/&oacute\;/g; # COMMENTED OUT BECAUSE IT IS SUBSTITUTING FOR THE mdash
	$cleanitem =~ s/Í/&Otilde\;/g;
	$cleanitem =~ s/›/&otilde\;/g;
	$cleanitem =~ s/…/&Ouml\;/g;
	$cleanitem =~ s/š/&ouml\;/g;
	$cleanitem =~ s/ô/&Ugrave\;/g;
	$cleanitem =~ s//&ugrave\;/g;
	$cleanitem =~ s/ò/&Uacute\;/g;
	$cleanitem =~ s/œ/&uacute\;/g;
	$cleanitem =~ s/ó/&Ucirc\;/g;  ## THIS REPLACES THE — FOR SOME REASON
	$cleanitem =~ s/ž/&ucirc\;/g;
	$cleanitem =~ s/†/&Uuml\;/g;
	$cleanitem =~ s/Ÿ/&uuml\;/g;
	$cleanitem =~ s/Ø/&yuml\;/g;
	$cleanitem =~ s/\/\>/\>/g; # REMOVE SINGLETON TAGS
	$cleanitem =~ s/ \& / &amp; /g; # REMOVE space ampersand space TAGS
	return ($cleanitem);
}
####################################################################
## END: SUBROUTINE cleanaccents2html
####################################################################


##########################################################################################
## START: SUBROUTINE - evaluate_word_search
##########################################################################################
sub evaluate_word_search {
	my $searchfor = $_[0];
	my $andor = $_[1];

	## THIS PROGRAM WILL HANDLE UP TO 6 SEARCH ITEMS/WORDS AT A TIME
	my $sf1 = ""; my $sf2 = ""; my $sf3 = ""; my $sf4 = ""; my $sf5 = ""; my $sf6 = "";

	my $searchphrase = $searchfor; # HOLDS SEARCH TEXT IN CASE USER SELECTED MENU OPTION FOR PHRASE SEARCH
	   $searchfor =~ s/\t//; # Remove tabs
	   $searchfor =~ s/  / /; # Remove excess spaces
	   $searchfor =~ s/  / /; # Remove excess spaces
	   $searchfor =~ s/\,//; # Remove commas
	   $searchfor =~ s/\'/\\'/; # Remove single quotes
	   $searchfor =~ s/\)//;
	   $searchfor =~ s/\(//;
#	   $searchfor =~ s/\"//; # Leaving double-quotes in to do Google-like phrase searching


	################################################################################
	## START: IF SEARCH STRING HAS A DOUBLE-QUOTES IN IT, CHECK FOR A PHRASE SEARCH
	################################################################################
	my $countquotes = "0"; # HIS WILL HOLD THE COUNT OF DOUBLE-QUOTES TO CHECK IF NEED TO DO A PHRASE SEARCH
	my $searchstringlength = length($searchfor); # LENGTH OF SEARCH STRING -DUH!

	## IF SEARCH STRING HAS A QUOTES AT ALL, DO THIS PART
	if ($searchfor =~ '\"') {
		my $searchfor2 = $searchfor;
		my @searchstring = "";
		my $count = $searchstringlength;
		my $lastcount = "";
		my $multiplewords = "";
		my $flagquotes = "off";
		my $teststring = "\"";


		## START: PUT CHARACTERS INTO ARRAY AND COUNT THE NUMBER OF DOUBLE-QUOTES
		while ($count >= 0) {
			$searchstring[$count] = chop($searchfor2);
			# print "<BR>$searchstring[$count] - $countquotes"; # DEBUG
			if ($searchstring[$count] eq '"') {
				$countquotes++;
			}
			$count--;
		} # END WHILE
		## END: PUT CHARACTERS INTO ARRAY AND COUNT THE NUMBER OF DOUBLE-QUOTES


		## START: HANDLE PHRASE SEARCH
		if ($countquotes eq '2') {
			$searchfor = ""; # FORGET ORIGINAL SEARCH STRING

			my $count = "0";
			while ($count <= $searchstringlength) {
#				print "- $count $searchstring[$count]<BR>" if $debug;
	
				# SET FLAG IF CHARACTER IS DOUBLE-QUOTES
				if ($searchstring[$count] eq '"') {
					if ($flagquotes eq 'on') {
						$flagquotes = "off";
#						print "FLAG $flagquotes" if $debug;
					} else {
					$flagquotes = "on";
#					print "FLAG $flagquotes" if $debug;
					} # END IF/ELSE
		
				} # END IF
		
				## START: SEND CHARACTER TO ONE OF TWO VARIABLES
				if ($flagquotes eq 'on') {
					$sf1 .= "$searchstring[$count]" if ($searchstring[$count] ne '"'); # PHRASE VARIABLE
#					print "<BR>SF1: $sf1" if $debug;
				} else {
					$multiplewords .= "$searchstring[$count]" if ($searchstring[$count] ne '"'); # NON-PHRASE VARIABLE
#					print "<BR>MULTIPLEWORDS: $multiplewords" if $debug;
				}
				## END: SEND CHARACTER TO ONE OF TWO VARIABLES
		
				$lastcount = $count;
				$count++;
			} # END WHILE
	
			($sf2, $sf3, $sf4, $sf5, $sf6) = split(/ /,$multiplewords);
		} else {
			###################################
			## START: HANDLE NON-PHRASE SEARCH
			###################################
			## REMOVE CLUTTER words
			$searchfor =~ s/of //g;
			$searchfor =~ s/The //g;
			$searchfor =~ s/the //g;
			$searchfor =~ s/ a / /g;
			$searchfor =~ s/ an / /g;
			$searchfor =~ s/ if / /g;   
			
			# Remove excess spaces
			$searchfor =~ s/\"/ /g; 
			$searchfor =~ s/  / /;
			$searchfor =~ s/  / /;

			($sf1, $sf2, $sf3, $sf4, $sf5, $sf6) = split(/ /,$searchfor);
		}
	###################################
	##END: HANDLE NON-PHRASE SEARCH
	###################################

		## START: HANDLE INSTANCE WHERE NOTHING BETWEEN DOUBLE-QUOTES
		if ($sf1 eq '') {
			$searchfor =~ s/\"/ /g; # Remove excess spaces
			($sf1, $sf2, $sf3, $sf4, $sf5, $sf6) = split(/ /,$searchfor);
		}
		## END: HANDLE INSTANCE WHERE NOTHING BETWEEN DOUBLE-QUOTES

	} else {
		($sf1, $sf2, $sf3, $sf4, $sf5, $sf6) = split(/ /,$searchfor);
	}
	################################################################################
	## END: IF SEARCH STRING HAS A DOUBLE-QUOTES IN IT, CHECK FOR A PHRASE SEARCH
	################################################################################

	$andor = "AND" if $andor eq '';

	## HANDLE SEARCHBING BY PHRASE BY PUTTING ALL SEARCH TERMS IN SF1 VALUE
	if (($andor eq 'phrase') || ($andor eq 'PHRASE')) {
		$sf1 = $searchphrase;
		$sf2 = "";
		$sf3 = "";
		$sf4 = "";
		$sf5 = "";
		$sf6 = "";
	}
	$andor = &cleanthisfordb($andor);
	$sf1 = &cleanthisfordb($sf1);
	$sf2 = &cleanthisfordb($sf2);
	$sf3 = &cleanthisfordb($sf3);
	$sf4 = &cleanthisfordb($sf4);
	$sf5 = &cleanthisfordb($sf5);
	$sf6 = &cleanthisfordb($sf6);
	return($andor, $sf1, $sf2, $sf3, $sf4, $sf5, $sf6);
}
##########################################################################################
## END: SUBROUTINE - evaluate_word_search
##########################################################################################


#################################################################
## START SUBROUTINE: determine_documentgroup_by_url
#################################################################
sub determine_documentgroup_by_url {
	my $url = $_[0];
	my $this_docgroup = "";
    	$this_docgroup = "afterschool" if ($url =~ 'afterschool');
    	$this_docgroup = "afterschool" if ($url =~ 'www.learningaccount.net');
    	$this_docgroup = "annualreport" if (($url =~ 'annual') && ($url =~ 'report'));
    	$this_docgroup = "annualreport" if ($url =~ '/pubs/ar');
    	$this_docgroup = "assessment" if ($url =~ 'toolkit98');
    	$this_docgroup = "assessment" if ($url =~ '/gap/');
    	$this_docgroup = "blueprint" if ($url =~ '/blueprint');
    	$this_docgroup = "change" if ($url =~ '/change/issues');
    	$this_docgroup = "change" if ($url =~ '/pubs/cha');
     	$this_docgroup = "change" if ($url =~ '/change/');
     	$this_docgroup = "change" if ($url =~ '/pubs/1001/');
  		$this_docgroup = "csr" if ($url =~ 'csr');
 		$this_docgroup = "cbam" if ($url =~ 'csr');
    	$this_docgroup = "culture" if ($url =~ '/culture');
    	$this_docgroup = "culture" if ($url =~ '/loteced');
    	$this_docgroup = "culture" if ($url =~ '/pubs/lc');
    	$this_docgroup = "culture" if ($url =~ '/pubs/lote');
    	$this_docgroup = "disability" if ($url =~ 'autism\.sedl\.org');
    	$this_docgroup = "disability" if ($url =~ 'campbellcollaboration');
    	$this_docgroup = "disability" if ($url =~ 'ncddr');
    	$this_docgroup = "disability" if ($url =~ 'researchutilization\.org');
    	$this_docgroup = "disability" if ($url =~ '/rural/seeds/assistivetech/');
    	$this_docgroup = "diversity" if ($url =~ 'nativeresources');
     	$this_docgroup = "eplan" if ($url =~ 'eplan');
     	$this_docgroup = "evaluation" if ($url =~ 'http://www.tea.state.tx.us/opge/progeval/OutOfScho');
     	$this_docgroup = "evaluation" if ($url =~ 'http://www.tea.state.tx.us/opge/progeval/HighSchoo');
     	$this_docgroup = "evaluation" if ($url =~ 'http://www.tea.state.tx.us/opge/progeval/LimitedEn');
   		$this_docgroup = "family" if ($url =~ '/connections/');
    	$this_docgroup = "family" if ($url =~ '/family');
    	$this_docgroup = "family" if ($url =~ '/pubs/fam');
    	$this_docgroup = "family" if ($url =~ '/prep/');
    	$this_docgroup = "family" if ($url =~ 'nationalpirc.org');
    	$this_docgroup = "family" if ($url =~ '/learning/');
    	$this_docgroup = "family" if ($url =~ 'adobeconnect.com/p7mejnh1nx0');
   		$this_docgroup = "landscape" if ($url =~ 'landscape');
    	$this_docgroup = "landscape" if ($url =~ 'tx-progress');
    	$this_docgroup = "landscape" if ($url =~ 'progress');
    	$this_docgroup = "landscape" if ($url =~ '/pubs/pic01');
	   	$this_docgroup = "mathsci" if ($url =~ '/classroom-compass');
    	$this_docgroup = "mathsci" if ($url =~ '/connectingkids');
    	$this_docgroup = "mathsci" if ($url =~ '/mosaic');
    	$this_docgroup = "mathsci" if ($url =~ '/ms');
    	$this_docgroup = "mathsci" if ($url =~ '/quick-takes');
   		$this_docgroup = "mathsci" if ($url =~ '/scimast');
    	$this_docgroup = "mathsci" if ($url =~ '/scimath/comp');
    	$this_docgroup = "mathsci" if ($url =~ '/scimath/quick');
    	$this_docgroup = "mathsci" if ($url =~ '/scimath/msframework');
    	$this_docgroup = "mathsci" if ($url =~ 'a-special-place');
     	$this_docgroup = "nclb" if ($url =~ '/rel/NCLBA');
     	$this_docgroup = "nsdc" if ($url =~ 'NSDC');
    	$this_docgroup = "pasopartners" if ($url =~ '/paso');
    	$this_docgroup = "policy" if ($url =~ '/policy');
   		$this_docgroup = "policy" if ($url =~ '/rel/policy');
 	   	$this_docgroup = "reading" if ($url =~ '/read');
    	$this_docgroup = "reading" if ($url =~ '/reading/');
    	$this_docgroup = "reading" if ($url =~ '/pubs/read');
	   	$this_docgroup = "rel" if ($url =~ '/rel/');
    	$this_docgroup = "research" if ($url =~ 'www.tea.state.tx.us/comm/06pd_finalreport.pdf');
    	$this_docgroup = "research" if ($url =~ 'sai_sedlbrieffinal.pdf');
    	$this_docgroup = "research" if ($url =~ '/re/');
    	$this_docgroup = "research" if ($url =~ 'www.tea.state.tx.us/opge/progeval/LimitedEnglish/lep_0807.pdf');
    	$this_docgroup = "hsrti" if ($url =~ 'hsrti');
    	$this_docgroup = "secac" if ($url =~ 'secac');
    	$this_docgroup = "secc" if ($url =~ 'secc\.sedl');
    	$this_docgroup = "sedlinsights" if (($url =~ 'insights') && ($url !~ 'policy\/insights'));
    	$this_docgroup = "sedlletter" if (($url =~ 'sedletter') || ($url =~ 'sedlletter') || ($url =~ 'sedl-letter'));
    	$this_docgroup = "sedlmonthly" if (($url =~ 'sedlmonthly') || ($url =~ 'sedl-monthly'));
    	$this_docgroup = "teaching" if ($url =~ '/pubs/teaching');
    	$this_docgroup = "teaching" if ($url =~ '/pubs/tl');
    	$this_docgroup = "teaching" if ($url =~ '/reflection.pdf');
     	$this_docgroup = "teaching" if ($url =~ '/pubs/pic04/');
     	$this_docgroup = "teaching" if ($url =~ '/pubs/policy23/');
  		$this_docgroup = "technology" if ($url =~ '/tap/');
	   	$this_docgroup = "technology" if ($url =~ '/tapinto/');
    	$this_docgroup = "technology" if ($url =~ '/pubs/tec');
    	$this_docgroup = "txcc" if ($url =~ 'txcc\.sedl');
	   	$this_docgroup = "ws" if ($url =~ '/ws/');
	return($this_docgroup);
}
#################################################################
## END SUBROUTINE: determine_documentgroup_by_url
#################################################################

######################################
## START: SUBROUTINE show_form_number_list
######################################
sub show_form_number_list {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	my $start_num = $_[2];
	my $end_num = $_[3];
print<<EOM;
<SELECT NAME="$field_name" id="$field_name">
EOM
	my $num_counter = $start_num;
		while ($num_counter <= $end_num) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $num_counter);
			print "<OPTION VALUE=\"$num_counter\" $selected>$num_counter</OPTION>\n";
			$num_counter++;
		} # END WHILE
	print "</SELECT>\n";
# SAMPLE USAGE: &commoncode::print_year_menu($site_current_year, $site_current_year + 3, "");
######################################
} # END: SUBROUTINE show_form_number_list
######################################


######################################
## START: SUBROUTINE show_form_verification
######################################
sub show_form_verification {
	my $timestamp = $_[0];

	###############################################
	## START: PRINT FIELDS FOR FORM VERIFICATION
	###############################################
	my $num1 = substr($timestamp, 13, 1);
	   $num1 = "1" if ($num1 eq '0');
	my $num2 = substr($timestamp, 12, 1);
	   $num2 = "2" if ($num2 eq '0');
	my $real_sum = $num1 + $num2;

print<<EOM;
<strong>Verification</strong><br>
<em>(Hint: You need to type the number $real_sum in the box below.)</em>
<br>
<span style="color:#000000">Please enter the <label for="user_calc"><strong>s</strong><strong>u</strong><strong>m</strong> of <strong>$num1</strong> and <strong>$num2</strong>:</label></span>
<input type="text" name="user_calc" id="user_calc" SIZE="4">
<input type="hidden" name="real_calc" value="$real_sum">
EOM


######################################
} # END: SUBROUTINE show_form_verification
######################################


#######################################
# START: SUBROUTINE display_hash
#######################################
sub display_hash {
	my ($key_label, $value_label, $header_color, $padding_for_tablecells, %the_hash) = @_;
	## START: SET DEFAULT SETTINGS
	$header_color = "#ebebeb;" if ($header_color eq '');
	$padding_for_tablecells = "4" if ($padding_for_tablecells eq '');
	$key_label = "KEY" if ($key_label eq '');
	$value_label = "VALUE" if ($value_label eq '');
	## END: SET DEFAULT SETTINGS
print<<EOM;
<table border="1" cellpadding="$padding_for_tablecells" cellspacing="0">
<tr style="background-color:$header_color;">
	<td><strong>$key_label</strong></td>
	<td><strong>$value_label</strong></td>
</tr>
EOM
	my $key = "";
	foreach $key (sort keys %the_hash) {
		print "<tr><td>$key</td><td>$the_hash{$key}</td></tr>\n";
	}
	print "</table>";
} # END SUBROUTINE
#######################################
# END: SUBROUTINE display_hash
#######################################


############################
## START: SUBROUTNE cleanurl
############################
sub cleanurl {
my $cleanitem = $_[0];
   $cleanitem =~ s/\=/\%3D/g;
   $cleanitem =~ s/\#/\%23/g;
   $cleanitem =~ s/\+/\%2B/g;
   $cleanitem =~ s/\?/\%3F/g;
   $cleanitem =~ s/\&/\%26/g;
   $cleanitem =~ s/\%20/xxyyzz/g;
   $cleanitem = $cleanitem;
}
############################
## END: SUBROUTNE cleanurl
############################


1;

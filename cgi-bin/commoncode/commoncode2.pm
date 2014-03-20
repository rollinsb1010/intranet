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
## START: SUBROUTINE print_year_menu
######################################
sub print_year_menu {
	my $field_name = $_[0];
	my $start_year = $_[1];
	my $end_year = $_[2];
	my $previous_selection = $_[3];
print<<EOM;
<select name="$field_name" id="$field_name">
<option value=\"\">year</option>
EOM
	my $year_counter = $start_year;
		while ($year_counter <= $end_year) {
			my $selected = "";
			   $selected = "SELECTED" if ($previous_selection == $year_counter);
			print "<option value=\"$year_counter\" $selected>$year_counter</option>\n";
			$year_counter++;
		} # END WHILE
	print "</select>\n";
# SAMPLE USAGE: &print_year_menu($site_current_year, $site_current_year + 3, "");
######################################
} # END: SUBROUTINE print_year_menu
######################################


#################################################################
## START SUBROUTINE: CONVERT MYSQL FORMATTED DATE TO PRETTY DATE
#################################################################
sub date2standard {
	my $date2transform = $_[0];
	   $date2transform =~ s/\ //g;
	   $date2transform =~ s/\-/\//g;
	my ($thisyear, $thismonth, $thisdate) = split(/\//,$date2transform);
	   $date2transform = "$thismonth\/$thisdate\/$thisyear";
	   $date2transform = "" if $thismonth eq '';
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
   return($pretty_time);
}
####################################################################
## END: CONVERT DATESTAMP TO PRETTY DATE/TIME FORMAT
####################################################################


#################################################################
## START SUBROUTINE: cleanthisfordb
#################################################################
sub cleanthisfordb {
my $dirtyitem = $_[0];
	my $clean_for_xss = $_[1];
	   $clean_for_xss = "remove-xss" if ($clean_for_xss eq '');

	## PREVENT CROSS-SIRE SCRIPTING <script> ATTACKS
	if ($clean_for_xss eq 'remove-xss') {
		if (($dirtyitem =~ '\<script') || ($dirtyitem =~ 'script\>')) {
			&send_email_to_webmaster("XSS ATTACK REPORT", $dirtyitem);
		}
		$dirtyitem =~ s/\<script/ \< /gi; # replace opening script tag with bar opening tag
		$dirtyitem =~ s/script\>/ \> /gi; # replace closing script tag with bar opening tag
	}

	## PREVENT SQL INJECTION
	$dirtyitem =~ s/\\//g; # REMOVE BACKSLASH IN CASE CHARACTERS ARE ALREADY BACKSLASHED
	$dirtyitem =~ s/\%22/"/g;
	$dirtyitem =~ s/�/"/g;			
	$dirtyitem =~ s/�/"/g;			
	$dirtyitem =~ s/"/\\"/g;
	$dirtyitem =~ s/'/\\'/g;
	$dirtyitem =~ s/�/\\�/g;
	$dirtyitem =~ s/�/\\�/g;
	return($dirtyitem);
}
#################################################################
## END SUBROUTINE: cleanthisfordb
#################################################################


#################################################################
## START SUBROUTINE: send_email_to_webmaster
#################################################################
sub send_email_to_webmaster {
	my $message_title = $_[0];
	my $message_content = $_[1];

	# SEND E-MAIL
	my $mailprog = '/usr/sbin/sendmail -t';
	my $recipient = $recipient_email;
  		$recipient =~ s/\\//g;
	my $webmaster_address = 'webmaster@sedl.org';

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


This is an automated e-mail, and the source code is at /staff/perlmodules/commoncode/commoncode.pm


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
## END SUBROUTINE: getFullStateName
#################################################################

######################################
## START: SUBROUTINE print_yes_no_menu
######################################
sub printform_yes_no_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	
	my @item_value = ("yes", "no");
	my @item_label = ("yes", "no");
	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<select name="$field_name" id="$field_name" alt="$previous_selection">
EOM
		while ($item_counter <= $count_total_items) {
			my $selected = "";
			   $selected = "SELECTED" if (($previous_selection eq $item_value[$item_counter]) && ($previous_selection ne ''));
			print "<option VALUE=\"$item_value[$item_counter]\" $selected>$item_label[$item_counter]</option>\n";
			$item_counter++;
		} # END WHILE
	print "</select>\n";
######################################
} # END: SUBROUTINE print_yes_no_menu
######################################


######################################
## START: SUBROUTINE printform_sedl_unit_menu
######################################
sub printform_sedl_unit_menu {
	my $field_name = $_[0];
	my $previous_selection = $_[1];
	my $mixed_case = $_[2];
	   $mixed_case = "no" if ($mixed_case eq '');
	my $add_sedlwide_option = $_[3];
	   $add_sedlwide_option = "no" if ($add_sedlwide_option eq '');

	my @item_value = ("", "ADMINISTRATIVE SERVICES", "AFTERSCHOOL, FAMILY, AND COMMUNITY (AFC)", "COMMUNICATIONS", "DEVELOPMENT", "DISABILITY RESEARCH TO PRACTICE (DRP)", "EXECUTIVE OFFICE", "IMPROVING SCHOOL PERFORMANCE (ISP) SECC", "IMPROVING SCHOOL PERFORMANCE (ISP) TXCC", "RESEARCH AND EVALUATION (R&E)");
	my @item_label = ("(select one)", "ADMINISTRATIVE SERVICES", "AFTERSCHOOL, FAMILY, AND COMMUNITY (AFC)", "COMMUNICATIONS", "DEVELOPMENT", "DISABILITY RESEARCH TO PRACTICE (DRP)", "EXECUTIVE OFFICE", "IMPROVING SCHOOL PERFORMANCE (ISP) SECC", "IMPROVING SCHOOL PERFORMANCE (ISP) TXCC", "RESEARCH AND EVALUATION (R&E)");

	if ($mixed_case eq 'no') {
		$previous_selection = "ADMINISTRATIVE SERVICES" if ($previous_selection eq 'AD');
		$previous_selection = "AFTERSCHOOL, FAMILY, AND COMMUNITY (AFC)" if ($previous_selection eq 'AFC');
		$previous_selection = "COMMUNICATIONS" if ($previous_selection eq 'COM');
		$previous_selection = "DEVELOPMENT" if ($previous_selection eq 'DEV');
		$previous_selection = "DISABILITY RESEARCH TO PRACTICE (DRP)" if ($previous_selection eq 'DRP');
		$previous_selection = "EXECUTIVE OFFICE" if ($previous_selection eq 'EO');
		$previous_selection = "COMMUNICATIONS" if ($previous_selection eq 'COM');
		$previous_selection = "IMPROVING SCHOOL PERFORMANCE (ISP) SECC" if ($previous_selection eq 'SECC');
		$previous_selection = "IMPROVING SCHOOL PERFORMANCE (ISP) TXCC" if ($previous_selection eq 'TXCC');
		$previous_selection = "RESEARCH AND EVALUATION (R&E)" if ($previous_selection eq 'RE');
	} else {
		$previous_selection = "Administrative Services" if ($previous_selection eq 'AD');
		$previous_selection = "Afterschool, Family, and Community (AFC)" if ($previous_selection eq 'AFC');
		$previous_selection = "Communications" if ($previous_selection eq 'COM');
		$previous_selection = "Development" if ($previous_selection eq 'DEV');
		$previous_selection = "Disability Research to Practice (DRP)" if ($previous_selection eq 'DRP');
		$previous_selection = "Executive Office" if ($previous_selection eq 'EO');
		$previous_selection = "Communications" if ($previous_selection eq 'COM');
		$previous_selection = "Improving School Performance (ISP) SECC" if ($previous_selection eq 'SECC');
		$previous_selection = "Improving School Performance (ISP) TXCC" if ($previous_selection eq 'TXCC');
		$previous_selection = "Research and Evaluation (R&E)" if ($previous_selection eq 'RE');

		@item_value = ("", "Administrative Services", "Afterschool, Family, and Community (AFC)", "Communications", "Development", "Disability Research to Practice (DRP)", "Executive Office", "Improving School Performance (ISP) SECC", "Improving School Performance (ISP) TXCC", "Research and Evaluation (R&E)");
		@item_label = ("(select one)", "Administrative Services", "Afterschool, Family, and Community (AFC)", "Communications", "Development", "Disability Research to Practice (DRP)", "Executive Office", "Improving School Performance (ISP) SECC", "Improving School Performance (ISP) TXCC", "Research and Evaluation (R&E)");
	}

	if ($add_sedlwide_option eq 'yes') {
		push (@item_value, "SEDL");
		push (@item_label, "SEDL");
	}

	my $item_counter = "0";
	my $count_total_items = $#item_value;
print<<EOM;
<select name="$field_name" id="$field_name" alt="$previous_selection">
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


####################################################################
## START: SUBROUTINE get_side_nav_menu_code (INTRANET SIDE NAVIGATION)
####################################################################
sub get_side_nav_menu_code {
	use DBI;
	my $dsn = "DBI:mysql:database=intranet;host=localhost";

	my $pid = $_[0];

	my $show_s = "";
	my $show_sg = "";
	my $currentpage_leftnav = "";

	#################################################################
	## START: QUERY INTRANET_PAGES DB TO GET THE DATA FOR THIS PAGE
	#################################################################
	my $command = "select intranet_section.is_id, intranet_section_group.isg_id FROM intranet_section, intranet_section_group, intranet_pages 
					WHERE intranet_section.is_id = intranet_section_group.isg_is_id
					AND intranet_section_group.isg_id = intranet_pages.page_isg_id";
#	   $command .= " AND intranet_section.is_id like '$show_s'" if ($show_s ne '');
#	   $command .= " AND intranet_section.is_id_text like '$section'" if ($section ne '');
#	   $command .= " AND intranet_section_group.isg_id LIKE '$show_sg'" if ($show_sg ne '');
#	   $command .= " AND intranet_section_group.isg_id_text LIKE '$group'" if ($group ne '');
#	   $command .= " AND intranet_pages.page_id_text like '$page'" if ($page ne '');
	   $command .= " AND intranet_pages.page_id like '$pid'" if ($pid ne '');
#	   $command .= " LIMIT 1" if ($pid eq '');

	my $dbh = DBI->connect($dsn, "intranetuser", "limited");
	my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
	$sth->execute;
	my $num_matches = $sth->rows;

	while (my @arr = $sth->fetchrow) {
		my ($is_id, $isg_id) = @arr;
			$show_s = $is_id;
			$show_sg = $isg_id;
	} # END WHILE LOOP
	#################################################################
	## END: QUERY INTRANET_PAGES DB TO GET THE DATA FOR THIS PAGE
	#################################################################

		#################################
		## START: BUILD SIDE NAVIGATION
		#################################
		my $command = "select intranet_section.is_id, intranet_section.is_id_text, intranet_section.is_title, 
								intranet_section_group.isg_id, intranet_section_group.isg_id_text, intranet_section_group.isg_title, 
								intranet_pages.* 
						FROM intranet_section, intranet_section_group, intranet_pages 
						WHERE intranet_section.is_id = intranet_section_group.isg_is_id
						AND intranet_section_group.isg_id = intranet_pages.page_isg_id
						AND intranet_section.is_id like '$show_s'
						order by intranet_section_group.isg_seq_num, intranet_pages.page_group_seq";
		my $dbh = DBI->connect($dsn, "intranetuser", "limited");
		my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
		$sth->execute;
		my $num_matches = $sth->rows;
#print "COMMAND: $command <P>MATCHES: $num_matches";
#		$currentpage_leftnav .= "<TABLE BORDER=0 CELLPADDING= 2 CELLSPACING=0>";
		$currentpage_leftnav .= "<div id=\"menu-main\">";

		my $last_isg_id = "";
		my $set_section_title = "no";
		my $inside_active_group_block = "no";
		my $counter_passes = "0";
		
		## GET THE RESULTS OF THE QUERY
		while (my @arr = $sth->fetchrow) {
		    my ($is_id, $is_id_text, $is_title, $isg_id, $isg_id_text, $isg_title, $page_id, $page_id_text, $page_isg_id, $page_group_seq, $page_title, $page_title_leftnav, $page_display_in_leftnav, $page_content, $page_redirect_tourl, $page_redirect_delay, $page_added_date, $page_added_by, $edit_committed, $edit_author) = @arr;
			$counter_passes++;
			## START: SET SECTION TITLE AT TOP OF LEFT NAV
			if (($is_title ne '') && ($set_section_title ne 'yes')) {
#				$currentpage_leftnav .= "<TR><TD COLSPAN=2><H2>Inside...<BR>$section_title</H2></TD></TR>";
				$currentpage_leftnav .= "<a href=\"/cgi-bin/mysql/staff/index.cgi?section=$is_id_text\"><img src=\"/staff/images/sidenav/$is_id.gif\" height=\"40\" width=\"180\" alt=\"Inside... $is_title\" class=\"noBorder decoration\"></a>\n<ul>\n";
				$set_section_title = "yes";
			}
			## END: SET SECTION TITLE AT TOP OF LEFT NAV

			## START: PRINT SECTION GROUP, IF NEEDED
			if ($isg_id ne $last_isg_id) {
				if ($show_sg == $isg_id) {
#					$currentpage_leftnav .= "<TR><TD VALIGN=\"TOP\" COLSPAN=2 BGCOLOR=\"#FAF3A5\"><strong><FONT COLOR=\"#B28E39\">$section_group_title</FONT></strong></TD></TR>";
					$currentpage_leftnav .= "</li>\n" if ($counter_passes > 1);
					if ($isg_id_text eq '') {
						$currentpage_leftnav .= "<li><a href=\"/cgi-bin/mysql/staff/index.cgi?show_sg=$isg_id\" id=\"active_menu\">$isg_title</a>\n	<div id=\"menu-main2\"><ul>\n";
					} else {
						$currentpage_leftnav .= "<li><a href=\"/cgi-bin/mysql/staff/index.cgi?group=$isg_id_text\" id=\"active_menu\">$isg_title</a>\n	<div id=\"menu-main2\"><ul>\n";
					}
					$inside_active_group_block = "yes";
				} else {
					if ($inside_active_group_block eq 'yes') {
						$currentpage_leftnav .= "	</ul></div></li>\n";
						$inside_active_group_block = "no";
					} else {
#						$currentpage_leftnav .= "</li>\n";
					}
#					$currentpage_leftnav .= "<TR><TD VALIGN=\"TOP\" COLSPAN=2 BGCOLOR=\"$bgcolor\"><IMG SRC=\"/images/spacer.gif\" ALT=\"\" HEIGHT=\"2\" WIDTH=\"50\"><BR><strong><a href=\"/cgi-bin/mysql/staff/index.cgi?show_s=$temp_section&show_sg=$temp_section_group\" class=\"nu\">$temp_section_group_title</a></strong><BR><IMG SRC=\"/images/spacer.gif\" ALT=\"\" HEIGHT=\"2\" WIDTH=\"50\"></TD></TR>";
					if ($isg_id_text eq '') {
						$currentpage_leftnav .= "<li><a href=\"/cgi-bin/mysql/staff/index.cgi?show_sg=$isg_id\">$isg_title</a></li>\n";
					} else {
						$currentpage_leftnav .= "<li><a href=\"/cgi-bin/mysql/staff/index.cgi?group=$isg_id_text\">$isg_title</a></li>\n";
					}
					$inside_active_group_block = "no";
				}
			}
			## END: PRINT SECTION GROUP, IF NEEDED


			## START: PRINT GROUP PAGES, IF ANY
			if (($show_sg == $isg_id) && ($page_display_in_leftnav ne 'no')) {
				if ($pid == $page_id) {
#					$currentpage_leftnav .= "<TR><TD VALIGN=\"TOP\" BGCOLOR=\"#FFFDE5\">-</TD><TD VALIGN=\"TOP\" BGCOLOR=\"#FFFDE5\"><em>$temp_page_title</em></TD></TR>";
					if (($page_redirect_tourl ne '') && ($page_redirect_delay == 0)) {
						$currentpage_leftnav .= "	<li><a href=\"$page_redirect_tourl\" class=\"nu\" id=\"active_menu\">$page_title</a></li>\n";
					} elsif ($page_id_text eq '') {
						$currentpage_leftnav .= "	<li><a href=\"/cgi-bin/mysql/staff/index.cgi?pid=$page_id\" class=\"nu\" id=\"active_menu\">$page_title</a></li>\n";
					} else {
						$currentpage_leftnav .= "	<li><a href=\"/cgi-bin/mysql/staff/index.cgi?page=$page_id_text\" class=\"nu\" id=\"active_menu\">$page_title</a></li>\n";
					}
				} else {
#						$currentpage_leftnav .= "<TR><TD VALIGN=\"TOP\" BGCOLOR=\"#FFFDE5\">-</TD><TD VALIGN=\"TOP\" BGCOLOR=\"#FFFDE5\"><a href=\"/cgi-bin/mysql/staff/index.cgi?show_s=$section&show_sg=$section_group\" class=\"nu\">$page_title</a></TD></TR>";
					if (($page_redirect_tourl ne '') && ($page_redirect_delay == 0)) {
						$currentpage_leftnav .= "	<li><a href=\"$page_redirect_tourl\" class=\"nu\">$page_title</a></li>\n";
					} elsif ($page_id_text eq '') {
						$currentpage_leftnav .= "	<li><a href=\"/cgi-bin/mysql/staff/index.cgi?pid=$page_id\" class=\"nu\">$page_title</a></li>\n";
					} else {
						$currentpage_leftnav .= "	<li><a href=\"/cgi-bin/mysql/staff/index.cgi?page=$page_id_text\" class=\"nu\">$page_title</a></li>\n";
					}
				}
			}
			## END: PRINT GROUP PAGES, IF ANY
			$last_isg_id = $isg_id;
		}
		
		if ($inside_active_group_block eq 'yes') {
			$currentpage_leftnav .= "	</ul></div></li>\n";
			$inside_active_group_block = "no";
		}
#		$currentpage_leftnav .= "</TABLE>";
		$currentpage_leftnav .= "</ul>\n</div>\n";
		#################################
		## END: BUILD SIDE NAVIGATION
		#################################


	return($currentpage_leftnav);

}
####################################################################
## END: SUBROUTINE get_side_nav_menu_code
####################################################################


####################################################################
## START: SUBROUTINE cleanaccents2html
####################################################################
sub cleanaccents2html {
	my $cleanitem = $_[0];
	$cleanitem =~ s/�/"/g;			
	$cleanitem =~ s/�/"/g;			
	$cleanitem =~ s/�/'/g;			
	$cleanitem =~ s/�/'/g;
	$cleanitem =~ s/�/\&ndash\;/g;
	$cleanitem =~ s/�/\&mdash\;/g;
	$cleanitem =~ s/--/\&mdash\;/g;
	$cleanitem =~ s/�//g; # invisible bullet
	$cleanitem =~ s/�/.../g;
	$cleanitem =~ s/�/&iquest\;/g; 
	$cleanitem =~ s/�/&Agrave\;/g; 
	$cleanitem =~ s/�/&agrave\;/g;	
	$cleanitem =~ s/�/&Aacute\;/g;  
	$cleanitem =~ s/�/&aacute\;/g;
	$cleanitem =~ s/�/&Acirc\;/g;
	$cleanitem =~ s/�/&acirc\;/g;
	$cleanitem =~ s/�/&Atilde\;/g;
	$cleanitem =~ s/�/&atilde\;/g;
	$cleanitem =~ s/�/&Auml\;/g;
	$cleanitem =~ s/�/&auml\;/g;
	$cleanitem =~ s/�/&Eacute\;/g;
	$cleanitem =~ s/�/&eacute\;/g;
	$cleanitem =~ s/�/&Egrave\;/g;
	$cleanitem =~ s/�/&egrave\;/g;
	$cleanitem =~ s/�/&Euml\;/g;
	$cleanitem =~ s/�/&euml\;/g;
	$cleanitem =~ s/�/&Igrave\;/g;
	$cleanitem =~ s/�/&igrave\;/g;
	$cleanitem =~ s/�/&Iacute\;/g;
	$cleanitem =~ s/�/&iacute\;/g;
	$cleanitem =~ s/�/&Icirc\;/g;
	$cleanitem =~ s/�/&icirc\;/g;
	$cleanitem =~ s/�/&Iuml\;/g;
	$cleanitem =~ s/�/&iuml\;/g;
	$cleanitem =~ s/�/&Ntilde\;/g;
#	$cleanitem =~ s/�/&ntilde\;/g; # COMMENTED OUT BECAUSE IT IS SUBSTITUTING FOR THE ndash
	$cleanitem =~ s/�/&Ograve\;/g;
	$cleanitem =~ s/�/&ograve\;/g;
	$cleanitem =~ s/�/&Oacute\;/g;
#	$cleanitem =~ s/�/&oacute\;/g; # COMMENTED OUT BECAUSE IT IS SUBSTITUTING FOR THE mdash
	$cleanitem =~ s/�/&Otilde\;/g;
	$cleanitem =~ s/�/&otilde\;/g;
	$cleanitem =~ s/�/&Ouml\;/g;
	$cleanitem =~ s/�/&ouml\;/g;
	$cleanitem =~ s/�/&Ugrave\;/g;
	$cleanitem =~ s/�/&ugrave\;/g;
	$cleanitem =~ s/�/&Uacute\;/g;
	$cleanitem =~ s/�/&uacute\;/g;
	$cleanitem =~ s/�/&Ucirc\;/g;  ## THIS REPLACES THE � FOR SOME REASON
	$cleanitem =~ s/�/&ucirc\;/g;
	$cleanitem =~ s/�/&Uuml\;/g;
	$cleanitem =~ s/�/&uuml\;/g;
	$cleanitem =~ s/�/&yuml\;/g;
	return ($cleanitem);
}
####################################################################
## END: SUBROUTINE cleanaccents2html
####################################################################


1;
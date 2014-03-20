<?php
session_start();

#############################################################################
# Copyright 2014 by SEDL
# Updated by Brian Litke 1/31/2014
#############################################################################

#################################
## START: LOAD FX.PHP INCLUDES ##
#################################
include_once('FX/FX.php');
include_once('FX/server_data.php');
###############################
## END: LOAD FX.PHP INCLUDES ##
###############################

################################
## START: GRAB FORM VARIABLES ##
################################
$ready_to_change_database = "ready"; # expects 'ready' or 'not ready'
$debug = "off"; # expects 'on' or 'off'
$counter = 0;
$number_updated = 0;
$number_inserted = 0;
$number_deleted = 0;
##############################
## END: GRAB FORM VARIABLES ##
##############################


#################################
## START: QUERY MYSQL FOR ALL USER IDs
#################################
	$db = mysql_connect('localhost','intranetuser','limited');
	$db_selected = mysql_select_db('intranet',$db);
	$command_check_user = "select userid from staff_profiles";
	$result = mysql_query("$command_check_user");
	$num_rows = mysql_num_rows($result);
#	echo " - COMMAND TO LOOKUP USER IN MYSQL: $command_check_user<br>MATCHES: $num_rows<br>";
	$current_id_in_mysql;
	while ($row = mysql_fetch_array($result)) {
		$this_user_id = $row['userid'];	# ADD TO THE ARRAY
		$current_id_in_mysql[$this_user_id] = "found";
#		echo "<br>Searching Mysql for $this_user_id";
	} # END DB QUERY LOOP
#		$current_id_in_mysql['blitke'] = "not found"; # FOR TESTING ONLY
#################################
## END: QUERY MYSQL FOR ALL USER IDs
#################################


#################################
## START: FIND EMPLOYEE RECORD ##
#################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','staff','all');
$search4 -> SetDBPassword($webPW,$webUN);
#$search4 -> AddDBParam('current_employee_status','=='.'SEDL Employee');

$searchResult4 = $search4 -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData4 = current($searchResult4['data']);
###############################
## END: FIND EMPLOYEE RECORD ##
###############################


####################################
## START: DISPLAY PAGE HEADING
####################################
	if ($debug == 'on') {
?>
<html>
<head>
<title>SIMS - Staff Profiles</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>
<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">
<?php
	}
####################################
## END: DISPLAY PAGE HEADING
####################################

foreach ($searchResult4['data'] as $key => $searchData) {
	$counter++;
	##  $searchData['staff_ID'][0];
	#######################################
	## START: GRAB UPDATE FORM VARIABLES ##
	#######################################
	$update_row_ID = $searchData['c_cwp_row_ID'][0];
	$first_name = $searchData['name_first'][0];
	$middle_initial = $searchData['name_middle'][0];
	$last_name = $searchData['name_last'][0];
	$status = $searchData['current_employee_status'][0];
	$title = $searchData['job_title'][0];
	$sedl_unit = $searchData['primary_SEDL_workgroup'][0];
	$other_SEDL_workgroup = "";
	for($i=0 ; $i<count($searchData['other_SEDL_workgroup'][0]) ; $i++) { // TRANSFORM CHECKBOX ARRAY FOR PROCESSING
		$other_SEDL_workgroup .= $searchData['other_SEDL_workgroup'][$i]."\r"; 
	}

	$email = $searchData['email'][0];
	$phone = $searchData['phone_full'][0];
	$ext = $searchData['phone_ext'][0];
	$work_hrs = $searchData['work_hrs'][0];
	$suite_number = $searchData['suite_number'][0];
	$start_date = $searchData['empl_start_date'][0];
	$end_date = $searchData['empl_end_date'][0];
	$birthmonth = $searchData['birthmonth'][0];
	$birthday = $searchData['birthday'][0];
	$on_mgmt_council = $searchData['on_mgmt_council'][0];
	$timesheet_name = $searchData['name_timesheet'][0];
	$empl_type = $searchData['employee_type'][0];
	$fte = $searchData['fte'][0];
	$imm_spvsr = $searchData['immediate_supervisor_sims_user_ID'][0];
		$immediate_supervisor_sims_user_ID = $imm_spvsr; //  for use when sending to mysql
	$pba = $searchData['bgt_auth_primary_sims_user_ID'][0];
		$bgt_auth_primary_sims_user_ID = $pba; //  for use when sending to mysql
	$pr_dir = $searchData['pr_dir'][0];
	$cpo = $searchData['cpo'][0];
	$time_leave_admin = $searchData['is_time_leave_admin'][0];
	$time_leave_admin_sims_user_ID = $searchData['time_leave_admin_sims_user_ID'][0]; //  for use when sending to mysql
	$pos_descr_admin = $searchData['pos_descr_admin'][0];
	$pos_descr_admin_other = $searchData['pos_descr_admin_other'][0];
	$plan_agrmt_admin = $searchData['plan_agrmt_admin'][0];
	$plan_agrmt_admin_other = $searchData['plan_agrmt_admin_other'][0];
	$lv_appr_cc = $searchData['lv_appr_cc'][0];
	$is_bgt_auth = $searchData['is_budget_authority'][0];
	$is_supervisor = $searchData['is_supervisor'][0];
	$is_auth_rep = $searchData['is_time_leave_admin'][0];
	$allow_variable_timesheet_hours = $searchData['allow_variable_timesheet_hours'][0];
	$sims_user_ID = $searchData['sims_user_ID'][0];
	$sims_access_main_menu = $searchData['sims_access_main_menu'][0];
	$sims_access_time_leave = $searchData['sims_access_time_leave'][0];
	$sims_access_supervisors = $searchData['sims_access_supervisors'][0];
	$sims_access_budget_authorities = $searchData['sims_access_budget_authorities'][0];
	$sims_access_planning_agrmts = $searchData['sims_access_planning_agrmts'][0];
	$sims_access_position_descr = $searchData['sims_access_position_descr'][0];
	$start_date_mysql = $searchData['c_empl_start_date_mysql'][0];

	if ($debug == 'on') {
		echo "<p>$counter Processing FileMaker record for $first_name $last_name<br>";
	}
	######################################################
	## START: BEFORE UPDATE, CHECK IF USER EXISTS IN MYSQL
	######################################################
	// IF USER ID NOT FOUND IN MYSQL, INITIATE RECORD
	$found = $current_id_in_mysql[$sims_user_ID];
	if ($found == '') {$found = "not found";}

	if ($debug == 'on') {
		echo " - COMMAND TO LOOKUP USER IN MYSQL: $sims_user_ID was $found (STATUS: $status)<br>";
	}
	

	if (($found != 'found') && ($status == 'SEDL Employee')) {
		$db = mysql_connect('localhost','intranetuser','limited');
		$db_selected = mysql_select_db('intranet',$db);
		$strong_pwd = crypt('password');
		$command_insert_user = "INSERT INTO staff_profiles 
		(fm_record_id, firstname, middleinitial, lastname, jobtitle, phone, userid, email, phoneext, birthmonth, birthday, timesheetname, department_abbrev, mgmtcouncil, room_number, start_date, supervised_by, automated_sentence, photo_permissions, strong_pwd, empl_type, degree, other_SEDL_workgroup, immediate_supervisor_sims_user_ID, bgt_auth_primary_sims_user_ID)
		VALUES ('$update_row_ID', '$first_name', '$middle_initial', '$last_name', '$title', '$phone', '$sims_user_ID', '$email', '$ext', '$birthmonth', '$birthday', '$timesheet_name', '$sedl_unit', '$on_mgmt_council', '$suite_number', '$start_date_mysql', '$imm_spvsr', '', 'Needed', '$strong_pwd', '$empl_type', '', '$is_bgt_auth', '$is_auth_rep', '$other_SEDL_workgroup', '$immediate_supervisor_sims_user_ID', '$bgt_auth_primary_sims_user_ID', '$time_leave_admin_sims_user_ID')";

		if ($ready_to_change_database == 'ready') {
			$result = mysql_query("$command_insert_user");
			$num_inserted++;
		} else {
			if ($debug == 'on') {
				echo " - STAFF MEMBER NOT FOUND<BR>COMMAND TO INSERT RECORD INTO MYSQL WILL BE: $command_insert_user<br>";
			}
			$num_inserted++;
		}
	} # END IF
	######################################################
	## END: BEFORE UPDATE, CHECK IF USER EXISTS IN MYSQL
	######################################################

	##################################################
	## START: UPDATE THE MYSQL staff_profiles TABLE ##
	##################################################
	// CONNECT TO mySQL
	
	$db = mysql_connect('localhost','intranetuser','limited');
	
	if (!$db) {
		die(' - Failed to connect to MySQL<br> '. mysql_error().'<br>');
	} else {
#		if ($debug == 'on') {
#			echo ' - Connected to: MySQL<br>';	
#		}
	}
	
	// CONNECT TO staff_profiles database
	
	$db_selected = mysql_select_db('intranet',$db);
	
	if (!$db_selected) {
		if ($debug == 'on') {
			echo 'no connection';
		}
		die(' - Can\'t use MySQL database intranet : ' . mysql_error().'<br>');
	} else {
#		echo ' - Connected to: MySQL database intranet<br>';
	}
			
	if (($status == 'SEDL Employee') && ($found == 'found')) {
		## BACKSLASH VARIABLES BEFORE SENDING TO MYSQL
		$first_name = addslashes($first_name);
		$middle_initial = addslashes($middle_initial);
		$last_name = addslashes($last_name);
		$title = addslashes($title);
		$phone = addslashes($phone);
		$email = addslashes($email);
		$ext = addslashes($ext);
		$timesheet_name = addslashes($timesheet_name);
		$sedl_unit = addslashes($sedl_unit);
		$on_mgmt_council = addslashes($on_mgmt_council);
		$suite_number = addslashes($suite_number);
		$start_date_mysql = addslashes($start_date_mysql);
		$imm_spvsr = addslashes($imm_spvsr);
		$empl_type = addslashes($empl_type);
		$other_SEDL_workgroup = addslashes($other_SEDL_workgroup);
		$immediate_supervisor_sims_user_ID = addslashes($immediate_supervisor_sims_user_ID);
		$bgt_auth_primary_sims_user_ID = addslashes($bgt_auth_primary_sims_user_ID);
		$time_leave_admin_sims_user_ID = addslashes($time_leave_admin_sims_user_ID);

		$command = 
		"UPDATE staff_profiles 
		SET 
		firstname = '$first_name', 
		middleinitial = '$middle_initial', 
		lastname='$last_name', 
		jobtitle='$title', 
		phone='$phone', 
		userid='$sims_user_ID', 
		email='$email', 
		phoneext='$ext', 
		birthmonth='$birthmonth', 
		birthday='$birthday', 
		timesheetname='$timesheet_name', 
		department_abbrev='$sedl_unit', 
		mgmtcouncil='$on_mgmt_council', 
		room_number='$suite_number', 
		start_date='$start_date_mysql', 
		supervised_by='$imm_spvsr', 
		empl_type='$empl_type', 
		is_bgt_auth='$is_bgt_auth', 
		is_auth_rep='$is_auth_rep',
		other_SEDL_workgroup='$other_SEDL_workgroup',
		immediate_supervisor_sims_user_ID='$immediate_supervisor_sims_user_ID',
		bgt_auth_primary_sims_user_ID='$bgt_auth_primary_sims_user_ID',
		time_leave_admin_sims_user_ID='$time_leave_admin_sims_user_ID'

		WHERE fm_record_id = '$update_row_ID'";
		
		if ($debug == 'on') {
			echo " - COMMAND TO UPDATE THIS RECORD WILL BE: $command<br>";			
		}	
			$num_updated++;

		if ($ready_to_change_database == 'ready') {
			$update = mysql_query($command);

			if (!$update) {
			   die('Invalid MySQL query: ' . mysql_error());
			} else {
			
				//echo 'hello';
				//echo '<br>Update Successful!';
			} # END IF 
		}

	} else {
		if ($found == 'found') {
			$command = 
			"delete from staff_profiles WHERE userid='$sims_user_ID'"; 
				if ($debug == 'on') {
					echo " - COMMAND TO DELETE THIS RECORD WILL BE: $command<br>";			
				} # END IF
			if ($ready_to_change_database == 'ready') {
				$update = mysql_query($command);
			} # END IF
				$num_deleted++;
		} else {
			if ($debug == 'on') {
				echo " - NOT IN MYSQL, SO NO DELETION NEEDED:<br>";			
			} # END IF
		} # END IF
	} # END IF/ELSE
			

	if ($debug == 'on') {
		echo "<hr>";
	}
}
##################################################
## END: UPDATE THE MYSQL staff_profiles TABLE ##
##################################################

	if ($debug == 'on') {
		echo "<P>NUMBER UPDATED: $num_updated</p>";
		echo "<P>NUMBER INSERTED: $num_inserted</p>";
		echo "<P>NUMBER DELETED: $num_deleted</p>";
	}

// REGENERATE INTRANET AND PUBLIC STAFF PAGES
exec('/home/httpd/html/staff/personnel/staffprofiles.cgi'); 


 ?>
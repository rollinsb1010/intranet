<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2013 by SEDL
#
# Written by Eric Waters - September 2013
#############################################################################
$debug = 'off'; //change to "on" to print variable values at top of table
###############################
## START: LOAD FX.PHP INCLUDES
###############################
include_once('FX/FX.php');
include_once('FX/server_data.php');
###############################
## END: LOAD FX.PHP INCLUDES
###############################

###############################
## START: GRAB FORM VARIABLES
###############################
//$sortfield = $_GET['sortfield'];
$timesheet_ID = $_GET['Timesheet_ID'];
$_SESSION['timesheet_ID'] = $_GET['Timesheet_ID'];
$action = $_GET['action'];
$new_row = $_GET['new_row'];
$new_row_ID = $_GET['new_row_ID'];
$row_ID = $_GET['edit_row_ID'];

$signature_status = '';
//echo '<br>Timesheet ID: '.$timesheet_ID;
//echo '<br>Action: '.$action;
//echo '<br>new_row: '.$new_row;
//echo '<br>new_row_ID: '.$new_row_ID;
//echo '<br>edit_row_ID: '.$row_ID;
###############################
## END: GRAB FORM VARIABLES
###############################

#####################################################
## START: FIND REGULAR WORK HOURS FOR THIS TIMESHEET
#####################################################
//echo $row_ID;
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('Timesheet_ID','=='.$timesheet_ID);
$search -> AddDBParam('HrsType','WkHrsReg');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam ($sortfield,'descend');


$searchResult = $search -> FMFind();
//$_SESSION['wk_hrs_data'] = $searchResult;

//echo $searchResult['errorCode'];
//echo '<br>RegularWkHrs FoundCount: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);

$_SESSION['signer_status_owner'] = $recordData['timesheets::Signer_status_owner'][0];
$_SESSION['signer_status_imm_spvsr'] = $recordData['timesheets::Signer_status_imm_spvsr'][0];
$_SESSION['signer_status_pba'] = $recordData['timesheets::Signer_status_pba'][0];
$_SESSION['signer_status_bgt_auth_1'] = $recordData['timesheets::Signer_status_bgt_auth_1'][0];
$_SESSION['signer_status_bgt_auth_2'] = $recordData['timesheets::Signer_status_bgt_auth_2'][0];
$_SESSION['signer_status_bgt_auth_3'] = $recordData['timesheets::Signer_status_bgt_auth_3'][0];
$_SESSION['signer_status_bgt_auth_4'] = $recordData['timesheets::Signer_status_bgt_auth_4'][0];
$_SESSION['signer_status_bgt_auth_5'] = $recordData['timesheets::Signer_status_bgt_auth_5'][0];
$_SESSION['signer_status_bgt_auth_6'] = $recordData['timesheets::Signer_status_bgt_auth_6'][0];
$_SESSION['signer_status_bgt_auth_7'] = $recordData['timesheets::Signer_status_bgt_auth_7'][0];
$_SESSION['signer_status_bgt_auth_8'] = $recordData['timesheets::Signer_status_bgt_auth_8'][0];
$_SESSION['signer_status_bgt_auth_OT'] = $recordData['timesheets::Signer_status_bgt_auth_OT'][0];

$_SESSION['signer_ID_bgt_auth_1'] = $recordData['timesheets::Signer_ID_bgt_auth_1'][0];
$_SESSION['signer_ID_bgt_auth_2'] = $recordData['timesheets::Signer_ID_bgt_auth_2'][0];
$_SESSION['signer_ID_bgt_auth_3'] = $recordData['timesheets::Signer_ID_bgt_auth_3'][0];
$_SESSION['signer_ID_bgt_auth_4'] = $recordData['timesheets::Signer_ID_bgt_auth_4'][0];
$_SESSION['signer_ID_bgt_auth_5'] = $recordData['timesheets::Signer_ID_bgt_auth_5'][0];
$_SESSION['signer_ID_bgt_auth_6'] = $recordData['timesheets::Signer_ID_bgt_auth_6'][0];
$_SESSION['signer_ID_bgt_auth_7'] = $recordData['timesheets::Signer_ID_bgt_auth_7'][0];
$_SESSION['signer_ID_bgt_auth_8'] = $recordData['timesheets::Signer_ID_bgt_auth_8'][0];
$_SESSION['signer_ID_bgt_auth_OT'] = $recordData['timesheets::Signer_ID_bgt_auth_OT'][0];
$_SESSION['signer_ID_owner'] = $recordData['timesheets::Signer_ID_owner'][0];
$_SESSION['signer_ID_imm_spvsr'] = $recordData['timesheets::StaffImmediateSupervisor'][0];
$_SESSION['signer_ID_pba'] = $recordData['timesheets::StaffPrimaryBudgetAuthority'][0];

$_SESSION['signer_timestamp_owner'] = $recordData['timesheets::Signer_Timestamp_owner'][0];
$_SESSION['signer_timestamp_bgt_auth_1'] = $recordData['timesheets::Signer_Timestamp_bgt_auth_1'][0];
$_SESSION['signer_timestamp_bgt_auth_2'] = $recordData['timesheets::Signer_Timestamp_bgt_auth_2'][0];
$_SESSION['signer_timestamp_bgt_auth_3'] = $recordData['timesheets::Signer_Timestamp_bgt_auth_3'][0];
$_SESSION['signer_timestamp_bgt_auth_4'] = $recordData['timesheets::Signer_Timestamp_bgt_auth_4'][0];
$_SESSION['signer_timestamp_bgt_auth_5'] = $recordData['timesheets::Signer_Timestamp_bgt_auth_5'][0];
$_SESSION['signer_timestamp_bgt_auth_6'] = $recordData['timesheets::Signer_Timestamp_bgt_auth_6'][0];
$_SESSION['signer_timestamp_bgt_auth_7'] = $recordData['timesheets::Signer_Timestamp_bgt_auth_7'][0];
$_SESSION['signer_timestamp_bgt_auth_8'] = $recordData['timesheets::Signer_Timestamp_bgt_auth_8'][0];
$_SESSION['signer_timestamp_bgt_auth_OT'] = $recordData['timesheets::Signer_Timestamp_bgt_auth_OT'][0];
$_SESSION['signer_timestamp_imm_spvsr'] = $recordData['timesheets::Signer_Timestamp_imm_spvsr'][0];
$_SESSION['signer_timestamp_pba'] = $recordData['timesheets::Signer_Timestamp_pba'][0];

$_SESSION['signer_fullname_owner'] = stripslashes($recordData['timesheets::TimeSheetOwnerFullName'][0]);
$_SESSION['approved_by_auth_rep_full_name'] = $recordData['timesheets::approved_by_auth_rep_full_name'][0];
$_SESSION['signer_fullname_imm_spvsr'] = $recordData['timesheets::Signer_fullname_imm_spvsr'][0];
$_SESSION['signer_fullname_pba'] = $recordData['timesheets::Signer_fullname_pba'][0];
$_SESSION['signer_fullname_bgt_auth_1'] = $recordData['timesheets::Signer_fullname_bgt_auth_1'][0];
$_SESSION['signer_fullname_bgt_auth_2'] = $recordData['timesheets::Signer_fullname_bgt_auth_2'][0];
$_SESSION['signer_fullname_bgt_auth_3'] = $recordData['timesheets::Signer_fullname_bgt_auth_3'][0];
$_SESSION['signer_fullname_bgt_auth_4'] = $recordData['timesheets::Signer_fullname_bgt_auth_4'][0];
$_SESSION['signer_fullname_bgt_auth_5'] = $recordData['timesheets::Signer_fullname_bgt_auth_5'][0];
$_SESSION['signer_fullname_bgt_auth_6'] = $recordData['timesheets::Signer_fullname_bgt_auth_6'][0];
$_SESSION['signer_fullname_bgt_auth_7'] = $recordData['timesheets::Signer_fullname_bgt_auth_7'][0];
$_SESSION['signer_fullname_bgt_auth_8'] = $recordData['timesheets::Signer_fullname_bgt_auth_8'][0];
$_SESSION['signer_fullname_bgt_auth_OT'] = $recordData['timesheets::Signer_fullname_bgt_auth_OT'][0];

$_SESSION['signer_pba_is_spvsr'] = $recordData['timesheets::spvsr_is_pba'][0];
$_SESSION['timesheet_name_owner'] = $recordData['timesheets::TimeSheetName'][0];
$_SESSION['workgroup_name_owner'] = $recordData['timesheets::staff_primary_workgroup'][0];
$_SESSION['employee_type_owner'] = $recordData['timesheets::c_timesheet_employee_type'][0];
$_SESSION['timesheet_owner_is_admin'] = $recordData['timesheets::staff_is_time_leave_admin'][0];
$_SESSION['timesheet_owner_FTE_status'] = $recordData['timesheets::staff_FTE_status'][0];
$_SESSION['timesheet_approval_not_required'] = $recordData['timesheets::staff_no_time_leave_approval_required'][0];
$_SESSION['timesheet_status'] = $recordData['timesheets::TimesheetSubmittedStatus'][0];
$_SESSION['timesheet_row_ID'] = $recordData['timesheets::c_row_ID_cwp'][0];
$_SESSION['time_hrs_revised_count'] = $recordData['timesheets::c_time_hrs_revised_count'][0];
$_SESSION['total_timesheet_hrs'] = $recordData['timesheets::c_total_timesheet_hrs'][0];
$_SESSION['blank_rows_check'] = $recordData['timesheets::c_sum_blank_rows_check'][0];
$_SESSION['new_employee_status'] = $recordData['staff::c_new_employee_status'][0];
$_SESSION['allow_variable_timesheet_hrs'] = $recordData['staff::allow_variable_timesheet_hrs'][0];
$_SESSION['timesheet_prefs_show_nicknames'] = $recordData['staff::timesheet_prefs_show_nicknames'][0];
$_SESSION['timesheet_prefs_hide_weekends'] = $recordData['staff::timesheet_prefs_hide_weekends'][0];


$_SESSION['timesheet_hrs_email_summary'] = $recordData['timesheets::c_timesheet_hrs_email_summary'][0];
$_SESSION['timesheet_row_ID'] = $recordData['timesheets::c_row_ID_cwp'][0];



#####################################################
## END: FIND REGULAR WORK HOURS FOR THIS TIMESHEET
#####################################################

#####################################################
## START: FIND WEEKEND DAYS FOR SHADING ON TIMESHEET
#####################################################
$pay_period_end_m = $recordData['timesheets::c_PayPeriodEnd_m'][0];
$pay_period_end_d = $recordData['timesheets::c_PayPeriodEnd_d'][0];
$pay_period_end_y = $recordData['timesheets::c_PayPeriodEnd_y'][0];
$pay_period_lockout = $recordData['timesheets::c_PayPeriodLockOutDate'][0];
$_SESSION['pay_period_lockout_date'] = $pay_period_lockout;
$_SESSION['rpt_outside_empl_form_signed'] = $recordData['timesheets::RptOutsideEmplFormSigned'][0];
$pay_period_lockout_days = $recordData['timesheets::c_PayPeriodLockOutDays'][0];
$today = date("M d Y");
$today_m = date("M");
$today_d = date("d");
$today_y = date("Y");
$today_stamp = strtotime($today);
$_SESSION['today_stamp'] = $today_stamp;
$lockout_day = date("M d Y",mktime(0,0,0,$pay_period_end_m,$pay_period_end_d + $pay_period_lockout_days,$pay_period_end_y));
$lockout_day_stamp = strtotime($lockout_day);
$_SESSION['lockout_day_stamp'] = $lockout_day_stamp;
//echo '<br>Today = '.$today;
//echo '<br>TodayStamp = '.$today_stamp;
//echo '<br>PayPeriod Lockout = '.$pay_period_lockout;
//echo '<br>Lockout day = '.$lockout_day;
//echo '<br>Lockout day Stamp = '.$lockout_day_stamp;
//echo '<br>Lockout days = '.$pay_period_lockout_days;

$_SESSION['day_of_week_01'] = date("D", mktime(0,0,0,$pay_period_end_m,1,$pay_period_end_y));
$_SESSION['day_of_week_02'] = date("D", mktime(0,0,0,$pay_period_end_m,2,$pay_period_end_y));
$_SESSION['day_of_week_03'] = date("D", mktime(0,0,0,$pay_period_end_m,3,$pay_period_end_y));
$_SESSION['day_of_week_04'] = date("D", mktime(0,0,0,$pay_period_end_m,4,$pay_period_end_y));
$_SESSION['day_of_week_05'] = date("D", mktime(0,0,0,$pay_period_end_m,5,$pay_period_end_y));
$_SESSION['day_of_week_06'] = date("D", mktime(0,0,0,$pay_period_end_m,6,$pay_period_end_y));
$_SESSION['day_of_week_07'] = date("D", mktime(0,0,0,$pay_period_end_m,7,$pay_period_end_y));
$_SESSION['day_of_week_08'] = date("D", mktime(0,0,0,$pay_period_end_m,8,$pay_period_end_y));
$_SESSION['day_of_week_09'] = date("D", mktime(0,0,0,$pay_period_end_m,9,$pay_period_end_y));
$_SESSION['day_of_week_10'] = date("D", mktime(0,0,0,$pay_period_end_m,10,$pay_period_end_y));
$_SESSION['day_of_week_11'] = date("D", mktime(0,0,0,$pay_period_end_m,11,$pay_period_end_y));
$_SESSION['day_of_week_12'] = date("D", mktime(0,0,0,$pay_period_end_m,12,$pay_period_end_y));
$_SESSION['day_of_week_13'] = date("D", mktime(0,0,0,$pay_period_end_m,13,$pay_period_end_y));
$_SESSION['day_of_week_14'] = date("D", mktime(0,0,0,$pay_period_end_m,14,$pay_period_end_y));
$_SESSION['day_of_week_15'] = date("D", mktime(0,0,0,$pay_period_end_m,15,$pay_period_end_y));
$_SESSION['day_of_week_16'] = date("D", mktime(0,0,0,$pay_period_end_m,16,$pay_period_end_y));
$_SESSION['day_of_week_17'] = date("D", mktime(0,0,0,$pay_period_end_m,17,$pay_period_end_y));
$_SESSION['day_of_week_18'] = date("D", mktime(0,0,0,$pay_period_end_m,18,$pay_period_end_y));
$_SESSION['day_of_week_19'] = date("D", mktime(0,0,0,$pay_period_end_m,19,$pay_period_end_y));
$_SESSION['day_of_week_20'] = date("D", mktime(0,0,0,$pay_period_end_m,20,$pay_period_end_y));
$_SESSION['day_of_week_21'] = date("D", mktime(0,0,0,$pay_period_end_m,21,$pay_period_end_y));
$_SESSION['day_of_week_22'] = date("D", mktime(0,0,0,$pay_period_end_m,22,$pay_period_end_y));
$_SESSION['day_of_week_23'] = date("D", mktime(0,0,0,$pay_period_end_m,23,$pay_period_end_y));
$_SESSION['day_of_week_24'] = date("D", mktime(0,0,0,$pay_period_end_m,24,$pay_period_end_y));
$_SESSION['day_of_week_25'] = date("D", mktime(0,0,0,$pay_period_end_m,25,$pay_period_end_y));
$_SESSION['day_of_week_26'] = date("D", mktime(0,0,0,$pay_period_end_m,26,$pay_period_end_y));
$_SESSION['day_of_week_27'] = date("D", mktime(0,0,0,$pay_period_end_m,27,$pay_period_end_y));
$_SESSION['day_of_week_28'] = date("D", mktime(0,0,0,$pay_period_end_m,28,$pay_period_end_y));
$_SESSION['day_of_week_29'] = date("D", mktime(0,0,0,$pay_period_end_m,29,$pay_period_end_y));
$_SESSION['day_of_week_30'] = date("D", mktime(0,0,0,$pay_period_end_m,30,$pay_period_end_y));
$_SESSION['day_of_week_31'] = date("D", mktime(0,0,0,$pay_period_end_m,31,$pay_period_end_y));
#####################################################
## END: FIND WEEKEND DAYS FOR SHADING ON TIMESHEET
#####################################################

if($_SESSION['employee_type_owner'] != 'Hourly'){ //HOURLY STAFF DON'T HAVE PAID LEAVE, UNPAID LEAVE, OR OVERTIME HRS
#####################################################
## START: FIND PAID LEAVE HOURS FOR THIS TIMESHEET
#####################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search3 -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search3 -> AddDBParam('Timesheet_ID',$timesheet_ID);
$search3 -> AddDBParam('HrsType','PdLv');
//$search3 -> AddDBParam('-lop','or');

//$search3 -> AddSortParam ($sortfield,'descend');


$searchResult3 = $search3 -> FMFind();
//$_SESSION['pdlv_hrs_data'] = $searchResult3;

//echo $searchResult3['errorCode'];
//echo '<br>PdLvHrs FoundCount: '.$searchResult3['foundCount'];
//print_r ($searchResult3);
$recordData3 = current($searchResult3['data']);
#####################################################
## END: FIND PAID LEAVE HOURS FOR THIS TIMESHEET
#####################################################

#####################################################
## START: FIND UNPAID LEAVE HOURS FOR THIS TIMESHEET
#####################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search4 -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search4 -> AddDBParam('Timesheet_ID',$timesheet_ID);
$search4 -> AddDBParam('HrsType','UnPdLv');
//$search4 -> AddDBParam('-lop','or');

//$search4 -> AddSortParam ($sortfield,'descend');


$searchResult4 = $search4 -> FMFind();
//$_SESSION['pdlv_hrs_data'] = $searchResult4;

//echo $searchResult4['errorCode'];
//echo '<br>UnPdLvHrs FoundCount: '.$searchResult4['foundCount'];
//print_r ($searchResult3);
$recordData4 = current($searchResult4['data']);

#####################################################
## END: FIND UNPAID LEAVE HOURS FOR THIS TIMESHEET
#####################################################
}
######################################################
## START: FIND OVERTIME WORK HOURS FOR THIS TIMESHEET
######################################################

if(($_SESSION['employee_type'] == 'Non-exempt')||($_SESSION['employee_type'] == 'Hourly')) {
//if the staff member is NON-EXEMPT, check for OT hrs --get status from SESSION variable that is set upon login
$search5 = new FX($serverIP,$webCompanionPort);
$search5 -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search5 -> SetDBPassword($webPW,$webUN);
//$search5 -> FMSkipRecords($skipsize);
$search5 -> AddDBParam('Timesheet_ID',$timesheet_ID);
$search5 -> AddDBParam('HrsType','WkHrsOT');
//$search5 -> AddDBParam('-lop','or');

//$search5 -> AddSortParam ($sortfield,'descend');


$searchResult5 = $search5 -> FMFind();
//$_SESSION['pdlv_hrs_data'] = $searchResult5;

//echo $searchResult5['errorCode'];
//echo '<br>OvertimeWkHrs FoundCount: '.$searchResult5['foundCount'];
//print_r ($searchResult5);
$recordData5 = current($searchResult5['data']);

}

#####################################################
## END: FIND OVERTIME WORK HOURS FOR THIS TIMESHEET
#####################################################


#####################################################
## START: FIND PAY PERIOD INFO FOR THIS TIMESHEET
#####################################################
//echo $row_ID;
$search6 = new FX($serverIP,$webCompanionPort);
$search6 -> SetDBData('SIMS_2.fp7','timesheet_pay_periods','all');
$search6 -> SetDBPassword($webPW,$webUN);
$search6 -> AddDBParam('Month_num',$pay_period_end_m);
$search6 -> AddDBParam('Year',$pay_period_end_y);
//$search6 -> AddDBParam('-lop','or');

//$search6 -> AddSortParam ($sortfield,'descend');


$searchResult6 = $search6 -> FMFind();

//echo $searchResult6['errorCode'];
//echo '<br>PayPeriod FoundCount: '.$searchResult6['foundCount'];
//print_r ($searchResult6);
$recordData6 = current($searchResult6['data']);

## SET PAY PERIOD SESSION VARIABLES
if($_SESSION['employee_type_owner'] == 'Exempt'){

$_SESSION['payperiod_workhrs'] = $recordData6['c_num_workhrs_exempt'][0] * $_SESSION['timesheet_owner_FTE_status'];

} elseif(($_SESSION['employee_type_owner'] != 'Exempt') && ($pay_period_end_d == '15')){

$_SESSION['payperiod_workhrs'] = $recordData6['c_num_workhrs_non_exempt_1'][0] * $_SESSION['timesheet_owner_FTE_status'];

} elseif(($_SESSION['employee_type_owner'] != 'Exempt') && ($pay_period_end_d != '15')){

$_SESSION['payperiod_workhrs'] = $recordData6['c_num_workhrs_non_exempt_2'][0] * $_SESSION['timesheet_owner_FTE_status'];

} else {

$_SESSION['payperiod_workhrs'] = 'Error_900';

}
#####################################################
## END: FIND PAY PERIOD INFO FOR THIS TIMESHEET
#####################################################





$staff_ID = $_SESSION['staff_ID']; //change this to a session variable that gets set upon login
$_SESSION ['days_in_month'] = $recordData['timesheets::c_days_in_month'][0];
$_SESSION ['weekdays_in_month'] = $recordData['timesheets::c_weekdays_in_month'][0];

$weekdays_in_month = $_SESSION ['weekdays_in_month'];
$days_in_month = $_SESSION ['days_in_month'];



$header_colspan = $days_in_month + 5;
//echo '<br>Days in month: '.$days_in_month;
//echo '<br>Weekdays in month: '.$weekdays_in_month;

//echo '<br>Header colspan: '.$header_colspan;
//echo '<br>Staff_ID: '.$staff_ID;

//echo '<br>SIMS_user_ID: '.$_SESSION['user_ID'];



##########################################
## START: PRINT VARIABLES FOR DEBUGGING ##
##########################################
if($debug == "on"){
echo '<br>Timesheet ID: '.$timesheet_ID;
echo '<br>Current Pay Period End: '.$_SESSION['current_pay_period_end'];
echo '<br>Action: '.$action;
echo '<br>new_row: '.$new_row;
echo '<br>new_row_ID: '.$new_row_ID;
echo '<br>edit_row_ID: '.$row_ID;

echo '<br>RegularWkHrs FoundCount: '.$searchResult['foundCount'];

echo '<br>Today = '.$today;
echo '<br>TodayStamp = '.$today_stamp;
echo '<br>PayPeriod Lockout = '.$pay_period_lockout;
echo '<br>Lockout day = '.$lockout_day;
echo '<br>Lockout day Stamp = '.$lockout_day_stamp;
echo '<br>Lockout days = '.$pay_period_lockout_days;

echo '<br>PdLvHrs FoundCount: '.$searchResult3['foundCount'];

echo '<br>UnPdLvHrs FoundCount: '.$searchResult4['foundCount'];

echo '<br>OvertimeWkHrs FoundCount: '.$searchResult5['foundCount'];

echo '<br>Days in month: '.$days_in_month;
echo '<br>Header colspan: '.$header_colspan;
echo '<br>Staff_ID: '.$staff_ID;

echo '<br>SIMS_user_ID: '.$_SESSION['user_ID'];

echo '<br>MyBudgetCodes FoundCount: '.$searchResult['foundCount'];

}

##########################################
## END: PRINT VARIABLES FOR DEBUGGING ##
##########################################





if (($searchResult['foundCount'] > 0) && ($action == 'view')) { 

####################################################################
## START: DISPLAY THE TIMESHEET IN AN HTML TABLE IN VIEW MODE
####################################################################
include_once('timesheets_view_st2.php'); //modularized the standard timesheet view used by staff
####################################################################
## END: DISPLAY THE TIMESHEET IN AN HTML TABLE IN VIEW MODE
####################################################################


} elseif (($searchResult['foundCount'] > 0) && ($action == 'edit')) { 

################################################################################################
## START: CREATE ARRAY CONTAINING THE LEAVE TYPE OF EACH EXISTING PD LV ROW FOR THIS TIMESHEET
################################################################################################
if($searchResult3['foundCount'] > 0){
	$i = 0;
	foreach($searchResult3['data'] as $key => $searchData3) { //searchResult -> fmp table = time_hrs 
		$lv_type_used[$i] = $searchData3['LvType'][0];
		$i++;
	}
}
################################################################################################
## END: CREATE ARRAY CONTAINING THE LEAVE TYPE OF EACH EXISTING PD LV ROW FOR THIS TIMESHEET
################################################################################################

################################################################################################
## START: GENERATE ARRAY CONTAINING LV TYPES THAT HAVE NOT ALREADY BEEN USED ON THIS TIMESHEET
################################################################################################
$lv_type_default[0] = "Sick";
$lv_type_default[1] = "Vacation";
$lv_type_default[2] = "Personal Holiday";
$lv_type_default[3] = "Other";


if($searchResult3['foundCount'] > 0){
$lv_type_available = array_diff($lv_type_default,$lv_type_used);
}else{
$lv_type_available = $lv_type_default;
}

################################################################################################
## END: GENERATE ARRAY CONTAINING LV TYPES THAT HAVE NOT ALREADY BEEN USED ON THIS TIMESHEET
################################################################################################

############################################################################
## START: FIND AVAILABLE BUDGET CODES FOR THIS USER TO POPULATE SELECT LIST
############################################################################

$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','budget_code_usage','all');
$search2 -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search2 -> AddDBParam('staff_ID','=='.$staff_ID);
$search2 -> AddDBParam('budget_codes::c_Active_Status_cwp','Active');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam ($sortfield,'descend');


$searchResult2 = $search2 -> FMFind();

//echo $searchResult['errorCode'];
//echo '<br>MyBudgetCodes FoundCount: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData2 = current($searchResult2['data']);
$_SESSION['user_bgt_codes'] = $recordData2;

############################################################################
## END: FIND AVAILABLE BUDGET CODES FOR THIS USER TO POPULATE SELECT LIST
############################################################################


##########################################
## START: PRINT VARIABLES FOR DEBUGGING ##
##########################################
if($debug == "on"){
echo '<p>$lv_type_default:<br>';
print_r($lv_type_default);

echo '<p>$lv_type_used:<br>';
print_r($lv_type_used);

echo '<p>$lv_type_available:<br>';
print_r($lv_type_available);


}

##########################################
## END: PRINT VARIABLES FOR DEBUGGING ##
##########################################

#################################################################################################
## START: DISPLAY THE TIMESHEET IN AN HTML TABLE IN EDIT MODE WITH EDIT FIELDS FOR SELECTED ROW
#################################################################################################

?>


<html>
<head>
<title>SIMS: My Timesheets</title>
<link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/grid.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />
<link rel="stylesheet" type="text/css" href="pace/pace-theme-center-simple.css" />

<script type="text/javascript" src="js/mootools-1.2.1-core.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="js/mootools-fluid16-autoselect.js"></script>
<script type="text/javascript" src="/pace/pace.js"></script>

<script language="JavaScript">
//<!--
function confirmDelete() { 
	var answer = confirm ("Are you sure you want to delete this row?")
	if (!answer) {
	return false;
	}
}
// -->
</script>

<script language="JavaScript">
//<!--
function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

function checkFields() { 

	// Budget Code
		if ((document.timesheet.budget_code.value =="choose") && (document.timesheet.row_type.value =="reg_hrs")){
			alert("Please enter a budget code for these hours.");
			document.timesheet.budget_code.focus();
			return false;	}

	// Leave Hrs Type
		if ((document.timesheet.LvType.value =="choose") && (document.timesheet.row_type.value =="pd_lv")){
			alert("Please enter the Leave Type for these hours.");
			document.timesheet.LvType.focus();
			return false;	}

	// Leave Hrs Type2
		if ((document.timesheet.LvType.value =="choose2") && (document.timesheet.row_type.value =="pd_lv")){
			alert("Only one row is allowed for each leave type. To add new leave, edit the row containing the type of leave you wish to add.");
			document.timesheet.LvType.focus();
			return false;	}

	var days_in_month = <?php echo $days_in_month;?>;

    var hrs_total = 0;
    var hrs_total_trimmed = 0;
	var h01 = ((document.timesheet.hrs_01.value) || ("0"));
	var h02 = ((document.timesheet.hrs_02.value) || ("0"));
	var h03 = ((document.timesheet.hrs_03.value) || ("0"));
	var h04 = ((document.timesheet.hrs_04.value) || ("0"));
	var h05 = ((document.timesheet.hrs_05.value) || ("0"));
	var h06 = ((document.timesheet.hrs_06.value) || ("0"));
	var h07 = ((document.timesheet.hrs_07.value) || ("0"));
	var h08 = ((document.timesheet.hrs_08.value) || ("0"));
	var h09 = ((document.timesheet.hrs_09.value) || ("0"));
	var h10 = ((document.timesheet.hrs_10.value) || ("0"));
	var h11 = ((document.timesheet.hrs_11.value) || ("0"));
	var h12 = ((document.timesheet.hrs_12.value) || ("0"));
	var h13 = ((document.timesheet.hrs_13.value) || ("0"));
	var h14 = ((document.timesheet.hrs_14.value) || ("0"));
	var h15 = ((document.timesheet.hrs_15.value) || ("0"));
	var h16 = ((document.timesheet.hrs_16.value) || ("0"));
	var h17 = ((document.timesheet.hrs_17.value) || ("0"));
	var h18 = ((document.timesheet.hrs_18.value) || ("0"));
	var h19 = ((document.timesheet.hrs_19.value) || ("0"));
	var h20 = ((document.timesheet.hrs_20.value) || ("0"));
	var h21 = ((document.timesheet.hrs_21.value) || ("0"));
	var h22 = ((document.timesheet.hrs_22.value) || ("0"));
	var h23 = ((document.timesheet.hrs_23.value) || ("0"));
	var h24 = ((document.timesheet.hrs_24.value) || ("0"));
	var h25 = ((document.timesheet.hrs_25.value) || ("0"));
	var h26 = ((document.timesheet.hrs_26.value) || ("0"));
	var h27 = ((document.timesheet.hrs_27.value) || ("0"));
	var h28 = ((document.timesheet.hrs_28.value) || ("0"));

	var h29 = 0;
	var h30 = 0;
	var h31 = 0;

	if(days_in_month > 28){
	h29 = ((document.timesheet.hrs_29.value) || ("0"));
	}

	if(days_in_month > 29){
	h30 = ((document.timesheet.hrs_30.value) || ("0"));
	}

	if(days_in_month > 30){
	h31 = ((document.timesheet.hrs_31.value) || ("0"));
	}

        h01 = parseFloat(h01);
        h02 = parseFloat(h02);
        h03 = parseFloat(h03);
        h04 = parseFloat(h04);
        h05 = parseFloat(h05);
        h06 = parseFloat(h06);
        h07 = parseFloat(h07);
        h08 = parseFloat(h08);
        h09 = parseFloat(h09);
        h10 = parseFloat(h10);
        h11 = parseFloat(h11);
        h12 = parseFloat(h12);
        h13 = parseFloat(h13);
        h14 = parseFloat(h14);
        h15 = parseFloat(h15);
        h16 = parseFloat(h16);
        h17 = parseFloat(h17);
        h18 = parseFloat(h18);
        h19 = parseFloat(h19);
        h20 = parseFloat(h20);
        h21 = parseFloat(h21);
        h22 = parseFloat(h22);
        h23 = parseFloat(h23);
        h24 = parseFloat(h24);
        h25 = parseFloat(h25);
        h26 = parseFloat(h26);
        h27 = parseFloat(h27);
        h28 = parseFloat(h28);
        h29 = parseFloat(h29);
        h30 = parseFloat(h30);
        h31 = parseFloat(h31);


		hrs_total = h01 + h02 + h03 + h04 + h05 + h06 + h07 + h08 + h09 + h10 + h11 + h12 + h13 + h14 + h15 + h16 + h17 + h18 + h19 + h20 + h21 + h22 + h23 + h24 + h25 + h26 + h27 + h28 + h29 + h30 + h31;
		hrs_total_trimmed = roundNumber(hrs_total,4);

		var roundedNumber = roundNumber(hrs_total,1);	

		if ((hrs_total_trimmed != roundedNumber) && (document.timesheet.row_type.value == "reg_hrs")){
			alert("Please round hours to the nearest tenth. - [hrs_entered: " + hrs_total + " - checkSum: " + roundedNumber + "]");
			return false;	}




/*
	// Hrs
		var days_in_month = <?php echo $days_in_month;?>;
		var day_counter = 1;
		var field_name;
		while(day_counter <= days_in_month){
		
		field_name = day_counter;
		if(day_counter < 10){
		field_name = "0" + day_counter;
		}
		
		var variable_focus = "document.timesheet.hrs_" + field_name + ".focus()";

		var variable_name = "document.timesheet.hrs_" + field_name + ".value";
		var hrs_entered = variable_name;
		var roundedNumber = roundNumber(hrs_entered,1);
		if ((hrs_entered !=roundedNumber) && (document.timesheet.row_type.value =="reg_hrs")){
			alert("Please round hours to the nearest tenth. - hrs_entered: " + hrs_entered + " - roundedNumber: " + roundedNumber + " - variableName: " + variable_name);
			variable_focus;
			return false;	}

		day_counter++;
		}
*/
}




// -->
</script>

</head>

<body>
<div class="container_16">

<?php include_once('http://www.sedl.org/staff/sims/includes/sims_header_2013.html');?>

<!--
###################################################################################
###################################################################################
############  BEGIN PAGE CONTENT  #################################################
###################################################################################
###################################################################################
-->
<div class="grid_16" style="position:relative">

<div class="nav" style="color:#0033ff;background-color:#ffffff;float:right;margin:12px;padding:4px 6px 2px 6px" nowrap>

	<a href="bgt_code_report_staff.php?action=new">Reports</a> | 
	<a href="/staff/sims/timesheet_prefs.php" title="Update your SIMS timesheet preferences.">Timesheet Preferences</a> | 
	<a href="/staff/sims/my_budget_codes.php" target="top" title="Click here to add budget codes to your budget code list.">My Budget Codes</a> | 
	<a href="/staff/sims/timesheets_newb.php" title="Create a new timesheet.">New Timesheet</a> | 
	<a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a>

</div>

<h2 id="page-heading">My Timesheets</h2>
</div>

<div class="clear"></div>

<div class="grid_16" style="position:relative">
<div class="grid_8">
<h2 id="page-heading" style="font-size:14px;font-weight:bold;border:0px;padding:10px 0px 0px 0px"><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</h2>
</div>
<div class="grid_8">
<h2 id="page-heading" style="font-size:14px;border:0px;padding:10px 0px 0px 0px;text-align:right">Pay Period: <?php echo $recordData['timesheets::PayPeriodBegin'][0];?> - <?php echo $recordData['timesheets::c_PayPeriodEnd'][0];?> | Status: <?php echo $recordData['timesheets::TimesheetSubmittedStatus'][0];?></h2>
</div>






<!--BEGIN FIRST SECTION: REGULAR HOURS BY BUDGET CODE-->


<table cellspacing=0 cellpadding=1 width="100%" class="sims">
		<tr><td class="section_head" colspan="<?php echo $header_colspan;?>">
		<form name="timesheet" id="timesheet" action="/staff/sims/timesheets_edit.php" method="GET" onsubmit="return checkFields()">
		<input type="hidden" name="timesheet_ID" value="<?php echo $timesheet_ID;?>">
		<input type="hidden" name="action" value="confirm_edit">
		<input type="hidden" name="edit_row_ID" value="<?php echo $row_ID;?>">
		<input type="hidden" name="days_in_month" value="<?php echo $days_in_month;?>">

		<strong>Regular Hours by Budget Code | <i>NOTE: Record Time to Nearest Tenth of an Hour</i></strong> <div style="float:right;text-align:right">Timesheet ID: <?php echo $_SESSION['timesheet_ID'];?></div></td></tr>
		<tr>
		<td class="slim_head" style="text-align:left">&nbsp;<strong>Budget Code</strong></td>
		<td class="slim_head"><strong>BA</strong></td>
		<td class="slim_head"><strong>T</strong></td>
		<td <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>01</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>02</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>03</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>04</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>05</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>06</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>07</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>08</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>09</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>10</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>11</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>12</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>13</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>14</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>15</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>16</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>17</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>18</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>19</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>20</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>21</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>22</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>23</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>24</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>25</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>26</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>27</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>28</strong>';}?></td>

		<?php if ($days_in_month > 28) { ?>
		<td <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>29</strong>';}?></td>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>30</strong>';}?></td>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>31</strong>';}?></td>
		<?php } ?>
		<td class="slim_head" colspan="2">&nbsp;</td>
		</tr>
		
		<?php 
		
		foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = time_hrs ?>
		
		<tr valign="top">
		<td style="text-align:left;padding-left:6px" nowrap>
		
		<?php if($row_ID == $searchData['c_cwp_row_ID'][0]){ //&& ($new_row == 'wk') ?>
		
			<select name="budget_code">
			<option value="choose"></option>
			
			<?php foreach($searchResult2['data'] as $key => $searchData2) { //searchResult2 -> fmp table = budget_code_usage ?>
			<option value="<?php echo $searchData2['budget_code'][0];?>" <?php if($searchData['BudgetCode'][0] == $searchData2['budget_code'][0]){echo 'SELECTED';}?>> <?php echo $searchData2['budget_code'][0].' - '.stripslashes($searchData2['Budget_Code_Nickname'][0]); ?></option>
			<?php } ?>
			</select>
		
		<? }else{ 
		echo $searchData['BudgetCode'][0]; ?><?php if($_SESSION['timesheet_prefs_show_nicknames'] == 'Yes'){echo '<font color="#666666"><em> - '.stripslashes($searchData['time_hrs_budget_code_usage::Budget_Code_Nickname'][0]).'</font></em>';}
		}?>
		
		</td>
		<td class="slim" style="font-size:9px"><?php echo strtoupper($searchData['BudgetAuthorityCodeLocal'][0]);?></td>
		<td class="slim" style="background-color:#e9f2ff"><strong><?php echo $searchData['c_TotalHrs'][0];?></strong></td><?php $WkHrsT_total = $WkHrsT_total + $searchData['c_TotalHrs'][0];?>
		<td <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_01" value="<?php echo $searchData['Hrs01'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs01'][0]; }?></td><?php $WkHrs01_total = $WkHrs01_total + $searchData['Hrs01'][0];?>
		<td <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_02" value="<?php echo $searchData['Hrs02'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs02'][0]; }?></td><?php $WkHrs02_total = $WkHrs02_total + $searchData['Hrs02'][0];?>
		<td <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_03" value="<?php echo $searchData['Hrs03'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs03'][0]; }?></td><?php $WkHrs03_total = $WkHrs03_total + $searchData['Hrs03'][0];?>
		<td <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_04" value="<?php echo $searchData['Hrs04'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs04'][0]; }?></td><?php $WkHrs04_total = $WkHrs04_total + $searchData['Hrs04'][0];?>
		<td <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_05" value="<?php echo $searchData['Hrs05'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs05'][0]; }?></td><?php $WkHrs05_total = $WkHrs05_total + $searchData['Hrs05'][0];?>
		<td <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_06" value="<?php echo $searchData['Hrs06'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs06'][0]; }?></td><?php $WkHrs06_total = $WkHrs06_total + $searchData['Hrs06'][0];?>
		<td <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_07" value="<?php echo $searchData['Hrs07'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs07'][0]; }?></td><?php $WkHrs07_total = $WkHrs07_total + $searchData['Hrs07'][0];?>
		<td <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_08" value="<?php echo $searchData['Hrs08'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs08'][0]; }?></td><?php $WkHrs08_total = $WkHrs08_total + $searchData['Hrs08'][0];?>
		<td <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_09" value="<?php echo $searchData['Hrs09'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs09'][0]; }?></td><?php $WkHrs09_total = $WkHrs09_total + $searchData['Hrs09'][0];?>
		<td <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_10" value="<?php echo $searchData['Hrs10'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs10'][0]; }?></td><?php $WkHrs10_total = $WkHrs10_total + $searchData['Hrs10'][0];?>
		<td <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_11" value="<?php echo $searchData['Hrs11'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs11'][0]; }?></td><?php $WkHrs11_total = $WkHrs11_total + $searchData['Hrs11'][0];?>
		<td <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_12" value="<?php echo $searchData['Hrs12'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs12'][0]; }?></td><?php $WkHrs12_total = $WkHrs12_total + $searchData['Hrs12'][0];?>
		<td <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_13" value="<?php echo $searchData['Hrs13'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs13'][0]; }?></td><?php $WkHrs13_total = $WkHrs13_total + $searchData['Hrs13'][0];?>
		<td <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_14" value="<?php echo $searchData['Hrs14'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs14'][0]; }?></td><?php $WkHrs14_total = $WkHrs14_total + $searchData['Hrs14'][0];?>
		<td <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_15" value="<?php echo $searchData['Hrs15'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs15'][0]; }?></td><?php $WkHrs15_total = $WkHrs15_total + $searchData['Hrs15'][0];?>
		<td <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_16" value="<?php echo $searchData['Hrs16'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs16'][0]; }?></td><?php $WkHrs16_total = $WkHrs16_total + $searchData['Hrs16'][0];?>
		<td <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_17" value="<?php echo $searchData['Hrs17'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs17'][0]; }?></td><?php $WkHrs17_total = $WkHrs17_total + $searchData['Hrs17'][0];?>
		<td <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_18" value="<?php echo $searchData['Hrs18'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs18'][0]; }?></td><?php $WkHrs18_total = $WkHrs18_total + $searchData['Hrs18'][0];?>
		<td <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_19" value="<?php echo $searchData['Hrs19'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs19'][0]; }?></td><?php $WkHrs19_total = $WkHrs19_total + $searchData['Hrs19'][0];?>
		<td <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_20" value="<?php echo $searchData['Hrs20'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs20'][0]; }?></td><?php $WkHrs20_total = $WkHrs20_total + $searchData['Hrs20'][0];?>
		<td <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_21" value="<?php echo $searchData['Hrs21'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs21'][0]; }?></td><?php $WkHrs21_total = $WkHrs21_total + $searchData['Hrs21'][0];?>
		<td <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_22" value="<?php echo $searchData['Hrs22'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs22'][0]; }?></td><?php $WkHrs22_total = $WkHrs22_total + $searchData['Hrs22'][0];?>
		<td <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_23" value="<?php echo $searchData['Hrs23'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs23'][0]; }?></td><?php $WkHrs23_total = $WkHrs23_total + $searchData['Hrs23'][0];?>
		<td <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_24" value="<?php echo $searchData['Hrs24'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs24'][0]; }?></td><?php $WkHrs24_total = $WkHrs24_total + $searchData['Hrs24'][0];?>
		<td <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_25" value="<?php echo $searchData['Hrs25'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs25'][0]; }?></td><?php $WkHrs25_total = $WkHrs25_total + $searchData['Hrs25'][0];?>
		<td <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_26" value="<?php echo $searchData['Hrs26'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs26'][0]; }?></td><?php $WkHrs26_total = $WkHrs26_total + $searchData['Hrs26'][0];?>
		<td <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_27" value="<?php echo $searchData['Hrs27'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs27'][0]; }?></td><?php $WkHrs27_total = $WkHrs27_total + $searchData['Hrs27'][0];?>
		<td <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_28" value="<?php echo $searchData['Hrs28'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs28'][0]; }?></td><?php $WkHrs28_total = $WkHrs28_total + $searchData['Hrs28'][0];?>



		<?php if ($days_in_month > 28) { ?>
		<td <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_29" value="<?php echo $searchData['Hrs29'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs29'][0]; }?></td><?php $WkHrs29_total = $WkHrs29_total + $searchData['Hrs29'][0];?>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_30" value="<?php echo $searchData['Hrs30'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs30'][0]; }?></td><?php $WkHrs30_total = $WkHrs30_total + $searchData['Hrs30'][0];?>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?><?php if($row_ID == $searchData['c_cwp_row_ID'][0]){?>class="slim2"><input <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="1" style="margin:0px"';}?> name="hrs_31" value="<?php echo $searchData['Hrs31'][0];?>"><?php }else{?>class="slim"><?php echo $searchData['Hrs31'][0]; }?></td><?php $WkHrs31_total = $WkHrs31_total + $searchData['Hrs31'][0];?>
		<?php } ?>
		<?php if($row_ID != $searchData['c_cwp_row_ID'][0]){?>
		<td class="dark_gray" colspan="2">&nbsp;</td>
		<?php }else{?>
		<td class="slim" colspan="2" style="background-color:#e9f2ff" nowrap>
		<input type="hidden" name="row_type" value="reg_hrs"><input type="hidden" name="LvType" value=""><input type="button" value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Save"><?php }?>
		</td>
		</tr>
		
		
		<?php  } ?>
	

		<?php if ($new_row == 'wk') {
		
		
		$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
		$newrecord -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
		$newrecord -> SetDBPassword($webPW,$webUN); //set password information
		
		
		###ADD THE SUBMITTED VALUES AS PARAMETERS###
		$newrecord -> AddDBParam('Timesheet_ID',$timesheet_ID);
		$newrecord -> AddDBParam('HrsType','WkHrsReg');
		
		###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
		$newrecordResult = $newrecord -> FMNew();
		
		$recordData2 = current($newrecordResult['data']);
		
		$new_row_ID = $recordData2['c_cwp_row_ID'][0];
		
		?>
		
		<tr>
		<td class="slim" nowrap>
			<select name="budget_code">
			<option value="choose"></option>
			
			<?php foreach($searchResult2['data'] as $key => $searchData2) { //searchResult2 -> fmp table = budget_code_usage ?>
			<option value="<?php echo $searchData2['budget_code'][0];?>"> <?php echo $searchData2['budget_code'][0].' - '.stripslashes($searchData2['Budget_Code_Nickname'][0]); ?></option>
			<?php } ?>
			</select>
		
		</td>
		<td class="slim" colspan="2">&nbsp;</td>
		
		<td class="slim" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_01"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_02"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_03"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_04"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_05"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_06"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_07"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_08"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_09"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_10"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_11"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_12"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_13"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_14"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_15"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_16"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_17"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_18"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_19"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_20"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_21"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_22"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_23"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_24"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_25"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_26"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_27"></td>
		<td class="slim" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_28"></td>



		<?php if ($days_in_month > 28) { ?>
		<td class="slim" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_29"></td>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td class="slim" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_30"></td>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td class="slim" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><input <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_31"></td>
		<?php } ?>
		<td class="slim" colspan="2" style="background-color:#e9f2ff"><input type="hidden" name="new_row_ID" value="<?php echo $recordData2['c_cwp_row_ID'][0];?>"><input type="hidden" name="row_type" value="reg_hrs"><input type="hidden" name="LvType" value=""><input type="submit" name="submit" value="Submit"></td>
		</tr>
		
		
		
		
		
		<?php }?>
<!--START: SUB-TOTAL REGULAR HRS-->									
		<tr bgcolor="#a2c7ca">
		<td class="slim_foot" style="text-align:right;padding:4px" nowrap colspan="2"><strong>Sub-Total RegHrs</strong></td>
		<td class="slim_foot"><strong><?php echo $WkHrsT_total;?></strong></td>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs01_total;}?></td><?php $AllHrs01_total = $WkHrs01_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs02_total;}?></td><?php $AllHrs02_total = $WkHrs02_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs03_total;}?></td><?php $AllHrs03_total = $WkHrs03_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs04_total;}?></td><?php $AllHrs04_total = $WkHrs04_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs05_total;}?></td><?php $AllHrs05_total = $WkHrs05_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs06_total;}?></td><?php $AllHrs06_total = $WkHrs06_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs07_total;}?></td><?php $AllHrs07_total = $WkHrs07_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs08_total;}?></td><?php $AllHrs08_total = $WkHrs08_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs09_total;}?></td><?php $AllHrs09_total = $WkHrs09_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs10_total;}?></td><?php $AllHrs10_total = $WkHrs10_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs11_total;}?></td><?php $AllHrs11_total = $WkHrs11_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs12_total;}?></td><?php $AllHrs12_total = $WkHrs12_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs13_total;}?></td><?php $AllHrs13_total = $WkHrs13_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs14_total;}?></td><?php $AllHrs14_total = $WkHrs14_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs15_total;}?></td><?php $AllHrs15_total = $WkHrs15_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs16_total;}?></td><?php $AllHrs16_total = $WkHrs16_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs17_total;}?></td><?php $AllHrs17_total = $WkHrs17_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs18_total;}?></td><?php $AllHrs18_total = $WkHrs18_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs19_total;}?></td><?php $AllHrs19_total = $WkHrs19_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs20_total;}?></td><?php $AllHrs20_total = $WkHrs20_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs21_total;}?></td><?php $AllHrs21_total = $WkHrs21_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs22_total;}?></td><?php $AllHrs22_total = $WkHrs22_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs23_total;}?></td><?php $AllHrs23_total = $WkHrs23_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs24_total;}?></td><?php $AllHrs24_total = $WkHrs24_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs25_total;}?></td><?php $AllHrs25_total = $WkHrs25_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs26_total;}?></td><?php $AllHrs26_total = $WkHrs26_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs27_total;}?></td><?php $AllHrs27_total = $WkHrs27_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs28_total;}?></td><?php $AllHrs28_total = $WkHrs28_total;?>
		
		
		<?php if ($days_in_month > 28) { ?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs29_total;}?></td><?php $AllHrs29_total = $WkHrs29_total;?>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs30_total;}?></td><?php $AllHrs30_total = $WkHrs30_total;?>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs31_total;}?></td><?php $AllHrs31_total = $WkHrs31_total;?>
		<?php } ?>
		<td class="slim_foot" colspan="2">&nbsp;</td>
		
		
		
		</tr>
<!--END: SUB-TOTAL REGULAR HRS-->							

<!--/table-->
<tr><td colspan="<?php echo $header_colspan;?>">
</td></tr>
<!--END FIRST SECTION: REGULAR HOURS BY BUDGET CODE-->

<?php if($_SESSION['employee_type_owner'] != 'Hourly'){ //HOURLY STAFF DON'T HAVE PAID LEAVE, UNPAID LEAVE, OR OVERTIME HRS ?>
<!--BEGIN SECOND SECTION: PAID LEAVE HOURS-->

<tr><td class="section_head" colspan="<?php echo $header_colspan;?>"><strong>Paid Leave Hours</strong> | <a href="http://www.sedl.org/staff/personnel/leavereport.cgi" target="_blank">Show current leave report</a></td></tr>

<?php 
	if(($searchResult3['foundCount'])==0 && ($new_row != 'pdlv')){ //searchResult3 -> fmp table = time_hrs  ?> 
		<tr><td colspan="<?php echo $header_colspan;?>">
		<p class="alert_small">There are no paid leave hours entered for this timesheet. | <a href="/staff/sims/timesheets.php?action=edit&new_row=pdlv&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Paid Leave Hours</a></p>&nbsp;<p>
		</td></tr>
	<?php }else{ ?>
	
		<!--table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="body"-->
		<tr>
		<td class="slim_head" colspan="2" style="text-align:left">&nbsp;<strong>Leave Type</strong></td>
		<td class="slim_head"><strong>T</strong></td>
		<td <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>01</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>02</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>03</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>04</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>05</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>06</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>07</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>08</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>09</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>10</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>11</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>12</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>13</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>14</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>15</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>16</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>17</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>18</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>19</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>20</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>21</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>22</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>23</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>24</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>25</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>26</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>27</strong>';}?></td>
		<td <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>28</strong>';}?></td>

		<?php if ($days_in_month > 28) { ?>
		<td <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>29</strong>';}?></td>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>30</strong>';}?></td>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>31</strong>';}?></td>
		<?php } ?>
		<td class="slim_head" colspan="2">&nbsp;</td>
		</tr>
		
		<?php
	
		foreach($searchResult3['data'] as $key => $searchData3) { //searchResult3 -> fmp table = time_hrs ?>
		
		<tr>
		<td class="slim" style="text-align:left;padding-left:6px" nowrap colspan="2">
		
		<?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?>

			<select name="LvType">
			<option value="choose"></option>
			<option value="<?php echo $searchData3['LvType'][0];?>" SELECTED> <?php echo $searchData3['LvType'][0];?></option>									
			<?php foreach($lv_type_available as $value) { ?>
				<option value="<?php echo $value;?>"> <?php echo $value;?></option>
			<?php } ?>
			
			</select>

<!--
			<select name="LvType" class="slim">
			<option value="choose"></option>
			<option value="Sick" <?php if($searchData3['LvType'][0] == 'Sick'){echo 'SELECTED';}?>> Sick</option>
			<option value="Vacation" <?php if($searchData3['LvType'][0] == 'Vacation'){echo 'SELECTED';}?>> Vacation</option>
			<option value="Personal Holiday" <?php if($searchData3['LvType'][0] == 'Personal Holiday'){echo 'SELECTED';}?>> Personal Holiday</option>
			<option value="Other" <?php if($searchData3['LvType'][0] == 'Other'){echo 'SELECTED';}?>> Other</option>
			</select>
-->									
		<? }else{ 
		echo $searchData3['LvType'][0]; } ?>
		
		</td>
		<td class="slim" style="background-color:#e9f2ff"><strong><?php echo $searchData3['c_TotalHrs'][0];?></strong></td><?php $PdLvHrsT_total = $PdLvHrsT_total + $searchData3['c_TotalHrs'][0];?>
		<td class="slim" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_01" value="<?php echo $searchData3['Hrs01'][0];?>"><?php }else{ echo $searchData3['Hrs01'][0]; }?></td><?php $PdLvHrs01_total = $PdLvHrs01_total + $searchData3['Hrs01'][0];?><?php $RegHrs01_total = $PdLvHrs01_total + $WkHrs01_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_02" value="<?php echo $searchData3['Hrs02'][0];?>"><?php }else{ echo $searchData3['Hrs02'][0]; }?></td><?php $PdLvHrs02_total = $PdLvHrs02_total + $searchData3['Hrs02'][0];?><?php $RegHrs02_total = $PdLvHrs02_total + $WkHrs02_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_03" value="<?php echo $searchData3['Hrs03'][0];?>"><?php }else{ echo $searchData3['Hrs03'][0]; }?></td><?php $PdLvHrs03_total = $PdLvHrs03_total + $searchData3['Hrs03'][0];?><?php $RegHrs03_total = $PdLvHrs03_total + $WkHrs03_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_04" value="<?php echo $searchData3['Hrs04'][0];?>"><?php }else{ echo $searchData3['Hrs04'][0]; }?></td><?php $PdLvHrs04_total = $PdLvHrs04_total + $searchData3['Hrs04'][0];?><?php $RegHrs04_total = $PdLvHrs04_total + $WkHrs04_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_05" value="<?php echo $searchData3['Hrs05'][0];?>"><?php }else{ echo $searchData3['Hrs05'][0]; }?></td><?php $PdLvHrs05_total = $PdLvHrs05_total + $searchData3['Hrs05'][0];?><?php $RegHrs05_total = $PdLvHrs05_total + $WkHrs05_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_06" value="<?php echo $searchData3['Hrs06'][0];?>"><?php }else{ echo $searchData3['Hrs06'][0]; }?></td><?php $PdLvHrs06_total = $PdLvHrs06_total + $searchData3['Hrs06'][0];?><?php $RegHrs06_total = $PdLvHrs06_total + $WkHrs06_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_07" value="<?php echo $searchData3['Hrs07'][0];?>"><?php }else{ echo $searchData3['Hrs07'][0]; }?></td><?php $PdLvHrs07_total = $PdLvHrs07_total + $searchData3['Hrs07'][0];?><?php $RegHrs07_total = $PdLvHrs07_total + $WkHrs07_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_08" value="<?php echo $searchData3['Hrs08'][0];?>"><?php }else{ echo $searchData3['Hrs08'][0]; }?></td><?php $PdLvHrs08_total = $PdLvHrs08_total + $searchData3['Hrs08'][0];?><?php $RegHrs08_total = $PdLvHrs08_total + $WkHrs08_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_09" value="<?php echo $searchData3['Hrs09'][0];?>"><?php }else{ echo $searchData3['Hrs09'][0]; }?></td><?php $PdLvHrs09_total = $PdLvHrs09_total + $searchData3['Hrs09'][0];?><?php $RegHrs09_total = $PdLvHrs09_total + $WkHrs09_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_10" value="<?php echo $searchData3['Hrs10'][0];?>"><?php }else{ echo $searchData3['Hrs10'][0]; }?></td><?php $PdLvHrs10_total = $PdLvHrs10_total + $searchData3['Hrs10'][0];?><?php $RegHrs10_total = $PdLvHrs10_total + $WkHrs10_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_11" value="<?php echo $searchData3['Hrs11'][0];?>"><?php }else{ echo $searchData3['Hrs11'][0]; }?></td><?php $PdLvHrs11_total = $PdLvHrs11_total + $searchData3['Hrs11'][0];?><?php $RegHrs11_total = $PdLvHrs11_total + $WkHrs11_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_12" value="<?php echo $searchData3['Hrs12'][0];?>"><?php }else{ echo $searchData3['Hrs12'][0]; }?></td><?php $PdLvHrs12_total = $PdLvHrs12_total + $searchData3['Hrs12'][0];?><?php $RegHrs12_total = $PdLvHrs12_total + $WkHrs12_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_13" value="<?php echo $searchData3['Hrs13'][0];?>"><?php }else{ echo $searchData3['Hrs13'][0]; }?></td><?php $PdLvHrs13_total = $PdLvHrs13_total + $searchData3['Hrs13'][0];?><?php $RegHrs13_total = $PdLvHrs13_total + $WkHrs13_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_14" value="<?php echo $searchData3['Hrs14'][0];?>"><?php }else{ echo $searchData3['Hrs14'][0]; }?></td><?php $PdLvHrs14_total = $PdLvHrs14_total + $searchData3['Hrs14'][0];?><?php $RegHrs14_total = $PdLvHrs14_total + $WkHrs14_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_15" value="<?php echo $searchData3['Hrs15'][0];?>"><?php }else{ echo $searchData3['Hrs15'][0]; }?></td><?php $PdLvHrs15_total = $PdLvHrs15_total + $searchData3['Hrs15'][0];?><?php $RegHrs15_total = $PdLvHrs15_total + $WkHrs15_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_16" value="<?php echo $searchData3['Hrs16'][0];?>"><?php }else{ echo $searchData3['Hrs16'][0]; }?></td><?php $PdLvHrs16_total = $PdLvHrs16_total + $searchData3['Hrs16'][0];?><?php $RegHrs16_total = $PdLvHrs16_total + $WkHrs16_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_17" value="<?php echo $searchData3['Hrs17'][0];?>"><?php }else{ echo $searchData3['Hrs17'][0]; }?></td><?php $PdLvHrs17_total = $PdLvHrs17_total + $searchData3['Hrs17'][0];?><?php $RegHrs17_total = $PdLvHrs17_total + $WkHrs17_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_18" value="<?php echo $searchData3['Hrs18'][0];?>"><?php }else{ echo $searchData3['Hrs18'][0]; }?></td><?php $PdLvHrs18_total = $PdLvHrs18_total + $searchData3['Hrs18'][0];?><?php $RegHrs18_total = $PdLvHrs18_total + $WkHrs18_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_19" value="<?php echo $searchData3['Hrs19'][0];?>"><?php }else{ echo $searchData3['Hrs19'][0]; }?></td><?php $PdLvHrs19_total = $PdLvHrs19_total + $searchData3['Hrs19'][0];?><?php $RegHrs19_total = $PdLvHrs19_total + $WkHrs19_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_20" value="<?php echo $searchData3['Hrs20'][0];?>"><?php }else{ echo $searchData3['Hrs20'][0]; }?></td><?php $PdLvHrs20_total = $PdLvHrs20_total + $searchData3['Hrs20'][0];?><?php $RegHrs20_total = $PdLvHrs20_total + $WkHrs20_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_21" value="<?php echo $searchData3['Hrs21'][0];?>"><?php }else{ echo $searchData3['Hrs21'][0]; }?></td><?php $PdLvHrs21_total = $PdLvHrs21_total + $searchData3['Hrs21'][0];?><?php $RegHrs21_total = $PdLvHrs21_total + $WkHrs21_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_22" value="<?php echo $searchData3['Hrs22'][0];?>"><?php }else{ echo $searchData3['Hrs22'][0]; }?></td><?php $PdLvHrs22_total = $PdLvHrs22_total + $searchData3['Hrs22'][0];?><?php $RegHrs22_total = $PdLvHrs22_total + $WkHrs22_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_23" value="<?php echo $searchData3['Hrs23'][0];?>"><?php }else{ echo $searchData3['Hrs23'][0]; }?></td><?php $PdLvHrs23_total = $PdLvHrs23_total + $searchData3['Hrs23'][0];?><?php $RegHrs23_total = $PdLvHrs23_total + $WkHrs23_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_24" value="<?php echo $searchData3['Hrs24'][0];?>"><?php }else{ echo $searchData3['Hrs24'][0]; }?></td><?php $PdLvHrs24_total = $PdLvHrs24_total + $searchData3['Hrs24'][0];?><?php $RegHrs24_total = $PdLvHrs24_total + $WkHrs24_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_25" value="<?php echo $searchData3['Hrs25'][0];?>"><?php }else{ echo $searchData3['Hrs25'][0]; }?></td><?php $PdLvHrs25_total = $PdLvHrs25_total + $searchData3['Hrs25'][0];?><?php $RegHrs25_total = $PdLvHrs25_total + $WkHrs25_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_26" value="<?php echo $searchData3['Hrs26'][0];?>"><?php }else{ echo $searchData3['Hrs26'][0]; }?></td><?php $PdLvHrs26_total = $PdLvHrs26_total + $searchData3['Hrs26'][0];?><?php $RegHrs26_total = $PdLvHrs26_total + $WkHrs26_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_27" value="<?php echo $searchData3['Hrs27'][0];?>"><?php }else{ echo $searchData3['Hrs27'][0]; }?></td><?php $PdLvHrs27_total = $PdLvHrs27_total + $searchData3['Hrs27'][0];?><?php $RegHrs27_total = $PdLvHrs27_total + $WkHrs27_total;?>
		<td class="slim" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_28" value="<?php echo $searchData3['Hrs28'][0];?>"><?php }else{ echo $searchData3['Hrs28'][0]; }?></td><?php $PdLvHrs28_total = $PdLvHrs28_total + $searchData3['Hrs28'][0];?><?php $RegHrs28_total = $PdLvHrs28_total + $WkHrs28_total;?>



		<?php if ($days_in_month > 28) { ?>
		<td class="slim" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_29" value="<?php echo $searchData3['Hrs29'][0];?>"><?php }else{ echo $searchData3['Hrs29'][0]; }?></td><?php $PdLvHrs29_total = $PdLvHrs29_total + $searchData3['Hrs29'][0];?><?php $RegHrs29_total = $PdLvHrs29_total + $WkHrs29_total;?>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td class="slim" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_30" value="<?php echo $searchData3['Hrs30'][0];?>"><?php }else{ echo $searchData3['Hrs30'][0]; }?></td><?php $PdLvHrs30_total = $PdLvHrs30_total + $searchData3['Hrs30'][0];?><?php $RegHrs30_total = $PdLvHrs30_total + $WkHrs30_total;?>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td class="slim" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($row_ID == $searchData3['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="slim"';}?> name="hrs_31" value="<?php echo $searchData3['Hrs31'][0];?>"><?php }else{ echo $searchData3['Hrs31'][0]; }?></td><?php $PdLvHrs31_total = $PdLvHrs31_total + $searchData3['Hrs31'][0];?><?php $RegHrs31_total = $PdLvHrs31_total + $WkHrs31_total;?>
		<?php } ?>
		
		
		<?php if($searchData3['LvType'][0] != 'Holiday'){?>
		
			<?php if($row_ID != $searchData3['c_cwp_row_ID'][0]){?>

			<td class="dark_gray" colspan="2">&nbsp;</td>	
			
			<?php }else{?>
			
			<td class="slim" colspan="2" style="background-color:#e9f2ff">
			<input type="hidden" name="row_type" value="pd_lv">
			<input type="hidden" name="budget_code" value="">
			<input type="submit" name="submit" value="Submit">
			</td>			
			
			<?php } ?>
		
		<?php }else{ ?>
		<td class="dark_gray" colspan="2"></td>
		<?php }?>
		
		</tr>
		
		
		<?php  } ?>
		
		<?php if ($new_row == 'pdlv') {
		
		
		$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
		$newrecord -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
		$newrecord -> SetDBPassword($webPW,$webUN); //set password information
		
		
		###ADD THE SUBMITTED VALUES AS PARAMETERS###
		$newrecord -> AddDBParam('Timesheet_ID',$timesheet_ID);
		$newrecord -> AddDBParam('HrsType','PdLv');
		
		###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
		$newrecordResult = $newrecord -> FMNew();
		
		$recordData2 = current($newrecordResult['data']);
		
		$new_row_ID = $recordData2['c_cwp_row_ID'][0];
		
		?>
		
		<tr>
		<td class="body" nowrap colspan="2">

			<select name="LvType" class="body">
			

			<?php if(count($lv_type_available) > 0){?>
			<option value="choose"></option>
				<?php foreach($lv_type_available as $value) { ?>
					<option value="<?php echo $value;?>"> <?php echo $value;?></option>
				<?php } ?>

			<?php }else{ ?>
			
			<option value="choose2">ALL LV TYPES USED</option>
			
			<?php } ?>
			
			</select>


<!--
			<select name="LvType" class="body">
			<option value="choose"></option>
			<option value="Sick"> Sick</option>
			<option value="Vacation"> Vacation</option>
			<option value="Personal Holiday"> Personal Holiday</option>
			<option value="Other"> Other</option>
			</select>
-->									
		</td>
		<td align="center" class="body"><strong>&nbsp;</strong></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_01"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_02"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_03"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_04"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_05"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_06"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_07"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_08"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_09"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_10"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_11"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_12"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_13"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_14"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_15"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_16"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_17"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_18"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_19"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_20"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_21"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_22"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_23"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_24"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_25"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_26"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_27"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_28"></td>


		
		
		<?php if ($days_in_month > 28) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_29"></td>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_30"></td>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_31"></td>
		<?php } ?>
		<td align="center" class="body"><input type="hidden" name="new_row_ID" value="<?php echo $recordData2['c_cwp_row_ID'][0];?>"><input type="hidden" name="row_type" value="pd_lv"><input type="hidden" name="budget_code" value=""><input type="submit" name="submit" value="Submit"></td>
		<td><a href="/staff/sims/timesheet_hrs_delete.php?row_ID=<?php echo $recordData2['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/red_cross_small.png" border="0"></a></td>
		</tr>
		
		
		
		
		
		<?php }?>

<!--START: SUB-TOTAL PAID LEAVE HRS-->
		
		<tr bgcolor="#a2c7ca">
		<td class="slim_foot" style="text-align:right;padding:4px" nowrap colspan="2"><strong>Total RegHrs</strong></td>
		<td class="slim_foot"><strong><?php $RegHrsT_total = $PdLvHrsT_total + $WkHrsT_total; echo $RegHrsT_total;?></strong></td>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs01_total;}?></td><?php $AllHrs01_total = $RegHrs01_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs02_total;}?></td><?php $AllHrs02_total = $RegHrs02_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs03_total;}?></td><?php $AllHrs03_total = $RegHrs03_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs04_total;}?></td><?php $AllHrs04_total = $RegHrs04_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs05_total;}?></td><?php $AllHrs05_total = $RegHrs05_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs06_total;}?></td><?php $AllHrs06_total = $RegHrs06_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs07_total;}?></td><?php $AllHrs07_total = $RegHrs07_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs08_total;}?></td><?php $AllHrs08_total = $RegHrs08_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs09_total;}?></td><?php $AllHrs09_total = $RegHrs09_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs10_total;}?></td><?php $AllHrs10_total = $RegHrs10_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs11_total;}?></td><?php $AllHrs11_total = $RegHrs11_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs12_total;}?></td><?php $AllHrs12_total = $RegHrs12_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs13_total;}?></td><?php $AllHrs13_total = $RegHrs13_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs14_total;}?></td><?php $AllHrs14_total = $RegHrs14_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs15_total;}?></td><?php $AllHrs15_total = $RegHrs15_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs16_total;}?></td><?php $AllHrs16_total = $RegHrs16_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs17_total;}?></td><?php $AllHrs17_total = $RegHrs17_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs18_total;}?></td><?php $AllHrs18_total = $RegHrs18_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs19_total;}?></td><?php $AllHrs19_total = $RegHrs19_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs20_total;}?></td><?php $AllHrs20_total = $RegHrs20_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs21_total;}?></td><?php $AllHrs21_total = $RegHrs21_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs22_total;}?></td><?php $AllHrs22_total = $RegHrs22_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs23_total;}?></td><?php $AllHrs23_total = $RegHrs23_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs24_total;}?></td><?php $AllHrs24_total = $RegHrs24_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs25_total;}?></td><?php $AllHrs25_total = $RegHrs25_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs26_total;}?></td><?php $AllHrs26_total = $RegHrs26_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs27_total;}?></td><?php $AllHrs27_total = $RegHrs27_total;?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs28_total;}?></td><?php $AllHrs28_total = $RegHrs28_total;?>



		<?php if ($days_in_month > 28) { ?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs29_total;}?></td><?php $AllHrs29_total = $RegHrs29_total;?>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs30_total;}?></td><?php $AllHrs30_total = $RegHrs30_total;?>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td class="slim_foot" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs31_total;}?></td><?php $AllHrs31_total = $RegHrs31_total;?>
		<?php } ?>
		<td class="slim_foot" colspan="2">&nbsp;</td>
		
		</tr>
	
<!--END: SUB-TOTAL PAID LEAVE HRS-->


<!--/table-->
<tr><td colspan="<?php echo $header_colspan;?>">
</td></tr>
<?php } ?>


<!--END SECOND SECTION: PAID LEAVE HOURS-->

<!--BEGIN THIRD SECTION: UNPAID LEAVE HOURS-->

<tr><td class="section_head" colspan="<?php echo $header_colspan;?>"><strong>Unpaid Leave Hours:</strong></td></tr>

<?php 
	if(($searchResult4['foundCount'])==0 && ($new_row != 'unpdlv')){ //searchResult4 -> fmp table = time_hrs  ?> 
		<tr><td colspan="<?php echo $header_colspan;?>">
		<p class="alert_small">There are no unpaid leave hours entered for this timesheet.</p>
		</td></tr>
	<?php }else{ ?>
	
		<!--table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="body"-->
		<tr>
		<td class="slim_head" colspan="2" style="text-align:left">Leave Type</td>
		<td align="center" class="body"><strong>T</strong></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>01</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>02</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>03</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>04</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>05</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>06</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>07</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>08</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>09</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>10</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>11</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>12</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>13</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>14</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>15</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>16</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>17</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>18</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>19</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>20</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>21</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>22</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>23</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>24</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>25</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>26</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>27</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>28</strong>';}?></td>

		<?php if ($days_in_month > 28) { ?>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>29</strong>';}?></td>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>30</strong>';}?></td>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>31</strong>';}?></td>
		<?php } ?>
		<td colspan="2">&nbsp;</td>
		</tr>
		
		<?php
	
		foreach($searchResult4['data'] as $key => $searchData4) { //searchResult4 -> fmp table = time_hrs ?>
		
		<tr>
		<td class="body" nowrap colspan="2">
		
		<?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?>
		
			<select name="LvType">
			<option value="choose"></option>
			<option value="Family & Medical" <?php if($searchData4['LvType'][0] == 'Family & Medical'){echo 'SELECTED';}?>> Family & Medical</option>
			<option value="Leave w/o Pay" <?php if($searchData4['LvType'][0] == 'Leave w/o Pay'){echo 'SELECTED';}?>> Leave w/o Pay</option>
			
			</select>
		
		<? }else{ 
		echo $searchData4['LvType'][0]; } ?>
		
		</td>
		<td align="center" class="body"><?php echo $searchData4['c_TotalHrs'][0];?></td><?php $UnPdLvHrsT_total = $UnPdLvHrsT_total + $searchData4['c_TotalHrs'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_01" value="<?php echo $searchData4['Hrs01'][0];?>"><?php }else{ echo $searchData4['Hrs01'][0]; }?></td><?php $UnPdLvHrs01_total = $UnPdLvHrs01_total + $searchData4['Hrs01'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_02" value="<?php echo $searchData4['Hrs02'][0];?>"><?php }else{ echo $searchData4['Hrs02'][0]; }?></td><?php $UnPdLvHrs02_total = $UnPdLvHrs02_total + $searchData4['Hrs02'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_03" value="<?php echo $searchData4['Hrs03'][0];?>"><?php }else{ echo $searchData4['Hrs03'][0]; }?></td><?php $UnPdLvHrs03_total = $UnPdLvHrs03_total + $searchData4['Hrs03'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_04" value="<?php echo $searchData4['Hrs04'][0];?>"><?php }else{ echo $searchData4['Hrs04'][0]; }?></td><?php $UnPdLvHrs04_total = $UnPdLvHrs04_total + $searchData4['Hrs04'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_05" value="<?php echo $searchData4['Hrs05'][0];?>"><?php }else{ echo $searchData4['Hrs05'][0]; }?></td><?php $UnPdLvHrs05_total = $UnPdLvHrs05_total + $searchData4['Hrs05'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_06" value="<?php echo $searchData4['Hrs06'][0];?>"><?php }else{ echo $searchData4['Hrs06'][0]; }?></td><?php $UnPdLvHrs06_total = $UnPdLvHrs06_total + $searchData4['Hrs06'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_07" value="<?php echo $searchData4['Hrs07'][0];?>"><?php }else{ echo $searchData4['Hrs07'][0]; }?></td><?php $UnPdLvHrs07_total = $UnPdLvHrs07_total + $searchData4['Hrs07'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_08" value="<?php echo $searchData4['Hrs08'][0];?>"><?php }else{ echo $searchData4['Hrs08'][0]; }?></td><?php $UnPdLvHrs08_total = $UnPdLvHrs08_total + $searchData4['Hrs08'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_09" value="<?php echo $searchData4['Hrs09'][0];?>"><?php }else{ echo $searchData4['Hrs09'][0]; }?></td><?php $UnPdLvHrs09_total = $UnPdLvHrs09_total + $searchData4['Hrs09'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_10" value="<?php echo $searchData4['Hrs10'][0];?>"><?php }else{ echo $searchData4['Hrs10'][0]; }?></td><?php $UnPdLvHrs10_total = $UnPdLvHrs10_total + $searchData4['Hrs10'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_11" value="<?php echo $searchData4['Hrs11'][0];?>"><?php }else{ echo $searchData4['Hrs11'][0]; }?></td><?php $UnPdLvHrs11_total = $UnPdLvHrs11_total + $searchData4['Hrs11'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_12" value="<?php echo $searchData4['Hrs12'][0];?>"><?php }else{ echo $searchData4['Hrs12'][0]; }?></td><?php $UnPdLvHrs12_total = $UnPdLvHrs12_total + $searchData4['Hrs12'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_13" value="<?php echo $searchData4['Hrs13'][0];?>"><?php }else{ echo $searchData4['Hrs13'][0]; }?></td><?php $UnPdLvHrs13_total = $UnPdLvHrs13_total + $searchData4['Hrs13'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_14" value="<?php echo $searchData4['Hrs14'][0];?>"><?php }else{ echo $searchData4['Hrs14'][0]; }?></td><?php $UnPdLvHrs14_total = $UnPdLvHrs14_total + $searchData4['Hrs14'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_15" value="<?php echo $searchData4['Hrs15'][0];?>"><?php }else{ echo $searchData4['Hrs15'][0]; }?></td><?php $UnPdLvHrs15_total = $UnPdLvHrs15_total + $searchData4['Hrs15'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_16" value="<?php echo $searchData4['Hrs16'][0];?>"><?php }else{ echo $searchData4['Hrs16'][0]; }?></td><?php $UnPdLvHrs16_total = $UnPdLvHrs16_total + $searchData4['Hrs16'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_17" value="<?php echo $searchData4['Hrs17'][0];?>"><?php }else{ echo $searchData4['Hrs17'][0]; }?></td><?php $UnPdLvHrs17_total = $UnPdLvHrs17_total + $searchData4['Hrs17'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_18" value="<?php echo $searchData4['Hrs18'][0];?>"><?php }else{ echo $searchData4['Hrs18'][0]; }?></td><?php $UnPdLvHrs18_total = $UnPdLvHrs18_total + $searchData4['Hrs18'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_19" value="<?php echo $searchData4['Hrs19'][0];?>"><?php }else{ echo $searchData4['Hrs19'][0]; }?></td><?php $UnPdLvHrs19_total = $UnPdLvHrs19_total + $searchData4['Hrs19'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_20" value="<?php echo $searchData4['Hrs20'][0];?>"><?php }else{ echo $searchData4['Hrs20'][0]; }?></td><?php $UnPdLvHrs20_total = $UnPdLvHrs20_total + $searchData4['Hrs20'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_21" value="<?php echo $searchData4['Hrs21'][0];?>"><?php }else{ echo $searchData4['Hrs21'][0]; }?></td><?php $UnPdLvHrs21_total = $UnPdLvHrs21_total + $searchData4['Hrs21'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_22" value="<?php echo $searchData4['Hrs22'][0];?>"><?php }else{ echo $searchData4['Hrs22'][0]; }?></td><?php $UnPdLvHrs22_total = $UnPdLvHrs22_total + $searchData4['Hrs22'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_23" value="<?php echo $searchData4['Hrs23'][0];?>"><?php }else{ echo $searchData4['Hrs23'][0]; }?></td><?php $UnPdLvHrs23_total = $UnPdLvHrs23_total + $searchData4['Hrs23'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_24" value="<?php echo $searchData4['Hrs24'][0];?>"><?php }else{ echo $searchData4['Hrs24'][0]; }?></td><?php $UnPdLvHrs24_total = $UnPdLvHrs24_total + $searchData4['Hrs24'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_25" value="<?php echo $searchData4['Hrs25'][0];?>"><?php }else{ echo $searchData4['Hrs25'][0]; }?></td><?php $UnPdLvHrs25_total = $UnPdLvHrs25_total + $searchData4['Hrs25'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_26" value="<?php echo $searchData4['Hrs26'][0];?>"><?php }else{ echo $searchData4['Hrs26'][0]; }?></td><?php $UnPdLvHrs26_total = $UnPdLvHrs26_total + $searchData4['Hrs26'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_27" value="<?php echo $searchData4['Hrs27'][0];?>"><?php }else{ echo $searchData4['Hrs27'][0]; }?></td><?php $UnPdLvHrs27_total = $UnPdLvHrs27_total + $searchData4['Hrs27'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_28" value="<?php echo $searchData4['Hrs28'][0];?>"><?php }else{ echo $searchData4['Hrs28'][0]; }?></td><?php $UnPdLvHrs28_total = $UnPdLvHrs28_total + $searchData4['Hrs28'][0];?>


		<?php if ($days_in_month > 28) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_29" value="<?php echo $searchData4['Hrs29'][0];?>"><?php }else{ echo $searchData4['Hrs29'][0]; }?></td><?php $UnPdLvHrs29_total = $UnPdLvHrs29_total + $searchData4['Hrs29'][0];?>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_30" value="<?php echo $searchData4['Hrs30'][0];?>"><?php }else{ echo $searchData4['Hrs30'][0]; }?></td><?php $UnPdLvHrs30_total = $UnPdLvHrs30_total + $searchData4['Hrs30'][0];?>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData4['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_31" value="<?php echo $searchData4['Hrs31'][0];?>"><?php }else{ echo $searchData4['Hrs31'][0]; }?></td><?php $UnPdLvHrs31_total = $UnPdLvHrs31_total + $searchData4['Hrs31'][0];?>
		<?php } ?>
		<td align="center" class="body"><?php if($row_ID != $searchData4['c_cwp_row_ID'][0]){?><a href="/staff/sims/timesheets.php?action=edit&Timesheet_ID=<?php echo $timesheet_ID;?>&edit_row_ID=<?php echo $searchData4['c_cwp_row_ID'][0];?>">Edit</a><?php }else{?>
		<input type="submit" name="submit" value="Submit"><?php }?></td>
		<td><a href="/staff/sims/timesheet_hrs_delete.php?row_ID=<?php echo $searchData4['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/red_cross_small.png" border="0"></a></td>
		
		
		</tr>
		
		
		<?php  } ?>
		
		<?php if ($new_row == 'unpdlv') {
		
		
		$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
		$newrecord -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
		$newrecord -> SetDBPassword($webPW,$webUN); //set password information
		
		
		###ADD THE SUBMITTED VALUES AS PARAMETERS###
		$newrecord -> AddDBParam('Timesheet_ID',$timesheet_ID);
		$newrecord -> AddDBParam('HrsType','UnPdLv');
		
		###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
		$newrecordResult = $newrecord -> FMNew();
		
		$recordData2 = current($newrecordResult['data']);
		
		$new_row_ID = $recordData2['c_cwp_row_ID'][0];
		
		?>
		
		<tr>
		<td class="body" nowrap colspan="2">
			<select name="LvType" class="body">
			<option value="choose"></option>
			<option value="Family & Medical"> Family & Medical</option>
			<option value="Leave w/o Pay"> Leave w/o Pay</option>
			
			</select>
		
		</td>
		<td align="center" class="body"><strong>&nbsp;</strong></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_01"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_02"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_03"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_04"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_05"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_06"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_07"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_08"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_09"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_10"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_11"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_12"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_13"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_14"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_15"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_16"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_17"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_18"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_19"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_20"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_21"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_22"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_23"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_24"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_25"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_26"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_27"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_28"></td>


		
		<?php if ($days_in_month > 28) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_29"></td>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_30"></td>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_31"></td>
		<?php } ?>
		<td align="center" class="body"><input type="hidden" name="new_row_ID" value="<?php echo $recordData2['c_cwp_row_ID'][0];?>"><input type="submit" name="submit" value="Submit"></td>
		<td><a href="/staff/sims/timesheet_hrs_delete.php?row_ID=<?php echo $recordData2['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/red_cross_small.png" border="0"></a></td>
		</tr>
		
		
		
		
		
		<?php }?>

		<?php $AllHrs01_total = $AllHrs01_total + $UnPdLvHrs01_total;?>
		<?php $AllHrs02_total = $AllHrs02_total + $UnPdLvHrs02_total;?>
		<?php $AllHrs03_total = $AllHrs03_total + $UnPdLvHrs03_total;?>
		<?php $AllHrs04_total = $AllHrs04_total + $UnPdLvHrs04_total;?>
		<?php $AllHrs05_total = $AllHrs05_total + $UnPdLvHrs05_total;?>
		<?php $AllHrs06_total = $AllHrs06_total + $UnPdLvHrs06_total;?>
		<?php $AllHrs07_total = $AllHrs07_total + $UnPdLvHrs07_total;?>
		<?php $AllHrs08_total = $AllHrs08_total + $UnPdLvHrs08_total;?>
		<?php $AllHrs09_total = $AllHrs09_total + $UnPdLvHrs09_total;?>
		<?php $AllHrs10_total = $AllHrs10_total + $UnPdLvHrs10_total;?>
		<?php $AllHrs11_total = $AllHrs11_total + $UnPdLvHrs11_total;?>
		<?php $AllHrs12_total = $AllHrs12_total + $UnPdLvHrs12_total;?>
		<?php $AllHrs13_total = $AllHrs13_total + $UnPdLvHrs13_total;?>
		<?php $AllHrs14_total = $AllHrs14_total + $UnPdLvHrs14_total;?>
		<?php $AllHrs15_total = $AllHrs15_total + $UnPdLvHrs15_total;?>
		<?php $AllHrs16_total = $AllHrs16_total + $UnPdLvHrs16_total;?>
		<?php $AllHrs17_total = $AllHrs17_total + $UnPdLvHrs17_total;?>
		<?php $AllHrs18_total = $AllHrs18_total + $UnPdLvHrs18_total;?>
		<?php $AllHrs19_total = $AllHrs19_total + $UnPdLvHrs19_total;?>
		<?php $AllHrs20_total = $AllHrs20_total + $UnPdLvHrs20_total;?>
		<?php $AllHrs21_total = $AllHrs21_total + $UnPdLvHrs21_total;?>
		<?php $AllHrs22_total = $AllHrs22_total + $UnPdLvHrs22_total;?>
		<?php $AllHrs23_total = $AllHrs23_total + $UnPdLvHrs23_total;?>
		<?php $AllHrs24_total = $AllHrs24_total + $UnPdLvHrs24_total;?>
		<?php $AllHrs25_total = $AllHrs25_total + $UnPdLvHrs25_total;?>
		<?php $AllHrs26_total = $AllHrs26_total + $UnPdLvHrs26_total;?>
		<?php $AllHrs27_total = $AllHrs27_total + $UnPdLvHrs27_total;?>
		<?php $AllHrs28_total = $AllHrs28_total + $UnPdLvHrs28_total;?>
		
		
		
		<?php $AllHrs29_total = $AllHrs29_total + $UnPdLvHrs29_total;?>
		
		
		<?php $AllHrs30_total = $AllHrs30_total + $UnPdLvHrs30_total;?>
		
		
		<?php $AllHrs31_total = $AllHrs31_total + $UnPdLvHrs31_total;?>
		


<!--/table-->
<tr><td colspan="<?php echo $header_colspan;?>">
<a href="/staff/sims/timesheets.php?action=edit&new_row=unpdlv&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Unpaid Leave Hours</a> | Row ID: <?php echo $row_ID;?><p>&nbsp;<p>
</td></tr>
<?php } ?>


<!--END THIRD SECTION: UNPAID LEAVE HOURS-->

<?php } ?>
<?php if(($_SESSION['employee_type'] == 'Non-exempt')||($_SESSION['employee_type'] == 'Hourly')) { ?>

<!--BEGIN FOURTH SECTION: OVERTIME HOURS BY BUDGET CODE-->


<tr><td colspan="<?php echo $header_colspan;?>"><strong>Overtime Hours By Budget Code:</strong></td></tr>

<?php 
	if(($searchResult5['foundCount']==0) &&($new_row != 'ot')){ //searchResult5 -> fmp table = time_hrs  ?> 
		<tr><td colspan="<?php echo $header_colspan;?>">
		<p class="alert_small">There are no overtime hours entered for this timesheet. | <a href="/staff/sims/timesheets.php?action=edit&new_row=ot&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Overtime Hours</a></p>
		</td></tr>
	<?php }else{ ?>
<!--table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="body"-->
		<tr bgcolor="cccccc">
		<td class="body">&nbsp;Budget Code</td>
		<td align="center" class="body"><strong>BA</strong></td>
		<td align="center" class="body"><strong>T</strong></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>01</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>02</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>03</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>04</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>05</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>06</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>07</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>08</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>09</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>10</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>11</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>12</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>13</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>14</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>15</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>16</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>17</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>18</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>19</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>20</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>21</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>22</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>23</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>24</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>25</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>26</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>27</strong>';}?></td>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>28</strong>';}?></td>

		<?php if ($days_in_month > 28) { ?>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>29</strong>';}?></td>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>30</strong>';}?></td>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td align="center" class="body"><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>31</strong>';}?></td>
		<?php } ?>
		<td colspan="2">&nbsp;</td>
		
		</tr>
		
		<?php 
		
		foreach($searchResult5['data'] as $key => $searchData5) { //searchResult5 -> fmp table = time_hrs ?>
		
		<tr>
		<td class="body" nowrap>
		
		<?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?>
		
			<select name="budget_code" class="body">
			<option value="choose"></option>
			
			<?php foreach($searchResult2['data'] as $key => $searchData2) { //searchResult2 -> fmp table = budget_code_usage ?>
			<option value="<?php echo $searchData2['budget_code'][0];?>" <?php if($searchData5['BudgetCode'][0] == $searchData2['budget_code'][0]){echo 'SELECTED';}?>> <?php echo $searchData2['budget_code'][0].' - '.$searchData2['Budget_Code_Nickname'][0]; ?></option>
			<?php } ?>
			</select>
		
		<? }else{ 
		echo $searchData5['BudgetCode'][0]; ?><?php if($_SESSION['timesheet_prefs_show_nicknames'] == 'Yes'){echo '<font color="#666666"><em> - '.$searchData5['time_hrs_budget_code_usage::Budget_Code_Nickname'][0].'</font></em>';}
		}?>
		
		</td>
		<td align="center" class="body"><?php echo $searchData5['BudgetAuthorityCodeLocal'][0];?></td>
		<td align="center" class="body"><?php echo $searchData5['c_TotalHrs'][0];?></td><?php $OThrsT_total = $OThrsT_total + $searchData5['c_TotalHrs'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_01" value="<?php echo $searchData5['Hrs01'][0];?>"><?php }else{ echo $searchData5['Hrs01'][0]; }?></td><?php $OThrs01_total = $OThrs01_total + $searchData5['Hrs01'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_02" value="<?php echo $searchData5['Hrs02'][0];?>"><?php }else{ echo $searchData5['Hrs02'][0]; }?></td><?php $OThrs02_total = $OThrs02_total + $searchData5['Hrs02'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_03" value="<?php echo $searchData5['Hrs03'][0];?>"><?php }else{ echo $searchData5['Hrs03'][0]; }?></td><?php $OThrs03_total = $OThrs03_total + $searchData5['Hrs03'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_04" value="<?php echo $searchData5['Hrs04'][0];?>"><?php }else{ echo $searchData5['Hrs04'][0]; }?></td><?php $OThrs04_total = $OThrs04_total + $searchData5['Hrs04'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_05" value="<?php echo $searchData5['Hrs05'][0];?>"><?php }else{ echo $searchData5['Hrs05'][0]; }?></td><?php $OThrs05_total = $OThrs05_total + $searchData5['Hrs05'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_06" value="<?php echo $searchData5['Hrs06'][0];?>"><?php }else{ echo $searchData5['Hrs06'][0]; }?></td><?php $OThrs06_total = $OThrs06_total + $searchData5['Hrs06'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_07" value="<?php echo $searchData5['Hrs07'][0];?>"><?php }else{ echo $searchData5['Hrs07'][0]; }?></td><?php $OThrs07_total = $OThrs07_total + $searchData5['Hrs07'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_08" value="<?php echo $searchData5['Hrs08'][0];?>"><?php }else{ echo $searchData5['Hrs08'][0]; }?></td><?php $OThrs08_total = $OThrs08_total + $searchData5['Hrs08'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_09" value="<?php echo $searchData5['Hrs09'][0];?>"><?php }else{ echo $searchData5['Hrs09'][0]; }?></td><?php $OThrs09_total = $OThrs09_total + $searchData5['Hrs09'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_10" value="<?php echo $searchData5['Hrs10'][0];?>"><?php }else{ echo $searchData5['Hrs10'][0]; }?></td><?php $OThrs10_total = $OThrs10_total + $searchData5['Hrs10'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_11" value="<?php echo $searchData5['Hrs11'][0];?>"><?php }else{ echo $searchData5['Hrs11'][0]; }?></td><?php $OThrs11_total = $OThrs11_total + $searchData5['Hrs11'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_12" value="<?php echo $searchData5['Hrs12'][0];?>"><?php }else{ echo $searchData5['Hrs12'][0]; }?></td><?php $OThrs12_total = $OThrs12_total + $searchData5['Hrs12'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_13" value="<?php echo $searchData5['Hrs13'][0];?>"><?php }else{ echo $searchData5['Hrs13'][0]; }?></td><?php $OThrs13_total = $OThrs13_total + $searchData5['Hrs13'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_14" value="<?php echo $searchData5['Hrs14'][0];?>"><?php }else{ echo $searchData5['Hrs14'][0]; }?></td><?php $OThrs14_total = $OThrs14_total + $searchData5['Hrs14'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_15" value="<?php echo $searchData5['Hrs15'][0];?>"><?php }else{ echo $searchData5['Hrs15'][0]; }?></td><?php $OThrs15_total = $OThrs15_total + $searchData5['Hrs15'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_16" value="<?php echo $searchData5['Hrs16'][0];?>"><?php }else{ echo $searchData5['Hrs16'][0]; }?></td><?php $OThrs16_total = $OThrs16_total + $searchData5['Hrs16'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_17" value="<?php echo $searchData5['Hrs17'][0];?>"><?php }else{ echo $searchData5['Hrs17'][0]; }?></td><?php $OThrs17_total = $OThrs17_total + $searchData5['Hrs17'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_18" value="<?php echo $searchData5['Hrs18'][0];?>"><?php }else{ echo $searchData5['Hrs18'][0]; }?></td><?php $OThrs18_total = $OThrs18_total + $searchData5['Hrs18'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_19" value="<?php echo $searchData5['Hrs19'][0];?>"><?php }else{ echo $searchData5['Hrs19'][0]; }?></td><?php $OThrs19_total = $OThrs19_total + $searchData5['Hrs19'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_20" value="<?php echo $searchData5['Hrs20'][0];?>"><?php }else{ echo $searchData5['Hrs20'][0]; }?></td><?php $OThrs20_total = $OThrs20_total + $searchData5['Hrs20'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_21" value="<?php echo $searchData5['Hrs21'][0];?>"><?php }else{ echo $searchData5['Hrs21'][0]; }?></td><?php $OThrs21_total = $OThrs21_total + $searchData5['Hrs21'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_22" value="<?php echo $searchData5['Hrs22'][0];?>"><?php }else{ echo $searchData5['Hrs22'][0]; }?></td><?php $OThrs22_total = $OThrs22_total + $searchData5['Hrs22'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_23" value="<?php echo $searchData5['Hrs23'][0];?>"><?php }else{ echo $searchData5['Hrs23'][0]; }?></td><?php $OThrs23_total = $OThrs23_total + $searchData5['Hrs23'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_24" value="<?php echo $searchData5['Hrs24'][0];?>"><?php }else{ echo $searchData5['Hrs24'][0]; }?></td><?php $OThrs24_total = $OThrs24_total + $searchData5['Hrs24'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_25" value="<?php echo $searchData5['Hrs25'][0];?>"><?php }else{ echo $searchData5['Hrs25'][0]; }?></td><?php $OThrs25_total = $OThrs25_total + $searchData5['Hrs25'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_26" value="<?php echo $searchData5['Hrs26'][0];?>"><?php }else{ echo $searchData5['Hrs26'][0]; }?></td><?php $OThrs26_total = $OThrs26_total + $searchData5['Hrs26'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_27" value="<?php echo $searchData5['Hrs27'][0];?>"><?php }else{ echo $searchData5['Hrs27'][0]; }?></td><?php $OThrs27_total = $OThrs27_total + $searchData5['Hrs27'][0];?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_28" value="<?php echo $searchData5['Hrs28'][0];?>"><?php }else{ echo $searchData5['Hrs28'][0]; }?></td><?php $OThrs28_total = $OThrs28_total + $searchData5['Hrs28'][0];?>



		<?php if ($days_in_month > 28) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_29" value="<?php echo $searchData5['Hrs29'][0];?>"><?php }else{ echo $searchData5['Hrs29'][0]; }?></td><?php $OThrs29_total = $OThrs29_total + $searchData5['Hrs29'][0];?>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_30" value="<?php echo $searchData5['Hrs30'][0];?>"><?php }else{ echo $searchData5['Hrs30'][0]; }?></td><?php $OThrs30_total = $OThrs30_total + $searchData5['Hrs30'][0];?>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($row_ID == $searchData5['c_cwp_row_ID'][0]){?><input <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_31" value="<?php echo $searchData5['Hrs31'][0];?>"><?php }else{ echo $searchData5['Hrs31'][0]; }?></td><?php $OThrs31_total = $OThrs31_total + $searchData5['Hrs31'][0];?>
		<?php } ?>
		<td align="center" class="body"><?php if($row_ID != $searchData5['c_cwp_row_ID'][0]){?><a href="/staff/sims/timesheets.php?action=edit&Timesheet_ID=<?php echo $timesheet_ID;?>&edit_row_ID=<?php echo $searchData5['c_cwp_row_ID'][0];?>">Edit</a><?php }else{?>
		<input type="submit" name="submit" value="Submit"><?php }?></td>
		<td><a href="/staff/sims/timesheet_hrs_delete.php?row_ID=<?php echo $searchData5['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/red_cross_small.png" border="0"></a></td>
		</tr>
		
		
		<?php  } ?>
	

		<?php if ($new_row == 'ot') {
		
		
		$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
		$newrecord -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
		$newrecord -> SetDBPassword($webPW,$webUN); //set password information
		
		
		###ADD THE SUBMITTED VALUES AS PARAMETERS###
		$newrecord -> AddDBParam('Timesheet_ID',$timesheet_ID);
		$newrecord -> AddDBParam('HrsType','WkHrsOT');
		
		###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
		$newrecordResult = $newrecord -> FMNew();
		
		$recordData2 = current($newrecordResult['data']);
		
		$new_row_ID = $recordData2['c_cwp_row_ID'][0];
		//echo $newrecordResult['errorCode'];
		?>
		
		<tr>
		<td class="body" nowrap>
			<select name="budget_code" class="body">
			<option value="choose"></option>
			
			<?php foreach($searchResult2['data'] as $key => $searchData2) { //searchResult2 -> fmp table = budget_code_usage ?>
			<option value="<?php echo $searchData2['budget_code'][0];?>"> <?php echo $searchData2['budget_code'][0]; ?></option>
			<?php } ?>
			</select>
		
		</td>
		<td align="center" class="body" colspan="2">&nbsp;</td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_01"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_02"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_03"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_04"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_05"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_06"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_07"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_08"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_09"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_10"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_11"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_12"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_13"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_14"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_15"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_16"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_17"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_18"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_19"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_20"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_21"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_22"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_23"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_24"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_25"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_26"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_27"></td>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_28"></td>



		<?php if ($days_in_month > 28) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_29"></td>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_30"></td>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td align="center" class="body" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'bgcolor="cccccc"';} ?>><input <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_31"></td>
		<?php } ?>
		<td align="center" class="body"><input type="hidden" name="new_row_ID" value="<?php echo $recordData2['c_cwp_row_ID'][0];?>"><input type="submit" name="submit" value="Submit"></td>
		<td><a href="/staff/sims/timesheet_hrs_delete.php?row_ID=<?php echo $recordData2['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/red_cross_small.png" border="0"></a></td>
		</tr>
		
		
		
		
		
		<?php }?>

		<?php $AllHrs01_total = $AllHrs01_total + $OTHrs01_total;?>
		<?php $AllHrs02_total = $AllHrs02_total + $OTHrs02_total;?>
		<?php $AllHrs03_total = $AllHrs03_total + $OTHrs03_total;?>
		<?php $AllHrs04_total = $AllHrs04_total + $OTHrs04_total;?>
		<?php $AllHrs05_total = $AllHrs05_total + $OTHrs05_total;?>
		<?php $AllHrs06_total = $AllHrs06_total + $OTHrs06_total;?>
		<?php $AllHrs07_total = $AllHrs07_total + $OTHrs07_total;?>
		<?php $AllHrs08_total = $AllHrs08_total + $OTHrs08_total;?>
		<?php $AllHrs09_total = $AllHrs09_total + $OTHrs09_total;?>
		<?php $AllHrs10_total = $AllHrs10_total + $OTHrs10_total;?>
		<?php $AllHrs11_total = $AllHrs11_total + $OTHrs11_total;?>
		<?php $AllHrs12_total = $AllHrs12_total + $OTHrs12_total;?>
		<?php $AllHrs13_total = $AllHrs13_total + $OTHrs13_total;?>
		<?php $AllHrs14_total = $AllHrs14_total + $OTHrs14_total;?>
		<?php $AllHrs15_total = $AllHrs15_total + $OTHrs15_total;?>
		<?php $AllHrs16_total = $AllHrs16_total + $OTHrs16_total;?>
		<?php $AllHrs17_total = $AllHrs17_total + $OTHrs17_total;?>
		<?php $AllHrs18_total = $AllHrs18_total + $OTHrs18_total;?>
		<?php $AllHrs19_total = $AllHrs19_total + $OTHrs19_total;?>
		<?php $AllHrs20_total = $AllHrs20_total + $OTHrs20_total;?>
		<?php $AllHrs21_total = $AllHrs21_total + $OTHrs21_total;?>
		<?php $AllHrs22_total = $AllHrs22_total + $OTHrs22_total;?>
		<?php $AllHrs23_total = $AllHrs23_total + $OTHrs23_total;?>
		<?php $AllHrs24_total = $AllHrs24_total + $OTHrs24_total;?>
		<?php $AllHrs25_total = $AllHrs25_total + $OTHrs25_total;?>
		<?php $AllHrs26_total = $AllHrs26_total + $OTHrs26_total;?>
		<?php $AllHrs27_total = $AllHrs27_total + $OTHrs27_total;?>
		<?php $AllHrs28_total = $AllHrs28_total + $OTHrs28_total;?>
		
		
		
		<?php $AllHrs29_total = $AllHrs29_total + $OTHrs29_total;?>
		
		
		<?php $AllHrs30_total = $AllHrs30_total + $OTHrs30_total;?>
		
		
		<?php $AllHrs31_total = $AllHrs31_total + $OTHrs31_total;?>



<!--/table-->
<tr><td colspan="<?php echo $header_colspan;?>">
<a href="/staff/sims/timesheets.php?action=edit&new_row=ot&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Overtime Hours</a> | Row ID: <?php echo $new_row_ID;?><p>&nbsp;<p>
</td></tr>
<?php } ?>


<?php } ?>
<!--END FOURTH SECTION: OVERTIME HOURS BY BUDGET CODE-->

<!--START: GRAND TOTAL HOURS-->


		<tr>
		<td class="slim_foot" style="text-align:right;padding-right:6px" nowrap align="right" colspan="2"><strong>TOTAL</strong></td>
		<td align="center" class="slim_foot"><?php $AllHrsT_total = $WkHrsT_total + $PdLvHrsT_total + $UnPdLvHrsT_total + $OTHrsT_total; echo $AllHrsT_total;?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs01_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs02_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs03_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs04_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs05_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs06_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs07_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs08_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs09_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs10_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs11_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs12_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs13_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs14_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs15_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs16_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs17_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs18_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs19_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs20_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs21_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs22_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs23_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs24_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs25_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs26_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs27_total;}?></td>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs28_total;}?></td>
		
		
		<?php if ($days_in_month > 28) { ?>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs29_total;}?></td>
		<?php } ?>
		<?php if ($days_in_month > 29) { ?>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs30_total;}?></td>
		<?php } ?>
		<?php if ($days_in_month > 30) { ?>
		<td align="center" class="slim_foot" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs31_total;}?></td>
		<?php } ?>
		<td align="center" class="slim_foot" colspan="2" bgcolor="ffffff"><font color="red">(<?php echo $_SESSION['payperiod_workhrs']; ?>)</font></td>
		
		</tr>

<!--END: GRAND TOTAL HOURS-->	

		
										
		
	
	</table>
</td></tr>




</form>




</td></tr>





</table>

</div><!--END grid_16-->
</div><!--END container_16-->

<div class="container_16" style="text-align:center"><hr style="padding:0px">For technical assistance contact <a href="mailto:sims@sed.org">sims@sedl.org</a>.
</div>
</body>

</html>

<?php
#################################################################################################
## END: DISPLAY THE TIMESHEET IN AN HTML TABLE IN EDIT MODE WITH EDIT FIELDS FOR SELECTED ROW
#################################################################################################

} else { ?>

ERROR: No records found.

<?php } ?>
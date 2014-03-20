<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2007 by the Texas Comprehensive Center at SEDL
#
# Written by Eric Waters 06/26/2007
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
$timesheet_ID = $_GET['Timesheet_ID'];
$_SESSION['timesheet_ID'] = $_GET['Timesheet_ID'];
$action = $_GET['action'];
$signature_status = '';
###############################
## END: GRAB FORM VARIABLES
###############################

#####################################################
## START: FIND REGULAR WORK HOURS FOR THIS TIMESHEET
#####################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('Timesheet_ID',$timesheet_ID);
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
$_SESSION['signer_ID_imm_spvsr'] = $recordData['timesheets::Signer_ID_imm_spvsr'][0];
$_SESSION['signer_ID_pba'] = $recordData['timesheets::Signer_ID_pba'][0];

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
$_SESSION['approved_by_auth_rep_status'] = $recordData['timesheets::approved_by_auth_rep_status'][0];
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
$_SESSION['oba_approvals_complete'] = $recordData['timesheets::c_sum_oba_time_hrs_approved'][0];
//$_SESSION['oba_approvals_complete'] = $recordData['timesheets::c_sum_bgt_auth_time_hrs_approved'][0];
//$_SESSION['oba_approvals_complete'] = $recordData['timesheets::c_oba_approvals_complete'][0];
$_SESSION['current_pay_period_end'] = $recordData['timesheets::c_PayPeriodEnd'][0];
$_SESSION['timesheet_approval_not_required'] = $recordData['timesheets::staff_no_time_leave_approval_required'][0];
$_SESSION['current_submitted_status'] = $recordData['timesheets::TimesheetSubmittedStatus'][0];
$_SESSION['timesheet_owner_FTE_status'] = $recordData['timesheets::staff_FTE_status'][0];

$_SESSION['timesheet_hrs_email_summary'] = $recordData['timesheets::c_timesheet_hrs_email_summary'][0];
$_SESSION['timesheet_row_ID'] = $recordData['timesheets::c_row_ID_cwp'][0];

$_SESSION['rpt_outside_empl_form_signed'] = $recordData['timesheets::RptOutsideEmplFormSigned'][0];
$_SESSION['rpt_outside_empl_form_has_content'] = $recordData['timesheets::c_rpt_out_empl_form_has_content'][0];
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

######################################################
## START: FIND OVERTIME WORK HOURS FOR THIS TIMESHEET
######################################################

if($_SESSION['employee_type_owner'] == 'Non-exempt') {
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
}

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

} elseif(($_SESSION['employee_type'] != 'Exempt') && ($pay_period_end_d == '15')){

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
$days_in_month = $_SESSION ['days_in_month'];
$header_colspan = $days_in_month + 3;
//echo '<br>Days in month: '.$days_in_month;
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

echo '<br>Employee Type: '.$_SESSION['employee_type_owner'];

echo '<br>MyBudgetCodes FoundCount: '.$searchResult['foundCount'];
}

##########################################
## END: PRINT VARIABLES FOR DEBUGGING ##
##########################################

if ($searchResult['foundCount'] > 0) { 

###############################################################################
## START: DISPLAY THE TIMESHEET IN AN HTML TABLE IN VIEW MODE FOR BA TO SIGN ##
###############################################################################
include_once('timesheets_view_ba.php'); //modularized the standard timesheet view used by staff
#############################################################################
## END: DISPLAY THE TIMESHEET IN AN HTML TABLE IN VIEW MODE FOR BA TO SIGN ##
#############################################################################


} else { ?>

ERROR: No records found.

<?php } ?>
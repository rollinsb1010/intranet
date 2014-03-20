<?php
session_start();

include_once('sims_checksession.php');

include_once('FX/FX.php');
include_once('FX/server_data.php');

$debug = 'off';

#################################################
## START: GRAB VARIABLES FROM THE TIMESHEET FORM
#################################################
//$sortfield = $_GET['sortfield'];
$timesheet_ID = $_GET['timesheet_ID'];
//echo '<br>Timesheet_ID: '.$timesheet_ID;
$action = $_GET['action'];
//echo '<br>Action: '.$action;
//$new_row = $_GET['new_row'];
$row_ID = $_GET['edit_row_ID'];
//echo '<br>Current Row ID: '.$row_ID;
$new_row_ID = $_GET['new_row_ID'];
//echo '<br>New Row ID: '.$new_row_ID;
$days_in_month = $_SESSION['days_in_month'];
//echo '<br>Days in Month: '.$days_in_month;
$header_colspan = $days_in_month + 5;
//echo '<br>Header colspan: '.$header_colspan;
//echo '<br>Staff_ID: '.$staff_ID;
$budget_code = $_GET['budget_code'];
if($budget_code == ''){
$budget_code = 'not entered';
}

//echo '<br>Budget Code: '.$budget_code;
//$HrsType = $_GET['HrsType'];
$LvType = trim($_GET['LvType']);
$hrs_01 = trim($_GET['hrs_01']);
$hrs_02 = trim($_GET['hrs_02']);
$hrs_03 = trim($_GET['hrs_03']);
$hrs_04 = trim($_GET['hrs_04']);
$hrs_05 = trim($_GET['hrs_05']);
$hrs_06 = trim($_GET['hrs_06']);
$hrs_07 = trim($_GET['hrs_07']);
$hrs_08 = trim($_GET['hrs_08']);
$hrs_09 = trim($_GET['hrs_09']);
$hrs_10 = trim($_GET['hrs_10']);
$hrs_11 = trim($_GET['hrs_11']);
$hrs_12 = trim($_GET['hrs_12']);
$hrs_13 = trim($_GET['hrs_13']);
$hrs_14 = trim($_GET['hrs_14']);
$hrs_15 = trim($_GET['hrs_15']);
$hrs_16 = trim($_GET['hrs_16']);
$hrs_17 = trim($_GET['hrs_17']);
$hrs_18 = trim($_GET['hrs_18']);
$hrs_19 = trim($_GET['hrs_19']);
$hrs_20 = trim($_GET['hrs_20']);
$hrs_21 = trim($_GET['hrs_21']);
$hrs_22 = trim($_GET['hrs_22']);
$hrs_23 = trim($_GET['hrs_23']);
$hrs_24 = trim($_GET['hrs_24']);
$hrs_25 = trim($_GET['hrs_25']);
$hrs_26 = trim($_GET['hrs_26']);
$hrs_27 = trim($_GET['hrs_27']);
$hrs_28 = trim($_GET['hrs_28']);
$hrs_29 = trim($_GET['hrs_29']);
$hrs_30 = trim($_GET['hrs_30']);
$hrs_31 = trim($_GET['hrs_31']);

//echo $row_ID;
#################################################
## END: GRAB VARIABLES FROM THE TIMESHEET FORM
#################################################






if ($action == 'confirm_edit') { 

//echo $row_ID;
//echo $new_row_ID;


#################################################################
## START: FIND THE FMP TIME_HRS ROW FOR EDITING & GET THE REC-ID
#################################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','time_hrs');
$search3 -> SetDBPassword($webPW,$webUN);

if($new_row_ID == ''){
$search3 -> AddDBParam('c_cwp_row_ID',$row_ID);
$current_id = $row_ID;
}else{
$search3 -> AddDBParam('c_cwp_row_ID',$new_row_ID);
$current_id = $new_row_ID;
$new_reg_hrs_added = '1';
}
$searchResult3 = $search3 -> FMFind();

$recordData7 = current($searchResult3['data']);

$_SESSION['bgt_auth_revised'] = $recordData7['BudgetAuthorityLocal'][0]; //GET BUDGET AUTHORITY WHOSE TIME WAS EDITED

//echo '<br>$_SESSION[timesheet_status]: '.$_SESSION['timesheet_status'];
//echo '<br>$_SESSION[bgt_auth_revised]: '.$_SESSION['bgt_auth_revised'];
//echo 'ErrorCode: '.$searchResult3['errorCode'];
//echo '<br>FoundCount: '.$searchResult3['foundCount'];

###GET THE RECORD ID OF THE ROW FOR EDITING###
//foreach($searchResult3['data'] as $key => $searchData3); //searchResult3 -> fmp table = time_hrs (check the missing curly braces on this one??)
//$recordDetail = explode('.',$key);
//$current_id = $recordDetail[0];




//echo $current_id;
#################################################################
## END: FIND THE FMP TIME_HRS ROW FOR EDITING & GET THE REC-ID
#################################################################

#####################################################
## START: FIND WEEKEND DAYS FOR SHADING ON TIMESHEET
#####################################################
$pay_period_end_m = $recordData7['timesheets::c_PayPeriodEnd_m'][0];
$pay_period_end_y = $recordData7['timesheets::c_PayPeriodEnd_y'][0];

$day_of_week_01 = date("D", mktime(0,0,0,$pay_period_end_m,1,$pay_period_end_y));
$day_of_week_02 = date("D", mktime(0,0,0,$pay_period_end_m,2,$pay_period_end_y));
$day_of_week_03 = date("D", mktime(0,0,0,$pay_period_end_m,3,$pay_period_end_y));
$day_of_week_04 = date("D", mktime(0,0,0,$pay_period_end_m,4,$pay_period_end_y));
$day_of_week_05 = date("D", mktime(0,0,0,$pay_period_end_m,5,$pay_period_end_y));
$day_of_week_06 = date("D", mktime(0,0,0,$pay_period_end_m,6,$pay_period_end_y));
$day_of_week_07 = date("D", mktime(0,0,0,$pay_period_end_m,7,$pay_period_end_y));
$day_of_week_08 = date("D", mktime(0,0,0,$pay_period_end_m,8,$pay_period_end_y));
$day_of_week_09 = date("D", mktime(0,0,0,$pay_period_end_m,9,$pay_period_end_y));
$day_of_week_10 = date("D", mktime(0,0,0,$pay_period_end_m,10,$pay_period_end_y));
$day_of_week_11 = date("D", mktime(0,0,0,$pay_period_end_m,11,$pay_period_end_y));
$day_of_week_12 = date("D", mktime(0,0,0,$pay_period_end_m,12,$pay_period_end_y));
$day_of_week_13 = date("D", mktime(0,0,0,$pay_period_end_m,13,$pay_period_end_y));
$day_of_week_14 = date("D", mktime(0,0,0,$pay_period_end_m,14,$pay_period_end_y));
$day_of_week_15 = date("D", mktime(0,0,0,$pay_period_end_m,15,$pay_period_end_y));
$day_of_week_16 = date("D", mktime(0,0,0,$pay_period_end_m,16,$pay_period_end_y));
$day_of_week_17 = date("D", mktime(0,0,0,$pay_period_end_m,17,$pay_period_end_y));
$day_of_week_18 = date("D", mktime(0,0,0,$pay_period_end_m,18,$pay_period_end_y));
$day_of_week_19 = date("D", mktime(0,0,0,$pay_period_end_m,19,$pay_period_end_y));
$day_of_week_20 = date("D", mktime(0,0,0,$pay_period_end_m,20,$pay_period_end_y));
$day_of_week_21 = date("D", mktime(0,0,0,$pay_period_end_m,21,$pay_period_end_y));
$day_of_week_22 = date("D", mktime(0,0,0,$pay_period_end_m,22,$pay_period_end_y));
$day_of_week_23 = date("D", mktime(0,0,0,$pay_period_end_m,23,$pay_period_end_y));
$day_of_week_24 = date("D", mktime(0,0,0,$pay_period_end_m,24,$pay_period_end_y));
$day_of_week_25 = date("D", mktime(0,0,0,$pay_period_end_m,25,$pay_period_end_y));
$day_of_week_26 = date("D", mktime(0,0,0,$pay_period_end_m,26,$pay_period_end_y));
$day_of_week_27 = date("D", mktime(0,0,0,$pay_period_end_m,27,$pay_period_end_y));
$day_of_week_28 = date("D", mktime(0,0,0,$pay_period_end_m,28,$pay_period_end_y));
$day_of_week_29 = date("D", mktime(0,0,0,$pay_period_end_m,29,$pay_period_end_y));
$day_of_week_30 = date("D", mktime(0,0,0,$pay_period_end_m,30,$pay_period_end_y));
$day_of_week_31 = date("D", mktime(0,0,0,$pay_period_end_m,31,$pay_period_end_y));
#####################################################
## END: FIND WEEKEND DAYS FOR SHADING ON TIMESHEET
#####################################################

################################
## START: UPDATE THE FMP RECORD
################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','time_hrs');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

//if($new_reg_hrs_added == '1'){
$update -> AddDBParam('BudgetCode',$budget_code);
//}

//$update -> AddDBParam('HrsType',$HrsType);
$update -> AddDBParam('LvType',$LvType);
$update -> AddDBParam('Hrs01',$hrs_01);
$update -> AddDBParam('Hrs02',$hrs_02);
$update -> AddDBParam('Hrs03',$hrs_03);
$update -> AddDBParam('Hrs04',$hrs_04);
$update -> AddDBParam('Hrs05',$hrs_05);
$update -> AddDBParam('Hrs06',$hrs_06);
$update -> AddDBParam('Hrs07',$hrs_07);
$update -> AddDBParam('Hrs08',$hrs_08);
$update -> AddDBParam('Hrs09',$hrs_09);
$update -> AddDBParam('Hrs10',$hrs_10);
$update -> AddDBParam('Hrs11',$hrs_11);
$update -> AddDBParam('Hrs12',$hrs_12);
$update -> AddDBParam('Hrs13',$hrs_13);
$update -> AddDBParam('Hrs14',$hrs_14);
$update -> AddDBParam('Hrs15',$hrs_15);
$update -> AddDBParam('Hrs16',$hrs_16);
$update -> AddDBParam('Hrs17',$hrs_17);
$update -> AddDBParam('Hrs18',$hrs_18);
$update -> AddDBParam('Hrs19',$hrs_19);
$update -> AddDBParam('Hrs20',$hrs_20);
$update -> AddDBParam('Hrs21',$hrs_21);
$update -> AddDBParam('Hrs22',$hrs_22);
$update -> AddDBParam('Hrs23',$hrs_23);
$update -> AddDBParam('Hrs24',$hrs_24);
$update -> AddDBParam('Hrs25',$hrs_25);
$update -> AddDBParam('Hrs26',$hrs_26);
$update -> AddDBParam('Hrs27',$hrs_27);
$update -> AddDBParam('Hrs28',$hrs_28);
$update -> AddDBParam('Hrs29',$hrs_29);
$update -> AddDBParam('Hrs30',$hrs_30);
$update -> AddDBParam('Hrs31',$hrs_31);

if(($_SESSION['timesheet_status'] == 'Pending')||($_SESSION['timesheet_status'] == 'Approved')||($_SESSION['timesheet_status'] == 'Revised')){
$update -> AddDBParam('TimeRevisedStatus','1');
$update -> AddDBParam('HrsApproved','1');
$update -> AddDBParam('timesheets::TimesheetSubmittedStatus','Revised');
$update -> AddDBParam('timesheets::approved_by_auth_rep_status','');
$update -> AddDBParam('timesheets::TimesheetBeingRevised','1');
/*
if($_SESSION['bgt_auth_revised'] == $recordData7['timesheets::Signer_ID_bgt_auth_1'][0]){
$update -> AddDBParam('timesheets::Signer_status_bgt_auth_1','');
}

if($_SESSION['bgt_auth_revised'] == $recordData7['timesheets::Signer_ID_bgt_auth_2'][0]){		
$update -> AddDBParam('timesheets::Signer_status_bgt_auth_2','');
}

if($_SESSION['bgt_auth_revised'] == $recordData7['timesheets::Signer_ID_bgt_auth_3'][0]){		
$update -> AddDBParam('timesheets::Signer_status_bgt_auth_3','');
}

if($_SESSION['bgt_auth_revised'] == $recordData7['timesheets::Signer_ID_bgt_auth_4'][0]){		
$update -> AddDBParam('timesheets::Signer_status_bgt_auth_4','');
}

if($_SESSION['bgt_auth_revised'] == $recordData7['timesheets::Signer_ID_bgt_auth_5'][0]){		
$update -> AddDBParam('timesheets::Signer_status_bgt_auth_5','');
}

if($_SESSION['bgt_auth_revised'] == $recordData7['timesheets::Signer_ID_bgt_auth_6'][0]){		
$update -> AddDBParam('timesheets::Signer_status_bgt_auth_6','');
}

if($_SESSION['bgt_auth_revised'] == $recordData7['timesheets::Signer_ID_bgt_auth_7'][0]){		
$update -> AddDBParam('timesheets::Signer_status_bgt_auth_7','');
}

if($_SESSION['bgt_auth_revised'] == $recordData7['timesheets::Signer_ID_bgt_auth_8'][0]){		
$update -> AddDBParam('timesheets::Signer_status_bgt_auth_8','');
}

if($_SESSION['bgt_auth_revised'] == $recordData7['timesheets::Signer_ID_bgt_auth_OT'][0]){		
$update -> AddDBParam('timesheets::Signer_status_bgt_auth_OT','');
}
*/
$update -> AddDBParam('timesheets::Signer_status_imm_spvsr','');


$update -> AddDBParam('timesheets::Signer_status_pba','');



}


$updateResult = $update -> FMEdit();

$recordData8 = current($updateResult['data']);

$_SESSION['total_timesheet_hrs'] = $recordData8['timesheets::c_total_timesheet_hrs'][0];

################################
## END: UPDATE THE FMP RECORD
################################

if($updateResult['errorCode']==0) {

#########################################################################
## START: UPDATE BudgetAuthorityLocal FIELD IF NEW REG_HRS ROW WAS ADDED
#########################################################################
if($new_reg_hrs_added == '1'){ //IF NEW REG HRS WERE ADDED

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','time_hrs');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

if($_SESSION['signer_pba_is_spvsr'] == 1){ //IF IMM SPVSR & PBA IS THE SAME PERSON
$signer_box_num = $_SESSION['total_other_signers'] + 2; //FINDS NEXT AVAILABLE BLANK SIGNATURE BOX
}elseif($_SESSION['signer_pba_is_spvsr'] != 1){ //IF IMM SPVSR & PBA IS NOT THE SAME PERSON
$signer_box_num = $_SESSION['total_other_signers'] + 1; //FINDS NEXT AVAILABLE BLANK SIGNATURE BOX
}

$update -> AddDBParam('timesheets::Signer_ID_bgt_auth_'.$signer_box_num,$recordData8['BudgetAuthorityLocal'][0]);

$updateResult2 = $update -> FMEdit();

if($updateResult2['errorCode']==0) {
$_SESSION['bgt_auth_revised'] = $recordData8['BudgetAuthorityLocal'][0]; //GET BUDGET AUTHORITY WHOSE TIME WAS ADDED
}
}
########################################################################
## END: UPDATE BudgetAuthorityLocal FIELD IF NEW REG_HRS ROW WAS ADDED
########################################################################


//echo '<br>$_SESSION[timesheet_status]: '.$_SESSION['timesheet_status'];
//echo '<br>$_SESSION[bgt_auth_revised]: '.$_SESSION['bgt_auth_revised'];
//echo '<br>$signer_box_num: '.$signer_box_num;

$_SESSION['signer_status_owner'] = $recordData8['timesheets::Signer_status_owner'][0];
$_SESSION['signer_status_imm_spvsr'] = $recordData8['timesheets::Signer_status_imm_spvsr'][0];
$_SESSION['signer_status_pba'] = $recordData8['timesheets::Signer_status_pba'][0];
$_SESSION['signer_status_bgt_auth_1'] = $recordData8['timesheets::Signer_status_bgt_auth_1'][0];
$_SESSION['signer_status_bgt_auth_2'] = $recordData8['timesheets::Signer_status_bgt_auth_2'][0];
$_SESSION['signer_status_bgt_auth_3'] = $recordData8['timesheets::Signer_status_bgt_auth_3'][0];
$_SESSION['signer_status_bgt_auth_4'] = $recordData8['timesheets::Signer_status_bgt_auth_4'][0];
$_SESSION['signer_status_bgt_auth_5'] = $recordData8['timesheets::Signer_status_bgt_auth_5'][0];
$_SESSION['signer_status_bgt_auth_6'] = $recordData8['timesheets::Signer_status_bgt_auth_6'][0];
$_SESSION['signer_status_bgt_auth_7'] = $recordData8['timesheets::Signer_status_bgt_auth_7'][0];
$_SESSION['signer_status_bgt_auth_8'] = $recordData8['timesheets::Signer_status_bgt_auth_8'][0];
$_SESSION['signer_status_bgt_auth_OT'] = $recordData8['timesheets::Signer_status_bgt_auth_OT'][0];

$_SESSION['signer_ID_bgt_auth_1'] = $recordData8['timesheets::Signer_ID_bgt_auth_1'][0];
$_SESSION['signer_ID_bgt_auth_2'] = $recordData8['timesheets::Signer_ID_bgt_auth_2'][0];
$_SESSION['signer_ID_bgt_auth_3'] = $recordData8['timesheets::Signer_ID_bgt_auth_3'][0];
$_SESSION['signer_ID_bgt_auth_4'] = $recordData8['timesheets::Signer_ID_bgt_auth_4'][0];
$_SESSION['signer_ID_bgt_auth_5'] = $recordData8['timesheets::Signer_ID_bgt_auth_5'][0];
$_SESSION['signer_ID_bgt_auth_6'] = $recordData8['timesheets::Signer_ID_bgt_auth_6'][0];
$_SESSION['signer_ID_bgt_auth_7'] = $recordData8['timesheets::Signer_ID_bgt_auth_7'][0];
$_SESSION['signer_ID_bgt_auth_8'] = $recordData8['timesheets::Signer_ID_bgt_auth_8'][0];
$_SESSION['signer_ID_bgt_auth_OT'] = $recordData8['timesheets::Signer_ID_bgt_auth_OT'][0];
$_SESSION['signer_ID_owner'] = $recordData8['timesheets::Signer_ID_owner'][0];
$_SESSION['signer_ID_imm_spvsr'] = $recordData8['timesheets::StaffImmediateSupervisor'][0];
$_SESSION['signer_ID_pba'] = $recordData8['timesheets::StaffPrimaryBudgetAuthority'][0];

$_SESSION['signer_timestamp_owner'] = $recordData8['timesheets::Signer_Timestamp_owner'][0];
$_SESSION['signer_timestamp_bgt_auth_1'] = $recordData8['timesheets::Signer_Timestamp_bgt_auth_1'][0];
$_SESSION['signer_timestamp_bgt_auth_2'] = $recordData8['timesheets::Signer_Timestamp_bgt_auth_2'][0];
$_SESSION['signer_timestamp_bgt_auth_3'] = $recordData8['timesheets::Signer_Timestamp_bgt_auth_3'][0];
$_SESSION['signer_timestamp_bgt_auth_4'] = $recordData8['timesheets::Signer_Timestamp_bgt_auth_4'][0];
$_SESSION['signer_timestamp_bgt_auth_5'] = $recordData8['timesheets::Signer_Timestamp_bgt_auth_5'][0];
$_SESSION['signer_timestamp_bgt_auth_6'] = $recordData8['timesheets::Signer_Timestamp_bgt_auth_6'][0];
$_SESSION['signer_timestamp_bgt_auth_7'] = $recordData8['timesheets::Signer_Timestamp_bgt_auth_7'][0];
$_SESSION['signer_timestamp_bgt_auth_8'] = $recordData8['timesheets::Signer_Timestamp_bgt_auth_8'][0];
$_SESSION['signer_timestamp_bgt_auth_OT'] = $recordData8['timesheets::Signer_Timestamp_bgt_auth_OT'][0];
$_SESSION['signer_timestamp_imm_spvsr'] = $recordData8['timesheets::Signer_Timestamp_imm_spvsr'][0];
$_SESSION['signer_timestamp_pba'] = $recordData8['timesheets::Signer_Timestamp_pba'][0];

$_SESSION['signer_fullname_owner'] = $recordData8['timesheets::TimeSheetOwnerFullName'][0];
$_SESSION['approved_by_auth_rep_full_name'] = $recordData8['timesheets::approved_by_auth_rep_full_name'][0];
$_SESSION['signer_fullname_imm_spvsr'] = $recordData8['timesheets::Signer_fullname_imm_spvsr'][0];
$_SESSION['signer_fullname_pba'] = $recordData8['timesheets::Signer_fullname_pba'][0];
$_SESSION['signer_fullname_bgt_auth_1'] = $recordData8['timesheets::Signer_fullname_bgt_auth_1'][0];
$_SESSION['signer_fullname_bgt_auth_2'] = $recordData8['timesheets::Signer_fullname_bgt_auth_2'][0];
$_SESSION['signer_fullname_bgt_auth_3'] = $recordData8['timesheets::Signer_fullname_bgt_auth_3'][0];
$_SESSION['signer_fullname_bgt_auth_4'] = $recordData8['timesheets::Signer_fullname_bgt_auth_4'][0];
$_SESSION['signer_fullname_bgt_auth_5'] = $recordData8['timesheets::Signer_fullname_bgt_auth_5'][0];
$_SESSION['signer_fullname_bgt_auth_6'] = $recordData8['timesheets::Signer_fullname_bgt_auth_6'][0];
$_SESSION['signer_fullname_bgt_auth_7'] = $recordData8['timesheets::Signer_fullname_bgt_auth_7'][0];
$_SESSION['signer_fullname_bgt_auth_8'] = $recordData8['timesheets::Signer_fullname_bgt_auth_8'][0];
$_SESSION['signer_fullname_bgt_auth_OT'] = $recordData8['timesheets::Signer_fullname_bgt_auth_OT'][0];

$_SESSION['signer_pba_is_spvsr'] = $recordData8['timesheets::c_cwp_pba_is_spvsr'][0];
$_SESSION['timesheet_name_owner'] = $recordData8['timesheets::TimeSheetName'][0];
$_SESSION['workgroup_name_owner'] = $recordData8['timesheets::staff_primary_workgroup'][0];
$_SESSION['employee_type_owner'] = $recordData8['timesheets::c_timesheet_employee_type'][0];
$_SESSION['timesheet_owner_is_admin'] = $recordData8['timesheets::staff_is_time_leave_admin'][0];
$_SESSION['timesheet_owner_FTE_status'] = $recordData8['timesheets::staff_FTE_status'][0];
$_SESSION['timesheet_approval_not_required'] = $recordData8['timesheets::staff_no_time_leave_approval_required'][0];
$_SESSION['timesheet_status'] = $recordData8['timesheets::TimesheetSubmittedStatus'][0];



//echo '<p>Timesheet was successfully updated.';
} else {
echo 'There was an error updating your record.<br>';
echo 'UpdateError: '.$updateResult['errorCode'];

exit;
}
################################
## END: UPDATE THE FMP RECORD
################################

#####################################################
## START: FIND REGULAR WORK HOURS FOR THIS TIMESHEET
#####################################################
//echo $row_ID;
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('Timesheet_ID',$timesheet_ID);
$search -> AddDBParam('HrsType','WkHrsReg');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam ($sortfield,'descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);

//$searchResult = $_SESSION['wk_hrs_data'];
$recordData = current($searchResult['data']);

$staff_ID = $recordData['timesheets::staff_ID'][0];
//$days_in_month = $recordData['c_days_in_month'][0];
#####################################################
## END: FIND REGULAR WORK HOURS FOR THIS TIMESHEET
#####################################################

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

$search3 -> AddSortParam ($sortfield,'descend');


$searchResult3 = $search3 -> FMFind();

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//print_r ($searchResult3);

//$searchResult3 = $_SESSION['pdlv_hrs_data'];
//$recordData3 = current($searchResult3['data']);
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

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//print_r ($searchResult3);
$recordData4 = current($searchResult4['data']);

#####################################################
## END: FIND UNPAID LEAVE HOURS FOR THIS TIMESHEET
#####################################################

######################################################
## START: FIND OVERTIME WORK HOURS FOR THIS TIMESHEET
######################################################

if($_SESSION['employee_type'] == 'Non-exempt') {
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
//echo $searchResult5['foundCount'];
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

################################################
## START: PRINT VARIABLE VALUES FOR DEBUGGING ##
################################################
if ($debug == 'on'){
echo '<br>Timesheet_ID: '.$timesheet_ID;
echo '<br>Action: '.$action;
echo '<br>Current Edit Row ID: '.$row_ID;
echo '<br>New Row ID: '.$new_row_ID;
echo '<br>Days in Month: '.$days_in_month;
echo '<br>Header colspan: '.$header_colspan;
echo '<br>Staff_ID: '.$staff_ID;
echo '<br>Budget Code: '.$budget_code;
}

################################################
## END: PRINT VARIABLE VALUES FOR DEBUGGING ##
################################################


#############################################################################################
## START: DISPLAY THE TIMESHEET IN AN HTML TABLE IN VIEW MODE AFTER EDITS ARE CONFIRMED
#############################################################################################
include_once('timesheets_view_st.php'); //modularized the standard timesheet view used by staff
#############################################################################################
## END: DISPLAY THE TIMESHEET IN AN HTML TABLE IN VIEW MODE AFTER EDITS ARE CONFIRMED
#############################################################################################

} else { ?>

No records found.

<?php } ?>
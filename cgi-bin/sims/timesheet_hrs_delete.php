<?php
session_start();

include_once('sims_checksession.php');

include_once('FX/FX.php');
include_once('FX/server_data.php');

#########################################
## START: GET THE TIME_HRS ROW TO DELETE
#########################################
$row_ID = $_GET['row_ID'];
$bgt_auth = $_GET['bgt_auth'];
$timesheet_ID = $_SESSION['timesheet_ID'];
$days_in_month = $_SESSION['days_in_month'];
//echo '<br>Days in Month: '.$days_in_month;
$header_colspan = $days_in_month + 5;
//echo '<br>Header colspan: '.$header_colspan;

$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('c_cwp_row_ID',$row_ID);

$searchResult = $search -> FMFind();
$recordData7 = current($searchResult['data']);
//echo 'ErrorCode: '.$searchResult['errorCode'];
//echo '<br>FoundCount: '.$searchResult['foundCount'];
//echo '<br>RowID: '.$row_ID;
//echo '<br>TimesheetID: '.$timesheet_ID;




if($searchResult['foundCount'] == 1){

foreach($searchResult['data'] as $key => $searchData);
$recordDetail = explode('.',$key);
$current_id = $recordDetail[0];
#########################################
## END: GET THE TIME_HRS ROW TO DELETE
#########################################

#####################################################
## START: FIND WEEKEND DAYS FOR SHADING ON TIMESHEET
#####################################################
$pay_period_end_m = $searchData['timesheets::c_PayPeriodEnd_m'][0];
$pay_period_end_y = $searchData['timesheets::c_PayPeriodEnd_y'][0];

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

#########################################
## START: DELETE THE TIME_HRS ROW IN FMP
#########################################
$delete = new FX($serverIP,$webCompanionPort);
$delete -> SetDBData('SIMS_2.fp7','time_hrs');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$current_id);

$deleteResult = $delete -> FMDelete();

//echo '<br>deleteResult ErrorCode: '.$deleteResult['errorCode'];
#########################################
## END: DELETE THE TIME_HRS ROW IN FMP
#########################################

####################################################################
## START: UPDATE TIMESHEET STATUS IF TIMESHEET WAS ALREADY SIGNED ##
####################################################################
if(($_SESSION['timesheet_status'] == 'Pending')||($_SESSION['timesheet_status'] == 'Approved')||($_SESSION['timesheet_status'] == 'Revised')){
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_SESSION['timesheet_row_ID']);

$update -> AddDBParam('TimesheetSubmittedStatus','Revised');
$update -> AddDBParam('approved_by_auth_rep_status','');
$update -> AddDBParam('Signer_status_imm_spvsr','');
$update -> AddDBParam('Signer_status_pba','');

$updateResult = $update -> FMEdit();

$recordData9 = current($updateResult['data']);

$_SESSION['total_timesheet_hrs'] = $recordData9['c_total_timesheet_hrs'][0];

}
##################################################################
## END: UPDATE TIMESHEET STATUS IF TIMESHEET WAS ALREADY SIGNED ##
##################################################################

#######################################################################
## START: ERASE BUDGET AUTHORITY SIGNER IDs ON THE TIMESHEET RECORD ##
#######################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_SESSION['timesheet_row_ID']);



$update -> AddDBParam('Signer_ID_bgt_auth_1','');
$update -> AddDBParam('Signer_ID_bgt_auth_2','');
$update -> AddDBParam('Signer_ID_bgt_auth_3','');
$update -> AddDBParam('Signer_ID_bgt_auth_4','');
$update -> AddDBParam('Signer_ID_bgt_auth_5','');
$update -> AddDBParam('Signer_ID_bgt_auth_6','');
$update -> AddDBParam('Signer_ID_bgt_auth_7','');
$update -> AddDBParam('Signer_ID_bgt_auth_8','');

$updateResult = $update -> FMEdit();

//echo '<p>Update errorCode (erase budget authority IDs): '.$updateResult['errorCode'];
//exit;
$recordData = current($updateResult['data']);
#####################################################################
## END: ERASE BUDGET AUTHORITY SIGNER IDs ON THE TIMESHEET RECORD ##
#####################################################################

#######################################################################
## START: RESET BUDGET AUTHORITY SIGNER IDs ON THE TIMESHEET RECORD ##
#######################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_SESSION['timesheet_row_ID']);

$update -> AddDBParam('Signer_ID_imm_spvsr',$_SESSION['immediate_supervisor']);
$update -> AddDBParam('Signer_ID_pba',$_SESSION['primary_bgt_auth']);
$update -> AddDBParam('total_oba_signers',$_SESSION['total_other_signers']);


if($_SESSION['signer_pba_is_spvsr'] == 1) {

	$update -> AddDBParam('Signer_ID_bgt_auth_1',$_SESSION['primary_bgt_auth']);
	
	$i=2;
	foreach($_SESSION['other_signers'] as $current){
		
	$update -> AddDBParam('Signer_ID_bgt_auth_'.$i,$current);
	
	$i++;
	}
	$i = $i - 1;
	for($i;$i < 9;$i++){
	$update -> AddDBParam('Signer_ID_bgt_auth_'.$i,'');
	$i++;
	}

} else {


	$update -> AddDBParam('Signer_ID_bgt_auth_1',$_SESSION['immediate_supervisor']);
	
	$i=2;
	foreach($_SESSION['other_signers'] as $current){
		
	$update -> AddDBParam('Signer_ID_bgt_auth_'.$i,$current);
	
	$i++;
	}
	$i = $i - 1;
	for($i;$i < 9;$i++){
	$update -> AddDBParam('Signer_ID_bgt_auth_'.$i,'');
	$i++;
	}

}




//$update -> AddDBParam('Signer_ID_bgt_auth_OT',$_SESSION['sims_user_ID']);
$update -> AddDBParam('signatures_required',$_SESSION['signatures_required']);
$update -> AddDBParam('signatures_required_oba',$_SESSION['total_other_signers']);

$updateResult = $update -> FMEdit();

//echo '<p>Update errorCode (reset budget authority IDs): '.$updateResult['errorCode'];

$recordData = current($updateResult['data']);
#####################################################################
## END: RESET BUDGET AUTHORITY SIGNER IDs ON THE TIMESHEET RECORD ##
#####################################################################


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
$_SESSION['blank_rows_check'] = $recordData['timesheets::c_sum_blank_rows_check'][0];
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

#############################################################################################
## START: DISPLAY THE TIMESHEET IN AN HTML TABLE IN VIEW MODE AFTER DELETE IS CONFIRMED
#############################################################################################
include_once('timesheets_view_st.php'); //modularized the standard timesheet view used by staff
#############################################################################################
## END: DISPLAY THE TIMESHEET IN AN HTML TABLE IN VIEW MODE AFTER DELETE IS CONFIRMED
#############################################################################################

} else { ?>

No records found.

<?php } ?>
<?php
session_start();

include_once('FX/FX.php');
include_once('FX/server_data.php');

################################
## START: GRAB FORM VARIABLES ##
################################
$src = $_GET['src'];
$object_ID = $_GET['staff_ID'];
$table_name = $_GET['target'];
//$based_on = $_GET['staff_based_on'];
$ip = $_SERVER['REMOTE_ADDR'];
$this_month = date("M");
$this_year = date("Y");
$session_cookie = $_COOKIE['ss_session_id']; 
##############################
## END: GRAB FORM VARIABLES ##
##############################
//echo '<p>$object_ID: '.$object_ID = $_GET['staff_ID'];
//echo '<p>$table_name: '.$table_name = $_GET['target'];
//echo '<p>$based_on: '.$based_on = $_GET['staff_based_on'];
//echo '<p>$ip: '.$ip = $_SERVER['REMOTE_ADDR'];
//echo '<p>$this_month: '.$this_month = date("m");
//echo '<p>$this_year: '.$this_year = date("Y");
//exit;

//if($based_on == 'new'){ // THIS IS A NEW (BLANK) PLANNING AGREEMENT
############################################
## START: CREATE THE NEW PLAN AGRMT RECORD ##
############################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','planning_agreements');
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('staff_ID',$object_ID); //send staff_ID to FMP
$newrecord -> AddDBParam('Performance_Period',$this_month.' '.$this_year.' - ???');
//$newrecord -> AddDBParam('Performance_PeriodAnnual',$this_month.' '.$this_year.' - ???');
$newrecord -> AddDBParam('prepared_by',$_SESSION['user_ID']);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$newrecordData = current($newrecordResult['data']);
$newrecordID = $newrecordData['RecordID'][0]; //get ID of new PA record

if($newrecordResult['errorCode'] == '0'){
$_SESSION['new_pa_created'] = '1';
}else{
$_SESSION['new_pa_created'] = '2';
$_SESSION['new_pa_error_code'] = $newrecordResult['errorCode'];
}
##########################################
## END: CREATE THE NEW PLAN AGRMT RECORD ##
##########################################
/*
}else{ // THIS PLANNING AGREEMENT IS BASED ON (PRE-POPULATED BY) A PREVIOUS PA

##################################
## START: CREATE THE FMP RECORD ##
##################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','sims_temp_launcher');
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('IP',$ip);
$newrecord -> AddDBParam('object_ID',$object_ID); // staff_ID
$newrecord -> AddDBParam('table_name',$table_name);
$newrecord -> AddDBParam('action','admin-new-dupl-PA');
$newrecord -> AddDBParam('pd_based_on',$based_on);
$newrecord -> AddDBParam('user_ID',$_SESSION['user_ID']);
$newrecord -> AddDBParam('g_temp_ID',$_SESSION['user_ID']);
$newrecord -> AddDBParam('sims_session_id',$session_cookie);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode1: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount1: '.$newrecordResult['foundCount'];
##################################
## END: CREATE THE FMP RECORD ##
##################################

#################################################
## START: DUPLICATE A PREVIOUS PA IN FMP ##
#################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','planning_agreements');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('staff_sims_ID','=='.$based_on);
$search -> AddSortParam('creation_timestamp','descend'); // ADJUST TIMESTAMPS TO REFLECT ORDER IF NECESSARY - creation_timestamp WAS RESET ON IMPORT TO NEW SIMS
$search -> AddDBParam('-script', 'duplicate_plan_agrmt_web');

$searchResult = $search -> FMFind();
//echo '<p>FoundCount: '.$searchResult['foundCount'];
//echo '<p>errorCode2: '.$searchResult['errorCode'];
//exit;
$searchData = current($searchResult['data']);
//echo '<p>planning period: '.$searchData['Performance_Period'][0];
$newrecordID = $searchData['RecordID'][0];
$row_ID = $searchData['RecordID'][0];
//echo '<p>$newrecordID: '.$newrecordID;
//exit;
#################################################
## END: DUPLICATE A PREVIOUS PA IN FMP ##
#################################################

#################################################
## START: UPDATE DUPLICATED PA WITH NEW STAFF ID ##
#################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','planning_agreements');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid', $row_ID);
$update -> AddDBParam('staff_ID',$object_ID);
$update -> AddDBParam('Performance_Period',$this_month.' '.$this_year.' - ???');
//$update -> AddDBParam('Performance_PeriodAnnual',$this_month.' '.$this_year.' - ???');
$update -> AddDBParam('prepared_by',$_SESSION['user_ID']);

$updateResult = $update -> FMEdit();
//echo '<p>FoundCount2: '.$updateResult['foundCount'];
//echo '<p>errorCode2: '.$updateResult['errorCode'];

$updateData = current($updateResult['data']);

//echo '<p>$newrecordID: '.$newrecordID;
//exit;
if($updateResult['errorCode'] == '0'){
$_SESSION['new_pa_created'] = '1';
}else{
$_SESSION['new_pa_created'] = '2';
$_SESSION['new_pa_error_code'] = $updateResult['errorCode'];
}


#################################################
## END: UPDATE DUPLICATED PA WITH NEW STAFF ID ##
#################################################

}
*/
if($src == 'staff'){
header('Location: http://www.sedl.org/staff/sims/staff_plan_agrmt.php?action=show_mine&staff_ID='.$object_ID);
}else{
header('Location: http://www.sedl.org/staff/sims/staff_plan_agrmt_admin.php?action=show_mine&staff_ID='.$object_ID);
}

?>
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
$this_month = date("m");
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

//if($based_on == 'new'){ // THIS IS A NEW (BLANK) POSITION DESCRIPTION
############################################
## START: CREATE THE NEW POS DESCR RECORD ##
############################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','position_descriptions2');
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('staff_ID',$object_ID); //send staff_ID to FMP
$newrecord -> AddDBParam('date_prepared_month',$this_month);
$newrecord -> AddDBParam('date_prepared_year',$this_year);
$newrecord -> AddDBParam('prepared_by',$_SESSION['user_ID']);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$newrecordData = current($newrecordResult['data']);
$newrecordID = $newrecordData['RecordID'][0]; //get ID of new PD record

if($newrecordResult['errorCode'] == '0'){
$_SESSION['new_pd_created'] = '1';
}else{
$_SESSION['new_pd_created'] = '2';
$_SESSION['new_pd_error_code'] = $newrecordResult['errorCode'];
}
##########################################
## END: CREATE THE NEW POS DESCR RECORD ##
##########################################
/*
}else{ // THIS POSITION DESCRIPTION IS BASED ON (PRE-POPULATED BY) A PREVIOUS PD

##################################
## START: CREATE THE FMP RECORD ##
##################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','sims_temp_launcher');
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('IP',$ip);
$newrecord -> AddDBParam('object_ID',$object_ID); // staff_ID
$newrecord -> AddDBParam('table_name',$table_name);
$newrecord -> AddDBParam('action','admin-new-dupl');
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
## START: DUPLICATE A PREVIOUS PD IN FMP ##
#################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','position_descriptions');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('staff_sims_ID','=='.$based_on);
$search -> AddDBParam('-script', 'duplicate_pos_descr_web');
$search -> AddSortParam('c_date_prepared','descend');

$searchResult = $search -> FMFind();
//echo '<p>FoundCount2: '.$searchResult['foundCount'];
//echo '<p>errorCode2: '.$searchResult['errorCode'];

$searchData = current($searchResult['data']);

$newrecordID = $searchData['RecordID'][0];
$row_ID = $searchData['c_cwp_row_ID'][0];
//echo '<p>$newrecordID: '.$newrecordID;
//exit;
#################################################
## END: DUPLICATE A PREVIOUS PD IN FMP ##
#################################################

#################################################
## START: UPDATE DUPLICATED PD WITH NEW STAFF ID ##
#################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','position_descriptions');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid', $row_ID);
$update -> AddDBParam('staff_ID',$object_ID);
$update -> AddDBParam('date_prepared_month',$this_month);
$update -> AddDBParam('date_prepared_year',$this_year);
$update -> AddDBParam('prepared_by',$_SESSION['user_ID']);

$updateResult = $update -> FMEdit();
//echo '<p>FoundCount2: '.$updateResult['foundCount'];
//echo '<p>errorCode2: '.$updateResult['errorCode'];

$updateData = current($updateResult['data']);

//echo '<p>$newrecordID: '.$newrecordID;
//exit;
if($updateResult['errorCode'] == '0'){
$_SESSION['new_pd_created'] = '1';
}else{
$_SESSION['new_pd_created'] = '2';
$_SESSION['new_pd_error_code'] = $updateResult['errorCode'];
}


#################################################
## END: UPDATE DUPLICATED PD WITH NEW STAFF ID ##
#################################################

}
*/
if($src == 'staff'){
header('Location: http://www.sedl.org/staff/sims/staff_pos_descr.php?action=show_mine&staff_ID='.$object_ID);
}else{
header('Location: http://www.sedl.org/staff/sims/staff_pos_descr_admin.php?action=show_mine&staff_ID='.$object_ID);
}
?>
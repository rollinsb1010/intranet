<?php
session_start();

include_once('sims_checksession.php');

if($_SESSION['user_ID'] == ''){
header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
exit;
}
//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');



$debug = 'off';


$action = $_GET['action'];

if($action == ''){ 
$action = 'view';
}

$PO_ID = $_GET['id'];
$approve = $_GET['approve'];
//$status = $_GET['status'];



############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
if($action == 'view'){ //IF THE USER IS VIEWING THIS PURCHASE REQUEST


if($approve == '1') { // IF THE SIGNER APPROVED THE PR

$current_id = $_GET['appid'];
$signer = $_GET['ba'];
$role = $_GET['role'];

$trigger = rand();
#################################################
## START: UPDATE THE PURCHASE REQUEST ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('Purchase_Req_Order.fp7','PO_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

if(($role == 'IT')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_IT','1');
$update -> AddDBParam('-script','PR_sign_form_IT');
}
if(($role == 'IRC')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_IRC','1');
$update -> AddDBParam('-script','PR_sign_form_IRC');
}
if(($role == 'CEO-PRE')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ceo_preauth','1');
$update -> AddDBParam('-script','PR_sign_form_ceo_preauth');
}
if(($role == 'ba1')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_1','1');
$update -> AddDBParam('-script','PR_sign_form_BA_1');
}elseif(($role == 'ba1')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_1','1');
$update -> AddDBParam('sign_status_ba_1_cfo','1');
$update -> AddDBParam('-script','PR_sign_form_BA_1');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba2')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_2','1');
$update -> AddDBParam('-script','PR_sign_form_BA_2');
}elseif(($role == 'ba2')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_2','1');
$update -> AddDBParam('sign_status_ba_2_cfo','1');
$update -> AddDBParam('-script','PR_sign_form_BA_2');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba3')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_3','1');
$update -> AddDBParam('-script','PR_sign_form_BA_3');
}elseif(($role == 'ba3')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_3','1');
$update -> AddDBParam('sign_status_ba_3_cfo','1');
$update -> AddDBParam('-script','PR_sign_form_BA_3');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba4')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_4','1');
$update -> AddDBParam('-script','PR_sign_form_BA_4');
}elseif(($role == 'ba4')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_4','1');
$update -> AddDBParam('sign_status_ba_4_cfo','1');
$update -> AddDBParam('-script','PR_sign_form_BA_4');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba5')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_5','1');
$update -> AddDBParam('-script','PR_sign_form_BA_5');
}elseif(($role == 'ba5')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_5','1');
$update -> AddDBParam('sign_status_ba_5_cfo','1');
$update -> AddDBParam('-script','PR_sign_form_BA_5');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba6')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_6','1');
$update -> AddDBParam('-script','PR_sign_form_BA_6');
}elseif(($role == 'ba6')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_6','1');
$update -> AddDBParam('sign_status_ba_6_cfo','1');
$update -> AddDBParam('-script','PR_sign_form_BA_6');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba7')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_7','1');
$update -> AddDBParam('-script','PR_sign_form_BA_7');
}elseif(($role == 'ba7')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_7','1');
$update -> AddDBParam('sign_status_ba_7_cfo','1');
$update -> AddDBParam('-script','PR_sign_form_BA_7');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba8')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_8','1');
$update -> AddDBParam('-script','PR_sign_form_BA_8');
}elseif(($role == 'ba8')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_8','1');
$update -> AddDBParam('sign_status_ba_8_cfo','1');
$update -> AddDBParam('-script','PR_sign_form_BA_8');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'cpo')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_cpo','1');
$update -> AddDBParam('-script','PR_sign_form_cpo');
}
if(($role == 'ceo')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ceo','1');
$update -> AddDBParam('-script','PR_sign_form_ceo');
}
if(($role == 'cfo')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_cfo','1');
$update -> AddDBParam('-script','PR_sign_form_cfo');
}


$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$recordData = current($updateResult['data']);

if($updateResult['errorCode'] == '0'){

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
if($role == 'cfo'){	
	$newrecord -> AddDBParam('action','APPROVE_PR');
}elseif($role == 'IT'){
	$newrecord -> AddDBParam('action','SIGN_IT');
}elseif($role == 'IRC'){
	$newrecord -> AddDBParam('action','SIGN_IRC');
}elseif($role == 'CEO-PRE'){
	$newrecord -> AddDBParam('action','SIGN_CEO_PREAUTH');
}elseif($role == 'cpo'){
	$newrecord -> AddDBParam('action','SIGN_CPO');
}elseif($role == 'ceo'){
	$newrecord -> AddDBParam('action','SIGN_CEO');
}else{
	$newrecord -> AddDBParam('action','SIGN_BA'.$cfo_proxy);
}
	$newrecord -> AddDBParam('table','PO_main');
	$newrecord -> AddDBParam('object_ID',$recordData['PO_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
	// LOG ACTION IN PO USER LOG
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('Purchase_Req_Order.fp7','PO_user_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
if($role == 'cfo'){	
	$newrecord -> AddDBParam('action','APPROVE_PR');
}elseif($role == 'IT'){
	$newrecord -> AddDBParam('action','SIGN_IT');
}elseif($role == 'IRC'){
	$newrecord -> AddDBParam('action','SIGN_IRC');
}elseif($role == 'CEO-PRE'){
	$newrecord -> AddDBParam('action','SIGN_CEO_PREAUTH');
}elseif($role == 'cpo'){
	$newrecord -> AddDBParam('action','SIGN_CPO');
}elseif($role == 'ceo'){
	$newrecord -> AddDBParam('action','SIGN_CEO');
}else{
	$newrecord -> AddDBParam('action','SIGN_BA'.$cfo_proxy);
}
	$newrecord -> AddDBParam('PO_ID',$recordData['PO_ID'][0]);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	

$_SESSION['purchase_request_signed'] = '1';
//$_SESSION['pba_email'] = $recordData['signer_ID_pba'][0].'@sedl.org';
//$_SESSION['timesheet_owner_email'] = $recordData['signer_ID_owner'][0].'@sedl.org';
//$_SESSION['ceo_email'] = $recordData['signer_ID_ceo'][0].'@sedl.org';
//if($recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0] != ''){
//$_SESSION['lv_appr_cc'] = $recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0].'@sedl.org';
//}else{
//$_SESSION['lv_appr_cc'] = '';
//}

} else {
$_SESSION['purchase_request_signed'] = '2';

}
###############################################
## END: UPDATE THE PURCHASE REQUEST ##
###############################################
}

if($approve == '2') { // IF THE CEO APPROVED THE PR FOR EXECUTIVE COMMITTEE

$current_id = $_GET['appid'];
$signer = $_GET['ba'];
$exc_notes = $_GET['exc_notes'];
//$role = $_GET['role'];

$trigger = rand();
#################################################
## START: UPDATE THE PURCHASE REQUEST ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('Purchase_Req_Order.fp7','PO_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('appr_status_exc','1');
$update -> AddDBParam('appr_status_exc_by',$signer);
$update -> AddDBParam('appr_status_exc_notes',$exc_notes);
$update -> AddDBParam('-script','PR_approve_form_ExC_web');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$recordData = current($updateResult['data']);

if($updateResult['errorCode'] == '0'){

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','APPROVE_PR_FOR_EXC');
	$newrecord -> AddDBParam('table','PO_main');
	$newrecord -> AddDBParam('object_ID',$recordData['PO_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
	// LOG ACTION IN PO USER LOG
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('Purchase_Req_Order.fp7','PO_user_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','APPROVE_ExC');
	$newrecord -> AddDBParam('PO_ID',$recordData['PO_ID'][0]);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	

$_SESSION['pr_approved_for_exc'] = '1';
//$_SESSION['pba_email'] = $recordData['signer_ID_pba'][0].'@sedl.org';
//$_SESSION['timesheet_owner_email'] = $recordData['signer_ID_owner'][0].'@sedl.org';
//$_SESSION['ceo_email'] = $recordData['signer_ID_ceo'][0].'@sedl.org';
//if($recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0] != ''){
//$_SESSION['lv_appr_cc'] = $recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0].'@sedl.org';
//}else{
//$_SESSION['lv_appr_cc'] = '';
//}

} else {
$_SESSION['pr_approved_for_exc'] = '2';

}
###############################################
## END: UPDATE THE PURCHASE REQUEST ##
###############################################
}

if($approve == '3') { // IF THE CEO UPDATED THE EXC NOTES

$current_id = $_GET['appid'];
$signer = $_GET['ba'];
$exc_notes = $_GET['exc_notes'];
//$role = $_GET['role'];

$trigger = rand();
#################################################
## START: UPDATE THE PURCHASE REQUEST ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('Purchase_Req_Order.fp7','PO_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('appr_status_exc_notes',$exc_notes);
//$update -> AddDBParam('-script','PR_approve_form_ExC_web');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$recordData = current($updateResult['data']);

if($updateResult['errorCode'] == '0'){

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','CEO_UPDATED_EXC_NOTES');
	$newrecord -> AddDBParam('table','PO_main');
	$newrecord -> AddDBParam('object_ID',$recordData['PO_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
	

//$_SESSION['pr_approved_for_exc'] = '1';
//$_SESSION['pba_email'] = $recordData['signer_ID_pba'][0].'@sedl.org';
//$_SESSION['timesheet_owner_email'] = $recordData['signer_ID_owner'][0].'@sedl.org';
//$_SESSION['ceo_email'] = $recordData['signer_ID_ceo'][0].'@sedl.org';
//if($recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0] != ''){
//$_SESSION['lv_appr_cc'] = $recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0].'@sedl.org';
//}else{
//$_SESSION['lv_appr_cc'] = '';
//}

}

}

if($_GET['attid'] != '') { // IF THE CFO APPROVED AN ATTACHED CONTRACT

$current_id = $_GET['attid'];

//$trigger = rand();
#################################################
## START: UPDATE THE ATTACHMENT RECORD ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('Purchase_Req_Order.fp7','PO_attachments');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('appr_status_contract_cfo','1');
$update -> AddDBParam('-script','PR_approve_form_CFO_contract');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$recordData = current($updateResult['data']);


if($updateResult['errorCode'] == '0'){


	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','CFO_APPROVE_PR_CONTRACT');
	$newrecord -> AddDBParam('table','PO_main');
	$newrecord -> AddDBParam('object_ID',$recordData['PO_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

	// LOG ACTION IN PO USER LOG
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('Purchase_Req_Order.fp7','PO_user_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','CFO_APPR_CONTR');
	$newrecord -> AddDBParam('PO_ID',$recordData['PO_ID'][0]);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


$_SESSION['attachment_approved'] = '1';

} else {
$_SESSION['attachment_approved'] = '2';

}
###############################################
## END: UPDATE THE ATTACHMENT RECORD ##
###############################################
}

#################################################################
## START: FIND THIS PR ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('Purchase_Req_Order.fp7','PO_table');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('PO_ID','=='.$PO_ID);
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam('leave_hrs_date','ascend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
$row_ID = $recordData['c_row_ID'][0];


if((($recordData['PR_pre_approval_required_IT'][0] == '1')&&($recordData['sign_status_IT'][0] != '1'))||(($recordData['PR_pre_approval_required_IRC'][0] == '1')&&($recordData['sign_status_IRC'][0] != '1'))||(($recordData['PR_pre_approval_required_CEO'][0] == '1')&&($recordData['sign_status_ceo_preauth'][0] != '1'))){
$preappRequired = '1';
}else{
$preappRequired = '0';
}
###############################################################
## END: FIND THIS PR ##
###############################################################

#################################################################
## START: FIND LINE-ITEMS RELATED TO THIS PR ##
#################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('Purchase_Req_Order.fp7','PO_line_items');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('PO_ID','=='.$PO_ID);
$search2 -> AddDBParam('item_type','0');
//$search2 -> AddDBParam('-lop','or');

//$search2 -> AddSortParam('leave_hrs_date','ascend');


$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
//print_r ($searchResult2);
$recordData2 = current($searchResult2['data']);
###############################################################
## END: FIND LEAVE REQUEST HRS RELATED TO THIS PR ##
###############################################################

if($recordData['c_attachment_count'][0] > 0){ // THE SELECTED PR HAS ATTACHMENTS
#################################################################
## START: FIND ATTACHMENTS RELATED TO THIS PR ##
#################################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('Purchase_Req_Order.fp7','PO_attachments');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('PO_ID','=='.$PO_ID);
//$search3 -> AddDBParam('-lop','or');

//$search3 -> AddSortParam('leave_hrs_date','ascend');


$searchResult3 = $search3 -> FMFind();

//echo '<p>$searchResult3[errorCode]: '.$searchResult3['errorCode'];
//echo '<p>$searchResult3[foundCount]: '.$searchResult3['foundCount'];
//print_r ($searchResult3);
$recordData3 = current($searchResult3['data']);
###############################################################
## END: FIND ATTACHMENTS RELATED TO THIS PR ##
###############################################################
}

if($recordData['c_user_log_count'][0] > 0){ // THE SELECTED PR HAS USER LOG ENTRIES
#################################################################
## START: FIND USER LOG ENTRIES RELATED TO THIS PR ##
#################################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('Purchase_Req_Order.fp7','PO_user_log');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('PO_ID','=='.$PO_ID);
//$search4 -> AddDBParam('-lop','or');

$search4 -> AddSortParam('creation_timestamp','ascend');


$searchResult4 = $search4 -> FMFind();

//echo '<p>$searchResult4[errorCode]: '.$searchResult4['errorCode'];
//echo '<p>$searchResult4[foundCount]: '.$searchResult4['foundCount'];
//print_r ($searchResult4);
$recordData4 = current($searchResult4['data']);
###############################################################
## END: FIND USER LOG ENTRIES RELATED TO THIS PR ##
###############################################################
}


$user = $_SESSION['user_ID'];
echo '<span style="color:#999999">User: '.$user.'</span>';
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Approve Purchase Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">


function confirmContract() { 
	var answer2 = confirm ("CFO: Approve this contract?")
	if (!answer2) {
	return false;
	}
}

function confirmExC() { 
	var answer2 = confirm ("CEO: Approve this PR for Executive Committee?")
	if (!answer2) {
	return false;
	}
}

function ExCOnly() { 
	var answer2 = confirm ("This section reserved for the CEO.")
	return false;
}

function confirmSign() { 
	var answer2 = confirm ("Sign this purchase requisition now?")
	if (!answer2) {
	return false;
	}
}

function wrongSigner() { 
	var answer2 = confirm ("This space reserved for another signer.")
	return false;
}

function preapprovalRequired() { 
	var answer2 = confirm ("Pre-approval signatures are required before budget authority approval.")
	return false;
}

function BARequired() { 
	var answer2 = confirm ("All budget authority signatures are required before EO/Fiscal approval.")
	return false;
}

function ACCTRequired() { 
	var answer2 = confirm ("CPO: Accounting Supervisor must approve this PR before CPO approval. You will be notified by SIMS when your signature (as CPO) is required.")
	return false;
}

function ACCTRequired2() { 
	var answer2 = confirm ("CEO: Accounting Supervisor must approve this PR before CEO approval. You will be notified by SIMS when your signature (as CEO) is required.")
	return false;
}

function CPORequired() { 
	var answer2 = confirm ("CEO: Chief Program Officer (CPO) must sign this PR before CEO approval. You will be notified by SIMS when your signature (as CEO) is required.")
	return false;
}

function CPORequired2() { 
	var answer2 = confirm ("CFO: Chief Program Officer (CPO) must sign this PR before CFO approval. You will be notified by SIMS when your signature (as CFO) is required.")
	return false;
}

function CEORequired() { 
	var answer2 = confirm ("CFO: Chief Executive Officer (CEO) must sign this PR before CFO approval. You will be notified by SIMS when your signature (as CFO) is required.")
	return false;
}

function CFOContract() { 
	var answer2 = confirm ("CFO: Unapproved contracts are attached to this PR. Please approve all contracts before approving this PR.")
	return false;
}


function rejectPrompt()
{
//var x;

var name=prompt("Enter a reason for rejecting this PR:","");

//	document.location = 'pur_order_ba.php?action=reject&reason='+name;	
if(name!=null && name!=""){
  document.location = 'pur_order_ba.php?action=reject_pr&ba=<?php echo $user;?>&reason='+name+'&id=<?php echo $row_ID;?>';	
  //x="Hello " + name + "! How are you today?";
  //document.getElementById("demo").innerHTML=x;
	}
}

function toggle1() {
	var ele = document.getElementById("toggleText");
	var text = document.getElementById("displayText");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Approve";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "CANCEL";
	}
} 

function toggle2() {
	var ele = document.getElementById("toggleText2");
	var text = document.getElementById("displayText2");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Edit";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "CANCEL";
	}
} 


</script>
</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="900px">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Purchase Requests: Budget Authority Admin</h1><hr /></td></tr>
			
			<?php if($recordData['status'][0] == 'Approved'){ ?>
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Budget Authority: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - This PR has been approved. <img src="/staff/sims/images/green_check.png"> | <a href="/staff/sims/menu_po_ba.php">Close PR</a></p>
			</td></tr>

			<?php }elseif($_SESSION['purchase_request_signed'] != '1'){ ?>
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Budget Authority: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - To approve this PR, click the appropriate signature box below. | <input type=button value="Reject PR" onClick="rejectPrompt()"> | <a href="/staff/sims/menu_po_ba.php">Close PR</a></p>
			</td></tr>
			
			<?php } elseif($_SESSION['purchase_request_signed'] == '1'){ ?>
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Budget Authority: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - You have successfully approved this PR. <img src="/staff/sims/images/green_check.png"> | <a href="/staff/sims/menu_po_ba.php">Close PR</a></p>
			</td></tr>			

			
			<?php } ?>
			
			
			<tr><td colspan="2">
			
						<table cellpadding="10" cellspacing="0" border="0" bordercolor="ebebeb" bgcolor="ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong>Requested by: <?php echo $recordData['staff::c_full_name_first_last'][0];?> (<?php echo $recordData['SEDL_unit'][0];?>)</strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">PR Status: <strong><?php if($recordData['status'][0] == 'Pending'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['status'][0]).'</span>';?></strong><?php if($recordData['status'][0] == 'Approved'){?> - <strong>PO Number: <?php echo $recordData['PURCHASE_ORDER_NO'][0].'</strong>'; }?></span></td></tr>
						<tr><td class="body" nowrap><strong>PURCHASE REQUISITION</strong></td><td align="right">ID: <?php echo $PO_ID;?></td></tr>

						<tr><td colspan="2">
						

							<table cellpadding="7" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
							
								<tr><td class="body" style="vertical-align:text-top;width:100%"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>DESCRIPTION/PURPOSE</strong></div><br><?php echo $recordData['PR_description_general'][0];?></td>
								<td rowspan="3" style="vertical-align:text-top" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>APPROVAL LOG</strong></div>



								
									<table cellspacing="2" style="margin-top:6px">

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf"><strong>DATE</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>USER</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>ACTION</strong></td></tr>

									<?php if($searchResult4['foundCount'] > 0){ // SHOW PR STATUS LOG ?>
									<?php foreach($searchResult4['data'] as $key => $searchData4) { if($searchData4['comment'][0] != ''){$rowcolor = '#ffd6d6';}elseif(($searchData4['action'][0] == 'APPROVE_PR')||($searchData4['action'][0] == 'APPROVE_SPR')){$rowcolor = '#ace29f';}else{$rowcolor = '#ebebeb';} ?>

										<tr><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['creation_timestamp'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['user'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['action'][0];?></td></tr>
										<?php if($searchData4['comment'][0] != ''){?><tr><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" colspan="3"><?php echo $searchData4['comment'][0];?></td></tr><?php }?>

									<?php } ?>
									<?php } ?>
									</table>
								</td>
								</tr>

								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>NOTES/INSTRUCTIONS</strong></div><br><?php if($recordData['notes'][0] != ''){echo $recordData['notes'][0];}else{echo 'N/A';}?></td></tr>

								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>ATTACHMENTS (<?php echo $searchResult3['foundCount'];?>)</strong></div><br>
								
								<?php if($searchResult3['foundCount'] > 0){ ?>
									<ol style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px;width:95%;list-style-position: inside;">
									
									<?php foreach($searchResult3['data'] as $key => $searchData3) { // LIST ATTACHMENTS ?>
										
										<li style="padding:5px">
										
										<strong><?php echo ucwords($searchData3['attachment_description'][0]);?></strong><br>
										Type: <?php echo $searchData3['attachment_type'][0];?><br>
										Uploaded: <?php echo $searchData3['uploaded_timestamp'][0];?> by <?php echo $searchData3['uploaded_by'][0];?><br>
										File: <a href="http://198.214.141.190/sims/attachments/<?php echo urlencode($searchData3['attachment_filename'][0]);?>" target="_blank" title="Click to download this attachment for review."><?php echo $searchData3['attachment_filename'][0];?></a><br>

										<?php if(($searchData3['attachment_type'][0] == 'Contract (hotel)')||($searchData3['attachment_type'][0] == 'Contract (other)')||($searchData3['attachment_type'][0] == 'Subcontract Agreement Notice')){ ?>
										
											<div class="tiny" style="background-color:#ffffff;border:1px dotted #000000;padding:4px;margin:4px"> 
											
											<?php if($searchData3['appr_status_contract_cfo'][0] == '1'){?>
											
												<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CFO CONTRACT APPROVAL | <span style="color:#0033ff"><strong>APPROVED</strong></span> | <?php echo $searchData3['appr_timestamp_contract_cfo'][0];?>
												</div>
										
											<?php }else{?>
										
												<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CFO CONTRACT APPROVAL | <span style="color:#ff0000"><strong>PENDING</strong></span>  | <a href="pur_order_ba.php?action=view&id=<?php echo $PO_ID;?>&attid=<?php echo $searchData3['c_row_ID'][0];?>" title="CFO: Click to approve this contract." <?php if($_SESSION['user_ID'] !== 'sferguso'){?>onclick="return wrongSigner()"<?php }else{?>onclick="return confirmContract()"<?php }?>>Approve</a></div>
												
												</div>
										
											<?php }?>
										

										<?php }?>
									
										</li>

										<hr style="border:1px dotted #000000">
									<?php  } ?></ol>
	
								<?php }else{ ?>
								
								N/A
								<?php } ?>

								</td></tr>
								
								
								<tr><td class="body" colspan="2"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>LINE ITEM DETAILS (<?php echo $searchResult2[foundCount];?>)</strong></div><br>
								
										<table cellpadding="7" cellspacing="0" border="1" bordercolor="#cccccc" width="100%" class="sims">
										<tr bgcolor="#ebebeb"><td class="body" nowrap>Item Description</td><td class="body">Quantity</td><td class="body">Unit</td><td class="body" align="right" nowrap>Unit Price</td><td class="body" align="right">Total</td></tr>
		
										<?php foreach($searchResult2['data'] as $key => $searchData) { ?>
										
		
												<tr class="body"><td style="vertical-align:top"><?php echo $searchData['item_description'][0];?><br><div class="tiny" style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:3px;font-size:10px"><?php echo $searchData['c_budget_code_list_ext'][0];?></div></td><td nowrap style="vertical-align:top"><?php echo $searchData['quantity'][0];?></td><td nowrap style="vertical-align:top"><?php echo $searchData['UNIT'][0];?></td><td nowrap align="right" style="vertical-align:top">$<?php echo number_format($searchData['unit_price'][0],2,'.',',');?></td><td nowrap align="right" style="vertical-align:top">$<?php echo number_format($searchData['c_TOTAL'][0],2,'.',',');?></td></tr>
											
									
										<?php } ?>
		
											<tr class="body"><td bgcolor="#ebebeb" colspan="4" nowrap align="right"><em>PR Total:</em></td><td align="right"><strong>$<?php echo number_format($recordData['c_PO_total_pr'][0],2,'.',','); ?></strong></td></tr>
		
		
		
													
		
		
									</table>
								
								
								</td></tr>
								

<?php if($recordData['bid1'][0] != ''){ // DON'T SHOW BIDS IF THERE ARE NONE ?>
								<tr><td colspan="2" class="body" style="width:50%;vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>BIDS</strong></div><br>
								
									<table style="border:0px dotted #000000;margin-top:6px;width:100%">
									<tr><td style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px;width:100%">Vendor</td><td style="text-align:right;border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px" nowrap>Bid Amount</td></tr>
									<tr><td style="padding:5px"><?php if($recordData['bid1'][0] == ''){echo 'N/A';}else{echo $recordData['bid1'][0];}?></td><td style="text-align:right;padding:5px">&nbsp;<?php if($recordData['bid_1_total'][0] != ''){echo '$'.number_format($recordData['bid_1_total'][0],2,'.',',');}?></td></tr>
									<tr><td style="padding:5px"><?php if($recordData['bid2'][0] == ''){echo 'N/A';}else{echo $recordData['bid2'][0];}?></td><td style="text-align:right;padding:5px">&nbsp;<?php if($recordData['bid_2_total'][0] != ''){echo '$'.number_format($recordData['bid_2_total'][0],2,'.',',');}?></td></tr>
									</table>
								
								</td></tr>
<?php } ?>								
								<tr><td colspan="2" class="body" style="width:50%;vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>VENDOR</strong></div><br>
								
									<table style="border:0px dotted #000000;margin-top:6px;width:100%">
									<tr><td style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px;width:100%">Vendor</td><td style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px;width:100%">Attention</td></tr>
									<tr><td style="padding:5px;vertical-align:text-top"><?php echo $recordData['vendor_name'][0];?><br><?php echo $recordData['vendor_addr1'][0];?><br><?php echo $recordData['vendor_city'][0];?>, <?php echo $recordData['vendor_state'][0];?> <?php echo $recordData['vendor_zip'][0];?></td><td style="padding:5px;vertical-align:text-top" nowrap><?php echo $recordData['vendor_attn_to'][0];?><br><?php echo $recordData['vendor_phone'][0];?></td></tr>
									</table>
								
								</td>
								
								</tr>

								
													<tr class="body"><td colspan="2" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>SIGNATURES</strong></div><br>
													
<?php if($recordData['PR_submitted_timestamp'][0] != ''){ ?>

													
														<table class="sims" cellspacing="1" cellpadding="10" border="1" width="100%">
		
		<?php if(($recordData['c_sign_button_IT'][0] != 'N/A')||($recordData['c_sign_button_IRC'][0] != 'N/A')||($recordData['c_sign_button_ceo_preauth'][0] != 'N/A')){ // IT, IRC, OR CEO PRE-APPROVAL REQUIRED  ?>
		
														<tr class="body" valign="top"><td><div style="border:1px dotted #000000;background-color:#bddbee;padding:5px" class="tiny">PRE-APPROVAL</div><br>
														
															<table>
															<tr><td style="border-width:0px;padding:0px;margin:0px">
															
															<?php if($recordData['c_sign_button_IT'][0] != 'N/A'){ // IT PRE-APPROVAL REQUIRED  ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_IT'][0] == '1'){ // IT APPROVED ?><img src="/staff/sims/signatures/cpierron.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=cpierron&appid=<?php echo $row_ID;?>&role=IT" <?php if($_SESSION['user_ID'] != 'cpierron'){echo 'onclick="return wrongSigner()"';}else{echo 'onclick="return confirmSign()"';}?>>cpierron</a><?php } ?><p>
																<span class="tiny"><em>IT: Network Administrator</em><br><?php if($recordData['sign_timestamp_IT'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_IT'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_IRC'][0] != 'N/A'){ // IRC PRE-APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_IRC'][0] == '1'){ // IRC APPROVED ?><img src="/staff/sims/signatures/nreynold.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=nreynold&appid=<?php echo $row_ID;?>&role=IRC" <?php if($_SESSION['user_ID'] != 'nreynold'){echo 'onclick="return wrongSigner()"';}else{echo 'onclick="return confirmSign()"';}?>>nreynold</a><?php } ?><p>
																<span class="tiny"><em>IRC: Information Associate</em><br><?php if($recordData['sign_timestamp_IRC'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_IRC'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>

															<?php if($recordData['c_sign_button_ceo_preauth'][0] != 'N/A'){ // CEO PRE-APPROVAL REQUIRED  ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ceo_preauth'][0] == '1'){ // CEO APPROVED ?><img src="/staff/sims/signatures/whoover.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=whoover&appid=<?php echo $row_ID;?>&role=CEO-PRE" <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return wrongSigner()"';}else{echo 'onclick="return confirmSign()"';}?>>whoover</a><?php } ?><p>
																<span class="tiny"><em>CEO: Pre-approval</em><br><?php if($recordData['sign_timestamp_ceo_preauth'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ceo_preauth'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>

															</tr>
															</table>
		
														</td></tr>										
		
		<?php } ?>
		
														<tr class="body" valign="top"><td><div style="border:1px dotted #000000;background-color:#bddbee;padding:5px" class="tiny">BA APPROVAL</div><br>
														
															<table>
															<tr><td style="border-width:0px;padding:0px;margin:0px">
		
															<?php if($recordData['c_sign_button_ba_1'][0] != 'N/A'){ // BA1 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_1'][0] == '1'){ // BA1 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_1'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_1'][0];?>&appid=<?php echo $row_ID;?>&role=ba1" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_1'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_1'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_1'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_1'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_2'][0] != 'N/A'){ // BA2 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_2'][0] == '1'){ // BA2 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_2'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_2'][0];?>&appid=<?php echo $row_ID;?>&role=ba2" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_2'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_2'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_2'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_2'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_3'][0] != 'N/A'){ // BA3 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_3'][0] == '1'){ // BA3 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_3'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_3'][0];?>&appid=<?php echo $row_ID;?>&role=ba3" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_3'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_3'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_3'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_3'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_4'][0] != 'N/A'){ // BA4 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_4'][0] == '1'){ // BA4 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_4'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_4'][0];?>&appid=<?php echo $row_ID;?>&role=ba4" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_4'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_4'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_4'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_4'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_5'][0] != 'N/A'){ // BA5 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_5'][0] == '1'){ // BA5 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_5'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_5'][0];?>&appid=<?php echo $row_ID;?>&role=ba5" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_5'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_5'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_5'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_5'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_6'][0] != 'N/A'){ // BA6 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_6'][0] == '1'){ // BA6 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_6'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_6'][0];?>&appid=<?php echo $row_ID;?>&role=ba6" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_6'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_6'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_6'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_6'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_7'][0] != 'N/A'){ // BA7 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_7'][0] == '1'){ // BA7 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_7'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_7'][0];?>&appid=<?php echo $row_ID;?>&role=ba7" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_7'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_7'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_7'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_7'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_8'][0] != 'N/A'){ // BA8 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_8'][0] == '1'){ // BA8 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_8'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_8'][0];?>&appid=<?php echo $row_ID;?>&role=ba8" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_8'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_8'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_8'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_8'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
															</tr>
															</table>
		
														</td></tr>										
		
														<tr class="body" valign="top"><td style="width:100%"><div style="border:1px dotted #000000;background-color:#bddbee;padding:5px" class="tiny">EO/FISCAL APPROVAL</div><br>


														<?php if($recordData['c_PO_total_pr'][0] > 100000){ // EXECUTIVE COMMITTEE APPROVAL IS REQUIRED - PO IS OVER $100K ?>			

															<div class="tiny" style="background-color:#ffffff;border:1px dotted #000000;padding:5px;width:50%;margin:4px"> 
															
															<?php if($recordData['appr_status_exc'][0] == '1'){?>
															
																<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> Executive Committee (over $100K) | <span style="color:#0033ff"><strong>APPROVED</strong></span> | <?php echo $recordData['appr_timestamp_exc'][0];?>
																	<div class="tiny" style="background-color:#fff6bf;border:1px solid #999999;padding:5px;margin:10px"><strong>CEO Notes</strong> (includes PR and SPR notes)<div style="float:right"><a href="javascript:toggle2();" id="displayText2" title="CEO: Click to edit CEO comments." <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return ExCOnly()"';}?>>Edit</a></div><br>
																	<?php echo $recordData['appr_status_exc_notes'][0];?>

																<div id="toggleText2" style="display: none; padding:10px 0px 0px 10px">
																<form method="get">
																<input type="hidden" name="action" value="view">
																<input type="hidden" name="approve" value="3">
																<input type="hidden" name="appid" value="<?php echo $row_ID;?>">
																<input type="hidden" name="ba" value="<?php echo $_SESSION['user_ID'];?>">
																<input type="hidden" name="id" value="<?php echo $PO_ID;?>">
																<input type="text" name="exc_notes" value="<?php echo $recordData['appr_status_exc_notes'][0];?>" size="55">
																<input type="submit" name="submit" value="Save">
																</form>
																</div>

																</div>
																</div>
														
															<?php }else{?>
														
																<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> Executive Committee (over $100K) | <span style="color:#ff0000"><strong>PENDING</strong></span>  | <!--<a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=2&appid=<?php echo $row_ID;?>&ba=<?php echo $_SESSION['user_ID'];?>" title="CEO: Click to approve this PR for Executive Committee." <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return ExCOnly()"';}else{echo 'onclick="return confirmExC()"';}?>>Approve</a>-->

																<span class="tiny"><a href="javascript:toggle1();" id="displayText" title="CEO: Click to approve this PR for Executive Committee." <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return ExCOnly()"';}?>>Approve</a></span>
																<div id="toggleText" style="display: none; padding:10px 0px 0px 10px"><strong>CEO Notes</strong> (includes PR and SPR notes)
																<form method="get">
																<input type="hidden" name="action" value="view">
																<input type="hidden" name="approve" value="2">
																<input type="hidden" name="appid" value="<?php echo $row_ID;?>">
																<input type="hidden" name="ba" value="<?php echo $_SESSION['user_ID'];?>">
																<input type="hidden" name="id" value="<?php echo $PO_ID;?>">
																<input type="text" name="exc_notes" value="<?php echo $recordData['appr_status_exc_notes'][0];?>" size="55">
																<input type="submit" name="submit" value="Approve">
																</form>
																</div>
																
																</div>
																
																</div>
														
															<?php }?>


														<?php } ?>
														
														<div class="tiny" style="background-color:#ffffff;border:1px dotted #000000;padding:5px;width:50%;margin:4px"><?php if($recordData['appr_status_acct'][0] == '1'){?><img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> Accounting Supervisor | <span style="color:#0033ff"><strong>APPROVED</strong></span> | <?php echo $recordData['appr_timestamp_acct'][0];?><?php }else{?><img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> Accounting Supervisor | <span style="color:#ff0000"><strong>PENDING</strong></span><?php }?></div>
														
															<table style="margin-top:10px">
															<tr><td style="border-width:0px;padding:0px;margin:0px">
		
			
															<?php if($recordData['c_sign_button_cpo'][0] != 'N/A'){ // CPO APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_cpo'][0] == '1'){ // CPO APPROVED ?><img src="/staff/sims/signatures/vdimock.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=vdimock&appid=<?php echo $row_ID;?>&role=cpo" <?php if($_SESSION['user_ID'] != 'vdimock'){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}elseif($recordData['c_ba_signer_status'][0] == '0'){echo 'onclick="return BARequired()"';}elseif($recordData['appr_status_acct'][0] != '1'){echo 'onclick="return ACCTRequired()"';}else{echo 'onclick="return confirmSign()"';}?>>vdimock</a><?php }?><p>
																<span class="tiny"><em>Chief Program Officer</em><br><?php if($recordData['sign_timestamp_cpo'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_cpo'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ceo'][0] != 'N/A'){ // CEO APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ceo'][0] == '1'){ // CEO APPROVED ?><img src="/staff/sims/signatures/whoover.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=whoover&appid=<?php echo $row_ID;?>&role=ceo" <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}elseif($recordData['c_ba_signer_status'][0] == '0'){echo 'onclick="return BARequired()"';}elseif(($recordData['c_sign_button_cpo'][0] != 'N/A')&&($recordData['sign_status_cpo'][0] != '1')){echo 'onclick="return CPORequired()"';}elseif($recordData['appr_status_acct'][0] != '1'){echo 'onclick="return ACCTRequired2()"';}else{echo 'onclick="return confirmSign()"';}?>>whoover</a><?php }?><p>
																<span class="tiny"><em>Chief Executive Officer</em><br><?php if($recordData['sign_timestamp_ceo'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ceo'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_cfo'][0] != 'N/A'){ // CFO APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_cfo'][0] == '1'){ // CFO APPROVED ?><img src="/staff/sims/signatures/sferguso.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&approve=1&ba=sferguso&appid=<?php echo $row_ID;?>&role=cfo" <?php if($_SESSION['user_ID'] != 'sferguso'){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}elseif($recordData['c_ba_signer_status'][0] == '0'){echo 'onclick="return BARequired()"';}elseif(($recordData['c_sign_button_cpo'][0] != 'N/A')&&($recordData['sign_status_cpo'][0] != '1')){echo 'onclick="return CPORequired2()"';}elseif(($recordData['c_sign_button_ceo'][0] != 'N/A')&&($recordData['sign_status_ceo'][0] != '1')){echo 'onclick="return CEORequired()"';}elseif($recordData['c_appr_status_contract_cfo'][0] == '0'){echo 'onclick="return CFOContract()"';}else{echo 'onclick="return confirmSign()"';}?>>sferguso</a><?php }?><p>
																<span class="tiny"><em>Chief Financial Officer</em><br><?php if($recordData['sign_timestamp_cfo'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_cfo'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
															</tr>
															</table>
		
														</td></tr>										
		
														</table>
		
													</td>
													
													</tr>
													
<?php }else{ ?>								

													<tr><td class="body" style="vertical-align:text-top" colspan="2">This PR was processed manually or has not been submitted.</td></tr>

<?php } ?>			

								
								
							</table>


						</td></tr>

						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>

<?php 
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################

}elseif($action == 'view_sup'){ //IF THE USER IS VIEWING THIS SUPPLEMENTAL PURCHASE REQUEST


if($approve == '1') { // IF THE SIGNER APPROVED THE SPR

$current_id = $_GET['appid'];
$signer = $_GET['ba'];
$role = $_GET['role'];

$trigger = rand();
#################################################
## START: UPDATE THE PURCHASE REQUEST ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('Purchase_Req_Order.fp7','PO_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

if(($role == 'IT')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_IT_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_IT');
}
if(($role == 'IRC')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_IRC_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_IRC');
}

if(($role == 'ba1')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_1_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_1');
}elseif(($role == 'ba1')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_1_sup','1');
$update -> AddDBParam('sign_status_ba_1_sup_cfo','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_1');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba2')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_2_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_2');
}elseif(($role == 'ba2')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_2_sup','1');
$update -> AddDBParam('sign_status_ba_2_sup_cfo','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_2');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba3')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_3_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_3');
}elseif(($role == 'ba3')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_3_sup','1');
$update -> AddDBParam('sign_status_ba_3_sup_cfo','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_3');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba4')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_4_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_4');
}elseif(($role == 'ba4')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_4_sup','1');
$update -> AddDBParam('sign_status_ba_4_sup_cfo','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_4');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba5')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_5_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_5');
}elseif(($role == 'ba5')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_5_sup','1');
$update -> AddDBParam('sign_status_ba_5_sup_cfo','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_5');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba6')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_6_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_6');
}elseif(($role == 'ba6')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_6_sup','1');
$update -> AddDBParam('sign_status_ba_6_sup_cfo','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_6');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba7')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_7_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_7');
}elseif(($role == 'ba7')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_7_sup','1');
$update -> AddDBParam('sign_status_ba_7_sup_cfo','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_7');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'ba8')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ba_8_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_8');
}elseif(($role == 'ba8')&&($_SESSION['user_ID'] == 'sferguso')){
$update -> AddDBParam('sign_status_ba_8_sup','1');
$update -> AddDBParam('sign_status_ba_8_sup_cfo','1');
$update -> AddDBParam('-script','SPR_sign_form_BA_8');
$cfo_proxy = ' (by_CFO)';
}

if(($role == 'cpo')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_cpo_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_cpo');
}
if(($role == 'ceo')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_ceo_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_ceo');
}
if(($role == 'cfo')&&($signer == $_SESSION['user_ID'])){
$update -> AddDBParam('sign_status_cfo_sup','1');
$update -> AddDBParam('-script','SPR_sign_form_cfo');
}


$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$recordData = current($updateResult['data']);

if($updateResult['errorCode'] == '0'){

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
if($role == 'cfo'){	
	$newrecord -> AddDBParam('action','APPROVE_SPR');
}elseif($role == 'IT'){
	$newrecord -> AddDBParam('action','SIGN_IT');
}elseif($role == 'IRC'){
	$newrecord -> AddDBParam('action','SIGN_IRC');
}elseif($role == 'cpo'){
	$newrecord -> AddDBParam('action','SIGN_CPO');
}elseif($role == 'ceo'){
	$newrecord -> AddDBParam('action','SIGN_CEO');
}else{
	$newrecord -> AddDBParam('action','SIGN_BA'.$cfo_proxy);
}
	$newrecord -> AddDBParam('table','PO_main');
	$newrecord -> AddDBParam('object_ID',$recordData['PO_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
	// LOG ACTION IN PO USER LOG
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('Purchase_Req_Order.fp7','PO_user_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
if($role == 'cfo'){	
	$newrecord -> AddDBParam('action','APPROVE_SPR');
}elseif($role == 'IT'){
	$newrecord -> AddDBParam('action','SIGN_IT');
}elseif($role == 'IRC'){
	$newrecord -> AddDBParam('action','SIGN_IRC');
}elseif($role == 'cpo'){
	$newrecord -> AddDBParam('action','SIGN_CPO');
}elseif($role == 'ceo'){
	$newrecord -> AddDBParam('action','SIGN_CEO');
}else{
	$newrecord -> AddDBParam('action','SIGN_BA'.$cfo_proxy);
}
	$newrecord -> AddDBParam('PO_ID',$recordData['PO_ID'][0]);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
	


$_SESSION['purchase_request_signed_sup'] = '1';
//$_SESSION['pba_email'] = $recordData['signer_ID_pba'][0].'@sedl.org';
//$_SESSION['timesheet_owner_email'] = $recordData['signer_ID_owner'][0].'@sedl.org';
//$_SESSION['ceo_email'] = $recordData['signer_ID_ceo'][0].'@sedl.org';
//if($recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0] != ''){
//$_SESSION['lv_appr_cc'] = $recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0].'@sedl.org';
//}else{
//$_SESSION['lv_appr_cc'] = '';
//}

} else {
$_SESSION['purchase_request_signed_sup'] = '2';

}
###############################################
## END: UPDATE THE PURCHASE REQUEST ##
###############################################
}

if($approve == '2') { // IF THE CEO APPROVED THE SPR FOR EXECUTIVE COMMITTEE

$current_id = $_GET['appid'];
$signer = $_GET['ba'];
//$role = $_GET['role'];

$trigger = rand();
#################################################
## START: UPDATE THE PURCHASE REQUEST ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('Purchase_Req_Order.fp7','PO_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('appr_status_exc_sup','1');
$update -> AddDBParam('appr_status_exc_sup_by',$signer);
$update -> AddDBParam('-script','SPR_submit_for_ACCT_approval');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$recordData = current($updateResult['data']);

if($updateResult['errorCode'] == '0'){

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','APPROVE_SPR_FOR_EXC');
	$newrecord -> AddDBParam('table','PO_main');
	$newrecord -> AddDBParam('object_ID',$recordData['PO_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
	// LOG ACTION IN PO USER LOG
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('Purchase_Req_Order.fp7','PO_user_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','APPROVE_ExC_SUP');
	$newrecord -> AddDBParam('PO_ID',$recordData['PO_ID'][0]);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	

$_SESSION['spr_approved_for_exc'] = '1';
//$_SESSION['pba_email'] = $recordData['signer_ID_pba'][0].'@sedl.org';
//$_SESSION['timesheet_owner_email'] = $recordData['signer_ID_owner'][0].'@sedl.org';
//$_SESSION['ceo_email'] = $recordData['signer_ID_ceo'][0].'@sedl.org';
//if($recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0] != ''){
//$_SESSION['lv_appr_cc'] = $recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0].'@sedl.org';
//}else{
//$_SESSION['lv_appr_cc'] = '';
//}

} else {
$_SESSION['spr_approved_for_exc'] = '2';

}
###############################################
## END: UPDATE THE PURCHASE REQUEST ##
###############################################
}

if($approve == '3') { // IF THE CEO UPDATED THE EXC NOTES

$current_id = $_GET['appid'];
$signer = $_GET['ba'];
$exc_notes = $_GET['exc_notes'];
//$role = $_GET['role'];

$trigger = rand();
#################################################
## START: UPDATE THE PURCHASE REQUEST ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('Purchase_Req_Order.fp7','PO_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('appr_status_exc_notes',$exc_notes);
//$update -> AddDBParam('-script','PR_approve_form_ExC_web');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$recordData = current($updateResult['data']);

if($updateResult['errorCode'] == '0'){

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','CEO_UPDATED_EXC_NOTES_SUP');
	$newrecord -> AddDBParam('table','PO_main');
	$newrecord -> AddDBParam('object_ID',$recordData['PO_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
	

//$_SESSION['pr_approved_for_exc'] = '1';
//$_SESSION['pba_email'] = $recordData['signer_ID_pba'][0].'@sedl.org';
//$_SESSION['timesheet_owner_email'] = $recordData['signer_ID_owner'][0].'@sedl.org';
//$_SESSION['ceo_email'] = $recordData['signer_ID_ceo'][0].'@sedl.org';
//if($recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0] != ''){
//$_SESSION['lv_appr_cc'] = $recordData['leave_requests_staff_byStaffID::lv_appr_cc'][0].'@sedl.org';
//}else{
//$_SESSION['lv_appr_cc'] = '';
//}

}

}

if($_GET['attid'] != '') { // IF THE CFO APPROVED AN ATTACHED CONTRACT

$current_id = $_GET['attid'];

//$trigger = rand();
#################################################
## START: UPDATE THE ATTACHMENT RECORD ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('Purchase_Req_Order.fp7','PO_attachments');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('appr_status_contract_cfo','1');
$update -> AddDBParam('-script','PR_approve_form_CFO_contract_sup');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
//$recordData = current($updateResult['data']);


if($updateResult['errorCode'] == '0'){

/*
	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','SIGN_PR');
	$newrecord -> AddDBParam('table','PO_main');
	$newrecord -> AddDBParam('object_ID',$recordData['PO_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
*/
$_SESSION['attachment_approved_sup'] = '1';

} else {
$_SESSION['attachment_approved_sup'] = '2';

}
###############################################
## END: UPDATE THE ATTACHMENT RECORD ##
###############################################
}

#################################################################
## START: FIND THIS PR ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('Purchase_Req_Order.fp7','PO_table');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('PO_ID','=='.$PO_ID);
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam('leave_hrs_date','ascend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
$row_ID = $recordData['c_row_ID'][0];
$preappRequired = '0';
###############################################################
## END: FIND THIS PR ##
###############################################################

#################################################################
## START: FIND LINE-ITEMS RELATED TO THIS PR ##
#################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('Purchase_Req_Order.fp7','PO_line_items');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('PO_ID','=='.$PO_ID);
$search2 -> AddDBParam('item_type','1');
//$search2 -> AddDBParam('-lop','or');

//$search2 -> AddSortParam('leave_hrs_date','ascend');


$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
//print_r ($searchResult2);
$recordData2 = current($searchResult2['data']);
###############################################################
## END: FIND LEAVE REQUEST HRS RELATED TO THIS PR ##
###############################################################

if($recordData['c_attachment_count'][0] > 0){ // THE SELECTED PR HAS ATTACHMENTS
#################################################################
## START: FIND ATTACHMENTS RELATED TO THIS PR ##
#################################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('Purchase_Req_Order.fp7','PO_attachments');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('PO_ID','=='.$PO_ID);
//$search3 -> AddDBParam('-lop','or');

//$search3 -> AddSortParam('leave_hrs_date','ascend');


$searchResult3 = $search3 -> FMFind();

//echo '<p>$searchResult3[errorCode]: '.$searchResult3['errorCode'];
//echo '<p>$searchResult3[foundCount]: '.$searchResult3['foundCount'];
//print_r ($searchResult3);
$recordData3 = current($searchResult3['data']);
###############################################################
## END: FIND ATTACHMENTS RELATED TO THIS PR ##
###############################################################
}

if($recordData['c_user_log_count'][0] > 0){ // THE SELECTED PR HAS USER LOG ENTRIES
#################################################################
## START: FIND USER LOG ENTRIES RELATED TO THIS PR ##
#################################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('Purchase_Req_Order.fp7','PO_user_log');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('PO_ID','=='.$PO_ID);
//$search4 -> AddDBParam('-lop','or');

$search4 -> AddSortParam('creation_timestamp','ascend');


$searchResult4 = $search4 -> FMFind();

//echo '<p>$searchResult4[errorCode]: '.$searchResult4['errorCode'];
//echo '<p>$searchResult4[foundCount]: '.$searchResult4['foundCount'];
//print_r ($searchResult4);
$recordData4 = current($searchResult4['data']);
###############################################################
## END: FIND USER LOG ENTRIES RELATED TO THIS PR ##
###############################################################
}

$user = $_SESSION['user_ID'];
echo '<span style="color:#999999">User: '.$user.'</span>';
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Approve Purchase Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">


function confirmContract() { 
	var answer2 = confirm ("CFO: Approve this contract?")
	if (!answer2) {
	return false;
	}
}

function confirmExC() { 
	var answer2 = confirm ("CEO: Approve this SPR for Executive Committee?")
	if (!answer2) {
	return false;
	}
}

function ExCOnly() { 
	var answer2 = confirm ("This section reserved for the CEO.")
	return false;
}

function confirmSign() { 
	var answer2 = confirm ("Sign this supplemental purchase requisition now?")
	if (!answer2) {
	return false;
	}
}

function wrongSigner() { 
	var answer2 = confirm ("This space reserved for another signer.")
	return false;
}

function preapprovalRequired() { 
	var answer2 = confirm ("Pre-approval signatures are required before budget authority approval.")
	return false;
}

function BARequired() { 
	var answer2 = confirm ("All budget authority signatures are required before EO/Fiscal approval.")
	return false;
}

function ACCTRequired() { 
	var answer2 = confirm ("CPO: Accounting Supervisor must approve this SPR before CPO approval. You will be notified by SIMS when your signature (as CPO) is required.")
	return false;
}

function ACCTRequired2() { 
	var answer2 = confirm ("CEO: Accounting Supervisor must approve this SPR before CEO approval. You will be notified by SIMS when your signature (as CEO) is required.")
	return false;
}

function CPORequired() { 
	var answer2 = confirm ("CEO: Chief Program Officer (CPO) must sign this SPR before CEO approval. You will be notified by SIMS when your signature (as CEO) is required.")
	return false;
}

function CPORequired2() { 
	var answer2 = confirm ("CFO: Chief Program Officer (CPO) must sign this SPR before CFO approval. You will be notified by SIMS when your signature (as CFO) is required.")
	return false;
}

function CEORequired() { 
	var answer2 = confirm ("CFO: Chief Executive Officer (CEO) must sign this SPR before CFO approval. You will be notified by SIMS when your signature (as CFO) is required.")
	return false;
}

function CFOContract() { 
	var answer2 = confirm ("CFO: Unapproved contracts are attached to this SPR. Please approve all contracts before approving this SPR.")
	return false;
}


function rejectPrompt()
{
//var x;

var name=prompt("Enter a reason for rejecting this SPR:","");

//	document.location = 'pur_order_ba.php?action=reject&reason='+name;	
if(name!=null && name!=""){
  document.location = 'pur_order_ba.php?action=reject_spr&ba=<?php echo $user;?>&reason='+name+'&id=<?php echo $row_ID;?>';	
  //x="Hello " + name + "! How are you today?";
  //document.getElementById("demo").innerHTML=x;
	}
}


function toggle1() {
	var ele = document.getElementById("toggleText");
	var text = document.getElementById("displayText");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Approve";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "CANCEL";
	}
} 

function toggle2() {
	var ele = document.getElementById("toggleText2");
	var text = document.getElementById("displayText2");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Edit";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "CANCEL";
	}
} 




</script>
</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Purchase Requests: Budget Authority Admin</h1><hr /></td></tr>
			
			<?php if($recordData['status_sup'][0] == 'Approved'){ ?>
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Budget Authority: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - This SPR has been approved. <img src="/staff/sims/images/green_check.png"> | <a href="/staff/sims/menu_po_ba.php?action=view_sup">Close SPR</a></p>
			</td></tr>

			<?php }elseif($_SESSION['purchase_request_signed'] != '1'){ ?>
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Budget Authority: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - To approve this SPR, click the appropriate signature box below. | <input type=button value="Reject SPR" onClick="rejectPrompt()"> | <a href="/staff/sims/menu_po_ba.php?action=view_sup">Close SPR</a></p>
			</td></tr>
			
			<?php } elseif($_SESSION['purchase_request_signed'] == '1'){ ?>
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Budget Authority: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - You have successfully approved this SPR. <img src="/staff/sims/images/green_check.png"> | <a href="/staff/sims/menu_po_ba.php?action=view_sup">Close SPR</a></p>
			</td></tr>			

			
			<?php } ?>
			
			
			<tr><td colspan="2">
			
						<table cellpadding="10" cellspacing="0" border="0" bordercolor="ebebeb" bgcolor="ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong>Requested by: <?php echo $recordData['staff::c_full_name_first_last'][0];?> (<?php echo $recordData['SEDL_unit'][0];?>)</strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">SPR Status: <strong><?php if($recordData['status_sup'][0] == 'Pending'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['status_sup'][0]).'</span>';?></strong> - <strong>PO Number: <?php echo $recordData['PURCHASE_ORDER_NO'][0].'</strong>';?></span></td></tr>
						<tr><td class="body" nowrap><strong><span style="color:#0033ff">SUPPLEMENTAL</span> PURCHASE REQUISITION (SPR)</strong></td><td align="right">ID: <?php echo $PO_ID;?></td></tr>

						<tr><td colspan="2" class="body">
						

							<table cellpadding="7" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">

								<tr><td class="body" style="vertical-align:text-top;width:100%"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>DESCRIPTION/PURPOSE OF ORIGINAL PR</strong></div><br><?php echo $recordData['PR_description_general'][0];?></td>
								<td rowspan="3" style="vertical-align:text-top" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>APPROVAL LOG</strong></div>



								
									<table cellspacing="2" style="margin-top:6px">

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf"><strong>DATE</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>USER</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>ACTION</strong></td></tr>

									<?php if($searchResult4['foundCount'] > 0){ // SHOW PR STATUS LOG ?>
									<?php foreach($searchResult4['data'] as $key => $searchData4) { if($searchData4['comment'][0] != ''){$rowcolor = '#ffd6d6';}elseif(($searchData4['action'][0] == 'APPROVE_PR')||($searchData4['action'][0] == 'APPROVE_SPR')){$rowcolor = '#ace29f';}else{$rowcolor = '#ebebeb';} ?>

										<tr><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['creation_timestamp'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['user'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['action'][0];?></td></tr>
										<?php if($searchData4['comment'][0] != ''){?><tr><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" colspan="3"><?php echo $searchData4['comment'][0];?></td></tr><?php }?>

									<?php } ?>
									<?php } ?>
									</table>
								</td>
								</tr>

								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>NOTES/INSTRUCTIONS/REASON FOR SUPPLEMENTAL PR</strong></div><br><?php if($recordData['notes_sup'][0] != ''){echo $recordData['notes_sup'][0];}else{echo 'N/A';}?></td></tr>

								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>ATTACHMENTS (<?php echo $searchResult3['foundCount'];?>)</strong></div><br>
								
								<?php if($searchResult3['foundCount'] > 0){ ?>
									<ol style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px;width:95%;list-style-position: inside;">
									
									<?php foreach($searchResult3['data'] as $key => $searchData3) { // LIST ATTACHMENTS ?>
										
										<li style="padding:5px">
										
										<strong><?php echo ucwords($searchData3['attachment_description'][0]);?></strong><br>
										Type: <?php echo $searchData3['attachment_type'][0];?><br>
										Uploaded: <?php echo $searchData3['uploaded_timestamp'][0];?> by <?php echo $searchData3['uploaded_by'][0];?><br>
										File: <a href="http://198.214.141.190/sims/attachments/<?php echo $searchData3['attachment_filename'][0];?>" target="_blank" title="Click to download this attachment for review."><?php echo $searchData3['attachment_filename'][0];?></a><br>

										<?php if(($searchData3['attachment_type'][0] == 'Contract (hotel)')||($searchData3['attachment_type'][0] == 'Contract (other)')||($searchData3['attachment_type'][0] == 'Subcontract Agreement Notice')){ ?>
										
											<div class="tiny" style="background-color:#ffffff;border:1px dotted #000000;padding:4px;margin:4px"> 
											
											<?php if($searchData3['appr_status_contract_cfo'][0] == '1'){?>
											
												<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CFO CONTRACT APPROVAL | <span style="color:#0033ff"><strong>APPROVED</strong></span> | <?php echo $searchData3['appr_timestamp_contract_cfo'][0];?>
												</div>
										
											<?php }else{?>
										
												<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CFO CONTRACT APPROVAL | <span style="color:#ff0000"><strong>PENDING</strong></span>  | <a href="pur_order_ba.php?action=view&id=<?php echo $PO_ID;?>&attid=<?php echo $searchData3['c_row_ID'][0];?>" title="CFO: Click to approve this contract." <?php if($_SESSION['user_ID'] !== 'sferguso'){?>onclick="return wrongSigner()"<?php }else{?>onclick="return confirmContract()"<?php }?>>Approve</a></div>
												
												</div>
										
											<?php }?>
										

										<?php }?>
									
										</li>

										<hr style="border:1px dotted #000000">
									<?php  } ?></ol>
	
								<?php }else{ ?>
								
								N/A
								<?php } ?>

								</td></tr>


								
								
								<tr><td class="body" colspan="2"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>LINE ITEM DETAILS (<?php echo $searchResult2[foundCount];?>)</strong></div><br>
								
										<table cellpadding="7" cellspacing="0" border="1" bordercolor="#cccccc" width="100%" class="sims">
										<tr bgcolor="#ebebeb"><td class="body" nowrap>Item Description</td><td class="body">Quantity</td><td class="body">Unit</td><td class="body" align="right" nowrap>Unit Price</td><td class="body" align="right">Total</td></tr>
		
										<?php foreach($searchResult2['data'] as $key => $searchData) { ?>
										
		
												<tr class="body"><td style="vertical-align:top"><?php echo $searchData['item_description'][0];?><br><div class="tiny" style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:3px;font-size:10px"><?php echo $searchData['c_budget_code_list_ext'][0];?></div></td><td nowrap style="vertical-align:top"><?php echo $searchData['quantity'][0];?></td><td nowrap style="vertical-align:top"><?php echo $searchData['UNIT'][0];?></td><td nowrap align="right" style="vertical-align:top">$<?php echo number_format($searchData['unit_price'][0],2,'.',',');?></td><td nowrap align="right" style="vertical-align:top">$<?php echo number_format($searchData['c_TOTAL'][0],2,'.',',');?></td></tr>
											
									
										<?php } ?>
		
											<tr class="body"><td bgcolor="#ebebeb" colspan="4" nowrap align="right"><em>Supplemental Amount:</em></td><td align="right"><strong><?php if($recordData['c_sup_po_amount'][0] != ''){?>$<?php echo number_format($recordData['c_sup_po_amount'][0],2,'.',','); ?><?php }else{?>N/A<?php }?></strong></td></tr>
											<tr class="body"><td bgcolor="#ebebeb" colspan="4" nowrap align="right"><em>Original PO Amount:</em></td><td align="right" nowrap><strong><?php if($recordData['original_PO_total'][0] != ''){?><span style="color:#4e474b">+ $<?php echo number_format($recordData['original_PO_total'][0],2,'.',','); ?><?php }else{?>N/A<?php }?></strong></span></td></tr>
											<tr class="body"><td bgcolor="#ebebeb" colspan="4" nowrap align="right"><em>Modified Total:</em></td><td align="right" style="border-top-width:3px"><span style="color:#0033ff"><strong>$<?php echo number_format($recordData['c_PO_total'][0],2,'.',','); ?></strong></span></td></tr>
		
		
		
													
		
		
									</table>
								
								
								</td></tr>
								


<?php if($recordData['bid1'][0] != ''){ // DON'T SHOW BIDS IF THERE ARE NONE ?>
								<tr><td colspan="2" class="body" style="width:50%;vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>BIDS</strong></div><br>
								
									<table style="border:0px dotted #000000;margin-top:6px;width:100%">
									<tr><td style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px;width:100%">Vendor</td><td style="text-align:right;border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px" nowrap>Bid Amount</td></tr>
									<tr><td style="padding:5px"><?php if($recordData['bid1'][0] == ''){echo 'N/A';}else{echo $recordData['bid1'][0];}?></td><td style="text-align:right;padding:5px">&nbsp;<?php if($recordData['bid_1_total'][0] != ''){echo '$'.number_format($recordData['bid_1_total'][0],2,'.',',');}?></td></tr>
									<tr><td style="padding:5px"><?php if($recordData['bid2'][0] == ''){echo 'N/A';}else{echo $recordData['bid2'][0];}?></td><td style="text-align:right;padding:5px">&nbsp;<?php if($recordData['bid_2_total'][0] != ''){echo '$'.number_format($recordData['bid_2_total'][0],2,'.',',');}?></td></tr>
									</table>
								
								</td></tr>
<?php } ?>								
								<tr><td colspan="2" class="body" style="width:50%;vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>VENDOR</strong></div><br>
								
									<table style="border:0px dotted #000000;margin-top:6px;width:100%">
									<tr><td style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px;width:100%">Vendor</td><td style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px;width:100%">Attention</td></tr>
									<tr><td style="padding:5px;vertical-align:text-top"><?php echo $recordData['vendor_name'][0];?><br><?php echo $recordData['vendor_addr1'][0];?><br><?php echo $recordData['vendor_city'][0];?>, <?php echo $recordData['vendor_state'][0];?> <?php echo $recordData['vendor_zip'][0];?></td><td style="padding:5px;vertical-align:text-top" nowrap><?php echo $recordData['vendor_attn_to'][0];?><br><?php echo $recordData['vendor_phone'][0];?></td></tr>
									</table>
								
								</td>
								
								</tr>

								

													<tr class="body"><td colspan="2" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>SIGNATURES</strong></div><br>

<?php if($recordData['PR_sup_submitted_timestamp'][0] != ''){ ?>

														<table class="sims" cellspacing="1" cellpadding="10" border="1" width="50%">
		
		<?php if(($recordData['c_sign_button_IT_sup'][0] != 'N/A')||($recordData['c_sign_button_IRC_sup'][0] != 'N/A')){ // IT or IRC APPROVAL REQUIRED  ?>
		
														<tr class="body" valign="top"><td><div style="border:1px dotted #000000;background-color:#bddbee;padding:5px" class="tiny">PRE-APPROVAL</div><br>
														
															<table>
															<tr><td style="border-width:0px;padding:0px;margin:0px">
															
															<?php if($recordData['c_sign_button_IT_sup'][0] != 'N/A'){ // IT APPROVAL REQUIRED  ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_IT_sup'][0] == '1'){ // IT APPROVED ?><img src="/staff/sims/signatures/cpierron.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=cpierron&appid=<?php echo $row_ID;?>&role=IT" <?php if($_SESSION['user_ID'] != 'cpierron'){echo 'onclick="return wrongSigner()"';}else{echo 'onclick="return confirmSign()"';}?>>cpierron</a><?php } ?><p>
																<span class="tiny"><em>IT: Network Administrator</em><br><?php if($recordData['sign_timestamp_IT_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_IT_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_IRC_sup'][0] != 'N/A'){ // IRC APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_IRC_sup'][0] == '1'){ // IRC APPROVED ?><img src="/staff/sims/signatures/nreynold.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=nreynold&appid=<?php echo $row_ID;?>&role=IRC" <?php if($_SESSION['user_ID'] != 'nreynold'){echo 'onclick="return wrongSigner()"';}else{echo 'onclick="return confirmSign()"';}?>>nreynold</a><?php } ?><p>
																<span class="tiny"><em>IRC: Information Associate</em><br><?php if($recordData['sign_timestamp_IRC_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_IRC_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
															</tr>
															</table>
		
														</td></tr>										
		
		<?php } ?>
		
														<tr class="body" valign="top"><td><div style="border:1px dotted #000000;background-color:#bddbee;padding:5px" class="tiny">BA APPROVAL</div><br>
														
															<table>
															<tr><td style="border-width:0px;padding:0px;margin:0px">
		
															<?php if($recordData['c_sign_button_ba_1_sup'][0] != 'N/A'){ // BA1 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_1_sup'][0] == '1'){ // BA1 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_1_sup'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_1_sup'][0];?>&appid=<?php echo $row_ID;?>&role=ba1" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_1_sup'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_1_sup'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_1_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_1_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_2_sup'][0] != 'N/A'){ // BA2 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_2_sup'][0] == '1'){ // BA2 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_2_sup'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_2_sup'][0];?>&appid=<?php echo $row_ID;?>&role=ba2" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_2_sup'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_2_sup'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_2_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_2_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_3_sup'][0] != 'N/A'){ // BA3 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_3_sup'][0] == '1'){ // BA3 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_3_sup'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_3_sup'][0];?>&appid=<?php echo $row_ID;?>&role=ba3" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_3_sup'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_3_sup'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_3_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_3_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_4_sup'][0] != 'N/A'){ // BA4 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_4_sup'][0] == '1'){ // BA4 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_4_sup'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_4_sup'][0];?>&appid=<?php echo $row_ID;?>&role=ba4" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_4_sup'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_4_sup'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_4_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_4_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_5_sup'][0] != 'N/A'){ // BA5 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_5_sup'][0] == '1'){ // BA5 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_5_sup'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_5_sup'][0];?>&appid=<?php echo $row_ID;?>&role=ba5" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_5_sup'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_5_sup'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_5_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_5_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_6_sup'][0] != 'N/A'){ // BA6 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_6_sup'][0] == '1'){ // BA6 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_6_sup'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_6_sup'][0];?>&appid=<?php echo $row_ID;?>&role=ba6" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_6_sup'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_6_sup'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_6_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_6_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_7_sup'][0] != 'N/A'){ // BA7 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_7_sup'][0] == '1'){ // BA7 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_7_sup'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_7_sup'][0];?>&appid=<?php echo $row_ID;?>&role=ba7" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_7_sup'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_7_sup'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_7_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_7_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ba_8_sup'][0] != 'N/A'){ // BA8 APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px">
																<?php if($recordData['sign_status_ba_8_sup'][0] == '1'){ // BA8 APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['signer_ID_ba_8_sup'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=<?php echo $recordData['signer_ID_ba_8_sup'][0];?>&appid=<?php echo $row_ID;?>&role=ba8" <?php if(($_SESSION['user_ID'] != $recordData['signer_ID_ba_8_sup'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ba_8_sup'][0];?></a><?php }?><p>
																<span class="tiny"><em>Budget Authority</em><br><?php if($recordData['sign_timestamp_ba_8_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ba_8_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
															</tr>
															</table>
		
														</td></tr>										
		
														<tr class="body" valign="top"><td style="width:100%"><div style="border:1px dotted #000000;background-color:#bddbee;padding:5px" class="tiny">EO/FISCAL APPROVAL</div><br>
														
														<?php if($recordData['c_PO_total'][0] > 100000){ // EXECUTIVE COMMITTEE APPROVAL IS REQUIRED - PO IS OVER $100K ?>			
														
															<div class="tiny" style="background-color:#ffffff;border:1px dotted #000000;padding:5px;width:90%">
															
																<?php if($recordData['appr_status_exc_sup'][0] == '1'){?>
																
																	<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> Executive Committee (over $100K) | <span style="color:#0033ff"><strong>APPROVED</strong></span> | <?php echo $recordData['appr_timestamp_exc_sup'][0];?>
																		<div class="tiny" style="background-color:#fff6bf;border:1px solid #999999;padding:5px;margin:10px"><strong>CEO Notes</strong> (includes PR and SPR notes)<div style="float:right"><a href="javascript:toggle2();" id="displayText2" title="CEO: Click to edit CEO comments." <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return ExCOnly()"';}?>>Edit</a></div><br>
																		<?php echo $recordData['appr_status_exc_notes'][0];?>
	
																	<div id="toggleText2" style="display: none; padding:10px 0px 0px 10px">
																	<form method="get">
																	<input type="hidden" name="action" value="view_sup">
																	<input type="hidden" name="approve" value="3">
																	<input type="hidden" name="appid" value="<?php echo $row_ID;?>">
																	<input type="hidden" name="ba" value="<?php echo $_SESSION['user_ID'];?>">
																	<input type="hidden" name="id" value="<?php echo $PO_ID;?>">
																	<input type="text" name="exc_notes" value="<?php echo $recordData['appr_status_exc_notes'][0];?>" size="55">
																	<input type="submit" name="submit" value="Save">
																	</form>
																	</div>
	
																	</div>
																	</div>
																
																<?php }else{?>
																
																	<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> Executive Committee approval (over $100K) | <span style="color:#ff0000"><strong>PENDING</strong></span> | <!--<a href="pur_order_ba.php?id=<?php echo $PO_ID;?>&action=view_sup&approve=2&appid=<?php echo $row_ID;?>&ba=<?php echo $_SESSION['user_ID'];?>" title="CEO: Click to approve this SPR for Executive Committee." <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return ExCOnly()"';}else{echo 'onclick="return confirmExC()"';}?>>Approve</a>-->
																	
																	<span class="tiny"><a href="javascript:toggle1();" id="displayText" title="CEO: Click to approve this PR for Executive Committee." <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return ExCOnly()"';}?>>Approve</a></span>
																	<div id="toggleText" style="display: none; padding:10px 0px 0px 10px"><strong>CEO Notes</strong> (includes PR and SPR notes)
																	<form method="get">
																	<input type="hidden" name="action" value="view_sup">
																	<input type="hidden" name="approve" value="2">
																	<input type="hidden" name="appid" value="<?php echo $row_ID;?>">
																	<input type="hidden" name="ba" value="<?php echo $_SESSION['user_ID'];?>">
																	<input type="hidden" name="id" value="<?php echo $PO_ID;?>">
																	<input type="text" name="exc_notes" value="<?php echo $recordData['appr_status_exc_notes'][0];?>" size="55">
																	<input type="submit" name="submit" value="Approve">
																	</form>
																	</div>
																	
																	</div>
																	
																	</div>
																	
																<?php }?>
																
															</div><p>
														
														<?php } ?>
														
														<div class="tiny" style="background-color:#ffffff;border:1px dotted #000000;padding:5px;width:90%"><?php if($recordData['appr_status_acct_sup'][0] == '1'){?><img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> Accounting Supervisor | <span style="color:#0033ff"><strong>APPROVED</strong></span> | <?php echo $recordData['appr_timestamp_acct_sup'][0];?><?php }else{?><img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> Accounting Supervisor | <span style="color:#ff0000"><strong>PENDING</strong></span><?php }?></div>
														
															<table style="margin-top:10px">
															<tr><td style="border-width:0px;padding:0px;margin:0px">
		
			
															<?php if($recordData['c_sign_button_cpo_sup'][0] != 'N/A'){ // CPO APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px" nowrap>
																<?php if($recordData['sign_status_cpo_sup'][0] == '1'){ // CPO APPROVED ?><img src="/staff/sims/signatures/vdimock.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=vdimock&appid=<?php echo $row_ID;?>&role=cpo" <?php if($_SESSION['user_ID'] != 'vdimock'){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}elseif($recordData['c_ba_signer_status_sup'][0] == '0'){echo 'onclick="return BARequired()"';}elseif($recordData['appr_status_acct_sup'][0] != '1'){echo 'onclick="return ACCTRequired()"';}else{echo 'onclick="return confirmSign()"';}?>>vdimock</a><?php }?><p>
																<span class="tiny"><em>Chief Program Officer</em><br><?php if($recordData['sign_timestamp_cpo_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_cpo_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_ceo_sup'][0] != 'N/A'){ // CEO APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px" nowrap>
																<?php if($recordData['sign_status_ceo_sup'][0] == '1'){ // CEO APPROVED ?><img src="/staff/sims/signatures/whoover.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=whoover&appid=<?php echo $row_ID;?>&role=ceo" <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}elseif($recordData['c_ba_signer_status_sup'][0] == '0'){echo 'onclick="return BARequired()"';}elseif(($recordData['c_sign_button_cpo_sup'][0] != 'N/A')&&($recordData['sign_status_cpo_sup'][0] != '1')){echo 'onclick="return CPORequired()"';}elseif($recordData['appr_status_acct_sup'][0] != '1'){echo 'onclick="return ACCTRequired2()"';}else{echo 'onclick="return confirmSign()"';}?>>whoover</a><?php }?><p>
																<span class="tiny"><em>Chief Executive Officer</em><br><?php if($recordData['sign_timestamp_ceo_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_ceo_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
			
															<?php if($recordData['c_sign_button_cfo_sup'][0] != 'N/A'){ // CFO APPROVAL REQUIRED ?>
															
																<td align="center" valign="bottom" style="padding:5px" nowrap>
																<?php if($recordData['sign_status_cfo_sup'][0] == '1'){ // CFO APPROVED ?><img src="/staff/sims/signatures/sferguso.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="pur_order_ba.php?action=view_sup&id=<?php echo $PO_ID;?>&approve=1&ba=sferguso&appid=<?php echo $row_ID;?>&role=cfo" <?php if($_SESSION['user_ID'] != 'sferguso'){echo 'onclick="return wrongSigner()"';}elseif($preappRequired == '1'){echo 'onclick="return preapprovalRequired()"';}elseif($recordData['c_ba_signer_status_sup'][0] == '0'){echo 'onclick="return BARequired()"';}elseif(($recordData['c_sign_button_cpo_sup'][0] != 'N/A')&&($recordData['sign_status_cpo_sup'][0] != '1')){echo 'onclick="return CPORequired2()"';}elseif(($recordData['c_sign_button_ceo_sup'][0] != 'N/A')&&($recordData['sign_status_ceo_sup'][0] != '1')){echo 'onclick="return CEORequired()"';}elseif($recordData['c_appr_status_contract_cfo'][0] == '0'){echo 'onclick="return CFOContract()"';}else{echo 'onclick="return confirmSign()"';}?>>sferguso</a><?php }?><p>
																<span class="tiny"><em>Chief Financial Officer</em><br><?php if($recordData['sign_timestamp_cfo_sup'][0] != ''){ ?><font color="999999">[<?php echo $recordData['sign_timestamp_cfo_sup'][0];?>]</font><?php } ?></span>
																</td>
															
															<?php } ?>
															</tr>
															</table>
		
														</td></tr>										
		
														</table>
		
													</td></tr>

<?php }else{ ?>								

													<tr><td class="body" style="vertical-align:text-top" colspan="2">This supplemental PR was processed manually or has not been submitted.</td></tr>

<?php } ?>			
							</table>


						</td></tr>

						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>

<? 
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################


} elseif(($action == 'reject_pr')||($action == 'reject_spr')) { 

$ba = $_GET['ba'];
$current_id = $_GET['id'];
$reason = $_GET['reason'];

//echo '<p>$ba: '.$ba;
//echo '<p>$current_id: '.$current_id;
//echo '<p>$reason: '.$reason;

//$trigger = rand();
#################################################
## START: UPDATE THE PURCHASE REQUEST ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('Purchase_Req_Order.fp7','PO_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('sign_status_IT','');
$update -> AddDBParam('sign_status_IRC','');
$update -> AddDBParam('sign_status_ba_1','');
$update -> AddDBParam('sign_status_ba_2','');
$update -> AddDBParam('sign_status_ba_3','');
$update -> AddDBParam('sign_status_ba_4','');
$update -> AddDBParam('sign_status_ba_5','');
$update -> AddDBParam('sign_status_ba_6','');
$update -> AddDBParam('sign_status_ba_7','');
$update -> AddDBParam('sign_status_ba_8','');
$update -> AddDBParam('sign_status_cpo','');
$update -> AddDBParam('sign_status_ceo','');
$update -> AddDBParam('sign_status_cfo','');
$update -> AddDBParam('appr_status_acct','');
$update -> AddDBParam('appr_status_exc','');

$update -> AddDBParam('ba_reject_reason',$reason);
$update -> AddDBParam('ba_reject_id',$ba);

$update -> AddDBParam('-script','PR_reject_ba');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$recordData = current($updateResult['data']);


if($updateResult['errorCode'] == '0'){


	#################################################################
	## START: SEND REJECT E-MAIL NOTIFICATION ##
	#################################################################
	$search = new FX($serverIP,$webCompanionPort);
	$search -> SetDBData('Purchase_Req_Order.fp7','PO_table');
	$search -> SetDBPassword($webPW,$webUN);
	$search -> AddDBParam('c_row_ID','=='.$current_id);
	
	$search -> AddDBParam('-script','PR_reject_ba');
	

	$searchResult = $search -> FMFind();

	//echo  '<p>errorCode: '.$searchResult['errorCode'];
	//echo  '<p>foundCount: '.$searchResult['foundCount'];

	###############################################################
	## END: SEND REJECT E-MAIL NOTIFICATION ##
	###############################################################


	// LOG THIS ACTION IN SIMS AUDIT TABLE
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	if($action == 'reject_pr'){
	$newrecord -> AddDBParam('action','REJECT_PR');
	}else{
	$newrecord -> AddDBParam('action','REJECT_SPR');
	}
	$newrecord -> AddDBParam('notes','Reason: '.$reason);
	$newrecord -> AddDBParam('table','PO_main');
	$newrecord -> AddDBParam('object_ID',$recordData['PO_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

	// LOG ACTION IN PO USER LOG
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('Purchase_Req_Order.fp7','PO_user_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	if($action == 'reject_pr'){
	$newrecord -> AddDBParam('action','REJECT_PR');
	}else{
	$newrecord -> AddDBParam('action','REJECT_SPR');
	}
	$newrecord -> AddDBParam('comment','Reason: '.$reason);
	$newrecord -> AddDBParam('PO_ID',$recordData['PO_ID'][0]);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

$_SESSION['pr_rejected'] = '1';

} else {
$_SESSION['pr_rejected'] = '2';

}
###############################################
## END: UPDATE THE PURCHASE REQUEST ##
###############################################

header('Location: http://www.sedl.org/staff/sims/menu_po_ba.php');
exit;
?>


<?php 

############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################

} elseif($action == 'reject_spr') { 

$ba = $_GET['ba'];
$current_id = $_GET['id'];
$reason = $_GET['reason'];

//echo '<p>$ba: '.$ba;
//echo '<p>$current_id: '.$current_id;
//echo '<p>$reason: '.$reason;

//$trigger = rand();
#################################################
## START: UPDATE THE PURCHASE REQUEST ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('Purchase_Req_Order.fp7','PO_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('sign_status_IT_sup','');
$update -> AddDBParam('sign_status_IRC_sup','');
$update -> AddDBParam('sign_status_ba_1_sup','');
$update -> AddDBParam('sign_status_ba_2_sup','');
$update -> AddDBParam('sign_status_ba_3_sup','');
$update -> AddDBParam('sign_status_ba_4_sup','');
$update -> AddDBParam('sign_status_ba_5_sup','');
$update -> AddDBParam('sign_status_ba_6_sup','');
$update -> AddDBParam('sign_status_ba_7_sup','');
$update -> AddDBParam('sign_status_ba_8_sup','');
$update -> AddDBParam('sign_status_cpo_sup','');
$update -> AddDBParam('sign_status_ceo_sup','');
$update -> AddDBParam('sign_status_cfo_sup','');
$update -> AddDBParam('appr_status_acct_sup','');
$update -> AddDBParam('appr_status_exc_sup','');

$update -> AddDBParam('ba_reject_reason_sup',$reason);
$update -> AddDBParam('ba_reject_id_sup',$ba);

$update -> AddDBParam('-script','SPR_reject_ba');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$recordData = current($updateResult['data']);


if($updateResult['errorCode'] == '0'){

	// LOG THIS ACTION SIMS AUDIT TABLE
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','REJECT_SPR');
	$newrecord -> AddDBParam('notes','Reason: '.$reason);
	$newrecord -> AddDBParam('table','PO_main');
	$newrecord -> AddDBParam('object_ID',$recordData['PO_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
	
	// LOG ACTION IN PO USER LOG
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('Purchase_Req_Order.fp7','PO_user_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','REJECT_SPR');
	$newrecord -> AddDBParam('comment','Reason: '.$reason);
	$newrecord -> AddDBParam('PO_ID',$recordData['PO_ID'][0]);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


$_SESSION['spr_rejected'] = '1';

} else {
$_SESSION['spr_rejected'] = '2';

}
###############################################
## END: UPDATE THE PURCHASE REQUEST ##
###############################################

header('Location: http://www.sedl.org/staff/sims/menu_po_ba.php?action=view_sup');
exit;
?>


<?php 

############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################

} else { ?>
Error
<?php } ?>

<?php 

if($debug == 'on'){

echo '<p>$action: '.$action;
echo '<p>$delete_request_row: '.$delete_request_row;
echo '<p>$add_to_request: '.$add_to_request;
echo '<p>$leave_request_ID: '.$leave_request_ID;
echo '<p>$timesheet_ID: '.$timesheet_ID;
echo '<p>$_SESSION[leave_request_ID]: '.$_SESSION['leave_request_ID'];
echo '<p>$day_from: '.$day_from;
echo '<p>$day_to: '.$day_to;
echo '<p>$time_from: '.$time_from;
echo '<p>$time_to: '.$time_to;
echo '<p>$num_hrs: '.$num_hrs;
echo '<p>$date_from_m: '.$date_from_m;
echo '<p>$date_from_y: '.$date_from_y;

}
?>
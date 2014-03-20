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
$sortfield = $_GET['sortfield'];
$sortorder = $_GET['sortorder'];
$displaynum = $_GET['displaynum'];
$position_ID = $_GET['pos_id'];


$view = $_GET['v'];

if($action == ''){ 
$action = 'view';
}

$resume_ID = $_GET['id'];
$reviewer_submit = $_GET['reviewer_submit'];
$logx = $_GET['logx'];
$logid = $_GET['logid'];
//$status = $_GET['status'];


$location = 'Location: http://www.sedl.org/staff/sims/menu_positions_novs.php?pos_id='.$position_ID.'&sortfield='.$sortfield.'&sortorder='.$sortorder.'&displaynum='.$displaynum.'#'.$recordData['resume_ID'][0];
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
if($action == 'view'){ //IF THE USER IS VIEWING THIS JOB APPLICATION

################################
## START: GET FMP VALUE-LISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('admin_base.fp7','action_log_comments');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
//echo  '<p>errorCode: '.$v1Result['errorCode'];
//echo  '<p>foundCount: '.$v1Result['foundCount'];
//print_r($v1Result);
##############################
## END: GET FMP VALUE-LISTS ##
##############################

if(($logx == '1')&&($logid !== '')) { // IF THE REVIEWER DELETED A LOG ITEM
#################################################
## START: DELETE THE LOG ITEM ##
#################################################
$delete = new FX($serverIP,$webCompanionPort);
$delete -> SetDBData('admin_base.fp7','resumes_action_log');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$logid);

$deleteResult = $delete -> FMDelete();
#################################################
## END: DELETE THE LOG ITEM ##
#################################################
}

if($reviewer_submit == '1') { // IF THE REVIEWER SUBMITTED A RESPONSE
#################################################
## START: ADD THE REVIEWER RESPONSE ##
#################################################

$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('admin_base.fp7','resumes_action_log'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information

$newrecord -> AddDBParam('resume_ID',$resume_ID);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('record_type',$_GET['record_type']);
$newrecord -> AddDBParam('action',$_GET['mgr_action']);


if(($_GET['action_target'] !== '')&&(($_GET['mgr_action'] == 'Send for further review')||($_GET['mgr_action'] == 'Provide feedback to hiring manager'))){

	for($i=0 ; $i<count($_GET['action_target']) ; $i++) {
	$action_target .= $_GET['action_target'][$i]."\r"; 
	}

$newrecord -> AddDBParam('action_target',$action_target);
}else{
$newrecord -> AddDBParam('action_target','HR');
}

if(($_GET['doc_screening_1b_reason'] !== '')&&($_GET['mgr_action'] == 'Resume reviewed - no to applicant')){
$newrecord -> AddDBParam('comment','[REASON->'.$_GET['doc_screening_1b_reason'].'] '.$_GET['comments']);
$app_rejected1 = '1';
}

if(($_GET['doc_screening_2b_reason'] !== '')&&($_GET['mgr_action'] == 'Application form reviewed - no to applicant')){
$newrecord -> AddDBParam('comment','[REASON->'.$_GET['doc_screening_2b_reason'].'] '.$_GET['comments']);
$app_rejected2 = '1';
}

if(($_GET['nov_priority_rating_post_resume'] !== '')&&($_GET['mgr_action'] == 'Resume reviewed - has potential, assign priority')){
$newrecord -> AddDBParam('comment','[PRIORITY->'.$_GET['nov_priority_rating_post_resume'].'] '.$_GET['comments']);
$priority_assigned1 = '1';
}

if(($_GET['nov_priority_rating_post_application'] !== '')&&($_GET['mgr_action'] == 'Application form reviewed - has potential, assign priority')){
$newrecord -> AddDBParam('comment','[PRIORITY->'.$_GET['nov_priority_rating_post_application'].'] '.$_GET['comments']);
$priority_assigned2 = '1';
}

if(($app_rejected1 !== '1')&&($app_rejected2 !== '1')&&($priority_assigned1 !== '1')&&($priority_assigned2 !== '1')){
$newrecord -> AddDBParam('comment',$_GET['comments']);
}

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

$newrecordData = current($newrecordResult['data']);

$reviewer_action_ID = $newrecordData['record_ID'][0];
$doc_type = $newrecordData['record_type'][0];
$send_to = $newrecordData['c_target_email'][0];
$send_to_IDs = $newrecordData['c_target_IDs'][0];
$current_id = $newrecordData['unsolicited_resumes::c_row_ID'][0];
$created_by = $newrecordData['unsolicited_resumes::created_by'][0];

//echo '$created_by: '.$created_by;
//echo '$send_to: '.$send_to;
//exit;
if($doc_type == '1'){
$doc_name = 'An unsolicited resume';
$position = 'Unsolicited resume';
}else{
$doc_name = 'A prospective job applicant';
$position = $_GET['position'];
}

if($newrecordResult['errorCode'] == '0'){ // USER RESPONSE WAS SUCCESSFULLY SAVED TO THE ACTION LOG
$_SESSION['action_logged'] = '1';


###########################################################################################################
## START: IF POST-RESUME PRIORITY OR POST-APPLICATION PRIORITY WAS ASSIGNED, UPDATE THE APPLICANT RECORD ##
###########################################################################################################
if(($priority_assigned1 == '1')||($priority_assigned2 == '1')){

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('admin_base.fp7','resume_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);
if($priority_assigned1 == '1'){
$update -> AddDBParam('nov_priority_rating_post_resume',$_GET['nov_priority_rating_post_resume']);
$update -> AddDBParam('nov_priority_rating_post_resume_by',$_SESSION['user_ID']);
$newpriority = $_GET['nov_priority_rating_post_resume'];
}
if($priority_assigned2 == '1'){
$update -> AddDBParam('nov_priority_rating_post_application',$_GET['nov_priority_rating_post_application']);
$update -> AddDBParam('nov_priority_rating_post_application_by',$_SESSION['user_ID']);
$newpriority = $_GET['nov_priority_rating_post_application'];
}

$updateResult = $update -> FMEdit();
//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];

}
###########################################################################################################
## END: IF POST-RESUME PRIORITY OR POST-APPLICATION PRIORITY WAS ASSIGNED, UPDATE THE APPLICANT RECORD ##
###########################################################################################################

###########################################################################################################
## START: IF APPLICANT WAS REJECTED, UPDATE THE APPLICANT RECORD (PROCESS DOCUMENTATION FORM) ##
###########################################################################################################
if(($app_rejected1 == '1')||($app_rejected2 == '1')){
$trigger = rand();

$cc = 'mturner@sedl.org'; // CC ON APPLICANT REJECTION

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('admin_base.fp7','resume_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);
if($app_rejected1 == '1'){
$update -> AddDBParam('doc_screening_1b_by',$_SESSION['user_ID']);
$update -> AddDBParam('doc_screening_1b_reason',$_GET['doc_screening_1b_reason']);
$update -> AddDBParam('doc_screening_1b_timestamp_trigger',$trigger);
}
if($app_rejected2 == '1'){
$update -> AddDBParam('doc_screening_2b_by',$_SESSION['user_ID']);
$update -> AddDBParam('doc_screening_2b_reason',$_GET['doc_screening_2b_reason']);
$update -> AddDBParam('doc_screening_2b_reason_other_skills',$_GET['doc_screening_2b_reason_other_skills']);
$update -> AddDBParam('doc_screening_2b_reason_other_educ',$_GET['doc_screening_2b_reason_other_educ']);
$update -> AddDBParam('doc_screening_2b_reason_other_exp',$_GET['doc_screening_2b_reason_other_exp']);
$update -> AddDBParam('doc_screening_2b_reason_other_writing',$_GET['doc_screening_2b_reason_other_writing']);
$update -> AddDBParam('doc_screening_2b_timestamp_trigger',$trigger);
}

$updateResult = $update -> FMEdit();
//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];

}
###########################################################################################################
## END: IF APPLICANT WAS REJECTED, UPDATE THE APPLICANT RECORD (PROCESS DOCUMENTATION FORM) ##
###########################################################################################################

###########################################################################################################
## START: IF OFFER TO EMPLOY WAS MADE, UPDATE THE APPLICANT RECORD ##
###########################################################################################################
if($_GET['mgr_action'] == 'Interview conducted - offer to employ'){

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('admin_base.fp7','resume_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('nov_status','Offer to employ');

$updateResult = $update -> FMEdit();
//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];

}
###########################################################################################################
## END: IF OFFER TO EMPLOY WAS MADE, UPDATE THE APPLICANT RECORD ##
###########################################################################################################

###########################################################################################################
## START: IF NO TO APPLICANT, UPDATE THE APPLICANT RECORD ##
###########################################################################################################
if(($_GET['mgr_action'] == 'Resume reviewed - no to applicant')||($_GET['mgr_action'] == 'Application form reviewed - no to applicant')||($_GET['mgr_action'] == 'Interview conducted - no to applicant')){

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('admin_base.fp7','resume_table');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('nov_status','No to applicant');

$updateResult = $update -> FMEdit();
//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];

}
###########################################################################################################
## END: IF NO TO APPLICANT, UPDATE THE APPLICANT RECORD ##
###########################################################################################################


	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action',$_GET['mgr_action']);
	$newrecord -> AddDBParam('table','resumes_action_log');
	$newrecord -> AddDBParam('object_ID',$newrecordData['record_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$newrecordData['record_ID'][0]);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];



		if($_GET['mgr_action'] == 'Provide feedback to hiring manager'){ // SEND E-MAIL NOTIFICATION TO SELECTED MANAGER (FEEDBACK FROM INTERVIEW TEAM)
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED MANAGER ##
			##########################################################
			$to = $send_to;
			$subject = 'SIMS RESUMES & APPLICATIONS: FEEDBACK RECEIVED FROM INTERVIEW TEAM';
			$message = 
			'Dear Manager,'."\n\n".
			
			$doc_name.' has been reviewed by interview team member ('.$_SESSION['user_ID'].') and feedback has been provided for your consideration.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Applicant Name: '.$_GET['applicant_name']."\n".
			'Position: '.$position."\n".
			'Action: '.$_GET['mgr_action'].' ('.$send_to_IDs.')'."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To review this document, click here:'."\n".
			'http://www.sedl.org/staff/sims/positions_novs_ba.php?v='.$view.'&id='.$resume_ID."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################

		}elseif($_GET['mgr_action'] == 'Application form reviewed - schedule interview'){ // SEND E-MAIL NOTIFICATION TO HR GENERALIST TO SCHEDULE INTERVIEW
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO HR ##
			##########################################################
			$to = 'sliberty@sedl.org';
			$subject = 'SIMS RESUMES & APPLICATIONS: INTERVIEW REQUESTED';
			$message = 
			'Dear HR,'."\n\n".
			
			$doc_name.' has been reviewed by interview team member ('.$_SESSION['user_ID'].') and feedback has been provided for your consideration.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Applicant Name: '.$_GET['applicant_name']."\n".
			'Position: '.$position."\n".
			'Action: '.$_GET['mgr_action'].' ('.$send_to_IDs.')'."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To process this response, log on to the SIMS: Resumes and Applications database at the following link:'."\n".
			
			'fmp7://198.214.140.248/admin_base.fp7'."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO HR ##
			########################################################

		}elseif($_GET['mgr_action'] == 'Interview conducted - offer to employ'){ // SEND E-MAIL NOTIFICATION TO HR GENERALIST TO SEND OFFER DETAILS TO APPLICANT
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO HR ##
			##########################################################
			$to = 'sliberty@sedl.org';
			$subject = 'SIMS RESUMES & APPLICATIONS: OFFER TO EMPLOY';
			$message = 
			'Dear HR,'."\n\n".
			
			$doc_name.' has been interviewed and an offer of employment has been made by ('.$_SESSION['user_ID'].')'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Applicant Name: '.$_GET['applicant_name']."\n".
			'Position: '.$position."\n".
			'Action: '.$_GET['mgr_action'].' ('.$send_to_IDs.')'."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To process this response, log on to the SIMS: Resumes and Applications database at the following link:'."\n".
			
			'fmp7://198.214.140.248/admin_base.fp7'."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO HR ##
			########################################################

		}elseif($send_to !== ''){ // SEND E-MAIL NOTIFICATION TO SELECTED MGR/REVIEWER
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = $send_to;
			$subject = 'SIMS RESUMES & APPLICATIONS: APPLICANT SUBMITTED FOR YOUR REVIEW';
			$message = 
			'Dear Reviewer,'."\n\n".
			
			$doc_name.' has been reviewed by '.$_SESSION['user_ID'].' and submitted for your consideration.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Applicant Name: '.$_GET['applicant_name']."\n".
			'Position: '.$position."\n".
			'Action: '.$_GET['mgr_action'].' ('.$send_to_IDs.') '.$newpriority."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To review this document, click here:'."\n".
			
			'http://www.sedl.org/staff/sims/positions_novs_ba.php?v='.$view.'&id='.$resume_ID."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################
		
		}else{ // SEND E-MAIL NOTIFICATION TO HR REPRESENTATIVE
		//$to2 = 'mturner@sedl.org'; // E-MAIL FOR HR REPRESENTATIVE
			if(($priority_assigned1 !== '1')&&($priority_assigned2 !== '1')){ // UNLESS PRIORITY WAS ASSIGNED IN WHICH CASE DON'T SEND HR AN E-MAIL
			#################################################
			## START: SEND E-MAIL NOTIFICATION TO HR ##
			#################################################
			if($_GET['mgr_action'] == 'Resume reviewed - request application form'){
			$cc = 'sue.liberty@sedl.org'; 
			}
			
			$to = $created_by.'@sedl.org'; // E-MAIL FOR HR REPRESENTATIVE
			$subject = 'SIMS RESUMES & APPLICATIONS: REVIEWER RESPONSE RECEIVED';
			$message = 
			'HR,'."\n\n".
			
			'A reviewer response was received for the applicant '.$_GET['applicant_name'].' (ID: '.$resume_ID.').'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Reviewer: '.$_SESSION['user_ID']."\n".
			'Applicant Name: '.$_GET['applicant_name']."\n".
			'Position: '.$position."\n".
			'Action: '.$_GET['mgr_action'].' '.$newpriority."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To process this response, log on to the SIMS: Resumes and Applications database at the following link:'."\n".
			
			'fmp7://198.214.140.248/admin_base.fp7'."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org'."\r\n".'Cc: '.$cc;
			
			mail($to, $subject, $message, $headers);
			#################################################
			## END: SEND E-MAIL NOTIFICATION TO HR ##
			#################################################
			}
		}

}else{ // THERE WAS A PROBLEM SAVING USER RESPONSE TO THE ACTION LOG
$_SESSION['action_logged'] = '2'; 
}
###############################################
## END: ADD THE REVIEWER RESPONSE ##
###############################################
//echo '$_GET[action_target]: '.$_GET['action_target'];
//exit;
header($location);
exit;
}

#################################################################
## START: FIND THIS JOB APPLICATION ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('admin_base.fp7','resume_table');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('resume_ID','=='.$resume_ID);
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam('leave_hrs_date','ascend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
$row_ID = $recordData['c_row_ID'][0];
$created_by = $recordData['created_by'][0];

$reject1 = 'at least '.$recordData['doc_position_app_pool_count'][0].' other applicants indicate more extensive relevant education, skills, and/or experience, and/or present stronger writing sample';
$reject2 = 'at least '.$recordData['doc_position_app_pool_count2'][0].' other applicants indicate more extensive relevant education, skills, and/or experience, and/or present stronger writing sample';


//if((($recordData['PR_pre_approval_required_IT'][0] == '1')&&($recordData['sign_status_IT'][0] != '1'))||(($recordData['PR_pre_approval_required_IRC'][0] == '1')&&($recordData['sign_status_IRC'][0] != '1'))){
//$preappRequired = '1';
//}else{
//$preappRequired = '0';
//}
###############################################################
## END: FIND THIS JOB APPLICATION ##
###############################################################

#################################################################
## START: FIND OTHER RESUMES OR APPS RELATED TO THIS APPLICANT ##
#################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('admin_base.fp7','resume_table');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('related_records_matchkey',$resume_ID);
//$search2 -> AddDBParam('-lop','or');

//$search2 -> AddSortParam('leave_hrs_date','ascend');


$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
//print_r ($searchResult2);
$recordData2 = current($searchResult2['data']);
###############################################################
## END: FIND OTHER RESUMES OR APPS RELATED TO THIS APPLICANT ##
###############################################################

if($recordData['c_attachment_count'][0] > 0){ // THE SELECTED APP HAS FILE ATTACHMENTS
#################################################################
## START: FIND ATTACHMENTS RELATED TO THIS APPLICANT ##
#################################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('admin_base.fp7','files_attachments');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('resume_ID','=='.$resume_ID);
//$search3 -> AddDBParam('-lop','or');

//$search3 -> AddSortParam('leave_hrs_date','ascend');


$searchResult3 = $search3 -> FMFind();

//echo '<p>$searchResult3[errorCode]: '.$searchResult3['errorCode'];
//echo '<p>$searchResult3[foundCount]: '.$searchResult3['foundCount'];
//print_r ($searchResult3);
$recordData3 = current($searchResult3['data']);
###############################################################
## END: FIND ATTACHMENTS RELATED TO THIS APPLICANT ##
###############################################################
}


if($recordData['c_action_log_count'][0] > 0){ // THE SELECTED APP HAS USER LOG ENTRIES
#################################################################
## START: FIND USER LOG ENTRIES RELATED TO THIS PR ##
#################################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('admin_base.fp7','resumes_action_log');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('resume_ID','=='.$resume_ID);
//$search4 -> AddDBParam('-lop','or');

$search4 -> AddSortParam('creation_timestamp','ascend');


$searchResult4 = $search4 -> FMFind();

//echo '<p>$searchResult4[errorCode]: '.$searchResult4['errorCode'];
//echo '<p>$searchResult4[foundCount]: '.$searchResult4['foundCount'];
//print_r ($searchResult4);
$recordData4 = current($searchResult4['data']);
###############################################################
## END: FIND USER LOG ENTRIES RELATED TO THIS APP ##
###############################################################
}

$user = $_SESSION['user_ID'];
//echo '<span style="color:#999999">User: '.$user.'</span>';
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Approve JOB APPLICATIONs</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function checkFields() { 
	
		if (document.form2.mgr_action.value ==""){
			alert("Please select the reviewer action.");
			document.form2.mgr_action.focus();
			return false;	}

		if ((document.form2.mgr_action.value =="Send for further review to... (see comments)")&&(document.form2.action_target.value == "")){
			alert("Please select the reviewer to receive this notification.");
			document.form2.action_target.focus();
			return false;	}

}

</script>



<style type="text/css">

body {
	background-color: #DBE8F9;
	font: 11px/24px "Lucida Grande", "Trebuchet MS", Arial, Helvetica, sans-serif;
	//color: #5A698B;
}

#title {
	width: 330px;
	height: 26px;
	color: #5A698B;
	font: bold 12px/18px "Lucida Grande", "Trebuchet MS", Arial, Helvetica, sans-serif;
	padding-top: 5px;
	text-transform: uppercase;
	letter-spacing: 2px;
	text-align: center;
}

form {
	width: 335px;
}

.col1 {
	text-align: right;
	width: 135px;
	height: 31px;
	margin: 0;
	float: left;
	margin-right: 2px;
}

.col2 {
	width: 195px;
	height: 31px;
	display: block;
	float: left;
	margin: 0;
}

.col2comment {
	width: 195px;
	height: 98px;
	margin: 0;
	display: block;
	float: left;
}

.col1comment {
	text-align: right;
	width: 135px;
	height: 98px;
	float: left;
	display: block;
	margin-right: 2px;
}

div.row {
	clear: both;
	width: 335px;
}


.input {
	background-color: #fff;
	font: 11px/14px "Lucida Grande", "Trebuchet MS", Arial, Helvetica, sans-serif;
	color: #5A698B;
	margin: 4px 0 5px 8px;
	padding: 1px;
	border: 1px solid #8595B2;
}

.textarea {
	border: 1px solid #8595B2;
	background-color: #fff;
	font: 11px/14px "Lucida Grande", "Trebuchet MS", Arial, Helvetica, sans-serif;
	color: #5A698B;
	margin: 4px 0 5px 8px;
}

</style>

<script language="JavaScript">

		function confirmDelete() { 
			var answer2 = confirm ("Delete this process log item now?")
			if (!answer2) {
			return false;
			}
		}
		

		
		function UpdateSelect()
		{
		select_value = "";
		select_value = document.form2.mgr_action.value;
		var id = 'specify_target';
		var obj = '';
		obj = (document.getElementById) ? document.getElementById(id) : ((document.all) ? document.all[id] : ((document.layers) ? document.layers[id] : false));
		
		select_value2 = "";
		select_value2 = document.form2.mgr_action.value;
		var id2 = 'specify_priority1';
		var obj2 = '';
		obj2 = (document.getElementById) ? document.getElementById(id2) : ((document.all) ? document.all[id2] : ((document.layers) ? document.layers[id2] : false));

		select_value3 = "";
		select_value3 = document.form2.mgr_action.value;
		var id3 = 'specify_priority2';
		var obj3 = '';
		obj3 = (document.getElementById) ? document.getElementById(id3) : ((document.all) ? document.all[id3] : ((document.layers) ? document.layers[id3] : false));

		select_value4 = "";
		select_value4 = document.form2.mgr_action.value;
		var id4 = 'reject_reason1';
		var obj4 = '';
		obj4 = (document.getElementById) ? document.getElementById(id4) : ((document.all) ? document.all[id4] : ((document.layers) ? document.layers[id4] : false));

		select_value5 = "";
		select_value5 = document.form2.mgr_action.value;
		var id5 = 'reject_reason2';
		var obj5 = '';
		obj5 = (document.getElementById) ? document.getElementById(id5) : ((document.all) ? document.all[id5] : ((document.layers) ? document.layers[id5] : false));

		select_value6 = "";
		select_value6 = document.form2.doc_screening_2b_reason.value;
		var id6 = 'reject_reason2b';
		var obj6 = '';
		obj6 = (document.getElementById) ? document.getElementById(id6) : ((document.all) ? document.all[id6] : ((document.layers) ? document.layers[id6] : false));


			if(select_value == ""){
			  obj.style.display = 'none';
			  //alert("onBodyLoad.");
			
			} else if((select_value == "Send for further review")||(select_value == "Provide feedback to hiring manager")){
			  // alert("You chose Journal article.");
			  // return false;
			  obj.style.display = 'block';
			}
			else
			{
			  obj.style.display = 'none';
			}
		
			if(select_value2 == ""){
			  obj2.style.display = 'none';
			  //alert("onBodyLoad.");
			
			} else if(select_value2 == "Resume reviewed - has potential, assign priority"){
			  // alert("You chose Journal article.");
			  // return false;
			  obj2.style.display = 'block';
			}
			else
			{
			  obj2.style.display = 'none';
			}

			if(select_value3 == ""){
			  obj3.style.display = 'none';
			  //alert("onBodyLoad.");
			
			} else if(select_value3 == "Application form reviewed - has potential, assign priority"){
			  // alert("You chose Journal article.");
			  // return false;
			  obj3.style.display = 'block';
			}
			else
			{
			  obj3.style.display = 'none';
			}

			if(select_value4 == ""){
			  obj4.style.display = 'none';
			  //alert("onBodyLoad.");
			
			} else if(select_value4 == "Resume reviewed - no to applicant"){
			  // alert("You chose Journal article.");
			  // return false;
			  obj4.style.display = 'block';
			}
			else
			{
			  obj4.style.display = 'none';
			}

			if(select_value5 == ""){
			  obj5.style.display = 'none';
			  //alert("onBodyLoad.");
			
			} else if(select_value5 == "Application form reviewed - no to applicant"){
			  // alert("You chose Journal article.");
			  // return false;
			  obj5.style.display = 'block';
			}
			else
			{
			  obj5.style.display = 'none';
			}

			if(select_value6 == ""){
			  obj6.style.display = 'none';
			  //alert("onBodyLoad.");
			
			} else if(select_value6 == "<?php echo $reject2;?>"){
			  // alert("You chose Journal article.");
			  // return false;
			  obj6.style.display = 'block';
			}
			else
			{
			  obj6.style.display = 'none';
			}

		}



</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="UpdateSelect();">

<table cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="900px">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Resumes & Applications: Reviewer Admin</h1><hr /></td></tr>
			
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Reviewer: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - To process this document, submit your action and comments below. | <a href="/staff/sims/menu_positions_novs.php?action=<?php echo $view;?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $position_ID;?>#<?php echo $recordData['resume_ID'][0];?>">Close Document</a></p>
			</td></tr>
			
			
			
			<tr><td colspan="2">
			
						<table cellpadding="10" cellspacing="0" border="0" bordercolor="ebebeb" bgcolor="ffffff" width="100%">
<?php if($view == 'urs'){ ?>
						<tr bgcolor="#a2c7ca"><td class="body"><strong>Unsolicited Resume: (<?php echo $recordData['resume_ID'][0];?>)</strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Resume Status: <strong><?php if($recordData['status'][0] == 'Pending'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['status'][0]).'</span>';?></strong></span></td></tr>
<?php }else{ ?>
						<tr bgcolor="#a2c7ca"><td class="body"><strong>NOV Position: <?php echo $recordData['positions_NOVs::c_position_display'][0].' ('.$recordData['resume_ID'][0].')';?></strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Application Status: <strong><?php if($recordData['nov_status'][0] == 'Pending'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['nov_status'][0]).'</span>';?></strong></span></td></tr>
<?php } ?>
						<tr><td class="body" nowrap><strong>APPLICATION</strong></td><td align="right">ID: <?php echo $recordData['resume_ID'][0];?></td></tr>

						<tr><td colspan="2" class="body">
						

							<table cellpadding="7" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
							
								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>APPLICANT DETAILS</strong></div>
								
									<table style="border:0px dotted #000000;width:100%;margin-top:6px">
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Name</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['resume_first_name'][0].' '.$recordData['resume_last_name'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Address</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['address'][0];?><br><?php echo $recordData['resume_city'][0];?>, <?php echo $recordData['resume_state'][0];?> <?php echo $recordData['zip'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Phone</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['phone'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">E-mail</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><a href="mailto:<?php echo $recordData['email'][0];?>"><?php echo $recordData['email'][0];?></a></td></tr>
									</table>

								
								</td>

								<td rowspan="3" class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>APPLICANT FILES (<?php echo $searchResult3['foundCount'];?>)</strong></div>
								
								<?php if($searchResult3['foundCount'] > 0){ ?>
									<ol style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px;width:95%;list-style-position: inside;">
									
									<?php foreach($searchResult3['data'] as $key => $searchData3) { // LIST ATTACHMENTS ?>
										
										<li style="padding:5px">
										
										<strong><?php echo ucwords($searchData3['attachment_type'][0]);?></strong><br>
										File: <a href="http://198.214.141.190/sims/attachments/<?php echo $searchData3['attachment_filename'][0];?>" target="_blank" title="Click to download this file for review."><?php echo $searchData3['attachment_filename'][0];?></a><br>

										<div class="tiny" style="padding:4px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb;margin: 6px 2px 0px 2px">
										Uploaded: <?php echo $searchData3['upload_timestamp'][0];?> by <?php echo $searchData3['uploaded_by'][0];?>
										<?php if($searchData3['attachment_notes'][0] !== ''){?><br>Comments: <?php echo $searchData3['attachment_notes'][0];?><br><?php }?>
										</div>

									
										</li>

										<hr style="border:1px dotted #000000">
									<?php  } ?></ol>
	
								<?php }else{ ?>
								
								N/A
								<?php } ?>

								</td>



								</tr>

								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>HR COMMENTS</strong></div><br><?php if($recordData['comments'][0] != ''){echo $recordData['comments'][0];}else{echo 'N/A';}?></td></tr>

								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>OTHER RESUMES/APPLICATIONS (<?php echo $searchResult2['foundCount'];?>)</strong></div><br>
									<?php if($searchResult2['foundCount'] > 0){ ?>
						
										<table style="border:0px dotted #000000;margin-top:6px">
										<tr bgcolor="#ebebeb">
										
										<td nowrap style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">ID</td>
										<td nowrap style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Date</td>
										<td nowrap style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Type</td>
										<td nowrap style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Reviewed by</td>
										<td nowrap style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Status</td>

										</tr>
		

										
										<?php foreach($searchResult2['data'] as $key => $searchData) { ?>			
												<tr class="body">
												
												
												<td style="border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:top" nowrap><a href="positions_novs_ba.php?id=<?php echo $searchData['resume_ID'][0];?>" target="_blank"><?php echo $searchData['resume_ID'][0];?></a></td>
												<td style="border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:top" nowrap><?php echo $searchData['creation_timestamp'][0];?></td>
												<td style="border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:top" nowrap><?php echo $searchData['record_type'][0];?></td>
												<td style="border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:top" nowrap><?php if($searchData['c_nov_mgrs_sent_to_search_target'][0] == ''){echo 'HR';}else{echo $searchData['c_nov_mgrs_sent_to_search_target'][0];}?></td>
												<td style="border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:top" nowrap><?php echo $searchData['c_status_display'][0];?></td>
												
												
												</tr>
											
									
										<?php } ?>

										</table>

								<?php }else{ ?>
								
								N/A
								<?php } ?>

								
								</td></tr>


								<tr><td  colspan="2" style="vertical-align:text-top" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>PROCESS LOG</strong></div>



								
									<table width="100%" style="margin-top:6px;width:100%">

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf"><strong>DATE</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>USER</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>ACTION</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>SENT TO</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap width="100%"><strong>COMMENTS</strong></td></tr>

									<?php if($searchResult4['foundCount'] > 0){ // SHOW APP ACTION LOG ?>
									<?php foreach($searchResult4['data'] as $key => $searchData4) {  ?>

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['creation_timestamp'][0];?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['user'][0];?><?php if($_SESSION['user_ID'] == $searchData4['user'][0]){?><br><a href="positions_novs_ba.php?id=<?php echo $recordData['resume_ID'][0];?>&logx=1&logid=<?php echo $searchData4['c_row_ID'][0];?>" onclick="javascript:return confirm('Are you sure you want to delete this log entry?')">Delete</a><?php }?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['action'][0];?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px"><?php echo $searchData4['action_target'][0];?></td><td class="tiny" width="100%" style="vertical-align:text-top;border:1px dotted #000000;padding:3px"><?php if($searchData4['comment'][0] != ''){?><?php echo $searchData4['comment'][0];?><?php }else{ echo 'N/A';}?></td></tr>
									
									<?php } ?>
									<?php } ?>

										<tr><td style="background-color:#ebebeb;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap colspan="5">
										
											<form id="form2" name="form2" onsubmit="return checkFields()">
											<input type="hidden" name="reviewer_submit" value="1">
											<input type="hidden" name="id" value="<?php echo $recordData['resume_ID'][0];?>">
											<input type="hidden" name="applicant_name" value="<?php echo $recordData['resume_first_name'][0].' '.$recordData['resume_last_name'][0];?>">
											<input type="hidden" name="record_type" value="<?php if($recordData['record_type'][0] == 'Response to NOV'){ echo '2';}else{echo '1';}?>">
											<input type="hidden" name="position" value="<?php echo $recordData['positions_NOVs::c_position_display'][0].' ('.$recordData['resume_ID'][0].')';?>">
											<input type="hidden" name="sortfield" value="<?php echo $sortfield;?>">
											<input type="hidden" name="sortorder" value="<?php echo $sortorder;?>">
											<input type="hidden" name="displaynum" value="<?php echo $displaynum;?>">
											<input type="hidden" name="pos_id" value="<?php echo $position_ID;?>">
											

											<table  style="border:1px dotted #000000;background-color:#b7e4fc;padding:6px">
											<tr><td id="title" colspan="2" style="border:0px;padding:6px">Reviewer Action</td></tr>
											<tr><td style="border:0px;padding:6px;vertical-align:top">
											
											
											
											<label class="col1"><strong>Reviewer:</strong>&nbsp;&nbsp;</label></td><td class="col2" style="border:1px dotted;background-color:#ffffff;padding:6px;vertical-align:top"><?php echo $_SESSION['user_ID'];?></td></tr>
											<tr><td style="border:0px;padding:6px;vertical-align:top"><label class="col1"><strong>Action:</strong>&nbsp;&nbsp;</label></td><td style="border:1px dotted;background-color:#ffffff;padding:6px;vertical-align:top">



<?php if($view == 'urs'){ //USE SELECT LIST FOR UNSOLICITED RESUMES ?>	
											<input type="hidden" name="v" value="urs">

											<select name="mgr_action" onChange="UpdateSelect();">
											<option value=""></option>
											<option value="Not a match">Not a match</option>
											<option value="No match at this time">No match at this time</option>
											<option value="Develop relationship">Develop relationship</option>
											<option value="">-</option>
											<option value="Other (see comments)">Other (see comments)</option>
											<option value="">-</option>
											<option value="Send for further review to... (see comments)">Send for further review to... (see comments)</option>
											</select><br>&nbsp;<br>

<?php }else{ //USE SELECT LIST FOR NOV APPLICATIONS ?>

											<select name="mgr_action" onChange="UpdateSelect();">
											<option value=""></option>
											<option value="">------------------------------</option>
											<option value="">&nbsp;&nbsp;MANAGER ACTIONS</option>
											<option value="">------------------------------</option>
											<option value="">AFTER REVIEWING RESUME...</option>
											<option value="Resume reviewed - no to applicant">&nbsp;(1) No to applicant - no application requested</option>
											<option value="Resume reviewed - has potential, assign priority">&nbsp;(2) Has potential - assign priority >></option>
											<option value="Resume reviewed - request application form">&nbsp;(3) Request application form from applicant</option>
											<option value="">-</option>
											<option value="">AFTER REVIEWING APPLICATION...</option>
											<option value="Application form reviewed - no to applicant">&nbsp;(1) No to applicant - no interview requested</option>
											<option value="Application form reviewed - has potential, assign priority">&nbsp;(2) Has potential - assign priority >></option>
											<option value="Application form reviewed - schedule interview">&nbsp;(3) Schedule interview</option>
											<option value="">-</option>
											<option value="">AFTER INTERVIEWING...</option>
											<option value="Interview conducted - no to applicant">&nbsp;(1) No to applicant - send letter</option>
											<option value="Interview conducted - pending">&nbsp;(2) No to applicant - keep pending</option>
											<option value="Interview conducted - offer to employ">&nbsp;(3) Offer to employ</option>
											<option value=""></option>
											<option value="">------------------------------</option>
											<option value="">&nbsp;&nbsp;INTERVIEW TEAM ACTIONS</option>
											<option value="">------------------------------</option>
											<option value="Provide feedback to hiring manager">&nbsp;(1) Provide feedback to hiring manager >></option>
											<option value=""></option>
											<option value="">------------------------------</option>
											<option value="">&nbsp;&nbsp;MORE OPTIONS</option>
											<option value="">------------------------------</option>
											<option value="Other (see comments)">&nbsp;(1) Other (see comments)</option>
											<option value="Send for further review">&nbsp;(2) Send for further review to >></option>
											</select><br>&nbsp;<br>

<?php } ?>
												<div id="specify_target" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
												Select reviewer(s)/manager(s): <br>&nbsp;<br>
												
							
													
													<?php foreach($v1Result['valueLists']['forward_to_list'] as $key => $value) { ?>
													<input type="checkbox" name="action_target[]" value="<?php echo $value;?>"> <?php echo $value; ?></input><br>
													<?php } ?>
													<hr>
													<span class="tiny">Contact <a href="mailto:sims@sedl.org">sims@sedl.org</a> to add a reviewer to this list.</span>
												</div>

												<div id="specify_priority1" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
												Assign priority (post-resume): 
												
							
													<select name="nov_priority_rating_post_resume"  onChange="UpdateSelect();">
													<option value=""></option>
													<option value="High">&nbsp;High</option>
													<option value="Medium">&nbsp;Medium</option>
													<option value="Low">&nbsp;Low</option>
													</select>
															
												</div>

												<div id="specify_priority2" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
												Assign priority (post-application): 
												
							
													<select name="nov_priority_rating_post_application"  onChange="UpdateSelect();">
													<option value=""></option>
													<option value="High">&nbsp;High</option>
													<option value="Medium">&nbsp;Medium</option>
													<option value="Low">&nbsp;Low</option>
													</select>
															
												</div>

												<div id="reject_reason1" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
												Reason for Rejection: 
												
							
													<select name="doc_screening_1b_reason"  onChange="UpdateSelect();">
													<option value="">&nbsp;</option>
													<option value="applicant does not meet minimum specified education">&nbsp;applicant does not meet minimum specified education</option>
													<option value="applicant does not meet minimum specified experience">&nbsp;applicant does not meet minimum specified experience</option>
													<option value="writing sample does not meet minimum standards for quality and/or relevance">&nbsp;writing sample does not meet minimum standards for quality and/or relevance</option>
													<option value="at least <?php echo $recordData['positions_NOVs::position_app_pool_count'][0];?> other applicants indicate more extensive relevant education and/or experience">&nbsp;at least (<?php echo $recordData['positions_NOVs::position_app_pool_count'][0];?>) other applicants indicate more extensive relevant education and/or experience</option>
													</select>
															
												</div>

												<div id="reject_reason2" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
												Reason for Rejection: 
												
							
													<select name="doc_screening_2b_reason"  onChange="UpdateSelect();">
													<option value="">&nbsp;</option>
													<option value="applicant does not meet minimum specified education (based on additional information in the application form)">&nbsp;applicant does not meet minimum specified education (based on additional information in the application form)</option>
													<option value="applicant does not meet minimum specified experience (based on additional information in the application form)">&nbsp;applicant does not meet minimum specified experience (based on additional information in the application form)</option>
													<option value="at least <?php echo $recordData['positions_NOVs::position_app_pool_count2'][0];?> other applicants indicate more extensive relevant education, skills, and/or experience, and/or present stronger writing sample">&nbsp;at least (<?php echo $recordData['positions_NOVs::position_app_pool_count2'][0];?>) other applicants indicate more extensive relevant education, skills, and/or experience, and/or present stronger writing sample</option>
													</select>
															
												</div>

												<div id="reject_reason2b" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
												Check which items apply to the above reason for rejection: <p>
													
													<ul style="list-style-type: none;list-style-position: outside">
													<li><input type="checkbox" name="doc_screening_2b_reason_other_educ" value="yes"> Others indicate more extensive relevant education</input><br>
													<li><input type="checkbox" name="doc_screening_2b_reason_other_exp" value="yes"> Others indicate more extensive relevant experience</input><br>
													<li><input type="checkbox" name="doc_screening_2b_reason_other_skills" value="yes"> Others indicate more extensive relevant skills</input><br>
													<li><input type="checkbox" name="doc_screening_2b_reason_other_writing" value="yes"> Others present a significantly stronger writing sample</input><br>
													</ul>		
												
												</div>

											</td></tr>
											<tr><td style="border:0px;padding:6px;vertical-align:top"><label class="col1comment"><strong>Comments:</strong>&nbsp;&nbsp;</label></td><td style="border:1px dotted;background-color:#ffffff;padding:6px;vertical-align:top"><textarea cols="40" class="textarea" rows="4" name="comments" id="comment" tabindex="4" ></textarea></td></tr>
											<tr><td colspan="2" style="text-align:right;border:0px;padding:6px"><input type="submit" value="Submit" />
											</td></tr>
											</table>


										</form>





									
										
										
										</td></tr>

									</table>
								</td>
								</tr>
								
								
		
		
		
		
													
		
		
									</table>
								
								
								</td></tr>
								
								
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
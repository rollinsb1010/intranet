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
$dev_reviewer = $_GET['dev_reviewer'];
//$send_to_all = $_GET['send_to_all'];
//$position_ID = $_GET['pos_id'];


$view = $_GET['v'];

if($action == ''){ 
$action = 'view';
}

$project_ID = $_GET['id'];
$reviewer_submit = $_GET['reviewer_submit'];
$logx = $_GET['logx'];
$logid = $_GET['logid'];
//$status = $_GET['status'];


$location = 'Location: http://www.sedl.org/staff/sims/menu_dev_projects.php?pos_id='.$project_ID.'&sortfield='.$sortfield.'&sortorder='.$sortorder.'&displaynum='.$displaynum.'#'.$recordData['project_ID'][0];
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
$v1 -> SetDBData('dev_base.fp7','projects_action_log');
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
$delete -> SetDBData('dev_base.fp7','projects_action_log');
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
$newrecord -> SetDBData('dev_base.fp7','projects_action_log'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information

$newrecord -> AddDBParam('project_ID',$project_ID);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action',$_GET['mgr_action']);

if(($_GET['action_target'] !== '')&&(($_GET['mgr_action'] == 'Send for further review')||($_GET['mgr_action'] == 'Send response to comments'))){

	for($i=0 ; $i<count($_GET['action_target']) ; $i++) {
	$action_target .= $_GET['action_target'][$i]."\r"; 
	}

$newrecord -> AddDBParam('action_target',$action_target);

}elseif($_GET['mgr_action'] == 'Interested'){
$newrecord -> AddDBParam('action_target',$dev_reviewer);
}elseif($_GET['mgr_action'] == 'Not interested'){
$newrecord -> AddDBParam('action_target','mboethel');
}elseif($_GET['mgr_action'] == 'Will respond later'){
$newrecord -> AddDBParam('action_target','DEV');
$newrecord -> AddDBParam('comments','[Manager unable to respond now -- will respond by ->'.$_GET['will_respond_date_m'].'/'.$_GET['will_respond_date_d'].'/'.$_GET['will_respond_date_y'].'] '.$_GET['comments']);
}elseif($_GET['mgr_action'] == 'No go-DEV'){
$newrecord -> AddDBParam('action_target','DEV');
}elseif($_GET['mgr_action'] == 'Recommend pursuing'){
$newrecord -> AddDBParam('action_target','sferguso');
}elseif($_GET['mgr_action'] == 'Pursue-ACTIVE'){
$newrecord -> AddDBParam('action_target','DEV');
}elseif($_GET['mgr_action'] == 'No go-AS'){
$newrecord -> AddDBParam('action_target','DEV');
}elseif($_GET['mgr_action'] == 'Contingent OK to pursue'){
$newrecord -> AddDBParam('action_target','mboethel'."\r".'whoover');
}elseif($_GET['mgr_action'] == 'OK to pursue'){
$newrecord -> AddDBParam('action_target','whoover');
}elseif($_GET['mgr_action'] == 'No go-CEO'){
$newrecord -> AddDBParam('action_target','DEV');
}elseif($_GET['mgr_action'] == 'Approval to pursue'){
$newrecord -> AddDBParam('action_target','DEV');
}else{
$newrecord -> AddDBParam('action_target','DEV');
}

if(($_GET['reject_reason_mgr'] !== '')&&($_GET['mgr_action'] == 'Not interested')){

	for($i=0 ; $i<count($_GET['reject_reason_mgr']) ; $i++) {
	$reject_reason_mgr .= $_GET['reject_reason_mgr'][$i].", "; 
	}

$newrecord -> AddDBParam('comments','[REASON->'.$reject_reason_mgr.'] '.$_GET['comments']);
$proj_rejected1 = '1';
}

if(($_GET['reject_reason_dev'] !== '')&&($_GET['mgr_action'] == 'No go-DEV')){

	for($i=0 ; $i<count($_GET['reject_reason_dev']) ; $i++) {
	$reject_reason_dev .= $_GET['reject_reason_dev'][$i].", "; 
	}

$newrecord -> AddDBParam('comments','[REASON->'.$reject_reason_dev.'] '.$_GET['comments']);
$proj_rejected2 = '1';
}

if(($_GET['reject_reason_as'] !== '')&&($_GET['mgr_action'] == 'No go-AS')){

	for($i=0 ; $i<count($_GET['reject_reason_as']) ; $i++) {
	$reject_reason_as .= $_GET['reject_reason_as'][$i].", "; 
	}

$newrecord -> AddDBParam('comments','[REASON->'.$reject_reason_as.'] '.$_GET['comments']);
$proj_rejected3 = '1';
}

if(($_GET['reject_reason_ceo'] !== '')&&($_GET['mgr_action'] == 'No go-CEO')){

	for($i=0 ; $i<count($_GET['reject_reason_ceo']) ; $i++) {
	$reject_reason_ceo .= $_GET['reject_reason_ceo'][$i].", "; 
	}

$newrecord -> AddDBParam('comments','[REASON->'.$reject_reason_ceo.'] '.$_GET['comments']);
$proj_rejected4 = '1';
}

if(($proj_rejected1 !== '1')&&($proj_rejected2 !== '1')&&($proj_rejected3 !== '1')&&($proj_rejected4 !== '1')&&($_GET['mgr_action'] !== 'Will respond later')){
$newrecord -> AddDBParam('comments',$_GET['comments']);
}


$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

$newrecordData = current($newrecordResult['data']);

$reviewer_action_ID = $newrecordData['record_ID'][0];
//$doc_type = $newrecordData['record_type'][0];
$send_to = $newrecordData['c_target_email'][0];
$send_to_all = $newrecordData['projects::c_target_email_list'][0];
//echo '<p>$send_to_all: '.$send_to_all;
//$send_to_IDs = $newrecordData['c_target_IDs'][0];
$current_id = $newrecordData['projects::c_row_ID'][0];
$created_by = $newrecordData['projects::created_by'][0];
$dev_reviewer = $newrecordData['projects::dev_reviewer'][0];
$comments = $newrecordData['comments'][0];
//echo '$created_by: '.$created_by;
//echo '$send_to: '.$send_to;
//exit;
//if($doc_type == '1'){
###########################################################################################################
## START: REVIEWER RESPONSE RECEIVED, UPDATE THE PROJECT RECORD ##
###########################################################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('dev_base.fp7','projects_detail');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('mgr_response_received','yes');
$update -> AddDBParam('mgr_response_email',$send_to);

$updateResult = $update -> FMEdit();
//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
###########################################################################################################
## END: REVIEWER RESPONSE RECEIVED, UPDATE THE PROJECT RECORD ##
###########################################################################################################


###########################################################################################################
## START: PROJECT REJECTED (NO-GO), UPDATE THE PROJECT RECORD ##
###########################################################################################################
if(($_GET['mgr_action'] == 'No go-DEV')||($_GET['mgr_action'] == 'No go-AS')||($_GET['mgr_action'] == 'No go-CEO')){

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('dev_base.fp7','projects_detail');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('status','No go');

$updateResult = $update -> FMEdit();
//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];

$updaterecordData = current($updateResult['data']);

$send_to_all = $updaterecordData['c_target_email_list'][0];
}
###########################################################################################################
## END: PROJECT REJECTED (NO-GO), UPDATE THE PROJECT RECORD ##
###########################################################################################################

###########################################################################################################
## START: PROJECT UNDER CONSIDERATION (INTERESTED), UPDATE THE PROJECT RECORD ##
###########################################################################################################
if(($_GET['mgr_action'] == 'Interested')||($_GET['mgr_action'] == 'Recommend pursuing')||($_GET['mgr_action'] == 'Contingent OK to pursue')){

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('dev_base.fp7','projects_detail');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('status','Under consideration');

$updateResult = $update -> FMEdit();
//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];

}
###########################################################################################################
## END: PROJECT UNDER CONSIDERATION (INTERESTED), UPDATE THE PROJECT RECORD ##
###########################################################################################################

###########################################################################################################
## START: PROJECT UNDER DEVELOPMENT (PURSUE), UPDATE THE PROJECT RECORD ##
###########################################################################################################
if($_GET['mgr_action'] == 'Approval to pursue'){

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('dev_base.fp7','projects_detail');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('status','Under development');

$updateResult = $update -> FMEdit();
//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];

$updaterecordData = current($updateResult['data']);

$send_to_all = $updaterecordData['c_target_email_list'][0];
}
###########################################################################################################
## END: PROJECT UNDER DEVELOPMENT (PURSUE), UPDATE THE PROJECT RECORD ##
###########################################################################################################

if($newrecordResult['errorCode'] == '0'){ // USER RESPONSE WAS SUCCESSFULLY SAVED TO THE ACTION LOG
$_SESSION['action_logged'] = '1';


	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action',$_GET['mgr_action']);
	$newrecord -> AddDBParam('table','projects_action_log');
	$newrecord -> AddDBParam('object_ID',$newrecordData['record_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$newrecordData['record_ID'][0]);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];



		if(($send_to !== '')&&($_GET['mgr_action'] == 'Send response to comments')){ // SEND E-MAIL NOTIFICATION TO SELECTED MGR/REVIEWER
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = $send_to;
			$subject = 'DEV PROJECT PLANNING: RESPONSE RECEIVED TO YOUR COMMENTS';
			$message = 
			'Dear Reviewer,'."\n\n".
			
			'A DEV project (funding opportunity) has been reviewed by '.$_SESSION['user_ID'].' and a response has been submitted to your comments.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To review and provide feedback on this funding opportunity, click here:'."\n".
			
			'http://www.sedl.org/staff/sims/dev_projects.php?id='.$project_ID."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################
		
		}elseif(($send_to !== '')&&($_GET['mgr_action'] == 'Send for further review')){ // SEND E-MAIL NOTIFICATION TO SELECTED MGR/REVIEWER
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = $send_to;
			$subject = 'DEV PROJECT PLANNING: PROJECT FORWARDED FOR YOUR REVIEW';
			$message = 
			'Dear Reviewer,'."\n\n".
			
			'A DEV project (funding opportunity) has been reviewed by '.$_SESSION['user_ID'].' and submitted for your consideration.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To review and provide feedback on this funding opportunity, click here:'."\n".
			
			'http://www.sedl.org/staff/sims/dev_projects.php?id='.$project_ID."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################
		
		}elseif($_GET['mgr_action'] == 'Interested'){ // SEND E-MAIL NOTIFICATION TO DEV REVIEWER
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = $dev_reviewer.'@sedl.org';
			$subject = 'DEV PROJECT PLANNING: MANAGER INTERESTED IN PROJECT';
			$message = 
			'DEV,'."\n\n".
			
			'A SEDL manager ('.$_SESSION['user_ID'].') has expressed interest in pursuing a DEV project (funding opportunity) and submitted comments for your consideration.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To process this request, click here:'."\n".
			
			'http://www.sedl.org/staff/sims/dev_projects.php?id='.$project_ID."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################

		}elseif($_GET['mgr_action'] == 'Not interested'){ // SEND E-MAIL NOTIFICATION TO DEV DIRECTOR
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = 'mboethel@sedl.org,'.$dev_reviewer.'@sedl.org';
			$subject = 'DEV PROJECT PLANNING: MANAGER NOT INTERESTED IN PROJECT';
			$message = 
			'DEV,'."\n\n".
			
			'A SEDL manager ('.$_SESSION['user_ID'].') has declined interest in pursuing a DEV project (funding opportunity) and submitted comments for your consideration.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$comments."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To process this request, click here:'."\n".
			
			'http://www.sedl.org/staff/sims/dev_projects.php?id='.$project_ID."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################

		}elseif($_GET['mgr_action'] == 'No go-DEV'){ // SEND E-MAIL NOTIFICATION TO ALL REVIEWERS
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = $send_to_all.','.$dev_reviewer.'@sedl.org';
			$subject = 'DEV PROJECT PLANNING: PROJECT REJECTED';
			$message = 
			'DEV Funding Opportunity Reviewer,'."\n\n".
			
			'DEV ('.$_SESSION['user_ID'].') has rejected (No go) a DEV project (funding opportunity) that you previously reviewed.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$comments."\n".
			'------------------------------------------------------------'."\n\n".
	
			'For more information about this decision, contact ('.$_SESSION['user_ID'].')'."\n\n".
			
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################

		}elseif($_GET['mgr_action'] == 'Recommend pursuing'){ // SEND E-MAIL NOTIFICATION TO AS/FISCAL REVIEWER (sferguso@sedl.org)
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = 'sferguso@sedl.org';
			$subject = 'DEV PROJECT PLANNING: PROJECT SUBMITTED FOR YOUR APPROVAL';
			$message = 
			'AS/FISCAL,'."\n\n".
			
			'A SEDL manager has expressed interest in pursuing a DEV project (funding opportunity). DEV ('.$_SESSION['user_ID'].') has recommended pursuing this project and submitted comments for your consideration. '."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To process this request, click here:'."\n".
			
			'http://www.sedl.org/staff/sims/dev_projects.php?id='.$project_ID."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################

		}elseif($_GET['mgr_action'] == 'Pursue-ACTIVE'){ // SEND E-MAIL NOTIFICATION TO DEV staff (created_by)
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = $created_by;
			$subject = 'DEV PROJECT PLANNING: PROJECT SUBMITTED IS ACTIVE';
			$message = 
			'DEV,'."\n\n".
			
			'A DEV reviewer ('.$_SESSION['user_ID'].') has indicated that a previously submitted DEV project (funding opportunity) is already under development.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To process this response, log on to the DEV: Project Planning database at the following link:'."\n".
			
			'fmp7://198.214.140.248/dev_base.fp7'."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################

		}elseif($_GET['mgr_action'] == 'No go-AS'){ // SEND E-MAIL NOTIFICATION TO ALL REVIEWERS
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = $send_to_all.','.$dev_reviewer.'@sedl.org';
			$subject = 'DEV PROJECT PLANNING: PROJECT REJECTED';
			$message = 
			'DEV Funding Opportunity Reviewer,'."\n\n".
			
			'AS ('.$_SESSION['user_ID'].') has rejected (No go) a DEV project (funding opportunity) that you previously reviewed.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$comments."\n".
			'------------------------------------------------------------'."\n\n".
	
			'For more information about this decision, contact ('.$_SESSION['user_ID'].')'."\n\n".
			
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################

		}elseif($_GET['mgr_action'] == 'Contingent OK to pursue'){ // SEND E-MAIL NOTIFICATION TO DEV & CEO TO ADDRESS FISCAL ISSUES
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = 'mboethel@sedl.org, whoover@sedl.org';
			$subject = 'DEV PROJECT PLANNING: PROJECT SUBMITTED FOR YOUR APPROVAL (CONTINGENT OK TO PURSUE)';
			$message = 
			'DEV/CEO,'."\n\n".
			
			'AS Director ('.$_SESSION['user_ID'].') has approved (WITH FISCAL CONTINGENCY) pursuing a DEV project (funding opportunity).'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To process this request, click here:'."\n".
			
			'http://www.sedl.org/staff/sims/dev_projects.php?id='.$project_ID."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################

		}elseif($_GET['mgr_action'] == 'OK to pursue'){ // SEND E-MAIL NOTIFICATION TO CEO FOR FINAL APPROVAL
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = 'whoover@sedl.org';
			$subject = 'DEV PROJECT PLANNING: PROJECT SUBMITTED FOR YOUR APPROVAL';
			$message = 
			'CEO,'."\n\n".
			
			'AS Director ('.$_SESSION['user_ID'].') has approved pursuing a DEV project (funding opportunity). The project has also been reviewed and approved for pursuing by relevant managers and DEV staff.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To process this request, click here:'."\n".
			
			'http://www.sedl.org/staff/sims/dev_projects.php?id='.$project_ID."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################

		}elseif($_GET['mgr_action'] == 'No go-CEO'){ // SEND E-MAIL NOTIFICATION TO ALL REVIEWERS
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = $send_to_all.','.$dev_reviewer.'@sedl.org';
			$subject = 'DEV PROJECT PLANNING: PROJECT REJECTED';
			$message = 
			'DEV Funding Opportunity Reviewer,'."\n\n".
			
			'CEO ('.$_SESSION['user_ID'].') has rejected (No go) a DEV project (funding opportunity) that you previously reviewed.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$comments."\n".
			'------------------------------------------------------------'."\n\n".
	
			'For more information about this decision, contact ('.$_SESSION['user_ID'].')'."\n\n".
			
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################

		}elseif($_GET['mgr_action'] == 'Approval to pursue'){ // SEND E-MAIL NOTIFICATION TO ALL REVIEWERS
			//$to = $_GET['action_target'].'@sedl.org';
		
			##########################################################
			## START: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			##########################################################
			$to = $send_to_all;
			$subject = 'DEV PROJECT PLANNING: PROJECT APPROVED TO PURSUE';
			$message = 
			'DEV Funding Opportunity Reviewer,'."\n\n".
			
			'CEO ('.$_SESSION['user_ID'].') has approved pursuing a DEV project (funding opportunity). The project status is now "under development".'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$_GET['comments']."\n".
			'------------------------------------------------------------'."\n\n".
	
			'Congratulations and good luck in pursuing this funding opportunity!'."\n\n".

			'For more information about this decision, contact ('.$_SESSION['user_ID'].')'."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			########################################################
			## END: SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
			########################################################

		}else{ // SEND E-MAIL NOTIFICATION TO DEV REPRESENTATIVE
		
			#################################################
			## START: SEND E-MAIL NOTIFICATION TO HR ##
			#################################################
			if($dev_reviewer == ''){
			$to = 'jwaisath@sedl.org'; // E-MAIL FOR DEV REPRESENTATIVE (IF NO REVIEWER SPECIFIED)
			}else{
			$to = $dev_reviewer.'@sedl.org'; // E-MAIL FOR DEV REPRESENTATIVE
			}
			$subject = 'DEV PROJECT PLANNING: REVIEWER RESPONSE RECEIVED';
			$message = 
			'DEV,'."\n\n".
			
			'A reviewer response was received for the project: '.$_GET['project_name'].' (ID: '.$project_ID.').'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' DATA RECEIVED'."\n".
			'------------------------------------------------------------'."\n".
			'Submitted by: '.$_SESSION['user_ID']."\n".
			'Project Name: '.$_GET['project_name']."\n".
			'Awarding Agency: '.$_GET['awarding_agency']."\n".
			'Award Type: '.$_GET['award_type']."\n".
			'Total: '.$_GET['possible_total_value']."\n".
			'Action: '.$_GET['mgr_action']."\n".
			'Comments: '.$comments."\n".
			'------------------------------------------------------------'."\n\n".
	
			'To process this response, log on to the DEV: Project Planning database at the following link:'."\n".
			
			'fmp7://198.214.140.248/dev_base.fp7'."\n\n".
	
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
			#################################################
			## END: SEND E-MAIL NOTIFICATION TO HR ##
			#################################################
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
## START: FIND THIS DEV PROJECT ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('dev_base.fp7','projects_detail');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('project_ID','=='.$project_ID);
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam('leave_hrs_date','ascend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
if($searchResult[foundCount] == 0){
$project_deleted = '1';
}

$recordData = current($searchResult['data']);
$row_ID = $recordData['c_row_ID'][0];
$created_by = $recordData['created_by'][0];

//$reject1 = 'at least '.$recordData['doc_position_app_pool_count'][0].' other applicants indicate more extensive relevant education, skills, and/or experience, and/or present stronger writing sample';
//$reject2 = 'at least '.$recordData['doc_position_app_pool_count2'][0].' other applicants indicate more extensive relevant education, skills, and/or experience, and/or present stronger writing sample';


//if((($recordData['PR_pre_approval_required_IT'][0] == '1')&&($recordData['sign_status_IT'][0] != '1'))||(($recordData['PR_pre_approval_required_IRC'][0] == '1')&&($recordData['sign_status_IRC'][0] != '1'))){
//$preappRequired = '1';
//}else{
//$preappRequired = '0';
//}
###############################################################
## END: FIND THIS DEV PROJECT ##
###############################################################

if($recordData['c_attachment_count'][0] > 0){ // THE SELECTED APP HAS FILE ATTACHMENTS
#################################################################
## START: FIND ATTACHMENTS RELATED TO THIS APPLICANT ##
#################################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('dev_base.fp7','files_attachments');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('project_ID','=='.$project_ID);
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
$search4 -> SetDBData('dev_base.fp7','projects_action_log');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('project_ID','=='.$project_ID);
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

$this_day = date("d");
$this_month = date("m");
$this_year = date("Y");
$next_year = $this_year + 1;

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: DEV Project Planning</title>
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
	width: 600px;
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
		select_value6 = document.form2.mgr_action.value;
		var id6 = 'reject_reason3';
		var obj6 = '';
		obj6 = (document.getElementById) ? document.getElementById(id6) : ((document.all) ? document.all[id6] : ((document.layers) ? document.layers[id6] : false));

		select_value7 = "";
		select_value7 = document.form2.mgr_action.value;
		var id7 = 'reject_reason4';
		var obj7 = '';
		obj7 = (document.getElementById) ? document.getElementById(id7) : ((document.all) ? document.all[id7] : ((document.layers) ? document.layers[id7] : false));

		select_value8 = "";
		select_value8 = document.form2.mgr_action.value;
		var id8 = 'will_respond_date';
		var obj8 = '';
		obj8 = (document.getElementById) ? document.getElementById(id8) : ((document.all) ? document.all[id8] : ((document.layers) ? document.layers[id8] : false));

			if(select_value == ""){
			  obj.style.display = 'none';
			  //alert("onBodyLoad.");
			
			} else if((select_value == "Send for further review")||(select_value == "Provide feedback to hiring manager")||(select_value == "Send response to comments")){
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
			
			} else if(select_value4 == "Not interested"){
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
			
			} else if(select_value5 == "No go-DEV"){
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
			
			} else if(select_value6 == "No go-AS"){
			  // alert("You chose Journal article.");
			  // return false;
			  obj6.style.display = 'block';
			}
			else
			{
			  obj6.style.display = 'none';
			}


			if(select_value7 == ""){
			  obj7.style.display = 'none';
			  //alert("onBodyLoad.");
			
			} else if(select_value7 == "No go-CEO"){
			  // alert("You chose Journal article.");
			  // return false;
			  obj7.style.display = 'block';
			}
			else
			{
			  obj7.style.display = 'none';
			}

			if(select_value8 == ""){
			  obj8.style.display = 'none';
			  //alert("onBodyLoad.");
			
			} else if(select_value8 == "Will respond later"){
			  // alert("You chose Journal article.");
			  // return false;
			  obj8.style.display = 'block';
			}
			else
			{
			  obj8.style.display = 'none';
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>Funding Opportunity Tracker</h1><hr /></td></tr>
			
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Reviewer: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - To process this document, submit your action and comments below. | <a href="/staff/sims/menu_dev_projects.php?action=<?php echo $view;?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $position_ID;?>#<?php echo $recordData['project_ID'][0];?>">Close Document</a></p>
			</td></tr>
			
<?php if($project_deleted == '1'){?>

			<tr><td colspan="2">
				<p class="alert_small">This project (ID: <?php echo $project_ID;?>) no longer exists in the DEV database. | <a href="/staff/sims/menu_dev_projects.php?action=<?php echo $view;?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $position_ID;?>#<?php echo $recordData['project_ID'][0];?>">Close Document</a></p>
			</td></tr>

<?php }else{ ?>
			
			<tr><td colspan="2">
			
						<table cellpadding="10" cellspacing="0" border="0" bordercolor="ebebeb" bgcolor="ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong>Project: <?php echo $recordData['project_name'][0].' ('.$recordData['project_ID'][0].')';?></strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Project Status: <strong><?php if($recordData['status'][0] == 'Pending'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['status'][0]).'</span>';?></strong></span></td></tr>
						<tr><td class="body" nowrap><strong>APPLICATION</strong></td><td align="right">ID: <?php echo $recordData['project_ID'][0];?></td></tr>

						<tr><td colspan="2" class="body">
						

							<table cellpadding="7" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
							
								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>PROJECT DETAILS</strong></div>
								
									<table style="border:0px dotted #000000;width:100%;margin-top:6px">
										<?php if(($recordData['respond_to_date'][0] !== '')&&($recordData['mgr_response_received'][0] !== 'yes')){?><tr><td colspan="2"><div class="alert_small">Manager response is due by: <span style="font-weight:bold;color:#ff0000"><?php echo $recordData['respond_to_date'][0];?></span></div></td></td></tr><?php } ?>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Project Name</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['project_name'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Awarding Agency</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top" nowrap><?php echo $recordData['awarding_agency'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Duration</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top" nowrap><?php echo $recordData['project_duration'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Award Type</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top" nowrap><?php echo $recordData['award_type'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Funds/Yr</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top" nowrap><?php echo $recordData['funds_per_proj_year'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Funds/Total</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top" nowrap><?php echo $recordData['possible_total_value'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Staff Lead</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top" nowrap><?php echo $recordData['staff_lead'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Date Closed</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top" nowrap><?php echo $recordData['date_closed'][0];?></td></tr>
									</table>

								
								</td>

								<td class="body" style="vertical-align:text-top" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>PROJECT FILES (<?php echo $searchResult3['foundCount'];?>)</strong></div>
								
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

								<tr><td colspan="2" class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>PROJECT DESCRIPTION</strong></div><br><?php if($recordData['description'][0] != ''){echo $recordData['c_description_html'][0];}else{echo 'N/A';}?></td></tr>



								<tr><td  colspan="2" style="vertical-align:text-top" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>ACTION LOG</strong></div>



								
									<table width="100%" style="margin-top:6px;width:100%">

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf"><strong>DATE</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>USER</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>ACTION</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>SENT TO</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap width="100%"><strong>COMMENTS</strong></td></tr>

									<?php if($searchResult4['foundCount'] > 0){ // SHOW APP ACTION LOG ?>
									<?php foreach($searchResult4['data'] as $key => $searchData4) {  ?>

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['creation_timestamp'][0];?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['user'][0];?><?php if($_SESSION['user_ID'] == $searchData4['user'][0]){?><br><a href="dev_projects.php?id=<?php echo $recordData['project_ID'][0];?>&logx=1&logid=<?php echo $searchData4['c_row_ID'][0];?>" onclick="javascript:return confirm('Are you sure you want to delete this log entry?')">Delete</a><?php }?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['action'][0];?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px"><?php echo $searchData4['action_target'][0];?></td><td class="tiny" width="100%" style="vertical-align:text-top;border:1px dotted #000000;padding:3px"><?php if($searchData4['comments'][0] != ''){?><?php echo $searchData4['c_comments_html_display'][0];?><?php }else{ echo 'N/A';}?></td></tr>
									
									<?php } ?>
									<?php }else{ ?>

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" colspan="5">N/A</td></tr>

									<?php } ?>

										<tr><td style="background-color:#ebebeb;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap colspan="5">
										
											<form id="form2" name="form2" onsubmit="return checkFields()">
											<input type="hidden" name="reviewer_submit" value="1">
											<input type="hidden" name="id" value="<?php echo $recordData['project_ID'][0];?>">
											<input type="hidden" name="project_name" value="<?php echo $recordData['project_name'][0];?>">
											<input type="hidden" name="awarding_agency" value="<?php echo $recordData['awarding_agency'][0];?>">
											<input type="hidden" name="award_type" value="<?php echo $recordData['award_type'][0];?>">
											<input type="hidden" name="possible_total_value" value="<?php echo $recordData['possible_total_value'][0];?>">
											<input type="hidden" name="dev_reviewer" value="<?php echo $recordData['dev_reviewer'][0];?>">
											<input type="hidden" name="send_to_all" value="<?php echo $recordData['c_target_email_list'][0];?>">
											<input type="hidden" name="sortfield" value="<?php echo $sortfield;?>">
											<input type="hidden" name="sortorder" value="<?php echo $sortorder;?>">
											<input type="hidden" name="displaynum" value="<?php echo $displaynum;?>">
											

											<table  style="border:1px dotted #000000;background-color:#b7e4fc;padding:6px;width:600px">
											<tr><td id="title" colspan="2" style="border:0px;padding:6px">Reviewer Action</td></tr>
											<tr><td style="border:0px;padding:6px;vertical-align:top">
											
											
											
											<label class="col1"><strong>Reviewer:</strong>&nbsp;&nbsp;</label></td><td class="col2" style="border:1px dotted;background-color:#ffffff;padding:6px;vertical-align:top"><?php echo $_SESSION['user_ID'];?></td></tr>
											<tr><td style="border:0px;padding:6px;vertical-align:top"><label class="col1"><strong>Action:</strong>&nbsp;&nbsp;</label></td><td style="border:1px dotted;background-color:#ffffff;padding:6px;vertical-align:top;width:400px">




											<select name="mgr_action" onChange="UpdateSelect();">
											<option value=""></option>
											<option value="">------------------------------</option>
											<option value="">&nbsp;&nbsp;MANAGERS/PROJECT DIRECTORS</option>
											<option value="">------------------------------</option>
											<option value="Interested">&nbsp;(1) Interested</option>
											<option value="Not interested">&nbsp;(2) Not interested</option>
											<option value="Will respond later">&nbsp;(3) Will respond later</option>
											<option value="">------------------------------</option>
											<option value="">&nbsp;&nbsp;DEV STAFF</option>
											<option value="">------------------------------</option>
											<option value="No go-DEV">&nbsp;(1) No go</option>
											<option value="Recommend pursuing">&nbsp;(2) Recommend pursuing</option>
											<option value="Pursue-ACTIVE">&nbsp;(3) Pursue (file is Active)</option>
											<option value="">------------------------------</option>
											<option value="">&nbsp;&nbsp;AS/FISCAL</option>
											<option value="">------------------------------</option>
											<option value="No go-AS">&nbsp;(1) No go</option>
											<option value="Contingent OK to pursue">&nbsp;(2) Contingent OK to pursue</option>
											<option value="OK to pursue">&nbsp;(3) OK to pursue</option>
											<option value="">------------------------------</option>
											<option value="">&nbsp;&nbsp;EO/CEO</option>
											<option value="">------------------------------</option>
											<option value="No go-CEO">&nbsp;(1) No go</option>
											<option value="Approval to pursue">&nbsp;(2) Approval to pursue</option>
											<option value="">------------------------------</option>
											<option value="">&nbsp;&nbsp;MORE OPTIONS</option>
											<option value="">------------------------------</option>
											<option value="Other (see comments)">&nbsp;(1) Other (see comments)</option>
											<option value="Send for further review">&nbsp;(2) Send for further review to >></option>
											<option value="Send response to comments">&nbsp;(3) Send response to comments >></option>
											</select><br>&nbsp;<br>

												<div id="specify_target" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
												<div style="padding:4px;border:1px dotted #000000;background-color:#ffffff">Select reviewer(s)/manager(s): </div>&nbsp;<br>
												
							
													
													<?php foreach($v1Result['valueLists']['project_reviewers_web'] as $key => $value) { ?>
													
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

												<div id="reject_reason1" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc" nowrap>
												<div style="padding:4px;border:1px dotted #000000;background-color:#ffffff"><strong>MGR</strong> - Reason for Rejection: </div>
												
													<input type="checkbox" name="reject_reason_mgr[]" value="low chance of winning">&nbsp;low chance of winning</option><br>
													<input type="checkbox" name="reject_reason_mgr[]" value="too little time or resources to prepare proposal">&nbsp;too little time or resources to prepare proposal</option><br>
													<input type="checkbox" name="reject_reason_mgr[]" value="underfunded project">&nbsp;underfunded project</option><br>
													<input type="checkbox" name="reject_reason_mgr[]" value="flawed project concept">&nbsp;flawed project concept</option><br>
													<input type="checkbox" name="reject_reason_mgr[]" value="political costs could outweigh benefits">&nbsp;political costs could outweigh benefits</option><br>
													<input type="checkbox" name="reject_reason_mgr[]" value="conflict of interest">&nbsp;conflict of interest</option><br>
													<input type="checkbox" name="reject_reason_mgr[]" value="other (see comments)">&nbsp;other (see comments)</option><br>
															
												</div>

												<div id="reject_reason2" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
															
												<div style="padding:4px;border:1px dotted #000000;background-color:#ffffff"><strong>DEV</strong> - Reason for Rejection: </div>
												
													<input type="checkbox" name="reject_reason_dev[]" value="concur with program staff">&nbsp;concur with program staff</option><br>
													<input type="checkbox" name="reject_reason_dev[]" value="low chance of winning">&nbsp;low chance of winning</option><br>
													<input type="checkbox" name="reject_reason_dev[]" value="too little time or resources to prepare proposal">&nbsp;too little time or resources to prepare proposal</option><br>
													<input type="checkbox" name="reject_reason_dev[]" value="underfunded project">&nbsp;underfunded project</option><br>
													<input type="checkbox" name="reject_reason_dev[]" value="flawed project concept">&nbsp;flawed project concept</option><br>
													<input type="checkbox" name="reject_reason_dev[]" value="political costs could outweigh benefits">&nbsp;political costs could outweigh benefits</option><br>
													<input type="checkbox" name="reject_reason_dev[]" value="conflict of interest">&nbsp;conflict of interest</option><br>
													<input type="checkbox" name="reject_reason_dev[]" value="other (see comments)">&nbsp;other (see comments)</option><br>

												</div>

												<div id="reject_reason3" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">

												<div style="padding:4px;border:1px dotted #000000;background-color:#ffffff"><strong>AS/FISCAL</strong> - Reason for Rejection: </div>
												
													<input type="checkbox" name="reject_reason_as[]" value="SEDL indirect cost rate disallowed">&nbsp;SEDL's indirect cost rate disallowed</option><br>
													<input type="checkbox" name="reject_reason_as[]" value="other (see comments)">&nbsp;other fatal administrative issues (see comments)</option><br>
												
												</div>

												<div id="reject_reason4" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">

												<div style="padding:4px;border:1px dotted #000000;background-color:#ffffff"><strong>EO/CEO</strong> - Reason for Rejection: </div>
												
													<input type="checkbox" name="reject_reason_ceo[]" value="concur with previous no-go reasons">&nbsp;concur with previous no-go reasons</option><br>
													<input type="checkbox" name="reject_reason_ceo[]" value="other (see comments)">&nbsp;other (see comments)</option><br>
												
												</div>

												<div id="will_respond_date" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">

												<div style="padding:4px;border:1px dotted #000000;background-color:#ffffff"><strong>MGR</strong> - Enter the earliest date you will be able to respond: </div>
												
													<select name="will_respond_date_m">
													<option value="">Select Month</option>
													<option value="">--------------</option>
													<option value="01" <?php if($this_month == '01'){?> selected<?php }?>>January</option>
													<option value="02" <?php if($this_month == '02'){?> selected<?php }?>>February</option>
													<option value="03" <?php if($this_month == '03'){?> selected<?php }?>>March</option>
													<option value="04" <?php if($this_month == '04'){?> selected<?php }?>>April</option>
													<option value="05" <?php if($this_month == '05'){?> selected<?php }?>>May</option>
													<option value="06" <?php if($this_month == '06'){?> selected<?php }?>>June</option>
													<option value="07" <?php if($this_month == '07'){?> selected<?php }?>>July</option>
													<option value="08" <?php if($this_month == '08'){?> selected<?php }?>>August</option>
													<option value="09" <?php if($this_month == '09'){?> selected<?php }?>>September</option>
													<option value="10" <?php if($this_month == '10'){?> selected<?php }?>>October</option>
													<option value="11" <?php if($this_month == '11'){?> selected<?php }?>>November</option>
													<option value="12" <?php if($this_month == '12'){?> selected<?php }?>>December</option>
													</select>
													
													<select name="will_respond_date_d">
													<option value="">Select Day</option>
													<option value="">--------------</option>
													<option value="01" <?php if($this_day == '01'){?> selected<?php }?>>01</option>
													<option value="02" <?php if($this_day == '02'){?> selected<?php }?>>02</option>
													<option value="03" <?php if($this_day == '03'){?> selected<?php }?>>03</option>
													<option value="04" <?php if($this_day == '04'){?> selected<?php }?>>04</option>
													<option value="05" <?php if($this_day == '05'){?> selected<?php }?>>05</option>
													<option value="06" <?php if($this_day == '06'){?> selected<?php }?>>06</option>
													<option value="07" <?php if($this_day == '07'){?> selected<?php }?>>07</option>
													<option value="08" <?php if($this_day == '08'){?> selected<?php }?>>08</option>
													<option value="09" <?php if($this_day == '09'){?> selected<?php }?>>09</option>
													<option value="10" <?php if($this_day == '10'){?> selected<?php }?>>10</option>
													<option value="11" <?php if($this_day == '11'){?> selected<?php }?>>11</option>
													<option value="12" <?php if($this_day == '12'){?> selected<?php }?>>12</option>
													<option value="13" <?php if($this_day == '13'){?> selected<?php }?>>13</option>
													<option value="14" <?php if($this_day == '14'){?> selected<?php }?>>14</option>
													<option value="15" <?php if($this_day == '15'){?> selected<?php }?>>15</option>
													<option value="16" <?php if($this_day == '16'){?> selected<?php }?>>16</option>
													<option value="17" <?php if($this_day == '17'){?> selected<?php }?>>17</option>
													<option value="18" <?php if($this_day == '18'){?> selected<?php }?>>18</option>
													<option value="19" <?php if($this_day == '19'){?> selected<?php }?>>19</option>
													<option value="20" <?php if($this_day == '20'){?> selected<?php }?>>20</option>
													<option value="21" <?php if($this_day == '21'){?> selected<?php }?>>21</option>
													<option value="22" <?php if($this_day == '22'){?> selected<?php }?>>22</option>
													<option value="23" <?php if($this_day == '23'){?> selected<?php }?>>23</option>
													<option value="24" <?php if($this_day == '24'){?> selected<?php }?>>24</option>
													<option value="25" <?php if($this_day == '25'){?> selected<?php }?>>25</option>
													<option value="26" <?php if($this_day == '26'){?> selected<?php }?>>26</option>
													<option value="27" <?php if($this_day == '27'){?> selected<?php }?>>27</option>
													<option value="28" <?php if($this_day == '28'){?> selected<?php }?>>28</option>
													<option value="29" <?php if($this_day == '29'){?> selected<?php }?>>29</option>
													<option value="30" <?php if($this_day == '30'){?> selected<?php }?>>30</option>
													<option value="31" <?php if($this_day == '31'){?> selected<?php }?>>31</option>
													</select>

													<select name="will_respond_date_y">
													<option value="">Select Year</option>
													<option value="">--------</option>
													<option value="<?php echo $this_year;?>" selected><?php echo $this_year;?></option>
													<option value="<?php echo $next_year;?>"><?php echo $next_year;?></option>
													</select>

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

<?php } ?>

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
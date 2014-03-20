<?php
session_start();

include_once('sims_checksession.php');

$action = $_GET['action'];


include_once('FX/FX.php');
include_once('FX/server_data.php');


if($action == 'staff_sign'){

#######################
## START: STAFF SIGN ##
#######################
$update_row = $_GET['row_ID'];


########################################
## START: UPDATE THE TIMESHEET RECORD ##
########################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);

if($_SESSION['timesheet_approval_not_required'] == '1'){
$update -> AddDBParam('TimesheetSubmittedStatus','Approved');
}else{
$update -> AddDBParam('TimesheetSubmittedStatus','Pending');
}

$update -> AddDBParam('Signer_status_owner','1');
$update -> AddDBParam('print_flag','0');

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

} else {


	$update -> AddDBParam('Signer_ID_bgt_auth_1',$_SESSION['immediate_supervisor']);
	
	$i=2;
	foreach($_SESSION['other_signers'] as $current){
		
	$update -> AddDBParam('Signer_ID_bgt_auth_'.$i,$current);
	
	$i++;
	}


}




//$update -> AddDBParam('Signer_ID_bgt_auth_OT',$_SESSION['sims_user_ID']);
$update -> AddDBParam('signatures_required',$_SESSION['signatures_required']);
$update -> AddDBParam('signatures_required_oba',$_SESSION['total_other_signers']);

$updateResult = $update -> FMEdit();

$recordData = current($updateResult['data']);
$_SESSION['timesheet_hrs_email_summary'] = $recordData['c_timesheet_hrs_email_summary'][0];
########################################
## END: UPDATE THE TIMESHEET RECORD ##
########################################
if($_SESSION['timesheet_approval_not_required'] != '1'){
/*
########################################
## START: UPDATE THE APPROVALS TABLE ##
########################################
foreach($_SESSION['other_signers'] as $current){

$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('SIMS_2.fp7','timesheet_approvals'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information

$newrecord -> AddDBParam('timesheet_ID',$_SESSION['timesheet_ID']);
$newrecord -> AddDBParam('signer_status','0');
$newrecord -> AddDBParam('signer_sims_user_ID',$current);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];	
}

//$recordData = current($newrecordResult['data']);
########################################
## END: UPDATE THE APPROVALS TABLE ##
########################################
*/
}


if($updateResult['errorCode']==0) { 

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_STAFF');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$_SESSION['timesheet_ID']);
$newrecord -> AddDBParam('affected_row_ID',$update_row);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecord -> AddDBParam('error_code',$updateResult['errorCode']);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


$_SESSION['timesheet_signed_staff'] = '1';

$_SESSION['staff_has_ar'] = $recordData['staff::c_staff_has_time_leave_admin'][0];
$_SESSION['imm_spvsr_is_pba'] = $recordData['staff::c_cwp_spvsr_is_pba'][0];
$_SESSION['staff_ar'] = $recordData['staff::time_leave_admin_sims_user_ID'][0];
$_SESSION['staff_ar_email'] = $recordData['staff::time_leave_admin_email'][0];

if($_SESSION['timesheet_approval_not_required'] == '1'){
$_SESSION['timesheet_owner_email'] = stripslashes($_SESSION['signer_ID_owner']).'@sedl.org';

########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################


//SEND E-MAIL APPROVED NOTIFICATION TO STAFF MEMBER AND AS

$to = $_SESSION['timesheet_owner_email'].',maria.turner@sedl.org';
$subject = 'Your timesheet has been approved.';
$message = 
'Dear '.$_SESSION['signer_fullname_owner'].','."\n\n".

'The timesheet you submitted for the pay period ending '.$_SESSION['current_pay_period_end'].' has been approved. No further action is necessary on your part.'."\n\n".

'------------------------------------------------------------'."\n".
' TIMESHEET DETAILS'."\n".
'------------------------------------------------------------'."\n".
$_SESSION['timesheet_hrs_email_summary']."\n".
'------------------------------------------------------------'."\n\n".

'To view this timesheet or print a copy for your records, click here: '."\n".
'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';

mail($to, $subject, $message, $headers);
		
		
		
		
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################
header('Location: http://www.sedl.org/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd');

exit;
}



########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################
if($_SESSION['staff_has_ar'] == 1){ //IF USER'S PRIMARY BGT AUTHORITY HAS AN AUTHORIZED TIMESHEET ADMIN


		//SEND E-MAIL NOTIFICATION TO PRIMARY BGT AUTHORITY'S TIMESHEET ADMIN
		
			$to = $_SESSION['staff_ar_email'];
			$subject = $_SESSION['signer_fullname_owner'].' has submitted a timesheet for '.$_SESSION['current_pay_period_end'];
			$message = 
			'Dear '.$_SESSION['approved_by_auth_rep_full_name'].','."\n\n".
			
			'A timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TIMESHEET DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			$_SESSION['timesheet_hrs_email_summary']."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To check the accuracy of this timesheet before submitting it for budget authority approval, click here: '."\n".
			'http://www.sedl.org/staff/sims/timesheets_approve.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&approver_ID='.$_SESSION['staff_ar'].'&src=eml'."\n\n".
			
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
		
		
		
		
} elseif($_SESSION['imm_spvsr_is_pba'] == '0') { //IF USER'S PRIMARY BGT AUTHORITY & IMMEDIATE SPVSR IS NOT THE SAME PERSON

		$_SESSION['imm_spvsr_email'] = stripslashes($_SESSION['immediate_supervisor']).'@sedl.org';
		
		//SEND E-MAIL NOTIFICATION TO IMMEDIATE SUPERVISOR
		
			$to = $_SESSION['imm_spvsr_email'];
			$subject = $_SESSION['signer_fullname_owner'].' has submitted a timesheet for '.$_SESSION['current_pay_period_end'];
			$message = 
			'Dear '.$_SESSION['signer_fullname_imm_spvsr'].','."\n\n".
			
			'A timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TIMESHEET DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			$_SESSION['timesheet_hrs_email_summary']."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To approve this timesheet, click here: '."\n".
			'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".
			
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);




} elseif($_SESSION['imm_spvsr_is_pba'] == '1') { //IF USER'S PRIMARY BGT AUTHORITY & IMMEDIATE SPVSR IS THE SAME PERSON

	if($_SESSION['total_other_signers'] > 0){ //IF THE TIMESHEET REQUIRES OTHER BUDGET AUTHORITY SIGNATURES BESIDES PBA
		
		$other_signers = $_SESSION['other_signers'];
			
			//echo '<p>$_SESSION[other_signers]: '.$_SESSION['other_signers'];
			//exit;
			
			foreach($other_signers as $current){
				 
					$bgt_auth_email = stripslashes($current).'@sedl.org';
					
//SEND E-MAIL NOTIFICATION TO OTHER BUDGET AUTHORITIES BESIDES PBA

$to = $bgt_auth_email;
$subject = $_SESSION['signer_fullname_owner'].' has submitted a timesheet requiring your approval';
$message = 
'Dear Budget Authority,'."\n\n".

'A timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'. This timesheet includes time charged to one or more of your budget codes and requires your approval.'."\n\n".

'------------------------------------------------------------'."\n".
' TIMESHEET DETAILS'."\n".
'------------------------------------------------------------'."\n".
$_SESSION['timesheet_hrs_email_summary']."\n".
'------------------------------------------------------------'."\n\n".

'To approve this timesheet, click here: '."\n".
'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';
					
$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';

mail($to, $subject, $message, $headers);
		
			}

	} else { //IF THE TIMESHEET DOES NOT REQUIRE OTHER BUDGET AUTHORITY SIGNATURES BESIDES PBA
	
			$pba_email = stripslashes($_SESSION['signer_ID_pba']).'@sedl.org';
			
//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY

$to = $pba_email;
$subject = $_SESSION['signer_fullname_owner'].' has submitted a timesheet requiring your approval';
$message = 
'Dear '.$_SESSION['signer_fullname_pba'].','."\n\n".

'A timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'. This timesheet includes time charged to one or more of your budget codes and requires your approval.'."\n\n".

'------------------------------------------------------------'."\n".
' TIMESHEET DETAILS'."\n".
'------------------------------------------------------------'."\n".
$_SESSION['timesheet_hrs_email_summary']."\n".
'------------------------------------------------------------'."\n\n".

'To approve this timesheet, click here: '."\n".
'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';
			
$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';

mail($to, $subject, $message, $headers);
	
	
	}




########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################


} else {

$_SESSION['timesheet_signed_staff'] = '2'; //CAPTURE MISCELLANEOUS ERROR
echo '<p>Error_999: timesheets_process.php (line 259)';
}
//echo '<p>action:'.$action;
//echo '<p>update_row:'.$update_row;
//echo '<p>ErrorCode:'.$_SESSION['timesheet_signed_staff'];
}
header('Location: http://www.sedl.org/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd');
//exit;
//}
#######################
## END: STAFF SIGN ##
#######################

} elseif($action == 'staff_sign_ar'){

##########################
## START: STAFF-AR SIGN ##
##########################
$update_row = $_GET['row_ID'];


########################################
## START: UPDATE THE TIMESHEET RECORD ##
########################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);

$update -> AddDBParam('TimesheetSubmittedStatus','Pending');
$update -> AddDBParam('Signer_status_owner','1');
$update -> AddDBParam('Signer_ID_imm_spvsr',$_SESSION['immediate_supervisor']);
$update -> AddDBParam('Signer_ID_pba',$_SESSION['primary_bgt_auth']);
$update -> AddDBParam('total_oba_signers',$_SESSION['total_other_signers']);
$update -> AddDBParam('approved_by_auth_rep_status','1');
$update -> AddDBParam('print_flag','0');



if($_SESSION['signer_pba_is_spvsr'] == 1) {

	$update -> AddDBParam('Signer_ID_bgt_auth_1',$_SESSION['primary_bgt_auth']);
	
	$i=2;
	foreach($_SESSION['other_signers'] as $current){
		
	$update -> AddDBParam('Signer_ID_bgt_auth_'.$i,$current);
	
	$i++;
	}

} else {


	$update -> AddDBParam('Signer_ID_bgt_auth_1',$_SESSION['immediate_supervisor']);
	
	$i=2;
	foreach($_SESSION['other_signers'] as $current){
		
	$update -> AddDBParam('Signer_ID_bgt_auth_'.$i,$current);
	
	$i++;
	}


}




//$update -> AddDBParam('Signer_ID_bgt_auth_OT',$_SESSION['sims_user_ID']);
$update -> AddDBParam('signatures_required',$_SESSION['signatures_required']);
$update -> AddDBParam('signatures_required_oba',$_SESSION['total_other_signers']);

$updateResult = $update -> FMEdit();

$recordData = current($updateResult['data']);
########################################
## END: UPDATE THE TIMESHEET RECORD ##
########################################
$_SESSION['timesheet_hrs_email_summary'] = $recordData['c_timesheet_hrs_email_summary'][0];




if($updateResult['errorCode']==0) { 
$_SESSION['timesheet_signed_staff'] = '1';

//$_SESSION['staff_has_ar'] = $recordData['staff::c_staff_has_time_leave_admin'][0];
$_SESSION['imm_spvsr_is_pba'] = $recordData['staff::c_cwp_spvsr_is_pba'][0];
//$_SESSION['staff_ar'] = $recordData['staff::time_leave_admin_sims_user_ID'][0];
//$_SESSION['staff_ar_email'] = $recordData['staff::time_leave_admin_email'][0];



// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_STAFF_AR');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$_SESSION['timesheet_ID']);
$newrecord -> AddDBParam('affected_row_ID',$update_row);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecord -> AddDBParam('error_code',$updateResult['errorCode']);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];



########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################
if($_SESSION['imm_spvsr_is_pba'] == '0') { //IF USER'S PRIMARY BGT AUTHORITY & IMMEDIATE SPVSR IS NOT THE SAME PERSON

$imm_spvsr_email = stripslashes($_SESSION['immediate_supervisor']).'@sedl.org';
		
//SEND E-MAIL NOTIFICATION TO IMMEDIATE SUPERVISOR

$to = $imm_spvsr_email;
$subject = $_SESSION['signer_fullname_owner'].' has submitted a timesheet requiring your approval';
$message = 
'Dear '.$_SESSION['signer_fullname_imm_spvsr'].','."\n\n".

'A timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'.'."\n\n".

'------------------------------------------------------------'."\n".
' TIMESHEET DETAILS'."\n".
'------------------------------------------------------------'."\n".
$_SESSION['timesheet_hrs_email_summary']."\n".
'------------------------------------------------------------'."\n\n".

'To approve this timesheet, click here: '."\n".
'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';

mail($to, $subject, $message, $headers);






} elseif($_SESSION['imm_spvsr_is_pba'] == '1') { //IF USER'S PRIMARY BGT AUTHORITY & IMMEDIATE SPVSR IS THE SAME PERSON


	if($_SESSION['total_other_signers'] > 0){ //IF THE TIMESHEET REQUIRES OTHER BUDGET AUTHORITY SIGNATURES BESIDES PBA
		
		$other_signers = $_SESSION['other_signers'];
			
			foreach($other_signers as $current){
				 
					$bgt_auth_email = stripslashes($current).'@sedl.org';
					
//SEND E-MAIL NOTIFICATION TO OTHER BUDGET AUTHORITIES BESIDES PBA

$to = $bgt_auth_email;
$subject = $_SESSION['signer_fullname_owner'].' has submitted a timesheet requiring your approval';
$message = 
'Dear Budget Authority,'."\n\n".

'A timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'. This timesheet includes time charged to one or more of your budget codes and requires your approval.'."\n\n".

'------------------------------------------------------------'."\n".
' TIMESHEET DETAILS'."\n".
'------------------------------------------------------------'."\n".
$_SESSION['timesheet_hrs_email_summary']."\n".
'------------------------------------------------------------'."\n\n".

'To approve this timesheet, click here: '."\n".
'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';

mail($to, $subject, $message, $headers);
		
			}

	} else { //IF THE TIMESHEET DOES NOT REQUIRE OTHER BUDGET AUTHORITY SIGNATURES BESIDES PBA
	
			$pba_email = stripslashes($_SESSION['signer_ID_pba']).'@sedl.org';
			
//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY

$to = $pba_email;
$subject = $_SESSION['signer_fullname_owner'].' has submitted a timesheet requiring your approval';
$message = 
'Dear '.$_SESSION['signer_fullname_pba'].','."\n\n".

'A timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'. This timesheet includes time charged to one or more of your budget codes and requires your approval.'."\n\n".

'------------------------------------------------------------'."\n".
' TIMESHEET DETAILS'."\n".
'------------------------------------------------------------'."\n".
$_SESSION['timesheet_hrs_email_summary']."\n".

'------------------------------------------------------------'."\n\n".

'To approve this timesheet, click here: '."\n".
'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';

mail($to, $subject, $message, $headers);
	
	
	}










}
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################


} else {

$_SESSION['timesheet_signed_staff'] = '2'; //ADD CONTINGENCY FOR STAFF WITH NO BGT AUTH REP AND WHOSE IMM_SPVSR IS NOT PBA?
echo '<p>Error_999: timesheets_process.php (line 468)';
}
//echo '<p>action:'.$action;
//echo '<p>update_row:'.$update_row;
//echo '<p>ErrorCode:'.$_SESSION['timesheet_signed_staff'];
header('Location: http://www.sedl.org/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd');
exit;

########################
## END: STAFF-AR SIGN ##
########################

} elseif($action == 'oba_sign') {

#######################
## START: OBA SIGN ##
#######################
$update_row = $_GET['row_ID'];
$bgt_auth = $_GET['bgt_auth'];


#########################################################
## START: UPDATE THE TIMESHEET RECORD IF SIGNER IS PBA ##
#########################################################
if($bgt_auth == $_SESSION['signer_ID_pba']){

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);

/*
if($bgt_auth == $_SESSION['signer_ID_bgt_auth_1']){
$update -> AddDBParam('Signer_status_bgt_auth_1','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_2']){
$update -> AddDBParam('Signer_status_bgt_auth_2','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_3']){
$update -> AddDBParam('Signer_status_bgt_auth_3','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_4']){
$update -> AddDBParam('Signer_status_bgt_auth_4','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_5']){
$update -> AddDBParam('Signer_status_bgt_auth_5','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_6']){
$update -> AddDBParam('Signer_status_bgt_auth_6','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_7']){
$update -> AddDBParam('Signer_status_bgt_auth_7','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_8']){
$update -> AddDBParam('Signer_status_bgt_auth_8','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_OT']){
$update -> AddDBParam('Signer_status_bgt_auth_OT','1');
}
*/

$update -> AddDBParam('Signer_status_pba','1');
$update -> AddDBParam('TimesheetSubmittedStatus','Approved');


$updateResult = $update -> FMEdit();

$recordData = current($updateResult['data']);
$_SESSION['timesheet_hrs_email_summary'] = $recordData['c_timesheet_hrs_email_summary'][0];


// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_PBA');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$_SESSION['timesheet_ID']);
$newrecord -> AddDBParam('affected_row_ID',$update_row);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecord -> AddDBParam('error_code',$updateResult['errorCode']);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

} elseif($bgt_auth != $_SESSION['signer_ID_pba']) {

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_OBA');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$_SESSION['timesheet_ID']);
$newrecord -> AddDBParam('affected_row_ID',$update_row);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecord -> AddDBParam('error_code',$updateResult['errorCode']);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


}

#######################################################
## END: UPDATE THE TIMESHEET RECORD IF SIGNER IS PBA ##
#######################################################

####################################################################
## START: FIND THE TIME_HRS RECORDS RELATED TO THIS BGT AUTHORITY ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('Timesheet_ID',$_SESSION['timesheet_ID']);
$search -> AddDBParam('BudgetAuthorityLocal',$bgt_auth);

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData9 = current($searchResult['data']);
//$revised_status = $recordData9['timesheets::TimesheetSubmittedStatus'][0];
####################################################################
## END: FIND THE TIME_HRS RECORDS RELATED TO THIS BGT AUTHORITY ##
####################################################################
########################################
## START: UPDATE THE TIME_HRS RECORDS ##
########################################
foreach($searchResult['data'] as $key => $searchData) { 

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','time_hrs');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$searchData['c_cwp_row_ID'][0]);

$update -> AddDBParam('HrsApproved','0'); // 0 = APPROVED
$update -> AddDBParam('TimeRevisedStatus','0'); // 0 = NOT REVISED

$updateResult = $update -> FMEdit();

$recordData2 = current($updateResult['data']);
}
########################################
## END: UPDATE THE TIME_HRS RECORDS ##
########################################



if($updateResult['errorCode']==0) { 



if($bgt_auth == $_SESSION['signer_ID_pba']){ //IF PRIMARY BUDGET AUTHORITY JUST SIGNED TIMESHEET

$_SESSION['timesheet_owner_email'] = stripslashes($_SESSION['signer_ID_owner']).'@sedl.org';

if($_SESSION['current_submitted_status'] == 'Revised'){
$revised_insert = 'revised ';
}else{
$revised_insert = '';
}

########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################


		//SEND E-MAIL APPROVED NOTIFICATION TO STAFF MEMBER AND AS
		
			$to = $_SESSION['timesheet_owner_email'].',maria.turner@sedl.org';
			$subject = 'Your '.$revised_insert.'timesheet has been approved.';
			$message = 
			'Dear '.$_SESSION['signer_fullname_owner'].','."\n\n".
			
			'The '.$revised_insert.'timesheet you submitted for the pay period ending '.$_SESSION['current_pay_period_end'].' has been approved. No further action is necessary on your part.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TIMESHEET DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			$_SESSION['timesheet_hrs_email_summary']."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view this timesheet or print a copy for your records, click here: '."\n".
			'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".
			
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
						
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
		
		
		
		
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################
header('Location: http://www.sedl.org/staff/sims/timesheets_ba_app.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&action=approve&payperiod='.$_SESSION['current_pay_period_end']);

exit;
}


if($recordData2['timesheets::c_sum_oba_time_hrs_approved'][0] > '0'){ //IF OTHER BUDGET AUTHORITIES STILL NEED TO SIGN THIS TIMESHEET
//if($recordData['c_oba_approvals_complete'][0] == '0'){ //IF OTHER BUDGET AUTHORITIES STILL NEED TO SIGN THIS TIMESHEET

header('Location: http://www.sedl.org/staff/sims/timesheets_ba_app.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&action=view&payperiod='.$_SESSION['current_pay_period_end']);

} else { //IF ALL OTHER BUDGET AUTHORITIES HAVE NOW SIGNED THIS TIMESHEET

//$_SESSION['imm_spvsr_is_pba'] = $recordData['staff::c_cwp_spvsr_is_pba'][0];
$_SESSION['pba_email'] = stripslashes($_SESSION['signer_ID_pba']).'@sedl.org';

########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################


		//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY
		
			$to = $_SESSION['pba_email'];
			$subject = $_SESSION['signer_fullname_owner'].' has submitted a '.$revised_insert.'timesheet for '.$_SESSION['current_pay_period_end'];
			$message = 
			'Dear '.$_SESSION['signer_fullname_pba'].','."\n\n".
			
			'A '.$revised_insert.'timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TIMESHEET DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			$_SESSION['timesheet_hrs_email_summary']."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To approve this timesheet, click here: '."\n".
			'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".
			
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
		
		
		
		
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################

}

} else {

$_SESSION['timesheet_signed_staff'] = '2'; //ADD CONTINGENCY FOR STAFF WITH NO BGT AUTH REP AND WHOSE IMM_SPVSR IS NOT PBA?
echo '<p>Error_999: timesheets_process.php (line 693)';
}
//echo '<p>action:'.$action;
//echo '<p>update_row:'.$update_row;
//echo '<p>ErrorCode:'.$_SESSION['timesheet_signed_staff'];
header('Location: http://www.sedl.org/staff/sims/timesheets_ba_app.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&action=approve&payperiod='.$_SESSION['current_pay_period_end']);


#######################
## END: OBA SIGN ##
#######################


} elseif($action == 'pba_sign') {

#######################
## START: PBA SIGN ##
#######################
$update_row = $_GET['row_ID'];
$bgt_auth = $_GET['bgt_auth'];


########################################
## START: UPDATE THE TIMESHEET RECORD ##
########################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);


if($bgt_auth == $_SESSION['signer_ID_bgt_auth_1']){
$update -> AddDBParam('Signer_status_bgt_auth_1','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_2']){
$update -> AddDBParam('Signer_status_bgt_auth_2','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_3']){
$update -> AddDBParam('Signer_status_bgt_auth_3','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_4']){
$update -> AddDBParam('Signer_status_bgt_auth_4','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_5']){
$update -> AddDBParam('Signer_status_bgt_auth_5','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_6']){
$update -> AddDBParam('Signer_status_bgt_auth_6','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_7']){
$update -> AddDBParam('Signer_status_bgt_auth_7','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_8']){
$update -> AddDBParam('Signer_status_bgt_auth_8','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_OT']){
$update -> AddDBParam('Signer_status_bgt_auth_OT','1');
}

if($bgt_auth == $_SESSION['signer_ID_pba']){
$update -> AddDBParam('Signer_status_pba','1');
$update -> AddDBParam('TimesheetSubmittedStatus','Approved');
}

$updateResult = $update -> FMEdit();

$recordData = current($updateResult['data']);
//$revised_status = $recordData['TimesheetSubmittedStatus'][0];
$_SESSION['timesheet_hrs_email_summary'] = $recordData['c_timesheet_hrs_email_summary'][0];


if($updateResult['errorCode']==0) { 

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_PBA');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$_SESSION['timesheet_ID']);
$newrecord -> AddDBParam('affected_row_ID',$update_row);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecord -> AddDBParam('error_code',$updateResult['errorCode']);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

}
########################################
## END: UPDATE THE TIMESHEET RECORD ##
########################################

####################################################################
## START: FIND THE TIME_HRS RECORDS RELATED TO THIS BGT AUTHORITY ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('Timesheet_ID','=='.$_SESSION['timesheet_ID']);
$search -> AddDBParam('BudgetAuthorityLocal',$bgt_auth);

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
####################################################################
## END: FIND THE TIME_HRS RECORDS RELATED TO THIS BGT AUTHORITY ##
####################################################################
########################################
## START: UPDATE THE TIME_HRS RECORDS ##
########################################
foreach($searchResult['data'] as $key => $searchData) { 

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','time_hrs');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$searchData['c_cwp_row_ID'][0]);

$update -> AddDBParam('HrsApproved','0'); //0 = APPROVED
$update -> AddDBParam('TimeRevisedStatus','0'); //0 = NOT REVISED

$updateResult = $update -> FMEdit();

//$recordData = current($updateResult['data']);
}
########################################
## END: UPDATE THE TIME_HRS RECORDS ##
########################################


if($updateResult['errorCode']==0) { 



//$_SESSION['imm_spvsr_is_pba'] = $recordData['staff::c_cwp_spvsr_is_pba'][0];
$_SESSION['timesheet_owner_email'] = stripslashes($_SESSION['signer_ID_owner']).'@sedl.org';


if($_SESSION['current_submitted_status'] == 'Revised'){
$revised_insert = 'revised ';
}else{
$revised_insert = '';
}


########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################


		//SEND E-MAIL APPROVED NOTIFICATION TO STAFF MEMBER AND AS
		
			$to = $_SESSION['timesheet_owner_email'].',maria.turner@sedl.org';
			$subject = 'Your '.$revised_insert.'timesheet has been approved.';
			$message = 
			'Dear '.$_SESSION['signer_fullname_owner'].','."\n\n".
			
			'The '.$revised_insert.'timesheet you submitted for the pay period ending '.$_SESSION['current_pay_period_end'].' has been approved. No further action is necessary on your part.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TIMESHEET DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			$_SESSION['timesheet_hrs_email_summary']."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view this timesheet or print a copy for your records, click here: '."\n".
			'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".
			
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
		
		
		
		
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################



} else {

$_SESSION['timesheet_signed_staff'] = '2'; //ADD CONTINGENCY FOR STAFF WITH NO BGT AUTH REP AND WHOSE IMM_SPVSR IS NOT PBA?

}
//echo '<p>action:'.$action;
//echo '<p>update_row:'.$update_row;
//echo '<p>ErrorCode:'.$_SESSION['timesheet_signed_staff'];

if($_SESSION['menu_type'] == 'spvsr_admin'){
header('Location: http://www.sedl.org/staff/sims/timesheets_spvsr_app.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&action=approve&payperiod='.$_SESSION['current_pay_period_end']);
}elseif($_SESSION['menu_type'] == 'ba_admin'){
header('Location: http://www.sedl.org/staff/sims/timesheets_ba_app.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&action=approve&payperiod='.$_SESSION['current_pay_period_end']);
}else{
header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
}


#######################
## END: PBA SIGN ##
#######################

} elseif($action == 'imm_spvsr_sign') {

######################################
## START: IMMEDIATE SUPERVISOR SIGN ##
######################################
$update_row = $_GET['row_ID'];
$bgt_auth = $_GET['bgt_auth'];


########################################
## START: UPDATE THE TIMESHEET RECORD ##
########################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);


$update -> AddDBParam('Signer_status_imm_spvsr','1');

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_1']){
$update -> AddDBParam('Signer_status_bgt_auth_1','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_2']){
$update -> AddDBParam('Signer_status_bgt_auth_2','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_3']){
$update -> AddDBParam('Signer_status_bgt_auth_3','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_4']){
$update -> AddDBParam('Signer_status_bgt_auth_4','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_5']){
$update -> AddDBParam('Signer_status_bgt_auth_5','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_6']){
$update -> AddDBParam('Signer_status_bgt_auth_6','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_7']){
$update -> AddDBParam('Signer_status_bgt_auth_7','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_8']){
$update -> AddDBParam('Signer_status_bgt_auth_8','1');
}

if($bgt_auth == $_SESSION['signer_ID_bgt_auth_OT']){
$update -> AddDBParam('Signer_status_bgt_auth_OT','1');
}

if($bgt_auth == $_SESSION['signer_ID_pba']){
$update -> AddDBParam('Signer_status_pba','1');
}

$updateResult = $update -> FMEdit();

$recordData = current($updateResult['data']);
//$revised_status = $recordData['TimesheetSubmittedStatus'][0];
$_SESSION['timesheet_hrs_email_summary'] = $recordData['c_timesheet_hrs_email_summary'][0];

if($updateResult['errorCode']==0) { 

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_IMM_SPVSR');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$_SESSION['timesheet_ID']);
$newrecord -> AddDBParam('affected_row_ID',$update_row);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecord -> AddDBParam('error_code',$updateResult['errorCode']);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
}
########################################
## END: UPDATE THE TIMESHEET RECORD ##
########################################

####################################################################
## START: FIND THE TIME_HRS RECORDS RELATED TO THIS BGT AUTHORITY ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('Timesheet_ID',$_SESSION['timesheet_ID']);
$search -> AddDBParam('BudgetAuthorityLocal',$bgt_auth);

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData9 = current($searchResult['data']);
//$revised_status = $recordData9['timesheets::TimesheetSubmittedStatus'][0];
####################################################################
## END: FIND THE TIME_HRS RECORDS RELATED TO THIS BGT AUTHORITY ##
####################################################################
if($searchResult['foundCount'] > '0'){ //IF THIS IMM SPVSR ALSO HAS HOURS TO APPROVE ON THIS TIMESHEET
########################################
## START: UPDATE THE TIME_HRS RECORDS ##
########################################
foreach($searchResult['data'] as $key => $searchData) { 

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','time_hrs');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$searchData['c_cwp_row_ID'][0]);

$update -> AddDBParam('HrsApproved','0'); // 0 = APPROVED
$update -> AddDBParam('TimeRevisedStatus','0'); // 0 = NOT REVISED

$updateResult = $update -> FMEdit();

$recordData2 = current($updateResult['data']);
}
########################################
## END: UPDATE THE TIME_HRS RECORDS ##
########################################
}

if($updateResult['errorCode']==0) { 


//$_SESSION['imm_spvsr_is_pba'] = $recordData['staff::c_cwp_spvsr_is_pba'][0];
//$_SESSION['imm_spvsr_email'] = $_SESSION['signer_ID_imm_spvsr'].'@sedl.org';

if($_SESSION['current_submitted_status'] == 'Revised'){
$revised_insert = 'revised ';
}else{
$revised_insert = '';
}


########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################


	if($_SESSION['total_other_signers2'] > 0){ //IF THE TIMESHEET REQUIRES OTHER BUDGET AUTHORITY SIGNATURES BESIDES PBA
		
		$other_signers = $_SESSION['other_signers2'];
			
			foreach($other_signers as $current){
				 
					$bgt_auth_email = stripslashes($current).'@sedl.org';
					
//SEND E-MAIL NOTIFICATION TO OTHER BUDGET AUTHORITIES BESIDES PBA

$to = $bgt_auth_email;
$subject = $_SESSION['signer_fullname_owner'].' has submitted a '.$revised_insert.'timesheet requiring your approval';
$message = 
'Dear Budget Authority,'."\n\n".

'A '.$revised_insert.'timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'. This timesheet includes time charged to one or more of your budget codes and requires your approval.'."\n\n".

'------------------------------------------------------------'."\n".
' TIMESHEET DETAILS'."\n".
'------------------------------------------------------------'."\n".
$_SESSION['timesheet_hrs_email_summary']."\n".
'------------------------------------------------------------'."\n\n".

'To approve this timesheet, click here: '."\n".
'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['signer_ID_imm_spvsr'].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';

mail($to, $subject, $message, $headers);
		
			}

	} else { //IF THE TIMESHEET DOES NOT REQUIRE OTHER BUDGET AUTHORITY SIGNATURES BESIDES PBA
	
			$pba_email = stripslashes($_SESSION['signer_ID_pba']).'@sedl.org';
			
//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY

$to = $pba_email;
$subject = $_SESSION['signer_fullname_owner'].' has submitted a '.$revised_insert.'timesheet requiring your approval';
$message = 
'Dear '.$_SESSION['signer_fullname_pba'].','."\n\n".

'A '.$revised_insert.'timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'. This timesheet includes time charged to one or more of your budget codes and requires your approval.'."\n\n".

'------------------------------------------------------------'."\n".
' TIMESHEET DETAILS'."\n".
'------------------------------------------------------------'."\n".
$_SESSION['timesheet_hrs_email_summary']."\n".
'------------------------------------------------------------'."\n\n".

'To approve this timesheet, click here: '."\n".
'http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';
			
$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['signer_ID_imm_spvsr'].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';

mail($to, $subject, $message, $headers);
	
	
	}
		
		
		
		
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################



} else {

$_SESSION['timesheet_signed_staff'] = '2'; //ADD CONTINGENCY FOR STAFF WITH NO BGT AUTH REP AND WHOSE IMM_SPVSR IS NOT PBA?
echo '<p>Error_999: timesheets_process.php (line 1033)';

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','Error_999: timesheets_process.php');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$_SESSION['timesheet_ID']);
$newrecord -> AddDBParam('affected_row_ID',$update_row);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


}
//echo '<p>action:'.$action;
//echo '<p>update_row:'.$update_row;
//echo '<p>ErrorCode:'.$_SESSION['timesheet_signed_staff'];
if($_SESSION['menu_type'] == 'spvsr_admin'){
header('Location: http://www.sedl.org/staff/sims/timesheets_spvsr_app.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&action=approve&payperiod='.$_SESSION['current_pay_period_end']);
}elseif($_SESSION['menu_type'] == 'ba_admin'){
header('Location: http://www.sedl.org/staff/sims/timesheets_ba_app.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&action=approve&payperiod='.$_SESSION['current_pay_period_end']);
}else{
header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
}

####################################
## END: IMMEDIATE SUPERVISOR SIGN ##
####################################

}
?>




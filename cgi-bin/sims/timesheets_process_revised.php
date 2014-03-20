<?php
session_start();

include_once('sims_checksession.php');

$action = $_GET['action'];
$update_row = $_GET['row_ID'];
$mod = $_GET['mod'];
$spvsr = $_GET['spvsr']; // ONLY CONTAINS A VALUE IF STAFF RE-SUBMITS FROM TIMESHEETS MENU AFTER A SPVSR OR PBA CHANGE
$pba = $_GET['pba']; // ONLY CONTAINS A VALUE IF STAFF RE-SUBMITS FROM TIMESHEETS MENU AFTER A SPVSR OR PBA CHANGE
$spvsr_is_pba = $_GET['sisba']; // ONLY CONTAINS A VALUE IF STAFF RE-SUBMITS FROM TIMESHEETS MENU AFTER A SPVSR OR PBA CHANGE -- THIS VARIABLE INDICATES IF SPVSR IS STILL PBA OR NOT


include_once('FX/FX.php');
include_once('FX/server_data.php');


if($action == 'staff_sign'){

#######################
## START: STAFF SIGN ##
#######################


####################################################################
## START: FIND THE TIME_HRS ROWS FOR THIS TIMESHEET ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('timesheets::c_row_ID_cwp',$update_row);
//$search -> AddDBParam('TimeRevisedStatus','1');

$searchResult = $search -> FMFind();


//echo $searchResult['errorCode'];
//echo '<br>RegularWkHrs FoundCount: '.$searchResult['foundCount'];
//print_r ($searchResult);
//$recordData = current($searchResult['data']);
####################################################################
## END: FIND THE TIME_HRS ROWS FOR THIS TIMESHEET ##
####################################################################


if($mod == 'new_spvsr'){
####################################################################
## START: UPDATE THE TIMESHEET HRS ROWS TO REFLECT NEW PBA LV HRS ##
####################################################################
foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = time_hrs 

	if(($searchData['BudgetCode'][0] == '')||($searchData['BudgetCode'][0] == 'not entered')){
		$update2 = new FX($serverIP,$webCompanionPort);
		$update2 -> SetDBData('SIMS_2.fp7','time_hrs');
		$update2 -> SetDBPassword($webPW,$webUN);
		$update2 -> AddDBParam('-recid',$searchData['c_cwp_row_ID'][0]);
		
		$update2 -> AddDBParam('BudgetAuthorityLocal',$_SESSION['primary_bgt_auth']);
		$update2 -> AddDBParam('TimeRevisedStatus','1');
		
		$updateResult2 = $update2 -> FMEdit();
		
		//$recordData = current($updateResult['data']);
		
		$revised_status_set = '1';
	}

}
##################################################################
## END: UPDATE THE TIMESHEET HRS ROWS TO REFLECT NEW PBA LV HRS ##
##################################################################
}

 
####################################################################
## START: BUILD BGT AUTHS REVISED ARRAY ##
####################################################################

$i = 0;
foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = time_hrs 
if($searchData['TimeRevisedStatus'][0] == '1'){	
	$bgt_auths_revised[$i] = $searchData['BudgetAuthorityLocal'][0]; //GET ARRAY OF BUDGET AUTHORITIES WITH REVISED HOURS
	$i++;
	}
}

if ($revised_status_set == ''){$bgt_auths_revised[0] = $_SESSION['primary_bgt_auth'];} // IF THERE ARE NO TIME HRS ROWS THAT HAVE BEEN REVISED, SET THE $bgt_auths_revised ARRAY TO PBA

$bgt_auths_revised2 = array_unique($bgt_auths_revised); //REMOVE ANY DUPLICATE REVISED BUDGET AUTHORITIES

$total_revised_signers = count($bgt_auths_revised2); //GET NUMBER OF BUDGET AUTHORITIES WITH REVISED HOURS
$_SESSION['total_revised_signers'] = $total_revised_signers;

//if(isset($key)){
if(in_array($searchData['timesheets::Signer_ID_pba'][0], $bgt_auths_revised2)){ //IF PBA IS IN ARRAY OF REVISED BUDGET AUTHORITIES
$key = array_search($searchData['timesheets::Signer_ID_pba'][0], $bgt_auths_revised2); //GET ARRAY KEY OF PBA FROM ARRAY OF REVISED BUDGET AUTHORITIES
unset($bgt_auths_revised2[$key]); //REMOVE PBA FROM ARRAY OF REVISED BUDGET AUTHORITIES BY UNSETTING ARRAY KEY
$bgt_auths_revised2 = array_values($bgt_auths_revised2); //GET NEW ARRAY OF BUDGET AUTHORITIES WITH REVISED HOURS BESIDES PRIMARY BUDGET AUTHORITY
}
$_SESSION['bgt_auths_revised2'] = $bgt_auths_revised2;
$total_bgt_auths_revised2 = count($bgt_auths_revised2); //GET NUMBER OF BUDGET AUTHORITIES WITH REVISED HOURS BESIDES PRIMARY BUDGET AUTHORITY
$_SESSION['total_bgt_auths_revised2'] = $total_bgt_auths_revised2;

##################################################################
## END: BUILD BGT AUTHS REVISED ARRAY ##
##################################################################
/*
echo '<p>$bgt_auths_revised[0]: '.$bgt_auths_revised[0];
echo '<p>$bgt_auths_revised[1]: '.$bgt_auths_revised[1];
echo '<p>$bgt_auths_revised[2]: '.$bgt_auths_revised[2];
echo '<p>$bgt_auths_revised[3]: '.$bgt_auths_revised[3];
echo '<p>$bgt_auths_revised[4]: '.$bgt_auths_revised[4];
echo '<p>$bgt_auths_revised[5]: '.$bgt_auths_revised[5];
echo '<p>$total_revised_signers: '.$total_revised_signers;

echo '<p>$bgt_auths_revised2[0]: '.$bgt_auths_revised2[0];
echo '<p>$bgt_auths_revised2[1]: '.$bgt_auths_revised2[1];
echo '<p>$bgt_auths_revised2[2]: '.$bgt_auths_revised2[2];
echo '<p>$bgt_auths_revised2[3]: '.$bgt_auths_revised2[3];
echo '<p>$bgt_auths_revised2[4]: '.$bgt_auths_revised2[4];
echo '<p>$bgt_auths_revised2[5]: '.$bgt_auths_revised2[5];
echo '<p>$total_bgt_auths_revised2: '.$total_bgt_auths_revised2;
echo '<p>'.$searchData['timesheets::Signer_ID_pba'][0]; 
echo '<p>$key: '.$key; 
echo '<p>';
print_r($bgt_auths_revised);
echo '<p>';
print_r($bgt_auths_revised2);

exit;
*/

########################################
## START: UPDATE THE TIMESHEET RECORD ##
########################################
$trigger = rand();

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);

if($_SESSION['timesheet_approval_not_required'] == '1'){ //CEO STATUS CHECK
$update -> AddDBParam('TimesheetSubmittedStatus','Approved');
//$update -> AddDBParam('Signer_status_owner','1');
//echo '<P>APPROVAL NOT REQUIRED';
}else{
$update -> AddDBParam('TimesheetSubmittedStatus','Revised');
$update -> AddDBParam('Signer_Timestamp_owner_revised_trigger',$trigger);
$update -> AddDBParam('Signer_status_imm_spvsr','');
$update -> AddDBParam('Signer_status_pba','');
$update -> AddDBParam('approved_by_auth_rep_status','');
$update -> AddDBParam('TimesheetBeingRevised','0');
//echo '<P>APPROVAL IS REQUIRED';
}

$update -> AddDBParam('print_flag','0');
$update -> AddDBParam('Signer_status_owner','1');
$update -> AddDBParam('Signer_ID_imm_spvsr',$_SESSION['immediate_supervisor']);
$update -> AddDBParam('Signer_ID_pba',$_SESSION['primary_bgt_auth']);
$update -> AddDBParam('total_oba_signers',$_SESSION['total_other_signers']);

if($mod == 'new_spvsr'){
$update -> AddDBParam('StaffImmediateSupervisor',$spvsr);
$update -> AddDBParam('signer_status_imm_spvsr','');
$update -> AddDBParam('signer_timestamp_imm_spvsr','');
$update -> AddDBParam('StaffPrimaryBudgetAuthority',$pba);
$update -> AddDBParam('signer_status_pba','');
$update -> AddDBParam('signer_timestamp_pba','');
$update -> AddDBParam('spvsr_is_pba',$spvsr_is_pba);
}


/*
if($_SESSION['signer_pba_is_spvsr'] == 1) { //IF PBA == IS

	$update -> AddDBParam('Signer_ID_bgt_auth_1',$_SESSION['primary_bgt_auth']);
	
	$i=2;
	foreach($_SESSION['other_signers'] as $current){
		
	$update -> AddDBParam('Signer_ID_bgt_auth_'.$i,$current);
	
	$i++;
	}

} else { //IF PBA != IS


	$update -> AddDBParam('Signer_ID_bgt_auth_1',$_SESSION['immediate_supervisor']);
	
	$i=2;
	foreach($_SESSION['other_signers'] as $current){
		
	$update -> AddDBParam('Signer_ID_bgt_auth_'.$i,$current);
	
	$i++;
	}


}

*/


//$update -> AddDBParam('Signer_ID_bgt_auth_OT',$_SESSION['sims_user_ID']);
$update -> AddDBParam('signatures_required',$_SESSION['signatures_required']);
$update -> AddDBParam('signatures_required_oba',$_SESSION['total_other_signers']);

$updateResult = $update -> FMEdit();

$recordData = current($updateResult['data']);
$_SESSION['timesheet_hrs_email_summary'] = $recordData['c_timesheet_hrs_email_summary'][0];
$_SESSION['current_pay_period_end'] = $recordData['c_PayPeriodEnd'][0];
$_SESSION['timesheet_ID'] = $recordData['TimesheetID'][0];
########################################
## END: UPDATE THE TIMESHEET RECORD ##
########################################



if($updateResult['errorCode']==0) { 

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_STAFF_REVISED');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$recordData['TimesheetID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$update_row);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecord -> AddDBParam('error_code',$updateResult['errorCode']);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


$_SESSION['timesheet_signed_staff'] = '1_revised';

$_SESSION['staff_has_ar'] = $recordData['staff::c_staff_has_time_leave_admin'][0];
$_SESSION['imm_spvsr_is_pba'] = $recordData['staff::c_cwp_spvsr_is_pba'][0];
$_SESSION['staff_ar'] = $recordData['staff::time_leave_admin_sims_user_ID'][0];
$_SESSION['staff_ar_email'] = $recordData['staff::time_leave_admin_email'][0];

if($_SESSION['timesheet_approval_not_required'] == '1'){ //CEO STATUS CHECK - NO APPROVAL REQUIRED
$_SESSION['timesheet_owner_email'] = $_SESSION['signer_ID_owner'].'@sedl.org';

######################################################
## START: TRIGGER NOTIFICATION E-MAIL TO CEO & AS ##
######################################################


		//SEND E-MAIL APPROVED NOTIFICATION TO STAFF MEMBER AND AS
		
			$to = $_SESSION['timesheet_owner_email'].',maria.turner@sedl.org';
			$subject = 'Your timesheet has been approved.';
			$message = 
			'Dear '.$_SESSION['signer_fullname_owner'].','."\n\n".
			
			'The revised timesheet you submitted for the pay period ending '.$_SESSION['current_pay_period_end'].' has been approved. No further action is necessary on your part.'."\n\n".
			
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
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
		
		
		
		
####################################################
## END: TRIGGER NOTIFICATION E-MAIL TO CEO & AS ##
####################################################
header('Location: http://www.sedl.org/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd');

exit;
}












########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################
if($_SESSION['staff_has_ar'] == 1){ //IF USER'S PRIMARY BGT AUTHORITY HAS AN AUTHORIZED TIMESHEET ADMIN


		//SEND E-MAIL NOTIFICATION TO PRIMARY BGT AUTHORITY'S TIMESHEET ADMIN
		
			$to = $_SESSION['staff_ar_email'];
			$subject = $_SESSION['signer_fullname_owner'].' has submitted a revised timesheet for '.$_SESSION['current_pay_period_end'];
			$message = 
			'Dear '.$_SESSION['approved_by_auth_rep_full_name'].','."\n\n".
			
			'A revised timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TIMESHEET DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			$_SESSION['timesheet_hrs_email_summary']."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To check the accuracy of this timesheet before submitting it for budget authority approval, click here: '."\n".
			'http://www.sedl.org/staff/sims/timesheets_approve.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&approver_ID='.$_SESSION['staff_ar'].'&submit_status=revised&src=eml'."\n\n".
			
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
		
		
		
		
} elseif($_SESSION['imm_spvsr_is_pba'] == '0') { //IF USER'S PRIMARY BGT AUTHORITY & IMMEDIATE SPVSR IS NOT THE SAME PERSON

		$_SESSION['imm_spvsr_email'] = stripslashes($_SESSION['immediate_supervisor']).'@sedl.org';
		
		//SEND E-MAIL NOTIFICATION TO IMMEDIATE SUPERVISOR
		
			$to = $_SESSION['imm_spvsr_email'];
			$subject = $_SESSION['signer_fullname_owner'].' has submitted a revised timesheet for '.$_SESSION['current_pay_period_end'];
			$message = 
			'Dear '.$_SESSION['signer_fullname_imm_spvsr'].','."\n\n".
			
			'A revised timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TIMESHEET DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			$_SESSION['timesheet_hrs_email_summary']."\n".
			'------------------------------------------------------------'."\n\n".

			'To approve this timesheet, click here: '."\n".
			'http://www.sedl.org/staff/sims/sims_menu.php?src-intr'."\n\n".
			
			'---------------------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'---------------------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);


}
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################


} else {

$_SESSION['timesheet_signed_staff'] = '2'; //ADD CONTINGENCY FOR STAFF WITH NO BGT AUTH REP AND WHOSE IMM_SPVSR = PBA?
echo 'ErrorCode 998: '.$_SESSION['signer_fullname_owner'].' has no bgt_auth_rep and IMM_SPVSR = PBA.';
}
//echo '<p>action:'.$action;
//echo '<p>update_row:'.$update_row;
//echo '<p>ErrorCode:'.$_SESSION['timesheet_signed_staff'];
header('Location: http://www.sedl.org/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd');


#######################
## END: STAFF SIGN ##
#######################

} elseif($action == 'staff_sign_ar'){

##########################
## START: STAFF-AR SIGN ##
##########################
$update_row = $_GET['row_ID'];

####################################################################
## START: FIND THE TIME_HRS ROWS TO BUILD BGT AUTHS REVISED ARRAY ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('timesheets::c_row_ID_cwp',$update_row);
$search -> AddDBParam('TimeRevisedStatus','1');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo '<br>RegularWkHrs FoundCount: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);


$i = 0;
foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = time_hrs 
$bgt_auths_revised[$i] = $searchData['BudgetAuthorityLocal'][0]; //GET ARRAY OF BUDGET AUTHORITIES WITH REVISED HOURS
$i++;
}

if($searchResult['foundCount'] == 0){$revised_status_set = '0';}
if($revised_status_set == '0'){$bgt_auths_revised[0] = $recordData['timesheets::StaffPrimaryBudgetAuthority'][0];} // IF THERE ARE NO TIME HRS ROWS THAT HAVE BEEN REVISED, SET THE $bgt_auths_revised ARRAY TO PBA

$bgt_auths_revised2 = array_unique($bgt_auths_revised);

$total_revised_signers = count($bgt_auths_revised2);
$_SESSION['total_revised_signers'] = $total_revised_signers;

//if(isset($key)){
if(in_array($searchData['timesheets::Signer_ID_pba'][0], $bgt_auths_revised2)){ //IF PBA IS IN ARRAY OF REVISED BUDGET AUTHORITIES
$key = array_search($searchData['timesheets::Signer_ID_pba'][0], $bgt_auths_revised2); //GET ARRAY KEY OF PBA FROM ARRAY OF REVISED BUDGET AUTHORITIES
unset($bgt_auths_revised2[$key]); //REMOVE PBA FROM ARRAY OF REVISED BUDGET AUTHORITIES BY UNSETTING ARRAY KEY
$bgt_auths_revised2 = array_values($bgt_auths_revised2); //GET NEW ARRAY OF BUDGET AUTHORITIES WITH REVISED HOURS BESIDES PRIMARY BUDGET AUTHORITY
}
$_SESSION['bgt_auths_revised2'] = $bgt_auths_revised2;
$total_bgt_auths_revised2 = count($bgt_auths_revised2); //GET NUMBER OF BUDGET AUTHORITIES WITH REVISED HOURS BESIDES PRIMARY BUDGET AUTHORITY
$_SESSION['total_bgt_auths_revised2'] = $total_bgt_auths_revised2;

##################################################################
## END: FIND THE TIME_HRS ROWS TO BUILD BGT AUTHS REVISED ARRAY ##
##################################################################

########################################
## START: UPDATE THE TIMESHEET RECORD ##
########################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);

$update -> AddDBParam('TimesheetSubmittedStatus','Revised');
$update -> AddDBParam('Signer_status_imm_spvsr','');
$update -> AddDBParam('Signer_status_pba','');
$update -> AddDBParam('total_oba_signers',$_SESSION['total_other_signers']);
$update -> AddDBParam('approved_by_auth_rep_status','1');
$update -> AddDBParam('print_flag','0');


/*
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
*/



//$update -> AddDBParam('Signer_ID_bgt_auth_OT',$_SESSION['sims_user_ID']);
$update -> AddDBParam('signatures_required',$_SESSION['signatures_required']);
$update -> AddDBParam('signatures_required_oba',$_SESSION['total_other_signers']);

$updateResult = $update -> FMEdit();

$recordData = current($updateResult['data']);
$_SESSION['timesheet_hrs_email_summary'] = $recordData['c_timesheet_hrs_email_summary'][0];
########################################
## END: UPDATE THE TIMESHEET RECORD ##
########################################




if($updateResult['errorCode']==0) { 

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_STAFF_AR_REVISED');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$recordData['TimesheetID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$update_row);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecord -> AddDBParam('error_code',$updateResult['errorCode']);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


$_SESSION['timesheet_signed_staff'] = '1_revised';

//$_SESSION['staff_has_ar'] = $recordData['staff::c_staff_has_time_leave_admin'][0];
$_SESSION['imm_spvsr_is_pba'] = $recordData['staff::c_cwp_spvsr_is_pba'][0];
//$_SESSION['staff_ar'] = $recordData['staff::time_leave_admin_sims_user_ID'][0];
//$_SESSION['staff_ar_email'] = $recordData['staff::time_leave_admin_email'][0];

########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################
if($_SESSION['imm_spvsr_is_pba'] == '0') { //IF USER'S PRIMARY BGT AUTHORITY & IMMEDIATE SPVSR IS NOT THE SAME PERSON

$imm_spvsr_email = stripslashes($_SESSION['immediate_supervisor']).'@sedl.org';
		
//SEND E-MAIL NOTIFICATION TO IMMEDIATE SUPERVISOR

$to = $imm_spvsr_email;
$subject = $_SESSION['signer_fullname_owner'].' has submitted a revised timesheet requiring your approval';
$message = 
'Dear '.$_SESSION['signer_fullname_imm_spvsr'].','."\n\n".

'A revised timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'.'."\n\n".

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
			
$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: sims@sedl.org';

mail($to, $subject, $message, $headers);






} elseif($_SESSION['imm_spvsr_is_pba'] == '1') { //IF USER'S PRIMARY BGT AUTHORITY & IMMEDIATE SPVSR IS THE SAME PERSON


	if($total_bgt_auths_revised2 > 0){ //IF THE TIMESHEET REQUIRES OTHER BUDGET AUTHORITY SIGNATURES BESIDES PBA (ONLY REVISED HRS)
		
		$other_signers = $bgt_auths_revised2;
			
			foreach($other_signers as $current){
				 
			$bgt_auth_email = stripslashes($current).'@sedl.org';
					
			//SEND E-MAIL NOTIFICATION TO OTHER BUDGET AUTHORITIES BESIDES PBA (REVISED ONLY)
			
			$to = $bgt_auth_email;
			$subject = $_SESSION['signer_fullname_owner'].' has submitted a revised timesheet requiring your approval';
			$message = 
			'Dear Budget Authority,'."\n\n".
			
			'A revised timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'. This timesheet includes time charged to one or more of your budget codes and requires your approval.'."\n\n".
			
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
								
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
		
			}

	} else { //IF THE TIMESHEET DOES NOT REQUIRE OTHER BUDGET AUTHORITY SIGNATURES BESIDES PBA
	
			$pba_email = stripslashes($_SESSION['signer_ID_pba']).'@sedl.org';
			
//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY

$to = $pba_email;
$subject = $_SESSION['signer_fullname_owner'].' has submitted a revised timesheet requiring your approval';
$message = 
'Dear '.$_SESSION['signer_fullname_pba'].','."\n\n".

'A revised timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'. This timesheet includes time charged to one or more of your budget codes and requires your approval.'."\n\n".

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
			
$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: sims@sedl.org';

mail($to, $subject, $message, $headers);
	
	
	}










}
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################


} else {

$_SESSION['timesheet_signed_staff'] = '2'; //ADD CONTINGENCY FOR STAFF WITH NO BGT AUTH REP AND WHOSE IMM_SPVSR = PBA?

}
//echo '<p>action:'.$action;
//echo '<p>update_row:'.$update_row;
//echo '<p>ErrorCode:'.$_SESSION['timesheet_signed_staff'];
header('Location: http://www.sedl.org/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd');


########################
## END: STAFF-AR SIGN ##
########################

} elseif($action == 'oba_sign') {

#######################
## START: OBA SIGN ##
#######################
$update_row = $_GET['row_ID'];
$bgt_auth = $_GET['bgt_auth'];


####################################################################
## START: FIND THE TIME_HRS ROWS TO BUILD BGT AUTHS REVISED ARRAY ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('timesheets::c_row_ID_cwp',$update_row);
$search -> AddDBParam('TimeRevisedStatus','1');

$searchResult = $search -> FMFind();


//echo '<br>RevisedHrs FoundCount: '.$searchResult['errorCode'];
//echo '<br>RevisedHrs FoundCount: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
$timesheetID = $recordData['Timesheet_ID'][0];

$i = 0;
foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = time_hrs 
$bgt_auths_revised[$i] = $searchData['BudgetAuthorityLocal'][0]; //GET ARRAY OF BUDGET AUTHORITIES WITH REVISED HOURS
$i++;
}

if($searchResult['foundCount'] == 0){$revised_status_set = '0';}
if($revised_status_set == '0'){$bgt_auths_revised[0] = $recordData['timesheets::StaffPrimaryBudgetAuthority'][0];} // IF THERE ARE NO TIME HRS ROWS THAT HAVE BEEN REVISED, SET THE $bgt_auths_revised ARRAY TO PBA

$bgt_auths_revised2 = array_unique($bgt_auths_revised); //REMOVE ANY DUPLICATE REVISED BUDGET AUTHORITIES

$total_revised_signers = count($bgt_auths_revised); //GET NUMBER OF BUDGET AUTHORITIES WITH REVISED HOURS
$_SESSION['total_revised_signers'] = $total_revised_signers;

//if(isset($key)){
if(in_array($searchData['timesheets::Signer_ID_pba'][0], $bgt_auths_revised2)){ //IF PBA IS IN ARRAY OF REVISED BUDGET AUTHORITIES
$key = array_search($searchData['timesheets::Signer_ID_pba'][0], $bgt_auths_revised2); //GET ARRAY KEY OF PBA FROM ARRAY OF REVISED BUDGET AUTHORITIES
unset($bgt_auths_revised2[$key]); //REMOVE PBA FROM ARRAY OF REVISED BUDGET AUTHORITIES BY UNSETTING ARRAY KEY
$bgt_auths_revised2 = array_values($bgt_auths_revised2); //GET NEW ARRAY OF BUDGET AUTHORITIES WITH REVISED HOURS BESIDES PRIMARY BUDGET AUTHORITY
}
$_SESSION['bgt_auths_revised2'] = $bgt_auths_revised2;
$total_bgt_auths_revised2 = count($bgt_auths_revised2); //GET NUMBER OF BUDGET AUTHORITIES WITH REVISED HOURS BESIDES PRIMARY BUDGET AUTHORITY
$_SESSION['total_bgt_auths_revised2'] = $total_bgt_auths_revised2;
##################################################################
## END: FIND THE TIME_HRS ROWS TO BUILD BGT AUTHS REVISED ARRAY ##
##################################################################

#########################################################
## START: UPDATE THE TIMESHEET RECORD IF SIGNER IS PBA ##
#########################################################
if($bgt_auth == $_SESSION['signer_ID_pba']){

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);

if($bgt_auth == $_SESSION['signer_ID_pba']){
$update -> AddDBParam('Signer_status_pba','1');
$update -> AddDBParam('TimesheetSubmittedStatus','Approved');
}

$updateResult = $update -> FMEdit();

$recordData = current($updateResult['data']);
$_SESSION['timesheet_hrs_email_summary'] = $recordData['c_timesheet_hrs_email_summary'][0];

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_PBA_REVISED');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$recordData['TimesheetID'][0]);
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
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_OBA_REVISED');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$recordData['TimesheetID'][0]);
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
//$recordData9 = current($searchResult['data']);
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
$_SESSION['timesheet_hrs_email_summary'] = $recordData2['timesheets::c_timesheet_hrs_email_summary'][0];
########################################
## END: UPDATE THE TIME_HRS RECORDS ##
########################################


if($updateResult['errorCode']==0) { 

if($bgt_auth == $_SESSION['signer_ID_pba']){ //IF PRIMARY BUDGET AUTHORITY JUST SIGNED TIMESHEET

$_SESSION['timesheet_owner_email'] = stripslashes($_SESSION['signer_ID_owner']).'@sedl.org';

########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################


//SEND E-MAIL APPROVED NOTIFICATION TO STAFF MEMBER AND AS

$to = $_SESSION['timesheet_owner_email'].',maria.turner@sedl.org';
$subject = 'Your timesheet has been approved.';
$message = 
'Dear '.$_SESSION['signer_fullname_owner'].','."\n\n".

'The revised timesheet you submitted for the pay period ending '.$_SESSION['current_pay_period_end'].' has been approved. No further action is necessary on your part.'."\n\n".

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

$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: sims@sedl.org';

mail($to, $subject, $message, $headers);
		
		
		
		
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################
header('Location: http://www.sedl.org/staff/sims/timesheets_ba_app.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&action=approve&payperiod='.$_SESSION['current_pay_period_end']);

exit;
}



if($recordData2['timesheets::c_sum_oba_time_hrs_approved'][0] > '0'){ //IF OTHER BUDGET AUTHORITIES STILL NEED TO SIGN THIS TIMESHEET

header('Location: http://www.sedl.org/staff/sims/timesheets_ba_app.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&action=view&payperiod='.$_SESSION['current_pay_period_end']);

} else { //IF ALL OTHER BUDGET AUTHORITIES HAVE NOW SIGNED THIS TIMESHEET

//$_SESSION['imm_spvsr_is_pba'] = $recordData['staff::c_cwp_spvsr_is_pba'][0];
$_SESSION['pba_email'] = stripslashes($_SESSION['signer_ID_pba']).'@sedl.org';

########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################


//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY

$to = $_SESSION['pba_email'];
$subject = $_SESSION['signer_fullname_owner'].' has submitted a revised timesheet for '.$_SESSION['current_pay_period_end'];
$message = 
'Dear '.$_SESSION['signer_fullname_pba'].','."\n\n".

'A revised timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'.'."\n\n".

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

$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: sims@sedl.org';

mail($to, $subject, $message, $headers);
		
		
		
		
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################

}

} else {

$_SESSION['timesheet_signed_staff'] = '2'; //ADD CONTINGENCY FOR STAFF WITH NO BGT AUTH REP AND WHOSE IMM_SPVSR = PBA?

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

if($bgt_auth == $_SESSION['signer_ID_pba']){
$update -> AddDBParam('Signer_status_pba','1');
$update -> AddDBParam('TimesheetSubmittedStatus','Approved');
}
$updateResult = $update -> FMEdit();

$recordData = current($updateResult['data']);
$_SESSION['timesheet_hrs_email_summary'] = $recordData['c_timesheet_hrs_email_summary'][0];

if($updateResult['errorCode']==0) { 

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_PBA_REVISED');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$recordData['TimesheetID'][0]);
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

########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################


//SEND E-MAIL APPROVED NOTIFICATION TO STAFF MEMBER AND AS

$to = $_SESSION['timesheet_owner_email'].',maria.turner@sedl.org';
$subject = 'Your timesheet has been approved.';
$message = 
'Dear '.$_SESSION['signer_fullname_owner'].','."\n\n".

'The revised timesheet you submitted for the pay period ending '.$_SESSION['current_pay_period_end'].' has been approved. No further action is necessary on your part.'."\n\n".

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

$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: sims@sedl.org';

mail($to, $subject, $message, $headers);
		
		
		
		
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################



} else {

$_SESSION['timesheet_signed_staff'] = '2'; //ADD CONTINGENCY FOR STAFF WITH NO BGT AUTH REP AND WHOSE IMM_SPVSR = PBA?

}
//echo '<p>action:'.$action;
//echo '<p>update_row:'.$update_row;
//echo '<p>ErrorCode:'.$_SESSION['timesheet_signed_staff'];
header('Location: http://www.sedl.org/staff/sims/timesheets_ba_app.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&action=approve&payperiod='.$_SESSION['current_pay_period_end']);


#######################
## END: PBA SIGN ##
#######################

} elseif($action == 'imm_spvsr_sign') {

######################################
## START: IMMEDIATE SUPERVISOR SIGN ##
######################################
$update_row = $_GET['row_ID'];
$bgt_auth = $_GET['bgt_auth'];


####################################################################
## START: FIND THE TIME_HRS ROWS TO BUILD BGT AUTHS REVISED ARRAY ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('timesheets::c_row_ID_cwp',$update_row);
$search -> AddDBParam('TimeRevisedStatus','1');

$searchResult = $search -> FMFind();


//echo $searchResult['errorCode'];
//echo '<br>RegularWkHrs FoundCount: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);


$i = 0;
foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = time_hrs 
$bgt_auths_revised[$i] = $searchData['BudgetAuthorityLocal'][0]; //GET ARRAY OF BUDGET AUTHORITIES WITH REVISED HOURS
$i++;
}

if($searchResult['foundCount'] == 0){$revised_status_set = '0';}
if($revised_status_set == '0'){$bgt_auths_revised[0] = $recordData['timesheets::StaffPrimaryBudgetAuthority'][0];} // IF THERE ARE NO TIME HRS ROWS THAT HAVE BEEN REVISED, SET THE $bgt_auths_revised ARRAY TO PBA

$bgt_auths_revised2 = array_unique($bgt_auths_revised); //REMOVE ANY DUPLICATE REVISED BUDGET AUTHORITIES

$total_revised_signers = count($bgt_auths_revised); //GET NUMBER OF BUDGET AUTHORITIES WITH REVISED HOURS
$_SESSION['total_revised_signers'] = $total_revised_signers;

//if(isset($key)){
if(in_array($searchData['timesheets::Signer_ID_pba'][0], $bgt_auths_revised2)){ //IF PBA IS IN ARRAY OF REVISED BUDGET AUTHORITIES
$key = array_search($searchData['timesheets::Signer_ID_pba'][0], $bgt_auths_revised2); //GET ARRAY KEY OF PBA FROM ARRAY OF REVISED BUDGET AUTHORITIES
unset($bgt_auths_revised2[$key]); //REMOVE PBA FROM ARRAY OF REVISED BUDGET AUTHORITIES BY UNSETTING ARRAY KEY
$bgt_auths_revised2 = array_values($bgt_auths_revised2); //GET NEW ARRAY OF BUDGET AUTHORITIES WITH REVISED HOURS BESIDES PRIMARY BUDGET AUTHORITY
}

if(in_array($searchData['timesheets::Signer_ID_imm_spvsr'][0], $bgt_auths_revised2)){ //IF IMM_SPVSR IS IN ARRAY OF REVISED BUDGET AUTHORITIES
$key2 = array_search($searchData['timesheets::Signer_ID_imm_spvsr'][0], $bgt_auths_revised2); //GET ARRAY KEY OF IMM_SPVSR FROM ARRAY OF REVISED BUDGET AUTHORITIES
unset($bgt_auths_revised2[$key2]); //REMOVE IMM_SPVSR FROM ARRAY OF REVISED BUDGET AUTHORITIES BY UNSETTING ARRAY KEY
$bgt_auths_revised2 = array_values($bgt_auths_revised2); //GET NEW ARRAY OF BUDGET AUTHORITIES WITH REVISED HOURS BESIDES IMM_SPVSR
}

$_SESSION['bgt_auths_revised2'] = $bgt_auths_revised2;
$total_bgt_auths_revised2 = count($bgt_auths_revised2); //GET NUMBER OF BUDGET AUTHORITIES WITH REVISED HOURS BESIDES IMM_SPVSR AND PRIMARY BUDGET AUTHORITY
$_SESSION['total_bgt_auths_revised2'] = $total_bgt_auths_revised2;

##################################################################
## END: FIND THE TIME_HRS ROWS TO BUILD BGT AUTHS REVISED ARRAY ##
##################################################################


########################################
## START: UPDATE THE TIMESHEET RECORD ##
########################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);

$update -> AddDBParam('Signer_status_imm_spvsr','1');
if($bgt_auth == $_SESSION['signer_ID_pba']){
$update -> AddDBParam('Signer_status_pba','1');
$update -> AddDBParam('TimesheetSubmittedStatus','Approved');
}

$updateResult = $update -> FMEdit();

$recordData = current($updateResult['data']);
$_SESSION['timesheet_hrs_email_summary'] = $recordData['c_timesheet_hrs_email_summary'][0];
########################################
## END: UPDATE THE TIMESHEET RECORD ##
########################################

if($updateResult['errorCode']==0) { 

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_TIMESHEET_IMM_SPVSR_REVISED');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$recordData['TimesheetID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$update_row);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecord -> AddDBParam('error_code',$updateResult['errorCode']);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


//$_SESSION['imm_spvsr_is_pba'] = $recordData['staff::c_cwp_spvsr_is_pba'][0];
//$_SESSION['imm_spvsr_email'] = $_SESSION['signer_ID_imm_spvsr'].'@sedl.org';


###################################################################################################################
## START: FIND THE TIME_HRS RECORDS RELATED TO THIS BGT AUTHORITY -- IF THE IMM SPVSR IS ALSO A BUDGET AUTHORITY ##
###################################################################################################################
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
#################################################################################################################
## END: FIND THE TIME_HRS RECORDS RELATED TO THIS BGT AUTHORITY -- IF THE IMM SPVSR IS ALSO A BUDGET AUTHORITY ##
#################################################################################################################
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


########################################
## START: TRIGGER NOTIFICATION E-MAIL ##
########################################


	if($total_bgt_auths_revised2 > 0){ //IF THE TIMESHEET REQUIRES OTHER BUDGET AUTHORITY SIGNATURES BESIDES PBA (REVISED ONLY)
		
		$other_signers = $_SESSION['bgt_auths_revised2'];
			
			foreach($other_signers as $current){
				 
					$bgt_auth_email = stripslashes($current).'@sedl.org';
					
					//SEND E-MAIL NOTIFICATION TO OTHER BUDGET AUTHORITIES BESIDES PBA
					
					$to = $bgt_auth_email;
					$subject = $_SESSION['signer_fullname_owner'].' has submitted a revised timesheet requiring your approval';
					$message = 
					'Dear Budget Authority,'."\n\n".
					
					'A revised timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'. This timesheet includes time charged to one or more of your budget codes and requires your approval.'."\n\n".
					
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
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['signer_ID_imm_spvsr'].'@sedl.org'."\r\n".'Bcc: sims@sedl.org';
					
					mail($to, $subject, $message, $headers);
		
			}

	} else { //IF THE TIMESHEET DOES NOT REQUIRE OTHER BUDGET AUTHORITY SIGNATURES BESIDES PBA (REVISED ONLY)
	
			$pba_email = stripslashes($_SESSION['signer_ID_pba']).'@sedl.org';
			
			//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY
			
			$to = $_SESSION['pba_email'];
			$subject = $_SESSION['signer_fullname_owner'].' has submitted a revised timesheet requiring your approval';
			$message = 
			'Dear '.$_SESSION['signer_fullname_pba'].','."\n\n".
			
			'A revised timesheet for '.$_SESSION['signer_fullname_owner'].' has been submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'. This timesheet includes time charged to one or more of your budget codes and requires your approval.'."\n\n".
			
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
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['signer_ID_imm_spvsr'].'@sedl.org'."\r\n".'Bcc: sims@sedl.org';
			
			mail($to, $subject, $message, $headers);
	
	
	}
		
		
		
		
########################################
## END: TRIGGER NOTIFICATION E-MAIL ##
########################################



} else {

$_SESSION['timesheet_signed_staff'] = '2'; //ADD CONTINGENCY FOR STAFF WITH NO BGT AUTH REP AND WHOSE IMM_SPVSR = PBA?
// echo '<p>Error_999';

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','Error_999: timesheets_process_revised.php');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$recordData['TimesheetID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$update_row);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


}
//echo '<p>action:'.$action;
//echo '<p>update_row:'.$update_row;
//echo '<p>ErrorCode:'.$_SESSION['timesheet_signed_staff'];
header('Location: http://www.sedl.org/staff/sims/timesheets_spvsr_app.php?Timesheet_ID='.$_SESSION['timesheet_ID'].'&action=approve&payperiod='.$_SESSION['current_pay_period_end']);


####################################
## END: IMMEDIATE SUPERVISOR SIGN ##
####################################

}
?>

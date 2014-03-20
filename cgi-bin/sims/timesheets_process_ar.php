<?php
session_start();

//include_once('sims_checksession.php');

include_once('FX/FX.php');
include_once('FX/server_data.php');

################################################################################
## START: TRIGGER E-MAIL NOTIFICATIONS WHEN AUTHORIZED REP APPROVES TIMESHEET ##
################################################################################
if($_SESSION['signer_pba_is_spvsr'] == 1){ //IF THE TIMESHEET OWNER'S IMMEDIATE SUPERVISOR AND PRIMARY BUDGET AUTHORITY IS THE SAME PERSON

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
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['approved_by_auth_rep'].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
					
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
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['approved_by_auth_rep'].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
	
	
	}
			
		
} elseif($_SESSION['signer_pba_is_spvsr'] != '1') { //IF THE TIMESHEET OWNER'S IMMEDIATE SUPERVISOR AND PRIMARY BUDGET AUTHORITY IS NOT THE SAME PERSON

		$imm_spvsr_email = stripslashes($_SESSION['signer_ID_imm_spvsr']).'@sedl.org';
		
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
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['approved_by_auth_rep'].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);


}
##############################################################################
## END: TRIGGER E-MAIL NOTIFICATIONS WHEN AUTHORIZED REP APPROVES TIMESHEET ##
##############################################################################

?>




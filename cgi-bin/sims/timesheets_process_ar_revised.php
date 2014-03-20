<?php
session_start();

//include_once('sims_checksession.php');

include_once('FX/FX.php');
include_once('FX/server_data.php');

####################################################################
## START: FIND THE TIME_HRS ROWS TO BUILD BGT AUTHS REVISED ARRAY ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('timesheets::c_row_ID_cwp',$update_row);
$search -> AddDBParam('TimeRevisedStatus','1');
$search -> AddDBParam('BudgetAuthorityLocal',$_SESSION['signer_ID_owner'],'neq');

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
if(in_array($searchData['timesheets::Signer_ID_pba'][0], $bgt_auths_revised2)){
$key = array_search($searchData['timesheets::Signer_ID_pba'][0], $bgt_auths_revised2);
unset($bgt_auths_revised2[$key]); //REMOVE PBA FROM ARRAY OF REVISED BUDGET AUTHORITIES - ***MAKE ALL INSTANCES OF PBA ARE REMOVED NOT JUST THE FIRST ONE***
$bgt_auths_revised2 = array_values($bgt_auths_revised2); //GET ARRAY OF BUDGET AUTHORITIES WITH REVISED HOURS BESIDES PRIMARY BUDGET AUTHORITY
}
$_SESSION['bgt_auths_revised2'] = $bgt_auths_revised2;
$total_bgt_auths_revised2 = count($bgt_auths_revised2);
$_SESSION['total_bgt_auths_revised2'] = $total_bgt_auths_revised2;

##################################################################
## END: FIND THE TIME_HRS ROWS TO BUILD BGT AUTHS REVISED ARRAY ##
##################################################################

/*
echo '<p>$bgt_auths_revised[0]: '.$bgt_auths_revised[0];
echo '<p>$bgt_auths_revised[1]: '.$bgt_auths_revised[1];
echo '<p>$bgt_auths_revised[2]: '.$bgt_auths_revised[2];
echo '<p>$bgt_auths_revised[3]: '.$bgt_auths_revised[3];
echo '<p>$bgt_auths_revised[4]: '.$bgt_auths_revised[4];
echo '<p>$bgt_auths_revised[5]: '.$bgt_auths_revised[5];
echo '<p>$bgt_auths_revised[6]: '.$bgt_auths_revised[6];
echo '<p>$bgt_auths_revised[7]: '.$bgt_auths_revised[7];
echo '<p>$total_revised_signers: '.$total_revised_signers;

echo '<p>$bgt_auths_revised2[0]: '.$bgt_auths_revised2[0];
echo '<p>$bgt_auths_revised2[1]: '.$bgt_auths_revised2[1];
echo '<p>$bgt_auths_revised2[2]: '.$bgt_auths_revised2[2];
echo '<p>$bgt_auths_revised2[3]: '.$bgt_auths_revised2[3];
echo '<p>$bgt_auths_revised2[4]: '.$bgt_auths_revised2[4];
echo '<p>$bgt_auths_revised2[5]: '.$bgt_auths_revised2[5];
echo '<p>$bgt_auths_revised2[6]: '.$bgt_auths_revised2[6];
echo '<p>$bgt_auths_revised2[7]: '.$bgt_auths_revised2[7];
echo '<p>$total_bgt_auths_revised2: '.$total_bgt_auths_revised2;
echo '<p>'.$searchData['timesheets::Signer_ID_pba'][0]; 
echo '<p>$key: '.$key; 

exit;
*/



################################################################################
## START: TRIGGER E-MAIL NOTIFICATIONS WHEN AUTHORIZED REP APPROVES TIMESHEET ##
################################################################################
if($_SESSION['signer_pba_is_spvsr'] == 1){ //IF THE TIMESHEET OWNER'S IMMEDIATE SUPERVISOR AND PRIMARY BUDGET AUTHORITY IS THE SAME PERSON

	if($_SESSION['total_bgt_auths_revised2'] > 0){ //IF THE TIMESHEET REQUIRES OTHER BUDGET AUTHORITY SIGNATURES BESIDES PBA
		
		$other_signers = $_SESSION['bgt_auths_revised2']; //MAKE SURE THIS DOESN'T INCLUDE DUPLICATE PBA BUDGET CODES
			
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
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['approved_by_auth_rep'].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
					
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
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['approved_by_auth_rep'].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
	
	
	}
			
		
} elseif($_SESSION['signer_pba_is_spvsr'] != '1') { //IF THE TIMESHEET OWNER'S IMMEDIATE SUPERVISOR AND PRIMARY BUDGET AUTHORITY IS NOT THE SAME PERSON

		$imm_spvsr_email = stripslashes($_SESSION['signer_ID_imm_spvsr']).'@sedl.org';
		
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
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['approved_by_auth_rep'].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);


}
##############################################################################
## END: TRIGGER E-MAIL NOTIFICATIONS WHEN AUTHORIZED REP APPROVES TIMESHEET ##
##############################################################################

?>




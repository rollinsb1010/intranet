<?php
session_start();

include_once('sims_checksession.php');

if($_SESSION['user_ID'] == ''){
header('Location: http://www.sedl.org/staff/');
exit;
}
//if($_SESSION['personnel_action_admin_access'] !== 'Yes'){

//header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
//exit;
//}

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

$debug = 'off';
$paystub_access = 'yes';
$today = date("m/d/Y");

$action = $_REQUEST['action'];

if($action == ''){
$action = 'show_all';
}

$query = $_GET['query'];
//$today = '10/15/2008';
//echo '<p>$today: '.$today;
//echo '<p>$_SESSION[staff_ID]: '.$_SESSION['staff_ID'];
//echo '<p>$_SESSION[user_ID]: '.$_SESSION['user_ID'];
//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


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

if($action == 'show_all'){

#######################################
## START: GRAB CURRENT STAFF RECORDS ##
#######################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff_table','all');
$search -> SetDBPassword($webPW,$webUN);

if($query == 'former_staff'){
$search -> AddDBParam('current_employee_status','Former Employee');
$search -> AddDBParam('personnel_action_admin_sims_user_ID',$_SESSION['user_ID']);
$search -> AddDBParam('employee_type','Hourly','neq');
} else {
$search -> AddDBParam('current_employee_status','SEDL Employee');
$search -> AddDBParam('personnel_action_admin_sims_user_ID',$_SESSION['user_ID']);
$search -> AddDBParam('employee_type','Hourly','neq');
}

$search -> AddSortParam('c_full_name_last_first','ascend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);
#####################################
## END: GRAB CURRENT STAFF RECORDS ##
#####################################


###################################
## START: DISPLAY ALL STAFF LIST ##
###################################
 
 ?>

<html>
<head>
<title>SIMS - Personnel Actions</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Workgroup Personnel Actions</h1><hr /></td></tr>
			
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>Workgroup <?php if($query == 'former_staff'){?>Former <?php }?>Staff</strong> | <?php echo $searchResult['foundCount'];?> records found. | <?php if($query == 'former_staff'){?><a href="menu_personnel_actions_wg_admin.php?action=show_all">Show current staff</a><?php }else{?><a href="menu_personnel_actions_wg_admin.php?action=show_all&query=former_staff">Show former staff</a><?php }?></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILES-->


							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">Name</td><td class="body">Unit</td><td class="body">Title</td><td class="body">FTE</td><td class="body">Empl. Start Date</td><td class="body">Empl. Term. Date</td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body"><?php echo $searchData['staff_ID'][0];?></td><td class="body" nowrap><a href="menu_personnel_actions_wg_admin.php?action=show_1&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo stripslashes($searchData['c_full_name_last_first'][0]);?></a></td><td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td><td class="body"><?php echo $searchData['job_title'][0];?></td><td class="body"><?php echo $searchData['FTE_status'][0];?></td><td class="body"><?php echo $searchData['empl_start_date'][0];?></td><td class="body" nowrap><?php if($searchData['empl_end_date'][0] == ''){echo 'Current';}else{echo $searchData['empl_end_date'][0];}?></td></tr>
								<?php } ?>

<!--END FIRST SECTION: STAFF PROFILES-->		

							
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

}elseif($action == 'show_1'){ 

##############################################################
## START: FIND ALL PERSONNEL ACTIONS FOR THE SELECTED STAFF ##
##############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','personnel_actions','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$_GET['staff_ID']);
//$search -> AddDBParam('c_periodend_local',$today,'lte');
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('action_effective_date','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
//$_SESSION['timesheet_foundcount'] = $searchResult['foundCount'];
$recordData = current($searchResult['data']);
############################################################
## END: FIND ALL PERSONNEL ACTIONS FOR THE SELECTED STAFF ##
############################################################

##################################################
## START: GET SELECTED STAFF NAME AND WORKGROUP ##
##################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff_table','all');
$search2 -> SetDBPassword($webPW,$webUN);

$search2 -> AddDBParam('staff_ID',$_GET['staff_ID']);

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);

$fullname = $recordData2['name_timesheet'][0];
$unit = $recordData2['primary_SEDL_workgroup'][0];
################################################
## END: GET SELECTED STAFF NAME AND WORKGROUP ##
################################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Personnel Actions</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}


function preventView() { 
	alert ("This personnel action is currently being processed. You will receive an e-mail notification when this document has been approved.")
	return false;
}

</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Personnel Actions</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $fullname;?> (<?php echo $unit;?>)</b></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?><?php if($recordData2['personnel_action_admin_sims_user_ID'][0] == $_SESSION['user_ID']){?> | <a href="menu_personnel_actions_wg_admin.php?action=new&id=<?php echo $recordData2['staff_ID'][0];?>" title="Create new personnel action for this staff member">New personnel action</a><?php }?> | <a href="menu_personnel_actions_wg_admin.php?action=show_all" title="Return to workgroup Personnel Actions.">Workgroup staff</a> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body">Effective Date</td>
						<td class="body">Action Description</td>
						<td class="body">Transfer From</td>
						<td class="body">Assign To</td>
						<td class="body" align="right">Status</td>
						
						</tr>
						
						<?php if($searchResult['foundCount'] > 0) { ?>

							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							
							<tr>
							<td class="body" style="vertical-align:text-top"><?php echo $searchData['record_ID'][0];?></td>
							<td class="body" style="vertical-align:text-top"><a href="/staff/sims/menu_personnel_actions_wg_admin.php?record_ID=<?php echo $searchData['record_ID'][0];?>&action=show_action" title="Click here to view this personnel action." target="_blank" <?php if(($searchData['doc_release_status'][0] == '0')&&($_SESSION['user_ID'] == $searchData['staff::sims_user_ID'][0])){echo 'onClick="return preventView()"'; }?>><?php echo $searchData['action_effective_date'][0];?></a></td>
							<td class="body" style="vertical-align:text-top"><?php echo $searchData['action_descr'][0];?></td>
							<td class="body" style="vertical-align:text-top"><?php if($searchData['action_descr'][0] == 'Probationary Employment'){echo 'N/A';}else{ echo $searchData['transfer_from_title'][0];?><br>Pay grade: <?php echo $searchData['transfer_from_paygrade'][0];?><br>$<?php echo $searchData['transfer_from_actual_monthly_rate'][0];?>/mo.<?php } ?></td>
							<td class="body" style="vertical-align:text-top"><?php echo $searchData['assign_to_title'][0];?><br>Pay grade: <?php echo $searchData['assign_to_paygrade'][0];?><br>$<?php echo $searchData['assign_to_actual_monthly_rate'][0];?>/mo.</td>
							<td class="body" align="right" style="vertical-align:text-top<?php if($searchData['c_approval_status'][0] == 'Pending'){echo ';color:#ff0000';}else{echo ';color:#0000ff';}?>"><?php echo $searchData['c_approval_status'][0];?></td>
							
							</tr>
				
							<?php } ?>

						<?php }else{  ?>

							<tr>
							<td class="body" colspan="6" style="vertical-align:text-top"><center>No personnel actions found for this staff member.</center></td>
							</tr>

						<?php } ?>

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

}elseif($action == 'show_action'){ 


if($_REQUEST['mod'] == 'submit_new'){

##################################################
## START: CREATE NEW PERSONNEL ACTION ##
##################################################
$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('SIMS_2.fp7','personnel_actions'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information

$newrecord -> AddDBParam('staff_ID',$_REQUEST['staff_ID']);
$newrecord -> AddDBParam('created_by',$_SESSION['user_ID']);
$newrecord -> AddDBParam('document_type','Regular');

if($_REQUEST['new_address'] == 'yes'){
$newrecord -> AddDBParam('staff_address',$_REQUEST['staff_address']);
$newrecord -> AddDBParam('staff_city',$_REQUEST['staff_city']);
$newrecord -> AddDBParam('staff_state',$_REQUEST['staff_state']);
$newrecord -> AddDBParam('staff_zip',$_REQUEST['staff_zip']);
}

if($_REQUEST['action_descr'] == 'other'){
$newrecord -> AddDBParam('action_descr',$_REQUEST['action_descr_other']);
}else{

	if($_REQUEST['action_descr'] == 'Lateral Transfer'){
	
	$newrecord -> AddDBParam('action_descr',$_REQUEST['action_descr'].' ('.$_REQUEST['lateral_transfer_unit_from'].' to '.$_REQUEST['lateral_transfer_unit_to'].')');
	
	}else{
	
	$newrecord -> AddDBParam('action_descr',$_REQUEST['action_descr']);

	}

}

$newrecord -> AddDBParam('action_effective_date',$_REQUEST['action_effective_date_m'].'/'.$_REQUEST['action_effective_date_d'].'/'.$_REQUEST['action_effective_date_y']);

$newrecord -> AddDBParam('assign_to_title',$_REQUEST['assign_to_title']);
$newrecord -> AddDBParam('assign_to_paygrade',$_REQUEST['assign_to_paygrade']);
$newrecord -> AddDBParam('assign_to_actual_monthly_rate',$_REQUEST['assign_to_actual_monthly_rate']);
$newrecord -> AddDBParam('assign_to_title2',$_REQUEST['assign_to_title2']);
$newrecord -> AddDBParam('assign_to_paygrade2',$_REQUEST['assign_to_paygrade2']);
$newrecord -> AddDBParam('assign_to_actual_monthly_rate2',$_REQUEST['assign_to_actual_monthly_rate2']);

$newrecord -> AddDBParam('transfer_from_title',$_REQUEST['transfer_from_title']);
$newrecord -> AddDBParam('transfer_from_paygrade',$_REQUEST['transfer_from_paygrade']);
$newrecord -> AddDBParam('transfer_from_actual_monthly_rate',$_REQUEST['transfer_from_actual_monthly_rate']);
$newrecord -> AddDBParam('transfer_from_title2',$_REQUEST['transfer_from_title2']);
$newrecord -> AddDBParam('transfer_from_paygrade2',$_REQUEST['transfer_from_paygrade2']);
$newrecord -> AddDBParam('transfer_from_actual_monthly_rate2',$_REQUEST['transfer_from_actual_monthly_rate2']);

$newrecord -> AddDBParam('percent_time_employed',($_REQUEST['percent_time_employed_assign_to_position1']+$_REQUEST['percent_time_employed_assign_to_position2'])*100);
$newrecord -> AddDBParam('percent_time_employed_assign_to_position1',$_REQUEST['percent_time_employed_assign_to_position1']*100);
$newrecord -> AddDBParam('percent_time_employed_assign_to_position2',$_REQUEST['percent_time_employed_assign_to_position2']*100);
$newrecord -> AddDBParam('percent_time_employed_transfer_from_position1',$_REQUEST['percent_time_employed_transfer_from_position1']*100);
$newrecord -> AddDBParam('percent_time_employed_transfer_from_position2',$_REQUEST['percent_time_employed_transfer_from_position2']*100);
$newrecord -> AddDBParam('exempt_status',$_REQUEST['exempt_status']);

$newrecord -> AddDBParam('base_monthly_salary',$_REQUEST['assign_to_actual_monthly_rate']/$_REQUEST['fte']);
$newrecord -> AddDBParam('base_annual_salary',($_REQUEST['assign_to_actual_monthly_rate']/$_REQUEST['fte'])*12);

$newrecord -> AddDBParam('benefits_ss',$_REQUEST['benefits_ss']);
$newrecord -> AddDBParam('benefits_tiaa_cref',$_REQUEST['benefits_tiaa_cref']);
$newrecord -> AddDBParam('benefits_health_ins',$_REQUEST['benefits_health_ins']);
$newrecord -> AddDBParam('benefits_dental_ins',$_REQUEST['benefits_dental_ins']);
$newrecord -> AddDBParam('benefits_life_ins',$_REQUEST['benefits_life_ins']);
$newrecord -> AddDBParam('benefits_disab_ins',$_REQUEST['benefits_disab_ins']);
$newrecord -> AddDBParam('benefits_add_ins',$_REQUEST['benefits_add_ins']);
$newrecord -> AddDBParam('benefits_bus_van_parking',$_REQUEST['benefits_bus_van_parking']);
$newrecord -> AddDBParam('benefits_fsa',$_REQUEST['benefits_fsa']);

$newrecord -> AddDBParam('action_remarks',$_REQUEST['action_remarks']);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$newrecordData = current($newrecordResult['data']);
##################################################
## END: CREATE NEW PERSONNEL ACTION ##
##################################################
$record_ID = $newrecordData['record_ID'][0];

if($newrecordResult['errorCode'] == 0){
$newrecordcreated = '1';

##########################################################
## START: SEND E-MAIL NOTIFICATION TO PBA ##
##########################################################
//$to = 'ewaters@sedl.org';
$to = $newrecordData['signer_ID_pba'][0].'@sedl.org';
$subject = 'SIMS: PERSONNEL ACTION RECEIVED';
$message = 
'Unit Budget Authority:'."\n\n".

'A new Personnel Action has been received by SIMS for staff member ('.$newrecordData['staff::c_full_name_first_last'][0].') that requires your approval.'."\n\n".

'------------------------------------------------------------'."\n".
' PERSONNEL ACTION DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Submitted by: '.$_SESSION['user_ID']."\n".
'Staff Member: '.$newrecordData['staff::c_full_name_first_last'][0]."\n".
'Action: '.$newrecordData['action_descr'][0]."\n".
'------------------------------------------------------------'."\n\n".

'To review and approve this personnel action, click here:'."\n".

'http://www.sedl.org/staff/sims/menu_personnel_actions_wg_admin.php?action=show_action&record_ID='.$newrecordData['record_ID'][0]."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';

mail($to, $subject, $message, $headers);
########################################################
## END: SEND E-MAIL NOTIFICATION TO PBA ##
########################################################

}else{
$newrecordcreated = '2';
$newrecorderror = $newrecordResult['errorCode'];
}

}else{
$record_ID = $_GET['record_ID'];
}

if($_REQUEST['mod'] == 'pba'){
##################################################
## START: SIGN FORM AS PBA ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_actions');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('sign_status_pba','1');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: SIGN FORM AS PBA ##
##################################################

if($updateResult['errorCode'] == '0'){

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','SIGN_PERSONNEL_ACTION_PBA');
	$newrecord -> AddDBParam('table','PERSONNEL_ACTIONS');
	$newrecord -> AddDBParam('object_ID',$updateData['record_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$c_row_ID);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

##########################################################
## START: SEND E-MAIL NOTIFICATION TO SIGNER ##
##########################################################
//$to = 'ewaters@sedl.org';
$to = $updateData['signer_ID_hr'][0].'@sedl.org';
$subject = 'SIMS: PERSONNEL ACTION RECEIVED';
$message = 
'HR Generalist:'."\n\n".

'A new Personnel Action has been received by SIMS for staff member ('.$updateData['staff::c_full_name_first_last'][0].') that requires your approval.'."\n\n".

'------------------------------------------------------------'."\n".
' PERSONNEL ACTION DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Submitted by: '.$_SESSION['user_ID']."\n".
'Staff Member: '.$updateData['staff::c_full_name_first_last'][0]."\n".
'Action: '.$updateData['action_descr'][0]."\n".
'------------------------------------------------------------'."\n\n".

'To review and approve this personnel action, click here:'."\n".

'http://www.sedl.org/staff/sims/menu_personnel_actions_wg_admin.php?action=show_action&record_ID='.$updateData['record_ID'][0]."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org'."\r\n".'Cc: mturner@sedl.org';

mail($to, $subject, $message, $headers);
########################################################
## END: SEND E-MAIL NOTIFICATION TO SIGNER ##
########################################################
}
}

if($_REQUEST['mod'] == 'hr'){
##################################################
## START: SIGN FORM AS HR ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_actions');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('sign_status_hr','1');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: SIGN FORM AS HR ##
##################################################

if($updateResult['errorCode'] == '0'){

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','SIGN_PERSONNEL_ACTION_HR');
	$newrecord -> AddDBParam('table','PERSONNEL_ACTIONS');
	$newrecord -> AddDBParam('object_ID',$updateData['record_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$c_row_ID);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

##########################################################
## START: SEND E-MAIL NOTIFICATION TO SIGNER ##
##########################################################
//$to = 'ewaters@sedl.org';
$to = $updateData['signer_ID_cfo'][0].'@sedl.org';
$subject = 'SIMS: PERSONNEL ACTION RECEIVED';
$message = 
'CFO:'."\n\n".

'A new Personnel Action has been received by SIMS for staff member ('.$updateData['staff::c_full_name_first_last'][0].') that requires your approval.'."\n\n".

'------------------------------------------------------------'."\n".
' PERSONNEL ACTION DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Submitted by: '.$_SESSION['user_ID']."\n".
'Staff Member: '.$updateData['staff::c_full_name_first_last'][0]."\n".
'Action: '.$updateData['action_descr'][0]."\n".
'------------------------------------------------------------'."\n\n".

'To review and approve this personnel action, click here:'."\n".

'http://www.sedl.org/staff/sims/menu_personnel_actions_wg_admin.php?action=show_action&record_ID='.$updateData['record_ID'][0]."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';

mail($to, $subject, $message, $headers);
########################################################
## END: SEND E-MAIL NOTIFICATION TO SIGNER ##
########################################################
}
}

if($_REQUEST['mod'] == 'cfo'){
##################################################
## START: SIGN FORM AS CFO ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_actions');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('sign_status_cfo','1');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: SIGN FORM AS CFO ##
##################################################

if($updateResult['errorCode'] == '0'){

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','SIGN_PERSONNEL_ACTION_CFO');
	$newrecord -> AddDBParam('table','PERSONNEL_ACTIONS');
	$newrecord -> AddDBParam('object_ID',$updateData['record_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$c_row_ID);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

##########################################################
## START: SEND E-MAIL NOTIFICATION TO SIGNER ##
##########################################################
//$to = 'ewaters@sedl.org';
$to = $updateData['signer_ID_ceo'][0].'@sedl.org';
$subject = 'SIMS: PERSONNEL ACTION RECEIVED';
$message = 
'CEO:'."\n\n".

'A new Personnel Action has been received by SIMS for staff member ('.$updateData['staff::c_full_name_first_last'][0].') that requires your approval.'."\n\n".

'------------------------------------------------------------'."\n".
' PERSONNEL ACTION DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Submitted by: '.$_SESSION['user_ID']."\n".
'Staff Member: '.$updateData['staff::c_full_name_first_last'][0]."\n".
'Action: '.$updateData['action_descr'][0]."\n".
'------------------------------------------------------------'."\n\n".

'To review and approve this personnel action, click here:'."\n".

'http://www.sedl.org/staff/sims/menu_personnel_actions_wg_admin.php?action=show_action&record_ID='.$updateData['record_ID'][0]."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';

mail($to, $subject, $message, $headers);
########################################################
## END: SEND E-MAIL NOTIFICATION TO SIGNER ##
########################################################
}
}

if($_REQUEST['mod'] == 'ceo'){
##################################################
## START: SIGN FORM AS CEO ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_actions');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('sign_status_ceo','1');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: SIGN FORM AS CEO ##
##################################################

if($updateResult['errorCode'] == '0'){

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','SIGN_PERSONNEL_ACTION_CEO');
	$newrecord -> AddDBParam('table','PERSONNEL_ACTIONS');
	$newrecord -> AddDBParam('object_ID',$updateData['record_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$c_row_ID);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

##########################################################
## START: SEND E-MAIL APPROVAL NOTIFICATION TO HR ##
##########################################################
//$to = 'ewaters@sedl.org';
$to = $updateData['signer_ID_hr'][0].'@sedl.org';
$subject = 'SIMS: PERSONNEL ACTION APPROVED';
$message = 
'HR Generalist:'."\n\n".

'A new Personnel Action has been approved by the CEO for staff member ('.$updateData['staff::c_full_name_first_last'][0].').'."\n\n".

'------------------------------------------------------------'."\n".
' PERSONNEL ACTION DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Submitted by: '.$_SESSION['user_ID']."\n".
'Staff Member: '.$updateData['staff::c_full_name_first_last'][0]."\n".
'Action: '.$updateData['action_descr'][0]."\n".
'------------------------------------------------------------'."\n\n".

'This personnel action approval has not been released to the staff member. To review, print, and/or release this personnel action, click here:'."\n".

'http://www.sedl.org/staff/sims/menu_personnel_actions_admin.php?action=show_action&record_ID='.$updateData['record_ID'][0]."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';

mail($to, $subject, $message, $headers);
########################################################
## END: SEND E-MAIL APPROVAL NOTIFICATION TO HR ##
########################################################
}
/*
##########################################################
## START: SEND E-MAIL NOTIFICATION TO SIGNER ##
##########################################################
$to = 'sliberty@sedl.org'; //ewaters@sedl.org';
//$to = $updateData['staff::sims_user_ID'][0].'@sedl.org';
$subject = 'SIMS: PERSONNEL ACTION APPROVED';
$message = 
'Dear '.$updateData['staff::c_full_name_first_last'][0].','."\n\n".

'Your recent Personnel Action has been approved.'."\n\n".

'DATA RECEIVED: '."\n\n".
'Submitted by: '.$_SESSION['user_ID']."\n".
'Staff Member: '.$updateData['staff::c_full_name_first_last'][0]."\n".
'Action: '.$updateData['action_descr'][0]."\n\n".

'----------'."\n\n".

'To review and print a copy of this personnel action, click here:'."\n".

'http://www.sedl.org/staff/sims/menu_personnel_actions_wg_admin.php?action=show_action&record_ID='.$updateData['record_ID'][0]."\n\n".

'----------'."\n\n".

'This is an automated message from SIMS.';

$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org'.'Cc: '.$updateData['created_by'][0].'@sedl.org, mturner@sedl.org, sliberty@sedl.org';

mail($to, $subject, $message, $headers);
########################################################
## END: SEND E-MAIL NOTIFICATION TO SIGNER ##
########################################################
*/
#####################################################################
## START: UPDATE STAFF PROFILE TO REFLECT PERSONNEL ACTION CHANGES ##
#####################################################################
$update2 = new FX($serverIP,$webCompanionPort);
$update2 -> SetDBData('SIMS_2.fp7','staff_table');
$update2 -> SetDBPassword($webPW,$webUN);
$update2 -> AddDBParam('-recid',$updateData['staff::c_cwp_row_ID'][0]);
$update2 -> AddDBParam('job_title',$updateData['assign_to_title'][0]);
$update2 -> AddDBParam('pay_grade',$updateData['assign_to_paygrade'][0]);
$update2 -> AddDBParam('pay_rate',$updateData['assign_to_actual_monthly_rate'][0]);
$update2 -> AddDBParam('FTE_status',$updateData['percent_time_employed'][0]/100);
$update2 -> AddDBParam('employee_type',$updateData['exempt_status'][0]);

$updateResult2 = $update2 -> FMEdit();

//echo  '<p>errorCode: '.$updateResult2['errorCode'];
//echo  '<p>foundCount: '.$updateResult2['foundCount'];
$updateData2 = current($updateResult2['data']);
###################################################################
## END: UPDATE STAFF PROFILE TO REFLECT PERSONNEL ACTION CHANGES ##
###################################################################
}

##################################################
## START: FIND PERSONNEL ACTIONS FOR THIS USER ##
##################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','personnel_actions');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('record_ID','=='.$record_ID);
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam($sortfield,'PERIODEND');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
################################################
## END: FIND PERSONNEL ACTIONS FOR THIS USER ##
################################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Personnel Actions</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Kameron:400,700' rel='stylesheet' type='text/css'>
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>

<script language="JavaScript">

function confirmSign() { 
	var answer = confirm ("Sign this Personnel Action?")
	if (!answer) {
	return false;
	}
}

function preventSignPBA() { 
	alert ("This signature box is reserved for the Unit Budget Authority. To sign this personnel action, click the box with your ID.")
	return false;
}

function preventSignHR() { 
	alert ("This signature box is reserved for the HR Generalist. To sign this personnel action, click the box with your ID.")
	return false;
}

function preventSignCFO() { 
	alert ("This signature box is reserved for the CFO. To sign this personnel action, click the box with your ID.")
	return false;
}

function preventSignCEO() { 
	alert ("This signature box is reserved for the CEO. To sign this personnel action, click the box with your ID.")
	return false;
}

function preventApproveCEO() { 
	alert ("This personnel action must be signed by unit budget authority, HR, and fiscal before final CEO approval. You will receive an e-mail when final approval is required.")
	return false;
}

</script>

<style type="text/css">
table{  }
table.stub td {
	color: #000000;
	font-family: 'Kameron', serif;
	font-size:13px;
	background-color:#ffffff;
	padding:0px ;
	border-width:0px;
	padding-right:20px;
	padding-top:2px;
	padding-bottom:2px;
	margin:0px;
	vertical-align: text-top;
	white-space: nowrap;
}


hr.ee {
border: none 0;
border-top: 1px dotted #000000;
width: 100%;
height: 1px;
margin: 0px;
text-align: left;
padding: 0px;
}

h1 {font-family: 'Kameron', serif; text-shadow: #ffffff 0px 0px 0px; color:#000000; padding:3px;}
h2 {font-family: 'Kameron', serif; text-shadow: #ffffff 0px 0px 0px; font-size:16px; color:#000000;}
th { 	font-family: 'Kameron', serif; }


</style>


</head>

<BODY BGCOLOR="#FFFFFF" onLoad="zoomWindow()">

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td colspan="2" align="right" style="vertical-align:text-top;padding:6px;border:0px"><img src="/images/print_sedl.jpg" width="20%"></td></tr>

<?php if($newrecordcreated == '1'){?>
<tr><td colspan="2" style="vertical-align:text-top;padding:6px;border:0px"><div class="alert_small">New Personnel Action successfully created - notification sent to unit budget authority. | <a href="menu_personnel_actions_wg_admin.php?action=show_1&staff_ID=<?php echo $recordData['staff_ID'][0];?>">Close document</a></div></td></tr>
<?php }?>

<?php if($newrecordcreated == '2'){?>
<tr><td colspan="2" style="vertical-align:text-top;padding:6px;border:0px"><div class="alert_small">There was an error in processing your request (Errorcode: <?php echo $newrecorderror;?>). Please contact <a href="mailto:sims@sedl.org">sims@sedl.org</a> for assistance. | <a href="sims_menu.php">Close document</a></div></td></tr>
<?php }?>


<tr><td width="50%" style="vertical-align:text-top;">

	<h1>PERSONNEL ACTION</h1>

</td><td width="50%" style="vertical-align:text-top;padding:6px;">

	<div>
	<div style="padding:3px; text-align:right"><strong>DATE PREPARED</strong>: <?php echo $recordData['date_prepared'][0];?></div>
	</div>

</td></tr>



<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>NAME OF STAFF MEMBER:</strong><br>
	<?php echo $recordData['staff_name'][0];?>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">
	<strong>ADDRESS:</strong><br>
	<?php echo $recordData['staff_address'][0];?><br>
	<?php echo $recordData['staff_city'][0];?>, <?php echo $recordData['staff_state'][0];?> <?php echo $recordData['staff_zip'][0];?>

</td></tr>






<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>NATURE OF ACTION:</strong><br>
	<?php echo $recordData['action_descr'][0];?>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>EFFECTIVE DATE OF ACTION:</strong><br>
	<?php echo $recordData['action_effective_date'][0];?>

</td></tr>






<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>ASSIGN TO:</strong><br>

<?php if($recordData['assign_to_title2'][0] == ''){ ?>

	<?php echo $recordData['assign_to_title'][0];?><br>
	Pay Grade: <?php echo $recordData['assign_to_paygrade'][0];?>

<?php }else{ ?>

	<?php echo $recordData['assign_to_title'][0].' - Pay Grade '.$recordData['assign_to_paygrade'][0].' ('.$recordData['percent_time_employed_assign_to_position1'][0].'%)';?><br>
	<?php echo $recordData['assign_to_title2'][0].' - Pay Grade '.$recordData['assign_to_paygrade2'][0].' ('.$recordData['percent_time_employed_assign_to_position2'][0].'%)';?>

<?php } ?>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>ACTUAL MONTHLY RATE:</strong><br>

<?php if($recordData['assign_to_title2'][0] == ''){ ?>

	$<?php echo number_format($recordData['assign_to_actual_monthly_rate'][0],2,'.',',');?>/mo.

<?php }else{ ?>

	<?php echo '(Position 1: $'.number_format($recordData['assign_to_actual_monthly_rate'][0],2,'.',',').'/mo.)';?><br>
	<?php echo '(Position 2: $'.number_format($recordData['assign_to_actual_monthly_rate2'][0],2,'.',',').'/mo.)';?><br>
	<?php echo '(Combined: $'.number_format($recordData['assign_to_actual_monthly_rate'][0]+$recordData['assign_to_actual_monthly_rate2'][0],2,'.',',').'/mo.)';?>

<?php } ?>

</td></tr>






<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>TRANSFER FROM:</strong><br>
	<?php if($recordData['action_descr'][0] == 'Probationary Employment'){echo 'N/A';}else{?>
	
		<?php if($recordData['transfer_from_title2'][0] == ''){
	
		echo $recordData['transfer_from_title'][0].'<br>';
		echo 'Pay Grade: '.$recordData['transfer_from_paygrade'][0];
	
		}else{
		
		echo $recordData['transfer_from_title'][0].' - Pay Grade '.$recordData['transfer_from_paygrade'][0].' ('.$recordData['percent_time_employed_transfer_from_position1'][0].'%)<br>';
		echo $recordData['transfer_from_title2'][0].' - Pay Grade '.$recordData['transfer_from_paygrade2'][0].' ('.$recordData['percent_time_employed_transfer_from_position2'][0].'%)';
		
		}
	
	}?>
	
</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>ACTUAL MONTHLY RATE:</strong><br>

	<?php if($recordData['action_descr'][0] == 'Probationary Employment'){echo 'N/A';}else{
		
		if($recordData['transfer_from_title2'][0] == ''){

		echo '$'.number_format($recordData['transfer_from_actual_monthly_rate'][0],2,'.',',').'/mo.';

		}else{
		
		echo '(Position 1: $'.number_format($recordData['transfer_from_actual_monthly_rate'][0],2,'.',',').'/mo.)<br>';
		echo '(Position 2: $'.number_format($recordData['transfer_from_actual_monthly_rate2'][0],2,'.',',').'/mo.)<br>';
		echo '(Combined: $'.number_format($recordData['transfer_from_actual_monthly_rate'][0]+$recordData['transfer_from_actual_monthly_rate2'][0],2,'.',',').'/mo.)';
		
		}
		
	}?>

</td></tr>






<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	PERCENT OF TIME EMPLOYED: <?php echo $recordData['percent_time_employed'][0];?>%<br>
	EMPLOYMENT TYPE: <?php echo $recordData['exempt_status'][0];?>
	
</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	BASE MONTHLY SALARY: $<?php echo number_format($recordData['base_monthly_salary'][0],2,'.',',');?>/mo.<br>
	BASE ANNUAL SALARY: $<?php echo number_format($recordData['base_annual_salary'][0],2,'.',',');?>/yr.

</td></tr>




</table>



	
<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr><th colspan="4"><h2 style="border-top:2px dotted #999999; text-align:left; padding-top:4px;">ELIGIBLE BENEFIT(S) SELECTED:</h2></th></tr>
	<tr><td style="padding:6px">
			<?php if($recordData['benefits_ss'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Social Security<br>
			<?php if($recordData['benefits_tiaa_cref'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> TIAA-CREF
			</td>
		
			<td style="padding:6px">
			<?php if($recordData['benefits_health_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Health Insurance<br>
			<?php if($recordData['benefits_dental_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Dental Insurance
			</td>
	
			<td style="padding:6px">
			<?php if($recordData['benefits_life_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Life Insurance<br>
			<?php if($recordData['benefits_disab_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Disability Insurance
			</td>
	
			<td style="padding:6px">
			<?php if($recordData['benefits_add_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Accidental Death & Dismemberment Insurance<br>
			<?php if($recordData['benefits_bus_van_parking'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Bus/Van Pool/Parking <?php if($recordData['benefits_fsa'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Flexible Spending Account
	</td></tr>
</table>






<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

	<tr><th><h2 style="border-top:2px dotted #999999; text-align:left; padding-top:4px;">REMARKS:</h2></th></tr>

	<tr><td><?php echo $recordData['c_action_remarks_html'][0];?></td></tr>

</table>




<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>1. SEDL UNIT RECOMMENDING ACTION</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_pba'][0] !== '1'){?>	<a href="menu_personnel_actions_wg_admin.php?action=show_action&mod=pba&record_ID=<?php echo $recordData['record_ID'][0];?>&id=<?php echo $recordData['c_row_ID'][0];?>" <?php if($recordData['signer_ID_pba'][0] == $_SESSION['user_ID']){echo 'onclick="return confirmSign()"';}else{echo 'onclick="return preventSignPBA()"';}?>><?php echo $recordData['signer_ID_pba'][0];?></a><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_pba'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_pba'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>UNIT BUDGET AUTHORITY</strong></span></td>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">
	<strong>2. HUMAN RESOURCES REVIEW</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_hr'][0] !== '1'){?><a href="menu_personnel_actions_wg_admin.php?action=show_action&mod=hr&record_ID=<?php echo $recordData['record_ID'][0];?>&id=<?php echo $recordData['c_row_ID'][0];?>"  <?php if($recordData['signer_ID_hr'][0] == $_SESSION['user_ID']){echo 'onclick="return confirmSign()"';}else{echo 'onclick="return preventSignHR()"';}?>><?php echo $recordData['signer_ID_hr'][0];?></a><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_hr'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_hr'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>HUMAN RESOURCES GENERALIST</strong></span></td></tr>


<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>3. FISCAL REVIEW</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_cfo'][0] !== '1'){?><a href="menu_personnel_actions_wg_admin.php?action=show_action&mod=cfo&record_ID=<?php echo $recordData['record_ID'][0];?>&id=<?php echo $recordData['c_row_ID'][0];?>"  <?php if($recordData['signer_ID_cfo'][0] == $_SESSION['user_ID']){echo 'onclick="return confirmSign()"';}else{echo 'onclick="return preventSignCFO()"';}?>><?php echo $recordData['signer_ID_cfo'][0];?></a><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_cfo'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_cfo'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>CHIEF FINANCIAL OFFICER</strong></span></td></td>
	
	<td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>4. APPROVAL</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_ceo'][0] !== '1'){?><a href="menu_personnel_actions_wg_admin.php?action=show_action&mod=ceo&record_ID=<?php echo $recordData['record_ID'][0];?>&id=<?php echo $recordData['c_row_ID'][0];?>"  <?php if($recordData['signer_ID_ceo'][0] !== $_SESSION['user_ID']){echo 'onclick="return preventSignCEO()"';}elseif($recordData['c_final_approval_ready'][0] == '0'){echo 'onclick="return preventApproveCEO()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['signer_ID_ceo'][0];?></a><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_ceo'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_ceo'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>PRESIDENT & CEO</strong></span></td></tr>
</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="color:#666666" align="right"><br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<span class="tiny">Document ID: <?php echo $recordData['record_ID'][0];?></span></td></tr>
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

}elseif($action == 'new'){ 



$staff_ID = $_GET['id'];

##################################################
## START: GET SELECTED STAFF DETAILS ##
##################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff_table','all');
$search2 -> SetDBPassword($webPW,$webUN);

$search2 -> AddDBParam('staff_ID',$staff_ID);

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);

//$fullname = $recordData2['name_timesheet'][0];
//$unit = $recordData2['primary_SEDL_workgroup'][0];
################################################
## END: GET SELECTED STAFF DETAILS ##
################################################


#############################################################
## START: FIND MOST RECENT PERSONNEL ACTIONS FOR THIS USER ##
#############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','personnel_actions','1');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$staff_ID);

$search -> AddSortParam('action_effective_date','descend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
##########################################################
## END: FIND MOST RECENT PERSONNEL ACTION FOR THIS USER ##
##########################################################

################################
## START: GET FMP VALUE-LISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('SIMS_2.fp7','personnel_actions');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
##############################
## END: GET FMP VALUE-LISTS ##
##############################

$year_this = date("Y");
$year_next = $year_this + 1;
$year_last = $year_this - 1;
$year_last2 = $year_this - 2;
$year_last3 = $year_this - 3;
$year_last4 = $year_this - 4;
$month_this = date("F");
$day_this = date("d");
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Personnel Actions</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Kameron:400,700' rel='stylesheet' type='text/css'>
<script language="JavaScript">

function showdiv(){
document.getElementById('assign_to_title_alt').style.display = "block";
document.getElementById('assign_to_monthly_rate_alt').style.display = "block";
document.getElementById('add_button').style.display = "none";
document.getElementById('hide_button').style.display = "block";
}

function hidediv(){
var pte1 = document.getElementById('percent_time_employed_assign_to_position1').value;
document.getElementById('assign_to_title_alt').style.display = "none";
document.getElementById('assign_to_monthly_rate_alt').style.display = "none";
document.getElementById('add_button').style.display = "block";
document.getElementById('hide_button').style.display = "none";
document.getElementById('assign_to_title2').value = "";
document.getElementById('assign_to_paygrade2').value = "";
document.getElementById('percent_time_employed_assign_to_position2').value = "";
document.getElementById('assign_to_actual_monthly_rate2').value = "";
document.getElementById('est_calc_sum3').value = Math.round((pte1*100));
calc();
}

function showdiv2(){
document.getElementById('transfer_from_title_alt').style.display = "block";
document.getElementById('transfer_from_monthly_rate_alt').style.display = "block";
document.getElementById('add_button2').style.display = "none";
document.getElementById('hide_button2').style.display = "block";
}

function hidediv2(){
document.getElementById('transfer_from_title_alt').style.display = "none";
document.getElementById('transfer_from_monthly_rate_alt').style.display = "none";
document.getElementById('add_button2').style.display = "block";
document.getElementById('hide_button2').style.display = "none";
document.getElementById('transfer_from_title2').value = "";
document.getElementById('transfer_from_paygrade2').value = "";
document.getElementById('percent_time_employed_transfer_from_position2').value = "";
document.getElementById('transfer_from_actual_monthly_rate2').value = "";
calc();
}

function startCalc(){
  interval = setInterval("calc()",1);
}

function calc(){

if(document.getElementById('assign_to_actual_monthly_rate').value == ''){
	var rate1 = 0;
}else{
	var rate1 = document.getElementById('assign_to_actual_monthly_rate').value;
}

if(document.getElementById('assign_to_actual_monthly_rate2').value == ''){
	var rate2 = 0;
}else{
	var rate2 = document.getElementById('assign_to_actual_monthly_rate2').value;
}

var sum = parseInt(rate1,10) + parseInt(rate2,10);
document.getElementById('est_calc_sum1').value = Math.round((sum / <?php echo $recordData2['FTE_status'][0];?>)* 1);
document.getElementById('est_calc_sum2').value = Math.round((sum / <?php echo $recordData2['FTE_status'][0];?>)* 12);
}

function stopCalc(){
  clearInterval(interval);
}

function UpdateSelect(){

	select_value = "";
	select_value = document.pa_form.action_descr.value;

	var id = 'other_action';
	var obj = '';
	obj = (document.getElementById) ? document.getElementById(id) : ((document.all) ? document.all[id] : ((document.layers) ? document.layers[id] : false));
	
	
	if(select_value == ""){
	  obj.style.display = 'none';
	  //alert("onBodyLoad.");
	
	} else if(select_value == "other"){
	  // alert("You chose Journal article.");
	  // return false;
	  obj.style.display = 'block';
	}
	else
	{
	  obj.style.display = 'none';
	}


	var id2 = 'lateral_transfer_units';
	var obj2 = '';
	obj2 = (document.getElementById) ? document.getElementById(id2) : ((document.all) ? document.all[id2] : ((document.layers) ? document.layers[id2] : false));
	
	
	if(select_value == ""){
	  obj2.style.display = 'none';
	  //alert("onBodyLoad.");
	
	} else if(select_value == "Lateral Transfer"){
	  // alert("You chose Journal article.");
	  // return false;
	  obj2.style.display = 'block';
	}
	else
	{
	  obj2.style.display = 'none';
	}

}

function UpdateSelect2(){

if(document.getElementById('percent_time_employed_assign_to_position1').value == ''){
	var percent1 = 0;
	//alert(percent1);
}else{
	var percent1 = document.getElementById('percent_time_employed_assign_to_position1').value;
	//alert(percent1);
}

if(document.getElementById('percent_time_employed_assign_to_position2').value == ''){
	var percent2 = 0;
	//alert(percent2);
}else{
	var percent2 = document.getElementById('percent_time_employed_assign_to_position2').value;
	//alert(percent2);
}

	//var id = 'other_action';
	//var obj = '';
	//obj = (document.getElementById) ? document.getElementById(id) : ((document.all) ? document.all[id] : ((document.layers) ? document.layers[id] : false));
	
var sum = parseFloat(percent1) + parseFloat(percent2);
//alert(sum);
document.getElementById('est_calc_sum3').value = Math.round(sum*100);

}

function overlayvis(blck) {
  el = document.getElementById(blck.id);
  el.style.visibility = (el.style.visibility == 'visible') ? 'hidden' : 'visible';
}

</script>

<style type="text/css">
table{  }
table.stub td {
	color: #000000;
	font-family: 'Kameron', serif;
	font-size:13px;
	background-color:#ffffff;
	padding:0px ;
	border-width:0px;
	padding-right:20px;
	padding-top:2px;
	padding-bottom:2px;
	margin:0px;
	vertical-align: text-top;
	white-space: nowrap;
}


hr.ee {
border: none 0;
border-top: 1px dotted #000000;
width: 100%;
height: 1px;
margin: 0px;
text-align: left;
padding: 0px;
}

h1 {font-family: 'Kameron', serif; text-shadow: #ffffff 0px 0px 0px; color:#0033cc; padding:3px;}
h2 {font-family: 'Kameron', serif; text-shadow: #ffffff 0px 0px 0px; font-size:16px; color:#000000;}
th { 	font-family: 'Kameron', serif; }


</style>


</head>

<BODY BGCOLOR="#FFFFFF" onLoad="UpdateSelect();UpdateSelect2();hidediv();hidediv2();">
<form method="post" name="pa_form" id="pa_form">
<input type="hidden" name="action" value="show_action">
<input type="hidden" name="mod" value="submit_new">
<input type="hidden" name="staff_ID" value="<?php echo $staff_ID;?>">
<input type="hidden" name="transfer_from_title" value="<?php if($searchResult['foundCount'] == 0){echo $recordData2['job_title'][0];}else{echo $recordData['assign_to_title'][0];}?>">
<input type="hidden" name="transfer_from_paygrade" value="<?php if($searchResult['foundCount'] == 0){echo $recordData2['pay_grade'][0];}else{echo $recordData['assign_to_paygrade'][0];}?>">
<input type="hidden" name="transfer_from_actual_monthly_rate" value="<?php echo $recordData2['c_cur_payrate'][0];?>">
<input type="hidden" name="fte" value="<?php echo $recordData2['FTE_status'][0];?>">

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td colspan="2" align="right" style="vertical-align:text-top;padding:6px;border:0px"><img src="/images/print_sedl.jpg" width="20%"></td></tr>


<tr><td width="50%" style="vertical-align:text-top;">

	<h1>NEW PERSONNEL ACTION</h1>

</td><td width="50%" style="vertical-align:text-top;padding:6px;">

	<div>
	<div style="padding:3px; text-align:right"><strong>DATE PREPARED</strong>: <?php echo date("m/d/Y");?></div>
	</div>

</td></tr>



<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>NAME OF STAFF MEMBER:</strong><br>
	<?php echo $recordData2['c_full_name_first_last'][0];?>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">
	<strong>ADDRESS:</strong><br>
	<?php if($recordData2['staff_hm_address'][0] == ''){?>
			<input type="hidden" name="new_address" value="yes">
			<div style="padding:4px;border:1px dotted #000000;background-color:#b7e4fc">
			
			<table>
			<tr><td colspan="2" style="color:#0033cc;background-color:#b7e4fc"><strong>Enter Home Address:</strong></td></tr>
			<tr><td style="background-color:#b7e4fc">Address</td><td style="background-color:#b7e4fc"><input type="text" name="staff_address" size="45"></td></tr>
			<tr><td style="background-color:#b7e4fc">City/St/Zip</td><td style="background-color:#b7e4fc"><input type="text" name="staff_city" size="20"> <input type="text" name="staff_state" size="5"> <input type="text" name="staff_zip" size="10"></td></tr>
			</table>
			
			</div>

	<?php }else{ ?>
		<?php echo $recordData2['staff_hm_address'][0];?> <?php echo $recordData2['staff_hm_address2'][0];?><br>
		<?php echo $recordData2['staff_hm_city'][0];?>, <?php echo $recordData2['staff_hm_state'][0];?> <?php echo $recordData2['staff_hm_zip'][0];?>
	<?php } ?>
</td></tr>






<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>NATURE OF ACTION:</strong><br>

		<select name="action_descr" onChange="UpdateSelect();">
		<option value="">Select the type of action</option>
		<option value="">----------------------------------------</option>
		
		<?php foreach($v1Result['valueLists']['personnel_action_types'] as $key => $value) { ?>
		<option value="<?php echo $value;?>" <?php if($value == 'Salary Change -- Annual Performance Review'){echo 'selected';}?>> <?php echo $value; ?></option>
		<?php } ?>
		<option value="">----------------------------------------</option>
		<option value="other"> Action not listed? Enter other...</option>
		</select>
		
		<div id="other_action" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
					
		<div style="padding:4px;border:1px dotted #000000;background-color:#ffffff">Other Action: <input type="text" name="action_descr_other" size="50"></div>

		</div>

		<div id="lateral_transfer_units" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
					
		<div style="padding:4px;border:1px dotted #000000;background-color:#ffffff">
		Transfer from <select name="lateral_transfer_unit_from">
			<option value=""></option>
			<?php foreach($v1Result['valueLists']['sedl_workgroups'] as $key => $value) { ?>
			<option value="<?php echo $value;?>" <?php if($value == $recordData2['primary_SEDL_workgroup'][0]){echo 'selected';}?>> <?php echo $value; ?></option>
			<?php } ?>
			</select>
		to <select name="lateral_transfer_unit_to">
			<option value=""></option>
			<?php foreach($v1Result['valueLists']['sedl_workgroups'] as $key => $value) { ?>
			<option value="<?php echo $value;?>"> <?php echo $value; ?></option>
			<?php } ?>
			</select>

		
		
		</div>

		</div>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>EFFECTIVE DATE OF ACTION:</strong><br>

		<select name="action_effective_date_m">
		<option value=""></option>
		
		<option value="01" <?php if($month_this == 'January'){echo 'selected';}?>>January</option>
		<option value="02" <?php if($month_this == 'February'){echo 'selected';}?>>February</option>
		<option value="03" <?php if($month_this == 'March'){echo 'selected';}?>>March</option>
		<option value="04" <?php if($month_this == 'April'){echo 'selected';}?>>April</option>
		<option value="05" <?php if($month_this == 'May'){echo 'selected';}?>>May</option>
		<option value="06" <?php if($month_this == 'June'){echo 'selected';}?>>June</option>
		<option value="07" <?php if($month_this == 'July'){echo 'selected';}?>>July</option>
		<option value="08" <?php if($month_this == 'August'){echo 'selected';}?>>August</option>
		<option value="09" <?php if($month_this == 'September'){echo 'selected';}?>>September</option>
		<option value="10" <?php if($month_this == 'October'){echo 'selected';}?>>October</option>
		<option value="11" <?php if($month_this == 'November'){echo 'selected';}?>>November</option>
		<option value="12" <?php if($month_this == 'December'){echo 'selected';}?>>December</option>

		</select>

		<select name="action_effective_date_d">
		<option value=""></option>
		
		<option value="01" <?php if($day_this == '01'){echo 'selected';}?>>01</option>
		<option value="02" <?php if($day_this == '02'){echo 'selected';}?>>02</option>
		<option value="03" <?php if($day_this == '03'){echo 'selected';}?>>03</option>
		<option value="04" <?php if($day_this == '04'){echo 'selected';}?>>04</option>
		<option value="05" <?php if($day_this == '05'){echo 'selected';}?>>05</option>
		<option value="06" <?php if($day_this == '06'){echo 'selected';}?>>06</option>
		<option value="07" <?php if($day_this == '07'){echo 'selected';}?>>07</option>
		<option value="08" <?php if($day_this == '08'){echo 'selected';}?>>08</option>
		<option value="09" <?php if($day_this == '09'){echo 'selected';}?>>09</option>
		<option value="10" <?php if($day_this == '10'){echo 'selected';}?>>10</option>
		<option value="11" <?php if($day_this == '11'){echo 'selected';}?>>11</option>
		<option value="12" <?php if($day_this == '12'){echo 'selected';}?>>12</option>
		<option value="13" <?php if($day_this == '13'){echo 'selected';}?>>13</option>
		<option value="14" <?php if($day_this == '14'){echo 'selected';}?>>14</option>
		<option value="15" <?php if($day_this == '15'){echo 'selected';}?>>15</option>
		<option value="16" <?php if($day_this == '16'){echo 'selected';}?>>16</option>
		<option value="17" <?php if($day_this == '17'){echo 'selected';}?>>17</option>
		<option value="18" <?php if($day_this == '18'){echo 'selected';}?>>18</option>
		<option value="19" <?php if($day_this == '19'){echo 'selected';}?>>19</option>
		<option value="20" <?php if($day_this == '20'){echo 'selected';}?>>20</option>
		<option value="21" <?php if($day_this == '21'){echo 'selected';}?>>21</option>
		<option value="22" <?php if($day_this == '22'){echo 'selected';}?>>22</option>
		<option value="23" <?php if($day_this == '23'){echo 'selected';}?>>23</option>
		<option value="24" <?php if($day_this == '24'){echo 'selected';}?>>24</option>
		<option value="25" <?php if($day_this == '25'){echo 'selected';}?>>25</option>
		<option value="26" <?php if($day_this == '26'){echo 'selected';}?>>26</option>
		<option value="27" <?php if($day_this == '27'){echo 'selected';}?>>27</option>
		<option value="28" <?php if($day_this == '28'){echo 'selected';}?>>28</option>
		<option value="29" <?php if($day_this == '29'){echo 'selected';}?>>29</option>
		<option value="30" <?php if($day_this == '30'){echo 'selected';}?>>30</option>
		<option value="31" <?php if($day_this == '31'){echo 'selected';}?>>31</option>

		</select>

		<select name="action_effective_date_y">
		<option value=""></option>
		
		<option value="<?php echo $year_last4;?>"><?php echo $year_last4;?></option>
		<option value="<?php echo $year_last3;?>"><?php echo $year_last3;?></option>
		<option value="<?php echo $year_last2;?>"><?php echo $year_last2;?></option>
		<option value="<?php echo $year_last;?>"><?php echo $year_last;?></option>
		<option value="<?php echo $year_this;?>" selected><?php echo $year_this;?></option>
		<option value="<?php echo $year_next;?>"><?php echo $year_next;?></option>

		</select>

</td></tr>






<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333;position:relative">
<div style="position:absolute;bottom:0;right:0;"><a style="text-decoration:none;background-color:#ffffff;color:#ffffff" href="#" onclick="showdiv();"><img src="images/add_button_green_round.png" style="padding:4px" id="add_button" title="Click to assign a second position title for this staff member."></a></div>
<div style="position:absolute;bottom:0;right:0;"><a style="text-decoration:none;background-color:#ffffff;color:#ffffff" href="#" onclick="hidediv();"><img src="images/red_delete.png" style="padding:4px" id="hide_button" title="Click to remove the second position title for this staff member."></a></div>
	<strong>ASSIGN TO:</strong><br>

		Title: <select name="assign_to_title">
		<option value="">Select the new position title</option>
		<option value="">-------------------------</option>
		
		<?php foreach($v1Result['valueLists']['job_titles'] as $key => $value) { ?>
		<option value="<?php echo $value;?>" <?php if($recordData2['job_title'][0] == $value){echo 'selected';}?>> <?php echo $value; ?></option>
		<?php } ?>
		</select>

		<br>Pay Grade: <select name="assign_to_paygrade">
		<option value="">Select the new pay grade</option>
		<option value="">-------------------------</option>
		<?php foreach($v1Result['valueLists']['pay_grades'] as $key => $value) { ?>
		<option value="<?php echo $value;?>" <?php if($recordData2['pay_grade'][0] == $value){echo 'selected';}?>> <?php echo $value; ?></option>
		<?php } ?>
		</select>

		<br>Percent Time: <select name="percent_time_employed_assign_to_position1" id="percent_time_employed_assign_to_position1" onChange="UpdateSelect2();">
		<option value="">Select the new percent time</option>
		<option value="">-------------------------</option>
		<?php foreach($v1Result['valueLists']['fte_status'] as $key => $value) { ?>
		<option value="<?php echo $value;?>" <?php if($recordData2['FTE_status'][0] == $value){echo 'selected';}?>> <?php echo $value*100; ?>%</option>
		<?php } ?>
		</select>
	
<div id="assign_to_title_alt" style="border-top:1px dotted #333333;padding-top:6px">


	Title 2: <select name="assign_to_title2" id="assign_to_title2">
	<option value="">Select the new position title</option>
	<option value="">-------------------------</option>
	
	<?php foreach($v1Result['valueLists']['job_titles'] as $key => $value) { ?>
	<option value="<?php echo $value;?>" <?php if($recordData2['job_title'][0] == $value){echo 'selected';}?>> <?php echo $value; ?></option>
	<?php } ?>
	</select>

	<br>Pay Grade 2: <select name="assign_to_paygrade2" id="assign_to_paygrade2">
	<option value="">Select the new pay grade</option>
	<option value="">-------------------------</option>
	<?php foreach($v1Result['valueLists']['pay_grades'] as $key => $value) { ?>
	<option value="<?php echo $value;?>" <?php if($recordData2['pay_grade'][0] == $value){echo 'selected';}?>> <?php echo $value; ?></option>
	<?php } ?>
	</select>

	<br>Percent Time 2: <select name="percent_time_employed_assign_to_position2" id="percent_time_employed_assign_to_position2" onChange="UpdateSelect2();">
	<option value="">Select the new percent time</option>
	<option value="">-------------------------</option>
	<?php foreach($v1Result['valueLists']['fte_status'] as $key => $value) { ?>
	<option value="<?php echo $value;?>" <?php if($recordData2['FTE_status'][0] == $value){echo 'selected';}?>> <?php echo $value*100; ?>%</option>
	<?php } ?>
	</select>

</div>



</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>ACTUAL MONTHLY RATE:</strong><br>
	$<input type="text" name="assign_to_actual_monthly_rate" id="assign_to_actual_monthly_rate" size="15" onFocus="startCalc();" onBlur="stopCalc();">/mo.<br>(Ex: 4520.00 - no commas or currency symbols)

<div id="assign_to_monthly_rate_alt" style="border-top:1px dotted #333333;padding-top:6px">


	$<input type="text" name="assign_to_actual_monthly_rate2" id="assign_to_actual_monthly_rate2" size="15" onFocus="startCalc();" onBlur="stopCalc();">/mo. (Position #2)<br>(Ex: 4520.00 - no commas or currency symbols)


</div>

</td></tr>


<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333;position:relative">
<div style="position:absolute;bottom:0;right:0;"><a style="text-decoration:none;background-color:#ffffff;color:#ffffff" href="#" onclick="showdiv2();"><img src="images/add_button_green_round.png" style="padding:4px" id="add_button2" title="Click to view the second position title for this staff member."></a></div>
<div style="position:absolute;bottom:0;right:0;"><a style="text-decoration:none;background-color:#ffffff;color:#ffffff" href="#" onclick="hidediv2();"><img src="images/red_delete.png" style="padding:4px" id="hide_button2" title="Click to hide the second position title for this staff member."></a></div>

	<strong>TRANSFER FROM:</strong><br>
	Title: <select name="transfer_from_title">
	<option value="">Select the old position title</option>
	<option value="">-------------------------</option>
	
	<?php foreach($v1Result['valueLists']['job_titles'] as $key => $value) { ?>
	<option value="<?php echo $value;?>" <?php if($recordData2['job_title'][0] == $value){echo 'selected';}?>> <?php echo $value; ?></option>
	<?php } ?>
	</select><br>

	Pay Grade: <select name="transfer_from_paygrade">
	<option value="">Select the old pay grade</option>
	<option value="">-------------------------</option>
	<?php foreach($v1Result['valueLists']['pay_grades'] as $key => $value) { ?>
	<option value="<?php echo $value;?>" <?php if($recordData2['pay_grade'][0] == $value){echo 'selected';}?>> <?php echo $value; ?></option>
	<?php } ?>
	</select>
	
	<br>Percent Time: <select name="percent_time_employed_transfer_from_position1">
	<option value="">Select the old percent time</option>
	<option value="">-------------------------</option>
	<?php foreach($v1Result['valueLists']['fte_status'] as $key => $value) { ?>
	<option value="<?php echo $value;?>" <?php if($recordData2['FTE_status'][0] == $value){echo 'selected';}?>> <?php echo $value*100; ?>%</option>
	<?php } ?>
	</select>

<div id="transfer_from_title_alt" style="border-top:1px dotted #333333;padding-top:6px">


	Title 2: <select name="transfer_from_title2" id="transfer_from_title2">
	<option value="">Select the old position title</option>
	<option value="">-------------------------</option>
	
	<?php foreach($v1Result['valueLists']['job_titles'] as $key => $value) { ?>
	<option value="<?php echo $value;?>" <?php if($recordData2['job_title'][0] == $value){echo 'selected';}?>> <?php echo $value; ?></option>
	<?php } ?>
	</select>

	<br>Pay Grade 2: <select name="transfer_from_paygrade2" id="transfer_from_paygrade2">
	<option value="">Select the old pay grade</option>
	<option value="">-------------------------</option>
	<?php foreach($v1Result['valueLists']['pay_grades'] as $key => $value) { ?>
	<option value="<?php echo $value;?>" <?php if($recordData2['pay_grade'][0] == $value){echo 'selected';}?>> <?php echo $value; ?></option>
	<?php } ?>
	</select>

	<br>Percent Time 2: <select name="percent_time_employed_transfer_from_position2" id="percent_time_employed_transfer_from_position2">
	<option value="">Select the old percent time</option>
	<option value="">-------------------------</option>
	<?php foreach($v1Result['valueLists']['fte_status'] as $key => $value) { ?>
	<option value="<?php echo $value;?>" <?php if($recordData2['FTE_status'][0] == $value){echo 'selected';}?>> <?php echo $value*100; ?>%</option>
	<?php } ?>
	</select>

</div>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>ACTUAL MONTHLY RATE:</strong><br>
	$<input type="text" name="transfer_from_actual_monthly_rate" size="15" value="<?php echo $recordData2['c_cur_payrate'][0];?>">/mo.<br>(Ex: 4520.00 - no commas or currency symbols)

<div id="transfer_from_monthly_rate_alt" style="border-top:1px dotted #333333;padding-top:6px">


	$<input type="text" name="transfer_from_actual_monthly_rate2" id="transfer_from_actual_monthly_rate2" size="15">/mo. (Position #2)<br>(Ex: 4520.00 - no commas or currency symbols)


</div>

</td></tr>






<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	PERCENT OF TIME EMPLOYED: <input type="text" name="est_calc_sum3" id="est_calc_sum3" size="5" DISABLED>%
	
<br>
	EMPLOYEE TYPE: <input type="radio" name="exempt_status" value="Exempt" <?php if($recordData2['employee_type'][0] == 'Exempt'){echo 'checked';}?>>Exempt &nbsp;&nbsp;&nbsp; <input type="radio" name="exempt_status" value="Non-exempt" <?php if($recordData2['employee_type'][0] == 'Non-exempt'){echo 'checked';}?>>Non-exempt
</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	BASE MONTHLY SALARY: $<input type="text" name="est_calc_sum1" id="est_calc_sum1" size="10" DISABLED>/mo.<br>
	BASE ANNUAL SALARY: $<input type="text" name="est_calc_sum2" id="est_calc_sum2" size="10" DISABLED>/yr.

</td></tr>




</table>



	
<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr><th colspan="4"><h2 style="border-top:2px dotted #999999; text-align:left; padding-top:4px;">ELIGIBLE BENEFIT(S) SELECTED:</h2></th></tr>
	<tr><td style="padding:6px">
			<input type="checkbox" name="benefits_ss" value="Yes" <?php if($recordData['benefits_ss'][0] == 'Yes'){?>checked<?php }?>> Social Security</input><br>
			<input type="checkbox" name="benefits_tiaa_cref" value="Yes" <?php if($recordData['benefits_tiaa_cref'][0] == 'Yes'){?>checked<?php }?>> TIAA-CREF</input>
			</td>
		
			<td style="padding:6px">
			<input type="checkbox" name="benefits_health_ins" value="Yes" <?php if($recordData['benefits_health_ins'][0] == 'Yes'){?>checked<?php }?>> Health Insurance</input><br>
			<input type="checkbox" name="benefits_dental_ins" value="Yes" <?php if($recordData['benefits_dental_ins'][0] == 'Yes'){?>checked<?php }?>> Dental Insurance</input>
			</td>
	
			<td style="padding:6px">
			<input type="checkbox" name="benefits_life_ins" value="Yes" <?php if($recordData['benefits_life_ins'][0] == 'Yes'){?>checked<?php }?>> Life Insurance</input><br>
			<input type="checkbox" name="benefits_disab_ins" value="Yes" <?php if($recordData['benefits_disab_ins'][0] == 'Yes'){?>checked<?php }?>> Disability Insurance</input>
			</td>
	
			<td style="padding:6px">
			<input type="checkbox" name="benefits_add_ins" value="Yes" <?php if($recordData['benefits_add_ins'][0] == 'Yes'){?>checked<?php }?>> Accidental Death & Dismemberment Insurance</input><br>
			<input type="checkbox" name="benefits_bus_van_parking" value="Yes" <?php if($recordData['benefits_bus_van_parking'][0] == 'Yes'){?>checked<?php }?>> Bus/Van Pool/Parking</input>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="benefits_fsa" value="Yes" <?php if($recordData['benefits_fsa'][0] == 'Yes'){?>checked<?php }?>> Flexible Spending Account</input>
	</td></tr>
</table>






<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

	<tr><th><h2 style="border-top:2px dotted #999999; text-align:left; padding-top:4px;">REMARKS:</h2></th></tr>

	<tr><td><textarea name="action_remarks" rows="10" cols="100"><?php if(($recordData['action_remarks'][0] !== '')&&($searchResult['foundCount'] > 0)){ echo $recordData['action_remarks'][0];}else{ ?>
1. Reference: Administrative Policy/Procedure xxxxxx.

2. See <?php echo $recordData2['c_full_name_first_last'][0];?>'s Annual Performance Review recommending a xxxx% salary increase.

3. Anniversary date for next annual performance review and possible salary change is xxxxxx.
<?php }?></textarea><br><span class="tiny">* Default text or remarks from most recent personnel action have been pre-entered.</span><br>&nbsp;<br>&nbsp;<br>
	<div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Submit"></div>
	</td></tr>

</table>


</form>



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

}else{ ?>


Error | <a href="menu_personnel_actions_wg_admin.php?action=show_all" title="Return to SIMS Personnel Actions screen.">Return to Personnel Actions Admin</a>

<?php } ?>





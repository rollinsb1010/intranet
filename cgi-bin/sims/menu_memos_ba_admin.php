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

//$debug = 'off';
//$paystub_access = 'yes';
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

#################################################
## START: GRAB CURRENT WORKGROUP STAFF RECORDS ##
#################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff_table','all');
$search -> SetDBPassword($webPW,$webUN);

if($query == 'former_staff'){
$search -> AddDBParam('current_employee_status','Former Employee');
$search -> AddDBParam('c_personnel_memos_access_list',$_SESSION['user_ID']);
$search -> AddDBParam('employee_type','Hourly','neq');
} else {
$search -> AddDBParam('current_employee_status','SEDL Employee');
$search -> AddDBParam('c_personnel_memos_access_list',$_SESSION['user_ID']);
$search -> AddDBParam('employee_type','Hourly','neq');
}

$search -> AddSortParam('c_full_name_last_first','ascend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);
###############################################
## END: GRAB CURRENT WORKGROUP STAFF RECORDS ##
###############################################

##################################################################
## START: FIND ALL UNASSIGNED PERSONNEL MEMOS FOR THE WORKGROUP ##
##################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','personnel_memos','all');
$search2 -> SetDBPassword($webPW,$webUN);
//$search2 -> FMSkipRecords($skipsize);
$search2 -> AddDBParam('staff_ID','=='.'');
$search2 -> AddDBParam('memo_unit_abbrev',$_SESSION['workgroup']);

$search2 -> AddSortParam('memo_ID','descend');


$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//print_r ($searchResult2);
//$_SESSION['timesheet_foundcount'] = $searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);
##################################################################
## END: FIND ALL UNASSIGNED PERSONNEL MEMOS FOR THE WORKGROUP ##
##################################################################

?>

<html>
<head>
<title>SIMS - Personnel Actions/Memos</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Workgroup Personnel Memos</h1><hr /></td></tr>
			
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>Workgroup <?php if($query == 'former_staff'){?>Former <?php }?>Staff</strong> | <?php echo $searchResult['foundCount'];?> records found. | <?php if($query == 'former_staff'){?><a href="menu_memos_ba_admin.php?action=show_all">Show current staff</a><?php }else{?><a href="menu_memos_ba_admin.php?action=show_all&query=former_staff">Show former staff</a><?php }?></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<tr><td class="body" colspan=2 style="padding-bottom:0px">Click a staff member name below to view and/or create personnel memos for existing staff members.</td></tr>
			
			<tr><td class="body" colspan=2 style="padding-top:0px">



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">Name</td><td class="body">Unit</td><td class="body">Title</td><td class="body">FTE</td><td class="body">Empl. Start Date</td><td class="body">Empl. Term. Date</td></tr>
							
							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							<tr><td class="body"><?php echo $searchData['staff_ID'][0];?></td><td class="body" nowrap><a href="menu_memos_ba_admin.php?action=show_1&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo stripslashes($searchData['c_full_name_last_first'][0]);?></a></td><td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td><td class="body"><?php echo $searchData['job_title'][0];?></td><td class="body"><?php echo $searchData['FTE_status'][0];?></td><td class="body"><?php echo $searchData['empl_start_date'][0];?></td><td class="body" nowrap><?php if($searchData['empl_end_date'][0] == ''){echo 'Current';}else{echo $searchData['empl_end_date'][0];}?></td></tr>
							<?php } ?>


							
							</table><p>
			
			</td></tr>
			
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>Unassigned Workgroup Memos</strong> | <?php echo $searchResult2['foundCount'];?> records found.</td><td align="right"><a href="menu_memos_ba_admin.php?action=new_unassigned" title="Click here to create a memo for a newly hired staff member.">New unassigned personnel memo</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">From</td><td class="body">To</td><td class="body">Memo Subject</td><td class="body" nowrap>Date Prepared</td><td class="body">Status</td></tr>
							
							<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
							<tr><td style="vertical-align:text-top"><?php echo $searchData2['memo_ID'][0];?></td><td style="vertical-align:text-top" nowrap><?php echo $searchData2['memo_from'][0];?></td><td style="vertical-align:text-top" nowrap><?php echo $searchData2['memo_to'][0];?></td><td><a href="menu_memos_ba_admin.php?action=show_memo&record_ID=<?php echo $searchData2['memo_ID'][0];?>"><?php echo stripslashes($searchData2['memo_subject'][0]);?></a></td><td style="vertical-align:text-top"><?php echo $searchData2['memo_date'][0];?></td><td style="vertical-align:text-top" nowrap><?php echo $searchData2['memo_approval_status'][0];?></td></tr>
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

}elseif($action == 'show_1'){ 

##############################################################
## START: FIND ALL PERSONNEL MEMOS FOR THE SELECTED STAFF ##
##############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','personnel_memos','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$_GET['staff_ID']);
//$search -> AddDBParam('c_periodend_local',$today,'lte');
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('memo_date','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
//$_SESSION['timesheet_foundcount'] = $searchResult['foundCount'];
$recordData = current($searchResult['data']);
############################################################
## END: FIND ALL PERSONNEL MEMOS FOR THE SELECTED STAFF ##
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
<title>SIMS: Personnel Memos</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}


function preventView() { 
	alert ("This personnel memo is currently being processed. You will receive an e-mail notification when this document has been approved.")
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Personnel Memos</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $fullname;?> (<?php echo $unit;?>)</b></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="menu_memos_ba_admin.php?action=new&id=<?php echo $recordData2['staff_ID'][0];?>" title="Create new memo for this staff member">New personnel memo</a> | <a href="menu_memos_ba_admin.php?action=show_all" title="Return to workgroup Personnel Memos.">Workgroup staff</a> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			

							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">From</td><td class="body">To</td><td class="body">Memo Subject</td><td class="body" nowrap>Date Prepared</td><td class="body">Status</td></tr>
							
						<?php if($searchResult['foundCount'] > 0) { ?>

							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							<tr><td style="vertical-align:text-top"><?php echo $searchData['memo_ID'][0];?></td><td style="vertical-align:text-top" nowrap><?php echo $searchData['memo_from'][0];?></td><td style="vertical-align:text-top"><?php echo $searchData['memo_to'][0];?></td><td><a href="menu_memos_ba_admin.php?action=show_memo&record_ID=<?php echo $searchData['memo_ID'][0];?>"><?php echo stripslashes($searchData['memo_subject'][0]);?></a></td><td style="vertical-align:text-top"><?php echo $searchData['memo_date'][0];?></td><td style="vertical-align:text-top"><?php echo $searchData['memo_approval_status'][0];?></td></tr>
							<?php } ?>

						<?php }else{  ?>

							<tr>
							<td class="body" colspan="6" style="vertical-align:text-top"><center>No personnel memos found for this staff member.</center></td>
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

}elseif($action == 'show_memo'){ 


if($_REQUEST['mod'] == 'submit_new_memo'){ // CREATOR SUBMITTED THE MEMO

##################################################
## START: CREATE NEW PERSONNEL MEMO ##
##################################################
$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('SIMS_2.fp7','personnel_memos'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information

$newrecord -> AddDBParam('staff_ID',$_REQUEST['staff_ID']);
$newrecord -> AddDBParam('created_by',$_SESSION['user_ID']);
if($_REQUEST['pba_user_ID'] == ''){
$newrecord -> AddDBParam('memo_signer_ID_pba',$_SESSION['user_ID']);
}else{
$newrecord -> AddDBParam('memo_signer_ID_pba',$_REQUEST['pba_user_ID']);
}
$newrecord -> AddDBParam('created_by_staff_ID',$_SESSION['staff_ID']);
$newrecord -> AddDBParam('memo_unit_abbrev',$_REQUEST['memo_unit_abbrev']);
$newrecord -> AddDBParam('memo_type',$_REQUEST['memo_type']);


// UNASSIGNED MEMO TYPES (NEW STAFF MEMBERS)
if($_REQUEST['memo_type'] == 'Terms of employment'){
$newrecord -> AddDBParam('memo_to',$_REQUEST['memo_to']);
//$newrecord -> AddDBParam('memo_date',$_REQUEST['memo_date']);
$newrecord -> AddDBParam('memo_subject',$_REQUEST['memo_subject']);
$newrecord -> AddDBParam('memo_body',$_REQUEST['memo_body']);
$memo_subject = $_REQUEST['memo_subject'];
}

if($_REQUEST['memo_type'] == 'Hiring recommendation'){
$newrecord -> AddDBParam('memo_to',$_REQUEST['memo_to2']);
$newrecord -> AddDBParam('memo_to_title',$_REQUEST['memo_to_title2']);
//$newrecord -> AddDBParam('memo_date',$_REQUEST['memo_date2']);
$newrecord -> AddDBParam('memo_subject',$_REQUEST['memo_subject2']);
$newrecord -> AddDBParam('memo_body',$_REQUEST['memo_body2']);
$newrecord -> AddDBParam('resume_ID',$_REQUEST['resume_ID']);
$memo_subject = $_REQUEST['memo_subject2'];
}

// PRE-ASSIGNED MEMO TYPES (EXISTING STAFF MEMBERS)
if($_REQUEST['memo_type'] == 'Promotion'){
$newrecord -> AddDBParam('memo_to',$_REQUEST['memo_to']);
$newrecord -> AddDBParam('memo_to_title',$_REQUEST['memo_to_title']);
//$newrecord -> AddDBParam('memo_date',$_REQUEST['memo_date']);
$newrecord -> AddDBParam('memo_subject',$_REQUEST['memo_subject']);
$newrecord -> AddDBParam('memo_body',$_REQUEST['memo_body']);
$memo_subject = $_REQUEST['memo_subject'];
}

if($_REQUEST['memo_type'] == 'Reassignment'){
$newrecord -> AddDBParam('memo_to',$_REQUEST['memo_to3']);
$newrecord -> AddDBParam('memo_to_title',$_REQUEST['memo_to_title3']);
//$newrecord -> AddDBParam('memo_date',$_REQUEST['memo_date']);
$newrecord -> AddDBParam('memo_subject',$_REQUEST['memo_subject3']);
$newrecord -> AddDBParam('memo_body',$_REQUEST['memo_body3']);
$memo_subject = $_REQUEST['memo_subject3'];
}

if($_REQUEST['memo_type'] == 'Removal of probationary employment status'){
$newrecord -> AddDBParam('memo_to',$_REQUEST['memo_to2']);
$newrecord -> AddDBParam('memo_to_title',$_REQUEST['memo_to_title2']);
//$newrecord -> AddDBParam('memo_date',$_REQUEST['memo_date2']);
$newrecord -> AddDBParam('memo_subject',$_REQUEST['memo_subject2']);
$newrecord -> AddDBParam('memo_body',$_REQUEST['memo_body2']);
$memo_subject = $_REQUEST['memo_subject2'];
}

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$newrecordData = current($newrecordResult['data']);
##################################################
## END: CREATE NEW PERSONNEL MEMO ##
##################################################
$record_ID = $newrecordData['memo_ID'][0];

if($newrecordResult['errorCode'] == 0){

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','CREATE_PERSONNEL_MEMO_PBA');
	$newrecord -> AddDBParam('action_description',$memo_subject);
	$newrecord -> AddDBParam('table','PERSONNEL_MEMOS');
	$newrecord -> AddDBParam('object_ID',$newrecordData['memo_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$newrecordData['c_row_ID'][0]);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


$newrecordcreated = '1';


	############################################
	## START: SAVE ACTION TO MEMO PROCESS LOG ##
	############################################
	$newrecord2 = new FX($serverIP,$webCompanionPort);
	$newrecord2 -> SetDBData('SIMS_2.fp7','personnel_memos_log');
	$newrecord2 -> SetDBPassword($webPW,$webUN);
	$newrecord2 -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord2 -> AddDBParam('action','CREATE_MEMO');
	$newrecord2 -> AddDBParam('memo_ID',$newrecordData['memo_ID'][0]);
	$newrecordResult2 = $newrecord2 -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult2['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult2['foundCount'];
	############################################
	## END: SAVE ACTION TO MEMO PROCESS LOG ##
	############################################

/*
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
*/
}else{
$newrecordcreated = '2';
$newrecorderror = $newrecordResult['errorCode'];
}


}else{
$record_ID = $_GET['record_ID'];
}

if($_REQUEST['mod'] == 'edit_confirm'){ // ADMIN OR PBA EDITED MEMO

#################################################################
## START: UPDATE MEMO ##
#################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_memos');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('memo_body',$_REQUEST['memo_body']);
$update -> AddDBParam('memo_sign_status_pba','');
$update -> AddDBParam('memo_approval_status','Not Submitted');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
#################################################################
## END: UPDATE MEMO ##
#################################################################

}

if($_REQUEST['mod'] == 'reject'){ // PBA OR CEO REJECTED MEMO

#################################################################
## START: UPDATE MEMO - REJECTED ##
#################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_memos');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['rid']);
$update -> AddDBParam('memo_approval_status','Rejected');
$update -> AddDBParam('memo_rejected_by',$_SESSION['user_ID']);
$update -> AddDBParam('memo_reject_reason',$_GET['memo_reject_reason']);

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
#################################################################
## END: UPDATE MEMO - REJECTED ##
#################################################################

############################################
## START: SAVE ACTION TO MEMO PROCESS LOG ##
############################################
$newrecord2 = new FX($serverIP,$webCompanionPort);
$newrecord2 -> SetDBData('SIMS_2.fp7','personnel_memos_log');
$newrecord2 -> SetDBPassword($webPW,$webUN);
$newrecord2 -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord2 -> AddDBParam('action','REJECT_MEMO');
$newrecord2 -> AddDBParam('comment',$_GET['memo_reject_reason']);
$newrecord2 -> AddDBParam('memo_ID',$updateData['memo_ID'][0]);
$newrecordResult2 = $newrecord2 -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult2['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult2['foundCount'];
############################################
## END: SAVE ACTION TO MEMO PROCESS LOG ##
############################################

##############################################################
## START: TRIGGER REJECTION E-MAIL TO STAFF MEMBER ##
##############################################################
//$to = 'eric.waters@sedl.org';
$to = $updateData['created_by'][0].'@sedl.org, '.$updateData['memo_signer_ID_pba'][0].'@sedl.org';
$subject = 'SIMS: A personnel memo you recently submitted has been rejected';
$message = 
'Dear '.$updateData['memo_from'][0].','."\n\n".

//'[E-mail was sent to: '.$updateData['staff::email'][0].'@sedl.org]'."\n\n".

'A personnel memo you recently submitted has been rejected by '.$_SESSION['user_ID'].'.'."\n\n".

'------------------------------------------------------------'."\n".
' PERSONNEL MEMO DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Memo From: '.$updateData['memo_from'][0]."\n".
'Memo To: '.$updateData['memo_to'][0]."\n".
'Memo Subject: '.$updateData['memo_subject'][0]."\n".
'Rejected by: '.$updateData['memo_rejected_by'][0]."\n".
'Rejection reason: '.$updateData['memo_reject_reason'][0]."\n".
'------------------------------------------------------------'."\n\n".

'To update and/or re-submit this personnel memo, click here: '."\n".
'http://www.sedl.org/staff/sims/menu_memos_ba_admin.php?record_ID='.$updateData['memo_ID'][0].'&action=show_memo'."\n\n".
					
'------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'------------------------------------------------------------------------------------------------------------------';

$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['user_ID'].'@sedl.org'."\r\n".'Bcc: SIMS@sedl.org';

mail($to, $subject, $message, $headers);
############################################################
## END: TRIGGER REJECTION E-MAIL TO STAFF MEMBER ##
############################################################
}

if($_REQUEST['mod'] == 'comments'){ // PBA OR CEO LOGGED COMMENTS

############################################
## START: SAVE ACTION TO MEMO PROCESS LOG ##
############################################
$newrecord2 = new FX($serverIP,$webCompanionPort);
$newrecord2 -> SetDBData('SIMS_2.fp7','personnel_memos_log');
$newrecord2 -> SetDBPassword($webPW,$webUN);
$newrecord2 -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord2 -> AddDBParam('action','COMMENTS');
$newrecord2 -> AddDBParam('comment',$_GET['comments']);
$newrecord2 -> AddDBParam('memo_ID',$record_ID);
$newrecordResult2 = $newrecord2 -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult2['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult2['foundCount'];
############################################
## END: SAVE ACTION TO MEMO PROCESS LOG ##
############################################

}

if($_REQUEST['mod'] == 'pba'){ // PBA SIGNED THE PERSONNEL MEMO
##################################################
## START: SIGN FORM AS PBA ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_memos');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('memo_sign_status_pba','1');
$update -> AddDBParam('memo_approval_status','Pending');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: SIGN FORM AS PBA ##
##################################################

	if($updateResult['errorCode'] == '0'){
	$pba_signed = '1';
	
	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','SIGN_PERSONNEL_MEMO_PBA');
	$newrecord -> AddDBParam('table','PERSONNEL_MEMOS');
	$newrecord -> AddDBParam('object_ID',$updateData['memo_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$_GET['id']);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

	############################################
	## START: SAVE ACTION TO MEMO PROCESS LOG ##
	############################################
	$newrecord2 = new FX($serverIP,$webCompanionPort);
	$newrecord2 -> SetDBData('SIMS_2.fp7','personnel_memos_log');
	$newrecord2 -> SetDBPassword($webPW,$webUN);
	$newrecord2 -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord2 -> AddDBParam('action','SIGN_MEMO_PBA');
	$newrecord2 -> AddDBParam('memo_ID',$updateData['memo_ID'][0]);
	$newrecordResult2 = $newrecord2 -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult2['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult2['foundCount'];
	############################################
	## END: SAVE ACTION TO MEMO PROCESS LOG ##
	############################################

	##########################################################
	## START: SEND E-MAIL NOTIFICATION TO HR TO REVIEW DOC ##
	##########################################################
	//$to = 'ewaters@sedl.org';
	$to = 'sliberty@sedl.org';
	$subject = 'SIMS: PERSONNEL MEMO RECEIVED';
	$message = 
	'HR:'."\n\n".
	
	'A new Personnel Memo has been received by SIMS and requires your review.'."\n\n".
	
	'------------------------------------------------------------'."\n".
	' PERSONNEL MEMO DETAILS'."\n".
	'------------------------------------------------------------'."\n".
	'Memo From: '.$updateData['memo_from'][0]."\n".
	'Memo To: '.$updateData['memo_to'][0]."\n".
	'Memo Subject: '.$updateData['memo_subject'][0]."\n".
	'------------------------------------------------------------'."\n\n".
	
	'To review and release this personnel memo to the second signer, click here:'."\n".
	
	'http://www.sedl.org/staff/sims/menu_memos_admin.php?action=show_memo&record_ID='.$updateData['memo_ID'][0]."\n\n".
	
	'---------------------------------------------------------------------------------------------------------------------------------'."\n".
	'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
	'---------------------------------------------------------------------------------------------------------------------------------';
	
	$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
	
	mail($to, $subject, $message, $headers);
	########################################################
	## END: SEND E-MAIL NOTIFICATION TO HR TO REVIEW DOC ##
	########################################################
	
	}
}

if($_REQUEST['mod'] == 'cpo'){ // CPO SIGNED THE PERSONNEL MEMO
##################################################
## START: SIGN FORM AS CPO ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_memos');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('memo_sign_status_cpo','1');
$update -> AddDBParam('memo_approval_status','Pending');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: SIGN FORM AS CPO ##
##################################################

	if($updateResult['errorCode'] == '0'){
	$cpo_signed = '1';
	
	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','SIGN_PERSONNEL_MEMO_CPO');
	$newrecord -> AddDBParam('table','PERSONNEL_MEMOS');
	$newrecord -> AddDBParam('object_ID',$updateData['memo_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$_GET['id']);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

	############################################
	## START: SAVE ACTION TO MEMO PROCESS LOG ##
	############################################
	$newrecord2 = new FX($serverIP,$webCompanionPort);
	$newrecord2 -> SetDBData('SIMS_2.fp7','personnel_memos_log');
	$newrecord2 -> SetDBPassword($webPW,$webUN);
	$newrecord2 -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord2 -> AddDBParam('action','SIGN_MEMO_CPO');
	$newrecord2 -> AddDBParam('memo_ID',$updateData['memo_ID'][0]);
	$newrecordResult2 = $newrecord2 -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult2['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult2['foundCount'];
	############################################
	## END: SAVE ACTION TO MEMO PROCESS LOG ##
	############################################

	###################################################
	## START: SEND E-MAIL NOTIFICATION TO CEO ##
	###################################################
	//$to = 'ewaters@sedl.org';
	$to = $updateData['memo_signer_ID_ceo'][0].'@sedl.org';
	$subject = 'SIMS: PERSONNEL MEMO REQUIRES YOUR SIGNATURE';
	$message = 
	'CEO,'."\n\n".
	
	'A new Personnel Memo has been received by SIMS that requires your signature.'."\n\n".
	
	'------------------------------------------------------------'."\n".
	' PERSONNEL MEMO DETAILS'."\n".
	'------------------------------------------------------------'."\n".
	'Submitted by: '.$updateData['memo_from'][0]."\n".
	'Submitted to: '.$updateData['memo_to'][0]."\n".
	'Memo Subject: '.$updateData['memo_subject'][0]."\n".
	'------------------------------------------------------------'."\n\n".
	
	'To review and sign this personnel memo, click here:'."\n".
	
	'http://www.sedl.org/staff/sims/menu_memos_ba_admin.php?action=show_memo&record_ID='.$updateData['memo_ID'][0]."\n\n".
	
	'---------------------------------------------------------------------------------------------------------------------------------'."\n".
	'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
	'---------------------------------------------------------------------------------------------------------------------------------';
	
	$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
	
	mail($to, $subject, $message, $headers);
	#################################################
	## END: SEND E-MAIL NOTIFICATION TO CEO ##
	#################################################
	
	}
}

if($_REQUEST['mod'] == 'ceo'){ // CEO SIGNED THE PERSONNEL MEMO
##################################################
## START: SIGN FORM AS CEO ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_memos');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('memo_sign_status_ceo','1');
$update -> AddDBParam('memo_approval_status','Approved');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: SIGN FORM AS CEO ##
##################################################
$pbasigner = $updateData['memo_signer_ID_pba'][0].'@sedl.org';


if($updateResult['errorCode'] == '0'){
$ceo_signed = '1';

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','SIGN_PERSONNEL_MEMO_CEO');
	$newrecord -> AddDBParam('table','PERSONNEL_MEMOS');
	$newrecord -> AddDBParam('object_ID',$updateData['memo_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$_GET['id']);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

	############################################
	## START: SAVE ACTION TO MEMO PROCESS LOG ##
	############################################
	$newrecord2 = new FX($serverIP,$webCompanionPort);
	$newrecord2 -> SetDBData('SIMS_2.fp7','personnel_memos_log');
	$newrecord2 -> SetDBPassword($webPW,$webUN);
	$newrecord2 -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord2 -> AddDBParam('action','APPROVE_MEMO_CEO');
	$newrecord2 -> AddDBParam('memo_ID',$updateData['memo_ID'][0]);
	$newrecordResult2 = $newrecord2 -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult2['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult2['foundCount'];
	############################################
	## END: SAVE ACTION TO MEMO PROCESS LOG ##
	############################################

##########################################################
## START: SEND E-MAIL NOTIFICATION TO HR APPROVED ##
##########################################################
$to = 'sliberty@sedl.org, mturner@sedl.org';
//$to = $updateData['signer_ID_hr'][0].'@sedl.org';
$subject = 'SIMS: PERSONNEL MEMO APPROVED';
$message = 
'HR:'."\n\n".

'A new Personnel Memo has been approved by SIMS.'."\n\n".

'------------------------------------------------------------'."\n".
' PERSONNEL MEMO DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Memo From: '.$updateData['memo_from'][0]."\n".
'Memo To: '.$updateData['memo_to'][0]."\n".
'Memo Type: '.$updateData['memo_subject'][0]."\n".
'------------------------------------------------------------'."\n\n".

'To review this personnel memo, click here:'."\n".

'http://www.sedl.org/staff/sims/menu_memos_admin.php?action=show_memo&record_ID='.$updateData['memo_ID'][0]."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org'."\r\n".'Cc: '.$pbasigner;

mail($to, $subject, $message, $headers);
########################################################
## END: SEND E-MAIL NOTIFICATION TO HR APPROVED ##
########################################################
}
}

if($_REQUEST['mod'] == 'ceo_salary_promotion'){ // CEO SAVED SALARY RECOMMENDATION (PROMOTION MEMO)
##################################################
## START: UPDATE MEMO SALARY RECOMMENDATION ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_memos');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('ceo_salary',$_GET['ceo_salary']);
$update -> AddDBParam('ceo_salary_date_effective',$_GET['ceo_salary_date_effective']);
$update -> AddDBParam('ceo_position_title',$_GET['ceo_position_title']);

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: UPDATE MEMO SALARY RECOMMENDATION ##
##################################################
$record_ID = $updateData['memo_ID'][0];
}

if($_REQUEST['mod'] == 'ceo_salary_hiring'){ // CEO SAVED SALARY & TITLE RECOMMENDATION (HIRING MEMO)
##################################################
## START: UPDATE MEMO SALARY RECOMMENDATION ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_memos');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('ceo_salary',$_GET['ceo_salary']);
$update -> AddDBParam('ceo_position_title',$_GET['ceo_position_title']);

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: UPDATE MEMO SALARY RECOMMENDATION ##
##################################################
$record_ID = $updateData['memo_ID'][0];
}

if($_REQUEST['mod'] == 'send_pba'){ // MEMO CREATOR SENT MEMO TO PBA FOR APPROVAL
##################################################
## START: FIND SELECTED MEMO ##
##################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','personnel_memos');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('memo_ID','=='.$_REQUEST['record_ID']);

$searchResult = $search -> FMFind();

//echo  '<p>errorCode: '.$searchResult['errorCode'];
//echo  '<p>foundCount: '.$searchResult['foundCount'];
$recordData = current($searchResult['data']);
$record_ID = $recordData['memo_ID'][0];
$pba_user_ID = $recordData['memo_signer_ID_pba'][0];
$staff_ID = $recordData['staff_ID'][0];
##################################################
## END: FIND SELECTED MEMO ##
##################################################

############################################
## START: SAVE ACTION TO MEMO PROCESS LOG ##
############################################
$newrecord2 = new FX($serverIP,$webCompanionPort);
$newrecord2 -> SetDBData('SIMS_2.fp7','personnel_memos_log');
$newrecord2 -> SetDBPassword($webPW,$webUN);
$newrecord2 -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord2 -> AddDBParam('action','SUBMIT_TO_PBA');
$newrecord2 -> AddDBParam('memo_ID',$recordData['memo_ID'][0]);
$newrecordResult2 = $newrecord2 -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult2['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult2['foundCount'];
############################################
## END: SAVE ACTION TO MEMO PROCESS LOG ##
############################################

##########################################################
## START: SEND E-MAIL NOTIFICATION TO HR TO REVIEW DOC ##
##########################################################
//$to = 'ewaters@sedl.org';
$to = $pba_user_ID.'@sedl.org';
$subject = 'SIMS: PERSONNEL MEMO RECEIVED';
$message = 
'Budget Authority:'."\n\n".

'A new Personnel Memo has been received by SIMS and requires your review.'."\n\n".

'------------------------------------------------------------'."\n".
' PERSONNEL MEMO DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Prepared by: '.$_SESSION['user_ID']."\n".
'Memo From: '.$recordData['memo_from'][0]."\n".
'Memo To: '.$recordData['memo_to'][0]."\n".
'Memo Subject: '.$recordData['memo_subject'][0]."\n".
'------------------------------------------------------------'."\n\n".

'To review and approve this personnel memo, click here:'."\n".

'http://www.sedl.org/staff/sims/menu_memos_ba_admin.php?action=show_memo&record_ID='.$recordData['memo_ID'][0]."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';

mail($to, $subject, $message, $headers);
########################################################
## END: SEND E-MAIL NOTIFICATION TO HR TO REVIEW DOC ##
########################################################
$_SESSION['sent_to_pba'] = '1';
}

##################################################
## START: FIND SELECTED PERSONNEL MEMO ##
##################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','personnel_memos');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('memo_ID','=='.$record_ID);
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam($sortfield,'PERIODEND');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
################################################
## END: FIND SELECTED PERSONNEL MEMO ##
################################################

#################################################################
## START: FIND USER LOG ENTRIES RELATED TO THIS MEMO ##
#################################################################
$search6 = new FX($serverIP,$webCompanionPort);
$search6 -> SetDBData('SIMS_2.fp7','personnel_memos_log');
$search6 -> SetDBPassword($webPW,$webUN);
$search6 -> AddDBParam('memo_ID','=='.$record_ID);
//$search6 -> AddDBParam('-lop','or');

$search6 -> AddSortParam('creation_timestamp','ascend');


$searchResult6 = $search6 -> FMFind();

//echo '<p>$searchResult6[errorCode]: '.$searchResult6['errorCode'];
//echo '<p>$searchResult6[foundCount]: '.$searchResult6['foundCount'];
//print_r ($searchResult6);
$recordData6 = current($searchResult6['data']);
###############################################################
## END: FIND USER LOG ENTRIES RELATED TO THIS MEMO ##
###############################################################

//if($recordData['c_attachment_count'][0] > 0){ // THE SELECTED PR HAS ATTACHMENTS
#################################################################
## START: FIND ATTACHMENTS RELATED TO THIS MEMO ##
#################################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','personnel_attachments');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('document_ID','=='.$record_ID);
//$search3 -> AddDBParam('-lop','or');

//$search3 -> AddSortParam('leave_hrs_date','ascend');


$searchResult3 = $search3 -> FMFind();

//echo '<p>$searchResult3[errorCode]: '.$searchResult3['errorCode'];
//echo '<p>$searchResult3[foundCount]: '.$searchResult3['foundCount'];
//print_r ($searchResult3);
$recordData3 = current($searchResult3['data']);
###############################################################
## END: FIND ATTACHMENTS RELATED TO THIS MEMO ##
###############################################################
//}

$first_day_of_this_month = date("m/01/Y");
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Personnel Memos</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Kameron:400,700' rel='stylesheet' type='text/css'>
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>

<script language="JavaScript">

function confirmSign() { 
	var answer = confirm ("Sign this Personnel Memo?")
	if (!answer) {
	return false;
	}
}

function submittoPBA() { 
	var answer = confirm ("Submit this Personnel Memo to the Unit Budget Authority?")
	if (!answer) {
	return false;
	}
}

function preventSignPBA() { 
	alert ("This signature box is reserved for the Unit Budget Authority. To sign this personnel memo, click the box with your ID.")
	return false;
}

function preventSignHR() { 
	alert ("This signature box is reserved for the HR Generalist. To sign this personnel memo, click the box with your ID.")
	return false;
}

function preventSignCPO() { 
	alert ("This signature box is reserved for the CPO. To sign this personnel memo, click the box with your ID.")
	return false;
}

function preventSignCEO() { 
	alert ("This signature box is reserved for the CEO. To sign this personnel memo, click the box with your ID.")
	return false;
}

function preventSignCEO2() { 
	alert ("CEO: Please enter a recommended salary before signing this personnel memo.")
	return false;
}

function preventApproveCEO() { 
	alert ("This personnel memo must be signed by unit budget authority before final CEO approval. You will receive an e-mail when final approval is required.")
	return false;
}

function ExCOnly() { 
	alert  ("This section reserved for the CEO.")
	return false;
}

function toggle1() {
	var ele = document.getElementById("toggleText");
	var text = document.getElementById("displayText");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Enter salary & position title";
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
		text.innerHTML = "Enter salary & position title";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "CANCEL";
	}
} 

function toggle3() {
	var ele = document.getElementById("toggleText3");
	var text = document.getElementById("displayText3");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Reject Memo";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "Cancel Rejection";
	}
} 

function toggle4() {
	var ele = document.getElementById("toggleText4");
	var text = document.getElementById("displayText4");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Add Comments";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "CANCEL";
	}
} 

function toggle5() {
	var ele = document.getElementById("toggleText5");
	var text = document.getElementById("displayText5");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Add Document";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "CANCEL";
	}
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

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table cellpadding="0" cellspacing="0" border="0" width="930">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Personnel Memos</h1><hr /></td></tr>
			

			<?php if($_SESSION['sent_to_pba'] == '1'){?>
			<tr><td colspan="2" style="padding-left:20px"><p class="alert_small" style="margin-bottom:8px"><strong>Budget Authority</strong>: <?php echo $_SESSION['user_ID'];?> | Notification sent to unit budget authority. | <a href="http://www.sedl.org/staff/sims/menu_memos_ba_admin.php?action=show_1&staff_ID=<?php echo $recordData['staff_ID'][0];?>">Return to list</a></p>
			</td></tr>
			<?php $_SESSION['sent_to_pba'] = '';}?>
			
			<?php if($newrecordcreated == '1'){?>
			<tr><td colspan="2" style="padding-left:20px"><p class="alert_small"><strong>Budget Authority</strong>: <?php echo $_SESSION['user_ID'];?> | Memorandum successfully created | <a href="http://www.sedl.org/staff/sims/menu_memos_ba_admin.php">Close memo</a><?php if(($_SESSION['user_ID'] != $recordData['memo_signer_ID_pba'][0])&&($recordData['memo_signer_status_staff'][0] != '1')&&($recordData['memo_signer_status_pba'][0] != '1')&&($recordData['memo_signer_status_ceo'][0] != '1')){?> | <a href="http://www.sedl.org/staff/sims/menu_memos_ba_admin.php?action=show_memo&record_ID=<?php echo $recordData['memo_ID'][0];?>&mod=send_pba" onclick="return submittoPBA()">Submit to PBA</a><?php }?></p>
			</td></tr>
			<?php }elseif($newrecordcreated == '2'){?>
			<tr><td colspan="2" style="padding-left:20px"><p class="alert_small">There was a problem creating the memorandum (Error: <?php echo $newrecorderror;?>) | <a href="http://www.sedl.org/staff/sims/menu_memos_ba_admin.php">Return to workgroup memos</a></p>
			</td></tr>
			<?php }elseif($pba_signed == '1'){?>
			<tr><td colspan="2" style="padding-left:20px"><p class="alert_small"><strong>Budget Authority</strong>: <?php echo $_SESSION['user_ID'];?> | You have successfully approved and submitted this memo. | <a href="http://www.sedl.org/staff/sims/menu_memos_ba_admin.php">Return to workgroup memos</a></p>
			</td></tr>
			<?php }elseif($ceo_signed == '1'){?>
			<tr><td colspan="2" style="padding-left:20px"><p class="alert_small"><strong>CEO</strong>: <?php echo $_SESSION['user_ID'];?> | You have successfully approved this memo. | <a href="http://www.sedl.org/staff/sims/menu_memos_ba_admin.php">Return to workgroup memos</a></p>
			</td></tr>
			<?php }elseif($cpo_signed == '1'){?>
			<tr><td colspan="2" style="padding-left:20px"><p class="alert_small"><strong>CPO</strong>: <?php echo $_SESSION['user_ID'];?> | You have successfully approved this memo. | <a href="http://www.sedl.org/staff/sims/menu_memos_ba_admin.php">Return to workgroup memos</a></p>
			</td></tr>
			<?php }else{?>
			<tr><td colspan="2" style="padding-left:20px"><div class="alert_small"><strong>Budget Authority</strong>: <?php echo $_SESSION['user_ID'];?> | This personnel memorandum is <?php echo $recordData['memo_approval_status'][0];?> | <a href="javascript:history.back();">Close memo</a> | <a href="menu_memos_admin.php?action=show_memo_print&record_ID=<?php echo $recordData['memo_ID'][0];?>" target="_blank">Print memo</a><?php if(($_SESSION['user_ID'] != $recordData['memo_signer_ID_pba'][0])&&($recordData['memo_signer_status_staff'][0] != '1')&&($recordData['memo_signer_status_pba'][0] != '1')&&($recordData['memo_signer_status_ceo'][0] != '1')){?> | <a href="http://www.sedl.org/staff/sims/menu_memos_ba_admin.php?action=show_memo&record_ID=<?php echo $recordData['memo_ID'][0];?>&mod=send_pba" onclick="return submittoPBA()">Submit to PBA</a><?php }?>

				<div style="float:right;padding:0px;margin-top:-3px"><input id="displayText3" type="button" name="reject" value="Reject Memo" onClick="toggle3();"></div>
				<div id="toggleText3" style="display: none; background-color:#ebebeb; border: 1px dotted #999999;padding:8px 0px 0px 8px;margin:8px">
				<form method="get">
				<input type="hidden" name="action" value="show_memo">
				<input type="hidden" name="mod" value="reject">
				<input type="hidden" name="rid" value="<?php echo $recordData['c_row_ID'][0];?>">
				<input type="hidden" name="record_ID" value="<?php echo $recordData['memo_ID'][0];?>">
				Enter a reason for rejecting this personnel memo: <input type="text" name="memo_reject_reason" size="50">
				<input type="submit" name="submit" value="Submit">
				</form>
				</div>

			</div>
			</td></tr>
			<?php }?>
			
			<tr><td colspan="2">

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $recordData['memo_subject'][0];?></strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Status: <strong><?php if($recordData['memo_approval_status'][0] !== 'Approved'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['memo_approval_status'][0]).'</span>';?></strong></td></tr>
						<tr><td class="body" nowrap><strong>MEMORANDUM</strong></td><td align="right">ID: <?php echo $recordData['memo_ID'][0];?> <?php if($recordData['memo_approval_status'][0] == 'Approved'){ ?> | <img src="/staff/sims/images/padlock.jpg" border="0" title="This memo is locked."> <?php }?></td></tr>
						
						<tr><td colspan="2">
						





<!-- START MEMO TABLE -->
							<table cellpadding="7" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
							
<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    MEMO DETAILS             ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<tr><td class="body" style="vertical-align:text-top;width:100%"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>MEMO DETAILS</strong></div><br>

									<table width="100%" cellpadding="0" cellspacing="0" border="0">
									<tr>
									<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333"><strong>TO</strong>:</td>
									<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['memo_to'][0];?><br><?php echo $recordData['memo_to_title'][0];?></td>
									</tr>
								
									<tr>
									<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333"><strong>FROM</strong>:</td>
									<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_from'][0];?><br><?php echo $recordData['memo_from_title'][0];?><br><?php echo $recordData['memo_unit_abbrev'][0];?></td>
									</tr>
								
									<tr>
									<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333"><strong>DATE</strong>:</td>
									<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_date'][0];?></td>
									</tr>
								
									<tr>
									<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333"><strong>SUBJECT</strong>:</td>
									<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_subject'][0];?></td>
									</tr>
									</table>

								</td>
<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    APPROVAL LOG             ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<td style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>APPROVAL LOG</strong></div>



								
									<table cellspacing="2" style="margin-top:6px">

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf"><strong>DATE</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>USER</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>ACTION</strong></td></tr>

									<?php if($searchResult6['foundCount'] > 0){ // SHOW MEMO PROCESS LOG ?>
									<?php foreach($searchResult6['data'] as $key => $searchData6) { if(($searchData6['comment'][0] != '')&&($searchData6['action'][0] == 'REJECT_MEMO')){$rowcolor = '#ffd6d6';}elseif(($searchData6['comment'][0] != '')&&($searchData6['action'][0] == 'COMMENTS')){$rowcolor = '#b6cafb';}elseif(($searchData6['action'][0] == 'APPROVE_MEMO_CEO')||($searchData6['action'][0] == 'SIGN_MEMO_STAFF')){$rowcolor = '#ace29f';}else{$rowcolor = '#ebebeb';} ?>

										<tr><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData6['creation_timestamp'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData6['user'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData6['action'][0];?></td></tr>
										<?php if($searchData6['comment'][0] != ''){?><tr><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" colspan="3"><?php echo $searchData6['comment'][0];?></td></tr><?php }?>

									<?php } ?>
									<?php } ?>
									<tr><td colspan="3" style="border:0px">
									<div>	
										<span class="tiny"><a href="javascript:toggle4();" id="displayText4" title="Click to add comments to the memo log.">Add Comments</a></span>
										<div id="toggleText4" style="display: none; padding:10px 10px 0px 10px;border:1px dotted #999999;background-color:#fff6bf"><strong>Comments:</strong>
										<form method="get">
										<input type="hidden" name="action" value="show_memo">
										<input type="hidden" name="mod" value="comments">
										<input type="hidden" name="record_ID" value="<?php echo $recordData['memo_ID'][0];?>">
										<input type="hidden" name="id" value="<?php echo $recordData['c_row_ID'][0];?>">
										<input type="text" name="comments" size="25">
										<input type="submit" name="submit" value="Submit">
										</form>
										</div>
									</div>
									</td></tr>	
									</table>
								</td>
								</tr>


<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    MEMO CONTENT             ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<tr><td colspan="2" class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>MEMO CONTENT</strong></div><br>

<?php if($_REQUEST['mod'] == 'edit'){ // EDIT MEMO CONTENT PRIOR TO HR RELEASE ?>

												<table width="100%">
												<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">

												<form method="get">
												<input type="hidden" name="action" value="show_memo">
												<input type="hidden" name="mod" value="edit_confirm">
												<input type="hidden" name="record_ID" value="<?php echo $recordData['memo_ID'][0];?>">
												<input type="hidden" name="id" value="<?php echo $recordData['c_row_ID'][0];?>">
												<textarea name="memo_body" style="width:100%" rows="25"><?php echo $recordData['c_memo_body_html'][0];?></textarea>
												<input type="submit" name="submit" value="Save Changes">
												</form>

												</td></tr>
												</table>

<?php }else{ // SHOW MEMO CONTENT  ?>

												<table width="100%">
												<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
												<p><?php echo $recordData['c_memo_body_html'][0];?></p>
												<?php if($recordData['doc_release_status'][0] == '0'){ ?>
												<p><span class="tiny"><a href="menu_memos_ba_admin.php?action=show_memo&mod=edit&record_ID=<?php echo $recordData['memo_ID'][0];?>">Edit Memo Content</a><?php if($recordData['memo_type'][0] == 'Hiring recommendation'){?> | <a href="http://www.sedl.org/staff/sims/documentation/hiring_recommendation_guidelines.pdf">SEDL Hiring Recommendation Guidelines</a><?php } ?></span></p>
												<?php } ?>
												</td></tr>
												</table>

<?php } ?>
								</td></tr>

<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    RELATED DOCUMENTS        ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<tr><td colspan="2" class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><a name="docs"></a><strong>RELATED DOCUMENTS</strong></div><br>

								<table>
								<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf"><strong>#</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf"><strong>DESCRIPTION</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>UPLOADED</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>LINK</strong></td></tr>
<?php if($recordData['memo_type'][0] == 'Hiring recommendation'){?>
									<tr><td class="tiny" style="background-color:#ffffff;vertical-align:text-top;border:0px dotted #000000;padding:3px" nowrap><strong>A</strong></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap>SIMS Application</td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap>See applications database</td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><a href="http://www.sedl.org/staff/sims/positions_novs_ba.php?id=<?php echo $recordData['resume_ID'][0];?>" target="_blank" title="Click to view application documents.">View</a></td></tr>
<?php }?>
								<?php if($searchResult3['foundCount'] > 0){ $i=1;// SHOW MEMO ATTACHMENTS ?>
								<?php foreach($searchResult3['data'] as $key => $searchData3) { ?>

									<tr><td class="tiny" style="background-color:#ffffff;vertical-align:text-top;border:0px dotted #000000;padding:3px" nowrap><strong><?php echo $i;?>.</strong></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData3['attachment_description'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData3['uploaded_timestamp'][0];?> by <?php echo $searchData3['uploaded_by'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><a href="http://198.214.141.190/sims/personnel_docs/<?php echo urlencode($searchData3['attachment_filename'][0]);?>" target="_blank" title="Click to download this attachment for review.">Download</a></td></tr>

								<?php $i++;} ?>

								<?php }else{ ?>

									<tr><td class="tiny" colspan="4">N/A</td></tr>

								<?php } ?>
									<tr><td colspan="4" style="border:0px">
									<div>	
										<span class="tiny"><a href="javascript:toggle5();" id="displayText5" title="Click to add related documents to this memo.">Add Document</a></span>
										<div id="toggleText5" style="display: none; padding:10px 10px 0px 10px;border:1px dotted #999999;background-color:#fff6bf"><strong>Enter document description and select file:</strong>

										<form action="http://198.214.141.190/sims/personnel_docs_upload_2.php" method="post" enctype="multipart/form-data">
										<input type="hidden" name="action" value="show_memo">
										<input type="hidden" name="record_ID" value="<?php echo $recordData['memo_ID'][0];?>">
										<input type="hidden" name="user" value="<?php echo $_SESSION['user_ID'];?>">
										<input type="hidden" name="id" value="<?php echo $recordData['c_row_ID'][0];?>">
										Description: <input type="text" name="attachment_description" size="40"><br>

										<input type="file" name="file" id="file" /> 
										<br />
										<input type="submit" name="submit" value="Save" />
										</form>

										</div>
									</div>
									</td></tr>	
								
								</table>




								</td></tr>

<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    APPROVAL SIGNATURES      ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<tr class="body"><td colspan="2" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>APPROVAL SIGNATURES</strong></div><br>
								
<?php if(($recordData['memo_type'][0] == 'Terms of employment')||($recordData['memo_type'][0] == 'Removal of probationary employment status')){ // SIGNED BY PBA AND NEW STAFF MEMBER ?>
<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>1. MANAGER RECOMMENDING ACTION</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_pba'][0] !== '1'){?>	<a href="menu_memos_ba_admin.php?action=show_memo&mod=pba&record_ID=<?php echo $recordData['memo_ID'][0];?>&id=<?php echo $recordData['c_row_ID'][0];?>" onclick="return confirmSign()" title="Unit Budget Authority: Click here to approve and submit this memo to SIMS."><?php echo $recordData['memo_signer_ID_pba'][0];?></a>
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_pba'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_pba'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>UNIT BUDGET AUTHORITY</strong></span>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">

	<strong>2. STAFF MEMBER</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_staff'][0] !== '1'){?>Pending staff signature (<?php if($recordData['memo_signer_ID_staff'][0] == ''){echo 'NEW STAFF';}else{echo $recordData['memo_signer_ID_staff'][0];}?>)
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_staff'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_staff'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>STAFF MEMBER</strong></span>
	
</td></tr>


</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="color:#666666" align="right"><br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<span class="tiny">Document ID: <?php echo $recordData['memo_ID'][0];?></span></td></tr>
</table>
<?php } ?>



<?php if(($recordData['memo_type'][0] == 'Hiring recommendation')||($recordData['memo_type'][0] == 'Promotion')||($recordData['memo_type'][0] == 'Reassignment')){ // SIGNED BY PBA AND CEO AND CPO (FOR PROGRAMMATIC STAFF - NOT COM, AS, EO, OR DEV) ?>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td width="33%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>1. MANAGER RECOMMENDING ACTION</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_pba'][0] !== '1'){?>	<a href="menu_memos_ba_admin.php?action=show_memo&mod=pba&record_ID=<?php echo $recordData['memo_ID'][0];?>&id=<?php echo $recordData['c_row_ID'][0];?>" <?php if($recordData['memo_signer_ID_pba'][0] == $_SESSION['user_ID']){echo 'onclick="return confirmSign()"';}else{echo 'onclick="return preventSignPBA()"';}?> title="Unit Budget Authority: Click here to approve and submit this memo to SIMS."><?php echo $recordData['memo_signer_ID_pba'][0];?></a>
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_pba'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_pba'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>UNIT BUDGET AUTHORITY</strong></span>

</td><td width="33%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

<?php if(($recordData['memo_unit_abbrev'][0] == 'AS')||($recordData['memo_unit_abbrev'][0] == 'DEV')||($recordData['memo_unit_abbrev'][0] == 'EO')||($recordData['memo_unit_abbrev'][0] == 'COM')){?>

	<strong>2. CPO APPROVAL</strong><br>
	
	<span style="margin-left:15px">
			<span class="tiny">NOT REQUIRED</span>
	</span><br>
	<span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>CHIEF PROGRAM OFFICER</strong></span>

<?php }else{?>

	<strong>2. CPO APPROVAL</strong><br>
	
	<span style="margin-left:15px">
		<?php if($recordData['memo_sign_status_cpo'][0] !== '1'){ // CPO HAS NOT SIGNED THE DOCUMENT ?>	
			<a href="menu_memos_ba_admin.php?action=show_memo&mod=cpo&record_ID=<?php echo $recordData['memo_ID'][0];?>&id=<?php echo $recordData['c_row_ID'][0];?>" <?php if($recordData['memo_signer_ID_cpo'][0] == $_SESSION['user_ID']){echo 'onclick="return confirmSign()"';}else{echo 'onclick="return preventSignCPO()"';}?> title="CPO: Click here to approve this memo."><?php echo $recordData['memo_signer_ID_cpo'][0];?></a> 
		<?php }else{ ?>
			<img src="signatures/<?php echo $recordData['memo_signer_ID_cpo'][0];?>.png"><br>
			<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_cpo'][0];?>]</span>
		<?php }?>
	</span><br>
	<span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>CHIEF PROGRAM OFFICER</strong></span>

<?php }?>

</td><td width="33%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">

<?php if(($recordData['memo_sign_status_ceo'][0] !== '1')&&($recordData['memo_type'][0] == 'Hiring recommendation')){ // CEO HAS NOT SIGNED THE DOCUMENT AND MEMO_TYPE = "Hiring recommendation", SHOW SALARY FIELD FOR CEO ?>

	<div style="float:right"><?php if($recordData['ceo_salary'][0] != ''){?><span class="tiny">Recommended Salary: <?php echo $recordData['ceo_salary'][0];?></span><br><?php }?><?php if($recordData['ceo_position_title'][0] != ''){?><span class="tiny">Title: <?php echo $recordData['ceo_position_title'][0];?></span><br><?php }?>
		<span class="tiny" style="align:right"><a href="javascript:toggle1();" id="displayText" title="CEO: Click to enter salary recommendation and position title." <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return ExCOnly()"';}?>>Enter salary & position title</a></span>
		<div id="toggleText" style="display: none; padding:10px 10px 0px 10px;border:1px dotted #999999;background-color:#fff6bf"><strong>CEO Recommendations</strong>
		<form method="get" style="text-align:right">
		<input type="hidden" name="action" value="show_memo">
		<input type="hidden" name="mod" value="ceo_salary_hiring">
		<input type="hidden" name="record_ID" value="<?php echo $recordData['memo_ID'][0];?>">
		<input type="hidden" name="id" value="<?php echo $recordData['c_row_ID'][0];?>">
		Salary: <input type="text" name="ceo_salary" value="<?php echo $recordData['ceo_salary'][0];?>" size="25"><br>
		Title: <input type="text" name="ceo_position_title" value="<?php echo $recordData['ceo_position_title'][0];?>" size="25"><br>
		<input type="submit" name="submit" value="Submit">
		</form>
		</div>
	</div>
	
<?php }?>

<?php if(($recordData['memo_sign_status_ceo'][0] !== '1')&&($recordData['memo_type'][0] == 'Promotion')){ // CEO HAS NOT SIGNED THE DOCUMENT AND MEMO_TYPE = "Promotion", SHOW SALARY & EFFECTIVE DATE FIELDS FOR CEO ?>

	<div style="float:right"><?php if($recordData['ceo_salary'][0] != ''){?><span class="tiny">Recommended Salary: <?php echo $recordData['ceo_salary'][0];?><br>Effective Date: <?php echo $recordData['ceo_salary_date_effective'][0];?><br>Title: <?php echo $recordData['ceo_position_title'][0];?></span><br><?php }?>
		<span class="tiny" style="align:right"><a href="javascript:toggle2();" id="displayText2" title="CEO: Click to enter salary recommendation." <?php if($_SESSION['user_ID'] != 'whoover'){echo '';}?>>Enter salary & position title</a></span>
		<div id="toggleText2" style="display: none; padding:10px 10px 0px 10px;border:1px dotted #999999;background-color:#fff6bf"><strong>CEO Recommendation</strong>
		<form method="get">
		<input type="hidden" name="action" value="show_memo">
		<input type="hidden" name="mod" value="ceo_salary_promotion">
		<input type="hidden" name="record_ID" value="<?php echo $recordData['memo_ID'][0];?>">
		<input type="hidden" name="id" value="<?php echo $recordData['c_row_ID'][0];?>">
		<input type="text" name="ceo_salary" value="<?php echo $recordData['ceo_salary'][0];?>" size="15"> <span class="tiny">Salary</span><br>
		<input type="text" name="ceo_salary_date_effective" value="<?php if($recordData['ceo_salary_date_effective'][0] != ''){echo $recordData['ceo_salary_date_effective'][0];}else{ echo $first_day_of_this_month;}?>" size="15"> <span class="tiny">Date effective (mm/dd/yyyy)</span><br>
		<input type="text" name="ceo_position_title" value="<?php echo $recordData['ceo_position_title'][0];?>" size="15"> <span class="tiny">New Position Title</span><br>
		<input type="submit" name="submit" value="Submit">
		</form>
		</div>
	</div>
	
<?php }?>

	<strong>3. CEO APPROVAL</strong><br>
	
	<span style="margin-left:15px">
		<?php if($recordData['memo_sign_status_ceo'][0] !== '1'){ // CEO HAS NOT SIGNED THE DOCUMENT ?>	
			<a href="menu_memos_ba_admin.php?action=show_memo&mod=ceo&record_ID=<?php echo $recordData['memo_ID'][0];?>&id=<?php echo $recordData['c_row_ID'][0];?>" <?php if(($recordData['memo_signer_ID_ceo'][0] == $_SESSION['user_ID'])&&($recordData['ceo_salary'][0] != '')){echo 'onclick="return confirmSign()"';}elseif(($recordData['memo_signer_ID_ceo'][0] == $_SESSION['user_ID'])&&($recordData['ceo_salary'][0] == '')){echo 'onclick="return preventSignCEO2()"';}else{echo 'onclick="return preventSignCEO()"';}?> title="CEO: Click here to approve this memo."><?php echo $recordData['memo_signer_ID_ceo'][0];?></a> 
		<?php }else{ ?>
			<img src="signatures/<?php echo $recordData['memo_signer_ID_ceo'][0];?>.png"><br>
			<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_ceo'][0];?>]</span>
		<?php }?>
	</span><br>
	<span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>PRESIDENT AND CEO</strong></span>


</td></tr>


</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="color:#666666" align="right"><br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<span class="tiny">Document ID: <?php echo $recordData['memo_ID'][0];?></span></td></tr>
</table>
<?php } ?>
								

						

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
}elseif($action == 'new'){ // CREATE NEW MEMORANDUM (BUDGET AUTHORITY) FOR AN EXISTING STAFF MEMBER

/*
THIS SECTION IS USED TO CREATE THE FOLLOWING PERSONNEL MEMOS (CREATED FOR EXISTING STAFF MEMBERS BY THE UNIT PBA OR AA)

1. JUSTIFICATION OF PROMOTION MEMO
2. REMOVAL OF PROBATIONARY EMPLOYMENT STATUS MEMO

*/




$staff_ID = $_GET['id'];

##################################################
## START: GET SELECTED STAFF DETAILS ##
##################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff_table','all');
$search -> SetDBPassword($webPW,$webUN);

$search -> AddDBParam('staff_ID',$staff_ID);

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);

$staffname = $recordData['c_full_name_first_last'][0];
$stafftitle = $recordData['job_title'][0];
$staffunit = $recordData['primary_SEDL_workgroup'][0];
$pba_user_ID = $recordData['bgt_auth_primary_sims_user_ID'][0];
################################################
## END: GET SELECTED STAFF DETAILS ##
################################################

##################################################
## START: GET PBA DETAILS ##
##################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff_table','all');
$search2 -> SetDBPassword($webPW,$webUN);

$search2 -> AddDBParam('sims_user_ID',$pba_user_ID);

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);

$fullname = $recordData2['c_full_name_first_last'][0];
$title = $recordData2['job_title'][0];
$unit = $recordData2['primary_SEDL_workgroup'][0];
//echo $unit;
################################################
## END: GET PBA DETAILS ##
################################################
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Personnel Memos</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Kameron:400,700' rel='stylesheet' type='text/css'>
<script language="JavaScript">

function UpdateSelect(){

	select_value = "";
	select_value = document.pa_form.memo_type.value;

	var id = 'memo_promotion';
	var obj = '';
	obj = (document.getElementById) ? document.getElementById(id) : ((document.all) ? document.all[id] : ((document.layers) ? document.layers[id] : false));
	
	
	if(select_value == ""){
	  obj.style.display = 'none';
	  //alert("onBodyLoad.");
	
	} else if(select_value == "Promotion"){
	  // alert("You chose Journal article.");
	  // return false;
	  obj.style.display = 'block';
	}
	else
	{
	  obj.style.display = 'none';
	}


	var id3 = 'memo_reassignment';
	var obj3 = '';
	obj3 = (document.getElementById) ? document.getElementById(id3) : ((document.all) ? document.all[id3] : ((document.layers) ? document.layers[id3] : false));
	
	
	if(select_value == ""){
	  obj3.style.display = 'none';
	  //alert("onBodyLoad.");
	
	} else if(select_value == "Reassignment"){
	  // alert("You chose Journal article.");
	  // return false;
	  obj3.style.display = 'block';
	}
	else
	{
	  obj3.style.display = 'none';
	}


	var id2 = 'memo_removal_pes';
	var obj2 = '';
	obj2 = (document.getElementById) ? document.getElementById(id2) : ((document.all) ? document.all[id2] : ((document.layers) ? document.layers[id2] : false));
	
	
	if(select_value == ""){
	  obj2.style.display = 'none';
	  //alert("onBodyLoad.");
	
	} else if(select_value == "Removal of probationary employment status"){
	  // alert("You chose Journal article.");
	  // return false;
	  obj2.style.display = 'block';
	}
	else
	{
	  obj2.style.display = 'none';
	}

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

<BODY BGCOLOR="#FFFFFF" onLoad="UpdateSelect();">
<form method="post" name="pa_form" id="pa_form">
<input type="hidden" name="action" value="show_memo">
<input type="hidden" name="mod" value="submit_new_memo">
<input type="hidden" name="staff_ID" value="<?php echo $staff_ID;?>">
<input type="hidden" name="pba_user_ID" value="<?php echo $pba_user_ID;?>">
<input type="hidden" name="memo_unit_abbrev" value="<?php echo $unit;?>">

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="vertical-align:text-top;padding:6px;border:0px"><img src="/images/print_sedl.jpg"></td>
<td align="right" style="vertical-align:text-top;padding:6px;border:0px">

	<h1>NEW MEMORANDUM</h1>

</td></tr>
</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border:0px solid #333333">

	<select name="memo_type" id="memo_type" onChange="UpdateSelect();">
	<option value="">Select the Memo Type</option>
	<option value="">--------------------------</option>
	<option value="Promotion">Promotion Memo</option>
	<option value="Reassignment">Reassignment Memo</option>
	<option value="Removal of probationary employment status">Removal of Probationary Employment Status Memo</option>
	</select>

</td></tr>
</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:0px">
<tr><td style="vertical-align:text-top;padding:0px;border:0px solid #333333">
<div id="memo_promotion">
<div class="alert_small" style="border:1px dotted #666666;margin-left:20px"><strong>Budget Authority</strong>: Edit the content of this memorandum below then click the "Submit" button.</div>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">TO:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%">Wes Hoover<br>President and CEO<input type="hidden" name="memo_to" value="Wes Hoover"><input type="hidden" name="memo_to_title" value="President and CEO"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">FROM:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $fullname.'<br>'.$title.'<br>'.$unit;?><input type="hidden" name="memo_from" value="<?php echo $fullname;?>"><input type="hidden" name="memo_from_title" value="<?php echo $title;?>"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">DATE:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo date("F j, Y");?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%">Promotion of <?php echo $staffname;?><input type="hidden" name="memo_subject" value="Promotion of <?php echo $staffname;?>"></td>
	</tr>

	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">

	<textarea name="memo_body" style="width:100%" rows="25">I recommend <?php echo $staffname;?> be promoted to the position of <<NEW POSITION TITLE>> for the <<ENTER NAME OF WORKGROUP/UNIT>> effective <<ENTER EFFECTIVE DATE OF PROMOTION>>. This will fall on pay grade <<ENTER NEW PAY GRADE>> of SEDL's salary schedule based on a review of similar positions at SEDL and descriptions of those positions in Watson Wyatt literature describing those positions.
	
<<ADD ADDITIONAL PARAGRAPH(S) JUSTIFYING THIS PROMOTION>></textarea>
	</td></tr>
	</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td><div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Submit"></div></td></tr>

</table>

</div>

<div id="memo_reassignment">
<div class="alert_small" style="border:1px dotted #666666;margin-left:20px"><strong>Budget Authority</strong>: Edit the content of this memorandum below then click the "Submit" button.</div>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">TO:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%">Wes Hoover<br>President and CEO<input type="hidden" name="memo_to3" value="Wes Hoover"><input type="hidden" name="memo_to_title3" value="President and CEO"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">FROM:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $fullname.'<br>'.$title.'<br>'.$unit;?><input type="hidden" name="memo_from3" value="<?php echo $fullname;?>"><input type="hidden" name="memo_from_title3" value="<?php echo $title;?>"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">DATE:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo date("F j, Y");?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%">Reassignment of <?php echo $staffname;?><input type="hidden" name="memo_subject3" value="Reassignment of <?php echo $staffname;?>"></td>
	</tr>

	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">

	<textarea name="memo_body3" style="width:100%" rows="25"><<ENTER RE-ASSIGNMENT MEMO TEXT>></textarea>
	</td></tr>
	</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td><div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Submit"></div></td></tr>

</table>

</div>


<div id="memo_removal_pes">
<div class="alert_small" style="border:1px dotted #666666;margin-left:20px"><strong>Budget Authority</strong>: <?php echo $fullname;?> | Edit the content of this memorandum below then click the "Submit" button.</div>
<input type="hidden" name="memo_to2" value="Wes Hoover, President and CEO">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">TO:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $staffname;?><br><?php echo $stafftitle;?><input type="hidden" name="memo_to2" value="<?php echo $staffname;?>"><input type="hidden" name="memo_to_title2" value="<?php echo $stafftitle;?>"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">FROM:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $fullname.'<br>'.$title.'<br>'.$unit;?><input type="hidden" name="memo_from2" value="<?php echo $fullname;?>"><input type="hidden" name="memo_from_title2" value="<?php echo $title;?>"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">DATE:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo date("F j, Y");?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%">Removal of Probationary Employment Status<input type="hidden" name="memo_subject2" value="Removal of Probationary Employment Status"></td>
	</tr>

	
	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	
	<textarea name="memo_body2" style="width:100%" rows="25"><<SIX MONTH ANNIVERSARY DATE>> marks the six-month anniversary of your service as <?php echo $stafftitle;?> with the <<UNIT NAME>> at SEDL. As you know, a condition of your initial employment with SEDL was that you would be on probationary status for your first 6 months. At the conclusion of this time period, your performance would be reviewed and a decision made regarding adjusting your status from probationary to regular employment.

I am pleased to inform you that based upon my observations of your work, and as we discussed in your recent performance review, I am recommending that your status be changed from probationary to regular. This is being accomplished through a Personnel Action. 
</textarea>
	</td></tr>
	</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td><div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Submit"></div></td></tr>

</table>


</div>

</form>

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
}elseif($action == 'new_unassigned'){ // CREATE NEW UNASSIGNED MEMORANDUM (BUDGET AUTHORITY)
/*
THIS SECTION IS USED TO CREATE THE FOLLOWING UNASSIGNED MEMOS (CREATED BEFORE A NEW STAFF MEMBER IS ADDED TO SIMS)

1. HIRING RECOMMENDATION MEMO
2. TERMS OF EMPLOYMENT MEMO

AFTER THE STAFF MEMBER (AND THEIR SIGNATURE FILE) HAS BEEN ADDED TO SIMS, THESE MEMOS CAN THEN BE LINKED (BY THE UNIT AA) TO THE 
STAFF MEMBER'S PROFILE RECORD, ALLOWING THEM TO BE VIEWABLE AND ELECTRONICALLY SIGNABLE BY THE STAFF MEMBER

OTHER SEDL MEMORANDUMS - SUCH AS THE PROMOTION MEMORANDUM - ARE CREATED FROM THE EXISTING STAFF MEMBER'S PERSONNEL MEMO ADMIN SCREEN (BY THE UNIT PBA OR AA) 
*/



//$staff_ID = $_GET['id'];


##################################################
## START: GET MANAGER IDs FOR "FROM" DROPDOWN ##
##################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff_table','all');
$search -> SetDBPassword($webPW,$webUN);

$search -> AddDBParam('current_employee_status','SEDL Employee');
$search -> AddDBParam('on_mgmt_council','Yes');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);

################################################
## END: GET MANAGER IDs FOR "FROM" DROPDOWN ##
################################################

##################################################
## START: GET CURRENT USER DETAILS ##
##################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff_table','all');
$search2 -> SetDBPassword($webPW,$webUN);

$search2 -> AddDBParam('staff_ID',$_SESSION['staff_ID']);

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);

$fullname = $recordData2['c_full_name_first_last'][0];
$title = $recordData2['job_title'][0];
$unit = $recordData2['primary_SEDL_workgroup'][0];
//$pba_user_ID = $recordData2['bgt_auth_primary_sims_user_ID'][0];
//echo $unit;
################################################
## END: GET CURRENT USER DETAILS ##
################################################
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Personnel Memos</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Kameron:400,700' rel='stylesheet' type='text/css'>
<script language="JavaScript">

function UpdateSelect(){

	select_value = "";
	select_value = document.pa_form.memo_type.value;

	var id = 'memo_terms';
	var obj = '';
	obj = (document.getElementById) ? document.getElementById(id) : ((document.all) ? document.all[id] : ((document.layers) ? document.layers[id] : false));
	
	
	if(select_value == ""){
	  obj.style.display = 'none';
	  //alert("onBodyLoad.");
	
	} else if(select_value == "Terms of employment"){
	  // alert("You chose Journal article.");
	  // return false;
	  obj.style.display = 'block';
	}
	else
	{
	  obj.style.display = 'none';
	}


	var id2 = 'memo_hiring';
	var obj2 = '';
	obj2 = (document.getElementById) ? document.getElementById(id2) : ((document.all) ? document.all[id2] : ((document.layers) ? document.layers[id2] : false));
	
	
	if(select_value == ""){
	  obj2.style.display = 'none';
	  //alert("onBodyLoad.");
	
	} else if(select_value == "Hiring recommendation"){
	  // alert("You chose Journal article.");
	  // return false;
	  obj2.style.display = 'block';
	}
	else
	{
	  obj2.style.display = 'none';
	}

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

<script language="JavaScript">
<!--
function checkFields() { 

	select_value2 = "";
	select_value2 = document.pa_form.memo_type.value;

	if (select_value2 == "Hiring recommendation"){
	// Application ID
		if (document.pa_form.resume_ID.value == "") {
			alert("Please enter the application ID for this applicant from the SIMS Resumes and Applications database.");
			document.pa_form.resume_ID.focus();
			return false;	}
	}	

}
// -->
</script>

</head>

<BODY BGCOLOR="#FFFFFF" onLoad="UpdateSelect();">
<form method="post" name="pa_form" id="pa_form" onsubmit="return checkFields()">
<input type="hidden" name="action" value="show_memo">
<input type="hidden" name="mod" value="submit_new_memo">
<input type="hidden" name="staff_ID" value="<?php echo $staff_ID;?>">
<input type="hidden" name="memo_unit_abbrev" value="<?php echo $unit;?>">
<input type="hidden" name="pba_user_ID" value="<?php echo $recordData2['sims_user_ID'][0];?>">

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="vertical-align:text-top;padding:6px;border:0px"><img src="/images/print_sedl.jpg"></td>
<td align="right" style="vertical-align:text-top;padding:6px;border:0px">

	<h1>NEW MEMORANDUM</h1>

</td></tr>
</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border:0px solid #333333">

	<select name="memo_type" id="memo_type" onChange="UpdateSelect();">
	<option value="">Select the Memo Type</option>
	<option value="">--------------------------</option>
	<option value="Hiring recommendation">Hiring Recommendation Memo</option>
	<option value="Terms of employment">Terms of Employment Memo</option>
	</select>

</td></tr>
</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:0px">
<tr><td style="vertical-align:text-top;padding:0px;border:0px solid #333333">

<div id="memo_terms">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">TO:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><input type="text" name="memo_to" value="<<FULL NAME OF NEW STAFF MEMBER>>" size="50"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">FROM:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333">
	<div style="float:right;border:1px dotted #999999;padding:8px;background-color:#fff6bf">If you are preparing this memo for another<br>budget authority, select the BA<br>

		<select name="pba_user_ID">
		<option value="">--Select budget authority ID--</option>
		
		<?php foreach($searchResult['data'] as $key => $searchData) { ?>
		<option value="<?php echo $searchData['sims_user_ID'][0];?>"> <?php echo $searchData['sims_user_ID'][0];?></option>
		<?php } ?>

		</select>

	</div>

	<?php echo $fullname.'<br>'.$title.'<br>'.$unit;?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">DATE:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo date("F j, Y");?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333">Terms of Employment<input type="hidden" name="memo_subject" value="Terms of Employment"></td>
	</tr>

	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	<div class="alert_small" style="border:1px dotted #666666"><strong>Budget Authority</strong>: Edit the content of this memorandum below then click the "Submit" button.</div><br>
	<textarea name="memo_body" style="width:100%" rows="25">Welcome to SEDL and the <<UNIT NAME>> Unit. I and the other staff members of the <<UNIT NAME ABBREVIATION>> look forward to a productive working relationship with you.

I want to state in writing the terms of your employment. SEDL is an "at will" employer. This means that your employment with SEDL may be terminated at any time, with or without cause. In accordance with SEDL Administrative Policies/Procedures 10.03 A.5, you are employed initially on a probationary basis. The length of this probationary period will generally not exceed six months and is scheduled to terminate on or before <<END DATE OF PROBATIONARY PERIOD>>. Please note that you are eligible for all applicable personnel benefits beginning <<BEGIN DATE OF BENEFITS>>. The period of probationary employment may be terminated, or it may be extended prior to the scheduled end of the original probationary period. Such action would be the subject of a separate Personnel Action and Memorandum. As was explained to you, funding for this position is made available from various local, state, and national projects and is considered "soft money". Continued employment is dependent upon SEDL's needs, the availability of funds, and the staff member's satisfactory performance.

No statement, verbal or written, shall in any way alter or change the terms of employment as outlined in this memorandum. Please sign below to acknowledge receipt of this memorandum and return to Human Resources at the time of your orientation. You will receive a copy of this signed memorandum attached to your copy of your employment personnel action.

All of the above remarks are made only to clarify the terms of employment and at this time I have no reason to believe that they will serve any other purpose. Based on the quality of your application and interview, we enthusiastically welcome you as <<POSITION TITLE>> in the <<UNIT NAME>>.

</textarea>
	</td></tr>
	</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td><div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Save"></div></td></tr>

</table>
</form>
</div>

<div id="memo_hiring">
<input type="hidden" name="memo_to2" value="Wes Hoover, President and CEO">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">TO:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%">Wes Hoover<br>President and CEO<input type="hidden" name="memo_to2" value="Wes Hoover"><input type="hidden" name="memo_to_title2" value="President and CEO"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">FROM:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333">
	
	<div style="float:right;border:1px dotted #999999;padding:8px;background-color:#fff6bf">If you are preparing this memo for another<br>budget authority, select the BA<br>

		<select name="pba_user_ID">
		<option value="">--Select hiring manager ID--</option>
		
		<?php foreach($searchResult['data'] as $key => $searchData) { ?>
		<option value="<?php echo $searchData['sims_user_ID'][0];?>"> <?php echo $searchData['sims_user_ID'][0];?></option>
		<?php } ?>

		</select>

	</div>

	<?php echo $fullname.'<br>'.$title.'<br>'.$unit;?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">DATE:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo date("F j, Y");?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><input type="text" name="memo_subject2" value="Hiring Recommendation for the <<POSITION TITLE>> for SEDL's <<UNIT NAME>> Unit" size="100"></td>
	</tr>

	
	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">

	<div class="alert_small" style="border:1px dotted #666666"><strong>Budget Authority</strong>: Edit memo content below then click "Submit". | <a href="novs_stats.php?nov=" target="_blank">Generate NOV statistics</a> | <a href="http://www.sedl.org/staff/sims/documentation/hiring_recommendation_guidelines.pdf">SEDL Hiring Recommendation Guidelines</a></div><br>
	Application ID: <input type="text" size="6" name="resume_ID"> <span class="tiny">Enter the application ID for this applicant from the <a href="http://www.sedl.org/staff/sims/menu_positions_novs.php" target="_blank">SIMS Resumes and Applications database</a>.</span><br>
	<textarea name="memo_body2" style="width:100%" rows="25"><<APPLICANT FULL NAME>> applied to SEDL's notice of vacancy for the <<POSITION TITLE>> position under the <<UNIT NAME>>, which was opened on <<POSITION OPEN DATE>>. <<APPLICANT FIRST NAME>>'s cover letter, resume, and writing sample were uploaded to the SIMS Resume and Applications review database on <<DATE ENTERED>>, her SEDL online application was uploaded on <<APPLICATION UPLOAD DATE>>, and her interview was conducted on <<INTERVIEW DATE>>. The interview team consisted of <<INTERVIEW TEAM MEMBER NAMES>>. Everyone on the interview team considered <<APPLICANT FIRST NAME>> to be highly qualified for the position and unanimously agreed to recommend an offer of employment.

Interviews for the position are <<POSITION STATUS>>. To date, there were <<NUM RESUMES RECEIVED>> resumes received; <<NUM RESUMES REJECTED>> candidates (XX%) were rejected in screening, <<NUM FORWARDED TO MANAGERS>> (XX%) were sent forward for manager review and of this number, <<NUM APPLICATIONS REQUESTED>> (XX%) were asked to submit applications. <<APPLICANT FIRST NAME>> was one of <<NUM INTERVIEWS CONDUCTED>> applicants selected for interviews. To our knowledge <<NUM MINORITY APPLICANTS/INTERVIEWEES>> of the candidates we asked to submit an application or participate in an interview have a minority background (<<NUM APPLICANTS NOT PROVIDING MINORITY STATUS>> candidates did not provide this information).

<<ADD REMAINING DETAIL PARAGRAPHS DESCRIBING APPLICANT'S QUALIFICATIONS AND HIRING RECOMMENDATION DETAILS>></textarea>
	</td></tr>
	</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td><div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Save"></div></td></tr>

</table>

</form>

</div>

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

}else{ ?>


Error | <a href="menu_memos_ba_admin.php?action=show_all" title="Return to SIMS Personnel Memos screen.">Return to Personnel Memos Admin</a>

<?php } ?>





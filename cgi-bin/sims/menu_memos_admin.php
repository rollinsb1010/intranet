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

#######################################
## START: GRAB CURRENT STAFF RECORDS ##
#######################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff_table','all');
$search -> SetDBPassword($webPW,$webUN);

if($query == 'former_staff'){
$search -> AddDBParam('current_employee_status','Former Employee');
} else {
$search -> AddDBParam('current_employee_status','SEDL Employee');
}
$search -> AddDBParam('employee_type','Hourly','neq');

$search -> AddSortParam('c_full_name_last_first','ascend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);
#####################################
## END: GRAB CURRENT STAFF RECORDS ##
#####################################

##################################################################
## START: FIND ALL UNASSIGNED PERSONNEL MEMOS ##
##################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','personnel_memos','all');
$search2 -> SetDBPassword($webPW,$webUN);
//$search2 -> FMSkipRecords($skipsize);
$search2 -> AddDBParam('staff_ID','=='.'');
//$search2 -> AddDBParam('memo_unit_abbrev',$_SESSION['workgroup']);

$search2 -> AddSortParam('memo_ID','descend');


$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//print_r ($searchResult2);
//$_SESSION['timesheet_foundcount'] = $searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);
##################################################################
## END: FIND ALL UNASSIGNED PERSONNEL MEMOS ##
##################################################################

?>

<html>
<head>
<title>SIMS - Personnel Memos</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Personnel Memos</h1><hr /></td></tr>
			
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL <?php if($query == 'former_staff'){?>Former <?php }?>Staff</strong> | <?php echo $searchResult['foundCount'];?> records found. | <?php if($query == 'former_staff'){?><a href="menu_memos_admin.php?action=show_all">Show current staff</a><?php }else{?><a href="menu_memos_admin.php?action=show_all&query=former_staff">Show former staff</a><?php }?></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">Name</td><td class="body">Unit</td><td class="body">Title</td><td class="body">FTE</td><td class="body">Empl. Start Date</td><td class="body">Empl. Term. Date</td></tr>
							
							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							<tr><td class="body"><?php echo $searchData['staff_ID'][0];?></td><td class="body" nowrap><a href="menu_memos_admin.php?action=show_1&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo stripslashes($searchData['c_full_name_last_first'][0]);?></a></td><td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td><td class="body"><?php echo $searchData['job_title'][0];?></td><td class="body"><?php echo $searchData['FTE_status'][0];?></td><td class="body"><?php echo $searchData['empl_start_date'][0];?></td><td class="body" nowrap><?php if($searchData['empl_end_date'][0] == ''){echo 'Current';}else{echo $searchData['empl_end_date'][0];}?></td></tr>
							<?php } ?>


							
							</table><p>
			
			</td></tr>
			
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>Unassigned Personnel Memos</strong> | <?php echo $searchResult2['foundCount'];?> records found.</td><td align="right"><a href="menu_memos_admin.php?action=new_unassigned" title="Click here to create a memo for a newly hired staff member."></a>New personnel memo</td></tr>
			
			
			
			<tr><td class="body" colspan=2>



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">From</td><td class="body">To</td><td class="body">Memo Subject</td><td class="body" nowrap>Date Prepared</td><td class="body">Status</td></tr>
							
							<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
							<tr><td style="vertical-align:text-top"><?php echo $searchData2['memo_ID'][0];?></td><td style="vertical-align:text-top" nowrap><?php echo $searchData2['memo_from'][0];?></td><td style="vertical-align:text-top"><?php echo $searchData2['memo_to'][0];?></td><td><a href="menu_memos_admin.php?action=show_memo&record_ID=<?php echo $searchData2['memo_ID'][0];?>"><?php echo stripslashes($searchData2['memo_subject'][0]);?></a></td><td style="vertical-align:text-top"><?php echo $searchData2['memo_date'][0];?></td><td style="vertical-align:text-top"><?php echo $searchData2['memo_approval_status'][0];?></td></tr>
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
			<tr><td class="body" nowrap><b><?php echo $fullname;?> (<?php echo $unit;?>)</b></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="menu_memos_admin.php?action=new&id=<?php echo $recordData2['staff_ID'][0];?>" title="Create new memo for this staff member"></a>New personnel memo | <a href="menu_memos_admin.php?action=show_all" title="Return to workgroup Personnel Memos.">SEDL staff</a> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			

							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">From</td><td class="body">To</td><td class="body">Memo Subject</td><td class="body" nowrap>Date Prepared</td><td class="body">Status</td></tr>
							
						<?php if($searchResult['foundCount'] > 0) { ?>

							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							<tr><td style="vertical-align:text-top"><?php echo $searchData['memo_ID'][0];?></td><td style="vertical-align:text-top" nowrap><?php echo $searchData['memo_from'][0];?></td><td style="vertical-align:text-top"><?php echo $searchData['memo_to'][0];?></td><td><a href="menu_memos_admin.php?action=show_memo&record_ID=<?php echo $searchData['memo_ID'][0];?>"><?php echo stripslashes($searchData['memo_subject'][0]);?></a></td><td style="vertical-align:text-top"><?php echo $searchData['memo_date'][0];?></td><td style="vertical-align:text-top"><?php echo $searchData['memo_approval_status'][0];?></td></tr>
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

$record_ID = $_GET['record_ID'];
$hr = 1;

if($_REQUEST['mod'] == 'hr_release'){
##################################################
## START: ACTIVATE/RELEASE DOCUMENT ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_memos');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['eid']);
$update -> AddDBParam('doc_release_status','1');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: ACTIVATE/RELEASE DOCUMENT ##
##################################################

	if($updateData['c_second_signer'][0] == 'staff'){ // HR RELEASE TO STAFF MEMBER (ONLY APPLIES TO TERMS OF EMPLOYMENT AND REMOVAL OF PROBATIONARY EMPLOYMENT MEMOS)
	###################################################
	## START: SEND E-MAIL NOTIFICATION TO STAFF ##
	###################################################
	//$to = 'ewaters@sedl.org';
	$to = $updateData['c_second_signer_ID'][0].'@sedl.org';
	$subject = 'SIMS: PERSONNEL MEMO REQUIRES YOUR SIGNATURE';
	$message = 
	'Dear '.$updateData['c_second_signer_ID'][0].','."\n\n".
	
	'A new Personnel Memo has been received by SIMS that requires your signature.'."\n\n".
	
	'------------------------------------------------------------'."\n".
	' PERSONNEL MEMO DETAILS'."\n".
	'------------------------------------------------------------'."\n".
	'Submitted by: '.$updateData['memo_from'][0]."\n".
	'Submitted to: '.$updateData['memo_to'][0]."\n".
	'Memo Subject: '.$updateData['memo_subject'][0]."\n".
	'------------------------------------------------------------'."\n\n".
	
	'To review and sign this personnel memo, click here:'."\n".
	
	'http://www.sedl.org/staff/sims/personnel_memos.php?action=show_memo&record_ID='.$updateData['memo_ID'][0]."\n\n".
	
	'---------------------------------------------------------------------------------------------------------------------------------'."\n".
	'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
	'---------------------------------------------------------------------------------------------------------------------------------';
	
	$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
	
	mail($to, $subject, $message, $headers);
	#################################################
	## END: SEND E-MAIL NOTIFICATION TO STAFF ##
	#################################################
	}
	
	if($updateData['c_second_signer'][0] == 'cpo'){ // HR RELEASE TO CPO (ONLY APPLIES TO HIRING RECOMMENDATION AND PROMOTION MEMOS)
	###################################################
	## START: SEND E-MAIL NOTIFICATION TO CEO ##
	###################################################
	//$to = 'ewaters@sedl.org';
	$to = $updateData['c_second_signer_ID'][0].'@sedl.org';
	$subject = 'SIMS: PERSONNEL MEMO REQUIRES YOUR SIGNATURE';
	$message = 
	'CPO,'."\n\n".
	
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

	if($updateData['c_second_signer'][0] == 'ceo'){ // HR RELEASE TO CEO (ONLY APPLIES TO HIRING RECOMMENDATION AND PROMOTION MEMOS)
	###################################################
	## START: SEND E-MAIL NOTIFICATION TO CEO ##
	###################################################
	//$to = 'ewaters@sedl.org';
	$to = $updateData['c_second_signer_ID'][0].'@sedl.org';
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


if($_REQUEST['mod'] == 'assign_staff_ID'){ // HR ASSSIGNED THE STAFF ID TO THIS MEMO
##################################################
## START: SET STAFF ID ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_memos');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('staff_ID',$_GET['staff_ID']);

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: SET STAFF ID ##
##################################################

	if($updateResult['errorCode'] == '0'){
	
		// LOG THIS ACTION
		$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
		
		$newrecord = new FX($serverIP,$webCompanionPort);
		$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
		$newrecord -> SetDBPassword($webPW,$webUN);
		$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
		$newrecord -> AddDBParam('action','PERSONNEL_MEMO_HR_ASSIGN_STAFF_ID');
		$newrecord -> AddDBParam('table','PERSONNEL_MEMOS');
		$newrecord -> AddDBParam('object_ID',$updateData['record_ID'][0]);
		$newrecord -> AddDBParam('affected_row_ID',$_GET['id']);
		$newrecord -> AddDBParam('ip_address',$ip);
		$newrecordResult = $newrecord -> FMNew();
		//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
		//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
	
		if($updateData['memo_type'][0] == 'Terms of employment'){
		##########################################################
		## START: SEND E-MAIL NOTIFICATION TO NEW STAFF MEMBER ##
		##########################################################
		$to = 'ewaters@sedl.org';
		//$to = $updateData['signer_ID_hr'][0].'@sedl.org';
		$subject = 'SIMS: PERSONNEL MEMO REQUIRES YOUR SIGNATURE';
		$message = 
		'New Staff Member:'."\n\n".
		
		'A new Personnel Memo has been received by SIMS that requires your signature.'."\n\n".
		
		'------------------------------------------------------------'."\n".
		' PERSONNEL MEMO DETAILS'."\n".
		'------------------------------------------------------------'."\n".
		'Submitted by: '.$updateData['memo_from'][0]."\n".
		'Staff Member: '.$updateData['memo_to'][0]."\n".
		'Memo Type: '.$updateData['memo_type'][0]."\n".
		'------------------------------------------------------------'."\n\n".
		
		'To review and sign this personnel memo, click here:'."\n".
		
		'http://www.sedl.org/staff/sims/personnel_memos.php?action=show_memo&record_ID='.$updateData['memo_ID'][0]."\n\n".
		
		'---------------------------------------------------------------------------------------------------------------------------------'."\n".
		'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
		'---------------------------------------------------------------------------------------------------------------------------------';
		
		$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
		
		mail($to, $subject, $message, $headers);
		########################################################
		## END: SEND E-MAIL NOTIFICATION TO NEW STAFF MEMBER ##
		########################################################
		}
	
	}

}


##################################################
## START: FIND PERSONNEL MEMOS FOR THIS USER ##
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
## END: FIND PERSONNEL MEMOS FOR THIS USER ##
################################################

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

function confirmRelease() { 
	var answer = confirm ("Release this Personnel Memo to the second signer?")
	if (!answer) {
	return false;
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

<BODY BGCOLOR="#FFFFFF">

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="vertical-align:text-top;padding:6px;border:0px"><img src="/images/print_sedl.jpg"></td>
<td align="right" style="vertical-align:text-top;padding:6px;border:0px">

	<h1>MEMORANDUM</h1>
	<div style="border: 1px dotted #333333;padding:6px;float:right">Status: <strong><?php if($recordData['memo_approval_status'][0] == 'Approved'){echo '<span style="color:#008206">';}else{echo '<span style="color:#ff0202">';} echo $recordData['memo_approval_status'][0];?></span></strong></div>
</td></tr>

</table>


<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:0px">

<?php if($recordData['doc_release_status'][0] == '0'){?>
<tr><td style="padding-left:20px;padding-bottom:10px" colspan="2">
<div class="alert_small"><strong>HR</strong>: This document has not been released. <a href="menu_memos_admin.php?action=show_memo&record_ID=<?php echo $recordData['memo_ID'][0];?>&eid=<?php echo $recordData['c_row_ID'][0];?>&mod=hr_release" onclick="return confirmRelease()">Click here</a> to notify the second signer and make this document active.</div>
</td></tr>
<?php }?>

<?php if($newrecordcreated == '1'){?>
<tr><td colspan="2" style="padding-left:20px"><p class="alert_small">Memorandum successfully created | <a href="http://www.sedl.org/staff/sims/menu_memos_admin.php">Return to SEDL memos</a></p>
</td></tr>
<?php }elseif($newrecordcreated == '2'){?>
<tr><td colspan="2" style="padding-left:20px"><p class="alert_small">There was a problem creating the memorandum (Error: <?php echo $newrecorderror;?>) | <a href="http://www.sedl.org/staff/sims/menu_memos_admin.php">Return to SEDL memos</a></p>
</td></tr>
<?php }elseif($pba_signed == '1'){?>
<tr><td colspan="2" style="padding-left:20px"><p class="alert_small"><strong>SEDL Admin</strong>: <?php echo $_SESSION['user_ID'];?> | You have successfully approved and submitted this memo. | <a href="http://www.sedl.org/staff/sims/menu_memos_admin.php">Return to SEDL memos</a></p>
</td></tr>
<?php }else{?>
<tr><td colspan="2" style="padding-left:20px"><p class="alert_small"><strong>SEDL Admin</strong>: <?php echo $_SESSION['user_ID'];?> | This personnel memorandum is <?php echo $recordData['memo_approval_status'][0];?> | <a href="javascript:history.back();">Return to SEDL memos</a> | <a href="menu_memos_admin.php?action=show_memo_print&record_ID=<?php echo $recordData['memo_ID'][0];?>&hr=<?php echo $hr;?>" target="_blank">Print memo</a></p>
</td></tr>
<?php }?>

<tr><td style="vertical-align:text-top;padding:0px;border:0px solid #333333">

	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">TO:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['memo_to'][0];?><br><?php echo $recordData['memo_to_title'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">FROM:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_from'][0];?><br><?php echo $recordData['memo_from_title'][0];?><br><?php echo $recordData['memo_unit_abbrev'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">DATE:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_date'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_subject'][0];?></td>
	</tr>
	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	<p><?php echo $recordData['c_memo_body_html'][0];?></p>
	</td></tr>

	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333"><div style="padding:4px;background-color:#cccccc" class="tiny"><a name="docs"></a><strong>RELATED DOCUMENTS</strong></div><br>

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
		
		</table>

	</td></tr>
	
	</table>


</td></tr>

</table>

<?php if(($recordData['memo_type'][0] == 'Terms of employment')||($recordData['memo_type'][0] == 'Removal of probationary employment status')){?>
<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>1. MANAGER RECOMMENDING ACTION</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_pba'][0] !== '1'){?>Pending PBA signature (<?php echo $recordData['memo_signer_ID_pba'][0];?>)
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_pba'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_pba'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>UNIT BUDGET AUTHORITY</strong></span>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">

	<strong>2. STAFF MEMBER</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_staff'][0] !== '1'){?>Pending staff signature <?php if($recordData['memo_signer_ID_staff'][0] == ''){
	
#######################################################################
## START: GRAB CURRENT STAFF RECORDS TO GENERATE SIMS ID SELECT LIST ##
#######################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff_table','all');
$search2 -> SetDBPassword($webPW,$webUN);

$search2 -> AddDBParam('current_employee_status','SEDL Employee');

$search2 -> AddSortParam('sims_user_ID','ascend');

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);
#####################################################################
## END: GRAB CURRENT STAFF RECORDS TO GENERATE SIMS ID SELECT LIST ##
#####################################################################
?>
	
<form>
<input type="hidden" name="action" value="show_memo">
<input type="hidden" name="mod" value="assign_staff_ID">
<input type="hidden" name="record_ID" value="<?php echo $recordData['memo_ID'][0];?>">
<input type="hidden" name="id" value="<?php echo $recordData['c_row_ID'][0];?>">
<select name="staff_ID">
<option value="">HR: Select staff SIMS ID</option>
<option value="">--------------------</option>

<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
<option value="<?php echo $searchData2['staff_ID'][0];?>"><?php echo $searchData2['sims_user_ID'][0];?></option>
<?php } ?>
</select>
<input type="submit" name="submit" value="Submit">
</form>
	
	<?php }else{echo '('.$recordData['memo_signer_ID_staff'][0].')';}?>
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_staff'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_staff'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>STAFF MEMBER</strong></span>
	
</td></tr>


</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="color:#666666" align="right"><br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<span class="tiny">Document ID: <?php echo $recordData['memo_ID'][0];?></span></td></tr>
</table>
<?php } ?>



<?php if(($recordData['memo_type'][0] == 'Hiring recommendation')||($recordData['memo_type'][0] == 'Promotion')||($recordData['memo_type'][0] == 'Reassignment')){ // SIGNED BY PBA AND CEO ?>

	<?php if($recordData['memo_signer_ID_staff'][0] == ''){ // IF THIS MEMO HAS NOT BEEN ASSIGNED TO A STAFF RECORD 
		
	#######################################################################
	## START: GRAB CURRENT STAFF RECORDS TO GENERATE SIMS ID SELECT LIST ##
	#######################################################################
	$search2 = new FX($serverIP,$webCompanionPort);
	$search2 -> SetDBData('SIMS_2.fp7','staff_table','all');
	$search2 -> SetDBPassword($webPW,$webUN);
	
	$search2 -> AddDBParam('current_employee_status','SEDL Employee');
	
	$search2 -> AddSortParam('sims_user_ID','ascend');
	
	$searchResult2 = $search2 -> FMFind();
	
	//echo $searchResult2['errorCode'];
	//echo $searchResult2['foundCount'];
	$recordData2 = current($searchResult2['data']);
	#####################################################################
	## END: GRAB CURRENT STAFF RECORDS TO GENERATE SIMS ID SELECT LIST ##
	#####################################################################
	?>
<div style="padding:10px 10px 0px 10px;margin-left:20px;border:2px solid #fc5c5c;width:780px;background-color:#ffb4b4">
	HR: This memo has not been assigned to a staff member's SIMS profile:	
	<form>
	<input type="hidden" name="action" value="show_memo">
	<input type="hidden" name="mod" value="assign_staff_ID">
	<input type="hidden" name="record_ID" value="<?php echo $recordData['memo_ID'][0];?>">
	<input type="hidden" name="id" value="<?php echo $recordData['c_row_ID'][0];?>">
	<select name="staff_ID">
	<option value="">HR: Select staff SIMS ID</option>
	<option value="">--------------------</option>
	
	<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
	<option value="<?php echo $searchData2['staff_ID'][0];?>"><?php echo $searchData2['sims_user_ID'][0];?></option>
	<?php } ?>
	</select>
	<input type="submit" name="submit" value="Submit">
	</form>
</div>	
	<?php }?>


<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>1. MANAGER RECOMMENDING ACTION</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_pba'][0] !== '1'){?>	Signature pending: <?php echo $recordData['memo_signer_ID_pba'][0];?>
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_pba'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_pba'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>UNIT BUDGET AUTHORITY</strong></span>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">

	<strong>2. CEO APPROVAL</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_ceo'][0] !== '1'){?>	Signature pending: <?php echo $recordData['memo_signer_ID_ceo'][0];?> (<a href="http://www.sedl.org/staff/sims/menu_memos_ba_admin.php?action=show_memo&record_ID=<?php echo $recordData['memo_ID'][0];?>">Show approval link</a>) 
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_ceo'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_ceo'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>PRESIDENT AND CEO</strong></span>
	
</td></tr>


</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="color:#666666" align="right"><br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<span class="tiny">Document ID: <?php echo $recordData['memo_ID'][0];?></span></td></tr>
</table>
<?php } ?>


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

}elseif($action == 'show_memo_print'){ 

$record_ID = $_GET['record_ID'];
$hr = $_GET['hr'];

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

<BODY BGCOLOR="#FFFFFF">

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="vertical-align:text-top;padding:6px;border:0px"><img src="/images/print_sedl.jpg"></td>
<td align="right" style="vertical-align:text-top;padding:6px;border:0px">

	<h1>MEMORANDUM</h1>
</td></tr>
</table>


<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:0px">


<tr><td style="vertical-align:text-top;padding:0px;border:0px solid #333333"><div style="float:right"><span class="tiny">Document ID: <?php echo $recordData['memo_ID'][0];?></span></div>

<?php if($hr == '1'){?>

	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">TO:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['memo_to'][0];?><br><?php echo $recordData['memo_to_title'][0];?></td>
	<td rowspan="3" style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333" nowrap>
	
<?php if($recordData['memo_type'][0] == 'Hiring recommendation'){?>

	<div style="padding:6px;border:1px dotted #999999">
	<strong>CEO RECOMMENDATIONS</strong><br>
	Original Salary Offered: <?php echo $recordData['ceo_salary'][0];?><br>
	Salary Accepted: <?php echo $recordData['salary_negotiated'][0];?><br>
	Effective Date: <?php echo $recordData['ceo_salary_date_effective'][0];?><br>
	Title: <?php echo $recordData['ceo_position_title'][0];?><br>
	</div>

<?php } ?>

<?php if($recordData['memo_type'][0] == 'Promotion'){?>

	<div style="padding:6px;border:1px dotted #999999">
	<strong>CEO RECOMMENDATIONS</strong><br>
	Salary: <?php echo $recordData['ceo_salary'][0];?><br>
	Effective Date: <?php echo $recordData['ceo_salary_date_effective'][0];?><br>
	Title: <?php echo $recordData['ceo_position_title'][0];?><br>
	</div>

<?php } ?>

	</td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">FROM:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_from'][0];?><br><?php echo $recordData['memo_from_title'][0];?><br><?php echo $recordData['memo_unit_abbrev'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">DATE:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_date'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td colspan="2" style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_subject'][0];?></td>
	</tr>
	<tr><td colspan="3" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	<p><?php echo $recordData['c_memo_body_html'][0];?></p>
	</td></tr>
	</table>

<?php }else{ ?>

	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">TO:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['memo_to'][0];?><br><?php echo $recordData['memo_to_title'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">FROM:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_from'][0];?><br><?php echo $recordData['memo_from_title'][0];?><br><?php echo $recordData['memo_unit_abbrev'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">DATE:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_date'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $recordData['memo_subject'][0];?></td>
	</tr>
	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	<p><?php echo $recordData['c_memo_body_html'][0];?></p>
	</td></tr>
	</table>

<?php } ?>



</td></tr>
</table>

<?php if(($recordData['memo_type'][0] == 'Terms of employment')||($recordData['memo_type'][0] == 'Removal of probationary employment status')){?>
<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>1. MANAGER RECOMMENDING ACTION</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_pba'][0] !== '1'){?>Pending PBA signature (<?php echo $recordData['memo_signer_ID_pba'][0];?>)
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_pba'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_pba'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>UNIT BUDGET AUTHORITY</strong></span>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">

	<strong>2. STAFF MEMBER</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_staff'][0] !== '1'){?>Pending staff signature <?php if($recordData['memo_signer_ID_staff'][0] == ''){?>
	(NEW STAFF)
	<?php }else{echo '('.$recordData['memo_signer_ID_staff'][0].')';}?>
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_staff'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_staff'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>STAFF MEMBER</strong></span>
	
</td></tr>


</table>

<?php } ?>



<?php if(($recordData['memo_type'][0] == 'Hiring recommendation')||($recordData['memo_type'][0] == 'Promotion')||($recordData['memo_type'][0] == 'Reassignment')){ // SIGNED BY PBA AND CEO ?>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>1. MANAGER RECOMMENDING ACTION</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_pba'][0] !== '1'){?>	Signature pending: <?php echo $recordData['memo_signer_ID_pba'][0];?>
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_pba'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_pba'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>UNIT BUDGET AUTHORITY</strong></span>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">

	<strong>2. CEO APPROVAL</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_ceo'][0] !== '1'){?>	Signature pending: <?php echo $recordData['memo_signer_ID_ceo'][0];?> 
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_ceo'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_ceo'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>PRESIDENT AND CEO</strong></span>
	
</td></tr>


</table>

<?php } ?>


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
################################################
## END: GET SELECTED STAFF DETAILS ##
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


	var id2 = 'memo_termination';
	var obj2 = '';
	obj2 = (document.getElementById) ? document.getElementById(id2) : ((document.all) ? document.all[id2] : ((document.layers) ? document.layers[id2] : false));
	
	
	if(select_value == ""){
	  obj2.style.display = 'none';
	  //alert("onBodyLoad.");
	
	} else if(select_value == "Termination"){
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


</head>

<BODY BGCOLOR="#FFFFFF" onLoad="UpdateSelect();">
<form method="post" name="pa_form" id="pa_form">
<input type="hidden" name="action" value="show_memo">
<input type="hidden" name="mod" value="submit_new_memo">
<input type="hidden" name="staff_ID" value="<?php echo $staff_ID;?>">
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
	<option value="Termination">Termination Memo</option>
	</select>

</td></tr>
</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:0px">
<tr><td style="vertical-align:text-top;padding:0px;border:0px solid #333333">
<div id="memo_promotion">
<div class="alert_small" style="border:1px dotted #666666;margin-left:20px"><strong>Budget Authority</strong>: <?php echo $fullname;?> | Edit the content of this memorandum below then click the "Submit" button.</div>
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
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo date("F, j Y");?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%">Promotion of <?php echo $staffname;?><input type="hidden" name="memo_subject" value="Promotion of <?php echo $staffname;?>"></td>
	</tr>

	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">

	<textarea name="memo_body" style="width:100%" rows="25">I recommend <?php echo $staffname;?> be promoted to the position of <<ENTER NAME OF NEW POSITION>> for the <<ENTER NAME OF WORKGROUP/UNIT>> effective <<ENTER EFFECTIVE DATE OF PROMOTION>>. This will fall on pay grade <<ENTER NEW PAY GRADE>> of SEDL's salary schedule based on a review of similar positions at SEDL and descriptions of those positions in Watson Wyatt literature describing those positions.
	
<<ADD ADDITIONAL PARAGRAPH(S) JUSTIFYING THIS PROMOTION>></textarea>
	</td></tr>
	</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td><div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Submit"></div></td></tr>

</table>
</form>
</div>

<div id="memo_termination">
<div class="alert_small" style="border:1px dotted #666666;margin-left:20px"><strong>Budget Authority</strong>: <?php echo $fullname;?> | Edit the content of this memorandum below then click the "Submit" button.</div>
<input type="hidden" name="memo_to2" value="Wes Hoover, President and CEO">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">TO:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%">Wes Hoover<br>President and CEO<input type="hidden" name="memo_to2" value="Wes Hoover"><input type="hidden" name="memo_to_title2" value="President and CEO"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">FROM:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $fullname.'<br>'.$title.'<br>'.$unit;?><input type="hidden" name="memo_from2" value="<?php echo $fullname;?>"><input type="hidden" name="memo_from_title2" value="<?php echo $title;?>"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">DATE:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo date("F, j Y");?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%">Termination of <?php echo $staffname;?><input type="hidden" name="memo_subject2" value="Termination of <?php echo $staffname;?>"></td>
	</tr>

	
	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	
	<textarea name="memo_body2" style="width:100%" rows="25"><<ENTER PARAGRAPH(S) DETAILING GROUNDS FOR THIS TERMINATION>></textarea>
	</td></tr>
	</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td><div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Submit"></div></td></tr>

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


</head>

<BODY BGCOLOR="#FFFFFF" onLoad="UpdateSelect();">
<form method="post" name="pa_form" id="pa_form">
<input type="hidden" name="action" value="show_memo">
<input type="hidden" name="mod" value="submit_new_unassigned_memo">
<input type="hidden" name="staff_ID" value="<?php echo $staff_ID;?>">
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
	<td style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><input type="text" name="memo_to" value="<<ENTER NAME OF NEW STAFF MEMBER>>" size="50"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">FROM:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $fullname.'<br>'.$title.'<br>'.$unit;?><input type="hidden" name="memo_from" value="<?php echo $fullname;?>"><input type="hidden" name="memo_from_title" value="<?php echo $title;?>"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">DATE:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo date("F, j Y");?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333">Terms of Employment<input type="hidden" name="memo_subject" value="Terms of Employment"></td>
	</tr>

	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	<div class="alert_small" style="border:1px dotted #666666">Budget Authority: Edit the content of this memorandum below then click the "Submit" button.</div><br>
	<textarea name="memo_body" style="width:100%" rows="25">Welcome to SEDL and the <<ENTER NAME OF NEW UNIT>> Unit. I and the other staff members of the <<ENTER UNIT NAME ABBREVIATION>> look forward to a productive working relationship with you.

I want to state in writing the terms of your employment. SEDL is an "at will" employer. This means that your employment with SEDL may be terminated at any time, with or without cause. In accordance with SEDL Administrative Policies/Procedures 10.03 A.5, you are employed initially on a probationary basis. The length of this probationary period will generally not exceed six months and is scheduled to terminate on or before <<ENTER END DATE OF PROBATIONARY PERIOD>>. Please note that you are eligible for all applicable personnel benefits beginning <<ENTER BEGIN DATE OF BENEFITS>>. The period of probationary employment may be terminated, or it may be extended prior to the scheduled end of the original probationary period. Such action would be the subject of a separate Personnel Action and Memorandum. As was explained to you, funding for this position is made available from various local, state, and national projects and is considered "soft money". Continued employment is dependent upon SEDL's needs, the availability of funds, and the staff member's satisfactory performance.

No statement, verbal or written, shall in any way alter or change the terms of employment as outlined in this memorandum. Please sign below to acknowledge receipt of this memorandum and return to Human Resources at the time of your orientation. You will receive a copy of this signed memorandum attached to your copy of your employment personnel action.

All of the above remarks are made only to clarify the terms of employment and at this time I have no reason to believe that they will serve any other purpose. Based on the quality of your application and interview, we enthusiastically welcome you as <<ENTER NEW POSITION TITLE>> in the <<ENTER NAME OF UNIT>>.

</textarea>
	</td></tr>
	</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td><div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Submit"></div></td></tr>

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
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo $fullname.'<br>'.$title.'<br>'.$unit;?><input type="hidden" name="memo_from2" value="<?php echo $fullname;?>"><input type="hidden" name="memo_from_title2" value="<?php echo $title;?>"></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">DATE:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><?php echo date("F, j Y");?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 0px #333333; border-left:solid 1px #333333">SUBJECT:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333"><input type="text" name="memo_subject2" value="Hiring Recommendation for the <<ENTER POSITION TITLE>> for SEDL's <?php echo $unit;?> Unit" size="100"></td>
	</tr>

	
	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	<textarea name="memo_body2" style="width:100%" rows="25"><<ENTER CONTENT OF HIRING RECOMMENDATION MEMORANDUM>>
	</textarea>
	</td></tr>
	</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td><div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Submit"></div></td></tr>

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


Error | <a href="menu_memos_admin.php?action=show_all" title="Return to SIMS Personnel Actions screen.">Return to Personnel Memos Admin</a>

<?php } ?>





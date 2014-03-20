<?php
session_start();

include_once('sims_checksession.php');

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
$action = 'show_mine';
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

if($action == 'show_mine'){ 

##############################################################
## START: FIND ALL PERSONNEL MEMOS FOR THE SELECTED STAFF ##
##############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','personnel_memos','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$_SESSION['staff_ID']);
$search -> AddDBParam('memo_type','Hiring recommendation','neq');
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
			<tr><td class="body" nowrap><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body" nowrap>Memo Date</td>
						<td class="body" nowrap>From</td>
						<td class="body" nowrap>To</td>
						<td class="body" nowrap>Memo Subject</td>
						<td class="body" align="right">Status</td>
						
						</tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="body" style="vertical-align:text-top"><?php echo $searchData['memo_ID'][0];?></td>
						<td class="body" style="vertical-align:text-top"><a href="/staff/sims/personnel_memos.php?record_ID=<?php echo $searchData['memo_ID'][0];?>&action=show_memo" title="Click here to view this personnel memo." target="_blank" <?php if($searchData['doc_release_status'][0] == '0'){echo 'onclick="return preventView()"';}?>><?php echo $searchData['memo_date'][0];?></a></td>
						<td class="body" style="vertical-align:text-top"><?php echo $searchData['memo_from'][0];?></td>
						<td class="body" style="vertical-align:text-top"><?php echo $searchData['memo_to'][0];?></td>
						<td class="body" style="vertical-align:text-top"><?php echo $searchData['memo_subject'][0];?></td>
						<td class="body" align="right" style="vertical-align:text-top<?php if($searchData['memo_approval_status'][0] == 'Pending'){echo ';color:#ff0000';}else{echo ';color:#0000ff';}?>"><?php echo $searchData['memo_approval_status'][0];?></td>
						
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
if($_REQUEST['mod'] == 'st'){ // STAFF MEMBER SIGNED THE PERSONNEL MEMO
##################################################
## START: SIGN FORM AS STAFF MEMBER ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_memos');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['id']);
$update -> AddDBParam('memo_sign_status_staff','1');
$update -> AddDBParam('memo_approval_status','Approved');

$updateResult = $update -> FMEdit();

//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
##################################################
## END: SIGN FORM AS STAFF MEMBER ##
##################################################
$pbasigner = $updateData['memo_signer_ID_pba'][0].'@sedl.org';

if($updateResult['errorCode'] == '0'){
$staff_signed = '1';

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','SIGN_PERSONNEL_MEMO_STAFF');
	$newrecord -> AddDBParam('table','PERSONNEL_MEMOS');
	$newrecord -> AddDBParam('object_ID',$updateData['record_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$updateData['c_row_ID'][0]);
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
	$newrecord2 -> AddDBParam('action','SIGN_MEMO_STAFF');
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
$to = 'sliberty@sedl.org,mturner@sedl.org';
//$to = $updateData['signer_ID_hr'][0].'@sedl.org';
$subject = 'SIMS: PERSONNEL MEMO APPROVED';
$message = 
'HR:'."\n\n".

'A new Personnel Memo has been approved by SIMS.'."\n\n".

'------------------------------------------------------------'."\n".
' PERSONNEL MEMO DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Submitted by: '.$updateData['memo_from'][0]."\n".
'Submitted to: '.$updateData['memo_to'][0]."\n".
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

<script language="JavaScript">

function confirmSign() { 
	var answer = confirm ("Sign this Personnel Memo?")
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

<?php if($staff_signed == '1'){?>
<tr><td colspan="2" style="padding-left:20px"><p class="alert_small"><strong>Staff Member</strong>: <?php echo $_SESSION['user_ID'];?> | You have successfully approved this memo. | <a href="http://www.sedl.org/staff/sims/personnel_memos.php">Return to personnel memos</a> | <a href="menu_memos_admin.php?action=show_memo_print&record_ID=<?php echo $recordData['memo_ID'][0];?>" target="_blank">Print memo</a></p>
</td></tr>
<?php }else{?>
<tr><td colspan="2" style="padding-left:20px"><p class="alert_small"><strong>Staff Member</strong>: <?php echo $_SESSION['user_ID'];?> | This personnel memorandum is <?php echo $recordData['memo_approval_status'][0];?> | <a href="javascript:self.close();">Return to personnel memos</a> | <a href="menu_memos_admin.php?action=show_memo_print&record_ID=<?php echo $recordData['memo_ID'][0];?>" target="_blank">Print memo</a></p>
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
	</table>




</td></tr>
</table>

</form>

<?php if(($recordData['memo_type'][0] == 'Terms of employment')||($recordData['memo_type'][0] == 'Removal of probationary employment status')){ // SIGNED BY PBA AND NEW STAFF MEMBER ?>
<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>1. MANAGER RECOMMENDING ACTION</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_pba'][0] !== '1'){?>	Pending signature: <?php echo $recordData['memo_signer_ID_pba'][0];?>
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_pba'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_pba'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>UNIT BUDGET AUTHORITY</strong></span>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">

	<strong>2. STAFF MEMBER</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_staff'][0] !== '1'){?><a href="personnel_memos.php?action=show_memo&mod=st&record_ID=<?php echo $recordData['memo_ID'][0];?>&id=<?php echo $recordData['c_row_ID'][0];?>" onclick="return confirmSign()" title="Staff Member: Click here to approve and submit this memo to SIMS."><?php echo $recordData['memo_signer_ID_staff'][0];?></a>
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_staff'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_staff'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>STAFF MEMBER</strong></span>
	
</td></tr>


</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="color:#666666" align="right"><br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<span class="tiny">Document ID: <?php echo $recordData['memo_ID'][0];?></span></td></tr>
</table>
<?php } ?>



<?php if(($recordData['memo_type'][0] == 'Hiring recommendation')||($recordData['memo_type'][0] == 'Promotion')){ // SIGNED BY PBA AND CEO ?>
<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>1. MANAGER RECOMMENDING ACTION</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_pba'][0] !== '1'){?>	Pending signature: <?php echo $recordData['memo_signer_ID_pba'][0];?>
	<?php }else{ ?><img src="signatures/<?php echo $recordData['memo_signer_ID_pba'][0];?>.png"><br>
	<span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['memo_sign_timestamp_pba'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>UNIT BUDGET AUTHORITY</strong></span>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">

	<strong>2. CEO APPROVAL</strong><br><span style="margin-left:15px">
	<?php if($recordData['memo_sign_status_ceo'][0] !== '1'){?>	Pending signature: <?php echo $recordData['memo_signer_ID_ceo'][0];?> 
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


}else{ ?>


Error | <a href="personnel_actions_admin.php?action=show_mine" title="Return to my SIMS Personnel Actions screen.">Return to Personnel Actions Admin</a>

<?php } ?>





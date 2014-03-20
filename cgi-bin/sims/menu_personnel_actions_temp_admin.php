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

############################################
## START: GRAB CURRENT TEMP STAFF RECORDS ##
############################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff_table','all');
$search -> SetDBPassword($webPW,$webUN);

if($query == 'former_staff'){
$search -> AddDBParam('current_employee_status','Former Employee');
//$search -> AddDBParam('personnel_action_admin_sims_user_ID',$_SESSION['user_ID']);
$search -> AddDBParam('employee_type','Hourly');
} else {
$search -> AddDBParam('current_employee_status','SEDL Employee');
//$search -> AddDBParam('personnel_action_admin_sims_user_ID',$_SESSION['user_ID']);
$search -> AddDBParam('employee_type','Hourly');
}

$search -> AddSortParam('c_full_name_last_first','ascend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);
##########################################
## END: GRAB CURRENT TEMP STAFF RECORDS ##
##########################################


###################################
## START: DISPLAY ALL STAFF LIST ##
###################################
 
 ?>

<html>
<head>
<title>SIMS - Temporary Personnel Actions</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Temporary Personnel Actions</h1><hr /></td></tr>
			
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL <?php if($query == 'former_staff'){?>Former <?php }?>Staff (Temp)</strong> | <?php echo $searchResult['foundCount'];?> records found. | <?php if($query == 'former_staff'){?><a href="menu_personnel_actions_temp_admin.php?action=show_all">Show current temp staff</a><?php }else{?><a href="menu_personnel_actions_temp_admin.php?action=show_all&query=former_staff">Show former temp staff</a><?php }?></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILES-->


							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">Name</td><td class="body">Unit</td><td class="body">Title</td><td class="body">FTE</td><td class="body">Empl. Start Date</td><td class="body">Empl. Term. Date</td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body"><?php echo $searchData['staff_ID'][0];?></td><td class="body" nowrap><a href="menu_personnel_actions_temp_admin.php?action=show_1&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo stripslashes($searchData['c_full_name_last_first'][0]);?></a></td><td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td><td class="body"><?php echo $searchData['job_title'][0];?></td><td class="body"><?php echo $searchData['FTE_status'][0];?></td><td class="body"><?php echo $searchData['empl_start_date'][0];?></td><td class="body" nowrap><?php if($searchData['empl_end_date'][0] == ''){echo 'Current';}else{echo $searchData['empl_end_date'][0];}?></td></tr>
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
<title>SIMS: Temporary Personnel Actions</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Temporary Personnel Actions</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $fullname;?> (<?php echo $unit;?>)</b></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="menu_personnel_actions_temp_admin.php?action=show_all" title="Return to workgroup Personnel Actions.">SEDL staff</a> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body" nowrap>Effective Date</td>
						<td class="body" nowrap>Action Description</td>
						<td class="body" nowrap>Transfer From</td>
						<td class="body" nowrap>Assign To</td>
						<td class="body" align="right">Status</td>
						
						</tr>
						
						<?php if($searchResult['foundCount'] > 0) { ?>

							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							
							<tr>
							<td class="body" style="vertical-align:text-top"><?php echo $searchData['record_ID'][0];?></td>
							<td class="body" style="vertical-align:text-top"><a href="/staff/sims/menu_personnel_actions_temp_admin.php?record_ID=<?php echo $searchData['record_ID'][0];?>&action=show_action" title="Click here to view this personnel action." target="_blank"><?php echo $searchData['action_effective_date'][0];?></a></td>
							<td class="body" style="vertical-align:text-top"><?php echo $searchData['action_descr'][0];?></td>
							<td class="body" style="vertical-align:text-top">N/A</td>
							<td class="body" style="vertical-align:text-top"><?php echo $searchData['assign_to_title'][0];?><br>$<?php echo $searchData['assign_to_hourly_rate'][0];?>/hr.</td>
							<td class="body" align="right" style="vertical-align:text-top<?php if($searchData['c_approval_status'][0] == 'Pending'){echo ';color:#ff0000';}else{echo ';color:#0000ff';}?>"><?php echo $searchData['c_approval_status'][0];?></td>
							
							</tr>
				
							<?php } ?>

						<?php }else{  ?>

							<tr>
							<td class="body" colspan="6" style="vertical-align:text-top"><center>No temporary personnel actions found for this staff member.</center></td>
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


$record_ID = $_GET['record_ID'];

if($_REQUEST['mod'] == 'hr_release'){
##################################################
## START: ACTIVATE/RELEASE DOCUMENT ##
##################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','personnel_actions');
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

##########################################################
## START: SEND E-MAIL NOTIFICATION TO STAFF MEMBER ##
##########################################################
//$to = 'ewaters@sedl.org';
$to = $updateData['staff::sims_user_ID'][0].'@sedl.org';
$subject = 'SIMS: TEMPORARY PERSONNEL ACTION APPROVED';
$message = 
'Dear '.$updateData['staff::c_full_name_first_last'][0].','."\n\n".

'Your recent Temporary Personnel Action has been approved.'."\n\n".

'------------------------------------------------------------'."\n".
' PERSONNEL ACTION DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Submitted by: '.$_SESSION['user_ID']."\n".
'Staff Member: '.$updateData['staff::c_full_name_first_last'][0]."\n".
'Action: '.$updateData['action_descr'][0]."\n".
'------------------------------------------------------------'."\n\n".

'To review and print a copy of this temporary personnel action, click here:'."\n".

'http://www.sedl.org/staff/sims/personnel_actions.php?action=show_action&record_ID='.$updateData['record_ID'][0]."\n\n".

'---------------------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'---------------------------------------------------------------------------------------------------------------------------------';

$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org'."\r\n".'Cc: '.$updateData['created_by'][0].'@sedl.org,mturner@sedl.org';

mail($to, $subject, $message, $headers);
########################################################
## END: SEND E-MAIL NOTIFICATION TO STAFF MEMBER ##
########################################################
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
<title>SIMS: Temporary Personnel Actions</title>
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


function preventSignHR() { 
	alert ("This signature box is reserved for the HR Generalist. To sign this temporary personnel action, click the box with your ID.")
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


<tr><td width="50%" style="vertical-align:text-top;">

	<h1>TEMPORARY PERSONNEL ACTION</h1>

</td><td width="50%" style="vertical-align:text-top;padding:6px;">

	<div>
	<div style="padding:3px; text-align:right"><strong>DATE PREPARED</strong>: <?php echo $recordData['date_prepared'][0];?></div>
	</div>

</td></tr>

<?php if(($recordData['doc_release_status'][0] == '0')&&($recordData['sign_status_ceo'][0] == '1')){?>
<tr><td colspan="2" style="vertical-align:text-top;padding:6px;border:0px">
<span class="alert_small">HR: This document has not been released to the staff member. <a href="menu_personnel_actions_temp_admin.php?action=show_action&record_ID=<?php echo $recordData['record_ID'][0];?>&eid=<?php echo $recordData['c_row_ID'][0];?>&mod=hr_release" <?php if($recordData['signer_ID_hr'][0] == $_SESSION['user_ID']){echo 'onclick="return confirmRelease()"';}else{echo 'onclick="return preventSignHR()"';}?>>Click here</a> to notify staff member and make this document active.</span>
</td></tr>
<?php }?>


<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>NAME OF STAFF MEMBER:</strong><br>
	<?php echo $recordData['staff_name'][0];?>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">
	<strong>ADDRESS:</strong><br>
	<?php echo $recordData['staff_address'][0];?><br>
	<?php echo $recordData['staff_city'][0];?>, <?php echo $recordData['state'][0];?> <?php echo $recordData['staff_zip'][0];?>

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
	<?php echo $recordData['assign_to_title'][0];?>
	
</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>HOURLY RATE:</strong><br>
	$<?php echo $recordData['assign_to_hourly_rate'][0];?>/hr.

</td></tr>


</table>



	
<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr>
	<th colspan="3"><h2 style="border-top:2px dotted #999999; text-align:left; padding-top:4px;">ELIGIBLE BENEFIT(S) SELECTED:</h2></th></tr>
	<tr><td style="padding:6px"><?php if($recordData['benefits_ss'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Social Security</td>
	<td style="padding:6px"><?php if($recordData['benefits_bus_van_parking'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Bus/Van/Pool/Parking</td>
	<td style="padding:6px"><?php if($recordData['benefits_add_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Accidental Death & Dismemberment Insurance</td>
	</tr>
</table>






<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

	<tr><th><h2 style="border-top:2px dotted #999999; text-align:left; padding-top:4px;">REMARKS:</h2></th></tr>

	<tr><td><?php echo $recordData['c_action_remarks_html'][0];?></td></tr>

</table>




<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>1. SEDL UNIT RECOMMENDING ACTION</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_pba'][0] !== '1'){?><?php echo 'Pending: '.$recordData['signer_ID_pba'][0];?><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_pba'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_pba'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>UNIT BUDGET AUTHORITY</strong></span></td>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">
	<strong>2. HUMAN RESOURCES REVIEW</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_hr'][0] !== '1'){?><a href="menu_personnel_actions_wg_temp.php?action=show_action&mod=hr&record_ID=<?php echo $recordData['record_ID'][0];?>&id=<?php echo $recordData['c_row_ID'][0];?>"  <?php if($recordData['signer_ID_hr'][0] == $_SESSION['user_ID']){echo 'onclick="return confirmSign()"';}else{echo 'onclick="return preventSignHR()"';}?>><?php echo $recordData['signer_ID_hr'][0];?></a><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_hr'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_hr'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>HUMAN RESOURCES GENERALIST</strong></span></td></tr>


<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>3. FISCAL REVIEW</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_cfo'][0] !== '1'){?><?php echo 'Pending: '.$recordData['signer_ID_cfo'][0];?><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_cfo'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_cfo'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>CHIEF FINANCIAL OFFICER</strong></span></td></td>
	
	<td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>4. APPROVAL</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_ceo'][0] !== '1'){?><?php echo 'Pending: '.$recordData['signer_ID_ceo'][0];?><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_ceo'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_ceo'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>PRESIDENT & CEO</strong></span></td></tr>
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
<title>SIMS: Temporary Personnel Actions</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Kameron:400,700' rel='stylesheet' type='text/css'>
<script language="JavaScript">

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
<input type="hidden" name="action" value="show_action">
<input type="hidden" name="mod" value="submit_new">
<input type="hidden" name="staff_ID" value="<?php echo $staff_ID;?>">
<input type="hidden" name="transfer_from_title" value="<?php if($searchResult['foundCount'] == 0){echo $recordData2['job_title'][0];}else{echo $recordData['assign_to_title'][0];}?>">
<input type="hidden" name="transfer_from_paygrade" value="<?php if($searchResult['foundCount'] == 0){echo $recordData2['pay_grade'][0];}else{echo $recordData['assign_to_paygrade'][0];}?>">
<input type="hidden" name="transfer_from_actual_monthly_rate" value="<?php echo $recordData2['c_cur_payrate'][0];?>">

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td colspan="2" align="right" style="vertical-align:text-top;padding:6px;border:0px"><img src="/images/print_sedl.jpg" width="20%"></td></tr>


<tr><td width="50%" style="vertical-align:text-top;">

	<h1>NEW TEMPORARY PERSONNEL ACTION</h1>

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
	[No address data entered in SIMS] 
	<?php }else{ ?>
		<?php echo $recordData2['staff_hm_address'][0];?> <?php echo $recordData2['staff_hm_address2'][0];?><br>
		<?php echo $recordData2['staff_hm_city'][0];?>, <?php echo $recordData2['staff_hm_state'][0];?> <?php echo $recordData2['staff_hm_zip'][0];?>
	<?php } ?>
</td></tr>






<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>NATURE OF ACTION:</strong><br>

		<select name="action_descr" onChange="UpdateSelect();">
		<option value="">Select the type of action</option>
		<option value="">-------------------------</option>
		
		<?php foreach($v1Result['valueLists']['personnel_action_types'] as $key => $value) { ?>
		<option value="<?php echo $value;?>" <?php if($value == 'Salary Change - Annual Performance Review'){echo 'selected';}?>> <?php echo $value; ?></option>
		<?php } ?>
		<option value="">-------------------------</option>
		<option value="other"> Action not listed? Enter other...</option>
		</select>
		
		<div id="other_action" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
					
		<div style="padding:4px;border:1px dotted #000000;background-color:#ffffff">Other Action: <input type="text" name="action_descr_other" size="50"></div>

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






<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>ASSIGN TO:</strong><br>

		Title: <select name="assign_to_title">
		<option value="">Select the new position title</option>
		<option value="">-------------------------</option>
		
		<?php foreach($v1Result['valueLists']['job_titles'] as $key => $value) { ?>
		<option value="<?php echo $value;?>" <?php if($recordData2['job_title'][0] == $value){echo 'selected';}?>> <?php echo $value; ?></option>
		<?php } ?>
		</select>
	
</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>HOURLY RATE:</strong><br>
	$<input type="text" name="assign_to_hourly_rate" size="15">/hr.<br>(enter numerals only - no commas or currency symbols)

</td></tr>

</table>



	
<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr>
	<th colspan="3"><h2 style="border-top:2px dotted #999999; text-align:left; padding-top:4px;">ELIGIBLE BENEFIT(S) SELECTED:</h2></th></tr>
	<tr><td style="padding:6px"><input type="checkbox" name="benefits_ss" value="Yes" <?php if($recordData['benefits_ss'][0] == 'Yes'){?>checked<?php }?>> Social Security</input></td>
	<td style="padding:6px"><input type="checkbox" name="benefits_bus_van_parking" value="Yes" <?php if($recordData['benefits_bus_van_parking'][0] == 'Yes'){?>checked<?php }?>> Bus/Van Pool/Parking</input></td>
	<td style="padding:6px"><input type="checkbox" name="benefits_add_ins" value="Yes" <?php if($recordData['benefits_add_ins'][0] == 'Yes'){?>checked<?php }?>> Accidental Death & Dismemberment Insurance</input></td>
	</tr>
</table>






<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

	<tr><th><h2 style="border-top:2px dotted #999999; text-align:left; padding-top:4px;">REMARKS:</h2></th></tr>

	<tr><td><textarea name="action_remarks" rows="10" cols="100"><?php if(($recordData['action_remarks'][0] !== '')&&($searchResult['foundCount'] > 0)){ echo $recordData['action_remarks'][0];}else{ ?>
1. Reference: Administrative Policy/Procedure 10.06 B.1.

2. See attached copy of approved justification memorandum.

<?php }?></textarea><br><span class="tiny">* Default text has been pre-entered.</span><br>&nbsp;<br>&nbsp;<br>
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


Error | <a href="menu_personnel_actions_temp_admin.php?action=show_all" title="Return to Temporary Personnel Actions screen.">Return to Temporary Personnel Actions Admin</a>

<?php } ?>





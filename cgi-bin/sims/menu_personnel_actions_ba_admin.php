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
$search -> AddDBParam('c_personnel_actions_access_list',$_SESSION['user_ID']);
$search -> AddDBParam('employee_type','Hourly','neq');
} else {
$search -> AddDBParam('current_employee_status','SEDL Employee');
$search -> AddDBParam('c_personnel_actions_access_list',$_SESSION['user_ID']);
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


###################################
## START: DISPLAY ALL STAFF LIST ##
###################################
 
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Workgroup Personnel Actions</h1><hr /></td></tr>
			
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>Workgroup <?php if($query == 'former_staff'){?>Former <?php }?>Staff</strong> | <?php echo $searchResult['foundCount'];?> records found. | <?php if($query == 'former_staff'){?><a href="menu_personnel_actions_ba_admin.php?action=show_all">Show current staff</a><?php }else{?><a href="menu_personnel_actions_ba_admin.php?action=show_all&query=former_staff">Show former staff</a><?php }?></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILES-->


							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">Name</td><td class="body">Unit</td><td class="body">Title</td><td class="body">FTE</td><td class="body">Empl. Start Date</td><td class="body">Empl. Term. Date</td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body"><?php echo $searchData['staff_ID'][0];?></td><td class="body" nowrap><a href="menu_personnel_actions_ba_admin.php?action=show_1&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo stripslashes($searchData['c_full_name_last_first'][0]);?></a></td><td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td><td class="body"><?php echo $searchData['job_title'][0];?></td><td class="body"><?php echo $searchData['FTE_status'][0];?></td><td class="body"><?php echo $searchData['empl_start_date'][0];?></td><td class="body" nowrap><?php if($searchData['empl_end_date'][0] == ''){echo 'Current';}else{echo $searchData['empl_end_date'][0];}?></td></tr>
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
			<tr><td class="body" nowrap><b><?php echo $fullname;?> (<?php echo $unit;?>)</b></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="menu_memos_ba_admin.php?action=new&id=<?php echo $recordData2['staff_ID'][0];?>" title="Create new memo for this staff member"></a>New personnel memo | <a href="menu_personnel_actions_ba_admin.php?action=show_all" title="Return to workgroup Personnel Actions.">Workgroup staff</a> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
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
							<td class="body" style="vertical-align:text-top"><a href="/staff/sims/menu_personnel_actions_ba_admin.php?record_ID=<?php echo $searchData['record_ID'][0];?>&action=show_action" title="Click here to view this personnel action." target="_blank" <?php if(($searchData['doc_release_status'][0] == '0')&&($_SESSION['user_ID'] == $searchData['staff::sims_user_ID'][0])){echo 'onClick="return preventView()"'; }?>><?php echo $searchData['action_effective_date'][0];?></a></td>
							<td class="body" style="vertical-align:text-top"><?php echo $searchData['action_descr'][0];?></td>
							<td class="body" style="vertical-align:text-top"><?php if($searchData['action_descr'][0] == 'Probationary Employment'){echo 'N/A';}else{ echo $searchData['transfer_from_title'][0];?><br>Pay grade: <?php echo $searchData['transfer_from_paygrade'][0];?><br>$<?php echo $searchData['transfer_from_actual_monthly_rate'][0];?>/mo.<?php } ?></td>
							<td class="body" style="vertical-align:text-top"><?php if(strpos($searchData['action_descr'][0],"Termination") !== false){echo 'N/A';}else{echo $searchData['assign_to_title'][0];?><br>Pay grade: <?php echo $searchData['assign_to_paygrade'][0];?><br>$<?php echo $searchData['assign_to_actual_monthly_rate'][0];?>/mo.<?php }?></td>
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

if($_REQUEST['memo_type'] == 'Terms of employment'){
$memo_subject = 'Terms of employment';
}elseif($_REQUEST['memo_type'] == 'Justification of promotion'){
$memo_subject = 'Promotion of '.$_REQUEST['memo_to'];
}
##################################################
## START: CREATE NEW PERSONNEL MEMO ##
##################################################
$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('SIMS_2.fp7','personnel_memos'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information

$newrecord -> AddDBParam('staff_ID',$_REQUEST['staff_ID']);
$newrecord -> AddDBParam('created_by',$_SESSION['user_ID']);
$newrecord -> AddDBParam('memo_type',$_REQUEST['memo_type']);
$newrecord -> AddDBParam('memo_to',$_REQUEST['memo_to']);
$newrecord -> AddDBParam('memo_from',$_REQUEST['memo_from']);
//$newrecord -> AddDBParam('memo_date',$_REQUEST['memo_date']);
$newrecord -> AddDBParam('memo_subject',$memo_subject);
$newrecord -> AddDBParam('memo_body',$_REQUEST['memo_body']);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$newrecordData = current($newrecordResult['data']);
##################################################
## END: CREATE NEW PERSONNEL MEMO ##
##################################################
$record_ID = $newrecordData['record_ID'][0];

if($newrecordResult['errorCode'] == 0){
$newrecordcreated = '1';

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
<tr><td colspan="2" style="vertical-align:text-top;padding:6px;border:0px"><div class="alert_small">New Personnel Action Memo successfully created. | <a href="menu_personnel_actions_ba_admin.php?action=show_1&staff_ID=<?php echo $recordData['staff_ID'][0];?>">Close document</a></div></td></tr>
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

<?php if(strpos($recordData['action_descr'][0],"Termination") !== false){ ?>

N/A

<?php }elseif($recordData['assign_to_title2'][0] == ''){ ?>

	<?php echo $recordData['assign_to_title'][0];?><br>
	Pay Grade: <?php echo $recordData['assign_to_paygrade'][0].' ('.$recordData['percent_time_employed_assign_to_position1'][0].'%)';?>

<?php }else{ ?>

	<?php echo $recordData['assign_to_title'][0].' - Pay Grade '.$recordData['assign_to_paygrade'][0].' ('.$recordData['percent_time_employed_assign_to_position1'][0].'%)';?><br>
	<?php echo $recordData['assign_to_title2'][0].' - Pay Grade '.$recordData['assign_to_paygrade2'][0].' ('.$recordData['percent_time_employed_assign_to_position2'][0].'%)';?>

<?php } ?>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>ACTUAL MONTHLY RATE:</strong><br>

<?php if(strpos($recordData['action_descr'][0],"Termination") !== false){ ?>

N/A

<?php }elseif($recordData['assign_to_title2'][0] == ''){ ?>

	$<?php echo number_format($recordData['assign_to_actual_monthly_rate'][0],2,'.',',');?>/mo.

<?php }else{ ?>

	<?php echo '(Position 1: $'.number_format($recordData['assign_to_actual_monthly_rate'][0],2,'.',',').'/mo.)';?><br>
	<?php echo '(Position 2: $'.number_format($recordData['assign_to_actual_monthly_rate2'][0],2,'.',',').'/mo.)';?><br>
	<?php echo '(Combined: $'.number_format($recordData['assign_to_actual_monthly_rate'][0]+$recordData['assign_to_actual_monthly_rate2'][0],2,'.',',').'/mo.)';?>

<?php } ?>

</td></tr>






<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>TRANSFER FROM:</strong><br>
	<?php if(($recordData['action_descr'][0] == 'Probationary Employment')&&($recordData['transfer_from_title'][0] == '')){echo 'N/A';}else{?>
	
		<?php if($recordData['transfer_from_title2'][0] == ''){
	
		echo $recordData['transfer_from_title'][0].'<br>';
		echo 'Pay Grade: '.$recordData['transfer_from_paygrade'][0].' ('.$recordData['percent_time_employed_transfer_from_position1'][0].'%)';
	
		}else{
		
		echo $recordData['transfer_from_title'][0].' - Pay Grade '.$recordData['transfer_from_paygrade'][0].' ('.$recordData['percent_time_employed_transfer_from_position1'][0].'%)<br>';
		echo $recordData['transfer_from_title2'][0].' - Pay Grade '.$recordData['transfer_from_paygrade2'][0].' ('.$recordData['percent_time_employed_transfer_from_position2'][0].'%)';
		
		}
	
	}?>
	
</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>ACTUAL MONTHLY RATE:</strong><br>

	<?php if(($recordData['action_descr'][0] == 'Probationary Employment')&&($recordData['transfer_from_actual_monthly_rate'][0] == '')){echo 'N/A';}else{
		
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

	<tr><td><?php echo $recordData['c_action_remarks_html'][0];?>
	
	<?php if($recordData['related_memo_ID'][0] !== ''){?><br>---<br>
	Related Memo ID: <a href="menu_memos_admin.php?action=show_memo_print&record_ID=<?php echo $recordData['related_memo_ID'][0];?>" target="_blank"><?php echo $recordData['related_memo_ID'][0];?></a>
	<?php } ?>
	
	</td></tr>

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

}elseif($action == 'show_memo'){ 


if($_REQUEST['mod'] == 'submit_new_memo'){

if($_REQUEST['memo_type'] == 'Terms of employment'){
$memo_subject = 'Terms of employment';
}elseif($_REQUEST['memo_type'] == 'Justification of promotion'){
$memo_subject = 'Promotion of '.$_REQUEST['memo_to'];
}
##################################################
## START: CREATE NEW PERSONNEL MEMO ##
##################################################
$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('SIMS_2.fp7','personnel_memos'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information

$newrecord -> AddDBParam('staff_ID',$_REQUEST['staff_ID']);
$newrecord -> AddDBParam('created_by',$_SESSION['user_ID']);
$newrecord -> AddDBParam('memo_type',$_REQUEST['memo_type']);
$newrecord -> AddDBParam('memo_to',$_REQUEST['memo_to']);
$newrecord -> AddDBParam('memo_from',$_REQUEST['memo_from']);
//$newrecord -> AddDBParam('memo_date',$_REQUEST['memo_date']);
$newrecord -> AddDBParam('memo_subject',$memo_subject);
$newrecord -> AddDBParam('memo_body',$_REQUEST['memo_body']);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$newrecordData = current($newrecordResult['data']);
##################################################
## END: CREATE NEW PERSONNEL MEMO ##
##################################################
$record_ID = $newrecordData['memo_ID'][0];

if($newrecordResult['errorCode'] == 0){
$newrecordcreated = '1';

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

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Personnel Actions & Memos</title>
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

<BODY BGCOLOR="#FFFFFF">

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="vertical-align:text-top;padding:6px;border:0px"><img src="/images/print_sedl.jpg"></td>
<td align="right" style="vertical-align:text-top;padding:6px;border:0px">

	<h1>MEMORANDUM</h1>

</td></tr>
</table>


<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:0px">
<tr><td style="vertical-align:text-top;padding:0px;border:0px solid #333333">

	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr><td style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	
		<p>TO:</p>
		<p>FROM:</p>
		<p>DATE:</p>
		<p>SUBJECT:</p>
	
	</td><td style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-right:solid 1px #333333;width:100%">
	
		<p><?php echo $recordData['memo_to'][0];?></p>
		<p><?php echo $recordData['memo_from'][0];?></p>
		<p><?php echo $recordData['memo_date'][0];?></p>
		<p><?php echo $recordData['memo_subject'][0];?></p>
	
	</td></tr>
	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	<?php echo $recordData['memo_body'][0];?>
	</td></tr>
	</table>




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
}elseif($action == 'new'){ // CREATE NEW MEMORANDUM (BUDGET AUTHORITY)



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

/*
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
*/
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
<title>SIMS: Personnel Actions & Memos</title>
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


	var id2 = 'memo_promotion';
	var obj2 = '';
	obj2 = (document.getElementById) ? document.getElementById(id2) : ((document.all) ? document.all[id2] : ((document.layers) ? document.layers[id2] : false));
	
	
	if(select_value == ""){
	  obj2.style.display = 'none';
	  //alert("onBodyLoad.");
	
	} else if(select_value == "Justification of promotion"){
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
<input type="hidden" name="memo_to" value="<?php echo $recordData2['c_full_name_first_last'][0];?>">
<input type="hidden" name="memo_from" value="<?php echo $recordData2['staff_SJ_by_pba::c_full_name_first_last'][0];?>">

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
	<option value="Terms of employment">Terms of Employment</option>
	<option value="Justification of promotion">Promotion of <?php echo $recordData2['c_full_name_first_last'][0];?></option>
	</select>

</td></tr>
</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:0px">
<tr><td style="vertical-align:text-top;padding:0px;border:0px solid #333333">

<div id="memo_terms">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr><td style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	
		<p>TO:</p>
		<p>FROM:</p>
		<p>DATE:</p>
		<p>SUBJECT:</p>
	
	</td><td style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-right:solid 1px #333333;width:100%">
	
		<p><?php echo $recordData2['c_full_name_first_last'][0];?></p>
		<p><?php echo $recordData2['staff_SJ_by_pba::c_full_name_first_last'][0];?></p>
		<p><?php echo date("F, j Y");?></p>
		<p>Terms of Employment</p>
	
	</td></tr>
	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	<div class="alert_small" style="border:1px dotted #666666">Budget Authority: Edit the content of this memorandum below then click the "Submit" button.</div><br>
	<textarea name="memo_body" style="width:100%" rows="25">Welcome to SEDL and the << ENTER NAME OF NEW UNIT >>. I and the other staff members of the << ENTER UNIT NAME ABBREVIATION >> look forward to a productive working relationship with you.

I want to state in writing the terms of your employment. SEDL is an "at will" employer. This means that your employment with SEDL may be terminated at any time, with or without cause. In accordance with SEDL Administrative Policies/Procedures 10.03 A.5, you are employed initially on a probationary basis. The length of this probationary period will generally not exceed six months and is scheduled to terminate on or before << ENTER END DATE OF PROBATIONARY PERIOD >>. Please note that you are eligible for all applicable personnel benefits beginning << ENTER BEGIN DATE OF BENEFITS >>. The period of probationary employment may be terminated, or it may be extended prior to the scheduled end of the original probationary period. Such action would be the subject of a separate Personnel Action and Memorandum. As was explained to you, funding for this position is made available from various local, state, and national projects and is considered "soft money". Continued employment is dependent upon SEDL's needs, the availability of funds, and the staff member's satisfactory performance.

No statement, verbal or written, shall in any way alter or change the terms of employment as outlined in this memorandum. Please sign below to acknowledge receipt of this memorandum and return to Human Resources at the time of your orientation. You will receive a copy of this signed memorandum attached to your copy of your employment personnel action.

All of the above remarks are made only to clarify the terms of employment and at this time I have no reason to believe that they will serve any other purpose. Based on the quality of your application and interview, we enthusiastically welcome you as << ENTER NEW POSITION TITLE >> in the << ENTER NAME OF UNIT >>.

</textarea>
	</td></tr>
	</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td><div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Submit"></div></td></tr>

</table>

</div>

<div id="memo_promotion">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr><td style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	
		<p>TO:</p>
		<p>FROM:</p>
		<p>DATE:</p>
		<p>SUBJECT:</p>
	
	</td><td style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333;width:100%">
	
		<p>Wes Hoover, President and CEO</p>
		<p><?php echo $recordData2['staff_SJ_by_pba::c_full_name_first_last'][0];?></p>
		<p><?php echo date("F, j Y");?></p>
		<p>Promotion of <?php echo $recordData2['c_full_name_first_last'][0];?></p>
	
	</td></tr>
	<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border-top:8px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333">
	<textarea name="memo_body" style="width:100%" rows="25">I recommend <?php echo $recordData2['c_full_name_first_last'][0];?> be promoted to the position of << ENTER NAME OF NEW POSITION >> for the << ENTER NAME OF WORKGROUP/UNIT >> effective << ENTER EFFECTIVE DATE OF PROMOTION >>. This will fall on pay grade << ENTER NEW PAY GRADE >> of SEDL's salary schedule based on a review of similar positions at SEDL and descriptions of those positions in Watson Wyatt literature describing those positions.

<< ENTER CONTENT OF MEMORANDUM >></textarea>
	</td></tr>
	</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td><div style="float:right"><input type=button value="Cancel" onClick="history.back()"> <input type="submit" name="submit" value="Submit"></div></td></tr>

</table>


</div>

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


Error | <a href="menu_personnel_actions_ba_admin.php?action=show_all" title="Return to SIMS Personnel Actions screen.">Return to Personnel Actions Admin</a>

<?php } ?>





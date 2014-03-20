<?php
session_start();

include_once('sims_checksession.php');

//if($_SESSION['paystub_admin_access'] !== 'Yes'){

//header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
//exit;
//}

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

$debug = 'off';
$paystub_access = 'yes';
$today = date("m/d/Y");

$action = $_GET['action'];
$query = $_GET['query'];
//$today = '10/15/2008';
//echo '<p>$today: '.$today;
//echo '<p>$_SESSION[staff_ID]: '.$_SESSION['staff_ID'];
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
$search -> AddDBParam('is_budget_authority','Yes');
} else {
$search -> AddDBParam('current_employee_status','SEDL Employee');
$search -> AddDBParam('is_budget_authority','Yes');
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
<title>SIMS - Electronic Signature Verification</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Electronic Signature Verification Admin</h1><hr /></td></tr>
			
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL <?php if($query == 'former_staff'){?>Former <?php }?>Budget Authorities</strong> | <?php echo $searchResult['foundCount'];?> records found. | <?php if($query == 'former_staff'){?><a href="menu_sig_verification.php?action=show_all">Show current BAs</a><?php }else{?><a href="menu_sig_verification.php?action=show_all&query=former_staff">Show former BAs</a><?php }?></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILES-->


							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">Name</td><td class="body">Unit</td><td class="body">Title</td><td class="body">Is a Supervisor</td><td class="body">Is a Budget Authority</td><td class="body" style="text-align:right">Last Form Received</td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body"><?php echo $searchData['staff_ID'][0];?></td><td class="body" nowrap><a href="menu_sig_verification.php?action=show_1&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo stripslashes($searchData['c_full_name_last_first'][0]);?></a></td><td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td><td class="body"><?php echo $searchData['job_title'][0];?></td><td class="body" nowrap><?php echo $searchData['is_supervisor'][0];?></td><td class="body" nowrap><?php echo $searchData['is_budget_authority'][0];?></td><td class="body" style="text-align:right"><?php if($searchData['c_most_recent_esig_auth_form'][0] == ''){echo '<span style="color:#ff0000">None</span>';}else{echo $searchData['c_most_recent_esig_auth_form'][0];}?></td></tr>
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

$staff_ID = $_GET['staff_ID'];
$acct_ver = $_GET['acct_ver'];
$aID = $_GET['aID'];

if($acct_ver == '1'){ // ACCOUNTING VERIFIED RECEIPT OF SIGNATURE VERIFICATION FORM FROM BUDGET AUTHORITY
$trigger = rand();
#################################################################
## START: UPDATE SIGNATURE LOG TO REFLECT ACCT RECEIPT OF FORM ##
#################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','SIMS_ba_signature_verification_log');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$aID);
$update -> AddDBParam('form_received_in_ACCT_timestamp_trigger',$trigger);
$update -> AddDBParam('form_received_in_ACCT','yes');

$updateResult = $update -> FMEdit();

//echo $updateResult['errorCode'];
//echo $updateResult['foundCount'];
//$updateData = current($updateResult['data']);
#################################################################
## END: UPDATE SIGNATURE LOG TO REFLECT ACCT RECEIPT OF FORM ##
#################################################################
}

if($acct_ver == '2'){ // ACCOUNTING REMOVED VERIFICATION OF RECEIPT OF SIGNATURE VERIFICATION FORM FROM BUDGET AUTHORITY
$trigger = rand();
#####################################################################
## START: UPDATE SIGNATURE LOG TO REFLECT ACCT NON-RECEIPT OF FORM ##
#####################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','SIMS_ba_signature_verification_log');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$aID);
$update -> AddDBParam('form_received_in_ACCT_timestamp','');

$updateResult = $update -> FMEdit();

//echo $updateResult['errorCode'];
//echo $updateResult['foundCount'];
//$updateData = current($updateResult['data']);
###################################################################
## END: UPDATE SIGNATURE LOG TO REFLECT ACCT NON-RECEIPT OF FORM ##
###################################################################
}

#########################################################
## START: FIND LAST 12 PAY PERIODS FOR THE SELECTED STAFF ##
#########################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','SIMS_ba_signature_verification_log',12);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$staff_ID);
//$search -> AddDBParam('c_periodend_local',$today,'lte');
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('month_start_date','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
//$_SESSION['timesheet_foundcount'] = $searchResult['foundCount'];
$recordData = current($searchResult['data']);
#################################################
## END: FIND LAST 12 PAY PERIODS FOR THE SELECTED STAFF ##
#################################################

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

$_SESSION['fullname'] = $recordData2['name_timesheet'][0];
$_SESSION['unit'] = $recordData2['primary_SEDL_workgroup'][0];
################################################
## END: GET SELECTED STAFF NAME AND WORKGROUP ##
################################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Electronic Signature Verification</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="500" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Electronic Signature Verification Admin</h1><hr /></td></tr>
			<tr bgcolor="#e2eaa4"><td class="body" nowrap><b><?php echo $_SESSION['fullname'];?> (<?php echo $_SESSION['unit'];?>)</b></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="menu_sig_verification.php?action=show_all" title="Return to SIMS Paystubs Admin screen.">SIMS Signatures Admin</a> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#e2eaa4">
						
						<td class="body">Audit ID</td>
						<td class="body">Year</td>
						<td class="body">Month</td>
						<td class="body">Num Signatures</td>
						<td class="body">Form Received in ACCT</td>
						
						</tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="body"><?php echo $searchData['c_record_ID'][0];?></td>
						<td class="body"><?php echo $searchData['year'][0];?></td>
						<td class="body"><a href="/staff/sims/menu_sig_verification.php?id=<?php echo $searchData['user_ID'][0];?>&month=<?php echo $searchData['month'][0];?>&year=<?php echo $searchData['year'][0];?>&action=show_log" title="Click here to view this staff member's signature log."><?php echo $searchData['month'][0];?></a></td>
						
						<td class="body"><?php echo $searchData['c_signature_count'][0];?></td>
						<td class="body"><?php if($searchData['form_received_in_ACCT_timestamp'][0] == ''){?><span style="color:#ff0000">Pending</span> | <a href="menu_sig_verification.php?action=show_1&staff_ID=<?php echo $staff_ID;?>&acct_ver=1&aID=<?php echo $searchData['c_record_ID'][0];?>">Verify form received by ACCT</a><?php }else{echo $searchData['form_received_in_ACCT_timestamp'][0];?> | <a href="menu_sig_verification.php?action=show_1&staff_ID=<?php echo $staff_ID;?>&acct_ver=2&aID=<?php echo $searchData['c_record_ID'][0];?>">Remove ACCT verification</a><?php }?></td>
						
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

}elseif($action == 'show_log'){ 



$user_ID = $_GET['id'];
$month = $_GET['month'];
$year = $_GET['year'];


$sortby = $_GET['sortby'];

if($sortby == ''){
$sortby = 'c_table_display';
}
#########################################
## START: FIND SIGNATURE LOG FOR THIS USER
#########################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','audit_table','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('user',$user_ID);
$search -> AddDBParam('c_monthname',$month);
$search -> AddDBParam('c_year',$year);
$search -> AddDBParam('c_is_a_signature','1');
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam($sortby,'ascend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
#########################################
## END: FIND SIGNATURE LOG FOR THIS USER
#########################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Electronic Signature Verification</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="500" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Electronic Signature Verification Admin</h1><hr /></td></tr>
			<tr bgcolor="#e2eaa4"><td class="body" nowrap><strong><?php echo $_SESSION['fullname'];?> (<?php echo $_SESSION['unit'];?>)</strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="menu_sig_verification.php?action=show_all" title="Return to SIMS Paystubs Admin screen.">SIMS Signatures Admin</a> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#ebebeb"><td colspan="4"><strong><?php echo $month.' '.$year.' | '.$searchResult['foundCount'];?> signatures found.</strong><div style="float:right"><input type=button value="<< Back <<" onClick="history.back()"></div></td></tr>
						<tr bgcolor="#e2eaa4">
						
						<td class="body" nowrap>Audit ID</td>
						<td class="body" nowrap><a href="menu_sig_verification.php?id=<?php echo $user_ID;?>&month=<?php echo $month;?>&year=<?php echo $year;?>&action=show_log&sortby=timestamp">Signature Date</a></td>
						<td class="body"><a href="menu_sig_verification.php?id=<?php echo $user_ID;?>&month=<?php echo $month;?>&year=<?php echo $year;?>&action=show_log&sortby=c_table_display">Item</a></td>
						<td class="body"><a href="menu_sig_verification.php?id=<?php echo $user_ID;?>&month=<?php echo $month;?>&year=<?php echo $year;?>&action=show_log&sortby=c_action_display">Description</a></td>
						
						</tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="body"><?php echo $searchData['c_record_ID'][0];?></td>
						<td class="body" nowrap><?php echo $searchData['timestamp'][0];?></td>
						<td class="body" nowrap><?php echo $searchData['c_table_display'][0];?></td>
						<td class="body"><?php echo $searchData['action_description'][0];?></td>
						
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

}else{ ?>


Error | <a href="menu_sig_verification.php?action=show_all" title="Return to SIMS Electronic Signatures Admin screen.">Return to Electronic Signatures Admin</a>

<?php } ?>





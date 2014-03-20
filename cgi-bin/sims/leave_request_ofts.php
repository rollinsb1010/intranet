<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


$debug = 'off';


$action = $_GET['action'];
$leave_request_ID = $_GET['leave_request_ID'];
$approve = $_GET['approve'];

if($action == 'view'){ //IF THE USER IS VIEWING THIS LEAVE REQUEST



#################################################################
## START: FIND LEAVE REQUEST HRS RELATED TO THIS LEAVE REQUEST ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','leave_request_hrs');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('leave_request_ID','=='.$leave_request_ID);
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('leave_hrs_date','ascend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
###############################################################
## END: FIND LEAVE REQUEST HRS RELATED TO THIS LEAVE REQUEST ##
###############################################################
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Leave Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function preventDelete() { 
	var answer = confirm ("Approved leave requests cannot be deleted.")
	return false;
	
}
</script>

<script language="JavaScript">


function confirmSign() { 
	var answer2 = confirm ("Sign this leave request now?")
	if (!answer2) {
	return false;
	}
}

function pbaMessage() { 
	var answer2 = confirm ("Immediate Supervisor must sign this leave request before Primary Budget Authority approval.")
	return false;
}

function checkTimesheet() { 
	
		if (document.timesheet_check.timesheet_ID.value ==""){
			alert("A timesheet does not yet exist for this pay period.");
			return false;	}

}			

</script>
</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Leave Requests: OFTS Admin</h1><hr /></td></tr>
			
			<?php if($recordData['leave_requests::approval_status'][0] == 'Approved'){ ?>
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Current User: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - This leave request has been approved. <img src="/staff/sims/images/green_check.png"> | <a href="/staff/sims/leave_request_print.php?leave_request_ID=<?php echo $recordData['leave_request_ID'][0];?>&action=view&payperiod=<?php echo $recordData['leave_requests::pay_period_end'][0];?>" target="_blank">Print form</a> | <input type=button value="Close Leave Request" onClick="history.back()"></p>
			</td></tr>
			
			<?php } elseif($recordData['leave_requests::approval_status'][0] != 'Approved'){ ?>
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Current User: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - This leave request has not been approved. | <input type=button value="Close Leave Request" onClick="history.back()"></p>
			</td></tr>			

			
			<?php } ?>
			
			
			<tr><td colspan="2">
			
						<table cellpadding="10" cellspacing="0" border="0" bordercolor="ebebeb" bgcolor="ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $recordData['leave_requests_staff_byStaffID::name_timesheet'][0];?> (<?php echo $recordData['leave_requests_staff_byStaffID::primary_SEDL_workgroup'][0];?>)</strong></td><td class="body" align="right" nowrap>Leave Request Status: <?php echo $recordData['leave_requests::approval_status'][0];?> | Pay Period: <strong><?php echo $recordData['leave_requests::pay_period_end'][0];?></strong></td></tr>
						<tr><td class="body" nowrap><strong>LEAVE REQUEST</strong></td><td align="right">Leave Request ID: <?php echo $recordData['leave_request_ID'][0];?> | <a href="/staff/sims/timesheets_ofts_app.php?Timesheet_ID=<?php echo $recordData['leave_requests::timesheet_ID'][0];?>&action=view&src=menu&payperiod=<?php echo $recordData['leave_requests::pay_period_end'][0];?>" target="_blank" onclick="return checkTimesheet()">View this timesheet</a></td></tr>
						<form name="timesheet_check"><input type="hidden" name="timesheet_ID" value="<?php echo $recordData['leave_requests::timesheet_ID'][0];?>"></form>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr bgcolor="#ebebeb"><td class="body" nowrap>Leave Type</td><td class="body">Date</td><td class="body">From</td><td class="body">To</td><td class="body" align="right">Hours</td><td class="body">Options</td></tr>

								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								

										<tr class="body"><td nowrap><?php echo $searchData['leave_hrs_type'][0];?><?php if($searchData['c_lv_hrs_requires_documentation'][0] == '1'){echo '*';}?></td><td nowrap><?php echo $searchData['leave_hrs_date'][0];?> <span class="tiny">(<?php echo strtoupper($searchData['c_leave_hrs_day_name'][0]);?>)</span></td><td nowrap><?php echo $searchData['leave_hrs_time_begin'][0];?></td><td nowrap><?php echo $searchData['leave_hrs_time_end'][0];?></td><td nowrap align="right"><?php echo $searchData['leave_num_hrs'][0];?></td><td nowrap width="100%">&nbsp;</td></tr>
									
							
								<?php } ?>

									<tr class="body"><td bgcolor="#ebebeb" colspan="4" nowrap align="right"><em>Total Request Hours:</em></td><td align="right"><?php echo $searchData['leave_requests::c_total_request_hrs'][0];?></td><td colspan="2" align="right"><?php if($_GET['edit_request_row'] != '1'){ ?><em><font color="#666666">Created: <?php echo $searchData['leave_requests::creation_timestamp'][0];?> | Modified: <?php echo $searchData['leave_requests::c_last_mod_hrs'][0];?></font></em><?php }?></tr>



											
											<tr class="body"><td colspan="6" nowrap><strong>SIGNATURES</strong>:<br>
											
												<table class="sims" cellspacing="1" cellpadding="10" border="1">
												<tr class="body" valign="top"><td align="center" valign="bottom">
												<?php if($searchData['leave_requests::signer_status_owner'][0] == '1'){?><img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_owner'][0];?>.png"><?php }?><p>
												<span class="tiny">Staff Member<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_owner'][0];?>]</font></span></td>

<?php if($recordData['leave_requests_staff_byStaffID::c_cwp_spvsr_is_pba'][0] != '1'){ // IF THE STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PRIMARY BUDGET AUTHORITY ARE NOT THE SAME PERSON ?>

												<td align="center" valign="bottom"><?php if($searchData['leave_requests::signer_status_imm_spvsr'][0] == '1'){ ?><img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_imm_spvsr'][0];?>.png"><?php } ?><p>
												<span class="tiny">Immediate Supervisor<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_imm_spvsr'][0];?>]</font></span></td>








												<td align="center" valign="bottom">

												<?php if($searchData['leave_requests::signer_status_pba'][0] == '1'){ // IF THE PBA HAS SIGNED THE LEAVE REQUEST ?>

													<img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_pba'][0];?>.png">

												<?php } elseif(($searchData['leave_requests::signer_status_pba'][0] != '1') && ($searchData['leave_requests::signer_status_imm_spvsr'][0] != '1')) {  // IF NEITHER THE IMMEDIATE SUPERVISOR NOR THE PBA HAS SIGNED THE LEAVE REQUEST
												
													echo '<a href="http://www.sedl.org/staff/sims/leave_request_ba.php?action=view&approve=ba&leave_request_ID='.$recordData['leave_request_ID'][0].'&leave_request_row_ID='.$searchData['leave_requests::c_row_ID_cwp'][0].'" onClick="return pbaMessage()">'.$searchData['leave_requests::signer_ID_pba'][0].'</a>';?>

												<?php } else {  // IF THE IMMEDIATE SUPERVISOR HAS SIGNED THE LEAVE REQUEST BUT THE PBA HAS NOT SIGNED THE LEAVE REQUEST

													echo '<a href="http://www.sedl.org/staff/sims/leave_request_ba.php?action=view&approve=ba&leave_request_ID='.$recordData['leave_request_ID'][0].'&leave_request_row_ID='.$searchData['leave_requests::c_row_ID_cwp'][0].'" onClick="return confirmSign()">'.$searchData['leave_requests::signer_ID_pba'][0].'</a>';?>


												<?php } ?><p>
												
												<span class="tiny">Primary Budget Authority<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_pba'][0];?>]</font></span></td>






<?php } else {  // IF THE STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PRIMARY BUDGET AUTHORITY ARE THE SAME PERSON ?>

												<td align="center" valign="bottom"><?php if($searchData['leave_requests::signer_status_pba'][0] == '1'){ ?><img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_pba'][0];?>.png"><?php } ?><p>
												<span class="tiny">Primary Budget Authority<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_pba'][0];?>]</font></span></td>

<?php } ?>

<?php if(($recordData['c_total_request_hrs_f'][0] > 1)||($recordData['c_total_request_hrs_l'][0] > 1)) {  // IF THE LEAVE REQUEST CONTAINS LEAVE W/O PAY HOURS AND REQUIRES CEO APPROVAL ?>

												<td align="center" valign="bottom"><?php if($searchData['leave_requests::signer_status_ceo'][0] == '1'){ ?><img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_ceo'][0];?>.png"><?php } ?><p>
												<span class="tiny">President & CEO<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_ceo'][0];?>]</font></span></td>

<?php } ?>



												</tr>										
												</table>

											</td></tr>


							</table><div class="tiny">*Supporting documentation required.</div>

						</td></tr>

						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>


<? } elseif($action == 'approve') { 

$leave_request_ID = $_GET['leave_request_ID'];
$current_id = $_GET['leave_request_row_ID'];

$trigger = rand();
#################################################
## START: UPDATE THE LEAVE REQUEST ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','leave_requests2');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);
$update -> AddDBParam('signer_status_pba','1');
$update -> AddDBParam('approval_status','Approved');
$update -> AddDBParam('signer_timestamp_pba_trigger',$trigger);


$updateResult = $update -> FMEdit();

//  echo  '<p>errorCode: '.$updateResult['errorCode'];
//  echo  '<p>foundCount: '.$updateResult['foundCount'];
//  $recordData = current($updateResult['data']);

if($updateResult['errorCode'] == '0'){
$_SESSION['leave_request_signed_pba'] = '1';

// SEND E-MAIL NOTIFICATION APPROVED TO STAFF MEMBER, AND WORKGROUP ADMIN

} else {
$_SESSION['leave_request_signed_pba'] = '2';

}
###############################################
## END: UPDATE THE LEAVE REQUEST ##
###############################################

header('Location: http://www.sedl.org/staff/sims/menu_leave_ba.php?view_payperiod='.$_SESSION['payperiod_selected']);
exit;
?>
<?php } else { ?>
Error
<?php } ?>

<?php 

if($debug == 'on'){

echo '<p>$action: '.$action;
echo '<p>$delete_request_row: '.$delete_request_row;
echo '<p>$add_to_request: '.$add_to_request;
echo '<p>$leave_request_ID: '.$leave_request_ID;
echo '<p>$timesheet_ID: '.$timesheet_ID;
echo '<p>$_SESSION[leave_request_ID]: '.$_SESSION['leave_request_ID'];
echo '<p>$day_from: '.$day_from;
echo '<p>$day_to: '.$day_to;
echo '<p>$time_from: '.$time_from;
echo '<p>$time_to: '.$time_to;
echo '<p>$num_hrs: '.$num_hrs;
echo '<p>$date_from_m: '.$date_from_m;
echo '<p>$date_from_y: '.$date_from_y;

}
?>
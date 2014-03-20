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
$approval_status = $_GET['approval_status'];


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
$d = $recordData['leave_requests::c_pay_period_begin_d'][0];

###############################################################
## END: FIND LEAVE REQUEST HRS RELATED TO THIS LEAVE REQUEST ##
###############################################################



?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: My Leave Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table cellpadding="0" cellspacing="0" border="0" width="700">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
			
			<tr><td colspan="2"><img src="/staff/sims/images/logo-new-grayscale.png" width="86" height="34" alt="SEDL-Logo"></td></tr>
		
			
			
			<tr><td colspan="2">
			
						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#ebebeb"><td class="body"><strong><?php echo $recordData['leave_requests_staff_byStaffID::name_timesheet'][0];?> (<?php echo $recordData['leave_requests_staff_byStaffID::primary_SEDL_workgroup'][0];?>)</strong></td><td class="body" align="right" nowrap>Leave Request Status: <?php echo $recordData['leave_requests::approval_status'][0];?> | Pay Period: <strong><?php echo $recordData['leave_requests::pay_period_end'][0];?></strong></td></tr>
						<tr><td class="body" nowrap><strong>LEAVE REQUEST</strong></td><td align="right">Leave Request ID: <?php echo $recordData['leave_request_ID'][0];?></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr bgcolor="#ebebeb"><td class="body" nowrap>Leave Type</td><td class="body">Date</td><td class="body">From</td><td class="body">To</td><td class="body" align="right">Hours</td></tr>

								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								

										<tr class="body"><td nowrap><?php echo $searchData['leave_hrs_type'][0];?></td><td nowrap><?php echo $searchData['leave_hrs_date'][0];?> <span class="tiny">(<?php echo strtoupper($searchData['c_leave_hrs_day_name'][0]);?>)</span></td><td nowrap><?php echo $searchData['leave_hrs_time_begin'][0];?></td><td nowrap><?php echo $searchData['leave_hrs_time_end'][0];?></td><td nowrap align="right"><?php echo $searchData['leave_num_hrs'][0];?></td></tr>
									
							
								<?php } ?>

									<tr class="body"><td bgcolor="#ebebeb" colspan="4" nowrap align="right"><em>Total Request Hours:</em></td><td align="right"><?php echo $searchData['leave_requests::c_total_request_hrs'][0];?></td></tr>

									<?php if(($_GET['enter_new_hours'] != '1') && ($_GET['edit_request_row'] != '1')){ // IF THE LEAVE REQUEST IS NOT CURRENTLY BEING MODIFIED ?>

										<?php if((($searchData['leave_requests::approval_status'][0] == 'Not Submitted')||($searchData['leave_requests::approval_status'][0] == 'Revised')) && ($searchData['timesheets::c_timesheet_is_locked'][0] == '0')){ //IF THE LEAVE REQUEST HAS NOT YET BEEN SIGNED/SUBMITTED, SHOW SUBMIT BUTTON  ?>



										<?php } else { //IF THE LEAVE REQUEST HAS BEEN SIGNED/SUBMITTED, SHOW APPROVAL SIGNATURES ?>

											<tr class="body">
											<td colspan="6" nowrap><em><font color="#666666">Created: <?php echo $searchData['leave_requests::creation_timestamp'][0];?> | Modified: <?php echo $searchData['leave_requests::c_last_mod_hrs'][0];?></font></em></td></tr>
											
											<tr><td colspan="6" nowrap><strong>SIGNATURES</strong>:<br>
											
												<table class="sims" cellspacing="1" cellpadding="10" border="1">
												<tr class="body" valign="top"><td align="center" valign="bottom">
												<img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_owner'][0];?>.png"><p>
												<span class="tiny">Staff Member<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_owner'][0];?>]</font></span></td>

<?php if($recordData['leave_requests_staff_byStaffID::c_cwp_spvsr_is_pba'][0] != '1'){ // IF THE STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PRIMARY BUDGET AUTHORITY ARE NOT THE SAME PERSON ?>


												<td align="center" valign="bottom"><?php if($searchData['leave_requests::signer_status_imm_spvsr'][0] == '1'){ ?><img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_imm_spvsr'][0];?>.png"><?php } else { echo $searchData['leave_requests::signer_ID_imm_spvsr'][0];?><?php } ?><p>
												<span class="tiny">Immediate Supervisor<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_imm_spvsr'][0];?>]</font></span></td>

												<td align="center" valign="bottom"><?php if($searchData['leave_requests::signer_status_pba'][0] == '1'){ ?><img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_pba'][0];?>.png"><?php } else { echo $searchData['leave_requests::signer_ID_pba'][0];?><?php } ?><p>
												<span class="tiny">Primary Budget Authority<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_pba'][0];?>]</font></span></td>

<?php } else {  // IF THE STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PRIMARY BUDGET AUTHORITY ARE THE SAME PERSON ?>

												<td align="center" valign="bottom"><?php if($searchData['leave_requests::signer_status_pba'][0] == '1'){ ?><img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_pba'][0];?>.png"><?php } else { echo $searchData['leave_requests::signer_ID_pba'][0];?><?php } ?><p>
												<span class="tiny">Primary Budget Authority<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_pba'][0];?>]</font></span></td>
<?php } ?>	

<?php if(($recordData['c_total_request_hrs_f'][0] > 1)||($recordData['c_total_request_hrs_l'][0] > 1)) {  // IF THE LEAVE REQUEST CONTAINS LEAVE W/O PAY HOURS AND REQUIRES CEO APPROVAL ?>

												<td align="center" valign="bottom"><?php if($searchData['leave_requests::signer_status_ceo'][0] == '1'){ ?><img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_ceo'][0];?>.png"><?php } ?><p>
												<span class="tiny">President & CEO<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_ceo'][0];?>]</font></span></td>

<?php } ?>


												</tr>										
												</table>

											</td></tr>

										<?php }?>

									<?php }?>
							</table>

						</td></tr>

						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>



<?php } else { ?>
Error
<?php } ?>


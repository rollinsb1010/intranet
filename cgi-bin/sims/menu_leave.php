<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


########################################
## START: DELETE REQUEST IF NECESSARY ##
########################################
if($_GET['delete_key'] != ''){
$delete_row_ID = $_GET['delete_key'];
$delete = new FX($serverIP,$webCompanionPort);
$delete -> SetDBData('SIMS_2.fp7','leave_requests2');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$delete_row_ID);

$deleteResult = $delete -> FMDelete();

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','DELETE_LEAVE_REQUEST_STAFF');
$newrecord -> AddDBParam('table','LEAVE_REQUESTS');
//$newrecord -> AddDBParam('object_ID',$recordData['leave_request_ID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$delete_row_ID);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

}
######################################
## END: DELETE REQUEST IF NECESSARY ##
######################################
$displaynum = $_GET['displaynum'];
if($displaynum == ''){
$displaynum = 10;
}
##############################################
## START: FIND LEAVE REQUESTS FOR THIS USER ##
##############################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','leave_requests2',$displaynum);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$_SESSION['staff_ID']);
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('approval_status','descend');
$search -> AddSortParam('pay_period_end','descend');
$search -> AddSortParam('c_leave_hrs_begin_date','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
$_SESSION['timesheet_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
############################################
## END: FIND LEAVE REQUESTS FOR THIS USER ##
############################################
$current_pay_period = date("m").'/'.date("t").'/'.date("Y");

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: My Leave Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function preventDelete() { 
	var answer = confirm ("Approved leave requests cannot be deleted.")
	return false;
	
}
</script>

<script language="JavaScript">

function confirmDelete() { 
	var answer2 = confirm ("Are you sure you want to delete this leave request?")
	if (!answer2) {
	return false;
	}
}

function confirmResubmit() { 
	var answer3 = confirm ("Re-submit this leave request to your current supervisor and PBA?")
	if (!answer3) {
	return false;
	}
}

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
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Leave Requests</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="/staff/sims/leave_prefs.php" title="Update your SIMS leave preferences.">Leave Preferences</a> | <a href="my_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>">My Leave Calendar</a> | <a href="/staff/sims/leave_request.php?action=new">New Leave Request</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<?php if($_SESSION['timesheet_signed_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your leave request has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['timesheet_signed_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your leave request, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance. (Error code: <?php echo $_SESSION['last_error'];?>)</p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['leave_prefs_updated'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your leave preferences have been updated.</p></td></tr>
				<?php $_SESSION['leave_prefs_updated'] = ''; ?>

			<?php } elseif($_SESSION['leave_prefs_updated'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem updating your leave preferences, please contact <a href="mailto:sims@sedl.org">technical support</a>.</p></td></tr>
				<?php $_SESSION['leave_prefs_updated'] = ''; ?>

			<?php } ?>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="#ebebeb" bgcolor="#ffffff" width="100%" class="sims">
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body">Date(s) of Leave</td>
						<td class="body" align="right">Total Hrs</td>
						<td class="body">Type</td>

						<td class="body">Pay Period</td>
						<td class="body">Date/Time Submitted</td>

						<td class="body">Submitted to</td>
						<td class="body" align="right">Status</td>

						<td class="body" align="right">Delete</td></tr>
						
<?php if($searchResult['foundCount'] > 0){ ?>

						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							<tr>
							<td class="body"><?php echo $searchData['leave_request_ID'][0];?></td>
							<td class="body"><a href="/staff/sims/leave_request.php?leave_request_ID=<?php echo $searchData['leave_request_ID'][0];?>&action=view&payperiod=<?php echo $searchData['pay_period_end'][0];?>"><?php if($searchData['c_leave_hrs_begin_date'][0] != $searchData['c_leave_hrs_end_date'][0]){echo $searchData['c_leave_hrs_begin_date'][0].' - '.$searchData['c_leave_hrs_end_date'][0];} else { echo $searchData['c_leave_hrs_begin_date'][0];}?></a></td>
							<td class="body" align="right"><?php echo $searchData['c_total_request_hrs'][0];?></td>
							<td class="body"><?php echo $searchData['c_lv_hrs_total_summary'][0];?></td>
	
							<td class="body"><?php echo $searchData['pay_period_end'][0];?></td>
							<td class="body"><?php echo $searchData['signer_timestamp_owner'][0];?></td>
							<td class="body"><span class="tiny">SPVSR: </span><?php echo $searchData['signer_ID_imm_spvsr'][0].' | <span class="tiny">PBA: </span>'.$searchData['signer_ID_pba'][0];?><?php if(($searchData['c_current_supervisor_pba_check'][0] == '1')&&($searchData['approval_status'][0] !== 'Approved')){?><div style="float:right"><a href="leave_request.php?action=submit_leave_request&leave_request_ID=<?php echo $searchData['leave_request_ID'][0];?>&leave_request_row_ID=<?php echo $searchData['c_row_ID_cwp'][0];?>&status=revised&mod=new_spvsr&spvsr=<?php echo $searchData['leave_requests_staff_byStaffID::immediate_supervisor_sims_user_ID'][0];?>&pba=<?php echo $searchData['leave_requests_staff_byStaffID::bgt_auth_primary_sims_user_ID'][0];?>&sisba=<?php echo $searchData['leave_requests_staff_byStaffID::c_cwp_spvsr_is_pba'][0];?>" onclick="return confirmResubmit()"><img src="/common/images/bullets/exclamation.png" title="A supervisor or PBA change has occurred since this leave request was submitted. Click this icon to re-submit this request to the current supervisor and/or PBA."></a></div><?php }?></td>
							
							
							
							<?php if($searchData['leave_requests_timesheets_byPayPeriod::c_timesheet_is_locked'][0]=='1'){ ?>
							<td class="body" align="right"><?php if($searchData['approval_status'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status'][0];?></font></td>
							<td class="body" align="right"><img src="/staff/sims/images/padlock.jpg" border="0"></td>
							<?php }else{ ?>
							<td class="body" align="right"><?php if($searchData['approval_status'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status'][0];?></font></td>
							<td class="body" align="right"><a href="menu_leave.php?delete_key=<?php echo $searchData['c_row_ID_cwp'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/trashcan.jpg" border="0"></a></td>
							<?php } ?>
							</tr>
						<?php }?>


						<tr><td colspan="6" style="background-color:#fbf59a"><a href="menu_leave.php?displaynum=<?php echo $displaynum + 10;?>">Show more</a><?php if($displaynum > 10){?> | <a href="menu_leave.php?displaynum=<?php echo $displaynum - 10;?>">Show less</a><?php }?></td><td colspan="3" align="right" style="background-color:#000000;color:#ffffff;text-align:center">Showing <?php echo $displaynum;?> records</td></tr>
						

<?php } else { ?>


						<tr>
						<td class="body" colspan="9" height="40" align="center">No records found.</td>
						</tr>


<?php } ?>







						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>

<?php //} else { ?>

<!--No records found.-->

<?php //} ?>

<?php //} else { ?>



<?php //} ?>
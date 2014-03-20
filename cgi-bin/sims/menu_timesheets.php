<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

$debug = 'off';
$timesheets_access = 'yes';

$sortfield = $_GET['sortfield'];

$displaynum = $_GET['displaynum'];
if($displaynum == ''){
$displaynum = 12;
}


include_once('FX/FX.php');
include_once('FX/server_data.php');

#########################################
## START: FIND TIMESHEETS FOR THIS USER
#########################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','timesheets',$displaynum);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$_SESSION['staff_ID']);
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam($sortfield,'descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$_SESSION['timesheet_foundcount'] = $searchResult['foundCount'];
$recordData = current($searchResult['data']);

$_SESSION['timesheet_has_related_leave_requests'] = $recordData['c_timesheet_has_related_leave_requests'][0];
$_SESSION['last_pay_period_end'] = $recordData['c_last_pay_period'][0];
$_SESSION['last_pay_period_end_m'] = $recordData['c_last_pay_period_month'][0];
$_SESSION['last_pay_period_end_d'] = $recordData['c_last_pay_period_day'][0];
$_SESSION['last_pay_period_end_y'] = $recordData['c_last_pay_period_year'][0];
$_SESSION['current_pay_period_end'] = $recordData['c_PayPeriodEnd'][0];

$_SESSION['timesheet_owner_FTE_status'] = $recordData['staff_FTE_status'][0];
$_SESSION['timesheet_approval_not_required'] = $recordData['staff_no_time_leave_approval_required'][0];


if($debug == 'on'){
echo '<p>staff_ID: '. $_SESSION['staff_ID'];
echo '<p>$_SESSION[timesheet_approval_not_required]: '. $_SESSION['timesheet_approval_not_required'];
echo '<p>$_SESSION[last_pay_period_end]: '. $_SESSION['last_pay_period_end'];
echo '<p>$_SESSION[last_pay_period_end_m]: '. $_SESSION['last_pay_period_end_m'];
echo '<p>$_SESSION[last_pay_period_end_d]: '. $_SESSION['last_pay_period_end_d'];
echo '<p>$_SESSION[last_pay_period_end_y]: '. $_SESSION['last_pay_period_end_y'];
echo '<p>$_SESSION[current_pay_period_end]: '. $_SESSION['current_pay_period_end'];
echo '<p>$_SESSION[timesheet_owner_FTE_status]: '. $_SESSION['timesheet_owner_FTE_status'];

}


/*
echo '<p>last_pay_period_end: '. $_SESSION['last_pay_period_end'];
echo '<p>last_pay_period_end_m: '. $_SESSION['last_pay_period_end_m'];
echo '<p>last_pay_period_end_d: '. $_SESSION['last_pay_period_end_d'];
echo '<p>last_pay_period_end_y: '. $_SESSION['last_pay_period_end_y'];

echo '<p>timesheet_name: '. $_SESSION['timesheet_name'];
*/
#########################################
## END: FIND TIMESHEETS FOR THIS USER
#########################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
if ($timesheets_access == 'yes') { //IF TIMESHEETS ACCESS IS TURNED ON 
?>

<html>
<head>
<title>SIMS: My Timesheets</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function preventDelete() { 
	var answer = confirm ("Approved Timesheets cannot be deleted.")
	return false;
	
}
</script>

<script language="JavaScript">

function confirmDelete() { 
	var answer2 = confirm ("Are you sure you want to delete this timesheet?")
	if (!answer2) {
	return false;
	}
}

function confirmDeleteLeave() { 
	var answer4 = confirm ("This timesheet has related leave requests. Deleting this timesheet will delete all related leave requests for this pay period. Are you sure you want to delete this timesheet?")
	if (!answer4) {
	return false;
	}
}


function confirmDuplicate() { 
	var answer3 = confirm ("Create a new timesheet by duplicating this timesheet?")
	if (!answer3) {
	return false;
	}
}

function confirmReSign() { 
	var answer4 = confirm ("Re-submit this timesheet to your current supervisor and PBA?")
	if (!answer4) {
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Timesheets</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="bgt_code_report_staff.php?action=new">Reports</a> | <a href="/staff/sims/timesheet_prefs.php" title="Update your SIMS timesheet preferences.">Timesheet Preferences</a> | <a href="/staff/sims/my_budget_codes.php" target="top" title="Click here to add budget codes to your budget code list.">My Budget Codes</a> | <a href="/staff/sims/timesheets_newb.php" title="Create a new timesheet.">New Timesheet</a> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			<?php if($_SESSION['timesheet_signed_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your timesheet has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['timesheet_signed_staff'] == '1_revised'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your revised timesheet has been successfully re-submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>


			<?php } elseif($_SESSION['timesheet_signed_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your timesheet, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance (errorCode_998).  </p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['timesheet_prefs_updated'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your timesheet preferences have been updated.</p></td></tr>
				<?php $_SESSION['timesheet_prefs_updated'] = ''; ?>

			<?php } ?>

			<?php if($_SESSION['illegal_action'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Illegal timesheet operation. Please contact SIMS technical assistance if you need help with this action. This action has been logged.</p></td></tr>
				<?php $_SESSION['illegal_action'] = ''; ?>

			<?php } ?>
			
			<?php if($_SESSION['timesheet_delete'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Timesheet successfully deleted.</p></td></tr>
				<?php $_SESSION['timesheet_delete'] = ''; ?>

			<?php } ?>
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body">Pay Period</td>
						<td class="body" align="right">Total Hrs<span class="tiny">*</span></td>
						<td class="body" align="right">Work Hrs</td>
						<td class="body" align="right">Leave Hrs</td>
						<td class="body">Date/Time Submitted</td>
						<td class="body">Submitted to</td>
						<td class="body" align="right">Status</td>
						<td class="body" align="right">Delete</td></tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="body"><?php echo $searchData['TimesheetID'][0];?></td>
						<td class="body"><a href="/staff/sims/timesheets.php?Timesheet_ID=<?php echo $searchData['TimesheetID'][0];?>&action=view&src=menu&payperiod=<?php echo $searchData['c_PayPeriodEnd'][0];?>" title="Click here to view this timesheet."><?php echo $searchData['c_PayPeriodEnd'][0];?></a>&nbsp;&nbsp;<a href="timesheets_duplicate2.php?Timesheet_ID=<?php echo $searchData['TimesheetID'][0];?>" title="Create a new timesheet from this timesheet." onclick="return confirmDuplicate()"><img src="images/duplicate.png" valign="middle" border="0"></a></td>
						
						<td class="body" align="right"><?php if($searchData['c_total_WkHrsReg'][0] == ''){echo '&nbsp;';}else{echo '<span title="'.$searchData['c_timesheet_hrs_email_summary'][0].'">'.$searchData['c_total_WkHrsReg'][0].'</span>';}?></td>
						<td class="body" align="right"><?php if($searchData['c_total_WkHrsRegNonLv'][0] == ''){echo '&nbsp;';}else{echo $searchData['c_total_WkHrsRegNonLv'][0];}?></td>
						<td class="body" align="right"><?php if($searchData['c_TimeSheetTotalLvHrs'][0] == ''){echo '&nbsp;';}else{echo $searchData['c_TimeSheetTotalLvHrs'][0];}?></td>
						
						<td class="body"><?php if($searchData['Signer_Timestamp_owner'][0] == ''){echo '&nbsp;';}else{echo $searchData['Signer_Timestamp_owner'][0];}?></td>
						<td class="body"><span class="tiny">SPVSR: </span><?php echo $searchData['StaffImmediateSupervisor'][0].' | <span class="tiny">PBA: </span>'.$searchData['StaffPrimaryBudgetAuthority'][0];?><?php if(($searchData['c_current_supervisor_pba_check'][0] == '1')&&($searchData['TimesheetSubmittedStatus'][0] !== 'Approved')){?><div style="float:right"><a href="timesheets_process_revised.php?action=staff_sign&row_ID=<?php echo $searchData['c_row_ID_cwp'][0];?>&mod=new_spvsr&spvsr=<?php echo $searchData['staff::immediate_supervisor_sims_user_ID'][0];?>&pba=<?php echo $searchData['staff::bgt_auth_primary_sims_user_ID'][0];?>&sisba=<?php echo $searchData['staff::c_cwp_spvsr_is_pba'][0];?>" onclick="return confirmReSign()"><img src="/common/images/bullets/exclamation.png" title="A supervisor or PBA change has occurred since this timesheet was submitted. Click this icon to re-submit this timesheet to the current supervisor and/or PBA."></div><?php }?></td>
						
						
						<?php if($searchData['TimesheetSubmittedStatus'][0]=='Approved'){ ?>
						<td class="body" align="right"><font color="blue"><?php echo $searchData['TimesheetSubmittedStatus'][0];?></font></td>
						<td class="body" align="right"><img src="/staff/sims/images/padlock.jpg" border="0"></td>
						<?php }else{ ?>
						<td class="body" align="right"><font color="red"><?php echo $searchData['TimesheetSubmittedStatus'][0];?></font></td>
						<td class="body" align="right"><a href="/staff/sims/timesheet_delete.php?row_ID=<?php echo $searchData['c_row_ID_cwp'][0];?>" onclick="return confirmDelete()" title="Click here to delete this timesheet."><img src="/staff/sims/images/trashcan.jpg" border="0"></a></td>
						<?php } ?>
						</tr>
			
						<?php } ?>
						
						<tr><td colspan="6" style="background-color:#fbf59a"><a href="menu_timesheets.php?sortfield=c_PayPeriodEnd&displaynum=<?php echo $displaynum + 12;?>">Show more</a><?php if($displaynum > 12){?> | <a href="menu_timesheets.php?sortfield=c_PayPeriodEnd&displaynum=<?php echo $displaynum - 12;?>">Show less</a><?php }?></td><td colspan="3" align="right" style="background-color:#000000;color:#ffffff;text-align:center">Showing <?php echo $displaynum;?> records</td></tr>

						</table>
						<span class="tiny">* Hover over the Total Hrs column to display a breakdown of timesheet hours.</span>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>

<?php } else { //IF TIMESHEETS ACCESS IS TURNED OFF?>

<html>
<head>
<title>SIMS: My Timesheets</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Timesheets</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<?php if($_SESSION['timesheet_signed_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your timesheet has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['timesheet_signed_staff'] == '1_revised'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your revised timesheet has been successfully re-submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>


			<?php } elseif($_SESSION['timesheet_signed_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your timesheet, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance (errorCode_998).  </p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>
			
			<?php } ?>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body">Pay Period</td>
						<td class="body" align="right">Total Hrs</td>
						<td class="body" align="right">Sick Hrs</td>
						<td class="body" align="right">Vac Hrs</td>
						<td class="body" align="right">Pers Hrs</td>
						<td class="body" align="right">UnPdLv Hrs</td>
						<td class="body" align="right">OT Hrs</td>
						<td class="body">Date/Time Submitted</td>
						<td class="body" align="right">Status</td>
						<td class="body" align="right">Delete</td></tr>
						
						
						<tr>
						<td class="body" colspan="11" align="center">TIMESHEETS ACCESS IS TEMPORARILY UNAVAILABLE</td>
						</tr>
			
						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>


<?php } ?>

<?php //} else { ?>



<?php //} ?>
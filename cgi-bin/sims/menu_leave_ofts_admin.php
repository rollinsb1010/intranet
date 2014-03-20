<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');

$today = date("M d Y");
$today_m = date("m");
$next_m = $today_m + 1;
$next_m2 = $today_m + 2;
$prev_m = $today_m - 1;
$prev_m2 = $today_m - 2;
$prev_m3 = $today_m - 3;
$today_d = date("d");
$today_y = date("Y");

//mktime(0, 0, 0, 7, 1, 2000);

$_SESSION['view_payperiod'] = $_GET['view_payperiod'];
########################################################################
## START: GENERATE DEFAULT PAY PERIOD & VALUE LIST FOR DROP DOWN MENU ##
########################################################################
if($today_d > 18){

	$_SESSION['default_payperiod'] = date("m/d/Y",mktime(0, 0, 0, $next_m, 0, $today_y)); //$next_m.'/'.'0'.'/'.$today_y;
	
	$_SESSION['payperiod_option_1'] = date("m/d/Y",mktime(0, 0, 0, $prev_m, 15, $today_y)); //$prev_m.'/'.'15'.'/'.$today_y;
	$_SESSION['payperiod_option_2'] = date("m/d/Y",mktime(0, 0, 0, $today_m, 0, $today_y)); //$today_m.'/'.'0'.'/'.$today_y;
	$_SESSION['payperiod_option_3'] = date("m/d/Y",mktime(0, 0, 0, $today_m, 15, $today_y)); //$today_m.'/'.'15'.'/'.$today_y;
	$_SESSION['payperiod_option_4'] = $_SESSION['default_payperiod'];
	$_SESSION['payperiod_option_5'] = date("m/d/Y",mktime(0, 0, 0, $next_m, 15, $today_y)); //$next_m.'/'.'15'.'/'.$today_y;
	$_SESSION['payperiod_option_6'] = date("m/d/Y",mktime(0, 0, 0, $next_m2, 0, $today_y)); //$next_m2.'/'.'0'.'/'.$today_y;

}elseif($today_d < 4){

	$_SESSION['default_payperiod'] = date("m/d/Y",mktime(0, 0, 0, $today_m, 0, $today_y)); //$next_m.'/'.'0'.'/'.$today_y;
	
	$_SESSION['payperiod_option_1'] = date("m/d/Y",mktime(0, 0, 0, $prev_m2, 15, $today_y)); //$prev_m.'/'.'0'.'/'.$today_y;
	$_SESSION['payperiod_option_2'] = date("m/d/Y",mktime(0, 0, 0, $prev_m, 0, $today_y)); //$prev_m.'/'.'15'.'/'.$today_y;
	$_SESSION['payperiod_option_3'] = date("m/d/Y",mktime(0, 0, 0, $prev_m, 15, $today_y)); //$today_m.'/'.'0'.'/'.$today_y;
	$_SESSION['payperiod_option_4'] = $_SESSION['default_payperiod'];
	$_SESSION['payperiod_option_5'] = date("m/d/Y",mktime(0, 0, 0, $today_m, 15, $today_y)); //$next_m.'/'.'0'.'/'.$today_y;
	$_SESSION['payperiod_option_6'] = date("m/d/Y",mktime(0, 0, 0, $next_m, 0, $today_y)); //$next_m.'/'.'15'.'/'.$today_y;

}else{

	$_SESSION['default_payperiod'] = date("m/d/Y",mktime(0, 0, 0, $today_m, 15, $today_y)); //$today_m.'/'.'15'.'/'.$today_y;
	
	$_SESSION['payperiod_option_1'] = date("m/d/Y",mktime(0, 0, 0, $prev_m, 0, $today_y)); //$prev_m.'/'.'0'.'/'.$today_y;
	$_SESSION['payperiod_option_2'] = date("m/d/Y",mktime(0, 0, 0, $prev_m, 15, $today_y)); //$prev_m.'/'.'15'.'/'.$today_y;
	$_SESSION['payperiod_option_3'] = date("m/d/Y",mktime(0, 0, 0, $today_m, 0, $today_y)); //$today_m.'/'.'0'.'/'.$today_y;
	$_SESSION['payperiod_option_4'] = $_SESSION['default_payperiod'];
	$_SESSION['payperiod_option_5'] = date("m/d/Y",mktime(0, 0, 0, $next_m, 0, $today_y)); //$next_m.'/'.'0'.'/'.$today_y;
	$_SESSION['payperiod_option_6'] = date("m/d/Y",mktime(0, 0, 0, $next_m, 15, $today_y)); //$next_m.'/'.'15'.'/'.$today_y;

}
 
######################################################################
## END: GENERATE DEFAULT PAY PERIOD & VALUE LIST FOR DROP DOWN MENU ##
######################################################################

####################################################################
## START: FIND STAFF LEAVE REQUESTS FOR THIS PAY PERIOD ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','leave_requests_subset','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);

if($_SESSION['view_payperiod'] == 'Other...'){
$search -> AddDBParam('pay_period_end',$_SESSION['payperiod_option_6'],'gt'); // FIND LEAVE REQUESTS FROM FUTURE PAY PERIODS
$_SESSION['payperiod_selected'] = 'Other...';

} elseif($_SESSION['view_payperiod'] != '') {
$search -> AddDBParam('pay_period_end',$_SESSION['view_payperiod']); // FIND LEAVE REQUESTS FROM THE SELECTED PAY PERIOD
$_SESSION['payperiod_selected'] = $_SESSION['view_payperiod'];

} else {
$search -> AddDBParam('pay_period_end',$_SESSION['default_payperiod']); // FIND LEAVE REQUESTS FROM THE DEFAULT PAY PERIOD
$_SESSION['payperiod_selected'] = $_SESSION['default_payperiod'];
}

//$search -> AddDBParam('signer_ID_pba','=='.$_SESSION['user_ID']); // FROM STAFF IN THIS BUDGET AUTHORITY'S WORKGROUP
//$search -> AddDBParam('signer_status_owner','1'); // ONLY LEAVE REQUESTS THAT HAVE ACTUALLY BEEN SIGNED BY STAFF


//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('approval_status','descend');
//$search -> AddSortParam('signer_status_imm_spvsr','ascend');
$search -> AddSortParam('leave_requests_staff_byStaffID::name_timesheet','ascend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['timesheet_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
##################################################################
## END: FIND STAFF LEAVE REQUESTS FOR THIS PAY PERIOD ##
##################################################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: OFTS Leave Request Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">
//<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>


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

function baMessage() { 
	var answer = confirm ("This leave request has not been submitted.")
	return false;
	
}

</script>


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Leave Requests: OFTS Admin</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b>Pay Period Ending: <?php echo $_SESSION['payperiod_selected'];?></b> | <i>Lockout Date: <?php echo $recordData['timesheets::c_PayPeriodLockOutDate'][0];?></i></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
<form name="form1" method="post">			
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="ebebeb" bgcolor="ffffff" width="100%" class="sims">
						
						<tr>
							<td colspan="4" class="body" bgcolor="ebebeb">Staff Leave Requests (<?php echo $searchResult['foundCount']; ?>)</td>
							<td colspan="4" class="body" bgcolor="ebebeb" align="right">Pay Period Ending: 
								<select name="menu1" onChange="MM_jumpMenu('parent',this,0)">
									<option value="menu_leave_ofts_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_1'];?>"<?php if($_SESSION['payperiod_option_1'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_1'];?></option>
									<option value="menu_leave_ofts_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_2'];?>"<?php if($_SESSION['payperiod_option_2'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_2'];?></option>
									<option value="menu_leave_ofts_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_3'];?>"<?php if($_SESSION['payperiod_option_3'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_3'];?></option>
									<option value="menu_leave_ofts_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_4'];?>"<?php if($_SESSION['payperiod_option_4'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_4'];?></option>
									<option value="menu_leave_ofts_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_5'];?>"<?php if($_SESSION['payperiod_option_5'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_5'];?></option>
									<option value="menu_leave_ofts_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_6'];?>"<?php if($_SESSION['payperiod_option_6'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_6'];?></option>
									<option value="menu_leave_ofts_admin.php?view_payperiod=Other..."<?php if($_SESSION['payperiod_selected'] == 'Other...'){echo 'selected';}?>>Other...</option>
								 </select>

                     
						</td></tr>
</form>						
						
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body">Name</td>
						<td class="body">Date(s) of Leave</td>
						<td class="body" align="right">Total Hrs</td>


						<td class="body">Date/Time Submitted</td>
						<td class="body" align="right" nowrap>Supervisor Approval</td>
						<td class="body" align="right">Status</td>

						</tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr <?php if($searchData['approval_status'][0] == 'Approved'){ ?> bgcolor="#D2E3FA"<?php }elseif($searchData['approval_status'][0] == 'Pending'){?> bgcolor="#F5FAD2"<?php }elseif($searchData['approval_status'][0] == 'Revised'){?> bgcolor="#FADED2"<?php }?>>
						<td class="body"><?php echo $searchData['leave_request_ID'][0];?></td>
						<td class="body"><a href="/staff/sims/leave_request_ofts.php?leave_request_ID=<?php echo $searchData['leave_request_ID'][0];?>&action=view&payperiod=<?php echo $searchData['pay_period_end'][0];?>"><?php echo $searchData['leave_requests_staff_byStaffID::name_timesheet'][0];?></a></td>

						<td class="body"><?php if($searchData['c_leave_hrs_begin_date'][0] != $searchData['c_leave_hrs_end_date'][0]){echo $searchData['c_leave_hrs_begin_date'][0].' - '.$searchData['c_leave_hrs_end_date'][0];} else { echo $searchData['c_leave_hrs_begin_date'][0];}?></td>
						<td class="body" align="right"><?php echo $searchData['c_total_request_hrs'][0];?></td>

						<td class="body"><?php echo $searchData['signer_timestamp_owner'][0];?></td>
						
						
						<?php if($searchData['signer_status_imm_spvsr'][0]=='1'){ ?>
						<td class="body" align="right"><font color="blue">Approved</font></td>
						
						<?php }else{ ?>
						<td class="body" align="right"><font color="red">Pending</font></td>
						
						<?php } ?>


						<?php if($searchData['approval_status'][0]=='Approved'){ ?>
						<td class="body" align="right"><font color="blue"><?php echo $searchData['approval_status'][0];?></font></td>
						
						<?php }else{ ?>
						<td class="body" align="right"><font color="red"><?php echo $searchData['approval_status'][0];?></font></td>
						
						<?php } ?>
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
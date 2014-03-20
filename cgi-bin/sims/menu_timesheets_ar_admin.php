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


//$_SESSION['default_payperiod'] = '6/30/2007'; //TEMP
//echo 'Default PayPeriod: '.$_SESSION['default_payperiod']; //TEMP
################################################################
## START: FIND WORKGROUP TIMESHEETS FOR THIS BUDGET AUTHORITY ##
################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','timesheets_subset','all');
$search -> SetDBPassword($webPW,$webUN);
if($_SESSION['view_payperiod'] != ''){
$search -> AddDBParam('c_PayPeriodEnd',$_SESSION['view_payperiod']); //FIND TIMESHEETS FROM THE SELECTED PAY PERIOD
$_SESSION['payperiod_selected'] = $_SESSION['view_payperiod'];
} else {
$search -> AddDBParam('c_PayPeriodEnd',$_SESSION['default_payperiod']); //FIND TIMESHEETS FROM THE DEFAULT PAY PERIOD
$_SESSION['payperiod_selected'] = $_SESSION['default_payperiod'];
}

//$search -> AddDBParam('staff_primary_workgroup',$_SESSION['workgroup']); //FROM STAFF IN THIS BUDGET AUTHORITY'S WORKGROUP
$search -> AddDBParam('staff::time_leave_admin_sims_user_ID',$_SESSION['user_ID']); //FROM STAFF ASSIGNED TO THIS AUTH REP
//$search -> AddDBParam('approved_by_auth_rep',$_SESSION['user_ID']); //FROM STAFF ASSIGNED TO THIS AUTH REP
//$search -> AddDBParam('StaffPrimaryBudgetAuthority',$_SESSION['primary_bgt_auth']); //FROM STAFF IN THIS BUDGET AUTHORITY'S WORKGROUP
//$search -> AddDBParam('sims_user_ID',$_SESSION['primary_bgt_auth']); //INCLUDING THE BUDGET AUTHORITY'S OWN TIMESHEET

$search -> AddSortParam('TimesheetSubmittedStatus','descend');
$search -> AddSortParam('TimeSheetName','ascend');

$searchResult = $search -> FMFind();

//echo '<p>ErrorCode (workgroup): '.$searchResult['errorCode'];
//echo '<p>FoundCount (workgroup): '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
##############################################################
## END: FIND WORKGROUP TIMESHEETS FOR THIS BUDGET AUTHORITY ##
##############################################################
/*
############################################################
## START: FIND OTHER TIMESHEETS FOR THIS BUDGET AUTHORITY ##
############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','timesheets_subset','all');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('c_PayPeriodEnd',$_SESSION['payperiod_selected']); //FIND TIMESHEETS FROM THE DEFAULT PAY PERIOD
$search2 -> AddDBParam('time_hrs::BudgetAuthorityLocal',$_SESSION['user_ID']); //WITH HRS CHARGED TO THIS BUDGET AUTHORITY
$search2 -> AddDBParam('sims_user_ID',$_SESSION['user_ID'],'neq'); //EXCLUDING THIS BUDGET AUTHORITY'S OWN TIMESHEET

//$search2 -> AddDBParam('staff_primary_workgroup',$_SESSION['workgroup'],'neq'); //THAT ARE NOT FROM THIS BUDGET AUTHORITY'S WORKGROUP
//$search -> AddDBParam('StaffPrimaryBudgetAuthority',$_SESSION['user_ID'],'neq'); //THAT ARE NOT OF THIS PRIMARY BUDGET AUTHORITY

$search2 -> AddSortParam('TimesheetSubmittedStatus','descend');
$search2 -> AddSortParam('TimeSheetName','ascend');

$searchResult2 = $search2 -> FMFind();

//echo '<p>ErrorCode (non-workgroup): '.$searchResult2['errorCode'];
//echo '<p>FoundCount (non-workgroup): '.$searchResult2['foundCount'];
//print_r ($searchResult);
$recordData2 = current($searchResult2['data']);
##########################################################
## END: FIND OTHER TIMESHEETS FOR THIS BUDGET AUTHORITY ##
##########################################################
*/

//echo '<p>$_SESSION[user_ID]: '.$_SESSION['user_ID'];




?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: <?php echo $_SESSION['workgroup'];?> Timesheets</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">
//<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">


<!--BEGIN FIRST SECTION: WORKGROUP TIMESHEETS-->

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Timesheets: Workgroup Time/Leave Admin</h1><hr /></td></tr>
			<tr><td class="body" nowrap><strong>Workgroup Admin: <?php echo $_SESSION['staff_name'];?></strong> | Budget Authority: <?php echo $_SESSION['primary_bgt_auth'];?> (<?php echo $_SESSION['workgroup'];?>)</td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td colspan="2">
<form name="form1" method="post" action="">
						<table cellpadding=4 cellspacing=0 width="100%" class="sims">
						<tr>
							<td colspan="3" class="body" bgcolor="ebebeb"><?php echo $_SESSION['workgroup'];?> Timesheets Requiring Verification (<?php echo $searchResult['foundCount']; ?>)</td>
							<td colspan="5" class="body" bgcolor="ebebeb" align="right">Pay Period Ending: 
								<select name="menu1" onChange="MM_jumpMenu('parent',this,0)">
									<option value="menu_timesheets_ar_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_1'];?>"<?php if($_SESSION['payperiod_option_1'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_1'];?></option>
									<option value="menu_timesheets_ar_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_2'];?>"<?php if($_SESSION['payperiod_option_2'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_2'];?></option>
									<option value="menu_timesheets_ar_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_3'];?>"<?php if($_SESSION['payperiod_option_3'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_3'];?></option>
									<option value="menu_timesheets_ar_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_4'];?>"<?php if($_SESSION['payperiod_option_4'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_4'];?></option>
									<option value="menu_timesheets_ar_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_5'];?>"<?php if($_SESSION['payperiod_option_5'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_5'];?></option>
									<option value="menu_timesheets_ar_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_6'];?>"<?php if($_SESSION['payperiod_option_6'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_6'];?></option>
								 </select>

                     
						</td></tr>
</form>						
						<tr bgcolor="#a2c7ca">
						
						<td class="body">Name</td>
						<td class="body" align="right">Total Hrs</td>
						<td class="body" align="right">Sick Hrs</td>
						<td class="body" align="right">Vac Hrs</td>
						<td class="body" align="right">Pers Hrs</td>
						<td class="body">Verified by</td>
						<td class="body" align="right">Verification Status</td>
						<td class="body" align="right">Timesheet Status</td>
						
						
						<?php if($searchResult['foundCount'] > 0){
						
								$i = 1;
								$n = 0;
								foreach($searchResult['data'] as $key => $searchData) { //IF THERE ARE WORKGROUP TIMESHEETS FOR THIS PAY PERIOD
								$workgroup_array[$n] = $searchData['TimesheetID'][0]; //SET TIMESHEET IDs INTO AN ARRAY ?>
								<tr <?php if($searchData['TimesheetSubmittedStatus'][0] == 'Approved'){ ?> bgcolor="#D2E3FA"<?php }elseif($searchData['TimesheetSubmittedStatus'][0] == 'Pending'){?> bgcolor="#F5FAD2"<?php }elseif($searchData['TimesheetSubmittedStatus'][0] == 'Revised'){?> bgcolor="#FADED2"<?php }?>>
								<td class="body"><?php echo $i.'. ';?><a href="/staff/sims/timesheets_approve.php?Timesheet_ID=<?php echo $searchData['TimesheetID'][0];?>&approver_ID=<?php echo $_SESSION['user_ID'];?>&src=intr"><?php echo $searchData['TimeSheetName'][0];?></a></td>
								<td class="body" align="right" bgcolor="ebebeb"><?php if($searchData['c_total_WkHrsReg'][0] == ''){echo '&nbsp;';}else{echo $searchData['c_total_WkHrsReg'][0];}?></td>
								<td class="body" align="right"><?php if($searchData['c_total_LvHrsSick'][0] == ''){echo '&nbsp;';}else{echo $searchData['c_total_LvHrsSick'][0];}?></td>
								<td class="body" align="right"><?php if($searchData['c_total_LvHrsVac'][0] == ''){echo '&nbsp;';}else{echo $searchData['c_total_LvHrsVac'][0];}?></td>
								<td class="body" align="right"><?php if($searchData['c_total_LvHrsPers'][0] == ''){echo '&nbsp;';}else{echo $searchData['c_total_LvHrsPers'][0];}?></td>
								<td class="body"><?php if($searchData['approved_by_auth_rep'][0] == ''){echo '&nbsp;';}else{echo $searchData['approved_by_auth_rep'][0];}?></td>
								<td class="body" align="right"><?php if($searchData['approved_by_auth_rep_status'][0] == '1'){echo '<font color="green">Approved</font>';}else{echo '<font color="red">Pending</font>';}?></td>
								<td class="body" align="right"><?php if($searchData['TimesheetSubmittedStatus'][0] == 'Approved'){echo '<font color="blue">'.$searchData['TimesheetSubmittedStatus'][0].'</font>'; } else { echo '<font color="red">'.$searchData['TimesheetSubmittedStatus'][0].'</font>'; }?></td>
								</tr>
					
								<?php $i++; $n++;
								
								} ?>
						
						<?php } else { //IF THERE ARE NO WORKGROUP TIMESHEETS FOR THIS PAY PERIOD 
								$workgroup_array[0] = ''; //INSTANTIATE THE ARRAY USED IN THE SECOND SEARCH IF NO RECORDS FOUND IN THE FIRST SEARCH
						?>
						
								<tr><td class="body" colspan="8" align="center" height="50"><b>No timesheets found.</b></td></tr>						
						
						<?php } ?>
						
						
						</table>
						
						
						
<!--END FIRST SECTION: WORKGROUP TIMESHEETS-->



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
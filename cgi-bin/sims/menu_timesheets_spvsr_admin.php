<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');

$_SESSION['menu_type'] == 'spvsr_admin';

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
## START: FIND WORKGROUP TIMESHEETS FOR THIS SUPERVISOR ##
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

//$search -> AddDBParam('staff_primary_workgroup',$_SESSION['workgroup']); //FROM STAFF IN THIS SUPERVISOR'S WORKGROUP
$search -> AddDBParam('c_spvsr_PBA_multikey',$_SESSION['user_ID']); //FROM STAFF THIS MANAGER SUPERVISES OR IS PBA FOR
$search -> AddDBParam('sims_user_ID',$_SESSION['user_ID'],'neq'); //EXCLUDING THIS SUPERVISOR'S OWN TIMESHEET


$search -> AddSortParam('TimesheetSubmittedStatus','descend');
$search -> AddSortParam('TimeSheetName','ascend');

$searchResult = $search -> FMFind();

//echo '<p>ErrorCode (workgroup): '.$searchResult['errorCode'];
//echo '<p>FoundCount (workgroup): '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
##############################################################
## END: FIND WORKGROUP TIMESHEETS FOR THIS SUPERVISOR ##
##############################################################

############################################################
## START: FIND OTHER TIMESHEETS FOR THIS SUPERVISOR ##
############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','timesheets_subset','all');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('c_PayPeriodEnd',$_SESSION['payperiod_selected']); //FIND TIMESHEETS FROM THE DEFAULT PAY PERIOD
$search2 -> AddDBParam('time_hrs::BudgetAuthorityLocal',$_SESSION['user_ID']); //WITH HRS CHARGED TO THIS BUDGET AUTHORITY
$search2 -> AddDBParam('sims_user_ID',$_SESSION['user_ID'],'neq'); //EXCLUDING THIS BUDGET AUTHORITY'S OWN TIMESHEET

//$search2 -> AddDBParam('staff_primary_workgroup',$_SESSION['workgroup'],'neq'); //THAT ARE NOT FROM THIS SUPERVISOR'S WORKGROUP
//$search -> AddDBParam('StaffPrimaryBudgetAuthority',$_SESSION['user_ID'],'neq'); //THAT ARE NOT OF THIS PRIMARY BUDGET AUTHORITY

$search2 -> AddSortParam('TimesheetSubmittedStatus','descend');
$search2 -> AddSortParam('TimeSheetName','ascend');

$searchResult2 = $search2 -> FMFind();

//echo '<p>ErrorCode (non-workgroup): '.$searchResult2['errorCode'];
//echo '<p>FoundCount (non-workgroup): '.$searchResult2['foundCount'];
//print_r ($searchResult);
$recordData2 = current($searchResult2['data']);
##########################################################
## END: FIND OTHER TIMESHEETS FOR THIS SUPERVISOR ##
##########################################################






?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: Timesheets - Supervisor Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">
//<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->

function baMessage() { 
	var answer = confirm ("This timesheet has not been submitted.")
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Timesheets: Supervisor Admin</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b>Supervisor: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td colspan="2">
<form name="form1" method="post" action="">
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="ebebeb" bgcolor="ffffff" width="100%" class="sims">
						<tr>
							<td colspan="3" class="body" bgcolor="ebebeb">Timesheets Requiring Your Approval (<?php echo $searchResult['foundCount']; ?>)</td>
							<td colspan="5" class="body" bgcolor="ebebeb" align="right">Pay Period Ending: 
								<select name="menu1" onChange="MM_jumpMenu('parent',this,0)">
									<option value="menu_timesheets_spvsr_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_1'];?>"<?php if($_SESSION['payperiod_option_1'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_1'];?></option>
									<option value="menu_timesheets_spvsr_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_2'];?>"<?php if($_SESSION['payperiod_option_2'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_2'];?></option>
									<option value="menu_timesheets_spvsr_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_3'];?>"<?php if($_SESSION['payperiod_option_3'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_3'];?></option>
									<option value="menu_timesheets_spvsr_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_4'];?>"<?php if($_SESSION['payperiod_option_4'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_4'];?></option>
									<option value="menu_timesheets_spvsr_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_5'];?>"<?php if($_SESSION['payperiod_option_5'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_5'];?></option>
									<option value="menu_timesheets_spvsr_admin.php?view_payperiod=<?php echo $_SESSION['payperiod_option_6'];?>"<?php if($_SESSION['payperiod_option_6'] == $_SESSION['payperiod_selected']){echo 'selected';}?>><?php echo $_SESSION['payperiod_option_6'];?></option>
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
						<td class="body">Required Signatures</td>
						<td class="body" align="right">Status</td>
						
						
						<?php if($searchResult['foundCount'] > 0){
						
								$i = 1;
								$n = 0;
								foreach($searchResult['data'] as $key => $searchData) { //IF THERE ARE WORKGROUP TIMESHEETS FOR THIS PAY PERIOD
								$workgroup_array[$n] = $searchData['TimesheetID'][0]; //SET TIMESHEET IDs INTO AN ARRAY ?>
								<tr>
								<td class="body"><?php echo $i.'. ';?><a href="/staff/sims/timesheets_spvsr_app.php?Timesheet_ID=<?php echo $searchData['TimesheetID'][0];?>&action=view&src=menu&payperiod=<?php echo $searchData['c_PayPeriodEnd'][0];?>"<?php if($searchData['TimesheetSubmittedStatus'][0] == 'Not Submitted'){echo ' onClick="return baMessage()"';}?>><?php echo $searchData['TimeSheetName'][0];?></a></td>
								<td class="body" align="right" bgcolor="ebebeb"><?php if($searchData['c_total_WkHrsReg'][0] == ''){echo '&nbsp;';}else{echo $searchData['c_total_WkHrsReg'][0];}?></td>
								<td class="body" align="right"><?php if($searchData['c_total_LvHrsSick'][0] == ''){echo '&nbsp;';}else{echo $searchData['c_total_LvHrsSick'][0];}?></td>
								<td class="body" align="right"><?php if($searchData['c_total_LvHrsVac'][0] == ''){echo '&nbsp;';}else{echo $searchData['c_total_LvHrsVac'][0];}?></td>
								<td class="body" align="right"><?php if($searchData['c_total_LvHrsPers'][0] == ''){echo '&nbsp;';}else{echo $searchData['c_total_LvHrsPers'][0];}?></td>
								<td class="body"><?php if($searchData['approved_by_auth_rep'][0] == ''){echo '&nbsp;';}else{echo $searchData['approved_by_auth_rep'][0];}?></td>
								<td class="body"><?php if($searchData['signatures_required'][0] == ''){echo '&nbsp;';}else{echo $searchData['signatures_required'][0];}?></td>
								<td class="body" align="right"><?php if($searchData['TimesheetSubmittedStatus'][0] == 'Approved'){echo '<font color="blue">'.$searchData['TimesheetSubmittedStatus'][0].'</font>'; } elseif($searchData['Signer_status_imm_spvsr'][0] == '1'){ echo '<font color="blue">Approved</font>';  } else { echo '<font color="red">'.$searchData['TimesheetSubmittedStatus'][0].'</font>'; }?></td>
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

<?php if($searchResult2['foundCount'] > 0){ ?>
<!--BEGIN SECOND SECTION: OTHER TIMESHEETS-->						
						
						
						<table cellpadding=4 cellspacing=0 width="100%" class="sims">
						<tr height="50" valign="bottom">
							<td colspan="3" class="body" bgcolor="ebebeb">Other Timesheets Requiring Your Approval (time charged)</td>
							<td colspan="2" class="body" bgcolor="ebebeb" align="right">Pay Period Ending: <?php echo $_SESSION['payperiod_selected'];?>                     
						</td></tr>
						
						<tr bgcolor="#a2c7ca">
						
						<td class="body" nowrap>Name</td>
						<td class="body" nowrap>Workgroup</td>
						<td class="body" nowrap>Supervisor</td>
						<td class="body" nowrap size="100%">Required Signatures</td>
						<td class="body" nowrap align="right">Status</td>
						
						
						<?php if($searchResult2['foundCount'] > 0){ //IF THERE ARE OTHER TIMESHEETS FOR THIS PAY PERIOD FOR THIS BUDGET AUTHORITY TO SIGN
								$i = 1;
								$workgroup_list = implode('<br />',$workgroup_array); //PUT WORKGROUP TIMESHEET ID ARRAY INTO A STRING FOR CHECKING AGAINST
								foreach($searchResult2['data'] as $key => $searchData2) { 

									if(strpos($workgroup_list,$searchData2['TimesheetID'][0]) === false){ //IF THE CURRENT TIMESHEET IS NOT ALREADY LISTED IN THE FIRST FOUND SET OF WORKGROUP TIMESHEETS ?>

										<tr>
										<td class="body"><?php echo $i.'. ';?><a href="/staff/sims/timesheets_ba_app.php?Timesheet_ID=<?php echo $searchData2['TimesheetID'][0];?>&action=view&src=menu&payperiod=<?php echo $searchData2['c_PayPeriodEnd'][0];?>"<?php if($searchData2['TimesheetSubmittedStatus'][0] == 'Not Submitted'){echo ' onClick="return baMessage()"';}?>><?php echo $searchData2['TimeSheetName'][0];?></a></td>
										<td class="body"><?php echo $searchData2['staff_primary_workgroup'][0];?></td>
										<td class="body"><?php echo $searchData2['StaffImmediateSupervisor'][0];?></td>
										<td class="body" size="100%" nowrap><?php echo $searchData2['signatures_required'][0];?></td>
										<td class="body" align="right">
										
										
										<?php if(strpos($searchData2['c_signatures_received'][0],$_SESSION['user_ID']) === false){	//IF THE CURRENT TIMESHEET HAS NOT BEEN APPROVED BY THE CURRENT BUDGET AUTHORITY ?>
										
										<font color="red"><?php echo $searchData2['TimesheetSubmittedStatus'][0];?></font>
										
										<?php } else { ?>
										
										<font color="blue">Approved</font>
										
										<?php
										/*
										$search = new FX($serverIP,$webCompanionPort);
										$search -> SetDBData('SIMS_2.fp7','time_hrs');
										$search -> SetDBPassword($webPW,$webUN);
										$search -> AddDBParam('Timesheet_ID','=='.$searchData2['TimesheetID'][0]);
										$search -> AddDBParam('BudgetAuthorityLocal',$_SESSION['user_ID']);
										
										$searchResult = $search -> FMFind();
										
										//echo $searchResult['errorCode'];
										//echo $searchResult['foundCount'];
										$recordData3 = current($searchResult['data']);
										//echo '<br>hello';
										*/
										?>



										<?php } ?>
										
										</td></tr>

									<?php $i++; } ?>
									
									

								<?php  
								} 
								
								if($i == 1){ //IF THERE ARE NO OTHER TIMESHEETS FOR THIS PAY PERIOD FOR THIS BUDGET AUTHORITY TO SIGN ?>

										<tr><td class="body" colspan="8" align="center" height="50"><b>No timesheets found.</b></td></tr>

									<?php $i++; } 
								
								?>
								
						<?php } else { //IF THERE ARE NO OTHER TIMESHEETS FOR THIS PAY PERIOD FOR THIS BUDGET AUTHORITY TO SIGN ?>
						
								<tr><td class="body" colspan="8" align="center" height="50"><b>No timesheets found.</b></td></tr>						
						
						<?php } ?>
								
								
						</table>

<!--END SECOND SECTION: OTHER TIMESHEETS-->
<?php } ?>
						
						
						

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
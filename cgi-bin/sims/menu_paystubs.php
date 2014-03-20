<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

$debug = 'off';
$paystub_access = 'yes';
$today = date("m/d/Y");
//$today = '10/15/2008';
//echo '<p>$today: '.$today;
//echo '<p>$_SESSION[staff_ID]: '.$_SESSION['staff_ID'];
//$sortfield = $_GET['sortfield'];
$displaynum = $_GET['displaynum'];
if($displaynum == ''){
$displaynum = 12;
}

include_once('FX/FX.php');
include_once('FX/server_data.php');

#########################################
## START: FIND PAYSTUBS FOR THIS USER
#########################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','paystubs',$displaynum);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$_SESSION['staff_ID']);
//$search -> AddDBParam('c_periodend_local',$today,'lte');
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('paystub_ID','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$_SESSION['timesheet_foundcount'] = $searchResult['foundCount'];
$recordData = current($searchResult['data']);



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
if ($paystub_access == 'yes') { //IF TIMESHEETS ACCESS IS TURNED ON 
?>

<html>
<head>
<title>SIMS: My Paystubs</title>
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Paystubs</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			<?php if($_SESSION['timesheet_signed_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your timesheet has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>
			
			<?php } ?>
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body">Pay Period Ending</td>
						<td class="body" align="right">Gross</td>
						<td class="body" align="right">Health</td>
						<td class="body" align="right">Dental</td>
						<td class="body" align="right">Ret(E)</td>
						<td class="body" align="right">Ret(L)</td>
						<td class="body" align="right">Fed Tax With</td>
						<td class="body" align="right">SS Tax With</td>
						<td class="body" align="right">Med Tax With</td>
						<td class="body" align="right">State</td>
						<td class="body" align="right">Park</td>
						<td class="body" align="right">Net</td>
						
						</tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="body"><?php echo $searchData['paystub_ID'][0];?></td>
						<td class="body"><a href="/staff/sims/paystubs.php?paystub_ID=<?php echo $searchData['paystub_ID'][0];?>" title="Click here to view this paystub." target="_blank"><?php echo $searchData['paystubs_timesheets_byPeriodEnd::c_PayPeriodEnd'][0];?> (<?php echo substr($searchData['payroll_type'][0],0,1);?>)</a></td>
						<td class="body" align="right"><?php echo $searchData['CURGRPAY'][0];?></td>
						
						<td class="body" align="right"><?php if($searchData['CURPTHEAL'][0] > 0){echo $searchData['CURPTHEAL'][0];}elseif($searchData['CURPTPRU'][0] > 0){echo $searchData['CURPTPRU'][0];}else{echo '0.00';}?></td>
						<td class="body" align="right"><?php echo $searchData['CURPTDENT'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURRET02'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['LABRET14'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURFWTAX'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURSSTAX'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURMDTAX'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURSTTAX'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURPARK'][0];?></td>
						
						<td class="body" align="right"><?php echo $searchData['CURNETPAY'][0];?></td>
						
						</tr>
			
						<?php } ?>
						<tr><td colspan="10" style="background-color:#fbf59a"><a href="menu_paystubs.php?displaynum=<?php echo $displaynum + 12;?>">Show more</a><?php if($displaynum > 12){?> | <a href="menu_paystubs.php?displaynum=<?php echo $displaynum - 12;?>">Show less</a><?php }?></td><td colspan="3" align="right" style="background-color:#000000;text-align:center;color:#ffffff">Showing <?php echo $displaynum;?> records</td></tr>


						</table>

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
						<td class="body">Pay Period Ending</td>
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
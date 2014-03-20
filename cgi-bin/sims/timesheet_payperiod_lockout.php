<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2008 by SEDL
#
# Written by Eric Waters 07/31/2008
#############################################################################

###############################
## START: LOAD FX.PHP INCLUDES
###############################
include_once('FX/FX.php');
include_once('FX/server_data.php');
###############################
## END: LOAD FX.PHP INCLUDES
###############################

###############################
## START: GRAB FORM VARIABLES
###############################
$action = $_GET['action'];
$update_row_ID = $_GET['update_row_ID'];
$lockout_date = $_GET['lockout_date'];
$new_lockout_date = $_GET['new_lockout_date'];
$pay_period = $_GET['pay_period'];

if(date("j", mktime(0, 0, 0, substr($pay_period,0,2), substr($pay_period,3,2), substr($pay_period,6,4))) == '15'){
$pay_period_type = '2';
}else{
$pay_period_type = '1';
}

$month_yr = date('n.Y', mktime(0, 0, 0, substr($pay_period,0,2), 1, substr($pay_period,6,4)));
//echo '<p>$month_yr: '.$month_yr;
//echo '<p>$pay_period: '.$pay_period;
//echo '<p>$pay_period_type: '.$pay_period_type;
//echo '<p>$update_row_ID: '.$update_row_ID;
###############################
## END: GRAB FORM VARIABLES
###############################

if($action == 'update'){
############################################################################
## START: UPDATE LOCKOUT DATE FOR THIS PAY PERIOD IF INDICATED
############################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheet_pay_periods');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row_ID);

if($pay_period_type == '1'){
$update -> AddDBParam('pay_period_lockout_date_exempt',$new_lockout_date);
}elseif($pay_period_type == '2'){
$update -> AddDBParam('pay_period_lockout_date_non_exempt_1',$new_lockout_date);
}

$updateResult = $update -> FMEdit();
if($updateResult['errorCode'] =='0'){
$_SESSION['timesheet_lockout_date_updated'] = '1';


	############################################################################
	## START: UPDATE ALL CURRENT TIMESHEETS WITH NEW PAY PERIOD LOCKOUT DATE
	############################################################################
	$search = new FX($serverIP,$webCompanionPort);
	$search -> SetDBData('SIMS_2.fp7','timesheets');
	$search -> SetDBPassword($webPW,$webUN);
	
	$search -> AddDBParam('c_PayPeriodEnd','=='.$pay_period);
	
	if($pay_period_type == '1'){
	$search -> AddDBParam('-script', 'update_payperiod_lockout_date_web_exempt'); // REPLACES LOCKOUT DATE ON ALL PAY PERIOD TIMESHEETS - (FMP SCRIPT)
	}elseif($pay_period_type == '2'){
	$search -> AddDBParam('-script', 'update_payperiod_lockout_date_web_non_exempt'); // REPLACES LOCKOUT DATE ON ALL PAY PERIOD TIMESHEETS - (FMP SCRIPT)
	}
	$searchResult = $search -> FMFind();
	
//	echo '<p>errorCode: '.$searchResult['errorCode'];
//	echo '<p>foundCount: '.$searchResult['foundCount'];
	//$searchData = current($searchResult['data']);
	//echo $searchData['timesheet_prefs_show_nicknames'][0];
	
	############################################################################
	## END: UPDATE ALL CURRENT TIMESHEETS WITH NEW PAY PERIOD LOCKOUT DATE
	############################################################################


header('Location: http://www.sedl.org/staff/sims/menu_timesheets_ofts_admin.php');
exit;
}

}

##########################################################################
## END: UPDATE LOCKOUT DATE FOR THIS PAY PERIOD IF INDICATED
##########################################################################

############################################################################
## START: FIND LOCKOUT DATE FOR THIS PAY PERIOD
############################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','timesheet_pay_periods');
$search -> SetDBPassword($webPW,$webUN);

$search -> AddDBParam('c_pay_period_month_yr_key','=='.$month_yr);

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$searchData = current($searchResult['data']);
//echo $searchData['timesheet_prefs_show_nicknames'][0];
############################################################################
## END: FIND LOCKOUT DATE FOR THIS PAY PERIOD
############################################################################

//echo '<p>row_ID: '.$searchData['c_cwp_row_ID'][0];
//echo '<p>$pay_period_type: '.$pay_period_type;

#################################################################################################
## START: DISPLAY THE LOCKOUT DATE FOR THIS PAY PERIOD
#################################################################################################
?>


<html>
<head>
<title>SIMS - Timesheet Preferences</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="300" cellpadding="0" cellspacing="0" border="1" bordercolor="#003745" align="center">
<tr bgcolor="#003745"><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
			
			
			<tr bgcolor="#a2c7ca"><td class="body" nowrap><strong>Set Lockout Date for Pay Period: <?php echo $pay_period;?></strong></td></tr>
			
			
			
			<tr><td class="body" colspan=2>
			<form name="timesheet_prefs">
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="update_row_ID" value="<?php echo $searchData['c_cwp_row_ID'][0];?>">
			<input type="hidden" name="pay_period_type" value="<?php echo $pay_period_type;?>">
			<input type="hidden" name="pay_period" value="<?php echo $pay_period;?>">
			
			Current lock-out date: <strong><?php echo $lockout_date;?></strong><p>					
			New lock-out date: <input type="text" name="new_lockout_date" value="<?php echo $lockout_date;?>">					


			<p>
			<input type="button" value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Submit">
			</form>
		

			
			</td></tr>
			
			
			<tr><td class="body" colspan="2">
			</td></tr>
			
			
			</table>

</td></tr>
</table>






</body>

</html>
<?php
#################################################################################################
## END: DISPLAY THE LOCKOUT DATE FOR THIS PAY PERIOD
#################################################################################################
?>
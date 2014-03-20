<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2008 by SEDL
#
# Written by Eric Waters 03/24/2008
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
$timesheet_prefs_show_nicknames = $_GET['timesheet_prefs_show_nicknames'];
$timesheet_prefs_hide_weekends = $_GET['timesheet_prefs_hide_weekends'];
$timesheet_prefs_allow_blank_rows = $_GET['timesheet_prefs_allow_blank_rows'];
###############################
## END: GRAB FORM VARIABLES
###############################

if($action == 'update'){
$_SESSION['timesheet_prefs_updated'] = '1';
############################################################################
## START: UPDATE TIMESHEET PREFERENCES FOR THIS STAFF MEMBER IF INDICATED
############################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','staff');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row_ID);
$update -> AddDBParam('timesheet_prefs_show_nicknames',$timesheet_prefs_show_nicknames);
$update -> AddDBParam('timesheet_prefs_hide_weekends',$timesheet_prefs_hide_weekends);
$update -> AddDBParam('timesheet_prefs_allow_blank_rows',$timesheet_prefs_allow_blank_rows);

$updateResult = $update -> FMEdit();
##########################################################################
## END: UPDATE TIMESHEET PREFERENCES FOR THIS STAFF MEMBER IF INDICATED
##########################################################################
if($updateResult['errorCode'] =='0'){
$_SESSION['timesheet_prefs_updated'] = '1';
header('Location: http://www.sedl.org/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd');
exit;
}

}

############################################################################
## START: FIND TIMESHEET PREFERENCES FOR THIS STAFF MEMBER
############################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff');
$search2 -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search2 -> AddDBParam('staff_ID','=='.$_SESSION['staff_ID']);
//$search2 -> AddDBParam('Active_To',$active_to);
//$search2 -> AddDBParam('-lop','or');

//$search2 -> AddSortParam ('c_BudgetCode','ascend');


$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
$searchData2 = current($searchResult2['data']);
//echo $searchData2['timesheet_prefs_show_nicknames'][0];
############################################################################
## END: FIND TIMESHEET PREFERENCES FOR THIS STAFF MEMBER
############################################################################

#################################################################################################
## START: DISPLAY THE TIMESHEET PREFERENCES FOR THIS STAFF MEMBER
#################################################################################################
?>


<html>
<head>
<title>SIMS - Timesheet Preferences</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="400" cellpadding="0" cellspacing="0" border="1" bordercolor="#003745" align="center">
<tr bgcolor="#003745"><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
			
			
			<tr bgcolor="#a2c7ca"><td class="body" nowrap><strong>Timesheet Preferences: <?php echo $searchData2['c_full_name_first_last'][0];?></strong></td></tr>
			
			
			
			<tr><td class="body" colspan=2>
			<form name="timesheet_prefs">
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="update_row_ID" value="<?php echo $searchData2['c_cwp_row_ID'][0];?>">
			
			<input type="checkbox" name="timesheet_prefs_show_nicknames" value="Yes" <?php if($searchData2['timesheet_prefs_show_nicknames'][0] == 'Yes'){echo 'CHECKED=CHECKED';}?>">	Show nicknames on timesheet	<br>					
			<input type="checkbox" name="timesheet_prefs_hide_weekends" value="Yes" <?php if($searchData2['timesheet_prefs_hide_weekends'][0] == 'Yes'){echo 'CHECKED=CHECKED';}?>">	Hide weekends on timesheet	<br>					
			<input type="checkbox" name="timesheet_prefs_allow_blank_rows" value="Yes" <?php if($searchData2['timesheet_prefs_allow_blank_rows'][0] == 'Yes'){echo 'CHECKED=CHECKED';}?>">	Allow blank rows on timesheet (all TS hours = Leave)						


			<p>
			<input type="button" value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Update Preferences">
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
## END: DISPLAY THE TIMESHEET PREFERENCES FOR THIS STAFF MEMBER
#################################################################################################
?>
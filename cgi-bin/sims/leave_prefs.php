<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2008 by SEDL
#
# Written by Eric Waters 11/11/2008
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
$leave_prefs_workgroups = $_GET['leave_prefs_workgroups'];

for($i=0 ; $i<count($leave_prefs_workgroups) ; $i++) {
$leave_prefs_workgroups_mod .= $leave_prefs_workgroups[$i]."\r"; 
}

###############################
## END: GRAB FORM VARIABLES
###############################

if($action == 'update'){
$_SESSION['leave_prefs_updated'] = '1';
############################################################################
## START: UPDATE LEAVE PREFERENCES FOR THIS STAFF MEMBER IF INDICATED
############################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','staff');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row_ID);
$update -> AddDBParam('leave_prefs_workgroups',$leave_prefs_workgroups_mod);

$updateResult = $update -> FMEdit();
##########################################################################
## END: UPDATE LEAVE PREFERENCES FOR THIS STAFF MEMBER IF INDICATED
##########################################################################
if($updateResult['errorCode'] =='0'){
$_SESSION['leave_prefs_updated'] = '1';
header('Location: http://www.sedl.org/staff/sims/menu_leave.php');
exit;
}else{
$_SESSION['leave_prefs_updated'] = '2';
header('Location: http://www.sedl.org/staff/sims/menu_leave.php');
exit;
}

}

############################################################################
## START: FIND LEAVE PREFERENCES FOR THIS STAFF MEMBER
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
## END: FIND LEAVE PREFERENCES FOR THIS STAFF MEMBER
############################################################################

################################
## START: GRAB FMP VALUELISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('SIMS_2.fp7','staff');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
##############################
## END: GRAB FMP VALUELISTS ##
##############################

#################################################################################################
## START: DISPLAY THE LEAVE PREFERENCES FOR THIS STAFF MEMBER
#################################################################################################
?>


<html>
<head>
<title>SIMS - Leave Preferences</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="300" cellpadding="0" cellspacing="0" border="1" bordercolor="#003745" align="center">
<tr bgcolor="#003745"><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
			
			
			<tr bgcolor="#a2c7ca"><td class="body" nowrap><strong>Leave Preferences: <?php echo $searchData2['c_full_name_first_last'][0];?></strong></td></tr>
			
			
			
			<tr><td class="body" colspan=2>
			<strong>Leave Calendar sharing</strong>: Indicate which workgroup leave calendars your leave should appear on.*<p>
			<form name="leave_prefs">
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="update_row_ID" value="<?php echo $searchData2['c_cwp_row_ID'][0];?>">
			


			<?php foreach($v1Result['valueLists']['sedl_workgroups'] as $key => $value) { 
			if($value != $_SESSION['workgroup']){
			?>
			<input type="checkbox" name="leave_prefs_workgroups[]" value="<?php echo $value;?>"<?php 
			if (strpos($searchData2['leave_prefs_workgroups'][0],$value) !== false) {
			echo ' checked="checked"';
			}
			?>>	<?php echo $value;?><br>
			<?php 
			}
			} ?>
			

			<p>
			<span class="tiny">*NOTE: Your leave will automatically appear on the <?php echo $_SESSION['workgroup'];?> leave calendar.</span><p>
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
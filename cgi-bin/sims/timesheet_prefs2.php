<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2013 by SEDL
#
# Written by Eric Waters September 2013
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
<title>SIMS: Timesheet Preferences</title>
<link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/grid.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />

<script type="text/javascript" src="js/mootools-1.2.1-core.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="js/mootools-fluid16-autoselect.js"></script>


</head>

<body>
<div class="container_16">

<?php include_once('http://www.sedl.org/staff/sims/includes/sims_header_2013.html');?>

<!--
###################################################################################
###################################################################################
############  BEGIN PAGE CONTENT  #################################################
###################################################################################
###################################################################################
-->
<div class="grid_16" style="position:relative">


<h2 id="page-heading">User Settings</h2>
</div>

<div class="clear"></div>


<div class="grid_8">




				<div class="box">
					<div class="block" id="forms">
						<form name="timesheet_prefs">
						<input type="hidden" name="action" value="update">
						<input type="hidden" name="update_row_ID" value="<?php echo $searchData2['c_cwp_row_ID'][0];?>">
							<fieldset class="login">
								<legend>Timsheet Preferences</legend>

								<p><input type="checkbox" name="timesheet_prefs_show_nicknames" value="Yes" style="width:10px" <?php if($searchData2['timesheet_prefs_show_nicknames'][0] == 'Yes'){echo 'CHECKED=CHECKED';}?>"> Show nicknames on timesheet</input></p>					
								<p><input type="checkbox" name="timesheet_prefs_hide_weekends" value="Yes" style="width:10px" <?php if($searchData2['timesheet_prefs_hide_weekends'][0] == 'Yes'){echo 'CHECKED=CHECKED';}?>"> Hide weekends on timesheet</input></p>					
								<p><input type="checkbox" name="timesheet_prefs_allow_blank_rows" value="Yes" style="width:10px" <?php if($searchData2['timesheet_prefs_allow_blank_rows'][0] == 'Yes'){echo 'CHECKED=CHECKED';}?>"> Allow blank rows on timesheet (all TS hours = Leave)</input></p>						
								<div nowrap style="border-top:1px dotted #999999;padding-top:6px;margin-left:15px;text-align:left"><input type="button" value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Update Preferences"></div>
							</fieldset>
						</form>
					</div>
				</div>
			</div>

</div><!--END grid_8-->
</div><!--END container_16-->

<div class="container_16" style="text-align:center"><hr style="padding:0px">For technical assistance contact <a href="mailto:sims@sed.org">sims@sedl.org</a>.
</div>
</body>

</html>
<?php
#################################################################################################
## END: DISPLAY THE TIMESHEET PREFERENCES FOR THIS STAFF MEMBER
#################################################################################################
?>
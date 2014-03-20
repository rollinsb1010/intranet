<?php
session_start();

?>

<html>
<head>
<title>SIMS: My Timesheets</title>
<link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/grid.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />
<link rel="stylesheet" type="text/css" href="pace/pace-theme-center-simple.css" />

<script type="text/javascript" src="js/mootools-1.2.1-core.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="js/mootools-fluid16-autoselect.js"></script>
<script type="text/javascript" src="/pace/pace.js"></script>

<script language="JavaScript">

function confirmDelete() { 
	var answer = confirm ("Are you sure you want to delete this row?")
	if (!answer) {
	return false;
	}
}

function confirmSign() { 
	var answer = confirm ("Sign this timesheet?")
	if (!answer) {
	return false;
	}
}

function confirmReSign() { 
	var answer = confirm ("Re-submit this timesheet?")
	if (!answer) {
	return false;
	}
}

function lockoutMessage() { 
	alert ("This timesheet is closed.")
	return false;
}

function resubmitReminder() { 
	var answer = confirm ("It appears this timesheet has been revised. Re-submit now?")
	if (!answer) {
	return false;
	}
}

function invalidHours() { 
	alert ("Total timesheet hours must equal hours in red.")
	return false;
}

function multipleLeavetypes() { 
	alert ("This timesheet contains multiple rows of the same leave type. The paid leave section must contain a single row for each leave type. Combine these rows before submitting timesheet.")
	return false;
}

function blankRows() { 
	alert ("Timesheet contains blank rows.")
	return false;
}

function oefUnsigned() { 
	alert ("Report of Outside Employment form must be signed before submitting timesheet.")
	return true;
}

function baMessage() { 
	alert ("This signature box is reserved for a budget authority. To sign this timesheet, click the box with your ID.")
	return false;
}



</script>

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

<div class="nav" style="color:#0033ff;background-color:#ffffff;float:right;margin:12px;padding:4px 6px 2px 6px" nowrap>

	<a href="/staff/sims/my_budget_codes.php" target="_blank" title="Click here to add budget codes to your budget code list.">My Budget Codes</a> | 
	<a href="/staff/sims/timesheets_rpt_out_empl.php?Timesheet_ID=<?php echo $_SESSION['timesheet_ID'];?>&action=view" title="Click here to view/sign your report of outside employment form.">Outside Employment Form</a> | 

	<?php if($recordData['timesheets::TimesheetBeingRevised'][0] == '1'){?>
	<a href="timesheets_process_revised.php?action=staff_sign&row_ID=<?php echo $recordData['timesheets::c_row_ID_cwp'][0];?>" onclick="return resubmitReminder()">Close Timesheet</a>
	<?php }else{?>
	<a href="/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd" title="Click here to close this timesheet and return to your timesheet list.">Close Timesheet</a>
	<?php } ?>

</div>

<h2 id="page-heading">My Timesheets</h2>
</div>

<div class="clear"></div>

<div class="grid_16" style="position:relative">
<div class="grid_8">
<h2 id="page-heading" style="font-size:14px;font-weight:bold;border:0px;padding:10px 0px 0px 0px"><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</h2>
</div>
<div class="grid_8">
<h2 id="page-heading" style="font-size:14px;border:0px;padding:10px 0px 0px 0px;text-align:right">Pay Period: <?php echo $recordData['timesheets::PayPeriodBegin'][0];?> - <?php echo $recordData['timesheets::c_PayPeriodEnd'][0];?> | Status: <?php echo $recordData['timesheets::TimesheetSubmittedStatus'][0];?></h2>
</div>

<!--BEGIN FIRST SECTION: REGULAR HOURS BY BUDGET CODE-->	
	<table>
	<tr><td class="section_head" colspan="<?php echo $header_colspan;?>"><strong>Regular Hours by Budget Code | <i>NOTE: Record Time to Nearest Tenth of an Hour</i></strong> <div style="float:right;text-align:right">Timesheet ID: <?php echo $_SESSION['timesheet_ID'];?></div></td></tr>
			<tr>
			<td class="slim_head" style="text-align:left">&nbsp;<strong>Budget Code</strong></td>
			<td class="slim_head"><strong>BA</strong></td>
			<td class="slim_head"><strong>T</strong></td>
			<td <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>01</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>02</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>03</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>04</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>05</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>06</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>07</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>08</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>09</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>10</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>11</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>12</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>13</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>14</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>15</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>16</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>17</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>18</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>19</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>20</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>21</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>22</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>23</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>24</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>25</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>26</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>27</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>28</strong>';}?></td>
	
			<?php if ($days_in_month > 28) { ?>
			<td <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>29</strong>';}?></td>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>30</strong>';}?></td>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>31</strong>';}?></td>
			<?php } ?>
			<td class="slim_head" colspan="2">&nbsp;</td>
			</tr>

			<?php 
			$i = 0;
			foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = time_hrs 
			$bgt_auths[$i] = $searchData['BudgetAuthorityLocal'][0]; // CREATE BGT_AUTHS ARRAY USED IN SIGNATURE BLOCK
			$i++;
			?>
			<tr>
			<td class="slim" style="text-align:left;padding-left:6px;padding-right:6px" nowrap><?php echo $searchData['BudgetCode'][0];?><?php if($_SESSION['timesheet_prefs_show_nicknames'] == 'Yes'){echo '<font color="#666666"><em> - '.stripslashes($searchData['time_hrs_budget_code_usage::Budget_Code_Nickname'][0]).'</font></em>';}?></td>
			<td class="slim"><?php echo $searchData['BudgetAuthorityCodeLocal'][0];?></td>
			<td class="slim" style="background-color:#e9f2ff"><strong><?php if($searchData['c_TotalHrs'][0] == ''){echo '&nbsp;';}else{ echo $searchData['c_TotalHrs'][0];}?></strong></td><?php $WkHrsT_total = $WkHrsT_total + $searchData['c_TotalHrs'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs01'][0] == ''){echo '';}else{ echo $searchData['Hrs01'][0];}?></td><?php $WkHrs01_total = $WkHrs01_total + $searchData['Hrs01'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs02'][0] == ''){echo '';}else{ echo $searchData['Hrs02'][0];}?></td><?php $WkHrs02_total = $WkHrs02_total + $searchData['Hrs02'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs03'][0] == ''){echo '';}else{ echo $searchData['Hrs03'][0];}?></td><?php $WkHrs03_total = $WkHrs03_total + $searchData['Hrs03'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs04'][0] == ''){echo '';}else{ echo $searchData['Hrs04'][0];}?></td><?php $WkHrs04_total = $WkHrs04_total + $searchData['Hrs04'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs05'][0] == ''){echo '';}else{ echo $searchData['Hrs05'][0];}?></td><?php $WkHrs05_total = $WkHrs05_total + $searchData['Hrs05'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs06'][0] == ''){echo '';}else{ echo $searchData['Hrs06'][0];}?></td><?php $WkHrs06_total = $WkHrs06_total + $searchData['Hrs06'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs07'][0] == ''){echo '';}else{ echo $searchData['Hrs07'][0];}?></td><?php $WkHrs07_total = $WkHrs07_total + $searchData['Hrs07'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs08'][0] == ''){echo '';}else{ echo $searchData['Hrs08'][0];}?></td><?php $WkHrs08_total = $WkHrs08_total + $searchData['Hrs08'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs09'][0] == ''){echo '';}else{ echo $searchData['Hrs09'][0];}?></td><?php $WkHrs09_total = $WkHrs09_total + $searchData['Hrs09'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs10'][0] == ''){echo '';}else{ echo $searchData['Hrs10'][0];}?></td><?php $WkHrs10_total = $WkHrs10_total + $searchData['Hrs10'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs11'][0] == ''){echo '';}else{ echo $searchData['Hrs11'][0];}?></td><?php $WkHrs11_total = $WkHrs11_total + $searchData['Hrs11'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs12'][0] == ''){echo '';}else{ echo $searchData['Hrs12'][0];}?></td><?php $WkHrs12_total = $WkHrs12_total + $searchData['Hrs12'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs13'][0] == ''){echo '';}else{ echo $searchData['Hrs13'][0];}?></td><?php $WkHrs13_total = $WkHrs13_total + $searchData['Hrs13'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs14'][0] == ''){echo '';}else{ echo $searchData['Hrs14'][0];}?></td><?php $WkHrs14_total = $WkHrs14_total + $searchData['Hrs14'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs15'][0] == ''){echo '';}else{ echo $searchData['Hrs15'][0];}?></td><?php $WkHrs15_total = $WkHrs15_total + $searchData['Hrs15'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs16'][0] == ''){echo '';}else{ echo $searchData['Hrs16'][0];}?></td><?php $WkHrs16_total = $WkHrs16_total + $searchData['Hrs16'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs17'][0] == ''){echo '';}else{ echo $searchData['Hrs17'][0];}?></td><?php $WkHrs17_total = $WkHrs17_total + $searchData['Hrs17'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs18'][0] == ''){echo '';}else{ echo $searchData['Hrs18'][0];}?></td><?php $WkHrs18_total = $WkHrs18_total + $searchData['Hrs18'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs19'][0] == ''){echo '';}else{ echo $searchData['Hrs19'][0];}?></td><?php $WkHrs19_total = $WkHrs19_total + $searchData['Hrs19'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs20'][0] == ''){echo '';}else{ echo $searchData['Hrs20'][0];}?></td><?php $WkHrs20_total = $WkHrs20_total + $searchData['Hrs20'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs21'][0] == ''){echo '';}else{ echo $searchData['Hrs21'][0];}?></td><?php $WkHrs21_total = $WkHrs21_total + $searchData['Hrs21'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs22'][0] == ''){echo '';}else{ echo $searchData['Hrs22'][0];}?></td><?php $WkHrs22_total = $WkHrs22_total + $searchData['Hrs22'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs23'][0] == ''){echo '';}else{ echo $searchData['Hrs23'][0];}?></td><?php $WkHrs23_total = $WkHrs23_total + $searchData['Hrs23'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs24'][0] == ''){echo '';}else{ echo $searchData['Hrs24'][0];}?></td><?php $WkHrs24_total = $WkHrs24_total + $searchData['Hrs24'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs25'][0] == ''){echo '';}else{ echo $searchData['Hrs25'][0];}?></td><?php $WkHrs25_total = $WkHrs25_total + $searchData['Hrs25'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs26'][0] == ''){echo '';}else{ echo $searchData['Hrs26'][0];}?></td><?php $WkHrs26_total = $WkHrs26_total + $searchData['Hrs26'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs27'][0] == ''){echo '';}else{ echo $searchData['Hrs27'][0];}?></td><?php $WkHrs27_total = $WkHrs27_total + $searchData['Hrs27'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs28'][0] == ''){echo '';}else{ echo $searchData['Hrs28'][0];}?></td><?php $WkHrs28_total = $WkHrs28_total + $searchData['Hrs28'][0];?>
			
			
			<?php if ($days_in_month > 28) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs29'][0] == ''){echo '';}else{ echo $searchData['Hrs29'][0];}?></td><?php $WkHrs29_total = $WkHrs29_total + $searchData['Hrs29'][0];?>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs30'][0] == ''){echo '';}else{ echo $searchData['Hrs30'][0];}?></td><?php $WkHrs30_total = $WkHrs30_total + $searchData['Hrs30'][0];?>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData['Hrs31'][0] == ''){echo '';}else{ echo $searchData['Hrs31'][0];}?></td><?php $WkHrs31_total = $WkHrs31_total + $searchData['Hrs31'][0];?>
			<?php } ?>
			
			<?php if($today_stamp <= $lockout_day_stamp){ ?>
	
				<td class="slim" style="background-color:#e9f2ff"><a href="/staff/sims/timesheets2.php?action=edit&Timesheet_ID=<?php echo $timesheet_ID;?>&edit_row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>" title="Click here to edit this row.">Edit</a></td>
				<td class="slim" style="background-color:#e9f2ff"><?php if($searchResult['foundCount'] > 1){ ?><a href="/staff/sims/timesheet_hrs_delete.php?row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>&bgt_auth=<?php echo $searchData['BudgetAuthorityLocal'][0];?>" onclick="return confirmDelete()" title="Click here to delete this row."><img src="/staff/sims/images/red_cross_small.png" border="0"></a><?php }else{?>&nbsp;<?php }?></td>
	
			<?php } else { ?>
	
				<td colspan="2" class="slim" style="padding:3px;vertical-align:bottom;border-left:0px;background-color:#e9f2ff"><img src="/staff/sims/images/padlock_blk_small.png" border="0" style="margin:0px"></td>
	
			<?php } ?>
	
			</tr>
			
			
			<?php
			
			  } ?>
	<!--START: SUB-TOTAL REGULAR HRS-->									
			<tr>
			<td class="slim_foot" style="text-align:right;padding:4px" nowrap colspan="2"><?php if($today_stamp <= $lockout_day_stamp){ ?><div style="float:left;vertical-align:text-bottom"><a href="/staff/sims/timesheets.php?action=edit&new_row=wk&Timesheet_ID=<?php echo $timesheet_ID;?>" title="Click here to add a new budget code to your timesheet."><img src="images/green_plus_button.png" style="border:0px;outline:0px;"></a></div><?php } ?><strong>Sub-Total RegHrs</strong></td>
			<td class="slim_foot"><strong><?php echo $WkHrsT_total;?></strong></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs01_total;}?></td><?php $AllHrs01_total = $WkHrs01_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs02_total;}?></td><?php $AllHrs02_total = $WkHrs02_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs03_total;}?></td><?php $AllHrs03_total = $WkHrs03_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs04_total;}?></td><?php $AllHrs04_total = $WkHrs04_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs05_total;}?></td><?php $AllHrs05_total = $WkHrs05_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs06_total;}?></td><?php $AllHrs06_total = $WkHrs06_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs07_total;}?></td><?php $AllHrs07_total = $WkHrs07_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs08_total;}?></td><?php $AllHrs08_total = $WkHrs08_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs09_total;}?></td><?php $AllHrs09_total = $WkHrs09_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs10_total;}?></td><?php $AllHrs10_total = $WkHrs10_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs11_total;}?></td><?php $AllHrs11_total = $WkHrs11_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs12_total;}?></td><?php $AllHrs12_total = $WkHrs12_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs13_total;}?></td><?php $AllHrs13_total = $WkHrs13_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs14_total;}?></td><?php $AllHrs14_total = $WkHrs14_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs15_total;}?></td><?php $AllHrs15_total = $WkHrs15_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs16_total;}?></td><?php $AllHrs16_total = $WkHrs16_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs17_total;}?></td><?php $AllHrs17_total = $WkHrs17_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs18_total;}?></td><?php $AllHrs18_total = $WkHrs18_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs19_total;}?></td><?php $AllHrs19_total = $WkHrs19_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs20_total;}?></td><?php $AllHrs20_total = $WkHrs20_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs21_total;}?></td><?php $AllHrs21_total = $WkHrs21_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs22_total;}?></td><?php $AllHrs22_total = $WkHrs22_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs23_total;}?></td><?php $AllHrs23_total = $WkHrs23_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs24_total;}?></td><?php $AllHrs24_total = $WkHrs24_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs25_total;}?></td><?php $AllHrs25_total = $WkHrs25_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs26_total;}?></td><?php $AllHrs26_total = $WkHrs26_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs27_total;}?></td><?php $AllHrs27_total = $WkHrs27_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs28_total;}?></td><?php $AllHrs28_total = $WkHrs28_total;?>
			
			
			<?php if ($days_in_month > 28) { ?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs29_total;}?></td><?php $AllHrs29_total = $WkHrs29_total;?>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs30_total;}?></td><?php $AllHrs30_total = $WkHrs30_total;?>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs31_total;}?></td><?php $AllHrs31_total = $WkHrs31_total;?>
			<?php } ?>
			<td class="slim_foot" colspan="2" style="background-color:#888;border-top:2px solid #888;border-right:1px solid #888;border-bottom:1px solid #888;">&nbsp;</td>
			
			
			
			</tr>
	
	<!--END: SUB-TOTAL REGULAR HRS-->
	
	<!--/table-->
	
		<tr><td colspan="<?php echo $header_colspan;?>" bgcolor="ebebeb">
		
		</td></tr>
	
	
	<!--END FIRST SECTION: REGULAR HOURS BY BUDGET CODE-->
	
	<?php if($_SESSION['employee_type'] != 'Hourly'){ //HOURLY STAFF DON'T HAVE PAID LEAVE, UNPAID LEAVE, OR OVERTIME HRS ?>
	
	<!--BEGIN SECOND SECTION: PAID LEAVE HOURS-->
	
	<tr><td class="section_head" colspan="<?php echo $header_colspan;?>"><strong>Paid Leave Hours</strong><div class="nav" style="float:right;text-align:right"><a href="http://www.sedl.org/staff/personnel/leavereport.cgi" target="_blank">Show current leave report</a><?php if($_SESSION['leave_requests_access'] == 'Yes'){?> | <a href="/staff/sims/menu_leave.php">My leave requests</a><?php }?></div></td></tr>
	
	<?php 
		if(($searchResult3['foundCount'])==0 && ($new_row != 'pdlv')){ //searchResult3 -> fmp table = time_hrs ?> 
			<tr><td colspan="<?php echo $header_colspan;?>">
			<p class="alert_small">There are no paid leave hours entered for this timesheet.<?php if($today_stamp <= $lockout_day_stamp){ ?> | <a href="/staff/sims/timesheets.php?action=edit&new_row=pdlv&Timesheet_ID=<?php echo $timesheet_ID;?>" title="Click here to add paid leave hours to your timesheet.">Add Paid Leave Hours</a></p><?php } ?>
			</td></tr>
			
			<tr bgcolor="#a2c7ca">
			<td class="slim" style="text-align:left;padding-left:6px" nowrap colspan="2"><strong>Total RegHrs</strong></td>
			<td class="slim"><strong><?php echo $WkHrsT_total;?></strong></td>
	
			<td class="slim" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs01_total;}?></td><?php $RegHrs01_total = $WkHrs01_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs02_total;}?></td><?php $RegHrs02_total = $WkHrs02_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs03_total;}?></td><?php $RegHrs03_total = $WkHrs03_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs04_total;}?></td><?php $RegHrs04_total = $WkHrs04_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs05_total;}?></td><?php $RegHrs05_total = $WkHrs05_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs06_total;}?></td><?php $RegHrs06_total = $WkHrs06_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs07_total;}?></td><?php $RegHrs07_total = $WkHrs07_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs08_total;}?></td><?php $RegHrs08_total = $WkHrs08_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs09_total;}?></td><?php $RegHrs09_total = $WkHrs09_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs10_total;}?></td><?php $RegHrs10_total = $WkHrs10_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs11_total;}?></td><?php $RegHrs11_total = $WkHrs11_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs12_total;}?></td><?php $RegHrs12_total = $WkHrs12_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs13_total;}?></td><?php $RegHrs13_total = $WkHrs13_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs14_total;}?></td><?php $RegHrs14_total = $WkHrs14_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs15_total;}?></td><?php $RegHrs15_total = $WkHrs15_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs16_total;}?></td><?php $RegHrs16_total = $WkHrs16_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs17_total;}?></td><?php $RegHrs17_total = $WkHrs17_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs18_total;}?></td><?php $RegHrs18_total = $WkHrs18_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs19_total;}?></td><?php $RegHrs19_total = $WkHrs19_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs20_total;}?></td><?php $RegHrs20_total = $WkHrs20_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs21_total;}?></td><?php $RegHrs21_total = $WkHrs21_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs22_total;}?></td><?php $RegHrs22_total = $WkHrs22_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs23_total;}?></td><?php $RegHrs23_total = $WkHrs23_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs24_total;}?></td><?php $RegHrs24_total = $WkHrs24_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs25_total;}?></td><?php $RegHrs25_total = $WkHrs25_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs26_total;}?></td><?php $RegHrs26_total = $WkHrs26_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs27_total;}?></td><?php $RegHrs27_total = $WkHrs27_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs28_total;}?></td><?php $RegHrs28_total = $WkHrs28_total;?>
	
	
			<?php if ($days_in_month > 28) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs29_total;}?></td><?php $RegHrs29_total = $WkHrs29_total;?>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs30_total;}?></td><?php $RegHrs30_total = $WkHrs30_total;?>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $WkHrs31_total;}?></td><?php $RegHrs31_total = $WkHrs31_total;?>
			<?php } ?>
			<td class="slim" colspan="2">&nbsp;</td>
			
			
			
			</tr>
		<?php }else{ ?>
		
	<!--table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="slim"-->
			<tr>
			<td class="slim_head" colspan="2" style="text-align:left">&nbsp;<strong>Leave Type</strong></td>
			<td class="slim_head"><strong>T</strong></td>
			<td <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>01</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>02</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>03</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>04</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>05</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>06</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>07</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>08</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>09</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>10</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>11</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>12</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>13</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>14</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>15</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>16</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>17</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>18</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>19</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>20</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>21</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>22</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>23</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>24</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>25</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>26</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>27</strong>';}?></td>
			<td <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>28</strong>';}?></td>
	
			<?php if ($days_in_month > 28) { ?>
			<td <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>29</strong>';}?></td>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>30</strong>';}?></td>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'class="slim_head_collapse">';}else{echo 'class="slim_head"><strong>31</strong>';}?></td>
			<?php } ?>
			<td class="slim_head" colspan="2">&nbsp;</td>
			</tr>
			
			
			<?php
		
		
			foreach($searchResult3['data'] as $key => $searchData3) { //searchResult3 -> fmp table = time_hrs ?>
			
			<tr>
			<td class="slim" style="text-align:left;padding-left:6px" nowrap colspan="2"><?php echo $searchData3['LvType'][0];?></td>
			<td class="slim"><strong><?php echo $searchData3['c_TotalHrs'][0];?></strong></td><?php $PdLvHrsT_total = $PdLvHrsT_total + $searchData3['c_TotalHrs'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs01'][0] == ''){echo '';}else{ echo $searchData3['Hrs01'][0];}?></td><?php $PdLvHrs01_total = $PdLvHrs01_total + $searchData3['Hrs01'][0];?><?php $RegHrs01_total = $PdLvHrs01_total + $WkHrs01_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs02'][0] == ''){echo '';}else{ echo $searchData3['Hrs02'][0];}?></td><?php $PdLvHrs02_total = $PdLvHrs02_total + $searchData3['Hrs02'][0];?><?php $RegHrs02_total = $PdLvHrs02_total + $WkHrs02_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs03'][0] == ''){echo '';}else{ echo $searchData3['Hrs03'][0];}?></td><?php $PdLvHrs03_total = $PdLvHrs03_total + $searchData3['Hrs03'][0];?><?php $RegHrs03_total = $PdLvHrs03_total + $WkHrs03_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs04'][0] == ''){echo '';}else{ echo $searchData3['Hrs04'][0];}?></td><?php $PdLvHrs04_total = $PdLvHrs04_total + $searchData3['Hrs04'][0];?><?php $RegHrs04_total = $PdLvHrs04_total + $WkHrs04_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs05'][0] == ''){echo '';}else{ echo $searchData3['Hrs05'][0];}?></td><?php $PdLvHrs05_total = $PdLvHrs05_total + $searchData3['Hrs05'][0];?><?php $RegHrs05_total = $PdLvHrs05_total + $WkHrs05_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs06'][0] == ''){echo '';}else{ echo $searchData3['Hrs06'][0];}?></td><?php $PdLvHrs06_total = $PdLvHrs06_total + $searchData3['Hrs06'][0];?><?php $RegHrs06_total = $PdLvHrs06_total + $WkHrs06_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs07'][0] == ''){echo '';}else{ echo $searchData3['Hrs07'][0];}?></td><?php $PdLvHrs07_total = $PdLvHrs07_total + $searchData3['Hrs07'][0];?><?php $RegHrs07_total = $PdLvHrs07_total + $WkHrs07_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs08'][0] == ''){echo '';}else{ echo $searchData3['Hrs08'][0];}?></td><?php $PdLvHrs08_total = $PdLvHrs08_total + $searchData3['Hrs08'][0];?><?php $RegHrs08_total = $PdLvHrs08_total + $WkHrs08_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs09'][0] == ''){echo '';}else{ echo $searchData3['Hrs09'][0];}?></td><?php $PdLvHrs09_total = $PdLvHrs09_total + $searchData3['Hrs09'][0];?><?php $RegHrs09_total = $PdLvHrs09_total + $WkHrs09_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs10'][0] == ''){echo '';}else{ echo $searchData3['Hrs10'][0];}?></td><?php $PdLvHrs10_total = $PdLvHrs10_total + $searchData3['Hrs10'][0];?><?php $RegHrs10_total = $PdLvHrs10_total + $WkHrs10_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs11'][0] == ''){echo '';}else{ echo $searchData3['Hrs11'][0];}?></td><?php $PdLvHrs11_total = $PdLvHrs11_total + $searchData3['Hrs11'][0];?><?php $RegHrs11_total = $PdLvHrs11_total + $WkHrs11_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs12'][0] == ''){echo '';}else{ echo $searchData3['Hrs12'][0];}?></td><?php $PdLvHrs12_total = $PdLvHrs12_total + $searchData3['Hrs12'][0];?><?php $RegHrs12_total = $PdLvHrs12_total + $WkHrs12_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs13'][0] == ''){echo '';}else{ echo $searchData3['Hrs13'][0];}?></td><?php $PdLvHrs13_total = $PdLvHrs13_total + $searchData3['Hrs13'][0];?><?php $RegHrs13_total = $PdLvHrs13_total + $WkHrs13_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs14'][0] == ''){echo '';}else{ echo $searchData3['Hrs14'][0];}?></td><?php $PdLvHrs14_total = $PdLvHrs14_total + $searchData3['Hrs14'][0];?><?php $RegHrs14_total = $PdLvHrs14_total + $WkHrs14_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs15'][0] == ''){echo '';}else{ echo $searchData3['Hrs15'][0];}?></td><?php $PdLvHrs15_total = $PdLvHrs15_total + $searchData3['Hrs15'][0];?><?php $RegHrs15_total = $PdLvHrs15_total + $WkHrs15_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs16'][0] == ''){echo '';}else{ echo $searchData3['Hrs16'][0];}?></td><?php $PdLvHrs16_total = $PdLvHrs16_total + $searchData3['Hrs16'][0];?><?php $RegHrs16_total = $PdLvHrs16_total + $WkHrs16_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs17'][0] == ''){echo '';}else{ echo $searchData3['Hrs17'][0];}?></td><?php $PdLvHrs17_total = $PdLvHrs17_total + $searchData3['Hrs17'][0];?><?php $RegHrs17_total = $PdLvHrs17_total + $WkHrs17_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs18'][0] == ''){echo '';}else{ echo $searchData3['Hrs18'][0];}?></td><?php $PdLvHrs18_total = $PdLvHrs18_total + $searchData3['Hrs18'][0];?><?php $RegHrs18_total = $PdLvHrs18_total + $WkHrs18_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs19'][0] == ''){echo '';}else{ echo $searchData3['Hrs19'][0];}?></td><?php $PdLvHrs19_total = $PdLvHrs19_total + $searchData3['Hrs19'][0];?><?php $RegHrs19_total = $PdLvHrs19_total + $WkHrs19_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs20'][0] == ''){echo '';}else{ echo $searchData3['Hrs20'][0];}?></td><?php $PdLvHrs20_total = $PdLvHrs20_total + $searchData3['Hrs20'][0];?><?php $RegHrs20_total = $PdLvHrs20_total + $WkHrs20_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs21'][0] == ''){echo '';}else{ echo $searchData3['Hrs21'][0];}?></td><?php $PdLvHrs21_total = $PdLvHrs21_total + $searchData3['Hrs21'][0];?><?php $RegHrs21_total = $PdLvHrs21_total + $WkHrs21_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs22'][0] == ''){echo '';}else{ echo $searchData3['Hrs22'][0];}?></td><?php $PdLvHrs22_total = $PdLvHrs22_total + $searchData3['Hrs22'][0];?><?php $RegHrs22_total = $PdLvHrs22_total + $WkHrs22_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs23'][0] == ''){echo '';}else{ echo $searchData3['Hrs23'][0];}?></td><?php $PdLvHrs23_total = $PdLvHrs23_total + $searchData3['Hrs23'][0];?><?php $RegHrs23_total = $PdLvHrs23_total + $WkHrs23_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs24'][0] == ''){echo '';}else{ echo $searchData3['Hrs24'][0];}?></td><?php $PdLvHrs24_total = $PdLvHrs24_total + $searchData3['Hrs24'][0];?><?php $RegHrs24_total = $PdLvHrs24_total + $WkHrs24_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs25'][0] == ''){echo '';}else{ echo $searchData3['Hrs25'][0];}?></td><?php $PdLvHrs25_total = $PdLvHrs25_total + $searchData3['Hrs25'][0];?><?php $RegHrs25_total = $PdLvHrs25_total + $WkHrs25_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs26'][0] == ''){echo '';}else{ echo $searchData3['Hrs26'][0];}?></td><?php $PdLvHrs26_total = $PdLvHrs26_total + $searchData3['Hrs26'][0];?><?php $RegHrs26_total = $PdLvHrs26_total + $WkHrs26_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs27'][0] == ''){echo '';}else{ echo $searchData3['Hrs27'][0];}?></td><?php $PdLvHrs27_total = $PdLvHrs27_total + $searchData3['Hrs27'][0];?><?php $RegHrs27_total = $PdLvHrs27_total + $WkHrs27_total;?>
			<td class="slim" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs28'][0] == ''){echo '';}else{ echo $searchData3['Hrs28'][0];}?></td><?php $PdLvHrs28_total = $PdLvHrs28_total + $searchData3['Hrs28'][0];?><?php $RegHrs28_total = $PdLvHrs28_total + $WkHrs28_total;?>
			
			
			<?php if ($days_in_month > 28) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs29'][0] == ''){echo '';}else{ echo $searchData3['Hrs29'][0];}?></td><?php $PdLvHrs29_total = $PdLvHrs29_total + $searchData3['Hrs29'][0];?><?php $RegHrs29_total = $PdLvHrs29_total + $WkHrs29_total;?>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs30'][0] == ''){echo '';}else{ echo $searchData3['Hrs30'][0];}?></td><?php $PdLvHrs30_total = $PdLvHrs30_total + $searchData3['Hrs30'][0];?><?php $RegHrs30_total = $PdLvHrs30_total + $WkHrs30_total;?>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if($searchData3['Hrs31'][0] == ''){echo '';}else{ echo $searchData3['Hrs31'][0];}?></td><?php $PdLvHrs31_total = $PdLvHrs31_total + $searchData3['Hrs31'][0];?><?php $RegHrs31_total = $PdLvHrs31_total + $WkHrs31_total;?>
			<?php } ?>
			
			<?php if($today_stamp <= $lockout_day_stamp){ //IF THE TIMESHEET IS NOT LOCKED ?>
	
						
						
						<?php if($searchData3['LvType'][0] != 'Holiday'){ //IF THE LEAVE TYPE IS NOT "HOLIDAY", ALLOW EDITING ?>
						
						<td class="slim" style="background-color:#e9f2ff">
						<a href="/staff/sims/timesheets.php?action=edit&Timesheet_ID=<?php echo $timesheet_ID;?>&edit_row_ID=<?php echo $searchData3['c_cwp_row_ID'][0];?>" title="Click here to edit this row.">Edit</a>
								
						</td><td class="slim" style="background-color:#e9f2ff">
							
						<a href="/staff/sims/timesheet_hrs_delete.php?row_ID=<?php echo $searchData3['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()" title="Click here to delete this row."><img src="/staff/sims/images/red_cross_small.png" border="0"></a></td>
						
						<?php }else{ //IF THE LEAVE TYPE IS "HOLIDAY", DISALLOW EDITING ?>										
						
								<td class="dark_gray" colspan="2"></td>
								
						<?php } ?>		
	
			<?php } else {  //IF THE TIMESHEET IS LOCKED ?>
	
						<td colspan="2" class="slim" style="padding:3px;vertical-align:bottom;background-color:#e9f2ff"><img src="/staff/sims/images/padlock_blk_small.png" border="0"></a></td>
	
			<?php } ?>
			
			</tr>
			
			
			<?php  } ?>
	<!--START: TOTAL REGULAR HRS-->									
			<tr>
			<td class="slim_foot" style="text-align:right;padding:4px" nowrap colspan="2"><?php if($today_stamp <= $lockout_day_stamp){ ?><div style="float:left;vertical-align:text-bottom"><a href="/staff/sims/timesheets.php?action=edit&new_row=pdlv&Timesheet_ID=<?php echo $timesheet_ID;?>" title="Click here to add paid leave hours to your timesheet."><img src="images/green_plus_button.png" style="border:0px;outline:0px;"></a></div><?php } ?><strong>Total RegHrs</strong></td>
			<td class="slim_foot"><strong><?php $RegHrsT_total = $PdLvHrsT_total + $WkHrsT_total; echo $RegHrsT_total;?></strong></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs01_total;}?></td><?php $AllHrs01_total = $RegHrs01_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs02_total;}?></td><?php $AllHrs02_total = $RegHrs02_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs03_total;}?></td><?php $AllHrs03_total = $RegHrs03_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs04_total;}?></td><?php $AllHrs04_total = $RegHrs04_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs05_total;}?></td><?php $AllHrs05_total = $RegHrs05_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs06_total;}?></td><?php $AllHrs06_total = $RegHrs06_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs07_total;}?></td><?php $AllHrs07_total = $RegHrs07_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs08_total;}?></td><?php $AllHrs08_total = $RegHrs08_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs09_total;}?></td><?php $AllHrs09_total = $RegHrs09_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs10_total;}?></td><?php $AllHrs10_total = $RegHrs10_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs11_total;}?></td><?php $AllHrs11_total = $RegHrs11_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs12_total;}?></td><?php $AllHrs12_total = $RegHrs12_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs13_total;}?></td><?php $AllHrs13_total = $RegHrs13_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs14_total;}?></td><?php $AllHrs14_total = $RegHrs14_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs15_total;}?></td><?php $AllHrs15_total = $RegHrs15_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs16_total;}?></td><?php $AllHrs16_total = $RegHrs16_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs17_total;}?></td><?php $AllHrs17_total = $RegHrs17_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs18_total;}?></td><?php $AllHrs18_total = $RegHrs18_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs19_total;}?></td><?php $AllHrs19_total = $RegHrs19_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs20_total;}?></td><?php $AllHrs20_total = $RegHrs20_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs21_total;}?></td><?php $AllHrs21_total = $RegHrs21_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs22_total;}?></td><?php $AllHrs22_total = $RegHrs22_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs23_total;}?></td><?php $AllHrs23_total = $RegHrs23_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs24_total;}?></td><?php $AllHrs24_total = $RegHrs24_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs25_total;}?></td><?php $AllHrs25_total = $RegHrs25_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs26_total;}?></td><?php $AllHrs26_total = $RegHrs26_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs27_total;}?></td><?php $AllHrs27_total = $RegHrs27_total;?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs28_total;}?></td><?php $AllHrs28_total = $RegHrs28_total;?>
	
	
	
			<?php if ($days_in_month > 28) { ?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs29_total;}?></td><?php $AllHrs29_total = $RegHrs29_total;?>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs30_total;}?></td><?php $AllHrs30_total = $RegHrs30_total;?>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $RegHrs31_total;}?></td><?php $AllHrs31_total = $RegHrs31_total;?>
			<?php } ?>

			<td class="slim_foot" colspan="2" style="background-color:#888;border-top:2px solid #888;border-right:1px solid #888;border-bottom:1px solid #888;">&nbsp;</td>
			
			</tr>
		
	<!--END: TOTAL REGULAR HRS-->
	
	<!--/table-->
		<tr><td colspan="<?php echo $header_colspan;?>" bgcolor="ebebeb">
		</td></tr>
	
	
	
	
	<?php } ?>
	
	<!--END SECOND SECTION: PAID LEAVE HOURS-->						
	
	<!--BEGIN THIRD SECTION: UNPAID LEAVE HOURS-->
	
	<tr><td class="section_head" colspan="<?php echo $header_colspan;?>"><strong>Unpaid Leave Hours</strong></td></tr>
	
	<?php 
		if($searchResult4['foundCount']==0){ //searchResult4 -> fmp table = time_hrs ?> 
			<tr><td colspan="<?php echo $header_colspan;?>">
			<div class="alert_small">There are no unpaid leave hours entered for this timesheet. <?php if($today_stamp <= $lockout_day_stamp){ ?><div style="float:right;text-align:right"><a href="/staff/sims/timesheets.php?action=edit&new_row=unpdlv&Timesheet_ID=<?php echo $timesheet_ID;?>" title="Click here to add unpaid leave hours to your timesheet.">Add Unpaid Leave Hours</a></div><?php } ?></div>&nbsp;<p>
			</td></tr>
		<?php }else{ ?>
		
	<!--table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="slim"-->
			<tr bgcolor="cccccc">
			<td class="slim_head" style="text-align:left">&nbsp;Leave Type</td>
			<td class="slim_head"><strong>&nbsp;</strong></td>
			<td class="slim_head"><strong>T</strong></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>01</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>02</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>03</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>04</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>05</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>06</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>07</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>08</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>09</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>10</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>11</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>12</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>13</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>14</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>15</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>16</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>17</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>18</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>19</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>20</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>21</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>22</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>23</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>24</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>25</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>26</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>27</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>28</strong>';}?></td>
	
			<?php if ($days_in_month > 28) { ?>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>29</strong>';}?></td>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>30</strong>';}?></td>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>31</strong>';}?></td>
			<?php } ?>
			<td class="slim_head" colspan="2">&nbsp;</td>
			</tr>
			
		
			<?php foreach($searchResult4['data'] as $key => $searchData4) { //searchResult4 -> fmp table = time_hrs ?>
			
			<tr>
			<td class="slim" style="text-align:left;padding-left:6px" nowrap><?php echo $searchData4['LvType'][0];?></td>
			<td class="slim"><strong>&nbsp;</strong></td>
			<td class="slim"><strong><?php echo $searchData4['c_TotalHrs'][0];?></strong></td><?php $UnPdLvHrsT_total = $UnPdLvHrsT_total + $searchData4['c_TotalHrs'][0];?>
	
			<td class="slim" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs01'][0] == ''){echo '';}else{ echo $searchData4['Hrs01'][0];}?></td><?php $UnPdLvHrs01_total = $UnPdLvHrs01_total + $searchData4['Hrs01'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs02'][0] == ''){echo '';}else{ echo $searchData4['Hrs02'][0];}?></td><?php $UnPdLvHrs02_total = $UnPdLvHrs02_total + $searchData4['Hrs02'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs03'][0] == ''){echo '';}else{ echo $searchData4['Hrs03'][0];}?></td><?php $UnPdLvHrs03_total = $UnPdLvHrs03_total + $searchData4['Hrs03'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs04'][0] == ''){echo '';}else{ echo $searchData4['Hrs04'][0];}?></td><?php $UnPdLvHrs04_total = $UnPdLvHrs04_total + $searchData4['Hrs04'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs05'][0] == ''){echo '';}else{ echo $searchData4['Hrs05'][0];}?></td><?php $UnPdLvHrs05_total = $UnPdLvHrs05_total + $searchData4['Hrs05'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs06'][0] == ''){echo '';}else{ echo $searchData4['Hrs06'][0];}?></td><?php $UnPdLvHrs06_total = $UnPdLvHrs06_total + $searchData4['Hrs06'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs07'][0] == ''){echo '';}else{ echo $searchData4['Hrs07'][0];}?></td><?php $UnPdLvHrs07_total = $UnPdLvHrs07_total + $searchData4['Hrs07'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs08'][0] == ''){echo '';}else{ echo $searchData4['Hrs08'][0];}?></td><?php $UnPdLvHrs08_total = $UnPdLvHrs08_total + $searchData4['Hrs08'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs09'][0] == ''){echo '';}else{ echo $searchData4['Hrs09'][0];}?></td><?php $UnPdLvHrs09_total = $UnPdLvHrs09_total + $searchData4['Hrs09'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs10'][0] == ''){echo '';}else{ echo $searchData4['Hrs10'][0];}?></td><?php $UnPdLvHrs10_total = $UnPdLvHrs10_total + $searchData4['Hrs10'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs11'][0] == ''){echo '';}else{ echo $searchData4['Hrs11'][0];}?></td><?php $UnPdLvHrs11_total = $UnPdLvHrs11_total + $searchData4['Hrs11'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs12'][0] == ''){echo '';}else{ echo $searchData4['Hrs12'][0];}?></td><?php $UnPdLvHrs12_total = $UnPdLvHrs12_total + $searchData4['Hrs12'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs13'][0] == ''){echo '';}else{ echo $searchData4['Hrs13'][0];}?></td><?php $UnPdLvHrs13_total = $UnPdLvHrs13_total + $searchData4['Hrs13'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs14'][0] == ''){echo '';}else{ echo $searchData4['Hrs14'][0];}?></td><?php $UnPdLvHrs14_total = $UnPdLvHrs14_total + $searchData4['Hrs14'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs15'][0] == ''){echo '';}else{ echo $searchData4['Hrs15'][0];}?></td><?php $UnPdLvHrs15_total = $UnPdLvHrs15_total + $searchData4['Hrs15'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs16'][0] == ''){echo '';}else{ echo $searchData4['Hrs16'][0];}?></td><?php $UnPdLvHrs16_total = $UnPdLvHrs16_total + $searchData4['Hrs16'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs17'][0] == ''){echo '';}else{ echo $searchData4['Hrs17'][0];}?></td><?php $UnPdLvHrs17_total = $UnPdLvHrs17_total + $searchData4['Hrs17'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs18'][0] == ''){echo '';}else{ echo $searchData4['Hrs18'][0];}?></td><?php $UnPdLvHrs18_total = $UnPdLvHrs18_total + $searchData4['Hrs18'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs19'][0] == ''){echo '';}else{ echo $searchData4['Hrs19'][0];}?></td><?php $UnPdLvHrs19_total = $UnPdLvHrs19_total + $searchData4['Hrs19'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs20'][0] == ''){echo '';}else{ echo $searchData4['Hrs20'][0];}?></td><?php $UnPdLvHrs20_total = $UnPdLvHrs20_total + $searchData4['Hrs20'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs21'][0] == ''){echo '';}else{ echo $searchData4['Hrs21'][0];}?></td><?php $UnPdLvHrs21_total = $UnPdLvHrs21_total + $searchData4['Hrs21'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs22'][0] == ''){echo '';}else{ echo $searchData4['Hrs22'][0];}?></td><?php $UnPdLvHrs22_total = $UnPdLvHrs22_total + $searchData4['Hrs22'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs23'][0] == ''){echo '';}else{ echo $searchData4['Hrs23'][0];}?></td><?php $UnPdLvHrs23_total = $UnPdLvHrs23_total + $searchData4['Hrs23'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs24'][0] == ''){echo '';}else{ echo $searchData4['Hrs24'][0];}?></td><?php $UnPdLvHrs24_total = $UnPdLvHrs24_total + $searchData4['Hrs24'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs25'][0] == ''){echo '';}else{ echo $searchData4['Hrs25'][0];}?></td><?php $UnPdLvHrs25_total = $UnPdLvHrs25_total + $searchData4['Hrs25'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs26'][0] == ''){echo '';}else{ echo $searchData4['Hrs26'][0];}?></td><?php $UnPdLvHrs26_total = $UnPdLvHrs26_total + $searchData4['Hrs26'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs27'][0] == ''){echo '';}else{ echo $searchData4['Hrs27'][0];}?></td><?php $UnPdLvHrs27_total = $UnPdLvHrs27_total + $searchData4['Hrs27'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs28'][0] == ''){echo '';}else{ echo $searchData4['Hrs28'][0];}?></td><?php $UnPdLvHrs28_total = $UnPdLvHrs28_total + $searchData4['Hrs28'][0];?>
	
	
	
			<?php if ($days_in_month > 28) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs29'][0] == ''){echo '';}else{ echo $searchData4['Hrs29'][0];}?></td><?php $UnPdLvHrs29_total = $UnPdLvHrs29_total + $searchData4['Hrs29'][0];?>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs30'][0] == ''){echo '';}else{ echo $searchData4['Hrs30'][0];}?></td><?php $UnPdLvHrs30_total = $UnPdLvHrs30_total + $searchData4['Hrs30'][0];?>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData4['Hrs31'][0] == ''){echo '';}else{ echo $searchData4['Hrs31'][0];}?></td><?php $UnPdLvHrs31_total = $UnPdLvHrs31_total + $searchData4['Hrs31'][0];?>
			<?php } ?>
			
			<?php if($today_stamp <= $lockout_day_stamp){ ?>
	
				<td class="slim"><a href="/staff/sims/timesheets.php?action=edit&Timesheet_ID=<?php echo $timesheet_ID;?>&edit_row_ID=<?php echo $searchData4['c_cwp_row_ID'][0];?>" title="Click here to edit this row.">Edit</a></td>
				<td><a href="/staff/sims/timesheet_hrs_delete.php?row_ID=<?php echo $searchData4['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()" title="Click here to delete this row."><img src="/staff/sims/images/red_cross_small.png" border="0"></a></td>
	
			<?php } else { ?>
	
				<td class="slim" bgcolor="cccccc">&nbsp;</td>
				<td><img src="/staff/sims/images/padlock_blk_small.png" border="0"></a></td>
	
			<?php } ?>
			
			
			
			
			
			
			</tr>
			
			
	
			
			<?php  } ?>
			
			<?php $AllHrs01_total = $AllHrs01_total + $UnPdLvHrs01_total;?>
			<?php $AllHrs02_total = $AllHrs02_total + $UnPdLvHrs02_total;?>
			<?php $AllHrs03_total = $AllHrs03_total + $UnPdLvHrs03_total;?>
			<?php $AllHrs04_total = $AllHrs04_total + $UnPdLvHrs04_total;?>
			<?php $AllHrs05_total = $AllHrs05_total + $UnPdLvHrs05_total;?>
			<?php $AllHrs06_total = $AllHrs06_total + $UnPdLvHrs06_total;?>
			<?php $AllHrs07_total = $AllHrs07_total + $UnPdLvHrs07_total;?>
			<?php $AllHrs08_total = $AllHrs08_total + $UnPdLvHrs08_total;?>
			<?php $AllHrs09_total = $AllHrs09_total + $UnPdLvHrs09_total;?>
			<?php $AllHrs10_total = $AllHrs10_total + $UnPdLvHrs10_total;?>
			<?php $AllHrs11_total = $AllHrs11_total + $UnPdLvHrs11_total;?>
			<?php $AllHrs12_total = $AllHrs12_total + $UnPdLvHrs12_total;?>
			<?php $AllHrs13_total = $AllHrs13_total + $UnPdLvHrs13_total;?>
			<?php $AllHrs14_total = $AllHrs14_total + $UnPdLvHrs14_total;?>
			<?php $AllHrs15_total = $AllHrs15_total + $UnPdLvHrs15_total;?>
			<?php $AllHrs16_total = $AllHrs16_total + $UnPdLvHrs16_total;?>
			<?php $AllHrs17_total = $AllHrs17_total + $UnPdLvHrs17_total;?>
			<?php $AllHrs18_total = $AllHrs18_total + $UnPdLvHrs18_total;?>
			<?php $AllHrs19_total = $AllHrs19_total + $UnPdLvHrs19_total;?>
			<?php $AllHrs20_total = $AllHrs20_total + $UnPdLvHrs20_total;?>
			<?php $AllHrs21_total = $AllHrs21_total + $UnPdLvHrs21_total;?>
			<?php $AllHrs22_total = $AllHrs22_total + $UnPdLvHrs22_total;?>
			<?php $AllHrs23_total = $AllHrs23_total + $UnPdLvHrs23_total;?>
			<?php $AllHrs24_total = $AllHrs24_total + $UnPdLvHrs24_total;?>
			<?php $AllHrs25_total = $AllHrs25_total + $UnPdLvHrs25_total;?>
			<?php $AllHrs26_total = $AllHrs26_total + $UnPdLvHrs26_total;?>
			<?php $AllHrs27_total = $AllHrs27_total + $UnPdLvHrs27_total;?>
			<?php $AllHrs28_total = $AllHrs28_total + $UnPdLvHrs28_total;?>
			
			
			
			<?php $AllHrs29_total = $AllHrs29_total + $UnPdLvHrs29_total;?>
			
			
			<?php $AllHrs30_total = $AllHrs30_total + $UnPdLvHrs30_total;?>
			
			
			<?php $AllHrs31_total = $AllHrs31_total + $UnPdLvHrs31_total;?>
			
			
		
	<!--/table-->
	<?php if($today_stamp <= $lockout_day_stamp){ ?>
	
		<tr><td colspan="<?php echo $header_colspan;?>">
		<a href="/staff/sims/timesheets.php?action=edit&new_row=unpdlv&Timesheet_ID=<?php echo $timesheet_ID;?>" title="Click here to add unpaid leave hours to your timesheet.">Add Unpaid Leave Hours</a><p>&nbsp;<p>
		</td></tr>
	
	<?php } else { ?>
	
		<tr><td colspan="<?php echo $header_colspan;?>" bgcolor="ebebeb">
		<p>&nbsp;<p>
		</td></tr>
	
	<?php } ?>
	
	
	
	
	<?php } ?>
	
	<!--END THIRD SECTION: UNPAID LEAVE HOURS-->
	
	<!--BEGIN FOURTH SECTION: OVERTIME HOURS BY BUDGET CODE-->
	<?php } ?>
	<?php if(($_SESSION['employee_type'] == 'Non-exempt')||($_SESSION['employee_type'] == 'Hourly')) { ?>
	
	
	
	<tr><td class="section_head" colspan="<?php echo $header_colspan;?>"><strong>Overtime Hours By Budget Code</strong></td></tr>
	
	<?php 
		if($searchResult5['foundCount']==0){ //searchResult5 -> fmp table = time_hrs ?> 
			<tr><td colspan="<?php echo $header_colspan;?>">
			<p class="alert_small">There are no overtime hours entered for this timesheet.<?php if($today_stamp <= $lockout_day_stamp){ ?> | <a href="/staff/sims/timesheets.php?action=edit&new_row=ot&Timesheet_ID=<?php echo $timesheet_ID;?>" title="Click here to add overtime hours to your timesheet.">Add Overtime Hours</a></p><?php } ?>
			</td></tr>
		<?php }else{ ?>
	<!--table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="slim"-->
			<tr bgcolor="cccccc">
			<td class="slim_head" style="text-align:left">&nbsp;Budget Code</td>
			<td class="slim_head"><strong>BA</strong></td>
			<td class="slim_head"><strong>T</strong></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>01</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>02</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>03</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>04</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>05</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>06</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>07</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>08</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>09</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>10</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>11</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>12</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>13</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>14</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>15</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>16</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>17</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>18</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>19</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>20</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>21</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>22</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>23</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>24</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>25</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>26</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>27</strong>';}?></td>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>28</strong>';}?></td>
	
			<?php if ($days_in_month > 28) { ?>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>29</strong>';}?></td>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>30</strong>';}?></td>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td class="slim_head"><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>31</strong>';}?></td>
			<?php } ?>
			<td class="slim_head" colspan="2">&nbsp;</td>
			</tr>
			
			<?php 
			
			foreach($searchResult5['data'] as $key => $searchData5) { //searchResult5 -> fmp table = time_hrs 
			$bgt_auths[$i] = $searchData5['BudgetAuthorityLocal'][0]; // CREATE BGT_AUTHS ARRAY USED IN SIGNATURE BLOCK
			$i++;
			?>
			
			
			<tr>
			<td class="slim" style="text-align:left;padding-left:6px" nowrap><?php echo $searchData5['BudgetCode'][0];?><?php if($_SESSION['timesheet_prefs_show_nicknames'] == 'Yes'){echo '<font color="#666666"><em> - '.$searchData5['time_hrs_budget_code_usage::Budget_Code_Nickname'][0].'</font></em>';}?></td>
			<td class="slim"><?php echo $searchData5['BudgetAuthorityCodeLocal'][0];?></td>
			<td class="slim"><strong><?php echo $searchData5['c_TotalHrs'][0];?></strong></td><?php $OTHrsT_total = $OTHrsT_total + $searchData5['c_TotalHrs'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs01'][0] == ''){echo '';}else{ echo $searchData5['Hrs01'][0];}?></td><?php $OTHrs01_total = $OTHrs01_total + $searchData5['Hrs01'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs02'][0] == ''){echo '';}else{ echo $searchData5['Hrs02'][0];}?></td><?php $OTHrs02_total = $OTHrs02_total + $searchData5['Hrs02'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs03'][0] == ''){echo '';}else{ echo $searchData5['Hrs03'][0];}?></td><?php $OTHrs03_total = $OTHrs03_total + $searchData5['Hrs03'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs04'][0] == ''){echo '';}else{ echo $searchData5['Hrs04'][0];}?></td><?php $OTHrs04_total = $OTHrs04_total + $searchData5['Hrs04'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs05'][0] == ''){echo '';}else{ echo $searchData5['Hrs05'][0];}?></td><?php $OTHrs05_total = $OTHrs05_total + $searchData5['Hrs05'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs06'][0] == ''){echo '';}else{ echo $searchData5['Hrs06'][0];}?></td><?php $OTHrs06_total = $OTHrs06_total + $searchData5['Hrs06'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs07'][0] == ''){echo '';}else{ echo $searchData5['Hrs07'][0];}?></td><?php $OTHrs07_total = $OTHrs07_total + $searchData5['Hrs07'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs08'][0] == ''){echo '';}else{ echo $searchData5['Hrs08'][0];}?></td><?php $OTHrs08_total = $OTHrs08_total + $searchData5['Hrs08'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs09'][0] == ''){echo '';}else{ echo $searchData5['Hrs09'][0];}?></td><?php $OTHrs09_total = $OTHrs09_total + $searchData5['Hrs09'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs10'][0] == ''){echo '';}else{ echo $searchData5['Hrs10'][0];}?></td><?php $OTHrs10_total = $OTHrs10_total + $searchData5['Hrs10'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs11'][0] == ''){echo '';}else{ echo $searchData5['Hrs11'][0];}?></td><?php $OTHrs11_total = $OTHrs11_total + $searchData5['Hrs11'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs12'][0] == ''){echo '';}else{ echo $searchData5['Hrs12'][0];}?></td><?php $OTHrs12_total = $OTHrs12_total + $searchData5['Hrs12'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs13'][0] == ''){echo '';}else{ echo $searchData5['Hrs13'][0];}?></td><?php $OTHrs13_total = $OTHrs13_total + $searchData5['Hrs13'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs14'][0] == ''){echo '';}else{ echo $searchData5['Hrs14'][0];}?></td><?php $OTHrs14_total = $OTHrs14_total + $searchData5['Hrs14'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs15'][0] == ''){echo '';}else{ echo $searchData5['Hrs15'][0];}?></td><?php $OTHrs15_total = $OTHrs15_total + $searchData5['Hrs15'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs16'][0] == ''){echo '';}else{ echo $searchData5['Hrs16'][0];}?></td><?php $OTHrs16_total = $OTHrs16_total + $searchData5['Hrs16'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs17'][0] == ''){echo '';}else{ echo $searchData5['Hrs17'][0];}?></td><?php $OTHrs17_total = $OTHrs17_total + $searchData5['Hrs17'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs18'][0] == ''){echo '';}else{ echo $searchData5['Hrs18'][0];}?></td><?php $OTHrs18_total = $OTHrs18_total + $searchData5['Hrs18'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs19'][0] == ''){echo '';}else{ echo $searchData5['Hrs19'][0];}?></td><?php $OTHrs19_total = $OTHrs19_total + $searchData5['Hrs19'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs20'][0] == ''){echo '';}else{ echo $searchData5['Hrs20'][0];}?></td><?php $OTHrs20_total = $OTHrs20_total + $searchData5['Hrs20'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs21'][0] == ''){echo '';}else{ echo $searchData5['Hrs21'][0];}?></td><?php $OTHrs21_total = $OTHrs21_total + $searchData5['Hrs21'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs22'][0] == ''){echo '';}else{ echo $searchData5['Hrs22'][0];}?></td><?php $OTHrs22_total = $OTHrs22_total + $searchData5['Hrs22'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs23'][0] == ''){echo '';}else{ echo $searchData5['Hrs23'][0];}?></td><?php $OTHrs23_total = $OTHrs23_total + $searchData5['Hrs23'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs24'][0] == ''){echo '';}else{ echo $searchData5['Hrs24'][0];}?></td><?php $OTHrs24_total = $OTHrs24_total + $searchData5['Hrs24'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs25'][0] == ''){echo '';}else{ echo $searchData5['Hrs25'][0];}?></td><?php $OTHrs25_total = $OTHrs25_total + $searchData5['Hrs25'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs26'][0] == ''){echo '';}else{ echo $searchData5['Hrs26'][0];}?></td><?php $OTHrs26_total = $OTHrs26_total + $searchData5['Hrs26'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs27'][0] == ''){echo '';}else{ echo $searchData5['Hrs27'][0];}?></td><?php $OTHrs27_total = $OTHrs27_total + $searchData5['Hrs27'][0];?>
			<td class="slim" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs28'][0] == ''){echo '';}else{ echo $searchData5['Hrs28'][0];}?></td><?php $OTHrs28_total = $OTHrs28_total + $searchData5['Hrs28'][0];?>
			
			
			<?php if ($days_in_month > 28) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs29'][0] == ''){echo '';}else{ echo $searchData5['Hrs29'][0];}?></td><?php $OTHrs29_total = $OTHrs29_total + $searchData5['Hrs29'][0];?>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs30'][0] == ''){echo '';}else{ echo $searchData5['Hrs30'][0];}?></td><?php $OTHrs30_total = $OTHrs30_total + $searchData5['Hrs30'][0];?>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td class="slim" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'bgcolor="cccccc"';} ?>><?php if($searchData5['Hrs31'][0] == ''){echo '';}else{ echo $searchData5['Hrs31'][0];}?></td><?php $OTHrs31_total = $OTHrs31_total + $searchData5['Hrs31'][0];?>
			<?php } ?>
			
			<?php if($today_stamp <= $lockout_day_stamp){ ?>
	
				<td class="slim"><a href="/staff/sims/timesheets.php?action=edit&Timesheet_ID=<?php echo $timesheet_ID;?>&edit_row_ID=<?php echo $searchData5['c_cwp_row_ID'][0];?>" title="Click here to edit this row.">Edit</a></td>
				<td><a href="/staff/sims/timesheet_hrs_delete.php?row_ID=<?php echo $searchData5['c_cwp_row_ID'][0];?>&bgt_auth=<?php echo $searchData5['BudgetAuthorityLocal'][0];?>" onclick="return confirmDelete()" title="Click here to delete this row."><img src="/staff/sims/images/red_cross_small.png" border="0"></a></td>
	
			<?php } else { ?>
	
				<td class="slim" bgcolor="cccccc">&nbsp;</td>
				<td><img src="/staff/sims/images/padlock_blk_small.png" border="0"></a></td>
	
			<?php } ?>
			
	
			
			</tr>
			
											
			
			<?php  } ?>
			
			
			<?php $AllHrs01_total = $AllHrs01_total + $OTHrs01_total;?>
			<?php $AllHrs02_total = $AllHrs02_total + $OTHrs02_total;?>
			<?php $AllHrs03_total = $AllHrs03_total + $OTHrs03_total;?>
			<?php $AllHrs04_total = $AllHrs04_total + $OTHrs04_total;?>
			<?php $AllHrs05_total = $AllHrs05_total + $OTHrs05_total;?>
			<?php $AllHrs06_total = $AllHrs06_total + $OTHrs06_total;?>
			<?php $AllHrs07_total = $AllHrs07_total + $OTHrs07_total;?>
			<?php $AllHrs08_total = $AllHrs08_total + $OTHrs08_total;?>
			<?php $AllHrs09_total = $AllHrs09_total + $OTHrs09_total;?>
			<?php $AllHrs10_total = $AllHrs10_total + $OTHrs10_total;?>
			<?php $AllHrs11_total = $AllHrs11_total + $OTHrs11_total;?>
			<?php $AllHrs12_total = $AllHrs12_total + $OTHrs12_total;?>
			<?php $AllHrs13_total = $AllHrs13_total + $OTHrs13_total;?>
			<?php $AllHrs14_total = $AllHrs14_total + $OTHrs14_total;?>
			<?php $AllHrs15_total = $AllHrs15_total + $OTHrs15_total;?>
			<?php $AllHrs16_total = $AllHrs16_total + $OTHrs16_total;?>
			<?php $AllHrs17_total = $AllHrs17_total + $OTHrs17_total;?>
			<?php $AllHrs18_total = $AllHrs18_total + $OTHrs18_total;?>
			<?php $AllHrs19_total = $AllHrs19_total + $OTHrs19_total;?>
			<?php $AllHrs20_total = $AllHrs20_total + $OTHrs20_total;?>
			<?php $AllHrs21_total = $AllHrs21_total + $OTHrs21_total;?>
			<?php $AllHrs22_total = $AllHrs22_total + $OTHrs22_total;?>
			<?php $AllHrs23_total = $AllHrs23_total + $OTHrs23_total;?>
			<?php $AllHrs24_total = $AllHrs24_total + $OTHrs24_total;?>
			<?php $AllHrs25_total = $AllHrs25_total + $OTHrs25_total;?>
			<?php $AllHrs26_total = $AllHrs26_total + $OTHrs26_total;?>
			<?php $AllHrs27_total = $AllHrs27_total + $OTHrs27_total;?>
			<?php $AllHrs28_total = $AllHrs28_total + $OTHrs28_total;?>
			
			
			
			<?php $AllHrs29_total = $AllHrs29_total + $OTHrs29_total;?>
			
			
			<?php $AllHrs30_total = $AllHrs30_total + $OTHrs30_total;?>
			
			
			<?php $AllHrs31_total = $AllHrs31_total + $OTHrs31_total;?>
			
			
		
	<!--/table-->
	<?php if($today_stamp <= $lockout_day_stamp){ ?>
	
		<tr><td colspan="<?php echo $header_colspan;?>">
		<a href="/staff/sims/timesheets.php?action=edit&new_row=ot&Timesheet_ID=<?php echo $timesheet_ID;?>" title="Click here to add overtime hours to your timesheet.">Add Overtime Hours</a><p>&nbsp;<p>
		</td></tr>
	
	<?php } else { ?>
	
		<tr><td colspan="<?php echo $header_colspan;?>" bgcolor="ebebeb">
		<p>&nbsp;<p>
		</td></tr>
	
	<?php } ?>
	
	
	
	<?php } ?>
	
	<?php }?>
	
	
	<!--END FOURTH SECTION: OVERTIME HOURS BY BUDGET CODE-->
	
	<!--START: GRAND TOTAL HOURS-->
	
	
			<tr>
			<td class="slim_foot" style="text-align:right;padding-right:6px" nowrap align="right" colspan="2"><strong>TOTAL</strong></td>
			<td class="slim_foot"><strong><?php $AllHrsT_total = $WkHrsT_total + $PdLvHrsT_total + $UnPdLvHrsT_total + $OTHrsT_total; echo $AllHrsT_total; $_SESSION['AllHrsT_total'] = $AllHrsT_total;?></strong></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs01_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs02_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs03_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs04_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs05_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs06_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs07_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs08_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs09_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs10_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs11_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs12_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs13_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs14_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs15_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs16_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs17_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs18_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs19_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs20_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs21_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs22_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs23_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs24_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs25_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs26_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs27_total;}?></td>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs28_total;}?></td>
			
			
			<?php if ($days_in_month > 28) { ?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs29_total;}?></td>
			<?php } ?>
			<?php if ($days_in_month > 29) { ?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs30_total;}?></td>
			<?php } ?>
			<?php if ($days_in_month > 30) { ?>
			<td class="slim_foot" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'style="background-color:#888;border:1px solid #888;padding:0px;border-spacing:0px"';} ?>><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo $AllHrs31_total;}?></td>
			<?php } ?>
			<td class="slim_foot" colspan="2" bgcolor="ffffff"><font color="red">(<?php echo $_SESSION['payperiod_workhrs']; ?>)</font></td>
			
			</tr>
	
	<!--END: GRAND TOTAL HOURS-->	
	
			<tr><td colspan="<?php echo $header_colspan;?>" bgcolor="ebebeb">
			</td></tr>

	
	<!--BEGIN FIFTH SECTION: APPROVAL SIGNATURES-->
	<?php
	include_once('timesheets_signature_include2.php'); //modularized the timesheet signature section
	?>
	<!--END FIFTH SECTION: APPROVAL SIGNATURES-->
	
	</td></tr>
	
	
	
	
	
	</table>

</div><!--END grid_16-->


</div><!--END container_16-->

<div class="container_16" style="text-align:center">For technical assistance contact <a href="mailto:sims@sed.org">sims@sedl.org</a>.
</div>
</body>

</html>

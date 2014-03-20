<?php
session_start();

include_once('FX/FX.php');
include_once('FX/server_data.php');

#################################################
## START: GRAB VARIABLES FROM THE TIMESHEET FORM
#################################################
$staff_ID = $_SESSION['staff_ID'];
$pay_period_begin = $_SESSION['last_pay_period_end'] + 1;

$pay_period_begin2 = date($pay_period_begin);

echo 'StaffID: '.$staff_ID;
echo 'PayPeriodBegin: '.$pay_period_begin;

//$action = $_GET['action'];
//$new_row = $_GET['new_row'];
//$row_ID = $_GET['edit_row_ID'];
//$new_row_ID = $_GET['new_row_ID'];
//$days_in_month = $_SESSION['days_in_month'];
//echo '<br>Days in Month: '.$days_in_month;
//$budget_code = $_GET['budget_code'];
#################################################
## END: GRAB VARIABLES FROM THE TIMESHEET FORM
#################################################


#################################################
## START: CREATE A NEW TIMESHEET RECORD
#################################################
$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('SIMS.fp7','cwp_timesheets'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information
$newrecord -> AddDBParam('staff_ID',$staff_ID);
$newrecord -> AddDBParam('PayPeriodBegin',$pay_period_begin);
//$newrecord -> AddDBParam('HrsType','WkHrsReg');

$newrecordResult = $newrecord -> FMNew();

$recordData = current($newrecordResult['data']);

$timesheet_ID = $recordData['Timesheet_ID'][0];
echo 'Timesheet ID: '.$timesheet_ID;
#################################################
## END: CREATE A NEW TIMESHEET RECORD
#################################################

#################################################################
## START: CREATE THE FIRST TIME-HRS ROW FOR REGULAR HOURS
#################################################################
$newrecord2 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord2 -> SetDBData('SIMS.fp7','cwp_time_hrs'); //set dbase information
$newrecord2 -> SetDBPassword($webPW,$webUN); //set password information


###ADD THE SUBMITTED VALUES AS PARAMETERS###
$newrecord2 -> AddDBParam('Timesheet_ID',$timesheet_ID);
$newrecord2 -> AddDBParam('HrsType','WkHrsReg');

###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
$newrecordResult2 = $newrecord2 -> FMNew();

$recordData2 = current($newrecordResult2['data']);

$new_row_ID = $recordData2['c_cwp_row_ID'][0];

//echo 'ErrorCode: '.$newrecordResult2['errorCode'];
//echo '<br>FoundCount: '.$newrecordResult2['foundCount'];
#################################################################
## END: FIND THE FMP TIME_HRS ROW FOR EDITING & GET THE REC-ID
#################################################################

#####################################################
## START: FIND REGULAR WORK HOURS FOR THIS TIMESHEET
#####################################################
//echo $row_ID;
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS.fp7','cwp_time_hrs','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('Timesheet_ID',$timesheet_ID);
$search -> AddDBParam('HrsType','WkHrsReg');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam ($sortfield,'descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);

//$searchResult = $_SESSION['wk_hrs_data'];
$recordData3 = current($searchResult['data']);

$staff_ID = $recordData3['TIMESHEETS::staff_ID'][0];
$days_in_month = $recordData3['c_days_in_month'][0];
#####################################################
## END: FIND REGULAR WORK HOURS FOR THIS TIMESHEET
#####################################################

#####################################################
## START: FIND PAID LEAVE HOURS FOR THIS TIMESHEET
#####################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS.fp7','cwp_time_hrs','all');
$search3 -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search3 -> AddDBParam('Timesheet_ID',$timesheet_ID);
$search3 -> AddDBParam('HrsType','PdLv');
//$search3 -> AddDBParam('-lop','or');

$search3 -> AddSortParam ($sortfield,'descend');


$searchResult3 = $search3 -> FMFind();

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//print_r ($searchResult3);

//$searchResult3 = $_SESSION['pdlv_hrs_data'];
//$recordData3 = current($searchResult3['data']);
#####################################################
## END: FIND PAID LEAVE HOURS FOR THIS TIMESHEET
#####################################################

#####################################################
## START: FIND UNPAID LEAVE HOURS FOR THIS TIMESHEET
#####################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS.fp7','cwp_time_hrs','all');
$search4 -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search4 -> AddDBParam('Timesheet_ID',$timesheet_ID);
$search4 -> AddDBParam('HrsType','UnPdLv');
//$search4 -> AddDBParam('-lop','or');

//$search4 -> AddSortParam ($sortfield,'descend');


$searchResult4 = $search4 -> FMFind();
//$_SESSION['pdlv_hrs_data'] = $searchResult4;

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//print_r ($searchResult3);
$recordData4 = current($searchResult4['data']);

#####################################################
## END: FIND UNPAID LEAVE HOURS FOR THIS TIMESHEET
#####################################################

######################################################
## START: FIND OVERTIME WORK HOURS FOR THIS TIMESHEET
######################################################
/*
//if the staff member is NON-EXEMPT, check for OT hrs --get status from SESSION variable that is set upon login
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS.fp7','cwp_time_hrs','all');
$search4 -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search4 -> AddDBParam('Timesheet_ID',$timesheet_ID);
$search4 -> AddDBParam('HrsType','UnPdLv');
//$search4 -> AddDBParam('-lop','or');

//$search4 -> AddSortParam ($sortfield,'descend');


$searchResult4 = $search4 -> FMFind();
//$_SESSION['pdlv_hrs_data'] = $searchResult4;

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//print_r ($searchResult3);
$recordData3 = current($searchResult3['data']);
*/
#####################################################
## END: FIND OVERTIME WORK HOURS FOR THIS TIMESHEET
#####################################################

#############################################################################################
## START: DISPLAY THE TIMESHEET IN AN HTML TABLE IN VIEW MODE AFTER EDITS ARE CONFIRMED
#############################################################################################
?>
<html>
<head>
<title>SIMS - Timesheets</title>
<link href="/staff/includes/staff2006.css" rel="stylesheet" type="text/css">

<script language="JavaScript">
//<!--
function confirmDelete() { 
	var answer = confirm ("Are you sure you want to delete this row?")
	if (!answer) {
	return false;
	}
}
// -->
</script>

</head>

<BODY BGCOLOR="#092129" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="1100" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#97B039"><td><img src="/staff/sims/images/timesheet_header_top_left.jpg"></td><td width="100%">&nbsp;</td><td align="right"><img src="/staff/sims/images/timesheet_header_top_right.jpg"></td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="1100">
			
			<tr bgcolor="cccccc"><td class="body"><b>SIMS Timesheet: <?php echo $recordData['STAFF::name_timesheet'][0];?> (<?php echo $recordData['STAFF::PrimarySEDLWorkgroup'][0];?>)</b></td><td align="right">Timesheet Status: <?php echo $recordData['TIMESHEETS::c_TimesheetSubmittedStatusFinal'][0];?> | Pay Period Ending: <?php echo $recordData['TIMESHEETS::c_PayPeriodEnd'][0];?></td></tr>
			
			
			
			<tr><td class="body">&nbsp;<i>NOTE: Record Time to Nearest Tenth of an Hour</i></td><td align="right">Timesheet ID: <?php echo $_SESSION['timesheet_ID'];?> | <a href="/staff/sims/menu_timesheets.php">Close Timesheet</a></td></tr>
			<tr><td class="body" colspan=2><strong>Regular Hours by Budget Code:</strong><br>
			
<!--BEGIN FIRST SECTION: REGULAR HOURS BY BUDGET CODE-->

							<table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="body">
									<tr bgcolor="cccccc">
									<td class="body">&nbsp;Budget Code</td>
									<td align="center" class="body"><strong>T</strong></td>
									<td align="center" class="body"><strong>01</strong></td>
									<td align="center" class="body"><strong>02</strong></td>
									<td align="center" class="body"><strong>03</strong></td>
									<td align="center" class="body"><strong>04</strong></td>
									<td align="center" class="body"><strong>05</strong></td>
									<td align="center" class="body"><strong>06</strong></td>
									<td align="center" class="body"><strong>07</strong></td>
									<td align="center" class="body"><strong>08</strong></td>
									<td align="center" class="body"><strong>09</strong></td>
									<td align="center" class="body"><strong>10</strong></td>
									<td align="center" class="body"><strong>11</strong></td>
									<td align="center" class="body"><strong>12</strong></td>
									<td align="center" class="body"><strong>13</strong></td>
									<td align="center" class="body"><strong>14</strong></td>
									<td align="center" class="body"><strong>15</strong></td>
									<td align="center" class="body"><strong>16</strong></td>
									<td align="center" class="body"><strong>17</strong></td>
									<td align="center" class="body"><strong>18</strong></td>
									<td align="center" class="body"><strong>19</strong></td>
									<td align="center" class="body"><strong>20</strong></td>
									<td align="center" class="body"><strong>21</strong></td>
									<td align="center" class="body"><strong>22</strong></td>
									<td align="center" class="body"><strong>23</strong></td>
									<td align="center" class="body"><strong>24</strong></td>
									<td align="center" class="body"><strong>25</strong></td>
									<td align="center" class="body"><strong>26</strong></td>
									<td align="center" class="body"><strong>27</strong></td>
									<td align="center" class="body"><strong>28</strong></td>
									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body"><strong>29</strong></td>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body"><strong>30</strong></td>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body"><strong>31</strong></td>
									<?php } ?>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									</tr>
									
									<?php 
									
									foreach($searchResult['data'] as $key => $searchData) { ?>
									
									<tr>
									<td class="body" nowrap><?php echo $searchData['BudgetCode'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['c_TotalHrs'][0];?></td><?php $WkHrsT_total = $WkHrsT_total + $searchData['c_TotalHrs'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs01'][0];?></td><?php $WkHrs01_total = $WkHrs01_total + $searchData['Hrs01'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs02'][0];?></td><?php $WkHrs02_total = $WkHrs02_total + $searchData['Hrs02'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs03'][0];?></td><?php $WkHrs03_total = $WkHrs03_total + $searchData['Hrs03'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs04'][0];?></td><?php $WkHrs04_total = $WkHrs04_total + $searchData['Hrs04'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs05'][0];?></td><?php $WkHrs05_total = $WkHrs05_total + $searchData['Hrs05'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs06'][0];?></td><?php $WkHrs06_total = $WkHrs06_total + $searchData['Hrs06'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs07'][0];?></td><?php $WkHrs07_total = $WkHrs07_total + $searchData['Hrs07'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs08'][0];?></td><?php $WkHrs08_total = $WkHrs08_total + $searchData['Hrs08'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs09'][0];?></td><?php $WkHrs09_total = $WkHrs09_total + $searchData['Hrs09'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs10'][0];?></td><?php $WkHrs10_total = $WkHrs10_total + $searchData['Hrs10'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs11'][0];?></td><?php $WkHrs11_total = $WkHrs11_total + $searchData['Hrs11'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs12'][0];?></td><?php $WkHrs12_total = $WkHrs12_total + $searchData['Hrs12'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs13'][0];?></td><?php $WkHrs13_total = $WkHrs13_total + $searchData['Hrs13'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs14'][0];?></td><?php $WkHrs14_total = $WkHrs14_total + $searchData['Hrs14'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs15'][0];?></td><?php $WkHrs15_total = $WkHrs15_total + $searchData['Hrs15'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs16'][0];?></td><?php $WkHrs16_total = $WkHrs16_total + $searchData['Hrs16'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs17'][0];?></td><?php $WkHrs17_total = $WkHrs17_total + $searchData['Hrs17'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs18'][0];?></td><?php $WkHrs18_total = $WkHrs18_total + $searchData['Hrs18'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs19'][0];?></td><?php $WkHrs19_total = $WkHrs19_total + $searchData['Hrs19'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs20'][0];?></td><?php $WkHrs20_total = $WkHrs20_total + $searchData['Hrs20'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs21'][0];?></td><?php $WkHrs21_total = $WkHrs21_total + $searchData['Hrs21'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs22'][0];?></td><?php $WkHrs22_total = $WkHrs22_total + $searchData['Hrs22'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs23'][0];?></td><?php $WkHrs23_total = $WkHrs23_total + $searchData['Hrs23'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs24'][0];?></td><?php $WkHrs24_total = $WkHrs24_total + $searchData['Hrs24'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs25'][0];?></td><?php $WkHrs25_total = $WkHrs25_total + $searchData['Hrs25'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs26'][0];?></td><?php $WkHrs26_total = $WkHrs26_total + $searchData['Hrs26'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs27'][0];?></td><?php $WkHrs27_total = $WkHrs27_total + $searchData['Hrs27'][0];?>
									<td align="center" class="body"><?php echo $searchData['Hrs28'][0];?></td><?php $WkHrs28_total = $WkHrs28_total + $searchData['Hrs28'][0];?>
									
									
									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body"><?php echo $searchData['Hrs29'][0];?></td><?php $WkHrs29_total = $WkHrs29_total + $searchData['Hrs29'][0];?>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body"><?php echo $searchData['Hrs30'][0];?></td><?php $WkHrs30_total = $WkHrs30_total + $searchData['Hrs30'][0];?>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body"><?php echo $searchData['Hrs31'][0];?></td><?php $WkHrs31_total = $WkHrs31_total + $searchData['Hrs31'][0];?>
									<?php } ?>
									<td align="center" class="body"><a href="/staff/sims/timesheets.php?action=edit&Timesheet_ID=<?php echo $timesheet_ID;?>&edit_row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>">Edit</a></td>
									<td><a href="/staff/sims/timesheet_delete.php?row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/trashcan.jpg" border="0"></a></td>
									
									
									</tr>
									
									
									<?php  } ?>
									
									<tr bgcolor="#FFFBB8">
									<td class="body" nowrap><strong>Total WorkHrs</strong></td>
									<td align="center" class="body"><?php echo $WkHrsT_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs01_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs02_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs03_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs04_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs05_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs06_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs07_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs08_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs09_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs10_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs11_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs12_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs13_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs14_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs15_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs16_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs17_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs18_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs19_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs20_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs21_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs22_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs23_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs24_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs25_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs26_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs27_total;?></td>
									<td align="center" class="body"><?php echo $WkHrs28_total;?></td>
									
									
									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body"><?php echo $WkHrs29_total;?></td>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body"><?php echo $WkHrs30_total;?></td>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body"><?php echo $WkHrs31_total;?></td>
									<?php } ?>
									<td align="center" class="body" colspan="2">&nbsp;</td>
									
									
									
									</tr>
								
							</table>
							<a href="/staff/sims/timesheets.php?action=edit&new_row=wk&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Regular Hours</a> | Row ID: <?php echo $row_ID;?><p>&nbsp;<p>
			
<!--END FIRST SECTION: REGULAR HOURS BY BUDGET CODE-->


<!--BEGIN SECOND SECTION: PAID LEAVE HOURS-->
			
							<strong>Paid Leave Hours:</strong><br>
							
							<?php 
								if(($searchResult3['foundCount'])==0 && ($new_row !== 'pdlv')){ //show no hours alert ?> 
									
									<p class="alert_small">There are no paid leave hours entered for this timesheet. | <a href="/staff/sims/timesheets.php?action=edit&new_row=pdlv&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Paid Leave Hours</a></p>
								
								<?php }else{ ?>
								
							<table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="body">
									<tr bgcolor="cccccc">
									<td class="body">&nbsp;Leave Type</td>
									<td align="center" class="body"><strong>T</strong></td>
									<td align="center" class="body"><strong>01</strong></td>
									<td align="center" class="body"><strong>02</strong></td>
									<td align="center" class="body"><strong>03</strong></td>
									<td align="center" class="body"><strong>04</strong></td>
									<td align="center" class="body"><strong>05</strong></td>
									<td align="center" class="body"><strong>06</strong></td>
									<td align="center" class="body"><strong>07</strong></td>
									<td align="center" class="body"><strong>08</strong></td>
									<td align="center" class="body"><strong>09</strong></td>
									<td align="center" class="body"><strong>10</strong></td>
									<td align="center" class="body"><strong>11</strong></td>
									<td align="center" class="body"><strong>12</strong></td>
									<td align="center" class="body"><strong>13</strong></td>
									<td align="center" class="body"><strong>14</strong></td>
									<td align="center" class="body"><strong>15</strong></td>
									<td align="center" class="body"><strong>16</strong></td>
									<td align="center" class="body"><strong>17</strong></td>
									<td align="center" class="body"><strong>18</strong></td>
									<td align="center" class="body"><strong>19</strong></td>
									<td align="center" class="body"><strong>20</strong></td>
									<td align="center" class="body"><strong>21</strong></td>
									<td align="center" class="body"><strong>22</strong></td>
									<td align="center" class="body"><strong>23</strong></td>
									<td align="center" class="body"><strong>24</strong></td>
									<td align="center" class="body"><strong>25</strong></td>
									<td align="center" class="body"><strong>26</strong></td>
									<td align="center" class="body"><strong>27</strong></td>
									<td align="center" class="body"><strong>28</strong></td>
									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body"><strong>29</strong></td>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body"><strong>30</strong></td>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body"><strong>31</strong></td>
									<?php } ?>
									<td colspan="2">&nbsp;</td>
									</tr>
									
									
									<?php
								
								
									foreach($searchResult3['data'] as $key => $searchData3) { ?>
									
									<tr>
									<td class="body" nowrap><?php echo $searchData3['LvType'][0];?></td>
									<td align="center" class="body"><?php echo $searchData3['c_TotalHrs'][0];?></td><?php $PdLvHrsT_total = $PdLvHrsT_total + $searchData3['c_TotalHrs'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs01'][0];?></td><?php $PdLvHrs01_total = $PdLvHrs01_total + $searchData3['Hrs01'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs02'][0];?></td><?php $PdLvHrs02_total = $PdLvHrs02_total + $searchData3['Hrs02'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs03'][0];?></td><?php $PdLvHrs03_total = $PdLvHrs03_total + $searchData3['Hrs03'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs04'][0];?></td><?php $PdLvHrs04_total = $PdLvHrs04_total + $searchData3['Hrs04'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs05'][0];?></td><?php $PdLvHrs05_total = $PdLvHrs05_total + $searchData3['Hrs05'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs06'][0];?></td><?php $PdLvHrs06_total = $PdLvHrs06_total + $searchData3['Hrs06'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs07'][0];?></td><?php $PdLvHrs07_total = $PdLvHrs07_total + $searchData3['Hrs07'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs08'][0];?></td><?php $PdLvHrs08_total = $PdLvHrs08_total + $searchData3['Hrs08'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs09'][0];?></td><?php $PdLvHrs09_total = $PdLvHrs09_total + $searchData3['Hrs09'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs10'][0];?></td><?php $PdLvHrs10_total = $PdLvHrs10_total + $searchData3['Hrs10'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs11'][0];?></td><?php $PdLvHrs11_total = $PdLvHrs11_total + $searchData3['Hrs11'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs12'][0];?></td><?php $PdLvHrs12_total = $PdLvHrs12_total + $searchData3['Hrs12'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs13'][0];?></td><?php $PdLvHrs13_total = $PdLvHrs13_total + $searchData3['Hrs13'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs14'][0];?></td><?php $PdLvHrs14_total = $PdLvHrs14_total + $searchData3['Hrs14'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs15'][0];?></td><?php $PdLvHrs15_total = $PdLvHrs15_total + $searchData3['Hrs15'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs16'][0];?></td><?php $PdLvHrs16_total = $PdLvHrs16_total + $searchData3['Hrs16'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs17'][0];?></td><?php $PdLvHrs17_total = $PdLvHrs17_total + $searchData3['Hrs17'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs18'][0];?></td><?php $PdLvHrs18_total = $PdLvHrs18_total + $searchData3['Hrs18'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs19'][0];?></td><?php $PdLvHrs19_total = $PdLvHrs19_total + $searchData3['Hrs19'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs20'][0];?></td><?php $PdLvHrs20_total = $PdLvHrs20_total + $searchData3['Hrs20'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs21'][0];?></td><?php $PdLvHrs21_total = $PdLvHrs21_total + $searchData3['Hrs21'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs22'][0];?></td><?php $PdLvHrs22_total = $PdLvHrs22_total + $searchData3['Hrs22'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs23'][0];?></td><?php $PdLvHrs23_total = $PdLvHrs23_total + $searchData3['Hrs23'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs24'][0];?></td><?php $PdLvHrs24_total = $PdLvHrs24_total + $searchData3['Hrs24'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs25'][0];?></td><?php $PdLvHrs25_total = $PdLvHrs25_total + $searchData3['Hrs25'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs26'][0];?></td><?php $PdLvHrs26_total = $PdLvHrs26_total + $searchData3['Hrs26'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs27'][0];?></td><?php $PdLvHrs27_total = $PdLvHrs27_total + $searchData3['Hrs27'][0];?>
									<td align="center" class="body"><?php echo $searchData3['Hrs28'][0];?></td><?php $PdLvHrs28_total = $PdLvHrs28_total + $searchData3['Hrs28'][0];?>
									
									
									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body"><?php echo $searchData3['Hrs29'][0];?></td><?php $PdLvHrs29_total = $PdLvHrs29_total + $searchData3['Hrs29'][0];?>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body"><?php echo $searchData3['Hrs30'][0];?></td><?php $PdLvHrs30_total = $PdLvHrs30_total + $searchData3['Hrs30'][0];?>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body"><?php echo $searchData3['Hrs31'][0];?></td><?php $PdLvHrs31_total = $PdLvHrs31_total + $searchData3['Hrs31'][0];?>
									<?php } ?>
									<td align="center" class="body"><a href="/staff/sims/timesheets.php?action=edit&Timesheet_ID=<?php echo $timesheet_ID;?>&edit_row_ID=<?php echo $searchData3['c_cwp_row_ID'][0];?>">Edit</a></td>
									<td><a href="/staff/sims/timesheet_delete.php?row_ID=<?php echo $searchData3['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/trashcan.jpg" border="0"></a></td>
									
									
									</tr>
									
									
									<?php  } ?>
									
									<tr bgcolor="#FFFBB8">
									<td class="body" nowrap><strong>Total LeaveHrs</strong></td>
									<td align="center" class="body"><?php echo $PdLvHrsT_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs01_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs02_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs03_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs04_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs05_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs06_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs07_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs08_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs09_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs10_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs11_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs12_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs13_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs14_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs15_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs16_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs17_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs18_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs19_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs20_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs21_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs22_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs23_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs24_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs25_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs26_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs27_total;?></td>
									<td align="center" class="body"><?php echo $PdLvHrs28_total;?></td>
									
									
									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body"><?php echo $PdLvHrs29_total;?></td>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body"><?php echo $PdLvHrs30_total;?></td>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body"><?php echo $PdLvHrs31_total;?></td>
									<?php } ?>
									<td align="center" class="body" colspan="2">&nbsp;</td>
									
									</tr>
								
							</table>
							<a href="/staff/sims/timesheets.php?action=edit&new_row=pdlv&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Paid Leave Hours</a> | Row ID: <?php echo $row_ID;?><p>&nbsp;<p>
							<?php } ?>
							
<!--END SECOND SECTION: PAID LEAVE HOURS-->	

<!--BEGIN THIRD SECTION: UNPAID LEAVE HOURS-->
			
							<strong>Unpaid Leave Hours:</strong><br>
							
							<?php 
								if($searchResult4['foundCount']==0){ ?>
									
									<p class="alert_small">There are no unpaid leave hours entered for this timesheet. | <a href="/staff/sims/timesheets.php?action=edit&new_row=unpdlv&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Unpaid Leave Hours</a></p>
								
								<?php }else{ ?>
								
							<table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="body">
									<tr bgcolor="cccccc">
									<td class="body">&nbsp;Leave Type</td>
									<td align="center" class="body"><strong>T</strong></td>
									<td align="center" class="body"><strong>01</strong></td>
									<td align="center" class="body"><strong>02</strong></td>
									<td align="center" class="body"><strong>03</strong></td>
									<td align="center" class="body"><strong>04</strong></td>
									<td align="center" class="body"><strong>05</strong></td>
									<td align="center" class="body"><strong>06</strong></td>
									<td align="center" class="body"><strong>07</strong></td>
									<td align="center" class="body"><strong>08</strong></td>
									<td align="center" class="body"><strong>09</strong></td>
									<td align="center" class="body"><strong>10</strong></td>
									<td align="center" class="body"><strong>11</strong></td>
									<td align="center" class="body"><strong>12</strong></td>
									<td align="center" class="body"><strong>13</strong></td>
									<td align="center" class="body"><strong>14</strong></td>
									<td align="center" class="body"><strong>15</strong></td>
									<td align="center" class="body"><strong>16</strong></td>
									<td align="center" class="body"><strong>17</strong></td>
									<td align="center" class="body"><strong>18</strong></td>
									<td align="center" class="body"><strong>19</strong></td>
									<td align="center" class="body"><strong>20</strong></td>
									<td align="center" class="body"><strong>21</strong></td>
									<td align="center" class="body"><strong>22</strong></td>
									<td align="center" class="body"><strong>23</strong></td>
									<td align="center" class="body"><strong>24</strong></td>
									<td align="center" class="body"><strong>25</strong></td>
									<td align="center" class="body"><strong>26</strong></td>
									<td align="center" class="body"><strong>27</strong></td>
									<td align="center" class="body"><strong>28</strong></td>
									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body"><strong>29</strong></td>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body"><strong>30</strong></td>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body"><strong>31</strong></td>
									<?php } ?>
									<td colspan="2">&nbsp;</td>
									</tr>
									
								
									<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
									
									<tr>
									<td class="body" nowrap><?php echo $searchData4['LvType'][0];?></td>
									<td align="center" class="body"><?php echo $searchData4['c_TotalHrs'][0];?></td><?php $UnPdLvHrsT_total = $UnPdLvHrsT_total + $searchData4['c_TotalHrs'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs01'][0];?></td><?php $UnPdLvHrs01_total = $UnPdLvHrs01_total + $searchData4['Hrs01'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs02'][0];?></td><?php $UnPdLvHrs02_total = $UnPdLvHrs02_total + $searchData4['Hrs02'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs03'][0];?></td><?php $UnPdLvHrs03_total = $UnPdLvHrs03_total + $searchData4['Hrs03'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs04'][0];?></td><?php $UnPdLvHrs04_total = $UnPdLvHrs04_total + $searchData4['Hrs04'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs05'][0];?></td><?php $UnPdLvHrs05_total = $UnPdLvHrs05_total + $searchData4['Hrs05'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs06'][0];?></td><?php $UnPdLvHrs06_total = $UnPdLvHrs06_total + $searchData4['Hrs06'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs07'][0];?></td><?php $UnPdLvHrs07_total = $UnPdLvHrs07_total + $searchData4['Hrs07'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs08'][0];?></td><?php $UnPdLvHrs08_total = $UnPdLvHrs08_total + $searchData4['Hrs08'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs09'][0];?></td><?php $UnPdLvHrs09_total = $UnPdLvHrs09_total + $searchData4['Hrs09'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs10'][0];?></td><?php $UnPdLvHrs10_total = $UnPdLvHrs10_total + $searchData4['Hrs10'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs11'][0];?></td><?php $UnPdLvHrs11_total = $UnPdLvHrs11_total + $searchData4['Hrs11'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs12'][0];?></td><?php $UnPdLvHrs12_total = $UnPdLvHrs12_total + $searchData4['Hrs12'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs13'][0];?></td><?php $UnPdLvHrs13_total = $UnPdLvHrs13_total + $searchData4['Hrs13'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs14'][0];?></td><?php $UnPdLvHrs14_total = $UnPdLvHrs14_total + $searchData4['Hrs14'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs15'][0];?></td><?php $UnPdLvHrs15_total = $UnPdLvHrs15_total + $searchData4['Hrs15'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs16'][0];?></td><?php $UnPdLvHrs16_total = $UnPdLvHrs16_total + $searchData4['Hrs16'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs17'][0];?></td><?php $UnPdLvHrs17_total = $UnPdLvHrs17_total + $searchData4['Hrs17'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs18'][0];?></td><?php $UnPdLvHrs18_total = $UnPdLvHrs18_total + $searchData4['Hrs18'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs19'][0];?></td><?php $UnPdLvHrs19_total = $UnPdLvHrs19_total + $searchData4['Hrs19'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs20'][0];?></td><?php $UnPdLvHrs20_total = $UnPdLvHrs20_total + $searchData4['Hrs20'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs21'][0];?></td><?php $UnPdLvHrs21_total = $UnPdLvHrs21_total + $searchData4['Hrs21'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs22'][0];?></td><?php $UnPdLvHrs22_total = $UnPdLvHrs22_total + $searchData4['Hrs22'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs23'][0];?></td><?php $UnPdLvHrs23_total = $UnPdLvHrs23_total + $searchData4['Hrs23'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs24'][0];?></td><?php $UnPdLvHrs24_total = $UnPdLvHrs24_total + $searchData4['Hrs24'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs25'][0];?></td><?php $UnPdLvHrs25_total = $UnPdLvHrs25_total + $searchData4['Hrs25'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs26'][0];?></td><?php $UnPdLvHrs26_total = $UnPdLvHrs26_total + $searchData4['Hrs26'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs27'][0];?></td><?php $UnPdLvHrs27_total = $UnPdLvHrs27_total + $searchData4['Hrs27'][0];?>
									<td align="center" class="body"><?php echo $searchData4['Hrs28'][0];?></td><?php $UnPdLvHrs28_total = $UnPdLvHrs28_total + $searchData4['Hrs28'][0];?>
									
									
									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body"><?php echo $searchData4['Hrs29'][0];?></td><?php $UnPdLvHrs29_total = $UnPdLvHrs29_total + $searchData4['Hrs29'][0];?>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body"><?php echo $searchData4['Hrs30'][0];?></td><?php $UnPdLvHrs30_total = $UnPdLvHrs30_total + $searchData4['Hrs30'][0];?>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body"><?php echo $searchData4['Hrs31'][0];?></td><?php $UnPdLvHrs31_total = $UnPdLvHrs31_total + $searchData4['Hrs31'][0];?>
									<?php } ?>
									<td align="center" class="body"><a href="/staff/sims/timesheets.php?action=edit&Timesheet_ID=<?php echo $timesheet_ID;?>&edit_row_ID=<?php echo $searchData4['c_cwp_row_ID'][0];?>">Edit</a></td>
									<td><a href="/staff/sims/timesheet_delete.php?row_ID=<?php echo $searchData4['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/trashcan.jpg" border="0"></a></td>
									
									
									</tr>
									
									
									<?php  } ?>
									
									<tr bgcolor="#FFFBB8">
									<td class="body" nowrap><strong>Total LeaveHrs</strong></td>
									<td align="center" class="body"><?php echo $UnPdLvHrsT_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs01_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs02_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs03_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs04_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs05_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs06_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs07_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs08_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs09_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs10_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs11_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs12_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs13_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs14_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs15_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs16_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs17_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs18_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs19_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs20_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs21_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs22_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs23_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs24_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs25_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs26_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs27_total;?></td>
									<td align="center" class="body"><?php echo $UnPdLvHrs28_total;?></td>
									
									
									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body"><?php echo $UnPdLvHrs29_total;?></td>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body"><?php echo $UnPdLvHrs30_total;?></td>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body"><?php echo $UnPdLvHrs31_total;?></td>
									<?php } ?>
									<td align="center" class="body" colspan="2">&nbsp;</td>
									
									</tr>
								
							</table>
							<a href="/staff/sims/timesheets.php?action=edit&new_row=unpdlv&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Unpaid Leave Hours</a> | Row ID: <?php echo $row_ID;?><p>&nbsp;<p>
							<?php } ?>
							
<!--END THIRD SECTION: UNPAID LEAVE HOURS-->
							
			
			
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>






</body>

</html>




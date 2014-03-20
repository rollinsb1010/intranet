<?php
include_once('FX/FX.php');
include_once('FX/server_data.php');

//$sortfield = $_GET['sortfield'];
$timesheet_ID = $_GET['Timesheet_ID'];
$action = $_GET['action'];

$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS.fp7','cwp_time_hrs','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('c_TimesheetID_AllHrs',$timesheet_ID);
$search -> AddDBParam('HrsType','WkHrsReg');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam ($sortfield,'descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);



$staff_ID = $recordData['TIMESHEETS::staff_ID'][0];


?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
if (($searchResult['foundCount'] > 0) && ($action == 'view')) { 
?>

<html>
<head>
<title>SIMS - Timesheets</title>
<link href="/staff/includes/staff2006.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#092129" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td><img src="/staff/sims/images/timesheet_header.jpg"></td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="930">
			
			<tr><td class="body"><b>SIMS Timesheet</b>: <?php echo $recordData['STAFF::name_timesheet'][0];?></td><td class="body" align="right">Pay Period Ending: <?php echo $recordData['TIMESHEETS::c_PayPeriodEnd'][0];?></td></tr>
			
			
			<tr><td class="body" align="right" colspan=2><?php if($recordData['TIMESHEETS::c_TimesheetSubmittedStatusFinal'][0] == "Approved"){ ?>Timesheet Locked <img src="/staff/sims/images/padlock.jpg"><?php } else { ?><a href="/staff/sims/sims_test_3.php?action=edit&Timesheet_ID=<?php echo $timesheet_ID;?>">Edit this Timesheet</a><?php } ?></td></tr>
			
			
			<tr bgcolor="cccccc"><td class="body" colspan=2>Budget Code (Days in month: <?php echo $recordData['c_days_in_month'][0];?>)</td></tr>
			
			
			
			<tr><td class="body" colspan=2>
			
			
							<table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="body">
									<tr bgcolor="cccccc">
									<td class="body">&nbsp;</td>
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
									<?php if ($recordData['c_days_in_month'][0] > 28) { ?>
									<td align="center" class="body"><strong>29</strong></td>
									<?php } ?>
									<?php if ($recordData['c_days_in_month'][0] > 29) { ?>
									<td align="center" class="body"><strong>30</strong></td>
									<?php } ?>
									<?php if ($recordData['c_days_in_month'][0] > 30) { ?>
									<td align="center" class="body"><strong>31</strong></td>
									<?php } ?>
									</tr>
									
									<?php 
									
									foreach($searchResult['data'] as $key => $searchData) { ?>
									
									<tr>
									<td class="body" nowrap><?php echo $searchData['BudgetCode'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs01'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs02'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs03'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs04'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs05'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs06'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs07'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs08'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs09'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs10'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs11'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs12'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs13'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs14'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs15'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs16'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs17'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs18'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs19'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs20'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs21'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs22'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs23'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs24'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs25'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs26'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs27'][0];?></td>
									<td align="center" class="body"><?php echo $searchData['Hrs28'][0];?></td>
									
									
									<?php if ($searchData['c_days_in_month'][0] > 28) { ?>
									<td align="center" class="body"><?php echo $searchData['Hrs29'][0];?></td>
									<?php } ?>
									<?php if ($searchData['c_days_in_month'][0] > 29) { ?>
									<td align="center" class="body"><?php echo $searchData['Hrs30'][0];?></td>
									<?php } ?>
									<?php if ($searchData['c_days_in_month'][0] > 30) { ?>
									<td align="center" class="body"><?php echo $searchData['Hrs31'][0];?></td>
									<?php } ?>
									</tr>
									
									
									<?php  } ?>
								
							</table>
			
			
			
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>






</body>

</html>

<?php } elseif (($searchResult['foundCount'] > 0) && ($action == 'edit')) { 

$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS.fp7','cwp_budget_code_usage','all');
$search2 -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search2 -> AddDBParam('staff_ID',$staff_ID);
//$search -> AddDBParam('HrsType','WkHrsReg');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam ($sortfield,'descend');


$searchResult2 = $search2 -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData2 = current($searchResult2['data']);


###QUERY FMP LAYOUT FOR VALUELISTS###

$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('SIMS.fp7','cwp_staff');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();

//echo $v1Result['errorCode'];



?>


<html>
<head>
<title>SIMS - Timesheets</title>
<link href="/staff/includes/staff2006.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#092129" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td><img src="/staff/sims/images/timesheet_header.jpg"></td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="930">
			
			<tr><td class="body"><b>SIMS Timesheet</b>: <?php echo $recordData['STAFF::name_timesheet'][0];?></td><td class="body" align="right">Pay Period Ending: <?php echo $recordData['TIMESHEETS::c_PayPeriodEnd'][0];?></td></tr>
			
			
			<tr><td class="body" align="right" colspan=2>&nbsp;</td></tr>
			
			
			<tr bgcolor="cccccc"><td class="body">Budget Code (Days in month: <?php echo $recordData['c_days_in_month'][0];?>)</td><td align="right"><i>NOTE: Record Time to Nearest Tenth of an Hour</i></td></tr>
			
			
			
			<tr><td class="body" colspan=2>
			<form name="timesheet">
			<input type="hidden" name="timesheet_ID" value="<?php echo $timesheet_ID;?>">
			<input type="hidden" name="action" value="submit">
			
							<table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="body">
									<tr bgcolor="cccccc">
									<td class="body">&nbsp;</td>
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
									<?php if ($recordData['c_days_in_month'][0] > 28) { ?>
									<td align="center" class="body"><strong>29</strong></td>
									<?php } ?>
									<?php if ($recordData['c_days_in_month'][0] > 29) { ?>
									<td align="center" class="body"><strong>30</strong></td>
									<?php } ?>
									<?php if ($recordData['c_days_in_month'][0] > 30) { ?>
									<td align="center" class="body"><strong>31</strong></td>
									<?php } ?>
									</tr>
									
									<?php 
									
									foreach($searchResult['data'] as $key => $searchData) { ?>
									
									<tr>
									<td class="body" nowrap>
										<select name="budget_code" class="body">
										<option value="choose">&gt;&gt; Bgt Code</option>
										
										<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
										<option value="<?php echo $searchData2['budget_code'][0];?>" <?php if($budget_code == $searchData2['budget_code'][0]){echo 'SELECTED';}?>> <?php echo $searchData2['budget_code'][0]; ?></option>
										<?php } ?>
										</select>
									
									</td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_01" value="<?php echo $searchData['Hrs01'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_02" value="<?php echo $searchData['Hrs02'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_03" value="<?php echo $searchData['Hrs03'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_04" value="<?php echo $searchData['Hrs04'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_05" value="<?php echo $searchData['Hrs05'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_06" value="<?php echo $searchData['Hrs06'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_07" value="<?php echo $searchData['Hrs07'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_08" value="<?php echo $searchData['Hrs08'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_09" value="<?php echo $searchData['Hrs09'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_10" value="<?php echo $searchData['Hrs10'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_11" value="<?php echo $searchData['Hrs11'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_12" value="<?php echo $searchData['Hrs12'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_13" value="<?php echo $searchData['Hrs13'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_14" value="<?php echo $searchData['Hrs14'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_15" value="<?php echo $searchData['Hrs15'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_16" value="<?php echo $searchData['Hrs16'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_17" value="<?php echo $searchData['Hrs17'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_18" value="<?php echo $searchData['Hrs18'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_19" value="<?php echo $searchData['Hrs19'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_20" value="<?php echo $searchData['Hrs20'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_21" value="<?php echo $searchData['Hrs21'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_22" value="<?php echo $searchData['Hrs22'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_23" value="<?php echo $searchData['Hrs23'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_24" value="<?php echo $searchData['Hrs24'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_25" value="<?php echo $searchData['Hrs25'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_26" value="<?php echo $searchData['Hrs26'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_27" value="<?php echo $searchData['Hrs27'][0];?>"></td>
									<td align="center" class="body"><input type="text" size="3" name="hrs_28" value="<?php echo $searchData['Hrs28'][0];?>"></td>
									
									
									<?php if ($searchData['c_days_in_month'][0] > 28) { ?>
									<td align="center" class="body"><input type="text" size="3" name="hrs_29" value="<?php echo $searchData['Hrs29'][0];?>"></td>
									<?php } ?>
									<?php if ($searchData['c_days_in_month'][0] > 29) { ?>
									<td align="center" class="body"><input type="text" size="3" name="hrs_30" value="<?php echo $searchData['Hrs30'][0];?>"></td>
									<?php } ?>
									<?php if ($searchData['c_days_in_month'][0] > 30) { ?>
									<td align="center" class="body"><input type="text" size="3" name="hrs_31" value="<?php echo $searchData['Hrs31'][0];?>"></td>
									<?php } ?>
									</tr>
									
									
									<?php  } ?>
								
							</table>
			
			
			
			</td></tr>
			<tr><td colspan="2" align="right">
			
			<input type="submit" name="submit" value="Submit">
			</form>
			
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>






</body>

</html>



<?php } else { ?>

No records found.

<?php } ?>
<?php
include_once('FX/FX.php');
include_once('FX/server_data.php');

$sortfield = $_GET['sortfield'];

$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS.fp7','cwp_timesheets','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','74');
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam($sortfield,'descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);


?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>Search Results</title>
<link href="/staff/includes/staff2006.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#092129" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td><img src="/staff/sims/images/timesheet_header.jpg"></td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
			<tr><td class="body" colspan=5><b>SIMS Timesheets</b>: <?php echo $recordData['STAFF::name_timesheet'][0];?></td></tr>
			
			
			<tr><td class="body" colspan=5><b><?php echo $searchResult['foundCount']; echo ' records found'; ?></b></td></tr>
			
			
			<tr><td>
			
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="cccccc" bgcolor="ffffff" width="100%">
						<tr bgcolor="cccccc">
						
						<td class="body"><a href="sims_test_2.php?sortfield=TimesheetID">ID</a></td>
						<td class="body"><a href="sims_test_2.php?sortfield=c_PayPeriodEnd">Pay Period Ending</a></td>
						<td class="body" align="right">RegHrs</td>
						<td class="body" align="right">Vac</td>
						<td class="body" align="right">Sick</td>
						<td class="body" align="right">Pers</td>
						<td class="body" align="right">UnPdLv</td>
						<td class="body" align="right">OT</td>
						<td class="body">Date/Time Submitted</td>
						<td class="body" align="right">Status</td></tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="body"><?php echo $searchData['TimesheetID'][0];?></td>
						<td class="body"><a href="/staff/sims/sims_test_3.php?Timesheet_ID=<?php echo $searchData['TimesheetID'][0];?>&action=view"><?php echo $searchData['c_PayPeriodEnd'][0];?></a></td>
						
						<td class="body" align="right"><?php echo $searchData['SubTotalRegHrsTotal'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['c_TotalVacationLvHrs'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['c_TotalSickLvHrs'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['c_TotalPersonalLvHrs'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['c_TotalLeaveWoPayLvHrs'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['c_TotalOTHrs'][0];?></td>
						
						<td class="body"><?php echo $searchData['DigitalSignatureStaff_Timestamp'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['c_TimesheetSubmittedStatusFinal'][0];?></td>
						</tr>
			
						<?php } ?>
						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>

<?php //} else { ?>

No records found.

<?php //} ?>
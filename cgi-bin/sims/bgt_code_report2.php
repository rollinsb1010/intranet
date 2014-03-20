<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2008 by SEDL
#
# Written by Eric Waters 03/12/2008
#############################################################################

#################################
## START: LOAD FX.PHP INCLUDES ##
#################################
include_once('FX/FX.php');
include_once('FX/server_data.php');
###############################
## END: LOAD FX.PHP INCLUDES ##
###############################

################################
## START: GRAB FORM VARIABLES ##
################################
$action = $_GET['action'];
$report_type = $_GET['report_type'];
//exit;
##############################
## END: GRAB FORM VARIABLES ##
##############################

if($action == 'new'){


############################################################
## START: GRAB BUDGET CODES FOR THIS DROP-DOWN MENU ##
############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search2 -> SetDBPassword($webPW,$webUN);
//$search2 -> AddDBParam('c_Active_Status','Active');

$search2 -> AddSortParam('c_BudgetCode','ascend');

$searchResult2 = $search2 -> FMFindall();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);
##########################################################
## END: GRAB BUDGET CODES FOR THIS DROP-DOWN MENU ##
##########################################################

#####################################
## START: GENERATE FUND YEAR ARRAY ##
#####################################
$i=0;
foreach($searchResult2['data'] as $key => $searchData2) {
$fund_year_list[$i] = $searchData2['c_fund_year'][0];
$i++;
} 
$fund_year_unique = array_unique($fund_year_list);
#####################################
## END: GENERATE FUND YEAR ARRAY ##
#####################################

############################################################
## START: GRAB STAFF IDs FOR THIS DROP-DOWN MENU ##
############################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','staff','all');
$search3 -> SetDBPassword($webPW,$webUN);
//$search3 -> AddDBParam('current_employee_status','SEDL Employee');

$search3 -> AddSortParam('sims_user_ID','ascend');

$searchResult3 = $search3 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);
##########################################################
## END: GRAB STAFF IDs FOR THIS DROP-DOWN MENU ##
##########################################################

#########################################
## START: DISPLAY NEW BUDGET CODE FORM ## 
#########################################
?>


<html>
<head>
<title>SIMS - Budget Code Report</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">
function checkFields() { 

	// Begin Date_m
		if (document.form2.begin_date_m.value =="") {
			alert("Enter a begin date (month) for this report.");
			document.form2.begin_date_m.focus();
			return false;	}

	// Begin Date_d
		if (document.form2.begin_date_d.value =="") {
			alert("Enter a begin date (day) for this report.");
			document.form2.begin_date_d.focus();
			return false;	}

	// Begin Date_y
		if (document.form2.begin_date_y.value =="") {
			alert("Enter a begin date (year) for this report.");
			document.form2.begin_date_y.focus();
			return false;	}

	// End Date_m
		if (document.form2.end_date_m.value =="") {
			alert("Enter an end date (month) for this report.");
			document.form2.end_date_m.focus();
			return false;	}

	// End Date_d
		if (document.form2.end_date_d.value =="") {
			alert("Enter an end date (day) for this report.");
			document.form2.end_date_d.focus();
			return false;	}

	// End Date_y
		if (document.form2.end_date_y.value =="") {
			alert("Enter an end date (year) for this report.");
			document.form2.end_date_y.focus();
			return false;	}

	// Budget Code - Fund Year
		if ((document.form2.budget_code.value =="")&&(document.form2.fund_year.value =="")) {
			alert("Select either a budget code or fund year for this report.");
			document.form2.budget_code.focus();
			return false;	}

}


</script>

<script language="JavaScript">
function checkFields2() { 

	// Begin Date_m
		if (document.form3.begin_date2_m.value =="") {
			alert("Enter a begin date (month) for this report.");
			document.form3.begin_date2_m.focus();
			return false;	}

	// Begin Date_d
		if (document.form3.begin_date2_d.value =="") {
			alert("Enter a begin date (day) for this report.");
			document.form3.begin_date2_d.focus();
			return false;	}

	// Begin Date_y
		if (document.form3.begin_date2_y.value =="") {
			alert("Enter a begin date (year) for this report.");
			document.form3.begin_date2_y.focus();
			return false;	}

	// End Date_m
		if (document.form3.end_date2_m.value =="") {
			alert("Enter an end date (month) for this report.");
			document.form3.end_date2_m.focus();
			return false;	}

	// End Date_d
		if (document.form3.end_date2_d.value =="") {
			alert("Enter an end date (day) for this report.");
			document.form3.end_date2_d.focus();
			return false;	}

	// End Date_y
		if (document.form3.end_date2_y.value =="") {
			alert("Enter an end date (year) for this report.");
			document.form3.end_date2_y.focus();
			return false;	}

	// Staff
		if (document.form3.staff_id2.value =="") {
			alert("Select a staff ID for this report.");
			document.form3.staff_id2.focus();
			return false;	}

	// Summarize By
	
		if (!document.form3.summarize_by[0].checked && !document.form3.summarize_by[1].checked) {
			// no radio button is selected
			alert("Indicate how to summarize this report.");
			document.form3.summarize_by[0].focus();
			return false;
		}



}


</script>



</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Reports</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>Budget Code Report Setup</strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: These reports returns current live data from budget code hours entered on staff timesheets.</p></td></tr>

			<tr><td class="body" colspan=2>


							<table cellspacing="0" cellpadding="1" width="100%" border="1" bordercolor="#cccccc">
							

							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;Choose Report</td></tr>
							
							<tr><td class="body" valign="top" width="100%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								<tr><td valign="top">


											<table cellspacing="0" cellpadding="5" border="0" width="100%">
											<tr><td colspan="2"><strong>Budget Code Summary by Date Range:</strong></td></tr>
											<form id="form2" name="form2" onsubmit="return checkFields()">
											<input type="hidden" name="action" value="run_bgt_cd_sum">
										
											
											<tr valign="middle"><td align="right" nowrap><font color="666666">Date Range Begin:</font></td><td width="100%">

												<select name="begin_date_m" class="body">
												<option value="">
												
												<option value="1">Jan</option>
												<option value="2">Feb</option>
												<option value="3">Mar</option>
												<option value="4">Apr</option>
												<option value="5">May</option>
												<option value="6">Jun</option>
												<option value="7">Jul</option>
												<option value="8">Aug</option>
												<option value="9">Sep</option>
												<option value="10">Oct</option>
												<option value="11">Nov</option>
												<option value="12">Dec</option>
												</select>

												<select name="begin_date_d" class="body">
												<option value="">
												
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="6">6</option>
												<option value="7">7</option>
												<option value="8">8</option>
												<option value="9">9</option>
												<option value="10">10</option>
												<option value="11">11</option>
												<option value="12">12</option>
												<option value="13">13</option>
												<option value="14">14</option>
												<option value="15">15</option>
												<option value="16">16</option>
												<option value="17">17</option>
												<option value="18">18</option>
												<option value="19">19</option>
												<option value="20">20</option>
												<option value="21">21</option>
												<option value="22">22</option>
												<option value="23">23</option>
												<option value="24">24</option>
												<option value="25">25</option>
												<option value="26">26</option>
												<option value="27">27</option>
												<option value="28">28</option>
												<option value="29">29</option>
												<option value="30">30</option>
												<option value="31">31</option>
												</select>

												<select name="begin_date_y" class="body">
												<option value="">
												
												<option value="2008">2008</option>
												<option value="2009">2009</option>
												<option value="2010">2010</option>
												<option value="2011">2011</option>
												<option value="2012">2012</option>
												</select>

											</td></tr>
											
											<tr valign="middle"><td align="right" nowrap><font color="666666">Date Range End:</font></td><td nowrap>

												<select name="end_date_m" class="body">
												<option value="">
												
												<option value="1">Jan</option>
												<option value="2">Feb</option>
												<option value="3">Mar</option>
												<option value="4">Apr</option>
												<option value="5">May</option>
												<option value="6">Jun</option>
												<option value="7">Jul</option>
												<option value="8">Aug</option>
												<option value="9">Sep</option>
												<option value="10">Oct</option>
												<option value="11">Nov</option>
												<option value="12">Dec</option>
												</select>

												<select name="end_date_d" class="body">
												<option value="">
												
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="6">6</option>
												<option value="7">7</option>
												<option value="8">8</option>
												<option value="9">9</option>
												<option value="10">10</option>
												<option value="11">11</option>
												<option value="12">12</option>
												<option value="13">13</option>
												<option value="14">14</option>
												<option value="15">15</option>
												<option value="16">16</option>
												<option value="17">17</option>
												<option value="18">18</option>
												<option value="19">19</option>
												<option value="20">20</option>
												<option value="21">21</option>
												<option value="22">22</option>
												<option value="23">23</option>
												<option value="24">24</option>
												<option value="25">25</option>
												<option value="26">26</option>
												<option value="27">27</option>
												<option value="28">28</option>
												<option value="29">29</option>
												<option value="30">30</option>
												<option value="31">31</option>
												</select>

												<select name="end_date_y" class="body">
												<option value="">
												
												<option value="2008">2008</option>
												<option value="2009">2009</option>
												<option value="2010">2010</option>
												<option value="2011">2011</option>
												<option value="2012">2012</option>
												</select>

											</td></tr>
											
											<tr valign="middle"><td align="right"><font color="666666">Budget Code:</font></td><td>
											
											

												<select name="budget_code" class="body">
												<option value="">
												
												<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
												<option value="<?php echo $searchData2['c_BudgetCode'][0];?>"> <?php echo $searchData2['c_BudgetCode'][0];?>
												<?php } ?>
												</select>
											
												<select name="fund_year" class="body">
												<option value="">
												
												<?php foreach($fund_year_unique as $current) { ?>
												<option value="<?php echo $current;?>"> <?php echo $current;?>
												<?php } ?>
												</select>
											
											
											</td></tr>


											
											
											<tr><td class="body">&nbsp;</td><td>
											<input type="submit" name="submit" value="Run Report">
											</td></tr>
			
											
</form>											
											</table>
<hr>

											<table cellspacing="0" cellpadding="5" border="0" width="100%">
											<tr><td colspan="2"><strong>Staff Time Summary by Budget Code/Task:</strong></td></tr>
											<form id="form3" name="form3" onsubmit="return checkFields2()">
											<input type="hidden" name="action" value="run_staff_time_sum">
											
											
											<tr valign="middle"><td align="right" nowrap><font color="666666">Date Range Begin:</font></td><td width="100%">

												<select name="begin_date2_m" class="body">
												<option value="">
												
												<option value="1">Jan</option>
												<option value="2">Feb</option>
												<option value="3">Mar</option>
												<option value="4">Apr</option>
												<option value="5">May</option>
												<option value="6">Jun</option>
												<option value="7">Jul</option>
												<option value="8">Aug</option>
												<option value="9">Sep</option>
												<option value="10">Oct</option>
												<option value="11">Nov</option>
												<option value="12">Dec</option>
												</select>

												<select name="begin_date2_d" class="body">
												<option value="">
												
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="6">6</option>
												<option value="7">7</option>
												<option value="8">8</option>
												<option value="9">9</option>
												<option value="10">10</option>
												<option value="11">11</option>
												<option value="12">12</option>
												<option value="13">13</option>
												<option value="14">14</option>
												<option value="15">15</option>
												<option value="16">16</option>
												<option value="17">17</option>
												<option value="18">18</option>
												<option value="19">19</option>
												<option value="20">20</option>
												<option value="21">21</option>
												<option value="22">22</option>
												<option value="23">23</option>
												<option value="24">24</option>
												<option value="25">25</option>
												<option value="26">26</option>
												<option value="27">27</option>
												<option value="28">28</option>
												<option value="29">29</option>
												<option value="30">30</option>
												<option value="31">31</option>
												</select>

												<select name="begin_date2_y" class="body">
												<option value="">
												
												<option value="2008">2008</option>
												<option value="2009">2009</option>
												<option value="2010">2010</option>
												<option value="2011">2011</option>
												<option value="2012">2012</option>
												</select>

											</td></tr>
											
											<tr valign="middle"><td align="right" nowrap><font color="666666">Date Range End:</font></td><td nowrap>

												<select name="end_date2_m" class="body">
												<option value="">
												
												<option value="1">Jan</option>
												<option value="2">Feb</option>
												<option value="3">Mar</option>
												<option value="4">Apr</option>
												<option value="5">May</option>
												<option value="6">Jun</option>
												<option value="7">Jul</option>
												<option value="8">Aug</option>
												<option value="9">Sep</option>
												<option value="10">Oct</option>
												<option value="11">Nov</option>
												<option value="12">Dec</option>
												</select>

												<select name="end_date2_d" class="body">
												<option value="">
												
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="6">6</option>
												<option value="7">7</option>
												<option value="8">8</option>
												<option value="9">9</option>
												<option value="10">10</option>
												<option value="11">11</option>
												<option value="12">12</option>
												<option value="13">13</option>
												<option value="14">14</option>
												<option value="15">15</option>
												<option value="16">16</option>
												<option value="17">17</option>
												<option value="18">18</option>
												<option value="19">19</option>
												<option value="20">20</option>
												<option value="21">21</option>
												<option value="22">22</option>
												<option value="23">23</option>
												<option value="24">24</option>
												<option value="25">25</option>
												<option value="26">26</option>
												<option value="27">27</option>
												<option value="28">28</option>
												<option value="29">29</option>
												<option value="30">30</option>
												<option value="31">31</option>
												</select>

												<select name="end_date2_y" class="body">
												<option value="">
												
												<option value="2008">2008</option>
												<option value="2009">2009</option>
												<option value="2010">2010</option>
												<option value="2011">2011</option>
												<option value="2012">2012</option>
												</select>

											</td></tr>
											
											<tr valign="middle"><td align="right"><font color="666666">Staff:</font></td><td>
											
											

												<select name="staff_id2" class="body">
												<option value=""></option>
												
												<?php foreach($searchResult3['data'] as $key => $searchData3) { ?>
												<option value="<?php echo $searchData3['sims_user_ID'][0];?>"> <?php echo $searchData3['sims_user_ID'][0];?></option>
												<?php } ?>
												</select>
											
											
											
											</td></tr>
											
											<tr valign="top"><td align="right"><font color="666666">Summarize By:</font></td><td>
											
											

												
												<input type="radio" name="summarize_by" value="fund_year"> Fund Year<br>
												<input type="radio" name="summarize_by" value="task"> Task/Budget Code
											
											
											
											</td></tr>

											<tr><td class="body">&nbsp;</td><td>
											<input type="submit" name="submit" value="Run Report">
											</td></tr>
			
											
											
											</table>



								</td></tr>
								</table>
							
							</td></tr>
							

							
							
							
							
							</table>

	

			</form>
			
			
			
			
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>







</body>

</html>


<?php
#######################################
## END: DISPLAY NEW BUDGET CODE FORM ## 
#######################################
 
} elseif ($action == 'run_bgt_cd_sum_chart') { // REPORT: BUDGET CODE SUMMARY BY DATE RANGE - BAR CHART

$begin_date = $_GET['begin_date'];
$end_date = $_GET['end_date'];
$budget_code = $_GET['budget_code'];
$show_bgtauth = $_GET['show_bgtauth'];
$show_descr = $_GET['show_descr'];
$show_status = $_GET['show_status'];
$show_dates = $_GET['show_dates'];
$show_comments = $_GET['show_comments'];
$show_mod = $_GET['show_mod'];

#########################################
## START: GRAB BUDGET CODES FOR REPORT ##
#########################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search -> SetDBPassword($webPW,$webUN);

$search -> AddDBParam('BudgetCode',$budget_code);
$search -> AddDBParam('timesheets::c_PayPeriodEnd',$begin_date,'gte');
$search -> AddDBParam('timesheets::PayPeriodBegin',$end_date,'lte');

//$search -> AddSortParam('timesheets::TimeSheetOwnerFullName','ascend');
$search -> AddSortParam('timesheets::c_PayPeriodEnd','ascend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];

if($searchResult['foundCount'] == 0){
$total_hrs[0] = 0;
}
$recordData = current($searchResult['data']);
#######################################
## END: GRAB BUDGET CODES FOR REPORT ##
#######################################

#######################################################
## START: SUMMARIZE PAY PERIOD TOTALS FOR RESULT SET ##
#######################################################
$month_name[0] = $recordData['c_pay_period_month_name'][0]; // SET THE FIRST VALUE OF THE MONTH_NUM ARRAY TO THE FIRST MONTH
$i=0;
$x=0;

foreach($searchResult['data'] as $key => $searchData) { // LOOP THROUGH THE RESULT SET

		if($month_name[$i] == $searchData['c_pay_period_month_name'][0]){ // IF THE MONTH NUM IS THE SAME AS THE PREVIOUS ROW

			$total_hrs[$x] = $total_hrs[$x] + $searchData['c_TotalHrs'][0]; // ADD THE HRS CHARGED TO THE TOTAL_HRS ARRAY

		} else { // IF THE MONTH NUM IS DIFFERENT THAN THE PREVIOUS ROW
			
			$i++; // INCREMENT THE MONTH NUM ARRAY KEY
			$month_name[$i] = $searchData['c_pay_period_month_name'][0]; // SET THE MONTH_NUM ARRAY TO THE NEW MONTH

			$x++; // INCREMENT THE TOTAL HRS ARRAY KEY
			$total_hrs[$x] = $searchData['c_TotalHrs'][0]; // SET NEXT ELEMENT FOR HRS CHARGED TO THE NEW ROW VALUE

		}

}

$num_months = count($month_name);
#####################################################
## END: SUMMARIZE PAY PERIOD TOTALS FOR RESULT SET ##
#####################################################

#################################
## START: WRITE CHART XML FILE ##
#################################
$rand = rand();
$xml_header = "<chart caption='SEDL Budget Code Summary: $begin_date - $end_date' subcaption='Budget Code $budget_code' xAxisName='Month' yAxisName='Hours Charged' numberPrefix=''>
";
//$file_name = "bgt_cd_summary.xml";
$file_name = 'bgt_cd_summary.xml';

$fh = fopen($file_name, 'w') or die("can't open file");

fwrite($fh, $xml_header);

for($j=0;$j<=$num_months;$j++) { 

	if(isset($month_name[$j])){
	$itemData = 
	'<set label=\''.$month_name[$j].'\' value=\''.$total_hrs[$j].'\' />
	';
	
	fwrite($fh, $itemData);
	}
} 

$xml_footer = 
'</chart>';

fwrite($fh, $xml_footer);

fclose($fh);

#################################
## END: WRITE CHART XML FILE ##
#################################




#####################################
## START: DISPLAY REPORT RESULTS ##
#####################################
 
 ?>

<html>
<head>
<title>SIMS - Budget Code Reports</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Reports</h1><hr /></td></tr>
			
<?php if($_SESSION['budget_code_deleted'] == 'yes'){ ?>
			<tr><td class="body" colspan="2"><p class="alert_small">Budget code successfully deleted from SIMS.</p></td></tr>
<?php $_SESSION['budget_code_deleted'] = ''; }?>

			<tr bgcolor="#e2eaa4"><td class="body"><strong>Budget Code Summary Report: <?php echo $searchResult['foundCount'];?> records found.</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> <a href="bgt_code_report.php?action=new">New Report</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							<tr bgcolor="#ebebeb"><td class="body" nowrap>
							Hours charged to budget code <strong><?php echo $budget_code;?></strong> from <strong><?php echo $begin_date;?></strong> to <strong><?php echo $end_date;?></strong></td></tr>
							<tr><td>

						      	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="900" height="300" id="Column3D" >
									<param name="movie" value="charts/FusionCharts/Column3D.swf" />
									<param name="FlashVars" value="&dataURL=<?php echo $file_name;?>">
									<param name="quality" value="high" />
									<embed src="charts/FusionCharts/Column3D.swf" flashVars="&dataURL=<?php echo $file_name;?>" quality="high" width="900" height="300" name="Column3D" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
							  	</object>

							</td></tr>

								<tr bgcolor="#ebebeb" height="35"><td class="body" nowrap align="right">Total Hours Charged: <?php echo array_sum($total_hrs);?></strong></td></tr>

							
							</table>
			
			</td></tr>
			
			
			</table>

</td></tr>
</table>







</body>

</html>

 <?php
 
###################################
## END: DISPLAY REPORT RESULTS ##
###################################

} elseif ($action == 'run_bgt_cd_sum') { // REPORT: BUDGET CODE SUMMARY BY DATE RANGE - TABLE FORMAT

$begin_date_m = $_GET['begin_date_m'];
$begin_date_d = $_GET['begin_date_d'];
$begin_date_y = $_GET['begin_date_y'];
$end_date_m = $_GET['end_date_m'];
$end_date_d = $_GET['end_date_d'];
$end_date_y = $_GET['end_date_y'];

$begin_date = $begin_date_m.'/'.$begin_date_d.'/'.$begin_date_y;
$end_date = $end_date_m.'/'.$end_date_d.'/'.$end_date_y;

$budget_code = $_GET['budget_code'];
$fund_year = $_GET['fund_year'];
//$show_bgtauth = $_GET['show_bgtauth'];
//$show_descr = $_GET['show_descr'];
//$show_status = $_GET['show_status'];
//$show_dates = $_GET['show_dates'];
//$show_comments = $_GET['show_comments'];
//$show_mod = $_GET['show_mod'];

#########################################
## START: GRAB BUDGET CODES FOR REPORT ##
#########################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs_subset','all');
$search -> SetDBPassword($webPW,$webUN);

if($budget_code != ''){
$search -> AddDBParam('BudgetCode',$budget_code);
$chart_sub_title = 'Budget Code: '.$budget_code;
$chart_title = 'Budget Code Summary';
}

if($fund_year != ''){
$search -> AddDBParam('c_fund_year',$fund_year);
$chart_sub_title = 'Fund Year: '.$fund_year;
$chart_title = 'Fund Year Summary';
}

$search -> AddDBParam('timesheets::c_PayPeriodEnd',$begin_date,'gte');
$search -> AddDBParam('timesheets::PayPeriodBegin',$end_date,'lte');

//$search -> AddSortParam('timesheets::TimeSheetOwnerFullName','ascend');
//$search -> AddSortParam('timesheets::c_PayPeriodEnd_m','ascend');
$search -> AddSortParam('timesheets::sims_user_ID','ascend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//exit;
#######################################
## END: GRAB BUDGET CODES FOR REPORT ##
#######################################

########################################################################
## START: CREATE ARRAYS WITH RESULTS TO SUMMARIZE STAFF UNIQUE HOURS  ##
########################################################################
$recordData = current($searchResult['data']);
//$current_month = $recordData['timesheets::c_PayPeriodEnd_m'][0];
$total_hrs = 0;
$current_staff = '';

$i=0;
foreach($searchResult['data'] as $key => $searchData) {

	$total_hrs = $total_hrs + $searchData['c_TotalHrs'][0];
	$staff_col[$i] = $searchData['timesheets::sims_user_ID'][0];
	$i++;


}
if($searchResult['foundCount'] == 0){
$staff_col[0] = '';
}

$staff_col_unique = array_unique($staff_col);

$staff_colspan = count($staff_col_unique)+2;
//print_r($staff_col);
//echo '<p>';

//print_r($staff_col_unique);
//echo '<p>';

//print_r($bgt_auth_col);
//echo '<p>';

//print_r($hrs_col);
//echo '<p>';

//$staff_col_value_count = array_count_values($staff_col);

//print_r($staff_col_value_count);

$a=0;

$current_staff_hrs = 0;

foreach($staff_col_unique as $current) {

	foreach($searchResult['data'] as $key => $searchData) {

		if($searchData['timesheets::sims_user_ID'][0] == $current){
		$current_staff_hrs = $current_staff_hrs + $searchData['c_TotalHrs'][0];
		
		}

	}
		$staff_hrs[$a] = $current_staff_hrs;
		$current_staff_hrs = 0;
		$a++;
}

//print_r($staff_hrs);
//echo '<p>';
//exit;
//$hrs_colspan = count($staff_hrs)+2;

########################################################################
## END: CREATE ARRAYS WITH RESULTS TO SUMMARIZE STAFF UNIQUE HOURS  ##
########################################################################

#################################
## START: WRITE CHART XML FILE ##
#################################

$xml_header = "<chart caption='$chart_title: $begin_date - $end_date' subcaption='$chart_sub_title' xAxisName='Staff Member' yAxisName='Hours Charged' numberPrefix=''>
";
//$file_name = "bgt_cd_summary.xml";
$file_name = 'bgt_cd_summary.xml';

$fh = fopen($file_name, 'w') or die("can't open file");

fwrite($fh, $xml_header);


$j=0;

foreach($staff_col_unique as $current) { 


	$itemData = 
	'<set label=\''.$current.'\' value=\''.$staff_hrs[$j].'\' />
	';
	
	fwrite($fh, $itemData);
	
$j++;
} 

$xml_footer = 
"
<styles>

      <definition>
         <style name='CanvasAnim' type='animation' param='_xScale' start='0' duration='1' />
      </definition>

      <application>
         <apply toObject='Canvas' styles='CanvasAnim' />
      </application>   

   </styles>
</chart>
";

fwrite($fh, $xml_footer);

fclose($fh);

#################################
## END: WRITE CHART XML FILE ##
#################################


/*
#################################
## START: WRITE CHART XML FILE ##
#################################

$xml_header = "<chart caption='$chart_title: $begin_date - $end_date' subcaption='$chart_sub_title' xAxisName='Staff Member' yAxisName='Hours Charged' numberPrefix='' labelDisplay='Rotate' slantLabels='1' rotateValues='1' placeValuesInside='1'>
";
//$file_name = "bgt_cd_summary.xml";
$file_name = 'bgt_cd_summary.xml';

$fh = fopen($file_name, 'w') or die("can't open file");

fwrite($fh, $xml_header);


$j=0;

foreach($staff_col_unique as $current) { 


	$itemData = 
	'<set label=\''.$current.'\' value=\''.$staff_hrs[$j].'\' />
	';
	
	fwrite($fh, $itemData);
	
$j++;
} 

$xml_footer = 
'</chart>';

fwrite($fh, $xml_footer);

fclose($fh);

#################################
## END: WRITE CHART XML FILE ##
#################################
*/

#####################################
## START: DISPLAY REPORT RESULTS ##
#####################################
 
 ?>

<html>
<head>
<title>SIMS - Budget Code Reports</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(screen.availWidth,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Reports</h1><hr /></td></tr>
			
<?php if($_SESSION['budget_code_deleted'] == 'yes'){ ?>
			<tr><td class="body" colspan="2"><p class="alert_small">Budget code successfully deleted from SIMS.</p></td></tr>
<?php $_SESSION['budget_code_deleted'] = ''; }?>

			<tr bgcolor="#e2eaa4"><td class="body"><strong>Budget Code Summary Report: <?php echo $searchResult['foundCount'];?></strong> records found.</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="bgt_code_report.php?action=new">New Report</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>

							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							<tr bgcolor="#ebebeb"><td class="body" nowrap>
							Hours charged from <strong><?php echo $begin_date;?></strong> to <strong><?php echo $end_date;?></strong></td></tr>
							<tr><td>

						      	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="100%" height="100%" id="Bar2D" >
									<param name="movie" value="charts/FusionCharts/Bar2D.swf" />
									<param name="FlashVars" value="&dataURL=<?php echo $file_name;?>">
									<param name="quality" value="high" />
									<embed src="charts/FusionCharts/Bar2D.swf" flashVars="&dataURL=<?php echo $file_name;?>" quality="high" width="1000" height="600" name="Bar2D" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
							  	</object>
				
			
							  	
							  	<p>


							<table cellspacing="0" cellpadding="3"  width="100%" class="sims">
							<tr bgcolor="#ebebeb"><td class="body" nowrap colspan="<?php echo $staff_colspan;?>">
							Hours charged to <strong><?php echo $chart_sub_title;?></strong> from <strong><?php echo $begin_date;?></strong> to <strong><?php echo $end_date;?></strong><?php if($chart_title == 'Budget Code Summary'){?> | <a href="bgt_code_report.php?action=run_bgt_cd_sum_chart&begin_date=<?php echo $begin_date;?>&end_date=<?php echo $end_date;?>&budget_code=<?php echo $budget_code;?>" target="_blank">Show monthly summary chart</a><?php }?></td></tr>
							<tr>
							<td bgcolor="#e2eaa4" nowrap>Staff</td>
							
							<?php foreach($staff_col_unique as $current) { ?>
							<td><?php echo $current;?></td>
							<?php } ?>
							
							<td><strong>Total</strong></td></tr>
							<tr>
							<td  bgcolor="#e2eaa4" nowrap>Hours</td>

							<?php foreach($staff_hrs as $current) { ?>
							<td><?php echo $current;?></td>
							<?php } ?>

							<td><?php echo $total_hrs;?></td></tr>

							

							


							
							</table>
			
			</td></tr>
			
			
			</table>

</td></tr>
</table>







</body>

</html>

 <?php
 
###################################
## END: DISPLAY REPORT RESULTS ##
###################################



} elseif ($action == 'run_staff_time_sum') { // REPORT: STAFF TIME SUMMARY BY DATE RANGE/BUDGET CODE


$begin_date2_m = $_GET['begin_date2_m'];
$begin_date2_d = $_GET['begin_date2_d'];
$begin_date2_y = $_GET['begin_date2_y'];
$end_date2_m = $_GET['end_date2_m'];
$end_date2_d = $_GET['end_date2_d'];
$end_date2_y = $_GET['end_date2_y'];

$begin_date = $begin_date2_m.'/'.$begin_date2_d.'/'.$begin_date2_y;
$end_date = $end_date2_m.'/'.$end_date2_d.'/'.$end_date2_y;




//$budget_code = $_GET['budget_code2'];
$staff_id = $_GET['staff_id2'];

$summarize_by = $_GET['summarize_by'];

//echo '<p>'.$staff_id;


//$show_bgtauth = $_GET['show_bgtauth'];
//$show_descr = $_GET['show_descr'];
//$show_status = $_GET['show_status'];
//$show_dates = $_GET['show_dates'];
//$show_comments = $_GET['show_comments'];
//$show_mod = $_GET['show_mod'];

#########################################
## START: GRAB BUDGET CODES FOR REPORT ##
#########################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','time_hrs_subset','all');
$search -> SetDBPassword($webPW,$webUN);

//$search -> AddDBParam('BudgetCode',$budget_code);

$search -> AddDBParam('timesheets::sims_user_ID',$staff_id);
$search -> AddDBParam('timesheets::c_PayPeriodEnd',$begin_date,'gte');
$search -> AddDBParam('timesheets::PayPeriodBegin',$end_date,'lte');
$search -> AddDBParam('HrsType','WkHrsReg');


//$search -> AddSortParam('timesheets::TimeSheetOwnerFullName','ascend');
$search -> AddSortParam('BudgetCode','ascend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
exit;
if($searchResult['foundCount'] == 0){
$total_hrs[0] = 0;
}
$recordData = current($searchResult['data']);
#######################################
## END: GRAB BUDGET CODES FOR REPORT ##
#######################################

if($summarize_by == 'task'){

		#######################################################
		## START: SUMMARIZE BUDGET CODE TOTALS FOR RESULT SET ##
		#######################################################
		$budget_code[0] = $recordData['BudgetCode'][0]; // SET THE FIRST VALUE OF THE MONTH_NUM ARRAY TO THE FIRST MONTH
		$i=0;
		$x=0;
		
		foreach($searchResult['data'] as $key => $searchData) { // LOOP THROUGH THE RESULT SET
		
				if($budget_code[$i] == $searchData['BudgetCode'][0]){ // IF THE BUDGET CODE IS THE SAME AS THE PREVIOUS ROW
		
					$total_hrs[$x] = $total_hrs[$x] + $searchData['c_TotalHrs'][0]; // ADD THE HRS CHARGED TO THE TOTAL_HRS ARRAY
		
				} else { // IF THE BUDGET CODE IS DIFFERENT THAN THE PREVIOUS ROW
					
					$i++; // INCREMENT THE BUDGET CODE ARRAY KEY
					$budget_code[$i] = $searchData['BudgetCode'][0]; // SET THE BUDGET CODE ARRAY TO THE BUDGET CODE
		
					$x++; // INCREMENT THE TOTAL HRS ARRAY KEY
					$total_hrs[$x] = $searchData['c_TotalHrs'][0]; // SET NEXT ELEMENT FOR HRS CHARGED TO THE NEW ROW VALUE
		
				}
		
		}
		
		$num_codes = count($budget_code);
		$data_table_colspan = $num_codes + 2;
		#####################################################
		## END: SUMMARIZE BUDGET CODE TOTALS FOR RESULT SET ##
		#####################################################
		
		#################################
		## START: WRITE CHART XML FILE ##
		#################################
		$rand = rand();
		$xml_header = "<chart caption='Staff Time Summary by Budget Code: $begin_date - $end_date' subcaption='Staff: $staff_id' xAxisName='Budget Code' yAxisName='Hours Charged' numberPrefix=''>
		";
		//$file_name = "bgt_cd_summary.xml";
		$file_name = 'bgt_cd_summary.xml';
		
		$fh = fopen($file_name, 'w') or die("can't open file");
		
		fwrite($fh, $xml_header);
		
		for($j=0;$j<=$num_codes;$j++) { 
		
			if(isset($budget_code[$j])){
			$itemData = 
			'<set label=\''.$budget_code[$j].'\' value=\''.$total_hrs[$j].'\' />
			';
			
			fwrite($fh, $itemData);
			}
		} 
		
		$xml_footer = 
		'</chart>';
		
		fwrite($fh, $xml_footer);
		
		fclose($fh);
		
		#################################
		## END: WRITE CHART XML FILE ##
		#################################
		
		
		
		
		#####################################
		## START: DISPLAY REPORT RESULTS ##
		#####################################
		 
		 ?>
		
		<html>
		<head>
		<title>SIMS - Budget Code Reports</title>
		<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
		</head>
		
		<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1200,1000)">
		
		<table width="800" cellpadding="0" cellspacing="0" border="0">
		<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
		<tr><td colspan="3">
		
					<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
					
					<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
				
					<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Reports</h1><hr /></td></tr>
					
		<?php if($_SESSION['budget_code_deleted'] == 'yes'){ ?>
					<tr><td class="body" colspan="2"><p class="alert_small">Budget code successfully deleted from SIMS.</p></td></tr>
		<?php $_SESSION['budget_code_deleted'] = ''; }?>
		
					<tr bgcolor="#e2eaa4"><td class="body"><strong>Staff Time Summary by Budget Code: <?php echo $searchResult['foundCount'];?> records found.</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="bgt_code_report.php?action=new">New Report</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
					
					
					
					<tr><td class="body" colspan=2>
		
		
		
									<table cellspacing="0" cellpadding="4" width="100%" class="sims">
									<tr bgcolor="#ebebeb"><td class="body" nowrap>
									Hours charged by <strong><?php echo $staff_id;?></strong> from <strong><?php echo $begin_date;?></strong> to <strong><?php echo $end_date;?></strong></td></tr>
									<tr><td>
		
										<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="900" height="300" id="Column3D" >
											<param name="movie" value="charts/FusionCharts/Column3D.swf" />
											<param name="FlashVars" value="&dataURL=<?php echo $file_name;?>">
											<param name="quality" value="high" />
											<embed src="charts/FusionCharts/Column3D.swf" flashVars="&dataURL=<?php echo $file_name;?>" quality="high" width="900" height="300" name="Column3D" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
										</object>
										<p>
										
										
									<table cellspacing="0" cellpadding="3"  width="100%" class="sims">
									<tr bgcolor="#ebebeb"><td class="body" nowrap colspan="<?php echo $data_table_colspan;?>">
									Data Table</td></tr>
									<tr><td nowrap>Budget Code</td>
									
		
									<?php for($j=0;$j<=$num_codes;$j++) { 
									
										if(isset($budget_code[$j])){ echo '<td><a href="budget_code_status.php?budget_code='.$budget_code[$j].'" target="_blank" title="Click for budget code details">'.$budget_code[$j].'</a></td>'; }
										
									} ?>
									<td><strong>Total</strong></td></tr>
									
									<tr><td nowrap>Hrs Charged</td>
		
									<?php for($j=0;$j<=$num_codes;$j++) { 
									
										if(isset($budget_code[$j])){ echo '<td>'.$total_hrs[$j].'</td>'; }
										
									} ?>
									<td><?php echo array_sum($total_hrs);?></td></tr>
									
									
		
									
		
		
									
									</table>
		
		
									</td></tr>
		
		
									
									</table>
					
					</td></tr>
					
					
					</table>
		
		</td></tr>
		</table>
		
		
		
		
		
		
		
		</body>
		
		</html>

 <?php
 
 } else { // IF $summarize_by == 'fund_year'
 
		#######################################################
		## START: SUMMARIZE FUND YEAR TOTALS FOR RESULT SET ##
		#######################################################
		$fund_year[0] = $recordData['c_fund_year'][0]; // SET THE FIRST VALUE OF THE MONTH_NUM ARRAY TO THE FIRST MONTH
		$i=0;
		$x=0;
		
		foreach($searchResult['data'] as $key => $searchData) { // LOOP THROUGH THE RESULT SET
		
				if($fund_year[$i] == $searchData['c_fund_year'][0]){ // IF THE FUND YEAR IS THE SAME AS THE PREVIOUS ROW
		
					$total_hrs[$x] = $total_hrs[$x] + $searchData['c_TotalHrs'][0]; // ADD THE HRS CHARGED TO THE TOTAL_HRS ARRAY
		
				} else { // IF THE BUDGET CODE IS DIFFERENT THAN THE PREVIOUS ROW
					
					$i++; // INCREMENT THE BUDGET CODE ARRAY KEY
					$fund_year[$i] = $searchData['c_fund_year'][0]; // SET THE FUND YEAR ARRAY TO THE BUDGET CODE
		
					$x++; // INCREMENT THE TOTAL HRS ARRAY KEY
					$total_hrs[$x] = $searchData['c_TotalHrs'][0]; // SET NEXT ELEMENT FOR HRS CHARGED TO THE NEW ROW VALUE
		
				}
		
		}
		
		$num_codes = count($fund_year);
		$data_table_colspan = $num_codes + 2;
		#####################################################
		## END: SUMMARIZE FUND YEAR TOTALS FOR RESULT SET ##
		#####################################################
		
		#################################
		## START: WRITE CHART XML FILE ##
		#################################
		//$rand = rand();
		$xml_header = "<chart caption='Staff Time Summary by Fund Year: $begin_date - $end_date' subcaption='Staff: $staff_id' xAxisName='Fund Year' yAxisName='Hours Charged' numberPrefix=''>
		";
		//$file_name = "bgt_cd_summary.xml";
		$file_name = 'bgt_cd_summary.xml';
		
		$fh = fopen($file_name, 'w') or die("can't open file");
		
		fwrite($fh, $xml_header);
		
		for($j=0;$j<=$num_codes;$j++) { 
		
			if(isset($fund_year[$j])){
			$itemData = 
			'<set label=\''.$fund_year[$j].'\' value=\''.$total_hrs[$j].'\' />
			';
			
			fwrite($fh, $itemData);
			}
		} 
		
		$xml_footer = 
		'</chart>';
		
		fwrite($fh, $xml_footer);
		
		fclose($fh);
		
		#################################
		## END: WRITE CHART XML FILE ##
		#################################
		
		
		
		
		#####################################
		## START: DISPLAY REPORT RESULTS ##
		#####################################
		 
		 ?>
		
		<html>
		<head>
		<title>SIMS - Budget Code Reports</title>
		<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
		</head>
		
		<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1200,1000)">
		
		<table width="800" cellpadding="0" cellspacing="0" border="0">
		<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
		<tr><td colspan="3">
		
					<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
					
					<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
				
					<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Reports</h1><hr /></td></tr>
					
		<?php if($_SESSION['budget_code_deleted'] == 'yes'){ ?>
					<tr><td class="body" colspan="2"><p class="alert_small">Budget code successfully deleted from SIMS.</p></td></tr>
		<?php $_SESSION['budget_code_deleted'] = ''; }?>
		
					<tr bgcolor="#e2eaa4"><td class="body"><strong>Staff Time Summary by Fund Year: <?php echo $searchResult['foundCount'];?> records found.</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="bgt_code_report.php?action=new">New Report</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
					
					
					
					<tr><td class="body" colspan=2>
		
		
		
									<table cellspacing="0" cellpadding="4" width="100%" class="sims">
									<tr bgcolor="#ebebeb"><td class="body" nowrap>
									Hours charged by <strong><?php echo $staff_id;?></strong> from <strong><?php echo $begin_date;?></strong> to <strong><?php echo $end_date;?></strong></td></tr>
									<tr><td>
		
										<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="900" height="300" id="Column3D" >
											<param name="movie" value="charts/FusionCharts/Column3D.swf" />
											<param name="FlashVars" value="&dataURL=<?php echo $file_name;?>">
											<param name="quality" value="high" />
											<embed src="charts/FusionCharts/Column3D.swf" flashVars="&dataURL=<?php echo $file_name;?>" quality="high" width="900" height="300" name="Column3D" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
										</object>
										<p>
										
										
									<table cellspacing="0" cellpadding="3"  width="100%" class="sims">
									<tr bgcolor="#ebebeb"><td class="body" nowrap colspan="<?php echo $data_table_colspan;?>">
									Data Table</td></tr>
									<tr><td nowrap>Fund Year</td>
									
		
									<?php for($j=0;$j<=$num_codes;$j++) { 
									
										if(isset($fund_year[$j])){ echo '<td>'.$fund_year[$j].'</td>'; }
										
									} ?>
									<td><strong>Total</strong></td></tr>
									
									<tr><td nowrap>Hrs Charged</td>
		
									<?php for($j=0;$j<=$num_codes;$j++) { 
									
										if(isset($fund_year[$j])){ echo '<td>'.$total_hrs[$j].'</td>'; }
										
									} ?>
									<td><?php echo array_sum($total_hrs);?></td></tr>
									
									
		
									
		
		
									
									</table>
		
		
									</td></tr>
		
		
									
									</table>
					
					</td></tr>
					
					
					</table>
		
		</td></tr>
		</table>
		
		
		
		
		
		
		
		</body>
		
		</html>
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 <?php }
 
###################################
## END: DISPLAY REPORT RESULTS ##
###################################


} elseif ($action == 'print_friendly') {

$sort_by = $_GET['sort_by'];
$pref = $_GET['pref'];
######################################
## START: GRAB CURRENT BUDGET CODES ##
######################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> AddDBParam('current_employee_status','SEDL Employee');
if($pref == 'active_only'){
$_SESSION['budget_code_view_pref'] = 'active_only';
$search -> AddDBParam('c_Active_Status','Active');
}
if($pref == 'all_codes'){
//$search -> AddDBParam('c_Active_Status','Active');
$_SESSION['budget_code_view_pref'] = 'all_codes';
}


if(($sort_by == 'Active_From')||($sort_by == 'Active_To')){
$search -> AddSortParam($sort_by,'descend');
}else{
$search -> AddSortParam($sort_by,'ascend');
}
$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
####################################
## END: GRAB CURRENT BUDGET CODES ##
####################################
 $today = date("M d, Y");
#####################################
## START: DISPLAY BUDGET CODE LIST ##
#####################################
 
 ?>

<html>
<head>
<title>SIMS - Budget Code Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1200,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td><img src="/staff/sims/images/logo-new-grayscale.png" width="86" height="34" alt="SEDL-Logo"></td></tr>
		
			
			<tr><td class="body"><strong>SEDL Budget Codes</strong> as of: <?php echo $today;?></td></tr>
			
			
			
			<tr><td class="body">



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr><td class="body" nowrap>Budget Code</td><td class="body">Description</td><td class="body" nowrap>Active From</td><td class="body" nowrap>Active To</td><td class="body" nowrap>Status</td><td class="body" nowrap>Approved By</td><td class="body">Comments</td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body" nowrap valign="top"><?php echo $searchData['c_BudgetCode'][0];?></td><td class="body" valign="top" nowrap><?php echo $searchData['BudgetCodeDescription'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_From'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_To'][0];?></td><td class="body" nowrap valign="top"><?php echo $searchData['c_Active_Status'][0];?></td><td class="body" valign="top"><?php echo $searchData['BgtAuthorityApproving_sims_ID'][0];?></td><td class="body" valign="top" nowrap><?php echo $searchData['Comments'][0];?></td></tr>
								<?php } ?>


							
							</table>
			
			</td></tr>
			
			
			</table>

</td></tr>
</table>







</body>

</html>

 <?php
 
###################################
## END: DISPLAY BUDGET CODE LIST ##
###################################



} elseif ($action == 'show_1') { 

$budget_code = $_GET['budget_code'];

############################################################
## START: GRAB PBA USERIDs TO POPULATE PBA DROP-DOWN LIST ##
############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff');
$search2 -> SetDBPassword($webPW,$webUN);
//$search2 -> AddDBParam('current_employee_status','SEDL Employee');
$search2 -> AddDBParam('is_budget_authority','Yes');

$search2 -> AddSortParam('sims_user_ID','ascend');

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);
##########################################################
## END: GRAB PBA USERIDs TO POPULATE PBA DROP-DOWN LIST ##
##########################################################

#################################
## START: FIND EMPLOYEE RECORD ##
#################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','budget_codes');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('c_BudgetCode',$budget_code);

$searchResult4 = $search4 -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData4 = current($searchResult4['data']);
###############################
## END: FIND EMPLOYEE RECORD ##
###############################


####################################
## START: DISPLAY EMPLOYEE RECORD ## 
####################################
?>


<html>
<head>
<title>SIMS - Budget Code Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">

function confirmDelete() { 
	var answer = confirm ("Are you sure you want to delete this budget code from SIMS?")
	if (!answer) {
	return false;
	}
}

</script>

</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Admin</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>Budget Code: <?php echo $recordData4['c_BudgetCode'][0];?></strong> (<?php echo $recordData4['c_Active_Status'][0];?>)</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode">Show All</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: Any changes made to this record must be saved by clicking the Update Budget Code button. | Last updated: <?php echo $recordData4['last_mod_timestamp'][0];?> by <?php echo $recordData4['last_mod_by'][0];?></p></td></tr>

			<tr><td class="body" colspan=2>


							<table cellspacing="0" cellpadding="1" width="100%" border="1" bordercolor="#cccccc">
							
							<form name="new_employee">
							<input type="hidden" name="action" value="update">
							<input type="hidden" name="update_row_ID" value="<?php echo $recordData4['c_cwp_row_ID'][0];?>">
							<input type="hidden" name="last_mod_by" value="<?php echo $_SESSION['user_ID'];?>">

							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;Budget Code Details | <a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode&delete=yes&row_ID=<?php echo $recordData4['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()">Delete</a></td></tr>
							
							<tr><td class="body" valign="top" width="100%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								<tr><td valign="top">

											<table cellspacing="0" cellpadding="5" border="0" width="100%">
											<tr valign="bottom"><td align="right"><font color="666666">Budget Code:</font></td><td>
											
												<table cellpadding="0" cellspacing="0" border="0">
													<td><font color="666666"><span class="tiny">FUND</span></font><br><input type="text" name="fund" size="5" value="<?php echo $recordData4['Fund'][0];?>"></td>
													<td><font color="666666"><span class="tiny">YEAR</span></font><br><input type="text" name="year" size="3" value="<?php echo $recordData4['Year'][0];?>"></td>
													<td><font color="666666"><span class="tiny">ORGCODE</span></font><br><input type="text" name="org_code" size="8" value="<?php echo $recordData4['OrganCode'][0];?>"></td></tr>
												</table>
											
											</td></tr>

											<tr valign="bottom"><td align="right"><font color="666666">Description:</font></td><td><input type="text" name="description" size="50" value="<?php echo $recordData4['BudgetCodeDescription'][0];?>"></td></tr>

											<tr valign="bottom"><td align="right" nowrap><font color="666666" nowrap>Active From:</font></td><td><input type="text" name="active_from" size="30" value="<?php echo $recordData4['Active_From'][0];?>"> <font color="666666"><span class="tiny">(Format: MM/DD/YYYY)</span></font></td></tr>
											<tr valign="bottom"><td align="right" nowrap><font color="666666" nowrap>Active To:</font></td><td><input type="text" name="active_to" size="30" value="<?php echo $recordData4['Active_To'][0];?>"> <font color="666666"><span class="tiny">(Format: MM/DD/YYYY)</span></font></td></tr>
											
											<tr valign="middle"><td align="right"><font color="666666">Approved By:</font></td><td>
											
											
												<select name="approved_by" class="body">
												<option value="choose">
												
												<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
												<option value="<?php echo $searchData2['staff_ID'][0];?>" <?php if($recordData4['BgtAuthorityApproving_sims_ID'][0] == $searchData2['sims_user_ID'][0]){echo 'SELECTED';}?>> <?php echo $searchData2['sims_user_ID'][0];?>
												<?php } ?>
												</select>
											
											
											
											</td></tr>
											
											<tr valign="bottom"><td align="right"><font color="666666">Comments:</font></td><td><input type="text" name="comments" size="50" value="<?php echo $recordData4['Comments'][0];?>"></td></tr>
			
											
											
											</table>


								</td></tr>
								</table>
							
							</td></tr>
							

							<tr><td class="body" colspan="2">
							<center><input type="submit" name="submit" value="Update Budget Code"></center>
							</td></tr>
							
							
							
							
							</table>

	

			</form>
			
			
			
			
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>







</body>

</html>


<?php
##################################
## END: DISPLAY EMPLOYEE RECORD ## 
##################################
 

 } elseif ($action == 'update') {

#######################################
## START: GRAB UPDATE FORM VARIABLES ##
#######################################
$update_row_ID = $_GET['update_row_ID'];
$fund = $_GET['fund'];
$year = $_GET['year'];
$org_code = $_GET['org_code'];
$description = $_GET['description'];
$active_from = $_GET['active_from'];
$active_to = $_GET['active_to'];
$approved_by = $_GET['approved_by'];
$comments = $_GET['comments'];
$last_mod_by = $_GET['last_mod_by'];
#####################################
## END: GRAB UPDATE FORM VARIABLES ##
#####################################

##################################
## START: UPDATE THE FMP RECORD ##
##################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','budget_codes');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row_ID);

$update -> AddDBParam('Fund',$fund);
$update -> AddDBParam('Year',$year);
$update -> AddDBParam('OrganCode',$org_code);
$update -> AddDBParam('BudgetCodeDescription',$description);
$update -> AddDBParam('Active_From',$active_from);
$update -> AddDBParam('Active_To',$active_to);
$update -> AddDBParam('BgtAuthorityApproving_staff_ID',$approved_by);
$update -> AddDBParam('Comments',$comments);
$update -> AddDBParam('last_mod_by',$last_mod_by);

$updateResult = $update -> FMEdit();

//echo '<p>$updateResult[errorCode]: '.$updateResult['errorCode'];
if($updateResult['errorCode'] == '0'){
$confirm_update = '1';
}
################################
## END: UPDATE THE FMP RECORD ##
################################

$sort_by = 'c_BudgetCode';
$pref = $_SESSION['budget_code_view_pref'];
######################################
## START: GRAB CURRENT BUDGET CODES ##
######################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> AddDBParam('current_employee_status','SEDL Employee');
if($pref == 'active_only'){
$_SESSION['budget_code_view_pref'] = 'active_only';
$search -> AddDBParam('c_Active_Status','Active');
}
if($pref == 'all_codes'){
//$search -> AddDBParam('c_Active_Status','Active');
$_SESSION['budget_code_view_pref'] = 'all_codes';
}


if(($sort_by == 'Active_From')||($sort_by == 'Active_To')){
$search -> AddSortParam($sort_by,'descend');
}else{
$search -> AddSortParam($sort_by,'ascend');
}
$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
####################################
## END: GRAB CURRENT BUDGET CODES ##
####################################


#####################################
## START: DISPLAY BUDGET CODE LIST ##
#####################################
 
 ?>

<html>
<head>
<title>SIMS - Budget Code Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Admin</h1><hr /></td></tr>
			
			<?php if($confirm_update == '1'){ ?>

			<tr><td colspan="2"><p class="alert_small">Record successfully updated.</p></td></tr>
			
			<?php $confirm_update = '0';
			} ?>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL Budget Codes</strong> | <?php echo $searchResult['foundCount'];?> records found.</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="bgt_code_admin.php?action=print_friendly&sort_by=<?php echo $sort_by;?>&pref=<?php echo $_SESSION['budget_code_view_pref'];?>" target="_blank">Print</a> | <a href="bgt_code_admin.php?action=new">New Budget Code</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode">Budget Code</a></td><td class="body"><a href="bgt_code_admin.php?action=show_all&sort_by=BudgetCodeDescription">Description</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=Active_From">Active From</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=Active_To">Active To</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=c_Active_Status">Status</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=BgtAuthorityApproving_sims_ID">Approved By</a></td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body" nowrap valign="top"><a href="bgt_code_admin.php?action=show_1&budget_code=<?php echo $searchData['c_BudgetCode'][0];?>"><?php echo $searchData['c_BudgetCode'][0];?></a></td><td class="body" valign="top"><?php echo $searchData['BudgetCodeDescription'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_From'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_To'][0];?></td><td class="body" nowrap valign="top"><font <?php if($searchData['c_Active_Status'][0] != 'Active'){?>color="red"<?php }?>><?php echo $searchData['c_Active_Status'][0];?></font></td><td class="body" valign="top"><?php echo $searchData['BgtAuthorityApproving_sims_ID'][0];?></td></tr>
								<?php } ?>


							
							</table>
			
			</td></tr>
			
			
			</table>

</td></tr>
</table>







</body>

</html>
 
<?php
###################################
## END: DISPLAY BUDGET CODE LIST ##
###################################
?>

 <?php } elseif ($action == 'new_submit') {

###########################################
## START: GRAB NEW RECORD FORM VARIABLES ##
###########################################
$fund = $_GET['fund'];
$year = $_GET['year'];
$org_code = $_GET['org_code'];
$description = $_GET['description'];
$active_from = $_GET['active_from'];
$active_to = $_GET['active_to'];
$approved_by = $_GET['approved_by'];
$comments = $_GET['comments'];
$last_mod_by = $_GET['last_mod_by'];
#########################################
## END: GRAB NEW RECORD FORM VARIABLES ##
#########################################

##################################
## START: UPDATE THE FMP RECORD ##
##################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','budget_codes');
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('Fund',$fund);
$newrecord -> AddDBParam('Year',$year);
$newrecord -> AddDBParam('OrganCode',$org_code);
$newrecord -> AddDBParam('BudgetCodeDescription',$description);
$newrecord -> AddDBParam('Active_From',$active_from);
$newrecord -> AddDBParam('Active_To',$active_to);
$newrecord -> AddDBParam('BgtAuthorityApproving_staff_ID',$approved_by);
$newrecord -> AddDBParam('Comments',$comments);
$newrecord -> AddDBParam('last_mod_by',$last_mod_by);

$newrecordResult = $newrecord -> FMNew();

//echo '<p>$updateResult[errorCode]: '.$updateResult['errorCode'];
if($newrecordResult['errorCode'] == '0'){
$confirm_new = '1';
}
################################
## END: UPDATE THE FMP RECORD ##
################################

$sort_by = 'c_BudgetCode';
$pref = $_SESSION['budget_code_view_pref'];
######################################
## START: GRAB CURRENT BUDGET CODES ##
######################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> AddDBParam('current_employee_status','SEDL Employee');
if($pref == 'active_only'){
$_SESSION['budget_code_view_pref'] = 'active_only';
$search -> AddDBParam('c_Active_Status','Active');
}
if($pref == 'all_codes'){
//$search -> AddDBParam('c_Active_Status','Active');
$_SESSION['budget_code_view_pref'] = 'all_codes';
}


if(($sort_by == 'Active_From')||($sort_by == 'Active_To')){
$search -> AddSortParam($sort_by,'descend');
}else{
$search -> AddSortParam($sort_by,'ascend');
}
$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
####################################
## END: GRAB CURRENT BUDGET CODES ##
####################################


#####################################
## START: DISPLAY BUDGET CODE LIST ##
#####################################
 
 ?>

<html>
<head>
<title>SIMS - Budget Code Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Admin</h1><hr /></td></tr>
			
			<?php if($confirm_update == '1'){ ?>

			<tr><td colspan="2"><p class="alert_small">Record successfully updated.</p></td></tr>
			
			<?php $confirm_update = '0';
			} ?>

			<?php if($confirm_new == '1'){ ?>

			<tr><td colspan="2"><p class="alert_small">Record successfully created.</p></td></tr>
			
			<?php $confirm_new = '0';
			} ?>

			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL Budget Codes</strong> | <?php echo $searchResult['foundCount'];?> records found.</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="bgt_code_admin.php?action=print_friendly&sort_by=<?php echo $sort_by;?>&pref=<?php echo $_SESSION['budget_code_view_pref'];?>" target="_blank">Print</a> | <a href="bgt_code_admin.php?action=new">New Budget Code</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode">Budget Code</a></td><td class="body"><a href="bgt_code_admin.php?action=show_all&sort_by=BudgetCodeDescription">Description</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=Active_From">Active From</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=Active_To">Active To</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=c_Active_Status">Status</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=BgtAuthorityApproving_sims_ID">Approved By</a></td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body" nowrap valign="top"><a href="bgt_code_admin.php?action=show_1&budget_code=<?php echo $searchData['c_BudgetCode'][0];?>"><?php echo $searchData['c_BudgetCode'][0];?></a></td><td class="body" valign="top"><?php echo $searchData['BudgetCodeDescription'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_From'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_To'][0];?></td><td class="body" nowrap valign="top"><font <?php if($searchData['c_Active_Status'][0] != 'Active'){?>color="red"<?php }?>><?php echo $searchData['c_Active_Status'][0];?></font></td><td class="body" valign="top"><?php echo $searchData['BgtAuthorityApproving_sims_ID'][0];?></td></tr>
								<?php } ?>


							
							</table>
			
			</td></tr>
			
			
			</table>

</td></tr>
</table>







</body>

</html>
 
<?php
###################################
## END: DISPLAY BUDGET CODE LIST ##
###################################


 
 
 } else {
 
 echo 'Error';
 
 }
 ?>
<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');

$selected_month = $_GET['selected_month'];
$selected_month_m = substr($selected_month,0,2);
$selected_month_d = substr($selected_month,3,2);
$selected_month_y = substr($selected_month,6,4);

//echo '<p>$selected_month: '.$selected_month;

// get year, eg 2006
//$year = date('Y');


// get month, eg 04
//$month = date('n');
//$month = date('02');


// get day, eg 3
//$day = date('j');

// get number of days in month, eg 28
//$daysInMonth = date("t",mktime(0,0,0,$month,1,$year));
$daysInMonth = date("t",mktime(0,0,0,$selected_month_m,1,$selected_month_y));
//echo '<p>$daysInMonth: '.$daysInMonth;

//$pay_period_end = $month.'/'.$daysInMonth.'/'.$year;
$pay_period_end = $selected_month;
$month_code = ltrim($selected_month_m,"0").'.'.$selected_month_y;


$month_menu_span_begin = date("m",mktime(0, 0, 0, date("m")-2, date("t"), date("Y"))).'/'.date("t",mktime(0, 0, 0, date("m")-2, date("t"), date("Y"))).'/'.date("Y",mktime(0, 0, 0, date("m")-2, date("t"), date("Y")));
$month_menu_span_end = date("m",mktime(0, 0, 0, date("m")+6, date("t"), date("Y"))).'/'.date("t",mktime(0, 0, 0, date("m")+6, date("t"), date("Y"))).'/'.date("Y",mktime(0, 0, 0, date("m")+6, date("t"), date("Y")));

//echo '<p>$month_menu_span_begin: '.$month_menu_span_begin;
//echo '<p>$month_menu_span_end: '.$month_menu_span_end;

//echo '<p>$pay_period_end: '.$pay_period_end;
//echo '<p>$_SESSION[workgroup]: '.$_SESSION['workgroup'];
//echo '<p>$selected_month: '.$selected_month;

################################################
## START: FIND WORKGROUP LEAVE FOR THIS MONTH ##
################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','leave_request_hrs_subset','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('c_searchkey_monthcode_workgroup',$_SESSION['workgroup'].'.'.$month_code);
$search -> AddDBParam('c_searchkey_monthcode_otherworkgrouplist',$_SESSION['workgroup'].'.'.$month_code);
$search -> AddDBParam('-lop','or');

$search -> AddSortParam('leave_requests::signer_ID_owner','ascend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//$recordData = current($searchResult['data']);
################################################
## END: FIND WORKGROUP LEAVE FOR THIS MONTH ##
################################################

################################################
## START: FIND SEDL HOLIDAYS FOR THIS MONTH ##
################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','SEDL_holidays','all');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('c_HolidayMonthYrKey',$month_code);

$search3 -> AddSortParam('c_HolidayDayOfMonth','ascend');

$searchResult3 = $search3 -> FMFind();

//echo $searchResult3['errorCode'];
//echo '<p>$searchResult3[foundCount]: '.$searchResult3['foundCount'];
//$recordData3 = current($searchResult3['data']);
if($searchResult3[foundCount] > 0){
$i=0;
		foreach($searchResult3['data'] as $key => $searchData3) { 
		$holidays[$i] = $searchData3['c_HolidayDay_numeric'][0];
		$i++;
		}
}else{
$holidays[0] = '';
}
################################################
## END: FIND SEDL HOLIDAYS FOR THIS MONTH ##
################################################

################################################
## START: FIND PAY PERIODS FOR DROP DOWN LIST ##
################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','timesheet_pay_periods',6);
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('PayPeriodEnd_exempt',$month_menu_span_begin.'...'.$month_menu_span_end,'gte');
//$search2 -> AddDBParam('PayPeriodEnd_exempt',$month_menu_span_end,'lte');

$search2 -> AddSortParam('PayPeriodEnd_exempt','ascend');

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult['foundCount'];
//$recordData2 = current($searchResult2['data']);
################################################
## END: FIND PAY PERIODS FOR DROP DOWN LIST ##
################################################


// get first day of the month, eg 4
$firstDay = date("w", mktime(0,0,0,$selected_month_m,1,$selected_month_y));
//echo '<p>$firstDay: '.$firstDay;

// calculate total spaces needed in array
$tempDays = $firstDay + $daysInMonth;
//echo '<p>$tempDays: '.$tempDays;

// calculate total rows needed
$weeksInMonth = ceil($tempDays/7);
//echo '<p>$weeksInMonth: '.$weeksInMonth;

//$counter = 0;
for($j=0;$j<$weeksInMonth;$j++) {

//echo "<br>week $j<br>";

    for($i=0;$i<7;$i++) {
        $counter++;
        $week[$j][$i] = $counter;
    }
}
//print_r($week);
/*
function fillArray() {
	// create a 2-d array
	for($j=0;$j<$this->weeksInMonth;$j++) {
		for($i=0;$i<7;$i++) {
			$counter++;
			$this->week[$j][$i] = $counter;
			// offset the days
			$this->week[$j][$i] -= $this->firstDay;
			if (($this->week[$j][$i] < 1) || ($this->week[$j][$i] > $this->daysInMonth)) {    
				$this->week[$j][$i] = "";
			}
		}
	}
}
*/

?>
<html>
<head>
<title>SIMS - Staff Leave Calendar</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">
//<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>

</head>


<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Staff Leave Calendar</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong><?php echo $_SESSION['workgroup'];?> Staff Leave Calendar</strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: This report returns current live data from staff leave requests. Leave hours reflected here represent requested leave and may not be approved yet.</p></td></tr>

			<tr bgcolor="#e2eaa4"><td class="body" colspan=2>

							<form name="form1" method="get">
							<table cellspacing="0" cellpadding="1" width="100%" border="0" bordercolor="#cccccc">
							

							<tr valign="middle" height="30"><td class="body"><h1><?= date('F', mktime(0,0,0,$selected_month_m,1,$selected_month_y)).' '.$selected_month_y; ?></h1>
							</td><td align="right">Show Month: 
								<select name="menu1" onChange="MM_jumpMenu('parent',this,0)">
								
								<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
									<option value="staff_leave_calendar.php?selected_month=<?php echo $searchData2['PayPeriodEnd_exempt'][0];?>"<?php if($searchData2['PayPeriodEnd_exempt'][0] == $pay_period_end){echo ' selected';}?>><?php echo $searchData2['PayPeriodEnd_exempt'][0];?></option>
								<?php } ?>

								 </select>
							</form>
							
							</td></tr>
							
							<tr><td colspan="2" class="body" valign="top" width="100%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								<tr><td valign="top">



<table width="100%" border="1" cellpadding="2" cellspacing="2" class="sims">
	<tr bgcolor="#ebebeb">
		<th>Sun</th>
		<th width="20%">Mon</th>
		<th width="20%">Tue</th>
		<th width="20%">Wed</th>
		<th width="20%">Thur</th>
		<th width="20%">Fri</th>
		<th>Sat</th>
	</tr>

<?php
$today=date("m/d/Y");
//echo '<p>$today: '.$today;
foreach ($week as $key => $val) { ?>
	
<tr height="100" valign="top">

<?php
$x = 0;
	for ($i=0;$i<7;$i++) { 
	
		$day_num = $val[$i] - $firstDay;
		$day_of_week = date("w",mktime(0,0,0,$selected_month_m,$day_num,$selected_month_y));

		if(($day_num > 0) && ($day_num <= $daysInMonth)){ ?>
		
		<td nowrap <?php if($today == date("m/d/Y",mktime(0,0,0,$selected_month_m,$day_num,$selected_month_y))){ echo ' bgcolor="#c2e0ff"';}elseif(($day_of_week == '0')||($day_of_week == '6')){ echo ' bgcolor="#ebebeb"';}elseif(in_array($day_num,$holidays)){ echo ' bgcolor="#cccccc"';}?>><?php echo $day_num;?><br>

		<?php if(!in_array($day_num,$holidays)){ ?>
		
				<?php foreach($searchResult['data'] as $key => $searchData) { 
				
						if($searchData['c_leave_hrs_date_d'][0] == $day_num){ 
						
									if($searchData['leave_requests::approval_status'][0] == 'Approved'){
									
										//echo '<font color="blue">'.$searchData['leave_requests::signer_ID_owner'][0].' - '.$searchData['c_leave_hrs_num_type'][0].'</font><br>';
										echo '<font color="blue">'.$searchData['leave_requests::signer_ID_owner'][0].' <span class="tiny">('.$searchData['leave_hrs_time_begin'][0].'-'.$searchData['leave_hrs_time_end'][0].' | '.$searchData['c_leave_hrs_num_type_filtered'][0].')</font></span><br>';
						
									} else {
									
										//echo '<font color="red">'.$searchData['leave_requests::signer_ID_owner'][0].' - '.$searchData['c_leave_hrs_num_type'][0].'</font><br>';
										echo '<font color="red">'.$searchData['leave_requests::signer_ID_owner'][0].' <span class="tiny">('.$searchData['leave_hrs_time_begin'][0].'-'.$searchData['leave_hrs_time_end'][0].' | '.$searchData['c_leave_hrs_num_type_filtered'][0].')</font></span><br>';
						
									}
						
						
						}
				
				} 

		}else{
		echo '<strong>SEDL HOLIDAY</strong>';
		}
		?>
		
		
		</td>

		<?php }else{ echo "<td bgcolor=\"#ebebeb\"></td>";}
	}
	echo "</tr>";
}
?>
</table> <br>
*Approved leave shown in <font color="blue">blue</font>. Pending leave shown in <font color="red">red</font>.<p>









								</td></tr>
								</table>
							
							</td></tr>
							

							
							
							
							
							</table>

	

			
			
			
			
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>







</body>

</html>
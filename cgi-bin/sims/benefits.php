<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

$debug = 'off';
$benefits_access = 'yes';

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


$id = $_GET['id'];



#########################################
## START: FIND BENEFITS REPORT FOR THIS USER
#########################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','benefits');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$id);
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam($sortfield,'PERIODEND');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);

if($recordData['staff_ID'][0] != $_SESSION['staff_ID']){
echo '<img src="images/busted.jpg"><p>';

echo 'Benefits information is private.<p>
<a href="menu_paystubs.php"><< Back to menu</a>';


// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','BENEFITS_VIEW_UNAUTHORIZED_ATTEMPT');
$newrecord -> AddDBParam('table','benefits');
$newrecord -> AddDBParam('object_ID',$recordData['form_ID'][0]);
$newrecord -> AddDBParam('affected_row_ID','N/A');
$newrecord -> AddDBParam('ip_address',$ip);
$newrecord -> AddDBParam('notes','Unauthorized attempt by '.$_SESSION['user_ID'].' to view benefits information for '.$recordData['NAME'][0].' for the pay period '.$recordData['paystubs_timesheets_byPeriodEnd::c_PayPeriodEnd'][0]);

$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];



$to = 'sims@sedl.org';
$subject = 'SIMS NOTIFICATION: SECURITY ALERT';
$message = 

'SIMS has detected an unauthorized attempt to view benefits information.'."\n\n".

'---------------------------------------------------------------------------'."\n".
'User: '.$_SESSION['user_ID']."\n".
'Target staff: '.$recordData['NAME'][0].' - '.$recordData['paystubs_timesheets_byPeriodEnd::c_PayPeriodEnd'][0]."\n".
'---------------------------------------------------------------------------'."\n\n".

'This information was saved in the SIMS audit_table.';

$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org';

mail($to, $subject, $message, $headers);

exit;
}

if($debug == 'on'){
echo '#####################################<br>';
echo '######### DEBUGGING IS ON ###########<br>';
echo '#####################################<p>';

echo '<p>staff_ID: '. $_SESSION['staff_ID'];
echo '<p>$_SESSION[timesheet_approval_not_required]: '. $_SESSION['timesheet_approval_not_required'];
echo '<p>$_SESSION[last_pay_period_end]: '. $_SESSION['last_pay_period_end'];
echo '<p>$_SESSION[last_pay_period_end_m]: '. $_SESSION['last_pay_period_end_m'];
echo '<p>$_SESSION[last_pay_period_end_d]: '. $_SESSION['last_pay_period_end_d'];
echo '<p>$_SESSION[last_pay_period_end_y]: '. $_SESSION['last_pay_period_end_y'];
echo '<p>$_SESSION[current_pay_period_end]: '. $_SESSION['current_pay_period_end'];
echo '<p>$_SESSION[timesheet_owner_FTE_status]: '. $_SESSION['timesheet_owner_FTE_status'];

#######################################
## START: ECHO D-BASE BENEFITS FIELDS ##
#######################################
echo '<p><table cellpadding="6" border="1" class="sims"><tr bgcolor="#ebebeb"><td>#</td><td>FieldName</td><td>EOB-Display</td><td>Value</td></tr>';

echo '<tr><td>1</td><td>NAME: '. '</td>			<td>Name</td><td>'.$recordData['NAME'][0].'</td></tr>';

echo '<tr><td>18-D</td><td>PERCENTTIM: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['PERCENTTIM'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>SALARY: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['SALARY'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>BENEYEAR: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['BENEYEAR'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LIFEINS: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LIFEINS'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>SSRATE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['SSRATE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>SSBASE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['SSBASE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABSSTAX: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABSSTAX'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>MDRATE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['MDRATE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>MDBASE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['MDBASE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABMDTAX: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABMDTAX'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>RETRATE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['RETRATE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABRET: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABRET'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABDISABIL: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABDISABIL'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABHEALTH: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABHEALTH'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>ANNHEALTH: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['ANNHEALTH'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABPRUCARE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABPRUCARE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>ANNPRUCARE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['ANNPRUCARE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABAETNA: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABAETNA'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>ANNAETNA: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['ANNAETNA'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABHSA: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABHSA'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>ANNHSA: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['ANNHSA'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABFSAMED: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABFSAMED'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>ANNFSAMED: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['ANNFSAMED'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABDENTAL: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABDENTAL'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>ANNDENTAL: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['ANNDENTAL'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABLIFE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABLIFE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>ANNLIFE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['ANNLIFE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABPAI: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABPAI'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>ANNPAI: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['ANNPAI'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABPARKTAX: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABPARKTAX'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>ANNPARKTAX: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['ANNPARKTAX'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>VACACCRUAL: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['VACACCRUAL'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>VACRATE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['VACRATE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>ANNVAC: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['ANNVAC'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>SICKPCNT: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['SICKPCNT'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABSICK: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABSICK'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>WORKDAYS: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['WORKDAYS'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>PSNLDAYS: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['PSNLDAYS'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>HOLIDAYS: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['HOLIDAYS'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>PSNLPCNT: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['PSNLPCNT'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABPSNL: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABPSNL'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LEAVEPCNT: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LEAVEPCNT'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABLEAVE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABLEAVE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>WORKPCNT: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['WORKPCNT'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABWORK: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABWORK'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>SUTAPCNT: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['SUTAPCNT'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>SUTABASE: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['SUTABASE'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABSUTA: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABSUTA'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>DATEPREP: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['DATEPREP'][0].'</td></tr>';

echo '<tr><td>18-D</td><td>STAFF_ID: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['staff_ID'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>FORM_ID: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['form_ID'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>CREATION_TIMSTAMP: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['creation_timestamp'][0].'</td></tr>';

echo '</table>';



echo '######################################<br>';
echo '######### END OF DEBUGGING ###########<br>';
echo '######################################<p><hr>';

#######################################
## END: ECHO D-BASE BENEFITS FIELDS ##
#######################################


}


/*
echo '<p>last_pay_period_end: '. $_SESSION['last_pay_period_end'];
echo '<p>last_pay_period_end_m: '. $_SESSION['last_pay_period_end_m'];
echo '<p>last_pay_period_end_d: '. $_SESSION['last_pay_period_end_d'];
echo '<p>last_pay_period_end_y: '. $_SESSION['last_pay_period_end_y'];

echo '<p>timesheet_name: '. $_SESSION['timesheet_name'];
*/
#########################################
## END: FIND BENEFITS REPORT FOR THIS USER
#########################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
if ($benefits_access == 'yes') { //IF BENEFITS ACCESS IS TURNED ON 
?>

<html>
<head>
<title>SIMS: Staff Member Benefits</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>

<style type="text/css">

table.stub td {
	color: #000000;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:10px;
	background-color:#ffffff;
	padding:0px ;
	border-width:0px;
	padding-right:20px;
	padding-top:2px;
	padding-bottom:2px;
	margin:0px;
	vertical-align: text-top;
	white-space: nowrap;
}


hr.ee {
border: none 0;
border-top: 1px dotted #000000;
width: 100%;
height: 1px;
margin: 0px;
text-align: left;
padding: 0px;
}





</style>



</head>

<BODY BGCOLOR="#FFFFFF" onLoad="zoomWindow()">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
		
			<tr>
			
			<td colspan="2" class="body" nowrap style="text-align:center;padding-left:0px;padding-top:8px">
			DATE PREPARED: <?php echo $recordData['c_date_prepared'][0];?>
			</td>
			
			</tr>
			<tr><td class="body" nowrap style="padding:0px">SOUTHWEST EDUCATIONAL DEVELOPMENT CORPORATION<br>STAFF MEMBER BENEFITS PAID BY SEDL</td><td align="right" style="padding:0px"><img src="/staff/sims/images/logo-new-grayscale.png" alt="SEDL-Advancing Research, Improving Education" border="0"></td></tr>


			<tr>
			
			<td class="body" nowrap style="padding-left:0px;padding-top:8px">
			Staff Member's Name:&nbsp;&nbsp;&nbsp;<strong><?php echo $recordData['NAME'][0];?></strong><br>&nbsp;<br>
			Annual Amount of Life Insurance*:&nbsp;&nbsp;&nbsp;<?php echo number_format((double)$recordData['c_ANNLIFEINS'][0]);?>
			</td>
			
			<td class="body" nowrap style="text-align:right;padding-left:0px;padding-top:8px">
			Actual Annual Salary (AAS):&nbsp;&nbsp;&nbsp;<?php echo number_format((double)$recordData['SALARY'][0],2);?><br>&nbsp;<br>
			Percent of Time Employed:&nbsp;&nbsp;&nbsp;<?php echo $recordData['PERCENTTIM'][0];?>
			</td>
			
			</tr>

			
			
			
			<tr><td colspan="2" style="width:50%;padding-left:0px;padding-bottom:0px;padding-top:0px"><hr class="ee">
			
						<table cellpadding=4 cellspacing=0 width="100%">

								<tr><td><u>BENEFIT</u></td><td><u>SEDL RATE</u></td><td nowrap><u>ANNUAL COST PAID BY SEDL</u></td><td style="text-align:right"><u>AMOUNT</u></td></tr>
		
								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Social Security</td><td><?php echo $recordData['SSRATE'][0];?>% x <?php echo $recordData['BENEYEAR'][0];?> base (110,100.00) or AAS, whichever is less.</td><td nowrap><?php echo $recordData['SSRATE'][0];?>% x <?php echo number_format((double)$recordData['SALARY'][0],2);?></td><td style="text-align:right"><?php echo number_format((double)$recordData['LABSSTAX'][0],2);?></td>
								</tr>		
										
								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Medicare</td><td><?php echo $recordData['MDRATE'][0];?>% x <?php echo $recordData['BENEYEAR'][0];?> base (400,000.00) or AAS, whichever is less.</td><td nowrap><?php echo $recordData['MDRATE'][0];?>% x <?php echo number_format((double)$recordData['SALARY'][0],2);?></td><td style="text-align:right"><?php echo number_format((double)$recordData['LABMDTAX'][0],2);?></td>
								</tr>		
								
								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Retirement Plan (TIAA-CREF)</td><td><?php echo $recordData['RETRATE'][0];?>% x AAS</td><td nowrap><?php echo $recordData['RETRATE'][0];?>% x <?php echo number_format((double)$recordData['SALARY'][0],2);?></td><td style="text-align:right"><?php echo number_format((double)$recordData['LABRET'][0],2);?></td>
								</tr>		

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Long Term Disability **</td><td><div style="float:left;width:75px;text-align:right"><?php echo $recordData['LABDISABIL'][0];?>/mo.</div></td><td nowrap><div style="float:left;width:105px;text-align:right"><?php echo $recordData['LABDISABIL'][0];?> x 12 mo.</div></td><td style="text-align:right"><?php echo number_format((double)$recordData['ANNDISABIL'][0],2);?></td>
								</tr>		

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Health Insurance ***</td><td><div style="float:left;width:75px;text-align:right"><?php echo $recordData['LABHEALTH'][0];?>/mo.</div></td><td nowrap><div style="float:left;width:105px;text-align:right"><?php echo $recordData['LABHEALTH'][0];?> x 12 mo.</div></td><td style="text-align:right"><?php echo number_format((double)$recordData['ANNHEALTH'][0],2);?></td>
								</tr>		

<?php if($recordData['LABFSAMED'][0] != ''){ // STAFF MEMBER HAS FSA ?>
								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px;padding-left:25px">Medical Dental FSA ***</td><td><div style="float:left;width:75px;text-align:right"><?php if($recordData['LABFSAMED'][0] != ''){echo $recordData['LABFSAMED'][0];}else{echo '0.00';}?>/mo.</div></td><td nowrap><div style="float:left;width:105px;text-align:right"><?php if($recordData['LABFSAMED'][0] != ''){echo $recordData['LABFSAMED'][0];}else{echo '0.00';}?> x 12 mo.</div></td><td style="text-align:right"><?php echo number_format((double)$recordData['ANNFSAMED'][0],2);?></td>
								</tr>
								
<?php }elseif($recordData['LABHSA'][0] != ''){ // STAFF MEMBER HAS HSA ?>

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px;padding-left:25px">HSA ***</td><td><div style="float:left;width:75px;text-align:right"><?php if($recordData['LABHSA'][0] != ''){echo $recordData['LABHSA'][0];}else{echo '0.00';}?>/mo.</div></td><td nowrap><div style="float:left;width:105px;text-align:right"><?php if($recordData['LABHSA'][0] != ''){echo $recordData['LABHSA'][0];}else{echo '0.00';}?> x 12 mo.</div></td><td style="text-align:right"><?php echo number_format((double)$recordData['ANNHSA'][0],2);?></td>
								</tr>		

<?php }else{ // STAFF MEMBER HAS NEITHER FSA NOR HSA 
} ?>

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Dental Insurance ***</td><td><div style="float:left;width:75px;text-align:right"><?php echo $recordData['LABDENTAL'][0];?>/mo.</div></td><td nowrap><div style="float:left;width:105px;text-align:right"><?php echo $recordData['LABDENTAL'][0];?> x 12 mo.</div></td><td style="text-align:right"><?php echo number_format((double)$recordData['ANNDENTAL'][0],2);?></td>
								</tr>		

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Life/AD&D Insurance ***</td><td><div style="float:left;width:75px;text-align:right"><?php echo number_format((double)$recordData['c_LIFEADD'][0],2,'.',',');?>/mo.</td><td nowrap><div style="float:left;width:105px;text-align:right"><?php echo $recordData['c_LIFEADD'][0];?> x 12 mo.</div></td><td style="text-align:right"><?php echo number_format((double)$recordData['c_ANNLIFEADD'][0],2,'.',',');?></div></td>
								</tr>		

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Supplemental AD&D</td><td><div style="float:left;width:75px;text-align:right"><?php echo $recordData['LABLIFE'][0];?>/mo.</div></td><td nowrap><div style="float:left;width:105px;text-align:right"><?php echo $recordData['LABLIFE'][0];?> x 12 mo.</div></td><td style="text-align:right"><?php echo number_format((double)$recordData['ANNLIFE'][0],2);?></td>
								</tr>		

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Parking Sales Tax</td><td><div style="float:left;width:75px;text-align:right"><?php echo $recordData['LABPARKTAX'][0];?>/mo.</div></td><td nowrap><div style="float:left;width:105px;text-align:right"><?php echo $recordData['LABPARKTAX'][0];?> x 12 mo.</div></td><td style="text-align:right"><?php echo number_format((double)$recordData['ANNPARKTAX'][0],2);?></td>
								</tr>		

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Vacation Leave ****</td><td><?php echo $recordData['VACACCRUAL'][0];?>/20 hrs worked</td><td nowrap><?php echo $recordData['VACRATE'][0];?>% x <?php echo number_format((double)$recordData['SALARY'][0],2);?></td><td style="text-align:right"><?php echo number_format((double)$recordData['ANNVAC'][0],2);?></td>
								</tr>		

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Sick Leave</td><td>1/20 hrs worked x % used</td><td nowrap>5% x <?php echo number_format((double)$recordData['SALARY'][0],2);?> x <?php echo $recordData['SICKPCNT'][0];?>% used</td><td style="text-align:right"><?php echo number_format((double)$recordData['LABSICK'][0],2);?></td>
								</tr>		

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Personal Holiday Leave</td><td><?php echo $recordData['PSNLDAYS'][0];?> Days/<?php echo $recordData['WORKDAYS'][0];?> work days (<?php echo $recordData['PSNLPCNT'][0];?>%)</td><td nowrap><?php echo $recordData['PSNLPCNT'][0];?>% x <?php echo number_format((double)$recordData['SALARY'][0],2);?></td><td style="text-align:right"><?php echo number_format((double)$recordData['LABPSNL'][0],2);?></td>
								</tr>		

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">SEDL Holiday Leave</td><td><?php echo $recordData['HOLIDAYS'][0];?> Days/<?php echo $recordData['WORKDAYS'][0];?> work days (<?php echo $recordData['LEAVEPCNT'][0];?>%)</td><td nowrap><?php echo $recordData['LEAVEPCNT'][0];?>% x <?php echo number_format((double)$recordData['SALARY'][0],2);?></td><td style="text-align:right"><?php echo number_format((double)$recordData['LABLEAVE'][0],2);?></td>
								</tr>		

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Worker's Compensation</td><td><?php echo $recordData['WORKPCNT'][0];?> x AAS</td><td nowrap><?php echo $recordData['WORKPCNT'][0];?>% x <?php echo number_format((double)$recordData['SALARY'][0],2);?></td><td style="text-align:right"><?php echo number_format((double)$recordData['LABWORK'][0],2);?></td>
								</tr>		

								<tr valign="top">						
								<td nowrap class="body" style="padding-top:0px">Unemployment Compensation</td><td><?php echo $recordData['SUTAPCNT'][0];?> x <?php echo $recordData['BENEYEAR'][0];?> Base</td><td nowrap><?php echo $recordData['SUTAPCNT'][0];?>% x <?php echo number_format((double)$recordData['SUTABASE'][0]);?></td><td style="text-align:right"><?php echo number_format((double)$recordData['LABSUTA'][0],2);?></td>
								</tr>		

								<tr valign="top">						
								<td colspan="4" class="body" style="text-align:right;padding:0px 0px 10px 0px"><hr class="ee" style="padding:0px;margin-bottom:0px"><br>Total SEDL contribution to staff member's benefits (except as noted below):&nbsp;&nbsp;&nbsp;(A)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo number_format((double)$recordData['c_BENETOTAL'][0],2);?><br>
								
								</td>
								</tr>		

						</table><hr class="ee" style="padding:0px 0px 0px 0px;margin-bottom:0px 0px 0px 0px">
						
			</td></tr>

			<tr>
			
			<td style="width:50%;padding-left:0px;padding-bottom:0px;padding-top:10px;vertical-align:top">

				<table style="width:100%;padding:0px;margin:0px;border:0px;vertical-align:top">
				<tr><td style="vertical-align:text-top;padding:4px">*</td><td style="vertical-align:text-top;padding:4px">(AAS rounded up to the nearest thousand not to exceed $100,000; amounts over $50,000 includable as taxable income.)</td></tr>
				<tr><td style="vertical-align:text-top;padding:4px">**</td><td style="vertical-align:text-top;padding:4px">Eligibility: Minimum 32 hrs/wk. (80%)</td></tr>
				<tr><td style="vertical-align:text-top;padding:4px">***</td><td style="vertical-align:text-top;padding:4px">Eligibility: Minimum 20 hrs/wk. (50%)</td></tr>
				<tr><td style="vertical-align:text-top;padding:4px">****</td><td style="vertical-align:text-top;padding:4px">1 hr/20 worked for staff members under 5 years cumulative service. 1.5 hr/20 worked for staff having 5 years, or more cumulative service.</td></tr>
				<tr><td style="vertical-align:text-top;padding:4px">NOTE:</td><td style="vertical-align:text-top;padding:4px">Jury duty leave & special emergency leave are not sufficiently predictable per individual staff member to be computed.</td></tr>
				</table>

			</td>
			
			<td style="text-align:right;padding-left:0px;padding-bottom:0px;padding-top:10px;vertical-align:top" nowrap>
			Staff Member's Actual Annual Salary:&nbsp;&nbsp;&nbsp;(B)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo number_format((double)$recordData['SALARY'][0],2);?><br>&nbsp;<br>
			
			Staff Member's Actual Benefit Rate:&nbsp;&nbsp;&nbsp;(A)/(B)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo number_format((double)$recordData['c_ACTUALBENERATE'][0]*100,2);?>%<br>&nbsp;<br><hr class="ee" style="padding:10px 0px 0px 0px;margin-bottom:10px 0px 0px 0px"><br>
			</td>
			
			</tr>

			<tr><td colspan="2" style="padding-left:0px;padding-top:0px">
								

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>

<?php } else { //IF PAYSTUBS ACCESS IS TURNED OFF?>

<html>
<head>
<title>SIMS: Paystubs</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS PAYSTUBS</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<?php if($_SESSION['timesheet_signed_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your timesheet has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['timesheet_signed_staff'] == '1_revised'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your revised timesheet has been successfully re-submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>


			<?php } elseif($_SESSION['timesheet_signed_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your timesheet, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance (errorCode_998).  </p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>
			
			<?php } ?>
			
			
			<tr><td colspan="2">
			
			BENEFITS ACCESS IS TEMPORARILY UNAVAILABLE

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>


<?php } ?>

<?php //} else { ?>



<?php //} ?>
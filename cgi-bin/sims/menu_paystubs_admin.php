<?php
session_start();

include_once('sims_checksession.php');

if($_SESSION['paystub_admin_access'] !== 'Yes'){

header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
exit;
}

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

$debug = 'off';
$paystub_access = 'yes';
$today = date("m/d/Y");

$action = $_GET['action'];
$query = $_GET['query'];
//$today = '10/15/2008';
//echo '<p>$today: '.$today;
//echo '<p>$_SESSION[staff_ID]: '.$_SESSION['staff_ID'];
//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
if($action == 'show_all'){

#######################################
## START: GRAB CURRENT STAFF RECORDS ##
#######################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff_table','all');
$search -> SetDBPassword($webPW,$webUN);

if($query == 'former_staff'){
$search -> AddDBParam('current_employee_status','Former Employee');
} else {
$search -> AddDBParam('current_employee_status','SEDL Employee');
}

$search -> AddSortParam('c_full_name_last_first','ascend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);
#####################################
## END: GRAB CURRENT STAFF RECORDS ##
#####################################


###################################
## START: DISPLAY ALL STAFF LIST ##
###################################
 
 ?>

<html>
<head>
<title>SIMS - Paystubs Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Paystubs Admin</h1><hr /></td></tr>
			
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL <?php if($query == 'former_staff'){?>Former <?php }?>Staff Paystubs</strong> | <?php echo $searchResult['foundCount'];?> records found. | <?php if($query == 'former_staff'){?><a href="menu_paystubs_admin.php?action=show_all">Show current staff</a><?php }else{?><a href="menu_paystubs_admin.php?action=show_all&query=former_staff">Show former staff</a><?php }?></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILES-->


							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">Name</td><td class="body">Unit</td><td class="body">Title</td><td class="body">FTE</td><td class="body">Empl. Start Date</td><td class="body">Empl. Term. Date</td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body"><?php echo $searchData['staff_ID'][0];?></td><td class="body" nowrap><a href="menu_paystubs_admin.php?action=show_1&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo stripslashes($searchData['c_full_name_last_first'][0]);?></a></td><td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td><td class="body"><?php echo $searchData['job_title'][0];?></td><td class="body"><?php echo $searchData['FTE_status'][0];?></td><td class="body"><?php echo $searchData['empl_start_date'][0];?></td><td class="body" nowrap><?php if($searchData['empl_end_date'][0] == ''){echo 'Current';}else{echo $searchData['empl_end_date'][0];}?></td></tr>
								<?php } ?>

<!--END FIRST SECTION: STAFF PROFILES-->		

							
							</table>
			
			</td></tr>
			
			
			</table>

</td></tr>
</table>







</body>

</html>



<?php 
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
}elseif($action == 'show_1'){ 

#########################################################
## START: FIND LAST 12 PAYSTUBS FOR THE SELECTED STAFF ##
#########################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','paystubs','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$_GET['staff_ID']);
//$search -> AddDBParam('c_periodend_local',$today,'lte');
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('paystub_ID','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$_SESSION['timesheet_foundcount'] = $searchResult['foundCount'];
$recordData = current($searchResult['data']);
#################################################
## END: FIND TIMESHEETS FOR THE SELECTED STAFF ##
#################################################

##################################################
## START: GET SELECTED STAFF NAME AND WORKGROUP ##
##################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff_table','all');
$search2 -> SetDBPassword($webPW,$webUN);

$search2 -> AddDBParam('staff_ID',$_GET['staff_ID']);

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);

$fullname = $recordData2['name_timesheet'][0];
$unit = $recordData2['primary_SEDL_workgroup'][0];
################################################
## END: GET SELECTED STAFF NAME AND WORKGROUP ##
################################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Paystubs Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Paystubs Admin</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $fullname;?> (<?php echo $unit;?>)</b></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="menu_paystubs_admin.php?action=show_all" title="Return to SIMS Paystubs Admin screen.">SIMS Paystubs Admin</a> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body">Pay Period Ending</td>
						<td class="body" align="right">Gross</td>
						<td class="body" align="right">Health</td>
						<td class="body" align="right">Dental</td>
						<td class="body" align="right">Ret(E)</td>
						<td class="body" align="right">Ret(L)</td>
						<td class="body" align="right">Fed Tax With</td>
						<td class="body" align="right">SS Tax With</td>
						<td class="body" align="right">Med Tax With</td>
						<td class="body" align="right">State</td>
						<td class="body" align="right">Park</td>
						<td class="body" align="right">Net</td>
						
						</tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="body"><?php echo $searchData['paystub_ID'][0];?></td>
						<td class="body"><a href="/staff/sims/menu_paystubs_admin.php?paystub_ID=<?php echo $searchData['paystub_ID'][0];?>&action=show_stub" title="Click here to view this paystub." target="_blank"><?php echo $searchData['paystubs_timesheets_byPeriodEnd::c_PayPeriodEnd'][0];?> (<?php echo substr($searchData['payroll_type'][0],0,1);?>)</a></td>
						<td class="body" align="right"><?php echo $searchData['CURGRPAY'][0];?></td>
						
						<td class="body" align="right"><?php if($searchData['CURPTHEAL'][0] > 0){echo $searchData['CURPTHEAL'][0];}elseif($searchData['CURPTPRU'][0] > 0){echo $searchData['CURPTPRU'][0];}else{echo '0.00';}?></td>
						<td class="body" align="right"><?php echo $searchData['CURPTDENT'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURRET02'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['LABRET14'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURFWTAX'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURSSTAX'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURMDTAX'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURSTTAX'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['CURPARK'][0];?></td>
						
						<td class="body" align="right"><?php echo $searchData['CURNETPAY'][0];?></td>
						
						</tr>
			
						<?php } ?>
						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>



<?php 
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
}elseif($action == 'show_stub'){ 



$paystub_ID = $_GET['paystub_ID'];



#########################################
## START: FIND PAYSTUBS FOR THIS USER
#########################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','paystubs');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('paystub_ID','=='.$paystub_ID);
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam($sortfield,'PERIODEND');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
#########################################
## END: FIND TIMESHEETS FOR THIS USER
#########################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Paystub - <?php echo $recordData['paystubs_timesheets_byPeriodEnd::c_PayPeriodEnd'][0];?></title>
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
			
		
			<tr><td class="body" nowrap style="padding:0px">SOUTHWEST EDUCATIONAL DEVELOPMENT CORPORATION<br>YEAR TO DATE PAYROLL INFORMATION</td><td align="right" style="padding:0px"><img src="/staff/sims/images/logo-new-grayscale.png" alt="SEDL-Advancing Research, Improving Education" border="0"></td></tr>

			
			<tr><td colspan="2" class="body" nowrap style="padding-left:0px;padding-top:8px">
			<?php echo $recordData['NAME'][0];?><br>
			<?php echo $recordData['ADDRESS'][0];?><br>
			<?php echo $recordData['CITY'][0];?>, <?php echo $recordData['STATE'][0];?> <?php echo $recordData['ZIPCODE'][0];?> 
			</td></tr>

			

			
			<tr><td colspan="2" style="padding-left:0px;padding-bottom:0px;padding-top:0px"><hr class="ee">
			
						<table cellpadding=4 cellspacing=0 width="100%">

								<tr><td width="33%"><u>PERSONAL DATA</u></td><td width="34%"><u>DEPOSIT DATA</u></td><td width="33%"><u>SECTION 125 ELECTIONS</u></td></tr>
		
								<tr valign="top">						
								<td class="body" style="padding-top:0px">
		
										<table cellpadding="2" cellspacing="0" class="stub">
										<tr><td>Marital Status</td><td><?php echo $recordData['MARITALSTA'][0];?></td></tr>
										<tr><td>Number of Exemptions</td><td><?php echo $recordData['EXEMPTIONS'][0];?></td></tr>
										<tr><td>Monthly Pay Rate</td><td><?php echo $recordData['PAYRATE'][0];?></td></tr>
										<tr><td>Extra Withholding</td><td><?php echo $recordData['EXTRAWITH'][0];?></td></tr>
										</table>
										
								</td><td class="body" style="padding-top:0px">

										<table cellpadding="2" cellspacing="0" class="stub">
										<tr><td>Type</td><td><?php echo $recordData['payroll_type'][0];?></td></tr>
										<tr><td>Period</td><td><?php echo $recordData['c_period_type'][0];?></td></tr>
										<tr><td>Ending Date</td><td><?php echo $recordData['paystubs_timesheets_byPeriodEnd::c_PayPeriodEnd'][0];?></td></tr>
										<tr><td>Type of Account</td><td><?php if($recordData['CKSV'][0] == 'C'){echo 'Checking';}elseif($recordData['CKSV'][0] == 'S'){echo 'Savings';}?></td></tr>
										</table>

								</td><td class="body" style="padding-top:0px">

										<table cellpadding="2" cellspacing="0" class="stub">
										<tr><td>Tax shelter Health Premiums</td><td><?php if($recordData['POPHEALTH'][0] == 'T'){ echo 'Yes';} else { echo 'No';}?></td></tr>
										<tr><td>Tax shelter Dental Premiums</td><td><?php if($recordData['POPDENTAL'][0] == 'T'){ echo 'Yes';} else { echo 'No';}?></td></tr>
										<tr><td>Healthcare FSA Account</td><td><?php if($recordData['payroll_type'][0] == 'Supplemental'){echo '0.00';} else { echo $recordData['FSAMED'][0]; }?></td></tr>
										<tr><td>Dependent Care FSA Account</td><td><?php echo $recordData['FSADEP'][0];?></td></tr>
										<tr><td>Health Savings Account (HSA)</td><td><?php echo $recordData['HSA'][0];?></td></tr>
										</table>
										
								</td></tr>
								
						</table><hr class="ee" style="padding-bottom:0px;margin-bottom:0px">
						
			</td></tr>
			<tr><td colspan="2" style="padding-left:0px;padding-top:0px">
								
						<table cellpadding=4 cellspacing=0 width="100%" class="stub">								
								<tr><td colspan="3" style="padding-top:0px">
								
										<table cellpadding="2" cellspacing="0" class="stub" width="100%" style="padding-top:0px;margin-top:0px">
										<tr><td style="padding-top:0px">&nbsp;</td><td colspan="2" align="right" style="padding-top:0px">----------Employee----------</td><td>&nbsp;</td><td>&nbsp;</td></tr>
										<tr><td>&nbsp;</td><td align="right">Current</td><td align="right">YTD</td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
										<tr><td>Gross Wages</td><td align="right">

											<?php echo $recordData['CURGRPAY'][0];?></td><td align="right">
											<?php echo $recordData['YTDGRPAY'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
										<tr><td style="padding-top:6px">Pre-tax Deductions:</td><td align="right">
										&nbsp;</td><td align="right">
										&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
											<tr><td>&nbsp;&nbsp;Less: Premium only Health</td><td align="right">
											<?php if($recordData['CURPTHEAL'][0] > 0){echo $recordData['CURPTHEAL'][0];}elseif($recordData['CURPTPRU'][0] > 0){echo $recordData['CURPTPRU'][0];}else{echo '0.00';}?></td><td align="right">
	
											<!-- <?php if($recordData['CURPTHEAL'][0] > 0){echo $recordData['YTDPTHEAL'][0];}elseif($recordData['CURPTPRU'][0] > 0){echo $recordData['YTDPTPRU'][0];}else{echo '0.00';}?></td><td>&nbsp;</td><td>&nbsp;</td></tr> -->
											<?php echo $recordData['YTDPTHEAL'][0] + $recordData['YTDPTPRU'][0];?></td><td colspan="2" align="right">----------SEDL----------</td></tr>
											
											<tr><td>&nbsp;&nbsp;Less: Premium only Dental</td><td align="right">
											<?php echo $recordData['CURPTDENT'][0];?></td><td align="right">
											<?php echo $recordData['YTDPTDENT'][0];?></td><td align="right">Current</td><td align="right">YTD</td></tr>
											
											<tr><td>&nbsp;&nbsp;Less: Health Care Reimbursement Account (HCRA)</td><td align="right">
											<?php if($recordData['payroll_type'][0] == 'Supplemental'){echo '0.00';} else { echo $recordData['CURFSAMED'][0]; }?></td><td align="right">
											<?php echo $recordData['YTDFSAMED'][0];?></td><td align="right"><?php echo $recordData['LABFSAMED'][0];?></td><td align="right"><?php echo $recordData['LTDFSAMED'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Less: Dependent Care Reimbursement Account (DCRA)</td><td align="right">
											<?php echo $recordData['FSADEP'][0];?></td><td align="right">
											<?php echo $recordData['YTDFSADEP'][0];?></td><td align="right">&nbsp;</td><td align="right">&nbsp;</td></tr>
										
											<tr><td>&nbsp;&nbsp;Less: Health Savings Account (HSA)</td><td align="right">
											<?php echo $recordData['CURHSA'][0];?></td><td align="right">
											<?php echo $recordData['YTDHSA'][0];?></td><td align="right"><?php echo $recordData['LABHSA'][0];?></td><td align="right"><?php echo $recordData['LTDHSA'][0];?></td></tr>

										<tr><td style="padding-top:6px">Social Security Taxable Wages</td><td align="right">

											<?php echo $recordData['CURSSGROSS'][0];?></td><td align="right">
											<?php echo $recordData['YTDSSGROSS'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
										<tr><td style="padding-top:6px">Medicare Taxable Wages</td><td align="right">

											<?php echo $recordData['CURMDGROSS'][0];?></td><td align="right">
											<?php echo $recordData['YTDMDGROSS'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;Less: Retirement Contribution <span class="tiny">(Employee: 2%, SEDL: 14%)</span></td><td align="right">
											<?php echo $recordData['CURRET02'][0];?></td><td align="right">
											<?php echo $recordData['YTDRET02'][0];?></td><td colspan="2" align="center" style="white-space:normal">&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;Less: SRA</td><td align="right">
											<?php echo $recordData['CURSRA'][0];?></td><td align="right">
											<?php echo $recordData['YTDSRA'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
										<tr><td style="padding-top:6px">Federal and State (W-2) Taxable Wages</td><td align="right">

											<?php echo $recordData['CURTXGROSS'][0];?></td><td align="right">
											<?php echo $recordData['YTDTXGROSS'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
										<tr><td style="padding-top:6px">Taxes Withheld:</td><td align="right">

											&nbsp;</td><td align="right">
											&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;Federal Tax Withheld</td><td align="right">
											<?php echo $recordData['CURFWTAX'][0];?></td><td align="right">
											<?php echo $recordData['YTDFWTAX'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;Social Security Tax Withheld</td><td align="right">
											<?php echo $recordData['CURSSTAX'][0];?></td><td align="right">
											<?php echo $recordData['YTDSSTAX'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;Medicare Tax Withheld</td><td align="right">
											<?php echo $recordData['CURMDTAX'][0];?></td><td align="right">
											<?php echo $recordData['YTDMDTAX'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;State Income Tax Withheld</td><td align="right">
											<?php echo $recordData['CURSTTAX'][0];?></td><td align="right">
											<?php echo $recordData['YTDSTTAX'][0];?></td><td colspan="2" align="right">----------SEDL----------</td></tr>
										
										<tr><td style="padding-top:6px">After-tax Deductions:</td><td align="right">
	
											&nbsp;</td><td align="right">
											&nbsp;</td><td align="right">Current</td><td align="right">YTD</td></tr>
											
											<tr><td>&nbsp;&nbsp;HDHP PPO</td><td align="right">
											<?php echo $recordData['CURHEALTH'][0];?></td><td align="right">
											<?php echo $recordData['YTDHEALTH'][0];?></td><td align="right">
											<?php echo $recordData['LABHEALTH'][0];?></td><td align="right">
											<?php echo $recordData['LTDHEALTH'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;CO-PAY PPO</td><td align="right">
											<?php echo $recordData['CURPRUCARE'][0];?></td><td align="right">
											<?php echo $recordData['YTDPRUCARE'][0];?></td><td align="right">
											<?php echo $recordData['LABPRUCARE'][0];?></td><td align="right">
											<?php echo $recordData['LTDPRUCARE'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Other Insurance</td><td align="right">
											<?php echo $recordData['CURETNA'][0];?></td><td align="right">
											<?php echo $recordData['YTDETNA'][0];?></td><td align="right">
											<?php echo $recordData['LABETNA'][0];?></td><td align="right">
											<?php echo $recordData['LTDETNA'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Dental</td><td align="right">
											<?php echo $recordData['CURDENTAL'][0];?></td><td align="right">
											<?php echo $recordData['YTDDENTAL'][0];?></td><td align="right">
											<?php echo $recordData['LABDENTAL'][0];?></td><td align="right">
											<?php echo $recordData['LTDDENTAL'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Life/AD&D Insurance</td><td align="right">
											<?php echo $recordData['CURLIFE'][0];?></td><td align="right">
											<?php echo $recordData['YTDLIFE'][0];?></td><td align="right">
											<?php echo $recordData['LABLIFE'][0];?></td><td align="right">
											<?php echo $recordData['LTDLIFE'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Supplemental AD&D (PAI)</td><td align="right">
											<?php echo $recordData['CURPAI'][0];?></td><td align="right">
											<?php echo $recordData['YTDPAI'][0];?></td><td align="right">
											<?php echo $recordData['LABPAI'][0];?></td><td align="right">
											<?php echo $recordData['LTDPAI'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Disability</td><td align="right">
											<?php echo $recordData['CURDISAB'][0];?></td><td align="right">
											<?php echo $recordData['YTDDISAB'][0];?></td><td align="right">
											<?php echo $recordData['LABDISAB'][0];?></td><td align="right">
											<?php echo $recordData['LTDDISAB'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Retirement Contribution*</td><td align="right">
											<?php echo '0.00';?></td><td align="right">
											<?php echo '0.00';?></td><td align="right">
											<?php echo $recordData['LABRET14'][0];?></td><td align="right">
											<?php echo $recordData['LTDRET14'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Parking</td><td align="right">
											<?php echo $recordData['CURPARK'][0];?></td><td align="right">
											<?php echo $recordData['YTDPARK'][0];?></td><td align="right">
											<?php echo $recordData['LABPARK'][0];?></td><td align="right">
											<?php echo $recordData['LTDPARK'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Other (United Fund)</td><td align="right">
											<?php echo $recordData['CURUNFUND'][0];?></td><td align="right">
											<?php echo $recordData['YTDUNFUND'][0];?></td><td align="right">
											&nbsp;</td><td align="right">
											&nbsp;</td></tr>
										
										<tr><td style="padding-top:8px">Net Pay</td><td align="right">
										<?php echo $recordData['CURNETPAY'][0];?></td><td align="right">
										<?php echo $recordData['YTDNETPAY'][0];?></td><td align="right">
										&nbsp;</td><td align="right">
										&nbsp;</td></tr>
										

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
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
}else{ ?>


Error | <a href="menu_paystubs_admin.php?action=show_all" title="Return to SIMS Paystubs Admin screen.">Return to Paystubs Admin</a>

<?php } ?>





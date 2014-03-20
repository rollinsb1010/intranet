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
<title>SIMS - Benefits Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Benefits Admin</h1><hr /></td></tr>
			
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL <?php if($query == 'former_staff'){?>Former <?php }?>Staff Benefits Reports</strong> | <?php echo $searchResult['foundCount'];?> records found. | <?php if($query == 'former_staff'){?><a href="menu_benefits_admin.php?action=show_all">Show current staff</a><?php }else{?><a href="menu_benefits_admin.php?action=show_all&query=former_staff">Show former staff</a><?php }?></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILES-->


							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">Name</td><td class="body">Unit</td><td class="body">Title</td><td class="body">FTE</td><td class="body">Empl. Start Date</td><td class="body">Empl. Term. Date</td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body"><?php echo $searchData['staff_ID'][0];?></td><td class="body" nowrap><a href="menu_benefits_admin.php?action=show_1&staff_ID=<?php echo $searchData['staff_ID'][0];?>" target="_blank"><?php echo stripslashes($searchData['c_full_name_last_first'][0]);?></a></td><td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td><td class="body"><?php echo $searchData['job_title'][0];?></td><td class="body"><?php echo $searchData['FTE_status'][0];?></td><td class="body"><?php echo $searchData['empl_start_date'][0];?></td><td class="body" nowrap><?php if($searchData['empl_end_date'][0] == ''){echo 'Current';}else{echo $searchData['empl_end_date'][0];}?></td></tr>
								<?php } ?>

<!--END FIRST SECTION: STAFF PROFILES-->		

							
							</table>
			
			</td></tr>
			
			
			</table>

</td></tr>
</table>







</body>

</html>



<?php }elseif($action == 'show_1'){ 

$id = $_GET['staff_ID'];

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

#########################################
## END: FIND BENEFITS REPORT FOR THIS USER
#########################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


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
								<td nowrap class="body" style="padding-top:0px">Life/AD&D Insurance ***</td><td><div style="float:left;width:75px;text-align:right"><?php echo number_format((double)$recordData['c_LIFEADD'][0],2,'.',',');?>/mo.</td><td nowrap><div style="float:left;width:105px;text-align:right"><?php echo number_format((double)$recordData['c_LIFEADD'][0],2,'.',',');?> x 12 mo.</div></td><td style="text-align:right"><?php echo number_format((double)$recordData['c_ANNLIFEADD'][0],2,'.',',');?></div></td>
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




<?php }else{ ?>


Error | <a href="menu_benefits_admin.php?action=show_all" title="Return to SIMS Benefits Admin screen.">Return to Benefits Admin</a>

<?php } ?>





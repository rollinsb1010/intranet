<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2008 by SEDL
#
# Written by Eric Waters 02/25/2008
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
//exit;
##############################
## END: GRAB FORM VARIABLES ##
##############################

if($action == 'new'){
$staff_ID = $_GET['staff_ID'];
$src = $_GET['src'];

/*
################################
## START: GRAB FMP VALUELISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('SIMS_2.fp7','staff');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
##############################
## END: GRAB FMP VALUELISTS ##
##############################
*/

#####################################################################
## START: GRAB STAFF USERIDs TO POPULATE STAFF DROP-DOWN LIST ##
#####################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff','all');
$search2 -> SetDBPassword($webPW,$webUN);
//$search2 -> AddDBParam('current_employee_status','SEDL Employee');

$search2 -> AddSortParam('sims_user_ID','ascend');

$searchResult2 = $search2 -> FMFindall();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);
###################################################################
## END: GRAB STAFF USERIDs TO POPULATE SUPERVISOR DROP-DOWN LIST ##
###################################################################
/*
############################################################
## START: GRAB PBA USERIDs TO POPULATE STAFF DROP-DOWN LIST ##
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

#####################################################################
## START: GRAB AA USERIDs TO POPULATE TIME/LV ADMIN DROP-DOWN LIST ##
#####################################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','staff');
$search3 -> SetDBPassword($webPW,$webUN);
//$search3 -> AddDBParam('current_employee_status','SEDL Employee');
$search3 -> AddDBParam('is_time_leave_admin','1');

$search3 -> AddSortParam('sims_user_ID','ascend');

$searchResult3 = $search3 -> FMFind();

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//$recordData3 = current($searchResult3['data']);
###################################################################
## END: GRAB AA USERIDs TO POPULATE TIME/LV ADMIN DROP-DOWN LIST ##
###################################################################
*/

#####################################################################
## START: GET STAFF RECORD FROM SIMS ##
#####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff');
$search -> SetDBPassword($webPW,$webUN);
//$search -> AddDBParam('current_employee_status','SEDL Employee');
$search -> AddDBParam('staff_ID','=='.$staff_ID);

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);
###################################################################
## END: GET STAFF RECORD FROM SIMS ##
###################################################################

######################################
## START: DISPLAY NEW EMPLOYEE FORM ## 
######################################
?>


<html>
<head>
<title>SIMS - Planning Agreement/Performance Appraisal</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">
function checkFields() { 


	// First Name
		if (document.new_pa.staff_based_on.value ==""){
			alert("Please select an option from the drop-down list.");
			document.new_pa.staff_based_on.focus();
			return false;	}
}
</script>

</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Planning Agreement/Performance Appraisal</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong><?php echo $recordData['c_full_name_last_first'][0];?> - <?php echo $recordData['primary_SEDL_workgroup'][0];?></strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: You are creating a new planning agreement for <?php echo $recordData['c_full_name_first_last'][0];?><?php if($src != 'staff'){ ?> | <a href="menu_plan_agrmt_admin.php">Back to workgroup planning agreements</a><?php }?>
			</p></td></tr>

			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILE-->


							<table cellspacing="0" cellpadding="1" width="100%" border="1" bordercolor="#cccccc">
							

							<tr bgcolor="#e2eaa4"><td class="body" width="50%">&nbsp;Staff Member</td><td class="body" width="50%">&nbsp;Planning Agreement(s)</td></tr>
							
							<tr><td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								<tr><td valign="top">
										<table cellspacing="0" cellpadding="5" border="0" width="100%">
										<tr valign="bottom"><td align="right" nowrap><font color="666666">Name:</font></td><td width="100%"><?php echo $recordData['c_full_name_last_first'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Status:</font></td><td nowrap><?php echo $recordData['current_employee_status'][0];?></td></tr>
										<tr valign="bottom"><td align="right" nowrap valign="top"><font color="666666">Title:</font></td><td><?php echo $recordData['job_title'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Pay Grade:</font></td><td><?php echo $recordData['pay_grade'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Empl. Status:</font></td><td><?php echo $recordData['employee_type'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Unit:</font></td><td><?php echo $recordData['primary_SEDL_workgroup'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Reports to:</font></td><td><?php echo $recordData['staff_SJ_by_imm_spvsr::c_full_name_first_last'][0];?></td></tr>
										
										<tr valign="bottom"><td align="right" nowrap><font color="666666">E-mail:</font></td><td nowrap><?php echo $recordData['email'][0];?></td></tr>
		
										<tr valign="bottom"><td align="right" nowrap><font color="666666">Phone Ext:</font></td><td nowrap><?php echo $recordData['phone_ext'][0];?></td></tr>

										<tr valign="bottom"><td align="right" nowrap><font color="666666">Work Hrs/Ste#:</font></td><td nowrap><?php echo $recordData['staff_work_hours'][0];?> / <?php echo $recordData['suite_number'][0];?></td></tr>
		
		
										<tr valign="bottom"><td align="right" nowrap><font color="666666" nowrap>Empl. Dates:</font></td><td nowrap><?php echo $recordData['empl_start_date'][0];?> to <?php if($recordData['empl_end_date'][0] != ''){ echo $recordData['empl_end_date'][0]; }else{ echo 'Current';}?></td></tr>
										
										</table>

								</td><td>
								
										<table cellspacing="0" cellpadding="5" border="0" width="100%">
										<tr valign="bottom"><td><img src="http://www.sedl.org/images/people/<?php echo $recordData['sims_user_ID'][0];?>.jpg"><p>
											<a href="http://www.sedl.org/staff/personnel/staffprofiles.cgi?intranetonly=yes&showuserid=<?php echo $recordData['sims_user_ID'][0];?>" target="_blank">Private profile</a><br>
											<a href="http://www.sedl.org/pubs/catalog/authors/<?php echo $recordData['sims_user_ID'][0];?>.html" target="_blank">Public profile</a></td></tr>

										</table>
								
								
								</td></tr>
								</table>
							
							</td>

<!--END FIRST SECTION: STAFF PROFILE-->		

<!--BEGIN SECOND SECTION: planning agreement INFORMATION-->

							<td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">


								<tr valign="middle"><td colspan="2"><h2>New planning agreement</h2>
								<form action="sims_new_pa_admin.php" name="new_pa" id="new_pa" onsubmit="return checkFields()">
								<input type="hidden" name="staff_ID" value="<?php echo $staff_ID;?>">
								<input type="hidden" name="target" value="plan_agrmt">
								<font color="#666666">You have chosen to create a new planning agreement (PA) for SEDL staff member - <?php echo $recordData['c_full_name_first_last'][0];?> - from the 
								profile information on the left. If you would like to base <?php echo $recordData['name_first'][0];?>'s new planning agreement on another staff member's planning agreement, select the appropriate staff member and click the "Create planning agreement" button.<p>
								Base the new PA on the most recent PA of: </font>
								
								<select name="staff_based_on" class="body">
								<option value="">
								<option value="">-----
								<option value="new">NEW
								
								<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
								<option value="<?php echo $searchData2['sims_user_ID'][0];?>"> <?php echo $searchData2['sims_user_ID'][0]; ?>
								<?php } ?>
								</select>
								</td></tr>
								<tr><td colspan="2" align="right"><input type="button" value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Create planning agreement"></td></tr>
								</form>
								


								<tr><td colspan="2">
								<p style="padding:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb"><span class="tiny"><strong>NOTE</strong>: If <?php echo $recordData['name_first'][0];?>'s new planning agreement will not be based on any other staff member's most recent planning agreement, select <strong>NEW</strong> from the drop-down list. 
								If it will be based on <?php echo $recordData['name_first'][0];?>'s own last planning agreement, select "<?php echo $recordData['sims_user_ID'][0];?>" from the drop-down list.</span></p></td></tr>
								
								</table>
								
							</td></tr>
							
<!--END SECOND SECTION: planning agreement INFORMATION-->



						
							
							
							
							</table>

	

			</form>
			
			
			
			
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>







</body>

</html>


<?php
####################################
## END: DISPLAY NEW EMPLOYEE FORM ## 
####################################
 
} elseif ($action == 'show_all') {

$confirm_update = $_GET['confirm_update'];
$confirm_new = $_GET['confirm_new'];
$query = $_GET['query'];

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
//$recordData = current($searchResult['data']);
#####################################
## END: GRAB CURRENT STAFF RECORDS ##
#####################################


###################################
## START: DISPLAY ALL STAFF LIST ##
###################################
 
 ?>

<html>
<head>
<title>SIMS - Staff Profiles</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Staff Profiles</h1><hr /></td></tr>
			
			<?php if($confirm_update == '1'){ ?>

			<tr><td colspan="2"><p class="alert_small">Record successfully updated.</p></td></tr>
			
			<?php $confirm_update = '0';
			} ?>

			<?php if($confirm_new == '1'){ ?>

			<tr><td colspan="2"><p class="alert_small">New record successfully created.</p></td></tr>
			
			<?php $confirm_new = '0';
			} ?>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL Staff Profiles</strong> | <?php echo $searchResult['foundCount'];?> records found. | <a href="staff_profiles.php?action=show_all&query=former_staff">Show former staff</a></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="staff_profiles.php?action=new">New Profile</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILES-->


							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td><td class="body">Name</td><td class="body">Unit</td><td class="body">Title</td><td class="body">FTE</td><td class="body">Start Date</td><td class="body">Last Updated</td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body"><?php echo $searchData['staff_ID'][0];?></td><td class="body" nowrap><a href="staff_profiles.php?action=show_1&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo stripslashes($searchData['c_full_name_last_first'][0]);?></a></td><td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td><td class="body"><?php echo $searchData['job_title'][0];?></td><td class="body"><?php echo $searchData['FTE_status'][0];?></td><td class="body"><?php echo $searchData['empl_start_date'][0];?></td><td class="body" nowrap><?php echo $searchData['last_mod_timestamp'][0];?></td></tr>
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
 
#################################
## END: DISPLAY ALL STAFF LIST ##
#################################

} elseif ($action == 'show_1') { 

$plan_agrmt_ID = $_GET['plan_agrmt_ID'];

#################################
## START: FIND EMPLOYEE RECORD ##
#################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','planning_agreements');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('staff_ID','=='.$_GET['staff_ID']);
$search4 -> AddDBParam('RecordID','=='.$plan_agrmt_ID);

//$search4 -> AddSortParam('sims_user_ID','ascend');

$searchResult4 = $search4 -> FMFind();

//echo '<p>errorCode: '.$searchResult4['errorCode'];
//echo '<p>foundCount: '.$searchResult4['foundCount'];
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
<title>SIMS - Planning Agreement/Performance Appraisal</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Planning Agreement/Performance Appraisal</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong><?php echo stripslashes($recordData4['c.FullName'][0]);?> - <?php echo $recordData4['c.SEDL_Unit'][0];?></strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: This planning agreement was last updated: <?php echo $recordData4['last_mod_timestamp'][0];?> | <a href="staff_plan_agrmt_admin.php?action=show_mine&staff_ID=<?php echo $recordData4['staff_ID'][0];?>">Back to PA list for <?php echo stripslashes($recordData4['c.FullName'][0]);?></a></p></td></tr>

			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: POSITION INFORMATION-->


							<table cellspacing="0" cellpadding="1" width="100%" border="1" bordercolor="#cccccc">
							

							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;SIMS Document ID: <?php echo $recordData4['RecordID'][0];?></td></tr>
							
							<tr><td class="body" valign="top" width="100%">
							

										<table cellspacing="0" cellpadding="5" border="0" width="100%">

											<tr><td><strong>NAME/UNIT:</strong> <?php echo stripslashes($recordData4['c.FullName'][0]);?> | <?php echo stripslashes($recordData4['c.SEDL_Unit'][0]);?></td><td align="right"><strong>PERFORMANCE PERIOD:</strong> <?php echo $recordData4['Performance_Period'][0];?></td></tr>
											<tr><td><strong>POSITION:</strong> <?php echo $recordData4['c.Position'][0];?></td><td align="right"><strong>PLANNING DATE:</strong> <?php echo $recordData4['Planning_Date'][0];?></td></tr>

											<tr><td colspan="2"><hr>SAMPLE PAGE 1:<br></td></tr>
											
												<table width="100%" cellpadding="6" border=1 class="sims">
												<tr><td><strong>Task#</strong></td><td><strong>PD#</strong></td><td><strong>Task Description</strong></td><td><strong>Performance Expectations</strong></td><td><strong>Accom</strong></td><td><strong>Status</strong></td></tr>
												<tr valign="top"><td><?php echo $recordData4['TaskNumberPg1_html'][0];?></td><td><?php echo $recordData4['PosDescrPg1_html'][0];?></td><td><?php echo $recordData4['Task_DescrPg1_html'][0];?></td><td><?php echo $recordData4['PerfExpPg1_html'][0];?></td><td><?php echo $recordData4['AccomodationsNeededPg1'][0];?></td><td><?php echo $recordData4['TaskStatusPg1'][0];?></td></tr>
												</table>





										</table>


							
							</td></tr>

<!--END FIRST SECTION: POSITION INFORMATION-->		


							
							
							
							
							</table>

	

			
			
			
			
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
 
} elseif ($action == 'show_mine') { 

$staff_ID = $_GET['staff_ID'];
#################################
## START: FIND EMPLOYEE RECORD ##
#################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','staff');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('staff_ID','=='.$staff_ID);
//$search4 -> AddDBParam('staff_ID','==180');

//$search4 -> AddSortParam('c_date_prepared','descend');

$searchResult4 = $search4 -> FMFind();

//echo $searchResult4['errorCode'];
//echo $searchResult4['foundCount'];
$recordData4 = current($searchResult4['data']);
###############################
## END: FIND EMPLOYEE RECORD ##
###############################

#################################
## START: FIND EMPLOYEE PAs ##
#################################
$search5 = new FX($serverIP,$webCompanionPort);
$search5-> SetDBData('SIMS_2.fp7','planning_agreements');
$search5 -> SetDBPassword($webPW,$webUN);
$search5 -> AddDBParam('staff_ID','=='.$staff_ID);
$search5 -> AddDBParam('view_status','active');
//$search5 -> AddDBParam('staff_ID','==180');

$search5 -> AddSortParam('creation_timestamp','descend');

$searchResult5 = $search5 -> FMFind();

//echo $searchResult5['errorCode'];
//echo $searchResult5['foundCount'];
//$recordData5 = current($searchResult5['data']);
###############################
## END: FIND EMPLOYEE PAs ##
###############################

####################################
## START: DISPLAY EMPLOYEE RECORD ## 
####################################
$ip = $_SERVER['REMOTE_ADDR'];

?>


<html>
<head>
<title>SIMS - Planning Agreements/Performance Appraisals</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">
<?php //echo $ip;?>
<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Performance Appraisals</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong><?php echo $recordData4['c_full_name_last_first'][0];?> - <?php echo $recordData4['primary_SEDL_workgroup'][0];?></strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">You are viewing performance appraisal documents for <?php echo $recordData4['c_full_name_first_last'][0];?> | <a href="menu_plan_agrmt_admin.php">Back to workgroup performance appraisals</a></span>
			</p></td></tr>

			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILE-->
<?php if($_SESSION['new_pa_created'] == '1'){ ?>
<p class="alert_small">A new planning agreement for <?php echo $recordData4['c_full_name_first_last'][0];?> was successfully created. Click the link for this PA to edit and/or print a copy.</p>
<?php $_SESSION['new_pa_created'] = ''; } ?>

<?php if($_SESSION['new_pa_created'] == '2'){ ?>
<p class="alert_small">There was a problem creating a new planning agreement for <?php echo $recordData4['c_full_name_first_last'][0];?>. Contact <a href="mailto:sims@sedl.org">sims@sedl.org</a> for assistance (ErrorCode: <?php echo $_SESSION['new_pa_error_code'];?>).</p>
<?php $_SESSION['new_pa_created'] = ''; } ?>

							<table cellspacing="0" cellpadding="1" width="100%" border="1" bordercolor="#cccccc">
							

							<tr bgcolor="#e2eaa4"><td class="body" width="50%">&nbsp;Staff Member</td><td class="body" width="50%">&nbsp;Performance Appraisal(s)</td></tr>
							
							<tr><td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								<tr><td valign="top">
										<table cellspacing="0" cellpadding="5" border="0" width="100%">
										<tr valign="bottom"><td align="right" nowrap><font color="666666">Name:</font></td><td width="100%"><?php echo $recordData4['c_full_name_last_first'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Status:</font></td><td nowrap><?php echo $recordData4['current_employee_status'][0];?></td></tr>
										<tr valign="bottom"><td align="right" nowrap valign="top"><font color="666666">Title:</font></td><td><?php echo $recordData4['job_title'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Pay Grade:</font></td><td><?php echo $recordData4['pay_grade'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Empl. Status:</font></td><td><?php echo $recordData4['employee_type'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Unit:</font></td><td><?php echo $recordData4['primary_SEDL_workgroup'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Reports to:</font></td><td><?php echo $recordData4['staff_SJ_by_imm_spvsr::c_full_name_first_last'][0];?></td></tr>
										
										<tr valign="bottom"><td align="right" nowrap><font color="666666">E-mail:</font></td><td nowrap><?php echo $recordData4['email'][0];?></td></tr>
		
										<tr valign="bottom"><td align="right" nowrap><font color="666666">Phone Ext:</font></td><td nowrap><?php echo $recordData4['phone_ext'][0];?></td></tr>

										<tr valign="bottom"><td align="right" nowrap><font color="666666">Work Hrs/Ste#:</font></td><td nowrap><?php echo $recordData4['staff_work_hours'][0];?> / <?php echo $recordData4['suite_number'][0];?></td></tr>
		
		
										<tr valign="bottom"><td align="right" nowrap><font color="666666" nowrap>Empl. Dates:</font></td><td nowrap><?php echo $recordData4['empl_start_date'][0];?> to <?php if($recordData4['empl_end_date'][0] != ''){ echo $recordData4['empl_end_date'][0]; }else{ echo 'Current';}?></td></tr>
										
										</table>

								</td><td>
								
										<table cellspacing="0" cellpadding="5" border="0" width="100%">
										<tr valign="bottom"><td><img src="http://www.sedl.org/images/people/<?php echo $recordData4['sims_user_ID'][0];?>.jpg"><p>
											<a href="http://www.sedl.org/staff/personnel/staffprofiles.cgi?intranetonly=yes&showuserid=<?php echo $recordData4['sims_user_ID'][0];?>" target="_blank">Private profile</a><br>
											<a href="http://www.sedl.org/pubs/catalog/authors/<?php echo $recordData4['sims_user_ID'][0];?>.html" target="_blank">Public profile</a></td></tr>

										</table>
								
								
								</td></tr>
								</table>
							
							</td>

<!--END FIRST SECTION: STAFF PROFILE-->		

<!--BEGIN SECOND SECTION: planning agreement INFORMATION-->

							<td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">


								<tr valign="middle"><td colspan="2"><font color="#666666">The following are live links to <?php echo $recordData4['name_first'][0];?>'s performance appraisal documents. Click the performance period to view, edit, sign, and/or print a formal copy 
								for your records.<p>
								<strong>FYI</strong>: The following staff have read/write access to <?php echo $recordData4['name_first'][0];?>'s performance appraisal documents: <strong><?php echo $recordData4['c_plan_agrmt_access_list'][0];?></strong></font><p>
								<p style="padding:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb"><span class="tiny"><strong>NOTE</strong>: To access these documents, you must have a copy of Filemaker Pro 8+ installed on your computer and
								your computer must be connected to SEDL's network or have a live VPN connection to SEDL's network.</span></p></td></tr>


								<tr valign="top"><td align="right" nowrap><font color="666666" nowrap>Forms(s):</font></td><td>

										<table class="sims" cellpadding="4">

										<tr bgcolor="#ebebeb" nowrap><td>Performance Period</td><td align="center" nowrap>Last Updated</td></tr>
		


											<?php if($searchResult5['foundCount'] == '0'){?>
													<tr><td colspan="2" align="center">No records found.</td></tr>
											<?php }else{?>

										<?php foreach($searchResult5['data'] as $key => $searchData5) { ?>

												<tr><td nowrap><a href="sims_temp_launcher_admin.php?object_ID=<?php echo $searchData5['RecordID'][0];?>&target=plan_agrmt" target="_blank"><?php echo $searchData5['Performance_Period'][0];?></a></td><td nowrap><?php echo $searchData5['last_mod_timestamp'][0];?></td></tr>
											<?php } ?>

										<?php } ?>		

										</table>
										

								</td></tr>

								<tr><td colspan="2">
								<p style="padding:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff">
								<font color="#666666"><span class="tiny"><strong>CREATING A DUPLICATE PLANNING AGREEMENT:</strong></span><br>In addition to viewing the existing performance appraisals above, you can also create a new planning agreement for <?php echo $recordData4['name_first'][0];?>. To create a new planning agreement, open the most recent planning agreement listed above, then click "Create Duplicate Document" from the File menu.
								<br>&nbsp;<br>
								<span class="tiny"><strong>CREATING A NEW BLANK PLANNING AGREEMENT:</strong></span><br>
								If there are no existing planning agreements or to create a new blank planning agreement for <?php echo $recordData4['name_first'][0];?> <a href="sims_new_pa_admin.php?staff_ID=<?php echo $recordData4['staff_ID'][0];?>">click here</a>. Then view or edit the newly created PA by clicking the new link above.</font></p>
											</td></tr>
								
								<tr><td colspan="2">

								<?php if(strpos($recordData4['c_pos_descr_access_list'][0],$_SESSION['user_ID']) !== false){?>
								<p style="padding:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">You can also view <a href="staff_pos_descr_admin.php?action=show_mine&staff_ID=<?php echo $recordData4['staff_ID'][0];?>">position descriptions</a> for <?php echo $recordData4['name_first'][0];?>.</p>
								<?php }?></font>
								
								<span class="tiny">*If you have trouble opening these documents, contact <a href="mailto:sims@sedl.org">sims@sedl.org</a></span></td></tr>
								
								</table>
								
							</td></tr>
							
<!--END SECOND SECTION: planning agreement INFORMATION-->



						
							
							
							
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
$first_name = $_GET['first_name'];
$middle_initial = $_GET['middle_initial'];
$last_name = $_GET['last_name'];
$status = $_GET['status'];
$title = $_GET['title'];
$sedl_unit = $_GET['sedl_unit'];
$email = $_GET['email'];
$phone = $_GET['phone'];
$ext = $_GET['ext'];
$work_hrs = $_GET['work_hrs'];
$ste_num = $_GET['ste_num'];
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];
$birthmonth = $_GET['birthmonth'];
$birthday = $_GET['birthday'];
$on_mgmt_council = $_GET['on_mgmt_council'];
$timesheet_name = $_GET['timesheet_name'];
$empl_type = $_GET['empl_type'];
$fte = $_GET['fte'];
$imm_spvsr = $_GET['imm_spvsr'];
$pba = $_GET['pba'];
$time_leave_admin = $_GET['time_leave_admin'];
$is_bgt_auth = $_GET['is_bgt_auth'];
$is_supervisor = $_GET['is_supervisor'];
$is_auth_rep = $_GET['is_auth_rep'];
$allow_variable_timesheet_hours = $_GET['allow_variable_timesheet_hours'];
$sims_user_ID = $_GET['sims_user_ID'];
$sims_access_main_menu = $_GET['sims_access_main_menu'];
$sims_access_time_leave = $_GET['sims_access_time_leave'];
$sims_access_supervisors = $_GET['sims_access_supervisors'];
$sims_access_budget_authorities = $_GET['sims_access_budget_authorities'];
$sims_access_admin_serv = $_GET['sims_access_admin_serv'];
$sims_access_travel_forms = $_GET['sims_access_travel_forms'];
$sims_access_planning_agrmts = $_GET['sims_access_planning_agrmts'];
$sims_access_position_descr = $_GET['sims_access_position_descr'];
$sims_access_staff_profiles = $_GET['sims_access_staff_profiles'];

$trigger = rand();
#####################################
## END: GRAB UPDATE FORM VARIABLES ##
#####################################

##################################
## START: UPDATE THE FMP RECORD ##
##################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','staff');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row_ID);

$update -> AddDBParam('name_first',$first_name);
$update -> AddDBParam('name_middle',$middle_initial);
$update -> AddDBParam('name_last',$last_name);
$update -> AddDBParam('current_employee_status',$status);
$update -> AddDBParam('job_title',$title);
$update -> AddDBParam('primary_SEDL_workgroup',$sedl_unit);
$update -> AddDBParam('email',$email);
$update -> AddDBParam('phone_full',$phone);
$update -> AddDBParam('phone_ext',$ext);
$update -> AddDBParam('staff_work_hours',$work_hrs);
$update -> AddDBParam('suite_number',$ste_num);
$update -> AddDBParam('empl_start_date',$start_date);
$update -> AddDBParam('empl_end_date',$end_date);
$update -> AddDBParam('birthmonth',$birthmonth);
$update -> AddDBParam('birthday',$birthday);
$update -> AddDBParam('on_mgmt_council',$on_mgmt_council);
$update -> AddDBParam('name_timesheet',$timesheet_name);
$update -> AddDBParam('employee_type',$empl_type);
$update -> AddDBParam('FTE_status',$fte);
$update -> AddDBParam('immediate_supervisor_sims_user_ID',$imm_spvsr);
$update -> AddDBParam('bgt_auth_primary_sims_user_ID',$pba);
$update -> AddDBParam('time_leave_admin_sims_user_ID',$time_leave_admin);
$update -> AddDBParam('is_budget_authority',$is_bgt_auth);
$update -> AddDBParam('is_supervisor',$is_supervisor);
$update -> AddDBParam('is_time_leave_admin',$is_auth_rep);
$update -> AddDBParam('allow_variable_timesheet_hrs',$allow_variable_timesheet_hours);
$update -> AddDBParam('sims_user_ID',$sims_user_ID);
$update -> AddDBParam('cwp_sims_access_main_menu',$sims_access_main_menu);
$update -> AddDBParam('cwp_sims_access_time_leave',$sims_access_time_leave);
$update -> AddDBParam('cwp_sims_access_spvsr',$sims_access_supervisors);
$update -> AddDBParam('cwp_sims_access_bgt_auth',$sims_access_budget_authorities);
$update -> AddDBParam('cwp_sims_access_ofts_admin',$sims_access_admin_serv);
$update -> AddDBParam('cwp_sims_access_travel_request',$sims_access_travel_forms);
$update -> AddDBParam('cwp_sims_access_plan_agrmt',$sims_access_planning_agrmts);
$update -> AddDBParam('cwp_sims_access_pos_descr',$sims_access_position_descr);
$update -> AddDBParam('cwp_sims_access_staff_profiles',$sims_access_staff_profiles);
$update -> AddDBParam('last_updated_by',$_SESSION['user_ID']);
$update -> AddDBParam('profile_info_last_mod_timestamp_trigger',$trigger);

$updateResult = $update -> FMEdit();

//echo $updateResult['errorCode'];
if($updateResult['errorCode'] == '0'){
			$confirm_update = '1';
			
			$updaterecordData = current($updateResult['data']);
			
			
			//$phone = $updaterecordData['phone_full'][0];
			//$birthmonth = $updaterecordData['birthmonth'][0];
			//$birthday = $updaterecordData['birthday'][0];
			//$mgmtcouncil = $updaterecordData['on_mgmt_council'][0];
			$lastupdated = $updaterecordData['last_mod_date'][0];
			$lastupdated_by = $updaterecordData['last_updated_by'][0];
			$stafflistsorting = $updaterecordData['stafflistsorting'][0];
			$intranet_pwd = $updaterecordData['sims_pwd'][0];
			$photo_permissions = $updaterecordData['photo_permissions'][0];
			$start_date_mysql = $updaterecordData['c_empl_start_date_mysql'][0];
			
			################################
			## END: UPDATE THE FMP RECORD ##
			################################
			
			##################################################
			## START: UPDATE THE MYSQL staff_profiles TABLE ##
			##################################################
			
			// CONNECT TO mySQL
			
			$db = mysql_connect('localhost','intranetuser','limited');
			
			if(!$db) {
				die('Not connected : '. mysql_error());
			} else {
			//echo 'Connected to: mysql';	
			}
			
			// CONNECT TO staff_profiles database
			
			$db_selected = mysql_select_db('intranet',$db);
			
			if(!$db_selected) {
			echo 'no connection';
				die('Can\'t use intranet : ' . mysql_error());
			} else {
			//echo 'Connected to: mysql database intranet';
			}
			
			
			$command = 
			
			"update staff_profiles 
			
			SET 
			firstname = '$first_name', 
			middleinitial = '$middle_initial', 
			lastname='$last_name', 
			jobtitle='$title', 
			phone='$phone', 
			userid='$sims_user_ID', 
			email='$email', 
			phoneext='$ext', 
			birthmonth='$birthmonth', 
			birthday='$birthday', 
			timesheetname='$timesheet_name', 
			department_abbrev='$sedl_unit', 
			mgmtcouncil='$on_mgmt_council', 
			lastupdated='$lastupdated', 
			lastupdated_by='$lastupdated_by', 
			room_number='$ste_num', 
			start_date='$start_date_mysql', 
			stafflistsorting='$stafflistsorting', 
			supervised_by='$imm_spvsr', 
			photo_permissions='$photo_permissions'
			
			where fm_record_id like '$update_row_ID'";
			
			$update = mysql_query($command);
			
			if (!$update) {
			   die('Invalid query: ' . mysql_error());
			}else{
			
			//$num_results = mysql_num_rows($result);
			
			//echo '<br>Update Successful!';
			}
			
			//exit;
			
			
			
			if($status == 'Former Employee'){ // IF THE EMPLOYEE STATUS WAS CHANGED TO FORMER EMPLOYEE, DELETE THE RECORD FROM MYSQL
			
					// CONNECT TO mySQL
					
					$db = mysql_connect('localhost','intranetuser','limited');
					
					if(!$db) {
						die('Not connected : '. mysql_error());
					} else {
					//echo 'Connected to: mysql';	
					}
					
					// CONNECT TO staff_profiles database
					
					$db_selected = mysql_select_db('intranet',$db);
					
					if(!$db_selected) {
					echo 'no connection';
						die('Can\'t use intranet : ' . mysql_error());
					} else {
					//echo 'Connected to: mysql database intranet';
					}
					
					$delete_user_id = $updaterecordData['sims_user_ID'][0];
					
					$command = 
					
					"delete from staff_profiles 
					
					where userid = '$delete_user_id'";
					
					$update = mysql_query($command);
					
					if (!$update) {
					   die('Invalid query: ' . mysql_error());
					}else{
					
					//$num_results = mysql_num_rows($result);
					
					//echo '<br>Update Successful!';
					}
					
					//exit;
			}
					
} else {


			echo 'There was an error updating the record.';


			exit;
}







##################################################
## END: UPDATE THE MYSQL staff_profiles TABLE ##
##################################################

#################################################################################################
## START: DISPLAY THE STAFF PROFILE RECORDS IN LIST VIEW
#################################################################################################
header('Location: http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&confirm_update=1');
exit;

#################################################################################################
## END: DISPLAY THE STAFF PROFILE RECORDS IN LIST VIEW
#################################################################################################



} elseif ($action == 'update_emerg_info') {

#######################################
## START: GRAB UPDATE FORM VARIABLES ##
#######################################
$update_row_ID = $_GET['update_row_ID'];
$emerg_contact_prim_name = $_GET['emerg_contact_prim_name'];
$emerg_contact_prim_relation = $_GET['emerg_contact_prim_relation'];
$emerg_contact_prim_phone_hm = $_GET['emerg_contact_prim_phone_hm'];
$emerg_contact_prim_phone_wk = $_GET['emerg_contact_prim_phone_wk'];
$emerg_contact_prim_phone_mbl = $_GET['emerg_contact_prim_phone_mbl'];

$emerg_contact_alt_name = $_GET['emerg_contact_alt_name'];
$emerg_contact_alt_relation = $_GET['emerg_contact_alt_relation'];
$emerg_contact_alt_phone_hm = $_GET['emerg_contact_alt_phone_hm'];
$emerg_contact_alt_phone_wk = $_GET['emerg_contact_alt_phone_wk'];
$emerg_contact_alt_phone_mbl = $_GET['emerg_contact_alt_phone_mbl'];
$no_changes_needed = $_GET['no_changes'];
#####################################
## END: GRAB UPDATE FORM VARIABLES ##
#####################################

##################################
## START: UPDATE THE FMP RECORD ##
##################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','staff');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row_ID);

$trigger = rand();

if($no_changes_needed != 'No Changes Needed'){

	$update -> AddDBParam('emerg_contact_prim_name',$emerg_contact_prim_name);
	$update -> AddDBParam('emerg_contact_prim_relation',$emerg_contact_prim_relation);
	$update -> AddDBParam('emerg_contact_prim_phone_hm',$emerg_contact_prim_phone_hm);
	$update -> AddDBParam('emerg_contact_prim_phone_wk',$emerg_contact_prim_phone_wk);
	$update -> AddDBParam('emerg_contact_prim_phone_mbl',$emerg_contact_prim_phone_mbl);
	$update -> AddDBParam('emerg_contact_alt_name',$emerg_contact_alt_name);
	$update -> AddDBParam('emerg_contact_alt_relation',$emerg_contact_alt_relation);
	$update -> AddDBParam('emerg_contact_alt_phone_hm',$emerg_contact_alt_phone_hm);
	$update -> AddDBParam('emerg_contact_alt_phone_wk',$emerg_contact_alt_phone_wk);
	$update -> AddDBParam('emerg_contact_alt_phone_mbl',$emerg_contact_alt_phone_mbl);
	$update -> AddDBParam('emerg_contact_info_last_mod_timestamp_trigger',$trigger);
	$update -> AddDBParam('emerg_contact_info_last_reviewed_timestamp_trigger',$trigger);
	
	$notify_admin_serv = '1';

} else {

	$update -> AddDBParam('emerg_contact_info_last_reviewed_timestamp_trigger',$trigger);

}

$updateResult = $update -> FMEdit();

$updatedrecordData = current($updateResult['data']);
//echo $updateResult['errorCode'];
if($updateResult['errorCode'] == '0'){
$_SESSION['confirm_update'] = '1';

// ADD SQL UPDATE CODE HERE TO UPDATE 'staff_profiles' mySQL DATABASE - IS THIS NECESSARY OR CAN THIS INFORMATION BE KEPT IN FILEMAKER ONLY?

}
################################
## END: UPDATE THE FMP RECORD ##
################################

#################################################################################
## START: NOTIFY ADMIN SERVICES WHEN SOMEONE UPDATES THEIR EMERG. CONTACT INFO ##
#################################################################################
if($notify_admin_serv == '1'){

	$to = 'sue.liberty@sedl.org';
	$subject = 'Emergency contact information has been updated by '.$updatedrecordData['c_full_name_first_last'][0];
	$message = 
	
	'Emergency contact information has been updated for SEDL staff member: '.$updatedrecordData['c_full_name_first_last'][0].'.'."\n\n".
	
	'----------'."\n\n".
	
	'To review and/or print this information, click here: '."\n".
	'http://www.sedl.org/staff/sims/staff_profiles.php?action=show_1&staff_ID='.$updatedrecordData['staff_ID'][0]."\n\n".
	
	
	'------------------------------------------------------------------------------------------------------------------'."\n".
	
	'This is an auto-generated message from the SEDL Information Management System (SIMS)';
	
	$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: SIMS-2@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
	
	mail($to, $subject, $message, $headers);

}
###############################################################################
## END: NOTIFY ADMIN SERVICES WHEN SOMEONE UPDATES THEIR EMERG. CONTACT INFO ##
###############################################################################

#####################################
## START: RETURN TO SIMS MAIN MENU ##
#####################################
header('Location: http://www.sedl.org/staff/sims/sims_menu.php');
exit;
###################################
## END: RETURN TO SIMS MAIN MENU ##
###################################
 
 ?>

 
 
 
 <?php } elseif ($action == 'new_submit') {

################################
## START: GRAB FORM VARIABLES ##
################################
$update_row_ID = $_GET['update_row_ID'];
$first_name = $_GET['first_name'];
$middle_initial = $_GET['middle_initial'];
$last_name = $_GET['last_name'];
$status = $_GET['status'];
$title = $_GET['title'];
$sedl_unit = $_GET['sedl_unit'];
$birthmonth = $_GET['birthmonth'];
$birthday = $_GET['birthday'];
$on_mgmt_council = $_GET['on_mgmt_council'];
$email = $_GET['email'];
$phone = $_GET['phone'];
$ext = $_GET['ext'];
$work_hrs = $_GET['work_hrs'];
$ste_num = $_GET['ste_num'];
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];
$timesheet_name = $_GET['timesheet_name'];
$empl_type = $_GET['empl_type'];
$fte = $_GET['fte'];
$imm_spvsr = $_GET['imm_spvsr'];
$pba = $_GET['pba'];
$time_leave_admin = $_GET['time_leave_admin'];
$is_bgt_auth = $_GET['is_bgt_auth'];
$is_supervisor = $_GET['is_supervisor'];
$is_auth_rep = $_GET['is_auth_rep'];
$allow_variable_timesheet_hours = $_GET['allow_variable_timesheet_hours'];
$sims_user_ID = $_GET['sims_user_ID'];
$sims_access_main_menu = $_GET['sims_access_main_menu'];
$sims_access_time_leave = $_GET['sims_access_time_leave'];
$sims_access_supervisors = $_GET['sims_access_supervisors'];
$sims_access_budget_authorities = $_GET['sims_access_budget_authorities'];
$sims_access_admin_serv = $_GET['sims_access_admin_serv'];
$sims_access_travel_forms = $_GET['sims_access_travel_forms'];
$sims_access_planning_agrmts = $_GET['sims_access_planning_agrmts'];
$sims_access_position_descr = $_GET['sims_access_position_descr'];
$sims_access_staff_profiles = $_GET['sims_access_staff_profiles'];
$responsibilities = $_GET['responsibilities'];
$education = $_GET['education'];
##############################
## END: GRAB FORM VARIABLES ##
##############################

##################################
## START: CREATE THE FMP RECORD ##
##################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','staff');
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('name_first',$first_name);
$newrecord -> AddDBParam('name_middle',$middle_initial);
$newrecord -> AddDBParam('name_last',$last_name);
$newrecord -> AddDBParam('current_employee_status',$status);
$newrecord -> AddDBParam('job_title',$title);
$newrecord -> AddDBParam('primary_SEDL_workgroup',$sedl_unit);
$newrecord -> AddDBParam('birthmonth',$birthmonth);
$newrecord -> AddDBParam('birthday',$birthday);
$newrecord -> AddDBParam('on_mgmt_council',$on_mgmt_council);
$newrecord -> AddDBParam('email',$email);
$newrecord -> AddDBParam('phone_full',$phone);
$newrecord -> AddDBParam('phone_ext',$ext);
$newrecord -> AddDBParam('staff_work_hours',$work_hrs);
$newrecord -> AddDBParam('suite_number',$ste_num);
$newrecord -> AddDBParam('empl_start_date',$start_date);
$newrecord -> AddDBParam('empl_end_date',$end_date);
$newrecord -> AddDBParam('name_timesheet',$timesheet_name);
$newrecord -> AddDBParam('employee_type',$empl_type);
$newrecord -> AddDBParam('FTE_status',$fte);
$newrecord -> AddDBParam('immediate_supervisor_sims_user_ID',$imm_spvsr);
$newrecord -> AddDBParam('bgt_auth_primary_sims_user_ID',$pba);
$newrecord -> AddDBParam('time_leave_admin_sims_user_ID',$time_leave_admin);
$newrecord -> AddDBParam('is_budget_authority',$is_bgt_auth);
$newrecord -> AddDBParam('is_supervisor',$is_supervisor);
$newrecord -> AddDBParam('is_time_leave_admin',$is_auth_rep);
$newrecord -> AddDBParam('allow_variable_timesheet_hrs',$allow_variable_timesheet_hours);
$newrecord -> AddDBParam('sims_user_ID',$sims_user_ID);
$newrecord -> AddDBParam('cwp_sims_access_main_menu',$sims_access_main_menu);
$newrecord -> AddDBParam('cwp_sims_access_time_leave',$sims_access_time_leave);
$newrecord -> AddDBParam('cwp_sims_access_spvsr',$sims_access_supervisors);
$newrecord -> AddDBParam('cwp_sims_access_bgt_auth',$sims_access_budget_authorities);
$newrecord -> AddDBParam('cwp_sims_access_ofts_admin',$sims_access_admin_serv);
$newrecord -> AddDBParam('cwp_sims_access_travel_request',$sims_access_travel_forms);
$newrecord -> AddDBParam('cwp_sims_access_plan_agrmt',$sims_access_planning_agrmts);
$newrecord -> AddDBParam('cwp_sims_access_pos_descr',$sims_access_position_descr);
$newrecord -> AddDBParam('cwp_sims_access_staff_profiles',$sims_access_staff_profiles);
$newrecord -> AddDBParam('last_updated_by',$_SESSION['user_ID']);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
if($newrecordResult['errorCode'] == '0'){
$confirm_new = '1';
$newrecordData = current($newrecordResult['data']);

$fm_record_id = $newrecordData['c_cwp_row_ID'][0];
$last_updated = $newrecordData['last_mod_date'][0];
$last_updated_by = $newrecordData['last_updated_by'][0];
$start_date_mysql = $newrecordData['c_empl_start_date_mysql'][0];

// ADD SQL UPDATE CODE HERE TO INSERT THE NEW RECORD INTO THE 'staff_profiles' mySQL DATABASE
// INSERT INTO staff_profiles (LastName, FirstName) VALUES ('Rasmussen', 'George')

// CONNECT TO mySQL

$db = mysql_connect('localhost','intranetuser','limited');

if(!$db) {
	die('Not connected : '. mysql_error());
} else {
//echo 'Connected to: mysql';	
}

// CONNECT TO staff_profiles database

$db_selected = mysql_select_db('intranet',$db);

if(!$db_selected) {
echo 'no connection';
	die('Can\'t use intranet : ' . mysql_error());
} else {
//echo 'Connected to: mysql database intranet';
}

$strong_pwd = crypt('password');

$command = 

"INSERT INTO staff_profiles 

(fm_record_id, firstname, middleinitial, lastname, jobtitle, phone, userid, email, phoneext, birthmonth, birthday, timesheetname, department_abbrev, mgmtcouncil, lastupdated, lastupdated_by, room_number, start_date, supervised_by, intranet_pwd, photo_permissions, strong_pwd)

VALUES

('$fm_record_id', '$first_name', '$middle_initial', '$last_name', '$title', '$phone', '$sims_user_ID', '$email', '$ext', '$birthmonth', '$birthday', '$timesheet_name', '$sedl_unit', '$on_mgmt_council', '$last_updated', '$last_updated_by', '$ste_num', '$start_date_mysql', '$imm_spvsr', 'password', 'Needed', '$strong_pwd')";




$update = mysql_query($command);

if (!$update) {
   die('Invalid query: ' . mysql_error());
}else{

//$num_results = mysql_num_rows($result);

//echo '<br>Update Successful!';
}

//exit;


}
################################
## END: CREATE THE FMP RECORD ##
################################

#################################################################################################
## START: DISPLAY THE STAFF PROFILE RECORDS IN LIST VIEW
#################################################################################################
header('Location: http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&confirm_new=1');
exit;

#################################################################################################
## END: DISPLAY THE STAFF PROFILE RECORDS IN LIST VIEW
#################################################################################################

 
 
 } elseif ($action == 'emerg_contact_info_update') { 

$staff_ID = $_GET['staff_ID'];

#################################
## START: FIND EMPLOYEE RECORD ##
#################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','staff','all');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('staff_ID','=='.$staff_ID);

//$search4 -> AddSortParam('sims_user_ID','ascend');

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
<title>SIMS - Staff Profiles</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Staff Profiles</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong><?php echo $recordData4['c_full_name_last_first'][0];?> - <?php echo $recordData4['primary_SEDL_workgroup'][0];?></strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="staff_profiles.php?action=show_all">Show All</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: Any changes made to this record must be saved by clicking the Update Profile button. | Last updated: <?php echo $recordData4['last_mod_timestamp'][0];?></p></td></tr>

			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILE-->


							<table cellspacing="0" cellpadding="1" width="100%" border="1" bordercolor="#cccccc">
							
							<form name="new_employee">
							<input type="hidden" name="action" value="update">
							<input type="hidden" name="update_row_ID" value="<?php echo $recordData4['c_cwp_row_ID'][0];?>">

							<tr bgcolor="#e2eaa4"><td class="body" colspan="2">&nbsp;Emergency Contact Information <font color="#666666">| <em>Last reviewed: <?php echo $recordData4['emerg_contact_info_last_reviewed_timestamp'][0];?></em> | <em>Last updated: <?php echo $recordData4['emerg_contact_info_last_mod_timestamp'][0];?></em></font></td></tr>
							
							<tr><td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								
								<tr valign="top"><td align="right" nowrap><font color="666666">Primary Contact:</font></td><td nowrap width="100%">
								
								<input type="text" size="25" name="emerg_contact_prim_name" value="<?php echo $recordData4['emerg_contact_prim_name'][0];?>"><font color="666666"> NAME</font><br>
								<input type="text" size="25" name="emerg_contact_prim_relation" value="<?php echo $recordData4['emerg_contact_prim_relation'][0];?>"><font color="666666"> RELATION</font>
								
								
								</td></tr>


								<tr valign="top"><td align="right"><font color="666666" nowrap>Phone(s):</font></td><td>
								
								<input type="text" size="25" name="emerg_contact_prim_phone_hm" value="<?php echo $recordData4['emerg_contact_prim_phone_hm'][0];?>"><font color="666666"> HOME</font><br>
								<input type="text" size="25" name="emerg_contact_prim_phone_wk" value="<?php echo $recordData4['emerg_contact_prim_phone_wk'][0];?>"><font color="666666"> WORK</font><br>
								<input type="text" size="25" name="emerg_contact_prim_phone_mbl" value="<?php echo $recordData4['emerg_contact_prim_phone_mbl'][0];?>"><font color="666666"> MOBILE</font>

								
								</td></tr>



								</table>
							
							</td><td width="50%" valign="top">

								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								
								<tr valign="top"><td align="right" nowrap><font color="666666">Alternate Contact:</font></td><td nowrap width="100%">
								
								<input type="text" size="25" name="emerg_contact_alt_name" value="<?php echo $recordData4['emerg_contact_alt_name'][0];?>"><font color="666666"> NAME</font><br>
								<input type="text" size="25" name="emerg_contact_alt_relation" value="<?php echo $recordData4['emerg_contact_alt_relation'][0];?>"><font color="666666"> RELATION</font>
								
								
								</td></tr>


								<tr valign="top"><td align="right"><font color="666666" nowrap>Phone(s):</font></td><td>
								
								<input type="text" size="25" name="emerg_contact_alt_phone_hm" value="<?php echo $recordData4['emerg_contact_alt_phone_hm'][0];?>"><font color="666666"> HOME</font><br>
								<input type="text" size="25" name="emerg_contact_alt_phone_wk" value="<?php echo $recordData4['emerg_contact_alt_phone_wk'][0];?>"><font color="666666"> WORK</font><br>
								<input type="text" size="25" name="emerg_contact_alt_phone_mbl" value="<?php echo $recordData4['emerg_contact_alt_phone_mbl'][0];?>"><font color="666666"> MOBILE</font>

								
								</td></tr>



								</table>
							
							</td></tr>

<!--END FIRST SECTION: STAFF PROFILE-->		

							

							

	

							<tr><td class="body" colspan="2">
							<center><input type="submit" name="submit" value="Update Profile"></center>
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
 
 
 } elseif ($action == 'cp') { 

$staff_ID = $_GET['staff_ID'];


###############################
## START: GRAB FORM VARIABLES
###############################
$new_pw = $_POST['new_pw'];
$old_pw = $_POST['old_pw'];
$update_row_ID = $_POST['update_row_ID'];
###############################
## END: GRAB FORM VARIABLES
###############################

if($update == 'yes'){
############################################################################
## START: UPDATE PASSWORD FOR THIS STAFF MEMBER IF INDICATED
############################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','staff');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row_ID);
$update -> AddDBParam('sims_pwd',$new_pw);

$updateResult = $update -> FMEdit();
##########################################################################
## END: UPDATE PASSWORD FOR THIS STAFF MEMBER IF INDICATED
##########################################################################
if($updateResult['errorCode'] =='0'){
$_SESSION['sims_pw_updated'] = '1';
header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
exit;
}

}

############################################################################
## START: FIND DATA FOR THIS STAFF MEMBER
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
## END: FIND DATA FOR THIS STAFF MEMBER
############################################################################

#################################################################################################
## START: DISPLAY THE PW MOD SCREEN FOR THIS STAFF MEMBER
#################################################################################################
?>


<html>
<head>
<title>SIMS - Preferences</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="300" cellpadding="0" cellspacing="0" border="1" bordercolor="#003745" align="center">
<tr bgcolor="#003745"><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
			
			
			<tr bgcolor="#a2c7ca"><td class="body" nowrap><strong>Change SIMS password: <?php echo $searchData2['c_full_name_first_last'][0];?></strong></td></tr>
			
			
			
			<tr><td class="body" colspan=2>
			<form name="timesheet_prefs">
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="update_row_ID" value="<?php echo $searchData2['c_cwp_row_ID'][0];?>">
			
			<input type="checkbox" name="timesheet_prefs_show_nicknames" value="Yes" <?php if($searchData2['timesheet_prefs_show_nicknames'][0] == 'Yes'){echo 'CHECKED=CHECKED';}?>">	Show nicknames on timesheet	<br>					
			<input type="checkbox" name="timesheet_prefs_hide_weekends" value="Yes" <?php if($searchData2['timesheet_prefs_hide_weekends'][0] == 'Yes'){echo 'CHECKED=CHECKED';}?>">	Hide weekends on timesheet						


			<p>
			<input type="button" value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Update Preferences">
			</form>
		

			
			</td></tr>
			
			
			<tr><td class="body" colspan="2">
			</td></tr>
			
			
			</table>

</td></tr>
</table>






</body>

</html>
<?php
#################################################################################################
## END: DISPLAY THE TIMESHEET PREFERENCES FOR THIS STAFF MEMBER
#################################################################################################



 } else {
 
 echo 'Error2';
 
 }
 ?>
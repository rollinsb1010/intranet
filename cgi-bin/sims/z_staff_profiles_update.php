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

#####################################################################
## START: GRAB STAFF USERIDs TO POPULATE SUPERVISOR DROP-DOWN LIST ##
#####################################################################
$array_allstaff = file("../personnel/sedlstaff-array.txt", FILE_IGNORE_NEW_LINES);

#$search = new FX($serverIP,$webCompanionPort);
#$search -> SetDBData('SIMS_2.fp7','staff','all');
#$search -> SetDBPassword($webPW,$webUN);
#$search -> AddDBParam('current_employee_status','SEDL Employee');

#$search -> AddSortParam('sims_user_ID','ascend');

#$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
###################################################################
## END: GRAB STAFF USERIDs TO POPULATE SUPERVISOR DROP-DOWN LIST ##
###################################################################

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

######################################
## START: DISPLAY NEW EMPLOYEE FORM ## 
######################################
?>


<html>
<head>
<title>SIMS - Staff Profiles</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">


<script language="JavaScript">
function checkFields() { 


	// First Name
		if (document.new_employee.first_name.value ==""){
			alert("Field missing: First Name");
			document.new_employee.first_name.focus();
			return false;	}

	// Last Name
		if (document.new_employee.last_name.value ==""){
			alert("Field missing: Last Name");
			document.new_employee.last_name.focus();
			return false;	}

	// Status
		if (document.new_employee.status.value ==""){
			alert("Field missing: Status");
			document.new_employee.status.focus();
			return false;	}

	// Unit
		if (document.new_employee.sedl_unit.value ==""){
			alert("Field missing: Unit");
			document.new_employee.sedl_unit.focus();
			return false;	}

	// Title
		if (document.new_employee.title.value ==""){
			alert("Field missing: title");
			document.new_employee.title.focus();
			return false;	}

	// E-mail
		if (document.new_employee.email.value ==""){
			alert("Field missing: E-mail");
			document.new_employee.email.focus();
			return false;	}


	// Employment Start Date
		if (document.new_employee.start_date.value ==""){
			alert("Field missing: Employment Start Date");
			document.new_employee.start_date.focus();
			return false;	}

	// Timesheet Name
		if (document.new_employee.timesheet_name.value ==""){
			alert("Field missing: Timesheet Name");
			document.new_employee.timesheet_name.focus();
			return false;	}

	// Employee Type
		if (document.new_employee.empl_type.value ==""){
			alert("Field missing: Employee Type");
			document.new_employee.empl_type.focus();
			return false;	}

	// Employee FTE
		if (document.new_employee.fte.value ==""){
			alert("Field missing: Employee FTE status");
			document.new_employee.fte.focus();
			return false;	}

	// Supervisor
		if (document.new_employee.imm_spvsr.value ==""){
			alert("Field missing: Supervisor");
			document.new_employee.imm_spvsr.focus();
			return false;	}

	// PBA
		if (document.new_employee.pba.value ==""){
			alert("Field missing: Primary Budget Authority");
			document.new_employee.pba.focus();
			return false;	}

	// SIMS ID
		if (document.new_employee.sims_user_ID.value ==""){
			alert("Field missing: SIMS ID");
			document.new_employee.sims_user_ID.focus();
			return false;	}


}




// -->
</script>


</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Staff Profiles</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>New Staff Profile</strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="staff_profiles.php?action=show_all">Show All</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: Please enter all fields completely.</p></td></tr>
			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILE-->


							<table cellspacing="0" cellpadding="1" border="1" bordercolor="cccccc" width="100%" class="body">
							
							<form name="new_employee" onsubmit="return checkFields()">
							<input type="hidden" name="action" value="new_submit">

							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;Profile</td><td class="body">&nbsp;Timesheet Information</td></tr>
							
							<tr><td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								
								<tr valign="bottom"><td align="right"><font color="666666">Name:</font></td><td>
								
									<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td><font color="666666"><span class="tiny">First</span></font><br><input type="text" name="first_name" size="12"></td>
										<td><font color="666666"><span class="tiny">MI</span></font><br><input type="text" name="middle_initial" size="2"></td>
										<td><font color="666666"><span class="tiny">Last</span></font><br><input type="text" name="last_name" size="16"></td></tr>
									</table>
								
								</td></tr>
								<tr valign="middle"><td align="right"><font color="666666">Status/Unit:</font></td><td colspan="4">
								
								
									<select name="status" class="body">
									<option value="">
									
									<?php foreach($v1Result['valueLists']['employee_status'] as $key => $value) { ?>
									<option value="<?php echo $value;?>"> <?php echo $value; ?>
									<?php } ?>
									</select>

									<select name="sedl_unit" class="body">
									<option value="">
									
									<?php foreach($v1Result['valueLists']['sedl_workgroups'] as $key => $value) { ?>
									<option value="<?php echo $value;?>"> <?php echo $value; ?>
									<?php } ?>
									</select>
					
								
								</td></tr>

								<tr valign="middle"><td align="right"><font color="666666">Other Unit(s):</font></td><td colspan="4">

									<select name="other_SEDL_workgroup" class="body">
									<option value="">
									
									<?php foreach($v1Result['valueLists']['sedl_other_workgroups'] as $key => $value) { ?>
									<option value="<?php echo $value;?>"> <?php echo $value; ?>
									<?php } ?>
									</select>
					
								
								</td></tr>

								<tr valign="bottom"><td align="right"><font color="666666">Title:</font></td><td><input type="text" name="title" size="30"></td></tr>
								
								
								<tr valign="bottom"><td align="right"><font color="666666">Phone/Ext:</font></td><td><input type="text" name="phone" size="20"><input type="text" name="ext" size="5"></td></tr>

								<tr valign="bottom"><td align="right"><font color="666666">E-mail:</font></td><td><input type="text" name="email" size="30"></td></tr>


								<tr valign="bottom"><td align="right"><font color="666666">Work Hrs:</font></td><td><input type="text" name="work_hrs" size="15"> Suite#: <input type="text" name="ste_num" size="5"></td></tr>


								<tr valign="bottom"><td align="right"><font color="666666" nowrap>Empl. Dates:</font></td><td>
								
								
									<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td><font color="666666"><span class="tiny">Start (MM/DD/YYYY)</span></font><br><input type="text" name="start_date" size="16"></td>
										<td><font color="666666"><span class="tiny">End (MM/DD/YYYY)</span></font><br><input type="text" name="end_date" size="16"></td>
									</tr>
									</table>

								
								</td></tr>

								<tr valign="middle"><td align="right"><font color="666666">Birthday:</font></td><td>

									
									<select name="birthmonth" class="body">
									<option value="">
									
									<?php foreach($v1Result['valueLists']['birthmonth'] as $key => $value) { ?>
									<option value="<?php echo $value;?>"> <?php echo $value; ?>
									<?php } ?>
									</select>
									
									<select name="birthday" class="body">
									<option value="">
									
									<?php foreach($v1Result['valueLists']['birthday'] as $key => $value) { ?>
									<option value="<?php echo $value;?>"> <?php echo $value; ?>
									<?php } ?>
									</select>
								
									<input type="checkbox" name="on_mgmt_council" value="Yes"> On Mgmt. Council

								
								</td></tr>


								</table>
							
							</td>

<!--END FIRST SECTION: STAFF PROFILE-->		

<!--BEGIN SECOND SECTION: TIMESHEET INFORMATION-->

							<td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">

								<tr valign="bottom"><td align="right"><font color="666666" nowrap>Timesheet Name:</font></td><td><input type="text" name="timesheet_name" size="30"></td></tr>

								<tr valign="middle"><td align="right"><font color="666666">Empl. Type/FTEs:</font></td><td>
								
								
									<table cellpadding="0" cellspacing="0" border="0">
									<tr><td>
										
											<select name="empl_type" class="body">
											<option value="">
											
											<?php foreach($v1Result['valueLists']['employee_type'] as $key => $value) { ?>
											<option value="<?php echo $value;?>"> <?php echo $value; ?>
											<?php } ?>
											</select>

											<select name="fte" class="body">
											<option value="">
											
											<?php foreach($v1Result['valueLists']['fte_status'] as $key => $value) { ?>
											<option value="<?php echo $value;?>"> <?php echo $value; ?>
											<?php } ?>
											</select>
									</td></tr>
									</table>
					
								
								</td></tr>
								<tr valign="middle"><td align="right"><font color="666666">Supervisor:</font></td><td>
								
								
									<select name="imm_spvsr" class="body">
									<option value="">
									
									<?php foreach($array_allstaff as $staffmember) { ?>
									<option value="<?php echo $staffmember;?>"> <?php echo $staffmember;?>
									<?php } ?>
									</select>
								
								
								
								</td></tr>

								<tr valign="middle"><td align="right"><font color="666666">Primary Bgt. Auth.:</font></td><td>
								
								
									<select name="pba" class="body">
									<option value="">
									
									<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
									<option value="<?php echo $searchData2['sims_user_ID'][0];?>"> <?php echo $searchData2['sims_user_ID'][0];?>
									<?php } ?>
									</select>
								
								
								
								</td></tr>

								<tr valign="middle"><td align="right"><font color="666666">Chief Prog. Officer:</font></td><td>
								
								
									<select name="cpo" class="body">
									<option value="">
									
									<option value="vdimock"> vdimock</option>
									</select>
								
								
								
								</td></tr>

								<tr valign="middle"><td align="right"><font color="666666">Time/Lv. Admin:</font></td><td>
								
								
									<select name="time_leave_admin" class="body">
									<option value="">
									
									<?php foreach($searchResult3['data'] as $key => $searchData3) { ?>
									<option value="<?php echo $searchData3['sims_user_ID'][0];?>"> <?php echo $searchData3['sims_user_ID'][0];?>
									<?php } ?>
									</select>
								
								
								
								</td></tr>
								
								<tr valign="middle"><td align="right"><font color="666666">cc on Lv. Approval:</font></td><td>
								
								
									<select name="lv_appr_cc" class="body">
									<option value="">
									
									<?php foreach($array_allstaff as $staffmember) { ?>
									<option value="<?php echo $staffmember;?>"> <?php echo $staffmember;?>
									<?php } ?>
									</select>
								
								
								
								</td></tr>


								<tr valign="top"><td align="right"><font color="666666" nowrap>Roles:</font></td><td>

								<input type="checkbox" name="is_bgt_auth" value="Yes"> Is a Budget Authority<br>
								<input type="checkbox" name="is_supervisor" value="1"> Is a Supervisor<br>
								<input type="checkbox" name="is_auth_rep" value="1"> Is a Time/Leave Admin<br>
								
								</td></tr>

								<tr valign="top"><td align="right"><font color="666666" nowrap>Options:</font></td><td>

								<input type="checkbox" name="allow_variable_timesheet_hours" value="Yes"> Allow variable timesheet hours<br>
								
								</td></tr>
								
								</table>
								
							</td></tr>
							
<!--END SECOND SECTION: TIMESHEET INFORMATION-->

<!--BEGIN THIRD SECTION: SIMS/INTRANET PREFERENCES-->
							
							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;SIMS/Intranet Preferences</td><td class="body">&nbsp;Responsibilities / Experience / Education</td></tr>
	
							<tr><td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								
								<tr valign="bottom"><td align="right"><font color="666666">SIMS ID (max=8):</font></td><td><input type="text" name="sims_user_ID" size="12" maxlength="8"> 
								<input type="checkbox" name="sims_access_main_menu" value="Yes"> SIMS access</td></tr>


								<tr valign="top"><td align="right"><font color="666666" nowrap>Permissions:</font></td><td>

								<input type="checkbox" name="sims_access_time_leave" value="Yes"> Time & Leave<br>
								<input type="checkbox" name="sims_access_supervisors" value="Yes"> Supervisor approvals<br>
								<input type="checkbox" name="sims_access_budget_authorities" value="Yes"> Budget authority approvals<br>
								<input type="checkbox" name="sims_access_planning_agrmts" value="Yes"> Planning agreements<br>
								<input type="checkbox" name="sims_access_position_descr" value="Yes"> Position descriptions<br>
								
								
								</td></tr>
								
								
								<tr valign="middle"><td align="right"><font color="666666">Pos. Descr. Admin:</font></td><td>
								
								
									<select name="pos_descr_admin" class="body">
									<option value="">
									
									<?php foreach($array_allstaff as $staffmember) { ?>
									<option value="<?php echo $staffmember;?>"> <?php echo $staffmember;?>
									<?php } ?>
									</select>
								
								</td></tr>

								<tr valign="bottom"><td align="right"><font color="666666">PD Accessible by:</font></td><td><input type="text" name="pos_descr_admin_other" size="25"></td></tr> 

								<tr valign="middle"><td align="right"><font color="666666">Plan. Agrmt. Admin:</font></td><td>
								
								
									<select name="plan_agrmt_admin" class="body">
									<option value="">
									
									<?php foreach($array_allstaff as $staffmember) { ?>
									<option value="<?php echo $staffmember;?>"> <?php echo $staffmember;?>
									<?php } ?>
									</select>
								
								</td></tr>

								<tr valign="bottom"><td align="right"><font color="666666">PA Accessible by:</font></td><td><input type="text" name="plan_agrmt_admin_other" size="25"></td></tr> 


								</table>
							
							</td>

<!--END THIRD SECTION: SIMS/INTRANET PREFERENCES-->		

<!--BEGIN FOURTH SECTION: RESPONSIBILITIES/EXPERIENCE/EDUCATION-->

							<td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">

								<tr valign="bottom"><td align="right"><td>
								<font color="666666"><span class="tiny">CURRENT RESPONSIBILITIES (HTML OK)</span></font><br><textarea name="responsibilities" rows="5" cols="46"></textarea><p>
								<font color="666666"><span class="tiny">EXPERIENCE / EDUCATION (and past positions at SEDL)</span></font><br><textarea name="education" rows="5" cols="46"></textarea>
								
								
								
								</td></tr>

								
								</table>
								
							</td></tr>
							
<!--END FOURTH SECTION: RESPONSIBILITIES/EXPERIENCE/EDUCATION-->



	

							<tr><td class="body" colspan="2">
							<center><input type="submit" name="submit" value="Create Profile"></center>
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
####################################
## END: DISPLAY NEW EMPLOYEE FORM ## 
####################################
 
} elseif ($action == 'show_all') {

$confirm_update = $_GET['confirm_update'];
$confirm_new = $_GET['confirm_new'];
$query = $_GET['query'];
$sortby = $_GET['sortby'];

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

if ($sortby == 'end_date') {
	$search -> AddSortParam('empl_end_date','descend');
} elseif ($sortby == 'end_date2') {
	$search -> AddSortParam('empl_end_date','ascend');
} elseif ($sortby == 'start_date') {
	$search -> AddSortParam('empl_start_date','ascend');
} elseif ($sortby == 'start_date2') {
	$search -> AddSortParam('empl_start_date','descend');
} elseif ($sortby == 'last_name2') {
	$search -> AddSortParam('c_full_name_last_first','descend');
} else {
	$search -> AddSortParam('c_full_name_last_first','ascend');
	$sortby = "last_name";
}
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
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL Staff Profiles</strong> | <?php echo $searchResult['foundCount'];?> records found. | 
			<?php
			if ($query == 'former_staff') {
				echo "<a href=\"staff_profiles.php?action=show_all\">Show current staff</a>";
			} else {
				echo "<a href=\"staff_profiles.php?action=show_all&query=former_staff\">Show former staff</a>";
			}
			?>
			</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="staff_profiles.php?action=new">New Profile</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILES-->


							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td>
								    <?php
								    if($query == 'former_staff') {
								    	if ($sortby == 'last_name') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;query=former_staff&amp;sortby=last_name2\"><img src=\"/images/up.gif\" alt=\"sorted ascending\" style=\"float:right;\" border=\"0\">Name</a></td>";
										} else if ($sortby == 'last_name2') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;query=former_staff&amp;sortby=last_name\"><img src=\"/images/down.gif\" alt=\"sorted descending\" style=\"float:right;\" border=\"0\">Name</a></td>";
										} else {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;query=former_staff&amp;sortby=last_name\">Name</a></td>";
										}
								    } else {
								    	if ($sortby == 'last_name') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;sortby=last_name2\"><img src=\"/images/up.gif\" alt=\"sorted ascending\" style=\"float:right;\" border=\"0\">Name</a></td>";
										} else if ($sortby == 'last_name2') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;sortby=last_name\"><img src=\"/images/down.gif\" alt=\"sorted descending\" style=\"float:right;\" border=\"0\">Name</a></td>";
										} else {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;sortby=last_name\">Name</a></td>";
										}
								    }
								    ?>

									
									<td class="body">Unit</td><td class="body">Title</td><td class="body">FTE</td>
								    <?php
								    if($query == 'former_staff') {
								    	if ($sortby == 'start_date') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;query=former_staff&amp;sortby=start_date2\"><img src=\"/images/up.gif\" alt=\"sorted ascending\" style=\"float:right;\" border=\"0\">Start Date</a></td>";
										} else if ($sortby == 'start_date2') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;query=former_staff&amp;sortby=start_date\"><img src=\"/images/down.gif\" alt=\"sorted descending\" style=\"float:right;\" border=\"0\">Start Date</a></td>";
										} else {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;query=former_staff&amp;sortby=start_date\">Start Date</a></td>";
										}
								    } else {
								    	if ($sortby == 'start_date') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;sortby=start_date2\"><img src=\"/images/up.gif\" alt=\"sorted ascending\" style=\"float:right;\" border=\"0\">Start Date</a></td>";
										} else if ($sortby == 'start_date2') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;sortby=start_date\"><img src=\"/images/down.gif\" alt=\"sorted descending\" style=\"float:right;\" border=\"0\">Start Date</a></td>";
										} else {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;sortby=start_date\">Start Date</a></td>";
										}
								    }
								    ?>
								    <?php
								    if($query == 'former_staff') {
								    	if ($sortby == 'end_date') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;query=former_staff&amp;sortby=end_date2\"><img src=\"/images/down.gif\" alt=\"sorted ascending\" style=\"float:right;\" border=\"0\">End Date</td>";
										} else if ($sortby == 'end_date2') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;query=former_staff&amp;sortby=end_date\"><img src=\"/images/up.gif\" alt=\"sorted descending\" style=\"float:right;\" border=\"0\">End Date</td>";
										} else {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles.php?action=show_all&amp;query=former_staff&amp;sortby=end_date\">End Date</a></td>";
										}
								    }
								    ?>
							   <td class="body">Last Updated</td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body"><?php echo $searchData['staff_ID'][0];?></td>
								    <td class="body" nowrap><a href="staff_profiles.php?action=show_1&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo stripslashes($searchData['c_full_name_last_first'][0]);?></a></td>
								    <td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td>
								    <td class="body"><?php echo $searchData['job_title'][0];?></td>
								    <td class="body"><?php echo $searchData['FTE_status'][0];?></td>
								    <td class="body"><?php echo $searchData['empl_start_date'][0];?></td>
								    <?php
								    if($query == 'former_staff') {
								    	echo "<td class=body>";
								    	echo $searchData['empl_end_date'][0];
								    	echo "</td>";
								    }
								    ?>
								    <td class="body" nowrap><?php echo $searchData['last_mod_timestamp'][0];?></td></tr>
								<?php 
								## PUT CODE HERE TO UPDATE MYSQL

	$immediate_supervisor_sims_user_ID = $searchData['immediate_supervisor_sims_user_ID'][0]; //  for use when sending to mysql
	$bgt_auth_primary_sims_user_ID = $searchData['bgt_auth_primary_sims_user_ID'][0]; //  for use when sending to mysql
	$time_leave_admin_sims_user_ID = $searchData['time_leave_admin_sims_user_ID'][0]; //  for use when sending to mysql
	$sims_user_ID = $searchData['sims_user_ID'][0];
								
								######################################################
								## START: UPDATE TWO FIELDS IN MYSQL
								######################################################
								// QUERY MySQL FOR THIS USER ID
								$db = mysql_connect('localhost','intranetuser','limited');
								$db_selected = mysql_select_db('intranet',$db);
								$command_update_user = "update staff_profiles SET immediate_supervisor_sims_user_ID = '$immediate_supervisor_sims_user_ID', bgt_auth_primary_sims_user_ID = '$bgt_auth_primary_sims_user_ID', time_leave_admin_sims_user_ID = '$time_leave_admin_sims_user_ID' where userid like '$sims_user_ID'";
								$result = mysql_query("$command_update_user");
								echo "<tr><td colspan=\"7\">$command_update_user ...</td></tr>";

		
								}
								
								
								
								
								
								?>

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

$staff_ID = $_GET['staff_ID'];
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

#####################################################################
## START: GRAB STAFF USERIDs TO POPULATE SUPERVISOR DROP-DOWN LIST ##
#####################################################################
$array_allstaff = file("../personnel/sedlstaff-array.txt", FILE_IGNORE_NEW_LINES);

#$search = new FX($serverIP,$webCompanionPort);
#$search -> SetDBData('SIMS_2.fp7','staff','all');
#$search -> SetDBPassword($webPW,$webUN);
#$search -> AddDBParam('current_employee_status','SEDL Employee');

#$search -> AddSortParam('sims_user_ID','ascend');

#$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
###################################################################
## END: GRAB STAFF USERIDs TO POPULATE SUPERVISOR DROP-DOWN LIST ##
###################################################################

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
$_SESSION['current_status'] = $recordData4['current_employee_status'][0];
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

<script language="JavaScript">
function checkFields() { 


	// First Name
		if (document.new_employee.first_name.value ==""){
			alert("Field missing: First Name");
			document.new_employee.first_name.focus();
			return false;	}

	// Last Name
		if (document.new_employee.last_name.value ==""){
			alert("Field missing: Last Name");
			document.new_employee.last_name.focus();
			return false;	}

	// Status
		if (document.new_employee.status.value ==""){
			alert("Field missing: Status");
			document.new_employee.status.focus();
			return false;	}

	// Unit
		if (document.new_employee.sedl_unit.value ==""){
			alert("Field missing: Unit");
			document.new_employee.sedl_unit.focus();
			return false;	}

	// Title
		if (document.new_employee.title.value ==""){
			alert("Field missing: title");
			document.new_employee.title.focus();
			return false;	}


	// E-mail
		if (document.new_employee.email.value ==""){
			alert("Field missing: E-mail");
			document.new_employee.email.focus();
			return false;	}


	// Employment Start Date
		if (document.new_employee.start_date.value ==""){
			alert("Field missing: Employment Start Date");
			document.new_employee.start_date.focus();
			return false;	}

	// Timesheet Name
		if (document.new_employee.timesheet_name.value ==""){
			alert("Field missing: Timesheet Name");
			document.new_employee.timesheet_name.focus();
			return false;	}

	// Employee Type
		if (document.new_employee.empl_type.value ==""){
			alert("Field missing: Employee Type");
			document.new_employee.empl_type.focus();
			return false;	}

	// Employee FTE
		if (document.new_employee.fte.value ==""){
			alert("Field missing: Employee FTE status");
			document.new_employee.fte.focus();
			return false;	}

	// Supervisor
		if (document.new_employee.imm_spvsr.value ==""){
			alert("Field missing: Supervisor");
			document.new_employee.imm_spvsr.focus();
			return false;	}

	// PBA
		if (document.new_employee.pba.value ==""){
			alert("Field missing: Primary Budget Authority");
			document.new_employee.pba.focus();
			return false;	}

	// SIMS ID
		if (document.new_employee.sims_user_ID.value ==""){
			alert("Field missing: SIMS ID");
			document.new_employee.sims_user_ID.focus();
			return false;	}


}




// -->
</script>



</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Staff Profiles</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong><?php echo stripslashes($recordData4['c_full_name_last_first'][0]);?> - <?php echo $recordData4['primary_SEDL_workgroup'][0];?></strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="staff_profiles.php?action=show_all">Show All</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: Any changes made to this record must be saved by clicking the Update Profile button. | Last updated: <?php echo $recordData4['last_mod_timestamp'][0];?></p></td></tr>

			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILE-->


							<table cellspacing="0" cellpadding="1" width="100%" border="1" bordercolor="#cccccc">
							
							<form name="new_employee" onsubmit="return checkFields()">
							<input type="hidden" name="action" value="update">
							<input type="hidden" name="update_row_ID" value="<?php echo $recordData4['c_cwp_row_ID'][0];?>">

							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;Profile</td><td class="body">&nbsp;Timesheet Information</td></tr>
							
							<tr><td class="body" valign="top" width="50%">
							

											<table cellspacing="0" cellpadding="5" border="0" width="100%">
											<tr valign="bottom"><td align="right"><font color="666666">Name:</font></td><td>
											
												<table cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td><font color="666666"><span class="tiny">First</span></font><br><input type="text" name="first_name" size="12" value="<?php echo stripslashes($recordData4['name_first'][0]);?>"></td>
													<td><font color="666666"><span class="tiny">MI</span></font><br><input type="text" name="middle_initial" size="2" value="<?php echo $recordData4['name_middle'][0];?>"></td>
													<td><font color="666666"><span class="tiny">Last</span></font><br><input type="text" name="last_name" size="16" value="<?php echo stripslashes($recordData4['name_last'][0]);?>"></td></tr>
												</table>
											
											</td></tr>
											<tr valign="middle"><td align="right"><font color="666666">Status/Unit:</font></td><td colspan="4">
											
										
												<select name="status" class="body">
												<option value="">
												
												<?php foreach($v1Result['valueLists']['employee_status'] as $key => $value) { ?>
												<option value="<?php echo $value;?>" <?php if($recordData4['current_employee_status'][0] == $value){echo 'SELECTED';}?>> <?php echo $value; ?>
												<?php } ?>
												</select>

												<select name="sedl_unit" class="body">
												<option value="">
												
												<?php foreach($v1Result['valueLists']['sedl_workgroups'] as $key => $value) { ?>
												<option value="<?php echo $value;?>" <?php if($recordData4['primary_SEDL_workgroup'][0] == $value){echo 'SELECTED';}?>> <?php echo $value; ?>
												<?php } ?>
												</select>
								
											
											</td></tr>
											
											<tr valign="middle"><td align="right" style="vertical-align:top"><font color="666666">Other Project(s):</font></td><td colspan="4">
			
												<?php foreach($v1Result['valueLists']['sedl_other_workgroups'] as $key => $value) { ?>
												<input type="checkbox" name="other_SEDL_workgroup[]" value="<?php echo $value; ?>" <?php if (strpos($recordData4['other_SEDL_workgroup'][0],$value) !== false) { echo 'checked';}?>>
												<?php echo $value; ?>&nbsp;
												<?php } ?>
								
											
											</td></tr>
			
											<tr valign="bottom"><td align="right"><font color="666666">Title:</font></td><td><input type="text" name="title" size="30" value="<?php echo $recordData4['job_title'][0];?>"></td></tr>
											
											<tr valign="bottom"><td align="right" nowrap><font color="666666">Phone:</font></td><td><input type="text" name="phone" size="20" value="<?php echo $recordData4['phone_full'][0];?>"> Ext.:<input type="text" name="ext" size="5" value="<?php echo $recordData4['phone_ext'][0];?>"></td></tr>
			
											<tr valign="bottom"><td align="right"><font color="666666">E-mail:</font></td><td><input type="text" name="email" size="20" value="<?php echo $recordData4['email'][0];?>"></td></tr>


											<tr valign="bottom"><td align="right" nowrap><font color="666666">Work Hrs:</font></td><td><input type="text" name="work_hrs" size="15" value="<?php echo $recordData4['staff_work_hours'][0];?>"> Suite#: <input type="text" name="ste_num" size="5" value="<?php echo $recordData4['suite_number'][0];?>"></td></tr>
			
			
											<tr valign="bottom"><td align="right" nowrap><font color="666666" nowrap>Empl. Dates:</font></td><td>
											
											
												<table cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td><font color="666666"><span class="tiny">Start (MM/DD/YYYY)</span></font><br><input type="text" name="start_date" size="16" value="<?php echo $recordData4['empl_start_date'][0];?>"></td>
													<td><font color="666666"><span class="tiny">End (MM/DD/YYYY)</span></font><br><input type="text" name="end_date" size="16" value="<?php echo $recordData4['empl_end_date'][0];?>"></td>
												</tr>
												</table>
			
											
											</td></tr>
											
											
											<tr valign="middle"><td align="right"><font color="666666">Birthday:</font></td><td>

									
											<select name="birthmonth" class="body">
											<option value="">
											
											<?php foreach($v1Result['valueLists']['birthmonth'] as $key => $value) { ?>
											<option value="<?php echo $value;?>" <?php if($recordData4['birthmonth'][0] == $value){echo 'SELECTED';}?>> <?php echo $value; ?>
											<?php } ?>
											</select>
											
											<select name="birthday" class="body">
											<option value="">
											
											<?php foreach($v1Result['valueLists']['birthday'] as $key => $value) { ?>
											<option value="<?php echo $value;?>" <?php if($recordData4['birthday'][0] == $value){echo 'SELECTED';}?>> <?php echo $value; ?>
											<?php } ?>
											</select>
										
											<input type="checkbox" name="on_mgmt_council" value="Yes" <?php if($recordData4['on_mgmt_council'][0] == 'Yes'){echo 'CHECKED';}?>> On Mgmt. Council
		
										
										</td></tr>
		
										</table>


							
							</td>

<!--END FIRST SECTION: STAFF PROFILE-->		

<!--BEGIN SECOND SECTION: TIMESHEET INFORMATION-->

							<td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">

								<tr valign="bottom"><td align="right"><font color="666666" nowrap>Timesheet Name:</font></td><td><input type="text" name="timesheet_name" size="30" value="<?php echo stripslashes($recordData4['name_timesheet'][0]);?>"></td></tr>

								<tr valign="middle"><td align="right"><font color="666666">Empl. Type/FTEs:</font></td><td>
								
								
									<table cellpadding="0" cellspacing="0" border="0">
									<tr><td>
										
											<select name="empl_type" class="body">
											<option value="">
											
											<?php foreach($v1Result['valueLists']['employee_type'] as $key => $value) { ?>
											<option value="<?php echo $value;?>" <?php if($recordData4['employee_type'][0] == $value){echo 'SELECTED';}?>> <?php echo $value; ?>
											<?php } ?>
											</select>

											<select name="fte" class="body">
											<option value="">
											
											<?php foreach($v1Result['valueLists']['fte_status'] as $key => $value) { ?>
											<option value="<?php echo $value;?>" <?php if($recordData4['FTE_status'][0] == $value){echo 'SELECTED';}?>> <?php echo $value; ?>
											<?php } ?>
											</select>
									</td></tr>
									</table>
					
								
								</td></tr>
								<tr valign="middle"><td align="right"><font color="666666">Supervisor:</font></td><td>
								
								
									<select name="imm_spvsr" class="body">
									<option value="">
									
									<?php foreach($array_allstaff as $supervisor) { ?>
									<option value="<?php echo $supervisor;?>" <?php if($recordData4['immediate_supervisor_sims_user_ID'][0] == $supervisor){echo 'SELECTED';}?>> <?php echo $supervisor;?>
									<?php } ?>
									</select>
								
								
								
								</td></tr>

								<tr valign="middle"><td align="right"><font color="666666">Primary Bgt. Auth.:</font></td><td>
								
								
									<select name="pba" class="body">
									<option value="">
									
									<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
									<option value="<?php echo $searchData2['sims_user_ID'][0];?>" <?php if($recordData4['bgt_auth_primary_sims_user_ID'][0] == $searchData2['sims_user_ID'][0]){echo 'SELECTED';}?>> <?php echo $searchData2['sims_user_ID'][0];?>
									<?php } ?>
									</select>
								
								
								
								</td></tr>

								<tr valign="middle"><td align="right"><font color="666666">Chief Prog. Officer:</font></td><td>
								
								
									<select name="cpo" class="body">
									
									
									<option value=""></option>
									<option value="vdimock" <?php if($recordData4['chief_prog_officer_sims_user_ID'][0] == 'vdimock'){echo 'SELECTED';}?>> vdimock</option>

									</select>
								
								
								
								</td></tr>

								<tr valign="middle"><td align="right"><font color="666666">Time/Lv. Admin:</font></td><td>
								
								
									<select name="time_leave_admin" class="body">
									<option value="">
									
									<?php foreach($searchResult3['data'] as $key => $searchData3) { ?>
									<option value="<?php echo $searchData3['sims_user_ID'][0];?>" <?php if($recordData4['time_leave_admin_sims_user_ID'][0] == $searchData3['sims_user_ID'][0]){echo 'SELECTED';}?>> <?php echo $searchData3['sims_user_ID'][0];?>
									<?php } ?>
									</select>
								
								
								
								</td></tr>
								
								<tr valign="middle"><td align="right"><font color="666666">cc on Lv. Approval:</font></td><td>
								
								
									<select name="lv_appr_cc" class="body">
									<option value="">
									
									<?php foreach($array_allstaff as $staffmember) { ?>
									<option value="<?php echo $staffmember;?>" <?php if($recordData4['lv_appr_cc'][0] == $staffmember){echo 'SELECTED';}?>> <?php echo $staffmember;?>
									<?php } ?>
									</select>
								
								
								
								</td></tr>



								<tr valign="top"><td align="right"><font color="666666" nowrap>Roles:</font></td><td>

								<input type="checkbox" name="is_bgt_auth" value="Yes" <?php if($recordData4['is_budget_authority'][0] == 'Yes'){echo 'CHECKED';}?>> Is a Budget Authority<br>
								<input type="checkbox" name="is_supervisor" value="Yes" <?php if($recordData4['is_supervisor'][0] == 'Yes'){echo 'CHECKED';}?>> Is a Supervisor<br>
								<input type="checkbox" name="is_auth_rep" value="1" <?php if($recordData4['is_time_leave_admin'][0] == '1'){echo 'CHECKED';}?>> Is a Time/Leave Admin<br>
								
								</td></tr>

								<tr valign="top"><td align="right"><font color="666666" nowrap>Options:</font></td><td>

								<input type="checkbox" name="allow_variable_timesheet_hours" value="Yes" <?php if($recordData4['allow_variable_timesheet_hrs'][0] == 'Yes'){echo 'CHECKED';}?>> Allow variable timesheet hours<br>
								
								</td></tr>
								
								</table>
								
							</td></tr>
							
<!--END SECOND SECTION: TIMESHEET INFORMATION-->

<!--BEGIN THIRD SECTION: SIMS/INTRANET PREFERENCES-->
							
							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;SIMS/Intranet Preferences</td><td class="body">&nbsp;Staff Picture / Signature / Profile Links</td></tr>
	
							<tr><td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								
								<tr valign="bottom"><td align="right"><font color="666666">SIMS ID (max=8):</font></td><td><input type="text" name="sims_user_ID" size="12" value="<?php echo $recordData4['sims_user_ID'][0];?>" maxlength="8"> 
								<input type="checkbox" name="sims_access_main_menu" value="Yes" <?php if($recordData4['cwp_sims_access_main_menu'][0] == 'Yes'){echo 'CHECKED';}?>> SIMS access</td></tr>


								<tr valign="top"><td align="right"><font color="666666" nowrap>Permissions:</font></td><td>

								<input type="checkbox" name="sims_access_time_leave" value="Yes" <?php if($recordData4['cwp_sims_access_time_leave'][0] == 'Yes'){echo 'CHECKED';}?>> Time & Leave<br>
								<input type="checkbox" name="sims_access_supervisors" value="Yes" <?php if($recordData4['cwp_sims_access_spvsr'][0] == 'Yes'){echo 'CHECKED';}?>> Supervisor approvals<br>
								<input type="checkbox" name="sims_access_budget_authorities" value="Yes" <?php if($recordData4['cwp_sims_access_bgt_auth'][0] == 'Yes'){echo 'CHECKED';}?>> Budget authority approvals<br>
								<input type="checkbox" name="sims_access_planning_agrmts" value="Yes" <?php if($recordData4['cwp_sims_access_plan_agrmt'][0] == 'Yes'){echo 'CHECKED';}?>> Planning agreements<br>
								<input type="checkbox" name="sims_access_position_descr" value="Yes" <?php if($recordData4['cwp_sims_access_pos_descr'][0] == 'Yes'){echo 'CHECKED';}?>> Position descriptions<br>
								
								</td></tr>
								
								<tr valign="middle"><td align="right"><font color="666666">Pos. Descr. (PD) Admin:</font></td><td>
								
								
									<select name="pos_descr_admin" class="body">
									<option value="">
									
									<?php foreach($array_allstaff as $staffmember) { ?>
									<option value="<?php echo $staffmember;?>" <?php if($recordData4['pos_descr_admin'][0] == $staffmember){echo 'SELECTED';}?>> <?php echo $staffmember;?>
									<?php } ?>
									</select>
								
								</td></tr>

								<tr valign="bottom"><td align="right"><font color="666666">PD Accessible by:</font></td><td><input type="text" name="pos_descr_admin_other" size="25" value="<?php echo $recordData4['pos_descr_admin_other'][0];?>"></td></tr> 
								
								<tr valign="middle"><td align="right"><font color="666666">Plan. Agrmt. (PA) Admin:</font></td><td>
								
								
									<select name="plan_agrmt_admin" class="body">
									<option value="">
									
									<?php foreach($array_allstaff as $staffmember) { ?>
									<option value="<?php echo $staffmember;?>" <?php if($recordData4['plan_agrmt_admin'][0] == $staffmember){echo 'SELECTED';}?>> <?php echo $staffmember;?>
									<?php } ?>
									</select>
								
								</td></tr>

								<tr valign="bottom"><td align="right"><font color="666666">PA Accessible by:</font></td><td><input type="text" name="plan_agrmt_admin_other" size="25" value="<?php echo $recordData4['plan_agrmt_admin_other'][0];?>"></td></tr> 


								</table>
							
							</td>

<!--END THIRD SECTION: SIMS/INTRANET PREFERENCES-->		

<!--BEGIN FOURTH SECTION: PICTURE-->

							<td class="body" valign="top" width="50%">
							
											<table cellspacing="0" cellpadding="5" border="0" width="100%">
											<tr valign="bottom"><td><img src="http://www.sedl.org/images/people/<?php echo $recordData4['sims_user_ID'][0];?>.jpg"><p><img src="http://www.sedl.org/staff/sims/signatures/<?php echo $recordData4['sims_user_ID'][0];?>.png" border="1"><p>
											<a href="http://www.sedl.org/staff/personnel/staffprofiles.cgi?intranetonly=yes&showuserid=<?php echo $recordData4['sims_user_ID'][0];?>" target="_blank">Private profile</a><br>
											<a href="http://www.sedl.org/pubs/catalog/authors/<?php echo $recordData4['sims_user_ID'][0];?>.html" target="_blank">Public profile</a></td></tr>
	
											</table>
								
							</td></tr>
							
<!--END FOURTH SECTION: RESPONSIBILITIES/EXPERIENCE/EDUCATION-->



<!--BEGIN FIFTH SECTION: EMERGENCY CONTACT INFORMATION-->
							
							<tr bgcolor="#e2eaa4"><td class="body" colspan="2">&nbsp;Emergency Contact Information <font color="#666666">| <em>Last reviewed: <?php echo $recordData4['emerg_contact_info_last_reviewed_timestamp'][0];?></em> | <em>Last updated: <?php echo $recordData4['emerg_contact_info_last_mod_timestamp'][0];?></em></font></td></tr>
	
							<tr><td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								
								<tr valign="top"><td align="right" nowrap><font color="666666">Primary Contact:</font></td><td nowrap width="100%">
								
								<?php echo $recordData4['emerg_contact_prim_name'][0];?> | <?php echo $recordData4['emerg_contact_prim_relation'][0];?>
								
								</td></tr>


								<tr valign="top"><td align="right"><font color="666666" nowrap>Phone(s):</font></td><td>
								
								<font color="#666666">Home: </font> <?php echo $recordData4['emerg_contact_prim_phone_hm'][0];?><br>
								<font color="#666666">Work: </font> <?php echo $recordData4['emerg_contact_prim_phone_wk'][0];?><br>
								<font color="#666666">Mobile: </font> <?php echo $recordData4['emerg_contact_prim_phone_mbl'][0];?>
								
								</td></tr>



								</table>
							
							</td>

<!--END FIFTH SECTION: EMERGENCY CONTACT INFORMATION-->		

<!--BEGIN SIXTH SECTION: PLACEHOLDER-->

							<td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">

								<tr valign="top"><td align="right" nowrap><font color="666666">Alternate Contact:</font></td><td nowrap width="100%">
								
								<?php echo $recordData4['emerg_contact_alt_name'][0];?> | <?php echo $recordData4['emerg_contact_alt_relation'][0];?>
								
								</td></tr>


								<tr valign="top"><td align="right"><font color="666666" nowrap>Phone(s):</font></td><td>
								
								<font color="#666666">Home: </font> <?php echo $recordData4['emerg_contact_alt_phone_hm'][0];?><br>
								<font color="#666666">Work: </font> <?php echo $recordData4['emerg_contact_alt_phone_wk'][0];?><br>
								<font color="#666666">Mobile: </font> <?php echo $recordData4['emerg_contact_alt_phone_mbl'][0];?>
								
								</td></tr>

								
								</table>
								
							</td></tr>
							
<!--END SIXTH SECTION: PLACEHOLDER-->
	

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
 
} elseif ($action == 'show_mine') { 

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
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong><?php echo $recordData4['c_full_name_last_first'][0];?> - <?php echo $recordData4['primary_SEDL_workgroup'][0];?></strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: Any changes made to this record must be saved by clicking the Update Profile button. | Last updated: <?php echo $recordData4['profile_info_last_mod_timestamp'][0];?><br>
			If updates or corrections are needed to your profile information, please contact <a href="mailto:eric.waters@sedl.org">eric.waters@sedl.org</a>.
			</p></td></tr>

			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILE-->


							<table cellspacing="0" cellpadding="1" width="100%" border="1" bordercolor="#cccccc">
							
							<form name="edit_profile">
							<input type="hidden" name="action" value="update_emerg_info">
							<input type="hidden" name="update_row_ID" value="<?php echo $recordData4['c_cwp_row_ID'][0];?>">

							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;Profile</td><td class="body">&nbsp;Timesheet Information</td></tr>
							
							<tr><td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								<tr><td valign="top">
										<table cellspacing="0" cellpadding="5" border="0" width="100%">
										<tr valign="bottom"><td align="right" nowrap><font color="666666">Name:</font></td><td width="100%"><?php echo $recordData4['c_full_name_last_first'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Status:</font></td><td nowrap><?php echo $recordData4['current_employee_status'][0];?></td></tr>
										<tr valign="bottom"><td align="right" nowrap valign="top"><font color="666666">Title:</font></td><td><?php echo $recordData4['job_title'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Unit:</font></td><td><?php echo $recordData4['primary_SEDL_workgroup'][0];?></td></tr>
										<tr valign="middle"><td align="right" nowrap><font color="666666">Other Project(s):</font></td><td><?php echo $recordData4['other_SEDL_workgroup'][0];?></td></tr>
										
										
										<tr valign="bottom"><td align="right" nowrap><font color="666666">E-mail/Ext:</font></td><td nowrap><?php echo $recordData4['email'][0];?> / <?php echo $recordData4['phone_ext'][0];?></td></tr>
		
										<tr valign="bottom"><td align="right" nowrap><font color="666666">Work Hrs/Ste#:</font></td><td nowrap><?php echo $recordData4['staff_work_hours'][0];?> / <?php echo $recordData4['suite_number'][0];?></td></tr>
		
		
										<tr valign="bottom"><td align="right" nowrap><font color="666666" nowrap>Empl. Dates:</font></td><td nowrap><?php echo $recordData4['empl_start_date'][0];?> to <?php if($recordData4['empl_end_date'][0] != ''){ echo $recordData4['empl_end_date'][0]; }else{ echo 'Current';}?></td></tr>

										</table>

								</td><td>
								
										<table cellspacing="0" cellpadding="5" border="0" width="100%">
										<tr valign="bottom"><td><img src="http://www.sedl.org/images/people/<?php echo $recordData4['sims_user_ID'][0];?>.jpg"><p>
											<a href="http://www.sedl.org/staff/personnel/staffprofiles.cgi?intranetonly=yes&showuserid=<?php echo $recordData4['sims_user_ID'][0];?>" target="_blank">Private profile</a><br>
											<a href="http://www.sedl.org/pubs/catalog/authors/<?php echo $recordData4['sims_user_ID'][0];?>.html" target="_blank">Public profile</a><br>
											<a href="http://www.sedl.org/staff/planning/vita_upload.cgi">My vitae</a></td></tr>

										</table>
								
								
								</td></tr>
								</table>
							
							</td>

<!--END FIRST SECTION: STAFF PROFILE-->		

<!--BEGIN SECOND SECTION: TIMESHEET INFORMATION-->

							<td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">

								<tr valign="bottom"><td align="right" nowrap><font color="666666" nowrap>Timesheet Name:</font></td><td width="100%"><?php echo $recordData4['name_timesheet'][0];?></td></tr>

								<tr valign="middle"><td align="right" nowrap><font color="666666">Empl. Type/FTEs:</font></td><td><?php echo $recordData4['employee_type'][0];?> / <?php echo $recordData4['FTE_status'][0];?></td></tr>
								<tr valign="middle"><td align="right" nowrap><font color="666666">Supervisor:</font></td><td><?php echo $recordData4['immediate_supervisor_sims_user_ID'][0];?></td></tr>

								<tr valign="middle"><td align="right" nowrap><font color="666666">Primary Bgt. Auth.:</font></td><td><?php echo $recordData4['bgt_auth_primary_sims_user_ID'][0];?></td></tr>

								<tr valign="middle"><td align="right" nowrap><font color="666666">Time/Lv. Admin:</font></td><td><?php echo $recordData4['time_leave_admin_sims_user_ID'][0];?></td></tr>
								
								<tr valign="top"><td align="right"><font color="666666" nowrap>Roles:</font></td><td>

								<?php if($recordData4['is_budget_authority'][0] == 'Yes'){echo 'Budget Authority<br>';}?>
								<?php if($recordData4['is_supervisor'][0] == 'Yes'){echo 'Supervisor<br>';}?>
								<?php if($recordData4['is_time_leave_admin'][0] == '1'){echo 'Time/Leave Admin<br>';}?>
								
								</td></tr>

								
								</table>
								
							</td></tr>
							
<!--END SECOND SECTION: TIMESHEET INFORMATION-->

<!--BEGIN THIRD SECTION: CURRENT RESPONSIBILITIES-->
							
							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;Current Responsibilities</td><td class="body">&nbsp;Experience / Education</td></tr>
	
							<tr><td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">

								<tr valign="bottom"><td align="right"><td>
								<?php echo stripslashes($recordData4['current_responsibilities'][0]);?><p>
								
								
								
								</td></tr>

								
								</table>
							
							</td>

<!--END THIRD SECTION: SIMS/INTRANET PREFERENCES-->		

<!--BEGIN FOURTH SECTION: EXPERIENCE/EDUCATION-->

							<td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">

								<tr valign="bottom"><td align="right"><td>
								<?php echo stripslashes($recordData4['experience_education'][0]);?><p>
								
								
								
								</td></tr>

								
								</table>
								
							</td></tr>
							
<!--END FOURTH SECTION: RESPONSIBILITIES/EXPERIENCE/EDUCATION-->



<!--BEGIN FIFTH SECTION: EMERGENCY CONTACT INFORMATION-->
							
							<tr bgcolor="#e2eaa4"><td class="body" colspan="2">&nbsp;Emergency Contact Information <font color="#666666">| <em>Last reviewed: <?php echo $recordData4['emerg_contact_info_last_reviewed_timestamp'][0];?></em> | <em>Last updated: <?php echo $recordData4['emerg_contact_info_last_mod_timestamp'][0];?></em></font></td></tr>
	
							<tr><td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								
								<tr valign="top"><td align="right" nowrap><font color="666666">Primary Contact:</font></td><td nowrap width="100%">
								
								<input type="text" name="emerg_contact_prim_name" size="20" value="<?php echo $recordData4['emerg_contact_prim_name'][0];?>"> NAME<br>
								<input type="text" name="emerg_contact_prim_relation" size="20" value="<?php echo $recordData4['emerg_contact_prim_relation'][0];?>"> RELATION

								</td></tr>


								<tr valign="top"><td align="right"><font color="666666" nowrap>Phone(s):<br>(at least 2)</font></td><td>

								<input type="text" name="emerg_contact_prim_phone_hm" size="20" value="<?php echo $recordData4['emerg_contact_prim_phone_hm'][0];?>"> HOME<br>
								<input type="text" name="emerg_contact_prim_phone_wk" size="20" value="<?php echo $recordData4['emerg_contact_prim_phone_wk'][0];?>"> WORK<br>
								<input type="text" name="emerg_contact_prim_phone_mbl" size="20" value="<?php echo $recordData4['emerg_contact_prim_phone_mbl'][0];?>"> MOBILE

								
								
								</td></tr>



								</table>
							
							</td>

<!--END FIFTH SECTION: EMERGENCY CONTACT INFORMATION-->		

<!--BEGIN SIXTH SECTION: PLACEHOLDER-->

							<td class="body" valign="top" width="50%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								
								<tr valign="top"><td align="right" nowrap><font color="666666">Alternate Contact:</font></td><td nowrap width="100%">
								
								<input type="text" name="emerg_contact_alt_name" size="20" value="<?php echo $recordData4['emerg_contact_alt_name'][0];?>"> NAME<br>
								<input type="text" name="emerg_contact_alt_relation" size="20" value="<?php echo $recordData4['emerg_contact_alt_relation'][0];?>"> RELATION

								</td></tr>


								<tr valign="top"><td align="right"><font color="666666" nowrap>Phone(s):<br>(at least 2)</font></td><td>

								<input type="text" name="emerg_contact_alt_phone_hm" size="20" value="<?php echo $recordData4['emerg_contact_alt_phone_hm'][0];?>"> HOME<br>
								<input type="text" name="emerg_contact_alt_phone_wk" size="20" value="<?php echo $recordData4['emerg_contact_alt_phone_wk'][0];?>"> WORK<br>
								<input type="text" name="emerg_contact_alt_phone_mbl" size="20" value="<?php echo $recordData4['emerg_contact_alt_phone_mbl'][0];?>"> MOBILE

								
								
								</td></tr>



								</table>
								
							</td></tr>
							
<!--END SIXTH SECTION: PLACEHOLDER-->
	

							<tr><td class="body" colspan="2">
							<center><input type="submit" name="submit" value="Update Profile"><input type="submit" name="no_changes" value="No Changes Needed"></center>
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
$first_name = $_GET['first_name'];
$middle_initial = $_GET['middle_initial'];
$last_name = $_GET['last_name'];
$status = $_GET['status'];
$title = $_GET['title'];
$sedl_unit = $_GET['sedl_unit'];

for($i=0 ; $i<count($_GET['other_SEDL_workgroup']) ; $i++) { // TRANSFORM CHECKBOX ARRAY FOR PROCESSING
		$other_SEDL_workgroup .= $_GET['other_SEDL_workgroup'][$i]."\r"; 
		}

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
	$immediate_supervisor_sims_user_ID = $imm_spvsr; //  for use when sending to mysql
$pba = $_GET['pba'];
	$bgt_auth_primary_sims_user_ID = $pba; //  for use when sending to mysql
$cpo = $_GET['cpo'];
$time_leave_admin = $_GET['time_leave_admin'];
	$time_leave_admin_sims_user_ID = $time_leave_admin; //  for use when sending to mysql
$pos_descr_admin = $_GET['pos_descr_admin'];
$pos_descr_admin_other = $_GET['pos_descr_admin_other'];
$plan_agrmt_admin = $_GET['plan_agrmt_admin'];
$plan_agrmt_admin_other = $_GET['plan_agrmt_admin_other'];
$lv_appr_cc = $_GET['lv_appr_cc'];
$is_bgt_auth = $_GET['is_bgt_auth'];
$is_supervisor = $_GET['is_supervisor'];
$is_auth_rep = $_GET['is_auth_rep'];
$allow_variable_timesheet_hours = $_GET['allow_variable_timesheet_hours'];
$sims_user_ID = $_GET['sims_user_ID'];
$sims_access_main_menu = $_GET['sims_access_main_menu'];
$sims_access_time_leave = $_GET['sims_access_time_leave'];
$sims_access_supervisors = $_GET['sims_access_supervisors'];
$sims_access_budget_authorities = $_GET['sims_access_budget_authorities'];
$sims_access_planning_agrmts = $_GET['sims_access_planning_agrmts'];
$sims_access_position_descr = $_GET['sims_access_position_descr'];

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

$update -> AddDBParam('webuser_mod_ID',$_SESSION['user_ID']);
$update -> AddDBParam('name_first',$first_name);
$update -> AddDBParam('name_middle',$middle_initial);
$update -> AddDBParam('name_last',$last_name);
$update -> AddDBParam('current_employee_status',$status);
$update -> AddDBParam('job_title',$title);
$update -> AddDBParam('primary_SEDL_workgroup',$sedl_unit);
$update -> AddDBParam('other_SEDL_workgroup',$other_SEDL_workgroup);
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
$update -> AddDBParam('chief_prog_officer_sims_user_ID',$cpo);
$update -> AddDBParam('time_leave_admin_sims_user_ID',$time_leave_admin);
$update -> AddDBParam('pos_descr_admin',$pos_descr_admin);
$update -> AddDBParam('pos_descr_admin_other',$pos_descr_admin_other);
$update -> AddDBParam('plan_agrmt_admin',$plan_agrmt_admin);
$update -> AddDBParam('plan_agrmt_admin_other',$plan_agrmt_admin_other);
$update -> AddDBParam('lv_appr_cc',$lv_appr_cc);
$update -> AddDBParam('is_budget_authority',$is_bgt_auth);
$update -> AddDBParam('is_supervisor',$is_supervisor);
$update -> AddDBParam('is_time_leave_admin',$is_auth_rep);
$update -> AddDBParam('allow_variable_timesheet_hrs',$allow_variable_timesheet_hours);
$update -> AddDBParam('sims_user_ID',$sims_user_ID);
$update -> AddDBParam('cwp_sims_access_main_menu',$sims_access_main_menu);
$update -> AddDBParam('cwp_sims_access_time_leave',$sims_access_time_leave);
$update -> AddDBParam('cwp_sims_access_spvsr',$sims_access_supervisors);

if(($sims_access_supervisors == 'Yes')||($sims_access_budget_authorities == 'Yes')){
$update -> AddDBParam('cwp_sims_access_pos_descr_workgroup','Yes');
$update -> AddDBParam('cwp_sims_access_plan_agrmt_workgroup','Yes');
}

$update -> AddDBParam('cwp_sims_access_bgt_auth',$sims_access_budget_authorities);
$update -> AddDBParam('cwp_sims_access_plan_agrmt',$sims_access_planning_agrmts);
$update -> AddDBParam('cwp_sims_access_pos_descr',$sims_access_position_descr);
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
			//$intranet_pwd = $updaterecordData['sims_pwd'][0];
			$photo_permissions = $updaterecordData['photo_permissions'][0];
			$start_date_mysql = $updaterecordData['c_empl_start_date_mysql'][0];

// GRAB COMMUNICATIONS FIELDS FOR MYSQL REPLACE INTO STATEMENT

			$responsibilities = $updaterecordData['current_responsibilities'][0];
			$experience = $updaterecordData['experience_education'][0];
			$show_birthday = $updaterecordData['show_birthday'][0];
			$external_publications = $updaterecordData['external_publications'][0];

			
			################################
			## END: UPDATE THE FMP RECORD ##
			################################
			

			######################################################
			## START: BEFORE UPDATE, CHECK IF USER EXISTS IN MYSQL
			######################################################
			// QUERY MySQL FOR THIS USER ID
			$db = mysql_connect('localhost','intranetuser','limited');
			$db_selected = mysql_select_db('intranet',$db);
			$command_check_user = "select userid from staff_profiles where userid like '$sims_user_ID'";
			$result = mysql_query("$command_check_user");
			$num_rows = mysql_num_rows($result);

			// IF USER ID NOT FOUND IN MYSQL, INITIATE RECORD
			if ($num_rows == '0') {
				$db = mysql_connect('localhost','intranetuser','limited');
				$db_selected = mysql_select_db('intranet',$db);
				$strong_pwd = crypt('password');
				$command_insert_user = "INSERT INTO staff_profiles 
				(fm_record_id, firstname, middleinitial, lastname, jobtitle, phone, userid, email, phoneext, birthmonth, birthday, timesheetname, department_abbrev, mgmtcouncil, lastupdated, lastupdated_by, room_number, start_date, adjusted_start_date, supervised_by, automated_sentence, photo_permissions, strong_pwd, empl_type, degree, other_SEDL_workgroup, immediate_supervisor_sims_user_ID, bgt_auth_primary_sims_user_ID)
				VALUES ('$update_row_ID', '$first_name', '$middle_initial', '$last_name', '$title', '$phone', '$sims_user_ID', '$email', '$ext', '$birthmonth', '$birthday', '$timesheet_name', '$sedl_unit', '$on_mgmt_council', '$last_updated', '$last_updated_by', '$ste_num', '$start_date_mysql', '$stafflistsorting', '$imm_spvsr', '', 'Needed', '$strong_pwd', '$empl_type', '', '$is_bgt_auth', '$is_auth_rep', '$other_SEDL_workgroup', '$immediate_supervisor_sims_user_ID', '$bgt_auth_primary_sims_user_ID')";
				$result = mysql_query("$command_insert_user");
				//echo "<p>INSERTING RECORD: $command_insert_user</p>";
			} # END IF
			######################################################
			## END: BEFORE UPDATE, CHECK IF USER EXISTS IN MYSQL
			######################################################

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
			
			if($status == 'SEDL Employee'){
				## BACKSLASH VARIABLES BEFORE SENDING TO MYSQL
				$first_name = addslashes($first_name);
				$middle_initial = addslashes($middle_initial);
				$last_name = addslashes($last_name);
				$title = addslashes($title);
				$phone = addslashes($phone);
				$email = addslashes($email);
				$ext = addslashes($ext);
				$timesheet_name = addslashes($timesheet_name);
				$sedl_unit = addslashes($sedl_unit);
				$on_mgmt_council = addslashes($on_mgmt_council);
				$lastupdated = addslashes($lastupdated);
				$lastupdated_by = addslashes($lastupdated_by);
				$ste_num = addslashes($ste_num);
				$start_date_mysql = addslashes($start_date_mysql);
				$stafflistsorting = addslashes($stafflistsorting);
				$imm_spvsr = addslashes($imm_spvsr);
				$empl_type = addslashes($empl_type);
				$other_SEDL_workgroup = addslashes($other_SEDL_workgroup);
				$immediate_supervisor_sims_user_ID = addslashes($immediate_supervisor_sims_user_ID);
				$bgt_auth_primary_sims_user_ID = addslashes($bgt_auth_primary_sims_user_ID);
				$time_leave_admin = addslashes($time_leave_admin);

			
			$command = 
			"UPDATE staff_profiles 
			SET 
			fm_record_id = '$update_row_ID',
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
			empl_type='$empl_type', 
			is_bgt_auth='$is_bgt_auth', 
			is_auth_rep='$is_auth_rep',
			other_SEDL_workgroup='$other_SEDL_workgroup',
			immediate_supervisor_sims_user_ID='$immediate_supervisor_sims_user_ID',
			bgt_auth_primary_sims_user_ID='$bgt_auth_primary_sims_user_ID',
			time_leave_admin_sims_user_ID='$time_leave_admin'

			WHERE fm_record_id like '$update_row_ID'";
//echo "<p>$command</p>";			
			} else {
			
			$command = 
			
			"delete from staff_profiles WHERE userid='$sims_user_ID'"; 
			
			
			} 
			
			$update = mysql_query($command);
			
			if (!$update) {
			   die('Invalid MySQL query: ' . mysql_error());
			}else{
			
			//$num_results = mysql_num_rows($result);
			exec('/home/httpd/html/staff/personnel/staffprofiles.cgi'); // REGENERATE INTRANET AND PUBLIC STAFF PAGES
			//echo 'hello';
			//echo '<br>Update Successful!';
			}
			
			//exit;

			
			
			
			if ($status == 'Former Employee'){ // IF THE EMPLOYEE STATUS WAS CHANGED TO FORMER EMPLOYEE, DELETE THE RECORD FROM MYSQL
			
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
					   die('Invalid MySQL query: ' . mysql_error());
					}else{
					
					//$num_results = mysql_num_rows($result);
					
					//echo '<br>Update Successful!';
					}
					
					//exit;
			}
					
} else {


			echo 'There was an error updating the record.<p>errorCode: '.$updateResult['errorCode'];
			echo '<p>e-mail: '.$email;
			echo '<p>phone_full: '.$phone;
			echo '<p>empl_start_date: '.$start_date;



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

$update -> AddDBParam('webuser_mod_ID',$_SESSION['user_ID']);

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
$cpo = $_GET['cpo'];
$time_leave_admin = $_GET['time_leave_admin'];
$plan_agrmt_admin = $_GET['plan_agrmt_admin'];
$plan_agrmt_admin_other = $_GET['plan_agrmt_admin_other'];
$pos_descr_admin = $_GET['pos_descr_admin'];
$pos_descr_admin_other = $_GET['pos_descr_admin_other'];
$lv_appr_cc = $_GET['lv_appr_cc'];
$is_bgt_auth = $_GET['is_bgt_auth'];
$is_supervisor = $_GET['is_supervisor'];
$is_auth_rep = $_GET['is_auth_rep'];
$allow_variable_timesheet_hours = $_GET['allow_variable_timesheet_hours'];
$sims_user_ID = $_GET['sims_user_ID'];
$sims_access_main_menu = $_GET['sims_access_main_menu'];
$sims_access_time_leave = $_GET['sims_access_time_leave'];
$sims_access_supervisors = $_GET['sims_access_supervisors'];
$sims_access_budget_authorities = $_GET['sims_access_budget_authorities'];
$sims_access_planning_agrmts = $_GET['sims_access_planning_agrmts'];
$sims_access_position_descr = $_GET['sims_access_position_descr'];
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

$newrecord -> AddDBParam('webuser_mod_ID',$_SESSION['user_ID']);
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
$newrecord -> AddDBParam('lv_appr_cc',$lv_appr_cc);
$newrecord -> AddDBParam('bgt_auth_primary_sims_user_ID',$pba);
$newrecord -> AddDBParam('chief_prog_officer_sims_user_ID',$cpo);
$newrecord -> AddDBParam('time_leave_admin_sims_user_ID',$time_leave_admin);
$newrecord -> AddDBParam('pos_descr_admin',$pos_descr_admin);
$newrecord -> AddDBParam('pos_descr_admin_other',$pos_descr_admin_other);
$newrecord -> AddDBParam('plan_agrmt_admin',$plan_agrmt_admin);
$newrecord -> AddDBParam('plan_agrmt_admin_other',$plan_agrmt_admin_other);
$newrecord -> AddDBParam('is_budget_authority',$is_bgt_auth);
$newrecord -> AddDBParam('is_supervisor',$is_supervisor);
$newrecord -> AddDBParam('is_time_leave_admin',$is_auth_rep);
$newrecord -> AddDBParam('allow_variable_timesheet_hrs',$allow_variable_timesheet_hours);
$newrecord -> AddDBParam('sims_user_ID',$sims_user_ID);
$newrecord -> AddDBParam('cwp_sims_access_main_menu',$sims_access_main_menu);
$newrecord -> AddDBParam('cwp_sims_access_time_leave',$sims_access_time_leave);
$newrecord -> AddDBParam('cwp_sims_access_spvsr',$sims_access_supervisors);
$newrecord -> AddDBParam('cwp_sims_access_bgt_auth',$sims_access_budget_authorities);

if(($sims_access_supervisors == 'Yes')||($sims_access_budget_authorities == 'Yes')){
	$newrecord -> AddDBParam('cwp_sims_access_pos_descr_workgroup','Yes');
	$newrecord -> AddDBParam('cwp_sims_access_plan_agrmt_workgroup','Yes');
}


$newrecord -> AddDBParam('cwp_sims_access_plan_agrmt',$sims_access_planning_agrmts);
$newrecord -> AddDBParam('cwp_sims_access_pos_descr',$sims_access_position_descr);
$newrecord -> AddDBParam('last_updated_by',$_SESSION['user_ID']);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
if ($newrecordResult['errorCode'] == '0') {
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

	## BACKSLASH VARIABLES BEFORE SENDING TO MYSQL
	$first_name = addslashes($first_name);
	$middle_initial = addslashes($middle_initial);
	$last_name = addslashes($last_name);
	$title = addslashes($title);
	$phone = addslashes($phone);
	$email = addslashes($email);
	$ext = addslashes($ext);
	$timesheet_name = addslashes($timesheet_name);
	$sedl_unit = addslashes($sedl_unit);
	$on_mgmt_council = addslashes($on_mgmt_council);
	$lastupdated = addslashes($lastupdated);
	$lastupdated_by = addslashes($lastupdated_by);
	$ste_num = addslashes($ste_num);
	$start_date_mysql = addslashes($start_date_mysql);
	$stafflistsorting = addslashes($stafflistsorting);
	$imm_spvsr = addslashes($imm_spvsr);
	$empl_type = addslashes($empl_type);



$command = 

"INSERT INTO staff_profiles 

(fm_record_id, firstname, middleinitial, lastname, jobtitle, phone, userid, email, phoneext, birthmonth, birthday, timesheetname, department_abbrev, mgmtcouncil, lastupdated, lastupdated_by, room_number, start_date, adjusted_start_date, supervised_by, automated_sentence, photo_permissions, strong_pwd, empl_type, degree)

VALUES

('$fm_record_id', '$first_name', '$middle_initial', '$last_name', '$title', '$phone', '$sims_user_ID', '$email', '$ext', '$birthmonth', '$birthday', '$timesheet_name', '$sedl_unit', '$on_mgmt_council', '$last_updated', '$last_updated_by', '$ste_num', '$start_date_mysql', '', '$imm_spvsr', '', 'Needed', '$strong_pwd', '$empl_type', '')";




$update = mysql_query($command);

	if (!$update) {
		die('Invalid MySQL query: ' . mysql_error());
	} else {

		//$num_results = mysql_num_rows($result);
		exec('/home/httpd/html/staff/personnel/staffprofiles.cgi'); // REGENERATE INTRANET AND PUBLIC STAFF PAGES
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
 
 echo 'Error';
 
 }
 ?>
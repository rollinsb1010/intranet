<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2007 by the Texas Comprehensive Center at SEDL
#
# Written by Eric Waters 06/26/2007
#############################################################################

###############################
## START: LOAD FX.PHP INCLUDES
###############################
include_once('FX/FX.php');
include_once('FX/server_data.php');
###############################
## END: LOAD FX.PHP INCLUDES
###############################

// PRINT PHP SESSION VARIABLES
//echo '<pre>';
//var_dump($_SESSION);
//echo '</pre>';

$sims_on = 'yes';

if($sims_on == 'no'){
echo 'SIMS is undergoing maintenance. Please try again later. | <a href="http://www.sedl.org/staff">Return to intranet</a>';
exit;
}

#####################################################
## START: FIND CONTACT RECORD FOR THIS USER
#####################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('staff_ID','=='.$_SESSION ['staff_ID']);

$searchResult = $search -> FMFind();
//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r($search);
$recordData = current($searchResult['data']);
$update_row = $recordData['c_cwp_row_ID'][0];
$rand_num = rand();
#####################################################
## END: FIND CONTACT RECORD FOR THIS USER
#####################################################

$_SESSION['user_ID'] = $recordData['sims_user_ID'][0];
$_SESSION['timesheet_name'] = $recordData['name_timesheet'][0];
$_SESSION['leave_requests_access'] = $recordData['cwp_sims_access_leave_requests'][0];

//echo $_SESSION['user_id'];

#####################################################
## START: UPDATE SIMS PASSWORD IF REQUIRED
#####################################################
if($_POST['pwd_update'] == '1'){
$pwd = $_POST['pwd'];
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','staff');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);
$update -> AddDBParam('sims_pwd', $pwd);

$updateResult = $update -> FMEdit();

//echo '<p>updateResult[errorCode]: '.$updateResult['errorCode'];


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


//$command = "UPDATE staff_profiles SET intranet_pwd = '$pwd' where fm_record_id like '$update_row'";
//$update = mysql_query($command);

//if (!$update) {
//   die('Invalid query: ' . mysql_error());
//}else{

//$num_results = mysql_num_rows($result);

//echo '<br>Update Successful!';
//}

}
#####################################################
## END: UPDATE SIMS PASSWORD IF REQUIRED
#####################################################

$current_pay_period = date("m").'/'.date("t").'/'.date("Y");
//echo '<p>$current_pay_period: '.$current_pay_period;
?> <!--END: LOGIN AND BEGIN SESSION-->




<!--###DISPLAY THE MAIN MENU IF LOGIN IS VALID###-->


<?php
if ($searchResult['foundCount'] == 1) { 


if ($recordData['cwp_sims_access_main_menu'][0] != 'Yes') { 
echo 'Your account does not have access to SIMS. Please contact <a href="mailto:tracy.hoes@sedl.org">Tracy Hoes</a> in Administrative Services for more information.<p>
<a href="http://www.sedl.org/staff"><< Return to SEDL Intranet</a>
';
exit;
}
/*
foreach($searchResult['data'] as $key => $searchData);
$recordDetail = explode('.',$key);
$current_recid = $recordDetail[0];

$rand_num = rand();

//echo $current_recid;

//###TRIGGER THE TIMESTAMP TO UPDATE IN FMP###

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS.fp7','staff_profile_detail');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_recid);
$update -> AddDBParam('txccNet_webtrigger', $rand_num);

$updateResult = $update -> FMEdit();

//echo $updateResult['errorCode'];



//###ADD THIS CWP LOGIN EVENT TO THE FMP USER LOG###

$login = new FX($serverIP,$webCompanionPort);
$login -> SetDBData('SIMS.fp7','db_access_log');
$login -> SetDBPassword($webPW,$webUN);
$login -> AddDBParam('session_type', 'cwp');
$login -> AddDBParam('user_target', 'sims');
$login -> AddDBParam('cwp_username', $_SESSION['user_id']);

$loginResult = $login -> FMNew();

//$loginData = current($newrecordResult['data']);


foreach($loginResult['data'] as $key => $loginData);
$loginDetail = explode('.',$key);
$login_recid = $loginDetail[0];

$_SESSION['login_recid'] = $login_recid;

//echo $loginResult['errorCode'];



//echo $_SESSION['esc_region'];

*/

//$strong_pwd = crypt('password','password');
//echo '<p>$strong_pwd: '.$strong_pwd;

?>


<html>
<head>
<title>SIMS: Main Menu</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">



<table cellpadding=5 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
	<tr><td>
	
	<table border=0 bordercolor="ccccae" cellpadding=4 cellspacing=0 bgcolor="ffffff" width="755" class="body">
	
		<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
		<tr><td height="33" colspan="2" scope="row"><h1>SIMS Main Menu</h1><hr /></td></tr>
		
		<tr><td class="body"><h2>Current User: <span style="color:#000000"><?php echo stripslashes($recordData['c_full_name_first_last'][0]).' ('.$recordData['primary_SEDL_workgroup'][0].')';?></span></h2></td><td align="right"><a href="http://www.sedl.org/staff/sims/menu_help_tickets.php">SEDL HelpDesk</a> | <a href="sims_menu.php?src=logout">Return to intranet</a></td></tr>
		
		<tr style="background-color:#e6ecfd"><td colspan="2" style="padding:8px;border:1px dotted #4c75fb">Profile: <?php echo stripslashes($recordData['c_full_name_first_last'][0]);?> (<?php echo $recordData['primary_SEDL_workgroup'][0];?>) | <?php echo $recordData['job_title'][0];?> | Ext: <?php echo $recordData['phone_ext'][0];?> | Ste: <?php echo $recordData['suite_number'][0];?> | Spvsr: <?php echo $recordData['immediate_supervisor_sims_user_ID'][0];?> | Empl. Since: <?php echo $recordData['empl_start_date'][0];?></td></tr>

		<tr><td colspan=2>
			<center>
			<table width="100%" bgcolor="#ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
			
			<?php if($_SESSION['confirm_update'] == '1'){?>
			<tr><td colspan="2"><p class="alert_small">Your emergency contact information was successfully updated. <img src="/staff/sims/images/green_check.png"></p></td></tr>	
			<?php $_SESSION['confirm_update'] = ''; }?>

			<?php if(($_POST['pwd_update'] == '1')&&($updateResult['errorCode'] == '0')){?>
			<tr><td colspan="2"><p class="alert_small">Your SIMS password was successfully updated. <img src="/staff/sims/images/green_check.png"></p></td></tr>	
			<?php $_SESSION['confirm_update'] = ''; }?>
			
			<tr><td valign="top" width="50%">
				
					<table cellpadding=4 cellspacing=0 width="100%" valign="top" class="sims">
					
			
						
<!--
###################################################################################
###################################################################################
############ ADMINISTRATIVE TOOLS #################################################
###################################################################################
###################################################################################
-->
						<tr><td bgcolor="#003745" class="body" nowrap><span class="head2">Administrative Tools</span></td></tr>
						
<?php if ($recordData['cwp_sims_access_time_leave'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">Staff Members</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
						
								<font face="verdana, helvetica, arial" color="999999">
								<a href="menu_timesheets.php?sortfield=c_PayPeriodEnd">My timesheets</a> | <a href="bgt_code_report_staff.php?action=new">Time/budget code reports</a><br>
								<a href="menu_leave.php">My leave requests</a> | <a href="/staff/personnel/leavereport.cgi" target="_blank">My leave report</a><br><a href="my_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>">My leave calendar</a> | <a href="staff_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>"><?php echo $recordData['primary_SEDL_workgroup'][0];?> leave calendar</a><br>
								<?php if($recordData['cwp_sims_access_paystubs'][0] == 'Yes'){?><a href="menu_paystubs.php">My paystubs</a><?php }?><?php if($recordData['cwp_sims_access_benefits'][0] == 'Yes'){?> | <a href="benefits.php?id=<?php echo $recordData['staff_ID'][0];?>" target="_blank">My benefits report</a><?php }?><?php if($recordData['cwp_sims_access_personnel_actions'][0] == 'Yes'){?><br><a href="personnel_actions.php" target="_blank">My personnel actions</a> | <a href="personnel_memos.php" target="_blank">My personnel memos</a><?php }?><p>
								<?php if ($recordData['cwp_sims_access_travel_request'][0] == 'Yes') { ?><a href="menu_travel.php">My travel requests</a><br><?php } ?>
								
								</font>
								
						</td></tr>

<?php } ?>

<?php if ($recordData['cwp_sims_access_time_leave_unit'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">Workgroup Admin</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
						
								<font face="verdana, helvetica, arial" color="999999">
								<?php if ($recordData['cwp_sims_access_time_leave_workgroup'][0] == 'Yes') { ?><a href="menu_timesheets_ar_admin.php">Verify timesheets</a><br>
								<a href="menu_leave_ar.php">Print leave requests</a><br>
								<a href="staff_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>">Workgroup leave calendar</a><br><?php }?>
								<?php if ($recordData['cwp_sims_access_plan_agrmt_workgroup'][0] == 'Yes') { ?><a href="menu_plan_agrmt_admin.php">Workgroup performance appraisals</a><br><?php }?>
								<?php if ($recordData['cwp_sims_access_pos_descr_workgroup'][0] == 'Yes') { ?><a href="menu_pos_descr_admin.php">Workgroup position descriptions</a><br><?php }?>
								<?php if ($recordData['cwp_sims_access_personnel_actions_admin'][0] == 'Yes') { ?><a href="menu_personnel_actions_wg_admin.php">Workgroup personnel actions</a><br><a href="menu_personnel_actions_wg_temp.php">Temporary personnel actions</a><br><?php }?>
								<?php if ($recordData['cwp_sims_access_personnel_memos_admin'][0] == 'Yes') { ?><a href="menu_memos_wg_admin.php">Workgroup personnel memos</a><br><?php }?>
								</font><p>
								
						</td></tr>

<?php } ?>

<?php if ($recordData['cwp_sims_access_bgt_auth'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">Budget Authorities</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
						
								<font face="verdana, helvetica, arial" color="999999">
								<a href="menu_timesheets_ba_admin.php">Approve timesheets</a><br>
								<a href="menu_leave_ba.php">Approve leave requests</a><br>
								<a href="bgt_code_report_ba.php?action=new">Staff time/budget reports</a><?php if($recordData['cwp_sims_access_bgt_relsw'][0] == 'Yes') { ?> | <a href="mgr_reports_launcher.php?m=relsw">RELSW</a><?php }?><?php if($recordData['cwp_sims_access_bgt_relse'][0] == 'Yes') { ?> | <a href="mgr_reports_launcher.php?m=relse">RELSE</a><?php }?><br>
								<a href="staff_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>">Workgroup leave calendar</a><br>
								<a href="http://www.sedl.org/staff/personnel/budgets.cgi">Budget Authority financial reports</a><br>
								<?php if($recordData['cwp_sims_access_plan_agrmt_workgroup'][0] == 'Yes') { ?><a href="menu_plan_agrmt_admin.php">Workgroup performance appraisals</a><br><?php }?>
								<?php if($recordData['cwp_sims_access_pos_descr_workgroup'][0] == 'Yes') { ?><a href="menu_pos_descr_admin.php">Workgroup position descriptions</a><br><?php }?>
								<?php if($recordData['cwp_sims_access_esign_verify_ba'][0] == 'Yes') { ?><a href="menu_sig_verification_ba.php">eSignature Verification</a><br><?php }?>
								<?php if ($recordData['cwp_sims_access_personnel_actions_ba'][0] == 'Yes') { ?><a href="menu_personnel_actions_ba_admin.php">Workgroup personnel actions</a><br><?php }?>
								<?php if ($recordData['cwp_sims_access_personnel_memos_ba'][0] == 'Yes') { ?><a href="menu_memos_ba_admin.php">Workgroup personnel memos</a><?php }?><p>
								
								<?php if ($recordData['cwp_sims_access_travel_auth_ba'][0] == 'Yes') { ?><p><a href="menu_travel_admin_spvsr.php">Workgroup Travel Authorizations</a><?php }?>
								<p>
								</font>
								
						</td></tr>

<?php } ?>

<?php if ($recordData['cwp_sims_access_spvsr'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">Supervisors</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
						
								<font face="verdana, helvetica, arial" color="999999">
								<a href="menu_timesheets_spvsr_admin.php">Sign timesheets</a><br>
								<a href="menu_leave_spvsr.php">Sign leave requests</a><br>
								<a href="staff_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>">Workgroup leave calendar</a>
								<?php if($recordData['cwp_sims_access_bgt_code_reports'][0] == 'Yes'){?><br><a href="bgt_code_report_spvsr.php?action=new">Staff time/budget reports</a><br><?php }else{?><br><?php }?>
								<?php if($recordData['cwp_sims_access_plan_agrmt_workgroup'][0] == 'Yes') { ?><a href="menu_plan_agrmt_admin.php">Workgroup performance appraisals</a><br><?php }?>
								<?php if($recordData['cwp_sims_access_pos_descr_workgroup'][0] == 'Yes') { ?><a href="menu_pos_descr_admin.php">Workgroup position descriptions</a><?php }?><br>
								<?php if ($recordData['cwp_sims_access_personnel_memos_spvsr'][0] == 'Yes') { ?><a href="menu_memos_ba_admin.php">Workgroup personnel memos</a><?php }?><p>
								
								<?php if ($recordData['cwp_sims_access_travel_auth_spvsr'][0] == 'Yes') { ?><p><a href="menu_travel_admin_spvsr.php">Workgroup Travel Authorizations</a><?php }?>
								<p></font>
								
						</td></tr>

<?php } ?>

<?php if ($recordData['cwp_sims_access_ofts_admin'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">SEDL Admin</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
						
								<font face="verdana, helvetica, arial" color="999999">
								<?php if ($recordData['cwp_sims_access_time_leave_sedl'][0] == 'Yes') { ?><a href="menu_timesheets_ofts_admin.php">Timesheets admin</a><br><?php }?>
								<?php if($recordData['cwp_sims_access_leave_requests'][0] == 'Yes'){?><a href="menu_leave_ofts_admin.php">Leave requests admin</a><br><?php }?>
								<?php if($recordData['cwp_sims_access_bgt_codes'][0] == 'Yes'){?><a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode&pref=active_only">Manage budget codes</a><br><?php }?>
								<?php if($recordData['cwp_sims_access_bgt_code_reports'][0] == 'Yes'){?><a href="bgt_code_report.php?action=new">Budget code reports</a><p><?php }?>
								<?php if($recordData['cwp_sims_access_plan_agrmt_sedl'][0] == 'Yes') { ?><a href="menu_plan_agrmt_admin.php">Performance Appraisals admin</a><br><?php }?>
								<?php if($recordData['cwp_sims_access_pos_descr_sedl'][0] == 'Yes') { ?><a href="menu_pos_descr_admin.php">Position Descriptions admin</a><br><?php }?>
								<?php if($recordData['cwp_sims_access_esign_verify_admin'][0] == 'Yes') { ?><a href="menu_sig_verification.php?action=show_all">eSignature Verification Admin</a><br><?php }?>
								<?php if($recordData['cwp_sims_access_paystubs_admin'][0] == 'Yes'){?><a href="menu_paystubs_admin.php?action=show_all">Paystubs admin</a><?php }?>
								<?php if($recordData['cwp_sims_access_benefits_admin'][0] == 'Yes'){?> | <a href="menu_benefits_admin.php?action=show_all">Benefits admin</a><br><?php }?>
								<?php if($recordData['cwp_sims_access_personnel_actions_sedl'][0] == 'Yes'){?><a href="menu_personnel_actions_admin.php?action=show_all">Personnel actions</a> | <a href="menu_personnel_actions_temp_admin.php?action=show_all">Temp personnel actions</a><br><a href="menu_memos_admin.php?action=show_all">Personnel memos</a><?php }?><p>
								<?php if ($recordData['cwp_sims_access_travel_auth_sedl'][0] == 'Yes') { ?><p><a href="menu_travel_admin_sedl.php">Travel Authorizations</a><?php }?>
								
								</font>
<!-- ADD: Leave Requests admin; Time & Leave: Summary reports; Manage CONUS rates; Manage inventory -->								
						</td></tr>

<?php } ?>


<?php if ($recordData['cwp_sims_access_purch_req_bgt_auth'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">Purchasing</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
						
								<font face="verdana, helvetica, arial" color="999999">
								<a href="menu_po_ba.php">Approve purchase requisitions</a><p>
								</font>
								
						</td></tr>

<?php } ?>

						
					
						
						
					</table>
				
					
				</td><td valign="top" width="50%">	
						
						<table cellpadding=4 cellspacing=0 width="100%" valign="top" class="sims">
						
					
<!--
###################################################################################
###################################################################################
############ PERSONNEL TOOLS ######################################################
###################################################################################
###################################################################################
-->


						<tr><td bgcolor="#003745" class="body" nowrap><span class="head2">Personnel Tools</span></td></tr>
						
						
<?php if ($recordData['cwp_sims_access_pos_descr'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">Position Descriptions</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
							<font face="verdana, helvetica, arial" color="999999">
							<a href="staff_pos_descr.php?action=show_mine&staff_ID=<?php echo $recordData['staff_ID'][0];?>">My position descriptions</a><p>
							
							</font>
						</td></tr>

<?php } ?>

<?php if ($recordData['cwp_sims_access_plan_agrmt'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">Performance Appraisals</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
							<font face="verdana, helvetica, arial" color="999999">
							<a href="staff_plan_agrmt.php?action=show_mine&staff_ID=<?php echo $recordData['staff_ID'][0];?>">My performance appraisals</a><p>
							
							</font>
						</td></tr>

<?php } ?>

<?php if ($recordData['cwp_sims_access_staff_profiles'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">Staff Profiles</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
							<font face="verdana, helvetica, arial" color="999999">
							<a href="staff_profiles.php?action=new">New employee setup</a><br>
							<a href="staff_profiles.php?action=show_all">Edit staff profiles</a><br>
							<?php if ($recordData['cwp_sims_access_staff_profiles_com'][0] == 'Yes') { ?>
							<a href="staff_profiles_com.php?action=show_all">Edit staff profiles (communications staff)</a><br><?php }?>
							<a href="staff_profiles.php?action=show_mine&staff_ID=<?php echo $recordData['staff_ID'][0];?>">My staff profile</a>
							<br><a href="/cgi-bin/mysql/staff/change_password.cgi">Change SIMS password</a><p>
							</font>
						</td></tr>

<?php }else{ ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">Staff Profiles</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
							<font face="verdana, helvetica, arial" color="999999">
							<?php if ($recordData['cwp_sims_access_staff_profiles_com'][0] == 'Yes') { ?>
							<a href="staff_profiles_com.php?action=show_all">Edit staff profiles (communications staff)</a><br><?php }?>
							<a href="staff_profiles.php?action=show_mine&staff_ID=<?php echo $recordData['staff_ID'][0];?>">Show my profile</a><br>
							<a href="/cgi-bin/mysql/staff/change_password.cgi">Change SIMS password</a><p>
							</font>
						</td></tr>

<?php } ?>

<?php if ($recordData['cwp_sims_access_sedl_clients'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">SEDL Clients Database</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
							<font face="verdana, helvetica, arial" color="999999">
							Search clients database<br>
							Show my preferred clients list<p>
							</font>
						</td></tr>

<?php } ?>

<?php if ($recordData['cwp_sims_access_ofts_job_postings'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">SEDL Job Postings</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
							<font face="verdana, helvetica, arial" color="999999">
							<?php if ($recordData['cwp_sims_access_resumes_novs_mgr'][0] == 'Yes') { ?>
							<a href="menu_positions_novs.php">Manage resumes and applications</a><br><?php }?>
							<?php if ($recordData['cwp_sims_access_resumes_novs_admin'][0] == 'Yes') { ?>
							<a href="menu_positions_novs_aa.php">View resumes and applications</a><br><?php }?>
							<a href="http://www.sedl.org/about/careers.html" target="_blank">Show current job postings</a><p>
							</font>
						</td></tr>

<?php } ?>

<?php if ($recordData['cwp_sims_access_ofts_purchase_orders'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">Purchase Orders</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
							<font face="verdana, helvetica, arial" color="999999">
							Create a new purchase order<br>
							Show current purchase orders<br>
							Manage vendors<p>
							</font>
						</td></tr>

<?php } ?>
						
<?php if ($recordData['cwp_sims_access_sedl_app'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">SEDL iOS App Links</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
							<font face="verdana, helvetica, arial" color="999999">
							<a href="SEDL_app/SEDL_app.fmp12">Download the SEDL app</a> <br>(iOS devices only - FM Go 12 is required to run the SEDL app)<p>
							</font>
						</td></tr>

<?php } ?>
						
						
						
<?php if ($recordData['cwp_sims_access_chps_admin'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">CHPS Admin</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
						
								<font face="verdana, helvetica, arial" color="999999">
								<?php if($recordData['cwp_sims_access_chps_project_planning'][0] == 'Yes'){?><a href="menu_dev_projects.php">Project planning admin</a><br><? }?>
								<?php if($recordData['cwp_sims_access_chps_hire_us_today_requests'][0] == 'Yes'){?><a href="menu_chps_requests.php">Custom session requests</a><br><? }?><p>
								</font>
								
						</td></tr>

<?php } ?>
						
<?php if ($recordData['cwp_sims_access_dev_admin'][0] == 'Yes') { ?>

						<tr><td bgcolor="#ecf0b1" class="body" nowrap><span class="head3">SEDL Development</span></td></tr>
						<tr><td class="body" valign="top" nowrap>
						
								<font face="verdana, helvetica, arial" color="999999">
								<?php if($recordData['cwp_sims_access_dev_project_planning'][0] == 'Yes'){?><a href="menu_dev_projects.php">Funding opportunity tracker</a><br><? }?>
								</font>
								
						</td></tr>

<?php } ?>
						
						
						
					
						
						
					</table>
					
					
					</td></tr>
			
			
			
			
			
				
				
				
				
				
				
				
				
				
				
				
				</table></center>
				
		</td></tr>
		<tr><td>
		
		
		
		
		<tr><td colspan="2" valign="top" bgcolor="#a2c7ca"><center>For technical assistance with SIMS, contact <a href="mailto:sims@sedl.org">SIMS@sedl.org</a></center></td></tr>
		
		
		
		</td></tr>
	</table>
		

</table>
</td></tr>




	

</body>

</html>

<?php 
################################
## START: SET LOGIN TIMESTAMP ##
################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','staff');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);
$update -> AddDBParam('last_login_trigger',$rand_num);

$updateResult = $update -> FMEdit();
##############################
## END: SET LOGIN TIMESTAMP ##
##############################

?>

<?php
} else {
?>

<!--###DISPLAY MESSAGE IF NO RECORDS FOUND###-->


<html>
<head>
<title>Invalid Login</title>
<link href="../txcc.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="EBEBEB">

<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">

		
		<tr><td>&nbsp;</td</tr>
		<tr><td align="center" class="body"><font face="verdana, helvetica, arial">Not a valid login. | <a href="http://www.sedl.org/staff">Try Again</a><p>&nbsp;<br>&nbsp;<br>&nbsp;</font></td></tr>
		
		

</table>


</body>

</html>


<?php 
session_destroy();

} ?>


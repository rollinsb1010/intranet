<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2013 by SEDL
#
# Written by Eric Waters September 2013
#############################################################################

###############################
## START: LOAD FX.PHP INCLUDES
###############################
include_once('FX/FX.php');
include_once('FX/server_data.php');
###############################
## END: LOAD FX.PHP INCLUDES
###############################

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
$_SESSION['full_name'] = $recordData['c_full_name_first_last'][0];

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
echo 'Your account does not have access to SIMS. Please contact <a href="mailto:maria.turner@sedl.org">Maria Turner</a> in Administrative Services for more information.<p>
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
<link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/grid.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />

<script type="text/javascript" src="js/mootools-1.2.1-core.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="js/mootools-fluid16-autoselect.js"></script>

</head>

<body>
<div class="container_16">

<?php include_once('http://www.sedl.org/staff/sims/includes/sims_header_2013.html');?>

<!--
###################################################################################
###################################################################################
############ ADMINISTRATIVE TOOLS #################################################
###################################################################################
###################################################################################
-->

<div class="grid_16" style="position:relative">

<div class="nav" style="color:#0033ff;background-color:#ffffff;float:right;margin:12px;padding:4px 6px 2px 6px" nowrap>

	<a href="mailto:sims@sedl.org" title="Questions or suggestions">Questions/Comments</a> | 
	<a href="/cgi-bin/mysql/staff/change_password.cgi" title="Change your SIMS password">Change SIMS password</a>

</div>

<h2 id="page-heading">Main Menu</h2>
</div>

<div class="clear"></div>

<div class="grid_4">
				<div class="box menu">
					<h2>
						<a href="#" id="toggle-section-menu">Administrative Tools</a>
					</h2>
					<div class="block" id="section-menu">
						<ul class="section menu">
							<li>
								<a class="menuitem">My Time & Leave</a>
								<ul class="submenu">
									<li>
										<a class="active" href="menu_timesheets.php?sortfield=c_PayPeriodEnd">Timesheets</a>
									</li>
									<li>
										<a href="bgt_code_report_staff.php?action=new">Time/budget reports</a>
									</li>
									<li>
										<a href="menu_leave.php">Leave requests</a>
									</li>
									<li>
										<a href="/staff/personnel/leavereport.cgi" target="_blank">Leave accrual report</a>
									</li>
									<li>
										<a href="my_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>">Leave calendar</a>
									</li>
									<li>
										<a href="menu_paystubs.php">Paystubs</a>
									</li>
									<li>
										<a href="benefits.php?id=<?php echo $recordData['staff_ID'][0];?>" target="_blank">Benefits report</a>
									</li>
								</ul>
							</li>
							<li>
								<a class="menuitem">Workgroup Admin</a>
								<ul class="submenu">
									<li>
										<a href="menu_timesheets_ar_admin.php">Timesheets</a>
									</li>
									<li>
										<a href="menu_leave_ar.php">Leave requests</a>
									</li>
									<li>
										<a href="staff_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>"><?php echo $recordData['primary_SEDL_workgroup'][0];?> leave calendar</a>
									</li>
									<li>
										<a href="menu_pos_descr_admin.php">Position descriptions</a>
									</li>
									<li>
										<a href="menu_plan_agrmt_admin.php">Performance appraisals</a>
									</li>
									<li>
										<a href="menu_personnel_actions_wg_admin.php">Personnel actions</a>
									</li>
									<li>
										<a href="menu_personnel_actions_wg_temp.php">Personnel actions (temp)</a>
									</li>
									<li>
										<a href="menu_travel_admin.php">Travel authorizations</a>
									</li>
								</ul>
							</li>
							<li>
								<a class="menuitem">Supervisors</a>
								<ul class="submenu">
									<li>
										<a href="menu_timesheets_spvsr_admin.php">Sign timesheets</a>
									</li>
									<li>
										<a href="menu_leave_spvsr.php">Sign leave requests</a>
									</li>
									<li>
										<a href="staff_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>"><?php echo $recordData['primary_SEDL_workgroup'][0];?> leave calendar</a>
									</li>
									<li>
										<a href="menu_plan_agrmt_admin.php">Workgroup performance appraisals</a>
									</li>
									<li>
										<a href="menu_pos_descr_admin.php">Workgroup position descriptions</a>
									</li>
								</ul>
							</li>
							<li>
								<a class="menuitem">Budget Authorities</a>
								<ul class="submenu">
									<li>
										<a href="menu_timesheets_ba_admin.php">Approve timesheets</a>
									</li>
									<li>
										<a href="menu_leave_ba.php">Approve leave requests</a>
									</li>
									<li>
										<a href="staff_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>"><?php echo $recordData['primary_SEDL_workgroup'][0];?> leave calendar</a>
									</li>
									<li>
										<a href="menu_plan_agrmt_admin.php">Workgroup performance appraisals</a>
									</li>
									<li>
										<a href="menu_pos_descr_admin.php">Workgroup position descriptions</a>
									</li>
									<li>
										<a href="menu_personnel_actions_ba_admin.php">Workgroup personnel actions</a>
									</li>
									<li>
										<a href="menu_sig_verification_ba.php">eSignature verification</a>
									</li>
									<li>
										<a href="menu_po_ba.php">Approve purchase requisitions</a>
									</li>
									<li>
										<a href="menu_travel_ba.php">Approve travel authorizations</a>
									</li>
									
								</ul>
							</li>
							<li>
								<a class="menuitem">SEDL Admin</a>
								<ul class="submenu">
									<li>
										<a href="menu_timesheets_ofts_admin.php">Staff timesheets</a>
									</li>
									<li>
										<a href="menu_leave_ofts_admin.php">Staff leave requests</a>
									</li>
									<li>
										<a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode&pref=active_only">Budget codes admin</a>
									</li>
									<li>
										<a href="menu_plan_agrmt_admin.php">Staff performance appraisals</a>
									</li>
									<li>
										<a href="menu_pos_descr_admin.php">Staff position descriptions</a>
									</li>
									<li>
										<a href="menu_personnel_actions_admin.php?action=show_all">Staff personnel actions</a>
									</li>
									<li>
										<a href="menu_personnel_actions_temp_admin.php?action=show_all">Temp personnel actions</a>
									</li>
									<li>
										<a href="menu_paystubs_admin.php?action=show_all">Paystubs admin</a>
									</li>
									<li>
										<a href="menu_benefits_admin.php?action=show_all">Benefits admin</a>
									</li>
									<li>
										<a href="menu_sig_verification.php?action=show_all">eSignature verification admin</a>
									</li>
								</ul>
							</li>
							<li>
								<a class="menuitem">My Travel</a>
								<ul class="submenu">
									<li>
										<a href="menu_travel.php">Travel requests</a>
									</li>
									<li>
										<a href="menu_travel.php">Travel reimbursement form</a>
									</li>
									<li>
										<a href="menu_travel.php">Lookup CONUS rates</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
</div>

<!--
###################################################################################
###################################################################################
################# PERSONNEL TOOLS #################################################
###################################################################################
###################################################################################
-->
<div class="grid_4">
				<div class="box menu">
					<h2>
						<a href="#" id="toggle-section-menu">Personnel Tools</a>
					</h2>
					<div class="block" id="section-menu">
						<ul class="section menu">
							<li>
								<a class="menuitem">My Planning Documents</a>
								<ul class="submenu">
									<li>
										<a class="active" href="staff_pos_descr.php?action=show_mine&staff_ID=<?php echo $recordData['staff_ID'][0];?>">Position descriptions</a>
									</li>
									<li>
										<a href="staff_plan_agrmt.php?action=show_mine&staff_ID=<?php echo $recordData['staff_ID'][0];?>">Planning agreements</a>
									</li>
									<li>
										<a href="staff_plan_agrmt.php?action=show_mine&staff_ID=<?php echo $recordData['staff_ID'][0];?>">Performance appraisals</a>
									</li>
									<li>
										<a href="personnel_actions.php">Personnel actions</a>
									</li>
								</ul>
							</li>
							<li>
								<a class="menuitem">Staff Profiles</a>
								<ul class="submenu">
									<li>
										<a href="staff_profiles.php?action=new">New employee setup</a>
									</li>
									<li>
										<a href="staff_profiles.php?action=show_all">Staff profiles</a>
									</li>
									<li>
										<a href="staff_profiles_com.php?action=show_all">Staff profiles COM</a>
									</li>
									<li>
										<a href="staff_profiles.php?action=show_mine&staff_ID=<?php echo $recordData['staff_ID'][0];?>">My staff profile</a>
									</li>
								</ul>
							</li>
							<li>
								<a class="menuitem">SEDL Job Postings</a>
								<ul class="submenu">
									<li>
										<a href="menu_positions_novs.php">Resumes & applications</a>
									</li>
									<li>
										<a href="http://www.sedl.org/about/careers.html" target="_blank">Current job postings</a>
									</li>
								</ul>
							</li>
							<li>
								<a class="menuitem">SEDL Development</a>
								<ul class="submenu">
									<li>
										<a href="menu_dev_projects.php">Funding opportunity tracker</a>
									</li>
								</ul>
							</li>
							<li>
								<a class="menuitem">CHPS Admin</a>
								<ul class="submenu">
									<li>
										<a href="menu_chps_requests.php">Custom session requests</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
</div>


<div class="grid_8">
	<div class="box">
		<h2>
			<a href="#" id="toggle-accordion">MESSAGE CENTER</a>
		</h2>
		<div class="block" id="accordion">
			<div id="accordion">

				<h3 class="toggler atStart" style="position:relative;margin-bottom:6px"><div class="noti_bubble">6</div>My Notifications</h3>
				<div class="element atStart">
				
						<table summary="List of notifications for SEDL staff members">
							
							<colgroup>
								<col class="colA" />
								<col class="colB" />
								<col class="colC" />
							</colgroup>
							<thead>
								<tr>
									<th>Message</th>
									<th>Date posted</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr class="odd">
									<td style="width:100%">1.) Please submit your timesheet - Due: 9/13/13</td>
									<td nowrap>09/01/2013 03:25:35</td>
									<td style="text-align:right"><img src="images/red_delete.png" style="border:0px"></td>
								</tr>
								<tr>
									<td>2.) eSignature verification form due for August 2013.</td>
									<td nowrap>09/05/2013 01:00:02</td>
									<td style="text-align:right"><img src="images/red_delete.png" style="border:0px"></td>
								</tr>
								<tr class="odd">
									<td>3.) 4 timesheets pending your approval.</td>
									<td nowrap>09/03/2013 03:25:35</td>
									<td style="text-align:right"><img src="images/red_delete.png" style="border:0px"></td>
								</tr>
								<tr>
									<td>4.) 7 leave requests pending your approval.</td>
									<td nowrap>09/03/2013 03:25:35</td>
									<td style="text-align:right"><img src="images/red_delete.png" style="border:0px"></td>
								</tr>
								<tr class="odd">
									<td>5.) 2 travel authorizations pending your approval.</td>
									<td nowrap>09/02/2013 03:25:35</td>
									<td style="text-align:right"><img src="images/red_delete.png" style="border:0px"></td>
								</tr>
								<tr class="odd">
									<td>6.) 5 purchase requisitions pending your approval.</td>
									<td nowrap>09/07/2013 03:25:35</td>
									<td style="text-align:right"><img src="images/red_delete.png" style="border:0px"></td>
								</tr>
							</tbody>
						</table>

				</div>

				<h3 class="toggler atStart">Timesheet Instructions</h3>
				<div class="element atStart">
					<p>Timesheets must be submitted by the due date for each pay period. Exempt staff submit one timesheet per monthly pay period. Non-exempt staff submit one timesheet per bi-monthly pay period (twice per month). Each pay period, staff are notified by email of timesheet due dates by SEDL's Administrative Services. Timesheets must be submitted by the due date in order for payroll to be processed. After your timesheet has been submitted, you may adjust and re-submit your timesheet at any time until the pay period lockout date, which is usually a few days after the end of the pay period.</p>
				</div>

				<h3 class="toggler atStart">Leave Request Instructions</h3>
				<div class="element atStart">
					<p>Leave requests should be submitted and approved by your supervisor prior to the beginning date of the leave you are requesting. To review your accrued leave hours, select the "Accrual" option in the My Time & Leave menu. </p>
				</div>

				<h3 class="toggler atStart">Planning Agreement Instructions</h3>
				<div class="element atStart">
					<p>Each staff member should complete a planning agreement document with their supervisor. This document describes the major tasks, performance expectations, and/or accommodations required for your position at SEDL. Both the supervisor and staff member sign the agreement at the beginning of the agreed upon performance period. To access your planning agreement, select the appropriate link from the My Planning Documents section.</p>
				</div>

				<h3 class="toggler atStart">Performance Appraisal Instructions</h3>
				<div class="element atStart">
					<p>At the end of the performance period indicated on the planning agreement, each staff member should schedule a meeting to complete a performance appraisal with their supervisor. This document provides staff members the opportunity to assess their performance relating to the indicators set forth in the planning agreement. Both the supervisor and staff member provide feedback and sign the appraisal forms at the end of the performance period set forth in the planning agreement. To access your performance appraisal documents, select the appropriate link from the My Planning Documents section.</p>
				</div>

				<h3 class="toggler atStart">SIMS - Frequently Asked Questions</h3>
				<div class="element atStart">
					<ul>
						<li>
							<a href="#">Are the Planning Agreement and the Performance Appraisal the same document or seperate documents?</a>
						</li>
						<li>
							<a href="#">Why are my leave request hours not automatically appearing on my timesheet?</a>
						</li>
						<li>
							<a href="#">Can I use a single leave request for multiple unrelated leave days?</a>
						</li>
					</ul>
				</div>

			</div>
		</div>
	</div>
</div>







	
</div><!--END container_16-->

<div class="container_16" style="text-align:center"><hr style="padding:0px">For technical assistance contact <a href="mailto:sims@sed.org">sims@sedl.org</a>.
</div>
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
		<tr><td align="center" class="body"><font face="verdana, helvetica, arial">Not a valid login. | <a href="<?php echo $_SESSION['login_url'] ?>">Try Again</a><p>&nbsp;<br>&nbsp;<br>&nbsp;</font></td></tr>
		
		

</table>


</body>

</html>


<?php 
session_destroy();

} ?>


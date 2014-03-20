<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


####################################################
## START: FIND PLANNING AGREEMENTS FOR THIS ADMIN ##
####################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('immediate_supervisor_sims_user_ID',$_SESSION['user_ID']);
$search -> AddDBParam('current_employee_status','SEDL Employee');
$search -> AddDBParam('sims_user_ID',$_SESSION['user_ID'],'neq');

$search -> AddSortParam('c_full_name_last_first','ascend');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['travel_request_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
############################################
## END: FIND PLANNING AGREEMENTS FOR THIS ADMIN ##
############################################

####################################################
## START: FIND OTHER PLANNING AGREEMENTS FOR THIS ADMIN ##
####################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff','all');
$search2 -> SetDBPassword($webPW,$webUN);
//$search2 -> FMSkipRecords($skipsize);
$search2 -> AddDBParam('c_plan_agrmt_access_list',$_SESSION['user_ID']);
$search2 -> AddDBParam('current_employee_status','SEDL Employee');
//$search2 -> AddDBParam('sims_user_ID',$_SESSION['user_ID'],'neq');
$search2 -> AddDBParam('immediate_supervisor_sims_user_ID',$_SESSION['user_ID'],'neq');

$search2 -> AddSortParam('c_full_name_last_first','ascend');

$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
//print_r ($searchResult2);
$recordData2 = current($searchResult2['data']);
//$_SESSION['travel_request_approval_not_required'] = $recordData2['staff::no_time_leave_approval_required'][0];
############################################
## END: FIND OTHER PLANNING AGREEMENTS FOR THIS ADMIN ##
############################################


//$current_pay_period = date("m").'/'.date("t").'/'.date("Y");

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: Workgroup Performance Appraisals</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">


function zoomWindow() {
window.resizeTo(1000,screen.height)
}
</script>

<script language="JavaScript" type="text/JavaScript">
//<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Performance Appraisals: Workgroup Admin</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b>Workgroup Admin: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<?php if($_SESSION['travel_request_submitted_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your travel request has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['travel_request_submitted_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['travel_request_submitted_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your travel request, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance. | ErrorCode: <?php echo $_SESSION['errorCode'];?></p></td></tr>
				<?php $_SESSION['travel_request_submitted_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['travel_prefs_updated'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your travel preferences have been updated.</p></td></tr>
				<?php $_SESSION['travel_prefs_updated'] = ''; ?>

			<?php } elseif($_SESSION['travel_prefs_updated'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem updating your travel preferences, please contact <a href="mailto:sims@sedl.org">technical support</a>.</p></td></tr>
				<?php $_SESSION['travel_prefs_updated'] = ''; ?>

			<?php } elseif($_SESSION['travel_authorization_submitted_admin'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">The travel authorization has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></p></td></tr>
				<?php $_SESSION['travel_authorization_submitted_admin'] = ''; ?>

			<?php } elseif($_SESSION['travel_authorization_submitted_admin'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting the travel authorization, please contact <a href="mailto:sims@sedl.org">technical support</a>.</p></td></tr>
				<?php $_SESSION['travel_authorization_submitted_admin'] = ''; ?>

			<?php } ?>
			
			
			<tr><td colspan="2">
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="#ebebeb" bgcolor="#ffffff" width="100%" class="sims">
						
						<tr>
							<td colspan="7" class="body" bgcolor="ebebeb"><?php echo $_SESSION['workgroup'];?> Performance Appraisals / staff you supervise (<?php echo $searchResult['foundCount']; ?>)</td>
						</tr>

						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body">Name</td>
						<td class="body">Title</td>
						<td class="body">Workgroup</td>
						<td class="body">Supervisor</td>
						<td class="body">Bgt Auth</td>
						<td class="body" align="right">Last Performance Period</td>
	
						</tr>
						
<?php if($searchResult['foundCount'] > 0){ ?>

						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>

						<td class="body"><?php echo $searchData['staff_ID'][0];?></td>
						<td class="body"><a href="staff_plan_agrmt_admin.php?action=show_mine&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo $searchData['c_full_name_last_first'][0];?></a></td>
						<td class="body"><?php echo $searchData['job_title'][0];?></td>
						<td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td>
						<td class="body"><?php echo $searchData['immediate_supervisor_sims_user_ID'][0];?></td>
						<td class="body"><?php echo $searchData['bgt_auth_primary_sims_user_ID'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['planning_agreements_active_only::Performance_Period'][0];?></td>
						
						</tr>
			
						<?php } ?>

<?php } else { ?>


						<tr>
						<td class="body" colspan="7" height="40" align="center">No records found.</td>
						</tr>


<?php } ?>


						<tr>
							<td colspan="7" class="body" bgcolor="ebebeb" valign="bottom" height="50">Other Performance Appraisals requiring your review / approval (<?php echo $searchResult2['foundCount']; ?>)</td>
						</tr>

						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body">Name</td>
						<td class="body">Title</td>
						<td class="body">Workgroup</td>
						<td class="body">Supervisor</td>
						<td class="body">Bgt Auth</td>
						<td class="body" align="right">Last Performance Period</td>
	
						</tr>
						
<?php if($searchResult2['foundCount'] > 0){ ?>

						<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
						
						<tr>

						<td class="body"><?php echo $searchData2['staff_ID'][0];?></td>
						<td class="body"><a href="staff_plan_agrmt_admin.php?action=show_mine&staff_ID=<?php echo $searchData2['staff_ID'][0];?>"><?php echo $searchData2['c_full_name_last_first'][0];?></a></td>
						<td class="body"><?php echo $searchData2['job_title'][0];?></td>
						<td class="body"><?php echo $searchData2['primary_SEDL_workgroup'][0];?></td>
						<td class="body"><?php echo $searchData2['immediate_supervisor_sims_user_ID'][0];?></td>
						<td class="body"><?php echo $searchData2['bgt_auth_primary_sims_user_ID'][0];?></td>
						<td class="body" align="right"><?php echo $searchData2['planning_agreements_active_only::Performance_Period'][0];?></td>
						
						</tr>
			
						<?php } ?>

<?php } else { ?>


						<tr>
						<td class="body" colspan="7" height="40" align="center">No records found.</td>
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

<!--No records found.-->

<?php //} ?>

<?php //} else { ?>



<?php //} ?>
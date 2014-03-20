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
## START: FIND POSITION DESCRIPTIONS FOR THIS ADMIN ##
####################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('c_pos_descr_access_list',$_SESSION['user_ID']);
$search -> AddDBParam('current_employee_status','SEDL Employee');
//$search -> AddDBParam('sims_user_ID',$_SESSION['user_ID'],'neq');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('c_full_name_last_first','ascend');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['travel_request_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
############################################
## END: FIND POSITION DESCRIPTIONS FOR THIS ADMIN ##
############################################


//$current_pay_period = date("m").'/'.date("t").'/'.date("Y");

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: Workgroup Position Descriptions</title>
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Position Descriptions: Workgroup Admin</h1><hr /></td></tr>
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
<form name="form1" method="post">			
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="#ebebeb" bgcolor="#ffffff" width="100%" class="sims">
						
						<tr>
							<td colspan="7" class="body" bgcolor="ebebeb"><?php echo $_SESSION['workgroup'];?> Position Descriptions (<?php echo $searchResult['foundCount']; ?>)</td>
						</tr>

</form>						
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body">Name</td>
						<td class="body">Title</td>
						<td class="body">Workgroup</td>
						<td class="body">Supervisor</td>
						<td class="body">Bgt Auth</td>
						<td class="body" align="right">Last Pos. Descr.</td>
	
						</tr>
						
<?php if($searchResult['foundCount'] > 0){ ?>

						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>

						<td class="body"><?php echo $searchData['staff_ID'][0];?></td>
						<td class="body"><a href="staff_pos_descr_admin.php?action=show_mine&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo $searchData['c_full_name_last_first'][0];?></a></td>
						<td class="body"><?php echo $searchData['job_title'][0];?></td>
						<td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td>
						<td class="body"><?php echo $searchData['immediate_supervisor_sims_user_ID'][0];?></td>
						<td class="body"><?php echo $searchData['bgt_auth_primary_sims_user_ID'][0];?></td>
						<td class="body" align="right"><?php echo $searchData['c_last_pos_descr'][0];?></td>
						
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
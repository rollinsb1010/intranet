<?php
session_start();

include_once('sims_checksession.php');
if($_SESSION['user_ID'] == ''){
header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
exit;
}

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];


if(($_SESSION['user_ID'] == '')||(!isset($_SESSION['user_ID']))){
header('Location: http://www.sedl.org/staff/');
exit;
}
//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');

//$_SESSION['menu_type'] == 'ba_admin';

$action = $_GET['action'];
$sortfield = $_GET['sortfield'];
$sortorder = $_GET['sortorder'];

//$filter = $_GET['filter'];
$project_ID = $_GET['pos_id'];
$status = $_GET['status'];
//echo '$status: '.$status;

$displaynum = $_GET['displaynum'];
if($displaynum == ''){
$displaynum = 100;
}

if($sortfield == ''){
$sortfield = 'status';
$sortorder = 'descend';
}

if($project_ID !== ''){
$_SESSION['project_ID'] = $project_ID;
}

if($_SESSION['project_ID'] == ''){
$_SESSION['project_ID'] = 'all';
}

if($status !== ''){
$_SESSION['status'] = $status;
}

if($_SESSION['status'] == ''){
$_SESSION['status'] = 'all';
}

//$mod = $_GET['mod'];

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
if($action == ''){ // SHOW APPLICATIONS FOR NOVs (APPs)


################################
## START: GET FMP VALUE-LISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('admin_base.fp7','projects_detail');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
##############################
## END: GET FMP VALUE-LISTS ##
##############################


####################################################################
## START: FIND PROJECTS TO REVIEW FOR THIS REVIEWER ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('dev_base.fp7','projects_detail',$displaynum);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);

$search -> AddDBParam('c_proj_access_search_target',$_SESSION['user_ID']);
$search -> AddDBParam('status','No go','neq');


//$search -> AddDBParam('c_nov_mgrs_access_search_target',$_SESSION['user_ID']);
//$search -> AddDBParam('positions_NOVs::c_managers_can_view',$_SESSION['user_ID']);

//if($_SESSION['project_ID'] !== 'all'){
//$search -> AddDBParam('project_ID',$project_ID);
//}

//if($status == ''){
//$search -> AddDBParam('nov_status','No to applicant','neq');
//}else{
//$search -> AddDBParam('nov_status',$status);
//}

//if($sortfield !== 'nov_status'){
//$search -> AddSortParam('nov_status','descend');
//}

if($sortorder == 'descend'){
$search -> AddSortParam($sortfield,'descend');
}else{
$search -> AddSortParam($sortfield,'ascend');
}

//$search -> AddSortParam('DATE_OF_ORDER','descend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//echo '$_SESSION[project_ID]: '.$_SESSION['project_ID'];
##################################################################
## END: FIND PROJECTS TO REVIEW FOR THIS REVIEWER ##
##################################################################

/*
####################################################################
## START: FIND OPEN POSITIONS FOR DROP-DOWN MENU ##
####################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('admin_base.fp7','positions_NOVs','all');
$search2 -> SetDBPassword($webPW,$webUN);
//$search2 -> FMSkipRecords($skipsize);

$search2 -> AddDBParam('position_status','Open');
$search2 -> AddDBParam('c_managers_can_view_filter',$_SESSION['user_ID']);

$search2 -> AddSortParam('position_title','ascend');


$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//print_r ($searchResult2);
$recordData2 = current($searchResult2['data']);
##################################################################
## END: FIND OPEN POSITIONS FOR DROP-DOWN MENU ##
##################################################################
*/
//$_SESSION['pr_rejected']
//$pending_app_count = 0; // SET PENDING APP COUNTER
/*
if($searchResult['foundCount'] > 0) { // PRs WERE FOUND FOR THIS BA
	foreach($searchResult['data'] as $key => $searchData) { // LOOP THROUGH PRs COUNTING PENDING ONES
		if(strpos($searchData['c_ba_signers_remaining'][0], $_SESSION['user_ID']) === false){}else{ // THE BA HAS NOT APPROVED THE PR
		$pending_pr_count++; // INCREMENT PENDING PR COUNTER
		}
	}	
}
*/
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: Project Planning</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>Funding Opportunity Tracker: Reviewer Admin</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b>Reviewer: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="documentation/dev_fundopp_tracker_process.pdf">Process Flowchart</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<?php if($_SESSION['action_logged'] == '1'){ ?>
			
				<tr><td colspan="2">
					<p class="alert_small"><b>Reviewer: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - Your response has been successfully submitted to SIMS.</p>
				</td></tr>

			<?php $_SESSION['action_logged'] = ''; } ?>

			<?php if($_SESSION['action_logged'] == '2'){ ?>
			
				<tr><td colspan="2">
					<p class="alert_small"><b>Reviewer: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - There was an error submitted your response to SIMS. Contact technical assistance at <a href="sims@sedl.org">sims@sedl.org</a>.</p>
				</td></tr>

			<?php $_SESSION['action_logged'] = ''; } ?>

			<tr><td colspan="2">
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="ebebeb" bgcolor="ffffff" width="100%" class="sims">
						
						<tr>
							<td colspan="8" class="body" bgcolor="ebebeb">DEV Projects Pending Your Review (<?php echo $searchResult['foundCount']; ?>)</td>
						</tr>
						
						<tr bgcolor="#a2c7ca">
						
						<td nowrap>ID</td>
						<td nowrap><a href="menu_dev_projects.php?sortfield=project_name&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['project_ID'];?>">Project Name</a></td>
						<td nowrap><a href="menu_dev_projects.php?sortfield=awarding_agency&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['project_ID'];?>">Awarding Agency</a></td>
						<td nowrap><a href="menu_dev_projects.php?sortfield=project_duration&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['project_ID'];?>">Duration</a></td>
						<td nowrap><a href="menu_dev_projects.php?sortfield=award_type&sortorder=descend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['project_ID'];?>">Award Type</a></td>
						<td nowrap><a href="menu_dev_projects.php?sortfield=funds_per_proj_year&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['project_ID'];?>">Funds/Yr</a></td>
						<td nowrap><a href="menu_dev_projects.php?sortfield=possible_total_value&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['project_ID'];?>">Total $</a></td>
						<td nowrap align="right"><a href="menu_dev_projects.php?sortfield=status&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['project_ID'];?>">Status</a></td>

						</tr>
						
						<?php if($searchResult['foundCount'] > 0) { $i=1;?>
						
							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							
								<tr>
								<td style="vertical-align:text-top"><a href="dev_projects.php?id=<?php echo $searchData['project_ID'][0];?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['project_ID'];?>" name="<?php echo $searchData['project_ID'][0];?>"><?php echo $searchData['project_ID'][0];?></a><br><span class="tiny" style="color:#666666"><?php echo $i;?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['project_name'][0];?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['awarding_agency'][0];?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['project_duration'][0];?></td>
								<td style="vertical-align:text-top" nowrap><?php echo $searchData['award_type'][0];?></td>
								<td style="vertical-align:text-top" nowrap><?php echo $searchData['funds_per_proj_year'][0];?></td>
								<td style="vertical-align:text-top" nowrap><?php echo $searchData['possible_total_value'][0];?></td>

								<td style="vertical-align:text-top" align="right"><font color="red"><?php echo $searchData['status'][0];?></font></td>
	
								</tr>
					
							<?php $i++;} ?>

								<tr><td colspan="5" style="background-color:#fbf59a"><?php if(($searchResult['foundCount'] < 100)||($i > $searchResult['foundCount'])){/* DON'T DISPLAY ANYTHING */}else{?><a href="menu_dev_projects.php?displaynum=<?php echo $i + 12;?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&pos_id=<?php echo $_SESSION['project_ID'];?>">Show more</a> | <a href="menu_dev_projects.php?displaynum=all&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&pos_id=<?php echo $_SESSION['project_ID'];?>">Show all</a> | <?php } ?><?php if($i > 100){?><a href="menu_dev_projects.php?displaynum=<?php echo $i - 12;?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&pos_id=<?php echo $_SESSION['project_ID'];?>">Show less</a><?php }?></td><td colspan="3" align="right" style="background-color:#000000;text-align:center;color:#ffffff">Showing <?php if($searchResult['foundCount'] < $displaynum){echo $searchResult['foundCount'];}else{echo $displaynum;}?> records</td></tr>

						<?php }else{ ?>

								<tr>
								<td style="vertical-align:text-top;text-align:center" colspan="8">No records found.</td>
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
}else{ ?>

Error

<?php } ?>




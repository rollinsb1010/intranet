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
$position_ID = $_GET['pos_id'];
$status = $_GET['status'];
//echo '$status: '.$status;

$displaynum = $_GET['displaynum'];
if($displaynum == ''){
$displaynum = 100;
}

if($sortfield == ''){
$sortfield = 'nov_status';
$sortorder = 'descend';
}

if($position_ID !== ''){
$_SESSION['position_ID'] = $position_ID;
}

if($_SESSION['position_ID'] == ''){
$_SESSION['position_ID'] = 'all';
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
$v1 -> SetDBData('admin_base.fp7','resume_table');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
##############################
## END: GET FMP VALUE-LISTS ##
##############################


####################################################################
## START: FIND APPLICATIONS TO REVIEW FOR THIS BUDGET AUTHORITY ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('admin_base.fp7','resume_table',$displaynum);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);

$search -> AddDBParam('record_type','Response to NOV');


$search -> AddDBParam('c_nov_admin_access_search_target',$_SESSION['user_ID']);
//$search -> AddDBParam('positions_NOVs::c_managers_can_view',$_SESSION['user_ID']);

if($_SESSION['position_ID'] !== 'all'){
$search -> AddDBParam('position_ID',$position_ID);
}

if($status == ''){
$search -> AddDBParam('nov_status','No to applicant','neq');
}else{
$search -> AddDBParam('nov_status',$status);
}

if($sortfield !== 'nov_status'){
$search -> AddSortParam('nov_status','descend');
}

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
//echo '$_SESSION[position_ID]: '.$_SESSION['position_ID'];
##################################################################
## END: FIND APPLICATIONS TO REVIEW FOR THIS BUDGET AUTHORITY ##
##################################################################


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
<title>SIMS: Resumes and Applications</title>
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Resumes & Applications: Administrator Admin</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b>Admin: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			


<?php if($searchResult2['foundCount'] > 1){?>
				<tr><td colspan="2">
			
				<div style="padding:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">
				<form name="form1" method="post" action="" style="padding:0px;margin:0px">
								<span class="tiny">FILTER BY POSITION:</span> <br>
								<select name="menu1" onChange="MM_jumpMenu('parent',this,0)">
								
								<option value="menu_positions_novs_aa.php?pos_id=all&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&displaynum=<?php echo $displaynum;?>"<?php if($_SESSION['position_ID'] == 'all'){echo 'selected';}?>><< SHOW ALL POSITIONS >></option>

								<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
								
									<option value="menu_positions_novs_aa.php?pos_id=<?php echo $searchData2['position_ID'][0];?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&displaynum=<?php echo $displaynum;?>"<?php if($_SESSION['position_ID'] == $searchData2['position_ID'][0]){echo 'selected';}?>><?php echo $searchData2['c_position_display'][0];?></option>
						
								<?php } ?>
								
								</select>

				</form>	
				</div>
				</td></tr>
<?php }?>


			<tr><td colspan="2">
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="ebebeb" bgcolor="ffffff" width="100%" class="sims">
						
						<tr>
							<td colspan="5" class="body" bgcolor="ebebeb">Current Applications (<?php echo $searchResult['foundCount']; ?>)</td>
							<td colspan="2" class="tiny" bgcolor="ebebeb" style="text-align:center"><strong>PRIORITIES ASSIGNED</strong></td>
							<td class="body" bgcolor="ebebeb" align="right">&nbsp;</td>
						</tr>
						
						<tr bgcolor="#a2c7ca">
						
						<td nowrap>ID</td>
						<td nowrap><a href="menu_positions_novs_aa.php?sortfield=positions_NOVs::c_position_display&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['position_ID'];?>">Position (NOV)</a></td>
						<td nowrap><a href="menu_positions_novs_aa.php?sortfield=resume_last_name&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['position_ID'];?>">Applicant Name</a></td>
						<td nowrap><a href="menu_positions_novs_aa.php?sortfield=resume_city&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['position_ID'];?>">Applicant Location</a></td>
						<td nowrap><a href="menu_positions_novs_aa.php?sortfield=c_nov_date_received_hr&sortorder=descend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['position_ID'];?>">Application Received</a></td>
						<td nowrap><a href="menu_positions_novs_aa.php?sortfield=c_nov_priority_rating_post_resume&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['position_ID'];?>">Post-Resume</a></td>
						<td nowrap>Post-Application</td>
						<td nowrap align="right"><a href="menu_positions_novs_aa.php?sortfield=nov_status&sortorder=descend&displaynum=<?php echo $displaynum;?>">Status</td>

						</tr>
						
						<?php if($searchResult['foundCount'] > 0) { $i=1;?>
						
							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							
								<tr>
								<td style="vertical-align:text-top"><a href="positions_novs_aa.php?id=<?php echo $searchData['resume_ID'][0];?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['position_ID'];?>" name="<?php echo $searchData['resume_ID'][0];?>"><?php echo $searchData['resume_ID'][0];?></a><br><span class="tiny" style="color:#666666"><?php echo $i;?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['positions_NOVs::c_position_display'][0];?></td>
								<td style="vertical-align:text-top" nowrap><?php echo $searchData['resume_first_name'][0].' '.$searchData['resume_last_name'][0];?></td>
								<td style="vertical-align:text-top" nowrap><?php echo $searchData['resume_city'][0].', '.$searchData['resume_state'][0];?></td>
								<td style="vertical-align:text-top" nowrap><?php echo $searchData['c_nov_date_received_hr'][0];?></td>
								<td style="vertical-align:text-top" nowrap><?php echo $searchData['nov_priority_rating_post_resume'][0];?> <span class="tiny" style="color:#666666">(<?php echo $searchData['nov_priority_rating_post_resume_by'][0];?>)</span></td>
								<td style="vertical-align:text-top" nowrap><?php echo $searchData['nov_priority_rating_post_application'][0];?>  <span class="tiny" style="color:#666666">(<?php echo $searchData['nov_priority_rating_post_application_by'][0];?>)</span></td>

								<?php if($searchData['nov_status'][0]=='Pending'){ ?>
								<td style="vertical-align:text-top" align="right" nowrap><font color="red"><?php echo $searchData['nov_status'][0];?></font></td>
								
								<?php }else{ ?>
								<td style="vertical-align:text-top" align="right" nowrap><font color="blue"><?php echo $searchData['nov_status'][0];?></font></td>
								
								<?php } ?>
	
	
								</tr>
					
							<?php $i++;} ?>

								<tr><td colspan="5" style="background-color:#fbf59a"><?php if(($searchResult['foundCount'] < 100)||($i > $searchResult['foundCount'])){/* DON'T DISPLAY ANYTHING */}else{?><a href="menu_positions_novs_aa.php?displaynum=<?php echo $i + 12;?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&pos_id=<?php echo $_SESSION['position_ID'];?>">Show more</a> | <a href="menu_positions_novs_aa.php?displaynum=all&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&pos_id=<?php echo $_SESSION['position_ID'];?>">Show all</a> | <?php } ?><?php if($i > 100){?><a href="menu_positions_novs_aa.php?displaynum=<?php echo $i - 12;?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&pos_id=<?php echo $_SESSION['position_ID'];?>">Show less</a><?php }?></td><td colspan="3" align="right" style="background-color:#000000;text-align:center;color:#ffffff">Showing <?php if($searchResult['foundCount'] < $displaynum){echo $searchResult['foundCount'];}else{echo $displaynum;}?> records</td></tr>

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
}elseif($action == 'urs'){ // SHOW UNSOLICITED RESUMES (URs) 

$displaynum = $_GET['displaynum'];
if($displaynum == ''){
$displaynum = 12;
}

####################################################################
## START: FIND APPLICATIONS TO REVIEW FOR THIS BUDGET AUTHORITY ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('admin_base.fp7','resume_table',$displaynum);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);

$search -> AddDBParam('c_nov_mgrs_sent_to_search_target',$_SESSION['user_ID']);
$search -> AddDBParam('record_type','Unsolicited resume');

$search -> AddSortParam('c_nov_date_received_hr','descend');
//$search -> AddSortParam('DATE_OF_ORDER','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
##################################################################
## END: FIND APPLICATIONS TO REVIEW FOR THIS BUDGET AUTHORITY ##
##################################################################

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
<title>SIMS: Resumes and Applications</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Resumes & Applications: Reviewer Admin</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b>Reviewer: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="menu_positions_novs_aa.php">Show Applications</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
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
							<td colspan="5" class="body" bgcolor="ebebeb">Unsolicited Resumes (<?php echo $searchResult['foundCount']; ?>)</td>
							
						</tr>
						
						<tr bgcolor="#a2c7ca">
						
						<td nowrap>ID</td>
						<td nowrap>Applicant Name</td>
						<td nowrap>Applicant Location</td>
						<td nowrap>Resume Received</td>
						<td nowrap align="right">Status</td>

						</tr>
						
						<?php if($searchResult['foundCount'] > 0) {?>
						
							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							
								<tr>
								<td style="vertical-align:text-top"><a href="positions_novs_ba.php?id=<?php echo $searchData['resume_ID'][0];?>&v=urs"><?php echo $searchData['resume_ID'][0];?></a></td>
								<td style="vertical-align:text-top"><?php echo $searchData['resume_first_name'][0].' '.$searchData['resume_last_name'][0];?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['resume_city'][0].' '.$searchData['resume_state'][0];?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['c_nov_date_received_hr'][0];?></td>

								<?php if($searchData['status'][0]=='Pending'){ ?>
								<td style="vertical-align:text-top" align="right"><font color="red"><?php echo $searchData['status'][0];?></font></td>
								
								<?php }else{ ?>
								<td style="vertical-align:text-top" align="right"><font color="blue"><?php echo $searchData['status'][0];?></font></td>
								
								<?php } ?>
	
	
								</tr>
					
							<?php } ?>

								<tr><td colspan="4" style="background-color:#fbf59a"><?php if($searchResult['foundCount'] < 12){/* DON'T DISPLAY ANYTHING */}else{?><a href="menu_positions_novs_aa.php?action=urs&displaynum=<?php echo $displaynum + 12;?>">Show more</a><?php } ?><?php if($displaynum > 12){?> | <a href="menu_positions_novs_aa.php?action=urs&displaynum=<?php echo $displaynum - 12;?>">Show less</a><?php }?></td><td align="right" style="background-color:#000000;text-align:center;color:#ffffff">Showing <?php if($searchResult['foundCount'] < $displaynum){echo $searchResult['foundCount'];}else{echo $displaynum;}?> records</td></tr>

						<?php }else{ ?>

								<tr>
								<td style="vertical-align:text-top;text-align:center" colspan="5">No records found.</td>
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




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
$request_ID = $_GET['pos_id'];
$request_type = $_GET['request_type'];
//echo '$request_type: '.$request_type;

$displaynum = $_GET['displaynum'];
if($displaynum == ''){
$displaynum = 100;
}

if($sortfield == ''){
$sortfield = 'status';
$sortorder = 'descend';
}

if($request_ID !== ''){
$_SESSION['request_ID'] = $request_ID;
}

if($_SESSION['request_ID'] == ''){
$_SESSION['request_ID'] = 'all';
}

if($request_type !== ''){
$_SESSION['request_type'] = $request_type;
}

if($_SESSION['request_type'] == ''){
$_SESSION['request_type'] = 'Request a Quote';
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
if($action == ''){ // SHOW CHPS SESSION REQUESTS


################################
## START: GET FMP VALUE-LISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('CC_dms.fp7','chps_session_requests');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
##############################
## END: GET FMP VALUE-LISTS ##
##############################


####################################################################
## START: FIND PROJECTS TO REVIEW FOR THIS REVIEWER ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('CC_dms.fp7','chps_session_requests',$displaynum);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);

//$search -> AddDBParam('CHPS_flag','yes');


//$search -> AddDBParam('c_nov_mgrs_access_search_target',$_SESSION['user_ID']);
//$search -> AddDBParam('positions_NOVs::c_managers_can_view',$_SESSION['user_ID']);

//if($_SESSION['request_ID'] !== 'all'){
//$search -> AddDBParam('request_ID',$request_ID);
//}

//if($request_type == ''){
//$search -> AddDBParam('status','No to applicant','neq');
//}else{
$search -> AddDBParam('status','Pending');
$search -> AddDBParam('request_type','CPL - Custom Session', 'neq');
//}

//if($sortfield !== 'nov_status'){
//$search -> AddSortParam('nov_status','descend');
//}

if($sortorder == 'descend'){
$search -> AddSortParam($sortfield,'descend');
}else{
$search -> AddSortParam($sortfield,'ascend');
}
$search -> AddSortParam('creation_timestamp','descend');

//$search -> AddSortParam('DATE_OF_ORDER','descend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//echo '$_SESSION[request_ID]: '.$_SESSION['request_ID'];
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
<title>CHPS: Custom Session Requests</title>
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>CHPS Admin: Custom Session Requests</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b>Reviewer: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
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
							<td colspan="6" class="body" bgcolor="ebebeb"><strong><?php echo $_SESSION['request_type'];?>:</strong> Requests Pending (<?php echo $searchResult['foundCount']; ?>)</td>
						</tr>
						
						<tr bgcolor="#a2c7ca">
						
						<td nowrap>ID</td>
						<td nowrap><a href="menu_chps_requests.php?sortfield=creation_timestamp&sortorder=descend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['request_ID'];?>">Date</a></td>
						<td nowrap><a href="menu_chps_requests.php?sortfield=cpl_cs_name_last&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['request_ID'];?>">Requestor</a></td>
						<td nowrap>Session(s) Requested</td>
						<td nowrap><a href="menu_chps_requests.php?sortfield=cpl_cs_location&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['request_ID'];?>">Where</a></td>
						<td nowrap align="right"><a href="menu_chps_requests.php?sortfield=status&sortorder=ascend&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['request_ID'];?>">Status</a></td>

						</tr>
						
						<?php if($searchResult['foundCount'] > 0) { $i=1;?>
						
							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							
								<tr>
								<td style="vertical-align:text-top" nowrap><span class="tiny" style="color:#666666"><?php echo $searchData['request_ID'][0];?></span></td>
								<td style="vertical-align:text-top" nowrap><?php echo $searchData['creation_timestamp'][0];?><br><span class="tiny" style="color:#666666"><em><?php echo $searchData['request_type'][0];?></em></span></td>
								<td style="vertical-align:text-top" nowrap><a href="chps_requests.php?id=<?php echo $searchData['request_ID'][0];?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $_SESSION['request_ID'];?>" name="<?php echo $searchData['request_ID'][0];?>"><?php echo $searchData['first_name'][0].' '.$searchData['last_name'][0];?></a><br>
								<?php echo $searchData['title'][0];?><br>
								<?php echo $searchData['org'][0];?><br>
								<?php echo $searchData['loc_city'][0].', '.$searchData['loc_state'][0];?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['c_sessions_requested'][0];?></td>
								<td style="vertical-align:text-top" nowrap><?php echo $searchData['location'][0];?></td>

								<td style="vertical-align:text-top" align="right"><font color="red"><?php echo $searchData['status'][0];?></font></td>
	
								</tr>
					
							<?php $i++;} ?>

								<tr><td colspan="4" style="background-color:#fbf59a"><?php if(($searchResult['foundCount'] < 100)||($i > $searchResult['foundCount'])){/* DON'T DISPLAY ANYTHING */}else{?><a href="menu_chps_requests.php?displaynum=<?php echo $i + 12;?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&pos_id=<?php echo $_SESSION['request_ID'];?>">Show more</a> | <a href="menu_chps_requests.php?displaynum=all&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&pos_id=<?php echo $_SESSION['request_ID'];?>">Show all</a> | <?php } ?><?php if($i > 100){?><a href="menu_chps_requests.php?displaynum=<?php echo $i - 12;?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&pos_id=<?php echo $_SESSION['request_ID'];?>">Show less</a><?php }?></td><td colspan="2" align="right" style="background-color:#000000;text-align:center;color:#ffffff">Showing <?php if($searchResult['foundCount'] < $displaynum){echo $searchResult['foundCount'];}else{echo $displaynum;}?> records</td></tr>

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




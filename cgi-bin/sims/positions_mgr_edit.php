<?php
session_start();

include_once('sims_checksession.php');

if($_SESSION['user_ID'] == ''){
header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
exit;
}
//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');



$debug = 'off';


$action = $_GET['action'];
$view = $_GET['v'];

if($action == ''){ 
$action = 'view';
}

$position_ID = $_GET['id'];
$mod = $_GET['mod'];
//$status = $_GET['status'];
$current_id = $_GET['row_id'];


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
if($action == 'view'){ //IF THE USER IS VIEWING THIS JOB APPLICATION


if($mod == '1') { // THE MANAGER SUBMITTED CHANGES

###########################################################################################################
## START: IF OFFER TO EMPLOY WAS MADE, UPDATE THE APPLICANT RECORD ##
###########################################################################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('admin_base.fp7','Position_NOV_detail');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('position_app_pool_count',$_GET['position_app_pool_count']);
$update -> AddDBParam('position_app_pool_count2',$_GET['position_app_pool_count2']);
$update -> AddDBParam('position_hire_timeframe',$_GET['position_hire_timeframe']);
$update -> AddDBParam('position_qualifications_summary',$_GET['position_qualifications_summary']);

$updateResult = $update -> FMEdit();
//echo  '<p>errorCode: '.$updateResult['errorCode'];
//echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);

###########################################################################################################
## END: IF OFFER TO EMPLOY WAS MADE, UPDATE THE APPLICANT RECORD ##
###########################################################################################################

	if($updateResult['errorCode'] == 0){
	$_SESSION['position_updated'] = '1';
	
		// LOG THIS ACTION
		$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
		
		$newrecord = new FX($serverIP,$webCompanionPort);
		$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
		$newrecord -> SetDBPassword($webPW,$webUN);
		$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
		$newrecord -> AddDBParam('action','Manager - update position details');
		$newrecord -> AddDBParam('table','positions_NOVs');
		$newrecord -> AddDBParam('object_ID',$updateData['record_ID'][0]);
		$newrecord -> AddDBParam('affected_row_ID',$current_id);
		$newrecord -> AddDBParam('ip_address',$ip);
		$newrecordResult = $newrecord -> FMNew();
		//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
		//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
	
	header('Location: http://www.sedl.org/staff/sims/menu_positions_novs.php?action='.$view);
	exit;
	
	}else{
	$_SESSION['position_updated'] = '2';
	$_SESSION['position_update_error'] = $updateResult['errorCode'];

	header('Location: http://www.sedl.org/staff/sims/menu_positions_novs.php?action='.$view);
	exit;
	}

}

#################################################################
## START: FIND THIS POSITION ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('admin_base.fp7','Position_NOV_detail');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('position_ID','=='.$position_ID);
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam('leave_hrs_date','ascend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
$row_ID = $recordData['c_row_ID'][0];


//if((($recordData['PR_pre_approval_required_IT'][0] == '1')&&($recordData['sign_status_IT'][0] != '1'))||(($recordData['PR_pre_approval_required_IRC'][0] == '1')&&($recordData['sign_status_IRC'][0] != '1'))){
//$preappRequired = '1';
//}else{
//$preappRequired = '0';
//}
###############################################################
## END: FIND THIS JOB POSITION ##
###############################################################

$user = $_SESSION['user_ID'];
//echo '<span style="color:#999999">User: '.$user.'</span>';
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: UPDATE POSITION DETAILS</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">



<style type="text/css">

body {
	background-color: #DBE8F9;
	font: 11px/24px "Lucida Grande", "Trebuchet MS", Arial, Helvetica, sans-serif;
	//color: #5A698B;
}

#title {
	width: 330px;
	height: 26px;
	color: #5A698B;
	font: bold 12px/18px "Lucida Grande", "Trebuchet MS", Arial, Helvetica, sans-serif;
	padding-top: 5px;
	text-transform: uppercase;
	letter-spacing: 2px;
	text-align: center;
}

form {
	width: 335px;
}

.col1 {
	text-align: right;
	width: 135px;
	height: 31px;
	margin: 0;
	float: left;
	margin-right: 2px;
}

.col2 {
	width: 195px;
	height: 31px;
	display: block;
	float: left;
	margin: 0;
}

.col2comment {
	width: 195px;
	height: 98px;
	margin: 0;
	display: block;
	float: left;
}

.col1comment {
	text-align: right;
	width: 135px;
	height: 98px;
	float: left;
	display: block;
	margin-right: 2px;
}

div.row {
	clear: both;
	width: 335px;
}


.input {
	background-color: #fff;
	font: 11px/14px "Lucida Grande", "Trebuchet MS", Arial, Helvetica, sans-serif;
	color: #5A698B;
	margin: 4px 0 5px 8px;
	padding: 1px;
	border: 1px solid #8595B2;
}

.textarea {
	border: 1px solid #8595B2;
	background-color: #fff;
	font: 11px/14px "Lucida Grande", "Trebuchet MS", Arial, Helvetica, sans-serif;
	color: #5A698B;
	margin: 4px 0 5px 8px;
}

</style>


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="UpdateSelect();">

<table cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="900px">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Resumes & Applications: Reviewer Admin</h1><hr /></td></tr>
			
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Reviewer: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - Update the manager details for this position below. | <a href="/staff/sims/menu_positions_novs.php?action=<?php echo $view;?>">Back to applications</a></p>
			</td></tr>
			
			
			
			<tr><td colspan="2">
			
						<table cellpadding="10" cellspacing="0" border="0" bordercolor="ebebeb" bgcolor="ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body" colspan="2"><strong>NOV Position: <?php echo $recordData['c_position_display'][0];?></strong></td></tr>

						<tr><td class="body" nowrap><strong>POSITION DETAILS</strong></td><td align="right">ID: <?php echo $recordData['position_ID'][0];?></td></tr>

						<tr><td colspan="2" class="body">
						
										<form id="form2" name="form2" onsubmit="return checkFields()">
										<input type="hidden" name="mod" value="1">
										<input type="hidden" name="id" value="<?php echo $recordData['position_ID'][0];?>">
										<input type="hidden" name="row_id" value="<?php echo $recordData['c_row_ID'][0];?>">

							<table cellpadding="7" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
							
								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc">Managers in charge of hiring for this position may change the options indicated in yellow below.</div>
								
									<table style="border:0px dotted #000000;width:100%;margin-top:6px">
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>SEDL Work Unit</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['position_workgroup'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Position Title</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['position_title'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Work Location</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['position_location'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Exempt Status</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['position_exempt_status'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Position Opens</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['position_opens'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Position Closes</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['position_closes'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Review Begins</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['position_closes_review_begins'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Quantity</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['c_quantity_display'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Qualified applicant pool</td><td style="background-color:#ffff66;width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><input type="text" name="position_app_pool_count" value="<?php echo $recordData['position_app_pool_count'][0];?>" size="6"> &nbsp;<span class="tiny" >How many qualified candidates are allowed to complete the application process?</span></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Qualified interview pool</td><td style="background-color:#ffff66;width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><input type="text" name="position_app_pool_count2" value="<?php echo $recordData['position_app_pool_count2'][0];?>" size="6"> &nbsp;<span class="tiny" >How many qualified candidates are allowed to complete the interview process?</span></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Start work timeframe</td><td style="background-color:#ffff66;width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><input type="text" name="position_hire_timeframe" value="<?php echo $recordData['position_hire_timeframe'][0];?>" size="40"> &nbsp;<span class="tiny" >How much time is allowed before an applicant starts work (from date of hire)?</span></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Qualifications summary</td><td style="background-color:#ffff66;width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><span class="tiny" >In the space below, summarize the qualifications of the most highly qualified applicants.</span><br><textarea name="position_qualifications_summary" rows="5" cols="50"><?php echo $recordData['position_qualifications_summary'][0];?></textarea>
										</td></tr>
										<tr><td class="submit" colspan="2" style="text-align:right;border:0px;padding:6px"><input type="submit" value="Submit" />
									</table>

								
								</td>




								</tr>


											</td></tr>




									</table>
								</td>
								</tr>
								
								
		
						</table>
								
								
				</td></tr>
								
								
			</table>


</td></tr>
</table>


</body>

</html>

<?php
} else { ?>
Error
<?php } ?>


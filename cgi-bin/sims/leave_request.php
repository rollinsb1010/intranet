<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


$debug = 'off';


$action = $_GET['action'];
$leave_request_ID = $_GET['leave_request_ID'];
$approval_status = $_GET['approval_status'];

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

if($action == 'view'){ //IF THE USER IS VIEWING THIS LEAVE REQUEST

#######################################################
## START: DELETE LEAVE REQUEST HRS ROW IF APPLICABLE ##
#######################################################
if($_GET['delete_request_row'] == '1'){
$delete_row_ID = $_GET['delete_row_ID'];
$delete = new FX($serverIP,$webCompanionPort);
$delete -> SetDBData('SIMS_2.fp7','leave_request_hrs');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$delete_row_ID);

$deleteResult = $delete -> FMDelete();

if($approval_status != 'Not Submitted'){
$timesheet_revised = '1';
}

}
#####################################################
## END: DELETE LEAVE REQUEST HRS ROW IF APPLICABLE ##
#####################################################

if($_GET['add_to_request'] == '1'){
#################################################
## START: GET VARIABLES FROM FORM ##
#################################################
$timesheet_ID = $_GET['timesheet_ID'];
$leave_request_ID = $_GET['leave_request_ID'];
$leave_type = $_GET['leave_type'];
$day_from = $_GET['day_from'];
$day_to = $_GET['day_to'];
$time_from = $_GET['time_from'];
$time_to = $_GET['time_to'];
$num_hrs = $_GET['num_hrs'];
$date_from_m = $_GET['date_from_m'];
$date_from_y = $_GET['date_from_y'];
$hrs_descr = $_GET['hrs_descr'];

$day_span = ($day_to - $day_from) + 1;

#################################################
## END: GET VARIABLES FROM FORM ##
#################################################
$counter = 0;
#################################################
## START: CREATE THE NEW LEAVE REQUEST HRS ROW ##
#################################################
for($i = 1; $i <= $day_span; $i++){ // CREATE A LEAVE REQUEST HOURS ROW FOR EACH DAY INDICATED ON THE FORM

$day_from_calc = $day_from + $counter;

	if((date ("w",mktime(0,0,0,$date_from_m,$day_from_calc,$date_from_y)) != '0') && (date ("w",mktime(0,0,0,$date_from_m,$day_from_calc,$date_from_y)) != '6')){ // IF THE DAY INDICATED IS A WORK DAY

	$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$newrecord -> SetDBData('SIMS_2.fp7','leave_request_hrs'); //set dbase information
	$newrecord -> SetDBPassword($webPW,$webUN); //set password information
	
	$newrecord -> AddDBParam('leave_request_ID',$leave_request_ID);
	$newrecord -> AddDBParam('leave_hrs_type',$leave_type);
	$newrecord -> AddDBParam('leave_hrs_date',$date_from_m.'/'.$day_from_calc.'/'.$date_from_y);
	$newrecord -> AddDBParam('leave_hrs_time_begin',$time_from);
	$newrecord -> AddDBParam('leave_hrs_time_end',$time_to);
	$newrecord -> AddDBParam('leave_num_hrs',$num_hrs);
	$newrecord -> AddDBParam('leave_hrs_description',$hrs_descr);
	
	$newrecordResult = $newrecord -> FMNew();
	
	//  echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//  echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	//  $recordData = current($newrecordResult['data']);
	//  $new_leave_request_ID = $recordData['leave_request_ID'][0];
	//  $default_date = $recordData['c_pay_period_m'][0].'/xx/'.$recordData['c_pay_period_y'][0];
	//  $_SESSION['new_leave_request_check2'] = '0';
	
	
	
	}
	$counter++;
}

if($approval_status != 'Not Submitted'){
$timesheet_revised = '1';
}

###############################################
## END: CREATE THE NEW LEAVE REQUEST HRS ROW ##
###############################################
}

if($_GET['edit_request_row_confirm'] == '1'){
#################################################
## START: GET VARIABLES FROM FORM ##
#################################################
//$timesheet_ID = $_GET['timesheet_ID'];
//$leave_request_ID = $_GET['leave_request_ID'];
$leave_type = $_GET['leave_type'];
//$day_from = $_GET['day_from'];
//$day_to = $_GET['day_to'];
$time_from = $_GET['time_from'];
$time_to = $_GET['time_to'];
$num_hrs = $_GET['num_hrs'];
$current_id = $_GET['edit_request_row_ID'];
$hrs_descr = $_GET['hrs_descr'];
//$date_from_m = $_GET['date_from_m'];
//$date_from_y = $_GET['date_from_y'];

//$day_span = ($day_to - $day_from) + 1;

#################################################
## END: GET VARIABLES FROM FORM ##
#################################################

#################################################
## START: UPDATE THE LEAVE REQUEST HRS ROW ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','leave_request_hrs');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);
$update -> AddDBParam('leave_hrs_type',$leave_type);
$update -> AddDBParam('leave_hrs_time_begin',$time_from);
$update -> AddDBParam('leave_hrs_time_end',$time_to);
$update -> AddDBParam('leave_num_hrs',$num_hrs);
$update -> AddDBParam('leave_hrs_description',$hrs_descr);


$updateResult = $update -> FMEdit();

//  echo  '<p>errorCode: '.$updateResult['errorCode'];
//  echo  '<p>foundCount: '.$updateResult['foundCount'];
//  $recordData = current($updateResult['data']);
	
###############################################
## END: UPDATE THE LEAVE REQUEST HRS ROW ##
###############################################
if($approval_status != 'Not Submitted'){
$timesheet_revised = '1';
}


}

if($timesheet_revised == '1'){ // IF THE LEAVE REQUEST HAS BEEN REVISED
$current_id = $_GET['leave_request_row_ID'];
#################################################
## START: UPDATE THE LEAVE REQUEST STATUS ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','leave_requests2');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);
$update -> AddDBParam('approval_status','Revised');
//$update -> AddDBParam('signer_status_owner','');
$update -> AddDBParam('signer_status_pba','');
$update -> AddDBParam('signer_status_imm_spvsr','');


$updateResult = $update -> FMEdit();

//  echo  '<p>errorCode: '.$updateResult['errorCode'];
//  echo  '<p>foundCount: '.$updateResult['foundCount'];
//  $recordData = current($updateResult['data']);
	
###############################################
## END: UPDATE THE LEAVE REQUEST STATUS ##
###############################################
}


#################################################################
## START: FIND LEAVE REQUEST HRS RELATED TO THIS LEAVE REQUEST ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','leave_request_hrs');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('leave_request_ID','=='.$leave_request_ID);
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('leave_hrs_date','ascend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
$d = $recordData['leave_requests::c_pay_period_begin_d'][0];

###############################################################
## END: FIND LEAVE REQUEST HRS RELATED TO THIS LEAVE REQUEST ##
###############################################################

###################################################
## START: GRAB LEAVE REQUEST VALUELISTS FROM FMP ##
###################################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('SIMS_2.fp7','leave_request_hrs');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
#################################################
## END: GRAB LEAVE REQUEST VALUELISTS FROM FMP ##
#################################################


?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: My Leave Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function preventDelete() { 
	var answer = confirm ("Approved leave requests cannot be deleted.")
	return false;
	
}
</script>

<script language="JavaScript">

function confirmDelete() { 
	var answer2 = confirm ("Delete this leave request row?")
	if (!answer2) {
	return false;
	}
}

function confirmSubmit() { 
	var answer2 = confirm ("Submit this leave request now?")
	if (!answer2) {
	return false;
	}
}




</script>



<script language="JavaScript">
function checkFields() { 

var d_from = document.leave_request2.day_from.value;
var d_to = document.leave_request2.day_to.value;

d_from = parseInt(d_from);
d_to = parseInt(d_to);
	
		if (document.leave_request2.leave_type.value ==""){
			alert("Please enter the leave type.");
			document.leave_request2.leave_type.focus();
			return false;	}

		if (document.leave_request2.day_from.value ==""){
			alert("Please enter the leave begin date.");
			document.leave_request2.day_from.focus();
			return false;	}



		if (document.leave_request2.day_to.value ==""){
			alert("Please enter the leave end date.");
			document.leave_request2.day_to.focus();
			return false;	}

		if (d_to < d_from){
			alert("Leave ending date cannot precede leave begin date.");
			document.leave_request2.day_to.focus();
			return false;	}


		if ((document.leave_request2.time_from.value =="") || (document.leave_request2.time_from.value =="hh:mm")){
			alert("Please enter the leave begin time.");
			document.leave_request2.time_from.focus();
			return false;	}

		if ((document.leave_request2.time_to.value =="") || (document.leave_request2.time_to.value =="hh:mm")){
			alert("Please enter the leave end time.");
			document.leave_request2.time_to.focus();
			return false;	}

		if (document.leave_request2.num_hrs.value ==""){
			alert("Please enter the number of leave hours.");
			document.leave_request2.num_hrs.focus();
			return false;	}

		if (document.leave_request2.num_hrs.value > 8){
			alert("Please enter a maximum of 8 hours (per day).");
			document.leave_request2.num_hrs.focus();
			return false;	}

		if (document.leave_request2.num_hrs.value == "0"){
			alert("Please enter the number of leave hours (must be greater than 0).");
			document.leave_request2.num_hrs.focus();
			return false;	}






function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

var lv_hrs = document.leave_request2.num_hrs.value;

var roundedNumber = roundNumber(lv_hrs,1);	

if (lv_hrs !=roundedNumber){
	alert("Please round hours to the nearest tenth.");
	document.leave_request2.num_hrs.focus();
	return false;	}







}			


function checkFields2() { 
	
		if (document.leave_request2.leave_type.value ==""){
			alert("Please enter the leave type.");
			document.leave_request2.leave_type.focus();
			return false;	}

		if ((document.leave_request2.time_from.value =="") || (document.leave_request2.time_from.value =="hh:mm")){
			alert("Please enter the leave begin time.");
			document.leave_request2.time_from.focus();
			return false;	}

		if ((document.leave_request2.time_to.value =="") || (document.leave_request2.time_to.value =="hh:mm")){
			alert("Please enter the leave end time.");
			document.leave_request2.time_to.focus();
			return false;	}

		if (document.leave_request2.num_hrs.value ==""){
			alert("Please enter the number of leave hours.");
			document.leave_request2.num_hrs.focus();
			return false;	}

		if (document.leave_request2.num_hrs.value > 8){
			alert("Please enter a maximum of 8 hours (per day).");
			document.leave_request2.num_hrs.focus();
			return false;	}

		if (document.leave_request2.num_hrs.value == "0"){
			alert("Please enter the number of leave hours (must be greater than 0).");
			document.leave_request2.num_hrs.focus();
			return false;	}


function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

var lv_hrs = document.leave_request2.num_hrs.value;

var roundedNumber = roundNumber(lv_hrs,1);	

if (lv_hrs !=roundedNumber){
	alert("Please round hours to the nearest tenth.");
	document.leave_request2.num_hrs.focus();
	return false;	}


}			

function checkTimesheet() { 
	
		if (document.timesheet_check.timesheet_ID.value ==""){
			alert("A timesheet does not yet exist for this pay period.");
			return false;	}

}			

function confirmAddHrs() { 
	var answer = confirm ("Add these leave hours to your timesheet?")
	if (!answer) {
	return false;
	}
}

</script>



</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Leave Requests</h1><hr /></td></tr>
			
			<?php if($_SESSION['leave_request_signed_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your leave request has been successfully submitted to SIMS.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['leave_request_signed_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your leave request, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>
			
			<?php } ?>
			
			<?php if(($recordData['timesheets::c_timesheet_is_locked'][0] == '0') && ($recordData['leave_requests::approval_status'][0] != 'Not Submitted')){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small"><font color="red"><strong>NOTE: Last day to make changes to this leave request is <?php echo $recordData['timesheets::c_PayPeriodLockOutDate'][0];?></font></strong></p></td></tr>

			<?php } ?>
			
			<tr><td colspan="2">
			
						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>Leave Request Status: <?php echo $recordData['leave_requests::approval_status'][0];?> | Pay Period: <strong><?php echo $recordData['leave_requests::pay_period_end'][0];?></strong></td></tr>
						<tr><td class="body" nowrap><strong>LEAVE REQUEST</strong></td><td align="right">Leave Request ID: <?php echo $recordData['leave_request_ID'][0];?> | <a href="http://www.sedl.org/staff/personnel/leavereport.cgi" target="_blank">My leave report</a> | <a href="/staff/sims/leave_request_print.php?leave_request_ID=<?php echo $recordData['leave_request_ID'][0];?>&action=view&payperiod=<?php echo $recordData['leave_requests::pay_period_end'][0];?>" target="_blank">Print</a> | <a href="/staff/sims/timesheets.php?Timesheet_ID=<?php echo $recordData['leave_requests::timesheet_ID'][0];?>&action=view&src=menu&payperiod=<?php echo $recordData['leave_requests::pay_period_end'][0];?>" target="_blank" onclick="return checkTimesheet()">View timesheet</a> | <?php if(($recordData['leave_requests::approval_status'][0] != 'Not Submitted') && ($recordData['timesheets::c_timesheet_is_locked'][0] == '0')){?><a href="leave_request_send_hrs.php?leave_request_ID=<?php echo $recordData['leave_request_ID'][0];?>&timesheet_ID=<?php echo $recordData['leave_requests::timesheet_ID'][0];?>" onclick="return confirmAddHrs()">Add leave hrs to Timesheet</a> | <?php } ?><a href="menu_leave.php">Close</a></td></tr>
						<form name="timesheet_check"><input type="hidden" name="timesheet_ID" value="<?php echo $recordData['leave_requests::timesheet_ID'][0];?>"></form>
						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr bgcolor="#ebebeb"><td class="body" nowrap>Leave Type</td><td class="body">Date</td><td class="body">From</td><td class="body">To</td><td class="body" align="right">Hours</td><td class="body" nowrap>Description (optional)</td><td class="body">Options</td></tr>

								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								
									<?php if(($_GET['edit_request_row'] == '1') && ($_GET['edit_row_ID'] == $searchData['c_cwp_row_ID'][0])){ // DISPLAY LEAVE REQUEST HRS ROW IN AN EDITABLE FORM ?>
									
										<form name="leave_request2" onSubmit="return checkFields2()">
										<input type="hidden" name="action" value="view">
										<input type="hidden" name="edit_request_row_confirm" value="1">
										<input type="hidden" name="leave_request_ID" value="<?php echo $recordData['leave_request_ID'][0];?>">
										<input type="hidden" name="edit_request_row_ID" value="<?php echo $searchData['c_cwp_row_ID'][0];?>">
										<input type="hidden" name="leave_request_row_ID" value="<?php echo $searchData['leave_requests::c_row_ID_cwp'][0];?>">
										<input type="hidden" name="approval_status" value="<?php echo $searchData['leave_requests::approval_status'][0];?>">

											<tr class="body"><td>
											<select name="leave_type" class="body">
											<option value="">
								
											<?php foreach($v1Result['valueLists']['leave_hrs_type'] as $key => $value) { ?>
											<option value="<?php echo $value;?>" <?php if($searchData['leave_hrs_type'][0] == $value){echo 'SELECTED';}?>> <?php echo $value; ?>
											<?php } ?>
											</select>
											</td><td nowrap><?php echo $searchData['leave_hrs_date'][0];?> <span class="tiny">(<?php echo strtoupper($searchData['c_leave_hrs_day_name'][0]);?>)</span></td>							
											<td nowrap><input type="text" size="10" name="time_from" value="<?php echo $searchData['leave_hrs_time_begin'][0];?>"></td><td><input type="text" size="10" name="time_to" value="<?php echo $searchData['leave_hrs_time_end'][0];?>"></td>
											<td nowrap><input type="text" size="5" name="num_hrs" value="<?php echo $searchData['leave_num_hrs'][0];?>"></td>
											<td nowrap><input type="text" size="15" name="hrs_descr" value="<?php echo $searchData['leave_hrs_description'][0];?>"></td>
											<td nowrap><input type="button" onClick="history.back()" value="Cancel"><input type="submit" name="submit" value="Save Changes"></td>
											</tr>									

										</form>
									
									<?php }else{ // DISPLAY LEAVE REQUEST HRS ROW ?>

										<tr class="body"><td nowrap><?php echo $searchData['leave_hrs_type'][0];?><?php if($searchData['c_lv_hrs_requires_documentation'][0] == '1'){echo '*';}?></td><td nowrap><?php echo $searchData['leave_hrs_date'][0];?> <span class="tiny">(<?php echo strtoupper($searchData['c_leave_hrs_day_name'][0]);?>)</span></td><td nowrap><?php echo $searchData['leave_hrs_time_begin'][0];?></td><td nowrap><?php echo $searchData['leave_hrs_time_end'][0];?></td><td nowrap align="right"><?php echo $searchData['leave_num_hrs'][0];?></td><td nowrap><?php echo stripslashes($searchData['leave_hrs_description'][0]);?></td><td nowrap width="100%"><?php if(($_GET['enter_new_hours'] != '1') && ($_GET['edit_request_row'] != '1') && (($searchData['timesheet_ID'][0] == '')||($searchData['timesheets::c_timesheet_is_locked'][0] == '0'))){ ?><a href="leave_request.php?action=view&leave_request_ID=<?php echo $recordData['leave_request_ID'][0];?>&edit_request_row=1&edit_row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>">Edit</a><?php if($searchResult['foundCount'] > 1){ ?> | <a href="leave_request.php?action=view&leave_request_ID=<?php echo $recordData['leave_request_ID'][0];?>&leave_request_row_ID=<?php echo $searchData['leave_requests::c_row_ID_cwp'][0];?>&delete_request_row=1&approval_status=<?php echo $searchData['leave_requests::approval_status'][0];?>&delete_row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()">Delete</a><?php }}?><?php if($searchData['timesheets::c_timesheet_is_locked'][0] == '1'){?><img src="/staff/sims/images/padlock.jpg" border="0"><?php } ?></td></tr>
									
									<?php } ?>
							
								<?php } ?>

									<tr class="body"><td bgcolor="#ebebeb" colspan="4" nowrap align="right"><em>Total Request Hours:</em></td><td align="right"><?php echo $searchData['leave_requests::c_total_request_hrs'][0];?></td><td colspan="2" align="right"><?php if($_GET['edit_request_row'] != '1'){ ?><em><font color="#666666">Created: <?php echo $searchData['leave_requests::creation_timestamp'][0];?> | Modified: <?php echo $searchData['leave_requests::c_last_mod_doc'][0];?></font></em><?php }?></tr>

									<?php if(($_GET['enter_new_hours'] != '1') && ($_GET['edit_request_row'] != '1')){ // IF THE LEAVE REQUEST IS NOT CURRENTLY BEING MODIFIED ?>

										<?php if((($searchData['leave_requests::approval_status'][0] == 'Not Submitted')||($searchData['leave_requests::approval_status'][0] == 'Revised')) && (($searchData['timesheet_ID'][0] == '')||($searchData['timesheets::c_timesheet_is_locked'][0] == '0'))){ //IF THE LEAVE REQUEST HAS NOT YET BEEN SIGNED/SUBMITTED, SHOW SUBMIT BUTTON  ?>

											<form name="submit_leave_request">
											<input type="hidden" name="action" value="submit_leave_request">
											<?php if($timesheet_revised == '1'){ ?>
											<input type="hidden" name="status" value="revised">
											<?php } ?>
											<input type="hidden" name="leave_request_ID" value="<?php echo $recordData['leave_request_ID'][0];?>">
											<input type="hidden" name="leave_request_row_ID" value="<?php echo $recordData['leave_requests::c_row_ID_cwp'][0];?>">
											
											<tr class="body">
											<td colspan="6" nowrap><a href="leave_request.php?action=view&enter_new_hours=1&leave_request_ID=<?php echo $recordData['leave_request_ID'][0];?>">Add Leave Request Hours</a></td>
											<td align="right"><input type="submit" value="Submit Leave Request" onclick="return confirmSubmit()"></td>
											</tr></form>

										<?php } else { //IF THE LEAVE REQUEST HAS BEEN SIGNED/SUBMITTED, SHOW APPROVAL SIGNATURES ?>

											<tr class="body">
											<td colspan="6" nowrap><?php if(($searchData['timesheet_ID'][0] == '')||($searchData['timesheets::c_timesheet_is_locked'][0] == '0')){ ?><a href="leave_request.php?action=view&enter_new_hours=1&leave_request_ID=<?php echo $recordData['leave_request_ID'][0];?>">Add Leave Request Hours</a><?php }?>&nbsp;</td></tr>
											
											<tr><td colspan="6" nowrap><strong>SIGNATURES</strong>:<br>
											
												<table class="sims" cellspacing="1" cellpadding="10" border="1">
												<tr class="body" valign="top"><td align="center" valign="bottom">
												<img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_owner'][0];?>.png"><p>
												<span class="tiny">Staff Member<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_owner'][0];?>]</font></span></td>

<?php if($recordData['leave_requests_staff_byStaffID::c_cwp_spvsr_is_pba'][0] != '1'){ // IF THE STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PRIMARY BUDGET AUTHORITY ARE NOT THE SAME PERSON ?>


												<td align="center" valign="bottom"><?php if($searchData['leave_requests::signer_status_imm_spvsr'][0] == '1'){ ?><img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_imm_spvsr'][0];?>.png"><?php } else { echo $searchData['leave_requests::signer_ID_imm_spvsr'][0];?><?php } ?><p>
												<span class="tiny">Immediate Supervisor<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_imm_spvsr'][0];?>]</font></span></td>

												<td align="center" valign="bottom"><?php if($searchData['leave_requests::signer_status_pba'][0] == '1'){ ?><img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_pba'][0];?>.png"><?php } else { echo $searchData['leave_requests::signer_ID_pba'][0];?><?php } ?><p>
												<span class="tiny">Primary Budget Authority<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_pba'][0];?>]</font></span></td>

<?php } else {  // IF THE STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PRIMARY BUDGET AUTHORITY ARE THE SAME PERSON ?>

												<td align="center" valign="bottom"><?php if($searchData['leave_requests::signer_status_pba'][0] == '1'){ ?><img src="/staff/sims/signatures/<?php echo $searchData['leave_requests::signer_ID_pba'][0];?>.png"><?php } else { echo $searchData['leave_requests::signer_ID_pba'][0];?><?php } ?><p>
												<span class="tiny">Primary Budget Authority<br><font color="999999">[<?php echo $searchData['leave_requests::signer_timestamp_pba'][0];?>]</font></span></td>
<?php } ?>												
												</tr>										
												</table>

											</td></tr>

										<?php }?>

									<?php }?>
							</table><div class="tiny">*Supporting documentation required.</div>

						</td></tr>
<?php if($_GET['enter_new_hours'] == '1'){ 

?>						
						<tr><td colspan="2">

							<form name="leave_request2" type="GET" onsubmit="return checkFields()">
							<input type="hidden" name="action" value="view">
							<input type="hidden" name="add_to_request" value="1">
							<input type="hidden" name="leave_request_ID" value="<?php echo $recordData['leave_request_ID'][0];?>">
							<input type="hidden" name="date_from_m" value="<?php echo $recordData['leave_requests::c_pay_period_m'][0];?>">
							<input type="hidden" name="date_from_y" value="<?php echo $recordData['leave_requests::c_pay_period_y'][0];?>">
							<input type="hidden" name="approval_status" value="<?php echo $recordData['leave_requests::approval_status'][0];?>">
							<input type="hidden" name="leave_request_row_ID" value="<?php echo $recordData['leave_requests::c_row_ID_cwp'][0];?>">

								<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
									<tr bgcolor="#ebebeb"><td class="body" nowrap colspan="5">ADD LEAVE HOURS TO THIS REQUEST</td></tr>
	
										<tr class="body"><td nowrap>Type of Leave:</td><td nowrap>Date(s) of Leave:</td><td nowrap>Exact Time(s):</td><td nowrap>#Leave Hrs (PER DAY):</td><td nowrap>Description (optional):</td></tr>
										<tr class="body"><td>
										<select name="leave_type" class="body">
										<option value="">
							
										<?php foreach($v1Result['valueLists']['leave_hrs_type'] as $key => $value) { ?>
										<option value="<?php echo $value;?>"> <?php echo $value; ?><?php if(($value == 'Jury Duty/Other (J)') || ($value == 'Leave w/o Pay - FMLA (F)') || ($value == 'Leave w/o Pay - not FMLA (L)')){echo '*';}?>
										<?php } ?>
										<option value="">----------------------------------
										<option value="">*Supporting documentation required.
										</select>
										</td><td nowrap><?php echo $recordData['leave_requests::c_pay_period_m'][0];?>/ 
										
										<select name="day_from" class="body">
										<option value="">
							
										<?php for($i=$d;$i <= $recordData['leave_requests::c_pay_period_end_d'][0];$i++) { ?>
										<option value="<?php echo $i;?>"> <?php echo $i; ?>
										<?php } ?>
										</select>
										
										
										/<?php echo $recordData['leave_requests::c_pay_period_y'][0];?> to <?php echo $recordData['leave_requests::c_pay_period_m'][0];?>/ 
										
										<select name="day_to" class="body">
										<option value="">
							
										<?php for($x=$d;$x <= $recordData['leave_requests::c_pay_period_end_d'][0];$x++) { ?>
										<option value="<?php echo $x;?>"> <?php echo $x; ?>
										<?php } ?>
										</select>
											
										/<?php echo $recordData['leave_requests::c_pay_period_y'][0];?></td>							
										<td nowrap><input type="text" size="10" name="time_from" value="hh:mm"> to <input type="text" size="10" name="time_to" value="hh:mm"></td>
										<td nowrap><input type="text" size="5" name="num_hrs"><span class="tiny">(numbers only. Enter "8" for all day)</span></td>								
										<td nowrap><input type="text" size="15" name="hrs_descr"></td></tr>								
	
										<tr class="body"><td bgcolor="#ebebeb" colspan="4" nowrap><em>Enter leave details in this section, then click "Add to Request".</em></td><td align="right" nowrap><input type="button" onClick="history.back()" value="Cancel"><input type="submit" name="submit" value="Add to Request"></td></tr>
	
								</table>

							</form>

						</td></tr>

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

} elseif($action == 'new') { //IF THIS IS A NEW LEAVE REQUEST, SELECT PAY PERIOD
//echo $_SESSION['employee_type'];
###############################################################################################
## START: FIND THE MOST RECENT 3 TIMESHEETS FOR THIS USER TO POPULATE PAY PERIOD SELECT LIST ##
###############################################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','timesheets');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('staff_ID','=='.$_SESSION['staff_ID']);
$search -> AddDBParam('c_timesheet_is_locked','0');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('TimesheetID','descend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
#############################################################################################
## END: FIND THE MOST RECENT 3 UNLOCKED TIMESHEETS FOR THIS USER TO POPULATE PAY PERIOD SELECT LIST ##
#############################################################################################

####################################
## START: GET YEARS FOR DROP-DOWN ##
####################################
$this_year = date("Y");
$next_year = $this_year + 1;
####################################
## END: GET YEARS FOR DROP-DOWN ##
####################################
$_SESSION['new_leave_request_check'] = '1';

?>

<html>
<head>
<title>SIMS: My Leave Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function checkFields() { 

	// Pay period
		if (document.leave_request1.timesheet_ID.value ==""){
			alert("Please select a pay period.");
			document.leave_request1.timesheet_ID.focus();
			return false;	}
}			

function checkFields2() { 

	// Pay period month
		if (document.leave_request2.future_pay_period_m.value ==""){
			alert("Please select the future pay period (month).");
			document.leave_request2.future_pay_period_m.focus();
			return false;	}

	// Pay period day
		if (document.leave_request2.future_pay_period_d.value ==""){
			alert("Please select the future pay period (day).");
			document.leave_request2.future_pay_period_d.focus();
			return false;	}

	// Pay period year
		if (document.leave_request2.future_pay_period_y.value ==""){
			alert("Please select the future pay period (year).");
			document.leave_request2.future_pay_period_y.focus();
			return false;	}



}	


// Get the HTTP Object
function getHTTPObject(){

	if (window.ActiveXObject) return new ActiveXObject("Microsoft.XMLHTTP");
	
	else if (window.XMLHttpRequest) return new XMLHttpRequest();
	
	else {
	
	alert("Your browser does not support AJAX.");
	
	return null;
	
	}

}



function doWork(){

	httpObject = getHTTPObject();
	
	if (httpObject != null) {
	
		httpObject.open("GET", "leave_request_pay_period_picker.php?month="+document.getElementById('month').value, true);
		
		httpObject.onreadystatechange = setOutput;
		
		httpObject.send(null);
	
	}

}

function setOutput(){

	if(httpObject.readyState == 4){
	
		var combo = document.getElementById('day');
		
		combo.options.length = 0;
		
		var response = httpObject.responseText;
		
		var items = response.split(";");
		
		var count = items.length;
		
		for (var i=0;i<count;i++){
			
			var options = items[i].split("-");
			
			combo.options[i] =
			
			new Option(options[0],options[1]);
		
		}
	
	}

}




</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Leave Requests</h1><hr /></td></tr>
			
			
			
			<tr><td colspan="2">
			<form name="leave_request1" method="GET" onsubmit="return checkFields()">
			<input type="hidden" name="action" value="new_detail">
			<input type="hidden" name="type" value="existing">

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW LEAVE REQUEST</strong></td><td align="right"><a href="menu_leave.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>Select Pay Period</strong>: <em>Choose an existing timesheet or enter a future pay period. </em></td></tr>
								<tr bgcolor="#ebebeb">
								<td class="body" nowrap valign="top" align="center">Existing Timesheets:
								
								<select name="timesheet_ID" class="body">
								<option value=""></option>
						
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
									<option value="<?php echo $searchData['TimesheetID'][0];?>"> <?php echo $searchData['PayPeriodBegin'][0];?> - <?php echo $searchData['c_PayPeriodEnd'][0];?></option>
								<?php } ?>
								
								<?php if($searchResult['foundCount'] == '0'){ ?>
									<option value="">NO AVAILABLE TIMESHEETS</option>
								<?php } ?>
								</select> <input type="submit" name="submit" value="Submit">
								
								</td>
			</form>					
			<form name="leave_request2" method="GET" onsubmit="return checkFields2()">
			<input type="hidden" name="action" value="new_detail">					
			<input type="hidden" name="type" value="future">
								
								<td class="body" nowrap valign="top" align="center">Future Pay Period (ending):
								
								<select name="future_pay_period_m" class="body" id="month" onchange="doWork();">
								<option value=""></option>
						
									<option value="01"> Jan</option>
									<option value="02"> Feb</option>
									<option value="03"> Mar</option>
									<option value="04"> Apr</option>
									<option value="05"> May</option>
									<option value="06"> Jun</option>
									<option value="07"> Jul</option>
									<option value="08"> Aug</option>
									<option value="09"> Sep</option>
									<option value="10"> Oct</option>
									<option value="11"> Nov</option>
									<option value="12"> Dec</option>

								</select> 
								
								<select name="future_pay_period_d" class="body" id="day">
									<option value=""></option>

									<option value="15">15</option>
									<option value="31">31</option>

								</select> 
								<select name="future_pay_period_y" class="body">
								<option value=""></option>
						
									<option value="<?php echo $this_year;?>" selected> <?php echo $this_year;?></option>
									<option value="<?php echo $next_year;?>"> <?php echo $next_year;?></option>

								</select> 

								<input type="submit" name="submit" value="Submit">
								
								
								</td></tr>
								<tr><td colspan="2"><em>Note: Leave requests cannot span pay periods. If your leave spans more than one pay period, create 2 leave requests (1 for each pay period).</em></td></tr>
								


							</table>

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

} elseif($action == 'new_detail') { //IF THIS IS A NEW LEAVE REQUEST, ENTER LEAVE DETAILS

#########################################
## START: CREATE THE NEW LEAVE REQUEST ##
#########################################
if($_SESSION['new_leave_request_check'] =='1'){ //PREVENT NEW RECORDS FROM BEING ADDED BY RE-LOADING THE BROWSER

$type = $_GET['type'];
$timesheet_ID = $_GET['timesheet_ID'];
$future_pay_period_m = $_GET['future_pay_period_m'];
$future_pay_period_d = $_GET['future_pay_period_d'];
$future_pay_period_y = $_GET['future_pay_period_y'];

$future_pay_period_end = date('m/d/Y',mktime(0, 0, 0, $future_pay_period_m, $future_pay_period_d, $future_pay_period_y));

//echo $future_pay_period_end;
//echo $type;

  $newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
  $newrecord -> SetDBData('SIMS_2.fp7','leave_requests2'); //set dbase information
  $newrecord -> SetDBPassword($webPW,$webUN); //set password information
  
if($type == 'existing'){

  $newrecord -> AddDBParam('timesheet_ID',$timesheet_ID);

}elseif($type == 'future'){

  $newrecord -> AddDBParam('pay_period_end',$future_pay_period_end);
  $newrecord -> AddDBParam('staff_ID',$_SESSION['staff_ID']);

}

  $newrecordResult = $newrecord -> FMNew();
  
//  echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//  echo  '<p>foundCount: '.$newrecordResult['foundCount'];
  $recordData = current($newrecordResult['data']);
  $new_leave_request_ID = $recordData['leave_request_ID'][0];
//  $default_date = $recordData['c_pay_period_m'][0].'/xx/'.$recordData['c_pay_period_y'][0];
  

  $_SESSION['new_leave_request_check'] = '0';
  $_SESSION['new_leave_request_check2'] ='1';
  $_SESSION['pay_period_m'] = $recordData['c_pay_period_m'][0];
  $_SESSION['pay_period_y'] = $recordData['c_pay_period_y'][0];
  $_SESSION['leave_request_ID'] = $recordData['leave_request_ID'][0];
  $d = $recordData['c_pay_period_begin_d'][0];

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','CREATE_LEAVE_REQUEST');
$newrecord -> AddDBParam('table','LEAVE_REQUESTS');
$newrecord -> AddDBParam('object_ID',$recordData['leave_request_ID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$recordData['c_row_ID_cwp'][0]);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


}
#######################################
## END: CREATE THE NEW LEAVE REQUEST ##
#######################################

###################################################
## START: GRAB LEAVE REQUEST VALUELISTS FROM FMP ##
###################################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('SIMS_2.fp7','leave_request_hrs');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
#################################################
## END: GRAB LEAVE REQUEST VALUELISTS FROM FMP ##
#################################################

?>

<html>
<head>
<title>SIMS: Leave Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function checkFields() { 


var d_from = document.leave_request3.day_from.value;
var d_to = document.leave_request3.day_to.value;

d_from = parseInt(d_from);
d_to = parseInt(d_to);
	
		if (document.leave_request3.leave_type.value ==""){
			alert("Please enter the leave type.");
			document.leave_request3.leave_type.focus();
			return false;	}

		if (document.leave_request3.day_from.value ==""){
			alert("Please enter the leave begin date.");
			document.leave_request3.day_from.focus();
			return false;	}

		if (document.leave_request3.day_to.value ==""){
			alert("Please enter the leave end date.");
			document.leave_request3.day_to.focus();
			return false;	}

		if (d_to < d_from){
			alert("Leave ending date cannot precede leave begin date.");
			document.leave_request3.day_to.focus();
			return false;	}


		if ((document.leave_request3.time_from.value =="") || (document.leave_request3.time_from.value =="hh:mm")){
			alert("Please enter the leave begin time.");
			document.leave_request3.time_from.focus();
			return false;	}

		if ((document.leave_request3.time_to.value =="") || (document.leave_request3.time_to.value =="hh:mm")){
			alert("Please enter the leave end time.");
			document.leave_request3.time_to.focus();
			return false;	}

		if (document.leave_request3.num_hrs.value ==""){
			alert("Please enter the number of leave hours.");
			document.leave_request3.num_hrs.focus();
			return false;	}

		if (document.leave_request3.num_hrs.value > 8){
			alert("Please enter a maximum of 8 hours (per day).");
			document.leave_request3.num_hrs.focus();
			return false;	}

		if (document.leave_request3.num_hrs.value == "0"){
			alert("Please enter the number of leave hours (must be greater than 0).");
			document.leave_request3.num_hrs.focus();
			return false;	}


function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

var lv_hrs = document.leave_request3.num_hrs.value;

var roundedNumber = roundNumber(lv_hrs,1);	

if (lv_hrs !=roundedNumber){
	alert("Please round hours to the nearest tenth.");
	document.leave_request3.num_hrs.focus();
	return false;	}


}			

function zoomWindow() {
window.resizeTo(1200,screen.height)
}



</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Leave Requests</h1><hr /></td></tr>
			
			<?php if($_SESSION['leave_request_signed_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your leave request has been successfully submitted to SIMS.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['leave_request_signed_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your leave request, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>
			
			<?php } ?>
			
			
			<tr><td colspan="2">
			<form name="leave_request3" type="GET" onsubmit="return checkFields()">
			<input type="hidden" name="action" value="view">
			<input type="hidden" name="add_to_request" value="1">
			<input type="hidden" name="leave_request_ID" value="<?php echo $recordData['leave_request_ID'][0];?>">
			<input type="hidden" name="date_from_m" value="<?php echo $recordData['c_pay_period_m'][0];?>">
			<input type="hidden" name="date_from_y" value="<?php echo $recordData['c_pay_period_y'][0];?>">
			<input type="hidden" name="pay_period_begin_d" value="<?php echo $recordData['c_pay_period_begin_d'][0];?>">
			<input type="hidden" name="pay_period_end" value="<?php echo $recordData['c_pay_period_end_d'][0];?>">
			
			
			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>Leave Request Status: <?php echo $recordData['approval_status'][0];?> | Pay Period: <strong><?php echo $recordData['pay_period_end'][0];?></strong></td></tr>
						<tr><td class="body" nowrap><strong>LEAVE REQUEST</strong></td><td align="right">Leave Request ID: <?php echo $recordData['leave_request_ID'][0];?> | <a href="http://www.sedl.org/staff/personnel/leavereport.cgi" target="_blank">My leave report</a> | <a href="menu_leave.php">Cancel</a></td></tr>




						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr bgcolor="#ebebeb"><td class="body" nowrap colspan="2">ADD LEAVE HOURS TO THIS REQUEST</td><td class="body" align="right" colspan="3"><font color="#666666"><em>Created: <?php echo $recordData['creation_timestamp'][0];?></font></em></td></tr>

									<tr class="body"><td nowrap>Type of Leave:</td><td nowrap>Date(s) of Leave:</td><td nowrap>Exact Time(s):</td><td nowrap>#Leave Hrs (PER DAY):</td><td nowrap>Description (optional):</td></tr>
									<tr class="body"><td>
									<select name="leave_type" class="body">
									<option value="">
						
									<?php foreach($v1Result['valueLists']['leave_hrs_type'] as $key => $value) { ?>
									<option value="<?php echo $value;?>"> <?php echo $value; ?><?php if(($value == 'Jury Duty/Other (J)') || ($value == 'Leave w/o Pay - FMLA (F)') || ($value == 'Leave w/o Pay - not FMLA (L)')){echo '*';}?>
									<?php } ?>
									<option value="">----------------------------------
									<option value="">*Supporting documentation required.
									</select>
									</td><td nowrap><?php echo $recordData['c_pay_period_m'][0];?>/ 
									
									<select name="day_from" class="body">
									<option value="">
						
									<?php for($i=$d;$i <= $recordData['c_pay_period_end_d'][0];$i++) { ?>
									<?php if((date("w",mktime(0,0,0,$recordData['c_pay_period_m'][0],$i,$recordData['c_pay_period_y'][0])) == '0')||(date("w",mktime(0,0,0,$recordData['c_pay_period_m'][0],$i,$recordData['c_pay_period_y'][0])) == '6')){?><option value=""> <?php echo '--</option>'; }else{ ?><option value="<?php echo $i;?>"> <?php echo $i.'</option>'; }?>
									<?php } ?>
									</select>
									
									
									/<?php echo $recordData['c_pay_period_y'][0];?> to <?php echo $recordData['c_pay_period_m'][0];?>/ 
									
									<select name="day_to" class="body">
									<option value="">
						
									<?php for($x=$d;$x <= $recordData['c_pay_period_end_d'][0];$x++) { ?>
									<?php if((date("w",mktime(0,0,0,$recordData['c_pay_period_m'][0],$x,$recordData['c_pay_period_y'][0])) == '0')||(date("w",mktime(0,0,0,$recordData['c_pay_period_m'][0],$x,$recordData['c_pay_period_y'][0])) == '6')){?><option value=""> <?php echo '--</option>'; }else{ ?><option value="<?php echo $x;?>"> <?php echo $x.'</option>'; }?>
									<?php } ?>
									</select>

									
									
									/<?php echo $recordData['c_pay_period_y'][0];?></td>							
									<td nowrap><input type="text" size="10" name="time_from" value="hh:mm"> to <input type="text" size="10" name="time_to" value="hh:mm"></td>
									<td nowrap><input type="text" size="5" name="num_hrs"> <span class="tiny">(numbers only. Enter "8" for all day)</span></td>
									<td nowrap><input type="text" size="15" name="hrs_descr"></td>
									
									</tr>								

									<tr class="body"><td bgcolor="#ebebeb" colspan="4" nowrap><em>Enter leave details in this section, then click "Add to Request".</em></td><td align="right"><input type="submit" name="submit" value="Add to Request"></td></tr>

							</table>

						</td></tr>



				</table>


			</form>

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

} elseif($action == 'new_detail2') { //IF THIS IS A NEW LEAVE REQUEST, ENTER LEAVE DETAILS

#######################################################
## START: DELETE LEAVE REQUEST HRS ROW IF APPLICABLE ##
#######################################################
if($_GET['delete_request_row'] == '1'){
$delete_row_ID = $_GET['delete_row_ID'];
$delete = new FX($serverIP,$webCompanionPort);
$delete -> SetDBData('SIMS_2.fp7','leave_request_hrs');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$delete_row_ID);

$deleteResult = $delete -> FMDelete();
}
#####################################################
## END: DELETE LEAVE REQUEST HRS ROW IF APPLICABLE ##
#####################################################

#################################################
## START: GET VARIABLES FROM FORM ##
#################################################

$leave_request_ID = $_GET['leave_request_ID'];
if($leave_request_ID == ''){
$leave_request_ID = $_SESSION['leave_request_ID'];
}
$leave_type = $_GET['leave_type'];
$day_from = $_GET['day_from'];
$day_to = $_GET['day_to'];
$time_from = $_GET['time_from'];
$time_to = $_GET['time_to'];
$num_hrs = $_GET['num_hrs'];
$date_from_m = $_GET['date_from_m'];
$date_from_y = $_GET['date_from_y'];
$hrs_descr = $_GET['hrs_descr'];



#################################################
## END: GET VARIABLES FROM FORM ##
#################################################

#################################################
## START: CREATE THE NEW LEAVE REQUEST HRS ROW ##
#################################################
if($_GET['add_to_request'] == '1'){
//if($_SESSION['new_leave_request_check2'] =='1'){ //PREVENT NEW RECORDS FROM BEING ADDED BY RE-LOADING THE BROWSER
  if($_GET['leave_type'] != ''){ //CHECK FOR NO PAY PERIOD SELECTED FROM DROP DOWN LIST
  $timesheet_ID = $_GET['timesheet_ID'];
  }else{
  echo 'Error: No leave type selected.';
  exit;
  }

  $newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
  $newrecord -> SetDBData('SIMS_2.fp7','leave_request_hrs'); //set dbase information
  $newrecord -> SetDBPassword($webPW,$webUN); //set password information
  
  $newrecord -> AddDBParam('leave_request_ID',$leave_request_ID);
  $newrecord -> AddDBParam('leave_hrs_type',$leave_type);
  $newrecord -> AddDBParam('leave_hrs_date',$date_from_m.'/'.$day_from.'/'.$date_from_y);
  $newrecord -> AddDBParam('leave_hrs_time_begin',$time_from);
  $newrecord -> AddDBParam('leave_hrs_time_end',$time_to);
  $newrecord -> AddDBParam('leave_num_hrs',$num_hrs);
  $newrecord -> AddDBParam('leave_hrs_description',$hrs_descr);
  
  $newrecordResult = $newrecord -> FMNew();
  
//  echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//  echo  '<p>foundCount: '.$newrecordResult['foundCount'];
//  $recordData = current($newrecordResult['data']);
//  $new_leave_request_ID = $recordData['leave_request_ID'][0];
//  $default_date = $recordData['c_pay_period_m'][0].'/xx/'.$recordData['c_pay_period_y'][0];
  

//  $_SESSION['new_leave_request_check2'] = '0';
//}
}
###############################################
## END: CREATE THE NEW LEAVE REQUEST HRS ROW ##
###############################################

###################################################
## START: GRAB LEAVE REQUEST VALUELISTS FROM FMP ##
###################################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('SIMS_2.fp7','leave_request_hrs');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
#################################################
## END: GRAB LEAVE REQUEST VALUELISTS FROM FMP ##
#################################################

#################################################################
## START: FIND LEAVE REQUEST HRS RELATED TO THIS LEAVE REQUEST ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','leave_request_hrs');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('leave_request_ID','=='.$leave_request_ID);
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('leave_hrs_date','ascend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
###############################################################
## END: FIND LEAVE REQUEST HRS RELATED TO THIS LEAVE REQUEST ##
###############################################################

?>

<html>
<head>
<title>SIMS: My Leave Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function checkFields() { 

	
		if (document.leave_request2.leave_type.value ==""){
			alert("Please enter the leave type.");
			document.leave_request2.leave_type.focus();
			return false;	}

		if ((document.leave_request2.day_from.value =="") || (document.leave_request2.day_from.value =="xx")){
			alert("Please enter the leave begin date.");
			document.leave_request2.day_from.focus();
			return false;	}

		if ((document.leave_request2.day_to.value =="") || (document.leave_request2.day_to.value =="xx")){
			alert("Please enter the leave end date.");
			document.leave_request2.day_to.focus();
			return false;	}

		if ((document.leave_request2.time_from.value =="") || (document.leave_request2.time_from.value =="hh:mm")){
			alert("Please enter the leave begin time.");
			document.leave_request2.time_from.focus();
			return false;	}

		if ((document.leave_request2.time_to.value =="") || (document.leave_request2.time_to.value =="hh:mm")){
			alert("Please enter the leave end time.");
			document.leave_request2.time_to.focus();
			return false;	}

		if (document.leave_request2.num_hrs.value ==""){
			alert("Please enter the number of leave hours.");
			document.leave_request2.num_hrs.focus();
			return false;	}

		if (document.leave_request2.num_hrs.value > 8){
			alert("Please enter a maximum of 8 hours (per day).");
			document.leave_request2.num_hrs.focus();
			return false;	}

		if (document.leave_request2.num_hrs.value == "0"){
			alert("Please enter the number of leave hours (must be greater than 0).");
			document.leave_request2.num_hrs.focus();
			return false;	}


function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

var lv_hrs = document.leave_request2.num_hrs.value;

var roundedNumber = roundNumber(lv_hrs,1);	

if (lv_hrs !=roundedNumber){
	alert("Please round hours to the nearest tenth.");
	document.leave_request2.num_hrs.focus();
	return false;	}


}			
</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Leave Requests</h1><hr /></td></tr>
			
			<?php if($_SESSION['leave_request_signed_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your leave request has been successfully submitted to SIMS.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['leave_request_signed_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your leave request, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>
			
			<?php } ?>
			
			
			<tr><td colspan="2">
			<form name="leave_request2" type="GET">
			<input type="hidden" name="action" value="new_detail2">
			<input type="hidden" name="add_to_request" value="1">
			<input type="hidden" name="leave_request_ID" value="<?php echo $recordData['leave_request_ID'][0];?>">
			<input type="hidden" name="date_from_m" value="<?php echo $recordData['leave_requests::c_pay_period_m'][0];?>">
			<input type="hidden" name="date_from_y" value="<?php echo $recordData['leave_requests::c_pay_period_y'][0];?>">


						<table cellpadding="10" cellspacing="0" border="0" bordercolor="ebebeb" bgcolor="ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>Leave Request Status: <?php echo $recordData['approval_status'][0];?> | Pay Period: <strong><?php echo $recordData['pay_period_end'][0];?></strong></td></tr>
						<tr><td class="body" nowrap><strong>LEAVE REQUEST</strong></td><td align="right">Leave Request ID: <?php echo $_SESSION['leave_request_ID'][0];?> | <a href="http://www.sedl.org/staff/personnel/leavereport.cgi" target="_blank">My leave report</a> | <a href="menu_leave.php">Cancel</a></td></tr>




						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%">
								<tr bgcolor="#ebebeb"><td class="body" nowrap>Leave Type</td><td class="body">Date</td><td class="body">From</td><td class="body">To</td><td class="body" align="right">Hours</td><td class="body">&nbsp;</td></tr>

								<?php if($searchResult['foundCount'] > '0'){ ?>
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								
									<tr class="body"><td nowrap><?php echo $searchData['leave_hrs_type'][0];?></td><td><?php echo $searchData['leave_hrs_date'][0];?></td><td nowrap><?php echo $searchData['leave_hrs_time_begin'][0];?></td><td nowrap><?php echo $searchData['leave_hrs_time_end'][0];?></td><td nowrap align="right"><?php echo $searchData['leave_num_hrs'][0];?></td><td nowrap><a href="">Edit</a> | <a href="leave_request.php?action=new_detail2&delete_request_row=1&delete_row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>&leave_request_ID=<?php echo $_SESSION['leave_request_ID'][0];?>">Delete</a></td></tr>
								
								<?php } ?>

								<?php } else { ?>
								
									<tr class="body"><td nowrap colspan="6" align="center"><em>No leave request hours found for this request.</em></td></tr>
								
								<?php } ?>


									<tr class="body"><td bgcolor="#ebebeb" colspan="4" nowrap align="right"><em>Total Request Hours:</em></td><td align="right"><?php echo $searchData['leave_requests::c_total_request_hrs'][0];?></td><td align="right"><em><font color="#666666">Created: <?php echo $searchData['leave_requests::creation_timestamp'][0];?> | Modified: <?php echo $searchData['leave_requests::c_last_mod_doc'][0];?></font></em></tr>

							</table>


						</td></tr>
						
						<tr><td colspan="2">
						


							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%">
								<tr bgcolor="#ebebeb"><td class="body" nowrap colspan="4">ADD LEAVE HOURS TO THIS REQUEST</td></tr>

									<tr class="body"><td nowrap>Type of Leave:</td><td nowrap>Date(s) of Leave:</td><td nowrap>Exact Time(s):</td><td nowrap>#Leave Hrs (PER DAY):</td></tr>
									<tr class="body"><td>
									<select name="leave_type" class="body">
									<option value="">
						
									<?php foreach($v1Result['valueLists']['leave_hrs_type'] as $key => $value) { ?>
									<option value="<?php echo $value;?>"> <?php echo $value; ?><?php if(($value == 'Jury Duty/Other (J)') || ($value == 'Leave w/o Pay - FMLA (F)') || ($value == 'Leave w/o Pay - not FMLA (L)')){echo '*';}?>
									<?php } ?>
									<option value="">----------------------------------
									<option value="">*Supporting documentation required.
									</select>
									</td><td nowrap><?php echo $_SESSION['pay_period_m'];?>/ <input type="text" size="3" name="day_from" value="xx"> /<?php echo $_SESSION['pay_period_y'];?> to <?php echo $_SESSION['pay_period_m'];?>/ <input type="text" size="3" name="day_to" value="xx"> /<?php echo $_SESSION['pay_period_y'];?></td>							
									<td nowrap><input type="text" size="10" name="time_from" value="hh:mm"> to <input type="text" size="10" name="time_to" value="hh:mm"></td>
									<td nowrap><input type="text" size="5" name="num_hrs"><span class="tiny">(numbers only. Enter "8" for all day)</span></td></tr>								

									<tr class="body"><td bgcolor="#ebebeb" colspan="3" nowrap><em>Enter leave details in this section, then click "Add to Request".</em></td><td align="right"><input type="submit" name="submit" value="Add to Request"></td></tr>

							</table>


						</td></tr>




				</table>


			</form>

</td></tr>
</table>


</body>

</html>











<? 
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

} elseif($action == 'submit_leave_request') { 

$leave_request_ID = $_GET['leave_request_ID'];
$current_id = $_GET['leave_request_row_ID'];
$status = $_GET['status'];
$mod = $_GET['mod'];
$spvsr = $_GET['spvsr']; // ONLY CONTAINS A VALUE IF STAFF RE-SUBMITS FROM LEAVE MENU AFTER A SPVSR OR PBA CHANGE
$pba = $_GET['pba']; // ONLY CONTAINS A VALUE IF STAFF RE-SUBMITS FROM LEAVE MENU AFTER A SPVSR OR PBA CHANGE
$spvsr_is_pba = $_GET['sisba']; // ONLY CONTAINS A VALUE IF STAFF RE-SUBMITS FROM LEAVE MENU AFTER A SPVSR OR PBA CHANGE -- THIS VARIABLE INDICATES IF SPVSR IS STILL PBA OR NOT
if($status == 'revised'){
$revised_flag = 'revised ';
}else{
$revised_flag = '';
}

$trigger = rand();
#################################################
## START: UPDATE THE LEAVE REQUEST ##
#################################################

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','leave_requests2');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);
$update -> AddDBParam('signer_status_owner','1');

if($status == 'revised'){
$update -> AddDBParam('approval_status','Revised');
}else{
$update -> AddDBParam('approval_status','Pending');
}

$update -> AddDBParam('signer_timestamp_owner_trigger',$trigger);

if($mod == 'new_spvsr'){
$update -> AddDBParam('signer_ID_imm_spvsr',$spvsr);
$update -> AddDBParam('signer_status_imm_spvsr','');
$update -> AddDBParam('signer_timestamp_imm_spvsr','');
$update -> AddDBParam('signer_ID_pba',$pba);
$update -> AddDBParam('signer_status_pba','');
$update -> AddDBParam('signer_timestamp_pba','');
$update -> AddDBParam('signer_imm_spvsr_is_pba',$spvsr_is_pba);
}

$updateResult = $update -> FMEdit();

//  echo  '<p>errorCode: '.$updateResult['errorCode'];
//  echo  '<p>foundCount: '.$updateResult['foundCount'];
$recordData = current($updateResult['data']);

if($recordData['leave_requests_staff_byStaffID::time_leave_admin_sims_user_ID'][0] != ''){
$time_leave_admin_email = $recordData['leave_requests_staff_byStaffID::time_leave_admin_sims_user_ID'][0].'@sedl.org';
}else{
$time_leave_admin_email = '';
}

$lv_submit_cc_email = $recordData['leave_requests_staff_byStaffID::lv_submit_cc'][0].'@sedl.org';


if($updateResult['errorCode'] == '0'){  // THE LEAVE REQUEST WAS SUCCESSFULLY UPDATED

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SIGN_LEAVE_REQUEST_STAFF');
$newrecord -> AddDBParam('table','LEAVE_REQUESTS');
$newrecord -> AddDBParam('object_ID',$recordData['leave_request_ID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$current_id);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];



$_SESSION['timesheet_signed_staff'] = '1';

	if($recordData['leave_requests_staff_byStaffID::no_time_leave_approval_required'][0] == '1'){ //CEO STATUS CHECK

		$_SESSION['timesheet_owner_email'] = $recordData['signer_ID_owner'][0].'@sedl.org';
		
		###############################################
		## START: TRIGGER NOTIFICATION E-MAIL TO CEO ##
		###############################################
		
		
				//SEND E-MAIL APPROVED NOTIFICATION TO STAFF MEMBER AND OFTS
				
					$to = $_SESSION['timesheet_owner_email'].',maria.turner@sedl.org';
					$subject = 'Your leave request has been approved.';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0].','."\n\n".
					
					'The leave request you submitted for the pay period ending '.$recordData['pay_period_end'][0].' has been approved. No further action is necessary on your part.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' LEAVE REQUEST DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n\n".
					
					'Hours Detail:'."\n".
					$recordData['c_leave_hrs_date_num_type_valuelist'][0].']'."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view this leave request or print a copy for your records, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_spvsr.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
					
					'---------------------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'---------------------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
				
				
				
				
		#############################################
		## END: TRIGGER NOTIFICATION E-MAIL TO CEO ##
		#############################################

	} elseif($recordData['signer_imm_spvsr_is_pba'][0] == '0') { //IF STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PBA IS NOT THE SAME PERSON


		$_SESSION['imm_spvsr_email'] = $recordData['signer_ID_imm_spvsr'][0].'@sedl.org';
		
		if($recordData['leave_requests_staff_byStaffID::lv_submit_cc'][0] == ''){
		$send_to = stripslashes($_SESSION['imm_spvsr_email']);
		}else{
		$send_to = stripslashes($_SESSION['imm_spvsr_email']).','.$lv_submit_cc_email;
		}
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO SUPERVISOR ##
		######################################################
		
		
				//SEND E-MAIL NOTIFICATION TO IMMEDIATE SUPERVISOR
				
					$to = $send_to;
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a leave request requiring your approval';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_fullname_imm_spvsr'][0].','."\n\n".
					
					'A leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' LEAVE REQUEST DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n\n".
					
					'Hours Detail:'."\n".
					$recordData['c_leave_hrs_date_num_type_valuelist'][0].']'."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To approve this leave request, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_spvsr.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
										
					'---------------------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'---------------------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO SUPERVISOR ##
		####################################################
		if($time_leave_admin_email != ''){
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO ADMIN ##
		######################################################
		
		
				//SEND E-MAIL NOTIFICATION TO ADMIN
				
					$to = $time_leave_admin_email;
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a leave request';
					$message = 
					'SEDL Leave Admin: '."\n\n".
					
					'A leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' LEAVE REQUEST DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n\n".
					
					'Hours Detail:'."\n".
					$recordData['c_leave_hrs_date_num_type_valuelist'][0].']'."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view or print this leave request, log in to SIMS and click the "Print leave requests" link in the Workgroup Admin section: '."\n".
					'Login: http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".
										
					'---------------------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'---------------------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO ADMIN ##
		####################################################
	}

	} elseif($recordData['signer_imm_spvsr_is_pba'][0] == '1') { //IF STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PBA IS THE SAME PERSON


		$_SESSION['pba_email'] = $recordData['signer_ID_pba'][0].'@sedl.org';
		
		if($recordData['leave_requests_staff_byStaffID::lv_submit_cc'][0] == ''){
		$send_to = stripslashes($_SESSION['pba_email']);
		}else{
		$send_to = stripslashes($_SESSION['pba_email']).','.$lv_submit_cc_email;
		}

		
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO PBA ##
		######################################################
		
		
					//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY
					
					$to = $send_to;
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a leave request requiring your approval';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_fullname_pba'][0].','."\n\n".
					
					'A leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' LEAVE REQUEST DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n\n".
					
					'Hours Detail:'."\n".
					$recordData['c_leave_hrs_date_num_type_valuelist'][0].']'."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To approve this leave request, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_ba.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
					
					'---------------------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'---------------------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO PBA ##
		####################################################
		if($time_leave_admin_email != ''){
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO ADMIN ##
		######################################################
		
		
				//SEND E-MAIL NOTIFICATION TO ADMIN
				
					$to = $time_leave_admin_email;
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a leave request';
					$message = 
					'SEDL Leave Admin: '."\n\n".
					
					'A leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' LEAVE REQUEST DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n\n".
					
					'Hours Detail:'."\n".
					$recordData['c_leave_hrs_date_num_type_valuelist'][0].']'."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view or print this leave request, log in to SIMS and click the "Print leave requests" link in the Workgroup Admin section: '."\n".
					'Login: http://www.sedl.org/staff/sims/sims_menu.php?src=intr'."\n\n".
										
					'---------------------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'---------------------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO ADMIN ##
		####################################################
	}

	}else{
	echo 'Error 2276 - <a href="mailto:eric.waters@sedl.org?subject=SIMS_Error_2277 - leave_request.php">contact technical assistance</a>';
	
	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','Error_2276: leave_request.php');
	$newrecord -> AddDBParam('table','LEAVE_REQUESTS');
	$newrecord -> AddDBParam('object_ID',$recordData['leave_request_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

	
	exit;
	}











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

} else { // THERE WAS AN ERROR UPDATING THE LEAVE REQUEST
$_SESSION['timesheet_signed_staff'] = '2';
$_SESSION['last_error'] = $updateResult['errorCode'];

}
###############################################
## END: UPDATE THE LEAVE REQUEST ##
###############################################

header('Location: http://www.sedl.org/staff/sims/menu_leave.php');
exit;
?>
<?php } else { 
echo 'Error_3327';

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','Error_3327: leave_request.php');
	$newrecord -> AddDBParam('table','LEAVE_REQUESTS');
	$newrecord -> AddDBParam('object_ID',$recordData['leave_request_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$current_id);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

 } ?>

<?php 

if($debug == 'on'){

echo '<p>$action: '.$action;
echo '<p>$delete_request_row: '.$delete_request_row;
echo '<p>$add_to_request: '.$add_to_request;
echo '<p>$leave_request_ID: '.$leave_request_ID;
echo '<p>$timesheet_ID: '.$timesheet_ID;
echo '<p>$_SESSION[leave_request_ID]: '.$_SESSION['leave_request_ID'];
echo '<p>$day_from: '.$day_from;
echo '<p>$day_to: '.$day_to;
echo '<p>$time_from: '.$time_from;
echo '<p>$time_to: '.$time_to;
echo '<p>$num_hrs: '.$num_hrs;
echo '<p>$date_from_m: '.$date_from_m;
echo '<p>$date_from_y: '.$date_from_y;

}
?>
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

if($debug == 'on'){
echo '<p>$_SESSION[leave_date_requested]: '.$_SESSION['leave_date_requested'];
echo '<p>$_SESSION[return_date_requested]: '.$_SESSION['return_date_requested'];
echo '<p>$_SESSION[leave_time_requested]: '.$_SESSION['leave_time_requested'];
echo '<p>$_SESSION[return_time_requested]: '.$_SESSION['return_time_requested'];

echo '<p>$_SESSION[num_dest]: '.$_SESSION['num_dest'];

echo '<p>$_SESSION[event_venue_state1]: '.$_SESSION['event_venue_state1'];
echo '<p>$_SESSION[event_venue_state2]: '.$_SESSION['event_venue_state2'];
echo '<p>$_SESSION[event_venue_state3]: '.$_SESSION['event_venue_state3'];
echo '<p>$_SESSION[event_venue_state4]: '.$_SESSION['event_venue_state4'];
echo '<p>$_SESSION[event_venue_state5]: '.$_SESSION['event_venue_state5'];
echo '<p>$_SESSION[event_venue_state6]: '.$_SESSION['event_venue_state6'];

echo '<p>$_SESSION[event_venue_city1]: '.$_SESSION['event_venue_city1'];
echo '<p>$_SESSION[event_venue_city2]: '.$_SESSION['event_venue_city2'];
echo '<p>$_SESSION[event_venue_city3]: '.$_SESSION['event_venue_city3'];
echo '<p>$_SESSION[event_venue_city4]: '.$_SESSION['event_venue_city4'];
echo '<p>$_SESSION[event_venue_city5]: '.$_SESSION['event_venue_city5'];
echo '<p>$_SESSION[event_venue_city6]: '.$_SESSION['event_venue_city6'];

echo '<p>$_SESSION[state]: '.$_SESSION['state'];
}
$action = $_GET['action'];
$travel_auth_ID = $_GET['travel_auth_ID'];
$approval_status = $_GET['app'];


if($action == 'view'){ //IF THE USER IS VIEWING THIS SINGLE-DESTINATION TRAVEL REQUEST


#################################################################
## START: FIND SELECTED TRAVEL REQUEST ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','travel_authorizations');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
//$search -> AddDBParam('-lop','or');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//echo '<p>$recordData[event_name]: '.$recordData['event_name'][0];
###############################################################
## END: FIND SELECTED TRAVEL REQUEST ##
###############################################################

##################################################################
## START: RE-CREATE ARRAY OF TRAVEL DATES
##################################################################
$start_date = date("m/d/Y",mktime(0,0,0,$recordData['c_leave_date_requested_m'][0],$recordData['c_leave_date_requested_d'][0],$recordData['c_leave_date_requested_y'][0]));
$end_date = date("m/d/Y",mktime(0,0,0,$recordData['c_return_date_requested_m'][0],$recordData['c_return_date_requested_d'][0],$recordData['c_return_date_requested_y'][0]));

$i=0;
while ($temp <> $end_date) {
$temp = date("m/d/Y", mktime(0, 0, 0, $recordData['c_leave_date_requested_m'][0], $recordData['c_leave_date_requested_d'][0]+$i, $recordData['c_leave_date_requested_y'][0]));
$travel_days[$i] = $temp;
$i++;
};
//echo '<p>';
//print_r($travel_days);
$_SESSION['travel_days'] = $travel_days;
##################################################################
## END: RE-CREATE ARRAY OF TRAVEL DATES
##################################################################


if($approval_status != 'Approved'){
############################################################
## START: GRAB BUDGET CODES FOR BUDGET CODE CHECKBOXES ##
############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','budget_code_usage','all');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('staff_ID',$_SESSION['staff_ID']);

$search2 -> AddSortParam('budget_code','ascend');

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);
##########################################################
## END: GRAB BUDGET CODES FOR BUDGET CODE CHECKBOXES ##
##########################################################

###############################################################
## START: GRAB ALL ACTIVE BUDGET CODES ##
###############################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('c_Active_Status','Active');

$search3 -> AddSortParam('c_BudgetCode','ascend');

$searchResult3 = $search3 -> FMFind();

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//$recordData3 = current($searchResult3['data']);
#############################################################
## END: GRAB ALL ACTIVE BUDGET CODES ##
#############################################################
}



?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: My Travel Requests</title>
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

<table cellpadding="0" cellspacing="0" border="0" width="930">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests</h1><hr /></td></tr>
			
			<?php if($_SESSION['leave_request_signed_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your travel request has been successfully submitted to SIMS.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['leave_request_signed_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your travel request, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>
			
			<?php } elseif($recordData['approval_status'][0] == 'Pending'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small"><strong>Staff Member <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong> - This travel request is pending.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>

			<?php } elseif($recordData['approval_status'][0] == 'Approved'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small"><strong>Staff Member <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong> - This travel request has been approved. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>

			<?php } ?>
			
			
			<tr><td colspan="2">
			

<?php 
#########################################################################################
## START: SHOW EDITABLE "TRAVEL REQUEST" FORM IF THE REQUEST HAS NOT YET BEEN APPROVED ##
#########################################################################################
if($recordData['approval_status'][0] != 'Approved'){
?>


						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>Travel Request Status: <?php echo $recordData['approval_status'][0];?></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL REQUEST</strong></td><td align="right">Request ID: <?php echo $recordData['travel_auth_ID'][0];?> | <a href="" target="_blank">Print</a> | <a href="menu_travel.php">Close</a></td></tr>
						
						<tr><td colspan="2" align="right">
						<form name="travel1" method="GET" onsubmit="return checkFields()">
						<input type="hidden" name="action" value="edit_confirm">
						<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
						<input type=button value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Save Changes/Re-submit Form"><br>
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Purpose:</td>
								<td width="100%">
								<input type="checkbox" name="purpose_of_travel[]" value="Data Collection"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Data Collection") !== false) {
								echo ' checked="checked"';
								}
								?>> Data Collection &nbsp;&nbsp;&nbsp;
								
								<input type="checkbox" name="purpose_of_travel[]" value="Evaluation"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Evaluation") !== false) {
								echo ' checked="checked"';
								}
								?>> Evaluation &nbsp;&nbsp;&nbsp;
								
								<input type="checkbox" name="purpose_of_travel[]" value="Meeting"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Meeting") !== false) {
								echo ' checked="checked"';
								}
								?>> Meeting &nbsp;&nbsp;&nbsp;

								<input type="checkbox" name="purpose_of_travel[]" value="Presentation"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Presentation") !== false) {
								echo ' checked="checked"';
								}
								?>> Presentation &nbsp;&nbsp;&nbsp;

								<input type="checkbox" name="purpose_of_travel[]" value="Provide PD/TA"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Provide PD/TA") !== false) {
								echo ' checked="checked"';
								}
								?>> Provide PD/TA &nbsp;&nbsp;&nbsp;

								<input type="checkbox" name="purpose_of_travel[]" value="Receive PD/TA"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Receive PD/TA") !== false) {
								echo ' checked="checked"';
								}
								?>> Receive PD/TA &nbsp;&nbsp;&nbsp;

								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Description:</td>
								<td width="100%"><input type="text" name="purpose_of_travel_descr" size="45" value="<?php echo stripslashes($recordData['purpose_of_travel_descr'][0]);?>"></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name" size="45" value="<?php echo stripslashes($recordData['event_name'][0]);?>"></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue" size="45" value="<?php echo stripslashes($recordData['event_venue'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr" size="45" value="<?php echo stripslashes($recordData['event_venue_addr'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Destination:</td>
								<td width="100%"><strong><?php echo $recordData['event_venue_city'][0];?>, <?php echo stripslashes($recordData['event_venue_state'][0]);?></strong></td>
								</tr>								

								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event Start Date:</td>
								<td>
								<select name="event_start_date" class="body">
								<option value="">Start</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_start_date'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time" size="10" value="<?php echo $recordData['event_start_time'][0];?>">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event End Date:
								
								
								
								</td><td>
								<select name="event_end_date" class="body">
								<option value="">End</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_end_date'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time" size="10" value="<?php echo $recordData['event_end_time'][0];?>">
								
								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Requested Leave Date:</td>
								<td>
								<select name="leave_date_requested" class="body">
								<option value="">Depart</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['leave_date_requested'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Requested Leave Time: <input type="text" name="leave_time_requested" size="10" value="<?php echo $recordData['leave_time_requested'][0];?>">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Requested Return Date:
								
								
								
								</td><td>
								<select name="return_date_requested" class="body">
								<option value="">Return</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['return_date_requested'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Requested Return Time: <input type="text" name="return_time_requested" size="10" value="<?php echo $recordData['return_time_requested'][0];?>">
								
								</td></tr>
								

								<tr><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_name'][0]);?>"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel" value="yes"<?php if ($recordData['preferred_hotel_is_conf_hotel'][0] == 'yes') { echo ' checked="checked"';}?>> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel" value="no"<?php if ($recordData['preferred_hotel_is_conf_hotel'][0] == 'no') { echo ' checked="checked"';}?>> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_other_justification'][0]);?>">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_addr'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city" size="20" value="<?php echo stripslashes($recordData['preferred_hotel_city'][0]);?>"> <input type="text" name="preferred_hotel_state" size="5" value="<?php echo $recordData['preferred_hotel_state'][0];?>"> <input type="text" name="preferred_hotel_zip" size="10" value="<?php echo $recordData['preferred_hotel_zip'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone" size="20" value="<?php echo $recordData['preferred_hotel_phone'][0];?>"> <input type="text" name="preferred_hotel_fax" size="20" value="<?php echo $recordData['preferred_hotel_fax'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested" size="5" value="<?php echo $recordData['hotel_nights_requested'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate" size="5" value="<?php echo $recordData['hotel_rate'][0];?>"> <em>(per night)</em></td>
								</tr>								


								<tr><td colspan="2"><strong>Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">

								<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
								
									<input type="checkbox" name="budget_code[]" value="<?php echo $searchData2['budget_code'][0];?>"<?php 
								if (strpos($recordData['budget_code'][0],$searchData2['budget_code'][0]) !== false) {
								echo ' checked="checked"';
								}
								?>><?php echo $searchData2['budget_code'][0];?></input><span class="tiny"> | <?php echo $searchData2['Budget_Code_Nickname'][0];?></span><br>
								
								<?php } ?>

								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">FFS (087):</td>
								<td width="100%">

								<select name="budget_code_FFS_code" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData3) { 
								if($searchData3['Fund'][0] == '087'){
								?>
								
									<option value="<?php echo $searchData3['c_BudgetCode'][0];?>"<?php if ($recordData['budget_code_FFS_code'][0] == $searchData3['c_BudgetCode'][0]) { echo ' SELECTED';}?>> <?php echo $searchData3['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Fee for Service (pre-FY2009)</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CPL (088):</td>
								<td width="100%">

								<select name="budget_code_CPL_code" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData4) { 
								if($searchData4['Fund'][0] == '088'){
								?>
								
									<option value="<?php echo $searchData4['c_BudgetCode'][0];?>"<?php if ($recordData['budget_code_CPL_code'][0] == $searchData4['c_BudgetCode'][0]) { echo ' SELECTED';}?>> <?php echo $searchData4['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Center for Professional Learning</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">Other:</td>
								<td width="100%">

								<select name="budget_code_other" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData5) { 
								if(($searchData5['Fund'][0] != '087')&&($searchData5['Fund'][0] != '088')){
								?>
								
									<option value="<?php echo $searchData5['c_BudgetCode'][0];?>"<?php if ($recordData['budget_code_other'][0] == $searchData5['c_BudgetCode'][0]) { echo ' SELECTED';}?>> <?php echo $searchData5['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Select from all active budget codes</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><input type="text" name="budget_code_instructions" size="30" value="<?php echo stripslashes($recordData['budget_code_instructions'][0]);?>"> <em>Example: "Charge 50% to each code"</em></td>
								</tr>								

								<tr><td colspan="2"><strong>Transportation</strong>: <em>How will you be traveling?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized" value="yes"<?php if ($recordData['trans_pers_veh_utilized'][0] == 'yes') { echo ' checked="checked"';}?>> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage" size="5" value="<?php echo $recordData['trans_pers_veh_approx_mileage'][0];?>"><p>
								<input type="checkbox" name="trans_airline_requested" value="yes"<?php if ($recordData['trans_airline_requested'][0] == 'yes') { echo ' checked="checked"';}?>> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier" size="10" value="<?php echo stripslashes($recordData['trans_airline_preferred_carrier'][0]);?>"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid" value="yes"<?php if ($recordData['trans_airline_bta_prepaid'][0] == 'yes') { echo ' checked="checked"';}?>> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested" value="yes"<?php if ($recordData['trans_rental_car_requested'][0] == 'yes') { echo ' checked="checked"';}?>> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested" size="5" value="<?php echo $recordData['trans_rental_car_num_days_requested'][0];?>"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification" size="20" value="<?php echo stripslashes($recordData['trans_rental_car_justification'][0]);?>"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff" value="yes"<?php if ($recordData['trans_traveling_with_other_staff'][0] == 'yes') { echo ' checked="checked"';}?>> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name" size="20" value="<?php echo stripslashes($recordData['trans_traveling_with_name'][0]);?>"><p>
								<input type="checkbox" name="travel_advance_requested" value="yes"<?php if ($recordData['travel_advance_requested'][0] == 'yes') { echo ' checked="checked"';}?>> Travel advance requested<p>
								</td>
								</tr>								


								<tr><td colspan="2"><strong>Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
								approving your request or to support staff in making your travel arrangements.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><input type="text" name="other_information" size="100%" value="<?php echo stripslashes($recordData['other_information'][0]);?>"></td>
								</tr>								



								<tr><td colspan="2" align="right"><input type=button value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Save Changes/Re-submit Form"></td></tr>
								


							</table>

<?php 
#########################################################################################
## END: SHOW EDITABLE "TRAVEL REQUEST" FORM IF THE REQUEST HAS NOT YET BEEN APPROVED ##
#########################################################################################
} elseif($recordData['approval_status'][0] == 'Approved'){
#########################################################################################
## START: SHOW READ-ONLY "TRAVEL AUTHORIZATION" FORM IF THE REQUEST HAS BEEN APPROVED ##
#########################################################################################

?>


						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>Travel Request Status: <?php echo $recordData['approval_status'][0];?></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL REQUEST</strong></td><td align="right">Request ID: <?php echo $recordData['travel_auth_ID'][0];?> <?php if($recordData['approval_status'][0] == 'Approved'){ ?>| <img src="/staff/sims/images/padlock.jpg" border="0" title="This travel request is locked."> <?php }?>| <a href="" target="_blank">Print</a> | <a href="menu_travel.php">Close</a></td></tr>
						
						<tr><td colspan="2" align="right">

							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">Purpose:</td>
								<td width="100%">
								
								<?php
								$purpose = explode("\n",$recordData['purpose_of_travel'][0]);
								for($i=0 ; $i<count($purpose) ; $i++) {
								echo $purpose[$i].'<br/>'; 
								} 
								
								?>



								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Name(s) of Event(s):</td>
								<td width="100%"><?php echo $recordData['event_name'][0];?></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event Venue:</td>
								<td width="100%"><?php echo $recordData['event_venue'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event Address:</td>
								<td width="100%"><?php echo $recordData['event_venue_addr'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event City/State:</td>
								<td width="100%"><?php echo $recordData['event_venue_city'][0];?>, <?php echo $recordData['event_venue_state'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event Start Date/Time:</td>
								<td width="100%"><?php echo $recordData['event_start_date'][0];?> | <?php echo $recordData['event_start_time'][0];?></td>
								</tr>
								
								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event End Date/Time:</td>
								<td width="100%"><?php echo $recordData['event_end_date'][0];?> | <?php echo $recordData['event_end_time'][0];?></td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Requested Leave Date/Time:</td>
								<td width="100%"><?php echo $recordData['leave_date_requested'][0];?> | <?php echo $recordData['leave_time_requested'][0];?></td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Requested Return Date/Time:</td>
								<td width="100%"><?php echo $recordData['return_date_requested'][0];?> | <?php echo $recordData['return_time_requested'][0];?></td>
								</tr>
								
								<tr><td colspan="2"><strong>Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>My Budget Codes:</em></td>
								<td width="100%">

								<?php
								$budget_code = explode("\n",$recordData['budget_code'][0]);
								for($i=0 ; $i<count($budget_code) ; $i++) {
								echo $budget_code[$i].'<br/>'; 
								} 
								
								?>



								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">FFS (087):</td>
								<td width="100%">

								<?php
								echo $recordData['budget_code_FFS_code'][0]; 
								 ?>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CPL (088):</td>
								<td width="100%">

								<?php
								echo $recordData['budget_code_CPL_code'][0]; 
								 ?>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">Other:</td>
								<td width="100%">

								<?php echo $recordData['budget_code_other'][0]; ?>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><?php echo $recordData['budget_code_instructions'][0];?></td>
								</tr>								

								<tr><td colspan="2"><strong>Transportation</strong>: <em>How will you be traveling?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Selected Preferences:</em></td>
								<td width="100%">
								Driving Personal Vehicle? <strong><?php echo $recordData['trans_pers_veh_utilized'][0];?></strong> | <em>Approximate mileage:</em> <strong><?php echo $recordData['trans_pers_veh_approx_mileage'][0];?></strong><p>
								Airline? <strong><?php echo $recordData['trans_airline_requested'][0];?></strong> | <em>Preferred carrier:</em> <strong><?php echo $recordData['trans_airline_preferred_carrier'][0];?></strong><br>
								&nbsp;&nbsp;&nbsp;Charge airline fare to SEDL BTA account (Pre-paid)? <strong><?php echo $recordData['trans_airline_bta_prepaid'][0];?></strong><p>
								Rental Car Requested? <strong><?php echo $recordData['trans_rental_car_requested'][0];?></strong> | <em>Number of days: <strong><?php echo $recordData['trans_rental_car_num_days_requested'][0];?></strong> | Justification:</em> <strong><?php echo $recordData['trans_rental_car_justification'][0];?></strong><p>
								Traveling with other person(s)? <strong><?php echo $recordData['trans_traveling_with_other_staff'][0];?></strong> | <em>Name(s):</em> <strong><?php echo $recordData['trans_traveling_with_name'][0];?></strong><p>
								Travel advance requested? <strong><?php echo $recordData['travel_advance_requested'][0];?><p>
								</td>
								</tr>								

								<tr><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Preferred Hotel:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_name'][0];?><p>
								Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel'][0];?></strong><p>
								Other justification for using this hotel: <br>
								<strong><?php echo $recordData['preferred_hotel_other_justification'][0];?></strong>
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Hotel Address:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_addr'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">City/State/Zip:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_city'][0];?>, <?php echo $recordData['preferred_hotel_state'][0];?> <?php echo $recordData['preferred_hotel_zip'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Phone/Fax:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_phone'][0];?> | <?php echo $recordData['preferred_hotel_fax'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Number of Nights:</td>
								<td width="100%"><?php echo $recordData['hotel_nights_requested'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Hotel Rate:</td>
								<td width="100%">$<?php echo $recordData['hotel_rate'][0];?> <em>(per night)</em></td>
								</tr>								

								<tr><td colspan="2"><strong>Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
								approving your request or to support staff in making your travel arrangements.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><?php echo $recordData['other_information'][0];?></td>
								</tr>								



								
								


							</table>


<?php 
}
#########################################################################################
## END: SHOW READ-ONLY "TRAVEL AUTHORIZATION" FORM IF THE REQUEST HAS BEEN APPROVED ##
#########################################################################################

?>
						

						</td></tr>


						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>



<?php } elseif($action == 'view_multi'){ //IF THE USER IS VIEWING THIS MULTI-DESTINATION TRAVEL REQUEST


#################################################################
## START: FIND SELECTED TRAVEL REQUEST ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','travel_authorizations');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
//$search -> AddDBParam('-lop','or');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//echo '<p>$recordData[event_name]: '.$recordData['event_name'][0];
###############################################################
## END: FIND SELECTED TRAVEL REQUEST ##
###############################################################
if($approval_status != 'Approved'){
############################################################
## START: GRAB BUDGET CODES FOR BUDGET CODE CHECKBOXES ##
############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','budget_code_usage','all');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('staff_ID',$_SESSION['staff_ID']);

$search2 -> AddSortParam('budget_code','ascend');

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);
##########################################################
## END: GRAB BUDGET CODES FOR BUDGET CODE CHECKBOXES ##
##########################################################

###############################################################
## START: GRAB ALL ACTIVE BUDGET CODES ##
###############################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('c_Active_Status','Active');

$search3 -> AddSortParam('c_BudgetCode','ascend');

$searchResult3 = $search3 -> FMFind();

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//$recordData3 = current($searchResult3['data']);
#############################################################
## END: GRAB ALL ACTIVE BUDGET CODES ##
#############################################################

##################################################################
## START: RE-CREATE ARRAY OF TRAVEL DATES
##################################################################
$start_date = date("m/d/Y",mktime(0,0,0,$recordData['c_leave_date_requested_m'][0],$recordData['c_leave_date_requested_d'][0],$recordData['c_leave_date_requested_y'][0]));
$end_date = date("m/d/Y",mktime(0,0,0,$recordData['c_return_date_requested_m'][0],$recordData['c_return_date_requested_d'][0],$recordData['c_return_date_requested_y'][0]));

$i=0;
while ($temp <> $end_date) {
$temp = date("m/d/Y", mktime(0, 0, 0, $recordData['c_leave_date_requested_m'][0], $recordData['c_leave_date_requested_d'][0]+$i, $recordData['c_leave_date_requested_y'][0]));
$travel_days[$i] = $temp;
$i++;
};
//echo '<p>';
//print_r($travel_days);
$_SESSION['travel_days'] = $travel_days;
##################################################################
## END: RE-CREATE ARRAY OF TRAVEL DATES
##################################################################

}



?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND### #######################################

From here to new_dot is what is already in database. go to 2307 to start new travel request
#####################################################################################################################-->


<html>
<head>
<title>SIMS: My Travel Requests</title>
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

<table cellpadding="0" cellspacing="0" border="0" width="930">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests</h1><hr /></td></tr>
			
			<?php if($_SESSION['leave_request_signed_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your travel request has been successfully submitted to SIMS.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['leave_request_signed_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your travel request, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>
			
			<?php } elseif($recordData['approval_status'][0] == 'Pending'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small"><strong>Staff Member: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong> - This travel request is pending.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>

			<?php } elseif($recordData['approval_status'][0] == 'Approved'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small"><strong>Staff Member: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong> - This travel request has been approved. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>

			<?php } ?>
			
			
			<tr><td colspan="2">
			

<?php 
#########################################################################################
## START: SHOW EDITABLE "TRAVEL REQUEST" FORM IF THE REQUEST HAS NOT YET BEEN APPROVED ##
#########################################################################################
if($recordData['approval_status'][0] != 'Approved'){
?>


						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>Travel Request Status: <?php echo $recordData['approval_status'][0];?></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL REQUEST (MULTI-DESTINATION)</strong></td><td align="right">Request ID: <?php echo $recordData['travel_auth_ID'][0];?> | <a href="" target="_blank">Print</a> | <a href="menu_travel.php">Close</a></td></tr>
						
						<tr><td colspan="2" align="right">
						<form name="travel1" method="GET" onsubmit="return checkFields()">
						<input type="hidden" name="action" value="edit_confirm_multi">
						<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
						<input type="hidden" name="num_dest" value="<?php echo $recordData['num_dest'][0];?>">
						<input type=button value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Save Changes/Re-submit Form"><br>
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Purpose:</td>
								<td width="100%">
								<input type="checkbox" name="purpose_of_travel[]" value="Data Collection"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Data Collection") !== false) {
								echo ' checked="checked"';
								}
								?>> Data Collection &nbsp;&nbsp;&nbsp;
								
								<input type="checkbox" name="purpose_of_travel[]" value="Evaluation"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Evaluation") !== false) {
								echo ' checked="checked"';
								}
								?>> Evaluation &nbsp;&nbsp;&nbsp;
								
								<input type="checkbox" name="purpose_of_travel[]" value="Meeting"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Meeting") !== false) {
								echo ' checked="checked"';
								}
								?>> Meeting &nbsp;&nbsp;&nbsp;

								<input type="checkbox" name="purpose_of_travel[]" value="Presentation"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Presentation") !== false) {
								echo ' checked="checked"';
								}
								?>> Presentation &nbsp;&nbsp;&nbsp;

								<input type="checkbox" name="purpose_of_travel[]" value="Provide PD/TA"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Provide PD/TA") !== false) {
								echo ' checked="checked"';
								}
								?>> Provide PD/TA &nbsp;&nbsp;&nbsp;

								<input type="checkbox" name="purpose_of_travel[]" value="Receive PD/TA"<?php 
								if (strpos($recordData['purpose_of_travel'][0],"Receive PD/TA") !== false) {
								echo ' checked="checked"';
								}
								?>> Receive PD/TA &nbsp;&nbsp;&nbsp;

								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Description:</td>
								<td width="100%"><input type="text" name="purpose_of_travel_descr" size="45" value="<?php echo stripslashes($recordData['purpose_of_travel_descr'][0]);?>"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Requested Leave Date:</td>
								<td>

								<select name="leave_date_requested" class="body">
								<option value="">Depart</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['leave_date_requested'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Requested Leave Time: <input type="text" name="leave_time_requested" size="10" value="<?php echo $recordData['leave_time_requested'][0];?>">

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Requested Return Date:
								
								
								
								</td><td>
								<select name="return_date_requested" class="body">
								<option value="">Return</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['return_date_requested'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Requested Return Time: <input type="text" name="return_time_requested" size="10" value="<?php echo $recordData['return_time_requested'][0];?>">
								
								</td></tr>


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 1</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $recordData['event_venue_city1'][0].', '.$recordData['event_venue_state1'][0].' - '.$recordData['event_venue_city1_travel_start'][0].' to '.$recordData['event_venue_city1_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name1" size="45" value="<?php echo stripslashes($recordData['event_name1'][0]);?>"></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue1" size="45" value="<?php echo stripslashes($recordData['event_venue1'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr1" size="45" value="<?php echo stripslashes($recordData['event_venue_addr1'][0]);?>"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>
								<select name="event_start_date1" class="body">
								<option value="">Start</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_start_date1'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time1" size="10" value="<?php echo $recordData['event_start_time1'][0];?>">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>
								<select name="event_end_date1" class="body">
								<option value="">End</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_end_date1'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time1" size="10" value="<?php echo $recordData['event_end_time1'][0];?>">
								
								</td></tr>
								
								
								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city1'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name1" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_name1'][0]);?>"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel1" value="yes"<?php if ($recordData['preferred_hotel_is_conf_hotel1'][0] == 'yes') { echo ' checked="checked"';}?>> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel1" value="no"<?php if ($recordData['preferred_hotel_is_conf_hotel1'][0] == 'no') { echo ' checked="checked"';}?>> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification1" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_other_justification1'][0]);?>">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr1" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_addr1'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city1" size="20" value="<?php echo stripslashes($recordData['preferred_hotel_city1'][0]);?>"> <input type="text" name="preferred_hotel_state1" size="5" value="<?php echo $recordData['preferred_hotel_state1'][0];?>"> <input type="text" name="preferred_hotel_zip1" size="10" value="<?php echo $recordData['preferred_hotel_zip1'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone1" size="20" value="<?php echo $recordData['preferred_hotel_phone1'][0];?>"> <input type="text" name="preferred_hotel_fax1" size="20" value="<?php echo $recordData['preferred_hotel_fax1'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested1" size="5" value="<?php echo $recordData['hotel_nights_requested1'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate1" size="5" value="<?php echo $recordData['hotel_rate1'][0];?>"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments1" size="45" value="<?php echo $recordData['destination_comments1'][0];?>"></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 2</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $recordData['event_venue_city2'][0].', '.$recordData['event_venue_state2'][0].' - '.$recordData['event_venue_city2_travel_start'][0].' to '.$recordData['event_venue_city2_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name2" size="45" value="<?php echo stripslashes($recordData['event_name2'][0]);?>"></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue2" size="45" value="<?php echo stripslashes($recordData['event_venue2'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr2" size="45" value="<?php echo stripslashes($recordData['event_venue_addr2'][0]);?>"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>
								<select name="event_start_date2" class="body">
								<option value="">Start</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_start_date2'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time2" size="10" value="<?php echo $recordData['event_start_time2'][0];?>">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>
								<select name="event_end_date2" class="body">
								<option value="">End</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_end_date2'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time2" size="10" value="<?php echo $recordData['event_end_time2'][0];?>">
								
								</td></tr>
								
								
								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city2'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name2" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_name2'][0]);?>"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel2" value="yes"<?php if ($recordData['preferred_hotel_is_conf_hotel2'][0] == 'yes') { echo ' checked="checked"';}?>> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel2" value="no"<?php if ($recordData['preferred_hotel_is_conf_hotel2'][0] == 'no') { echo ' checked="checked"';}?>> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification2" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_other_justification2'][0]);?>">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr2" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_addr2'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city2" size="20" value="<?php echo stripslashes($recordData['preferred_hotel_city2'][0]);?>"> <input type="text" name="preferred_hotel_state2" size="5" value="<?php echo $recordData['preferred_hotel_state2'][0];?>"> <input type="text" name="preferred_hotel_zip2" size="10" value="<?php echo $recordData['preferred_hotel_zip2'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone2" size="20" value="<?php echo $recordData['preferred_hotel_phone2'][0];?>"> <input type="text" name="preferred_hotel_fax2" size="20" value="<?php echo $recordData['preferred_hotel_fax2'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested2" size="5" value="<?php echo $recordData['hotel_nights_requested2'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate2" size="5" value="<?php echo $recordData['hotel_rate2'][0];?>"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments2" size="45" value="<?php echo $recordData['destination_comments2'][0];?>"></td>
								</tr>								


<?php if($recordData['num_dest'][0] > 2){?>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 3</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $recordData['event_venue_city3'][0].', '.$recordData['event_venue_state3'][0].' - '.$recordData['event_venue_city3_travel_start'][0].' to '.$recordData['event_venue_city3_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name3" size="45" value="<?php echo stripslashes($recordData['event_name3'][0]);?>"></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue3" size="45" value="<?php echo stripslashes($recordData['event_venue3'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr3" size="45" value="<?php echo stripslashes($recordData['event_venue_addr3'][0]);?>"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>
								<select name="event_start_date3" class="body">
								<option value="">Start</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_start_date3'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time3" size="10" value="<?php echo $recordData['event_start_time3'][0];?>">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>
								<select name="event_end_date3" class="body">
								<option value="">End</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_end_date3'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time3" size="10" value="<?php echo $recordData['event_end_time3'][0];?>">
								
								</td></tr>
								
								
								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city3'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name3" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_name3'][0]);?>"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel3" value="yes"<?php if ($recordData['preferred_hotel_is_conf_hotel3'][0] == 'yes') { echo ' checked="checked"';}?>> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel3" value="no"<?php if ($recordData['preferred_hotel_is_conf_hotel3'][0] == 'no') { echo ' checked="checked"';}?>> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification3" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_other_justification3'][0]);?>">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr3" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_addr3'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city3" size="20" value="<?php echo stripslashes($recordData['preferred_hotel_city3'][0]);?>"> <input type="text" name="preferred_hotel_state3" size="5" value="<?php echo $recordData['preferred_hotel_state3'][0];?>"> <input type="text" name="preferred_hotel_zip3" size="10" value="<?php echo $recordData['preferred_hotel_zip3'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone3" size="20" value="<?php echo $recordData['preferred_hotel_phone3'][0];?>"> <input type="text" name="preferred_hotel_fax3" size="20" value="<?php echo $recordData['preferred_hotel_fax3'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested3" size="5" value="<?php echo $recordData['hotel_nights_requested3'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate3" size="5" value="<?php echo $recordData['hotel_rate3'][0];?>"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments3" size="45" value="<?php echo $recordData['destination_comments3'][0];?>"></td>
								</tr>								


<?php } ?>



<?php if($recordData['num_dest'][0] > 3){?>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 4</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $recordData['event_venue_city4'][0].', '.$recordData['event_venue_state4'][0].' - '.$recordData['event_venue_city4_travel_start'][0].' to '.$recordData['event_venue_city4_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name4" size="45" value="<?php echo stripslashes($recordData['event_name4'][0]);?>"></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue4" size="45" value="<?php echo stripslashes($recordData['event_venue4'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr4" size="45" value="<?php echo stripslashes($recordData['event_venue_addr4'][0]);?>"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>
								<select name="event_start_date4" class="body">
								<option value="">Start</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_start_date4'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time4" size="10" value="<?php echo $recordData['event_start_time4'][0];?>">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>
								<select name="event_end_date4" class="body">
								<option value="">End</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_end_date4'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time4" size="10" value="<?php echo $recordData['event_end_time4'][0];?>">
								
								</td></tr>
								
								
								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city4'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name4" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_name4'][0]);?>"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel4" value="yes"<?php if ($recordData['preferred_hotel_is_conf_hotel4'][0] == 'yes') { echo ' checked="checked"';}?>> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel4" value="no"<?php if ($recordData['preferred_hotel_is_conf_hotel4'][0] == 'no') { echo ' checked="checked"';}?>> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification4" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_other_justification4'][0]);?>">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr4" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_addr4'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city4" size="20" value="<?php echo stripslashes($recordData['preferred_hotel_city4'][0]);?>"> <input type="text" name="preferred_hotel_state4" size="5" value="<?php echo $recordData['preferred_hotel_state4'][0];?>"> <input type="text" name="preferred_hotel_zip4" size="10" value="<?php echo $recordData['preferred_hotel_zip4'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone4" size="20" value="<?php echo $recordData['preferred_hotel_phone4'][0];?>"> <input type="text" name="preferred_hotel_fax4" size="20" value="<?php echo $recordData['preferred_hotel_fax4'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested4" size="5" value="<?php echo $recordData['hotel_nights_requested4'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate4" size="5" value="<?php echo $recordData['hotel_rate4'][0];?>"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments4" size="45" value="<?php echo $recordData['destination_comments4'][0];?>"></td>
								</tr>								


<?php } ?>



<?php if($recordData['num_dest'][0] > 4){?>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 5</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $recordData['event_venue_city5'][0].', '.$recordData['event_venue_state5'][0].' - '.$recordData['event_venue_city5_travel_start'][0].' to '.$recordData['event_venue_city5_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name5" size="45" value="<?php echo stripslashes($recordData['event_name5'][0]);?>"></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue5" size="45" value="<?php echo stripslashes($recordData['event_venue5'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr5" size="45" value="<?php echo stripslashes($recordData['event_venue_addr5'][0]);?>"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>
								<select name="event_start_date5" class="body">
								<option value="">Start</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_start_date5'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time5" size="10" value="<?php echo $recordData['event_start_time5'][0];?>">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>
								<select name="event_end_date5" class="body">
								<option value="">End</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_end_date5'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time5" size="10" value="<?php echo $recordData['event_end_time5'][0];?>">
								
								</td></tr>
								
								
								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city5'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name5" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_name5'][0]);?>"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel5" value="yes"<?php if ($recordData['preferred_hotel_is_conf_hotel5'][0] == 'yes') { echo ' checked="checked"';}?>> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel5" value="no"<?php if ($recordData['preferred_hotel_is_conf_hotel5'][0] == 'no') { echo ' checked="checked"';}?>> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification5" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_other_justification5'][0]);?>">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr5" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_addr5'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city5" size="20" value="<?php echo stripslashes($recordData['preferred_hotel_city5'][0]);?>"> <input type="text" name="preferred_hotel_state5" size="5" value="<?php echo $recordData['preferred_hotel_state5'][0];?>"> <input type="text" name="preferred_hotel_zip5" size="10" value="<?php echo $recordData['preferred_hotel_zip5'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone5" size="20" value="<?php echo $recordData['preferred_hotel_phone5'][0];?>"> <input type="text" name="preferred_hotel_fax5" size="20" value="<?php echo $recordData['preferred_hotel_fax5'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested5" size="5" value="<?php echo $recordData['hotel_nights_requested5'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate5" size="5" value="<?php echo $recordData['hotel_rate5'][0];?>"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments5" size="45" value="<?php echo $recordData['destination_comments5'][0];?>"></td>
								</tr>								


<?php } ?>


<?php if($recordData['num_dest'][0] > 5){?>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 6</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $recordData['event_venue_city6'][0].', '.$recordData['event_venue_state6'][0].' - '.$recordData['event_venue_city6_travel_start'][0].' to '.$recordData['event_venue_city6_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name6" size="45" value="<?php echo stripslashes($recordData['event_name6'][0]);?>"></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue6" size="45" value="<?php echo stripslashes($recordData['event_venue6'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr6" size="45" value="<?php echo stripslashes($recordData['event_venue_addr6'][0]);?>"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>
								<select name="event_start_date6" class="body">
								<option value="">Start</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_start_date6'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time6" size="10" value="<?php echo $recordData['event_start_time6'][0];?>">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>
								<select name="event_end_date6" class="body">
								<option value="">End</option>
								<option value="">-------------</option>
								
								<?php foreach($_SESSION['travel_days'] as $current) { ?>
								<option value="<?php echo $current;?>"<?php if ($recordData['event_end_date6'][0] == $current) { echo ' SELECTED';}?>> <?php echo $current;?>
								<?php } ?>
								</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time6" size="10" value="<?php echo $recordData['event_end_time6'][0];?>">
								
								</td></tr>
								
								
								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city6'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name6" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_name6'][0]);?>"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel6" value="yes"<?php if ($recordData['preferred_hotel_is_conf_hotel6'][0] == 'yes') { echo ' checked="checked"';}?>> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel6" value="no"<?php if ($recordData['preferred_hotel_is_conf_hotel6'][0] == 'no') { echo ' checked="checked"';}?>> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification6" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_other_justification6'][0]);?>">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr6" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_addr6'][0]);?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city6" size="20" value="<?php echo stripslashes($recordData['preferred_hotel_city6'][0]);?>"> <input type="text" name="preferred_hotel_state6" size="5" value="<?php echo $recordData['preferred_hotel_state6'][0];?>"> <input type="text" name="preferred_hotel_zip6" size="10" value="<?php echo $recordData['preferred_hotel_zip6'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone6" size="20" value="<?php echo $recordData['preferred_hotel_phone6'][0];?>"> <input type="text" name="preferred_hotel_fax6" size="20" value="<?php echo $recordData['preferred_hotel_fax6'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested6" size="5" value="<?php echo $recordData['hotel_nights_requested6'][0];?>"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate6" size="5" value="<?php echo $recordData['hotel_rate6'][0];?>"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments6" size="45" value="<?php echo $recordData['destination_comments6'][0];?>"></td>
								</tr>								


<?php } ?>


								<tr><td colspan="2"><strong>Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">

								<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
								
									<input type="checkbox" name="budget_code[]" value="<?php echo $searchData2['budget_code'][0];?>"<?php 
								if (strpos($recordData['budget_code'][0],$searchData2['budget_code'][0]) !== false) {
								echo ' checked="checked"';
								}
								?>><?php echo $searchData2['budget_code'][0];?></input><span class="tiny"> | <?php echo $searchData2['Budget_Code_Nickname'][0];?></span><br>
								
								<?php } ?>

								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">FFS (087):</td>
								<td width="100%">

								<select name="budget_code_FFS_code" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData3) { 
								if($searchData3['Fund'][0] == '087'){
								?>
								
									<option value="<?php echo $searchData3['c_BudgetCode'][0];?>"<?php if ($recordData['budget_code_FFS_code'][0] == $searchData3['c_BudgetCode'][0]) { echo ' SELECTED';}?>> <?php echo $searchData3['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Fee for Service (pre-FY2009)</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CPL (088):</td>
								<td width="100%">

								<select name="budget_code_CPL_code" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData4) { 
								if($searchData4['Fund'][0] == '088'){
								?>
								
									<option value="<?php echo $searchData4['c_BudgetCode'][0];?>"<?php if ($recordData['budget_code_CPL_code'][0] == $searchData4['c_BudgetCode'][0]) { echo ' SELECTED';}?>> <?php echo $searchData4['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Center for Professional Learning</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">Other:</td>
								<td width="100%">

								<select name="budget_code_other" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData5) { 
								if(($searchData5['Fund'][0] != '087')&&($searchData5['Fund'][0] != '088')){
								?>
								
									<option value="<?php echo $searchData5['c_BudgetCode'][0];?>"<?php if ($recordData['budget_code_other'][0] == $searchData5['c_BudgetCode'][0]) { echo ' SELECTED';}?>> <?php echo $searchData5['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Select from all active budget codes</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><input type="text" name="budget_code_instructions" size="30" value="<?php echo stripslashes($recordData['budget_code_instructions'][0]);?>"> <em>Example: "Charge 50% to each code"</em></td>
								</tr>								

								<tr><td colspan="2"><strong>Transportation</strong>: <em>How will you be traveling?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized" value="yes"<?php if ($recordData['trans_pers_veh_utilized'][0] == 'yes') { echo ' checked="checked"';}?>> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage" size="5" value="<?php echo $recordData['trans_pers_veh_approx_mileage'][0];?>"><p>
								<input type="checkbox" name="trans_airline_requested" value="yes"<?php if ($recordData['trans_airline_requested'][0] == 'yes') { echo ' checked="checked"';}?>> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier" size="10" value="<?php echo stripslashes($recordData['trans_airline_preferred_carrier'][0]);?>"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid" value="yes"<?php if ($recordData['trans_airline_bta_prepaid'][0] == 'yes') { echo ' checked="checked"';}?>> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested" value="yes"<?php if ($recordData['trans_rental_car_requested'][0] == 'yes') { echo ' checked="checked"';}?>> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested" size="5" value="<?php echo $recordData['trans_rental_car_num_days_requested'][0];?>"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification" size="20" value="<?php echo stripslashes($recordData['trans_rental_car_justification'][0]);?>"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff" value="yes"<?php if ($recordData['trans_traveling_with_other_staff'][0] == 'yes') { echo ' checked="checked"';}?>> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name" size="20" value="<?php echo stripslashes($recordData['trans_traveling_with_name'][0]);?>"><p>
								<input type="checkbox" name="travel_advance_requested" value="yes"<?php if ($recordData['travel_advance_requested'][0] == 'yes') { echo ' checked="checked"';}?>> Travel advance requested<p>
								</td>
								</tr>								


								<tr><td colspan="2"><strong>Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
								approving your request or to support staff in making your travel arrangements.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><input type="text" name="other_information" size="100%" value="<?php echo stripslashes($recordData['other_information'][0]);?>"></td>
								</tr>								



								<tr><td colspan="2" align="right"><input type=button value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Save Changes/Re-submit Form"></td></tr>
								


							</table>

<?php 
#########################################################################################
## END: SHOW EDITABLE "TRAVEL REQUEST" FORM IF THE REQUEST HAS NOT YET BEEN APPROVED ##
#########################################################################################
} elseif($recordData['approval_status'][0] == 'Approved'){
#########################################################################################
## START: SHOW READ-ONLY "TRAVEL AUTHORIZATION" FORM IF THE REQUEST HAS BEEN APPROVED ##
#########################################################################################

?>


						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>Travel Request Status: <?php echo $recordData['approval_status'][0];?></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL REQUEST</strong></td><td align="right">Request ID: <?php echo $recordData['travel_auth_ID'][0];?> <?php if($recordData['approval_status'][0] == 'Approved'){ ?>| <img src="/staff/sims/images/padlock.jpg" border="0" title="This travel request is locked."> <?php }?>| <a href="" target="_blank">Print</a> | <a href="menu_travel.php">Close</a></td></tr>
						
						<tr><td colspan="2" align="right">

							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">Purpose:</td>
								<td width="100%">
								
								<?php
								$purpose = explode("\n",$recordData['purpose_of_travel'][0]);
								for($i=0 ; $i<count($purpose) ; $i++) {
								echo $purpose[$i].'<br/>'; 
								} 
								
								?>



								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Name(s) of Event(s):</td>
								<td width="100%"><?php echo $recordData['event_name'][0];?></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event Venue:</td>
								<td width="100%"><?php echo $recordData['event_venue'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event Address:</td>
								<td width="100%"><?php echo $recordData['event_venue_addr'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event City/State:</td>
								<td width="100%"><?php echo $recordData['event_venue_city'][0];?>, <?php echo $recordData['event_venue_state'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event Start Date/Time:</td>
								<td width="100%"><?php echo $recordData['event_start_date'][0];?> | <?php echo $recordData['event_start_time'][0];?></td>
								</tr>
								
								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event End Date/Time:</td>
								<td width="100%"><?php echo $recordData['event_end_date'][0];?> | <?php echo $recordData['event_end_time'][0];?></td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Requested Leave Date/Time:</td>
								<td width="100%"><?php echo $recordData['leave_date_requested'][0];?> | <?php echo $recordData['leave_time_requested'][0];?></td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Requested Return Date/Time:</td>
								<td width="100%"><?php echo $recordData['return_date_requested'][0];?> | <?php echo $recordData['return_time_requested'][0];?></td>
								</tr>
								
								<tr><td colspan="2"><strong>Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>My Budget Codes:</em></td>
								<td width="100%">

								<?php
								$budget_code = explode("\n",$recordData['budget_code'][0]);
								for($i=0 ; $i<count($budget_code) ; $i++) {
								echo $budget_code[$i].'<br/>'; 
								} 
								
								?>



								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">FFS (087):</td>
								<td width="100%">

								<?php
								echo $recordData['budget_code_FFS_code'][0]; 
								 ?>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CPL (088):</td>
								<td width="100%">

								<?php
								echo $recordData['budget_code_CPL_code'][0]; 
								 ?>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">Other:</td>
								<td width="100%">

								<?php echo $recordData['budget_code_other'][0]; ?>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><?php echo $recordData['budget_code_instructions'][0];?></td>
								</tr>								

								<tr><td colspan="2"><strong>Transportation</strong>: <em>How will you be traveling?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Selected Preferences:</em></td>
								<td width="100%">
								Driving Personal Vehicle? <strong><?php echo $recordData['trans_pers_veh_utilized'][0];?></strong> | <em>Approximate mileage:</em> <strong><?php echo $recordData['trans_pers_veh_approx_mileage'][0];?></strong><p>
								Airline? <strong><?php echo $recordData['trans_airline_requested'][0];?></strong> | <em>Preferred carrier:</em> <strong><?php echo $recordData['trans_airline_preferred_carrier'][0];?></strong><br>
								&nbsp;&nbsp;&nbsp;Charge airline fare to SEDL BTA account (Pre-paid)? <strong><?php echo $recordData['trans_airline_bta_prepaid'][0];?></strong><p>
								Rental Car Requested? <strong><?php echo $recordData['trans_rental_car_requested'][0];?></strong> | <em>Number of days: <strong><?php echo $recordData['trans_rental_car_num_days_requested'][0];?></strong> | Justification:</em> <strong><?php echo $recordData['trans_rental_car_justification'][0];?></strong><p>
								Traveling with other person(s)? <strong><?php echo $recordData['trans_traveling_with_other_staff'][0];?></strong> | <em>Name(s):</em> <strong><?php echo $recordData['trans_traveling_with_name'][0];?></strong><p>
								Travel advance requested? <strong><?php echo $recordData['travel_advance_requested'][0];?><p>
								</td>
								</tr>								

								<tr><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Preferred Hotel:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_name'][0];?><p>
								Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel'][0];?></strong><p>
								Other justification for using this hotel: <br>
								<strong><?php echo $recordData['preferred_hotel_other_justification'][0];?></strong>
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Hotel Address:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_addr'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">City/State/Zip:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_city'][0];?>, <?php echo $recordData['preferred_hotel_state'][0];?> <?php echo $recordData['preferred_hotel_zip'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Phone/Fax:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_phone'][0];?> | <?php echo $recordData['preferred_hotel_fax'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Number of Nights:</td>
								<td width="100%"><?php echo $recordData['hotel_nights_requested'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Hotel Rate:</td>
								<td width="100%">$<?php echo $recordData['hotel_rate'][0];?> <em>(per night)</em></td>
								</tr>								

								<tr><td colspan="2"><strong>Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
								approving your request or to support staff in making your travel arrangements.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><?php echo $recordData['other_information'][0];?></td>
								</tr>								



								
								


							</table>


<?php 
}
#########################################################################################
## END: SHOW READ-ONLY "TRAVEL AUTHORIZATION" FORM IF THE REQUEST HAS BEEN APPROVED ##
#########################################################################################

?>
						

						</td></tr>


						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>



<?php } elseif($action == 'new_dot') { // GET THE STAFF MEMBER'S DATES AND TIMES OF TRAVEL ##################   Step 1. ###############

?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function checkFields() { 

	// State selector
		if (document.travel_1.num_dest.value ==""){
			alert("Please enter the number of destinations (cities) for this travel request.");
			document.travel_1.num_dest.focus();
			return false;	}

		if (document.travel_1.num_dest.value > 6){
			alert("The maximum number of destinations (cities) for one travel request is 6. If more destinations are required, create a separate travel request.");
			document.travel_1.num_dest.focus();
			return false;	}

}			



</script>




</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>Create NEW SIMS Travel Requests</h1><hr /></td></tr>
			
			
			
			<tr><td colspan="2">

			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW TRAVEL REQUEST</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>1. Dates of Travel</strong>: <em>Indicate the begin and end dates for this trip.</em></td></tr>
								
								<form name="travel_1" method="GET">
								<input type="hidden" name="action" value="new_dest">
								
								
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Requested Leave Date:</td>
								<td width="100%">
								<select name="leave_date_requested_m" class="body" id="month">
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
								
								<select name="leave_date_requested_d" class="body" id="day">
									<option value=""></option>
									
									<?php 
									for($i=1;$i<=31;$i++){?>
									<option value="<?php echo $i;?>"><?php echo $i;?></option>
									<?php
									}
									?>
									
								</select> 
								<select name="leave_date_requested_y" class="body">
								<option value=""></option>
						
									
									<option value="2012"> 2012</option>
									<option value="2013"> 2013</option>
									<option value="2014"> 2014</option>
									<option value="2015"> 2015</option>
									
								</select> 
								&nbsp;&nbsp;&nbsp;Requested Leave Time: <input type="text" name="leave_time_requested" size="10">
								
								
								<input type="radio" name="am_d" value="A.M.">A.M. &#160;&#160; 
								<input type="radio" name="am_d" value="P.M.">P.M.
								<!-- I added the am and pm because a couple of the admin. said this would be needed. _d for depart and _r for return-->

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Requested Return Date:
								
								
								
								</td><td>
								<select name="return_date_requested_m" class="body" id="month" onchange="doWork();">
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
								
								<select name="return_date_requested_d" class="body" id="day">
									<option value=""></option>
									
									<?php 
									for($i=1;$i<=31;$i++){?>
									<option value="<?php echo $i;?>"><?php echo $i;?></option>
									<?php
									}
									?>
									
								</select> 
								<select name="return_date_requested_y" class="body">
								<option value=""></option>
						
									<option value="2012"> 2012</option>
									<option value="2013"> 2013</option>
									<option value="2014"> 2014</option>
									<option value="2015"> 2015</option>

								</select> 
								&nbsp;&nbsp;&nbsp;Requested Return Time: <input type="text" name="return_time_requested" size="10">
								<input type="radio" name="am_r" value="A.M.">A.M. &#160;&#160; 
								<input type="radio" name="am_r" value="P.M.">P.M.
								</td></tr>
								
								<tr><td bgcolor="#ebebeb">&nbsp;</td><td><input type="submit" name="submit" value="Continue"></td></tr>

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





<?php } elseif($action == 'new_dest') { // GET IF THE STAFF MEMBER IS TRAVELLING TO MULTIPLE DESTINATIONS ######### Step 2. MULTIPLE

$_SESSION['leave_date_requested'] = $_GET['leave_date_requested_m'].'/'.$_GET['leave_date_requested_d'].'/'.$_GET['leave_date_requested_y'];
$_SESSION['return_date_requested'] = $_GET['return_date_requested_m'].'/'.$_GET['return_date_requested_d'].'/'.$_GET['return_date_requested_y'];
$_SESSION['leave_time_requested'] = $_GET['leave_time_requested'];
$_SESSION['return_time_requested'] = $_GET['return_time_requested'];
$_SESSION['am_d'] = $_GET['am_d'];
$_SESSION['am_r'] = $_GET['am_r'];

##################################################################
## START: CREATE ARRAY OF TRAVEL DATES FOR CHECKING CONUS RATES
##################################################################
$start_date = date("m/d/Y",mktime(0,0,0,$_GET['leave_date_requested_m'],$_GET['leave_date_requested_d'],$_GET['leave_date_requested_y']));
$end_date = date("m/d/Y",mktime(0,0,0,$_GET['return_date_requested_m'],$_GET['return_date_requested_d'],$_GET['return_date_requested_y']));

$i=0;
while ($temp <> $end_date) {
$temp = date("m/d/Y", mktime(0, 0, 0, $_GET['leave_date_requested_m'], $_GET['leave_date_requested_d']+$i, $_GET['leave_date_requested_y']));
$travel_days[$i] = $temp;
$i++;
};
//echo '<p>';
//print_r($travel_days);
$_SESSION['travel_days'] = $travel_days;
##################################################################
## END: CREATE ARRAY OF TRAVEL DATES FOR CHECKING CONUS RATES
##################################################################

?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function checkFields() { 

	// State selector
		if (document.travel_1.num_dest.value ==""){
			alert("Please enter the number of destinations (cities) for this travel request.");
			document.travel_1.num_dest.focus();
			return false;	}

		if (document.travel_1.num_dest.value =="1"){
			alert("Please only use this form for 2 or more destinations.");
			document.travel_1.num_dest.focus();
			return false;	}

		if (document.travel_1.num_dest.value > 6){
			alert("The maximum number of destinations (cities) for one travel request is 6. If more destinations are required, create a separate travel request.");
			document.travel_1.num_dest.focus();
			return false;	}

}			



</script>




</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests</h1><hr /></td></tr>
			
			
			
			<tr><td colspan="2">

			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW TRAVEL REQUEST</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>2. Destination</strong>: <em>Will you be traveling to more than 1 destination?</em></td></tr>
								
								<tr>
								<td width="50%">
								<form name="travel_1" method="GET" onsubmit="return checkFields()">
								<input type="hidden" name="action" value="new_st_multi">Yes, I will be traveling to <input type="text" name="num_dest" size="3"> destinations. <input type="submit" name="submit" value="Continue"></form></td>
								<td width="50%"><form name="travel_2" method="GET">
								<input type="hidden" name="action" value="new_st"><input type="hidden" name="num_dest" value="1">No, I will be traveling to only one destination. <input type="submit" name="submit" value="Continue"></form>


								</td></tr>
								


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







<?php } elseif($action == 'new_st') { //IF THIS IS A NEW TRAVEL REQUEST

###############################################################
## START: GRAB ALL DESTINATION STATES ##
###############################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','conus_states','all');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('country','USA');

$search4 -> AddSortParam('abbrev','ascend');

$searchResult4 = $search4 -> FMFind();

//echo $searchResult4['errorCode'];
//echo $searchResult4['foundCount'];
//$recordData4 = current($searchResult4['data']);
#############################################################
## END: GRAB ALL DESTINATION STATES ##
#############################################################

$_SESSION['num_dest'] = $_GET['num_dest'];
?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function checkFields() { 

	// City
		if (document.travel_1.event_venue_city.value ==""){
			alert("Please enter a destination city.");
			document.travel_1.event_venue_city.focus();
			return false;	}


	// State selector
		if (document.travel_1.event_venue_state.value ==""){
			alert("Please select a destination state.");
			document.travel_1.event_venue_state.focus();
			return false;	}
}			



</script>




</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests</h1><hr /></td></tr>
			
			
			
			<tr><td colspan="2">
			<form name="travel_1" method="GET" onsubmit="return checkFields()">
			<input type="hidden" name="action" value="new">
			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW TRAVEL REQUEST</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>3. Enter Destination</strong>: <em>To what destination will you be traveling?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">City/State:</td>
								<td width="100%">

								<input type="text" name="event_venue_city" size="15">
								
								<select name="event_venue_state" class="body" onChange="MM_jumpMenu('parent',this,0)">
								<option value="">Select State</option>
								<option value="">-------------</option>
								
								<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
								<option value="<?php echo $searchData4['abbrev'][0];?>" <?php if($searchData4['abbrev'][0] == $_SESSION['state']){echo 'SELECTED';}?>> <?php echo $searchData4['abbrev'][0];?>
								<?php } ?>
								</select>


								<input type="submit" name="submit" value="Continue"></td></tr>
								


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



<?php } elseif($action == 'new_st_multi') { // GET THE STAFF MEMBER'S DESTINATION STATES FOR MULTI-DESTINATION TRAVEL

$_SESSION['num_dest'] = $_GET['num_dest'];

###############################################################
## START: GRAB ALL DESTINATION STATES ##
###############################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','conus_states','all');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('country','USA');

$search4 -> AddSortParam('abbrev','ascend');

$searchResult4 = $search4 -> FMFind();

//echo $searchResult4['errorCode'];
//echo $searchResult4['foundCount'];
//$recordData4 = current($searchResult4['data']);
#############################################################
## END: GRAB ALL DESTINATION STATES ##
#############################################################
?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function checkFields() { 

	// State selector
		if (document.travel_1.event_venue_state.value ==""){
			alert("Please select a destination state.");
			document.travel_1.event_venue_state.focus();
			return false;	}
}			



</script>




</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests</h1><hr /></td></tr>
			
			
			
			<tr><td colspan="2">
			<form name="travel_1" method="GET">
			<input type="hidden" name="action" value="new_st_multi_3">
			<input type="hidden" name="num_dest" value="<?php echo $num_dest;?>">
			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW TRAVEL REQUEST</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>3. Enter Destinations</strong>: <em>To what destinations will you be traveling? Indicate the city/state for each destination.</em></td></tr>
								
					<?php for($i=1;$i<=$_SESSION['num_dest'];$i++) { ?>
					
					

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">City/State (Destination <?php echo $i;?>):</td>
								<td width="100%">
								
								<input type="text" name="event_venue_city<?php echo $i;?>" size="15">
								
								<select name="event_venue_state<?php echo $i;?>" class="body">
								<option value="">Select State</option>
								<option value="">-------------</option>
								
								<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
								<option value="<?php echo $searchData4['abbrev'][0];?>"> <?php echo $searchData4['abbrev'][0];?>
								<?php } ?>
								</select>


								</td></tr>
								
					<?php } ?>

								<tr><td bgcolor="#ebebeb">&nbsp;</td><td><input type="submit" name="submit" value="Continue"></td></tr>

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



<?php } elseif($action == 'new_st_multi_2') { // GET THE STAFF MEMBER'S DESTINATION CITIES FOR MULTI-DESTINATION TRAVEL

//$num_dest = $_GET['num_dest'];


$_SESSION['event_venue_state1'] = $_GET['event_venue_state1'];
$_SESSION['event_venue_state2'] = $_GET['event_venue_state2'];
$_SESSION['event_venue_state3'] = $_GET['event_venue_state3'];
$_SESSION['event_venue_state4'] = $_GET['event_venue_state4'];
$_SESSION['event_venue_state5'] = $_GET['event_venue_state5'];
$_SESSION['event_venue_state6'] = $_GET['event_venue_state6'];


###############################################################
## START: GRAB CITIES FOR DESTINATION 1 ##
###############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','travel_authorizations_CONUS_byDestinationState','all');
$search -> SetDBPassword($webPW,$webUN);

$search -> AddDBParam('state_abbrev',$_SESSION['event_venue_state1']);

$search -> AddSortParam('destination','ascend');

$searchResult = $search -> FMFind();

//echo '<p>errorCode: '.$searchResult['errorCode'];
//echo '<p>foundCount: '.$searchResult['foundCount'];
//$recordData = current($searchResult['data']);
#############################################################
## END: GRAB CITIES FOR DESTINATION 1 ##
#############################################################

if($_SESSION['num_dest'] > 1){
###############################################################
## START: GRAB CITIES FOR DESTINATION 2 ##
###############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','travel_authorizations_CONUS_byDestinationState','all');
$search2 -> SetDBPassword($webPW,$webUN);

$search2 -> AddDBParam('state_abbrev',$_SESSION['event_venue_state2']);

$search2 -> AddSortParam('destination','ascend');

$searchResult2 = $search2 -> FMFind();

//echo '<p>errorCode: '.$searchResult2['errorCode'];
//echo '<p>foundCount: '.$searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);
#############################################################
## END: GRAB CITIES FOR DESTINATION 2 ##
#############################################################
}

if($_SESSION['num_dest'] > 2){
###############################################################
## START: GRAB CITIES FOR DESTINATION 3 ##
###############################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','travel_authorizations_CONUS_byDestinationState','all');
$search3 -> SetDBPassword($webPW,$webUN);

$search3 -> AddDBParam('state_abbrev',$_SESSION['event_venue_state3']);

$search3 -> AddSortParam('destination','ascend');

$searchResult3 = $search3 -> FMFind();

//echo '<p>errorCode: '.$searchResult3['errorCode'];
//echo '<p>foundCount: '.$searchResult3['foundCount'];
//$recordData3 = current($searchResult3['data']);
#############################################################
## END: GRAB CITIES FOR DESTINATION 3 ##
#############################################################
}

if($_SESSION['num_dest'] > 3){
###############################################################
## START: GRAB CITIES FOR DESTINATION 4 ##
###############################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','travel_authorizations_CONUS_byDestinationState','all');
$search4 -> SetDBPassword($webPW,$webUN);

$search4 -> AddDBParam('state_abbrev',$_SESSION['event_venue_state4']);

$search4 -> AddSortParam('destination','ascend');

$searchResult4 = $search4 -> FMFind();

//echo '<p>errorCode: '.$searchResult4['errorCode'];
//echo '<p>foundCount: '.$searchResult4['foundCount'];
//$recordData4 = current($searchResult4['data']);
#############################################################
## END: GRAB CITIES FOR DESTINATION 4 ##
#############################################################
}

if($_SESSION['num_dest'] > 4){
###############################################################
## START: GRAB CITIES FOR DESTINATION 5 ##
###############################################################
$search5 = new FX($serverIP,$webCompanionPort);
$search5 -> SetDBData('SIMS_2.fp7','travel_authorizations_CONUS_byDestinationState','all');
$search5 -> SetDBPassword($webPW,$webUN);

$search5 -> AddDBParam('state_abbrev',$_SESSION['event_venue_state5']);

$search5 -> AddSortParam('destination','ascend');

$searchResult5 = $search5 -> FMFind();

//echo '<p>errorCode: '.$searchResult5['errorCode'];
//echo '<p>foundCount: '.$searchResult5['foundCount'];
//$recordData5 = current($searchResult5['data']);
#############################################################
## END: GRAB CITIES FOR DESTINATION 5 ##
#############################################################
}

if($_SESSION['num_dest'] > 5){
###############################################################
## START: GRAB CITIES FOR DESTINATION 6 ##
###############################################################
$search6 = new FX($serverIP,$webCompanionPort);
$search6 -> SetDBData('SIMS_2.fp7','travel_authorizations_CONUS_byDestinationState','all');
$search6 -> SetDBPassword($webPW,$webUN);

$search6 -> AddDBParam('state_abbrev',$_SESSION['event_venue_state6']);

$search6 -> AddSortParam('destination','ascend');

$searchResult6 = $search6 -> FMFind();

//echo '<p>errorCode: '.$searchResult6['errorCode'];
//echo '<p>foundCount: '.$searchResult6['foundCount'];
//$recordData6 = current($searchResult6['data']);
#############################################################
## END: GRAB CITIES FOR DESTINATION 6 ##
#############################################################
}

?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function checkFields() { 

	// State selector
		if (document.travel_1.event_venue_state.value ==""){
			alert("Please select a destination state.");
			document.travel_1.event_venue_state.focus();
			return false;	}
}			



</script>




</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests</h1><hr /></td></tr>
			
			
			
			<tr><td colspan="2">
			<form name="travel_1" method="GET">
			<input type="hidden" name="action" value="new_st_multi_3">
			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW TRAVEL REQUEST</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>4. Select Cities</strong>: <em>To what destinations (cities) will you be traveling?</em></td></tr>
								
					<?php for($i=1;$i<=$_SESSION['num_dest'];$i++) { ?>
					
					
								<?php 
								if($i==1){?>

									<tr><td class="body" nowrap align="right" bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_state1'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city1" class="body">
									<option value="">Select City</option>
									<option value="">-------------</option>
									
									<?php foreach($searchResult['data'] as $key => $searchData) { ?>
									<option value="<?php echo $searchData['destination'][0];?>"> <?php echo $searchData['destination'][0];?>
									<?php } ?>
									</select>
									</td>
									</tr>								
								
								<?php }elseif($i==2){?>						

									<tr><td class="body" nowrap align="right" bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_state2'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city2" class="body">
									<option value="">Select City</option>
									<option value="">-------------</option>
									
									<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
									<option value="<?php echo $searchData2['destination'][0];?>"> <?php echo $searchData2['destination'][0];?>
									<?php } ?>
									</select>
									</td>
									</tr>								

								<?php }elseif($i==3){?>						

									<tr><td class="body" nowrap align="right" bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_state3'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city3" class="body">
									<option value="">Select City</option>
									<option value="">-------------</option>
									
									<?php foreach($searchResult3['data'] as $key => $searchData3) { ?>
									<option value="<?php echo $searchData3['destination'][0];?>"> <?php echo $searchData3['destination'][0];?>
									<?php } ?>
									</select>
									</td>
									</tr>								

								<?php }elseif($i==4){?>						

									<tr><td class="body" nowrap align="right" bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_state4'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city4" class="body">
									<option value="">Select City</option>
									<option value="">-------------</option>
									
									<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
									<option value="<?php echo $searchData4['destination'][0];?>"> <?php echo $searchData4['destination'][0];?>
									<?php } ?>
									</select>
									</td>
									</tr>								

								<?php }elseif($i==5){?>						

									<tr><td class="body" nowrap align="right" bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_state5'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city5" class="body">
									<option value="">Select City</option>
									<option value="">-------------</option>
									
									<?php foreach($searchResult5['data'] as $key => $searchData5) { ?>
									<option value="<?php echo $searchData5['destination'][0];?>"> <?php echo $searchData5['destination'][0];?>
									<?php } ?>
									</select>
									</td>
									</tr>								

								<?php }elseif($i==6){?>						

									<tr><td class="body" nowrap align="right" bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_state6'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city6" class="body">
									<option value="">Select City</option>
									<option value="">-------------</option>
									
									<?php foreach($searchResult6['data'] as $key => $searchData6) { ?>
									<option value="<?php echo $searchData6['destination'][0];?>"> <?php echo $searchData6['destination'][0];?>
									<?php } ?>
									</select>
									</td>
									</tr>								

								<?php } ?>

					<?php } ?>

								<tr><td bgcolor="#ebebeb">&nbsp;</td><td><input type="submit" name="submit" value="Continue"></td></tr>

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


<?php } elseif($action == 'new_st_multi_3') { // GET THE STAFF MEMBER'S DATES OF TRAVEL FOR MULTI-DESTINATION TRAVEL

//$num_dest = $_GET['num_dest'];


$_SESSION['event_venue_city1'] = $_GET['event_venue_city1'];
$_SESSION['event_venue_city2'] = $_GET['event_venue_city2'];
$_SESSION['event_venue_city3'] = $_GET['event_venue_city3'];
$_SESSION['event_venue_city4'] = $_GET['event_venue_city4'];
$_SESSION['event_venue_city5'] = $_GET['event_venue_city5'];
$_SESSION['event_venue_city6'] = $_GET['event_venue_city6'];

$_SESSION['event_venue_state1'] = $_GET['event_venue_state1'];
$_SESSION['event_venue_state2'] = $_GET['event_venue_state2'];
$_SESSION['event_venue_state3'] = $_GET['event_venue_state3'];
$_SESSION['event_venue_state4'] = $_GET['event_venue_state4'];
$_SESSION['event_venue_state5'] = $_GET['event_venue_state5'];
$_SESSION['event_venue_state6'] = $_GET['event_venue_state6'];

?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function checkFields() { 

	// State selector
		if (document.travel_1.event_venue_state.value ==""){
			alert("Please select a destination state.");
			document.travel_1.event_venue_state.focus();
			return false;	}
}			



</script>




</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests</h1><hr /></td></tr>
			
			
			
			<tr><td colspan="2">
			<form name="travel_1" method="GET">
			<input type="hidden" name="action" value="new_multi">
			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW TRAVEL REQUEST</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>4. Enter Dates of Travel</strong>: <em>Indicate the travel dates for each destination.</em></td></tr>
								
					<?php for($i=1;$i<=$_SESSION['num_dest'];$i++) { ?>
					
					
								<?php 
								if($i==1){?>

									<tr><td class="body" nowrap bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_city1'].', '.$_SESSION['event_venue_state1'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city_date_from1" class="body">
									<option value="">Arrive</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									<select name="event_venue_city_date_to1" class="body">
									<option value="">Depart</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									</td>
									</tr>								
								
								<?php }elseif($i==2){?>						

									<tr><td class="body" nowrap bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_city2'].', '.$_SESSION['event_venue_state2'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city_date_from2" class="body">
									<option value="">Arrive</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									<select name="event_venue_city_date_to2" class="body">
									<option value="">Depart</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									</td>
									</tr>								

								<?php }elseif($i==3){?>						

									<tr><td class="body" nowrap bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_city3'].', '.$_SESSION['event_venue_state3'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city_date_from3" class="body">
									<option value="">Arrive</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									<select name="event_venue_city_date_to3" class="body">
									<option value="">Depart</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									</td>
									</tr>								

								<?php }elseif($i==4){?>						

									<tr><td class="body" nowrap bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_city4'].', '.$_SESSION['event_venue_state4'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city_date_from4" class="body">
									<option value="">Arrive</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									<select name="event_venue_city_date_to4" class="body">
									<option value="">Depart</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									</td>
									</tr>								

								<?php }elseif($i==5){?>						

									<tr><td class="body" nowrap bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_city5'].', '.$_SESSION['event_venue_state5'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city_date_from5" class="body">
									<option value="">Arrive</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									<select name="event_venue_city_date_to5" class="body">
									<option value="">Depart</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									</td>
									</tr>								

								<?php }elseif($i==6){?>						

									<tr><td class="body" nowrap bgcolor="#ebebeb">Destination <?php echo $i;?> (<strong><?php echo $_SESSION['event_venue_city6'].', '.$_SESSION['event_venue_state6'];?></strong>):</td>
									<td width="100%">
									<select name="event_venue_city_date_from6" class="body">
									<option value="">Arrive</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									<select name="event_venue_city_date_to6" class="body">
									<option value="">Depart</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

									</td>
									</tr>								

								<?php } ?>

					<?php } ?>

								<tr><td bgcolor="#ebebeb">&nbsp;</td><td><input type="submit" name="submit" value="Continue"></td></tr>

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



<?php } elseif($action == 'new') { //IF THIS IS A NEW TRAVEL REQUEST



#########################################################################################################################################################
## Start: Form detail request with dates requested from - new_dot GET THE STAFF MEMBER'S DATES AND TIMES OF TRAVEL - ## 
#############################################################

$_SESSION['city'] = $_GET['event_venue_city'];
$_SESSION['state'] = $_GET['event_venue_state'];

/*
#########################################################################
## START: GRAB TRAVEL DATES/TIMES FOR MULTI-DESTINATION TRAVEL REQUESTS
#########################################################################
$_SESSION['event_venue_city_date_from1'] = $_GET['event_venue_city_date_from1'];
$_SESSION['event_venue_city_date_to1'] = $_GET['event_venue_city_date_to1'];
$_SESSION['event_venue_city_date_from2'] = $_GET['event_venue_city_date_from2'];
$_SESSION['event_venue_city_date_to2'] = $_GET['event_venue_city_date_to2'];
$_SESSION['event_venue_city_date_from3'] = $_GET['event_venue_city_date_from3'];
$_SESSION['event_venue_city_date_to3'] = $_GET['event_venue_city_date_to3'];
$_SESSION['event_venue_city_date_from4'] = $_GET['event_venue_city_date_from4'];
$_SESSION['event_venue_city_date_to4'] = $_GET['event_venue_city_date_to4'];
$_SESSION['event_venue_city_date_from5'] = $_GET['event_venue_city_date_from5'];
$_SESSION['event_venue_city_date_to5'] = $_GET['event_venue_city_date_to5'];
$_SESSION['event_venue_city_date_from6'] = $_GET['event_venue_city_date_from6'];
$_SESSION['event_venue_city_date_to6'] = $_GET['event_venue_city_date_to6'];
#########################################################################
## END: GRAB TRAVEL DATES/TIMES FOR MULTI-DESTINATION TRAVEL REQUESTS
#########################################################################
*/
############################################################
## START: GRAB BUDGET CODES FOR BUDGET CODE CHECKBOXES ##
############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','budget_code_usage','all');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('staff_ID',$_SESSION['staff_ID']);

$search2 -> AddSortParam('budget_code','ascend');

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);
##########################################################
## END: GRAB BUDGET CODES FOR BUDGET CODE CHECKBOXES ##
##########################################################

###############################################################
## START: GRAB ALL ACTIVE BUDGET CODES ##
###############################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('c_Active_Status','Active');

$search3 -> AddSortParam('c_BudgetCode','ascend');

$searchResult3 = $search3 -> FMFind();

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//$recordData3 = current($searchResult3['data']);
#############################################################
## END: GRAB ALL ACTIVE BUDGET CODES ##
#############################################################
echo '<p>leavedate: '.$_SESSION['leave_date_requested']; 
echo '<p>returndate: '.$_SESSION['return_date_requested'];


?>





<html>
<head>
<title>SIMS: My Travel Requests</title>
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

	// Pay period
		if (document.leave_request2.future_pay_period_m.value ==""){
			alert("Please select the future pay period (month).");
			document.leave_request2.future_pay_period_m.focus();
			return false;	}

	// Pay period
		if (document.leave_request2.future_pay_period_d.value ==""){
			alert("Please select the future pay period (day).");
			document.leave_request2.future_pay_period_d.focus();
			return false;	}

	// Pay period
		if (document.leave_request2.future_pay_period_y.value ==""){
			alert("Please select the future pay period (year).");
			document.leave_request2.future_pay_period_y.focus();
			return false;	}



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

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests</h1><hr /></td></tr>
			
			
			
			<tr><td colspan="2">
			<form name="leave_request1" method="GET">
			<input type="hidden" name="action" value="new_submit">
			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW TRAVEL REQUEST</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Purpose:</td>
								<td width="100%">
								<input type="checkbox" name="purpose_of_travel[]" value="Data Collection"> Data Collection &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="purpose_of_travel[]" value="Evaluation"> Evaluation &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="purpose_of_travel[]" value="Meeting"> Meeting &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="purpose_of_travel[]" value="Presentation"> Presentation &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="purpose_of_travel[]" value="Provide PD/TA"> Provide PD/TA &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="purpose_of_travel[]" value="Receive PD/TA"> Receive PD/TA &nbsp;&nbsp;&nbsp;
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Description:</td>
								<td width="100%"><input type="text" name="purpose_of_travel_descr" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name" size="45"></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Destination:</td>
								<?php if($_SESSION['num_dest'] == 1){?>

								<td width="100%"><strong><?php echo $_SESSION['city'].', '.$_SESSION['state'];?></strong></td>

								<?php }elseif($_SESSION['num_dest'] > 1){?><strong>
								<?php
								if(isset($_SESSION['event_venue_city1'])){ echo $_SESSION['event_venue_city1'].', '.$_SESSION['event_venue_state1'].'<br>';}
								if(isset($_SESSION['event_venue_city2'])){ echo $_SESSION['event_venue_city2'].', '.$_SESSION['event_venue_state2'].'<br>';}
								if(isset($_SESSION['event_venue_city3'])){ echo $_SESSION['event_venue_city3'].', '.$_SESSION['event_venue_state3'].'<br>';}
								if(isset($_SESSION['event_venue_city4'])){ echo $_SESSION['event_venue_city4'].', '.$_SESSION['event_venue_state4'].'<br>';}
								if(isset($_SESSION['event_venue_city5'])){ echo $_SESSION['event_venue_city5'].', '.$_SESSION['event_venue_state5'].'<br>';}
								if(isset($_SESSION['event_venue_city6'])){ echo $_SESSION['event_venue_city6'].', '.$_SESSION['event_venue_state6'];}?>
								</strong>
								
								<?php }?>

								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr" size="45"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event Start Date:</td>
								<td>

									<select name="event_start_date" class="body">
									<option value="">Start</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time" size="10">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event End Date:
								
								
								
								</td><td>

									<select name="event_end_date" class="body">
									<option value="">End</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time" size="10">
								
								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Requested Leave Date:</td>
								<td><strong><?php echo $_SESSION['leave_date_requested'];?></strong>
								&nbsp;&nbsp;&nbsp;Requested Leave Time: <strong><?php echo $_SESSION['leave_time_requested'].' '.$_SESSION['am_d'];?></strong>
								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Requested Return Date:</td>
								<td><strong><?php echo $_SESSION['return_date_requested'];?></strong>
								&nbsp;&nbsp;&nbsp;Requested Return Time: <strong><?php echo $_SESSION['return_time_requested'].' '.$_SESSION['am_r'];?></strong>
								</td></tr>
								
								<tr><td colspan="2"><strong>Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">

								<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
								
									<input type="checkbox" name="budget_code[]" value="<?php echo $searchData2['budget_code'][0];?>"><?php echo $searchData2['budget_code'][0];?></input><span class="tiny"> | <?php echo $searchData2['Budget_Code_Nickname'][0];?></span><br>
								
								<?php } ?>

								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">FFS (087):</td>
								<td width="100%">

								<select name="budget_code_FFS_code" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData3) { 
								if($searchData3['Fund'][0] == '087'){
								?>
								
									<option value="<?php echo $searchData3['c_BudgetCode'][0];?>"> <?php echo $searchData3['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Fee for Service (pre-FY2009)</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CPL (088):</td>
								<td width="100%">

								<select name="budget_code_CPL_code" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData4) { 
								if($searchData4['Fund'][0] == '088'){
								?>
								
									<option value="<?php echo $searchData4['c_BudgetCode'][0];?>"> <?php echo $searchData4['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Center for Professional Learning</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">Other:</td>
								<td width="100%">

								<select name="budget_code_other" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData5) { 
								if(($searchData5['Fund'][0] != '087')&&($searchData5['Fund'][0] != '088')){
								?>
								
									<option value="<?php echo $searchData5['c_BudgetCode'][0];?>"> <?php echo $searchData5['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Select from all active budget codes</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><input type="text" name="budget_code_instructions" size="30"> <em>Example: "Charge 50% to each code"</em></td>
								</tr>								

								<tr><td colspan="2"><strong>Transportation</strong>: <em>How will you be traveling?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized" value="yes"> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage" size="5"><p>
								<input type="checkbox" name="trans_airline_requested" value="yes"> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier" size="10"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid" value="yes"> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested" value="yes"> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested" size="5"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification" size="20"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff" value="yes"> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name" size="20"><p>
								<input type="checkbox" name="travel_advance_requested" value="yes"> Travel advance requested (only available for rental car, hotel, and/or meals)<p>
								</td>
								</tr>								

								<tr><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name" size="45"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel" value="yes"> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel" value="no"> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification" size="45">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city" size="20"> <input type="text" name="preferred_hotel_state" size="5"> <input type="text" name="preferred_hotel_zip" size="10"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone" size="20"> <input type="text" name="preferred_hotel_fax" size="20"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested" size="5"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate" size="5"> <em>(per night)</em></td>
								</tr>								

								<tr><td colspan="2"><strong>Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
								approving your request or to support staff in making your travel arrangements.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><input type="text" name="other_information" size="100%"></td>
								</tr>								



								<tr><td colspan="2" align="right"><input type="reset" name="reset" value="Reset Form"><input type="submit" name="submit" value="Submit Travel Request"></td></tr>
								


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


<?php } elseif($action == 'new_multi') { //IF THIS IS A NEW TRAVEL REQUEST

//$_SESSION['city'] = $_GET['event_venue_city'];
//$_SESSION['state'] = $_GET['event_venue_state'];

#########################################################################
## START: GRAB TRAVEL DATES/TIMES FOR MULTI-DESTINATION TRAVEL REQUESTS
#########################################################################
$_SESSION['event_venue_city_date_from1'] = $_GET['event_venue_city_date_from1'];
$_SESSION['event_venue_city_date_to1'] = $_GET['event_venue_city_date_to1'];
$_SESSION['event_venue_city_date_from2'] = $_GET['event_venue_city_date_from2'];
$_SESSION['event_venue_city_date_to2'] = $_GET['event_venue_city_date_to2'];
$_SESSION['event_venue_city_date_from3'] = $_GET['event_venue_city_date_from3'];
$_SESSION['event_venue_city_date_to3'] = $_GET['event_venue_city_date_to3'];
$_SESSION['event_venue_city_date_from4'] = $_GET['event_venue_city_date_from4'];
$_SESSION['event_venue_city_date_to4'] = $_GET['event_venue_city_date_to4'];
$_SESSION['event_venue_city_date_from5'] = $_GET['event_venue_city_date_from5'];
$_SESSION['event_venue_city_date_to5'] = $_GET['event_venue_city_date_to5'];
$_SESSION['event_venue_city_date_from6'] = $_GET['event_venue_city_date_from6'];
$_SESSION['event_venue_city_date_to6'] = $_GET['event_venue_city_date_to6'];
#########################################################################
## END: GRAB TRAVEL DATES/TIMES FOR MULTI-DESTINATION TRAVEL REQUESTS
#########################################################################

############################################################
## START: GRAB BUDGET CODES FOR BUDGET CODE CHECKBOXES ##
############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','budget_code_usage','all');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('staff_ID',$_SESSION['staff_ID']);

$search2 -> AddSortParam('budget_code','ascend');

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);
##########################################################
## END: GRAB BUDGET CODES FOR BUDGET CODE CHECKBOXES ##
##########################################################

###############################################################
## START: GRAB ALL ACTIVE BUDGET CODES ##
###############################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('c_Active_Status','Active');

$search3 -> AddSortParam('c_BudgetCode','ascend');

$searchResult3 = $search3 -> FMFind();

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//$recordData3 = current($searchResult3['data']);
#############################################################
## END: GRAB ALL ACTIVE BUDGET CODES ##
#############################################################
echo '<p>leavedate: '.$_SESSION['leave_date_requested']; 
echo '<p>returndate: '.$_SESSION['return_date_requested'];
echo '<p>$_SESSION[numdest]: '.$_SESSION['num_dest'];


?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
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

	// Pay period
		if (document.leave_request2.future_pay_period_m.value ==""){
			alert("Please select the future pay period (month).");
			document.leave_request2.future_pay_period_m.focus();
			return false;	}

	// Pay period
		if (document.leave_request2.future_pay_period_d.value ==""){
			alert("Please select the future pay period (day).");
			document.leave_request2.future_pay_period_d.focus();
			return false;	}

	// Pay period
		if (document.leave_request2.future_pay_period_y.value ==""){
			alert("Please select the future pay period (year).");
			document.leave_request2.future_pay_period_y.focus();
			return false;	}



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

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests</h1><hr /></td></tr>
			
			
			
			<tr><td colspan="2">
			<form name="leave_request1" method="GET">
			<input type="hidden" name="action" value="new_submit_multi">
			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW TRAVEL REQUEST</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2"><strong>Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Purpose:</td>
								<td width="100%">
								<input type="checkbox" name="purpose_of_travel[]" value="Data Collection"> Data Collection &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="purpose_of_travel[]" value="Evaluation"> Evaluation &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="purpose_of_travel[]" value="Meeting"> Meeting &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="purpose_of_travel[]" value="Presentation"> Presentation &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="purpose_of_travel[]" value="Provide PD/TA"> Provide PD/TA &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="purpose_of_travel[]" value="Receive PD/TA"> Receive PD/TA &nbsp;&nbsp;&nbsp;
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Description:</td>
								<td width="100%"><input type="text" name="purpose_of_travel_descr" size="45"></td>
								</tr>								

								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Requested Leave Date:</td>
								<td><strong><?php echo $_SESSION['leave_date_requested'];?></strong>
								&nbsp;&nbsp;&nbsp;Requested Leave Time: <strong><?php echo $_SESSION['leave_time_requested'];?></strong>
								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Requested Return Date:</td>
								<td><strong><?php echo $_SESSION['return_date_requested'];?></strong>
								&nbsp;&nbsp;&nbsp;Requested Return Time: <strong><?php echo $_SESSION['return_time_requested'];?></strong>
								</td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 1</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $_SESSION['event_venue_city1'].', '.$_SESSION['event_venue_state1'].' - '.$_SESSION['event_venue_city_date_from1'].' to '.$_SESSION['event_venue_city_date_to1'];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name1" size="45"></td>
								</tr>								
								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue1" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr1" size="45"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>

									<select name="event_start_date1" class="body">
									<option value="">Start</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time1" size="10">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>

									<select name="event_end_date1" class="body">
									<option value="">End</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time1" size="10">
								
								</td></tr>

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city1'];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name1" size="45"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel1" value="yes"> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel1" value="no"> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification1" size="45">
								</td>
								</tr>								



								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr1" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city1" size="20"> <input type="text" name="preferred_hotel_state1" size="5"> <input type="text" name="preferred_hotel_zip1" size="10"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone1" size="20"> <input type="text" name="preferred_hotel_fax1" size="20"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested1" size="5"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate1" size="5"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments1" size="45"></td>
								</tr>								


<?php if($_SESSION['num_dest'] > 1){?>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 2</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $_SESSION['event_venue_city2'].', '.$_SESSION['event_venue_state2'].' - '.$_SESSION['event_venue_city_date_from2'].' to '.$_SESSION['event_venue_city_date_to2'];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name2" size="45"></td>
								</tr>								
								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue2" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr2" size="45"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>

									<select name="event_start_date2" class="body">
									<option value="">Start</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time2" size="10">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>

									<select name="event_end_date2" class="body">
									<option value="">End</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time2" size="10">
								
								</td></tr>

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city2'];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name2" size="45"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel2" value="yes"> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel2" value="no"> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification2" size="45">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr2" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city2" size="20"> <input type="text" name="preferred_hotel_state2" size="5"> <input type="text" name="preferred_hotel_zip2" size="10"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone2" size="20"> <input type="text" name="preferred_hotel_fax2" size="20"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested2" size="5"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate2" size="5"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments2" size="45"></td>
								</tr>								

<?php } ?>

<?php if($_SESSION['num_dest'] > 2){?>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 3</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $_SESSION['event_venue_city3'].', '.$_SESSION['event_venue_state3'].' - '.$_SESSION['event_venue_city_date_from3'].' to '.$_SESSION['event_venue_city_date_to3'];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name3" size="45"></td>
								</tr>								
								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue3" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr3" size="45"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>

									<select name="event_start_date3" class="body">
									<option value="">Start</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time3" size="10">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>

									<select name="event_end_date3" class="body">
									<option value="">End</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time3" size="10">
								
								</td></tr>

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city3'];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name3" size="45"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel3" value="yes"> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel3" value="no"> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification3" size="45">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr3" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city3" size="20"> <input type="text" name="preferred_hotel_state3" size="5"> <input type="text" name="preferred_hotel_zip3" size="10"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone3" size="20"> <input type="text" name="preferred_hotel_fax3" size="20"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested3" size="5"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate3" size="5"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments3" size="45"></td>
								</tr>								



<?php } ?>


<?php if($_SESSION['num_dest'] > 3){?>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 4</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $_SESSION['event_venue_city4'].', '.$_SESSION['event_venue_state4'].' - '.$_SESSION['event_venue_city_date_from4'].' to '.$_SESSION['event_venue_city_date_to4'];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name4" size="45"></td>
								</tr>								
								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue4" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr4" size="45"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>

									<select name="event_start_date4" class="body">
									<option value="">Start</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time4" size="10">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>

									<select name="event_end_date4" class="body">
									<option value="">End</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time4" size="10">
								
								</td></tr>

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city4'];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name4" size="45"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel4" value="yes"> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel4" value="no"> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification4" size="45">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr4" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city4" size="20"> <input type="text" name="preferred_hotel_state4" size="5"> <input type="text" name="preferred_hotel_zip4" size="10"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone4" size="20"> <input type="text" name="preferred_hotel_fax4" size="20"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested4" size="5"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate4" size="5"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments4" size="45"></td>
								</tr>								



<?php } ?>

<?php if($_SESSION['num_dest'] > 4){?>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 5</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $_SESSION['event_venue_city5'].', '.$_SESSION['event_venue_state5'].' - '.$_SESSION['event_venue_city_date_from5'].' to '.$_SESSION['event_venue_city_date_to5'];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name5" size="45"></td>
								</tr>								
								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue5" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr5" size="45"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>

									<select name="event_start_date5" class="body">
									<option value="">Start</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time5" size="10">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>

									<select name="event_end_date5" class="body">
									<option value="">End</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time5" size="10">
								
								</td></tr>

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city5'];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name5" size="45"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel5" value="yes"> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel5" value="no"> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification5" size="45">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr5" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city5" size="20"> <input type="text" name="preferred_hotel_state5" size="5"> <input type="text" name="preferred_hotel_zip5" size="10"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone5" size="20"> <input type="text" name="preferred_hotel_fax5" size="20"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested5" size="5"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate5" size="5"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments5" size="45"></td>
								</tr>								



<?php } ?>


<?php if($_SESSION['num_dest'] > 5){?>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 6</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $_SESSION['event_venue_city6'].', '.$_SESSION['event_venue_state6'].' - '.$_SESSION['event_venue_city_date_from6'].' to '.$_SESSION['event_venue_city_date_to6'];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name6" size="45"></td>
								</tr>								
								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue6" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><input type="text" name="event_venue_addr6" size="45"></td>
								</tr>								


								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date:</td>
								<td>

									<select name="event_start_date6" class="body">
									<option value="">Start</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event Start Time: <input type="text" name="event_start_time6" size="10">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date:
								
								
								
								</td><td>

									<select name="event_end_date6" class="body">
									<option value="">End</option>
									<option value="">-------------</option>
									
									<?php foreach($_SESSION['travel_days'] as $current) { ?>
									<option value="<?php echo $current;?>"> <?php echo $current;?>
									<?php } ?>
									</select>

								&nbsp;&nbsp;&nbsp;Event End Time: <input type="text" name="event_end_time6" size="10">
								
								</td></tr>

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city6'];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name6" size="45"><p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel6" value="yes"> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel6" value="no"> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification6" size="45">
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><input type="text" name="preferred_hotel_addr6" size="45"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><input type="text" name="preferred_hotel_city6" size="20"> <input type="text" name="preferred_hotel_state6" size="5"> <input type="text" name="preferred_hotel_zip6" size="10"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><input type="text" name="preferred_hotel_phone6" size="20"> <input type="text" name="preferred_hotel_fax6" size="20"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><input type="text" name="hotel_nights_requested6" size="5"></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%"><input type="text" name="hotel_rate6" size="5"> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><input type="text" name="destination_comments6" size="45"></td>
								</tr>								



<?php } ?>

								
								
								<tr><td colspan="2"><strong>Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">

								<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
								
									<input type="checkbox" name="budget_code[]" value="<?php echo $searchData2['budget_code'][0];?>"><?php echo $searchData2['budget_code'][0];?></input><span class="tiny"> | <?php echo $searchData2['Budget_Code_Nickname'][0];?></span><br>
								
								<?php } ?>

								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">FFS (087):</td>
								<td width="100%">

								<select name="budget_code_FFS_code" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData3) { 
								if($searchData3['Fund'][0] == '087'){
								?>
								
									<option value="<?php echo $searchData3['c_BudgetCode'][0];?>"> <?php echo $searchData3['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Fee for Service (pre-FY2009)</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CPL (088):</td>
								<td width="100%">

								<select name="budget_code_CPL_code" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData4) { 
								if($searchData4['Fund'][0] == '088'){
								?>
								
									<option value="<?php echo $searchData4['c_BudgetCode'][0];?>"> <?php echo $searchData4['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Center for Professional Learning</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">Other:</td>
								<td width="100%">

								<select name="budget_code_other" class="body">
								<option value=""></option>

								<?php foreach($searchResult3['data'] as $key => $searchData5) { 
								if(($searchData5['Fund'][0] != '087')&&($searchData5['Fund'][0] != '088')){
								?>
								
									<option value="<?php echo $searchData5['c_BudgetCode'][0];?>"> <?php echo $searchData5['c_BudgetCode'][0];?></option>
								
								<?php 
								}
								} ?>
								
								</select> <em>Select from all active budget codes</em>
									
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><input type="text" name="budget_code_instructions" size="30"> <em>Example: "Charge 50% to each code"</em></td>
								</tr>								

								<tr><td colspan="2"><strong>Transportation</strong>: <em>How will you be traveling?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized" value="yes"> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage" size="5"><p>
								<input type="checkbox" name="trans_airline_requested" value="yes"> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier" size="10"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid" value="yes"> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested" value="yes"> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested" size="5"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification" size="20"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff" value="yes"> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name" size="20"><p>
								<input type="checkbox" name="travel_advance_requested" value="yes"> Travel advance requested (only available for rental car, hotel, and/or meals)<p>
								</td>
								</tr>								



								<tr><td colspan="2"><strong>Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
								approving your request or to support staff in making your travel arrangements.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><input type="text" name="other_information" size="100%"></td>
								</tr>								



								<tr><td colspan="2" align="right"><input type="reset" name="reset" value="Reset Form"><input type="submit" name="submit" value="Submit Travel Request"></td></tr>
								


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


<? } elseif($action == 'new_submit') { 

//$event_start_date = $_GET['event_start_date_m'].'/'.$_GET['event_start_date_d'].'/'.$_GET['event_start_date_y'];
//$event_end_date = $_GET['event_end_date_m'].'/'.$_GET['event_end_date_d'].'/'.$_GET['event_end_date_y'];
//$leave_date_requested = $_GET['leave_date_requested_m'].'/'.$_GET['leave_date_requested_d'].'/'.$_GET['leave_date_requested_y'];
//$return_date_requested = $_GET['return_date_requested_m'].'/'.$_GET['return_date_requested_d'].'/'.$_GET['return_date_requested_y'];

for($i=0 ; $i<count($_GET['purpose_of_travel']) ; $i++) {
$purpose_of_travel .= $_GET['purpose_of_travel'][$i]."\r"; 
}

for($i=0 ; $i<count($_GET['budget_code']) ; $i++) {
$budget_code .= $_GET['budget_code'][$i]."\r"; 
}

#################################################
## START: CREATE NEW TRAVEL REQUEST RECORD ##
#################################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','travel_authorizations');
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('staff_ID',$_SESSION['staff_ID']);
$newrecord -> AddDBParam('approval_status','Pending');

$newrecord -> AddDBParam('purpose_of_travel',$purpose_of_travel);
$newrecord -> AddDBParam('purpose_of_travel_descr',$_GET['purpose_of_travel_descr']);
$newrecord -> AddDBParam('event_name',$_GET['event_name']);
$newrecord -> AddDBParam('event_venue',$_GET['event_venue']);
$newrecord -> AddDBParam('event_venue_addr',$_GET['event_venue_addr']);
$newrecord -> AddDBParam('event_start_date',$_GET['event_start_date']);
$newrecord -> AddDBParam('event_end_date',$_GET['event_end_date']);
$newrecord -> AddDBParam('event_start_time',$_GET['event_start_time']);
$newrecord -> AddDBParam('event_end_time',$_GET['event_end_time']);
$newrecord -> AddDBParam('leave_date_requested',$_SESSION['leave_date_requested']);
$newrecord -> AddDBParam('return_date_requested',$_SESSION['return_date_requested']);
$newrecord -> AddDBParam('leave_time_requested',$_GET['leave_time_requested']);
$newrecord -> AddDBParam('return_time_requested',$_GET['return_time_requested']);
$newrecord -> AddDBParam('budget_code',$budget_code);
$newrecord -> AddDBParam('budget_code_FFS_code',$_GET['budget_code_FFS_code']);
$newrecord -> AddDBParam('budget_code_CPL_code',$_GET['budget_code_CPL_code']);
$newrecord -> AddDBParam('budget_code_other',$_GET['budget_code_other']);
$newrecord -> AddDBParam('budget_code_instructions',$_GET['budget_code_instructions']);
$newrecord -> AddDBParam('trans_pers_veh_utilized',$_GET['trans_pers_veh_utilized']);
$newrecord -> AddDBParam('trans_pers_veh_approx_mileage',$_GET['trans_pers_veh_approx_mileage']);
$newrecord -> AddDBParam('trans_airline_requested',$_GET['trans_airline_requested']);
$newrecord -> AddDBParam('trans_airline_preferred_carrier',$_GET['trans_airline_preferred_carrier']);
$newrecord -> AddDBParam('trans_airline_bta_prepaid',$_GET['trans_airline_bta_prepaid']);
$newrecord -> AddDBParam('trans_rental_car_requested',$_GET['trans_rental_car_requested']);
$newrecord -> AddDBParam('trans_rental_car_num_days_requested',$_GET['trans_rental_car_num_days_requested']);
$newrecord -> AddDBParam('trans_rental_car_justification',$_GET['trans_rental_car_justification']);
$newrecord -> AddDBParam('trans_traveling_with_other_staff',$_GET['trans_traveling_with_other_staff']);
$newrecord -> AddDBParam('trans_traveling_with_name',$_GET['trans_traveling_with_name']);
$newrecord -> AddDBParam('travel_advance_requested',$_GET['travel_advance_requested']);
$newrecord -> AddDBParam('preferred_hotel_name',$_GET['preferred_hotel_name']);
$newrecord -> AddDBParam('preferred_hotel_is_conf_hotel',$_GET['preferred_hotel_is_conf_hotel']);
$newrecord -> AddDBParam('preferred_hotel_other_justification',$_GET['preferred_hotel_other_justification']);
$newrecord -> AddDBParam('preferred_hotel_addr',$_GET['preferred_hotel_addr']);
$newrecord -> AddDBParam('preferred_hotel_city',$_GET['preferred_hotel_city']);
$newrecord -> AddDBParam('preferred_hotel_state',$_GET['preferred_hotel_state']);
$newrecord -> AddDBParam('preferred_hotel_zip',$_GET['preferred_hotel_zip']);
$newrecord -> AddDBParam('preferred_hotel_phone',$_GET['preferred_hotel_phone']);
$newrecord -> AddDBParam('preferred_hotel_fax',$_GET['preferred_hotel_fax']);
$newrecord -> AddDBParam('hotel_nights_requested',$_GET['hotel_nights_requested']);
$newrecord -> AddDBParam('hotel_rate',$_GET['hotel_rate']);
$newrecord -> AddDBParam('other_information',$_GET['other_information']);
$newrecord -> AddDBParam('num_dest','1');

if($_SESSION['num_dest'] > 1){

	$newrecord -> AddDBParam('multi_dest','yes');
	
	$newrecord -> AddDBParam('event_venue_city1',$_SESSION['event_venue_city1']);
	$newrecord -> AddDBParam('event_venue_state1',$_SESSION['event_venue_state1']);
	$newrecord -> AddDBParam('event_venue_city1_travel_start',$_SESSION['event_venue_city_date_from1']);
	$newrecord -> AddDBParam('event_venue_city1_travel_end',$_SESSION['event_venue_city_date_to1']);
	
	$newrecord -> AddDBParam('event_venue_city2',$_SESSION['event_venue_city2']);
	$newrecord -> AddDBParam('event_venue_state2',$_SESSION['event_venue_state2']);
	$newrecord -> AddDBParam('event_venue_city2_travel_start',$_SESSION['event_venue_city_date_from2']);
	$newrecord -> AddDBParam('event_venue_city2_travel_end',$_SESSION['event_venue_city_date_to2']);
	
	$newrecord -> AddDBParam('event_venue_city3',$_SESSION['event_venue_city3']);
	$newrecord -> AddDBParam('event_venue_state3',$_SESSION['event_venue_state3']);
	$newrecord -> AddDBParam('event_venue_city3_travel_start',$_SESSION['event_venue_city_date_from3']);
	$newrecord -> AddDBParam('event_venue_city3_travel_end',$_SESSION['event_venue_city_date_to3']);
	
	$newrecord -> AddDBParam('event_venue_city4',$_SESSION['event_venue_city4']);
	$newrecord -> AddDBParam('event_venue_state4',$_SESSION['event_venue_state4']);
	$newrecord -> AddDBParam('event_venue_city4_travel_start',$_SESSION['event_venue_city_date_from4']);
	$newrecord -> AddDBParam('event_venue_city4_travel_end',$_SESSION['event_venue_city_date_to4']);
	
	$newrecord -> AddDBParam('event_venue_city5',$_SESSION['event_venue_city5']);
	$newrecord -> AddDBParam('event_venue_state5',$_SESSION['event_venue_state5']);
	$newrecord -> AddDBParam('event_venue_city5_travel_start',$_SESSION['event_venue_city_date_from5']);
	$newrecord -> AddDBParam('event_venue_city5_travel_end',$_SESSION['event_venue_city_date_to5']);
	
	$newrecord -> AddDBParam('event_venue_city6',$_SESSION['event_venue_city6']);
	$newrecord -> AddDBParam('event_venue_state6',$_SESSION['event_venue_state6']);
	$newrecord -> AddDBParam('event_venue_city6_travel_start',$_SESSION['event_venue_city_date_from6']);
	$newrecord -> AddDBParam('event_venue_city6_travel_end',$_SESSION['event_venue_city_date_to6']);

}else{

	$newrecord -> AddDBParam('event_venue_city',$_SESSION['city']);
	$newrecord -> AddDBParam('event_venue_state',$_SESSION['state']);

}

$newrecordResult = $newrecord -> FMNew();

//  echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//  echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$newrecordData = current($newrecordResult['data']);
#################################################
## END: CREATE NEW TRAVEL REQUEST RECORD ##
#################################################





if($newrecordResult['errorCode'] == '0'){  // THE TRAVEL REQUEST WAS SUCCESSFULLY CREATED

if($_SESSION['num_dest'] == 1){ // IF THIS IS A SINGLE DESTINATION TRAVEL REQUEST
	###############################################################################
	## START: POPULATE TRAVEL_AUTH_DAYS TABLE FOR THIS AUTH - SINGLE DESTINATION ##
	###############################################################################
		foreach($_SESSION['travel_days'] as $current) {
			$newrecord = new FX($serverIP,$webCompanionPort);
			$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_days');
			$newrecord -> SetDBPassword($webPW,$webUN);
			$newrecord -> AddDBParam('destination_city',$_SESSION['city']);
			$newrecord -> AddDBParam('destination_state',$_SESSION['state']);
			$newrecord -> AddDBParam('travel_auth_ID',$newrecordData['travel_auth_ID'][0]);
			$newrecord -> AddDBParam('travel_date',$current);
			$newrecordResult = $newrecord -> FMNew();
			//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
			//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
		}
	#############################################################################
	## END: POPULATE TRAVEL_AUTH_DAYS TABLE FOR THIS AUTH - SINGLE DESTINATION ##
	#############################################################################
}

/*
// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SUBMIT_TRAVEL_REQUEST_STAFF');
$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
$newrecord -> AddDBParam('object_ID',$newrecordData['travel_auth_ID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$newrecordData['c_row_ID_cwp'][0]);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
*/


$_SESSION['travel_request_submitted_staff'] = '1';


########################################################
## START: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
########################################################


		//SEND E-MAIL NOTIFICATION TO TRAVEL ADMIN
		
			$to = 'eric.waters@sedl.org,magda.acuna@sedl.org';
			//$to = $newrecordData['staff::travel_admin_sims_user_ID'].'@sedl.org';
			$subject = stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' has submitted a travel request';
			$message = 
			'Dear '.$newrecordData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0].','."\n\n".
			
			'A travel request for '.stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' has been submitted to SIMS.'."\n\n".
			
			'----------'."\n".
			
			'TRAVEL REQUEST DETAILS'."\n\n".
			
			'Event: '.stripslashes($newrecordData['event_name'][0])."\n".
			'Destination: '.$newrecordData['event_venue_city'][0].', '.$newrecordData['event_venue_state'][0]."\n".
			'Date(s) of Travel: '.$newrecordData['leave_date_requested'][0].' to '.$newrecordData['return_date_requested'][0]."\n".

			'----------'."\n\n".
			
			'To process this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$newrecordData['travel_auth_ID'][0].'&action=view'."\n\n".
								
			'------------------------------------------------------------------------------------------------------------------'."\n".
			
			'This is an auto-generated message from the SEDL Information Management System (SIMS)';
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
				
		
		
		
######################################################
## END: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
######################################################


/*

	if($recordData['leave_requests_staff_byStaffID::no_time_leave_approval_required'][0] == '1'){ //CEO STATUS CHECK

		$_SESSION['timesheet_owner_email'] = $recordData['signer_ID_owner'][0].'@sedl.org';
		
		###############################################
		## START: TRIGGER NOTIFICATION E-MAIL TO CEO ##
		###############################################
		
		
				//SEND E-MAIL APPROVED NOTIFICATION TO STAFF MEMBER AND OFTS
				
					$to = $_SESSION['timesheet_owner_email'].',tracy.hoes@sedl.org';
					$subject = 'Your leave request has been approved.';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0].','."\n\n".
					
					'The leave request you submitted for the pay period ending '.$recordData['pay_period_end'][0].' has been approved. No further action is necessary on your part.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
		
					'----------'."\n\n".
					
					'To view this leave request or print a copy for your records, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_spvsr.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
					
					
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
				
				
				
				
		#############################################
		## END: TRIGGER NOTIFICATION E-MAIL TO CEO ##
		#############################################
		
		*/
		
/*		

	} elseif($recordData['signer_imm_spvsr_is_pba'][0] == '0') { //IF STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PBA IS NOT THE SAME PERSON


		$_SESSION['imm_spvsr_email'] = $recordData['signer_ID_imm_spvsr'][0].'@sedl.org';
		
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO SUPERVISOR ##
		######################################################
		
		
				//SEND E-MAIL NOTIFICATION TO IMMEDIATE SUPERVISOR
				
					$to = stripslashes($_SESSION['imm_spvsr_email']);
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a '.$revised_flag.'leave request requiring your approval';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_fullname_imm_spvsr'][0].','."\n\n".
					
					'A '.$revised_flag.'leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
		
					'----------'."\n\n".
					
					'To approve this leave request, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_spvsr.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO SUPERVISOR ##
		####################################################


	} elseif($recordData['signer_imm_spvsr_is_pba'][0] == '1') { //IF STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PBA IS THE SAME PERSON


		$_SESSION['pba_email'] = $recordData['signer_ID_pba'][0].'@sedl.org';
		
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO PBA ##
		######################################################
		
		
					//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY
					
					$to = stripslashes($_SESSION['pba_email']);
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a '.$revised_flag.'leave request requiring your approval';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_fullname_pba'][0].','."\n\n".
					
					'A '.$revised_flag.'leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
					
					'----------'."\n\n".
					
					'To approve this leave request, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_ba.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
					
					
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO PBA ##
		####################################################

	
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

*/










} else { // THERE WAS AN ERROR SUBMITTING THE TRAVEL REQUEST
$_SESSION['travel_request_submitted_staff'] = '2';
$_SESSION['errorCode'] = $newrecordResult['errorCode'];
}
###############################################
## END: UPDATE THE LEAVE REQUEST ##
###############################################

header('Location: http://www.sedl.org/staff/sims/menu_travel.php');
exit;
?>


<? } elseif($action == 'new_submit_multi') { 

//$event_start_date = $_GET['event_start_date_m'].'/'.$_GET['event_start_date_d'].'/'.$_GET['event_start_date_y'];
//$event_end_date = $_GET['event_end_date_m'].'/'.$_GET['event_end_date_d'].'/'.$_GET['event_end_date_y'];
//$leave_date_requested = $_GET['leave_date_requested_m'].'/'.$_GET['leave_date_requested_d'].'/'.$_GET['leave_date_requested_y'];
//$return_date_requested = $_GET['return_date_requested_m'].'/'.$_GET['return_date_requested_d'].'/'.$_GET['return_date_requested_y'];

for($i=0 ; $i<count($_GET['purpose_of_travel']) ; $i++) {
$purpose_of_travel .= $_GET['purpose_of_travel'][$i]."\r"; 
}

for($i=0 ; $i<count($_GET['budget_code']) ; $i++) {
$budget_code .= $_GET['budget_code'][$i]."\r"; 
}

#################################################
## START: CREATE NEW TRAVEL REQUEST RECORD ##
#################################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','travel_authorizations');
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('staff_ID',$_SESSION['staff_ID']);
$newrecord -> AddDBParam('approval_status','Pending');
$newrecord -> AddDBParam('multi_dest','yes');
$newrecord -> AddDBParam('num_dest',$_SESSION['num_dest']);
$newrecord -> AddDBParam('event_name',$_GET['event_name1'].'**');

$newrecord -> AddDBParam('purpose_of_travel',$purpose_of_travel);
$newrecord -> AddDBParam('purpose_of_travel_descr',$_GET['purpose_of_travel_descr']);
$newrecord -> AddDBParam('leave_date_requested',$_GET['leave_date_requested']);
$newrecord -> AddDBParam('return_date_requested',$_GET['return_date_requested']);
$newrecord -> AddDBParam('leave_time_requested',$_GET['leave_time_requested']);
$newrecord -> AddDBParam('return_time_requested',$_GET['return_time_requested']);
$newrecord -> AddDBParam('budget_code',$budget_code);
$newrecord -> AddDBParam('budget_code_FFS_code',$_GET['budget_code_FFS_code']);
$newrecord -> AddDBParam('budget_code_CPL_code',$_GET['budget_code_CPL_code']);
$newrecord -> AddDBParam('budget_code_other',$_GET['budget_code_other']);
$newrecord -> AddDBParam('budget_code_instructions',$_GET['budget_code_instructions']);
$newrecord -> AddDBParam('trans_pers_veh_utilized',$_GET['trans_pers_veh_utilized']);
$newrecord -> AddDBParam('trans_pers_veh_approx_mileage',$_GET['trans_pers_veh_approx_mileage']);
$newrecord -> AddDBParam('trans_airline_requested',$_GET['trans_airline_requested']);
$newrecord -> AddDBParam('trans_airline_preferred_carrier',$_GET['trans_airline_preferred_carrier']);
$newrecord -> AddDBParam('trans_airline_bta_prepaid',$_GET['trans_airline_bta_prepaid']);
$newrecord -> AddDBParam('trans_rental_car_requested',$_GET['trans_rental_car_requested']);
$newrecord -> AddDBParam('trans_rental_car_num_days_requested',$_GET['trans_rental_car_num_days_requested']);
$newrecord -> AddDBParam('trans_rental_car_justification',$_GET['trans_rental_car_justification']);
$newrecord -> AddDBParam('trans_traveling_with_other_staff',$_GET['trans_traveling_with_other_staff']);
$newrecord -> AddDBParam('trans_traveling_with_name',$_GET['trans_traveling_with_name']);
$newrecord -> AddDBParam('travel_advance_requested',$_GET['travel_advance_requested']);
$newrecord -> AddDBParam('other_information',$_GET['other_information']);

$newrecord -> AddDBParam('event_venue_city1',$_SESSION['event_venue_city1']);
$newrecord -> AddDBParam('event_venue_state1',$_SESSION['event_venue_state1']);
$newrecord -> AddDBParam('event_venue_city1_travel_start',$_SESSION['event_venue_city_date_from1']);
$newrecord -> AddDBParam('event_venue_city1_travel_end',$_SESSION['event_venue_city_date_to1']);
$newrecord -> AddDBParam('event_name1',$_GET['event_name1']);
$newrecord -> AddDBParam('event_venue1',$_GET['event_venue1']);
$newrecord -> AddDBParam('event_venue_addr1',$_GET['event_venue_addr1']);
$newrecord -> AddDBParam('event_start_date1',$_GET['event_start_date1']);
$newrecord -> AddDBParam('event_end_date1',$_GET['event_end_date1']);
$newrecord -> AddDBParam('event_start_time1',$_GET['event_start_time1']);
$newrecord -> AddDBParam('event_end_time1',$_GET['event_end_time1']);
$newrecord -> AddDBParam('preferred_hotel_name1',$_GET['preferred_hotel_name1']);
$newrecord -> AddDBParam('preferred_hotel_is_conf_hotel1',$_GET['preferred_hotel_is_conf_hotel1']);
$newrecord -> AddDBParam('preferred_hotel_other_justification1',$_GET['preferred_hotel_other_justification1']);
$newrecord -> AddDBParam('preferred_hotel_addr1',$_GET['preferred_hotel_addr1']);
$newrecord -> AddDBParam('preferred_hotel_city1',$_GET['preferred_hotel_city1']);
$newrecord -> AddDBParam('preferred_hotel_state1',$_GET['preferred_hotel_state1']);
$newrecord -> AddDBParam('preferred_hotel_zip1',$_GET['preferred_hotel_zip1']);
$newrecord -> AddDBParam('preferred_hotel_phone1',$_GET['preferred_hotel_phone1']);
$newrecord -> AddDBParam('preferred_hotel_fax1',$_GET['preferred_hotel_fax1']);
$newrecord -> AddDBParam('hotel_nights_requested1',$_GET['hotel_nights_requested1']);
$newrecord -> AddDBParam('hotel_rate1',$_GET['hotel_rate1']);
$newrecord -> AddDBParam('destination_comments1',$_GET['destination_comments1']);

$newrecord -> AddDBParam('event_venue_city2',$_SESSION['event_venue_city2']);
$newrecord -> AddDBParam('event_venue_state2',$_SESSION['event_venue_state2']);
$newrecord -> AddDBParam('event_venue_city2_travel_start',$_SESSION['event_venue_city_date_from2']);
$newrecord -> AddDBParam('event_venue_city2_travel_end',$_SESSION['event_venue_city_date_to2']);
$newrecord -> AddDBParam('event_name2',$_GET['event_name2']);
$newrecord -> AddDBParam('event_venue2',$_GET['event_venue2']);
$newrecord -> AddDBParam('event_venue_addr2',$_GET['event_venue_addr2']);
$newrecord -> AddDBParam('event_start_date2',$_GET['event_start_date2']);
$newrecord -> AddDBParam('event_end_date2',$_GET['event_end_date2']);
$newrecord -> AddDBParam('event_start_time2',$_GET['event_start_time2']);
$newrecord -> AddDBParam('event_end_time2',$_GET['event_end_time2']);
$newrecord -> AddDBParam('preferred_hotel_name2',$_GET['preferred_hotel_name2']);
$newrecord -> AddDBParam('preferred_hotel_is_conf_hotel2',$_GET['preferred_hotel_is_conf_hotel2']);
$newrecord -> AddDBParam('preferred_hotel_other_justification2',$_GET['preferred_hotel_other_justification2']);
$newrecord -> AddDBParam('preferred_hotel_addr2',$_GET['preferred_hotel_addr2']);
$newrecord -> AddDBParam('preferred_hotel_city2',$_GET['preferred_hotel_city2']);
$newrecord -> AddDBParam('preferred_hotel_state2',$_GET['preferred_hotel_state2']);
$newrecord -> AddDBParam('preferred_hotel_zip2',$_GET['preferred_hotel_zip2']);
$newrecord -> AddDBParam('preferred_hotel_phone2',$_GET['preferred_hotel_phone2']);
$newrecord -> AddDBParam('preferred_hotel_fax2',$_GET['preferred_hotel_fax2']);
$newrecord -> AddDBParam('hotel_nights_requested2',$_GET['hotel_nights_requested2']);
$newrecord -> AddDBParam('hotel_rate2',$_GET['hotel_rate2']);
$newrecord -> AddDBParam('destination_comments2',$_GET['destination_comments2']);

if($_SESSION['num_dest'] > 2){
	
$newrecord -> AddDBParam('event_venue_city3',$_SESSION['event_venue_city3']);
$newrecord -> AddDBParam('event_venue_state3',$_SESSION['event_venue_state3']);
$newrecord -> AddDBParam('event_venue_city3_travel_start',$_SESSION['event_venue_city_date_from3']);
$newrecord -> AddDBParam('event_venue_city3_travel_end',$_SESSION['event_venue_city_date_to3']);
$newrecord -> AddDBParam('event_name3',$_GET['event_name3']);
$newrecord -> AddDBParam('event_venue3',$_GET['event_venue3']);
$newrecord -> AddDBParam('event_venue_addr3',$_GET['event_venue_addr3']);
$newrecord -> AddDBParam('event_start_date3',$_GET['event_start_date3']);
$newrecord -> AddDBParam('event_end_date3',$_GET['event_end_date3']);
$newrecord -> AddDBParam('event_start_time3',$_GET['event_start_time3']);
$newrecord -> AddDBParam('event_end_time3',$_GET['event_end_time3']);
$newrecord -> AddDBParam('preferred_hotel_name3',$_GET['preferred_hotel_name3']);
$newrecord -> AddDBParam('preferred_hotel_is_conf_hotel3',$_GET['preferred_hotel_is_conf_hotel3']);
$newrecord -> AddDBParam('preferred_hotel_other_justification3',$_GET['preferred_hotel_other_justification3']);
$newrecord -> AddDBParam('preferred_hotel_addr3',$_GET['preferred_hotel_addr3']);
$newrecord -> AddDBParam('preferred_hotel_city3',$_GET['preferred_hotel_city3']);
$newrecord -> AddDBParam('preferred_hotel_state3',$_GET['preferred_hotel_state3']);
$newrecord -> AddDBParam('preferred_hotel_zip3',$_GET['preferred_hotel_zip3']);
$newrecord -> AddDBParam('preferred_hotel_phone3',$_GET['preferred_hotel_phone3']);
$newrecord -> AddDBParam('preferred_hotel_fax3',$_GET['preferred_hotel_fax3']);
$newrecord -> AddDBParam('hotel_nights_requested3',$_GET['hotel_nights_requested3']);
$newrecord -> AddDBParam('hotel_rate3',$_GET['hotel_rate3']);
$newrecord -> AddDBParam('destination_comments3',$_GET['destination_comments3']);
	
}

if($_SESSION['num_dest'] > 3){
	
$newrecord -> AddDBParam('event_venue_city4',$_SESSION['event_venue_city4']);
$newrecord -> AddDBParam('event_venue_state4',$_SESSION['event_venue_state4']);
$newrecord -> AddDBParam('event_venue_city4_travel_start',$_SESSION['event_venue_city_date_from4']);
$newrecord -> AddDBParam('event_venue_city4_travel_end',$_SESSION['event_venue_city_date_to4']);
$newrecord -> AddDBParam('event_name4',$_GET['event_name4']);
$newrecord -> AddDBParam('event_venue4',$_GET['event_venue4']);
$newrecord -> AddDBParam('event_venue_addr4',$_GET['event_venue_addr4']);
$newrecord -> AddDBParam('event_start_date4',$_GET['event_start_date4']);
$newrecord -> AddDBParam('event_end_date4',$_GET['event_end_date4']);
$newrecord -> AddDBParam('event_start_time4',$_GET['event_start_time4']);
$newrecord -> AddDBParam('event_end_time4',$_GET['event_end_time4']);
$newrecord -> AddDBParam('preferred_hotel_name4',$_GET['preferred_hotel_name4']);
$newrecord -> AddDBParam('preferred_hotel_is_conf_hotel4',$_GET['preferred_hotel_is_conf_hotel4']);
$newrecord -> AddDBParam('preferred_hotel_other_justification4',$_GET['preferred_hotel_other_justification4']);
$newrecord -> AddDBParam('preferred_hotel_addr4',$_GET['preferred_hotel_addr4']);
$newrecord -> AddDBParam('preferred_hotel_city4',$_GET['preferred_hotel_city4']);
$newrecord -> AddDBParam('preferred_hotel_state4',$_GET['preferred_hotel_state4']);
$newrecord -> AddDBParam('preferred_hotel_zip4',$_GET['preferred_hotel_zip4']);
$newrecord -> AddDBParam('preferred_hotel_phone4',$_GET['preferred_hotel_phone4']);
$newrecord -> AddDBParam('preferred_hotel_fax4',$_GET['preferred_hotel_fax4']);
$newrecord -> AddDBParam('hotel_nights_requested4',$_GET['hotel_nights_requested4']);
$newrecord -> AddDBParam('hotel_rate4',$_GET['hotel_rate4']);
$newrecord -> AddDBParam('destination_comments4',$_GET['destination_comments4']);
	
}

if($_SESSION['num_dest'] > 4){
	
$newrecord -> AddDBParam('event_venue_city5',$_SESSION['event_venue_city5']);
$newrecord -> AddDBParam('event_venue_state5',$_SESSION['event_venue_state5']);
$newrecord -> AddDBParam('event_venue_city5_travel_start',$_SESSION['event_venue_city_date_from5']);
$newrecord -> AddDBParam('event_venue_city5_travel_end',$_SESSION['event_venue_city_date_to5']);
$newrecord -> AddDBParam('event_name5',$_GET['event_name5']);
$newrecord -> AddDBParam('event_venue5',$_GET['event_venue5']);
$newrecord -> AddDBParam('event_venue_addr5',$_GET['event_venue_addr5']);
$newrecord -> AddDBParam('event_start_date5',$_GET['event_start_date5']);
$newrecord -> AddDBParam('event_end_date5',$_GET['event_end_date5']);
$newrecord -> AddDBParam('event_start_time5',$_GET['event_start_time5']);
$newrecord -> AddDBParam('event_end_time5',$_GET['event_end_time5']);
$newrecord -> AddDBParam('preferred_hotel_name5',$_GET['preferred_hotel_name5']);
$newrecord -> AddDBParam('preferred_hotel_is_conf_hotel5',$_GET['preferred_hotel_is_conf_hotel5']);
$newrecord -> AddDBParam('preferred_hotel_other_justification5',$_GET['preferred_hotel_other_justification5']);
$newrecord -> AddDBParam('preferred_hotel_addr5',$_GET['preferred_hotel_addr5']);
$newrecord -> AddDBParam('preferred_hotel_city5',$_GET['preferred_hotel_city5']);
$newrecord -> AddDBParam('preferred_hotel_state5',$_GET['preferred_hotel_state5']);
$newrecord -> AddDBParam('preferred_hotel_zip5',$_GET['preferred_hotel_zip5']);
$newrecord -> AddDBParam('preferred_hotel_phone5',$_GET['preferred_hotel_phone5']);
$newrecord -> AddDBParam('preferred_hotel_fax5',$_GET['preferred_hotel_fax5']);
$newrecord -> AddDBParam('hotel_nights_requested5',$_GET['hotel_nights_requested5']);
$newrecord -> AddDBParam('hotel_rate5',$_GET['hotel_rate5']);
$newrecord -> AddDBParam('destination_comments5',$_GET['destination_comments5']);
	
}

if($_SESSION['num_dest'] > 5){
	
$newrecord -> AddDBParam('event_venue_city6',$_SESSION['event_venue_city6']);
$newrecord -> AddDBParam('event_venue_state6',$_SESSION['event_venue_state6']);
$newrecord -> AddDBParam('event_venue_city6_travel_start',$_SESSION['event_venue_city_date_from6']);
$newrecord -> AddDBParam('event_venue_city6_travel_end',$_SESSION['event_venue_city_date_to6']);
$newrecord -> AddDBParam('event_name6',$_GET['event_name6']);
$newrecord -> AddDBParam('event_venue6',$_GET['event_venue6']);
$newrecord -> AddDBParam('event_venue_addr6',$_GET['event_venue_addr6']);
$newrecord -> AddDBParam('event_start_date6',$_GET['event_start_date6']);
$newrecord -> AddDBParam('event_end_date6',$_GET['event_end_date6']);
$newrecord -> AddDBParam('event_start_time6',$_GET['event_start_time6']);
$newrecord -> AddDBParam('event_end_time6',$_GET['event_end_time6']);
$newrecord -> AddDBParam('preferred_hotel_name6',$_GET['preferred_hotel_name6']);
$newrecord -> AddDBParam('preferred_hotel_is_conf_hotel6',$_GET['preferred_hotel_is_conf_hotel6']);
$newrecord -> AddDBParam('preferred_hotel_other_justification6',$_GET['preferred_hotel_other_justification6']);
$newrecord -> AddDBParam('preferred_hotel_addr6',$_GET['preferred_hotel_addr6']);
$newrecord -> AddDBParam('preferred_hotel_city6',$_GET['preferred_hotel_city6']);
$newrecord -> AddDBParam('preferred_hotel_state6',$_GET['preferred_hotel_state6']);
$newrecord -> AddDBParam('preferred_hotel_zip6',$_GET['preferred_hotel_zip6']);
$newrecord -> AddDBParam('preferred_hotel_phone6',$_GET['preferred_hotel_phone6']);
$newrecord -> AddDBParam('preferred_hotel_fax6',$_GET['preferred_hotel_fax6']);
$newrecord -> AddDBParam('hotel_nights_requested6',$_GET['hotel_nights_requested6']);
$newrecord -> AddDBParam('hotel_rate6',$_GET['hotel_rate6']);
$newrecord -> AddDBParam('destination_comments6',$_GET['destination_comments6']);
	
}

$newrecordResult = $newrecord -> FMNew();

//  echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//  echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$newrecordData = current($newrecordResult['data']);
#################################################
## END: CREATE NEW TRAVEL REQUEST RECORD ##
#################################################





if($newrecordResult['errorCode'] == '0'){  // THE TRAVEL REQUEST WAS SUCCESSFULLY CREATED


/*
// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SUBMIT_TRAVEL_REQUEST_STAFF');
$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
$newrecord -> AddDBParam('object_ID',$newrecordData['travel_auth_ID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$newrecordData['c_row_ID_cwp'][0]);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
*/


$_SESSION['travel_request_submitted_staff'] = '1';


########################################################
## START: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
########################################################


		//SEND E-MAIL NOTIFICATION TO TRAVEL ADMIN
		
			$to = 'eric.waters@sedl.org';
			//$to = $newrecordData['staff::travel_admin_sims_user_ID'].'@sedl.org';
			$subject = stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' has submitted a travel request';
			$message = 
			'Dear '.$newrecordData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0].','."\n\n".
			
			'A travel request for '.stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' has been submitted to SIMS.'."\n\n".
			
			'----------'."\n".
			
			'TRAVEL REQUEST DETAILS'."\n\n".
			
			'Event: '.$newrecordData['event_name'][0]."\n".
			'Destination: '.$newrecordData['event_venue_city1'][0].', '.$newrecordData['event_venue_state1'][0].' ++'."\n".
			'Date(s) of Travel: '.$newrecordData['leave_date_requested'][0].' to '.$newrecordData['return_date_requested'][0]."\n".

			'----------'."\n\n".
			
			'To process this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$newrecordData['travel_auth_ID'][0].'&action=view'."\n\n".
								
			'------------------------------------------------------------------------------------------------------------------'."\n".
			
			'This is an auto-generated message from the SEDL Information Management System (SIMS)';
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
				
		
		
		
######################################################
## END: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
######################################################


/*

	if($recordData['leave_requests_staff_byStaffID::no_time_leave_approval_required'][0] == '1'){ //CEO STATUS CHECK

		$_SESSION['timesheet_owner_email'] = $recordData['signer_ID_owner'][0].'@sedl.org';
		
		###############################################
		## START: TRIGGER NOTIFICATION E-MAIL TO CEO ##
		###############################################
		
		
				//SEND E-MAIL APPROVED NOTIFICATION TO STAFF MEMBER AND OFTS
				
					$to = $_SESSION['timesheet_owner_email'].',tracy.hoes@sedl.org';
					$subject = 'Your leave request has been approved.';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0].','."\n\n".
					
					'The leave request you submitted for the pay period ending '.$recordData['pay_period_end'][0].' has been approved. No further action is necessary on your part.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
		
					'----------'."\n\n".
					
					'To view this leave request or print a copy for your records, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_spvsr.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
					
					
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
				
				
				
				
		#############################################
		## END: TRIGGER NOTIFICATION E-MAIL TO CEO ##
		#############################################

	} elseif($recordData['signer_imm_spvsr_is_pba'][0] == '0') { //IF STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PBA IS NOT THE SAME PERSON


		$_SESSION['imm_spvsr_email'] = $recordData['signer_ID_imm_spvsr'][0].'@sedl.org';
		
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO SUPERVISOR ##
		######################################################
		
		
				//SEND E-MAIL NOTIFICATION TO IMMEDIATE SUPERVISOR
				
					$to = stripslashes($_SESSION['imm_spvsr_email']);
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a '.$revised_flag.'leave request requiring your approval';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_fullname_imm_spvsr'][0].','."\n\n".
					
					'A '.$revised_flag.'leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
		
					'----------'."\n\n".
					
					'To approve this leave request, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_spvsr.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO SUPERVISOR ##
		####################################################


	} elseif($recordData['signer_imm_spvsr_is_pba'][0] == '1') { //IF STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PBA IS THE SAME PERSON


		$_SESSION['pba_email'] = $recordData['signer_ID_pba'][0].'@sedl.org';
		
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO PBA ##
		######################################################
		
		
					//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY
					
					$to = stripslashes($_SESSION['pba_email']);
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a '.$revised_flag.'leave request requiring your approval';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_fullname_pba'][0].','."\n\n".
					
					'A '.$revised_flag.'leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
					
					'----------'."\n\n".
					
					'To approve this leave request, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_ba.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
					
					
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO PBA ##
		####################################################

	
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

*/










} else { // THERE WAS AN ERROR SUBMITTING THE TRAVEL REQUEST
$_SESSION['travel_request_submitted_staff'] = '2';
$_SESSION['errorCode'] = $newrecordResult['errorCode'];
}
###############################################
## END: UPDATE THE LEAVE REQUEST ##
###############################################

header('Location: http://www.sedl.org/staff/sims/menu_travel.php');
exit;
?>





<? } elseif($action == 'edit_confirm') { 

$row_ID = $_GET['row_ID'];
//$event_start_date = $_GET['event_start_date_m'].'/'.$_GET['event_start_date_d'].'/'.$_GET['event_start_date_y'];
//$event_end_date = $_GET['event_end_date_m'].'/'.$_GET['event_end_date_d'].'/'.$_GET['event_end_date_y'];
//$leave_date_requested = $_GET['leave_date_requested_m'].'/'.$_GET['leave_date_requested_d'].'/'.$_GET['leave_date_requested_y'];
//$return_date_requested = $_GET['return_date_requested_m'].'/'.$_GET['return_date_requested_d'].'/'.$_GET['return_date_requested_y'];

for($i=0 ; $i<count($_GET['purpose_of_travel']) ; $i++) {
$purpose_of_travel .= $_GET['purpose_of_travel'][$i]."\r"; 
}

for($i=0 ; $i<count($_GET['budget_code']) ; $i++) {
$budget_code .= $_GET['budget_code'][$i]."\r"; 
}

#################################################
## START: UPDATE TRAVEL REQUEST RECORD ##
#################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$row_ID);

$update -> AddDBParam('staff_ID',$_SESSION['staff_ID']);
$update -> AddDBParam('approval_status','Pending');

$update -> AddDBParam('leave_date_requested',$_GET['leave_date_requested']);
$update -> AddDBParam('return_date_requested',$_GET['return_date_requested']);
$update -> AddDBParam('leave_time_requested',$_GET['leave_time_requested']);
$update -> AddDBParam('return_time_requested',$_GET['return_time_requested']);
$update -> AddDBParam('budget_code',$budget_code);
$update -> AddDBParam('budget_code_FFS_code',$_GET['budget_code_FFS_code']);
$update -> AddDBParam('budget_code_CPL_code',$_GET['budget_code_CPL_code']);
$update -> AddDBParam('budget_code_other',$_GET['budget_code_other']);
$update -> AddDBParam('budget_code_instructions',$_GET['budget_code_instructions']);
$update -> AddDBParam('trans_pers_veh_utilized',$_GET['trans_pers_veh_utilized']);
$update -> AddDBParam('trans_pers_veh_approx_mileage',$_GET['trans_pers_veh_approx_mileage']);
$update -> AddDBParam('trans_airline_requested',$_GET['trans_airline_requested']);
$update -> AddDBParam('trans_airline_preferred_carrier',$_GET['trans_airline_preferred_carrier']);
$update -> AddDBParam('trans_airline_bta_prepaid',$_GET['trans_airline_bta_prepaid']);
$update -> AddDBParam('trans_rental_car_requested',$_GET['trans_rental_car_requested']);
$update -> AddDBParam('trans_rental_car_num_days_requested',$_GET['trans_rental_car_num_days_requested']);
$update -> AddDBParam('trans_rental_car_justification',$_GET['trans_rental_car_justification']);
$update -> AddDBParam('trans_traveling_with_other_staff',$_GET['trans_traveling_with_other_staff']);
$update -> AddDBParam('trans_traveling_with_name',$_GET['trans_traveling_with_name']);
$update -> AddDBParam('travel_advance_requested',$_GET['travel_advance_requested']);
$update -> AddDBParam('other_information',$_GET['other_information']);

$update -> AddDBParam('purpose_of_travel',$purpose_of_travel);
$update -> AddDBParam('purpose_of_travel_descr',$_GET['purpose_of_travel_descr']);
$update -> AddDBParam('event_name',$_GET['event_name']);
$update -> AddDBParam('event_venue',$_GET['event_venue']);
$update -> AddDBParam('event_venue_addr',$_GET['event_venue_addr']);
$update -> AddDBParam('event_start_date',$_GET['event_start_date']);
$update -> AddDBParam('event_end_date',$_GET['event_end_date']);
$update -> AddDBParam('event_start_time',$_GET['event_start_time']);
$update -> AddDBParam('event_end_time',$_GET['event_end_time']);
$update -> AddDBParam('preferred_hotel_name',$_GET['preferred_hotel_name']);
$update -> AddDBParam('preferred_hotel_is_conf_hotel',$_GET['preferred_hotel_is_conf_hotel']);
$update -> AddDBParam('preferred_hotel_other_justification',$_GET['preferred_hotel_other_justification']);
$update -> AddDBParam('preferred_hotel_addr',$_GET['preferred_hotel_addr']);
$update -> AddDBParam('preferred_hotel_city',$_GET['preferred_hotel_city']);
$update -> AddDBParam('preferred_hotel_state',$_GET['preferred_hotel_state']);
$update -> AddDBParam('preferred_hotel_zip',$_GET['preferred_hotel_zip']);
$update -> AddDBParam('preferred_hotel_phone',$_GET['preferred_hotel_phone']);
$update -> AddDBParam('preferred_hotel_fax',$_GET['preferred_hotel_fax']);
$update -> AddDBParam('hotel_nights_requested',$_GET['hotel_nights_requested']);
$update -> AddDBParam('hotel_rate',$_GET['hotel_rate']);




$updateResult = $update -> FMEdit();

//  echo  '<p>errorCode: '.$updateResult['errorCode'];
//  echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
#################################################
## END: UPDATE TRAVEL REQUEST RECORD ##
#################################################





if($updateResult['errorCode'] == '0'){  // THE TRAVEL REQUEST WAS SUCCESSFULLY UPDATED

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SUBMIT_UPDATED_TRAVEL_REQUEST_STAFF');
$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
$newrecord -> AddDBParam('object_ID',$newrecordData['travel_auth_ID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$newrecordData['c_row_ID_cwp'][0]);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];



$_SESSION['travel_request_submitted_staff'] = '1';



########################################################
## START: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
########################################################


		//SEND E-MAIL NOTIFICATION TO TRAVEL ADMIN
		
			$to = 'eric.waters@sedl.org';
			//$to = $updateData['staff::travel_admin_sims_user_ID'].'@sedl.org';
			$subject = stripslashes($updateData['staff::c_full_name_first_last'][0]).' has submitted a revised travel request';
			$message = 
			'Dear '.$updateData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0].','."\n\n".
			
			'A revised travel request for '.stripslashes($updateData['staff::c_full_name_first_last'][0]).' has been submitted to SIMS.'."\n\n".
			
			'----------'."\n".
			
			'TRAVEL REQUEST DETAILS'."\n\n".
			
			'Event: '.stripslashes($updateData['event_name'][0])."\n".
			'Destination: '.stripslashes($updateData['event_venue_city'][0]).', '.$updateData['event_venue_state'][0]."\n".
			'Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".

			'----------'."\n\n".
			
			'To process this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0].'&action=view'."\n\n".
								
			'------------------------------------------------------------------------------------------------------------------'."\n".
			
			'This is an auto-generated message from the SEDL Information Management System (SIMS)';
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
				
		
		
		
######################################################
## END: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
######################################################

/*

	if($recordData['leave_requests_staff_byStaffID::no_time_leave_approval_required'][0] == '1'){ //CEO STATUS CHECK

		$_SESSION['timesheet_owner_email'] = $recordData['signer_ID_owner'][0].'@sedl.org';
		
		###############################################
		## START: TRIGGER NOTIFICATION E-MAIL TO CEO ##
		###############################################
		
		
				//SEND E-MAIL APPROVED NOTIFICATION TO STAFF MEMBER AND OFTS
				
					$to = $_SESSION['timesheet_owner_email'].',tracy.hoes@sedl.org';
					$subject = 'Your leave request has been approved.';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0].','."\n\n".
					
					'The leave request you submitted for the pay period ending '.$recordData['pay_period_end'][0].' has been approved. No further action is necessary on your part.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
		
					'----------'."\n\n".
					
					'To view this leave request or print a copy for your records, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_spvsr.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
					
					
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
				
				
				
				
		#############################################
		## END: TRIGGER NOTIFICATION E-MAIL TO CEO ##
		#############################################

	} elseif($recordData['signer_imm_spvsr_is_pba'][0] == '0') { //IF STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PBA IS NOT THE SAME PERSON


		$_SESSION['imm_spvsr_email'] = $recordData['signer_ID_imm_spvsr'][0].'@sedl.org';
		
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO SUPERVISOR ##
		######################################################
		
		
				//SEND E-MAIL NOTIFICATION TO IMMEDIATE SUPERVISOR
				
					$to = stripslashes($_SESSION['imm_spvsr_email']);
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a '.$revised_flag.'leave request requiring your approval';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_fullname_imm_spvsr'][0].','."\n\n".
					
					'A '.$revised_flag.'leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
		
					'----------'."\n\n".
					
					'To approve this leave request, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_spvsr.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO SUPERVISOR ##
		####################################################


	} elseif($recordData['signer_imm_spvsr_is_pba'][0] == '1') { //IF STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PBA IS THE SAME PERSON


		$_SESSION['pba_email'] = $recordData['signer_ID_pba'][0].'@sedl.org';
		
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO PBA ##
		######################################################
		
		
					//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY
					
					$to = stripslashes($_SESSION['pba_email']);
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a '.$revised_flag.'leave request requiring your approval';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_fullname_pba'][0].','."\n\n".
					
					'A '.$revised_flag.'leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
					
					'----------'."\n\n".
					
					'To approve this leave request, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_ba.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
					
					
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO PBA ##
		####################################################

	
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

*/










} else { // THERE WAS AN ERROR UPDATING THE LEAVE REQUEST
$_SESSION['travel_request_submitted_staff'] = '2';

}
###############################################
## END: UPDATE THE LEAVE REQUEST ##
###############################################

header('Location: http://www.sedl.org/staff/sims/menu_travel.php');
exit;
?>



<? } elseif($action == 'edit_confirm_multi') { 

$num_dest = $_GET['num_dest'];
$row_ID = $_GET['row_ID'];
//$event_start_date = $_GET['event_start_date_m'].'/'.$_GET['event_start_date_d'].'/'.$_GET['event_start_date_y'];
//$event_end_date = $_GET['event_end_date_m'].'/'.$_GET['event_end_date_d'].'/'.$_GET['event_end_date_y'];
//$leave_date_requested = $_GET['leave_date_requested_m'].'/'.$_GET['leave_date_requested_d'].'/'.$_GET['leave_date_requested_y'];
//$return_date_requested = $_GET['return_date_requested_m'].'/'.$_GET['return_date_requested_d'].'/'.$_GET['return_date_requested_y'];

for($i=0 ; $i<count($_GET['purpose_of_travel']) ; $i++) {
$purpose_of_travel .= $_GET['purpose_of_travel'][$i]."\r"; 
}

for($i=0 ; $i<count($_GET['budget_code']) ; $i++) {
$budget_code .= $_GET['budget_code'][$i]."\r"; 
}

#################################################
## START: UPDATE TRAVEL REQUEST RECORD ##
#################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$row_ID);

$update -> AddDBParam('staff_ID',$_SESSION['staff_ID']);
$update -> AddDBParam('approval_status','Pending');

$update -> AddDBParam('purpose_of_travel',$purpose_of_travel);
$update -> AddDBParam('purpose_of_travel_descr',$_GET['purpose_of_travel_descr']);
$update -> AddDBParam('leave_date_requested',$_GET['leave_date_requested']);
$update -> AddDBParam('return_date_requested',$_GET['return_date_requested']);
$update -> AddDBParam('leave_time_requested',$_GET['leave_time_requested']);
$update -> AddDBParam('return_time_requested',$_GET['return_time_requested']);
$update -> AddDBParam('budget_code',$budget_code);
$update -> AddDBParam('budget_code_FFS_code',$_GET['budget_code_FFS_code']);
$update -> AddDBParam('budget_code_CPL_code',$_GET['budget_code_CPL_code']);
$update -> AddDBParam('budget_code_other',$_GET['budget_code_other']);
$update -> AddDBParam('budget_code_instructions',$_GET['budget_code_instructions']);
$update -> AddDBParam('trans_pers_veh_utilized',$_GET['trans_pers_veh_utilized']);
$update -> AddDBParam('trans_pers_veh_approx_mileage',$_GET['trans_pers_veh_approx_mileage']);
$update -> AddDBParam('trans_airline_requested',$_GET['trans_airline_requested']);
$update -> AddDBParam('trans_airline_preferred_carrier',$_GET['trans_airline_preferred_carrier']);
$update -> AddDBParam('trans_airline_bta_prepaid',$_GET['trans_airline_bta_prepaid']);
$update -> AddDBParam('trans_rental_car_requested',$_GET['trans_rental_car_requested']);
$update -> AddDBParam('trans_rental_car_num_days_requested',$_GET['trans_rental_car_num_days_requested']);
$update -> AddDBParam('trans_rental_car_justification',$_GET['trans_rental_car_justification']);
$update -> AddDBParam('trans_traveling_with_other_staff',$_GET['trans_traveling_with_other_staff']);
$update -> AddDBParam('trans_traveling_with_name',$_GET['trans_traveling_with_name']);
$update -> AddDBParam('travel_advance_requested',$_GET['travel_advance_requested']);
$update -> AddDBParam('other_information',$_GET['other_information']);



$update -> AddDBParam('event_name1',$_GET['event_name1']);
$update -> AddDBParam('event_venue1',$_GET['event_venue1']);
$update -> AddDBParam('event_venue_addr1',$_GET['event_venue_addr1']);
$update -> AddDBParam('event_start_date1',$_GET['event_start_date1']);
$update -> AddDBParam('event_end_date1',$_GET['event_end_date1']);
$update -> AddDBParam('event_start_time1',$_GET['event_start_time1']);
$update -> AddDBParam('event_end_time1',$_GET['event_end_time1']);
$update -> AddDBParam('preferred_hotel_name1',$_GET['preferred_hotel_name1']);
$update -> AddDBParam('preferred_hotel_is_conf_hotel1',$_GET['preferred_hotel_is_conf_hotel1']);
$update -> AddDBParam('preferred_hotel_other_justification1',$_GET['preferred_hotel_other_justification1']);
$update -> AddDBParam('preferred_hotel_addr1',$_GET['preferred_hotel_addr1']);
$update -> AddDBParam('preferred_hotel_city1',$_GET['preferred_hotel_city1']);
$update -> AddDBParam('preferred_hotel_state1',$_GET['preferred_hotel_state1']);
$update -> AddDBParam('preferred_hotel_zip1',$_GET['preferred_hotel_zip1']);
$update -> AddDBParam('preferred_hotel_phone1',$_GET['preferred_hotel_phone1']);
$update -> AddDBParam('preferred_hotel_fax1',$_GET['preferred_hotel_fax1']);
$update -> AddDBParam('hotel_nights_requested1',$_GET['hotel_nights_requested1']);
$update -> AddDBParam('hotel_rate1',$_GET['hotel_rate1']);
$update -> AddDBParam('destination_comments1',$_GET['destination_comments1']);



$update -> AddDBParam('event_name2',$_GET['event_name2']);
$update -> AddDBParam('event_venue2',$_GET['event_venue2']);
$update -> AddDBParam('event_venue_addr2',$_GET['event_venue_addr2']);
$update -> AddDBParam('event_start_date2',$_GET['event_start_date2']);
$update -> AddDBParam('event_end_date2',$_GET['event_end_date2']);
$update -> AddDBParam('event_start_time2',$_GET['event_start_time2']);
$update -> AddDBParam('event_end_time2',$_GET['event_end_time2']);
$update -> AddDBParam('preferred_hotel_name2',$_GET['preferred_hotel_name2']);
$update -> AddDBParam('preferred_hotel_is_conf_hotel2',$_GET['preferred_hotel_is_conf_hotel2']);
$update -> AddDBParam('preferred_hotel_other_justification2',$_GET['preferred_hotel_other_justification2']);
$update -> AddDBParam('preferred_hotel_addr2',$_GET['preferred_hotel_addr2']);
$update -> AddDBParam('preferred_hotel_city2',$_GET['preferred_hotel_city2']);
$update -> AddDBParam('preferred_hotel_state2',$_GET['preferred_hotel_state2']);
$update -> AddDBParam('preferred_hotel_zip2',$_GET['preferred_hotel_zip2']);
$update -> AddDBParam('preferred_hotel_phone2',$_GET['preferred_hotel_phone2']);
$update -> AddDBParam('preferred_hotel_fax2',$_GET['preferred_hotel_fax2']);
$update -> AddDBParam('hotel_nights_requested2',$_GET['hotel_nights_requested2']);
$update -> AddDBParam('hotel_rate2',$_GET['hotel_rate2']);
$update -> AddDBParam('destination_comments2',$_GET['destination_comments2']);


if($num_dest > 2){
$update -> AddDBParam('event_name3',$_GET['event_name3']);
$update -> AddDBParam('event_venue3',$_GET['event_venue3']);
$update -> AddDBParam('event_venue_addr3',$_GET['event_venue_addr3']);
$update -> AddDBParam('event_start_date3',$_GET['event_start_date3']);
$update -> AddDBParam('event_end_date3',$_GET['event_end_date3']);
$update -> AddDBParam('event_start_time3',$_GET['event_start_time3']);
$update -> AddDBParam('event_end_time3',$_GET['event_end_time3']);
$update -> AddDBParam('preferred_hotel_name3',$_GET['preferred_hotel_name3']);
$update -> AddDBParam('preferred_hotel_is_conf_hotel3',$_GET['preferred_hotel_is_conf_hotel3']);
$update -> AddDBParam('preferred_hotel_other_justification3',$_GET['preferred_hotel_other_justification3']);
$update -> AddDBParam('preferred_hotel_addr3',$_GET['preferred_hotel_addr3']);
$update -> AddDBParam('preferred_hotel_city3',$_GET['preferred_hotel_city3']);
$update -> AddDBParam('preferred_hotel_state3',$_GET['preferred_hotel_state3']);
$update -> AddDBParam('preferred_hotel_zip3',$_GET['preferred_hotel_zip3']);
$update -> AddDBParam('preferred_hotel_phone3',$_GET['preferred_hotel_phone3']);
$update -> AddDBParam('preferred_hotel_fax3',$_GET['preferred_hotel_fax3']);
$update -> AddDBParam('hotel_nights_requested3',$_GET['hotel_nights_requested3']);
$update -> AddDBParam('hotel_rate3',$_GET['hotel_rate3']);
$update -> AddDBParam('destination_comments3',$_GET['destination_comments3']);
}

if($num_dest > 3){
$update -> AddDBParam('event_name4',$_GET['event_name4']);
$update -> AddDBParam('event_venue4',$_GET['event_venue4']);
$update -> AddDBParam('event_venue_addr4',$_GET['event_venue_addr4']);
$update -> AddDBParam('event_start_date4',$_GET['event_start_date4']);
$update -> AddDBParam('event_end_date4',$_GET['event_end_date4']);
$update -> AddDBParam('event_start_time4',$_GET['event_start_time4']);
$update -> AddDBParam('event_end_time4',$_GET['event_end_time4']);
$update -> AddDBParam('preferred_hotel_name4',$_GET['preferred_hotel_name4']);
$update -> AddDBParam('preferred_hotel_is_conf_hotel4',$_GET['preferred_hotel_is_conf_hotel4']);
$update -> AddDBParam('preferred_hotel_other_justification4',$_GET['preferred_hotel_other_justification4']);
$update -> AddDBParam('preferred_hotel_addr4',$_GET['preferred_hotel_addr4']);
$update -> AddDBParam('preferred_hotel_city4',$_GET['preferred_hotel_city4']);
$update -> AddDBParam('preferred_hotel_state4',$_GET['preferred_hotel_state4']);
$update -> AddDBParam('preferred_hotel_zip4',$_GET['preferred_hotel_zip4']);
$update -> AddDBParam('preferred_hotel_phone4',$_GET['preferred_hotel_phone4']);
$update -> AddDBParam('preferred_hotel_fax4',$_GET['preferred_hotel_fax4']);
$update -> AddDBParam('hotel_nights_requested4',$_GET['hotel_nights_requested4']);
$update -> AddDBParam('hotel_rate4',$_GET['hotel_rate4']);
$update -> AddDBParam('destination_comments4',$_GET['destination_comments4']);
}

if($num_dest > 4){
$update -> AddDBParam('event_name5',$_GET['event_name5']);
$update -> AddDBParam('event_venue5',$_GET['event_venue5']);
$update -> AddDBParam('event_venue_addr5',$_GET['event_venue_addr5']);
$update -> AddDBParam('event_start_date5',$_GET['event_start_date5']);
$update -> AddDBParam('event_end_date5',$_GET['event_end_date5']);
$update -> AddDBParam('event_start_time5',$_GET['event_start_time5']);
$update -> AddDBParam('event_end_time5',$_GET['event_end_time5']);
$update -> AddDBParam('preferred_hotel_name5',$_GET['preferred_hotel_name5']);
$update -> AddDBParam('preferred_hotel_is_conf_hotel5',$_GET['preferred_hotel_is_conf_hotel5']);
$update -> AddDBParam('preferred_hotel_other_justification5',$_GET['preferred_hotel_other_justification5']);
$update -> AddDBParam('preferred_hotel_addr5',$_GET['preferred_hotel_addr5']);
$update -> AddDBParam('preferred_hotel_city5',$_GET['preferred_hotel_city5']);
$update -> AddDBParam('preferred_hotel_state5',$_GET['preferred_hotel_state5']);
$update -> AddDBParam('preferred_hotel_zip5',$_GET['preferred_hotel_zip5']);
$update -> AddDBParam('preferred_hotel_phone5',$_GET['preferred_hotel_phone5']);
$update -> AddDBParam('preferred_hotel_fax5',$_GET['preferred_hotel_fax5']);
$update -> AddDBParam('hotel_nights_requested5',$_GET['hotel_nights_requested5']);
$update -> AddDBParam('hotel_rate5',$_GET['hotel_rate5']);
$update -> AddDBParam('destination_comments5',$_GET['destination_comments5']);
}

if($num_dest > 5){
$update -> AddDBParam('event_name6',$_GET['event_name6']);
$update -> AddDBParam('event_venue6',$_GET['event_venue6']);
$update -> AddDBParam('event_venue_addr6',$_GET['event_venue_addr6']);
$update -> AddDBParam('event_start_date6',$_GET['event_start_date6']);
$update -> AddDBParam('event_end_date6',$_GET['event_end_date6']);
$update -> AddDBParam('event_start_time6',$_GET['event_start_time6']);
$update -> AddDBParam('event_end_time6',$_GET['event_end_time6']);
$update -> AddDBParam('preferred_hotel_name6',$_GET['preferred_hotel_name6']);
$update -> AddDBParam('preferred_hotel_is_conf_hotel6',$_GET['preferred_hotel_is_conf_hotel6']);
$update -> AddDBParam('preferred_hotel_other_justification6',$_GET['preferred_hotel_other_justification6']);
$update -> AddDBParam('preferred_hotel_addr6',$_GET['preferred_hotel_addr6']);
$update -> AddDBParam('preferred_hotel_city6',$_GET['preferred_hotel_city6']);
$update -> AddDBParam('preferred_hotel_state6',$_GET['preferred_hotel_state6']);
$update -> AddDBParam('preferred_hotel_zip6',$_GET['preferred_hotel_zip6']);
$update -> AddDBParam('preferred_hotel_phone6',$_GET['preferred_hotel_phone6']);
$update -> AddDBParam('preferred_hotel_fax6',$_GET['preferred_hotel_fax6']);
$update -> AddDBParam('hotel_nights_requested6',$_GET['hotel_nights_requested6']);
$update -> AddDBParam('hotel_rate6',$_GET['hotel_rate6']);
$update -> AddDBParam('destination_comments6',$_GET['destination_comments6']);
}


$updateResult = $update -> FMEdit();

//  echo  '<p>errorCode: '.$updateResult['errorCode'];
//  echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
#################################################
## END: UPDATE TRAVEL REQUEST RECORD ##
#################################################





if($updateResult['errorCode'] == '0'){  // THE TRAVEL REQUEST WAS SUCCESSFULLY UPDATED

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SUBMIT_UPDATED_TRAVEL_REQUEST_STAFF');
$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
$newrecord -> AddDBParam('object_ID',$newrecordData['travel_auth_ID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$newrecordData['c_row_ID_cwp'][0]);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];



$_SESSION['travel_request_submitted_staff'] = '1';



########################################################
## START: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
########################################################


		//SEND E-MAIL NOTIFICATION TO TRAVEL ADMIN
		
			$to = 'eric.waters@sedl.org';
			//$to = $updateData['staff::travel_admin_sims_user_ID'].'@sedl.org';
			$subject = stripslashes($updateData['staff::c_full_name_first_last'][0]).' has submitted a revised travel request';
			$message = 
			'Dear '.$updateData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0].','."\n\n".
			
			'A revised travel request for '.stripslashes($updateData['staff::c_full_name_first_last'][0]).' has been submitted to SIMS.'."\n\n".
			
			'----------'."\n".
			
			'TRAVEL REQUEST DETAILS'."\n\n".
			
			'Event: '.$updateData['event_name'][0]."\n".
			'Destination: '.$updateData['event_venue_city1'][0].', '.$updateData['event_venue_state1'][0].'**'."\n".
			'Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".

			'----------'."\n\n".
			
			'To process this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0].'&action=view'."\n\n".
								
			'------------------------------------------------------------------------------------------------------------------'."\n".
			
			'This is an auto-generated message from the SEDL Information Management System (SIMS)';
			
			$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
				
		
		
		
######################################################
## END: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
######################################################

/*

	if($recordData['leave_requests_staff_byStaffID::no_time_leave_approval_required'][0] == '1'){ //CEO STATUS CHECK

		$_SESSION['timesheet_owner_email'] = $recordData['signer_ID_owner'][0].'@sedl.org';
		
		###############################################
		## START: TRIGGER NOTIFICATION E-MAIL TO CEO ##
		###############################################
		
		
				//SEND E-MAIL APPROVED NOTIFICATION TO STAFF MEMBER AND OFTS
				
					$to = $_SESSION['timesheet_owner_email'].',tracy.hoes@sedl.org';
					$subject = 'Your leave request has been approved.';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0].','."\n\n".
					
					'The leave request you submitted for the pay period ending '.$recordData['pay_period_end'][0].' has been approved. No further action is necessary on your part.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
		
					'----------'."\n\n".
					
					'To view this leave request or print a copy for your records, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_spvsr.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
					
					
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
				
				
				
				
		#############################################
		## END: TRIGGER NOTIFICATION E-MAIL TO CEO ##
		#############################################

	} elseif($recordData['signer_imm_spvsr_is_pba'][0] == '0') { //IF STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PBA IS NOT THE SAME PERSON


		$_SESSION['imm_spvsr_email'] = $recordData['signer_ID_imm_spvsr'][0].'@sedl.org';
		
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO SUPERVISOR ##
		######################################################
		
		
				//SEND E-MAIL NOTIFICATION TO IMMEDIATE SUPERVISOR
				
					$to = stripslashes($_SESSION['imm_spvsr_email']);
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a '.$revised_flag.'leave request requiring your approval';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_fullname_imm_spvsr'][0].','."\n\n".
					
					'A '.$revised_flag.'leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
		
					'----------'."\n\n".
					
					'To approve this leave request, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_spvsr.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO SUPERVISOR ##
		####################################################


	} elseif($recordData['signer_imm_spvsr_is_pba'][0] == '1') { //IF STAFF MEMBER'S IMMEDIATE SUPERVISOR AND PBA IS THE SAME PERSON


		$_SESSION['pba_email'] = $recordData['signer_ID_pba'][0].'@sedl.org';
		
		######################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO PBA ##
		######################################################
		
		
					//SEND E-MAIL NOTIFICATION TO PRIMARY BUDGET AUTHORITY
					
					$to = stripslashes($_SESSION['pba_email']);
					$subject = stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has submitted a '.$revised_flag.'leave request requiring your approval';
					$message = 
					'Dear '.$recordData['leave_requests_staff_byStaffID::c_fullname_pba'][0].','."\n\n".
					
					'A '.$revised_flag.'leave request for '.stripslashes($recordData['leave_requests_staff_byStaffID::c_full_name_first_last'][0]).' has been submitted for the pay period ending '.$recordData['pay_period_end'][0].'.'."\n\n".
					
					'----------'."\n".
					
					'LEAVE REQUEST HOURS'."\n\n".
					
					'Date(s) of Leave: '.$recordData['c_leave_hrs_begin_date'][0].' to '.$recordData['c_leave_hrs_end_date'][0]."\n".
					'Total Hours: '.$recordData['c_total_request_hrs'][0]."\n".
					
					'----------'."\n\n".
					
					'To approve this leave request, click here: '."\n".
					'http://www.sedl.org/staff/sims/leave_request_ba.php?leave_request_ID='.$leave_request_ID.'&action=view&payperiod='.$recordData['pay_period_end'][0]."\n\n".
					
					
					'------------------------------------------------------------------------------------------------------------------'."\n".
					
					'This is an auto-generated message from the SEDL Information Management System (SIMS)';
					
					$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org, '.$time_leave_admin_email;
					
					mail($to, $subject, $message, $headers);
						
				
				
				
		####################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO PBA ##
		####################################################

	
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

*/










} else { // THERE WAS AN ERROR UPDATING THE LEAVE REQUEST
$_SESSION['travel_request_submitted_staff'] = '2';

}
###############################################
## END: UPDATE THE LEAVE REQUEST ##
###############################################

header('Location: http://www.sedl.org/staff/sims/menu_travel.php');
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
	$newrecord -> AddDBParam('action','Error_3327: travel.php');
	$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
	$newrecord -> AddDBParam('object_ID',$recordData['travel_auth_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$recordData['c_row_ID_cwp'][0]);
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
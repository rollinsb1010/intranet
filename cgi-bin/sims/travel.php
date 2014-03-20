<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');

if(($_SESSION['user_ID'] == '')||($_SESSION['staff_ID'] == '')){
header('Location: http://www.sedl.org/staff/');
}

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
$mod = $_GET['mod'];
$row_ID = $_GET['id'];


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################
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



if($action == 'view'){ //IF THE USER IS VIEWING THIS SINGLE-DESTINATION TRAVEL REQUEST


/*
if($mod == 'edit'){
############################################################
## START: CHANGE TR STATUS TO REVISED ##
############################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$row_ID);

$update -> AddDBParam('approval_status_tr','Revised');

$updateResult = $update -> FMEdit();

//  echo  '<p>errorCode: '.$updateResult['errorCode'];
//  echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
##########################################################
## END: CHANGE TR STATUS TO REVISED ##
##########################################################
}
*/

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
$end_date = date("m/d/Y",mktime(0,0,0,$recordData['c_return_date_requested_m'][0],$recordData['c_return_date_requested_d'][0]+2,$recordData['c_return_date_requested_y'][0]));

$i=0;
while ($temp <> $end_date) {
$temp = date("m/d/Y", mktime(0, 0, 0, $recordData['c_leave_date_requested_m'][0], $recordData['c_leave_date_requested_d'][0]-2+$i, $recordData['c_leave_date_requested_y'][0]));
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

function confirmResubmit() { 
	var answer = confirm ("Re-submit this travel request? This will require supervisor and/or budget authority re-approval.")
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
			
			<?php } elseif($recordData['approval_status_tr'][0] == 'Pending'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small"><strong>Staff Member <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong> - This travel request is pending.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>

			<?php } elseif($recordData['approval_status_tr'][0] == 'Approved'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small"><strong>Staff Member <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong> - This travel request has been approved. <img src="/staff/sims/images/green_check.png"> | 
				
				<?php if($recordData['approval_status_vchr'][0] == 'Approved'){?><a href="travel.php?travel_auth_ID=<?php echo $recordData['travel_auth_ID'][0];?>&action=expenses_review">Show voucher</a>
				<?php }elseif($recordData['approval_status'][0] == 'Approved'){?><a href="travel.php?travel_auth_ID=<?php echo $recordData['travel_auth_ID'][0];?>&action=expenses">Enter travel expenses</a>
				<?php }else{ ?><a href="travel.php?travel_auth_ID=<?php echo $recordData['travel_auth_ID'][0];?>&action=view&mod=edit&id=<?php echo $recordData['c_row_ID_cwp'][0];?>">Edit travel request</a><?php } ?></p>
				
				</td></tr>
				
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>

			<?php } elseif($recordData['approval_status_tr'][0] == 'Rejected'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small"><strong>Staff Member <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong> - This travel request has been rejected by <strong><?php echo $recordData['tr_rejected_by'][0];?></strong>.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>

			<?php } ?>
			
			
			<tr><td colspan="2">
			

<?php 
#########################################################################################
## START: SHOW EDITABLE "TRAVEL REQUEST" FORM IF THE REQUEST HAS NOT YET BEEN APPROVED ##
#########################################################################################

if($recordData['approval_status_tr'][0] != 'Approved'){
?>


						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Travel Request Status: <strong><?php if($recordData['approval_status_tr'][0] !== 'Approved'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo $recordData['approval_status_tr'][0];?></span></strong></span></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL REQUEST (S)</strong></td><td align="right">Request ID: <?php echo $recordData['travel_auth_ID'][0];?> | <a href="" target="_blank">Print</a> | <a href="menu_travel.php">Close</a></td></tr>
						
						<tr><td colspan="2" align="right">
						<form name="travel1" method="GET" onsubmit="return checkFields()">
						<input type="hidden" name="action" value="edit_confirm">
						<input type="hidden" name="self_approves_tr" value="<?php echo $recordData['staff::travel_approves_own_travel_request'][0];?>">
						<input type="hidden" name="mod" value="<?php echo $mod;?>">
						<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
						<input type=button value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Save Changes/Re-submit Form" onClick="confirmResubmit()"><br>
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
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
								<td width="100%"><input type="text" name="purpose_of_travel_descr" size="100" value="<?php echo stripslashes($recordData['purpose_of_travel_descr'][0]);?>"></td>
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
								

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Accommodations</strong>: <em>Where will you be lodging?</em></td></tr>

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


								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
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

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Transportation</strong>: <em>How will you be traveling?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized" value="Yes"<?php if ($recordData['trans_pers_veh_utilized'][0] == 'Yes') { echo ' checked="checked"';}?>> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage" size="5" value="<?php echo $recordData['trans_pers_veh_approx_mileage'][0];?>"><p>
								<input type="checkbox" name="trans_airline_requested" value="Yes"<?php if ($recordData['trans_airline_requested'][0] == 'Yes') { echo ' checked="checked"';}?>> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier" size="10" value="<?php echo stripslashes($recordData['trans_airline_preferred_carrier'][0]);?>"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid" value="yes"<?php if ($recordData['trans_airline_bta_prepaid'][0] == 'yes') { echo ' checked="checked"';}?>> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested" value="Yes"<?php if ($recordData['trans_rental_car_requested'][0] == 'Yes') { echo ' checked="checked"';}?>> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested" size="5" value="<?php echo $recordData['trans_rental_car_num_days_requested'][0];?>"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification" size="20" value="<?php echo stripslashes($recordData['trans_rental_car_justification'][0]);?>"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff" value="Yes"<?php if ($recordData['trans_traveling_with_other_staff'][0] == 'Yes') { echo ' checked="checked"';}?>> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name" size="20" value="<?php echo stripslashes($recordData['trans_traveling_with_name'][0]);?>"><p>
								<input type="checkbox" name="travel_advance_requested" value="Yes"<?php if ($recordData['travel_advance_requested'][0] == 'Yes') { echo ' checked="checked"';}?>> Travel advance requested<p>
								</td>
								</tr>								


								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
								approving your request or to support staff in making your travel arrangements.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><input type="text" name="other_information" size="100%" value="<?php echo stripslashes($recordData['other_information'][0]);?>"></td>
								</tr>								



								<tr><td colspan="2" align="right"><input type=button value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Save Changes/Re-submit Form" onClick="confirmResubmit()"></td></tr>
								


							</table>

<?php 
#########################################################################################
## END: SHOW EDITABLE "TRAVEL REQUEST" FORM IF THE REQUEST HAS NOT YET BEEN APPROVED ##
#########################################################################################


} elseif($recordData['approval_status_tr'][0] == 'Approved'){
#########################################################################################
## START: SHOW READ-ONLY "TRAVEL AUTHORIZATION" FORM IF THE REQUEST HAS BEEN APPROVED ##
#########################################################################################

?>


						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Travel Request Status: <strong><?php if($recordData['approval_status_tr'][0] !== 'Approved'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo $recordData['approval_status_tr'][0];?></span></strong></span></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL REQUEST (S)</strong></td><td align="right">Request ID: <?php echo $recordData['travel_auth_ID'][0];?> <?php if($recordData['approval_status_tr'][0] == 'Approved'){ ?>| <img src="/staff/sims/images/padlock.jpg" border="0" title="This travel request is locked."> <?php }?>| <a href="" target="_blank">Print</a> | <a href="menu_travel.php">Close</a></td></tr>
						
						<tr><td colspan="2" align="right">

							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
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
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Description:</td>
								<td width="100%"><?php echo $recordData['purpose_of_travel_descr'][0];?></td>
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
								
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
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

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Transportation</strong>: <em>How will you be traveling?</em></td></tr>
								
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

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Accommodations</strong>: <em>Where will you be lodging?</em></td></tr>

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

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
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



<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'view_multi'){ //IF THE USER IS VIEWING THIS MULTI-DESTINATION TRAVEL REQUEST


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
			
			<?php } elseif($recordData['approval_status_tr'][0] == 'Pending'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small"><strong>Staff Member: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong> - This travel request is pending.</p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>

			<?php } elseif($recordData['approval_status_tr'][0] == 'Approved'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small"><strong>Staff Member: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong> - This travel request has been approved. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['leave_request_signed_staff'] = ''; ?>

			<?php } ?>
			
			
			<tr><td colspan="2">
			

<?php 
#########################################################################################
## START: SHOW EDITABLE "TRAVEL REQUEST" FORM IF THE REQUEST HAS NOT YET BEEN APPROVED ##
#########################################################################################
if($recordData['approval_status_tr'][0] != 'Approved'){
?>


						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Travel Request Status: <strong><?php if($recordData['approval_status_tr'][0] !== 'Approved'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo $recordData['approval_status_tr'][0];?></span></strong></span></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL REQUEST (M)</strong></td><td align="right">Request ID: <?php echo $recordData['travel_auth_ID'][0];?> | <a href="" target="_blank">Print</a> | <a href="menu_travel.php">Close</a></td></tr>
						
						<tr><td colspan="2" align="right">
						<form name="travel1" method="GET" onsubmit="return checkFields()">
						<input type="hidden" name="action" value="edit_confirm_multi">
						<input type="hidden" name="self_approves_tr" value="<?php echo $recordData['staff::travel_approves_own_travel_request'][0];?>">
						<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
						<input type="hidden" name="num_dest" value="<?php echo $recordData['num_dest'][0];?>">
						<input type=button value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Save Changes/Re-submit Form"><br>
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
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
								<td width="100%"><input type="text" name="purpose_of_travel_descr" size="100" value="<?php echo stripslashes($recordData['purpose_of_travel_descr'][0]);?>"></td>
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
								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 1</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city1'][0].', '.$recordData['event_venue_state1'][0].' - '.$recordData['event_venue_city1_travel_start'][0].' to '.$recordData['event_venue_city1_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name1" size="100" value="<?php echo stripslashes($recordData['event_name1'][0]);?>"></td>
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
								
								
								<tr bgcolor="#ecf0b1"><td colspan="2" style="background-color:#a4d6a7"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city1'][0];?>?</em></td></tr>

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
								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 2</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city2'][0].', '.$recordData['event_venue_state2'][0].' - '.$recordData['event_venue_city2_travel_start'][0].' to '.$recordData['event_venue_city2_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name2" size="100" value="<?php echo stripslashes($recordData['event_name2'][0]);?>"></td>
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
								
								
								<tr bgcolor="#a4d6a7"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city2'][0];?>?</em></td></tr>

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
								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 3</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city3'][0].', '.$recordData['event_venue_state3'][0].' - '.$recordData['event_venue_city3_travel_start'][0].' to '.$recordData['event_venue_city3_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name3" size="100" value="<?php echo stripslashes($recordData['event_name3'][0]);?>"></td>
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
								
								
								<tr bgcolor="#a4d6a7"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city3'][0];?>?</em></td></tr>

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
								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 4</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city4'][0].', '.$recordData['event_venue_state4'][0].' - '.$recordData['event_venue_city4_travel_start'][0].' to '.$recordData['event_venue_city4_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name4" size="100" value="<?php echo stripslashes($recordData['event_name4'][0]);?>"></td>
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
								
								
								<tr bgcolor="#a4d6a7"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city4'][0];?>?</em></td></tr>

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
								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 5</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city5'][0].', '.$recordData['event_venue_state5'][0].' - '.$recordData['event_venue_city5_travel_start'][0].' to '.$recordData['event_venue_city5_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name5" size="100" value="<?php echo stripslashes($recordData['event_name5'][0]);?>"></td>
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
								
								
								<tr bgcolor="#a4d6a7"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city5'][0];?>?</em></td></tr>

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
								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 6</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city6'][0].', '.$recordData['event_venue_state6'][0].' - '.$recordData['event_venue_city6_travel_start'][0].' to '.$recordData['event_venue_city6_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><input type="text" name="event_name6" size="100" value="<?php echo stripslashes($recordData['event_name6'][0]);?>"></td>
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
								
								
								<tr bgcolor="#a4d6a7"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city6'][0];?>?</em></td></tr>

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


								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
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

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Transportation</strong>: <em>How will you be traveling?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized" value="Yes"<?php if ($recordData['trans_pers_veh_utilized'][0] == 'Yes') { echo ' checked="checked"';}?>> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage" size="5" value="<?php echo $recordData['trans_pers_veh_approx_mileage'][0];?>"><p>
								<input type="checkbox" name="trans_airline_requested" value="Yes"<?php if ($recordData['trans_airline_requested'][0] == 'Yes') { echo ' checked="checked"';}?>> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier" size="10" value="<?php echo stripslashes($recordData['trans_airline_preferred_carrier'][0]);?>"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid" value="Yes"<?php if ($recordData['trans_airline_bta_prepaid'][0] == 'Yes') { echo ' checked="checked"';}?>> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested" value="Yes"<?php if ($recordData['trans_rental_car_requested'][0] == 'Yes') { echo ' checked="checked"';}?>> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested" size="5" value="<?php echo $recordData['trans_rental_car_num_days_requested'][0];?>"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification" size="20" value="<?php echo stripslashes($recordData['trans_rental_car_justification'][0]);?>"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff" value="Yes"<?php if ($recordData['trans_traveling_with_other_staff'][0] == 'Yes') { echo ' checked="checked"';}?>> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name" size="20" value="<?php echo stripslashes($recordData['trans_traveling_with_name'][0]);?>"><p>
								<input type="checkbox" name="travel_advance_requested" value="Yes"<?php if ($recordData['travel_advance_requested'][0] == 'Yes') { echo ' checked="checked"';}?>> Travel advance requested<p>
								</td>
								</tr>								


								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
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


} elseif($recordData['approval_status_tr'][0] == 'Approved'){
#########################################################################################
## START: SHOW READ-ONLY "TRAVEL AUTHORIZATION" FORM IF THE REQUEST HAS BEEN APPROVED ##
#########################################################################################

?>


						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Travel Request Status: <strong><?php if($recordData['approval_status_tr'][0] !== 'Approved'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo $recordData['approval_status_tr'][0];?></span></strong></span></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL REQUEST (M)</strong></td><td align="right">Request ID: <?php echo $recordData['travel_auth_ID'][0];?> <?php if($recordData['approval_status_tr'][0] == 'Approved'){ ?>| <img src="/staff/sims/images/padlock.jpg" border="0" title="This travel request is locked."> <?php }?>| <a href="" target="_blank">Print</a> | <a href="menu_travel.php">Close</a></td></tr>
						
						<tr><td colspan="2" align="right">

							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
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
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Description:</td>
								<td width="100%"><?php echo $recordData['purpose_of_travel_descr'][0];?></td>
								</tr>	
								
								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Requested Leave Date/Time:</td>
								<td width="100%"><?php echo $recordData['leave_date_requested'][0];?> | <?php echo $recordData['leave_time_requested'][0];?></td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Requested Return Date/Time:</td>
								<td width="100%"><?php echo $recordData['return_date_requested'][0];?> | <?php echo $recordData['return_time_requested'][0];?></td>
								</tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 1</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city1'][0].', '.$recordData['event_venue_state1'][0].' - '.$recordData['event_venue_city1_travel_start'][0].' to '.$recordData['event_venue_city1_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_name1'][0]);?></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue1'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue_addr1'][0]);?></td>
								</tr>								


								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date/Time:</td>
								<td><?php echo $recordData['event_start_date1'][0];?> | <?php echo $recordData['event_start_time1'][0];?></td>
								</tr>
								
								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date/Time:</td>
								<td><?php echo $recordData['event_end_date1'][0];?> | <?php echo $recordData['event_end_time1'][0];?></td>
								</tr>

								<tr bgcolor="#ecf0b1"><td colspan="2" style="background-color:#a4d6a7"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city1'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_name1'][0]);?><p>
								Is this the conference or meeting hotel? <?php echo $recordData['preferred_hotel_is_conf_hotel1'][0];?><p>
								Other justification for using this hotel: <br>
								<?php echo stripslashes($recordData['preferred_hotel_other_justification1'][0]);?>
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_addr1'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_city1'][0]);?>, <?php echo $recordData['preferred_hotel_state1'][0];?> <?php echo $recordData['preferred_hotel_zip1'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_phone1'][0];?> | <?php echo $recordData['preferred_hotel_fax1'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><?php echo $recordData['hotel_nights_requested1'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%">$<?php echo $recordData['hotel_rate1'][0];?> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><?php echo $recordData['destination_comments1'][0];?></td>
								</tr>								


								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 2</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city2'][0].', '.$recordData['event_venue_state2'][0].' - '.$recordData['event_venue_city2_travel_start'][0].' to '.$recordData['event_venue_city2_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_name2'][0]);?></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue2'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue_addr2'][0]);?></td>
								</tr>								


								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date/Time:</td>
								<td><?php echo $recordData['event_start_date2'][0];?> | <?php echo $recordData['event_start_time2'][0];?></td>
								</tr>
								
								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date/Time:</td>
								<td><?php echo $recordData['event_end_date2'][0];?> | <?php echo $recordData['event_end_time2'][0];?></td>
								</tr>

								<tr bgcolor="#ecf0b1"><td colspan="2" style="background-color:#a4d6a7"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city2'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_name2'][0]);?><p>
								Is this the conference or meeting hotel? <?php echo $recordData['preferred_hotel_is_conf_hotel2'][0];?><p>
								Other justification for using this hotel: <br>
								<?php echo stripslashes($recordData['preferred_hotel_other_justification2'][0]);?>
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_addr2'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_city2'][0]);?>, <?php echo $recordData['preferred_hotel_state2'][0];?> <?php echo $recordData['preferred_hotel_zip2'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_phone2'][0];?> | <?php echo $recordData['preferred_hotel_fax2'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><?php echo $recordData['hotel_nights_requested2'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%">$<?php echo $recordData['hotel_rate2'][0];?> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><?php echo $recordData['destination_comments2'][0];?></td>
								</tr>								


<?php if($recordData['num_dest'][0] > 2){?>

								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 3</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city3'][0].', '.$recordData['event_venue_state3'][0].' - '.$recordData['event_venue_city3_travel_start'][0].' to '.$recordData['event_venue_city3_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_name3'][0]);?></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue3'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue_addr3'][0]);?></td>
								</tr>								


								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date/Time:</td>
								<td><?php echo $recordData['event_start_date3'][0];?> | <?php echo $recordData['event_start_time3'][0];?></td>
								</tr>
								
								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date/Time:</td>
								<td><?php echo $recordData['event_end_date3'][0];?> | <?php echo $recordData['event_end_time3'][0];?></td>
								</tr>

								<tr bgcolor="#ecf0b1"><td colspan="2" style="background-color:#a4d6a7"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city3'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_name3'][0]);?><p>
								Is this the conference or meeting hotel? <?php echo $recordData['preferred_hotel_is_conf_hotel3'][0];?><p>
								Other justification for using this hotel: <br>
								<?php echo stripslashes($recordData['preferred_hotel_other_justification3'][0]);?>
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_addr3'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_city3'][0]);?>, <?php echo $recordData['preferred_hotel_state3'][0];?> <?php echo $recordData['preferred_hotel_zip3'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_phone3'][0];?> | <?php echo $recordData['preferred_hotel_fax3'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><?php echo $recordData['hotel_nights_requested3'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%">$<?php echo $recordData['hotel_rate3'][0];?> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><?php echo $recordData['destination_comments3'][0];?></td>
								</tr>								


<?php } ?>



<?php if($recordData['num_dest'][0] > 3){?>

								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 4</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city4'][0].', '.$recordData['event_venue_state4'][0].' - '.$recordData['event_venue_city4_travel_start'][0].' to '.$recordData['event_venue_city4_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_name4'][0]);?></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue4'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue_addr4'][0]);?></td>
								</tr>								


								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date/Time:</td>
								<td><?php echo $recordData['event_start_date4'][0];?> | <?php echo $recordData['event_start_time4'][0];?></td>
								</tr>
								
								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date/Time:</td>
								<td><?php echo $recordData['event_end_date4'][0];?> | <?php echo $recordData['event_end_time4'][0];?></td>
								</tr>

								<tr bgcolor="#ecf0b1"><td colspan="2" style="background-color:#a4d6a7"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city4'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_name4'][0]);?><p>
								Is this the conference or meeting hotel? <?php echo $recordData['preferred_hotel_is_conf_hotel4'][0];?><p>
								Other justification for using this hotel: <br>
								<?php echo stripslashes($recordData['preferred_hotel_other_justification4'][0]);?>
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_addr4'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_city4'][0]);?>, <?php echo $recordData['preferred_hotel_state4'][0];?> <?php echo $recordData['preferred_hotel_zip4'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_phone4'][0];?> | <?php echo $recordData['preferred_hotel_fax4'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><?php echo $recordData['hotel_nights_requested4'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%">$<?php echo $recordData['hotel_rate4'][0];?> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><?php echo $recordData['destination_comments4'][0];?></td>
								</tr>								


<?php } ?>



<?php if($recordData['num_dest'][0] > 4){?>

								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 5</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city5'][0].', '.$recordData['event_venue_state5'][0].' - '.$recordData['event_venue_city5_travel_start'][0].' to '.$recordData['event_venue_city5_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_name5'][0]);?></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue5'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue_addr5'][0]);?></td>
								</tr>								


								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date/Time:</td>
								<td><?php echo $recordData['event_start_date5'][0];?> | <?php echo $recordData['event_start_time5'][0];?></td>
								</tr>
								
								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date/Time:</td>
								<td><?php echo $recordData['event_end_date5'][0];?> | <?php echo $recordData['event_end_time5'][0];?></td>
								</tr>

								<tr bgcolor="#ecf0b1"><td colspan="2" style="background-color:#a4d6a7"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city5'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_name5'][0]);?><p>
								Is this the conference or meeting hotel? <?php echo $recordData['preferred_hotel_is_conf_hotel5'][0];?><p>
								Other justification for using this hotel: <br>
								<?php echo stripslashes($recordData['preferred_hotel_other_justification5'][0]);?>
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_addr5'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_city5'][0]);?>, <?php echo $recordData['preferred_hotel_state5'][0];?> <?php echo $recordData['preferred_hotel_zip5'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_phone5'][0];?> | <?php echo $recordData['preferred_hotel_fax5'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><?php echo $recordData['hotel_nights_requested5'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%">$<?php echo $recordData['hotel_rate5'][0];?> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><?php echo $recordData['destination_comments5'][0];?></td>
								</tr>								


<?php } ?>


<?php if($recordData['num_dest'][0] > 5){?>

								<td class="body" nowrap align="right" bgcolor="#cccccc"><strong>Destination 6</strong>:</td>
								<td width="100%" bgcolor="#cccccc"><strong><?php echo $recordData['event_venue_city6'][0].', '.$recordData['event_venue_state6'][0].' - '.$recordData['event_venue_city6_travel_start'][0].' to '.$recordData['event_venue_city6_travel_end'][0];?></strong></td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Name of Event:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_name6'][0]);?></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Venue:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue6'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Event Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_venue_addr6'][0]);?></td>
								</tr>								


								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event Start Date/Time:</td>
								<td><?php echo $recordData['event_start_date6'][0];?> | <?php echo $recordData['event_start_time6'][0];?></td>
								</tr>
								
								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ecf0b1">Event End Date/Time:</td>
								<td><?php echo $recordData['event_end_date6'][0];?> | <?php echo $recordData['event_end_time6'][0];?></td>
								</tr>

								<tr bgcolor="#ecf0b1"><td colspan="2" style="background-color:#a4d6a7"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $recordData['event_venue_city6'][0];?>?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Preferred Hotel:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_name6'][0]);?><p>
								Is this the conference or meeting hotel? <?php echo $recordData['preferred_hotel_is_conf_hotel6'][0];?><p>
								Other justification for using this hotel: <br>
								<?php echo stripslashes($recordData['preferred_hotel_other_justification6'][0]);?>
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Address:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_addr6'][0]);?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">City/State/Zip:</td>
								<td width="100%"><?php echo stripslashes($recordData['preferred_hotel_city6'][0]);?>, <?php echo $recordData['preferred_hotel_state6'][0];?> <?php echo $recordData['preferred_hotel_zip6'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Phone/Fax:</td>
								<td width="100%"><?php echo $recordData['preferred_hotel_phone6'][0];?> | <?php echo $recordData['preferred_hotel_fax6'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Number of Nights:</td>
								<td width="100%"><?php echo $recordData['hotel_nights_requested6'][0];?></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Hotel Rate:</td>
								<td width="100%">$<?php echo $recordData['hotel_rate6'][0];?> <em>(per night)</em></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1">Comments:</td>
								<td width="100%"><?php echo $recordData['destination_comments6'][0];?></td>
								</tr>								


<?php } ?>
								
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
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

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Transportation</strong>: <em>How will you be traveling?</em></td></tr>
								
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

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
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



<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'new_dot_old') { // GET THE STAFF MEMBER'S DATES AND TIMES OF TRAVEL
$this_year = date("Y");
$last_year = $this_year - 1;
$next_year = $this_year + 1;
$next_year2 = $this_year + 2;
?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function checkFields() { 

	// Requested leave date month
		if (document.travel_1.leave_date_requested_m.value ==""){
			alert("Please select the requested leave date (month).");
			document.travel_1.leave_date_requested_m.focus();
			return false;	}

	// Requested leave date day
		if (document.travel_1.leave_date_requested_d.value ==""){
			alert("Please select the requested leave date (day).");
			document.travel_1.leave_date_requested_d.focus();
			return false;	}

	// Requested leave date year
		if (document.travel_1.leave_date_requested_y.value ==""){
			alert("Please select the requested leave date (year).");
			document.travel_1.leave_date_requested_y.focus();
			return false;	}

	// Requested return date month
		if (document.travel_1.return_date_requested_m.value ==""){
			alert("Please select the requested return date (month).");
			document.travel_1.return_date_requested_m.focus();
			return false;	}

	// Requested return date day
		if (document.travel_1.return_date_requested_d.value ==""){
			alert("Please select the requested return date (day).");
			document.travel_1.return_date_requested_d.focus();
			return false;	}

	// Requested return date year
		if (document.travel_1.return_date_requested_y.value ==""){
			alert("Please select the requested return date (year).");
			document.travel_1.return_date_requested_y.focus();
			return false;	}

	// Requested leave time
		if (document.travel_1.leave_time_requested.value ==""){
			alert("Please enter the requested leave time.");
			document.travel_1.leave_time_requested.focus();
			return false;	}

	// Requested return time
		if (document.travel_1.return_time_requested.value ==""){
			alert("Please select the requested return time.");
			document.travel_1.return_time_requested.focus();
			return false;	}

	// Compare dates for proper sequence
	
		var x = new Date(document.travel_1.leave_date_requested_y.value + "-" + document.travel_1.leave_date_requested_m.value + "-" + document.travel_1.leave_date_requested_d.value);
		var y = new Date(document.travel_1.return_date_requested_y.value + "-" + document.travel_1.return_date_requested_m.value + "-" + document.travel_1.return_date_requested_d.value);

		if (x > y){
			alert("Leave date cannot be after return date.");
			document.travel_1.leave_date_requested_m.focus();
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
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>1. Dates of Travel</strong>: <em>Select the begin and end dates for this trip.</em></td></tr>
								
								<form name="travel_1" method="GET" onsubmit="return checkFields()">
								<input type="hidden" name="action" value="new_dest">
								<input type="hidden" name="num_dest" value="1">
								
								
								
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
						
									<option value="<?php echo $this_year;?>" selected> <?php echo $this_year;?></option>
									<option value="<?php echo $next_year;?>"> <?php echo $next_year;?></option>
									<option value="<?php echo $next_year2;?>"> <?php echo $next_year2;?></option>

								</select> 
								&nbsp;&nbsp;&nbsp;Requested Leave Time: <input type="text" name="leave_time_requested" size="10">

								

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
						
									<option value="<?php echo $this_year;?>" selected> <?php echo $this_year;?></option>
									<option value="<?php echo $next_year;?>"> <?php echo $next_year;?></option>
									<option value="<?php echo $next_year2;?>"> <?php echo $next_year2;?></option>

								</select> 
								&nbsp;&nbsp;&nbsp;Requested Return Time: <input type="text" name="return_time_requested" size="10">
								
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

} elseif($action == 'new_dot') { // GET THE STAFF MEMBER'S DATES AND TIMES OF TRAVEL
$this_year = date("Y");
$last_year = $this_year - 1;
$next_year = $this_year + 1;
$next_year2 = $this_year + 2;
?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/redmond/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
$(function() {
$( "#datepicker" ).datepicker();
});
</script>

<script>
$(function() {
$( "#datepicker2" ).datepicker();
});
</script>

<script>
$(function() {
$( document ).tooltip();
});
</script>
<style>
label {
display: inline-block;
width: 5em;
}
  </style>

<script language="JavaScript">
function checkFields() { 

	// Requested leave date month
		if (document.travel_1.leave_date_requested.value ==""){
			alert("Please select the requested leave date.");
			document.travel_1.leave_date_requested.focus();
			return false;	}

	// Requested return date month
		if (document.travel_1.return_date_requested.value ==""){
			alert("Please select the requested return date.");
			document.travel_1.return_date_requested.focus();
			return false;	}

	// Requested leave time
		if (document.travel_1.leave_time_requested.value ==""){
			alert("Please enter the requested leave time.");
			document.travel_1.leave_time_requested.focus();
			return false;	}

	// Requested return time
		if (document.travel_1.return_time_requested.value ==""){
			alert("Please select the requested return time.");
			document.travel_1.return_time_requested.focus();
			return false;	}

	// Compare dates for proper sequence
	
		//var x = new Date(document.travel_1.leave_date_requested_y.value + "-" + document.travel_1.leave_date_requested_m.value + "-" + document.travel_1.leave_date_requested_d.value);
		//var y = new Date(document.travel_1.return_date_requested_y.value + "-" + document.travel_1.return_date_requested_m.value + "-" + document.travel_1.return_date_requested_d.value);
		
		var x = new Date(document.travel_1.leave_date_requested.value);
		var y = new Date(document.travel_1.return_date_requested.value);

		if (x > y){
			alert("Leave date cannot be after return date.");
			document.travel_1.leave_date_requested.focus();
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
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>1. Dates of Travel</strong>: <em>Select the begin and end dates for this trip.</em></td></tr>
								
								<form name="travel_1" method="GET" onsubmit="return checkFields()">
								<input type="hidden" name="action" value="new_dest">
								<input type="hidden" name="num_dest" value="1">
								
								
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Requested Leave Date:</td>
								<td width="100%" nowrap>

								<input type="text" id="datepicker" name="leave_date_requested">
								&nbsp;&nbsp;&nbsp;<label for="time1">Time:</label> <input type="text" name="leave_time_requested" size="10" id="time1" title="Use: hh:mm am/pm (ex: 10:00 am)">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Requested Return Date:
								
								
								
								</td><td nowrap>
								<input type="text" id="datepicker2" name="return_date_requested">
								&nbsp;&nbsp;&nbsp;<label for="time2">Time:</label> <input type="text" name="return_time_requested" size="10" id="time2" title="Use: hh:mm am/pm (ex: 06:00 pm)">
								
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



<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'new_dest_old') { // GET IF THE STAFF MEMBER IS TRAVELLING TO MULTIPLE DESTINATIONS

//echo '<p>'.$_GET['leave_date_requested'];
//echo '<p>'.$_GET['return_date_requested'];

//$_SESSION['leave_date_requested'] = $_GET['leave_date_requested'];
//$_SESSION['return_date_requested'] = $_GET['return_date_requested'];

$_SESSION['leave_date_requested'] = $_GET['leave_date_requested_m'].'/'.$_GET['leave_date_requested_d'].'/'.$_GET['leave_date_requested_y'];
$_SESSION['return_date_requested'] = $_GET['return_date_requested_m'].'/'.$_GET['return_date_requested_d'].'/'.$_GET['return_date_requested_y'];
$_SESSION['leave_time_requested'] = $_GET['leave_time_requested'];
$_SESSION['return_time_requested'] = $_GET['return_time_requested'];

##################################################################
## START: CREATE ARRAY OF TRAVEL DATES FOR CHECKING CONUS RATES
##################################################################
//$start_date = date("n/j/Y",mktime(0,0,0,date("m",$_GET['leave_date_requested']),date("d",$_GET['leave_date_requested']),date("Y",$_GET['leave_date_requested'])));
//$end_date = date("n/j/Y",mktime(0,0,0,date("m",$_GET['return_date_requested']),date("d",$_GET['return_date_requested']),date("Y",$_GET['return_date_requested'])));

$start_date = date("n/j/Y",mktime(0,0,0,$_GET['leave_date_requested_m'],$_GET['leave_date_requested_d'],$_GET['leave_date_requested_y']));
$end_date = date("n/j/Y",mktime(0,0,0,$_GET['return_date_requested_m'],$_GET['return_date_requested_d'],$_GET['return_date_requested_y']));

$i=0;
while ($temp <> $end_date) {
//$temp = date("n/j/Y", mktime(0, 0, 0, substr($_GET['leave_date_requested'],0,-8), substr($_GET['leave_date_requested'],3,-6)+$i, substr($_GET['leave_date_requested'],6,-3)));
$temp = date("n/j/Y", mktime(0, 0, 0, $_GET['leave_date_requested_m'], $_GET['leave_date_requested_d']+$i, $_GET['leave_date_requested_y']));
$travel_days[$i] = $temp;
$i++;
};
//echo '<p>';
//print_r($travel_days);
$_SESSION['travel_days'] = $travel_days;
//exit;
##################################################################
## END: CREATE ARRAY OF TRAVEL DATES FOR CHECKING CONUS RATES
##################################################################

?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function alertMulti() { 
	var answer = alert ("Multi-destination travel requests are not active yet. Please use the old travel request form or submit a single destination travel request.")
	
	return false;
	
}

function checkFields() { 

	// State selector
	/*
		if (document.travel_1.num_dest.value ==""){
			alert("Please enter the number of destinations (cities) for this travel request.");
			document.travel_1.num_dest.focus();
			return false;	}

		if (document.travel_1.num_dest.value =="0"){
			alert("Please only use this form for 2 or more destinations.");
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

		if ((document.travel_1.num_dest.value !=="2")&&(document.travel_1.num_dest.value !=="3")&&(document.travel_1.num_dest.value !=="4")&&(document.travel_1.num_dest.value !=="5")&&(document.travel_1.num_dest.value !=="6")){
			alert("This form only supports travel for 2 to 6 destinations (numeric values only).");
			document.travel_1.num_dest.focus();
			return false;	}
	*/
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
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>2. Number of Destinations</strong>: <em>Will you be traveling to more than 1 destination?</em></td></tr>
								
								<tr>
								<td width="50%">
								<form name="travel_1" method="GET" onsubmit="return checkFields()">
								<?php if($_SESSION['user_ID'] == 'ewaters'){?>
								<input type="hidden" name="action" value="new_st_multi">Yes, I will be traveling to <input type="text" name="num_dest" size="3"> destinations. <input type="submit" name="submit" value="Continue" onclick="alertMulti()"></form>
								<?php }?>
								Multi-destination travel is not yet active. Please use the old travel request form or submit a single destination travel request.</td>
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

} elseif($action == 'new_dest') { // GET IF THE STAFF MEMBER IS TRAVELLING TO MULTIPLE DESTINATIONS

//echo '<p>'.$_GET['leave_date_requested'];
//echo '<p>'.$_GET['return_date_requested'];

//echo '<p>'.date("m",$_GET['leave_date_requested']);

$_SESSION['leave_date_requested'] = $_GET['leave_date_requested'];
$_SESSION['return_date_requested'] = $_GET['return_date_requested'];

//$_SESSION['leave_date_requested'] = $_GET['leave_date_requested_m'].'/'.$_GET['leave_date_requested_d'].'/'.$_GET['leave_date_requested_y'];
//$_SESSION['return_date_requested'] = $_GET['return_date_requested_m'].'/'.$_GET['return_date_requested_d'].'/'.$_GET['return_date_requested_y'];
$_SESSION['leave_time_requested'] = $_GET['leave_time_requested'];
$_SESSION['return_time_requested'] = $_GET['return_time_requested'];

##################################################################
## START: CREATE ARRAY OF TRAVEL DATES FOR CHECKING CONUS RATES
##################################################################
$start_date = date("n/j/Y",mktime(0,0,0,substr($_GET['leave_date_requested'],0,-8),substr($_GET['leave_date_requested'],3,-5),substr($_GET['leave_date_requested'],6)));
$end_date = date("n/j/Y",mktime(0,0,0,substr($_GET['return_date_requested'],0,-8),substr($_GET['return_date_requested'],3,-5),substr($_GET['return_date_requested'],6)));

//echo '<p>'.$start_date;
//echo '<p>'.$end_date;

//echo '<p>'.substr($_GET['leave_date_requested'],0,-8);
//echo '<p>'.substr($_GET['leave_date_requested'],3,-5);
//echo '<p>'.substr($_GET['leave_date_requested'],6);



//$start_date = date("n/j/Y",mktime(0,0,0,$_GET['leave_date_requested_m'],$_GET['leave_date_requested_d'],$_GET['leave_date_requested_y']));
//$end_date = date("n/j/Y",mktime(0,0,0,$_GET['return_date_requested_m'],$_GET['return_date_requested_d'],$_GET['return_date_requested_y']));

$i=0;
while ($temp <> $end_date) {
$temp = date("n/j/Y", mktime(0, 0, 0, substr($_GET['leave_date_requested'],0,-8), substr($_GET['leave_date_requested'],3,-5)+$i, substr($_GET['leave_date_requested'],6)));
//$temp = date("n/j/Y", mktime(0, 0, 0, $_GET['leave_date_requested_m'], $_GET['leave_date_requested_d']+$i, $_GET['leave_date_requested_y']));
$travel_days[$i] = $temp;
$i++;
};
//echo '<p>';
//print_r($travel_days);
$_SESSION['travel_days'] = $travel_days;
//exit;
##################################################################
## END: CREATE ARRAY OF TRAVEL DATES FOR CHECKING CONUS RATES
##################################################################

?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function alertMulti() { 
	var answer = alert ("Multi-destination travel requests are not active yet. Please use the old travel request form or submit a single destination travel request.")
	
	return false;
	
}

function checkFields() { 

	// State selector
	/*
		if (document.travel_1.num_dest.value ==""){
			alert("Please enter the number of destinations (cities) for this travel request.");
			document.travel_1.num_dest.focus();
			return false;	}

		if (document.travel_1.num_dest.value =="0"){
			alert("Please only use this form for 2 or more destinations.");
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

		if ((document.travel_1.num_dest.value !=="2")&&(document.travel_1.num_dest.value !=="3")&&(document.travel_1.num_dest.value !=="4")&&(document.travel_1.num_dest.value !=="5")&&(document.travel_1.num_dest.value !=="6")){
			alert("This form only supports travel for 2 to 6 destinations (numeric values only).");
			document.travel_1.num_dest.focus();
			return false;	}
	*/
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
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>2. Number of Destinations</strong>: <em>Will you be traveling to more than 1 destination?</em></td></tr>
								
								<tr>
								<td width="50%">
								<form name="travel_1" method="GET" onsubmit="return checkFields()">
								<?php //if($_SESSION['user_ID'] == 'ewaters'){?>
								<input type="hidden" name="action" value="new_st_multi">Yes, I will be traveling to <input type="text" name="num_dest" size="3"> destinations. <input type="submit" name="submit" value="Continue"></form>
								<?php //}?>
								<!--Multi-destination travel is not yet active. Please use the old travel request form or submit a single destination travel request.--></td>
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






<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'new_st') { // GET THE STAFF MEMBER'S DESTINATION STATE FOR SINGLE-DESTINATION TRAVEL



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
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>3A. Enter Origin</strong>: <em>Where are you departing from?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">City:</td>
								<td width="100%">

								<input type="text" name="origin_city" size="25" value="Austin">
								
								<select name="origin_state" class="body">
								<option value="">Select State</option>
								<option value="">-------------</option>
								
								<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
								<option value="<?php echo $searchData4['abbrev'][0];?>" <?php if($searchData4['abbrev'][0] == 'TX'){echo 'SELECTED';}?>> <?php echo $searchData4['abbrev'][0];?>
								<?php } ?>
								</select>


								</td></tr>
								
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>3B. Enter Destination</strong>: <em>To what destination will you be traveling?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">City:</td>
								<td width="100%">

								<input type="text" name="event_venue_city" size="25">
								
								<select name="event_venue_state" class="body">
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



<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'new_st_multi') { // GET THE STAFF MEMBER'S ORIGIN AND DESTINATION STATES FOR MULTI-DESTINATION TRAVEL



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

	// Origin City
		if (document.travel_1.origin_city.value ==""){
			alert("Please enter the origin city.");
			document.travel_1.origin_city.focus();
			return false;	}
}			

	// Origin State
		if (document.travel_1.origin_state.value ==""){
			alert("Please select the origin state.");
			document.travel_1.origin_state.focus();
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
			<input type="hidden" name="action" value="new_st_multi_3">
			<input type="hidden" name="num_dest" value="<?php echo $num_dest;?>">
			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW TRAVEL REQUEST</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
							
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>3A. Enter Origin</strong>: <em>Where are you departing from?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">City:</td>
								<td width="100%">

								<input type="text" name="origin_city" size="25" value="Austin">
								
								<select name="origin_state" class="body">
								<option value="">Select State</option>
								<option value="">-------------</option>
								
								<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
								<option value="<?php echo $searchData4['abbrev'][0];?>" <?php if($searchData4['abbrev'][0] == 'TX'){echo 'SELECTED';}?>> <?php echo $searchData4['abbrev'][0];?>
								<?php } ?>
								</select>


								</td></tr>

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>3B. Enter Destinations</strong>: <em>To what destinations will you be traveling? Enter the city/state for each destination.</em></td></tr>
								
					<?php for($i=1;$i<=$_SESSION['num_dest'];$i++) { ?>
					
					

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">City (Destination <?php echo $i;?>):</td>
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



<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'new_st_multi_2') { // GET THE STAFF MEMBER'S DESTINATION CITIES FOR MULTI-DESTINATION TRAVEL

//$num_dest = $_GET['num_dest'];


$_SESSION['origin_city'] = $_GET['origin_city'];
$_SESSION['origin_state'] = $_GET['origin_state'];
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
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>4. Select Cities</strong>: <em>To what destinations (cities) will you be traveling?</em></td></tr>
								
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


<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'new_st_multi_3') { // GET THE STAFF MEMBER'S DATES OF TRAVEL FOR MULTI-DESTINATION TRAVEL

//$num_dest = $_GET['num_dest'];


$_SESSION['origin_city'] = $_GET['origin_city'];
$_SESSION['origin_state'] = $_GET['origin_state'];

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
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>4. Enter Dates of Travel</strong>: <em>Indicate the travel dates for each destination.</em></td></tr>
								
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



<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'new') { //IF THIS IS A NEW TRAVEL REQUEST

$_SESSION['origin_city'] = $_GET['origin_city'];
$_SESSION['origin_state'] = $_GET['origin_state'];
$_SESSION['city'] = $_GET['event_venue_city'];
$_SESSION['state'] = $_GET['event_venue_state'];
//echo 'staff_ID: '.$_SESSION['staff_ID'];

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
## START: GRAB STAFF MEMBER'S TRAVEL ADMIN INFO ##
############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('staff_ID',$_SESSION['staff_ID']);

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);
##########################################################
## END: GRAB STAFF MEMBER'S TRAVEL ADMIN INFO ##
##########################################################

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
//echo '<p>leavedate: '.$_SESSION['leave_date_requested']; 
//echo '<p>returndate: '.$_SESSION['return_date_requested'];


?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<style type="text/css">
.lodging_table td{
padding:8px;
}
</style>

<script type="text/javascript" language="javascript">
 function toggleVisibility(cb)
 {
  var x = document.getElementById("lodging");
  if(cb.checked==true)
   x.style.display = "none"; // or x.style.display = "none";
  else
   x.style.display = "block"; //or x.style.display = "block";
 }


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
			<input type="hidden" name="self_approves_tr" value="<?php echo $recordData['travel_approves_own_travel_request'][0];?>">
			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW TRAVEL REQUEST</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
						<div style="border:1px dotted #cccccc;padding:6px">Travel Admin for this trip: <input type="radio" name="travel_admin_sims_user_ID" value="<?php echo $recordData['travel_admin_sims_user_ID'][0];?>" checked> <?php echo $recordData['travel_admin_sims_user_ID'][0];?>
						<?php if($recordData['c_travel_admin_alt1'][0] !== ''){?>&nbsp;&nbsp;&nbsp;<input type="radio" name="travel_admin_sims_user_ID" value="<?php echo $recordData['c_travel_admin_alt1'][0];?>"> <?php echo $recordData['c_travel_admin_alt1'][0];?><?php } ?>
						<?php if($recordData['c_travel_admin_alt2'][0] !== ''){?>&nbsp;&nbsp;&nbsp;<input type="radio" name="travel_admin_sims_user_ID" value="<?php echo $recordData['c_travel_admin_alt2'][0];?>"> <?php echo $recordData['c_travel_admin_alt2'][0];?><?php } ?>
						<?php if($recordData['c_travel_admin_alt3'][0] !== ''){?>&nbsp;&nbsp;&nbsp;<input type="radio" name="travel_admin_sims_user_ID" value="<?php echo $recordData['c_travel_admin_alt3'][0];?>"> <?php echo $recordData['c_travel_admin_alt3'][0];?><?php } ?>
						</div>

						<?php if($recordData['travel_pba_alts'][0] !== ''){?>
						<div style="border:1px dotted #cccccc;padding:6px">Primary Budget Authority for this trip: <input type="radio" name="travel_pba_sims_user_ID" value="<?php echo $recordData['bgt_auth_primary_sims_user_ID'][0];?>" checked> <?php echo $recordData['bgt_auth_primary_sims_user_ID'][0];?>
						<?php if($recordData['c_travel_pba_alt1'][0] !== ''){?>&nbsp;&nbsp;&nbsp;<input type="radio" name="travel_pba_sims_user_ID" value="<?php echo $recordData['c_travel_pba_alt1'][0];?>"> <?php echo $recordData['c_travel_pba_alt1'][0];?><?php } ?>
						<?php if($recordData['c_travel_pba_alt2'][0] !== ''){?>&nbsp;&nbsp;&nbsp;<input type="radio" name="travel_pba_sims_user_ID" value="<?php echo $recordData['c_travel_pba_alt2'][0];?>"> <?php echo $recordData['c_travel_pba_alt2'][0];?><?php } ?>
						<?php if($recordData['c_travel_pba_alt3'][0] !== ''){?>&nbsp;&nbsp;&nbsp;<input type="radio" name="travel_pba_sims_user_ID" value="<?php echo $recordData['c_travel_pba_alt3'][0];?>"> <?php echo $recordData['c_travel_pba_alt3'][0];?><?php } ?>
						</div>
						<?php }?>
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>4. Purpose and Venue</strong>: <em>What is the purpose, description, and venue of this travel?</em></td></tr>
								
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
								<td width="100%"><input type="text" name="purpose_of_travel_descr" size="100"><br><span class="tiny">Name of event, type of work to be performed...etc.</span></td>
								</tr>								

								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Origin:</td>

								<td width="100%"><strong><?php echo $_SESSION['origin_city'].', '.$_SESSION['origin_state'];?></strong></td>

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
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Airport Dest.:</td>
								<td width="100%"><input type="text" name="req_flight_destination_city_state" size="45"><br><span class="tiny">Which city will you be flying to (ex: Dallas, TX)?</span></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event Venue:</td>
								<td width="100%"><input type="text" name="event_venue" size="45"><br><span class="tiny">Location of the event or work to be performed.</span></td>
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
								&nbsp;&nbsp;&nbsp;Requested Leave Time: <strong><?php echo $_SESSION['leave_time_requested'];?></strong>
								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Requested Return Date:</td>
								<td><strong><?php echo $_SESSION['return_date_requested'];?></strong>
								&nbsp;&nbsp;&nbsp;Requested Return Time: <strong><?php echo $_SESSION['return_time_requested'];?></strong>
								</td></tr>
								
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>5. Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">

								<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
								
									<input type="checkbox" name="budget_code[]" value="<?php echo $searchData2['budget_code'][0];?>"><?php echo $searchData2['budget_code'][0];?></input><span class="tiny"> | <?php echo $searchData2['Budget_Code_Nickname'][0];?></span><br>
								
								<?php } ?>

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

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" style="vertical-align:text-top">Registration Fee:</td>
								<td width="100%"><input type="text" name="req_registration_fee" size="5"> <em>Enter amount of registration fee. Leave blank if no fee.</em><br>
								Registration fee paid by? <input type="radio" name="req_registration_fee_paid_by" value="SEDL"> SEDL &nbsp;&nbsp;&nbsp;<input type="radio" name="req_registration_fee_paid_by" value="Staff"> Staff
								</td>
								</tr>								

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>6. Transportation</strong>: <em>How will you be traveling?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized" value="Yes"> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage" size="5"><p>
								<input type="checkbox" name="trans_airline_requested" value="Yes"> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier" size="10"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid" value="Yes"> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested" value="Yes"> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested" size="5"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification" size="20"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff" value="Yes"> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name" size="20"><p>
								<input type="checkbox" name="travel_advance_requested" value="Yes"> Travel advance requested (only available for rental car, hotel, and/or meals)<p>
								</td>
								</tr>								

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>7. Accommodations</strong>: <em>Where will you be lodging?</em><div style="float:right"><input type="checkbox" name="lodging_not_required" value="Yes" onClick="toggleVisibility(this);"> Lodging not required</div></td></tr>

								<tr><td colspan="2">
								<div id="lodging">
									<table class="lodging_table">
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
									</table>
								</div>	
								</td>
								</tr>

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>8. Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
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


<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'new_multi') { //IF THIS IS A NEW MULTI-DESTINATION TRAVEL REQUEST

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
## START: GRAB STAFF MEMBER'S TRAVEL ADMIN INFO ##
############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('staff_ID',$_SESSION['staff_ID']);

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);
##########################################################
## END: GRAB STAFF MEMBER'S TRAVEL ADMIN INFO ##
##########################################################

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
//echo '<p>leavedate: '.$_SESSION['leave_date_requested']; 
//echo '<p>returndate: '.$_SESSION['return_date_requested'];
//echo '<p>$_SESSION[numdest]: '.$_SESSION['num_dest'];


?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">


<style type="text/css">
.lodging_table td{
padding:8px;
}
</style>

<script type="text/javascript" language="javascript">
 function toggleVisibility(cb)
 {
  var x = document.getElementById("lodging1");
  if(cb.checked==true)
   x.style.display = "none"; // or x.style.display = "none";
  else
   x.style.display = "block"; //or x.style.display = "block";
 }


 function toggleVisibility2(cb)
 {
  var x = document.getElementById("lodging2");
  if(cb.checked==true)
   x.style.display = "none"; // or x.style.display = "none";
  else
   x.style.display = "block"; //or x.style.display = "block";
 }


 function toggleVisibility3(cb)
 {
  var x = document.getElementById("lodging3");
  if(cb.checked==true)
   x.style.display = "none"; // or x.style.display = "none";
  else
   x.style.display = "block"; //or x.style.display = "block";
 }


 function toggleVisibility4(cb)
 {
  var x = document.getElementById("lodging4");
  if(cb.checked==true)
   x.style.display = "none"; // or x.style.display = "none";
  else
   x.style.display = "block"; //or x.style.display = "block";
 }


 function toggleVisibility5(cb)
 {
  var x = document.getElementById("lodging5");
  if(cb.checked==true)
   x.style.display = "none"; // or x.style.display = "none";
  else
   x.style.display = "block"; //or x.style.display = "block";
 }


 function toggleVisibility6(cb)
 {
  var x = document.getElementById("lodging6");
  if(cb.checked==true)
   x.style.display = "none"; // or x.style.display = "none";
  else
   x.style.display = "block"; //or x.style.display = "block";
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
			<form name="leave_request1" method="GET">
			<input type="hidden" name="action" value="new_submit_multi">
			<input type="hidden" name="self_approves_tr" value="<?php echo $recordData['travel_approves_own_travel_request'][0];?>">

			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>CREATE NEW TRAVEL REQUEST</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2"><div style="border:1px dotted #cccccc;padding:6px">Travel Admin for this trip: <input type="radio" name="travel_admin_sims_user_ID" value="<?php echo $recordData['travel_admin_sims_user_ID'][0];?>" checked> <?php echo $recordData['travel_admin_sims_user_ID'][0];?>
						
						<?php if($recordData['c_travel_admin_alt1'][0] !== ''){?>&nbsp;&nbsp;&nbsp;<input type="radio" name="travel_admin_sims_user_ID" value="<?php echo $recordData['c_travel_admin_alt1'][0];?>"> <?php echo $recordData['c_travel_admin_alt1'][0];?><?php } ?>
						<?php if($recordData['c_travel_admin_alt2'][0] !== ''){?>&nbsp;&nbsp;&nbsp;<input type="radio" name="travel_admin_sims_user_ID" value="<?php echo $recordData['c_travel_admin_alt2'][0];?>"> <?php echo $recordData['c_travel_admin_alt2'][0];?><?php } ?>
						<?php if($recordData['c_travel_admin_alt3'][0] !== ''){?>&nbsp;&nbsp;&nbsp;<input type="radio" name="travel_admin_sims_user_ID" value="<?php echo $recordData['c_travel_admin_alt3'][0];?>"> <?php echo $recordData['c_travel_admin_alt3'][0];?><?php } ?>
						
						
						</div>
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>6. Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
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
								<td width="100%"><input type="text" name="purpose_of_travel_descr" size="100%"></td>
								</tr>								

								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Requested Leave Date:</td>
								<td><strong><?php echo $_SESSION['leave_date_requested'];?></strong>
								&nbsp;&nbsp;&nbsp;Requested Leave Time: <strong><?php echo $_SESSION['leave_time_requested'];?></strong>
								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Requested Return Date:</td>
								<td><strong><?php echo $_SESSION['return_date_requested'];?></strong>
								&nbsp;&nbsp;&nbsp;Requested Return Time: <strong><?php echo $_SESSION['return_time_requested'];?></strong>
								</td></tr>

								<tr><td class="body" nowrap valign="top" align="center" bgcolor="#ebebeb">Origin of Travel:</td>
								<td><strong><?php echo $_SESSION['origin_city'].', '.$_SESSION['origin_state'];?></strong>
								</td></tr>

								<tr>
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>7. Travel Destinations</strong>: <em>Enter event, accommodation, and transportation details for each of your <strong><?php echo $_SESSION['num_dest'];?></strong> destinations.</em></td></tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb"><strong>Destination 1</strong>:</td>
								<td width="100%" bgcolor="#ebebeb"><strong><?php echo $_SESSION['event_venue_city1'].', '.$_SESSION['event_venue_state1'].' - '.$_SESSION['event_venue_city_date_from1'].' to '.$_SESSION['event_venue_city_date_to1'];?></strong></td>
								</tr>								

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Event Information</strong>: <em>Enter details about the event or activity planned for <?php echo $_SESSION['event_venue_city1'];?>.</em></td></tr>

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

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city1'];?>?</em><div style="float:right"><input type="checkbox" name="lodging_not_required1" value="Yes" onClick="toggleVisibility(this);"> Lodging not required</div></td></tr>
								
								<tr><td colspan="2">
								<div id="lodging1">
									<table class="lodging_table">
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
									</table>
								</div>	
								</td>
								</tr>
								
								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Transportation</strong>: <em>Enter details about your transportation to (and while in) <?php echo $_SESSION['event_venue_city1'];?>.</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized1" value="yes"> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage1" size="5"><p>
								<input type="checkbox" name="trans_airline_requested1" value="yes"> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier1" size="10"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid1" value="yes"> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested1" value="yes"> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested1" size="5"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification1" size="20"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff1" value="yes"> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name1" size="20"><p>
								</td>
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

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city2'];?>?</em><div style="float:right"><input type="checkbox" name="lodging_not_required2" value="Yes" onClick="toggleVisibility2(this);"> Lodging not required</div></td></tr>

								<tr><td colspan="2">
								<div id="lodging2">
									<table class="lodging_table">
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
									</table>
								</div>	
								</td>
								</tr>
	
								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Transportation</strong>: <em>Enter details about your transportation to (and while in) <?php echo $_SESSION['event_venue_city2'];?>.</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized2" value="yes"> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage2" size="5"><p>
								<input type="checkbox" name="trans_airline_requested2" value="yes"> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier2" size="10"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid2" value="yes"> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested2" value="yes"> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested2" size="5"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification2" size="20"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff2" value="yes"> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name2" size="20"><p>
								</td>
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

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city3'];?>?</em><div style="float:right"><input type="checkbox" name="lodging_not_required3" value="Yes" onClick="toggleVisibility3(this);"> Lodging not required</div></td></tr>

								<tr><td colspan="2">
								<div id="lodging3">
									<table class="lodging_table">
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
									</table>
								</div>	
								</td>
								</tr>

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Transportation</strong>: <em>Enter details about your transportation to (and while in) <?php echo $_SESSION['event_venue_city3'];?>.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized3" value="yes"> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage3" size="5"><p>
								<input type="checkbox" name="trans_airline_requested3" value="yes"> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier3" size="10"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid3" value="yes"> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested3" value="yes"> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested3" size="5"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification3" size="20"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff3" value="yes"> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name3" size="20"><p>
								</td>
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

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city4'];?>?</em><div style="float:right"><input type="checkbox" name="lodging_not_required4" value="Yes" onClick="toggleVisibility4(this);"> Lodging not required</div></td></tr>

								<tr><td colspan="2">
								<div id="lodging4">
									<table class="lodging_table">
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
									</table>
								</div>	
								</td>
								</tr>

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Transportation</strong>: <em>Enter details about your transportation to (and while in) <?php echo $_SESSION['event_venue_city4'];?>.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized4" value="yes"> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage4" size="5"><p>
								<input type="checkbox" name="trans_airline_requested4" value="yes"> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier4" size="10"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid4" value="yes"> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested4" value="yes"> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested4" size="5"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification4" size="20"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff4" value="yes"> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name4" size="20"><p>
								</td>
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

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city5'];?>?</em><div style="float:right"><input type="checkbox" name="lodging_not_required5" value="Yes" onClick="toggleVisibility5(this);"> Lodging not required</div></td></tr>

								<tr><td colspan="2">
								<div id="lodging5">
									<table class="lodging_table">
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
									</table>
								</div>	
								</td>
								</tr>

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Transportation</strong>: <em>Enter details about your transportation to (and while in) <?php echo $_SESSION['event_venue_city5'];?>.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized5" value="yes"> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage5" size="5"><p>
								<input type="checkbox" name="trans_airline_requested5" value="yes"> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier5" size="10"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid5" value="yes"> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested5" value="yes"> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested5" size="5"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification5" size="20"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff5" value="yes"> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name5" size="20"><p>
								</td>
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

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging in <?php echo $_SESSION['event_venue_city6'];?>?</em><div style="float:right"><input type="checkbox" name="lodging_not_required6" value="Yes" onClick="toggleVisibility6(this);"> Lodging not required</div></td></tr>

								<tr><td colspan="2">
								<div id="lodging6">
									<table class="lodging_table">
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
									</table>
								</div>	
								</td>
								</tr>

								<tr bgcolor="#ecf0b1"><td colspan="2"><strong>Transportation</strong>: <em>Enter details about your transportation to (and while in) <?php echo $_SESSION['event_venue_city6'];?>.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ecf0b1" valign="top"><em>Check all that apply</em></td>
								<td width="100%">
								<input type="checkbox" name="trans_pers_veh_utilized6" value="yes"> Driving Personal Vehicle | <em>If yes, indicate approximate mileage:</em> <input type="text" name="trans_pers_veh_approx_mileage6" size="5"><p>
								<input type="checkbox" name="trans_airline_requested6" value="yes"> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier6" size="10"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid6" value="yes"> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested6" value="yes"> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested6" size="5"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification6" size="20"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff6" value="yes"> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name6" size="20"><p>
								</td>
								</tr>								


<?php } ?>

								
								
								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>8. Budget</strong>: <em>How will this travel be charged?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top"><em>Check all that apply</em></td>
								<td width="100%">

								<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
								
									<input type="checkbox" name="budget_code[]" value="<?php echo $searchData2['budget_code'][0];?>"><?php echo $searchData2['budget_code'][0];?></input><span class="tiny"> | <?php echo $searchData2['Budget_Code_Nickname'][0];?></span><br>
								
								<?php } ?>

								</td>
								</tr>								

<!--
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
-->
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

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Travel Advance:</td>
								<td width="100%"><input type="checkbox" name="travel_advance_requested" value="yes"> Travel advance requested (only available for rental car, hotel, and/or meals)</td>
								</tr>		
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb" style="vertical-align:text-top">Registration Fee:</td>
								<td width="100%"><input type="text" name="req_registration_fee" size="5"> <em>Enter amount of registration fee. Leave blank if no fee.</em><br>
								Registration fee paid by? <input type="radio" name="req_registration_fee_paid_by" value="SEDL"> SEDL &nbsp;&nbsp;&nbsp;<input type="radio" name="req_registration_fee_paid_by" value="Staff"> Staff
								</td>
								</tr>								

								<tr><td colspan="2" style="background-color:#a4d6a7"><strong>9. Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
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


<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'new_submit') { 

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
$newrecord -> AddDBParam('travel_admin_sims_user_ID',$_GET['travel_admin_sims_user_ID']);

if($_GET['self_approves_tr'] == 'Yes'){
$newrecord -> AddDBParam('approval_status_tr','Approved');
$newrecord -> AddDBParam('tr_approved_by',$_SESSION['user_ID']);
}else{
$newrecord -> AddDBParam('approval_status_tr','Pending');
}

if($_GET['travel_pba_sims_user_ID'] !== ''){
$newrecord -> AddDBParam('alt_pba_for_travel',$travel_pba_sims_user_ID);
}
$newrecord -> AddDBParam('purpose_of_travel',$purpose_of_travel);
$newrecord -> AddDBParam('purpose_of_travel_descr',$_GET['purpose_of_travel_descr']);
$newrecord -> AddDBParam('req_flight_destination_city_state',$_GET['req_flight_destination_city_state']);
$newrecord -> AddDBParam('event_name',$_GET['purpose_of_travel_descr']);
$newrecord -> AddDBParam('event_venue',$_GET['event_venue']);
$newrecord -> AddDBParam('event_venue_addr',$_GET['event_venue_addr']);
$newrecord -> AddDBParam('event_start_date',$_GET['event_start_date']);
$newrecord -> AddDBParam('event_end_date',$_GET['event_end_date']);
$newrecord -> AddDBParam('event_start_time',$_GET['event_start_time']);
$newrecord -> AddDBParam('event_end_time',$_GET['event_end_time']);
$newrecord -> AddDBParam('leave_date_requested',$_SESSION['leave_date_requested']);
$newrecord -> AddDBParam('return_date_requested',$_SESSION['return_date_requested']);
$newrecord -> AddDBParam('leave_time_requested',$_SESSION['leave_time_requested']);
$newrecord -> AddDBParam('return_time_requested',$_SESSION['return_time_requested']);
$newrecord -> AddDBParam('budget_code',$budget_code);
$newrecord -> AddDBParam('budget_code_FFS_code',$_GET['budget_code_FFS_code']);
$newrecord -> AddDBParam('budget_code_CPL_code',$_GET['budget_code_CPL_code']);
$newrecord -> AddDBParam('budget_code_other',$_GET['budget_code_other']);
$newrecord -> AddDBParam('budget_code_instructions',$_GET['budget_code_instructions']);
$newrecord -> AddDBParam('req_registration_fee',$_GET['req_registration_fee']);
$newrecord -> AddDBParam('req_registration_fee_paid_by',$_GET['req_registration_fee_paid_by']);
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

if($_GET['lodging_not_required'] == 'Yes'){
$newrecord -> AddDBParam('lodging_not_required','Yes');
}else{
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
}

$newrecord -> AddDBParam('other_information',$_GET['other_information']);
$newrecord -> AddDBParam('num_dest','1');
$newrecord -> AddDBParam('voucher_origin',$_SESSION['origin_city'].', '.$_SESSION['origin_state']);


if($_SESSION['num_dest'] > 1){

	$newrecord -> AddDBParam('multi_dest','yes');
	
	$newrecord -> AddDBParam('event_venue_city1',$_SESSION['event_venue_city1']);
	$newrecord -> AddDBParam('event_venue_state1',$_SESSION['event_venue_state1']);
	$newrecord -> AddDBParam('event_conus_state1',$_SESSION['event_venue_state1']);
	$newrecord -> AddDBParam('event_venue_city1_travel_start',$_SESSION['event_venue_city_date_from1']);
	$newrecord -> AddDBParam('event_venue_city1_travel_end',$_SESSION['event_venue_city_date_to1']);
	
	$newrecord -> AddDBParam('event_venue_city2',$_SESSION['event_venue_city2']);
	$newrecord -> AddDBParam('event_venue_state2',$_SESSION['event_venue_state2']);
	$newrecord -> AddDBParam('event_conus_state2',$_SESSION['event_venue_state2']);
	$newrecord -> AddDBParam('event_venue_city2_travel_start',$_SESSION['event_venue_city_date_from2']);
	$newrecord -> AddDBParam('event_venue_city2_travel_end',$_SESSION['event_venue_city_date_to2']);
	
	$newrecord -> AddDBParam('event_venue_city3',$_SESSION['event_venue_city3']);
	$newrecord -> AddDBParam('event_venue_state3',$_SESSION['event_venue_state3']);
	$newrecord -> AddDBParam('event_conus_state3',$_SESSION['event_venue_state3']);
	$newrecord -> AddDBParam('event_venue_city3_travel_start',$_SESSION['event_venue_city_date_from3']);
	$newrecord -> AddDBParam('event_venue_city3_travel_end',$_SESSION['event_venue_city_date_to3']);
	
	$newrecord -> AddDBParam('event_venue_city4',$_SESSION['event_venue_city4']);
	$newrecord -> AddDBParam('event_venue_state4',$_SESSION['event_venue_state4']);
	$newrecord -> AddDBParam('event_conus_state4',$_SESSION['event_venue_state4']);
	$newrecord -> AddDBParam('event_venue_city4_travel_start',$_SESSION['event_venue_city_date_from4']);
	$newrecord -> AddDBParam('event_venue_city4_travel_end',$_SESSION['event_venue_city_date_to4']);
	
	$newrecord -> AddDBParam('event_venue_city5',$_SESSION['event_venue_city5']);
	$newrecord -> AddDBParam('event_venue_state5',$_SESSION['event_venue_state5']);
	$newrecord -> AddDBParam('event_conus_state5',$_SESSION['event_venue_state5']);
	$newrecord -> AddDBParam('event_venue_city5_travel_start',$_SESSION['event_venue_city_date_from5']);
	$newrecord -> AddDBParam('event_venue_city5_travel_end',$_SESSION['event_venue_city_date_to5']);
	
	$newrecord -> AddDBParam('event_venue_city6',$_SESSION['event_venue_city6']);
	$newrecord -> AddDBParam('event_venue_state6',$_SESSION['event_venue_state6']);
	$newrecord -> AddDBParam('event_conus_state6',$_SESSION['event_venue_state6']);
	$newrecord -> AddDBParam('event_venue_city6_travel_start',$_SESSION['event_venue_city_date_from6']);
	$newrecord -> AddDBParam('event_venue_city6_travel_end',$_SESSION['event_venue_city_date_to6']);

}else{

	$newrecord -> AddDBParam('event_venue_city',$_SESSION['city']);
	$newrecord -> AddDBParam('event_venue_state',$_SESSION['state']);
	$newrecord -> AddDBParam('event_conus_state',$_SESSION['state']);

}

$newrecordResult = $newrecord -> FMNew();

//  echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//  echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$newrecordData = current($newrecordResult['data']);
#################################################
## END: CREATE NEW TRAVEL REQUEST RECORD ##
#################################################





if($newrecordResult['errorCode'] == '0'){  // THE TRAVEL REQUEST WAS SUCCESSFULLY CREATED

###############################################
## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
###############################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SUBMIT_TR');
$newrecord -> AddDBParam('travel_auth_ID',$newrecordData['travel_auth_ID'][0]);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
#############################################
## END: SAVE ACTION TO TRAVEL APPROVAL LOG ##
#############################################

###############################################
## START: SAVE ACTION TO SIMS AUDIT LOG ##
###############################################
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
#############################################
## END: SAVE ACTION TO SIMS AUDIT LOG ##
#############################################

if($_SESSION['num_dest'] == 1){ // IF THIS IS A SINGLE DESTINATION TRAVEL REQUEST
	###############################################################################
	## START: POPULATE TRAVEL_AUTH_DAYS TABLE FOR THIS AUTH - SINGLE DESTINATION ##
	###############################################################################
		foreach($_SESSION['travel_days'] as $current) {
			$newrecord = new FX($serverIP,$webCompanionPort);
			$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_days');
			$newrecord -> SetDBPassword($webPW,$webUN);
			$newrecord -> AddDBParam('destination_city',$_SESSION['city']);
			$newrecord -> AddDBParam('destination_city_original',$_SESSION['city']);
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



$_SESSION['travel_request_submitted_staff'] = '1';

	if($_GET['self_approves_tr'] == 'Yes'){ // REQUESTOR APPROVES OWN TRAVEL REQUESTS (MANAGER)
	
		##############################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
		##############################################################
		//$to = 'eric.waters@sedl.org';
		$to = $newrecordData['travel_admin_sims_user_ID'][0].'@sedl.org';
		$subject = stripslashes($newrecordData['staff_full_name'][0]).' has submitted an approved travel request requiring processing';
		$message = 
		'Dear '.$newrecordData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0].','."\n\n".
		
		//'[E-mail was sent to: '.$newrecordData['travel_admin_sims_user_ID'][0].'@sedl.org]'."\n\n".
		
		'A travel request for '.stripslashes($newrecordData['staff_full_name'][0]).' has been submitted to SIMS for processing.'."\n\n".
		
		'------------------------------------------------------------'."\n".
		' TRAVEL REQUEST DETAILS'."\n".
		'------------------------------------------------------------'."\n".
		'Description: '.stripslashes($newrecordData['purpose_of_travel_descr'][0])."\n".
		'Destination: '.$newrecordData['c_destinations_all_display_venues_csv'][0]."\n".
		'Date(s) of Travel: '.$newrecordData['leave_date_requested'][0].' to '.$newrecordData['return_date_requested'][0]."\n".
		'------------------------------------------------------------'."\n\n".
		
		'To process this travel request, click here: '."\n".
		'fmp7://198.214.140.248/CC_dms.fp7'."\n\n".
		
		'------------------------------------------------------------------------------------------------------------------'."\n".
		'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
		'------------------------------------------------------------------------------------------------------------------';
		
		$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: SIMS@sedl.org';
		
		mail($to, $subject, $message, $headers);
		############################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
		############################################################

	}else{ // REQUESTOR DOES NOT APPROVE OWN TRAVEL REQUESTS (NOT MANAGER)

		if($newrecordData['signer_ID_pba'][0] == $newrecordData['signer_ID_spvsr'][0]){ // PBA AND SPVSR ARE THE SAME PERSON
		
			########################################################
			## START: TRIGGER NOTIFICATION E-MAIL TO PBA ##
			########################################################
			//$to = 'eric.waters@sedl.org';
			$to = $newrecordData['signer_ID_pba'][0].'@sedl.org';
			$subject = stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' has submitted a travel request (s)';
			$message = 
			'Dear '.$newrecordData['signer_ID_pba'][0].','."\n\n".
			
			'A travel request for '.stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' has been submitted to SIMS and requires your approval.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TRAVEL REQUEST DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			'Event: '.stripslashes($newrecordData['event_name'][0])."\n".
			'Destination: '.$newrecordData['c_destinations_all_display_venues_csv'][0]."\n".
			'Date(s) of Travel: '.$newrecordData['leave_date_requested'][0].' to '.$newrecordData['return_date_requested'][0]."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view and approve this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$newrecordData['travel_auth_ID'][0].'&action=view_tr'."\n\n".
			
			'------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Cc: '.$newrecordData['travel_admin_sims_user_ID'][0].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
			######################################################
			## END: TRIGGER NOTIFICATION E-MAIL TO PBA ##
			######################################################
		
		}else{ // PBA AND SPVSR ARE NOT THE SAME PERSON
		
			########################################################
			## START: TRIGGER NOTIFICATION E-MAIL TO SPVSR ##
			########################################################
			//$to = 'eric.waters@sedl.org';
			$to = $newrecordData['signer_ID_spvsr'][0].'@sedl.org';
			$subject = stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' has submitted a travel request (s)';
			$message = 
			'Dear '.$newrecordData['signer_ID_spvsr'][0].','."\n\n".
			
			'A travel request for '.stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' has been submitted to SIMS and requires your approval.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TRAVEL REQUEST DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			'Event: '.stripslashes($newrecordData['event_name'][0])."\n".
			'Destination: '.$newrecordData['c_destinations_all_display_venues_csv'][0]."\n".
			'Date(s) of Travel: '.$newrecordData['leave_date_requested'][0].' to '.$newrecordData['return_date_requested'][0]."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view and approve this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$newrecordData['travel_auth_ID'][0].'&action=view_tr'."\n\n".
			
			'------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Cc: '.$newrecordData['travel_admin_sims_user_ID'][0].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
			######################################################
			## END: TRIGGER NOTIFICATION E-MAIL TO SPVSR ##
			######################################################
		
		}
	
	}


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


<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'new_submit_multi') { 

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
$newrecord -> AddDBParam('multi_dest','yes');
$newrecord -> AddDBParam('num_dest',$_SESSION['num_dest']);
$newrecord -> AddDBParam('event_name',$_GET['event_name1'].'**');
$newrecord -> AddDBParam('travel_admin_sims_user_ID',$_GET['travel_admin_sims_user_ID']);

if($_GET['self_approves_tr'] == 'Yes'){
$newrecord -> AddDBParam('approval_status_tr','Approved');
$newrecord -> AddDBParam('tr_approved_by',$_SESSION['user_ID']);
}else{
$newrecord -> AddDBParam('approval_status_tr','Pending');
}

$newrecord -> AddDBParam('purpose_of_travel',$purpose_of_travel);
$newrecord -> AddDBParam('purpose_of_travel_descr',$_GET['purpose_of_travel_descr']);
$newrecord -> AddDBParam('leave_date_requested',$_SESSION['leave_date_requested']);
$newrecord -> AddDBParam('return_date_requested',$_SESSION['return_date_requested']);
$newrecord -> AddDBParam('leave_time_requested',$_SESSION['leave_time_requested']);
$newrecord -> AddDBParam('return_time_requested',$_SESSION['return_time_requested']);
$newrecord -> AddDBParam('budget_code',$budget_code);
$newrecord -> AddDBParam('budget_code_FFS_code',$_GET['budget_code_FFS_code']);
$newrecord -> AddDBParam('budget_code_CPL_code',$_GET['budget_code_CPL_code']);
$newrecord -> AddDBParam('budget_code_other',$_GET['budget_code_other']);
$newrecord -> AddDBParam('budget_code_instructions',$_GET['budget_code_instructions']);
$newrecord -> AddDBParam('req_registration_fee',$_GET['req_registration_fee']);
$newrecord -> AddDBParam('req_registration_fee_paid_by',$_GET['req_registration_fee_paid_by']);
$newrecord -> AddDBParam('travel_advance_requested',$_GET['travel_advance_requested']);
$newrecord -> AddDBParam('other_information',$_GET['other_information']);

$newrecord -> AddDBParam('voucher_origin1',$_SESSION['origin_city'].', '.$_SESSION['origin_state']);
$newrecord -> AddDBParam('event_venue_city1',$_SESSION['event_venue_city1']);
$newrecord -> AddDBParam('event_venue_state1',$_SESSION['event_venue_state1']);
$newrecord -> AddDBParam('event_conus_state1',$_SESSION['event_venue_state1']);
$newrecord -> AddDBParam('event_venue_city1_travel_start',$_SESSION['event_venue_city_date_from1']);
$newrecord -> AddDBParam('event_venue_city1_travel_end',$_SESSION['event_venue_city_date_to1']);
$newrecord -> AddDBParam('event_name1',$_GET['event_name1']);
$newrecord -> AddDBParam('event_venue1',$_GET['event_venue1']);
$newrecord -> AddDBParam('event_venue_addr1',$_GET['event_venue_addr1']);
$newrecord -> AddDBParam('event_start_date1',$_GET['event_start_date1']);
$newrecord -> AddDBParam('event_end_date1',$_GET['event_end_date1']);
$newrecord -> AddDBParam('event_start_time1',$_GET['event_start_time1']);
$newrecord -> AddDBParam('event_end_time1',$_GET['event_end_time1']);

if($_GET['lodging_not_required1'] == 'Yes'){
$newrecord -> AddDBParam('lodging_not_required1','Yes');
}else{
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
}

$newrecord -> AddDBParam('trans_pers_veh_utilized1',$_GET['trans_pers_veh_utilized1']);
$newrecord -> AddDBParam('trans_pers_veh_approx_mileage1',$_GET['trans_pers_veh_approx_mileage1']);
$newrecord -> AddDBParam('trans_airline_requested1',$_GET['trans_airline_requested1']);
$newrecord -> AddDBParam('trans_airline_preferred_carrier1',$_GET['trans_airline_preferred_carrier1']);
$newrecord -> AddDBParam('trans_airline_bta_prepaid1',$_GET['trans_airline_bta_prepaid1']);
$newrecord -> AddDBParam('trans_rental_car_requested1',$_GET['trans_rental_car_requested1']);
$newrecord -> AddDBParam('trans_rental_car_num_days_requested1',$_GET['trans_rental_car_num_days_requested1']);
$newrecord -> AddDBParam('trans_rental_car_justification1',$_GET['trans_rental_car_justification1']);
$newrecord -> AddDBParam('trans_traveling_with_other_staff1',$_GET['trans_traveling_with_other_staff1']);
$newrecord -> AddDBParam('trans_traveling_with_name1',$_GET['trans_traveling_with_name1']);

$newrecord -> AddDBParam('voucher_origin2',$_SESSION['event_venue_city1'].', '.$_SESSION['event_venue_state1']);
$newrecord -> AddDBParam('event_venue_city2',$_SESSION['event_venue_city2']);
$newrecord -> AddDBParam('event_venue_state2',$_SESSION['event_venue_state2']);
$newrecord -> AddDBParam('event_conus_state2',$_SESSION['event_venue_state2']);
$newrecord -> AddDBParam('event_venue_city2_travel_start',$_SESSION['event_venue_city_date_from2']);
$newrecord -> AddDBParam('event_venue_city2_travel_end',$_SESSION['event_venue_city_date_to2']);
$newrecord -> AddDBParam('event_name2',$_GET['event_name2']);
$newrecord -> AddDBParam('event_venue2',$_GET['event_venue2']);
$newrecord -> AddDBParam('event_venue_addr2',$_GET['event_venue_addr2']);
$newrecord -> AddDBParam('event_start_date2',$_GET['event_start_date2']);
$newrecord -> AddDBParam('event_end_date2',$_GET['event_end_date2']);
$newrecord -> AddDBParam('event_start_time2',$_GET['event_start_time2']);
$newrecord -> AddDBParam('event_end_time2',$_GET['event_end_time2']);

if($_GET['lodging_not_required2'] == 'Yes'){
$newrecord -> AddDBParam('lodging_not_required2','Yes');
}else{
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
}

$newrecord -> AddDBParam('trans_pers_veh_utilized2',$_GET['trans_pers_veh_utilized2']);
$newrecord -> AddDBParam('trans_pers_veh_approx_mileage2',$_GET['trans_pers_veh_approx_mileage2']);
$newrecord -> AddDBParam('trans_airline_requested2',$_GET['trans_airline_requested2']);
$newrecord -> AddDBParam('trans_airline_preferred_carrier2',$_GET['trans_airline_preferred_carrier2']);
$newrecord -> AddDBParam('trans_airline_bta_prepaid2',$_GET['trans_airline_bta_prepaid2']);
$newrecord -> AddDBParam('trans_rental_car_requested2',$_GET['trans_rental_car_requested2']);
$newrecord -> AddDBParam('trans_rental_car_num_days_requested2',$_GET['trans_rental_car_num_days_requested2']);
$newrecord -> AddDBParam('trans_rental_car_justification2',$_GET['trans_rental_car_justification2']);
$newrecord -> AddDBParam('trans_traveling_with_other_staff2',$_GET['trans_traveling_with_other_staff2']);
$newrecord -> AddDBParam('trans_traveling_with_name2',$_GET['trans_traveling_with_name2']);

if($_SESSION['num_dest'] > 2){
	
$newrecord -> AddDBParam('voucher_origin3',$_SESSION['event_venue_city2'].', '.$_SESSION['event_venue_state2']);
$newrecord -> AddDBParam('event_venue_city3',$_SESSION['event_venue_city3']);
$newrecord -> AddDBParam('event_venue_state3',$_SESSION['event_venue_state3']);
$newrecord -> AddDBParam('event_conus_state3',$_SESSION['event_venue_state3']);
$newrecord -> AddDBParam('event_venue_city3_travel_start',$_SESSION['event_venue_city_date_from3']);
$newrecord -> AddDBParam('event_venue_city3_travel_end',$_SESSION['event_venue_city_date_to3']);
$newrecord -> AddDBParam('event_name3',$_GET['event_name3']);
$newrecord -> AddDBParam('event_venue3',$_GET['event_venue3']);
$newrecord -> AddDBParam('event_venue_addr3',$_GET['event_venue_addr3']);
$newrecord -> AddDBParam('event_start_date3',$_GET['event_start_date3']);
$newrecord -> AddDBParam('event_end_date3',$_GET['event_end_date3']);
$newrecord -> AddDBParam('event_start_time3',$_GET['event_start_time3']);
$newrecord -> AddDBParam('event_end_time3',$_GET['event_end_time3']);

if($_GET['lodging_not_required3'] == 'Yes'){
$newrecord -> AddDBParam('lodging_not_required3','Yes');
}else{
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

$newrecord -> AddDBParam('trans_pers_veh_utilized3',$_GET['trans_pers_veh_utilized3']);
$newrecord -> AddDBParam('trans_pers_veh_approx_mileage3',$_GET['trans_pers_veh_approx_mileage3']);
$newrecord -> AddDBParam('trans_airline_requested3',$_GET['trans_airline_requested3']);
$newrecord -> AddDBParam('trans_airline_preferred_carrier3',$_GET['trans_airline_preferred_carrier3']);
$newrecord -> AddDBParam('trans_airline_bta_prepaid3',$_GET['trans_airline_bta_prepaid3']);
$newrecord -> AddDBParam('trans_rental_car_requested3',$_GET['trans_rental_car_requested3']);
$newrecord -> AddDBParam('trans_rental_car_num_days_requested3',$_GET['trans_rental_car_num_days_requested3']);
$newrecord -> AddDBParam('trans_rental_car_justification3',$_GET['trans_rental_car_justification3']);
$newrecord -> AddDBParam('trans_traveling_with_other_staff3',$_GET['trans_traveling_with_other_staff3']);
$newrecord -> AddDBParam('trans_traveling_with_name3',$_GET['trans_traveling_with_name3']);
	
}

if($_SESSION['num_dest'] > 3){
	
$newrecord -> AddDBParam('voucher_origin4',$_SESSION['event_venue_city3'].', '.$_SESSION['event_venue_state3']);
$newrecord -> AddDBParam('event_venue_city4',$_SESSION['event_venue_city4']);
$newrecord -> AddDBParam('event_venue_state4',$_SESSION['event_venue_state4']);
$newrecord -> AddDBParam('event_conus_state4',$_SESSION['event_venue_state4']);
$newrecord -> AddDBParam('event_venue_city4_travel_start',$_SESSION['event_venue_city_date_from4']);
$newrecord -> AddDBParam('event_venue_city4_travel_end',$_SESSION['event_venue_city_date_to4']);
$newrecord -> AddDBParam('event_name4',$_GET['event_name4']);
$newrecord -> AddDBParam('event_venue4',$_GET['event_venue4']);
$newrecord -> AddDBParam('event_venue_addr4',$_GET['event_venue_addr4']);
$newrecord -> AddDBParam('event_start_date4',$_GET['event_start_date4']);
$newrecord -> AddDBParam('event_end_date4',$_GET['event_end_date4']);
$newrecord -> AddDBParam('event_start_time4',$_GET['event_start_time4']);
$newrecord -> AddDBParam('event_end_time4',$_GET['event_end_time4']);

if($_GET['lodging_not_required4'] == 'Yes'){
$newrecord -> AddDBParam('lodging_not_required4','Yes');
}else{
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

$newrecord -> AddDBParam('trans_pers_veh_utilized4',$_GET['trans_pers_veh_utilized4']);
$newrecord -> AddDBParam('trans_pers_veh_approx_mileage4',$_GET['trans_pers_veh_approx_mileage4']);
$newrecord -> AddDBParam('trans_airline_requested4',$_GET['trans_airline_requested4']);
$newrecord -> AddDBParam('trans_airline_preferred_carrier4',$_GET['trans_airline_preferred_carrier4']);
$newrecord -> AddDBParam('trans_airline_bta_prepaid4',$_GET['trans_airline_bta_prepaid4']);
$newrecord -> AddDBParam('trans_rental_car_requested4',$_GET['trans_rental_car_requested4']);
$newrecord -> AddDBParam('trans_rental_car_num_days_requested4',$_GET['trans_rental_car_num_days_requested4']);
$newrecord -> AddDBParam('trans_rental_car_justification4',$_GET['trans_rental_car_justification4']);
$newrecord -> AddDBParam('trans_traveling_with_other_staff4',$_GET['trans_traveling_with_other_staff4']);
$newrecord -> AddDBParam('trans_traveling_with_name4',$_GET['trans_traveling_with_name4']);
	
}

if($_SESSION['num_dest'] > 4){
	
$newrecord -> AddDBParam('voucher_origin5',$_SESSION['event_venue_city4'].', '.$_SESSION['event_venue_state4']);
$newrecord -> AddDBParam('event_venue_city5',$_SESSION['event_venue_city5']);
$newrecord -> AddDBParam('event_venue_state5',$_SESSION['event_venue_state5']);
$newrecord -> AddDBParam('event_conus_state5',$_SESSION['event_venue_state5']);
$newrecord -> AddDBParam('event_venue_city5_travel_start',$_SESSION['event_venue_city_date_from5']);
$newrecord -> AddDBParam('event_venue_city5_travel_end',$_SESSION['event_venue_city_date_to5']);
$newrecord -> AddDBParam('event_name5',$_GET['event_name5']);
$newrecord -> AddDBParam('event_venue5',$_GET['event_venue5']);
$newrecord -> AddDBParam('event_venue_addr5',$_GET['event_venue_addr5']);
$newrecord -> AddDBParam('event_start_date5',$_GET['event_start_date5']);
$newrecord -> AddDBParam('event_end_date5',$_GET['event_end_date5']);
$newrecord -> AddDBParam('event_start_time5',$_GET['event_start_time5']);
$newrecord -> AddDBParam('event_end_time5',$_GET['event_end_time5']);

if($_GET['lodging_not_required5'] == 'Yes'){
$newrecord -> AddDBParam('lodging_not_required5','Yes');
}else{
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

$newrecord -> AddDBParam('trans_pers_veh_utilized5',$_GET['trans_pers_veh_utilized5']);
$newrecord -> AddDBParam('trans_pers_veh_approx_mileage5',$_GET['trans_pers_veh_approx_mileage5']);
$newrecord -> AddDBParam('trans_airline_requested5',$_GET['trans_airline_requested5']);
$newrecord -> AddDBParam('trans_airline_preferred_carrier5',$_GET['trans_airline_preferred_carrier5']);
$newrecord -> AddDBParam('trans_airline_bta_prepaid5',$_GET['trans_airline_bta_prepaid5']);
$newrecord -> AddDBParam('trans_rental_car_requested5',$_GET['trans_rental_car_requested5']);
$newrecord -> AddDBParam('trans_rental_car_num_days_requested5',$_GET['trans_rental_car_num_days_requested5']);
$newrecord -> AddDBParam('trans_rental_car_justification5',$_GET['trans_rental_car_justification5']);
$newrecord -> AddDBParam('trans_traveling_with_other_staff5',$_GET['trans_traveling_with_other_staff5']);
$newrecord -> AddDBParam('trans_traveling_with_name5',$_GET['trans_traveling_with_name5']);
	
}

if($_SESSION['num_dest'] > 5){
	
$newrecord -> AddDBParam('voucher_origin6',$_SESSION['event_venue_city5'].', '.$_SESSION['event_venue_state5']);
$newrecord -> AddDBParam('event_venue_city6',$_SESSION['event_venue_city6']);
$newrecord -> AddDBParam('event_venue_state6',$_SESSION['event_venue_state6']);
$newrecord -> AddDBParam('event_conus_state6',$_SESSION['event_venue_state6']);
$newrecord -> AddDBParam('event_venue_city6_travel_start',$_SESSION['event_venue_city_date_from6']);
$newrecord -> AddDBParam('event_venue_city6_travel_end',$_SESSION['event_venue_city_date_to6']);
$newrecord -> AddDBParam('event_name6',$_GET['event_name6']);
$newrecord -> AddDBParam('event_venue6',$_GET['event_venue6']);
$newrecord -> AddDBParam('event_venue_addr6',$_GET['event_venue_addr6']);
$newrecord -> AddDBParam('event_start_date6',$_GET['event_start_date6']);
$newrecord -> AddDBParam('event_end_date6',$_GET['event_end_date6']);
$newrecord -> AddDBParam('event_start_time6',$_GET['event_start_time6']);
$newrecord -> AddDBParam('event_end_time6',$_GET['event_end_time6']);

if($_GET['lodging_not_required6'] == 'Yes'){
$newrecord -> AddDBParam('lodging_not_required6','Yes');
}else{
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

$newrecord -> AddDBParam('trans_pers_veh_utilized6',$_GET['trans_pers_veh_utilized6']);
$newrecord -> AddDBParam('trans_pers_veh_approx_mileage6',$_GET['trans_pers_veh_approx_mileage6']);
$newrecord -> AddDBParam('trans_airline_requested6',$_GET['trans_airline_requested6']);
$newrecord -> AddDBParam('trans_airline_preferred_carrier6',$_GET['trans_airline_preferred_carrier6']);
$newrecord -> AddDBParam('trans_airline_bta_prepaid6',$_GET['trans_airline_bta_prepaid6']);
$newrecord -> AddDBParam('trans_rental_car_requested6',$_GET['trans_rental_car_requested6']);
$newrecord -> AddDBParam('trans_rental_car_num_days_requested6',$_GET['trans_rental_car_num_days_requested6']);
$newrecord -> AddDBParam('trans_rental_car_justification6',$_GET['trans_rental_car_justification6']);
$newrecord -> AddDBParam('trans_traveling_with_other_staff6',$_GET['trans_traveling_with_other_staff6']);
$newrecord -> AddDBParam('trans_traveling_with_name6',$_GET['trans_traveling_with_name6']);
	
}

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode[5464]: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
$newrecordData = current($newrecordResult['data']);
#################################################
## END: CREATE NEW TRAVEL REQUEST RECORD ##
#################################################





if($newrecordResult['errorCode'] == '0'){  // THE TRAVEL REQUEST WAS SUCCESSFULLY CREATED

// NEED TO COMPARE TRAVEL DAYS FIELDS FOR EACH DESTINATION TO DETERMINE WHICH RATE AND CITY TO ENTER IN travel_auth_days TABLE
//print_r($_SESSION['travel_days']);
//echo '<p>$num_dest[5479]: '.$_SESSION['num_dest'];
	###############################################################################
	## START: POPULATE TRAVEL_AUTH_DAYS TABLE FOR THIS AUTH - MULTI DESTINATION ##
	###############################################################################
		foreach($_SESSION['travel_days'] as $current) { // LOOP THROUGH NON-REPEATING TRAVEL DAYS ARRAY

			for($i=1;$i<=$_SESSION['num_dest'];$i++) { // FOR EACH NON-REPEATING TRAVEL DAY, LOOP THROUGH EACH DESTINATION CITY TO CHECK IF TRAVEL WILL BE IN THAT CITY THAT DAY
				
				$travel_days_fieldname = 'c_event_venue_city'.$i.'_travel_days';
				$travel_city_fieldname = 'event_venue_city'.$i;
				$travel_state_fieldname = 'event_venue_state'.$i;
				
				$pos = strpos($newrecordData["$travel_days_fieldname"][0], $current);
				//echo '<p>$current: '.$current;
				//echo '<p>$travel_days_fieldname: '.$newrecordData["$travel_days_fieldname"][0];
				if($pos === false){ // CURRENT TRAVEL DAY NOT FOUND IN TRAVEL DAYS FOR THIS DESTINATION
				//echo '<p>'.$i.' - travel day not found in array';
				}else{ // ADD RECORD TO travel_auth_days TABLE FOR THIS DESTINATION AND TRAVEL DAY
				//echo '<p>$travel_city_fieldname[5477]: '.$newrecordData["$travel_city_fieldname"][0];	
				//echo '<p>$travel_state_fieldname: '.$newrecordData["$travel_state_fieldname"][0];	
					$newrecord = new FX($serverIP,$webCompanionPort);
					$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_days');
					$newrecord -> SetDBPassword($webPW,$webUN);
					
					$newrecord -> AddDBParam('destination_city',$newrecordData["$travel_city_fieldname"][0]);
					$newrecord -> AddDBParam('destination_city_original',$newrecordData["$travel_city_fieldname"][0]);
					$newrecord -> AddDBParam('destination_state',$newrecordData["$travel_state_fieldname"][0]);
					$newrecord -> AddDBParam('travel_auth_ID',$newrecordData['travel_auth_ID'][0]);
					$newrecord -> AddDBParam('travel_date',$current);
					$newrecordResult = $newrecord -> FMNew();
					//echo  '<p>errorCode[5506]: '.$i.' - '.$newrecordResult['errorCode'];
					//echo  '<p>foundCount: '.$i.' - '.$newrecordResult['foundCount'];

				}

			}

		}
	#############################################################################
	## END: POPULATE TRAVEL_AUTH_DAYS TABLE FOR THIS AUTH - MULTI DESTINATION ##
	#############################################################################

					


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



$_SESSION['travel_request_submitted_staff'] = '1';


	if($_GET['self_approves_tr'] == 'Yes'){ // REQUESTOR APPROVES OWN TRAVEL REQUESTS (MANAGER)
	
		##############################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
		##############################################################
		//$to = 'eric.waters@sedl.org';
		$to = $newrecordData['travel_admin_sims_user_ID'][0].'@sedl.org';
		$subject = stripslashes($newrecordData['staff_full_name'][0]).' has submitted an approved travel request requiring processing';
		$message = 
		'Dear '.$newrecordData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0].','."\n\n".
		
		//'[E-mail was sent to: '.$newrecordData['travel_admin_sims_user_ID'][0].'@sedl.org]'."\n\n".
		
		'A travel request for '.stripslashes($newrecordData['staff_full_name'][0]).' has been submitted to SIMS for processing.'."\n\n".
		
		'------------------------------------------------------------'."\n".
		' TRAVEL REQUEST DETAILS'."\n".
		'------------------------------------------------------------'."\n".
		'Description: '.stripslashes($newrecordData['purpose_of_travel_descr'][0])."\n".
		'Destinations: '.$newrecordData['c_destinations_all_display_venues_csv'][0]."\n".
		'Date(s) of Travel: '.$newrecordData['leave_date_requested'][0].' to '.$newrecordData['return_date_requested'][0]."\n".
		'------------------------------------------------------------'."\n\n".
		
		'To process this travel request, click here: '."\n".
		'fmp7://198.214.140.248/CC_dms.fp7'."\n\n".
		
		'------------------------------------------------------------------------------------------------------------------'."\n".
		'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
		'------------------------------------------------------------------------------------------------------------------';
		
		$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: SIMS@sedl.org';
		
		mail($to, $subject, $message, $headers);
		############################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
		############################################################

	}else{ // REQUESTOR DOES NOT APPROVE OWN TRAVEL REQUESTS (NOT MANAGER)

		if($newrecordData['signer_ID_pba'][0] == $newrecordData['signer_ID_spvsr'][0]){ // PBA AND SPVSR ARE THE SAME PERSON
		
			########################################################
			## START: TRIGGER NOTIFICATION E-MAIL TO PBA ##
			########################################################
			//$to = 'eric.waters@sedl.org';
			$to = $newrecordData['signer_ID_pba'].'@sedl.org';
			$subject = stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' has submitted a travel request (m)';
			$message = 
			'Dear '.$newrecordData['signer_ID_pba'][0].','."\n\n".
			
			//'[E-mail was sent to: '.$newrecordData['signer_ID_pba'][0].'@sedl.org'."\n\n".
			
			'A travel request has been submitted by '.stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' and requires your approval.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TRAVEL REQUEST DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			'Event: '.$newrecordData['event_name'][0]."\n".
			'Destinations: '.$newrecordData['c_destinations_all_display_venues_csv'][0]."\n".
			'Date(s) of Travel: '.$newrecordData['leave_date_requested'][0].' to '.$newrecordData['return_date_requested'][0]."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view and approve this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$newrecordData['travel_auth_ID'][0].'&action=view_tr'."\n\n".
							
			'------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'------------------------------------------------------------------------------------------------------------------';
			
			//$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Cc: '.$newrecordData['travel_admin_sims_user_ID'][0].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
			$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
			######################################################
			## END: TRIGGER NOTIFICATION E-MAIL TO PBA ##
			######################################################
		
		}else{ // PBA AND SPVSR ARE NOT THE SAME PERSON
		
			########################################################
			## START: TRIGGER NOTIFICATION E-MAIL TO SPVSR ##
			########################################################
			//$to = 'eric.waters@sedl.org';
			$to = $newrecordData['signer_ID_spvsr'].'@sedl.org';
			$subject = stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' has submitted a travel request (m)';
			$message = 
			'Dear '.$newrecordData['signer_ID_spvsr'][0].','."\n\n".
			
			//'[E-mail was sent to: '.$newrecordData['signer_ID_spvsr'][0].'@sedl.org'."\n\n".
			
			'A travel request has been submitted by '.stripslashes($newrecordData['staff::c_full_name_first_last'][0]).' and requires your approval.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TRAVEL REQUEST DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			'Event: '.$newrecordData['event_name'][0]."\n".
			'Destinations: '.$newrecordData['c_destinations_all_display_venues_csv'][0]."\n".
			'Date(s) of Travel: '.$newrecordData['leave_date_requested'][0].' to '.$newrecordData['return_date_requested'][0]."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view and approve this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$newrecordData['travel_auth_ID'][0].'&action=view_tr'."\n\n".
							
			'------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'------------------------------------------------------------------------------------------------------------------';
			
			//$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Cc: '.$newrecordData['travel_admin_sims_user_ID'][0].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
			$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
			######################################################
			## END: TRIGGER NOTIFICATION E-MAIL TO SPVSR ##
			######################################################
		 
		}

	}





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





<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'edit_confirm') { 

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

//$update -> AddDBParam('staff_ID',$_SESSION['staff_ID']);

if($_GET['self_approves_tr'] == 'Yes'){
$update -> AddDBParam('approval_status_tr','Approved');
$update -> AddDBParam('tr_approved_by',$_SESSION['user_ID']);
}else{
$update -> AddDBParam('approval_status_tr','Pending');
$update -> AddDBParam('approval_status_tr_spvsr','Pending');
$update -> AddDBParam('approval_status','Pending');
$update -> AddDBParam('tr_approved_timestamp','');
}

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
$update -> AddDBParam('event_name',$_GET['purpose_of_travel_descr']);
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
$update -> AddDBParam('doc_status','0');




$updateResult = $update -> FMEdit();

//  echo  '<p>errorCode: '.$updateResult['errorCode'];
//  echo  '<p>foundCount: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
#################################################
## END: UPDATE TRAVEL REQUEST RECORD ##
#################################################





if($updateResult['errorCode'] == '0'){  // THE TRAVEL REQUEST WAS SUCCESSFULLY UPDATED

	###############################################
	## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
	###############################################
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','SUBMIT_TR_revised');
	$newrecord -> AddDBParam('travel_auth_ID',$updateData['travel_auth_ID'][0]);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	#############################################
	## END: SAVE ACTION TO TRAVEL APPROVAL LOG ##
	#############################################
	
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
	
	
	if($_GET['self_approves_tr'] == 'Yes'){ // REQUESTOR APPROVES OWN TRAVEL REQUESTS (MANAGER)
	
		##############################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
		##############################################################
		//$to = 'eric.waters@sedl.org';
		$to = $updateData['travel_admin_sims_user_ID'][0].'@sedl.org';
		$subject = stripslashes($updateData['staff_full_name'][0]).' has submitted a revised travel request';
		$message = 
		'Dear '.$updateData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0].','."\n\n".
		
		//'[E-mail was sent to: '.$updateData['travel_admin_sims_user_ID'][0].'@sedl.org]'."\n\n".
		
		'A revised travel request for '.stripslashes($updateData['staff_full_name'][0]).' has been submitted to SIMS.'."\n\n".
		
		'------------------------------------------------------------'."\n".
		' TRAVEL REQUEST DETAILS'."\n".
		'------------------------------------------------------------'."\n".
		'Description: '.stripslashes($updateData['purpose_of_travel_descr'][0])."\n".
		'Destination: '.$updateData['c_destinations_all_display_venues_csv'][0]."\n".
		'Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
		'------------------------------------------------------------'."\n\n".
		
		'To process this travel request, click here: '."\n".
		'fmp7://198.214.140.248/CC_dms.fp7'."\n\n".
		
		'------------------------------------------------------------------------------------------------------------------'."\n".
		'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
		'------------------------------------------------------------------------------------------------------------------';
		
		$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: SIMS@sedl.org';
		
		mail($to, $subject, $message, $headers);
		############################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
		############################################################

	}else{ // REQUESTOR DOES NOT APPROVE OWN TRAVEL REQUESTS (NOT MANAGER)

		if($updateData['signer_ID_pba'][0] == $updateData['signer_ID_spvsr'][0]){ // PBA AND SPVSR ARE THE SAME PERSON
	
			########################################################
			## START: TRIGGER NOTIFICATION E-MAIL TO PBA ##
			########################################################
			//$to = 'eric.waters@sedl.org';
			$to = $updateData['signer_ID_pba'][0].'@sedl.org';
			$subject = stripslashes($updateData['staff::c_full_name_first_last'][0]).' has submitted a revised travel request';
			$message = 
			'Dear '.$updateData['signer_ID_pba'][0].','."\n\n".
			
			'A revised travel request has been submitted by '.stripslashes($updateData['staff::c_full_name_first_last'][0]).' and requires your approval.'."\n\n".
			
			//'[E-mail was sent to: '.$updateData['signer_ID_pba'].'@sedl.org'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TRAVEL REQUEST DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			'Event: '.stripslashes($updateData['purpose_of_travel_descr'][0])."\n".
			'Destination: '.$updateData['c_destinations_all_display_venues_csv'][0]."\n".
			'Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view and approve this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0].'&action=view_tr'."\n\n".
								
			'------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
			######################################################
			## END: TRIGGER NOTIFICATION E-MAIL TO PBA ##
			######################################################
	
		} else { // // PBA AND SPVSR ARE NOT THE SAME PERSON
	
			########################################################
			## START: TRIGGER NOTIFICATION E-MAIL TO SPVSR ##
			########################################################
			//$to = 'eric.waters@sedl.org';
			$to = $updateData['signer_ID_spvsr'][0].'@sedl.org';
			$subject = stripslashes($updateData['staff::c_full_name_first_last'][0]).' has submitted a revised travel request';
			$message = 
			'Dear '.$updateData['signer_ID_spvsr'][0].','."\n\n".
			
			'A revised travel request has been submitted by '.stripslashes($updateData['staff::c_full_name_first_last'][0]).' and requires your approval.'."\n\n".
			
			//'[E-mail was sent to: '.$updateData['signer_ID_spvsr'].'@sedl.org'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TRAVEL REQUEST DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			'Event: '.stripslashes($updateData['purpose_of_travel_descr'][0])."\n".
			'Destination: '.$updateData['c_destinations_all_display_venues_csv'][0]."\n".
			'Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view and approve this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0].'&action=view_tr'."\n\n".
								
			'------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
			######################################################
			## END: TRIGGER NOTIFICATION E-MAIL TO SPVSR ##
			######################################################
	
		}

	}

} else { // THERE WAS AN ERROR UPDATING THE LEAVE REQUEST
	
	$_SESSION['travel_request_submitted_staff'] = '2';

}
###############################################
## END: UPDATE THE LEAVE REQUEST ##
###############################################

header('Location: http://www.sedl.org/staff/sims/menu_travel.php');
exit;
?>



<?php


##############################################################################################################################
##############################################################################################################################
##############################################################################################################################

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

} elseif($action == 'edit_confirm_multi') { 

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

//$update -> AddDBParam('staff_ID',$_SESSION['staff_ID']);

if($_GET['self_approves_tr'] == 'Yes'){
$update -> AddDBParam('approval_status_tr','Approved');
$update -> AddDBParam('tr_approved_by',$_SESSION['user_ID']);
}else{
$update -> AddDBParam('approval_status_tr','Pending');
$update -> AddDBParam('approval_status','Pending');
$update -> AddDBParam('tr_approved_timestamp','');
}

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
	
	
	if($_GET['self_approves_tr'] == 'Yes'){ // REQUESTOR APPROVES OWN TRAVEL REQUESTS (MANAGER)
	
		##############################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
		##############################################################
		//$to = 'eric.waters@sedl.org';
		$to = $updateData['travel_admin_sims_user_ID'][0].'@sedl.org';
		$subject = stripslashes($updateData['staff_full_name'][0]).' has submitted a revised travel request';
		$message = 
		'Dear '.$updateData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0].','."\n\n".
		
		//'[E-mail was sent to: '.$updateData['travel_admin_sims_user_ID'][0].'@sedl.org]'."\n\n".
		
		'A revised travel request for '.stripslashes($updateData['staff_full_name'][0]).' has been submitted to SIMS.'."\n\n".
		
		'------------------------------------------------------------'."\n".
		' TRAVEL REQUEST DETAILS'."\n".
		'------------------------------------------------------------'."\n".
		'Description: '.stripslashes($updateData['purpose_of_travel_descr'][0])."\n".
		'Destinations: '.$updateData['c_destinations_all_display_venues_csv'][0]."\n".
		'Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
		'------------------------------------------------------------'."\n\n".
		
		'To process this travel request, click here: '."\n".
		'fmp7://198.214.140.248/CC_dms.fp7'."\n\n".
		
		'------------------------------------------------------------------------------------------------------------------'."\n".
		'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
		'------------------------------------------------------------------------------------------------------------------';
		
		$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: SIMS@sedl.org';
		
		mail($to, $subject, $message, $headers);
		############################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
		############################################################

	}else{ // REQUESTOR DOES NOT APPROVE OWN TRAVEL REQUESTS (NOT MANAGER)

		if($updateData['signer_ID_pba'][0] == $updateData['signer_ID_spvsr'][0]){ // PBA AND SPVSR ARE THE SAME PERSON
		
			########################################################
			## START: TRIGGER NOTIFICATION E-MAIL TO PBA ##
			########################################################
			//$to = 'eric.waters@sedl.org';
			$to = $updateData['signer_ID_pba'][0].'@sedl.org';
			$subject = stripslashes($updateData['staff::c_full_name_first_last'][0]).' has submitted a revised travel request (m)';
			$message = 
			'Dear '.$updateData['signer_ID_pba'][0].','."\n\n".
			
			'A revised travel request has been submitted by '.stripslashes($updateData['staff::c_full_name_first_last'][0]).' and requires your approval.'."\n\n".
			
			//'[E-mail was sent to: '.$updateData['signer_ID_pba'][0].'@sedl.org'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TRAVEL REQUEST DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			'Event: '.$updateData['event_name'][0]."\n".
			'Destinations: '.$updateData['c_destinations_all_display_venues_csv'][0]."\n".
			'Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view and approve this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0].'&action=view_tr'."\n\n".
								
			'------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
			######################################################
			## END: TRIGGER NOTIFICATION E-MAIL TO PBA ##
			######################################################
		
		}else{ // PBA AND SPVSR ARE NOT THE SAME PERSON
		
			########################################################
			## START: TRIGGER NOTIFICATION E-MAIL TO SPVSR ##
			########################################################
			//$to = 'eric.waters@sedl.org';
			$to = $updateData['signer_ID_spvsr'][0].'@sedl.org';
			$subject = stripslashes($updateData['staff::c_full_name_first_last'][0]).' has submitted a revised travel request (m)';
			$message = 
			'Dear '.$updateData['signer_ID_spvsr'][0].','."\n\n".
			
			'A revised travel request has been submitted by '.stripslashes($updateData['staff::c_full_name_first_last'][0]).' and requires your approval.'."\n\n".
			
			//'[E-mail was sent to: '.$updateData['signer_ID_spvsr'][0].'@sedl.org'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TRAVEL REQUEST DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			'Event: '.$updateData['event_name'][0]."\n".
			'Destinations: '.$updateData['c_destinations_all_display_venues_csv'][0]."\n".
			'Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view and approve this travel request, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0].'&action=view_tr'."\n\n".
								
			'------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
			######################################################
			## END: TRIGGER NOTIFICATION E-MAIL TO SPVSR ##
			######################################################
	
		}

	}
	
} else { // THERE WAS AN ERROR UPDATING THE LEAVE REQUEST
	
	$_SESSION['travel_request_submitted_staff'] = '2';

}
###############################################
## END: UPDATE THE LEAVE REQUEST ##
###############################################

header('Location: http://www.sedl.org/staff/sims/menu_travel.php');
exit;
?>


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

} elseif($action == 'expenses') { // STAFF MEMBER IS ENTERING POST-TRAVEL EXPENSES ON TRAVEL EXPENSE WORKSHEET

$travel_auth_ID = $_GET['travel_auth_ID'];

#################################################################
## START: FIND SELECTED TRAVEL REQUEST ##
#################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','travel_authorizations');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
//$search2 -> AddDBParam('-lop','or');

$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
//print_r ($searchResult2);
$recordData2 = current($searchResult2['data']);
//echo '<p>$recordData2[event_name]: '.$recordData2['event_name'][0];
###############################################################
## END: FIND SELECTED TRAVEL REQUEST ##
###############################################################

############################################################
## START: GRAB TRAVEL AUTH DAYS ##
############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','travel_auth_days');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('travel_auth_ID',$travel_auth_ID);

$search -> AddSortParam('travel_date','ascend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
##########################################################
## END: GRAB TRAVEL AUTH DAYS ##
##########################################################

$i=0;
?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">

	<?php $c=0; foreach($searchResult['data'] as $key => $searchData) { // CREATE DYNAMIC JAVASCRIPT TOTAL CALC FOR EACH TRAVEL DAYS ROW?>
	
		function startCalc<?php echo $c;?>(){
		  interval = setInterval("calc<?php echo $c;?>()",1);
		}
		function calc<?php echo $c;?>(){
		  one<?php echo $c;?> = document.travel_expense_submit.exp_fares<?php echo $c;?>.value;
		  two<?php echo $c;?> = document.travel_expense_submit.exp_tpl<?php echo $c;?>.value; 
		  twob<?php echo $c;?> = document.travel_expense_submit.exp_tpl_park<?php echo $c;?>.value; 
		  twoc<?php echo $c;?> = document.travel_expense_submit.exp_tpl_limo<?php echo $c;?>.value; 
		  four<?php echo $c;?> = document.travel_expense_submit.exp_mie<?php echo $c;?>.value; 
		  five<?php echo $c;?> = document.travel_expense_submit.exp_lodging<?php echo $c;?>.value; 
		  six<?php echo $c;?> = document.travel_expense_submit.exp_other<?php echo $c;?>.value; 
		  seven<?php echo $c;?> = document.travel_expense_submit.exp_otherb<?php echo $c;?>.value; 
		  eight<?php echo $c;?> = document.travel_expense_submit.exp_otherc<?php echo $c;?>.value; 
		  nine<?php echo $c;?> = document.travel_expense_submit.exp_otherd<?php echo $c;?>.value; 
		  document.travel_expense_submit.est_calc_sum<?php echo $c;?>.value = Math.round(((one<?php echo $c;?> * 1) + (two<?php echo $c;?> * 1) + (twob<?php echo $c;?> * 1) + (twoc<?php echo $c;?> * 1) + (four<?php echo $c;?> * 1) + (five<?php echo $c;?> * 1) + (six<?php echo $c;?> * 1) + (seven<?php echo $c;?> * 1) + (eight<?php echo $c;?> * 1) + (nine<?php echo $c;?> * 1))*100)/100;
		}
		function stopCalc<?php echo $c;?>(){
		  clearInterval(interval);
		}
	
	<?php $c++; } ?>

</script>

<script language="JavaScript">

	function submitDeny() { 
	
		// Don't allow re-submit if Travel Voucher already sent for approval.
		alert("This travel voucher has already been submitted by your travel admin. Contact your travel admin if you need to change your expense worksheet.");
		return false;	
	}			

function confirmSubmit() { 
	var answer2 = confirm ("Submit travel expenses to SIMS?")
	if (!answer2) {
	return false;
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests</h1><hr /></td></tr>
			
			<tr><td class="body" nowrap colspan="2"><p class="alert_small"><strong>Staff Member <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong> - Enter your travel expenses and submit to SIMS using the form below. | <a href="travel_receipts_info.php" target="_blank" title="Click for information about submitting your receipts for this trip.">Submitting receipts</a></p></td></tr>
			
			
			<tr><td colspan="2">
			<form name="travel_expense_submit" method="GET" <?php if($recordData2['TA_voucher_submitted_timestamp'][0] != ''){echo 'onsubmit="return submitDeny()"';}else{echo 'onsubmit="return confirmSubmit()"';}?>>
			<input type="hidden" name="action" value="expenses_submit">
			<input type="hidden" name="travel_auth_ID" value="<?php echo $travel_auth_ID;?>">
			<input type="hidden" name="row_ID" value="<?php echo $recordData2['c_row_ID_cwp'][0];?>">
			

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $recordData2['c_event_venue_city_state'][0];?> - <?php echo $recordData2['leave_date_requested'][0];?> to <?php echo $recordData2['return_date_requested'][0];?></strong></td><td class="body" align="right" nowrap>&nbsp;</td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL EXPENSE WORKSHEET</strong></td><td align="right"><a href="menu_travel.php">Cancel</a></td></tr>

						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr style="background-color:#000000;color:#ffffff"><td><strong>STEP 1</strong>: <em>Enter your ACTUAL depart and return times.</em></td></tr>
								<tr><td>Enter actual departure time on <strong><?php echo $recordData2['leave_date_requested'][0];?></strong>: <input type="text" size="10" name="actual_travel_depart_time" value="<?php echo $recordData2['leave_time_requested'][0];?>"> | Enter actual return time on <strong><?php echo $recordData2['return_date_requested'][0];?></strong>: <input type="text" size="10" name="actual_travel_return_time" value="<?php echo $recordData2['return_time_requested'][0];?>"></td></tr>
							</table>	
						
						</td></tr>
						
						<tr><td colspan="2">
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr style="background-color:#000000;color:#ffffff"><td colspan="8"><strong>STEP 2</strong>: <em>Enter your expenses for each travel day in the space provided below (enter numbers only unless field is yellow).</em></td></tr>
								

								<tr style="background-color:#ebebeb;vertical-align: text-top">
								<td>Date</td><td>Fares*</td><td>Taxi (T)*<br>Parking (P)<br>Limo (L)</td><td>Mileage<br><span class="tiny" style="color:#ff0000">[ENTER #MILES]</span></td><td>M&IE **<br><span class="tiny" style="color:#ff0000">[CONUS rate in red]</span></td><td>Lodging*<br><span class="tiny" style="color:#ff0000">[CONUS rate in red]</span></td><td>Other*<br>Type/Amount</td><td>TOTAL</td>
								</tr>								

								<?php $c=1; foreach($searchResult['data'] as $key => $searchData) { ?>
								
								<tr style="vertical-align:text-top">
								<td><?php echo $searchData['travel_date'][0]. ' <span class="tiny">('.strtoupper($searchData['c_travel_day_name'][0]).')</span>';?></td>
								<td><input type="text" name="exp_fares<?php echo $i;?>" id="exp_fares<?php echo $i;?>" size="6" value="<?php echo $searchData['voucher_fares'][0];?>" onFocus="startCalc<?php echo $i;?>();" onBlur="stopCalc<?php echo $i;?>();" style="text-align: right"></td>
								<td>
									<input type="text" name="exp_tpl<?php echo $i;?>" id="exp_tpl<?php echo $i;?>" size="6" value="<?php echo $searchData['voucher_tpl'][0];?>" onFocus="startCalc<?php echo $i;?>();" onBlur="stopCalc<?php echo $i;?>();" style="text-align: right"> <strong>T</strong><br>
									<input type="text" name="exp_tpl_park<?php echo $i;?>" id="exp_tpl_park<?php echo $i;?>" size="6" value="<?php echo $searchData['voucher_tpl_park'][0];?>" onFocus="startCalc<?php echo $i;?>();" onBlur="stopCalc<?php echo $i;?>();" style="text-align: right"> <strong>P</strong><br>
									<input type="text" name="exp_tpl_limo<?php echo $i;?>" id="exp_tpl_limo<?php echo $i;?>" size="6" value="<?php echo $searchData['voucher_tpl_limo'][0];?>" onFocus="startCalc<?php echo $i;?>();" onBlur="stopCalc<?php echo $i;?>();" style="text-align: right"> <strong>L</strong>
								</td>
								<td><input type="text" name="exp_mileage_num<?php echo $i;?>" id="exp_mileage_num<?php echo $i;?>" size="4" value="<?php echo $searchData['voucher_mileage_num'][0];?>" style="text-align: center"> <span class="tiny">MILES</span></td>
								<td><input type="text" name="exp_mie<?php echo $i;?>" id="exp_mie<?php echo $i;?>" size="6" value="<?php echo $searchData['voucher_mie'][0];?>" onFocus="startCalc<?php echo $i;?>();" onBlur="stopCalc<?php echo $i;?>();" style="text-align: right"> <span class="tiny" style="color:#ff0000">[$<?php echo $searchData['mie'][0];?>]</span></td>
								<td><input type="text" name="exp_lodging<?php echo $i;?>" id="exp_lodging<?php echo $i;?>" size="6" value="<?php echo $searchData['voucher_lodging'][0];?>" onFocus="startCalc<?php echo $i;?>();" onBlur="stopCalc<?php echo $i;?>();" style="text-align: right"> <span class="tiny" style="color:#ff0000">[$<?php echo $searchData['lodging'][0];?>]</span></td>
								<td>
									<input type="text" name="exp_other_type<?php echo $i;?>" id="exp_other_type<?php echo $i;?>" size="14" value="<?php echo $searchData['voucher_other_1_type'][0];?>" style="background-color:#fff6bf"><input type="text" name="exp_other<?php echo $i;?>" id="exp_other<?php echo $i;?>" size="6" value="<?php echo $searchData['voucher_other_1'][0];?>" onFocus="startCalc<?php echo $i;?>();" onBlur="stopCalc<?php echo $i;?>();" style="text-align: right"><br>
									<input type="text" name="exp_other_typeb<?php echo $i;?>" id="exp_other_typeb<?php echo $i;?>" size="14" value="<?php echo $searchData['voucher_other_2_type'][0];?>" style="background-color:#fff6bf"><input type="text" name="exp_otherb<?php echo $i;?>" id="exp_otherb<?php echo $i;?>" size="6" value="<?php echo $searchData['voucher_other_2'][0];?>" onFocus="startCalc<?php echo $i;?>();" onBlur="stopCalc<?php echo $i;?>();" style="text-align: right"><br>
									<input type="text" name="exp_other_typec<?php echo $i;?>" id="exp_other_typec<?php echo $i;?>" size="14" value="<?php echo $searchData['voucher_other_3_type'][0];?>" style="background-color:#fff6bf"><input type="text" name="exp_otherc<?php echo $i;?>" id="exp_otherc<?php echo $i;?>" size="6" value="<?php echo $searchData['voucher_other_3'][0];?>" onFocus="startCalc<?php echo $i;?>();" onBlur="stopCalc<?php echo $i;?>();" style="text-align: right"><br>
									<input type="text" name="exp_other_typed<?php echo $i;?>" id="exp_other_typed<?php echo $i;?>" size="14" value="<?php echo $searchData['voucher_other_4_type'][0];?>" style="background-color:#fff6bf"><input type="text" name="exp_otherd<?php echo $i;?>" id="exp_otherd<?php echo $i;?>" size="6" value="<?php echo $searchData['voucher_other_4'][0];?>" onFocus="startCalc<?php echo $i;?>();" onBlur="stopCalc<?php echo $i;?>();" style="text-align: right"><br>
								</td>
								<td><input type="text" name="est_calc_sum<?php echo $i;?>" id="est_calc_sum<?php echo $i;?>" size="6" DISABLED style="text-align: right"></td>
								</tr>
								
								<?php $i++; } ?>

								<tr style="background-color:#ebebeb"><td colspan="5" style="border-right-width:0px"><span class="tiny" style="color:#ff0000">CONUS Summary: Breakfast (<?php echo $searchData['mie_breakfast'][0];?>); Lunch (<?php echo $searchData['mie_lunch'][0];?>); Dinner (<?php echo $searchData['mie_dinner'][0];?>); Incidentals (<?php echo $searchData['mie_incidentals'][0];?>)</span></td><td colspan="3" align="right" style="border-left-width:0px"><input type="reset" name="reset" value="Reset Form"><input type="submit" name="submit" value="Submit Travel Expenses"></td></tr>
								


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

} elseif($action == 'expenses_review') { // STAFF MEMBER IS APPROVING POST-TRAVEL FINAL EXPENSES ON TRAVEL VOUCHER

$signer = $_GET['signer'];
if((!isset($_SESSION['user_ID'])) && ($signer != '')){
$_SESSION['user_ID'] = $signer;
}
$travel_auth_ID = $_GET['travel_auth_ID'];
$travel_auth_row_ID = $_GET['travel_auth_row_ID'];

$sign = $_GET['sign'];

#################################################################
## START: FIND PER DIEM RATES FOR EACH TRAVEL DAY ##
#################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','travel_auth_days');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
//$search -> AddDBParam('-lop','or');
$search2 -> AddSortParam('travel_date','ascend');


$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
//print_r ($searchResult2);
$recordData2 = current($searchResult2['data']);
###############################################################
## END: FIND PER DIEM RATES FOR EACH TRAVEL DAY ##
###############################################################

if($sign == '1'){ // STAFF MEMBER SIGNED THE TRAVEL VOUCHER  

			########################################################################
			## START: UPDATE TRAVEL AUTHORIZATION - VOUCHER APPROVED BY STAFF ##
			########################################################################
			$update2 = new FX($serverIP,$webCompanionPort);
			$update2 -> SetDBData('SIMS_2.fp7','travel_authorizations');
			$update2 -> SetDBPassword($webPW,$webUN);
			$update2 -> AddDBParam('-recid',$travel_auth_row_ID);
			$update2 -> AddDBParam('staff_vchr_approval_status','1');
			
			$updateResult2 = $update2 -> FMEdit();
			$updateData2 = current($updateResult2['data']);
			//echo  '<p>errorCode: '.$updateResult2['errorCode'];
			//echo  '<p>foundCount: '.$updateResult2['foundCount'];
			if($updateResult2['errorCode'] !== '0'){
			echo 'Session Error ('.$updateResult2['errorCode'].'): Please re-login to the <a href="http://www.sedl.org/staff">SEDL Intranet</a>, then re-click the link from your e-mail.';
			exit;
			}
			######################################################################
			## END: UPDATE TRAVEL AUTHORIZATION - APPROVED BY STAFF ##
			######################################################################
	
			###############################################
			## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
			###############################################
			$newrecord = new FX($serverIP,$webCompanionPort);
			$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
			$newrecord -> SetDBPassword($webPW,$webUN);
			$newrecord -> AddDBParam('user',$signer);
			$newrecord -> AddDBParam('action','SIGN_TV_ST');
			$newrecord -> AddDBParam('travel_auth_ID',$updateData2['travel_auth_ID'][0]);
			$newrecordResult = $newrecord -> FMNew();
			//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
			//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
			#############################################
			## END: SAVE ACTION TO TRAVEL APPROVAL LOG ##
			#############################################
			
			###############################################
			## START: SAVE ACTION TO SIMS AUDIT LOG ##
			###############################################
			$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
			
			$newrecord = new FX($serverIP,$webCompanionPort);
			$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
			$newrecord -> SetDBPassword($webPW,$webUN);
			$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
			$newrecord -> AddDBParam('action','SIGN_TRAVEL_VCHR_STAFF');
			$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
			$newrecord -> AddDBParam('object_ID',$updateData2['travel_auth_ID'][0]);
			$newrecord -> AddDBParam('affected_row_ID',$updateData2['c_row_ID_cwp'][0]);
			$newrecord -> AddDBParam('ip_address',$ip);
			$newrecordResult = $newrecord -> FMNew();
			//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
			//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
			###############################################
			## END: SAVE ACTION TO SIMS AUDIT LOG ##
			###############################################

			####################################################
			## START: FIND ALL APPROVAL ROLES FOR THIS TA ##
			####################################################
			$search4 = new FX($serverIP,$webCompanionPort);
			$search4 -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
			$search4 -> SetDBPassword($webPW,$webUN);
			$search4 -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
			$search4 -> AddDBParam('signs_voucher','yes');
			$search4 -> AddSortParam('signer_role','ascend');
			
			$searchResult4 = $search4 -> FMFind();
			
			//echo '<p>$searchResult4[errorCode]: '.$searchResult4['errorCode'];
			//echo '<p>$searchResult4[foundCount]: '.$searchResult4['foundCount'];
			//print_r ($searchResult4);
			$recordData4 = current($searchResult4['data']);
			##################################################
			## END: FIND ALL APPROVAL ROLES FOR THIS TA ##
			##################################################
			
		##########################################################################################
		## START: FORWARD NOTIFICATION APPROVED TO SEDL AS IF ALL VOUCHER SIGNERS HAVE SIGNED ##
		##########################################################################################
		if($recordData4['c_all_signers_status_vchr'][0] == '1'){ // ALL VOUCHER SIGNERS HAVE SIGNED THE DOCUMENT

			########################################################################
			## START: UPDATE TRAVEL AUTHORIZATION - VOUCHER APPROVED ##
			########################################################################
			$update2 = new FX($serverIP,$webCompanionPort);
			$update2 -> SetDBData('SIMS_2.fp7','travel_authorizations');
			$update2 -> SetDBPassword($webPW,$webUN);
			$update2 -> AddDBParam('-recid',$recordData4['travel_authorizations::c_row_ID_cwp'][0]);
			$update2 -> AddDBParam('approval_status_vchr','Approved');
			
			$updateResult2 = $update2 -> FMEdit();
			$updateData2 = current($updateResult2['data']);
			######################################################################
			## END: UPDATE TRAVEL AUTHORIZATION - APPROVED ##
			######################################################################
	
			###############################################
			## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
			###############################################
			$newrecord = new FX($serverIP,$webCompanionPort);
			$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
			$newrecord -> SetDBPassword($webPW,$webUN);
			$newrecord -> AddDBParam('user','SIMS');
			$newrecord -> AddDBParam('action','APPROVE_TV');
			$newrecord -> AddDBParam('travel_auth_ID',$updateData2['travel_auth_ID'][0]);
			$newrecordResult = $newrecord -> FMNew();
			//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
			//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
			#############################################
			## END: SAVE ACTION TO TRAVEL APPROVAL LOG ##
			#############################################
			
			$destination =	stripslashes($updateData2['event_venue_city'][0]).', '.$updateData2['event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION
	
			#############################################################
			## START: TRIGGER NOTIFICATION E-MAIL TO AA - TV APPROVED ##
			#############################################################
			//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
			$to = 'claizure@sedl.org';
			$subject = 'A travel voucher for '.stripslashes($updateData2['staff_full_name'][0]).' has been approved by budget authorities.';
			$message = 
			'Travel Admin,'."\n\n".
			
			//'[E-mail was sent to: traveladmin@sedl.org]'."\n\n".
	
			'A travel voucher (TV) for staff member '.stripslashes($updateData2['staff_full_name'][0]).' has been approved by all relevent budget authorities.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TRAVEL DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			' TA ID: '.$updateData2['travel_auth_ID'][0]."\n".
			' Event: '.stripslashes($updateData2['event_name'][0])."\n".
			' Destination: '.$destination."\n".
			' Date(s) of Travel: '.$updateData2['leave_date_requested'][0].' to '.$updateData2['return_date_requested'][0]."\n".
			' Voucher Amount Due: '.$updateData2['c_voucher_total_due'][0]."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view and process this travel voucher, click here: '."\n".
			'fmp7://SIMS_2.fp7'."\n\n".
								
			'------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
				
			###########################################################
			## END: TRIGGER NOTIFICATION E-MAIL TO AA - TV APPROVED ##
			###########################################################
		

		}
		
		######################################################################################
		## END: FORWARD NOTIFICATION APPROVED TO SEDL AS IF ALL VOUCHER SIGNERS HAVE SIGNED ##
		######################################################################################


		foreach($searchResult4['data'] as $key => $searchData4) { // LOOP THROUGH APPROVAL ROLES AND SEND E-MAIL NOTIFICATION TO EACH VOUCHER SIGNER
		

			if(($searchData4['signer_ID'][0] == $signer)&&($searchData4['signs_voucher'][0] == 'yes')){ // SIGN AS BA IF STAFF MEMBER IS ALSO A BA SIGNER
			$trigger = rand();
			########################################################################
			## START: UPDATE SIGNER STATUS FOR ALL APPROVAL ROLES FOR THIS SIGNER ##
			########################################################################
			$update = new FX($serverIP,$webCompanionPort);
			$update -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
			$update -> SetDBPassword($webPW,$webUN);
			$update -> AddDBParam('-recid',$searchData4['c_cwp_row_ID'][0]);
			$update -> AddDBParam('signer_status_vchr','1');
			$update -> AddDBParam('signer_timestamp_trigger_vchr',$trigger);
			
			$updateResult = $update -> FMEdit();
			$updateData = current($updateResult['data']);
			######################################################################
			## END: UPDATE SIGNER STATUS FOR ALL APPROVAL ROLES FOR THIS SIGNER ##
			######################################################################

			###############################################
			## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
			###############################################
			$newrecord = new FX($serverIP,$webCompanionPort);
			$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
			$newrecord -> SetDBPassword($webPW,$webUN);
			$newrecord -> AddDBParam('user',$signer);
			$newrecord -> AddDBParam('action','SIGN_TV');
			$newrecord -> AddDBParam('travel_auth_ID',$updateData['travel_auth_ID'][0]);
			$newrecordResult = $newrecord -> FMNew();
			//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
			//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
			#############################################
			## END: SAVE ACTION TO TRAVEL APPROVAL LOG ##
			#############################################

			###############################################
			## START: SAVE ACTION TO SIMS AUDIT LOG ##
			###############################################
			$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
			
			$newrecord = new FX($serverIP,$webCompanionPort);
			$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
			$newrecord -> SetDBPassword($webPW,$webUN);
			$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
			$newrecord -> AddDBParam('action','APPROVE_TRAVEL_VCHR_BA');
			$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
			$newrecord -> AddDBParam('object_ID',$updateData['travel_auth_ID'][0]);
			$newrecord -> AddDBParam('affected_row_ID',$updateData['c_row_ID_cwp'][0]);
			$newrecord -> AddDBParam('ip_address',$ip);
			$newrecordResult = $newrecord -> FMNew();
			//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
			//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
			###############################################
			## END: SAVE ACTION TO SIMS AUDIT LOG ##
			###############################################
			}

			
			
			$destination =	stripslashes($updateData2['event_venue_city'][0]).', '.$updateData2['event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION
	
			if(($searchData4['signer_ID'][0] !== $signer)&&($searchData4['signs_voucher'][0] == 'yes')){ // SEND EMAIL TO ALL BA VOUCHER SIGNERS EXCEPT THE CURRENT SIGNER
			#######################################################################
			## START: TRIGGER NOTIFICATION E-MAIL TO BAs - TV REQUIRES SIGNATURE ##
			#######################################################################
			//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
			$to = $searchData4['signer_ID'][0].'@sedl.org';
			$subject = 'A travel voucher for '.stripslashes($updateData2['staff_full_name'][0]).' has been submitted to SIMS and requires your approval.';
			$message = 
			'Budget Authority,'."\n\n".
			
			//'[E-mail was sent to: '.$searchData4['signer_ID'][0].'@sedl.org]'."\n\n".
	
			'A travel voucher (TV) for staff member '.stripslashes($updateData2['staff_full_name'][0]).' has been submitted to SIMS and requires your approval.'."\n\n".
			
			'------------------------------------------------------------'."\n".
			' TRAVEL DETAILS'."\n".
			'------------------------------------------------------------'."\n".
			' TA ID: '.$updateData2['travel_auth_ID'][0]."\n".
			' Event: '.stripslashes($updateData2['event_name'][0])."\n".
			' Destination: '.$destination."\n".
			' Date(s) of Travel: '.$updateData2['leave_date_requested'][0].' to '.$updateData2['return_date_requested'][0]."\n".
			' Voucher Amount Due: '.$updateData2['c_voucher_total_due'][0]."\n".
			'------------------------------------------------------------'."\n\n".
			
			'To view and approve this travel voucher, click here: '."\n".
			'http://www.sedl.org/staff/sims/travel_admin.php?action=apprv&travel_auth_ID='.$updateData2['travel_auth_ID'][0]."\n\n".
								
			'------------------------------------------------------------------------------------------------------------------'."\n".
			'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
			'------------------------------------------------------------------------------------------------------------------';
			
			$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
			
			mail($to, $subject, $message, $headers);
				
			#####################################################################
			## END: TRIGGER NOTIFICATION E-MAIL TO BAs - TV REQUIRES SIGNATURE ##
			#####################################################################
			}
		
		}


		


}


#####################################
## START: FIND TRAVEL AUTH SIGNERS ##
#####################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
$search3 -> AddDBParam('signs_voucher','yes');
//$search3 -> AddDBParam('-lop','or');
$search3 -> AddSortParam('signer_role','ascend');

$searchResult3 = $search3 -> FMFind();

//echo '<p>$searchResult3[errorCode]: '.$searchResult3['errorCode'];
//echo '<p>$searchResult3[foundCount]: '.$searchResult3['foundCount'];
$recordData3 = current($searchResult3['data']);
###################################
## END: FIND TRAVEL AUTH SIGNERS ##
###################################
$current_signer = '';
#############################################
## START: GET CURRENT USER'S SIGNER STATUS ##
#############################################
foreach($searchResult3['data'] as $key => $searchData3) {

	if($searchData3['signer_ID'][0] == $_SESSION['user_ID']){
	$current_user_status = $searchData3['signer_status_vchr'][0];
	}

}
#############################################
## END: GET CURRENT USER'S SIGNER STATUS ##
#############################################

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
//$travel_admin = $recordData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0];
//$request_owner_email = $recordData['staff::email'][0];
//$google_event_name = str_replace(" ","+",$recordData['event_name'][0]);
//$google_event_venue = str_replace(" ","+",$recordData['event_venue'][0]);
//$google_preferred_hotel = str_replace(" ","+",$recordData['preferred_hotel_name'][0]);
//$request_owner_sims_ID = $recordData['staff_sims_ID'][0];
$immediate_supervisor = $recordData['staff::immediate_supervisor_sims_user_ID'][0];
###############################################################
## END: FIND SELECTED TRAVEL REQUEST ##
###############################################################

#################################################################
## START: FIND USER LOG ENTRIES RELATED TO THIS TA ##
#################################################################
$search6 = new FX($serverIP,$webCompanionPort);
$search6 -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
$search6 -> SetDBPassword($webPW,$webUN);
$search6 -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
//$search6 -> AddDBParam('-lop','or');

$search6 -> AddSortParam('creation_timestamp','ascend');


$searchResult6 = $search6 -> FMFind();

//echo '<p>$searchResult6[errorCode]: '.$searchResult6['errorCode'];
//echo '<p>$searchResult6[foundCount]: '.$searchResult6['foundCount'];
//print_r ($searchResult6);
$recordData6 = current($searchResult6['data']);
###############################################################
## END: FIND USER LOG ENTRIES RELATED TO THIS TA ##
###############################################################

//echo '$_SESSION[user_ID]: '.$_SESSION['user_ID'];
?>


<html>
<head>
<title>SIMS: Travel Authorizations</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">

function preventSign() { 
	alert ("This signature box is reserved for another budget authority. To sign this travel voucher, click the signature space with your ID.")
	return false;
}

function confirmSign() { 
	var answer = confirm ("Approve this travel voucher?")
	if (!answer) {
	return false;
	}
}

function wrongSigner() { 
	alert ("This signature box is reserved for another budget authority. To sign this travel voucher, click the signature space with your ID.")
	return false;
}

</script>


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table cellpadding="0" cellspacing="0" border="0" width="930">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Authorizations</h1><hr /></td></tr>
			

<?php if(($current_user_status == '1') || ($sign == '1')){ ?>

			<tr><td colspan="2"><p class="alert_small"><b>Staff Member (<?php echo $_SESSION['user_ID'];?>)</b>: You have successfully approved this travel voucher (TV). <img src="/staff/sims/images/green_check.png"> | <a href="menu_travel.php">Close TV</a></p></td></tr>

<?php }else{ ?>

			<tr><td colspan="2"><p class="alert_small"><b>Staff Member (<?php echo $_SESSION['user_ID'];?>)</b>: To approve this travel voucher (TV), click the appropriate signature box below. | <a href="menu_travel.php">Close TV</a></p></td></tr>

<?php } ?>
			
			<tr><td colspan="2">
			<form name="travel_auth_approve">
			<input type="hidden" name="travel_auth_ID" value="<?php echo $recordData['travel_auth_ID'][0];?>">
			<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
			<input type="hidden" name="action" value="approve_ta_sign">




						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $recordData['staff::name_timesheet'][0];?> (<?php echo $recordData['staff::primary_SEDL_workgroup'][0];?>)</strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">TV Status: <strong><?php if($recordData['approval_status_vchr'][0] == 'Pending'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['approval_status_vchr'][0]).'</span>';?></strong></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL VOUCHER</strong></td><td align="right">ID: <?php echo $recordData['travel_auth_ID'][0];?> <?php if($recordData['approval_status_vchr'][0] == 'Approved'){ ?> | <img src="/staff/sims/images/padlock.jpg" border="0" title="This travel voucher is locked."> <?php }?></td></tr>
						
						<tr><td colspan="2">
						





<!-- START PR TABLE -->
							<table cellpadding="7" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
							
<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    TRAVEL DETAILS           ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<tr><td class="body" style="vertical-align:text-top;width:100%"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>TRAVEL DETAILS</strong></div><br>
								<div class="tiny" style="border:2px solid #509049;padding:8px;background-color:#ffffff;font-weight:bold;color:#477643"><img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> Travel request approved <?php echo $recordData['tr_approved_timestamp'][0];?> by <?php echo $recordData['tr_approved_by'][0];?></div>
										<table width="100%" style="margin-top:10px"><tr>
										<td width="100%" valign="top" style="border:0px">

												<table >
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Name:</strong></td><td style="border:0px;padding:6px"><?php echo $recordData['staff::c_full_name_first_last'][0];?></td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Title/Unit:</strong></td><td style="border:0px;padding:6px"><?php echo $recordData['staff::job_title'][0];?> (<?php echo $recordData['staff::primary_SEDL_workgroup'][0];?>)</td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Destination:</strong></td><td style="border:0px;padding:6px"><?php if($recordData['multi_dest'][0] == 'yes'){echo $recordData['c_multi_destinations_all_display_venues'][0];}else{echo $recordData['event_venue_city'][0];?>, <?php echo $recordData['event_venue_state'][0];}?></td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Period of Travel:</strong></td><td style="border:0px;padding:6px"><?php echo $recordData['leave_date_requested'][0];?> (<?php echo $recordData['leave_time_requested'][0];?>) to <?php echo $recordData['return_date_requested'][0];?> (<?php echo $recordData['return_time_requested'][0];?>)</td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Purpose of Travel:</strong></td><td style="border:0px;padding:6px"><?php echo stripslashes($recordData['c_purpose_of_travel_csv'][0]);?></td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Description:</strong></td><td style="border:0px;padding:6px"><?php echo stripslashes($recordData['purpose_of_travel_descr'][0]);?></td></tr>
												</table>														
												
												
										</td>
										
										</tr>
										</table>
										

								</td>
								</tr>


<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    VOUCHER DETAILS          ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<tr><td style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>VOUCHER EXPENSE DETAILS</strong></div><br>

								
									<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
										<tr style="background-color:#ebebeb;vertical-align: text-top">
										<td>Date</td><td>Fares</td><td>Taxi (T)<br>Parking (P)<br>Limo (L)</td><td>Mileage (.555/mi)<br>#Miles/Amount</td><td>M&IE<br><span class="tiny" style="color:#ff0000">[CONUS rate in red]</span></td><td>Lodging<br><span class="tiny" style="color:#ff0000">[CONUS rate in red]</span></td><td>Other<br>Type/Amount</td><td style="border-left:3px solid #cccccc;text-align:right">TOTAL</td>
										</tr>								
		
										<?php $c=1; foreach($searchResult2['data'] as $key => $searchData) { ?>
										<tr style="vertical-align:text-top">
										
										<td><?php echo $searchData['travel_date'][0]. ' <span class="tiny">('.strtoupper($searchData['c_travel_day_name'][0]).')</span>';?></td>
										<td style="text-align: right"><?php if($searchData['voucher_fares'][0] != ''){echo '$'.number_format($searchData['voucher_fares'][0],2);}?></td>
										<td>
										<?php if($searchData['voucher_tpl'][0] != ''){echo '$'.number_format($searchData['voucher_tpl'][0],2).' <strong>T</strong><br>';}?>
										<?php if($searchData['voucher_tpl_park'][0] != ''){echo '$'.number_format($searchData['voucher_tpl_park'][0],2).' <strong>P</strong><br>';}?>
										<?php if($searchData['voucher_tpl_limo'][0] != ''){echo '$'.number_format($searchData['voucher_tpl_limo'][0],2).' <strong>L</strong><br>';}?>
										</td>
										<td><?php if($searchData['voucher_mileage_num'][0] != ''){echo $searchData['voucher_mileage_num'][0];?> mi / $<?php echo number_format($searchData['voucher_mileage_amt'][0],2);}?></td>
										<td style="text-align: right"><?php if($searchData['voucher_mie'][0] != ''){echo '$'.number_format($searchData['voucher_mie'][0],2);}?> <span class="tiny" style="color:#ff0000">[$<?php echo $searchData['mie'][0];?>]</span></td>
										<td style="text-align: right"><?php if($searchData['voucher_lodging'][0] != ''){echo '$'.number_format($searchData['voucher_lodging'][0],2);}?> <span class="tiny" style="color:#ff0000">[$<?php echo $searchData['lodging'][0];?>]</span></td>
										<td>
										<table style="width:100%;border:0px;margin:0px;padding:6px">
										<?php if($searchData['voucher_other_1_type'][0] != ''){?><tr><td style="width:100%;padding:0px;border:0px;margin:0px"><?php echo $searchData['voucher_other_1_type'][0];?></td><td style="text-align:right;padding:0px;border:0px;margin:0px">$<?php echo number_format($searchData['voucher_other_1'][0],2);?></td></tr><?php }?>
										<?php if($searchData['voucher_other_2_type'][0] != ''){?><tr><td style="width:100%;padding:0px;border:0px;margin:0px"><?php echo $searchData['voucher_other_2_type'][0];?></td><td style="text-align:right;padding:0px;border:0px;margin:0px">$<?php echo number_format($searchData['voucher_other_2'][0],2);?></td></tr><?php }?>
										<?php if($searchData['voucher_other_3_type'][0] != ''){?><tr><td style="width:100%;padding:0px;border:0px;margin:0px"><?php echo $searchData['voucher_other_3_type'][0];?></td><td style="text-align:right;padding:0px;border:0px;margin:0px">$<?php echo number_format($searchData['voucher_other_3'][0],2);?></td></tr><?php }?>
										<?php if($searchData['voucher_other_4_type'][0] != ''){?><tr><td style="width:100%;padding:0px;border:0px;margin:0px"><?php echo $searchData['voucher_other_4_type'][0];?></td><td style="text-align:right;padding:0px;border:0px;margin:0px">$<?php echo number_format($searchData['voucher_other_4'][0],2);?></td></tr><?php }?>
										</table>
										</td>
										<td style="text-align: right;border-left:3px solid #cccccc">$<?php echo number_format($searchData['c_voucher_day_total'][0],2);?></td>
										
										</tr>
										<?php $i++; } ?>
										
										<tr><td rowspan="4" style="vertical-align:text-top;border-top:3px solid #cccccc" colspan="6"><strong>Remarks:</strong> <?php echo $recordData['voucher_remarks'][0];?></td>
										<td style="text-align:right;border-top:3px solid #cccccc"><strong>Total Expenses</strong></td><td style="text-align:right;border-top:3px solid #cccccc">$<?php echo number_format($recordData['c_total_travel_expenses_all'][0],2);?></td>
										</tr>
		
										<tr>
										<td style="text-align:right"><strong>Less Advance</strong></td><td style="text-align:right"><?php if($recordData['c_travel_advance_itemized_total_amt'][0] != ''){echo '$'.number_format($recordData['c_travel_advance_itemized_total_amt'][0],2);}?></td>
										</tr>
										
										<tr>
										<td style="text-align:right"><strong>Less Prepays</strong></td><td style="text-align:right"><?php if($recordData['prepaid_amount'][0] != ''){echo '$'.number_format($recordData['prepaid_amount'][0],2);}?></td>
										</tr>
		
										<tr>
										<td style="text-align:right"><strong>Total Due</strong></td><td style="text-align:right">$<?php echo number_format($recordData['c_voucher_total_due'][0],2);?></td>
										</tr>
		
									</table>
		
								
								
								
								</td></tr>
								

<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    APPROVAL SIGNATURES      ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<tr class="body"><td nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>SIGNATURES</strong></div><br>
													
<?php if($recordData['TA_voucher_submitted_timestamp'][0] != ''){ ?>

													
														<table class="sims" cellspacing="1" cellpadding="10" border="1" width="100%">
		
		

														<tr class="body" valign="top"><td><div style="border:1px dotted #000000;background-color:#bddbee;padding:5px" class="tiny">STAFF MEMBER APPROVAL</div><br>
														
															<table>

															<tr><td style="border-width:0px;padding:0px;margin:0px">
															
																	<td align="center" valign="bottom" style="padding:5px">
																	<?php if($recordData['staff_vchr_approval_status'][0] == '1'){ // STAFF APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['staff_sims_ID'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="travel.php?action=expenses_review&travel_auth_ID=<?php echo $travel_auth_ID;?>&travel_auth_row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&sign=1&signer=<?php echo $recordData['staff_sims_ID'][0];?>" <?php if(($_SESSION['user_ID'] != $recordData['staff_sims_ID'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $recordData['staff_sims_ID'][0];?></a><?php }?><p>
																	<span class="tiny"><em>Staff Member</em><br><?php if($recordData['staff_vchr_approval_timestamp'][0] != ''){ ?><font color="999999">[<?php echo $recordData['staff_vchr_approval_timestamp'][0];?>]</font><?php } ?></span>
																	</td>
															
			
															</tr>
															</table>

														</td></tr>										


														<tr class="body" valign="top"><td><div style="border:1px dotted #000000;background-color:#bddbee;padding:5px" class="tiny">BA APPROVAL</div><br>
														
															<table>

															<tr><td style="border-width:0px;padding:0px;margin:0px">
															<?php foreach($searchResult3['data'] as $key => $searchData3){ ?>
															
																	<td align="center" valign="bottom" style="padding:5px">
																	<?php if($searchData3['signer_status_vchr'][0] == '1'){ // BA APPROVED ?><img src="/staff/sims/signatures/<?php echo $searchData3['signer_ID'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="travel_admin.php?action=apprv&travel_auth_ID=<?php echo $travel_auth_ID;?>&travel_auth_row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&sign=1&signer=<?php echo $searchData3['signer_ID'][0];?>" <?php if(($_SESSION['user_ID'] != $searchData3['signer_ID'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $searchData3['signer_ID'][0];?></a><?php }?><p>
																	<span class="tiny"><em><?php echo $searchData3['signer_role'][0];?></em><br><?php if($searchData3['signer_timestamp_vchr'][0] != ''){ ?><font color="999999">[<?php echo $searchData3['signer_timestamp_vchr'][0];?>]</font><?php } ?></span>
																	</td>
															
															
															<?php } ?>
			
															</tr>
															</table>
		
														</td></tr>										
		
													
<?php }else{ ?>								

													<tr><td class="body" style="vertical-align:text-top" colspan="2">This TV has not been submitted.</td></tr>

<?php } ?>			

								
								
							</table>


<!-- END PR TABLE -->











						

						</td></tr>


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

} elseif($action == 'expenses_submit') { // STAFF MEMBER SUBMITTED TRAVEL EXPENSE WORKSHEET

$travel_auth_ID = $_GET['travel_auth_ID'];
$row_ID = $_GET['row_ID'];

#################################################
## START: UPDATE ACTUAL TRAVEL TIMES ##
#################################################
$update2 = new FX($serverIP,$webCompanionPort);
$update2 -> SetDBData('SIMS_2.fp7','travel_authorizations');
$update2 -> SetDBPassword($webPW,$webUN);
$update2 -> AddDBParam('-recid',$row_ID);

$update2 -> AddDBParam('leave_time_requested',$_GET['actual_travel_depart_time']);
$update2 -> AddDBParam('return_time_requested',$_GET['actual_travel_return_time']);

$updateResult2 = $update2 -> FMEdit();

//echo  '<p>errorCode: '.$updateResult2['errorCode'];
//echo  '<p>foundCount: '.$updateResult2['foundCount'];
$updateData2 = current($updateResult2['data']);
#################################################
## END: UPDATE ACTUAL TRAVEL TIMES ##
#################################################

############################################################
## START: GRAB TRAVEL AUTH DAYS ##
############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','travel_auth_days');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('travel_auth_ID',$travel_auth_ID);

$search -> AddSortParam('travel_date','ascend');

$searchResult = $search -> FMFind();

//echo '<p>ta: '.$searchResult['errorCode'];
//echo '<p>ta: '.$searchResult['foundCount'];
//$recordData = current($searchResult['data']);
##########################################################
## END: GRAB TRAVEL AUTH DAYS ##
##########################################################

$i = 0; // RESET THE COUNTER

foreach($searchResult['data'] as $key => $searchData) { // LOOP THROUGH TRAVEL DAYS, ASSEMBLE ARRAYS OF VOUCHER ROWS


	$voucher_fares[$i] = $_GET['exp_fares'."$i"];
	$voucher_tpl[$i] = $_GET['exp_tpl'."$i"];
	$voucher_tpl_park[$i] = $_GET['exp_tpl_park'."$i"];
	$voucher_tpl_limo[$i] = $_GET['exp_tpl_limo'."$i"];
	$voucher_mileage_num[$i] = $_GET['exp_mileage_num'."$i"];
	$voucher_mie[$i] = $_GET['exp_mie'."$i"];
	$voucher_lodging[$i] = $_GET['exp_lodging'."$i"];
	$voucher_other[$i] = $_GET['exp_other'."$i"];
	$voucher_otherb[$i] = $_GET['exp_otherb'."$i"];
	$voucher_otherc[$i] = $_GET['exp_otherc'."$i"];
	$voucher_otherd[$i] = $_GET['exp_otherd'."$i"];
	$voucher_other_type[$i] = $_GET['exp_other_type'."$i"];
	$voucher_other_typeb[$i] = $_GET['exp_other_typeb'."$i"];
	$voucher_other_typec[$i] = $_GET['exp_other_typec'."$i"];
	$voucher_other_typed[$i] = $_GET['exp_other_typed'."$i"];
	
	$i++;

}


$i = 0; // RESET THE COUNTER
$update_errors = 0; // CAPTURE NUMBER OF UPDATE ERRORS ACROSS TRAVEL DAYS ROWS

foreach($searchResult['data'] as $key => $searchData) { // LOOP THROUGH TRAVEL DAYS, UPDATING VOUCHER AMOUNTS FOR EACH DAY

	#################################################
	## START: UPDATE TRAVEL DAYS RECORD ##
	#################################################
	$update = new FX($serverIP,$webCompanionPort);
	$update -> SetDBData('SIMS_2.fp7','travel_auth_days');
	$update -> SetDBPassword($webPW,$webUN);
	
	$update -> AddDBParam('-recid',$searchData['c_row_ID'][0]);
	
	$update -> AddDBParam('voucher_fares',$voucher_fares[$i]);
	$update -> AddDBParam('voucher_tpl',$voucher_tpl[$i]);
	$update -> AddDBParam('voucher_tpl_park',$voucher_tpl_park[$i]);
	$update -> AddDBParam('voucher_tpl_limo',$voucher_tpl_limo[$i]);
	$update -> AddDBParam('voucher_mileage_num',$voucher_mileage_num[$i]);
	$update -> AddDBParam('voucher_mie',$voucher_mie[$i]);
	$update -> AddDBParam('voucher_lodging',$voucher_lodging[$i]);
	$update -> AddDBParam('voucher_other_1',$voucher_other[$i]);
	$update -> AddDBParam('voucher_other_1_type',$voucher_other_type[$i]);
	$update -> AddDBParam('voucher_other_2',$voucher_otherb[$i]);
	$update -> AddDBParam('voucher_other_2_type',$voucher_other_typeb[$i]);
	$update -> AddDBParam('voucher_other_3',$voucher_otherc[$i]);
	$update -> AddDBParam('voucher_other_3_type',$voucher_other_typec[$i]);
	$update -> AddDBParam('voucher_other_4',$voucher_otherd[$i]);
	$update -> AddDBParam('voucher_other_4_type',$voucher_other_typed[$i]);
	
	$updateResult = $update -> FMEdit();
	
	if($updateResult['errorCode'] <> 0){$update_errors = $update_errors + 1;}
	  //echo  '<p>errorCode: '.$updateResult['errorCode'];
	  //echo  '<p>foundCount: '.$updateResult['foundCount'];
	$updateData = current($updateResult['data']);
	#################################################
	## END: UPDATE TRAVEL DAYS RECORD ##
	#################################################
$i++;
}

//$row_ID = $_GET['row_ID'];
//$event_start_date = $_GET['event_start_date_m'].'/'.$_GET['event_start_date_d'].'/'.$_GET['event_start_date_y'];
//$event_end_date = $_GET['event_end_date_m'].'/'.$_GET['event_end_date_d'].'/'.$_GET['event_end_date_y'];
//$leave_date_requested = $_GET['leave_date_requested_m'].'/'.$_GET['leave_date_requested_d'].'/'.$_GET['leave_date_requested_y'];
//$return_date_requested = $_GET['return_date_requested_m'].'/'.$_GET['return_date_requested_d'].'/'.$_GET['return_date_requested_y'];


//print_r($voucher_fares);
//print_r($voucher_tpl);
//print_r($voucher_mileage_amt);
//print_r($voucher_mie);
//print_r($voucher_lodging);
//print_r($voucher_other_1);








if($update_errors == '0'){  // THE TRAVEL EXPENSE WORKSHEET WAS SUCCESSFULLY SUBMITTED

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SUBMIT_TRAVEL_EXPENSE_WORKSHEET_STAFF');
$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
$newrecord -> AddDBParam('object_ID',$travel_auth_ID);
//$newrecord -> AddDBParam('affected_row_ID',$newrecordData['c_row_ID_cwp'][0]);
//$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

$_SESSION['travel_expenses_submitted_staff'] = '1';

########################################################
## START: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
########################################################
//$to = 'eric.waters@sedl.org';
$to = $updateData['staff::travel_admin_sims_user_ID'][0].'@sedl.org';
$subject = stripslashes($updateData['staff::c_full_name_first_last'][0]).' has submitted a travel expense worksheet';
$message = 
'Dear '.$updateData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0].','."\n\n".

'A travel expense worksheet for recent travel by '.stripslashes($updateData['staff::c_full_name_first_last'][0]).' has been submitted to SIMS.'."\n\n".

'------------------------------------------------------------'."\n".
' TRAVEL AUTH DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Event: '.stripslashes($updateData['travel_authorizations::event_name'][0])."\n".
'Destination: '.stripslashes($updateData['travel_authorizations::event_venue_city'][0]).', '.$updateData['travel_authorizations::event_venue_state'][0]."\n".
'Date(s) of Travel: '.$updateData['travel_authorizations::leave_date_requested'][0].' to '.$updateData['travel_authorizations::return_date_requested'][0]."\n".
'------------------------------------------------------------'."\n\n".

'To process this expense worksheet and generate a travel voucher, click here: '."\n".
'fmp7://198.214.140.248/CC_dms.fp7'."\n\n".
				
'------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'------------------------------------------------------------------------------------------------------------------';

$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';

mail($to, $subject, $message, $headers);
######################################################
## END: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
######################################################


} else { // THERE WAS AN ERROR UPDATING THE TRAVEL EXPENSE WORKSHEET
$_SESSION['travel_expenses_submitted_staff'] = '2';

}
###############################################
## END: UPDATE THE LEAVE REQUEST ##
###############################################

header('Location: http://www.sedl.org/staff/sims/menu_travel.php');
//exit;



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
<?php
session_start();

if($_SESSION['user_ID'] == '') {
header("Location:http://www.sedl.org/staff/sims/sims_menu.php?src=intr");
exit;
}

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


$action = $_GET['action'];

if($action == ''){
$action = 'approve_ta';
}

$travel_auth_ID = $_GET['travel_auth_ID'];
$mod = $_GET['mod'];
//$approval_status = $_GET['app'];



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


if($action == 'view'){ //IF THE ADMIN IS VIEWING THIS TRAVEL REQUEST


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
$travel_admin = $recordData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0];
$request_owner_email = $recordData['staff::email'][0];
//$google_event_name = str_replace(" ","+",$recordData['event_name'][0]);
//$google_event_venue = str_replace(" ","+",$recordData['event_venue'][0]);
//$google_preferred_hotel = str_replace(" ","+",$recordData['preferred_hotel_name'][0]);
$request_owner_staff_ID = $recordData['staff_ID'][0];
if($recordData['multi_dest'][0] == 'yes'){
$dest = 'm';
}else{
$dest = 's';
}

$num_dest = $recordData['num_dest'][0];
###############################################################
## END: FIND SELECTED TRAVEL REQUEST ##
###############################################################
//if($approval_status != 'Approved'){

if($recordData['conus_dest_selected'][0] == '1'){
header('Location: http://www.sedl.org/staff/sims/travel_admin.php?action=view_ta&travel_auth_ID='.$travel_auth_ID.'&dest='.$dest);
}

		if($recordData['multi_dest'][0] != 'yes'){// IF THIS IS NOT A MULTI-DESTINATION REQUEST, SHOW THE SINGLE DESTINATION CONUS SELECT LIST
		
			###############################################################
			## START: GRAB ALL DESTINATIONS FOR THE SELECTED STATE ##
			###############################################################
			$search4 = new FX($serverIP,$webCompanionPort);
			$search4 -> SetDBData('SIMS_2.fp7','travel_authorizations_CONUS_byDestinationState','all');
			$search4 -> SetDBPassword($webPW,$webUN);
			$search4 -> AddDBParam('state_abbrev',$recordData['event_venue_state'][0]);
			//$search4 -> AddDBParam('season_begin_date',$_SESSION['leave_date_requested'],'lte');
			//$search4 -> AddDBParam('season_end_date',$_SESSION['return_date_requested'], 'gte');
			
			$search4 -> AddSortParam('state_abbrev','ascend');
			
			$searchResult4 = $search4 -> FMFind();
			
			//echo '<p>errorCode: '.$searchResult4['errorCode'];
			//echo '<p>foundCount: '.$searchResult4['foundCount'];
			//$recordData4 = current($searchResult4['data']);
			#############################################################
			## END: GRAB ALL DESTINATIONS FOR THE SELECTED STATE ##
			#############################################################
			
			?>
			
			<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->
			
			
			<html>
			<head>
			<title>SIMS: Travel Requests</title>
			<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
			
			
			<script language="JavaScript">
			function checkFields() { 
			
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
						
						<tr><td colspan="2"><p class="alert_small"><b>Travel Admin (<?php echo $travel_admin;?>)</b>: To process this travel request, select the CONUS destination(s) in the form below, click the "Continue" button and follow the 
						instructions on that screen. If you have questions for the staff member who submitted this request, you may <a href="mailto:<?php echo $request_owner_email;?>?subject=Your travel request for <?php echo $recordData['event_venue_city'][0];?>, <?php echo $recordData['event_venue_state'][0];?> (<?php echo $recordData['leave_date_requested'][0];?>)">send a message</a> via e-mail.</p></td></tr>
						
						
						<tr><td colspan="2">
						
			
			<?php 
			#########################################################################################
			## START: SHOW EDITABLE "TRAVEL REQUEST" FORM  ##
			#########################################################################################
			?>
			
			
									<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
									<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $recordData['staff::name_timesheet'][0];?> (<?php echo $recordData['staff::primary_SEDL_workgroup'][0];?>)</strong></td><td class="body" align="right" nowrap>Travel Request Status: <?php echo $recordData['approval_status'][0];?></td></tr>
									<tr><td class="body" nowrap><strong>TRAVEL REQUEST</strong></td><td align="right">Request ID: <?php echo $recordData['travel_auth_ID'][0];?> | Submitted: <strong><?php echo $recordData['creation_timestamp'][0];?></strong> by <strong><?php echo $recordData['staff_sims_ID'][0];?></strong> | <a href="menu_travel_admin.php">Close</a></td></tr>
									
									<tr><td colspan="2" align="right">
									<form name="travel1" method="GET" onsubmit="return checkFields()">
									<input type="hidden" name="action" value="view_ta">
									<input type="hidden" name="mod" value="1">
									<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
									<input type="hidden" name="travel_auth_ID" value="<?php echo $recordData['travel_auth_ID'][0];?>">
									<input type="hidden" name="doc_status" value="1">
									<input type="hidden" name="dest" value="s">
									<input type="hidden" name="event_venue_state" value="<?php echo $recordData['event_venue_state'][0];?>">
									
										<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
			
											<?php if($_SESSION['travel_request_updated_admin'] == '1'){?>
											<tr><td colspan="2"><p class="alert_small">Travel request was successfully updated | <?php echo $recordData['last_mod_timestamp'][0];?> | <a href="travel_admin.php?action=view_ta&mod=1&row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&travel_auth_ID=<?php echo $recordData['travel_auth_ID'][0];?>&city=<?php echo $recordData['event_venue_city'][0];?>">Create a travel authorization from this request</a> <img src="/staff/sims/images/green_check.png"></p></td></tr>
											<?php 
											$_SESSION['travel_request_updated_admin'] = '';
											}elseif($_SESSION['travel_request_updated_admin'] == '2'){?>
											<tr><td colspan="2"><p class="alert_small">There was a problem updating this travel request. Contact <a href="mailto:sims@sedl.org">technical assistance</a> for help.</p></td></tr>
											<?php 
											$_SESSION['travel_request_updated_admin'] = '';
											}?>
			
											<tr><td colspan="2"><strong>Step 1</strong>: <em>Select CONUS destination(s).</em></td></tr>
											
			
											<tr>
											<td class="body" nowrap align="right" bgcolor="#ebebeb">Name of Event:</td>
											<td width="100%"><strong><?php echo stripslashes($recordData['event_name'][0]);?></strong>
											</td>
											</tr>								
											
											<tr>
											<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Period of Travel:</td>
											<td width="100%"><strong><?php echo $recordData['leave_date_requested'][0];?> to <?php echo $recordData['return_date_requested'][0];?></strong> <em>(Hotel nights: <?php echo $recordData['hotel_nights_requested'][0];?>)</em></td>
											</tr>
			
											<tr>
											<td class="body" nowrap align="right" bgcolor="#ebebeb">Destination:</td>
											<td width="100%"><strong><?php echo $recordData['event_venue_city'][0];?>, <?php echo $recordData['event_venue_state'][0];?></strong> | <a href="http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=<?php echo $recordData['event_venue_city'][0];?>,+<?php echo $recordData['event_venue_state'][0];?>&ie=UTF8&z=13&iwloc=addr" target="_blank">Show Map</a></td>
											</tr>								
			
											<tr>
											<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CONUS Destination:</td>
											<td width="100%">
											<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">
											Select the CONUS destination from the drop-down list. If the city entered is not available, select the closest reference city. If you don't know the closest city, click the "Show Map" link.<p>
												<select name="event_conus_city" class="body">
												<option value="">Select City (<?php echo $recordData['event_venue_state'][0];?>)</option>
												<option value="">-------------</option>
												
												<?php foreach($searchResult4['data'] as $key => $searchData4) { 
												if($dest != $searchData4['destination'][0]){
												?>
												<option value="<?php echo $searchData4['destination'][0];?>" <?php if($searchData4['destination'][0] == $recordData['event_venue_city'][0]){ echo ' selected';}?>> <?php echo $searchData4['destination'][0];?>
												<?php $dest = $searchData4['destination'][0];
												}
												} ?>
												</select>
											</div>
											
											</tr>
			
			
			
											<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Continue >>"></td></tr>
											
			
			
										</table>
			
			<?php 
			#########################################################################################
			## END: SHOW EDITABLE "TRAVEL REQUEST" FORM  ##
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
		
		<?php }else{// IF THIS IS A MULTI-DESTINATION REQUEST, SHOW THE MULTI DESTINATION CONUS SELECT LISTS 
		
			#################################################################################################
			## START: LOOP THROUGH MULTIPLE DESTINATIONS AND GRAB ALL CONUS CITIES FOR THE SELECTED STATES ##
			#################################################################################################
			$i=1;
			while($i<=$num_dest){
			
				$cur_state = 'event_venue_state'.$i;
				
				$search2 = new FX($serverIP,$webCompanionPort);
				$search2 -> SetDBData('SIMS_2.fp7','travel_authorizations_CONUS_byDestinationState','all');
				$search2 -> SetDBPassword($webPW,$webUN);
				$search2 -> AddDBParam('state_abbrev',$recordData["$cur_state"][0]);
				//$search2 -> AddDBParam('season_begin_date',$_SESSION['leave_date_requested'],'lte');
				//$search2 -> AddDBParam('season_end_date',$_SESSION['return_date_requested'], 'gte');
				
				$search2 -> AddSortParam('state_abbrev','ascend');
				
				$searchResult2 = $search2 -> FMFind();
				
				//echo '<p>errorCode: '.$searchResult2['errorCode'];
				//echo '<p>foundCount: '.$searchResult2['foundCount'];
				$recordData2 = current($searchResult2['data']);
				
				$z=0;
				foreach($searchResult2['data'] as $key => $searchData2) { // CREATE ARRAY OF CONUS DESTINATION CITIES FOR THIS STATE
				
					if($i == 1){
					$conus_dest1["$z"] = $searchData2['destination'][0];
					$z++;
					}elseif($i == 2){
					$conus_dest2["$z"] = $searchData2['destination'][0];
					$z++;				
					}elseif($i == 3){
					$conus_dest3["$z"] = $searchData2['destination'][0];
					$z++;				
					}elseif($i == 4){
					$conus_dest4["$z"] = $searchData2['destination'][0];
					$z++;				
					}elseif($i == 5){
					$conus_dest5["$z"] = $searchData2['destination'][0];
					$z++;				
					}elseif($i == 6){
					$conus_dest6["$z"] = $searchData2['destination'][0];
					$z++;				
					}
				}
				
			//echo ' - '.$i.'. '.$cur_state.' - '.$recordData2['state'][0].'<br>';
			//print_r($conus_dest3);
			$i++;
			}
			###############################################################################################
			## END: LOOP THROUGH MULTIPLE DESTINATIONS AND GRAB ALL CONUS CITIES FOR THE SELECTED STATES ##
			###############################################################################################
			
			?>
			
			<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->
			
			
			<html>
			<head>
			<title>SIMS: Travel Requests</title>
			<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
			
			
			<script language="JavaScript">
			function checkFields() { 
			
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
						
						<tr><td colspan="2"><p class="alert_small"><b>Travel Admin (<?php echo $travel_admin;?>)</b>: To process this travel request, select the CONUS destination(s) in the form below, click the "Continue" button and follow the 
						instructions on that screen. If you have questions for the staff member who submitted this request, you may <a href="mailto:<?php echo $request_owner_email;?>?subject=Your travel request for <?php echo $recordData['event_venue_city'][0];?>, <?php echo $recordData['event_venue_state'][0];?> (<?php echo $recordData['leave_date_requested'][0];?>)">send a message</a> via e-mail.</p></td></tr>
						
						
						<tr><td colspan="2">
						
			
			<?php 
			#########################################################################################
			## START: SHOW EDITABLE "TRAVEL REQUEST" FORM  ##
			#########################################################################################
			?>
			
			
									<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
									<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $recordData['staff::name_timesheet'][0];?> (<?php echo $recordData['staff::primary_SEDL_workgroup'][0];?>)</strong></td><td class="body" align="right" nowrap>Travel Request Status: <?php echo $recordData['approval_status'][0];?></td></tr>
									<tr><td class="body" nowrap><strong>TRAVEL REQUEST</strong></td><td align="right">Request ID: <?php echo $recordData['travel_auth_ID'][0];?> | Submitted: <strong><?php echo $recordData['creation_timestamp'][0];?></strong> by <strong><?php echo $recordData['staff_sims_ID'][0];?></strong> | <a href="menu_travel_admin.php">Close</a></td></tr>
									
									<tr><td colspan="2" align="right">
									<form name="travel1" method="GET" onsubmit="return checkFields()">
									<input type="hidden" name="action" value="view_ta">
									<input type="hidden" name="mod" value="1">
									<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
									<input type="hidden" name="travel_auth_ID" value="<?php echo $recordData['travel_auth_ID'][0];?>">
									<input type="hidden" name="doc_status" value="1">
									<input type="hidden" name="dest" value="m">
									<input type="hidden" name="num_dest" value="<?php echo $num_dest;?>">
									<input type="hidden" name="event_venue_state" value="<?php echo $recordData['event_venue_state'][0];?>">
									
										<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
			
											<?php if($_SESSION['travel_request_updated_admin'] == '1'){?>
											<tr><td colspan="2"><p class="alert_small">Travel request was successfully updated | <?php echo $recordData['last_mod_timestamp'][0];?> | <a href="travel_admin.php?action=view_ta&mod=1&row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&travel_auth_ID=<?php echo $recordData['travel_auth_ID'][0];?>&city=<?php echo $recordData['event_venue_city'][0];?>">Create a travel authorization from this request</a> <img src="/staff/sims/images/green_check.png"></p></td></tr>
											<?php 
											$_SESSION['travel_request_updated_admin'] = '';
											}elseif($_SESSION['travel_request_updated_admin'] == '2'){?>
											<tr><td colspan="2"><p class="alert_small">There was a problem updating this travel request. Contact <a href="mailto:sims@sedl.org">technical assistance</a> for help.</p></td></tr>
											<?php 
											$_SESSION['travel_request_updated_admin'] = '';
											}?>
			
											<tr><td colspan="2"><strong>Step 1</strong>: <em>Select CONUS destination(s).</em></td></tr>
											
			
											<tr>
											<td class="body" nowrap align="right" bgcolor="#ebebeb">Name of Event:</td>
											<td width="100%"><strong><?php echo stripslashes($recordData['event_name'][0]);?></strong>
											</td>
											</tr>								
											
											<tr>
											<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Period of Travel:</td>
											<td width="100%"><strong><?php echo $recordData['leave_date_requested'][0];?> to <?php echo $recordData['return_date_requested'][0];?></strong> <em>(Hotel nights: <?php echo $recordData['hotel_nights_requested1'][0]+$recordData['hotel_nights_requested2'][0]+$recordData['hotel_nights_requested3'][0]+$recordData['hotel_nights_requested4'][0]+$recordData['hotel_nights_requested5'][0]+$recordData['hotel_nights_requested6'][0];?>)</em></td>
											</tr>
			
											<tr>
											<td class="body" nowrap align="right" bgcolor="#ebebeb">Destination:</td>
											<td width="100%"><strong>Multiple Destinations</strong></td>
											</tr>								
			


											<tr>
											<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CONUS Destination 1:</td>
											<td width="100%">
											<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">
											<div style="display: block;float:right;padding:2px;margin:0px 0px 0px 8px;border-width:1px;border-style:solid;border-color:#0a5253;background-color:#ffffff">
											<iframe width="250" height="150" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city1'][0];?>+<?php echo $recordData['event_venue_state1'][0];?>&amp;output=embed"></iframe><br /><small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city1'][0];?>+<?php echo $recordData['event_venue_state1'][0];?>&amp;iwloc=A" style="color:#0000FF;text-align:left" target="_blank">View Larger Map</a></small>
											</div>
											Select the CONUS destination from the drop-down list. If the city entered is not available, select the closest reference city. If you don't know the closest city, use the map to the right or click "View Larger Map" to view in Google Maps.<p>
											Event city: <?php echo $recordData['event_venue_city1'][0];?> 
												<select name="event_conus_city1" class="body">
												<option value="">Select CONUS City (<?php echo $recordData['event_venue_state1'][0];?>)</option>
												<option value="">-------------</option>
												
		

												<?php foreach($conus_dest1 as $current){ 
												if($dest != $current){
												?>
												
												<option value="<?php echo $current;?>" <?php if($current == $recordData['event_venue_city1'][0]){ echo ' selected';}?>> <?php echo $current;?>
												
												<?php $dest = $current;
												}
												} ?>
												</select>
												<p>Travel Date(s) for this location: <strong><?php echo $recordData['event_venue_city1_travel_start'][0].' to '.$recordData['event_venue_city1_travel_end'][0];?></strong>
											</div>
											
											</tr>
			


											<tr>
											<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CONUS Destination 2:</td>
											<td width="100%">
											<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">
											<div style="display: block;float:right;padding:2px;margin:0px 0px 0px 8px;border-width:1px;border-style:solid;border-color:#0a5253;background-color:#ffffff">
											<iframe width="250" height="150" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city2'][0];?>+<?php echo $recordData['event_venue_state2'][0];?>&amp;output=embed"></iframe><br /><small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city2'][0];?>+<?php echo $recordData['event_venue_state2'][0];?>&amp;iwloc=A" style="color:#0000FF;text-align:left" target="_blank">View Larger Map</a></small>
											</div>
											Select the CONUS destination from the drop-down list. If the city entered is not available, select the closest reference city. If you don't know the closest city, click the "Show Map" link above.<p>
											Event city: <?php echo $recordData['event_venue_city2'][0];?> 
												<select name="event_conus_city2" class="body">
												<option value="">Select CONUS City (<?php echo $recordData['event_venue_state2'][0];?>)</option>
												<option value="">-------------</option>
												
		

												<?php foreach($conus_dest2 as $current){ 
												if($dest != $current){
												?>
												
												<option value="<?php echo $current;?>" <?php if($current == $recordData['event_venue_city2'][0]){ echo ' selected';}?>> <?php echo $current;?>
												
												<?php $dest = $current;
												}
												} ?>
												</select> | <a href="http://maps.google.com/maps?q=<?php echo $recordData['event_venue_city2'][0];?>&hl=en" target="_blank">Show Map</a>
												<p>Travel Date(s) for this location: <strong><?php echo $recordData['event_venue_city2_travel_start'][0].' to '.$recordData['event_venue_city2_travel_end'][0];?></strong>
											</div>
											
											</tr>
			
			
<?php if($num_dest > 2){ ?>

											<tr>
											<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CONUS Destination 3:</td>
											<td width="100%">
											<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">
											<div style="display: block;float:right;padding:2px;margin:0px 0px 0px 8px;border-width:1px;border-style:solid;border-color:#0a5253;background-color:#ffffff">
											<iframe width="250" height="150" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city3'][0];?>+<?php echo $recordData['event_venue_state3'][0];?>&amp;output=embed"></iframe><br /><small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city3'][0];?>+<?php echo $recordData['event_venue_state3'][0];?>&amp;iwloc=A" style="color:#0000FF;text-align:left" target="_blank">View Larger Map</a></small>
											</div>
											Select the CONUS destination from the drop-down list. If the city entered is not available, select the closest reference city. If you don't know the closest city, click the "Show Map" link above.<p>
											Event city: <?php echo $recordData['event_venue_city3'][0];?> 
												<select name="event_conus_city3" class="body">
												<option value="">Select CONUS City (<?php echo $recordData['event_venue_state3'][0];?>)</option>
												<option value="">-------------</option>
												
		

												<?php foreach($conus_dest3 as $current){ 
												if($dest != $current){
												?>
												
												<option value="<?php echo $current;?>" <?php if($current == $recordData['event_venue_city3'][0]){ echo ' selected';}?>> <?php echo $current;?>
												
												<?php $dest = $current;
												}
												} ?>
												</select> | <a href="http://maps.google.com/maps?q=<?php echo $recordData['event_venue_city3'][0];?>&hl=en" target="_blank">Show Map</a>
												<p>Travel Date(s) for this location: <strong><?php echo $recordData['event_venue_city3_travel_start'][0].' to '.$recordData['event_venue_city3_travel_end'][0];?></strong>
											</div>
											
											</tr>



<?php } ?>

<?php if($num_dest > 3){ ?>

											<tr>
											<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CONUS Destination 4:</td>
											<td width="100%">
											<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">
											<div style="display: block;float:right;padding:2px;margin:0px 0px 0px 8px;border-width:1px;border-style:solid;border-color:#0a5253;background-color:#ffffff">
											<iframe width="250" height="150" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city4'][0];?>+<?php echo $recordData['event_venue_state4'][0];?>&amp;output=embed"></iframe><br /><small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city4'][0];?>+<?php echo $recordData['event_venue_state4'][0];?>&amp;iwloc=A" style="color:#0000FF;text-align:left" target="_blank">View Larger Map</a></small>
											</div>
											Select the CONUS destination from the drop-down list. If the city entered is not available, select the closest reference city. If you don't know the closest city, click the "Show Map" link above.<p>
											Event city: <?php echo $recordData['event_venue_city4'][0];?> 
												<select name="event_conus_city4" class="body">
												<option value="">Select CONUS City (<?php echo $recordData['event_venue_state4'][0];?>)</option>
												<option value="">-------------</option>
												
		

												<?php foreach($conus_dest4 as $current){ 
												if($dest != $current){
												?>
												
												<option value="<?php echo $current;?>" <?php if($current == $recordData['event_venue_city4'][0]){ echo ' selected';}?>> <?php echo $current;?>
												
												<?php $dest = $current;
												}
												} ?>
												</select> | <a href="http://maps.google.com/maps?q=<?php echo $recordData['event_venue_city4'][0];?>&hl=en" target="_blank">Show Map</a>
											</div>
												<p>Travel Date(s) for this location: <strong><?php echo $recordData['event_venue_city4_travel_start'][0].' to '.$recordData['event_venue_city4_travel_end'][0];?></strong>
											
											</tr>



<?php } ?>


<?php if($num_dest > 4){ ?>

											<tr>
											<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CONUS Destination 5:</td>
											<td width="100%">
											<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">
											<div style="display: block;float:right;padding:2px;margin:0px 0px 0px 8px;border-width:1px;border-style:solid;border-color:#0a5253;background-color:#ffffff">
											<iframe width="250" height="150" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city5'][0];?>+<?php echo $recordData['event_venue_state5'][0];?>&amp;output=embed"></iframe><br /><small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city5'][0];?>+<?php echo $recordData['event_venue_state5'][0];?>&amp;iwloc=A" style="color:#0000FF;text-align:left" target="_blank">View Larger Map</a></small>
											</div>
											Select the CONUS destination from the drop-down list. If the city entered is not available, select the closest reference city. If you don't know the closest city, click the "Show Map" link above.<p>
											Event city: <?php echo $recordData['event_venue_city5'][0];?> 
												<select name="event_conus_city5" class="body">
												<option value="">Select CONUS City (<?php echo $recordData['event_venue_state5'][0];?>)</option>
												<option value="">-------------</option>
												
		

												<?php foreach($conus_dest5 as $current){ 
												if($dest != $current){
												?>
												
												<option value="<?php echo $current;?>" <?php if($current == $recordData['event_venue_city5'][0]){ echo ' selected';}?>> <?php echo $current;?>
												
												<?php $dest = $current;
												}
												} ?>
												</select> | <a href="http://maps.google.com/maps?q=<?php echo $recordData['event_venue_city5'][0];?>&hl=en" target="_blank">Show Map</a>
												<p>Travel Date(s) for this location: <strong><?php echo $recordData['event_venue_city5_travel_start'][0].' to '.$recordData['event_venue_city5_travel_end'][0];?></strong>
											</div>
											
											</tr>



<?php } ?>


<?php if($num_dest > 5){ ?>

											<tr>
											<td class="body" nowrap align="right" bgcolor="#ebebeb" valign="top">CONUS Destination 6:</td>
											<td width="100%">
											<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">
											<div style="display: block;float:right;padding:2px;margin:0px 0px 0px 8px;border-width:1px;border-style:solid;border-color:#0a5253;background-color:#ffffff">
											<iframe width="250" height="150" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city6'][0];?>+<?php echo $recordData['event_venue_state6'][0];?>&amp;output=embed"></iframe><br /><small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=<?php echo $recordData['event_venue_city6'][0];?>+<?php echo $recordData['event_venue_state6'][0];?>&amp;iwloc=A" style="color:#0000FF;text-align:left" target="_blank">View Larger Map</a></small>
											</div>
											Select the CONUS destination from the drop-down list. If the city entered is not available, select the closest reference city. If you don't know the closest city, click the "Show Map" link above.<p>
											Event city: <?php echo $recordData['event_venue_city6'][0];?> 
												<select name="event_conus_city6" class="body">
												<option value="">Select CONUS City (<?php echo $recordData['event_venue_state6'][0];?>)</option>
												<option value="">-------------</option>
												
		

												<?php foreach($conus_dest6 as $current){ 
												if($dest != $current){
												?>
												
												<option value="<?php echo $current;?>" <?php if($current == $recordData['event_venue_city6'][0]){ echo ' selected';}?>> <?php echo $current;?>
												
												<?php $dest = $current;
												}
												} ?>
												</select> | <a href="http://maps.google.com/maps?q=<?php echo $recordData['event_venue_city6'][0];?>&hl=en" target="_blank">Show Map</a>
												<p>Travel Date(s) for this location: <strong><?php echo $recordData['event_venue_city6_travel_start'][0].' to '.$recordData['event_venue_city6_travel_end'][0];?></strong>
											</div>
											
											</tr>



<?php } ?>





											<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Continue >>"></td></tr>
											
			
			
										</table>
			
			<?php 
			#########################################################################################
			## END: SHOW EDITABLE "TRAVEL REQUEST" FORM  ##
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
		
		<?php } ?>






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


} elseif($action == 'view2'){ //IF THE ADMIN IS VIEWING THIS TRAVEL REQUEST


/*
if($_GET['update'] == '1'){

	$row_ID = $_GET['row_ID'];
	$event_start_date = $_GET['event_start_date_m'].'/'.$_GET['event_start_date_d'].'/'.$_GET['event_start_date_y'];
	$event_end_date = $_GET['event_end_date_m'].'/'.$_GET['event_end_date_d'].'/'.$_GET['event_end_date_y'];
	$leave_date_requested = $_GET['leave_date_requested_m'].'/'.$_GET['leave_date_requested_d'].'/'.$_GET['leave_date_requested_y'];
	$return_date_requested = $_GET['return_date_requested_m'].'/'.$_GET['return_date_requested_d'].'/'.$_GET['return_date_requested_y'];
	
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
	$update -> AddDBParam('approval_status','Pending');
	
	$update -> AddDBParam('purpose_of_travel',$purpose_of_travel);
	$update -> AddDBParam('event_name',$_GET['event_name']);
	$update -> AddDBParam('event_venue',$_GET['event_venue']);
	$update -> AddDBParam('event_venue_addr',$_GET['event_venue_addr']);
	$update -> AddDBParam('event_start_date',$event_start_date);
	$update -> AddDBParam('event_end_date',$event_end_date);
	$update -> AddDBParam('event_start_time',$_GET['event_start_time']);
	$update -> AddDBParam('event_end_time',$_GET['event_end_time']);
	$update -> AddDBParam('leave_date_requested',$leave_date_requested);
	$update -> AddDBParam('return_date_requested',$return_date_requested);
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
	$update -> AddDBParam('other_information',$_GET['other_information']);
	
	
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
	$newrecord -> AddDBParam('action','UPDATE_TRAVEL_REQUEST_ADMIN');
	$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
	$newrecord -> AddDBParam('object_ID',$newrecordData['travel_auth_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$newrecordData['c_row_ID_cwp'][0]);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
	$_SESSION['travel_request_updated_admin'] = '1';
	} else {
	$_SESSION['travel_request_updated_admin'] = '2';
	}
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
$travel_admin = $recordData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0];
$request_owner_email = $recordData['staff::email'][0];
$google_event_name = str_replace(" ","+",$recordData['event_name'][0]);
$google_event_venue = str_replace(" ","+",$recordData['event_venue'][0]);
$google_preferred_hotel = str_replace(" ","+",$recordData['preferred_hotel_name'][0]);
$request_owner_staff_ID = $recordData['staff_ID'][0];
###############################################################
## END: FIND SELECTED TRAVEL REQUEST ##
###############################################################
//if($approval_status != 'Approved'){

/*
############################################################
## START: GRAB BUDGET CODES FOR BUDGET CODE CHECKBOXES ##
############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','budget_code_usage','all');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('staff_ID',$request_owner_staff_ID);

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
//}
*/


?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">


<script language="JavaScript">
function checkFields() { 

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
			
			<tr><td colspan="2"><p class="alert_small"><b>Travel Admin (<?php echo $travel_admin;?>)</b>: To process this travel request, select the CONUS destination(s) in the form below, click the "Create Travel Authorization" button and follow the 
			instructions on that screen. If you have questions for the staff member who submitted this request, you may <a href="mailto:<?php echo $request_owner_email;?>?subject=Your travel request for <?php echo $recordData['event_venue_city'][0];?>, <?php echo $recordData['event_venue_state'][0];?> (<?php echo $recordData['leave_date_requested'][0];?>)">send a message</a> via e-mail.</p></td></tr>
			
			
			<tr><td colspan="2">
			

<?php 
#########################################################################################
## START: SHOW EDITABLE "TRAVEL REQUEST" FORM  ##
#########################################################################################
?>


						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $recordData['staff::name_timesheet'][0];?> (<?php echo $recordData['staff::primary_SEDL_workgroup'][0];?>)</strong></td><td class="body" align="right" nowrap>Travel Request Status: <?php echo $recordData['approval_status'][0];?></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL REQUEST</strong></td><td align="right">Request ID: <?php echo $recordData['travel_auth_ID'][0];?> | Submitted: <strong><?php echo $recordData['creation_timestamp'][0];?></strong> by <strong><?php echo $recordData['staff_sims_ID'][0];?></strong> | <a href="" target="_blank">Print</a> | <a href="travel_admin.php?action=view_ta&mod=1&row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&travel_auth_ID=<?php echo $recordData['travel_auth_ID'][0];?>&city=<?php echo $recordData['event_venue_city'][0];?>">Create travel authorization</a> | <a href="menu_travel.php">Close</a></td></tr>
						
						<tr><td colspan="2" align="right">
						<form name="travel1" method="GET" onsubmit="return checkFields()">
						<input type="hidden" name="action" value="view_ta">
						<input type="hidden" name="mod" value="1">
						<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
						<input type="hidden" name="travel_auth_ID" value="<?php echo $recordData['travel_auth_ID'][0];?>">
						<input type="hidden" name="doc_status" value="1">
						<input type="submit" name="submit" value="Create Travel Authorization"><br>
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">

								<?php if($_SESSION['travel_request_updated_admin'] == '1'){?>
								<tr><td colspan="2"><p class="alert_small">Travel request was successfully updated | <?php echo $recordData['last_mod_timestamp'][0];?> | <a href="travel_admin.php?action=view_ta&mod=1&row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&travel_auth_ID=<?php echo $recordData['travel_auth_ID'][0];?>&city=<?php echo $recordData['event_venue_city'][0];?>">Create a travel authorization from this request</a> <img src="/staff/sims/images/green_check.png"></p></td></tr>
								<?php 
								$_SESSION['travel_request_updated_admin'] = '';
								}elseif($_SESSION['travel_request_updated_admin'] == '2'){?>
								<tr><td colspan="2"><p class="alert_small">There was a problem updating this travel request. Contact <a href="mailto:sims@sedl.org">technical assistance</a> for help.</p></td></tr>
								<?php 
								$_SESSION['travel_request_updated_admin'] = '';
								}?>

								<tr><td colspan="2"><strong>Purpose of Travel</strong>: <em>What is the purpose of this travel?</em></td></tr>
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Purpose:</td>
								<td width="100%">
								<strong><?php echo $recordData['c_purpose_of_travel_csv'][0];?></strong>
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Name of Event:</td>
								<td width="100%"><strong><?php echo $recordData['event_name'][0];?></strong> | 
								<a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $google_event_name;?>&btnG=Google+Search&aq=f" target="_blank">Google: Event Name</a>
								</td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event Venue:</td>
								<td width="100%"><strong><?php echo $recordData['event_venue'][0];?></strong> | 
								<a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $google_event_venue;?>&btnG=Google+Search&aq=f" target="_blank">Google: Event Venue</a>
								</td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Event Address:</td>
								<td width="100%"><strong><?php echo stripslashes($recordData['event_venue_addr'][0]);?></strong></td>
								</tr>								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Destination:</td>
								<td width="100%"><strong><?php echo $recordData['event_venue_city'][0];?>, <?php echo $recordData['event_venue_state'][0];?></strong></td>
								</tr>								

								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event Start Date:</td>
								<td><strong><?php echo $recordData['event_start_date'][0];?></strong>
	
								&nbsp;&nbsp;&nbsp;Event Start Time: <strong><?php echo $recordData['event_start_time'][0];?></strong>

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event End Date:
								
								
								
								</td><td><strong><?php echo $recordData['event_end_date'][0];?></strong>

								&nbsp;&nbsp;&nbsp;Event End Time: <strong><?php echo $recordData['event_end_time'][0];?></strong>
								
								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Leave Date:</td>
								<td>
								<select name="leave_date_requested_m" class="body" id="month" onchange="doWork();">
								<option value=""></option>
						
									<option value="01"<?php if ($recordData['c_leave_date_requested_m'][0] == '1') { echo ' SELECTED';}?>> Jan</option>
									<option value="02"<?php if ($recordData['c_leave_date_requested_m'][0] == '2') { echo ' SELECTED';}?>> Feb</option>
									<option value="03"<?php if ($recordData['c_leave_date_requested_m'][0] == '3') { echo ' SELECTED';}?>> Mar</option>
									<option value="04"<?php if ($recordData['c_leave_date_requested_m'][0] == '4') { echo ' SELECTED';}?>> Apr</option>
									<option value="05"<?php if ($recordData['c_leave_date_requested_m'][0] == '5') { echo ' SELECTED';}?>> May</option>
									<option value="06"<?php if ($recordData['c_leave_date_requested_m'][0] == '6') { echo ' SELECTED';}?>> Jun</option>
									<option value="07"<?php if ($recordData['c_leave_date_requested_m'][0] == '7') { echo ' SELECTED';}?>> Jul</option>
									<option value="08"<?php if ($recordData['c_leave_date_requested_m'][0] == '8') { echo ' SELECTED';}?>> Aug</option>
									<option value="09"<?php if ($recordData['c_leave_date_requested_m'][0] == '9') { echo ' SELECTED';}?>> Sep</option>
									<option value="10"<?php if ($recordData['c_leave_date_requested_m'][0] == '10') { echo ' SELECTED';}?>> Oct</option>
									<option value="11"<?php if ($recordData['c_leave_date_requested_m'][0] == '11') { echo ' SELECTED';}?>> Nov</option>
									<option value="12"<?php if ($recordData['c_leave_date_requested_m'][0] == '12') { echo ' SELECTED';}?>> Dec</option>

								</select> 
								
								<select name="leave_date_requested_d" class="body" id="day">
									<option value=""></option>
									
									<?php 
									for($i=1;$i<=31;$i++){?>
									<option value="<?php echo $i;?>"<?php if ($recordData['c_leave_date_requested_d'][0] == $i) { echo ' SELECTED';}?>><?php echo $i;?></option>
									<?php 
									}
									?>
									
								</select> 
								<select name="leave_date_requested_y" class="body">
								<option value=""></option>
						
									<option value="2008"<?php if ($recordData['c_leave_date_requested_y'][0] == '2008') { echo ' SELECTED';}?>> 2008</option>
									<option value="2009"<?php if ($recordData['c_leave_date_requested_y'][0] == '2009') { echo ' SELECTED';}?>> 2009</option>
									<option value="2010"<?php if ($recordData['c_leave_date_requested_y'][0] == '2010') { echo ' SELECTED';}?>> 2010</option>
									<option value="2011"<?php if ($recordData['c_leave_date_requested_y'][0] == '2011') { echo ' SELECTED';}?>> 2011</option>
									<option value="2012"<?php if ($recordData['c_leave_date_requested_y'][0] == '2012') { echo ' SELECTED';}?>> 2012</option>

								</select> 
								&nbsp;&nbsp;&nbsp;Requested Leave Time: <input type="text" name="leave_time_requested" size="10" value="<?php echo $recordData['leave_time_requested'][0];?>">

								

								</td></tr>
								
								<tr><td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Return Date:
								
								
								
								</td><td>
								<select name="return_date_requested_m" class="body" id="month" onchange="doWork();">
								<option value=""></option>
						
									<option value="01"<?php if ($recordData['c_return_date_requested_m'][0] == '1') { echo ' SELECTED';}?>> Jan</option>
									<option value="02"<?php if ($recordData['c_return_date_requested_m'][0] == '2') { echo ' SELECTED';}?>> Feb</option>
									<option value="03"<?php if ($recordData['c_return_date_requested_m'][0] == '3') { echo ' SELECTED';}?>> Mar</option>
									<option value="04"<?php if ($recordData['c_return_date_requested_m'][0] == '4') { echo ' SELECTED';}?>> Apr</option>
									<option value="05"<?php if ($recordData['c_return_date_requested_m'][0] == '5') { echo ' SELECTED';}?>> May</option>
									<option value="06"<?php if ($recordData['c_return_date_requested_m'][0] == '6') { echo ' SELECTED';}?>> Jun</option>
									<option value="07"<?php if ($recordData['c_return_date_requested_m'][0] == '7') { echo ' SELECTED';}?>> Jul</option>
									<option value="08"<?php if ($recordData['c_return_date_requested_m'][0] == '8') { echo ' SELECTED';}?>> Aug</option>
									<option value="09"<?php if ($recordData['c_return_date_requested_m'][0] == '9') { echo ' SELECTED';}?>> Sep</option>
									<option value="10"<?php if ($recordData['c_return_date_requested_m'][0] == '10') { echo ' SELECTED';}?>> Oct</option>
									<option value="11"<?php if ($recordData['c_return_date_requested_m'][0] == '11') { echo ' SELECTED';}?>> Nov</option>
									<option value="12"<?php if ($recordData['c_return_date_requested_m'][0] == '12') { echo ' SELECTED';}?>> Dec</option>

								</select> 
								
								<select name="return_date_requested_d" class="body" id="day">
									<option value=""></option>
									
									<?php 
									for($i=1;$i<=31;$i++){?>
									<option value="<?php echo $i;?>"<?php if ($recordData['c_return_date_requested_d'][0] == $i) { echo ' SELECTED';}?>><?php echo $i;?></option>
									<?php 
									}
									?>
									
								</select> 
								<select name="return_date_requested_y" class="body">
								<option value=""></option>
						
									<option value="2008"<?php if ($recordData['c_return_date_requested_y'][0] == '2008') { echo ' SELECTED';}?>> 2008</option>
									<option value="2009"<?php if ($recordData['c_return_date_requested_y'][0] == '2009') { echo ' SELECTED';}?>> 2009</option>
									<option value="2010"<?php if ($recordData['c_return_date_requested_y'][0] == '2010') { echo ' SELECTED';}?>> 2010</option>
									<option value="2011"<?php if ($recordData['c_return_date_requested_y'][0] == '2011') { echo ' SELECTED';}?>> 2011</option>
									<option value="2012"<?php if ($recordData['c_return_date_requested_y'][0] == '2012') { echo ' SELECTED';}?>> 2012</option>

								</select> 
								&nbsp;&nbsp;&nbsp;Requested Return Time: <input type="text" name="return_time_requested" size="10" value="<?php echo $recordData['return_time_requested'][0];?>">
								
								</td></tr>
								
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
								<input type="checkbox" name="trans_airline_requested" value="yes"<?php if ($recordData['trans_airline_requested'][0] == 'yes') { echo ' checked="checked"';}?>> Airline | <em>If yes, indicate preferred carrier:</em> <input type="text" name="trans_airline_preferred_carrier" size="10" value="<?php echo $recordData['trans_airline_preferred_carrier'][0];?>"><br>
								&nbsp;&nbsp;&nbsp;<input type="checkbox" name="trans_airline_bta_prepaid" value="yes"<?php if ($recordData['trans_airline_bta_prepaid'][0] == 'yes') { echo ' checked="checked"';}?>> Charge airline fare to SEDL BTA account (Pre-paid)<p>
								<input type="checkbox" name="trans_rental_car_requested" value="yes"<?php if ($recordData['trans_rental_car_requested'][0] == 'yes') { echo ' checked="checked"';}?>> Rental Car Requested | <em>If yes, indicate number of days: <input type="text" name="trans_rental_car_num_days_requested" size="5" value="<?php echo $recordData['trans_rental_car_num_days_requested'][0];?>"> &nbsp;and justification:</em> <input type="text" name="trans_rental_car_justification" size="20" value="<?php echo $recordData['trans_rental_car_justification'][0];?>"><p>
								<input type="checkbox" name="trans_traveling_with_other_staff" value="yes"<?php if ($recordData['trans_traveling_with_other_staff'][0] == 'yes') { echo ' checked="checked"';}?>> Traveling with other person(s) | <em>If yes, indicate names:</em> <input type="text" name="trans_traveling_with_name" size="20" value="<?php echo $recordData['trans_traveling_with_name'][0];?>"><p>
								<input type="checkbox" name="travel_advance_requested" value="yes"<?php if ($recordData['travel_advance_requested'][0] == 'yes') { echo ' checked="checked"';}?>> Travel advance requested<p>
								</td>
								</tr>								

								<tr><td colspan="2"><strong>Accommodations</strong>: <em>Where will you be lodging?</em></td></tr>

								<tr valign="top">
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Preferred Hotel:</td>
								<td width="100%"><input type="text" name="preferred_hotel_name" size="45" value="<?php echo stripslashes($recordData['preferred_hotel_name'][0]);?>">
																<a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $google_preferred_hotel;?>&btnG=Google+Search&aq=f" target="_blank">Google: Preferred Hotel</a>

								<p>
								Is this the conference or meeting hotel? <input type="radio" name="preferred_hotel_is_conf_hotel" value="yes"<?php if ($recordData['preferred_hotel_is_conf_hotel'][0] == 'yes') { echo ' checked="checked"';}?>> Yes &nbsp;&nbsp;&nbsp;<input type="radio" name="preferred_hotel_is_conf_hotel" value="no"<?php if ($recordData['preferred_hotel_is_conf_hotel'][0] == 'no') { echo ' checked="checked"';}?>> No<p>
								Other justification for using this hotel: <br>
								<input type="text" name="preferred_hotel_other_justification" size="45" value="<?php echo $recordData['preferred_hotel_other_justification'][0];?>">
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

								<tr><td colspan="2"><strong>Other Information</strong>: <em>Please provide any additional information that you think would be helpful to your manager/director in 
								approving your request or to support staff in making your travel arrangements.</em></td></tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Comments:</td>
								<td width="100%"><input type="text" name="other_information" size="100%" value="<?php echo stripslashes($recordData['other_information'][0]);?>"></td>
								</tr>								



								<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Create Travel Authorization"></td></tr>
								


							</table>

<?php 
#########################################################################################
## END: SHOW EDITABLE "TRAVEL REQUEST" FORM  ##
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


} elseif($action == 'view_ta') { //IF THE ADMIN IS VIEWING A TRAVEL AUTHORIZATION

if($_GET['mod'] == '1'){ // IF THE TRAVEL REQUEST HAS BEEN VERIFIED AND CONVERTED TO A TRAVEL AUTHORIZATION

	$row_ID = $_GET['row_ID'];
	$dest = $_GET['dest'];
	$num_dest = $_GET['num_dest'];
	
	//echo '<p>$dest: '.$dest;
	//echo '<p>$num_dest: '.$num_dest;

	//$leave_date_requested = $_GET['leave_date_requested_m'].'/'.$_GET['leave_date_requested_d'].'/'.$_GET['leave_date_requested_y'];
	//$return_date_requested = $_GET['return_date_requested_m'].'/'.$_GET['return_date_requested_d'].'/'.$_GET['return_date_requested_y'];
	
//	for($i=0 ; $i<count($_GET['purpose_of_travel']) ; $i++) {
//	$purpose_of_travel .= $_GET['purpose_of_travel'][$i]."\r"; 
//	}
	
//	for($i=0 ; $i<count($_GET['budget_code']) ; $i++) {
//	$budget_code .= $_GET['budget_code'][$i]."\r"; 
//	}
	
	if($dest == 's'){ // IF THIS IS A SINGLE-DESTINATION TRAVEL REQUEST

				#########################################################
				## START: UPDATE TRAVEL REQUEST RECORD WITH CONUS CITY ##
				#########################################################
				$update = new FX($serverIP,$webCompanionPort);
				$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
				$update -> SetDBPassword($webPW,$webUN);
				$update -> AddDBParam('-recid',$row_ID);
				
				$update -> AddDBParam('event_conus_city',$_GET['event_conus_city']);
				$update -> AddDBParam('conus_dest_selected','1');
				
				$updateResult = $update -> FMEdit();
				
				//  echo  '<p>errorCode: '.$updateResult['errorCode'];
				//  echo  '<p>foundCount: '.$updateResult['foundCount'];
				$updateData = current($updateResult['data']);
				#######################################################
				## END: UPDATE TRAVEL REQUEST RECORD WITH CONUS CITY ##
				#######################################################
				
				if($updateData['event_venue_city'][0] != $_GET['event_conus_city']){ // IF THE CITY ENTERED DOES NOT MATCH A CONUS CITY	
					#########################################################################################
					## START: UPDATE TRAVEL_AUTH_DAYS TABLE WITH CONUS CITY (IF DIFFERENT THAN VENUE CITY) ##
					#########################################################################################
				
						## FIND TRAVEL AUTH DAYS FOR THIS AUTHORIZATION
						$search5 = new FX($serverIP,$webCompanionPort);
						$search5 -> SetDBData('SIMS_2.fp7','travel_auth_days');
						$search5 -> SetDBPassword($webPW,$webUN);
						$search5 -> AddDBParam('travel_auth_ID','=='.$_GET['travel_auth_ID']);
						//$search5 -> AddDBParam('-lop','or');
						
						$searchResult5 = $search5 -> FMFind();
				
						//$recordData5 = current($searchResult5['data']);
				
						## UPDATE DESTINATION CITY WITH ACTUAL CONUS CITY
						foreach($searchResult5['data'] as $key => $searchData5) {
							$update = new FX($serverIP,$webCompanionPort);
							$update -> SetDBData('SIMS_2.fp7','travel_auth_days');
							$update -> SetDBPassword($webPW,$webUN);
							$update -> AddDBParam('-recid',$searchData5['c_row_ID'][0]);
							$update -> AddDBParam('destination_city',$_GET['event_conus_city']);
							$updateResult = $update -> FMEdit();
							//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
							//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
						}
					#######################################################################################
					## END: UPDATE TRAVEL_AUTH_DAYS TABLE WITH CONUS CITY (IF DIFFERENT THAN VENUE CITY) ##
					#######################################################################################
				}
				
			
			
	}elseif($dest == 'm'){ // IF THIS IS A MULTI-DESTINATION TRAVEL REQUEST
	
	
				###########################################################
				## START: UPDATE TRAVEL REQUEST RECORD WITH CONUS CITIES ##
				###########################################################
				$update = new FX($serverIP,$webCompanionPort);
				$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
				$update -> SetDBPassword($webPW,$webUN);
				$update -> AddDBParam('-recid',$row_ID);
				
				$i=1;
				while($i<=$num_dest){ // LOOP THROUGH DESTINATION VENUE CITIES
					$cur_conus_dest = 'event_conus_city'.$i;
					$update -> AddDBParam("$cur_conus_dest",$_GET["$cur_conus_dest"]);
				$i++;
				}			
				$update -> AddDBParam('conus_dest_selected','1');
				
				$updateResult = $update -> FMEdit();
				
				  //echo  '<p>errorCode[1451]: '.$updateResult['errorCode'];
				  //echo  '<p>foundCount: '.$updateResult['foundCount'];
				$updateData = current($updateResult['data']);
				#########################################################
				## END: UPDATE TRAVEL REQUEST RECORD WITH CONUS CITIES ##
				#########################################################
				
				$i=1;
				while($i<=$num_dest){ // LOOP THROUGH DESTINATION VENUE CITIES
					$cur_conus_city_field = 'event_conus_city'.$i;
					$cur_venue_city_field = 'event_venue_city'.$i;
					//echo '<p>$cur_venue_city_field: '.$updateData["$cur_venue_city_field"][0];
					//echo '<p>$cur_conus_city_field: '.$updateData["$cur_conus_city_field"][0];
					//exit;
					if($updateData["$cur_conus_city_field"][0] != $updateData["$cur_venue_city_field"][0]){ // IF THE CITY ENTERED DOES NOT MATCH A CONUS CITY	
						#########################################################################################
						## START: UPDATE TRAVEL_AUTH_DAYS TABLE WITH CONUS CITY (IF DIFFERENT THAN VENUE CITY) ##
						#########################################################################################
					
							## FIND TRAVEL AUTH DAYS FOR THIS AUTHORIZATION
							$search5 = new FX($serverIP,$webCompanionPort);
							$search5 -> SetDBData('SIMS_2.fp7','travel_auth_days');
							$search5 -> SetDBPassword($webPW,$webUN);
							$search5 -> AddDBParam('travel_auth_ID','=='.$_GET['travel_auth_ID']);
							$search5 -> AddDBParam('destination_city',$updateData["$cur_venue_city_field"][0]);
							//$search5 -> AddDBParam('-lop','or');
							
							$searchResult5 = $search5 -> FMFind();
					
								//echo  '<p>$i='.$i.', errorCode[1478]: '.$searchResult5['errorCode'];
								//echo  '<p>foundCount: '.$searchResult5['foundCount'];

							//$recordData5 = current($searchResult5['data']);
					
							## UPDATE DESTINATION CITY WITH ACTUAL CONUS CITY
							foreach($searchResult5['data'] as $key => $searchData5) {
								$update = new FX($serverIP,$webCompanionPort);
								$update -> SetDBData('SIMS_2.fp7','travel_auth_days');
								$update -> SetDBPassword($webPW,$webUN);
								$update -> AddDBParam('-recid',$searchData5['c_row_ID'][0]);
								$update -> AddDBParam('destination_city',$updateData["$cur_conus_city_field"][0]);
								$updateResult = $update -> FMEdit();
								//echo  '<p>errorCode[1491]: '.$newrecordResult['errorCode'];
								//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
							}
						#######################################################################################
						## END: UPDATE TRAVEL_AUTH_DAYS TABLE WITH CONUS CITY (IF DIFFERENT THAN VENUE CITY) ##
						#######################################################################################
					}
				$i++;
				}	
	

	}
}

#################################################################
## START: FIND SELECTED TRAVEL REQUEST ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','travel_authorizations');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('travel_auth_ID','=='.$_GET['travel_auth_ID']);
//$search -> AddDBParam('-lop','or');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//echo '<p>$recordData[event_name]: '.$recordData['event_name'][0];
$travel_admin = $recordData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0];
$request_owner_email = $recordData['staff::email'][0];
$google_event_name = str_replace(" ","+",$recordData['event_name'][0]);
$google_event_venue = str_replace(" ","+",$recordData['event_venue'][0]);
$google_preferred_hotel = str_replace(" ","+",$recordData['preferred_hotel_name'][0]);
$request_owner_sims_ID = $recordData['staff_sims_ID'][0];
$num_dest = $recordData['num_dest'][0];
if($recordData['multi_dest'][0] == 'yes'){
$dest = 'm';
}else{
$dest = 's';
}

###############################################################
## END: FIND SELECTED TRAVEL REQUEST ##
###############################################################


#################################################################
## START: FIND PER DIEM RATES FOR EACH TRAVEL DAY ##
#################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','travel_auth_days');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('travel_auth_ID','=='.$_GET['travel_auth_ID']);
//$search -> AddDBParam('-lop','or');

$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
//print_r ($searchResult2);
$recordData2 = current($searchResult2['data']);
###############################################################
## END: FIND PER DIEM RATES FOR EACH TRAVEL DAY ##
###############################################################

/*
###############################################################
## START: GRAB ALL DESTINATIONS FOR THE SELECTED STATE ##
###############################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','travel_authorizations_CONUS_byDestinationState','all');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('state_abbrev',$recordData['event_venue_state'][0]);
//$search4 -> AddDBParam('season_begin_date',$_SESSION['leave_date_requested'],'lte');
//$search4 -> AddDBParam('season_end_date',$_SESSION['return_date_requested'], 'gte');

$search4 -> AddSortParam('state_abbrev','ascend');

$searchResult4 = $search4 -> FMFind();

//echo '<p>errorCode: '.$searchResult4['errorCode'];
//echo '<p>foundCount: '.$searchResult4['foundCount'];
//$recordData4 = current($searchResult4['data']);
#############################################################
## END: GRAB ALL DESTINATIONS FOR THE SELECTED STATE ##
#############################################################
*/

?>

<html>
<head>
<title>SIMS: Travel Authorizations</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">

function startCalc(){
  interval = setInterval("calc()",1);
}
function calc(){
  one = document.travel_auth_submit.est_calc1.value;
  two = document.travel_auth_submit.est_calc2.value; 
  three = document.travel_auth_submit.est_calc3.value; 
  four = document.travel_auth_submit.est_calc4.value; 
  five = document.travel_auth_submit.est_calc5.value; 
  six = document.travel_auth_submit.est_calc6.value; 
  seven = document.travel_auth_submit.est_calc7.value; 
  document.travel_auth_submit.est_calc_sum.value = Math.round(((one * 1) + (two * 1) + (three * 1) + (four * 1) + (five * 1) + (six * 1) + (seven * 1))*100)/100;
}
function stopCalc(){
  clearInterval(interval);
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
			
			<tr><td colspan="2"><p class="alert_small">
			
			<b>Travel Admin (<?php echo $travel_admin;?>)</b>: 
			<?php if($recordData['approval_status'][0] == 'Pending'){?>
			To process this travel authorization, fill in the required information (in the blue sections) and click the "Submit for Approval" button.
			<?php }elseif($recordData['approval_status'][0] == 'Approved'){?>
			This travel authorization has been approved: <a href="travel_itinerary.php?id=<?php echo $recordData['travel_auth_ID'][0];?>">Print Itinerary</a>
			<?php }else{ } ?>
			</p></td></tr>
			
			
			<tr><td colspan="2">
			<form name="travel_auth_submit">
			<input type="hidden" name="travel_auth_ID" value="<?php echo $recordData['travel_auth_ID'][0];?>">
			<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
			<input type="hidden" name="action" value="submit_ta">
			<input type="hidden" name="multi_dest" value="<?php echo $recordData['multi_dest'][0];?>">
			<input type="hidden" name="num_dest" value="<?php echo $recordData['num_dest'][0];?>">




						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $recordData['staff::name_timesheet'][0];?> (<?php echo $recordData['staff::primary_SEDL_workgroup'][0];?>)</strong></td><td class="body" align="right" nowrap>Travel Authorization Status: <?php echo $recordData['approval_status'][0];?></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL AUTHORIZATION FORM</strong></td><td align="right">ID: <?php echo $recordData['travel_auth_ID'][0];?> <?php if($recordData['approval_status'][0] == 'Approved'){ ?>| <img src="/staff/sims/images/padlock.jpg" border="0" title="This travel authorization is locked."> <?php }?>| <a href="" target="_blank">Print</a> | <a href="menu_travel_admin.php">Close</a></td></tr>
						
						<tr><td colspan="2" align="right">
						<?php if($recordData['approval_status'][0] == 'Pending'){?><input type="submit" name="submit" value="Submit for Approval"><?php }?>
						
							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="4" align="center" style="color:#ffffff;background-color:#000000"><strong>SEDL TRAVEL AUTHORIZATION</strong></td></tr>
								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Name:</td>
								<td width="100%"><?php echo $recordData['staff::c_full_name_first_last'][0];?></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Title/Unit:</td>
								<td width="100%"><?php echo $recordData['staff::job_title'][0];?> (<?php echo $recordData['staff::primary_SEDL_workgroup'][0];?>)</td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Destination:</td>
								<td width="100%"><?php if($dest == 'm'){echo $recordData['c_multi_destinations_all_display_venues'][0];}else{echo $recordData['event_venue_city'][0];?>, <?php echo $recordData['event_venue_state'][0];}?></td>
								</tr>								


								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Period of Travel:</td>
								<td width="100%"><?php echo $recordData['leave_date_requested'][0];?> to <?php echo $recordData['return_date_requested'][0];?> <em>(Travel nights: <?php echo $recordData['c_travel_num_days'][0]-1;?>)</em></td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Purpose of Travel:</td>
								<td width="100%"><?php echo stripslashes($recordData['c_purpose_of_travel_csv'][0]);?></td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event Name:</td>
								<td width="100%"><?php echo stripslashes($recordData['event_name'][0]);?></td>
								</tr>
								
								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Description:</td>
								<td width="100%"><?php echo $recordData['purpose_of_travel_descr'][0];?></td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Mode of Transportation:</td>
								<td width="100%"><?php echo $recordData['c_trans_mode_description'][0];?></td>
								</tr>

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">CONUS Reference:</td>
								<td width="100%"><?php if($dest == 'm'){echo $recordData['c_multi_destinations_all_display_conus'][0];}else{echo $recordData['event_conus_city'][0];?>, <?php echo $recordData['event_venue_state'][0];}?><!-- | <a href="travel_admin.php?action=view&mod=1&travel_auth_ID=<?php echo $_GET['travel_auth_ID'];?>">Update CONUS city</a>--></td>
								</tr>
								

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">CONUS Per Diem Rates:</td>
								<td width="100%">

										<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr><td valign="top">
										
											<table cellpadding="6" cellspacing="0" bgcolor="#cccccc">

												<?php if($recordData['c_travel_rate_variance'][0] == '0'){ // IF TRAVEL RATES DON'T CHANGE DURING THIS TRAVEL PERIOD ?>
												<tr><td>CONUS Dest.</td><td align="right">Breakfast</td><td align="right">Lunch</td><td align="right">Dinner</td><td align="right">Incidentals</td><td align="right">Total MIE</td><td align="right">Lodging</td></tr>
												<tr bgcolor="#ffffff"><td><?php echo $recordData2['destination_city'][0].', '.$recordData2['destination_state'][0];?></td><td align="right"><?php echo $recordData2['mie_breakfast'][0];?></td><td align="right"><?php echo $recordData2['mie_lunch'][0];?></td><td align="right"><?php echo $recordData2['mie_dinner'][0];?></td><td align="right"><?php echo $recordData2['mie_incidentals'][0];?></td><td align="right"><?php echo $recordData2['mie'][0];?></td><td align="right"><?php echo $recordData2['lodging'][0];?></td></tr>
												<?php }else{ // IF TRAVEL RATES CHANGE DURING THIS TRAVEL PERIOD ?><p class="alert_small">NOTE: A per diem rate change takes place during this travel period. See rates per travel day below.</p>
												<tr><td>Date of Travel</td><td>CONUS Dest.</td><td align="right">Breakfast</td><td align="right">Lunch</td><td align="right">Dinner</td><td align="right">Incidentals</td><td align="right">Total MIE</td><td align="right">Lodging</td></tr>
												<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
													<tr <?php if($searchData2['c_duplicate_travel_day_count'][0] > 1){?>bgcolor="ebebeb"<?php }else{?>bgcolor="#ffffff"<?php }?>><td style="position:relative"><?php if($searchData2['c_duplicate_travel_day_count'][0] > 1){?><div style="font-size:10px;background-color:#fff000;position:absolute;border: 1px solid #a2c7ca;float:left;margin:-6px 0px 0px -110px;padding:2px">Travel Day <img src="images/small_right_arrow.png" style="margin:0px;padding:0px"></div><?php }?><?php echo $searchData2['travel_date'][0];?></td><td><?php echo $searchData2['destination_city'][0].', '.$searchData2['destination_state'][0];?></td><td align="right"><?php echo $searchData2['mie_breakfast'][0];?></td><td align="right"><?php echo $searchData2['mie_lunch'][0];?></td><td align="right"><?php echo $searchData2['mie_dinner'][0];?></td><td align="right"><?php echo $searchData2['mie_incidentals'][0];?></td><td align="right"><?php echo $searchData2['mie'][0];?></td><td align="right"><?php echo $searchData2['lodging'][0];?></td></tr>
													<?php } ?>

												<?php } ?>

											</table>
										

										</td</tr></table>			

								</td>
								</tr>

<?php if($dest == 's'){?>
								<tr>
								<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination: <?php echo $recordData['event_venue_city'][0].', '.$recordData['event_venue_state'][0].' - '.$recordData['travel_start_date'][0].' to '.$recordData['travel_end_date'][0];?></strong></td>
								</tr>


								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
								<td width="100%" colspan="2"><input type="text" name="preferred_hotel_name" size="30" value="<?php echo $recordData['preferred_hotel_name'][0];?>"> at <input type="text" name="hotel_rate" size="5" value="<?php echo $recordData['hotel_rate'][0];?>" style="text-align: right"> <strong>per night</strong> 
								<em>(<strong><?php echo $recordData['hotel_nights_requested'][0];?></strong> nights requested at <strong>$<?php echo $recordData['hotel_rate'][0];?></strong> per night = <strong>$<?php echo $recordData['hotel_rate'][0]*$recordData['hotel_nights_requested'][0];?></strong>)</em><p>
								Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel'][0];?></strong><br>
								Other justification for using this hotel? <strong><?php echo $recordData['preferred_hotel_other_justification'][0];?></strong><p>

										<table cellpadding="8" cellspacing="0">
										<tr><td bgcolor="#ffffff">
										<strong>Hotel Details:</strong><br>
										<?php echo stripslashes($recordData['preferred_hotel_name'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $google_preferred_hotel;?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
										<?php echo stripslashes($recordData['preferred_hotel_addr'][0]);?><br>
										<?php echo stripslashes($recordData['preferred_hotel_city'][0]);?>, <?php echo $recordData['preferred_hotel_state'][0];?> <?php echo $recordData['preferred_hotel_zip'][0];?><br>
										PH: <?php echo $recordData['preferred_hotel_phone'][0];?><br>
										Fax: <?php echo $recordData['preferred_hotel_fax'][0];?><p>
										Num nights: <strong><?php echo $recordData['hotel_nights_requested'][0];?></strong><br>
										Rate: <strong>$<?php echo $recordData['hotel_rate'][0];?></strong>
										</td></tr>
										</table><br>
										<input type="checkbox" name="hotel_direct_billed_to_SEDL" value="yes" <?php if($recordData['hotel_direct_billed_to_SEDL'][0] == 'yes'){echo ' checked';} ?>> Hotel is direct-billed to SEDL?
								
								</td>
								</tr>


<?php }elseif($dest == 'm'){?>



										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 1: <?php echo $recordData['event_venue_city1'][0].', '.$recordData['event_venue_state1'][0].' - '.$recordData['event_venue_city1_travel_start'][0].' to '.$recordData['event_venue_city1_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="100%" colspan="2"><input type="text" name="preferred_hotel_name1" size="30" value="<?php echo $recordData['preferred_hotel_name1'][0];?>"> at <input type="text" name="hotel_rate1" size="5" value="<?php echo $recordData['hotel_rate1'][0];?>" style="text-align: right"> <strong>per night</strong> 
										<em>(<strong><?php echo $recordData['hotel_nights_requested1'][0];?></strong> nights requested at <strong>$<?php echo $recordData['hotel_rate1'][0];?></strong> per night = <strong>$<?php echo $recordData['hotel_rate1'][0]*$recordData['hotel_nights_requested1'][0];?></strong>)</em><p>
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel1'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php echo $recordData['preferred_hotel_other_justification1'][0];?></strong><p>
		
												<table cellpadding="8" cellspacing="0">
												<tr><td bgcolor="#ffffff">
												<strong>Hotel Details:</strong><br>
												<?php echo stripslashes($recordData['preferred_hotel_name1'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name1'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr1'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city1'][0]);?>, <?php echo $recordData['preferred_hotel_state1'][0];?> <?php echo $recordData['preferred_hotel_zip1'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone1'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax1'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested1'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate1'][0];?></strong>
												</td></tr>
												</table><br>
												<input type="checkbox" name="hotel_direct_billed_to_SEDL1" value="yes" <?php if($recordData['hotel_direct_billed_to_SEDL1'][0] == 'yes'){echo ' checked';} ?>> Hotel is direct-billed to SEDL?
										
										</td>
										</tr>





		<?php if($num_dest > 1){ ?>
										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 2: <?php echo $recordData['event_venue_city2'][0].', '.$recordData['event_venue_state2'][0].' - '.$recordData['event_venue_city2_travel_start'][0].' to '.$recordData['event_venue_city2_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="100%" colspan="2"><input type="text" name="preferred_hotel_name2" size="30" value="<?php echo $recordData['preferred_hotel_name2'][0];?>"> at <input type="text" name="hotel_rate2" size="5" value="<?php echo $recordData['hotel_rate2'][0];?>" style="text-align: right"> <strong>per night</strong> 
										<em>(<strong><?php echo $recordData['hotel_nights_requested2'][0];?></strong> nights requested at <strong>$<?php echo $recordData['hotel_rate2'][0];?></strong> per night = <strong>$<?php echo $recordData['hotel_rate2'][0]*$recordData['hotel_nights_requested2'][0];?></strong>)</em><p>
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel2'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php echo $recordData['preferred_hotel_other_justification2'][0];?></strong><p>
		
												<table cellpadding="8" cellspacing="0">
												<tr><td bgcolor="#ffffff">
												<strong>Hotel Details:</strong><br>
												<?php echo stripslashes($recordData['preferred_hotel_name2'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name2'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr2'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city2'][0]);?>, <?php echo $recordData['preferred_hotel_state2'][0];?> <?php echo $recordData['preferred_hotel_zip2'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone2'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax2'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested2'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate2'][0];?></strong>
												</td></tr>
												</table><br>
												<input type="checkbox" name="hotel_direct_billed_to_SEDL2" value="yes" <?php if($recordData['hotel_direct_billed_to_SEDL2'][0] == 'yes'){echo ' checked';} ?>> Hotel is direct-billed to SEDL?
										
										</td>
										</tr>
		
		<?php } ?>
		
		
		<?php if($num_dest > 2){ ?>
										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 3: <?php echo $recordData['event_venue_city3'][0].', '.$recordData['event_venue_state3'][0].' - '.$recordData['event_venue_city3_travel_start'][0].' to '.$recordData['event_venue_city3_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="100%" colspan="2"><input type="text" name="preferred_hotel_name3" size="30" value="<?php echo $recordData['preferred_hotel_name3'][0];?>"> at <input type="text" name="hotel_rate3" size="5" value="<?php echo $recordData['hotel_rate3'][0];?>" style="text-align: right"> <strong>per night</strong> 
										<em>(<strong><?php echo $recordData['hotel_nights_requested3'][0];?></strong> nights requested at <strong>$<?php echo $recordData['hotel_rate3'][0];?></strong> per night = <strong>$<?php echo $recordData['hotel_rate3'][0]*$recordData['hotel_nights_requested3'][0];?></strong>)</em><p>
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel3'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php echo $recordData['preferred_hotel_other_justification3'][0];?></strong><p>
		
												<table cellpadding="8" cellspacing="0">
												<tr><td bgcolor="#ffffff">
												<strong>Hotel Details:</strong><br>
												<?php echo stripslashes($recordData['preferred_hotel_name3'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name3'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr3'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city3'][0]);?>, <?php echo $recordData['preferred_hotel_state3'][0];?> <?php echo $recordData['preferred_hotel_zip3'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone3'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax3'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested3'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate3'][0];?></strong>
												</td></tr>
												</table><br>
												<input type="checkbox" name="hotel_direct_billed_to_SEDL3" value="yes" <?php if($recordData['hotel_direct_billed_to_SEDL3'][0] == 'yes'){echo ' checked';} ?>> Hotel is direct-billed to SEDL?
										
										</td>
										</tr>
		
		<?php } ?>


		<?php if($num_dest > 3){ ?>
										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 4: <?php echo $recordData['event_venue_city4'][0].', '.$recordData['event_venue_state4'][0].' - '.$recordData['event_venue_city4_travel_start'][0].' to '.$recordData['event_venue_city4_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="100%" colspan="2"><input type="text" name="preferred_hotel_name4" size="30" value="<?php echo $recordData['preferred_hotel_name4'][0];?>"> at <input type="text" name="hotel_rate4" size="5" value="<?php echo $recordData['hotel_rate4'][0];?>" style="text-align: right"> <strong>per night</strong> 
										<em>(<strong><?php echo $recordData['hotel_nights_requested4'][0];?></strong> nights requested at <strong>$<?php echo $recordData['hotel_rate4'][0];?></strong> per night = <strong>$<?php echo $recordData['hotel_rate4'][0]*$recordData['hotel_nights_requested4'][0];?></strong>)</em><p>
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel4'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php echo $recordData['preferred_hotel_other_justification4'][0];?></strong><p>
		
												<table cellpadding="8" cellspacing="0">
												<tr><td bgcolor="#ffffff">
												<strong>Hotel Details:</strong><br>
												<?php echo stripslashes($recordData['preferred_hotel_name4'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name4'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr4'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city4'][0]);?>, <?php echo $recordData['preferred_hotel_state4'][0];?> <?php echo $recordData['preferred_hotel_zip4'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone4'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax4'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested4'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate4'][0];?></strong>
												</td></tr>
												</table><br>
												<input type="checkbox" name="hotel_direct_billed_to_SEDL4" value="yes" <?php if($recordData['hotel_direct_billed_to_SEDL4'][0] == 'yes'){echo ' checked';} ?>> Hotel is direct-billed to SEDL?
										
										</td>
										</tr>
		
		<?php } ?>


		<?php if($num_dest > 4){ ?>
										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 5: <?php echo $recordData['event_venue_city5'][0].', '.$recordData['event_venue_state5'][0].' - '.$recordData['event_venue_city5_travel_start'][0].' to '.$recordData['event_venue_city5_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="100%" colspan="2"><input type="text" name="preferred_hotel_name5" size="30" value="<?php echo $recordData['preferred_hotel_name5'][0];?>"> at <input type="text" name="hotel_rate5" size="5" value="<?php echo $recordData['hotel_rate5'][0];?>" style="text-align: right"> <strong>per night</strong> 
										<em>(<strong><?php echo $recordData['hotel_nights_requested5'][0];?></strong> nights requested at <strong>$<?php echo $recordData['hotel_rate5'][0];?></strong> per night = <strong>$<?php echo $recordData['hotel_rate5'][0]*$recordData['hotel_nights_requested5'][0];?></strong>)</em><p>
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel5'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php echo $recordData['preferred_hotel_other_justification5'][0];?></strong><p>
		
												<table cellpadding="8" cellspacing="0">
												<tr><td bgcolor="#ffffff">
												<strong>Hotel Details:</strong><br>
												<?php echo stripslashes($recordData['preferred_hotel_name5'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name5'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr5'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city5'][0]);?>, <?php echo $recordData['preferred_hotel_state5'][0];?> <?php echo $recordData['preferred_hotel_zip5'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone5'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax5'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested5'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate5'][0];?></strong>
												</td></tr>
												</table><br>
												<input type="checkbox" name="hotel_direct_billed_to_SEDL5" value="yes" <?php if($recordData['hotel_direct_billed_to_SEDL5'][0] == 'yes'){echo ' checked';} ?>> Hotel is direct-billed to SEDL?
										
										</td>
										</tr>
		
		<?php } ?>


		<?php if($num_dest > 5){ ?>
										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 6: <?php echo $recordData['event_venue_city6'][0].', '.$recordData['event_venue_state6'][0].' - '.$recordData['event_venue_city6_travel_start'][0].' to '.$recordData['event_venue_city6_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="100%" colspan="2"><input type="text" name="preferred_hotel_name6" size="30" value="<?php echo $recordData['preferred_hotel_name6'][0];?>"> at <input type="text" name="hotel_rate6" size="5" value="<?php echo $recordData['hotel_rate6'][0];?>" style="text-align: right"> <strong>per night</strong> 
										<em>(<strong><?php echo $recordData['hotel_nights_requested6'][0];?></strong> nights requested at <strong>$<?php echo $recordData['hotel_rate6'][0];?></strong> per night = <strong>$<?php echo $recordData['hotel_rate6'][0]*$recordData['hotel_nights_requested6'][0];?></strong>)</em><p>
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel6'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php echo $recordData['preferred_hotel_other_justification6'][0];?></strong><p>
		
												<table cellpadding="8" cellspacing="0">
												<tr><td bgcolor="#ffffff">
												<strong>Hotel Details:</strong><br>
												<?php echo stripslashes($recordData['preferred_hotel_name6'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name6'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr6'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city6'][0]);?>, <?php echo $recordData['preferred_hotel_state6'][0];?> <?php echo $recordData['preferred_hotel_zip6'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone6'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax6'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested6'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate6'][0];?></strong>
												</td></tr>
												</table><br>
												<input type="checkbox" name="hotel_direct_billed_to_SEDL6" value="yes" <?php if($recordData['hotel_direct_billed_to_SEDL6'][0] == 'yes'){echo ' checked';} ?>> Hotel is direct-billed to SEDL?
										
										</td>
										</tr>
		
		<?php } ?>





<?php }else{ ?>


<tr><td colspan="2">Error: $dest variable not set.</td></tr>

<?php } ?>


								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Total $$ Authorized:</td>
								<td width="100%"><input type="text" name="amount_authorized" size="5" value="<?php echo $recordData['amount_authorized'][0];?>"> <em>(Ex: airfare + hotel + per diem + parking + mileage + misc.)</em></td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Expense Estimator:</td>
								<td width="100%"><p class="alert_small">NOTE: Enter estimated expenses into the appropriate fields to update the total.</p>

										<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr><td valign="top">
										
											<table cellpadding="6" cellspacing="0" bgcolor="#cccccc">

												<tr><td>Lodging (<?php if($dest == 's'){echo $recordData['hotel_nights_requested'][0];}else{echo $recordData['c_hotel_nights_requested_multi'][0];}?>)</td><td align="right">MIE (<?php echo $recordData['c_travel_num_days'][0];?>)</td><td align="right">Airfare</td><td align="right">Car Rental</td><td align="right">Parking</td><td align="right">Mileage (.55)</td><td align="right">Misc</td><td align="right">Total</td></tr>
												<tr bgcolor="#ffffff">

												<?php if($dest == 's'){ ?>
													<td align="right" bgcolor="#ffffff"><input type="text" name="est_calc1" id="est_calc1" size="7" value="<?php echo $recordData['hotel_rate'][0]*$recordData['hotel_nights_requested'][0];?>" onFocus="startCalc();" onBlur="stopCalc();" style="text-align: right"></td>
												<?php }else{ ?>
													<td align="right" bgcolor="#ffffff"><input type="text" name="est_calc1" id="est_calc1" size="7" value="<?php echo $recordData['c_hotel_rate_all_total'][0];?>" onFocus="startCalc();" onBlur="stopCalc();" style="text-align: right"></td>
												<?php } ?>
													
												<?php if($dest == 's'){ ?>
													<td align="right" bgcolor="#ffffff"><input type="text" name="est_calc2" id="est_calc2" size="7" value="<?php echo $recordData2['mie'][0]*$recordData['c_travel_num_days'][0];?>" onFocus="startCalc();" onBlur="stopCalc();" style="text-align: right"></td>
												<?php }else{ ?>
													<td align="right" bgcolor="#ffffff"><input type="text" name="est_calc2" id="est_calc2" size="7" value="<?php echo $recordData['c_mie_total_all_travel'][0];?>" onFocus="startCalc();" onBlur="stopCalc();" style="text-align: right"></td>
												<?php } ?>
													<td align="right" bgcolor="#ffffff"><input type="text" name="est_calc3" id="est_calc3" size="7" onFocus="startCalc();" onBlur="stopCalc();" style="text-align: right"></td>
													<td align="right" bgcolor="#ffffff"><input type="text" name="est_calc4" id="est_calc4" size="7" onFocus="startCalc();" onBlur="stopCalc();" style="text-align: right"></td>
													<td align="right" bgcolor="#ffffff"><input type="text" name="est_calc5" id="est_calc5" size="7" onFocus="startCalc();" onBlur="stopCalc();" style="text-align: right"></td>
													<td align="right" bgcolor="#ffffff"><input type="text" name="est_calc6" id="est_calc6" size="7" value="<?php echo $recordData['trans_pers_veh_approx_mileage'][0]*.55;?>" onFocus="startCalc();" onBlur="stopCalc();" style="text-align: right"></td>
													<td align="right" bgcolor="#ffffff"><input type="text" name="est_calc7" id="est_calc7" size="7" onFocus="startCalc();" onBlur="stopCalc();" style="text-align: right"></td>
													<td align="right" bgcolor="#ffffff"><input type="text" name="est_calc_sum" id="est_calc_sum" size="7" DISABLED style="text-align: right"></td>
												</tr>

											</table>
										

										</td</tr></table>			

								</td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Transportation Details:</td>
								<td width="100%">
								<?php if($recordData['trans_pers_veh_utilized'][0] == 'yes'){echo 'Driving personal vehicle: <strong>yes</strong> | mileage: <strong>'.$recordData['trans_pers_veh_approx_mileage'][0].'</strong><br>';}?>
								<?php if($recordData['trans_airline_requested'][0] == 'yes'){echo 'Air travel required: <strong>yes</strong> | preferred carrier: <strong>'.$recordData['trans_airline_preferred_carrier'][0].'</strong> | charge to BTA: <strong>'.$recordData['trans_airline_bta_prepaid'][0].'</strong><br>';}?>
								<?php if($recordData['trans_rental_car_requested'][0] == 'yes'){echo 'Rental car requested: <strong>yes</strong> | number of days: <strong>'.$recordData['trans_rental_car_num_days_requested'][0].'</strong> | justification: <strong>'.$recordData['trans_rental_car_justification'][0].'</strong><br>';}?>
								<?php if($recordData['trans_traveling_with_other_staff'][0] == 'yes'){echo 'Traveling with other staff: <strong>yes</strong> | name(s): <strong>'.$recordData['trans_traveling_with_name'][0].'</strong><br>';}?>
								
								</td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Budget Code(s):</td>
								<td width="100%"><?php echo $recordData['c_budget_codes_csv'][0];?></td>
								</tr>
								
<?php if($recordData['budget_code_instructions'][0] != ''){?>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Budget Comments:</td>
								<td width="100%"><?php echo $recordData['budget_code_instructions'][0];?></td>
								</tr>
<?php }?>								

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Travel Advance/Comments:</td>
								<td width="100%" valign="top">
								
									<table cellpadding="4" cellspacing="0" width="100%">
										<tr><td width="50%" valign="top">
										Travel advances are subject to the following conditions:<br>
										<strong>Meals</strong>: not already provided for;<br>
										<strong>Hotel</strong>: nightly rate only if not direct-billed to SEDL;<br>
										<strong>Rental car</strong>: daily rate only or est. total provided by agency.<hr size="1"><p>
										Travel advance requested? <strong><?php if($recordData['travel_advance_requested'][0] == 'Yes'){echo 'Yes';}else{echo 'No';}?></strong>
										<?php if($recordData['travel_advance_requested'][0] == 'Yes'){?><p>
										Date needed: <input type="text" name="travel_advance_date_needed" size="10" value="<?php echo $recordData['travel_advance_date_needed'][0];?>"><p>
										<strong>Itemized Expenses:</strong><br>
										<input type="text" name="travel_advance_itemized_1" size="20" value="<?php echo $recordData['travel_advance_itemized_1'][0];?>"> <input type="text" name="travel_advance_itemized_1_amt" size="5" value="<?php echo $recordData['travel_advance_itemized_1_amt'][0];?>" style="text-align: right"><br>
										<input type="text" name="travel_advance_itemized_2" size="20" value="<?php echo $recordData['travel_advance_itemized_2'][0];?>"> <input type="text" name="travel_advance_itemized_2_amt" size="5" value="<?php echo $recordData['travel_advance_itemized_2_amt'][0];?>" style="text-align: right"><br>
										<input type="text" name="travel_advance_itemized_3" size="20" value="<?php echo $recordData['travel_advance_itemized_3'][0];?>"> <input type="text" name="travel_advance_itemized_3_amt" size="5" value="<?php echo $recordData['travel_advance_itemized_3_amt'][0];?>" style="text-align: right"><br>
										<input type="text" name="travel_advance_itemized_4" size="20" value="<?php echo $recordData['travel_advance_itemized_4'][0];?>"> <input type="text" name="travel_advance_itemized_4_amt" size="5" value="<?php echo $recordData['travel_advance_itemized_4_amt'][0];?>" style="text-align: right"><br>
										<input type="text" name="travel_advance_itemized_5" size="20" value="<?php echo $recordData['travel_advance_itemized_5'][0];?>"> <input type="text" name="travel_advance_itemized_5_amt" size="5" value="<?php echo $recordData['travel_advance_itemized_5_amt'][0];?>" style="text-align: right">
										<?php }?>
										
										</td><td valign="top" width="50%" >
										<strong>Comments:</strong><br>
										<textarea name="other_information" rows="8" cols="30" class="body"><?php echo $recordData['other_information'][0];?></textarea>
										</td>
										
										</tr>
									</table>
								
								</td>
								</tr>
								

							</table>


						

						</td></tr>


						</table>

			</td></tr>
			</table>

</form>

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


} elseif($action == 'view_tr') { //IF THE PBA OR SPVSR IS VIEWING A TRAVEL REQUEST

if($_GET['mod'] == 'reject'){

#################################################################
## START: UPDATE TRAVEL REQUEST - REJECTED ##
#################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_GET['rid']);
$update -> AddDBParam('approval_status_tr','Rejected');
$update -> AddDBParam('approval_status','Rejected');
$update -> AddDBParam('tr_reject_reason',$_GET['tr_reject_reason']);
$update -> AddDBParam('tr_rejected_by',$_SESSION['user_ID']);

$updateResult = $update -> FMEdit();
//echo '<p>$updateResult[errorCode]: '.$updateResult['errorCode'];
//echo '<p>$updateResult[foundCount]: '.$updateResult['foundCount'];
$updateData = current($updateResult['data']);
#################################################################
## END: UPDATE TRAVEL REQUEST - REJECTED ##
#################################################################

###############################################
## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
###############################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','REJECT_TR');
$newrecord -> AddDBParam('comment',$_GET['tr_reject_reason']);
$newrecord -> AddDBParam('travel_auth_ID',$updateData['travel_auth_ID'][0]);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
#############################################
## END: SAVE ACTION TO TRAVEL APPROVAL LOG ##
#############################################

if($updateData['multi_dest'][0] == 'yes'){
$view = 'view_multi';
}else{
$view = 'view';
}
##############################################################
## START: TRIGGER REJECTION E-MAIL TO STAFF MEMBER ##
##############################################################
//$to = 'eric.waters@sedl.org';
$to = $updateData['staff::email'][0];
$subject = 'Your travel request for '.$updateData['c_destinations_all_display_venues'][0].' has been rejected';
$message = 
'Dear '.$updateData['staff_full_name'][0].','."\n\n".

//'[E-mail was sent to: '.$updateData['staff::email'][0].'@sedl.org]'."\n\n".

'Your recent travel request for '.$updateData['c_destinations_all_display_venues_csv'][0].' has been rejected by '.$_SESSION['user_ID'].'.'."\n\n".

'------------------------------------------------------------'."\n".
' TRAVEL REQUEST DETAILS'."\n".
'------------------------------------------------------------'."\n".
'Event: '.stripslashes($updateData['event_name'][0])."\n".
'Destination: '.$updateData['c_destinations_all_display_venues_csv'][0]."\n".
'Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
'Rejected by: '.$updateData['tr_rejected_by'][0]."\n".
'Rejection reason: '.$updateData['tr_reject_reason'][0]."\n".
'------------------------------------------------------------'."\n\n".

'To update and/or re-submit this travel request, click here: '."\n".
'http://www.sedl.org/staff/sims/travel.php?travel_auth_ID='.$updateData['travel_auth_ID'][0].'&action='.$view."\n\n".
					
'------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'------------------------------------------------------------------------------------------------------------------';

$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['user_ID'].'@sedl.org'."\r\n".'Bcc: SIMS@sedl.org';

mail($to, $subject, $message, $headers);
############################################################
## END: TRIGGER REJECTION E-MAIL TO STAFF MEMBER ##
############################################################
}

#################################################################
## START: FIND SELECTED TRAVEL REQUEST ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','travel_authorizations');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('travel_auth_ID','=='.$_GET['travel_auth_ID']);
//$search -> AddDBParam('-lop','or');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//echo '<p>$recordData[event_name]: '.$recordData['event_name'][0];
$travel_admin = $recordData['staff_SJ_by_travel_admin_sims_ID::c_full_name_first_last'][0];
$pba = $recordData['staff::bgt_auth_primary_sims_user_ID'][0];
$request_owner_email = $recordData['staff::email'][0];
$google_event_name = str_replace(" ","+",$recordData['event_name'][0]);
$google_event_venue = str_replace(" ","+",$recordData['event_venue'][0]);
$google_preferred_hotel = str_replace(" ","+",$recordData['preferred_hotel_name'][0]);
$request_owner_sims_ID = $recordData['staff_sims_ID'][0];
$num_dest = $recordData['num_dest'][0];
if($recordData['multi_dest'][0] == 'yes'){
$dest = 'm';
}else{
$dest = 's';
}

###############################################################
## END: FIND SELECTED TRAVEL REQUEST ##
###############################################################


#################################################################
## START: FIND PER DIEM RATES FOR EACH TRAVEL DAY ##
#################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','travel_auth_days');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('travel_auth_ID','=='.$_GET['travel_auth_ID']);
//$search -> AddDBParam('-lop','or');

$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
//print_r ($searchResult2);
$recordData2 = current($searchResult2['data']);
###############################################################
## END: FIND PER DIEM RATES FOR EACH TRAVEL DAY ##
###############################################################


###############################################################
## START: GRAB ALL DESTINATIONS FOR THE SELECTED STATE ##
###############################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','travel_authorizations_CONUS_byDestinationState','all');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('state_abbrev',$recordData['event_venue_state'][0]);
//$search4 -> AddDBParam('season_begin_date',$_SESSION['leave_date_requested'],'lte');
//$search4 -> AddDBParam('season_end_date',$_SESSION['return_date_requested'], 'gte');

$search4 -> AddSortParam('state_abbrev','ascend');

$searchResult4 = $search4 -> FMFind();

//echo '<p>errorCode: '.$searchResult4['errorCode'];
//echo '<p>foundCount: '.$searchResult4['foundCount'];
//$recordData4 = current($searchResult4['data']);
#############################################################
## END: GRAB ALL DESTINATIONS FOR THE SELECTED STATE ##
#############################################################


?>

<html>
<head>
<title>SIMS: Travel Authorizations</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="javascript"> 
function toggle1() {
	var ele = document.getElementById("toggleText");
	var text = document.getElementById("displayText");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Reject Request";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "Cancel Rejection";
	}
} 

function confirmView() { 
	var answer = alert ("This travel authorization is currently being processed. You will receive an e-mail notification when your signature is required.")
	
	return false;
	
}

function preventSign() { 
	var answer = alert ("This travel request can only be approved by the budget authority.")
	
	return false;
	
}

function confirmSign() { 
	var answer = confirm ("Approve this travel request and submit for processing?")
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Authorizations</h1><hr /></td></tr>
			
			<tr><td colspan="2"><div class="alert_small">
			
			<b><?php if($recordData['signer_ID_spvsr'][0] == $_SESSION['user_ID']){ echo 'Supervisor';}else{echo 'Budget Authority';}?> (<?php echo $_SESSION['user_ID'];?>)</b>: 
			<?php if($recordData['approval_status_tr'][0] != 'Approved'){?>
			To approve this travel request, click the "Approve Request" button below.
								<div style="float:right;padding:0px;margin-top:-3px"><input id="displayText" type="button" name="reject" value="Reject Request" <?php if(($recordData['signer_ID_spvsr'][0] != $_SESSION['user_ID'])&&($recordData['signer_ID_pba'][0] != $_SESSION['user_ID'])){?>onclick="return preventSign()"<?php }else{?>onClick="toggle1();"<?php }?>></div>
								<div id="toggleText" style="display: none; background-color:#ebebeb; border: 1px dotted #999999;padding:8px 0px 0px 8px;margin:8px">
								<form method="get">
								<input type="hidden" name="action" value="view_tr">
								<input type="hidden" name="mod" value="reject">
								<input type="hidden" name="rid" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
								<input type="hidden" name="travel_auth_ID" value="<?php echo $recordData['travel_auth_ID'][0];?>">
								Enter a reason for rejecting this travel request: <input type="text" name="tr_reject_reason" size="50">
								<input type="submit" name="submit" value="Submit">
								</form>
								</div>

			<?php }elseif($recordData['approval_status_tr'][0] == 'Approved'){?>
			This travel request has been approved. <a href="travel_admin.php?action=approve_ta&travel_auth_ID=<?php echo $recordData['travel_auth_ID'][0];?>" <?php if($recordData['doc_status'][0] == '0'){echo 'onClick="return confirmView()"';}?>>View Travel Authorization</a>
			<?php }elseif($recordData['approval_status_tr'][0] == 'Rejected'){?>
			This travel request has been rejected by <strong><?php echo $recordData['tr_rejected_by'][0];?></strong>.
			<?php }else{ } ?>
			</div></td></tr>
			
			
			<tr><td colspan="2">

						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong>TRAVEL REQUEST <?php if($recordData['multi_dest'][0] == 'yes'){?>(M)<?php }?></strong></td><td align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Travel Request Status: <strong><?php if($recordData['approval_status_tr'][0] != 'Approved'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['approval_status_tr'][0]).'</span>';?></strong>
						<?php if($recordData['approval_status_tr'][0] !== 'Approved'){?>
						<div style="float:right;padding:0px;margin:0px" id="approve">
							<form style="padding:0px 0px 0px 6px;margin:0px" name="travel_auth_submit">
							<input type="hidden" name="travel_auth_ID" value="<?php echo $recordData['travel_auth_ID'][0];?>">
							<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
							<input type="hidden" name="action" value="approve_tr">
							<input type="hidden" name="multi_dest" value="<?php echo $recordData['multi_dest'][0];?>">
							<input type="hidden" name="num_dest" value="<?php echo $recordData['num_dest'][0];?>">

							<?php if(($_SESSION['user_ID'] == $recordData['signer_ID_spvsr'][0])&&($recordData['signer_ID_spvsr'][0] !== $recordData['signer_ID_pba'][0])){?>
	
								<input type="hidden" name="role" value="spvsr">
							
							<?php }else{ ?>
							
								<input type="hidden" name="role" value="pba">

							<?php } ?>
							
							<input style="padding:0px;margin:0px" type="submit" name="submit" value="Approve Request" <?php if(($recordData['signer_ID_spvsr'][0] !== $_SESSION['user_ID'])&&($recordData['signer_ID_pba'][0] !== $_SESSION['user_ID'])){?>onclick="return preventSign()"<?php }else{?>onClick="return confirmSign()"<?php }?>>
							</form>
							</div>
						<?php } ?>	
							</td></tr>
						<tr><td style="vertical-align:text-top;padding-bottom:0px" nowrap><h2 style="margin-bottom:0px"><?php echo $recordData['staff::name_timesheet'][0];?> (<?php echo $recordData['staff::primary_SEDL_workgroup'][0];?>)<br><?php if($recordData['multi_dest'][0] == 'yes'){echo $recordData['c_event_venue_city_state1'][0].'**';}else{echo $recordData['c_event_venue_city_state'][0];}?></h2><strong><?php echo $recordData['leave_date_requested'][0].' - '.$recordData['return_date_requested'][0];?></strong></td><td style="vertical-align:text-top" align="right">ID: <?php echo $recordData['travel_auth_ID'][0];?> | <a href="menu_travel_admin_spvsr.php">Close</a></td></tr>
						

						<tr><td colspan="2" align="right" style="padding-top:0px">

							<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
								<tr><td colspan="4" style="color:#ffffff;background-color:#000000"><strong>DESTINATION AND DESCRIPTION OF REQUESTED TRAVEL</strong></td></tr>

								

								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Name:</td>
								<td width="100%" colspan="3"><?php echo $recordData['staff::c_full_name_first_last'][0];?></td>
								</tr>								
								
								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Title/Unit:</td>
								<td width="100%" colspan="3"><?php echo $recordData['staff::job_title'][0];?> (<?php echo $recordData['staff::primary_SEDL_workgroup'][0];?>)</td>
								</tr>								


								<tr>
								<td class="body" nowrap align="right" bgcolor="#ebebeb">Destination:</td>
								<td width="100%" colspan="3"><?php if($dest == 'm'){echo $recordData['c_multi_destinations_all_display_venues'][0];}else{echo $recordData['event_venue_city'][0];?>, <?php echo $recordData['event_venue_state'][0];}?></td>
								</tr>								


								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Period of Travel:</td>
								<td width="100%" colspan="3"><?php echo $recordData['leave_date_requested'][0];?> to <?php echo $recordData['return_date_requested'][0];?> <em>(Travel nights: <?php echo $recordData['c_travel_num_days'][0]-1;?>)</em></td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Purpose of Travel:</td>
								<td width="100%" colspan="3"><?php echo stripslashes($recordData['c_purpose_of_travel_csv'][0]);?></td>
								</tr>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Description:</td>
								<td width="100%" colspan="3"><?php echo $recordData['purpose_of_travel_descr'][0];?></td>
								</tr>


<?php if($dest == 's'){?>
								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Mode of Transportation:</td>
								<td width="100%"colspan="3"><?php echo $recordData['c_trans_mode_description'][0];?></td>
								</tr>



										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="4" style="background-color:#a4d6a7"><strong>Destination: <?php echo $recordData['event_venue_city'][0].', '.$recordData['event_venue_state'][0].' - '.$recordData['leave_date_requested'][0].' to '.$recordData['return_date_requested'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event:</td>
										<td width="100%" style="vertical-align:text-top" colspan="2"><?php echo stripslashes($recordData['event_name'][0]);?>
										</td>
										<td rowspan="3" style="vertical-align:text-top;padding-top:0px">

											<table style="width:300px;padding:2px">
											<tr><td style="padding:2px;border:0px" colspan="2"><strong>CONUS Hotel Rates for <?php echo $recordData['event_venue_state'][0];?></strong> | <a href="http://www.gsa.gov/portal/category/104711" target="_blank">Search GSA</a></td></tr>
											<tr><td style="background-color:#ebebeb;padding:2px">Destination</td><td style="text-align:right;background-color:#ebebeb;padding:2px">Rate</td></tr>
											
											<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
											<tr><td style="padding:2px"><?php echo $searchData4['destination'][0];?></td><td style="text-align:right;padding:2px"><?php echo $searchData4['lodging'][0];?></td></tr>
											<?php } ?>
											
											</table>

										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Venue:</td>
										<td width="100%" style="vertical-align:text-top">
		
												<?php echo stripslashes($recordData['event_venue'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_addr'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_city'][0]);?>, <?php echo $recordData['event_venue_state'][0];?>
										
										</td>
										
										
										</tr>

										<tr>
										<td nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="100%" style="vertical-align:text-top">
										
												<?php echo stripslashes($recordData['preferred_hotel_name'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city'][0]);?>, <?php echo $recordData['preferred_hotel_state'][0];?> <?php echo $recordData['preferred_hotel_zip'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate'][0];?></strong>
												<p>
												<?php if($recordData['hotel_direct_billed_to_SEDL'][0] == 'yes'){echo 'Hotel is direct-billed to SEDL<br>';}?> 
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php if($recordData['preferred_hotel_other_justification'][0] !== ''){echo $recordData['preferred_hotel_other_justification'][0];}else{echo 'N/A';}?></strong><p>
										
										</td>
										</tr>


<?php }elseif($dest == 'm'){?>



										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 1: <?php echo $recordData['event_venue_city1'][0].', '.$recordData['event_venue_state1'][0].' - '.$recordData['event_venue_city1_travel_start'][0].' to '.$recordData['event_venue_city1_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event:</td>
										<td width="100%" colspan="2"><?php echo stripslashes($recordData['event_name1'][0]);?>
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Venue:</td>
										<td width="100%" colspan="2">
		
												<?php echo stripslashes($recordData['event_venue1'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_addr1'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_city1'][0]);?>, <?php echo $recordData['event_venue_state1'][0];?>
										
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="100%" colspan="2">
										
												<?php echo stripslashes($recordData['preferred_hotel_name1'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name1'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr1'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city1'][0]);?>, <?php echo $recordData['preferred_hotel_state1'][0];?> <?php echo $recordData['preferred_hotel_zip1'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone1'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax1'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested1'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate1'][0];?></strong>
												<p>
												<?php if($recordData['hotel_direct_billed_to_SEDL1'][0] == 'yes'){echo 'Hotel is direct-billed to SEDL<br>';}?> 
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel1'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php if($recordData['preferred_hotel_other_justification1'][0] !== ''){echo $recordData['preferred_hotel_other_justification1'][0];}else{echo 'N/A';}?></strong><p>
										
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Transportation Details:</td>
										<td width="100%" colspan="3">
										<?php if($recordData['trans_pers_veh_utilized1'][0] == 'Yes'){echo 'Driving personal vehicle: <strong>Yes</strong> | mileage: <strong>'.$recordData['trans_pers_veh_approx_mileage1'][0].'</strong><br>';}?>
										<?php if($recordData['trans_airline_requested1'][0] == 'Yes'){echo 'Air travel required: <strong>Yes</strong> | preferred carrier: <strong>'.$recordData['trans_airline_preferred_carrier1'][0].'</strong> | charge to BTA: <strong>'.$recordData['trans_airline_bta_prepaid1'][0].'</strong><br>';}?>
										<?php if($recordData['trans_rental_car_requested1'][0] == 'Yes'){echo 'Rental car requested: <strong>Yes</strong> | number of days: <strong>'.$recordData['trans_rental_car_num_days_requested1'][0].'</strong> | justification: <strong>'.$recordData['trans_rental_car_justification1'][0].'</strong><br>';}?>
										<?php if($recordData['trans_traveling_with_other_staff1'][0] == 'Yes'){echo 'Traveling with other staff: <strong>Yes</strong> | name(s): <strong>'.$recordData['trans_traveling_with_name1'][0].'</strong><br>';}?>
										</td>
										</tr>




		<?php if($num_dest > 1){ ?>
										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 2: <?php echo $recordData['event_venue_city2'][0].', '.$recordData['event_venue_state2'][0].' - '.$recordData['event_venue_city2_travel_start'][0].' to '.$recordData['event_venue_city2_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event:</td>
										<td width="100%" colspan="2"><?php echo stripslashes($recordData['event_name2'][0]);?>
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Venue:</td>
										<td width="100%" colspan="2">
		
												<?php echo stripslashes($recordData['event_venue2'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_addr2'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_city2'][0]);?>, <?php echo $recordData['event_venue_state2'][0];?>
										
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="200%" colspan="2">
										
												<?php echo stripslashes($recordData['preferred_hotel_name2'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name2'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr2'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city2'][0]);?>, <?php echo $recordData['preferred_hotel_state2'][0];?> <?php echo $recordData['preferred_hotel_zip2'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone2'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax2'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested2'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate2'][0];?></strong>
												<p>
												<?php if($recordData['hotel_direct_billed_to_SEDL2'][0] == 'yes'){echo 'Hotel is direct-billed to SEDL<br>';}?> 
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel2'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php if($recordData['preferred_hotel_other_justification2'][0] !== ''){echo $recordData['preferred_hotel_other_justification2'][0];}else{echo 'N/A';}?></strong><p>
										
										</td>
										</tr>
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Transportation Details:</td>
										<td width="100%" colspan="3">
										<?php if($recordData['trans_pers_veh_utilized2'][0] == 'Yes'){echo 'Driving personal vehicle: <strong>Yes</strong> | mileage: <strong>'.$recordData['trans_pers_veh_approx_mileage2'][0].'</strong><br>';}?>
										<?php if($recordData['trans_airline_requested2'][0] == 'Yes'){echo 'Air travel required: <strong>Yes</strong> | preferred carrier: <strong>'.$recordData['trans_airline_preferred_carrier2'][0].'</strong> | charge to BTA: <strong>'.$recordData['trans_airline_bta_prepaid2'][0].'</strong><br>';}?>
										<?php if($recordData['trans_rental_car_requested2'][0] == 'Yes'){echo 'Rental car requested: <strong>Yes</strong> | number of days: <strong>'.$recordData['trans_rental_car_num_days_requested2'][0].'</strong> | justification: <strong>'.$recordData['trans_rental_car_justification2'][0].'</strong><br>';}?>
										<?php if($recordData['trans_traveling_with_other_staff2'][0] == 'Yes'){echo 'Traveling with other staff: <strong>Yes</strong> | name(s): <strong>'.$recordData['trans_traveling_with_name2'][0].'</strong><br>';}?>
										</td>
										</tr>

		<?php } ?>
		
		
		<?php if($num_dest > 2){ ?>
										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 3: <?php echo $recordData['event_venue_city3'][0].', '.$recordData['event_venue_state3'][0].' - '.$recordData['event_venue_city3_travel_start'][0].' to '.$recordData['event_venue_city3_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event:</td>
										<td width="300%" colspan="2"><?php echo stripslashes($recordData['event_name3'][0]);?>
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Venue:</td>
										<td width="300%" colspan="2">
		
												<?php echo stripslashes($recordData['event_venue3'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_addr3'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_city3'][0]);?>, <?php echo $recordData['event_venue_state3'][0];?>
										
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="300%" colspan="2">
										
												<?php echo stripslashes($recordData['preferred_hotel_name3'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name3'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr3'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city3'][0]);?>, <?php echo $recordData['preferred_hotel_state3'][0];?> <?php echo $recordData['preferred_hotel_zip3'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone3'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax3'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested3'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate3'][0];?></strong>
												<p>
												<?php if($recordData['hotel_direct_billed_to_SEDL3'][0] == 'yes'){echo 'Hotel is direct-billed to SEDL<br>';}?> 
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel3'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php if($recordData['preferred_hotel_other_justification3'][0] !== ''){echo $recordData['preferred_hotel_other_justification3'][0];}else{echo 'N/A';}?></strong><p>
										
										</td>
										</tr>
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Transportation Details:</td>
										<td width="100%" colspan="3">
										<?php if($recordData['trans_pers_veh_utilized3'][0] == 'Yes'){echo 'Driving personal vehicle: <strong>Yes</strong> | mileage: <strong>'.$recordData['trans_pers_veh_approx_mileage3'][0].'</strong><br>';}?>
										<?php if($recordData['trans_airline_requested3'][0] == 'Yes'){echo 'Air travel required: <strong>Yes</strong> | preferred carrier: <strong>'.$recordData['trans_airline_preferred_carrier3'][0].'</strong> | charge to BTA: <strong>'.$recordData['trans_airline_bta_prepaid3'][0].'</strong><br>';}?>
										<?php if($recordData['trans_rental_car_requested3'][0] == 'Yes'){echo 'Rental car requested: <strong>Yes</strong> | number of days: <strong>'.$recordData['trans_rental_car_num_days_requested3'][0].'</strong> | justification: <strong>'.$recordData['trans_rental_car_justification3'][0].'</strong><br>';}?>
										<?php if($recordData['trans_traveling_with_other_staff3'][0] == 'Yes'){echo 'Traveling with other staff: <strong>Yes</strong> | name(s): <strong>'.$recordData['trans_traveling_with_name3'][0].'</strong><br>';}?>
										</td>
										</tr>

		<?php } ?>


		<?php if($num_dest > 3){ ?>
										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 4: <?php echo $recordData['event_venue_city4'][0].', '.$recordData['event_venue_state4'][0].' - '.$recordData['event_venue_city4_travel_start'][0].' to '.$recordData['event_venue_city4_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event:</td>
										<td width="400%" colspan="2"><?php echo stripslashes($recordData['event_name4'][0]);?>
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Venue:</td>
										<td width="400%" colspan="2">
		
												<?php echo stripslashes($recordData['event_venue4'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_addr4'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_city4'][0]);?>, <?php echo $recordData['event_venue_state4'][0];?>
										
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="400%" colspan="2">
										
												<?php echo stripslashes($recordData['preferred_hotel_name4'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name4'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr4'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city4'][0]);?>, <?php echo $recordData['preferred_hotel_state4'][0];?> <?php echo $recordData['preferred_hotel_zip4'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone4'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax4'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested4'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate4'][0];?></strong>
												<p>
												<?php if($recordData['hotel_direct_billed_to_SEDL4'][0] == 'yes'){echo 'Hotel is direct-billed to SEDL<br>';}?> 
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel4'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php if($recordData['preferred_hotel_other_justification4'][0] !== ''){echo $recordData['preferred_hotel_other_justification4'][0];}else{echo 'N/A';}?></strong><p>
										
										</td>
										</tr>
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Transportation Details:</td>
										<td width="100%" colspan="3">
										<?php if($recordData['trans_pers_veh_utilized5'][0] == 'Yes'){echo 'Driving personal vehicle: <strong>Yes</strong> | mileage: <strong>'.$recordData['trans_pers_veh_approx_mileage5'][0].'</strong><br>';}?>
										<?php if($recordData['trans_airline_requested5'][0] == 'Yes'){echo 'Air travel required: <strong>Yes</strong> | preferred carrier: <strong>'.$recordData['trans_airline_preferred_carrier5'][0].'</strong> | charge to BTA: <strong>'.$recordData['trans_airline_bta_prepaid5'][0].'</strong><br>';}?>
										<?php if($recordData['trans_rental_car_requested5'][0] == 'Yes'){echo 'Rental car requested: <strong>Yes</strong> | number of days: <strong>'.$recordData['trans_rental_car_num_days_requested5'][0].'</strong> | justification: <strong>'.$recordData['trans_rental_car_justification5'][0].'</strong><br>';}?>
										<?php if($recordData['trans_traveling_with_other_staff5'][0] == 'Yes'){echo 'Traveling with other staff: <strong>Yes</strong> | name(s): <strong>'.$recordData['trans_traveling_with_name5'][0].'</strong><br>';}?>
										</td>
										</tr>

		<?php } ?>


		<?php if($num_dest > 4){ ?>
										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 5: <?php echo $recordData['event_venue_city5'][0].', '.$recordData['event_venue_state5'][0].' - '.$recordData['event_venue_city5_travel_start'][0].' to '.$recordData['event_venue_city5_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event:</td>
										<td width="500%" colspan="2"><?php echo stripslashes($recordData['event_name5'][0]);?>
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Venue:</td>
										<td width="500%" colspan="2">
		
												<?php echo stripslashes($recordData['event_venue5'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_addr5'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_city5'][0]);?>, <?php echo $recordData['event_venue_state5'][0];?>
										
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="500%" colspan="2">
										
												<?php echo stripslashes($recordData['preferred_hotel_name5'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name5'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr5'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city5'][0]);?>, <?php echo $recordData['preferred_hotel_state5'][0];?> <?php echo $recordData['preferred_hotel_zip5'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone5'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax5'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested5'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate5'][0];?></strong>
												<p>
												<?php if($recordData['hotel_direct_billed_to_SEDL5'][0] == 'yes'){echo 'Hotel is direct-billed to SEDL<br>';}?> 
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel5'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php if($recordData['preferred_hotel_other_justification5'][0] !== ''){echo $recordData['preferred_hotel_other_justification5'][0];}else{echo 'N/A';}?></strong><p>
										
										</td>
										</tr>
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Transportation Details:</td>
										<td width="100%" colspan="3">
										<?php if($recordData['trans_pers_veh_utilized5'][0] == 'Yes'){echo 'Driving personal vehicle: <strong>Yes</strong> | mileage: <strong>'.$recordData['trans_pers_veh_approx_mileage5'][0].'</strong><br>';}?>
										<?php if($recordData['trans_airline_requested5'][0] == 'Yes'){echo 'Air travel required: <strong>Yes</strong> | preferred carrier: <strong>'.$recordData['trans_airline_preferred_carrier5'][0].'</strong> | charge to BTA: <strong>'.$recordData['trans_airline_bta_prepaid5'][0].'</strong><br>';}?>
										<?php if($recordData['trans_rental_car_requested5'][0] == 'Yes'){echo 'Rental car requested: <strong>Yes</strong> | number of days: <strong>'.$recordData['trans_rental_car_num_days_requested5'][0].'</strong> | justification: <strong>'.$recordData['trans_rental_car_justification5'][0].'</strong><br>';}?>
										<?php if($recordData['trans_traveling_with_other_staff5'][0] == 'Yes'){echo 'Traveling with other staff: <strong>Yes</strong> | name(s): <strong>'.$recordData['trans_traveling_with_name5'][0].'</strong><br>';}?>
										</td>
										</tr>

		<?php } ?>


		<?php if($num_dest > 5){ ?>
										<tr>
										<td class="body" nowrap valign="top"  bgcolor="#ebebeb" colspan="2" style="background-color:#a4d6a7"><strong>Destination 6: <?php echo $recordData['event_venue_city6'][0].', '.$recordData['event_venue_state6'][0].' - '.$recordData['event_venue_city6_travel_start'][0].' to '.$recordData['event_venue_city6_travel_end'][0];?></strong></td>
										</tr>
		
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Event:</td>
										<td width="600%" colspan="2"><?php echo stripslashes($recordData['event_name6'][0]);?>
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Venue:</td>
										<td width="600%" colspan="2">
		
												<?php echo stripslashes($recordData['event_venue6'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_addr6'][0]);?><br>
												<?php echo stripslashes($recordData['event_venue_city6'][0]);?>, <?php echo $recordData['event_venue_state6'][0];?>
										
										</td>
										</tr>

										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Hotel:</td>
										<td width="600%" colspan="2">
										
												<?php echo stripslashes($recordData['preferred_hotel_name6'][0]);?> | <a href="http://www.google.com/search?source=ig&hl=en&rlz=&=&q=<?php echo $recordData['preferred_hotel_name6'][0];?>&btnG=Google+Search&aq=f" target="_blank">Google this hotel</a><br>
												<?php echo stripslashes($recordData['preferred_hotel_addr6'][0]);?><br>
												<?php echo stripslashes($recordData['preferred_hotel_city6'][0]);?>, <?php echo $recordData['preferred_hotel_state6'][0];?> <?php echo $recordData['preferred_hotel_zip6'][0];?><br>
												PH: <?php echo $recordData['preferred_hotel_phone6'][0];?><br>
												Fax: <?php echo $recordData['preferred_hotel_fax6'][0];?><p>
												Num nights: <strong><?php echo $recordData['hotel_nights_requested6'][0];?></strong><br>
												Rate: <strong>$<?php echo $recordData['hotel_rate6'][0];?></strong>
												<p>
												<?php if($recordData['hotel_direct_billed_to_SEDL6'][0] == 'yes'){echo 'Hotel is direct-billed to SEDL<br>';}?> 
										Is this the conference or meeting hotel? <strong><?php echo $recordData['preferred_hotel_is_conf_hotel6'][0];?></strong><br>
										Other justification for using this hotel? <strong><?php if($recordData['preferred_hotel_other_justification6'][0] !== ''){echo $recordData['preferred_hotel_other_justification6'][0];}else{echo 'N/A';}?></strong><p>
										
										</td>
										</tr>
		
										<tr>
										<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Transportation Details:</td>
										<td width="100%" colspan="3">
										<?php if($recordData['trans_pers_veh_utilized6'][0] == 'Yes'){echo 'Driving personal vehicle: <strong>Yes</strong> | mileage: <strong>'.$recordData['trans_pers_veh_approx_mileage6'][0].'</strong><br>';}?>
										<?php if($recordData['trans_airline_requested6'][0] == 'Yes'){echo 'Air travel required: <strong>Yes</strong> | preferred carrier: <strong>'.$recordData['trans_airline_preferred_carrier6'][0].'</strong> | charge to BTA: <strong>'.$recordData['trans_airline_bta_prepaid6'][0].'</strong><br>';}?>
										<?php if($recordData['trans_rental_car_requested6'][0] == 'Yes'){echo 'Rental car requested: <strong>Yes</strong> | number of days: <strong>'.$recordData['trans_rental_car_num_days_requested6'][0].'</strong> | justification: <strong>'.$recordData['trans_rental_car_justification6'][0].'</strong><br>';}?>
										<?php if($recordData['trans_traveling_with_other_staff6'][0] == 'Yes'){echo 'Traveling with other staff: <strong>Yes</strong> | name(s): <strong>'.$recordData['trans_traveling_with_name6'][0].'</strong><br>';}?>
										</td>
										</tr>

		<?php } ?>





<?php }else{ ?>


<tr><td colspan="2">Error: $dest variable not set.</td></tr>

<?php } ?>


<?php if($recordData['num_dest'][0] == 1){?>								

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Transportation Details:</td>
								<td width="100%" colspan="3">
								<?php if($recordData['trans_pers_veh_utilized'][0] == 'Yes'){echo 'Driving personal vehicle: <strong>Yes</strong> | mileage: <strong>'.$recordData['trans_pers_veh_approx_mileage'][0].'</strong><br>';}?>
								<?php if($recordData['trans_airline_requested'][0] == 'Yes'){echo 'Air travel required: <strong>Yes</strong> | preferred carrier: <strong>'.$recordData['trans_airline_preferred_carrier'][0].'</strong> | charge to BTA: <strong>'.$recordData['trans_airline_bta_prepaid'][0].'</strong><br>';}?>
								<?php if($recordData['trans_rental_car_requested'][0] == 'Yes'){echo 'Rental car requested: <strong>Yes</strong> | number of days: <strong>'.$recordData['trans_rental_car_num_days_requested'][0].'</strong> | justification: <strong>'.$recordData['trans_rental_car_justification'][0].'</strong><br>';}?>
								<?php if($recordData['trans_traveling_with_other_staff'][0] == 'Yes'){echo 'Traveling with other staff: <strong>Yes</strong> | name(s): <strong>'.$recordData['trans_traveling_with_name'][0].'</strong><br>';}?>
								</td>
								</tr>

<?php } ?>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb" style="border-top:8px solid #a4d6a7">Budget Code(s):</td>
								<td width="100%" colspan="3"  style="border-top:8px solid #a4d6a7"><?php echo $recordData['c_budget_codes_csv_w_initials'][0];?></td>
								</tr>
								
<?php if($recordData['budget_code_instructions'][0] != ''){?>

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Budget Comments:</td>
								<td width="100%" colspan="3"><?php echo $recordData['budget_code_instructions'][0];?></td>
								</tr>
<?php }?>								

								<tr>
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Other Comments:</td>
								<td width="100%" valign="top" colspan="3">
								
								<?php echo $recordData['other_information'][0];?>
								
								</td>
								</tr>
								

							</table>


						

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


} elseif($action == 'approve_ta') { //IF A REQUIRED SIGNER IS VIEWING THE TRAVEL AUTHORIZATION

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

$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
//print_r ($searchResult2);
$recordData2 = current($searchResult2['data']);
###############################################################
## END: FIND PER DIEM RATES FOR EACH TRAVEL DAY ##
###############################################################

if($sign == '1'){ // BGT AUTH OR SUPERVISOR SIGNED THE DOCUMENT - TIER 1 SIGNERS

		$trigger = rand();
		
		####################################################
		## START: FIND ALL APPROVAL ROLES FOR THIS TA ##
		####################################################
		$search4 = new FX($serverIP,$webCompanionPort);
		$search4 -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
		$search4 -> SetDBPassword($webPW,$webUN);
		$search4 -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
		//$search4 -> AddDBParam('signer_ID','=='.$signer);
		//$search4 -> AddDBParam('-lop','or');
		
		$searchResult4 = $search4 -> FMFind();
		
		//echo '<p>$searchResult4[errorCode]: '.$searchResult4['errorCode'];
		//echo '<p>$searchResult4[foundCount]: '.$searchResult4['foundCount'];
		//print_r ($searchResult4);
		//$recordData4 = current($searchResult4['data']);
		##################################################
		## END: FIND ALL APPROVAL ROLES FOR THIS TA ##
		##################################################

		foreach($searchResult4['data'] as $key => $searchData4) { // LOOP THROUGH APPROVAL ROLES AND UPDATE STATUS FOR EACH IDENTICAL SIGNER
		
			if(($searchData4['signer_ID'][0] == $signer)&&($searchData4['signer_tier'][0] != '3')){
			########################################################################
			## START: UPDATE SIGNER STATUS FOR ALL APPROVAL ROLES FOR THIS SIGNER ##
			########################################################################
			$update = new FX($serverIP,$webCompanionPort);
			$update -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
			$update -> SetDBPassword($webPW,$webUN);
			$update -> AddDBParam('-recid',$searchData4['c_cwp_row_ID'][0]);
			$update -> AddDBParam('signer_status','1');
			$update -> AddDBParam('signer_timestamp_trigger',$trigger);
			
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
			$newrecord -> AddDBParam('action',$updateData['c_signer_log_tag'][0]);
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
			$newrecord -> AddDBParam('action','SIGN_TRAVEL_AUTH_BA');
			$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
			$newrecord -> AddDBParam('object_ID',$updateData['travel_auth_ID'][0]);
			$newrecord -> AddDBParam('affected_row_ID',$searchData4['c_cwp_row_ID'][0]);
			$newrecord -> AddDBParam('ip_address',$ip);
			$newrecordResult = $newrecord -> FMNew();
			//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
			//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
			###############################################
			## END: SAVE ACTION TO SIMS AUDIT LOG ##
			###############################################
			

			}
		
		}


		##########################################################################################
		## START: FORWARD TRAVEL AUTH FOR EO/FISCAL APPROVAL, IF ALL TIER 1 SIGNERS HAVE SIGNED ##
		##########################################################################################
		if($updateData['c_all_signers_status_tier_1'][0] == '1'){ // ALL TIER 1 SIGNERS HAVE SIGNED THE DOCUMENT

		//if($updateData['multi_dest'][0] == 'yes'){$destination = stripslashes($updateData['event_venue_city1'][0]).', '.$updateData['event_venue_state1'][0].'**';}else{$destination =	stripslashes($updateData['event_venue_city'][0]).', '.$updateData['event_venue_state'][0];}	// SET $destination STRING FOR E-MAIL NOTIFICATION
		$destination =	stripslashes($updateData['travel_authorizations::event_venue_city'][0]).', '.$updateData['travel_authorizations::event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION
		
			if($updateData['c_all_signers_count_tier_2'][0] > 0){ // IF THERE ARE TIER 2 SIGNERS (CPO OR CEO)
			
				foreach($searchResult4['data'] as $key => $searchData4) { // LOOP THROUGH APPROVAL ROLES AND SEND E-MAIL NOTIFICATION TO ALL TIER 2 SIGNERS

					if(($searchData4['signer_tier'][0] == 2)&&($searchData4['signer_status'][0] == 0)&&($searchData4['signer_ID'][0] !== $signer)){
					####################################################################################################
					## START: TRIGGER NOTIFICATION E-MAIL TO EO/FISCAL APPROVERS (ONLY THOSE WHO HAVE NOT SIGNED YET) ##
					####################################################################################################
					//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
					$to = $searchData4['signer_ID'][0].'@sedl.org';
					$subject = 'A travel authorization for '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been submitted and requires your approval.';
					$message = 
					'Dear '.$searchData4['signer_ID'][0].','."\n\n".
					
					//'[E-mail was sent to: '.$searchData4['signer_ID'][0].'@sedl.org]'."\n\n".
		
					'A travel authorization (TA) for staff member '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been submitted by ('.$updateData['travel_authorizations::travel_admin_sims_user_ID'][0].') and requires your approval.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' TRAVEL DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
					' Event: '.stripslashes($updateData['travel_authorizations::event_name'][0])."\n".
					' Destination: '.$destination."\n".
					' Date(s) of Travel: '.$updateData['travel_authorizations::leave_date_requested'][0].' to '.$updateData['travel_authorizations::return_date_requested'][0]."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view and approve this travel authorization, click here: '."\n".
					'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
					##################################################################################################
					## END: TRIGGER NOTIFICATION E-MAIL TO EO/FISCAL APPROVERS (ONLY THOSE WHO HAVE NOT SIGNED YET) ##
					##################################################################################################
					}
				
				}
				
				if( // CHECK FOR CEO SPECIAL APPROVALS
				(($updateData['travel_authorizations::ceo_approval_rate_required'][0] == '1')&&($updateData['travel_authorizations::ceo_approval_rate_status'][0] == '0'))|| // CEO APPROVAL REQUIRED FOR HOTEL RATE 
				(($updateData['travel_authorizations::travel_advance_requested'][0] == 'Yes')&&($updateData['travel_authorizations::ceo_approval_advance_status'][0] == '0'))|| // CEO APPROVAL REQUIRED FOR TRAVEL ADVANCE 
				(($updateData['travel_authorizations::req_registration_fee'][0] > 0)&&($updateData['travel_authorizations::ceo_approval_reg_fee_status'][0] == '0')) // CEO APPROVAL REQUIRED FOR REGISTRATION FEE 
				){ 
				if(($updateData['travel_authorizations::ceo_approval_rate_required'][0] == '1')&&($updateData['travel_authorizations::ceo_approval_rate_status'][0] == '0')){$approval_reason = 'Hotel Rate'; $i=', ';} // RECORD APPROVAL REASON 1
				if(($updateData['travel_authorizations::travel_advance_requested'][0] == 'Yes')&&($updateData['travel_authorizations::ceo_approval_advance_status'][0] == '0')){$approval_reason .= $i.'Travel Advance'; $i=', ';} // RECORD APPROVAL REASON 2
				if(($updateData['travel_authorizations::req_registration_fee'][0] > 0)&&($updateData['travel_authorizations::ceo_approval_reg_fee_status'][0] == '0')){$approval_reason .= $i.'Registration Fee'; $i=', ';} // RECORD APPROVAL REASON 3
				#############################################################
				## START: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
				#############################################################
				$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
				$to = 'whoover@sedl.org';
				$subject = 'A travel authorization for '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' requires your approval for: '.$approval_reason;
				$message = 
				'Dear CEO,'."\n\n".
				
				//'[E-mail was sent to: whoover@sedl.org]'."\n\n".
	
				'A travel authorization (TA) for staff member '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been submitted by ('.$updateData['travel_authorizations::travel_admin_sims_user_ID'][0].') and requires your approval.'."\n\n".
				
				'------------------------------------------------------------'."\n".
				' TRAVEL DETAILS'."\n".
				'------------------------------------------------------------'."\n".
				' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
				' Event: '.stripslashes($updateData['travel_authorizations::event_name'][0])."\n".
				' Destination: '.$destination."\n".
				' Date(s) of Travel: '.$updateData['travel_authorizations::leave_date_requested'][0].' to '.$updateData['travel_authorizations::return_date_requested'][0]."\n".
				' Approval Required for: '.$approval_reason."\n".
				'------------------------------------------------------------'."\n\n".
				
				'To view and approve this travel authorization, click here: '."\n".
				'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
									
				'------------------------------------------------------------------------------------------------------------------'."\n".
				'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
				'------------------------------------------------------------------------------------------------------------------';
				
				$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
				
				mail($to, $subject, $message, $headers);
					
				###########################################################
				## END: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
				###########################################################
				}

		
			}else{ // THERE ARE NO TIER 2 SIGNERS (CPO OR CEO)
			
				if( // CHECK FOR CEO SPECIAL APPROVALS
				(($updateData['travel_authorizations::ceo_approval_rate_required'][0] == '1')&&($updateData['travel_authorizations::ceo_approval_rate_status'][0] == '0'))|| // CEO APPROVAL REQUIRED FOR HOTEL RATE 
				(($updateData['travel_authorizations::travel_advance_requested'][0] == 'Yes')&&($updateData['travel_authorizations::ceo_approval_advance_status'][0] == '0'))|| // CEO APPROVAL REQUIRED FOR TRAVEL ADVANCE 
				(($updateData['travel_authorizations::req_registration_fee'][0] > 0)&&($updateData['travel_authorizations::ceo_approval_reg_fee_status'][0] == '0')) // CEO APPROVAL REQUIRED FOR REGISTRATION FEE 
				){ 
				if(($updateData['travel_authorizations::ceo_approval_rate_required'][0] == '1')&&($updateData['travel_authorizations::ceo_approval_rate_status'][0] == '0')){$approval_reason = 'Hotel Rate'; $i=', ';} // RECORD APPROVAL REASON 1
				if(($updateData['travel_authorizations::travel_advance_requested'][0] == 'Yes')&&($updateData['travel_authorizations::ceo_approval_advance_status'][0] == '0')){$approval_reason .= $i.'Travel Advance'; $i=', ';} // RECORD APPROVAL REASON 2
				if(($updateData['travel_authorizations::req_registration_fee'][0] > 0)&&($updateData['travel_authorizations::ceo_approval_reg_fee_status'][0] == '0')){$approval_reason .= $i.'Registration Fee'; $i=', ';} // RECORD APPROVAL REASON 3
				#############################################################
				## START: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
				#############################################################
				//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
				$to = 'whoover@sedl.org';
				$subject = 'A travel authorization for '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' requires your approval for: '.$approval_reason;
				$message = 
				'Dear CEO,'."\n\n".
				
				//'[E-mail was sent to: whoover@sedl.org]'."\n\n".
	
				'A travel authorization (TA) for staff member '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been submitted by ('.$updateData['travel_authorizations::travel_admin_sims_user_ID'][0].') and requires your approval.'."\n\n".
				
				'------------------------------------------------------------'."\n".
				' TRAVEL DETAILS'."\n".
				'------------------------------------------------------------'."\n".
				' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
				' Event: '.stripslashes($updateData['travel_authorizations::event_name'][0])."\n".
				' Destination: '.$destination."\n".
				' Date(s) of Travel: '.$updateData['travel_authorizations::leave_date_requested'][0].' to '.$updateData['travel_authorizations::return_date_requested'][0]."\n".
				' Approval Required for: '.$approval_reason."\n".
				'------------------------------------------------------------'."\n\n".
				
				'To view and approve this travel authorization, click here: '."\n".
				'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
									
				'------------------------------------------------------------------------------------------------------------------'."\n".
				'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
				'------------------------------------------------------------------------------------------------------------------';
				
				$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
				
				mail($to, $subject, $message, $headers);
					
				###########################################################
				## END: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
				###########################################################
				}else{ // THERE ARE NO CEO SPECIAL APPROVALS REQUIRED - SEND NOTIFICATION TO CFO FOR FINAL APPROVAL
				
					#############################################################
					## START: TRIGGER NOTIFICATION E-MAIL TO CFO TO APPROVE TA ##
					#############################################################
					//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
					$to = 'sferguso@sedl.org';
					$subject = 'A travel authorization for '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' requires your approval.';
					$message = 
					'Dear CFO,'."\n\n".
					
					//'[E-mail was sent to: sferguso@sedl.org]'."\n\n".
		
					'A travel authorization (TA) for staff member '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been submitted by ('.$updateData['travel_authorizations::travel_admin_sims_user_ID'][0].') and requires your final approval.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' TRAVEL DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
					' Event: '.stripslashes($updateData['travel_authorizations::event_name'][0])."\n".
					' Destination: '.$destination."\n".
					' Date(s) of Travel: '.$updateData['travel_authorizations::leave_date_requested'][0].' to '.$updateData['travel_authorizations::return_date_requested'][0]."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view and approve this travel authorization, click here: '."\n".
					'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
					###########################################################
					## END: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
					###########################################################

				}
			
			}

		}
		
		########################################################################################
		## END: FORWARD TRAVEL AUTH FOR EO/FISCAL APPROVAL, IF ALL TIER 1 SIGNERS HAVE SIGNED ##
		########################################################################################
		


}

if(($sign == '2')&&($_SESSION['user_ID'] == 'whoover')){ // CEO SIGNED THE DOCUMENT TO APPROVE HOTEL OVERAGE

		//$trigger = rand();
		//$vc = $_GET['vc'];
		//$today = date("m/d/Y");
		#######################################################
		## START: UPDATE SIGNER STATUS FOR CEO HOTEL OVERAGE ##
		#######################################################
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$travel_auth_row_ID);
		
		$update -> AddDBParam('ceo_approval_rate_status','1');

/*
		if($vc == '1'){
		$update -> AddDBParam('ceo_approval_rate_status1','1');
		$update -> AddDBParam('ceo_approval_rate_date1',$today);
		}elseif($vc == '2'){
		$update -> AddDBParam('ceo_approval_rate_status2','1');
		$update -> AddDBParam('ceo_approval_rate_date2',$today);
		}elseif($vc == '3'){
		$update -> AddDBParam('ceo_approval_rate_status3','1');
		$update -> AddDBParam('ceo_approval_rate_date3',$today);
		}elseif($vc == '4'){
		$update -> AddDBParam('ceo_approval_rate_status4','1');
		$update -> AddDBParam('ceo_approval_rate_date4',$today);
		}elseif($vc == '5'){
		$update -> AddDBParam('ceo_approval_rate_status5','1');
		$update -> AddDBParam('ceo_approval_rate_date5',$today);
		}elseif($vc == '6'){
		$update -> AddDBParam('ceo_approval_rate_status6','1');
		$update -> AddDBParam('ceo_approval_rate_date6',$today);
		}else{
		$update -> AddDBParam('ceo_approval_rate_status','1');
		$update -> AddDBParam('ceo_approval_rate_date',$today);
		}
*/
		$updateResult = $update -> FMEdit();
		$updateData = current($updateResult['data']);
		//echo '<p>$updateResult[errorCode]: '.$updateResult['errorCode'];
		//echo '<p>$updateResult[foundCount]: '.$updateResult['foundCount'];

		#####################################################
		## END: UPDATE SIGNER STATUS FOR CEO HOTEL OVERAGE ##
		#####################################################
		
		###############################################
		## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
		###############################################
		$newrecord = new FX($serverIP,$webCompanionPort);
		$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
		$newrecord -> SetDBPassword($webPW,$webUN);
		$newrecord -> AddDBParam('user',$signer);
		$newrecord -> AddDBParam('action','APPROVE_Rate');
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
		$newrecord -> AddDBParam('user',$signer);
		$newrecord -> AddDBParam('action','SIGN_TRAVEL_AUTH_CEO_RATE');
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
		
		
		if( // IF THIS IS THE LAST REQUIRED SIGNATURE BEFORE CFO FINAL APPROVAL
			($updateData['travel_auth_approvals::c_all_signers_status_tier_1'][0] == '1') && // ALL TIER 1 SIGNERS HAVE SIGNED
			(($updateData['travel_auth_approvals::c_all_signers_status_tier_2'][0] == '1')||($updateData['travel_auth_approvals::c_all_signers_count_tier_2'][0] == '0')) && // ALL TIER 2 SIGNERS HAVE SIGNED OR NO TIER 2 SIGNERS REQUIRED
			(($updateData['ceo_approval_rate_required'][0] == '0')||($updateData['ceo_approval_rate_status'][0] == '1')) && // CEO HAS APPROVED HOTEL RATE OR TA DOES NOT REQUIRE HOTEL RATE APPROVAL 
			(($updateData['travel_advance_requested'][0] == '')||($updateData['ceo_approval_advance_status'][0] == '1')) && // CEO HAS APPROVED TRAVEL ADVANCE OR TRAVEL ADVANCE NOT REQUESTED
			(($updateData['req_registration_fee'][0] == '')||($updateData['ceo_approval_reg_fee_status'][0] == '1')) // CEO HAS APPROVED REGISTRATION FEE OR NO REGISTRATION FEE REQUIRED
		){ // ALL REGULAR SIGNERS HAVE SIGNED THE DOCUMENT AND NO ADDITIONAL CEO APPROVAL REQUIRED
		
		
		$destination =	stripslashes($updateData['event_venue_city'][0]).', '.$updateData['event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION
		
		
					#############################################################
					## START: TRIGGER NOTIFICATION E-MAIL TO CFO TO APPROVE TA ##
					#############################################################
					//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
					$to = 'sferguso@sedl.org';
					$subject = 'A travel authorization for '.stripslashes($updateData['staff_full_name'][0]).' requires your approval.';
					$message = 
					'Dear CFO,'."\n\n".
					
					//'[E-mail was sent to: sferguso@sedl.org]'."\n\n".
		
					'A travel authorization (TA) for staff member '.stripslashes($updateData['staff_full_name'][0]).' has been submitted by ('.$updateData['travel_admin_sims_user_ID'][0].') and requires your final approval.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' TRAVEL DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
					' Event: '.stripslashes($updateData['event_name'][0])."\n".
					' Destination: '.$destination."\n".
					' Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view and approve this travel authorization, click here: '."\n".
					'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
					###########################################################
					## END: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
					###########################################################
		
			
		}

}

if(($sign == '3')&&($_SESSION['user_ID'] == 'whoover')){ // CEO SIGNED THE DOCUMENT TO APPROVE TRAVEL ADVANCE

		//$trigger = rand();
		//$today = date("m/d/Y");
		########################################################
		## START: UPDATE SIGNER STATUS FOR CEO TRAVEL ADVANCE ##
		########################################################
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$travel_auth_row_ID);
		$update -> AddDBParam('ceo_approval_advance_status','1');
		
		$updateResult = $update -> FMEdit();
		$updateData = current($updateResult['data']);
		######################################################
		## END: UPDATE SIGNER STATUS FOR CEO TRAVEL ADVANCE ##
		######################################################

		###############################################
		## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
		###############################################
		$newrecord = new FX($serverIP,$webCompanionPort);
		$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
		$newrecord -> SetDBPassword($webPW,$webUN);
		$newrecord -> AddDBParam('user',$signer);
		$newrecord -> AddDBParam('action','APPROVE_Advance');
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
		$newrecord -> AddDBParam('user',$signer);
		$newrecord -> AddDBParam('action','SIGN_TRAVEL_AUTH_CEO_ADVANCE');
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
		


		if( // IF THIS IS THE LAST REQUIRED SIGNATURE BEFORE CFO FINAL APPROVAL
			($updateData['travel_auth_approvals::c_all_signers_status_tier_1'][0] == '1') && // ALL TIER 1 SIGNERS HAVE SIGNED
			(($updateData['travel_auth_approvals::c_all_signers_status_tier_2'][0] == '1')||($updateData['travel_auth_approvals::c_all_signers_count_tier_2'][0] == '0')) && // ALL TIER 2 SIGNERS HAVE SIGNED OR NO TIER 2 SIGNERS REQUIRED
			(($updateData['ceo_approval_rate_required'][0] == '0')||($updateData['ceo_approval_rate_status'][0] == '1')) && // CEO HAS APPROVED HOTEL RATE OR TA DOES NOT REQUIRE HOTEL RATE APPROVAL 
			(($updateData['travel_advance_requested'][0] == '')||($updateData['ceo_approval_advance_status'][0] == '1')) && // CEO HAS APPROVED TRAVEL ADVANCE OR TRAVEL ADVANCE NOT REQUESTED
			(($updateData['req_registration_fee'][0] == '')||($updateData['ceo_approval_reg_fee_status'][0] == '1')) // CEO HAS APPROVED REGISTRATION FEE OR NO REGISTRATION FEE REQUIRED
		){ // ALL REGULAR SIGNERS HAVE SIGNED THE DOCUMENT AND NO ADDITIONAL CEO APPROVAL REQUIRED
		
		
		$destination =	stripslashes($updateData['event_venue_city'][0]).', '.$updateData['event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION
		
		
					#############################################################
					## START: TRIGGER NOTIFICATION E-MAIL TO CFO TO APPROVE TA ##
					#############################################################
					//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
					$to = 'sferguso@sedl.org';
					$subject = 'A travel authorization for '.stripslashes($updateData['staff_full_name'][0]).' requires your approval.';
					$message = 
					'Dear CFO,'."\n\n".
					
					//'[E-mail was sent to: sferguso@sedl.org]'."\n\n".
		
					'A travel authorization (TA) for staff member '.stripslashes($updateData['staff_full_name'][0]).' has been submitted by ('.$updateData['travel_admin_sims_user_ID'][0].') and requires your final approval.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' TRAVEL DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
					' Event: '.stripslashes($updateData['event_name'][0])."\n".
					' Destination: '.$destination."\n".
					' Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view and approve this travel authorization, click here: '."\n".
					'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
					###########################################################
					## END: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
					###########################################################
		
			
		}


}

if(($sign == '4')&&($_SESSION['user_ID'] == 'whoover')){ // CEO SIGNED THE DOCUMENT TO APPROVE REGISTRATION FEE

		//$trigger = rand();
		//$today = date("m/d/Y");
		#################################################
		## START: UPDATE SIGNER STATUS FOR CEO REG FEE ##
		#################################################
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$travel_auth_row_ID);
		$update -> AddDBParam('ceo_approval_reg_fee_status','1');
		
		$updateResult = $update -> FMEdit();
		$updateData = current($updateResult['data']);
		###############################################
		## END: UPDATE SIGNER STATUS FOR CEO REG FEE ##
		###############################################

		###############################################
		## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
		###############################################
		$newrecord = new FX($serverIP,$webCompanionPort);
		$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
		$newrecord -> SetDBPassword($webPW,$webUN);
		$newrecord -> AddDBParam('user',$signer);
		$newrecord -> AddDBParam('action','APPROVE_Reg_Fee');
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
		$newrecord -> AddDBParam('user',$signer);
		$newrecord -> AddDBParam('action','SIGN_TRAVEL_AUTH_CEO_REG_FEE');
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



		if( // IF THIS IS THE LAST REQUIRED SIGNATURE BEFORE CFO FINAL APPROVAL
			($updateData['travel_auth_approvals::c_all_signers_status_tier_1'][0] == '1') && // ALL TIER 1 SIGNERS HAVE SIGNED
			(($updateData['travel_auth_approvals::c_all_signers_status_tier_2'][0] == '1')||($updateData['travel_auth_approvals::c_all_signers_count_tier_2'][0] == '0')) && // ALL TIER 2 SIGNERS HAVE SIGNED OR NO TIER 2 SIGNERS REQUIRED
			(($updateData['ceo_approval_rate_required'][0] == '0')||($updateData['ceo_approval_rate_status'][0] == '1')) && // CEO HAS APPROVED HOTEL RATE OR TA DOES NOT REQUIRE HOTEL RATE APPROVAL 
			(($updateData['travel_advance_requested'][0] == '')||($updateData['ceo_approval_advance_status'][0] == '1')) && // CEO HAS APPROVED TRAVEL ADVANCE OR TRAVEL ADVANCE NOT REQUESTED
			(($updateData['req_registration_fee'][0] == '')||($updateData['ceo_approval_reg_fee_status'][0] == '1')) // CEO HAS APPROVED REGISTRATION FEE OR NO REGISTRATION FEE REQUIRED
		){ // ALL REGULAR SIGNERS HAVE SIGNED THE DOCUMENT AND NO ADDITIONAL CEO APPROVAL REQUIRED
		
		
		$destination =	stripslashes($updateData['event_venue_city'][0]).', '.$updateData['event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION
		
		
					#############################################################
					## START: TRIGGER NOTIFICATION E-MAIL TO CFO TO APPROVE TA ##
					#############################################################
					//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
					$to = 'sferguso@sedl.org';
					$subject = 'A travel authorization for '.stripslashes($updateData['staff_full_name'][0]).' requires your approval.';
					$message = 
					'Dear CFO,'."\n\n".
					
					//'[E-mail was sent to: sferguso@sedl.org]'."\n\n".
		
					'A travel authorization (TA) for staff member '.stripslashes($updateData['staff_full_name'][0]).' has been submitted by ('.$updateData['travel_admin_sims_user_ID'][0].') and requires your final approval.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' TRAVEL DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
					' Event: '.stripslashes($updateData['event_name'][0])."\n".
					' Destination: '.$destination."\n".
					' Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view and approve this travel authorization, click here: '."\n".
					'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
					###########################################################
					## END: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
					###########################################################
		
			
		}


}


if($sign == '1b'){ // CPO OR CEO SIGNED THE DOCUMENT - TIER 2 SIGNERS

		$trigger = rand();
		
		####################################################
		## START: FIND ALL APPROVAL ROLES FOR THIS SIGNER ##
		####################################################
		$search4 = new FX($serverIP,$webCompanionPort);
		$search4 -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
		$search4 -> SetDBPassword($webPW,$webUN);
		$search4 -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
		$search4 -> AddDBParam('signer_ID','=='.$signer);
		//$search4 -> AddDBParam('-lop','or');
		
		$searchResult4 = $search4 -> FMFind();
		
		//echo '<p>$searchResult4[errorCode]: '.$searchResult4['errorCode'];
		//echo '<p>$searchResult4[foundCount]: '.$searchResult4['foundCount'];
		//print_r ($searchResult4);
		//$recordData4 = current($searchResult4['data']);
		##################################################
		## END: FIND ALL APPROVAL ROLES FOR THIS SIGNER ##
		##################################################

		foreach($searchResult4['data'] as $key => $searchData4) { // LOOP THROUGH APPROVAL ROLES AND UPDATE STATUS FOR EACH IDENTICAL SIGNER

		########################################################################
		## START: UPDATE SIGNER STATUS FOR ALL APPROVAL ROLES FOR THIS SIGNER ##
		########################################################################
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$searchData4['c_cwp_row_ID'][0]);
		$update -> AddDBParam('signer_status','1');
		$update -> AddDBParam('signer_timestamp_trigger',$trigger);
		
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
		$newrecord -> AddDBParam('action','SIGN_EO');
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
		$newrecord -> AddDBParam('action','SIGN_TRAVEL_AUTH_EO');
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



		if( // IF THIS IS THE LAST REQUIRED SIGNATURE BEFORE CFO FINAL APPROVAL
			($updateData['c_all_signers_status_tier_1'][0] == '1') && // ALL TIER 1 SIGNERS HAVE SIGNED
			(($updateData['c_all_signers_status_tier_2'][0] == '1')||($updateData['c_all_signers_count_tier_2'][0] == '0')) && // ALL TIER 2 SIGNERS HAVE SIGNED OR NO TIER 2 SIGNERS REQUIRED
			(($updateData['travel_authorizations::ceo_approval_rate_required'][0] == '0')||($updateData['travel_authorizations::ceo_approval_rate_status'][0] == '1')) && // CEO HAS APPROVED HOTEL RATE OR TA DOES NOT REQUIRE HOTEL RATE APPROVAL 
			(($updateData['travel_authorizations::travel_advance_requested'][0] == '')||($updateData['travel_authorizations::ceo_approval_advance_status'][0] == '1')) && // CEO HAS APPROVED TRAVEL ADVANCE OR TRAVEL ADVANCE NOT REQUESTED
			(($updateData['travel_authorizations::req_registration_fee'][0] == '')||($updateData['travel_authorizations::ceo_approval_reg_fee_status'][0] == '1')) // CEO HAS APPROVED REGISTRATION FEE OR NO REGISTRATION FEE REQUIRED
		){ // ALL REGULAR SIGNERS HAVE SIGNED THE DOCUMENT AND NO ADDITIONAL CEO APPROVAL REQUIRED
		
		
		$destination =	stripslashes($updateData['travel_authorizations::event_venue_city'][0]).', '.$updateData['travel_authorizations::event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION
		
		
					#############################################################
					## START: TRIGGER NOTIFICATION E-MAIL TO CFO TO APPROVE TA ##
					#############################################################
					//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
					$to = 'sferguso@sedl.org';
					$subject = 'A travel authorization for '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' requires your approval.';
					$message = 
					'Dear CFO,'."\n\n".
					
					//'[E-mail was sent to: sferguso@sedl.org]'."\n\n".
		
					'A travel authorization (TA) for staff member '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been submitted by ('.$updateData['travel_authorizations::travel_admin_sims_user_ID'][0].') and requires your final approval.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' TRAVEL DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
					' Event: '.stripslashes($updateData['travel_authorizations::event_name'][0])."\n".
					' Destination: '.$destination."\n".
					' Date(s) of Travel: '.$updateData['travel_authorizations::leave_date_requested'][0].' to '.$updateData['travel_authorizations::return_date_requested'][0]."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view and approve this travel authorization, click here: '."\n".
					'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
					###########################################################
					## END: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
					###########################################################
		
			
		}
		
}		

if(($sign == '1c')&&($_SESSION['user_ID'] == 'sferguso')){ // CFO SIGNED THE DOCUMENT - FINAL APPROVAL

		$trigger = rand();

		########################################################################
		## START: UPDATE SIGNER STATUS FOR CFO FINAL APPROVAL ##
		########################################################################
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$travel_auth_row_ID);
		$update -> AddDBParam('signer_status','1');
		$update -> AddDBParam('signer_timestamp_trigger',$trigger);
		
		$updateResult = $update -> FMEdit();
		$updateData = current($updateResult['data']);
		######################################################################
		## END: UPDATE SIGNER STATUS FOR CFO FINAL APPROVAL ##
		######################################################################
		
		########################################################################
		## START: UPDATE TRAVEL AUTHORIZATION - APPROVED ##
		########################################################################
		$update2 = new FX($serverIP,$webCompanionPort);
		$update2 -> SetDBData('SIMS_2.fp7','travel_authorizations');
		$update2 -> SetDBPassword($webPW,$webUN);
		$update2 -> AddDBParam('-recid',$updateData['travel_authorizations::c_row_ID_cwp'][0]);
		$update2 -> AddDBParam('approval_status','Approved');
		$update2 -> AddDBParam('cfo_approval_status','1');
		
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
		$newrecord -> AddDBParam('user',$signer);
		$newrecord -> AddDBParam('action','APPROVE_TA');
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
		$newrecord -> AddDBParam('action','APPROVE_TRAVEL_AUTH_CFO');
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

		$destination =	stripslashes($updateData['travel_authorizations::event_venue_city'][0]).', '.$updateData['travel_authorizations::event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION

		#############################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO AA - CFO APPROVED ##
		#############################################################
		//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
		$to = $updateData2['travel_admin_sims_user_ID'][0].'@sedl.org';
		$subject = 'A travel authorization for '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been approved by the CFO.';
		$message = 
		'Travel Admin,'."\n\n".
		
		//'[E-mail was sent to: traveladmin@sedl.org]'."\n\n".

		'A travel authorization (TA) for staff member '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been approved by the CFO.'."\n\n".
		
		'------------------------------------------------------------'."\n".
		' TRAVEL DETAILS'."\n".
		'------------------------------------------------------------'."\n".
		' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
		' Event: '.stripslashes($updateData['travel_authorizations::event_name'][0])."\n".
		' Destination: '.$destination."\n".
		' Date(s) of Travel: '.$updateData['travel_authorizations::leave_date_requested'][0].' to '.$updateData['travel_authorizations::return_date_requested'][0]."\n".
		'------------------------------------------------------------'."\n\n".
		
		'To view and process this travel authorization, click here: '."\n".
		'fmp7://198.214.140.248/CC_dms.fp7'."\n\n".
							
		'------------------------------------------------------------------------------------------------------------------'."\n".
		'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
		'------------------------------------------------------------------------------------------------------------------';
		
		$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org'."\r\n".'Cc: connie.laizure@sedl.org';
		
		mail($to, $subject, $message, $headers);
			
		###########################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO AA - CFO APPROVED ##
		###########################################################


		if($updateData2['trans_airline_bta_prepaid'][0] == 'Yes'){ // CC LORI FORADORY IF AIRLINE CHARGED TO SEDL'S BTA ACCOUNT

		############################################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO AS ACCT SPECIALIST - AIRLINE BTA ##
		############################################################################
		//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
		$to = 'lori.foradory@sedl.org';
		$subject = 'A travel authorization for '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been approved by the CFO (Air charged to BTA)';
		$message = 
		'Accounting Specialist,'."\n\n".
		
		//'[E-mail was sent to: traveladmin@sedl.org]'."\n\n".

		'A travel authorization (TA) for staff member '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been approved by the CFO and contains air travel charged to the SEDL BTA.'."\n\n".
		
		'------------------------------------------------------------'."\n".
		' TRAVEL DETAILS'."\n".
		'------------------------------------------------------------'."\n".
		' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
		' Event: '.stripslashes($updateData['travel_authorizations::event_name'][0])."\n".
		' Destination: '.$destination."\n".
		' Date(s) of Travel: '.$updateData['travel_authorizations::leave_date_requested'][0].' to '.$updateData['travel_authorizations::return_date_requested'][0]."\n".
		'------------------------------------------------------------'."\n\n".
		
		'To view and print this travel authorization, click here: '."\n".
		'fmp7://198.214.140.248/CC_dms.fp7'."\n\n".
							
		'------------------------------------------------------------------------------------------------------------------'."\n".
		'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
		'------------------------------------------------------------------------------------------------------------------';
		
		$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
		
		mail($to, $subject, $message, $headers);
			
		############################################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO AS ACCT SPECIALIST - AIRLINE BTA ##
		############################################################################



		}



}



#####################################
## START: FIND TRAVEL AUTH SIGNERS ##
#####################################

	$search3 = new FX($serverIP,$webCompanionPort);
	$search3 -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
	$search3 -> SetDBPassword($webPW,$webUN);
	$search3 -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
	//$search3 -> AddDBParam('-lop','or');
	
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
	$current_user_status = $searchData3['signer_status'][0];
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

if($recordData['c_attachments_count'][0] > 0){ // THE SELECTED TA HAS ATTACHMENTS
#################################################################
## START: FIND ATTACHMENTS RELATED TO THIS TA ##
#################################################################
$search5 = new FX($serverIP,$webCompanionPort);
$search5 -> SetDBData('SIMS_2.fp7','travel_auth_attachments');
$search5 -> SetDBPassword($webPW,$webUN);
$search5 -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
//$search5 -> AddDBParam('-lop','or');

//$search5 -> AddSortParam('leave_hrs_date','ascend');


$searchResult5 = $search5 -> FMFind();

//echo '<p>$searchResult5[errorCode]: '.$searchResult5['errorCode'];
//echo '<p>$searchResult5[foundCount]: '.$searchResult5['foundCount'];
//print_r ($searchResult5);
$recordData5 = current($searchResult5['data']);
###############################################################
## END: FIND ATTACHMENTS RELATED TO THIS TA ##
###############################################################

}

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
	alert ("This signature box is reserved for another budget authority. To sign this travel authorization, click the signature space with your ID.")
	return false;
}

function confirmSign() { 
	var answer = confirm ("Approve this travel authorization?")
	if (!answer) {
	return false;
	}
}

function confirmSign2() { 
	var answer = confirm ("CEO: Approve this hotel rate?")
	if (!answer) {
	return false;
	}
}

function confirmSign3() { 
	var answer = confirm ("CEO: Approve this travel advance?")
	if (!answer) {
	return false;
	}
}

function confirmSign4() { 
	var answer = confirm ("CEO: Approve this event registration fee?")
	if (!answer) {
	return false;
	}
}

function preventSignceo() { 
	alert ("This signature box is reserved for CEO approval. To sign this travel authorization, click the signature space with your ID.")
	return false;
}

function wrongSigner() { 
	alert ("This signature box is reserved for another budget authority. To sign this travel authorization, click the signature space with your ID.")
	return false;
}

function ExCOnly() { 
	var answer2 = confirm ("This section reserved for the CEO.")
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

			<tr><td colspan="2"><p class="alert_small"><b>Supervisor/Budget Authority (<?php echo $_SESSION['user_ID'];?>)</b>: You have successfully approved this travel authorization (TA). <img src="/staff/sims/images/green_check.png"> | <a href="menu_travel_admin_spvsr.php">Close TA</a><?php if($recordData['TA_voucher_submitted_timestamp'][0] != ''){?> | <a href="travel_admin.php?action=apprv&travel_auth_ID=<?php echo $recordData['travel_auth_ID'][0];?>">Show voucher</a><?php }?></p></td></tr>

<?php }else{ ?>

			<tr><td colspan="2"><p class="alert_small"><b>Supervisor/Budget Authority (<?php echo $_SESSION['user_ID'];?>)</b>: To approve this travel authorization (TA), click the appropriate signature box below. | <a href="menu_travel_admin_spvsr.php">Close TA</a></p></td></tr>

<?php } ?>
			
			<tr><td colspan="2">
			<form name="travel_auth_approve">
			<input type="hidden" name="travel_auth_ID" value="<?php echo $recordData['travel_auth_ID'][0];?>">
			<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
			<input type="hidden" name="action" value="approve_ta_sign">




						<table cellpadding="10" cellspacing="0" border="0" bordercolor="#ebebeb" bgcolor="#ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong><?php echo $recordData['staff::name_timesheet'][0];?> (<?php echo $recordData['staff::primary_SEDL_workgroup'][0];?>)</strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">TA Status: <strong><?php if($recordData['approval_status'][0] == 'Pending'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['approval_status'][0]).'</span>';?></strong></td></tr>
						<tr><td class="body" nowrap><strong>TRAVEL AUTHORIZATION</strong></td><td align="right">ID: <?php echo $recordData['travel_auth_ID'][0];?> <?php if($recordData['approval_status'][0] == 'Approved'){ ?> | <img src="/staff/sims/images/padlock.jpg" border="0" title="This travel authorization is locked."> <?php }?></td></tr>
						
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
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Destination(s):</strong></td><td style="border:0px;padding:6px"><?php if($recordData['multi_dest'][0] == 'yes'){echo $recordData['c_multi_destinations_all_display_venues'][0];}else{echo $recordData['event_venue_city'][0];?>, <?php echo $recordData['event_venue_state'][0];}?></td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right" nowrap><strong>Period of Travel:</strong></td><td style="border:0px;padding:6px"><?php echo $recordData['leave_date_requested'][0];?> to <?php echo $recordData['return_date_requested'][0];?></td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right" nowrap><strong>Purpose of Travel:</strong></td><td style="border:0px;padding:6px"><?php echo stripslashes($recordData['c_purpose_of_travel_csv'][0]);?></td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Description:</strong></td><td style="border:0px;padding:6px"><?php echo stripslashes($recordData['purpose_of_travel_descr'][0]);?></td></tr>
												</table>														
												
												
										</td>
										
										</tr>
										</table>
										

								</td>
<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    APPROVAL LOG             ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<td style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>APPROVAL LOG</strong></div>



								
									<table cellspacing="2" style="margin-top:6px">

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf"><strong>DATE</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>USER</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>ACTION</strong></td></tr>

									<?php if($searchResult6['foundCount'] > 0){ // SHOW TA STATUS LOG ?>
									<?php foreach($searchResult6['data'] as $key => $searchData6) { if($searchData6['comment'][0] != ''){$rowcolor = '#ffd6d6';}elseif(($searchData6['action'][0] == 'APPROVE_TA')||($searchData6['action'][0] == 'APPROVE_TV')){$rowcolor = '#ace29f';}else{$rowcolor = '#ebebeb';} ?>

										<tr><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData6['creation_timestamp'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData6['user'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData6['action'][0];?></td></tr>
										<?php if($searchData6['comment'][0] != ''){?><tr><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" colspan="3"><?php echo $searchData6['comment'][0];?></td></tr><?php }?>

									<?php } ?>
									<?php } ?>
									</table>
								</td>
								</tr>


								<tr>
<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    ATTACHMENTS              ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>ATTACHMENTS (<?php echo $recordData['c_attachments_count'][0];?>)</strong></div><br>
								
								<?php if($searchResult5['foundCount'] > 0){ ?>
								
									<ol style="border:1px dotted #000000;background-color:#f8fafc;margin-top:6px;padding:5px;width:95%;list-style-position: inside;"><img src="http://www.sedl.org/common/images/icon_blank.png" style="float:right">
									
									<?php foreach($searchResult5['data'] as $key => $searchData5) { // LIST ATTACHMENTS ?>
										
										<li style="padding:5px">
										
										<strong><a href="http://198.214.141.190/sims/attachments/<?php echo $searchData5['attachment_filename'][0];?>" target="_blank" title="Click to download this attachment for review."><?php echo ucwords($searchData5['attachment_description'][0]);?></a></strong><br>
										<span class="tiny">Type: <?php echo $searchData5['attachment_type'][0];?> | Uploaded: <?php echo $searchData5['uploaded_timestamp'][0];?> by <?php echo $searchData5['uploaded_by'][0];?></span><br>

									
										</li>

										<hr style="border:1px dotted #000000">
									<?php  } ?></ol>
								
								<?php }else{ ?>
								
								N/A
								<?php } ?>
										
								</td>
<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    NOTES/COMMENTS           ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->
								
								<td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>NOTES/COMMENTS</strong></div>

								<div style="padding:8px"><?php echo $recordData['notes'][0];?></div>
								
								</td>
								
								
								</tr>

<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    TRANSPORTATION/LODGING   ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<tr><td colspan="2" class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>LODGING/MIE RATES</strong></div><br>

<?php if($recordData['multi_dest'][0] == 'yes'){ // ############################## THIS IS A MULTI-DESTINATION TA ############################## ?>

												<table width="100%">
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#A4D6A7" align="right" nowrap><strong>Destination 1:</strong></td><td colspan="2" valign="top" style="border:0px;padding:6px;background-color:#A4D6A7"><strong><?php echo $recordData['c_event_venue_city_state1'][0];?> | <?php echo $recordData['event_venue_city1_travel_start'][0].' - '.$recordData['event_venue_city1_travel_end'][0];?></strong></td></tr>

												<tr><td style="border:0px;padding:6px;background-color:#ffffff;vertical-align:text-top" align="right"><strong>Lodging:</strong></td>
												<td style="border:0px;padding:6px;vertical-align:text-top">

<?php if($recordData['lodging_not_required1'][0] == 'yes'){ ?>	
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													No lodging required.
													</td></tr>			
													</table>
												</div>	
												
												
<?php }else{ ?>

												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													<?php echo $recordData['preferred_hotel_name1'][0];?><br>
													<?php echo $recordData['preferred_hotel_addr1'][0];?><br>
													<?php echo $recordData['preferred_hotel_city1'][0];?>, <?php echo $recordData['preferred_hotel_state1'][0];?> <?php echo $recordData['preferred_hotel_zip1'][0];?> 
													</td></tr>

													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top">
													Rates:<br>
															<table cellpadding="6" cellspacing="2" style="background-color:#ffffff" width="100%">
				
																	<tr><td style="border:1px solid #999999;padding:6px;width:100%;font-weight:bold;background-color:#ebebeb" class="tiny">TYPE</td><td align="right" style="border:1px solid #999999;padding:6px;font-weight:bold;background-color:#ebebeb" class="tiny">AMOUNT</td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>Hotel Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px;font-weight:bold">$<?php echo number_format($recordData['hotel_rate1'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CONUS Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php echo number_format($recordData['allowance_lodging1'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CEO Rate (120% CONUS):</td><td align="right" style="border:1px dotted #000000;padding:6px<?php if($recordData['hotel_rate1'][0] > ($recordData['allowance_lodging1'][0]*1.2)){ ?>;background-color:#fcbebe<?php }else{?>;background-color:#a8dda2<?php }?>">$<?php echo number_format($recordData['allowance_lodging1'][0]*1.2, 2, '.', '');?></td></tr>
				
															</table>

													</td></tr>

													<?php if($recordData['hotel_rate1'][0] > ($recordData['allowance_lodging1'][0]*1.2)){ ?>
													
													<tr><td colspan="2" style="border:0px solid #ffd324;vertical-align:text-top" nowrap>


														<?php if($recordData['ceo_approval_rate_status'][0] == '0'){ // IF THE CEO HAS NOT SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #ffd324;padding:8px;background-color:#fff6bf;font-weight:bold;color:#477643">
															<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Approval Required | <span style="color:#ff0000"><strong>PENDING</strong></span></div>
															</div>
															</td></tr>

														<?php }else{ // IF THE CEO HAS SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #509049;padding:8px;background-color:#ffffff;font-weight:bold;color:#477643">
															<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Approved
															</div>
															</td></tr>

														<?php } ?>
														
													
													<?php }?>

													</table>
												</div>	



<?php } ?>
												</td>
												<td style="border:0px;padding:6px;vertical-align:text-top;width:100%">
												
												
												
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>TRAVEL PER DIEM</strong> (CONUS Ref: <?php echo $recordData['c_event_conus_city_state1'][0];?>)</span><br>
													<table cellspacing="2">
		

														
															<tr style="background-color:#ebebeb"><td style="padding:6px" nowrap>Travel Date</td><td style="padding:6px" align="right">Breakfast</td><td style="padding:6px" align="right">Lunch</td><td style="padding:6px" align="right">Dinner</td><td style="padding:6px" align="right">Incidentals</td><td style="padding:6px" align="right" nowrap>Total MIE</td><td style="padding:6px" align="right">Lodging</td></tr>
															
															<?php foreach($searchResult2['data'] as $key => $searchData2) { 
															if($searchData2['destination_city_original'][0] == $recordData['event_venue_city1'][0]){ ?>
															<tr bgcolor="#ffffff"><td style="padding:6px"><?php echo $searchData2['travel_date'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_breakfast'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_lunch'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_dinner'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_incidentals'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['lodging'][0];?></td></tr>
															<?php }} ?>
		
		
													</table>
												</div>


												
													
												
												</td></tr>



												<tr><td valign="top" style="border:0px;padding:6px;background-color:#A4D6A7" align="right" nowrap><strong>Destination 2:</strong></td><td colspan="2" valign="top" style="border:0px;padding:6px;background-color:#A4D6A7"><strong><?php echo $recordData['c_event_venue_city_state2'][0];?> | <?php echo $recordData['event_venue_city2_travel_start'][0].' - '.$recordData['event_venue_city2_travel_end'][0];?></strong></td></tr>

												<tr><td style="border:0px;padding:6px;background-color:#ffffff;vertical-align:text-top" align="right"><strong>Lodging:</strong></td>
												<td style="border:0px;padding:6px;vertical-align:text-top">

<?php if($recordData['lodging_not_required2'][0] == 'yes'){ ?>	
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													No lodging required.
													</td></tr>			
													</table>
												</div>	
												
												
<?php }else{ ?>

												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													<?php echo $recordData['preferred_hotel_name2'][0];?><br>
													<?php echo $recordData['preferred_hotel_addr2'][0];?><br>
													<?php echo $recordData['preferred_hotel_city2'][0];?>, <?php echo $recordData['preferred_hotel_state2'][0];?> <?php echo $recordData['preferred_hotel_zip2'][0];?> 
													</td></tr>

													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top">
													Rates:<br>
															<table cellpadding="6" cellspacing="2" style="background-color:#ffffff" width="100%">
				
																	<tr><td style="border:1px solid #999999;padding:6px;width:100%;font-weight:bold;background-color:#ebebeb" class="tiny">TYPE</td><td align="right" style="border:1px solid #999999;padding:6px;font-weight:bold;background-color:#ebebeb" class="tiny">AMOUNT</td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>Hotel Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px;font-weight:bold">$<?php echo number_format($recordData['hotel_rate2'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CONUS Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php echo number_format($recordData['allowance_lodging2'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CEO Rate (120% CONUS):</td><td align="right" style="border:1px dotted #000000;padding:6px<?php if($recordData['hotel_rate2'][0] > ($recordData['allowance_lodging2'][0]*1.2)){ ?>;background-color:#fcbebe<?php }else{?>;background-color:#a8dda2<?php }?>">$<?php echo number_format($recordData['allowance_lodging2'][0]*1.2, 2, '.', '');?></td></tr>
				
															</table>

													</td></tr>

													<?php if($recordData['hotel_rate2'][0] > ($recordData['allowance_lodging2'][0]*1.2)){ ?>
													
													<tr><td colspan="2" style="border:0px solid #ffd324;vertical-align:text-top" nowrap>


														<?php if($recordData['ceo_approval_rate_status'][0] == '0'){ // IF THE CEO HAS NOT SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #ffd324;padding:8px;background-color:#fff6bf;font-weight:bold;color:#477643">
															<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Approval Required | <span style="color:#ff0000"><strong>PENDING</strong></span></div>
															</div>
															</td></tr>

														<?php }else{ // IF THE CEO HAS SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #509049;padding:8px;background-color:#ffffff;font-weight:bold;color:#477643">
															<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Approved
															</div>
															</td></tr>

														<?php } ?>
														
													
													<?php }?>

													</table>
												</div>	



<?php } ?>
												</td>
												<td style="border:0px;padding:6px;vertical-align:text-top;width:100%">
												
												
												
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>TRAVEL PER DIEM</strong> (CONUS Ref: <?php echo $recordData['c_event_conus_city_state2'][0];?>)</span><br>
													<table cellspacing="2">
		

														
															<tr style="background-color:#ebebeb"><td style="padding:6px" nowrap>Travel Date</td><td style="padding:6px" align="right">Breakfast</td><td style="padding:6px" align="right">Lunch</td><td style="padding:6px" align="right">Dinner</td><td style="padding:6px" align="right">Incidentals</td><td style="padding:6px" align="right" nowrap>Total MIE</td><td style="padding:6px" align="right">Lodging</td></tr>
															
															<?php foreach($searchResult2['data'] as $key => $searchData2) { 
															if($searchData2['destination_city_original'][0] == $recordData['event_venue_city2'][0]){ ?>
															<tr bgcolor="#ffffff"><td style="padding:6px"><?php echo $searchData2['travel_date'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_breakfast'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_lunch'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_dinner'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_incidentals'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['lodging'][0];?></td></tr>
															<?php }} ?>
		
		
													</table>
												</div>


												
													
												
												</td></tr>


<?php if($recordData['num_dest'][0] > 2){ ?>

												<tr><td valign="top" style="border:0px;padding:6px;background-color:#A4D6A7" align="right" nowrap><strong>Destination 3:</strong></td><td colspan="2" valign="top" style="border:0px;padding:6px;background-color:#A4D6A7"><strong><?php echo $recordData['c_event_venue_city_state3'][0];?> | <?php echo $recordData['event_venue_city3_travel_start'][0].' - '.$recordData['event_venue_city3_travel_end'][0];?></strong></td></tr>

												<tr><td style="border:0px;padding:6px;background-color:#ffffff;vertical-align:text-top" align="right"><strong>Lodging:</strong></td>
												<td style="border:0px;padding:6px;vertical-align:text-top">

<?php if($recordData['lodging_not_required3'][0] == 'yes'){ ?>	
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													No lodging required.
													</td></tr>			
													</table>
												</div>	
												
												
<?php }else{ ?>

												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													<?php echo $recordData['preferred_hotel_name3'][0];?><br>
													<?php echo $recordData['preferred_hotel_addr3'][0];?><br>
													<?php echo $recordData['preferred_hotel_city3'][0];?>, <?php echo $recordData['preferred_hotel_state3'][0];?> <?php echo $recordData['preferred_hotel_zip3'][0];?> 
													</td></tr>

													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top">
													Rates:<br>
															<table cellpadding="6" cellspacing="2" style="background-color:#ffffff" width="100%">
				
																	<tr><td style="border:1px solid #999999;padding:6px;width:100%;font-weight:bold;background-color:#ebebeb" class="tiny">TYPE</td><td align="right" style="border:1px solid #999999;padding:6px;font-weight:bold;background-color:#ebebeb" class="tiny">AMOUNT</td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>Hotel Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px;font-weight:bold">$<?php echo number_format($recordData['hotel_rate3'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CONUS Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php echo number_format($recordData['allowance_lodging3'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CEO Rate (120% CONUS):</td><td align="right" style="border:1px dotted #000000;padding:6px<?php if($recordData['hotel_rate3'][0] > ($recordData['allowance_lodging3'][0]*1.2)){ ?>;background-color:#fcbebe<?php }else{?>;background-color:#a8dda2<?php }?>">$<?php echo number_format($recordData['allowance_lodging3'][0]*1.2, 2, '.', '');?></td></tr>
				
															</table>

													</td></tr>

													<?php if($recordData['hotel_rate3'][0] > ($recordData['allowance_lodging3'][0]*1.2)){ ?>
													
													<tr><td colspan="2" style="border:0px solid #ffd324;vertical-align:text-top" nowrap>


														<?php if($recordData['ceo_approval_rate_status'][0] == '0'){ // IF THE CEO HAS NOT SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #ffd324;padding:8px;background-color:#fff6bf;font-weight:bold;color:#477643">
															<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Approval Required | <span style="color:#ff0000"><strong>PENDING</strong></span></div>
															</div>
															</td></tr>

														<?php }else{ // IF THE CEO HAS SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #509049;padding:8px;background-color:#ffffff;font-weight:bold;color:#477643">
															<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Approved
															</div>
															</td></tr>

														<?php } ?>
														
													
													<?php }?>

													</table>
												</div>	



<?php } ?>
												</td>
												<td style="border:0px;padding:6px;vertical-align:text-top;width:100%">
												
												
												
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>TRAVEL PER DIEM</strong> (CONUS Ref: <?php echo $recordData['c_event_conus_city_state3'][0];?>)</span><br>
													<table cellspacing="2">
		

														
															<tr style="background-color:#ebebeb"><td style="padding:6px" nowrap>Travel Date</td><td style="padding:6px" align="right">Breakfast</td><td style="padding:6px" align="right">Lunch</td><td style="padding:6px" align="right">Dinner</td><td style="padding:6px" align="right">Incidentals</td><td style="padding:6px" align="right" nowrap>Total MIE</td><td style="padding:6px" align="right">Lodging</td></tr>
															
															<?php foreach($searchResult2['data'] as $key => $searchData2) { 
															if($searchData2['destination_city_original'][0] == $recordData['event_venue_city3'][0]){ ?>
															<tr bgcolor="#ffffff"><td style="padding:6px"><?php echo $searchData2['travel_date'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_breakfast'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_lunch'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_dinner'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_incidentals'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['lodging'][0];?></td></tr>
															<?php }} ?>
		
		
													</table>
												</div>


												
													
												
												</td></tr>


<?php }?>


<?php if($recordData['num_dest'][0] > 3){ ?>

												<tr><td valign="top" style="border:0px;padding:6px;background-color:#A4D6A7" align="right" nowrap><strong>Destination 4:</strong></td><td colspan="2" valign="top" style="border:0px;padding:6px;background-color:#A4D6A7"><strong><?php echo $recordData['c_event_venue_city_state4'][0];?> | <?php echo $recordData['event_venue_city4_travel_start'][0].' - '.$recordData['event_venue_city4_travel_end'][0];?></strong></td></tr>

												<tr><td style="border:0px;padding:6px;background-color:#ffffff;vertical-align:text-top" align="right"><strong>Lodging:</strong></td>
												<td style="border:0px;padding:6px;vertical-align:text-top">

<?php if($recordData['lodging_not_required4'][0] == 'yes'){ ?>	
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													No lodging required.
													</td></tr>			
													</table>
												</div>	
												
												
<?php }else{ ?>

												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													<?php echo $recordData['preferred_hotel_name4'][0];?><br>
													<?php echo $recordData['preferred_hotel_addr4'][0];?><br>
													<?php echo $recordData['preferred_hotel_city4'][0];?>, <?php echo $recordData['preferred_hotel_state4'][0];?> <?php echo $recordData['preferred_hotel_zip4'][0];?> 
													</td></tr>

													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top">
													Rates:<br>
															<table cellpadding="6" cellspacing="2" style="background-color:#ffffff" width="100%">
				
																	<tr><td style="border:1px solid #999999;padding:6px;width:100%;font-weight:bold;background-color:#ebebeb" class="tiny">TYPE</td><td align="right" style="border:1px solid #999999;padding:6px;font-weight:bold;background-color:#ebebeb" class="tiny">AMOUNT</td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>Hotel Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px;font-weight:bold">$<?php echo number_format($recordData['hotel_rate4'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CONUS Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php echo number_format($recordData['allowance_lodging4'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CEO Rate (120% CONUS):</td><td align="right" style="border:1px dotted #000000;padding:6px<?php if($recordData['hotel_rate4'][0] > ($recordData['allowance_lodging4'][0]*1.2)){ ?>;background-color:#fcbebe<?php }else{?>;background-color:#a8dda2<?php }?>">$<?php echo number_format($recordData['allowance_lodging4'][0]*1.2, 2, '.', '');?></td></tr>
				
															</table>

													</td></tr>

													<?php if($recordData['hotel_rate4'][0] > ($recordData['allowance_lodging4'][0]*1.2)){ ?>
													
													<tr><td colspan="2" style="border:0px solid #ffd324;vertical-align:text-top" nowrap>


														<?php if($recordData['ceo_approval_rate_status'][0] == '0'){ // IF THE CEO HAS NOT SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #ffd324;padding:8px;background-color:#fff6bf;font-weight:bold;color:#477643">
															<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Approval Required | <span style="color:#ff0000"><strong>PENDING</strong></span></div>
															</div>
															</td></tr>

														<?php }else{ // IF THE CEO HAS SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #509049;padding:8px;background-color:#ffffff;font-weight:bold;color:#477643">
															<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Approved
															</div>
															</td></tr>

														<?php } ?>
														
													
													<?php }?>

													</table>
												</div>	



<?php } ?>



												</td>
												<td style="border:0px;padding:6px;vertical-align:text-top;width:100%">
												
												
												
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>TRAVEL PER DIEM</strong> (CONUS Ref: <?php echo $recordData['c_event_conus_city_state4'][0];?>)</span><br>
													<table cellspacing="2">
		

														
															<tr style="background-color:#ebebeb"><td style="padding:6px" nowrap>Travel Date</td><td style="padding:6px" align="right">Breakfast</td><td style="padding:6px" align="right">Lunch</td><td style="padding:6px" align="right">Dinner</td><td style="padding:6px" align="right">Incidentals</td><td style="padding:6px" align="right" nowrap>Total MIE</td><td style="padding:6px" align="right">Lodging</td></tr>
															
															<?php foreach($searchResult2['data'] as $key => $searchData2) { 
															if($searchData2['destination_city_original'][0] == $recordData['event_venue_city4'][0]){ ?>
															<tr bgcolor="#ffffff"><td style="padding:6px"><?php echo $searchData2['travel_date'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_breakfast'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_lunch'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_dinner'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_incidentals'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['lodging'][0];?></td></tr>
															<?php }} ?>
		
		
													</table>
												</div>


												
													
												
												</td></tr>


<?php }?>


<?php if($recordData['num_dest'][0] > 4){ ?>

												<tr><td valign="top" style="border:0px;padding:6px;background-color:#A4D6A7" align="right" nowrap><strong>Destination 5:</strong></td><td colspan="2" valign="top" style="border:0px;padding:6px;background-color:#A4D6A7"><strong><?php echo $recordData['c_event_venue_city_state5'][0];?> | <?php echo $recordData['event_venue_city5_travel_start'][0].' - '.$recordData['event_venue_city5_travel_end'][0];?></strong></td></tr>

												<tr><td style="border:0px;padding:6px;background-color:#ffffff;vertical-align:text-top" align="right"><strong>Lodging:</strong></td>
												<td style="border:0px;padding:6px;vertical-align:text-top">

<?php if($recordData['lodging_not_required5'][0] == 'yes'){ ?>	
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													No lodging required.
													</td></tr>			
													</table>
												</div>	
												
												
<?php }else{ ?>

												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													<?php echo $recordData['preferred_hotel_name5'][0];?><br>
													<?php echo $recordData['preferred_hotel_addr5'][0];?><br>
													<?php echo $recordData['preferred_hotel_city5'][0];?>, <?php echo $recordData['preferred_hotel_state5'][0];?> <?php echo $recordData['preferred_hotel_zip5'][0];?> 
													</td></tr>

													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top">
													Rates:<br>
															<table cellpadding="6" cellspacing="2" style="background-color:#ffffff" width="100%">
				
																	<tr><td style="border:1px solid #999999;padding:6px;width:100%;font-weight:bold;background-color:#ebebeb" class="tiny">TYPE</td><td align="right" style="border:1px solid #999999;padding:6px;font-weight:bold;background-color:#ebebeb" class="tiny">AMOUNT</td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>Hotel Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px;font-weight:bold">$<?php echo number_format($recordData['hotel_rate5'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CONUS Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php echo number_format($recordData['allowance_lodging5'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CEO Rate (120% CONUS):</td><td align="right" style="border:1px dotted #000000;padding:6px<?php if($recordData['hotel_rate5'][0] > ($recordData['allowance_lodging5'][0]*1.2)){ ?>;background-color:#fcbebe<?php }else{?>;background-color:#a8dda2<?php }?>">$<?php echo number_format($recordData['allowance_lodging5'][0]*1.2, 2, '.', '');?></td></tr>
				
															</table>

													</td></tr>

													<?php if($recordData['hotel_rate5'][0] > ($recordData['allowance_lodging5'][0]*1.2)){ ?>
													
													<tr><td colspan="2" style="border:0px solid #ffd324;vertical-align:text-top" nowrap>


														<?php if($recordData['ceo_approval_rate_status'][0] == '0'){ // IF THE CEO HAS NOT SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #ffd324;padding:8px;background-color:#fff6bf;font-weight:bold;color:#477643">
															<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Approval Required | <span style="color:#ff0000"><strong>PENDING</strong></span></div>
															</div>
															</td></tr>

														<?php }else{ // IF THE CEO HAS SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #509049;padding:8px;background-color:#ffffff;font-weight:bold;color:#477643">
															<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Approved
															</div>
															</td></tr>

														<?php } ?>
														
													
													<?php }?>

													</table>
												</div>	



<?php } ?>



												</td>
												<td style="border:0px;padding:6px;vertical-align:text-top;width:100%">
												
												
												
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>TRAVEL PER DIEM</strong> (CONUS Ref: <?php echo $recordData['c_event_conus_city_state5'][0];?>)</span><br>
													<table cellspacing="2">
		

														
															<tr style="background-color:#ebebeb"><td style="padding:6px" nowrap>Travel Date</td><td style="padding:6px" align="right">Breakfast</td><td style="padding:6px" align="right">Lunch</td><td style="padding:6px" align="right">Dinner</td><td style="padding:6px" align="right">Incidentals</td><td style="padding:6px" align="right" nowrap>Total MIE</td><td style="padding:6px" align="right">Lodging</td></tr>
															
															<?php foreach($searchResult2['data'] as $key => $searchData2) { 
															if($searchData2['destination_city_original'][0] == $recordData['event_venue_city5'][0]){ ?>
															<tr bgcolor="#ffffff"><td style="padding:6px"><?php echo $searchData2['travel_date'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_breakfast'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_lunch'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_dinner'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_incidentals'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['lodging'][0];?></td></tr>
															<?php }} ?>
		
		
													</table>
												</div>


												
													
												
												</td></tr>


<?php }?>


<?php if($recordData['num_dest'][0] > 5){ ?>

												<tr><td valign="top" style="border:0px;padding:6px;background-color:#A4D6A7" align="right" nowrap><strong>Destination 6:</strong></td><td colspan="2" valign="top" style="border:0px;padding:6px;background-color:#A4D6A7"><strong><?php echo $recordData['c_event_venue_city_state6'][0];?> | <?php echo $recordData['event_venue_city6_travel_start'][0].' - '.$recordData['event_venue_city6_travel_end'][0];?></strong></td></tr>

												<tr><td style="border:0px;padding:6px;background-color:#ffffff;vertical-align:text-top" align="right"><strong>Lodging:</strong></td>
												<td style="border:0px;padding:6px;vertical-align:text-top">

<?php if($recordData['lodging_not_required6'][0] == 'yes'){ ?>	
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													No lodging required.
													</td></tr>			
													</table>
												</div>	
												
												
<?php }else{ ?>

												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													<?php echo $recordData['preferred_hotel_name6'][0];?><br>
													<?php echo $recordData['preferred_hotel_addr6'][0];?><br>
													<?php echo $recordData['preferred_hotel_city6'][0];?>, <?php echo $recordData['preferred_hotel_state6'][0];?> <?php echo $recordData['preferred_hotel_zip6'][0];?> 
													</td></tr>

													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top">
													Rates:<br>
															<table cellpadding="6" cellspacing="2" style="background-color:#ffffff" width="100%">
				
																	<tr><td style="border:1px solid #999999;padding:6px;width:100%;font-weight:bold;background-color:#ebebeb" class="tiny">TYPE</td><td align="right" style="border:1px solid #999999;padding:6px;font-weight:bold;background-color:#ebebeb" class="tiny">AMOUNT</td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>Hotel Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px;font-weight:bold">$<?php echo number_format($recordData['hotel_rate6'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CONUS Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php echo number_format($recordData['allowance_lodging6'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CEO Rate (120% CONUS):</td><td align="right" style="border:1px dotted #000000;padding:6px<?php if($recordData['hotel_rate6'][0] > ($recordData['allowance_lodging6'][0]*1.2)){ ?>;background-color:#fcbebe<?php }else{?>;background-color:#a8dda2<?php }?>">$<?php echo number_format($recordData['allowance_lodging6'][0]*1.2, 2, '.', '');?></td></tr>
				
															</table>

													</td></tr>

													<?php if($recordData['hotel_rate6'][0] > ($recordData['allowance_lodging6'][0]*1.2)){ ?>
													
													<tr><td colspan="2" style="border:0px solid #ffd324;vertical-align:text-top" nowrap>


														<?php if($recordData['ceo_approval_rate_status'][0] == '0'){ // IF THE CEO HAS NOT SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #ffd324;padding:8px;background-color:#fff6bf;font-weight:bold;color:#477643">
															<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Approval Required | <span style="color:#ff0000"><strong>PENDING</strong></span></div>
															</div>
															</td></tr>

														<?php }else{ // IF THE CEO HAS SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #509049;padding:8px;background-color:#ffffff;font-weight:bold;color:#477643">
															<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Approved
															</div>
															</td></tr>

														<?php } ?>
														
													
													<?php }?>

													</table>
												</div>	



<?php } ?>



												</td>
												<td style="border:0px;padding:6px;vertical-align:text-top;width:100%">
												
												
												
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block;vertical-align:text-top"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>TRAVEL PER DIEM</strong> (CONUS Ref: <?php echo $recordData['c_event_conus_city_state6'][0];?>)</span><br>
													<table cellspacing="2">
		

														
															<tr style="background-color:#ebebeb"><td style="padding:6px" nowrap>Travel Date</td><td style="padding:6px" align="right">Breakfast</td><td style="padding:6px" align="right">Lunch</td><td style="padding:6px" align="right">Dinner</td><td style="padding:6px" align="right">Incidentals</td><td style="padding:6px" align="right" nowrap>Total MIE</td><td style="padding:6px" align="right">Lodging</td></tr>
															
															<?php foreach($searchResult2['data'] as $key => $searchData2) { 
															if($searchData2['destination_city_original'][0] == $recordData['event_venue_city6'][0]){ ?>
															<tr bgcolor="#ffffff"><td style="padding:6px"><?php echo $searchData2['travel_date'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_breakfast'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_lunch'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_dinner'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie_incidentals'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['mie'][0];?></td><td style="padding:6px" align="right"><?php echo $searchData2['lodging'][0];?></td></tr>
															<?php }} ?>
		
		
													</table>
												</div>


												
													
												
												</td></tr>


<?php }?>


												</table>														

<?php }else{ // ############################ THIS IS A SINGLE-DESTINATION TA ############################## ?>

												<table width="100%">
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Lodging:</strong></td>
												<td style="border:0px;padding:6px;vertical-align:text-top">

<?php if($recordData['lodging_not_required'][0] == 'Yes'){ ?>	
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													No lodging required.
													</td></tr>			
													</table>
												</div>	
												
												
<?php }else{ ?>

												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>HOTEL</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													<?php echo $recordData['preferred_hotel_name'][0];?><br>
													<?php echo $recordData['preferred_hotel_addr'][0];?><br>
													<?php echo $recordData['preferred_hotel_city'][0];?>, <?php echo $recordData['preferred_hotel_state'][0];?> <?php echo $recordData['preferred_hotel_zip'][0];?> 
													</td></tr>

													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top">
													Rates:<br>
															<table cellpadding="6" cellspacing="2" style="background-color:#ffffff" width="100%">
				
																	<tr><td style="border:1px solid #999999;padding:6px;width:100%;font-weight:bold;background-color:#ebebeb" class="tiny">TYPE</td><td align="right" style="border:1px solid #999999;padding:6px;font-weight:bold;background-color:#ebebeb" class="tiny">AMOUNT</td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>Hotel Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px;font-weight:bold">$<?php echo number_format($recordData['hotel_rate'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CONUS Rate:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php echo number_format($recordData2['lodging'][0], 2, '.', '');?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px" nowrap>CEO Rate (120% CONUS):</td><td align="right" style="border:1px dotted #000000;padding:6px<?php if($recordData['hotel_rate'][0] > ($recordData2['lodging'][0]*1.2)){ ?>;background-color:#fcbebe<?php }else{?>;background-color:#a8dda2<?php }?>">$<?php echo number_format($recordData2['lodging'][0]*1.2, 2, '.', '');?></td></tr>
				
															</table>

													</td></tr>

													<?php if($recordData['hotel_rate'][0] > ($recordData2['lodging'][0]*1.2)){ ?>
													
													<tr><td colspan="2" style="border:0px solid #ffd324;vertical-align:text-top" nowrap>


														<?php if($recordData['ceo_approval_rate_status'][0] == '0'){ // IF THE CEO HAS NOT SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #ffd324;padding:8px;background-color:#fff6bf;font-weight:bold;color:#477643">
															<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Approval Required | <span style="color:#ff0000"><strong>PENDING</strong></span></div>
															</div>
															</td></tr>

														<?php }else{ // IF THE CEO HAS SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #509049;padding:8px;background-color:#ffffff;font-weight:bold;color:#477643">
															<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Approved
															</div>
															</td></tr>

														<?php } ?>
														
													
													<?php }?>

													</table>
												</div>	



<?php } ?>
												</td>
												<td style="border:0px;padding:6px;vertical-align:text-top;width:100%">
												
												
												
												
												<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>TRAVEL PER DIEM</strong></span><br>
													<table cellspacing="2">
		

														<?php if($recordData['c_travel_rate_variance'][0] == '0'){ // IF TRAVEL RATES DON'T CHANGE DURING THIS TRAVEL PERIOD ?>
															
															<tr><td colspan="2" style="border:1px dotted #000000;padding:6px;width:100%;background-color:#ffffff"><span class="tiny"><strong>REF:</strong> <?php echo $recordData['c_event_conus_city_state'][0];?></span><br>
															
																<table cellpadding="6" cellspacing="2" style="background-color:#ffffff" width="100%">
																<tr><td style="border:1px solid #999999;padding:6px;width:100%;font-weight:bold;background-color:#ebebeb" class="tiny">TYPE</td><td align="right" style="border:1px solid #999999;padding:6px;font-weight:bold;background-color:#ebebeb" class="tiny">AMOUNT</td></tr>
																<tr><td style="border:1px dotted #806d50;padding:6px">Breakfast:</td><td align="right" style="border:1px dotted #806d50;padding:6px">&nbsp;$<?php echo $recordData2['mie_breakfast'][0];?></td></tr>
																<tr><td style="border:1px dotted #806d50;padding:6px">Lunch:</td><td align="right" style="border:1px dotted #806d50;padding:6px">$<?php echo $recordData2['mie_lunch'][0];?></td></tr>
																<tr><td style="border:1px dotted #806d50;padding:6px">Dinner:</td><td align="right" style="border:1px dotted #806d50;padding:6px">$<?php echo $recordData2['mie_dinner'][0];?></td></tr>
																<tr><td style="border:1px dotted #806d50;padding:6px">Incidentals:</td><td align="right" style="border:1px dotted #806d50;padding:6px">$<?php echo $recordData2['mie_incidentals'][0];?></td></tr>
																<tr><td style="border:1px dotted #806d50;padding:6px;font-weight:bold">Total:</td><td align="right" style="border:1px dotted #806d50;padding:6px">$<?php echo $recordData2['mie'][0];?></td></tr>
																</table>															
														
															</td></tr>
														
														<?php }else{ // IF TRAVEL RATES CHANGE DURING THIS TRAVEL PERIOD ?><p class="alert_small">NOTE: A per diem rate change takes place during this travel period. See rates per travel day below.</p>
														
															<tr style="background-color:#ebebeb"><td>Date of Travel</td><td align="right">Breakfast</td><td align="right">Lunch</td><td align="right">Dinner</td><td align="right">Incidentals</td><td align="right">Total MIE</td><td align="right">Lodging</td></tr>
															
															<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
															<tr bgcolor="#ffffff"><td><?php echo $searchData2['travel_date'][0];?></td><td align="right"><?php echo $searchData2['mie_breakfast'][0];?></td><td align="right"><?php echo $searchData2['mie_lunch'][0];?></td><td align="right"><?php echo $searchData2['mie_dinner'][0];?></td><td align="right"><?php echo $searchData2['mie_incidentals'][0];?></td><td align="right"><?php echo $searchData2['mie'][0];?></td><td align="right"><?php echo $searchData2['lodging'][0];?></td></tr>
															<?php } ?>
		
														<?php } ?>
		
													</table>
												</div>


												
													
												
												</td></tr>
												</table>														
<?php } ?>												
								</td></tr>
<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    BUDGET/FISCAL            ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<tr><td colspan="2" class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>BUDGET/FISCAL</strong></div><br>

												<table width="100%">
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right" nowrap><strong>Amount Authorized:</strong></td><td colspan="2" style="border:0px;padding:6px" width="100%"><span style="color:#0000ff"><strong>$<?php echo number_format($recordData['amount_authorized'][0], 2, '.', '');?></span></td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right" nowrap><strong>Budget Code(s):</strong></td><td colspan="2" style="border:0px;padding:6px" width="100%"><?php echo $recordData['c_budget_codes_csv_w_initials'][0];?></td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right" nowrap><strong>Instructions:</strong></td><td colspan="2" style="border:0px;padding:6px" width="100%"><?php echo $recordData['budget_code_instructions'][0];?> <?php if($recordData['trans_airline_bta_prepaid'][0] == 'Yes'){?>| charge airfare to SEDL's BTA account<?php }?></td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Advance/Fees:</strong></td>
												<td style="border:0px;padding:6px;vertical-align:text-top" nowrap>

<?php if(($recordData['travel_advance_requested'][0] == 'yes')||($recordData['travel_advance_requested'][0] == 'Yes')){ ?>	
													<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>TRAVEL ADVANCE</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:2px solid #448ce6;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													Amount Requested: <strong>$<?php echo number_format($recordData['c_travel_advance_itemized_total_amt'][0], 2, '.', '');?></strong>
													</td></tr>

													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top">
													Itemized Expenses:<br>
															<table cellpadding="6" cellspacing="2" style="background-color:#ffffff" width="100%">
				
																	<tr><td style="border:1px solid #999999;padding:6px;width:100%;font-weight:bold;background-color:#ebebeb" class="tiny">CATEGORY</td><td align="right" style="border:1px solid #999999;padding:6px;font-weight:bold;background-color:#ebebeb" class="tiny">AMOUNT</td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px"><?php echo $recordData['travel_advance_itemized_1'][0];?>:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php if($recordData['travel_advance_itemized_1_amt'][0] !== ''){echo number_format($recordData['travel_advance_itemized_1_amt'][0], 2, '.', '');}?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px"><?php echo $recordData['travel_advance_itemized_2'][0];?>:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php if($recordData['travel_advance_itemized_2_amt'][0] !== ''){echo number_format($recordData['travel_advance_itemized_2_amt'][0], 2, '.', '');}?></td></tr>
																	<tr><td style="border:1px dotted #000000;padding:6px"><?php echo $recordData['travel_advance_itemized_3'][0];?>:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php if($recordData['travel_advance_itemized_3_amt'][0] !== ''){echo number_format($recordData['travel_advance_itemized_3_amt'][0], 2, '.', '');}?></td></tr>
																	<?php if($recordData['travel_advance_itemized_4_amt'][0] !== ''){?><tr><td style="border:1px dotted #000000;padding:6px"><?php echo $recordData['travel_advance_itemized_4'][0];?>:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php if($recordData['travel_advance_itemized_4_amt'][0] !== ''){echo number_format($recordData['travel_advance_itemized_4_amt'][0], 2, '.', '');}?></td></tr><?php }?>
																	<?php if($recordData['travel_advance_itemized_5_amt'][0] !== ''){?><tr><td style="border:1px dotted #000000;padding:6px"><?php echo $recordData['travel_advance_itemized_5'][0];?>:</td><td align="right" style="border:1px dotted #000000;padding:6px">$<?php if($recordData['travel_advance_itemized_5_amt'][0] !== ''){echo number_format($recordData['travel_advance_itemized_5_amt'][0], 2, '.', '');}?></td></tr><?php }?>
				
															</table>

													</td></tr>

													
													<tr><td style="border:0px solid #ffd324;background-color:#fff6bf;vertical-align:text-top" nowrap>


														<?php if($recordData['ceo_approval_advance_status'][0] == '0'){ // IF THE CEO HAS NOT APPROVED THE TRAVEL ADVANCE ?>
															<div class="tiny" style="border:2px solid #ffd324;padding:8px;background-color:#fff6bf;font-weight:bold;color:#477643">
															<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Approval Required | <span style="color:#ff0000"><strong>PENDING</strong></span></div>
															</div>			
															</td></tr>

														<?php }else{ // IF THE CEO HAS SIGNED THE TRAVEL AUTHORIZATION ?>
															<div class="tiny" style="border:2px solid #509049;padding:8px;background-color:#ffffff;font-weight:bold;color:#477643">
															<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Approved
															</div>	
															</td></tr>

														<?php } ?>
														
													

													</table>
													</div>
<?php }else{?>
													<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>TRAVEL ADVANCE</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													No travel advance requested.
													</td></tr>
													</table>	
													</div>
<?php } ?>
												</td>
												<td style="border:0px;padding:6px;vertical-align:text-top;width:100%" nowrap>
												
<?php if($recordData['req_registration_fee'][0] > 0){ ?>												
													<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>EVENT REGISTRATION FEE</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:2px solid #448ce6;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													Registration Fee: <strong>$<?php echo number_format($recordData['req_registration_fee'][0], 2, '.', '');?></strong>
													</td></tr>

													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													Fee Paid by: <strong><?php echo $recordData['req_registration_fee_paid_by'][0];?></strong>
													</td></tr>

													<tr><td style="border:0px solid #ffd324;background-color:#fff6bf;vertical-align:text-top" nowrap>


														<?php if($recordData['ceo_approval_reg_fee_status'][0] == '0'){ // IF THE CEO HAS NOT APPROVED THE REGISTRATION FEE ?>
															<div class="tiny" style="border:2px solid #ffd324;padding:8px;background-color:#fff6bf;font-weight:bold;color:#477643">
															<img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Approval Required | <span style="color:#ff0000"><strong>PENDING</strong></span></div>
															</div>
															</td></tr>

														<?php }else{ // IF THE CEO HAS APPROVED THE REGISTRATION FEE ?>
															<div class="tiny" style="border:2px solid #509049;padding:8px;background-color:#ffffff;font-weight:bold;color:#477643">
															<img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Approved
															</div>
															</td></tr>

														<?php } ?>
														
													

													</table>
													</div>
													
<?php }else{?>
													<div style="border:1px solid #448ce6;background-color:#c0daf9;padding:15px;display:inline-block"><span class="tiny" style="border:0px;padding:3px;color:#448ce6"><strong>EVENT REGISTRATION FEE</strong></span><br>
													<table cellspacing="2">
													<tr><td style="border:1px dotted #000000;padding:8px;background-color:#ffffff;vertical-align:text-top" nowrap>
													No registration fee required.
													</td></tr>
													</table>	
													</div>		
<?php } ?>

												
												
												</td></tr>
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

								<tr class="body"><td colspan="2" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>SIGNATURES</strong></div><br>
													
<?php if(($recordData['TA_submitted_timestamp'][0] != '')&&($recordData['approval_status_tr'][0] == 'Approved')){ // THE TA HAS BEEN SUBMITTED BY THE TRAVEL ADMIN ?>

													
														<table class="sims" cellspacing="1" cellpadding="10" border="1" width="100%">
		
<!--		
		<tr><td>
		<?php //echo '$updateData[travel_auth_approvals::c_all_signers_status_tier_1][0]: '.$updateData['travel_auth_approvals::c_all_signers_status_tier_1'][0];?><br>
		<?php //echo '$updateData[travel_auth_approvals::c_all_signers_status_tier_2][0]: '.$updateData['travel_auth_approvals::c_all_signers_status_tier_2'][0];?><br>
		<?php //echo '$updateData[travel_auth_approvals::c_all_signers_count_tier_2][0]: '.$updateData['travel_auth_approvals::c_all_signers_count_tier_2'][0];?><br>
		<?php //echo '$updateData[ceo_approval_rate_required][0]: '.$updateData['ceo_approval_rate_required'][0];?><br>
		<?php //echo '$updateData[ceo_approval_rate_status][0]: '.$updateData['ceo_approval_rate_status'][0];?><br>
		<?php //echo '$updateData[travel_advance_requested][0]: '.$updateData['travel_advance_requested'][0];?><br>
		<?php //echo '$updateData[ceo_approval_advance_status][0]: '.$updateData['ceo_approval_advance_status'][0];?><br>
		<?php //echo '$updateData[req_registration_fee][0]: '.$updateData['req_registration_fee'][0];?><br>
		<?php //echo '$updateData[ceo_approval_reg_fee_status][0]: '.$updateData['ceo_approval_reg_fee_status'][0];?><br>
		</td></tr>
-->
														<tr class="body" valign="top"><td><div style="border:1px dotted #000000;background-color:#bddbee;padding:5px" class="tiny">SUPERVISOR/BA APPROVAL</div><br>
														
															<table>
															<tr><td style="border-width:0px;padding:0px;margin:0px">
															<?php foreach($searchResult3['data'] as $key => $searchData3){ ?>
																<?php if($searchData3['signer_tier'][0] == '1'){ ?>
																	<?php if(($searchData3['signer_ID'][0] !== $searchData3['pba_signer_ID'][0])||($searchData3['signer_role'][0] !== 'Budget Authority')){ // DON'T DISPLAY DUPLICATE SIGNERS IN SUPERVISOR/BA APPROVAL SECTION ?>
																
																		<td align="center" valign="bottom" style="padding:5px">
																		<?php if($searchData3['signer_status'][0] == '1'){ // BA APPROVED ?><img src="/staff/sims/signatures/<?php echo $searchData3['signer_ID'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="travel_admin.php?travel_auth_ID=<?php echo $travel_auth_ID;?>&travel_auth_row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&sign=1&signer=<?php echo $searchData3['signer_ID'][0];?>" <?php if(($_SESSION['user_ID'] != $searchData3['signer_ID'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $searchData3['signer_ID'][0];?></a><?php }?><p>
																		<span class="tiny"><em><?php echo $searchData3['signer_role'][0];?></em><br><?php if($searchData3['signer_timestamp'][0] != ''){ ?><font color="999999">[<?php echo $searchData3['signer_timestamp'][0];?>]</font><?php } ?></span>
																		</td>
																
																	<?php } ?>
																<?php } ?>
															
															<?php } ?>
			
															</tr></table>
		
														</td></tr>										
		
														<tr class="body" valign="top"><td style="width:100%"><div style="border:1px dotted #000000;background-color:#bddbee;padding:5px" class="tiny">EO/FISCAL APPROVAL</div><br>
														
<!--														
$updateData['travel_authorizations::ceo_approval_rate_required'][0] == '0')||($updateData['travel_authorizations::ceo_approval_rate_status'][0] == '1')
$updateData['travel_authorizations::travel_advance_requested'][0] == '')||($updateData['travel_authorizations::ceo_approval_advance_status'][0] == '1')
$updateData['travel_authorizations::req_registration_fee'][0] == '')||($updateData['travel_authorizations::ceo_approval_reg_fee_status'][0] == '1')) //
-->

														<?php if($recordData['ceo_approval_rate_required'][0] == '1'){?>														
														<div class="tiny" style="background-color:#ffffff;border:1px dotted #000000;padding:5px;width:50%;margin-bottom:8px;font-weight:bold;color:#477643"><?php if($recordData['ceo_approval_rate_status'][0] == '1'){?><img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Hotel Rate | <span style="color:#0033ff"><strong>APPROVED</strong></span> | <?php echo $recordData['ceo_approval_rate_timestamp'][0];?><?php }else{?><img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Hotel Rate | <span style="color:#ff0000"><strong>PENDING</strong> | <a href="travel_admin.php?travel_auth_ID=<?php echo $travel_auth_ID;?>&travel_auth_row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&sign=2&signer=whoover" id="displayText" title="CEO: Click to approve the hotel rate for this TA." <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return ExCOnly()"';}else{echo 'onclick="return confirmSign2()"';}?>>Approve</a></span><?php }?></div>
														<?php }?>

														<?php if(($recordData['travel_advance_requested'][0] == 'Yes')||($recordData['travel_advance_requested'][0] == 'yes')){?>														
														<div class="tiny" style="background-color:#ffffff;border:1px dotted #000000;padding:5px;width:50%;margin-bottom:8px;font-weight:bold;color:#477643"><?php if($recordData['ceo_approval_advance_status'][0] == '1'){?><img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Travel Advance | <span style="color:#0033ff"><strong>APPROVED</strong></span> | <?php echo $recordData['ceo_approval_advance_timestamp'][0];?><?php }else{?><img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Travel Advance | <span style="color:#ff0000"><strong>PENDING</strong> | <a href="travel_admin.php?travel_auth_ID=<?php echo $travel_auth_ID;?>&travel_auth_row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&sign=3&signer=whoover" id="displayText" title="CEO: Click to approve the travel advance for this TA." <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return ExCOnly()"';}else{echo 'onclick="return confirmSign3()"';}?>>Approve</a></span><?php }?></div>
														<?php }?>

														<?php if($recordData['req_registration_fee'][0] > 0){?>														
														<div class="tiny" style="background-color:#ffffff;border:1px dotted #000000;padding:5px;width:50%;margin-bottom:8px;font-weight:bold;color:#477643"><?php if($recordData['ceo_approval_reg_fee_status'][0] == '1'){?><img src="/staff/sims/images/green_check.png" style="vertical-align:bottom"> CEO Registration Fee | <span style="color:#0033ff"><strong>APPROVED</strong></span> | <?php echo $recordData['ceo_approval_reg_fee_timestamp'][0];?><?php }else{?><img src="/common/images/bullets/exclamation.png" style="vertical-align:bottom"> CEO Registration Fee | <span style="color:#ff0000"><strong>PENDING</strong> | <a href="travel_admin.php?travel_auth_ID=<?php echo $travel_auth_ID;?>&travel_auth_row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&sign=4&signer=whoover" id="displayText" title="CEO: Click to approve the registration fee for this TA." <?php if($_SESSION['user_ID'] != 'whoover'){echo 'onclick="return ExCOnly()"';}else{echo 'onclick="return confirmSign4()"';}?>>Approve</a></span><?php }?></div>
														<?php }?>

															<table>
															<tr><td style="border-width:0px;padding:0px;margin:0px">

															<?php foreach($searchResult3['data'] as $key => $searchData3){ ?>
																<?php if($searchData3['signer_tier'][0] == '2'){ ?>
															
																	<td align="center" valign="bottom" style="padding:5px">
																	<?php if($searchData3['signer_status'][0] == '1'){ // BA APPROVED ?><img src="/staff/sims/signatures/<?php echo $searchData3['signer_ID'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="travel_admin.php?travel_auth_ID=<?php echo $travel_auth_ID;?>&travel_auth_row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&sign=1b&signer=<?php echo $searchData3['signer_ID'][0];?>" <?php if(($_SESSION['user_ID'] != $searchData3['signer_ID'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $searchData3['signer_ID'][0];?></a><?php }?><p>
																	<span class="tiny"><em><?php echo $searchData3['signer_role'][0];?></em><br><?php if($searchData3['signer_timestamp'][0] != ''){ ?><font color="999999">[<?php echo $searchData3['signer_timestamp'][0];?>]</font><?php } ?></span>
																	</td>
															
																<?php } ?>
															
															<?php } ?>

															<?php foreach($searchResult3['data'] as $key => $searchData3){ ?>
																<?php if($searchData3['signer_tier'][0] == '3'){ ?>
															
																	<td align="center" valign="bottom" style="padding:5px">
																	<?php if($searchData3['signer_status'][0] == '1'){ // CFO APPROVED ?><img src="/staff/sims/signatures/<?php echo $searchData3['signer_ID'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="travel_admin.php?travel_auth_ID=<?php echo $travel_auth_ID;?>&travel_auth_row_ID=<?php echo $searchData3['c_cwp_row_ID'][0];?>&sign=1c&signer=<?php echo $searchData3['signer_ID'][0];?>" <?php if(($_SESSION['user_ID'] != $searchData3['signer_ID'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $searchData3['signer_ID'][0];?></a><?php }?><p>
																	<span class="tiny"><em><?php echo $searchData3['signer_role'][0];?></em><br><?php if($searchData3['signer_timestamp'][0] != ''){ ?><font color="999999">[<?php echo $searchData3['signer_timestamp'][0];?>]</font><?php } ?></span>
																	</td>
															
																<?php } ?>
															
															<?php } ?>
														
														
		
														</tr></table>
		
													</td>
													
													</tr>
													
<?php }else{ ?>								

													<tr><td class="body" style="vertical-align:text-top" colspan="2">This TA has not been submitted or the staff member recently revised and re-submitted the original travel request. You will receive an email notification when your signature is required on this TA.</td></tr>

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





<? 

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


} elseif($action == 'apprv') { //IF A REQUIRED SIGNER IS VIEWING THE TRAVEL VOUCHER

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

if($sign == '1'){ // BGT AUTH OR PBA SIGNED THE TRAVEL VOUCHER  

		$trigger = rand();
		
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
		//$recordData4 = current($searchResult4['data']);
		##################################################
		## END: FIND ALL APPROVAL ROLES FOR THIS TA ##
		##################################################

		foreach($searchResult4['data'] as $key => $searchData4) { // LOOP THROUGH APPROVAL ROLES AND UPDATE STATUS FOR EACH IDENTICAL SIGNER
		
			if(($searchData4['signer_ID'][0] == $signer)&&($searchData4['signs_voucher'][0] == 'yes')){
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
		
		}


		##########################################################################################
		## START: FORWARD NOTIFICATION APPROVED TO SEDL AS IF ALL VOUCHER SIGNERS HAVE SIGNED ##
		##########################################################################################
		if($updateData['c_all_signers_status_vchr'][0] == '1'){ // ALL VOUCHER SIGNERS HAVE SIGNED THE DOCUMENT

			########################################################################
			## START: UPDATE TRAVEL AUTHORIZATION - VOUCHER APPROVED ##
			########################################################################
			$update2 = new FX($serverIP,$webCompanionPort);
			$update2 -> SetDBData('SIMS_2.fp7','travel_authorizations');
			$update2 -> SetDBPassword($webPW,$webUN);
			$update2 -> AddDBParam('-recid',$updateData['travel_authorizations::c_row_ID_cwp'][0]);
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
		


}

if(($sign == '2')&&($_SESSION['user_ID'] == 'whoover')){ // CEO SIGNED THE DOCUMENT TO APPROVE HOTEL OVERAGE

		//$trigger = rand();
		//$vc = $_GET['vc'];
		//$today = date("m/d/Y");
		#######################################################
		## START: UPDATE SIGNER STATUS FOR CEO HOTEL OVERAGE ##
		#######################################################
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$travel_auth_row_ID);
		
		$update -> AddDBParam('ceo_approval_rate_status','1');

/*
		if($vc == '1'){
		$update -> AddDBParam('ceo_approval_rate_status1','1');
		$update -> AddDBParam('ceo_approval_rate_date1',$today);
		}elseif($vc == '2'){
		$update -> AddDBParam('ceo_approval_rate_status2','1');
		$update -> AddDBParam('ceo_approval_rate_date2',$today);
		}elseif($vc == '3'){
		$update -> AddDBParam('ceo_approval_rate_status3','1');
		$update -> AddDBParam('ceo_approval_rate_date3',$today);
		}elseif($vc == '4'){
		$update -> AddDBParam('ceo_approval_rate_status4','1');
		$update -> AddDBParam('ceo_approval_rate_date4',$today);
		}elseif($vc == '5'){
		$update -> AddDBParam('ceo_approval_rate_status5','1');
		$update -> AddDBParam('ceo_approval_rate_date5',$today);
		}elseif($vc == '6'){
		$update -> AddDBParam('ceo_approval_rate_status6','1');
		$update -> AddDBParam('ceo_approval_rate_date6',$today);
		}else{
		$update -> AddDBParam('ceo_approval_rate_status','1');
		$update -> AddDBParam('ceo_approval_rate_date',$today);
		}
*/
		$updateResult = $update -> FMEdit();
		$updateData = current($updateResult['data']);
		//echo '<p>$updateResult[errorCode]: '.$updateResult['errorCode'];
		//echo '<p>$updateResult[foundCount]: '.$updateResult['foundCount'];

		#####################################################
		## END: UPDATE SIGNER STATUS FOR CEO HOTEL OVERAGE ##
		#####################################################
		
		###############################################
		## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
		###############################################
		$newrecord = new FX($serverIP,$webCompanionPort);
		$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
		$newrecord -> SetDBPassword($webPW,$webUN);
		$newrecord -> AddDBParam('user',$signer);
		$newrecord -> AddDBParam('action','APPROVE_Rate');
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
		$newrecord -> AddDBParam('action','APPROVE_TRAVEL_AUTH_CEO_RATE');
		$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
		$newrecord -> AddDBParam('object_ID',$updateData['travel_auth_ID'][0]);
		$newrecord -> AddDBParam('affected_row_ID',$updateData['c_row_ID_cwp'][0]);
		$newrecord -> AddDBParam('ip_address',$ip);
		$newrecordResult = $newrecord -> FMNew();
		//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
		//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
		###############################################
		## START: SAVE ACTION TO SIMS AUDIT LOG ##
		###############################################

		if( // IF THIS IS THE LAST REQUIRED SIGNATURE BEFORE CFO FINAL APPROVAL
			($updateData['travel_auth_approvals::c_all_signers_status_tier_1'][0] == '1') && // ALL TIER 1 SIGNERS HAVE SIGNED
			(($updateData['travel_auth_approvals::c_all_signers_status_tier_2'][0] == '1')||($updateData['travel_auth_approvals::c_all_signers_count_tier_2'][0] == '0')) && // ALL TIER 2 SIGNERS HAVE SIGNED OR NO TIER 2 SIGNERS REQUIRED
			(($updateData['ceo_approval_rate_required'][0] == '0')||($updateData['ceo_approval_rate_status'][0] == '1')) && // CEO HAS APPROVED HOTEL RATE OR TA DOES NOT REQUIRE HOTEL RATE APPROVAL 
			(($updateData['travel_advance_requested'][0] == '')||($updateData['ceo_approval_advance_status'][0] == '1')) && // CEO HAS APPROVED TRAVEL ADVANCE OR TRAVEL ADVANCE NOT REQUESTED
			(($updateData['req_registration_fee'][0] == '')||($updateData['ceo_approval_reg_fee_status'][0] == '1')) // CEO HAS APPROVED REGISTRATION FEE OR NO REGISTRATION FEE REQUIRED
		){ // ALL REGULAR SIGNERS HAVE SIGNED THE DOCUMENT AND NO ADDITIONAL CEO APPROVAL REQUIRED
		
		
		$destination =	stripslashes($updateData['event_venue_city'][0]).', '.$updateData['event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION
		
		
					#############################################################
					## START: TRIGGER NOTIFICATION E-MAIL TO CFO TO APPROVE TA ##
					#############################################################
					//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
					$to = 'sferguso@sedl.org';
					$subject = 'A travel authorization for '.stripslashes($updateData['staff_full_name'][0]).' requires your approval.';
					$message = 
					'Dear CFO,'."\n\n".
					
					//'[E-mail was sent to: sferguso@sedl.org]'."\n\n".
		
					'A travel authorization (TA) for staff member '.stripslashes($updateData['staff_full_name'][0]).' has been submitted by ('.$updateData['travel_admin_sims_user_ID'][0].') and requires your final approval.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' TRAVEL DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
					' Event: '.stripslashes($updateData['event_name'][0])."\n".
					' Destination: '.$destination."\n".
					' Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view and approve this travel authorization, click here: '."\n".
					'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
					###########################################################
					## END: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
					###########################################################
		
			
		}

}

if(($sign == '3')&&($_SESSION['user_ID'] == 'whoover')){ // CEO SIGNED THE DOCUMENT TO APPROVE TRAVEL ADVANCE

		//$trigger = rand();
		//$today = date("m/d/Y");
		########################################################
		## START: UPDATE SIGNER STATUS FOR CEO TRAVEL ADVANCE ##
		########################################################
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$travel_auth_row_ID);
		$update -> AddDBParam('ceo_approval_advance_status','1');
		
		$updateResult = $update -> FMEdit();
		$updateData = current($updateResult['data']);
		######################################################
		## END: UPDATE SIGNER STATUS FOR CEO TRAVEL ADVANCE ##
		######################################################

		###############################################
		## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
		###############################################
		$newrecord = new FX($serverIP,$webCompanionPort);
		$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
		$newrecord -> SetDBPassword($webPW,$webUN);
		$newrecord -> AddDBParam('user',$signer);
		$newrecord -> AddDBParam('action','APPROVE_Advance');
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
		$newrecord -> AddDBParam('action','APPROVE_TRAVEL_AUTH_CEO_ADV');
		$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
		$newrecord -> AddDBParam('object_ID',$updateData['travel_auth_ID'][0]);
		$newrecord -> AddDBParam('affected_row_ID',$updateData['c_row_ID_cwp'][0]);
		$newrecord -> AddDBParam('ip_address',$ip);
		$newrecordResult = $newrecord -> FMNew();
		//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
		//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
		###############################################
		## START: SAVE ACTION TO SIMS AUDIT LOG ##
		###############################################

		if( // IF THIS IS THE LAST REQUIRED SIGNATURE BEFORE CFO FINAL APPROVAL
			($updateData['travel_auth_approvals::c_all_signers_status_tier_1'][0] == '1') && // ALL TIER 1 SIGNERS HAVE SIGNED
			(($updateData['travel_auth_approvals::c_all_signers_status_tier_2'][0] == '1')||($updateData['travel_auth_approvals::c_all_signers_count_tier_2'][0] == '0')) && // ALL TIER 2 SIGNERS HAVE SIGNED OR NO TIER 2 SIGNERS REQUIRED
			(($updateData['ceo_approval_rate_required'][0] == '0')||($updateData['ceo_approval_rate_status'][0] == '1')) && // CEO HAS APPROVED HOTEL RATE OR TA DOES NOT REQUIRE HOTEL RATE APPROVAL 
			(($updateData['travel_advance_requested'][0] == '')||($updateData['ceo_approval_advance_status'][0] == '1')) && // CEO HAS APPROVED TRAVEL ADVANCE OR TRAVEL ADVANCE NOT REQUESTED
			(($updateData['req_registration_fee'][0] == '')||($updateData['ceo_approval_reg_fee_status'][0] == '1')) // CEO HAS APPROVED REGISTRATION FEE OR NO REGISTRATION FEE REQUIRED
		){ // ALL REGULAR SIGNERS HAVE SIGNED THE DOCUMENT AND NO ADDITIONAL CEO APPROVAL REQUIRED
		
		
		$destination =	stripslashes($updateData['event_venue_city'][0]).', '.$updateData['event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION
		
		
					#############################################################
					## START: TRIGGER NOTIFICATION E-MAIL TO CFO TO APPROVE TA ##
					#############################################################
					//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
					$to = 'sferguso@sedl.org';
					$subject = 'A travel authorization for '.stripslashes($updateData['staff_full_name'][0]).' requires your approval.';
					$message = 
					'Dear CFO,'."\n\n".
					
					//'[E-mail was sent to: sferguso@sedl.org]'."\n\n".
		
					'A travel authorization (TA) for staff member '.stripslashes($updateData['staff_full_name'][0]).' has been submitted by ('.$updateData['travel_admin_sims_user_ID'][0].') and requires your final approval.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' TRAVEL DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
					' Event: '.stripslashes($updateData['event_name'][0])."\n".
					' Destination: '.$destination."\n".
					' Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view and approve this travel authorization, click here: '."\n".
					'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
					###########################################################
					## END: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
					###########################################################
		
			
		}


}

if(($sign == '4')&&($_SESSION['user_ID'] == 'whoover')){ // CEO SIGNED THE DOCUMENT TO APPROVE REGISTRATION FEE

		//$trigger = rand();
		//$today = date("m/d/Y");
		#################################################
		## START: UPDATE SIGNER STATUS FOR CEO REG FEE ##
		#################################################
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$travel_auth_row_ID);
		$update -> AddDBParam('ceo_approval_reg_fee_status','1');
		
		$updateResult = $update -> FMEdit();
		$updateData = current($updateResult['data']);
		###############################################
		## END: UPDATE SIGNER STATUS FOR CEO REG FEE ##
		###############################################

		###############################################
		## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
		###############################################
		$newrecord = new FX($serverIP,$webCompanionPort);
		$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
		$newrecord -> SetDBPassword($webPW,$webUN);
		$newrecord -> AddDBParam('user',$signer);
		$newrecord -> AddDBParam('action','APPROVE_Reg_Fee');
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
		$newrecord -> AddDBParam('action','APPROVE_TRAVEL_AUTH_CEO_REG_FEE');
		$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
		$newrecord -> AddDBParam('object_ID',$updateData['travel_auth_ID'][0]);
		$newrecord -> AddDBParam('affected_row_ID',$updateData['c_row_ID_cwp'][0]);
		$newrecord -> AddDBParam('ip_address',$ip);
		$newrecordResult = $newrecord -> FMNew();
		//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
		//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
		###############################################
		## START: SAVE ACTION TO SIMS AUDIT LOG ##
		###############################################

		if( // IF THIS IS THE LAST REQUIRED SIGNATURE BEFORE CFO FINAL APPROVAL
			($updateData['travel_auth_approvals::c_all_signers_status_tier_1'][0] == '1') && // ALL TIER 1 SIGNERS HAVE SIGNED
			(($updateData['travel_auth_approvals::c_all_signers_status_tier_2'][0] == '1')||($updateData['travel_auth_approvals::c_all_signers_count_tier_2'][0] == '0')) && // ALL TIER 2 SIGNERS HAVE SIGNED OR NO TIER 2 SIGNERS REQUIRED
			(($updateData['ceo_approval_rate_required'][0] == '0')||($updateData['ceo_approval_rate_status'][0] == '1')) && // CEO HAS APPROVED HOTEL RATE OR TA DOES NOT REQUIRE HOTEL RATE APPROVAL 
			(($updateData['travel_advance_requested'][0] == '')||($updateData['ceo_approval_advance_status'][0] == '1')) && // CEO HAS APPROVED TRAVEL ADVANCE OR TRAVEL ADVANCE NOT REQUESTED
			(($updateData['req_registration_fee'][0] == '')||($updateData['ceo_approval_reg_fee_status'][0] == '1')) // CEO HAS APPROVED REGISTRATION FEE OR NO REGISTRATION FEE REQUIRED
		){ // ALL REGULAR SIGNERS HAVE SIGNED THE DOCUMENT AND NO ADDITIONAL CEO APPROVAL REQUIRED
		
		
		$destination =	stripslashes($updateData['event_venue_city'][0]).', '.$updateData['event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION
		
		
					#############################################################
					## START: TRIGGER NOTIFICATION E-MAIL TO CFO TO APPROVE TA ##
					#############################################################
					//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
					$to = 'sferguso@sedl.org';
					$subject = 'A travel authorization for '.stripslashes($updateData['staff_full_name'][0]).' requires your approval.';
					$message = 
					'Dear CFO,'."\n\n".
					
					//'[E-mail was sent to: sferguso@sedl.org]'."\n\n".
		
					'A travel authorization (TA) for staff member '.stripslashes($updateData['staff_full_name'][0]).' has been submitted by ('.$updateData['travel_admin_sims_user_ID'][0].') and requires your final approval.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' TRAVEL DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
					' Event: '.stripslashes($updateData['event_name'][0])."\n".
					' Destination: '.$destination."\n".
					' Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view and approve this travel authorization, click here: '."\n".
					'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
					###########################################################
					## END: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
					###########################################################
		
			
		}


}


if($sign == '1b'){ // CPO OR CEO SIGNED THE DOCUMENT - TIER 2 SIGNERS

		$trigger = rand();
		
		####################################################
		## START: FIND ALL APPROVAL ROLES FOR THIS SIGNER ##
		####################################################
		$search4 = new FX($serverIP,$webCompanionPort);
		$search4 -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
		$search4 -> SetDBPassword($webPW,$webUN);
		$search4 -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
		$search4 -> AddDBParam('signer_ID','=='.$signer);
		//$search4 -> AddDBParam('-lop','or');
		
		$searchResult4 = $search4 -> FMFind();
		
		//echo '<p>$searchResult4[errorCode]: '.$searchResult4['errorCode'];
		//echo '<p>$searchResult4[foundCount]: '.$searchResult4['foundCount'];
		//print_r ($searchResult4);
		//$recordData4 = current($searchResult4['data']);
		##################################################
		## END: FIND ALL APPROVAL ROLES FOR THIS SIGNER ##
		##################################################

		foreach($searchResult4['data'] as $key => $searchData4) { // LOOP THROUGH APPROVAL ROLES AND UPDATE STATUS FOR EACH IDENTICAL SIGNER

		########################################################################
		## START: UPDATE SIGNER STATUS FOR ALL APPROVAL ROLES FOR THIS SIGNER ##
		########################################################################
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$searchData4['c_cwp_row_ID'][0]);
		$update -> AddDBParam('signer_status','1');
		$update -> AddDBParam('signer_timestamp_trigger',$trigger);
		
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
		$newrecord -> AddDBParam('action','SIGN_EO');
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
		$newrecord -> AddDBParam('action','SIGN_TRAVEL_AUTH_EO');
		$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
		$newrecord -> AddDBParam('object_ID',$updateData['travel_auth_ID'][0]);
		$newrecord -> AddDBParam('affected_row_ID',$updateData['c_row_ID_cwp'][0]);
		$newrecord -> AddDBParam('ip_address',$ip);
		$newrecordResult = $newrecord -> FMNew();
		//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
		//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
		###############################################
		## START: SAVE ACTION TO SIMS AUDIT LOG ##
		###############################################

		}



		if( // IF THIS IS THE LAST REQUIRED SIGNATURE BEFORE CFO FINAL APPROVAL
			($updateData['c_all_signers_status_tier_1'][0] == '1') && // ALL TIER 1 SIGNERS HAVE SIGNED
			(($updateData['c_all_signers_status_tier_2'][0] == '1')||($updateData['c_all_signers_count_tier_2'][0] == '0')) && // ALL TIER 2 SIGNERS HAVE SIGNED OR NO TIER 2 SIGNERS REQUIRED
			(($updateData['travel_authorizations::ceo_approval_rate_required'][0] == '0')||($updateData['travel_authorizations::ceo_approval_rate_status'][0] == '1')) && // CEO HAS APPROVED HOTEL RATE OR TA DOES NOT REQUIRE HOTEL RATE APPROVAL 
			(($updateData['travel_authorizations::travel_advance_requested'][0] == '')||($updateData['travel_authorizations::ceo_approval_advance_status'][0] == '1')) && // CEO HAS APPROVED TRAVEL ADVANCE OR TRAVEL ADVANCE NOT REQUESTED
			(($updateData['travel_authorizations::req_registration_fee'][0] == '')||($updateData['travel_authorizations::ceo_approval_reg_fee_status'][0] == '1')) // CEO HAS APPROVED REGISTRATION FEE OR NO REGISTRATION FEE REQUIRED
		){ // ALL REGULAR SIGNERS HAVE SIGNED THE DOCUMENT AND NO ADDITIONAL CEO APPROVAL REQUIRED
		
		
		$destination =	stripslashes($updateData['travel_authorizations::event_venue_city'][0]).', '.$updateData['travel_authorizations::event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION
		
		
					#############################################################
					## START: TRIGGER NOTIFICATION E-MAIL TO CFO TO APPROVE TA ##
					#############################################################
					//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
					$to = 'sferguso@sedl.org';
					$subject = 'A travel authorization for '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' requires your approval.';
					$message = 
					'Dear CFO,'."\n\n".
					
					//'[E-mail was sent to: sferguso@sedl.org]'."\n\n".
		
					'A travel authorization (TA) for staff member '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been submitted by ('.$updateData['travel_authorizations::travel_admin_sims_user_ID'][0].') and requires your final approval.'."\n\n".
					
					'------------------------------------------------------------'."\n".
					' TRAVEL DETAILS'."\n".
					'------------------------------------------------------------'."\n".
					' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
					' Event: '.stripslashes($updateData['travel_authorizations::event_name'][0])."\n".
					' Destination: '.$destination."\n".
					' Date(s) of Travel: '.$updateData['travel_authorizations::leave_date_requested'][0].' to '.$updateData['travel_authorizations::return_date_requested'][0]."\n".
					'------------------------------------------------------------'."\n\n".
					
					'To view and approve this travel authorization, click here: '."\n".
					'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0]."\n\n".
										
					'------------------------------------------------------------------------------------------------------------------'."\n".
					'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
					'------------------------------------------------------------------------------------------------------------------';
					
					$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
					
					mail($to, $subject, $message, $headers);
						
					###########################################################
					## END: TRIGGER NOTIFICATION E-MAIL TO CEO TO APPROVE TA ##
					###########################################################
		
			
		}
		
}		

if(($sign == '1c')&&($_SESSION['user_ID'] == 'sferguso')){ // CFO SIGNED THE DOCUMENT - FINAL APPROVAL

		$trigger = rand();

		########################################################################
		## START: UPDATE SIGNER STATUS FOR CFO FINAL APPROVAL ##
		########################################################################
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('SIMS_2.fp7','travel_auth_approvals');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$travel_auth_row_ID);
		$update -> AddDBParam('signer_status','1');
		$update -> AddDBParam('signer_timestamp_trigger',$trigger);
		
		$updateResult = $update -> FMEdit();
		$updateData = current($updateResult['data']);
		######################################################################
		## END: UPDATE SIGNER STATUS FOR CFO FINAL APPROVAL ##
		######################################################################
		
		########################################################################
		## START: UPDATE TRAVEL AUTHORIZATION - APPROVED ##
		########################################################################
		$update2 = new FX($serverIP,$webCompanionPort);
		$update2 -> SetDBData('SIMS_2.fp7','travel_authorizations');
		$update2 -> SetDBPassword($webPW,$webUN);
		$update2 -> AddDBParam('-recid',$updateData['travel_authorizations::c_row_ID_cwp'][0]);
		$update2 -> AddDBParam('approval_status','Approved');
		$update2 -> AddDBParam('cfo_approval_status','1');
		
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
		$newrecord -> AddDBParam('user',$signer);
		$newrecord -> AddDBParam('action','APPROVE_TA');
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
		$newrecord -> AddDBParam('action','APPROVE_TRAVEL_AUTH_CFO');
		$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
		$newrecord -> AddDBParam('object_ID',$updateData['travel_auth_ID'][0]);
		$newrecord -> AddDBParam('affected_row_ID',$updateData['c_row_ID_cwp'][0]);
		$newrecord -> AddDBParam('ip_address',$ip);
		$newrecordResult = $newrecord -> FMNew();
		//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
		//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
		###############################################
		## START: SAVE ACTION TO SIMS AUDIT LOG ##
		###############################################
		
		$destination =	stripslashes($updateData['travel_authorizations::event_venue_city'][0]).', '.$updateData['travel_authorizations::event_venue_state'][0];	// SET $destination STRING FOR E-MAIL NOTIFICATION

		#############################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO AA - CFO APPROVED ##
		#############################################################
		//$to = 'eric.waters@sedl.org';//$current.'@sedl.org';
		$to = $updateData2['staff::travel_admin_sims_user_ID'][0].'@sedl.org';
		$subject = 'A travel authorization for '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been approved by the CFO.';
		$message = 
		'Travel Admin,'."\n\n".
		
		//'[E-mail was sent to: traveladmin@sedl.org]'."\n\n".

		'A travel authorization (TA) for staff member '.stripslashes($updateData['travel_authorizations::staff_full_name'][0]).' has been approved by the CFO.'."\n\n".
		
		'------------------------------------------------------------'."\n".
		' TRAVEL DETAILS'."\n".
		'------------------------------------------------------------'."\n".
		' TA ID: '.$updateData['travel_auth_ID'][0]."\n".
		' Event: '.stripslashes($updateData['travel_authorizations::event_name'][0])."\n".
		' Destination: '.$destination."\n".
		' Date(s) of Travel: '.$updateData['travel_authorizations::leave_date_requested'][0].' to '.$updateData['travel_authorizations::return_date_requested'][0]."\n".
		'------------------------------------------------------------'."\n\n".
		
		'To view and process this travel authorization, click here: '."\n".
		'fmp7://SIMS_2.fp7'."\n\n".
							
		'------------------------------------------------------------------------------------------------------------------'."\n".
		'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
		'------------------------------------------------------------------------------------------------------------------';
		
		$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: ewaters@sedl.org';
		
		mail($to, $subject, $message, $headers);
			
		###########################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO AA - CFO APPROVED ##
		###########################################################


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

#################################################################
## START: FIND RECEIPTS RELATED TO THIS TA ##
#################################################################
$search7 = new FX($serverIP,$webCompanionPort);
$search7 -> SetDBData('SIMS_2.fp7','travel_auth_receipts');
$search7 -> SetDBPassword($webPW,$webUN);
$search7 -> AddDBParam('travel_auth_ID','=='.$travel_auth_ID);
//$search7 -> AddDBParam('-lop','or');

$search7 -> AddSortParam('creation_timestamp','ascend');


$searchResult7 = $search7 -> FMFind();

//echo '<p>$searchResult7[errorCode]: '.$searchResult7['errorCode'];
//echo '<p>$searchResult7[foundCount]: '.$searchResult7['foundCount'];
//print_r ($searchResult7);
//$recordData7 = current($searchResult7['data']);
###############################################################
## END: FIND RECEIPTS RELATED TO THIS TA ##
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

function preventSignBA() { 
	alert ("Staff Member must sign this voucher before BA approval.")
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

			<tr><td colspan="2"><p class="alert_small"><b>Supervisor/Budget Authority (<?php echo $_SESSION['user_ID'];?>)</b>: You have successfully approved this travel voucher (TV). <img src="/staff/sims/images/green_check.png"> | <a href="menu_travel_admin_spvsr.php">Close TV</a> | <a href="travel_admin.php?travel_auth_ID=<?php echo $recordData['travel_auth_ID'][0];?>">Show TA</a></p></td></tr>

<?php }else{ ?>

			<tr><td colspan="2"><p class="alert_small"><b>Supervisor/Budget Authority (<?php echo $_SESSION['user_ID'];?>)</b>: To approve this travel voucher (TV), click the appropriate signature box below. | <a href="menu_travel_admin_spvsr.php">Close TV</a> | <a href="travel_admin.php?travel_auth_ID=<?php echo $recordData['travel_auth_ID'][0];?>">Show TA</a></p></td></tr>

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
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Period of Travel:</strong></td><td style="border:0px;padding:6px"><?php echo $recordData['leave_date_requested'][0];?> to <?php echo $recordData['return_date_requested'][0];?></td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Purpose of Travel:</strong></td><td style="border:0px;padding:6px"><?php echo stripslashes($recordData['c_purpose_of_travel_csv'][0]);?></td></tr>
												<tr><td valign="top" style="border:0px;padding:6px;background-color:#ffffff" align="right"><strong>Description:</strong></td><td style="border:0px;padding:6px"><?php echo stripslashes($recordData['purpose_of_travel_descr'][0]);?></td></tr>
												</table>														
												
												
										</td>
										
										</tr>
										</table>
										

								</td>
<!--
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
#######    APPROVAL LOG             ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<td style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>APPROVAL LOG</strong></div>



								
									<table cellspacing="2" style="margin-top:6px">

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf"><strong>DATE</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>USER</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>ACTION</strong></td></tr>

									<?php if($searchResult6['foundCount'] > 0){ // SHOW TA STATUS LOG ?>
									<?php foreach($searchResult6['data'] as $key => $searchData6) { if($searchData6['comment'][0] != ''){$rowcolor = '#ffd6d6';}elseif(($searchData6['action'][0] == 'APPROVE_TA')||($searchData6['action'][0] == 'APPROVE_TV')){$rowcolor = '#ace29f';}else{$rowcolor = '#ebebeb';} ?>

										<tr><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData6['creation_timestamp'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData6['user'][0];?></td><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData6['action'][0];?></td></tr>
										<?php if($searchData6['comment'][0] != ''){?><tr><td class="tiny" style="background-color:<?php echo $rowcolor;?>;vertical-align:text-top;border:1px dotted #000000;padding:3px" colspan="3"><?php echo $searchData6['comment'][0];?></td></tr><?php }?>

									<?php } ?>
									<?php } ?>
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

								<tr><td colspan="2" class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>VOUCHER EXPENSE DETAILS</strong></div><br>

								
									<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
										<tr style="background-color:#ebebeb;vertical-align: text-top">
										<td>Date</td><td>Fares</td><td>Taxi (T)<br>Parking (P)<br>Limo (L)</td><td>Mileage (.565/mi)<br>#Miles/Amount</td><td>M&IE<br><span class="tiny" style="color:#ff0000">[CONUS rate in red]</span></td><td>Lodging<br><span class="tiny" style="color:#ff0000">[CONUS rate in red]</span></td><td>Other<br>Type/Amount</td><td style="border-left:3px solid #cccccc;text-align:right">TOTAL</td>
										</tr>								
		
										<?php $c=1; foreach($searchResult2['data'] as $key => $searchData) { ?>
										<tr>
										
										<td style="vertical-align:text-top"><?php echo $searchData['travel_date'][0]. ' <span class="tiny">('.strtoupper($searchData['c_travel_day_name'][0]).')</span>';?></td>
										<td style="text-align: right;vertical-align:text-top"><?php if($searchData['voucher_fares'][0] != ''){echo '$'.number_format($searchData['voucher_fares'][0],2);}?></td>
										<td style="text-align: right;vertical-align:text-top">
										<?php if($searchData['voucher_tpl'][0] != ''){echo '$'.number_format($searchData['voucher_tpl'][0],2).' <strong>T</strong><br>';}?>
										<?php if($searchData['voucher_tpl_park'][0] != ''){echo '$'.number_format($searchData['voucher_tpl_park'][0],2).' <strong>P</strong><br>';}?>
										<?php if($searchData['voucher_tpl_limo'][0] != ''){echo '$'.number_format($searchData['voucher_tpl_limo'][0],2).' <strong>L</strong><br>';}?>
										</td>
										<td style="vertical-align:text-top"><?php if($searchData['voucher_mileage_num'][0] != ''){echo $searchData['voucher_mileage_num'][0];?> mi / $<?php echo number_format($searchData['voucher_mileage_amt'][0],2);}?></td>
										<td style="text-align: right;vertical-align:text-top"><?php if($searchData['voucher_mie'][0] != ''){echo '$'.number_format($searchData['voucher_mie'][0],2);}?> <span class="tiny" style="color:#ff0000">[$<?php echo $searchData['mie'][0];?>]</span></td>
										<td style="text-align: right;vertical-align:text-top"><?php if($searchData['voucher_lodging'][0] != ''){echo '$'.number_format($searchData['voucher_lodging'][0],2);}?> <span class="tiny" style="color:#ff0000">[$<?php echo $searchData['lodging'][0];?>]</span></td>
										<td style="vertical-align:text-top">
										<table style="width:100%">
										<?php if($searchData['voucher_other_1_type'][0] != ''){echo '<td style="border:0px">'.$searchData['voucher_other_1_type'][0];?></td><td style="border:0px;text-align:right">$<?php echo number_format($searchData['voucher_other_1'][0],2).'</td></tr>';}?>
										<?php if($searchData['voucher_other_2_type'][0] != ''){echo '<td style="border:0px">'.$searchData['voucher_other_2_type'][0];?></td><td style="border:0px;text-align:right">$<?php echo number_format($searchData['voucher_other_2'][0],2).'</td></tr>';}?>
										<?php if($searchData['voucher_other_3_type'][0] != ''){echo '<td style="border:0px">'.$searchData['voucher_other_3_type'][0];?></td><td style="border:0px;text-align:right">$<?php echo number_format($searchData['voucher_other_3'][0],2).'</td></tr>';}?>
										<?php if($searchData['voucher_other_4_type'][0] != ''){echo '<td style="border:0px">'.$searchData['voucher_other_4_type'][0];?></td><td style="border:0px;text-align:right">$<?php echo number_format($searchData['voucher_other_4'][0],2).'</td></tr>';}?>
										</table>
										</td>
										<td style="text-align: right;vertical-align:text-top;border-left:3px solid #cccccc">$<?php echo number_format($searchData['c_voucher_day_total'][0],2);?></td>
										
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
#######    VOUCHER RECEIPTS         ########################################################################################################################################################################################################################
#######                             ########################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################
-->

								<tr><td colspan="2" class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>VOUCHER RECEIPTS</strong></div><br>

								
									<table cellpadding="8" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
										<tr style="background-color:#ebebeb;vertical-align: text-top">
										<td>Date</td><td>Vendor</td><td>Description</td><td style="text-align:right">Amount</td><td>Receipt File</td></td>
										</tr>								
		
										<?php foreach($searchResult7['data'] as $key => $searchData) { ?>
										<tr>
										
										<td style="vertical-align:text-top"><?php echo $searchData['receipt_date'][0];?></td>
										<td style="vertical-align:text-top"><?php echo $searchData['vendor'][0];?></td>
										<td style="vertical-align:text-top"><?php echo $searchData['description'][0];?></td>
										<td style="vertical-align:text-top;text-align:right">$<?php echo $searchData['amount'][0];?></td>
										<td style="vertical-align:text-top"><span class="tiny"><a href="http://198.214.141.190/sims/travel_receipts/<?php echo $searchData['scan_file_url'][0];?>" target="_blank">View/download</a></span></td>
										</tr>
										<?php } ?>
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

								<tr class="body"><td colspan="2" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>SIGNATURES</strong></div><br>
													
<?php if($recordData['TA_voucher_submitted_timestamp'][0] != ''){ ?>

													
														<table class="sims" cellspacing="1" cellpadding="10" border="1" width="100%">
		
		
														<tr class="body" valign="top"><td><div style="border:1px dotted #000000;background-color:#bddbee;padding:5px" class="tiny">STAFF MEMBER APPROVAL</div><br>
														
															<table>

															<tr><td style="border-width:0px;padding:0px;margin:0px">
															
																	<td align="center" valign="bottom" style="padding:5px">
																	<?php if($recordData['staff_vchr_approval_status'][0] == '1'){ // STAFF APPROVED ?><img src="/staff/sims/signatures/<?php echo $recordData['staff_sims_ID'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?>Pending: (<?php echo $recordData['staff_sims_ID'][0];?>)<?php }?><p>
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
																	<?php if($searchData3['signer_status_vchr'][0] == '1'){ // BA APPROVED ?><img src="/staff/sims/signatures/<?php echo $searchData3['signer_ID'][0];?>.png"><img src="/staff/sims/images/green_check.png" style="vertical-align:top"><?php }else{?><a href="travel_admin.php?action=apprv&travel_auth_ID=<?php echo $travel_auth_ID;?>&travel_auth_row_ID=<?php echo $recordData['c_row_ID_cwp'][0];?>&sign=1&signer=<?php echo $searchData3['signer_ID'][0];?>" <?php if(($_SESSION['user_ID'] != $searchData3['signer_ID'][0])&&($_SESSION['user_ID'] != 'sferguso')){echo 'onclick="return wrongSigner()"';}elseif($recordData['staff_vchr_approval_status'][0] !== '1'){echo 'onclick="return preventSignBA()"';}else{echo 'onclick="return confirmSign()"';}?>><?php echo $searchData3['signer_ID'][0];?></a><?php }?><p>
																	<span class="tiny"><em><?php echo $searchData3['signer_role'][0];?></em><br><?php if($searchData3['signer_timestamp_vchr'][0] != ''){ ?><font color="999999">[<?php echo $searchData3['signer_timestamp_vchr'][0];?>]</font><?php } ?></span>
																	</td>
															
															
															<?php } ?>
			
															</tr></table>
		
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


} elseif($action == 'approve_tr') { 

$row_ID = $_GET['row_ID'];
$date_prepared = date("m/d/Y");
$multi_dest = $_GET['multi_dest'];
$num_dest = $_GET['num_dest'];
$role = $_GET['role'];

$trigger = rand();

if($role == 'spvsr'){ // SPVSR IS APPROVING THE TRAVEL REQUEST


	#################################################
	## START: UPDATE TRAVEL AUTHORIZATION RECORD ##
	#################################################
	$update = new FX($serverIP,$webCompanionPort);
	$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
	$update -> SetDBPassword($webPW,$webUN);
	$update -> AddDBParam('-recid',$row_ID);
	
	$update -> AddDBParam('approval_status_tr_spvsr','Approved');
	$update -> AddDBParam('tr_approved_by_spvsr',$_SESSION['user_ID']);
	
	$updateResult = $update -> FMEdit();
	
	//  echo  '<p>errorCode: '.$updateResult['errorCode'];
	//  echo  '<p>foundCount: '.$updateResult['foundCount'];
	$updateData = current($updateResult['data']);
	$multi_dest = $updateData['multi_dest'][0];
	$travel_admin = $updateData['travel_admin_sims_user_ID'][0];
	$travel_admin_name = $updateData['travel_authorizations_staff_by_current_travel_admin::c_full_name_first_last'][0];
	$pba = $updateData['staff::bgt_auth_primary_sims_user_ID'][0];
	
	#################################################
	## END: UPDATE TRAVEL AUTHORIZATION RECORD ##
	#################################################
	
	
	
	
	
	if($updateResult['errorCode'] == '0'){  // THE TRAVEL REQUEST WAS SUCCESSFULLY UPDATED
	
	###############################################
	## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
	###############################################
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','SIGN_TR_SPVSR');
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
	$newrecord -> AddDBParam('action','APPROVE_TRAVEL_REQUEST_SPVSR');
	$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
	$newrecord -> AddDBParam('object_ID',$updateData['travel_auth_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$row_ID);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	###############################################
	## START: SAVE ACTION TO SIMS AUDIT LOG ##
	###############################################
	
	
	
	$_SESSION['travel_request_approved_spvsr'] = '1';
	
		if($multi_dest == 'yes'){ // MULTI-DESTINATION EMAIL
		########################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO PBA (M) ##
		########################################################
		$to = 'eric.waters@sedl.org';
		//$to = $updateData['signer_ID_pba'][0].'@sedl.org';
		$subject = stripslashes($updateData['staff::c_full_name_first_last'][0]).' has submitted a travel request (m)';
		$message = 
		'Dear '.$updateData['signer_ID_pba'][0].','."\n\n".
		
		'A travel request has been submitted by '.stripslashes($updateData['staff::c_full_name_first_last'][0]).' and requires your approval.'."\n\n".
		
		'------------------------------------------------------------'."\n".
		' TRAVEL REQUEST DETAILS'."\n".
		'------------------------------------------------------------'."\n".
		'Event: '.stripslashes($updateData['event_name'][0])."\n".
		'Destinations: '.$updateData['c_destinations_all_display_venues_csv'][0]."\n".
		'Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
		'------------------------------------------------------------'."\n\n".
		
		'To view and approve this travel request, click here: '."\n".
		'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0].'&action=view_tr'."\n\n".
		
		'------------------------------------------------------------------------------------------------------------------'."\n".
		'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
		'------------------------------------------------------------------------------------------------------------------';
		
		$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$updateData['signer_ID_spvsr'][0].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
		
		mail($to, $subject, $message, $headers);
		######################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO PBA (M) ##
		######################################################

		}else{ // SINGLE-DESTINATION EMAIL

		########################################################
		## START: TRIGGER NOTIFICATION E-MAIL TO PBA (S) ##
		########################################################
		//$to = 'eric.waters@sedl.org';
		$to = $updateData['signer_ID_pba'][0].'@sedl.org';
		$subject = stripslashes($updateData['staff::c_full_name_first_last'][0]).' has submitted a travel request (s)';
		$message = 
		'Dear '.$updateData['signer_ID_pba'][0].','."\n\n".
		
		'A travel request for '.stripslashes($updateData['staff::c_full_name_first_last'][0]).' has been submitted to SIMS and requires your approval.'."\n\n".
		
		'------------------------------------------------------------'."\n".
		' TRAVEL REQUEST DETAILS'."\n".
		'------------------------------------------------------------'."\n".
		'Event: '.stripslashes($updateData['event_name'][0])."\n".
		'Destination: '.$updateData['event_venue_city'][0].', '.$updateData['event_venue_state'][0]."\n".
		'Date(s) of Travel: '.$updateData['leave_date_requested'][0].' to '.$updateData['return_date_requested'][0]."\n".
		'------------------------------------------------------------'."\n\n".
		
		'To view and approve this travel request, click here: '."\n".
		'http://www.sedl.org/staff/sims/travel_admin.php?travel_auth_ID='.$updateData['travel_auth_ID'][0].'&action=view_tr'."\n\n".
		
		'------------------------------------------------------------------------------------------------------------------'."\n".
		'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
		'------------------------------------------------------------------------------------------------------------------';
		
		$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$updateData['signer_ID_spvsr'][0].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
		
		mail($to, $subject, $message, $headers);
		######################################################
		## END: TRIGGER NOTIFICATION E-MAIL TO PBA (S) ##
		######################################################
		
		}

	} else { // THERE WAS AN ERROR UPDATING THE TRAVEL REQUEST
	$_SESSION['travel_request_approved_spvsr'] = '2';
	
	}
	
	header('Location: http://www.sedl.org/staff/sims/menu_travel_admin_spvsr.php');
	exit;


}else{ //PBA IS APPROVING THE TRAVEL REQUEST


	#################################################
	## START: UPDATE TRAVEL AUTHORIZATION RECORD ##
	#################################################
	$update = new FX($serverIP,$webCompanionPort);
	$update -> SetDBData('SIMS_2.fp7','travel_authorizations');
	$update -> SetDBPassword($webPW,$webUN);
	$update -> AddDBParam('-recid',$row_ID);
	
	$update -> AddDBParam('approval_status_tr','Approved');
	$update -> AddDBParam('tr_approved_by',$_SESSION['user_ID']);
	$update -> AddDBParam('tr_approved_trigger',$trigger);
	
	$updateResult = $update -> FMEdit();
	
	//  echo  '<p>errorCode: '.$updateResult['errorCode'];
	//  echo  '<p>foundCount: '.$updateResult['foundCount'];
	$updateData = current($updateResult['data']);
	$multi_dest = $updateData['multi_dest'][0];
	$travel_admin = $updateData['travel_admin_sims_user_ID'][0];
	$travel_admin_name = $updateData['travel_authorizations_staff_by_current_travel_admin::c_full_name_first_last'][0];
	$pba = $updateData['staff::bgt_auth_primary_sims_user_ID'][0];
	
	#################################################
	## END: UPDATE TRAVEL AUTHORIZATION RECORD ##
	#################################################
	
	
	
	
	
	if($updateResult['errorCode'] == '0'){  // THE TRAVEL REQUEST WAS SUCCESSFULLY UPDATED
	
	###############################################
	## START: SAVE ACTION TO TRAVEL APPROVAL LOG ##
	###############################################
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','travel_auth_user_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','APPROVE_TR');
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
	$newrecord -> AddDBParam('action','APPROVE_TRAVEL_REQUEST_PBA');
	$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
	$newrecord -> AddDBParam('object_ID',$updateData['travel_auth_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$row_ID);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	###############################################
	## START: SAVE ACTION TO SIMS AUDIT LOG ##
	###############################################
	
	
	
	$_SESSION['travel_request_approved_pba'] = '1';
	
	##############################################################
	## START: TRIGGER NOTIFICATION E-MAIL TO TRAVEL ADMIN ##
	##############################################################
	//$to = 'eric.waters@sedl.org';
	$to = $travel_admin.'@sedl.org';
	$subject = stripslashes($updateData['staff_full_name'][0]).' has submitted an approved travel request requiring processing';
	$message = 
	'Dear '.$travel_admin_name.','."\n\n".
	
	//'[E-mail was sent to: '.$travel_admin.'@sedl.org]'."\n\n".
	
	'A travel request for '.stripslashes($updateData['staff_full_name'][0]).' has been approved by ('.$pba.') and submitted to SIMS for processing.'."\n\n".
	
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
	
	} else { // THERE WAS AN ERROR UPDATING THE TRAVEL REQUEST
	$_SESSION['travel_request_approved_pba'] = '2';
	
	}
	
	header('Location: http://www.sedl.org/staff/sims/menu_travel_admin_spvsr.php');
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


} else { 
echo 'Error_3367'; // NO ACTION VARIABLE

	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','Error_3367: travel_admin.php');
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
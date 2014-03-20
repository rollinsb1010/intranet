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


//$action = $_GET['action'];
$travel_auth_ID = $_GET['id'];
//$mod = $_GET['mod'];
//$approval_status = $_GET['app'];


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

header("Content-Type: application/msword");
 
print "<table border=\"0\" width=\"100%\" style=\"margin:0px 0px 25px 0px\"><tr><td colspan=\"2\"><img src=\"http://www.sedl.org/staff/sims/images/logo-new-grayscale.png\" height=\"34\" width=\"86\"></td><td colspan=\"2\" align=\"right\" style=\"font-size:x-large\"><strong>Itinerary</strong></td></tr></table>";


print "<table border=\"1\" width=\"100%\" cellpadding=\"15\"><tr><td colspan=\"4\">Traveler: ".$recordData['staff::c_full_name_first_last'][0]."</td></tr>";
 
print "<tr style=\"background-color:#ebebeb\"><td>Destination</td><td>Budget Code</td><td>Travel Dates</td><td>CONUS:</td></tr>";

print "<tr style=\"background-color:#ffffff\"><td>".$recordData['c_destinations_all_display_venues'][0]."</td><td>".$recordData['budget_code'][0]."</td><td>".$recordData['leave_date_requested'][0]." - ".$recordData['return_date_requested'][0]."</td><td>CONUS:</td></tr></table>";


exit;


?>

<html>
<head>
<title>SIMS: Travel Authorizations</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
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
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Mode of Transportation:</td>
								<td width="100%"><?php echo $recordData['c_trans_mode_description'][0];?></td>
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
								<td width="100%" colspan="2" bgcolor="#d3e5f7"><input type="text" name="preferred_hotel_name" size="30" value="<?php echo $recordData['preferred_hotel_name'][0];?>"> at <input type="text" name="hotel_rate" size="5" value="<?php echo $recordData['hotel_rate'][0];?>" style="text-align: right"> <strong>per night</strong> 
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
										<td width="100%" colspan="2" bgcolor="#d3e5f7"><input type="text" name="preferred_hotel_name1" size="30" value="<?php echo $recordData['preferred_hotel_name1'][0];?>"> at <input type="text" name="hotel_rate1" size="5" value="<?php echo $recordData['hotel_rate1'][0];?>" style="text-align: right"> <strong>per night</strong> 
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
										<td width="100%" colspan="2" bgcolor="#d3e5f7"><input type="text" name="preferred_hotel_name2" size="30" value="<?php echo $recordData['preferred_hotel_name2'][0];?>"> at <input type="text" name="hotel_rate2" size="5" value="<?php echo $recordData['hotel_rate2'][0];?>" style="text-align: right"> <strong>per night</strong> 
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
										<td width="100%" colspan="2" bgcolor="#d3e5f7"><input type="text" name="preferred_hotel_name3" size="30" value="<?php echo $recordData['preferred_hotel_name3'][0];?>"> at <input type="text" name="hotel_rate3" size="5" value="<?php echo $recordData['hotel_rate3'][0];?>" style="text-align: right"> <strong>per night</strong> 
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
										<td width="100%" colspan="2" bgcolor="#d3e5f7"><input type="text" name="preferred_hotel_name4" size="30" value="<?php echo $recordData['preferred_hotel_name4'][0];?>"> at <input type="text" name="hotel_rate4" size="5" value="<?php echo $recordData['hotel_rate4'][0];?>" style="text-align: right"> <strong>per night</strong> 
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
										<td width="100%" colspan="2" bgcolor="#d3e5f7"><input type="text" name="preferred_hotel_name5" size="30" value="<?php echo $recordData['preferred_hotel_name5'][0];?>"> at <input type="text" name="hotel_rate5" size="5" value="<?php echo $recordData['hotel_rate5'][0];?>" style="text-align: right"> <strong>per night</strong> 
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
										<td width="100%" colspan="2" bgcolor="#d3e5f7"><input type="text" name="preferred_hotel_name6" size="30" value="<?php echo $recordData['preferred_hotel_name6'][0];?>"> at <input type="text" name="hotel_rate6" size="5" value="<?php echo $recordData['hotel_rate6'][0];?>" style="text-align: right"> <strong>per night</strong> 
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
								<td class="body" nowrap valign="top" align="right" bgcolor="#ebebeb">Budget Code(s):</td>
								<td width="100%"><?php echo $recordData['c_budget_codes_csv'][0];?></td>
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


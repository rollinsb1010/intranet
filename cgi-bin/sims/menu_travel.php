<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


if($_GET['delete_key'] != ''){
$delete_row_ID = $_GET['delete_key'];
##############################################
## START: FIND RECORD INFO TO BE DELETED ##
##############################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','travel_authorizations',12);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('c_row_ID_cwp','=='.$delete_row_ID);
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam('c_event_start_date_menu_sort_display','descend');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['travel_request_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
############################################
## END: FIND RECORD INFO TO BE DELETED ##
############################################

########################################################
## START: TRIGGER NOTIFICATION E-MAIL TO ADMIN ##
########################################################
//$to = 'eric.waters@sedl.org';
$to = $recordData['travel_admin_sims_user_ID'][0].'@sedl.org';
$subject = stripslashes($recordData['staff::c_full_name_first_last'][0]).' has deleted a travel request';
$message = 
'Travel Admin'.','."\n\n".

stripslashes($recordData['staff::c_full_name_first_last'][0]).' has deleted a recent travel request.'."\n\n".

'------------------------------------------------------------'."\n".
' TRAVEL REQUEST DETAILS'."\n".
'------------------------------------------------------------'."\n".
'ID: '.$recordData['travel_auth_ID'][0]."\n".
'Event: '.stripslashes($recordData['event_name'][0])."\n".
'Destination: '.$recordData['c_destinations_all_display_venues_csv'][0]."\n".
'Date(s) of Travel: '.$recordData['leave_date_requested'][0].' to '.$recordData['return_date_requested'][0]."\n".
'------------------------------------------------------------'."\n\n".

'------------------------------------------------------------------------------------------------------------------'."\n".
'This is an auto-generated message from the SEDL Information Management System (SIMS)'."\n".
'------------------------------------------------------------------------------------------------------------------';

$headers = 'From: SIMS@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email']."\r\n".'Bcc: sims@sedl.org';

mail($to, $subject, $message, $headers);
######################################################
## END: TRIGGER NOTIFICATION E-MAIL TO ADMIN ##
######################################################

########################################
## START: DELETE REQUEST IF NECESSARY ##
########################################
$delete = new FX($serverIP,$webCompanionPort);
$delete -> SetDBData('SIMS_2.fp7','travel_authorizations');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$delete_row_ID);

$deleteResult = $delete -> FMDelete();

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','DELETE_TRAVEL_REQUEST_STAFF');
$newrecord -> AddDBParam('table','TRAVEL_AUTHORIZATIONS');
//$newrecord -> AddDBParam('object_ID',$recordData['leave_request_ID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$delete_row_ID);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];


######################################
## END: DELETE REQUEST IF NECESSARY ##
######################################

}


##############################################
## START: FIND TRAVEL REQUESTS FOR THIS USER ##
##############################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','travel_authorizations',12);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$_SESSION['staff_ID']);
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('approval_status','descend');
$search -> AddSortParam('travel_auth_ID','descend');
//$search -> AddSortParam('c_event_start_date_menu_sort_display','descend');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['travel_request_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
############################################
## END: FIND TRAVEL REQUESTS FOR THIS USER ##
############################################
//$current_pay_period = date("m").'/'.date("t").'/'.date("Y");

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function preventDelete() { 
	var answer = confirm ("Approved travel requests cannot be deleted.")
	return false;
	
}
</script>

<script language="JavaScript">

function confirmDelete() { 
	var answer2 = confirm ("Are you sure you want to delete this travel request?")
	if (!answer2) {
	return false;
	}
}

function zoomWindow() {
window.resizeTo(1000,screen.height)
}
</script>


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="/staff/sims/travel_prefs.php" title="Update your SIMS leave preferences.">Travel Preferences</a> | <a href="my_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>">My Travel Calendar</a> | <a href="/staff/sims/travel.php?action=new_dot">New Travel Request</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
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

			<?php } ?>
			
			<?php if($_SESSION['travel_expenses_submitted_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your travel expense worksheet has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>

			<?php $_SESSION['travel_expenses_submitted_staff'] = ''; } ?>
			
			<?php if($_SESSION['travel_expenses_submitted_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your travel expense worksheet, please contact <a href="mailto:sims@sedl.org">technical support</a></p></td></tr>

			<?php $_SESSION['travel_expenses_submitted_staff'] = ''; } ?>
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="#ebebeb" bgcolor="#ffffff" width="100%" class="sims">
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body">Date(s) of Travel</td>
						<td class="body">Event Description</td>
						<td class="body">Destination</td>
						<td class="body">Date/Time Submitted</td>
						<td class="body" align="right">Status</td>

						<td class="body" align="right">Delete</td></tr>
						
<?php if($searchResult['foundCount'] > 0){ ?>

						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr style="vertical-align:text-top">
						<td class="body"><?php echo $searchData['travel_auth_ID'][0];?></td>
						<td class="body" nowrap><?php if($searchData['leave_date_requested'][0] != $searchData['return_date_requested'][0]){echo $searchData['leave_date_requested'][0].' - '.$searchData['return_date_requested'][0];} else { echo $searchData['leave_date_requested'][0];}?></td>
						<td class="body"><a href="/staff/sims/travel.php?travel_auth_ID=<?php echo $searchData['travel_auth_ID'][0];?>&action=<?php if($searchData['multi_dest'][0] == 'yes'){ ?>view_multi<?php }else{?>view<?php }?>&app=<?php echo $searchData['approval_status'][0];?>"><?php echo stripslashes($searchData['purpose_of_travel_descr'][0]);?></a></td>
						<td class="body" nowrap><?php if($searchData['multi_dest'][0] == 'yes'){ echo $searchData['event_venue_city1'][0];?>, <?php echo $searchData['event_venue_state1'][0];?>**<?php } else { echo $searchData['event_venue_city'][0];?>, <?php echo $searchData['event_venue_state'][0];}?></td>
						<td class="body" nowrap><?php echo $searchData['creation_timestamp'][0];?></td>

						
						
						
						<?php if($searchData['c_lock_status'][0] =='1'){ ?>
						<td class="body" align="right"><?php if($searchData['approval_status_tr'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status_tr'][0];?></font></td>
						<td class="body" align="right"><img src="/staff/sims/images/padlock.jpg" border="0" title="This travel request is locked."></td>
						<?php }else{ ?>
						<td class="body" align="right"><?php if($searchData['approval_status_tr'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status_tr'][0];?></font></td>
						<td class="body" align="right"><a href="menu_travel.php?delete_key=<?php echo $searchData['c_row_ID_cwp'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/trashcan.jpg" border="0"></a></td>
						<?php } ?>
						</tr>
			
						<?php } ?>

<?php } else { ?>


						<tr>
						<td class="body" colspan="8" height="40" align="center">No records found.</td>
						</tr>


<?php } ?>







						</table>&nbsp;**<span class="tiny">indicates multi-destination travel.</span>

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
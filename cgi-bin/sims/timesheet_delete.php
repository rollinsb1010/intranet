<?php
session_start();

include_once('sims_checksession.php');

include_once('FX/FX.php');
include_once('FX/server_data.php');

$debug = 'off';

$row_ID = $_GET['row_ID'];

###################################################################################################
## BEGIN: CHECK IF THE USER IS AUTHORIZED TO DELETE THIS TIMESHEET -- (PREVENT URL MANIPULATION) ##
###################################################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','timesheets');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('c_row_ID_cwp','=='.$row_ID);

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
$timesheet_ID = $recordData['TimesheetID'][0];

if($recordData['staff_ID'][0] != $_SESSION['staff_ID']){ // IF THE USER IS NOT AUTHORIZED, LOG ACTION, SEND E-MAIL, SHOW MESSAGE, AND RETURN TO MENU

	// LOG ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','TIMESHEET_DELETE_UNAUTHORIZED_ATTEMPT');
	$newrecord -> AddDBParam('table','TIMESHEETS');
	$newrecord -> AddDBParam('object_ID',$recordData['TimesheetID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$row_ID);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

	// SEND E-MAIL NOTIFICATION OF EVENT	
	$to = 'eric.waters@sedl.org';
	$subject = 'SIMS: ILLEGAL ACTION LOGGED - UNAUTHORIZED TIMESHEET DELETE ATTEMPT';
	$message = 
	'An illegal timesheet action has been logged in the SIMS-2 audit_table.'."\n\n".
	'----------'."\n\n".
	'Audit Details:'."\n\n".
	'user: '.$_SESSION['user_ID']."\n\n".
	'action: TIMESHEET_DELETE_UNAUTHORIZED_ATTEMPT'."\n\n".
	'table: TIMESHEETS'."\n\n".
	'timesheet_ID: '.$recordData['TimesheetID'][0]."\n\n".
	'c_row_ID_cwp: '.$row_ID."\n\n".
	'IP address: '.$ip."\n\n".
	'----------'."\n\n".
	'This is an auto-generated message from SIMS-2';
	$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: SIMS-2@sedl.org';
	mail($to, $subject, $message, $headers);
	
	$_SESSION['illegal_action'] = '1'; // SHOW MESSAGE TRIGGER
	header('Location: http://www.sedl.org/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd'); // RETURN TO MENU
	exit;
	
} else { 



	// LOG ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action','TIMESHEET_DELETE');
	$newrecord -> AddDBParam('table','TIMESHEETS');
	$newrecord -> AddDBParam('object_ID',$recordData['TimesheetID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$row_ID);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

}
###################################################################
## END: CHECK IF THE USER IS AUTHORIZED TO DELETE THIS TIMESHEET ##
###################################################################

#########################################
## START: DELETE THE TIMESHEET ROW IN FMP
#########################################
$delete = new FX($serverIP,$webCompanionPort);
$delete -> SetDBData('SIMS_2.fp7','timesheets');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$row_ID);

$deleteResult = $delete -> FMDelete();

//echo '<br>deleteResult ErrorCode: '.$deleteResult['errorCode'];
$_SESSION['timesheet_delete'] = '1';
#########################################
## END: DELETE THE TIME_HRS ROW IN FMP
#########################################

	header('Location: http://www.sedl.org/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd'); // RETURN TO MENU
	exit;


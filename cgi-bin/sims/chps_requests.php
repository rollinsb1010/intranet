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
$sortfield = $_GET['sortfield'];
$sortorder = $_GET['sortorder'];
$displaynum = $_GET['displaynum'];
//$position_ID = $_GET['pos_id'];


$view = $_GET['v'];

if($action == ''){ 
$action = 'view';
}

$request_ID = $_GET['id'];
$reviewer_submit = $_GET['reviewer_submit'];
$logx = $_GET['logx'];
$logid = $_GET['logid'];
//$status = $_GET['status'];


$location = 'Location: http://www.sedl.org/staff/sims/menu_chps_requests.php?pos_id='.$request_ID.'&sortfield='.$sortfield.'&sortorder='.$sortorder.'&displaynum='.$displaynum.'#'.$recordData['request_ID'][0];
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
if($action == 'view'){ //IF THE USER IS VIEWING THIS REQUEST

################################
## START: GET FMP VALUE-LISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('CC_dms.fp7','chps_action_log');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
//echo  '<p>errorCode: '.$v1Result['errorCode'];
//echo  '<p>foundCount: '.$v1Result['foundCount'];
//print_r($v1Result);
##############################
## END: GET FMP VALUE-LISTS ##
##############################

if(($logx == '1')&&($logid !== '')) { // IF THE REVIEWER DELETED A LOG ITEM
#################################################
## START: DELETE THE LOG ITEM ##
#################################################
$delete = new FX($serverIP,$webCompanionPort);
$delete -> SetDBData('CC_dms.fp7','chps_action_log');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$logid);

$deleteResult = $delete -> FMDelete();
#################################################
## END: DELETE THE LOG ITEM ##
#################################################
}

if($reviewer_submit == '1') { // IF THE REVIEWER SUBMITTED A RESPONSE
#################################################
## START: ADD THE REVIEWER RESPONSE ##
#################################################

$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('CC_dms.fp7','chps_action_log'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information

$newrecord -> AddDBParam('request_ID',$request_ID);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action',$_GET['mgr_action']);

if($_GET['action_target'] !== ''){

	for($i=0 ; $i<count($_GET['action_target']) ; $i++) {
	$action_target .= $_GET['action_target'][$i]."\r"; 
	}

$newrecord -> AddDBParam('action_target',$action_target);
}else{
$newrecord -> AddDBParam('action_target','CHPS');
}

$newrecord -> AddDBParam('comments',$_GET['comments']);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

$newrecordData = current($newrecordResult['data']);

$reviewer_action_ID = $newrecordData['record_ID'][0];
//$doc_type = $newrecordData['record_type'][0];
$send_to = $newrecordData['c_target_email'][0];
//echo '<p>$send_to: '.$send_to;
//$send_to_IDs = $newrecordData['c_target_IDs'][0];
//$current_id = $newrecordData['unsolicited_resumes::c_row_ID'][0];
//$created_by = $newrecordData['projects::created_by'][0];
$request_type = $newrecordData['chps_session_requests::request_type'][0];

//echo '$created_by: '.$created_by;
//echo '$send_to: '.$send_to;
//exit;
//if($doc_type == '1'){
//$doc_name = 'An unsolicited resume';
//$position = 'Unsolicited resume';
//}else{
//$doc_name = 'A prospective job applicant';
//$position = $_GET['position'];
//}

if($newrecordResult['errorCode'] == '0'){ // USER RESPONSE WAS SUCCESSFULLY SAVED TO THE ACTION LOG
$_SESSION['action_logged'] = '1';


	// LOG THIS ACTION
	$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS
	
	$newrecord = new FX($serverIP,$webCompanionPort);
	$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
	$newrecord -> SetDBPassword($webPW,$webUN);
	$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
	$newrecord -> AddDBParam('action',$_GET['mgr_action']);
	$newrecord -> AddDBParam('table','chps_action_log');
	$newrecord -> AddDBParam('object_ID',$newrecordData['record_ID'][0]);
	$newrecord -> AddDBParam('affected_row_ID',$newrecordData['record_ID'][0]);
	$newrecord -> AddDBParam('ip_address',$ip);
	$newrecordResult = $newrecord -> FMNew();
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];



if($send_to !== ''){ // SEND E-MAIL NOTIFICATION TO SELECTED MGR/REVIEWER
			//$to = $_GET['action_target'].'@sedl.org';
		

	####################################################
	## SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER/ ##
	####################################################
	$to = $send_to;
	$subject = 'CHPS: REQUEST FORWARDED FOR YOUR REVIEW';
	$message = 
	'CHPS Manager,'."\n\n".
	
	'An online session request was received by the CHPS Request a Quote form and has been forwarded for your review.'."\n\n".
	
	'------------------------------------------------------------'."\n".
	' REQUEST A QUOTE DETAILS'."\n".
	'------------------------------------------------------------'."\n".
	'Submitted by: '.$_SESSION['user_ID']."\n".
	'Action: '.$newrecordData['action'][0]."\n".
	'Comments: '.$newrecordData['comments'][0]."\n\n".
	
	'Requestor Name: '.$newrecordData['chps_session_requests::first_name'][0].' '.$newrecordData['chps_session_requests::last_name'][0]."\n".
	'Organization: '.$newrecordData['chps_session_requests::organization'][0]."\n".
	'Title: '.$newrecordData['chps_session_requests::title'][0]."\n".
	'Location: '.$newrecordData['chps_session_requests::loc_city'][0].', '.$newrecordData['chps_session_requests::loc_state'][0]."\n".
	'Action: '.$newrecordData['action'][0]."\n".
	'Comments: '.$newrecordData['comments'][0]."\n".
	'------------------------------------------------------------'."\n\n".
	
	'To review and process this request, click here:'."\n".
	'http://www.sedl.org/staff/sims/chps_requests.php?id='.$newrecordData['request_ID'][0].'&sortfield=status&sortorder=descend&displaynum=100&pos_id=all'."\n\n".
	
	'---------------------------------------------------------------------------------------------------------------------------------'."\n".
	'This is an auto-generated message from SIMS'."\n".
	'---------------------------------------------------------------------------------------------------------------------------------';
	
	$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
	
	mail($to, $subject, $message, $headers);	
	####################################################
	## /SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
	####################################################
		
}else{ // SEND E-MAIL NOTIFICATION TO SUBMITTER (NO REVIEWER SPECIFIED)

	####################################################
	## SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER/ ##
	####################################################
	$to = $_SESSION['user_ID'].'@sedl.org';
	$subject = 'CHPS: REQUEST FORWARDED FOR YOUR REVIEW';
	$message = 
	'CHPS Manager,'."\n\n".
	
	'An online session request was received by the CHPS Request a Quote form and has been forwarded for your review (no other reviewer(s) specified).'."\n\n".
	
	'------------------------------------------------------------'."\n".
	' REQUEST A QUOTE DETAILS'."\n".
	'------------------------------------------------------------'."\n".
	'Submitted by: '.$_SESSION['user_ID']."\n".
	'Action: '.$newrecordData['action'][0]."\n".
	'Comments: '.$newrecordData['comments'][0]."\n\n".
	
	'Requestor Name: '.$newrecordData['chps_session_requests::first_name'][0].' '.$newrecordData['chps_session_requests::last_name'][0]."\n".
	'Organization: '.$newrecordData['chps_session_requests::organization'][0]."\n".
	'Title: '.$newrecordData['chps_session_requests::title'][0]."\n".
	'Location: '.$newrecordData['chps_session_requests::loc_city'][0].', '.$newrecordData['chps_session_requests::loc_state'][0]."\n".
	'------------------------------------------------------------'."\n\n".
	
	'To review and process this request, click here:'."\n".
	'http://www.sedl.org/staff/sims/chps_requests.php?id='.$newrecordData['request_ID'][0].'&sortfield=status&sortorder=descend&displaynum=100&pos_id=all'."\n\n".
	
	'---------------------------------------------------------------------------------------------------------------------------------'."\n".
	'This is an auto-generated message from SIMS'."\n".
	'---------------------------------------------------------------------------------------------------------------------------------';
	
	$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org'."\r\n".'Bcc: sims@sedl.org';
	
	mail($to, $subject, $message, $headers);	
	####################################################
	## /SEND E-MAIL NOTIFICATION TO SELECTED REVIEWER ##
	####################################################
}

}else{ // THERE WAS A PROBLEM SAVING USER RESPONSE TO THE ACTION LOG
$_SESSION['action_logged'] = '2'; 
}
###############################################
## END: ADD THE REVIEWER RESPONSE ##
###############################################
//echo '$_GET[action_target]: '.$_GET['action_target'];
//exit;
header($location);
exit;
}

#################################################################
## START: FIND THIS CHPS REQUEST ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('CC_dms.fp7','chps_session_requests');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('request_ID','=='.$request_ID);
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam('leave_hrs_date','ascend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
$row_ID = $recordData['c_row_ID'][0];
$created_by = $recordData['created_by'][0];

$reject1 = 'at least '.$recordData['doc_position_app_pool_count'][0].' other applicants indicate more extensive relevant education, skills, and/or experience, and/or present stronger writing sample';
$reject2 = 'at least '.$recordData['doc_position_app_pool_count2'][0].' other applicants indicate more extensive relevant education, skills, and/or experience, and/or present stronger writing sample';


//if((($recordData['PR_pre_approval_required_IT'][0] == '1')&&($recordData['sign_status_IT'][0] != '1'))||(($recordData['PR_pre_approval_required_IRC'][0] == '1')&&($recordData['sign_status_IRC'][0] != '1'))){
//$preappRequired = '1';
//}else{
//$preappRequired = '0';
//}
###############################################################
## END: FIND THIS CHPS REQUEST ##
###############################################################

if($recordData['c_attachment_count'][0] > 0){ // THE SELECTED APP HAS FILE ATTACHMENTS
#################################################################
## START: FIND ATTACHMENTS RELATED TO THIS APPLICANT ##
#################################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('CC_dms.fp7','files_attachments');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('request_ID','=='.$request_ID);
//$search3 -> AddDBParam('-lop','or');

//$search3 -> AddSortParam('leave_hrs_date','ascend');


$searchResult3 = $search3 -> FMFind();

//echo '<p>$searchResult3[errorCode]: '.$searchResult3['errorCode'];
//echo '<p>$searchResult3[foundCount]: '.$searchResult3['foundCount'];
//print_r ($searchResult3);
$recordData3 = current($searchResult3['data']);
###############################################################
## END: FIND ATTACHMENTS RELATED TO THIS APPLICANT ##
###############################################################
}


if($recordData['c_action_log_count'][0] > 0){ // THE SELECTED REQUEST HAS USER LOG ENTRIES
#################################################################
## START: FIND USER LOG ENTRIES RELATED TO THIS REQUEST ##
#################################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('CC_dms.fp7','chps_action_log');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('request_ID','=='.$request_ID);
//$search4 -> AddDBParam('-lop','or');

$search4 -> AddSortParam('creation_timestamp','ascend');


$searchResult4 = $search4 -> FMFind();

//echo '<p>$searchResult4[errorCode]: '.$searchResult4['errorCode'];
//echo '<p>$searchResult4[foundCount]: '.$searchResult4['foundCount'];
//print_r ($searchResult4);
$recordData4 = current($searchResult4['data']);
###############################################################
## END: FIND USER LOG ENTRIES RELATED TO THIS REQUEST ##
###############################################################
}

$user = $_SESSION['user_ID'];
//echo '<span style="color:#999999">User: '.$user.'</span>';
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>CHPS: Custom Session Requests</title>
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

<script language="JavaScript">




function checkFields() { 
	

		if (document.form2.mgr_action.value ==""){
			alert("Please select the reviewer action.");
			document.form2.mgr_action.focus();
			return false;	}

}


function confirmDelete() { 
	var answer2 = confirm ("Delete this process log item now?")
	if (!answer2) {
	return false;
	}
}



function UpdateSelect()
{


select_value4 = "";
select_value4 = document.form2.mgr_action.value;
var id4 = 'reject_reason1';
var obj4 = '';
obj4 = (document.getElementById) ? document.getElementById(id4) : ((document.all) ? document.all[id4] : ((document.layers) ? document.layers[id4] : false));




	if(select_value4 == ""){
	  obj4.style.display = 'none';
	  //alert("onBodyLoad.");
	
	} else if(select_value4 == "No to requestor"){
	  // alert("You chose Journal article.");
	  // return false;
	  obj4.style.display = 'block';
	}
	else
	{
	  obj4.style.display = 'none';
	}


}



</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="UpdateSelect();">

<table cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="900px">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>CHPS: Custom Session Requests</h1><hr /></td></tr>
			
			
			<tr><td colspan="2">
				<p class="alert_small"><b>Reviewer: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - To process this request, submit your action and comments below. | <a href="/staff/sims/menu_chps_requests.php?action=<?php echo $view;?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $position_ID;?>#<?php echo $recordData['request_ID'][0];?>">Close Document</a></p>
			</td></tr>
			
			
			
			<tr><td colspan="2">
			
						<table cellpadding="10" cellspacing="0" border="0" bordercolor="ebebeb" bgcolor="ffffff" width="100%">
						<tr bgcolor="#a2c7ca"><td class="body"><strong>Request Type: <?php echo $recordData['request_type'][0];?></strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Status: <strong><?php if($recordData['status'][0] == 'Pending'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['status'][0]).'</span>';?></strong></span></td></tr>
						<tr><td class="body" nowrap><strong>Requestor: <?php echo $recordData['first_name'][0].' '.$recordData['last_name'][0];?></strong></td><td align="right">ID: <?php echo $recordData['request_ID'][0];?></td></tr>

						<tr><td colspan="2" class="body">
						

							<table cellpadding="7" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
							
								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>REQUEST DETAILS</strong></div>
								
									<table style="border:0px dotted #000000;width:100%;margin-top:6px">
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Name</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['first_name'][0].' '.$recordData['last_name'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Request Date</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['creation_timestamp'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>Phone</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['phone_work'][0];?><?php if($recordData['phone_work_ext'][0] !== ''){echo ' (Ext: '.$recordData['phone_work_ext'][0].')';}?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px" nowrap>E-mail</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><a href="mailto:<?php echo $recordData['email'][0];?>"><?php echo $recordData['email'][0];?></a></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Organization/Title</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['organization'][0].'<br>'.$recordData['title'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Session(s) Client is interested in</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['c_sessions_requested_html'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Session (Other Type)</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['session_other'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Outcomes</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['outcomes'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Location for Training</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['location'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">City/state If delivered at user's location</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['loc_city'][0].', '.$recordData['loc_state'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Materials Distribution Preference</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['meterials_distribution'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px"># Participants</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['num_participants'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Budget Deadline</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['budget_deadline'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Delivery Deadline</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['delivery_deadline'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Cost Breakout</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['cost_breakout'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Client's Estimated Budget Range</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['budgetrange'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Days of Training</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['days_of_training'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Date preferred for Training</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['date_training'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Date preferred for Followup</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['date_followup'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Type of Followup Preferred</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['type_followup'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Type of Evaluation Preferred</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:text-top"><?php echo $recordData['type_evaluation'][0];?></td></tr>
									</table>

								
								</td>




								</tr>



								<tr><td  colspan="2" style="vertical-align:text-top" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>ACTION LOG</strong></div>



								
									<table width="100%" style="margin-top:6px;width:100%">

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf"><strong>DATE</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>USER</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>ACTION</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>SENT TO</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap width="100%"><strong>COMMENTS</strong></td></tr>

									<?php if($searchResult4['foundCount'] > 0){ // SHOW APP ACTION LOG ?>
									<?php foreach($searchResult4['data'] as $key => $searchData4) {  ?>

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['creation_timestamp'][0];?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['user'][0];?><?php if($_SESSION['user_ID'] == $searchData4['user'][0]){?><br><a href="chps_requests.php?id=<?php echo $recordData['request_ID'][0];?>&logx=1&logid=<?php echo $searchData4['c_row_ID'][0];?>" onclick="javascript:return confirm('Are you sure you want to delete this log entry?')">Delete</a><?php }?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['action'][0];?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px"><?php echo $searchData4['action_target'][0];?></td><td class="tiny" width="100%" style="vertical-align:text-top;border:1px dotted #000000;padding:3px"><?php if($searchData4['comments'][0] != ''){?><?php echo $searchData4['comments'][0];?><?php }else{ echo 'N/A';}?></td></tr>
									
									<?php } ?>
									<?php }else{ ?>

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" colspan="5">N/A</td></tr>

									<?php } ?>

										<tr><td style="background-color:#ebebeb;vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap colspan="5">
										
											<form id="form2" name="form2" onsubmit="return checkFields()">
											<input type="hidden" name="reviewer_submit" value="1">
											<input type="hidden" name="id" value="<?php echo $recordData['request_ID'][0];?>">
											<input type="hidden" name="requestor_name" value="<?php echo $recordData['cpl_cs_name_first'][0].' '.$recordData['cpl_cs_name_last'][0];?>">
											<input type="hidden" name="org" value="<?php echo $recordData['cpl_cs_org'][0];?>">
											<input type="hidden" name="title" value="<?php echo $recordData['cpl_cs_title'][0];?>">
											<input type="hidden" name="location" value="<?php echo $recordData['cpl_cs_location'][0];?>">
											<input type="hidden" name="city_state" value="<?php echo $recordData['cpl_cs_loc_city'][0].', '.$recordData['cpl_cs_loc_state'][0];?>">
											<input type="hidden" name="sortfield" value="<?php echo $sortfield;?>">
											<input type="hidden" name="sortorder" value="<?php echo $sortorder;?>">
											<input type="hidden" name="displaynum" value="<?php echo $displaynum;?>">
											

											<table  style="border:1px dotted #000000;background-color:#b7e4fc;padding:6px">
											<tr><td id="title" colspan="2" style="border:0px;padding:6px">Reviewer Action</td></tr>
											<tr><td style="border:0px;padding:6px;vertical-align:top">
											
											
											
											<label class="col1"><strong>Reviewer:</strong>&nbsp;&nbsp;</label></td><td class="col2" style="border:1px dotted;background-color:#ffffff;padding:6px;vertical-align:top"><?php echo $_SESSION['user_ID'];?></td></tr>
											<tr><td style="border:0px;padding:6px;vertical-align:top"><label class="col1"><strong>Action:</strong>&nbsp;&nbsp;</label></td><td style="border:1px dotted;background-color:#ffffff;padding:6px;vertical-align:top">




											<select name="mgr_action" onChange="UpdateSelect();">
											<option value=""></option>
											<option value="">------------------------------</option>
											<option value="">&nbsp;&nbsp;MANAGER ACTIONS</option>
											<option value="">------------------------------</option>
											<option value="No to requestor">&nbsp;(1) No to requestor</option>
											<option value="Send requestor quote/proposal">&nbsp;(2) Send requestor quote/proposal</option>
											<option value="">------------------------------</option>
											<option value="">&nbsp;&nbsp;REVIEWER ACTIONS</option>
											<option value="">------------------------------</option>
											<option value="Recommend No to requestor">&nbsp;(1) Recommend No to requestor (see comments)</option>
											<option value="Sent proposal/awaiting response">&nbsp;(2) Sent proposal/awaiting response (see comments)</option>
											<option value="Proposal accepted">&nbsp;(3) Proposal accepted (see comments)</option>
											<option value="Proposal rejected">&nbsp;(4) Proposal rejected (see comments)</option>
											<option value="">------------------------------</option>
											<option value="">&nbsp;&nbsp;MORE OPTIONS</option>
											<option value="">------------------------------</option>
											<option value="Other (see comments)">&nbsp;(1) Other (see comments)</option>
											</select><br>&nbsp;<br>

												<div id="specify_target" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
												Select reviewer(s)/manager(s) to notify: <br>&nbsp;<br>
												
							
													
													<?php foreach($v1Result['valueLists']['chps_session_request_reviewers'] as $key => $value) { ?>
													
													<input type="checkbox" name="action_target[]" value="<?php echo $value;?>"> <?php echo $value; ?></input><br>
													
													<?php } ?>
													<hr>
													<span class="tiny">Contact <a href="mailto:sims@sedl.org">sims@sedl.org</a> to add a reviewer to this list.</span>
												</div>

												<div id="reject_reason1" style="padding:8px;border:1px dotted #000000;background-color:#b7e4fc">
												Reason for Rejection: 
												
							
													<select name="reject_reason"  onChange="UpdateSelect();">
													<option value="">&nbsp;</option>
													<option value="limited response time">&nbsp;limited response time</option>
													<option value="limited staff expertise">&nbsp;limited staff expertise</option>
													<option value="under funded">&nbsp;under funded</option>
													</select>
															
												</div>


											</td></tr>
											<tr><td style="border:0px;padding:6px;vertical-align:top"><label class="col1comment"><strong>Comments:</strong>&nbsp;&nbsp;</label></td><td style="border:1px dotted;background-color:#ffffff;padding:6px;vertical-align:top"><textarea cols="40" class="textarea" rows="4" name="comments" id="comment" tabindex="4" ></textarea></td></tr>
											<tr><td colspan="2" style="text-align:right;border:0px;padding:6px"><input type="submit" value="Submit" />
											</td></tr>
											</table>


										</form>





									
										
										
										</td></tr>

									</table>
								</td>
								</tr>
								
								
		
		
		
		
													
		
		
									</table>
								
								
								</td></tr>
								
								
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
} else { ?>
Error
<?php } ?>

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
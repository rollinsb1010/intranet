<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('fxphp-master/FX.php');
include_once('fxphp-master/server_data.php');
error_reporting(0);


$action = $_REQUEST['action'];

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

if($action == 'new'){ 

################################
## START: GET FMP VALUE-LISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort,$dataSourceType);
$v1 -> SetDBData('SEDL_HelpDesk.fmp12','help_tickets_by_selection');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
##############################
## END: GET FMP VALUE-LISTS ##
##############################

?>

<html>
<head>
<title>SIMS: My HelpDesk Tickets</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function checkFields() { 

	// Subject
		if (document.form2.issue_subject.value =="") {
			alert("Please enter the subject of this help ticket.");
			document.form2.issue_subject.focus();
			return false;	}

	// Description
		if (document.form2.issue_description.value =="") {
			alert("Please enter the description of this help ticket.");
			document.form2.issue_description.focus();
			return false;	}

}	
</script>


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#333333"><img src="/staff/sims/images/helpdesk_header.jpg"></td></tr>
		
			<tr><td class="body" nowrap><strong>SIMS User:</strong> <?php echo $_SESSION['user_ID'];?></td><td align="right"><strong>New Help Ticket</strong> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<tr style="color:#ffffff;background-color:#747373"><td colspan="2"><strong>NEW HELP TICKET</strong></td></tr>
			
			
			<tr><td colspan="2">Enter the details of your request below. Please enter as much information as possible so the HelpDesk staff can better troubleshoot your issue. <p>
			<form method="get" name="form2" onsubmit="return checkFields()">
			<input type="hidden" name="action" value="new_submit">
			<input type="hidden" name="requestor_sims_ID" value="<?php echo $_SESSION['user_ID'];?>">
			<input type="hidden" name="requestor_name" value="<?php echo $_SESSION['staff_name'];?>">
			<input type="hidden" name="requestor_email" value="<?php echo $_SESSION['staff_email'];?>">
			<input type="hidden" name="requestor_department" value="<?php echo $_SESSION['workgroup'];?>">
						<table width="100%" style="border:1px #a2a2a2 solid">

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Staff Member:</td>
						<td style="padding:6px;width:100%"><?php echo $_SESSION['user_ID'];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>SEDL Unit:</td>
						<td style="padding:6px;width:100%"><?php echo $_SESSION['workgroup'];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc;vertical-align:text-top" nowrap>Subject*:</td>
						<td style="padding:6px;width:100%;border-top:1px dotted #cccccc">
						<em>Enter the subject for this help ticket (e.g. - Can't get document to print...etc.)</em><br>
						<input type="text" name="issue_subject" size="75">
						
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc;vertical-align:text-top" nowrap>Description*:</td>
						<td style="padding:6px;width:100%;border-bottom:1px dotted #cccccc">
						<em>Enter the specific details relating to your technical issue. Include as much detail as possible.</em><br>
						<textarea name="issue_description" rows="6" cols="75"></textarea>
						
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Location:</td>
						<td style="padding:6px;width:100%">
						
						<select name="requestor_location">
						<option value="" selected>--Select location of this issue--
						
						<?php foreach($v1Result['valueLists']['issue_location'] as $key => $value) { ?>
						<option value="<?php echo $value;?>"> <?php echo $value; ?>
						<?php } ?>
						</select>
						or enter other location <input type="text" size="25" name="requestor_location_other">
						
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Priority:</td>
						<td style="padding:6px;width:100%">
						
						<?php foreach($v1Result['valueLists']['priority'] as $key => $value) { ?>
						<input type="radio" name="priority" value="<?php echo $value;?>" <?php if($value == 'Medium'){?>checked<?php }?>><?php echo $value;?>&nbsp;
						<?php } ?>
						</select>
						
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Recurring Issue:</td>
						<td style="padding:6px;width:100%">
						
						<?php foreach($v1Result['valueLists']['Yes_No'] as $key => $value) { ?>
						<input type="radio" name="issue_recurring" value="<?php echo $value;?>"><?php echo $value;?>&nbsp;
						<?php } ?>
						</select>
						
						</td>
						</tr>						

						</table>
						<input type="button" onClick="history.back()" value="Cancel"><input type="submit" name="submit" value="Submit Ticket">
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

}elseif($action == 'show1'){ 

if($_REQUEST['mod'] == 'save_edit'){

// TRANSFORM CHECKBOX ARRAYS FOR SAVING TO FMP
$issue_type = '';
for($i=0 ; $i<count($_REQUEST['issue_type']) ; $i++) {
$issue_type .= $_REQUEST['issue_type'][$i]."\r"; 
}

$hardware_type = '';
for($i=0 ; $i<count($_REQUEST['hardware_type']) ; $i++) {
$hardware_type .= $_REQUEST['hardware_type'][$i]."\r"; 
}

##############################################
## START: UPDATE EDITED TICKET ##
##############################################
$update = new FX($serverIP,$webCompanionPort,$dataSourceType);
$update -> SetDBData('SEDL_HelpDesk.fmp12','help_tickets_by_selection');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_REQUEST['row']);

if($_REQUEST['requestor_location_other'] !== ''){
$update -> AddDBParam('requestor_location',$_REQUEST['requestor_location_other']);
}else{
$update -> AddDBParam('requestor_location',$_REQUEST['requestor_location']);
}
$update -> AddDBParam('priority',$_REQUEST['priority']);
$update -> AddDBParam('issue_recurring',$_REQUEST['issue_recurring']);
$update -> AddDBParam('issue_subject',$_REQUEST['issue_subject']);
$update -> AddDBParam('issue_description',$_REQUEST['issue_description']);

$updateResult = $update -> FMEdit();

//echo '<p>$updateResult[errorCode]: '.$updateResult['errorCode'];
//echo '<p>$updateResult[foundCount]: '.$updateResult['foundCount'];
$updateData= current($updateResult['data']);
############################################
## END: UPDATE EDITED TICKET ##
############################################


	if($updateResult['errorCode'] == 0){
	$_SESSION['ticket_updated'] = '1';
	
	####################################################
	## START: TRIGGER NOTIFICATION E-MAIL TO HELPDESK ##
	####################################################
	$to = 'helpdesk@sedl.org';
	if($_REQUEST['priority'] == 'Urgent'){
	$subject = 'Revised URGENT Helpdesk Ticket Received: '.$_REQUEST['issue_subject'];
	}else{
	$subject = 'Revised Helpdesk Ticket Received: '.$_REQUEST['issue_subject'];
	}
	$message = 
	'SEDL IT Staff-'."\n\n".
	
	'A revised online help ticket has been submitted to SEDL Helpdesk by '.$_REQUEST['requestor_name'].'.'."\n\n".
	
	'------------------------------------------------------------'."\n".
	' TICKET DETAILS'."\n".
	'------------------------------------------------------------'."\n".
	'Requestor: '.$_REQUEST['requestor_name']."\n".
	'Unit: '.$_REQUEST['requestor_department']."\n".
	'Location: '.$updateData['requestor_location'][0]."\n".
	'Priority: '.$_REQUEST['priority']."\n".
	'Subject: '.$_REQUEST['issue_subject']."\n".
	'------------------------------------------------------------'."\n\n".
	
	'To view this ticket, click here: '."\n".
	'fmp://198.214.140.246/SEDL_HelpDesk.fmp12?script=show_assigned_ticket&$id='.$updateData['ticket_ID'][0]."\n\n".
	
	'---------------------------------------------------------------------------------------------------------------------------------'."\n".
	'This is an auto-generated message from the SEDL HelpDesk Ticket system.'."\n".
	'---------------------------------------------------------------------------------------------------------------------------------';
	
	$headers = 'From: helpdesk@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email'];
	
	mail($to, $subject, $message, $headers);
	####################################################
	## END: TRIGGER NOTIFICATION E-MAIL TO HELPDESK ##
	####################################################
	
	##############################################
	## START: RECORD EDIT TO TICKET THREAD ##
	##############################################
	$newrecord = new FX($serverIP,$webCompanionPort,$dataSourceType);
	$newrecord -> SetDBData('SEDL_HelpDesk.fmp12','process_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	
	$newrecord -> AddDBParam('ticket_ID',$updateData['ticket_ID'][0]);
	$newrecord -> AddDBParam('action','Client');
	$newrecord -> AddDBParam('user',$updateData['requestor_sims_ID'][0]);
	$newrecord -> AddDBParam('comments','Requestor modified and re-submitted the ticket.');
	
	$newrecordResult = $newrecord -> FMNew();
	
	//echo '<p>$newrecordResult[errorCode]: '.$newrecordResult['errorCode'];
	//echo '<p>$newrecordResult[foundCount]: '.$newrecordResult['foundCount'];
	$recordData = current($newrecordResult['data']);
	############################################
	## END: RECORD EDIT TO TICKET THREAD ##
	############################################
	
	}

}

if($_REQUEST['mod'] == 're_open'){


##############################################
## START: UPDATE TICKET STATUS ##
##############################################
$update = new FX($serverIP,$webCompanionPort,$dataSourceType);
$update -> SetDBData('SEDL_HelpDesk.fmp12','help_tickets_by_selection');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$_REQUEST['row']);

$update -> AddDBParam('status','Open');
$update -> AddDBParam('issue_resolved','');

$updateResult = $update -> FMEdit();

//echo '<p>$updateResult[errorCode]: '.$updateResult['errorCode'];
//echo '<p>$updateResult[foundCount]: '.$updateResult['foundCount'];
$updateData= current($updateResult['data']);
############################################
## END: UPDATE TICKET STATUS ##
############################################


	if($updateResult['errorCode'] == 0){
	$_SESSION['ticket_reopened'] = '1';
	
	####################################################
	## START: TRIGGER NOTIFICATION E-MAIL TO HELPDESK ##
	####################################################
	$to = 'helpdesk@sedl.org';
	$subject = $updateData['requestor_name'][0].' has re-opened help ticket #'.$updateData['ticket_ID'][0];
	$message = 
	'SEDL IT Staff-'."\n\n".
	
	'A previously closed help ticket has been re-opened by staff member '.$updateData['requestor_name'][0].'.'."\n\n".
	
	'------------------------------------------------------------'."\n".
	' TICKET DETAILS'."\n".
	'------------------------------------------------------------'."\n".
	'Requestor: '.$updateData['requestor_name'][0]."\n".
	'Unit: '.$updateData['requestor_department'][0]."\n".
	'Location: '.$updateData['requestor_location'][0]."\n".
	'Priority: High'."\n".
	'Subject: '.$updateData['issue_subject'][0]."\n".
	'Reason for re-opening ticket: '.$_REQUEST['reopen_comments']."\n".
	'------------------------------------------------------------'."\n\n".
	
	'To view this ticket, click here: '."\n".
	'fmp://198.214.140.246/SEDL_HelpDesk.fmp12?script=show_assigned_ticket&$id='.$updateData['ticket_ID'][0]."\n\n".
	
	'---------------------------------------------------------------------------------------------------------------------------------'."\n".
	'This is an auto-generated message from the SEDL HelpDesk Ticket system.'."\n".
	'---------------------------------------------------------------------------------------------------------------------------------';
	
	$headers = 'From: helpdesk@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email'];
	
	mail($to, $subject, $message, $headers);
	####################################################
	## END: TRIGGER NOTIFICATION E-MAIL TO HELPDESK ##
	####################################################
	
	##############################################
	## START: RECORD EDIT TO TICKET THREAD ##
	##############################################
	$newrecord = new FX($serverIP,$webCompanionPort,$dataSourceType);
	$newrecord -> SetDBData('SEDL_HelpDesk.fmp12','process_log');
	$newrecord -> SetDBPassword($webPW,$webUN);
	
	$newrecord -> AddDBParam('ticket_ID',$updateData['ticket_ID'][0]);
	$newrecord -> AddDBParam('action','Client');
	$newrecord -> AddDBParam('user',$updateData['requestor_sims_ID'][0]);
	$newrecord -> AddDBParam('comments','Ticket re-opened by staff member. | '.$_REQUEST['reopen_comments']);
	
	$newrecordResult = $newrecord -> FMNew();
	
	//echo '<p>$newrecordResult[errorCode]: '.$newrecordResult['errorCode'];
	//echo '<p>$newrecordResult[foundCount]: '.$newrecordResult['foundCount'];
	$recordData = current($newrecordResult['data']);
	############################################
	## END: RECORD EDIT TO TICKET THREAD ##
	############################################
	
	}

}

##############################################
## START: FIND SELECTED TICKET ##
##############################################
$search = new FX($serverIP,$webCompanionPort,$dataSourceType);
$search -> SetDBData('SEDL_HelpDesk.fmp12','help_tickets_by_selection');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('ticket_ID',$_REQUEST['id']);
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam('creation_timestamp','descend');
//$search -> AddSortParam('travel_auth_ID','descend');
//$search -> AddSortParam('c_event_start_date_menu_sort_display','descend');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['travel_request_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
############################################
## END: FIND SELECTED TICKET ##
############################################

##############################################
## START: FIND RELATED ATTACHMENTS ##
##############################################
$search2 = new FX($serverIP,$webCompanionPort,$dataSourceType);
$search2 -> SetDBData('SEDL_HelpDesk.fmp12','attachments');
$search2 -> SetDBPassword($webPW,$webUN);
//$search2 -> FMSkipRecords($skipsize);
$search2 -> AddDBParam('ticket_ID',$_REQUEST['id']);

//$search2 -> AddSortParam('creation_timestamp','descend');

$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
############################################
## END: FIND RELATED ATTACHMENTS ##
############################################

##############################################
## START: FIND TICKET THREAD ENTRIES ##
##############################################
$search3 = new FX($serverIP,$webCompanionPort,$dataSourceType);
$search3 -> SetDBData('SEDL_HelpDesk.fmp12','process_log');
$search3 -> SetDBPassword($webPW,$webUN);
//$search3 -> FMSkipRecords($skipsize);
$search3 -> AddDBParam('ticket_ID',$_REQUEST['id']);
$search3 -> AddDBParam('staff_view','yes');

//$search3 -> AddSortParam('creation_timestamp','descend');

$searchResult3 = $search3 -> FMFind();

//echo '<p>$searchResult3[errorCode]: '.$searchResult3['errorCode'];
//echo '<p>$searchResult3[foundCount]: '.$searchResult3['foundCount'];
############################################
## END: FIND TICKET THREAD ENTRIES ##
############################################
?>

<html>
<head>
<title>SIMS: My HelpDesk Tickets</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function toggle1() {
	var ele = document.getElementById("toggleText");
	var text = document.getElementById("displayText");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "Re-open Ticket";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "Cancel";
	}
} 

function confirmReopen() { 
	var answer = confirm ("Re-open this help ticket?")
	if (!answer) {
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
			
			<tr><td colspan="2" bgcolor="#333333"><img src="/staff/sims/images/helpdesk_header.jpg"></td></tr>
		
			<tr><td class="body" nowrap><strong>SIMS User:</strong> <?php echo $_SESSION['user_ID'];?></td><td align="right"><a href="menu_help_tickets.php">My Help Tickets</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<tr style="color:#ffffff;background-color:#747373"><td colspan="2"><strong>TICKET #<?php echo $recordData['ticket_ID'][0];?></strong></td></tr>
			
<?php if($_SESSION['ticket_updated'] == '1'){?>
			<tr><td colspan="2"><p class="alert_small">Ticket was successfully revised and re-submitted.</p>
<?php $_SESSION['ticket_updated'] = '';} ?>

<?php if($_SESSION['ticket_reopened'] == '1'){?>
			<tr><td colspan="2"><p class="alert_small">Ticket was successfully re-opened | Notification sent to SEDL Helpdesk.</p>
<?php $_SESSION['ticket_reopened'] = '';} ?>

			<tr><td colspan="2">The status and details of your ticket appear below. 
			
			<div style="float:right">
			<?php if($recordData['status'][0] !== 'Closed'){?><a href="help_ticket.php?action=edit&id=<?php echo $recordData['ticket_ID'][0];?>">Modify and re-submit this ticket</a><?php }else{ ?>
			
			
					<span style="align:right"><a href="javascript:toggle1();" id="displayText" title="Staff Member: Click to re-open this closed ticket.">Re-open Ticket</a></span>
					<div id="toggleText" style="display: none; padding:10px 10px 0px 10px;border:1px dotted #999999;background-color:#fff6bf">Enter the reason for re-opening this ticket:
					<form method="get" style="text-align:right">
					<input type="hidden" name="action" value="show1">
					<input type="hidden" name="mod" value="re_open">
					<input type="hidden" name="id" value="<?php echo $recordData['ticket_ID'][0];?>">
					<input type="hidden" name="row" value="<?php echo $recordData['c_row_ID'][0];?>">
					<input type="text" name="reopen_comments" size="50"><br>
					<input type="submit" name="submit" value="Submit">
					</form>
					</div>

			
			<?php } ?>
			</div> <p>

						<table width="100%" style="border:1px #a2a2a2 solid">


						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Submitted:</td>
						<td style="padding:6px;width:100%;border-bottom:1px dotted #cccccc""><?php echo $recordData['creation_timestamp'][0].' by '.$recordData['requestor_sims_ID'][0].' | '.$recordData['requestor_department'][0].' | '.$recordData['requestor_location'][0];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc;vertical-align:text-top" nowrap>Subject:</td>
						<td style="padding:6px;width:100%"><?php echo $recordData['issue_subject'][0];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc;vertical-align:text-top" nowrap>Description:</td>
						<td style="padding:6px;width:100%;border-bottom:1px dotted #cccccc"><?php echo $recordData['issue_description'][0];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Priority:</td>
						<td style="padding:6px;width:100%"><?php echo $recordData['priority'][0];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Recurring Issue:</td>
						<td style="padding:6px;width:100%"><?php echo $recordData['issue_recurring'][0];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc;vertical-align:text-top" nowrap>Attachments:</td>
						<td style="padding:6px;width:100%;border-top:1px dotted #cccccc" >
						<?php if($searchResult2['foundCount'] > 0){ $i=1;?>
						
							<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
							<?php echo $i;?>) <a href="http://198.214.141.190/sims/helpdesk/<?php echo $searchData2['attachment_filename'][0];?>"><?php echo $searchData2['attachment_description'][0];?></a><br>
							<?php $i++;} ?>						
						<p>
						<?php }else{ ?>
						<?php if($recordData['status'][0] == 'Closed'){?>N/A<?php } ?>
						<?php } ?>
						
<?php if($recordData['status'][0] !== 'Closed'){?>						

						<div style="background-color:#f9f6c9;padding:6px"><em>Upload files and/or screen shots relating to this ticket:</em><p>	

							<form action="http://198.214.141.190/sims/helpdesk_docs_upload_2.php" method="post" enctype="multipart/form-data">
							<input type="hidden" name="action" value="show1">
							<input type="hidden" name="ticket_ID" value="<?php echo $recordData['ticket_ID'][0];?>">
							<input type="hidden" name="user" value="<?php echo $_SESSION['user_ID'];?>">
							<input type="hidden" name="id" value="<?php echo $recordData['c_row_ID'][0];?>">
							Description: <input type="text" name="attachment_description" size="40"><br>

							<input type="file" name="file" id="file" /> 
							<br />
							<input type="submit" name="submit" value="Upload" />
							</form>

						</div>
						
<?php }?>

						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Status:</td>
						<td style="padding:6px;width:100%;background-color:#<?php if($recordData['status'][0] == 'New'){echo 'f9c9c9';}elseif($recordData['status'][0] == 'Open'){echo 'f9f6c9';}else{echo '7cb17d';}?>">
						<?php if($recordData['status'][0] == 'New'){echo $recordData['status'][0].' | pending assignment';}?>
						<?php if($recordData['status'][0] == 'Open'){echo $recordData['status'][0].' | Assigned to: '.$recordData['assigned_to'][0];}?>
						<?php if($recordData['status'][0] == 'Closed'){echo $recordData['status'][0].' | Completed by: '.$recordData['completed_by'][0];}?>
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc;vertical-align:text-top" nowrap>Ticket Thread:</td>
						<td style="padding:6px;width:100%">
						
						<?php foreach($searchResult3['data'] as $key => $searchData3) { ?>
						<table width="100%" style="border:1px #a2a2a2 solid;margin-bottom:4px">
						<tr style="background-color:#f9f6c9"><td class="tiny" nowrap style="width:15%"><?php echo $searchData3['creation_timestamp'][0];?></td><td class="tiny" style="width:70%"><?php echo $searchData3['action'][0];?></td><td class="tiny" style="width:15%"><?php echo $searchData3['user'][0];?></td></tr>
						<tr><td class="tiny" colspan="3"><?php echo $searchData3['comments'][0];?></td></tr>
						</table>						
						<?php } ?>	
						
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

}elseif($action == 'edit'){ 

################################
## START: GET FMP VALUE-LISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort,$dataSourceType);
$v1 -> SetDBData('SEDL_HelpDesk.fmp12','help_tickets_by_selection');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
##############################
## END: GET FMP VALUE-LISTS ##
##############################


##############################################
## START: FIND SELECTED TICKET ##
##############################################
$search = new FX($serverIP,$webCompanionPort,$dataSourceType);
$search -> SetDBData('SEDL_HelpDesk.fmp12','help_tickets_by_selection');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('ticket_ID',$_REQUEST['id']);
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam('creation_timestamp','descend');
//$search -> AddSortParam('travel_auth_ID','descend');
//$search -> AddSortParam('c_event_start_date_menu_sort_display','descend');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['travel_request_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
############################################
## END: FIND SELECTED TICKET ##
############################################
?>

<html>
<head>
<title>SIMS: My HelpDesk Tickets</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function checkFields() { 

	// Subject
		if (document.form2.issue_subject.value =="") {
			alert("Please enter the subject of this help ticket.");
			document.form2.issue_subject.focus();
			return false;	}

	// Description
		if (document.form2.issue_description.value =="") {
			alert("Please enter the description of this help ticket.");
			document.form2.issue_description.focus();
			return false;	}

}	
</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#333333"><img src="/staff/sims/images/helpdesk_header.jpg"></td></tr>
		
			<tr><td class="body" nowrap><strong>SIMS User:</strong> <?php echo $_SESSION['user_ID'];?></td><td align="right"><a href="menu_help_tickets.php">My Help Tickets</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<tr style="color:#ffffff;background-color:#747373"><td colspan="2"><strong>EDIT HELP TICKET #<?php echo $recordData['ticket_ID'][0];?></strong></td></tr>
			
			
			<tr><td colspan="2">Modify the details of your request below. Please enter as much information as possible so the HelpDesk staff can better troubleshoot your issue. <p>
			<form method="get" onsubmit="return checkFields()">
			<input type="hidden" name="action" value="show1">
			<input type="hidden" name="mod" value="save_edit">
			<input type="hidden" name="requestor_name" value="<?php echo $recordData['requestor_name'][0];?>">
			<input type="hidden" name="requestor_department" value="<?php echo $recordData['requestor_department'][0];?>">
			<input type="hidden" name="row" value="<?php echo $recordData['c_row_ID'][0];?>">
			<input type="hidden" name="id" value="<?php echo $recordData['ticket_ID'][0];?>">
						<table width="100%" style="border:1px #a2a2a2 solid">

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Status:</td>
						<td style="padding:6px;width:100%;background-color:#<?php if($recordData['status'][0] == 'New'){echo 'f9c9c9';}elseif($recordData['status'][0] == 'Open'){echo 'f9f6c9';}else{echo '7cb17d';}?>">
						<?php if($recordData['status'][0] == 'New'){echo $recordData['status'][0].' | pending assignment';}?>
						<?php if($recordData['status'][0] == 'Open'){echo $recordData['status'][0].' | Assigned to: '.$recordData['assigned_to'][0];}?>
						<?php if($recordData['status'][0] == 'Closed'){echo $recordData['status'][0].' | Completed by: '.$recordData['completed_by'][0];}?>
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc;vertical-align:text-top" nowrap>Subject:</td>
						<td style="padding:6px;width:100%;border-top:1px dotted #cccccc">
						<em>Enter the subject for this help ticket (e.g. - Can't get document to print...etc.)</em><br>
						<input type="text" name="issue_subject" size="75" value="<?php echo $recordData['issue_subject'][0];?>">
						
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc;vertical-align:text-top" nowrap>Description:</td>
						<td style="padding:6px;width:100%;border-bottom:1px dotted #cccccc">
						<em>Enter the specific details relating to your technical issue. Include as much detail as possible.</em><br>
						<textarea name="issue_description" rows="6" cols="75"><?php echo $recordData['issue_description'][0];?></textarea>
						
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Location:</td>
						<td style="padding:6px;width:100%">
						
						<select name="requestor_location">
						<option value="" selected>--Select location of this issue--
						
						<?php foreach($v1Result['valueLists']['issue_location'] as $key => $value) { ?>
						<option value="<?php echo $value;?>" <?php if($recordData['requestor_location'][0] == $value){echo 'selected';}?>> <?php echo $value; ?>
						<?php } ?>
						</select>
						or enter other location <input type="text" size="25" name="requestor_location_other">
						
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Category:</td>
						<td style="padding:6px;width:100%">
						
						<?php foreach($v1Result['valueLists']['category'] as $key => $value) { ?>
						<input type="radio" name="request_type" value="<?php echo $value;?>" <?php if($value == $recordData['request_type'][0]){?>checked<?php }?>><?php echo $value;?>&nbsp;
						<?php } ?>
						</select>
						
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Priority:</td>
						<td style="padding:6px;width:100%">
						
						<?php foreach($v1Result['valueLists']['priority'] as $key => $value) { ?>
						<input type="radio" name="priority" value="<?php echo $value;?>" <?php if($value == $recordData['priority'][0]){?>checked<?php }?>><?php echo $value;?>&nbsp;
						<?php } ?>
						</select>
						
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;vertical-align:text-top;background-color:#bdbcbc" nowrap>Issue Type:</td>
						<td style="padding:6px;width:100%"><em>Check all that apply:</em>

						<?php foreach($v1Result['valueLists']['issue_type'] as $key => $value) { ?>
						<input type="checkbox" name="issue_type[]" value="<?php echo $value;?>"<?php if (strpos($recordData['issue_type'][0],$value) !== false) {echo ' checked="checked"';}?>> <?php echo $value; ?>
						<?php } ?>
						
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;vertical-align:text-top;background-color:#bdbcbc" nowrap>Hardware Type:</td>
						<td style="padding:6px;width:100%"><em>Check all that apply:</em>
						

						<?php foreach($v1Result['valueLists']['hardware'] as $key => $value) { ?>
						<input type="checkbox" name="hardware_type[]" value="<?php echo $value;?>"<?php if (strpos($recordData['hardware_type'][0],$value) !== false) {echo ' checked="checked"';}?>> <?php echo $value; ?>
						<?php } ?>

						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Software Type:</td>
						<td style="padding:6px;width:100%">
						
						<select name="software_type">
						<option value="">--Select the software relating to this issue--
						
						<?php foreach($v1Result['valueLists']['software_type'] as $key => $value) { ?>
						<option value="<?php echo $value;?>" <?php if($recordData['software_type'][0] == $value){echo 'selected';}?>> <?php echo $value; ?>
						<?php } ?>
						</select>
						or enter other software <input type="text" size="25" name="software_type_other">
						
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Recurring Issue:</td>
						<td style="padding:6px;width:100%">
						
						<?php foreach($v1Result['valueLists']['Yes_No'] as $key => $value) { ?>
						<input type="radio" name="issue_recurring" value="<?php echo $value;?>" <?php if($value == $recordData['issue_recurring'][0]){?>checked<?php }?>><?php echo $value;?>&nbsp;
						<?php } ?>
						</select>
						
						</td>
						</tr>						

						</table>
						<input type="button" onClick="history.back()" value="Cancel"><input type="submit" name="submit" value="Submit Changes">
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

}elseif($action == 'new_submit'){ 

// TRANSFORM CHECKBOX ARRAYS FOR SAVING TO FMP
$issue_type = '';
for($i=0 ; $i<count($_REQUEST['issue_type']) ; $i++) {
$issue_type .= $_REQUEST['issue_type'][$i]."\r"; 
}

$hardware_type = '';
for($i=0 ; $i<count($_REQUEST['hardware_type']) ; $i++) {
$hardware_type .= $_REQUEST['hardware_type'][$i]."\r"; 
}


##############################################
## START: MAKE NEW HELP TICKET ##
##############################################
$newrecord = new FX($serverIP,$webCompanionPort,$dataSourceType);
$newrecord -> SetDBData('SEDL_HelpDesk.fmp12','help_tickets_by_selection',12);
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('requestor_sims_ID',$_REQUEST['requestor_sims_ID']);
$newrecord -> AddDBParam('requestor_name',$_REQUEST['requestor_name']);
$newrecord -> AddDBParam('requestor_email',$_REQUEST['requestor_email']);
$newrecord -> AddDBParam('requestor_department',$_REQUEST['requestor_department']);
if($_REQUEST['requestor_location_other'] !== ''){
$newrecord -> AddDBParam('requestor_location',$_REQUEST['requestor_location_other']);
}else{
$newrecord -> AddDBParam('requestor_location',$_REQUEST['requestor_location']);
}
$newrecord -> AddDBParam('priority',$_REQUEST['priority']);
$newrecord -> AddDBParam('issue_recurring',$_REQUEST['issue_recurring']);
$newrecord -> AddDBParam('issue_subject',$_REQUEST['issue_subject']);
$newrecord -> AddDBParam('issue_description',$_REQUEST['issue_description']);

$newrecordResult = $newrecord -> FMNew();

//echo '<p>$newrecordResult[errorCode]: '.$newrecordResult['errorCode'];
//echo '<p>$newrecordResult[foundCount]: '.$newrecordResult['foundCount'];
$recordData = current($newrecordResult['data']);
############################################
## END: MAKE NEW HELP TICKET ##
############################################

if($newrecordResult['errorCode'] == 0){
$_SESSION['ticket_saved'] = '1';

####################################################
## START: TRIGGER NOTIFICATION E-MAIL TO HELPDESK ##
####################################################

	$to = 'helpdesk@sedl.org';
	if($_REQUEST['priority'] == 'Urgent'){
	$subject = 'URGENT Helpdesk Ticket Received: '.$_REQUEST['issue_subject'];
	}else{
	$subject = 'Helpdesk Ticket Received: '.$_REQUEST['issue_subject'];
	}
	$message = 
	'SEDL IT Staff-'."\n\n".
	
	'An online help ticket has been submitted to SEDL Helpdesk by '.$_REQUEST['requestor_name'].'.'."\n\n".
	
	'------------------------------------------------------------'."\n".
	' TICKET DETAILS'."\n".
	'------------------------------------------------------------'."\n".
	'Requestor: '.$_REQUEST['requestor_name']."\n".
	'Unit: '.$_REQUEST['requestor_department']."\n".
	'Location: '.$_REQUEST['requestor_location']."\n".
	'Priority: '.$_REQUEST['priority']."\n".
	'Subject: '.$_REQUEST['issue_subject']."\n".
	'Description: '.$_REQUEST['issue_description']."\n".
	'------------------------------------------------------------'."\n\n".
	
	'To view this ticket, click here: '."\n".
	'fmp://198.214.140.246/SEDL_HelpDesk.fmp12?script=show_assigned_ticket&$id='.$recordData['ticket_ID'][0]."\n\n".
	
	'---------------------------------------------------------------------------------------------------------------------------------'."\n".
	'This is an auto-generated message from the SEDL HelpDesk Ticket system.'."\n".
	'---------------------------------------------------------------------------------------------------------------------------------';
	
	$headers = 'From: helpdesk@sedl.org'."\r\n".'Reply-To: '.$_SESSION['staff_email'];
	
	mail($to, $subject, $message, $headers);

####################################################
## END: TRIGGER NOTIFICATION E-MAIL TO HELPDESK ##
####################################################



}else{
$_SESSION['ticket_saved'] = '2';
$_SESSION['ticket_error'] = $newrecordResult['errorCode'];
header('Location: http://www.sedl.org/staff/sims/menu_help_tickets.php');
}

?>

<html>
<head>
<title>SIMS: My HelpDesk Tickets</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function focusAttachment() { 

			document.form2.attachment_description.focus();

}	
</script>


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="focusAttachment()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#333333"><img src="/staff/sims/images/helpdesk_header.jpg"></td></tr>
		
			<tr><td class="body" nowrap><strong>SIMS User:</strong> <?php echo $_SESSION['user_ID'];?></td><td align="right"><a href="menu_help_tickets.php">My Help Tickets</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<tr style="color:#ffffff;background-color:#747373"><td colspan="2"><strong>TICKET #<?php echo $recordData['ticket_ID'][0];?></strong></td></tr>
			
<?php if($_SESSION['ticket_saved'] == '1'){?>
			<tr><td colspan="2"><p class="alert_small">Ticket was successfully submitted. Add screenshots or attachments using the form below.</p>
<?php $_SESSION['ticket_saved'] = '';} ?>

			<tr><td colspan="2">The status and details of your ticket appear below. <?php if($recordData['status'][0] !== 'Closed'){?><div style="float:right"><a href="help_ticket.php?action=edit&id=<?php echo $recordData['ticket_ID'][0];?>">Modify and re-submit this ticket</a></div><?php }?> <p>

						<table width="100%" style="border:1px #a2a2a2 solid">

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Status:</td>
						<td style="padding:6px;width:100%;background-color:#<?php if($recordData['status'][0] == 'New'){echo 'f9c9c9';}elseif($recordData['status'][0] == 'Open'){echo 'f9f6c9';}else{echo '7cb17d';}?>">
						<?php if($recordData['status'][0] == 'New'){echo $recordData['status'][0].' | pending assignment';}?>
						<?php if($recordData['status'][0] == 'Open'){echo $recordData['status'][0].' | Assigned to: '.$recordData['assigned_to'][0];}?>
						<?php if($recordData['status'][0] == 'Closed'){echo $recordData['status'][0].' | Completed by: '.$recordData['completed_by'][0];}?>
						</td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Submitted:</td>
						<td style="padding:6px;width:100%"><?php echo $recordData['creation_timestamp'][0].' by '.$recordData['requestor_sims_ID'][0].' | '.$recordData['requestor_department'][0].' | '.$recordData['requestor_location'][0];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc;vertical-align:text-top" nowrap>Subject:</td>
						<td style="padding:6px;width:100%"><?php echo $recordData['issue_subject'][0];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc;vertical-align:text-top" nowrap>Description:</td>
						<td style="padding:6px;width:100%;border-bottom:1px dotted #cccccc"><?php echo $recordData['issue_description'][0];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Priority:</td>
						<td style="padding:6px;width:100%"><?php echo $recordData['priority'][0];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc" nowrap>Recurring Issue:</td>
						<td style="padding:6px;width:100%"><?php echo $recordData['issue_recurring'][0];?></td>
						</tr>						

						<tr>
						<td style="padding:6px;font-weight:bold;background-color:#bdbcbc;vertical-align:text-top" nowrap>Attachments:</td>
						<td style="padding:6px;width:100%;border-top:1px dotted #cccccc" >
						<?php if($searchResult2['foundCount'] > 0){ $i=1;?>
						
							<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
							<?php echo $i;?>) <a href="http://198.214.141.190/sims/helpdesk/<?php echo $searchData2['attachment_filename'][0];?>"><?php echo $searchData2['attachment_description'][0];?></a><br>
							<?php $i++;} ?>						
						<p>
						<?php } ?>
						
<?php if($recordData['status'][0] !== 'Closed'){?>						

						<div style="background-color:#f9f6c9;padding:6px"><em>Upload files and/or screen shots relating to this ticket:</em><p>	

							<form name="form2" action="http://198.214.141.190/sims/helpdesk_docs_upload_2.php" method="post" enctype="multipart/form-data">
							<input type="hidden" name="action" value="show1">
							<input type="hidden" name="ticket_ID" value="<?php echo $recordData['ticket_ID'][0];?>">
							<input type="hidden" name="user" value="<?php echo $_SESSION['user_ID'];?>">
							<input type="hidden" name="id" value="<?php echo $recordData['c_row_ID'][0];?>">
							Description: <input type="text" name="attachment_description" size="40"><br>

							<input type="file" name="file" id="file" /> 
							<br />
							<input type="submit" name="submit" value="Upload" />
							</form>

						</div>
						
<?php }?>

						</td>
						</tr>						

						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>


<?php } ?>
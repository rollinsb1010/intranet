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
$position_ID = $_GET['pos_id'];


$view = $_GET['v'];

if($action == ''){ 
$action = 'view';
}

$resume_ID = $_GET['id'];
$reviewer_submit = $_GET['reviewer_submit'];
$logx = $_GET['logx'];
$logid = $_GET['logid'];
//$status = $_GET['status'];


$location = 'Location: http://www.sedl.org/staff/sims/menu_positions_novs.php?pos_id='.$position_ID.'&sortfield='.$sortfield.'&sortorder='.$sortorder.'&displaynum='.$displaynum.'#'.$recordData['resume_ID'][0];
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

################################
## START: GET FMP VALUE-LISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('admin_base.fp7','action_log_comments');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
//echo  '<p>errorCode: '.$v1Result['errorCode'];
//echo  '<p>foundCount: '.$v1Result['foundCount'];
//print_r($v1Result);
##############################
## END: GET FMP VALUE-LISTS ##
##############################


#################################################################
## START: FIND THIS JOB APPLICATION ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('admin_base.fp7','resume_table');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('resume_ID','=='.$resume_ID);
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam('leave_hrs_date','ascend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
$row_ID = $recordData['c_row_ID'][0];
$created_by = $recordData['created_by'][0];


//if((($recordData['PR_pre_approval_required_IT'][0] == '1')&&($recordData['sign_status_IT'][0] != '1'))||(($recordData['PR_pre_approval_required_IRC'][0] == '1')&&($recordData['sign_status_IRC'][0] != '1'))){
//$preappRequired = '1';
//}else{
//$preappRequired = '0';
//}
###############################################################
## END: FIND THIS JOB APPLICATION ##
###############################################################

#################################################################
## START: FIND OTHER RESUMES OR APPS RELATED TO THIS APPLICANT ##
#################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('admin_base.fp7','resume_table');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('related_records_matchkey',$resume_ID);
//$search2 -> AddDBParam('-lop','or');

//$search2 -> AddSortParam('leave_hrs_date','ascend');


$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
//print_r ($searchResult2);
$recordData2 = current($searchResult2['data']);
###############################################################
## END: FIND OTHER RESUMES OR APPS RELATED TO THIS APPLICANT ##
###############################################################

if($recordData['c_attachment_count'][0] > 0){ // THE SELECTED APP HAS FILE ATTACHMENTS
#################################################################
## START: FIND ATTACHMENTS RELATED TO THIS APPLICANT ##
#################################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('admin_base.fp7','files_attachments');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('resume_ID','=='.$resume_ID);
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


if($recordData['c_action_log_count'][0] > 0){ // THE SELECTED APP HAS USER LOG ENTRIES
#################################################################
## START: FIND USER LOG ENTRIES RELATED TO THIS PR ##
#################################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('admin_base.fp7','resumes_action_log');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('resume_ID','=='.$resume_ID);
//$search4 -> AddDBParam('-lop','or');

$search4 -> AddSortParam('creation_timestamp','ascend');


$searchResult4 = $search4 -> FMFind();

//echo '<p>$searchResult4[errorCode]: '.$searchResult4['errorCode'];
//echo '<p>$searchResult4[foundCount]: '.$searchResult4['foundCount'];
//print_r ($searchResult4);
$recordData4 = current($searchResult4['data']);
###############################################################
## END: FIND USER LOG ENTRIES RELATED TO THIS APP ##
###############################################################
}

$user = $_SESSION['user_ID'];
//echo '<span style="color:#999999">User: '.$user.'</span>';
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Approve JOB APPLICATIONs</title>
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
				<p class="alert_small"><b>Reviewer: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - Click below to download application files. | <a href="/staff/sims/menu_positions_novs_aa.php?action=<?php echo $view;?>&sortfield=<?php echo $sortfield;?>&sortorder=<?php echo $sortorder;?>&displaynum=<?php echo $displaynum;?>&pos_id=<?php echo $position_ID;?>#<?php echo $recordData['resume_ID'][0];?>">Close Document</a></p>
			</td></tr>
			
			
			
			<tr><td colspan="2">
			
						<table cellpadding="10" cellspacing="0" border="0" bordercolor="ebebeb" bgcolor="ffffff" width="100%">
<?php if($view == 'urs'){ ?>
						<tr bgcolor="#a2c7ca"><td class="body"><strong>Unsolicited Resume: (<?php echo $recordData['resume_ID'][0];?>)</strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Resume Status: <strong><?php if($recordData['status'][0] == 'Pending'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['status'][0]).'</span>';?></strong></span></td></tr>
<?php }else{ ?>
						<tr bgcolor="#a2c7ca"><td class="body"><strong>NOV Position: <?php echo $recordData['positions_NOVs::c_position_display'][0].' ('.$recordData['resume_ID'][0].')';?></strong></td><td class="body" align="right" nowrap><span style="background-color:#ffffff;border:1px dotted #000000;padding:4px">Application Status: <strong><?php if($recordData['nov_status'][0] == 'Pending'){echo '<span style="color:#ff0000">';}else{echo '<span style="color:#0033ff">';}?><?php echo strtoupper($recordData['nov_status'][0]).'</span>';?></strong></span></td></tr>
<?php } ?>
						<tr><td class="body" nowrap><strong>APPLICATION</strong></td><td align="right">ID: <?php echo $recordData['resume_ID'][0];?></td></tr>

						<tr><td colspan="2" class="body">
						

							<table cellpadding="7" cellspacing="0" border="1" bordercolor="#ebebeb" width="100%" class="sims">
							
								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>APPLICANT DETAILS</strong></div>
								
									<table style="border:0px dotted #000000;width:100%;margin-top:6px">
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Name</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['resume_first_name'][0].' '.$recordData['resume_last_name'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Address</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['address'][0];?><br><?php echo $recordData['resume_city'][0];?>, <?php echo $recordData['resume_state'][0];?> <?php echo $recordData['zip'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Phone</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><?php echo $recordData['phone'][0];?></td></tr>
										<tr><td style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">E-mail</td><td style="width:100%;border:1px dotted #000000;margin-top:6px;padding:5px" nowrap><a href="mailto:<?php echo $recordData['email'][0];?>"><?php echo $recordData['email'][0];?></a></td></tr>
									</table>

								
								</td>

								<td rowspan="3" class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>APPLICANT FILES (<?php echo $searchResult3['foundCount'];?>)</strong></div>
								
								<?php if($searchResult3['foundCount'] > 0){ ?>
									<ol style="border:1px dotted #000000;background-color:#fff6bf;margin-top:6px;padding:5px;width:95%;list-style-position: inside;">
									
									<?php foreach($searchResult3['data'] as $key => $searchData3) { // LIST ATTACHMENTS ?>
										
										<li style="padding:5px">
										
										<strong><?php echo ucwords($searchData3['attachment_type'][0]);?></strong><br>
										File: <a href="http://198.214.141.190/sims/attachments/<?php echo $searchData3['attachment_filename'][0];?>" target="_blank" title="Click to download this file for review."><?php echo $searchData3['attachment_filename'][0];?></a><br>

										<div class="tiny" style="padding:4px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb;margin: 6px 2px 0px 2px">
										Uploaded: <?php echo $searchData3['upload_timestamp'][0];?> by <?php echo $searchData3['uploaded_by'][0];?>
										<?php if($searchData3['attachment_notes'][0] !== ''){?><br>Comments: <?php echo $searchData3['attachment_notes'][0];?><br><?php }?>
										</div>

									
										</li>

										<hr style="border:1px dotted #000000">
									<?php  } ?></ol>
	
								<?php }else{ ?>
								
								N/A
								<?php } ?>

								</td>



								</tr>

								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>HR COMMENTS</strong></div><br><?php if($recordData['comments'][0] != ''){echo $recordData['comments'][0];}else{echo 'N/A';}?></td></tr>

								<tr><td class="body" style="vertical-align:text-top"><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>OTHER RESUMES/APPLICATIONS (<?php echo $searchResult2['foundCount'];?>)</strong></div><br>
									<?php if($searchResult2['foundCount'] > 0){ ?>
						
										<table style="border:0px dotted #000000;margin-top:6px">
										<tr bgcolor="#ebebeb">
										
										<td nowrap style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">ID</td>
										<td nowrap style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Date</td>
										<td nowrap style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Type</td>
										<td nowrap style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Reviewed by</td>
										<td nowrap style="vertical-align:text-top;border:1px dotted #000000;background-color:#ebebeb;margin-top:6px;padding:5px">Status</td>

										</tr>
		

										
										<?php foreach($searchResult2['data'] as $key => $searchData) { ?>			
												<tr class="body">
												
												
												<td style="border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:top" nowrap><a href="positions_novs_ba.php?id=<?php echo $searchData['resume_ID'][0];?>" target="_blank"><?php echo $searchData['resume_ID'][0];?></a></td>
												<td style="border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:top" nowrap><?php echo $searchData['creation_timestamp'][0];?></td>
												<td style="border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:top" nowrap><?php echo $searchData['record_type'][0];?></td>
												<td style="border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:top" nowrap><?php if($searchData['c_nov_mgrs_sent_to_search_target'][0] == ''){echo 'HR';}else{echo $searchData['c_nov_mgrs_sent_to_search_target'][0];}?></td>
												<td style="border:1px dotted #000000;margin-top:6px;padding:5px;vertical-align:top" nowrap><?php echo $searchData['c_status_display'][0];?></td>
												
												
												</tr>
											
									
										<?php } ?>

										</table>

								<?php }else{ ?>
								
								N/A
								<?php } ?>

								
								</td></tr>


								<tr><td  colspan="2" style="vertical-align:text-top" nowrap><div style="padding:4px;background-color:#cccccc" class="tiny"><strong>PROCESS LOG</strong></div>



								
									<table width="100%" style="margin-top:6px;width:100%">

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf"><strong>DATE</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>USER</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>ACTION</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap><strong>SENT TO</strong></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px;background-color:#fff6bf" nowrap width="100%"><strong>COMMENTS</strong></td></tr>

									<?php if($searchResult4['foundCount'] > 0){ // SHOW APP ACTION LOG ?>
									<?php foreach($searchResult4['data'] as $key => $searchData4) {  ?>

										<tr><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['creation_timestamp'][0];?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['user'][0];?><?php if($_SESSION['user_ID'] == $searchData4['user'][0]){?><br><a href="positions_novs_ba.php?id=<?php echo $recordData['resume_ID'][0];?>&logx=1&logid=<?php echo $searchData4['c_row_ID'][0];?>" onclick="javascript:return confirm('Are you sure you want to delete this log entry?')">Delete</a><?php }?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px" nowrap><?php echo $searchData4['action'][0];?></td><td class="tiny" style="vertical-align:text-top;border:1px dotted #000000;padding:3px"><?php echo $searchData4['action_target'][0];?></td><td class="tiny" width="100%" style="vertical-align:text-top;border:1px dotted #000000;padding:3px"><?php if($searchData4['comment'][0] != ''){?><?php echo $searchData4['comment'][0];?><?php }else{ echo 'N/A';}?></td></tr>
									
									<?php } ?>
									<?php } ?>


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
<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2007 by SEDL
#
# Written by Eric Waters 06/26/2007
#############################################################################

###############################
## START: LOAD FX.PHP INCLUDES
###############################
include_once('FX/FX.php');
include_once('FX/server_data.php');
###############################
## END: LOAD FX.PHP INCLUDES
###############################

###############################
## START: GRAB FORM VARIABLES
###############################
//$sortfield = $_GET['sortfield'];
$timesheet_ID = $_GET['Timesheet_ID'];
$action = $_GET['action'];
//$_SESSION['timesheet_ID'] = $_GET['Timesheet_ID'];
//$action = $_GET['action'];
//$new_row = $_GET['new_row'];
//$new_row_ID = $_GET['new_row_ID'];
//$row_ID = $_GET['edit_row_ID'];

//echo '<br>Action: '.$action;
//echo '<br>Timesheet ID: '.$timesheet_ID;
###############################
## END: GRAB FORM VARIABLES
###############################

############################################################
## START: FIND OUTSIDE EMPLOYMENT FIELDS FOR THIS TIMESHEET
############################################################
//echo $row_ID;
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','timesheets_rpt_out_empl','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('TimesheetID',$timesheet_ID);
//$search -> AddDBParam('HrsType','WkHrsReg');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam ($sortfield,'descend');


$searchResult = $search -> FMFind();
//$_SESSION['wk_hrs_data'] = $searchResult;

//echo $searchResult['errorCode'];
//echo '<br>RegularWkHrs FoundCount: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
##########################################################
## END: FIND OUTSIDE EMPLOYMENT FIELDS FOR THIS TIMESHEET
##########################################################

if($action == 'edit'){

#################################################################################################
## START: DISPLAY THE REPORT OF OUTSIDE EMPLOYMENT FORM IN AN HTML TABLE IN EDIT MODE 
#################################################################################################
?>
<span name="edit">

<html>
<head>
<title>SIMS - Timesheets - Report of Outside Employment Form</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">
//<!--
function confirmDelete() { 
	var answer = confirm ("Are you sure you want to delete this row?")
	if (!answer) {
	return false;
	}
}
// -->
</script>


</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Timesheets</h1><hr /></td></tr>
			
			<tr bgcolor="#a2c7ca"><td class="body"><strong>Report of Outside Employment Form</strong></td><td align="right">Timesheet Status: <?php echo $recordData['TimesheetSubmittedStatus'][0];?> | Pay Period: <strong><?php echo $recordData['PayPeriodBegin'][0];?> - <?php echo $recordData['c_PayPeriodEnd'][0];?></strong></td></tr>
			
			
			
			<tr><td class="body">&nbsp;<i>NOTE: Enter "None" if not applicable.</i></td><td align="right">Timesheet ID: <?php echo $_SESSION['timesheet_ID'];?>
			<?php if($_SESSION['today_stamp'] > $_SESSION['lockout_day_stamp']){ //IF THE TIMESHEET IS LOCKED ?><img src="/staff/sims/images/padlock.jpg" border="0"><?php } ?>
			</td></tr>
			<tr><td class="body" colspan=2>
			<form name="outside_empl_rpt">
			<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
			<input type="hidden" name="action" value="confirm">
			<input type="hidden" name="Timesheet_ID" value="<?php echo $_SESSION['timesheet_ID'];?>">
			


<!--BEGIN FIRST SECTION: STAFF INFORMATION AND SIGNATURE BOX-->


							<table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="body">
							
							<tr bgcolor="#a2c7ca"><td class="body">&nbsp;Staff Details:</td><td class="body">&nbsp;Signature:</td></tr>
							
							<tr><td class="body" valign="top">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								
								<tr><td align="right"><font color="666666">Pay Period Begin:</font></td><td><?php echo $recordData['PayPeriodBegin'][0];?></td><td><font color="666666">Pay Period End:</font> <?php echo $recordData['c_PayPeriodEnd'][0];?></td></tr>
								<tr><td align="right"><font color="666666">Name:</font></td><td colspan="2"><?php echo $_SESSION['timesheet_name'];?></td></tr>
								<tr><td align="right"><font color="666666">Title:</font></td><td colspan="2"><?php echo $_SESSION['title'];?></td></tr>
								<tr><td align="right"><font color="666666">SEDL Unit:</font></td><td colspan="2"><?php echo $_SESSION['workgroup'];?></td></tr>
								</table>
							
							</td>
							
							<td class="body" valign="middle">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								<tr><td valign="bottom">
								
								
								<?php if($recordData['RptOutsideEmplFormSigned'][0] == 1){ ?>
									<center>
									<img src="/staff/sims/signatures/<?php echo $recordData['sims_user_ID'][0];?>.png"><br>
									<?php echo $recordData['RptOutsideEmplDateSigned'][0];?>
									</center>
								<?php }?>
								
								
								</td><td>
								</table>
								
							</td></tr>
	
<!--END FIRST SECTION: STAFF INFORMATION AND SIGNATURE BOX-->


<!--BEGIN SECOND SECTION: CONSULTANT SERVICES RENDERED TO-->	
	
							<tr bgcolor="#a2c7ca"><td class="body" colspan="2">&nbsp;Outside Employment/Consultant Services Performed/Rendered to:</td></tr>
							
							<tr><td class="body" colspan="2">
							
								<table cellspacing="0" cellpadding="5" border="1" bordercolor="cccccc" width="100%">
								<tr><td align="center"><font color="666666">Name & Address of Employer</font></td><td align="center"><font color="666666">Employment or Type of Services Rendered</font></td><td align="center"><font color="666666">Dates of Service<br>Use format: MM/DD/YYYY</font></td><td align="center"><font color="666666">Duration of Employment or Service</font></td></tr>
								<tr><td align="center" valign="top"><input type="text" name="rpt_out_empl_1a" value="<?php echo $recordData['RptOutsideEmplEmployer1'][0];?>"></td><td align="center" valign="top"><input type="text" name="rpt_out_empl_2a" value="<?php echo $recordData['RptOutsideEmplService1'][0];?>"></td><td align="right" valign="top" nowrap>From: <input type="text" name="rpt_out_empl_3a1" value="<?php echo $recordData['RptOutsideEmplDateFrom1'][0];?>"><br>To: <input type="text" name="rpt_out_empl_3a2" value="<?php echo $recordData['RptOutsideEmplDateTo1'][0];?>"></td><td align="center" valign="top"><input type="text" name="rpt_out_empl_4a" value="<?php echo $recordData['RptOutsideEmplTime1'][0];?>"></td></tr>
								<tr><td align="center" valign="top"><input type="text" name="rpt_out_empl_1b" value="<?php echo $recordData['RptOutsideEmplEmployer2'][0];?>"></td><td align="center" valign="top"><input type="text" name="rpt_out_empl_2b" value="<?php echo $recordData['RptOutsideEmplService2'][0];?>"></td><td align="right" valign="top" nowrap>From: <input type="text" name="rpt_out_empl_3b1" value="<?php echo $recordData['RptOutsideEmplDateFrom2'][0];?>"><br>To: <input type="text" name="rpt_out_empl_3b2" value="<?php echo $recordData['RptOutsideEmplDateTo2'][0];?>"></td><td align="center" valign="top"><input type="text" name="rpt_out_empl_4b" value="<?php echo $recordData['RptOutsideEmplTime2'][0];?>"></td></tr>
								<tr><td align="center" valign="top"><input type="text" name="rpt_out_empl_1c" value="<?php echo $recordData['RptOutsideEmplEmployer3'][0];?>"></td><td align="center" valign="top"><input type="text" name="rpt_out_empl_2c" value="<?php echo $recordData['RptOutsideEmplService3'][0];?>"></td><td align="right" valign="top" nowrap>From: <input type="text" name="rpt_out_empl_3c1" value="<?php echo $recordData['RptOutsideEmplDateFrom3'][0];?>"><br>To: <input type="text" name="rpt_out_empl_3c2" value="<?php echo $recordData['RptOutsideEmplDateTo3'][0];?>"></td><td align="center" valign="top"><input type="text" name="rpt_out_empl_4c" value="<?php echo $recordData['RptOutsideEmplTime3'][0];?>"></td></tr>
								</table>
											
							</td></tr>		
							
<!--END SECOND SECTION: CONSULTANT SERVICES RENDERED TO-->	

							<tr><td class="body" colspan="2">
							<p class="info_small"><span class="tiny">
							The above information is furnished by the undersigned in compliance with SEDL Board and Administrative Policy/Procedure 10.05. I understand that SEDL will make reasonable efforts to hold the above furnished information in confidence except where SEDL may be legally required to furnish such information to competent authority.<br>&nbsp;<br>
							I declare the foregoing to be, to the best of my knowledge and belief, a full and accurate statement of the facts. It is understood by me that any false or misleading statements shall be sufficient reason for my dismissal for cause from the services of Southwest Educational Development Laboratory (i.e., SEDL). I hereby release the Southwest Educational Development Corporation from all liability whatsoever that may issue from securing this information.
							</span>
							<center><input type="submit" name="submit" value="Sign / Confirm Changes"></center>
							</p>
							</td></tr>
							
							
							
							
							</table>

	

			</form>
			
			
			
			
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>







</body>

</html>

</span>
<?php
#################################################################################################
## END: DISPLAY THE REPORT OF OUTSIDE EMPLOYMENT FORM IN AN HTML TABLE IN EDIT MODE
#################################################################################################
 
} elseif ($action == 'view') {

#################################################################################################
## START: DISPLAY THE REPORT OF OUTSIDE EMPLOYMENT FORM IN AN HTML TABLE IN READ-ONLY MODE
#################################################################################################
 
 ?>
<span name="view">

<html>
<head>
<title>SIMS - Timesheets - Report of Outside Employment Form</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">
//<!--
function confirmDelete() { 
	var answer = confirm ("Are you sure you want to delete this row?")
	if (!answer) {
	return false;
	}
}
// -->
</script>


</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Timesheets</h1><hr /></td></tr>
			
			<tr><td colspan="2" align="right"><a href="/staff/sims/timesheets_rpt_out_empl_print.php?Timesheet_ID=<?php echo $_SESSION['timesheet_ID'];?>&action=print" target="_blank">Print form</a> | <a href="timesheets.php?Timesheet_ID=<?php echo $_SESSION['timesheet_ID'];?>&action=view&src=menu&payperiod=<?php echo $recordData['c_PayPeriodEnd'][0];?>">Return to timesheet</a></td></tr>
			
			<tr bgcolor="#a2c7ca"><td class="body"><strong>Report of Outside Employment Form</strong></td><td align="right">Timesheet Status: <?php echo $recordData['TimesheetSubmittedStatus'][0];?> | Pay Period: <strong><?php echo $recordData['PayPeriodBegin'][0];?> - <?php echo $recordData['c_PayPeriodEnd'][0];?></strong></td></tr>
			
			
			<tr><td class="body">&nbsp;<i>NOTE: Enter "None" if not applicable.</i></td><td align="right">Timesheet ID: <?php echo $_SESSION['timesheet_ID'];?>
			<?php if($_SESSION['today_stamp'] > $_SESSION['lockout_day_stamp']){ //IF THE TIMESHEET IS LOCKED ?><img src="/staff/sims/images/padlock.jpg" border="0"><?php } ?>
			</td></tr>
			<tr><td class="body" colspan=2>
			


<!--BEGIN FIRST SECTION: STAFF INFORMATION AND SIGNATURE BOX-->


							<table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="body">
							
							<tr bgcolor="#a2c7ca"><td class="body">&nbsp;Staff Details:</td><td class="body">&nbsp;Signature:</td></tr>
							
							<tr><td class="body" valign="top">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								
								<tr><td align="right"><font color="666666">Pay Period Begin:</font></td><td><?php echo $recordData['PayPeriodBegin'][0];?></td><td><font color="666666">Pay Period End:</font> <?php echo $recordData['c_PayPeriodEnd'][0];?></td></tr>
								<tr><td align="right"><font color="666666">Name:</font></td><td colspan="2"><?php echo $_SESSION['timesheet_name'];?></td></tr>
								<tr><td align="right"><font color="666666">Title:</font></td><td colspan="2"><?php echo $_SESSION['title'];?></td></tr>
								<tr><td align="right"><font color="666666">SEDL Unit:</font></td><td colspan="2"><?php echo $_SESSION['workgroup'];?></td></tr>
								</table>
							
							</td>
							
							<td class="body" valign="middle">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								<tr><td valign="bottom">
								
								
								<?php if($recordData['RptOutsideEmplFormSigned'][0] == 1){ ?>
									
									<center>
									<img src="/staff/sims/signatures/<?php echo $recordData['sims_user_ID'][0];?>.png"><br>
									<?php echo $recordData['RptOutsideEmplDateSigned'][0];?>
									</center>
								
								<?php }else{?>
									
									<center>
									<form>
									<input type="hidden" name="row_ID" value="<?php echo $recordData['c_row_ID_cwp'][0];?>">
									<input type="hidden" name="action" value="confirm">
									<input type="hidden" name="Timesheet_ID" value="<?php echo $_SESSION['timesheet_ID'];?>">
									<input type="hidden" name="quick_sign" value="yes">
											
									<input type="submit" name="submit" value="Sign Form">
									</form>
									</center>
									
								<?php } ?>
								
								</td><td>
								</table>
								
							</td></tr>
	
<!--END FIRST SECTION: STAFF INFORMATION AND SIGNATURE BOX-->


<!--BEGIN SECOND SECTION: CONSULTANT SERVICES RENDERED TO-->	
	
							<tr bgcolor="#a2c7ca"><td class="body" colspan="2">&nbsp;Outside Employment/Consultant Services Performed/Rendered to:</td></tr>
							
							<tr><td class="body" colspan="2">
							
								<table cellspacing="0" cellpadding="5" border="1" bordercolor="cccccc" width="100%">
								<tr><td align="center"><font color="666666">Name & Address of Employer</font></td><td align="center"><font color="666666">Employment or Type of Services Rendered</font></td><td align="center"><font color="666666">Dates</font></td><td align="center"><font color="666666">Duration of Employment or Service</font></td></tr>
								<tr><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplEmployer1'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplService1'][0];?></td><td align="right" valign="top" nowrap>  <table cellpadding="0" cellspacing="0" border="0"><tr><td align="right">From:</td><td><?php echo $recordData['RptOutsideEmplDateFrom1'][0];?></td></tr><tr><td align="right">To:</td><td><?php echo $recordData['RptOutsideEmplDateTo1'][0];?></td></tr></table>  </td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplTime1'][0];?></td></tr>
								<tr><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplEmployer2'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplService2'][0];?></td><td align="right" valign="top" nowrap>  <table cellpadding="0" cellspacing="0" border="0"><tr><td align="right">From:</td><td><?php echo $recordData['RptOutsideEmplDateFrom2'][0];?></td></tr><tr><td align="right">To:</td><td><?php echo $recordData['RptOutsideEmplDateTo2'][0];?></td></tr></table>  </td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplTime2'][0];?></td></tr>
								<tr><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplEmployer3'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplService3'][0];?></td><td align="right" valign="top" nowrap>  <table cellpadding="0" cellspacing="0" border="0"><tr><td align="right">From:</td><td><?php echo $recordData['RptOutsideEmplDateFrom3'][0];?></td></tr><tr><td align="right">To:</td><td><?php echo $recordData['RptOutsideEmplDateTo3'][0];?></td></tr></table>  </td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplTime3'][0];?></td></tr>
								</table>
											
							</td></tr>		
							
<!--END SECOND SECTION: CONSULTANT SERVICES RENDERED TO-->	

							<tr><td class="body" colspan="2">
							<p class="info_small"><span class="tiny">
							The above information is furnished by the undersigned in compliance with SEDL Board and Administrative Policy/Procedure 10.05. I understand that SEDL will make reasonable efforts to hold the above furnished information in confidence except where SEDL may be legally required to furnish such information to competent authority.<br>&nbsp;<br>
							I declare the foregoing to be, to the best of my knowledge and belief, a full and accurate statement of the facts. It is understood by me that any false or misleading statements shall be sufficient reason for my dismissal for cause from the services of Southwest Educational Development Laboratory (i.e., SEDL). I hereby release the Southwest Educational Development Corporation from all liability whatsoever that may issue from securing this information.
							</span>
							<center>
							<?php if($_SESSION['today_stamp'] <= $_SESSION['lockout_day_stamp']){ //IF THE TIMESHEET IS NOT LOCKED ?>
							<form><input type="hidden" name="action" value="edit"><input type="hidden" name="Timesheet_ID" value="<?php echo $_SESSION['timesheet_ID'];?>"><input type="submit" name="submit" value="Edit Form"></center>
							<?php } ?>
							</p>
							</td></tr>
							
							
							
							
							</table>

	

			</form>
			
			
			
			
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>







</body>

</html>

</span>
 <?php
 
#################################################################################################
## END: DISPLAY THE REPORT OF OUTSIDE EMPLOYMENT FORM IN AN HTML TABLE IN READ-ONLY MODE
#################################################################################################

} elseif ($action == 'confirm') { ?>

<span name="confirm">

<?php
###############################################################################################################
## START: SAVE FORM CHANGES & DISPLAY THE REPORT OF OUTSIDE EMPLOYMENT FORM IN AN HTML TABLE IN READ-ONLY MODE
###############################################################################################################

###############################
## START: GRAB FORM VARIABLES
###############################
//$timesheet_ID = $_SESSION['timesheet_ID'];
//$update_row = $recordData['c_row_ID_cwp'][0];
$quick_sign = $_GET['quick_sign'];
$update_row = $_GET['row_ID'];
//echo '<br>Update Row: '.$update_row;

$employer1a = $_GET['rpt_out_empl_1a'];
$employer1b = $_GET['rpt_out_empl_1b'];
$employer1c = $_GET['rpt_out_empl_1c'];

$employment2a = $_GET['rpt_out_empl_2a'];
$employment2b = $_GET['rpt_out_empl_2b'];
$employment2c = $_GET['rpt_out_empl_2c'];

$dates3a1 = $_GET['rpt_out_empl_3a1'];
$dates3b1 = $_GET['rpt_out_empl_3b1'];
$dates3c1 = $_GET['rpt_out_empl_3c1'];

$dates3a2 = $_GET['rpt_out_empl_3a2'];
$dates3b2 = $_GET['rpt_out_empl_3b2'];
$dates3c2 = $_GET['rpt_out_empl_3c2'];

$duration4a = $_GET['rpt_out_empl_4a'];
$duration4b = $_GET['rpt_out_empl_4b'];
$duration4c = $_GET['rpt_out_empl_4c'];

//echo '<br>Timesheet ID: '.$timesheet_ID;
###############################
## END: GRAB FORM VARIABLES
###############################

################################
## START: UPDATE THE FMP RECORD
################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets_rpt_out_empl');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);

if($quick_sign != 'yes'){
$update -> AddDBParam('RptOutsideEmplEmployer1',$employer1a);
$update -> AddDBParam('RptOutsideEmplEmployer2',$employer1b);
$update -> AddDBParam('RptOutsideEmplEmployer3',$employer1c);

$update -> AddDBParam('RptOutsideEmplService1',$employment2a);
$update -> AddDBParam('RptOutsideEmplService2',$employment2b);
$update -> AddDBParam('RptOutsideEmplService3',$employment2c);

$update -> AddDBParam('RptOutsideEmplDateFrom1',$dates3a1);
$update -> AddDBParam('RptOutsideEmplDateFrom2',$dates3b1);
$update -> AddDBParam('RptOutsideEmplDateFrom3',$dates3c1);

$update -> AddDBParam('RptOutsideEmplDateTo1',$dates3a2);
$update -> AddDBParam('RptOutsideEmplDateTo2',$dates3b2);
$update -> AddDBParam('RptOutsideEmplDateTo3',$dates3c2);

$update -> AddDBParam('RptOutsideEmplTime1',$duration4a);
$update -> AddDBParam('RptOutsideEmplTime2',$duration4b);
$update -> AddDBParam('RptOutsideEmplTime3',$duration4c);

}

$update -> AddDBParam('RptOutsideEmplFormSigned','1');

$updateResult = $update -> FMEdit();

$recordData = current($updateResult['data']);

if($updateResult['errorCode']==0) { 

$_SESSION['rpt_outside_empl_form_signed'] = '1';
?>

<html>
<head>
<title>SIMS - Timesheets - Report of Outside Employment Form</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">
//<!--
function confirmDelete() { 
	var answer = confirm ("Are you sure you want to delete this row?")
	if (!answer) {
	return false;
	}
}
// -->
</script>



</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Timesheets</h1><hr /></td></tr>
			
			<tr><td colspan="2" align="right"><a href="timesheets.php?Timesheet_ID=<?php echo $_SESSION['timesheet_ID'];?>&action=view&src=menu&payperiod=<?php echo $recordData['c_PayPeriodEnd'][0];?>">Return to timesheet</a></td></tr>
			
			<tr bgcolor="#a2c7ca"><td class="body"><strong>Report of Outside Employment Form</strong></td><td align="right">Timesheet Status: <?php echo $recordData['TimesheetSubmittedStatus'][0];?> | Pay Period: <strong><?php echo $recordData['PayPeriodBegin'][0];?> - <?php echo $recordData['c_PayPeriodEnd'][0];?></strong></td></tr>
			
			<tr><td colspan="2" class="body" align="center"><p class="alert_small">Report of Outside Employment form was successfully updated.</p></td></tr>
			
			
			<tr><td class="body">&nbsp;<i>NOTE: Enter "None" if not applicable.</i></td><td align="right">Timesheet ID: <?php echo $_SESSION['timesheet_ID'];?></td></tr>
			<tr><td class="body" colspan=2>
			


<!--BEGIN FIRST SECTION: STAFF INFORMATION AND SIGNATURE BOX-->


							<table cellspacing=0 cellpadding=1 border=1 bordercolor="cccccc" width="100%" class="body">
							
							<tr bgcolor="#a2c7ca"><td class="body">&nbsp;Staff Details:</td><td class="body">&nbsp;Signature:</td></tr>
							
							<tr><td class="body" valign="top">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								
								<tr><td align="right"><font color="666666">Pay Period Begin:</font></td><td><?php echo $recordData['PayPeriodBegin'][0];?></td><td><font color="666666">Pay Period End:</font> <?php echo $recordData['c_PayPeriodEnd'][0];?></td></tr>
								<tr><td align="right"><font color="666666">Name:</font></td><td colspan="2"><?php echo $_SESSION['timesheet_name'];?></td></tr>
								<tr><td align="right"><font color="666666">Title:</font></td><td colspan="2"><?php echo $_SESSION['title'];?></td></tr>
								<tr><td align="right"><font color="666666">SEDL Unit:</font></td><td colspan="2"><?php echo $_SESSION['workgroup'];?></td></tr>
								</table>
							
							</td>
							
							<td class="body" valign="middle">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								<tr><td valign="bottom">
								
								
								<?php if($recordData['RptOutsideEmplFormSigned'][0] == 1){ ?>
									<center>
									<img src="/staff/sims/signatures/<?php echo $recordData['sims_user_ID'][0];?>.png"><br>
									<?php echo $recordData['RptOutsideEmplDateSigned'][0];?>
									</center>
								<?php }?>
								
								</td><td>
								</table>
								
							</td></tr>
	
<!--END FIRST SECTION: STAFF INFORMATION AND SIGNATURE BOX-->


<!--BEGIN SECOND SECTION: CONSULTANT SERVICES RENDERED TO-->	
	
							<tr bgcolor="#a2c7ca"><td class="body" colspan="2">&nbsp;Outside Employment/Consultant Services Performed/Rendered to:</td></tr>
							
							<tr><td class="body" colspan="2">
							
								<table cellspacing="0" cellpadding="5" border="1" bordercolor="cccccc" width="100%">
								<tr><td align="center"><font color="666666">Name & Address of Employer</font></td><td align="center"><font color="666666">Employment or Type of Services Rendered</font></td><td align="center"><font color="666666">Dates</font></td><td align="center"><font color="666666">Duration of Employment or Service</font></td></tr>
								<tr><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplEmployer1'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplService1'][0];?></td><td align="right" valign="top" nowrap>From: <?php echo $recordData['RptOutsideEmplDateFrom1'][0];?><br>To: <?php echo $recordData['RptOutsideEmplDateTo1'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplTime1'][0];?></td></tr>
								<tr><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplEmployer2'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplService2'][0];?></td><td align="right" valign="top" nowrap>From: <?php echo $recordData['RptOutsideEmplDateFrom2'][0];?><br>To: <?php echo $recordData['RptOutsideEmplDateTo2'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplTime2'][0];?></td></tr>
								<tr><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplEmployer3'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplService3'][0];?></td><td align="right" valign="top" nowrap>From: <?php echo $recordData['RptOutsideEmplDateFrom3'][0];?><br>To: <?php echo $recordData['RptOutsideEmplDateTo3'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplTime3'][0];?></td></tr>
								</table>
											
							</td></tr>		
							
<!--END SECOND SECTION: CONSULTANT SERVICES RENDERED TO-->	

							<tr><td class="body" colspan="2">
							<p class="info_small"><span class="tiny">
							The above information is furnished by the undersigned in compliance with SEDL Board and Administrative Policy/Procedure 10.05. I understand that SEDL will make reasonable efforts to hold the above furnished information in confidence except where SEDL may be legally required to furnish such information to competent authority.<br>&nbsp;<br>
							I declare the foregoing to be, to the best of my knowledge and belief, a full and accurate statement of the facts. It is understood by me that any false or misleading statements shall be sufficient reason for my dismissal for cause from the services of Southwest Educational Development Laboratory (i.e., SEDL). I hereby release the Southwest Educational Development Corporation from all liability whatsoever that may issue from securing this information.
							</span>
							<center><form><input type="hidden" name="action" value="edit"><input type="hidden" name="Timesheet_ID" value="<?php echo $_SESSION['timesheet_ID'];?>"><input type="submit" name="submit" value="Edit Form"></center>
							</p>
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
exit;
//echo '<p>Report of Outside Employment form was successfully updated.';

} else {
echo 'There was an error updating your record.<br>';
echo 'UpdateError: '.$updateResult['errorCode'];

exit;
}
################################
## END: UPDATE THE FMP RECORD
################################

###############################################################################################################
## END: SAVE FORM CHANGES & DISPLAY THE REPORT OF OUTSIDE EMPLOYMENT FORM IN AN HTML TABLE IN READ-ONLY MODE
###############################################################################################################
 ?>
 </span>
 <?php
 } else {
 
 echo 'Error';
 
 }
 ?>
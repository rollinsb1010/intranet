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
$timesheet_ID = $_GET['Timesheet_ID'];
$action = $_GET['action'];
//echo '<br>Action: '.$action;
//echo '<br>Timesheet ID: '.$timesheet_ID;
###############################
## END: GRAB FORM VARIABLES
###############################

############################################################
## START: FIND OUTSIDE EMPLOYMENT FIELDS FOR THIS TIMESHEET
############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','timesheets_rpt_out_empl','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('TimesheetID',$timesheet_ID);
//$search -> AddDBParam('HrsType','WkHrsReg');
//$search -> AddDBParam('-lop','or');
//$search -> AddSortParam ($sortfield,'descend');
$searchResult = $search -> FMFind();
//echo $searchResult['errorCode'];
//echo '<br>RegularWkHrs FoundCount: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
##########################################################
## END: FIND OUTSIDE EMPLOYMENT FIELDS FOR THIS TIMESHEET
##########################################################

if ($action == 'print') {

#################################################################################################
## START: DISPLAY THE REPORT OF OUTSIDE EMPLOYMENT FORM IN READ-ONLY PRINT MODE
#################################################################################################
 
 ?>

<html>
<head>
<title>SIMS - Timesheets - Report of Outside Employment Form</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,860)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td></tr>
<tr><td>
			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
				<tr><td class="body"><img src="/staff/sims/images/logo-new-grayscale.png" width="86" height="34" alt="SEDL-Logo"><p><strong>4700 Mueller Blvd.<br>Austin, TX 78723</strong></td></strong></td></tr>
				
				<tr><td class="body" align="center"><strong>REPORT OF OUTSIDE EMPLOYMENT OR CONSULTANT SERVICES</strong></td></tr>
				
				
				<tr><td class="body">&nbsp;For the Pay Period of: <u><?php echo $recordData['PayPeriodBegin'][0];?></u> Through <u><?php echo $recordData['c_PayPeriodEnd'][0];?></u></td></tr>
				<tr><td class="body">&nbsp;Staff Member's Name: <u><?php echo $_SESSION['timesheet_name_owner'];?></u></td></tr>
				<tr><td class="body">&nbsp;Position Title: <u><?php echo $_SESSION['title_owner'];?></u></td></tr>
				<tr><td class="body">&nbsp;SEDL Unit: <u><?php echo $_SESSION['workgroup_name_owner'];?></u></td></tr>
	
				<tr><td class="body">&nbsp;Outside Employment/Consultant Services Performed/Rendered to:</td></tr>
	
				<tr><td class="body">
				
				
									<table cellspacing="0" cellpadding="5" border="1" bordercolor="cccccc" width="100%">
									<tr><td align="center" width="25%">Name & Address of Employer</td><td align="center" width="25%">Employment or Type of Services Rendered</td><td align="center" width="25%">Dates</td><td align="center" width="25%">Duration of Employment or Service</td></tr>
									<tr><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplEmployer1'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplService1'][0];?></td><td valign="top" nowrap>  <table cellpadding="0" cellspacing="0" border="0"><tr><td>From:</td><td><?php echo $recordData['RptOutsideEmplDateFrom1'][0];?></td></tr><tr><td>To:</td><td><?php echo $recordData['RptOutsideEmplDateTo1'][0];?></td></tr></table>  </td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplTime1'][0];?></td></tr>
									<tr><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplEmployer2'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplService2'][0];?></td><td valign="top" nowrap>  <table cellpadding="0" cellspacing="0" border="0"><tr><td>From:</td><td><?php echo $recordData['RptOutsideEmplDateFrom2'][0];?></td></tr><tr><td>To:</td><td><?php echo $recordData['RptOutsideEmplDateTo2'][0];?></td></tr></table>  </td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplTime2'][0];?></td></tr>
									<tr><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplEmployer3'][0];?></td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplService3'][0];?></td><td valign="top" nowrap>  <table cellpadding="0" cellspacing="0" border="0"><tr><td>From:</td><td><?php echo $recordData['RptOutsideEmplDateFrom3'][0];?></td></tr><tr><td>To:</td><td><?php echo $recordData['RptOutsideEmplDateTo3'][0];?></td></tr></table>  </td><td align="center" valign="top"><?php echo $recordData['RptOutsideEmplTime3'][0];?></td></tr>
									</table>
												
				</td></tr>		
								
	<!--END SECOND SECTION: CONSULTANT SERVICES RENDERED TO-->	
	
				<tr><td class="body">
								
					The above information is furnished by the undersigned in compliance with SEDL Board and Administrative Policy/Procedure 10.05. I understand that SEDL will make reasonable efforts to hold the above furnished information in confidence except where SEDL may be legally required to furnish such information to competent authority.<br>&nbsp;<br>
					I declare the foregoing to be, to the best of my knowledge and belief, a full and accurate statement of the facts. It is understood by me that any false or misleading statements shall be sufficient reason for my dismissal for cause from the services of Southwest Educational Development Laboratory (i.e., SEDL). I hereby release the Southwest Educational Development Corporation from all liability whatsoever that may issue from securing this information.
								
				</td></tr>
				
				<tr><td class="body">

					<table border="0" cellspacing="0" bordercolor="cccccc">
						<tr>
							<td class="body" valign="bottom">Staff Member Signature:<br>&nbsp;</td>
							<td align="center" valign="bottom"><?php if($recordData['RptOutsideEmplFormSigned'][0] == 1){ ?><img src="/staff/sims/signatures/<?php echo $recordData['sims_user_ID'][0];?>.png"><?php }?><hr size="1"></td>
							<td valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;Date Signed:<br>&nbsp;</td>
							<td valign="bottom"><u><?php echo $recordData['RptOutsideEmplDateSigned'][0];?></u><br>&nbsp;</td>
						</tr>
					</table>


				</td></tr>
							
			</table>

</td></tr>
			
</table>



</body>

</html>


 <?php
 
#################################################################################################
## END: DISPLAY THE REPORT OF OUTSIDE EMPLOYMENT FORM IN AN HTML TABLE IN READ-ONLY MODE
#################################################################################################


 } else {
 
 echo 'Error';
 
 }
 ?>
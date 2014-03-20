<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2007 by the Texas Comprehensive Center at SEDL
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
//$timesheet_ID = $_GET['Timesheet_ID'];
//$_SESSION['timesheet_ID'] = $_GET['Timesheet_ID'];
//$action = $_GET['action'];
$budget_code = $_GET['budget_code'];
$active_to = $_GET['active_to'];
//$new_nickname = $_GET['new_nickname'];
//$row_ID = $_GET['delete_row_ID'];
//$new_row = $_GET['new_row'];
//$new_row_ID = $_GET['new_row_ID'];
//$row_ID = $_GET['edit_row_ID'];
###############################
## END: GRAB FORM VARIABLES
###############################

############################################################################
## START: FIND THE CURRENTLY SELECTED BUDGET CODE DETAILS
############################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search2 -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search2 -> AddDBParam('c_BudgetCode',$budget_code);
$search2 -> AddDBParam('Active_To',$active_to);
//$search2 -> AddDBParam('-lop','or');

//$search2 -> AddSortParam ('c_BudgetCode','ascend');


$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
$searchData2 = current($searchResult2['data']);
############################################################################
## END: FIND THE CURRENTLY SELECTED BUDGET CODE DETAILS
############################################################################

#################################################################################################
## START: DISPLAY THE DETAILS FOR THIS BUDGET CODE
#################################################################################################
?>


<html>
<head>
<title>SIMS - Budget Code Status</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(500,600)">

<table width="300" cellpadding="0" cellspacing="0" border="1" bordercolor="#003745" align="center">
<tr bgcolor="#003745"><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
			
			
			<tr bgcolor="#a2c7ca"><td class="body" nowrap><strong>Budget Code Details: <?php echo $searchData2['c_BudgetCode_cwp'][0];?></strong></td></tr>
			
			
			
			<tr><td class="body" colspan=2>
										
							<table cellspacing=0 cellpadding=4 border=1 bordercolor="cccccc" width="100%" class="sims">
									<tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">Description:</td><td colspan="2"><?php echo $searchData2['BudgetCodeDescription'][0];?></td></tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">Status:</td><td nowrap colspan="2"><?php if($searchData2['c_Active_Status_cwp'][0] == 'Active'){echo $searchData2['c_Active_Status_cwp'][0];}else{echo '<font color="red"><strong>'.$searchData2['c_Active_Status_cwp'][0].'</strong></font>';}?></td></tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">Approved By:</td><td colspan="2"><?php echo $searchData2['BgtAuthorityApproving_sims_ID'][0];?></td></tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">Active From:</td><td><?php echo $searchData2['Active_From'][0];?></td><td>To: <?php echo $searchData2['Active_To'][0];?></td></tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">Comments:</td><td colspan="2"><?php echo $searchData2['Comments'][0];?></td></tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">Last Updated:</td><td colspan="2"><?php echo $searchData2['Mod_Timestamp'][0];?></td></tr>
									</tr>
									
									
							</table>
							
							

		

			
			</td></tr>
			
			
			<tr><td class="body" colspan="2">
			<p class="alert_small">
			<i>NOTE: SEDL budget codes are maintained by AS. If you feel there is a discrepancy with this code or have questions about the Active dates, contact 
			<a href="mailto:stuart.ferguson@sedl.org?subject=SIMS - Question RE: Bgt Code: <?php echo $searchData2['c_BudgetCode_cwp'][0];?>">Stuart Ferguson</a> or <a href="mailto:lori.foradory@sedl.org?subject=SIMS - Question RE: Bgt Code: <?php echo $searchData2['c_BudgetCode_cwp'][0];?>">Lori Foradory</a>.</i>
			</p>
			</td></tr>
			
			
			</table>

</td></tr>
</table>






</body>

</html>
<?php
#################################################################################################
## END: DISPLAY THE DETAILS FOR THIS BUDGET CODE
#################################################################################################
?>
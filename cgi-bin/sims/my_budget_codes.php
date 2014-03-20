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
$action = $_GET['action'];
$new_budget_code = $_GET['new_budget_code'];
$new_nickname = substr($_GET['new_nickname'],0,15);
//$active_to = $_GET['active_to'];
$row_ID = $_GET['delete_row_ID'];
//$new_row = $_GET['new_row'];
//$new_row_ID = $_GET['new_row_ID'];
//$row_ID = $_GET['edit_row_ID'];
//print_r($_SESSION['my_row_IDs']);
###############################
## END: GRAB FORM VARIABLES
###############################

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

if ($action == 'add'){ 

/*
##################################################
## START: FIND ACTIVE_TO DATE OF NEW BUDGET CODE
##################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search3 -> SetDBPassword($webPW,$webUN);
//$search3 -> FMSkipRecords($skipsize);
$search3 -> AddDBParam('c_BudgetCode_cwp',$new_budget_code);
$search3 -> AddDBParam('c_Active_Status_cwp','Active');
//$search3 -> AddDBParam('-lop','or');

//$search3 -> AddSortParam ('c_BudgetCode','ascend');


$searchResult3 = $search3 -> FMFind();

echo '<br>ActiveTo search ErrorCode: '.$searchResult3['errorCode'];
echo '<br>ActiveTo search FoundCount: '.$searchResult3['foundCount'];
$recordData3 = current($searchResult3['data']);
$active_to = $recordData3['Active_To'][0];
echo '<br>Active To: '.$active_to;
##################################################
## END: FIND ACTIVE_TO DATE OF NEW BUDGET CODE
##################################################
*/

##################################################
## START: ADD NEW BUDGET CODE TO MY BUDGET CODES
##################################################
$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('SIMS_2.fp7','budget_code_usage'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information


###ADD THE SUBMITTED VALUES AS PARAMETERS###
$newrecord -> AddDBParam('staff_ID',$_SESSION['staff_ID']);
$newrecord -> AddDBParam('budget_code',$new_budget_code);
$newrecord -> AddDBParam('Budget_Code_Nickname',$new_nickname);
//$newrecord -> AddDBParam('Budget_Code_Active_To',$active_to);

###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
$newrecordResult = $newrecord -> FMNew();

$recordData = current($newrecordResult['data']);

//echo '<br>NewBudgetCode ErrorCode: '.$newrecordResult['errorCode'];
//echo '<br>NewBudgetCode FoundCount: '.$newrecordResult['foundCount'];

//$new_row_ID = $recordData2['c_cwp_row_ID'][0];
##################################################
## END: ADD NEW BUDGET CODE TO MY BUDGET CODES
##################################################

}

if (($action == 'delete') && (in_array($row_ID, $_SESSION['my_row_IDs']))){ 

############################################################
## START: DELETE SELECTED BUDGET CODE FROM MY BUDGET CODES
############################################################
$delete = new FX($serverIP,$webCompanionPort);
$delete -> SetDBData('SIMS_2.fp7','budget_code_usage');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$row_ID);

$deleteResult = $delete -> FMDelete();
##########################################################
## END: DELETE SELECTED BUDGET CODE FROM MY BUDGET CODES
##########################################################

}

############################################################################
## START: FIND AVAILABLE BUDGET CODES FOR THIS USER
############################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','budget_code_usage','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$_SESSION['staff_ID']);
//$search -> AddDBParam('HrsType','WkHrsReg');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam ($sortfield,'descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['user_bgt_codes'] = $recordData;
$_SESSION['current_staff_ID'] = $recordData['staff_ID'][0];
############################################################################
## END: FIND AVAILABLE BUDGET CODES FOR THIS USER
############################################################################
//echo '<p>$_SESSION[staff_ID]: '.$_SESSION['staff_ID'];
//echo '<p>$_SESSION[current_staff_ID]: '.$_SESSION['current_staff_ID'];


if($action == 'print'){ 
$today = date("M d, Y");
?>


<html>
<head>
<title>SIMS - My Budget Codes</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2"><img src="/staff/sims/images/logo-new-grayscale.png" width="86" height="34" alt="SEDL-Logo"></td></tr>
		
			
			
			
			
			<tr><td class="body"><strong>Budget Codes for <?php echo $_SESSION['user_ID'];?></strong> as of <?php echo $today;?>:<p>

							
							<table cellspacing=10 cellpadding=10 width="100%" class="sims">
									<tr>
									<td class="body" nowrap>Budget Code</td>
									<td class="body" nowrap>Description</td>
									<td class="body" nowrap width="100%">Nickname</td>
									<td class="body" nowrap>Approved By</td>
									<td class="body" nowrap>Active From</td>
									<td class="body" nowrap>Active To</td>
									<td class="body" nowrap>Status</td>
									<td class="body" nowrap>Comments</td>
									</tr>
									
									<?php 
									$i = 0;
									foreach($searchResult['data'] as $key => $searchData) { 
									$my_row_IDs[$i] = $searchData['c_cwp_row_ID'][0]; 
									$i++;
																		
									?>
									
									<tr>
									<td class="body" nowrap><?php echo $searchData['budget_code'][0];?></td>
									<td class="body" nowrap><?php echo $searchData['budget_codes::BudgetCodeDescription'][0];?></td>
									<td class="body" nowrap width="100%"><?php echo stripslashes($searchData['Budget_Code_Nickname'][0]);?></td>
									<td class="body" nowrap><?php echo $searchData['Bgt_Auth_Approving'][0];?></td>
									<td class="body" nowrap><?php echo $searchData['budget_codes::Active_From'][0];?></td>
									<td class="body" nowrap><?php echo $searchData['budget_codes::Active_To'][0];?></td>
									<td class="body" nowrap><?php echo $searchData['budget_codes::c_Active_Status_cwp'][0];?></td>
									<td class="body" nowrap><?php echo stripslashes($searchData['budget_codes::Comments'][0]);?></td>

									</tr>
									
									
									<?php  } 
									$_SESSION['my_row_IDs'] = $my_row_IDs;
									?>
								
																
							
							</table>
							
							

		

			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>






</body>

</html>

<?php
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



############################################################################
## START: FIND ACTIVE BUDGET CODES TO POPULATE SELECT LIST
############################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search2 -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search2 -> AddDBParam('c_Active_Status_cwp','Active');
//$search2 -> AddDBParam('-lop','or');

$search2 -> AddSortParam ('c_BudgetCode','ascend');


$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);
############################################################################
## END: FIND ACTIVE BUDGET CODES TO POPULATE SELECT LIST
############################################################################

$staff_ID = $_SESSION['staff_ID']; //change this to a session variable that gets set upon login

#################################################################################################
## START: DISPLAY THIS USER'S AVAILABLE BUDGET CODES
#################################################################################################
//onLoad="resizeTo(700,580)"
?>


<html>
<head>
<title>SIMS - My Budget Codes</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">
//<!--
function confirmDelete() { 
	var answer = confirm ("Are you sure you want to delete this budget code from your list?")
	if (!answer) {
	return false;
	}
}
// -->
</script>
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="500" cellpadding="0" cellspacing="0" border="0" align="center">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>My Budget Codes</h1><hr /></td></tr>
			
			<tr bgcolor="#a2c7ca"><td class="body"><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right">&nbsp;</td></tr>
			
			
			
			<tr><td class="body" colspan="2">&nbsp;<i>NOTE: Enter any budget codes you charge to in this area. Budget codes entered here will be available to you when entering time on your timesheet. If one of your budget codes is not available on the list,
			contact <a href="mailto:lori.foradory@sedl.org?subject=SIMS: Budget Code Request">Lori Foradory</a>.</i></td></tr>
			<tr><td class="body" colspan=2><a href="my_budget_codes.php?action=print" target="_blank">Print-friendly</a><br>
			<form name="budget_codes">
			<input type="hidden" name="timesheet_ID" value="<?php echo $timesheet_ID;?>">
			<input type="hidden" name="action" value="add">
			<input type="hidden" name="edit_row_ID" value="<?php echo $row_ID;?>">
			<input type="hidden" name="days_in_month" value="<?php echo $days_in_month;?>">






							
							<table cellspacing=0 cellpadding=4 width="100%" class="sims">
									<tr bgcolor="#a2c7ca">
									<td class="body" nowrap>Budget Code</td>
									<td class="body" nowrap>Approved By</td>
									<td class="body" nowrap>Status</td>
									<td class="body" nowrap width="100%">Nickname</td>
									<td class="body" nowrap>Delete</td>									
									</tr>
									
									<?php 
									$i = 0;
									foreach($searchResult['data'] as $key => $searchData) { 
									$my_row_IDs[$i] = $searchData['c_cwp_row_ID'][0]; 
									$i++;
																		
									?>
									
									<tr>
									<td class="body" nowrap><?php echo $searchData['budget_code'][0];?></td>
									<td class="body" nowrap><?php echo $searchData['Bgt_Auth_Approving'][0];?></td>
									<td class="body" nowrap><a href="/staff/sims/budget_code_status.php?budget_code=<?php echo $searchData['budget_code'][0];?>&active_to=<?php echo $searchData['budget_codes::Active_To'][0];?>" target="_blank"><?php echo $searchData['budget_codes::c_Active_Status_cwp'][0];?></a></td>
									<td class="body" nowrap width="100%"><?php echo stripslashes($searchData['Budget_Code_Nickname'][0]);?></td>
									<td class="body" nowrap><a href="/staff/sims/my_budget_codes.php?action=delete&delete_row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/trashcan.jpg" border="0"></a></td>
									</tr>
									
									
									<?php  } 
									$_SESSION['my_row_IDs'] = $my_row_IDs;
									?>
								
									<tr>
									<td class="body" nowrap colspan="5">
									
											
													 <p class="info_small">
													 <table cellpadding="0" cellspacing="0" width="100%" class="sims2"><tr><td colspan="2">
													 	<strong>Add a new budget code to your "My Budget Codes" list</strong><p></td></tr>
													 	<tr><td nowrap>
														Budget Code:</td><td>  
														<select name="new_budget_code" class="body">
														<option value="choose"></option>
														
														<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
														<option value="<?php echo $searchData2['c_BudgetCode_cwp'][0];?>"> <?php echo $searchData2['c_BudgetCode_cwp'][0].' ('.$searchData2['BudgetCodeDescription'][0].')'; ?></option>
														<?php } ?>
														</select></td></tr>
														<tr><td nowrap>
														Nickname:</td><td> <input type="text" name="new_nickname" size="25"><em>(15 character limit)</em></td></tr>
														<tr><td>&nbsp;</td><td>
														<input type="submit" name="submit" value="Add">
													 </td></tr></table>
													 </p>
											
											
									</td>
									</tr>
																
							
							</table>
							
							

		

			</form>
			</td></tr>
			<tr><td colspan="2" align="right">
			
			
			
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>






</body>

</html>


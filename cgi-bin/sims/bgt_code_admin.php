<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2008 by SEDL
#
# Written by Eric Waters 03/12/2008
#############################################################################

#################################
## START: LOAD FX.PHP INCLUDES ##
#################################
include_once('FX/FX.php');
include_once('FX/server_data.php');
###############################
## END: LOAD FX.PHP INCLUDES ##
###############################

################################
## START: GRAB FORM VARIABLES ##
################################
$action = $_GET['action'];
//exit;
##############################
## END: GRAB FORM VARIABLES ##
##############################

if($action == 'new'){


############################################################
## START: GRAB PBA USERIDs TO POPULATE PBA DROP-DOWN LIST ##
############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff');
$search2 -> SetDBPassword($webPW,$webUN);
//$search2 -> AddDBParam('current_employee_status','SEDL Employee');
$search2 -> AddDBParam('is_budget_authority','Yes');

$search2 -> AddSortParam('sims_user_ID','ascend');

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);
##########################################################
## END: GRAB PBA USERIDs TO POPULATE PBA DROP-DOWN LIST ##
##########################################################

#########################################
## START: DISPLAY NEW BUDGET CODE FORM ## 
#########################################
?>


<html>
<head>
<title>SIMS - Budget Code Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Admin</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>New Budget Code</strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode">Show All</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: This new budget code must be saved by clicking the Create Budget Code button.</p></td></tr>

			<tr><td class="body" colspan=2>


							<table cellspacing="0" cellpadding="1" width="100%" border="1" bordercolor="#cccccc">
							
							<form name="new_budget_code">
							<input type="hidden" name="action" value="new_submit">
							<input type="hidden" name="last_mod_by" value="<?php echo $_SESSION['user_ID'];?>">

							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;Budget Code Details</td></tr>
							
							<tr><td class="body" valign="top" width="100%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								<tr><td valign="top">

											<table cellspacing="0" cellpadding="5" border="0" width="100%">
											<tr valign="bottom"><td align="right"><font color="666666">Budget Code:</font></td><td>
											
												<table cellpadding="0" cellspacing="0" border="0"><tr>
													<td><font color="666666"><span class="tiny">FUND</span></font><br><input type="text" name="fund" size="5"></td>
													<td><font color="666666"><span class="tiny">YEAR</span></font><br><input type="text" name="year" size="3"></td>
													<td><font color="666666"><span class="tiny">ORGCODE</span></font><br><input type="text" name="org_code" size="8"></td></tr>
												</table>
											
											</td></tr>

											<tr valign="bottom"><td align="right"><font color="666666">Description:</font></td><td><input type="text" name="description" size="50"></td></tr>

											<tr valign="bottom"><td align="right" nowrap><font color="666666" nowrap>Active From:</font></td><td><input type="text" name="active_from" size="30"> <font color="666666"><span class="tiny">(Format: MM/DD/YYYY)</span></font></td></tr>
											<tr valign="bottom"><td align="right" nowrap><font color="666666" nowrap>Active To:</font></td><td><input type="text" name="active_to" size="30"> <font color="666666"><span class="tiny">(Format: MM/DD/YYYY)</span></font></td></tr>
											
											<tr valign="middle"><td align="right"><font color="666666">Approved By:</font></td><td>
											
											
												<select name="approved_by" class="body">
												<option value="choose">
												
												<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
												<option value="<?php echo $searchData2['staff_ID'][0];?>"> <?php echo $searchData2['sims_user_ID'][0];?>
												<?php } ?>
												</select>
											
											
											
											</td></tr>
											
											<tr valign="bottom"><td align="right"><font color="666666">Comments:</font></td><td><input type="text" name="comments" size="50"></td></tr>
			
											
											
											</table>


								</td></tr>
								</table>
							
							</td></tr>
							

							<tr><td class="body" colspan="2">
							<center><input type="submit" name="submit" value="Create Budget Code"></center>
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
#######################################
## END: DISPLAY NEW BUDGET CODE FORM ## 
#######################################
 
} elseif ($action == 'show_all') {

$delete = $_GET['delete'];
$sort_by = $_GET['sort_by'];
$pref = $_GET['pref'];
$delete_ID = $_GET['row_ID'];

####################################################
## START: DELETE CURRENT BUDGET CODE IF REQUESTED ##
####################################################
if($delete == 'yes'){
$_SESSION['budget_code_deleted'] = 'yes';
$delete = new FX($serverIP,$webCompanionPort);
$delete -> SetDBData('SIMS_2.fp7','budget_codes');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$delete_ID);

$deleteResult = $delete -> FMDelete();
}
##################################################
## END: DELETE CURRENT BUDGET CODE IF REQUESTED ##
##################################################

######################################
## START: GRAB CURRENT BUDGET CODES ##
######################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> AddDBParam('current_employee_status','SEDL Employee');
if($pref == 'active_only'){
$_SESSION['budget_code_view_pref'] = 'active_only';
$search -> AddDBParam('c_Active_Status','Active');
}
if($pref == 'all_codes'){
//$search -> AddDBParam('c_Active_Status','Active');
$_SESSION['budget_code_view_pref'] = 'all_codes';
}


if(($sort_by == 'Active_From')||($sort_by == 'Active_To')){
$search -> AddSortParam($sort_by,'descend');
}else{
$search -> AddSortParam($sort_by,'ascend');
}
$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
####################################
## END: GRAB CURRENT BUDGET CODES ##
####################################

#####################################
## START: DISPLAY BUDGET CODE LIST ##
#####################################
 
 ?>

<html>
<head>
<title>SIMS - Budget Code Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Admin</h1><hr /></td></tr>
			
<?php if($_SESSION['budget_code_deleted'] == 'yes'){ ?>
			<tr><td class="body" colspan="2"><p class="alert_small">Budget code successfully deleted from SIMS.</p></td></tr>
<?php $_SESSION['budget_code_deleted'] = ''; }?>

			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL Budget Codes</strong> | <?php echo $searchResult['foundCount'];?> records found.</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="bgt_code_admin.php?action=print_friendly&sort_by=<?php echo $sort_by;?>&pref=<?php echo $_SESSION['budget_code_view_pref'];?>" target="_blank">Print</a> | <a href="bgt_code_admin.php?action=new">New Budget Code</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2><?php if($_SESSION['budget_code_view_pref'] == 'all_codes'){ ?><a href="bgt_code_admin.php?action=show_all&pref=active_only">Show active codes only</a><?php }elseif($_SESSION['budget_code_view_pref'] == 'active_only'){ ?><a href="bgt_code_admin.php?action=show_all&pref=all_codes">Show all codes</a><?php } ?><br>



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode&pref=<?php echo $_SESSION['budget_code_view_pref'];?>">Budget Code</a></td><td class="body"><a href="bgt_code_admin.php?action=show_all&sort_by=BudgetCodeDescription&pref=<?php echo $_SESSION['budget_code_view_pref'];?>">Description</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=Active_From&pref=<?php echo $_SESSION['budget_code_view_pref'];?>">Active From</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=Active_To&pref=<?php echo $_SESSION['budget_code_view_pref'];?>">Active To</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=c_Active_Status&pref=<?php echo $_SESSION['budget_code_view_pref'];?>">Status</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=BgtAuthorityApproving_sims_ID&pref=<?php echo $_SESSION['budget_code_view_pref'];?>">Approved By</a></td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body" nowrap valign="top"><a href="bgt_code_admin.php?action=show_1&budget_code=<?php echo $searchData['c_BudgetCode'][0];?>"><?php echo $searchData['c_BudgetCode'][0];?></a></td><td class="body" valign="top"><?php echo $searchData['BudgetCodeDescription'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_From'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_To'][0];?></td><td class="body" nowrap valign="top"><font <?php if($searchData['c_Active_Status'][0] != 'Active'){?>color="red"<?php }?>><?php echo $searchData['c_Active_Status'][0];?></font></td><td class="body" valign="top"><?php echo $searchData['BgtAuthorityApproving_sims_ID'][0];?></td></tr>
								<?php } ?>


							
							</table>
			
			</td></tr>
			
			
			</table>

</td></tr>
</table>







</body>

</html>

 <?php
 
###################################
## END: DISPLAY BUDGET CODE LIST ##
###################################

} elseif ($action == 'print_friendly') {

$sort_by = $_GET['sort_by'];
$pref = $_GET['pref'];
######################################
## START: GRAB CURRENT BUDGET CODES ##
######################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> AddDBParam('current_employee_status','SEDL Employee');
if($pref == 'active_only'){
$_SESSION['budget_code_view_pref'] = 'active_only';
$search -> AddDBParam('c_Active_Status','Active');
}
if($pref == 'all_codes'){
//$search -> AddDBParam('c_Active_Status','Active');
$_SESSION['budget_code_view_pref'] = 'all_codes';
}


if(($sort_by == 'Active_From')||($sort_by == 'Active_To')){
$search -> AddSortParam($sort_by,'descend');
}else{
$search -> AddSortParam($sort_by,'ascend');
}
$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
####################################
## END: GRAB CURRENT BUDGET CODES ##
####################################
 $today = date("M d, Y");
#####################################
## START: DISPLAY BUDGET CODE LIST ##
#####################################
 
 ?>

<html>
<head>
<title>SIMS - Budget Code Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1200,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td><img src="/staff/sims/images/logo-new-grayscale.png" width="86" height="34" alt="SEDL-Logo"></td></tr>
		
			
			<tr><td class="body"><strong>SEDL Budget Codes</strong> as of: <?php echo $today;?></td></tr>
			
			
			
			<tr><td class="body">



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr><td class="body" nowrap>Budget Code</td><td class="body">Description</td><td class="body" nowrap>Active From</td><td class="body" nowrap>Active To</td><td class="body" nowrap>Status</td><td class="body" nowrap>Approved By</td><td class="body">Comments</td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body" nowrap valign="top"><?php echo $searchData['c_BudgetCode'][0];?></td><td class="body" valign="top" nowrap><?php echo $searchData['BudgetCodeDescription'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_From'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_To'][0];?></td><td class="body" nowrap valign="top"><?php echo $searchData['c_Active_Status'][0];?></td><td class="body" valign="top"><?php echo $searchData['BgtAuthorityApproving_sims_ID'][0];?></td><td class="body" valign="top" nowrap><?php echo $searchData['Comments'][0];?></td></tr>
								<?php } ?>


							
							</table>
			
			</td></tr>
			
			
			</table>

</td></tr>
</table>







</body>

</html>

 <?php
 
###################################
## END: DISPLAY BUDGET CODE LIST ##
###################################



} elseif ($action == 'show_1') { 

$budget_code = $_GET['budget_code'];

############################################################
## START: GRAB PBA USERIDs TO POPULATE PBA DROP-DOWN LIST ##
############################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff');
$search2 -> SetDBPassword($webPW,$webUN);
//$search2 -> AddDBParam('current_employee_status','SEDL Employee');
$search2 -> AddDBParam('is_budget_authority','Yes');

$search2 -> AddSortParam('sims_user_ID','ascend');

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);
##########################################################
## END: GRAB PBA USERIDs TO POPULATE PBA DROP-DOWN LIST ##
##########################################################

#################################
## START: FIND BUDGET CODE RECORD ##
#################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','budget_codes');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('c_BudgetCode',$budget_code);

$searchResult4 = $search4 -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData4 = current($searchResult4['data']);
###############################
## END: FIND BUDGET CODE RECORD ##
###############################


####################################
## START: DISPLAY BUDGET CODE RECORD ## 
####################################
?>


<html>
<head>
<title>SIMS - Budget Code Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">

function confirmDelete() { 
	var answer = confirm ("Are you sure you want to delete this budget code from SIMS?")
	if (!answer) {
	return false;
	}
}

</script>

</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(1000,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Admin</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>Budget Code: <?php echo $recordData4['c_BudgetCode'][0];?></strong> (<?php echo $recordData4['c_Active_Status'][0];?>)</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode">Show All</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: Any changes made to this record must be saved by clicking the Update Budget Code button. | Last updated: <?php echo $recordData4['last_mod_timestamp'][0];?> by <?php echo $recordData4['last_mod_by'][0];?></p></td></tr>

			<tr><td class="body" colspan=2>


							<table cellspacing="0" cellpadding="1" width="100%" border="1" bordercolor="#cccccc">
							
							<form name="new_employee">
							<input type="hidden" name="action" value="update">
							<input type="hidden" name="update_row_ID" value="<?php echo $recordData4['c_cwp_row_ID'][0];?>">
							<input type="hidden" name="last_mod_by" value="<?php echo $_SESSION['user_ID'];?>">

							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;Budget Code Details | <a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode&delete=yes&row_ID=<?php echo $recordData4['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()">Delete</a></td></tr>
							
							<tr><td class="body" valign="top" width="100%">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">
								<tr><td valign="top">

											<table cellspacing="0" cellpadding="5" border="0" width="100%">
											<tr valign="bottom"><td align="right"><font color="666666">Budget Code:</font></td><td>
											
												<table cellpadding="0" cellspacing="0" border="0">
													<td><font color="666666"><span class="tiny">FUND</span></font><br><input type="text" name="fund" size="5" value="<?php echo $recordData4['Fund'][0];?>"></td>
													<td><font color="666666"><span class="tiny">YEAR</span></font><br><input type="text" name="year" size="3" value="<?php echo $recordData4['Year'][0];?>"></td>
													<td><font color="666666"><span class="tiny">ORGCODE</span></font><br><input type="text" name="org_code" size="8" value="<?php echo $recordData4['OrganCode'][0];?>"></td></tr>
												</table>
											
											</td></tr>

											<tr valign="bottom"><td align="right"><font color="666666">Description:</font></td><td><input type="text" name="description" size="50" value="<?php echo $recordData4['BudgetCodeDescription'][0];?>"></td></tr>

											<tr valign="bottom"><td align="right" nowrap><font color="666666" nowrap>Active From:</font></td><td><input type="text" name="active_from" size="30" value="<?php echo $recordData4['Active_From'][0];?>"> <font color="666666"><span class="tiny">(Format: MM/DD/YYYY)</span></font></td></tr>
											<tr valign="bottom"><td align="right" nowrap><font color="666666" nowrap>Active To:</font></td><td><input type="text" name="active_to" size="30" value="<?php echo $recordData4['Active_To'][0];?>"> <font color="666666"><span class="tiny">(Format: MM/DD/YYYY)</span></font></td></tr>
											
											<tr valign="middle"><td align="right"><font color="666666">Approved By:</font></td><td>
											
											
												<select name="approved_by" class="body">
												<option value="">
												
												<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
												<option value="<?php echo $searchData2['staff_ID'][0];?>" <?php if($recordData4['BgtAuthorityApproving_sims_ID'][0] == $searchData2['sims_user_ID'][0]){echo 'SELECTED';}?>> <?php echo $searchData2['sims_user_ID'][0];?>
												<?php } ?>
												</select>
											
											
											
											</td></tr>
											
											<tr valign="bottom"><td align="right"><font color="666666">Comments:</font></td><td><input type="text" name="comments" size="50" value="<?php echo $recordData4['Comments'][0];?>"></td></tr>
			
											
											
											</table>


								</td></tr>
								</table>
							
							</td></tr>
							

							<tr><td class="body" colspan="2">
							<center><input type="submit" name="submit" value="Update Budget Code"></center>
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
##################################
## END: DISPLAY BUDGET CODE RECORD ## 
##################################
 

 } elseif ($action == 'update') {

#######################################
## START: GRAB UPDATE FORM VARIABLES ##
#######################################
$update_row_ID = $_GET['update_row_ID'];
$fund = $_GET['fund'];
$year = $_GET['year'];
$org_code = $_GET['org_code'];
$description = $_GET['description'];
$active_from = $_GET['active_from'];
$active_to = $_GET['active_to'];
$approved_by = $_GET['approved_by'];
$comments = $_GET['comments'];
$last_mod_by = $_GET['last_mod_by'];
#####################################
## END: GRAB UPDATE FORM VARIABLES ##
#####################################

##################################
## START: UPDATE THE FMP RECORD ##
##################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','budget_codes');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row_ID);

$update -> AddDBParam('Fund',$fund);
$update -> AddDBParam('Year',$year);
$update -> AddDBParam('OrganCode',$org_code);
$update -> AddDBParam('BudgetCodeDescription',$description);
$update -> AddDBParam('Active_From',$active_from);
$update -> AddDBParam('Active_To',$active_to);
$update -> AddDBParam('BgtAuthorityApproving_staff_ID',$approved_by);
$update -> AddDBParam('Comments',$comments);
$update -> AddDBParam('last_mod_by',$last_mod_by);

$updateResult = $update -> FMEdit();

echo '<p>$updateResult[errorCode]: '.$updateResult['errorCode'];
if($updateResult['errorCode'] == '0'){
$confirm_update = '1';
}
################################
## END: UPDATE THE FMP RECORD ##
################################

$sort_by = 'c_BudgetCode';
$pref = $_SESSION['budget_code_view_pref'];
######################################
## START: GRAB CURRENT BUDGET CODES ##
######################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> AddDBParam('current_employee_status','SEDL Employee');
if($pref == 'active_only'){
$_SESSION['budget_code_view_pref'] = 'active_only';
$search -> AddDBParam('c_Active_Status','Active');
}
if($pref == 'all_codes'){
//$search -> AddDBParam('c_Active_Status','Active');
$_SESSION['budget_code_view_pref'] = 'all_codes';
}


if(($sort_by == 'Active_From')||($sort_by == 'Active_To')){
$search -> AddSortParam($sort_by,'descend');
}else{
$search -> AddSortParam($sort_by,'ascend');
}
$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
####################################
## END: GRAB CURRENT BUDGET CODES ##
####################################


#####################################
## START: DISPLAY BUDGET CODE LIST ##
#####################################
 
 ?>

<html>
<head>
<title>SIMS - Budget Code Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Admin</h1><hr /></td></tr>
			
			<?php if($confirm_update == '1'){ ?>

			<tr><td colspan="2"><p class="alert_small">Record successfully updated.</p></td></tr>
			
			<?php $confirm_update = '0';
			} ?>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL Budget Codes</strong> | <?php echo $searchResult['foundCount'];?> records found.</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="bgt_code_admin.php?action=print_friendly&sort_by=<?php echo $sort_by;?>&pref=<?php echo $_SESSION['budget_code_view_pref'];?>" target="_blank">Print</a> | <a href="bgt_code_admin.php?action=new">New Budget Code</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode">Budget Code</a></td><td class="body"><a href="bgt_code_admin.php?action=show_all&sort_by=BudgetCodeDescription">Description</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=Active_From">Active From</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=Active_To">Active To</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=c_Active_Status">Status</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=BgtAuthorityApproving_sims_ID">Approved By</a></td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body" nowrap valign="top"><a href="bgt_code_admin.php?action=show_1&budget_code=<?php echo $searchData['c_BudgetCode'][0];?>"><?php echo $searchData['c_BudgetCode'][0];?></a></td><td class="body" valign="top"><?php echo $searchData['BudgetCodeDescription'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_From'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_To'][0];?></td><td class="body" nowrap valign="top"><font <?php if($searchData['c_Active_Status'][0] != 'Active'){?>color="red"<?php }?>><?php echo $searchData['c_Active_Status'][0];?></font></td><td class="body" valign="top"><?php echo $searchData['BgtAuthorityApproving_sims_ID'][0];?></td></tr>
								<?php } ?>


							
							</table>
			
			</td></tr>
			
			
			</table>

</td></tr>
</table>







</body>

</html>
 
<?php
###################################
## END: DISPLAY BUDGET CODE LIST ##
###################################
?>

 <?php } elseif ($action == 'new_submit') {

###########################################
## START: GRAB NEW RECORD FORM VARIABLES ##
###########################################
$fund = $_GET['fund'];
$year = $_GET['year'];
$org_code = $_GET['org_code'];
$description = $_GET['description'];
$active_from = $_GET['active_from'];
$active_to = $_GET['active_to'];
$approved_by = $_GET['approved_by'];
$comments = $_GET['comments'];
$last_mod_by = $_GET['last_mod_by'];
#########################################
## END: GRAB NEW RECORD FORM VARIABLES ##
#########################################

##################################
## START: UPDATE THE FMP RECORD ##
##################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','budget_codes');
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('Fund',$fund);
$newrecord -> AddDBParam('Year',$year);
$newrecord -> AddDBParam('OrganCode',$org_code);
$newrecord -> AddDBParam('BudgetCodeDescription',$description);
$newrecord -> AddDBParam('Active_From',$active_from);
$newrecord -> AddDBParam('Active_To',$active_to);
$newrecord -> AddDBParam('BgtAuthorityApproving_staff_ID',$approved_by);
$newrecord -> AddDBParam('Comments',$comments);
$newrecord -> AddDBParam('last_mod_by',$last_mod_by);

$newrecordResult = $newrecord -> FMNew();

//echo '<p>$updateResult[errorCode]: '.$updateResult['errorCode'];
if($newrecordResult['errorCode'] == '0'){
$confirm_new = '1';
}
################################
## END: UPDATE THE FMP RECORD ##
################################

$sort_by = 'c_BudgetCode';
$pref = $_SESSION['budget_code_view_pref'];
######################################
## START: GRAB CURRENT BUDGET CODES ##
######################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','budget_codes','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> AddDBParam('current_employee_status','SEDL Employee');
if($pref == 'active_only'){
$_SESSION['budget_code_view_pref'] = 'active_only';
$search -> AddDBParam('c_Active_Status','Active');
}
if($pref == 'all_codes'){
//$search -> AddDBParam('c_Active_Status','Active');
$_SESSION['budget_code_view_pref'] = 'all_codes';
}


if(($sort_by == 'Active_From')||($sort_by == 'Active_To')){
$search -> AddSortParam($sort_by,'descend');
}else{
$search -> AddSortParam($sort_by,'ascend');
}
$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
####################################
## END: GRAB CURRENT BUDGET CODES ##
####################################


#####################################
## START: DISPLAY BUDGET CODE LIST ##
#####################################
 
 ?>

<html>
<head>
<title>SIMS - Budget Code Admin</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Budget Code Admin</h1><hr /></td></tr>
			
			<?php if($confirm_update == '1'){ ?>

			<tr><td colspan="2"><p class="alert_small">Record successfully updated.</p></td></tr>
			
			<?php $confirm_update = '0';
			} ?>

			<?php if($confirm_new == '1'){ ?>

			<tr><td colspan="2"><p class="alert_small">Record successfully created.</p></td></tr>
			
			<?php $confirm_new = '0';
			} ?>

			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL Budget Codes</strong> | <?php echo $searchResult['foundCount'];?> records found.</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="bgt_code_admin.php?action=print_friendly&sort_by=<?php echo $sort_by;?>&pref=<?php echo $_SESSION['budget_code_view_pref'];?>" target="_blank">Print</a> | <a href="bgt_code_admin.php?action=new">New Budget Code</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>



							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=c_BudgetCode">Budget Code</a></td><td class="body"><a href="bgt_code_admin.php?action=show_all&sort_by=BudgetCodeDescription">Description</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=Active_From">Active From</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=Active_To">Active To</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=c_Active_Status">Status</a></td><td class="body" nowrap><a href="bgt_code_admin.php?action=show_all&sort_by=BgtAuthorityApproving_sims_ID">Approved By</a></td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body" nowrap valign="top"><a href="bgt_code_admin.php?action=show_1&budget_code=<?php echo $searchData['c_BudgetCode'][0];?>"><?php echo $searchData['c_BudgetCode'][0];?></a></td><td class="body" valign="top"><?php echo $searchData['BudgetCodeDescription'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_From'][0];?></td><td class="body" valign="top"><?php echo $searchData['Active_To'][0];?></td><td class="body" nowrap valign="top"><font <?php if($searchData['c_Active_Status'][0] != 'Active'){?>color="red"<?php }?>><?php echo $searchData['c_Active_Status'][0];?></font></td><td class="body" valign="top"><?php echo $searchData['BgtAuthorityApproving_sims_ID'][0];?></td></tr>
								<?php } ?>


							
							</table>
			
			</td></tr>
			
			
			</table>

</td></tr>
</table>







</body>

</html>
 
<?php
###################################
## END: DISPLAY BUDGET CODE LIST ##
###################################


 
 
 } else {
 
 echo 'Error';
 
 }
 ?>
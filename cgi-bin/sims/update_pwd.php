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

#####################################################
## START: FIND CONTACT RECORD FOR THIS USER
#####################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('staff_ID','=='.$_SESSION ['staff_ID']);

$searchResult = $search -> FMFind();
//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r($search);
$recordData = current($searchResult['data']);
$update_row = $recordData['c_cwp_row_ID'][0];
$rand_num = rand();
#####################################################
## END: FIND CONTACT RECORD FOR THIS USER
#####################################################

$_SESSION['user_id'] = $recordData['sims_user_ID'][0];
$_SESSION['timesheet_name'] = $recordData['name_timesheet'][0];
$_SESSION['leave_requests_access'] = $recordData['cwp_sims_access_leave_requests'][0];

?> <!--END: LOGIN AND BEGIN SESSION-->



<!--###DISPLAY THE MAIN MENU IF LOGIN IS VALID###-->


<?php
if ($searchResult['foundCount'] == 1) { 


if ($recordData['cwp_sims_access_main_menu'][0] != 'Yes') { 
echo 'Your account does not have access to SIMS. Please contact <a href="mailto:tracy.hoes@sedl.org">Tracy Hoes</a> in Administrative Services for more information.<p>
<a href="http://www.sedl.org/staff"><< Return to SEDL Intranet</a>
';
exit;
}
/*
foreach($searchResult['data'] as $key => $searchData);
$recordDetail = explode('.',$key);
$current_recid = $recordDetail[0];

$rand_num = rand();

//echo $current_recid;

//###TRIGGER THE TIMESTAMP TO UPDATE IN FMP###

$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS.fp7','staff_profile_detail');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_recid);
$update -> AddDBParam('txccNet_webtrigger', $rand_num);

$updateResult = $update -> FMEdit();

//echo $updateResult['errorCode'];



//###ADD THIS CWP LOGIN EVENT TO THE FMP USER LOG###

$login = new FX($serverIP,$webCompanionPort);
$login -> SetDBData('SIMS.fp7','db_access_log');
$login -> SetDBPassword($webPW,$webUN);
$login -> AddDBParam('session_type', 'cwp');
$login -> AddDBParam('user_target', 'sims');
$login -> AddDBParam('cwp_username', $_SESSION['user_id']);

$loginResult = $login -> FMNew();

//$loginData = current($newrecordResult['data']);


foreach($loginResult['data'] as $key => $loginData);
$loginDetail = explode('.',$key);
$login_recid = $loginDetail[0];

$_SESSION['login_recid'] = $login_recid;

//echo $loginResult['errorCode'];



//echo $_SESSION['esc_region'];

*/

?>


<html>
<head>
<title>SIMS: Main Menu</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">
function checkFields() { 

	// Password1
		if (document.update_pwd.pwd.value ==""){
			alert("Please enter your new password in both fields.");
			document.update_pwd.pwd.focus();
			return false;	}

	// Password2
		if (document.update_pwd.pwd2.value ==""){
			alert("Please enter your new password in both fields.");
			document.update_pwd.pwd2.focus();
			return false;	}

	// Password Check
		if (document.update_pwd.pwd.value != document.update_pwd.pwd2.value){
			alert("Passwords do not match. Please re-enter your password in both fields.");
			document.update_pwd.pwd.focus();
			return false;	}

}

</script>


</head>

<body bgcolor="FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<form name="update_pwd" method="post" action="sims_menu.php" onsubmit="return checkFields()">
<input type="hidden" name="staff_update_ID" value="<?php echo $update_row;?>">
<input type="hidden" name="pwd_update" value="1">

<table cellpadding=5 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff" width="100%">
	<tr><td>
	
	<table border=0 bordercolor="ccccae" cellpadding=4 cellspacing=0 bgcolor="#ffffff" width="755" class="body">
	
		<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
		<tr><td height="33" colspan="2" scope="row"><h1>SIMS Main Menu</h1><hr /></td></tr>
		
		<tr><td class="body"><h2>Current User Information</h2></td><td align="right"><a href="sims_menu.php?src=logout">Log out</a></td></tr>
		
		
		<tr>
			<td valign="top" bgcolor="#a2c7ca">
				&nbsp;&nbsp;<b>Name</b>: <?php echo $recordData['c_full_name_last_first'][0];?><p>
				&nbsp;&nbsp;<b>Dept.</b>: <?php echo $recordData['primary_SEDL_workgroup'][0];?>
			</td>

			<td align="right"  valign="top" bgcolor="#a2c7ca">
			<b>SIMS ID</b>: <?php echo $recordData['staff_ID'][0];?>&nbsp;&nbsp;<p>
			<b>Last Login</b>: <?php echo $recordData['last_login_timestamp'][0];?>&nbsp;&nbsp;
			</td>
		</tr>
		
				
		<tr><td colspan=2>
			<center>
			<table width="100%" bgcolor="#ffffff" cellspacing=0 cellpadding=4 border="0" bordercolor="#ffffff" valign="top">
			
			
			<tr><td valign="top" width="100%">
				
					<table cellpadding=4 cellspacing=0 width="100%" valign="top" class="sims">
					
						<tr><td bgcolor="#003745" class="body" nowrap><span class="head2">Update SIMS Password</span></td></tr>
						
						<tr><td class="body" valign="top" nowrap>
						
						
								<table cellspacing="0" cellpadding="4" border="0">
								<tr><td align="right"><font face="verdana, helvetica, arial" color="999999"><strong>Enter new password:</strong></font></td><td><input type="password" name="pwd" size="10"></td></tr>
								<tr><td align="right"><font face="verdana, helvetica, arial" color="999999"><strong>Confirm new password:</strong></font></td><td><input type="password" name="pwd2" size="10"></td></tr>
								<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Submit"></td></tr>
								</table>	
							
								
						</td></tr>
						
					</table>
					
					
					</td></tr>
				
				
				</table></center>
				
		</td></tr>
		<tr><td>
		<tr><td colspan=2 bgcolor="ffffff">&nbsp;</td></tr>
		
		
		
		</td></tr>
	</table>
		
	</td></tr>
</table>
</form>

</body>

</html>

<?php 
################################
## START: SET LOGIN TIMESTAMP ##
################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','staff');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row);
$update -> AddDBParam('last_login_trigger',$rand_num);

$updateResult = $update -> FMEdit();
##############################
## END: SET LOGIN TIMESTAMP ##
##############################

?>

<?php
} else {
?>

<!--###DISPLAY MESSAGE IF NO RECORDS FOUND###-->


<html>
<head>
<title>Invalid Login</title>
<link href="../txcc.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="EBEBEB">

<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">

		
		<tr><td>&nbsp;</td</tr>
		<tr><td align="center" class="body"><font face="verdana, helvetica, arial">Not a valid login. | <a href="<?php echo $_SESSION['login_url'] ?>">Try Again</a><p>&nbsp;<br>&nbsp;<br>&nbsp;</font></td></tr>
		
		

</table>


</body>

</html>


<?php 
session_destroy();

} ?>


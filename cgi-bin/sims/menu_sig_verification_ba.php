<?php
session_start();

include_once('sims_checksession.php');

//if($_SESSION['paystub_admin_access'] !== 'Yes'){

//header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
//exit;
//}

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

$debug = 'off';
//$paystub_access = 'yes';
$today = date("m/d/Y");

$action = $_GET['action'];

if($action == ''){
$action = 'show_mine';
}
$query = $_GET['query'];
//$today = '10/15/2008';
//echo '<p>$today: '.$today;
//echo '<p>$_SESSION[staff_ID]: '.$_SESSION['staff_ID'];
//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


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

if($action == 'show_mine'){

$staff_ID = $_SESSION['staff_ID'];
$aID = $_GET['aID'];
//echo $_SESSION['staff_ID'];
#########################################################
## START: FIND LAST 12 PAYSTUBS FOR THE SELECTED STAFF ##
#########################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','SIMS_ba_signature_verification_log',12);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$staff_ID);
//$search -> AddDBParam('c_periodend_local',$today,'lte');
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('month_start_date','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
//$_SESSION['timesheet_foundcount'] = $searchResult['foundCount'];
$recordData = current($searchResult['data']);
$_SESSION['audit_id'] = $recordData['c_record_ID'][0];
#################################################
## END: FIND TIMESHEETS FOR THE SELECTED STAFF ##
#################################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Electronic Signature Verification</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="500" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Electronic Signature Audit Screen</h1><hr /></td></tr>
			<tr bgcolor="#e2eaa4"><td class="body" nowrap><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#e2eaa4">
						
						<td class="body">Audit ID</td>
						<td class="body">Year</td>
						<td class="body">Month</td>
						<td class="body">Num Signatures</td>
						<td class="body">Form Received in ACCT</td>
						
						</tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="body"><?php echo $searchData['c_record_ID'][0];?></td>
						<td class="body"><?php echo $searchData['year'][0];?></td>
						<td class="body"><a href="/staff/sims/menu_sig_verification_ba.php?id=<?php echo $_SESSION['user_ID'];?>&month=<?php echo $searchData['month'][0];?>&year=<?php echo $searchData['year'][0];?>&action=show_log" title="Click here to view this staff member's signature log."><?php echo $searchData['month'][0];?></a></td>
						
						<td class="body"><?php echo $searchData['c_signature_count'][0];?></td>
						<td class="body"><?php if($searchData['form_received_in_ACCT_timestamp'][0] == ''){?><span style="color:#ff0000">Pending</span><?php }else{echo $searchData['form_received_in_ACCT_timestamp'][0];?><?php }?></td>
						
						</tr>
			
						<?php } ?>
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

}elseif($action == 'show_log'){ 



$user_ID = $_GET['id'];
$month = $_GET['month'];
$year = $_GET['year'];


$sortby = $_GET['sortby'];

if($sortby == ''){
$sortby = 'c_table_display';
}
#########################################
## START: FIND SIGNATURE LOG FOR THIS USER
#########################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','audit_table','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('user',$user_ID);
$search -> AddDBParam('c_monthname',$month);
$search -> AddDBParam('c_year',$year);
$search -> AddDBParam('c_is_a_signature','1');
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam($sortby,'ascend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
#########################################
## END: FIND SIGNATURE LOG FOR THIS USER
#########################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Electronic Signature Verification</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="500" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Electronic Signature Audit Screen</h1><hr /></td></tr>
			<tr bgcolor="#e2eaa4"><td class="body" nowrap><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="menu_sig_verification_ba.php" title="Return to SIMS Electronic Signature Audit screen.">SIMS Signatures Audit Menu</a> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#ebebeb"><td colspan="4"><strong><?php echo $month.' '.$year.' | '.$searchResult['foundCount'];?> signatures found.</strong><div style="float:right"><a href="/staff/sims/menu_sig_verification_ba.php?id=<?php echo $user_ID;?>&month=<?php echo $month;?>&year=<?php echo $year;?>&action=show_log_print" title="Click here to print signature verification form for the selected month." target="_blank">Print Signature Verification Form</a></div></td></tr>
						<tr bgcolor="#e2eaa4">
						
						<td class="body" nowrap>Audit ID</td>
						<td class="body" nowrap><a href="menu_sig_verification_ba.php?id=<?php echo $user_ID;?>&month=<?php echo $month;?>&year=<?php echo $year;?>&action=show_log&sortby=timestamp">Signature Date</a></td>
						<td class="body"><a href="menu_sig_verification_ba.php?id=<?php echo $user_ID;?>&month=<?php echo $month;?>&year=<?php echo $year;?>&action=show_log&sortby=c_table_display">Item</a></td>
						<td class="body"><a href="menu_sig_verification_ba.php?id=<?php echo $user_ID;?>&month=<?php echo $month;?>&year=<?php echo $year;?>&action=show_log&sortby=c_action_display">Description</a></td>
						
						</tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="body"  style="vertical-align:text-top"><?php echo $searchData['c_record_ID'][0];?></td>
						<td class="body"  style="vertical-align:text-top" nowrap><?php echo $searchData['timestamp'][0];?></td>
						<td class="body"  style="vertical-align:text-top" nowrap><?php echo $searchData['c_table_display'][0];?></td>
						<td class="body"  style="vertical-align:text-top"><?php echo $searchData['action_description'][0];?></td>
						
						</tr>
			
						<?php } ?>
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

}elseif($action == 'show_log_print'){ 



$user_ID = $_GET['id'];
$month = $_GET['month'];
$year = $_GET['year'];


$sortby = $_GET['sortby'];

if($sortby == ''){
$sortby = 'c_table_display';
}
#########################################
## START: FIND SIGNATURE LOG FOR THIS USER
#########################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','audit_table','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('user',$user_ID);
$search -> AddDBParam('c_monthname',$month);
$search -> AddDBParam('c_year',$year);
$search -> AddDBParam('c_is_a_signature','1');
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('timestamp','ascend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
#########################################
## END: FIND SIGNATURE LOG FOR THIS USER
#########################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Electronic Signature Verification</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>

</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#ffffff"><img src="/staff/sims/images/logo-new-grayscale.png"><div style="float:right">SIMS: BUDGET AUTHORITY ELECTRONIC SIGNATURE VERIFICATION FORM</div></td></tr>
		
			<tr bgcolor="#ebebeb"><td class="body" nowrap><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></td><td align="right">Form ID: <?php echo $_SESSION['audit_id'];?></td></tr>
			
			
			<tr><td colspan="2">
			
<span class="tiny"><strong><?php echo strtoupper($month).' '.$year.' | '.$searchResult['foundCount'];?> signatures processed.</strong></span><br/>

						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#ebebeb">
						
						<td class="tiny" nowrap>Audit ID</td>
						<td class="tiny" nowrap>Signature Date</td>
						<td class="tiny">Item</td>
						<td class="tiny">Description</td>
						
						</tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="tiny" style="vertical-align:text-top"><?php echo $searchData['c_record_ID'][0];?></td>
						<td class="tiny" style="vertical-align:text-top" nowrap><?php echo $searchData['timestamp'][0];?></td>
						<td class="tiny" style="vertical-align:text-top" nowrap><?php echo $searchData['c_table_display'][0];?></td>
						<td class="tiny" style="vertical-align:text-top"><?php echo $searchData['action_description_print'][0];?></td>
						
						</tr>
			
						<?php } ?>
						</table>

			</td></tr>
			<tr bgcolor="#ebebeb"><td class="body"  style="text-align:right" nowrap>Budget Authority Signature/Date:</td><td style="border-bottom:1px #999999 solid;width:100%">&nbsp;</td></tr>
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

}else{ ?>


Error | <a href="menu_sig_verification.php?action=show_all" title="Return to SIMS Electronic Signatures Admin screen.">Return to Electronic Signatures Admin</a>

<?php } ?>





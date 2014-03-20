<?php
session_start();

include_once('sims_checksession.php');
if($_SESSION['user_ID'] == ''){
header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
exit;
}

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];


if(($_SESSION['user_ID'] == '')||(!isset($_SESSION['user_ID']))){
header('Location: http://www.sedl.org/staff/');
exit;
}
//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');

$_SESSION['menu_type'] == 'ba_admin';

$action = $_GET['action'];

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
if($action == ''){ // SHOW REGULAR PURCHASE REQUISITIONS (PRs)

$displaynum = $_GET['displaynum'];
if($displaynum == ''){
$displaynum = 12;
}

####################################################################
## START: FIND PURCHASE REQUESTS FOR THIS BUDGET AUTHORITY ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('Purchase_Req_Order.fp7','PO_table',$displaynum);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);

$search -> AddDBParam('c_signer_list_all',$_SESSION['user_ID']);
$search -> AddDBParam('PR_submitted_timestamp','*');
$search -> AddDBParam('c_search_target_status_pending_approved','1');

$search -> AddSortParam('status','descend');
$search -> AddSortParam('DATE_OF_ORDER','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
##################################################################
## END: FIND PURCHASE REQUESTS FOR THIS BUDGET AUTHORITY ##
##################################################################

//$_SESSION['pr_rejected']
$pending_pr_count = 0; // SET PENDING PR COUNTER
if($searchResult['foundCount'] > 0) { // PRs WERE FOUND FOR THIS BA
	foreach($searchResult['data'] as $key => $searchData) { // LOOP THROUGH PRs COUNTING PENDING ONES
		if(strpos($searchData['c_ba_signers_remaining'][0], $_SESSION['user_ID']) === false){}else{ // THE BA HAS NOT APPROVED THE PR
		$pending_pr_count++; // INCREMENT PENDING PR COUNTER
		}
	}	
}

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: Purchase Requests / Purchase Orders</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">



<script language="JavaScript">

function baMessage() { 
	var answer = confirm ("This leave request has not been submitted.")
	return false;
	
}

</script>


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Purchase Requisitions: Budget Authority Admin</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b>Budget Authority: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<?php if($_SESSION['pr_rejected'] == '1'){ ?>
			
				<tr><td colspan="2">
					<p class="alert_small"><b>Budget Authority: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - PR has been returned to requestor for corrections.</p>
				</td></tr>

			<?php $_SESSION['pr_rejected'] = ''; } ?>

			<?php if($_SESSION['pr_rejected'] == '2'){ ?>
			
				<tr><td colspan="2">
					<p class="alert_small"><b>Budget Authority: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b> - There was an error rejecting this PR. Contact technical assistance at <a href="sims@sedl.org">sims@sedl.org</a>.</p>
				</td></tr>

			<?php $_SESSION['pr_rejected'] = ''; } ?>

			<tr><td colspan="2">
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="ebebeb" bgcolor="ffffff" width="100%" class="sims">
						
						<tr>
							<td colspan="6" class="body" bgcolor="ebebeb">Purchase Requisitions Pending Your Approval (<?php echo $pending_pr_count; ?>)</td>
							<td colspan="3" class="body" bgcolor="ebebeb" align="right"><a href="menu_po_ba.php?action=view_sup">Show Supplementals</a></td>
						</tr>
						
						<tr bgcolor="#a2c7ca">
						
						<td nowrap>ID</td>
						<td nowrap>Requested by</td>
						<td nowrap>SEDL Unit</td>
						<td nowrap>Date of PR</td>
						<td nowrap>Date of PO</td>
						<td nowrap>Purpose/Description</td>
						<td nowrap align="right">Amount</td>
						<td nowrap align="right" nowrap>BA Approval</td>
						<td nowrap align="right">Status</td>

						</tr>
						
						<?php if($searchResult['foundCount'] > 0) {?>
						
							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							
								<tr>
								<td style="vertical-align:text-top"><a href="pur_order_ba.php?id=<?php echo $searchData['PO_ID'][0];?>"><?php echo $searchData['PO_ID'][0];?></a></td>
								<td style="vertical-align:text-top"><?php echo $searchData['requested_by'][0];?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['c_SEDL_unit_csv'][0];?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['PR_submitted_date'][0];?></td>
								<td style="vertical-align:text-top"><?php if($searchData['DATE_OF_ORDER'][0] == ''){echo '<font color="red">Pending</font>';}else{echo $searchData['DATE_OF_ORDER'][0];?><br><strong><span title="PO Number: <?php echo $searchData['PURCHASE_ORDER_NO'][0];?>" style="color:#339900"><?php echo $searchData['PURCHASE_ORDER_NO'][0];?></span></strong><?php }?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['PR_description_general'][0];?><br><span class="tiny"><em><?php echo $searchData['c_vendor_name_truncated'][0];?></em></span></td>
								<td style="vertical-align:text-top" align="right">$<?php echo number_format($searchData['c_PO_total'][0],2,'.',',');?></td>
	


								<?php if(strpos($searchData['c_ba_signers_remaining'][0], $_SESSION['user_ID']) === false){ // CURRENT USER HAS SIGNED ALL SIGNATURE BOXES FOR THIS PR ?>
								
								<td style="vertical-align:text-top" align="right"><font color="blue">Approved</font></td>
								
								<?php }else{ ?>
								<td style="vertical-align:text-top" align="right"><font color="red">Pending</font><br><span class="tiny" style="color:#999999;font-weight:bold">><?php echo $searchData['c_next_to_sign'][0];?></span></td>
								
								<?php } ?>
	
								<?php if($searchData['status'][0]=='Approved'){ ?>
								<td style="vertical-align:text-top" align="right"><font color="blue"><?php echo $searchData['status'][0];?></font></td>
								
								<?php }else{ ?>
								<td style="vertical-align:text-top" align="right"><font color="red"><?php echo $searchData['status'][0];?></font></td>
								
								<?php } ?>
	
	
								</tr>
					
							<?php } ?>

								<tr><td colspan="6" style="background-color:#fbf59a"><?php if($searchResult['foundCount'] < 12){/* DON'T DISPLAY ANYTHING */}else{?><a href="menu_po_ba.php?displaynum=<?php echo $displaynum + 12;?>">Show more</a><?php } ?><?php if($displaynum > 12){?> | <a href="menu_po_ba.php?displaynum=<?php echo $displaynum - 12;?>">Show less</a><?php }?></td><td colspan="3" align="right" style="background-color:#000000;text-align:center;color:#ffffff">Showing <?php if($searchResult['foundCount'] < $displaynum){echo $searchResult['foundCount'];}else{echo $displaynum;}?> records</td></tr>

						<?php }else{ ?>

								<tr>
								<td style="vertical-align:text-top;text-align:center" colspan="9">No records found.</td>
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
}elseif($action == 'view_sup'){ // SHOW SUPPLEMENTAL PURCHASE REQUISITIONS (SPRs) 

$displaynum = $_GET['displaynum'];
if($displaynum == ''){
$displaynum = 12;
}


####################################################################
## START: FIND PURCHASE REQUESTS FOR THIS BUDGET AUTHORITY ##
####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('Purchase_Req_Order.fp7','PO_table',$displaynum);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);

$search -> AddDBParam('c_signer_list_all',$_SESSION['user_ID']);
$search -> AddDBParam('PR_sup_submitted_timestamp','==*');

$search -> AddSortParam('status_sup','descend');
$search -> AddSortParam('PR_sup_submitted_date','descend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
##################################################################
## END: FIND PURCHASE REQUESTS FOR THIS BUDGET AUTHORITY ##
##################################################################


?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: Purchase Requests / Purchase Orders</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">



<script language="JavaScript">

function baMessage() { 
	var answer = confirm ("This leave request has not been submitted.")
	return false;
	
}

</script>


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Purchase Requisitions: Budget Authority Admin</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b>Budget Authority: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="ebebeb" bgcolor="ffffff" width="100%" class="sims">
						
						<tr>
							<td colspan="7" class="body" bgcolor="ebebeb">Supplemental Purchase Requisitions (SPRs) Requiring Your Approval (<?php echo $searchResult['foundCount']; ?>)</td>
							<td colspan="3" class="body" bgcolor="ebebeb" align="right"><a href="menu_po_ba.php">Show Regular PRs</a></td>
						</tr>
						
						<tr bgcolor="#a2c7ca">
						
						<td nowrap>ID</td>
						<td nowrap>Requested by</td>
						<td nowrap>SEDL Unit</td>
						<td nowrap>Date of PO</td>
						<td nowrap>Date of SPR</td>
						<td nowrap>Purpose/Description</td>
						<td nowrap align="right">PO Amount</td>
						<td nowrap align="right">Sup Amount</td>
						<td nowrap align="right" nowrap>BA Approval</td>
						<td nowrap align="right">Status</td>

						</tr>
						
						<?php if($searchResult['foundCount'] > 0) {?>
						
							<?php foreach($searchResult['data'] as $key => $searchData) { ?>
							
								<tr>
								<td style="vertical-align:text-top"><a href="pur_order_ba.php?id=<?php echo $searchData['PO_ID'][0];?>&action=view_sup"><?php echo $searchData['PO_ID'][0];?></a></td>
								<td style="vertical-align:text-top"><?php echo $searchData['requested_by_sup'][0];?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['c_SEDL_unit_csv'][0];?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['DATE_OF_ORDER'][0];?><br><strong><span title="PO Number: <?php echo $searchData['PURCHASE_ORDER_NO'][0];?>" style="color:#339900"><?php echo $searchData['PURCHASE_ORDER_NO'][0];?></span></strong></td>
								<td style="vertical-align:text-top"><?php echo $searchData['PR_sup_submitted_date'][0];?></td>
								<td style="vertical-align:text-top"><?php echo $searchData['PR_description_general'][0];?></td>
								<td style="vertical-align:text-top" align="right">$<?php echo number_format($searchData['original_PO_total'][0],2,'.',',');?></td>
								<td style="vertical-align:text-top" align="right">$<?php echo number_format($searchData['c_sup_po_amount'][0],2,'.',',');?></td>
	
								<?php if(strpos($searchData['c_ba_signers_remaining_sup'][0], $_SESSION['user_ID']) === false){ // CURRENT USER HAS SIGNED ALL SIGNATURE BOXES FOR THIS SPR ?>
								
								<td style="vertical-align:text-top" align="right"><font color="blue">Approved</font></td>
								
								<?php }else{ ?>
								<td style="vertical-align:text-top" align="right"><font color="red">Pending</font></td>
								
								<?php } ?>
	
								<?php if($searchData['status_sup'][0]=='Approved'){ ?>
								<td style="vertical-align:text-top" align="right"><font color="blue"><?php echo $searchData['status_sup'][0];?></font></td>
								
								<?php }else{ ?>
								<td style="vertical-align:text-top" align="right"><font color="red"><?php echo $searchData['status_sup'][0];?></font></td>
								
								<?php } ?>
	
	
								</tr>
					
							<?php } ?>

								<tr><td colspan="7" style="background-color:#fbf59a"><?php if($searchResult['foundCount'] < 12){/* DON'T DISPLAY ANYTHING */}else{?><a href="menu_po_ba.php?action=view_sup&displaynum=<?php echo $displaynum + 12;?>">Show more</a><?php } ?><?php if($displaynum > 12){?> | <a href="menu_po_ba.php?action=view_sup&displaynum=<?php echo $displaynum - 12;?>">Show less</a><?php }?></td><td colspan="3" align="right" style="background-color:#000000;text-align:center;color:#ffffff">Showing <?php if($searchResult['foundCount'] < 12){echo $searchResult['foundCount'];}else{echo $displaynum;}?> records</td></tr>

						<?php }else{ ?>

								<tr>
								<td style="vertical-align:text-top;text-align:center" colspan="10">No records found.</td>
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
}else{ ?>

Error

<?php } ?>




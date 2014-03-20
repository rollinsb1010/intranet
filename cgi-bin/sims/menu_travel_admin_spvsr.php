<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


####################################################
## START: FIND TRAVEL REQUESTS FOR THIS ADMIN ##
####################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','travel_authorizations',25);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('c_signer_list_menu',$_SESSION['user_ID']);
$search -> AddDBParam('approval_status','Cancelled', 'neq');

//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('approval_status_tr','descend');
$search -> AddSortParam('approval_status','descend');
$search -> AddSortParam('approval_status_vchr','descend');
$search -> AddSortParam('c_event_start_date_menu_sort_display','descend');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['travel_request_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
############################################
## END: FIND TRAVEL REQUESTS FOR THIS ADMIN ##
############################################


//$current_pay_period = date("m").'/'.date("t").'/'.date("Y");

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: Workgroup Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">


function zoomWindow() {
window.resizeTo(1000,screen.height)
}
</script>

<script language="JavaScript" type="text/JavaScript">
//<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->



function confirmView() { 
	var answer = alert ("This travel authorization is currently being processed. You will receive an e-mail notification when your signature is required.")
	
	return false;
	
}
</script>
</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Travel Requests: Supervisor/Budget Authority Admin</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b>Supervisor/Budget Authority: <?php echo $_SESSION['staff_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<?php if($_SESSION['travel_request_submitted_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your travel request has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['travel_request_submitted_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['travel_request_submitted_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your travel request, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance. | ErrorCode: <?php echo $_SESSION['errorCode'];?></p></td></tr>
				<?php $_SESSION['travel_request_submitted_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['travel_prefs_updated'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your travel preferences have been updated.</p></td></tr>
				<?php $_SESSION['travel_prefs_updated'] = ''; ?>

			<?php } elseif($_SESSION['travel_prefs_updated'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem updating your travel preferences, please contact <a href="mailto:sims@sedl.org">technical support</a>.</p></td></tr>
				<?php $_SESSION['travel_prefs_updated'] = ''; ?>

			<?php } elseif($_SESSION['travel_authorization_submitted_admin'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">The travel authorization has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></p></td></tr>
				<?php $_SESSION['travel_authorization_submitted_admin'] = ''; ?>

			<?php } elseif($_SESSION['travel_authorization_submitted_admin'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting the travel authorization, please contact <a href="mailto:sims@sedl.org">technical support</a>.</p></td></tr>
				<?php $_SESSION['travel_authorization_submitted_admin'] = ''; ?>

			<?php } elseif($_SESSION['travel_request_approved_pba'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">The travel request has been approved and submitted to SIMS for processing. <img src="/staff/sims/images/green_check.png"></p></p></td></tr>
				<?php $_SESSION['travel_request_approved_pba'] = ''; ?>

			<?php } elseif($_SESSION['travel_request_approved_pba'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem approving the travel request, please contact <a href="mailto:sims@sedl.org">technical support</a>.</p></td></tr>
				<?php $_SESSION['travel_request_approved_pba'] = ''; ?>

			<?php } elseif($_SESSION['travel_request_approved_spvsr'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">You have successfully approved this travel request. <img src="/staff/sims/images/green_check.png"></p></p></td></tr>
				<?php $_SESSION['travel_request_approved_spvsr'] = ''; ?>

			<?php } elseif($_SESSION['travel_request_approved_spvsr'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem approving the travel request, please contact <a href="mailto:sims@sedl.org">technical support</a>.</p></td></tr>
				<?php $_SESSION['travel_request_approved_spvsr'] = ''; ?>

			<?php } ?>
			





			



			<tr><td colspan="2">
<form name="form1" method="post">			
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="#ebebeb" bgcolor="#ffffff" width="100%" class="sims">
						
						<tr>
							<td colspan="9" class="body" bgcolor="ebebeb"><?php echo $_SESSION['workgroup'];?> Travel Requests / Authorizations (<?php echo $searchResult['foundCount']; ?>)</td>
						</tr>

</form>						
						<tr bgcolor="#a2c7ca">
						
						<td>ID</td>
						<td>Staff</td>
						<td nowrap>Date(s) of Travel</td>
						<td>Event Name</td>
						<td>Destination</td>
						<td>Request</td>
						<td align="right" nowrap>Authorization</td>
						<td align="right" nowrap>Voucher</td>

						</tr>
						
<?php if($searchResult['foundCount'] > 0){ ?>

						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="body" style="vertical-align:text-top"><?php echo $searchData['travel_auth_ID'][0];?></td>
						<td class="body" style="vertical-align:text-top" nowrap><a href="/staff/sims/travel_admin.php?travel_auth_ID=<?php echo $searchData['travel_auth_ID'][0];?>&action=<?php if(($searchData['doc_status'][0] == '0')||($searchData['approval_status'][0] == 'Not Submitted')){?>view_tr<?php }else{?>approve_ta<?php }?>&app=<?php echo $searchData['approval_status'][0];?>" <?php if($searchData['doc_status'][0] == '2'){?>onclick="return confirmView()"<?php }?>><?php echo $searchData['staff::c_full_name_last_first'][0];?></a></td>
						<td class="body" style="vertical-align:text-top" nowrap><?php if($searchData['leave_date_requested'][0] != $searchData['return_date_requested'][0]){echo $searchData['leave_date_requested'][0].' - '.$searchData['return_date_requested'][0];} else { echo $searchData['leave_date_requested'][0];}?></td>
						<td class="body" style="vertical-align:text-top"><?php echo $searchData['purpose_of_travel_descr'][0];?></td>
						<td class="body" style="vertical-align:text-top" nowrap><?php if($searchData['multi_dest'][0] == 'yes'){echo $searchData['event_venue_city1'][0];?>, <?php echo $searchData['event_venue_state1'][0].'**';}else{echo $searchData['event_venue_city'][0];?>, <?php echo $searchData['event_venue_state'][0];}?></td>

						<?php if($searchData['staff::travel_approves_own_travel_request'][0] == 'Yes'){ ?>
						<td class="body" style="vertical-align:text-top" nowrap><?php if($searchData['approval_status_tr'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status_tr'][0];?></font> <span class="tiny">(<?php echo $searchData['tr_approved_by'][0];?>)</span></td>
						<?php }elseif($searchData['signer_ID_spvsr'][0] == $searchData['signer_ID_pba'][0]){ ?>
						<td class="body" style="vertical-align:text-top" nowrap><?php if($searchData['approval_status_tr'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status_tr'][0];?></font> <span class="tiny">(<?php echo $searchData['tr_approved_by'][0];?>)</span></td>
						<?php }else{ ?>
						<td class="body" style="vertical-align:text-top" nowrap><?php if($searchData['approval_status_tr_spvsr'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status_tr_spvsr'][0];?></font> <span class="tiny">(<?php echo $searchData['tr_approved_by_spvsr'][0];?>)</span><br>
						<?php if($searchData['approval_status_tr'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status_tr'][0];?></font> <span class="tiny">(<?php echo $searchData['tr_approved_by'][0];?>)</span></td>
						<?php } ?>

						<td class="body" style="vertical-align:text-top" align="right" nowrap><?php if($searchData['approval_status'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status'][0];?></font></td>
						<td class="body" style="vertical-align:text-top" align="right" nowrap><?php if($searchData['approval_status_vchr'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status_vchr'][0];?></font></td>
						</tr>
			
						<?php } ?>

<?php } else { ?>


						<tr>
						<td class="body" colspan="9" height="40" align="center">No records found.</td>
						</tr>


<?php } ?>



						<tr>
							<td colspan="9" class="body" bgcolor="ebebeb"><span class="tiny">** Indicates multi-destination travel.</span></td>
						</tr>




						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>

<?php //} else { ?>

<!--No records found.-->

<?php //} ?>

<?php //} else { ?>



<?php //} ?>
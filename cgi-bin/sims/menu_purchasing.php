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
## START: FIND PRs AND POs FOR THIS USER ##
####################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('Purchase_Req_Order.fp7','PO_main_table',12);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('requested_by','=='.$_SESSION['user_ID']);
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam('event_start_date','descend');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['travel_request_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
##################################################
## END: FIND PRs AND POs FOR THIS USER ##
##################################################
//$current_pay_period = date("m").'/'.date("t").'/'.date("Y");

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: My Purchase Requisitions</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Purchase Requisitions</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 border=1 bordercolor="#ebebeb" bgcolor="#ffffff" width="100%" class="sims">
						<tr bgcolor="#a2c7ca">
						
						<td>PO ID</td>
						<td>Description</td>
						<td>Signers</td>
						<td style="text-align:right">Total</td>
						<td>Date/Time Submitted</td>
						<td style="text-align:right">Status</td>

						</tr>
						
<?php if($searchResult['foundCount'] > 0){ ?>

						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td><a href="fmp7://198.214.140.248/Purchase_Req_Order.fp7"><?php echo $searchData['PO_ID'][0];?></a></td>
						<td><?php echo $searchData['PR_description_general'][0];?></td>
						<td><?php echo $searchData['c_ba_signer_list'][0];?></td>
						<td style="text-align:right">$<?php echo $searchData['c_PO_total'][0];?></td>	
						<td><?php echo $searchData['PR_submitted_timestamp'][0];?></td>

						<td class="body" align="right"><?php if($searchData['status'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['status'][0];?></font></td>
						</tr>
			
						<?php } ?>

<?php } else { ?>


						<tr>
						<td class="body" colspan="8" height="40" align="center">No records found.</td>
						</tr>


<?php } ?>







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
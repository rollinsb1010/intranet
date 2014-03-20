<?php
session_start();


include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('fxphp-master/FX.php');
include_once('fxphp-master/server_data.php');
error_reporting(0);
$mod = $_REQUEST['mod'];

if($mod == 'x'){ // USER DELETED AN OPEN TICKET
#######################################################
## START: DELETE TICKET ##
#######################################################
$delete = new FX($serverIP,$webCompanionPort,$dataSourceType);
$delete -> SetDBData('SEDL_HelpDesk.fmp12','help_tickets_by_selection');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$_REQUEST['row']);

$deleteResult = $delete -> FMDelete();
#####################################################
## END: DELETE TICKET ##
#####################################################
}

##############################################
## START: FIND HELPDESK TICKETS FOR THIS USER ##
##############################################
$search = new FX($serverIP,$webCompanionPort,$dataSourceType);
$search -> SetDBData('SEDL_HelpDesk.fmp12','help_tickets_by_selection',12);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('requestor_sims_ID',$_SESSION['user_ID']);
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('creation_timestamp','descend');
//$search -> AddSortParam('travel_auth_ID','descend');
//$search -> AddSortParam('c_event_start_date_menu_sort_display','descend');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['travel_request_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
############################################
## END: FIND HELPDESK TICKETS FOR THIS USER ##
############################################
//$current_pay_period = date("m").'/'.date("t").'/'.date("Y");

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: My HelpDesk Tickets</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function preventDelete() { 
	var answer = confirm ("Closed help tickets cannot be deleted.")
	return false;
	
}
</script>

<script language="JavaScript">

function confirmDelete() { 
	var answer2 = confirm ("Are you sure you want to delete this help ticket?")
	if (!answer2) {
	return false;
	}
}

function zoomWindow() {
window.resizeTo(1000,screen.height)
}
</script>


</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#333333"><img src="/staff/sims/images/helpdesk_header.jpg"></td></tr>
		
			<tr><td class="body" nowrap><strong>SIMS User:</strong> <?php echo $_SESSION['user_ID'];?></td><td align="right"><a href="/staff/sims/help_ticket.php?action=new">New Help Ticket</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<tr style="color:#ffffff;background-color:#747373"><td colspan="2"><strong>MY HELP TICKETS</strong></td></tr>
			
			
<?php if($_SESSION['ticket_saved'] == '1'){?>
			<tr><td colspan="2"><p class="alert_small">Your help ticket was successfully submitted to SEDL HelpDesk.</p></td></tr>
<?php $_SESSION['ticket_saved'] = '';}?>
			
<?php if($_SESSION['ticket_saved'] == '2'){?>
			<tr><td colspan="2"><p class="alert_small">There was a problem submitting your help ticket. Errorcode: <?php echo $_SESSION['ticket_error'];?>. Contact <a href="mailto:helpdesk@sedl.org">helpdesk@sedl.org</a> for assistance.</p></td></tr>
<?php $_SESSION['ticket_saved'] = '';}?>
			<tr><td colspan="2">
			
						<table style="border:1px #a2a2a2 solid;width:100%">
						<tr style="color:#000000;background-color:#bdbcbc">
						
						<td style="padding:6px">Ticket#</td>
						<td style="padding:6px">Date</td>
						<td style="padding:6px">Subject</td>
						<td style="padding:6px">Priority</td>
						<td style="padding:6px" align="right">Status</td>

						<td style="padding:6px" align="right">Delete</td></tr>
						
<?php if($searchResult['foundCount'] > 0){ ?>

						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr style="vertical-align:text-top;background-color:#<?php if($searchData['status'][0] == 'New'){echo 'f9c9c9';}elseif($searchData['status'][0] == 'Open'){echo 'f9f6c9';}else{echo '7cb17d';}?>">
						<td style="padding:6px"><a href="help_ticket.php?action=show1&id=<?php echo $searchData['ticket_ID'][0];?>"><?php echo $searchData['ticket_ID'][0];?></a></td>
						<td style="padding:6px" nowrap><?php echo $searchData['creation_timestamp'][0];?></td>
						<td style="padding:6px" style="width:100%"><?php echo $searchData['issue_subject'][0];?></td>
						<td style="padding:6px" nowrap><?php echo $searchData['priority'][0];?></td>

						
						
						
						<?php if($searchData['status'][0] =='Closed'){ ?>
						<td style="padding:6px" align="right"><font color="blue"><?php echo $searchData['status'][0];?></font></td>
						<td style="padding:6px" align="right" style="background-color:#ffffff"><img src="/staff/sims/images/padlock.jpg" border="0" title="This travel request is locked."></td>
						<?php }else{ ?>
						<td style="padding:6px" align="right"><font color="red"><?php echo $searchData['status'][0];?></font></td>
						<td style="padding:6px;background-color:#ffffff" align="right"><a href="menu_help_tickets.php?mod=x&row=<?php echo $searchData['c_row_ID'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/trashcan.jpg" border="0"></a></td>
						<?php } ?>
						</tr>
			
						<?php } ?>

<?php } else { ?>


						<tr>
						<td style="padding:6px" colspan="8" height="40" align="center">No records found.</td>
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
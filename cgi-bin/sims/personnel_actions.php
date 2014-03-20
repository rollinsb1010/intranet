<?php
session_start();

include_once('sims_checksession.php');

//if($_SESSION['personnel_action_admin_access'] !== 'Yes'){

//header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
//exit;
//}

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

$debug = 'off';
$paystub_access = 'yes';
$today = date("m/d/Y");

$action = $_REQUEST['action'];

if($action == ''){
$action = 'show_mine';
}

$query = $_GET['query'];
//$today = '10/15/2008';
//echo '<p>$today: '.$today;
//echo '<p>$_SESSION[staff_ID]: '.$_SESSION['staff_ID'];
//echo '<p>$_SESSION[user_ID]: '.$_SESSION['user_ID'];
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

##############################################################
## START: FIND ALL PERSONNEL ACTIONS FOR THE SELECTED STAFF ##
##############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','personnel_actions','all');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$_SESSION['staff_ID']);
//$search -> AddDBParam('c_periodend_local',$today,'lte');
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('action_effective_date','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
//$_SESSION['timesheet_foundcount'] = $searchResult['foundCount'];
$recordData = current($searchResult['data']);
############################################################
## END: FIND ALL PERSONNEL ACTIONS FOR THE SELECTED STAFF ##
############################################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Personnel Actions</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

function preventView() { 
	alert ("This personnel action is currently being processed. You will receive an e-mail notification when this document has been approved.")
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
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Personnel Actions</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr" title="Return to your SIMS home screen.">SIMS Home</a></td></tr>
			
			
			<tr><td colspan="2">
			
						<table cellpadding=4 cellspacing=0 class="sims" width="100%">
						<tr bgcolor="#a2c7ca">
						
						<td class="body">ID</td>
						<td class="body" nowrap>Effective Date</td>
						<td class="body" nowrap>Action Description</td>
						<td class="body" nowrap>Transfer From</td>
						<td class="body" nowrap>Assign To</td>
						<td class="body" align="right">Status</td>
						
						</tr>
						
						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
						<tr>
						<td class="body" style="vertical-align:text-top"><?php echo $searchData['record_ID'][0];?></td>
						<td class="body" style="vertical-align:text-top"><a href="/staff/sims/personnel_actions.php?record_ID=<?php echo $searchData['record_ID'][0];?>&action=show_action" title="Click here to view this personnel action." target="_blank" <?php if($searchData['doc_release_status'][0] == '0'){echo 'onclick="return preventView()"';}?>><?php echo $searchData['action_effective_date'][0];?></a></td>
						<td class="body" style="vertical-align:text-top"><?php echo $searchData['action_descr'][0];?></td>
						<td class="body" style="vertical-align:text-top"><?php if(($searchData['document_type'][0] == 'Temporary')||($searchData['action_descr'][0] == 'Probationary Employment')){echo 'N/A';}else{echo $searchData['transfer_from_title'][0];?><br>Pay grade: <?php echo $searchData['transfer_from_paygrade'][0];?><br>$<?php echo number_format($searchData['transfer_from_actual_monthly_rate'][0],2,'.',',');?>/mo.<?php } ?></td>
						<td class="body" style="vertical-align:text-top"><?php if($searchData['document_type'][0] == 'Temporary'){echo $searchData['assign_to_title'][0];?><br>$<?php echo number_format($searchData['assign_to_hourly_rate'][0],2,'.',',');?>/hr.<?php }else{echo $searchData['assign_to_title'][0];?><br>Pay grade: <?php echo $searchData['assign_to_paygrade'][0];?><br>$<?php echo number_format($searchData['assign_to_actual_monthly_rate'][0],2,'.',',');?>/mo.<?php } ?></td>
						<td class="body" align="right" style="vertical-align:text-top<?php if($searchData['c_approval_status'][0] == 'Pending'){echo ';color:#ff0000';}else{echo ';color:#0000ff';}?>"><?php echo $searchData['c_approval_status'][0];?></td>
						
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

}elseif($action == 'show_action'){ 

$record_ID = $_GET['record_ID'];
##################################################
## START: FIND PERSONNEL ACTIONS FOR THIS USER ##
##################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','personnel_actions');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('record_ID','=='.$record_ID);
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam($sortfield,'PERIODEND');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
################################################
## END: FIND PERSONNEL ACTIONS FOR THIS USER ##
################################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Personnel Actions</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Kameron:400,700' rel='stylesheet' type='text/css'>
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>


<style type="text/css">
table{  }
table.stub td {
	color: #000000;
	font-family: 'Kameron', serif;
	font-size:13px;
	background-color:#ffffff;
	padding:0px ;
	border-width:0px;
	padding-right:20px;
	padding-top:2px;
	padding-bottom:2px;
	margin:0px;
	vertical-align: text-top;
	
}


hr.ee {
border: none 0;
border-top: 1px dotted #000000;
width: 100%;
height: 1px;
margin: 0px;
text-align: left;
padding: 0px;
}

h1 {font-family: 'Kameron', serif; text-shadow: #ffffff 0px 0px 0px; color:#000000; padding:3px;}
h2 {font-family: 'Kameron', serif; text-shadow: #ffffff 0px 0px 0px; font-size:16px; color:#000000;}
th { 	font-family: 'Kameron', serif; }


</style>


</head>

<BODY BGCOLOR="#FFFFFF" onLoad="zoomWindow()">

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td colspan="2" align="right" style="vertical-align:text-top;padding:6px;border:0px"><img src="/images/print_sedl.jpg" width="20%"></td></tr>


<?php if($recordData['document_type'][0] == 'Regular'){ // SHOW REGULAR PERSONNEL ACTION FORM ?>

	<tr><td width="50%" style="vertical-align:text-top;">
	
		<h1>PERSONNEL ACTION</h1>
	
	</td><td width="50%" style="vertical-align:text-top;padding:6px;">
	
		<div>
		<div style="padding:3px; text-align:right"><strong>DATE PREPARED</strong>: <?php echo $recordData['date_prepared'][0];?></div>
		</div>
	
	</td></tr>
	
	
	
	<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">
	
		<strong>NAME OF STAFF MEMBER:</strong><br>
		<?php echo $recordData['staff_name'][0];?>
	
	</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">
		<strong>ADDRESS:</strong><br>
		<?php echo $recordData['staff_address'][0];?><br>
		<?php echo $recordData['staff_city'][0];?>, <?php echo $recordData['staff_state'][0];?> <?php echo $recordData['staff_zip'][0];?>
	
	</td></tr>
	
	
	
	
	
	
	<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">
	
		<strong>NATURE OF ACTION:</strong><br>
		<?php echo $recordData['action_descr'][0];?>
	
	</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">
	
		<strong>EFFECTIVE DATE OF ACTION:</strong><br>
		<?php echo $recordData['action_effective_date'][0];?>
	
	</td></tr>
	
	
	
	
	
	
<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>ASSIGN TO:</strong><br>

<?php if($recordData['action_descr'][0] == 'Termination'){ ?>

N/A

<?php }elseif($recordData['assign_to_title2'][0] == ''){ ?>

	<?php echo $recordData['assign_to_title'][0];?><br>
	Pay Grade: <?php echo $recordData['assign_to_paygrade'][0];?>

<?php }else{ ?>

	<?php echo $recordData['assign_to_title'][0].' - Pay Grade '.$recordData['assign_to_paygrade'][0].' ('.$recordData['percent_time_employed_assign_to_position1'][0].'%)';?><br>
	<?php echo $recordData['assign_to_title2'][0].' - Pay Grade '.$recordData['assign_to_paygrade2'][0].' ('.$recordData['percent_time_employed_assign_to_position2'][0].'%)';?>

<?php } ?>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>ACTUAL MONTHLY RATE:</strong><br>

<?php if($recordData['action_descr'][0] == 'Termination'){ ?>

N/A

<?php }elseif($recordData['assign_to_title2'][0] == ''){ ?>

	$<?php echo number_format($recordData['assign_to_actual_monthly_rate'][0],2,'.',',');?>/mo.

<?php }else{ ?>

	<?php echo '(Position 1: $'.number_format($recordData['assign_to_actual_monthly_rate'][0],2,'.',',').'/mo.)';?><br>
	<?php echo '(Position 2: $'.number_format($recordData['assign_to_actual_monthly_rate2'][0],2,'.',',').'/mo.)';?><br>
	<?php echo '(Combined: $'.number_format($recordData['assign_to_actual_monthly_rate'][0]+$recordData['assign_to_actual_monthly_rate2'][0],2,'.',',').'/mo.)';?>

<?php } ?>

</td></tr>






<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>TRANSFER FROM:</strong><br>
	<?php if($recordData['action_descr'][0] == 'Probationary Employment'){echo 'N/A';}else{?>
	
		<?php if($recordData['transfer_from_title2'][0] == ''){
	
		echo $recordData['transfer_from_title'][0].'<br>';
		echo 'Pay Grade: '.$recordData['transfer_from_paygrade'][0];
	
		}else{
		
		echo $recordData['transfer_from_title'][0].' - Pay Grade '.$recordData['transfer_from_paygrade'][0].' ('.$recordData['percent_time_employed_transfer_from_position1'][0].'%)<br>';
		echo $recordData['transfer_from_title2'][0].' - Pay Grade '.$recordData['transfer_from_paygrade2'][0].' ('.$recordData['percent_time_employed_transfer_from_position2'][0].'%)';
		
		}
	
	}?>
	
</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>ACTUAL MONTHLY RATE:</strong><br>

	<?php if($recordData['action_descr'][0] == 'Probationary Employment'){echo 'N/A';}else{
		
		if($recordData['transfer_from_title2'][0] == ''){

		echo '$'.number_format($recordData['transfer_from_actual_monthly_rate'][0],2,'.',',').'/mo.';

		}else{
		
		echo '(Position 1: $'.number_format($recordData['transfer_from_actual_monthly_rate'][0],2,'.',',').'/mo.)<br>';
		echo '(Position 2: $'.number_format($recordData['transfer_from_actual_monthly_rate2'][0],2,'.',',').'/mo.)<br>';
		echo '(Combined: $'.number_format($recordData['transfer_from_actual_monthly_rate'][0]+$recordData['transfer_from_actual_monthly_rate2'][0],2,'.',',').'/mo.)';
		
		}
		
	}?>

</td></tr>


	
	
	
	
	<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">
	
		PERCENT OF TIME EMPLOYED: <?php echo $recordData['percent_time_employed'][0];?>%<br>
		EMPLOYMENT TYPE: <?php echo $recordData['exempt_status'][0];?>
		
	</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">
	
		BASE MONTHLY SALARY: $<?php echo number_format($recordData['base_monthly_salary'][0],2,'.',',');?>/mo.<br>
		BASE ANNUAL SALARY: $<?php echo number_format($recordData['base_annual_salary'][0],2,'.',',');?>/yr.
	
	</td></tr>
	
	
	
	
	</table>
	
	
	
		
	<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
		<tr><th colspan="4"><h2 style="border-top:2px dotted #999999; text-align:left; padding-top:4px;">ELIGIBLE BENEFIT(S) SELECTED:</h2></th></tr>
		<tr><td style="padding:6px">
				<?php if($recordData['benefits_ss'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Social Security<br>
				<?php if($recordData['benefits_tiaa_cref'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> TIAA-CREF
				</td>
			
				<td style="padding:6px">
				<?php if($recordData['benefits_health_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Health Insurance<br>
				<?php if($recordData['benefits_dental_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Dental Insurance
				</td>
		
				<td style="padding:6px">
				<?php if($recordData['benefits_life_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Life Insurance<br>
				<?php if($recordData['benefits_disab_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Disability Insurance
				</td>
		
				<td style="padding:6px">
				<?php if($recordData['benefits_add_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Accidental Death & Dismemberment Insurance<br>
				<?php if($recordData['benefits_bus_van_parking'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Bus/Van/Pool/Parking <?php if($recordData['benefits_fsa'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Flexible Spending Account
		</td></tr>
	</table>



<?php }elseif($recordData['document_type'][0] == 'Temporary'){ // SHOW TEMPORARY PERSONNEL ACTION FORM ?>

	<tr><td width="50%" style="vertical-align:text-top;">
	
		<h1>TEMPORARY PERSONNEL ACTION</h1>
	
	</td><td width="50%" style="vertical-align:text-top;padding:6px;">
	
		<div>
		<div style="padding:3px; text-align:right"><strong>DATE PREPARED</strong>: <?php echo $recordData['date_prepared'][0];?></div>
		</div>
	
	</td></tr>
	
	
	
	<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">
	
		<strong>NAME OF STAFF MEMBER:</strong><br>
		<?php echo $recordData['staff_name'][0];?>
	
	</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">
		<strong>ADDRESS:</strong><br>
		<?php echo $recordData['staff_address'][0];?><br>
		<?php echo $recordData['staff_city'][0];?>, <?php echo $recordData['staff_state'][0];?> <?php echo $recordData['staff_zip'][0];?>
	
	</td></tr>
	
	
	
	
	
	
	<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">
	
		<strong>NATURE OF ACTION:</strong><br>
		<?php echo $recordData['action_descr'][0];?>
	
	</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">
	
		<strong>EFFECTIVE DATE OF ACTION:</strong><br>
		<?php echo $recordData['action_effective_date'][0];?>
	
	</td></tr>
	
	
	
	
	
	
	<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">
	
		<strong>ASSIGN TO:</strong><br>
		<?php echo $recordData['assign_to_title'][0];?>
		
	</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">
	
		<strong>HOURLY RATE:</strong><br>
		$<?php echo number_format($recordData['assign_to_hourly_rate'][0],2,'.',',');?>/hr.
	
	</td></tr>
	
	
	
	</table>
	
	
	
		
<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
	<tr>
	<th colspan="3"><h2 style="border-top:2px dotted #999999; text-align:left; padding-top:4px;">ELIGIBLE BENEFIT(S) SELECTED:</h2></th></tr>
	<tr><td style="padding:6px"><?php if($recordData['benefits_ss'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Social Security</td>
	<td style="padding:6px"><?php if($recordData['benefits_bus_van_parking'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Bus/Van/Pool/Parking</td>
	<td style="padding:6px"><?php if($recordData['benefits_add_ins'][0] == 'Yes'){?><img src="images/benefits_check_filled.jpg" style="vertical-align:middle"><?php }else{?><img src="images/benefits_check_empty.jpg" style="vertical-align:middle"><?php }?> Accidental Death & Dismemberment Insurance</td>
	</tr>
</table>


<?php } ?>



<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

	<tr><th><h2 style="border-top:2px dotted #999999; text-align:left; padding-top:4px;">REMARKS:</h2></th></tr>

	<tr><td><?php echo $recordData['c_action_remarks_html'][0];?>
	
	<?php if($recordData['related_memo_ID'][0] !== ''){?><br>---<br>
	Related Memo ID: <a href="menu_memos_admin.php?action=show_memo_print&record_ID=<?php echo $recordData['related_memo_ID'][0];?>" target="_blank"><?php echo $recordData['related_memo_ID'][0];?></a>
	<?php } ?>
	
	</td></tr>

</table>




<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333; border-right:solid 1px #333333">

	<strong>1. SEDL UNIT RECOMMENDING ACTION</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_pba'][0] !== '1'){?><?php echo 'Pending: '.$recordData['signer_ID_pba'][0];?><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_pba'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_pba'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>UNIT BUDGET AUTHORITY</strong></span></td>

</td><td width="50%" style="vertical-align:text-top;padding:6px;border-top:8px solid #333333">
	<strong>2. HUMAN RESOURCES REVIEW</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_hr'][0] !== '1'){?><?php echo 'Pending: '.$recordData['signer_ID_hr'][0];?><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_hr'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_hr'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>HUMAN RESOURCES GENERALIST</strong></span></td></tr>


<tr><td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333; border-right:solid 1px #333333">

	<strong>3. FISCAL REVIEW</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_cfo'][0] !== '1'){?><?php echo 'Pending: '.$recordData['signer_ID_cfo'][0];?><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_cfo'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_cfo'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>VICE PRESIDENT & CFO</strong></span></td></td>
	
	<td width="50%" style="vertical-align:text-top;padding:6px;border-top:1px solid #333333">

	<strong>4. APPROVAL</strong><br><span style="margin-left:15px"><?php if($recordData['sign_status_ceo'][0] !== '1'){?><?php echo 'Pending: '.$recordData['signer_ID_ceo'][0];?><?php }else{ ?><img src="signatures/<?php echo $recordData['signer_ID_ceo'][0];?>.png"><br><span class="tiny" style="color:#666666;margin-left:15px;padding:2px">[<?php echo $recordData['sign_timestamp_ceo'][0];?>]</span><?php }?></span><br><span class="tiny" style="margin-left:15px;border-top:1px dotted #666666"><strong>PRESIDENT & CEO</strong></span></td></tr>
</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="color:#666666" align="right"><br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<span class="tiny">Document ID: <?php echo $recordData['record_ID'][0];?></span></td></tr>
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


Error | <a href="personnel_actions_admin.php?action=show_mine" title="Return to my SIMS Personnel Actions screen.">Return to Personnel Actions Admin</a>

<?php } ?>





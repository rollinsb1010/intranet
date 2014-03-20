<?php
session_start();

include_once('sims_checksession.php');

if($_SESSION['user_ID'] == ''){
header('Location: http://www.sedl.org/staff/sims/sims_menu.php?src=intr');
exit;
}
//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');



$debug = 'off';


$nov = $_GET['nov'];
//echo $nov;

#################################################################
## START: FIND SEDL NOVS ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('admin_base.fp7','positions_NOVs','all');
$search -> SetDBPassword($webPW,$webUN);

$search -> AddSortParam('c_unit_abbrev','ascend');
$search -> AddSortParam('position_title','ascend');

$searchResult = $search -> FMFindall();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//$recordData = current($searchResult['data']);
###############################################################
## END: FIND SEDL NOVS ##
###############################################################

if($nov !== ''){ 

#################################################################
## START: FIND SELECTED SEDL NOV ##
#################################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('admin_base.fp7','positions_NOVs_stats','all');
$search2 -> SetDBPassword($webPW,$webUN);

$search2 -> AddDBParam('position_ID',$nov);

//$search2 -> AddSortParam('c_unit_abbrev','ascend');

$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
$recordData = current($searchResult2['data']);
//echo '<p>$recordData[position_ID]: '.$recordData['position_ID'][0];
###############################################################
## END: FIND SELECTED SEDL NOV ##
###############################################################

#################################################################
## START: FIND SELECTED SEDL NOV -- ACTION LOG ENTRIES ##
#################################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('admin_base.fp7','resumes_action_log','all');
$search3 -> SetDBPassword($webPW,$webUN);

$search3 -> AddDBParam('position_ID',$nov);

//$search3 -> AddSortParam('c_unit_abbrev','ascend');

$searchResult3 = $search3 -> FMFind();

//echo '<p>$searchResult3[errorCode]: '.$searchResult3['errorCode'];
//echo '<p>$searchResult3[foundCount]: '.$searchResult3['foundCount'];
//$recordData3 = current($searchResult3['data']);
//echo '<p>$recordData3[position_ID]: '.$recordData3['position_ID'][0];
###############################################################
## END: FIND SELECTED SEDL NOV -- ACTION LOG ENTRIES ##
###############################################################

###################################
## GET MINORITY APPLICANT COUNT/ ##
###################################
$minority_count = 0;
$unknown_count = 0;
foreach($searchResult3['data'] as $key => $searchData3) { 

	if($searchData3['c_action_is_app_or_interview'][0] == '1'){ // APPLICATION WAS REQUESTED OR INTERVIEW CONDUCTED
	
		if($searchData3['unsolicited_resumes::ethnicity_code'][0] == 'Unknown'){$unknown_count = $unknown_count + 1;}
		if(($searchData3['unsolicited_resumes::ethnicity_code'][0] !== 'Unknown')&&($searchData3['unsolicited_resumes::ethnicity_code'][0] !== 'White')&&($searchData3['unsolicited_resumes::ethnicity_code'][0] !== '')){$minority_count = $minority_count + 1;}
	
	}

}
###################################
## /GET MINORITY APPLICANT COUNT ##
###################################
}

?>

<html>
<head>
<title>SIMS: NOV Stats</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Kameron:400,700' rel='stylesheet' type='text/css'>
<script language="JavaScript">

function overlayvis(blck) {
  el = document.getElementById(blck.id);
  el.style.visibility = (el.style.visibility == 'visible') ? 'hidden' : 'visible';
}

</script>


<script language="JavaScript" type="text/JavaScript">
//<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
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
	white-space: nowrap;
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

h1 {font-family: 'Kameron', serif; text-shadow: #ffffff 0px 0px 0px; color:#0033cc; padding:3px;}
h2 {font-family: 'Kameron', serif; text-shadow: #ffffff 0px 0px 0px; font-size:16px; color:#000000;}
th { 	font-family: 'Kameron', serif; }


</style>


</head>

<BODY BGCOLOR="#FFFFFF" onLoad="UpdateSelect();">

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td style="vertical-align:text-top;padding:6px;border:0px"><img src="/images/print_sedl.jpg"></td>
<td align="right" style="vertical-align:text-top;padding:6px;border:0px">

	<h1>NOV STATISTICS REPORT</h1>

</td></tr>
</table>

<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">
<tr><td colspan="2" style="vertical-align:text-top;padding:12px;border:0px solid #333333">

<form method="post" name="pa_form" id="pa_form">
<select name="nov" id="nov" onChange="MM_jumpMenu('parent',this,0)">
<option value="">Select the NOV Position</option>
<option value="">--------------------------</option>

<?php foreach($searchResult['data'] as $key => $searchData) { ?>
<option value="novs_stats.php?nov=<?php echo $searchData['position_ID'][0];?>" <?php if($recordData['position_ID'][0] == $searchData['position_ID'][0]){echo 'selected';}?>><?php echo $searchData['c_unit_abbrev'][0].' - '.$searchData['c_position_display'][0];?>
<?php } ?>
</select>
</form>


</td></tr>
</table>
<?php if($nov !== ''){?>
<table width="800" cellpadding="0" cellspacing="0" border="0" style="margin:0px">
<tr><td style="vertical-align:text-top;padding:10px;border:0px solid #333333">
<h2>Position Statistics as of <?php echo date("F, j Y");?>:</h2>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px" class="stub">

	<tr>
	<td colspan="2" style="vertical-align:text-top;padding:8px;border-top:8px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;width:100%"><?php echo $recordData['c_position_display'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:1px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right">SEDL Unit:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:1px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['position_workgroup'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Position Title:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['position_title'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Location:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['position_location'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Positions open:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['position_num_to_fill'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Positions filled:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['position_num_filled'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Date opened:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['nov_date_opened'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Current status:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['position_status'][0];?></td>
	</tr>

<?php if($recordData['c_num_resumes'][0] > 0){?>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Resumes received:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['c_num_resumes'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Applications pending:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['c_num_applications_pending'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Applications withdrawn:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['c_num_applications_withdrawn'][0];?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Rejected in screening:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['c_num_resumes_rejected'][0];?> (<?php $pct = ($recordData['c_num_resumes_rejected'][0]/$recordData['c_num_resumes'][0])*100;echo round($pct,0).'%';?>)</td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Sent for Mgr Review:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['c_num_resumes_sent_for_mgr_review'][0];?> (<?php $pct = ($recordData['c_num_resumes_sent_for_mgr_review'][0]/$recordData['c_num_resumes'][0])*100;echo round($pct,0).'%';?>)</td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Applications requested:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['c_num_applications_requested'][0];?> (<?php $pct = ($recordData['c_num_applications_requested'][0]/$recordData['c_num_resumes'][0])*100;echo round($pct,0).'%';?>)</td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Interviews scheduled:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:0px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['c_num_interviews_scheduled'][0];?> (<?php $pct = ($recordData['c_num_interviews_scheduled'][0]/$recordData['c_num_resumes'][0])*100;echo round($pct,0).'%';?>)</td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Interviews conducted:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $recordData['c_num_interviews_conducted'][0];?> (<?php $pct = ($recordData['c_num_interviews_conducted'][0]/$recordData['c_num_resumes'][0])*100;echo round($pct,0).'%';?>)</td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Minority applicants/interviewees:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $minority_count;?></td>
	</tr>

	<tr>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;text-align:right"">Unknown applicants/interviewees:</td>
	<td style="vertical-align:text-top;padding:8px;border-top:0px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 0px #333333;width:100%"><?php echo $unknown_count;?></td>
	</tr>

<?php }else{ ?>

	<tr>
	<td colspan="2" style="vertical-align:text-top;padding:8px;border-top:1px solid #333333; border-bottom:1px solid #333333; border-right:solid 1px #333333; border-left:solid 1px #333333;width:100%">No resumes have been received for this position.</td>
	</tr>

<?php } ?>

	</table>

<?php }?>



</td></tr>
</table>




</body>

</html>

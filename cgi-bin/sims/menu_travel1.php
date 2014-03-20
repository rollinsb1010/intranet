<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


########################################
## START: DELETE REQUEST IF NECESSARY ##
########################################
if($_GET['delete_key'] != ''){
$delete_row_ID = $_GET['delete_key'];
$delete = new FX($serverIP,$webCompanionPort);
$delete -> SetDBData('SIMS_2.fp7','travel_authorizations');
$delete -> SetDBPassword($webPW,$webUN);
$delete -> AddDBParam('-recid',$delete_row_ID);

$deleteResult = $delete -> FMDelete();

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','DELETE_TRAVEL_REQUEST_STAFF');
$newrecord -> AddDBParam('table','LEAVE_REQUESTS');
//$newrecord -> AddDBParam('object_ID',$recordData['leave_request_ID'][0]);
$newrecord -> AddDBParam('affected_row_ID',$delete_row_ID);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

}
######################################
## END: DELETE REQUEST IF NECESSARY ##
######################################


##############################################
## START: FIND TRAVEL REQUESTS FOR THIS USER ##
##############################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','travel_authorizations',12);
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('staff_ID','=='.$_SESSION['staff_ID']);
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('event_start_date','descend');

$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$_SESSION['travel_request_approval_not_required'] = $recordData['staff::no_time_leave_approval_required'][0];
############################################
## END: FIND TRAVEL REQUESTS FOR THIS USER ##
############################################
//$current_pay_period = date("m").'/'.date("t").'/'.date("Y");

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
//if ($searchResult['foundCount'] > 0) { 
?>

<html>
<head>
<title>SIMS: My Travel Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function preventDelete() { 
	var answer = confirm ("Approved travel requests cannot be deleted.")
	return false;
	
}
</script>

<script language="JavaScript">

function confirmDelete() { 
	var answer2 = confirm ("Are you sure you want to delete this travel request?")
	if (!answer2) {
	return false;
	}
}

function zoomWindow() {
window.resizeTo(1000,screen.height)
}
</script>
<style type="text/css">

body {
	width: 1000px;
	background: #FFF; /* the auto value on the sides, coupled with the width, centers the layout */
	margin-top: 0;
	margin-right: auto;
	margin-bottom: 0;
	margin-left: auto; /* ~~ CHANGE TO LEFT 0px IF WANT LEFT JUSTIFIED ~~ */
	padding-bottom: 0px;	
}


/*############### Use these styles with php and with css to line up bullets and make every other row different color.################*/

tr.even{ background-color: #ebebeb; }
tr.odd{background-color: #b6cdcf; }

td.even{ background-color: #ebebeb; border-left: 3px #006666 solid; padding:5px 10px 5px 15px; }
td.odd{background-color: #b6cdcf;  border-left: 3px #006666 solid; padding:5px 10px 5px 15px }


td.bullet_1 ul{  margin-top:0px;}
.bullet_1 { list-style-type: disc; margin-left: -12px; padding-left: 0em;
	 display:block;}
td.bullet_1 li{ margin-bottom:5px; padding-right:3px; }




td.bullet_2 ul{  margin-top:0px;  margin-left: -25px; display:block;}
.bullet_2 { list-style-type: disc; margin-left: 0px; padding-left: 0em;
	 }
td.bullet_2 li{ margin-bottom:5px; padding-right:3px;}

tr.colhead {filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='ebebeb', endColorstr='#8AC0BD'); /* for IE */
	background: -webkit-gradient(linear, left top, left bottom, from(#ebebeb), to(#8AC0BD)); /* for webkit browsers */
	background: -moz-linear-gradient(top, #ebebeb, #8AC0BD); color: #4B573C; /* for firefox 3.6+ */ }
	
.dotted-box-orc {  border: 1px #105F4D dotted; padding: 10px; background-color: #ebebeb}
	

.dottedBox 

{
	border-top-width: 2px;
	border-bottom-width: 2px;
	border-top-style: dotted;
	border-bottom-style: dotted;
	border-top-color: #0a5253;
	border-bottom-color: #0a5253;
	color: #000000;
	font-family: Verdana,Arial,Helvetica,sans-serif;
	font-size: 11px;
	line-height: 15px;
	background-color: #f8fafc;
	padding: 8px;
	margin-right: 270px;
}


td.even_last{background-color: #ebebeb; color: #4B573C;
  border-left: 3px #006666 solid;
  -moz-box-shadow: 2px 2px 5px #888;
  -moz-border-radius-bottomright: 10px;
  -webkit-box-shadow:2px 2px 5px #888;
  -webkit-border-bottom-right-radius: 10px; }
  
th.even_last{ color: #4B573C;
  -moz-box-shadow: 2px 2px 5px #888;
  -moz-border-radius-bottomright: 0px;
  -webkit-box-shadow:2px 2px 5px #888;
  -webkit-border-bottom-right-radius: 0px; }  
  
/* ##########################END##########################*/

table.col {filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='ffffff', endColorstr='#b6cdcf'); /* for IE */
	background: -webkit-gradient(linear, left top, left bottom, from(#ffffff), to(#b6cdcf)); /* for webkit browsers */
	background: -moz-linear-gradient(top, #ffffff, #b6cdcf); color: #4B573C; /* for firefox 3.6+ */ }

table.col th, td {border-bottom: solid 1px #ffffff;}
table.col th {padding:5px 5px 5px 0px; }
	
.search {filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='ffffff', endColorstr='#b6cdcf'); /* for IE */
	background: -webkit-gradient(linear, left top, left bottom, from(#ffffff), to(#b6cdcf)); /* for webkit browsers */
	background: -moz-linear-gradient(top, #ffffff, #b6cdcf); /* for firefox 3.6+ */ }
	
label { line-height:150%;}

</style>


</head>

<BODY onLoad="zoomWindow()">

<p style="width:1024px; background-color:#003745">
<img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></p>
<table cellpadding=10 cellspacing=0 border=0 style="background-color:#ffffff; bordercolor:#cccccc; width:1024px">
<tr><td>

<h1>SIMS Travel Requests</h1>

<hr>

<div class="dotted-box-orc" style="width:1004px">
	

<div class="tiny" style="float:left; margin-top:20px; padding-top:10px;padding-bottom:6px;border-width:0px;border-style:dotted;border-color:#0a5253"><strong><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</strong></div>

<div style="float:right;padding-top:10px;padding-bottom:6px; margin-right:20px"><a href="/staff/sims/travel_prefs.php" title="Update your SIMS leave preferences.">Travel Preferences</a> | <a href="my_leave_calendar.php?selected_month=<?php echo $current_pay_period;?>">My Travel Calendar</a> | <a href="/staff/sims/travel.php?action=new_dot">New Travel Request</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></div>

<div style="background-color:#ffffff; margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted;">

				<?php if($_SESSION['travel_request_submitted_staff'] == '1'){ ?>
			
				<p class="alert_small">Your travel request has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p>
				<?php $_SESSION['travel_request_submitted_staff'] = ''; ?>
			
				<?php } elseif($_SESSION['travel_request_submitted_staff'] == '2'){ ?>
			
				<p class="alert_small">There was a problem submitting your travel request, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance. | ErrorCode: <?php echo $_SESSION['errorCode'];?></p>
				<?php $_SESSION['travel_request_submitted_staff'] = ''; ?>
			
				<?php } elseif($_SESSION['travel_prefs_updated'] == '1'){ ?>
			
				<p class="alert_small">Your travel preferences have been updated.</p>
				<?php $_SESSION['travel_prefs_updated'] = ''; ?>

				<?php } elseif($_SESSION['travel_prefs_updated'] == '2'){ ?>
		
		
				<p class="alert_small">There was a problem updating your travel preferences, please contact <a href="mailto:sims@sedl.org">technical support</a>.</p>
				<?php $_SESSION['travel_prefs_updated'] = ''; ?>

			<?php } ?>
			

				<table cellpadding="5" style="width:960px; margin-top:10px;border-color:#c0c9da;border-width:1px;border-style:dotted">
					<tr valign="top" style="padding-top:5px;background-color:#c0c9da" class="colhead">
						<th style="width:10%; text-align:left;">ID</th>
						<th style="width:20%; text-align:left;">Date(s) of Travel</th>
									<th style="width:20%; text-align:left;">Event Name</th>
									<th style="width:20%; text-align:left;">Destination</th>
									<th style="width:20%; text-align:left;">Date/Time Submitted</th>
									<th style="width:20%; text-align:left;">Status</th>
			
									<th style="width:10%;">Delete</th>
									
									
									</tr>
						
	<?php if($searchResult['foundCount'] > 0){ ?>

						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						
									<tr class="<?=($i++%2==1) ? 'odd' : 'even' ?>" valign="top">
			
				   
									<td style="padding-top:5px;color:#666666"><?php echo $searchData['travel_auth_ID'][0];?></td>
									<td style="padding-top:5px;color:#666666"><?php if($searchData['leave_date_requested'][0] != $searchData['return_date_requested'][0]){echo $searchData['leave_date_requested'][0].' - '.$searchData['return_date_requested'][0];} else { echo $searchData['leave_date_requested'][0];}?></td>
									<td style="padding-top:5px;color:#666666"><a href="/staff/sims/travel.php?travel_auth_ID=<?php echo $searchData['travel_auth_ID'][0];?>&action=<?php if($searchData['multi_dest'][0] == 'yes'){ ?>view_multi<?php }else{?>view<?php }?>&app=<?php echo $searchData['approval_status'][0];?>"><?php echo stripslashes($searchData['event_name'][0]);?></a></td>
									<td style="padding-top:5px;color:#666666"><?php if($searchData['multi_dest'][0] == 'yes'){ echo $searchData['event_venue_city1'][0];?>, <?php echo $searchData['event_venue_state1'][0];?>**<?php } else { echo $searchData['event_venue_city'][0];?>, <?php echo $searchData['event_venue_state'][0];}?></td>
									<td style="padding-top:5px;color:#666666"><?php echo $searchData['creation_timestamp'][0];?></td>

						
						
						
									<?php if($searchData['c_lock_status'][0] =='1'){ ?>
									<td style="padding-top:5px;color:#666666"><?php if($searchData['approval_status'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status'][0];?></font></td>
									<td style="padding-top:5px;color:#666666"><img src="/staff/sims/images/padlock.jpg" border="0" title="This travel request is locked."></td>
									<?php }else{ ?>
									<td style="padding-top:5px;color:#666666"><?php if($searchData['approval_status'][0]=='Approved'){ ?><font color="blue"><?php } else { ?><font color="red"><?php }?><?php echo $searchData['approval_status'][0];?></font></td>
									<td style="padding-top:5px;color:#666666"><a href="menu_travel.php?delete_key=<?php echo $searchData['c_row_ID_cwp'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/trashcan.jpg" border="0"></a></td>
									<?php } ?>
									</tr>
			
						<?php } ?>

<?php } else { ?>


									<tr>
									<td style="padding-top:5px;color:#666666" colspan="8">
									No records found.</td>
									</tr>


<?php } ?>

							 </table>&nbsp;**<span class="tiny">indicates multi-destination travel.</span>

			</div>


		</div>




</td></tr>
</table>


</body>

</html>

<?php //} else { ?>

<!--No records found.-->

<?php //} ?>

<?php //} else { ?>



<?php //} ?>



									
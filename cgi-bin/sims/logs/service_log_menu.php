<?php // LOGIN AND BEGIN SESSION
session_start();

##INCLUDE FX.PHP FUNCTIONALITY AND SERVER CREDENTIALS##
include_once('../FX/FX.php');
include_once('../FX/server_data.php');

//if($_SESSION['user_ID'] !== 'ewaters'){
//header('Location: http://www.sedl.org/staff/');
//}
$src = 'intr';

include_once('../sims_checksession.php');

if($_SESSION ['staff_ID'] == ''){

$_SESSION['staff_ID'] = $_COOKIE['staffid'];

#####################################################
## START: FIND CONTACT RECORD FOR THIS USER
#####################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff_cwp_svc_log');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('staff_ID','=='.$_SESSION ['staff_ID']);

$searchResult = $search -> FMFind();
echo $searchResult['errorCode'];
echo $searchResult['foundCount'];
//print_r($search);
$recordData = current($searchResult['data']);
#####################################################
## END: FIND CONTACT RECORD FOR THIS USER
#####################################################

$_SESSION['user_ID'] = $recordData['sims_user_ID'][0];
$_SESSION['PrimarySEDLWorkgroup'] = $recordData['primary_SEDL_workgroup'][0];
$_SESSION['svc_log_admin_wg'] = $recordData['cwp_sims_access_staff_svc_log_admin_wg'][0];
$_SESSION['svc_log_admin_sedl'] = $recordData['cwp_sims_access_staff_svc_log_admin_sedl'][0];
$_SESSION['svc_log_admin_prgms'] = $recordData['cwp_sims_access_staff_svc_log_admin_pgms'][0];
$_SESSION['svc_log_admin_spvsr'] = $recordData['cwp_sims_access_staff_svc_log_admin_spvsr'][0];
$_SESSION['svc_log_admin_allow_surrogates'] = $recordData['cwp_sims_access_staff_svc_log_allow_surrogate_entries'][0];

//echo '<p>HELLO!';
//echo 'surrogates: '.$_SESSION['svc_log_admin_allow_surrogates'];
}

//echo '<p>svc_log_admin_sedl: '.$_SESSION['svc_log_admin_sedl'];
//echo '<p>svc_log_admin_wg: '.$_SESSION['svc_log_admin_wg'];
//echo '<p>svc_log_admin_prgms: '.$_SESSION['svc_log_admin_prgms'];
//echo '<p>staff_ID: '.$_SESSION['staff_ID'];

//echo '<p>status: '.$_SESSION['status'];
//echo '<p>session: '.$_SESSION['session'];
//exit;
$_SESSION['session'] = 'active';
$_SESSION['status'] = 'active';


if((isset($_SESSION['status'])) && ($_SESSION['session'] == 'active')) { // IF THE USER IS LOGGED IN AND HAS A VALID SESSION 

################################
## START: GET FMP VALUE-LISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('CC_dms.fp7','sedl_service_log');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
##############################
## END: GET FMP VALUE-LISTS ##
##############################


//if($_SESSION['svc_log_admin_sedl'] == 'Yes'){
#####################################################
## START: FIND STAFF IDs FOR CURRENT SEDL STAFF ##
#####################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','staff_table_sims_ID_workgroup','all');
$search3 -> SetDBPassword($webPW,$webUN);
//$search3 -> AddDBParam('primary_SEDL_workgroup','=='.$_SESSION ['PrimarySEDLWorkgroup']);
$search3 -> AddDBParam('current_employee_status','=='.'SEDL Employee');

$search3 -> AddSortParam('sims_user_ID','ascend');


$searchResult3 = $search3 -> FMFind();
//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//print_r($search3);
$recordData3 = current($searchResult3['data']);
#####################################################
## END: FIND STAFF IDs FOR CURRENT SEDL STAFF ##
#####################################################
//echo '<p>HELLO2!';
//}

##############################################
### START: GET SEDL BUDGET CODES FROM SIMS ###
##############################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','budget_codes_fund_year_only','all');
$search2 -> SetDBPassword($webPW,$webUN);

//$search2 -> AddDBParam('created_by','=='.$_SESSION['user_ID']);
$search2 -> AddDBParam('c_svc_log_select_list','1');
$search2 -> AddSortParam('c_fund_year','ascend');
$searchResult2 = $search2 -> FMFind();
//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//$recordData2 = current($searchResult2['data']);

$i=0;
foreach($searchResult2['data'] as $key => $searchData2) { 

$fundyear[$i] = $searchData2['c_fund_year'][0].' - '.$searchData2['BudgetCodeDescription'][0];
$i++;

} 

$fund_year_unique = array_unique($fundyear);
############################################
### END: GET SEDL BUDGET CODES FROM SIMS ###
############################################

######################################################
## START: GET WORKGROUP PROJECTS for the drop-down list
######################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('CC_dms.fp7','cc_projects', 'all');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('category','staff','neq');

$search4 -> AddSortParam('cc','ascend');
$search4 -> AddSortParam('project_ID','ascend');
//$search4 -> AddSortParam('project_number','ascend');

$searchResult4 = $search4 -> FMFind();

//echo $searchResult4['errorCode'];
//echo $searchResult4['foundCount'];
//$recordData4 = current($searchResult4['data']);
###################################################
## END: GET WORKGROUP PROJECTS for the drop-down list
###################################################





/*
if($_SESSION['svc_log_admin_wg'] == 'Yes'){
#####################################################
## START: FIND STAFF IDs FOR THIS WORKGROUP ##
#####################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','staff_table_sims_ID_workgroup','all');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('primary_SEDL_workgroup','=='.$_SESSION ['PrimarySEDLWorkgroup']);
$search2 -> AddDBParam('current_employee_status','=='.'SEDL Employee');

$search2 -> AddSortParam('sims_user_ID','ascend');


$searchResult2 = $search2 -> FMFind();
//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
//print_r($search2);
$recordData2 = current($searchResult2['data']);
#####################################################
## END: FIND STAFF IDs FOR THIS WORKGROUP
#####################################################
//echo '<p>HELLO!';
}

if($_SESSION['svc_log_admin_prgms'] == 'Yes'){
#######################################################
## START: FIND STAFF IDs FOR SEDL Program Staff only ##
#######################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','staff_table_sims_ID_workgroup','all');
$search4 -> SetDBPassword($webPW,$webUN);
//$search4 -> AddDBParam('primary_SEDL_workgroup','=='.$_SESSION ['PrimarySEDLWorkgroup']);
$search4 -> AddDBParam('current_employee_status','=='.'SEDL Employee');
$search4 -> AddDBParam('c_program_area_staff','yes');

$search4 -> AddSortParam('sims_user_ID','ascend');


$searchResult4 = $search4 -> FMFind();
//echo '<p>$searchResult4[errorCode]: '.$searchResult4['errorCode'];
//echo '<p>$searchResult4[foundCount]: '.$searchResult4['foundCount'];
//print_r($search4);
$recordData4 = current($searchResult4['data']);
#####################################################
## END: FIND STAFF IDs FOR SEDL Program Staff only ##
#####################################################
//echo '<p>HELLO2!';
}

if($_SESSION['svc_log_admin_spvsr'] == 'Yes'){
################################################################
## START: FIND STAFF IDs FOR STAFF SUPERVISED BY THIS MANAGER ##
################################################################
$search5 = new FX($serverIP,$webCompanionPort);
$search5 -> SetDBData('SIMS_2.fp7','staff_table_sims_ID_workgroup','all');
$search5 -> SetDBPassword($webPW,$webUN);
$search5 -> AddDBParam('immediate_supervisor_sims_user_ID','=='.$_SESSION ['user_ID']);
$search5 -> AddDBParam('current_employee_status','=='.'SEDL Employee');

$search5 -> AddSortParam('sims_user_ID','ascend');


$searchResult5 = $search5 -> FMFind();
//echo $searchResult5['errorCode'];
//echo $searchResult5['foundCount'];
//print_r($search5);
$recordData5 = current($searchResult5['data']);
##############################################################
## END: FIND STAFF IDs FOR STAFF SUPERVISED BY THIS MANAGER ##
##############################################################
//echo '<p>HELLO!';
}
*/
?>







<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Style-Type" content="text/css">
<META NAME="description" CONTENT="The Texas Comprehensive Center (TXCC), funded by the U.S. Department of Education, provides high-quality technical assistance in the state of Texas.">
<META NAME="keywords" lang="en-us" CONTENT="No Child left behind, NCLB, Texas Comprehensive Center, Teaching, resources, spelling, grants">
<META NAME="author" content="Vicki Dimock">
<meta name="Copyright" content="SEDL">
<meta name="Robots" content="index,follow"> 
<title>SEDL - Staff Service Log - Main Menu</title> <!-- page title -->
<link rel="shortcut icon" href="http://www.sedl.org/imagesN/SEDL.ico">
<link href="http://www.sedl.org/css/sims2007.css" rel="stylesheet" type="text/css">
<!-- BEGIN: header/footer stylesheet -->

<style type="text/css">

		.form-text td {
		font-family: Verdana,Arial,Helvetica,sans-serif;
		font-size: 11px;
		color: #333333;
		padding-left:0px;
		padding-right:5px;
		padding-top:3px;
		padding-bottom:3px;
		vertical-align: text-top;
		border-width:0px
		}
		


</style>

<!-- END: header/footer stylesheet -->

<script language="JavaScript" type="text/JavaScript">
//<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->

function toggle1() {
	var ele = document.getElementById("toggleText");
	var text = document.getElementById("displayText");
	if(ele.style.display == "block") {
    	ele.style.display = "none";
		text.innerHTML = "Advanced search";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "Basic search";
	}
} 

</script>


</head>

<body bgcolor="#101229">	

<!-- BEGIN: header server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/header_sedl_log_svc.html');?>

<!-- END: header server-side include -->

<!-- BEGIN: PAGE CONTENT -->

<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff" width="760">

<tr><td>

<div style="float:right;padding-top:0px;padding-bottom:6px"><a href="mailto:service_log@sedl.org?subject=SEDL-Staff-Service-Log-Comments">Suggestions/Comments</a> | <a href="http://www.sedl.org/staff/sims/logs/service_log_menu.php"></a>Main Menu | <a href="http://www.sedl.org/staff">Return to intranet</a></div>

			<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px;background-color:#ebebeb">
			
			<tr><td class="body" valign="top"><font face="verdana, helvetica, arial" style="color:#101229">
			<h2 class="txcc">SEDL - Staff Service Log</h2></font></td><td align="right" style="padding:0px"><div style="float:right;background-color:#ffffff;padding:4px;margin:0px;border-width:1px;border-style:dotted;border-color:#0a5253"><strong>Current User</strong>: <?php echo $_SESSION['user_ID'].' | '.$_SESSION['PrimarySEDLWorkgroup'];?></div>
			</td></tr>
				
			<tr><td valign="top" colspan="2">
					
					
					<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
		
					<tr><td>				
							
							<table cellpadding=10 cellspacing=0 border=1 bordercolor="#cccccc" width="100%" valign="top" class="sims" style="margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">
							
							<tr><td class="body" nowrap style="background-color:#c0c9da"><strong>MAIN MENU</strong></td></tr>
		
							<tr><td class="body" valign="top">
							<div style="width:48%;float:right;margin-left:10px;margin-bottom:10px;padding-top:0px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">
							<div style="float:right;margin:0px;padding-top:14px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:0px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb"><a href="javascript:toggle1();" id="displayText">Advanced search</a></div>
								<h2 class="txcc" style="color:#101229;padding-top:0px">Search the log</h2>
								Select from the options below to search within your service log entries.
								
								

									  <form method="get" action="service_log.php">
									  
									  <input type="hidden" name="action" value="search_go">
									  <p style="padding-top:12px;padding-bottom:12px;border-top-width:1px;border-top-color:#bbbbbb;border-top-style:dotted;border-bottom-width:1px;border-bottom-color:#bbbbbb;border-bottom-style:dotted">Search by <strong>log ID:</strong> <input type="text" name="service_log_ID" size="30">
									  </p>

									  <input type="hidden" name="action" value="search_go">
									  <p style="padding-top:0px;padding-bottom:12px;border-top-width:0px;border-top-color:#bbbbbb;border-top-style:dotted;border-bottom-width:1px;border-bottom-color:#bbbbbb;border-bottom-style:dotted">Search by <strong>keyword:</strong> <input type="text" name="keyword" size="30"><br>&nbsp;<br>
									  <span class="tiny" style="padding:2px;border-width:1px;border-color:#bbbbbb;border-style:solid;margin-left:20px;background-color:#ffffff">Searches within the Activity Name and Comments fields.</span>
									  </p>

									  <div id="toggleText" style="display:none;margin-top:10px;margin-bottom:0px;margin-left:0px;margin-right:0px;padding:8px;border-width:0px;border-style:dotted;border-color:#0a5253;background-color:#ffffff">
									  <strong>Advanced search</strong>
									  
									  <p style="padding-top:6px;border-top-width:1px;border-top-color:#bbbbbb;border-top-style:dotted"><div style="float:right;margin:0px;padding:0px;border-width:0px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;color:#ff0000"><strong>ISP ONLY</strong></div>
									  Search by <strong>priority area</strong>:<br> 
									  	<div style="background-color:#ffffff;padding:4px">
									  	
									  	<select name="wg_usde_priority_area" class="body">
										<option value="">
										
										<option value="01">Early Learning
										<option value="02">College and Career Ready Standards and Assessments
										<option value="03">Low Performing Schools 
										<option value="04">Rigorous Instructional Pathways
										<option value="05">Innovative Approaches 
										<option value="06">Highly Effective Teachers and Leaders
										<option value="07">Data-Based Decision Making
										<option value="08">Building State Capacity
										

										</select>
									  	
								  		</div>
									  </p>

									  <p style="padding-top:6px;border-top-width:1px;border-top-color:#bbbbbb;border-top-style:dotted"><div style="float:right;margin:0px;padding:0px;border-width:0px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;color:#ff0000"><strong>ISP ONLY</strong></div>
									  Search by <strong>project</strong>:<br> 
									  	<div style="background-color:#ffffff;padding:4px">
									  	
									  	<select name="wg_project_ID" class="body">

										<option value="">
										<option value="">-------------------------------
										<option value="">## TXCC Projects (YEAR 7) ##
										<option value="">-------------------------------

										<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
				
											<?php if(($searchData4['cc'][0] == 'txcc')&&($searchData4['contract_yr'][0] == '7')) { ?>
					
												<option value="<?php echo $searchData4['project_ID'][0];?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData4['project_number'][0].' - ';?><?php echo stripslashes($searchData4['project_name'][0]); ?>
					
											<?php } ?>
				
										<?php } ?>
										
										<option value="">-------------------------------
										<option value="">## TXCC Projects (YEAR 6) ##
										<option value="">-------------------------------

										<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
				
											<?php if(($searchData4['cc'][0] == 'txcc')&&($searchData4['contract_yr'][0] == '6')) { ?>
					
												<option value="<?php echo $searchData4['project_ID'][0];?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData4['project_number'][0].' - ';?><?php echo stripslashes($searchData4['project_name'][0]); ?>
					
											<?php } ?>
				
										<?php } ?>

										<option value="">---------------------------
										<option value="">## SECC Projects ##
										<option value="">---------------------------
										<option value="">==> GOAL 1
										<option value="">---------------------------
				
										<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
				
											<?php if($searchData4['goal'][0] == '1') { ?>

											<?php if ($project_num !== substr($searchData4['project_number'][0],0,3)){ echo '<option value="">';}?>
					
												<option value="<?php echo $searchData4['project_ID'][0];?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData4['project_number'][0].' - '.$searchData4['project_name'][0]; ?>
	
												<?php $project_num = substr($searchData4['project_number'][0],0,3); ?>
				
											<?php } ?>

										<?php } ?>
						
				
				
										<option value="">---------------------------
										<option value="">==> GOAL 2
										<option value="">---------------------------
				
										<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
				
											<?php if($searchData4['goal'][0] == '2') { ?>

											<?php if ($project_num !== substr($searchData4['project_number'][0],0,3)){ echo '<option value="">';}?>
					
												<option value="<?php echo $searchData4['project_ID'][0];?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php if(strlen($searchData4['project_number'][0].' - '.$searchData4['project_name'][0])>35){echo substr($searchData4['project_number'][0].' - '.$searchData4['project_name'][0],0,35).'...';}else{echo $searchData4['project_number'][0].' - '.$searchData4['project_name'][0];}?>
					
												<?php $project_num = substr($searchData4['project_number'][0],0,3); ?>
												

											<?php } ?>
				
										<?php } ?>
		
										</select>
									  	
								  		</div>
									  </p>
									  
									  <p style="padding-top:6px;border-top-width:1px;border-top-color:#bbbbbb;border-top-style:dotted">
									  Search by <strong>budget code/funding stream</strong>:<br> 
									  	<div style="background-color:#ffffff;padding:4px">
									  	
									  	<select name="project_funding_stream" class="body">
										<option value="">
										
										<?php foreach($fund_year_unique as $current) { ?>
										<option value="<?php echo $current;?>"> <?php if(strlen($current)>35){echo substr($current,0,35).'...';}else{echo $current;}?>
										<?php } ?>

										</select>
									  	
								  		</div>
									  </p>


									  
									  <p style="padding-top:6px;border-top-width:1px;border-top-color:#bbbbbb;border-top-style:dotted">
									  Search by <strong>date range</strong>:<br> 
									  	<div style="background-color:#ffffff;padding:4px">
									  	<table>
									  	<tr><td style="border-width:0px;padding:4px">
										 FROM</td><td style="border-width:0px;padding:4px" nowrap>			 
										<select name="start_date_m" class="body">
										<option value="">Month
										<option value="">-------
										<?php foreach($v1Result['valueLists']['month_num'] as $key => $value) { ?>
										<option value="<?php echo $value;?>"> <?php echo $value; ?>
										<?php } ?>
										</select>
										
										 
										<select name="start_date_d" class="body">
										<option value="">Day
										<option value="">-------
										<?php foreach($v1Result['valueLists']['day_num'] as $key => $value) { ?>
										<option value="<?php echo $value;?>"> <?php echo $value; ?>
										<?php } ?>
										</select> 
										
										 
										<select name="start_date_y" class="body">
										<option value="">Year
										<option value="">-------
										<?php foreach($v1Result['valueLists']['year_num'] as $key => $value) { 
										if($value >= (date("Y")-2)){ ?><option value="<?php echo $value;?>"> <?php echo $value; } ?>
										<?php } ?>
										</select> 
										
										</td></tr>
										<tr><td style="border-width:0px;padding:4px">TO</td><td style="border-width:0px;padding:4px" nowrap> 
						
										<select name="end_date_m" class="body">
										<option value="">Month
										<option value="">-------
										<?php foreach($v1Result['valueLists']['month_num'] as $key => $value) { ?>
										<option value="<?php echo $value;?>"> <?php echo $value; ?>
										<?php } ?>
										</select>
										
										 
										<select name="end_date_d" class="body">
										<option value="">Day
										<option value="">-------
										<?php foreach($v1Result['valueLists']['day_num'] as $key => $value) { ?>
										<option value="<?php echo $value;?>"> <?php echo $value; ?>
										<?php } ?>
										</select> 
										
										 
										<select name="end_date_y" class="body">
										<option value="">Year
										<option value="">-------
										<?php foreach($v1Result['valueLists']['year_num'] as $key => $value) { 
										if($value >= (date("Y")-2)){ ?><option value="<?php echo $value;?>"> <?php echo $value; } ?>
										<?php } ?>
										</select>
										</td></tr>
										</table>
								  		</div>
									  </p>
									  
									  <p style="padding-top:6px;border-top-width:1px;border-top-color:#bbbbbb;border-top-style:dotted">
									  Search by <strong>activity type</strong>:<br> 
									  	<div style="background-color:#ffffff;padding:4px">
									  	
									  	<select name="activity_type" class="body">
										<option value="">
										
										<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_type'] as $key => $value) { ?>
										<option value="<?php echo $value;?>"> <?php echo $value; ?>
										<?php } ?>
										</select>
									  	
								  		</div>
									  </p>

									  <p style="padding-top:6px;border-top-width:1px;border-top-color:#bbbbbb;border-top-style:dotted">
									  Search by <strong>contact method</strong>:<br> 
									  	<div style="background-color:#ffffff;padding:4px">
									  	
									  	<select name="contact_method" class="body">
										<option value="">
										
										<?php foreach($v1Result['valueLists']['sedl_svc_log_contact_method'] as $key => $value) { ?>
										<option value="<?php echo $value;?>"> <?php echo $value; ?>
										<?php } ?>
										</select>
									  	
								  		</div>
									  </p>

									  <p style="padding-top:6px;border-top-width:1px;border-top-color:#bbbbbb;border-top-style:dotted">
									  Search by <strong>state</strong>:<br> 
									  	<div style="background-color:#ffffff;padding:4px">
									  	
									  	<select name="state" class="body">
										<option value="">
										
										<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_states'] as $key => $value) { ?>
										<option value="<?php echo $value;?>"> <?php echo $value; ?>
										<?php } ?>
										</select>
									  	
								  		</div>
									  </p>

									  </div>


									  <p style="float:right;padding-right:10px">
									  <input type="submit" name="submit" value="Search">
									  </p>																					
									  </form>

								
							</div>


								<h2 class="txcc" style="color:#101229">Enter or view log entries</h2>

								<?php //if($_SESSION['tadds_user_ID'] == 'ewaters'){?>								
										<p><a href="service_log.php?action=new">Submit new log entry</a><br>Use this link to enter a new log entry in the database.</p>
										<p><a href="service_log.php?action=show_mine">Show my records</a><br>Use this link to show a list of all log entries for your account.</p>

								<?php if($_SESSION['svc_log_admin_wg'] == 'Yes'){ ?>

										<p style="width:45%;margin:0px;padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#fff6bf">
										<strong>Manager Options</strong> (<?php echo $_SESSION['PrimarySEDLWorkgroup'];?> staff)<br>&nbsp;<br>
										<a href="service_log.php?action=show_workgroup">Show <?php echo $_SESSION['PrimarySEDLWorkgroup'];?> log entries</a><br>Use this link to show a list of all log entries relating to <?php echo $_SESSION['PrimarySEDLWorkgroup'];?> work.<br>&nbsp;<br>
										

									  Search by <strong>staff member</strong>:  
									  	
									  	
									  	<select name="created_by"  onChange="MM_jumpMenu('parent',this,0)" class="body">
										<option value="">

										<?php foreach($searchResult3['data'] as $key => $searchData3) { 
										if($searchData3['primary_SEDL_workgroup'][0] == $_SESSION ['PrimarySEDLWorkgroup']){
										?>
										<option value="service_log.php?action=search_go_admin&created_by=<?php echo $searchData3['sims_user_ID'][0];?>"> <?php echo $searchData3['sims_user_ID'][0];?>
										<?php }} ?>
										</select>
								  		
									  </p>

								<?php } ?>

								<?php if($_SESSION['svc_log_admin_sedl'] == 'Yes'){ ?>

										<p style="width:45%;margin-top:8px;padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#fff6bf">
										<strong>Admin Options</strong> (All staff)<br>&nbsp;<br>
										

									  Search by <strong>staff member</strong>:  
									  	
									  	
									  	<select name="created_by"  onChange="MM_jumpMenu('parent',this,0)" class="body">
										<option value="">

										<?php foreach($searchResult3['data'] as $key => $searchData3) { ?>
										<option value="service_log.php?action=search_go_admin&created_by=<?php echo $searchData3['sims_user_ID'][0];?>"> <?php echo $searchData3['sims_user_ID'][0];?>
										<?php } ?>
										</select>
								  		
									  </p>

								<?php } ?>

								<?php if($_SESSION['svc_log_admin_prgms'] == 'Yes'){ ?>

										<p style="width:45%;margin-top:8px;padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#fff6bf">
										<strong>Admin Options</strong> (Program staff)<br>&nbsp;<br>
										

									  Search by <strong>staff member</strong>:  
									  	
									  	
									  	<select name="created_by"  onChange="MM_jumpMenu('parent',this,0)" class="body">
										<option value="">

										<?php foreach($searchResult3['data'] as $key => $searchData3) { 
										if($searchData3['c_program_area_staff'][0] == 'yes'){
										?>
										
										<option value="service_log.php?action=search_go_admin&created_by=<?php echo $searchData3['sims_user_ID'][0];?>"> <?php echo $searchData3['sims_user_ID'][0];?>
										<?php }} ?>
										</select>
								  		
									  <br>&nbsp;<br>Search by <strong>workgroup</strong>:  
									  	
									  	
									  	<select name="sedl_workgroup"  onChange="MM_jumpMenu('parent',this,0)" class="body">
										<option value="">

										<option value="service_log.php?action=search_go_admin&sedl_workgroup=R&E"> R&E
										<option value="service_log.php?action=search_go_admin&sedl_workgroup=ISP"> ISP
										<option value="service_log.php?action=search_go_admin&sedl_workgroup=AFC"> AFC
										<option value="service_log.php?action=search_go_admin&sedl_workgroup=DRP"> DRP

										</select>


									  </p>

								<?php } ?>

								<?php if($_SESSION['svc_log_admin_spvsr'] == 'Yes'){ ?>

										<p style="width:45%;margin-top:8px;padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#fff6bf">
										<strong>Manager Options</strong> (Staff you supervise)<br>&nbsp;<br>
										

									  Search by <strong>staff member</strong>:  
									  	
									  	
									  	<select name="created_by"  onChange="MM_jumpMenu('parent',this,0)" class="body">
										<option value="">

										<?php foreach($searchResult3['data'] as $key => $searchData3) { 
										if($searchData3['immediate_supervisor_sims_user_ID'][0] == $_SESSION ['user_ID']){
										?>
										<option value="service_log.php?action=search_go_admin&created_by=<?php echo $searchData3['sims_user_ID'][0];?>"> <?php echo $searchData3['sims_user_ID'][0];?>
										<?php }} ?>
										</select>
								  		
									  </p>

								<?php } ?>


								<?php if($_SESSION['svc_log_admin_allow_surrogates'] == 'Yes'){ ?>

										<p style="width:45%;margin-top:8px;padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#fff6bf">
										<strong>Admin Options</strong> (Surrogates)<br>&nbsp;<br>
										

										<a href="service_log.php?action=search_go_admin&created_by=<?php echo $_SESSION ['user_ID'];?>&surrogate=1">Show surrogate log entries</a><br>Use this link to show a list of all log entries you created as a surrogate for another person.<br>&nbsp;<br>
								  		
									  </p>

								<?php } ?>









								<?php //}?>								


							
										
							</td></tr>
								
							</table>
							
					</td></tr>	
						
					</table>
				
			</td></tr>
				
			<tr><td colspan=2 bgcolor="ffffff">&nbsp;</td></tr>
				
			</table>
		
</td></tr>
</table>

<!-- END: PAGE CONTENT -->

<!-- BEGIN: footer server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/footer_sedl_log_svc.html');?>

<!-- END: footer server-side include -->





</body>

</html>




<?php } else { 
include_once('http://www.sedl.org/staff');

} ?>

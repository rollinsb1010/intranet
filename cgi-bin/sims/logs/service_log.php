<?php
session_start();

###CHECK TO SEE IF THE LOGIN SESSION IS VALID###

//if(!isset($_SESSION['contact_ID'])) {
//include_once('cc_network_login.php');

//}else{

include_once('../FX/FX.php');
include_once('../FX/server_data.php');





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
##############################################################################################################################
##############################################################################################################################
##############################################################################################################################


if($action == 'new') {

$thismonth = date("m");
$thisday = date("d");
$thisyear = date("Y");
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

###################################################
## START: GET FMP VALUE-LISTS FOR STAFF SIMS IDs ##
###################################################
$v2 = new FX($serverIP,$webCompanionPort);
$v2 -> SetDBData('SIMS_2.fp7','staff_sims_IDs');
$v2 -> SetDBPassword($webPW,$webUN);
$v2Result = $v2 -> FMView();
#################################################
## END: GET FMP VALUE-LISTS FOR STAFF SIMS IDs ##
#################################################

#######################################################
### START: FIND LOG ID'S FOR THIS USER'S ACTIVITIES ###
#######################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('CC_dms.fp7','sedl_service_log_IDs_only','all');
$search -> SetDBPassword($webPW,$webUN);

$search -> AddDBParam('c_created_by_project_lead',$_SESSION['user_ID']);
//$search -> AddDBParam('cc_host','TXCC');
$search -> AddSortParam('activity_begin_date','descend');
$searchResult = $search -> FMFind();
//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);
#####################################################
### END: FIND LOG ID'S FOR THIS USER'S ACTIVITIES ###
#####################################################

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

###############################################################
### START: GET ACTIVITY TYPE VALUES FOR VALUE-LIST ###
###############################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('CC_dms.fp7','sedl_service_log_activity_types','all');
$search3 -> SetDBPassword($webPW,$webUN);

//$search3 -> AddSortParam('resource_ID','ascend');
//$search3 -> AddSortParam('item','ascend');

$searchResult3 = $search3 -> FMFindall();

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
$recordData3 = current($searchResult3['data']);
#############################################################
### END: GET ACTIVITY TYPE VALUES FOR VALUE-LIST ###
#############################################################

$cols = 2;
$count = 0;

//if($recordData['wg_project_ID'][0] != ''){ // GET WORKGROUP PROJECTS FOR DROP-DOWN LIST - ESS STAFF ONLY
######################################################
## START: GET WORKGROUP PROJECTS for the drop-down list
######################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('CC_dms.fp7','cc_projects', 'all');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('c_active_status','Active');

$search4 -> AddSortParam('cc','ascend');
//$search4 -> AddSortParam('project_ID','ascend');
$search4 -> AddSortParam('project_number','ascend');

$searchResult4 = $search4 -> FMFind();

//echo $searchResult4['errorCode'];
//echo $searchResult4['foundCount'];
//$recordData4 = current($searchResult4['data']);
###################################################
## END: GET WORKGROUP PROJECTS for the drop-down list
###################################################
//}


?>




<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="en-us">
<META NAME="description" CONTENT="The Texas Comprehensive Center (TXCC), funded by the U.S. Department of Education, provides high-quality technical assistance in the state of Texas to the Texas Education Agency.">
<META NAME="keywords" lang="en-us" CONTENT="No Child left behind, NCLB, Texas Comprehensive Center, SEDL, Teaching, resources, spelling, grants">
<META NAME="author" content="Vicki Dimock">
<meta name="Copyright" content="SEDL">
<meta name="Robots" content="index,follow"> 
<title>SEDL - Staff Service Log</title> <!-- page title -->
<link rel="shortcut icon" href="http://www.sedl.org/imagesN/SEDL.ico">
<link href="http://www.sedl.org/css/sims2007.css" rel="stylesheet" type="text/css">
<!script language="javascript" type="text/javascript" src="http://txcc.sedl.org/orc/common/tiny_mce/jscripts/tiny_mce/tiny_mce.js"><!/script>
<script language="javascript" type="text/javascript" src="http://www.sedl.org/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script language="JavaScript">

		
		function UpdateSelect()
		{
		select_value = "";
		select_value = document.form2.activity_intensity.value;
		var id = 'multiple_logs';
		var obj = '';
		obj = (document.getElementById) ? document.getElementById(id) : ((document.all) ? document.all[id] : ((document.layers) ? document.layers[id] : false));
		
		if(select_value == ""){
		  obj.style.display = 'none';
		
		} else if(select_value == "multiple/ongoing"){
		  // alert("You chose Journal article.");
		  // return false;
		  obj.style.display = 'table-row';
		}
		else
		{
		  obj.style.display = 'none';
		}
		
		}


		function UpdateSelect2()
		{
		select_value2 = "";
		select_value2 = document.form2.activity_location_scope.value;
		var id2 = 'activity_location_scope_states';
		var obj2 = '';
		obj2 = (document.getElementById) ? document.getElementById(id2) : ((document.all) ? document.all[id2] : ((document.layers) ? document.layers[id2] : false));
		
		if(select_value2 == ""){
		  obj2.style.display = 'none';
		
		} else if(select_value2 == "Regional" || select_value2 == "State" || select_value2 == "Local"){
		  // alert("You chose Journal article.");
		  // return false;
		  obj2.style.display = 'table-row';
		}
		else
		{
		  obj2.style.display = 'none';
		}
		
		}

		function UpdateSelect3()
		{
		select_value3 = "";
		select_value3 = document.form2.project_funding_stream.value;
		var id3 = 'isp_extra_fields';
		var obj3 = '';
		obj3 = (document.getElementById) ? document.getElementById(id3) : ((document.all) ? document.all[id3] : ((document.layers) ? document.layers[id3] : false));
		
		if(select_value3 == ""){
		  obj3.style.display = 'none';
		
		} else if(select_value3.substring(0,4) == "0305" || select_value3.substring(0,4) == "0315" || select_value3.substring(0,4) == "0202" || select_value3.substring(0,4) == "0212"){
		  // alert("hello");
		  // return false;
		  obj3.style.display = 'table-row';
		}
		else
		{
		  obj3.style.display = 'none';
		}
		
		}

		function UpdateSelect4()
		{
		select_value4 = "";
		select_value4 = document.form2.wg_project_ID.value;
		var id4 = 'txcc_tea_partners';
		var obj4 = '';
		obj4 = (document.getElementById) ? document.getElementById(id4) : ((document.all) ? document.all[id4] : ((document.layers) ? document.layers[id4] : false));
		
		if(select_value4 == ""){
		  obj4.style.display = 'none';
		
		} else if(select_value4 == "101"){
		  // alert("hello");
		  // return false;
		  obj4.style.display = 'table-row';
		}
		else
		{
		  obj4.style.display = 'none';
		}
		
		}




	<!-- 
	function showMe (it, box) { 
	  var vis = (box.checked) ? "block" : "none"; 
	  document.getElementById(it).style.display = vis;
	} 
	//--> 

</script>


<script language="javascript" type="text/javascript">     


<!--

function win1() {
    window.open("service_log_client_search.php?action=search","Window1","menubar=yes,width=500,height=700,toolbar=no, scrollbars=1");
}


//-->
</script>

<script language="javascript" type="text/javascript">     
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	plugins : "spellchecker,paste",
	gecko_spellcheck : true,
	theme_advanced_buttons1_add : "pastetext,pasteword",
	theme_advanced_buttons3_add : "spellchecker",
	invalid_elements : "style,span",
    force_br_newlines : true,
    force_p_newlines : false,
	paste_auto_cleanup_on_paste : true,
 	theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
	apply_source_formatting : true,
	convert_urls : false
});
</script>

<script language="JavaScript">
<!--
function checkFields() { 



// Activity Intensity
		if (document.form2.activity_intensity.value == "") {
			alert("Please select the Activity Intensity (1).");
			document.form2.activity_intensity.focus();
			return false;	}


// Activity Name
		if (document.form2.activity_name.value == "") {
			alert("Please enter an Activity Name (2).");
			document.form2.activity_name.focus();
			return false;	}


// Project Funding Stream
		if (document.form2.project_funding_stream.value == "") {
			alert("Please select the Project Funding Stream (3).");
			document.form2.project_funding_stream.focus();
			return false;	}


// Activity Type (checkbox validation)
	var activity_type_count = "";
	for (i=0;i < 10;i++) {
		if (document.form2.activity_type[i].checked !== false) {
			activity_type_count = "yes";
		}
	}
	if (activity_type_count == "") {
		alert("Please select at least one Activity Type (4).");
		return false;
	}

//		alert("Hello.");

}	
// -->




</script>


<SCRIPT language="JavaScript">

function setTitle(activityName)
{
	document.form2.activity_name.value = activityName;

  //return true;
}
</SCRIPT>


<style type="text/css">

input:focus, textarea:focus {
background-color:#fcfacf;
}
</style>


</head>

<body bgcolor="#101229" onLoad="UpdateSelect(); UpdateSelect2(); UpdateSelect3(); UpdateSelect4(); showMe('other_activity_type', this);">	
<script type="text/javascript" src="http://txcc.sedl.org/orc/common/wz_tooltip.js"></script>
<!-- BEGIN: header server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/header_sedl_log_svc.html');?>

<!-- END: header server-side include -->

<!-- BEGIN: PAGE CONTENT -->

<form method="get" id="form2" name="form2" onsubmit="return checkFields()">
<input type="hidden" name="action" value="new_submit">

<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff" width="760" class="body">

<tr><td><div class="tiny" style="float:left;width:200px;background-color:#ffffff;padding-top:0px;padding-bottom:6px;margin:0px;border-width:0px;border-style:dotted;border-color:#0a5253"><strong>Current User</strong>: <?php echo $_SESSION['user_ID'].' | '.$_SESSION['PrimarySEDLWorkgroup'];?></div>
<div style="float:right;padding-top:0px;padding-bottom:6px"><a href="mailto:service_log@sedl.org?subject=SEDL-Staff-Service-Log-Comments">Suggestions/Comments</a> | <a href="http://www.sedl.org/staff/sims/logs/service_log_menu.php">Main Menu</a> | <a href="http://www.sedl.org/staff">Return to intranet</a></div>

		<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px">

		<tr><td nowrap><h2 class="txcc">SEDL - Staff Service Log</h2></td><td align="right"><a href="service_log.php?action=show_mine">Show my records</a> | <span style="color:#666666">Submit new activity</span></td></tr>
		<tr><td colspan="2">
		<div style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">

	<?php if($_SESSION['svc_log_admin_allow_surrogates'] == 'Yes'){ ?>
	
				If this is a surrogate log entry for someone else, select their name or ID: 
				
				<select name="surrogate_name" class="body">
						<option value="">
						<option value="Chuck Russell"> Chuck Russell</option>
						<option value="Myrna Mandlawitz"> Myrna Mandlawitz</option>
						<option value="">-----</option>
						<?php foreach($v2Result['valueLists']['sims_IDs_active_only'] as $key => $value) { ?>
						<option value="<?php echo $value;?>"> <?php echo $value; ?></option>
						<?php } ?>
						</select>
				
	
	<?php } ?>

			<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=10 border=0 bordercolor="cccccc" valign="top" class="dotted-box-orc" style="padding-top:0px;margin-top:6px">


			<tr><td bgcolor="#ebebeb" valign="top" nowrap><strong>NEW LOG ENTRY</strong></td><td align="right"><input type=button value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Submit Entry" class="body"></td></tr>
	
			<tr><td bgcolor="#ebebeb" align="right" valign="top"><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>1</strong></div>
			Activity Intensity:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Indicate whether this is a one-time or multiple/ongoing activity (i.e., with the <em>same primary objective</em> and the <em>same clients</em>). If this activity is one in a series of "multiple/ongoing" activities and relates to a prior log entry, identify the related log entry(-ies) in the box that appears to the right.<p>

			<select name="activity_intensity" onChange="UpdateSelect();">
			<option value="">
			<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_intensity'] as $key => $value) { ?>
			<option  value="<?php echo $value; ?>" /> <?php echo $value; ?><br>
			<?php } ?>
			</select>
			
					<div id="multiple_logs" style="float:right;padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#fff6bf">
					Select previous log entries related to this ongoing activity:<br><span class="tiny">To copy the activity name of a previous log entry, just click the ID.</span>
					<table style="padding-top:4px">

					<?php if($searchResult['foundCount'] !== 0){ ?>

						<?php foreach($searchResult['data'] as $key => $searchData) { ?>
						<tr><td style="margin:0px;padding-top:2px;padding-left:2px;padding-right:4px;padding-bottom:2px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff"><input type="checkbox" name="related_entries[]" value="<?php echo $searchData['service_log_ID'][0];?>"> <span title="<?php echo $searchData['activity_begin_date'][0];?> | <?php echo $searchData['activity_name'][0];?>"><span onclick="setTitle('<?php echo $searchData['activity_name'][0];?>')"><?php echo $searchData['service_log_ID'][0];?> - <span class="tiny"><?php echo $searchData['activity_begin_date'][0];?> | <?php echo $searchData['activity_name'][0];?></span></span></span></input></td></tr>
						<?php  } 

					}else{ ?>

						<tr><td style="margin:0px;padding:12px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff">No previous log entries found.</td></tr>

					<?php }
					?>
					</table>
					<span class="tiny"><strong>TIP</strong>: Hover mouse over log ID to view date and activity name.</span>
					</div>
			
			
			</div>
			</td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>2</strong></div>
			Activity Name:</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb"><p class="alert_small" style="margin-top:0px">NOTE: Only activities that involve some external client audience member(s) should be reported.</p> Enter a short but descriptive title that conveys the essence of the activity and distinguishes it from similar, yet different, activities. The same activity name should only be used for a series of related activities being delivered in a sequence, followed by a number that describes the order of each activity in this sequence (see examples below). Different names should be given to activities when they: (a) are provided to a different client or client group; or (b) are intended to accomplish a different objective.<p>
			<input type="text" name="activity_name" size="65" class="body"><br><font color="666666"><span class="tiny">Examples: TX ESC 7 WSM Training Session 1; TX ESC 7 WS Training Session 2 | <a href="service_log_activity_name_examples.php" target="_blank">More examples</a></span></font>
			</div>
			</td></tr>
	
	
			<tr><td style="text-align:right;background-color:#ebebeb" valign="top"><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>3</strong></div>
			Project Funding Stream:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">
			Select the SEDL budget code for this activity.<p>
			
						<select name="project_funding_stream" onChange="UpdateSelect3();">
						<option value="">
						<option value="N/A"> N/A
						<option value=""> -----

						<?php foreach($fund_year_unique as $current) { ?>
						<option value="<?php echo $current;?>"> <?php echo $current;?>
						<?php } ?>

						</select>

							<div id="isp_extra_fields" style="float:right;border:1px dotted #0a5253; padding:0px; background-color:#fff6bf">
							
							<table style="padding:4px;margin-top:0px"><tr><td>
							<strong>ESS Options:</strong><br>
							
									You have selected an ESS budget code. Please complete the following fields related to this ESS activity.
									<table style="padding:4px;margin-top:0px">
									<tr><td style="padding-left:6px;padding-top:4px;padding-bottom:4px;padding-right:4px;margin-top:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff">
									<strong>Project</strong><br>
									Select the ESS project relating to this activity.<p>
										<select name="wg_project_ID" onChange="UpdateSelect4();">
		
										<option value="">Select Project
										<option value="">---------------------------
										<option value="">## TXCC Projects ##
										<option value="">---------------------------
										<option value="">==> GOAL 1
										<option value="">---------------------------

										<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
				
											<?php if(($searchData4['cc'][0] == 'txcc')&&($searchData4['category'][0] == 'Texas CC Work Plan')&&($searchData4['goal'][0] == '1')&&($searchData4['contract'][0] == 'current')) { ?>
					
												<option value="<?php echo $searchData4['project_ID'][0];?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData4['project_number'][0].' - ';?><?php echo stripslashes($searchData4['project_name'][0]); ?>
					
											<?php } ?>
				
										<?php } ?>

										<option value="">---------------------------
										<option value="">==> GOAL 2
										<option value="">---------------------------

										<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
				
											<?php if(($searchData4['cc'][0] == 'txcc')&&($searchData4['category'][0] == 'Texas CC Work Plan')&&($searchData4['goal'][0] == '2')&&($searchData4['contract'][0] == 'current')) { ?>
					
												<option value="<?php echo $searchData4['project_ID'][0];?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData4['project_number'][0].' - ';?><?php echo stripslashes($searchData4['project_name'][0]); ?>
					
											<?php } ?>
				
										<?php } ?>

										<option value="">---------------------------
										<option value="">## SECC Projects ##
										<option value="">---------------------------
										<option value="">==> GOAL 1
										<option value="">---------------------------
				
										<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
				
											<?php if(($searchData4['cc'][0] == 'secc')&&($searchData4['goal'][0] == '1')&&($searchData4['revised'][0] == '2013')) { ?>

											<?php if ($project_num !== substr($searchData4['project_number'][0],0,3)){ echo '<option value="">';}?>
					
												<option value="<?php echo $searchData4['project_ID'][0];?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData4['project_number'][0].' - '.$searchData4['project_name'][0]; ?>
	
												<?php $project_num = substr($searchData4['project_number'][0],0,3); ?>
				
											<?php } ?>

										<?php } ?>
						
				
				
										<option value="">---------------------------
										<option value="">==> GOAL 2
										<option value="">---------------------------
				
										<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
				
											<?php if(($searchData4['cc'][0] == 'secc')&&($searchData4['goal'][0] == '2')&&($searchData4['revised'][0] == '2013')) { ?>

											<?php if ($project_num !== substr($searchData4['project_number'][0],0,3)){?> 
											
											<option value=""><option value=""><?php echo strtoupper($searchData4['project_state'][0]);?><?php }?>
					
												<option value="<?php echo $searchData4['project_ID'][0];?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData4['project_number'][0].' - '.$searchData4['project_name'][0]; ?>
					
												<?php $project_num = substr($searchData4['project_number'][0],0,3); ?>

											<?php } ?>
				
										<?php } ?>
						
<!--				
				
										<option value="">-------------------------------------
										<option value="">## SECC Projects (staff generated) ##
										<option value="">-------------------------------------
				
										<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
				
											<?php if(($searchData4['category'][0] == 'staff')&&($searchData4['cc'][0] == 'secc')) { ?>
					
												<option value="<?php echo stripslashes($searchData4['project_ID'][0]);?>"> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData4['created_by'][0].' - '.stripslashes($searchData4['project_name'][0]); ?>
					
											<?php } ?>
				
										<?php } ?>
-->		
										</select>
										
										<div id="txcc_tea_partners" style="float:right;padding-left:6px;padding-top:4px;padding-bottom:4px;padding-right:4px;margin-top:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff">
										You have selected the project "TEA-TXCC Partners". Please enter the name(s) of the TEA partner(s) for this activity.<p>
										<input type="text" name="wg_txcc_tea_partners" size="40">
										</div>
				
									</td></tr>
									
									<tr><td style="padding-left:6px;padding-top:4px;padding-bottom:4px;padding-right:4px;margin-top:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff">
									<strong>Priority Area</strong><br>	
									Indicate the U.S. Department of Education priority area(s) relating to this activity. Select all that apply.<p>
								<p style="color:red; font-size:15px">ESS staff must choose at least one Priority Area.</p>	
									<input type="checkbox" name="wg_usde_priority_area[]" value="01"> <span onmouseover="Tip('Ensuring the School Readiness and Success of Preschool-Age Children and Their Successful Transition to Kindergarten', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 1', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Early Learning</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="02"> <span onmouseover="Tip('Implementing College- and Career-Ready Standards and Aligned, High-Quality Assessments For All Students', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 2', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">College and Career Ready Standards and Assessments</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="03"> <span onmouseover="Tip('Turning Around the Lowest-Performing Schools', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 3', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Low Performing Schools</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="04"> <span onmouseover="Tip('Building Rigorous Instructional Pathways That Support the Successful Transition of All Students From Secondary Education to College Without the Need for Remediation, and Careers', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 4', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Rigorous Instructional Pathways</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="05"> <span onmouseover="Tip('Identifying and Scaling Up Innovative Approaches to Teaching and Learning That Significantly Improve Student Outcomes', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 5', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Innovative Approaches</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="06"> <span onmouseover="Tip('Identifying, Recruiting, Developing, and Retaining Highly Effective Teachers And Leaders', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 6', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Highly Effective Teachers and Leaders</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="07"> <span onmouseover="Tip('Using Data-Based Decision-Making to Improve Instructional Practices, Policies, and Student Outcomes', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 7', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Data-Based Decision Making</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="08"> <span onmouseover="Tip('Increasing the capacity of states to implement their key initiatives statewide and support the school-level implementation of effective practices', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 8', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Building State Capacity</span><br>




									
									</td></tr>
									</table>


							</td></tr>
							</table>
							</div>

			</div>
			</td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>4</strong></div>
			Activity Type(s):</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Select the activity type(s) that are applicable to this log entry. <strong>Select all that apply</strong>.<p>
				<?php $counter = 1; foreach($searchResult3['data'] as $key => $searchData3)  { ?>
				<span onmouseover="Tip('<?php echo $searchData3['activity_type_definition'][0];?>', WIDTH, 300, TITLE, '<?php echo $searchData3['activity_type'][0];?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">
				<input type="checkbox" name="activity_type[]" id="activity_type<?php echo $counter;?>" value="<?php echo $searchData3['activity_type'][0];?>"> <label for="activity_type<?php echo $counter;?>"><?php echo $searchData3['activity_type'][0]; ?></label></input></span><br>
				<?php $counter++; } ?>

					<div id="other_activity_type" style="width:400px;float:right;padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#fff6bf">
					Specify other activity type by selecting from the drop-down list or entering a new activity type in the space provided:<br>
					<table style="padding-top:4px"><tr><td>
									<select name="activity_type_other_select" class="body">
									<option value="">Select activity type
									<option value="">-------
									<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_type_other'] as $key => $value) { ?>
									<option value="<?php echo $value;?>"> <?php echo $value; ?>
									<?php } ?>
									</select>			
					</td>
					<td> &nbsp;<strong>OR</strong>&nbsp; <input type="text" name="activity_type_other_specify" maxlength="30" size="20" class="body" value="Enter new activity type"></td>
					
					</tr></table>
					</div>

				<p>
				<input type="checkbox" name="activity_type_other" id="activity_type_other" value="Yes"  onClick="showMe('other_activity_type', this);"> <label for="activity_type_other">Other</label></input><br>
				

			</div>
			</td></tr>


			<tr><td class="body" bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>5</strong></div>
			Activity Date(s):</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb;float:right">Enter the beginning and ending date(s) for this activity. If the activity happened on a single day, enter the same date for both.
			
			<p style="text-align:right;padding-right:90px">

							Beginning Date: 			 
							<select name="start_date_m" class="body">
							<option value="">Month
							<option value="">-------
							<?php foreach($v1Result['valueLists']['month_num'] as $key => $value) { ?>
							<option value="<?php echo $value;?>" <?php if($value == $thismonth){echo ' SELECTED';}?>> <?php echo $value; ?>
							<?php } ?>
							</select>
							
							 
							<select name="start_date_d" class="body">
							<option value="">Day
							<option value="">-------
							<?php foreach($v1Result['valueLists']['day_num'] as $key => $value) { ?>
							<option value="<?php echo $value;?>" <?php if($value == $thisday){echo ' SELECTED';}?>> <?php echo $value; ?>
							<?php } ?>
							</select> 
							
							 
							<select name="start_date_y" class="body">
							<option value="">Year
							<option value="">-------
							<?php foreach($v1Result['valueLists']['year_num'] as $key => $value) { 
							if($value >= date("Y")-1){ ?><option value="<?php echo $value;?>" <?php if($value == $thisyear){echo ' SELECTED';}?>> <?php echo $value; } ?>
							<?php } ?>
							</select> 
							
							<br>&nbsp;<br>Ending Date: 
			
							<select name="end_date_m" class="body">
							<option value="">Month
							<option value="">-------
							<?php foreach($v1Result['valueLists']['month_num'] as $key => $value) { ?>
							<option value="<?php echo $value;?>" <?php if($value == $thismonth){echo ' SELECTED';}?>> <?php echo $value; ?>
							<?php } ?>
							</select>
							
							 
							<select name="end_date_d" class="body">
							<option value="">Day
							<option value="">-------
							<?php foreach($v1Result['valueLists']['day_num'] as $key => $value) { ?>
							<option value="<?php echo $value;?>" <?php if($value == $thisday){echo ' SELECTED';}?>> <?php echo $value; ?>
							<?php } ?>
							</select> 
							
							 
							<select name="end_date_y" class="body">
							<option value="">Year
							<option value="">-------
							<?php foreach($v1Result['valueLists']['year_num'] as $key => $value) { 
							if($value >= date("Y")-1){ ?><option value="<?php echo $value;?>" <?php if($value == $thisyear){echo ' SELECTED';}?>> <?php echo $value; } ?>
							<?php } ?>
							</select> 

			</p>
			
			</div>			
			</td></tr>
			
	

			<tr><td bgcolor="#ebebeb" align="right" valign="top"><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>6</strong></div>
			Activity Scope:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb;float:right">Select the geographical scope of this activity. If this is a regional, state, or local activity, indicate the state(s) involved.<p>
			

					<div id="activity_location_scope_states" style="width:400px;float:right;padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#fff6bf">
					Select the state(s) related to this activity:<br>
					<table style="padding-top:4px"><tr><td>

					<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_states'] as $key => $value) { ?>
					<input class="body" type="checkbox" name="activity_location_state[]" value="<?php echo $value;?>"> <?php echo $value; ?><br>
					<?php } ?>

					</td></tr>
					</table>
					</div>


			<select name="activity_location_scope" onChange="UpdateSelect2();">
			<option value="">
			<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_geographical_scope'] as $key => $value) { ?>
			<option  value="<?php echo $value; ?>" /> <?php echo $value; ?><br>
			<?php } ?>
			</select>
			
			
			
			</div>
			</td></tr>


			<tr><td bgcolor="#ebebeb" align="right" valign="top"><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>7</strong></div>
			Contact Method:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Select the <strong>primary</strong> contact method that applies to this activity.<p>
			
			
						<select name="contact_method" class="body">
						<option value="">
						
						<?php foreach($v1Result['valueLists']['sedl_svc_log_contact_method'] as $key => $value) { ?>
						<option value="<?php echo $value;?>"> <?php echo $value; ?></option>
						<?php } ?>
						</select>

			
			
			</div>
			</td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top"><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>8</strong></div>
			Activity Duration:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Select the duration for this activity.<p>

			<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_duration'] as $key => $value) { ?>
			<input type="radio" name="activity_duration" value="<?php echo $value; ?>"> <?php echo $value; ?><br>
			<?php } ?>

			
			</div>
			</td></tr>



			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>9</strong></div>
			Clients Served:</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Enter number (actual or estimated) of clients served for each client type.<p>

				<table>
				<tr><td style="text-align:top;border-right-width:1px;border-left-width:0px;border-top-width:0px;border-bottom-width:0px;border-style:solid;border-color:#cccccc;padding-right:15px" valign="top">


						<table>
		
						<tr><td><span onmouseover="Tip('Examples: superintendent or assistant superintendent, principal or assistant principal, Title 1 coordinator, curriculum coordinator', WIDTH, 300, TITLE, 'ADMINISTRATOR  (school or district)', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Administrator (school or district)</span></td><td><input type="text" name="client_served_count_administrators" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('a provider of afterschool services', WIDTH, 300, TITLE, 'AFTERSCHOOL PROVIDER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Afterschool Provider</span></td><td><input type="text" name="client_served_count_afterschool_provider" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Examples: occupational therapists, physical therapists, vocational rehabilitation practitioners, allied health practitioners, independent living services staffs', WIDTH, 300, TITLE, 'HEALTH/DISABILITY SERVICES PROVIDER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Disability Svcs. Provider</span></td><td><input type="text" name="client_served_count_hds_provider" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Someone who employs, or potentially may employ, individuals with disabilities', WIDTH, 300, TITLE, 'EMPLOYER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Employer</span></td><td><input type="text" name="client_served_count_employer" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Examples: staff from U.S. Department of Education or other federal funding agency, foundation staff, SEA staff acting in the capacity of a funding representative', WIDTH, 300, TITLE, 'FUNDING REPRESENTATIVE', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Funding Rep.</span></td><td><input type="text" name="client_served_count_foundation" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Independent Education Agency', WIDTH, 300, TITLE, 'IEA', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">IEA (ESC)</span></td><td><input type="text" name="client_served_count_IEA" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('University administrator or faculty member', WIDTH, 300, TITLE, 'IHE', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">IHE (Higher Ed)</span></td><td><input type="text" name="client_served_count_IHE" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Math coach, literacy coach, others working directly with teachers in a coaching capacity', WIDTH, 300, TITLE, 'INSTRUCTIONAL COACH/SPECIALIST', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Instructional Coach/Specialist</span></td><td><input type="text" name="client_served_count_instr_coaches_specialists" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Individuals who provide expertise or services related to knowledge translation processes', WIDTH, 300, TITLE, 'KNOWLEDGE TRANSLATION PROFESSIONAL', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Knowledge Translation Prof.</span></td><td><input type="text" name="client_served_count_ktp" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('a representative of the media', WIDTH, 300, TITLE, 'MEDIA REPRESENTATIVE', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Media Rep.</span></td><td><input type="text" name="client_served_count_reporter" size="5" class="body" style="text-align:right"></td></tr>
		
						</table>
			
			
				</td><td style="text-align:top;padding-left:15px" valign="top">
				
						<table>
		
						<tr><td><span onmouseover="Tip('a parent', WIDTH, 300, TITLE, 'PARENT', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Parent</span></td><td><input type="text" name="client_served_count_parents" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Parent Information Resource Center staff', WIDTH, 300, TITLE, 'PIRC STAFF', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">PIRC Staff</span></td><td><input type="text" name="client_served_count_PIRC_staff" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Examples: Chief state school officers, elected officials, state or local school board members, and/or their staffs', WIDTH, 300, TITLE, 'POLICYMAKER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Policymaker</span></td><td><input type="text" name="client_served_count_policymakers" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Association staff or board members', WIDTH, 300, TITLE, 'PROFESSIONAL ASSOCIATION ', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Prof. Association</span></td><td><input type="text" name="client_served_count_prof_associations" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Staff of a funded research project or a consultant researcher', WIDTH, 300, TITLE, 'RESEARCHER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Researcher</span></td><td><input type="text" name="client_served_count_other_research_provider" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('State Education Agency', WIDTH, 300, TITLE, 'SEA', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">SEA</span></td><td><input type="text" name="client_served_count_SEA" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('a student', WIDTH, 300, TITLE, 'STUDENT', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Student</span></td><td><input type="text" name="client_served_count_students" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('a teacher', WIDTH, 300, TITLE, 'TEACHER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Teacher</span></td><td><input type="text" name="client_served_count_teachers" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('a training or technical assistance service provider', WIDTH, 300, TITLE, 'TRAINING/TA SERVICE PROVIDER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Training/TA Svc. Provider</span></td><td><input type="text" name="client_served_count_other_TA" size="5" class="body" style="text-align:right"></td></tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
		
						</table>
				
				</td></tr>
				
				<tr><td colspan="2" style="text-align:top;border-right-width:0px;border-left-width:0px;border-top-width:1px;border-bottom-width:0px;border-style:solid;border-color:#cccccc;padding-right:15px;padding-top:10px" valign="top">

					Specify other client type by selecting from the drop-down list or entering a new client type in the space provided. Enter the client count in the box to the right.<br>

					<table style="padding-top:4px"><tr><td>
									<select name="client_served_other_select" class="body">
									<option value="">Select client type
									<option value="">-------
									<?php foreach($v1Result['valueLists']['sedl_svc_log_client_served_other_specify'] as $key => $value) { ?>
									<option value="<?php echo $value;?>"> <?php echo $value; ?>
									<?php } ?>
									</select>			
					</td>
					<td> &nbsp;<strong>OR</strong>&nbsp; <input type="text" name="client_served_other_specify" size="20" maxlength="30" class="body" value="Enter new client type"> &nbsp; <input type="text" name="client_served_count_other_specify" size="5" class="body"></td>
					
					</tr></table>

				
				</td></tr>
				</table>
			
			</div>
			</td></tr>
	
	
	
			<tr><td bgcolor="#ebebeb" align="right" valign="top"><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>10</strong></div>
			Primary <br>Requestor ID:</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">If applicable, enter the six-digit client ID of the individual who is the primary requestor for this activity. The primary requestor is an <em>individual</em> who specifically <em>originates</em> a request for SEDL's assistance or involvement, e.g., a chief state school officer who requests a rapid response brief, or a high-ranking SEA official who asks SEDL to provide technical assistance to her staff.  To find the requestor ID, <a href="javascript:win1()" onMouseOver="self.status='Open A Window'; return true;">search the SEDL client database</a>.<p>
			<input type="text" name="primary_sedl_client_ID" size="5" class="body">		
			</div>
			
			</td></tr>
	
			<tr><td bgcolor="#ebebeb" align="right" valign="top"><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>11</strong></div>
			SEDL Unit:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Select the SEDL unit that funded, sponsored, or is responsible for delivering the activity.<p>
			
			
						<select name="sedl_workgroup" class="body">
						<option value="">
						
						<?php foreach($v1Result['valueLists']['sedl_svc_log_workgroup_affil'] as $key => $value) { ?>
						<option value="<?php echo $value;?>" <?php if($value == $_SESSION['workgroup']){echo 'selected';}?>> <?php echo $value; ?></option>
						<?php } ?>
						</select>

			
			
			</div>
			</td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top"><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>12</strong></div>
			Comments:</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">If needed, briefly enter any additional information or notes relating to this activity.
<p class="alert_small">NOTE: Please be brief - only 5-6 lines of text. Please do not paste text from other sources.</p><p>
			<textarea name="notes" cols="70" rows="10" class="body"></textarea>
			</div>
			</td></tr>

			<tr><td>&nbsp;</td><td align="right" width="100%" style="background-color:#ffffff"><input type=button value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Submit Entry" class="body"></td></tr>
			</form>


			</table>

		</td></tr>
		</table>
</div>
</td></tr>
</table>


<!-- BEGIN: footer server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/footer_sedl_log_svc.html');?>

<!-- END: footer server-side include -->


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
##############################################################################################################################
##############################################################################################################################
##############################################################################################################################


} elseif ($action == 'new_submit') { 


###COLLECT THE FORM-SUBMITTED VALUES INTO VARIABLES###

$surrogate_name = $_GET['surrogate_name'];
$activity_name = stripslashes($_GET['activity_name']);

for($i=0 ; $i<count($_GET['activity_type']) ; $i++) {
		$activity_type .= $_GET['activity_type'][$i]."\r"; 
		}

for($i=0 ; $i<count($_GET['related_entries']) ; $i++) {
		$related_entries .= $_GET['related_entries'][$i]."\r"; 
		}

for($i=0 ; $i<count($_GET['activity_location_state']) ; $i++) {
		$activity_location_state .= $_GET['activity_location_state'][$i]."\r"; 
		}

$activity_type_other = $_GET['activity_type_other'];
$activity_type_other_select = $_GET['activity_type_other_select'];
$activity_type_other_specify = $_GET['activity_type_other_specify'];

$activity_begin_date = $_GET['start_date_m'].'/'.$_GET['start_date_d'].'/'.$_GET['start_date_y'];
$activity_end_date = $_GET['end_date_m'].'/'.$_GET['end_date_d'].'/'.$_GET['end_date_y'];

//$activity_location_city = $_GET['activity_location_city'];
$activity_location_scope = $_GET['activity_location_scope'];
$activity_duration = $_GET['activity_duration'];
$activity_intensity = $_GET['activity_intensity'];
$contact_method = $_GET['contact_method'];

$client_served_count_SEA = $_GET['client_served_count_SEA'];
$client_served_count_IEA = $_GET['client_served_count_IEA'];
$client_served_count_IHE = $_GET['client_served_count_IHE'];
$client_served_count_administrators = $_GET['client_served_count_administrators'];
$client_served_count_teachers = $_GET['client_served_count_teachers'];
$client_served_count_parents = $_GET['client_served_count_parents'];
$client_served_count_instr_coaches_specialists = $_GET['client_served_count_instr_coaches_specialists'];
$client_served_count_policymakers = $_GET['client_served_count_policymakers'];
$client_served_count_prof_associations = $_GET['client_served_count_prof_associations'];
$client_served_count_PIRC_staff = $_GET['client_served_count_PIRC_staff'];
$client_served_count_afterschool_provider = $_GET['client_served_count_afterschool_provider'];
$client_served_count_other_TA = $_GET['client_served_count_other_TA'];
$client_served_count_other_research_provider = $_GET['client_served_count_other_research_provider'];
$client_served_count_reporter = $_GET['client_served_count_reporter'];
$client_served_count_employer = $_GET['client_served_count_employer'];
$client_served_count_hds_provider = $_GET['client_served_count_hds_provider'];
$client_served_count_ktp = $_GET['client_served_count_ktp'];
$client_served_count_students = $_GET['client_served_count_students'];
$client_served_count_foundation = $_GET['client_served_count_foundation'];

if($_GET['client_served_other_select'] !== ''){
$client_served_other_specify = $_GET['client_served_other_select'];
} elseif(($_GET['client_served_other_specify'] !== '')&&($_GET['client_served_other_specify'] !== 'Enter new client type')) {
$client_served_other_specify = $_GET['client_served_other_specify'];
} else {
$client_served_other_specify = '';
}

$client_served_count_other_specify = $_GET['client_served_count_other_specify'];

$primary_sedl_client_ID = $_GET['primary_sedl_client_ID'];
$sedl_workgroup = $_GET['sedl_workgroup'];
$project_funding_stream = $_GET['project_funding_stream'];

$wg_project_ID = $_GET['wg_project_ID'];


for($i=0 ; $i<count($_GET['wg_usde_priority_area']) ; $i++) {
		$wg_usde_priority_area .= $_GET['wg_usde_priority_area'][$i]."\r"; 
		}

$wg_txcc_tea_partners = $_GET['wg_txcc_tea_partners'];

$notes = $_GET['notes'];

$created_by = $_SESSION['user_ID'];

###CREATE A REQUEST THAT ADDS THE NEW RECORD###

$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('CC_dms.fp7','sedl_service_log'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information


###ADD THE SUBMITTED VALUES AS PARAMETERS###

		
$newrecord -> AddDBParam('surrogate_name',$surrogate_name);
$newrecord -> AddDBParam('activity_name',$activity_name);
$newrecord -> AddDBParam('activity_type',$activity_type);

if($activity_type_other == 'Yes'){

$newrecord -> AddDBParam('activity_type_other','Yes');

	if($activity_type_other_select !== ''){
	$newrecord -> AddDBParam('activity_type_other_specify',$activity_type_other_select);
	}elseif(($activity_type_other_specify !== '')&&($activity_type_other_specify !== 'Enter new activity type')){
	$newrecord -> AddDBParam('activity_type_other_specify',$activity_type_other_specify);
	}

}

$newrecord -> AddDBParam('activity_begin_date',$activity_begin_date);
$newrecord -> AddDBParam('activity_end_date',$activity_end_date);

//$newrecord -> AddDBParam('activity_location_city',$activity_location_city);
$newrecord -> AddDBParam('activity_location_scope',$activity_location_scope);

if(($activity_location_scope == 'Regional')||($activity_location_scope == 'State')||($activity_location_scope == 'Local')){
$newrecord -> AddDBParam('activity_location_state',$activity_location_state);
}

$newrecord -> AddDBParam('contact_method',$contact_method);
$newrecord -> AddDBParam('activity_duration',$activity_duration);
$newrecord -> AddDBParam('related_entries',$related_entries);
if($related_entries !== ''){
$related_entries_create_trigger = rand();
$newrecord -> AddDBParam('related_entries_update_trigger',$related_entries_create_trigger);
}

$newrecord -> AddDBParam('activity_intensity',$activity_intensity);

$newrecord -> AddDBParam('client_served_count_employer',$client_served_count_employer);
$newrecord -> AddDBParam('client_served_count_hds_provider',$client_served_count_hds_provider);
$newrecord -> AddDBParam('client_served_count_ktp',$client_served_count_ktp);
$newrecord -> AddDBParam('client_served_count_students',$client_served_count_students);
$newrecord -> AddDBParam('client_served_count_SEA',$client_served_count_SEA);
$newrecord -> AddDBParam('client_served_count_IEA',$client_served_count_IEA);
$newrecord -> AddDBParam('client_served_count_IHE',$client_served_count_IHE);
$newrecord -> AddDBParam('client_served_count_administrators',$client_served_count_administrators);
$newrecord -> AddDBParam('client_served_count_teachers',$client_served_count_teachers);
$newrecord -> AddDBParam('client_served_count_parents',$client_served_count_parents);
$newrecord -> AddDBParam('client_served_count_instr_coaches_specialists',$client_served_count_instr_coaches_specialists);
$newrecord -> AddDBParam('client_served_count_policymakers',$client_served_count_policymakers);
$newrecord -> AddDBParam('client_served_count_prof_associations',$client_served_count_prof_associations);
$newrecord -> AddDBParam('client_served_count_PIRC_staff',$client_served_count_PIRC_staff);
$newrecord -> AddDBParam('client_served_count_afterschool_provider',$client_served_count_afterschool_provider);
$newrecord -> AddDBParam('client_served_count_other_TA',$client_served_count_other_TA);
$newrecord -> AddDBParam('client_served_count_other_research_provider',$client_served_count_other_research_provider);
$newrecord -> AddDBParam('client_served_count_reporter',$client_served_count_reporter);
$newrecord -> AddDBParam('client_served_count_foundation',$client_served_count_foundation);
//$newrecord -> AddDBParam('client_served_count_room_rental',$client_served_count_other_room_rental);
$newrecord -> AddDBParam('client_served_count_other_specify',$client_served_count_other_specify);
$newrecord -> AddDBParam('client_served_other_specify',$client_served_other_specify);

$newrecord -> AddDBParam('primary_sedl_client_ID',$primary_sedl_client_ID);
$newrecord -> AddDBParam('sedl_workgroup',$sedl_workgroup);
$newrecord -> AddDBParam('project_funding_stream',$project_funding_stream);
$newrecord -> AddDBParam('notes',$notes);
$newrecord -> AddDBParam('created_by',$created_by);
$newrecord -> AddDBParam('last_mod_by',$created_by);
$newrecord -> AddDBParam('staff_ID',$_SESSION['staff_ID']);

$newrecord -> AddDBParam('wg_project_ID',$wg_project_ID);
$newrecord -> AddDBParam('wg_usde_priority_area',$wg_usde_priority_area);
$newrecord -> AddDBParam('wg_txcc_tea_partners',$wg_txcc_tea_partners);



###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
$newrecordResult = $newrecord -> FMNew();
$recordData = current($newrecordResult['data']);

$new_record_ID = $recordData['service_log_ID'][0];
$new_record_row_ID = $recordData['c_cwp_row_ID'][0];
//echo '$new_record_ID: '.$recordData['service_log_ID'][0];
//echo '$errorCode: '.$newrecordResult['errorCode'];
//exit;

if($newrecordResult['errorCode'] == '0'){
$_SESSION['new_activity_submitted'] = '1';

### SEND E-MAIL NOTIFICATION TO ADMIN STAFF TO CHECK CONTENT ###

$to = 'eric.waters@sedl.org';
$subject = 'SEDL service log entry received';
$message = 

'#############################################################################################'."\n".
'A new entry was received in the SEDL Service Log Database.'."\n".
'#############################################################################################'."\n\n".

'DETAILS:'."\n\n".

'Submitted by: '.$created_by."\n\n".

'Surrogate for: '.$surrogate_name."\n\n".

'Activity Name: '."\n".
stripslashes($activity_name)."\n\n".

'Activity Type: '."\n".
$activity_type."\n\n".

'Activity Begin Date: '."\n".
$activity_begin_date."\n\n".

'Activity End Date: '."\n".
$activity_end_date."\n\n".

'Activity Scope: '."\n".
$activity_location_scope.' - '.$activity_location_scope_states."\n\n".

'Contact Method: '."\n".
$contact_method."\n\n".

'Activity Duration: '."\n".
$activity_duration."\n\n".

'Related Entries: '."\n".
$related_entries."\n\n".

'Activity Intensity: '."\n".
$activity_intensity."\n\n".

'Clients served counts: '."\n".
'SEA: '.$client_served_count_SEA."\n".
'IEA: '.$client_served_count_IEA."\n".
'IHE: '.$client_served_count_IHE."\n".
'Administrators: '.$client_served_count_administrators."\n".
'Teachers: '.$client_served_count_teachers."\n".
'Parents: '.$client_served_count_parents."\n".
'Instructional coaches/specialists: '.$client_served_count_instr_coaches_specialists."\n".
'Policymakers: '.$client_served_count_policymakers."\n".
'Professional associations: '.$client_served_count_prof_associations."\n".
'PIRC staff: '.$client_served_count_PIRC_staff."\n".
'Afterschool provider: '.$client_served_count_afterschool_provider."\n".
'Other TA: '.$client_served_count_other_TA."\n".
'Other research provider: '.$client_served_count_other_research_provider."\n".
'Reporters: '.$client_served_count_reporter."\n".
'Funding Rep.: '.$client_served_count_foundation."\n".
'HDS Provider: '.$client_served_count_hds_provider."\n".
'KTP: '.$client_served_count_ktp."\n".
'Students: '.$client_served_count_students."\n".
'Employers: '.$client_served_count_employer."\n\n".

'Primary SEDL client ID: '.$primary_sedl_client_ID."\n\n".

'SEDL workgroup: '.$sedl_workgroup."\n\n".

'Notes: '."\n".
$notes."\n\n".

'----------'."\n\n".

'This information has been saved in the table \'sedl_service_log\' in the database \'CC_dms.fp7\' (record_ID='.$new_record_ID.').';

$headers = 'From: service_log@sedl.org'."\r\n".'Reply-To: service_log@sedl.org'."\r\n".'Bcc: eric.waters@sedl.org';

mail($to, $subject, $message, $headers);

/*
### SEND E-MAIL CONFIRMATION TO SUBMITTER ###

$to = $_SESSION['email'];
$subject = 'TXCC Resources Web site content received';
$message = 

'Thank you for submitting a resource to the CC Network Resources database. Your resource submission was received and will be evaluated and posted to the TXCC Resources web site if approved.'."\n\n". 

'If this resource was a .pdf, .doc, or .ppt document, make sure to upload the file to the server using the "Upload file" link on the Resource Detail screen (if you have not done so already).'."\n\n".

'----------'."\n\n".

'DETAILS:'."\n\n".

'Submitted by: '.$_SESSION['tadds_user_ID']."\n\n".

'Resource Title: '.stripslashes($resource_title)."\n\n".

'Resource ID: '.$recordData['resource_ID'][0]."\n\n".

'----------'."\n\n";


$headers = 'From: txcc_orc@sedl.org'."\r\n".'Reply-To: txcc_orc@sedl.org';

mail($to, $subject, $message, $headers);

*/

header('Location: http://www.sedl.org/staff/sims/logs/service_log.php?action=show_1&uid='.$related_entries_create_trigger.'&row_ID='.$new_record_row_ID);
exit;
}else{
$_SESSION['new_activity_submitted'] = '2';
$_SESSION['new_activity_submitted_errorcode'] = $newrecordResult['errorCode'];
header('Location: http://www.sedl.org/staff/sims/logs/service_log.php?action=show_mine');
exit;
}




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


} elseif ($action == 'show_1') { 

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


###COLLECT THE FORM-SUBMITTED VALUES INTO VARIABLES###
$related_entries_create_trigger = $_GET['uid'];
$service_log_ID = $_GET['service_log_ID'];
$row_ID = $_GET['row_ID'];
$mod = $_GET['mod'];
//$src = $_GET['src'];
//echo '<p>$mod: '.$mod;
//echo '<p>$row_ID: '.$row_ID;
//exit;

if($mod == 'update_project_lead'){
	$project_lead = $_GET['project_lead'];

	$update = new FX($serverIP,$webCompanionPort);
	$update -> SetDBData('CC_dms.fp7','sedl_service_log');
	$update -> SetDBPassword($webPW,$webUN);
	$update -> AddDBParam('-recid',$row_ID);
	
	$update -> AddDBParam('project_lead',$project_lead);
	$update -> AddDBParam('last_mod_by',$_SESSION['user_ID']);
	
	$updateResult = $update -> FMEdit();
	//echo '<p>Update errorCode: '.$updateResult['errorCode'];
	//$mod = "";
	if($updateResult['errorCode'] == '0'){
		$_SESSION['activity_updated'] = '1';
		}else{
		$_SESSION['activity_updated'] = '2';
		$_SESSION['activity_updated_error'] = $updateResult['errorCode'];
	}
	$mod = '';

}

if($mod == 'edit_confirm1'){
	$surrogate_name = $_GET['surrogate_name'];
	$activity_name = stripslashes($_GET['activity_name']);
	//$activity_location_city = $_GET['activity_location_city'];
	
	$activity_type_other = $_GET['activity_type_other'];
	$activity_type_other_select = $_GET['activity_type_other_select'];
	$activity_type_other_specify = $_GET['activity_type_other_specify'];

	
	$activity_location_scope = $_GET['activity_location_scope'];
	
	for($i=0 ; $i<count($_GET['activity_location_state']) ; $i++) {
		$activity_location_state .= $_GET['activity_location_state'][$i]."\r"; 
		}
	
	$contact_method = $_GET['contact_method'];
	$sedl_workgroup = $_GET['sedl_workgroup'];
	$project_funding_stream = $_GET['project_funding_stream'];

	$wg_project_ID = $_GET['wg_project_ID'];
	$wg_txcc_tea_partners = $_GET['wg_txcc_tea_partners'];

	for($i=0 ; $i<count($_GET['wg_usde_priority_area']) ; $i++) {
		$wg_usde_priority_area .= $_GET['wg_usde_priority_area'][$i]."\r"; 
		}

	$activity_duration = $_GET['activity_duration'];
	$activity_intensity = $_GET['activity_intensity'];
	$primary_sedl_client_ID = $_GET['primary_sedl_client_ID'];
	$notes = $_GET['notes'];

	$start_date_m = $_GET['start_date_m'];
	$start_date_d = $_GET['start_date_d'];
	$start_date_y = $_GET['start_date_y'];

	$end_date_m = $_GET['end_date_m'];
	$end_date_d = $_GET['end_date_d'];
	$end_date_y = $_GET['end_date_y'];
	
	$client_served_count_administrators = $_GET['client_served_count_administrators'];
	$client_served_count_afterschool_provider = $_GET['client_served_count_afterschool_provider'];
	$client_served_count_IEA = $_GET['client_served_count_IEA'];
	$client_served_count_IHE = $_GET['client_served_count_IHE'];
	$client_served_count_instr_coaches_specialists = $_GET['client_served_count_instr_coaches_specialists'];
	$client_served_count_other_TA = $_GET['client_served_count_other_TA'];
	$client_served_count_other_research_provider = $_GET['client_served_count_other_research_provider'];
	$client_served_count_PIRC_staff = $_GET['client_served_count_PIRC_staff'];
	$client_served_count_policymakers = $_GET['client_served_count_policymakers'];
	$client_served_count_prof_associations = $_GET['client_served_count_prof_associations'];
	$client_served_count_SEA = $_GET['client_served_count_SEA'];
	$client_served_count_teachers = $_GET['client_served_count_teachers'];
	$client_served_count_parents = $_GET['client_served_count_parents'];
	$client_served_count_reporter = $_GET['client_served_count_reporter'];
	$client_served_count_foundation = $_GET['client_served_count_foundation'];

	$client_served_count_employer = $_GET['client_served_count_employer'];
	$client_served_count_hds_provider = $_GET['client_served_count_hds_provider'];
	$client_served_count_ktp = $_GET['client_served_count_ktp'];
	$client_served_count_students = $_GET['client_served_count_students'];

	//$client_served_count_room_rental = $_GET['client_served_count_room_rental'];
	
	if($_GET['client_served_other_select'] !== ''){
	$client_served_other_specify = $_GET['client_served_other_select'];
	} elseif(($_GET['client_served_other_specify'] !== '')&&($_GET['client_served_other_specify'] !== 'Enter new client type')) {
	$client_served_other_specify = $_GET['client_served_other_specify'];
	} else {
	$client_served_other_specify = '';
	}
	
	$client_served_count_other_specify = $_GET['client_served_count_other_specify'];
	

	
	for($i=0 ; $i<count($_GET['activity_type']) ; $i++) {
			$activity_type .= $_GET['activity_type'][$i]."\r"; 
			}
	
	for($i=0 ; $i<count($_GET['related_entries']) ; $i++) {
			$related_entries .= $_GET['related_entries'][$i]."\r"; 
			}

	$update = new FX($serverIP,$webCompanionPort);
	$update -> SetDBData('CC_dms.fp7','sedl_service_log');
	$update -> SetDBPassword($webPW,$webUN);
	$update -> AddDBParam('-recid',$row_ID);
	
	$update -> AddDBParam('surrogate_name',$surrogate_name);
	$update -> AddDBParam('activity_name',$activity_name);
	//$update -> AddDBParam('activity_location_city',$activity_location_city);
	
	$update -> AddDBParam('activity_location_scope',$activity_location_scope);
	
	if(($activity_location_scope == 'Regional')||($activity_location_scope == 'State')||($activity_location_scope == 'Local')){
	$update -> AddDBParam('activity_location_state',$activity_location_state);
	} else {
	$update -> AddDBParam('activity_location_state','');
	}
	
	$update -> AddDBParam('contact_method',$contact_method);
	$update -> AddDBParam('activity_type',$activity_type);
	
	$update -> AddDBParam('activity_type_other',$activity_type_other);

if($activity_type_other == 'Yes'){

	if($activity_type_other_select !== ''){
	$update -> AddDBParam('activity_type_other_specify',$activity_type_other_select);
	}elseif(($activity_type_other_specify !== '')&&($activity_type_other_specify !== 'Enter new activity type')){
	$update -> AddDBParam('activity_type_other_specify',$activity_type_other_specify);
	}

} else {

	$update -> AddDBParam('activity_type_other_specify','');

}

	$activity_begin_date = $_GET['start_date_m'].'/'.$_GET['start_date_d'].'/'.$_GET['start_date_y'];
	$activity_end_date = $_GET['end_date_m'].'/'.$_GET['end_date_d'].'/'.$_GET['end_date_y'];

	
	$update -> AddDBParam('activity_begin_date',$start_date_m.'/'.$start_date_d.'/'.$start_date_y);
	$update -> AddDBParam('activity_end_date',$end_date_m.'/'.$end_date_d.'/'.$end_date_y);

	//$update -> AddDBParam('start_date_m',$start_date_m);
	//$update -> AddDBParam('start_date_d',$start_date_d);
	//$update -> AddDBParam('start_date_y',$start_date_y);

	//$update -> AddDBParam('end_date_m',$end_date_m);
	//$update -> AddDBParam('end_date_d',$end_date_d);
	//$update -> AddDBParam('end_date_y',$end_date_y);

	$update -> AddDBParam('activity_duration',$activity_duration);
	$update -> AddDBParam('related_entries',$related_entries);

	if($related_entries !== ''){
	$related_entries_update_trigger = rand();
	$update -> AddDBParam('related_entries_update_trigger',$related_entries_update_trigger);
	}

	$update -> AddDBParam('activity_intensity',$activity_intensity);
	$update -> AddDBParam('primary_sedl_client_ID',$primary_sedl_client_ID);
	$update -> AddDBParam('sedl_workgroup',$sedl_workgroup);
	$update -> AddDBParam('project_funding_stream',$project_funding_stream);
	$update -> AddDBParam('notes',$notes);

	$update -> AddDBParam('wg_project_ID',$wg_project_ID);
	$update -> AddDBParam('wg_usde_priority_area',$wg_usde_priority_area);
	$update -> AddDBParam('wg_txcc_tea_partners',$wg_txcc_tea_partners);

	$update -> AddDBParam('client_served_count_administrators',$client_served_count_administrators);
	$update -> AddDBParam('client_served_count_afterschool_provider',$client_served_count_afterschool_provider);
	$update -> AddDBParam('client_served_count_IEA',$client_served_count_IEA);
	$update -> AddDBParam('client_served_count_IHE',$client_served_count_IHE);
	$update -> AddDBParam('client_served_count_instr_coaches_specialists',$client_served_count_instr_coaches_specialists);
	$update -> AddDBParam('client_served_count_other_TA',$client_served_count_other_TA);
	$update -> AddDBParam('client_served_count_other_research_provider',$client_served_count_other_research_provider);
	$update -> AddDBParam('client_served_count_PIRC_staff',$client_served_count_PIRC_staff);
	$update -> AddDBParam('client_served_count_policymakers',$client_served_count_policymakers);
	$update -> AddDBParam('client_served_count_prof_associations',$client_served_count_prof_associations);
	$update -> AddDBParam('client_served_count_SEA',$client_served_count_SEA);
	$update -> AddDBParam('client_served_count_teachers',$client_served_count_teachers);
	$update -> AddDBParam('client_served_count_parents',$client_served_count_parents);
	$update -> AddDBParam('client_served_count_reporter',$client_served_count_reporter);
	$update -> AddDBParam('client_served_count_foundation',$client_served_count_foundation);
	$update -> AddDBParam('client_served_count_employer',$client_served_count_employer);
	$update -> AddDBParam('client_served_count_hds_provider',$client_served_count_hds_provider);
	$update -> AddDBParam('client_served_count_ktp',$client_served_count_ktp);
	$update -> AddDBParam('client_served_count_students',$client_served_count_students);
	//$update -> AddDBParam('client_served_count_room_rental',$client_served_count_room_rental);
	$update -> AddDBParam('client_served_count_other_specify',$client_served_count_other_specify);
	$update -> AddDBParam('client_served_other_specify',$client_served_other_specify);


	$update -> AddDBParam('last_mod_by',$_SESSION['user_ID']);

	
	
	$updateResult = $update -> FMEdit();
	//echo '<p>Update errorCode: '.$updateResult['errorCode'];
	//$mod = "";
	if($updateResult['errorCode'] == '0'){
		$_SESSION['activity_updated'] = '1';
		}else{
		$_SESSION['activity_updated'] = '2';
		$_SESSION['activity_updated_error'] = $updateResult['errorCode'];
	}
	$mod = '';
}

if($mod == 'delete'){
	$delete = new FX($serverIP,$webCompanionPort);
	$delete -> SetDBData('CC_dms.fp7','sedl_service_log');
	$delete -> SetDBPassword($webPW,$webUN);
	$delete -> AddDBParam('-recid',$row_ID);
	
	$deleteResult = $delete -> FMDelete();
	$_SESSION['activity_deleted'] = '1';
	$mod = '';
	header('Location: http://www.sedl.org/staff/sims/logs/service_log.php?action=show_mine');

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','SERVICE_LOG_DELETE');
$newrecord -> AddDBParam('table','sedl_service_log (ccdms.fp7)');
$newrecord -> AddDBParam('object_ID',$service_log_ID);
$newrecord -> AddDBParam('affected_row_ID',$row_ID);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];



}
/*
if($mod == 'approve'){

	$update = new FX($serverIP,$webCompanionPort);
	$update -> SetDBData('CC_dms.fp7','cc_network_resources');
	$update -> SetDBPassword($webPW,$webUN);
	$update -> AddDBParam('-recid',$row_ID);
	$update -> AddDBParam('approval_status','Approved');
	
	$updateResult = $update -> FMEdit();
	//echo '<p>Update errorCode: '.$updateResult['errorCode'];
	//$mod = "";
	if($updateResult['errorCode'] == '0'){
		$_SESSION['resource_approved'] = '1';
		}else{
		$_SESSION['resource_approved'] = '2';
		$_SESSION['resource_approved_error'] = $updateResult['errorCode'];
	}
	$mod = '';
}

if($mod == 'disapprove'){

	$update = new FX($serverIP,$webCompanionPort);
	$update -> SetDBData('CC_dms.fp7','cc_network_resources');
	$update -> SetDBPassword($webPW,$webUN);
	$update -> AddDBParam('-recid',$row_ID);
	$update -> AddDBParam('approval_status','Pending');
	
	$updateResult = $update -> FMEdit();
	//echo '<p>Update errorCode: '.$updateResult['errorCode'];
	//$mod = "";
	if($updateResult['errorCode'] == '0'){
		$_SESSION['resource_approved'] = '1';
		}else{
		$_SESSION['resource_approved'] = '2';
		$_SESSION['resource_approved_error'] = $updateResult['errorCode'];
	}
	$mod = '';
}
*/



#################################
### START: FIND THIS ACTIVITY ###
#################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('CC_dms.fp7','sedl_service_log');
$search -> SetDBPassword($webPW,$webUN);

//echo '<p>$related_entries_update_trigger: '.$related_entries_update_trigger;
//echo '<p>$related_entries_create_trigger: '.$related_entries_create_trigger;

if(($related_entries_update_trigger != '')||($related_entries_create_trigger != '')){
$search -> AddDBParam('-script','update_related_entries_trigger2');
}


$search -> AddDBParam('c_cwp_row_ID','=='.$row_ID);

//$search -> AddSortParam('resource_ID','ascend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);

//$_SESSION['resource_title'] = $recordData['resource_title'][0];
//$_SESSION['resource_submitted_by'] = $recordData['submitted_by_user_ID'][0];
//$_SESSION['resource_creation_timestamp'] = $recordData['creation_timestamp'][0];
//$_SESSION['submitted_by_email'] = $recordData['submitted_by_user_ID'][0].'@sedl.org';
###############################
### END: FIND THIS ACTIVITY ###
###############################

if($recordData['wg_project_ID'][0] != ''){
#######################################
### START: FIND PROJECT NAME ###
#######################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('CC_dms.fp7','cc_projects');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('project_ID','=='.$recordData['wg_project_ID'][0]);

$searchResult3 = $search3 -> FMFind();

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
$recordData3 = current($searchResult3['data']);
############################################
### END: FIND PROJECT NAME ###
############################################
$project_name = $recordData3['project_name'][0];
}

if($recordData['primary_sedl_client_ID'][0] != ''){
#######################################
### START: FIND PRIMARY CLIENT NAME ###
#######################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('CC_dms.fp7','sedl_client_cwp_subset');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('contact_ID','=='.$recordData['primary_sedl_client_ID'][0]);

$searchResult2 = $search2 -> FMFind();

//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);
############################################
### END: START: FIND PRIMARY CLIENT NAME ###
############################################
$client_name = $recordData2['c_full_name_last_first'][0];
}



$i = 1;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="en-us">
<META NAME="description" CONTENT="The Texas Comprehensive Center (TXCC), funded by the U.S. Department of Education, provides high-quality technical assistance in the state of Texas to the Texas Education Agency.">
<META NAME="keywords" lang="en-us" CONTENT="No Child left behind, NCLB, Texas Comprehensive Center, SEDL, Teaching, resources, spelling, grants">
<META NAME="author" content="Vicki Dimock">
<meta name="Copyright" content="SEDL">
<meta name="Robots" content="index,follow"> 
<title>SEDL - Staff Service Log</title> <!-- page title -->
<link rel="shortcut icon" href="http://www.sedl.org/imagesN/SEDL.ico">
<link href="http://www.sedl.org/css/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
//<!--
function confirmDelete() { 
	var answer = confirm ("Delete this record? This action will remove this activity from the SEDL Service Log database.")
	if (!answer) {
	return false;
	}
}

/*
function confirmApprove() { 
	var answer = confirm ("Approve this resource? This resource will be made available in Show All and Search Results screens. Please make sure this resource was created by CC Network member before approving.")
	if (!answer) {
	return false;
	}
}

function confirmDisapprove() { 
	var answer = confirm ("Dis-approve this resource? This resource will no longer be available in Show All and Search Results screens.")
	if (!answer) {
	return false;
	}
}
*/
// -->
</script>



<script language="javascript" type="text/javascript">     


<!--

function win1() {
    window.open("<?php echo 'service_log_client_view.php?action=search_go&contact_ID='.$recordData['primary_sedl_client_ID'][0];?>","Window1","menubar=yes,width=500,height=700,toolbar=no, scrollbars=1");
}


//-->


function toggle2() {
	var ele = document.getElementById("toggleText2");
	var text = document.getElementById("displayText2");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "EDIT";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "CANCEL";
	}
} 

function toggle3() {
	var ele = document.getElementById("toggleText3");
	var text = document.getElementById("displayText3");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "UPDATE PROJECT LEAD";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "CANCEL";
	}
} 


</script>




</head>

<body bgcolor="#101229">	

<!-- BEGIN: header server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/header_sedl_log_svc.html');?>

<!-- END: header server-side include -->

<!-- BEGIN: PAGE CONTENT -->




<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff" width="760" class="body">

<tr><td><div class="tiny" style="float:left;width:200px;background-color:#ffffff;padding-top:0px;padding-bottom:6px;margin:0px;border-width:0px;border-style:dotted;border-color:#0a5253"><strong>Current User</strong>: <?php echo $_SESSION['user_ID'].' | '.$_SESSION['PrimarySEDLWorkgroup'];?></div>
<div style="float:right;padding-top:0px;padding-bottom:6px"><a href="mailto:service_log@sedl.org?subject=SEDL-Staff-Service-Log-Comments">Suggestions/Comments</a> | <a href="http://www.sedl.org/staff/sims/logs/service_log_menu.php">Main Menu</a> | <a href="http://www.sedl.org/staff">Return to intranet</a></div>
		<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px">

		<tr><td class="body" nowrap><h2 class="txcc">SEDL - Staff Service Log</h2></td><td class="body" align="right" nowrap><a href="service_log.php?action=show_mine&group_size=<?php echo $_SESSION['group_size'];?>">Show my records</a> | <a href="service_log.php?action=new">Submit new activity</a></td></tr>
		<tr><td colspan="2">
		<div style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">
			<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=10 border=0 bordercolor="cccccc" valign="top" class="dotted-box-orc" style="padding-top:0px">
			<?php if($_SESSION['new_activity_submitted'] == '1'){ ?>
	
			<tr><td class="body" colspan="2"><p class="alert_small">Your activity was successfully submitted. <!--If this resource was a .pdf, .doc, or .ppt file, click here to <a href="cc_network_resources_files.php?action=select&resource_ID=<?php echo $recordData['resource_ID'][0];?>&row_ID=<?php echo $recordData['c_row_ID'][0];?>">upload the file</a> to the server.--></p></td></tr>
	
	
			
			<?php $_SESSION['new_activity_submitted'] = '';
			}?>
			
			<?php if($_SESSION['activity_updated'] == '1'){ ?>
	
			<tr><td class="body" colspan="2"><p class="alert_small">Your activity was successfully updated.</p></td></tr>
			
			<?php $_SESSION['activity_updated'] = '';
			}?>
	
	
			<?php if($_SESSION['new_activity_submitted'] == '2'){ ?>
	
			<tr><td class="body" colspan="2"><p class="alert_small">User: <?php echo $_SESSION['tadds_user_ID'];?>: There was a problem submitting your activity. E-mail <a href="mailto:service_log@sedl.org">service_log@sedl.org</a> and report error code: <?php echo $_SESSION['new_activity_submitted_errorcode'];?>.</p></td></tr>
			
			<?php $_SESSION['upload_result'] = '';
			}?>
			
			
		
			<tr bgcolor="#ebebeb"><td valign="top" nowrap style="border-bottom:1px dotted;border-color:#c0c9da;padding-bottom:10px"><strong>ACTIVITY DETAILS</strong></td><td align="right" style="padding-bottom:10px;border-bottom:1px dotted;border-color:#c0c9da"><?php if(($recordData['created_by'][0] == $_SESSION['user_ID'])||($recordData['surrogate_name'][0] == $_SESSION['user_ID'])){?><a href="service_log.php?action=show_1&row_ID=<?php echo $recordData['c_cwp_row_ID'][0];?>&mod=delete&service_log_ID=<?php echo $recordData['service_log_ID'][0];?>" onclick="return confirmDelete()">Delete</a> | <a href="service_log.php?action=edit&row_ID=<?php echo $recordData['c_cwp_row_ID'][0];?>">Edit</a><?php }?></td></tr>
	
			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Submitted:</td><td class="body" width="100%" style="background-color:#ffffff"><?php echo $recordData['creation_timestamp'][0];?> by <?php echo $recordData['created_by'][0];?> | Last modified: <?php echo $recordData['last_mod_timestamp'][0];?></td></tr>


<?php if($recordData['surrogate_name'][0] != ''){ ?>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Surrogate for:</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['surrogate_name'][0];?></td></tr>

<?php } ?> 


			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Record ID:</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['service_log_ID'][0];?></td></tr>
	
			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Activity Intensity:</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['activity_intensity'][0];?> <?php if($recordData['related_entries'][0] !== ''){echo ' | Related entries: '.$recordData['c_related_entries_csv'][0];} ?></td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Activity Name:</td><td class="body" style="background-color:#ffffff"><?php echo stripslashes($recordData['activity_name'][0]);?><?php if($recordData['project_lead'][0] != ''){?>
			<br><em>(NOTE: Project lead changed to: <?php echo $recordData['project_lead'][0];?>)</em>

				<?php if($recordData['created_by'][0] == $_SESSION['user_ID']){?> | 
	
					<span class="tiny"><a href="javascript:toggle2();" id="displayText2">EDIT</a></span>
					<div id="toggleText2" style="display: none">
					<form method="get">
					<input type="hidden" name="action" value="show_1">
					<input type="hidden" name="mod" value="update_project_lead">
					<input type="hidden" name="row_ID" value="<?php echo $recordData['c_cwp_row_ID'][0];?>">
					Select new project lead: 
					<select name="project_lead">
					<option value="">
						
						<?php foreach($v1Result['valueLists']['staff_sims_user_IDs_svc_log'] as $key => $value) { ?>
						<option value="<?php echo $value;?>" <?php if($recordData['project_lead'][0] == $value){echo ' SELECTED';}?>> <?php echo $value; ?>
						<?php } ?>

					</select>
					<input type="submit" name="submit" value="Submit">
					</form>
					</div>
	
				<?php } ?>

			<?php }else{ ?>
			
				<?php if($recordData['created_by'][0] == $_SESSION['user_ID']){?> | 
	
				
				
					<span class="tiny"><a href="javascript:toggle3();" id="displayText3">UPDATE PROJECT LEAD</a></span>
					<div id="toggleText3" style="display: none">
					<form method="get">
					<input type="hidden" name="action" value="show_1">
					<input type="hidden" name="mod" value="update_project_lead">
					<input type="hidden" name="row_ID" value="<?php echo $recordData['c_cwp_row_ID'][0];?>">
					Select new project lead: 
					<select name="project_lead">
					<option value="">
						
						<?php foreach($v1Result['valueLists']['staff_sims_user_IDs_svc_log'] as $key => $value) { ?>
						<option value="<?php echo $value;?>" <?php if($recordData['project_lead'][0] == $value){echo ' SELECTED';}?>> <?php echo $value; ?>
						<?php } ?>

					</select>
					<input type="submit" name="submit" value="Submit">
					</form>
					</div>
	
				<?php } ?>

			<?php }?></td></tr>
	
			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Project Funding Stream:</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['project_funding_stream'][0];?></td></tr>

<?php if(($recordData['wg_project_ID'][0] != '')||($recordData['wg_usde_priority_area'][0] != '')) { ?>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Project:</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['wg_project_ID'][0];?> | <?php echo $project_name;?></td></tr>

			<?php if($recordData['wg_txcc_tea_partners'][0] != '') { ?>
			
			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>TEA-TXCC Partner(s):</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['wg_txcc_tea_partners'][0];?></td></tr>
			
			<?php } ?>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Priority Area(s):</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['wg_usde_priority_area'][0];?></td></tr>

<?php } ?>
			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Activity Type(s):</td><td class="body" style="background-color:#ffffff">
			
			
			<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_type'] as $key => $value) {  
				if (strpos($recordData['activity_type'][0],$value) !== false) {
				echo $value; ?><br>
			<?php }
			} ?>
			<?php if($recordData['activity_type_other_specify'][0] !== ''){echo 'Other: '.$recordData['activity_type_other_specify'][0];}?>
			
			</td></tr>


			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Activity Date(s):</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['activity_begin_date'][0];?> <?php if($recordData['activity_begin_date'][0] !== $recordData['activity_end_date'][0]){ echo ' to '.$recordData['activity_end_date'][0];}?></td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Activity Scope:</td><td class="body" style="background-color:#ffffff"><?php if($recordData['activity_location_scope'][0] == 'National'){ echo $recordData['activity_location_scope'][0];} else { echo $recordData['activity_location_scope'][0].' | '.$recordData['activity_location_state'][0];}?>
			
			</td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Contact Method:</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['contact_method'][0];?></td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Activity Duration:</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['activity_duration'][0];?></td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>
			Clients Served:</td><td class="body" width="100%" style="background-color:#ffffff;padding-top:6px;border-color:#c0c9da;border-top-width:1px;border-bottom-width:1px;border-left-width:0px;border-right-width:0px;border-style:dotted"">

				<table>
				<tr><td style="text-align:top;border-right-width:1px;border-left-width:0px;border-top-width:0px;border-bottom-width:0px;border-style:dotted;border-color:#c0c9da;padding-right:15px;padding-top:0px;margin-top:0px" valign="top">


						<table>
		
						<tr><td style="padding-top:0px">Administrator (school or district)</td><td align="right" style="padding-left:20px;padding-top:0px"><?php echo $recordData['client_served_count_administrators'][0];?></td></tr>
						<tr><td>Afterschool Provider</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_afterschool_provider'][0];?></td></tr>
						<tr><td>Disability Svcs. Provider</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_hds_provider'][0];?></td></tr>
						<tr><td>Employer</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_employer'][0];?></td></tr>
						<tr><td>Funding Rep.</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_foundation'][0];?></td></tr>
						<tr><td>IEA (ESC)</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_IEA'][0];?></td></tr>
						<tr><td>IHE (Higher Ed)</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_IHE'][0];?></td></tr>
						<tr><td>Instructional Coach/Specialist</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_instr_coaches_specialists'][0];?></td></tr>
						<tr><td>Knowledge Translation Prof.</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_ktp'][0];?></td></tr>
						<tr><td>Media Rep.</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_reporter'][0];?></td></tr>
		
						</table>
			
			
				</td><td style="text-align:top;padding-left:15px;padding-top:0px;margin-top:0px" valign="top">
				
						<table>
		
						<tr><td style="padding-top:0px">Parent</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_parents'][0];?></td></tr>
						<tr><td>PIRC Staff</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_PIRC_staff'][0];?></td></tr>
						<tr><td>Policymaker</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_policymakers'][0];?></td></tr>
						<tr><td>Prof. Association</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_prof_associations'][0];?></td></tr>
						<tr><td>Researcher</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_other_research_provider'][0];?></td></tr>
						<tr><td>SEA</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_SEA'][0];?></td></tr>
						<tr><td>Student</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_students'][0];?></td></tr>
						<tr><td>Teacher</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_teachers'][0];?></td></tr>
						<tr><td>Training/TA Svc. Provider</td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_other_TA'][0];?></td></tr>
						
						<?php if($recordData['client_served_other_specify'][0] !== ''){ ?>
						<tr><td>Other: <?php echo $recordData['client_served_other_specify'][0];?></td><td align="right" style="padding-left:20px"><?php echo $recordData['client_served_count_other_specify'][0];?></td></tr>
						<?php } else { ?>
						<tr><td>&nbsp;</td><td align="right" style="padding-left:20px">&nbsp;</td></tr>
						<?php } ?>
						</table>
				
				</td></tr>
				</table>
			
			</td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Primary Requestor ID:</td><td class="body" style="background-color:#ffffff"><?php if($recordData['primary_sedl_client_ID'][0] == ''){ echo 'N/A';} else { echo '<a href="javascript:win1()" onMouseOver="self.status=\'Open A Window\'; return true;">'.$recordData['primary_sedl_client_ID'][0];?> | <?php echo $client_name.'</a>';}?></td></tr>
	
			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>SEDL Unit:</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['sedl_workgroup'][0];?></td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Comments:</td><td class="body" style="background-color:#ffffff;padding-top:0px" valign="top"><?php echo stripslashes($recordData['notes'][0]);?></td></tr>
	
			<?php if($src == 'my_list'){ ?><tr><td align="center" valign="top" colspan="2"><hr size="1" class="ee" /><br /><?php if($previous != 'none'){ ?><a href="service_log.php?action=show_1&row_ID=<?php echo $previous;?>&src=my_list"><< Previous Record</a> | <?php } ?><?php if($next != 'none'){ ?><a href="service_log.php?action=show_1&row_ID=<?php echo $next;?>&src=my_list">Next Record >></a><?php } ?></td></tr><?php } ?>
	
	
			</table>

		</td></tr>
		</table>
</div>
</td></tr>
</table>

<!-- BEGIN: footer server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/footer_sedl_log_svc.html');?>

<!-- END: footer server-side include -->


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


} elseif ($action == 'edit') { 

###COLLECT THE FORM-SUBMITTED VALUES INTO VARIABLES###
//$resource_ID = $_GET['resource_ID'];
$row_ID = $_GET['row_ID'];

################################################################
### START: FIND ACTIVITY RECORD AND DISPLAY EDIT FORM FIELDS ###
################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('CC_dms.fp7','sedl_service_log');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('c_cwp_row_ID','=='.$row_ID);

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);
##############################################################
### END: FIND ACTIVITY RECORD AND DISPLAY EDIT FORM FIELDS ###
##############################################################

#######################################################
### START: FIND LOG ID'S FOR THIS USER'S ACTIVITIES ###
#######################################################
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('CC_dms.fp7','sedl_service_log_IDs_only','all');
$search2 -> SetDBPassword($webPW,$webUN);

$search2 -> AddDBParam('c_created_by_project_lead',$_SESSION['user_ID']);
//$search2 -> AddDBParam('cc_host','TXCC');
$search2 -> AddSortParam('activity_begin_date','descend');
$searchResult2 = $search2 -> FMFind();
//echo $searchResult2['errorCode'];
//echo $searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);
#####################################################
### END: FIND LOG ID'S FOR THIS USER'S ACTIVITIES ###
#####################################################

#######################################################
### START: GET SEDL PROJECT ABBREVIATIONS FROM SIMS ###
#######################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','budget_codes_fund_year_only','all');
$search3 -> SetDBPassword($webPW,$webUN);

//$search3 -> AddDBParam('created_by','=='.$_SESSION['user_ID']);
$search3 -> AddDBParam('c_svc_log_select_list','1');
$search3 -> AddSortParam('c_fund_year','ascend');
$searchResult3 = $search3 -> FMFindall();
//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//$recordData3 = current($searchResult3['data']);

$i=0;
foreach($searchResult3['data'] as $key => $searchData3) { 

$fundyear[$i] = $searchData3['c_fund_year'][0].' - '.$searchData3['BudgetCodeDescription'][0];
$i++;

} 

$fund_year_unique = array_unique($fundyear);
#####################################################
### END: GET SEDL PROJECT ABBREVIATIONS FROM SIMS ###
#####################################################


$cols = 4;
$count = 0;


###QUERY FMP LAYOUT FOR VALUELISTS###

$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('CC_dms.fp7','sedl_service_log');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();


###############################################################
### START: GET ACTIVITY TYPE VALUES FOR VALUE-LIST ###
###############################################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('CC_dms.fp7','sedl_service_log_activity_types','all');
$search4 -> SetDBPassword($webPW,$webUN);

//$search4 -> AddSortParam('resource_ID','ascend');
//$search4 -> AddSortParam('item','ascend');

$searchResult4 = $search4 -> FMFindall();

//echo $searchResult4['errorCode'];
//echo $searchResult4['foundCount'];
$recordData4 = current($searchResult4['data']);
#############################################################
### END: GET ACTIVITY TYPE VALUES FOR VALUE-LIST ###
#############################################################

######################################################
## START: GET WORKGROUP PROJECTS for the drop-down list
######################################################
$search5 = new FX($serverIP,$webCompanionPort);
$search5 -> SetDBData('CC_dms.fp7','cc_projects', 'all');
$search5 -> SetDBPassword($webPW,$webUN);
$search5 -> AddDBParam('c_active_status','Active');

$search5 -> AddSortParam('cc','ascend');
//$search5 -> AddSortParam('project_ID','ascend');
$search5 -> AddSortParam('project_number','ascend');

$searchResult5 = $search5 -> FMFind();

//echo $searchResult5['errorCode'];
//echo $searchResult5['foundCount'];
//$recordData5 = current($searchResult5['data']);
###################################################
## END: GET WORKGROUP PROJECTS for the drop-down list
###################################################




###PRINT ACTIVITY INFORMATION ON SCREEN FOR EDITING###

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="en-us">
<META NAME="description" CONTENT="The Texas Comprehensive Center (TXCC), funded by the U.S. Department of Education, provides high-quality technical assistance in the state of Texas to the Texas Education Agency.">
<META NAME="keywords" lang="en-us" CONTENT="No Child left behind, NCLB, Texas Comprehensive Center, SEDL, Teaching, resources, spelling, grants">
<META NAME="author" content="Vicki Dimock">
<meta name="Copyright" content="SEDL">
<meta name="Robots" content="index,follow"> 
<title>SEDL - Staff Service Log</title> <!-- page title -->
<link rel="shortcut icon" href="http://www.sedl.org/imagesN/SEDL.ico">
<link href="http://www.sedl.org/css/sims2007.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="http://txcc.sedl.org/orc/common/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script language="JavaScript">

		function UpdateSelect()
		{
		select_value = "";
		select_value = document.form2.activity_intensity.value;
		var id = 'multiple_logs';
		var obj = '';
		obj = (document.getElementById) ? document.getElementById(id) : ((document.all) ? document.all[id] : ((document.layers) ? document.layers[id] : false));
		
		if(select_value == ""){
		  obj.style.display = 'none';
		
		} else if(select_value == "multiple/ongoing"){
		  // alert("You chose Journal article.");
		  // return false;
		  obj.style.display = 'table-row';
		}
		else
		{
		  obj.style.display = 'none';
		}
		
		}


		function UpdateSelect2()
		{
		select_value2 = "";
		select_value2 = document.form2.activity_location_scope.value;
		var id2 = 'activity_location_scope_states';
		var obj2 = '';
		obj2 = (document.getElementById) ? document.getElementById(id2) : ((document.all) ? document.all[id2] : ((document.layers) ? document.layers[id2] : false));
		
		if(select_value2 == ""){
		  obj2.style.display = 'none';
		
		} else if(select_value2 == "Regional" || select_value2 == "State" || select_value2 == "Local"){
		  // alert("You chose Journal article.");
		  // return false;
		  obj2.style.display = 'table-row';
		}
		else
		{
		  obj2.style.display = 'none';
		}
		
		}

		function UpdateSelect3()
		{
		select_value3 = "";
		select_value3 = document.form2.project_funding_stream.value;
		var id3 = 'isp_extra_fields';
		var obj3 = '';
		obj3 = (document.getElementById) ? document.getElementById(id3) : ((document.all) ? document.all[id3] : ((document.layers) ? document.layers[id3] : false));
		
		if(select_value3 == ""){
		  obj3.style.display = 'none';
		
		} else if(select_value3.substring(0,4) == "0305" || select_value3.substring(0,4) == "0315" || select_value3.substring(0,4) == "0202" || select_value3.substring(0,4) == "0212"){
		  // alert("hello");
		  // return false;
		  obj3.style.display = 'table-row';
		}
		else
		{
		  obj3.style.display = 'none';
		}
		
		}

/*
		function UpdateSelect4()
		{
		select_value4 = "";
		select_value4 = document.form2.wg_project_ID.value;
		var id4 = 'txcc_tea_partners';
		var obj4 = '';
		obj4 = (document.getElementById) ? document.getElementById(id4) : ((document.all) ? document.all[id4] : ((document.layers) ? document.layers[id4] : false));
		
		if(select_value4 == ""){
		  obj4.style.display = 'none';
		
		} else if(select_value4 == "98"){
		  // alert("hello");
		  // return false;
		  obj4.style.display = 'table-row';
		}
		else
		{
		  obj4.style.display = 'none';
		}
		
		}
*/

	<!-- 
	function showMe (it, box) { 
	  var vis = (box.checked) ? "block" : "none"; 
	  document.getElementById(it).style.display = vis;
	} 
	//--> 

</script>





<script language="JavaScript">
<!--

function win1() {
    window.open("service_log_client_search.php?action=search","Window1","menubar=yes,width=500,height=700,toolbar=no, scrollbars=1");
}


//-->
</script>


<script language="javascript" type="text/javascript">     
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	plugins : "spellchecker,paste",
	gecko_spellcheck : true,
	theme_advanced_buttons1_add : "pastetext,pasteword",
	theme_advanced_buttons3_add : "spellchecker",
	invalid_elements : "style,span",
    force_br_newlines : true,
    force_p_newlines : false,
	paste_auto_cleanup_on_paste : true,
 	theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
	apply_source_formatting : true,
	convert_urls : false
});
</script>

<script language="JavaScript">
<!--
function checkFields() { 

	// Title
		if (document.form2.resource_title.value == "") {
			alert("Please enter a Resource Title.");
			document.form2.resource_title.focus();
			return false;	}

/*
// Description
		if (document.form2.resource_description.value == "") {
			alert("Please enter a brief Resource Description.");
			document.form2.resource_description.focus();
			return false;	}

*/

// Resource Type
		if ((document.form2.resource_type_product.value == "")&&(document.form2.resource_type_activity.value == "")) {
			alert("Please select the Resource Type.");
			document.form2.resource_type_product.focus();
			return false;	}

/*	// Resource URL (check for blank)
		if (document.form2.URL.value == "") {
			alert("Please enter the Resource URL. If this resource is a file, enter \"N/A\".");
			document.form2.URL.focus();
			return false;	}
*/
	// Resource URL (check for "http://")
		var str=document.form2.resource_url.value;
		if ((document.form2.resource_url.value != "") && (str.substr(0,7) != "http://")) {
			alert("Please enter a valid Resource URL beginning with \"http://\". If there is no URL or this resource is a file you are uploading, leave this field blank.");
			document.form2.resource_url.focus();
			return false;	}




/*
	// Content Area
	user_input = 0;
	for (i=0;i < 6;i++) {
		if (document.form2.content_area[i].checked == true) {
			user_input++;
		}
	}
	if (user_input > 0) {
	} else {
		alert("Please select at least one content area for this resource.");
		return false;
	}
*/

}	
// -->
</script>


<SCRIPT language="JavaScript">

function setTitle(activityName)
{
	document.form2.activity_name.value = activityName;

  //return true;
}
</SCRIPT>



<style type="text/css">

		.form-text td {
		font-family: Verdana,Arial,Helvetica,sans-serif;
		font-size: 11px;
		color: #333333;
		padding:3px;
		vertical-align: text-top;
		}
		


</style>

<style type="text/css">

input:focus, textarea:focus {
background-color:#fcfacf;
}
</style>

</head>

<body bgcolor="#101229"  onLoad="UpdateSelect(); UpdateSelect2(); UpdateSelect3(); UpdateSelect4(); showMe('other_activity_type', this);">	
<script type="text/javascript" src="http://txcc.sedl.org/orc/common/wz_tooltip.js"></script>

<!-- BEGIN: header server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/header_sedl_log_svc.html');?>

<!-- END: header server-side include -->

<!-- BEGIN: PAGE CONTENT -->

<form method="get" id="form2" name="form2" onsubmit="return checkFields()">
<input type="hidden" name="action" value="show_1">
<input type="hidden" name="mod" value="edit_confirm1">
<input type="hidden" name="row_ID" value="<?php echo $row_ID;?>">


<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff" width="760" class="body">

<tr><td><div class="tiny" style="float:left;width:200px;background-color:#ffffff;padding-top:0px;padding-bottom:6px;margin:0px;border-width:0px;border-style:dotted;border-color:#0a5253"><strong>Current User</strong>: <?php echo $_SESSION['user_ID'].' | '.$_SESSION['PrimarySEDLWorkgroup'];?></div>
<div style="float:right;padding-top:0px;padding-bottom:6px"><a href="mailto:service_log@sedl.org?subject=SEDL-Staff-Service-Log-Comments">Suggestions/Comments</a> | <a href="http://www.sedl.org/staff/sims/logs/service_log_menu.php">Main Menu</a> | <a href="http://www.sedl.org/staff">Return to intranet</a></div>

		<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px">
		
		<tr><td nowrap><h2 class="txcc">SEDL - Staff Service Log</h2></td><td align="right"><a href="service_log.php?action=show_mine">Show my records</a> | <a href="service_log.php?action=new">Submit new activity</a></td></tr>
		<tr><td colspan="2">
		<div style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">
			<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=10 border=0 bordercolor="cccccc" valign="top" class="dotted-box-orc" style="padding-top:0px">
		
			<tr><td bgcolor="#ebebeb" valign="top" nowrap><strong>EDIT ACTIVITY</strong></td><td align="right"><input type=button value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Update" class="body"></td></tr>
	

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Submitted:</td><td class="body" width="100%" style="background-color:#ffffff"><?php echo $recordData['creation_timestamp'][0];?> by <?php echo $recordData['created_by'][0];?> | Last modified: <?php echo $recordData['last_mod_timestamp'][0];?></td></tr>
	
			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap>Service Log ID:</td><td class="body" style="background-color:#ffffff"><?php echo $recordData['service_log_ID'][0];?></td></tr>

	<?php if($_SESSION['svc_log_admin_allow_surrogates'] == 'Yes'){ ?>
	
				If this is a surrogate log entry for someone else, enter their name: <input type="text" name="surrogate_name" size="25" value="<?php echo $recordData['surrogate_name'][0];?>"><br>
	
	<?php } ?>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>1</strong></div>
			Activity Intensity:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Indicate whether this is a one-time or multiple/ongoing activity (i.e., with the <em>same primary objective</em> and the <em>same clients</em>). If this activity is one in a series of "multiple/ongoing" activities and relates to a prior log entry, identify the related log entry(-ies) in the box that appears to the right.<p>
			
					<div id="multiple_logs" style="float:right;padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#fff6bf">
					Select previous log entries related to this ongoing activity:<br><span class="tiny">To copy the activity name of a previous log entry, just click the ID.</span>
					<table style="padding-top:4px">

					<?php if($searchResult2['foundCount'] !== 0){ ?>

						<?php foreach($searchResult2['data'] as $key => $searchData2) { ?>
						<tr><td style="margin:0px;padding-top:2px;padding-left:2px;padding-right:4px;padding-bottom:2px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff"><input type="checkbox" name="related_entries[]" value="<?php echo $searchData2['service_log_ID'][0];?>"<?php if (strpos($recordData['related_entries'][0],$searchData2['service_log_ID'][0]) !== false) {	echo 'CHECKED';}?>> <span title="<?php echo $searchData2['activity_begin_date'][0];?> | <?php echo $searchData2['activity_name'][0];?>"><span onclick="setTitle('<?php echo $searchData2['activity_name'][0];?>')"><?php echo $searchData2['service_log_ID'][0];?> - <span class="tiny"><?php echo $searchData2['activity_begin_date'][0];?> | <?php echo $searchData2['activity_name'][0];?></span></span></span></input></td></tr>
						<?php } 

					}else{ ?>

						<tr><td style="margin:0px;padding:12px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff">No previous log entries found.</td></tr>

					<?php }

					?>
					</table>
					<span class="tiny"><strong>TIP</strong>: Hover mouse over log ID to view date and activity name.</span>
					</div>
			
			<select name="activity_intensity" onChange="UpdateSelect();">
			<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_intensity'] as $key => $value) { ?>
			<option  value="<?php echo $value; ?>" <?php if($recordData['activity_intensity'][0] == $value){ echo 'SELECTED';}?> /> <?php echo $value; ?><br>
			<?php } ?>
			</select>
			
			</div>
			</td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>2</strong></div>
			Activity Name:</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb"><p class="alert_small" style="margin-top:0px">NOTE: Only activities that involve some external client audience member(s) should be reported.</p> Enter a short but descriptive title that conveys the essence of the activity and distinguishes it from similar, yet different, activities. The same activity name should only be used for a series of related activities being delivered in a sequence, followed by a number that describes the order of each activity in this sequence (see examples below). Different names should be given to activities when they: (a) are provided to a different client or client group; or (b) are intended to accomplish a different objective.<p>
			<input type="text" name="activity_name" size="65" class="body" value="<?php echo $recordData['activity_name'][0];?>"><br><font color="666666"><span class="tiny">Examples: TX ESC 7 WSM Training Session 1; TX ESC 7 WS Training Session 2</span></font>
			</div>
			</td></tr>
	
	
			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px;margin-top:-5px"><strong>3</strong></div>
			Project Funding <br>Stream:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Select the SEDL budget code for this activity.<p>
			
						<select name="project_funding_stream" onChange="UpdateSelect3();">
						<option value="">
						
						<option value="N/A" <?php if($recordData['project_funding_stream'][0] == 'N/A'){ echo 'SELECTED';}?>> N/A
						<option value=""> -----


						<?php foreach($fund_year_unique as $current) { ?>
						<option value="<?php echo $current;?>" <?php if($recordData['project_funding_stream'][0] == $current){ echo 'SELECTED';}?>> <?php echo $current;?>
						<?php } ?>
						</select>
						
							<div id="isp_extra_fields" style="float:right;border:1px dotted #0a5253; padding:0px; background-color:#fff6bf">
							
							<table style="padding:4px;margin-top:0px"><tr><td>
							<strong>ESS Options:</strong><br>
							
									You have selected an ESS budget code. Please complete the following fields related to this ESS activity.
									<table style="padding:4px;margin-top:0px">
									<tr><td style="padding-left:6px;padding-top:4px;padding-bottom:4px;padding-right:4px;margin-top:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff">
									<strong>Project</strong><br>
									Select the ESS project relating to this activity. <p>
										<select name="wg_project_ID">
		
										<option value="">Select Project
										<option value="">---------------------------
										<option value="">## TXCC Projects ##
										<option value="">---------------------------
										<option value="">==> GOAL 1
										<option value="">---------------------------

										<?php foreach($searchResult5['data'] as $key => $searchData5) { ?>
				
											<?php if(($searchData5['cc'][0] == 'txcc')&&($searchData5['category'][0] == 'Texas CC Work Plan')&&($searchData5['goal'][0] == '1')&&($searchData5['contract'][0] == 'current')) { ?>
					
												<option value="<?php echo $searchData5['project_ID'][0];?>" <?php if($recordData['wg_project_ID'][0] == $searchData5['project_ID'][0]){ echo 'SELECTED';}?>> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData5['project_number'][0].' - ';?><?php echo stripslashes($searchData5['project_name'][0]); ?>
					
											<?php } ?>
				
										<?php } ?>

										<option value="">---------------------------
										<option value="">==> GOAL 2
										<option value="">---------------------------

										<?php foreach($searchResult5['data'] as $key => $searchData5) { ?>
				
											<?php if(($searchData5['cc'][0] == 'txcc')&&($searchData5['category'][0] == 'Texas CC Work Plan')&&($searchData5['goal'][0] == '2')&&($searchData5['contract'][0] == 'current')) { ?>
					
												<option value="<?php echo $searchData5['project_ID'][0];?>" <?php if($recordData['wg_project_ID'][0] == $searchData5['project_ID'][0]){ echo 'SELECTED';}?>> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData5['project_number'][0].' - ';?><?php echo stripslashes($searchData5['project_name'][0]); ?>
					
											<?php } ?>
				
										<?php } ?>


										<option value="">---------------------------
										<option value="">## SECC Projects ##
										<option value="">---------------------------
										<option value="">==> GOAL 1
										<option value="">---------------------------
				
										<?php foreach($searchResult5['data'] as $key => $searchData5) { ?>
				
											<?php if(($searchData5['cc'][0] == 'secc')&&($searchData5['goal'][0] == '1')&&($searchData5['revised'][0] == '2013')) { ?>

											<?php if ($project_num !== substr($searchData5['project_number'][0],0,3)){ echo '<option value="">';} // PRINTS BLANK SPACE BETWEEN PROJECT NUMBER SECTIONS ?>
					
												<option value="<?php echo $searchData5['project_ID'][0];?>" <?php if($recordData['wg_project_ID'][0] == $searchData5['project_ID'][0]){ echo 'SELECTED';}?>> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData5['project_number'][0].' - '.$searchData5['project_name'][0]; ?>
	
												<?php $project_num = substr($searchData5['project_number'][0],0,3); ?>
				
											<?php } ?>

										<?php } ?>

				
				
										<option value="">---------------------------
										<option value="">==> GOAL 2
										<option value="">---------------------------
				
										<?php foreach($searchResult5['data'] as $key => $searchData5) { ?>
				
											<?php if(($searchData5['cc'][0] == 'secc')&&($searchData5['goal'][0] == '2')&&($searchData5['revised'][0] == '2013')) { ?>

											<?php if ($project_num !== substr($searchData5['project_number'][0],0,3)){ ?>
											
											<option value=""><option value=""><?php echo strtoupper($searchData5['project_state'][0]);?><?php }?>
					
												<option value="<?php echo $searchData5['project_ID'][0];?>" <?php if($recordData['wg_project_ID'][0] == $searchData5['project_ID'][0]){ echo 'SELECTED';}?>> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData5['project_number'][0].' - '.$searchData5['project_name'][0]; ?>
					
												<?php $project_num = substr($searchData5['project_number'][0],0,3); ?>

											<?php } ?>
				
										<?php } ?>

				
<!--				
										<option value="">---------------------------------
										<option value="">STAFF GENERATED PROJECTS (SECC)
										<option value="">---------------------------------
				
										<?php foreach($searchResult5['data'] as $key => $searchData5) { ?>
				
											<?php if(($searchData5['category'][0] == 'staff')&&($searchData5['cc'][0] == 'secc')) { ?>
					
												<option value="<?php echo $searchData5['project_ID'][0];?>" <?php if($recordData['wg_project_ID'][0] == $searchData5['project_ID'][0]){ echo 'SELECTED';}?>> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $searchData5['created_by'][0].' - '.stripslashes($searchData5['project_name'][0]); ?>
					
											<?php } ?>
				
										<?php } ?>
-->		
										</select>
										
										<div id="txcc_tea_partners" style="float:right;padding-left:6px;padding-top:4px;padding-bottom:4px;padding-right:4px;margin-top:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff">
										Enter the name(s) of any TEA partner(s) for this activity. (TEA-TXCC Partners)<p>
										<input type="text" name="wg_txcc_tea_partners" size="40" value="<?php echo $recordData['wg_txcc_tea_partners'][0];?>">
										</div>
				
									</td></tr>
									
									<tr><td style="padding-left:6px;padding-top:4px;padding-bottom:4px;padding-right:4px;margin-top:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff">
									<strong>Priority Area</strong><br>	
									Indicate the U.S. Department of Education priority area(s) relating to this activity. Select all that apply.<p>
									
									<input type="checkbox" name="wg_usde_priority_area[]" value="01" <?php if (strpos($recordData['wg_usde_priority_area'][0],"01") !== false) {echo ' checked="checked"';}?>> <span onmouseover="Tip('Ensuring the School Readiness and Success of Preschool-Age Children and Their Successful Transition to Kindergarten', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 1', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Early Learning</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="02" <?php if (strpos($recordData['wg_usde_priority_area'][0],"02") !== false) {echo ' checked="checked"';}?>> <span onmouseover="Tip('Implementing College- and Career-Ready Standards and Aligned, High-Quality Assessments For All Students', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 2', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">College and Career Ready Standards and Assessments</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="03" <?php if (strpos($recordData['wg_usde_priority_area'][0],"03") !== false) {echo ' checked="checked"';}?>> <span onmouseover="Tip('Turning Around the Lowest-Performing Schools', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 3', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Low Performing Schools</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="04" <?php if (strpos($recordData['wg_usde_priority_area'][0],"04") !== false) {echo ' checked="checked"';}?>> <span onmouseover="Tip('Building Rigorous Instructional Pathways That Support the Successful Transition of All Students From Secondary Education to College Without the Need for Remediation, and Careers', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 4', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Rigorous Instructional Pathways</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="05" <?php if (strpos($recordData['wg_usde_priority_area'][0],"05") !== false) {echo ' checked="checked"';}?>> <span onmouseover="Tip('Identifying and Scaling Up Innovative Approaches to Teaching and Learning That Significantly Improve Student Outcomes', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 5', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Innovative Approaches</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="06" <?php if (strpos($recordData['wg_usde_priority_area'][0],"06") !== false) {echo ' checked="checked"';}?>> <span onmouseover="Tip('Identifying, Recruiting, Developing, and Retaining Highly Effective Teachers And Leaders', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 6', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Highly Effective Teachers and Leaders</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="07" <?php if (strpos($recordData['wg_usde_priority_area'][0],"07") !== false) {echo ' checked="checked"';}?>> <span onmouseover="Tip('Using Data-Based Decision-Making to Improve Instructional Practices, Policies, and Student Outcomes', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 7', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Data-Based Decision Making</span><br>
									<input type="checkbox" name="wg_usde_priority_area[]" value="08" <?php if (strpos($recordData['wg_usde_priority_area'][0],"08") !== false) {echo ' checked="checked"';}?>> <span onmouseover="Tip('Increasing the capacity of states to implement their key initiatives statewide and support the school-level implementation of effective practices', WIDTH, 300, TITLE, 'U.S.D.E. Priority Area 8', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Building State Capacity</span><br>

									</td></tr>
									</table>


							</td></tr>
							</table>
							</div>
						

			</div>
			</td></tr>


			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>4</strong></div>
			Activity Type(s):</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Select the activity type(s) that are applicable to this log entry. <strong>Select all that apply</strong>.<p>
				<?php foreach($searchResult4['data'] as $key => $searchData4) { ?>
				<span onmouseover="Tip('<?php echo $searchData4['activity_type_definition'][0];?>', WIDTH, 300, TITLE, '<?php echo $searchData4['activity_type'][0];?>', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">
				<input type="checkbox" name="activity_type[]" value="<?php echo $searchData4['activity_type'][0];?>"<?php 
					if (strpos($recordData['activity_type'][0],$searchData4['activity_type'][0]) !== false) {
					echo 'CHECKED';
					}
					?>> <?php echo $searchData4['activity_type'][0]; ?></span><br>
				<?php } ?>
				
				
					<div id="other_activity_type" style="width:400px;float:right;padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#fff6bf">
					Specify other activity type by selecting from the drop-down list or entering a new activity type in the space provided:<br>
					<table style="padding-top:4px"><tr><td>
									<select name="activity_type_other_select" class="body" id="activity_type_other_select">
									<option value="">Select activity type
									<option value="">-------
									<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_type_other'] as $key => $value) { ?>
									<option value="<?php echo $value;?>" <?php 
									if ($recordData['activity_type_other_specify'][0] == $value) { echo ' SELECTED';}?>> <?php echo $value; ?>
									<?php } ?>
									</select>			
					</td>
					
					<td> &nbsp;<strong>OR</strong>&nbsp; <input type="text" name="activity_type_other_specify" size="20" maxlength="30" class="body" value="Enter new activity type" onclick="JavaScript: document.getElementById('activity_type_other_select').value = '';"></td>
					
					</tr></table>
					</div>

				<p>
				<input type="checkbox" name="activity_type_other" id="activity_type_other" value="Yes"  onClick="showMe('other_activity_type', this);" <?php 
					if (strpos($recordData['activity_type_other'][0],'Yes') !== false) {
					echo 'CHECKED';
					}
					?>> <label for="activity_type_other">Other</label></input><br>
				
				
			</div>
			</td></tr>

			<tr><td class="body" bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>5</strong></div>
			Activity Date(s):</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb;float:right">Enter the beginning and ending date(s) for this activity. If the activity happened on a single day, enter the same date for both.
			
			<p style="text-align:right;padding-right:90px">

							Beginning Date: 			 
							<select name="start_date_m" class="body">
							<option value="">Month
							<option value="">-------
							<?php foreach($v1Result['valueLists']['month_num'] as $key => $value) { ?>
							<option value="<?php echo $value;?>" <?php if($recordData['start_date_m'][0] == $value){ echo 'SELECTED';}?>> <?php echo $value; ?>
							<?php } ?>
							</select>
							
							 
							<select name="start_date_d" class="body">
							<option value="">Day
							<option value="">-------
							<?php foreach($v1Result['valueLists']['day_num'] as $key => $value) { ?>
							<option value="<?php echo $value;?>" <?php if($recordData['start_date_d'][0] == $value){ echo 'SELECTED';}?>> <?php echo $value; ?>
							<?php } ?>
							</select> 
							
							 
							<select name="start_date_y" class="body">
							<option value="">Year
							<option value="">-------
							<?php foreach($v1Result['valueLists']['year_num'] as $key => $value) { 
							if($value >= date("Y")-2){ ?><option value="<?php echo $value;?>" <?php if($recordData['start_date_y'][0] == $value){ echo 'SELECTED';}?>> <?php echo $value; } ?>
							<?php } ?>
							</select> 
							
							<br>&nbsp;<br>Ending Date: 
			
							<select name="end_date_m" class="body">
							<option value="">Month
							<option value="">-------
							<?php foreach($v1Result['valueLists']['month_num'] as $key => $value) { ?>
							<option value="<?php echo $value;?>" <?php if($recordData['end_date_m'][0] == $value){ echo 'SELECTED';}?>> <?php echo $value; ?>
							<?php } ?>
							</select>
							
							 
							<select name="end_date_d" class="body">
							<option value="">Day
							<option value="">-------
							<?php foreach($v1Result['valueLists']['day_num'] as $key => $value) { ?>
							<option value="<?php echo $value;?>" <?php if($recordData['end_date_d'][0] == $value){ echo 'SELECTED';}?>> <?php echo $value; ?>
							<?php } ?>
							</select> 
							
							 
							<select name="end_date_y" class="body">
							<option value="">Year
							<option value="">-------
							<?php foreach($v1Result['valueLists']['year_num'] as $key => $value) { 
							if($value >= date("Y")-2){ ?><option value="<?php echo $value;?>" <?php if($recordData['end_date_y'][0] == $value){ echo 'SELECTED';}?>> <?php echo $value; } ?>
							<?php } ?>
							</select> 

			</p>
			
			</div>			
			</td></tr>
			
	

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>6</strong></div>
			Activity Scope:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb;float:right">Select the geographical scope of this activity. If this is a regional, state, or local activity, indicate the state(s) involved.<p>
			

					<div id="activity_location_scope_states" style="width:400px;float:right;padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#fff6bf">
					Select the state(s) related to this activity:<br>
					<table style="padding-top:4px"><tr><td>

					<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_states'] as $key => $value) { ?>
					<input class="body" type="checkbox" name="activity_location_state[]" value="<?php echo $value;?>" <?php 
					if (strpos($recordData['activity_location_state'][0],$value) !== false) {
					echo 'CHECKED';
					}
					?>> <?php echo $value; ?><br>
					<?php } ?>

					</td></tr>
					</table>
					</div>


			<select name="activity_location_scope" onChange="UpdateSelect2();">
			<option value="">
			<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_geographical_scope'] as $key => $value) { ?>
			<option  value="<?php echo $value; ?>" <?php if($recordData['activity_location_scope'][0] == $value){ echo 'SELECTED';}?>/> <?php echo $value; ?><br>
			<?php } ?>
			</select>
			
			
			
			</div>
			</td></tr>


			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>7</strong></div>
			Contact Method:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Select the <strong>primary</strong> contact method that applies to this activity.<p>
			
			
						<select name="contact_method" class="body">
						<option value="choose">
						
						<?php foreach($v1Result['valueLists']['sedl_svc_log_contact_method'] as $key => $value) { ?>
						<option value="<?php echo $value;?>" <?php if($recordData['contact_method'][0] == $value){ echo 'SELECTED';}?>> <?php echo $value; ?></option>
						<?php } ?>
						</select>

			
			
			</div>
			</td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>8</strong></div>
			Activity Duration:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Select the duration for this activity.<p>

			<?php foreach($v1Result['valueLists']['sedl_svc_log_activity_duration'] as $key => $value) { ?>
			<input type="radio" name="activity_duration" value="<?php echo $value; ?>" <?php if($recordData['activity_duration'][0] == $value){ echo 'CHECKED';}?>> <?php echo $value; ?><br>
			<?php } ?>

			
			</div>
			</td></tr>


			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>9</strong></div>
			Clients Served:</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Enter number (actual or estimated) of clients served for each client type.<p>

				<table>
				<tr><td style="text-align:top;border-right-width:1px;border-left-width:0px;border-top-width:0px;border-bottom-width:0px;border-style:solid;border-color:#cccccc;padding-right:15px" valign="top">





						<table>
		
						<tr><td><span onmouseover="Tip('Examples: superintendent or assistant superintendent, principal or assistant principal, Title 1 coordinator, curriculum coordinator', WIDTH, 300, TITLE, 'ADMINISTRATOR  (school or district)', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Administrator (school or district)</span></td><td><input type="text" name="client_served_count_administrators" size="5" class="body" value="<?php echo $recordData['client_served_count_administrators'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('a provider of afterschool services', WIDTH, 300, TITLE, 'AFTERSCHOOL PROVIDER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Afterschool Provider</span></td><td><input type="text" name="client_served_count_afterschool_provider" size="5" class="body" value="<?php echo $recordData['client_served_count_afterschool_provider'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Examples: occupational therapists, physical therapists, vocational rehabilitation practitioners, allied health practitioners, independent living services staffs', WIDTH, 300, TITLE, 'HEALTH/DISABILITY SERVICES PROVIDER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Disability Svcs. Provider</span></td><td><input type="text" name="client_served_count_hds_provider" size="5" class="body" value="<?php echo $recordData['client_served_count_hds_provider'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Someone who employs, or potentially may employ, individuals with disabilities', WIDTH, 300, TITLE, 'EMPLOYER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Employer</span></td><td><input type="text" name="client_served_count_employer" size="5" class="body" value="<?php echo $recordData['client_served_count_employer'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Examples: staff from U.S. Department of Education or other federal funding agency, foundation staff, SEA staff acting in the capacity of a funding representative', WIDTH, 300, TITLE, 'FUNDING REPRESENTATIVE', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Funding Rep.</span></td><td><input type="text" name="client_served_count_foundation" size="5" class="body" value="<?php echo $recordData['client_served_count_foundation'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Independent Education Agency', WIDTH, 300, TITLE, 'IEA', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">IEA (ESC)</span></td><td><input type="text" name="client_served_count_IEA" size="5" class="body" value="<?php echo $recordData['client_served_count_IEA'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('University administrator or faculty member', WIDTH, 300, TITLE, 'IHE', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">IHE (Higher Ed)</span></td><td><input type="text" name="client_served_count_IHE" size="5" class="body" value="<?php echo $recordData['client_served_count_IHE'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Math coach, literacy coach, others working directly with teachers in a coaching capacity', WIDTH, 300, TITLE, 'INSTRUCTIONAL COACH/SPECIALIST', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Instructional Coach/Specialist</span></td><td><input type="text" name="client_served_count_instr_coaches_specialists" size="5" class="body" value="<?php echo $recordData['client_served_count_instr_coaches_specialists'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Individuals who provide expertise or services related to knowledge translation processes', WIDTH, 300, TITLE, 'KNOWLEDGE TRANSLATION PROFESSIONAL', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Knowledge Translation Prof.</span></td><td><input type="text" name="client_served_count_ktp" size="5" class="body" value="<?php echo $recordData['client_served_count_ktp'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('a representative of the media', WIDTH, 300, TITLE, 'MEDIA REPRESENTATIVE', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Media Rep.</span></td><td><input type="text" name="client_served_count_reporter" size="5" class="body" value="<?php echo $recordData['client_served_count_reporter'][0];?>" style="text-align:right"></td></tr>
		
						</table>
			
			
				</td><td style="text-align:top;padding-left:15px" valign="top">
				
						<table>
		
						<tr><td><span onmouseover="Tip('a parent', WIDTH, 300, TITLE, 'PARENT', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Parent</span></td><td><input type="text" name="client_served_count_parents" size="5" class="body" value="<?php echo $recordData['client_served_count_parents'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Parent Information Resource Center staff', WIDTH, 300, TITLE, 'PIRC STAFF', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">PIRC Staff</span></td><td><input type="text" name="client_served_count_PIRC_staff" size="5" class="body" value="<?php echo $recordData['client_served_count_PIRC_staff'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Examples: Chief state school officers, elected officials, state or local school board members, and/or their staffs', WIDTH, 300, TITLE, 'POLICYMAKER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Policymaker</span></td><td><input type="text" name="client_served_count_policymakers" size="5" class="body" value="<?php echo $recordData['client_served_count_policymakers'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Association staff or board members', WIDTH, 300, TITLE, 'PROFESSIONAL ASSOCIATION ', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Prof. Association</span></td><td><input type="text" name="client_served_count_prof_associations" size="5" class="body" value="<?php echo $recordData['client_served_count_prof_associations'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('Staff of a funded research project or a consultant researcher', WIDTH, 300, TITLE, 'RESEARCHER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Researcher</span></td><td><input type="text" name="client_served_count_other_research_provider" size="5" class="body" value="<?php echo $recordData['client_served_count_other_research_provider'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('State Education Agency', WIDTH, 300, TITLE, 'SEA', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">SEA</span></td><td><input type="text" name="client_served_count_SEA" size="5" class="body" value="<?php echo $recordData['client_served_count_SEA'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('a student', WIDTH, 300, TITLE, 'STUDENT', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Student</span></td><td><input type="text" name="client_served_count_students" size="5" class="body" value="<?php echo $recordData['client_served_count_students'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('a teacher', WIDTH, 300, TITLE, 'TEACHER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Teacher</span></td><td><input type="text" name="client_served_count_teachers" size="5" class="body" value="<?php echo $recordData['client_served_count_teachers'][0];?>" style="text-align:right"></td></tr>
						<tr><td><span onmouseover="Tip('a training or technical assistance service provider', WIDTH, 300, TITLE, 'TRAINING/TA SERVICE PROVIDER', SHADOW, true, FADEIN, 300, FADEOUT, 300, STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true)" onmouseout="UnTip()">Training/TA Svc. Provider</span></td><td><input type="text" name="client_served_count_other_TA" size="5" class="body" value="<?php echo $recordData['client_served_count_other_TA'][0];?>" style="text-align:right"></td></tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
		
						</table>
				
				</td></tr>
				
				<tr><td colspan="2" style="text-align:top;border-right-width:0px;border-left-width:0px;border-top-width:1px;border-bottom-width:0px;border-style:solid;border-color:#cccccc;padding-right:15px;padding-top:10px" valign="top">

					Specify other client type by selecting from the drop-down list or entering a new client type in the space provided. Enter the client count in the box to the right.<br>

					<table style="padding-top:4px"><tr><td>
									<select name="client_served_other_select" class="body" id="client_served_other_select">
									<option value="">Select client type
									<option value="">-------
									<?php foreach($v1Result['valueLists']['sedl_svc_log_client_served_other_specify'] as $key => $value) { ?>
									<option value="<?php echo $value;?>" <?php if($recordData['client_served_other_specify'][0] == $value){ echo 'SELECTED';}?> /> <?php echo $value; ?>
									<?php } ?>
									</select>			
					</td>
					<td> &nbsp;<strong>OR</strong>&nbsp; <input type="text" name="client_served_other_specify" size="20" maxlength="30" class="body" value="Enter new client type" onclick="JavaScript: document.getElementById('client_served_other_select').value = '';"> &nbsp; <input type="text" name="client_served_count_other_specify" size="5" class="body" value="<?php echo $recordData['client_served_count_other_specify'][0];?>" style="text-align:right"></td>
					
					</tr></table>
				
				</td></tr>
				</table>
			
			</div>
			</td></tr>
	
	
	
			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>10</strong></div>
			Primary Requestor <br>ID:</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">If applicable, enter the six-digit client ID of the individual who is the primary requestor for this activity. The primary requestor is an <em>individual</em> who specifically <em>originates</em> a request for SEDL's assistance or involvement, e.g., a chief state school officer who requests a rapid response brief, or a high-ranking SEA official who asks SEDL to provide technical assistance to her staff. To find the requestor ID, <a href="javascript:win1()" onMouseOver="self.status='Open A Window'; return true;">search the SEDL client database</a>.<p>
			<input type="text" name="primary_sedl_client_ID" size="5" class="body" value="<?php echo $recordData['primary_sedl_client_ID'][0];?>">		
			</div>
			
			</td></tr>
	
			<tr><td bgcolor="#ebebeb" align="right" valign="top" nowrap><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>11</strong></div>
			SEDL Unit:
			</td><td class="body" width="100%" valign="top" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Select the SEDL unit that funded, sponsored, or is responsible for delivering the activity.<p>
			
			
						<select name="sedl_workgroup" class="body">
						<option value="choose">
						
						<?php foreach($v1Result['valueLists']['sedl_svc_log_workgroup_affil'] as $key => $value) { ?>
						<option value="<?php echo $value;?>" <?php if($recordData['sedl_workgroup'][0] == $value){ echo 'SELECTED';}?>> <?php echo $value; ?></option>
						<?php } ?>
						</select>

			
			
			</div>
			</td></tr>

			<tr><td bgcolor="#ebebeb" align="right" valign="top"><div style="float:left;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ffffff;padding-left:5px;padding-right:5px;padding-top:3px;padding-bottom:3px;margin-left:-21px;margin-top:-5px"><strong>12</strong></div>
			Comments:</td><td class="body" width="100%" style="background-color:#ffffff">
			<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:8px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">If needed, briefly enter any additional information or notes relating to this activity.
<p class="alert_small">NOTE: Please be brief - only 5-6 lines of text. Please do not paste text from other sources.</p><p>
			<textarea name="notes" cols="70" rows="10" class="body"><?php echo stripslashes($recordData['notes'][0]);?></textarea>
			</div>
			</td></tr>


			<tr><td bgcolor="#ebebeb">&nbsp;</td><td align="right" style="background-color:#ffffff"><input type=button value="Cancel" onClick="history.back()"><input type="submit" name="submit" value="Update" class="body"></td></tr>
			</form>

			</table>

		</td></tr>
		</table>
</div>
</td></tr>
</table>

<!-- BEGIN: footer server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/footer_sedl_log_svc.html');?>

<!-- END: footer server-side include -->

</body>

</html>




<?php 

/* ## THIS SECTION NOT IN USE FOR SERVICE_LOG ##

} elseif ($action == 'approve_submit') { 

###COLLECT THE FORM-SUBMITTED VALUES INTO VARIABLES###
$resource_ID = $_GET['resource_ID'];
$row_ID = $_GET['row_ID'];

###SET RESOURCE STATUS TO APPROVED###
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('CC_dms.fp7','secc_web_resources');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$row_ID);

if($_SESSION ['tadds_user_ID'] == 'sbeckwit'){
$update -> AddDBParam('approval_status_sbeckwit','Yes');
}elseif($_SESSION ['tadds_user_ID'] == 'vdimock'){
$update -> AddDBParam('approval_status_vdimock','Yes');
}

$updateResult = $update -> FMEdit();

//echo '<p>$updateResult[errorCode]: '.$updateResult['errorCode'];
//echo $updateResult['foundCount'];
$updateData = current($updateResult['data']);

if($updateResult['errorCode'] == '0'){

$_SESSION['resource_approved'] = '1';

		if($updateData['active_status'][0] == 'Approved'){
		###SEND E-MAIL APPROVAL NOTIFICATION TO SECC STAFF###
		
		$to = 'eric.waters@sedl.org';
		$subject = 'SECC Resources Web site content APPROVED';
		$message = 
		
		'New content has been approved for the SECC Web site (Resources section) and has been posted online.'."\n\n".
		
		'----------'."\n\n".
		
		'DETAILS:'."\n\n".
		
		'Submitted by: '.$updateData['submitted_by_user_ID'][0]."\n\n".
		
		'Resource ID: '.$resource_ID."\n\n".
		
		'Resource Title: '.stripslashes($updateData['resource_title'][0])."\n\n".
		
		'Description: '.stripslashes($updateData['resource_description'][0])."\n\n".
		
		
		'----------'."\n\n";
		
		
		$headers = 'From: secc_orc@sedl.org'."\r\n".'Reply-To: secc_orc@sedl.org';
		
		mail($to, $subject, $message, $headers);
		
		
		
		}


header('Location: http://txcc.sedl.org/orc/cc_network_resources.php?action=show_1&resource_ID='.$resource_ID);
exit;
}else{
$_SESSION['resource_approved'] = '2';
$_SESSION['resource_approved_errorcode'] = $updateResult['errorCode'];
header('Location: http://txcc.sedl.org/orc/cc_network_resources.php?action=show_all');
exit;
}

###PRINT RESOURCE INFORMATION ON SCREEN WITH APPROVAL CONFIRMATION MESSAGE###
*/
?>






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


} elseif ($action == 'show_all') { 

if($_SESSION['group_size'] == $_GET['group_size']){
//$group_size = $_GET['group_size'];
}else{
//$group_size = 3;
$_SESSION['group_size'] = $_GET['group_size'];

}

if($_GET['group_size'] == ''){
$_SESSION['group_size'] = '10';
}

$skip_size = $_GET['skipsize'];

###FIND AND DISPLAY ALL WEBSITE RESOURCES###

$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('CC_dms.fp7','cc_network_resources',$_SESSION['group_size']);
$search -> SetDBPassword($webPW,$webUN);
$search -> FMSkipRecords($skip_size);

$search -> AddDBParam('approval_status','Approved');

$search -> AddSortParam('resource_title','ascend');

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);

###PRINT NEW INFORMATION ON SCREEN WITH CONFIRMATION MESSAGE###
$results_pages = ceil($searchResult['foundCount'] / $_SESSION['group_size']);

// CAPTURE RETURNED RECORD IDs IN AN ARRAY
$i = 0;
foreach($searchResult['data'] as $key => $searchData) { 

		$my_list[$i] = $searchData['resource_ID'][0];
$i++;
}
$_SESSION['my_list'] = $my_list;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="en-us">
<META NAME="description" CONTENT="The Texas Comprehensive Center (TXCC), funded by the U.S. Department of Education, provides high-quality technical assistance in the state of Texas to the Texas Education Agency.">
<META NAME="keywords" lang="en-us" CONTENT="No Child left behind, NCLB, Texas Comprehensive Center, SEDL, Teaching, resources, spelling, grants">
<META NAME="author" content="Vicki Dimock">
<meta name="Copyright" content="SEDL">
<meta name="Robots" content="index,follow"> 
<title>TXCC - Online Resource Center - CC Network Resources Database</title> <!-- page title -->
<link rel="shortcut icon" href="http://www.sedl.org/imagesN/SEDL.ico">
<link href="http://www.sedl.org/css/sims2007.css" rel="stylesheet" type="text/css">

</head>

<body bgcolor="#101229">	

<!-- BEGIN: header server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/header_cc_orc.html');?>

<!-- END: header server-side include -->

<!-- BEGIN: PAGE CONTENT -->

<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff" width="760">
<tr><td>
		<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px">
		<tr><td><h2 class="txcc">CC Network Resources Database</h2></td><td align="right" nowrap><a href="cc_network_resources.php?action=show_mine">Show mine</a> | <a href="cc_network_resources.php?action=new">Submit new resource</a></td></tr>
		<tr><td colspan="2">

			<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
			<?php if($_SESSION['new_resource_submitted'] == '2'){ ?>
	
			<tr><td class="body" nowrap><p class="alert_small">There was a problem submitting your resource. Contact <a href="mailto:secc_orc@sedl.org">txcc_orc@sedl.org</a> for assistance (ErrorCode: <?php echo $_SESSION['new_resource_submitted_errorcode'];?>)</p></td></tr>
			
			<?php $_SESSION['new_resource_submitted'] = '';
			}?>
	
			<?php if($_SESSION['resource_approved'] == '2'){ ?>
	
			<tr><td class="body" nowrap><p class="alert_small">There was a problem approving this resource. Contact <a href="mailto:secc_orc@sedl.org">txcc_orc@sedl.org</a> for assistance (ErrorCode: <?php echo $_SESSION['resource_approved_errorcode'];?>)</p></td></tr>
			
			<?php $_SESSION['resource_approved'] = '';
			}?>
	
<!--			<tr bgcolor="#B3CCF5"><td class="body">ID</td><td class="body">Resource Title</td><td class="body">Type</td><td class="body" nowrap>Submitted By</td><td class="body" nowrap align="right">Submitted On</td></tr> -->
	
			<?php if($searchResult['foundCount'] == '0'){?>
						<tr valign="top"><td class="body" align="center">No records found.</td></tr>
			<?php }else{ ?>
			<tr valign="top"><td class="body">
			<div style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">

  Click on a title to view a detailed description of the resource, including the URL and source information.


<hr class="ee" style="border-color:#c0c9da">



<div style="float:right;padding-right:35px;padding-top:5px">Results per page 
<?php
if($_SESSION['group_size'] != '10'){echo '<a href="cc_network_resources.php?action=show_all&group_size=10">10</a> | ';}else{echo '<strong>10</strong> | ';}
if($_SESSION['group_size'] != '25'){echo '<a href="cc_network_resources.php?action=show_all&group_size=25">25</a> | ';}else{echo '<strong>25</strong> | ';}
if($_SESSION['group_size'] != '50'){echo '<a href="cc_network_resources.php?action=show_all&group_size=50">50</a>';}else{echo '<strong>50</strong>';}
?>
</div>
<div class="info_plain">Found <strong><?php echo $searchResult['foundCount'];?> resource<?php if($searchResult['foundCount'] > 1){ ?>s<?php }?></strong> sorted by title | <a href="cc_network_menu.php">Search again</a></div>
<hr class="ee" style="border-color:#c0c9da">

Search results pages:
<?php
$num_list_start = $skip_size + 1;


for($i=1;$i <= $results_pages;$i++) { 
$start_rec = ($i-1)*$_SESSION['group_size'];

if($i*$_SESSION['group_size'] - ($_SESSION['group_size'] - 1) == $num_list_start){
echo '<strong>'.$i.'</strong>';
}else{
echo '<a href="cc_network_resources.php?action=show_all&skipsize='.$start_rec.'&group_size='.$_SESSION['group_size'].'">'.$i.'</a>';
}

	if($i< $results_pages){
	echo ' | ';
	}
}
?>



<ol start="<?php echo $num_list_start;?>">

			<?php foreach($searchResult['data'] as $key => $searchData) { ?><?php if($searchData['created_by'][0] == $_SESSION['tadds_user_ID']){?><div style="float:right;padding-top:10px;padding-right:10px"><a href="cc_network_resources.php?action=edit&row_ID=<?php echo $searchData['resource_ID'][0];?>" style="text-decoration:none"><img src="images/page_edit.png" title="Click to edit this resource." style="border:0px"></a></div> <?php }?>
				<h2 class="txcc_list" style="padding-top:10px"><li> <a href="cc_network_resources.php?action=show_1&row_ID=<?php echo $searchData['resource_ID'][0];?>" title="Click here to view more details about this resource." style="text-decoration:none"><?php echo $searchData['resource_title'][0];?></a></h2>
				<p>
				<?php echo $searchData['resource_description'][0];?> (<a href="cc_network_resources.php?action=show_1&row_ID=<?php echo $searchData['resource_ID'][0];?>" title="Click here to view more details about this resource.">more details...</a>)
				</p>
				<p style="border-bottom-width:1px;border-bottom-color:#c0c9da;border-bottom-style:dotted;padding-bottom:10px">
				<strong>Developed by</strong>: <?php echo $searchData['resource_developer'][0];?> | Submitted: <?php echo $searchData['creation_timestamp'][0];?><br>
				<strong>URL</strong>: <a href="<?php echo $searchData['resource_url'][0];?>" target="_blank" title="This link opens a new window and leaves the TXCC web site."><?php echo $searchData['resource_url'][0];?></a>
				
				<?php if($searchData['resource_comments'][0] != ''){ ?>
				<br>&nbsp;<br>
				<strong>Comments</strong>: <?php echo $searchData['resource_comments'][0];?>
				</p>
				
				<?php } ?>
<!--				<tr valign="top"><td class="body"><?php echo $searchData['resource_ID'][0];?></td><td class="body" width="100%"><a href="cc_network_resources.php?action=show_1&row_ID=<?php echo $searchData['resource_ID'][0];?>"><?php echo $searchData['resource_title'][0];?></a></td><td class="body"><?php echo $searchData['resource_type_product'][0];?></td><td class="body" nowrap><?php echo $searchData['created_by'][0];?></td><td class="body" align="right" nowrap><?php echo $searchData['creation_timestamp'][0];?></td></tr> -->

				
			<?php } ?>		
			</ol>
			<?php }?>
			</div>
			</td></tr>
			</table>
		
		</td></tr></table>

</td></tr></table>

<!-- BEGIN: footer server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/footer_sedl_log_svc.html');?>

<!-- END: footer server-side include -->


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


} elseif ($action == 'show_mine') { 

if($_SESSION['group_size'] == $_GET['group_size']){
//$group_size = $_GET['group_size'];
}else{
//$group_size = 3;
$_SESSION['group_size'] = $_GET['group_size'];

}

if($_GET['group_size'] == ''){
$_SESSION['group_size'] = '10';
}

$skip_size = $_GET['skipsize'];

######################################################
### START: FIND AND DISPLAY THIS USER'S ACTIVITIES ###
######################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('CC_dms.fp7','sedl_service_log',$_SESSION['group_size']);
$search -> SetDBPassword($webPW,$webUN);
$search -> FMSkipRecords($skip_size);

$search -> AddDBParam('c_created_by_project_lead',$_SESSION['user_ID']);
//$search -> AddDBParam('cc_host','TXCC');

$search -> AddSortParam('activity_begin_date','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);

$results_pages = ceil($searchResult['foundCount'] / $_SESSION['group_size']);
####################################################
### END: FIND AND DISPLAY THIS USER'S ACTIVITIES ###
####################################################



// CAPTURE RETURNED RECORD IDs IN AN ARRAY
$i = 0;
foreach($searchResult['data'] as $key => $searchData) { 

		$my_list[$i] = $searchData['resource_ID'][0];
$i++;
}
$_SESSION['my_list'] = $my_list;

###PRINT NEW INFORMATION ON SCREEN WITH CONFIRMATION MESSAGE###

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="en-us">
<META NAME="description" CONTENT="The Texas Comprehensive Center (TXCC), funded by the U.S. Department of Education, provides high-quality technical assistance in the state of Texas to the Texas Education Agency.">
<META NAME="keywords" lang="en-us" CONTENT="No Child left behind, NCLB, Texas Comprehensive Center, SEDL, Teaching, resources, spelling, grants">
<META NAME="author" content="Vicki Dimock">
<meta name="Copyright" content="SEDL">
<meta name="Robots" content="index,follow"> 
<title>SEDL - Staff Service Log</title> <!-- page title -->
<link rel="shortcut icon" href="http://www.sedl.org/imagesN/SEDL.ico">
<link href="http://www.sedl.org/css/sims2007.css" rel="stylesheet" type="text/css">

</head>

<body bgcolor="#101229">	

<!-- BEGIN: header server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/header_sedl_log_svc.html');?>

<!-- END: header server-side include -->

<!-- BEGIN: PAGE CONTENT -->

<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff" width="760">
<tr><td><div class="tiny" style="float:left;width:200px;background-color:#ffffff;padding-top:0px;padding-bottom:6px;margin:0px;border-width:0px;border-style:dotted;border-color:#0a5253"><strong>Current User</strong>: <?php echo $_SESSION['user_ID'].' | '.$_SESSION['PrimarySEDLWorkgroup'];?></div>
<div style="float:right;padding-top:0px;padding-bottom:6px"><a href="mailto:service_log@sedl.org?subject=SEDL-Staff-Service-Log-Comments">Suggestions/Comments</a> | <a href="http://www.sedl.org/staff/sims/logs/service_log_menu.php">Main Menu</a> | <a href="http://www.sedl.org/staff">Return to intranet</a></div>

		<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px">
		<tr><td><h2 class="txcc">SEDL - Staff Service Log</h2></td><td align="right" nowrap><span style="color:#666666">Show my records</span> | <a href="service_log.php?action=new">Submit new activity</a></td></tr>
		<tr><td colspan="2">

			<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
			<?php if($_SESSION['activity_deleted'] == '1'){ ?>
	
			<tr><td class="body" nowrap><p class="alert_small">Your activity was deleted.</p></td></tr>
			
			<?php $_SESSION['activity_deleted'] = '';
			}?>
			
			<?php if($_SESSION['new_activity_submitted'] == '2'){ ?>
	
			<tr><td class="body" nowrap><p class="alert_small">There was a problem submitting your entry - errorCode: <?php echo $_SESSION['new_activity_submitted_errorcode'];?></p></td></tr>
			
			<?php }	?>
			
	
	
<!--			<tr bgcolor="#B3CCF5"><td class="body">ID</td><td class="body">Resource Title</td><td class="body">Type</td><td class="body" nowrap>Submitted By</td><td class="body" nowrap align="right">Submitted On</td></tr> -->
	
			<?php if($searchResult['foundCount'] == '0'){?>
						<tr valign="top"><td class="body" align="center">No records found.</td></tr>
			<?php }else{ ?>
			<tr valign="top"><td class="body">
			<div style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">

  Click on a title to view, edit, or delete an activity.


<hr class="ee" style="border-color:#c0c9da">



<div style="float:right;padding-right:35px;padding-top:5px">Results per page 
<?php
if($_SESSION['group_size'] != '10'){echo '<a href="service_log.php?action=show_mine&group_size=10">10</a> | ';}else{echo '<strong>10</strong> | ';}
if($_SESSION['group_size'] != '25'){echo '<a href="service_log.php?action=show_mine&group_size=25">25</a> | ';}else{echo '<strong>25</strong> | ';}
if($_SESSION['group_size'] != '50'){echo '<a href="service_log.php?action=show_mine&group_size=50">50</a>';}else{echo '<strong>50</strong>';}
?>
</div>
<div class="info_plain">Found <strong><?php echo $searchResult['foundCount'];?> activit<?php if($searchResult['foundCount'] > 1){ ?>ies<?php }else{?>y<?php }?></strong> for <?php echo $_SESSION['user_ID'];?></div>
<hr class="ee" style="border-color:#c0c9da">

Search results pages:
<?php
$num_list_start = $skip_size + 1;


for($i=1;$i <= $results_pages;$i++) { 
$start_rec = ($i-1)*$_SESSION['group_size'];

if($i*$_SESSION['group_size'] - ($_SESSION['group_size'] - 1) == $num_list_start){
echo '<strong>'.$i.'</strong>';
}else{
echo '<a href="service_log.php?action=show_mine&skipsize='.$start_rec.'&group_size='.$_SESSION['group_size'].'">'.$i.'</a>';
}

	if($i< $results_pages){
	echo ' | ';
	}
}
?>



				<table cellpadding="5" style="margin-top:10px;border-color:#c0c9da;border-width:1px;border-style:dotted">
				<tr valign="top" style="padding-top:5px;background-color:#c0c9da"><td class="body">Log ID</td><td class="body">Begin Date</td><td class="body" nowrap>End Date</td><td class="body" width="100%">Activity Name</td><td class="body" nowrap>Scope</td></tr>

				<?php foreach($searchResult['data'] as $key => $searchData) { ?>

				<tr valign="top"><td class="body" style="padding-top:5px;color:#666666"><?php echo $searchData['service_log_ID'][0];?></td><td class="body" style="padding-top:5px"><?php echo $searchData['activity_begin_date'][0];?></td><td class="body" nowrap style="padding-top:5px"><?php echo $searchData['activity_end_date'][0];?></td><td class="body" style="padding-top:5px"><a href="service_log.php?action=show_1&row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>&src=my_list" title="Click here to view more details about this activity." style="text-decoration:none"><?php echo $searchData['activity_name'][0];?></a></td><td class="body" nowrap style="padding-top:5px"><?php echo $searchData['activity_location_scope'][0];?><?php if(($searchData['activity_location_scope'][0] !== 'National')&&($searchData['activity_location_scope'][0] !== 'International')){echo ' | '.$searchData['activity_location_state'][0];}?></td></tr>
				
				<?php } ?>
		
				</table>

			<?php }?>
			</div>
			</td></tr>
			</table>
		
		</td></tr></table>

</td></tr></table>

<!-- BEGIN: footer server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/footer_sedl_log_svc.html');?>

<!-- END: footer server-side include -->


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


} elseif ($action == 'show_workgroup') { 

if($_SESSION['group_size'] == $_GET['group_size']){
//$group_size = $_GET['group_size'];
}else{
//$group_size = 3;
$_SESSION['group_size'] = $_GET['group_size'];

}

if($_GET['group_size'] == ''){
$_SESSION['group_size'] = '10';
}

$skip_size = $_GET['skipsize'];

######################################################
### START: FIND AND DISPLAY WORKGROUP ACTIVITIES ###
######################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('CC_dms.fp7','sedl_service_log',$_SESSION['group_size']);
$search -> SetDBPassword($webPW,$webUN);
$search -> FMSkipRecords($skip_size);

$search -> AddDBParam('sedl_workgroup','=='.$_SESSION['PrimarySEDLWorkgroup']);
//$search -> AddDBParam('cc_host','TXCC');

$search -> AddSortParam('activity_begin_date','descend');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData = current($searchResult['data']);

$results_pages = ceil($searchResult['foundCount'] / $_SESSION['group_size']);
####################################################
### END: FIND AND DISPLAY WORKGROUP ACTIVITIES ###
####################################################



// CAPTURE RETURNED RECORD IDs IN AN ARRAY
$i = 0;
foreach($searchResult['data'] as $key => $searchData) { 

		$my_list[$i] = $searchData['resource_ID'][0];
$i++;
}
$_SESSION['my_list'] = $my_list;

###PRINT NEW INFORMATION ON SCREEN WITH CONFIRMATION MESSAGE###

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="en-us">
<META NAME="description" CONTENT="The Texas Comprehensive Center (TXCC), funded by the U.S. Department of Education, provides high-quality technical assistance in the state of Texas to the Texas Education Agency.">
<META NAME="keywords" lang="en-us" CONTENT="No Child left behind, NCLB, Texas Comprehensive Center, SEDL, Teaching, resources, spelling, grants">
<META NAME="author" content="Vicki Dimock">
<meta name="Copyright" content="SEDL">
<meta name="Robots" content="index,follow"> 
<title>SEDL - Staff Service Log</title> <!-- page title -->
<link rel="shortcut icon" href="http://www.sedl.org/imagesN/SEDL.ico">
<link href="http://www.sedl.org/css/sims2007.css" rel="stylesheet" type="text/css">

</head>

<body bgcolor="#101229">	

<!-- BEGIN: header server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/header_sedl_log_svc.html');?>

<!-- END: header server-side include -->

<!-- BEGIN: PAGE CONTENT -->

<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff" width="760">
<tr><td><div class="tiny" style="float:left;width:200px;background-color:#ffffff;padding-top:0px;padding-bottom:6px;margin:0px;border-width:0px;border-style:dotted;border-color:#0a5253"><strong>Current User</strong>: <?php echo $_SESSION['user_ID'].' | '.$_SESSION['PrimarySEDLWorkgroup'];?></div>
<div style="float:right;padding-top:0px;padding-bottom:6px"><a href="mailto:service_log@sedl.org?subject=SEDL-Staff-Service-Log-Comments">Suggestions/Comments</a> | <a href="http://www.sedl.org/staff/sims/logs/service_log_menu.php">Main Menu</a> | <a href="http://www.sedl.org/staff">Return to intranet</a></div>

		<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px">
		<tr><td><h2 class="txcc">SEDL - Staff Service Log</h2></td><td align="right" nowrap><span style="color:#666666"><a href="service_log.php?action=show_mine">Show my records</a></span> | <a href="service_log.php?action=new">Submit new activity</a></td></tr>
		<tr><td colspan="2">

			<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
			<?php if($_SESSION['activity_deleted'] == '1'){ ?>
	
			<tr><td class="body" nowrap><p class="alert_small">Your activity was deleted.</p></td></tr>
			
			<?php $_SESSION['activity_deleted'] = '';
			}?>
	
	
<!--			<tr bgcolor="#B3CCF5"><td class="body">ID</td><td class="body">Resource Title</td><td class="body">Type</td><td class="body" nowrap>Submitted By</td><td class="body" nowrap align="right">Submitted On</td></tr> -->
	
			<?php if($searchResult['foundCount'] == '0'){?>
						<tr valign="top"><td class="body" align="center">No records found.</td></tr>
			<?php }else{ ?>
			<tr valign="top"><td class="body">
			<div style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">

  Click on an activity name to view a log entry.


<hr class="ee" style="border-color:#c0c9da">



<div style="float:right;padding-right:35px;padding-top:5px">Results per page 
<?php
if($_SESSION['group_size'] != '10'){echo '<a href="service_log.php?action=show_workgroup&group_size=10">10</a> | ';}else{echo '<strong>10</strong> | ';}
if($_SESSION['group_size'] != '25'){echo '<a href="service_log.php?action=show_workgroup&group_size=25">25</a> | ';}else{echo '<strong>25</strong> | ';}
if($_SESSION['group_size'] != '50'){echo '<a href="service_log.php?action=show_workgroup&group_size=50">50</a>';}else{echo '<strong>50</strong>';}
?>
</div>
<div class="info_plain">Found <strong><?php echo $searchResult['foundCount'];?> activit<?php if($searchResult['foundCount'] > 1){ ?>ies<?php }else{?>y<?php }?></strong> for <?php echo $_SESSION['PrimarySEDLWorkgroup'];?></div>
<hr class="ee" style="border-color:#c0c9da">

Search results pages:
<?php
$num_list_start = $skip_size + 1;


for($i=1;$i <= $results_pages;$i++) { 
$start_rec = ($i-1)*$_SESSION['group_size'];

if($i*$_SESSION['group_size'] - ($_SESSION['group_size'] - 1) == $num_list_start){
echo '<strong>'.$i.'</strong>';
}else{
echo '<a href="service_log.php?action=show_workgroup&skipsize='.$start_rec.'&group_size='.$_SESSION['group_size'].'">'.$i.'</a>';
}

	if($i< $results_pages){
	echo ' | ';
	}
}
?>



				<table cellpadding="5" style="margin-top:10px;border-color:#c0c9da;border-width:1px;border-style:dotted">
				<tr valign="top" style="padding-top:5px;background-color:#c0c9da"><td class="body">Log ID</td><td class="body">Begin Date</td><td class="body" nowrap>End Date</td><td class="body" width="100%">Activity Name</td><td class="body" nowrap>Scope</td><td class="body" align="right" nowrap>Submitted by</td></tr>

				<?php foreach($searchResult['data'] as $key => $searchData) { ?>

				<tr valign="top"><td class="body" style="padding-top:5px;color:#666666"><?php echo $searchData['service_log_ID'][0];?></td><td class="body" style="padding-top:5px"><?php echo $searchData['activity_begin_date'][0];?></td><td class="body" nowrap style="padding-top:5px"><?php echo $searchData['activity_end_date'][0];?></td><td class="body" style="padding-top:5px"><a href="service_log.php?action=show_1&row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>&src=my_list" title="Click here to view more details about this activity." style="text-decoration:none"><?php echo $searchData['activity_name'][0];?></a></td><td class="body" nowrap style="padding-top:5px"><?php echo $searchData['activity_location_scope'][0];?><?php if(($searchData['activity_location_scope'][0] !== 'National')&&($searchData['activity_location_scope'][0] !== 'International')){echo ' | '.$searchData['activity_location_state'][0];}?></td><td class="body" nowrap style="padding-top:5px" align="right"><?php echo $searchData['created_by'][0];?></td></tr>
				
				<?php } ?>
		
				</table>

			<?php }?>
			</div>
			</td></tr>
			</table>
		
		</td></tr></table>

</td></tr></table>

<!-- BEGIN: footer server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/footer_sedl_log_svc.html');?>

<!-- END: footer server-side include -->


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


} elseif ($action == 'search_go') { 

if($_SESSION['group_size'] == $_GET['group_size']){
//$group_size = $_GET['group_size'];
}else{
//$group_size = 3;
$_SESSION['group_size'] = $_GET['group_size'];

}

if($_GET['group_size'] == ''){
$_SESSION['group_size'] = '10';
}

$skip_size = $_GET['skipsize'];
$sortby = $_GET['sortby'];
$_SESSION['sortby'] = $sortby;

## GET SEARCH CRITERIA ##
$service_log_ID = $_GET['service_log_ID'];
$keyword = $_GET['keyword'];

$wg_usde_priority_area = $_GET['wg_usde_priority_area'];
$wg_project_ID = $_GET['wg_project_ID'];
$project_funding_stream = $_GET['project_funding_stream'];


$start_date_m = $_GET['start_date_m'];
$start_date_d = $_GET['start_date_d'];
$start_date_y = $_GET['start_date_y'];

$end_date_m = $_GET['end_date_m'];
$end_date_d = $_GET['end_date_d'];
$end_date_y = $_GET['end_date_y'];

if(($start_date_m != '')&&($start_date_d != '')&&($start_date_y != '')&&($end_date_m != '')&&($end_date_d != '')&&($end_date_y != '')){
$activity_begin_date = $start_date_m.'/'.$start_date_d.'/'.$start_date_y;
$activity_end_date = $end_date_m.'/'.$end_date_d.'/'.$end_date_y;
}

$activity_type = $_GET['activity_type'];
$contact_method = $_GET['contact_method'];
$state = $_GET['state'];

//for($i=0 ; $i<count($_GET['activity_type']) ; $i++) {
//		$activity_type .= $_GET['activity_type'][$i]."\r"; 
//		}


#####################################################################
### START: FIND AND DISPLAY ALL MATCHING ACTIVITIES FOR THIS USER ###
#####################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('CC_dms.fp7','sedl_service_log',$_SESSION['group_size']);
$search -> SetDBPassword($webPW,$webUN);
$search -> FMSkipRecords($skip_size);

if($service_log_ID != ''){
$search -> AddDBParam('service_log_ID','=='.$service_log_ID);
//$keyword_array = explode(" ",$keyword);
}

if($keyword != ''){
$search -> AddDBParam('c_activity_name_comments',$keyword);
}

if($wg_usde_priority_area != ''){
$search -> AddDBParam('wg_usde_priority_area',$wg_usde_priority_area);
}

if($wg_project_ID != ''){
$search -> AddDBParam('wg_project_ID','=='.$wg_project_ID);
}

if($project_funding_stream != ''){
$search -> AddDBParam('project_funding_stream',$project_funding_stream);
}

if($activity_type != ''){
$search -> AddDBParam('activity_type',$activity_type);
}

if($contact_method != ''){
$search -> AddDBParam('contact_method',$contact_method);
}

if($state != ''){
$search -> AddDBParam('activity_location_state',$state);
}

if(($activity_begin_date != '')&&($activity_end_date != '')){
$search -> AddDBParam('activity_begin_date',$activity_begin_date.'...'.$activity_end_date);
}

//if(($_SESSION['svc_log_admin_sedl'] != 'Yes')&&($_SESSION['svc_log_admin_wg'] != 'Yes')&&($_SESSION['svc_log_admin_prgms'] != 'Yes')&&($_SESSION['svc_log_admin_spvsr'] != 'Yes')){
//$search -> AddDBParam('sedl_workgroup',$_SESSION['workgroup']); // RESTRICT QUERY TO CURRENT WORKGROUP
//}

$search -> AddSortParam('activity_begin_date','descend');

$searchResult = $search -> FMFind();


//echo '<p>$service_log_ID: '.$service_log_ID;
//echo '<p>$activity_begin_date: '.$activity_begin_date;
//echo '<p>$activity_end_date: '.$activity_end_date;
//echo '<p>$activity_type: '.$activity_type;
//print_r($searchResult);
//exit;
//echo '<p>'.$searchResult['errorCode'];
//echo '<p>'.$searchResult['foundCount'];
//$recordData = current($searchResult['data']);
###################################################################
### END: FIND AND DISPLAY ALL MATCHING ACTIVITIES FOR THIS USER ###
###################################################################

###PRINT NEW INFORMATION ON SCREEN WITH CONFIRMATION MESSAGE###
$results_pages = ceil($searchResult['foundCount'] / $_SESSION['group_size']);

// CAPTURE RETURNED RECORD IDs IN AN ARRAY
$i = 0;
foreach($searchResult['data'] as $key => $searchData) { 

		$my_list[$i] = $searchData['service_log_ID'][0];
$i++;
}
$_SESSION['my_list'] = $my_list;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="en-us">
<META NAME="description" CONTENT="The Texas Comprehensive Center (TXCC), funded by the U.S. Department of Education, provides high-quality technical assistance in the state of Texas to the Texas Education Agency.">
<META NAME="keywords" lang="en-us" CONTENT="No Child left behind, NCLB, Texas Comprehensive Center, SEDL, Teaching, resources, spelling, grants">
<META NAME="author" content="Vicki Dimock">
<meta name="Copyright" content="SEDL">
<meta name="Robots" content="index,follow"> 
<title>SEDL - Staff Service Log</title> <!-- page title -->
<link rel="shortcut icon" href="http://www.sedl.org/imagesN/SEDL.ico">
<link href="http://www.sedl.org/css/sims2007.css" rel="stylesheet" type="text/css">


<style>
  span.highlighted {  
	background-color: #fffc00;  
	font-weight: bold;  
  }  

</style>

</head>

<body bgcolor="#101229">	

<!-- BEGIN: header server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/header_sedl_log_svc.html');?>

<!-- END: header server-side include -->

<!-- BEGIN: PAGE CONTENT -->

<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff" width="760">
<tr><td><div class="tiny" style="float:left;width:200px;background-color:#ffffff;padding-top:0px;padding-bottom:6px;margin:0px;border-width:0px;border-style:dotted;border-color:#0a5253"><strong>Current User</strong>: <?php echo $_SESSION['user_ID'].' | '.$_SESSION['PrimarySEDLWorkgroup'];?></div>
<div style="float:right;padding-top:0px;padding-bottom:6px"><a href="mailto:service_log@sedl.org?subject=SEDL-Staff-Service-Log-Comments">Suggestions/Comments</a> | <a href="http://www.sedl.org/staff/sims/logs/service_log_menu.php">Main Menu</a> | <a href="http://www.sedl.org/staff">Return to intranet</a></div>

		<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px">
		<tr><td><h2 class="txcc">SEDL - Staff Service Log</h2></td><td align="right" nowrap><a href="service_log.php?action=show_mine">Show my records</a> | <a href="service_log.php?action=new">Submit new activity</a></td></tr>
		<tr><td colspan="2">

			<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
	
<!--			<tr bgcolor="#B3CCF5"><td class="body">ID</td><td class="body">Resource Title</td><td class="body">Type</td><td class="body" nowrap>Submitted By</td><td class="body" nowrap align="right">Submitted On</td></tr> -->
	
			<?php if($searchResult['foundCount'] == '0'){?>
						<tr valign="top"><td class="body" align="center">
						<div class="info_plain" style="padding-top:30px;padding-bottom:30px;padding-left:10px">
						<hr class="ee" style="border-color:#c0c9da">
						No records found matching your query | <a href="service_log_menu.php">Search again</a>
						<hr class="ee" style="border-color:#c0c9da">
						</div>
						
						</td></tr>
			<?php }else{ ?>
			<tr valign="top"><td class="body">
			<div style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">

  Click on a title to view, edit, or delete an activity.


<hr class="ee" style="border-color:#c0c9da">



<div style="float:right;padding-right:35px;padding-top:5px">Results per page 
<?php

if($_SESSION['group_size'] != '10'){ ?>
<a href="service_log.php?action=search_go

<?php if($service_log_ID != ''){echo '&service_log_ID='.$service_log_ID; }?>
<?php if($keyword != ''){echo '&keyword='.$keyword;}?>
<?php if($wg_usde_priority_area != ''){echo '&wg_usde_priority_area='.$wg_usde_priority_area;}?>
<?php if($wg_project_ID != ''){echo '&wg_project_ID='.$wg_project_ID;}?>
<?php if($project_funding_stream != ''){echo '&project_funding_stream='.$project_funding_stream;}?>
<?php if($project_funding_stream != ''){echo '&project_funding_stream='.$project_funding_stream;}?>
<?php if($activity_type != ''){echo '&activity_type='.$activity_type;}?>
<?php if($contact_method != ''){echo '&contact_method='.$contact_method;}?>
<?php if($state != ''){echo '&state='.$state;}?>
<?php if(($activity_begin_date != '')&&($activity_end_date != '')){echo '&activity_begin_date='.$activity_begin_date.'...'.activity_end_date; }?>

&group_size=10">10</a> | <?php }else{echo '<strong>10</strong> | ';}



if($_SESSION['group_size'] != '25'){ ?>

<a href="service_log.php?action=search_go

<?php if($service_log_ID != ''){echo '&service_log_ID='.$service_log_ID; }?>
<?php if($keyword != ''){echo '&keyword='.$keyword;}?>
<?php if($wg_usde_priority_area != ''){echo '&wg_usde_priority_area='.$wg_usde_priority_area;}?>
<?php if($wg_project_ID != ''){echo '&wg_project_ID='.$wg_project_ID;}?>
<?php if($project_funding_stream != ''){echo '&project_funding_stream='.$project_funding_stream;}?>
<?php if($project_funding_stream != ''){echo '&project_funding_stream='.$project_funding_stream;}?>
<?php if($activity_type != ''){echo '&activity_type='.$activity_type;}?>
<?php if($contact_method != ''){echo '&contact_method='.$contact_method;}?>
<?php if($state != ''){echo '&state='.$state;}?>
<?php if(($activity_begin_date != '')&&($activity_end_date != '')){echo '&activity_begin_date='.$activity_begin_date.'...'.activity_end_date; }?>

&group_size=25">25</a> | <?php }else{echo '<strong>25</strong> | ';}


if($_SESSION['group_size'] != '50'){ ?><a href="service_log.php?action=search_go

<?php if($service_log_ID != ''){echo '&service_log_ID='.$service_log_ID; }?>
<?php if($keyword != ''){echo '&keyword='.$keyword;}?>
<?php if($wg_usde_priority_area != ''){echo '&wg_usde_priority_area='.$wg_usde_priority_area;}?>
<?php if($wg_project_ID != ''){echo '&wg_project_ID='.$wg_project_ID;}?>
<?php if($project_funding_stream != ''){echo '&project_funding_stream='.$project_funding_stream;}?>
<?php if($project_funding_stream != ''){echo '&project_funding_stream='.$project_funding_stream;}?>
<?php if($activity_type != ''){echo '&activity_type='.$activity_type;}?>
<?php if($contact_method != ''){echo '&contact_method='.$contact_method;}?>
<?php if($state != ''){echo '&state='.$state;}?>
<?php if(($activity_begin_date != '')&&($activity_end_date != '')){echo '&activity_begin_date='.$activity_begin_date.'...'.activity_end_date; }?>

&group_size=50">50</a>   <?php }else{echo '<strong>50</strong>';}


?>

</div>
<div class="info_plain">Found <strong><?php echo $searchResult['foundCount'];?> activit<?php if($searchResult['foundCount'] > 1){ ?>ies<?php }else{?>y<?php }?></strong> matching your query | <a href="service_log_menu.php">Search again</a></div>
<hr class="ee" style="border-color:#c0c9da">

Search results pages:
<?php
$num_list_start = $skip_size + 1;


for($i=1;$i <= $results_pages;$i++) { 
$start_rec = ($i-1)*$_SESSION['group_size'];

if($i*$_SESSION['group_size'] - ($_SESSION['group_size'] - 1) == $num_list_start){
echo '<strong>'.$i.'</strong>';
}else{ ?>
<a href="service_log.php?action=search_go

<?php if($service_log_ID != ''){echo '&service_log_ID='.$service_log_ID; }?>
<?php if($keyword != ''){echo '&keyword='.$keyword;}?>
<?php if($wg_usde_priority_area != ''){echo '&wg_usde_priority_area='.$wg_usde_priority_area;}?>
<?php if($wg_project_ID != ''){echo '&wg_project_ID='.$wg_project_ID;}?>
<?php if($project_funding_stream != ''){echo '&project_funding_stream='.$project_funding_stream;}?>
<?php if($project_funding_stream != ''){echo '&project_funding_stream='.$project_funding_stream;}?>
<?php if($activity_type != ''){echo '&activity_type='.$activity_type;}?>
<?php if($contact_method != ''){echo '&contact_method='.$contact_method;}?>
<?php if($state != ''){echo '&state='.$state;}?>
<?php if(($activity_begin_date != '')&&($activity_end_date != '')){echo '&activity_begin_date='.$activity_begin_date.'...'.activity_end_date; }?>

&skipsize=<?php echo $start_rec;?>&group_size=<?php echo $_SESSION['group_size'];?>"><?php echo $i;?></a>


<?php }

	if($i< $results_pages){
	echo ' | ';
	}
}
?>



				<table cellpadding="5" style="margin-top:10px;border-color:#c0c9da;border-width:1px;border-style:dotted">
				<tr valign="top" style="padding-top:5px;background-color:#c0c9da"><td class="body">Log ID</td><td class="body">Begin Date</td><td class="body" nowrap>End Date</td><td class="body" width="100%">Activity Name</td><td class="body" nowrap>Scope</td><td class="body" nowrap align="right">Submitted by</td></tr>

				<?php foreach($searchResult['data'] as $key => $searchData) { ?>

				<tr valign="top"><td class="body" style="padding-top:5px;color:#666666"><?php echo $searchData['service_log_ID'][0];?></td><td class="body" style="padding-top:5px"><?php echo $searchData['activity_begin_date'][0];?></td><td class="body" nowrap style="padding-top:5px"><?php echo $searchData['activity_end_date'][0];?></td><td class="body" style="padding-top:5px"><a href="service_log.php?action=show_1&row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>&src=my_list" title="Click here to view more details about this activity." style="text-decoration:none"><?php echo $searchData['activity_name'][0];?></a></td><td class="body" nowrap style="padding-top:5px"><?php echo $searchData['activity_location_scope'][0];?><?php if(($searchData['activity_location_scope'][0] !== 'National')&&($searchData['activity_location_scope'][0] !== 'International')){echo ' | '.$searchData['activity_location_state'][0];}?></td><td class="body" style="padding-top:5px" align="right"><?php echo $searchData['created_by'][0];?></td></tr>
				
				<?php } ?>
		
				</table>


			<?php }?>
			</div>
			</td></tr>
			</table>
		
		</td></tr></table>

</td></tr></table>

<!-- BEGIN: footer server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/footer_sedl_log_svc.html');?>

<!-- END: footer server-side include -->


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


} elseif ($action == 'search_go_admin') { 

if($_SESSION['group_size'] == $_GET['group_size']){
//$group_size = $_GET['group_size'];
}else{
//$group_size = 3;
$_SESSION['group_size'] = $_GET['group_size'];

}

if($_GET['group_size'] == ''){
$_SESSION['group_size'] = '10';
}

$skip_size = $_GET['skipsize'];
$sortby = $_GET['sortby'];
$_SESSION['sortby'] = $sortby;

$sedl_workgroup = $_GET['sedl_workgroup'];
$surrogate = $_GET['surrogate'];

## GET SEARCH CRITERIA ##
//$service_log_ID = $_GET['service_log_ID'];

//$start_date_m = $_GET['start_date_m'];
//$start_date_d = $_GET['start_date_d'];
//$start_date_y = $_GET['start_date_y'];

//$end_date_m = $_GET['end_date_m'];
//$end_date_d = $_GET['end_date_d'];
//$end_date_y = $_GET['end_date_y'];

//if(($start_date_m != '')&&($start_date_d != '')&&($start_date_y != '')&&($end_date_m != '')&&($end_date_d != '')&&($end_date_y != '')){
//$activity_begin_date = $start_date_m.'/'.$start_date_d.'/'.$start_date_y;
//$activity_end_date = $end_date_m.'/'.$end_date_d.'/'.$end_date_y;
//}

//$activity_type = $_GET['activity_type'];
//$contact_method = $_GET['contact_method'];
$created_by = $_GET['created_by'];

//for($i=0 ; $i<count($_GET['activity_type']) ; $i++) {
//		$activity_type .= $_GET['activity_type'][$i]."\r"; 
//		}


##########################################################################################
### START: FIND AND DISPLAY ALL MATCHING ACTIVITIES FOR THE SELECTED STAFF MEMBER USER ###
##########################################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('CC_dms.fp7','sedl_service_log',$_SESSION['group_size']);
$search -> SetDBPassword($webPW,$webUN);
$search -> FMSkipRecords($skip_size);

$search -> AddDBParam('created_by',$created_by); // RESTRICT QUERY TO SELECTED STAFF MEMBER
$search -> AddDBParam('sedl_workgroup',$sedl_workgroup); // RESTRICT QUERY TO SELECTED WORKGROUP

if($surrogate == '1'){
$search -> AddDBParam('c_surrogate',$surrogate); // RESTRICT QUERY TO SURROGATE ENTRIES
}

$search -> AddSortParam('activity_begin_date','descend');

$searchResult = $search -> FMFind();


//echo '<p>$service_log_ID: '.$service_log_ID;
//echo '<p>$activity_begin_date: '.$activity_begin_date;
//echo '<p>$activity_end_date: '.$activity_end_date;
//echo '<p>$activity_type: '.$activity_type;

//exit;
//echo '<p>'.$searchResult['errorCode'];
//echo '<p>'.$searchResult['foundCount'];
//$recordData = current($searchResult['data']);
###################################################################################
### END: FIND AND DISPLAY ALL MATCHING ACTIVITIES FOR THE SELECTED STAFF MEMBER ###
###################################################################################

###PRINT NEW INFORMATION ON SCREEN WITH CONFIRMATION MESSAGE###
$results_pages = ceil($searchResult['foundCount'] / $_SESSION['group_size']);

// CAPTURE RETURNED RECORD IDs IN AN ARRAY
$i = 0;
foreach($searchResult['data'] as $key => $searchData) { 

		$my_list[$i] = $searchData['service_log_ID'][0];
$i++;
}
$_SESSION['my_list'] = $my_list;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="en-us">
<META NAME="description" CONTENT="The Texas Comprehensive Center (TXCC), funded by the U.S. Department of Education, provides high-quality technical assistance in the state of Texas to the Texas Education Agency.">
<META NAME="keywords" lang="en-us" CONTENT="No Child left behind, NCLB, Texas Comprehensive Center, SEDL, Teaching, resources, spelling, grants">
<META NAME="author" content="Vicki Dimock">
<meta name="Copyright" content="SEDL">
<meta name="Robots" content="index,follow"> 
<title>SEDL - Staff Service Log</title> <!-- page title -->
<link rel="shortcut icon" href="http://www.sedl.org/imagesN/SEDL.ico">
<link href="http://www.sedl.org/css/sims2007.css" rel="stylesheet" type="text/css">


<style>
  span.highlighted {  
	background-color: #fffc00;  
	font-weight: bold;  
  }  

</style>

</head>

<body bgcolor="#101229">	

<!-- BEGIN: header server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/header_sedl_log_svc.html');?>

<!-- END: header server-side include -->

<!-- BEGIN: PAGE CONTENT -->

<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff" width="760">
<tr><td><div class="tiny" style="float:left;width:200px;background-color:#ffffff;padding-top:0px;padding-bottom:6px;margin:0px;border-width:0px;border-style:dotted;border-color:#0a5253"><strong>Current User</strong>: <?php echo $_SESSION['user_ID'].' | '.$_SESSION['PrimarySEDLWorkgroup'];?></div>
<div style="float:right;padding-top:0px;padding-bottom:6px"><a href="mailto:service_log@sedl.org?subject=SEDL-Staff-Service-Log-Comments">Suggestions/Comments</a> | <a href="http://www.sedl.org/staff/sims/logs/service_log_menu.php">Main Menu</a> | <a href="http://www.sedl.org/staff">Return to intranet</a></div>

		<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px">
		<tr><td><h2 class="txcc">SEDL - Staff Service Log</h2></td><td align="right" nowrap><a href="service_log.php?action=show_mine">Show my records</a> | <a href="service_log.php?action=new">Submit new activity</a></td></tr>
		<tr><td colspan="2">

			<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
	
<!--			<tr bgcolor="#B3CCF5"><td class="body">ID</td><td class="body">Resource Title</td><td class="body">Type</td><td class="body" nowrap>Submitted By</td><td class="body" nowrap align="right">Submitted On</td></tr> -->
	
			<?php if($searchResult['foundCount'] == '0'){?>
						<tr valign="top"><td class="body" align="center">
						<div class="info_plain" style="padding-top:30px;padding-bottom:30px;padding-left:10px">
						<hr class="ee" style="border-color:#c0c9da">
						No records found matching your query | <a href="service_log_menu.php">Search again</a>
						<hr class="ee" style="border-color:#c0c9da">
						</div>
						
						</td></tr>
			<?php }else{ ?>
			<tr valign="top"><td class="body">
			<div style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">

  Click on an activity name to view a log entry.


<hr class="ee" style="border-color:#c0c9da">



<div style="float:right;padding-right:35px;padding-top:5px">Results per page 
<?php
if($_SESSION['group_size'] != '10'){ ?><a href="service_log.php?action=search_go_admin&created_by=<?php echo $created_by;?>&sedl_workgroup=<?php echo $sedl_workgroup;?>&group_size=10">10</a> | <?php }else{echo '<strong>10</strong> | ';}
if($_SESSION['group_size'] != '25'){ ?><a href="service_log.php?action=search_go_admin&created_by=<?php echo $created_by;?>&sedl_workgroup=<?php echo $sedl_workgroup;?>&group_size=25">25</a> | <?php }else{echo '<strong>25</strong> | ';}
if($_SESSION['group_size'] != '50'){ ?><a href="service_log.php?action=search_go_admin&created_by=<?php echo $created_by;?>&sedl_workgroup=<?php echo $sedl_workgroup;?>&group_size=50">50</a>   <?php }else{echo '<strong>50</strong>';}





?>
</div>
<div class="info_plain">Found <strong><?php echo $searchResult['foundCount'];?> activit<?php if($searchResult['foundCount'] > 1){ ?>ies<?php }else{?>y<?php }?></strong> matching your query | <a href="service_log_menu.php">Search again</a></div>
<hr class="ee" style="border-color:#c0c9da">

Search results pages:
<?php
$num_list_start = $skip_size + 1;


for($i=1;$i <= $results_pages;$i++) { 
$start_rec = ($i-1)*$_SESSION['group_size'];

if($i*$_SESSION['group_size'] - ($_SESSION['group_size'] - 1) == $num_list_start){
echo '<strong>'.$i.'</strong>';
}else{ ?>
<a href="service_log.php?action=search_go_admin&created_by=<?php echo $created_by;?>&sedl_workgroup=<?php echo $sedl_workgroup;?>&skipsize=<?php echo $start_rec;?>&group_size=<?php echo $_SESSION['group_size'];?>"><?php echo $i;?></a>
<?php }

	if($i< $results_pages){
	echo ' | ';
	}
}
?>



				<table cellpadding="5" style="margin-top:10px;border-color:#c0c9da;border-width:1px;border-style:dotted">
				<tr valign="top" style="padding-top:5px;background-color:#c0c9da"><td class="body">Log ID</td><td class="body">Begin Date</td><td class="body" nowrap>End Date</td><td class="body" width="100%">Activity Name</td><td class="body" nowrap>Scope</td><td class="body" nowrap align="right">Submitted by</td></tr>

				<?php foreach($searchResult['data'] as $key => $searchData) { ?>

				<tr valign="top"><td class="body" style="padding-top:5px;color:#666666"><?php echo $searchData['service_log_ID'][0];?></td><td class="body" style="padding-top:5px"><?php echo $searchData['activity_begin_date'][0];?></td><td class="body" nowrap style="padding-top:5px"><?php echo $searchData['activity_end_date'][0];?></td><td class="body" style="padding-top:5px"><a href="service_log.php?action=show_1&row_ID=<?php echo $searchData['c_cwp_row_ID'][0];?>&src=my_list" title="Click here to view more details about this activity." style="text-decoration:none"><?php echo $searchData['activity_name'][0];?></a></td><td class="body" nowrap style="padding-top:5px"><?php echo $searchData['activity_location_scope'][0].' | '.$searchData['activity_location_state'][0];?></td><td class="body" nowrap style="padding-top:5px" align="right"><?php echo $searchData['created_by'][0];?></td></tr>
				
				<?php } ?>
		
				</table>


			<?php }?>
			</div>
			</td></tr>
			</table>
		
		</td></tr></table>

</td></tr></table>

<!-- BEGIN: footer server-side include - include_once();-->

<?php include_once('http://txcc.sedl.org/orc/includes/footer_sedl_log_svc.html');?>

<!-- END: footer server-side include -->


</body>

</html>


<?php } else { ?>

Error

<?php echo '<p>'.$action;?>
<?php echo '<p>'.$mod;?>

<?php } ?>


<?php//  } ?>
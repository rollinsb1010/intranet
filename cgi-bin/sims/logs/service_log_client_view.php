<?php
session_start();

###CHECK TO SEE IF THE LOGIN SESSION IS VALID###

//if(!isset($_SESSION['contact_ID'])) {
//include_once('cc_network_login.php');

//}else{

include_once('../FX/FX.php');
include_once('../FX/server_data.php');

$action = $_REQUEST['action'];
$contact_ID = $_REQUEST['contact_ID'];

if ($action == 'search_go') { 

		#########################################
		### START: EXECUTE SEDL CLIENTS QUERY ###
		#########################################
		
		$search = new FX($serverIP,$webCompanionPort);
		$search -> SetDBData('CC_dms.fp7','sedl_client_cwp_subset','all');
		$search -> SetDBPassword($webPW,$webUN);
		
		$search -> AddDBParam('contact_ID',$contact_ID);
		
		$searchResult = $search -> FMFind();
		
		//echo $searchResult['errorCode'];
		//echo $searchResult['foundCount'];
		$recordData = current($searchResult['data']);
		
		#######################################
		### END: EXECUTE SEDL CLIENTS QUERY ###
		#######################################
		
		//$results_pages = ceil($searchResult['foundCount'] / $_SESSION['group_size']);
		
		
		
		// CAPTURE RETURNED RECORD IDs IN AN ARRAY
		//$i = 0;
		//foreach($searchResult['data'] as $key => $searchData) { 
		
		//		$my_list[$i] = $searchData['resource_ID'][0];
		//$i++;
		//}
		//$_SESSION['my_list'] = $my_list;

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
		<title>SEDL - Client Database</title> <!-- page title -->
		<link rel="shortcut icon" href="http://www.sedl.org/imagesN/SEDL.ico">
		<link href="http://www.sedl.org/css/sims2007.css" rel="stylesheet" type="text/css">
		
		
				<script type="text/javascript"><!--
					  function input(formName, obj, val){
						 opener.document.forms[formName].elements[obj].value = val;
						 self.close();
					  }
			  
		</script>
		
		</head>
		
		<body bgcolor="#101229">	
		
		
		<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff">
		<tr><td>
				<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px">
				<tr><td><h2 class="txcc">SEDL Clients Database</h2></td></tr>
				<tr><td>
		
					<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
			
			
					<?php if($searchResult['foundCount'] == '0'){?>
					<tr valign="top"><td class="body" colspan="4" style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">
		
		  			<div class="alert_small"><strong>No records found matching your query.</strong><br>&nbsp;<br>
		  			This client doesn't exist in the database. This client may have been deleted after your ILS (Service Log) entry was created. Contact <a href="mailto:service_log@sedl.org">service_log@sedl.org</a> for assistance.</div>
					<hr class="ee">
					<a href="#" onclick="self.close();">Close this window</a>
					</td></tr></table>		
					
					<?php }else{ ?>

		
		
		
		
		
		
		
		
		<?php $counter=1; ?>
		
		
		
		
				<tr bgcolor="#B3CCF5" style="background-color:#ebebeb;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:solid">
				<td colspan="2" style="background-color:#ebebeb;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-right-width:0px;border-left-width:1px;border-top-width:1px;border-bottom-width:1px;border-style:solid">CLIENT</td>
				<td nowrap style="background-color:#ebebeb;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border: 1px #c0c9da solid;">SEDL UNIT</td>
				</tr>

		
					<?php foreach($searchResult['data'] as $key => $searchData) { ?>
					
							<tr valign="top" style="background-color:#ffffff;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:solid">
							<td style="background-color:#4e7f60;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:4px;padding-left:5px;border-color:#c0c9da;border-right-width:0px;border-left-width:1px;border-top-width:0px;border-bottom-width:1px;border-style:solid">&nbsp;</td>
							<td width="100%" style="background-color:#ffffff;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:5px;border-color:#c0c9da;border-right-width:0px;border-left-width:0px;border-top-width:0px;border-bottom-width:1px;border-style:solid">
							<div style="float:right"><span style="color:#999999"><?php echo $searchData['contact_ID'][0];?></span></div>
							<?php echo $searchData['c_preferred_addr_label_last_first_html'][0];?><br><a href="mailto:<?php echo $searchData['c_prim_email'][0];?>"><?php echo $searchData['c_prim_email'][0];?></a></td>
							<td style="background-color:#ffffff;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-right-width:1px;border-left-width:1px;border-top-width:0px;border-bottom-width:1px;border-style:solid"><?php echo $searchData['c_sedl_unit_affiliation_csv'][0];?></td>
							</tr>
									
					<?php $counter++;
					} ?>		
					
				
				</table>
		</div><hr class="ee">
		<a href="#" onclick="self.close();">Close this window</a>
		</td></tr></table>
		
		<?php } ?>
		</body>
		
		</html>



<?php } else { ?>

	Error
	
	<?php echo '<p>'.$action;?>
	<?php echo '<p>'.$mod;?>
	
<?php } ?>


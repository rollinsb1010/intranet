<?php
session_start();

###CHECK TO SEE IF THE LOGIN SESSION IS VALID###

//if(!isset($_SESSION['contact_ID'])) {
//include_once('cc_network_login.php');

//}else{

include_once('../FX/FX.php');
include_once('../FX/server_data.php');

$action = $_REQUEST['action'];
$keyword = $_GET['keyword'];

if ($action == 'search_go') { 


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
		
		#########################################
		### START: EXECUTE SEDL CLIENTS QUERY ###
		#########################################
		
		$search = new FX($serverIP,$webCompanionPort);
		$search -> SetDBData('CC_dms.fp7','sedl_client_cwp_subset','all');
		$search -> SetDBPassword($webPW,$webUN);
		$search -> FMSkipRecords($skip_size);
		
		$search -> AddDBParam('c_global_search_target',$keyword);
		
		$search -> AddSortParam('c_full_name_last_first','ascend');
		
		
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
		<title>SEDL - Staff Service Log</title> <!-- page title -->
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
				<tr><td><h2 class="txcc">Search SEDL Clients Database</h2></td></tr>
				<tr><td>
		
					<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
			
			
					<?php if($searchResult['foundCount'] == '0'){?>
					<tr valign="top"><td class="body" colspan="4" style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">
		
		  			<div class="alert_small"><strong>No records found matching your query.</strong><br>&nbsp;<br>
		  			If you searched by first and last name, you might search again by last name only or if you're sure this client doesn't exist in the database, use the link below to create a new client record in the SEDL clients database.<br>&nbsp;<br>  <a title="Click here to create a new client record to link to the current activity report entry." href="service_log_client_search.php?action=new_client">Create a new client record</a></div>
					<hr class="ee">
					<a href="#" onclick="self.close();">Close this window</a> | <a href="service_log_client_search.php?action=search">Search again</a>
					</td></tr></table>		
					
					<?php }else{ ?>

					<tr valign="top"><td class="body" colspan="4" style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">
		
		  <div class="alert_small">If the primary client for your service log activity appears in the search results list below, click "Select client" to link the client to the service log entry form. If the client does not exist in the database, <a title="Click here to create a new client record to link to the current activity report entry." href="service_log_client_search.php?action=new_client">create a new client record</a>.</div>
		
		
		<hr class="ee" style="border-color:#c0c9da">
		
		
		
		<div class="info_plain">Found <strong><?php echo $searchResult['foundCount'];?> record<?php if($searchResult['foundCount'] > 1){?>s<?php }?>
		<div style="float:right;padding-right:10px"><a href="service_log_client_search.php?action=search">Search again</a></div>
		</div>
		<hr class="ee" style="border-color:#c0c9da">
		
		
		<?php $counter=1; ?>
		
		
		
		
				<tr bgcolor="#B3CCF5" style="background-color:#ebebeb;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:solid">
				<td colspan="2" style="background-color:#ebebeb;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-right-width:0px;border-left-width:1px;border-top-width:1px;border-bottom-width:1px;border-style:solid">CLIENT</td>
				<td nowrap style="background-color:#ebebeb;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-right-width:0px;border-left-width:1px;border-top-width:1px;border-bottom-width:1px;border-style:solid">SEDL UNIT</td>
				<td nowrap style="background-color:#ebebeb;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-right-width:1px;border-left-width:1px;border-top-width:1px;border-bottom-width:1px;border-style:solid">SERVICE LOG</td></tr>

		
					<?php foreach($searchResult['data'] as $key => $searchData) { ?>
					
							<tr valign="top" style="background-color:#ffffff;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:solid">
							<td style="background-color:#ffffff;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:4px;padding-left:5px;border-color:#c0c9da;border-right-width:0px;border-left-width:1px;border-top-width:0px;border-bottom-width:1px;border-style:solid"><?php echo $counter;?>.</td>
							<td width="100%" style="background-color:#ffffff;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:0px;border-color:#c0c9da;border-right-width:0px;border-left-width:0px;border-top-width:0px;border-bottom-width:1px;border-style:solid">
							<div style="float:right"><span style="color:#999999"><?php echo $searchData['contact_ID'][0];?></span></div>
							<?php echo $searchData['c_preferred_addr_label_last_first_html'][0];?></td>
							<td style="background-color:#ffffff;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-right-width:0px;border-left-width:1px;border-top-width:0px;border-bottom-width:1px;border-style:solid"><?php echo $searchData['c_sedl_unit_affiliation_csv'][0];?></td>
							<td style="background-color:#ffffff;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-right-width:1px;border-left-width:1px;border-top-width:0px;border-bottom-width:1px;border-style:solid"><a title="Click here to add this client ID to the current activity report entry." href="#" onclick="input('form2', 'primary_sedl_client_ID', '<?php echo $searchData['contact_ID'][0];?>'); return false">Select client</a></td></tr>
					
									
					<?php $counter++;
					} ?>		
					
				
				</td></tr></table>
		</div><hr class="ee">
		<a href="#" onclick="self.close();">Close this window</a> | <a href="service_log_client_search.php?action=search">Search again</a>
		</td></tr></table>
		
		<?php } ?>
		</body>
		
		</html>

<?php } elseif ($action == 'search') { 


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
		
		<script language="JavaScript">
		<!--
		
				function win1() {
					window.open("win1.php","Window1","menubar=no,width=460,height=700,toolbar=no, scrollbars=1");
				}
		
		
		//-->
		</script>
		
		
				<style>
				  span.highlighted {  
					background-color: #fffc00;  
					font-weight: bold;  
				  }  
				
				</style>
		
		</head>
		
		<body bgcolor="#101229" OnLoad="document.form2.keyword.focus();">	
		
		
		<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff">
		<tr><td>
				<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px">
				<tr><td><h2 class="txcc">Search SEDL Clients Database</h2></td></tr>
				<tr><td>
		
					<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
					<form method="get" id="form2" name="form2">
					<input type="hidden" name="action" value="search_go">
		
					<tr><td class="body" width="100%" style="background-color:#ffffff">
					<div style="padding-top:8px;padding-left:8px;padding-right:8px;padding-bottom:0px;border-width:1px;border-style:dotted;border-color:#0a5253;background-color:#ebebeb">Enter the client's first name, last name, and/or organization name.<p>
						<input type="text" name="keyword" size="35"> &nbsp;<input type="button" name="cancel" value="Cancel" class="body" onclick="self.close();"><input type="submit" name="submit" value="Search" class="body">
					</div>
					</td></tr>
		
					
					
		
		
					</form>
					</table>
				
				</td></tr>
				</table>
		
		</td></tr>
		</table>
		
		</body>
		
		</html>


<?php } elseif ($action == 'new_client') { 


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
		
				<script language="JavaScript">
				<!--
				function checkFields() { 
				
					// First Name
						if (document.form2.first_name.value =="") {
							alert("Blank field: First Name.");
							document.form2.first_name.focus();
							return false;	}
				
					// Last Name
						if (document.form2.last_name.value =="") {
							alert("Blank field: Last Name.");
							document.form2.last_name.focus();
							return false;	}
				
					// Org
						if (document.form2.organization.value =="") {
							alert("Blank field: Organization.");
							document.form2.organization.focus();
							return false;	}
				
				}	
				// -->
				</script>
		
		
		
				<style>
				  span.highlighted {  
					background-color: #fffc00;  
					font-weight: bold;  
				  }  
				
				</style>
		
		</head>
		
		<body bgcolor="#101229">	
		
		
		<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="#ffffff">
		<tr><td>
				<table cellpadding=0 cellspacing=0 width="100%" class="dotted-box-orc" style="padding-top:0px">
				<tr><td colspan="2"><h2 class="txcc">Create New SEDL Client</h2></td></tr>
				<tr><td colspan="2">
		
					<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
					<form method="post" id="form2" name="form2" onsubmit="return checkFields()">
					<input type="hidden" name="action" value="new_submit">
		
					<tr><td bgcolor="#ebebeb" align="right" valign="bottom" nowrap style="padding-bottom:10px">
					Name</td><td class="body" width="100%" style="background-color:#ffffff">
					
							<table>
							<tr><td style="padding-left:0px;padding-bottom:0px"><span class="tiny">PREFIX</span></td><td style="padding-bottom:0px"><span class="tiny">*FIRST</span></td><td style="padding-bottom:0px"><span class="tiny">*LAST</span></td><td style="padding-bottom:0px"><span class="tiny">SUFFIX</span></td></tr>
							<tr><td style="padding-left:0px"><input type="text" name="prefix" size="5"></td><td><input type="text" name="first_name" size="15"></td><td><input type="text" name="last_name" size="15"></td><td><input type="text" name="suffix" size="5"></td></tr>
							</table>
					
					</td></tr>
		
					<tr><td bgcolor="#ebebeb" align="right" valign="bottom" nowrap style="padding-bottom:10px">
					Title</td><td class="body" width="100%" style="background-color:#ffffff;padding-left:6px">
					
					<input type="text" name="title" size="50">
					
					</td></tr>
		
					<tr><td bgcolor="#ebebeb" align="right" valign="bottom" nowrap style="padding-bottom:10px">
					*Org.</td><td class="body" width="100%" style="background-color:#ffffff;padding-left:6px">
					
					<input type="text" name="organization" size="50">
					
					</td></tr>
		
					<tr><td bgcolor="#ebebeb" align="right" valign="bottom" nowrap style="padding-bottom:10px">
					Addr 1</td><td class="body" width="100%" style="background-color:#ffffff;padding-left:6px">
					
					<input type="text" name="addr1" size="50">
					
					</td></tr>
		
					<tr><td bgcolor="#ebebeb" align="right" valign="bottom" nowrap style="padding-bottom:10px">
					Addr 2</td><td class="body" width="100%" style="background-color:#ffffff;padding-left:6px">
					
					<input type="text" name="addr2" size="50">
					
					</td></tr>
		
					<tr><td bgcolor="#ebebeb" align="right" valign="bottom" nowrap style="padding-bottom:10px">
					Location</td><td class="body" width="100%" style="background-color:#ffffff">
					
							<table>
							<tr><td style="padding-left:0px;padding-bottom:0px"><span class="tiny">CITY</span></td><td style="padding-bottom:0px"><span class="tiny">STATE</span></td><td style="padding-bottom:0px"><span class="tiny">ZIP</span></td></tr>
							<tr><td style="padding-left:0px"><input type="text" name="city" size="25"></td><td><input type="text" name="state" size="5"></td><td><input type="text" name="zip" size="10"></td></tr>
							</table>
					
					</td></tr>
		
					<tr><td bgcolor="#ebebeb" align="right" valign="bottom" nowrap style="padding-bottom:10px">
					E-mail</td><td class="body" width="100%" style="background-color:#ffffff;padding-left:6px">
					
					<input type="text" name="email" size="50">
					
					</td></tr>
		
					<tr><td bgcolor="#ebebeb" align="right" valign="bottom" nowrap style="padding-bottom:10px">
					Phone</td><td class="body" width="100%" style="background-color:#ffffff;padding-left:6px">
					
					<input type="text" name="phone" size="50">
					
					</td></tr>
		
					<tr><td>&nbsp;</td><td align="right" width="100%" style="background-color:#ffffff"><input type="button" name="cancel" value="Cancel" class="body" onclick="self.close();"><input type="submit" name="submit" value="Create Client" class="body"></td></tr>
					
					<tr><td>&nbsp;</td><td width="100%" style="background-color:#ebebeb"><span class="tiny">*Required fields.</span></td></tr>
					
		
		
					</form>
					</table>
				
				</td></tr>
				</table>
		
		</td></tr>
		</table>
		
		</body>
		
		</html>


<?php } elseif ($action == 'new_submit') { 

		###COLLECT THE FORM-SUBMITTED VALUES INTO VARIABLES###
		
		$prefix = $_POST['prefix'];
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$suffix = $_POST['suffix'];
		$title = $_POST['title'];
		$organization = $_POST['organization'];
		$addr1 = $_POST['addr1'];
		$addr2 = $_POST['addr2'];
		$city = $_POST['city'];
		$state = $_POST['state'];
		$zip = $_POST['zip'];
		$email = $_POST['email'];
		$phone = $_POST['phone'];
		
		$created_by = $_SESSION['user_ID'];
		$workgroup = $_SESSION['PrimarySEDLWorkgroup'];
		
		##############################################
		### START: ADD NEW SEDL CLIENT TO DATABASE ###
		##############################################
		$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
		$newrecord -> SetDBData('CC_dms.fp7','sedl_client_detail'); //set dbase information
		$newrecord -> SetDBPassword($webPW,$webUN); //set password information

		$newrecord -> AddDBParam('prefix',$prefix);
		$newrecord -> AddDBParam('first_name',$first_name);
		$newrecord -> AddDBParam('last_name',$last_name);
		$newrecord -> AddDBParam('suffix',$suffix);
		$newrecord -> AddDBParam('title',$title);
		$newrecord -> AddDBParam('organization',$organization);
		
		$newrecord -> AddDBParam('created_by',$created_by);
		$newrecord -> AddDBParam('client_primary_workgroup',$workgroup);
		$newrecord -> AddDBParam('sedl_unit_affiliation',$workgroup);
		$newrecord -> AddDBParam('notes','SEDL Staff Service Log');

		
		###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
		$newrecordResult = $newrecord -> FMNew();
		$newrecordData = current($newrecordResult['data']);
		
		$new_record_ID = $newrecordData['contact_ID'][0];
		$new_record_row_ID = $newrecordData['c_cwp_row_ID'][0];
		//echo '$new_record_ID: '.$newrecordData['service_log_ID'][0];
		//echo '$errorCode: '.$newrecordResult['errorCode'];
		//exit;
		############################################
		### END: ADD NEW SEDL CLIENT TO DATABASE ###
		############################################
		
	if($newrecordResult['errorCode'] == '0'){ // IF SEDL CLIENT RECORD WAS SUCCEFULLY ADDED TO THE DATABASE
	$_SESSION['new_record_submitted'] = '1';

		####################################################################
		### START: ADD NEW SEDL CLIENT ADDRESS TO CLIENT ADDRESSES TABLE ###
		####################################################################
		if($addr1 !== ''){
		$newrecord2 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
		$newrecord2 -> SetDBData('CC_dms.fp7','cc_contacts_addresses'); //set dbase information
		$newrecord2 -> SetDBPassword($webPW,$webUN); //set password information

		$newrecord2 -> AddDBParam('contact_ID',$new_record_ID);
		$newrecord2 -> AddDBParam('addr1',$addr1);
		$newrecord2 -> AddDBParam('addr2',$addr2);
		$newrecord2 -> AddDBParam('city',$city);
		$newrecord2 -> AddDBParam('state',$state);
		$newrecord2 -> AddDBParam('zip',$zip);
		
		$newrecord2 -> AddDBParam('created_by',$created_by);
		$newrecord2 -> AddDBParam('workgroup_key',$workgroup);
		$newrecord2 -> AddDBParam('notes','SEDL Staff Service Log');
		$newrecord2 -> AddDBParam('addr_type','Primary');
		
		###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
		$newrecordResult2 = $newrecord2 -> FMNew();
		$newrecordData2 = current($newrecordResult2['data']);
		$new_record_ID2 = $newrecordData2['address_ID'][0];
		}
		##################################################################
		### END: ADD NEW SEDL CLIENT ADDRESS TO CLIENT ADDRESSES TABLE ###
		##################################################################

		#################################################################
		### START: ADD NEW SEDL CLIENT E-MAIL TO CLIENT E-MAILS TABLE ###
		#################################################################
		if($email !== ''){
		$newrecord3 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
		$newrecord3 -> SetDBData('CC_dms.fp7','cc_contacts_emails'); //set dbase information
		$newrecord3 -> SetDBPassword($webPW,$webUN); //set password information

		$newrecord3 -> AddDBParam('contact_ID',$new_record_ID);
		$newrecord3 -> AddDBParam('email_address',$email);
		//$newrecord3 -> AddDBParam('phone',$phone);
		
		$newrecord3 -> AddDBParam('created_by',$created_by);
		$newrecord3 -> AddDBParam('workgroup_key',$workgroup);
		$newrecord3 -> AddDBParam('notes','SEDL Staff Service Log');
		$newrecord3 -> AddDBParam('email_type','Primary');
		
		###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
		$newrecordResult3 = $newrecord3 -> FMNew();
		$newrecordData3 = current($newrecordResult3['data']);
		$new_record_ID3 = $newrecordData3['email_ID'][0];
		}
		###############################################################
		### END: ADD NEW SEDL CLIENT E-MAIL TO CLIENT E-MAILS TABLE ###
		###############################################################

		###############################################################
		### START: ADD NEW SEDL CLIENT PHONE TO CLIENT PHONES TABLE ###
		###############################################################
		if($phone !== ''){
		$newrecord4 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
		$newrecord4 -> SetDBData('CC_dms.fp7','cc_contacts_phones'); //set dbase information
		$newrecord4 -> SetDBPassword($webPW,$webUN); //set password information

		$newrecord4 -> AddDBParam('contact_ID',$new_record_ID);
		$newrecord4 -> AddDBParam('phone_number',$phone);
		$newrecord4 -> AddDBParam('phone_type','Main');
		
		$newrecord4 -> AddDBParam('created_by',$created_by);
		$newrecord4 -> AddDBParam('workgroup_key',$workgroup);
		$newrecord4 -> AddDBParam('notes','SEDL Staff Service Log');
		
		###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
		$newrecordResult4 = $newrecord4 -> FMNew();
		$newrecordData4 = current($newrecordResult4['data']);
		$new_record_ID4 = $newrecordData4['phone_ID'][0];
		}
		###############################################################
		### END: ADD NEW SEDL CLIENT PHONE TO CLIENT PHONES TABLE ###
		###############################################################

		####################################################################################
		### START: UPDATE NEW SEDL CLIENT RECORD WITH PRIMARY ADDR, EMAIL, AND PHONE IDs ###
		####################################################################################
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('CC_dms.fp7','sedl_client_detail');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$new_record_row_ID);
		$update -> AddDBParam('client_prim_address_ID',$new_record_ID2);
		$update -> AddDBParam('client_prim_email_ID',$new_record_ID3);
		$update -> AddDBParam('client_prim_phone_ID',$new_record_ID4);
		
		$updateResult = $update -> FMEdit();

		$updateData = current($updateResult['data']);
		$new_client_data = $updateData['c_preferred_addr_label_last_first_html'][0];

		##################################################################################
		### END: UPDATE NEW SEDL CLIENT RECORD WITH PRIMARY ADDR, EMAIL, AND PHONE IDs ###
		##################################################################################


	}
		
		
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
				<tr><td><h2 class="txcc">Create New SEDL Client</h2></td></tr>
				<tr><td>
		
					<table width="100%" bgcolor="ffffff" cellspacing=0 cellpadding=4 border=0 bordercolor="cccccc" valign="top">
			
			
					<tr valign="top"><td class="body" colspan="2" style="background-color:#ffffff;margin-top:0px;padding-top:15px;padding-bottom:15px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:dotted">
		
		
		
		
		
		
		<p class="alert_small">New record successfully created.</p>
		
		
		
		
		
				<tr bgcolor="#B3CCF5"><td class="body" style="background-color:#ebebeb;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:solid"><strong>New Client Details</strong></td></tr>
		
		
				<tr valign="top">
				<td class="body" style="background-color:#ffffff;margin-top:0px;padding-top:5px;padding-bottom:5px;padding-right:15px;padding-left:15px;border-color:#c0c9da;border-width:1px;border-style:solid">
				
				<?php echo $new_client_data;?>
				
				<p>
				Client ID: <?php echo $newrecordData['contact_ID'][0];?><p>
				<a title="Click here to add this client ID to the current activity report entry." href="#" onclick="input('form2', 'primary_sedl_client_ID', '<?php echo $newrecordData['contact_ID'][0];?>'); return false">Add client ID to activity record</a>
				
				</td></tr>
		
						
					
				</td></tr></table>
		<hr class="ee">
		<a href="#" onclick="self.close();">Close this window</a>
		</td></tr></table>
		
		</body>
		
		</html>


<?php } else { ?>

	Error
	
	<?php echo '<p>'.$action;?>
	<?php echo '<p>'.$mod;?>
	
<?php } ?>


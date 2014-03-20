<?php
session_start();

###CHECK TO SEE IF THE LOGIN SESSION IS VALID###

//if(!isset($_SESSION['contact_ID'])) {
//include_once('ccdms_tadds_login.php');

//}else{

include_once('../FX/FX.php');
include_once('../FX/server_data.php');

$action = $_GET['action'];
$project_name = $_GET['project_name'];
$project_wg = $_GET['project_wg'];

if($action == 'submit_project'){

####################################
## START: CREATE NEW PROJECT RECORD
####################################

$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('CC_dms.fp7','cc_projects'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information

$newrecord -> AddDBParam('project_name',$project_name);
$newrecord -> AddDBParam('cc',$project_wg);
$newrecord -> AddDBParam('category','staff');
$newrecord -> AddDBParam('created_by',$_SESSION['user_ID']);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

####################################
## END: CREATE NEW PROJECT RECORD
####################################

if($newrecordResult['errorCode'] == '0'){
$_SESSION['new_cc_project_created'] = '1';
}else{
$_SESSION['new_cc_project_created'] = '2';
}
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
		

<body bgcolor="#101229" onLoad="resizeTo(500,500)">	


<!-- BEGIN: PAGE CONTENT -->

<form id="form2" name="form2"  onsubmit="return checkFields()">
<input type="hidden" name="action" value="submit_project">
<table style="border:1px dotted #0a5253; padding:10px; background-color:#ffffff">



		<tr><td class="body" nowrap colspan="2" bgcolor="#ffffff">

		<?php if($_SESSION['new_cc_project_created'] = '1'){ ?>
		<p class="alert_small">Project created. Close this window, then refresh your browser to make this <br>
		new project available from the Project Name drop-down list.</p>	
		
		<?php $_SESSION['new_cc_project_created'] = '';
		}elseif($_SESSION['new_cc_project_created'] = '2'){ ?>
		<p class="alert_small">There was a problem creating your project. <br>
		Please contact <a href="mailto:eric.waters@sedl.org">support</a> with the following error code: <?php echo $newrecordResult['errorCode'];?></p>		
		
		<?php $_SESSION['new_cc_project_created'] = ''; 
		}?>
		<p><a href="#" onclick="self.close();">Close this window</a></p>
		</td></tr>
		
</table>




</form>



</body>

</html>

<?php 
exit;
}





################################
## START: GET FMP VALUE-LISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('CC_dms.fp7','cc_projects');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
##############################
## END: GET FMP VALUE-LISTS ##
##############################



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
		
		

<script language="JavaScript">
<!--
function checkFields() { 

	// Project Name
		if (document.form2.project_name.value =="") {
			alert("Please enter a project name.");
			document.form2.project_name.focus();
			return false;	}

	// Project Name (length)
		var uInput = document.form2.project_name.value;
		if (uInput.length > 35) {
			alert("Project name is too long. Maximum length is 35 characters (including spaces).");
			document.form2.project_name.focus();
			return false;	}

	// Project Workgroup
		if (document.form2.project_wg.value =="") {
			alert("Please select a workgroup.");
			document.form2.project_wg.focus();
			return false;	}



}	
// -->
</script>




</head>

<body bgcolor="#101229" onLoad="resizeTo(500,500)">	


<!-- BEGIN: PAGE CONTENT -->

<form id="form2" name="form2"  onsubmit="return checkFields()">
<input type="hidden" name="action" value="submit_project">
<table style="border:1px dotted #0a5253; padding:10px; background-color:#ffffff">



		<tr><td class="body" nowrap colspan="2"><strong>Enter a new workgroup project</strong> (name max length: 35) <p></td></tr>
		
		<tr><td class="body" align="right" nowrap>Project Name:</td><td class="body"><input type="text" name="project_name" size="50" maxlength="35" class="body"></td></tr>
		
		
		<tr><td class="body" align="right" valign="top" nowrap>Workgroup:</td><td class="body">
		
						<select name="project_wg" class="body">
						<option value="">
						
						<option value="secc">SECC</option>
						<option value="txcc">TXCC</option>

						</select>		

		</td></tr>

		
		
		<tr><td class="body">&nbsp;</td><td class="body" align="left"><input type="submit" name="submit" value="Submit" class="body"></td></tr>
</table>




</form>



</body>

</html>


<?php // }
//echo $_SESSION['schools_attending'];
 ?>



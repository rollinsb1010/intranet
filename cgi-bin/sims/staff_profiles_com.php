<?php
session_start();

include_once('sims_checksession.php');

#############################################################################
# Copyright 2008 by SEDL
#
# Written by Eric Waters 02/25/2008
#############################################################################

#################################
## START: LOAD FX.PHP INCLUDES ##
#################################
include_once('FX/FX.php');
include_once('FX/server_data.php');
###############################
## END: LOAD FX.PHP INCLUDES ##
###############################

################################
## START: GRAB FORM VARIABLES ##
################################
$action = $_REQUEST['action'];
//exit;
##############################
## END: GRAB FORM VARIABLES ##
##############################

if ($action == 'show_all') {

$confirm_update = $_REQUEST['confirm_update'];
$query = $_REQUEST['query'];
$sortby = $_GET['sortby'];

#######################################
## START: GRAB CURRENT STAFF RECORDS ##
#######################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff_table','all');
$search -> SetDBPassword($webPW,$webUN);

if($query == 'former_staff'){
	$search -> AddDBParam('current_employee_status','Former Employee');
} else {
	$search -> AddDBParam('current_employee_status','SEDL Employee');
}

if ($sortby == 'end_date') {
	$search -> AddSortParam('empl_end_date','descend');
} elseif ($sortby == 'end_date2') {
	$search -> AddSortParam('empl_end_date','ascend');
} elseif ($sortby == 'start_date') {
	$search -> AddSortParam('empl_start_date','ascend');
} elseif ($sortby == 'start_date2') {
	$search -> AddSortParam('empl_start_date','descend');
} elseif ($sortby == 'last_name2') {
	$search -> AddSortParam('c_full_name_last_first','descend');
} else {
	$search -> AddSortParam('c_full_name_last_first','ascend');
	$sortby = "last_name";
}

$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//$recordData = current($searchResult['data']);
#####################################
## END: GRAB CURRENT STAFF RECORDS ##
#####################################


###################################
## START: DISPLAY ALL STAFF LIST ##
###################################
 
 ?>

<html>
<head>
<title>SIMS - Staff Profiles (Comm Admin): List of All SEDL Staff</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(860,1000)">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Staff Profiles - Communications Admin</h1><hr /></td></tr>
			
			<?php if($confirm_update == '1'){ ?>

			<tr><td colspan="2"><p class="alert_small">Record successfully updated.</p></td></tr>
			
			<?php $confirm_update = '0';
			} ?>

			
			<tr bgcolor="#e2eaa4"><td class="body"><strong>SEDL Staff Profiles</strong> | <?php echo $searchResult['foundCount'];?> records found. | 
			<?php
			if ($query == 'former_staff') {
				echo "<a href=\"staff_profiles_com.php?action=show_all\">Show current staff</a>";
			} else {
				echo "<a href=\"staff_profiles_com.php?action=show_all&query=former_staff\">Show former staff</a>";
			}
			?>
			</td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILES-->


							<table cellspacing="0" cellpadding="4" width="100%" class="sims">
							

							<tr bgcolor="#e2eaa4"><td class="body">ID</td>
								    <?php
								    if($query == 'former_staff') {
								    	if ($sortby == 'last_name') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;query=former_staff&amp;sortby=last_name2\"><img src=\"/images/up.gif\" alt=\"sorted ascending\" style=\"float:right;\" border=\"0\">Name</a></td>";
										} else if ($sortby == 'last_name2') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;query=former_staff&amp;sortby=last_name\"><img src=\"/images/down.gif\" alt=\"sorted descending\" style=\"float:right;\" border=\"0\">Name</a></td>";
										} else {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;query=former_staff&amp;sortby=last_name\">Name</a></td>";
										}
								    } else {
								    	if ($sortby == 'last_name') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;sortby=last_name2\"><img src=\"/images/up.gif\" alt=\"sorted ascending\" style=\"float:right;\" border=\"0\">Name</a></td>";
										} else if ($sortby == 'last_name2') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;sortby=last_name\"><img src=\"/images/down.gif\" alt=\"sorted descending\" style=\"float:right;\" border=\"0\">Name</a></td>";
										} else {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;sortby=last_name\">Name</a></td>";
										}
								    }
								    ?>

									
									<td class="body">Unit</td><td class="body">Title</td><td class="body">FTE</td>
								    <?php
								    if($query == 'former_staff') {
								    	if ($sortby == 'start_date') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;query=former_staff&amp;sortby=start_date2\"><img src=\"/images/up.gif\" alt=\"sorted ascending\" style=\"float:right;\" border=\"0\">Start Date</a></td>";
										} else if ($sortby == 'start_date2') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;query=former_staff&amp;sortby=start_date\"><img src=\"/images/down.gif\" alt=\"sorted descending\" style=\"float:right;\" border=\"0\">Start Date</a></td>";
										} else {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;query=former_staff&amp;sortby=start_date\">Start Date</a></td>";
										}
								    } else {
								    	if ($sortby == 'start_date') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;sortby=start_date2\"><img src=\"/images/up.gif\" alt=\"sorted ascending\" style=\"float:right;\" border=\"0\">Start Date</a></td>";
										} else if ($sortby == 'start_date2') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;sortby=start_date\"><img src=\"/images/down.gif\" alt=\"sorted descending\" style=\"float:right;\" border=\"0\">Start Date</a></td>";
										} else {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;sortby=start_date\">Start Date</a></td>";
										}
								    }
								    ?>
								    <?php
								    if($query == 'former_staff') {
								    	if ($sortby == 'end_date') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;query=former_staff&amp;sortby=end_date2\"><img src=\"/images/down.gif\" alt=\"sorted ascending\" style=\"float:right;\" border=\"0\">End Date</td>";
										} else if ($sortby == 'end_date2') {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;query=former_staff&amp;sortby=end_date\"><img src=\"/images/up.gif\" alt=\"sorted descending\" style=\"float:right;\" border=\"0\">End Date</td>";
										} else {
								    		echo "<td class=\"body\"><a href=\"http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&amp;query=former_staff&amp;sortby=end_date\">End Date</a></td>";
										}
								    }
								    ?>
							   <td class="body">Last Updated</td></tr>
							
								<?php foreach($searchResult['data'] as $key => $searchData) { ?>
								<tr><td class="body"><?php echo $searchData['staff_ID'][0];?></td><td class="body" nowrap><a href="staff_profiles_com.php?action=show_1&staff_ID=<?php echo $searchData['staff_ID'][0];?>"><?php echo stripslashes($searchData['c_full_name_last_first'][0]);?></a></td><td class="body"><?php echo $searchData['primary_SEDL_workgroup'][0];?></td><td class="body"><?php echo $searchData['job_title'][0];?></td><td class="body"><?php echo $searchData['FTE_status'][0];?></td><td class="body"><?php echo $searchData['empl_start_date'][0];?></td>
								    <?php
								    if($query == 'former_staff') {
								    	echo "<td class=body>";
								    	echo $searchData['empl_end_date'][0];
								    	echo "</td>";
								    }
								    ?>
								<td class="body" nowrap><?php echo $searchData['last_mod_timestamp'][0];?></td></tr>
								<?php } ?>

<!--END FIRST SECTION: STAFF PROFILES-->		

							</table>
			</td></tr>			
			</table>
</td></tr>
</table>

</body>
</html>
 <?php
 
#################################
## END: DISPLAY ALL STAFF LIST ##
#################################

} elseif ($action == 'show_1') { 

$staff_ID = $_REQUEST['staff_ID'];
################################
## START: GRAB FMP VALUELISTS ##
################################
$v1 = new FX($serverIP,$webCompanionPort);
$v1 -> SetDBData('SIMS_2.fp7','staff');
$v1 -> SetDBPassword($webPW,$webUN);
$v1Result = $v1 -> FMView();
##############################
## END: GRAB FMP VALUELISTS ##
##############################

#################################
## START: FIND EMPLOYEE RECORD ##
#################################
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','staff','all');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('staff_ID','=='.$staff_ID);

//$search4 -> AddSortParam('sims_user_ID','ascend');

$searchResult4 = $search4 -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
$recordData4 = current($searchResult4['data']);
###############################
## END: FIND EMPLOYEE RECORD ##
###############################


####################################
## START: DISPLAY EMPLOYEE RECORD ## 
####################################
?>


<html>
<head>
<title>SIMS - Staff Profiles (Comm Admin): <?php echo stripslashes($recordData4['c_full_name_last_first'][0]);?></title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="javascript" type="text/javascript" src="http://www.sedl.org/common/javascript/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">     
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	plugins : "spellchecker,paste",
	gecko_spellcheck : true,
   	force_br_newlines : true,
   	force_p_newlines : false,
	forced_root_block : false,
	paste_auto_cleanup_on_paste : true,
 	theme_advanced_toolbar_location : "top",
   	theme_advanced_toolbar_align : "left",
	apply_source_formatting : true,
	theme_advanced_buttons1 : "bold,italic,underline,bullist,numlist,undo,redo,link,unlink,charmap,spellchecker,pastetext,pasteword,cleanup,code,styleselect",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	content_css: "/css/sedl2007_forTinyMCE.css",
	convert_urls : false
});
</script>


</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="800" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="666666" bgcolor="ffffff" width="800">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo-small.gif" width="500" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS - Staff Profiles - Communications Admin</h1><hr /></td></tr>
			
			<tr bgcolor="#e2eaa4"><td class="body"><strong><?php echo stripslashes($recordData4['c_full_name_last_first'][0]);?> - <?php echo $recordData4['primary_SEDL_workgroup'][0];?></strong></td><td align="right">Current user: <?php echo $_SESSION['user_ID'];?> | <a href="staff_profiles_com.php?action=show_all">Show All</a> | <a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			
			
			<tr><td class="body" colspan="2"><p class="info_small"><span class="tiny">NOTE: Any changes made to this record must be saved by clicking the Update Profile button. | Last updated: <?php echo $recordData4['last_mod_timestamp'][0];?></p></td></tr>

			<tr><td class="body" colspan=2>

<!--BEGIN FIRST SECTION: STAFF PROFILE-->


							<table cellspacing="0" cellpadding="1" width="100%" border="1" bordercolor="#cccccc" class="sims">
							
							<form name="communications_update" method="post">
							<input type="hidden" name="action" value="update">
							<input type="hidden" name="update_row_ID" value="<?php echo $recordData4['c_cwp_row_ID'][0];?>">

							<tr bgcolor="#e2eaa4"><td class="body">&nbsp;UPDATE SIMS PROFILE FOR: <?php echo stripslashes($recordData4['c_full_name_last_first'][0]);?></td></tr>
							
<!--BEGIN FOURTH SECTION: RESPONSIBILITIES/EXPERIENCE/EDUCATION-->

							<tr><td class="body" valign="top">
							
								<table cellspacing="0" cellpadding="5" border="0" width="100%">

								<tr valign="bottom"><td align="right"><td style="border:0px">

								<input type="checkbox" name="automated_sentence" value="hide" <?php if($recordData4['automated_sentence'][0] == 'hide'){echo 'CHECKED';}?>> Check this box to suppress display of the automated intro sentence (name, title and program).
								<br><br>
								<font color="666666"><span class="tiny"><strong>CURRENT RESPONSIBILITIES</strong></span></font><br><textarea name="responsibilities" rows="10" cols="70"><?php echo stripslashes($recordData4['current_responsibilities'][0]);?></textarea><p>
								<font color="666666"><span class="tiny"><strong>AREAS OF EXPERTISE</strong></span></font><br><textarea name="areas_expertise" rows="10" cols="70"><?php echo stripslashes($recordData4['areas_expertise'][0]);?></textarea><p>
								<font color="666666"><span class="tiny"><strong>AREAS OF EXPERTISE - for SECC</strong></span></font><br><textarea name="areas_expertise_list" rows="10" cols="70"><?php echo stripslashes($recordData4['areas_expertise_list'][0]);?></textarea><p>
								<font color="666666"><span class="tiny"><strong>AREAS OF EXPERTISE - for TXCC</strong></span></font><br><textarea name="areas_expertise_list_txcc" rows="10" cols="70"><?php echo stripslashes($recordData4['areas_expertise_list_txcc'][0]);?></textarea><p>
								<font color="666666"><span class="tiny"><strong>EXPERIENCE</strong></span></font><br><textarea name="experience" rows="10" cols="70"><?php echo stripslashes($recordData4['experience_education'][0]);?></textarea><p>
								<font color="666666"><span class="tiny"><strong>EDUCATION</strong></span></font><br><textarea name="education" rows="10" cols="70"><?php echo stripslashes($recordData4['education'][0]);?></textarea><p>
								<font color="666666"><span class="tiny"><strong>OTHER INFORMATION (e.g. EXTERNAL PUBLICATIONS, MEMBERSHIPS)</strong><br>
								Unlike the other fields, you must insert a bold heading before the text.
								<br><br>
								To achieve a hanging indentation for the list of presentations/publications, make the list under the heading into a bulleted (not numbered) list.  When viewed on the web site, this will appear with hanging indents.</span></font>
								<textarea name="external_publications" rows="10" cols="70"><?php echo stripslashes($recordData4['external_publications'][0]);?></textarea><br><p>
								
								</td><td valign="top" nowrap style="border:0px">
								<font color="666666"><span class="tiny"><strong>PHOTO PERMISSIONS:</strong></span></font><br />
								<select name="photo_permissions" class="body">
								<option value="">
								
								<?php foreach($v1Result['valueLists']['photo_permissions'] as $key => $value) { ?>
								<option value="<?php echo $value;?>" <?php if($recordData4['photo_permissions'][0] == $value){echo 'SELECTED';}?>> <?php echo $value; ?>
								<?php } ?>
								</select>

								<p>

								<img src="http://www.sedl.org/images/people/<?php echo $recordData4['sims_user_ID'][0];?>.jpg"><p>
								<a href="http://www.sedl.org/staff/personnel/staffprofiles.cgi?intranetonly=yes&showuserid=<?php echo $recordData4['sims_user_ID'][0];?>" target="_blank">Internal profile</a><br />
								<a href="http://www.sedl.org/pubs/catalog/authors/<?php echo $recordData4['sims_user_ID'][0];?>.html" target="_blank">Public profile</a>


									<br /><br />
									<font color="666666">Birthday:</font>

									
									<select name="birthmonth" class="body">
									<option value="">
									
									<?php foreach($v1Result['valueLists']['birthmonth'] as $key => $value) { ?>
									<option value="<?php echo $value;?>" <?php if($recordData4['birthmonth'][0] == $value){echo 'SELECTED';}?>> <?php echo $value; ?>
									<?php } ?>
									</select>
									
									<select name="birthday" class="body">
									<option value="">
									
									<?php foreach($v1Result['valueLists']['birthday'] as $key => $value) { ?>
									<option value="<?php echo $value;?>" <?php if($recordData4['birthday'][0] == $value){echo 'SELECTED';}?>> <?php echo $value; ?>
									<?php } ?>
									</select>
								<p>

								<input type="checkbox" name="show_birthday" value="no" <?php if($recordData4['show_birthday'][0] == 'no'){echo 'CHECKED';}?>> Don't show birthday
								

								<p>
									<font color="666666"><span class="tiny"><strong>EDUCATION DEGREE</strong></span></font><br>
									<input type="text" name="education_degree" value="<?php echo $recordData4['education_degree'][0];?>" size="20">
								
								</td></tr>

								
								</table>
								
							</td></tr>
							
<!--END FOURTH SECTION: RESPONSIBILITIES/EXPERIENCE/EDUCATION-->
							







							<tr><td class="body">
							<center><input type="submit" name="submit" value="Update Profile"></center>
							</td></tr>
							
							
							
							
							</table>

	

			</form>
			
			
			
			
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>







</body>

</html>


<?php
##################################
## END: DISPLAY EMPLOYEE RECORD ## 
##################################
 
 } elseif ($action == 'update') {

#######################################
## START: GRAB UPDATE FORM VARIABLES ##
#######################################
$update_row_ID = $_REQUEST['update_row_ID'];
$responsibilities = $_REQUEST['responsibilities'];
$areas_expertise = $_REQUEST['areas_expertise'];
$areas_expertise_list = $_REQUEST['areas_expertise_list'];
$areas_expertise_list_txcc = $_REQUEST['areas_expertise_list_txcc'];
$experience = $_REQUEST['experience'];
$education = $_REQUEST['education'];
$education_degree = $_REQUEST['education_degree'];
$external_publications = $_REQUEST['external_publications'];
$photo_permissions = $_REQUEST['photo_permissions'];
$show_birthday = $_REQUEST['show_birthday'];
$automated_sentence = $_REQUEST['automated_sentence'];
$birthmonth = $_REQUEST['birthmonth'];
$birthday = $_REQUEST['birthday'];

$trigger = rand();
#####################################
## END: GRAB UPDATE FORM VARIABLES ##
#####################################

##################################
## START: UPDATE THE FMP RECORD ##
##################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','staff');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$update_row_ID);

$update -> AddDBParam('current_responsibilities',$responsibilities);
$update -> AddDBParam('areas_expertise',$areas_expertise);
$update -> AddDBParam('areas_expertise_list',$areas_expertise_list);
$update -> AddDBParam('areas_expertise_list_txcc',$areas_expertise_list_txcc);
$update -> AddDBParam('experience_education',$experience);
$update -> AddDBParam('education',$education);
$update -> AddDBParam('education_degree',$education_degree);
$update -> AddDBParam('external_publications',$external_publications);
$update -> AddDBParam('photo_permissions',$photo_permissions);
$update -> AddDBParam('show_birthday',$show_birthday);
$update -> AddDBParam('automated_sentence',$automated_sentence);
$update -> AddDBParam('last_updated_by',$_SESSION['user_ID']);
$update -> AddDBParam('profile_info_last_mod_timestamp_trigger',$trigger);
$update -> AddDBParam('birthmonth',$birthmonth);
$update -> AddDBParam('birthday',$birthday);

$updateResult = $update -> FMEdit();

//echo $updateResult['errorCode'];
if($updateResult['errorCode'] == '0'){
$confirm_update = '1';

$updaterecordData = current($updateResult['data']);


$lastupdated = $updaterecordData['last_mod_date'][0];
$lastupdated_by = $updaterecordData['last_updated_by'][0];






// ADD SQL UPDATE CODE HERE TO UPDATE 'staff_profiles' mySQL DATABASE


// CONNECT TO mySQL

$db = mysql_connect('localhost','intranetuser','limited');

if(!$db) {
	die('Not connected : '. mysql_error());
} else {
//echo 'Connected to: mysql';	
}

// CONNECT TO staff_profiles database

$db_selected = mysql_select_db('intranet',$db);

if(!$db_selected) {
echo 'no connection';
	die('Can\'t use intranet : ' . mysql_error());
} else {
//echo 'Connected to: mysql database intranet';
}

// declare new field to hold three types of information
$expertise_experience_education = "";

if ($areas_expertise != '') {
	$areas_expertise = str_replace("<ul>","<ul STYLE=\"list-style-image: url(http://www.sedl.org/images/bullets/light-blue.gif)\">",$areas_expertise);
	$expertise_experience_education = "<strong>Areas of Expertise</strong><br>$areas_expertise\n";
}

if ($experience != '') {
	if ($expertise_experience_education != '') {
		$expertise_experience_education = "$expertise_experience_education<p></p>\n";
	}
	$expertise_experience_education = "$expertise_experience_education\n<strong>Experience</strong><br>$experience\n";
}
if ($education != '') {
	if ($expertise_experience_education != '') {
		$expertise_experience_education = "$expertise_experience_education<p></p>\n";
	}
	$expertise_experience_education = "$expertise_experience_education\n<strong>Education</strong><br>$education\n";
}

$experience = $expertise_experience_education;

# BACKSLASH VARIABLES BEING SENT TO MYSQL
$responsibilities = addslashes($responsibilities);
$experience = addslashes($experience);
$photo_permissions = addslashes($photo_permissions);
$show_birthday = addslashes($show_birthday);
$birthmonth = addslashes($birthmonth);
$birthday = addslashes($birthday);
$automated_sentence = addslashes($automated_sentence);
$external_publications = addslashes($external_publications);
$education_degree = addslashes($education_degree);
$areas_expertise_list = addslashes($areas_expertise_list);
$areas_expertise_list_txcc = addslashes($areas_expertise_list_txcc);

$command = 
"UPDATE staff_profiles 
SET 
responsibilities = '$responsibilities',
experience = '$experience',
photo_permissions='$photo_permissions', 
show_birthday='$show_birthday', 
birthmonth='$birthmonth', 
birthday='$birthday', 
automated_sentence='$automated_sentence', 
external_publications='$external_publications',
degree='$education_degree',
areas_expertise_list='$areas_expertise_list',
areas_expertise_list_txcc='$areas_expertise_list_txcc'

WHERE fm_record_id like '$update_row_ID'";
$update = mysql_query($command);


if (!$update) {
   die('Invalid query: ' . mysql_error());
}else{

//$num_results = mysql_num_rows($result);

//

exec('/home/httpd/html/staff/personnel/staffprofiles.cgi'); // REGENERATE INTRANET AND PUBLIC STAFF PAGES
//echo '<br>Update Successful!';
}

//exit;

} else {
echo 'There was an error updating the record.';
exit;
}
################################
## END: UPDATE THE FMP RECORD ##
################################

#################################################################################################
## START: DISPLAY THE STAFF PROFILE RECORDS IN LIST VIEW
#################################################################################################
header('Location: http://www.sedl.org/staff/sims/staff_profiles_com.php?action=show_all&confirm_update=1');
exit;

#################################################################################################
## END: DISPLAY THE STAFF PROFILE RECORDS IN LIST VIEW
#################################################################################################




 
 } else {
 
 echo 'Error';
 
 }
 ?>
<?php
session_start();

//echo 'HELLO';

if($src==''){
$src = $_GET['src'];
}

if($src == 'logout') {
header("Location:http://www.sedl.org/staff/");
exit;
}
//echo $src;
//echo '<p>'.$_SESSION['user_ID'];
//echo '<p>'.$_SESSION['staff_ID'];
//echo '<p>'.$_SESSION['immediate_supervisor'];

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];
$session_cookie = $_COOKIE['ss_session_id']; 

if(strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid']) == false){ // THE STAFF MEMBER'S INTRANET BROWSER COOKIE HAS BEEN DESTROYED OR EXPIRED
//header("Location:http://www.sedl.org/staff/");
//exit;
?>


		<html>
		<head>
		<title>Session Not Valid</title>
		<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
		<META HTTP-EQUIV=REFRESH CONTENT="0;URL=/staff">
		</head>
		
		<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">
		
		<table width="811" cellpadding="0" cellspacing="0" border="0">
		<tr><td class="body" align="center"><img src="http://www.sedl.org/staff/sims/images/header-logo.gif" border="0"></td></tr>
		<tr><td>
		
					<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
					
					<tr><td class="body" align="center"><p class="alert"><b>Your session is no longer valid | You will be taken to the <a href="/staff/">SEDL Intranet Login</a> in 5 seconds.</p></td></tr>
					
					</table>
		
		
		
		</td></tr>
		</table>
		
		
		</body>
		
		</html>
		
		
		<?php  
		session_destroy();
		exit;

} else { 

###CONNECT TO MYSQL - PROCEDURAL VERSION###

//print_r($update);

$db = mysql_connect('localhost','intranetuser','limited');

if(!$db) {
	die('Not connected : '. mysql_error());
} else {
//echo 'Connected to: mysql';	
}

//select cc as the current db

$db_selected = mysql_select_db('intranet',$db);

if(!$db_selected) {
echo 'no connection';
	die('Can\'t use test : ' . mysql_error());
} else {
//echo 'Connected to: mysql database test';
}




###UPDATE MYSQL FROM PHP###

$command = "select * from staff_sessions where ss_session_id like '$session_cookie'";

//echo $command;
//echo '<p>100';

$result = mysql_query($command);
if (!$result) {
   die('Invalid query: ' . mysql_error());
}else{

$num_results = mysql_num_rows($result);

//echo '<br>Num Results: '.$num_results;
}


if($num_results !== 1){ ?>


<html>
		<head>
		<title>Session Not Valid</title>
		<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
		<META HTTP-EQUIV=REFRESH CONTENT="5;URL=/staff">
		</head>
		
		<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">
		
		<table width="811" cellpadding="0" cellspacing="0" border="0">
		<tr><td class="body" align="center"><img src="http://www.sedl.org/staff/sims/images/header-logo.gif" border="0"></td></tr>
		<tr><td>
		
					<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
					<tr><td class="body" align="center"><p class="alert"><b>Your session is no longer valid | You will be taken to the <a href="/staff/">SEDL Intranet Login</a> in 5 seconds.</p></td></tr>
					
					</table>
		
		
		
		</td></tr>
		</table>
		
		
		</body>
		
		</html>
		
		
		
		
		<?php  
		session_destroy();
		exit;

}

//echo '<p>$src: '.$src;
//echo '<p>$_SESSION[login_status]: '.$_SESSION['login_status'];
		
		if(($src == 'intr') && (($_SESSION['login_status'] != 'active') || ($_SESSION['user_ID'] != $_COOKIE['staffid']))){
		
		//echo '<p>157';
				$staff_id_cookie = $_COOKIE['staffid'];
				
				include_once('FX/FX.php');
				include_once('FX/server_data.php');
				
				$search = new FX($serverIP,$webCompanionPort);
				$search -> SetDBData('SIMS_2.fp7','staff','all');
				$search -> SetDBPassword($webPW,$webUN);
				//$search -> FMSkipRecords($skipsize);
				$search -> AddDBParam('sims_user_ID',$staff_id_cookie);
				//$search -> AddDBParam('c_Active_Status','Active');
				//$search -> AddDBParam('-lop','or');
				
				//$search -> AddSortParam($sortfield,'descend');
				
				
				$searchResult = $search -> FMFind();
				
				//echo '<p>'.$searchResult['errorCode'];
				//echo '<p>'.$searchResult['foundCount'];
				//print_r ($searchResult);
				$recordData = current($searchResult['data']);
				
						if($searchResult['foundCount'] == 1)
						{
						
							$_SESSION['login_status'] = 'active';
							
							$_SESSION['staff_ID'] = $recordData['staff_ID'][0];
							$_SESSION['user_ID'] = $recordData['sims_user_ID'][0];
							$_SESSION['staff_name'] = stripslashes($recordData['c_full_name_first_last'][0]);
							$_SESSION['staff_email'] = $recordData['email'][0];
							$_SESSION['timesheet_name'] = $recordData['name_timesheet'][0];
							$_SESSION['workgroup'] = $recordData['primary_SEDL_workgroup'][0];
							$_SESSION['PrimarySEDLWorkgroup'] = $recordData['primary_SEDL_workgroup'][0];
							$_SESSION['payperiod_type'] = $recordData['payperiod_type'][0];
							$_SESSION['immediate_supervisor'] = $recordData['immediate_supervisor_sims_user_ID'][0];
							$_SESSION['primary_bgt_auth'] = $recordData['bgt_auth_primary_sims_user_ID'][0];
							$_SESSION['employee_type'] = $recordData['employee_type'][0];
							$_SESSION['title'] = $recordData['job_title'][0];
							$_SESSION['employee_FTE_status'] = $recordData['FTE_status'][0];
							$_SESSION['svc_log_admin_wg'] = $recordData['cwp_sims_access_staff_svc_log_admin_wg'][0];
							$_SESSION['svc_log_admin_sedl'] = $recordData['cwp_sims_access_staff_svc_log_admin_sedl'][0];
							$_SESSION['svc_log_admin_prgms'] = $recordData['cwp_sims_access_staff_svc_log_admin_pgms'][0];
							$_SESSION['svc_log_admin_spvsr'] = $recordData['cwp_sims_access_staff_svc_log_admin_spvsr'][0];
							$_SESSION['svc_log_admin_allow_surrogates'] = $recordData['cwp_sims_access_staff_svc_log_allow_surrogate_entries'][0];

							
							
							//$_SESSION['most_recent_timesheet'] = $recordData['???'][0];
							
							
							//$src = '';
							
							
							//echo '<p>FOUND STAFF_ID AND RESET SESSION VARIABLES';
							//echo '<span style="color:#999999">User: '.$_SESSION['user_ID'].' | '.$_GET['src'].' | '.$_SESSION['login_status'].'</span>';
						
						} else {
							
							echo 'Login Error: '.$searchResult['errorCode'];
							echo '<br>FoundCount: '.$searchResult['foundCount'];
							echo '<br>Staff ID: '.$staff_id_cookie;
							
							exit;
						}
				
		} else {
		//echo '<p>227';
			//echo 'VERIFIED SESSION IS GOOD BUT DID NOT PERFORM ANOTHER FIND ON USER_ID AND RESET OF SESSION VARIABLES';	
		}

}

?>
<?php
session_start();



$src = $_GET['src'];
//echo $src;

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];
$session_cookie = $_COOKIE['ss_session_id']; 

if (!strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){ ?>


		<html>
		<head>
		<title>Session Not Valid</title>
		<link href="/staff/includes/staff2006.css" rel="stylesheet" type="text/css">
		</head>
		
		<BODY BGCOLOR="#092129" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">
		
		<table width="930" cellpadding="0" cellspacing="0" border="0">
		
		<tr><td>
		
					<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
					<tr><td class="body" align="center"><p class="alert"><b>Your session is no longer valid | <a href="/staff/">Return to SEDL Intranet Login</a></p></td></tr>
					
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

$db = mysql_connect('localhost','sedlstaff','opensesame123');

if(!$db) {
	die('Not connected : '. mysql_error());
} else {
//echo 'Connected to: mysql';	
}

//select cc as the current db

$db_selected = mysql_select_db('test',$db);

if(!$db_selected) {
echo 'no connection';
	die('Can\'t use test : ' . mysql_error());
} else {
//echo 'Connected to: mysql database test';
}




###UPDATE MYSQL FROM PHP###

$command = "select * from staff_sessions where ss_session_id like '$session_cookie'";

//echo $command;


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
		<link href="/staff/includes/staff2006.css" rel="stylesheet" type="text/css">
		</head>
		
		<BODY BGCOLOR="#092129" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">
		
		<table width="930" cellpadding="0" cellspacing="0" border="0">
		
		<tr><td>
		
					<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
					<tr><td class="body" align="center"><p class="alert"><b>Your session is no longer valid | <a href="/staff/">Return to SEDL Intranet Login</a></p></td></tr>
					
					</table>
		
		
		
		</td></tr>
		</table>
		
		
		</body>
		
		</html>
		
		
		
		
		<?php  
		session_destroy();
		exit;

		}


		
		if(($_GET['src'] == 'intr') && (($_SESSION['login_status'] !== 'active') || ($_SESSION['staff_ID'] !== $_COOKIE['staffid'])))
		{
		
				$staff_id_cookie = $_COOKIE['staffid'];
				
				include_once('FX/FX.php');
				include_once('FX/server_data.php');
				
				$search = new FX($serverIP,$webCompanionPort);
				$search -> SetDBData('SIMS.fp7','cwp_staff','all');
				$search -> SetDBPassword($webPW,$webUN);
				//$search -> FMSkipRecords($skipsize);
				$search -> AddDBParam('user_ID',$staff_id_cookie);
				//$search -> AddDBParam('c_Active_Status','Active');
				//$search -> AddDBParam('-lop','or');
				
				//$search -> AddSortParam($sortfield,'descend');
				
				
				$searchResult = $search -> FMFind();
				
				//echo $searchResult['errorCode'];
				//echo $searchResult['foundCount'];
				//print_r ($searchResult);
				$recordData = current($searchResult['data']);
				
						if($searchResult['foundCount'] == 1)
						{
						
							$_SESSION['login_status'] = 'active';
							
							$_SESSION['staff_ID'] = $recordData['staff_ID'][0];
							$_SESSION['user_ID'] = $recordData['user_ID'][0];
							$_SESSION['timesheet_name'] = $recordData['name_timesheet'][0];
							$_SESSION['workgroup'] = $recordData['PrimarySEDLWorkgroup'][0];
							$_SESSION['payperiod_type'] = $recordData['payperiod_type'][0];
							$_SESSION['immediate_supervisor'] = $recordData['ImmediateSupervisor'][0];
							$_SESSION['primary_bgt_auth'] = $recordData['PrimaryBudgetAuthority'][0];
							$_SESSION['employee_type'] = $recordData['EmployeeType'][0];
							$_SESSION['payperiod_type'] = $recordData['payperiod_type'][0];
							
							//$_SESSION['most_recent_timesheet'] = $recordData['???'][0];
							
							
							//$src = '';
							
							
							echo 'FOUND STAFF_ID AND RESET SESSION VARIABLES';
							echo '<br>Source: '.$_GET['src'];
							echo 'Login Status: '.$_SESSION['login_status'];
						
						} else {
							
							echo 'Login Error: '.$searchResult['errorCode'];
							echo '<br>FoundCount: '.$searchResult['foundCount'];
							echo '<br>Staff ID: '.$staff_id_cookie;
							
							exit;
						}
				
		} else {
			//echo 'VERIFIED SESSION IS GOOD BUT DID NOT PERFORM ANOTHER FIND ON USER_ID AND RESET OF SESSION VARIABLES';	
		}

}

?>
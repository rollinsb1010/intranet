<?php
session_start();
		
include_once('FX/FX.php');
include_once('FX/server_data.php');

#$staff_id_cookie = $_COOKIE['staffid']; # NOW PASSED AS VARIABLE #logon_user
$logon_user = $_POST['logon_user'];
$logon_pass = $_POST['logon_pass'];
$show_s = $_POST['show_s'];
$show_sg = $_POST['show_sg'];
$pid = $_POST['pid'];

##################################################
## START: ALLOW TECHIES TO USE A SUPERUSER LOGIN
##################################################
$super_login = "no";
if ($logon_pass == 'sudo') {
	$super_login = "yes";
}
##################################################
## END: ALLOW TECHIES TO USE A SUPERUSER LOGIN
##################################################

##################################################
## START: COOKIE DEFAULT VARIABLES
##################################################
#$expdate = "Fri, 25-Dec-2015 00:00:00 GMT"; # FROM Perl
$expdate = time()+60*60*24*30; #In this example the expiration time is set to a month (60 sec * 60 min * 24 hours * 30 days).
$thedomain = ".sedl.org";
$path = "/";
##################################################
## END: COOKIE DEFAULT VARIABLES
##################################################


//date.timezone = "America/Chicago"
#THIS SHOUL DBE A 10-DIGIT TIMESTAMP (no year included)
$session_suffix = date("mdHis", time());

	if (($logon_user != '') && ($logon_pass != '')) {
		## PROCEED WITH CHECKING LOGON CREDENTIALS
		$strong_pwd = crypt($logon_pass,'password');

		#######################################################################
		## START: CONNECT TO DATABASE - COUNT MATCHES FOR USERID AND PASSWORD
		#######################################################################
		$connection = mysql_connect('localhost', 'intranetuser', 'limited') or die('Could not connect to Staff database: ' . mysql_error());
		$db_select = mysql_select_db('intranet', $connection) or die('<p>Could not select Staff database</p>'. mysql_error());
		$query = "select userid from staff_profiles where 
			((userid like '$logon_user') AND (strong_pwd LIKE '$strong_pwd') )";
		$result = mysql_query($query, $connection) or die('Query failed: ' . mysql_error());
		$num_matches_password = mysql_num_rows($result);
		#######################################################################
		## END: CONNECT TO DATABASE - COUNT MATCHES FOR USERID AND PASSWORD
		#######################################################################

		#############################################################
		## START: CONNECT TO DATABASE - COUNT MATCHES FOR USERID
		#############################################################
		$connection2 = mysql_connect('localhost', 'intranetuser', 'limited') or die('Could not connect to Staff database: ' . mysql_error());
		$db_select = mysql_select_db('intranet', $connection2) or die('<p>Could not select Staff database</p>'. mysql_error());
		$query2 = "select userid from staff_profiles where 
			(userid like '$logon_user')";
		$result = mysql_query($query2, $connection2) or die('Query failed: ' . mysql_error());
		$num_matches_for_logon_id_entered = mysql_num_rows($result);
		#############################################################
		## END: CONNECT TO DATABASE - COUNT MATCHES FOR USERID
		#############################################################


		if (($num_matches_password == '1') || (($super_login == 'yes') && ($num_matches_for_logon_id_entered == 1)) ) {
			#############################################################
			## START: HANDLE GOOD LOGON
			#############################################################
			$cookie_ss_session_id = "$logon_user$session_suffix";
			
			## VALID ID/PASSWORD, SET SESSION
			$connection = mysql_connect('localhost', 'intranetuser', 'limited') or die('Could not connect to Staff database: ' . mysql_error());
			$db_select = mysql_select_db('intranet', $connection) or die('<p>Could not select Staff database</p>'. mysql_error());
			$command_set_session = "REPLACE into staff_sessions VALUES ('$cookie_ss_session_id', '$logon_user', '$timestamp', '$remote_addr', '', '', '', '')";
			$result = mysql_query($command_set_session, $connection) or die('Query failed: ' . mysql_error());
		
			## SAVE COOKIES WITH SESSION ID AND USER ID
				$cookie_ss_staff_id = $logon_user;
				setCookie ("ss_session_id", "$cookie_ss_session_id", $expdate, $path, $thedomain);
				setCookie ("staffid", $logon_user, $expdate, $path, $thedomain);

			####################################################################################
			## START: SET PHP SESSION VARIABLESS FOR SIMS LOGON TO FILEMAKER FROM PHP SCRIPTS
			####################################################################################
				$search = new FX($serverIP,$webCompanionPort);
				$search -> SetDBData('SIMS_2.fp7','staff','all');
				$search -> SetDBPassword($webPW,$webUN);
				//$search -> FMSkipRecords($skipsize);
				$search -> AddDBParam('sims_user_ID',$logon_user);
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
							$_SESSION['user_ID'] = $recordData['sims_user_ID'][0];
							$_SESSION['staff_name'] = stripslashes($recordData['c_full_name_first_last'][0]);
							$_SESSION['staff_email'] = $recordData['email'][0];
							$_SESSION['timesheet_name'] = $recordData['name_timesheet'][0];
							$_SESSION['workgroup'] = $recordData['primary_SEDL_workgroup'][0];
							$_SESSION['payperiod_type'] = $recordData['payperiod_type'][0];
							$_SESSION['immediate_supervisor'] = $recordData['immediate_supervisor_sims_user_ID'][0];
							$_SESSION['primary_bgt_auth'] = $recordData['bgt_auth_primary_sims_user_ID'][0];
							$_SESSION['employee_type'] = $recordData['employee_type'][0];
							$_SESSION['title'] = $recordData['job_title'][0];
							$_SESSION['employee_FTE_status'] = $recordData['FTE_status'][0];
							$_SESSION['paystub_admin_access'] = $recordData['cwp_sims_access_paystubs_admin'][0];
						} else {
							
							echo 'Login Error: '.$searchResult['errorCode'];
							echo '<br>FoundCount: '.$searchResult['foundCount'];
							echo '<br>Staff ID: '.$logon_user;
							
							exit;
						}
			####################################################################################
			## END: SET PHP SESSION VARIABLESS FOR SIMS LOGON TO FILEMAKER FROM PHP SCRIPTS
			####################################################################################

			## SET LOCATION TO SHOW PAGE ON INTRANET
				header("Location:http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?location=show_page&amp;show_s=$show_s&amp;show_sg=$show_sg&amp;pid=$pid");
#			echo "You should now be logged in as $logon_user";

			#############################################################
			## END: HANDLE GOOD LOGON
			#############################################################
		} else {
			#############################################################
			## START: HANDLE BAD LOGON
			#############################################################
			## INVALID ID/PASS: SHOW LOON SCREEN & SET ERROR MESSAGE
			if ($num_matches_for_logon_id_entered == 1) {
				$error_message = "Your User ID ($logon_user) is valid, but the password ($logon_pass) you entered did not match the one on file.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
			} else {
				if (strlen($logon_user) > 8) {
					$error_message = "The User ID you entered ($logon_user) is over 8 characters long.  The intranet logon is your first initial and last name, with a maximum limit of 8 characters.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
				} else {
					$error_message = "The User ID you entered ($logon_user) is not a valid SEDL staff ID.  Try again, or contact SEDL's web administrator, Brian Litke, at ext. 6529.";
				}
			}
			$error_message = str_replace(" ","+",$error_message); 
			header("Location:http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?location=show_page&amp;error_message=$error_message&amp;show_s=$show_s&amp;show_sg=$show_sg&amp;pid=$pid");
			#############################################################
			## END: HANDLE BAD LOGON
			#############################################################
		}
	} else {
		#############################################################
		## START: HANDLE BLANK LOGON
		#############################################################
		## USER DIDN't ENTER USER ID OR PASSWORD, SHOW LOON SCREEN & SET ERROR MESSAGE
		if ($logon_user == '') {
			$error_message .= "You forgot to enter your User ID (ex: whoover).";
		}
		if ($logon_pass == '') {
			$error_message .= "You forgot to enter your password.";
		}
		$error_message = str_replace(" ","+",$error_message); 
		header("Location:http://www.sedl.org/cgi-bin/mysql/staff/index.cgi?location=show_page&amp;error_message=$error_message&amp;show_s=$show_s&amp;show_sg=$show_sg&amp;pid=$pid");
		#############################################################
		## END: HANDLE BLANK LOGON
		#############################################################
	}

######################################################
## END: LOCATION = PROCESS_LOGON
######################################################

?>

<?php
session_start();

include_once('FX/FX.php');
include_once('FX/server_data.php');

#####################################
## START: MYSQL CONNECTION SETINGS
#####################################
$hostname_staff = "localhost";
$database_staff = "corp";
$username_staff = "corpuser";
$password_staff = "public";
## NEW mysqli API
$mysqli = mysqli_connect($hostname_staff, $username_staff, $password_staff, $database_staff);
#####################################
## END: MYSQL CONNECTION SETINGS
#####################################


############################################################################################################################################################################################################################################################
############################################################################################################################################################################################################################################################


################################################################################################
## START: QUERY MYSQL DATABASE job_vacancies TO SEE WHICH JOBS NOT YET SYNC'D WITH FILEMAKER
################################################################################################
$query_to_get_list_of_nov = "
	SELECT sedl_unit, position_title, position_location, position_opens, position_closes, position_closes_review_begins, position_exempt_status, sync_w_filemaker, recordid, quantity 
	FROM job_vacancies WHERE show_onweb = 'yes'";
$list_of_nov = mysqli_query($mysqli, $query_to_get_list_of_nov);
$num_rows = mysqli_num_rows($list_of_nov);
//echo "<p>I found $num_rows matching NoVs.</p>";
###################################################################################
## END: GRAB STAFF DATA FROM MYSQL WHERE STAFF WORKGROUP IS SECCF
###################################################################################

	while ($nov = mysqli_fetch_row($list_of_nov)) {
		list ($sedl_unit, $position_title, $position_location, $position_opens, $position_closes, $position_closes_review_begins, $position_exempt_status, $sync_w_filemaker, $recordid, $quantity) = $nov;
echo '<p>sedl unit: '.$sedl_unit;
echo '<p>position title: '.$position_title;
echo '<p>sync with filemaker: '.$sync_w_filemaker;
echo '<p>record ID: '.$recordid;
echo '<p>quantity: '.$quantity;

## THIS CODE IS TO RETRIEVE AN ASSOCIATIVE ARRAY (KEY/VALUE PAIR)
#	while ($nov = mysqli_fetch_assoc($list_of_nov)) {
#		####################################################
#		## START: GRAB VARIABLES FROM THE ASSOCIATIVE ARRAY
#		####################################################
#		$recordid = $nov['recordid'];
#		$sedl_unit = $nov['sedl_unit'];
#		$position_title = $nov['position_title'];
#		$position_location = $nov['position_location'];
#		$position_opens = $nov['position_opens'];
#		$position_closes = $nov['position_closes'];
#		$position_closes_review_begins = $nov['position_closes_review_begins'];
#		$position_exempt_status = $nov['position_exempt_status'];
#		$sync_w_filemaker = $nov['sync_w_filemaker'];
#		####################################################
#		## END: GRAB VARIABLES FROM THE ASSOCIATIVE ARRAY
#		####################################################

		if ($sync_w_filemaker == 'yes') {
			## DO NOTHING, IT IS ALREADY SYNCED
		} elseif($sync_w_filemaker == 'update') { // NEED TO BE UPDATED
		
			###############################################################
			## START: SET VARIABLE IN MYSQL FOR sync_w_filemaker TO 'yes'
			###############################################################
			$command_update = "UPDATE job_vacancies set sync_w_filemaker = 'yes' where recordid = '$recordid'";
			$response = mysqli_query($mysqli, $command_update);
			###############################################################
			## END: SET VARIABLE IN MYSQL FOR sync_w_filemaker TO 'yes'
			###############################################################
#			echo "<p>$command_update</p>";


			#################################################
			## START: FIND THE FMP RECORD TO UPDATE ##
			#################################################
			$search = new FX($serverIP,$webCompanionPort);
			$search -> SetDBData('admin_base.fp7','positions_mysql'); //set dbase information
			$search -> SetDBPassword($webPW,$webUN);
			$search -> AddDBParam('mysql_ID',$recordid);
			
			$searchResult = $search -> FMFind();
			
			echo'<p>errorCode(search): '.$searchResult['errorCode'];
			echo'<p>foundCount(search): '.$searchResult['foundCount'];
			
			$recordData = current($searchResult['data']);
			
			$current_id = $recordData['c_row_ID'][0];
			#################################################
			## END: FIND THE FMP RECORD TO UPDATE ##
			#################################################



			#################################################
			## START: ADD THE NEW POSITION TO SIMS ##
			#################################################

			$update = new FX($serverIP,$webCompanionPort);
			$update -> SetDBData('admin_base.fp7','positions_mysql'); //set dbase information
			$update -> SetDBPassword($webPW,$webUN);
			$update -> AddDBParam('-recid',$current_id);

			$update -> AddDBParam('position_workgroup',$sedl_unit);
			$update -> AddDBParam('position_title',$position_title);
			$update -> AddDBParam('position_location',$position_location);
			$update -> AddDBParam('position_opens',$position_opens);
			$update -> AddDBParam('position_closes',$position_closes);
			$update -> AddDBParam('position_closes_review_begins',$position_closes_review_begins);
			$update -> AddDBParam('position_exempt_status',$position_exempt_status);
			$update -> AddDBParam('quantity_mysql',$quantity);
			
			$updateResult = $update -> FMEdit();

			echo  '<p>errorCode(update): '.$updateResult['errorCode'];
			echo  '<p>foundCount(update): '.$updateResult['foundCount'];
	
			//$newrecordData = current($newrecordResult['data']);
			#################################################
			## END: ADD THE NEW POSITION TO SIMS ##
			#################################################

		} else {
		
			###############################################################
			## START: SET VARIABLE IN MYSQL FOR sync_w_filemaker TO 'yes'
			###############################################################
			$command_update = "UPDATE job_vacancies set sync_w_filemaker = 'yes' where recordid = '$recordid'";
			$response = mysqli_query($mysqli, $command_update);
			###############################################################
			## END: SET VARIABLE IN MYSQL FOR sync_w_filemaker TO 'yes'
			###############################################################
#			echo "<p>$command_update</p>";

			#################################################
			## START: ADD THE NEW POSITION TO SIMS ##
			#################################################
			$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
			$newrecord -> SetDBData('admin_base.fp7','positions_mysql'); //set dbase information
			$newrecord -> SetDBPassword($webPW,$webUN); //set password information
	
			$newrecord -> AddDBParam('mysql_ID',$recordid);
			$newrecord -> AddDBParam('position_workgroup',$sedl_unit);
			$newrecord -> AddDBParam('position_title',$position_title);
			$newrecord -> AddDBParam('position_location',$position_location);
			$newrecord -> AddDBParam('position_opens',$position_opens);
			$newrecord -> AddDBParam('position_closes',$position_closes);
			$newrecord -> AddDBParam('position_closes_review_begins',$position_closes_review_begins);
			$newrecord -> AddDBParam('position_exempt_status',$position_exempt_status);
			$newrecord -> AddDBParam('quantity_mysql',$quantity);
	
			$newrecordResult = $newrecord -> FMNew();
	
			//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
			//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	
			//$newrecordData = current($newrecordResult['data']);
			#################################################
			## END: ADD THE NEW POSITION TO SIMS ##
			#################################################


		} # END IF/ELSE
	} # END WHILE LOOP
################################################################################################
## END: QUERY MYSQL DATABASE job_vacancies TO SEE WHICH JOBS NOT YET SYNC'D WITH FILEMAKER
################################################################################################



?>
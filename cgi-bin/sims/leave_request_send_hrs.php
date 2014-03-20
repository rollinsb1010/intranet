<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


$debug = 'on';

$src = $_GET['src'];
$pay_period = $_GET['pay_period'];
$leave_request_ID = $_GET['leave_request_ID'];
$timesheet_ID = $_GET['timesheet_ID'];

#################################################################
## START: FIND LEAVE REQUEST HRS RELATED TO THIS LEAVE REQUEST ##
#################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','leave_request_hrs');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('leave_request_ID','=='.$leave_request_ID);
//$search -> AddDBParam('-lop','or');

$search -> AddSortParam('c_leave_hrs_type_code','ascend');
$search -> AddSortParam('leave_hrs_date','ascend');


$searchResult = $search -> FMFind();

//echo '<p>$searchResult[errorCode]: '.$searchResult['errorCode'];
//echo '<p>$searchResult[foundCount]: '.$searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);
//$d = $recordData['leave_requests::c_pay_period_begin_d'][0];
$total_request_hrs_s = $recordData['c_total_request_hrs_s'][0];
$total_request_hrs_v = $recordData['c_total_request_hrs_v'][0];
$total_request_hrs_p = $recordData['c_total_request_hrs_p'][0];
$total_request_hrs_f = $recordData['c_total_request_hrs_f'][0];
$total_request_hrs_j = $recordData['c_total_request_hrs_j'][0];
$total_request_hrs_l = $recordData['c_total_request_hrs_l'][0];

//$current_day = $recordData['c_leave_hrs_day_num'][0];


###############################################################
## END: FIND LEAVE REQUEST HRS RELATED TO THIS LEAVE REQUEST ##
###############################################################
$previous_day = '';
#################################################################
## START: ADD FAMILY/MEDICAL HRS TO TIMESHEET ##
#################################################################
if($total_request_hrs_f > 0){

	###############################################################################################
	### START: FIND EXISTING TIME_HRS ROWS FOR THIS TIMESHEET, THIS PAY PERIOD, THIS LEAVE TYPE ###
	###############################################################################################
	$search5 = new FX($serverIP,$webCompanionPort);
	$search5 -> SetDBData('SIMS_2.fp7','time_hrs');
	$search5 -> SetDBPassword($webPW,$webUN);
	$search5 -> AddDBParam('Timesheet_ID','=='.$timesheet_ID);
	$search5 -> AddDBParam('HrsType','UnPdLv');
	$search5 -> AddDBParam('LvType','Family & Medical');
	
	$searchResult5 = $search5 -> FMFind();
	
	//echo $searchResult5['errorCode'];
	//echo $searchResult5['foundCount'];
	if($searchResult5['foundCount'] == 1){ // IF THERE IS ALREADY A FAM & MED LEAVE ROW ON THIS TIMESHEET
	
	$recordData5 = current($searchResult5['data']);
	$update_row_ID = $recordData5['c_cwp_row_ID'][0];// GRAB THE ROW ID FOR UPDATING
	}
	########################################################################################
	### END: FIND EXISTING TIME_HRS ROWS FOR THIS USER, THIS PAY PERIOD, THIS LEAVE TYPE ###
	########################################################################################

if($searchResult5['foundCount'] == 1){ // IF THERE IS ALREADY A FAM & MED LEAVE ROW ON THIS TIMESHEET

	$update = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$update -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$update -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$update -> AddDBParam('-recid',$update_row_ID);
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
	if($searchData['c_leave_hrs_type_code'][0] == 'F'){

			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY	
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
		
			if($current_day != $previous_day){
			$total_lv_hrs = $searchData['c_aggregate_lv_hrs'][0] + $recordData5["$hrs_fieldname"][0];// ADD LV HRS FOR THIS DAY
			$update -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			$previous_day = $current_day; // RESET PREVIOUS DAY
			}
		
		}
	
	
	}
	
	$updateResult = $update -> FMEdit();

	//echo  '<p>errorCode: '.$updateResult['errorCode'];
	//echo  '<p>foundCount: '.$updateResult['foundCount'];
	//exit;
	
	
} else { // IF THERE IS NO EXISTING FAM & MED LEAVE ROW ON THIS TIMESHEET

	$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$newrecord -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$newrecord -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$newrecord -> AddDBParam('Timesheet_ID',$timesheet_ID);
	$newrecord -> AddDBParam('HrsType','UnPdLv');
	$newrecord -> AddDBParam('LvType','Family & Medical');
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
		if($searchData['c_leave_hrs_type_code'][0] == 'F'){
		
			if($searchData['c_leave_hrs_day_num'][0] == $current_day){
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			} else {
			$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			//$last_field = $hrs_fieldname;
			
			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY TO NEXT LV HRS DAY
			$total_lv_hrs = 0; // RESET LV HRS COUNTER
			
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			
			}
		
		}
	
	
	//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
	//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
	}
	$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME -- LAST ROW IF FOUNDCOUNT > 1 OR FIRST ROW IF FOUNDCOUNT = 1
	
	
	$newrecordResult = $newrecord -> FMNew();
	
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	//exit;

}

}
#################################################################
## END: ADD FAMILY/MEDICAL HRS TO TIMESHEET ##
#################################################################
$previous_day = '';
#################################################################
## START: ADD JURY DUTY HRS TO TIMESHEET ##
#################################################################
if($total_request_hrs_j > 0){

	###############################################################################################
	### START: FIND EXISTING TIME_HRS ROWS FOR THIS TIMESHEET, THIS PAY PERIOD, THIS LEAVE TYPE ###
	###############################################################################################
	$search7 = new FX($serverIP,$webCompanionPort);
	$search7 -> SetDBData('SIMS_2.fp7','time_hrs');
	$search7 -> SetDBPassword($webPW,$webUN);
	$search7 -> AddDBParam('Timesheet_ID','=='.$timesheet_ID);
	$search7 -> AddDBParam('HrsType','PdLv');
	$search7 -> AddDBParam('LvType','Other');
	
	$searchResult7 = $search7 -> FMFind();
	
	//echo $searchResult7['errorCode'];
	//echo $searchResult7['foundCount'];
	if($searchResult7['foundCount'] == 1){ // IF THERE IS ALREADY A OTHER LEAVE ROW ON THIS TIMESHEET
	
	$recordData7 = current($searchResult7['data']);
	$update_row_ID = $recordData7['c_cwp_row_ID'][0];// GRAB THE ROW ID FOR UPDATING
	}
	########################################################################################
	### END: FIND EXISTING TIME_HRS ROWS FOR THIS USER, THIS PAY PERIOD, THIS LEAVE TYPE ###
	########################################################################################

if($searchResult7['foundCount'] == 1){ // IF THERE IS ALREADY A OTHER LEAVE ROW ON THIS TIMESHEET

	$update = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$update -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$update -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$update -> AddDBParam('-recid',$update_row_ID);
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
	if($searchData['c_leave_hrs_type_code'][0] == 'J'){

			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY	
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
		
			if($current_day != $previous_day){
			$total_lv_hrs = $searchData['c_aggregate_lv_hrs'][0] + $recordData7["$hrs_fieldname"][0];// ADD LV HRS FOR THIS DAY
			$update -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			$previous_day = $current_day; // RESET PREVIOUS DAY
			}
		
		}
	
	
	}
	
	$updateResult = $update -> FMEdit();

	//echo  '<p>errorCode: '.$updateResult['errorCode'];
	//echo  '<p>foundCount: '.$updateResult['foundCount'];
	//exit;
	
	
} else { // IF THERE IS NO EXISTING OTHER LEAVE ROW ON THIS TIMESHEET

	$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$newrecord -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$newrecord -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$newrecord -> AddDBParam('Timesheet_ID',$timesheet_ID);
	$newrecord -> AddDBParam('HrsType','PdLv');
	$newrecord -> AddDBParam('LvType','Other');
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
		if($searchData['c_leave_hrs_type_code'][0] == 'J'){
		
			if($searchData['c_leave_hrs_day_num'][0] == $current_day){
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			} else {
			$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			//$last_field = $hrs_fieldname;
			
			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY TO NEXT LV HRS DAY
			$total_lv_hrs = 0; // RESET LV HRS COUNTER
			
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			
			}
		
		}
	
	
	//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
	//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
	}
	$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME -- LAST ROW IF FOUNDCOUNT > 1 OR FIRST ROW IF FOUNDCOUNT = 1
	
	
	$newrecordResult = $newrecord -> FMNew();
	
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	//exit;

}

}
#################################################################
## END: ADD LEAVE JURY DUTY HRS TO TIMESHEET ##
#################################################################
$previous_day = '';
#################################################################
## START: ADD LEAVE W/O PAY HRS TO TIMESHEET ##
#################################################################
if($total_request_hrs_l > 0){

	###############################################################################################
	### START: FIND EXISTING TIME_HRS ROWS FOR THIS TIMESHEET, THIS PAY PERIOD, THIS LEAVE TYPE ###
	###############################################################################################
	$search6 = new FX($serverIP,$webCompanionPort);
	$search6 -> SetDBData('SIMS_2.fp7','time_hrs');
	$search6 -> SetDBPassword($webPW,$webUN);
	$search6 -> AddDBParam('Timesheet_ID','=='.$timesheet_ID);
	$search6 -> AddDBParam('HrsType','UnPdLv');
	$search6 -> AddDBParam('LvType','Leave w/o Pay');
	
	$searchResult6 = $search6 -> FMFind();
	
	//echo $searchResult6['errorCode'];
	//echo $searchResult6['foundCount'];
	if($searchResult6['foundCount'] == 1){ // IF THERE IS ALREADY A LEAVE W/O PAY LEAVE ROW ON THIS TIMESHEET
	
	$recordData6 = current($searchResult6['data']);
	$update_row_ID = $recordData6['c_cwp_row_ID'][0];// GRAB THE ROW ID FOR UPDATING
	}
	########################################################################################
	### END: FIND EXISTING TIME_HRS ROWS FOR THIS USER, THIS PAY PERIOD, THIS LEAVE TYPE ###
	########################################################################################

if($searchResult6['foundCount'] == 1){ // IF THERE IS ALREADY A LEAVE W/O PAY LEAVE ROW ON THIS TIMESHEET

	$update = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$update -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$update -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$update -> AddDBParam('-recid',$update_row_ID);
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
	if($searchData['c_leave_hrs_type_code'][0] == 'L'){

			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY	
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
		
			if($current_day != $previous_day){
			$total_lv_hrs = $searchData['c_aggregate_lv_hrs'][0] + $recordData6["$hrs_fieldname"][0];// ADD LV HRS FOR THIS DAY
			$update -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			$previous_day = $current_day; // RESET PREVIOUS DAY
			}
		
		}
	
	
	}
	
	$updateResult = $update -> FMEdit();

	//echo  '<p>errorCode: '.$updateResult['errorCode'];
	//echo  '<p>foundCount: '.$updateResult['foundCount'];
	//exit;
	
	
} else { // IF THERE IS NO EXISTING LEAVE W/O PAY LEAVE ROW ON THIS TIMESHEET

	$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$newrecord -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$newrecord -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$newrecord -> AddDBParam('Timesheet_ID',$timesheet_ID);
	$newrecord -> AddDBParam('HrsType','UnPdLv');
	$newrecord -> AddDBParam('LvType','Leave w/o Pay');
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
		if($searchData['c_leave_hrs_type_code'][0] == 'L'){
		
			if($searchData['c_leave_hrs_day_num'][0] == $current_day){
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			} else {
			$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			//$last_field = $hrs_fieldname;
			
			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY TO NEXT LV HRS DAY
			$total_lv_hrs = 0; // RESET LV HRS COUNTER
			
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			
			}
		
		}
	
	
	//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
	//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
	}
	$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME -- LAST ROW IF FOUNDCOUNT > 1 OR FIRST ROW IF FOUNDCOUNT = 1
	
	
	$newrecordResult = $newrecord -> FMNew();
	
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	//exit;

}

}
#################################################################
## END: ADD LEAVE W/O PAY HRS TO TIMESHEET ##
#################################################################
$previous_day = '';
#################################################################
## START: ADD PERSONAL HRS TO TIMESHEET ##
#################################################################
if($total_request_hrs_p > 0){
//echo '<p>$total_request_hrs_p: '.$total_request_hrs_p;
	###############################################################################################
	### START: FIND EXISTING TIME_HRS ROWS FOR THIS TIMESHEET, THIS PAY PERIOD, THIS LEAVE TYPE ###
	###############################################################################################
	$search4 = new FX($serverIP,$webCompanionPort);
	$search4 -> SetDBData('SIMS_2.fp7','time_hrs');
	$search4 -> SetDBPassword($webPW,$webUN);
	$search4 -> AddDBParam('Timesheet_ID','=='.$timesheet_ID);
	$search4 -> AddDBParam('HrsType','PdLv');
	$search4 -> AddDBParam('LvType','Personal Holiday');
	
	$searchResult4 = $search4 -> FMFind();
	
	//echo $searchResult4['errorCode'];
	//echo $searchResult4['foundCount'];
	if($searchResult4['foundCount'] == 1){ // IF THERE IS ALREADY A PERSONAL LEAVE ROW ON THIS TIMESHEET
	
	$recordData4 = current($searchResult4['data']);
	$update_row_ID = $recordData4['c_cwp_row_ID'][0];// GRAB THE ROW ID FOR UPDATING
	}
	########################################################################################
	### END: FIND EXISTING TIME_HRS ROWS FOR THIS USER, THIS PAY PERIOD, THIS LEAVE TYPE ###
	########################################################################################

if($searchResult4['foundCount'] == 1){ // IF THERE IS ALREADY A PERSONAL LEAVE ROW ON THIS TIMESHEET

	$update = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$update -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$update -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$update -> AddDBParam('-recid',$update_row_ID);
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
	if($searchData['c_leave_hrs_type_code'][0] == 'P'){

			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY	
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
		
			if($current_day != $previous_day){
			$total_lv_hrs = $searchData['c_aggregate_lv_hrs'][0] + $recordData4["$hrs_fieldname"][0];// ADD LV HRS FOR THIS DAY
			$update -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			$previous_day = $current_day; // RESET PREVIOUS DAY
			}
		
		}
	
	
	}
	
	$updateResult = $update -> FMEdit();

	//echo  '<p>errorCode: '.$updateResult['errorCode'];
	//echo  '<p>foundCount: '.$updateResult['foundCount'];
	//exit;
	
	
} else { // IF THERE IS NO EXISTING PERSONAL LEAVE ROW ON THIS TIMESHEET

	$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$newrecord -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$newrecord -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$newrecord -> AddDBParam('Timesheet_ID',$timesheet_ID);
	$newrecord -> AddDBParam('HrsType','PdLv');
	$newrecord -> AddDBParam('LvType','Personal Holiday');
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
		if($searchData['c_leave_hrs_type_code'][0] == 'P'){
		
			if($searchData['c_leave_hrs_day_num'][0] == $current_day){
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			} else {
			$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			//$last_field = $hrs_fieldname;
			
			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY TO NEXT LV HRS DAY
			$total_lv_hrs = 0; // RESET LV HRS COUNTER
			
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			
			}
		
		}
	
	
	//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
	//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
	}
	$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME -- LAST ROW IF FOUNDCOUNT > 1 OR FIRST ROW IF FOUNDCOUNT = 1
	
	
	$newrecordResult = $newrecord -> FMNew();
	
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	//exit;

}

}
#################################################################
## END: ADD PERSONAL HRS TO TIMESHEET ##
#################################################################
$previous_day = '';
#################################################################
## START: ADD SICK HRS TO TIMESHEET ##
#################################################################
if($total_request_hrs_s > 0){

	###############################################################################################
	### START: FIND EXISTING TIME_HRS ROWS FOR THIS TIMESHEET, THIS PAY PERIOD, THIS LEAVE TYPE ###
	###############################################################################################
	$search2 = new FX($serverIP,$webCompanionPort);
	$search2 -> SetDBData('SIMS_2.fp7','time_hrs');
	$search2 -> SetDBPassword($webPW,$webUN);
	$search2 -> AddDBParam('Timesheet_ID','=='.$timesheet_ID);
	$search2 -> AddDBParam('HrsType','PdLv');
	$search2 -> AddDBParam('LvType','Sick');
	
	$searchResult2 = $search2 -> FMFind();
	
	echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
	echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
	if($searchResult2['foundCount'] == 1){ // IF THERE IS ALREADY A SICK LEAVE ROW ON THIS TIMESHEET
	
	$recordData2 = current($searchResult2['data']);
	$update_row_ID = $recordData2['c_cwp_row_ID'][0];// GRAB THE ROW ID FOR UPDATING
	}
	########################################################################################
	### END: FIND EXISTING TIME_HRS ROWS FOR THIS USER, THIS PAY PERIOD, THIS LEAVE TYPE ###
	########################################################################################

if($searchResult2['foundCount'] == 1){ // IF THERE IS ALREADY A SICK LEAVE ROW ON THIS TIMESHEET

	$update = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$update -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$update -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$update -> AddDBParam('-recid',$update_row_ID);
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
	if($searchData['c_leave_hrs_type_code'][0] == 'S'){

			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY	
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
		
			if($current_day != $previous_day){
			$total_lv_hrs = $searchData['c_aggregate_lv_hrs'][0] + $recordData2["$hrs_fieldname"][0];// ADD LV HRS FOR THIS DAY
			$update -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			$previous_day = $current_day; // RESET PREVIOUS DAY
			}
		
		}
	
	
	//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
	//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
	}
	
	$updateResult = $update -> FMEdit();
	
	//echo  '<p>errorCode: '.$updateResult['errorCode'];
	//echo  '<p>foundCount: '.$updateResult['foundCount'];
	//print_r($updateResult);
	//exit;
	
} else { // IF THERE IS NO EXISTING SICK LEAVE ROW ON THIS TIMESHEET

	$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$newrecord -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$newrecord -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$newrecord -> AddDBParam('Timesheet_ID',$timesheet_ID);
	$newrecord -> AddDBParam('HrsType','PdLv');
	$newrecord -> AddDBParam('LvType','Sick');
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
		if($searchData['c_leave_hrs_type_code'][0] == 'S'){
		
			if($searchData['c_leave_hrs_day_num'][0] == $current_day){
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			} else {
			$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			//$last_field = $hrs_fieldname;
			
			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY TO NEXT LV HRS DAY
			$total_lv_hrs = 0; // RESET LV HRS COUNTER
			
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			
			}
		
		}
	
	
	//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
	//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
	}
	$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME -- LAST ROW IF FOUNDCOUNT > 1 OR FIRST ROW IF FOUNDCOUNT = 1
	
	
	$newrecordResult = $newrecord -> FMNew();
	
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	echo '<p>$newrecordResult<p>';
	print_r($newrecordResult);
	//exit;

}

}
#################################################################
## END: ADD SICK HRS TO TIMESHEET ##
#################################################################
$previous_day = '';
#################################################################
## START: ADD VACATION HRS TO TIMESHEET ##
#################################################################
if($total_request_hrs_v > 0){

	###############################################################################################
	### START: FIND EXISTING TIME_HRS ROWS FOR THIS TIMESHEET, THIS PAY PERIOD, THIS LEAVE TYPE ###
	###############################################################################################
	$search3 = new FX($serverIP,$webCompanionPort);
	$search3 -> SetDBData('SIMS_2.fp7','time_hrs');
	$search3 -> SetDBPassword($webPW,$webUN);
	$search3 -> AddDBParam('Timesheet_ID','=='.$timesheet_ID);
	$search3 -> AddDBParam('HrsType','PdLv');
	$search3 -> AddDBParam('LvType','Vacation');
	
	$searchResult3 = $search3 -> FMFind();
	
	//echo $searchResult3['errorCode'];
	//echo $searchResult3['foundCount'];
	if($searchResult3['foundCount'] == 1){ // IF THERE IS ALREADY A VACATION LEAVE ROW ON THIS TIMESHEET
	
	$recordData3 = current($searchResult3['data']);
	$update_row_ID = $recordData3['c_cwp_row_ID'][0];// GRAB THE ROW ID FOR UPDATING
	}
	########################################################################################
	### END: FIND EXISTING TIME_HRS ROWS FOR THIS USER, THIS PAY PERIOD, THIS LEAVE TYPE ###
	########################################################################################

if($searchResult3['foundCount'] == 1){ // IF THERE IS ALREADY A VACATION LEAVE ROW ON THIS TIMESHEET

	$update = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$update -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$update -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$update -> AddDBParam('-recid',$update_row_ID);
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
	if($searchData['c_leave_hrs_type_code'][0] == 'V'){

			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY	
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
		
			if($current_day != $previous_day){
			$total_lv_hrs = $searchData['c_aggregate_lv_hrs'][0] + $recordData3["$hrs_fieldname"][0];// ADD LV HRS FOR THIS DAY
			$update -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			$previous_day = $current_day; // RESET PREVIOUS DAY
			}
		
		}
	
	
	}
	
	$updateResult = $update -> FMEdit();

	//echo  '<p>errorCode: '.$updateResult['errorCode'];
	//echo  '<p>foundCount: '.$updateResult['foundCount'];
	//exit;
	
	
} else { // IF THERE IS NO EXISTING VACATION LEAVE ROW ON THIS TIMESHEET

	$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
	$newrecord -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
	$newrecord -> SetDBPassword($webPW,$webUN); //set password information
	
	
	###ADD THE SUBMITTED VALUES AS PARAMETERS###
	$newrecord -> AddDBParam('Timesheet_ID',$timesheet_ID);
	$newrecord -> AddDBParam('HrsType','PdLv');
	$newrecord -> AddDBParam('LvType','Vacation');
	
	
	foreach($searchResult['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
	
		if($searchData['c_leave_hrs_type_code'][0] == 'V'){
		
			if($searchData['c_leave_hrs_day_num'][0] == $current_day){
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			} else {
			$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			//$last_field = $hrs_fieldname;
			
			$current_day = $searchData['c_leave_hrs_day_num'][0]; // SET CURRENT DAY TO NEXT LV HRS DAY
			$total_lv_hrs = 0; // RESET LV HRS COUNTER
			
			$total_lv_hrs = $total_lv_hrs + $searchData['leave_num_hrs'][0];// ADD LV HRS FOR THIS DAY
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			
			}
		
		}
	
	
	//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
	//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
	}
	$newrecord -> AddDBParam("$hrs_fieldname",$total_lv_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME -- LAST ROW IF FOUNDCOUNT > 1 OR FIRST ROW IF FOUNDCOUNT = 1
	
	
	$newrecordResult = $newrecord -> FMNew();
	
	//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
	//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
	//exit;

}

}
#################################################################
## END: ADD VACATION HRS TO TIMESHEET ##
#################################################################




if($debug == 'on'){

echo '<p>$total_request_hrs_s: '.$total_request_hrs_s;
echo '<p>$total_request_hrs_v: '.$total_request_hrs_v;
echo '<p>$total_request_hrs_p: '.$total_request_hrs_p;
echo '<p>$total_request_hrs_f: '.$total_request_hrs_f;
echo '<p>$total_request_hrs_j: '.$total_request_hrs_j;
echo '<p>$total_request_hrs_l: '.$total_request_hrs_l;

}

if($src != 'new_ts'){
$header_url = 'Location: http://www.sedl.org/staff/sims/timesheets.php?Timesheet_ID='.$timesheet_ID.'&action=view&src=menu&payperiod='.$pay_period;


//header("$header_url");
exit;
}
?>

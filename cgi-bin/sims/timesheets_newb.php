<?php
session_start();

include_once('FX/FX.php');
include_once('FX/server_data.php');

$debug = 'off'; //change to "on" to print variable values at top of table

####################################
## START: RESET SESSION VARIABLES ##
####################################
$_SESSION['signer_status_owner'] = '';
$_SESSION['signer_status_imm_spvsr'] = '';
$_SESSION['signer_status_pba'] = '';
$_SESSION['signer_status_bgt_auth_1'] = '';
$_SESSION['signer_status_bgt_auth_2'] = '';
$_SESSION['signer_status_bgt_auth_3'] = '';
$_SESSION['signer_status_bgt_auth_4'] = '';
$_SESSION['signer_status_bgt_auth_5'] = '';
$_SESSION['signer_status_bgt_auth_6'] = '';
$_SESSION['signer_status_bgt_auth_7'] = '';
$_SESSION['signer_status_bgt_auth_8'] = '';
$_SESSION['signer_status_bgt_auth_OT'] = '';
$_SESSION['approved_by_auth_rep_status'] = '';

$_SESSION['signer_ID_bgt_auth_1'] = '';
$_SESSION['signer_ID_bgt_auth_2'] = '';
$_SESSION['signer_ID_bgt_auth_3'] = '';
$_SESSION['signer_ID_bgt_auth_4'] = '';
$_SESSION['signer_ID_bgt_auth_5'] = '';
$_SESSION['signer_ID_bgt_auth_6'] = '';
$_SESSION['signer_ID_bgt_auth_7'] = '';
$_SESSION['signer_ID_bgt_auth_8'] = '';
$_SESSION['signer_ID_bgt_auth_OT'] = '';
$_SESSION['signer_ID_owner'] = '';
$_SESSION['signer_ID_imm_spvsr'] = '';
$_SESSION['signer_ID_pba'] = '';
$_SESSION['approved_by_auth_rep'] = '';
$_SESSION['timesheet_status'] = '';
##################################
## END: RESET SESSION VARIABLES ##
##################################

#################################################
## START: GRAB VARIABLES FROM THE TIMESHEET FORM
#################################################
$staff_ID = $_SESSION['staff_ID'];
$last_pay_period_end = $_SESSION['last_pay_period_end'];
$last_pay_period_end_m = $_SESSION['last_pay_period_end_m'];
$last_pay_period_end_d = $_SESSION['last_pay_period_end_d'];
$last_pay_period_end_y = $_SESSION['last_pay_period_end_y'];

$this_m = date("m");
$this_d = date("d");
$this_y = date("Y");
$next_m = $this_m + 1;

if(($_SESSION['timesheet_foundcount'] == 0) && ($_SESSION['employee_type'] == 'Exempt')){
//echo '1';
	$new_pay_period = date("m/d/Y", mktime(0,0,0,$this_m,1,$this_y));
	$days_in_month = date("t", mktime(0,0,0,$this_m,1,$this_y));

}elseif(($_SESSION['timesheet_foundcount'] == 0) && ($_SESSION['employee_type'] != 'Exempt')){
//echo '2';
	if($this_d > 15){
	$new_pay_period = date("m/d/Y", mktime(0,0,0,$this_m,16,$this_y));
	$days_in_month = date("t", mktime(0,0,0,$this_m,16,$this_y));
	}else{
	$new_pay_period = date("m/d/Y", mktime(0,0,0,$this_m,1,$this_y));
	$days_in_month = date("t", mktime(0,0,0,$this_m,1,$this_y));
	}

}else{
//echo '3';
	$new_pay_period = date("m/d/Y", mktime(0,0,0,$last_pay_period_end_m,$last_pay_period_end_d + 1,$last_pay_period_end_y));
	$days_in_month = date("t", mktime(0,0,0,$last_pay_period_end_m,$last_pay_period_end_d + 1,$last_pay_period_end_y));
}
//exit;

//$day_of_month = date("d",$pay_period_end);
//$month = date("n",$pay_period_end);
//$year = date("Y",$pay_period_end);



//$days_in_month = cal_days_in_month(CAL_GREGORIAN, $last_pay_period_end_m + 1, $last_pay_period_end_y);
$_SESSION['days_in_month'] = $days_in_month;
$header_colspan = $days_in_month + 3;

//echo '<br>Days in Month: '.$days_in_month;
//echo '<br>Header colspan: '.$header_colspan;
//echo '<br>StaffID: '.$staff_ID;
//echo '<br>LastPayPeriodEnd: '.$last_pay_period_end;
//echo '<br>Month: '.$last_pay_period_end_m;
//echo '<br>Day: '.$last_pay_period_end_d;
//echo '<br>Year: '.$last_pay_period_end_y;
//echo '<br>NewPayPeriodBegin: '.$new_pay_period;
#################################################
## END: GRAB VARIABLES FROM THE TIMESHEET FORM
#################################################

#################################################
## START: CREATE A NEW TIMESHEET RECORD
#################################################
$newrecord = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord -> SetDBData('SIMS_2.fp7','timesheets'); //set dbase information
$newrecord -> SetDBPassword($webPW,$webUN); //set password information
$newrecord -> AddDBParam('staff_ID',$staff_ID);
$newrecord -> AddDBParam('PayPeriodBegin',$new_pay_period);
//$newrecord -> AddDBParam('HrsType','WkHrsReg');

$newrecordResult = $newrecord -> FMNew();

$recordData = current($newrecordResult['data']);

$timesheet_ID = $recordData['TimesheetID'][0];
$timesheet_row_ID = $recordData['c_row_ID_cwp'][0];

$_SESSION['timesheet_ID'] = $timesheet_ID;

//echo '<br>New Timesheet ID: '.$timesheet_ID;
//echo '<br>New Record ErrorCode: '.$newrecordResult['errorCode'];
//echo '<br>FoundCount: '.$newrecordResult['foundCount'];
$_SESSION['last_pay_period_end'] = $recordData['c_last_pay_period'][0];
$pay_period_end_d = $recordData['c_PayPeriodEnd_d'][0];
$pay_period_end_m = $recordData['c_PayPeriodEnd_m'][0];
$pay_period_end_y = $recordData['c_PayPeriodEnd_y'][0];
$pay_period_end = $recordData['c_PayPeriodEnd'][0];
$pay_period_begin = $recordData['PayPeriodBegin'][0];

$pay_period_begin_d = $recordData['c_PayPeriodBegin_d'][0];

$new_pay_period_monthkey = $recordData['c_payperiod_month_yr_key'][0];
//echo '<br>New Pay Period Monthkey: '.$new_pay_period_monthkey;

$_SESSION['current_pay_period_end'] = $recordData['c_PayPeriodEnd'][0];
$_SESSION['signer_ID_imm_spvsr'] = $recordData['Signer_ID_imm_spvsr'][0];
$_SESSION['signer_ID_owner'] = $recordData['Signer_ID_owner'][0];
$_SESSION['signer_ID_pba'] = $recordData['Signer_ID_pba'][0];
$_SESSION['timesheet_prefs_hide_weekends'] = $recordData['staff::timesheet_prefs_hide_weekends'][0];
#################################################
## END: CREATE A NEW TIMESHEET RECORD
#################################################

// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['user_ID']);
$newrecord -> AddDBParam('action','CREATE_TIMESHEET');
$newrecord -> AddDBParam('table','TIMESHEETS');
$newrecord -> AddDBParam('object_ID',$timesheet_ID);
$newrecord -> AddDBParam('affected_row_ID',$timesheet_row_ID);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

//echo '<p>foo';

#####################################################
## START: FIND PAY PERIOD INFO FOR THIS NEW TIMESHEET
#####################################################
//echo $row_ID;
$search6 = new FX($serverIP,$webCompanionPort);
$search6 -> SetDBData('SIMS_2.fp7','timesheet_pay_periods','all');
$search6 -> SetDBPassword($webPW,$webUN);
$search6 -> AddDBParam('c_pay_period_month_yr_key',$pay_period_end_m.'.'.$pay_period_end_y);
//$search6 -> AddDBParam('-lop','or');

//$search6 -> AddSortParam ($sortfield,'descend');


$searchResult6 = $search6 -> FMFind();

//echo $searchResult6['errorCode'];
//echo '<br>PayPeriod FoundCount: '.$searchResult6['foundCount'];
//print_r ($searchResult6);
$recordData6 = current($searchResult6['data']);

## SET PAY PERIOD SESSION VARIABLES
if($_SESSION['employee_type'] == 'Exempt'){

$_SESSION['payperiod_workhrs'] = $recordData6['c_num_workhrs_exempt'][0] * $_SESSION['timesheet_owner_FTE_status'];

} elseif(($_SESSION['employee_type'] != 'Exempt') && ($pay_period_end_d == '15')){

$_SESSION['payperiod_workhrs'] = $recordData6['c_num_workhrs_non_exempt_1'][0] * $_SESSION['timesheet_owner_FTE_status'];
$_SESSION['pay_period_num'] = '1';

} elseif(($_SESSION['employee_type'] != 'Exempt') && ($pay_period_end_d != '15')){

$_SESSION['payperiod_workhrs'] = $recordData6['c_num_workhrs_non_exempt_2'][0] * $_SESSION['timesheet_owner_FTE_status'];
$_SESSION['pay_period_num'] = '2';

} else {

$_SESSION['payperiod_workhrs'] = 'Error_900';

}
#####################################################
## END: FIND PAY PERIOD INFO FOR THIS TIMESHEET
#####################################################

#################################################################
## START: CREATE THE FIRST TIME-HRS ROW FOR REGULAR HOURS
#################################################################
$newrecord2 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord2 -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
$newrecord2 -> SetDBPassword($webPW,$webUN); //set password information


###ADD THE SUBMITTED VALUES AS PARAMETERS###
$newrecord2 -> AddDBParam('Timesheet_ID',$timesheet_ID);
$newrecord2 -> AddDBParam('HrsType','WkHrsReg');

###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
$newrecordResult2 = $newrecord2 -> FMNew();

$recordData2 = current($newrecordResult2['data']);

$new_row_ID = $recordData2['c_cwp_row_ID'][0];

//echo '<br>TimeHrs ErrorCode: '.$newrecordResult2['errorCode'];
//echo '<br>FoundCount: '.$newrecordResult2['foundCount'];
#################################################################
## END: CREATE THE FIRST TIME-HRS ROW FOR REGULAR HOURS
#################################################################

if($_SESSION['employee_type'] != 'Hourly'){ //HOURLY STAFF DON'T HAVE PAID LEAVE, UNPAID LEAVE, OR OVERTIME HRS
#################################################################################################
## START: FIND ANY SEDL HOLIDAYS FOR THE NEW PAY PERIOD MONTH (FOR EXEMPT OR NON-EXEMPT STAFF) ##
#################################################################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','SEDL_holidays','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('c_HolidayMonthYrKey',$new_pay_period_monthkey);
$search -> AddDBParam('HolidayDate',$recordData['PayPeriodBegin'][0].'...'.$recordData['c_PayPeriodEnd'][0]);
//$search -> AddDBParam('HolidayDate',$recordData['c_PayPeriodEnd'][0],'lte');


//$search -> AddSortParam ($sortfield,'descend');


$searchResult = $search -> FMFind();

//echo '<br>SEDL HolidaySearch ErrorCode: '.$searchResult['errorCode'];
//echo '<br>SEDL HolidaySearch Foundcount: '.$searchResult['foundCount'];
//print_r ($searchResult);
$searchData = current($searchResult['data']);
//$_SESSION['user_bgt_codes'] = $searchData;
###############################################################################################
## END: FIND ANY SEDL HOLIDAYS FOR THE NEW PAY PERIOD MONTH (FOR EXEMPT OR NON-EXEMPT STAFF) ##
###############################################################################################

##############################################################################
## START: IF HOLIDAYS EXIST FOR THE NEW TIMESHEET, CREATE PAID LV HOLIDAY ROW
##############################################################################
if($searchResult['foundCount'] > 0){

$fte_hrs = $_SESSION['employee_FTE_status'] * 8;
/*
if( //CHECK IF ANY OF THE FOUND HOLIDAYS FALL WITHIN THE CURRENT PAY PERIOD OR NOT - VALID FOR UP TO 4 HOLIDAYS IN A GIVEN MONTH
($searchData['c_HolidayDay_numeric'][0] >= $pay_period_begin_d) && ($searchData['c_HolidayDay_numeric'][0] <= $pay_period_end_d) ||
($searchData['c_HolidayDay_numeric'][1] >= $pay_period_begin_d) && ($searchData['c_HolidayDay_numeric'][1] <= $pay_period_end_d) ||
($searchData['c_HolidayDay_numeric'][2] >= $pay_period_begin_d) && ($searchData['c_HolidayDay_numeric'][2] <= $pay_period_end_d) ||
($searchData['c_HolidayDay_numeric'][3] >= $pay_period_begin_d) && ($searchData['c_HolidayDay_numeric'][3] <= $pay_period_end_d) 

){
*/



$newrecord3 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
$newrecord3 -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
$newrecord3 -> SetDBPassword($webPW,$webUN); //set password information


###ADD THE SUBMITTED VALUES AS PARAMETERS###
$newrecord3 -> AddDBParam('Timesheet_ID',$timesheet_ID);
$newrecord3 -> AddDBParam('HrsType','PdLv');
$newrecord3 -> AddDBParam('LvType','Holiday');

//$i=0;
foreach($searchResult['data'] as $key => $searchData5) { //searchResult -> fmp table = SEDL_holidays 

//if(($searchData5['c_HolidayDay_numeric'][0] >= $pay_period_begin_d) && ($searchData5['c_HolidayDay_numeric'][0] <= $pay_period_end_d)){ //IF THE CURRENT HOLIDAY FALLS WITHIN THE CURRENT PAY PERIOD
$holiday_days .= $searchData5['c_HolidayDayOfMonth'][0].','; //COLLECT HOLIDAY DAYS FOR DEBUG
$hrs_fieldname = 'Hrs'.$searchData5['c_HolidayDayOfMonth'][0]; //SET FIELDNAME FOR FMP PARAM
//echo '<br>Holiday Hrs Fieldname '.$i.': '.$hrs_fieldname;
$newrecord3 -> AddDBParam("$hrs_fieldname",$fte_hrs); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
//$i++;

//}
}



###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMNew() FUNCTION###
$newrecordResult3 = $newrecord3 -> FMNew();

$recordData3 = current($newrecordResult3['data']);

$new_row_ID3 = $recordData3['c_cwp_row_ID'][0];

//echo '<br>NewHolidayRecord ErrorCode: '.$newrecordResult3['errorCode'];
//echo '<br>NewHolidayRecord FoundCount: '.$newrecordResult3['foundCount'];
//}
}
##############################################################################
## END: IF HOLIDAYS EXIST FOR THE NEW TIMESHEET, CREATE PAID LV HOLIDAY ROW
##############################################################################

#####################################################################################
## START: IF LEAVE REQUESTS EXIST FOR THE NEW TIMESHEET PAY PERIOD, CREATE LV ROWS
#####################################################################################
// FIND LEAVE REQUESTS FOR NEW TIMESHEET'S PAY PERIOD
$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','leave_requests2');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('pay_period_end',$_SESSION['current_pay_period_end']);
$search2 -> AddDBParam('staff_ID','=='.$_SESSION['staff_ID']);

//$search2 -> AddSortParam('leave_hrs_date','ascend');

$searchResult2 = $search2 -> FMFind();

//echo '<p>$searchResult2[errorCode]: '.$searchResult2['errorCode'];
//echo '<p>$searchResult2[foundCount]: '.$searchResult2['foundCount'];
$recordData2 = current($searchResult2['data']);



//exit;
if($searchResult2['foundCount'] > 0){

$i=1;
$s=1;
$v=1;
$p=1;
$f=1;
$j=1;
$l=1;

foreach($searchResult2['data'] as $key => $searchData2) { // LOOP THROUGH FOUND SET OF LEAVE REQUESTS FOR THIS TIMESHEET
		

			$leave_request_row_ID = $searchData2['c_row_ID_cwp'][0];
			$pay_period = $_SESSION['current_pay_period_end'];
			$leave_request_ID = $searchData2['leave_request_ID'][0];

			// UPDATE LEAVE REQUEST WITH NEW TIMESHEET ID
			
			$update = new FX($serverIP,$webCompanionPort);
			$update -> SetDBData('SIMS_2.fp7','leave_requests2');
			$update -> SetDBPassword($webPW,$webUN);
			$update -> AddDBParam('-recid',$leave_request_row_ID);
			$update -> AddDBParam('timesheet_ID',$timesheet_ID);
			
			$updateResult = $update -> FMEdit();
			
			#################################################################
			## START: FIND LEAVE REQUEST HRS RELATED TO THIS LEAVE REQUEST ##
			#################################################################
			$search6 = new FX($serverIP,$webCompanionPort);
			$search6 -> SetDBData('SIMS_2.fp7','leave_request_hrs');
			$search6 -> SetDBPassword($webPW,$webUN);
			$search6 -> AddDBParam('leave_request_ID','=='.$leave_request_ID);
			//$search6 -> AddDBParam('-lop','or');
			
			$search6 -> AddSortParam('leave_hrs_date','ascend');
			
			
			$searchResult6 = $search6 -> FMFind();
			
			//echo '<p>$searchResult6[errorCode]: '.$searchResult6['errorCode'];
			//echo '<p>$searchResult6[foundCount]: '.$searchResult6['foundCount'];
			//print_r ($searchResult6);
			$recordData6 = current($searchResult6['data']);
			//$d = $recordData['leave_requests::c_pay_period_begin_d'][0];
			$total_request_hrs_s = $recordData6['c_total_request_hrs_s'][0];
			$total_request_hrs_v = $recordData6['c_total_request_hrs_v'][0];
			$total_request_hrs_p = $recordData6['c_total_request_hrs_p'][0];
			$total_request_hrs_f = $recordData6['c_total_request_hrs_f'][0];
			$total_request_hrs_j = $recordData6['c_total_request_hrs_j'][0];
			$total_request_hrs_l = $recordData6['c_total_request_hrs_l'][0];
			
			###############################################################
			## END: FIND LEAVE REQUEST HRS RELATED TO THIS LEAVE REQUEST ##
			###############################################################
			
			#################################################################
			## START: ADD SICK HRS TO TIMESHEET ##
			#################################################################
			if($total_request_hrs_s > 0){ //IF THERE ARE SICK HOURS ON THIS LV REQUEST
			
					if($s > 1){ //IF THIS IS NOT THE FIRST ITERATION THROUGH THE LV REQUEST HRS
		
						$update2 = new FX($serverIP,$webCompanionPort);
						$update2 -> SetDBData('SIMS_2.fp7','time_hrs');
						$update2 -> SetDBPassword($webPW,$webUN);
						$update2 -> AddDBParam('-recid',$_SESSION['time_hrs_row_ID_s']);
			
								foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
									if($searchData['c_leave_hrs_type_code'][0] == 'S'){
									$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
									$update2 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
									}
								}
						
						$updateResult2 = $update2 -> FMEdit();
					
					} else { //IF THIS IS THE FIRST ITERATION THROUGH THE LV REQUEST HRS
					
						$newrecord4 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
						$newrecord4 -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
						$newrecord4 -> SetDBPassword($webPW,$webUN); //set password information
						
						
						###ADD THE SUBMITTED VALUES AS PARAMETERS###
						$newrecord4 -> AddDBParam('Timesheet_ID',$timesheet_ID);
						$newrecord4 -> AddDBParam('HrsType','PdLv');
						$newrecord4 -> AddDBParam('LvType','Sick');
						
								foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
								
									if($searchData['c_leave_hrs_type_code'][0] == 'S'){
									$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
									$newrecord4 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
									}
								
								//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
								//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
								}
						
						$newrecordResult4 = $newrecord4 -> FMNew();
						
						$recordData4 = current($newrecordResult4['data']);
			
						$_SESSION['time_hrs_row_ID_s'] = $recordData4['c_cwp_row_ID'][0];
			
						//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
						//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
					}
			$s++;		
			}
			#################################################################
			## END: ADD SICK HRS TO TIMESHEET ##
			#################################################################
			
			#################################################################
			## START: ADD VACATION HRS TO TIMESHEET ##
			#################################################################
			if($total_request_hrs_v > 0){
			
			if($v > 1){ //IF THIS IS NOT THE FIRST ITERATION THROUGH THE LV REQUEST HRS

			$update3 = new FX($serverIP,$webCompanionPort);
			$update3 -> SetDBData('SIMS_2.fp7','time_hrs');
			$update3 -> SetDBPassword($webPW,$webUN);
			$update3 -> AddDBParam('-recid',$_SESSION['time_hrs_row_ID_v']);

			foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
				if($searchData['c_leave_hrs_type_code'][0] == 'V'){
				$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
				$update3 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
				}
			}
			
			$updateResult3 = $update3 -> FMEdit();
			
			} else { //IF THIS IS THE FIRST ITERATION THROUGH THE LV REQUEST HRS

			
			$newrecord5 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
			$newrecord5 -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
			$newrecord5 -> SetDBPassword($webPW,$webUN); //set password information
			
			
			###ADD THE SUBMITTED VALUES AS PARAMETERS###
			$newrecord5 -> AddDBParam('Timesheet_ID',$timesheet_ID);
			$newrecord5 -> AddDBParam('HrsType','PdLv');
			$newrecord5 -> AddDBParam('LvType','Vacation');
			
			foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
			
			if($searchData['c_leave_hrs_type_code'][0] == 'V'){
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			$newrecord5 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			}
			
			//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
			//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
			}
			
			$newrecordResult5 = $newrecord5 -> FMNew();

			$recordData5 = current($newrecordResult5['data']);

			$_SESSION['time_hrs_row_ID_v'] = $recordData5['c_cwp_row_ID'][0];
			
			}
			$v++;
			}
			#################################################################
			## END: ADD VACATION HRS TO TIMESHEET ##
			#################################################################
			
			#################################################################
			## START: ADD PERSONAL HRS TO TIMESHEET ##
			#################################################################
			if($total_request_hrs_p > 0){
			
			if($p > 1){

			$update4 = new FX($serverIP,$webCompanionPort);
			$update4 -> SetDBData('SIMS_2.fp7','time_hrs');
			$update4 -> SetDBPassword($webPW,$webUN);
			$update4 -> AddDBParam('-recid',$_SESSION['time_hrs_row_ID_p']);

			foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
				if($searchData['c_leave_hrs_type_code'][0] == 'P'){
				$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
				$update4 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
				}
			}
			
			$updateResult4 = $update4 -> FMEdit();
			
			} else {
			
			
			$newrecord6 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
			$newrecord6 -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
			$newrecord6 -> SetDBPassword($webPW,$webUN); //set password information
			
			
			###ADD THE SUBMITTED VALUES AS PARAMETERS###
			$newrecord6 -> AddDBParam('Timesheet_ID',$timesheet_ID);
			$newrecord6 -> AddDBParam('HrsType','PdLv');
			$newrecord6 -> AddDBParam('LvType','Personal Holiday');
			
			foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
			
			if($searchData['c_leave_hrs_type_code'][0] == 'P'){
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			$newrecord6 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			}
			
			//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
			//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
			}
			
			$newrecordResult6 = $newrecord6 -> FMNew();
			
			$recordData6 = current($newrecordResult6['data']);

			$_SESSION['time_hrs_row_ID_p'] = $recordData6['c_cwp_row_ID'][0];
			
			}
			$p++;
			}
			#################################################################
			## END: ADD PERSONAL HRS TO TIMESHEET ##
			#################################################################
			
			#################################################################
			## START: ADD FAMILY/MEDICAL HRS TO TIMESHEET ##
			#################################################################
			if($total_request_hrs_f > 0){
			
			if($f > 1){

			$update5 = new FX($serverIP,$webCompanionPort);
			$update5 -> SetDBData('SIMS_2.fp7','time_hrs');
			$update5 -> SetDBPassword($webPW,$webUN);
			$update5 -> AddDBParam('-recid',$_SESSION['time_hrs_row_ID_f']);

			foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
				if($searchData['c_leave_hrs_type_code'][0] == 'F'){
				$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
				$update5 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
				}
			}
			
			$updateResult5 = $update5 -> FMEdit();
			
			} else {

			$newrecord7 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
			$newrecord7 -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
			$newrecord7 -> SetDBPassword($webPW,$webUN); //set password information
			
			
			###ADD THE SUBMITTED VALUES AS PARAMETERS###
			$newrecord7 -> AddDBParam('Timesheet_ID',$timesheet_ID);
			$newrecord7 -> AddDBParam('HrsType','UnPdLv');
			$newrecord7 -> AddDBParam('LvType','Family & Medical');
			
			foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
			
			if($searchData['c_leave_hrs_type_code'][0] == 'F'){
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			$newrecord7 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			}
			
			//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
			//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
			}
			
			$newrecordResult7 = $newrecord7 -> FMNew();

			$recordData7 = current($newrecordResult7['data']);

			$_SESSION['time_hrs_row_ID_f'] = $recordData7['c_cwp_row_ID'][0];
			
			}
			$f++;
			}
			#################################################################
			## END: ADD FAMILY/MEDICAL HRS TO TIMESHEET ##
			#################################################################
			
			#################################################################
			## START: ADD LEAVE W/O PAY HRS TO TIMESHEET ##
			#################################################################
			if($total_request_hrs_l > 0){
			
			if($l > 1){

			$update6 = new FX($serverIP,$webCompanionPort);
			$update6 -> SetDBData('SIMS_2.fp7','time_hrs');
			$update6 -> SetDBPassword($webPW,$webUN);
			$update6 -> AddDBParam('-recid',$_SESSION['time_hrs_row_ID_l']);

			foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
				if($searchData['c_leave_hrs_type_code'][0] == 'L'){
				$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
				$update6 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
				}
			}
			
			$updateResult6 = $update6 -> FMEdit();
			
			} else {


			$newrecord8 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
			$newrecord8 -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
			$newrecord8 -> SetDBPassword($webPW,$webUN); //set password information
			
			
			###ADD THE SUBMITTED VALUES AS PARAMETERS###
			$newrecord8 -> AddDBParam('Timesheet_ID',$timesheet_ID);
			$newrecord8 -> AddDBParam('HrsType','UnPdLv');
			$newrecord8 -> AddDBParam('LvType','Leave w/o Pay');
			
			foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
			
			if($searchData['c_leave_hrs_type_code'][0] == 'L'){
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			$newrecord8 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			}
			
			//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
			//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
			}
			
			$newrecordResult8 = $newrecord8 -> FMNew();
			
			$recordData8 = current($newrecordResult8['data']);

			$_SESSION['time_hrs_row_ID_l'] = $recordData8['c_cwp_row_ID'][0];
			
			}
			$l++;
			}
			#################################################################
			## END: ADD LEAVE W/O PAY HRS TO TIMESHEET ##
			#################################################################
			
			#################################################################
			## START: ADD JURY DUTY HRS TO TIMESHEET ##
			#################################################################
			if($total_request_hrs_j > 0){
			
			if($j > 1){

			$update7 = new FX($serverIP,$webCompanionPort);
			$update7 -> SetDBData('SIMS_2.fp7','time_hrs');
			$update7 -> SetDBPassword($webPW,$webUN);
			$update7 -> AddDBParam('-recid',$_SESSION['time_hrs_row_ID_j']);

			foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
				if($searchData['c_leave_hrs_type_code'][0] == 'J'){
				$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
				$update7 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
				}
			}
			
			$updateResult7 = $update7 -> FMEdit();
			
			} else {


			$newrecord9 = new FX($serverIP,$webCompanionPort); //create a new instance of FX
			$newrecord9 -> SetDBData('SIMS_2.fp7','time_hrs'); //set dbase information
			$newrecord9 -> SetDBPassword($webPW,$webUN); //set password information
			
			
			###ADD THE SUBMITTED VALUES AS PARAMETERS###
			$newrecord9 -> AddDBParam('Timesheet_ID',$timesheet_ID);
			$newrecord9 -> AddDBParam('HrsType','PdLv');
			$newrecord9 -> AddDBParam('LvType','Other');
			
			foreach($searchResult6['data'] as $key => $searchData) { //searchResult -> fmp table = leave_request_hrs 
			
			if($searchData['c_leave_hrs_type_code'][0] == 'J'){
			$hrs_fieldname = 'Hrs'.$searchData['c_leave_hrs_day_num'][0]; //SET FIELDNAME FOR FMP PARAM
			$newrecord9 -> AddDBParam("$hrs_fieldname",$searchData['c_lv_hrs_date_type_sum'][0]); //SUBMIT DYNAMICALLY DERIVED FMP FIELDNAME
			}
			
			//echo '<p>$hrs_fieldname: '.$hrs_fieldname;
			//echo '<p>$searchData[leave_num_hrs]: '.$searchData['leave_num_hrs'][0];
			}
			
			$newrecordResult9 = $newrecord9 -> FMNew();
			
			$recordData9 = current($newrecordResult9['data']);

			$_SESSION['time_hrs_row_ID_j'] = $recordData9['c_cwp_row_ID'][0];
			
			}
			$j++;
			}
			#################################################################
			## END: ADD LEAVE JURY DUTY HRS TO TIMESHEET ##
			#################################################################


$i++;

} 

}
#####################################################################################
## END: IF LEAVE REQUESTS EXIST FOR THE NEW TIMESHEET PAY PERIOD, CREATE LV ROWS
#####################################################################################





}

############################################################################
## START: FIND AVAILABLE BUDGET CODES FOR THIS USER TO POPULATE SELECT LIST
############################################################################
$search3 = new FX($serverIP,$webCompanionPort);
$search3 -> SetDBData('SIMS_2.fp7','budget_code_usage','all');
$search3 -> SetDBPassword($webPW,$webUN);
$search3 -> AddDBParam('staff_ID','=='.$staff_ID);
$search3 -> AddDBParam('budget_codes::c_Active_Status_cwp','Active');


//$search3 -> AddSortParam ($sortfield,'descend');


$searchResult3 = $search3 -> FMFind();

//echo $searchResult3['errorCode'];
//echo $searchResult3['foundCount'];
//print_r ($searchResult3);
$searchData3 = current($searchResult3['data']);
$_SESSION['user_bgt_codes'] = $searchData3;
############################################################################
## END: FIND AVAILABLE BUDGET CODES FOR THIS USER TO POPULATE SELECT LIST
############################################################################

############################################################################
## START: IF NEW HOLIDAY LINE WAS CREATED, FIND PAID LEAVE HOURS ROWS 
############################################################################
if($searchResult['foundCount'] > 0){
$search4 = new FX($serverIP,$webCompanionPort);
$search4 -> SetDBData('SIMS_2.fp7','time_hrs','all');
$search4 -> SetDBPassword($webPW,$webUN);
$search4 -> AddDBParam('Timesheet_ID',$timesheet_ID);
$search4 -> AddDBParam('HrsType','PdLv');


//$search4 -> AddSortParam ($sortfield,'descend');


$searchResult4 = $search4 -> FMFind();

//echo '<br>PdLvHrs ErrorCode: '.$searchResult4['errorCode'];
//echo '<br>PdLvHrs FoundCount: '.$searchResult4['foundCount'];

$searchData4 = current($searchResult4['data']);
}
############################################################################
## END: IF NEW HOLIDAY LINE WAS CREATED, FIND PAID LEAVE HOURS ROWS
############################################################################

#####################################################
## START: FIND WEEKEND DAYS FOR SHADING ON TIMESHEET
#####################################################
$pay_period_end_m = $recordData['c_PayPeriodEnd_m'][0];
$pay_period_end_d = $recordData['c_PayPeriodEnd_d'][0];
$pay_period_end_y = $recordData['c_PayPeriodEnd_y'][0];
$pay_period_lockout = $recordData['c_PayPeriodLockOutDate'][0];
$_SESSION['pay_period_lockout_date'] = $pay_period_lockout;
$pay_period_lockout_days = $recordData['c_PayPeriodLockOutDays'][0];
$today = date("M d Y");
$today_m = date("M");
$today_d = date("d");
$today_y = date("Y");
$today_stamp = strtotime($today);
$lockout_day = date("M d Y",mktime(0,0,0,$pay_period_end_m,$pay_period_end_d + $pay_period_lockout_days,$pay_period_end_y));
$lockout_day_stamp = strtotime($lockout_day);
//echo '<br>Today = '.$today;
//echo '<br>TodayStamp = '.$today_stamp;
//echo '<br>PayPeriod Lockout = '.$pay_period_lockout;
//echo '<br>Lockout day = '.$lockout_day;
//echo '<br>Lockout day Stamp = '.$lockout_day_stamp;
//echo '<br>Lockout days = '.$pay_period_lockout_days;

$_SESSION['day_of_week_01'] = date("D", mktime(0,0,0,$pay_period_end_m,1,$pay_period_end_y));
$_SESSION['day_of_week_02'] = date("D", mktime(0,0,0,$pay_period_end_m,2,$pay_period_end_y));
$_SESSION['day_of_week_03'] = date("D", mktime(0,0,0,$pay_period_end_m,3,$pay_period_end_y));
$_SESSION['day_of_week_04'] = date("D", mktime(0,0,0,$pay_period_end_m,4,$pay_period_end_y));
$_SESSION['day_of_week_05'] = date("D", mktime(0,0,0,$pay_period_end_m,5,$pay_period_end_y));
$_SESSION['day_of_week_06'] = date("D", mktime(0,0,0,$pay_period_end_m,6,$pay_period_end_y));
$_SESSION['day_of_week_07'] = date("D", mktime(0,0,0,$pay_period_end_m,7,$pay_period_end_y));
$_SESSION['day_of_week_08'] = date("D", mktime(0,0,0,$pay_period_end_m,8,$pay_period_end_y));
$_SESSION['day_of_week_09'] = date("D", mktime(0,0,0,$pay_period_end_m,9,$pay_period_end_y));
$_SESSION['day_of_week_10'] = date("D", mktime(0,0,0,$pay_period_end_m,10,$pay_period_end_y));
$_SESSION['day_of_week_11'] = date("D", mktime(0,0,0,$pay_period_end_m,11,$pay_period_end_y));
$_SESSION['day_of_week_12'] = date("D", mktime(0,0,0,$pay_period_end_m,12,$pay_period_end_y));
$_SESSION['day_of_week_13'] = date("D", mktime(0,0,0,$pay_period_end_m,13,$pay_period_end_y));
$_SESSION['day_of_week_14'] = date("D", mktime(0,0,0,$pay_period_end_m,14,$pay_period_end_y));
$_SESSION['day_of_week_15'] = date("D", mktime(0,0,0,$pay_period_end_m,15,$pay_period_end_y));
$_SESSION['day_of_week_16'] = date("D", mktime(0,0,0,$pay_period_end_m,16,$pay_period_end_y));
$_SESSION['day_of_week_17'] = date("D", mktime(0,0,0,$pay_period_end_m,17,$pay_period_end_y));
$_SESSION['day_of_week_18'] = date("D", mktime(0,0,0,$pay_period_end_m,18,$pay_period_end_y));
$_SESSION['day_of_week_19'] = date("D", mktime(0,0,0,$pay_period_end_m,19,$pay_period_end_y));
$_SESSION['day_of_week_20'] = date("D", mktime(0,0,0,$pay_period_end_m,20,$pay_period_end_y));
$_SESSION['day_of_week_21'] = date("D", mktime(0,0,0,$pay_period_end_m,21,$pay_period_end_y));
$_SESSION['day_of_week_22'] = date("D", mktime(0,0,0,$pay_period_end_m,22,$pay_period_end_y));
$_SESSION['day_of_week_23'] = date("D", mktime(0,0,0,$pay_period_end_m,23,$pay_period_end_y));
$_SESSION['day_of_week_24'] = date("D", mktime(0,0,0,$pay_period_end_m,24,$pay_period_end_y));
$_SESSION['day_of_week_25'] = date("D", mktime(0,0,0,$pay_period_end_m,25,$pay_period_end_y));
$_SESSION['day_of_week_26'] = date("D", mktime(0,0,0,$pay_period_end_m,26,$pay_period_end_y));
$_SESSION['day_of_week_27'] = date("D", mktime(0,0,0,$pay_period_end_m,27,$pay_period_end_y));
$_SESSION['day_of_week_28'] = date("D", mktime(0,0,0,$pay_period_end_m,28,$pay_period_end_y));
$_SESSION['day_of_week_29'] = date("D", mktime(0,0,0,$pay_period_end_m,29,$pay_period_end_y));
$_SESSION['day_of_week_30'] = date("D", mktime(0,0,0,$pay_period_end_m,30,$pay_period_end_y));
$_SESSION['day_of_week_31'] = date("D", mktime(0,0,0,$pay_period_end_m,31,$pay_period_end_y));
#####################################################
## END: FIND WEEKEND DAYS FOR SHADING ON TIMESHEET
#####################################################

################################################
## START: PRINT VARIABLE VALUES FOR DEBUGGING ##
################################################
if ($debug == 'on'){
echo '<br>Days in Month: '.$days_in_month;
echo '<br>Header colspan: '.$header_colspan;
echo '<br>StaffID: '.$staff_ID;
echo '<br>LastPayPeriodEnd: '.$last_pay_period_end;
echo '<br>Month: '.$last_pay_period_end_m;
echo '<br>Day: '.$last_pay_period_end_d;
echo '<br>Year: '.$last_pay_period_end_y;
echo '<br>NewPayPeriodBegin: '.$new_pay_period;

echo '<br>New Timesheet ID: '.$timesheet_ID;
echo '<br>New Record ErrorCode: '.$newrecordResult['errorCode'];
echo '<br>New Pay Period Monthkey: '.$new_pay_period_monthkey;

echo '<br>TimeHrs ErrorCode: '.$newrecordResult2['errorCode'];

echo '<br>SEDL HolidaySearch ErrorCode: '.$searchResult['errorCode'];
echo '<br>SEDL HolidaySearch Foundcount: '.$searchResult['foundCount'];

echo '<br>$searchData[c_HolidayDay_numeric][0]: '.$searchData['c_HolidayDay_numeric'][0];
echo '<br>$searchData[c_HolidayDay_numeric][1]: '.$searchData['c_HolidayDay_numeric'][1];
echo '<br>$searchData[c_HolidayDay_numeric][2]: '.$searchData['c_HolidayDay_numeric'][2];
echo '<br>$searchData[c_HolidayDay_numeric][3]: '.$searchData['c_HolidayDay_numeric'][3];



echo '<br>PdLvHrs ErrorCode: '.$searchResult4['errorCode'];
echo '<br>PdLvHrs FoundCount: '.$searchResult4['foundCount'];
echo '<br>$_SESSION payperiod_workhrs: '.$_SESSION['payperiod_workhrs'];
echo '<br>$_SESSION employee type: '.$_SESSION['employee_type'];

echo '<br>$pay_period_end_d: '.$pay_period_end_d;
echo '<br>$pay_period_end_m: '.$pay_period_end_m;
echo '<br>$pay_period_end_y: '.$pay_period_end_y;

echo '<br>$new_pay_period_monthkey: '.$new_pay_period_monthkey;

echo '<br>$_SESSION[pay_period_num]: '.$_SESSION['pay_period_num'];

echo '<br>$fte_hrs: '.$fte_hrs;
}

################################################
## END: PRINT VARIABLE VALUES FOR DEBUGGING ##
################################################

##################################################################################################
## START: DISPLAY THE TIMESHEET IN AN HTML TABLE IN EDIT MODE WITH EDIT FIELDS FOR SELECTED ROW ##
##################################################################################################
?>


<html>
<head>
<title>SIMS - Timesheets</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">

<script language="JavaScript">

function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

function checkFields() { 

	// Name
		if (document.timesheet.budget_code.value =="choose") {
			alert("Please enter a budget code for these hours.");
			document.timesheet.budget_code.focus();
			return false;	}

	var days_in_month = <?php echo $days_in_month;?>;

    var hrs_total = 0;
	var h01 = ((document.timesheet.hrs_01.value) || ("0"));
	var h02 = ((document.timesheet.hrs_02.value) || ("0"));
	var h03 = ((document.timesheet.hrs_03.value) || ("0"));
	var h04 = ((document.timesheet.hrs_04.value) || ("0"));
	var h05 = ((document.timesheet.hrs_05.value) || ("0"));
	var h06 = ((document.timesheet.hrs_06.value) || ("0"));
	var h07 = ((document.timesheet.hrs_07.value) || ("0"));
	var h08 = ((document.timesheet.hrs_08.value) || ("0"));
	var h09 = ((document.timesheet.hrs_09.value) || ("0"));
	var h10 = ((document.timesheet.hrs_10.value) || ("0"));
	var h11 = ((document.timesheet.hrs_11.value) || ("0"));
	var h12 = ((document.timesheet.hrs_12.value) || ("0"));
	var h13 = ((document.timesheet.hrs_13.value) || ("0"));
	var h14 = ((document.timesheet.hrs_14.value) || ("0"));
	var h15 = ((document.timesheet.hrs_15.value) || ("0"));
	var h16 = ((document.timesheet.hrs_16.value) || ("0"));
	var h17 = ((document.timesheet.hrs_17.value) || ("0"));
	var h18 = ((document.timesheet.hrs_18.value) || ("0"));
	var h19 = ((document.timesheet.hrs_19.value) || ("0"));
	var h20 = ((document.timesheet.hrs_20.value) || ("0"));
	var h21 = ((document.timesheet.hrs_21.value) || ("0"));
	var h22 = ((document.timesheet.hrs_22.value) || ("0"));
	var h23 = ((document.timesheet.hrs_23.value) || ("0"));
	var h24 = ((document.timesheet.hrs_24.value) || ("0"));
	var h25 = ((document.timesheet.hrs_25.value) || ("0"));
	var h26 = ((document.timesheet.hrs_26.value) || ("0"));
	var h27 = ((document.timesheet.hrs_27.value) || ("0"));
	var h28 = ((document.timesheet.hrs_28.value) || ("0"));

	var h29 = 0;
	var h30 = 0;
	var h31 = 0;

	if(days_in_month > 28){
	h29 = ((document.timesheet.hrs_29.value) || ("0"));
	}

	if(days_in_month > 29){
	h30 = ((document.timesheet.hrs_30.value) || ("0"));
	}

	if(days_in_month > 30){
	h31 = ((document.timesheet.hrs_31.value) || ("0"));
	}

        h01 = parseFloat(h01);
        h02 = parseFloat(h02);
        h03 = parseFloat(h03);
        h04 = parseFloat(h04);
        h05 = parseFloat(h05);
        h06 = parseFloat(h06);
        h07 = parseFloat(h07);
        h08 = parseFloat(h08);
        h09 = parseFloat(h09);
        h10 = parseFloat(h10);
        h11 = parseFloat(h11);
        h12 = parseFloat(h12);
        h13 = parseFloat(h13);
        h14 = parseFloat(h14);
        h15 = parseFloat(h15);
        h16 = parseFloat(h16);
        h17 = parseFloat(h17);
        h18 = parseFloat(h18);
        h19 = parseFloat(h19);
        h20 = parseFloat(h20);
        h21 = parseFloat(h21);
        h22 = parseFloat(h22);
        h23 = parseFloat(h23);
        h24 = parseFloat(h24);
        h25 = parseFloat(h25);
        h26 = parseFloat(h26);
        h27 = parseFloat(h27);
        h28 = parseFloat(h28);
        h29 = parseFloat(h29);
        h30 = parseFloat(h30);
        h31 = parseFloat(h31);


	hrs_total = h01 + h02 + h03 + h04 + h05 + h06 + h07 + h08 + h09 + h10 + h11 + h12 + h13 + h14 + h15 + h16 + h17 + h18 + h19 + h20 + h21 + h22 + h23 + h24 + h25 + h26 + h27 + h28 + h29 + h30 + h31;

		var roundedNumber = roundNumber(hrs_total,1);	

		if (hrs_total !=roundedNumber){
			alert("Please round hours to the nearest tenth.");
			return false;	}

}	


function confirmDelete() { 
	var answer = confirm ("Are you sure you want to delete this row?")
	if (!answer) {
	return false;
	}


}
// -->

function zoomWindow() {
window.resizeTo(screen.width,screen.height)
}



</script>




</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="zoomWindow()">

<table width="1100" cellpadding="0" cellspacing="0" border="0">
<tr bgcolor="#ffffff"><td>&nbsp;</td><td width="100%">&nbsp;</td><td align="right">&nbsp;</td></tr>
<tr><td colspan="3">

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="#cccccc" bgcolor="ffffff" width="1100">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS Timesheets</h1><hr /></td></tr>
			
			<tr bgcolor="#a2c7ca"><td class="body"><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right">Timesheet Status: <?php echo $recordData['TimesheetSubmittedStatus'][0];?> | Pay Period: <strong><?php echo $recordData['PayPeriodBegin'][0];?> - <?php echo $recordData['c_PayPeriodEnd'][0];?></strong></td></tr>
			
			
			
			<tr><td class="body">&nbsp;<i>NOTE: Record Time to Nearest Tenth of an Hour</i></td><td align="right">Timesheet ID: <?php echo $_SESSION['timesheet_ID'];?> | <a href="/staff/sims/my_budget_codes.php" target="_blank">My Budget Codes</a> | <a href="/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd">Close Timesheet</a></td></tr>
			<tr><td class="body" colspan=2>
			<form name="timesheet" action="/staff/sims/timesheets_edit.php" onsubmit="return checkFields()">
			<input type="hidden" name="timesheet_ID" value="<?php echo $timesheet_ID;?>">
			<input type="hidden" name="action" value="confirm_edit">
			<input type="hidden" name="edit_row_ID" value="<?php echo $row_ID;?>">
			<input type="hidden" name="new_row_ID" value="<?php echo $new_row_ID;?>">
			<input type="hidden" name="days_in_month" value="<?php echo $days_in_month;?>">



<!--BEGIN FIRST SECTION: REGULAR HOURS BY BUDGET CODE-->


							
							<table cellspacing=0 cellpadding=1 border=1 bordercolor="#cccccc" width="100%" class="sims">
							<tr><td colspan="<?php echo $header_colspan;?>"><strong>Regular Hours by Budget Code:</strong></td></tr>
									<tr bgcolor="#cccccc">
									<td class="body">&nbsp;Budget Code</td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>01</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>02</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>03</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>04</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>05</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>06</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>07</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>08</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>09</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>10</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>11</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>12</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>13</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>14</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>15</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>16</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>17</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>18</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>19</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>20</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>21</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>22</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>23</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>24</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>25</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>26</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>27</strong>';}?></td>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>28</strong>';}?></td>

									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>29</strong>';}?></td>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>30</strong>';}?></td>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body"><?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo '';}else{echo '<strong>31</strong>';}?></td>
									<?php } ?>
									<td colspan="2">&nbsp;</td>
									
									</tr>
									
									<?php 
									
									foreach($newrecordResult2['data'] as $key => $newrecordData) { //newrecordResult2 -> fmp table = time_hrs ?>
									
									<tr>
									<td class="body" nowrap>
									
									
									
										<select name="budget_code" class="body">
										<option value="choose"></option>
										
										<?php foreach($searchResult3['data'] as $key => $searchData3) { //searchResult3 -> fmp table = budget_code_usage ?>
										<option value="<?php echo $searchData3['budget_code'][0];?>"> <?php echo $searchData3['budget_code'][0]; ?> - <?php echo stripslashes($searchData3['Budget_Code_Nickname'][0]); ?></option>
										<?php } ?>
										</select>
									
									
									
									</td>
									
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')||(strpos($holiday_days,'01') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'01') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'01') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_01"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')||(strpos($holiday_days,'02') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'02') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'02') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_02"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')||(strpos($holiday_days,'03') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'03') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'03') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_03"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')||(strpos($holiday_days,'04') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'04') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'04') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_04"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')||(strpos($holiday_days,'05') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'05') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'05') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_05"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')||(strpos($holiday_days,'06') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'06') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'06') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_06"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')||(strpos($holiday_days,'07') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'07') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'07') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_07"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')||(strpos($holiday_days,'08') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'08') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'08') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_08"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')||(strpos($holiday_days,'09') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'09') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'09') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_09"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')||(strpos($holiday_days,'10') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'10') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'10') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_10"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')||(strpos($holiday_days,'11') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'11') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'11') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_11"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')||(strpos($holiday_days,'12') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'12') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'12') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_12"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')||(strpos($holiday_days,'13') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'13') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'13') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_13"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')||(strpos($holiday_days,'14') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'14') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'14') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_14"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')||(strpos($holiday_days,'15') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'15') === false)&&($_SESSION['pay_period_num'] == '1'))||((strpos($holiday_days,'15') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_15"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')||(strpos($holiday_days,'16') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'16') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'16') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_16"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')||(strpos($holiday_days,'17') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'17') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'17') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_17"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')||(strpos($holiday_days,'18') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'18') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'18') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_18"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')||(strpos($holiday_days,'19') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'19') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'19') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_19"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')||(strpos($holiday_days,'20') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'20') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'20') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_20"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')||(strpos($holiday_days,'21') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'21') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'21') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_21"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')||(strpos($holiday_days,'22') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'22') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'22') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_22"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')||(strpos($holiday_days,'23') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'23') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'23') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_23"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')||(strpos($holiday_days,'24') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'24') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'24') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_24"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')||(strpos($holiday_days,'25') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'25') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'25') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_25"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')||(strpos($holiday_days,'26') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'26') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'26') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_26"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')||(strpos($holiday_days,'27') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'27') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'27') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_27"><?php }else{echo '';} ?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')||(strpos($holiday_days,'28') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'28') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'28') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_28"><?php }else{echo '';} ?></td>


									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')||(strpos($holiday_days,'29') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'29') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'29') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_29"><?php }else{echo '';} ?></td>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')||(strpos($holiday_days,'30') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'30') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'30') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_30"><?php }else{echo '';} ?></td>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')||(strpos($holiday_days,'31') !== false)){echo 'bgcolor="#cccccc"';} ?>><?php if(((strpos($holiday_days,'31') === false)&&($_SESSION['pay_period_num'] == '2'))||((strpos($holiday_days,'31') === false)&&($_SESSION['employee_type'] == 'Exempt'))){ ?><input <?php if((($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun'))&&($_SESSION['timesheet_prefs_hide_weekends'] == 'Yes')){echo 'type="hidden"';}else{echo 'type="text" size="2" class="body"';}?> name="hrs_31"><?php }else{echo '';} ?></td>
									<?php } ?>
									<td align="center" class="body"><input type="submit" name="submit" value="Submit"></td>
									<td>&nbsp;</td>
									</tr>
									
									
									<?php  } ?>
								
							
									
									
									
							
							
							<!--/table-->
							<tr><td colspan="<?php echo $header_colspan;?>">
							<a href="/staff/sims/timesheets.php?action=edit&new_row=wk&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Regular Hours</a> | Row ID: <?php echo $new_row_ID;?>&nbsp;<p>
							</td></tr>
<!--END FIRST SECTION: REGULAR HOURS BY BUDGET CODE-->


<!--BEGIN SECOND SECTION: PAID LEAVE HOURS-->

							<tr><td colspan="<?php echo $header_colspan;?>"><strong>Paid Leave Hours:</strong></td></tr>
							
							
									
									<?php 
								if($searchResult4['foundCount']==0){ //searchResult4 -> fmp table = time_hrs  ?> 
									<tr><td colspan="<?php echo $header_colspan;?>">
									<p class="alert_small">There are no paid leave hours entered for this timesheet. | <a href="/staff/sims/timesheets.php?action=edit&new_row=pdlv&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Paid Leave Hours</a></p>&nbsp;<p>
									</td></tr>
								<?php }else{ ?>
								
							<!--table cellspacing=0 cellpadding=1 border=1 bordercolor="#cccccc" width="100%" class="body"-->
									<tr bgcolor="#cccccc">
									<td class="body">&nbsp;Leave Type</td>
									<td align="center" class="body"><strong>01</strong></td>
									<td align="center" class="body"><strong>02</strong></td>
									<td align="center" class="body"><strong>03</strong></td>
									<td align="center" class="body"><strong>04</strong></td>
									<td align="center" class="body"><strong>05</strong></td>
									<td align="center" class="body"><strong>06</strong></td>
									<td align="center" class="body"><strong>07</strong></td>
									<td align="center" class="body"><strong>08</strong></td>
									<td align="center" class="body"><strong>09</strong></td>
									<td align="center" class="body"><strong>10</strong></td>
									<td align="center" class="body"><strong>11</strong></td>
									<td align="center" class="body"><strong>12</strong></td>
									<td align="center" class="body"><strong>13</strong></td>
									<td align="center" class="body"><strong>14</strong></td>
									<td align="center" class="body"><strong>15</strong></td>
									<td align="center" class="body"><strong>16</strong></td>
									<td align="center" class="body"><strong>17</strong></td>
									<td align="center" class="body"><strong>18</strong></td>
									<td align="center" class="body"><strong>19</strong></td>
									<td align="center" class="body"><strong>20</strong></td>
									<td align="center" class="body"><strong>21</strong></td>
									<td align="center" class="body"><strong>22</strong></td>
									<td align="center" class="body"><strong>23</strong></td>
									<td align="center" class="body"><strong>24</strong></td>
									<td align="center" class="body"><strong>25</strong></td>
									<td align="center" class="body"><strong>26</strong></td>
									<td align="center" class="body"><strong>27</strong></td>
									<td align="center" class="body"><strong>28</strong></td>
									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body"><strong>29</strong></td>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body"><strong>30</strong></td>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body"><strong>31</strong></td>
									<?php } ?>
									<td colspan="2">&nbsp;</td>
									</tr>
									
									
									<?php
								
								
									foreach($searchResult4['data'] as $key => $searchData4) { ?>
									
									<tr>
									<td class="body" nowrap><?php echo $searchData4['LvType'][0];?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_01']=='Sat')||($_SESSION['day_of_week_01']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs01'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs01'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_02']=='Sat')||($_SESSION['day_of_week_02']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs02'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs02'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_03']=='Sat')||($_SESSION['day_of_week_03']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs03'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs03'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_04']=='Sat')||($_SESSION['day_of_week_04']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs04'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs04'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_05']=='Sat')||($_SESSION['day_of_week_05']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs05'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs05'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_06']=='Sat')||($_SESSION['day_of_week_06']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs06'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs06'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_07']=='Sat')||($_SESSION['day_of_week_07']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs07'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs07'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_08']=='Sat')||($_SESSION['day_of_week_08']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs08'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs08'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_09']=='Sat')||($_SESSION['day_of_week_09']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs09'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs09'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_10']=='Sat')||($_SESSION['day_of_week_10']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs10'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs10'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_11']=='Sat')||($_SESSION['day_of_week_11']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs11'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs11'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_12']=='Sat')||($_SESSION['day_of_week_12']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs12'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs12'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_13']=='Sat')||($_SESSION['day_of_week_13']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs13'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs13'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_14']=='Sat')||($_SESSION['day_of_week_14']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs14'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs14'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_15']=='Sat')||($_SESSION['day_of_week_15']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs15'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs15'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_16']=='Sat')||($_SESSION['day_of_week_16']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs16'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs16'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_17']=='Sat')||($_SESSION['day_of_week_17']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs17'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs17'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_18']=='Sat')||($_SESSION['day_of_week_18']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs18'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs18'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_19']=='Sat')||($_SESSION['day_of_week_19']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs19'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs19'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_20']=='Sat')||($_SESSION['day_of_week_20']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs20'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs20'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_21']=='Sat')||($_SESSION['day_of_week_21']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs21'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs21'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_22']=='Sat')||($_SESSION['day_of_week_22']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs22'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs22'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_23']=='Sat')||($_SESSION['day_of_week_23']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs23'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs23'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_24']=='Sat')||($_SESSION['day_of_week_24']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs24'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs24'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_25']=='Sat')||($_SESSION['day_of_week_25']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs25'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs25'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_26']=='Sat')||($_SESSION['day_of_week_26']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs26'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs26'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_27']=='Sat')||($_SESSION['day_of_week_27']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs27'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs27'][0];}?></td>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_28']=='Sat')||($_SESSION['day_of_week_28']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs28'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs28'][0];}?></td>
									
									
									<?php if ($days_in_month > 28) { ?>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_29']=='Sat')||($_SESSION['day_of_week_29']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs29'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs29'][0];}?></td>
									<?php } ?>
									<?php if ($days_in_month > 29) { ?>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_30']=='Sat')||($_SESSION['day_of_week_30']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs30'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs30'][0];}?></td>
									<?php } ?>
									<?php if ($days_in_month > 30) { ?>
									<td align="center" class="body" <?php if(($_SESSION['day_of_week_31']=='Sat')||($_SESSION['day_of_week_31']=='Sun')){echo 'bgcolor="#cccccc"';} ?>><?php if($searchData4['Hrs31'][0] == ''){echo '&nbsp;';}else{ echo $searchData4['Hrs31'][0];}?></td>
									<?php } ?>
									<td align="center" class="body"><a href="/staff/sims/timesheets.php?action=edit&Timesheet_ID=<?php echo $timesheet_ID;?>&edit_row_ID=<?php echo $searchData4['c_cwp_row_ID'][0];?>">Edit</a></td>
									<td><a href="/staff/sims/timesheet_hrs_delete.php?row_ID=<?php echo $searchData3['c_cwp_row_ID'][0];?>" onclick="return confirmDelete()"><img src="/staff/sims/images/trashcan.jpg" border="0"></a></td>
									
									
									</tr>
									
									
									<?php  } ?>
									
									
								
							<!--/table-->
							
							<?php } ?>
								
								

<!--END SECOND SECTION: PAID LEAVE HOURS-->

<!--BEGIN THIRD SECTION: UNPAID LEAVE HOURS-->

							<tr><td colspan="<?php echo $header_colspan;?>"><p>&nbsp;<p>
							<strong>Unpaid Leave Hours:</strong>
							
							
									
									<p class="alert_small">There are no unpaid leave hours entered for this timesheet. | <a href="/staff/sims/timesheets.php?action=edit&new_row=unpdlv&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Unpaid Leave Hours</a></p>
							</td></tr>	
								
<!--END THIRD SECTION: UNPAID LEAVE HOURS-->


<?php if($_SESSION['employee_type'] == 'Non-exempt') { ?>

<!--BEGIN FOURTH SECTION: OVERTIME HOURS BY BUDGET CODE-->

							<tr><td colspan="<?php echo $header_colspan;?>">
							<strong>Overtime Hours by Budget Code:</strong>
							
							
									
									<p class="alert_small">There are no overtime hours entered for this timesheet. | <a href="/staff/sims/timesheets.php?action=edit&new_row=ot&Timesheet_ID=<?php echo $timesheet_ID;?>">Add Overtime Hours</a></p>
								
							</td></tr>	
							

<?php } ?>		

<!--END FOURTH SECTION: OVERTIME HOURS BY BUDGET CODE-->

			</form>
			</td></tr>
			
			
			
			
			
			</table>

</td></tr>
</table>

</td></tr>
</table>


</body>

</html>
<?php
#################################################################################################
## END: DISPLAY THE TIMESHEET IN AN HTML TABLE IN EDIT MODE WITH EDIT FIELDS FOR SELECTED ROW
#################################################################################################

?>
<?php
session_start();

include_once('FX/FX.php');
include_once('FX/server_data.php');

$debug = 'off'; //change to "on" to print variable values at top of table
$timesheet_ID = $_GET['Timesheet_ID'];

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
## START: DUPLICATE THE TIMESHEET IN FMP
#################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','timesheets');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('TimesheetID','=='.$timesheet_ID);
$search -> AddDBParam('-script', 'duplicate_timesheet_web');

$searchResult = $search -> FMFind();
//echo '<p>FoundCount: '.$searchResult['foundCount'];
//echo '<p>errorCode: '.$searchResult['errorCode'];

//$recordData = current($searchResult['data']);
//$new_timesheet_ID = $recordData['c_newest_timesheet_ID'][0];
//echo '<p>new Timesheet ID: '.$new_timesheet_ID;

//exit;
#################################################
## END: DUPLICATE THE TIMESHEET IN FMP
#################################################

##############################################################
## START: FIND THE NEWLY DUPLICATED TIMESHEET AND GET ROW_ID
##############################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','timesheets','1');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('staff_ID',$_SESSION['staff_ID']);
$search -> AddSortParam('TimesheetID', 'descend');

$searchResult = $search -> FMFind();
//echo '<p>FoundCount: '.$searchResult['foundCount'];
//echo '<p>errorCode: '.$searchResult['errorCode'];

$recordData = current($searchResult['data']);
$new_timesheet_ID = $recordData['c_newest_timesheet_ID'][0];
//echo '<p>new Timesheet ID: '.$new_timesheet_ID;


$search2 = new FX($serverIP,$webCompanionPort);
$search2 -> SetDBData('SIMS_2.fp7','timesheets');
$search2 -> SetDBPassword($webPW,$webUN);
$search2 -> AddDBParam('TimesheetID',$new_timesheet_ID);

$searchResult2 = $search2 -> FMFind();
//echo '<p>FoundCount: '.$searchResult2['foundCount'];
//echo '<p>errorCode: '.$searchResult2['errorCode'];

$recordData2 = current($searchResult2['data']);
$current_id = $recordData2['c_row_ID_cwp'][0];
//echo '<p>c_row_ID_cwp: '.$current_id;

//exit;
############################################################
## END: FIND THE NEWLY DUPLICATED TIMESHEET AND GET ROW_ID
############################################################

####################################################################
## START: UPDATE THE DUPLICATED FMP RECORD'S PAY PERIOD BEGIN DATE
####################################################################
$update = new FX($serverIP,$webCompanionPort);
$update -> SetDBData('SIMS_2.fp7','timesheets');
$update -> SetDBPassword($webPW,$webUN);
$update -> AddDBParam('-recid',$current_id);

$update -> AddDBParam('PayPeriodBegin',$new_pay_period);



$updateResult = $update -> FMEdit();
//echo '<p>errorCode: '.$updateResult['errorCode'];
$recordData3 = current($updateResult['data']);
####################################################################
## END: UPDATE THE DUPLICATED FMP RECORD'S PAY PERIOD BEGIN DATE
####################################################################

#####################################################
## START: GET PAY PERIOD INFO FOR THIS NEW TIMESHEET
#####################################################
$timesheet_ID = $recordData3['TimesheetID'][0];
$_SESSION['timesheet_ID'] = $timesheet_ID;

//echo '<br>New Timesheet ID: '.$timesheet_ID;
//echo '<br>New Record ErrorCode: '.$updateResult['errorCode'];
//echo '<br>FoundCount: '.$updateResult['foundCount'];
$_SESSION['last_pay_period_end'] = $recordData3['c_last_pay_period'][0];
$pay_period_end_d = $recordData3['c_PayPeriodEnd_d'][0];
$pay_period_end_m = $recordData3['c_PayPeriodEnd_m'][0];
$pay_period_end_y = $recordData3['c_PayPeriodEnd_y'][0];
$pay_period_end = $recordData3['c_PayPeriodEnd'][0];
$pay_period_begin = $recordData3['PayPeriodBegin'][0];

$pay_period_begin_d = $recordData3['c_PayPeriodBegin_d'][0];

$new_pay_period_monthkey = $recordData3['c_payperiod_month_yr_key'][0];
//echo '<br>New Pay Period Monthkey: '.$new_pay_period_monthkey;

$_SESSION['current_pay_period_end'] = $recordData3['c_PayPeriodEnd'][0];
$_SESSION['signer_ID_imm_spvsr'] = $recordData3['Signer_ID_imm_spvsr'][0];
$_SESSION['signer_ID_owner'] = $recordData3['Signer_ID_owner'][0];
$_SESSION['signer_ID_pba'] = $recordData3['Signer_ID_pba'][0];


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
## END: GET PAY PERIOD INFO FOR THIS TIMESHEET
#####################################################

#################################################################################################
## START: FIND ANY SEDL HOLIDAYS FOR THE NEW PAY PERIOD MONTH (FOR EXEMPT OR NON-EXEMPT STAFF) ##
#################################################################################################
if($_SESSION['employee_type'] != 'Hourly'){ //HOURLY STAFF DON'T HAVE PAID LEAVE, UNPAID LEAVE, OR OVERTIME HRS

$search5 = new FX($serverIP,$webCompanionPort);
$search5 -> SetDBData('SIMS_2.fp7','SEDL_holidays','all');
$search5 -> SetDBPassword($webPW,$webUN);
$search5 -> AddDBParam('c_HolidayMonthYrKey',$new_pay_period_monthkey);
$search5 -> AddDBParam('HolidayDate',$recordData3['PayPeriodBegin'][0].'...'.$recordData3['c_PayPeriodEnd'][0]);
//$search5 -> AddDBParam('HolidayDate',$recordData3['c_PayPeriodEnd'][0],'lte');


//$search5 -> AddSortParam ($sortfield,'descend');


$searchResult5 = $search5 -> FMFind();

//echo '<br>SEDL HolidaySearch ErrorCode: '.$searchResult5'errorCode'];
//echo '<br>SEDL HolidaySearch Foundcount: '.$searchResult5['foundCount'];
//print_r ($searchResult5);
//$searchData5 = current($searchResult5['data']);
//$_SESSION['user_bgt_codes'] = $searchData5;
###############################################################################################
## END: FIND ANY SEDL HOLIDAYS FOR THE NEW PAY PERIOD MONTH (FOR EXEMPT OR NON-EXEMPT STAFF) ##
###############################################################################################

##############################################################################
## START: IF HOLIDAYS EXIST FOR THE NEW TIMESHEET, CREATE PAID LV HOLIDAY ROW
##############################################################################
if($searchResult5['foundCount'] > 0){

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
foreach($searchResult5['data'] as $key => $searchData5) { //searchResult -> fmp table = SEDL_holidays 

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

//$recordData3 = current($newrecordResult3['data']);

//$new_row_ID3 = $recordData3['c_cwp_row_ID'][0];

//echo '<br>NewHolidayRecord ErrorCode: '.$newrecordResult3['errorCode'];
//echo '<br>NewHolidayRecord FoundCount: '.$newrecordResult3['foundCount'];
//}
}
}
##############################################################################
## END: IF HOLIDAYS EXIST FOR THE NEW TIMESHEET, CREATE PAID LV HOLIDAY ROW
##############################################################################



header('Location: http://www.sedl.org/staff/sims/menu_timesheets.php?sortfield=c_PayPeriodEnd');
exit;


<?php

include_once('FX/FX.php');
include_once('FX/server_data.php');






###GET FUTURE CALENDAR ENTRIES FROM FMP###

$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('CC_dms.fp7','cc_events_web_calendar','all');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('c_show_on_sedl_corp_calendar','yes');


###COLLECT THE NEW DATA INTO A VARIABLE AND SEND TO THE FMFind() FUNCTION###

$searchResult = $search -> FMFind();

$searchData = current($searchResult['data']);

//echo 'ErrorCode: '.$searchResult['errorCode'].'<br>';
//echo 'Foundcount: '.$searchResult['foundCount'];
//echo 'hello there';


###CONNECT TO MYSQL###



$db = mysql_connect('localhost','sedlstaff','opensesame123');

//if(!$db) {
//	die('Not connected : '. mysql_error());
//} else {
//echo 'Connected to: mysql';	
//}

//select corp as the current db

$db_selected = mysql_select_db('corp',$db);

//if(!$db_selected) {
//	die('Can\'t use corp : ' . mysql_error());
//} else {
//echo 'Connected to: mysql database corp';
//}




###UPDATE MYSQL CALENDAR FROM PHP###

$command_1 = "
delete from sedlcalendar where from_calendar = 'secc' or from_calendar = 'txcc'";

$command_1 = "
delete from sedlcalendar where entered_by = 'ewaters'";

//echo $command_1;

$result_1 = mysql_query($command_1);
//if (!$result_1) {
//   die('Invalid query: ' . mysql_error());
//}


$today = date("n").'/'.date("j").'/'.date("Y");





###INSERT ROWS INTO MYSQL FROM DATA RETURNED BY PHP###

foreach($searchResult['data'] as $key => $searchData) { 

//echo $searchData['event_name_1'][0];


// SEPARATE OUT CITY AND STATE
$wordChunks = explode(",", $searchData['event_city_state'][0]);
$wordChunks[0] = trim($wordChunks[0]);
$wordChunks[1] = trim($wordChunks[1]);


// Set default subject and check to see if RTI
$subject_label = "Improving School Performance";

$haystack = $searchData['event_name_1'][0] . " " . $searchData['event_name_2'][0];
$pos = strpos($haystack,'Response to Intervention');
$pos2 = strpos($haystack,'English Language Learners');

if($pos === false) {
} else {
	$subject_label = "Response to Intervention (RtI)";
}
if($pos2 === false) {
} else {
	$subject_label = "English Language Learners";
}

$hosting_organization = $searchData['c_cc_name'][0];
if ($hosting_organization == '') {
	$hosting_organization = "External Organization";
}
							
	$command_2 = "
	insert into sedlcalendar values (
	'', '" . 
	addslashes($searchData['event_name_1'][0]) . "',
	'" .addslashes($searchData['event_name_2'][0]) . "',
	'" .$searchData['c_event_start_date_mysql'][0] . "',
	'" .$searchData['c_event_end_date_mysql'][0] . "',
	'" .$wordChunks[0] . "',
	'" .$wordChunks[1] . "',
	'" .addslashes($searchData['event_venue'][0]) . "',
	'" .addslashes($searchData['c_event_description_html'][0]) . "',
	'" .addslashes($searchData['event_contact_person'][0]) . "',
	'" .$searchData['event_contact_person_phone'][0] . "',
	'" .$searchData['event_contact_person_email'][0] . "',
	'" .$searchData['event_website'][0] . "',
	'$subject_label',
	'" .$hosting_organization. "',
	'corp intranet-home secc txcc',
	'ewaters',
	'" .$today."',
	'',
	'',
	'" .$searchData['event_type'][0] . "',
	''
	)";
	
//	'" .$searchData['c_sedl_calendar_identifier'][0] . "',
	$result_2 = mysql_query($command_2);
	//echo "$command_2";
	//if (!$result_2) { 
	//die('Invalid query: ' . mysql_error());
	//}						
//echo $command_2.'<p>';

} 



?>
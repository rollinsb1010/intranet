<?php
session_start();

include_once('FX/FX.php');
include_once('FX/server_data.php');

################################
## START: GRAB FORM VARIABLES ##
################################
$object_ID = $_GET['object_ID'];
$table_name = $_GET['target'];
$ip = $_SERVER['REMOTE_ADDR'];
$session_cookie = $_COOKIE['ss_session_id'];
$user = '';
$user = $_GET['user'];
##############################
## END: GRAB FORM VARIABLES ##
##############################

if($table_name == 'pos_descr'){
##################################
## START: CREATE THE FMP RECORD ##
##################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','sims_temp_launcher');
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('IP',$ip);
$newrecord -> AddDBParam('object_ID',$object_ID);
$newrecord -> AddDBParam('table_name',$table_name);
$newrecord -> AddDBParam('action','admin-view-edit');
$newrecord -> AddDBParam('user_ID',$_SESSION['user_ID']);
$newrecord -> AddDBParam('sims_session_id',$session_cookie);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];

?>
<html>
<head>
<meta http-equiv="refresh" content="0; url=fmp7://admin:admin@198.214.140.248/SIMS_2.fp7">
<script language="javascript"> 
<!-- 
setTimeout("self.close();",1000) 
//--> 
</script>

</head>
<body>
Opening FileMaker...
</body>
</html>
<?php
//header('Location: fmp7://admin:admin@198.214.140.248/SIMS_2.fp7');
exit;
}


if($table_name == 'plan_agrmt'){
##################################
## START: CREATE THE FMP RECORD ##
##################################
$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','sims_temp_launcher');
$newrecord -> SetDBPassword($webPW,$webUN);

$newrecord -> AddDBParam('IP',$ip);
$newrecord -> AddDBParam('object_ID',$object_ID);
$newrecord -> AddDBParam('table_name',$table_name);
$newrecord -> AddDBParam('action','admin-view-edit');
$newrecord -> AddDBParam('user_ID',$_SESSION['user_ID']);
$newrecord -> AddDBParam('sims_session_id',$session_cookie);

$newrecordResult = $newrecord -> FMNew();

//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];
//echo '<p>$user: '.$user;
//echo '<p>$_SESSION[user_ID]: '.$_SESSION['user_ID'];
//exit;
if($user == $_SESSION['user_ID']){
?>
<html>
<head>
<meta http-equiv="refresh" content="0; url=fmp7://staff2a:staff@198.214.140.248/SIMS_2.fp7">
<script language="javascript"> 
<!-- 
setTimeout("self.close();",1000) 
//--> 
</script>

</head>
<body>
Opening FileMaker...
</body>
</html>
<?php
//header('Location: fmp7://staff2a:staff@198.214.140.248/SIMS_2.fp7');
}else{
?>
<html>
<head>
<meta http-equiv="refresh" content="0; url=fmp7://admin2:admin@198.214.140.248/SIMS_2.fp7">
<script language="javascript"> 
<!-- 
setTimeout("self.close();",1000) 
//--> 
</script>

</head>
<body>
Opening FileMaker...
</body>
</html>
<?php
//header('Location: fmp7://admin2:admin@198.214.140.248/SIMS_2.fp7');
 }
exit;
}

?>
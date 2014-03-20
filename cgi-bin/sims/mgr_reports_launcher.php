<?php
session_start();

include_once('FX/FX.php');
include_once('FX/server_data.php');

$menu = $_GET['m'];

if($menu == 'relsw'){

header('Location: fmp7://relsw:relsw@198.214.140.248/SIMS_2.fp7');
exit;
}

if($menu == 'relse'){

header('Location: fmp7://relse:relse@198.214.140.248/SIMS_2.fp7');
exit;
}

?>


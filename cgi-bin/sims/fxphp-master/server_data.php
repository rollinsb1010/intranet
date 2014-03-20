<?php
error_reporting(E_ALL);
if (! defined('DEBUG')) {
    define('DEBUG', false);         // set to true to turn debugging on
}

if (! defined('DEBUG_FUZZY')) {
    define('DEBUG_FUZZY', false);   // set to true to activate the fuzzy debugger
}

$serverIP = '198.214.140.246';

$webCompanionPort = 80;
$dataSourceType = 'FMPro7';

$webUN = 'simsuser';
$webPW = 'simsuser';

$scheme = 'http';

function fmdate( $cD, $cM, $cY ) {
	return substr( '00' . $cM, -2 ) . '/' . substr( '00' . $cD, -2 ) . '/' . $cY;
}

?>

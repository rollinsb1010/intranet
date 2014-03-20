<?php
include_once('FX.php');
include_once('server_data.php');

$testfx = new FX($serverIP,$webCompanionPort);
$testfx -> SetDBData('Manager.fp7','Contacts');
$testfx -> SetDBPassword($WebPW,$webUN);
$testfxResult = $testfx -> FMFindany();

echo $testfxResult['errorCode'];
print_r($testfxResult);

?>

<html>

<head>
<title>Testing the FX.php Connection</title>

<body>

</body>

</html>


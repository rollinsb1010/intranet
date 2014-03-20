<?php
include_once('FX/FX.php');
include_once('FX/server_data.php');

$testfx = new FX($serverIP,$webCompanionPort);
$testfx -> SetDBData('SIMS.fp7','cwp_budget_codes');
$testfx -> SetDBPassword($WebPW,$webUN);
$testfxResult = $testfx -> FMFindany();

echo $testfxResult['errorCode'];

?>

<html>

<head>
<title>Testing the FX.php Connection</title>

<body>
Records found: <?php echo $testfxResult['foundCount'];?>
</body>

</html>


<?php
session_start();

include_once('FX/FX.php');
include_once('FX/server_data.php');

$PO_ID = $_GET['id'];
$row_id = $_GET['row_id'];
$user = $_GET['user'];
?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->


<html>
<head>
<title>SIMS: Approve Purchase Requests</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<form action="pur_order_ba_attachments_upload_2.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo $PO_ID;?>" />
<input type="hidden" name="user" value="<?php echo $user;?>" />
<input type="hidden" name="row_id" value="<?php echo $row_id;?>" /><label for="file">Filename:</label>
<input type="file" name="file" id="file" /> 
<br />
<input type="submit" name="submit" value="Submit" />
</form>


</body>

</html>




<?php
session_start();

include_once('FX/FX.php');
include_once('FX/server_data.php');

$PO_ID = $_POST['id'];
$row_id = $_POST['row_id'];
$user = $_POST['user'];
?>

<?php
$allowedExts = array("doc", "docx", "xls", "xlsx", "pdf");
$extension = end(explode(".", $_FILES["file"]["name"]));
if (
	(($_FILES["file"]["type"] == "application/msword")||($_FILES["file"]["type"] == "application/vnd.ms-excel")||($_FILES["file"]["type"] == "application/pdf"))
	//&&($_FILES["file"]["size"] < 20000)
	&&in_array($extension, $allowedExts)
)
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
    echo "Result: Upload Successful!<br />";
    echo "File: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    //echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

    if (file_exists("attachments/" . $_FILES["file"]["name"]))
      {
      echo $_FILES["file"]["name"] . " already exists. ";
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "attachments/pr".$PO_ID."_".$_FILES["file"]["name"]);
      echo "Stored in: "."attachments/pr".$PO_ID."_".$_FILES["file"]["name"];
      
		#################################################
		## START: UPDATE THE PURCHASE REQUEST ##
		#################################################
		$filename = 'pr'.$PO_ID.'_'.$_FILES["file"]["name"];
		$trigger = rand();
		$update = new FX($serverIP,$webCompanionPort);
		$update -> SetDBData('Purchase_Req_Order.fp7','PO_attachments');
		$update -> SetDBPassword($webPW,$webUN);
		$update -> AddDBParam('-recid',$row_id);
		
		$update -> AddDBParam('attachment_filename',$filename);
		$update -> AddDBParam('uploaded_by',$user);
		$update -> AddDBParam('upload_trigger',$trigger);
		
		$updateResult = $update -> FMEdit();
		
		//echo  '<p>errorCode: '.$updateResult['errorCode'];
		//echo  '<p>foundCount: '.$updateResult['foundCount'];
		$recordData = current($updateResult['data']);
		#################################################
		## END: UPDATE THE PURCHASE REQUEST ##
		#################################################
      
      }
    }
  }
else
  {
  echo "Invalid file";
  }
?>

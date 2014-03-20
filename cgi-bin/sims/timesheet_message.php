<?php
session_start();

//include_once('sims_checksession.php');

#############################################################################
# Copyright 2007 by the Texas Comprehensive Center at SEDL
#
# Written by Eric Waters 06/26/2007
#############################################################################

###############################
## START: LOAD FX.PHP INCLUDES
###############################
include_once('FX/FX.php');
include_once('FX/server_data.php');
###############################
## END: LOAD FX.PHP INCLUDES
###############################
$action = $_GET['action'];
$to = $_GET['to'];
$body = $_GET['message'];

if($action == 'message') {

#############################################
## START: DISPLAY TIMESHEET MESSAGE SCREEN ##
#############################################
?>


<html>
<head>
<title>SIMS - Timesheets</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(500,500); document.message.message.focus();">
<form name="message" action="">
<input type="hidden" name="action" value="send">
<input type="hidden" name="to" value="<?php echo $_SESSION['signer_ID_owner'];?>@sedl.org">

<table width="300" cellpadding="0" cellspacing="0" border="1" bordercolor="#003745" align="center">
<tr bgcolor="#003745"><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
			
			
			<tr bgcolor="#a2c7ca"><td class="body" nowrap><strong>Send message to timesheet owner:</strong></td></tr>
			
			
			
			<tr><td class="body" nowrap>
							
							Pay Period Ending: <b><?php echo $_SESSION['current_pay_period_end'];?></b> | Timesheet ID: <b><?php echo $_SESSION['timesheet_ID'];?></b><p>			
							<table cellspacing=0 cellpadding=4 border=1 bordercolor="cccccc" width="100%" class="body">
									<tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">To:</td><td valign="top" size="100%"><?php echo $_SESSION['signer_ID_owner'];?>@sedl.org</td></tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">From:</td><td nowrap valign="top"><?php echo $_SESSION['approver_ID'];?>@sedl.org</td></tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">Message:</td><td valign="top"><textarea name="message" cols="30" rows="10" class="body"></textarea></td></tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">&nbsp;</td><td valign="top"><input type="submit" name="submit" value="Send"></td></tr>
									</tr>	
							</table>

			</td></tr>
						
			</table>
</form>

</td></tr>
</table>

</body>

</html>
<?php
###########################################
## END: DISPLAY TIMESHEET MESSAGE SCREEN ##
###########################################

} elseif($action == 'send') {

###################################
## START: SEND TIMESHEET MESSAGE ##
###################################


	$subject = $_SESSION['approver_ID'].' has a question about your timesheet';
	$message = 
	'Dear '.$_SESSION['signer_ID_owner'].','."\n\n".
	
	$_SESSION['approver_ID'].' has a question about the timesheet you submitted for the pay period ending '.$_SESSION['current_pay_period_end'].'.'."\n\n".
	
	'----------'."\n".
	
	'MESSAGE:'."\n".
	
	'----------'."\n\n".
	
	stripslashes($body)."\n\n".
	
	
	'------------------------------------------------------------------------------------------------------------------'."\n".
	
	'This is an auto-generated message from the SEDL Information Management System (SIMS)';
	
	$headers = 'From: SIMS-2@sedl.org'."\r\n".'Reply-To: '.$_SESSION['approver_ID'].'@sedl.org'."\r\n".'Bcc: ewaters@sedl.org';
	
	mail($to, $subject, $message, $headers);



?>

<html>
<head>
<title>SIMS - Timesheets</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#ffffff" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" onLoad="resizeTo(500,500)">
<form name="message" action="">
<input type="hidden" name="action" value="send">
<input type="hidden" name="to" value="<?php echo $_SESSION['timesheet_owner'];?>@sedl.org">

<table width="300" cellpadding="0" cellspacing="0" border="1" bordercolor="#003745" align="center">
<tr bgcolor="#003745"><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="cccccc" bgcolor="ffffff" width="100%">
			
			
			<tr bgcolor="#a2c7ca"><td class="body" nowrap><strong>Send message to timesheet owner:</strong></td></tr>
			
			<tr><td class="body">
			<p class="alert_small">
			<i>Your message has been sent to the timesheet owner.
			</p>
			</td></tr>
			
			<tr><td class="body" nowrap>
							Pay Period Ending: <b><?php echo $_SESSION['current_pay_period_end'];?></b> | Timesheet ID: <b><?php echo $_SESSION['timesheet_ID'];?></b><p>			
							<table cellspacing=0 cellpadding=4 border=1 bordercolor="cccccc" width="100%" class="body">
									<tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">To:</td><td valign="top" size="100%"><?php echo $_SESSION['signer_ID_owner'];?>@sedl.org</td></tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">From:</td><td nowrap valign="top"><?php echo $_SESSION['approver_ID'];?>@sedl.org</td></tr>
									<td class="body" nowrap valign="top" bgcolor="ebebeb">Message:</td><td valign="top"><?php echo stripslashes(str_replace("\r\n",'<br>',$body));?></td></tr>
									</tr>	
							</table>

			</td></tr>
			

			
			
			
			</table>
</form>

</td></tr>
</table>






</body>

</html>

<?php 

#################################
## END: SEND TIMESHEET MESSAGE ##
#################################


} ?>
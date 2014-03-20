<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');
?>

<html>
<head><div style="float:right"><a href="javascript:self.close()">Return to Travel Request</a></div>
<title>SIMS: Travel Receipts Info</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22" width="500">
<h2>SIMS: Submitting Travel Receipts:</h2>

<p class="alert_small">If you are in the same physical location as your travel admin, you can ignore these instructions and simply give your paper receipts to your travel admin who will scan and enter them into the system.</p>

<p>If you need to submit your receipts while traveling or from a remote location, receipts must be converted to digital files and e-mailed to your travel admin.</p> 

<p>There are two primary methods for creating electronic versions of your paper receipts.</p>

<ol style="padding-left:20px">
<li><strong>USING A SCANNER</strong>:<br>
If you have a scanner and know how to use it to scan your receipts, this may be the simplest method for you. Just e-mail the scanned receipt images to your travel admin. Please save the files as JPG or PDF format and create a separate file for each receipt.
<p>
<li><strong>USING YOUR SMART PHONE (or iPad)</strong>:<br>
If you have a smart phone (like an iPhone or Android) or an iPad, you can use a simple scanning or photo app to take pictures of your receipts (separate file for each receipt) and e-mail them to your travel admin. This is actually the easiest method and there are some great easy-to-use apps for scanning receipts. My recommendations are below (these are for iPhones or iPads and are available on the App Store on your device - there are also scanning apps available for Android phones on the Android Marketplace app store).

<p>
<img src="images/turbo_scan.png" align="left">
<strong>TurboScan</strong> (BEST - $2.99 but well worth it -  iPhone only):<br>
TurboScan lets you just snap a picture and the app creates a clean PDF file that can be emailed to your travel admin right from the app.<br>
<a href="https://itunes.apple.com/us/app/turboscan-quickly-scan-multipage/id342548956?mt=8" target="_blank">https://itunes.apple.com/us/app/turboscan-quickly-scan-multipage/id342548956?mt=8</a>
</p>
<p><br><p>
<img src="images/genius_scan.png" align="left">
<strong>Genius Scan</strong> (FREE - iPhone or iPad):<br>
Genius Scan also lets you snap a picture, you crop it properly, then email to your travel admin right from the app.<br>
<a href="https://itunes.apple.com/us/app/genius-scan-pdf-scanner/id377672876" target="_blank">https://itunes.apple.com/us/app/genius-scan-pdf-scanner/id377672876</a>
</p>
<p>
Just search the app store from your iPhone or iPad for these apps, then download and begin using them. If you need help setting them up, contact <a href="mailto:sims@sedl.org">sims@sedl.org</a> or call Eric Waters at 512-391-6564.
</p>
</ol>

</body>
</html>
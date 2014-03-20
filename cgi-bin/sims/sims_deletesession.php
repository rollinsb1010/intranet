<?php
session_start();
session_destroy(); // delete PHP session


//session_destroy();

// get Perl session ID from cookie
$cookie_ss_session_id = $_COOKIE["ss_session_id"];

// Declare database variables
$dbhost = "localhost";
$database="intranet";
$dbuser="intranetuser"; // none needed for the test database
$dbpass="limited"; // none needed for the test database

// connect to the database
$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mySQL');
mysql_select_db($database);

// send query to delete Perl session ID from "test.staff_sessions"
$query=" DELETE FROM staff_sessions WHERE ss_session_id='$cookie_ss_session_id'";
mysql_query($query) or die('Error, deletion query failed');

// close the db connection
mysql_close($conn);

// redirect user to staff home page
?>
		<html>
		<head>
		<META HTTP-EQUIV=REFRESH CONTENT="2;URL=/cgi-bin/mysql/staff/index.cgi">
		<title>SEDL Intranet Log Out Successful</title>
<?php
include 'http://www.sedl.org/staff/includes/header2012.txt';  // Works.
?>
<TABLE CELLPADDING=\"15\"><TR><TD>
<p>
		<p class="info">You have logged out of the SEDL intranet. <?php echo $_SESSION['user_ID'];?>
		<br><br>
		Now returning to the SEDL intranet home page.
		</p>
</td></tr></table>
<?php
include 'http://www.sedl.org/staff/includes/footer2012.txt';  // Works.
?>

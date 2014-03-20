#!/usr/bin/perl 

################################################################################
## Written by Brian Litke - SEDL Web Administrator
## This script allows state representatives to update their CSR Award entries
##
## 2002-06-05 Updated to allow for multiple FY allocations for each of the three award years per record
################################################################################

use strict;
use CGI qw/:all/;
use CGI::Carp qw(fatalsToBrowser);
use DBI;
my $dsn = "DBI:mysql:database=corp;host=localhost";
#my $dbh = DBI->connect($dsn, "corpuser", "public");
#my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
#$sth->execute;
#my $num_matches = $sth->rows;

#############################################
## START: LOAD PERL MODULES
#############################################
## THIS IS A PERL MODULE THAT FORMATS NUMBERS
use Number::Format;
# EXAMPLE OF USAGE
# my $this_number
#	my $x = new Number::Format;
#	$this_number = $x->format_number($this_number, 2, 2);
#############################################
## END: LOAD PERL MODULES
#############################################

# THIS MODULE IS PROGRAMMED BY SEDL WITH SHARED FUNCTIONS FOR STRIVING READERS
use lib '/home/httpd/html/staff/perlmodules/commoncode/'; # CONTAINS MODULES PROGRAMMED BY SEDL
use commoncode; 
use intranetcommoncode; 

my $query = new CGI;
my $debug = $query->param("debug") || "0"; # CHANGE THIS TO "1" TO TURN ON DEBUGGING PRINT STATEMENTS
my $count = 0;
my $remote_host = $ENV{"REMOTE_HOST"};
my $remote_addr = $ENV{"REMOTE_ADDR"};
my $browser = $ENV{"HTTP_USER_AGENT"};
my $location = $query->param("location");
   $location = "report" if ($location eq '');

my $htmlhead = "";
my $htmltail = "";

open(HTMLHEAD,"</home/httpd/html/staff/includes/header_withside2012.txt");
while (<HTMLHEAD>) {
	$htmlhead .= $_;
}
close(HTMLHEAD);

open(HTMLTAIL,"</home/httpd/html/staff/includes/footer_withside2012.txt");
while (<HTMLTAIL>) {
	$htmltail .= $_;
}
close(HTMLTAIL);


my $greybox_top = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#EBEBEB\"><tr><td valign=\"top\"><img src=\"/img/nav/corners-gray_01.gif\" width=\"6\" height=\"6\" alt=\" \"></td>	<td><img src=\"/resources/images/nav/spacer.gif\" width=\"6\" height=\"8\" alt=\"\"></td>	<td align=\"right\" valign=\"top\"><img src=\"/img/nav/corners-gray_01-UR.gif\" width=\"6\" height=\"6\" alt=\" \"></td></tr><tr><td><img src=\"/resources/images/nav/spacer.gif\" width=\"10\" height=\"1\" alt=\"\"></td>	<td>";

my $greybox_bottom = "</td>	<td width=\"6\"><img src=\"/resources/images/nav/spacer.gif\" width=\"10\" height=\"1\" alt=\"\"></td></tr><tr><td valign=\"bottom\"><img src=\"/img/nav/corners-gray_01-LL.gif\" width=\"6\" height=\"6\" alt=\" \"></td>	<td><img src=\"/resources/images/nav/spacer.gif\" width=\"6\" height=\"8\" alt=\"\"></td>	<td align=\"right\" valign=\"bottom\"><img src=\"/img/nav/corners-gray_01-LR.gif\" width=\"6\" height=\"6\" alt=\" \"></td></tr></table>";

my $bluebox_top = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#D8DFEB\"><tr><td valign=\"top\"><img src=\"/img/nav/corners-gray_01.gif\" width=\"6\" height=\"6\" alt=\" \"></td>	<td><img src=\"/resources/images/nav/spacer.gif\" width=\"6\" height=\"8\" alt=\"\"></td>	<td align=\"right\" valign=\"top\"><img src=\"/img/nav/corners-gray_01-UR.gif\" width=\"6\" height=\"6\" alt=\" \"></td></tr><tr><td><img src=\"/resources/images/nav/spacer.gif\" width=\"10\" height=\"1\" alt=\"\"></td>	<td>";

my $bluebox_bottom = "</td>	<td width=\"6\"><img src=\"/resources/images/nav/spacer.gif\" width=\"10\" height=\"1\" alt=\"\"></td></tr><tr><td valign=\"bottom\"><img src=\"/img/nav/corners-gray_01-LL.gif\" width=\"6\" height=\"6\" alt=\" \"></td>	<td><img src=\"/resources/images/nav/spacer.gif\" width=\"6\" height=\"8\" alt=\"\"></td>	<td align=\"right\" valign=\"bottom\"><img src=\"/img/nav/corners-gray_01-LR.gif\" width=\"6\" height=\"6\" alt=\" \"></td></tr></table>";

my $side_nav_menu_code = "";
   $side_nav_menu_code =  &intranetcommoncode::get_side_nav_menu_code("76"); # 76 is the PID for the "Online Sales Transactions" page on the intranet

		my ($head1, $head2) = split(/QQQ/,$htmlhead);
		$htmlhead = "$head1$side_nav_menu_code$head2";


####################################
## START: GET THE CURRENT DATE INFO
####################################
	use POSIX;
	my $todaysdate = POSIX::strftime('%b %e, %Y, %X', localtime(time)); # (e.g. Mar 6, 2008, 14:39:38)
	my $year = POSIX::strftime('%Y', localtime(time)); # Locale's year (e.g. 2008)
	my $month = POSIX::strftime('%m', localtime(time)); # Locale's numerical month (e.g. 03)
	my $month_name_abbr = POSIX::strftime('%b', localtime(time)); # Locale's abbreviated month name (e.g. Mar)
	my $month_name_full = POSIX::strftime('%B', localtime(time)); # Locale's full month name (e.g. March)
	my $monthdate = POSIX::strftime('%e', localtime(time)); # Date in month (e.g. 6)
	my $monthdate_wleadingzero = POSIX::strftime('%d', localtime(time)); # Date in month w/leadingzero (e.g. 06)
	my $weekday_name_abbr = POSIX::strftime('%a', localtime(time)); # Locale's abbreviated weekday name. (e.g. Thu)
	my $weekday_name_full = POSIX::strftime('%A', localtime(time)); # Locale's full weekday name. (e.g. Thursday)
	my $date_full_pretty = POSIX::strftime('%D', localtime(time)); # Full date in human-readable format  (e.g. 03/06/08)
	my $date_full_mysql = POSIX::strftime('%F', localtime(time)); # Full date in machine-readable "mysql-compatible" format (e.g. 2008-03-06)
	my $time_hour = POSIX::strftime('%l', localtime(time)); # Hour (e.g. 9 or 9)
	my $time_hour_mil = POSIX::strftime('%k', localtime(time)); # Hour in military notation (e.g. 9 or 21)
	my $time_hour_leadingzero = POSIX::strftime('%I', localtime(time)); # Hour w/leadingsero (e.g. 09 or 09)
	my $time_hour_mil_leadingzero = POSIX::strftime('%H', localtime(time)); # Hour in military notation w/leadingsero (e.g. 09 or 21)
	my $time_min = POSIX::strftime('%M', localtime(time)); # Date in month (e.g. 39)
	my $time_sec = POSIX::strftime('%S', localtime(time)); # Date in month (e.g. 38)

	my $timestamp = "$year$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; # 14-digit timestamp (e.g. 20080306143938)

#my $session_suffix = "$month$monthdate_wleadingzero$time_hour_mil_leadingzero$time_min$time_sec"; ## MAKE UNIQUE SESSION ID BY ADDING THIS SUFFIX TO DISTRICT CODE
#   $session_suffix =~ tr/-0-9//cd;

## MAKE A UNIQUE ID USING THE PERSONS IP NUMBERS AND LOGON TIME
#	my $this_user_id = "$timestamp$ipnum$ipnum2";
#	   $this_user_id =~ tr/A-Za-z0-9//cd;
####################################
## END: GET THE CURRENT DATE INFO
####################################


################################################################################################################
## START OF VARIABLE READING
################################################################################################################
my $request_type = $query->param("request_type");
my $show_transaction_id= $query->param("show_transaction_id");
my $show_approved= $query->param("show_approved");
my $detail = $query->param("detail") || "brief"; 
my $records_per_page = $query->param("records_per_page") || "50"; 
my $confirm_save = $query->param("confirm_save"); 

my $show_type = $query->param("show_type");
   $show_type = "pubs" if ($show_type eq '');

################################################################################################################
## END OF VARIABLE READING
################################################################################################################

if ($debug eq 'dont_ever_show_this') {

print<<EOM;

#drop table shoppingcart_return;

#create table shoppingcart_return (
transaction_date date,
transaction_timestamp varchar(50),
transaction_referring_ip varchar(100),
customerip varchar(100),
PNREF varchar(15) NOT NULL,
TYPE varchar(20),
RESULT varchar(50),
AUTHCODE varchar(50),
RESPMSG varchar(50),
AVSDATA varchar(50),
CSCMATCH varchar(50),
HOSTCODE varchar(50),
INVOICE varchar(9),
AMOUNT varchar(50),
TAX varchar(50),
SHIPAMOUNT varchar(50),
METHOD varchar(10),
DESCRIPTION varchar(255),
PONUM varchar(25),
CUSTID varchar(11),
NAME varchar(60),
ADDRESS varchar(120),
CITY varchar(32),
STATE varchar(20),
ZIP varchar(15),
COUNTRY varchar(32),
PHONE varchar(20),
FAX varchar(20),
EMAIL varchar(40),
NAMETOSHIP varchar(60),
ADDRESSTOSHIP varchar(120),
CITYTOSHIP varchar(32),
STATETOSHIP varchar(20),
ZIPTOSHIP varchar(15),
COUNTRYTOSHIP varchar(32),
PHONETOSHIP varchar(20),
FAXTOSHIP varchar(20),
EMAILTOSHIP varchar(40),
USER1 varchar(31),
USER2 varchar(31),
USER3 varchar(31),
USER4 varchar(31),
USER5 varchar(31),
USER6 varchar(31),
USER7 varchar(31),
USER8 varchar(31),
USER9 varchar(31),
USER10 varchar(31),
primary key(PNREF));
EOM
}


#########################################################################################
## START: LOCATION = HOW_HEARD
#########################################################################################
if ($location eq 'how_heard') {
	##################################
	## START: STANDARD HTML HEADER
	##################################
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<HTML>
<HEAD>
<TITLE>How Customers Heard About the Product</TITLE>

$htmlhead



<H2><A href="/cgi-bin/mysql/staff/index.cgi?show_s=3&show_sg=27&show_sgs=0">Product Sales Reports</A><br>
How Customers Heard About the Product</H2>
<p>
Note: SEDL began recording this information for onlne credit card sales starting 2/16/2007.
</p>
EOM
	##################################
	## END: STANDARD HTML HEADER
	##################################

my $url_for_piechart = "/cgi-bin/mysql/ChartDirector/intranet/sales-howheard.cgi?x=1";
my $table_rows = "";
	###################################################################
	## START: QUERY DATABASE OF GOOD CHECKOUTS & LOAD IDs INTO A HASH
	###################################################################
	my $command_show_data = "select USER7, COUNT(USER7) from shoppingcart_return where PNREF like '%' AND USER7 NOT LIKE 'b31' AND USER7 NOT LIKE ''";
#		$command_show_data .= " AND RESPMSG like 'Approved' " if ($show_approved ne '');
		$command_show_data .= " AND USER1 = 'PUBLICATIONS' GROUP by USER7";
	#print "$command_show_data";
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command_show_data) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
#my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
    		my ($USER7, $this_count) = @arr;
    		if ($USER7 ne 'b31') {
    			my $USER7_label = $USER7;
    			   $USER7_label =~ s/ //gi;
    			   $USER7_label =~ s/-//gi;
    			   $USER7_label = substr($USER7_label,0,5);
    			   $USER7_label = lc($USER7_label);
    			$table_rows .= "<tr><td>$USER7</td><td>$this_count</td></tr>";
    			$url_for_piechart .= "&amp;$USER7_label=$this_count";
			}
		} # END DB QUERY LOOP
	###################################################################
	## END: QUERY DATABASE OF GOOD CHECKOUTS & LOAD IDs INTO A HASH
	###################################################################
print<<EOM;
<IMG SRC="$url_for_piechart" align="right">
<table border ="1" cellpadding="2" cellspacing="0">
<tr><td bgcolor="#EBEBEB"><strong>Source</strong></td><td bgcolor="#EBEBEB"><strong>#</strong></td></tr>
$table_rows
</table>

EOM
}
#########################################################################################
## END: LOCATION = HOW_HEARD
#########################################################################################


#########################################################################################
## START: LOCATION = CHECKOUTS_NOT_RETURNED_FROM_VERISIGN
#########################################################################################
if ($location eq 'checkout_not_returned_from_verisign') {
	##################################
	## START: STANDARD HTML HEADER
	##################################
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<HTML>
<HEAD>
<TITLE>View SEDL Credit Card Checkouts Not Returned from Verisign</TITLE>

$htmlhead


<TABLE WIDTH=100% CELLPADDING=0 CELLSPACING=0 BORDER=0>
<TR><TD><H2><A href="/cgi-bin/mysql/staff/index.cgi?show_s=3&show_sg=27&show_sgs=0">Product Sales Reports</A><br>
		CC Check-outs that did not trigger a "Silent Post" back to the database from Verisign indicating a sale in the online report.</H2></TD>
	<TD VALIGN=TOP ALIGN=RIGHT>Click to log on to<BR><A HREF="https://manager.verisign.com">Verisign Manager</A>
</TD></TR>
</TABLE>

<p>
If these checkouts did proceed to Verisign for completion, SEDL will still get an e-mail notice about the sale, which is why it is important 
for SEDL's Communications staff to monitor the Verisign e-mails as the primary data source regarding new online sales.
</p>
	<TABLE CELLPADDING="2" BORDER="1" CELLSPACING="0">
EOM
	##################################
	## END: STANDARD HTML HEADER
	##################################

my %returned_cart_id;
	###################################################################
	## START: QUERY DATABASE OF GOOD CHECKOUTS & LOAD IDs INTO A HASH
	###################################################################
	my $command_show_data = "select transaction_date, transaction_timestamp, PNREF, RESPMSG, USER10 from shoppingcart_return where PNREF like '%' ";
#		$command_show_data .= " AND RESPMSG like 'Approved' " if ($show_approved ne '');
		$command_show_data .= " AND USER1 = 'PUBLICATIONS'";
	#print "$command_show_data";
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command_show_data) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
#my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
    		my ($transaction_date, $transaction_timestamp, $PNREF, $RESPMSG, $USER10) = @arr;
			$returned_cart_id{$USER10} = "y";
		} # END DB QUERY LOOP
	###################################################################
	## END: QUERY DATABASE OF GOOD CHECKOUTS & LOAD IDs INTO A HASH
	###################################################################

	##########################################################
	## START: QUERY DB OF PRE-CHECKOUTS TO LOOK FOR PROBLEMS
	##########################################################
print<<EOM;
<tr><td bgcolor="#EBEBEB"><strong>#</strong></td><td bgcolor="#EBEBEB"><strong>Timestamp</strong></td><td bgcolor="#EBEBEB"><strong>Cart Contents</strong></td><td bgcolor="#EBEBEB"><strong>E-mail</strong></td><td bgcolor="#EBEBEB"><strong>Cart ID</strong></td></tr>
EOM

my $lastmonth = "";
my $counter = 1;
			## START: SAVE STATUS OF USER'S SHOPPING CART TO DATABASE
			my $command_save_cart = "select * from shoppingcart_checkouts WHERE cart_id NOT LIKE '%sedl%' order by cart_timestamp DESC";
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command_save_cart) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
#my $num_matches = $sth->rows;
				while (my @arr = $sth->fetchrow) {
    				my ($cart_id, $cart_email, $cart_contents, $cart_timestamp) = @arr;
					my $thismonth = substr($cart_timestamp,0,6);
					if ($thismonth ne $lastmonth) {
						$counter = 1;
						if ($lastmonth ne '') {
							print "<tr><td bgcolor=\"#000000\" colspan=\"5\"><IMG SRC=\"/images/spacer.gif\" height=\"1\" width=\"200\"></td></tr>";
						}
					}
					## PRINT LINE IF NO CORRESPONDING CHECKOUT ON FILE
					if ($returned_cart_id{$cart_id} ne 'y') {
						my $cart_timestamp_label = &convert_timestamp_2pretty_w_date($cart_timestamp, "yes");
						print "<tr><td>$counter</td><td>$cart_timestamp_label</td><td>$cart_contents</td><td><A HREF=\"mailto:$cart_email\">$cart_email</A></td><td>$cart_id</td></tr>\n";
						$counter++;
					}
					$lastmonth = $thismonth;
				}
			print "</table>\n";
	##########################################################
	## END: QUERY DB OF CHECKOUTS
	##########################################################


}
#########################################################################################
## END: LOCATION = CHECKOUTS_NOT_RETURNED_FROM_VERISIGN
#########################################################################################

#########################################################################################
## START: LOCATION = REPORT
#########################################################################################
if ($location eq 'report') {


	##################################
	## START: STANDARD HTML HEADER
	##################################
print header;
print <<EOM;
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<HTML>
<HEAD>
<TITLE>

View SEDL Credit Card Transaction Data Returned from Verisign</TITLE>

$htmlhead


EOM
	##################################
	## END: STANDARD HTML HEADER
	##################################

	###########################################
	## START: SET VARRS TO REMEMBER FORM STATE
	###########################################
	my $selected5 = "";
	   $selected5 = "SELECTED" if ($records_per_page eq '5');
	my $selected15 = "";
	   $selected15 = "SELECTED" if ($records_per_page eq '15');
	my $selected50 = "";
	   $selected50 = "SELECTED" if ($records_per_page eq '50');
	my $selected100 = "";
	   $selected100 = "SELECTED" if ($records_per_page eq '100');
	my $selected200 = "";
	   $selected200 = "SELECTED" if ($records_per_page eq '200');
	my $selectedall = "";
	   $selectedall = "SELECTED" if ($records_per_page eq '99999');
	my $selected_approved = "";
	   $selected_approved = "SELECTED" if ($show_approved eq 'yes');
	my $selected_approved_no = "";
	   $selected_approved_no = "SELECTED" if ($show_approved ne 'yes');
	###########################################
	## END: SET VARRS TO REMEMBER FORM STATE
	###########################################

print<<EOM;
<TABLE WIDTH=100% CELLPADDING=0 CELLSPACING=0 BORDER=0>
<TR><TD><H2><A href="/cgi-bin/mysql/staff/index.cgi?show_s=3&show_sg=27&show_sgs=0">Product Sales Reports</A><br>
		SEDL Credit Card Transaction Data<BR>Returned from Verisign</H2></TD>
	<TD VALIGN=TOP ALIGN=RIGHT>Click to log on to<BR><A HREF="https://manager.verisign.com">Verisign Manager</A>
</TD></TR>
</TABLE>
<P>
<table>
<tr><td valign="top">
		<form action="shoppingcart-return-viewer.cgi" method=GET>
		<label for="records_per_page">Show the last</label>
		<select name="records_per_page" id="records_per_page">
		<option value="5" $selected5>5</option>
		<option value="15" $selected15>15</option>
		<option value="50" $selected50>50</option>
		<option value="100" $selected100>100</option>
		<option value="200" $selected200>200</option>
		<option value="99999" $selectedall>All</option>
		</select>

		<select name="show_approved" id="show_approved">
		<option value="yes" $selected_approved>Approved</option>
		<option value="" $selected_approved_no>Approved and Declined</option>
		</select><br>
 
<SELECT name="show_type" id="show_type">
EOM
	my @options = ("pubs", "trainings", "cc");
	my @options_labels = ("publication sales", "training registrations", "all credit card approvals");
	my $counter_options = 0;
	while ($counter_options <= $#options) {
		print "<OPTION VALUE=\"$options[$counter_options]\"";
		print " SELECTED" if ($options[$counter_options] eq $show_type);
		print ">$options_labels[$counter_options]</OPTION>";
		$counter_options++;
	} # END DB QUERY LOOP


print<<EOM;
</SELECT>

transactions.<BR>
		<INPUT TYPE="checkbox" NAME="confirm_save" VALUE="yes"> Click here to save results to a data file.<BR>
		<input TYPE="HIDDEN" NAME="detail" VALUE="$detail">
		<input TYPE="HIDDEN" NAME="show_transaction_id" VALUE="$show_transaction_id">
		<input TYPE="submit" VALUE="Show List of Transactions">
	</form>
</td>
<td valign="top" width="45%">
	<strong>Related Reports</strong>
	<ul>
	<li><a href="cross_sell_report.cgi">Cross-sell report</a> that shows which users clicked on a cross-sell in the shopping cart.</li>
	<li><a href="/staff/communications/banner_ad_report.cgi">Sales Generated by Banner Ads or e-Campaigns</a></li>
	<li><A HREF="shoppingcart-return-viewer.cgi?location=checkout_not_returned_from_verisign">CC Check-outs that did not trigger a sale from Verisign</A>.</li>
	<li><A HREF="shoppingcart-return-viewer.cgi?location=how_heard">How users heard about product</A>.</li>
	</ul>
</td></tr>
</table>

<P>
EOM

	if ($confirm_save eq 'yes') {
		open(DATAFILE,">/home/httpd/html/staff/communications/shoppincart_datafile.txt");
		print "<P><FONT COLOR=GREEN><strong>DATA FILE:</strong> A tab-delimited <A HREF=\"shoppincart_datafile.txt\">data file</A> with your search results was saved here.</FONT> <BR>
		Directions: Click the link to the <A HREF=\"shoppincart_datafile.txt\">data file</A> to view the file, then select FILE ==> SAVE AS (text only) in your Web browser to save the file to your desktop.  You can then open in in Excel or FileMaker for analysis.<P>";
		print DATAFILE "Transaction_date\t Verisign PNREF\t AUTH_CODE\tRESP_MSG\tAMOUNT\tCUSTOMER NAME\tDESCRIPTION\n";
	} # END IF

	if ($detail eq 'detailed') {
print<<EOM;
<TABLE WIDTH=100%>
<TR><TD><H3><FONT COLOR="BLUE">Transaction $show_transaction_id Detail</FONT></H3></TD>
	<TD ALIGN=RIGHT>
		<A HREF="shoppingcart-return-viewer.cgi?show_approved=$show_approved&records_per_page=$records_per_page">Back to List of Online Sales Transactions</A>
</TD></TR>
</TABLE>
EOM
	} # END IF
	print "<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=1 WIDTH=\"100%\">";




	if ($detail ne 'detailed') {
print<<EOM;
<TR BGCOLOR=CCCCCC><TD>Date</TD>
	<TD class=small>Verisign Payment Network Reference (PNREF) #</TD>
	<TD class=small>Response<BR>Message<BR><SPAN class=small>
EOM
print "<A HREF=\"/staff/communications/shoppingcart-return-viewer.cgi?show_approved=yes&records_per_page=$records_per_page\">show approved only</A>" if ($show_approved ne 'yes');
print "<A HREF=\"/staff/communications/shoppingcart-return-viewer.cgi?show_approved=&records_per_page=$records_per_page\">show approved & declined</A>" if ($show_approved eq 'yes');
print<<EOM;	
	</SPAN></TD>
	<TD class=small>Total<BR>Amount</TD>
	<TD class=small>Description</TD>
EOM
		if ($show_type ne 'trainings') {
print<<EOM;
<td class=small>Cross-site<br>Referral?</td>
EOM
		}
print<<EOM;
	<td>State</td>
	</TR>
EOM
	} # END IF
	my $bgcolor = "";
	my $last_transaction_date = "";
	my $total_cc_sales = "0";
	my $total_cc_sales_test = "0";
	my $total_registration_sales = "0";
	my $records_shown = "1";

	my $command_show_data = "select * from shoppingcart_return where PNREF like '%' ";
		$command_show_data .= " AND PNREF like '$show_transaction_id' " if ($show_transaction_id ne '');
		$command_show_data .= " AND RESPMSG like 'Approved' " if ($show_approved ne '');
		$command_show_data .= " AND USER1 LIKE 'PUBLICATIONS'" if (($show_type eq 'pubs') && ($show_transaction_id eq ''));
		$command_show_data .= " AND USER1 NOT LIKE 'PUBLICATIONS'" if ($show_type eq 'trainings');
		$command_show_data .= " order by transaction_date DESC";
#	print "$command_show_data";
my $dbh = DBI->connect($dsn, "corpuser", "public");
my $sth = $dbh->prepare($command_show_data) or die "Couldn't prepare statement: " . $dbh->errstr;
$sth->execute;
#my $num_matches = $sth->rows;
		while (my @arr = $sth->fetchrow) {
    		my ($transaction_date, $transaction_timestamp, $transaction_referring_ip, $customerip, $PNREF, $TYPE, $RESULT, $AUTHCODE, $RESPMSG, $AVSDATA, $CSCMATCH, $HOSTCODE, $INVOICE, $AMOUNT, $TAX, $SHIPAMOUNT, $METHOD, $DESCRIPTION, $PONUM, $CUSTID, $NAME, $ADDRESS, $CITY, $STATE, $ZIP, $COUNTRY, $PHONE, $FAX, $EMAIL, $NAMETOSHIP, $ADDRESSTOSHIP, $CITYTOSHIP, $STATETOSHIP, $ZIPTOSHIP, $COUNTRYTOSHIP, $PHONETOSHIP, $FAXTOSHIP, $EMAILTOSHIP, $USER1, $USER2, $USER3, $USER4, $USER5, $USER6, $USER7, $USER8, $USER9, $USER10) = @arr;
				if ($RESPMSG eq 'Approved') {
					$bgcolor = " BGCOLOR=\"B5F7C2\"";
					$bgcolor = " BGCOLOR=\"74DB87\"" if ($transaction_date eq $date_full_mysql);
					my $this_amount = $AMOUNT;
				   $this_amount =~ s/\$//;
					if ($NAME !~ 'Litke') {
						if ($USER1 eq 'PUBLICATIONS') {
							$total_cc_sales = $total_cc_sales + $this_amount;
						} else {
							$total_registration_sales = $total_registration_sales + $this_amount;
						}
					} else {
						$total_cc_sales_test = $total_cc_sales_test + $this_amount;
					}
				} elsif (($RESPMSG =~ m/Declined/i) || ($RESPMSG =~ m/Invalid/i) || ($RESPMSG =~ m/tender/i)) {
		$bgcolor = " BGCOLOR=\"F5B5B5\"";
		$bgcolor = " BGCOLOR=\"E59191\"" if ($transaction_date eq $date_full_mysql);
	}

	# MAKE THE TRANSACTION TYPE LOOK PRETTY
	my $TYPE_LABEL = $TYPE;
	   $TYPE_LABEL = "Sale" if $TYPE_LABEL eq 'S';
	   $TYPE_LABEL = "Void" if $TYPE_LABEL eq 'V';

	# MAKE THE DATE LOOK PRETTY
	if ($transaction_date ne $date_full_mysql) {
		$transaction_date = &date2standard($transaction_date);
	} else {
		$transaction_date = "<H3>Today</H3>";
	}
	############################################
	## START: DETAIL PRINTING OF SINGLE RECORD
	############################################
	if ($detail eq 'detailed') {
		while (length($customerip) > 60) {
			chop($customerip);
		}
	$transaction_date = "$transaction_date<BR>$transaction_timestamp" if ($transaction_timestamp ne '');
print<<EOM;
	<TR BGCOLOR=CCCCCC><TD><strong>transaction_date </strong></TD><TD>$transaction_date</TD></TR>
	<TR><TD><strong>PNREF</strong></TD><TD>$PNREF</TD></TR>
	<TR><TD>DESCRIPTION</TD><TD>$DESCRIPTION</TD></TR>
	<TR><TD>AMOUNT</TD><TD>$AMOUNT</TD></TR>
	<TR><TD VALIGN=TOP>Name/Address</TD><TD>$NAME<BR>$ADDRESS<BR>$CITY $STATE $ZIP $COUNTRY</TD></TR>
	<TR><TD>PHONE/FAX</TD><TD>$PHONE FAX:$FAX</TD></TR>
	<TR><TD>EMAIL / EMAILTOSHIP</TD><TD>$EMAIL / $EMAILTOSHIP</TD></TR>
	<TR><TD VALIGN=TOP>Name/Address Shipping</TD><TD>$NAMETOSHIP<BR>$ADDRESSTOSHIP<BR>
			$CITYTOSHIP $STATETOSHIP $ZIPTOSHIP $COUNTRYTOSHIP</TD></TR>
	<TR><TD>PHONE/FAX SHIPPING</TD><TD>$PHONETOSHIP FAX: $FAXTOSHIP</TD></TR>
	<TR><TD>TYPE</TD><TD>$TYPE_LABEL</TD></TR>
	<TR><TD>RESULT</TD><TD>$RESULT</TD></TR>
	<TR><TD>AUTHCODE</TD><TD>$AUTHCODE</TD></TR>
	<TR><TD>RESPMSG</TD><TD>$RESPMSG</TD></TR>
	<TR><TD>AVSDATA</TD><TD>$AVSDATA</TD></TR>
	<TR><TD>CSCMATCH</TD><TD>$CSCMATCH</TD></TR>
	<TR><TD>INVOICE</TD><TD>$INVOICE</TD></TR>
	<TR><TD>TAX</TD><TD>$TAX</TD></TR>
	<TR><TD>SHIPAMOUNT</TD><TD>$SHIPAMOUNT</TD></TR>
	<TR><TD>METHOD</TD><TD>$METHOD</TD></TR>
	<TR><TD>PONUM</TD><TD>$PONUM</TD></TR>
	<TR><TD>CUSTID</TD><TD>$CUSTID</TD></TR>
	<TR><TD>SEDL USE 1 (Pubs sale<BR> vs. Registration)</TD><TD>$USER1</TD></TR>
	<TR><TD>SEDL USE 2 (Tax Status)</TD><TD>$USER2</TD></TR>
	<TR><TD>SEDL USE 3 (Shipping)</TD><TD>$USER3</TD></TR>
	<TR><TD>SEDL USE 4<br>(Marketing Codes<br>or Referring<br>Banner Ads Clicked)</TD><TD>$USER4</TD></TR>
	<TR><TD>SEDL USE 5</TD><TD>$USER5</TD></TR>
	<TR><TD>SEDL USE 6</TD><TD>$USER6</TD></TR>
	<TR><TD>SEDL USE 7 (How did you hear about product?)</TD><TD>$USER7</TD></TR>
	<TR><TD>SEDL USE 8 (User Remote Host)</TD><TD>$USER8</TD></TR>
	<TR><TD>SEDL USE 9 (User IP Address)</TD><TD>$USER9</TD></TR>
	<TR><TD>SEDL USE 10 (SEDL Shopping Cart ID)</TD><TD>$USER10</TD></TR>
	<TR><TD>transaction_referring_ip</TD><TD>$transaction_referring_ip</TD></TR>
	<TR><TD>customerip</TD><TD class=small>$customerip</TD></TR>
	</TR>
EOM
	} else {
	############################################
	## END: DETAIL PRINTING OF SINGLE RECORD
	############################################


	my $DESCRIPTION_LABEL = $DESCRIPTION;
		
		############################################
		## START: BRIEF PRINTING OF MULTIPLE RECORDS
		############################################
		if ($records_shown <= $records_per_page) {


			# START: QUERY SEDL CATALOG TO GET NAME OF ITEM PURCHASED, LOOP THROUGH RECORDS
			if ($records_per_page ne '99999') {
				my $command = "select salesid, onlineid, title, title2, price
								from sedlcatalog where isactive LIKE 'y%' order by title";
				my $dbh = DBI->connect($dsn, "corpuser", "public");
				my $sth = $dbh->prepare($command) or die "Couldn't prepare statement: " . $dbh->errstr;
				$sth->execute;
				my $num_matches = $sth->rows;
				while (my @arr = $sth->fetchrow) {
				    my ($salesid, $onlineid, $title, $title2, $price) = @arr;
					if (($DESCRIPTION_LABEL =~ $salesid) && ($salesid ne '')) {
						my $title_small = $title;
							while (length($title_small) > 25) {
								chop($title_small);
							}
						my $onlineid_label = $onlineid;
						   $onlineid_label =~ s/\.html//g;
						$DESCRIPTION_LABEL =~ s/$salesid/\<A HREF=\"\/pubs\/index.cgi?l=item&id=$onlineid_label\"\ class=small>$salesid\ $title_small<\/A\>/;
					}
				}
			# END: QUERY SEDL CATALOG TO GET NAME OF ITEM PURCHASED, LOOP THROUGH RECORDS
			}
			## START: PRINT SPACER ROW BETWEEN DIFFERENT DAYS OF ACTIVITY
			if (($transaction_date ne $last_transaction_date) && ($last_transaction_date ne '')) {
				print "<TR><TD COLSPAN=7 BGCOLOR=\"#999999\"><IMG SRC=\"/images/spacer.gif\" HEIGHT=1 WIDTH=10></TD></TR>";
			}
			## END: PRINT SPACER ROW BETWEEN DIFFERENT DAYS OF ACTIVITY
	my $time_detail = "";
	my $time_detail_h = substr($transaction_timestamp, length($transaction_timestamp) - 8, 2);
	my $time_detail_m = substr($transaction_timestamp, length($transaction_timestamp) - 5, 2);
	if (substr($time_detail_h,0,2) > 12) {
		$time_detail_h = $time_detail_h - 12;
		$time_detail = "$time_detail_h\:$time_detail_m PM";
	} else {
		$time_detail = "$time_detail_h\:$time_detail_m AM";
	}
print<<EOM;
<TR VALIGN="TOP" $bgcolor>
	<TD class=small>$transaction_date $time_detail</TD>
	<TD class=small><A HREF="shoppingcart-return-viewer.cgi?show_transaction_id=$PNREF&detail=detailed&show_approved=$show_approved&records_per_page=$records_per_page">$PNREF</A><BR>
		$TYPE_LABEL\</TD>
	<TD class=small>$RESPMSG
EOM
print "<br />Auth: $AUTHCODE" if ($AUTHCODE ne '');
$DESCRIPTION_LABEL =~ s/\)/\) /gi;
$DESCRIPTION_LABEL =~ s/PEOPLE:/\<br\>\<br\>PEOPLE:/gi;

if ($DESCRIPTION_LABEL =~ 'Donation to SEDL') {
	$NAME = "Donor Name Removed for Privacy";
}
print<<EOM;
</TD>
	<TD class=small align="right">\$$AMOUNT</TD>
	<TD class=small>$NAME<BR>$DESCRIPTION_LABEL</TD>
EOM
		if ($show_type ne 'trainings') {
print<<EOM;
	<td class=small>$USER4</td>
EOM
		}
print<<EOM;
	<TD>$STATE</td>
	</TR>
EOM
	if ($confirm_save eq 'yes') {
		print DATAFILE "$transaction_date\t$PNREF\t$AUTHCODE\t$RESPMSG\t$AMOUNT\t$NAME\t$DESCRIPTION_LABEL\n";
	}

		}
		############################################
		## END: BRIEF PRINTING OF MULTIPLE RECORDS
		############################################
			$bgcolor = "";
			$records_shown++;
		} # END IF/THEN FOR DETAIL vs. BRIEF PRINTING
		$last_transaction_date = $transaction_date;
	} # END DB QUERY

	if ($confirm_save eq 'yes') {
		close(DATAFILE);
	}

$total_cc_sales = &format_number($total_cc_sales, "2","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
print<<EOM;
</TABLE>
<p></p>
<div class="first">
Total Credit Card sales 10/21/2003 to date: \$$total_cc_sales<BR>
Total Registration Money Processed Online to date: \$$total_registration_sales
</div>

EOM
}
#########################################################################################
## START: LOCATION = REPORT
#########################################################################################

## START: PRINT PAGE FOOTER
print<<EOM;

$htmltail
EOM
## END: PRINT PAGE FOOTER






sub cleanthisfordb {
my $dirtyitem = $_[0];
   $dirtyitem =~ s/\\//g;
   $dirtyitem =~ s/\n/ /g;
   $dirtyitem =~ s/\r/ /g;
   $dirtyitem =~ s/\t/ /g;
   $dirtyitem =~ s/'/\\'/g;
   $dirtyitem =~ s/Ô/\\Ô/g;
   $dirtyitem =~ s/Õ/\\Õ/g;
   $dirtyitem =~ s/"/\\"/g;
   $dirtyitem =~ s/=/\\=/g;
   $dirtyitem =~ s/\+/\\+/g;
   $dirtyitem =~ s/\*/\\*/g;
   $dirtyitem =~ s/&/\\&/g;
   $dirtyitem =~ s/\,/\\,/g;
   $dirtyitem =~ s/\?/\\?/g;
   $dirtyitem =~ s/\;/\\;/g;
   $dirtyitem =~ s/\:/\\:/g;
   $dirtyitem =~ s/\-/\\-/g;
   $dirtyitem =~ s/\)/\\)/g;
   $dirtyitem =~ s/\(/\\(/g;
   $dirtyitem =~ s/\{/\\{/g;
   $dirtyitem =~ s/\}/\\}/g;
   $dirtyitem =~ s/\]/\\]/g;
   $dirtyitem =~ s/\[/\\[/g;
   $dirtyitem =~ s/\|/\\|/g;
   $dirtyitem =~ s/\^/\\^/g;
   $dirtyitem =~ s/\%/\\%/g;
   $dirtyitem =~ s/\#/\\#/g;
   $dirtyitem =~ s/\@/\\@/g;
   $dirtyitem =~ s/\!/\\!/g;
#   $dirtyitem =~ s/\_/\\_/g;
   $dirtyitem =~ s/\~/\\~/g;
   $dirtyitem =~ s/\</\\</g;
   $dirtyitem =~ s/\>/\\>/g;
   return($dirtyitem);
}


sub date2standard {
my $date2transform = $_[0];
my ($thisyear,$thismonth,$thisdate) = split(/\-/,$date2transform);
   $date2transform = "$thismonth\/$thisdate\/$thisyear";
   $date2transform = "" if $date2transform eq '//';
   return($date2transform);
}



####################################################################
## START: CONVERT DATESTAMP TO PRETTY DATE/TIME FORMAT
####################################################################
sub convert_timestamp_2pretty_w_date {
my $timestamp = $_[0];
my $show_hours_minutes = $_[1];

my $this_year = substr($timestamp, 0, 4);
my $this_month = substr($timestamp, 4, 2);
my $this_date = substr($timestamp, 6, 2);
my $this_hours = substr($timestamp, 8, 2);
my $this_min = substr($timestamp, 10, 2);
my $am_pm = "AM";
	if ($this_hours > 12) {
		$this_hours = $this_hours - 12;
		$am_pm = "PM";
	}
	if ($this_hours == 12) {
		$am_pm = "PM";
	}
my $pretty_time = "$this_month/$this_date/$this_year";
	if ($show_hours_minutes eq 'yes') {
		$pretty_time .= " $this_hours:$this_min $am_pm";
	}
   return($pretty_time);
}
####################################################################
## END: CONVERT DATESTAMP TO PRETTY DATE/TIME FORMAT
####################################################################


####################################################################
## START: ROUND NUMBER TO X PLACES, WITH OR WITHOUT COMMAS
####################################################################
# EXAMPLE OF USAGE
# $num = &format_number($num, "0","yes"); # ROUND TO 0 DECIMAL PLACES WITH COMMAS (YES)
sub format_number {
	my $number_unformatted = $_[0];
	   $number_unformatted = 0 if ($number_unformatted eq '');
	   $number_unformatted =~ s/\,//g; # REMOVE COMMA IF ALREADY EXISTS 
	my $decimal_places = $_[1];
		$decimal_places = "2" if ($decimal_places eq '');
	my $commas_included = $_[2];

	my $x = new Number::Format;
	my $number_formatted = $x->format_number($number_unformatted, $decimal_places, $decimal_places);
		if ($commas_included ne 'yes') {
			$number_formatted =~ s/\,//g;
		}
	return($number_formatted);
}
####################################################################
## END: ROUND NUMBER TO X PLACES, WITH OR WITHOUT COMMAS
####################################################################

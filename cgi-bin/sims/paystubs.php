<?php
session_start();

include_once('sims_checksession.php');

//echo "StaffID Cookie: ".$_COOKIE['staffid'].'<br>';
//echo "SessionID Cookie: ".$_COOKIE['ss_session_id'];$sortfield = $_GET['sortfield'];

//if (strstr($_COOKIE['ss_session_id'],$_COOKIE['staffid'])){

$debug = 'off';
$paystub_access = 'yes';

//$sortfield = $_GET['sortfield'];

include_once('FX/FX.php');
include_once('FX/server_data.php');


$paystub_ID = $_GET['paystub_ID'];



#########################################
## START: FIND PAYSTUBS FOR THIS USER
#########################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','paystubs');
$search -> SetDBPassword($webPW,$webUN);
//$search -> FMSkipRecords($skipsize);
$search -> AddDBParam('paystub_ID','=='.$paystub_ID);
//$search -> AddDBParam('c_Active_Status','Active');
//$search -> AddDBParam('-lop','or');

//$search -> AddSortParam($sortfield,'PERIODEND');


$searchResult = $search -> FMFind();

//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r ($searchResult);
$recordData = current($searchResult['data']);

if($recordData['staff_ID'][0] != $_SESSION['staff_ID']){
echo '<img src="images/busted.jpg"><p>';

echo 'Paystub information is private.<p>
<a href="menu_paystubs.php"><< Back to menu</a>';


// LOG THIS ACTION
$ip = $_SERVER['REMOTE_ADDR']; // CAPTURE IP ADDRESS

$newrecord = new FX($serverIP,$webCompanionPort);
$newrecord -> SetDBData('SIMS_2.fp7','audit_table');
$newrecord -> SetDBPassword($webPW,$webUN);
$newrecord -> AddDBParam('user',$_SESSION['staff_ID']);
$newrecord -> AddDBParam('action','PAYSTUB_VIEW_UNAUTHORIZED_ATTEMPT');
$newrecord -> AddDBParam('table','paystubs');
$newrecord -> AddDBParam('object_ID',$paystub_ID);
$newrecord -> AddDBParam('affected_row_ID',$paystub_ID);
$newrecord -> AddDBParam('ip_address',$ip);
$newrecord -> AddDBParam('notes','Unauthorized attempt by '.$_SESSION['user_ID'].' to view paystub information for '.$recordData['NAME'][0].' for the pay period '.$recordData['paystubs_timesheets_byPeriodEnd::c_PayPeriodEnd'][0]);

$newrecordResult = $newrecord -> FMNew();
//echo  '<p>errorCode: '.$newrecordResult['errorCode'];
//echo  '<p>foundCount: '.$newrecordResult['foundCount'];



$to = 'sims@sedl.org';
$subject = 'SIMS NOTIFICATION: SECURITY ALERT';
$message = 

'SIMS has detected an unauthorized attempt to view paystub information.'."\n\n".

'---------------------------------------------------------------------------'."\n".
'User: '.$_SESSION['user_ID']."\n".
'Target paystub: '.$recordData['NAME'][0].' - '.$recordData['paystubs_timesheets_byPeriodEnd::c_PayPeriodEnd'][0]."\n".
'---------------------------------------------------------------------------'."\n\n".

'This information was saved in the SIMS audit_table.';

$headers = 'From: sims@sedl.org'."\r\n".'Reply-To: sims@sedl.org';

mail($to, $subject, $message, $headers);

exit;
}

if($debug == 'on'){
echo '#####################################<br>';
echo '######### DEBUGGING IS ON ###########<br>';
echo '#####################################<p>';

echo '<p>staff_ID: '. $_SESSION['staff_ID'];
echo '<p>$_SESSION[timesheet_approval_not_required]: '. $_SESSION['timesheet_approval_not_required'];
echo '<p>$_SESSION[last_pay_period_end]: '. $_SESSION['last_pay_period_end'];
echo '<p>$_SESSION[last_pay_period_end_m]: '. $_SESSION['last_pay_period_end_m'];
echo '<p>$_SESSION[last_pay_period_end_d]: '. $_SESSION['last_pay_period_end_d'];
echo '<p>$_SESSION[last_pay_period_end_y]: '. $_SESSION['last_pay_period_end_y'];
echo '<p>$_SESSION[current_pay_period_end]: '. $_SESSION['current_pay_period_end'];
echo '<p>$_SESSION[timesheet_owner_FTE_status]: '. $_SESSION['timesheet_owner_FTE_status'];

#######################################
## START: ECHO D-BASE PAYSTUB FIELDS ##
#######################################
echo '<p><table cellpadding="6" border="1" class="sims"><tr bgcolor="#ebebeb"><td>#</td><td>FieldName</td><td>PaystubDisplay</td><td>Value</td></tr>';

echo '<tr><td>1</td><td>NAME: '. '</td>			<td>Name</td><td>'.$recordData['NAME'][0].'</td></tr>';
echo '<tr><td>2</td><td>ADDRESS: '. '</td>		<td>Address</td><td>'.$recordData['ADDRESS'][0].'</td></tr>';
echo '<tr><td>3</td><td>ADDRESS2: '. '</td>		<td>Address2</td><td>'.$recordData['ADDRESS2'][0].'</td></tr>';
echo '<tr><td>4</td><td>CITY: '. '</td>			<td>City</td><td>'.$recordData['CITY'][0].'</td></tr>';
echo '<tr><td>5</td><td>STATE: '. '</td>		<td>ST</td><td>'.$recordData['STATE'][0].'</td></tr>';
echo '<tr><td>6</td><td>ZIPCODE: '. '</td>		<td>Zip</td><td>'.$recordData['ZIPCODE'][0].'</td></tr>';
echo '<tr><td>7</td><td>MARITALSTA: '. '</td>	<td>Marital Status</td><td>'.$recordData['MARITALSTA'][0].'</td></tr>';
echo '<tr><td>8</td><td>EXEMPTIONS: '. '</td>	<td>Number of Exemptions</td><td>'.$recordData['EXEMPTIONS'][0].'</td></tr>';
echo '<tr><td>9</td><td>FSAMED: '.'</td>		<td>NOT DISPLAYED</td><td>'.$recordData['FSAMED'][0].'</td></tr>';
echo '<tr><td>10</td><td>FSADEP: '.'</td>		<td>NOT DISPLAYED</td><td>'.$recordData['FSADEP'][0].'</td></tr>';
echo '<tr><td>11</td><td>EXTRAWITH: '.'</td>	<td>Extra Withholding</td><td>'.$recordData['EXTRAWITH'][0].'</td></tr>';
echo '<tr><td>12</td><td>RET14CODE: '.'</td>	<td>NOT DISPLAYED</td><td>'.$recordData['RET14CODE'][0].'</td></tr>';
echo '<tr><td>13</td><td>PAYCODE: '.'</td>		<td>Period (fmp_calc)</td><td>'.$recordData['PAYCODE'][0].'</td></tr>';
echo '<tr><td>14</td><td>PAYRATE: '.'</td>		<td>Monthly Pay Rate</td><td>'.$recordData['PAYRATE'][0].'</td></tr>';
echo '<tr><td>15</td><td>PERIODEND: '.'</td>	<td>Ending Date</td><td>'.$recordData['PERIODEND'][0].'</td></tr>';
echo '<tr><td>16</td><td>DIRECTDEP: '.'</td>	<td>NOT DISPLAYED</td><td>'.$recordData['DIRECTDEP'][0].'</td></tr>';
echo '<tr><td>17</td><td>CKSV: '.'</td>			<td>Type of Account</td><td>'.$recordData['CKSV'][0].'</td></tr>';
echo '<tr><td>18</td><td>CHECKNO: '.'</td>		<td>NOT DISPLAYED</td><td>'.$recordData['CHECKNO'][0].'</td></tr>';

echo '<tr><td>18-A</td><td>HSA: '.'</td>		<td>Health Savings Account (HSA)</td><td>'.$recordData['HSA'][0].'</td></tr>';
echo '<tr><td>18-B</td><td>CURHSA: '.'</td>		<td>Less: Health Savings Account (HSA) - Current</td><td>'.$recordData['CURHSA'][0].'</td></tr>';
echo '<tr><td>18-C</td><td>YTDHSA: '.'</td>		<td>Less: Health Savings Account (HSA) - YRD</td><td>'.$recordData['YTDHSA'][0].'</td></tr>';
echo '<tr><td>18-D</td><td>LABHSA: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL Current</td><td>'.$recordData['LABHSA'][0].'</td></tr>';
echo '<tr><td>18-E</td><td>LTDHSA: '.'</td>		<td>Less: Health Savings Account (HSA) - SEDL YTD</td><td>'.$recordData['LTDHSA'][0].'</td></tr>';
echo '<tr><td>18-F</td><td>LABFSAMED: '.'</td>	<td>Less: Health Care Reimbursement Account (HCRA) - SEDL Current</td><td>'.$recordData['LABFSAMED'][0].'</td></tr>';
echo '<tr><td>18-G</td><td>LTDFSAMED: '.'</td>	<td>Less: Health Care Reimbursement Account (HCRA) - SEDL YTD</td><td>'.$recordData['LTDFSAMED'][0].'</td></tr>';


echo '<tr><td>19</td><td>CURGRPAY: '. '</td>	<td>Gross Wages: Current</td><td>'.$recordData['CURGRPAY'][0].'</td></tr>';
echo '<tr><td>20</td><td>YTDGRPAY: '.'</td>	<td>Gross Wages: YTD</td><td>'.$recordData['YTDGRPAY'][0].'</td></tr>';

echo '<tr><td>21</td><td>POPHEALTH: '.'</td>	<td>Tax shelter Health Premiums</td><td>'.$recordData['POPHEALTH'][0].'</td></tr>';
echo '<tr><td>22</td><td>POPDENTAL: '.'</td>	<td>Tax shelter Dental Premiums</td><td>'.$recordData['POPDENTAL'][0].'</td></tr>';

echo '<tr><td>23</td><td>CURFSAMED: '.'</td>	<td>Healthcare FSA Account</td><td>'.$recordData['CURFSAMED'][0].'</td></tr>';
echo '<tr><td>24</td><td>YTDFSAMED: '.'</td>	<td>NOT DISPLAYED</td><td>'.$recordData['YTDFSAMED'][0].'</td></tr>';

echo '<tr><td>25</td><td>CURFSADEP: '.'</td>	<td>Dependent Care FSA Account</td><td>'.$recordData['CURFSADEP'][0].'</td></tr>';
echo '<tr><td>26</td><td>YTDFSADEP: '.'</td>	<td>NOT DISPLAYED</td><td>'.$recordData['YTDFSADEP'][0].'</td></tr>';

echo '<tr><td>27</td><td>CURSSGROSS: '.'</td>	<td>Social Security Taxable Wages: Current</td><td>'.$recordData['CURSSGROSS'][0].'</td></tr>';
echo '<tr><td>28</td><td>CURSSGROSS: '.'</td>	<td>Social Security Taxable Wages: YTD</td><td>'.$recordData['YTDSSGROSS'][0].'</td></tr>';

echo '<tr><td>29</td><td>CURMDGROSS: '.'</td>	<td>Medicare Taxable Wages: Current</td><td>'.$recordData['CURMDGROSS'][0].'</td></tr>';
echo '<tr><td>30</td><td>YTDMDGROSS: '.'</td>	<td>Medicare Taxable Wages: YTD</td><td>'.$recordData['YTDMDGROSS'][0].'</td></tr>';

echo '<tr><td>31</td><td>CURRET02: '.'</td>	<td> Less: Retirement Contribution: Current</td><td>'.$recordData['CURRET02'][0].'</td></tr>';
echo '<tr><td>32</td><td>YTDRET02: '.'</td>	<td> Less: Retirement Contribution: YTD</td><td>'.$recordData['YTDRET02'][0].'</td></tr>';

echo '<tr><td>33</td><td>CURSRA: '.'</td>	<td> Less: SRA: Current</td><td>'.$recordData['CURSRA'][0].'</td></tr>';
echo '<tr><td>34</td><td>YTDSRA: '.'</td>	<td> Less: SRA: YTD</td><td>'.$recordData['YTDSRA'][0].'</td></tr>';

echo '<tr><td>35</td><td>CURTXGROSS: '.'</td>	<td> Federal and State (W-2) Taxable Wages: Current</td><td>'.$recordData['CURTXGROSS'][0].'</td></tr>';
echo '<tr><td>36</td><td>YTDTXGROSS: '.'</td>	<td> Federal and State (W-2) Taxable Wages: YTD</td><td>'.$recordData['YTDTXGROSS'][0].'</td></tr>';

echo '<tr><td>37</td><td>CURFWTAX: '.'</td>	<td> Federal Tax Withheld: Current</td><td>'.$recordData['CURFWTAX'][0].'</td></tr>';
echo '<tr><td>38</td><td>YTDFWTAX: '.'</td>	<td> Federal Tax Withheld: YTD</td><td>'.$recordData['YTDFWTAX'][0].'</td></tr>';

echo '<tr><td>39</td><td>CURSSTAX: '.'</td>	<td> Social Security Tax Withheld: Current</td><td>'.$recordData['CURSSTAX'][0].'</td></tr>';
echo '<tr><td>40</td><td>YTDSSTAX: '.'</td>	<td> Social Security Tax Withheld: YTD</td><td>'.$recordData['YTDSSTAX'][0].'</td></tr>';

echo '<tr><td>41</td><td>CURMDTAX: '.'</td>	<td> Medicare Tax Withheld: Current</td><td>'.$recordData['CURMDTAX'][0].'</td></tr>';
echo '<tr><td>42</td><td>YTDMDTAX: '.'</td>	<td> Medicare Tax Withheld: YTD</td><td>'.$recordData['YTDMDTAX'][0].'</td></tr>';

echo '<tr><td>43</td><td>CURSTTAX: '.'</td>	<td> State Income Tax Withheld: Current</td><td>'.$recordData['CURSTTAX'][0].'</td></tr>';
echo '<tr><td>44</td><td>YTDSTTAX: '.'</td>	<td> State Income Tax Withheld: YTD</td><td>'.$recordData['YTDSTTAX'][0].'</td></tr>';

echo '<tr><td>45</td><td>CURHEALTH: '.'</td>	<td> PPO: Current</td><td>'.$recordData['CURHEALTH'][0].'</td></tr>';
echo '<tr><td>46</td><td>YTDHEALTH: '.'</td>	<td> PPO: YTD</td><td>'.$recordData['YTDHEALTH'][0].'</td></tr>';

echo '<tr><td>47</td><td>CURPTHEAL: '.'</td>	<td> Less: Premium only Health: Current</td><td>'.$recordData['CURPTHEAL'][0].'</td></tr>';
echo '<tr><td>48</td><td>YTDPTHEAL: '.'</td>	<td> Less: Premium only Health: YTD</td><td>'.$recordData['YTDPTHEAL'][0].'</td></tr>';

echo '<tr><td>49</td><td>LABHEALTH: '.'</td>	<td> SEDL: HDHP PPO: Current</td><td>'.$recordData['LABHEALTH'][0].'</td></tr>';
echo '<tr><td>50</td><td>LTDHEALTH: '.'</td>	<td> SEDL: HDHP PPO: YTD</td><td>'.$recordData['LTDHEALTH'][0].'</td></tr>';

echo '<tr><td>51</td><td>CURPRUCARE: '.'</td>	<td> HMO: Current</td><td>'.$recordData['CURPRUCARE'][0].'</td></tr>';
echo '<tr><td>52</td><td>YTDPRUCARE: '.'</td>	<td> HMO: YTD</td><td>'.$recordData['YTDPRUCARE'][0].'</td></tr>';

echo '<tr><td>53</td><td>CURPTPRU: '.'</td>	<td> NOT DISPLAYED</td><td>'.$recordData['CURPTPRU'][0].'</td></tr>';
echo '<tr><td>54</td><td>YTDPTPRU: '.'</td>	<td> NOT DISPLAYED</td><td>'.$recordData['YTDPTPRU'][0].'</td></tr>';

echo '<tr><td>55</td><td>LABPRUCARE: '.'</td>	<td> SEDL: CO-PAY PPO: Current</td><td>'.$recordData['LABPRUCARE'][0].'</td></tr>';
echo '<tr><td>56</td><td>LTDPRUCARE: '.'</td>	<td> SEDL: CO-PAY PPO: YTD</td><td>'.$recordData['LTDPRUCARE'][0].'</td></tr>';

echo '<tr><td>57</td><td>CURETNA: '.'</td>	<td> Other Insurance: Current</td><td>'.$recordData['CURETNA'][0].'</td></tr>';
echo '<tr><td>58</td><td>YTDETNA: '.'</td>	<td> Other Insurance: YTD</td><td>'.$recordData['YTDETNA'][0].'</td></tr>';

echo '<tr><td>59</td><td>CURPTETNA: '.'</td>	<td> NOT DISPLAYED</td><td>'.$recordData['CURPTETNA'][0].'</td></tr>';
echo '<tr><td>60</td><td>YTDPTETNA: '.'</td>	<td> NOT DISPLAYED</td><td>'.$recordData['YTDPTETNA'][0].'</td></tr>';

echo '<tr><td>61</td><td>LABETNA: '.'</td>	<td> SEDL: Other Insurance: Current</td><td>'.$recordData['LABETNA'][0].'</td></tr>';
echo '<tr><td>62</td><td>LTDETNA: '.'</td>	<td> SEDL: Other Insurance: YTD</td><td>'.$recordData['LTDETNA'][0].'</td></tr>';

echo '<tr><td>63</td><td>CURDENTAL: '.'</td>	<td> Dental: Current</td><td>'.$recordData['CURDENTAL'][0].'</td></tr>';
echo '<tr><td>64</td><td>YTDDENTAL: '.'</td>	<td> Dental: YTD</td><td>'.$recordData['YTDDENTAL'][0].'</td></tr>';

echo '<tr><td>65</td><td>CURPTDENT: '.'</td>	<td> Less: Premium only Dental: Current</td><td>'.$recordData['CURPTDENT'][0].'</td></tr>';
echo '<tr><td>66</td><td>YTDPTDENT: '.'</td>	<td> Less: Premium only Dental: YTD</td><td>'.$recordData['YTDPTDENT'][0].'</td></tr>';

echo '<tr><td>67</td><td>LABDENTAL: '.'</td>	<td> SEDL: Dental: Current</td><td>'.$recordData['LABDENTAL'][0].'</td></tr>';
echo '<tr><td>68</td><td>LTDDENTAL: '.'</td>	<td> SEDL: Dental: YTD</td><td>'.$recordData['LTDDENTAL'][0].'</td></tr>';

echo '<tr><td>69</td><td>CURLIFE: '.'</td>	<td> Life/AD&D Insurance: Current</td><td>'.$recordData['CURLIFE'][0].'</td></tr>';
echo '<tr><td>70</td><td>YTDLIFE: '.'</td>	<td> Life/AD&D Insurance: YTD</td><td>'.$recordData['YTDLIFE'][0].'</td></tr>';

echo '<tr><td>71</td><td>LABLIFE: '.'</td>	<td> SEDL: Life/AD&D Insurance: Current</td><td>'.$recordData['LABLIFE'][0].'</td></tr>';
echo '<tr><td>72</td><td>LTDLIFE: '.'</td>	<td> SEDL: Life/AD&D Insurance: YTD</td><td>'.$recordData['LTDLIFE'][0].'</td></tr>';

echo '<tr><td>73</td><td>CURPAI: '.'</td>	<td> Supplemental AD&D (PAI): Current</td><td>'.$recordData['CURPAI'][0].'</td></tr>';
echo '<tr><td>74</td><td>YTDPAI: '.'</td>	<td> Supplemental AD&D (PAI): YTD</td><td>'.$recordData['YTDPAI'][0].'</td></tr>';

echo '<tr><td>75</td><td>LABPAI: '.'</td>	<td> SEDL: Supplemental AD&D (PAI): Current</td><td>'.$recordData['LABPAI'][0].'</td></tr>';
echo '<tr><td>76</td><td>LTDPAI: '.'</td>	<td> SEDL: Supplemental AD&D (PAI): YTD</td><td>'.$recordData['LTDPAI'][0].'</td></tr>';

echo '<tr><td>77</td><td>CURDISAB: '.'</td>	<td> Disability: Current</td><td>'.$recordData['CURDISAB'][0].'</td></tr>';
echo '<tr><td>78</td><td>YTDDISAB: '.'</td>	<td> Disability: YTD</td><td>'.$recordData['YTDDISAB'][0].'</td></tr>';

echo '<tr><td>79</td><td>LABDISAB: '.'</td>	<td> SEDL: Disability: Current</td><td>'.$recordData['LABDISAB'][0].'</td></tr>';
echo '<tr><td>80</td><td>LTDDISAB: '.'</td>	<td> SEDL: Disability: YTD</td><td>'.$recordData['LTDDISAB'][0].'</td></tr>';

echo '<tr><td>81</td><td>CURRET14: '.'</td>	<td> NOT DISPLAYED</td><td>'.$recordData['CURRET14'][0].'</td></tr>';
echo '<tr><td>82</td><td>YTDRET14: '.'</td>	<td> NOT DISPLAYED</td><td>'.$recordData['YTDRET14'][0].'</td></tr>';

echo '<tr><td>83</td><td>LABRET14: '.'</td>	<td> SEDL: Retirement Contribution: Current</td><td>'.$recordData['LABRET14'][0].'</td></tr>';
echo '<tr><td>84</td><td>LTDRET14: '.'</td>	<td> SEDL: Retirement Contribution: YTD</td><td>'.$recordData['LTDRET14'][0].'</td></tr>';

echo '<tr><td>85</td><td>CURPARK: '.'</td>	<td> Parking: Current</td><td>'.$recordData['CURPARK'][0].'</td></tr>';
echo '<tr><td>86</td><td>YTDPARK: '.'</td>	<td> Parking: YTD</td><td>'.$recordData['YTDPARK'][0].'</td></tr>';

echo '<tr><td>87</td><td>LABPARK: '.'</td>	<td> SEDL: Parking: Current</td><td>'.$recordData['LABPARK'][0].'</td></tr>';
echo '<tr><td>88</td><td>LTDPARK: '.'</td>	<td> SEDL: Parking: YTD</td><td>'.$recordData['LTDPARK'][0].'</td></tr>';

echo '<tr><td>89</td><td>CURUNFUND: '.'</td>	<td> Other (United Fund): Current</td><td>'.$recordData['CURUNFUND'][0].'</td></tr>';
echo '<tr><td>90</td><td>YTDUNFUND: '.'</td>	<td> Other (United Fund): YTD</td><td>'.$recordData['YTDUNFUND'][0].'</td></tr>';

echo '<tr><td>91</td><td>CURNETPAY: '.'</td>	<td> Net Pay: Current</td><td>'.$recordData['CURNETPAY'][0].'</td></tr>';
echo '<tr><td>92</td><td>YTDNETPAY: '.'</td>	<td> Net Pay: YTD</td><td>'.$recordData['YTDNETPAY'][0].'</td></tr>';

echo '</table>';



echo '######################################<br>';
echo '######### END OF DEBUGGING ###########<br>';
echo '######################################<p><hr>';

#######################################
## END: ECHO D-BASE PAYSTUB FIELDS ##
#######################################


}


/*
echo '<p>last_pay_period_end: '. $_SESSION['last_pay_period_end'];
echo '<p>last_pay_period_end_m: '. $_SESSION['last_pay_period_end_m'];
echo '<p>last_pay_period_end_d: '. $_SESSION['last_pay_period_end_d'];
echo '<p>last_pay_period_end_y: '. $_SESSION['last_pay_period_end_y'];

echo '<p>timesheet_name: '. $_SESSION['timesheet_name'];
*/
#########################################
## END: FIND TIMESHEETS FOR THIS USER
#########################################

?>

<!--###DISPLAY THE SEARCH RESULTS IN AN HTML TABLE IF ANY RECORDS FOUND###-->

<?php
if ($paystub_access == 'yes') { //IF TIMESHEETS ACCESS IS TURNED ON 
?>

<html>
<head>
<title>SIMS: Paystub - <?php echo $recordData['paystubs_timesheets_byPeriodEnd::c_PayPeriodEnd'][0];?></title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
<script language="JavaScript">

function zoomWindow() {
window.resizeTo(1000,screen.height)
}

</script>

<style type="text/css">

table.stub td {
	color: #000000;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:10px;
	background-color:#ffffff;
	padding:0px ;
	border-width:0px;
	padding-right:20px;
	padding-top:2px;
	padding-bottom:2px;
	margin:0px;
	vertical-align: text-top;
	white-space: nowrap;
}


hr.ee {
border: none 0;
border-top: 1px dotted #000000;
width: 100%;
height: 1px;
margin: 0px;
text-align: left;
padding: 0px;
}





</style>



</head>

<BODY BGCOLOR="#FFFFFF" onLoad="zoomWindow()">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
		
			<tr><td class="body" nowrap style="padding:0px">SOUTHWEST EDUCATIONAL DEVELOPMENT CORPORATION<br>YEAR TO DATE PAYROLL INFORMATION</td><td align="right" style="padding:0px"><img src="/staff/sims/images/logo-new-grayscale.png" alt="SEDL-Advancing Research, Improving Education" border="0"></td></tr>

			
			<tr><td colspan="2" class="body" nowrap style="padding-left:0px;padding-top:8px">
			<?php echo $recordData['NAME'][0];?><br>
			<?php echo $recordData['ADDRESS'][0];?><br>
			<?php echo $recordData['CITY'][0];?>, <?php echo $recordData['STATE'][0];?> <?php echo $recordData['ZIPCODE'][0];?> 
			</td></tr>

			

			
			<tr><td colspan="2" style="padding-left:0px;padding-bottom:0px;padding-top:0px"><hr class="ee">
			
						<table cellpadding=4 cellspacing=0 width="100%">

								<tr><td width="33%"><u>PERSONAL DATA</u></td><td width="34%"><u>DEPOSIT DATA</u></td><td width="33%"><u>SECTION 125 ELECTIONS</u></td></tr>
		
								<tr valign="top">						
								<td class="body" style="padding-top:0px">
		
										<table cellpadding="2" cellspacing="0" class="stub">
										<tr><td>Marital Status</td><td><?php echo $recordData['MARITALSTA'][0];?></td></tr>
										<tr><td>Number of Exemptions</td><td><?php echo $recordData['EXEMPTIONS'][0];?></td></tr>
										<tr><td>Monthly Pay Rate</td><td><?php echo $recordData['PAYRATE'][0];?></td></tr>
										<tr><td>Extra Withholding</td><td><?php echo $recordData['EXTRAWITH'][0];?></td></tr>
										</table>
										
								</td><td class="body" style="padding-top:0px">

										<table cellpadding="2" cellspacing="0" class="stub">
										<tr><td>Type</td><td><?php echo $recordData['payroll_type'][0];?></td></tr>
										<tr><td>Period</td><td><?php echo $recordData['c_period_type'][0];?></td></tr>
										<tr><td>Ending Date</td><td><?php echo $recordData['paystubs_timesheets_byPeriodEnd::c_PayPeriodEnd'][0];?></td></tr>
										<tr><td>Type of Account</td><td><?php if($recordData['CKSV'][0] == 'C'){echo 'Checking';}elseif($recordData['CKSV'][0] == 'S'){echo 'Savings';}?></td></tr>
										</table>

								</td><td class="body" style="padding-top:0px">

										<table cellpadding="2" cellspacing="0" class="stub">
										<tr><td>Tax shelter Health Premiums</td><td><?php if($recordData['POPHEALTH'][0] == 'T'){ echo 'Yes';} else { echo 'No';}?></td></tr>
										<tr><td>Tax shelter Dental Premiums</td><td><?php if($recordData['POPDENTAL'][0] == 'T'){ echo 'Yes';} else { echo 'No';}?></td></tr>
										<tr><td>Healthcare FSA Account</td><td><?php if($recordData['payroll_type'][0] == 'Supplemental'){echo '0.00';} else { echo $recordData['FSAMED'][0]; }?></td></tr>
										<tr><td>Dependent Care FSA Account</td><td><?php echo $recordData['FSADEP'][0];?></td></tr>
										<tr><td>Health Savings Account (HSA)</td><td><?php echo $recordData['HSA'][0];?></td></tr>
										</table>
										
								</td></tr>
								
						</table><hr class="ee" style="padding-bottom:0px;margin-bottom:0px">
						
			</td></tr>
			<tr><td colspan="2" style="padding-left:0px;padding-top:0px">
								
						<table cellpadding=4 cellspacing=0 width="100%" class="stub">								
								<tr><td colspan="3" style="padding-top:0px">
								
										<table cellpadding="2" cellspacing="0" class="stub" width="100%" style="padding-top:0px;margin-top:0px">
										<tr><td style="padding-top:0px">&nbsp;</td><td colspan="2" align="right" style="padding-top:0px">----------Employee----------</td><td>&nbsp;</td><td>&nbsp;</td></tr>
										<tr><td>&nbsp;</td><td align="right">Current</td><td align="right">YTD</td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
										<tr><td>Gross Wages</td><td align="right">

											<?php echo $recordData['CURGRPAY'][0];?></td><td align="right">
											<?php echo $recordData['YTDGRPAY'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
										<tr><td style="padding-top:6px">Pre-tax Deductions:</td><td align="right">
										&nbsp;</td><td align="right">
										&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
											<tr><td>&nbsp;&nbsp;Less: Premium only Health</td><td align="right">
											<?php if($recordData['CURPTHEAL'][0] > 0){echo $recordData['CURPTHEAL'][0];}elseif($recordData['CURPTPRU'][0] > 0){echo $recordData['CURPTPRU'][0];}else{echo '0.00';}?></td><td align="right">
	
											<!-- <?php if($recordData['CURPTHEAL'][0] > 0){echo $recordData['YTDPTHEAL'][0];}elseif($recordData['CURPTPRU'][0] > 0){echo $recordData['YTDPTPRU'][0];}else{echo '0.00';}?></td><td>&nbsp;</td><td>&nbsp;</td></tr> -->
											<?php echo $recordData['YTDPTHEAL'][0] + $recordData['YTDPTPRU'][0];?></td><td colspan="2" align="right">----------SEDL----------</td></tr>
											
											<tr><td>&nbsp;&nbsp;Less: Premium only Dental</td><td align="right">
											<?php echo $recordData['CURPTDENT'][0];?></td><td align="right">
											<?php echo $recordData['YTDPTDENT'][0];?></td><td align="right">Current</td><td align="right">YTD</td></tr>
											
											<tr><td>&nbsp;&nbsp;Less: Health Care Reimbursement Account (HCRA)</td><td align="right">
											<?php if($recordData['payroll_type'][0] == 'Supplemental'){echo '0.00';} else { echo $recordData['CURFSAMED'][0]; }?></td><td align="right">
											<?php echo $recordData['YTDFSAMED'][0];?></td><td align="right"><?php echo $recordData['LABFSAMED'][0];?></td><td align="right"><?php echo $recordData['LTDFSAMED'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Less: Dependent Care Reimbursement Account (DCRA)</td><td align="right">
											<?php echo $recordData['FSADEP'][0];?></td><td align="right">
											<?php echo $recordData['YTDFSADEP'][0];?></td><td align="right">&nbsp;</td><td align="right">&nbsp;</td></tr>
										
											<tr><td>&nbsp;&nbsp;Less: Health Savings Account (HSA)</td><td align="right">
											<?php echo $recordData['CURHSA'][0];?></td><td align="right">
											<?php echo $recordData['YTDHSA'][0];?></td><td align="right"><?php echo $recordData['LABHSA'][0];?></td><td align="right"><?php echo $recordData['LTDHSA'][0];?></td></tr>

										<tr><td style="padding-top:6px">Social Security Taxable Wages</td><td align="right">

											<?php echo $recordData['CURSSGROSS'][0];?></td><td align="right">
											<?php echo $recordData['YTDSSGROSS'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
										<tr><td style="padding-top:6px">Medicare Taxable Wages</td><td align="right">

											<?php echo $recordData['CURMDGROSS'][0];?></td><td align="right">
											<?php echo $recordData['YTDMDGROSS'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;Less: Retirement Contribution <span class="tiny">(Employee: 2%, SEDL: 14%)</span></td><td align="right">
											<?php echo $recordData['CURRET02'][0];?></td><td align="right">
											<?php echo $recordData['YTDRET02'][0];?></td><td colspan="2" align="center" style="white-space:normal">&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;Less: SRA</td><td align="right">
											<?php echo $recordData['CURSRA'][0];?></td><td align="right">
											<?php echo $recordData['YTDSRA'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
										<tr><td style="padding-top:6px">Federal and State (W-2) Taxable Wages</td><td align="right">

											<?php echo $recordData['CURTXGROSS'][0];?></td><td align="right">
											<?php echo $recordData['YTDTXGROSS'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
										
										<tr><td style="padding-top:6px">Taxes Withheld:</td><td align="right">

											&nbsp;</td><td align="right">
											&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;Federal Tax Withheld</td><td align="right">
											<?php echo $recordData['CURFWTAX'][0];?></td><td align="right">
											<?php echo $recordData['YTDFWTAX'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;Social Security Tax Withheld</td><td align="right">
											<?php echo $recordData['CURSSTAX'][0];?></td><td align="right">
											<?php echo $recordData['YTDSSTAX'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;Medicare Tax Withheld</td><td align="right">
											<?php echo $recordData['CURMDTAX'][0];?></td><td align="right">
											<?php echo $recordData['YTDMDTAX'][0];?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
											
											<tr><td>&nbsp;&nbsp;State Income Tax Withheld</td><td align="right">
											<?php echo $recordData['CURSTTAX'][0];?></td><td align="right">
											<?php echo $recordData['YTDSTTAX'][0];?></td><td colspan="2" align="right">----------SEDL----------</td></tr>
										
										<tr><td style="padding-top:6px">After-tax Deductions:</td><td align="right">
	
											&nbsp;</td><td align="right">
											&nbsp;</td><td align="right">Current</td><td align="right">YTD</td></tr>
											
											<tr><td>&nbsp;&nbsp;HDHP PPO</td><td align="right">
											<?php echo $recordData['CURHEALTH'][0];?></td><td align="right">
											<?php echo $recordData['YTDHEALTH'][0];?></td><td align="right">
											<?php echo $recordData['LABHEALTH'][0];?></td><td align="right">
											<?php echo $recordData['LTDHEALTH'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;CO-PAY PPO</td><td align="right">
											<?php echo $recordData['CURPRUCARE'][0];?></td><td align="right">
											<?php echo $recordData['YTDPRUCARE'][0];?></td><td align="right">
											<?php echo $recordData['LABPRUCARE'][0];?></td><td align="right">
											<?php echo $recordData['LTDPRUCARE'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Other Insurance</td><td align="right">
											<?php echo $recordData['CURETNA'][0];?></td><td align="right">
											<?php echo $recordData['YTDETNA'][0];?></td><td align="right">
											<?php echo $recordData['LABETNA'][0];?></td><td align="right">
											<?php echo $recordData['LTDETNA'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Dental</td><td align="right">
											<?php echo $recordData['CURDENTAL'][0];?></td><td align="right">
											<?php echo $recordData['YTDDENTAL'][0];?></td><td align="right">
											<?php echo $recordData['LABDENTAL'][0];?></td><td align="right">
											<?php echo $recordData['LTDDENTAL'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Life/AD&D Insurance</td><td align="right">
											<?php echo $recordData['CURLIFE'][0];?></td><td align="right">
											<?php echo $recordData['YTDLIFE'][0];?></td><td align="right">
											<?php echo $recordData['LABLIFE'][0];?></td><td align="right">
											<?php echo $recordData['LTDLIFE'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Supplemental AD&D (PAI)</td><td align="right">
											<?php echo $recordData['CURPAI'][0];?></td><td align="right">
											<?php echo $recordData['YTDPAI'][0];?></td><td align="right">
											<?php echo $recordData['LABPAI'][0];?></td><td align="right">
											<?php echo $recordData['LTDPAI'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Disability</td><td align="right">
											<?php echo $recordData['CURDISAB'][0];?></td><td align="right">
											<?php echo $recordData['YTDDISAB'][0];?></td><td align="right">
											<?php echo $recordData['LABDISAB'][0];?></td><td align="right">
											<?php echo $recordData['LTDDISAB'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Retirement Contribution*</td><td align="right">
											<?php echo '0.00';?></td><td align="right">
											<?php echo '0.00';?></td><td align="right">
											<?php echo $recordData['LABRET14'][0];?></td><td align="right">
											<?php echo $recordData['LTDRET14'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Parking</td><td align="right">
											<?php echo $recordData['CURPARK'][0];?></td><td align="right">
											<?php echo $recordData['YTDPARK'][0];?></td><td align="right">
											<?php echo $recordData['LABPARK'][0];?></td><td align="right">
											<?php echo $recordData['LTDPARK'][0];?></td></tr>
											
											<tr><td>&nbsp;&nbsp;Other (United Fund)</td><td align="right">
											<?php echo $recordData['CURUNFUND'][0];?></td><td align="right">
											<?php echo $recordData['YTDUNFUND'][0];?></td><td align="right">
											&nbsp;</td><td align="right">
											&nbsp;</td></tr>
										
										<tr><td style="padding-top:8px">Net Pay</td><td align="right">
										<?php echo $recordData['CURNETPAY'][0];?></td><td align="right">
										<?php echo $recordData['YTDNETPAY'][0];?></td><td align="right">
										&nbsp;</td><td align="right">
										&nbsp;</td></tr>
										

										</table>
								
								</td></tr>
								
			
						</table>

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>

<?php } else { //IF PAYSTUBS ACCESS IS TURNED OFF?>

<html>
<head>
<title>SIMS: Paystubs</title>
<link href="/staff/includes/sims2007.css" rel="stylesheet" type="text/css">
</head>

<BODY BGCOLOR="#FFFFFF" LEFTMARGIN="30" RIGHTMARGIN="30" TOPMARGIN="22" MARGINWIDTH="30" MARGINHEIGHT="22">

<table width="930" cellpadding="0" cellspacing="0" border="0">
<tr><td>&nbsp;</td></tr>
<tr><td>

			<table cellpadding=10 cellspacing=0 border=0 bordercolor="ebebeb" bgcolor="ffffff" width="100%">
			
			<tr><td colspan="2" bgcolor="#003745"><img src="/staff/sims/images/header-logo.gif" width="811" height="45" alt="SEDL-Advancing Research, Improving Education"></td></tr>
		
			<tr><td height="33" colspan="2" scope="row"><h1>SIMS PAYSTUBS</h1><hr /></td></tr>
			<tr><td class="body" nowrap><b><?php echo $_SESSION['timesheet_name'];?> (<?php echo $_SESSION['workgroup'];?>)</b></td><td align="right"><a href="sims_menu.php?src=intr">SIMS Home</a></td></tr>
			
			<?php if($_SESSION['timesheet_signed_staff'] == '1'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your timesheet has been successfully submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>
			
			<?php } elseif($_SESSION['timesheet_signed_staff'] == '1_revised'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">Your revised timesheet has been successfully re-submitted to SIMS. <img src="/staff/sims/images/green_check.png"></p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>


			<?php } elseif($_SESSION['timesheet_signed_staff'] == '2'){ ?>
			
				<tr><td class="body" nowrap colspan="2"><p class="alert_small">There was a problem submitting your timesheet, please contact <a href="mailto:ewaters@sedl.org">technical support</a> for assistance (errorCode_998).  </p></td></tr>
				<?php $_SESSION['timesheet_signed_staff'] = ''; ?>
			
			<?php } ?>
			
			
			<tr><td colspan="2">
			
			PAYSTUBS ACCESS IS TEMPORARILY UNAVAILABLE

			</td></tr>
			</table>



</td></tr>
</table>


</body>

</html>


<?php } ?>

<?php //} else { ?>



<?php //} ?>
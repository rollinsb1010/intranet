<?php
session_start();

include_once('../sims_checksession.php');

#############################################################################
# Copyright 2011 by the Texas Comprehensive Center at SEDL
#
# Written by Eric Waters 03/16/2011
#############################################################################

###############################
## START: LOAD FX.PHP INCLUDES
###############################
include_once('../FX/FX.php');
include_once('../FX/server_data.php');
###############################
## END: LOAD FX.PHP INCLUDES
###############################

$sims_on = 'yes';

if($sims_on == 'no'){
echo 'SIMS is undergoing maintenance. Please try again later. | <a href="http://www.sedl.org/staff">Return to intranet</a>';
exit;
}

#####################################################
## START: FIND CONTACT RECORD FOR THIS USER
#####################################################
$search = new FX($serverIP,$webCompanionPort);
$search -> SetDBData('SIMS_2.fp7','staff');
$search -> SetDBPassword($webPW,$webUN);
$search -> AddDBParam('staff_ID','=='.$_SESSION ['staff_ID']);

$searchResult = $search -> FMFind();
//echo $searchResult['errorCode'];
//echo $searchResult['foundCount'];
//print_r($search);
$recordData = current($searchResult['data']);
$update_row = $recordData['c_cwp_row_ID'][0];
$rand_num = rand();
#####################################################
## END: FIND CONTACT RECORD FOR THIS USER
#####################################################

$_SESSION['user_ID'] = $recordData['sims_user_ID'][0];
$_SESSION['timesheet_name'] = $recordData['name_timesheet'][0];
$_SESSION['leave_requests_access'] = $recordData['cwp_sims_access_leave_requests'][0];

//echo $_SESSION['user_id'];


$current_pay_period = date("m").'/'.date("t").'/'.date("Y");
//echo '<p>$current_pay_period: '.$current_pay_period;
?> <!--END: LOGIN AND BEGIN SESSION-->




<!--###DISPLAY THE MAIN MENU IF LOGIN IS VALID###-->


<?php



if ($recordData['cwp_sims_access_main_menu'][0] != 'Yes') { 
echo 'Your account does not have access to SIMS. Please contact <a href="mailto:tracy.hoes@sedl.org">Tracy Hoes</a> in Administrative Services for more information.<p>
<a href="http://www.sedl.org/staff"><< Return to SEDL Intranet</a>
';
exit;
}
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8" />

<title>SIMS: Main Menu</title>

        <style type="text/css" media="screen">@import "/cgi-bin/isp/jqTouch/jqtouch-1.0-beta-2-r109/jqtouch/jqtouch.min.css";</style>
        <style type="text/css" media="screen">@import "/cgi-bin/isp/jqTouch/jqtouch-1.0-beta-2-r109/themes/apple/theme.min.css";</style>
        <script src="/cgi-bin/isp/jqTouch/jqtouch-1.0-beta-2-r109/jqtouch/jquery.1.3.2.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="/cgi-bin/isp/jqTouch/jqtouch-1.0-beta-2-r109/jqtouch/jqtouch.min.js" type="application/x-javascript" charset="utf-8"></script>
        <!-- 
            jQTouch Clock Demo
            All custom code is embedded below:
        -->
        <link rel="stylesheet" href="/cgi-bin/isp/jqTouch/jqtouch-1.0-beta-2-r109/demos/clock/clock.css" type="text/css" media="screen" charset="utf-8" />
        <script type="text/javascript" charset="utf-8">
            $.jQTouch({
                icon: '/cgi-bin/isp/jqTouch/jqtouch-1.0-beta-2-r109/demos/clock/icon.png',
                startupScreen: '/cgi-bin/isp/jqTouch/jqtouch-1.0-beta-2-r109/demos/clock/img/startup.png'
            });
            $(function(){
                function addClock(label, tz){
                    var html = '';
                    html += '<div>'
                    html +=     '<div class="clock">'
                    html +=         '<div class="hour"></div>'
                    html +=         '<div class="min"></div>'
                    html +=         '<div class="sec"></div>'
                    html +=     '</div>'
                    html +=     '<div class="city">GMT</div>'
                    html +=     '<div class="time">Time</div>'
                    html += '</div>'
                    var insert = $(html);
                    $('#clocks').append(
                        insert.data('tz_offset', tz).find('.city').html(label).end()
                    );
                }
                function updateClocks(){
                    var localTime = new Date();
                    $('#clocks > div').each(function(){
                        var tz_offset = $(this).data('tz_offset') || 0;
                        var ms = localTime.getTime() 
                             + (localTime.getTimezoneOffset() * 60000)
                             + (tz_offset + 1) * 3600000;
                        var time = new Date(ms);
                        var hour = time.getHours();
                        var minute = time.getMinutes();
                        var second = time.getSeconds();
                        var $el = $(this);
                        var ampm = 'AM';
                        var nicehour = hour;
                        if (hour > 12 ) {
                            nicehour = hour - 12;
                            ampm = 'PM';
                        } else if ( hour == 0 ) {
                            nicehour = 12;
                        }
                        $('.hour', $el).css('-webkit-transform', 'rotate(' + ( hour * 30 + (minute/2) ) + 'deg)');
                        $('.min', $el).css('-webkit-transform', 'rotate(' + ( minute * 6 ) + 'deg)');
                        $('.sec', $el).css('-webkit-transform', 'rotate(' + ( second * 6 ) + 'deg)');
                        $('.time', this).html(nicehour + ':' + minute + ':' + second + ' ' + ampm);
                    });
                }
                $('#time').submit(function(){
                    addClock($('#label').val(), Number($('#timezone').val()));
                    $('input').blur();
                    $('#add .cancel').click();
                    this.reset();
                    return false;
                });
                addClock('Philadelphia', -5.0);
                addClock('Los Angeles', -8.0);
                updateClocks();
                setInterval(updateClocks, 1000);
            });
        </script>



</head>

<body>



<div id="home">
<div class="toolbar">
				<h1>SIMS Main Menu</h1>
                <a href="#info" class="button leftButton flip">Info</a>
                <a href="#add" class="button add slideup">+</a>
</div>
</div>

        <div class="form" id="add">
            <div class="toolbar">
                <h1>Pending Requests</h1>
                <a href="#add" class="cancel">Cancel</a>
            </div>
            <form id="time">
                <ul class="rounded">
                    <li><input type="text" id="label" name="Label" placeholder="Label"></li>
                    <li><select name="timezone" id="timezone">
                          <option value="-12.0">(GMT -12:00) Eniwetok</option>
                          <option value="-11.0">(GMT -11:00) Samoa</option>
                          <option value="-10.0">(GMT -10:00) Hawaii</option>
                          <option value="-9.0">(GMT -9:00) Alaska</option>
                          <option value="-8.0">(GMT -8:00) Pacific Time</option>
                          <option value="-7.0">(GMT -7:00) Mountain Time</option>
                          <option value="-6.0">(GMT -6:00) Central Time</option>
                          <option value="-5.0">(GMT -5:00) Eastern Time</option>
                          <option value="-4.0">(GMT -4:00) Atlantic Time</option>
                          <option value="-3.5">(GMT -3:30) Newfoundland</option>
                          <option value="-3.0">(GMT -3:00) Brazil</option>
                          <option value="-2.0">(GMT -2:00) Mid-Atlantic</option>
                          <option value="-1.0">(GMT -1:00 hour) Azores</option>
                          <option value="0.0" selected>(GMT) Western Europe Time</option>
                          <option value="1.0">(GMT +1:00 hour) Paris</option>
                          <option value="2.0">(GMT +2:00) Kaliningrad</option>
                          <option value="3.0">(GMT +3:00) Baghdad</option>
                          <option value="3.5">(GMT +3:30) Tehran</option>
                          <option value="4.0">(GMT +4:00) Abu Dhabi</option>
                          <option value="4.5">(GMT +4:30) Kabul</option>
                          <option value="5.0">(GMT +5:00) Ekaterinburg</option>
                          <option value="5.5">(GMT +5:30) New Delhi</option>
                          <option value="5.75">(GMT +5:45) Kathmandu</option>
                          <option value="6.0">(GMT +6:00) Colombo</option>
                          <option value="7.0">(GMT +7:00) Bangkok</option>
                          <option value="8.0">(GMT +8:00) Hong Kong</option>
                          <option value="9.0">(GMT +9:00) Tokyo</option>
                          <option value="9.5">(GMT +9:30) Adelaide</option>
                          <option value="10.0">(GMT +10:00) Eastern Australia</option>
                          <option value="11.0">(GMT +11:00) Solomon Islands</option>
                          <option value="12.0">(GMT +12:00) Fiji</option>
                    </select></li>
                </ul>
                <a href="#" style="margin: 10px;" class="whiteButton submit">Add Clock</a>
            </form>
        </div>
        <div id="info">
            <div class="toolbar">
                <h1>About</h1>
                <a href="#add" class="cancel">Cancel</a>
            </div>
            <div class="info">
                This is a demo for jQTouch.
            </div>
        </div>
    </body>
</html>
		

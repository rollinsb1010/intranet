<?php
session_start();

echo '<p>login_status: '.$_SESSION['login_status'];
echo '<p>staff_ID: '.$_SESSION['staff_ID'];
echo '<p>user_ID: '.$_SESSION['user_ID'];
echo '<p>staff_name: '.$_SESSION['staff_name'];
echo '<p>staff_email: '.$_SESSION['staff_email'];
echo '<p>timesheet_name: '.$_SESSION['timesheet_name'];
echo '<p>workgroup: '.$_SESSION['workgroup'];
echo '<p>PrimarySEDLWorkgroup: '.$_SESSION['PrimarySEDLWorkgroup'];
echo '<p>payperiod_type: '.$_SESSION['payperiod_type'];
echo '<p>immediate_supervisor: '.$_SESSION['immediate_supervisor'];
echo '<p>primary_bgt_auth: '.$_SESSION['primary_bgt_auth'];
echo '<p>employee_type: '.$_SESSION['employee_type'];
echo '<p>title: '.$_SESSION['title'];
echo '<p>employee_FTE_status: '.$_SESSION['employee_FTE_status'];
echo '<p>svc_log_admin_wg: '.$_SESSION['svc_log_admin_wg'];
echo '<p>svc_log_admin_sedl: '.$_SESSION['svc_log_admin_sedl'];
echo '<p>svc_log_admin_prgms: '.$_SESSION['svc_log_admin_prgms'];
echo '<p>svc_log_admin_spvsr: '.$_SESSION['svc_log_admin_spvsr'];
echo '<p>svc_log_admin_allow_surrogates: '.$_SESSION['svc_log_admin_allow_surrogates'];

?>
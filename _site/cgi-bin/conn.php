<?php
// Info for Auris web access
$URL = 'https://auristechnology.com/webservices/testtransactions.asp';
$OptArr = array('user_id'=>'scretail');
$OptArr['user_pw'] = 'scr1111';
$OptArr['response_type'] = '03';

// Info for MySQL connection for access numbers
$db_host = "localhost";
$db_user = "airtel_usr";
$db_pwd = "airtel_pwd";
$db_name = "AirtelDirect";

mysql_connect($db_host,$db_user,$db_pwd);
@mysql_select_db($db_name);
?>

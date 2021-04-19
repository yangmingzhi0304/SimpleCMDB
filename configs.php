<?php

define('DEBUG',false);

if(DEBUG)
{
    error_reporting(1);
    ini_set('display_errors','On');
} else {
    error_reporting(0);
    ini_set('display_errors','Off');
}

# MongoDB连接信息
$dbhost = 'localhost';
$dbport = '27017';
$dbuser = 'ITAsset';
$dbpass = 'ITAssetPass';
$dbname = 'infrastructure';

$cmdb_time  = '_meta_simple_cmdb_update_time'; # 本字段用于记录该条资产在cmdb内的修改时间，不是用户提交的数据。
$username   = @trim($_SERVER['PHP_AUTH_USER']);
if(empty($username))
{
    $username='匿名用户';
    # 您也可以在这里自行实现权限控制
}














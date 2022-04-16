<?php
if (extension_loaded('zlib')){
    ob_end_clean();
    ob_start('ob_gzhandler');
}
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
error_reporting(E_ALL ^ E_NOTICE);
define('APP_DEBUG',true);
define('APP_PATH','./Application/');
define('PLUGIN_PATH','plugins/');
define('UPLOAD_PATH','Public/upload/');
define('TPSHOP_CACHE_TIME',-1);
$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
define('SITE_URL',$http_type.$_SERVER['HTTP_HOST']);
define('HTML_PATH','./Application/Runtime/Html/');
require './ThinkPHP/ThinkPHP.php';
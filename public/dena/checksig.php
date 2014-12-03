<?php
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );
define( "ROOT_DIR" , dirname( __FILE__ ) . '/../..' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config" );
define( "LIB_DIR" , ROOT_DIR ."/lib" );
require LIB_DIR .'/Core.php';
include_once( 'callback/include/php/config.php' );
include_once( 'callback/include/php/RestApi/RestApi.class.php' );
//$app = new  RestApi(_Consumer_Key,_Consumer_Secret,_App_ID);
//echo $chsig=$app->Api('checkSign_RSA_SHA1',array('uid'=>'29489','fields'=>'id,nickname,thumbnailUrl,profileUrl,age,gender'));
echo json_encode(array('sig_valid'=>true));			
?>


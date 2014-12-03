<?php
header( "Status:200 OK" );
header( "Content-Type:text/plain" ); 
echo "OK";
/*
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );
define( "ROOT_DIR" , dirname( __FILE__ ) . '/../../..' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config" );
define( "MOD_DIR" , ROOT_DIR ."/model" );
define( "CON_DIR" , ROOT_DIR ."/controller/".SNS );
define( "TPL_DIR" , ROOT_DIR ."/tpl/" . SNS );
define( "LIB_DIR" , ROOT_DIR ."/lib" );
require LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';
require CON_DIR .'/Api.php';
include_once( 'include/php/RestApi/config.php' );
include_once( 'include/php/RestApi/RestApi.class.php' );
$app = new  RestApi(_Consumer_Key,_Consumer_Secret,_App_ID);
$chsig=$app->Api('checkSign_HMAC_SHA1',array());

if( $chsig['sig_valid'] ) 
{
//{"eventtype":"event.addapp","id":"57086","mbga_invite_from":"29322","opensocial_app_id":"12012139"} 
$eventtype=$_REQUEST['eventtype'];//event.addapp 事件类型
$id=$_REQUEST['id'];//57086  被邀请者
$mbga_invite_from=$_REQUEST['mbga_invite_from'];//29322 邀请者
$opensocial_app_id=$_REQUEST['opensocial_app_id'];//12012139 游戏应用id
//判断邀请者是否已安装的玩家

 //添加邀请功能代码
header( "Status:200 OK" );
header( "Content-Type:text/plain" ); 
echo "OK";
}
*/
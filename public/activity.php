<?php
/**
 * 活动页面
 * @category   public
 * @author     ming
 * @version    $Id: activity.php 1 2012-03-08 10:39:36Z $
 */

header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );

define( "ROOT_DIR" , dirname( __FILE__ ) . '/..' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config" );
define( "MOD_DIR" , ROOT_DIR ."/model" );
define( "API_DIR" , ROOT_DIR ."/api" );
define( "CON_DIR" , ROOT_DIR ."/controller/".SNS );
define( "LIB_DIR" , ROOT_DIR ."/lib" );

require LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';
require CON_DIR .'/Api.php';
require API_DIR . "/Base.php";
require API_DIR . "/Activity.php";
$cache = Common::getCache();

$user_key = $_GET["_session"];//平台uid
//判断用户是否为真实用户
$sharding = Common::getUid($user_key);
$uid = $sharding['uid'];
$api = Api::getInstance();
if(defined('SSNS') && SSNS=='QQ')
{
	$admin_ustr = $cache->get('for_admin');
	if($admin_ustr != $user_key)
	{
		$islogin = $api->checkLogin();
		if(!$islogin)
		{
			exit('非登录状态，请先登录！');
		}
		//屏蔽玩家
		//10302,79811,137370
		$outuid = array();
		if(in_array($uid, $outuid))
		{
			//exit('此玩家处于黑名单中');
		}
	}
}

$Activity = new Activity();
$Activity->uid = $uid;
$act = $_GET['act'];
$type = intval($_GET['type']);
$Activity->type = $type;
switch($act)
{
	case 'vipreword':
		$mess = $Activity->getVipDaily();
		break;
	case 'invitereword':	
		$mess = $Activity->getInviteReword();
		break;
	case 'signinreword':
		$param[] = intval($_GET['rtype']);
		$mess = $Activity->getSigninReword($param);
		break;
	case 'daysign':
		$mess = $Activity->getDaySign();
		break;
	case 'levelreword':
		$param[] = intval($_GET['rtype']);
		$mess = $Activity->getLevelReword($param);
		break;
	default:
		break;
}
echo json_encode($mess);
	

<?php
/**
 * 入口
 *
 * @category   public
 * @author     fisher.lee<63764977@qq.com>
 * @version    $Id: command.php 2012-03-01 10:19:44Z$
 */
$microtime = microtime(true); 
// $xhprof_on = false;
// if($xhprof_on) xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY); //启动xhprof

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
define( "TPL_DIR" , ROOT_DIR ."/tpl/" . SNS );
define( "LIB_DIR" , ROOT_DIR ."/lib" );

require LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';
require CON_DIR .'/Api.php';
require API_DIR . "/Base.php";
require API_DIR . "/Command.php";

$Command = new Command();
$cache = Common::getCache();

$user_key = $_GET["_session"];//平台uid
//判断用户是否为真实用户
$sharding = Common::getUid($user_key);
$Command->uid = $uid = $sharding["uid"];//游戏uid



if(defined('CLOSE') && CLOSE)
{
	$testustr = $cache->get('sandbox_ustrs');
	$testustr = empty($testustr)?array():$testustr;
	$ustr_array = array();
	$ustr_array = array_merge($ustr_array,$testustr);
	if(!in_array($user_key, $ustr_array))
	{
		echo "<font color=red size=2>系统升级，给你造成不便，将多谅解……</font>";
		exit();
	}

}

$api = Api::getInstance();

$admin_ustr = $cache->get('for_admin');
if($admin_ustr != $user_key)
{
    $islogin = $api->checkLogin();
    if(!$islogin){
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




/*
 [
{"id":0,"method":"ProductApi.getUserCurrency","params":[]},
{"id":1,"method":"ProductApi.getAlternativeCurrency","params":[]},
{"id":2,"method":"ProductApi.getAllProductPackages","params":[]},
{"id":3,"method":"StarLevelApi.getLevels","params":[]},
{"id":4,"method":"SocialUserApi.getAppFriends","params":[]},
{"id":5,"method":"SocialUserApi.getCurrentUser","params":[]},
{"id":6,"method":"BoosterApi.getBoosters","params":[]},
{"id":7,"method":"AppointmentApi.getAppointments","params":[]},
{"id":8,"method":"CollectionFeatureApi.getAllCollectibles","params":[]},
{"id":9,"method":"CollectionFeatureApi.getCollections","params":[]},
{"id":10,"method":"VirtualCurrencyApi.getBalance","params":[]},
{"id":11,"method":"StaticFileManagerApi.getFiles","params":[true]},
{"id":12,"method":"LifeApi.getLife","params":[]},
{"id":13,"method":"LifeApi.getMaxLives","params":[]},
{"id":14,"method":"CollaborationApi.getCollaborationContainers","params":[[1,2,3]]},
{"id":15,"method":"MessageApi.fetchAndDeleteMessages","params":[]},
{"id":16,"method":"TutorialProgressionApi.getUserTutorialProgression","params":[]},
{"id":17,"method":"TimeApi.getUserTime","params":[]}
]
*/
$post = file_get_contents("php://input");
if(empty($post))
{
	exit('params_is_error');
}
$data= json_decode($post,true);
if(!empty($data))
{
	
	$apis = array();//按顺序保存所有的model
	$list = array();
	$result = array();
	foreach($data as $value)
	{
		$params[0] = $value['id'];
		$params[1] = $value['params'];
		$method = $value['method'];
		list($api,$act) = explode('.', $method);
		$apis[$value['id']] = $api;
		$list[$value['id']][0] = $params;
		$list[$value['id']][1] = $act;
	}
	
	if(!empty($apis) && !empty($list))
	{
		$apilist = array_unique($apis);
		foreach($apilist as $apiname)
		{
			//require API_DIR . "/".$apiname.".php";
			//$$apiname = new $apiname($uid);
			//$$apiname->uid = $uid;
		}

		foreach($list as $k => $control){
			$param = empty($control[0])?'' : $control[0];
			$Command->params = $param;
            $act = $control[1];
			$result[] = $Command->$act($param);
		}
		$result = array_merge($result);
		echo json_encode($result);
	}
}
else
{
	echo json_encode(array('error'=>'is_error'));
}





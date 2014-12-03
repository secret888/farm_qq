<?php
/**
 * 程序入口
 *
 * @category   public
 * @author     fisher.lee<63764977@qq.com>
 * @version    $Id: index.php 2011-05-22 14:53:20Z fisher.lee$
 */
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );
session_start();
error_reporting(E_ALL^E_NOTICE);
define( "ROOT_DIR" , dirname( __FILE__ ) . '' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config" );
define( "MOD_DIR" , ROOT_DIR ."/model" );
define( "CON_DIR" , ROOT_DIR ."/controller/".SNS );
define(	"CTRL_DIR", ROOT_DIR ."/controller/");
define( "TPL_DIR" , ROOT_DIR ."/tpl/" . SNS );
define( "LIB_DIR" , ROOT_DIR ."/lib" );

require LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';
require CTRL_DIR .'/Controller.php';
require CON_DIR .'/Api.php';
Common::loadModel("CommonModel");
Common::loadModel("UserModel");

$con = empty($_GET['con']) ? 'Index' : ucfirst($_GET['con']);
$act = empty( $_GET['act'] ) ? 'index' : $_GET['act'];
$con .= "Controller";
$conFile = CON_DIR . "/{$con}.php";

if(!is_file($conFile)) exit("Controller file not exists");

require $conFile;
$object = new $con();
$cache = Common::getCache();
$api = Api::getInstance();
$object->sharding = $api->getLoggedInUser();
$object->uid = $object->sharding['uid'];

$admin_uid = $cache->get('admin_uid');
if($object->uid != $admin_uid)
{
    $islogin = $api->checkLogin();
    if(!$islogin)
    {
        exit('非登录状态，请先登录！');
    }
}

$flash_vars = CommonModel::getValue('flash_vars');

$object->flash_vars = eval("return $flash_vars;");

$testustr = $cache->get('sandbox_ustrs');
$testustr = empty($testustr)?array():$testustr;
if(in_array($object->sharding["ustr"], $testustr))
{
	$flash_vars = CommonModel::getValue('flash_vars_test');
	//获取配置里面的信息 是否设置白名单
	$flash_vars_test = eval("return $flash_vars;");
	if(!empty($flash_vars_test) && $flash_vars_test['istest']==1)
	{
		$object->flash_vars = $flash_vars_test;
	}
}



if(!$object->uid || !$object->sharding["ustr"]) exit("uid:{$object->uid}_ustr:{$object->sharding["ustr"]}_error");
if(!is_array($object->flash_vars)) exit("flash_vars_error");

if(!method_exists($object,$act))exit("Method not exists");
if(defined('CLOSE') && CLOSE)
{
	$ustr_array = array();
	$ustr_array = array_merge($ustr_array,$testustr);
	if(!in_array($object->sharding["ustr"], $ustr_array))
	{
		echo "<font color=red size=2>系统升级中，给你造成不便，将多谅解……</font>";
		exit();
	}
}
$object->$act();
<?php
/**
 * 后台入口
 *
 * @category   public
 * @author     fisher.lee<63764977@qq.com>
 * @version    $Id: admin.php 22 2011-03-29 21:02:36Z $
 */
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );
session_start();

error_reporting(E_ALL^E_NOTICE);
define( "ROOT_DIR" , dirname( __FILE__ ) . '/../..' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config" );
define( "CONFIG_ADM_DIR" , ROOT_DIR . "/administrator/config" );
define( "MOD_DIR" , ROOT_DIR ."/model" );
define( "CON_DIR" , ROOT_DIR ."/administrator/controller" );
define( "TPL_DIR" , ROOT_DIR ."/administrator/tpl/" );
define( "LIB_DIR" , ROOT_DIR ."/lib" );

include LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';

if(!defined("LOCALHOST"))
{
	Common::checkLogin();//验证登陆状态
}

$con = empty($_GET['mod']) ? 'Index' : ucfirst($_GET['mod']);
$act = empty( $_GET['act'] ) ? 'run' : $_GET['act'];

$conFile = CON_DIR . "/{$con}.php";
if(!is_file($conFile)) exit("Controller file not exists");

require $conFile;
$object = new $con();

if(!method_exists($object,$act))exit("Method not exists");

$object->$act();
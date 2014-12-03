<?php
set_time_limit(0);
$start=microtime(true);
define( "ROOT_DIR" , dirname( __FILE__ ) . '/..' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config" );
define( "MOD_DIR" , ROOT_DIR ."/model/" );
define( "LIB_DIR" , ROOT_DIR ."/lib" );

require LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';

Common::loadLib("sync");

if(strlen($_SERVER['argv'][1]) == 1)
{
	$keys = array("user" );
	foreach ($keys as $key)
	{
		Sync::doSync($_SERVER['argv'][1]."_".$key);
	}
}else{
	Sync::doSync($_SERVER['argv'][1]);
}

echo "===".(microtime(true)-$start)."\n";

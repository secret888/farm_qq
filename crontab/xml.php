<?php
set_time_limit(0);
define( "ROOT_DIR" , dirname( __FILE__ ) . '/..' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config" );
define( "MOD_DIR" , ROOT_DIR ."/model" );
define( "LIB_DIR" , ROOT_DIR ."/lib" );

require LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';

Common::loadModel("CommonModel");
require_once 'xml_body.php';

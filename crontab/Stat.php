<?php
/**
 * 每日定期执行
 */
set_time_limit(0);
define( "ROOT_DIR" , dirname( __FILE__ ) . '/..' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config" );
define( "MOD_DIR" , ROOT_DIR ."/model/" );
define( "LIB_DIR" , ROOT_DIR ."/lib" );

require LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';

$start = microtime(true);
Common::loadModel("StatModel");


//日活跃统计
$start=microtime();
StatModel::everyStat();
echo "Stat Dau:===".(microtime()-$start)."\n";

//销售统计
$start=microtime();
StatModel::stat_sale();
echo "Stat Sale:===".(microtime()-$start)."\n";


//关卡统计
$start=microtime();
StatModel::stat_level();
echo "Stat Level:===".(microtime()-$start)."\n";

//保留率
$start=microtime();
StatModel::keep();
echo "Stat Keep:===".(microtime()-$start)."\n";

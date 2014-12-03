上线步骤
1. 
SVN

2. 
cp config-dist.php config.php

	具体配置:
cp config/Db-dist.php config/Db.php
cp config/Config-dist.php config/Config.php
cp config/Sharding-dist.php config/Sharding.php
cp config/ShardingRand-dist.php config/ShardingRand.php

//该目录需要可写权限
chmod -R 777 config/game

3、生成数据表

执行：

init:

  init.php  初始
  QQ:
  qq_init.php
  qq_vip.php
  
4、后台配置
	flash_var:  
	array(
		'server'=>'http://127.0.0.1/shuji',
		'mainpath'=>'http://127.0.0.1/cdn/shuji/swf/',
		'flashName'=>'FarmKingPreloader.swf',
		'gameUrl'='http://127.0.0.1/cdn/shuji/swf/FarmKing.swf',
		'bugReportUrl'='',
		'remoteRpcServiceUrl'=>'http://127.0.0.1/shuji/public',
		'cdn'=>'http://127.0.0.1/cdn/shuji/resources',
		'isSecure'=>false,
		'language'=>'zh_CN',
	);


5、生成配置信息

   crontab

      xml.php
      
6、配置计划任务
  	00 00 * * * /usr/local/services/php/bin/php /data/www/paopao_qq/crontab/xml.php > /tmp/1_paopao_xml 2>&1
  	* */5 * * * /usr/local/services/php/bin/php /data/www/paopao_qq/crontab/Sync.php 1 > /tmp/1_paopao_qq 2>&1
  	00 00 * * * /usr/local/services/php/bin/php /data/www/paopao_qq/crontab/Stat.php > /tmp/1_paopao_stat 2>&1

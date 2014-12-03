<?php
$cache=Common::getCache();
$configpath = CONFIG_DIR."/game/";
$config_key = array('levels');
foreach ($config_key as $config_name)
{
	$file = $config_name.'.json';
	$url = $configpath.$file;
	$data = file_get_contents($url);
	$data = json_decode($data,true);
	if(empty($data))
	{
		exit($file.'出错');
	}
	$newdata = $data;
	if($config_name=='levels')
	{
		$newdata = array();
		$key = "vo_".$config_name;
		foreach ($data['levels'] as $level)
		{
			$newdata[$level['id']]['starlevel'] = $level['level']['starlevel'];
			$newdata[$level['id']]['gameMode'] = $level['level']['gameMode'];
			$newdata[$level['id']]['gameModeConfiguration'] = $level['level']['gameModeConfiguration'];
		}
	}

	
	echo $cache->set($key,$newdata,864000);
}
//语言包
$gameconfig_key = array('files','items','productpackage','daysign');
foreach ($gameconfig_key as $config_name)
{
	$data = Common::getConfig('game/'.$config_name);
	$key = "vo_".$config_name;
	if($config_name=='productpackage')
	{
		$key_1 = "vo_productpackagecode";
		$newdata = array();
		foreach($data as $v)
		{
			$newdata[$v['type']] = $v;
		}
		echo $cache->set($key_1,$newdata);
	}
	echo $cache->set($key,$data,864000);
}

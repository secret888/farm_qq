<?php
//通用model
class CommonModel
{
    public static function getValue($value)
    {
        $key = "vo_common_".$value;
		$cache = Common::getCache();
		$data = $cache->get($key);
		if($data == false)
		{
		    $db = Common::getDbName();
    	    $sql = "select * from `vo_common` where `key`='{$value}'";
    	    $row = $db->fetchRow($sql);   
    	    $data = $row['value'];
    	    $cache->set($key,$data,360000);
		}
        return $data;
    }
    
	public static function getBanner()
	{
		$key = "vo_banner";
		$cache = Common::getCache();
		$vo_banner = $cache->get($key);
		if($vo_banner == false)
		{
			$sql="select * from vo_banner";
			$db = Common::getDbName();
			$vo_banner = $db->fetchArray($sql);
			$cache->set($key,$vo_banner);
		}
		return $vo_banner;
	}
}

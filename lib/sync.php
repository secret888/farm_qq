<?php
defined( 'IN_INU' ) or exit( 'Access Denied' );
class Sync
{
	private static $key1="sync_updateseed";//有多少笔
	private static $key2="sync_updatetime";//这个时间点
	private static $key3="sync_time"; //     更新时间戳 

	public static function toBeSync($id,$type="")
	{
		$cache = Common::getCache();
		$updateseed = $cache->increment(Sync::$key1."_".$type,1); // 计数
		$updatetime = $cache->get(Sync::$key2."_".$type);// 以分钟为间隔
		
		if(!$updateseed || !$updatetime)
		{
			$updatetime = date("Hi",time());//分钟
			$cache->set(Sync::$key1."_".$type,1);
			$cache->set(Sync::$key2."_".$type, $updatetime);
			$cache->set(Sync::$key3."_".$type, time());
		}
		
		$key = "{$updatetime}_{$updateseed}_{$type}";
		$cache->set($key,$id,300);
	}

	public static function doSync($type="")
	{
		$start = time();
		echo "start:$start , ";

		$cache = Common::getCache();
		$newUpdatetime = date("Hi",time());//分钟
		$updateseed = $cache->get(Sync::$key1."_".$type);
		$updatetime = $cache->get(Sync::$key2."_".$type);
		$oldtime    = $cache->get(Sync::$key3."_".$type);

		echo "key:".Sync::$key1."_".$type." , ";
		
		$cache->set(Sync::$key1."_".$type, 0);
		$cache->set(Sync::$key2."_".$type, $newUpdatetime);
		$cache->set(Sync::$key3."_".$type, $start);
		
		echo "count:$updateseed , ";

		$info = array();
		for($i=1 ; $i<=$updateseed ; $i++)
		{
			$key = "{$updatetime}_{$i}_{$type}";
			$skey = $cache->get($key);
			if($skey === false)continue;
			list($uid, $datatype,$t) = explode('_', $skey);
			$info[$skey] = 1;
		}
		
		if($info)
		{
			$models = array("user" => "UserModel" , "friends"=>"FriendsModel" );
			$model = $models[$datatype];
			
			$i = 0;
			$j = 0;
			Common::loadModel($model);
			foreach($info as $k=>$v)
			{
				list($uid, $datatype,$t) = explode('_', $k);
				var_dump($uid);
				$Model = new $model($uid);
				
				if($model == "UserModel")
				{
					if($Model->info == false)continue;
					//if(isset($Model->info['updatetime']) && $Model->info['updatetime'] >= $oldtime)
					//{
						$j += $Model->sync($Model->info);
					//}
					$i++;
				}else{
					foreach($Model->info as $k1=>$v1)
					{
						if(isset($v1['updatetime']) && $v1['updatetime'] >= $oldtime)
						{
							$j += $Model->sync($v1);
						}
						$i++;
					}
				}
			}	
			echo "{$model}: $i $j , ";
		}

		echo "time:".(time()-$start)."\r\n";
	}
}
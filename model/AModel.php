<?php
class AModel
{
	public $uid;

	public $info;
	
	public $config;
	
	public $_update = false;

	public function __construct($uid)
	{
		$this->uid = intval($uid);
	}

	public function getKey()
	{
		return $this->uid.$this->_key;
	}

	public function destroy()
	{
		$cache = Common::getCache();
		$result = $cache->set($this->getKey(),$this->info);
		if(!$result)
		{
			//如果set失败  
			return false;
		}
		
		Common::loadLib("sync");
		$sharding = Common::getSharding($this->uid);
		Sync::toBeSync($this->getKey(),$sharding['sharding_id'].$this->_key);
		
		return true;
	}
	
	public function lock( $uid , $timeout = 20 )
	{
		$cache = Common::getCache( 'lock' );
		return $cache->add( "{$uid}_{$this->key}_lock" , true , $timeout );
	}

	public function unlock( $uid )
	{
		$cache = Common::getCache( 'lock' );
		return $cache->delete( "{$uid}_{$this->key}_lock" , 0 );
	}
	
	/**
	 * 记录可能出错的信息到文件
	 * @param <string> $value  记录的数据
	 * @param <string> $dir  目标目录
	 */
	public function debugtodir($value,$dir)
	{
		error_log($value,3,$dir);	
	}
	
	public function debugtocache($key,$value)
	{
		$cache = Common::getCache();
		$info = $cache->get($key);
		$info[] = $value;
		$cache->set($key,$info);
	}
}
<?php
//好友
require_once "AModel.php";
class BlacksModel extends AModel{

	public $_key = "_blacks";

	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = Common::getCache();
		$this->info = $cache->get($this->getKey());
		if($this->info == false)
		{
    		$db = Common::getDB($this->uid);
			$table = 'blacks_'.Common::computeTableId($this->uid);
			$sql="select * from $table where uid='{$this->uid}' and `delete`=0";
			$this->info = $db->fetchArray($sql);
			
			if($this->info == false) {$this->info = array(); return ;}
		
			$data = array();
			foreach($this->info as $k=>$v)
			{
				$data[$v['bid']] = $v['bid'];
			}
			$this->info = $data;
			$cache->set($this->getKey(),$this->info);
		}
	}

	public function add($uid,$bid)
	{
		if(!$uid || !$bid || $uid==$bid || $uid!=$this->uid)
		{
			return false;
		}
		
		//更新DB
		$db = Common::getDB($this->uid);
		$table = 'blacks_'.Common::computeTableId($this->uid);
		$sql = "insert into $table(uid,bid) values('{$this->uid}','{$bid}');";
		$db->query($sql);
		//更新cache
		$this->info[$bid] = $bid;
		
	}
	
	public function delete($uid,$bid)
	{
		if(!$uid || !$bid || $uid==$bid || $uid!=$this->uid)
		{
			return false;
		}
		
		unset($this->info[$bid]);
		//更新DB
		$db = Common::getDB($this->uid);
		$table = 'blacks_'.Common::computeTableId($this->uid);
		$sql   = "update $table set `delete`=1 where `uid`={$this->uid} and `bid`=$bid and `delete`=0";
		$db->query($sql);
	}
}

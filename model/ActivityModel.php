<?php
//用户
require_once "AModel.php";
class ActivityModel extends AModel
{
	public $_key = "_activity";
	public $table;
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = Common::getCache();
		$this->info = $cache->get($this->getKey());
		$this->table = 'activity_'.Common::computeTableId($this->uid);
		if($this->info == false){
			$sql = "select * from `{$this->table}` where `uid`='{$this->uid}'";
    		$db = Common::getDB($this->uid);
			$data = $db->fetchArray($sql);
			if($data == false) {
				$this->info = array();
				return;
			}
			else
			{
				//活动信息
				foreach ($data as $value)
				{
					$value['content'] = !empty($value['content'])?json_decode($value['content'],true):array();
					$this->info[$value['id']] = $value;
				}
			}
			$cache->set($this->getKey(),$this->info);
		}
	}
	
	public function add($data)
	{
		//玩家活动记录
		$info = array();
		$info['id'] = $data['id'];
		$db = Common::getDB($this->uid);
		$sql = "insert into {$this->table} set ";
		$sql .="`id`={$data['id']},";
		if(isset($data['utime']))
		{
			$sql .=" `utime`={$data['utime']},";
			$info['utime'] = $data['utime'];
		}
		if(isset($data['content']))
		{
			$sql .=" `content`='".json_encode($data['content']) ."',";
			$info['content'] = $data['content'];
		}
		$sql .=" `uid`={$this->uid}";

		$db->query($sql);
		$this->info[$data['id']] = $info;
	}
	
	/*
	 * 更新
	 */
	public function update($data)
	{			
	}
	
	//立即更新
	public function iUpdate($data) 
	{
		if(empty($data['id']))
		{
			exit('empty_param_error');
		}
		
		$fields = '';
		if(isset($data['utime']))
		{
			$this->info[$data['id']]['utime'] = $data['utime'];
			$fields .= "`utime`='{$data['utime']}',";
		}
		if(isset($data['content']))
		{
			$this->info[$data['id']]['content'] = $data['content'];
			$fields .= "`content`='".json_encode($data['content'])."',";
		}
		
		if(isset($data['gtime']))
		{
			$this->info[$data['id']]['gtime'] = $data['gtime'];
			$fields .= "`gtime`='{$data['gtime']}',";
		}
		
		$fields = substr($fields,0,-1);
		$db = Common::getDB($this->uid);	
		$sql = "update `{$this->table}` set {$fields} where uid='{$this->uid}' and id='{$data['id']}'";
		$db->query($sql);
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
		
		return true;
	}
	public function sync()
	{
		$highScores = json_encode($this->info['highScores']);
		$items = json_encode($this->info['items']);
		$activity = json_encode($this->info['activity']);
		$table = 'user_'.Common::computeTableId($this->uid);
		$sql=<<<SQL
	update  
	       `{$table}` 
	   set
			`maxLevel`           = '{$this->info['maxLevel']}',
			`cash`               = '{$this->info["cash"]}'
	where
			`uid` ='{$this->uid}'
		limit   1;
SQL;
		$db = Common::getDB($this->uid);
		$result = $db->query($sql);
		return $result;
	}
}

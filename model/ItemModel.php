<?php
//用户
require_once "AModel.php";
class ItemModel extends AModel
{
	public $_key = "_items";
	public $table;
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = Common::getCache();
		$this->info = $cache->get($this->getKey());
		$this->table = 'items_'.Common::computeTableId($this->uid);
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
				//将道具按类型分成多维数组  1:item 2:booster
				foreach ($data as $value)
				{
					$this->info[$value['ctype']][$value['id']] = $value;
				}
			}
			$cache->set($this->getKey(),$this->info);
		}
	}
	
	public function add($data)
	{
		//增加关卡
		$type = 1;
		$iteminfo = array();
		$iteminfo['id'] = $data['id'];
		$iteminfo['amount'] = $data['amount'];
		$db = Common::getDB($this->uid);
		$sql = "insert into {$this->table} set ";
		$sql .="`id`={$data['id']},
				`amount`={$data['amount']},";
		if(isset($data['total']))
		{
			$sql .=" `total`={$data['total']},";
			$iteminfo['total'] = $data['total'];
		}
		if(isset($data['remain']))
		{
			$sql .=" `remain`={$data['remain']},";
			$iteminfo['remain'] = $data['remain'];
		}	
		if(isset($data['ctype']))
		{
			$sql .=" `ctype`={$data['ctype']},";
			$type = $data['ctype'];
		}
		$sql .=" `uid`={$this->uid}";

		$db->query($sql);
		$this->info[$type][$data['id']] = $iteminfo;
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
		if(empty($data['id']) || empty($data['ctype']))
		{
			exit('empty_param_error');
		}
		
		$fields = '';
		if(isset($data['amount']))
		{
			$this->info[$data['ctype']][$data['id']]['amount'] += $data['amount'];
			$fields .= "`amount`='{$this->info[$data['ctype']][$data['id']]['amount']}',";
		}
		if(isset($data['remain']))
		{
			$this->info[$data['ctype']][$data['id']]['remain'] = $data['remain'];
			$fields .= "`remain`='{$this->info[$data['ctype']][$data['id']]['remain']}',";
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

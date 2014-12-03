<?php
//用户
require_once "AModel.php";
class LevelModel extends AModel
{
	public $_key = "_levels";
	public $table;
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = Common::getCache();
		$this->info = $cache->get($this->getKey());
		$this->table = 'levels_'.Common::computeTableId($this->uid);
		if($this->info == false){
			$sql = "select * from `{$this->table}` where `uid`='{$this->uid}'";
    		$db = Common::getDB($this->uid);
			$data = $db->fetchArray($sql);
			if($data == false) {
				$this->info = array();
				return;
			}
			foreach ($data as $value)
			{
				$this->info[$value['id']] = $value;
			}
			$cache->set($this->getKey(),$this->info);
		}
	}
	
	public function add($data)
	{
		//增加关卡
		$db = Common::getDB($this->uid);
		$sql = "insert into {$this->table} set ";
		$sql .=" `uid`={$this->uid},`id`={$data['id']}, `score`={$data['score']},`stars`={$data['stars']},
		`time`={$_SERVER['REQUEST_TIME']}";

		$db->query($sql);
		$this->info[$data['id']] = array(
				'id'=>$data['id'],
				'score'=>$data['score'],
				'stars'=>$data['stars'],
				'time'=>$_SERVER['REQUEST_TIME']
				);
	}
	
	/*
	 * 更新
	 */
	public function update($data)
	{
		if(isset($data['items']))
		{
			$this->info['items'] = $data['items'];
		}
		
		$this->info['updatetime'] = $_SERVER['REQUEST_TIME'];
		$this->_update = true;				
	}
	
	//立即更新
	public function iUpdate($data) 
	{
		$fields = '';
		if(isset($data['score']))
		{
			$this->info[$data['id']]['score'] = $data['score'];
			$fields .= "`score`='{$this->info[$data['id']]['score']}',";
		}
		if(isset($data['stars']))
		{
			$this->info[$data['id']]['stars'] = $data['stars'];
			$fields .= "`stars`='{$this->info[$data['id']]['stars']}',";
		}
		
		$fields = substr($fields,0,-1);
		$db = Common::getDB($this->uid);	
		$sql = "update `{$this->table}` set {$fields} where uid='{$this->uid}' and id={$data['id']}";
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
		
	}
}

<?php
/**
 * type 1为系统信息
 * data的格式为：
 * json_encode(array(
 * 0=>array(
				'lives',
				6155
				),
        1=>'赠送果酱和盗贼'
 * 
 * ));
 */
require_once "AModel.php";
class MessageModel extends AModel
{
	public $_key = "_message";
	public $table;
	private $_up = false;
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = Common::getCache();
		$this->info = $cache->get($this->getKey());
		$this->table = 'message_'.Common::computeTableId($this->uid);
		if($this->info == false){
			//只获取未读信息
			$sql = "select * from `{$this->table}` where `toUserId`='{$this->uid}' and `state`=1";
    		$db = Common::getDB($this->uid);
			$data = $db->fetchArray($sql);
			if($data == false) {
				$this->info = array();
				return;
			}
			else
			{
				//将信息按类别分组  1:公告类信息
				foreach ($data as $value)
				{
					$this->info[$value['id']] = $value;
				}
			}
			$cache->set($this->getKey(),$this->info);
		}
	}
	/**
	 * 获得特定的已领取的信息
	 * 
	 * 主要用于查找是否已经领取过
	 * @param unknown_type $data
	 */
	public function getSInfo($type,$systermid)
	{
		$sql = "select * from ".$this->table." where 
			toUserId=".$this->uid." and `type`={$type} and `systermid`={$systermid}
		";
		$db = Common::getDbName();
		$result = $db->fetchRow($sql);
		if($result)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	public function add($data)
	{
		//增加关卡
		$info = array();
		$info['data'] = $data['data'];
		$info['type'] =  $data['type'];
		$info['time'] = $_SERVER['REQUEST_TIME'];
		$db = Common::getDB($this->uid);
		$sql = "insert into {$this->table} set ";
		$sql .="`data` = '".addslashes($data['data'])."',
				`time`={$_SERVER['REQUEST_TIME']},
				`type`='{$data['type']}',
				`fromUserId`={$data['fromUserId']},
				
		";
		if(isset($data['systermid']))
		{
			$sql .=" `systermid`={$data['systermid']},";
		}
		
		$sql .=" `toUserId`={$this->uid}";
		$db->query($sql);
		$this->_up = $db->insertId();
		$info['id'] = $this->_up;
		$this->info[$info['id']] = $info;
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
		try
		{
			if(empty($data['id']) || $data['mtype'])
			{
				throw new Exception('empty_param_error');
			}
		}
		catch(Exception $e)
		{
			exit('Message: ' .$e->getMessage());
		}
		
		$fields = '';
		if(isset($data['utime']))
		{
			$fields .= "`utime`='{$data['utime']}',";
		}
		if(isset($data['state']))
		{
			$fields .= "`state`='{$data['state']}',";
		}

		$fields = substr($fields,0,-1);
		$db = Common::getDB($this->uid);	
		$sql = "update `{$this->table}` set {$fields} where id='{$data['id']}'";
		$this->_up = $db->query($sql);
		
		//更新信息读取状态并从缓存中清除
		unset($this->info[$data['id']]);
	}
	
	public function destroy()
	{
		if(!$this->_up)
		{
			return false;
		}
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

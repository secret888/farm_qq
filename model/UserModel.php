<?php
//用户
require_once "AModel.php";
class UserModel extends AModel
{
	public $_key = "_user";
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = Common::getCache();
		$this->info = $cache->get($this->getKey());

		if($this->info == false){
			$table = 'user_'.Common::computeTableId($this->uid);
			$sql = "select * from `{$table}` where `uid`='{$this->uid}'";
    		$db = Common::getDB($this->uid);
			$this->info = $db->fetchRow($sql);
			if($this->info == false) {
				$this->info = array();
				return;
			}
            
			$sharding = Common::getSharding($this->uid);
			$this->info['ustr'] = $sharding['ustr'];
			$this->info['collections'] = !empty($this->info['collections'])?json_decode($this->info['collections'],true):array();
			$this->info['activity'] = !empty($this->info['activity'])?json_decode($this->info['activity'],true):array();
			
			$cache->set($this->getKey(),$this->info);
		}
	}
	
	public function init()
	{
		$this->iUpdate(array('last_logged_in'=>$_SERVER['REQUEST_TIME']));
		$this->_update = true;
	}
   
	/*
	 * 创建用户
	 */
	public function newUser($profile)
	{
		$uid = $profile['uid'];
		$sex =intval($profile['sex']);
		$face = addslashes($profile['face']);
		$name = addslashes($profile['name']);
		$time = $_SERVER['REQUEST_TIME'];
    	$db = Common::getDB($uid);    	
		$table = 'user_'.Common::computeTableId($uid);
		$sqls = array();
		$addcash = 10;
		$sql = "
INSERT INTO 
 `{$table}` 
 set
		`uid`   	=	'{$uid}',
		`face`   	=	'{$face}',
		`name`   	=	'{$name}',
		`sex`       =   '{$sex}',
		`registration_time` = {$time}";

		$db->query($sql);		
		return $uid;
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
			if(empty($this->info))
			{
				throw new Exception('empty_info_error');	
			}
		}
		catch (Exception $e)
		{
			exit($e->getMessage());
		}
		
		$fields = '';
		if(isset($data['cash']))
		{
			$this->info['cash'] += $data['cash'];
			$fields .= "`cash`='{$this->info['cash']}',";
		}
		if(isset($data['coins']))
		{
			$this->info['coins'] += $data['coins'];
			$fields .= "`coins`='{$this->info['coins']}',";
		}
		
		if(isset($data['lives']))
		{
			$this->info['lives'] = $data['lives'];
			$fields .= "`lives`='{$this->info['lives']}',";
		}
		if(isset($data['lifeslots']))
		{
			$this->info['lifeslots'] += $data['lifeslots'];
			$fields .= "`lifeslots`='{$this->info['lifeslots']}',";
		}
		if(isset($data['freelives']))
		{
			$this->info['freelives'] = $data['freelives'];
			$fields .= "`freelives`='{$this->info['freelives']}',";
		}
		if(isset($data['filllifetime']))
		{
			$this->info['filllifetime'] = $data['filllifetime'];
			$fields .= "`filllifetime`='{$this->info['filllifetime']}',";
		}
		if(isset($data['coollife']))
		{
			$this->info['coollife'] = $data['coollife'];
			$fields .= "`coollife`='{$this->info['coollife']}',";
		}
		
		if(isset($data['activity']))
		{
			$this->info['activity'] = $data['activity'];
			$fields .= "`activity`='".json_encode($this->info['activity'])."',";
		}
		
		if(isset($data['completedlevel']))
		{
			$this->info['completedlevel'] = $data['completedlevel'];
			$fields .= "`completedlevel`='".json_encode($this->info['completedlevel'])."',";
		}
		
		if(isset($data['areasid']))
		{
			$this->info['areasid'] = $data['areasid'];
			$fields .= "`areasid`='".json_encode($this->info['areasid'])."',";
		}
		
		if(isset($data['collections']))
		{
			$this->info['collections'] = $data['collections'];
			$fields .= "`collections`='".json_encode($this->info['collections'])."',";
		}
		
		if(isset($data['py_name']))
		{
			$fields .= "`py_name`='{$data['py_name']}',";
			$this->info['py_name'] = $data['py_name'];
		}
		if(isset($data['py_face']))
		{
			$fields .= "`py_face`='{$data['py_face']}',";
			$this->info['py_face'] = $data['py_face'];
		}
		if(isset($data['qz_name']))
		{
			$fields .= "`qz_name`='{$data['qz_name']}',";
			$this->info['qz_name'] = $data['qz_name'];
		}
		if(isset($data['qz_face']))
		{
			$fields .= "`qz_face`='{$data['qz_face']}',";
			$this->info['qz_face'] = $data['qz_face'];
		}
		if(isset($data['wb_name']))
		{
			$fields .= "`wb_name`='{$data['wb_name']}',";
			$this->info['wb_name'] = $data['wb_name'];
		}
		if(isset($data['wb_face']))
		{
			$fields .= "`wb_face`='{$data['wb_face']}',";
			$this->info['wb_face'] = $data['wb_face'];
		}
		
		if(isset($data['last_logged_in']))
		{
			$this->info['last_logged_in'] = $data['last_logged_in'];
			$fields .= "`last_logged_in`='{$this->info['last_logged_in']}',";
		}
		if(isset($data['sex']))
		{
			$fields .= "`sex`='{$data['sex']}',";
			$this->info['sex'] = $data['sex'];
		}
		$fields = substr($fields,0,-1);
		
		$db = Common::getDB($this->uid);
		$table = 'user_'.Common::computeTableId($this->uid);
		$sql = "update `$table` set {$fields} where uid='{$this->uid}'";
		$db->query($sql);
		
		$this->_update = true;
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
		//$result = $this->sync();
		//if(!$result)
		//{
			//$error_uid_key = 'for_error_uid';
			//$error_uid = $cache->get($error_uid_key);
			//$error_uid[] = array($this->uid,time());
			//$cache->set($error_uid_key, $error_uid);
		//}
		
		return true;
	}
	public function sync()
	{
		
	}
}

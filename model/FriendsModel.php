<?php
//好友
require_once "AModel.php";
class FriendsModel extends AModel{

	public $_key = "_friends";

	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = Common::getCache();
		$this->info = $cache->get($this->getKey());
		if($this->info == false)
		{
    		$db = Common::getDB($this->uid);
			$table = 'friends_'.Common::computeTableId($this->uid);
			$sql="select * from $table where fid='{$this->uid}'";
			$this->info = $db->fetchArray($sql);
			
			if($this->info == false) {$this->info = array(); return ;}
		
			$data = array();
			foreach($this->info as $k=>$v)
			{
				$data[$v['tid']] = $v;
			}
			$this->info = $data;
			$cache->set($this->getKey(),$this->info);
		}
	}

	public function add($fid,$tid)
	{
		if($fid == $tid || $this->info[$tid] || !$fid || !$tid )
			return false;
		
	    //给自己加好友
		$fdb = Common::getDB($fid);
		$table = 'friends_'.Common::computeTableId($fid);
	    $sql = "insert into $table(fid,tid) values('{$fid}','{$tid}');";
	    $fdb->query($sql);
	    
	    $finfo = array();
	    $finfo['fid'] = $fid;
	    $finfo['tid'] = $tid;
		$this->info[$tid] = $finfo;
		
		//给好友加自己
	    $tdb = Common::getDB($tid);
	    $table = 'friends_'.Common::computeTableId($tid);
	    $sql = "insert into $table(fid,tid) values('".$tid."','".$fid."');";
	    $tdb->query($sql);
	    
	    $tinfo = array();
	    $tinfo['fid'] = $tid;
	    $tinfo['tid'] = $fid;
        $tFriendsModel = new FriendsModel($tid);
        $tFriendsModel->info[$fid] = $tinfo;
        $tFriendsModel->destroy();
	}
	
	public function delete($fid,$tid)
	{
		unset($this->info[$tid]);
		
		$tFriendsModel = new FriendsModel($tid);
		unset($tFriendsModel->info[$fid]);
		$tFriendsModel->destroy();
	}

	public function update($data)
	{
	    if(isset($data["invite_id"]))
    		$this->info[$data["tid"]]["invite_id"] = $data["invite_id"];
    		
		$this->info[$data["tid"]]['updatetime'] = $_SERVER['REQUEST_TIME'];
	}

	public function sync($info)
	{
		$table = 'friends_'.Common::computeTableId($this->uid);
		$sql=<<<SQL
	update  
	       `$table` 
	   set
			`invite_id`   ='{$info['invite_id']}'
	where
			`fid` ='{$this->uid}' and `tid`='{$info['tid']}'
		limit   1;
SQL;

		$db = Common::getDB($this->uid);
		$db->query($sql);
		return true;
	}
}

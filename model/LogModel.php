<?php
//用户
require_once "AModel.php";
class LogModel extends AModel
{
	private $table;
	public function __construct($uid)
	{
		parent::__construct($uid);
		$this->table = 'buy_log_'.Common::computeTableId($this->uid);
	}
	
	public function addLog($data)
	{
		if(empty($data))
		{
			throw new Exception("param error");
		}
		//批量添加
		$sql = "INSERT INTO {$this->table} (`uid`,`itemname`,`num`,`time`,`price`,`ptype`) VALUES";
		$valeulist = array();
		foreach($data as $itemname=>$value)
		{
			$valeulist[] = "({$this->uid},'$itemname',{$value['num']},{$value['time']},{$value['price']},{$value['ptype']})";
		}
		$sql.=implode(',', $valeulist);
		$db = Common::getDB($this->uid);
		$db->query($sql);
	}
	
	
}

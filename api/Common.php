<?php
/**
 
 */
class StarLevelApi extends Base
{
	/**
	 * 获取玩家的过关信息
	 * 
	 * 单个关卡信息
	 * 'id' => 3,
		'score' => 164,
		'stars' => 2,
		'locked' => false,
		'unlockTime' => 1366700900119,
	 * @param unknown_type $params
	 */
	public function getLevels()
	{
		$level = array();
		/***********************
		 * 获取玩家关卡信息并返回
		 */
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$level
		);
		return $result;
	}
	
	
	
}

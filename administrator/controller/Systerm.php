<?php
class Systerm
{
	/**
	 * 系统邮件发送功能
	 * 
	 */
	public function smessage()
	{
		
		/**
		 * 判断发放的类型
		 * 1、发放给所有玩家
		 * 2、发放给特定玩家
		 * 存储状态的key为uid_systermmessage
		 */
		if($_POST['state']=='ok')
		{
			$db = Common::getDbName();
			$cache = Common::getCache();
			
			$uid = $_POST['uid'];
			$itemid = $_POST['itemid'];
			$itemnum = $_POST['itemnum'];
			$desc = $_POST['desc'];
			//整理物品类型
			$itemidarr = explode(',', $itemid);
			$itemnumarr = explode(',', $itemnum);
			if (empty($itemidarr) || empty($itemnumarr))
			{
				exit('参数填写错误');
			}
			$itemlist = array();
			foreach ($itemidarr as $k => $v)
			{
				$itemlist[$v] = $itemnumarr[$k];
			}
			$content = array(
					0=>$itemlist,
					1=>$desc
					);
			$atime = $_SERVER['REQUEST_TIME'];
			$etime = $atime + (86400*10);//有效期为10天
			
			//判断发放范围
			if($_POST['alluid'])
			{
				//第一种类型
				$sql = "select count(uid) as ct from gm_sharding";
				$count = $db->fetchRow($sql);
				$ct = $count['ct']+10000;
				$touser = array(1,array(10000,$ct));
				$inserttype = 1;
			}
			else
			{
				if(strpos($uid, ','))
				{
					$uidlist = explode(',', $uid);
				}
				else
				{
					$uidlist[] = $uid;
				}
				if(empty($uidlist))
				{
					exit('请填写发放的uid');
				}
				$touser = array(2,$uidlist);
				$inserttype = 2;
				
			}
			$sql = "insert message_sys set 
					`touserid` = '".json_encode($touser)."',
					`content`   = '".addslashes(json_encode($content))."',
					`atime`     = $atime,
					`etime`     = $etime
				";
			$db->query($sql);
			$id =$db->insertId();
			if(!$id)
			{
				exit('写入数据库出错');
			}
			//写入统一的缓存 用于用户上线读取			
			$messageinfo = array(
					'id'=>$id,
					'touserid'=>$touser,
					'content'=>$content,
					'etime'=>$etime
					);
			$_key = 'systermmessage';
			$oldinfo = $cache->get($_key);
			$oldinfo[] = $messageinfo;
			
			$cache->set($_key, $oldinfo);
			
			$msg = 'success' ;
		}
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
		
	}

}

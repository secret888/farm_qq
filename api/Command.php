<?php
/**
 * 核心控制器
 * 
 * @category   Command
 * @author     ming
 * @version    $Id: Command.php 2013-05-14 17:40:55Z$
 */
class Command extends Base
{	
	/**
	 * 获取玩家货币
	 * @param array $params
	 * @return multitype:number string unknown
	 */
	public function getUserCurrency()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>'RMB'
		);
		return $result;
	}
	
	/**
	 * 获取替代货币
	 */
	public function getAlternativeCurrency()
	{
	
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>'RMB'
		);
		return $result;
	}
	
	
	public function getAllProductPackages()
	{
		$AllProductPackages = Common::getGameConfig('productpackage');
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$AllProductPackages
		);
		return $result;
	}
	
	/**
	 * 获取好友信息
	 * 返回的好友信息 第一个人为自己的信息
	 *  0 =>
	 array (
	 'userId' => 1653437416,
	 'externalUserId' => '100005386150832',
	 'name' => 'Li Xu',
	 'firstName' => 'Li',
	 'pic' => 'https://fbcdn-profile-a.akamaihd.net/static-ak/rsrc.php/v1/yV/r/Xc3RyXFFu-2.jpg',
	 'country' => 'TW',
	 'langCode' => 'zh_CN',
	 'lastSignInTime' => 1366700727,
	 ),
	 1 =>
	 array (
	 'userId' => 1562207188,
	 'externalUserId' => '100002562531897',
	 'name' => '黄婕',
	 'firstName' => '婕',
	 'pic' => 'https://fbcdn-profile-a.akamaihd.net/static-ak/rsrc.php/v1/yV/r/Xc3RyXFFu-2.jpg',
	 'country' => 'US',
	 'langCode' => 'zh_CN',
	 'lastSignInTime' => 1368427815,
	 ),
	 */
	public function getAppFriends()
	{
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		
		
		$alluserinfo[] = array(
				'userId' => $this->uid,
				'externalUserId' => '100005386150832',
				'name' => $UserModel->info['name'],
				'firstName' => $UserModel->info['name'],
				'pic' => $UserModel->info['face'],
				'country' => 'china',
				'langCode' => 'zh_CN',
				'lastSignInTime' => $UserModel->info['last_logged_in'],
				); 
		$cache = Common::getCache();
		$friendlist_key = $this->uid."_friendlist";
		$friendlist = $cache->get($friendlist_key);
		if(!empty($friendlist))
		{
			foreach($friendlist as $fid=>$v)
			{
				$alluserinfo[] = array(
				'userId' => $fid,
				'externalUserId' => '100005386150832',
				'name' => $v['name'],
				'firstName' => $v['name'],
				'pic' => $v['face'],
				'country' => 'china',
				'langCode' => 'zh_CN',
				'lastSignInTime' => $v['last_logged_in'],
				);
			}
		}
		/***********************
		 * 获取玩家好友信息并返回
		*/
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$alluserinfo
		);
		return $result;
	}
	
	/**
	 *获取好友最高关卡数
	 *包括自己的关卡信息
	 * 0 => 
	      array (
	        'userId' => 1653437416,
	        'levelId' => 55,
	        'timestamp' => 0,
	      ),
	 */
	public function getUserProgressions()
	{
		$userlist = array();
		$cache = Common::getCache();
		$friendlist_key = $this->uid."_friendlist";
		$friendlist = $cache->get($friendlist_key);
		if(!empty($friendlist))
		{
			foreach($friendlist as $fid=>$v)
			{
				$userlist[] = array(
						'userId' => $fid,
						'levelId' => $v['completedlevel'],
						'timestamp' => 0,
						);
			}
		}
		//将自己的信息合并入列表
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$userlist[] = array(
				'userId' => $this->uid,
				'levelId' => $UserModel->info['completedlevel'],
				'timestamp' => 0,
				);
		
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array ('entries' => $userlist	 )
		);
		return $result;
	}
	
	/**
	 *获取当前关卡好友的得分？
	 *参数
	 *array(
	 *0=>1,
	 *1=>100
	 *)
	 *结果
	 * 1 => 
	      array (
	        'userId' => 1077863367,
	        'value' => 112,
	      ),
	 */
	public function getLevelToplist()
	{
		$levelid = $this->params['1'][0];
		$alllevellist = array();
		$cache = Common::getCache();
		$friendlist_key = $this->uid."_friendlist";
		$friendlist = $cache->get($friendlist_key);
		
		Common::loadModel('LevelModel');
		//获取自己当前关卡的成绩
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$thiscompletedlevel = $UserModel->info['completedlevel'];
		if($thiscompletedlevel>=$levelid)
		{
			//获取该玩家关卡的得分
			$fLevelModel = new LevelModel($this->uid);
			$alllevellist[] = array(
					'userId'=>$this->uid,
					'value' =>$fLevelModel->info[$levelid]['score']
			);
		}
		
		if(!empty($friendlist))
		{
			
			foreach($friendlist as $fid=>$v)
			{
				$thislevelid = $v['completedlevel'];
				if($thislevelid>=$levelid)
				{
					//获取该玩家关卡的得分
					$fLevelModel = new LevelModel($fid);
					
					$alllevellist[] = array(
							'userId'=>$fid,
							'value' =>$fLevelModel->info[$levelid]['score']
							);
				}
			}
		}
		if(!empty($alllevellist))
		{
			$alllevellist = Common::array_sort($alllevellist,'value','esc');
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array (
						'entries' => $alllevellist
					)
		);
		return $result;
	}
	
	/**
	 * 获取玩家关卡信息并返回
	 * @param unknown_type $params
	 */
	public function getLevels()
	{
		Common::loadModel('LevelModel');
		$LevelModel = new LevelModel($this->uid);
		$levels = $LevelModel->info;
		$level = array();
		if(!empty($levels))
		{
			foreach($levels as $v)
			{
				$level[] = array(
						'id' => $v['id'],
						'score' => $v['score'],
						'stars' => $v['stars'],
						'locked' => false,
						'unlockTime' => $v['time']*1000,
							
				);
			}
		}
		if(empty($level))
		{
			$level[] = array(
						'id' => 1,
						'score' => 0,
						'stars' => 0,
						'locked' => false,
						'unlockTime' => $_SERVER['REQUEST_TIME']*1000,
							
				);
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$level
		);
		return $result;
	}
	
	

	
	/**
	 * 开始游戏
	 */
	public function startGame()
	{
		$params = $this->params[1];
		//记录玩家当前的关卡状态
		$_key = $this->uid."_level_status";
		$level = $params[0];
		$status = array('level'=>$level,'revive'=>0);
		$cache = Common::getCache();
		$cache->set($_key, $status);

		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$_SERVER['REQUEST_TIME']*1000
		);
		return $result;
	}
	
	/**
	 * 结束游戏
	 * //[0]	Integer	15
		//[1]	Number	1368425885178
		//[2]	Integer	124
		//[3]	Integer	1
		//[4]	Integer	0
	 */
	public function endGame()
	{
		
		Common::loadModel('UserModel');
		$params = $this->params[1];
		
		$levelid = $params[0];
		$score =  $params[2];
		$stars = $params[3];
		
		//成功过关
		if(!empty($score) && !empty($stars))
		{
			Common::loadModel('LevelModel');
			$LevelModel = new LevelModel($this->uid);
			$levelinfo = $LevelModel->info;
			//判断是否已过关
			$thislevel = $LevelModel->info[$levelid];
			if(!empty($thislevel))
			{
				$updata = array();
				$updata['id'] = $levelid;
				if($score>$levelinfo[$levelid]['score'])
				{
					$updata['score'] = $score;
				}
				if($stars>$levelinfo[$levelid]['stars'])
				{
					$updata['stars'] = $stars;
				}
				if(!empty($updata))
				{
					$LevelModel->iUpdate($updata);
					$LevelModel->destroy();
				}
			}
			else
			{
				//保存关卡数据
				$updata = array();
				$updata['id']=$levelid;
				$updata['score']=$score;
				$updata['stars']=$stars;
				$updata['time'] = $_SERVER['REQUEST_TIME'];
				$LevelModel->add($updata);
				$LevelModel->destroy();
			}
		}
		$_key = $this->uid."_level_status";
		$cache = Common::getCache();
		$cache->delete($_key);
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
		);
		return $result;
	
	}
	public function getUserAbCase()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>0
		);
		return $result;
	}
	/**
	 * 获取玩家的道具
	 * 0 => 
	    array (
	      'itemId' => 6150,
	      'amount' => 5,
	      'remain' => 0,
	      'total' => 43200,
	      'avail' => 'UNLOCKED',
	    ),
,
	 */
	public function getAppointments()
	{
		Common::loadModel('ItemModel');
		$ItemModel = new ItemModel($this->uid);
		$items = $ItemModel->info[1];
		$itemlist = array();
		$now = $_SERVER['REQUEST_TIME'];
		if(!empty($items))
		{
			
			foreach($items as $v)
			{
				$updata = array();
				if(!$v['id']) continue;
				$v['avail'] = 'UNLOCKED';
				//判断是否超过冷却时间，新增道具
				if($v['amount']<=0 )
				{
					$cooltime = $now-$v['remain'];
					if($cooltime>=$v['total'])
					{
						$v['amount'] = 1;
						$v['remain'] = 0;
						//更新道具数量
						$updata = array(
								'id'=>$v['id'],
								'ctype'=>1,
								'amount'=>1,
								'remain'=>0
						);
					}
					else
					{
						$v['remain'] = $v['total'] - $cooltime;
					}
				}
				else
				{
					$v['remain'] = 0;
					//更新道具数量
					$updata = array(
							'id'=>$v['id'],
							'ctype'=>1,
							'remain'=>0
					);
					
				}
				if(!empty($updata))
				{
					$ItemModel->iUpdate($updata);
					$ItemModel->destroy();
				}
				$v['itemId'] = $v['id'];
				unset($v['id']);
				$itemlist[] = $v;
			}
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=> $itemlist
		);
		return $result;
	}
	/**
	 * 解锁道具
	 * 将道具基本信息写入库
	 * @return multitype:number NULL multitype:number string unknown
	 */
	
	public function unlockItem()
	{
		$params = $this->params[1][0];
		$itemsConfig = Common::getGameConfig('items');
		$iteminfo = $itemsConfig[$params];
		if(empty($iteminfo))
		{
			return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
		}
		Common::loadModel('ItemModel');
		$ItemModel = new ItemModel($this->uid);
		if(empty($ItemModel->info[1][$iteminfo['itemId']]))
		{
			$data = array(
					'id'=>$iteminfo['itemId'],
					'amount'=>3,
					'total'=>$iteminfo['total'],
					'remain'=>$_SERVER['REQUEST_TIME']
			);
			$ItemModel->add($data);
			$ItemModel->destroy();
			$amount = 3;
			$iteminfo['remain'] = $iteminfo['total'];
		}
		else
		{
			
			$iteminfo = $ItemModel->info[1][$iteminfo['itemId']];
			//此段为了修复BUG
			if(empty($iteminfo['id']))
			{
				unset($ItemModel->info[1][$iteminfo['itemId']]);
				$iteminfo = $itemsConfig[$params];
				$data = array(
						'id'=>$iteminfo['itemId'],
						'amount'=>3,
						'total'=>$iteminfo['total'],
						'remain'=>$_SERVER['REQUEST_TIME']
				);
				$ItemModel->add($data);
				$ItemModel->destroy();
				$amount = 3;
				$iteminfo['remain'] = $iteminfo['total'];
				
			}
			else
			{
				$amount = $iteminfo['amount'];
				$iteminfo['itemId'] = $iteminfo['id'];	
				$remain = $iteminfo['total'] - ($_SERVER['REQUEST_TIME']-$iteminfo['remain']);
				$iteminfo['remain'] = $remain<0?0:$remain;
			}
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array(
						'itemId'=>$iteminfo['itemId'],
						'amount'=>$amount,
						'remain'=>$iteminfo['remain'],
						'total'=>$iteminfo['total'],
						'avail'=>'UNLOCKED'
						)
		);
		return $result;
	}
	
	
	/**
	 * 扣除玩家道具
	 * itemId	Integer	6153
amount	Integer	0
remain	Integer	86400
total	Integer	86400
avail	String	UNLOCKED
	 */
	
	public function useAppointmentItem()
	{
		$params = $this->params[1];
		
		Common::loadModel('ItemModel');
		$ItemModel = new ItemModel($this->uid);
		$itemid = $params[0];
		$iteminfo = $ItemModel->info[1][$itemid];
		
		$updata = array();
		
		//判断是否最后一个，最后一个则冷却
		if($iteminfo['amount']<=0)
		{
			return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
		}
		elseif($iteminfo['amount']==1)
		{
			$updata['remain'] = $_SERVER['REQUEST_TIME'];
		}
		$updata['amount'] = -1;
		$updata['ctype'] = 1;
		$updata['id'] = $itemid;
		$ItemModel->iUpdate($updata);
		$ItemModel->destroy();
		
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array(
						'itemId'=>$itemid,
						'amount'=>$ItemModel->info[1][$itemid]['amount'],
						'remain'=>$ItemModel->info[1][$itemid]['remain'],
						'total'=>$ItemModel->info[1][$itemid]['total'],
						'avail'=>'UNLOCKED'
						)
				);
		return $result;
	}
	
	public function clientException2()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array (
							
				)
		);
		return $result;
	}
	
	/**
	 * 解锁栏杆
	 * 开启7、145关  8、160关
	 */
	
	public function getCollaborationContainers()
	{
		switch (SNS)
		{
			case 'QQ':
				$unlocknum = 8;
				break;
			default:
				$unlocknum = 7;
				break;
		}
		$slots = array (
					0 =>
					array (
							'filled' => true,
							'friendId' => -1,
					),
					1 =>
					array (
							'filled' => true,
							'friendId' => -1,
					),
					2 =>
					array (
							'filled' => true,
							'friendId' => -1,
					),
			);
		$maps = array();
		for ($i=1;$i<=$unlocknum;$i++)
		{
			$maps[] = array(
					'id'=>$i,
					'slots'=>$slots
					);
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=> $maps
		);
		return $result;
	}
	/**
	 * 添加到收藏
	 */
	public function addToCollection()
	{
		//获取配置信息
		$levelsconfig = Common::getGameConfig('levels');
		$params = $this->params[1][0];
		$levelid = $params['levelId'];
		$levelinfo = $levelsconfig[$levelid];
		if(empty($levelinfo))
		{
			return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
		}
		$collectibleIds = $levelinfo['gameModeConfiguration']['collectibleRewardIds'];
		Common::loadModel('LevelModel');
		$LevelModel = new LevelModel($this->uid);
		$levelinfo = $LevelModel->info[$levelid];
		if(empty($levelinfo))
		{
			return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
		}
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$oldcollectibles = $UserModel->info['collections'];
		//根据头目星数获取收集物
		$stars = $levelinfo['stars'];
		$newcollectibles = array();
		foreach ($collectibleIds as $k=>$v)
		{
			if($stars>$k)
			{
				$newcollectibles[] = $v;
			}
			else
			{
				break;
			}
		}
		$diff = array_diff($newcollectibles, $oldcollectibles);
		if(!empty($diff))
		{
			$newcollectibles = array_merge($diff,$oldcollectibles);
			$updata = array('collections'=>$newcollectibles);
			$UserModel->iUpdate($updata);
			$UserModel->destroy();
		}
				
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array(
						'collectibles'=>$UserModel->info['collections']
						)
				);
		return $result;
	}
	
	/**
	 * 获取玩家收集的动物
	 */
	public function getAllCollectibles()
	{
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array (
						'collectibles' =>$UserModel->info['collections']
						
				)
		);
		return $result;
	}
	
	
	/**
	 * 显示窗口 
	 * 参数
	 * LOADER_SCREEN   APP_SCREEN
	 * @return multitype:number NULL
	 */
	public function GuiShown()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
		);
		return $result;
	}
	
	public function getMaxLives()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>5
		);
		return $result;
	}
	/**
	 * 获取玩家生命
	 */
	public function getLife()
	{
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$userinfo = $UserModel->info;
		$data = array(
				'lives'=>$userinfo['lives'],
				'lifeslots'=>$userinfo['lifeslots'],
				'freelives'=>$userinfo['freelives']
		);
		$lifeinfo = Common::getLives($data);
		$otherlives = $userinfo['activity']['lives'];
		$filllifetime = $userinfo['filllifetime'];
		$immortal = true;
		if($filllifetime<time())
		{
			$immortal = false;
		}
		//将玩家最新生命更新到数据库
		$newlives = $lifeinfo[0];
		$newfreelives = $lifeinfo[1];
		if($newlives != $userinfo['lives'])
		{
			$now = $_SERVER['REQUEST_TIME'];
			$updata = array(
					'lives'=>$newlives,
					'freelives'=>$lifeinfo[1]>=0?$now-$lifeinfo[2]:0,
			);
			$UserModel->iUpdate($updata);
			$UserModel->destroy();
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array (
						'lives' => $lifeinfo[0]+$otherlives,
						'timeToNextRegeneration' => $lifeinfo[1],
						'immortal' => $immortal,//无限生命boolean
				),
		);
		return $result;
	}
	
	
	
	public function addLives()
	{
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$userinfo = $UserModel->info;
		$data = array(
				'lives'=>$userinfo['lives'],
				'lifeslots'=>$userinfo['lifeslots'],
				'freelives'=>$userinfo['freelives']
				);
		$lifeinfo = Common::getLives($data);
		$otherlives = $userinfo['activity']['lives'];
		$coollife = $userinfo['coollife'];
		$activity = $userinfo['activity'];
		$alllives = $lifeinfo[0]+$otherlives;
		$newfreelives = $lifeinfo[1];
		$updata = array();
		if($coollife)
		{
			$activity['lives'] = $otherlives+1;
			$updata['activity']  = $activity;
			$alllives +=1;
		}
		else 
		{
			$filllifetime = $userinfo['filllifetime'];
			$immortal = $filllifetime<time()?false:true;
			if(!$immortal)
			{
				if($lifeinfo[0]<$userinfo['lifeslots'])
				{
					$alllives +=1;
					$updata['lives'] = $lifeinfo[0]+1;
					if($lifeinfo[0]==($userinfo['lifeslots']-1))
					{
						$updata['freelives'] = 0;
						$newfreelives = -1;
					}
				}
				else
				{
					$updata['freelives'] = 0;
					$newfreelives = -1;
				}
			}
		}
		$updata['coollife'] = 0;
		$UserModel->iUpdate($updata);
		$UserModel->destroy();

		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array (
						'lives' => $alllives,
						'timeToNextRegeneration' => $newfreelives,
						'immortal' => $immortal,//无限生命boolean
				),
		);
		return $result;
	}
	
	public function removeLives()
	{
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$userinfo = $UserModel->info;
		$data = array(
				'lives'=>$userinfo['lives'],
				'lifeslots'=>$userinfo['lifeslots'],
				'freelives'=>$userinfo['freelives']
				);
		$lifeinfo = Common::getLives($data);
		$otherlives = $userinfo['activity']['lives'];
		if($lifeinfo[0]<=0 && $otherlives<=0)
		{
			return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
		}

		//判断是否有无限生命
		$filllifetime = $userinfo['filllifetime'];
		
		$immortal = true;
		if($filllifetime<time())
		{
			$immortal = false;
			if($lifeinfo[0]>0)
			{
				
				$newlives = $lifeinfo[0]-1;
				$updata = array('lives'=>$newlives,'coollife'=>0);
				if($lifeinfo[0]>$userinfo['lives'])
				{
					if ($lifeinfo[1]>=0)
					{
						$newfreelives = $_SERVER['REQUEST_TIME']-$lifeinfo[2];
						$updata['freelives'] = $newfreelives;
					}
					else
					{
						$updata['freelives'] = 0;
					}
				}
				if($lifeinfo[1]<0)
				{
					$lifeinfo[1] = 1800;
					$freelives = $_SERVER['REQUEST_TIME'];
					$updata['freelives'] = $freelives;
				}
				$UserModel->iUpdate($updata);
				$UserModel->destroy();
			}
			elseif($otherlives>0)
			{
					$activity = $userinfo['activity'];
					$newotherlives = $otherlives-1;
					$activity['lives'] = $newotherlives;
					$updata = array('activity'=>$activity,'coollife'=>1);
					$UserModel->iUpdate($updata);
					$UserModel->destroy();		
			}
			else
			{
				return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
			}
		}
		
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array (
						'lives' => $lifeinfo[0]+$otherlives-1,
						'timeToNextRegeneration' => $lifeinfo[1],
						'immortal' => $immortal,//无限生命boolean
				),
		);
		return $result;
	}
	/**
	 * 获取玩家信息并同时设置为已读
	 * 	0 =>
			array (
					'id' => 65232675513,
					'toUserId' => 1653437416,
					'fromUserId' => 1077863367,
					'time' => 1368510993,
					'type' => 'requestLife',
					'data' => '',
			),
	 */
	public function fetchAndDeleteMessages()
	{
		Common::loadModel('MessageModel');
		$MessageModel = new MessageModel($this->uid);
		$messages= $MessageModel->info;
		$list = array();
		if(!empty($messages))
		{
			$info = array();
			foreach ($messages as $k=>$v)
			{
				$list[] = $v;
				$updata = array(
						'id'=>$v['id'],
						'utime'=>$_SERVER['REQUEST_TIME'],
						'state'=>2
						);
				$MessageModel->iUpdate($updata);
			}
		}
		$MessageModel->destroy();
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$list
		);
		return $result;
	}	
	public function getUrlMessageOncePerId()
	{
		$str = "";
	
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$str
		);
		return $result;
	}
	/**
	 * 更新完成的关卡
	 * //levelId	Integer	15
		//stars	Integer	1
		//episodeId	Integer	2
		//	Integer	124000
	 */
	public function publishCompletedLevel()
	{
		$params = $this->params[1][0];
		$levelId = $params['levelId'];
		$stars = $params['stars'];
		$episodeId = $params['episodeId'];
		$score = $params['score'];
		
		Common::loadModel('LevelModel');
		$LevelModel = new LevelModel($this->uid);
		$list = array_keys($LevelModel->info);
		$toplevel = array_pop($list);
		$maxlevel = $toplevel+1;
		if($levelId>$maxlevel)
		{
			return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
		}
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$completedlevel = $UserModel->info['completedlevel'];
		if( $completedlevel != $levelId)
		{
			$updata = array('completedlevel'=>$levelId);
			$UserModel->iUpdate($updata);
			$UserModel->destroy();
		}
		
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
		);
		return $result;
	}
	
	/**
	 * 货币相关 作用不明
	 */
	public function getCurrentUser()
	{
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$userinfo = $UserModel->info;
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array (
						'userId' => $this->uid,
						'externalUserId' => '100005386150832',
						'name' => $userinfo['name'],
						'firstName' => $userinfo['name'],
						'pic' => $userinfo['face'],
						'country' => 'china',
						'langCode' => 'zh_CN',
						'lastSignInTime' => $userinfo['last_logged_in'],
				)
		);
		return $result;
	}
	/**
	 * 获取玩家服务端时间
	 */
	public function getUserTime()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>microtime(true)*1000
		);
		return $result;
	}
	
	/**
	 * 新手引导的步数记录
	 * 0 => 1,
							      1 => 2,
							      2 => 3,
							      3 => 4,
							      4 => 20,
							      5 => 6,
							      6 => 7,
							      7 => 8,
							      8 => 9,
							      9 => 10,
							      10 => 5,
							      11 => 11,
							      12 => 12,
							      13 => 13,
							      14 => 14,
							      15 => 15,
							      16 => 16,
							      17 => 17,
							      18 => 18,
							      19 => 21,
							      20 => 19,
	 */
	public function getUserTutorialProgression()
	{
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$activity = $UserModel->info['activity'];
		$tutorial= empty($activity['tutorial'])?array():$activity['tutorial'];
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array (
						'completeTutorialIds' =>$tutorial
				)
		);
		return $result;
	}
	/**
	 *更新新手引导的步数
	 *
	 */
	public function updateUserTutorialProgression()
	{
		$params = $this->params[1][0]['tutorialId'];
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$activity = $UserModel->info['activity'];
		$activity['tutorial'] = empty($activity['tutorial'])?array():$activity['tutorial'];
		if(!in_array($params, $activity['tutorial']))
		{
			$activity['tutorial'][] = $params;
			$updata = array('activity'=>$activity);
			$UserModel->iUpdate($updata);
			$UserModel->destroy();
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array(
						'completeTutorialIds' =>$activity['tutorial']
				)
		);
		return $result;
	}
	
	
	/**
	 * 获取豌豆，金块
	 */
	public function getBalance()
	{
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$userinfo = $UserModel->info;
		//修复一下玩家负值问题
		$coins = $userinfo['coins'];
		if($coins<0)
		{
			$UserModel->iUpdate(array('coins'=>abs($userinfo['coins'])));
			$UserModel->destroy();
			$coins = 0;
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array (
						'softCurrency' => $coins ,
    					'hardCurrency' => $userinfo['cash'],
				)
		);
		return $result;
	}
	
	/*
	 * 用于BOSS关扣除豌豆
	*/
	public function deductBalance()
	{
		$params = $this->params[1];
	
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$dconins = abs($params[0]);
		if($dconins==0)
		{
			return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
		}
		//判断是否超出
		$oldcoins = $UserModel->info['coins'];
		if($dconins>$oldcoins)
		{
			return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
		}
		$UserModel->iUpdate(array('coins'=>-$dconins));
		$UserModel->destroy();
	
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array (
						'softCurrency' => $UserModel->info['coins'],
						'hardCurrency' => $UserModel->info['cash'],
				)
		);
		return $result;
	}
	
	
	/**
	 * 关卡胜利后传参增加
	 * 与getBalance有什么不同
	 * 
		[0]	Object
		details	String	level15
		softCurrencyDelta	Integer	75
		[1]	String	ccc63af8f6607864c2fe90f06b0e8e0f
	 */
	public function payoutSoftCurrency()
	{
		Common::loadModel('LevelModel');
		Common::loadModel('UserModel');
		$params = $this->params[1];
		
		$level = $params[0]['details'];
		$coins = $params[0]['softCurrencyDelta'];
		$scriet = $params[1];
		$scriet_str = 'BuFu6gBFv79BH9hk';
		$newscriet = md5($coins.':'.$level.':'.$scriet_str);
		if($scriet==$newscriet)
		{
			//增加coins并返回最新的货币
			$UserModel = new UserModel($this->uid);
			$updata = array('coins'=>$coins);
			$UserModel->iUpdate($updata);
			$UserModel->destroy();
			
			$result = array(
					'jsonrpc'=>2.0,
					'id'=>$this->params[0],
					'result'=>array (
							'softCurrency' => $UserModel->info['coins'],
							'hardCurrency' => $UserModel->info['cash'],
					)
			);
		}
		else
		{
			$result = $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
		}
		
		return $result;
	}
	
	/**
	 * 获得所有配置文件名
	 */
	public function getFiles()
	{
		$files =  Common::getGameConfig('files');
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>json_encode($files)
		);
		return $result;
	}
	
	public function synchronizeLevels()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
		);
		return $result;
	}
	
	/**
	 * Boosters  翻译为支持者？？？？？
	 * 0 =>
						array (
								'type' => 'FarmKingBoosterExtraMovesPreGame',
								'typeId' => 6101,
								'amount' => 0,
								'category' => 'farmKingBooster',
								'availability' => 2,
						),
						1 =>
						array (
								'type' => 'FarmKingBoosterAddMoves',
								'typeId' => 6100,
								'amount' => 0,
								'category' => 'farmKingBooster',
								'availability' => 2,
						),
	 */
	public function getBoosters()
	{
		Common::loadModel('ItemModel');
		$ItemModel = new ItemModel($this->uid);
		$iteminfo = $ItemModel->info[2];
		$list = array();
		if(!empty($iteminfo))
		{
			$itemsConfig = Common::getGameConfig('items');
			foreach($iteminfo as $itemid=>$v)
			{
				$list[] = array(
						'type' => $itemsConfig[$itemid]['type'],
					      'typeId' => $itemid,
					      'amount' => $v['amount'],
					      'category' => $itemsConfig[$itemid]['category'],
					      'availability' => $itemsConfig[$itemid]['availability'],	
						);
			}
		}
		
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$list
		);
		return $result;
	}
	
	/**
	 * 使用boosters类型的道具
	 */
	public function useBoosters()
	{
		
		$params = $itemid = $this->params[1][0];
		
		Common::loadModel('ItemModel');
		$ItemModel = new ItemModel($this->uid);
		$iteminfo = $ItemModel->info[2];
		
		foreach($params as $uselist)
		{
			$itemid = $uselist['boosterTypeId'];
			$amount = $uselist['amount'];
			if($amount>0)
			{
				//由于前端有BUG 先把 $amount 固定为1
				$amount = 1;
				if($iteminfo[$itemid]['amount']<$amount)
				{
					return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
				}
				
				
				$updata = array(
						'id'=>$itemid,
						'ctype'=>2,
						'amount'=>-$amount
				);
				$ItemModel->iUpdate($updata);
				$ItemModel->destroy();
			}
		}
		$itemsinfo = $ItemModel->info[2];
		$list = array();
		if(!empty($itemsinfo))
		{
			$itemsConfig = Common::getGameConfig('items');
			foreach($itemsinfo as $itemid=>$v)
			{
				$list[] = array(
						'type' => $itemsConfig[$itemid]['type'],
						'typeId' => $itemid,
						'amount' => $v['amount'],
						'category' => $itemsConfig[$itemid]['category'],
						'availability' => $itemsConfig[$itemid]['availability'],
				);
			}
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$list
				);
		return $result;
		
	}
	
	/**
	 * 用金块购买道具
	 * $productid 物品id
	 */
	public  function buyBoosters($productid)
	{
		$product = Common::getGameConfig('productpackagecode');
		$price = $product[$productid]['listPrices'][0]['cents'];
		$currency = $product[$productid]['listPrices'][0]['currency'];
		$price = $price/100;
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
		$oldcoins = $UserModel->info['coins'];
		$oldcash  = $UserModel->info['cash'];
		$result = false;
		if($currency == 'KHC')
		{
			if($oldcash >= $price)
			{
				$updata = array('cash'=>-$price);
				//购买相关道具
				$itemid = $product[$productid]['products'][0]['itemType'];
				Common::loadModel('ItemModel');
				$ItemModel = new ItemModel($this->uid);
				$itemupdata = array(
						'id'=>$itemid,
						'ctype'=>2,
						'amount'=>1
				);
				$ItemModel->iUpdate($itemupdata);
				$ItemModel->destroy();
				$result = true;
			}
		}
		if(!empty($updata))
		{
			$UserModel->iUpdate($updata);
			$UserModel->destroy();
			$result = true;
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$result
		);
		return $result;
		
		
	}
	
	/**
	 * 获取收集后的奖励道具
	 * 
	 *0 =>
	 array (
	 'itemId' => 6702,
	 'avail' => 'ACTIVATED',//已领取的状态 
	 ),
	 1 =>
	 array (
	 'itemId' => 6701,
	 'avail' => 'ACTIVATED',
	 ),
	 2 =>
	 array (
	 'itemId' => 6700,
	 'avail' => 'ACTIVATED',
	 ),
	 */
	public function getCollections()
	{
		Common::loadModel('ItemModel');
		$ItemModel = new ItemModel($this->uid);
		$iteminfo = $ItemModel->info[3];
		$list = array();
		if(!empty($iteminfo))
		{
			foreach($iteminfo as $itemid=>$v)
			{
				if($v['amount']>0)
				{
					$list[] = array(
							'itemId' => $itemid,
							'avail' => 'ACTIVATED',
							);
				}
			}
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=> $list,
		);
		return $result;
	}
	
	/**
	 * 解锁收集后的奖励道具
	 * collectionItemType
	 */
	public function unlockReward()
	{
		$params = $itemid = $this->params[1];
		
		
		Common::loadModel('ItemModel');
		$ItemModel = new ItemModel($this->uid);
		$iteminfo = $ItemModel->info[3];
		foreach($params as $list)
		{
			$itemid = $list['collectionItemType'];
			if(empty($iteminfo[$itemid]))
			{
				$data = array(
						'id'=>$itemid,
						'amount'=>0,
						'ctype'=>3,
				);
				$ItemModel->add($data);
				$ItemModel->destroy();
			}
		}
		$iteminfo = $ItemModel->info[3];
		$list = array();
		foreach($iteminfo as $itemid=>$v)
		{
			if($v['amount']==0)
			{
				$list[] = array(
						'itemId'=>$itemid,
						'avail'=>'UNLOCKED'
				);
			}
			
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$list
		);
		return $result;
	}
	/**
	 * 使用奖励道具
	 * 先判断是否已解锁了。如果已解锁将数据更新为1即为领取了
	 * 返回解锁状态的道具列表
	 */
	public function redeemReward()
	{
		$params = $itemid = $this->params[1];
		Common::loadModel('ItemModel');
		$ItemModel = new ItemModel($this->uid);
		$iteminfo = $ItemModel->info[3];
		$voitem = Common::getGameConfig('items');
		foreach($params as $list)
		{
			$itemid = $list['collectionItemType'];
			if(empty($iteminfo[$itemid]) )
			{
				return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
			}
			if($iteminfo[$itemid]['amount']==0)
			{
				$updata  = array(
						'id'=>$itemid,
						'ctype'=>3,
						'amount'=>1
				);
				$ItemModel->iUpdate($updata);
				$ItemModel->destroy();
				//对应的道具
				$iteminfo = $voitem[$itemid];
				switch ($iteminfo['type'])
				{
					case 1:
						$updata  = array(
								'id'=>$iteminfo['itemid'],
								'ctype'=>1,
								'amount'=>$iteminfo['amount']
						);
						$ItemModel->iUpdate($updata);
						$ItemModel->destroy();
						break;
					case 2:
						Common::loadModel('UserModel');
						$UserModel = new UserModel($this->uid);
						$updata = array('coins'=>$iteminfo['amount']);
						$UserModel->iUpdate($updata);
						$UserModel->destroy();
						break;
					default:
						break;
				}
				
				
			}	
		}
		
		$iteminfo = $ItemModel->info[3];
		$list = array();
		foreach($iteminfo as $itemid=>$v)
		{
			if($v['amount']==0)
			{
				$list[] = array(
						'itemId'=>$itemid,
						'avail'=>'UNLOCKED'
				);
			}
				
		}
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>$list
				
		);
		return $result;
		
	}
	
	/**
	 * 解锁
	 *  'type' => 'FarmKingBoosterAddMoves',
      'typeId' => 6100,
      'amount' => 0,
      'category' => 'farmKingBooster',
      'availability' => 2,
      -------------------------------------------
      'type' => 'FarmKingBoosterExtraMovesPreGame',
      'typeId' => 6101,
      'amount' => 0,
      'category' => 'farmKingBooster',
      'availability' => 2,
	 * 
	 * @return multitype:number multitype:string number  NULL
	 */
	
	public function unlockBooster()
	{
		$itemid = $this->params[1][0];
		
		$itemsConfig = Common::getGameConfig('items');
		$iteminfo = $itemsConfig[$itemid];
		if(empty($iteminfo))
		{
			return $this->error(array('id'=>$this->params[0],'code'=>__FUNCTION__));
		}
		
		Common::loadModel('ItemModel');
		$ItemModel = new ItemModel($this->uid);
		$thisinfo = $ItemModel->info[2][$iteminfo['itemId']];
		if(empty($thisinfo))
		{
			
			$data = array(
					'id'=>$iteminfo['itemId'],
					'amount'=>1,
					'ctype'=>2
			);
			$ItemModel->add($data);
			$ItemModel->destroy();
			$amount = 1;
		}
		else
		{
			
			$amount = $thisinfo['amount'];
			$iteminfo['itemId'] = $thisinfo['id'];
		}
		
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>array(
						 'type' => $iteminfo['type'],
					      'typeId' => $iteminfo['itemId'],
					      'amount' => $amount,
					      'category' => $iteminfo['category'],
					      'availability' => $iteminfo['availability'],				
						)
		);
		return $result;
	}
	
	/**
	 * ????
	 */
	public function publishHighScore()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
		);
		return $result;
	}
	
	public function trackFreeBossLevelEntry()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
		);
		return $result;
	}
	
	/**
	 * 参数
	 *   array (
    'friendFacebookId' => 
    array (
      0 => '100005386150832',
      1 => '100003003538691',
    ),
    'levelId' => 1,
  )
	 */
	public function publishFriendBeaten()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
		);
		return $result;
	}
	
	public function publishPassFriend()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
		);
		return $result;
	}
	public function publishGaveLife()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
		);
		return $result;
	}
	
	public function missionAccomplished()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
				'result'=>false
		);
		return $result;
	}
	/**
	 * 
	 */
	public function purchase()
	{
		
	}
	
	public function GuiLeft()
	{
		$result = array(
				'jsonrpc'=>2.0,
				'id'=>$this->params[0],
		);
		return $result;
	}
    public function getCurrentUserAppSignIn()
    {
        return true;
    }
    public function getUserMetrics()
    {
        return true;
    }
    public function getFriendsForOtherGames()
    {
        return true;
    }

    public function getLifeRegenerationTimeInSeconds()
    {
        return true;
    }
	
	
}

<?php
class IndexController extends Controller{
	public function index()
	{
		Common::loadModel("UserModel");

		$UserModel = new UserModel($this->uid);

		//用于内测奖励
		//$isOldUser = $UserModel->isOldUser();
		//初始化用户
		if(empty($UserModel->info['uid']))
		{
			$api = Api::getInstance();
			$profile = $api->getUserProfile($this->sharding['ustr']);
			$uid =  $UserModel->newUser($profile[0]);
			$UserModel = new UserModel($this->uid);
			//黄钻等级
			$UserModel->info['is_vip'] = $profile[0]['is_vip'];
			$UserModel->info['is_year_vip'] = $profile[0]['is_year_vip'];
			$UserModel->info['vip_level'] = $profile[0]['vip_level'];
			$UserModel->_update = true;
			
			//首次登录
			$UserModel->info["firstloading"] = 1;
			$_firstloading = true;
		}
		//end 初始化用户


		//更新平台的昵称和头像
		$sns = Common::get_sns();
		$sns_info = array();
		
		switch ($sns)
		{
			case "pengyou":
				Common::loadModel("PyFriendsModel");
				$v = 1;
				if(!$UserModel->info["py_name"] || !$UserModel->info["py_face"])
				{
					$sns_info = array("py_name" , "py_face");
				}
				break;
			case "qzone":
				Common::loadModel("QzFriendsModel");
				$v = 2;
				if(!$UserModel->info["qz_name"] || !$UserModel->info["qz_face"])
				{
					$sns_info = array("qz_name" , "qz_face");
				}
				break;
			default:
				exit("sns_error");
		}
		if($sns_info || Common::getPlatformFriends(9))
		{
			if(!$profile)
			{
				$api = Api::getInstance();
				$profile = $api->getUserProfile($this->sharding['ustr']);
			}
		
			if($profile[0]["name"])
			{
				$udata = array($sns_info[0]=>$profile[0]["name"] , $sns_info[1]=>$profile[0]["face"]);
				$udata['sex'] = $profile[0]["sex"];
				$UserModel->iUpdate($udata);
		
				//同步黄钻等级
				$UserModel->info['is_vip'] = $profile[0]['is_vip'];
				$UserModel->info['is_year_vip'] = $profile[0]['is_year_vip'];
				$UserModel->info['vip_level'] = $profile[0]['vip_level'];
			}
		}
		//end 更新平台的昵称和头像

		//随机同步换钻等级信息
		$rand = rand(1,20);
		if(!isset($UserModel->info['is_vip']) || $rand >15)
		{
			if(!$profile)
			{
				$api = Api::getInstance();
				$profile = $api->getUserProfile($this->uids['ustr']);
			}
		
			if($profile[0]["name"])
			{
				$UserModel->info['is_vip'] = $profile[0]['is_vip'];
				$UserModel->info['is_year_vip'] = $profile[0]['is_year_vip'];
				$UserModel->info['vip_level'] = $profile[0]['vip_level'];
				$UserModel->_update = true;
			}
		}
		//end 随机同步黄钻等级信息
	
		$UserModel->init();


		//好友
		$FriendsModel = new FriendsModel($this->uid);
		//强制刷新，到平台获取好友
		if(empty($FriendsModel->info) || Common::getPlatformFriends(count($Friends->info)))
		{
			$api = Api::getInstance();
			$uids = $api->getAppFriendIds();
	
			if(is_array($uids) && !empty($uids))
			{
				$fuids = array_keys($FriendsModel->info);
				$insert_id = array_diff($uids,$fuids);
				$delete_id = array_diff($fuids,$uids);
				if($insert_id)
				{
					foreach ($insert_id as $tid)
					{
						if($tid) $FriendsModel->add($this->uid,$tid,$v);
					}
				}
				if($delete_id)
				{
					foreach ($delete_id as $tid)
					{
						$FriendsModel->delete($this->uid,$tid);
					}
				}
				$FriendsModel->_update = true;
			}
		}
		
		//----------------------------------------------------
		//活动相关
	
		//如果被邀请进入游戏
		if(!empty($_REQUEST["iopenid"]))
		{        	
			$iopenid = sprintf('%s',$_REQUEST["iopenid"]);//邀请人平台id  
			$inSharding = Common::getUid($iopenid);
			$inUid = sprintf('%d',$inSharding["uid"]);//邀请人id

			//此前未响应过其它人的邀请
			if(!$FriendsModel->info[$inUid]['invite_id'])
			{
				$iFriendsModel = new FriendsModel($inUid);
				$udata = array("tid"=>$this->uid, "invite_id"=>$inUid);
				$iFriendsModel->update($udata);
				$iFriendsModel->destroy();
				
				$udata = array("tid"=>$inUid, "invite_id"=>$inUid);
				$FriendsModel->update($udata);
				$FriendsModel->_update = true;
				//成功邀请到好友则增加value值
				$vUserModel = new UserModel($inUid);
				$activity = $vUserModel->info['activity'];
				$activity['friend_value'] += 100;
				$vUserModel->update(array('activity'=>$activity));
				$vUserModel->destroy();
				
				//被邀请记录到邀请队列里
				if(defined('INVITEONE') && INVITEONE)
				{
					Common::loadModel('ActivityModel');
					$vActivityModel = new ActivityModel($inUid);
					$weekinvite = $vActivityModel->info[3];
					$week = date('w');
					$days = $week==0?6:$week-1;
					$latesttime =  mktime(0, 0, 0, date("m")  , date("d")-$days, date("Y"));
					if(!empty($weekinvite))
					{
						$_updata = false;
						$iupdata = array();
						if($weekinvite['utime']<$latesttime)
						{
							$iupdata['utime'] = $_SERVER['REQUEST_TIME'];
							$iupdata['content'][] = $this->uid;
							$_updata = true;
						}
						else
						{
							$num = count($weekinvite['content']);
							if($num<5)
							{
								$newcontent = $weekinvite['content'];
								$newcontent[] = $this->uid;
								$iupdata['content'] = $newcontent;
								$iupdata['utime'] = $_SERVER['REQUEST_TIME'];
								$_updata = true;
							}
						}
						if($_updata)
						{
							$iupdata['id']=3;
							$vActivityModel->iUpdate($iupdata);
							$vActivityModel->destroy();
						}
					}
					else
					{
						//增加新的记录
						$adata = array(
								'id'=>3,
								'utime'=>$_SERVER['REQUEST_TIME'],
								'content'=>array($this->uid)
								);
						$vActivityModel->add($adata);
						$vActivityModel->destroy();
					}
					
				}
			}	    				
		}//邀请结束
		
		if($UserModel->_update)
		{
			$UserModel->destroy();
		}
		if($FriendsModel->_update)$FriendsModel->destroy();
		//end邀请好友
	
		
		//首次加载奖励
		if( defined('FIRSTLOAD') && FIRSTLOAD && $_firstloading)
		{
			
		}
		
		//-------------------------------------------------------------------------
		//公告相关
		$this->getSystermReword();
		//--------------------------------------------------------------------------
		//配置相关
		//加载时
		//1、生成玩家唯一标识
		$token = Common::setToken($this->uid);
		
		$friendsUid = array_keys($FriendsModel->info);
		//2、获取所有好友的基本信息以及最高关卡数
		$cache = Common::getCache();
		if(!empty($friendsUid))
		{
			$key = $this->uid."_friendlist";
			$friendlist = array();
			foreach($friendsUid as $fid)
			{
				$fUserModel = new UserModel($fid);
				$friendlist[$fid] = array(
						'name'=>$fUserModel->info['name'],
						'face'=>$fUserModel->info['face'],
						'last_logged_in'=>$fUserModel->info['last_logged_in'],
						'completedlevel'=>$fUserModel->info['completedlevel'],
						);
			}
			
			$cache->set($key, $friendlist);
		}
		//array_unshift($friendsUid,$this->uid);
		//$fb_sig_friends = implode(',',$friendsUid);

		
		if($sns=='qzone')
		{
			$this->flash_vars['server'] = $this->flash_vars['server_qz'];
			$this->flash_vars['remoteRpcServiceUrl'] = $this->flash_vars['remoteRpcServiceUrl_qz'];
		}

		unset($this->flash_vars['server_qz']);
		unset($this->flash_vars['remoteRpcServiceUrl_qz']);
		//根据玩家性别交叉推广
		//男性玩家特殊处理
		if($UserModel->info['sex'] && isset($this->flash_vars['gotoURL_m']))
		{
			$this->flash_vars['gotoURL'] = $this->flash_vars['gotoURL_m'];
			unset($this->flash_vars['gotoURL_m']);
		}
		$flashvars = array(
				"fv_fbid"	=>	$this->uid,
				"shard_id"	=>	$this->sharding['sharding_id'],
				'sessionKey' =>	$this->sharding['ustr'],
				'sns' =>$sns,
				"accessToken"=> $token
		);
		$flashvars = array_merge($this->flash_vars,$flashvars);
		//if($this->uid==10000) echo $this->flash_vars['qz_serverPath'];
		$flashvars = http_build_query($flashvars);
		//$flashvars = "sessionKey=26.ix15MUVmY.XsA.uC3&CDN=http://192.168.59.87/cdn/shuji/&remoteRpcServiceUrl=http://192.168.59.87/shuji/public/&isSecure=1";
		include TPL_DIR.'/index.php';
	}
	
	
	/**
	 * Q点充值
	 */
	public function qqPay()
	{
		//获取支付的配置文件
		$info = array(
				'uid'     => $this->uid,
				'content' => '支付专用',
		);
		
		//item_id组合（type ,itemtype）
		//金块   1_cash   红辣椒  1_coins 买满生命    2_4
		//购买道具 booter类   3_2_itemid    
		 //item类  3_1_itemid
		 //补签 4_day
		
		$item_id = $_GET['item_id'];
		if(empty($item_id))exit(json_encode(array("err_num" => 1,"msg" => 'empty_item_id')));
		$payinfo 	= explode('_', $item_id);
		$paytype 	= $payinfo[0];
		$itemtype 	= $payinfo[1];
		
		switch ($paytype)
		{
			case 1:
			{
				$info['item_id'] = $item_id;
				
				
				if($itemtype=='coins')
				{
					$info['title']   = '变态辣X1000';
					$info['content'] = '变态辣';
					$info['pic']     = $this->flash_vars['CDN']."/ms/images/pay/coins.jpg";
					$info['price']   = 60;
				}
				if($itemtype=='cash')
				{
					$info['title']   = '移动次数+5步';
					$info['content'] = '移动次数+5步';
					$info['pic']     = $this->flash_vars['CDN']."/ms/images/pay/6101.jpg";
					$info['price']   = 54;
				}
				$info['num']     = 1;
				$info['appmode'] = 1;
				break;
			}
			case 2:
			{
				$pricearr = array(1=>1314,2=>188,3=>14,4=>70);
				$titlearr = array(1=>'一周无限生命',2=>'24小时无限生命',3=>'小怪兽果酱x1',4=>'买满果酱');
				$info['item_id'] = $item_id.'_t';
				$info['price']   = $pricearr[$itemtype];
				$info['pic']     = $this->flash_vars['CDN'].'/ms/images/pay/lives.jpg';
				$info['title']   = $titlearr[$itemtype];
				$info['content']   = $titlearr[$itemtype];
				$info['num']     = 1;
				$info['appmode'] = 1;
				break;
			}
			case 3:
			{
				$itemid = $payinfo[2];
				$itemconfig = Common::getGameConfig('items');
				$info['appmode'] = 2;
				if($itemtype==2)
				{
					if($itemid==5068)
					{
						$payinfo[1]=1;
						$itemtype = 6155;
					}
					else
					{
						$product = Common::getGameConfig('productpackagecode');
						$itemtype = $product[$itemid]['products'][0]['itemType'];
					}
					$iteminfo = $itemconfig[$itemtype];
					$item_id = $payinfo[0].'_'.$payinfo[1].'_'.$itemtype;
					if($itemtype==6100)
					{
						$cache = Common::getCache();
						$_key = $this->uid."_level_status";
						$levelstatus = $cache->get($_key);
						$levelstatus['revive'] +=1;
						$cache->set($_key, $levelstatus);
						
						//复活按次数价格不同
						$num = $levelstatus['revive']-1;
						$num = $num<=0?0:$num;
						$num = $num>=3?3:$num;
						$iteminfo['price'] = $iteminfo['price'][$num];
						$item_id = $payinfo[0].'_'.$payinfo[1].'_'.$itemtype.'_'.$num;
						$info['appmode'] = 1;
					}
				}
				if($itemtype==1)
				{
					$itemtype = $itemid;
					$iteminfo = $itemconfig[$itemtype];
				}
				if(empty($iteminfo))
				{
					exit(json_encode(array("err_num" => 2,"msg" => 'error_item_id')));
				}
				
				$info['item_id'] = $item_id.'_t';
				$info['price']   = $iteminfo['price'];
				$info['pic']     = $this->flash_vars['CDN'].'/ms/images/pay/'.$itemtype.'.jpg';
				$info['title']   = $iteminfo['title'];
				$info['content']   = $iteminfo['title'];
				$info['num']     = 1;
				break;
			}
			case 4:
			{
				Common::loadModel('ActivityModel');
				$ActivityModel = new ActivityModel($this->uid);
				$content = $ActivityModel->info[4]['content'];
				if(empty($content))
				{
					exit('param_error');
				}
				if(in_array($itemtype, $content['days']))
				{
					exit('param_error');
				}
				$newrepair = $content['repair']+1;
				$price = (($newrepair>5?5:$newrepair)*10)+15;

				$info['item_id'] = $item_id.'_t';
				$info['price']   = $price;
				$info['pic']     = $this->flash_vars['CDN'].'/ms/images/pay/repair.jpg';
				$info['title']   = $itemtype.'号补签';
				$info['content']   = $itemtype.'号补签';
				$info['num']     = 1;
				$info['appmode'] = 1;
				break;
			}
			default:
				break;	
		}
				
		$api = Api::getInstance();
		$result = $api->qzBuyGoods($info);//直接支付
		
		if($result['ret'])
		{
			$data = array(
					"err_num" => $result['ret'],
					"msg" => $result['msg'],
			);
		}else{
			$data['res']['url'] = $result['url_params'];
			$data['res']['token'] = $result['token'];
		}
		echo json_encode($data);
	}
	
	/**
	 * 邀请即送奖励活动
	 */
	public function inviteOne()
	{
		if(defined('INVITEONE') && INVITEONE)
		{
			Common::loadModel('ActivityModel');
			$ActivityModel = new ActivityModel($this->uid);
			$dayinvite = $ActivityModel->info[2];
			$updata = array();
			if(!empty($dayinvite))
			{
				//判断  gtime 领取奖励时间   utime 邀请好友时间  content当天邀请的好友列表
				if(empty($dayinvite['gtime']) || (date('Ymd',$dayinvite['gtime'])<date('Ymd')))
				{
					$update = false;
					//判断当天是否邀请过好友
					if(empty($dayinvite['utime']) || date('Ymd',$dayinvite['utime'])==date('Ymd'))
					{
						//判断当前邀请的好友是否超过两个
						if(empty($dayinvite['content']) || count($dayinvite['content'])<5)
						{
							$oldlist = empty($dayinvite['content'])?array():$dayinvite['content'];
							//记录玩家已邀请的openid
							$openid = $_GET['id'];
							if(strstr($openid,','))
							{
								$openidlist = explode(',', $openid);
								foreach($openidlist as $k=>$v)
								{
									$openidlist[$k] = md5($v);
								}
							}
							else
							{
								$openidlist[] = md5($openid);
							}
							if($openidlist)
							{
								$list = array_merge($oldlist,$openidlist);
								$newlist = array_unique($list);
								$updata['content'] = $newlist;
								$updata['utime'] = $_SERVER['REQUEST_TIME'];
								$update = true;
							}
						}
					}
					else
					{
						//当天第一次邀请 清空之前的好友 重新记录好友信息
						$openid = $_GET['id'];
						if(strstr($openid,','))
						{
							$openidlist = explode(',', $openid);
							foreach($openidlist as $k=>$v)
							{
								$openidlist[$k] = md5($v);
							}
						}
						else
						{
							$openidlist[] = md5($openid);
						}
						if($openidlist)
						{
							$updata['content'] = $openidlist;
							$updata['utime'] = $_SERVER['REQUEST_TIME'];
							$update = true;
						}
					}
					if($update)
					{
						$updata['id'] = 2;
						$ActivityModel->iUpdate($updata);
						$ActivityModel->destroy();
					}
				}
			}
			else
			{
				$openid = $_GET['id'];
				if(strstr($openid,','))
				{
					$openidlist = explode(',', $openid);
					foreach($openidlist as $k=>$v)
					{
						$openidlist[$k] = md5($v);
					}
				}
				else
				{
					$openidlist[] = md5($openid);
				}
				//增加新的记录
				$adata = array(
						'id'=>2,
						'utime'=>$_SERVER['REQUEST_TIME'],
						'content'=>$openidlist
				);
				$ActivityModel->add($adata);
				$ActivityModel->destroy();
			}
	
		}
		echo json_encode(array('success'=>true));
	}
	
	
	
	
	
}

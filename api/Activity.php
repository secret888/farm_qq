<?php
/**
 * 活动信息控制器
 * 活动编号(id)
 * 1:黄钻活动
 * 2:每日发送5次邀请请求则赠送 
 *    两瓶果酱
 * 3：每周邀请好友，有超过5个好友安装则赠送 
 *    变态辣x1000(coins)、盗贼x5(6155)、收集物品全+1x5(6152)、火箭筒x3(6150)、大怪兽x3(6153)、增加5次移动次数x1(6101)
 * 4: 月度每日签到活动
 * 5: 以31天为周期 连续签到活动
 * 6:新手助力活动（暂时）
 * 
 * @category   Activity
 * @author     ming
 * @version    $Id: Activity.php 2012-10-24 Z$
 */
class Activity extends Base
{
	public $type;
	private $id;//活动编号
	/**
	 * 黄钻每日奖励
	 * @param array $param
	 */
	public function getVipDaily()
	{
		Common::loadModel('UserModel');
		$UserModel = new UserModel($this->uid);
	
		$acttype = $this->type;
		$rewordlist = $this->getVipReword();
		$today = date('Ymd');
		$bodies = array();
		switch ($acttype)
		{
			case 1:
				$activity = $UserModel->info['activity'];
				$ts = $activity['vip']['ts'];		
				$bodies = $rewordlist;
				if(empty($ts))
				{
					$bodies['isget']=0;
				}
				else
				{
					$gettime = date('Ymd',$ts);
					if($gettime<$today)
					{
						$bodies['isget']=0;
					}
					else
					{
						$bodies['isget']=1;
					}
				}
				$bodies['is_vip'] = $UserModel->info['is_vip'];
				$bodies['is_year_vip'] = $UserModel->info['is_year_vip'];
				$bodies['vip_level'] = $UserModel->info['vip_level'];
				break;
			case 2:
				if($UserModel->info['is_vip'])
				{
					$activity = $UserModel->info['activity'];
					$ts = $activity['vip']['ts'];
					if(empty($ts) || (date('Ymd',$ts)<$today))
					{
						$coins = 0;
						$otherlives = 0;
							
						$vip_level = $UserModel->info['vip_level'];
						$is_year_vip = $UserModel->info['is_year_vip'];
						//领取奖励
						$reword = $rewordlist['vip'][$vip_level];
						$coins += $reword['c'];
						$otherlives += $reword['l'];
						if($is_year_vip)
						{
							$rewordyear = $rewordlist['vipyear'];
							$otherlives += $rewordyear['l'];
						}
						$data['coins'] = $coins;
						$activity['vip']['ts'] = $_SERVER['REQUEST_TIME'];
						if($otherlives>0)
						{
							$activity['lives'] +=$otherlives;
						}
						$data['activity'] = $activity;
						$UserModel->iUpdate($data);
						$UserModel->destroy();
						$bodies['isget'] = 1;
					}
				}
				break;
			default:
				break;
					
		}
		return $bodies;
	}
	
	public function getVipReword()
	{
		$vipreword = array(
				1=>array('c'=>50),
				2=>array('c'=>100),
				3=>array('c'=>100,'l'=>1),
				4=>array('c'=>150,'l'=>1),
				5=>array('c'=>200,'l'=>1),
				6=>array('c'=>200,'l'=>2),
				7=>array('c'=>250,'l'=>2),
				8=>array('c'=>300,'l'=>2)
		);
		$vipyearreword = array('l'=>1);
		$result['vip'] = $vipreword;
		$result['vipyear'] = $vipyearreword;
		return $result;
	}
	
	/**
	 * 邀请好友活动
	 * type:
	 * 1、获取日邀请数据，周邀请数据
	 * 2、领取日邀请数据
	 * 3、领取周邀请数据
	 */
	public function getInviteReword()
	{
		Common::loadModel('ActivityModel');
		$ActivityModel = new ActivityModel($this->uid);
		if(!(defined('INVITEONE') && INVITEONE))
		{
			exit('{"error":"inviteone_is_close"}');
		}
		$acttype = $this->type;
		
		
		$bodies = array();
		switch ($acttype)
		{
			case 1:
			{
				$dayinvite = $ActivityModel->info[2];
				
				//获取已邀请人数是否领取过
				if(!empty($dayinvite))
				{
					$data =array(
							'utime'=>$dayinvite['utime'],
							'gtime'=>$dayinvite['gtime'],
							'round'=>1
							); 
					$state = $this->getrewordstate($data);
					//判断上次邀请好友是不是今天
					if($state['up']==1)
					{
						$bodies['day']['num'] = 0;
					}
					else
					{
						$bodies['day']['num'] = count($dayinvite['content'])>5?5:count($dayinvite['content']);
					}
					$bodies['day']['isget'] = $state['get']==1 ? 0 : 1;
				}
				else
				{
					$bodies['day']['num']=0;
					$bodies['day']['isget']=0;
				}
				//周邀请成功5个
				$weekinvite = $ActivityModel->info[3];
				if(!empty($weekinvite))
				{
					$data =array(
							'utime'=>$weekinvite['utime'],
							'gtime'=>$weekinvite['gtime'],
							'round'=>7
					);
					$state = $this->getrewordstate($data);
					if($state['up']==1)
					{
						$bodies['week']['num'] = 0;
					}
					else
					{
						$bodies['week']['num'] = count($weekinvite['content'])>5?5:count($weekinvite['content']);
					}
					$bodies['week']['isget'] = $state['get']==1 ? 0 : 1;
				}
				else
				{
					$bodies['week']['num']=0;
					$bodies['week']['isget']=0;
				}
				$bodies['now'] = $_SERVER['REQUEST_TIME'];
				break;
			}
			case 2:
			{
				//领取日奖励
				//果酱(lives)x2
				$dayinvite = $ActivityModel->info[2];
				$data =array(
						'utime'=>$dayinvite['utime'],
						'gtime'=>$dayinvite['gtime'],
						'round'=>1
				);
				$state = $this->getrewordstate($data);
				
				if($state['get']==1)
				{
					$num = count($dayinvite['content']);
					if($state['up']==2 && $num>=3)
					{
						Common::loadModel('UserModel');
						$UserModel = new UserModel($this->uid);
						$activity = $UserModel->info['activity'];
						$activity['lives'] +=2;
						$UserModel-> iUpdate(array('activity'=>$activity));
						$result = $UserModel->destroy();
						if(!$result)
						{
							return $this->error('cache_set_error_#2');
						}
						$updata = array(
								'uid'=>$this->uid,
								'id'=>2,
								'gtime'=>$_SERVER['REQUEST_TIME']
								);
						$ActivityModel->iUpdate($updata);
						$ActivityModel->destroy();
						$bodies['isget']=1;
					}
				}
				break;
			}
			case 3:
			{
				//领取周奖励
				// 变态辣x1000(coins)、盗贼x5(6155)、收集物品全+1x5(6152)、火箭筒x3(6150)、大怪兽x3(6153)、增加5次移动次数x1(6101)
				$weekinvite = $ActivityModel->info[3];
				$data =array(
						'utime'=>$weekinvite['utime'],
						'gtime'=>$weekinvite['gtime'],
						'round'=>7
				);
				$state = $this->getrewordstate($data);
				if($state['get']==1)
				{
					$num = count($weekinvite['content']);
					if($state['up']==2 && $num>=5)
					{
						$updata = array(
								'uid'=>$this->uid,
								'id'=>3,
								'gtime'=>$_SERVER['REQUEST_TIME']
						);
						$ActivityModel->iUpdate($updata);
						$ActivityModel->destroy();
						
						Common::loadModel('UserModel');
						$UserModel = new UserModel($this->uid);
						$UserModel-> iUpdate(array('coins'=>1000));
						$result = $UserModel->destroy();
						Common::loadModel('ItemModel');
						$ItemModel = new ItemModel($this->uid);
						$ItemModel->iUpdate(array('ctype'=>2,'id'=>6101,'amount'=>1));
						$ItemModel->iUpdate(array('ctype'=>1,'id'=>6155,'amount'=>5));
						$ItemModel->iUpdate(array('ctype'=>1,'id'=>6152,'amount'=>5));
						$ItemModel->iUpdate(array('ctype'=>1,'id'=>6150,'amount'=>3));
						$ItemModel->iUpdate(array('ctype'=>1,'id'=>6153,'amount'=>3));
						$ItemModel->destroy();
						$bodies['isget']=1;
					}
				}
				break;
			}
			default:
				break;
		}
		return $bodies;
	}
	
	/**
	 * 月度签到
	 * 活动id:4
	 * 按月为周期
	 * 签到累积达到 2天可以领取
	 */
	public function getSigninReword($param)
	{
		Common::loadModel('ActivityModel');
		$ActivityModel = new ActivityModel($this->uid);
		$this->id = 4;
		$acttype = $this->type;
		$signininfo = $ActivityModel->info[$this->id];
		$bodies = array();
		switch ($acttype)
		{
			case 1:
			{
				//获取基本信息
				if(!empty($signininfo))
				{
					$data =array(
							'utime'=>$signininfo['utime'],
							'gtime'=>$signininfo['gtime'],
							'round'=>30
					);
					$state = $this->getrewordstate($data);
					if($state['up']==2)
					{
						$bodies['reword'] = $signininfo['content'];
					}
					else
					{
						$bodies['reword'] = array(
								'days'=>array(),
								'repair'=>0,
								'get'=>array()
						);
					}
					
				}
				else
				{
					$bodies['reword'] = array(
							'days'=>array(),
							'repair'=>0,
							'get'=>array()
							);
				}
				$bodies['date'] = array(
						'mother'=>date('n'),
						'day'=>date('t'),
						'today'=>date('j')
						);
				break;
			}
			case 2:
			{
				//当天签到
				$today = date('j');
				if(!empty($signininfo))
				{
					$data =array(
							'utime'=>$signininfo['utime'],
							'gtime'=>$signininfo['gtime'],
							'round'=>30
					);
					$state = $this->getrewordstate($data);
					if($state['up']==2)
					{
						//判断当天是否已领取
						$oldcontent = $signininfo['content'];
						if(!in_array($today, $oldcontent['days']))
						{
							$newdays = $oldcontent['days'];
							$newdays[] = $today;
							$content = array(
									'days' => $newdays,
									'repair'=>$oldcontent['repair'],
									'get'=>$oldcontent['get'],
									);
							
							$updata = array(
									'id'=>$this->id,
									'utime'=>$_SERVER['REQUEST_TIME'],
									'content'=>$content,
							);
							$ActivityModel->iUpdate($updata);
							$ActivityModel->destroy();
							Common::loadModel('UserModel');
							$UserModel = new UserModel($this->uid);
							$oldactivity = $UserModel->info['activity'];
							$oldlives = $oldactivity['lives'];
							$activity = $oldactivity;
							$activity['lives'] = $oldlives+1;
							$UserModel->iUpdate(array('activity'=>$activity));
							$UserModel->destroy();
						}
					}
					else
					{
						$content = array(
							'days'=>array($today),
							'repair'=>0,
							'get'=>array()
						);
						$updata = array(
							'id'=>$this->id,
							'utime'=>$_SERVER['REQUEST_TIME'],
							'content'=>$content,
							);
						$ActivityModel->iUpdate($updata);
						$ActivityModel->destroy();
					}
				}
				else
				{
					$content = array(
							'days'=>array($today),
							'repair'=>0,
							'get'=>array()
					);
					$updata = array(
							'id'=>$this->id,
							'utime'=>$_SERVER['REQUEST_TIME'],
							'content'=>$content,
							);
					$ActivityModel->add($updata);
					$ActivityModel->destroy();
				}
				$bodies['success'] = true;
				break;
			}
			case 3:
			{
				//领取奖励	
				$rtype = $param[0];
				if(empty($rtype))
				{
					exit('{"error":"param_erro"}');
				}
				if(!empty($signininfo))
				{
					
					$data =array(
							'utime'=>$signininfo['utime'],
							'gtime'=>$signininfo['gtime'],
							'round'=>30
					);
					$state = $this->getrewordstate($data);
					if($state['up']==2)
					{
						$content = $signininfo['content'];
						//判断当前天数是不是已经领取过奖励
						//判断签到总数是不是大等于领奖天数
						//奖励物品
						//2天 盗贼x2(6155) 火箭筒x1(6150)
						//7天 盗贼x3(6155) 火箭筒x2(6150) 变态辣x300	
						//15天 盗贼x3(6155) 火箭筒x2(6150) 全部收集物+1 x2(6152)  变态辣x500	
						//28天 盗贼x3(6155) 火箭筒x3(6150) 全部收集物+1 x2(6152) 大怪兽x1(6153)  变态辣x1000
						
						$get = $content['get'];
						$days = count($content['days']);
						if(!in_array($rtype, $get) && ($days>=$rtype))
						{
							//更新领取状态
							$newcontent = $content;
							$newget = $newcontent['get'];
							$newget[] =  $rtype;
							$newcontent['get'] = $newget;
							$ActivityModel->iUpdate(array('id'=>$this->id,'content'=>$newcontent));
							$ActivityModel->destroy();
							Common::loadModel('ItemModel');
							$ItemModel = new ItemModel($this->uid);
							Common::loadModel('UserModel');
							$UserModel = new UserModel($this->uid);
							switch ($rtype)
							{
								case 2:
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6155,'amount'=>2));
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6150,'amount'=>1));
									$ItemModel->destroy();
									break;
								case 7:
									$UserModel->iUpdate(array('coins'=>300));
									$UserModel->destroy();
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6155,'amount'=>3));
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6150,'amount'=>2));
									$ItemModel->destroy();
									break;
								case 15:
									$UserModel->iUpdate(array('coins'=>500));
									$UserModel->destroy();
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6155,'amount'=>3));
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6150,'amount'=>2));
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6152,'amount'=>2));
									
									$ItemModel->destroy();
									break;
								case 28:
									$UserModel->iUpdate(array('coins'=>1000));
									$UserModel->destroy();
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6155,'amount'=>3));
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6150,'amount'=>3));
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6152,'amount'=>2));
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6153,'amount'=>1));
									$ItemModel->destroy();
									break;
								default:
									break;
							}
							$bodies['isget'] = 1;
						}
					}
				}
				break;
			}
			default:
				break;
		}
		
		
		return $bodies;
		
	}
	
	
	/**
	 * 周期为31天的连续签到活动
	 * 活动id:5
	 * 5=>array(
	 *   days=>0,已经连续签到了几天
	 *   gtime=>0  上次签到的时期
	 * )
	 */
	public function getDaySign()
	{
		Common::loadModel('ActivityModel');
		$ActivityModel = new ActivityModel($this->uid);
		$this->id = 5;
		$alldays = 31;
		$signininfo = $ActivityModel->info[$this->id];
		$acttype = $this->type;
		$bodies = array();
		switch ($acttype)
		{
			case 1:
			{
				//获取奖励配置信息
				$vodaysign = Common::getGameConfig('daysign');
				
				$newdaysign = array();
				foreach ($vodaysign as $k=>$v)
				{
					$value = array();
					$value['days'] = $k;
					foreach ($v as $x=>$y)
					{
						if($x=='i')
						{
							foreach($y as $m=>$n)
							{
								$value[$m]=$n;
							}
						}
						else
						{
							$value[$x]=$y;
						}
					}
					$newdaysign[] = $value;
					
				}
				$bodies[] = $newdaysign;
				$getinfo = array(
						'days'=>0,
						'isget'=>0
						);
				//获取基本信息
				if(!empty($signininfo))
				{
					$signininfo = $signininfo['content'];
					//判断上次领取时间
					$data =array(
							'gtime'=>$signininfo['gtime'],
							'round'=>2
					);
					$state = $this->getrewordstate($data);
					if($state['get']==2)
					{
						//昨天已领取 则判断当天是否已领取
						$data =array(
								'gtime'=>$signininfo['gtime'],
								'round'=>1
						);
						$state = $this->getrewordstate($data);
						if($state['get']==2)
						{
							$getinfo = array(
									'days'=>$signininfo['days'],
									'isget'=>1
							);
						}
						else
						{
							//判断昨天领取是否为第31天
							if($signininfo['days']<$alldays)
							{
								$getinfo = array(
										'days'=>$signininfo['days'],
										'isget'=>0
								);
							}		
						}
					}
				}
				$bodies[] = $getinfo;
				break;
			}
			case 2:
			{
				
				$get = false;
				$days = 0;
				
				//获取基本信息
				if(!empty($signininfo))
				{
					$signininfo = $signininfo['content'];
					//判断上次领取时间
					$data =array(
							'gtime'=>$signininfo['gtime'],
							'round'=>2
					);
					$state = $this->getrewordstate($data);
					if($state['get']==2)
					{
						//昨天已领取 则判断当天是否已领取
						$data =array(
								'gtime'=>$signininfo['gtime'],
								'round'=>1
						);
						$state = $this->getrewordstate($data);
						if($state['get']==1)
						{
							$days = $signininfo['days'];
							$get = true;
						}
					}
					else
					{
						$get = true;
					}
				}
				else
				{
					$get = true;
				}
				if($get)
				{
					//获取奖励配置信息
					$vodaysign = Common::getGameConfig('daysign');
					$newdays = $days>=$alldays?1:$days+1;
					Common::loadModel('UserModel');
					$UserModel = new UserModel($this->uid);
					$activity = $UserModel->info['activity'];
					$newactivity = $activity;
					$udata = array();
					
					//奖励数据
					$rewordinfo = $vodaysign[$newdays];
					foreach($rewordinfo as $key=>$value)
					{
						switch ($key)
						{
							case 'l':
								$newactivity['lives'] = $activity['lives']+$value;
								break;
							case 'c':
								$udata['coins']=$value;
								break;
							case 'i':
								Common::loadModel('ItemModel');
								$ItemModel = new ItemModel($this->uid);
								$itemsConfig = Common::getGameConfig('items');
								foreach($value as $k=>$v)
								{
									$ctype = $k>=6150?1:2;
									$iteminfo = $ItemModel->info[$ctype][$k];
									if(!empty($iteminfo))
									{
										$ItemModel->iUpdate(array('ctype'=>$ctype,'id'=>$k,'amount'=>$v));
									}
									else
									{
										$iteminfo = $itemsConfig[$k];
										$data = array(
												'id'=>$k,
												'amount'=>3+$v,
												'total'=>$iteminfo['total'],
												'remain'=>$_SERVER['REQUEST_TIME'],
												'ctype'=>$ctype
										);
										$ItemModel->add($data);
									}
								}
								$ItemModel->destroy();
								break;
							default:
								break;
						}
						
						$udata['activity'] = $newactivity;
						$UserModel->iUpdate($udata);
						$UserModel->destroy();
							
						$newcontent = array('days'=>$newdays,'gtime'=>$_SERVER['REQUEST_TIME']);
						$ActivityModel->iUpdate(array('id'=>$this->id,'content'=>$newcontent));
						$ActivityModel->destroy();
						$bodies['isget']=1;
					}
					
					
				}
				break;
			}
			
		}
		
		return $bodies;
	}
	/**
	 * 新手助力活动
	 * 当关卡达到 6、8、14时 可领取相关奖励
	 * 
	 */
	public function getLevelReword($param)
	{
		Common::loadModel('ActivityModel');
		$ActivityModel = new ActivityModel($this->uid);
		$this->id = 6;
		$actinfo = $ActivityModel->info[$this->id];
		$acttype = $this->type;
		$bodies = array();
		switch ($acttype)
		{
			case 1:
			{
				if(!empty($actinfo))
				{
					$bodies = $actinfo['content'];
				}
				break;
			}
			case 2:
			{
				//领取奖励	
				$rtype = $param[0];
				$levels = array(6,8,14);
				if(empty($rtype))
				{
					exit('{"error":"param_erro"}');
				}
				if(in_array($rtype, $levels))
				{
					$_get = false;
					if(!empty($actinfo))
					{
						$content = $actinfo['content'];
						if(!in_array($rtype, $content))
						{
							$_get = true;
						}
					}
					else
					{
						$content = array();
						$_get = true;
					}
					if($_get)
					{
						//奖励物品
						//6关   果酱x2 盗贼x2(6155)
						//8关 果酱x8 火箭筒x2(6150) 变态辣x500
						//14关 果酱x16 大怪兽x2(6153) 增加5次移动次数x2(6101)
						Common::loadModel('ItemModel');
						$ItemModel = new ItemModel($this->uid);
						Common::loadModel('UserModel');
						$UserModel = new UserModel($this->uid);
						//判断玩家当前最高关卡是否超过领取的关卡数
						$completedlevel = $UserModel->info['completedlevel'];
						if($completedlevel>=$rtype)
						{
							$newcontent = $content;
							$newcontent[] = $rtype;
							
							$ActivityModel->iUpdate(array('id'=>$this->id,'content'=>$newcontent));
							$ActivityModel->destroy();
							
							$activity = $UserModel->info['activity'];
							switch ($rtype)
							{
								case 6:
									$activity['lives'] += 2;
									$UserModel->iUpdate(array('activity'=>$activity));
									$UserModel->destroy();	
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6155,'amount'=>2));
									$ItemModel->destroy();
									break;
								case 8:
									$activity['lives'] += 8;
									$UserModel->iUpdate(array('activity'=>$activity,'coins'=>500));
									$UserModel->destroy();
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6150,'amount'=>2));
									$ItemModel->destroy();
									break;
								case 14:
									$activity['lives'] += 16;
									$UserModel->iUpdate(array('activity'=>$activity));
									$UserModel->destroy();
									$ItemModel->iUpdate(array('ctype'=>1,'id'=>6153,'amount'=>2));
									$ItemModel->iUpdate(array('ctype'=>2,'id'=>6101,'amount'=>2));						
									$ItemModel->destroy();
									break;
								default:
									break;
							}
							$bodies['isget'] = 1;
						}
					}
				}
				break;
			}
					
		}
		return $bodies;
		
	}
	
	/**
	 * 根据日期获取当前的状态
	 * uptime 最新更新时间
	 * gtime  最新领取时间
	 * round  周期为 1、7
	 * @param $data
	 * @return array
	 * array(
	 * 'up'   1、周期内未更新  2、周期内有更新
	 * 'get'  1、周期内未领取  2、周期内已领取
	 * 
	 * )
	 */
	private function getrewordstate($data)
	{
		$utime = intval($data['utime']);
		$gtime = intval($data['gtime']);
		$round  = intval($data['round']);
		//计算上个周期结束时间
		switch ($round)
		{
			case 1:
				$days = 0;
				break;
			case 7:
				$week = date('w');
				$days = $week==0?6:$week-1;
				break;
			case 30:
				$days = date('j')-1;
				break;
			case 2:
				$days=1;
				break;
				
		}
		$data = date('Y').'-'.date('n').'-'.(date('j')-$days);
		$latesttime = strtotime($data);
		$result = array();
		$result['up']  = $utime<$latesttime ? 1 : 2 ;
		$result['get'] = $gtime<$latesttime ? 1 : 2 ;

		return $result;
	}
}

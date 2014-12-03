<?php
defined( 'IN_INU' ) or exit( 'Access Denied' );
/**
 * Controller
 *
 * @category   Controller
 * @author     ming 
 * @version    $Id: Controller.php 2013-07-17  ming$
 */
class Controller
{
	public $config = array();
	public $uid;
	public $sharding;
	public $flash_vars;
	
	public function __construct()
	{
	    $this->config = Common::getConfig();
	}
	
	
	
	
	/**
	 * 获取系统相关奖励
	 * @param unknown_type $idlist
	 */
	protected function getSystermReword()
	{
		//判断玩家是否有未领取的奖励
		//先判断是否有需要领取的系统信息
		//再从DB判断是否已领取过
		$cache = Common::getCache();
		$s_key = "systermmessage";
		$systermmessage = $cache->get($s_key);
		$mupdate = false;
		if(!empty($systermmessage))
		{
			$now = $_SERVER['REQUEST_TIME'];
			$db = Common::getDB($this->uid);
			foreach ($systermmessage as $key=>$value)
			{
				//判断此消息是否过期或无效
				if($value['etime']<=$now || empty($value['id']))
				{
					unset($systermmessage[$key]);
					$mupdate = true;
					continue;
				}
	
				//判断uid是否在发放范围内
				$touserid = $value['touserid'];
				$unget = true;
				switch ($touserid[0])
				{
					case 1:
						if($this->uid>=$touserid[1][0] && $this->uid<=$touserid[1][1])
						{
							$unget = false;
						}
						break;
					case 2:
						if(in_array($this->uid, $touserid[1]))
						{
							$unget = false;
						}
						break;
					default:
						break;
				}
				if($unget)
				{
					continue;
				}
	
				Common::loadModel('MessageModel');
				$MessageModel = new MessageModel($this->uid);
				$result = $MessageModel->getSInfo(1,$value['id']);
				if(!$result)
				{
					Common::loadModel('ItemModel');
					$ItemModel = new ItemModel($this->uid);
					$itemsConfig = Common::getGameConfig('items');
					$_update = false;
					$itemlist = $value['content'][0];
					$namelist = array();
					foreach ($itemlist as $name=>$num)
					{
						$namelist[] = $name;
						switch ($name)
						{
							case 'lives':
								{
									Common::loadModel('UserModel');
									$UserModel = new UserModel($this->uid);
									$activity = $UserModel->info['activity'];
									$activity['lives'] += $num;
									$UserModel->iUpdate(array('activity'=>$activity));
									$UserModel->destroy();
									break;
								}
							case 'coins':
								{
									Common::loadModel('UserModel');
									$UserModel = new UserModel($this->uid);
									$UserModel->iUpdate(array('coins'=>$num));
									$UserModel->destroy();
									break;
								}
							default:
								{
									$ctype = $name>=6150?1:2;
									$iteminfo = $ItemModel->info[$ctype][$name];
									if(!empty($iteminfo))
									{
										$ItemModel->iUpdate(array('ctype'=>$ctype,'id'=>$name,'amount'=>$num));
									}
									else
									{
										$iteminfo = $itemsConfig[$name];
										$data = array(
												'id'=>$name,
												'amount'=>3+$num,
												'total'=>$iteminfo['total'],
												'remain'=>$_SERVER['REQUEST_TIME'],
												'ctype'=>$ctype
										);
										$ItemModel->add($data);
									}
									$_update = true;
	
									break;
								}
						}
					}
					//写入公告
					$data = array(
							0=>$namelist,
							1=>$value['content'][1]
					);
					$adata = array(
							'data'=>json_encode($data),
							'type'=>1,
							'fromUserId'=>0,
							'systermid'=>$value['id']
					);
					$MessageModel->add($adata);
					$MessageModel->destroy();
						
					if($_update)
					{
						$ItemModel->destroy();
					}
				}
			}
		}
		if($mupdate)
		{
			$cache->set($s_key, $systermmessage,864000);
		}
	}
	
	/**
	 * BOSS关卡扣除道具用的接口
	 */
	public function receive()
	{
		//处理传过来的参数
		$post = $_POST;
		if(empty($post))
		{
			exit('params_is_error');
		}
		$data = array(
				"err_num" => 'error'
		);
		echo json_encode($data);
	}
	
	
}
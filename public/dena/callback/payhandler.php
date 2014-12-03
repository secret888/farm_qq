<?php
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );
define( "ROOT_DIR" , dirname( __FILE__ ) . '/../../..' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config" );
define( "MOD_DIR" , ROOT_DIR ."/model" );
define( "CON_DIR" , ROOT_DIR ."/controller/".SNS );
define( "TPL_DIR" , ROOT_DIR ."/tpl/" . SNS );
define( "LIB_DIR" , ROOT_DIR ."/lib" );
require LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';
require CON_DIR .'/Api.php';
include_once( 'include/php/config.php' );
include_once( 'include/php/RestApi/RestApi.class.php' );
$app = new  RestApi(_Consumer_Key,_Consumer_Secret,_App_ID);
$chsig=$app->Api('checkSign_HMAC_SHA1',array());


Common::loadModel("UserModel");
$db = Common::getDbName();	
if($chsig['sig_valid'])
{
	if($_REQUEST['order_id']<>'')
	{
		//第二次调用
		$orderid = $_REQUEST['order_id'];
		
		$ustr = $_REQUEST['opensocial_owner_id'];
		$uid = Common::getAppUid($ustr);
		$uid = $uid['uid'];
		$db = Common::getDB($uid);
		$sql = "select * from pay where `status`=1 and orderid=".$orderid;
		$result = $db->fetchRow($sql);
		if($result)
		{
			$sql = "update pay set `status`=2 where orderid=".$orderid;
			$rt = $db->query($sql);
			if(!$rt)
			{
				exit('error');
			}
		}
		//根据item_id发放相关的物品
		$item_id = $result['item_id'];
		//给用户添加物品
		$itemidlist = explode('_', $item_id);
		$paytype = $itemidlist[0];
		$itemtype = $itemidlist[1];
		Common::loadModel('UserModel');
		$UserModel = new UserModel($uid);
		
		switch ($paytype)
		{
			case 1:
				{
					if($itemtype=='coins')
					{
						$uArray = array(
								'coins' => 1000,
						);
					}
		
					//购买现金（加速道具）5个一组
					$UserModel->iUpdate($uArray);
					$UserModel->destroy();
					break;
				}
			case 2:
				{
					$uArray = array();
					$oldfreelives = $UserModel->info['freelives'];
					$now = $_SERVER['REQUEST_TIME'];
					switch ($itemtype)
					{
						case 1:
							//一周无限生命
							if($oldfreelives>$now)
							{
								$newtime = $oldfreelives+86400*7;
							}
							else
							{
								$newtime = $now+86400*7;
							}
							$uArray = array(
									'freelives' => $newtime,
							);
							break;
						case 2:
							//24小时无限生命
							if($oldfreelives>$now)
							{
								$newtime = $oldfreelives+86400;
							}
							else
							{
								$newtime = $now+86400;
							}
							$uArray = array(
									'freelives' => $newtime,
							);
							break;
						case 3:
							//生命+1
							$uArray = array(
							'lives' => $UserModel->info['lives']+1
							);
							break;
						case 4:
							//买满生命
							$lifeslots = $UserModel->info['lifeslots'];
							$lives     = $UserModel->info['lives'];
							$newlives  = -$lives+$lifeslots;
							$uArray = array(
									'lives' => $newlives,
									'filllifetime' =>''
							);
							break;
						default:
							break;
					}
					$UserModel->iUpdate($uArray);
					$UserModel->destroy();
					break;
				}
			case 3:
				{
					Common::loadModel('ItemModel');
					$ItemModel = new ItemModel($uid);
					$itemid = $itemidlist[2];
					if(empty($ItemModel->info[$itemtype][$itemid]))
					{
						exit(json_encode(array("ret"=>3 , "msg"=>"道具ID错误")));
					}
					$updata = array(
							'id'=>$itemid,
							'ctype'=>$itemtype,
							'amount'=>1
					);
					$ItemModel->iUpdate($updata);
					$ItemModel->destroy();
					break;
				}
			case 4:
				{
					//补签送赠送一条生命
					$oldactivity = $UserModel->info['activity'];
					$activity = $oldactivity;
					$activity['lives'] =$oldactivity['lives']+1;
					$UserModel->iUpdate(array('activity'=>$activity));
					$UserModel->destroy();
					Common::loadModel('ActivityModel');
					$ActivityModel = new ActivityModel($uid);
					$content = $ActivityModel->info[4]['content'];
					if(empty($content))
					{
						$content = array(
								'days'=>array($itemtype),
								'repair'=>0,
								'get'=>array()
						);
						$updata = array(
								'id'=>4,
								'utime'=>$_SERVER['REQUEST_TIME'],
								'content'=>$content,
						);
						$ActivityModel->add($updata);
						$ActivityModel->destroy();
					}
					else
					{
						$newdays = $content['days'];
						$newdays[] = $itemtype;
						$newrepair = $content['repair']+1;
						$newcontent = array(
								'days'=>$newdays,
								'repair'=>$newrepair,
								'get'=>$content['get']
						);
						$aupdata = array(
								'id'=>4,
								'utime'=>$_SERVER['REQUEST_TIME'],
								'content'=>$newcontent,
						);
						$ActivityModel->iUpdate($aupdata);
						$ActivityModel->destroy();
					}
					
					break;
				}
			default:
				break;
		}
		$amt = $result['amt'];
		$body1 = $app->Api('second_checkedsign_pay',array('order_id'=>$_REQUEST['order_id'],'amount'=>$amt));
		echo $body1;
	}
	else 
	{
		
		$dataarray2=file_get_contents("php://input");
		$paymentinfo_array=json_decode($dataarray2, true);
		$ustr = $_REQUEST['opensocial_owner_id'];
		$uid = Common::getAppUid($ustr);
		$uid = $uid['uid'];
		$skuid = $paymentinfo_array['ITEMS'][0]['SKU_ID'];
		$orderid = date('Y').date('m').date('d').$skuid.$uid;
		//获取此订单的信息
		$sql = "select * from pay where `status`=0 and orderid=".$orderid;
		$result = $db->fetchRow($sql);
		if($result)
		{
			$sql = "update pay set payment_id='{$paymentinfo_array["PAYMENT_ID"]}',`status`=1 where orderid=".$orderid;
			$rt = $db->query($sql);
			if(!$rt)
			{
				exit('error');
			}
		}
		
		$response_first = $app->Api('first_checkedsign_pay',array('order_id'=>$orderid));
		echo $app->Api('first_checkedsign_pay',array('order_id'=>$orderid));
	}
}
else{
	file_put_contents("/tmp/pay.error.log","sig_valid_err",FILE_APPEND);
}

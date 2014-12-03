<?php 
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );
session_start();
error_reporting(E_ALL^E_NOTICE);
define( "ROOT_DIR" , dirname( __FILE__ ) . '/../..' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config" );
define( "MOD_DIR" , ROOT_DIR ."/model" );
define( "LIB_DIR" , ROOT_DIR ."/lib" );
require LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';
$config = Common::getConfig();
$info = $_POST;
$secret = $config['api']['apiKey'];
$signed_request = $_POST['signed_request'];
/*
 * amount
	"1.00"
	
currency
	"USD"
	
payment_id
	340584202738952
	
quantity
	"1"
	
request_id
	"1_coins_papa_test_1"
	
signed_request
	"AxZLGw4kbfQExO3U6j4s0K_G07QL2S53pm0KF_4b_Yg.eyJhbGdvcml0aG0iOiJITUFDLVNIQTI1NiIsImFtb3VudCI6IjEuMDAiLCJjdXJyZW5jeSI6IlVTRCIsImlzc3VlZF9hdCI6MTM3ODcxMDM1NCwicGF5bWVudF9pZCI6MzQwNTg0MjAyNzM4OTUyLCJxdWFudGl0eSI6IjEiLCJyZXF1ZXN0X2lkIjoiMV9jb2luc19wYXBhX3Rlc3RfMSIsInN0YXR1cyI6ImNvbXBsZXRlZCJ9"
	
status
	"completed"
 * array(8) {
  ["algorithm"]=>
  string(11) "HMAC-SHA256"
  ["amount"]=>
  string(4) "1.00"
  ["currency"]=>
  string(3) "USD"
  ["issued_at"]=>
  int(1378722274)
  ["payment_id"]=>
  int(340613152735557)
  ["quantity"]=>
  string(1) "1"
  ["request_id"]=>
  string(22) "2013090910242110000_-1"
  ["status"]=>
  string(9) "completed"
}
 */
$secretdata = parse_signed_request($signed_request,$secret);
$request_id_1 = $_POST['request_id'];
$request_id_2 = $secretdata['request_id'];
list($orderid,$id) = explode('_', $request_id_2);
//验证request_id
if($request_id_1 != $request_id_2)
{
	$msg = array(
			'id'=>$id,
			'error'=>1,
			'msg' =>'request_id_error'
			);
	echo json_encode($msg);
	exit();
}
//查找订单状态
$db = Common::getDbName();
$sql = "select * from pay where status=0 and orderid=".$orderid;
$result  = $db->fetchRow($sql);
if($result)
{
	$sql = "update pay set `status`=2 where status=0 and orderid=".$orderid;
	$rt = $db->query($sql);
	if(!$rt)
	{
		$msg = array(
			'id'=>$id,
			'error'=>2,
			'msg' =>'sql_error'
			);
		echo json_encode($msg);
		exit();
	}
	//根据item_id发放相关的物品
	$item_id = $result['item_id'];
	$uid = $result['uid'];
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
	$msg = array(
			'id'=>$id,
			'error'=>0,
			'msg' =>''
	);
	echo json_encode($msg);
	exit();
}

/**
 * 解析signed_request数据
 * @param string $signed_request
 * @param string $secret
 */
function parse_signed_request($signed_request, $secret) {
	list($encoded_sig, $payload) = explode('.', $signed_request, 2);
	$sig = base64_url_decode($encoded_sig);
	$data = json_decode(base64_url_decode($payload), true);

	if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
		error_log('Unknown algorithm. Expected HMAC-SHA256');
		return null;
	}
	$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
	if ($sig !== $expected_sig) {
		error_log('Bad Signed JSON signature!');
		return null;
	}
	return $data;
}

function base64_url_decode($input) {
	return base64_decode(strtr($input, '-_', '+/'));
}








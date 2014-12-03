<?php
/**
 * QQ充值回调地址
 * 
 */

header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );

error_reporting(E_ALL^E_NOTICE);
define( "ROOT_DIR" , dirname( __FILE__ ) . '/..' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config" );
define( "MOD_DIR" , ROOT_DIR ."/model" );
define( "CON_DIR" , ROOT_DIR ."/controller/".SNS );
define( "TPL_DIR" , ROOT_DIR ."/tpl/" . SNS );
define( "LIB_DIR" , ROOT_DIR ."/lib" );
define( "API_DIR" , ROOT_DIR ."/api" );

require LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';
require CON_DIR .'/Api.php';
require API_DIR . "/Base.php";
Common::loadModel("CommonModel");

//file_put_contents("/tmp/v3_pay", var_export($_REQUEST,true),FILE_APPEND);
/*
$_REQUEST = array (
		'amt' => '50',
		'appid' => '100624380',
		'billno' => '-APPDJ11011-20120528-1614571632',
		'openid' => '53BEDC52CC3529F91DDDE498032144BC',
		'payamt_coins' => '',
		'payitem' => 'cash*5*1',
		'providetype' => '0',
		'pubacct_payamt_coins' => '',
		'token' => 'E24CBD9C9B13F341D32159253C39910917153',
		'ts' => '1338192898',
		'version' => 'v3',
		'zoneid' => '',
		'sig' => 'zPF8tBpABfiwbOkAYnGBO5o/zy4=',
);
*/
$oldsig = $_REQUEST['sig'];

unset($_REQUEST['sig']);
$config = Common::getConfig();
$secret = $config['api']['apiKey'].'&';
$sig = SnsSigCheck::makeBackSig('get', '/ceecloudpay/success.php', $_REQUEST, $secret);


if(strtolower($sig) != strtolower($oldsig)) exit(json_encode(array("ret"=>3 , "msg"=>"token错误"))); 
$data = $_REQUEST;

$payitem = explode("*",$data['payitem']); 
$data['item_id'] = $payitem[0];
$data['price'] = $payitem[1];
$data['num'] = $payitem[2];


$sharding = Common::getUid($data['openid']);
$data['uid'] = $sharding['uid'];

$cache = Common::getCache();
$db = Common::getDB($data['uid']);

$sql = "select * from `pay` where `billno`='{$data['billno']}' and `openid`='{$data['openid']}'";
$row = $db->fetchRow($sql);
if(!empty($row)) exit(json_encode(array("ret"=>2 , "msg"=>"订单重复"))); 

//给用户添加物品
$itemidlist = explode('_', $data['item_id']);
$paytype = $itemidlist[0];
$itemtype = $itemidlist[1];
Common::loadModel('UserModel');
$UserModel = new UserModel($data['uid']);

switch ($paytype)
{
	case 1:
	{
		if($itemtype=='coins')
		{
			$uArray = array(
					'coins' => $data['num']*1000,
			);
		}
		if($itemtype=='cash')
		{
			$uArray = array(
					'cash' => $data['num']*9,
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
					'lives' => $data['num']
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
		$ItemModel = new ItemModel($data['uid']);
		$itemid = $itemidlist[2];
		if(empty($ItemModel->info[$itemtype][$itemid]))
		{
			exit(json_encode(array("ret"=>3 , "msg"=>"道具ID错误")));
		}
		$updata = array(
				'id'=>$itemid,
				'ctype'=>$itemtype,
				'amount'=>$data['num']
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
		$ActivityModel = new ActivityModel($data['uid']);
		$content = $ActivityModel->info[4]['content'];
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
			'content'=>$content,
			);
		$ActivityModel->iUpdate($aupdata);
		$ActivityModel->destroy();
		break;
	}
	default:
		break;
}




$udata = array();
$udata['openid'] = $_REQUEST['openid'];//QQ号码转化得到的ID。
$udata['appid'] = $_REQUEST['appid'];//应用的唯一ID
$udata['ts'] = $_REQUEST['ts'];//UNIX时间戳
$udata['payitem'] = $_REQUEST['payitem'];//请使用x*p*num的格式，x表示物品ID，p表示单价（以Q点为单位，1Q币=10Q点），num表示实际的购买数量。
$udata['amt'] = $_REQUEST['amt'];//支付的总金额（注意，这里以0.1Q点为单位
$udata['billno'] = $_REQUEST['billno'];//支付流水号（64个字符长度。该字段和openid合起来是唯一的）。
$udata['sig']  = $_REQUEST['sig'];
$udata['item_id'] = $payitem[0];
$udata['price'] = $payitem[1];
$udata['num'] = $payitem[2];
$udata['uid'] = $data['uid'];
//记录
Common::pay($udata);

exit(json_encode(array("ret"=>0 , "msg"=>"OK"))); 
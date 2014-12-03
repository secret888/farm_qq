<?php
/**
 * QQ
 *
 * @category   Api
 * @author     fisher.lee <63764977@qq.com>
 * @version    $Id: Api.php 2011-11-28 13:00:12Z fisher.lee$
 */
require_once dirname(__FILE__) . '/sdk/v3/OpenApiV3.php';
class Api
{
	private $_instance;
	private static $apiInstance;
    protected $_open_id;
    protected $_open_key;
    protected $_pf;

	public static function getInstance()
	{
		if(!Api::$apiInstance)
		{
			Api::$apiInstance = new Api();
		}
		return Api::$apiInstance;
	}

	public function __construct()
	{

        if(isset($_GET['pf']))
		{
			$this->_pf     = $_GET['pf'];
			setcookie("qq_pf", $_GET['pf'], time()+3600*6);
		}
		else{
			$this->_pf = $_COOKIE['qq_pf'];
		}
	    if (!isset($_GET['open_id'])) {
            $this->_open_id = $_COOKIE["qq_openid"];
            $this->_open_key = $_COOKIE['qq_openkey'];
        } else {
            $this->_open_id  = $_GET["openid"];
            $this->_open_key = $_GET['openkey'];
            setcookie("qq_openid", $_GET['openid'], time()+3600*6);
            setcookie("qq_openkey", $_GET['openkey'], time()+3600*6);
        }

        if(empty($this->_open_id) || empty($this->_open_key)){
            echo "非法登录";exit;
        }
        
		$this->config = Common::getConfig();
		$this->_instance = new Pengyou($this->config['api']['appId'], $this->config['api']['apiKey']);
		$this->_instance->setServerName($this->config['api']['api_server']);
	}

	public function getLoggedInUser()
	{
		try {
			$sharding = Common::getUid($this->_open_id);
			$uids = array("uid"=>$sharding["uid"],"ustr"=>$sharding["ustr"],"sharding_id"=>$sharding["sharding_id"]);
		}catch (Exception $e){
			$uids = array();
		}
		return $uids;
	}
	
	public function getUserProfile($uids)
	{
		try {
	        $user_info = $this->_instance->getUserInfo($this->_open_id, $this->_open_key,$this->_pf);
	        if($user_info['figureurl'] && strpos($user_info['figureurl'],"http://") === false)
	        {
	        	$user_info['figureurl'] = "http://".$user_info['figureurl'];
	        }
	        $sharding = Common::getUid($this->_open_id);

			$user = $obj = array();
			$obj['uid'] = $sharding['uid'];
			$obj['ustr'] = $sharding['ustr'];
			$obj['sharding_id'] = $sharding['sharding_id'];
			$obj['name'] = $user_info['nickname'];
			$obj['face'] = $user_info['figureurl'];
			$obj['sex'] = $user_info['gender']=='男'?1:0;
			$obj['is_vip'] = $user_info['is_yellow_vip'];
			$obj['is_year_vip'] = $user_info['is_yellow_year_vip'];
			$obj['vip_level'] = $user_info['yellow_vip_level'];
			$user[] = $obj;
		}catch (Exception $e){
			print_r($e->getMessage());
		}

		if(empty($user))
		{
    		$obj = array();
    		$obj['uid'] = $sharding['uid'];
    		$obj['ustr'] = $sharding['ustr'];
    		$obj['sharding_id'] = $sharding['sharding_id'];
    		$obj['name'] = '';
    		$obj['face'] = '';
    		$obj['sex']  = 0;
			$obj['is_vip'] = 0;
			$obj['is_year_vip'] = 0;
			$obj['vip_level'] = 0;
    		$user[] = $obj;
		}
		
		return $user;
	}

	/**
	 获取使用过该app的好友列表
	 社区默认给新创建的用户一个ID为1的好友
	 @param  int $uid
	 @return array
	 */
	public function getAppFriendIds()
	{
		$data = array();
		try {
	        $friends = $this->_instance->getFriendList($this->_open_id,  $this->_open_key,$this->_pf);
	        if($friends['ret'] == 0) {
	            foreach($friends['items'] as $id) {
	                if($id['openid']!=$this->_open_id) {
	                    $sharding = Common::getUid($id['openid']);
				        $data[$sharding["ustr"]] = $sharding['uid'];
	                }
	            }
	        }
		}catch (Exception $e){
			print_r($e->getMessage());
		}
		return $data;
	}
	
	/**
	 * 获取未安装应用的平台好友
	 * @return string $openid
	 */
	public function getRcmdFriends()
	{
		$openid = '';
		$rcmdFriends = $this->_instance->gerRcmdFriend($this->_open_id,  $this->_open_key,$this->_pf);
		if(empty($rcmdFriends['ret']))
		{
			$items = $rcmdFriends['items'];
			$count = count($items);
			if($count>0)
			{
				
				foreach($items as $v)
				{
					$openid[] = $v['openid']; 
				}
				shuffle($openid);
				$openid = implode(',', $openid);
			}
		}
		return $openid;
	}
	/**
	 * Q点直冲
	 * Content-type: text/html; charset=utf-8
		{
		"ret":0,
		"is_lost":0,
		"url_params": "v1/m01/10227/pay/buy_goods?token_id=4021A324754CCD7EA01836261D0AFF7207622&sig=5b9feed5b43b8f8f829d19fb489814e4",
		"token": "4021A324754CCD7EA01836261D0AFF7207622"
		} 
	 */
	public function qzBuyGoods($info)
	{
		//用于开启特殊玩家的沙箱模式
		$cache = Common::getCache();
		if(in_array($this->_open_id, $cache->get('sandbox_ustrs')))
		{
			$this->_instance->setServerName($this->config['api']['api_server_sandbox']);
		}
		$server_name = '/v3/pay/buy_goods';
		$method = 'get';
		
		$params = array(
			'appid' => $this->config['api']['appId'],
			'openid' => $this->_open_id,
			'openkey' => $this->_open_key,
			'pf'     => $this->_pf,
			'pfkey'  => $this->_pf_key,
			'format' => 'json',
			'userip' => $this->ip(),
			'cee_extend' =>getenv("CEE_DOMAINNAME").'*'.getenv("CEE_VERSIONID").'*'.getenv("CEE_WSNAME"),
				
			'ts' => $_SERVER['REQUEST_TIME'],
			'payitem' => $info['item_id']."*".$info['price']."*".$info['num'],
			'goodsmeta' => $info['title']."*".$info['content'],
			'goodsurl' => $info['pic'],
			'appmode' => $info['appmode'],
			'zoneid'  =>0,
		);

		$result = $this->_instance->api($server_name,$params,$method,'https');

		return $result;
	}
	
	/*
	 * 验证是否登录 并 续期 openkey
	 * true 为正常登录
	 * false 为非法登录
	 */
	public function checkLogin()
	{
		$result = $this->_instance->isLogin($this->_open_id, $this->_open_key, $this->_pf);
		if($result['ret']==0)
		{
			return true;
		}
		else
		{
            if(defined('DEVELOP') && DEVELOP){
                return true;
            }else{
                return false;
            }
		}
	}
    public function ip()
    {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $onlineip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        return $onlineip;
    }
}

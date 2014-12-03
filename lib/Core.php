<?php
defined( 'IN_INU' ) or exit( 'Access Denied' );
/**
 * Core
 *
 * @category   Common
 * @author     fisher.lee <63764977@qq.com>
 * @version    $Id: Api.php 2011-05-22 14:57:20Z fisher.lee$
 */

class Common
{
    /**
     * 去掉反斜杠
     *
     * @param array $var
     * @return array    
     */
	public static function prepareGPCData( & $var )
	{
		if( is_array( $var ) )
		{
			while( list( $key , $val ) = each( $var ) )
			{
				$var[$key] = self::prepareGPCData( $val );
			}
		}
		else
		{
			$var = stripslashes( $var );
		}

		return $var;
	}

	/**
	 * 获取系统配置信息
	 *
	 * @return Array
	 */
	public static function getConfig( $key = 'Config' )
	{
		static $config;
		if( empty( $config[$key] ) )
		{
			if( !file_exists( CONFIG_DIR . "/{$key}.php" ) )
			return false;

			$config[$key] = require( CONFIG_DIR . "/{$key}.php" );
		}
		return $config[$key];
	}
	
	/**
	 * 获取游戏配置信息
	 *
	 * @return Array
	 */
	public static function getGameConfig($key)
	{
		static $config;
		if( empty( $config[$key] ) )
		{
			$cache = self::getCache();
			$config[$key] = $cache->get("vo_{$key}");
            if(!$config[$key]){
                //读取文件信息
                $config[$key] = Common::getConfig('game/'.$key);
                if(!$config[$key]){
                    throw new Exception('error vo_'.$key);
                }
                $cache->set($key,$config[$key],864000);
            }
		}
		return $config[$key];
	}

	 /**
	 * 获取后台配置信息
	 *
	 * @return Array
	 */
	public static function getConfigAdmin( $key = 'Config' )
	{
		static $config;
		if( empty( $config[$key] ) )
		{
			if( !file_exists( CONFIG_ADM_DIR . "/{$key}.php" ) )
			return false;

			$config[$key] = require( CONFIG_ADM_DIR . "/{$key}.php" );
		}
		return $config[$key];
	}

	/**
	 * 获取系统参数
	 *
	 * @param String $key
	 * @return String | Int
	 */
	public static function getParam( $key )
	{
		$config = self::getConfig();
		return $config['param'][$key];
	}

	/**
	 * 获取余数
	 *
	 * @param int $uid
	 * @return int
	 */
	public static function computeTableId($uid)
	{
		static $table_id = array();
		if(empty($table_id[$uid]))
		{
			$config = self::getConfig();
			$table_id[$uid] = str_pad( $uid % $config['param']['table_div'] , $config['param']['table_bit'] , "0" , STR_PAD_LEFT );
		}
	    return $table_id[$uid];
	}

	/**
	 * 加载Model
	 */
	public static function loadModel( $name )
	{
		$path = MOD_DIR . '/' . $name . '.php';
		require_once( $path );
	}
	
	/**
	 * 加载核心类文件
	 *
	 * @param string $name
	 */
	public static function loadLib( $name )
	{
		$path = LIB_DIR . '/' . $name . '.php';
		require_once( $path );
	}
	
    /**
     * 获取配置文件
     *
     * @param string $param
     * @return array
     */
	public static function getCache( $param = 'data' )
	{
		static $cache = array();
		if( empty( $cache[$param] ) )
		{
			$config = self::getConfig();
			$cache[$param] = new MemcachedClass( $config['memcache'][$param] );
		}
		return $cache[$param];
	}

	/**
	 * 通过uid加载DB
	 *
	 * @param int $uid
	 * @return array
	 */
	public static function getDB($uid)
	{
		static $db = array();
		if( empty( $db[$uid] ) )
		{
			$sharding = self::getSharding($uid);
			$dbSharding = self::getConfig("Sharding");
			$dbName = $dbSharding[$sharding['sharding_id']];
		
			$db[$uid] = self::getDbName($dbName);
		}
		return $db[$uid];
	}
	
	/**
	 * 通过dbName加载DB
	 *
	 * @param string $dbName
	 * @return array
	 */
	public static function getDbName($dbName="default")
	{
		static $db = array();
		if( empty( $db[$dbName] ) )
		{
			$dbConfig = self::getConfig( 'Db');

			if( !class_exists( 'Db' ) )
				self::loadLib( 'Db' );
            try{
                $db[$dbName] = new Db( $dbConfig[$dbName] );
            }catch (Exception $e){
                die("error: " . $e->__tostring() . "<br/>");
            }

		}
		return $db[$dbName];
	}
	
	/**
	 * 验证后台用户登陆状态
	 */
	public static function checkLogin()
	{
		if(empty($_SESSION['admin']))
		{
			if(!empty($_COOKIE['USPS']))
			{
				Common::loadLib('Encrypt');
				$encrypt=new Encrypt();
				$USPS = $encrypt->decrypt($_COOKIE['USPS'],"4399_yuanning");
				$USPS = explode('@',$USPS);
				if(!empty($_COOKIE['admin']) && !empty($USPS[0]) && $USPS[0] == $_COOKIE['admin']){
					$_SESSION['admin'] = $_COOKIE['admin'];
					return true;
				}
			}
			else 
			{
				if(empty($_GET['sig']) || strlen($_GET['sig'])!=32){
					header("HTTP/1.1 404 Not Found");exit;
				}
				else 
				{
					$_SESSION['admin']=1;
					return true;
				}	
			}
		}
	}
	
	/**
	 * 获取sharding
	 *
	 * @param string $ustr 平台ID
	 * $flag true:uid和ustr都相同   false:uid和ustr不相同   默认true
	 * @return array
	 */
	public static function getUid($ustr,$flag=false)
	{
		static $uids = array();
		if(empty($uids[$ustr]))
		{
			$key = "{$ustr}_ustr";
			$cache = Common::getCache();
			$sharding = $cache->get($key);

			
			if($sharding == false)
			{
			    $sharding = self::getUidForCache($ustr,$flag);
			    
			    $cache->set($key,$sharding);
			}
			$uids[$ustr] = $sharding;
		}

		return $uids[$ustr];
	}
	
	
	/**
	 * 获取已安装过应用的uid
	 */
	public static function getAppUid($ustr,$flag=false)
	{
		static $uids = array();
		if(empty($uids[$ustr]))
		{
			$key = "{$ustr}_ustr";
			$cache = Common::getCache();
			$sharding = $cache->get($key);

			
			if($sharding == false)
			{
			    $sharding = self::getUidForCache($ustr,$flag,0);
			    if($sharding)
			    {
			    	$cache->set($key,$sharding);
			    }
			    else
			    {
			    	return false;
			    } 
			}
			$uids[$ustr] = $sharding;
		}
		return $uids[$ustr];
	}
	/**
	 * 从DB中获取sharding信息
	 *
	 * @param array $ustr
	 * @param $type 1为重新创建  0仅查询
	 */
	public static function getUidForCache($ustr,$flag,$type=1)
	{
		$sql="select * from `gm_sharding` where `ustr` = '{$ustr}'";
	    $db = Common::getDbName();
		$sharding = $db->fetchRow($sql);

		if(!$sharding)
		{
			if($type)
			{
				$sharding = self::createUidForUstr($ustr,$flag);
			}
		}
		
		return $sharding;
	}
	
	/**
	 * 通过ustr创建适配uid
     * 通过gm_sharding表自增 uid
     * $flag参数 为true  可自由设定uid值
	 */
	public static function createUidForUstr($ustr,$flag)
	{
	    $sharding_rand = Common::getConfig("ShardingRand");

        //shuffle() 函数把数组中的元素按随机顺序重新排列
	    shuffle($sharding_rand);
	    $sharding_id = $sharding_rand[0];
	    
	    if($flag)
	    {
	    	$sql="insert into `gm_sharding` set `uid`='{$ustr}', `ustr` ='{$ustr}',`sharding_id`='{$sharding_id}'";
	    }else{
	    	$sql="insert into `gm_sharding` set `ustr` ='{$ustr}',`sharding_id`='{$sharding_id}'";
	    }
	    $db = Common::getDbName();
		$uid = $db->query($sql);
		if(empty($uid))
		{
			exit("insert_uid_error");
		}
		
		$sharding = array();
		$sharding['uid'] = $uid;
		$sharding['sharding_id'] = $sharding_id;
		$sharding['ustr'] = $ustr;
		
		return $sharding;
	}
	
	/**
	 * 获取用户sharding信息
	 *
	 * @param int $uid
	 * @return array
	 */
	public static function getSharding($uid)
	{
		static $static = array();
		if(empty($static[$uid]))
		{
    		$key = $uid."_sharding";
    		$cache = Common::getCache();
    		$sharding = $cache->get($key);
    		if($sharding == false)
    		{
    		    $sharding = self::getShardingForCache($uid);
    		    $cache->set($key,$sharding);
    		}
			$static[$uid] = $sharding;
		}
		return $static[$uid];
	}
	
	/**
	 * 从db获取用户sharding
	 *
	 * @param int $uid
	 * @return array
	 */
	public static function getShardingForCache($uid)
	{
	    $db = Common::getDbName();
	    $sql = "select * from `gm_sharding` where `uid`='$uid'";
	    $sharding = $db->fetchRow($sql);
	    
		return $sharding;
	}
	/**
	 * 更新玩家当前步数
	 * 保存玩家所有步数，如果发送已存在过的步数刚视为重刷
	 */
	public static function setStep($uid,$step)
	{
		$key = $uid."_update_step";
		$cache = Common::getCache();
		$step_arr = self::getStep($uid);
		$step_arr[] = $step;
		$result = $cache->set($key,$step_arr);
		return $result;
	}
	/**
	 * 获取玩家已执行的步数
	 */
	public static function getStep($uid)
	{
		$key = $uid."_update_step";
		$cache = Common::getCache();
		$result = $cache->get($key);
		if(empty($result))
		{
			$result = array();
		}
		return $result;
	}
	/**
	 * 清空玩家步数
	 */
	public static function clearStep($uid)
	{
		$key = $uid."_update_step";
		$cache = Common::getCache();
		$rsult = $cache->delete($key);
		return $rsult;
	}
	/**
	 * 获取token
	 *
	 * @param uid $uid
	 * @return string
	 */
	public static function getToken($uid)
	{
    	$key = $uid."_token";
    	$cache = Common::getCache();
    	$token = $cache->get($key);
        return $token;
	}
	
	/**
	 * 设置token
	 *
	 * @param int $uid
	 * @return string
	 */
	public static function setToken($uid)
	{
    	$key = $uid."_token";
    	$token = md5(uniqid().$uid.$_SERVER['REQUEST_TIME']);
    	$cache = Common::getCache();
    	$cache->set($key,$token);
        return $token;
	}
	
	/**
	 * 设置密钥
	 */
	public static function setSecret($param)
	{
		
		$baseinfo = $param[0];
		$callcount = $baseinfo->callCount;
		unset($param[0]);
		$str = '';
		if(!empty($param))
		{
			foreach ($param as $key=>$value)
			{
				if(is_array($value))
				{
					if(!empty($value))
					{
						foreach ($value as $k=>$v)
						{
							$str_temp[] = "{$k}={$v}";
						}
						$str[] = "{$key}=".implode('&', $str_temp);
					}
					else
					{
						$str[] = "{$key}=";
					}
				}
				elseif(is_object($value))
				{
					$value = get_object_vars($value);
					if(!empty($value))
					{
						foreach ($value as $k=>$v)
						{
							$str_temp[] = "{$k}={$v}";
						}
						$str[] = "{$key}=".implode('&', $str_temp);
					}
					else
					{
						$str[] = "{$key}=";
					}
				}
				else
				{
					$str[] = "{$key}={$value}";
				}
					
			}
			$str = implode('&', $str);
		}
		
		//$token = self::getToken($uid);
		//$str .= $token;
		$result = '';
		if(!empty($str))
		{
			$str .= "&b00b2cf4fc92bce7a067f2dd0445e8e8paopao";
			$str .= $callcount;
			$result = md5($str);
		}
		return $result;
	}
	
	/*
	 * 充值入库
	*/
	public function pay($data)
	{
		if(!empty($data) && is_array($data))
		{
			$sql = "";
			foreach ($data as $key=>$value)
			{
				$sql .= "`$key`='$value',";
			}
			$sql = substr($sql,0,-1);
			$db=Common::getDB($data['uid']);
			$sql = "insert into pay set ".$sql;
			$db->query($sql);
		}
	}
	
    
    /**
     * 是否到平台获取好友
     */
    public static function getPlatformFriends($num)
    {
        $num = intval($num);
        $isget = false;
        $random = rand(1,10);
        
        switch ($num)
        {
            case $num < 10:
                if($random > 0) $isget = true;
                break;
            case $num < 20:
                if($random > 3) $isget = true;
                break;    
            case $num < 30:
                if($random > 5) $isget = true;
                break; 
            case $num < 40:
                if($random > 7) $isget = true;
                break;  
            default:
                if($random > 8) $isget = true;
                break; 
        }
        
        return $isget;
    }
    
    /**
     * 连接redis
     */
    public static function getRedis()
    {
    	static $cache = array();
    	if(empty($cache[$param]))
    	{
    		$config = self::getConfig();
    		$redis = new Redis();
    		$redis->connect($config["redis"]["host"],$config["redis"]["port"]);
    		$cache[$param] = $redis;
    	}
    	return $cache[$param];
    }

    /**
     * 判断玩家是在那个平台
     * pengyou:http://app100624380.qzoneapp.com/
     * qzone:http://app100624380.qzone.qzoneapp.com/
     * weibo:http://app100624380.t.qzoneapp.com
     * yahoo:http://youdong.sunvy.com/
     */
    public static function get_sns()
    {
    	static $sns = array();
    	if(empty($sns[$_SERVER['SERVER_NAME']]))
    	{
    		if($_SERVER['SERVER_NAME'] == "pengyou.app100650795.twsapp.com" || "app100650795.qzoneapp.com" || $_SERVER['SERVER_NAME'] == "nongchangqq.test.redekuai.com")
    		{
    			$info = "pengyou";//朋友
    		}elseif($_SERVER['SERVER_NAME'] == "qzone.app100650795.twsapp.com" || "app100650795.qzone.qzoneapp.com"){
    			$info = "qzone";//QQ空间
    		}elseif($_SERVER['SERVER_NAME'] == "app100696513.t.qzoneapp.com"){
    			$info = "weibo";//微博
    		}
    		else
    		{
    			$info = "pengyou";
    		}
    		$sns[$_SERVER['SERVER_NAME']] = $info;
    	}
    
    	return $sns[$_SERVER['SERVER_NAME']];
    }

    
  
    
    /**
     * 分页
     */
    public function getPageInfo($data)
    {
    	$perPage = $data['perPage'];
    	$pageNo  = $data['pageNo'];
    	$count   = $data['count'];
    	$start   = ($pageNo-1)*$perPage;
    	$end     = $start+$perPage;
    	if($end>$count) $end = $count;
    	$pages =  $count%$perPage?intval($count/$perPage)+1:intval($count/$perPage);
    	return array($start,$end,$pages);
    }
    /**
     * 获取值的区间
     */
    public static function getAchvValue($array,$value)
    {
    	if(is_array($array))
    	{
    		$temp='';
    		foreach($array as $k=>$v)
    		{
    				
    			if($value<$k) break;
    			else
    			{
    				$temp = $v;
    			}	
    		}
    		return $temp;
    	}
    }
    
    /**
     * 计算生命值
     */
    
    public static function getFreeLife($freetime,$begin,$now)
    {
    	$cooltime = $now-$begin;
    	//计算小数点 最大取12位
    	return round($cooltime/$freetime, 16);
    }
    /**
     * 计算玩家免费生命值
     * lives,lifeSlots,freetime,freelives
     */
    public static function getLives($data)
    {
    	$freetime = 1800;
    	$now = $_SERVER['REQUEST_TIME'];
    	$cooltime = $now-$data['freelives'];
    	$newlives = $data['lives']+floor($cooltime/$freetime);
    	if($newlives >= $data['lifeslots'])
    	{
    		$lives = $data['lifeslots'];
    		$freesecs=-1;
    		$coolfreesecs=0;
    	}
    	else 
    	{
    		$lives = $newlives;
    		$freesecs = $freetime-$cooltime%$freetime;
    		$coolfreesecs = $cooltime%$freetime;
    	}
    	return array(0=>$lives,1=>$freesecs,2=>$coolfreesecs);
    }
    
    //计算无限生命值
    public static function getFreeLivestime($freelives)
    {
    	$result = 0;
    	$now = $_SERVER['REQUEST_TIME'];
    	$cooltime = $freelives-$now;
    	if($cooltime<=0)
    	{
    		$result = -$now;
    	}
    	else
    	{
    		$result = $cooltime;
    	}
    	return $result;
    }
    //二维数组根据字段排序
    public static function array_sort($arr,$keys,$type='asc')
    {
    	$keysvalue = $new_array = array();
    	foreach ($arr as $k=>$v)
    	{
    		$keysvalue[$k] = $v[$keys];
    	}
    	if($type == 'asc')
    	{
    		asort($keysvalue);
    	}
    	else
    	{
    		arsort($keysvalue);
    	}
    	reset($keysvalue);
    	foreach ($keysvalue as $k=>$v)
    	{
    		$new_array[] = $arr[$k];
    	}
    	return $new_array;
    }
}

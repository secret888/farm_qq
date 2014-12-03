<?php
class Commons 
{
    public function jsonToArray()
	{
	    $json = stripslashes($_POST['json']);
		if($json)
		{
			$array = json_decode($json,true);
		}
		
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}


	public function voCommon()
	{
		$vo_config = Common::getConfigAdmin("vo_config");
		$db = Common::getDbName();
		$sql = "select * from `vo_common`";
		$data = $db->fetchArray($sql);
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}

	public function addVoCommon()
	{
		$db = Common::getDbName();
		if(!empty($_POST['key']))
		{
			$cache = Common::getCache();
			$key = $_POST['key'];
			$value = $_POST['value'];
			$sql = "select * from `vo_common` where `key`='{$key}'";
			$row = $db->fetchRow($sql);
			if(empty($row['key']))//不存在公告
			{
				$sql = "insert into `vo_common`(`key`,`value`) values ('{$key}','{$value}')";
			}else{
				$sql = "update `vo_common` set `key`='{$key}',`value`='{$value}' where `key`='{$key}'";
			}
			$db->query($sql);

			//删除缓存
			$cache->delete('vo_common_'.$key);
			
			echo "<script>alert('操作成功!');location.href='?mod=commons&act=voCommon';</script>";
		}

		if(!empty($_GET['key']))
		{
			$sql = "select * from `vo_common` where `key`='{$_GET['key']}'";
			$row = $db->fetchRow($sql);
		}

		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}


	public function delVoCommon()
	{
		if(empty($_GET['key'])) exit('Deny Access');

		$db = Common::getDbName();
		$key = $_GET['key'];
		$sql = "delete from `vo_common` where `key`='{$key}'";
		$db->query($sql);

		$cache = Common::getCache();
		$cache->delete('vo_common_'.$key);
		
		echo "<script>location.href='?mod=commons&act=voCommon';</script>";
	}

	public function baseConfig()
	{
		$vo_config = Common::getConfigAdmin("vo_config");
		if($_FILES["file"]["name"])
		{
			$dir = CONFIG_DIR.'/game/';
			$filename = $_FILES["file"]["tmp_name"];
			$destination = $dir.$_FILES["file"]["name"];
			$result = move_uploaded_file($filename, $destination);
			if($result)
			{
				
				$return = 'OK';
				require_once ROOT_DIR.'/crontab/xml_body.php';
				
			}
			else
			{
				$return = "ERROR";
			}
		}
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
		
	}
	
    public function config()
    {
		$vo_config = Common::getConfigAdmin("vo_config");

		if($_GET['config'])
		{
			$config = $_GET['config'];
			$ROOT = dirname(dirname(dirname(__FILE__)));
			$path = $ROOT.'/config/'.$config.'.php';
			if($_POST['config'])
			{
				file_put_contents($path,stripslashes($_POST['config']));
				echo "<script>alert('修改成功~');</script>";
			}
			$data = file_get_contents($path);
		}

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    public function xml()
    {
        $setting = dirname(dirname(dirname(__FILE__))).'/crontab/xml.php';
        
        if($_POST)
        {
            $path = $_POST['phppath'];
            $xml = $_POST['xml'];
            if(is_file($path))
            {
                exec($path." ".$xml);
                
                echo  "<script>alert('缓存生成成功~');history.back(-1);</script>";
            }else{
                echo  "<script>alert('php路径不存在');history.back(-1);</script>";
            }
        }
        
        $phppath = array(
            '/usr/local/php/bin/php',
        );
        
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * 时间转换
     */
    public function timeToDate()
    {
		$time = $_POST['time'];
		if($_POST["type"] == 1)
		{
			$data = date("Y-m-d H:i:s",$time);
		}else{
			$data = strtotime($time);
		}
		
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    public function banner()
    {
    	$id = $_GET['id'] ;
    	$t  = $_GET['t'] ;
    	$db = Common::getDbName();
    	$cache = Common::getCache();
    	$banner = $cache->get("gm_banner");
    	if($t=="edit" && $id>=0)
    	{
    		$udata = $banner[$id];
    		if($_POST['up']=="OK")
    		{
    			if($banner)
    			{
    				$banner[$id] = array(
    								'title'=>$_POST['title'],
    								'desc'=>$_POST['desc'],
    								'src'=>$_POST['src'],
    								'href'=>$_POST['href'],
    								'type'=>$_POST['type'],
    								'target'=>$_POST['target'],
    						);
    				
    				$cache->set('gm_banner',$banner,86400*30);
    			}
    			echo "<script>alert('操作成功!');location.href='?mod=commons&act=banner';</script>";
    		}
    	}
    	if($t=="del" && $id>=0)
    	{
    		
    		if($banner)
    		{
    			unset($banner[$id]);
    			$cache->set('gm_banner',$banner);
    		}
    		echo "<script>alert('操作成功!');location.href='?mod=commons&act=banner';</script>";
    	}
    	if($t=="add" && $_POST['up']=="OK")
    	{
    		$banner[] = array(
    			'title'=>$_POST['title'],
    			'desc'=>$_POST['desc'],
    			'src'=>$_POST['src'],
    			'href'=>$_POST['href'],
    			'type'=>$_POST['type'],
    			'target'=>$_POST['target'],
    			);
    		$cache->set('gm_banner',$banner,86400*30);
    		echo "<script>alert('操作成功!');location.href='?mod=commons&act=banner';</script>";
    	}
    	$data = $banner;
    	include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
}

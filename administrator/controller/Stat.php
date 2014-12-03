<?php
class Stat
{
    


    /**
     * 用户等级统计
     */
    public function level()
    {
        $page = max(1,intval($_GET['page']));
        $perPage = 30;
        $offset = ($page-1)*$perPage;
        $limit = "limit ".$offset.",".$perPage;
    	$key = 'admin_level_page_'.$page;
        $cache = Common::getCache();
        $data = $cache->get($key); 	
        if($data == false)
        {
			$db = Common::getDbName();
    		$sql = "select date,sum(num) num  from stat_level group by date order by date desc $limit";
    		$data = $db->fetchArray($sql);
            $cache->set($key,$data,3600);
        } 
        
        $key1 = 'admin_level_total';
        $total = $cache->get($key1);
        if($total == false)
    	{	
			$db = Common::getDbName();
			$sql = "select count(*) as c from stat_level group by date";
			$result = $db->fetchArray($sql);
			$total = count($result); //总数
			$cache->set($key1,$total,3600);
    	}  
		
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * 每日等级信息
     */
    public function levelInfo()
    {
    	if(empty($_GET['date']) )exit('Deny Access!');
     
	    $date = $_GET['date'];
		$db = Common::getDbName();
		$sql = "select * from stat_level where date='{$date}'";
		$data = $db->fetchArray($sql);
		
		
		
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';	
    }
	 
    /**
     * 金币统计
     */
    public function gold()
    {
        $page = max(1,intval($_GET['page']));
        $perPage = 30;
        $offset = ($page-1)*$perPage;
        $limit = "limit ".$offset.",".$perPage;
    	$key = 'admin_everyday_gold_'.$page;
        $cache = Common::getCache(); 
        $data = $cache->get($key);
        if($data == false)
        {
			$db = Common::getDbName();
    		$sql = "select date,sum(num) num  from stat_gold group by date order by date desc $limit";
    		$data = $db->fetchArray($sql);			
            $cache->set($key,$data,3600);
        }
        
        $key1 = 'admin_everyday_gold_total';
        $total = $cache->get($key1);
        if($total == false)
    	{   
			$db = Common::getDbName();
			$sql = "select count(*) as c from stat_gold group by date";
			$result = $db->fetchArray($sql);
			$total = count($result); //总数
	        $cache->set($key1,$total,3600);
    	}
    	
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * 每日统计信息
     *
     */
    public function goldInfo()
    {
    	if(empty($_GET['date'])) exit('Deny Access!');
		$date = $_GET['date'];

		$db = Common::getDbName(); 
		$sql = "select * from stat_gold where date='{$date}'";
		$data = $db->fetchArray($sql);	

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    

    /**
     * 每日统计（充值、活跃）
     */
    public function everyStat()
    {
        $page = max(1,intval($_GET['page']));
        $perPage = 30;
        $offset = ($page-1)*$perPage;
        $limit = "limit ".$offset.",".$perPage;
		$key = 'admin_everyStat_'.$page;
        $cache = Common::getCache();        
       // $data = $cache->get($key);
        $data = false;
        if($data == false)
        {   
			$db = Common::getDbName();
    		$sql = "select * from every_stat order by id desc $limit";
    		$data = $db->fetchArray($sql);		
			//$cache->set($key,$data,3600);
        }  
        
        $key1 = "admin_everyStat_total";
        //$total = $cache->get($key1);
        $total = false;
        if($total == false)
    	{   
			$db = Common::getDbName();
			$sql = "select count(*) as c from every_stat";
			$result = $db->fetchRow($sql);
			$total  = intval($result['c']); //总数		
			//$cache->set($key1,$total,3600);
		}
		
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    



    /**
     * 每日统计明细
     *
     */
    public function everydaySaleInfo()
    {   
    	if(empty($_GET['date']) )exit('Deny Access!');
		$date = $_GET['date'];

		$db = Common::getDbName();
		$sql = "select * from stat_sale where date='{$date}' order by `type` asc, `item_id` desc";
		$data = $db->fetchArray($sql);

		$type = array(1=>"黄金", 2=>"木头" , 3=>"石油" , 4=>"钢筋" , 5=>"钞票",);
		$vo_items = Common::getGameConfig("items");
        
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * 查找当前等级下的用户信息
     */
    public function levelForUid()
    {
        $level = $_GET['level'] ? $_GET['level'] : 1;
      
        $key = "admin_level_user_".$level;
        $cache = Common::getCache();
        $info = $cache->get($key);
        
        if($info == false)
        {
            $Sharding = Common::getConfig('Sharding');
			$info = array();
			foreach($Sharding as $dbName)
			{
				$db = Common::getDbName($dbName);
				for ($i = 0 ; $i < $this->config['param']['table_div'] ; $i++)
				{
					$table = "user_".Common::computeTableId($i);
					$sql = "select * from `$table` where `completedlevel`='{$level}' limit 100";
					$data = $db->fetchArray($sql);
					$info = array_merge($info,$data);
				}				
			}
			$cache->set($key,$info,3600);
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * 获取某日某个item被购买的用户信息
     */
    public function getUserForItem()
    {
    	$date = $_GET['date'];
    	$item_id = $_GET["item_id"];
    	$start_time = strtotime($date);
    	$end_time = $start_time+24*3600;
    	
    	$key = "admin_user_item_".$item_id;
    	$cache = Common::getCache();
    	$info = $cache->get($key);
    	
    	if($info == false)
    	{
    		$Sharding = Common::getConfig('Sharding');
    		$config = Common::getConfig();
    		$info = array();
    		foreach($Sharding as $dbName)
    		{
    			$db = Common::getDbName($dbName);
    			for ($i = 0 ; $i < $config['param']['table_div'] ; $i++)
    			{
	    			$table = "log_".Common::computeTableId($i);
	    			$sql = "select uid,item_id,`type`,sum(num) num,count(uid) `count` from `$table` where `time`>{$start_time} and `time`<{$end_time} and `item_id`='{$item_id}' group by uid order by `count` desc limit 10";
	    			$data = $db->fetchArray($sql);
	    			if(!is_array($data)) $data = array();
	    			$info = array_merge($info,$data);
    			}
    		}
    			$cache->set($key,$info,3600);
    	}
    	$type = array(1=>"黄金", 2=>"木头" , 3=>"石油" , 4=>"钢筋" , 5=>"钞票",);
    	$vo_items = Common::getGameConfig("items");
    	
    	include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

}

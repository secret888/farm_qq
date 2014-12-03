<?php
class Consume
{    
    /**
     * 充值查询
     */
    public function paysearch()
    {
        if(isset($_POST['uid']))
        {
            $uid = intval($_POST['uid']);
    		$db = Common::getDbName();
            $config = Common::getConfig();
            $sql = "select * from pay where `status`=1 and uid='{$uid}'"; 
            $data = $db->fetchArray($sql);
            $newdata = array();
            foreach ($data as $k=>$v)
            {
            	$newdata[$k]['uid'] = $v['uid'];
            	$newdata[$k]['item_id'] = $v['item_id'];
            	$newdata[$k]['amt'] = $v['amt'];
            	$newdata[$k]['price'] = $v['price'];
            	$newdata[$k]['num'] = $v['num'];
            	$newdata[$k]['ts'] = $v['ts'];
            	$newdata[$k]['item'] = $this->getItemInfo($v['item_id']);
            }
            $data = $newdata;
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * 消费查询
     */
    public function salesearch()
    {
    	$page = max(1,intval($_GET['page']));
    	$perPage = 30;
    	$offset = ($page-1)*$perPage;
    	$limit = "limit ".$offset.",".$perPage;
    		
    	if($_GET['uid']){
    		$uid = $_GET['uid'];
    		$db = Common::getDbName();
    		$sql = "select uid,item_id,sum(amt) as num, count(*) as ct from pay where uid=$uid group by item_id order by num desc $limit";
    		$data = $db->fetchArray($sql);
    
    		if(!empty($data))
    		{
    			$newdata = array();
    			foreach ($data as $k=>$v)
    			{
    				$newdata[$k]['uid'] = $v['uid'];
    				$newdata[$k]['num'] = $v['num'];
    				$newdata[$k]['ct'] = $v['ct'];
    				$newdata[$k]['item'] = $this->getItemInfo($v['item_id']);
    			}
    			$data = $newdata;
    		}
    	}
    	include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * 充值记录
     */
    public function pay()
    {
    	$cache = Common::getCache();
    	$sandbox_ustrs = $cache->get('sandbox_ustrs');
    	$where = '';
    	if(!empty($sandbox_ustrs))
    	{
    		$uidlist = implode(',', $sandbox_ustrs);
    		foreach($sandbox_ustrs as $ustr)
    		{
    			$where .= "and openid<>'$ustr' ";
    		}
    	}
        $page = max(1,intval($_GET['page']));
        $perPage = 30;
        $offset = ($page-1)*$perPage;
        $limit = "limit ".$offset.",".$perPage;
    	$key = "admin_player_pay_list_".$page;
        $cache = Common::getCache();
        $data = $cache->get($key);
        if($data === false)
        {
    		$db = Common::getDbName();
            $sql = "select uid,sum(amt) as money,sum(price*num) as gamemoney,count(*) as count from pay where status=1 $where group by uid order by gamemoney desc $limit";
            $data = $db->fetchArray($sql);
            //$cache->set($key, $data, 3600);
        }
        
    	$key1 = "admin_player_pay_list_total";
		$result = $cache->get($key1);
		if($result == false)
		{	
			$db = Common::getDbName();
			$sql = "select sum(amt) as money,sum(price*num) as gamemoney,count(*) as count from pay where status=1 $where";
			$result = $db->fetchRow($sql); 
			
			$sql = "select uid from pay where status=1 $where group by uid";
			$data1 = $db->fetchArray($sql);
			$result['total'] = count($data1);
  
			//$cache->set($key1,$result,3600);
		}
		$total = $result['total'];

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * 每日(月)充值
     */
    public function everydayPay()
    {
        $page = max(1,intval($_GET['page']));
        $perPage = 30;
        $offset = ($page-1)*$perPage;
        $limit = "limit ".$offset.",".$perPage;
    	$key = 'admin_every_day_pay_'.$page;
    	$total_key = 'admin_ever_day_total';
        $cache = Common::getCache();
        $info = $cache->get($key);
        $sandbox_ustrs = $cache->get('sandbox_ustrs');
        $info = false;
        if($info === false)
        {
        	$where = '';
        	if(!empty($sandbox_ustrs))
        	{
        		$uidlist = implode(',', $sandbox_ustrs);
        		foreach($sandbox_ustrs as $ustr)
        		{
        			$where .= "and openid<>'$ustr' ";
        		}
        	}
			$db = Common::getDbName();
	        $sql = "select count(*) as c from pay where status=1 $where group by date_format(from_unixtime(ts),'%Y-%m-%d')";
	        $total = $db->fetchArray($sql);
	        $total = count($total); //总数
	        //$cache->set($total_key, $total);
	        
            //每日充值
            $sql = "select sum(amt) money ,sum(price*num) gamemoney,date_format(from_unixtime(ts),'%Y-%m-%d') time from pay where status=1 $where group by date_format(from_unixtime(ts),'%Y-%m-%d') order by ts desc $limit";
            $data_day = $db->fetchArray($sql);
            
            //每月充值
            $cache = Common::getCache();
            $data_month = $cache->get('admin_month_pay');
            $data_month = false;
            if( $data_month === false)
            {
                $sql = "select sum(amt) money ,sum(price*num) gamemoney,date_format(from_unixtime(ts),'%Y-%m') time from pay where status=1 group by date_format(from_unixtime(ts),'%Y-%m') order by ts desc";
                $data_month = $db->fetchArray($sql);
                //$cache->set('admin_month_pay',$data_month,3600*12);
            }
            
            $info = array(
                'day' => $data_day,
                'month' => $data_month,
            	"time"  => time(),
            );
            $cache->set($key,$info,300);
        }
        $total = $total>0?$total:$cache->get($total_key);

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * 当日充值详情
     */
    public function everydayPayInfo()
    {
        if(empty($_GET['time'])) exit('Deny Access!');
            $time = $_GET['time'];
    		$db = Common::getDbName();
            $sql = "select uid,sum(amt) money ,sum(price*num) gamemoney,max(ts)as time from pay where status=1 and date_format(from_unixtime(ts),'%Y-%m-%d')='{$time}' group by uid order by gamemoney desc";
            $data_day_info = $db->fetchArray($sql);          
      
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * 每日销售分布
     */
    
    public function everydayPaydetails()
    {
    	if(empty($_GET['time'])) exit('Deny Access!');
    	$time = $_GET['time'];
    	$db = Common::getDbName();
    	$sql = "select item_id,sum(amt) as money, count(*) as ct from pay where status=1 and date_format(from_unixtime(ts),'%Y-%m-%d')='{$time}' group by item_id order by money desc";
    	$data_day_info = $db->fetchArray($sql);
    	$newdata = array();
    	foreach ($data_day_info as $k=>$v)
    	{
    		$newdata[$k]['item_id'] = $v['item_id'];
    		$newdata[$k]['money'] = $v['money'];
    		$newdata[$k]['ct'] = $v['ct'];
    		$newdata[$k]['item'] = $this->getItemInfo($v['item_id']);
    	}
    	$data_day_info = $newdata;
    	
    	include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /**
     * 充值分布
     */
    public function details()
    {
    	$cache = Common::getCache();
    	$sandbox_ustrs = $cache->get('sandbox_ustrs');
    	$where = '';
    	if(!empty($sandbox_ustrs))
    	{
    		$uidlist = implode(',', $sandbox_ustrs);
    		foreach($sandbox_ustrs as $ustr)
    		{
    			$where .= "and openid<>'$ustr' ";
    		}
    	}
       
    	$db = Common::getDbName();
    	$sql = "select item_id,sum(amt) as num,count(*) as ct from pay where 1=1 $where group by item_id order by num desc";
    	$data = $db->fetchArray($sql);
    	$newdata = array();
    	foreach ($data as $k=>$v)
    	{
    		$newdata[$k]['item_id'] = $v['item_id'];
    		$newdata[$k]['num'] = $v['num'];
    		$newdata[$k]['ct'] = $v['ct'];
    		$newdata[$k]['item'] = $this->getItemInfo($v['item_id']);
    	}
    	$data = $newdata;
        
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * Q点直购销售统计
     *
     */
    public function sale()
    {
    	
    	$db = Common::getDbName();
    	
    	$newdata = array();
    	
    	$config = Common::getConfig();
    	$locale = Common::getGameConfig('locale');
    	
    	for ($i = 0 ; $i < $config["param"]['table_div'] ; $i++)
    	{
    		$sql = "select count(*) as num ,itemname,price,ptype from buy_log_".Common::computeTableId($i)." group by itemname";
    		$data = $db->fetchArray($sql);
    		foreach ($data as $k=>$v)
    		{
    			$itemname = $v['itemname'];
    			$newdata[$itemname]['num'] += $v['num'];
    			$newdata[$itemname]['price'] = $v['price'];
    			$newdata[$itemname]['ptype'] = $v['ptype'];
    		}
    	}
    	$data = array();
    	foreach ($newdata as $itemname=>$value)
    	{
    		$data[$itemname]['num'] = $value['num'];
    		$data[$itemname]['amt'] = $value['num']*$value['price'];
    		$data[$itemname]['ptype'] = $value['num']==1?'金币':'坚果';

    		$itemname_en = 'booster_'.$itemname;
    		$itemname_zh = $locale[$itemname_en];
    		if(empty($itemname_zh))
    		{
    			$itemname_zh = $locale[$itemname];
    		} 
    		if(empty($itemname_zh))
    		{
    			$itemname_en = 'helper_'.$itemname;
    			$itemname_zh = $locale[$itemname_en];
    		}
    		$data[$itemname]['item'] = $itemname_zh;
    	}
    	include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    
    
    
    
    
 /**
     * 获取充值购买物品名称
     * @param unknown_type $item_id
     * @return string
     */
    public function getItemInfo($item_id)
    {
    	$iteminfo = explode('_', $item_id);
    	$paytype  = $iteminfo[0];
    	$itemtype = $iteminfo[1];
    	switch ($paytype)
		{
			case 1:
				{
					if($itemtype=='coins')
					{
						$name  = '红辣椒X1000';
					}
					if($itemtype=='cash')
					{
						$name   = '复活';	
					}
					break;
				}
			case 2:
				{
					$titlearr = array(1=>'一周无限生命',2=>'24小时无限生命',3=>'生命+1',4=>'买满生命');
					$name   = $titlearr[$itemtype];
					break;
				}
			case 3:
				{
					$voitem = Common::getGameConfig('items');				
					$name  = $voitem[$iteminfo[2]]['title'];
					
				}
			default:
				break;	
		}
		return $name;
    }
    
}

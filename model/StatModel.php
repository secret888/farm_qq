<?php
class StatModel 
{
    /**
     * 删除 user_item_ 和 user_map_ 字段delete为1的记录
     */
	public function delete()
	{
		$Sharding = Common::getConfig(Sharding);
		$config = Common::getConfig();
		foreach($Sharding as $dbName)
		{
			$db = Common::getDbName($dbName);
			$sqls = array();
			for($i = 0 ; $i < $config['param']['table_div'] ; $i++)
			{
				$table = "item_".Common::computeTableId($i);
				$sqls[]= sprintf("delete from `$table` where `delete`= '1' limit 20000 ");
			}

			foreach($sqls as $sql)
			{
				$db->query($sql);			
			}
		}
	}

    /**
     * 获取 日活跃,月活跃,新注册用户,充值信息
     */
    public function everyStat()
    {
        $config = Common::getConfig();
        $time1 = strtotime(date('y-m-d',time()-3600*24)); //昨天0点的时间戳
        $time2 = strtotime(date('y-m-d'));//今天0点的时间戳
        $time3 = strtotime(date('y-m-d',strtotime("-1 month"))); //上个月的今天0点的时间戳

        $new_user = 0; //新注册用户
        $dau = 0 ; //日活跃用户
        $mau = 0 ; // 月活跃用户

    	$Sharding = Common::getConfig(Sharding);
		foreach($Sharding as $dbName)
		{
        	$db = Common::getDbName($dbName);
            for($i=0 ; $i < $config['param']['table_div'] ; $i++)
	        {
	        	//获取新注册用户数
	        	$table =  "user_".str_pad($i, $config['param']['table_bit'] , '0', STR_PAD_LEFT );
	        	$sql = "select count(*) as c from `$table` where `registration_time`>='$time1' and `registration_time`<$time2";
	        	$row = $db->fetchRow($sql);
	        	$new_user += intval($row['c']);

	        	//日活跃用户
	        	$sql = "select count(*) as c from `$table` where `last_logged_in`>='$time1'";
	        	$row = $db->fetchRow($sql);
	        	$dau += intval($row['c']);
	
	        	//月活跃用户
	        	$sql = "select count(*) as c from `$table` where `last_logged_in`>='$time3'";
	        	$row = $db->fetchRow($sql);
	        	$mau += intval($row['c']);
	        }
		}
        
    	$db = Common::getDbName();
        //获取当天的充值情况
        $table = 'pay';
        $sql = "select sum(amt) as money,sum(amt) as gamemoney from `$table` where `status`=1 and `ts`>='$time1' and `ts`<$time2";
        $row = $db->fetchRow($sql);
        $total_money = intval($row['money']); //当天充值的金额
        $total_gamemoney = intval($row['gamemoney']);//当天充值的金币数

        //最近一个月的充值情况
        $table = 'pay';
        $sql = "select sum(amt) as money,sum(amt) as gamemoney from `$table` where `status`=1 and `ts`>='$time3' and `ts`<$time2";
        $row = $db->fetchRow($sql);
        $month_total_money = intval($row['money']);//最近一月现金
        $month_total_gamemoney = intval($row['gamemoney']);//最近一月金币
 
        //当天充值的用户
        $table = 'pay';
        $sql = "select count(distinct uid) as c from `$table` where `status`=1 and `ts`>='$time1' and `ts`<$time2";
        $row = $db->fetchRow($sql);
        $pay_user = intval($row['c']);//今天充值用户数

        //插入新的记录
        $sql = "select id from every_stat where ctime='$time1'";
        $row = $db->fetchRow($sql);
        if(empty($row))
        {
            $sql = "insert into every_stat (new_user,dau,mau,money,gamemoney,month_money,month_gamemoney,pay_user,ctime) values ($new_user,$dau,$mau,$total_money,$total_gamemoney,$month_total_money,$month_total_gamemoney,$pay_user,$time1)";
        }else{
            $sql = "update every_stat set new_user=$new_user,dau=$dau,mau=$mau,money=$total_money,gamemoney=$total_gamemoney,month_money=$month_total_money,month_gamemoney=$month_total_gamemoney,pay_user=$pay_user where ctime='$time1'";
        }
        $db->query($sql);
    }
  
    
	/**
     * 销售统计
     */
    public function stat_sale()
    {
    	
		$config = Common::getConfig();
		$info = array();
		$time1 = strtotime(date('Y-m-d'))-3600*24;
		$time2 = strtotime(date('Y-m-d'));
    	$Sharding = Common::getConfig(Sharding);
		foreach($Sharding as $dbName)
		{
			$db = Common::getDbName($dbName);
			for ($i = 0 ; $i < $config['param']['table_div'] ; $i++)
			{
			    $table = "buy_log_".Common::computeTableId($i);
			    $sql = "select `itemname`,sum(num) as num , count(itemname) as `count`,`ptype`,`price` from {$table} where `time`>{$time1} and `time`<{$time2} group by itemname"; 
			    $data = $db->fetchArray($sql);
			    if(empty($data)) continue;
			    foreach ($data as $v)
			    {   
			    	if($info[$v['item_name']])
			    	{
			    		$info[$v['item_name']]["num"] += $v["num"]*$v['price'];
			    		$info[$v['item_name']]["count"] += $v["count"];
			    	}else{
			    		$info[$v['item_name']] = $v;
			    	}
			    }
			}
		}	
		
		$db = Common::getDbName();
		$date = date("Y-m-d",time()-24*3600);
		foreach ($info as $key=>$value)
		{
			$sql = "select * from stat_sale where `date`='$date' and `item_name`='{$key}'";
			$row = $db->fetchRow($sql);
			if($row)
			{
				$sql = "update stat_sale set `num`='".$value["num"]."' , `count`='".$value["count"]."' , `type`='".$value["ptype"]."'  where `date`='$date' and `item_name`='{$key}'";
			}else{
				$sql = "insert into stat_sale (`date`,`item_name`,`num`,`count`,`type`) values('$date','$key','".$value["num"]."','".$value["count"]."','".$value["ptype"]."')";
			}
			$db->query($sql);
		}	
		
    }    
    /**
     * 每天等级统计
     */
    public function stat_level()
    {
        $config = Common::getConfig();
    	$Sharding = Common::getConfig('Sharding');
		$info = array();
		foreach($Sharding as $dbName)
		{
    	   $db = Common::getDbName($dbName);
            for ($i = 0 ; $i < $config['param']['table_div'] ; $i++)
            {
                $table = "user_".Common::computeTableId($i);

                /**
                 * 用户等级分布情况
                 */
                $sql = "select maxLevel,count(maxLevel) as sum from $table group by maxLevel";
                $data = $db->fetchArray($sql);
                if(!empty($data) && is_array($data))
                {
                    foreach ($data as $row)
                    {
                        $info[$row['maxLevel']] += $row['sum'];
                    }
                }
            }
		}
    	ksort($info);
    	
        $db = Common::getDbName();
    	$table = "stat_level";
    	$date = date("Y-m-d",time()-24*3600);
    	foreach ($info as $key=>$value)
		{
			$sql = "select * from $table where `date`='{$date}' and `level`='{$key}'";
			$row = $db->fetchRow($sql);
			if($row)
			{
				$sql = "update `$table` set `num`='".$value."' where `date`='{$date}' and `level`='{$key}'";
			}else{
				$sql = "insert into `$table`(`date`,`num`,`level`) values('{$date}','{$value}','".$key."')";
			}
			$db->query($sql);
		}
    }
    
    /**
     * 留存率
     * gm_keep:id time day2 day3 day7 day30
     */
    public function keep()
    {
    	$time = strtotime(date("Y-m-d")) - 3600*24;//几号的留存
    	$day2 = $time - 3600*24;//次日留存时间
    	$day3 = $time - 3600*24*2;//三日留存时间
    	$day6 = $time - 3600*24*5;//七日留存前一天
    	$day7 = $time - 3600*24*6;//七日留存时间
    	$day29 = $time - 3600*24*28;//月留存时间的前一天
    	$day30 = $time - 3600*24*29;//月留存时间
    	
    	$Sharding = Common::getConfig('Sharding');
    	$config = Common::getConfig();
    	$mday2 = $mday3 = $mday7 = $mday30 = 0;
    	$mday2_total = $mday3_total = $mday7_total = $mday30_total = 1;
		foreach($Sharding as $dbName)
		{
			$db = Common::getDbName($dbName);
			for ($i = 0 ; $i < $config['param']['table_div'] ; $i++)
			{
			    $table = "user_".Common::computeTableId($i);
			    
			    //次日留存
			    $sql = "select count(*) count from `{$table}` where `registration_time` >= '{$day2}' and `registration_time` <= '{$time}'";
			    $row = $db->fetchRow($sql);
			    $mday2_total += $row["count"];
			    
			    $sql = "select count(*) count from `{$table}` where `registration_time` >= '{$day2}' and `registration_time` <= '{$time}' ";
			    $row = $db->fetchRow($sql);
			    $mday2 += $row["count"];
			    
			    //三日留存
			    $sql = "select count(*) count from `{$table}` where `registration_time` >= '{$day3}' and `registration_time` <= '{$day2}'";
			    $row = $db->fetchRow($sql);
			    $mday3_total += $row["count"];
			    
			    $sql = "select count(*) count from `{$table}` where `registration_time` >= '{$day3}' and `registration_time` <= '{$day2}' ";
			    $row = $db->fetchRow($sql);
			    $mday3 += $row["count"];
			    
			    //七日留存
			    $sql = "select count(*) count from `{$table}` where `registration_time` >= '{$day7}' and `registration_time` <= '{$day6}'";
			    $row = $db->fetchRow($sql);
			    $mday7_total += $row["count"];
			    
			    $sql = "select count(*) count from `{$table}` where `registration_time` >= '{$day7}' and `registration_time` <= '{$day6}' ";
			    $row = $db->fetchRow($sql);
			    $mday7 += $row["count"];
			    
			    //月留存
			    $sql = "select count(*) count from `{$table}` where `registration_time` >= '{$day30}' and `registration_time` <= '{$day29}'";
			    $row = $db->fetchRow($sql);
			    $mday30_total += $row["count"];
			    
			    $sql = "select count(*) count from `{$table}` where `registration_time` >= '{$day30}' and `registration_time` <= '{$day29}' ";
			    $row = $db->fetchRow($sql);
			    $mday30 += $row["count"];
			}
		}
    	
    	$mday2 = intval($mday2*10000/$mday2_total);
    	$mday3 = intval($mday3*10000/$mday3_total);
    	$mday7 = intval($mday7*10000/$mday7_total);
    	$mday30 = intval($mday30*10000/$mday30_total);
        $db = Common::getDbName();
    	$table = "gm_keep";
		$sql = "select * from `{$table}` where `time`='{$time}'";
		$row = $db->fetchRow($sql);
		if($row)
		{
			$sql = "update `$table` set `day2`='{$mday2}',`day3`='{$mday3}',`day7`='{$mday7}',`day30`='{$mday30}' where `time`='{$time}'";
		}else{
			$sql = "insert into `$table`(`time`,`day2`,`day3`,`day7`,`day30`) values('{$time}','{$mday2}','{$mday3}','{$mday7}','{$mday30}')";
		}
		$db->query($sql);
    }
}

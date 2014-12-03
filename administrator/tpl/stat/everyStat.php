<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_stat.php');?>
<h2>每日统计</h2>
<table>
        <tr>
          <th nowrap="nowrap">日期(Date)</th>
          <th nowrap="nowrap">DAU</th>
          <th nowrap="nowrap">MAU</th>
          <th nowrap="nowrap">新增用户</th>
          <th nowrap="nowrap">今日充值钱币</th>
          <th nowrap="nowrap">最近一月金额</th>
          <th nowrap="nowrap">ARPU(日金额/DAU)</th>
          <th nowrap="nowrap">充值用户/DAU</th>
          <th nowrap="nowrap">DAU/MAU</th>
		  <th nowrap="nowrap">付费率</th>
		  <th nowrap="nowrap">ARPU</th>
        </tr>
        <?php
        $weekArray=array("星期天","星期一","星期二","星期三","星期四","星期五","星期六");
        $rate = array(
            'QD' => array('percent'=>'0.4','rate'=>'0.01'),
            'facebook_tg' => array('percent'=>'0.2','rate'=>'0.2187'),
            'naver' => array('percent'=>'0.318','rate'=>'0.0059'),
            'VZ' => array('percent'=>'8.7','rate'=>'0.1666667'),
        	'NK' => array('percent'=>'0.004','rate'=>'8'),
        	"baidu" => array('percent'=>'0.48','rate'=>'1'),
        	"facebook_tk" => array('percent'=>'0.4','rate'=>'4.5'),
        	"4399" => array('percent'=>'0.6','rate'=>'1'),
        	"nate" => array('percent'=>'0.33','rate'=>'0.59101'),
        );
        $rate = $rate[SNS];

        foreach($data as $k=>$v)
        {
        	if($v['dau'])$rmb_mau = number_format($v['money']/$v['dau'],4);
        	if($v['dau'])$coin_mau = number_format($v['gamemoney']/$v['dau'],4);
        	if($v['mau'])$dau_mau = number_format($v['dau']/$v['mau'],4);
        	$max_dau = max($v['dau'],1);
            $pdistu = sprintf('%0.2f', $v['pay_user']/$max_dau*1000);
			$fufeili = sprintf('%0.3f', $v['pay_user']/$max_dau*100);

            if(is_array($rate))
            {
                $income = intval($v['money']*$rate['rate']);
            }
            if(!empty($v['pay_user']))
            {
                $arpu = ceil($income/$v['pay_user']);
            }
            $weekIndex = date("w",$v['ctime']);
        ?>
        <tr>
            <td nowrap="nowrap"><?php echo date('Y-m-d',$v['ctime']);?>(<?php echo $weekArray[$weekIndex];?>)</td>
            <td nowrap="nowrap"><?php echo $v['dau']?></td>
            <td nowrap="nowrap"><?php echo $v['mau']?></td>
            <td nowrap="nowrap"><?php echo $v['new_user']?></td>
            <td nowrap="nowrap"><?php echo $v['money']?></td>
            <td nowrap="nowrap"><?php echo $v['gamemoney']?></td>            
            <td nowrap="nowrap"><?php echo $v['month_money']?></td>
            <td nowrap="nowrap"><?php echo $v['month_gamemoney']?></td>
            <td nowrap="nowrap"><?php echo $rmb_mau?></td>
            <td nowrap="nowrap"><?php echo $coin_mau?></td>
            <td nowrap="nowrap"><?php echo $pdistu?>‰</td>
            <td nowrap="nowrap"><?php echo $dau_mau?></td>
			<td nowrap="nowrap"><?php echo $fufeili?>%</td>
			<td nowrap="nowrap"><?php echo $arpu?></td>
        </tr>
        <?php }?>
</table>
<div class="page">
<?php
for( $i = 1 ; $num = ceil($total/$perPage) , $i <= $num  ; $i++)
{
    echo  ($i!=$page)?"<a href='?mod=stat&act=everyStat&page=".$i."'>$i</a>":"<b>$i</b>";
}
?>
</div>
<?php include(TPL_DIR.'footer.php');?>
<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_pay.php');?>
<h2>每月充值详情(<?php echo $data_month;?>)</h2>
<table>
<tr><th>月份(Month)</th><th>金额(Cash)</th><th>金币(Coin)</th><th>实际收入</th></tr>
<?php
$weekArray=array("星期天","星期一","星期二","星期三","星期四","星期五","星期六");
$rate = array(
    'QD' => array('percent'=>'0.4','rate'=>'0.01'),
    'facebook_tg' => array('percent'=>'0.2','rate'=>'0.2187'),
    'naver' => array('percent'=>'0.318','rate'=>'0.0059'),
    'VZ' => array('percent'=>'8.7','rate'=>'0.1666667'),
	'NK' => array('percent'=>'0.004','rate'=>'8'),
	"baidu" => array('percent'=>'0.6','rate'=>'1'),
	"facebook_tk" => array('percent'=>'0.4','rate'=>'4.5'),
	"4399" => array('percent'=>'0.6','rate'=>'1'),
	"nate" => array('percent'=>'0.33','rate'=>'0.59101'),
);
$rate = $rate[SNS];

$total_money = 0;
$total_gamemoney = 0;
foreach ($info['month'] as $value)
{
    $total_money += $value['money']*0.1;
    $total_gamemoney += $value['gamemoney'];
    $income = '?';
    if(is_array($rate))
    {
        $income = intval($value['money']*0.1*$rate['percent']*$rate['rate']);
        
    }
    $money = $value['money']*0.1;
    echo "<tr><td>{$value['time']}</td><td>".$money."</td><td>{$value['gamemoney']}</td><td>{$income}</td></tr>";
}
?>
</table>
<div>总金额数(Cash total):<?php echo $total_money;?></div>
<div>总金币数(Coin total):<?php echo $total_gamemoney;?></div>
<div>实际总收入:<?php echo intval($total_money*$rate['percent']*$rate['rate']);;?></div>
<br/><br/>
<h2>每日充值详情(admin_every_day_pay_$page)</h2>
<table>
<tr><th>日期(Date)</th><th>金额(Cash)</th><th>金币(Coin)</th><th>实际收入(RMB)</th></tr>
<?php
foreach ($info['day'] as $value)
{
    $income = '?';
    if(is_array($rate))
    {
        $income = intval($value['money']*0.1*$rate['percent']*$rate['rate']);
    }
    $money = $value['money']*0.1;
    $weekIndex = date("w",strtotime($value['time']));
?>
    <tr>
	    <td>
	    	<a href='?mod=consume&act=everydayPayInfo&time=<?php echo $value['time'];?>'><?php echo $value['time'].'('.$weekArray[$weekIndex].')'?></a>
	    </td>
	    <td>
	    	<?php echo $money;?>
	    </td>
	    <td>
	    <a href='?mod=consume&act=everydayPaydetails&time=<?php echo $value['time'];?>'>
	    <?php echo $value['gamemoney'];?>
	    </a>
	    
	    	
	    </td>
	    <td>
	    	<?php echo $income;?>
	    </td>
	</tr>
<?php 
}
?>
</table>
<div class="page">
<?php
$perPage = empty($perPage) ? 15 : $perPage;
for( $i = 1 ; $num = ceil($total/$perPage) , $i <= $num  ; $i++)
{
    echo  ($i!=$page)?"<a href='?mod=consume&act=everydayPay&page=".$i."'>$i</a>":"<b>$i</b>";
}
?>
</div>
<?php include(TPL_DIR.'footer.php');?>

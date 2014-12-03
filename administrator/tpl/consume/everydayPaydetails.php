<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_pay.php');?>
<h2><?php echo $_GET['time'];?>充值详情(Pay detail)</h2>
<table>
<tr><th>物品(item_id)</th><th>金额(Cash)</th><th>购买次数</th></tr>
<?php
$total_money = 0;
$total_gamemoney = 0;
foreach ($data_day_info as $value)
{
    $total_money += $value['money']*0.1;
	$money = $value['money']*0.1;
    echo "<tr><td>{$value['item']}({$value['item_id']})</td><td>{$money}</td><td>{$value['ct']}</td></tr>";
}
?>
</table>
<div>当日总金额数(Day cash total):<?php echo $total_money;?></div>
<?php include(TPL_DIR.'footer.php');?>
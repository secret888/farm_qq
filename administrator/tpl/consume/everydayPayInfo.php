<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_pay.php');?>
<h2><?php echo $_GET['time'];?>充值详情(Pay detail)</h2>
<table>
<tr><th>UID</th><th>金额(Cash)</th><th>金币(Coin)</th><th>时间</th></tr>
<?php
$total_money = 0;
$total_gamemoney = 0;
foreach ($data_day_info as $value)
{
    $total_money += $value['money']*0.1;
    $total_gamemoney += $value['gamemoney'];
	if($value['time']){
		$value['time']=date("Y-m-d H:i:s",$value['time']);
	}
	$money = $value['money']*0.1;
    echo "<tr><td>{$value['uid']}</td><td>{$money}</td><td>{$value['gamemoney']}</td><td>{$value['time']}</td></tr>";
}
?>
</table>
<div>当日总金额数(Day cash total):<?php echo $total_money;?></div>
<div>当日总金币数(Day coin total):<?php echo $total_gamemoney;?></div>
<?php include(TPL_DIR.'footer.php');?>
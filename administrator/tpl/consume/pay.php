<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_pay.php');?>
<h2>充值记录(<?php echo $key;?> <?php echo $key1;?>)</h2>
<div>总金额数(Cash total):<?php echo $result['money'];?></div>
<div>总金币数(Coin total):<?php echo $result['gamemoney'];?></div>
<div>充值总次数(Pay times):<?php echo $result['count'];?></div>
<table>
<tr><th>uid</th><th>金额(Cash)</th><th>金币(Coin)</th><th>次数(Times)</th></tr>
<?php
foreach ($data as $value)
{
	$money = $value['money']*0.1;
    echo "<tr><td>{$value['uid']}</td><td>{$money}</td><td>{$value['gamemoney']}</td><td>{$value['count']}</td></tr>";
}
?>
</table>
<div class="page">
<?php
for( $i = 1 ; $num = ceil($total/$perPage) , $i <= $num  ; $i++)
{
    echo  ($i!=$page)?"<a href='?mod=consume&act=pay&page=".$i."'>$i</a>":"<b>$i</b>";
}
?>
</div>
<?php include(TPL_DIR.'footer.php');?>
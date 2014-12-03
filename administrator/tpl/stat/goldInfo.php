<?php include(TPL_DIR.'header.php');?>
<h2>钻石统计</h2>
<table>
<tr><th>日期</th><th>钻石范围</th><th>玩家数量</th></tr>
<?php
$total_num = 0;
foreach ($data as $key=>$value)
{
    $total_num += $value['num'];
?>
<tr><td><?php echo $value['date'];?></td><td><?php echo $value['gold'];?></td><td><?php echo $value['num'];?></td></tr>
<?php }?>
</table>
<p>玩家总数(player total): <?php echo $total_num;?></p>
<?php include(TPL_DIR.'footer.php');?>

<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_stat.php');?>
<h2>等级统计</h2>
<table>
<tr><th>等级</th><th>数量</th></tr>
<?php
$total_num = 0;
foreach ($data as $key=>$value)
{
    $total_num += $value['num'];
?>
<tr><td><a href="?mod=stat&act=levelForUid&level=<?php echo $value['level'];?>"><?php echo $value['level'];?></a></td><td><?php echo $value['num'];?></td></tr>
<?php }?>
</table>
<p>玩家总数(player total): <?php echo $total_num;?></p>
<?php include(TPL_DIR.'footer.php');?>

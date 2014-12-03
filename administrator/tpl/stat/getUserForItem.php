<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_sale.php');?>
<h2>每日玩家消费统计(<?php echo $key;?>)</h2>
<table>
<tr><th>日期</th><th>uid</th><th>item_id</th><th>费用</th><th>count</th><th>type</th></tr>
<?php
$total_cash = 0;
foreach ($info as $key=>$value)
{
    $total_cash += $value['num'];
?>
<tr>
	<td><?php echo $date;?></td>
	<td><?php echo $value['uid'];?></td>
	<td><?php echo $item_id;?>(<?php echo $vo_items[$item_id]["name"];?>)</td>
	<td><?php echo $value['num'];?></td>
	<td><?php echo $value['count'];?></td>
	<td><?php echo $type[$value['type']];?></td>
</tr>
<?php }?>
</table>
<p>每日金币总数(Cash total): <?php echo $total_cash;?></p>
<?php include(TPL_DIR.'footer.php');?>

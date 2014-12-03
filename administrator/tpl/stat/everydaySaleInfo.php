<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_sale.php');?>
<h2>每日销售统计</h2>
<table>
<tr><th>日期</th><th>item_id</th><th>费用</th><th>count</th><th>type</th></tr>
<?php
$total_cash = 0;
foreach ($data as $key=>$value)
{
    $total_cash += $value['num'];
?>
<tr>
	<td><?php echo $value['date'];?></td>
	<td><?php echo $value['item_id'];?>(<?php echo $vo_items[$value['item_id']]["name"];?>)</td>
	<td><?php echo $value['num'];?></td>
	<td><a href="?mod=stat&act=getUserForItem&date=<?php echo $value['date'];?>&item_id=<?php echo $value['item_id'];?>"><?php echo $value['count'];?></a></td>
	<td><?php echo $type[$value['type']];?></td>
</tr>
<?php }?>
</table>
<p>每日金币总数(Cash total): <?php echo $total_cash;?></p>
<?php include(TPL_DIR.'footer.php');?>

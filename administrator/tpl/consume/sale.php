<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_pay.php');?>
<h2>销售统计(<?php echo $key;?>)</h2>
<table>
<tr><th>item_id</th><th>item</th><th>费用</th><th>次数</th><th>type</th></tr>
<?php
$total_cash = 0;
foreach ($data as $key=>$value)
{
    $total_cash += $value['num'];
?>
<tr>
	<td><?php echo $value['item_id']?></td>
	<td><?php echo $value['item']?></td>
	<td><?php echo $value['num'];?></td>
	<td><?php echo $value['ct'];?></td>
	<td></td>
</tr>
<?php }?>
</table>
<p>金币总数(Cash total): <?php echo $total_cash;?></p>
<?php include(TPL_DIR.'footer.php');?>

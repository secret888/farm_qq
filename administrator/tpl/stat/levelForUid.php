<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_stat.php');?>
<h2>用户信息(<?php echo $key;?>)</h2>
<table>
<tr><th>uid</th><th>name</th><th>exp</th><th>level</th><th>coin</th><th>cash</th></tr>
<?php
if(is_array($info) && !empty($info))
{
	foreach ($info as $key=>$value)
	{
?>
<tr>
	<td style="text-align:center;"><?php echo $value['uid'];?></td>
	<td style="text-align:center;"><?php echo $value['name'];?></td>
	<td style="text-align:center;"><?php echo $value['exp'];?></td>
	<td style="text-align:center;"><?php echo $value['level'];?></td>
	<td style="text-align:center;"><?php echo $value['coin'];?></td>
	<td style="text-align:center;"><?php echo $value['cash'];?></td>
</tr>
<?php }
}else{
	echo '<td style="text-align:center;" colspan="6">暂无消息</td>';
}
?>
</table>
<?php include(TPL_DIR.'footer.php');?>
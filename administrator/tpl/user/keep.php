<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_stat.php');?>
<h2>保留率</h2>
<table>
<tr><th>日期</th><th>次日留存</th><th>三日留存</th><th>七日留存</th><th>月留存</th></tr>
<?php foreach ($data as $value){?>
<tr>
	<td><?php echo date("Y-m-d",$value['time']);?></td>
	<td><?php echo round($value['day2']/10000,5);?></td>
	<td><?php echo round($value['day3']/10000,5);?></td>
	<td><?php echo round($value['day7']/10000,5);?></td>
	<td><?php echo round($value['day30']/10000,5);?></td>
</tr>
<?php }?>
</table>
<div class="page">
<?php
for( $i = 1 ; $num = ceil($total/$perPage) , $i <= $num  ; $i++)
{
    echo  ($i!=$page)?"<a href='?mod=stat&act=keep&page=".$i."'>$i</a>":"<b>$i</b>";
}
?>
</div>
<?php include(TPL_DIR.'footer.php');?>

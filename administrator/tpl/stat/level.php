<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_stat.php');?>
<h2>等级统计(admin_level_page_1)</h2>
<table>
<tr><th>日期(Date)</th><th>玩家总数</th></tr>
<?php
foreach ($data as $key=>$value)
{
?>
<tr><td><a href="?mod=stat&act=levelInfo&date=<?php echo $value['date'];?>"><?php echo $value['date'];?></a></td>
<td><?php echo $value['num'];?></td></tr>
<?php }?>
</table>
<div class="page">
<?php
for( $i = 1 ; $num = ceil($total/$perPage) , $i <= $num  ; $i++)
{
    echo  ($i!=$page)?"<a href='?mod=stat&act=level&page=".$i."'>$i</a>":"<b>$i</b>";
}
?>
</div>
<?php include(TPL_DIR.'footer.php');?>

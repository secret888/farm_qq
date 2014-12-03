<?php include(TPL_DIR.'header.php');?>
<h2>昨日销售(Yesterday sale)</h2>
<table>
<tr><th>类型(Type)</th><th>商品ID(Item ID)</th><th>商品名称(Item name)</th><th>销售总数(Sale)</th><th>金币总数(Coin)</th></tr>
<?php
$total_num = 0;
$total_gold = 0;
foreach ($data['info'] as $key=>$value)
{
    $total_num += $value['num'];
    $total_gold += $value['gold'];
?>
<tr><td><?php echo $this->stype[$value['stype']];?></td><td><?php echo $key;?></td><td><a href="?mod=stat&act=saleInfo&item_id=<?php echo $key;?>"><?php echo $data['data_items'][$key];?></a></td><td><?php echo $value['num'];?></td><td><?php echo $value['gold'];?></td></tr>
<?php }?>
</table>
<p>昨日销售总数(Yesterday sale total): <?php echo $total_num;?></p>
<p>昨日金币总数(Yesterday coin total): <?php echo $total_gold;?></p>
<br /><br />
<?php
$mtime = $mtime=='' ? time() : $mtime;
if(time() - $mtime > 600){ //删除缓存后10分钟才会再次可以清空缓存
?>
<div><a href="?mod=index&act=cleanData&key=stat_yestoday_sale" onclick="return confirm('您确定要清空缓存吗?(Are you sure?)');">清空缓存(Clear cache)</a> (Update time:<?php echo date('Y-m-d H:i:s',$mtime=='' ? time() : $mtime);?>)</div>
<?php }else{
  echo "<div>十分钟后才能再次清除缓存~~</div>";  
}
?>
<div>(已过:(<?php echo ((time() - $mtime)/60);?>)分钟)</div>
<?php include(TPL_DIR.'footer.php');?>

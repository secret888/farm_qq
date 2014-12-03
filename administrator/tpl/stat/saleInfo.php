<?php include(TPL_DIR.'header.php');?>
<h2>商品明细(Item detail)</h2>
<table>
<tr><th>类型(Type)</th><th>UID</th><th>商品ID(Item ID)</th><th>商品名称(Item name)</th><th>销售数(Number)</th><th>金币数(Coin)</th><th>时间(Date)</th></tr>
<?php
$total_num = 0;
$total_gold = 0;
$count = count($data['info']);
$num = ceil($count/$perPage);
$offset = ($page-1)*$perPage;
for ($i = $offset ; $i < $offset+$perPage ; $i++)
{
    $value = $data['info'][$i];
?>
<tr><td><?php echo $this->stype[$value['stype']];?></td><td><?php echo $value['user_id'];?></td><td><?php echo $value['item'];?></td><td><?php echo $data['data_items']['name'];?></td><td><?php echo $value['num'];?></td><td><?php echo $value['gold'];?></td><td><?php echo date('Y-m-d H:i:s',$value['time']);?></td></tr>
<?php }?>
</table>
<p>销售总数(Sale Total): <?php echo $total_num;?></p>
<p>金币总数(Coin Total): <?php echo $total_gold;?></p>
<?php
for( $i = 1 ; $i <= $num  ; $i++){

       $show=($i!=$page)?"<a href='?mod=stat&act=saleInfo&item_id={$item_id}&page=".$i."'>$i</a>":"<b>$i</b>";
        echo $show." ";
}
?>
<br /><br />
<?php
$mtime = $mtime=='' ? time() : $mtime;
if(time() - $mtime > 600){ //删除缓存后10分钟才会再次可以清空缓存
?>
<div><a href="?mod=index&act=cleanData&key=stat_sale_info<?php echo $item_id;?>" onclick="return confirm('您确定要清空缓存吗?(are you sure?)');">清空缓存(Clear cache)</a> (Update time:<?php echo date('Y-m-d H:i:s',$mtime=='' ? time() : $mtime);?>)</div>
<?php }else{
  echo "<div>十分钟后才能再次清除缓存~~</div>";  
}
?>
<div>(已过:(<?php echo ((time() - $mtime)/60);?>)分钟)</div>
<?php include(TPL_DIR.'footer.php');?>

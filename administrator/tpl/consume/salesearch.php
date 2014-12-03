<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_pay.php');?>
<h2>销售查询(<?php echo $key?> - <?php echo $key1?>)</h2>
<form name="form1" method="GET" action="">
<input type="hidden" name="mod" value="consume" />
<input type="hidden" name="act" value="salesearch" />
<p>Uid: <input type="text" name="uid" value="<?php echo $_GET['uid'];?>"  /><input type="submit" value="销售查询" /></p>
</form>

<?php if(!empty($_GET['uid'])){?>
    <table>
    <tr><th>uid</th><th>item</th><th>费用</th><th>次数</th><th>type</th></tr>
    <?php
    $total_cash = 0;
    foreach ($data as $key=>$value)
    {
        $total_cash += $value['num'];
    ?>
    <tr>
		<td><?php echo $value['uid'];?></td>
		<td><?php echo $value['item'];?></td>
		<td><?php echo $value['num'];?></td>
		<td><?php echo $value['ct'];?></td>
		<td></td>
	</tr>
    <?php }?>
    </table>
    <p>Total: <?php echo $total_cash;?></p>
    <br /><br />
<?php }?>

<?php include(TPL_DIR.'footer.php');?>

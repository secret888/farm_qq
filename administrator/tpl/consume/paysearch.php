<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_pay.php');?>
<h2>充值查询(Pay search)</h2>
<form name="form1" method="post" action="">
<p>Uid: <input type="text" name="uid" value="<?php echo $_POST['uid'];?>"  /><input type="submit" value="充值查询(Search)" /></p>
</form>

<?php if(!empty($_POST['uid'])){?>
    <table>
    <tr><th>订单ID(OrderID)</th><th>数量</th><th>充值金额(Pay cash)</th><th>充值金币(Pay coin)</th><th>时间(Date)</th></tr>
    <?php
    $total_money = 0;
    $total_gamemoney = 0;
    foreach ($data as $key=>$value)
    {
        $total_money += $value['amt']*0.1;
        $total_gamemoney += $value['price']*$value['num'];
    ?>
    <tr><td><?php echo $value['item'].'('.$value['item_id'].')'?></td><td><?php echo $value['num'];?></td><td><?php echo $value['amt']*0.1;?></td><td><?php echo $value['price']*$value['num'];?></td><td><?php echo date('Y-m-d H:i:s',$value['ts']);?></td></tr>
    <?php }?>
    </table>
    <p>充值总金额(Pay cash total): <?php echo $total_money;?></p>
    <p>充值总金币(Pay coin total): <?php echo $total_gamemoney;?></p>
    <br /><br />
<?php }?>

<?php include(TPL_DIR.'footer.php');?>

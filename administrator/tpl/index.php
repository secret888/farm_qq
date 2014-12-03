<?php include('header.php');?>
当前服务器时间:<?php echo date("Y-m-d H:i:s");?><br />
在线用户：<?php echo $row["count"];?><br />
战场用户: <?php echo $total;?>
<?php include('footer.php');?>
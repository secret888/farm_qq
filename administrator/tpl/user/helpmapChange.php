<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_usermodify.php');?>
<div style="color:#F00"><?php echo $msg;?></div>
<form name="form1" id="form1" method="post" action="">
<p>

Uid:<input type="text" name="uid" value="<?php echo $_POST["uid"];?>"  />&nbsp;
key:<input type="text" name="key" value="<?php echo $_POST["key"]?>" /> &nbsp;
<input type="submit" value="查看" name="show" />
</p>
<p><input type="submit" value="添加" name="change" /></p>

</form>
<?php include(TPL_DIR.'footer.php');?>
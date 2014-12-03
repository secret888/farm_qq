<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_config.php');?>
<form name="form1" method="post" action="">
<p>Config.php:
<textarea name="config" cols="80" rows="30"><?php echo $data;?></textarea>
<p>
<input type="submit" value="提交" /></p>
</form>
<?php include(TPL_DIR.'footer.php');?>
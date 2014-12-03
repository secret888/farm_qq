<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_tools.php');?>
<form name="form1" method="post" action="">
<p>
	time: <input name="time" id="time" value="<?php echo $time;?>" />
	<select name="type"><option value="1">时间转日期</option><option value="2">日期转时间</option></select>
</p>
<p><input type="submit" value="提交" /></p>
</form>
<?php
echo "<pre>";
echo $time."<br />";
echo $data."<br />";
echo "</pre>";
?>
<?php include(TPL_DIR.'footer.php');?>

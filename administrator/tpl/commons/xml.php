<?php include(TPL_DIR.'header.php');?>
<form name="form1" method="post" action="">
<p>php路径: 
<select name="phppath">
<?php foreach ($phppath as $path){?>
<option value="<?php echo $path;?>"><?php echo $path;?></option>
<?php }?>
</select>
</p>
<p>Xml.php: <input type="text" name="xml" value="<?php echo $setting;?>" readonly /></p>
<p><input type="submit" value="提交" /></p>
</form>
<?php include(TPL_DIR.'footer.php');?>
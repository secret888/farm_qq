<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_tools.php');?>
<form name="form1" method="post" action="">
<p>json: <textarea name="json" cols="70" rows="5" id="json"><?php echo $json;?></textarea></p>
<p><input type="submit" value="提交" /></p>
</form>
<?php
echo "<pre>";
var_export($array);
echo "</pre>";
?>
<?php include(TPL_DIR.'footer.php');?>

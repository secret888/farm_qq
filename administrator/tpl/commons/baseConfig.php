<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_config.php');?>
<form name="form1" method="post" action="" enctype="multipart/form-data">
<p>game config:
<input type="file" name="file" />
<p>
<input type="submit" value="提交" /></p>
</form>
<?php 
if($return)
{
	echo $return;
}
include(TPL_DIR.'footer.php');
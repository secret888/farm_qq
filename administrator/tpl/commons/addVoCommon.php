<?php include(TPL_DIR.'header.php');?>
<h2>公共配置(vo_common_KEY)</h2>
<div><a href="?mod=commons&act=addVoCommon">添加配置</a> <a href="?mod=commons&act=voCommon">配置列表</a></div>

<form name="form1" method="post" action="">
<p>key: <input type="text" name="key" value="<?php echo $row['key'];?>" <?php if(!empty($row['key'])) echo 'readonly';?>  /></p>
<p>value: 
  <textarea name="value" cols="80" rows="30"><?php echo $row['value'];?></textarea>
</p>
<p><input type="submit" value="提交" /></p>
</form>
<?php include(TPL_DIR.'footer.php');?>
<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_usermodify.php');?>
<div style="color:#F00"><?php echo $msg;?></div>
<form name="form1" id="form1" method="post" action="">
<p>

Uid:<input type="text" name="uid" value="<?php echo $_POST["uid"];?>"  />&nbsp;
id:<input type="text" name="id" value="<?php echo $_POST["id"]?>" /> &nbsp; 
map_id:<select name="map_id">
<option value="0" <?php if($map_id==0){?> selected<?php }?>>0</option>
</select> &nbsp;
<input type="submit" value="查看" name="show" />
</p>
<p>info: 
  <textarea name="info" cols="80" rows="30"><?php if($info) var_export($info);?></textarea>
 </p>
 <p><input type="submit" value="提交" name="change" />&nbsp;&nbsp;<input type="submit" value="删除" name="delete" /></p>
</form>
<?php include(TPL_DIR.'footer.php');?>
<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_user.php');?>
<div style="color:#F00"><?php echo $msg;?></div>
<form name="form1" id="form1" method="post" action="">
<p>

Uid:<input type="text" name="uid" value="<?php echo $uid;?>"  />&nbsp;
id:<input type="text" name="id" value="<?php echo $id?>" /> &nbsp; 
<input type="submit" value="查看" name="show" />
<br />最高关卡：<?php if(!empty($topid)){echo $topid;}?>
<input type="submit" value="直接跳关" name="skip" />
</p>
<p><input type="submit" value="提交" name="change" />&nbsp;&nbsp;<input type="submit" value="删除" name="delete" /></p>
<p>info: 
  <textarea name="info" cols="80" rows="30"><?php if($info) var_export($info);?></textarea>
 </p>
 
</form>
例子:<br />
 array (<br />
    'id' => '1',<br />
    'score' => '125',<br />
    'stars' => '3',<br />
    'time' => '1369395921',<br />
  )<br />
<?php include(TPL_DIR.'footer.php');?>
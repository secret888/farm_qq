<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_user.php');?>
<script type="text/javascript">
function reset_user()
{
	if(confirm('您确定要重置吗？'))
	{
		document.getElementById("reset_hidden").value = "1";
		document.getElementById("form1").submit();
	}
}
</script>
<div style="color:#F00"><?php echo $msg;?></div>
<form name="form1" id="form1" method="post" action="">
<p>Uid: <input type="text" name="uid" value="<?php echo $_POST["uid"];?>"  />
<input type="submit" name="show" value="查看" />
<input type="submit" name="gohome" value="GoHome" />
<input type="hidden" name="reset_hidden" id="reset_hidden" value="" />
<input type="button" name="reset_button" value="重置" <?php if(!$_POST['uid']) echo 'disabled="disabled"';?> onclick="reset_user();" />
<select name=reset <?php if(!$_POST['uid']) echo 'disabled="disabled"';?> ><option value="1">重置用户</option><option value="2" selected>重置缓存</option></select>
</p>
</form>

<form name="form2" id="form2" method="post" action="">
<p>Uid: <textarea name="sandboxuid" rows="5" cols="50">
<?php var_export($sandbox);?>
</textarea>

<input type="submit" name="add" value="提交" />
</p>
</form>




<!-- 显示游戏主界面 -->
<?php 
if($_POST["gohome"])
{
?>

<div>
<div id="flashMain">
         <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="970" height="650" id="flashPlayer">
            <param name="movie" value="<?php echo $flash_vars['mainpath'].$flash_vars['flashName'];?>" />
            <param name="flashvars" value="<?php echo $flash_vars_str;?>" />
            <param name="menu" value="false" />
            <param name="allowFullScreen" value="true" />
            <param name="allowscriptaccess" value="always" />        
            <param name="wmode" value="transparent" />
            <param name="bgcolor" value="#93eafe" />
            <embed name="flashPlayer" src="<?php echo $flash_vars['mainpath'].$flash_vars['flashName'];?>" flashvars="<?php echo $flash_vars_str;?>" menu="false" allowFullScreen="true" bgcolor="#93eafe"  wmode="transparent"  allowscriptaccess="always" type="application/x-shockwave-flash" width="970" height="650"></embed>
        </object>
    </div>

</div>
<?php 	
}
?>

<br/>
<?php
if(is_array($row))
{
    foreach ($row as $key=>$value)
    {
        echo "<p>{$key}: {$value}</p>";
    }
}
?>

<form name="form1" method="post" action="">
<?php
if(is_array($row))
{
    foreach ($row as $key=>$value)
    {
        echo "<p>{$key}: <input type=\"text\" name=\"main[{$key}]\" value=\"{$value}\"  /></p>";
    }
    echo '<p><input type="submit" name="edit" value="修改" /></p>';
}
?>
</form>

<?php include(TPL_DIR.'footer.php');?>
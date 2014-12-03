<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_tools.php');?>
<div><a href="?mod=commons&act=banner&t=add">添加广告</a> </div>
<table>
<tr><th>id</th><th>src</th><th>href</th><th></th></tr>
<?php
if(!empty($data) && is_array($data))
{
    foreach ($data as $key=>$row)
    {
?>

<tr>
	<td><?php echo $key;?></td>
	<td>
		<img src="<?php echo $row['src'];?>" width="760" height="150" alt="" />
	</td>
	<td><?php echo $row['href'];?></td>
	<td>
		<a href='?mod=commons&act=banner&t=edit&id=<?php echo $key;?>'>编辑(Modify)</a>
		<a onclick='return confirm(\"您确定要删除吗(Are you sure?)\");' href='?mod=commons&act=banner&t=del&id=<?php echo $key;?>'>删除(Delete)</a>
	</td>
</tr>
<?php  
    }
}
?>
</table>

<?php 
if($id || $t)
{
?>
<form action="" method="POST">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
title:<input type="text" name="title" value="<?php echo $udata['title']?>" /><br />
desc:<input type="text" name="desc" value="<?php echo $udata['desc']?>" /><br />
src:<input type="text" name="src" value="<?php echo $udata['src']?>" /><br />
href:<input type="text" name="href" value="<?php echo $udata['href']?>" /><br />
type:<input type="text" name="type" value="<?php echo $udata['type']?>" /> &nbsp;1:图片广告 &nbsp; 2:文本广告<br />
target:<input type="text" name="target" value="<?php echo $udata['target']?>" /><br />
<input type="hidden" name="t" value="<?php echo $t?>" />
<input type="hidden" name="up" value="OK" />
<input type="submit" value="提交" />
</form>
<?php 	

}
?>


<?php include(TPL_DIR.'footer.php');?>

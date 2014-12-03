<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_user.php');?>
<br/>
Uid= <?php echo $row['uid']; ?>
<form name="form1" method="post" action="">
<p>UString: <input type="text" name="ustring" value="<?php echo $_POST['ustring'];?>"  /></p>
<p><input type="submit" value="查找(Find)" /></p>
</form>
<br />
<h2>UString - UID</h2>
UString(Social ID)= <?php echo $row['ustr']; ?><br/>
Container= <?php echo $row['container'];?>
<form name="form1" method="post" action="">
<p>Uid: <input type="text" name="uid" value="<?php echo $_POST['uid'];?>"  /></p>
<p><input type="submit" value="查找(Find)" /></p>
</form>
<br />
<h2>Name - UID</h2>
<table>
<tr>
<th>Uid</th><th>Name</th>
</tr>
<?php
if(!empty($data) && is_array($data)){
    foreach ($data as $value)
    {
        echo "<tr><td>{$value['uid']}</td><td>{$value['name']}</td></tr>";
    }
}
?>
</table>
<form name="form1" method="post" action="">
<p>UName: <input type="text" name="uname" value="<?php echo $_POST['uname'];?>"  /></p>
<p><input type="submit" value="查找(Find)" /></p>
</form>
<?php include(TPL_DIR.'footer.php');?>
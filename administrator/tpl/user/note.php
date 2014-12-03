<?php include(TPL_DIR.'header.php');?>
<h2>公共配置</h2>
<div><a href="?mod=user&act=addNote">添加配置</a> <a href="?mod=user&act=note">配置列表</a></div>
<table>
<tr><th>key</th><th>value</th><th>operate</th></tr>
<?php
if(!empty($data) && is_array($data))
{
    foreach ($data as $row)
    {
        $url = "<a href='?mod=user&act=addNote&key={$row['key']}'>{$row['key']}</a>";
        $delete = '';
        if(strpos($row['key'],'setting')===false)
            $delete = "<a onclick='return confirm(\"您确定要删除吗(Are you sure?)\");' href='?mod=user&act=delNote&key={$row['key']}'>删除(Delete)</a>";
        echo "<tr><td>{$url}</td><td>{$row['value']}</td><td>$delete</td></tr>";
    }
}
?>
</table>
<br /><br />
<div>缓存(Cache): key_value_{key}</div>

<?php include(TPL_DIR.'footer.php');?>
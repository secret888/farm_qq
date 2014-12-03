<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_config.php');?>
<div><a href="?mod=commons&act=addVoCommon">添加配置</a> <a href="?mod=commons&act=voCommon">配置列表</a></div>
<table>
<tr><th>key</th><th>value</th><th>operate</th></tr>
<?php
if(!empty($data) && is_array($data))
{
    foreach ($data as $row)
    {
        $url = "<a href='?mod=commons&act=addVoCommon&key={$row['key']}'>{$row['key']}</a>";
        $delete = '';
        if(strpos($row['key'],'setting')===false)
            $delete = "<a onclick='return confirm(\"您确定要删除吗(Are you sure?)\");' href='?mod=commons&act=delVoCommon&key={$row['key']}'>删除(Delete)</a>";
        echo "<tr><td>{$url}</td><td>{$row['value']}</td><td>$delete</td></tr>";
    }
}
?>
</table>
<br /><br />
<div>缓存: vo_common_KEY</div>

<?php include(TPL_DIR.'footer.php');?>
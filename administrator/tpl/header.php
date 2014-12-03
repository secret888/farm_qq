<?php
$links = array(
/**
 * 用户
 */
array(
    array( 'title'   =>  '账号管理' , 'src'  =>  '?mod=user&act=change' ),
	//array( 'title'   =>  '资源管理' , 'src'  =>  '?mod=usermodify&act=playmoney' ),
    array( 'title'   =>  '查看缓存' , 'src'  =>  '?mod=user&act=showCache' ),
	array( 'title'   =>  '公共配置' , 'src'  =>  '?mod=commons&act=voCommon'),
	//array( 'title'   =>  'xml' , 'src'  =>  '?mod=commons&act=xml' ),
	
	array( 'title'   =>  '每日统计' , 'src'  =>  '?mod=stat&act=everyStat' ),
	array( 'title'   =>  '充值查询' , 'src'  =>  '?mod=consume&act=everydayPay'),
	//array( 'title'   =>  '日志相关' , 'src'  =>  '?mod=log&act=alliancegvg'),
	array( 'title'   =>  '系统相关' , 'src'  =>  '?mod=systerm&act=smessage'),
	array( 'title'   =>  '工具箱' , 'src'  =>  '?mod=commons&act=jsonToArray'),
), 
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理</title>
<style type="text/css">
    *{ font-size:12px;}
    .header a{ margin-right:7px; border:#06F 1px solid;  line-height:25px; padding:3px;}
    a{text-decoration:none;}
    a:link {color: #666}
    a:visited {color: #666}
    a:hover { color:#F00;}
    a:active {color: #F00}
    table{width:70%;border-collapse:collapse; line-height:20px;}
    table th { background-color:#B5B5FF;border:#06F solid 1px;}
    table td {  border:#06F solid 1px; padding-left:5px;}

    .page a{ width:20px; line-height:20px; display:block; float:left; background:#B5B5FF; margin:2px; padding-left:10px;}
	.page b{width:20px; line-height:20px; display:block; float:left;  margin:2px;padding-left:10px;background:#CCC}
	.mytable{ margin-top:5px;margin-bottom:5px;}
	.mytable a{ border:#CCC 1px dotted; padding:2px; margin-right:5px;}
</style>

</head>

<body>
<?php
foreach ($links as $link)
{
    echo '<div class="header" id="header">';
    foreach ($link as $lk)
    {
        if('?'.$_SERVER['QUERY_STRING'] == $lk['src']) $sty = 'style="color:#F00"'; else $sty='';
        echo '<a href="'.$lk['src'].'" '.$sty.'>'.$lk['title'].'</a>';
    }
    echo '</div>';
}
?>
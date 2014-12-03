<div class="mytable">
	<a href="?mod=commons&act=voCommon">公共配置</a>
	<a href="?mod=commons&act=baseConfig">game config</a>
	<?php
	foreach($vo_config as $value)
	{
		echo '<a href="?mod=commons&act=config&config='.$value.'">'.$value.'</a> ';
	}
	?>
</div>
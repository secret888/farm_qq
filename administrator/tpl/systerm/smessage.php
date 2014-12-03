<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'vo_systerm.php');?>
<div style="color:#F00"><?php echo $msg;?></div>


发放补偿等信息
<form name="form1" id="form1" method="post" action="">
<p>
发放Uid:<input type="checkbox" name="alluid" />发放给所有玩家(勾选则发放给当前所有玩家)
<br />
&nbsp;&nbsp;&nbsp;<input type="text" name="uid" value="" style="width:300px" />
<br />
每个UID以‘,’隔开。例：10000,10004,10015
</p>
<p>
发放物品ID:<input type="text" name="itemid" value=""  style="width:300px"  /><br />
每种物品以‘,’隔开。
例： <span>lives,coins,6153</span>
<br />
果酱(lives) 辣椒(coins)  增加5次移动次数(6101) 盗贼(6155) 火箭筒(6150) 大怪兽(6153) 全部收集物+1(6152)
</p>
<p>
发放物品数量:<input type="text" name="itemnum" value=""  style="width:300px"  /><br />
此项与上一项对应。参考如下例子。
例： <span>3,300,2</span>
<br />
(两项的例子结合在一起。就是发放果酱3个，辣椒300个，6153道具2个)
</p>
<p>
公告文字内容:<input type="text" name="desc" value="" style="width:400px"  /><br />
</p>
<input type="hidden" name="state" value="ok" />
<input type="submit" value="提交" name="change" onclick="this.disabled = true"  />
</form>

<?php include(TPL_DIR.'footer.php');?>
<?php include(TPL_DIR.'header.php');?>
<h2>Show cache</h2>
<div style="color:#F00"><?php echo $msg;?></div>
<form name="form1" method="post" action="">
<p>Key: <input type="text" name="key" value="<?php echo $_POST['key'];?>" style="width:200px;" />
<input type="submit" name="submit" value="show" />
<input type="submit" name="submit" value="delete" <?php if(!$_POST['key']) echo 'disabled="disabled"';?> />
<input type="submit" name="submit" value="export" <?php if(!$_POST['key']) echo 'disabled="disabled"';?> />
</p>
</form>
<br><br>


<form name="form2" method="GET" action="">
<input type="hidden" name="mod" value="<?php echo $_GET['mod'];?>" />
<input type="hidden" name="act" value="<?php echo $_GET['act'];?>" />
<table>
<tr><td>Key:</td><td><input type="text" name="key" value="<?php echo $_GET['key'];?>"  /></td><tr/>
<tr><td>k:</td><td><input type="text" name="k" value="<?php echo $_GET['k'];?>"  /></td><tr/>
<tr><td>v:</td><td><input type="text" name="v" value="<?php echo $_GET['v'];?>"  /></td><tr/>
<tr><td> </td><td><input type="submit" name="submit" value="submit" /></td><tr/>
</table>
</form>
<br><br>

<?php
if(!empty($_POST['key']))
{
    echo '<div>Cache:</div>';
    echo '<pre>';
    var_export($data);
    echo '</pre>';
}
?>
<table>
    <tr>
      <td nowrap="nowrap">玩家缓存: UID_user</td>
      <td nowrap="nowrap">Sharding: USTRING_ustr</td>
      <td nowrap="nowrap">Sharding: UID_sharding</td>
      <td nowrap="nowrap">建筑Item: UID_item_0</td>
    </tr>
    <tr>
      <td nowrap="nowrap">地图缓存: UID_map</td>
      <td nowrap="nowrap">背包缓存: UID_bag</td>
      <td nowrap="nowrap">收藏品缓存: UID_collection</td>
      <td nowrap="nowrap">医院缓存: UID_deadheros</td>
    </tr>
    <tr>
      <td nowrap="nowrap">副本缓存:UID_questRank</td>
      <td nowrap="nowrap">兑换接口: UID_exchange</td>
      <td nowrap="nowrap">大使馆成员: UID_uEmbassy</td>
      <td nowrap="nowrap">大使馆奖励: UID_uEReward</td>
    </tr>
    <tr>
      <td nowrap="nowrap">朋友好友缓存: UID_pyFriends</td>
      <td nowrap="nowrap">空间好友缓存: UID_qzFriends</td>
      <td nowrap="nowrap">战斗日志缓存: UID_attacks_log</td>
      <td nowrap="nowrap"></td>
    </tr>
    <tr>
      <td nowrap="nowrap">联盟缓存: alliance</td>
      <td nowrap="nowrap">联盟成员缓存: AID_alliance_member</td>
      <td nowrap="nowrap">联盟战日志:alliance_attacks_log</td>
      <td nowrap="nowrap">联盟战积分记录:alliance_member_points</td>
    </tr>
    <tr>
      <td nowrap="nowrap">联盟战斗缓存: alliance_gvg_scorse</td>
      <td nowrap="nowrap">联盟战斗结果: alliance_gvg_result</td>
      <td nowrap="nowrap">联盟战参战列表:alliance_gvg_list</td>
      <td nowrap="nowrap">联盟排行:alliance_ranking</td>
    </tr>

</table>
<?php include(TPL_DIR.'footer.php');?>
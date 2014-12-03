<?php
/**
 * filename : SunvyInterfaces.php
 * author : Simba
 * version : 0.1
 */

// sunvy -> media
interface SunvyInterfaces {
	// 获得用户信息
	function getUserInfo();
	// 获得道具信息
	function getProperInfo();
	// 追加道具信息
	function addProperInfo();
	// 分红回调信息
	function setBonusInfo();
}

// media -> sunvy
interface SunvyApiInterfaces {
	// 邀请成功通知					
	function setInviteInfo( $viewerId, $inviteId );
	// 消费成功通知
	function setPurchaseInfo( $viewerId, $orderId, $paymentType, $itemPrice, $itemNum, $itemName, $itemCode, $itemVolume, $itemType );
	// 奖励成功通知
	function setRewardInfo( $viewerId, $rewardType, $rewardReason, $vcBonus, $gcBonus, $rcmBonus );
	// 登陆通知
	function setAccessInfo( $viewerId, $viwerName, $viewerAge, $viewerGender, $accessIp, $accessToken, $accessInfo );
	// 获得分红信息
	function getBonusInfo( $viewerId, $bonusCycle, $cycleNum );
	// 发起分红请求
	function setBonusRequest( $viewerId, $bonusCycle, $cycleNum );
	// 限时活动申请通知
	function setApplyRequest( $viewerId, $viewerName, $grouponId );
	// 限时活动内容查询
	function searchApplyInfo( $viewerId, $viewerName, $grouponId );
	// 限时活动发放请求
	function requireApply( $viewerId, $viewerName, $applyId );
	// KEYCODE活动查询
	function searchKeycode( $viewerId, $deviceId );
	// KEYCODE发放请求
	function requireKeycode( $eventId, $ownerId, $deviceId );
	// KEYCODE兑奖请求
	function applyKeycode( $eventId, $ticketNo, $userId, $deviceId );
}
?>
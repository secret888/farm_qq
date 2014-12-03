<?php
$_sunvyTestModal = false;
require_once( 'Sunvy.php' );
class mySunvyLog {
	public $mySunvyLog;
	
	public function mySunvyLog( $mediaId, $mediaKey, $mediaSecret ) {
		$this->mySunvyLog = new STAT_API( $mediaId, $mediaKey, $mediaSecret );
	}
	public function Set_Invite_Info( $viewerId, $inviteId ) {
		return $this->mySunvyLog->setInviteInfo( $viewerId, $inviteId );
	}
	public function Set_Purchase_Info( $viewerId, $orderId, $paymentType, $itemPrice, $itemNum, $itemName, $itemCode, $itemVolume, $itemType ) {
		return $this->mySunvyLog->setPurchaseInfo($viewerId,$orderId,$paymentType,$itemPrice,$itemNum,$itemName,$itemCode,$itemVolume,$itemType);
	}
	public function Set_Reward_Info( $viewerId, $rewardType, $rewardReason, $vcBonus, $gcBonus, $rcmBonus ) {
		return $this->mySunvyLog->setRewardInfo($viewerId,$rewardType,$rewardReason,$vcBonus,$gcBonus,$rcmBonus);
	}
	public function Set_Access_Info( $viewerId, $viwerName, $viewerAge, $viewerGender, $accessIp, $accessToken, $accessInfo ) {
		return $this->mySunvyLog->setAccessInfo($viewerId,$viwerName,$viewerAge,$viewerGender,$accessIp,$accessToken,$accessInfo);
	}
	public function Get_Bonus_Info( $viewerId, $bonusCycle = 0, $cycleNum = 2 ) {
		return $this->mySunvyLog->getBonusInfo( $viewerId, $bonusCycle, $cycleNum );
	}
	public function Set_Bonus_Request( $viewerId, $bonusCycle = 0, $cycleNum = 1 ) {
		return $this->mySunvyLog->setBonusRequest( $viewerId, $bonusCycle, $cycleNum );
	}
	public function Set_Apply_Request( $viewerId, $viewerName, $grouponId ) {
		return $this->mySunvyLog->setApplyRequest( $viewerId, $viewerName, $grouponId );
	}
	public function Search_Apply_Info( $viewerId, $viewerName, $grouponId ) {
		return $this->mySunvyLog->searchApplyInfo( $viewerId, $viewerName, $grouponId );
	}
	public function Require_Apply( $viewerId, $viewerName, $applyId ) {
		return $this->mySunvyLog->requireApply( $viewerId, $viewerName, $applyId );
	}
	public function Search_Keycode( $viewerId, $deviceId ) {
		return $this->mySunvyLog->searchKeycode( $viewerId, $deviceId );
	}
	public function Require_Keycode( $eventId, $ownerId, $deviceId ) {
		return $this->mySunvyLog->requireKeycode( $eventId, $ownerId, $deviceId );
	}
	public function Apply_Keycode( $eventId, $ticketNo, $userId, $deviceId ) {
		return $this->mySunvyLog->applyKeycode( $eventId, $ticketNo, $userId, $deviceId );
	}
};
?>
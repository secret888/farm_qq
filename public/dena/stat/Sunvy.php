<?php
/**
 * filename : Sunvy.php
 * author : Simba
 * version : 0.1
 */
require_once( "SunvyAuth.php" );
require_once( "SunvyApi.php" );
require_once( "SunvyInterfaces.php" );

// sunvy -> media 的类 Sunvy
class Sunvy extends SunvyAuth implements SunvyInterfaces {
	function Sunvy( $mediaId, $mediaKey, $mediaSecret ) {
		$this->SunvyAuth( $mediaId, $mediaKey, $mediaSecret );
	}
	
	// 获得用户信息，请定义全局函数 sunvyGetUserInfo( $viewerId )
	function getUserInfo() {
		if( !function_exists( "sunvyGetUserInfo" ) ) {
			$this->missFunction( "sunvyGetUserInfo" );
		}
		
		$viewerId = $this->auth['viewerId'];
		
		if( !isset( $this->auth['viewerId'] ) ) {
			$this->missParam( "viewerId" );
		}

		$info = sunvyGetUserInfo( $viewerId );
		if( empty( $info ) ) {
			$this->missInfo();
		} else {
			$info += array(
				'mediaId' => $this->mediaId,
				'viewerId' => $viewerId,
			);
			$this->ok( $info );
		}
	}
	
	// 获得道具信息，请定义全局函数 sunvyGetProperInfo( $viewerId, $itemCode, $storageType )
	function getProperInfo() {
		if( !function_exists( "sunvyGetProperInfo" ) ) {
			$this->missFunction( "sunvyGetProperInfo" );
		}
		
		$viewerId = $this->auth['viewerId'];
		$itemCode = $this->auth['itemCode'];
		$storageType = $this->auth['storageType'];
		
		if( !isset( $this->auth['viewerId'] ) ) {
			$this->missParam( "viewerId" );
		} elseif( !isset( $this->auth['itemCode'] ) ) {
			$this->missParam( "itemCode" );
		} elseif( empty( $storageType ) ) {
			$storageType = 1;	// 默认包裹
		} 

		$info = sunvyGetProperInfo( $viewerId, $itemCode, $storageType );
		if( empty( $info ) ) {
			$this->missInfo();
		} else {
			$info += array(
				'mediaId' => $this->mediaId,
				'viewerId' => $viewerId,
			);
			$this->ok( $info );
		}
	}
	
	// 追加道具信息，请定义全局函数 sunvyAddProperInfo( $viewerId, $itemCode, $itemNum, $storageType )
	function addProperInfo() {
		if( !function_exists( "sunvyAddProperInfo" ) ) {
			$this->missFunction( "sunvyAddProperInfo" );
		}
		
		$viewerId = $this->auth['viewerId'];
		$itemCode = $this->auth['itemCode'];
		$itemNum = $this->auth['itemNum'];
		$storageType = $this->auth['storageType'];
		
		if( !isset( $this->auth['viewerId'] ) ) {
			$this->missParam( "viewerId" );
		} elseif( !isset( $this->auth['itemCode'] ) ) {
			$this->missParam( "itemCode" );
		} elseif( !isset( $this->auth['itemNum'] ) ) {
			$this->missParam( "itemNum" );
		} elseif( empty( $storageType ) ) {
			$storageType = 1;	// 默认包裹
		} 

		$info = sunvyAddProperInfo( $viewerId, $itemCode, $itemNum, $storageType );
		if( empty( $info ) ) {
			$this->missInfo();
		} else {
			$info += array(
				'mediaId' => $this->mediaId,
				'viewerId' => $viewerId,
			);
			$this->ok( $info );
		}
	}

	// 分红回调信息，请定义全局函数 sunvySetBonusInfo( $viewerId, $vcBonus, $gcBonus, $rcaBonus, $rcbBonus, $rccBonus, $rcdBonus, $rceBonus )
	function setBonusInfo() {
		if( !function_exists( "sunvySetBonusInfo" ) ) {
			$this->missFunction( "sunvySetBonusInfo" );
		}
		
		$viewerId = $this->auth['viewerId'];
		$vcBonus = $this->auth['vcBonus'];
		$gcBonus = $this->auth['gcBonus'];
		$rcaBonus = $this->auth['rcaBonus'];
		$rcbBonus = $this->auth['rcbBonus'];
		$rccBonus = $this->auth['rccBonus'];
		$rcdBonus = $this->auth['rcdBonus'];
		$rceBonus = $this->auth['rceBonus'];
		
		if( !isset( $this->auth['viewerId'] ) ) {
			$this->missParam( "viewerId" );
		} elseif( !isset( $this->auth['vcBonus'] ) ) {
			$this->missParam( "vcBonus" );
		} elseif( !isset( $this->auth['gcBonus'] ) ) {
			$this->missParam( "gcBonus" );
		} elseif( !isset( $this->auth['rcaBonus'] ) ) {
			$this->missParam( "rcaBonus" );
		} elseif( !isset( $this->auth['rcbBonus'] ) ) {
			$this->missParam( "rcbBonus" );
		} elseif( !isset( $this->auth['rccBonus'] ) ) {
			$this->missParam( "rccBonus" );
		} elseif( !isset( $this->auth['rcdBonus'] ) ) {
			$this->missParam( "rcdBonus" );
		} elseif( !isset( $this->auth['rceBonus'] ) ) {
			$this->missParam( "rceBonus" );
		}

		$info = sunvySetBonusInfo( $viewerId, $vcBonus, $gcBonus, $rcaBonus, $rcbBonus, $rccBonus, $rcdBonus, $rceBonus );
		if( empty( $info ) ) {
			$this->missInfo();
		} else {
			$info += array(
				'mediaId' => $this->mediaId,
				'viewerId' => $viewerId,
			);
			$this->ok( $info );
		}
	}
}

// media -> sunvy 的类 STAT_API
class STAT_API extends SunvyApi implements SunvyApiInterfaces {
	function STAT_API( $mediaId, $mediaKey, $mediaSecret ) {
		$this->SunvyApi( $mediaId, $mediaKey, $mediaSecret );
	}

	// 邀请成功通知					
	function setInviteInfo( $viewerId, $inviteId ) {
		$this->base = $this->api."invite";
		$this->filter = array(
			'mediaId' => $this->mediaId, 
			'inviteId' => $inviteId,
			'viewerId' => $viewerId
		);
		return $this->_makeRequest();
	}

	// 消费成功通知
	function setPurchaseInfo( $viewerId, $orderId, $paymentType, $itemPrice, $itemNum, $itemName, $itemCode, $itemVolume, $itemType ) {
		$this->base = $this->api."purchase";
		$this->filter = array(
			'mediaId' => $this->mediaId,
			'viewerId' => $viewerId,
			'orderId' => $orderId,
			'paymentType' => $paymentType,
			'itemPrice' => $itemPrice,
			'itemNum' => $itemNum,
			'itemName' => $itemName,
			'itemCode' => $itemCode,
			'itemVolume' => $itemVolume,
			'itemType' => $itemType
		);
		return $this->_makeRequest();
	}

	// 奖励成功通知
	function setRewardInfo( $viewerId, $rewardType, $rewardReason, $vcBonus, $gcBonus, $rcmBonus ) {
		$this->base = $this->api."reward";
		$this->filter = array(
			'mediaId' => $this->mediaId,
			'viewerId' => $viewerId,
			'rewardType' => $rewardType,
			'rewardReason' => $rewardReason,
			'vcBonus' => $vcBonus,
			'gcBonus' => $gcBonus,
			'rcmBonus' => $rcmBonus
		);
		return $this->_makeRequest();
	}

	// 登陆通知
	function setAccessInfo( $viewerId, $viwerName, $viewerAge, $viewerGender, $accessIp, $accessToken, $accessInfo ) {
		$this->base = $this->api."access";
		$this->filter = array(
			'mediaId' => $this->mediaId,
			'viewerId' => $viewerId,
			'viewerName' => $viwerName,
			'viewerAge' => $viewerAge,
			'viewerGender' => $viewerGender,
			'accessIp' => $accessIp,
			'accessToken' => $accessToken,
			'accessInfo' => $accessInfo
		);
		return $this->_makeRequest();
	}

	// 获得分红信息
	function getBonusInfo( $viewerId, $bonusCycle, $cycleNum ) {
		$this->base = $this->api."bonus/detail";
		$this->filter = array(
			'mediaId' => $this->mediaId,
			'viewerId' => $viewerId,
			'bonusCycle' => $bonusCycle,
			'cycleNum' => $cycleNum,
		);
		return $this->_makeRequest( true );
	}

	// 发起分红请求
	function setBonusRequest( $viewerId, $bonusCycle, $cycleNum ) {
		$this->base = $this->api."bonus/commit";
		$this->filter = array(
			'mediaId' => $this->mediaId,
			'viewerId' => $viewerId,
			'bonusCycle' => $bonusCycle,
			'cycleNum' => $cycleNum,
		);
		return $this->_makeRequest( true );
	}
	
	// 限时活动申请通知
	function setApplyRequest( $viewerId, $viewerName, $grouponId ) {
		$this->base = $this->api."apply";
		$this->filter = array(
			'mediaId' => $this->mediaId,
			'viewerId' => $viewerId,
			'viewerName' => $viewerName,
			'grouponId' => $grouponId,
		);
		return $this->_makeRequest( true );
	}
	
	// 限时活动内容查询
	function searchApplyInfo( $viewerId, $viewerName, $grouponId ) {
		$this->base = $this->api."apply/detail";
		$this->filter = array(
			'mediaId' => $this->mediaId,
			'viewerId' => $viewerId,
			'viewerName' => $viewerName,
			'grouponId' => $grouponId,
		);
		return $this->_makeRequest( true );
	}
	
	// 限时活动发放请求
	function requireApply( $viewerId, $viewerName, $applyId ) {
		$this->base = $this->api."apply/commit";
		$this->filter = array(
			'mediaId' => $this->mediaId,
			'viewerId' => $viewerId,
			'viewerName' => $viewerName,
			'applyId' => $applyId,
		);
		return $this->_makeRequest( true );
	}
	
	// KEYCODE活动查询
	function searchKeycode( $viewerId, $deviceId ) {
		$this->base = $this->api."ticket/detail";
		$this->filter = array(
			'mediaId' => $this->mediaId,
			'viewerId' => $viewerId,
			'deviceId' => $deviceId,
		);
		return $this->_makeRequest( true );
	}

	// KEYCODE发放请求
	function requireKeycode( $eventId, $ownerId, $deviceId ) {
		$this->base = $this->api."ticket/issue";
		$this->filter = array(
			'mediaId' => $this->mediaId,
			'eventId' => $eventId,
			'ownerId' => $ownerId,
			'deviceId' => $deviceId,
		);
		return $this->_makeRequest( true );
	}
	
	// KEYCODE兑奖请求
	function applyKeycode( $eventId, $ticketNo, $userId, $deviceId ) {
		$this->base = $this->api."ticket/check";
		$this->filter = array(
			'mediaId' => $this->mediaId,
			'eventId' => $eventId,
			'ticketNo' => $ticketNo,
			'userId' => $userId,
			'deviceId' => $deviceId,
		);
		return $this->_makeRequest( true );
	}
}
?>

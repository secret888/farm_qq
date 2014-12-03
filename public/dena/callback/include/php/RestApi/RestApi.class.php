<?php
switch (_Platform) {
	case 'dena':
		if(_RestApi_Debug)
		{
			include_once( 'Yahoo_test.class.php' );
		}else 
		{
			include_once( 'Yahoo.class.php' );
		}
		break;
	case 'rw':
		break;
	case 'aima':
		break;
	case 'gmo':
		break;
	case 'mixi':
		break;
}
class RestApi{
	private $RA;
	private $RA_SIG;
    public function __construct( $api_key, $secret,$app_id ) {
    	switch (_Platform) {
			case 'dena':				
				$this->RA=new YahooApp($api_key, $secret,$app_id);
				break;
			case 'rw':
				break;
			case 'aima':
				break;
			case 'gmo':
				break;
			case 'mixi':
				break;
		}
    }
    public function Api($method,$params)
    {
    	switch ($method) {
			case 'UserInfo':
				return $this->RA->GetUserInfo($params);
				break;
			case 'FriendsAppInfo':
				return $this->RA->GetFriendsAppInfo($params);
				break;
			case 'FriendsInfo':
				return $this->RA->GetFriendsInfo($params);
				break;
			case 'PayMoney':
				return $this->RA->GetPayMoney($params);
				break;
			case 'BlackList':
				return $this->RA->GetBlackList($params);
				break;
			case 'NGword':
				return $this->RA->GetNGword($params);
				break;
			case 'textdatagroup_create':
				return $this->RA->textdatagroup_create($params);
				break;
			case 'textdatagroup_get':
				return $this->RA->textdatagroup_get($params);
				break;
			case 'textdatagroup_delete':
				return $this->RA->textdatagroup_delete($params);
				break;
			case 'textdata_create':
				return $this->RA->textdata_create($params);
				break;
			case 'textdata_get':
				return $this->RA->textdata_get($params);
				break;
			case 'textdata_update':
				return $this->RA->textdata_update($params);
				break;
			case 'textdata_delete':
				return $this->RA->textdata_delete($params);
				break;
			case 'checkSign_HMAC_SHA1':
				return $this->RA->checkSign_HMAC_SHA1($params);
				break;
			case 'checkSign_RSA_SHA1':
				return $this->RA->checkSign_RSA_SHA1($params);
				break;
			case 'first_checkedsign_pay':
				return $this->RA->first_checkedsign_pay($params);
				break;
			case 'second_checkedsign_pay':
				return $this->RA->second_checkedsign_pay($params);
				break;
    	}
    }
}
?>
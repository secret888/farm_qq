<?php
// 签名档类定义
include_once( 'OAuth.php' );
// 定义应用官方常量
define( "_YAHOO_PAY_DEBUG", false );
define( "_YAHOO_DEBUG", false );
define( "_YAHOO_PRINT", false );
define( "_YAHOO_DEBUG_PATH", "/var/www/html/log/yahoo_test.log" );
//测试地址：http://app.sb.mbga-platform.jp/social/api/restful/v2
//正式地址：http://app.mbga-platform.jp/social/api/restful/v2
define( "_BASE_URL", "http://app.sb.mbga-platform.jp/social/api/restful/v2" );
define( "_PAGE_RECORD", 10 );
// 请求验证类定义
class YahooSignatureMethod extends OAuthSignatureMethod_RSA_SHA1 {
	protected function fetch_public_cert( &$request ) {
		return <<<EOD
-----BEGIN CERTIFICATE-----
MIICOTCCAaKgAwIBAgIJAK3cE459+jV9MA0GCSqGSIb3DQEBBQUAMB4xHDAaBgNV
BAMTE3NiLm1iZ2EtcGxhdGZvcm0uanAwHhcNMTMwNzEwMDU0NDUzWhcNMTUwODI1
MDU0NDUzWjAeMRwwGgYDVQQDExNzYi5tYmdhLXBsYXRmb3JtLmpwMIGfMA0GCSqG
SIb3DQEBAQUAA4GNADCBiQKBgQDZ8xJKX1rPli72IF2L+tRV9Tk1c2kRixEEwzxR
T2bz37w/8XJQaMVxtFQMCYqquZUmHDss4JgF/prE4HGnX0j6x9MZUrt0k2VzDINm
Y+F61QJZCLqqy5MBxR9Dyu87DucPf7WsP3C1EMrfB8c29qVT7is+pMuYDowmsPql
eJ4pswIDAQABo38wfTAdBgNVHQ4EFgQUtNIqfC+B1PmcIhDmIA8+QxALZU4wTgYD
VR0jBEcwRYAUtNIqfC+B1PmcIhDmIA8+QxALZU6hIqQgMB4xHDAaBgNVBAMTE3Ni
Lm1iZ2EtcGxhdGZvcm0uanCCCQCt3BOOffo1fTAMBgNVHRMEBTADAQH/MA0GCSqG
SIb3DQEBBQUAA4GBALcHzr9wJsGR05rxPZYD1tZOrGnFJWfYyEwSDC5TD51WXLAl
7MUmHqqtP8s+2bYMSZ6y8Lc8hwYHfY1KKGrjEOVbmpR2FLYtno5b6G8GtPMiWiCL
QlshVv9rPVgcUsabuH2eRZ1Dl4G0KxJ1XIYpNaUbsoOwgGWfRZj5o8Qe6wTV
-----END CERTIFICATE-----
EOD;
}
 protected function fetch_private_cert(&$request){}
}
// 封装 AIMA api、读取 MIXI 数据
class YahooApp  {
	public $YA;
	private $me;
	private $user;
	private $app_id;
	private $feed;
	private $base;
	private $filter;
	private $api_key;
	private $secret;
    private $method;
	// 初始化 MIXI 的读取接口，将api_key和secret传入
	public function __construct( $api_key, $secret,$app_id ) {
		$this->api_key = $api_key;
		$this->secret = $secret;
		$this->app_id = $app_id;
		$this->YA = new OAuthConsumer( $api_key, $secret, null );
		$this->base = _BASE_URL."/people/%s/%s?";
		global $_REQUEST;
		$this->me = isset( $_REQUEST['opensocial_viewer_id'] ) ? $_REQUEST['opensocial_viewer_id'] : ( isset( $_REQUEST['u_id'] ) ? $_REQUEST['u_id'] : 0 );
		if( _YAHOO_DEBUG ) {
			$this->_debug( date( "Y-m-d H:i:s", time() )." me = {$this->me}\n", _YAHOO_DEBUG_PATH );
		}
		//		if( !$this->checkSign() ) {
		//			exit();
		//		}
	}
	protected function _debug( $data, $file ) {
		$fp = @fopen( $file, "ab" );
		if( $fp ) {
			$len = strlen( $data );
			@fwrite( $fp, $data, $len );
			@fclose( $fp );
		}
	}
	public function checkSign_RSA_SHA1() {
		global $_GET, $_POST;
		if( _YAHOO_PAY_DEBUG ) {
			global $_GET;
			if( !empty( $_GET ) ) {
				foreach( $_GET as $key=>$value ) {
					$this->_debug( date( "Y-m-d H:i:s", time() )." {$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}

		$request = OAuthRequest::from_request( null, null, array_merge( $_GET, $_POST ) );

		//Initialize the new signature method
		$signature_method = new YahooSignatureMethod();

		//Check the request signature
		@$signature_valid = $signature_method->check_signature( $request, null, null, $_GET["oauth_signature"] );

		if( $signature_valid == true ) {
			if( _YAHOO_PAY_DEBUG ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." Signature OK.\n", _YAHOO_DEBUG_PATH );
			}
			if( _YAHOO_PRINT ) {
				$this->_print( date( "Y-m-d H:i:s", time() )." Signature OK.\n" );
			}
			$returnArray= json_encode(array('sig_valid'=>true));
		} else {
			if( _YAHOO_PAY_DEBUG ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." Signature Fail.\n", _YAHOO_DEBUG_PATH );
			}
			if( _YAHOO_PRINT ) {
				$this->_print( date( "Y-m-d H:i:s", time() )." Signature Fail.\n" );
			}
			$returnArray =json_encode(array('sig_valid'=>false));
		}
		return $returnArray;
	}
	public function checkSign_HMAC_SHA1() {
		global $_GET, $_POST;
		if( _YAHOO_PAY_DEBUG ) {
			global $_GET;
			if( !empty( $_GET ) ) {
				foreach( $_GET as $key=>$value ) {
					$this->_debug( date( "Y-m-d H:i:s", time() )." {$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}

		$request = OAuthRequest::from_request( null, null, null );
		$token = new OAuthToken(
            $request->get_parameter('oauth_token'),
            $request->get_parameter('oauth_token_secret')
        );
        $sign_method = new  OAuthSignatureMethod_HMAC_SHA1();
        $sign = $request->get_parameter('oauth_signature');
        $signature_valid = $sign_method->check_signature($request, $this->YA, $token, $sign);
		if( $signature_valid == true ) {
			if( _YAHOO_PAY_DEBUG ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." Signature OK.\n", _YAHOO_DEBUG_PATH );
			}
			if( _YAHOO_PRINT ) {
				$this->_print( date( "Y-m-d H:i:s", time() )." Signature OK.\n" );
			}
			$returnArray= json_encode(array('sig_valid'=>true));
		} else {
			if( _YAHOO_PAY_DEBUG ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." Signature Fail.\n", _YAHOO_DEBUG_PATH );
			}
			if( _YAHOO_PRINT ) {
				$this->_print( date( "Y-m-d H:i:s", time() )." Signature Fail.\n" );
			}
			$returnArray =json_encode(array('sig_valid'=>false));
		}
		return $returnArray;
	}
	
	public function getUserId() {
		return $this->me;
	}

	/**
	 * Joins key:value pairs by inner_glue and each pair together by outer_glue
	 * @param string $inner_glue The HTTP method (GET, POST, PUT, DELETE)
	 * @param string $outer_glue Full URL of the resource to access
	 * @param array $array Associative array of query parameters
	 * @return string Urlencoded string of query parameters
	 */
	private function _implode_assoc( $inner_glue, $outer_glue, $array ) {
		$output = array();
		foreach( $array as $key => $item ) {
			$output[] = $key.$inner_glue . urlencode( $item );
		}
		return '&'.implode( $outer_glue, $output );
	}

	private function _makeRequest() {
		global $_GET;
		$params = array( 'xoauth_requestor_id' => $this->app_id );
		$baseUrl = sprintf( $this->base, $this->user, $this->feed );
		if( !empty( $this->filter ) ) {
			$curlPost= $this->filter;
			$baseUrl .= $this->_implode_assoc( '=', '&', $this->filter );
		}
		$baseUrl .= $this->_implode_assoc( '=', '&', $params );
		//echo $baseUrl;
		$request = OAuthRequest::from_consumer_and_token( $this->YA, NULL, $this->method, $baseUrl, $params );
		$request->sign_request( new OAuthSignatureMethod_HMAC_SHA1(), $this->YA, NULL );

		// Make signed OAuth request to the Contacts API server


		if( _YAHOO_DEBUG ) {
			$this->_debug( date( "Y-m-d H:i:s", time() )." base URL : {$baseUrl}.\n", _YAHOO_DEBUG_PATH );
		}
		if( _YAHOO_PRINT ) {
			$this->_print( date( "Y-m-d H:i:s", time() )." base URL : {$baseUrl}.\n" );
		}
		//echo $baseUrl;
		//$this->_debug( $baseUrl, _YAHOO_DEBUG_PATH );
		$curl = curl_init( $baseUrl );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FAILONERROR, false );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_ENCODING, 'gzip' );
		switch ($this->method) {
			case 'POST':
				curl_setopt($curl,  CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
				break;
			case 'DELETE':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
				break;
			case 'PUT':
				if (!empty($curlPost)) {
				$file = tempnam(sys_get_temp_dir(), 'iron-money-');
				file_put_contents($file, $curlPost);
				}
				$file_size = filesize($file);
				curl_setopt($curl, CURLOPT_PUT, true);
				curl_setopt($curl, CURLOPT_INFILE, fopen($file, 'r'));
				curl_setopt($curl, CURLOPT_INFILESIZE, $file_size);
				break;
		}
		$auth_header = $request->to_header();
		//print_r($auth_header);
		if( $auth_header ) {
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array( $auth_header ) );
		}

		$response = curl_exec( $curl );
       // print_r($response);
		if( !$response ) {
			$response = curl_error( $curl );
			exit( "has error within OAuth..." );
		}
		curl_close( $curl );

		$return = json_decode( $response, true );
		//print_r($return);
		//return $return['entry'];
		return $return;
	}

	// 读取自己的信息
	public function GetUserInfo( $params )
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'] ;
		$this->feed = "@self";
		$this->method='GET';
		$this->filter = array(fields=>$params['fields']);
		$returnArray = $this->_makeRequest();
		if( _YAHOO_DEBUG ) {
			if( !empty( $returnArray ) ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." user info : \n", _YAHOO_DEBUG_PATH );
				foreach( $returnArray as $key=>$value ) {
					$this->_debug( "{$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user info :";
			$this->_print( $returnArray );
		}
		return $returnArray;
	}

	// 读取安装了应用的好友id列表
	// 返回格式：?Array ( [0] => 250356491 [1] => 263628433 )
	public function GetFriendsAppInfo( $params )
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'] ;
		$this->feed = "@friends";
		$this->method='GET';
		$this->filter = array( "count" => $params['count'], "filterBy" => $params['filterBy'],fields=>$params['fields'] );	// 每页记录数最多为1K
		$returnArray = $this->_makeRequest();
		$hasApp = array();
		if( !empty( $returnArray['entry'] ) ) {
			foreach( $returnArray['entry'] as $value ) {
				$value['id'] = explode( ':', $value['id'] );
				$hasApp[] = $value['id'][1];
			}
		}

		if( _YAHOO_DEBUG ) {
			$has = implode( ',', $hasApp );
			$this->_debug( date( "Y-m-d H:i:s", time() )." user has apps : {$has}.\n", _YAHOO_DEBUG_PATH );
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user has apps :";
			$this->_print( $hasApp );
		}

		return $hasApp;
	}

	// 读取好友信息
	// 返回格式：?Array ( [0] => Array ( [id] => 276832268 [name] => 王莉 [headurl] => http://hdn511.xnimg.cn/photos/hdn511/20090526/1000/head_70wf_529e204234.jpg [tinyurl] => http://hdn511.xnimg.cn/photos/hdn511/20090526/1000/tiny_3Hq4_529e204234.jpg ) [1] => Array ( [id] => 262538398 [name] => 上官云阳 [headurl] => http://hdn511.xnimg.cn/photos/hdn511/20090422/1010/head_OcI8_94485i204234.jpg [tinyurl] => http://hdn511.xnimg.cn/photos/hdn511/20090422/1010/tiny_PJx7_94485i204234.jpg ) )
	public function GetFriendsInfo( $params )
	{
		//$this->user='17750459';
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'] ;
		$this->feed = "@friends";
		$this->method='GET';
		$this->filter = array( fields=>$params['fields'],"count" => $params['count'], "startIndex" => $params['count'] * ( $params['page'] - 1 ) );
		$returnArray = $this->_makeRequest();
		$users = array();
		if( !empty( $returnArray['entry'] ) ) {
			foreach( $returnArray['entry'] as $key=>$value ) {
				if(!is_numeric( $value['id'] )) {
					$sureId = explode( ":", $value['id'] );
					if( count( $sureId ) == 2 ) {
						$value['id'] = $sureId[1];
					}
				}
				$info = array(
				'id' => $value['id'],
				'name' => $value['nickname'],
				'gender' => $value['gender'],
				'thumbnailUrl' => $value['thumbnailUrl'],
				);
				$users[] = $info;
			}

			if( _YAHOO_DEBUG ) {
				$self = implode( ',', $users );
				$this->_debug( date( "Y-m-d H:i:s", time() )." friends info : {$self}.\n", _YAHOO_DEBUG_PATH );
			}
			if( _YAHOO_PRINT ) {
				print date( "Y-m-d H:i:s", time() )." friends info :";
				$this->_print( $users );
			}
		}

		return $users;
	}
	public function second_checkedsign_pay($params)
	{
		$body = json_encode(
		array(
		order_id => $params['order_id'],
		response_code => 'OK',
		amount=>$params['amount']
		));
		return $this->checksign_pay($body);

	}
	public function first_checkedsign_pay($params)
	{
		$body = json_encode(
		array(
		order_id => $params['order_id'],
		response_code => 'OK'
		));
		return $this->checksign_pay($body);
	}
	public function checksign_pay($body)
	{
		$param = array(
		timestamp => time(),
		nonce => md5(microtime()),
		consumer_key => $this->api_key,
		body_hash => rtrim(base64_encode(sha1($body, true)), '=')
		);

		$params = array();
		ksort($param);
		foreach ($param as $key => $value) {
			$out = urlencode($key) . '=' . urlencode($value);
			array_push($params, $out);
		}

		$base_string = implode('&', $params);
		$sign = rtrim(base64_encode(hash_hmac('sha1', $base_string, $this->secret, true)), '=');

		header("Content-type: application/json");
		header("X-MBGA-PAYMENT-SIGNATURE: ".$base_string."&signature=".urlencode($sign));
		return  $body;
	}
	public function GetPayMoney($params)
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'];
		$this->feed = "@self";
		$this->method='GET';
		$this->filter = array();
		$this->base = _BASE_URL."/payment/%s/%s/@app/".$params['paymentId']."?";
		$returnArray = $this->_makeRequest();
		//print_r($returnArray);
		if( _YAHOO_PAY_DEBUG ) {
			if( !empty( $returnArray ) ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." user info : \n", _YAHOO_DEBUG_PATH );
				foreach( $returnArray as $key=>$value ) {
					$this->_debug( "{$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user info :";
			$this->_print( $returnArray );
		}
		return $returnArray;

	}
	public function GetBlackList($params)
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'];
		$this->feed = "@self";
		$this->method='GET';
		$this->filter = array();
		$this->base = _BASE_URL."/blacklist/%s/@all?";
		$blacklist = array();
		$returnArray = $this->_makeRequest();
		print_r($returnArray);
		if( !empty( $returnArray['entry'] ) ) {
			foreach( $returnArray['entry'] as $key=>$value ) {
				if(!is_numeric( $value['targetId'] )) {
					$sureId = explode( ":", $value['targetId'] );
					if( count( $sureId ) == 2 ) {
						$value['targetId'] = $sureId[1];
					}
				}
				$blacklist[]=$value['targetId'];
			}
		}
		if( _YAHOO_DEBUG ) {
			if( !empty( $returnArray ) ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." user info : \n", _YAHOO_DEBUG_PATH );
				foreach( $returnArray as $key=>$value ) {
					$this->_debug( "{$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user info :";
			$this->_print( $returnArray );
		}
		return $blacklist;

	}
	public function GetNGword( $params )
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'];
		$this->feed = "@self";
		$this->method='POST';
		$this->filter =json_encode(array('data'=>$params['word']));
		$this->base = _BASE_URL."/ngword?_method=check";
		$returnArray = $this->Post_makeRequest();
		if( _YAHOO_DEBUG ) {
			if( !empty( $returnArray ) ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." user info : \n", _YAHOO_DEBUG_PATH );
				foreach( $returnArray as $key=>$value ) {
					$this->_debug( "{$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user info :";
			$this->_print( $returnArray );
		}
		return $returnArray;
	}
	public function textdatagroup_create( $params)
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'];
		$this->feed = "@self";
		$this->method='POST';
		$this->filter =json_encode(array('name'=>$params['groupname']));
		$this->base = _BASE_URL."/textdata/@app/@all?";
		$returnArray = $this->Post_makeRequest();
		if( _YAHOO_DEBUG ) {
			if( !empty( $returnArray ) ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." user info : \n", _YAHOO_DEBUG_PATH );
				foreach( $returnArray as $key=>$value ) {
					$this->_debug( "{$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user info :";
			$this->_print( $returnArray );
		}
		return $returnArray;
	}
	public function textdatagroup_get( $params)
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'];
		$this->feed = "@self";
		$this->method='GET';
		$this->filter =array("count" => $params['count'], "startIndex" => $params['count'] * ( $params['page'] - 1 ));
		if (empty($groupname)) {
			$this->base = _BASE_URL."/textdata/@app/@all?";
		}else 
		{
			$this->base = _BASE_URL."/textdata/@app/".$params['groupname']."/@self?";
		}
		$returnArray = $this->_makeRequest();
		if( _YAHOO_DEBUG ) {
			if( !empty( $returnArray ) ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." user info : \n", _YAHOO_DEBUG_PATH );
				foreach( $returnArray as $key=>$value ) {
					$this->_debug( "{$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user info :";
			$this->_print( $returnArray );
		}
		return $returnArray;
	}
	public function textdatagroup_delete( $params)
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'];
		$this->feed = "@self";
		$this->method='DELETE';
		$this->filter =array();
		$this->base = _BASE_URL."/textdata/@app/".$params['groupname']."/@self?";
		$returnArray = $this->_makeRequest();
		if( _YAHOO_DEBUG ) {
			if( !empty( $returnArray ) ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." user info : \n", _YAHOO_DEBUG_PATH );
				foreach( $returnArray as $key=>$value ) {
					$this->_debug( "{$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user info :";
			$this->_print( $returnArray );
		}
		return $returnArray;
	}
	public function textdata_create($params)
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'];
		$this->feed = "@self";
		$this->method='POST';
		$this->filter =json_encode(array('data'=>$params['textdata'],'writerId'=>$params['writerId'],'ownerId'=>$params['ownerId']));
		$this->base = _BASE_URL."/textdata/@app/".$params['groupname']."/@all?";
		$returnArray = $this->Post_makeRequest();
		if( _YAHOO_DEBUG ) {
			if( !empty( $returnArray ) ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." user info : \n", _YAHOO_DEBUG_PATH );
				foreach( $returnArray as $key=>$value ) {
					$this->_debug( "{$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user info :";
			$this->_print( $returnArray );
		}
		return $returnArray;
	}
	public function textdata_get( $params)
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'];
		$this->feed = "@self";
		$this->method='GET';
		//$this->filter =array("fields"=>"data,writerId,status,id,ownerId,published,created,parentId,groupName","filterBy"=>"writerId","filterOp"=>"=","filterValue"=>$uid);
		$this->filter =array("fields"=>"data,writerId,status,id,ownerId,published,created,parentId,groupName","count" => $params['count'], "startIndex" => $params['count'] * ( $params['page'] - 1 ));
		$this->base = _BASE_URL."/textdata/@app/".$params['groupname']."/@all/".$params['textdata_id']."?";
		$returnArray = $this->_makeRequest();
		if( _YAHOO_DEBUG ) {
			if( !empty( $returnArray ) ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." user info : \n", _YAHOO_DEBUG_PATH );
				foreach( $returnArray as $key=>$value ) {
					$this->_debug( "{$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user info :";
			$this->_print( $returnArray );
		}
		return $returnArray;
	}
	public function textdata_update( $params)
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'];
		$this->feed = "@self";
		$this->method='PUT';
		$this->filter =json_encode(array('data'=>$params['textdata']));
		$this->base = _BASE_URL."/textdata/@app/".$params['groupname']."/@all/".$params['parentId']."?";
		$returnArray = $this->Post_makeRequest();
		if( _YAHOO_DEBUG ) {
			if( !empty( $returnArray ) ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." user info : \n", _YAHOO_DEBUG_PATH );
				foreach( $returnArray as $key=>$value ) {
					$this->_debug( "{$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user info :";
			$this->_print( $returnArray );
		}
		return $returnArray;
	}
	public function textdata_delete( $params)
	{
		$this->user = empty( $params['uid'] ) ? $this->me : $params['uid'];
		$this->feed = "@self";
		$this->method='DELETE';
		$this->filter =array();
		$this->base = _BASE_URL."/textdata/@app/".$params['groupname']."/@all/".$params['parentId']."?";
		$returnArray = $this->_makeRequest();
		if( _YAHOO_DEBUG ) {
			if( !empty( $returnArray ) ) {
				$this->_debug( date( "Y-m-d H:i:s", time() )." user info : \n", _YAHOO_DEBUG_PATH );
				foreach( $returnArray as $key=>$value ) {
					$this->_debug( "{$key} = {$value}\n", _YAHOO_DEBUG_PATH );
				}
			}
		}
		if( _YAHOO_PRINT ) {
			print date( "Y-m-d H:i:s", time() )." user info :";
			$this->_print( $returnArray );
		}
		return $returnArray;
	}
	private function Post_makeRequest() {
		global $_GET,$_SERVER;
		$params = array( 'xoauth_requestor_id' => $this->app_id );
		$baseUrl = sprintf( $this->base, $this->user, $this->feed );
		$curlPost= $this->filter;
		$baseUrl .= $this->_implode_assoc( '=', '&', $params );
		$request = OAuthRequest::from_consumer_and_token( $this->YA, NULL, $this->method, $baseUrl, $params );
		$request->sign_request( new OAuthSignatureMethod_HMAC_SHA1(), $this->YA, NULL );

		// Make signed OAuth request to the Contacts API server


		if( _YAHOO_DEBUG ) {
			$this->_debug( date( "Y-m-d H:i:s", time() )." base URL : {$baseUrl}.\n", _YAHOO_DEBUG_PATH );
		}
		if( _YAHOO_PRINT ) {
			$this->_print( date( "Y-m-d H:i:s", time() )." base URL : {$baseUrl}.\n" );
		}
		//echo $baseUrl;
		//$this->_debug( $baseUrl, _YAHOO_DEBUG_PATH );
		$curl = curl_init( $baseUrl );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FAILONERROR, false );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_ENCODING, 'gzip' );
		if($this->method=="PUT")
		{
			if (!empty($curlPost)) {
				$file = tempnam(sys_get_temp_dir(), 'iron-money-');
				file_put_contents($file, $curlPost);
			}
			$file_size = filesize($file);
			curl_setopt($curl, CURLOPT_PUT, true);
			curl_setopt($curl, CURLOPT_INFILE, fopen($file, 'r'));
			curl_setopt($curl, CURLOPT_INFILESIZE, $file_size);
		}
		if($this->method=="POST")
		{
			curl_setopt($curl,  CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
		}

		$auth_header = $request->to_header();
		//print_r($auth_header);
		if( $auth_header ) {
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array( $auth_header ) );
		}

		$response = curl_exec( $curl );
       // print_r($response);
		if( !$response ) {
			$response = curl_error( $curl );
			if( !$response )
			{
				return "1";
			}else 
			{
				exit( "has error within OAuth..." );
			}
		}
		curl_close( $curl );

		$return = json_decode( $response, true );
		//print_r($return);
		//return $return['entry'];
		return $return;
	}
}
?>

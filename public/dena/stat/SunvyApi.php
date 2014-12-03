<?php
/**
 * filename : SunvyApi.php
 * author : Simba
 * version : 0.1
 */
require_once( "SunvyAuthConfig.php" );

class SunvyApi {
	var $mediaId;
	var $mediaKey;
	var $mediaSecret;
    var $method;
    var $api;
	var $base;
	var $filter;
	
	function SunvyApi( $mediaId, $mediaKey, $mediaSecret ) {
		global $_sunvyTestModal;

		if( _SUNVYAUTHMETHOD == "" ||  _SUNVYAPI == "" ||  _SUNVYTESTAPI == "" ) {
			die( "Please make the correct configurations of authentication in file SunvyAuthConfig.php." );
		}
		
		$this->mediaId = $mediaId;
		$this->mediaKey = $mediaKey;
		$this->mediaSecret = $mediaSecret;
		$this->method = _SUNVYAUTHMETHOD;
		$this->api = $_sunvyTestModal ? _SUNVYTESTAPI : _SUNVYAPI;
	}
	
	function _implode_assoc( $inner_glue, $outer_glue, $array ) {
		$output = array();
		foreach( $array as $key => $item ) {
			$output[] = $key.$inner_glue.urlencode( $item );
		}
		return '&'.implode( $outer_glue, $output );
	}
	
	function _makeRequest( $debug = false ) {
		$baseUrl = $this->base;
		if( $debug ) {
			$this->filter['logPostData'] = 'true';		// 调试模式
		}
		
		if( !empty( $this->filter ) ) {
			$curlPost = $this->_implode_assoc( '=', '&', $this->filter );
		}
		$curl = curl_init( $baseUrl );
		curl_setopt($curl,  CURLOPT_CONNECTTIMEOUT,5);
		curl_setopt($curl,  CURLOPT_TIMEOUT,10);
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FAILONERROR, false );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt( $curl, CURLOPT_USERPWD, $this->mediaKey.":".$this->mediaSecret );
		curl_setopt( $curl, CURLOPT_ENCODING, 'gzip' );
		curl_setopt( $curl, CURLOPT_POST, 1 );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $curlPost );

		$response = curl_exec( $curl );
		if( !$response ) {
			$response = curl_error( $curl );
			exit( "has error within STAT_API..." );
		}
		curl_close( $curl );
		return $response;
	}
}
?>
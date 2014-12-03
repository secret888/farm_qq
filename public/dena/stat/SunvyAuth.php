<?php
/**
 * filename : SunvyAuth.php
 * author : Simba
 * version : 0.1
 */
require_once( "SunvyAuthConfig.php" );

class SunvyAuth {
	var $mediaId;
	var $mediaKey;
	var $mediaSecret;
    var $method;
	var $auth;
	
	function SunvyAuth( $mediaId, $mediaKey, $mediaSecret ) {
		if( _SUNVYAUTHMETHOD == "" ) {
			die( "Please make the correct configurations of authentication in file SunvyAuthConfig.php." );
		}
		
		$this->mediaId = $mediaId;
		$this->mediaKey = $mediaKey;
		$this->mediaSecret = $mediaSecret;
		$this->method = _SUNVYAUTHMETHOD;

		if( _SUNVYAUTHMETHOD == "BOTH" ) {
			global $_REQUEST;
			$this->auth = $_REQUEST;
		} elseif( _SUNVYAUTHMETHOD == "POST" ) {
			global $_POST;
			$this->auth = $_POST;
		} else {
			global $_GET;
			$this->auth = $_GET;
		}
		$this->authCheck();
	}
	
	function authCheck() {
		if( _SUNVYDEBUG ) {
			if( !empty( $this->auth ) ) {
				$this->_debug( "Request: ".date( "Y-m-d H:i:s", time() )."\n", _SUNVYDEBUGPATH );
				foreach( $this->auth as $key=>$value ) {
					$this->_debug( "{$key}:{$value}\n", _SUNVYDEBUGPATH );
				}
				$this->_debug( "\n\n", _SUNVYDEBUGPATH );
			}
		}

		if( $this->auth['mediaId'] != $this->mediaId ) {
			$this->fail( "mediaId" );
		}
		if( $this->auth['mediaKey'] != $this->mediaKey ) {
			$this->fail( "mediaKey" );
		}
		if( $this->auth['mediaSecret'] != $this->mediaSecret ) {
			$this->fail( "mediaSecret" );
		}
	}
	
	function missFunction( $key ) {
		die( "Please define function {$key} at first." );
	}

	function missParam( $param = "" ) {
		header( "HTTP/1.0 400 Bad Request" );
		print json_encode( array( "error" => "Parameter {$param} is missed!" ) );
		exit();
	}
	
	function fail( $key ) {
		header( "HTTP/1.0 401 Unauthorized" );
		print json_encode( array( "error" => "{$key} is error!" ) );
		exit();
	}
	
	function missInfo() {
		header( "HTTP/1.0 404 Not Found" );
		print json_encode( array( "error" => "Infomations is not found!" ) );
		exit();
	}
	
	function ok( $info ) {
		if( _SUNVYDEBUG ) {
			if( !empty( $info ) ) {
				$this->_debug( "Response: ".date( "Y-m-d H:i:s", time() )."\n", _SUNVYDEBUGPATH );
				foreach( $info as $key=>$value ) {
					$this->_debug( "{$key}:{$value}\n", _SUNVYDEBUGPATH );
				}
				$this->_debug( "\n\n", _SUNVYDEBUGPATH );
			}
		}
		header( "HTTP/1.1 200 OK" );
		print json_encode( $info );
		exit();
	}
	
	function _debug( $data, $file ) {
		$fp = @fopen( $file, "ab" );
		if( $fp ) {
			$len = strlen( $data );
			@fwrite( $fp, $data, $len );
			@fclose( $fp );
		}
	}
}
?>
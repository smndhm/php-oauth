<?php

	/**
	 * Class ApiTwitter
	 * Twitter Api Class Extends
	 *
	 * @author	Cyril Vermande (cyril@cyrilwebdesign.com)
	 * @version	0.1
	 */
	
	if (!class_exists("Api")) {
		require_once("class.Api.php");
	}
	
	class ApiTwitter extends Api {
		
		/**
		 * Constructor
		 *
		 * @param	array	$config	config for API : client_id, client_secret, redirect_uri
		 */
		public function __construct($config=array()) {
			parent::setUrls(array(
				"api"           => "https://api.twitter.com",
				"authorization" => "https://api.twitter.com/oauth/authenticate",
				"access_token"  => "https://api.twitter.com/oauth/access_token"
			));
			
			$this->api_url = "https://api.twitter.com";
			$this->authorization_url = "https://api.twitter.com/oauth/authenticate";
			$this->access_token_url = "https://api.twitter.com/oauth/access_token";
			$this->request_token_url = "https://api.twitter.com/oauth/request_token";
			
			parent::__construct($config);
		}

		/**
		 * Get login URL
		 *
		 * @param	array 		$params	Parameters to add to the url -> perms
		 *
		 * @return	string		URL to grant authorization
		 *
		 * @throws	Exception	if redirect_uri is empty
		 */
		public function getLoginUrl($params=array()) {
			if (empty($this->redirect_uri)) {
				throw new Exception("redirect_uri undefined");
			}
			$params['app_id']       = $this->client_id;
			$params['redirect_uri'] = $this->redirect_uri;
			return parent::getLoginUrl($params);
		}

		/**
		 * Get token params
		 *
		 * @param	array 		$params	Parameters to add to the url -> code
		 *
		 * @return	json 		token params
		 *
		 * @throws	Exception	if oauth_token or oauth_verifier are not in the parameters or empty
		 */
		public function getTokenParams($params=array()) {
			if (empty($params['oauth_token'])) {
				throw new Exception("oauth_token undefined");
			}
			if (empty($params['oauth_verifier'])) {
				throw new Exception("oauth_verifier undefined");
			}
			
			$params['oauth_consumer_key'] = $this->client_id;
			$params['oauth_nonce'] = md5(time());
			$params['oauth_signature_method'] = "HMAC-SHA1";
			$params['oauth_timestamp'] = time();
			$params['oauth_version'] = "1.0";
			$params['oauth_signature'] = $this->getRequestSignature("POST", $this->access_token_url, $params);
			$token_file = $this->getTokenFile($params, "POST");
			$token_params = array();
			parse_str($token_file, $token_params);
			return (object) $token_params;
		}
		
		public function requestToken($params=array()) {
			$params['oauth_callback'] = urlencode($this->redirect_uri);
			$params['oauth_consumer_key'] = $this->client_id;
			$params['oauth_nonce'] = md5(time());
			$params['oauth_signature_method'] = "HMAC-SHA1";
			$params['oauth_timestamp'] = time();
			$params['oauth_version'] = "1.0";
			$params['oauth_signature'] = $this->getRequestSignature("POST", $this->request_token_url, $params);
			
			$token_file = $this->__curl($this->request_token_url, "POST", $params);
			$token_params = array();
			parse_str($token_file, $token_params);
			return (object) $token_params;
		}
		
		/**
		 * Sign request
		 *
		 * @param	string		$method	Method
		 * @param	string		$url	URL
		 * @param	array		$params	Parameters
		 *
		 * @return	string		signature
		 */
		private function getRequestSignature($method='GET', $url='', $params=array()){
			if(isset($params['oauth_signature'])) unset($params['oauth_signature']);

			$parse = (object) parse_url($url);
			$url = $parse->scheme."://".$parse->host.$parse->path;
			if(isset($parse->query)){
				parse_str($parse->query, $query);
				foreach($query as $key=>$value){
					$params[$key] = $value;
				}
			}

			$r = array();
			ksort($params);
			foreach($params as $key=>$value){
				$r[] = $key."=".rawurlencode($value);
			}

			$base_string = strtoupper($method)."&".rawurlencode($url)."&".rawurlencode(implode("&", $r));
			$key = rawurlencode($this->client_secret)."&".rawurlencode($this->access_token_secret);

			return base64_encode(hash_hmac("sha1", $base_string, $key, true));
		}
		
		/**
		 * Build authorisation header
		 *
		 * @param	array		$params	Parameters
		 *
		 * @return	string		authorization
		 */
		private function getRequestAuthorizationHeader($params){
			$authorization = "Authorization: OAuth ";
			$values = array();
			foreach($params as $key=>$value){
				if(substr($key, 0, 6) == "oauth_") $values[] = $key."=\"".rawurlencode($value)."\"";
			}
			$authorization .= implode(", ", $values);
			return $authorization;
		}
		
		/**
		 * API call
		 *
		 * @param	string	$path	API Called path
		 * @param	string	$method	Used method for the call
		 * @param	array	$params	Added parameters for the call
		 * @param	array	$header	Used header for the call (POST)
		 *
		 * @return	string	URL
		 *
		 * @throws	Exception	if api_url or $path are empty
		 */
		public function api($path='',$method='GET',$params=array(),$header=array('Content-Type: application/x-www-form-urlencoded')){
			if (empty($this->api_url)) {
				throw new Exception('Api URL undefined');
			}
			if (empty($path)) {
				throw new Exception('Api path undefined');
			}
			if (strpos($path, $this->api_url)===0) {
				$path = substr($path, strlen($this->api_url));
			}
			
			$params['oauth_consumer_key'] = $this->client_id;
			$params['oauth_token'] = $this->access_token;
			$params['oauth_nonce'] = md5(time());
			$params['oauth_signature_method'] = "HMAC-SHA1";
			$params['oauth_timestamp'] = time();
			$params['oauth_version'] = "1.0";
			$params['oauth_signature'] = $this->getRequestSignature($method, $this->api_url.$path, $params);
			
			$header[] = $this->getRequestAuthorizationHeader($params);
			$result = $this->__curl($this->api_url.$path, $method, $params, $header);
			
			return json_decode($result);
		}
		
	}

?>
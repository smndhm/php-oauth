<?php 

	/**
	 *  Class Api
	 *  Class structure to connect to diffents APIs through Extends Class
	 *
	 * @author	Simon Duhem @DuMe
	 * @version	0.1
	 */

	class Api {
		
		private $api_url;
		private $access_token_url;
		private $authorization_url;
		
		public $client_id;
		public $client_secret;
		public $redirect_uri;
		public $access_token;
		public $access_token_secret;
		
		/**
		 * Constructor
		 *
		 * @param 	array	$config	config for API : client_id, client_secret, redirect_uri
		 */
		public function __construct($config=array()) {
			if (isset($config['client_id']))     { $this->client_id     = $config['client_id'];     }
			if (isset($config['client_secret'])) { $this->client_secret = $config['client_secret']; }
			if (isset($config['redirect_uri']))  { $this->redirect_uri  = $config['redirect_uri'];  }
		}
		
		/**
		 * Set URLs used by the Extends Class API
		 *
		 * @param	array	$urls	URLs used by the API
		 */
		protected function setUrls($urls=array()) {
			if (isset($urls['api']))           { $this->api_url           = $urls['api'];           }
			if (isset($urls['authorization'])) { $this->authorization_url = $urls['authorization']; }
			if (isset($urls['access_token']))  { $this->access_token_url  = $urls['access_token'];  }
		}

		/**
		 * Get login URL
		 *
		 * @param	array		$params Parameters to add to the url
		 *
		 * @return	string		URL to grant authorization
		 *
		 * @throws	Exception	if client_id is not set
		 */
		public function getLoginUrl($params=array()) {
			if (!isset($this->client_id)) {
				throw new Exception('Client ID undefined');
			}
			return $this->__buildUrl($this->authorization_url, $params);
		}
		
		/**
		 * Get token file content
		 *
		 * @param	array	$params	Parameters to add to the url
		 * @param	string	$method	Used method for the call
		 * @param	array	$header	Used header for the call (POST)
		 *
		 * @return	string	token file content
		 */
		protected function getTokenFile($params=array(),$method='GET',$header=array('Content-Type: application/x-www-form-urlencoded')) {
			$token_url = $this->__buildUrl($this->access_token_url, $params);
			return $this->__curl($token_url, $method, $params, $header);
		}

		/**
		 * Set token
		 *
		 * @param		string		$access_token
		 *
		 * @throws		Exception	if $access_token is empty
		 */
		 public function setToken($access_token='') {
		 	if (empty($access_token)) {
		 		throw new Exception('Access token undefined');
		 	}
		 	$this->access_token = $access_token;
		 }

		/**
		 * Set token secret
		 *
		 * @param		string		$access_token_secret
		 *
		 * @throws		Exception	if $access_token_secret is empty
		 */
		 public function setTokenSecret($access_token_secret='') {
		 	if (empty($access_token_secret)) {
		 		throw new Exception('Access token secret undefined');
		 	}
		 	$this->access_token_secret = $access_token_secret;
		 }

		/**
		 * Build URL
		 *
		 * @param	string	$base_url	Base URL
		 * @param	array	$params		Parameters to add to the base url
		 *
		 * @return	string	URL
		 */
		private function __buildUrl($base_url='',$params=array()) {
			return $base_url . '?' . http_build_query($params, null, '&');
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
		public function api($path='',$method='GET',$params=array(),$header=array('Content-Type: application/x-www-form-urlencoded')) {
			if (empty($this->api_url)) {
				throw new Exception('Api URL undefined');
			}
			if (empty($path)) {
				throw new Exception('Api path undefined');
			}
			if (strpos($path, $this->api_url)===0) {
				$path = substr($path, strlen($this->api_url));
			}
			return json_decode($this->__curl($this->api_url.$path.(isset($this->access_token)&&!empty($this->access_token)?(strpos($path,'?')!==false?'&':'?')."access_token={$this->access_token}":''),$method,$params,$header));
		}
		
		/**
		 * Get URL content
		 *
		 * @param	string	$url	content url to get
		 * @param	string	$method	Used method for the call
		 * @param	array	$params	Added parameters for the call
		 * @param	array	$header	Used header for the call (POST)
		 *
		 * @return	string	url content
		 */
		public function __curl($url,$method='GET',$params=array(),$header=array('Content-Type: application/x-www-form-urlencoded')) {
			$c = curl_init();
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($c, CURLOPT_URL, $url);
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
			if ($method=='POST') {
				curl_setopt($c, CURLOPT_POST, true);
				curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($params, null,'&'));
			}
			if (!empty($header)) {
				curl_setopt($c, CURLOPT_HTTPHEADER, $header);
			}
			$content = curl_exec($c);
			//$status  = curl_getinfo($c, CURLINFO_HTTP_CODE);
			curl_close($c);
			return $content;
		}
		
	}

?>
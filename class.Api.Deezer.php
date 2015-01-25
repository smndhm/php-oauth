<?php

	/**
	 * Class ApiDeezer
	 * Deezer Api Class Extends
	 * @link	http://developers.deezer.com/api	Deezer for developers
	 *
	 * @author	Simon Duhem @DuMe
	 * @version	0.1
	 */
	
	if (!class_exists("Api")) {
		require_once("class.Api.php");
	}
	
	class ApiDeezer extends Api {
		
		/**
		 * Constructor
		 *
		 * @param	array	$config	config for API : client_id, client_secret, redirect_uri
		 */
		public function __construct($config=array()) {
			parent::setUrls(array(
				"api"           => "http://api.deezer.com/",
				"authorization" => "https://connect.deezer.com/oauth/auth.php",
				"access_token"  => "https://connect.deezer.com/oauth/access_token.php",
			));
			parent::__construct($config);
		}

		/**
		 * Get login URL
		 *
		 * @see		http://developers.deezer.com/api/oauth
		 * @see		http://developers.deezer.com/api/permissions
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
		 * @see		http://developers.deezer.com/api/oauth
		 *
		 * @param	array		$params	Parameters to add to the url -> code
		 *
		 * @return	json		token params
		 *
		 * @throws	Exception	if code is not in the parameters
		 */
		public function getTokenParams($params=array()) {
			if (!isset($params['code'])) {
				throw new Exception("code undefined");
			}
			$params['app_id'] = $this->client_id;
			$params['secret'] = $this->client_secret;
			$token_file = $this->getTokenFile($params);
			$token_params = array();
			parse_str($token_file, $token_params);
			return json_decode(json_encode($token_params));
		}
		
	}

?>
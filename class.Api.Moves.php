<?php

	/**
	 * ApiMoves Class
	 * Moves Api Class Extends
	 * @link	https://dev.moves-app.com/docs/overview	Moves for Developers
	 *
	 * @author	Simon Duhem @DuMe
	 * @version	0.1
	 * @since	01-05-2014
	 *
	 */
	
	if (!class_exists("Api")) {
		require_once("class.Api.php");
	}
	
	class ApiMoves extends Api {
		
		/**
		 * Constructor
		 *
		 * @param	array	$config	config for API : client_id, client_secret, redirect_uri
		 *
		 */
		public function __construct($config=array()) {
			parent::setUrls(array(
				"api"           => "https://api.moves-app.com/api/1.1/",
				"authorization" => "https://api.moves-app.com/oauth/v1/authorize",
				"access_token"  => "https://api.moves-app.com/oauth/v1/access_token",
			));
			parent::__construct($config);
		}
		
		/**
		 * Get login URL
		 *
		 * @see	https://dev.moves-app.com/docs/authentication
		 *
		 * @param	array	$params	Parameters to add to the url -> scope
		 *
		 * @return 	string	URL to grant authorization
		 *
		 */
		public function getLoginUrl($params=array()) {
			if (!empty($this->redirect_uri)) {
				$params['redirect_uri'] = $this->redirect_uri;
			}
			$params['client_id'] = $this->client_id;
			$params['response_type'] = "code";
			return parent::getLoginUrl($params);
		}
		
		/**
		 * Get token params
		 *
		 * @see	https://dev.moves-app.com/docs/authentication
		 *
		 * @param	array	$params	Parameters to add to the url -> code
		 *
		 * @return 	json	token params
		 *
		 */
		public function getTokenParams($params=array()) { //code
			if (!isset($params['code'])) {
				throw new Exception("code undefined");
			}
			if (!empty($this->redirect_uri)) {
				$params['redirect_uri'] = $this->redirect_uri;
			}
			$params['grant_type']    = "authorization_code";
			$params['client_id']     = $this->client_id;
			$params['client_secret'] = $this->client_secret;
			$token_file = $this->getTokenFile($params, "POST");
			return json_decode($token_file);
		}
		
	}

?>
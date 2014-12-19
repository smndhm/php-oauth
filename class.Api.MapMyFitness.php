<?php

	/**
	 * ApiMapMyFitness Class
	 * MapMyFitness Api Class Extends
	 * @link	https://www.mapmyapi.com/docs	MapMyFitness
	 *
	 * @author	Simon Duhem @DuMe
	 * @version	0.1
	 * @since	01-05-2014
	 *
	 */
	
	if (!class_exists("Api")) {
		require_once("class.Api.php");
	}
	
	class ApiMapMyFitness extends Api {
		
		/**
		 * Constructor
		 *
		 * @param	array	$config	config for API : client_id, client_secret, redirect_uri
		 *
		 */
		public function __construct($config=array()) {
			parent::setUrls(array(
				"api"           => "https://oauth2-api.mapmyapi.com/v7.0/",
				"authorization" => "https://www.mapmyfitness.com/v7.0/oauth2/authorize/",
				"access_token"  => "https://oauth2-api.mapmyapi.com/v7.0/oauth2/access_token/",
			));
			parent::__construct($config);
		}
		
		/**
		 * Get login URL
		 *
		 * @see	https://www.mapmyapi.com/docs/OAuth_2_Intro
		 *
		 * @param	array	$params	Parameters to add to the url
		 *
		 * @return 	string	URL to grant authorization
		 *
		 */
		public function getLoginUrl($params=array()) {
			if (empty($this->redirect_uri)) {
				throw new Exception("redirect_uri undefined");
			}
			$params['redirect_uri']  = $this->redirect_uri;
			$params['client_id']     = $this->client_id;
			$params['response_type'] = "code";
			return parent::getLoginUrl($params);
		}
		
		/**
		 * Get token params
		 *
		 * @see	https://www.mapmyapi.com/docs/OAuth_2_Intro
		 *
		 * @param	array	$params	Parameters to add to the url -> code
		 *
		 * @return 	json	token params
		 *
		 */
		public function getTokenParams($params=array()) {
			if (!isset($params['code'])) {
				throw new Exception("code undefined");
			}
			if (empty($this->redirect_uri)) {
				throw new Exception("redirect_uri undefined");
			}
			$params['redirect_uri']  = $this->redirect_uri;
			$params['grant_type']    = "authorization_code";
			$params['client_id']     = $this->client_id;
			$params['client_secret'] = $this->client_secret;
			$token_file = $this->getTokenFile($params, "POST", array("Api-Key: {$this->client_id}"));
			return json_decode($token_file);
		}
		
		/**
		 * API call
		 * Extra header
		 *
		 * @param	string	$path	API Called path
		 *
		 * @return	string	URL
		 *
		 */
		public function api($path="",$method="GET",$params=array(),$header=array('Content-Type: application/x-www-form-urlencoded')) {
			return parent::api($path,$method,$params,array("Authorization: Bearer {$this->access_token}", "Api-Key: {$this->client_id}"));
		}
		
	}

?>
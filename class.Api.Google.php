<?php

	/**
	 * Class ApiGoogle
	 * Google Api Class Extends
	 * @link https://developers.google.com/accounts/docs/OAuth2WebServer
	 * 
	 * @author  Cyril Vermande (cyril@cyrilwebdesign.com)
	 * @version	0.1
	 */

	if (!class_exists("Api")) {
		require_once("class.Api.php");
	}

	class ApiGoogle extends Api {

		/**
		 * Constructor
		 *
		 * @param	array	$config	config for API : client_id, client_secret, redirect_uri
		 */
		public function __construct($config=array()) {
			$this->setUrls(array(
				"api"           => "https://www.googleapis.com",
				"authorization" => "https://accounts.google.com/o/oauth2/auth",
				"access_token"  => "https://accounts.google.com/o/oauth2/token",
			));
			parent::__construct($config);
		}

		/**
		 * Get login URL
		 *
		 * @param	array		$params	Parameters to add to the url
		 *
		 * @return	string		URL to grant authorization
		 *
		 * @throws	Exception	if redirect_uri is empty
		 */
		public function getLoginUrl($params=array()) {
			if (empty($this->redirect_uri)) {
				throw new Exception("redirect_uri undefined");
			}
			$params['client_id']     = $this->client_id;
			$params['redirect_uri']  = $this->redirect_uri;
			$params['response_type'] = "code";
			return parent::getLoginUrl($params);
		}

		/**
		 * Get token params
		 *
		 * @param	array 		$params	Parameters to add to the url -> code
		 *
		 * @return	json 		token params
		 *
		 * @throws	Exception	if code is not in the parameters or redirect_uri is empty
		 */
		public function getTokenParams($params=array()) {
			if (!isset($params['code'])) {
				throw new Exception("code undefined");
			}
			if (empty($this->redirect_uri)) {
				throw new Exception("redirect_uri undefined");
			}
			$params['client_id']     = $this->client_id;
			$params['redirect_uri']  = $this->redirect_uri;
			$params['client_secret'] = $this->client_secret;
			$params['grant_type']    = "authorization_code";
			$token_file = $this->getTokenFile($params, "POST");
			return json_decode($token_file);
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
		 * @throws	Exception	if access_token is empty
		 */
		public function api($path='',$method='GET',$params=array(),$header=array('Content-Type: application/x-www-form-urlencoded')) {
			if (empty($this->access_token)) {
				throw new Exception("access_token undefined");
			}
			return parent::api($path,$method,$params,array('Content-Type: application/x-www-form-urlencoded', "Authorization: Bearer {$this->access_token}"));
		}

	}

?>
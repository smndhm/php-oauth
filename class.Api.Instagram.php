<?php

	/**
	 * ApiInstagram Class
	 * Instagram Api Class Extends
	 * @link	http://instagram.com/developer/ Instagram Developer Documentation
	 *
	 * @author	Simon Duhem @DuMe
	 * @version	0.1
 	 * @since	19-12-2014
	 *
	 */
	
	if (!class_exists('Api')) {
		require_once('class.Api.php');
	}
	
	class ApiInstagram extends Api {
		
		/**
		 * Constructor
		 *
		 * @param	array	$config	config for API : client_id, client_secret, redirect_uri
		 *
		 */
		public function __construct($config=array()) {
			parent::setUrls(array(
				'api'           => 'https://api.instagram.com/v1',
				'authorization' => 'https://api.instagram.com/oauth/authorize',
				'access_token'  => 'https://api.instagram.com/oauth/access_token'
			));
			parent::__construct($config);
		}
		
		/**
		 * Get login URL
		 *
		 * @see	http://instagram.com/developer/authentication/
		 *
		 * @param	array	$params	Parameters to add to the url -> scope
		 *
		 * @return 	string	URL to grant authorization
		 *
		 */
		public function getLoginUrl($params=array()) {
			if (empty($this->redirect_uri)) {
				throw new Exception('redirect_uri undefined');
			}
			$params['redirect_uri']  = $this->redirect_uri;
			$params['client_id']     = $this->client_id;
			$params['response_type'] = 'code';
			return parent::getLoginUrl($params);
		}
		
		/**
		 * Get token params
		 *
		 * @see	http://instagram.com/developer/authentication/
		 *
		 * @param	array	$params	Parameters to add to the url -> code
		 *
		 * @return 	json	token params
		 *
		 */
		public function getTokenParams($params=array()) {
			if (!isset($params['code'])) {
				throw new Exception('code undefined');
			}
			if (empty($this->redirect_uri)) {
				throw new Exception('redirect_uri undefined');
			}
			$params['grant_type']    = 'authorization_code';
			$params['redirect_uri']  = $this->redirect_uri;
			$params['client_id']     = $this->client_id;
			$params['client_secret'] = $this->client_secret;
			$token_file = $this->getTokenFile($params, 'POST');
			return json_decode($token_file);
		}
		
	}

?>
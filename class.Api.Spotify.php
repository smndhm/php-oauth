<?php

	/**
	 * ApiSpotify Class
	 * Spotify Api Class Extends
	 * @link	https://developer.spotify.com/ Spotify Developer
	 *
	 * @author	Simon Duhem @DuMe
	 * @version	0.1
 	 * @since	23-10-2014
	 *
	 */
	
	if (!class_exists('Api')) {
		require_once('class.Api.php');
	}
	
	class ApiSpotify extends Api {
		
		/**
		 * Constructor
		 *
		 * @param	array	$config	config for API : client_id, client_secret, redirect_uri
		 *
		 */
		public function __construct($config=array()) {
			parent::setUrls(array(
				'api'           => 'https://api.spotify.com/',
				'authorization' => 'https://accounts.spotify.com/authorize',
				'access_token'  => 'https://accounts.spotify.com/api/token'
			));
			parent::__construct($config);
		}
		
		/**
		 * Get login URL
		 *
		 * @see	https://developer.spotify.com/web-api/authorization-guide/
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
		 * @see	https://developer.spotify.com/web-api/authorization-guide/
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
<?php

/**
 * Class ApiFacebook
 * Facebook Api Class Extends
 * @link	https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow	Manually Build a Login Flow
 *
 * @author	Simon Duhem @DuMe
 * @version	0.1
 */

if (!class_exists("Api")) {
	require_once("class.Api.php");
}

class ApiFacebook extends Api {

	/**
	 * Constructor
	 *
	 * @param	array	$config	config for API : client_id, client_secret, redirect_uri
	 */
	public function __construct($config=array()) {
		parent::setUrls(array(
			"api"           => "https://graph.facebook.com/",
			"authorization" => "https://www.facebook.com/dialog/oauth/",
			"access_token"  => "https://graph.facebook.com/oauth/access_token",
		));
		parent::__construct($config);
	}

	/**
	 * Get login URL
	 *
	 * @see		https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/#login
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
		$params['redirect_uri']  = $this->redirect_uri;
		$params['client_id']     = $this->client_id;
		$params['response_type'] = "code";
		return parent::getLoginUrl($params);
	}

	/**
	 * Get token params
	 *
	 * @see		https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/#confirm
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
		$token_file = $this->getTokenFile($params);
		$token_params = array();
		parse_str($token_file, $token_params);
		return json_decode(json_encode($token_params));
	}

}

?>
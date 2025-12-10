<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH . "/libraries/vendor/autoload.php");

class google_oauth extends CI_Model {

    private $client; 

    public function __construct() {  
        $this->client = new Google_Client();
        $this->client->setClientId(CLIENT_ID);
        $this->client->setClientSecret(ClientSecret);
        $this->client->setRedirectUri(base_url(RedirectURL));
        $this->client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
        $this->client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
    }

    public function get_login_url() {
        return $this->client->createAuthUrl();
    }

    public function get_token(){
        return $this->client->getAccessToken();
    }

    public function authenticate($code) { 
        $this->client->fetchAccessTokenWithAuthCode($code);
        $accessToken = $this->client->getAccessToken();

        if ($accessToken) { 
            $this->client->setAccessToken($accessToken);
            $this->session->set_userdata("access_token",$accessToken);
            return $this->client;
        }
        return false;
    }

    public function get_user_info() {
        // Get user info from Google
        if ($this->client->getAccessToken()) {
            $oauth2Service = new Google_Service_Oauth2($this->client);
            return $oauth2Service->userinfo->get();
        }
        return false;
    }

    function logout(){
        $google_client = new Google_Client();
        $google_client->revokeToken($this->client->getAccessToken());
    }
}

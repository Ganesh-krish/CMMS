<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Google OAuth removed; placeholder to satisfy old references without loading vendor.
class google_oauth extends CI_Model {
    public function __construct() { parent::__construct(); }
    public function get_login_url() { return ''; }
    public function get_token(){ return null; }
    public function authenticate($code) { return false; }
    public function get_user_info() { return false; }
    function logout(){ return true; }
}

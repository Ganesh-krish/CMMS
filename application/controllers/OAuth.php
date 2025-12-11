<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OAuth extends CI_Controller { 
    
    private $user_session;
    public function __construct() {
        parent::__construct();
        $this->load->model('db_model'); 
        $this->user_session= $this->session->userdata('owner');
    }

    private function verify_password($input, $stored, $owner_id = null) {
        if (!empty($stored) && strlen($stored) > 30 && password_verify($input, $stored)) {
            return true;
        }
        if ($stored === $input) {
            // upgrade to bcrypt if we have owner id
            if ($owner_id) {
                $hash = password_hash($input, PASSWORD_BCRYPT);
                $this->db_model->update(TABLE_OWNER, ['password' => $hash], ['id' => $owner_id]);
            }
            return true;
        }
        return false;
    }

    public function index() {   
        if($this->user_session){
            redirect("Dashboard");
        } 
        $post = $this->input->post();
        if ($post) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            if (empty($username) || empty($password)) {
                $data['error'] = 'Username and password are required';
                return $this->load->view('auth/owner_login', $data);
            }

            $owner = $this->db_model->get_row(TABLE_OWNER, [
                'is_active' => 1,
                'email' => $username
            ]);
            if (!$owner) {
                $data['error'] = 'Invalid credentials';
                return $this->load->view('auth/owner_login', $data);
            }

            if (!$this->verify_password($password, $owner['password'] ?? '', $owner['id'])) {
                $data['error'] = 'Invalid credentials';
                return $this->load->view('auth/owner_login', $data);
            }

            $this->session->set_userdata('owner', $owner);
            return redirect(base_url('Dashboard'));
        }

        $this->load->view('auth/owner_login');
    }

    public function logout() { 
        $this->session->sess_destroy();
        redirect(base_url("OAuth"));
    }
}

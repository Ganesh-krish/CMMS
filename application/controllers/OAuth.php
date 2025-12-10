<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OAuth extends CI_Controller { 
    
    private $access_token;
    private $user_session;
    public function __construct() {
        parent::__construct();
        $this->load->model('google_oauth');
        $this->load->model('db_model'); 
        $this->access_token= $this->session->userdata('access_token');
        $this->user_session= $this->session->userdata('owner');
    }

    public function index() {   
        if($this->user_session){
            redirect("Dashboard");
        } 
        if (!$this->access_token) { 
            $loginUrl = $this->google_oauth->get_login_url(); 
            redirect($loginUrl);
        }else{
            $user_info = $this->google_oauth->get_user_info();
            if (empty($user_info)) {  
                 redirect(base_url("OAuth/logout"));
            }
            $owner=$this->db_model->get_row(TABLE_OWNER,['email'=>$user_info['email'],"is_active"=>1]); 
            if (empty($owner)) { 
                redirect(base_url("OAuth/access_denied"));
            }
            $this->session->set_userdata('owner', $owner);
            redirect(base_url('Dashboard'));

        }   
    }

    public function verify() { 
        if (empty($this->input->get('code'))) { redirect(base_url('')); }

        $code = $this->input->get('code'); 
        $client = $this->google_oauth->authenticate($code);  
        if (empty($client)) {  
            echo "client not found";
            return;
        }  
        $user_info = $this->google_oauth->get_user_info(); 
        if (empty($user_info)) {  
             echo "User Info Not Found";
             return;
        } 
        $owner=$this->db_model->get_row(TABLE_OWNER,['email'=>$user_info['email']]); 
        if ($owner) {
            if(empty($owner['name']) || empty($owner['profile_picture'])){
                $this->db_model->update(TABLE_OWNER,["name" => $user_info['name'], "profile_picture" => $user_info['picture']],["id"=>$owner['id']]);
            }
            $this->session->set_userdata('owner', $owner);
            redirect(base_url('Dashboard'));
        } else { 
            $login_requests=$this->db->where('is_active',1)->where('email',$user_info['email'])->get(TABLE_REQUEST)->row();
            if(!$login_requests){ 
                $this->db_model->insert(TABLE_REQUEST, ["email" => $user_info['email'], "name" => $user_info['name'], "profile_picture" => $user_info['picture']]);
            } 
            redirect(base_url("OAuth/access_denied"));
        }  
        
    }

    public function access_denied(){
        echo "access denied";
    }

    public function logout() { 
        $this->google_oauth->logout();
        $this->session->sess_destroy();
        redirect(base_url("OAuth"));
    }
}

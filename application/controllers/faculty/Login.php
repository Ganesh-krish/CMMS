<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	private $college;
	function __construct() {
        parent::__construct(); 
        $this->load->model('faculty/common', 'common');
        $this->load->model('faculty/db_model', 'db_model');
		$url = $this->uri->segment(1); 
		if(!($url)){
			return redirect(base_url(""));
		} 

        // if($this->session->userdata($url)){
        //     return redirect(base_url("$url/principal"));
        // }
		$this->college = $this->db_model->get_row(TABLE_COLLEGE,["is_active"=>1,"site_url"=>$url]); 

        if(!$this->college){
            redirect(base_url(""));
        }
    }
	public function index()
	{
		$this->load->view('landing_page');
	}

	public function faculty($url)
	{
		$post = $this->input->post();
        if($post){ 
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email'); 
            if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
                return redirect(base_url("$url/login/faculty"));
            } else {
				$data = $this->db_model->get_row(TABLE_STAFF,["email"=>$post['email'],"is_active"=>1,"college_id"=>$this->college['id']]);
                if(!$data){
                    $this->session->set_flashdata('message',array("danger","Invalid Email or Password")); 
                    return redirect(base_url("$url/login/faculty"));
                }
                $stored = $data['password'] ?? '';
                $valid = false;
                if (!empty($stored) && strlen($stored) > 30 && password_verify($post['password'], $stored)) {
                    $valid = true;
                } elseif ($stored === $post['password']) {
                    $valid = true;
                    // upgrade hash
                    $hash = password_hash($post['password'], PASSWORD_BCRYPT);
                    $this->db_model->update(TABLE_STAFF, ['password' => $hash], ['id' => $data['id']]);
                    $data['password'] = $hash;
                }
                if(!$valid){
                    $this->session->set_flashdata('message',array("danger","Invalid Email or Password")); 
                    return redirect(base_url("$url/login/faculty"));
                } 
                $this->session->set_userdata($url, $data);
                return redirect(base_url("$url/principal")); 
            }
		}
		$data["college_url"] = $url;
		$data["logo"] = $this->college['logo'];
		$data["banner"] = $this->college['banner'];
		$data["url"] = $url;
		$data["college_name"] = $this->college['name'];
		$this->load->view("faculty/login",$data);
	}

	public function logout($url){ 
		$this->session->unset_userdata($url);
		redirect(base_url("$url/login/faculty"));
	}
}

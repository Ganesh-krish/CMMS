<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class College extends CI_Controller {

	private $user_session;
	function __construct() {
        parent::__construct();
		$this->common->check_user_session();
		$this->user_session= $this->session->userdata('owner');
    }
	public function index()
	{
		return redirect('college/view');
	}

	public function add() {
		$this->session->set_flashdata('message', array('warning',"Single-college mode enabled. College cannot be created."));
		return redirect('college/view');
	}

	public function changeStatus($id,$status){  
		$this->session->set_flashdata('message', array('warning',"Single-college mode enabled. Status changes are disabled."));
		return redirect(base_url("college/view"));
	}

	public function edit($id){
		if ((int)$id !== (int)SINGLE_COLLEGE_ID) {
			$this->session->set_flashdata('message', array('warning',"Single-college mode enabled. Only the default college can be managed."));
			return redirect('college/view');
		}
		$post = $this->input->post();
		if($post){
			$data = array(
				'name' => $this->input->post('name'),
				'established_year' => $this->input->post('established_year'),
				'address' => $this->input->post('address'),
				'description' => $this->input->post('description'),
				'city' => $this->input->post('city'),
				'state' => $this->input->post('state'), 
				'phone_number' => $this->input->post('phone_number'),
				'email' => $this->input->post('email'), 
				'created_by' => $this->user_session['id']
			); 
			$this->db_model->update(TABLE_COLLEGE,$data,["id"=>$id]);
			$this->session->set_flashdata('message', array('success',"College updated."));
			return redirect('college/view');
		}else{ 
			$data["college"] = $this->db_model->get_row(TABLE_COLLEGE,["id"=>$id,"is_active"=>1]);
			$this->common->load_view('college/add',$data);
		}
	}

	public function view(){
		$college = $this->db_model->get_row(TABLE_COLLEGE,["id"=>SINGLE_COLLEGE_ID]);
		if ($college) {
			$college["subscription_count"] = $this->db_model->get_subscription_count($college["id"]);
			$data["colleges"] = [$college];
		} else {
			$data["colleges"] = [];
		}
		$this->common->load_view('college/view',$data);
	}
}

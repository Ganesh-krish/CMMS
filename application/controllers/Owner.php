<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Owner extends CI_Controller {

	private $user_session;
	function __construct() {
        parent::__construct();
        $this->common->check_user_session();
		$this->user_session= $this->session->userdata('owner');
    }

	public function view()
	{
		$data['owners'] = $this->db_model->get_all(TABLE_OWNER,["id !="=>$this->user_session['id']]); 
		$this->common->load_view("owner/view",$data); 
	}

	public function changeUserStatus($id,$status){  
		$result=$this->db_model->update(TABLE_OWNER,["is_active"=>$status?0:1],["id"=>$id]);
		$message = array('success',"Status Updated SuccessFully");
		if(!$result){
			$message = array('danger',"Something went wrong");
		}
		$this->session->set_flashdata('message', $message); 
		redirect(base_url("owner/view"));
	}

	public function add()
	{
		$data['users'] = $this->db_model->get_all(TABLE_REQUEST);
		$this->common->load_view("owner/add",$data); 
	}

	public function accept_request($id){
		$request =  $this->db_model->get_row(TABLE_REQUEST,["id"=>$id]);
		if(empty($request)){
			$this->session->set_flashdata('message', array('danger',"Request not Found")); 
			return redirect(base_url("owner/view"));
		}

		$owner = [
			"name"=>$request['name'],
			"email"=>$request['email'],
			"profile_picture"=>$request['profile_picture'],
			'created_by' => $this->user_session['id'] 
		];

		$result=$this->db_model->insert(TABLE_OWNER,$owner);
		$message = array('success',"Request Accepted SuccessFully");
		if(!$result){
			$message = array('danger',"Something went wrong"); 
		}
		$this->db_model->delete(TABLE_REQUEST,["id"=>$id]);
		$this->session->set_flashdata('message', $message); 
		redirect(base_url("owner/add"));
	}

	public function delete_request($id){
		$result=$this->db_model->delete(TABLE_REQUEST,["id"=>$id]);
		$message = array('success',"Request Deleted SuccessFully");
		if(!$result){
			$message = array('danger',"Something went wrong");  
		}
		$this->session->set_flashdata('message', $message); 
		redirect(base_url("owner/add"));
	}


 
}

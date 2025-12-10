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
		// $this->common->load_view('college/add');
	}

	public function add() {
		$post = $this->input->post();
		if($post){
			$this->form_validation->set_rules('name', 'College Name', 'trim|required|min_length[3]|max_length[255]');
			$this->form_validation->set_rules('site_url', 'Site Url', 'trim|required|regex_match[/^[a-zA-Z0-9-]+$/]');
			$this->form_validation->set_rules('established_year', 'Established Year', 'required|numeric|exact_length[4]');
			$this->form_validation->set_rules('address', 'Address', 'trim|required'); 
			$this->form_validation->set_rules('city', 'City', 'trim|required|max_length[100]');
			$this->form_validation->set_rules('state', 'State', 'trim|required|max_length[100]'); 
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|numeric|min_length[10]|max_length[15]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[colleges.email]'); 
			if ($this->form_validation->run() == FALSE) { 
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
				return redirect('college/add');
			} else {
				$data = array(
					'name' => $this->input->post('name'),
					'site_url' => $this->input->post('site_url'),
					'established_year' => $this->input->post('established_year'),
					'address' => $this->input->post('address'),
					'description' => $this->input->post('description'),
					'city' => $this->input->post('city'),
					'state' => $this->input->post('state'), 
					'phone_number' => $this->input->post('phone_number'),
					'email' => $this->input->post('email'), 
					'created_by' => $this->user_session['id']
				); 
				if ($this->db_model->insert(TABLE_COLLEGE,$data)) {
					$this->session->set_flashdata('message', array('success',"College registered successfully!")); 	
				} else {
					$this->session->set_flashdata('ma', array('danger',"Failed to register the college."));
				}
				redirect('college/view');
			}
		}else{
			$this->common->load_view('college/add');
		}
	}

	public function changeStatus($id,$status){  
		$result=$this->db_model->update(TABLE_COLLEGE,["is_active"=>$status?0:1],["id"=>$id]);
		$message = array('success',"Status Updated SuccessFully");
		if(!$result){
			$message = array('danger',"Something went wrong");
		}
		$this->session->set_flashdata('message', $message); 
		redirect(base_url("college/view"));
	}

	public function edit($id){
		$post = $this->input->post();
		if($post){
			$this->form_validation->set_rules('name', 'College Name', 'trim|min_length[3]|max_length[255]');
			$this->form_validation->set_rules('site_url', 'Site Url', 'trim|required|regex_match[/^[a-zA-Z0-9-]+$/]');
			$this->form_validation->set_rules('established_year', 'Established Year', 'numeric|exact_length[4]');
			$this->form_validation->set_rules('address', 'Address', 'trim'); 
			$this->form_validation->set_rules('city', 'City', 'trim|max_length[100]');
			$this->form_validation->set_rules('state', 'State', 'trim|max_length[100]'); 
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|numeric|min_length[10]|max_length[15]');
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email'); 
			if ($this->form_validation->run() == FALSE) { 
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
				return redirect('college/view');
			} else {
				$data = array(
					'name' => $this->input->post('name'),
					'site_url' => $this->input->post('site_url'),
					'established_year' => $this->input->post('established_year'),
					'address' => $this->input->post('address'),
					'description' => $this->input->post('description'),
					'city' => $this->input->post('city'),
					'state' => $this->input->post('state'), 
					'phone_number' => $this->input->post('phone_number'),
					'email' => $this->input->post('email'), 
					'created_by' => $this->user_session['id']
				); 
				if ($this->db_model->update(TABLE_COLLEGE,$data,["id"=>$id])) {
					$this->session->set_flashdata('message', array('success',"College registered successfully!")); 	
				} else {
					$this->session->set_flashdata('ma', array('danger',"Failed to register the college."));
				}
				redirect('college/view');
			}
		}else{ 
			$data["college"] = $this->db_model->get_row(TABLE_COLLEGE,["id"=>$id,"is_active"=>1]);
			$this->common->load_view('college/add',$data);
		}
	}

	public function view(){
		$colleges = $this->db_model->get_all(TABLE_COLLEGE);

		foreach ($colleges as &$value) {
			$value["subscription_count"] = $this->db_model->get_subscription_count($value["id"]);
		}
		unset($value);
		$data["colleges" ] = $colleges;

		$this->common->load_view('college/view',$data);
	}
}

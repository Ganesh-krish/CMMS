<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends CI_Controller {

    private $url;
    private $college;
    private $session_data;
    private $permissions;

    function __construct() {
        parent::__construct();

        $this->load->model('common', 'faculty_common');
        $this->load->model('Db_model', 'db_model');
        

        $this->url = $this->uri->segment(1);

        // Use unified session approach
        $unified_user = $this->session->userdata('user');
        if ($unified_user && isset($unified_user['user_type']) && $unified_user['user_type'] === 'faculty') {
            // Unified session access
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $unified_user;
        } else {
            // Fallback for legacy access
            $this->faculty_common->check_user_session($this->url);
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $this->session->userdata($this->url);
        }

        $this->permissions = $this->faculty_common->get_access_permissions($this->session_data);

        // Only SuperAdmin can manage departments at system level
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        if ($role !== ROLE_PRINCIPAL) {
            redirect($this->url.'/dashboard');
        }
    }

    public function view() {
        $departments = $this->db_model->get_all(TABLE_DEPARTMENT,["is_active"=>1]);

        // Add student and staff counts for each department
        foreach ($departments as &$dept) {
            // Count students in this department
            $dept['total_students'] = $this->db_model->count(TABLE_STUDENT, [
                'department' => $dept['id'],
                'is_active' => 1
            ]);

            // Count staff/faculty in this department
            $dept['total_staff'] = $this->db_model->count(TABLE_FACULTY, [
                'department' => $dept['id'],
                'is_active' => 1
            ]);
        }

        $data["departments"] = $departments;
        $data["can_manage"] = true; // SuperAdmin can manage departments

        $data["url"] = $this->url;
        $class["classname"] = "departments";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/departments");
        $class["college"] = $this->college;

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/department/view', $data);
        $this->load->view('common/footer');
    }

    public function add($college_id = null) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Department Name', 'trim|required|min_length[1]|max_length[255]');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message',array("danger",validation_errors()));
                return redirect($this->url.'/departments');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'created_by' => $this->session_data['id'],
                    "is_active"=>1,
                    "college_id"=>$college_id ?: $this->college['id']
                );
                if ($this->db_model->insert(TABLE_DEPARTMENT,$data)) {
                    $this->session->set_flashdata('message', array('success',"Department Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger',"Failed to create Department."));
                }
                redirect($this->url.'/departments');
            }
        }else{
            $data["url"] = $this->url;
            $data["can_manage"] = true; // SuperAdmin can manage departments
            $class["classname"] = "departments";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/departments");

            $data['college_id'] = $college_id ?: $this->college['id'];

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/department/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function edit($id) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Department Name', 'trim|min_length[3]|max_length[255]');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message',array("danger",validation_errors()));
                return redirect($this->url.'/departments');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'updated_by' => $this->session_data['id']
                );
                if ($this->db_model->update(TABLE_DEPARTMENT,$data,["id"=>$id])) {
                    $this->session->set_flashdata('message', array('success',"Department Updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger',"Failed to update Department."));
                }
                redirect($this->url.'/departments');
            }
        }else{
            $data["department"] = $this->db_model->get_row(TABLE_DEPARTMENT,["id"=>$id,"is_active"=>1]);
            $data["can_manage"] = true; // SuperAdmin can manage departments

            $data["url"] = $this->url;
            $class["classname"] = "departments";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/departments");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/department/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function delete($id){
        $result=$this->db_model->delete(TABLE_DEPARTMENT,["id"=>$id]);
        $message = array('success',"Department Deleted Successfully");
        if(!$result){
            $message = array('danger',"Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect($this->url.'/departments');
    }
}
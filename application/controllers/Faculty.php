<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faculty extends CI_Controller {

    private $url;
    private $college;
    private $session_data;
    private $permissions;

    function __construct() {
        parent::__construct();

        $this->load->model('common', 'faculty_common');
        $this->load->model('Db_model', 'db_model');
        $this->load->model('Test_model', 'test_model');

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

        // Only SuperAdmin can manage faculty
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_SUPERADMIN) {
            redirect($this->url.'/dashboard');
        }
    }

    // ============ INSTRUCTOR MANAGEMENT ============

    public function instructor() {
        $data["instructors"] = $this->db_model->get_all(TABLE_FACULTY, ["role" => ROLE_STAFF, "is_active" => 1]);

        $data["url"] = $this->url;
        $class["classname"] = "faculty_instructor";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/faculty/instructor");

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/faculty/instructor/view', $data);
        $this->load->view('common/footer');
    }

    public function add_instructor() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[faculty.email]');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
            $this->form_validation->set_rules('department_id', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/faculty/instructor');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'role' => ROLE_STAFF,
                    'designation' => DESIGNATION_STAFF,
                    'college_id' => $this->college['id'],
                    'department_id' => $this->input->post('department_id'),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->session_data['id']
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Instructor added successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to add Instructor."));
                }
                redirect($this->url.'/faculty/instructor');
            }
        } else {
            $data["url"] = $this->url;
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1]);
            $class["classname"] = "faculty_instructor";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/faculty/instructor");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/faculty/instructor/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function edit_instructor($id) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('department_id', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/faculty/instructor');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department_id' => $this->input->post('department_id'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->session_data['id']
                );

                if ($this->db_model->update(TABLE_FACULTY, $data, ["id" => $id])) {
                    $this->session->set_flashdata('message', array('success', "Instructor updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Instructor."));
                }
                redirect($this->url.'/faculty/instructor');
            }
        } else {
            $data["instructor"] = $this->db_model->get_row(TABLE_FACULTY, ["id" => $id, "role" => ROLE_STAFF, "is_active" => 1]);
            if (!$data["instructor"]) {
                $this->session->set_flashdata('message', array('danger', "Instructor not found."));
                redirect($this->url.'/faculty/instructor');
            }

            $data["url"] = $this->url;
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1]);
            $class["classname"] = "faculty_instructor";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/faculty/instructor");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/faculty/instructor/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function delete_instructor($id) {
        $result = $this->db_model->update(TABLE_FACULTY, ["is_active" => 0], ["id" => $id, "role" => ROLE_STAFF]);
        $message = array('success', "Instructor deleted successfully!");
        if (!$result) {
            $message = array('danger', "Failed to delete Instructor.");
        }
        $this->session->set_flashdata('message', $message);
        redirect($this->url.'/faculty/instructor');
    }

    // ============ CUSTODIAN MANAGEMENT ============

    public function custodian() {
        $data["custodians"] = $this->db_model->get_all(TABLE_FACULTY, ["role" => ROLE_CUSTODIAN, "is_active" => 1]);

        $data["url"] = $this->url;
        $class["classname"] = "faculty_custodian";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/faculty/custodian");

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/faculty/custodian/view', $data);
        $this->load->view('common/footer');
    }

    public function add_custodian() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[faculty.email]');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
            $this->form_validation->set_rules('department_id', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/faculty/custodian');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'role' => ROLE_CUSTODIAN,
                    'designation' => DESIGNATION_CUSTODIAN,
                    'college_id' => $this->college['id'],
                    'department_id' => $this->input->post('department_id'),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->session_data['id']
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Custodian added successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to add Custodian."));
                }
                redirect($this->url.'/faculty/custodian');
            }
        } else {
            $data["url"] = $this->url;
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1]);
            $class["classname"] = "faculty_custodian";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/faculty/custodian");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/faculty/custodian/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function edit_custodian($id) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('department_id', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/faculty/custodian');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department_id' => $this->input->post('department_id'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->session_data['id']
                );

                if ($this->db_model->update(TABLE_FACULTY, $data, ["id" => $id])) {
                    $this->session->set_flashdata('message', array('success', "Custodian updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Custodian."));
                }
                redirect($this->url.'/faculty/custodian');
            }
        } else {
            $data["custodian"] = $this->db_model->get_row(TABLE_FACULTY, ["id" => $id, "role" => ROLE_CUSTODIAN, "is_active" => 1]);
            if (!$data["custodian"]) {
                $this->session->set_flashdata('message', array('danger', "Custodian not found."));
                redirect($this->url.'/faculty/custodian');
            }

            $data["url"] = $this->url;
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1]);
            $class["classname"] = "faculty_custodian";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/faculty/custodian");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/faculty/custodian/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function delete_custodian($id) {
        $result = $this->db_model->update(TABLE_FACULTY, ["is_active" => 0], ["id" => $id, "role" => ROLE_CUSTODIAN]);
        $message = array('success', "Custodian deleted successfully!");
        if (!$result) {
            $message = array('danger', "Failed to delete Custodian.");
        }
        $this->session->set_flashdata('message', $message);
        redirect($this->url.'/faculty/custodian');
    }
}
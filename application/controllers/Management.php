<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Management extends CI_Controller {

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
        // print_r($unified_user);
         
        // Convert object to array if needed (session serialization can change data type)
        if (is_object($unified_user)) {
            $unified_user = (array) $unified_user;
        }

        // print_r($unified_user);
           
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
        // print_r($this->session_data);
           
        $this->permissions = $this->faculty_common->get_access_permissions($this->session_data);
        // print_r($this->permissions);
          
        // Only SuperAdmin can manage administrators
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        // print_r($role);
        
        if ($role !== ROLE_SUPERADMIN) {
            redirect($this->url.'/dashboard');
        }
    }

    // ============ PRINCIPAL MANAGEMENT ============

    public function principal() {
        // print_r("principal");
        // print_r($this->url);
        // exit;   

        $data["administrators"] = $this->db_model->get_all(TABLE_FACULTY, ["role" => ROLE_SUPERADMIN, "is_active" => 1]);

        $data["url"] = $this->url;
        $class["classname"] = "management_principal";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/management/principal");

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/principal/view', $data);
        $this->load->view('common/footer');
    }

    public function add_principal() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[faculty.email]');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/management/principal');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'role' => ROLE_SUPERADMIN,
                    'designation' => DESIGNATION_PRINCIPAL,
                    'college_id' => $this->college['id'],
                    'department_id' => null,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->session_data['id']
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Administrator added successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to add Administrator."));
                }
                redirect($this->url.'/management/principal');
            }
        } else {
            $data["url"] = $this->url;
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1]);
            $class["classname"] = "management_principal";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/management/principal");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/principal/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function edit_principal($id) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/management/principal');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->session_data['id']
                );

                if ($this->db_model->update(TABLE_FACULTY, $data, ["id" => $id])) {
                    $this->session->set_flashdata('message', array('success', "Administrator updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Administrator."));
                }
                redirect($this->url.'/management/principal');
            }
        } else {
            $data["administrator"] = $this->db_model->get_row(TABLE_FACULTY, ["id" => $id, "role" => ROLE_SUPERADMIN, "is_active" => 1]);
            if (!$data["administrator"]) {
                $this->session->set_flashdata('message', array('danger', "Administrator not found."));
                redirect($this->url.'/management/principal');
            }

            $data["url"] = $this->url;
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1]);
            $class["classname"] = "management_principal";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/management/principal");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/principal/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function delete_principal($id) {
        $result = $this->db_model->update(TABLE_FACULTY, ["is_active" => 0], ["id" => $id, "role" => ROLE_SUPERADMIN]);
        $message = array('success', "Administrator deleted successfully!");
        if (!$result) {
            $message = array('danger', "Failed to delete Administrator.");
        }
        $this->session->set_flashdata('message', $message);
        redirect($this->url.'/management/principal');
    }

    // ============ VICE-PRINCIPAL MANAGEMENT ============

    public function vice_principal() {
        $data["vice_principals"] = $this->db_model->get_all(TABLE_FACULTY, ["role" => ROLE_VICE_PRINCIPAL, "is_active" => 1]);

        $data["url"] = $this->url;
        $class["classname"] = "management_vice_principal";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/management/vice_principal");

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/vice_principal/view', $data);
        $this->load->view('common/footer');
    }

    public function add_vice_principal() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[faculty.email]');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
            $this->form_validation->set_rules('department_id', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/management/vice_principal');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'role' => ROLE_VICE_PRINCIPAL,
                    'designation' => DESIGNATION_VICE_PRINCIPAL,
                    'college_id' => $this->college['id'],
                    'department_id' => $this->input->post('department_id'),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->session_data['id']
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Assistant Administrator added successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to add Assistant Administrator."));
                }
                redirect($this->url.'/management/vice_principal');
            }
        } else {
            $data["url"] = $this->url;
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1]);
            $class["classname"] = "management_vice_principal";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/management/vice_principal");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/vice_principal/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function edit_vice_principal($id) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('department_id', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/management/vice_principal');
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
                    $this->session->set_flashdata('message', array('success', "Assistant Administrator updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Assistant Administrator."));
                }
                redirect($this->url.'/management/vice_principal');
            }
        } else {
            $data["vice_principal"] = $this->db_model->get_row(TABLE_FACULTY, ["id" => $id, "role" => ROLE_VICE_PRINCIPAL, "is_active" => 1]);
            if (!$data["vice_principal"]) {
                $this->session->set_flashdata('message', array('danger', "Assistant Administrator not found."));
                redirect($this->url.'/management/vice_principal');
            }

            $data["url"] = $this->url;
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1]);
            $class["classname"] = "management_vice_principal";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/management/vice_principal");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/vice_principal/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function delete_vice_principal($id) {
        $result = $this->db_model->update(TABLE_FACULTY, ["is_active" => 0], ["id" => $id, "role" => ROLE_VICE_PRINCIPAL]);
        $message = array('success', "Assistant Administrator deleted successfully!");
        if (!$result) {
            $message = array('danger', "Failed to delete Assistant Administrator.");
        }
        $this->session->set_flashdata('message', $message);
        redirect($this->url.'/management/vice_principal');
    }

    // ============ HOD MANAGEMENT ============

    public function hod() {
        $data["hods"] = $this->db_model->get_all(TABLE_FACULTY, ["role" => ROLE_HOD, "is_active" => 1]);

        $data["url"] = $this->url;
        $class["classname"] = "management_hod";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/management/hod");

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/hod/view', $data);
        $this->load->view('common/footer');
    }

    public function add_hod() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[faculty.email]');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
            $this->form_validation->set_rules('department_id', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/management/hod');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'role' => ROLE_HOD,
                    'designation' => DESIGNATION_HOD,
                    'college_id' => $this->college['id'],
                    'department_id' => $this->input->post('department_id'),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->session_data['id']
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Department Administrator added successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to add Department Administrator."));
                }
                redirect($this->url.'/management/hod');
            }
        } else {
            $data["url"] = $this->url;
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1]);
            $class["classname"] = "management_hod";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/management/hod");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/hod/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function edit_hod($id) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('department_id', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/management/hod');
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
                    $this->session->set_flashdata('message', array('success', "Department Administrator updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Department Administrator."));
                }
                redirect($this->url.'/management/hod');
            }
        } else {
            $data["hod"] = $this->db_model->get_row(TABLE_FACULTY, ["id" => $id, "role" => ROLE_HOD, "is_active" => 1]);
            if (!$data["hod"]) {
                $this->session->set_flashdata('message', array('danger', "Department Administrator not found."));
                redirect($this->url.'/management/hod');
            }

            $data["url"] = $this->url;
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1]);
            $class["classname"] = "management_hod";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/management/hod");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/hod/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function delete_hod($id) {
        $result = $this->db_model->update(TABLE_FACULTY, ["is_active" => 0], ["id" => $id, "role" => ROLE_HOD]);
        $message = array('success', "Department Administrator deleted successfully!");
        if (!$result) {
            $message = array('danger', "Failed to delete Department Administrator.");
        }
        $this->session->set_flashdata('message', $message);
        redirect($this->url.'/management/hod');
    }

    // ============ RESET PASSWORD METHODS ============

    public function reset_password_principal() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('id', 'Id', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message',array("danger",validation_errors()));
                return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/management/principal"));
            }
            $update = $this->db_model->update(TABLE_FACULTY,["password"=>password_hash($post['password'], PASSWORD_DEFAULT)],["is_active"=>1,"id"=>$post['id'], "role" => ROLE_SUPERADMIN]);
            if(!$update){
                $this->session->set_flashdata('message',array("danger","Something Went Wrong"));
                return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/management/principal"));
            }
            $this->session->set_flashdata('message',array("success","Password Reset successfully"));
            return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/management/principal"));
        }
    }

    public function reset_password_vice_principal() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('id', 'Id', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message',array("danger",validation_errors()));
                return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/management/vice_principal"));
            }
            $update = $this->db_model->update(TABLE_FACULTY,["password"=>password_hash($post['password'], PASSWORD_DEFAULT)],["is_active"=>1,"id"=>$post['id'], "role" => ROLE_VICE_PRINCIPAL]);
            if(!$update){
                $this->session->set_flashdata('message',array("danger","Something Went Wrong"));
                return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/management/vice_principal"));
            }
            $this->session->set_flashdata('message',array("success","Password Reset successfully"));
            return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/management/vice_principal"));
        }
    }

    public function reset_password_hod() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('id', 'Id', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message',array("danger",validation_errors()));
                return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/management/hod"));
            }
            $update = $this->db_model->update(TABLE_FACULTY,["password"=>password_hash($post['password'], PASSWORD_DEFAULT)],["is_active"=>1,"id"=>$post['id'], "role" => ROLE_HOD]);
            if(!$update){
                $this->session->set_flashdata('message',array("danger","Something Went Wrong"));
                return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/management/hod"));
            }
            $this->session->set_flashdata('message',array("success","Password Reset successfully"));
            return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/management/hod"));
        }
    }
}
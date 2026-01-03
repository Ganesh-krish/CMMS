<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Student extends CI_Controller {
    

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
          
        // Role-based access control for student management
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);

        // Allow appropriate roles to access student management:
        // - Principal: Full access to all student management
        // - Vice Principal: Can manage students
        // - HOD: Can view students in their department
        $allowed_roles = [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD];

        if (!in_array($role, $allowed_roles, true)) {
            redirect($this->url.'/dashboard');
        }
    }





    // ============ ADMIN STUDENT MANAGEMENT METHODS ============

    public function students() {
        // Switch to HTML output for admin interface
        $this->output->set_content_type('text/html');

        $this->load->model('faculty/common', 'faculty_common');

        // Use unified session approach for admin
        $unified_user = $this->session->userdata('user');
        if ($unified_user && isset($unified_user['user_type']) && $unified_user['user_type'] === 'faculty') {
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $unified_user;
        } else {
            $this->faculty_common->check_user_session($this->url);
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $this->session->userdata($this->url);
        }

        $data["students"] = $this->db_model->get_all(TABLE_STUDENT, [
            "is_active" => 1,
            "college_id" => $this->college['id']
        ]);

        $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, [
            "is_active" => 1,
            "college_id" => $this->college['id']
        ]);

        // Statistics for student management
        $data["stats"] = array(
            "total_departments" => count($data["departments"]),
            "total_students" => count($data["students"])
        );

        // Get students count per department
        $department_stats = array();
        foreach ($data["departments"] as $dept) {
            $student_count = $this->db_model->count(TABLE_STUDENT, [
                "department" => $dept['id'],
                "is_active" => 1,
                "college_id" => $this->college['id']
            ]);
            if ($student_count > 0) {
                $department_stats[] = array(
                    "name" => $dept['name'],
                    "student_count" => $student_count
                );
            }
        }
        $data["department_stats"] = $department_stats;

        $data["url"] = $this->url;
        $class["classname"] = "students";
        $class["url"] = $this->url;

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/student/view', $data);
        $this->load->view('common/footer');
    }

    public function add_student() {
        // Switch to HTML output for admin interface
        $this->output->set_content_type('text/html');

        $this->load->model('faculty/common', 'faculty_common');

        // Use unified session approach for admin
        $unified_user = $this->session->userdata('user');
        if ($unified_user && isset($unified_user['user_type']) && $unified_user['user_type'] === 'faculty') {
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $unified_user;
        } else {
            $this->faculty_common->check_user_session($this->url);
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $this->session->userdata($this->url);
        }

        $post = $this->input->post();

        if($post){
            $this->form_validation->set_rules('name', 'Full Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email|is_unique[students.email]');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('roll_no', 'Enrollment Number', 'trim|required|is_unique[students.roll_no]');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('batch', 'Batch', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/students');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'roll_no' => $this->input->post('roll_no'), // Changed from registration_number
                    'department' => $this->input->post('department'),
                    'batch' => $this->input->post('batch'),
                    'role' => ROLE_STUDENT,
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'college_id' => $this->college['id'],
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                );

                $insert_id = $this->db_model->insert(TABLE_STUDENT, $data);

                if ($insert_id) {
                    $this->session->set_flashdata('message', array('success', "Student added successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to add student."));
                }
                redirect($this->url.'/students');
            }
        } else {
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, [
                "is_active" => 1,
                "college_id" => $this->college['id']
            ]);

            $data["url"] = $this->url;
            $class["classname"] = "students";
            $class["url"] = $this->url;

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/student/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function edit_student($id) {
        // Switch to HTML output for admin interface
        $this->output->set_content_type('text/html');

        $this->load->model('faculty/common', 'faculty_common');

        // Use unified session approach for admin
        $unified_user = $this->session->userdata('user');
        if ($unified_user && isset($unified_user['user_type']) && $unified_user['user_type'] === 'faculty') {
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $unified_user;
        } else {
            $this->faculty_common->check_user_session($this->url);
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $this->session->userdata($this->url);
        }

        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Full Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('batch', 'Batch', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/students');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'phone' => $this->input->post('phone'),
                    'department' => $this->input->post('department'),
                    'batch' => $this->input->post('batch'),
                    'updated_at' => date('Y-m-d H:i:s')
                );

                if ($this->db_model->update(TABLE_STUDENT, $data, ["id" => $id])) {
                    $this->session->set_flashdata('message', array('success', "Student updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update student."));
                }
                redirect($this->url.'/students');
            }
        } else {
            $data["student"] = $this->db_model->get_row(TABLE_STUDENT, ["id" => $id, "is_active" => 1]);
            if (!$data["student"]) {
                $this->session->set_flashdata('message', array('danger', "Student not found."));
                redirect($this->url.'/students');
            }

            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, [
                "is_active" => 1,
                "college_id" => $this->college['id']
            ]);

            $data["url"] = $this->url;
            $class["classname"] = "students";
            $class["url"] = $this->url;

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/student/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function delete_student($id) {
        // Switch to HTML output for admin interface
        $this->output->set_content_type('text/html');

        $this->load->model('faculty/common', 'faculty_common');

        // Use unified session approach for admin
        $unified_user = $this->session->userdata('user');
        if ($unified_user && isset($unified_user['user_type']) && $unified_user['user_type'] === 'faculty') {
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $unified_user;
        } else {
            $this->faculty_common->check_user_session($this->url);
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $this->session->userdata($this->url);
        }

        // Prevent deletion of own account - only applies to students, not administrators
        // Administrators (principals) should be able to delete students
        if ($this->session_data['role'] == ROLE_STUDENT && $id == $this->session_data['id']) {
            $this->session->set_flashdata('message', array('warning', "You cannot delete your own account."));
            redirect($this->url.'/students');
            return;
        }

        $result = $this->db_model->delete(TABLE_STUDENT, ["id" => $id]);
        $message = array('success', "Student deleted successfully!");
        if (!$result) {
            $message = array('danger', "Failed to delete student.");
        }
        $this->session->set_flashdata('message', $message);
        redirect($this->url.'/students');
    }

    public function reset_password_student() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('id', 'Id', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message',array("danger",validation_errors()));
                return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/students"));
            }
            $update = $this->db_model->update(TABLE_STUDENT,["password"=>password_hash($post['password'], PASSWORD_DEFAULT)],["is_active"=>1,"id"=>$post['id']]);
            if(!$update){
                $this->session->set_flashdata('message',array("danger","Something Went Wrong"));
                return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/students"));
            }
            $this->session->set_flashdata('message',array("success","Password Reset successfully"));
            return redirect($_SERVER['HTTP_REFERER'] ?? base_url($this->url."/students"));
        }
    }
}

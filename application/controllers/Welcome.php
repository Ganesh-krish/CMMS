<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('db_model');
        $this->load->library('session');
    }

    public function index() {
        // Check if user is already logged in
        if ($this->session->has_userdata('user')) {
            $this->redirect_based_on_role();
            return;
        }

        $data['error'] = $this->session->flashdata('error');
        $this->load->view('login', $data);
    }

    public function authenticate() {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        if (empty($email) || empty($password)) {
            $this->session->set_flashdata('error', 'Email and password are required');
            redirect('Welcome');
            return;
        }

        // Check all user types in order of priority
        $user = $this->authenticate_user($email, $password);

        if ($user) {
            // Set unified session
            $this->session->set_userdata('user', $user);
            $this->redirect_based_on_role();
        } else {
            $this->session->set_flashdata('error', 'Invalid email or password');
            redirect('Welcome');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('Welcome');
    }

    private function authenticate_user($email, $password) {
        // Priority order: Admin → Faculty → Students

        // 1. Check faculty table (includes admins, principals, etc.)
        $faculty = $this->db_model->get_row(TABLE_FACULTY, [
            'email' => $email,
            'is_active' => 1
        ]);

        if ($faculty && $this->verify_password($password, $faculty['password'])) {
            return [
                'id' => $faculty['id'],
                'name' => $faculty['name'],
                'email' => $faculty['email'],
                'role' => $faculty['role'],
                'department' => $faculty['department'] ?? null,
                'college_id' => $faculty['college_id'],
                'user_type' => 'faculty'
            ];
        }

        // 2. Check students table
        $student = $this->db_model->get_row(TABLE_STUDENT, [
            'email' => $email,
            'is_active' => 1
        ]);

        if ($student && $this->verify_password($password, $student['password'])) {
            return [
                'id' => $student['id'],
                'name' => $student['name'],
                'email' => $student['email'],
                'role' => 'student', // Add role field to students table
                'department' => $student['department'] ?? null,
                'college_id' => $student['college_id'],
                'user_type' => 'student'
            ];
        }

        return false;
    }

    private function verify_password($input, $stored) {
        // Handle both bcrypt and plain text passwords
        if (!empty($stored) && strlen($stored) > 30 && password_verify($input, $stored)) {
            return true;
        }
        if ($stored === $input) {
            return true;
        }
        return false;
    }

    private function redirect_based_on_role() {
        $user = $this->session->userdata('user');

        // Convert object to array if needed (session serialization can change data type)
        if (is_object($user)) {
            $user = (array) $user;
        }

        // Check if user data and role exist
        if (!isset($user['role']) || empty($user['role'])) {
            // Invalid session data, destroy session and redirect to login
            $this->session->unset_userdata('user');
            redirect('Welcome');
            return;
        }

        $role = $user['role'];

        switch ($role) {
            case ROLE_SUPERADMIN: // Principal
                redirect('portal/principal');
                break;

            case ROLE_VICE_PRINCIPAL:
                redirect('portal/principal/vice_principal');
                break;

            case ROLE_HOD:
                redirect('portal/hod');
                break;

            case ROLE_STAFF:
                redirect('portal/staff');
                break;

            case 'student':
                redirect('portal/student/dashboard');
                break;

            default:
                // Unknown role - destroy session and redirect to login
                $this->session->unset_userdata('user');
                $this->session->set_flashdata('error', 'Invalid user role. Please login again.');
                redirect('Welcome');
        }
    }
}

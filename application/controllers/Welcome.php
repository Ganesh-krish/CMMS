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

        // Get college information
        $college = $this->db_model->get_row(TABLE_COLLEGE, ['is_active' => 1]);
        $data['college'] = $college;

        $this->load->view('login', $data);
    }

    public function authenticate() {
        $username = $this->input->post('email'); // Username field (using email as username)
        $password = $this->input->post('password');

        if (empty($username) || empty($password)) {
            $this->session->set_flashdata('error', 'Username and password are required');
            redirect('Welcome');
            return;
        }

        // Check all user types in order of priority
        $user = $this->authenticate_user($username, $password);

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

    private function authenticate_user($username, $password) {
        // Check faculty table (includes admins, principals, staff, etc.) - using email as username
        $faculty = $this->db_model->get_row(TABLE_FACULTY, [
            'email' => $username,
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
            case ROLE_PRINCIPAL: // Principal
                redirect('panel/principal/dashboard');
                break;

            case ROLE_VICE_PRINCIPAL:
                redirect('panel/vice_principal/dashboard'); // Unified dashboard for vice-principal too
                break;

            case ROLE_HOD:
                redirect('panel/hod/dashboard'); // Unified dashboard adapts to role
                break;

            case ROLE_STAFF:
                redirect('panel/staff/dashboard'); // Unified dashboard adapts to role
                break;

            case ROLE_CUSTODIAN:
                redirect('panel/custodian/dashboard'); // Unified dashboard adapts to role
                break;

            case ROLE_STUDENT:
                redirect('panel/student/dashboard');
                break;

            default:
                // Unknown role - destroy session and redirect to login
                $this->session->unset_userdata('user');
                $this->session->set_flashdata('error', 'Invalid user role. Please login again.');
                redirect('Welcome');
        }
    }
}

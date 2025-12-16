<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    private $url;
    private $college;
    private $session_data;
    private $permissions;

    function __construct()
    {
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

        // Basic access check - all faculty roles can access dashboard
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        $allowed_roles = [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF, ROLE_CLERK_STAFF];
        if (!in_array($role, $allowed_roles, true)) {
            redirect('Welcome');
        }
    }

    // Main dashboard - adapts based on user role
    public function index()
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        $data["url"] = $this->url;
        $class["classname"] = "dashboard";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/dashboard");

        // Role-specific dashboard data
        if (in_array($role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL])) {
            // Principal/Vice-Principal dashboard
            $data = array_merge($data, $this->get_principal_dashboard_data());
            $view_file = 'faculty/faculty/principal';
        } elseif ($role == ROLE_HOD) {
            // HOD dashboard
            $data = array_merge($data, $this->get_hod_dashboard_data());
            $view_file = 'faculty/faculty/hod';
        } elseif (in_array($role, [ROLE_STAFF, ROLE_CLERK_STAFF])) {
            // Staff dashboard
            $data = array_merge($data, $this->get_staff_dashboard_data());
            $view_file = 'faculty/faculty/staff';
        } else {
            // Default dashboard
            $data = array_merge($data, $this->get_default_dashboard_data());
            $view_file = 'faculty/faculty/dashboard';
        }

        // Add permissions to all views
        $data["permissions"] = $this->permissions;

        $this->load->view('common/sidebar', $class);
        $this->load->view($view_file, $data);
        $this->load->view('common/footer');
    }

    // Unified view method - adapts based on permissions
    public function view()
    {
        if (!$this->permissions['can_view_admin_data']) {
            redirect($this->url.'/dashboard');
        }

        $data["url"] = $this->url;
        $class["classname"] = "view";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/view");

        // Get data based on user permissions
        $role = $this->session_data['role'];
        if (in_array($role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL])) {
            $data["title"] = "Administrative Overview";
            $data["stats"] = $this->get_admin_stats();
            $data["show_full_admin"] = true;
        } elseif ($role == ROLE_HOD) {
            $data["title"] = "Department Overview";
            $data["stats"] = $this->get_department_stats();
            $data["show_department_admin"] = true;
        } else {
            $data["title"] = "Staff Overview";
            $data["stats"] = $this->get_staff_stats();
            $data["show_staff_view"] = true;
        }

        $data["permissions"] = $this->permissions;

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/admin_view', $data); // Unified view
        $this->load->view('common/footer');
    }

    // Unified departments method
    public function departments()
    {
        if (!$this->permissions['can_view_departments']) {
            redirect($this->url.'/dashboard');
        }

        $data["url"] = $this->url;
        $class["classname"] = "departments";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/departments");

        $role = $this->session_data['role'];

        // Get departments based on permissions
        if (in_array($role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL])) {
            // Full access to all departments
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1]);
            $data["can_manage"] = true;
        } elseif ($role == ROLE_HOD) {
            // Limited access - own department
            $dept_id = $this->session_data['department'];
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["id" => $dept_id, "is_active" => 1]);
            $data["can_manage"] = false; // Read-only
        } else {
            // Staff - very limited access
            $data["departments"] = [];
            $data["can_manage"] = false;
        }

        $data["permissions"] = $this->permissions;

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/departments', $data);
        $this->load->view('common/footer');
    }

    // Unified profile method
    public function profile()
    {
        $data["url"] = $this->url;
        $class["classname"] = "profile";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/profile");

        $data["user"] = $this->session_data;
        $data["permissions"] = $this->permissions;

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/profile', $data);
        $this->load->view('common/footer');
    }

    // Helper methods for dashboard data
    private function get_principal_dashboard_data()
    {
        // Get comprehensive stats for principal
        $data["total_students"] = count($this->db_model->get_all(TABLE_STUDENT, ["is_active" => true]));
        $data["total_courses"] = count($this->db_model->get_all(TABLE_COURCES, ["is_active" => true]));
        $data["total_departments"] = count($this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => true]));
        $data["active_tests"] = 0; // Placeholder
        return $data;
    }

    private function get_hod_dashboard_data()
    {
        $dept_id = $this->session_data['department'];
        $data["department_students"] = count($this->db_model->get_all(TABLE_STUDENT, ["department" => $dept_id, "is_active" => true]));
        $data["department_courses"] = count($this->db_model->get_all(TABLE_COURCES, ["department" => $dept_id, "is_active" => true]));
        $data["department_name"] = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $dept_id])['name'] ?? 'Department';
        return $data;
    }

    private function get_staff_dashboard_data()
    {
        $staff_id = $this->session_data['id'];
        $data["my_courses"] = count($this->db_model->get_all(TABLE_COURCES, ["created_by" => $staff_id, "is_active" => true]));
        $data["my_students"] = count($this->db_model->get_all(TABLE_STUDENT, ["is_active" => true])); // Limited view
        return $data;
    }

    private function get_default_dashboard_data()
    {
        return ["message" => "Welcome to your dashboard"];
    }

    private function get_admin_stats()
    {
        return [
            "total_users" => count($this->db_model->get_all(TABLE_FACULTY, ["is_active" => 1])),
            "system_health" => "Good"
        ];
    }

    private function get_department_stats()
    {
        $dept_id = $this->session_data['department'];
        return [
            "department_performance" => "85%",
            "department_students" => count($this->db_model->get_all(TABLE_STUDENT, ["department" => $dept_id]))
        ];
    }

    private function get_staff_stats()
    {
        return [
            "assigned_tasks" => 5,
            "completed_tasks" => 3
        ];
    }

    // System Administration Features (SuperAdmin only)
    public function students()
    {
        // Only SuperAdmin can access system student management
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_SUPERADMIN) {
            redirect($this->url.'/dashboard');
        }

        $college_id = $this->college['id'] ?? SINGLE_COLLEGE_ID;

        // Handle AJAX requests for DataTables
        if ($this->input->get('draw') !== null || $this->input->is_ajax_request()) {
            $this->handle_ajax_students();
            return;
        }

        // Handle form submissions
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->handle_student_crud();
            return;
        }

        // Prepare data for the view
        $data = [
            'classname' => 'students',
            'sidebar_href' => base_url($this->url.'/dashboard'),
            'url' => $this->url,
            'departments' => $this->db_model->get_all(TABLE_DEPARTMENT, ['is_active' => 1, 'college_id' => $college_id]),
            'add_student_url' => base_url($this->url.'/add_student'),
            'edit_student_url' => base_url($this->url.'/edit_student'),
            'delete_student_url' => base_url($this->url.'/delete_student'),
            'memgroups' => $this->db_model->get_groupMembers($college_id)
        ];

        $data["permissions"] = $this->permissions;

        $this->load->view('common/sidebar', $data);
        $this->load->view('faculty/faculty/students', $data);
        $this->load->view('common/footer');
    }

    private function handle_ajax_students() {
        $college_id = $this->college['id'] ?? SINGLE_COLLEGE_ID;

        $limit = $this->input->get('length') ?? 10;
        $offset = $this->input->get('start') ?? 0;
        $search_value = $this->input->get('search')['value'] ?? '';

        // Get total count
        $total_count = $this->db_model->get_count(TABLE_STUDENT, ['college_id' => $college_id, 'is_active' => 1]);

        // Build query for filtered results
        $select = 's.id, s.name, s.email, s.phone, s.enrollment_no, s.created_at, d.name as department_name';
        $joins = [
            TABLE_DEPARTMENT . ' AS d' => 's.department = d.id'
        ];
        $conditions = ['s.college_id' => $college_id, 's.is_active' => 1];

        if (!empty($search_value)) {
            $conditions[] = "(s.name LIKE '%{$search_value}%' OR s.email LIKE '%{$search_value}%' OR s.enrollment_no LIKE '%{$search_value}%' OR d.name LIKE '%{$search_value}%')";
        }

        $filtered_count = $this->db_model->get_count_with_joins(TABLE_STUDENT . ' AS s', $joins, $conditions);

        $students = $this->db_model->get_with_joins(
            TABLE_STUDENT . ' AS s',
            $select,
            $joins,
            $conditions,
            's.created_at',
            'DESC',
            null,
            $limit,
            $offset
        );

        $response = [
            'draw' => intval($this->input->get('draw')),
            'recordsTotal' => $total_count,
            'recordsFiltered' => $filtered_count,
            'data' => array_map(function($student) {
                return [
                    $student['id'],
                    htmlspecialchars($student['name']),
                    htmlspecialchars($student['email']),
                    htmlspecialchars($student['enrollment_no']),
                    htmlspecialchars($student['department_name'] ?? 'N/A'),
                    htmlspecialchars($student['phone'] ?? 'N/A'),
                    date('Y-m-d', strtotime($student['created_at'])),
                    '<a href="' . base_url('portal/edit_student/' . $student['id']) . '" class="btn btn-sm btn-primary">Edit</a> ' .
                    '<a href="' . base_url('portal/delete_student/' . $student['id']) . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>'
                ];
            }, $students)
        ];

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    private function handle_student_crud() {
        $action = $this->input->post('action');

        switch ($action) {
            case 'create':
                $this->create_student();
                break;
            case 'update':
                $this->update_student();
                break;
            case 'delete':
                $this->delete_student_private();
                break;
        }
    }

    private function create_student() {
        $college_id = $this->college['id'] ?? SINGLE_COLLEGE_ID;

        $data = [
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'enrollment_no' => $this->input->post('enrollment_no'),
            'department' => $this->input->post('department'),
            'college_id' => $college_id,
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db_model->insert(TABLE_STUDENT, $data);
        $this->session->set_flashdata('message', [1, 'Student created successfully']);
        redirect($this->url.'/students');
    }

    private function update_student() {
        $id = $this->input->post('id');

        $data = [
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'enrollment_no' => $this->input->post('enrollment_no'),
            'department' => $this->input->post('department')
        ];

        if ($this->input->post('password')) {
            $data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        }

        $this->db_model->update(TABLE_STUDENT, $data, ['id' => $id]);
        $this->session->set_flashdata('message', [1, 'Student updated successfully']);
        redirect($this->url.'/students');
    }

    private function delete_student_private() {
        $id = $this->input->post('id');
        $this->db_model->update(TABLE_STUDENT, ['is_active' => 0], ['id' => $id]);
        $this->session->set_flashdata('message', [1, 'Student deleted successfully']);
        redirect($this->url.'/students');
    }

    public function add_student() {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_SUPERADMIN) {
            redirect($this->url.'/dashboard');
        }

        $college_id = $this->college['id'] ?? SINGLE_COLLEGE_ID;

        $data = [
            'classname' => 'students',
            'sidebar_href' => base_url($this->url.'/dashboard'),
            'url' => $this->url,
            'departments' => $this->db_model->get_all(TABLE_DEPARTMENT, ['is_active' => 1, 'college_id' => $college_id])
        ];

        $data["permissions"] = $this->permissions;

        $this->load->view('common/sidebar', $data);
        $this->load->view('faculty/add_student', $data);
        $this->load->view('common/footer');
    }

    public function edit_student($id) {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_SUPERADMIN) {
            redirect($this->url.'/dashboard');
        }

        $college_id = $this->college['id'] ?? SINGLE_COLLEGE_ID;

        $student = $this->db_model->get_row(TABLE_STUDENT, ['id' => $id, 'college_id' => $college_id]);
        if (!$student) {
            show_404();
        }

        $data = [
            'classname' => 'students',
            'sidebar_href' => base_url($this->url.'/dashboard'),
            'url' => $this->url,
            'student' => $student,
            'departments' => $this->db_model->get_all(TABLE_DEPARTMENT, ['is_active' => 1, 'college_id' => $college_id])
        ];

        $data["permissions"] = $this->permissions;

        $this->load->view('common/sidebar', $data);
        $this->load->view('faculty/edit_student', $data);
        $this->load->view('common/footer');
    }

    public function delete_student($id) {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_SUPERADMIN) {
            redirect($this->url.'/dashboard');
        }

        $this->db_model->update(TABLE_STUDENT, ['is_active' => 0], ['id' => $id]);
        $this->session->set_flashdata('message', [1, 'Student deleted successfully']);
        redirect($this->url.'/students');
    }

    public function get_student($id) {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_SUPERADMIN) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $college_id = $this->college['id'] ?? SINGLE_COLLEGE_ID;
        $student = $this->db_model->get_row(TABLE_STUDENT, ['id' => $id, 'college_id' => $college_id]);

        $this->output->set_content_type('application/json')->set_output(json_encode($student));
    }
}
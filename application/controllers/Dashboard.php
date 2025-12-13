<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	function __construct() {
        parent::__construct();
        $this->user_session = $this->rbac->require_owner();
        // Refresh owner session from DB for latest profile values
        $this->common->check_user_session();

        // Load required libraries and models
        $this->load->library('form_validation');
        $this->load->model('faculty/db_model', 'db_model');
        $this->load->model('faculty/common', 'common');
    }

	public function index() {
        if ($this->input->is_ajax_request()) {
            // Server-side processing
            $limit = $this->input->get('length')??15;
            $offset = $this->input->get('start')??0;
			// print_r($limit);
			// exit;
            $search_value = $this->input->get('search')['value'];

            $joins = [
                TABLE_STUDENT_TEST_SUBMISSION . ' AS sts' => 's.id = sts.student_id',
                TABLE_COLLEGE . ' AS c' => 's.college_id = c.id',
                TABLE_DEPARTMENT . ' AS d' => 's.department = d.id'
            ];

            $select = "s.registration_number AS reg_no, 
                s.name AS student_name, 
                s.email, 
                s.phone_number, 
                s.batch,
                d.name AS department_name, 
                c.name AS college_name, 
                COALESCE(SUM(sts.earned_score), 0) AS score";

            $conditions = ['s.is_active' => 1];

            if (!empty($search_value)) {
                $this->db->group_start()
                    ->like('s.registration_number', $search_value)
                    ->or_like('s.name', $search_value)
                    ->or_like('s.email', $search_value)
                    ->or_like('s.phone_number', $search_value)
                    ->or_like('c.name', $search_value)
                    ->or_like('d.name', $search_value)
                    ->group_end();
            }

            $group_by = 's.id';

            $data = $this->db_model->get_with_joins(
                TABLE_STUDENT . ' AS s',
                $select,
                $joins,
                $conditions,
                'score',
                'DESC',
                $group_by,
                $limit,
                $offset
            );

            $total_records = $this->db_model->get_with_joins(
                TABLE_STUDENT . ' AS s',
                'COUNT(DISTINCT s.id) AS count',
                $joins,
                $conditions,
                null,
                null,
                null
            );

            $response = [
                'draw' => intval($this->input->get('draw')),
                'recordsTotal' => $total_records[0]['count'],
                'recordsFiltered' => $total_records[0]['count'],
                'data' => $data
            ];

            echo json_encode($response);
            return;
        }

        // Normal view render for initial load (reuse faculty layout with safe defaults)
        $url_key = 'admin';
        // Ensure session data exists for sidebar expectations
        $this->session->set_userdata($url_key, [
            'role' => ROLE_SUPERADMIN,
            'name' => $this->user_session['name'] ?? 'Admin'
        ]);

        $data = [
            'classname' => 'dashboard',
            'sidebar_href' => base_url('Dashboard'),
            'url' => $url_key,
            // Dashboard metrics (set safe defaults)
            'total_students' => 0,
            'total_courses' => 0,
            'active_tests' => 0,
            'total_questions' => 0,
            'code_questions' => 0,
            'mcq_questions' => 0,
            'easy_questions_percent' => 0,
            'medium_questions_percent' => 0,
            'hard_questions_percent' => 0,
            'department_batch_table' => [
                'years' => [],
                'departments' => []
            ],
            'manage_student_url' => base_url('Dashboard')
        ];

        $this->load->view('faculty/faculty/sidebar', $data);
        $this->load->view('faculty/faculty/dashboard', $data);
        $this->load->view('faculty/faculty/footer');
    }

    public function students() {
        // Load required models
        $this->load->model('faculty/db_model', 'db_model');
        $this->load->model('faculty/common', 'common');

        // Get college ID (for admin, use default college)
        $college_id = 1; // Default college ID

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
            'sidebar_href' => base_url('Dashboard'),
            'url' => 'admin',
            'departments' => $this->db_model->get_all(TABLE_DEPARTMENT, ['is_active' => 1, 'college_id' => $college_id]),
            'add_student_url' => base_url('Dashboard/add_student'),
            'edit_student_url' => base_url('Dashboard/edit_student'),
            'delete_student_url' => base_url('Dashboard/delete_student')
        ];

        // Ensure session data exists for sidebar
        $this->session->set_userdata('admin', [
            'role' => ROLE_SUPERADMIN,
            'name' => $this->user_session['name'] ?? 'Admin'
        ]);

        $this->load->view('faculty/faculty/sidebar', $data);
        $this->load->view('faculty/faculty/students', $data);
        $this->load->view('faculty/faculty/footer');
    }

    private function handle_ajax_students() {
        $college_id = 1; // Default college ID

        // Debug: Log that AJAX method is called
        log_message('debug', 'handle_ajax_students called with draw: ' . $this->input->get('draw'));

        $limit = $this->input->get('length') ?? 10;
        $offset = $this->input->get('start') ?? 0;
        $search_value = $this->input->get('search')['value'] ?? '';

        // Get total count
        $total_records = $this->db_model->count(TABLE_STUDENT, ['is_active' => 1, 'college_id' => $college_id]);

        // Debug: Log the count
        log_message('debug', 'Total students found: ' . $total_records);

        // Get paginated data with department join using db_model
        $conditions = ['s.is_active' => 1, 's.college_id' => $college_id];

        // Add search conditions
        if (!empty($search_value)) {
            $conditions['s.name LIKE'] = '%' . $search_value . '%';
        }

        // Use db_model's get_with_joins method for consistency
        $select = 's.id, s.name, s.email, s.phone, s.roll_no, s.batch, d.name as department_name';
        $joins = [
            TABLE_DEPARTMENT . ' d' => 's.department = d.id'
        ];

        $students = $this->db_model->get_with_joins(
            TABLE_STUDENT . ' s',
            $select,
            $joins,
            $conditions,
            null, // order_by
            'ASC', // order_direction
            null, // group_by
            $limit,
            $offset
        );

        // Format data for DataTables
        $data = [];
        foreach ($students as $student) {
            $data[] = [
                $student['id'],
                $student['name'],
                $student['email'],
                $student['phone'] ?? '-',
                $student['roll_no'] ?? '-',
                $student['department_name'] ?? '-',
                $student['batch'] ?? '-',
                '<button class="btn btn-sm btn-info" onclick="editStudent(' . $student['id'] . ')">Edit</button>
                 <button class="btn btn-sm btn-danger" onclick="deleteStudent(' . $student['id'] . ')">Delete</button>'
            ];
        }

        $response = [
            'draw' => intval($this->input->get('draw')),
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $data
        ];

        echo json_encode($response);
    }

    private function handle_student_crud() {
        $action = $this->input->post('action') ?? 'create';

        switch ($action) {
            case 'create':
                $this->create_student();
                break;
            case 'update':
                $this->update_student();
                break;
            case 'delete':
                $this->delete_student();
                break;
        }
    }

    private function create_student() {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('phone_number', 'Phone', 'trim');
        $this->form_validation->set_rules('department', 'Department', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', ['danger', validation_errors()]);
            redirect('Dashboard/students');
            return;
        }

        // Check if email already exists
        $existing_student = $this->db_model->get_row(TABLE_STUDENT, ['email' => $this->input->post('email')]);
        if ($existing_student) {
            $this->session->set_flashdata('message', ['danger', 'Email already exists']);
            redirect('Dashboard/students');
            return;
        }

        $data = [
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone_number'),
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'roll_no' => $this->input->post('roll_no'),
            'department' => $this->input->post('department'),
            'batch' => $this->input->post('batch'),
            'college_id' => 1, // Default college ID
            'is_active' => 1,
            'created_by' => 1 // Admin user ID
        ];

        if ($this->db_model->insert(TABLE_STUDENT, $data)) {
            $this->session->set_flashdata('message', ['success', 'Student created successfully']);
        } else {
            $this->session->set_flashdata('message', ['danger', 'Failed to create student']);
        }

        redirect('Dashboard/students');
    }

    private function update_student() {
        $id = $this->input->post('id');
        if (!$id) {
            $this->session->set_flashdata('message', ['danger', 'Student ID is required']);
            redirect('Dashboard/students');
            return;
        }

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('phone_number', 'Phone', 'trim');
        $this->form_validation->set_rules('department', 'Department', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', ['danger', validation_errors()]);
            redirect('Dashboard/students');
            return;
        }

        // Check if email already exists for another student
        $existing_student = $this->db_model->get_row(TABLE_STUDENT, ['email' => $this->input->post('email'), 'id !=' => $id]);
        if ($existing_student) {
            $this->session->set_flashdata('message', ['danger', 'Email already exists for another student']);
            redirect('Dashboard/students');
            return;
        }

        $data = [
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone_number'),
            'roll_no' => $this->input->post('roll_no'),
            'department' => $this->input->post('department'),
            'batch' => $this->input->post('batch'),
            'updated_by' => 1 // Admin user ID
        ];

        if ($this->db_model->update(TABLE_STUDENT, $data, ['id' => $id])) {
            $this->session->set_flashdata('message', ['success', 'Student updated successfully']);
        } else {
            $this->session->set_flashdata('message', ['danger', 'Failed to update student']);
        }

        redirect('Dashboard/students');
    }

    private function delete_student_private() {
        $id = $this->input->post('id');
        if (!$id) {
            $this->session->set_flashdata('message', ['danger', 'Student ID is required']);
            redirect('Dashboard/students');
            return;
        }

        if ($this->db_model->update(TABLE_STUDENT, ['is_active' => 0], ['id' => $id])) {
            $this->session->set_flashdata('message', ['success', 'Student deleted successfully']);
        } else {
            $this->session->set_flashdata('message', ['danger', 'Failed to delete student']);
        }

        redirect('Dashboard/students');
    }

    // Additional methods for form handling
    public function add_student() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->create_student();
        } else {
            redirect('Dashboard/students');
        }
    }

    public function edit_student() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->update_student();
        } else {
            redirect('Dashboard/students');
        }
    }

    public function delete_student() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->delete_student_private();
        } else {
            redirect('Dashboard/students');
        }
    }

    public function get_student($id) {
        $this->load->model('faculty/db_model', 'db_model');

        $student = $this->db_model->get_row(TABLE_STUDENT, ['id' => $id]);

        if ($student) {
            echo json_encode($student);
        } else {
            echo json_encode(['error' => 'Student not found']);
        }
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	function __construct() {
        parent::__construct();
        $this->user_session = $this->rbac->require_owner();
        // Refresh owner session from DB for latest profile values
        $this->common->check_user_session();
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
            'designation' => DESIGNATION_PRINCIPAL,
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
}

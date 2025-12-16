<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends CI_Controller
{
    private $url;
    private $college;
    private $session_data;
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('common', 'common');
        $this->load->model('Db_model', 'db_model');
        $this->load->model('Test_model', 'test_model');
        // Check for owner/admin session first
        $this->user_session = $this->session->userdata('owner');
        if ($this->user_session) {
            // Admin/owner access
            $this->url = 'admin';
            $this->college = $this->common->get_default_college();
            $this->session_data = [
                'id' => $this->user_session['id'],
                'role' => ROLE_SUPERADMIN,
                'designation' => DESIGNATION_PRINCIPAL,
                'department' => null,
                'college_id' => $this->college['id'] ?? SINGLE_COLLEGE_ID
            ];
        } else {
            // Faculty session - find the active session
            $possible_keys = ['admin', 'staff', 'hod', 'principal'];
            $this->url = null;
            foreach ($possible_keys as $key) {
                if ($this->session->userdata($key)) {
                    $this->url = $key;
                    break;
                }
            }

            if (!$this->url) {
                redirect(base_url('OAuth'));
            }

            $this->common->check_user_session($this->url);
            $this->college = $this->common->get_default_college();
            $this->session_data = $this->session->userdata($this->url);
        }
        $this->permissions = $this->common->get_access_permissions($this->session_data);
    }

    private function respond($status, $data = [], $code = 200)
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => $status, 'data' => $data]));
    }

    public function index()
    {
        $data["url"] = $this->url;
        $class["classname"] = "reports";
        $class["url"] = $this->url;
        $class["college_id"] = $this->college['id'];
        
        // Determine the path based on user designation
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[$this->session_data['designation']] ?? 'hod';
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        
        // Get filter parameters
        $student_id = $this->input->get('student_id');
        $test_id = $this->input->get('test_id');
        $course_id = $this->input->get('course_id');
        
        // Load necessary data for filters
        $data['students'] = $this->db_model->get_all(
            TABLE_STUDENT,
            ['college_id' => $this->college['id'], 'is_active' => 1]
        );
        
        $data['courses'] = $this->db_model->get_all(
            TABLE_COURCES,
            ['college_id' => $this->college['id'], 'is_active' => 1]
        );
        
        // Build query for course enrollments with joins
        $this->db->select('
            course_enrollments.*,
            students.name as student_name,
            students.email as student_email,
            students.batch,
            students.department,
            courses.name as course_title,
            courses.description as course_description
        ');

        $this->db->from(TABLE_COURSE_ENROLLMENTS);

        $this->db->join(
            TABLE_STUDENT . ' as students',
            'course_enrollments.student_id = students.id',
            'inner'
        );

        $this->db->join(
            TABLE_COURCES . ' as courses',
            'course_enrollments.course_id = courses.id',
            'inner'
        );

        // Apply filters based on parameters
        $this->db->where('courses.college_id', $this->college['id']);

        if (!empty($student_id)) {
            $this->db->where('course_enrollments.student_id', $student_id);
        }

        if (!empty($course_id)) {
            $this->db->where('course_enrollments.course_id', $course_id);
        }

        // Execute query
        $data['enrollments'] = $this->db->get()->result_array();
        
        // Calculate additional statistics for enrollments
        $total_enrollments = count($data['enrollments']);
        $active_enrollments = 0;

        if ($total_enrollments > 0) {
            foreach ($data['enrollments'] as $enrollment) {
                if ($enrollment['is_active'] == 1) {
                    $active_enrollments++;
                }
            }
        }

        $data['total_submissions'] = $total_enrollments; // Keep for backward compatibility with view
        $data['pass_count'] = $active_enrollments; // Keep for backward compatibility with view
        $data['pass_rate'] = $total_enrollments > 0 ? ($active_enrollments / $total_enrollments) * 100 : 0;
        $data['avg_score'] = 0; // Not applicable for enrollments
        
        // Load views
        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/reports/index', $data);
        $this->load->view('common/footer');
    }
    
    public function student_detail($student_id)
    {
        if (empty($student_id)) {
            $this->session->set_flashdata('message', array('danger', 'Student ID is required'));
            redirect($this->url . '/report');
        }
        
        $data["url"] = $this->url;
        $class["classname"] = "reports";
        $class["url"] = $this->url;
        $class["college_id"] = $this->college['id'];
        
        // Determine the path based on user designation
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[$this->session_data['designation']] ?? 'hod';
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        
        // Get student details
        $data['student'] = $this->db_model->get_row(TABLE_STUDENT, ['id' => $student_id, 'college_id' => $this->college['id']]);

        $data['student']['department'] = $this->db_model->get_row(TABLE_DEPARTMENT, ['id' => $data['student']['department']])['name'];
        
        if (!$data['student']) {
            $this->session->set_flashdata('message', array('danger', 'Student not found'));
            redirect($this->url . '/report');
        }
        
        // Get courses the student is enrolled in
        $this->db->select('
            courses.id,
            courses.name,
            courses.course_code,
            courses.start_date,
            courses.end_date
        ');
        
        $this->db->from(TABLE_COURSE_STUDENTS . ' as course_students');
        
        $this->db->join(
            TABLE_COURCES . ' as courses',
            'course_students.course_id = courses.id',
            'inner'
        );
        
        $this->db->where('course_students.student_id', $student_id);
        $this->db->where('courses.is_active', 1);
        
        $data['courses'] = $this->db->get()->result_array();
        
        // Calculate performance metrics
        $total_submissions = count($data['submissions']);
        $completed_tests = 0;
        $total_score = 0;
        $passed_tests = 0;
        
        foreach ($data['submissions'] as $submission) {
            if ($submission['finished'] == 1) {
                $completed_tests++;
                $total_score += $submission['percentage'];
                
                // Assuming 60% as passing score
                if ($submission['percentage'] >= 60) {
                    $passed_tests++;
                }
            }
        }
        
        $data['total_submissions'] = $total_submissions;
        $data['completed_tests'] = $completed_tests;
        $data['avg_score'] = $completed_tests > 0 ? $total_score / $completed_tests : 0;
        $data['pass_rate'] = $completed_tests > 0 ? ($passed_tests / $completed_tests) * 100 : 0;
        
        // Get test modules for reference
        $test_modules = $this->db_model->get_all(TABLE_TEST_MODULES, ['is_active' => 1]);
        $module_map = [];
        foreach ($test_modules as $module) {
            $module_map[$module['id']] = $module['name'];
        }
        
        $data['module_map'] = $module_map;
        
        // Load views
        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/reports/student_detail', $data);
        $this->load->view('common/footer');
    }
    
    public function course_detail($course_id)
    {
        if (empty($course_id)) {
            $this->session->set_flashdata('message', array('danger', 'Course ID is required'));
            redirect($this->url . '/report');
        }
        
        $data["url"] = $this->url;
        $class["classname"] = "reports";
        $class["url"] = $this->url;
        $class["college_id"] = $this->college['id'];
        
        // Determine the path based on user designation
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[$this->session_data['designation']] ?? 'hod';
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        
        // Get course details
        $data['course'] = $this->db_model->get_row(TABLE_COURCES, ['id' => $course_id]);
        
        if (!$data['course']) {
            $this->session->set_flashdata('message', array('danger', 'Course not found'));
            redirect($this->url . '/report');
        }
        
        // Get students enrolled in this course
        $this->db->select('
            students.id,
            students.name,
            students.email,
            students.joining_date
        ');
        
        $this->db->from(TABLE_COURSE_STUDENTS . ' as course_students');
        
        $this->db->join(
            TABLE_STUDENT . ' as students',
            'course_students.student_id = students.id',
            'inner'
        );
        
        $this->db->where('course_students.course_id', $course_id);
        $this->db->where('course_students.is_active', 1);
        $this->db->where('students.is_active', 1);
        
        $data['students'] = $this->db->get()->result_array();
        
        // Get tests associated with this course
        $this->db->select('
            tests.id,
            tests.title,
            tests.start_date,
            tests.end_date,
            tests.module_id
        ');
        
        $this->db->from(TABLE_COURSE_TESTS . ' as course_tests');
        
        $this->db->join(
            TABLE_TESTS . ' as tests',
            'course_tests.test_id = tests.id',
            'inner'
        );
        
        $this->db->where('course_tests.course_id', $course_id);
        $this->db->where('course_tests.is_active', 1);
        $this->db->where('tests.is_active', 1);
        
        $data['tests'] = $this->db->get()->result_array();
        
        // Get test modules for reference
        $test_modules = $this->db_model->get_all(TABLE_TEST_MODULES, ['is_active' => 1]);
        $module_map = [];
        foreach ($test_modules as $module) {
            $module_map[$module['id']] = $module['name'];
        }
        
        $data['module_map'] = $module_map;
        
        // Calculate performance metrics for each test
        foreach ($data['tests'] as &$test) {
            $this->db->select('COUNT(*) as total_submissions, AVG(percentage) as avg_score');
            $this->db->from('student_test_submission');
            $this->db->where('test_id', $test['id']);
            $this->db->where('finished', 1);
            
            $result = $this->db->get()->row_array();
            
            $test['submission_count'] = $result ? $result['total_submissions'] : 0;
            $test['avg_score'] = $result ? $result['avg_score'] : 0;
        }
        
        // Count active students
        $data['total_students'] = count($data['students']);
        $data['total_tests'] = count($data['tests']);
        
        // Load views
        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/reports/course_detail', $data);
        $this->load->view('common/footer');
    }
    
    // Export functions for different report types
    public function export_csv($type, $id = null)
    {
        // Get data based on report type
        $data = [];
        $filename = 'report_' . date('Y-m-d') . '.csv';
        
        switch ($type) {
            case 'all':
                // Export all course enrollments
                $this->db->select('
                    students.name as student_name,
                    students.email as student_email,
                    students.batch,
                    courses.name as course_title,
                    course_enrollments.enrolled_at,
                    course_enrollments.is_active
                ');

                $this->db->from(TABLE_COURSE_ENROLLMENTS);

                $this->db->join(
                    TABLE_STUDENT . ' as students',
                    'course_enrollments.student_id = students.id',
                    'inner'
                );

                $this->db->join(
                    TABLE_COURCES . ' as courses',
                    'course_enrollments.course_id = courses.id',
                    'inner'
                );

                $this->db->where('courses.college_id', $this->college['id']);

                $data = $this->db->get()->result_array();
                $filename = 'all_enrollments_' . date('Y-m-d') . '.csv';
                break;
                
            case 'student':
                // Export student course enrollments
                if (!$id) {
                    $this->session->set_flashdata('message', array('danger', 'Student ID is required'));
                    redirect($this->url . '/report');
                }

                $student = $this->db_model->get_row(TABLE_STUDENT,['id' => $id]);

                $this->db->select('
                    courses.name as course_title,
                    course_enrollments.enrolled_at,
                    course_enrollments.is_active,
                    course_enrollments.status
                ');

                $this->db->from(TABLE_COURSE_ENROLLMENTS);

                $this->db->join(
                    TABLE_COURCES . ' as courses',
                    'course_enrollments.course_id = courses.id',
                    'inner'
                );

                $this->db->where('course_enrollments.student_id', $id);

                $data = $this->db->get()->result_array();

                $filename = 'student_' . preg_replace('/[^A-Za-z0-9]/', '_', $student['name']) . '_enrollments_' . date('Y-m-d') . '.csv';
                break;
                
            case 'course':
                // Export course enrollments
                if (!$id) {
                    $this->session->set_flashdata('message', array('danger', 'Course ID is required'));
                    redirect($this->url . '/report');
                }

                $course = $this->db_model->get_row(TABLE_COURCES, ['id' => $id]);

                $this->db->select('
                    students.name as student_name,
                    students.email as student_email,
                    course_enrollments.enrolled_at,
                    course_enrollments.is_active,
                    course_enrollments.status
                ');

                $this->db->from(TABLE_COURSE_ENROLLMENTS);

                $this->db->join(
                    TABLE_STUDENT . ' as students',
                    'course_enrollments.student_id = students.id',
                    'inner'
                );

                $this->db->where('course_enrollments.course_id', $id);
                
                $data = $this->db->get()->result_array();

                $filename = 'course_' . preg_replace('/[^A-Za-z0-9]/', '_', $course['name']) . '_enrollments_' . date('Y-m-d') . '.csv';
                break;
                
        }
        
        // Generate and output CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add column headers
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
    }


    public function dashboard()
    {
        $data["url"] = $this->url;
        $class["classname"] = "reports";
        $class["url"] = $this->url;
        $class["college_id"] = $this->college['id'];
        
        // Determine the path based on user designation
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[$this->session_data['designation']] ?? 'hod';
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
        
        // Get filter parameters
        $course_id = $this->input->get('course_id');
        $module_id = $this->input->get('module_id');
        $date_range = $this->input->get('date_range');
        
        // Initialize all data variables to prevent undefined index errors
        $this->initialize_dashboard_data($data);
        
        // Process date range filter
        $start_date = null;
        $end_date = null;
        
        if (!empty($date_range)) {
            $date_parts = explode(' - ', $date_range);
            if (count($date_parts) == 2) {
                $start_date = date('Y-m-d 00:00:00', strtotime($date_parts[0]));
                $end_date = date('Y-m-d 23:59:59', strtotime($date_parts[1]));
            }
        }
        
        // Load necessary data for filters
        $data['courses'] = $this->db_model->get_all(
            TABLE_COURCES,
            ['college_id' => $this->college['id'], 'is_active' => 1]
        );
        
        $data['modules'] = $this->db_model->get_all(
            TABLE_TEST_MODULES,
            ['is_active' => 1]
        );
        
        // Build base query with where conditions based on filters
        $where_conditions = [];
        
        if (!empty($course_id)) {
            $where_conditions['course_id'] = $course_id;
        }
        
        if (!empty($module_id)) {
            $where_conditions['tests.module_id'] = $module_id;
        }
        
        if ($start_date && $end_date) {
            $where_conditions['student_test_submission.submission_time >='] = $start_date;
            $where_conditions['student_test_submission.submission_time <='] = $end_date;
        }
        
        // Get overall statistics
        $this->db->select('
            COUNT(DISTINCT tests.id) as total_tests,
            COUNT(DISTINCT student_test_submission.student_id) as total_students,
            COUNT(DISTINCT student_test_submission.id) as total_submissions,
            SUM(CASE WHEN student_test_submission.finished = 1 THEN 1 ELSE 0 END) as completed_tests,
            AVG(CASE WHEN student_test_submission.finished = 1 THEN student_test_submission.percentage ELSE NULL END) as overall_avg_score,
            AVG(student_test_submission.tab_changes) as avg_tab_switches,
            MIN(student_test_submission.tab_changes) as min_tab_switches,
            MAX(student_test_submission.tab_changes) as max_tab_switches
        ');
        
        $this->db->from('student_test_submission');
        
        $this->db->join(
            TABLE_TESTS . ' as tests',
            'student_test_submission.test_id = tests.id',
            'inner'
        );
        
        if (!empty($course_id)) {
            $this->db->join(
                TABLE_COURSE_TESTS . ' as course_tests',
                'tests.id = course_tests.test_id',
                'inner'
            );
        }
        
        foreach ($where_conditions as $key => $value) {
            $this->db->where($key, $value);
        }
        
        $stats = $this->db->get()->row_array();
        
        // Handle empty stats (when no data)
        if (!$stats) {
            $stats = [
                'total_tests' => 0,
                'total_students' => 0,
                'total_submissions' => 0,
                'completed_tests' => 0,
                'overall_avg_score' => 0,
                'avg_tab_switches' => 0,
                'min_tab_switches' => 0,
                'max_tab_switches' => 0
            ];
        }
        
        // Calculate derived statistics
        $data['total_tests'] = $stats['total_tests'] ?? 0;
        $data['total_students'] = $stats['total_students'] ?? 0;
        $data['total_submissions'] = $stats['total_submissions'] ?? 0;
        $data['completed_tests'] = $stats['completed_tests'] ?? 0;
        
        // Calculate test status counts
        $this->db->select('
            COUNT(CASE WHEN student_test_submission.finished = 1 THEN 1 ELSE NULL END) as completed_count,
            COUNT(CASE WHEN student_test_submission.finished = 0 AND student_test_submission.id IS NOT NULL THEN 1 ELSE NULL END) as in_progress_count
        ');
        
        $this->db->from(TABLE_TESTS . ' as tests');
        
        $this->db->join(
            'student_test_submission',
            'tests.id = student_test_submission.test_id',
            'left'
        );
        
        if (!empty($course_id)) {
            $this->db->join(
                TABLE_COURSE_TESTS . ' as course_tests',
                'tests.id = course_tests.test_id',
                'inner'
            );
            $this->db->where('course_tests.course_id', $course_id);
        }
        
        if (!empty($module_id)) {
            $this->db->where('tests.module_id', $module_id);
        }
        
        $this->db->where('tests.college_id', $this->college['id']);
        $this->db->where('tests.is_active', 1);
        
        $test_status = $this->db->get()->row_array();
        
        // Handle empty test status
        if (!$test_status) {
            $test_status = [
                'completed_count' => 0,
                'in_progress_count' => 0
            ];
        }
        
        $data['completed_tests'] = $test_status['completed_count'] ?? 0;
        $data['in_progress_tests'] = $test_status['in_progress_count'] ?? 0;
        $data['not_started_tests'] = $data['total_tests'] - ($data['completed_tests'] + $data['in_progress_tests']);
        
        // Calculate percentages
        $data['overall_avg_score'] = $stats['overall_avg_score'] ?? 0;
        $data['completion_rate'] = ($data['total_tests'] > 0) ? 
            ($data['completed_tests'] / $data['total_tests']) * 100 : 0;
        $data['in_progress_rate'] = ($data['total_tests'] > 0) ? 
            ($data['in_progress_tests'] / $data['total_tests']) * 100 : 0;
        $data['not_started_rate'] = ($data['total_tests'] > 0) ? 
            ($data['not_started_tests'] / $data['total_tests']) * 100 : 0;
        
        // Tab switch statistics
        $data['avg_tab_switches'] = $stats['avg_tab_switches'] ?? 0;
        $data['min_tab_switches'] = $stats['min_tab_switches'] ?? 0;
        $data['max_tab_switches'] = $stats['max_tab_switches'] ?? 0;
        
        // Calculate median tab switches - requires additional query
        $this->db->select('tab_changes');
        $this->db->from('student_test_submission');
        $this->db->order_by('tab_changes', 'ASC');
        
        if (!empty($where_conditions)) {
            foreach ($where_conditions as $key => $value) {
                $this->db->where($key, $value);
            }
        }
        
        $tab_changes_result = $this->db->get()->result_array();
        $tab_changes = !empty($tab_changes_result) ? array_column($tab_changes_result, 'tab_changes') : [0];
        
        $data['median_tab_switches'] = $this->calculate_median($tab_changes);
        
        // Tab switch distribution
        $tab_switch_distribution = array_fill(0, 7, 0);
        foreach ($tab_changes as $switches) {
            $index = min((int)$switches, 6);
            $tab_switch_distribution[$index]++;
        }
        
        $data['tab_switch_distribution'] = $tab_switch_distribution;
        
        // Count students exceeding tab switch limit
        $max_tab_switch_limit = 3; // Configurable threshold
        $exceeded_tab_switch_count = count(array_filter($tab_changes, function($switches) use ($max_tab_switch_limit) {
            return $switches > $max_tab_switch_limit;
        }));
        
        $data['exceeded_tab_switch_count'] = $exceeded_tab_switch_count;
        $data['exceeded_tab_switch_percentage'] = ($data['total_students'] > 0) ? 
            ($exceeded_tab_switch_count / $data['total_students']) * 100 : 0;
        
        // Calculate tab switch correlation with performance
        $this->db->select('
            AVG(CASE WHEN tab_changes = 0 THEN percentage ELSE NULL END) as tab_switch_0_avg_score,
            COUNT(CASE WHEN tab_changes = 0 THEN 1 ELSE NULL END) as tab_switch_0_count,
            SUM(CASE WHEN tab_changes = 0 AND percentage >= 60 THEN 1 ELSE 0 END) as tab_switch_0_pass_count,
            
            AVG(CASE WHEN tab_changes BETWEEN 1 AND 2 THEN percentage ELSE NULL END) as tab_switch_1_2_avg_score,
            COUNT(CASE WHEN tab_changes BETWEEN 1 AND 2 THEN 1 ELSE NULL END) as tab_switch_1_2_count,
            SUM(CASE WHEN tab_changes BETWEEN 1 AND 2 AND percentage >= 60 THEN 1 ELSE 0 END) as tab_switch_1_2_pass_count,
            
            AVG(CASE WHEN tab_changes BETWEEN 3 AND 5 THEN percentage ELSE NULL END) as tab_switch_3_5_avg_score,
            COUNT(CASE WHEN tab_changes BETWEEN 3 AND 5 THEN 1 ELSE NULL END) as tab_switch_3_5_count,
            SUM(CASE WHEN tab_changes BETWEEN 3 AND 5 AND percentage >= 60 THEN 1 ELSE 0 END) as tab_switch_3_5_pass_count,
            
            AVG(CASE WHEN tab_changes >= 6 THEN percentage ELSE NULL END) as tab_switch_6_plus_avg_score,
            COUNT(CASE WHEN tab_changes >= 6 THEN 1 ELSE NULL END) as tab_switch_6_plus_count,
            SUM(CASE WHEN tab_changes >= 6 AND percentage >= 60 THEN 1 ELSE 0 END) as tab_switch_6_plus_pass_count
        ');
        
        $this->db->from('student_test_submission');
        $this->db->where('finished', 1);
        
        if (!empty($where_conditions)) {
            foreach ($where_conditions as $key => $value) {
                if ($key != 'student_test_submission.finished') {
                    $this->db->where($key, $value);
                }
            }
        }
        
        $tab_switch_stats = $this->db->get()->row_array();
        
        // Handle empty tab switch stats
        if (!$tab_switch_stats) {
            $tab_switch_stats = [
                'tab_switch_0_avg_score' => 0,
                'tab_switch_0_count' => 0,
                'tab_switch_0_pass_count' => 0,
                'tab_switch_1_2_avg_score' => 0,
                'tab_switch_1_2_count' => 0,
                'tab_switch_1_2_pass_count' => 0,
                'tab_switch_3_5_avg_score' => 0,
                'tab_switch_3_5_count' => 0,
                'tab_switch_3_5_pass_count' => 0,
                'tab_switch_6_plus_avg_score' => 0,
                'tab_switch_6_plus_count' => 0,
                'tab_switch_6_plus_pass_count' => 0
            ];
        }
        
        // Map tab switch stats to data array
        $data = array_merge($data, $tab_switch_stats);
        
        // Calculate pass rates
        $data['tab_switch_0_pass_rate'] = ($data['tab_switch_0_count'] > 0) ? 
            ($data['tab_switch_0_pass_count'] / $data['tab_switch_0_count']) * 100 : 0;
        
        $data['tab_switch_1_2_pass_rate'] = ($data['tab_switch_1_2_count'] > 0) ? 
            ($data['tab_switch_1_2_pass_count'] / $data['tab_switch_1_2_count']) * 100 : 0;
        
        $data['tab_switch_3_5_pass_rate'] = ($data['tab_switch_3_5_count'] > 0) ? 
            ($data['tab_switch_3_5_pass_count'] / $data['tab_switch_3_5_count']) * 100 : 0;
        
        $data['tab_switch_6_plus_pass_rate'] = ($data['tab_switch_6_plus_count'] > 0) ? 
            ($data['tab_switch_6_plus_pass_count'] / $data['tab_switch_6_plus_count']) * 100 : 0;
        
        // Generate trend data for performance chart
        $trend_data = $this->generate_trend_data($start_date, $end_date, $course_id, $module_id);
        $data['trend_labels'] = $trend_data['labels'];
        $data['trend_avg_scores'] = $trend_data['avg_scores'];
        $data['trend_completion_rates'] = $trend_data['completion_rates'];
        
        // Course comparison data for radar chart
        $data['course_radar_data'] = $this->generate_course_radar_data($course_id);
        
        // Course trend data
        $course_trend_data = $this->generate_course_trend_data($course_id);
        $data['course_trend_labels'] = $course_trend_data['labels'];
        $data['course_trend_data'] = $course_trend_data['datasets'];
        
        // Get test modules for reference
        $test_modules = $this->db_model->get_all(TABLE_TEST_MODULES, ['is_active' => 1]);
        $module_map = [];
        foreach ($test_modules as $module) {
            $module_map[$module['id']] = $module['name'];
        }
        
        $data['module_map'] = $module_map;
        
        // Get placeholder data for charts
        $this->initialize_placeholder_chart_data($data);
        
        // Load views
        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/reports/dashboard', $data);
        $this->load->view('common/footer');
    }

    private function initialize_dashboard_data(&$data) {
        // Initialize all variables that might be accessed in the view
        $data['total_tests'] = 0;
        $data['total_students'] = 0;
        $data['total_submissions'] = 0;
        $data['completed_tests'] = 0;
        $data['in_progress_tests'] = 0;
        $data['not_started_tests'] = 0;
        $data['overall_avg_score'] = 0;
        $data['completion_rate'] = 0;
        $data['in_progress_rate'] = 0;
        $data['not_started_rate'] = 0;
        $data['avg_tab_switches'] = 0;
        $data['min_tab_switches'] = 0;
        $data['max_tab_switches'] = 0;
        $data['median_tab_switches'] = 0;
        $data['tab_switch_distribution'] = array_fill(0, 7, 0);
        $data['exceeded_tab_switch_count'] = 0;
        $data['exceeded_tab_switch_percentage'] = 0;
        $data['tab_switch_0_avg_score'] = 0;
        $data['tab_switch_0_count'] = 0;
        $data['tab_switch_0_pass_count'] = 0;
        $data['tab_switch_0_pass_rate'] = 0;
        $data['tab_switch_1_2_avg_score'] = 0;
        $data['tab_switch_1_2_count'] = 0;
        $data['tab_switch_1_2_pass_count'] = 0;
        $data['tab_switch_1_2_pass_rate'] = 0;
        $data['tab_switch_3_5_avg_score'] = 0;
        $data['tab_switch_3_5_count'] = 0;
        $data['tab_switch_3_5_pass_count'] = 0;
        $data['tab_switch_3_5_pass_rate'] = 0;
        $data['tab_switch_6_plus_avg_score'] = 0;
        $data['tab_switch_6_plus_count'] = 0;
        $data['tab_switch_6_plus_pass_count'] = 0;
        $data['tab_switch_6_plus_pass_rate'] = 0;
        $data['top_students'] = [];
        $data['challenging_questions'] = [];
        $data['test_analysis'] = [];
        $data['course_analysis'] = [];
        $data['module_time_data'] = [];
        $data['easy_questions_count'] = 0;
        $data['medium_questions_count'] = 0;
        $data['hard_questions_count'] = 0;
        $data['easy_questions_success_rate'] = 0;
        $data['medium_questions_success_rate'] = 0;
        $data['hard_questions_success_rate'] = 0;
        $data['avg_time_easy'] = 0;
        $data['min_time_easy'] = 0;
        $data['max_time_easy'] = 0;
        $data['avg_time_medium'] = 0;
        $data['min_time_medium'] = 0;
        $data['max_time_medium'] = 0;
        $data['avg_time_hard'] = 0;
        $data['min_time_hard'] = 0;
        $data['max_time_hard'] = 0;
        $data['max_time_total'] = 0;
        $data['avg_time_per_test'] = 0;
        $data['courses'] = [];
        $data['modules'] = [];
        $data['trend_labels'] = [];
        $data['trend_avg_scores'] = [];
        $data['trend_completion_rates'] = [];
        $data['course_radar_data'] = [];
        $data['course_trend_labels'] = [];
        $data['course_trend_data'] = [];
        $data['module_map'] = [];
    }


    private function initialize_placeholder_chart_data(&$data) {
        // If there's no data, add some placeholder data for charts
        if (empty($data['trend_labels'])) {
            $data['trend_labels'] = ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'];
            $data['trend_avg_scores'] = [0, 0, 0, 0, 0, 0, 0];
            $data['trend_completion_rates'] = [0, 0, 0, 0, 0, 0, 0];
        }
        
        if (empty($data['course_radar_data'])) {
            $data['course_radar_data'] = [
                [
                    'label' => 'No Data',
                    'data' => [0, 0, 0, 0, 0],
                    'backgroundColor' => 'rgba(84, 105, 212, 0.7)',
                    'borderColor' => 'rgba(84, 105, 212, 1)',
                    'borderWidth' => 1
                ]
            ];
        }
        
        if (empty($data['course_trend_labels'])) {
            $data['course_trend_labels'] = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            $data['course_trend_data'] = [
                [
                    'label' => 'No Data',
                    'data' => [0, 0, 0, 0, 0, 0],
                    'borderColor' => 'rgba(84, 105, 212, 1)',
                    'backgroundColor' => 'rgba(84, 105, 212, 0.1)',
                    'tension' => 0.4,
                    'fill' => false
                ]
            ];
        }
        
        // Initialize module_time_data if empty
        if (empty($data['module_time_data'])) {
            $data['module_time_data'] = [
                ['name' => 'No Data', 'avg_time' => 0]
            ];
        }
        
        // Set up placeholder difficulty data if not set
        if (!isset($data['easy_questions_count'])) {
            $data['easy_questions_count'] = 0;
            $data['medium_questions_count'] = 0;
            $data['hard_questions_count'] = 0;
            $data['easy_questions_success_rate'] = 0;
            $data['medium_questions_success_rate'] = 0;
            $data['hard_questions_success_rate'] = 0;
        }
        
        // Initialize time spent variables for question difficulty
        if (!isset($data['avg_time_easy'])) {
            $data['avg_time_easy'] = 0;
            $data['min_time_easy'] = 0;
            $data['max_time_easy'] = 0;
            $data['avg_time_medium'] = 0;
            $data['min_time_medium'] = 0;
            $data['max_time_medium'] = 0;
            $data['avg_time_hard'] = 0;
            $data['min_time_hard'] = 0;
            $data['max_time_hard'] = 0;
            $data['max_time_total'] = 1; // Prevent division by zero
            $data['avg_time_per_test'] = 0;
        }
    }

    private function calculate_median($arr) {
        $count = count($arr);
        if ($count == 0) {
            return 0;
        }
        
        sort($arr);
        $middle = floor($count / 2);
        
        if ($count % 2) {
            return $arr[$middle];
        } else {
            return ($arr[$middle-1] + $arr[$middle]) / 2;
        }
    }


    private function generate_trend_data($start_date = null, $end_date = null, $course_id = null, $module_id = null) {
        // Default to last 30 days if no date range specified
        if (empty($start_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime('-30 days'));
        }
        
        if (empty($end_date)) {
            $end_date = date('Y-m-d 23:59:59');
        }
        
        // Initialize return data
        $trend_data = [
            'labels' => [],
            'avg_scores' => [],
            'completion_rates' => []
        ];
        
        // Create DateTime objects
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        
        // Ensure start date is before end date
        if ($start > $end) {
            return $trend_data; // Return empty data if dates are invalid
        }
        
        // Calculate days difference
        $interval = date_diff($start, $end);
        $days_diff = $interval->days;
        
        // Determine appropriate interval size based on date range
        if ($days_diff <= 14) {
            // For 2 weeks or less, show daily data
            $interval_type = 'day';
            $group_by = 'DATE(submission_time)';
            $format = 'Y-m-d';
            $display_format = 'M d';
        } elseif ($days_diff <= 90) {
            // For 3 months or less, show weekly data
            $interval_type = 'week';
            $group_by = 'YEARWEEK(submission_time)';
            $format = 'Y-W';
            $display_format = '\WW (M d)';
        } else {
            // For longer periods, show monthly data
            $interval_type = 'month';
            $group_by = 'DATE_FORMAT(submission_time, "%Y-%m")';
            $format = 'Y-m';
            $display_format = 'M Y';
        }
        
        // Query to get trend data
        $this->db->select($group_by . ' as period');
        $this->db->select('AVG(percentage) as avg_score');
        $this->db->select('COUNT(DISTINCT id) as submissions');
        $this->db->select('SUM(CASE WHEN finished = 1 THEN 1 ELSE 0 END) as completed');
        
        $this->db->from('student_test_submission');
        
        if (!empty($course_id) || !empty($module_id)) {
            $this->db->join(
                TABLE_TESTS . ' as tests',
                'student_test_submission.test_id = tests.id',
                'inner'
            );
        }
        
        if (!empty($course_id)) {
            $this->db->join(
                TABLE_COURSE_TESTS . ' as course_tests',
                'tests.id = course_tests.test_id',
                'inner'
            );
            $this->db->where('course_tests.course_id', $course_id);
        }
        
        if (!empty($module_id)) {
            $this->db->where('tests.module_id', $module_id);
        }
        
        $this->db->where('submission_time >=', $start_date);
        $this->db->where('submission_time <=', $end_date);
        
        $this->db->group_by($group_by);
        $this->db->order_by($group_by, 'ASC');
        
        $results = $this->db->get()->result_array();
        
        // Handle empty results
        if (empty($results)) {
            // Just return empty arrays for chart
            return $trend_data;
        }
        
        // Generate date periods based on interval type
        $periods = [];
        
        if ($interval_type == 'day') {
            // Generate daily periods
            $period = new DatePeriod(
                $start,
                new DateInterval('P1D'),
                $end
            );
            
            foreach ($period as $date) {
                $period_key = $date->format($format);
                $periods[$period_key] = [
                    'avg_score' => 0,
                    'completion_rate' => 0,
                    'display' => $date->format($display_format)
                ];
            }
        } elseif ($interval_type == 'week') {
            // Generate weekly periods
            $current = clone $start;
            $current->modify('Monday this week'); // Start from Monday
            
            while ($current <= $end) {
                $period_key = $current->format('Y') . '-' . $current->format('W');
                $periods[$period_key] = [
                    'avg_score' => 0,
                    'completion_rate' => 0,
                    'display' => 'Week ' . $current->format('W') . ' (' . $current->format('M d') . ')'
                ];
                
                $current->modify('+1 week');
            }
        } else {
            // Generate monthly periods
            $current = clone $start;
            $current->modify('first day of this month'); // Start from first day of month
            
            while ($current <= $end) {
                $period_key = $current->format($format);
                $periods[$period_key] = [
                    'avg_score' => 0,
                    'completion_rate' => 0,
                    'display' => $current->format($display_format)
                ];
                
                $current->modify('+1 month');
            }
        }
        
        // Fill with actual data
        foreach ($results as $row) {
            $period_key = '';
            
            // Extract period key based on group_by format
            if (strpos($group_by, 'DATE(') === 0) {
                $period_key = date($format, strtotime($row['period']));
            } elseif (strpos($group_by, 'YEARWEEK') === 0) {
                // Convert YEARWEEK format to Y-W
                $year = substr($row['period'], 0, 4);
                $week = substr($row['period'], 4);
                $period_key = $year . '-' . $week;
            } else {
                $period_key = $row['period'];
            }
            
            if (isset($periods[$period_key])) {
                $periods[$period_key]['avg_score'] = round($row['avg_score'], 1);
                $periods[$period_key]['completion_rate'] = ($row['submissions'] > 0) ? 
                    round(($row['completed'] / $row['submissions']) * 100, 1) : 0;
            }
        }
        
        // Convert to arrays for chart
        foreach ($periods as $period_data) {
            $trend_data['labels'][] = $period_data['display'];
            $trend_data['avg_scores'][] = $period_data['avg_score'];
            $trend_data['completion_rates'][] = $period_data['completion_rate'];
        }
        
        return $trend_data;
    }

    private function generate_course_radar_data($selected_course_id = null) {
        // Get top courses by performance
        $this->db->select('
            courses.id,
            courses.name,
            COUNT(student_test_submission.id) / 
                (COUNT(DISTINCT course_tests.test_id) * COUNT(DISTINCT course_students.student_id)) * 100 as completion_rate,
            AVG(student_test_submission.percentage) as avg_score,
            SUM(CASE WHEN student_test_submission.percentage >= 60 THEN 1 ELSE 0 END) / 
                NULLIF(COUNT(student_test_submission.id), 0) * 100 as pass_rate,
            AVG(student_test_submission.tab_changes) / 3 * 100 as engagement,
            90 as student_satisfaction  # Placeholder - would be from actual feedback data
        ');
        
        $this->db->from(TABLE_COURCES . ' as courses');
        
        $this->db->join(
            TABLE_COURSE_STUDENTS . ' as course_students',
            'courses.id = course_students.course_id AND course_students.is_active = 1',
            'left'
        );
        
        $this->db->join(
            TABLE_COURSE_TESTS . ' as course_tests',
            'courses.id = course_tests.course_id AND course_tests.is_active = 1',
            'left'
        );
        
        $this->db->join(
            'student_test_submission',
            'course_tests.test_id = student_test_submission.test_id AND 
            course_students.student_id = student_test_submission.student_id AND 
            student_test_submission.finished = 1',
            'left'
        );
        
        if (!empty($selected_course_id)) {
            $this->db->where('courses.id', $selected_course_id);
        }
        
        $this->db->where('courses.college_id', $this->college['id']);
        $this->db->where('courses.is_active', 1);
        
        $this->db->group_by('courses.id');
        $this->db->order_by('avg_score', 'DESC');
        $this->db->limit(5);
        
        $courses = $this->db->get()->result_array();
        
        // Prepare data for radar chart
        $datasets = [];
        $colors = [
            'rgba(84, 105, 212, 0.7)',
            'rgba(40, 167, 69, 0.7)',
            'rgba(23, 162, 184, 0.7)',
            'rgba(255, 193, 7, 0.7)',
            'rgba(220, 53, 69, 0.7)'
        ];
        
        foreach ($courses as $index => $course) {
            $color = $colors[$index % count($colors)];
            $datasets[] = [
                'label' => $course['name'],
                'data' => [
                    round($course['completion_rate'] ?? 0, 1),
                    round($course['avg_score'] ?? 0, 1),
                    round($course['pass_rate'] ?? 0, 1),
                    round($course['engagement'] ?? 0, 1),
                    round($course['student_satisfaction'] ?? 0, 1)
                ],
                'backgroundColor' => $color,
                'borderColor' => str_replace('0.7', '1', $color),
                'borderWidth' => 1
            ];
        }
        
        return $datasets;
    }


    private function generate_course_trend_data($selected_course_id = null) {
        // Get course performance over time (last 6 months)
        $start_date = date('Y-m-d 00:00:00', strtotime('-6 months'));
        $end_date = date('Y-m-d 23:59:59');
        
        // Query to get monthly course performance
        $this->db->select('DATE_FORMAT(student_test_submission.submission_time, "%Y-%m") as period');
        $this->db->select('courses.id, courses.name');
        $this->db->select('AVG(student_test_submission.percentage) as avg_score');
        
        $this->db->from(TABLE_COURCES . ' as courses');
        
        $this->db->join(
            TABLE_COURSE_TESTS . ' as course_tests',
            'courses.id = course_tests.course_id',
            'inner'
        );
        
        $this->db->join(
            'student_test_submission',
            'course_tests.test_id = student_test_submission.test_id',
            'inner'
        );
        
        if (!empty($selected_course_id)) {
            $this->db->where('courses.id', $selected_course_id);
        }
        
        $this->db->where('courses.college_id', $this->college['id']);
        $this->db->where('courses.is_active', 1);
        $this->db->where('student_test_submission.finished', 1);
        $this->db->where('student_test_submission.submission_time >=', $start_date);
        $this->db->where('student_test_submission.submission_time <=', $end_date);
        
        $this->db->group_by('courses.id, period');
        $this->db->order_by('period', 'ASC');
        
        if (empty($selected_course_id)) {
            $this->db->limit(5);
        }
        
        $results = $this->db->get()->result_array();
        
        // Prepare data for chart
        $trend_data = [
            'labels' => [],
            'datasets' => []
        ];
        
        // Generate complete month range
        $period = new DatePeriod(
            new DateTime($start_date),
            new DateInterval('P1M'),
            new DateTime($end_date)
        );
        
        $months = [];
        foreach ($period as $date) {
            $month_key = $date->format('Y-m');
            $months[] = $month_key;
            $trend_data['labels'][] = $date->format('M Y');
        }
        
        // Group results by course
        $course_data = [];
        foreach ($results as $row) {
            if (!isset($course_data[$row['id']])) {
                $course_data[$row['id']] = [
                    'name' => $row['name'],
                    'data' => array_fill(0, count($months), null)
                ];
            }
            
            $month_index = array_search($row['period'], $months);
            if ($month_index !== false) {
                $course_data[$row['id']]['data'][$month_index] = round($row['avg_score'], 1);
            }
        }
        
        // Colors for datasets
        $colors = [
            'rgba(84, 105, 212, 1)',
            'rgba(40, 167, 69, 1)',
            'rgba(23, 162, 184, 1)',
            'rgba(255, 193, 7, 1)',
            'rgba(220, 53, 69, 1)'
        ];
        
        // Convert to chart datasets
        $index = 0;
        foreach ($course_data as $course) {
            $color = $colors[$index % count($colors)];
            $trend_data['datasets'][] = [
                'label' => $course['name'],
                'data' => $course['data'],
                'borderColor' => $color,
                'backgroundColor' => str_replace('1)', '0.1)', $color),
                'tension' => 0.4,
                'fill' => false
            ];
            $index++;
        }
        
        return $trend_data;
    }


    public function export_dashboard($format = 'csv')
    {
        // Get filter parameters
        $course_id = $this->input->get('course_id');
        $module_id = $this->input->get('module_id');
        $date_range = $this->input->get('date_range');
        
        // Process date range filter
        $start_date = null;
        $end_date = null;
        
        if (!empty($date_range)) {
            $date_parts = explode(' - ', $date_range);
            if (count($date_parts) == 2) {
                $start_date = date('Y-m-d 00:00:00', strtotime($date_parts[0]));
                $end_date = date('Y-m-d 23:59:59', strtotime($date_parts[1]));
            }
        }
        
        // Default filename
        $filename = 'test_analytics_' . date('Y-m-d') . '.' . strtolower($format);
        
        // Build query with join and where conditions
        $this->db->select('
            students.name as student_name,
            students.email as student_email,
            tests.title as test_title,
            test_modules.name as module_name,
            student_test_submission.submission_time,
            student_test_submission.earned_score,
            student_test_submission.total_score,
            student_test_submission.percentage,
            student_test_submission.tab_changes,
            student_test_submission.finished,
            student_solutions.question_id,
            question_bank.question_title,
            student_solutions.score as question_score,
            student_solutions.max_score as question_max_score,
            student_solutions.time_spent as question_time_spent
        ');
        
        $this->db->from('student_test_submission');
        
        $this->db->join(
            TABLE_STUDENT . ' as students',
            'student_test_submission.student_id = students.id',
            'inner'
        );
        
        $this->db->join(
            TABLE_TESTS . ' as tests',
            'student_test_submission.test_id = tests.id',
            'inner'
        );
        
        $this->db->join(
            TABLE_TEST_MODULES . ' as test_modules',
            'tests.module_id = test_modules.id',
            'inner'
        );
        
        $this->db->join(
            'student_solutions',
            'student_test_submission.student_id = student_solutions.student_id AND 
            student_test_submission.test_id = student_solutions.test_id',
            'left'
        );
        
        $this->db->join(
            TABLE_QUESTION_BANK . ' as question_bank',
            'student_solutions.question_id = question_bank.id',
            'left'
        );
        
        if (!empty($course_id)) {
            $this->db->join(
                TABLE_COURSE_TESTS . ' as course_tests',
                'tests.id = course_tests.test_id',
                'inner'
            );
            $this->db->where('course_tests.course_id', $course_id);
            
            // Get course name for filename
            $course = $this->db_model->get_row(TABLE_COURCES, ['id' => $course_id]);
            if ($course) {
                $filename = 'course_' . preg_replace('/[^A-Za-z0-9]/', '_', $course['name']) . '_analytics_' . date('Y-m-d') . '.' . strtolower($format);
            }
        }
        
        if (!empty($module_id)) {
            $this->db->where('tests.module_id', $module_id);
            
            // Get module name for filename
            $module = $this->db_model->get_row(TABLE_TEST_MODULES, ['id' => $module_id]);
            if ($module) {
                $filename = 'module_' . preg_replace('/[^A-Za-z0-9]/', '_', $module['name']) . '_analytics_' . date('Y-m-d') . '.' . strtolower($format);
            }
        }
        
        if ($start_date && $end_date) {
            $this->db->where('student_test_submission.submission_time >=', $start_date);
            $this->db->where('student_test_submission.submission_time <=', $end_date);
            
            // Add date range to filename
            $filename = 'analytics_' . date('Y-m-d', strtotime($start_date)) . '_to_' . date('Y-m-d', strtotime($end_date)) . '.' . strtolower($format);
        }
        
        $this->db->order_by('student_test_submission.submission_time', 'DESC');
        
        $data = $this->db->get()->result_array();
        
        // Generate and output report based on format
        switch (strtolower($format)) {
            case 'excel':
                $this->export_excel($data, $filename);
                break;
                
            case 'pdf':
                $this->export_pdf($data, $filename);
                break;
                
            case 'csv':
            default:
                $this->export_csv_data($data, $filename);
                break;
        }
    }

    /**
     * Export data to CSV
     */
    private function export_csv_data($data, $filename) {
        // Set content type and headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Create a file pointer
        $output = fopen('php://output', 'w');
        
        // Add column headers
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            
            // Output data rows
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
    }


    private function export_excel($data, $filename) {
        // Load PHPSpreadsheet library
        $this->load->library('phpspreadsheet');
        
        // Create a new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Add headers
        if (!empty($data)) {
            $col = 1;
            foreach (array_keys($data[0]) as $header) {
                $sheet->setCellValueByColumnAndRow($col++, 1, $header);
            }
            
            // Add data
            $row = 2;
            foreach ($data as $data_row) {
                $col = 1;
                foreach ($data_row as $value) {
                    $sheet->setCellValueByColumnAndRow($col++, $row, $value);
                }
                $row++;
            }
        }
        
        // Set auto column width
        foreach (range('A', $sheet->getHighestColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
    }

 
    private function export_pdf($data, $filename) {
        // Load TCPDF library
        $this->load->library('pdf');
        
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($this->college['name']);
        $pdf->SetTitle('Test Analytics Report');
        $pdf->SetSubject('Test Analytics Report');
        
        // Set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $this->college['name'], 'Test Analytics Report - ' . date('Y-m-d'));
        
        // Set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // Add a page
        $pdf->AddPage('L', 'A4');
        
        // Create the table
        $html = '<h1>Test Analytics Report</h1>';
        $html .= '<table border="1" cellpadding="5">';
        
        // Add headers
        if (!empty($data)) {
            $html .= '<tr>';
            foreach (array_keys($data[0]) as $header) {
                $html .= '<th>' . $header . '</th>';
            }
            $html .= '</tr>';
            
            // Add data rows
            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ($row as $value) {
                    $html .= '<td>' . $value . '</td>';
                }
                $html .= '</tr>';
            }
        }
        
        $html .= '</table>';
        
        // Output HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Close and output PDF
        $pdf->Output($filename, 'D');
    }

    public function kpis()
    {
        $college_id = $this->college['id'];

        $totals = [
            'students' => $this->db->where(['is_active' => 1, 'college_id' => $college_id])->count_all_results(TABLE_STUDENT),
            'staff' => $this->db->where(['is_active' => 1])->count_all_results(TABLE_FACULTY),
            'courses' => $this->db->where(['is_active' => 1, 'college_id' => $college_id])->count_all_results(TABLE_COURCES),
            'batches' => $this->db->table_exists(TABLE_BATCHES) ? $this->db->where('college_id', $college_id)->count_all_results(TABLE_BATCHES) : 0,
            'modules' => $this->db->table_exists('course_modules') ? $this->db->where('course_id !=', null)->count_all_results('course_modules') : 0,
            'lessons' => $this->db->table_exists('lessons') ? $this->db->count_all('lessons') : 0,
            'instruments_available' => $this->db->table_exists(TABLE_INSTRUMENTS) ? $this->db->where(['availability_status' => 'available', 'college_id' => $college_id])->count_all_results(TABLE_INSTRUMENTS) : 0,
            'instruments_issued' => $this->db->table_exists(TABLE_INSTRUMENT_TRANSACTIONS) ? $this->db->where(['status' => 'issued', 'college_id' => $college_id])->count_all_results(TABLE_INSTRUMENT_TRANSACTIONS) : 0,
            'maintenance_open' => $this->db->table_exists(TABLE_INSTRUMENT_MAINTENANCE) ? $this->db->where(['status' => 'open', 'college_id' => $college_id])->count_all_results(TABLE_INSTRUMENT_MAINTENANCE) : 0,
        ];

        return $this->respond('success', $totals);
    }
}
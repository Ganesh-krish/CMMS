<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentPortal extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('common', 'faculty_common');
        $this->load->model('Db_model', 'db_model');
        $this->load->model('Announcement_model', 'announcement');

        // Get college information
        $this->college = $this->faculty_common->get_default_college();

        // Check if student is logged in for protected routes
        $this->student_session = $this->session->userdata('student_logged_in');
    }

    // Student login page
    public function login() {
        // If student is already logged in, redirect to dashboard
        if ($this->student_session) {
            redirect('student-portal/dashboard');
        }

        $data['college'] = $this->college;
        $data['error'] = $this->session->flashdata('error');

        $this->load->view('student/login', $data);
    }

    // Student authentication
    public function authenticate() {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        // Validate input
        $this->form_validation->set_rules('email', 'Email', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('student-portal/login');
        }

        // Check student credentials
        $student = $this->db_model->get_row('students', [
            'email' => $email,
            'college_id' => $this->college['id'],
            'is_active' => 1
        ]);

        if ($student && password_verify($password, $student['password'])) {
            // Set student session
            $student_session = [
                'id' => $student['id'],
                'name' => $student['name'],
                'email' => $student['email'],
                'roll_no' => $student['roll_no'],
                'department' => $student['department'],
                'batch' => $student['batch'],
                'college_id' => $student['college_id'],
                'logged_in' => true
            ];

            $this->session->set_userdata('student_logged_in', $student_session);
            // print_r($student_session);
            // exit;
            redirect('student-portal/dashboard');
        } else {
            $this->session->set_flashdata('error', 'Invalid email or password');
            redirect('student-portal/login');
        }
    }

    // Student dashboard
    public function dashboard() {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        $data['student'] = $this->student_session;
        $data['college'] = $this->college;

        $this->load->view('student/common/sidebar', $data);
        $this->load->view('student/dashboard', $data);
        $this->load->view('student/common/footer');
    }

    // Student courses
    public function courses() {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        $data['student'] = $this->student_session;
        $data['college'] = $this->college;

        // Get courses that the student is enrolled in
        $this->db->select('c.*, ce.enrolled_at, ce.progress_percentage, ce.status as enrollment_status');
        $this->db->from('courses c');
        $this->db->join('course_enrollments ce', 'ce.course_id = c.id');
        $this->db->where('ce.student_id', $this->student_session['id']);
        $this->db->where('c.is_active', 1);
        $this->db->where('ce.status !=', 'dropped');
        $data['courses'] = $this->db->get()->result_array();

        $this->load->view('student/common/sidebar', $data);
        $this->load->view('student/courses/index', $data);
        $this->load->view('student/common/footer');
    }

    // Student course modules
    public function course_modules($course_id) {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        $data['student'] = $this->student_session;
        $data['college'] = $this->college;

        // Verify student is enrolled in this course
        $enrollment = $this->db_model->get_row('course_enrollments', [
            'course_id' => $course_id,
            'student_id' => $this->student_session['id'],
            'status !=' => 'dropped'
        ]);

        if (!$enrollment) {
            $this->session->set_flashdata('error', 'You are not enrolled in this course');
            redirect('student-portal/courses');
        }

        $data['course'] = $this->db_model->get_row('courses', ['id' => $course_id, 'is_active' => 1]);
        if (!$data['course']) {
            $this->session->set_flashdata('error', 'Course not found');
            redirect('student-portal/courses');
        }

        // Get modules for this course
        $data['modules'] = $this->db_model->get_all('course_modules', [
            'course_id' => $course_id,
            'is_active' => 1
        ], '*', 'order', 'ASC');

        $this->load->view('student/common/sidebar', $data);
        $this->load->view('student/courses/modules', $data);
        $this->load->view('student/common/footer');
    }

    // Student module lessons
    public function module_lessons($course_id, $module_id) {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        $data['student'] = $this->student_session;
        $data['college'] = $this->college;

        // Verify student is enrolled in this course
        $enrollment = $this->db_model->get_row('course_enrollments', [
            'course_id' => $course_id,
            'student_id' => $this->student_session['id'],
            'status !=' => 'dropped'
        ]);

        if (!$enrollment) {
            $this->session->set_flashdata('error', 'You are not enrolled in this course');
            redirect('student-portal/courses');
        }

        $data['course'] = $this->db_model->get_row('courses', ['id' => $course_id, 'is_active' => 1]);
        $data['module'] = $this->db_model->get_row('course_modules', ['id' => $module_id, 'is_active' => 1]);

        if (!$data['course'] || !$data['module']) {
            $this->session->set_flashdata('error', 'Course or module not found');
            redirect('student-portal/courses');
        }

        // Get lessons for this module
        $data['lessons'] = $this->db_model->get_all('course_module_lessons', [
            'module_id' => $module_id,
            'is_active' => 1
        ], '*','order', 'ASC');

        $this->load->view('student/common/sidebar', $data);
        $this->load->view('student/courses/lessons', $data);
        $this->load->view('student/common/footer');
    }

    // Student music inventory
    public function inventory() {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        $data['student'] = $this->student_session;
        $data['college'] = $this->college;

        // Get available instruments
        $data['instruments'] = $this->db_model->get_all('instruments', [
            'college_id' => $this->college['id'],
            'is_active' => 1,
            'availability_status' => 'available'
        ]);

        $this->load->view('student/common/sidebar', $data);
        $this->load->view('student/inventory/index', $data);
        $this->load->view('student/common/footer');
    }

    // Student announcements
    public function announcements() {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        $data['student'] = $this->student_session;
        $data['college'] = $this->college;

        // Get announcements using the model (same logic as faculty portal)
        $filters = [
            'college_id' => $this->college['id'],
            'is_active' => 1,
            'user_role' => ROLE_STUDENT,
            'user_department' => $this->student_session['department']
        ];

        $data['announcements'] = $this->announcement->get_announcements($filters);

        $this->load->view('student/common/sidebar', $data);
        $this->load->view('student/announcements/index', $data);
        $this->load->view('student/common/footer');
    }

    // View lesson for students
    public function view_lesson($course_id, $module_id, $lesson_id) {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        // Verify student is enrolled in this course
        $enrollment = $this->db_model->get_row('course_enrollments', [
            'course_id' => $course_id,
            'student_id' => $this->student_session['id'],
            'status !=' => 'dropped'
        ]);

        if (!$enrollment) {
            $this->session->set_flashdata('error', 'You are not enrolled in this course');
            redirect('student-portal/courses');
        }

        // Get course, module, and lesson data
        $data['course'] = $this->db_model->get_row('courses', ['id' => $course_id, 'is_active' => 1]);
        $data['module'] = $this->db_model->get_row('course_modules', ['id' => $module_id, 'is_active' => 1]);
        $data['lesson'] = $this->db_model->get_row('course_module_lessons', ['id' => $lesson_id, 'is_active' => 1]);

        if (!$data['course'] || !$data['module'] || !$data['lesson']) {
            $this->session->set_flashdata('error', 'Course, module, or lesson not found');
            redirect('student-portal/courses');
        }

        $data['student'] = $this->student_session;
        $data['college'] = $this->college;

        $this->load->view('student/common/sidebar', $data);
        $this->load->view('student/courses/view_lesson', $data);
        $this->load->view('student/common/footer');
    }

    // Student logout
    public function logout() {
        $this->session->unset_userdata('student_logged_in');
        redirect('student-portal/login');
    }
}
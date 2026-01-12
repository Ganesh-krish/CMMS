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
        $this->db->select('c.*, ce.id as enrollment_id, ce.enrolled_at, ce.progress_percentage, ce.status as enrollment_status');
        $this->db->from('courses c');
        $this->db->join('course_enrollments ce', 'ce.course_id = c.id');
        $this->db->where('ce.student_id', $this->student_session['id']);
        $this->db->where('c.is_active', 1);
        $this->db->where('ce.status !=', 'dropped');
        $courses = $this->db->get()->result_array();

        // Load certificate model and get certificates for completed courses
        $this->load->model('Certificate_model', 'certificate_model');
        $certificates_map = [];
        
        foreach ($courses as &$course) {
            if ($course['enrollment_status'] === 'completed') {
                $certificate = $this->certificate_model->get_certificate_by_enrollment($course['enrollment_id']);
                if ($certificate) {
                    $certificates_map[$course['id']] = $certificate;
                }
            }
        }
        
        $data['courses'] = $courses;
        $data['certificates_map'] = $certificates_map;

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

        // Load lesson progress model and mark lesson as in-progress
        $this->load->model('Lesson_progress_model', 'lesson_progress');
        $this->lesson_progress->mark_lesson_in_progress(
            $enrollment['id'],
            $lesson_id,
            $module_id,
            $course_id,
            $this->student_session['id']
        );

        // Get lesson progress status
        $data['lesson_progress'] = $this->lesson_progress->get_lesson_progress($enrollment['id'], $lesson_id);
        
        // Get all lessons in module to check if this is the last one
        $all_lessons = $this->db_model->get_all('course_module_lessons', [
            'module_id' => $module_id,
            'is_active' => 1
        ], '*', 'order', 'ASC');
        
        $data['is_last_lesson'] = false;
        if (!empty($all_lessons)) {
            $last_lesson = end($all_lessons);
            $data['is_last_lesson'] = ($last_lesson['id'] == $lesson_id);
        }
        
        // Check if all lessons in course are completed
        $data['all_lessons_completed'] = $this->lesson_progress->are_all_lessons_completed($enrollment['id'], $course_id);
        
        // Check if certificate request exists
        $this->load->model('Certificate_request_model', 'cert_request');
        $data['certificate_request'] = $this->cert_request->get_request_by_enrollment($enrollment['id']);
        
        // Get enrollment for progress calculation
        $data['enrollment'] = $enrollment;

        $data['student'] = $this->student_session;
        $data['college'] = $this->college;

        $this->load->view('student/common/sidebar', $data);
        $this->load->view('student/courses/view_lesson', $data);
        $this->load->view('student/common/footer');
    }

    // Mark lesson as completed
    public function mark_lesson_completed($course_id, $module_id, $lesson_id) {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        // Verify student is enrolled
        $enrollment = $this->db_model->get_row('course_enrollments', [
            'course_id' => $course_id,
            'student_id' => $this->student_session['id'],
            'status !=' => 'dropped'
        ]);

        if (!$enrollment) {
            $this->session->set_flashdata('message', array('danger', 'You are not enrolled in this course'));
            redirect('student-portal/courses');
        }

        // Mark lesson as completed
        $this->load->model('Lesson_progress_model', 'lesson_progress');
        if ($this->lesson_progress->mark_lesson_completed($enrollment['id'], $lesson_id)) {
            // Recalculate course progress
            $this->recalculate_course_progress($enrollment['id'], $course_id);
            
            $this->session->set_flashdata('message', array('success', 'Lesson marked as completed!'));
        } else {
            $this->session->set_flashdata('message', array('danger', 'Failed to mark lesson as completed.'));
        }

        redirect('student-portal/view-lesson/' . $course_id . '/' . $module_id . '/' . $lesson_id);
    }

    // Request certificate
    public function request_certificate($course_id) {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        // Verify student is enrolled
        $enrollment = $this->db_model->get_row('course_enrollments', [
            'course_id' => $course_id,
            'student_id' => $this->student_session['id'],
            'status !=' => 'dropped'
        ]);

        if (!$enrollment) {
            $this->session->set_flashdata('message', array('danger', 'You are not enrolled in this course'));
            redirect('student-portal/courses');
        }

        // Check if all lessons are completed
        $this->load->model('Lesson_progress_model', 'lesson_progress');
        if (!$this->lesson_progress->are_all_lessons_completed($enrollment['id'], $course_id)) {
            $this->session->set_flashdata('message', array('warning', 'Please complete all lessons before requesting a certificate.'));
            redirect('student-portal/courses');
        }

        // Check if request already exists
        $this->load->model('Certificate_request_model', 'cert_request');
        $existing = $this->cert_request->get_request_by_enrollment($enrollment['id']);
        
        if ($existing) {
            $status_msg = $existing['status'] === 'pending' ? 'pending review' : 
                         ($existing['status'] === 'approved' ? 'already approved' : 'was rejected');
            $this->session->set_flashdata('message', array('info', 'Certificate request ' . $status_msg . '.'));
            redirect('student-portal/courses');
        }

        // Create certificate request
        if ($this->cert_request->create_request($enrollment['id'], $course_id, $this->student_session['id'])) {
            $this->session->set_flashdata('message', array('success', 'Certificate request submitted successfully! It will be reviewed by the principal.'));
        } else {
            $this->session->set_flashdata('message', array('danger', 'Failed to submit certificate request.'));
        }

        redirect('student-portal/courses');
    }

    // Recalculate course progress (private helper method)
    private function recalculate_course_progress($enrollment_id, $course_id) {
        $this->load->model('Lesson_progress_model', 'lesson_progress');
        $stats = $this->lesson_progress->get_completion_stats($enrollment_id);
        
        // Get total lessons in course (including those not started)
        $this->db->select('COUNT(*) as total');
        $this->db->from(TABLE_COURSE_MODULE_LESSONS . ' l');
        $this->db->join(TABLE_COURSE_MODULES . ' m', 'm.id = l.module_id');
        $this->db->where('m.course_id', $course_id);
        $this->db->where('l.is_active', 1);
        $total_lessons = $this->db->get()->row()->total;
        
        if ($total_lessons > 0) {
            $progress_percentage = ($stats['completed_lessons'] / $total_lessons) * 100;
            
            // Update enrollment progress
            $update_data = [
                'progress_percentage' => round($progress_percentage, 2)
            ];
            
            // Update status based on progress
            if ($progress_percentage >= 100) {
                $update_data['status'] = 'completed';
                $update_data['completed_at'] = date('Y-m-d H:i:s');
            } elseif ($progress_percentage > 0) {
                $update_data['status'] = 'in_progress';
            } else {
                $update_data['status'] = 'enrolled';
            }
            
            $this->db_model->update('course_enrollments', $update_data, ['id' => $enrollment_id]);
        }
    }

    // View certificate
    public function certificate($certificate_id = null) {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        $this->load->model('Certificate_model', 'certificate_model');
        
        $certificate = $this->certificate_model->get_certificate($certificate_id);
        
        if (!$certificate) {
            $this->session->set_flashdata('error', 'Certificate not found');
            redirect('student-portal/certificates');
        }
        
        // Verify certificate belongs to logged-in student
        if ($certificate['student_id'] != $this->student_session['id']) {
            $this->session->set_flashdata('error', 'You do not have permission to view this certificate');
            redirect('student-portal/certificates');
        }
        
        // Get full certificate data with course and student info
        $this->db->select('cc.*, c.name as course_name, c.course_code, ce.completed_at, s.name as student_name, s.email as student_email');
        $this->db->from(TABLE_COURSE_CERTIFICATES . ' cc');
        $this->db->join(TABLE_COURSE_ENROLLMENTS . ' ce', 'ce.id = cc.enrollment_id', 'inner');
        $this->db->join(TABLE_COURCES . ' c', 'c.id = cc.course_id', 'inner');
        $this->db->join(TABLE_STUDENT . ' s', 's.id = cc.student_id', 'inner');
        $this->db->where('cc.id', $certificate_id);
        $cert_data = $this->db->get()->row_array();
        
        if (!$cert_data) {
            $this->session->set_flashdata('error', 'Certificate not found');
            redirect('student-portal/certificates');
        }
        
        $data['certificate'] = $cert_data;
        $data['student'] = $this->student_session;
        $data['college'] = $this->college;
        
        // If certificate file exists, redirect to view it
        if (!empty($cert_data['certificate_file']) && file_exists(FCPATH . $cert_data['certificate_file'])) {
            $this->load->helper('url');
            redirect(base_url($cert_data['certificate_file']));
        } else {
            // Show certificate in HTML format (always show HTML view for better control)
            $this->load->view('student/certificates/view', $data);
        }
    }

    // List all certificates
    public function certificates() {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        $this->load->model('Certificate_model', 'certificate_model');
        
        $data['student'] = $this->student_session;
        $data['college'] = $this->college;
        $data['certificates'] = $this->certificate_model->get_student_certificates($this->student_session['id']);

        $this->load->view('student/common/sidebar', $data);
        $this->load->view('student/certificates/index', $data);
        $this->load->view('student/common/footer');
    }

    // Download certificate
    public function download_certificate($certificate_id = null) {
        if (!$this->student_session) {
            redirect('student-portal/login');
        }

        $this->load->model('Certificate_model', 'certificate_model');
        
        $certificate = $this->certificate_model->get_certificate($certificate_id);
        
        if (!$certificate || $certificate['student_id'] != $this->student_session['id']) {
            $this->session->set_flashdata('error', 'Certificate not found');
            redirect('student-portal/certificates');
        }
        
        if (!empty($certificate['certificate_file']) && file_exists(FCPATH . $certificate['certificate_file'])) {
            $this->load->helper('download');
            $file_path = FCPATH . $certificate['certificate_file'];
            $file_name = 'certificate_' . $certificate['certificate_number'] . '.html';
            force_download($file_name, file_get_contents($file_path));
        } else {
            $this->session->set_flashdata('error', 'Certificate file not found');
            redirect('student-portal/certificates');
        }
    }

    // Student logout
    public function logout() {
        $this->session->unset_userdata('student_logged_in');
        redirect('student-portal/login');
    }
}
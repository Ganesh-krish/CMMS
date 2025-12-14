<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentPortal extends CI_Controller
{
    private $college_slug;
    private $college;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Db_model', 'db_model');
        $this->load->model('common', 'faculty_common');
        $this->load->model('Lesson_model', 'lesson_model');
        $this->load->library('session');
    }

    private function resolve_college($slug)
    {
        $college = $this->faculty_common->get_default_college();
        if (!$college) {
            show_error('College not found', 404);
        }
        $this->college = $college;
        $this->college_slug = $slug;
    }

    public function login($college_slug = 'democollege')
    {
        // Redirect to unified login
        redirect('Welcome');
    }

    public function authenticate($college_slug = 'democollege')
    {
        // Redirect to unified login
        redirect('Welcome');
    }

    public function dashboard($college_slug = 'democollege')
    {
        $this->resolve_college($college_slug);

        // Check unified session
        $user = $this->session->userdata('user');
        if (!$user || $user['user_type'] !== 'student') {
            return redirect('Welcome');
        }

        $student = $user; // Use unified session data

        $courses = $this->db_model->get_all(TABLE_COURCES, [
            'college_id' => $student->college_id,
            'is_active' => 1
        ]);

        $modules_by_course = [];
        if (!empty($courses)) {
            foreach ($courses as $course) {
                $modules = $this->db_model->get_all('course_modules', [
                    'course_id' => $course['id']
                ], '*', 'id', 'ASC');
                foreach ($modules as &$module) {
                    $module['lessons'] = $this->lesson_model->list_by_module($module['id']);
                }
                $modules_by_course[$course['id']] = $modules;
            }
        }

        $data = [
            'college' => $this->college,
            'student' => $student,
            'courses' => $courses,
            'modules_by_course' => $modules_by_course,
            'college_slug' => $college_slug
        ];
        $this->load->view('student/dashboard', $data);
    }

    public function logout($college_slug = 'democollege')
    {
        $this->session->unset_userdata($college_slug . '_student');
        redirect("student-portal/$college_slug/login");
    }
}



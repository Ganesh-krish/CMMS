<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentPortal extends CI_Controller
{
    private $college_slug;
    private $college;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('faculty/db_model', 'db_model');
        $this->load->model('faculty/common', 'faculty_common');
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
        $this->resolve_college($college_slug);
        $data = [
            'college' => $this->college,
            'college_slug' => $college_slug,
            'message' => $this->session->flashdata('message'),
        ];
        $this->load->view('student/login', $data);
    }

    public function authenticate($college_slug = 'democollege')
    {
        $this->resolve_college($college_slug);
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        if (!$email || !$password) {
            $this->session->set_flashdata('message', ['danger', 'Email and password are required']);
            return redirect("student-portal/$college_slug/login");
        }

        $student = $this->db_model->get_row(TABLE_STUDENT, [
            'email' => $email,
            'password' => $password,
            'college_id' => $this->college['id'],
            'is_active' => 1
        ]);

        if (!$student) {
            $this->session->set_flashdata('message', ['danger', 'Invalid credentials']);
            return redirect("student-portal/$college_slug/login");
        }

        $this->session->set_userdata($college_slug . '_student', (object)$student);
        return redirect("student-portal/$college_slug/dashboard");
    }

    public function dashboard($college_slug = 'democollege')
    {
        $this->resolve_college($college_slug);
        $student = $this->session->userdata($college_slug . '_student');
        if (!$student) {
            return redirect("student-portal/$college_slug/login");
        }

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



<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends CI_Controller {
    private $url;
    private $college;
    private $session_data;

    function __construct() {
        parent::__construct();
        $this->load->model('common', 'faculty_common');
        $this->load->model('Db_model', 'db_model');
        $segment1 = $this->uri->segment(1);

        // Check unified session
        $user = $this->session->userdata('user');
        if (!$user || $user['user_type'] !== 'faculty') {
            redirect('Welcome');
        }

        $this->user_session = $user;
        $this->url = $segment1 ?: 'admin';
        $this->college = $this->faculty_common->get_default_college();
        $this->session_data = $user; // Use unified session data

        $this->permissions = $this->faculty_common->get_access_permissions($this->session_data);

        $role = (int)($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        if(!in_array($role, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF], true)){
            redirect('Welcome');
        }
    }

    public function index() {
        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/courses");
        $class["college"] = $this->college;

        // Role-based access control for courses
        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        $user_department = $this->session_data['department'] ?? null;

        $conditions = ["is_active" => true, "college_id" => $this->college['id']];

        // HOD and Staff can only see courses from their department (if department column exists)
        if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
            if ($this->db->field_exists('department', TABLE_COURCES)) {
                $conditions['department'] = $user_department;
            }
            $data["can_edit_all_courses"] = false;
            $data["can_delete_courses"] = false;
        } else {
            // Principal and Vice-Principal can see all courses
            $data["can_edit_all_courses"] = true;
            $data["can_delete_courses"] = true;
        }

        if ($user_role === ROLE_STAFF) {
            // Staff can only see courses they created
            $conditions['created_by'] = $this->session_data['id'];
        }

        $data["courses"] = $this->db_model->get_all(TABLE_COURCES, $conditions);

        // Get departments for dropdown
        $departments = $this->db_model->get_all(TABLE_DEPARTMENT, [
            "is_active" => true,
            "college_id" => $this->college['id']
        ]);

        // If no departments found for this college, try to get all active departments
        if (empty($departments)) {
            $departments = $this->db_model->get_all(TABLE_DEPARTMENT, [
                "is_active" => true
            ]);
            log_message('debug', 'Courses Controller - No departments for college ' . $this->college['id'] . ', loaded all departments: ' . count($departments));
        } else {
            log_message('debug', 'Courses Controller - Departments loaded for college ' . $this->college['id'] . ': ' . count($departments));
        }

        $data['departments'] = $departments;
        $data['current_user_role'] = $user_role; // Pass current user role to view

        // Statistics for course management
        $course_ids = array_column($data["courses"], 'id');

        // Count modules and lessons for visible courses
        $total_modules = 0;
        $total_lessons = 0;

        if (!empty($course_ids)) {
            $total_modules = $this->db_model->count(TABLE_COURSE_MODULES, [
                "is_active" => 1,
                "course_id IN (" . implode(',', $course_ids) . ")" => null
            ]);

            $total_lessons = $this->db_model->count(TABLE_COURSE_MODULE_LESSONS, [
                "is_active" => 1,
                "module_id IN (SELECT id FROM " . TABLE_COURSE_MODULES . " WHERE course_id IN (" . implode(',', $course_ids) . ") AND is_active = 1)" => null
            ]);
        }

        $data["stats"] = array(
            "total_courses" => count($data["courses"]),
            "total_modules" => $total_modules,
            "total_lessons" => $total_lessons,
            "total_students_enrolled" => $this->db_model->count(TABLE_COURSE_ENROLLMENTS, ["status" => "enrolled"])
        );

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/courses/index', $data);
        $this->load->view('common/footer');
    }

    public function add() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Course Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('code', 'Course Code', 'trim|required');
            $this->form_validation->set_rules('department', 'Department', 'trim'); // Made optional
            $this->form_validation->set_rules('description', 'Description', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
                $user_department = $this->session_data['department'] ?? null;
                $requested_department = $this->input->post('department');

                // Check role-based permissions
                if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
                    // HOD and Staff can only create courses in their department
                    if ($requested_department != $user_department) {
                        $this->session->set_flashdata('message', array('danger', "You can only create courses in your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $course_name = trim($this->input->post('name'));
                $course_code = trim($this->input->post('code'));

                // Check if course with same name already exists
                $existing_course = $this->db_model->get_row(TABLE_COURCES, [
                    'name' => $course_name,
                    'college_id' => $this->college['id'],
                    'is_active' => 1
                ]);

                if ($existing_course) {
                    $course_id = $existing_course['id'];

                    // Auto-enroll students from selected department for existing course
                    if (!empty($requested_department)) {
                        $department_students = $this->db_model->get_all(TABLE_STUDENT, [
                            'department' => $requested_department,
                            'college_id' => $this->college['id'],
                            'is_active' => 1
                        ]);

                        $enrolled_count = 0;
                        foreach ($department_students as $student) {
                            $enrollment_data = [
                                'course_id' => $course_id,
                                'student_id' => $student['id'],
                                'enrolled_by' => $this->session_data['id'],
                                'progress_percentage' => 0.00,
                                'status' => 'enrolled',
                                'created_by' => $this->session_data['id']
                            ];

                            // Check if student is not already enrolled
                            $existing_enrollment = $this->db_model->get_row(TABLE_COURSE_ENROLLMENTS, [
                                'course_id' => $course_id,
                                'student_id' => $student['id']
                            ]);

                            if (!$existing_enrollment) {
                                $this->db_model->insert(TABLE_COURSE_ENROLLMENTS, $enrollment_data);
                                $enrolled_count++;
                            }
                        }

                        $this->session->set_flashdata('message', array('info', "Course '{$course_name}' already exists. Using existing course. {$enrolled_count} students from the selected department were auto-enrolled."));
        } else {
                        $this->session->set_flashdata('message', array('info', "Course '{$course_name}' already exists. Using existing course."));
                    }
            } else {
                    $data = array(
                        'name' => $course_name,
                        'course_code' => $course_code, // Fixed column name
                        'description' => $this->input->post('description'),
                        'college_id' => $this->college['id'],
                        'created_by' => $this->session_data['id'],
                        'is_active' => 1
                    );

                    // Only add department column if it exists in the table
                    if ($this->db->field_exists('department', TABLE_COURCES)) {
                        $data['department'] = $requested_department;
                    }

                    if ($this->db_model->insert(TABLE_COURCES, $data)) {
                        $course_id = $this->db->insert_id();

                        // Auto-enroll students from selected department
                        if (!empty($requested_department)) {
                            $department_students = $this->db_model->get_all(TABLE_STUDENT, [
                                'department' => $requested_department,
                                'college_id' => $this->college['id'],
                                'is_active' => 1
                            ]);

                            $enrolled_count = 0;
                            foreach ($department_students as $student) {
                                $enrollment_data = [
                                    'course_id' => $course_id,
                                    'student_id' => $student['id'],
                                    'enrolled_by' => $this->session_data['id'],
                                    'progress_percentage' => 0.00,
                                    'status' => 'enrolled',
                                    'created_by' => $this->session_data['id']
                                ];

                                // Check if student is not already enrolled
                                $existing_enrollment = $this->db_model->get_row(TABLE_COURSE_ENROLLMENTS, [
                                    'course_id' => $course_id,
                                    'student_id' => $student['id']
                                ]);

                                if (!$existing_enrollment) {
                                    $this->db_model->insert(TABLE_COURSE_ENROLLMENTS, $enrollment_data);
                                    $enrolled_count++;
                                }
                            }

                            $this->session->set_flashdata('message', array('success', "Course Created successfully! {$enrolled_count} students from the selected department were auto-enrolled."));
        } else {
                            $this->session->set_flashdata('message', array('success', "Course Created successfully!"));
                        }
        } else {
                        $this->session->set_flashdata('message', array('danger', "Failed to create Course."));
                    }
                }
                redirect(base_url($this->url . "/courses"));
            }
        } else {
            // Load the add course form for GET requests
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, [
                "is_active" => 1,
                "college_id" => $this->college['id']
            ]);

            // Pre-select department for HODs
            $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
            if ($user_role == ROLE_HOD) {
                $data["selected_department"] = $this->session_data['department'];
            }

            $data["url"] = $this->url;
            $class["classname"] = "courses";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/courses");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/courses/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function edit($id = null) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Course Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('code', 'Course Code', 'trim|required');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
                $user_department = $this->session_data['department'] ?? null;
                $requested_department = $this->input->post('department');

                // Check role-based permissions
                if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
                    // HOD and Staff can only edit courses in their department
                    if ($requested_department != $user_department) {
                        $this->session->set_flashdata('message', array('danger', "You can only edit courses in your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $data = array(
                    'name' => $this->input->post('name'),
                    'code' => $this->input->post('code'),
                    'description' => $this->input->post('description'),
                    'updated_by' => $this->session_data['id']
                );

                // Only update department column if it exists in the table
                if ($this->db->field_exists('department', TABLE_COURCES)) {
                    $data['department'] = $requested_department;
                }

                if ($this->db_model->update(TABLE_COURCES, $data, ["id" => $post['id']])) {
                    $this->session->set_flashdata('message', array('success', "Course Updated successfully!"));
        } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Course."));
                }
                redirect(base_url($this->url . "/courses"));
            }
        } else {
            // Load the edit course form for GET requests
            $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $id, "is_active" => 1]);
            if (!$course) {
                $this->session->set_flashdata('message', array('danger', "Course not found."));
                redirect(base_url($this->url.'/courses'));
                return;
            }

            // Check role-based access for editing
            $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
            if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
                if ($this->db->field_exists('department', TABLE_COURCES) && isset($course['department']) && $course['department'] != $this->session_data['department']) {
                    $this->session->set_flashdata('message', array('danger', "You can only edit courses in your department."));
                    redirect(base_url($this->url.'/courses'));
                    return;
                }
            }

            $data["course"] = $course;
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, [
                "is_active" => 1,
                "college_id" => $this->college['id']
            ]);

            $data["url"] = $this->url;
            $class["classname"] = "courses";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/courses");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/courses/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function delete($id) {
        // Check if user has permission to delete courses
        if (!$this->faculty_common->has_permission($this->session_data, 'delete', 'course')) {
            $this->session->set_flashdata('message', array('danger', "You do not have permission to delete courses."));
            redirect(base_url($this->url . "/courses"));
            return;
        }

        $result = $this->db_model->delete(TABLE_COURCES, ["id" => $id]);
        $message = array('success', "Course Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/courses"));
    }

    public function modules($course_id = null) {
        $data["url"] = $this->url;
        $data["course_id"] = $course_id;
        $class["classname"] = "course_modules";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/courses");

        // Check course access permission
        $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $course_id, "is_active" => 1]);
        if (!$course) {
            $this->session->set_flashdata('message', array('danger', "Course not found."));
            redirect(base_url($this->url . "/courses"));
            return;
        }

        // Check role-based access
        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
            if ($this->db->field_exists('department', TABLE_COURCES) && isset($course['department']) && $course['department'] != $this->session_data['department']) {
                $this->session->set_flashdata('message', array('danger', "You can only access modules for courses in your department."));
                redirect(base_url($this->url . "/courses"));
                return;
            }
        }

        $data["course"] = $course;
        $data["current_user_role"] = $user_role; // Pass current user role to view
        $data["permissions"] = $this->permissions; // Pass permissions to view
        $data["modules"] = $this->db_model->get_all(TABLE_COURSE_MODULES, [
            "course_id" => $course_id,
            "is_active" => 1
        ]);

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/courses/modules', $data);
        $this->load->view('common/footer');
    }

    public function lessons($course_id = null, $module_id = null) {
            $data["url"] = $this->url;
        $data["course_id"] = $course_id;
        $data["module_id"] = $module_id;
        $class["classname"] = "course_lessons";
            $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/courses");

        // Check access permissions
        $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $course_id, "is_active" => 1]);
        $module = $this->db_model->get_row(TABLE_COURSE_MODULES, ["id" => $module_id, "is_active" => 1]);

        if (!$course || !$module) {
            $this->session->set_flashdata('message', array('danger', "Course or module not found."));
                redirect(base_url($this->url . "/courses"));
            return;
        }

        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
            if ($this->db->field_exists('department', TABLE_COURCES) && isset($course['department']) && $course['department'] != $this->session_data['department']) {
                $this->session->set_flashdata('message', array('danger', "You can only access lessons for courses in your department."));
                redirect(base_url($this->url . "/courses"));
                return;
            }
        }

        $data["course"] = $course;
        $data["module"] = $module;
        $data["current_user_role"] = $user_role; // Pass current user role to view
        $data["lessons"] = $this->db_model->get_all("course_module_lessons", [
                "module_id" => $module_id,
            "is_active" => 1
        ]);

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/courses/lessons', $data);
        $this->load->view('common/footer');
    }

    public function enrollments($course_id = null) {
        $data["url"] = $this->url;
        $data["course_id"] = $course_id;
        $class["classname"] = "course_enrollments";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/courses");

        // Check course access permission
        $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $course_id, "is_active" => 1]);
        if (!$course) {
            $this->session->set_flashdata('message', array('danger', "Course not found."));
            redirect(base_url($this->url . "/courses"));
            return;
        }

        // Check role-based access
        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
            if ($this->db->field_exists('department', TABLE_COURCES) && isset($course['department']) && $course['department'] != $this->session_data['department']) {
                $this->session->set_flashdata('message', array('danger', "You can only access enrollments for courses in your department."));
                redirect(base_url($this->url . "/courses"));
                return;
            }
        }

        $data["course"] = $course;
        $data["enrollments"] = $this->db_model->get_all(TABLE_COURSE_ENROLLMENTS, [
            "course_id" => $course_id
        ]);

        // Get student details for each enrollment
        foreach ($data["enrollments"] as $key => $enrollment) {
            $student = $this->db_model->get_row(TABLE_STUDENT, ["id" => $enrollment['student_id'], "is_active" => 1]);
            $data["enrollments"][$key]['student_name'] = $student ? $student['name'] : 'Unknown';
            $data["enrollments"][$key]['student_email'] = $student ? $student['email'] : 'Unknown';
        }

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/courses/enrollments', $data);
        $this->load->view('common/footer');
    }

    // Module CRUD Methods
    public function add_module() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Module Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');
            $this->form_validation->set_rules('order', 'Order', 'trim|required|numeric');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
                } else {
                $course_id = $this->input->post('course_id');

                // Check course access permission
                $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $course_id, "is_active" => 1]);
                if (!$course) {
                    $this->session->set_flashdata('message', array('danger', "Course not found."));
                    redirect(base_url($this->url . "/courses"));
                    return;
                }

                $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
                if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
                    if ($this->db->field_exists('department', TABLE_COURCES) && isset($course['department']) && $course['department'] != $this->session_data['department']) {
                        $this->session->set_flashdata('message', array('danger', "You can only add modules to courses in your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $data = array(
                    'course_id' => $course_id,
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'order' => $this->input->post('order'),
                    'is_active' => 1,
                    'created_by' => $this->session_data['id']
                );

                if ($this->db_model->insert(TABLE_COURSE_MODULES, $data)) {
                    $this->session->set_flashdata('message', array('success', "Module Created successfully!"));
            } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Module."));
            }
                redirect(base_url($this->url . "/courses/modules/" . $course_id));
        }
    }
        }

    public function edit_module($course_id = null, $module_id = null) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Module Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');
            $this->form_validation->set_rules('order', 'Order', 'trim|required|numeric');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
        } else {
                // Check course access permission
                $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $course_id, "is_active" => 1]);
                if (!$course) {
                    $this->session->set_flashdata('message', array('danger', "Course not found."));
                redirect(base_url($this->url . "/courses"));
                    return;
                }

                $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
                if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
                    if ($this->db->field_exists('department', TABLE_COURCES) && isset($course['department']) && $course['department'] != $this->session_data['department']) {
                        $this->session->set_flashdata('message', array('danger', "You can only edit modules for courses in your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $data = array(
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'order' => $this->input->post('order'),
                    'updated_by' => $this->session_data['id']
                );

                if ($this->db_model->update(TABLE_COURSE_MODULES, $data, ["id" => $post['module_id']])) {
                    $this->session->set_flashdata('message', array('success', "Module Updated successfully!"));
                    } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Module."));
                }
                redirect(base_url($this->url . "/courses/modules/" . $course_id));
            }
        }
    }

    public function delete_module($course_id = null, $module_id = null) {
        // Check course access permission
        $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $course_id, "is_active" => 1]);
        if (!$course) {
            $this->session->set_flashdata('message', array('danger', "Course not found."));
            redirect(base_url($this->url . "/courses"));
            return;
        }

        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Only Principal and Vice-Principal can delete modules
        if (!$this->faculty_common->has_permission($this->session_data, 'delete', 'module')) {
            $this->session->set_flashdata('message', array('danger', "You do not have permission to delete modules."));
            redirect(base_url($this->url . "/courses/modules/" . $course_id));
            return;
        }

        $result = $this->db_model->delete(TABLE_COURSE_MODULES, ["id" => $module_id]);
        $message = array('success', "Module Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/courses/modules/" . $course_id));
    }

    // Lesson CRUD Methods
    public function add_lesson() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('title', 'Lesson Title', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('type', 'Lesson Type', 'trim|required');
            $this->form_validation->set_rules('content', 'Description', 'trim|required');
            $this->form_validation->set_rules('order', 'Order', 'trim|required|numeric');

            // Get lesson type first
            $lesson_type = trim($this->input->post('type'));
            
            // Conditional validation based on lesson type - only set rules for the selected type
            if ($lesson_type === LESSON_TYPE_TEXT) {
                $this->form_validation->set_rules('lesson_text', 'Lesson Text', 'trim|required', array('required' => 'The Lesson Text field is required for text type lessons.'));
            } elseif ($lesson_type === LESSON_TYPE_VIDEO) {
                $this->form_validation->set_rules('lesson_video', 'Video URL', 'trim|required|valid_url', array('required' => 'The Video URL field is required for video type lessons.', 'valid_url' => 'Please enter a valid video URL.'));
            } elseif ($lesson_type === LESSON_TYPE_FILE) {
                // File validation will be handled in the upload logic below
                // No form validation rule needed here as file validation is different
            } else {
                // If lesson type is not set or invalid, show error
                $this->session->set_flashdata('message', array('danger', "Please select a valid lesson type."));
                return redirect($_SERVER['HTTP_REFERER']);
            }

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
        } else {
                $course_id = $this->input->post('course_id');
                $module_id = $this->input->post('module_id');

                // Check course and module access permission
                $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $course_id, "is_active" => 1]);
                $module = $this->db_model->get_row(TABLE_COURSE_MODULES, ["id" => $module_id, "is_active" => 1]);

                if (!$course || !$module) {
                    $this->session->set_flashdata('message', array('danger', "Course or module not found."));
                    redirect(base_url($this->url . "/courses"));
                    return;
                }

                $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
                if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
                    if ($this->db->field_exists('department', TABLE_COURCES) && isset($course['department']) && $course['department'] != $this->session_data['department']) {
                        $this->session->set_flashdata('message', array('danger', "You can only add lessons to courses in your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                // Handle file upload for course_file
                $course_file_path = null;
                if ($lesson_type === LESSON_TYPE_FILE) {
                    // For file type, file is required
                    if (empty($_FILES['lesson_file']['name'])) {
                        $this->session->set_flashdata('message', array('danger', "Please upload a file for this lesson."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                    
                    $config['upload_path'] = './uploads/course_files/';
                    $config['allowed_types'] = 'pdf|doc|docx|ppt|pptx|txt|jpg|jpeg|png|gif';
                    $config['max_size'] = 10240; // 10MB
                    $config['file_name'] = 'lesson_' . time() . '_' . rand(1000, 9999);

                    // Create directory if it doesn't exist
                    if (!is_dir($config['upload_path'])) {
                        mkdir($config['upload_path'], 0777, true);
                    }

                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload('lesson_file')) {
                        $upload_data = $this->upload->data();
                        $course_file_path = 'uploads/course_files/' . $upload_data['file_name'];
                    } else {
                        $error = $this->upload->display_errors('', '');
                        $this->session->set_flashdata('message', array('danger', "File upload failed: " . $error));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                } elseif (!empty($_FILES['lesson_file']['name'])) {
                    // Optional file upload for other types
                    $config['upload_path'] = './uploads/course_files/';
                    $config['allowed_types'] = 'pdf|doc|docx|ppt|pptx|txt|jpg|jpeg|png|gif';
                    $config['max_size'] = 10240; // 10MB
                    $config['file_name'] = 'lesson_' . time() . '_' . rand(1000, 9999);

                    // Create directory if it doesn't exist
                    if (!is_dir($config['upload_path'])) {
                        mkdir($config['upload_path'], 0777, true);
                    }

                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload('lesson_file')) {
                        $upload_data = $this->upload->data();
                        $course_file_path = 'uploads/course_files/' . $upload_data['file_name'];
                    }
                }

                $data = array(
                    'module_id' => $module_id,
                    'title' => $this->input->post('title'),
                    'type' => $this->input->post('type'),
                    'content' => $this->input->post('content'),
                    'course_text' => $this->input->post('lesson_text'),
                    'course_url' => $this->input->post('lesson_video'),
                    'course_file' => $course_file_path,
                    'duration' => $this->input->post('duration'),
                    'order' => $this->input->post('order'),
                    'is_active' => $this->input->post('is_active') ?? 1,
                    'created_by' => $this->session_data['id']
                );

                if ($this->db_model->insert('course_module_lessons', $data)) {
                    $this->session->set_flashdata('message', array('success', "Lesson Created successfully!"));
        } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Lesson."));
                }
                redirect(base_url($this->url . "/courses/lessons/" . $course_id . "/" . $module_id));
            }
        }
    }

    public function edit_lesson($course_id = null, $module_id = null, $lesson_id = null) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('title', 'Lesson Title', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('type', 'Lesson Type', 'trim|required');
            $this->form_validation->set_rules('content', 'Description', 'trim|required');
            $this->form_validation->set_rules('order', 'Order', 'trim|required|numeric');

            // Get lesson type first
            $lesson_type = trim($this->input->post('type'));
            
            // Conditional validation based on lesson type - only validate the relevant field
            if ($lesson_type === LESSON_TYPE_TEXT) {
                $this->form_validation->set_rules('lesson_text', 'Lesson Text', 'trim|required');
                // Clear validation for other type-specific fields
                $this->form_validation->set_rules('lesson_video', 'Video URL', 'trim');
            } elseif ($lesson_type === LESSON_TYPE_VIDEO) {
                $this->form_validation->set_rules('lesson_video', 'Video URL', 'trim|required|valid_url');
                // Clear validation for other type-specific fields
                $this->form_validation->set_rules('lesson_text', 'Lesson Text', 'trim');
            } elseif ($lesson_type === LESSON_TYPE_FILE) {
                // For file type, check if file is uploaded (only for new files, not required for edit)
                // File validation is handled in the upload logic below
                // Clear validation for other type-specific fields
                $this->form_validation->set_rules('lesson_text', 'Lesson Text', 'trim');
                $this->form_validation->set_rules('lesson_video', 'Video URL', 'trim');
            }

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
                            } else {
                // Check course and module access permission
                $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $course_id, "is_active" => 1]);
                $module = $this->db_model->get_row(TABLE_COURSE_MODULES, ["id" => $module_id, "is_active" => 1]);

                if (!$course || !$module) {
                    $this->session->set_flashdata('message', array('danger', "Course or module not found."));
                    redirect(base_url($this->url . "/courses"));
                    return;
                }

                $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
                if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
                    if ($this->db->field_exists('department', TABLE_COURCES) && isset($course['department']) && $course['department'] != $this->session_data['department']) {
                        $this->session->set_flashdata('message', array('danger', "You can only edit lessons for courses in your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                // Handle file upload for course_file (only if a new file is uploaded)
                $course_file_path = null;
                if (!empty($_FILES['lesson_file']['name'])) {
                    $config['upload_path'] = './uploads/course_files/';
                    $config['allowed_types'] = 'pdf|doc|docx|ppt|pptx|txt|jpg|jpeg|png|gif';
                    $config['max_size'] = 10240; // 10MB
                    $config['file_name'] = 'lesson_' . time() . '_' . rand(1000, 9999);

                    // Create directory if it doesn't exist
                    if (!is_dir($config['upload_path'])) {
                        mkdir($config['upload_path'], 0777, true);
                    }

                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload('lesson_file')) {
                        $upload_data = $this->upload->data();
                        $course_file_path = 'uploads/course_files/' . $upload_data['file_name'];
                    }
                }

                $data = array(
                    'title' => $this->input->post('title'),
                    'type' => $this->input->post('type'),
                    'content' => $this->input->post('content'),
                    'course_text' => $this->input->post('lesson_text'),
                    'course_url' => $this->input->post('lesson_video'),
                    'duration' => $this->input->post('duration'),
                    'order' => $this->input->post('order'),
                    'is_active' => $this->input->post('is_active') ?? 1,
                    'updated_by' => $this->session_data['id']
                );

                // Only update course_file if a new file was uploaded
                if ($course_file_path) {
                    $data['course_file'] = $course_file_path;
                }

                if ($this->db_model->update('course_module_lessons', $data, ["id" => $post['lesson_id']])) {
                    $this->session->set_flashdata('message', array('success', "Lesson Updated successfully!"));
                            } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Lesson."));
                }
                redirect(base_url($this->url . "/courses/lessons/" . $course_id . "/" . $module_id));
            }
        }
    }

    public function view_lesson($course_id = null, $module_id = null, $lesson_id = null) {
        $data["url"] = $this->url;
        $data["course_id"] = $course_id;
        $data["module_id"] = $module_id;
        $data["lesson_id"] = $lesson_id;

        $class["classname"] = "course_lesson_view";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/courses");

        // Check access permissions
        $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $course_id, "is_active" => 1]);
        $module = $this->db_model->get_row(TABLE_COURSE_MODULES, ["id" => $module_id, "is_active" => 1]);
        $lesson = $this->db_model->get_row(TABLE_COURSE_MODULE_LESSONS, ["id" => $lesson_id, "is_active" => 1]);

        if (!$course || !$module || !$lesson) {
            $this->session->set_flashdata('message', array('danger', "Course, module, or lesson not found."));
            redirect(base_url($this->url . "/courses"));
            return;
        }


        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
            if ($this->db->field_exists('department', TABLE_COURCES) && isset($course['department']) && $course['department'] != $this->session_data['department']) {
                $this->session->set_flashdata('message', array('danger', "You can only view lessons for courses in your department."));
                redirect(base_url($this->url . "/courses"));
                return;
            }
        }

        $data["course"] = $course;
        $data["module"] = $module;
        $data["lesson"] = $lesson;

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/courses/view_lesson', $data);
        $this->load->view('common/footer');
    }

    public function delete_lesson($course_id = null, $module_id = null, $lesson_id = null) {
        // Check course and module access permission
        $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $course_id, "is_active" => 1]);
        $module = $this->db_model->get_row(TABLE_COURSE_MODULES, ["id" => $module_id, "is_active" => 1]);

        if (!$course || !$module) {
            $this->session->set_flashdata('message', array('danger', "Course or module not found."));
            redirect(base_url($this->url . "/courses"));
            return;
        }

        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Only Principal and Vice-Principal can delete lessons
        if (!$this->faculty_common->has_permission($this->session_data, 'delete', 'lesson')) {
            $this->session->set_flashdata('message', array('danger', "You do not have permission to delete lessons."));
            redirect(base_url($this->url . "/courses/lessons/" . $course_id . "/" . $module_id));
            return;
        }

        $result = $this->db_model->delete('course_module_lessons', ["id" => $lesson_id]);
        $message = array('success', "Lesson Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/courses/lessons/" . $course_id . "/" . $module_id));
    }

    // Enrollment CRUD Methods
    public function enroll_student() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('student_id', 'Student', 'trim|required');
            $this->form_validation->set_rules('course_id', 'Course', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
    } else {
                $course_id = $this->input->post('course_id');
                $student_id = $this->input->post('student_id');

                // Check course access permission
                $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $course_id, "is_active" => 1]);
                $student = $this->db_model->get_row(TABLE_STUDENT, ["id" => $student_id, "is_active" => 1]);

                if (!$course || !$student) {
                    $this->session->set_flashdata('message', array('danger', "Course or student not found."));
                    redirect(base_url($this->url . "/courses/enrollments/" . $course_id));
                    return;
                }

                // Check if student is already enrolled
                $existing_enrollment = $this->db_model->get_row(TABLE_COURSE_ENROLLMENTS, [
                "student_id" => $student_id,
                "course_id" => $course_id
                ]);

                if ($existing_enrollment) {
                    $this->session->set_flashdata('message', array('danger', "Student is already enrolled in this course."));
                    redirect(base_url($this->url . "/courses/enrollments/" . $course_id));
                    return;
                }

                $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
                if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
                    if ($this->db->field_exists('department', TABLE_COURCES) && isset($course['department']) && $course['department'] != $this->session_data['department']) {
                        $this->session->set_flashdata('message', array('danger', "You can only enroll students in courses from your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                    if ($student['department'] != $this->session_data['department']) {
                        $this->session->set_flashdata('message', array('danger', "You can only enroll students from your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $data = array(
            'course_id' => $course_id,
                    'student_id' => $student_id,
                    'enrolled_by' => $this->session_data['id'],
                    'status' => 'enrolled',
                    'progress_percentage' => 0.00,
                    'enrolled_at' => date('Y-m-d H:i:s')
                );

                if ($this->db_model->insert(TABLE_COURSE_ENROLLMENTS, $data)) {
                    $this->session->set_flashdata('message', array('success', "Student Enrolled successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to enroll student."));
                }
                redirect(base_url($this->url . "/courses/enrollments/" . $course_id));
            }
        }
    }

    public function update_enrollment_status($enrollment_id = null, $status = null) {
        // Check enrollment access permission
        $enrollment = $this->db_model->get_row(TABLE_COURSE_ENROLLMENTS, ["id" => $enrollment_id]);
        if (!$enrollment) {
            $this->session->set_flashdata('message', array('danger', "Enrollment not found."));
            redirect(base_url($this->url . "/courses"));
            return;
        }

        $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $enrollment['course_id'], "is_active" => 1]);
        if (!$course) {
            $this->session->set_flashdata('message', array('danger', "Course not found."));
            redirect(base_url($this->url . "/courses"));
            return;
        }

        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
            if ($this->db->field_exists('department', TABLE_COURCES) && isset($course['department']) && $course['department'] != $this->session_data['department']) {
                $this->session->set_flashdata('message', array('danger', "You can only manage enrollments for courses in your department."));
                redirect(base_url($this->url . "/courses/enrollments/" . $course['id']));
                return;
            }
        }

        // NOTE: Manual status changes are disabled. Status is now automatically calculated based on lesson completion.
        // This method is kept for backward compatibility but should not be used.
        // Status changes happen automatically when students complete lessons.
        
        $this->session->set_flashdata('message', array('info', "Enrollment status is now automatically calculated based on lesson progress. Manual status changes are disabled."));
        redirect(base_url($this->url . "/courses/enrollments/" . $course['id']));
    }

    // Certificate Request Management
    public function certificate_requests() {
        $role = (int)($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        
        // Only Principal and Vice-Principal can access
        // Debug: Log role values for troubleshooting
        log_message('debug', 'Certificate Requests Access Check - Role: ' . $role . ', ROLE_PRINCIPAL: ' . ROLE_PRINCIPAL . ', ROLE_VICE_PRINCIPAL: ' . ROLE_VICE_PRINCIPAL);
        log_message('debug', 'Session data: ' . json_encode($this->session_data));
        
        if (!in_array($role, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL], true)) {
            $this->session->set_flashdata('message', array('danger', "You don't have permission to access certificate requests. Only Principal and Vice-Principal can access this page."));
            redirect(base_url($this->url . "/dashboard"));
            return;
        }

        $this->load->model('Certificate_request_model', 'cert_request');
        
        $data['url'] = $this->url;
        $data['college'] = $this->college;
        $data['session_data'] = $this->session_data;
        
        // Get pending requests
        $data['pending_requests'] = $this->cert_request->get_pending_requests();
        
        // Get all requests for history
        $data['all_requests'] = $this->cert_request->get_all_requests();
        
        $class['classname'] = "certificate_requests";
        $class['url'] = $this->url;
        $class['sidebar_href'] = base_url($this->url . "/courses/certificate_requests");
        $class['college'] = $this->college;

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/courses/certificate_requests', $data);
        $this->load->view('common/footer');
    }

    // Approve certificate request
    public function approve_certificate_request($request_id) {
        $role = (int)($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        
        if (!in_array($role, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL], true)) {
            $this->session->set_flashdata('message', array('danger', "You don't have permission to approve certificate requests."));
            redirect(base_url($this->url . "/courses/certificate_requests"));
            return;
        }

        $this->load->model('Certificate_request_model', 'cert_request');
        $this->load->model('Certificate_model', 'certificate_model');
        $this->load->library('Certificate_generator');
        
        $request = $this->cert_request->get_request($request_id);
        
        if (!$request) {
            $this->session->set_flashdata('message', array('danger', "Certificate request not found."));
            redirect(base_url($this->url . "/courses/certificate_requests"));
            return;
        }

        if ($request['status'] !== 'pending') {
            $this->session->set_flashdata('message', array('warning', "This request has already been processed."));
            redirect(base_url($this->url . "/courses/certificate_requests"));
            return;
        }

        // Get enrollment details
        $enrollment = $this->db_model->get_row(TABLE_COURSE_ENROLLMENTS, ['id' => $request['enrollment_id']]);
        $course = $this->db_model->get_row(TABLE_COURCES, ['id' => $request['course_id']]);
        $student = $this->db_model->get_row(TABLE_STUDENT, ['id' => $request['student_id']]);

        if (!$enrollment || !$course || !$student) {
            $this->session->set_flashdata('message', array('danger', "Invalid request data."));
            redirect(base_url($this->url . "/courses/certificate_requests"));
            return;
        }

        // Approve the request
        $notes = $this->input->post('notes');
        if ($this->cert_request->approve_request($request_id, $this->session_data['id'], $notes)) {
            // Generate certificate
            try {
                // Check if certificate already exists
                if (!$this->certificate_model->certificate_exists($enrollment['id'])) {
                    // Generate certificate number
                    $cert_number = $this->certificate_model->generate_certificate_number(
                        $course['id'], 
                        $student['id']
                    );
                    
                    // Prepare certificate data
                    $cert_data = [
                        'student_name' => $student['name'],
                        'course_name' => $course['name'],
                        'certificate_number' => $cert_number,
                        'issued_at' => date('Y-m-d H:i:s'),
                        'college_name' => $this->college['name'] ?? 'Educational Institution'
                    ];
                    
                    // Generate certificate file
                    $cert_file = $this->certificate_generator->generate_pdf($cert_data);
                    
                    if ($cert_file !== false) {
                        // Save certificate record
                        $cert_record = [
                            'enrollment_id' => $enrollment['id'],
                            'course_id' => $course['id'],
                            'student_id' => $student['id'],
                            'certificate_number' => $cert_number,
                            'certificate_file' => $cert_file,
                            'issued_at' => date('Y-m-d H:i:s'),
                            'issued_by' => $this->session_data['id'],
                            'is_active' => 1
                        ];
                        
                        if ($this->certificate_model->create_certificate($cert_record)) {
                            $this->session->set_flashdata('message', array('success', "Certificate request approved and certificate generated successfully!"));
                        } else {
                            $this->session->set_flashdata('message', array('warning', "Request approved but certificate generation failed. Please try again."));
                        }
                    } else {
                        $this->session->set_flashdata('message', array('warning', "Request approved but certificate file generation failed. Please check uploads/certificates directory permissions."));
                    }
                } else {
                    $this->session->set_flashdata('message', array('info', "Request approved. Certificate already exists for this enrollment."));
                }
            } catch (Exception $e) {
                log_message('error', 'Certificate generation error: ' . $e->getMessage());
                $this->session->set_flashdata('message', array('warning', "Request approved but certificate generation error: " . $e->getMessage()));
            }
        } else {
            $this->session->set_flashdata('message', array('danger', "Failed to approve certificate request."));
        }

        redirect(base_url($this->url . "/courses/certificate_requests"));
    }

    // Reject certificate request
    public function reject_certificate_request($request_id) {
        $role = (int)($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        
        if (!in_array($role, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL], true)) {
            $this->session->set_flashdata('message', array('danger', "You don't have permission to reject certificate requests."));
            redirect(base_url($this->url . "/courses/certificate_requests"));
            return;
        }

        $this->load->model('Certificate_request_model', 'cert_request');
        
        $request = $this->cert_request->get_request($request_id);
        
        if (!$request) {
            $this->session->set_flashdata('message', array('danger', "Certificate request not found."));
            redirect(base_url($this->url . "/courses/certificate_requests"));
            return;
        }

        if ($request['status'] !== 'pending') {
            $this->session->set_flashdata('message', array('warning', "This request has already been processed."));
            redirect(base_url($this->url . "/courses/certificate_requests"));
            return;
        }

        $rejection_reason = $this->input->post('rejection_reason');
        $notes = $this->input->post('notes');

        if (empty($rejection_reason)) {
            $this->session->set_flashdata('message', array('danger', "Rejection reason is required."));
            redirect(base_url($this->url . "/courses/certificate_requests"));
            return;
        }

        if ($this->cert_request->reject_request($request_id, $this->session_data['id'], $rejection_reason, $notes)) {
            $this->session->set_flashdata('message', array('success', "Certificate request rejected successfully."));
        } else {
            $this->session->set_flashdata('message', array('danger', "Failed to reject certificate request."));
        }

        redirect(base_url($this->url . "/courses/certificate_requests"));
    }

    public function unenroll_student($enrollment_id = null) {
        // Check enrollment access permission
        $enrollment = $this->db_model->get_row(TABLE_COURSE_ENROLLMENTS, ["id" => $enrollment_id]);
        if (!$enrollment) {
            $this->session->set_flashdata('message', array('danger', "Enrollment not found."));
            redirect(base_url($this->url . "/courses"));
            return;
        }

        $course = $this->db_model->get_row(TABLE_COURCES, ["id" => $enrollment['course_id'], "is_active" => 1]);
        if (!$course) {
            $this->session->set_flashdata('message', array('danger', "Course not found."));
            redirect(base_url($this->url . "/courses"));
            return;
        }

        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Only Principal and Vice-Principal can unenroll students
        if (!in_array($user_role, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL])) {
            $this->session->set_flashdata('message', array('danger', "You do not have permission to unenroll students."));
            redirect(base_url($this->url . "/courses/enrollments/" . $course['id']));
            return;
        }

        $result = $this->db_model->update(TABLE_COURSE_ENROLLMENTS, ["status" => "dropped"], ["id" => $enrollment_id]);
        $message = array('success', "Student Unenrolled Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/courses/enrollments/" . $course['id']));
    }

    public function students() {
        // Show course enrollment overview
        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/courses");
        $class["college"] = $this->college;

        // Get all course enrollments with student and course details
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

        $this->db->where('courses.college_id', $this->college['id']);
        $this->db->order_by('course_enrollments.enrolled_at', 'DESC');

        $data['enrollments'] = $this->db->get()->result_array();

        // Get summary stats
        $data['total_enrollments'] = count($data['enrollments']);
        $data['active_enrollments'] = count(array_filter($data['enrollments'], function($e) {
            return $e['is_active'] == 1;
        }));

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/courses/students', $data);
        $this->load->view('common/footer');
    }

    // System-level course management (SuperAdmin only)
    public function system_courses()
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_PRINCIPAL) {
            redirect($this->url.'/dashboard');
        }

        // System-wide course management (similar to root Course.php index)
        if ($this->input->is_ajax_request()) {
            $this->handle_system_courses_ajax();
            return;
        }

        $data["url"] = $this->url;
        $class["classname"] = "system_courses";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/system_courses");

        // Get all colleges for filtering
        $data["colleges"] = $this->db_model->get_all(TABLE_COLLEGE, ["is_active" => 1]);

        $data["permissions"] = $this->permissions;

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/system_courses', $data);
        $this->load->view('common/footer');
    }

    private function handle_system_courses_ajax()
    {
        $limit = $this->input->get('length') ?? 50;
        $offset = $this->input->get('start') ?? 0;
        $search_value = $this->input->get('search')['value'] ?? '';
        $college_filter = $this->input->get('college_id');

        $joins = [
            TABLE_COLLEGE . ' AS c' => 'c.id = co.college_id'
        ];

        $select = "co.id, co.course_code, co.name, co.description, co.tag, co.course_mode, co.course_type, co.created_at, co.college_id AS creator_college_id, c.name AS college_name";

        $conditions = ['co.is_active' => 1];

        if (!empty($college_filter)) {
            $college_ids = explode(',', $college_filter);
            $this->db->where_in('co.college_id', $college_ids);
        }

        if (!empty($search_value)) {
            $this->db->group_start()
                ->like('co.course_code', $search_value)
                ->or_like('co.name', $search_value)
                ->or_like('co.description', $search_value)
                ->or_like('co.tag', $search_value)
                ->or_like('c.name', $search_value)
                ->group_end();
        }

        $data = $this->db_model->get_with_joins(
            TABLE_COURCES . ' AS co',
            $select,
            $joins,
            $conditions,
            'co.id',
            'DESC',
            null,
            $limit,
            $offset
        );

        $total = $this->db_model->get_with_joins(
            TABLE_COURCES . ' AS co',
            'COUNT(co.id) as count',
            $joins,
            $conditions
        );

        $response = [
            'draw' => intval($this->input->get('draw')),
            'recordsTotal' => isset($total[0]['count']) ? $total[0]['count'] : 0,
            'recordsFiltered' => isset($total[0]['count']) ? $total[0]['count'] : 0,
            'data' => array_map(function($course) {
                return [
                    $course['id'],
                    htmlspecialchars($course['course_code']),
                    htmlspecialchars($course['name']),
                    htmlspecialchars($course['college_name']),
                    htmlspecialchars($course['course_mode']),
                    htmlspecialchars($course['course_type']),
                    date('Y-m-d', strtotime($course['created_at']))
                ];
            }, $data)
        ];

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // Multi-college course assignment methods (SuperAdmin only)
    public function add_colleges($course_id)
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_PRINCIPAL) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $college_ids = $this->input->post('college_ids');
        if (empty($college_ids) || !is_array($college_ids)) {
            $this->output->set_status_header(400);
            echo json_encode(['error' => 'Invalid college IDs']);
            return;
        }

        // Add colleges to course
        $insert_data = [];
        foreach ($college_ids as $college_id) {
            $insert_data[] = [
                'course_id' => $course_id,
                'college_id' => $college_id,
                'assigned_at' => date('Y-m-d H:i:s')
            ];
        }

        $this->db->insert_batch('course_college_assignments', $insert_data);

        echo json_encode(['success' => true, 'message' => 'Colleges assigned successfully']);
    }

    public function get_colleges()
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_PRINCIPAL) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $colleges = $this->db_model->get_all(TABLE_COLLEGE, ['is_active' => 1]);
        echo json_encode($colleges);
    }

    public function get_shared_colleges($course_id)
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_PRINCIPAL) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        // Get colleges that have access to this course
        $shared_colleges = $this->db->select('c.*')
            ->from('course_college_assignments cca')
            ->join(TABLE_COLLEGE . ' c', 'c.id = cca.college_id')
            ->where('cca.course_id', $course_id)
            ->where('c.is_active', 1)
                ->get()
                ->result_array();

        echo json_encode($shared_colleges);
    }

    public function assign_course()
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_PRINCIPAL) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $course_id = $this->input->post('course_id');
        $college_ids = $this->input->post('college_ids');

        if (empty($course_id) || empty($college_ids) || !is_array($college_ids)) {
            $this->output->set_status_header(400);
            echo json_encode(['error' => 'Invalid data']);
            return;
        }

        // Remove existing assignments for this course
        $this->db->where('course_id', $course_id)->delete('course_college_assignments');

        // Add new assignments
        $insert_data = [];
        foreach ($college_ids as $college_id) {
            $insert_data[] = [
                        'course_id' => $course_id,
                'college_id' => $college_id,
                'assigned_at' => date('Y-m-d H:i:s')
            ];
        }

        $this->db->insert_batch('course_college_assignments', $insert_data);

        echo json_encode(['success' => true, 'message' => 'Course assigned to colleges successfully']);
    }

    public function remove_course_assign($course_id, $college_id)
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_PRINCIPAL) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $this->db->where('course_id', $course_id)
                 ->where('college_id', $college_id)
                 ->delete('course_college_assignments');

        echo json_encode(['success' => true, 'message' => 'Course assignment removed']);
    }

    public function get_exclude_colleges($course_id, $creator_college_id)
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_PRINCIPAL) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        // Get colleges that don't have access to this course (excluding creator college)
        $exclude_colleges = $this->db->select('c.*')
            ->from(TABLE_COLLEGE . ' c')
            ->where('c.is_active', 1)
            ->where('c.id !=', $creator_college_id)
            ->where_not_in('c.id',
                $this->db->select('college_id')
                        ->from('course_college_assignments')
                        ->where('course_id', $course_id)
                        ->get_compiled_select()
            )
            ->get()
            ->result_array();

        echo json_encode($exclude_colleges);
    }
}

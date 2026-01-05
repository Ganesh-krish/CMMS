<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Principal extends CI_Controller {
    private $url;
    private $college;
    private $session_data;
	function __construct() {
        parent::__construct();
        $this->load->model('common', 'faculty_common');
        $this->load->model('Db_model', 'db_model');
        
        $this->url = $this->uri->segment(1);

        // Use unified session approach for all access
        $unified_user = $this->session->userdata('user');
        if ($unified_user && isset($unified_user['user_type']) && $unified_user['user_type'] === 'faculty') {
            // Unified session access
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $unified_user;
        } else {
            // Fallback for legacy access or admin direct access
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = [
                'id' => 0,
                'name' => 'Administrator',
                'role' => ROLE_PRINCIPAL,
                'designation' => DESIGNATION_PRINCIPAL,
                'department' => null,
                'college_id' => $this->college['id'] ?? SINGLE_COLLEGE_ID,
                'user_type' => 'faculty'
            ];
            $this->session->set_userdata('user', $this->session_data);
        }

        // Check permissions using Common model
        $permissions = $this->faculty_common->get_access_permissions($this->session_data);
            $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Allow Principal (SuperAdmin) and Vice-Principal access to principal functions
        $allowed_roles = [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL];
            if(!in_array($role, $allowed_roles)){
            redirect($this->url . '/dashboard'); // Redirect to appropriate dashboard
        }
    }
    public function index()
    {
        // Basic data for the view
        $data["url"] = $this->url;
        $class["classname"] = "home";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url . "/principal");
        $class["college"] = $this->college;
        
        // Get current college and staff/department info
        $college_id = $this->college['id'] ?? SINGLE_COLLEGE_ID;
        $staff_id = $this->session_data['id'] ?? 0;
        $department = $this->session_data['department'] ?? null;
        
        // Get counts for stat cards
        $data["total_students"] = count($this->db_model->get_all(
            TABLE_STUDENT,
            array_merge(["is_active" => true], $this->college_filter(TABLE_STUDENT, $college_id))
        ));
        
        $data["total_courses"] = count($this->db_model->get_all(
            TABLE_COURCES,
            array_merge(["is_active" => true], $this->college_filter(TABLE_COURCES, $college_id))
        ));
        
        // Tests module removed: keep metric zero
        $data["active_tests"] = 0;
            
        $data["total_departments"] = 1; // Only showing own department
        
        // Question bank module removed: zeroed metrics
        $data["total_questions"] = 0;
        $data["code_questions"] = 0;
        $data["mcq_questions"] = 0;
        $data["easy_questions_percent"] = 0;
        $data["medium_questions_percent"] = 0;
        $data["hard_questions_percent"] = 0;
        
        // Tests module removed: provide empty placeholders
        $data["recent_tests"] = [];
        $test_performance = [];

        $question_types = [];

        // Get question subtypes and their counts
        $question_subtypes = [];

        // Prepare data for question types chart
        $data['question_types'] = [];
        $data['question_type_counts'] = [];
        $data['question_type_colors'] = [
            'rgba(54, 162, 235, 0.8)',  // Blue
            'rgba(255, 99, 132, 0.8)',  // Red
            'rgba(255, 206, 86, 0.8)',  // Yellow
            'rgba(75, 192, 192, 0.8)',  // Teal
            'rgba(153, 102, 255, 0.8)', // Purple
            'rgba(255, 159, 64, 0.8)',  // Orange
            'rgba(199, 199, 199, 0.8)', // Grey
            'rgba(83, 102, 255, 0.8)',  // Indigo
        ];

        foreach ($question_types as $type) {
            $data['question_types'][] = $type->type;
            $data['question_type_counts'][] = $type->count;
        }

        // Prepare data for question subtypes chart
        $data['question_subtypes'] = [];
        $data['question_subtype_counts'] = [];
        $data['question_subtype_colors'] = [
            'rgba(54, 162, 235, 0.8)',  // Blue
            'rgba(255, 99, 132, 0.8)',  // Red
            'rgba(255, 206, 86, 0.8)',  // Yellow
            'rgba(75, 192, 192, 0.8)',  // Teal
            'rgba(153, 102, 255, 0.8)', // Purple
            'rgba(255, 159, 64, 0.8)',  // Orange
            'rgba(199, 199, 199, 0.8)', // Grey
            'rgba(83, 102, 255, 0.8)',  // Indigo
        ];

        foreach ($question_subtypes as $subtype) {
            $data['question_subtypes'][] = $subtype->sub_type;
            $data['question_subtype_counts'][] = $subtype->count;
        }

            

        // Get student distribution by department (for pie chart)
        // Since we're only showing one department, create a simplified structure
        $data["department_names"] = [$this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $department])['name'] ?? 'My Department'];
        $data["department_student_counts"] = [$data["total_students"]];
        
        // Get course enrollment data
        $courses_q = $this->db->select('c.id, c.name')
            ->from(TABLE_COURCES . ' c')
            ->where('c.is_active', 1);

        // For admin access ($staff_id = 0), show all courses in the college
        // For regular staff, filter by courses they created
        if ($staff_id !== 0) {
            // Regular staff access - show only courses they created
            $courses_q->where('c.created_by', $staff_id);
        }

        if ($this->has_college_column(TABLE_COURCES)) {
            $courses_q->where('c.college_id', $college_id);
        }
        $courses = $courses_q
            ->limit(10)
            ->get()
            ->result_array();
            
        $data["course_names"] = [];
        $data["course_enrollments"] = [];
        
        foreach ($courses as $course) {
            $enrollments = $this->db->where('course_id', $course['id'])
                ->count_all_results(TABLE_COURSE_STUDENTS);
                
            $data["course_names"][] = $course['name'];
            $data["course_enrollments"][] = $enrollments;
        }
        
        // Tests module removed: zeroed metrics
        $data["test_completion_rate"] = 0;
        $data["avg_test_score"] = 0;
        $data["test_pass_rate"] = 0;
        $data["test_module_stats"] = [];
            
        // Groups module removed: zeroed metrics
        $data["total_groups"] = 0;

        // Get all distinct batch years from students table
        $batch_years_q = $this->db->select('batch')
            ->distinct()
            ->from(TABLE_STUDENT)
            ->where('is_active', 1)
            ->order_by('batch', 'DESC'); // Show most recent batches first
        if ($this->has_college_column(TABLE_STUDENT)) {
            $batch_years_q->where('college_id', $college_id);
        }
        $batch_years = $batch_years_q->get()->result_array();

        // Extract just the years from the results
        $years = array_column($batch_years, 'batch');
        // If you want to limit to specific years (e.g., last 5 years)
        // $years = array_slice($years, 0, 5);

        // Get student counts by department and batch year
        $departments_q = $this->db->select('id, name')
            ->from(TABLE_DEPARTMENT)
            ->where('is_active', 1);
        if ($this->has_college_column(TABLE_DEPARTMENT)) {
            $departments_q->where('college_id', $college_id);
        }
        $departments = $departments_q->get()->result_array();

        $department_batch_counts = [];

        foreach ($departments as $dept) {
            $dept_data = [
                'name' => $dept['name'],
                'batches' => [],
                'total' => 0
            ];
            
            foreach ($years as $year) {
                $count_q = $this->db->where('department', $dept['id'])
                    ->where('batch', $year)
                    ->where('is_active', 1);
                if ($this->has_college_column(TABLE_STUDENT)) {
                    $count_q->where('college_id', $college_id);
                }
                $count = $count_q->count_all_results(TABLE_STUDENT);
                    
                $dept_data['batches'][$year] = $count;
                $dept_data['total'] += $count;
            }
            
            $department_batch_counts[] = $dept_data;
        }

        $data['department_batch_table'] = [
            'years' => $years,
            'departments' => $department_batch_counts
        ];

        $data["manage_student_url"] = base_url($this->url."/principal/students");


        // var_dump($data);die;
        
        $this->load->view('common/sidebar', $class);
        $this->load->view('common/dashboard', $data);
        $this->load->view('common/footer');
    }

    public function view(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password");
        $data["add_url"] = base_url($this->url."/principal/add");
        $class["classname"] = "principal";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");
        $class["college"] = $this->college;
        $data["principal"] = $this->db_model->get_all(
            TABLE_FACULTY,
            [
                "is_active" => true,
                "role" => ROLE_PRINCIPAL
            ]
        );

        $data["vice_principal"] = $this->db_model->get_all(
            TABLE_FACULTY,
            [
                "is_active" => true,
                "role" => ROLE_VICE_PRINCIPAL
            ]
        );

        // Get Department Administrators (HODs)
        $data["department_admins"] = $this->db_model->get_all(
            TABLE_FACULTY,
            [
                "is_active" => true,
                "role" => ROLE_HOD
            ]
        );

        // Load departments for dropdown
        $college_id = $this->college['id'] ?? SINGLE_COLLEGE_ID;

        // First try to load departments for current college
        $data["departments"] = $this->db_model->get_all(
            TABLE_DEPARTMENT,
            ["is_active" => true, "college_id" => $college_id]
        );

        // If no departments found for current college, try loading all departments
        if (empty($data["departments"])) {
            $data["departments"] = $this->db_model->get_all(
                TABLE_DEPARTMENT,
                ["is_active" => true]
            );
        }

        // If still no departments, create some default ones for testing
        if (empty($data["departments"])) {
            $data["departments"] = [
                ["id" => 1, "name" => "Computer Science"],
                ["id" => 2, "name" => "Information Technology"],
                ["id" => 3, "name" => "Electronics"],
                ["id" => 4, "name" => "Mechanical Engineering"],
                ["id" => 5, "name" => "Civil Engineering"]
            ];
        }

        $data["add_vice_principal_url"] = base_url($this->url."/principal/add_vice_principal");
        $data["add_department_admin_url"] = base_url($this->url."/principal/add_department_admin");

		$this->load->view('common/sidebar',$class);
		$this->load->view('faculty/faculty/principal',$data);
		$this->load->view('common/footer');
    }

    public function vice_principal(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password");
        $class["classname"] = "vice_principal";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");
        $class["college"] = $this->college;
        $data["vice_principal"] = $this->db_model->get_all(
            TABLE_FACULTY,
            [
                "is_active" => true,
                "role" => ROLE_VICE_PRINCIPAL
            ]
        );

        foreach ($data["vice_principal"] as $key => $value) {
            $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $value['department']]);
            $data["vice_principal"][$key]['department'] = $dept ? $dept['name'] : 'unknown';
        }
		$this->load->view('common/sidebar',$class);
		$this->load->view('faculty/faculty/vice_principal',$data);
		$this->load->view('common/footer'); 
    }

    public function hod(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password");
        $data["add_url"] = base_url($this->url."/principal/add_hod");
        $class["classname"] = "hod";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");
        $class["college"] = $this->college;
        $data["hod"] = $this->db_model->get_all(
            TABLE_FACULTY,
            [
                "is_active" => true,
                "role" => ROLE_HOD
            ]
        );

        foreach ($data["hod"] as $key => $value) {
            $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $value['department']]);
            $data["hod"][$key]['department'] = $dept ? $dept['name'] : 'unknown';
        }
		$this->load->view('common/sidebar',$class); 
		$this->load->view('faculty/faculty/hod',$data);
		$this->load->view('common/footer');
    }

    public function staff(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password");
        $data["add_url"] = base_url($this->url."/principal/add_staff");
        $class["classname"] = "staff";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");
        $class["college"] = $this->college;
        $data["staff"] = $this->db_model->get_all(
            TABLE_FACULTY,
            [
                "is_active" => true,
                "role" => ROLE_STAFF
            ]
        );

        foreach ($data["staff"] as $key => $value) {
            $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $value['department']]);
            $data["staff"][$key]['department'] = $dept ? $dept['name'] : 'unknown';
        }

        // Load departments for dropdown
        $data["departments"] = $this->db_model->get_all(
            TABLE_DEPARTMENT,
            ["is_active" => true, "college_id" => $this->college['id']]
        );

		$this->load->view('common/sidebar',$class);
		$this->load->view('faculty/faculty/staff',$data);
		$this->load->view('common/footer'); 
    }

    public function groups()
    {
        $data["url"] = $this->url;
        $class["classname"] = "groups";
        $class["url"] =  $this->url;
        $class["college_id"] = $this->college['id'];
        $class["sidebar_href"] = base_url($this->url . "/staff");
        $class["college"] = $this->college;
        $group_conditions = ["is_active"=>1,"college_id"=>$this->college['id']];

        $data["groups"] = $this->db_model->get_all(TABLE_GROUPS,$group_conditions);
        $data["memgroups"] = $this->db_model->get_groupMembers($this->college['id']);
        // var_dump($data["groups"]);die;
        $this->load->view('common/sidebar', $class);
        $this->load->view('groups/view', $data);
        $this->load->view('common/footer');
    }


    public function students(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password_student");
        $class["classname"] = "students";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");
        $class["college"] = $this->college;
        $data["memgroups"] = $this->db_model->get_groupMembers($this->college['id']);


        $department = $this->input->get('department');
        $batch = $this->input->get('batch');

        $conditions = ["is_active"=>true,"college_id"=>$this->college['id']];

        if($department != null){
            $conditions['department'] = $department;
        }
        if($batch != null){
            $conditions['batch'] = $batch;
        }

        $data['departments'] = $this->db_model->get_all(TABLE_DEPARTMENT,["is_active"=>true,"college_id"=>$this->college['id']]);

        // groupby batch from student table

        $data["staff"] = $this->db_model->get_all(TABLE_STUDENT,$conditions);
        foreach ($data["staff"] as $key => $value) {
            $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $value['department']]);
            $data["staff"][$key]['department'] = $dept ? $dept['name'] : 'unknown';
        }

        // get all batches from student table and unique 

        $data['batches'] = $this->db_model->get_all(TABLE_STUDENT,["is_active"=>true,"college_id"=>$this->college['id']]);
        $data['batches'] = array_unique(array_column($data['batches'], 'batch'));


        $data['groups'] = $this->db_model->get_all(TABLE_GROUPS,["is_active"=>true,"college_id"=>$this->college['id']]);
		$this->load->view('common/sidebar',$class); 
		$this->load->view('faculty/faculty/students',$data);
		$this->load->view('common/footer'); 
    }

    public function reset_password(){  
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('id', 'Id', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
                return redirect($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:base_url($this->url."/principal"));
            }
           $update= $this->db_model->update(TABLE_FACULTY,["password"=>password_hash($post['password'], PASSWORD_DEFAULT)],["is_active"=>1,"id"=>$post['id']]);
           if(!$update){
                $this->session->set_flashdata('message',array("danger","Something Went Wrong")); 
                return redirect($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:base_url($this->url."/principal"));
           }
           $this->session->set_flashdata('message',array("success","Password Reseted successfully")); 
           return redirect($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:base_url($this->url."/principal"));
        }
    }
    public function reset_password_student(){  
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('id', 'Id', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
                return redirect($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:base_url($this->url."/principal"));
            }
           $update= $this->db_model->update(TABLE_STUDENT,["password"=>password_hash($post['password'], PASSWORD_DEFAULT)],["is_active"=>1,"college_id"=>$this->college['id'],"id"=>$post['id']]);
           if(!$update){
                $this->session->set_flashdata('message',array("danger","Something Went Wrong")); 
                return redirect($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:base_url($this->url."/principal"));
           }
           $this->session->set_flashdata('message',array("success","Password Reseted successfully")); 
           return redirect($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:base_url($this->url."/principal"));
        }
    }

    private function process_cropped_image($image_data_field) {
        $imageData = $this->input->post($image_data_field); 

        if (empty($imageData)) {
            return null; // Return false if no data from post
        }

        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $decodedData = base64_decode($imageData);

        if ($decodedData) {
            // Create a temporary file
            $temp_file = tempnam(sys_get_temp_dir(), 'img_');
            file_put_contents($temp_file, $decodedData);

            // Prepare file for Cloudinary upload
            $_FILES['image'] = [
                'name' => uniqid() . '.png',
                'type' => 'image/png',
                'tmp_name' => $temp_file,
                'error' => 0,
                'size' => filesize($temp_file)
            ];

            // Upload to Cloudinary using the correct method
            $image_url = $this->faculty_common->upload_to_cloudinary('image', 'college');

            // Clean up temporary file
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }

            if ($image_url) {
                return $image_url;
            } else {
                log_message('error', 'Failed to upload image to Cloudinary');
                return false;
            }
        }
        return null; // Return false if decoding fails
    }

    /**
     * Utility: check if the given table has a college_id column.
     */
    private function has_college_column($table)
    {
        if (!$this->db->table_exists($table)) {
            return false;
        }
        return $this->db->field_exists('college_id', $table);
    }

    /**
     * Utility: return a filter array for college_id if the table supports it.
     */
    private function college_filter($table, $college_id)
    {
        return $this->has_college_column($table) ? ['college_id' => $college_id] : [];
    }


    public function profile(){
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'College Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            }

            $post_image = $this->process_cropped_image('croppedImageData1');
            $post_image_2 = $this->process_cropped_image('croppedImageData2');

            $data = [
                'name' => $this->input->post('name'),
                'address' => $this->input->post('address'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'website' => $this->input->post('website'),
                'correspondent' => $this->input->post('correspondent'),
                'vice_correspondent' => $this->input->post('vice_correspondent'),
                'our_vision' => $this->input->post('our_vision'),
                'our_mission' => $this->input->post('our_mission'),
                'updated_by' => $this->session_data['id']
            ];

            if($post_image){
                $data['logo'] = $post_image;
            }

            if($post_image_2){
                $data['banner'] = $post_image_2;
            }

            if ($this->db_model->update(TABLE_COLLEGE, $data, ["id" => $this->college['id'], "is_active" => true])) {
                $this->session->set_flashdata('message', array('success', 'College details updated successfully!'));
            } else {
                $this->session->set_flashdata('message', array('danger', 'Failed to update college details.'));
            }

            return redirect(base_url($this->url."/principal/profile"));
        } else {
            $class["classname"] = "profile";
            $class["url"] =  $this->url;
            $class["sidebar_href"] = base_url($this->url."/principal");
            $class["college"] = $this->college;

            $data["college"] = $this->db_model->get_row(TABLE_COLLEGE, ["id" => $this->college['id'], "is_active" => true]);
            $data['logo'] = $data["college"]['logo'];
            $data['banner'] = $data["college"]['banner'];
            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/settings', $data);
            $this->load->view('common/footer');
        }
    }

    public function departments(){
        $data["url"] = $this->url;
        $class["classname"] = "departments";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");
        $class["college"] = $this->college;

        // Get departments
        $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, [
            "is_active" => true,
            "college_id" => $this->college['id']
        ]);

        $this->load->view('common/sidebar',$class);
        $this->load->view('faculty/faculty/departments',$data);
        $this->load->view('common/footer');
    }

    // Department CRUD Methods
    public function add_department() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Department Name', 'trim|required|min_length[1]|max_length[255]');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $department_name = trim($this->input->post('name'));

                // Check if department with same name already exists
                $existing_department = $this->db_model->get_row(TABLE_DEPARTMENT, [
                    'name' => $department_name,
                    'college_id' => $this->college['id'],
                    'is_active' => 1
                ]);

                if ($existing_department) {
                    $this->session->set_flashdata('message', array('info', "Department '{$department_name}' already exists. Using existing department."));
                } else {
                    $data = array(
                        'name' => $department_name,
                        'college_id' => $this->college['id'],
                        'created_by' => $this->session_data['id'],
                        "is_active" => 1
                    );
                    if ($this->db_model->insert(TABLE_DEPARTMENT, $data)) {
                        $this->session->set_flashdata('message', array('success', "Department Created successfully!"));
                    } else {
                        $this->session->set_flashdata('message', array('danger', "Failed to create Department."));
                    }
                }
                redirect(base_url($this->url . "/principal/batch_dept"));
            }
        }
    }

    public function edit_department($id) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Department Name', 'trim|required|min_length[1]|max_length[255]');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'updated_by' => $this->session_data['id']
                );
                if ($this->db_model->update(TABLE_DEPARTMENT, $data, ["id" => $id])) {
                    $this->session->set_flashdata('message', array('success', "Department Updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Department."));
                }
                redirect(base_url($this->url . "/principal/batch_dept"));
            }
        }
    }

    public function add() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'role' => ROLE_PRINCIPAL,
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
                    "is_active" => 1,
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Administrator Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Administrator."));
                }
                redirect(base_url($this->url . "/principal/view"));
            }
        }
    }

    public function delete_department($id) {
        $result = $this->db_model->delete(TABLE_DEPARTMENT, ["id" => $id]);
        $message = array('success', "Department Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/principal/batch_dept"));
    }

    // Vice-Principal CRUD Methods
    public function add_vice_principal() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'role' => ROLE_VICE_PRINCIPAL,
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
                    "is_active" => 1,
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Assistant Administrator Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Assistant Administrator."));
                }
                redirect(base_url($this->url . "/principal/view"));
            }
        }
    }

    public function add_department_admin() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department' => $this->input->post('department'),
                    'role' => ROLE_HOD,
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
                    "is_active" => 1,
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Department Administrator Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Department Administrator."));
                }
                redirect(base_url($this->url . "/principal/view"));
            }
        }
    }

    public function edit_vice_principal($id = null) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department' => $this->input->post('department'),
                    'updated_by' => $this->session_data['id']
                );

                if ($this->db_model->update(TABLE_FACULTY, $data, ["id" => $post['id']])) {
                    $this->session->set_flashdata('message', array('success', "Vice-Principal Updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Vice-Principal."));
                }
                redirect(base_url($this->url . "/principal/vice_principal"));
            }
        }
    }

    public function delete_vice_principal($id) {
        $result = $this->db_model->delete(TABLE_FACULTY, ["id" => $id]);
        $message = array('success', "Vice-Principal Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/principal/vice_principal"));
    }

    public function add_hod() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department' => $this->input->post('department'),
                    'role' => ROLE_HOD,
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
                    "is_active" => 1,
                    'password' => password_hash('123456', PASSWORD_DEFAULT) // Default password
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Department Administrator Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Department Administrator."));
                }
                redirect(base_url($this->url . "/principal/hod"));
            }
        }
    }

    public function edit_hod($id = null) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department' => $this->input->post('department'),
                    'updated_by' => $this->session_data['id']
                );

                if ($this->db_model->update(TABLE_FACULTY, $data, ["id" => $id])) {
                    $this->session->set_flashdata('message', array('success', "Department Administrator Updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Department Administrator."));
                }
                redirect(base_url($this->url . "/principal/view"));
            }
        } else {
            // Load data for editing
            $data["hod"] = $this->db_model->get_row(TABLE_FACULTY, ["id" => $id]);
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => true]);
            $data["url"] = $this->url;

            $class["classname"] = "hod_edit";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url . "/principal");
            $class["college"] = $this->college;

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/hod/edit', $data);
            $this->load->view('common/footer');
        }
    }

    public function delete_hod($id) {
        $result = $this->db_model->delete(TABLE_FACULTY, ["id" => $id]);
        $message = array('success', "Department Administrator Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/principal/view"));
    }

    public function add_staff() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department' => $this->input->post('department'),
                    'role' => ROLE_STAFF,
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
                    "is_active" => 1,
                    'password' => password_hash('123456', PASSWORD_DEFAULT) // Default password
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Instructor Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Instructor."));
                }
                redirect(base_url($this->url . "/principal/staff"));
            }
        }
    }


}

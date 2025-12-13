<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Principal extends CI_Controller {
    private $url;
    private $college;
    private $session_data;
	function __construct() {
        parent::__construct();
        $this->load->model('common', 'faculty_common');
        $this->load->model('faculty/db_model', 'db_model');
        $this->load->model('faculty/test_model', 'test_model');
        $this->url = $this->uri->segment(1);
        // Support single-college admin access without an existing session
        if ($this->url === 'admin') {
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = [
                'id' => 0,
                'role' => ROLE_SUPERADMIN,
                'designation' => DESIGNATION_PRINCIPAL,
                'department' => null,
                'college_id' => $this->college['id'] ?? SINGLE_COLLEGE_ID
            ];
        } else {
            $this->faculty_common->check_user_session($this->url);
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $this->session->userdata($this->url);
            $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
            // Allow Principal (SuperAdmin) and Vice-Principal access to college administration
            $allowed_roles = [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, DESIGNATION_PRINCIPAL, DESIGNATION_VICE_PRINCIPAL];
            if(!in_array($role, $allowed_roles)){
                $this->faculty_common->redirect_route($role,$this->url);
            }
        }
    }
    public function index()
    {
        // Basic data for the view
        $data["url"] = $this->url;
        $class["classname"] = "home";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url . "/principal");
        
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
            ->where('c.department', $department)
            ->where('c.created_by', $staff_id)
            ->where('c.is_active', 1);
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
        
        $this->load->view('faculty/faculty/sidebar', $class);
        $this->load->view('faculty/faculty/dashboard', $data);
        $this->load->view('faculty/faculty/footer');
    }

    public function view(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password");

        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Access control for Principal CRUD
        // Principal: Full access
        // Vice-Principal: Read only
        // HOD: Read only
        // Staff: Read only
        if ($user_role === ROLE_SUPERADMIN) {
            $data["add_url"] = base_url($this->url."/principal/add");
            $data["can_edit_principals"] = true;
            $data["can_delete_principals"] = true;
        } elseif ($user_role === ROLE_VICE_PRINCIPAL) {
            $data["can_edit_principals"] = false;
            $data["can_delete_principals"] = false;
        } else {
            // HOD and Staff can only read
            $data["can_edit_principals"] = false;
            $data["can_delete_principals"] = false;
        }

        $class["classname"] = "principal";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");
        $data["principal"] = $this->db_model->get_all(
            TABLE_FACULTY,
            [
                "is_active" => true,
                "role" => ROLE_SUPERADMIN
            ]
        );

		$this->load->view('faculty/faculty/sidebar',$class); 
		$this->load->view('faculty/faculty/principal',$data); 
		$this->load->view('faculty/faculty/footer'); 
    }

    public function vice_principal(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password");

        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Access control for Vice-Principal CRUD
        // Principal: Full access
        // Vice-Principal: Full access (can manage other VPs)
        // HOD: Read only
        // Staff: Read only
        if (in_array($user_role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL])) {
            $data["add_url"] = base_url($this->url."/principal/add_vice_principal");
            $data["can_edit_vice_principals"] = true;
            $data["can_delete_vice_principals"] = true;
        } else {
            // HOD and Staff can only read
            $data["can_edit_vice_principals"] = false;
            $data["can_delete_vice_principals"] = false;
        }

        $class["classname"] = "vice_principal";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");
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
		$this->load->view('faculty/faculty/sidebar',$class);
		$this->load->view('faculty/faculty/vice_principal',$data);
		$this->load->view('faculty/faculty/footer');
    }

    public function hod(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password");

        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        $user_department = $this->session_data['department'] ?? null;

        // Access control for HOD CRUD
        // Principal: Full access
        // Vice-Principal: Full access
        // HOD: Can read all HODs but only update own profile
        // Staff: Read only
        if (in_array($user_role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL])) {
            $data["add_url"] = base_url($this->url."/principal/add_hod");
            $data["can_edit_all_hods"] = true;
            $data["can_delete_hods"] = true;
        } elseif ($user_role === ROLE_HOD) {
            // HOD can only edit their own profile
            $data["can_edit_all_hods"] = false;
            $data["can_edit_own_hod"] = true;
            $data["user_hod_id"] = $this->session_data['id'];
            $data["can_delete_hods"] = false;
        } else {
            // Staff can only read
            $data["can_edit_all_hods"] = false;
            $data["can_delete_hods"] = false;
        }

        $class["classname"] = "hod";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");
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
		$this->load->view('faculty/faculty/sidebar',$class);
		$this->load->view('faculty/faculty/hod',$data);
		$this->load->view('faculty/faculty/footer');
    }

    public function staff(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password");

        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        $user_id = $this->session_data['id'] ?? null;

        // Access control for Staff CRUD
        // Principal: Full access
        // Vice-Principal: Full access
        // HOD: Create staff in own department, Read all staff, Update staff in own department
        // Staff: Read all staff, Update own profile only
        if (in_array($user_role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL])) {
            $data["add_url"] = base_url($this->url."/principal/add_staff");
            $data["can_edit_all_staff"] = true;
            $data["can_delete_staff"] = true;
        } elseif ($user_role === ROLE_HOD) {
            $data["add_url"] = base_url($this->url."/principal/add_staff");
            $data["can_edit_all_staff"] = false;
            $data["can_edit_own_dept_staff"] = true;
            $data["user_department"] = $this->session_data['department'];
            $data["can_delete_staff"] = false;
        } elseif ($user_role === ROLE_STAFF) {
            $data["can_edit_all_staff"] = false;
            $data["can_edit_own_staff"] = true;
            $data["user_staff_id"] = $user_id;
            $data["can_delete_staff"] = false;
        }

        $class["classname"] = "staff";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");
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

		$this->load->view('faculty/faculty/sidebar',$class);
		$this->load->view('faculty/faculty/staff',$data);
		$this->load->view('faculty/faculty/footer'); 
    }

    public function groups()
    {
        $data["url"] = $this->url;
        $class["classname"] = "groups";
        $class["url"] =  $this->url;
        $class["college_id"] = $this->college['id'];
        $class["sidebar_href"] = base_url($this->url . "/staff");
        $group_conditions = ["is_active"=>1,"college_id"=>$this->college['id']];

        $data["groups"] = $this->db_model->get_all(TABLE_GROUPS,$group_conditions);
        $data["memgroups"] = $this->db_model->get_groupMembers($this->college['id']);
        // var_dump($data["groups"]);die;
        $this->load->view('faculty/faculty/sidebar', $class);
        $this->load->view('groups/view', $data);
        $this->load->view('faculty/faculty/footer');
    }


    public function students(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password_student");

        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Access control for Student CRUD
        // Principal: Full access
        // Vice-Principal: Full access
        // HOD: Create, Read, Update students in own department
        // Staff: Read students in own department, Update academic data only
        if (in_array($user_role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL])) {
            $data["add_url"] = base_url($this->url."/principal/add_student");
            $data["can_edit_all_students"] = true;
            $data["can_delete_students"] = true;
        } elseif ($user_role === ROLE_HOD) {
            $data["add_url"] = base_url($this->url."/principal/add_student");
            $data["can_edit_all_students"] = false;
            $data["can_edit_own_dept_students"] = true;
            $data["user_department"] = $this->session_data['department'];
            $data["can_delete_students"] = false;
        } elseif ($user_role === ROLE_STAFF) {
            $data["can_edit_all_students"] = false;
            $data["can_edit_academic_data"] = true;
            $data["can_delete_students"] = false;
        }

        $class["classname"] = "students";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");
        // $data["memgroups"] = $this->db_model->get_groupMembers($this->college['id']); // Temporarily disabled


        $department = $this->input->get('department');
        $batch = $this->input->get('batch');

        $conditions = ["is_active"=>true,"college_id"=>$this->college['id']];

        // Role-based access control for students
        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        $user_department = $this->session_data['department'] ?? null;

        // HOD and Staff can only see students from their department
        if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
            $conditions['department'] = $user_department;
            // Also filter available departments to only show their department
            $data['departments'] = $this->db_model->get_all(TABLE_DEPARTMENT, [
                "is_active" => true,
                "college_id" => $this->college['id'],
                "id" => $user_department
            ]);
        } else {
            // Principal and Vice-Principal can see all departments
            $data['departments'] = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => true, "college_id" => $this->college['id']]);
        }

        // Apply additional filters if provided (only for Principal/Vice-Principal, or within their department for HOD/Staff)
        if($department != null && in_array($user_role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF])){
            // For HOD/Staff, only allow filtering within their department
            if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
                if ($department == $user_department) {
                    $conditions['department'] = $department;
                }
            } else {
                $conditions['department'] = $department;
            }
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

        // get batches based on role-based access
        if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
            // HOD and Staff see only batches from their department
            $batch_conditions = ["is_active" => true, "college_id" => $this->college['id'], "department" => $user_department];
            $data['batches'] = $this->db_model->get_all(TABLE_STUDENT, $batch_conditions);
        } else {
            // Principal and Vice-Principal see all batches
            $data['batches'] = $this->db_model->get_all(TABLE_STUDENT, ["is_active" => true, "college_id" => $this->college['id']]);
        }
        $data['batches'] = array_unique(array_column($data['batches'], 'batch'));


        $data['groups'] = $this->db_model->get_all(TABLE_GROUPS,["is_active"=>true,"college_id"=>$this->college['id']]);
		$this->load->view('faculty/faculty/sidebar',$class);
		$this->load->view('faculty/faculty/students',$data);
		$this->load->view('faculty/faculty/footer'); 
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
           $update= $this->db_model->update(TABLE_FACULTY,["password"=>$post['password']],["is_active"=>1,"id"=>$post['id']]);
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
           $update= $this->db_model->update(TABLE_STUDENT,["password"=>$post['password']],["is_active"=>1,"college_id"=>$this->college['id'],"id"=>$post['id']]);
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
        $post_image = $this->process_cropped_image('croppedImageData1');
        $post_image_2 = $this->process_cropped_image('croppedImageData2');   

        $data = []; 


        if($post_image){
            $data['logo'] = $post_image;
        }

        if($post_image_2){
            $data['banner'] = $post_image_2;
        }

        if(!empty($data)) {
            $this->db_model->update(TABLE_COLLEGE, $data, ["id" => $this->college['id'], "is_active" => true]);
            $this->session->set_flashdata('message', [0, 'Profile updated successfully!']);
        }
        
        return redirect(base_url($this->url."/principal/profile"));
    } else { 
        $class["classname"] = "profile";
        $class["url"] =  $this->url; 
        $class["sidebar_href"] = base_url($this->url."/principal");

        $data["college"] = $this->db_model->get_row(TABLE_COLLEGE, ["id" => $this->college['id'], "is_active" => true]);
        $data['logo'] = $data["college"]['logo'];
        $data['banner'] = $data["college"]['banner'];
        $this->load->view('faculty/faculty/sidebar', $class);
        $this->load->view('faculty/faculty/profile', $data);
        $this->load->view('faculty/faculty/footer');
        }
    }

    public function batch_dept(){
        $data["url"] = $this->url;
        $class["classname"] = "batch_dept";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url."/principal");

        // Get departments
        $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, [
            "is_active" => true,
            "college_id" => $this->college['id']
        ]);

        // Get batches
        $data["batches"] = $this->db_model->get_all(TABLE_BATCHES, [
            "is_active" => true,
            "college_id" => $this->college['id']
        ]);

        $this->load->view('faculty/faculty/sidebar',$class);
        $this->load->view('faculty/faculty/batch_dept',$data);
        $this->load->view('faculty/faculty/footer');
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
                $data = array(
                    'name' => $this->input->post('name'),
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
                    "is_active" => 1
                );
                if ($this->db_model->insert(TABLE_DEPARTMENT, $data)) {
                    $this->session->set_flashdata('message', array('success', "Department Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Department."));
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

    public function delete_department($id) {
        $result = $this->db_model->update(TABLE_DEPARTMENT, ["is_active" => 0], ["id" => $id]);
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
                    'role' => ROLE_VICE_PRINCIPAL,
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
                    "is_active" => 1,
                    'password' => password_hash('123456', PASSWORD_DEFAULT) // Default password
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Vice-Principal Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Vice-Principal."));
                }
                redirect(base_url($this->url . "/principal/vice_principal"));
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
        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Only Principal can delete Vice-Principals (Vice-Principals cannot delete each other)
        if ($user_role !== ROLE_SUPERADMIN) {
            $this->session->set_flashdata('message', array('danger', "You do not have permission to delete Vice-Principals."));
            redirect(base_url($this->url . "/principal/vice_principal"));
            return;
        }

        $result = $this->db_model->update(TABLE_FACULTY, ["is_active" => 0], ["id" => $id]);
        $message = array('success', "Vice-Principal Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/principal/vice_principal"));
    }

    // HOD CRUD Methods
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
                    $this->session->set_flashdata('message', array('success', "HOD Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create HOD."));
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

                if ($this->db_model->update(TABLE_FACULTY, $data, ["id" => $post['id']])) {
                    $this->session->set_flashdata('message', array('success', "HOD Updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update HOD."));
                }
                redirect(base_url($this->url . "/principal/hod"));
            }
        }
    }

    public function delete_hod($id) {
        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Only Principal and Vice-Principal can delete HODs
        if (!in_array($user_role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL])) {
            $this->session->set_flashdata('message', array('danger', "You do not have permission to delete HODs."));
            redirect(base_url($this->url . "/principal/hod"));
            return;
        }

        $result = $this->db_model->update(TABLE_FACULTY, ["is_active" => 0], ["id" => $id]);
        $message = array('success', "HOD Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/principal/hod"));
    }

    // Staff CRUD Methods
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
                $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
                $user_department = $this->session_data['department'] ?? null;
                $requested_department = $this->input->post('department');

                // Check role-based permissions for staff creation
                if ($user_role === ROLE_HOD) {
                    // HOD can only create staff in their own department
                    if ($requested_department != $user_department) {
                        $this->session->set_flashdata('message', array('danger', "You can only add staff to your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                } elseif ($user_role === ROLE_STAFF) {
                    // Staff cannot create other staff
                    $this->session->set_flashdata('message', array('danger', "You do not have permission to add staff."));
                    return redirect($_SERVER['HTTP_REFERER']);
                }

                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department' => $requested_department,
                    'role' => ROLE_STAFF,
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
                    "is_active" => 1,
                    'password' => password_hash('123456', PASSWORD_DEFAULT) // Default password
                );

                if ($this->db_model->insert(TABLE_FACULTY, $data)) {
                    $this->session->set_flashdata('message', array('success', "Staff Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Staff."));
                }
                redirect(base_url($this->url . "/principal/staff"));
            }
        }
    }

    public function edit_staff($id = null) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
                $user_department = $this->session_data['department'] ?? null;
                $user_id = $this->session_data['id'] ?? null;
                $requested_department = $this->input->post('department');

                // Check role-based permissions for staff editing
                if ($user_role === ROLE_HOD) {
                    // HOD can only edit staff in their own department
                    if ($requested_department != $user_department) {
                        $this->session->set_flashdata('message', array('danger', "You can only edit staff in your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                } elseif ($user_role === ROLE_STAFF) {
                    // Staff can only edit their own profile
                    if ($post['id'] != $user_id) {
                        $this->session->set_flashdata('message', array('danger', "You can only edit your own profile."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department' => $requested_department,
                    'updated_by' => $this->session_data['id']
                );

                if ($this->db_model->update(TABLE_FACULTY, $data, ["id" => $post['id']])) {
                    $this->session->set_flashdata('message', array('success', "Staff Updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Staff."));
                }
                redirect(base_url($this->url . "/principal/staff"));
            }
        }
    }

    public function delete_staff($id) {
        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Only Principal and Vice-Principal can delete staff
        if (!in_array($user_role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL])) {
            $this->session->set_flashdata('message', array('danger', "You do not have permission to delete staff."));
            redirect(base_url($this->url . "/principal/staff"));
            return;
        }

        $result = $this->db_model->update(TABLE_FACULTY, ["is_active" => 0], ["id" => $id]);
        $message = array('success', "Staff Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/principal/staff"));
    }

    // Student CRUD Methods
    public function add_student() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('batch', 'Batch', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
                $user_department = $this->session_data['department'] ?? null;
                $requested_department = $this->input->post('department');

                // Check role-based permissions
                if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
                    // HOD and Staff can only add students to their own department
                    if ($requested_department != $user_department) {
                        $this->session->set_flashdata('message', array('danger', "You can only add students to your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department' => $requested_department,
                    'batch' => $this->input->post('batch'),
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
                    "is_active" => 1,
                    'password' => password_hash('123456', PASSWORD_DEFAULT) // Default password
                );

                if ($this->db_model->insert(TABLE_STUDENT, $data)) {
                    $this->session->set_flashdata('message', array('success', "Student Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Student."));
                }
                redirect(base_url($this->url . "/principal/students"));
            }
        }
    }

    public function edit_student($id = null) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('batch', 'Batch', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
                $user_department = $this->session_data['department'] ?? null;
                $requested_department = $this->input->post('department');

                // Check role-based permissions
                if (in_array($user_role, [ROLE_HOD, ROLE_STAFF])) {
                    // HOD and Staff can only edit students in their own department
                    if ($requested_department != $user_department) {
                        $this->session->set_flashdata('message', array('danger', "You can only edit students in your department."));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phone' => $this->input->post('phone'),
                    'department' => $requested_department,
                    'batch' => $this->input->post('batch'),
                    'updated_by' => $this->session_data['id']
                );

                if ($this->db_model->update(TABLE_STUDENT, $data, ["id" => $post['id']])) {
                    $this->session->set_flashdata('message', array('success', "Student Updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Student."));
                }
                redirect(base_url($this->url . "/principal/students"));
            }
        }
    }

    public function delete_student($id) {
        $user_role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Global rule: Only Principal and Vice-Principal can delete
        if (!in_array($user_role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL])) {
            $this->session->set_flashdata('message', array('danger', "You do not have permission to delete students."));
            redirect(base_url($this->url . "/principal/students"));
            return;
        }

        $result = $this->db_model->update(TABLE_STUDENT, ["is_active" => 0], ["id" => $id]);
        $message = array('success', "Student Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/principal/students"));
    }

    // Batch CRUD Methods
    public function add_batch() {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Batch Name', 'trim|required|min_length[1]|max_length[255]');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'year' => $this->input->post('year'),
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
                    "is_active" => 1
                );
                if ($this->db_model->insert(TABLE_BATCHES, $data)) {
                    $this->session->set_flashdata('message', array('success', "Batch Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create Batch."));
                }
                redirect(base_url($this->url . "/principal/batch_dept"));
            }
        }
    }

    public function edit_batch($id) {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Batch Name', 'trim|required|min_length[1]|max_length[255]');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'year' => $this->input->post('year'),
                    'updated_by' => $this->session_data['id']
                );
                if ($this->db_model->update(TABLE_BATCHES, $data, ["id" => $id])) {
                    $this->session->set_flashdata('message', array('success', "Batch Updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update Batch."));
                }
                redirect(base_url($this->url . "/principal/batch_dept"));
            }
        }
    }

    public function delete_batch($id) {
        $result = $this->db_model->update(TABLE_BATCHES, ["is_active" => 0], ["id" => $id]);
        $message = array('success', "Batch Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/principal/batch_dept"));
    }

}

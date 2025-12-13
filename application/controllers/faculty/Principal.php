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
        $data["add_url"] = base_url($this->url."/principal/add");
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
        $class["classname"] = "students";
        $class["url"] =  $this->url; 
        $class["sidebar_href"] = base_url($this->url."/principal");
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
        $result = $this->db_model->update(TABLE_FACULTY, ["is_active" => 0], ["id" => $id]);
        $message = array('success', "Vice-Principal Deleted Successfully");
        if(!$result){
            $message = array('danger', "Something went wrong");
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url($this->url . "/principal/vice_principal"));
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Principal extends CI_Controller {
    private $url;
    private $college;
    private $session_data;
	function __construct() {
        parent::__construct();
        $this->load->model('faculty/common', 'common');
        $this->load->model('faculty/db_model', 'db_model');
        $this->load->model('faculty/test_model', 'test_model');
        $this->url = $this->uri->segment(1);
        $this->common->check_user_session($this->url);
        $this->college = $this->common->get_college_by_url($this->url);
        $this->session_data = $this->session->userdata($this->url);

        if($this->session_data['designation'] != DESIGNATION_PRINCIPAL){
            $this->common->redirect_route($this->session_data['designation'],$this->url);
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
        $college_id = $this->college['id'];
        $staff_id = $this->session_data['id'];
        $department = $this->session_data['department'];
        
        // Get counts for stat cards
        $data["total_students"] = count($this->db_model->get_all(TABLE_STUDENT, [
            "is_active" => true, 
            "college_id" => $college_id, 
            // "department" => $department
        ]));
        
        $data["total_courses"] = count($this->db_model->get_all(TABLE_COURCES, [
            "is_active" => true, 
            "college_id" => $college_id,
            // "department" => $department,
            // 'created_by' => $staff_id
        ]));
        
        $data["active_tests"] = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            // ->where('start_date <=', date('Y-m-d H:i:s'))
            // ->where('end_date >=', date('Y-m-d H:i:s'))
            // ->where('created_by', $staff_id)
            ->count_all_results(TABLE_TESTS);
            
        $data["total_departments"] = 1; // Only showing own department
        
        // Get question bank statistics
        $data["total_questions"] = count($this->db_model->get_all(TABLE_QUESTION_BANK, [
            "is_active" => true, 
            "college_id" => $college_id,
            // "created_by" => $staff_id
        ]));
        
        // Get questions by type
        $data["code_questions"] = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            // ->where('created_by', $staff_id)
            ->where('type', 2) // Code questions
            ->count_all_results(TABLE_QUESTION_BANK);
        
        $data["mcq_questions"] = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            // ->where('created_by', $staff_id)
            ->where('type', 1) // MCQ questions
            ->count_all_results(TABLE_QUESTION_BANK);
        
        // Calculate question difficulty distribution
        $difficulty_easy = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            // ->where('created_by', $staff_id)
            ->where('difficulty_level', 1) // Easy level
            ->count_all_results(TABLE_QUESTION_BANK);
            
        $difficulty_medium = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            // ->where('created_by', $staff_id)
            ->where('difficulty_level', 2) // Medium level
            ->count_all_results(TABLE_QUESTION_BANK);
            
        $difficulty_hard = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            // ->where('created_by', $staff_id)
            ->where('difficulty_level', 3) // Hard level
            ->count_all_results(TABLE_QUESTION_BANK);
        
        $total = $data["total_questions"] > 0 ? $data["total_questions"] : 1; // Avoid division by zero
        
        $data["easy_questions_percent"] = round(($difficulty_easy / $total) * 100);
        $data["medium_questions_percent"] = round(($difficulty_medium / $total) * 100);
        $data["hard_questions_percent"] = round(($difficulty_hard / $total) * 100);
        
        // Get recent tests
        $data["recent_tests"] = $this->db->select('t.*, m.name as module_name')
            ->from(TABLE_TESTS . ' t')
            ->join(TABLE_TEST_MODULES . ' m', 'm.id = t.module_id', 'left')
            ->where('t.college_id', $college_id)
            // ->where('t.created_by', $staff_id)
            ->where('t.is_active', 1)
            ->order_by('t.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->result();
        
        // Get test performance data (for line chart)
        // Assuming student_test_submission has the test results
        $test_performance = $this->db->select('DATE(sts.submission_time) as date, AVG(sts.percentage) as avg_score')
            ->from(TABLE_STUDENT_TEST_SUBMISSION . ' sts')
            ->join(TABLE_TESTS . ' t', 't.id = sts.test_id')
            ->where('t.college_id', $college_id)
            // ->where('t.created_by', $staff_id)
            ->group_by('DATE(sts.submission_time)')
            ->order_by('DATE(sts.submission_time)', 'ASC')
            ->limit(14) // Last 14 days with data
            ->get()
            ->result_array();

            $question_types = $this->db->select('qt.id, qt.type, COUNT(qb.id) as count')
            ->from(TABLE_QUESTION_TYPES . ' qt')
            ->join(TABLE_QUESTION_BANK . ' qb', 'qb.type = qt.id', 'left')
            ->where('qt.is_active', 1)
            ->where('qb.is_active', 1)
            ->where('qb.college_id', $college_id)
            // ->where_in('qb.created_by', $this->permissions['read'])
            ->group_by('qt.id, qt.type')
            ->get()
            ->result();

        // Get question subtypes and their counts
        $question_subtypes = $this->db->select('qst.id, qst.sub_type, qst.type_id, COUNT(qb.id) as count')
            ->from(TABLE_QUESTION_SUB_TYPES . ' qst')
            ->join(TABLE_QUESTION_BANK . ' qb', 'qb.sub_type = qst.id', 'left')
            ->where('qst.is_active', 1)
            ->where('qb.is_active', 1)
            ->where('qb.college_id', $college_id)
            // ->where_in('qb.created_by', $this->permissions['read'])
            ->group_by('qst.id, qst.sub_type, qst.type_id')
            ->get()
            ->result();

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
        $courses = $this->db->select('c.id, c.name')
            ->from(TABLE_COURCES . ' c')
            ->where('c.college_id', $college_id)
            ->where('c.department', $department)
            ->where('c.created_by', $staff_id)
            ->where('c.is_active', 1)
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
        
        // Calculate test completion statistics
        $total_test_attempts = $this->db->count_all_results(TABLE_STUDENT_TEST_SUBMISSION);
        $completed_tests = $this->db->where('finished', 1)
            ->count_all_results(TABLE_STUDENT_TEST_SUBMISSION);
            
        $data["test_completion_rate"] = $total_test_attempts > 0 ? round(($completed_tests / $total_test_attempts) * 100) : 0;
        
        // Calculate average test score
        $avg_score = $this->db->select_avg('percentage')
            ->from(TABLE_STUDENT_TEST_SUBMISSION)
            ->join(TABLE_TESTS . ' t', 't.id = ' . TABLE_STUDENT_TEST_SUBMISSION . '.test_id')
            ->where('t.created_by', $staff_id)
            ->get()
            ->row();
            
        $data["avg_test_score"] = $avg_score ? round($avg_score->percentage) : 0;
        
        // Calculate pass rate (assuming 40% is passing)
        $passed_tests = $this->db->where('percentage >=', 40)
            ->join(TABLE_TESTS . ' t', 't.id = ' . TABLE_STUDENT_TEST_SUBMISSION . '.test_id')
            ->where('t.created_by', $staff_id)
            ->count_all_results(TABLE_STUDENT_TEST_SUBMISSION);
            
        $data["test_pass_rate"] = $total_test_attempts > 0 ? round(($passed_tests / $total_test_attempts) * 100) : 0;
        
        // Get test module statistics
        $data["test_module_stats"] = $this->db->select('m.name as module_name, AVG(sts.percentage) as avg_score, COUNT(sts.id) as attempts')
            ->from(TABLE_STUDENT_TEST_SUBMISSION . ' sts')
            ->join(TABLE_TESTS . ' t', 't.id = sts.test_id')
            ->join(TABLE_TEST_MODULES . ' m', 'm.id = t.module_id', 'left')
            ->where('t.college_id', $college_id)
            ->where('t.created_by', $staff_id)
            ->group_by('m.id, m.name')
            ->order_by('attempts', 'DESC')
            ->limit(5)
            ->get()
            ->result();
            
        // Get groups statistics
        $data["total_groups"] = $this->db->where('college_id', $college_id)
            ->where('created_by', $staff_id)
            ->where('is_active', 1)
            ->count_all_results(TABLE_GROUPS);

        // Get all distinct batch years from students table
        $batch_years = $this->db->select('batch')
            ->distinct()
            ->from(TABLE_STUDENT)
            ->where('college_id', $college_id)
            ->where('is_active', 1)
            ->order_by('batch', 'DESC') // Show most recent batches first
            ->get()
            ->result_array();

        // Extract just the years from the results
        $years = array_column($batch_years, 'batch');
        // If you want to limit to specific years (e.g., last 5 years)
        // $years = array_slice($years, 0, 5);

        // Get student counts by department and batch year
        $departments = $this->db->select('id, name')
            ->from(TABLE_DEPARTMENT)
            ->where('college_id', $college_id)
            ->where('is_active', 1)
            ->get()
            ->result_array();

        $department_batch_counts = [];

        foreach ($departments as $dept) {
            $dept_data = [
                'name' => $dept['name'],
                'batches' => [],
                'total' => 0
            ];
            
            foreach ($years as $year) {
                $count = $this->db->where('college_id', $college_id)
                    ->where('department', $dept['id'])
                    ->where('batch', $year)
                    ->where('is_active', 1)
                    ->count_all_results(TABLE_STUDENT);
                    
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
        
        $this->load->view('faculty/sidebar', $class);
        $this->load->view('faculty/dashboard', $data);
        $this->load->view('faculty/footer');
    }

    public function view(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password");
        $class["classname"] = "principal";
        $class["url"] =  $this->url; 
        $class["sidebar_href"] = base_url($this->url."/principal");
        $data["principal"] = $this->db_model->get_all(TABLE_STAFF,["id !="=>$this->session_data['id'],"is_active"=>true,"college_id"=>$this->college['id'],"designation"=>DESIGNATION_PRINCIPAL]);

		$this->load->view('faculty/sidebar',$class); 
		$this->load->view('faculty/principal',$data); 
		$this->load->view('faculty/footer'); 
    }

    public function hod(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password");
        $class["classname"] = "hod";
        $class["url"] =  $this->url; 
        $class["sidebar_href"] = base_url($this->url."/principal");
        $data["hod"] = $this->db_model->get_all(TABLE_STAFF,["is_active"=>true,"college_id"=>$this->college['id'],"designation"=>DESIGNATION_HOD]);

        foreach ($data["hod"] as $key => $value) {
            $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $value['department'], 'college_id' => $this->college['id']]);
            $data["hod"][$key]['department'] = $dept ? $dept['name'] : 'unknown';
        }
		$this->load->view('faculty/sidebar',$class); 
		$this->load->view('faculty/hod',$data); 
		$this->load->view('faculty/footer'); 
    }

    public function staff(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/principal/reset_password");
        $class["classname"] = "staff";
        $class["url"] =  $this->url; 
        $class["sidebar_href"] = base_url($this->url."/principal");
        $data["staff"] = $this->db_model->get_all(TABLE_STAFF,["is_active"=>true,"college_id"=>$this->college['id'],"designation"=>DESIGNATION_STAFF]);

        foreach ($data["staff"] as $key => $value) {
            $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $value['department'], 'college_id' => $this->college['id']]);
            $data["staff"][$key]['department'] = $dept ? $dept['name'] : 'unknown';
        }

		$this->load->view('faculty/sidebar',$class); 
		$this->load->view('faculty/staff',$data); 
		$this->load->view('faculty/footer'); 
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
        $this->load->view('faculty/sidebar', $class);
        $this->load->view('groups/view', $data);
        $this->load->view('faculty/footer');
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
		$this->load->view('faculty/sidebar',$class); 
		$this->load->view('faculty/students',$data); 
		$this->load->view('faculty/footer'); 
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
           $update= $this->db_model->update(TABLE_STAFF,["password"=>$post['password']],["is_active"=>1,"college_id"=>$this->college['id'],"id"=>$post['id']]);
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
            $image_url = $this->common->upload_to_cloudinary('image', 'college');

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
        $this->load->view('faculty/sidebar', $class); 
        $this->load->view('faculty/profile', $data); 
        $this->load->view('faculty/footer');
        }
    }

}

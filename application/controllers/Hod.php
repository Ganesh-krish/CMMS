<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hod extends CI_Controller {
    private $url;
    private $college;
    private $session_data;
	function __construct() {
        parent::__construct();
        $this->load->model('common', 'faculty_common');
        $this->load->model('Db_model', 'db_model');
        $this->load->model('Test_model', 'test_model');
        $this->url = $this->uri->segment(1);

        // Use unified session approach
        $unified_user = $this->session->userdata('user');
        if ($unified_user && isset($unified_user['user_type']) && $unified_user['user_type'] === 'faculty') {
            // Unified session access
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $unified_user;
        } else {
            // Fallback for legacy access
        $this->faculty_common->check_user_session($this->url);
        $this->college = $this->faculty_common->get_default_college();
        $this->session_data = $this->session->userdata($this->url);
        }

        $this->permissions = $this->faculty_common->get_access_permissions($this->session_data);
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Allow HOD and higher roles to access department administration
        $allowed_roles = [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD];
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
        $class["sidebar_href"] = base_url($this->url . "/hod");
        
        // Get current college and staff/department info
        $college_id = $this->college['id'];
        $staff_id = $this->session_data['id'];
        $department = $this->session_data['department'];
        
        // Get counts for stat cards
        $data["total_students"] = count($this->db_model->get_all(TABLE_STUDENT, [
            "is_active" => true, 
            "college_id" => $college_id, 
            "department" => $department
        ]));
        
        $data["total_courses"] = count($this->db_model->get_all(TABLE_COURCES, [
            "is_active" => true, 
            "college_id" => $college_id,
            "created_by" => $this->permissions['read']
        ]));
        
        $data["active_tests"] = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            ->where_in('created_by', $this->permissions['read'])
            ->count_all_results(TABLE_TESTS);
            
        // Get question bank statistics
        $data["total_questions"] = count($this->db_model->get_all(TABLE_QUESTION_BANK, [
            "is_active" => true, 
            "college_id" => $college_id,
            "department" => $department,
            "created_by" => $this->permissions['read']
        ]));
        
        // Get questions by type
        $data["code_questions"] = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            ->where('type', 2) // C
            ->where_in('created_by', $this->permissions['read'])
            ->count_all_results(TABLE_QUESTION_BANK);
        
        $data["mcq_questions"] = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            ->where('type', 1) // MCQ questions
            ->where_in('created_by', $this->permissions['read'])
            ->count_all_results(TABLE_QUESTION_BANK);
        
        // Calculate question difficulty distribution
        $difficulty_easy = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            ->where('difficulty_level', 1) // Easy level
            ->where_in('created_by', $this->permissions['read'])
            ->count_all_results(TABLE_QUESTION_BANK);
            
        $difficulty_medium = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            ->where('difficulty_level', 2) // Medium level
            ->where_in('created_by', $this->permissions['read'])
            ->count_all_results(TABLE_QUESTION_BANK);
            
        $difficulty_hard = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            ->where('difficulty_level', 3) // Hard level
            ->where_in('created_by', $this->permissions['read'])
            ->count_all_results(TABLE_QUESTION_BANK);
        
        $total = $data["total_questions"] > 0 ? $data["total_questions"] : 1; // Avoid division by zero
        
        $data["easy_questions_percent"] = round(($difficulty_easy / $total) * 100);
        $data["medium_questions_percent"] = round(($difficulty_medium / $total) * 100);
        $data["hard_questions_percent"] = round(($difficulty_hard / $total) * 100);
        
        // Get question types and their counts
        $question_types = $this->db->select('qt.id, qt.type, COUNT(qb.id) as count')
            ->from(TABLE_QUESTION_TYPES . ' qt')
            ->join(TABLE_QUESTION_BANK . ' qb', 'qb.type = qt.id AND qb.is_active = 1 AND qb.college_id = ' . $college_id . ' AND qb.created_by IN (' . implode(',', $this->permissions['read']) . ')')
            ->where('qt.is_active', 1)
            ->group_by('qt.id, qt.type')
            ->get()
            ->result();

        // Get question subtypes and their counts
        $question_subtypes = $this->db->select('qst.id, qst.sub_type, qst.type_id, COUNT(qb.id) as count')
            ->from(TABLE_QUESTION_SUB_TYPES . ' qst')
            ->join(TABLE_QUESTION_BANK . ' qb', 'qb.sub_type = qst.id AND qb.is_active = 1 AND qb.college_id = ' . $college_id . ' AND qb.created_by IN (' . implode(',', $this->permissions['read']) . ')')
            ->where('qst.is_active', 1)
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

        // If no question types found, show a default "No Questions" segment
        if (empty($question_types)) {
            $data['question_types'] = ['No Questions'];
            $data['question_type_counts'] = [100];
            $data['question_type_colors'] = ['rgba(199, 199, 199, 0.8)']; // Grey color
        } else {
            foreach ($question_types as $type) {
                $data['question_types'][] = $type->type;
                $data['question_type_counts'][] = $type->count;
            }
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

        // If no question subtypes found, show a default "No Questions" segment
        if (empty($question_subtypes)) {
            $data['question_subtypes'] = ['No Questions'];
            $data['question_subtype_counts'] = [100];
            $data['question_subtype_colors'] = ['rgba(199, 199, 199, 0.8)']; // Grey color
        } else {
            foreach ($question_subtypes as $subtype) {
                $data['question_subtypes'][] = $subtype->sub_type;
                $data['question_subtype_counts'][] = $subtype->count;
            }
        }

        // Get recent tests
        $data["recent_tests"] = $this->db->select('t.*, m.name as module_name')
            ->from(TABLE_TESTS . ' t')
            ->join(TABLE_TEST_MODULES . ' m', 'm.id = t.module_id', 'left')
            ->where('t.college_id', $college_id)
            ->where('t.is_active', 1)
            ->order_by('t.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->result();
        
        // Get test performance data (for line chart)
        // Default sample data for tests performance over time
        $test_performance = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $score = rand(60, 95); // Random score between 60-95 for demo
            $test_performance[] = [
                'date' => $date,
                'avg_score' => $score
            ];
        }
            
        $data["test_performance_dates"] = array_column($test_performance, 'date');
        $data["test_performance_scores"] = array_column($test_performance, 'avg_score');
        
        
        // If no courses found, provide dummy data
        if (empty($data["course_names"])) {
            $data["course_names"] = ['Course A', 'Course B', 'Course C', 'Course D'];
            $data["course_enrollments"] = [12, 19, 8, 15];
        }
        
        // Default test completion statistics 
        $data["test_completion_rate"] = 78; // Default value
        $data["avg_test_score"] = 82;       // Default value
        $data["test_pass_rate"] = 85;       // Default value
        


         // Get all distinct batch years from students table
         $batch_years = $this->db->select('batch')
         ->distinct()
         ->from(TABLE_STUDENT)
         ->where('college_id', $college_id)
         ->where('is_active', 1)
         ->where_in('department',$this->permissions['department'])
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
                ->where_in('id',$this->permissions['department'])
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


        $data["manage_student_url"] = base_url($this->url."/hod/students");
        
        $this->load->view('common/sidebar', $class);
        $this->load->view('common/dashboard', $data);
        $this->load->view('common/footer');
    }

    public function hod(){
        $data["url"] = $this->url;
        $data["post_url"] = base_url($this->url."/hod/reset_password");
        $data["add_url"] = base_url($this->url."/hod/add");
        $class["classname"] = "hod";
        $class["url"] =  $this->url; 
        $class["sidebar_href"] = base_url($this->url."/hod");
        $data["hod"] = $this->db_model->get_all(
            TABLE_FACULTY,
            [
                "is_active"=>true,
                "role"=>ROLE_ADMIN
            ]
        );
		$this->load->view('common/sidebar',$class); 
		$this->load->view('faculty/faculty/hod',$data); 
		$this->load->view('common/footer'); 
    }

    // Alias for /hod/view
    public function view() {
        return $this->hod();
    }

    public function staff(){
        $data["url"] = $this->url;
        $class["classname"] = "staff";
        $class["url"] =  $this->url; 
        $class["sidebar_href"] = base_url($this->url."/hod");
        $data["post_url"] = base_url($this->url."/hod/reset_password");
        $data["add_url"] = base_url($this->url."/hod/add_staff");
        $departments = explode(";", $this->session_data['other_department']);
        array_push($departments, $this->session_data['department']);
        $data["staff"] = $this->db_model->get_all(TABLE_FACULTY,["is_active"=>true,"role"=>ROLE_STAFF,"department"=>$departments]);
        foreach ($data["staff"] as $key => $value) {
            if (in_array($value['department'], $departments)) {
                $data["staff"][$key]['department'] = $this->db_model->get_row(TABLE_DEPARTMENT,["id"=>$value['department']])['name'];
            }
        }
		$this->load->view('common/sidebar',$class); 
		$this->load->view('faculty/faculty/staff',$data); 
		$this->load->view('common/footer'); 
    }

    public function students(){
        $data["url"] = $this->url;
        $class["classname"] = "students";
        $class["url"] =  $this->url; 
        $class["sidebar_href"] = base_url($this->url."/hod");
        $data["post_url"] = base_url($this->url."/hod/reset_password_student"); 
        $departments = explode(",", $this->session_data['other_department']);
        array_push($departments, $this->session_data['department']);

        // filter empty values
        $departments = array_filter($departments);

        // remove duplicate values
        $departments = array_unique($departments);


        $department = $this->input->get('department');
        $batch = $this->input->get('batch');

        $conditions = ["is_active"=>true,"college_id"=>$this->college['id']];

        if($department != null){
            $conditions['department'] = $department;
        }else{
            $conditions['department'] = $departments;
        }

        if($batch != null){
            $conditions['batch'] = $batch;
        }


        $group_conditions = ["is_active"=>1,"college_id"=>$this->college['id']];

        if($this->permissions['read']){
            $group_conditions['created_by'] = $this->permissions['read'];
        }else{
            $group_conditions['created_by'] = $this->permissions['read'];
        }

        // var_dump($group_conditions);die;

        $data['groups'] = $this->db_model->get_all(TABLE_GROUPS,$group_conditions);
        $data["memgroups"] = $this->db_model->get_groupMembers($this->college['id'],$this->permissions['read']);
        $data["staff"] = $this->db_model->get_all(TABLE_STUDENT,$conditions);
        
        foreach ($data["staff"] as $key => $value) {
            $data["staff"][$key]['department'] = $this->db_model->get_row(TABLE_DEPARTMENT,["id"=>$value['department']])['name'];
        }

        $data['batches'] = $this->db_model->get_all(TABLE_STUDENT,["is_active"=>true,"college_id"=>$this->college['id']]);
        $data['batches'] = array_unique(array_column($data['batches'], 'batch'));

        $data['departments'] = $this->db_model->get_all(TABLE_DEPARTMENT,["id"=>$departments,"is_active"=>true,"college_id"=>$this->college['id']]);


		$this->load->view('common/sidebar',$class);
		$this->load->view('faculty/faculty/students',$data);
		$this->load->view('common/footer'); 
    }


    public function groups()
    {
        $data["url"] = $this->url;
        $class["classname"] = "groups";
        $class["url"] =  $this->url;
        $class["college_id"] = $this->college['id'];
        $class["sidebar_href"] = base_url($this->url . "/staff");
        $group_conditions = ["is_active"=>1,"college_id"=>$this->college['id']];

        if(is_array($this->permissions['read'])){
            $group_conditions['created_by'] = $this->permissions['read'];
        }else{
            $group_conditions['created_by'] = $this->permissions['read'];
        }
        $data["groups"] = $this->db_model->get_all(TABLE_GROUPS,$group_conditions);
        // var_dump($data["groups"]);die;
        $this->load->view('common/sidebar', $class);
        $this->load->view('groups/view', $data);
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
            $departments = explode(";", $this->session_data['other_department']);
            array_push($departments, $this->session_data['department']);
           $update= $this->db_model->update(TABLE_FACULTY,["password"=>$post['password']],["role !="=>ROLE_ADMIN,"is_active"=>1,"id"=>$post['id'],"department"=>$departments]);
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
            $departments = explode(";", $this->session_data['other_department']);
            array_push($departments, $this->session_data['department']); 
           $update= $this->db_model->update(TABLE_STUDENT,["password"=>$post['password']],["is_active"=>1,"college_id"=>$this->college['id'],"id"=>$post['id'],"department"=>$departments]);
           if(!$update){
                $this->session->set_flashdata('message',array("danger","Something Went Wrong")); 
                return redirect($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:base_url($this->url."/principal"));
           }
           $this->session->set_flashdata('message',array("success","Password Reseted successfully")); 
           return redirect($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:base_url($this->url."/principal"));
        }
    }
 
}

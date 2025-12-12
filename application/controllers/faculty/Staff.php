<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Staff extends CI_Controller
{
    private $url;
    private $college;
    private $session_data;
    function __construct()
    {
        parent::__construct();
        $this->load->model('faculty/common', 'common');
        $this->load->model('faculty/db_model', 'db_model');
        $this->load->model('faculty/test_model', 'test_model');
        $this->url = $this->uri->segment(1);
        $this->common->check_user_session($this->url);
        $this->college = $this->common->get_default_college();
        $this->session_data = $this->session->userdata($this->url);
        $this->permissions = $this->common->get_access_permissions($this->session_data);

        // var_dump($this->permissions);die;

        if ($this->session_data['designation'] != DESIGNATION_STAFF) {
            $this->common->redirect_route($this->session_data['designation'], $this->url);
        }
    }
    public function index()
    {
        // Basic data for the view
        $data["url"] = $this->url;
        $class["classname"] = "home";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url . "/staff");
        
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
            "department" => $department,
            'created_by' => $staff_id
        ]));
        
        $data["active_tests"] = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            // ->where('start_date <=', date('Y-m-d H:i:s'))
            // ->where('end_date >=', date('Y-m-d H:i:s'))
            ->where('created_by', $staff_id)
            ->count_all_results(TABLE_TESTS);
            
        $data["total_departments"] = 1; // Only showing own department
        
        // Get question bank statistics
        $data["total_questions"] = count($this->db_model->get_all(TABLE_QUESTION_BANK, [
            "is_active" => true, 
            "college_id" => $college_id,
            "created_by" => $staff_id
        ]));
        
        // Get questions by type
        $data["code_questions"] = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            ->where('created_by', $staff_id)
            ->where('type', 2) // Code questions
            ->count_all_results(TABLE_QUESTION_BANK);
        
        $data["mcq_questions"] = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            ->where('created_by', $staff_id)
            ->where('type', 1) // MCQ questions
            ->count_all_results(TABLE_QUESTION_BANK);
        
        // Calculate question difficulty distribution
        $difficulty_easy = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            ->where('created_by', $staff_id)
            ->where('difficulty_level', 1) // Easy level
            ->count_all_results(TABLE_QUESTION_BANK);
            
        $difficulty_medium = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            ->where('created_by', $staff_id)
            ->where('difficulty_level', 2) // Medium level
            ->count_all_results(TABLE_QUESTION_BANK);
            
        $difficulty_hard = $this->db->where('college_id', $college_id)
            ->where('is_active', 1)
            ->where('created_by', $staff_id)
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
            ->where('t.created_by', $staff_id)
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
            ->where('t.created_by', $staff_id)
            ->group_by('DATE(sts.submission_time)')
            ->order_by('DATE(sts.submission_time)', 'ASC')
            ->limit(14) // Last 14 days with data
            ->get()
            ->result_array();
            
        // If no performance data, create sample data
        if (empty($test_performance)) {
            $test_performance = [];
            for ($i = 13; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $test_performance[] = [
                    'date' => $date,
                    'avg_score' => 0 // No data
                ];
            }
        }
        
        $data["test_performance_dates"] = array_column($test_performance, 'date');
        $data["test_performance_scores"] = array_column($test_performance, 'avg_score');
        
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


        $data["manage_student_url"] = base_url($this->url."/staff/students");


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


        
        $this->load->view('faculty/sidebar', $class);
        $this->load->view('faculty/dashboard', $data);
        $this->load->view('faculty/footer');
    }
    public function staff()
    {
        $data["url"] = $this->url;
        $class["classname"] = "staff";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url . "/staff");
        $data["post_url"] = base_url($this->url . "/staff/reset_password");
        $data["staff"] = $this->db_model->get_all(TABLE_STAFF, ["is_active" => true, "college_id" => $this->college['id'], "designation" => DESIGNATION_STAFF, "department" => $this->session_data['department']]);

        $this->load->view('faculty/sidebar', $class);
        $this->load->view('faculty/staff', $data);
        $this->load->view('faculty/footer');
    }

    // Cource module functionality
    public function cources()
    {
        $data["url"] = $this->url;
        $class["classname"] = "courses";
        $class["url"] =  $this->url;
        $class["college_id"] = $this->college['id'];
        $class["sidebar_href"] = base_url($this->url . "/staff");
        $data["cources"] = $this->db_model->get_all(TABLE_COURCES, ["is_active" => true, "college_id" => $this->college['id'],"department" => $this->session_data['department'],'created_by' => $this->session_data['id']]);
        // var_dump($data["cources"]);die;
        $data["faculty"] = $this->db_model->get_row(TABLE_COURCES, ["is_active" => true, "college_id" => $this->college['id'], "department" => $this->session_data['department'],'created_by' => $this->session_data['id']]);
        $this->load->view('faculty/sidebar', $class);
        $this->load->view('faculty/cources', $data);
        $this->load->view('faculty/footer');
    }

    
    public function students()
    {

        $data["url"] = $this->url;
        $class["classname"] = "students";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url . "/staff");
        $data["post_url"] = base_url($this->url . "/staff/reset_password_student");

        $batch = $this->input->get('batch');

        $conditions = ["is_active" => true, "college_id" => $this->college['id'], "department" => $this->session_data['department']];


        if($batch != null){
            $conditions['batch'] = $batch;
        }


        $data["staff"] = $this->db_model->get_all(TABLE_STUDENT, $conditions);

        $data["groups"] = $this->db_model->get_all(TABLE_GROUPS, [
            "is_active" => 1,
            "college_id" => $this->college['id'],
            "created_by" => array_map('intval', $this->permissions['read'])
        ]);
        $data["memgroups"] = $this->db_model->get_groupMembers($this->college['id'],$this->permissions['read']);
        $departments = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => true, "college_id" => $this->college['id']]);
        foreach ($data["staff"] as $key => $value) {
                $data["staff"][$key]['department'] = $this->db_model->get_row(TABLE_DEPARTMENT,["id"=>$value['department']])['name'];
        }

        $data['batches'] = $this->db_model->get_all(TABLE_STUDENT,["is_active"=>true,"college_id"=>$this->college['id'],"department"=>$this->session_data['department']]);
        $data['batches'] = array_unique(array_column($data['batches'], 'batch'));

        $this->load->view('faculty/sidebar', $class);
        $this->load->view('faculty/students', $data);
        $this->load->view('faculty/footer');
    }

    public function addMemberstoGroup() {

        $post = $this->input->post();



        if ($post) {
            $this->form_validation->set_rules('group_id', 'Group ID', 'required|numeric');
            if ($this->form_validation->run() == FALSE) {
                $response = [
                    'status' => 'error',
                    'message' => strip_tags(validation_errors())
                ];

                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }

            $student_ids = $this->input->post('student_ids'); // Get selected text
            $group_id = $this->input->post('group_id'); // Get all options


            if (empty($student_ids) || !is_array($student_ids)) {
                $response = [
                    'status' => 'error',
                    'message' => "No students selected or invalid format."
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }

            foreach ($student_ids as $student_id) {
                $insertData = [
                    'group_id'   => $group_id,
                    'student_id' => $student_id,
                    'college_id'     => $this->college['id'],
                    'created_by'   => $this->session_data['id'],
                ];

               $exists =  $this->db_model->get_row(TABLE_MEMGROUPS, ['group_id' => $group_id, 'student_id' => $student_id]);

               if(!$exists){
                $insert = $this->db_model->insert(TABLE_MEMGROUPS, $insertData);
               }

            }

            $response = [
                'status' => 'success',
                'message' => "Members Added successfully!"
            ];

           
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => 'error',
                'message' => "Invalid request. No data received."
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }


    public function reset_password_student()
    {
        $post = $this->input->post();
        if ($post) {
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('id', 'Id', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : base_url($this->url . "/principal"));
            }
            $update = $this->db_model->update(TABLE_STUDENT, ["password" => $post['password']], ["is_active" => 1, "college_id" => $this->college['id'], "id" => $post['id'], "department" => $this->session_data['department']]);
            if (!$update) {
                $this->session->set_flashdata('message', array("danger", "Something Went Wrong"));
                return redirect($_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : base_url($this->url . "/principal"));
            }
            $this->session->set_flashdata('message', array("success", "Password Reseted successfully"));
            return redirect($_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : base_url($this->url . "/principal"));
        }
    }
}

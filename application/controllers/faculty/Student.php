<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Student extends CI_Controller {
    private $url;

    public function __construct() {

        $this->config =& get_config();

        header( 'Access-Control-Allow-Origin:'.$this->config['student_url'] );
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH' );
        header( 'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization' );
        header( 'Access-Control-Max-Age: 3600' );


        if ( $_SERVER[ 'REQUEST_METHOD' ] === 'OPTIONS' ) {
            http_response_code( 200 );
            exit();
        }

        parent::__construct();

        $this->url = $this->uri->segment( 1 );

        $this->load->database();
        $this->load->helper( 'form' );
        $this->load->model( 'faculty/common', 'common' );
        $this->load->model( 'faculty/db_model', 'db_model' );
        $this->load->model( 'faculty/test_model', 'test_model' );
        $this->load->model( 'Lesson_model', 'lesson_model' );
        $this->load->library( 'session' );

        
        $this->output->set_content_type( 'application/json' );
    }



    private function create_external_user($student) {
    
        $curl = curl_init();
        

        
        $postData = json_encode(array(
            'name' => $student->name,
            'email' => $student->email,
        ));
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://onecompiler.com/api/v1/createUser?access_token=' . ONE_COMPILER_API_KEY,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            
            log_message('error', 'OneCompiler API Error: ' . $err);
            return false;
        }
        
        return json_decode($response, true);
    }

    public function login() {
        $content_type = $this->input->server( 'CONTENT_TYPE' );
    
        if ( strpos( $content_type, 'application/json' ) !== false ) {
            $json_input = json_decode( file_get_contents( 'php://input' ), true );
            $email = isset( $json_input[ 'email' ] ) ? $json_input[ 'email' ] : null;
            $password = isset( $json_input[ 'password' ] ) ? $json_input[ 'password' ] : null;
        } else {
            $email = $this->input->post( 'email' );
            $password = $this->input->post( 'password' );
            
        }
    
        if ( empty( $email ) || empty( $password ) ) {
            $response = array(
                'status' => 'error',
                'message' => 'Email and password are required',
                'data' => null
            );
            echo json_encode( $response );
            return;
        }
    
        
        $this->db->select( 'id, name, email, password, college_id, external_id, user_token,expire_date' );
        $this->db->from( 'students' );
        $this->db->where( 'email', $email );
        $query = $this->db->get();
        $student = $query->row();
    
        $college = $this->db_model->get_row(TABLE_COLLEGE, ["id" => SINGLE_COLLEGE_ID, "is_active" => 1]);
        
        $password_valid = false;
        if ($student && $college) {
            if (!empty($student->password) && strlen($student->password) > 30 && password_verify($password, $student->password)) {
                $password_valid = true;
            } elseif ($student->password === $password) {
                $password_valid = true;
                // upgrade to bcrypt
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $this->db->where('id', $student->id)->update('students', ['password' => $hash]);
                $student->password = $hash;
            }
        }

        if ( $student && $college && $password_valid ) {
        
            if (!isset($student->college_id) || empty($student->college_id)) {
                $student->college_id = $college['id'];
            }


            if ($student->expire_date < date('Y-m-d')) {
                http_response_code( 401 );
                $response = array(
                    'status' => 'error',
                    'message' => 'Your account has expired. Please contact support.',
                    'data' => null
                );

                if (!isset($student->external_id) || empty($student->external_id) || 
                !isset($student->user_token) || empty($student->user_token)) { 

                    $update_data = array(
                        'external_id' => null,
                        'user_token' => null,
                    );
                    
                    $this->db->where('id', $student->id);
                    $this->db->update('students', $update_data);

                }
                echo json_encode( $response );
                return;
            } 
            
            
            
            $response = array(
                'status' => 'success',
                'message' => 'Login successful',
                'data' => array(
                    'user' => array(
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'college_id' => $student->college_id
                    )
                )
            );
            $this->session->set_userdata($this->url . '_student', $student);
        } else {
            http_response_code( 401 );
            $response = array(
                'status' => 'error',
                'message' => 'Invalid email or password',
                'data' => null
            );
        }
        echo json_encode( $response );
        return;
    }

    public function check_auth() {
        $student = $this->session->userdata( $this->url . '_student' );

        if ( $student ) {
            $this->db->select( 'id,expire_date' );
            $this->db->from( 'students' );
            $this->db->where( 'id',  $student->id);
            $query = $this->db->get();
            $student_data = $query->row();

            if ($student_data->expire_date < date('Y-m-d')) {
                http_response_code( 401 );
                $response = array(
                    'status' => 'error',
                    'message' => 'Your account has expired. Please contact support.',
                    'data' => null
                );
                if (!isset($student_data->external_id) || empty($student_data->external_id) || 
                !isset($student_data->user_token) || empty($student_data->user_token)) { 
                    // $update_data = array(
                    //     'external_id' => null,
                    //     'user_token' => null,
                    // );
                    // $this->db->where('id', $student_data->id);
                    // $this->db->update('students', $update_data);
                }
                echo json_encode( $response );
                return;
            } 
            $response = array(
                'status' => 'success',
                'message' => 'User is authenticated',
                'data' => array(
                    'user' => array(
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'url' => $this->url,
                        'college_id' => isset($student->college_id) ? $student->college_id : null
                    )
                )
            );
        } else {
            http_response_code( 401 );
            $response = array(
                'status' => 'error',
                'message' => 'User not authenticated',
                'data' => null
            );
        }
        echo json_encode( $response );
        return;
    }
    
    public function auth_logout() {
        $this->session->unset_userdata($this->url );
        $this->session->sess_destroy();
        $response = array(
            'status' => 'success',
            'message' => 'Logout successful',
            'data' => null
        );
        echo json_encode( $response );
        return;
    }


    private function get_student_course_ids($student) {
        
        $course_ids = [];
        if (!isset($student->id) || empty($student->id)) {
            return $course_ids;
        }
        $student_data = $this->db_model->get_row("students", ["id" => $student->id]);
        if ($student_data && isset($student_data['department'])) {
            $course_departments = $this->db_model->get_all(
                "course_departments", 
                [
                    "department_id" => $student_data['department'],
                    "college_id" => $student->college_id
                ]
            );
            if ($course_departments) {
                $dept_course_ids = array_column($course_departments, 'course_id');
                $course_ids = array_merge($course_ids, $dept_course_ids);
            }
        }
        if ($student_data && isset($student_data['id'])) {
            $student_groups = $this->db_model->get_all(
                "group_members", 
                [
                    "student_id" => $student_data['id'],
                    "college_id" => $student->college_id
                ]
            );
            if ($student_groups && count($student_groups) > 0) {
                $group_ids = array_column($student_groups, 'group_id');
                if (!empty($group_ids)) {
                    $course_groups = $this->db_model->get_all(
                        "course_groups", 
                        [
                            "group_id" => $group_ids,
                            // "college_id" => $student->college_id
                        ]
                    );
                    if (is_array($course_groups) && !empty($course_groups)) {
                        $group_course_ids = array_column($course_groups, 'course_id');
                        $course_ids = array_merge($course_ids, $group_course_ids);
                    }
                }
            }
            $student_courses = $this->db_model->get_all(
                "course_students", 
                [
                    "student_id" => $student_data['id'],
                ]
            );
            if (is_array($student_courses) && !empty($student_courses)) {
                $direct_course_ids = array_column($student_courses, 'course_id');
                $course_ids = array_merge($course_ids, $direct_course_ids);
            }
        } else {
            $student_courses = $this->db_model->get_all(
                "course_students", 
                [
                    "student_id" => $student->id
                ]
            );
            if (is_array($student_courses) && !empty($student_courses)) {
                $direct_course_ids = array_column($student_courses, 'course_id');
                $course_ids = array_merge($course_ids, $direct_course_ids);
            }
        }
        // ✅ Add shared courses (shared with this student's college)
        $shared_courses = $this->db_model->get_all(
            "special_courses", 
            [
                "to_college_id" => $student->college_id,
                "is_active" => 1
            ]
        );
        if (is_array($shared_courses) && !empty($shared_courses)) {
            $shared_course_ids = array_column($shared_courses, 'course_id');
            $course_ids = array_merge($course_ids, $shared_course_ids);
        }
        $active_courses = $this->db_model->get_all(
            "courses",
            [
                "id" => $course_ids,
                "is_active" => 1
            ]
        );
        if (is_array($active_courses) && !empty($active_courses)) {
            $course_ids = array_column($active_courses, 'id');
        }
        
        return array_unique($course_ids);
    }


    public function courses() {
        $student = $this->session->userdata($this->url . '_student');
        if (!$student) {
            http_response_code(401);
            $response = array(
                'status' => 'error',
                'message' => 'User not authenticated',
                'data' => null
            );
            echo json_encode($response);
            return;
        }
        if (!isset($student->college_id) || empty($student->college_id)) {
            $student->college_id = SINGLE_COLLEGE_ID;
        }
        $course_ids = $this->get_student_course_ids($student);
        if (empty($course_ids)) {
            $response = array(
                'status' => 'success',
                'message' => 'No courses found',
                'data' => []
            );
            echo json_encode($response);
            return;
        }
        
        
        $course_types = $this->common->get_student_course_types($student);
        if (empty($course_types)) {
        $response = array([
        'status' => 'success',
        'message' => 'No course types found',
        'data' => []
        ]);
        echo json_encode($response);
        return;
        }
        $valid_type_ids = array_column($course_types, 'id');
        // print_r($valid_type_ids);
        // exit;

        $selected_type = $this->input->get('course_type');
        $selected_type = is_numeric($selected_type) ? (int) $selected_type : null;
         if ($selected_type !== null && !in_array($selected_type, $valid_type_ids, true)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'No matching course type found',
            'data' => []
        ]);
        return;
    }

    // Fetch all courses
    // $this->db->select('id, name, course_code, description, end_date as course_expiry, start_date, end_date, course_type,course_mode')
    //     ->from('courses')
    //     ->where('college_id', $student->college_id)
    //     ->where('is_active', 1)
    //     ->where_in('id', $course_ids);
    
    $this->db->select('c.id, c.name, c.course_code, c.description, c.end_date as course_expiry, c.start_date, c.end_date, c.course_type, c.course_mode');
    $this->db->from('courses c');
    $this->db->join('special_courses sc', 'sc.course_id = c.id AND sc.is_active = 1', 'left');
    $this->db->group_start();
    $this->db->where('c.college_id', $student->college_id);
    $this->db->or_where('sc.to_college_id', $student->college_id);
    $this->db->group_end();
    $this->db->where('c.is_active', 1);
    $this->db->where_in('c.id', $course_ids);
    // // Apply course_type filter if passed
    if ($selected_type !== null) {
        $this->db->where('c.course_type', $selected_type);
    }
       $courses = $this->db->get()->result_array();

        foreach ($courses as &$test) {
            $current_date = date('Y-m-d H:i:s');
            $test['expired'] = ($current_date < $test['start_date'] || $current_date > $test['end_date']) ? true : false;
            unset($test);
        }

        if ($courses) {
            // Get all course tests
            $course_tests = $this->db_model->get_all(
                'course_tests',
                [
                    'course_id' => $course_ids,
                    'is_active' => 1
                ],
                'id, course_id, test_id'
            );

            // Get student's test submissions
            $student_submissions = $this->db_model->get_all(
                'student_test_submission',
                [
                    'student_id' => $student->id,
                    'course_id' => $course_ids,
                    // 'finished' => 1
                ],
                'test_id, course_id'
            );

            // Organize submissions by course
            $course_submissions = [];
            foreach ($student_submissions as $submission) {
                if (!isset($course_submissions[$submission['course_id']])) {
                    $course_submissions[$submission['course_id']] = [];
                }
                $course_submissions[$submission['course_id']][] = $submission['test_id'];
            }

            // Process each course
            foreach ($courses as &$course) {
                // Get tests for this course
                $course_test_ids = array_filter($course_tests, function($test) use ($course) {
                    return $test['course_id'] == $course['id'];
                });

                $total_tests = count($course_test_ids);
                $completed_tests = isset($course_submissions[$course['id']]) ? 
                    count($course_submissions[$course['id']]) : 0;

                $completion_percentage = $total_tests > 0 ? 
                    round(($completed_tests / $total_tests) * 100) : 0;

                // Add progress information to course
                $course['total_tests'] = $total_tests;
                $course['completed_tests'] = $completed_tests;
                $course['completion_percentage'] = $completion_percentage;
                $course['status'] = $completed_tests > 0 ? 'ongoing' : 'new';
            }

            $response = array(
                'status' => 'success',
                'message' => 'Courses retrieved successfully',
                'data' => $courses,
                // 'course_types'=>$course_types
            );
        } else {
            $response = array(
                'status' => 'success',
                'message' => 'No courses found',
                'data' => []
            );
        }
        echo json_encode($response);
        return;
    }



    
    public function test(){
        $content_type = $this->input->server( 'CONTENT_TYPE' );
        if ( strpos( $content_type, 'application/json' ) !== false ) {
            $json_input = json_decode( file_get_contents( 'php://input' ), true );
            $course_id = isset( $json_input[ 'course_id' ] ) ? $json_input[ 'course_id' ] : null;
            $module_id = isset( $json_input[ 'module_id' ] ) ? $json_input[ 'module_id' ] : null;
        } else {
            $course_id = $this->input->post( 'course_id' );
            $module_id = $this->input->post( 'module_id' );
        }
        if ( empty( $course_id ) ) {
            http_response_code( 400 );
            $response = array(
                'status' => 'error',
                'message' => 'Course ID is required',
                'data' => null
            );
            echo json_encode( $response );
            return;
        }


        


        $student = $this->session->userdata( $this->url . '_student' );
        $student_data =  $this->db_model->get_row("students", ["id" => $student->id]);
        if (!$student) {
            http_response_code(401);
            $response = array(
                'status' => 'error',
                'message' => 'User not authenticated',
                'data' => null
            );
            echo json_encode($response);
            return;
        }


        $access_courses = $this->get_student_course_ids($student);


        if (!in_array($course_id, $access_courses)){
            http_response_code(403);
            $response = array(
                'status' => 'error',
                'message' => 'No Access For This Course',
                'data' => null
            );
            echo json_encode($response);
            return;
        }


        // Single query to get all test data with submissions
        $tests_query = $this->db
            ->select('
                t.id, t.title, t.duration, t.challenge_id, t.challenge_link, t.no_of_attempts, t.pass_percentage, t.is_new_tab,
                ct.start_date, ct.end_date, ct.level,
                sts.attempts, sts.total_score, sts.earned_score, sts.finished
            ')
            ->from('course_tests ct')
            ->join('tests t', 'ct.test_id = t.id', 'inner')
            ->join('courses c', 'c.id = ct.course_id AND c.is_active = 1', 'inner')
            ->join('special_courses sc', 'sc.course_id = c.id AND sc.is_active = 1', 'left')
            ->join('student_test_submission sts', '
                sts.test_id = ct.test_id
                AND sts.student_id = ' . $this->db->escape($student->id) . '
                AND sts.course_id = ' . $this->db->escape($course_id) . '
                AND sts.module_id = ' . $this->db->escape($module_id) . '
            ', 'left')
            ->where('ct.course_id', $course_id)
            ->where('ct.module_id', $module_id)
            ->where('ct.is_active', 1)
            ->where('t.is_active', 1)
            ->group_start()
            ->where('t.college_id', $student->college_id)
            ->or_where('sc.to_college_id', $student->college_id)
            ->group_end()
            ->get()
            ->result_array();

        if(empty($tests_query)){
            $response = array(
                'status' => 'success',
                'message' => 'No tests found',
                'data' => []
            );
            echo json_encode($response);
            return;
        }

        // Process the results
        $tests = [];
        $course_tests_map = [];
        $submissions_by_test = [];

        foreach ($tests_query as $row) {
            $test_id = $row['id'];

            // Build test data
            $tests[] = [
                'id' => $test_id,
                'title' => $row['title'],
                'duration' => $row['duration'],
                'challenge_id' => $row['challenge_id'],
                'challenge_link' => $row['challenge_link'],
                'no_of_attempts' => $row['no_of_attempts'],
                'pass_percentage' => $row['pass_percentage'],
                'is_new_tab' => $row['is_new_tab']
            ];

            // Build course_tests_map
            $course_tests_map[$test_id] = [
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'level' => $row['level']
            ];

            // Build submissions_by_test
            if ($row['attempts'] !== null) {
                $submissions_by_test[$test_id] = [
                    'attempts' => (int)$row['attempts'],
                    'total_score' => (float)$row['total_score'],
                    'earned_score' => (float)$row['earned_score'],
                    'finished' => (int)$row['finished']
                ];
            }
        }


        // Optimized: No API calls needed - determine status from database only

        foreach ($tests as $key => $test) {
            // Use total_score from submission, default to 0 if no submission
            $test_submission = $submissions_by_test[$test['id']] ?? null;
            $total_score = $test_submission ? $test_submission['total_score'] : 0;

            $c_t = $course_tests_map[$test['id']] ?? null;
            // print_r($level);
            $tests[$key]['no_of_attempts'] = isset($test['no_of_attempts']) ? (int)$test['no_of_attempts'] : 0;
            $tests[$key]['attempted_count'] = isset($test_submission['attempts']) ? (int)$test_submission['attempts'] : 0;

            // test new tab open status
            $tests[$key]['is_new_tab'] = !empty($test['is_new_tab']) ? true : false;
            $tests[$key]['level'] = isset($c_t['level']) ?(int) $c_t['level'] : 0;;
            $tests[$key]['pass_percentage'] = isset($test['pass_percentage']) ?(int) $test['pass_percentage'] : 0;
            $duration_minutes = $tests[$key]['duration'];
            $duration_hours = floor($duration_minutes / 60);
            $remaining_minutes = $duration_minutes % 60;

            $duration_display = '';
            if ($duration_hours > 0) {
                $duration_display .= $duration_hours . 'h ';
            }
            if ($remaining_minutes > 0 || $duration_hours > 0) {
                $duration_display .= $remaining_minutes . 'm';
            }

            if (empty($duration_display)) {
                $duration_display = '0m';
            }

            $tests[$key]["duration_display"] = trim($duration_display);

            if ($test_submission && isset($test_submission['finished']) && $test_submission['finished'] == 1) {
                // Test is completed - use database status
                $tests[$key]['submitted'] = true;
                $tests[$key]['score'] = isset($test_submission['total_score']) ? $test_submission['total_score'] : null;
                $tests[$key]['earned_score'] = isset($test_submission['earned_score']) ? $test_submission['earned_score'] : null;

                $total_time_spent = $this->db
                ->select_sum('time_spent')
                ->from('student_solutions')
                ->where([
                    'course_id' => $course_id,
                    'module_id' => $module_id,
                    'test_id' => $test['id'],
                    'student_id' => $student->id
                ])
                ->get()
                ->row()
                ->time_spent;
                $total_hours = floor($total_time_spent / 3600);
                $total_minutes = floor(($total_time_spent % 3600) / 60);
                $total_seconds = $total_time_spent % 60;

                $total_time_display = '';
                if ($total_hours > 0) {
                    $total_time_display .= $total_hours . 'h ';
                }
                if ($total_minutes > 0 || $total_hours > 0) {
                    $total_time_display .= $total_minutes . 'm ';
                }
                $total_time_display .= $total_seconds . 's';
                $tests[$key]["time_spent"] = $total_time_display;
                $tests[$key]['status'] = 'completed';
            } else if ($test_submission) {
                // Has submission but not finished = in progress
                $tests[$key]['submitted'] = false;
                $tests[$key]['earned_score'] = null;
                $tests[$key]["time_spent"] = null;
                $tests[$key]['status'] = 'in_progress';
            } else {
                // No submission = not attempted
                $tests[$key]['submitted'] = false;
                $tests[$key]['earned_score'] = null;
                $tests[$key]["time_spent"] = null;
                $tests[$key]['status'] = 'not_attempted';
            }

            $course_test = $this->db_model->get_row(
                'course_tests',
                [
                    "course_id" => $course_id,
                    'is_active' => 1,
                    'module_id' => $module_id,
                    'test_id' => $test['id']
                ],
            );
            $tests[$key]['start_date'] = $course_test['start_date'];
            $tests[$key]['end_date'] = $course_test['end_date'];


            $current_date = date('Y-m-d H:i:s');
            $is_expired = ($current_date < $course_test['start_date'] || $current_date > $course_test['end_date']);

            if($is_expired){

                // $tests[$key]['link'] = '';
                $tests[$key]['status'] = 'expired';
                if($test_submission){
                    $tests[$key]['submitted'] = true;

                } else {
                    $tests[$key]['submitted'] = false;
                }

            }
            $test[$key]['is_expired'] =  $is_expired;

        }
        
        if ($tests) {
            $response = array(
                'status' => 'success',
                'message' => 'Tests retrieved successfully',
                'data' => $tests
            );
            echo json_encode($response);
            return;
        } else {
            $response = array(
                'status' => 'success',
                'message' => 'No tests found',
                'data' => []
            );
            echo json_encode($response);
            return;
        }

    }



    public function test_submit($test_id,$course_id,$module_id, $return_response = false){
        $content_type = $this->input->server('CONTENT_TYPE');
        
        // Get force_finish parameter
        $force_finish = false;
        if (strpos($content_type, 'application/json') !== false) {
            $json_input = json_decode(file_get_contents('php://input'), true);
            $force_finish = isset($json_input['force_finish']) ? (bool)$json_input['force_finish'] : false;
        } else {
            $force_finish = $this->input->post('force_finish') ? true : false;
        }
    
        $student = $this->session->userdata($this->url.'_student');
        
        if (!$student) {
            http_response_code(401);
            $response = array(
                'status' => 'error',
                'message' => 'User not authenticated',
                'data' => null
            );
            echo json_encode($response);
            return;
        }
    
        
        // $test = $this->db_model->get_row(
        //     'tests',
        //     [
        //         "id" => $test_id,
        //         "college_id" => $student->college_id
        //     ],
        //     'id,title,challenge_id'
        // );
        $test = $this->common->get_test_details($test_id, $student->college_id);

        if (!$test) {
            http_response_code(404);
            $response = array(
                'status' => 'error',
                'message' => 'Test not found',
                'data' => null
            );
            echo json_encode($response);
            return;
        }
    
        $challengeId = $test['challenge_id'];
        $studentData = $this->db_model->get_row(TABLE_STUDENT, ['id' => $student->id], 'external_id');
        $userId = $studentData['external_id'];
    
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://onecompiler.com/api/v1/challenges/submission/' . $challengeId . '/' . $userId . '?access_token=' . ONE_COMPILER_API_KEY,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        
        $api_response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            http_response_code(500);
            $response = array(
                'status' => 'error',
                'message' => 'Failed to fetch challenge submission details',
                'data' => null
            );
            echo json_encode($response);
            return;
        }
        
        $submission_data = json_decode($api_response, true);
        
        
        if (!isset($submission_data['status']) || $submission_data['status'] !== 'success') {
            http_response_code(500);
            $response = array(
                'status' => 'error',
                'message' => 'Invalid response from challenge submission API',
                'data' => null
            );
            echo json_encode($response);
            return;
        }
        
        
        // Calculate total score from all questions in the test (not just attempted ones)
        $total_score = $this->common->get_test_total_score($test_id);

        $earned_score = 0;
        $problem_scores = [];

        $problem_type_mapper = [
            'multipleChoice' => 1,
            'code' => 2,
            'fillInTheBlank' => 3
        ];

        // Delete existing solutions for this test attempt to clear previous data
        $this->db->trans_start();

        $this->db->delete('student_solutions', [
            'student_id' => $student->id,
            'test_id' => $test_id,
            'course_id' => $course_id,
            'module_id' => $module_id
        ]);

        // Determine if sections are enabled for this test to exclude default section questions
        $sections_enabled = false;
        $default_section_id = null;

        $ui_settings = $this->db->select('enable_sections')
            ->from(TABLE_TEST_SETTINGS_UI)
            ->where('test_id', $test_id)
            ->get()
            ->row();

        if ($ui_settings && !empty($ui_settings->enable_sections)) {
            $sections_enabled = true;
            $default_section = $this->db->select('id')
                ->from(TABLE_TEST_SECTIONS)
                ->where('id', DEFAULT_SECTION_ID)
                ->get()
                ->row();

            if ($default_section) {
                $default_section_id = $default_section->id;
            }
        }

        $batch_solutions = [];
        if (isset($submission_data['problemProgress']) && is_array($submission_data['problemProgress'])) {
            foreach ($submission_data['problemProgress'] as $problem) {

                $problem_details = null;
                foreach ($submission_data['problems'] as $p) {
                    if ($p['_id'] === $problem['problem']['_id']) {
                        $problem_details = $p;
                        break;
                    }
                }

                if ($problem_details) {
                    $earned_problem_score = isset($problem['score']) ? floatval($problem['score']) : 0;
                    $earned_score += $earned_problem_score;

                    $problem_type = isset($problem_details['properties']['problemType']) ?
                        $problem_details['properties']['problemType'] : '';

                    $problem_scores[] = [
                        'problem_id' => $problem['problem']['_id'],
                        'title' => $problem_details['title'],
                        'max_score' => isset($problem_details['properties']['score']) ?
                            floatval($problem_details['properties']['score']) : 0,
                        'earned_score' => $earned_problem_score,
                        'submissions' => isset($problem['metrics']['submissions']) ?
                            intval($problem['metrics']['submissions']) : 0,
                        'time_spent' => isset($problem['metrics']['timeSpent']) ?
                            intval($problem['metrics']['timeSpent']) : 0,
                        'problem_type' => $problem_type,
                        'testcase_result' => isset($problem['testcaseLevelResult'])?$problem['testcaseLevelResult'] :'',
                        'plagiarism_score' => isset($problem['plagiarismScore']) ? $problem['plagiarismScore'] :'' // plagiarismScore
                    ];

                    $this->db->select('qb.id');
                    $this->db->from('test_questions tq');
                    $this->db->join('question_bank qb', 'qb.id = tq.question_id');
                    $this->db->where('tq.test_id', $test_id);
                    $this->db->where('tq.is_active', 1);
                    $this->db->where('qb.question_title', $problem_details['title']);
                    if ($sections_enabled && $default_section_id !== null) {
                        $this->db->where('tq.section_id !=', $default_section_id);
                    }
                    $this->db->order_by('tq.question_order', 'asc');
                    $this->db->limit(1);
                    $question = $this->db->get()->row_array();

                    // if (!$question && isset($test['college_id'])) {
                    //     log_message('debug', 'MCQ question lookup fell back to college filter', [
                    //         'test_id' => $test_id,
                    //         'college_id' => $test['college_id'],
                    //         'question_title' => $problem_details['title']
                    //     ]);
                    //     $question = $this->db_model->get_row(
                    //         'question_bank',
                    //         [
                    //             'question_title' => $problem_details['title'],
                    //             'college_id' => $test['college_id']
                    //         ],
                    //         'id'
                    //     );
                    // }

                    if (!$question) {
                        log_message('error', 'Skipped storing student solution: question not part of test', [
                            'test_id' => $test_id,
                            'student_id' => $student->id,
                            'problem_id' => $problem['problem']['_id'],
                            'question_title' => $problem_details['title']
                        ]);
                        continue;
                    }

                    if ($question) {

                        $meta = isset($problem['userEnvData']) ? $problem['userEnvData'] : '';
                        $solution_data = [
                            'student_id' => $student->id,
                            'test_id' => $test_id,
                            'course_id' => $course_id,
                            'module_id' => $module_id,
                            'question_id' => $question['id'],
                            'problem_id' => $problem['problem']['_id'],
                            'score' => $earned_problem_score,
                            'max_score' => isset($problem_details['properties']['score']) ?
                                floatval($problem_details['properties']['score']) : 0,
                            'submission_time' => date('Y-m-d H:i:s'),
                            'time_spent' => isset($problem['metrics']['timeSpent']) ?
                                intval($problem['metrics']['timeSpent']) : 0,
                            'problem_type' => isset($problem_type_mapper[$problem_type]) ? $problem_type_mapper[$problem_type] : 0,
                            'meta' => json_encode($meta),
                            'solution' => '',
                            'language' => ''
                        ];

                        switch ($problem_type) {
                            case 'code':
                                if (isset($problem['solution']['code'])) {
                                    $code_solution = $problem['solution']['code'];
                                    $solution_data['language'] = isset($code_solution['properties']['language']) ?
                                        $code_solution['properties']['language'] : '';
                                    $testcase_result = isset($problem['testcaseLevelResult']) ? $problem['testcaseLevelResult'] : '';
                                    $plagiarism_score = isset($problem['plagiarismScore']) ? $problem['plagiarismScore'] :''; // plagiarismScore  added
                                    $solution_data['solution'] = json_encode(['files' => $code_solution['properties']['files'],'testcase_result' => $testcase_result,'plagiarism_score'=>$plagiarism_score]);
                                }
                                break;

                            case 'fillInTheBlank':
                                if (isset($problem['solution']['fillInTheBlank'])) {
                                    $solution_data['solution'] = json_encode($problem['solution']['fillInTheBlank']);
                                }
                                break;

                            case 'multipleChoice':
                                if (isset($problem['solution']['multipleChoice'])) {
                                    // Get MCQ options and answers
                                    $mcq_options = isset($problem_details['properties']['options']['multipleChoice']) ?
                                        $problem_details['properties']['options']['multipleChoice'] : null;

                                    if ($mcq_options) {
                                        $solution_data['solution'] = json_encode([
                                            'options' => $mcq_options['options'],
                                            'correct_answer' => $mcq_options['answer'],
                                            'answer' => $problem['solution']['multipleChoice']['answer']
                                        ]);
                                    } else {
                                        $solution_data['solution'] = json_encode($problem['solution']['multipleChoice']);
                                    }
                                }
                                break;

                        }
                        $batch_solutions[] = $solution_data;
                }
            }
        }

        // Batch insert all solutions
        if (!empty($batch_solutions)) {
            // Ensure all data is properly formatted for database insertion
            // foreach ($batch_solutions as &$solution) {
            //     foreach ($solution as $key => &$value) {
            //         if (is_array($value) || is_object($value)) {
            //             $value = json_encode($value);
            //         }
            //     }
            // }
            $inserted_count = $this->db->insert_batch('student_solutions', $batch_solutions);
            if ($inserted_count === false) {
                log_message('error', 'Failed to batch insert student solutions', [
                    'test_id' => $test_id,
                    'student_id' => $student->id,
                    'db_error' => $this->db->error()
                ]);
            }
        } else {
            log_message('info', 'No solutions to insert for test submission', [
                'test_id' => $test_id,
                'student_id' => $student->id
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            log_message('error', 'Transaction failed while saving student solutions', [
                'test_id' => $test_id,
                'student_id' => $student->id
            ]);

            http_response_code(500);
            $response = array(
                'status' => 'error',
                'message' => 'Failed to record student solutions',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

    }
        // Find the active submission record to update
        $existing_submission = $this->db->get_where('student_test_submission', [
            'student_id' => $student->id,
            'test_id' => $test_id,
            'course_id' => $course_id,
            'module_id' => $module_id,
            'finished' => 0
        ])->row();

        if (!$existing_submission) {
            // This shouldn't happen, but handle it just in case
            http_response_code(500);
            $response = array(
                'status' => 'error',
                'message' => 'No active submission found to update',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // Determine attempts (increment if this is a re-attempt)
        // $attempts = $existing_submission->attempts+1;

        

        // Update the existing submission with final results
        $submission = [
            'student_id' => $student->id,
            'test_id' => $test_id,
            'course_id' => $course_id,
            'module_id' => $module_id,
            'challenge_id' => $challengeId,
            'challenge_user_id' => $userId,
            'total_score' => floatval($total_score),
            'earned_score' => floatval($earned_score),
            'percentage' => $total_score > 0 ? max(0, ($earned_score / $total_score) * 100) : 0,
            'submission_time' => $submission_data['challenegeProgress']['updated'],
            'tab_changes' => isset($submission_data['challenegeProgress']['tabChanges']) ?
                intval($submission_data['challenegeProgress']['tabChanges']) : 0,
            'finished' => $force_finish ? 1 : (isset($submission_data['challenegeProgress']['finished']) ?
                ($submission_data['challenegeProgress']['finished'] ? 1 : 0) : 0),
            // 'attempts' => $attempts, // Use the calculated attempts
            'details' => json_encode([
                'problem_scores' => $problem_scores,
                'sessionRecordings' => $submission_data['challenegeProgress']['sessionRecordings'] ?? [],
                'trackingData' => $submission_data['challenegeProgress']['trackingData'] ?? []
            ]),
            'created_at' =>  $submission_data['challenegeProgress']['created']
        ];

        // Update the existing record
        $this->db->where('id', $existing_submission->id);
        $this->db->update('student_test_submission', $submission);
        $submission_id = $existing_submission->id;
        
        $response = array(
            'status' => 'success',
            'message' => 'Test submission recorded successfully',
            'data' => [
                'submission_id' => $submission_id,
                'total_score' => $total_score,
                'earned_score' => $earned_score,
                'percentage' => $submission['percentage'],
                'problem_scores' => $problem_scores
            ]
        );

        if ($return_response) {
            return $response;
        } else {
            echo json_encode($response);
            return;
        }
    }


    public function dashboard() { 
        $student = $this->session->userdata($this->url . '_student');
        if (!$student) {
            http_response_code(401);
            $response = array(
                'status' => 'error',
                'message' => 'User not authenticated',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // Get year filter from request
        $content_type = $this->input->server('CONTENT_TYPE');
        if (strpos($content_type, 'application/json') !== false) {
            $json_input = json_decode(file_get_contents('php://input'), true);
            $year = isset($json_input['year']) ? $json_input['year'] : date('Y');
        } else {
            $year = $this->input->get('year') ?: date('Y');
        }

        // Validate year
        if (!is_numeric($year) || strlen($year) != 4) {
            $year = date('Y');
        }

        $course_ids = $this->get_student_course_ids($student);
        $course_types = $this->common->get_student_course_types($student);
 
        $student_data = $this->db_model->get_row(TABLE_STUDENT, ['id' => $student->id], 'name,email,registration_number,batch,department,college_id');
        $student_data['college'] = $this->db_model->get_row(TABLE_COLLEGE, ['id' => $student_data['college_id']], 'name') ['name'];
        $student_data['department'] = $this->db_model->get_row(TABLE_DEPARTMENT, ['id' => $student_data['department']], 'name')['name'];


        // $course_ids = $this->db_model->get_all(TABLE_COURCES,[
        //     'id' => $course_ids,
        //     'college_id' => $student_data['college_id'],
        //     'is_active' => 1
        // ]);
        // $course_ids = array_column($course_ids,'id');
         // Fallback: if no direct courses found, check shared (special) courses
        if (empty($course_ids)) {    
                
                    // Get all active difficulty levels
                    $difficulty_levels = $this->db_model->get_all(
                        'question_difficulty_level',
                        ['is_active' => 1],
                        'id, level'
                    );

                    $difficulty_stats = [];
                    foreach ($difficulty_levels as $level) {
                        $difficulty_stats[strtolower($level['level'])] = [
                            'solved' => 0,
                            'total' => 0
                        ];
                    }


                    $response = array(
                        'status' => 'success',
                        'message' => 'Dashboard data fetched successfully',
                        'data' => [
                            'student' => $student_data,
                            'total_courses' => 0,
                            'total_tests' => 0,
                            'completed_tests' => 0,
                            'score_percentage' => 0,
                            'solved_questions' => [
                                'total' => ['solved' => 0, 'total' => 0],
                                'difficulty_levels' => $difficulty_stats
                            ],
                            'coding_stats' => [
                                'questions_attended' => 0,
                                'solved_correctly' => 0,
                                'score' => 0,
                                'accuracy' => 0
                            ],
                            'mcq_stats' => [
                                'questions_attended' => 0,
                                'solved_correctly' => 0,
                                'score' => 0,
                                'accuracy' => 0
                            ],
                            'contributions' => [
                                'total_days' => 0,
                                'total_hours' => 0,
                                'average_time' => 0,
                                'monthly_contributions' => [],
                                'year' => $year
                            ]
                        ]
                    );
                    echo json_encode($response);
                    return;
                  
        }

        // Get total tests
        $total_tests = $this->db_model->get_all(
            'course_tests',
            [               
                'course_id' => $course_ids
            ]
        );

        $no_of_courses = count($course_ids);
        $completed_tests = $this->db_model->get_all(
            'student_test_submission',
            [
                'student_id' => $student->id,
                'finished' => 1
            ]
        );

        // Calculate basic test statistics
        $no_of_tests = count($completed_tests);
        $score_percentage = 0;
        if ($no_of_tests > 0) {
            $total_earned_score = array_sum(array_column($completed_tests, 'earned_score'));
            $total_possible_score = array_sum(array_column($completed_tests, 'total_score'));
            $score_percentage = $total_possible_score > 0 ? ($total_earned_score / $total_possible_score) * 100 : 0;
        }

        // Get all active difficulty levels
        $difficulty_levels = $this->db_model->get_all(
            'question_difficulty_level',
            ['is_active' => 1],
            'id, level'
        );

        // Get solved questions by difficulty level
        $solved_questions = $this->db->query("
            SELECT 
                qdl.level,
                COUNT(DISTINCT q.id) as total_questions,
                COUNT(DISTINCT CASE WHEN ss.score > 0 THEN q.id END) as solved_questions
            FROM question_bank q
            JOIN question_difficulty_level qdl ON q.difficulty_level = qdl.id
            LEFT JOIN student_solutions ss ON q.id = ss.question_id AND ss.student_id = ?
            WHERE q.id IN (
                SELECT DISTINCT question_id 
                FROM test_questions 
                WHERE test_id IN (
                    SELECT test_id 
                    FROM course_tests 
                    WHERE course_id IN (?)
                )
            )
            AND qdl.is_active = 1
            GROUP BY qdl.id, qdl.level
            ORDER BY qdl.id
        ", [$student->id, implode(',', $course_ids)])->result_array();

        $difficulty_stats = [];
        foreach ($difficulty_levels as $level) {
            $difficulty_stats[strtolower($level['level'])] = [
                'solved' => 0,
                'total' => 0
            ];
        }

        $total_solved = 0;
        $total_questions = 0;

        foreach ($solved_questions as $stat) {
            $level = strtolower($stat['level']);
            $difficulty_stats[$level]['solved'] = (int)$stat['solved_questions'];
            $difficulty_stats[$level]['total'] = (int)$stat['total_questions'];
            $total_solved += (int)$stat['solved_questions'];
            $total_questions += (int)$stat['total_questions'];
        }

        // Get coding questions statistics
        $coding_stats = $this->db->query("
            SELECT 
                COUNT(DISTINCT ss.question_id) as questions_attended,
                COUNT(DISTINCT CASE WHEN ss.score > 0 THEN ss.question_id END) as solved_correctly,
                SUM(ss.score) as total_score
            FROM student_solutions ss
            JOIN question_bank q ON ss.question_id = q.id
            WHERE ss.student_id = ? 
            AND q.type = 2
            AND ss.course_id IN (?)
        ", [$student->id, implode(',', $course_ids)])->row_array();

        $coding_accuracy = $coding_stats['questions_attended'] > 0 
            ? ($coding_stats['solved_correctly'] / $coding_stats['questions_attended']) * 100 
            : 0;

        // Get MCQ statistics
        $mcq_stats = $this->db->query("
            SELECT 
                COUNT(DISTINCT ss.question_id) as questions_attended,
                COUNT(DISTINCT CASE WHEN ss.score > 0 THEN ss.question_id END) as solved_correctly,
                SUM(ss.score) as total_score
            FROM student_solutions ss
            JOIN question_bank q ON ss.question_id = q.id
            WHERE ss.student_id = ? 
            AND q.type = 1
            AND ss.course_id IN (?)
        ", [$student->id, implode(',', $course_ids)])->row_array();

        $mcq_accuracy = $mcq_stats['questions_attended'] > 0 
            ? ($mcq_stats['solved_correctly'] / $mcq_stats['questions_attended']) * 100 
            : 0;

        // Get contributions data with year filter
        $contributions = $this->db->query("
            SELECT 
                DATE(ss.submission_time) as submission_date,
                SUM(ss.time_spent) as daily_time_spent
            FROM student_solutions ss
            WHERE ss.student_id = ?
            AND ss.course_id IN (?)
            AND YEAR(ss.submission_time) = ?
            GROUP BY DATE(ss.submission_time)
            ORDER BY submission_date
        ", [$student->id, implode(',', $course_ids), $year])->result_array();

        $total_days = count($contributions);
        $total_hours = array_sum(array_column($contributions, 'daily_time_spent')) / 3600; // Convert seconds to hours
        $average_time = $total_days > 0 ? $total_hours / $total_days : 0;

        // Get monthly contributions with year filter
        $monthly_contributions = $this->db->query("
            SELECT 
                DATE_FORMAT(ss.submission_time, '%Y-%m') as month,
                COUNT(DISTINCT DATE(ss.submission_time)) as days_contributed,
                SUM(ss.time_spent) as total_time_spent
            FROM student_solutions ss
            WHERE ss.student_id = ?
            AND ss.course_id IN (?)
            AND YEAR(ss.submission_time) = ?
            GROUP BY DATE_FORMAT(ss.submission_time, '%Y-%m')
            ORDER BY month
        ", [$student->id, implode(',', $course_ids), $year])->result_array();

        // Get available years for filtering
        $available_years = $this->db->query("
            SELECT DISTINCT YEAR(submission_time) as year
            FROM student_solutions
            WHERE student_id = ?
            AND course_id IN (?)
            ORDER BY year DESC
        ", [$student->id, implode(',', $course_ids)])->result_array();

        $response = array(
            'status' => 'success',
            'message' => 'Dashboard data fetched successfully',
            'data' => [
                'student' => $student_data,
                'total_courses' => $no_of_courses,
                'course_types' => $course_types,
                'total_tests' => count($total_tests),
                'completed_tests' => count($completed_tests),
                'score_percentage' => $score_percentage,
                'solved_questions' => [
                    'total' => [
                        'solved' => $total_solved,
                        'total' => $total_questions
                    ],
                    'difficulty_levels' => $difficulty_stats
                ],
                'coding_stats' => [
                    'questions_attended' => (int)$coding_stats['questions_attended'],
                    'solved_correctly' => (int)$coding_stats['solved_correctly'],
                    'score' => (int)$coding_stats['total_score'],
                    'accuracy' => round($coding_accuracy, 2)
                ],
                'mcq_stats' => [
                    'questions_attended' => (int)$mcq_stats['questions_attended'],
                    'solved_correctly' => (int)$mcq_stats['solved_correctly'],
                    'score' => (int)$mcq_stats['total_score'],
                    'accuracy' => round($mcq_accuracy, 2)
                ],
                'contributions' => [
                    'total_days' => $total_days,
                    'total_hours' => round($total_hours, 2),
                    'average_time' => round($average_time, 2),
                    'monthly_contributions' => $monthly_contributions,
                    'year' => $year,
                    'available_years' => array_column($available_years, 'year')
                ]
              
            ]
        );

        echo json_encode($response);
        return;
    }

    public function course_modules($course_id = null){
        $student = $this->session->userdata($this->url . '_student');
        if (!$student) {
            http_response_code(401);
            $response = array(
                'status' => 'error',
                'message' => 'User not authenticated',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        if(!$course_id) {
            $response = array(
                'status' => 'error',
                'message' => 'Missing Course Id',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        $access_courses = $this->get_student_course_ids($student);


        if (!in_array($course_id, $access_courses)){
            http_response_code(403);
            $response = array(
                'status' => 'error',
                'message' => 'No Access For This Course',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // Get course details
        $course = $this->db_model->get_row('courses', [
            'id' => $course_id,
            'is_active' => 1
        ]);

        if (!$course) {
            $response = array(
                'status' => 'error',
                'message' => 'Course not found',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // Get all modules for the course
        $course_modules = $this->db_model->get_all('course_modules', [
            'course_id' => $course_id,
            'is_active' => 1
        ], 'id, name');

        // Get all tests for the course
        $course_tests = $this->db_model->get_all('course_tests', [
            'course_id' => $course_id,
            'is_active' => 1
        ], 'id, test_id, module_id');

        // Get student's test submissions
        $student_submissions = $this->db_model->get_all('student_test_submission', [
            'student_id' => $student->id,
            'course_id' => $course_id
            // 'finished' => 1
        ], 'test_id');

        $submitted_test_ids = array_column($student_submissions, 'test_id');

        // Calculate course status
        $course_status = !empty($submitted_test_ids) ? 'ongoing' : 'new';

        // Process modules with progress information
        $modules_with_progress = [];
        foreach ($course_modules as $module) {
            // Get tests for this module
            $module_tests = array_filter($course_tests, function($test) use ($module) {
                return $test['module_id'] == $module['id'];
            });

            $total_tests = count($module_tests);
            $completed_tests = 0;

            foreach ($module_tests as $test) {
                if (in_array($test['test_id'], $submitted_test_ids)) {
                    $completed_tests++;
                }
            }

            $completion_percentage = $total_tests > 0 ? round(($completed_tests / $total_tests) * 100) : 0;
            $module_status = $completed_tests > 0 ? 'ongoing' : 'new';

            if ( $completed_tests > 0 && $total_tests ==  $completed_tests ){
                $module_status = "completed";
            }

            $modules_with_progress[] = [
                'id' => $module['id'],
                'name' => $module['name'],
                'total_tests' => $total_tests,
                'completed_tests' => $completed_tests,
                'completion_percentage' => $completion_percentage,
                'status' => $module_status
            ];
        }

        $response = array(
            'status' => 'success',
            'message' => 'Modules fetched successfully',
            'data' => [
                'course' => [
                    'id' => $course['id'],
                    'name' => $course['name'],
                    'status' => $course_status
                ],
                'modules' => $modules_with_progress
            ]
        );
        echo json_encode($response);
        return;
    }

    public function logo(){
        $college = $this->db_model->get_row(TABLE_COLLEGE, ["is_active" => 1, "id" => SINGLE_COLLEGE_ID]);
        
        if (!$college || empty($college['logo'])) {
            http_response_code(404);
            echo "Logo not found";
            return;
        }

        $logo_path = FCPATH . "assets/college/" . $college['logo'];
        
        if (!file_exists($logo_path)) {
            http_response_code(404);
            echo "Logo file not found";
            return;
        }

        header('Content-Type: image/png');
        readfile($logo_path);
    }

    public function banner(){
        $college = $this->db_model->get_row(TABLE_COLLEGE, ["is_active" => 1, "id" => SINGLE_COLLEGE_ID]);
        
        if (!$college || empty($college['banner'])) {
            http_response_code(404);
            echo "banner not found";
            return;
        }

        $logo_path = FCPATH . "assets/college/" . $college['banner'];
        
        if (!file_exists($logo_path)) {
            http_response_code(404);
            echo "banner file not found";
            return;
        }

        header('Content-Type: image/png');
        readfile($logo_path);
    }
    
    public function test_result() {
        $student = $this->session->userdata($this->url . '_student');
        if (!$student) {
            http_response_code(401);
            $response = array(
                'status' => 'error',
                'message' => 'User not authenticated',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        $content_type = $this->input->server('CONTENT_TYPE');
        if (strpos($content_type, 'application/json') !== false) {
            $json_input = json_decode(file_get_contents('php://input'), true);
            $course_id = isset($json_input['course_id']) ? $json_input['course_id'] : null;
            $module_id = isset($json_input['module_id']) ? $json_input['module_id'] : null;
            $test_id = isset($json_input['test_id']) ? $json_input['test_id'] : null;
        } else {
            $course_id = $this->input->post('course_id');
            $module_id = $this->input->post('module_id');
            $test_id = $this->input->post('test_id');
        }

        if (empty($course_id) || empty($module_id) || empty($test_id)) {
            http_response_code(400);
            $response = array(
                'status' => 'error',
                'message' => 'Course ID, Module ID, and Test ID are required',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // $course = $this->db_model->get_row('courses', [
        //     "id" => $course_id,
        //     "is_active" => 1,
        //     "college_id" => $student->college_id
        // ]);
    
        $course = $this->common->get_course_details($course_id,$student->college_id);

        // Get course details
        $course_test = $this->db_model->get_row('course_tests', [
            "course_id" => $course_id,
            "module_id" => $module_id,
            "test_id" => $test_id
        ]);

        if (!$course) {
            http_response_code(404);
            $response = array(
                'status' => 'error',
                'message' => 'Course not found',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // Gate student result visibility using the explanation window toggle
        $results_gate_enabled = ($course_test && isset($course_test['show_explanation']) && intval($course_test['show_explanation']) === 1);
        $results_visible = true; // default: visible unless gated
        $result_message = null;
        $result_publish_date = null;

        if ($results_gate_enabled) {
            $current_time = date('Y-m-d H:i:s');

            // If dates are set, check if current time is within range
            if (!empty($course_test['explanation_start_date']) && !empty($course_test['explanation_end_date'])) {
                if ($current_time >= $course_test['explanation_start_date'] && $current_time <= $course_test['explanation_end_date']) {
                    $results_visible = true;
                } else {
                    $results_visible = false;

                    // Results not yet published or expired
                    if ($current_time < $course_test['explanation_start_date']) {
                        $result_publish_date = $course_test['explanation_start_date'];
                        $result_message = "Test results were not yet published.\n\nThe results are scheduled to be published on " . date('F d, Y \\a\\t h:i A', strtotime($result_publish_date)) . ". Kindly check again at the specified time.";
                    } else {
                        $result_message = "Test results are no longer available.";
                    }
                }
            }
            // If flag is enabled but no dates set, allow access (backward compatibility)
        }

        // If results are not visible, return error message
        if (!$results_visible) {
            http_response_code(403);
            $response = array(
                'status' => 'error',
                'message' => $result_message ?: 'Test results were not yet published.',
                'data' => null,
                'publish_date' => $result_publish_date
            );
            echo json_encode($response);
            return;
        }

        // Check if explanation is enabled and within time frame
        $show_explanation = false;
        if ($course_test && isset($course_test['show_explanation']) && $course_test['show_explanation'] == 1) {
            $current_time = date('Y-m-d H:i:s');
            if (!empty($course_test['explanation_start_date']) && !empty($course_test['explanation_end_date'])) {
                if ($current_time >= $course_test['explanation_start_date'] && $current_time <= $course_test['explanation_end_date']) {
                    $show_explanation = true;
                }
            }
        }

        // Get module details
        $module = $this->db_model->get_row('course_modules', [
            "id" => $module_id,
            "course_id" => $course_id,
            "is_active" => 1
        ]);

        if (!$module) {
            http_response_code(404);
            $response = array(
                'status' => 'error',
                'message' => 'Module not found',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // Get test details
        $test = $this->db
            ->select("t.*, ct.start_date, ct.end_date")
            ->from("tests t")
            ->join("course_tests ct", "ct.test_id = t.id")
            ->where("t.id", $test_id)
            ->where("ct.course_id", $course_id)
            ->where("ct.module_id", $module_id)
            ->where("ct.is_active", 1)
            ->limit(1)
            ->get()
            ->row_array();

        if (!$test) {
            http_response_code(404);
            $response = array(
                'status' => 'error',
                'message' => 'Test not found',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // Get student's submission
        $submission = $this->db_model->get_row('student_test_submission', [
            "course_id" => $course_id,
            "module_id" => $module_id,
            "test_id" => $test_id,
            "student_id" => $student->id
        ]);

        if (!$submission) {
            $response = array(
                'status' => 'success',
                'message' => 'No submission found',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // Decode submission details
        $submission_details = json_decode($submission['details'], true);
        $problem_details_map = [];
        if (is_array($submission_details) && isset($submission_details['problem_scores']) && is_array($submission_details['problem_scores'])) {
            foreach ($submission_details['problem_scores'] as $detail) {
                $problem_details_map[$detail['problem_id']] = $detail;
            }
        }

        // Calculate total time spent
        $total_time_spent = $this->db
            ->select_sum('time_spent')
            ->from('student_solutions')
            ->where([
                'course_id' => $course_id,
                'module_id' => $module_id,
                'test_id' => $test_id,
                'student_id' => $student->id
            ])
            ->get()
            ->row()
            ->time_spent;

        $submission['time_spent'] = $total_time_spent ? (int)$total_time_spent : 0;
        $submission['tab_changes'] = isset($submission['tab_changes']) ? $submission['tab_changes'] : 0;

        // Get questions and student's answers
        $questions = $this->common->get_test_questions($test_id);

        // Build section context for sectioned tests
        // $section_context = $this->common->build_section_context($questions);
        // $sections_enabled = $section_context['enabled'];

        // Process questions to handle explanation visibility
        foreach ($questions as &$question) {
            if (!$show_explanation) {
                $question['explanation'] = null;
            }
            unset($question);
        }

        // Get student's answers for each question
        $student_solutions = [];
        foreach ($questions as $question) {
            $solution = $this->db_model->get_row("student_solutions", [
                "test_id" => $test_id,
                "question_id" => $question["question_id"],
                "student_id" => $student->id,
                "course_id" => $course_id,
                "module_id" => $module_id
            ]);

            if ($solution) {
                // Process meta data
                $meta_data = json_decode($solution['meta'], true);
                $solution['submission_details'] = [
                    'ip_address' => $meta_data['userIp'] ?? 'N/A',
                    'browser' => $meta_data['userAgent'] ?? 'N/A',
                    'screen_resolution' => isset($meta_data['screenResolution']) ? 
                        $meta_data['screenResolution']['width'] . 'x' . $meta_data['screenResolution']['height'] : 'N/A',
                    'window_size' => isset($meta_data['windowResolution']) ? 
                        $meta_data['windowResolution']['width'] . 'x' . $meta_data['windowResolution']['height'] : 'N/A'
                ];

                // Map problem details if available
                if (isset($solution['problem_id']) && isset($problem_details_map[$solution['problem_id']])) {
                    $problem_detail = $problem_details_map[$solution['problem_id']];
                    $solution['submission_count'] = $problem_detail['submissions'] ?? 0;
                    $solution['problem_time_spent'] = $problem_detail['time_spent'] ?? 0;
                } else {
                    $solution['submission_count'] = 0;
                    $solution['problem_time_spent'] = 0;
                }

                // Process the solution based on question type
                if ($question['type'] == 1) { // MCQ
                    $solution_data = json_decode($solution['solution'], true);
                    if ($solution_data !== null && is_array($solution_data)) {
                        if (!isset($solution_data['options']) || !is_array($solution_data['options'])) {
                            $formatted_options = [];
                            foreach ($solution_data['options'] as $option) {
                                $formatted_options[] = [
                                    'id' => $option['id'],
                                    'option_text' => $option['text'],
                                    'text' => $option['text'],
                                    'is_correct' => isset($solution_data['correct_answer']) && in_array($option['id'], $solution_data['correct_answer']) ? 1 : 0
                                ];
                            }
                            $solution['options'] = $formatted_options;
                            $solution['correct_answer'] = isset($solution_data['correct_answer']) ? $solution_data['correct_answer'] : [];
                            $solution['answered_options'] = [];
                            if (isset($solution_data['answer']) && is_array($solution_data['answer'])) {
                                foreach ($solution_data['answer'] as $answer_index) {
                                    foreach ($formatted_options as $option) {
                                        if ($option['id'] == $answer_index) {
                                            $solution['answered_options'][] = $option;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else {
                        $formatted_options = [];
                        $options = $this->db_model->get_all("answer_options", [
                            "question_id" => $question["question_id"]
                        ]);
                        foreach ($options as $option) {
                            $formatted_options[] = [
                                'id' => $option['id'],
                                'option_text' => $option['option_text'],
                                'text' => $option['option_text'],
                                'is_correct' => $option['is_correct']
                            ];
                        }

                        $solution['options'] = $formatted_options;
                    }
                    $solution['max_score'] = $question['max_score'];
                }
                elseif ($question['type'] == 2) { // Code
                    $test_cases = $this->db_model->get_all("question_test_cases", [
                        "question_id" => $question["question_id"]
                    ]);
                    
                    $solution_data = json_decode($solution['solution'], true);
                    // print_r($solution_data);
                    // exit;
                    $testcase_results = [];
                    if (is_array($solution_data)) {
                        $code_files = [];
                        if (isset($solution_data['files']) && is_array($solution_data['files'])) {
                            foreach ($solution_data['files'] as $file) {
                                if (isset($file['name']) && isset($file['content'])) {
                                    $code_files[] = [
                                        'name' => $file['name'],
                                        'content' => $file['content']
                                    ];
                                }
                            }
                        }
                        $solution['code_solution'] = $code_files;
                        if (isset($solution_data['testcase_result']) && is_array($solution_data['testcase_result'])) {
                            $testcase_results = $solution_data['testcase_result'];
                        }
                        // ✅ Add plagiarism_score if available
                        $solution['plagiarism_score'] = isset($solution_data['plagiarism_score'])  ? $solution_data['plagiarism_score'] : ''; 
                        
                    } else {
                        $solution['code_solution'] = [];
                    }
                    // print_r($testcase_results);
                    // print_r($test_cases);
                    if (!empty($testcase_results)) {
                        foreach ($test_cases as $index => $test_case) {
                            if (isset($testcase_results[$index]['status'])) {
                                $test_cases[$index]['status'] = $testcase_results[$index]['status'];
                            }
                        }
                    }
                    $meta_data = json_decode($solution['meta'], true);
                    $solution['test_cases'] = isset($meta_data['test_cases']) ? $meta_data['test_cases'] : $test_cases;
                    $solution['language'] = $solution['language'] ?? 'N/A';
                    $solution['max_score'] = $question['max_score'];
                }
                elseif ($question['type'] == 3) { // Fill in Blank
                    $solution['answered_text'] = $solution['solution'];
                    $solution['correct_answer'] = $question['fill_blank_answer'];
                }

                $solution['time_spent'] = $solution['time_spent'] ?? 0;
                $solution['formatted_time_spent'] = $this->format_time_spent($solution['time_spent']);

                $student_solutions[$question["question_id"]] = $solution;
            } else {
                $empty_solution = [
                    'score' => 0,
                    'max_score' => $question['max_score'],
                    'time_spent' => 0,
                    'formatted_time_spent' => '0s',
                    'submission_details' => [
                        'ip_address' => 'N/A',
                        'browser' => 'N/A',
                        'screen_resolution' => 'N/A',
                        'window_size' => 'N/A'
                    ]
                ];

                if ($question['type'] == 1) {
                    $options = $this->db_model->get_all("answer_options", [
                        "question_id" => $question["question_id"]
                    ]);
                    $empty_solution['options'] = $options;
                    $empty_solution['answered_options'] = [];
                }
                elseif ($question['type'] == 2) {
                    $test_cases = $this->db_model->get_all("question_test_cases", [
                        "question_id" => $question["question_id"]
                    ]);
                    $empty_solution['test_cases'] = $test_cases;
                    $empty_solution['code_solution'] = [];
                    $empty_solution['language'] = 'N/A';
                }
                elseif ($question['type'] == 3) {
                    $empty_solution['correct_answer'] = $question['fill_blank_answer'];
                    $empty_solution['answered_text'] = '';
                }

                $student_solutions[$question["question_id"]] = $empty_solution;
            }
        }

        // Prepare section-wise data if sections are enabled
        // $section_data = null;
        // if ($sections_enabled) {
        //     $section_data = [
        //         'enabled' => true,
        //         'sections' => []
        //     ];

        //     foreach ($section_context['order'] as $section_id) {
        //         $section_questions = $section_context['grouped'][$section_id] ?? [];
        //         $section_solutions = [];

        //         // Calculate section scores
        //         $section_total_score = 0;
        //         $section_earned_score = 0;
        //         $section_time_spent = 0;

        //         foreach ($section_questions as $question) {
        //             $question_id = $question['question_id'];
        //             $section_total_score += $question['max_score'];

        //             if (isset($student_solutions[$question_id])) {
        //                 $solution = $student_solutions[$question_id];
        //                 $section_earned_score += $solution['score'];
        //                 $section_time_spent += $solution['time_spent'];
        //                 $section_solutions[$question_id] = $solution;
        //             }
        //         }

        //         $section_data['sections'][] = [
        //             'id' => $section_id,
        //             'name' => $section_context['map'][$section_id] ?? 'Unknown Section',
        //             'total_score' => $section_total_score,
        //             'earned_score' => $section_earned_score,
        //             'percentage' => $section_total_score > 0 ? round(($section_earned_score / $section_total_score) * 100, 2) : 0,
        //             'time_spent' => $section_time_spent,
        //             'formatted_time_spent' => $this->format_time_spent($section_time_spent),
        //             'questions' => $section_questions,
        //             'solutions' => $section_solutions
        //         ];
        //     }
        // }

        $response = array(
            'status' => 'success',
            'message' => 'Test results retrieved successfully',
            'data' => array(
                'test' => array(
                    'id' => $test['id'],
                    'title' => $test['title'],
                    'duration' => $test['duration'],
                    'start_date' => $test['start_date'],
                    'end_date' => $test['end_date']
                ),
                'course' => array(
                    'id' => $course['id'],
                    'name' => $course['name'],
                    'code' => $course['course_code']
                ),
                'module' => array(
                    'id' => $module['id'],
                    'name' => $module['name']
                ),
                'submission' => array(
                    'total_score' => $submission['total_score'],
                    'earned_score' => $submission['earned_score'],
                    'percentage' => $submission['percentage'],
                    'submission_time' => $submission['submission_time'],
                    'time_spent' => $submission['time_spent'],
                    'tab_changes' => $submission['tab_changes'],
                    'finished' => $submission['finished']
                ),
                // 'sections_enabled' => $sections_enabled,
                // 'sections' => $section_data,
                'questions' => $questions,
                'solutions' => $student_solutions
            )
        );

        echo json_encode($response);
        return;
    }

    private function format_time_spent($seconds) {
        if ($seconds < 60) {
            return $seconds . 's';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remaining_seconds = $seconds % 60;
            return $minutes . 'm ' . $remaining_seconds . 's';
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $remaining_seconds = $seconds % 60;
            return $hours . 'h ' . $minutes . 'm ' . $remaining_seconds . 's';
        }
    }


    public function get_test_link() {
        $content_type = $this->input->server('CONTENT_TYPE');
        if (strpos($content_type, 'application/json') !== false) {
            $json_input = json_decode(file_get_contents('php://input'), true);
            $test_id = isset($json_input['test_id']) ? $json_input['test_id'] : null;
            $course_id = isset($json_input['course_id']) ? $json_input['course_id'] : null;
            $module_id = isset($json_input['module_id']) ? $json_input['module_id'] : null;
        } else {
            $test_id = $this->input->post('test_id');
            $course_id = $this->input->post('course_id');
            $module_id = $this->input->post('module_id');
        }

        if (empty($course_id) || empty($module_id) || empty($test_id)) {
            http_response_code(400);
            $response = array(
                'status' => 'error',
                'message' => 'Test ID / Course ID / Module Id is required',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        $course_test = $this->db_model->get_row(
            'course_tests',
            [
                "course_id" => $course_id,
                'is_active' => 1,
                'module_id' => $module_id,
                'test_id' => $test_id
            ],
        );

        if (!$course_test) {
            http_response_code(400);
            $response = array(
                'status' => 'error',
                'message' => 'Invalid Request',
                'data' => null
            );
            echo json_encode($response);
            return;
        }

        // Time logic
        date_default_timezone_set('Asia/Kolkata');
        $current_time = date('Y-m-d H:i:s');
        $start = $course_test['start_date'];
        $end = $course_test['end_date'];

        if (!empty($start) && $current_time < $start) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'Test is not yet started. Start time: ' . $start,
                'data' => null
            ]);
            return;
        }

        if (!empty($end) && $current_time > $end) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'Test has expired. End time: ' . $end,
                'data' => null
            ]);
            return;
        }

        $student = $this->session->userdata($this->url . '_student');
        $student_data = $this->db_model->get_row("students", ["id" => $student->id]);

        // Gamification mode logic
        $course = $this->db_model->get_row('courses', ['id' => $course_id], 'course_mode');
        $course_mode = (int)$course['course_mode'];

        if ($course_mode == 2) {
            $all_tests = $this->db_model->get_all(
                'course_tests',
                [
                    'course_id' => $course_id,
                    'module_id' => $module_id,
                    'is_active' => 1
                ]
            );
            $all_test_levels = array_column($all_tests, 'level');
            sort($all_test_levels);
            $current_test_level = (int)$course_test['level'];
            $current_index = array_search($current_test_level, $all_test_levels);

            if ($current_index === false) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Test level not found in Gamification.',
                    'data' => null
                ]);
                return;
            }

            if ($current_index > 0) {
                $previous_level = $all_test_levels[$current_index - 1];
                $prev_test = null;
                foreach ($all_tests as $test_item) {
                    if ((int)$test_item['level'] == $previous_level) {
                        $prev_test = $test_item;
                        break;
                    }
                }

                if ($prev_test) {
                    $test_details = $this->db_model->get_row('tests', ['id' => $prev_test['test_id']], 'pass_percentage');
                    $required_pass_score = isset($test_details['pass_percentage']) ? (float)$test_details['pass_percentage'] : 0;

                    $prev_result = $this->db_model->get_row('student_test_submission', [
                        'student_id' => $student->id,
                        'test_id' => $prev_test['test_id'],
                        'course_id' => $course_id,
                        'module_id' => $module_id
                    ], 'percentage,finished');

                    if (!$prev_result || $prev_result['finished'] != 1) {
                        http_response_code(403);
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Please complete the previous level before attempting this test.',
                            'data' => null
                        ]);
                        return;
                    }

                    if ((float)$prev_result['percentage'] < $required_pass_score) {
                        http_response_code(403);
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'You completed the previous level but did not pass. Please reattempt and score at least ' . $required_pass_score . '%.',
                            'data' => [
                                'required_pass_score' => $required_pass_score,
                                'your_percentage' => (float)$prev_result['percentage']
                            ]
                        ]);
                        return;
                    }
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Previous level test not configured in the system.',
                        'data' => null
                    ]);
                    return;
                }
            }
        }

        $check = $this->test_model->get_attempted_test_count($student->id, $test_id, $course_id, $module_id);
        if ($check['status'] !== 'success') {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => $check['message'],
                'data' => [
                    'max_no_of_attempts' => $check['max_attempts'],
                    'attempted_count' => $check['attempted_count']
                ]
            ]);
            return;
        }

        // Check for existing active submission
        $existing_submission = $this->db->get_where('student_test_submission', [
            'student_id' => $student->id,
            'test_id' => $test_id,
            'course_id' => $course_id,
            'module_id' => $module_id
        ])->row();

        // Determine if this is a re-attempt based on database state

        if ($existing_submission) {
            if ($existing_submission->finished == 1) {
                // Test is finished in database, prepare for re-attempt
                $this->db->where('id', $existing_submission->id)->update('student_test_submission', [
                    'finished' => 0,
                    'attempts' => $existing_submission->attempts + 1
                ]);
                // Delete OneCompiler submission
                $delete_url = 'https://onecompiler.com/api/v1/challenges/submission/' . $existing_submission->challenge_id . '/' . $existing_submission->challenge_user_id . '?access_token=' . ONE_COMPILER_API_KEY;
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $delete_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                ]);
                curl_exec($curl);
                curl_close($curl);
            } else {
                // Test not finished in database - check OneCompiler status for confirmation
                // Make curl call to get submission data
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://onecompiler.com/api/v1/challenges/submission/' . $existing_submission->challenge_id . '/' . $existing_submission->challenge_user_id . '?access_token=' . ONE_COMPILER_API_KEY,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));
                $api_response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    // Log error, assume in progress
                    log_message('error', 'OneCompiler API Error in get_test_link: ' . $err);
                } else {
                    $submission_data = json_decode($api_response, true);
                    if (isset($submission_data['status']) && $submission_data['status'] === 'success' && isset($submission_data['challenegeProgress']['finished']) && $submission_data['challenegeProgress']['finished'] == true) {
                        // Test is finished on OneCompiler, sync DB with full results and return error
                        $this->test_submit($test_id, $course_id, $module_id, true);
                        // Return error message
                        http_response_code(403);
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Test was completed already',
                            'data' => null
                        ]);
                        return;
                    }
                }
            }
        } else {
            // First attempt
            $test = $this->common->get_test_details($test_id, $student->college_id);
            $submission_data = [
                'student_id' => $student->id,
                'test_id' => $test_id,
                'course_id' => $course_id,
                'module_id' => $module_id,
                'challenge_id' => $test['challenge_id'],
                'challenge_user_id' => $student_data['external_id'],
                'total_score' => 0,
                'earned_score' => 0,
                'percentage' => 0,
                'submission_time' => '',
                'tab_changes' => 0,
                'finished' => 0,
                'attempts' => 1,
                'details' => json_encode([])
            ];
            $this->db->insert('student_test_submission', $submission_data);
        }

        // Generate and return link
        $test = $this->common->get_test_details($test_id, $student->college_id);
        $link = 'https://embd.in/embed/challenges/' . $test['challenge_id'] . '/' . $test['challenge_link'] . '?apiKey=' . ONE_COMPILER_ACCESS_CODE . '&userApiToken=' . $student_data["user_token"] . '&hideNew=true' .'&challengeEvents=true';
        $link = base64_encode($link);

        $response = array(
            'status' => 'success',
            'data' => $link
        );
        echo json_encode($response);
        return;
    }

    public function lessons($module_id = null) {
        $student = $this->session->userdata($this->url . '_student');
        if (!$student) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'User not authenticated',
                'data' => null
            ]);
            return;
        }
        $module_id = (int)$module_id;
        if (!$module_id) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'module_id is required',
                'data' => null
            ]);
            return;
        }

        $lessons = $this->lesson_model->list_by_module($module_id);
        echo json_encode([
            'status' => 'success',
            'data' => $lessons
        ]);
    }
}

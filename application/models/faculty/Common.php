<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class common extends CI_Model {

    private $user_session;

	function __construct() { 
        $this->load->model("db_model"); 
        
    } 

    public function get_access_permissions($session) {
        $user_id =  $session['id'];
        $department = $session['department'];
        $role = $session['designation'];
        $college_id = $session['college_id'];

        if (!$user_id || !$role) {
            return ['read' => [], 'modify' => false]; 
        }

        if ($role == DESIGNATION_PRINCIPAL) {

            $this->db->select('id');
            $this->db->from('staffs');
            $this->db->where('college_id', $college_id);
            $this->db->where('is_active', 1);

            $query = $this->db->get();
            $staff_ids = array_column($query->result_array(), 'id');

            return [
                'read' => 'all',   
                'modify' => $staff_ids,
                'departments' => [],'additional_departments' => []
            ];
        } elseif ($role == DESIGNATION_HOD) {

            if (!$department) {
                return ['read' => [], 'modify' => false,'departments' => [],'additional_departments' => []]; 
            }

            $this->db->select('id');
            $this->db->from('staffs');
            $this->db->where('department', $department);
            $query = $this->db->get();

            $other_department_query = $this->db->select('other_department')->from('staffs')->where('id', $user_id)->get();
            $other_departments = $other_department_query->row()->other_department ?? null;

            $additional_staff_ids = [];
            if ($other_departments) {
                $other_departments_array = explode(',', $other_departments);
                foreach ($other_departments_array as $other_department) {
                    $additional_staff = $this->db->select('id')->from('staffs')->where('department', trim($other_department))
                    ->where('is_active', 1)
                    ->where('designation', DESIGNATION_STAFF)
                    ->get();
                    $additional_staff_ids = array_merge($additional_staff_ids, array_column($additional_staff->result_array(), 'id'));
                }
            }

            $staff_ids = array_column($query->result_array(), 'id');

            $combined_staff_ids = array_unique(array_merge($staff_ids, $additional_staff_ids));

            return [
                'read' => $combined_staff_ids,   
                'modify' => array_unique(array_merge($staff_ids, [$user_id])),
                'additional_departments' => array_unique(explode(',', $other_departments)),
                'department' => array_filter(
                    array_merge([$department], array_unique(explode(',', $other_departments))),
                    function($value) {
                        return $value !== null && $value !== '';
                    }
                )
            ];
        } elseif ($role == DESIGNATION_STAFF) {


            $department_staffs = $this->db_model->get_all(TABLE_STAFF,["is_active"=>true,"college_id"=>$college_id,"department"=>$department]);

            $staff_ids = array_column($department_staffs, 'id');
            $staff_ids = array_merge($staff_ids, [$user_id]);
            $staff_ids = array_unique($staff_ids);



            return [
                'read' =>  $staff_ids,   
                'modify' => [$user_id],
                'additional_departments' => [],
                'department' => [$department]
            ];
        }

        return ['read' => [], 'modify' => false]; 
    }
    

    function redirect_route($designation,$url){
        switch ($designation) {
            case DESIGNATION_PRINCIPAL: 
                redirect( base_url("$url/principal"));
                break;
     
            case DESIGNATION_HOD:
                redirect( base_url("$url/hod"));
                break;
    
            case DESIGNATION_STAFF:
                redirect( base_url("$url/staff"));
                break; 

            default:
                redirect( base_url("$url/login/faculty")); 
                break;
        }
    }

	public function check_user_session($url)
	{  
        $session = $this->session->userdata($url);
        if(empty($session)){  
            redirect( base_url("$url/login/faculty")); 
        }
        $college = $this->get_default_college();
        if(empty($college)){
            redirect( base_url("$url/login/faculty")); 
        }
        
        // Check if staff belongs to the requesting college
        $user = $this->db_model->get_row(TABLE_STAFF, [
            'id' => $session['id'],
            'college_id' => $college['id'],
            'is_active' => 1
        ]);   
        
        if(empty($user)) {
            $this->session->unset_userdata($url); 
            redirect( base_url("$url/login/faculty")); 
        }
        
        $this->session->unset_userdata($url); 
        $this->session->set_userdata($url, $user);
	} 

    public function get_default_college(){
       $college =  $this->db_model->get_row(TABLE_COLLEGE,["is_active"=>1,"id"=>SINGLE_COLLEGE_ID]);
        return $college;
    }

    public function check_student_session($url)
    {
        $session = $this->session->userdata($url);
        if(empty($session)){  
            return redirect(base_url(""));
        }
        $college = $this->get_default_college();
        if(empty($college)){
            return redirect(base_url(""));
        }
        $student=$this->db_model->get_row(TABLE_STUDENT,['id'=>$session['id'],"college_id"=>$college["id"],"is_active"=>1]);   
        if(empty($student)) {
            $this->session->unset_userdata($url); 
            redirect(base_url(""));
        }
        $this->session->unset_userdata($url); 
        $this->session->set_userdata($url, $student);
    }

    public function upload()
    {
        if (!empty($_FILES['image']['name'])) {
            $config['upload_path'] = 'application/uploads';
            $config['allowed_types'] = 'jpg|jpeg|png|svg|';
            $config['max_size'] = 2000;
            $config['file_name'] = md5($_FILES['image']['name'] . date("dmYHis"));

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            $uploadFile = $this->upload->do_upload('image');
            if ($uploadFile) {
                $uploadData = $this->upload->data();
                $picture = $uploadData['file_name'];
            }
            // else {
            // 	$this->session->set_flashdata('message', array('warning', $this->upload->display_errors()));
            // }
            return $picture;
        }
    }

    public function upload_csv() {
        // Set upload configuration
        $upload_path = 'application/uploads';

        // Check if the directory exists, if not, create it
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true); // Create directory with full permissions
        }
        $config['upload_path'] = $upload_path; // Path where files will be stored
        $config['allowed_types'] = 'csv';     // Only allow CSV files
        $config['max_size'] = 2048;            // Max file size (in KB)
        $config['file_name'] = md5($_FILES['csvFile']['name'] . date("dmYHis"));
        
        // Initialize upload library with config
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload('csvFile')) {
            // File uploaded successfully
            $data = $this->upload->data();
            return $data['file_name'];
        } else { 
            return false;
        }
    }

    public function read_csv($filePath) { 
        $headers = [];
        $values = [];
        
        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            // Read the first row for headers
            $headers = fgetcsv($handle, 1000, ',');
            
            // Read remaining rows for values
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $values[] = $data; // Add each row of data to the values array
            }
            fclose($handle); // Close the file after reading
        }

        return ['headers' => $headers, 'values' => $values];
    } 

    public function display_date($date){
        $formatted_date = date("M d, Y", strtotime($date));
        echo $formatted_date;
    }

    public function get_department($id){
        $data = $this->db_model->get_row(TABLE_DEPARTMENT,["id"=>$id]);
        return $data;
    }
    public function get_departments($id){
        $query = $this->db->query("
        SELECT 
            d.department,
            COALESCE(s.student_count, 0) AS student_count,
            COALESCE(h.hod_count, 0) AS hod_count,
            COALESCE(t.staff_count, 0) AS staff_count
        FROM (
            -- Get unique department names from both tables
            SELECT DISTINCT department FROM staffs WHERE is_active = 1 AND college_id = ? AND department IS NOT NULL
            UNION
            SELECT DISTINCT department FROM students WHERE is_active = 1 AND college_id = ? AND department IS NOT NULL
        ) AS d
        LEFT JOIN (
            -- Count active students per department
            SELECT department, COUNT(id) AS student_count
            FROM students
            WHERE is_active = 1 AND college_id = ?
            GROUP BY department
        ) AS s ON d.department = s.department
        LEFT JOIN (
            -- Count active HODs per department
            SELECT department, COUNT(id) AS hod_count
            FROM staffs
            WHERE is_active = 1 AND college_id = ? AND designation = ?
            GROUP BY department
        ) AS h ON d.department = h.department
        LEFT JOIN (
            -- Count active staff per department
            SELECT department, COUNT(id) AS staff_count
            FROM staffs
            WHERE is_active = 1 AND college_id = ? AND designation = ?
            GROUP BY department
        ) AS t ON d.department = t.department
    ", [$id, $id, $id, $id,DESIGNATION_HOD,$id,DESIGNATION_STAFF]);
    $result = $query->result_array();
    return $result;
    }

    public function upload_to_cloudinary($field_name = 'image', $folder = '') 
    {
        if (!empty($_FILES[$field_name]['name'])) {

            
            $cloud_name = CLOUDINARY_CLIENT;
            $api_key = CLOUDINARY_API_KEY;
            $api_secret = CLOUDINARY_API_SECRET;
            $upload_url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload";
            
        
            $file_path = $_FILES[$field_name]['tmp_name'];
            $file_name = $_FILES[$field_name]['name'];
            
        
            $params = [
                'file' => new CURLFile($file_path, $_FILES[$field_name]['type'], $file_name),
                'upload_preset' => CLOUDINARY_PRESET, 
                'timestamp' => time(),
            ];
            
        
            if (!empty($folder)) {
                $params['folder'] = $folder;
            }
            
        
            $params['signature'] = $this->generate_cloudinary_signature($params, $api_secret);
            $params['api_key'] = $api_key;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $upload_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only, remove in production
            
        
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                log_message('error', 'Cloudinary upload error: ' . curl_error($ch));
                return false;
            }
            
            curl_close($ch);
            
            $result = json_decode($response, true);
            
            if (isset($result['secure_url'])) {
                return $result['secure_url'];
            } else {
                log_message('error', 'Cloudinary upload failed: ' . print_r($result, true));
                return false;
            }
        }
        return false;
    }

    private function generate_cloudinary_signature($params, $api_secret) {
    
        $signature_params = $params;
        unset($signature_params['file']);
        unset($signature_params['resource_type']);
        
        
        ksort($signature_params);
        
        
        $string_to_sign = http_build_query($signature_params);
        $string_to_sign = str_replace(['%0D%0A', '%0A%0D', '%0D'], '%0A', $string_to_sign);
        
        
        $signature = sha1($string_to_sign . $api_secret);
        
        return $signature;
    }

public function get_student_course_types($student)
{
    if (!$student || !isset($student->college_id)) {
        return [];
    }

    $course_type_config = unserialize(COURSE_TYPES);
    // Fetch distinct course types for the student's college
    // $result = $this->db->distinct()
    //     ->select('course_type')
    //     ->where([
    //         'college_id' => $student->college_id,
    //         'is_active' => 1
    //     ])
    //     ->get('courses')
    //     ->result_array();

    $this->db->distinct();
    $this->db->select('c.course_type');
    $this->db->from('courses c');

    $this->db->group_start();
        // Owned courses
        $this->db->where('c.college_id', $student->college_id);
        // Shared courses
        $this->db->or_where('c.id IN (
            SELECT course_id FROM special_courses 
            WHERE to_college_id = ' . (int)$student->college_id . ' AND is_active = 1
        )');
    $this->db->group_end();

    $this->db->where('c.is_active', 1);
    $result = $this->db->get()->result_array();

    $course_type_ids = array_column($result, 'course_type'); // e.g., [1, 2]
    $type_map = [];     
    foreach ($course_type_config as $type_name => $info) {
        $type_map[$info['id']] = $type_name;
    }

    $course_types = [];
    foreach ($course_type_ids as $id) {
        if (isset($type_map[$id])) {
            $course_types[] = [
                'id' => (int)$id,
                'type' => $type_map[$id]
            ];
        }
    }

    return $course_types;
}

// single course  details
public function get_course_details($course_id, $college_id)
{
    $this->db->select('c.*, sc.to_college_id');
    $this->db->from(TABLE_COURCES . ' as c');
    $this->db->join(TABLE_SPECIAL_COURSES . ' as sc', 'sc.course_id = c.id AND sc.is_active = 1', 'left');
    $this->db->where('c.id', $course_id);
    $this->db->where('c.is_active', 1);
    $this->db->group_start();
    $this->db->where('c.college_id', $college_id); // own college
    $this->db->or_where('sc.to_college_id', $college_id); // shared
    $this->db->group_end();
    $this->db->limit(1);

    return $this->db->get()->row_array();
}

// single test details
public function get_test_details($test_id, $college_id)
{
    $this->db->select('t.*');
    $this->db->from('tests t');
    $this->db->join(TABLE_COURSE_TESTS .' as ct', 'ct.test_id = t.id AND ct.is_active = 1');
    $this->db->join(TABLE_COURCES .' as c', 'c.id = ct.course_id AND c.is_active = 1');
    $this->db->join(TABLE_SPECIAL_COURSES .' as sc', 'sc.course_id = c.id AND sc.is_active = 1', 'left');
    $this->db->where('t.id', $test_id);
    $this->db->group_start();
    $this->db->where('t.college_id', $college_id);
    $this->db->or_where('sc.to_college_id', $college_id);
    $this->db->group_end();
    $this->db->limit(1);

    return $this->db->get()->row_array();
}

// all tests 
public function get_student_tests($test_ids, $college_id)
{
    if (empty($test_ids) || empty($college_id)) {
        return [];
    }

    $this->db->select('t.*');
    $this->db->from('tests t');
    $this->db->join(TABLE_COURSE_TESTS. ' as ct', 'ct.test_id = t.id AND ct.is_active = 1');
    $this->db->join(TABLE_COURCES .' as c', 'c.id = ct.course_id AND c.is_active = 1');
    $this->db->join(TABLE_SPECIAL_COURSES .' as sc', 'sc.course_id = c.id AND sc.is_active = 1', 'left');
    $this->db->where_in('t.id', $test_ids);
    $this->db->group_start();
    $this->db->where('t.college_id', $college_id);
    $this->db->or_where('sc.to_college_id', $college_id);
    $this->db->group_end();
    $this->db->group_by('t.id');

    return $this->db->get()->result_array();
}

  
public function get_test_total_score($test_id) {
    // Check if sections are enabled
    $this->db->select('*');
    $this->db->from(TABLE_TEST_SETTINGS_UI);
    $this->db->where('test_id', $test_id);
    $ui = $this->db->get()->row();
    $sections_enabled = $ui && $ui->enable_sections;

    // If sections are enabled, exclude questions in the default unassigned section
    $default_section_id = null;
    if ($sections_enabled) {
        $this->db->select('*');
        $this->db->from(TABLE_TEST_SECTIONS);
        $this->db->where('id', DEFAULT_SECTION_ID);
        $default_section = $this->db->get()->row();
        
        if ($default_section) {
            $default_section_id = $default_section->id;
        }
    }

    $this->db->select('SUM(q.score) as total_score');
    $this->db->from('test_questions tq');
    $this->db->join('question_bank q', 'q.id = tq.question_id');
    $this->db->where('tq.test_id', $test_id);
    $this->db->where('tq.is_active', 1);
    
    if ($default_section_id !== null) {
        $this->db->where('tq.section_id !=', $default_section_id);
    }
    
    $result = $this->db->get()->row();
    return $result ? (float)$result->total_score : 0;
}
public function get_test_questions($test_id) {
   // Check if sections are enabled
   $this->db->select('*');
   $this->db->from(TABLE_TEST_SETTINGS_UI);
   $this->db->where('test_id', $test_id);
   $ui = $this->db->get()->row();
   $sections_enabled = $ui && $ui->enable_sections;

   // If sections are enabled, exclude questions in the default unassigned section
   $default_section_id = null;
   if ($sections_enabled) {
       $this->db->select('*');
       $this->db->from(TABLE_TEST_SECTIONS);
       $this->db->where('id', DEFAULT_SECTION_ID);
       $default_section = $this->db->get()->row();

       if ($default_section) {
           $default_section_id = $default_section->id;
       }
   }

   $select_fields = "q.*, tq.question_order, tq.section_id, q.score as max_score, q.id as question_id, q.question_title, q.question_content, q.type, q.fill_blank_answer";
   if ($sections_enabled) {
       $select_fields .= ", ts.section_name";
       $this->db->join(TABLE_TEST_SECTIONS . " ts", "ts.id = tq.section_id", "left");
   }
   $this->db->select($select_fields);
   $this->db->from("test_questions tq");
   $this->db->join("question_bank q", "q.id = tq.question_id");
   $this->db->where("tq.test_id", $test_id);
   $this->db->where("tq.is_active", 1);

   if ($default_section_id !== null) {
       $this->db->where("tq.section_id !=", $default_section_id);
   }

   return $this->db
       ->order_by("tq.question_order", "asc")
       ->get()
       ->result_array();
}

/**
 * Update OneCompiler when sections are enabled during test edit
 * Sends empty arrays for problems and sections so user can select questions for sections
 * 
 * @param int $test_id Test ID
 * @return bool Success status
 */
public function update_onecompiler_for_sections_enable($test_id) {
    // Load test_model if not already loaded
    if (!isset($this->test_model)) {
        $this->load->model('test_model');
    }
    
    $test = $this->test_model->get_test($test_id);
    if (!$test || !$test->challenge_id) {
        log_message('error', 'OneCompiler Section Enable: Test or challenge_id not found for test_id: ' . $test_id);
        return false;
    }

    $api_token = ONE_COMPILER_API_KEY;
    if (empty($api_token)) {
        log_message('error', 'OneCompiler API Error: API token not configured');
        return false;
    }

    // STEP 1: Read existing challenge from OneCompiler
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://onecompiler.com/api/v1/challenges/' . $test->challenge_id . '?access_token=' . $api_token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        log_message('error', 'OneCompiler API Error (Read Challenge): ' . $err);
        return false;
    }

    $challenge_response = json_decode($response, true);
    
    // Check response structure
    if (isset($challenge_response['doc'])) {
        $existing_challenge = $challenge_response['doc'];
        $existing_problems = $challenge_response['doc']['problems'] ?? [];
    } elseif (isset($challenge_response['challenge'])) {
        $existing_challenge = $challenge_response['challenge'];
        $existing_problems = $challenge_response['problems'] ?? [];
    } else {
        log_message('error', 'OneCompiler API Error: Unexpected response structure');
        return false;
    }

    // Verify required fields exist
    if (!isset($existing_challenge['_id']) || !isset($existing_challenge['user'])) {
        log_message('error', 'OneCompiler API Error: Challenge missing required fields (_id or user)');
        return false;
    }

    // STEP 2: Update challenge properties with empty arrays
    if (!isset($existing_challenge['properties'])) {
        $existing_challenge['properties'] = [];
    }
    $existing_challenge['properties']['problemIds'] = [];
    $existing_challenge['properties']['sections'] = [];

    // STEP 3: Prepare update data with empty problems array
    $update_data = [
        'challenge' => $existing_challenge,
        'problems' => []  // Empty problems array
    ];

    // STEP 4: Send update to OneCompiler
    log_message('info', 'OneCompiler Section Enable Update - Test ID: ' . $test_id . ' - Sending empty arrays');

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://onecompiler.com/api/v1/challenges/update?access_token=' . $api_token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($update_data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        log_message('error', 'OneCompiler API Error (Update): ' . $err);
        return false;
    }

    $result = json_decode($response, true);
    
    if (isset($result['status']) && $result['status'] === 'success') {
        log_message('info', 'OneCompiler updated with empty sections for test_id: ' . $test_id);
        return true;
    } else {
        log_message('error', 'OneCompiler API Error (Update Response): ' . json_encode($result));
        return false;
    }
}



    /**
     * Check whether sections are enabled for a given test from UI settings.
     *
     * @param int $test_id
     * @return bool
     */
    public function is_sections_enabled_for_test($test_id)
    {
        if (!$test_id) {
            return false;
        }

        $ui_settings = $this->db
            ->select('enable_sections')
            ->from(TABLE_TEST_SETTINGS_UI)
            ->where('test_id', $test_id)
            ->get()
            ->row();

        return $ui_settings && intval($ui_settings->enable_sections) === 1;
    }

    /**
     * Build section context from questions that already have section_name.
     *
     * NOTE:
     * - This helper is only responsible for GROUPING questions by section.
     * - Whether sections are enabled for a test should be decided at the test/UI
     *   level (TABLE_TEST_SETTINGS_UI) and passed in as $sections_enabled.
     *
     * @param array $questions
     * @param bool  $sections_enabled  Flag from test UI/settings indicating if sections are enabled.
     * @return array
     */
    public function build_section_context(array $questions, bool $sections_enabled) {
        $context = [
            'enabled' => $sections_enabled,
            'map'     => [],
            'grouped' => [],
            'order'   => []
        ];

        // If there are no questions or sections are not enabled, just return the defaults.
        if (empty($questions) || !$sections_enabled) {
            return $context;
        }

        $section_order = [];
        foreach ($questions as $question) {
            // Skip questions without a valid section_id / section_name
            if (empty($question['section_id']) || empty($question['section_name'])) {
                continue;
            }

            $section_id   = (int) $question['section_id'];
            $section_name = $question['section_name'];

            if (!isset($context['grouped'][$section_id])) {
                $context['grouped'][$section_id] = [];
                $context['map'][$section_id]     = $section_name;
                $section_order[]                 = $section_id;
            }

            $context['grouped'][$section_id][] = $question;
        }

        $context['order'] = $section_order;

        return $context;
    }
}

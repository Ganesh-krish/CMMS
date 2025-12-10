<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Test_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }


    
    /**
     * Get filtered tests based on provided conditions
     * 
     * @param array $conditions Array of conditions for filtering tests
     * @return array List of tests matching the filter criteria
     */
    public function get_filtered_tests($conditions = []) {
        $this->db->select('t.*, m.name as module_name');
        $this->db->from(TABLE_TESTS . ' as t');
        $this->db->join(TABLE_TEST_MODULES . ' as m', 'm.id = t.module_id', 'left');
        // Apply filters
        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                $this->db->where('t.' . $field, $value);
            }
        }
        
        // Order by creation date, newest first
        $this->db->order_by('t.created_at', 'DESC');
        
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_all_tests() {
        $this->db->select('*');
        $this->db->from(TABLE_TESTS);
        $this->db->where('is_active', 1);
        $this->db->order_by('created_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    public function get_test($id) {
        $this->db->where('id', $id);
        return $this->db->get(TABLE_TESTS)->row();
    }


    
    public function create_test($data,$created_by,$college_id) {
        // Start transaction
        $this->db->trans_start();
        
        // Insert main test data
        $test_data = [
            'title' => $data['title'],
            'instructions' => $data['instructions'],
            'start_date' => !empty($data['start_date']) ? $data['start_date'] : NULL,
            'end_date' => !empty($data['end_date']) ? $data['end_date'] : NULL,
            'duration' => !empty($data['duration']) ? $data['duration'] : 0,
            'module_id' => !empty($data['module_id']) ? $data['module_id'] : NULL,
            'pass_percentage' => !empty($data['pass_percentage'])  ? $data['pass_percentage'] : 50 ,
            'is_active' =>  1,
            'created_by' => $created_by,
            'college_id' => $college_id,
            'no_of_attempts' => !empty($data['maxattempts']) ? $data['maxattempts'] : 1,
            'negative_mark_value' => !empty($data['negative_mark_value']) ? $data['negative_mark_value'] : 0,
            'is_new_tab' => isset($data['is_new_tab']) ? 1 : 0 // New field to indicate if test opens in new tab
        ];
        $this->db->insert(TABLE_TESTS, $test_data);
        $test_id = $this->db->insert_id();
        
        if ($test_id) {
            // Insert navigation settings
            $nav_data = [
                'test_id' => $test_id,
                'next_test_id' => !empty($data['next_test_id']) ? $data['next_test_id'] : NULL,
                'prev_test_id' => !empty($data['prev_test_id']) ? $data['prev_test_id'] : NULL,
                'enable_finish_button' => isset($data['enable_finish_button']) ? 1 : 0,
                'show_next_on_last_question' => isset($data['show_next_on_last_question']) ? 1 : 0,
            ];
            $this->db->insert(TABLE_TEST_SETTINGS_NAVIGATION, $nav_data);
            
            // Insert security settings
            $security_data = [
                'test_id' => $test_id,
                'disable_copy_paste' => isset($data['disable_copy_paste']) ? 1 : 0,
                'shuffle_questions' => isset($data['shuffle_questions']) ? 1 : 0,
                'shuffle_answer_options' => isset($data['shuffle_answer_options']) ? 1 : 0,
                'plagiarism_check' => isset($data['plagiarism_check']) ? 1 : 0,
                'force_full_screen' => isset($data['force_full_screen']) ? 1 : 0
            ];
            $this->db->insert(TABLE_TEST_SETTINGS_SECURITY, $security_data);
            
            // Insert monitoring settings
            $monitoring_data = [
                'test_id' => $test_id,
                'capture_tab_change' => isset($data['capture_tab_change']) ? 1 : 0,
                'capture_user_image' => isset($data['capture_user_image']) ? 1 : 0,
                'enable_time_tracking' => isset($data['enable_time_tracking']) ? 1 : 0,
                'record_user_session' => isset($data['record_user_session']) ? 1 : 0
            ];
            $this->db->insert(TABLE_TEST_SETTINGS_MONITORING, $monitoring_data);
            
            // Insert UI settings
            $ui_data = [
                'test_id' => $test_id,
                'show_tab_change_warning' => isset($data['show_tab_change_warning']) ? 1 : 0,
                'close_after_tab_change' => isset($data['close_after_tab_change']) ? 1 : 0,
                'close_after_tab_count' => !empty($data['close_after_tab_count']) ? $data['close_after_tab_count'] : NULL,
                'show_instructions_on_start' => isset($data['show_instructions_on_start']) ? 1 : 0,
                'hide_score_after_title' => isset($data['hide_score_after_title']) ? 1 : 0,
                'show_report_on_finish' => isset($data['show_report_on_finish']) ? 1 : 0,
                'hide_test_end_time' => isset($data['hide_test_end_time']) ? 1 : 0,
                'auto_submit' => isset($data['auto_submit']) ? 1 : 0,
                'hide_problem_menu' => isset($data['hide_problem_menu']) ? 1 : 0,
                'enable_sections' => isset($data['enable_sections']) ? 1 : 0
            ];
            $this->db->insert(TABLE_TEST_SETTINGS_UI, $ui_data);
        }
        
        // Complete transaction
        $this->db->trans_complete();
        
        return $this->db->trans_status() ? $test_id : false;
    }
    
    public function update_test($id,$data,$created_by) {
        // Start transaction
        $this->db->trans_start();
        // Update main test data
        $test_data = [
            'title' => $data['title'],
            'instructions' => $data['instructions'],
            'start_date' => !empty($data['start_date']) ? $data['start_date'] : NULL,
            'end_date' => !empty($data['end_date']) ? $data['end_date'] : NULL,
            'duration' => !empty($data['duration']) ? $data['duration'] : 0,
            'module_id' => !empty($data['module_id']) ? $data['module_id'] : NULL,
            'pass_percentage' => !empty($data['pass_percentage'])  ? $data['pass_percentage'] : 50 ,
            'no_of_attempts' => !empty($data['maxattempts'])  ? $data['maxattempts'] : 1,
            'negative_mark_value' =>!empty($data['negative_mark_value'])  ? $data['negative_mark_value'] : 0,
            'is_new_tab' => isset($data['is_new_tab']) ? 1 : 0,
            'updated_by' => $created_by
        ];
        $this->db->where('id', $id);
        $this->db->update(TABLE_TESTS, $test_data);
        
        // Update navigation settings
        $nav_data = [
            'next_test_id' => !empty($data['next_test_id']) ? $data['next_test_id'] : NULL,
            'prev_test_id' => !empty($data['prev_test_id']) ? $data['prev_test_id'] : NULL,
            'enable_finish_button' => isset($data['enable_finish_button']) ? 1 : 0,
            'show_next_on_last_question' => isset($data['show_next_on_last_question']) ? 1 : 0
        ];
        $this->db->where('test_id', $id);
        $this->db->update(TABLE_TEST_SETTINGS_NAVIGATION, $nav_data);
        
        // Update security settings
        $security_data = [
            'disable_copy_paste' => isset($data['disable_copy_paste']) ? 1 : 0,
            'shuffle_questions' => isset($data['shuffle_questions']) ? 1 : 0,
            'shuffle_answer_options' => isset($data['shuffle_answer_options']) ? 1 : 0,
            'plagiarism_check' => isset($data['plagiarism_check']) ? 1 : 0,
            'force_full_screen' => isset($data['force_full_screen']) ? 1 : 0
        ];
        $this->db->where('test_id', $id);
        $this->db->update(TABLE_TEST_SETTINGS_SECURITY, $security_data);
        
        // Update monitoring settings
        $monitoring_data = [
            'capture_tab_change' => isset($data['capture_tab_change']) ? 1 : 0,
            'capture_user_image' => isset($data['capture_user_image']) ? 1 : 0,
            'enable_time_tracking' => isset($data['enable_time_tracking']) ? 1 : 0,
            'record_user_session' => isset($data['record_user_session']) ? 1 : 0
        ];
        $this->db->where('test_id', $id);
        $this->db->update(TABLE_TEST_SETTINGS_MONITORING, $monitoring_data);
        
        // Update UI settings
        $ui_data = [
            'show_tab_change_warning' => isset($data['show_tab_change_warning']) ? 1 : 0,
            'close_after_tab_change' => isset($data['close_after_tab_change']) ? 1 : 0,
            'close_after_tab_count' => !empty($data['close_after_tab_count']) ? $data['close_after_tab_count'] : NULL,
            'show_instructions_on_start' => isset($data['show_instructions_on_start']) ? 1 : 0,
            'hide_score_after_title' => isset($data['hide_score_after_title']) ? 1 : 0,
            'show_report_on_finish' => isset($data['show_report_on_finish']) ? 1 : 0,
            'hide_test_end_time' => isset($data['hide_test_end_time']) ? 1 : 0,
            'auto_submit' => isset($data['auto_submit']) ? 1 : 0,
            'hide_problem_menu' => isset($data['hide_problem_menu']) ? 1 : 0,
            'enable_sections' => isset($data['enable_sections']) ? 1 : 0
        ];
        $this->db->where('test_id', $id);
        $this->db->update(TABLE_TEST_SETTINGS_UI, $ui_data);
        
        // Complete transaction
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    public function delete_test($id) {
        // Start transaction
        $this->db->trans_start();
        
        // // Delete test settings
        // $this->db->where('test_id', $id);
        // $this->db->delete(TABLE_TEST_SETTINGS_NAVIGATION);
        
        // $this->db->where('test_id', $id);
        // $this->db->delete(TABLE_TEST_SETTINGS_SECURITY);
        
        // $this->db->where('test_id', $id);
        // $this->db->delete(TABLE_TEST_SETTINGS_MONITORING);
        
        // $this->db->where('test_id', $id);
        // $this->db->delete(TABLE_TEST_SETTINGS_UI);
        
        // // Delete test
        // $this->db->where('id', $id);
        // $this->db->update(TABLE_TESTS);

        $this->db->update(TABLE_TESTS, 
        [
            'is_active'=>0
        ],
        [
            'id' => $id,
        ]
        );
        
        // Complete transaction
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    public function get_test_navigation($id) {
        $this->db->where('test_id', $id);
        return $this->db->get(TABLE_TEST_SETTINGS_NAVIGATION)->row();
    }
    
    public function get_test_security($id) {
        $this->db->where('test_id', $id);
        return $this->db->get(TABLE_TEST_SETTINGS_SECURITY)->row();
    }
    
    public function get_test_monitoring($id) {
        $this->db->where('test_id', $id);
        return $this->db->get(TABLE_TEST_SETTINGS_MONITORING)->row();
    }
    
    public function get_test_ui($id) {
        $this->db->where('test_id', $id);
        return $this->db->get(TABLE_TEST_SETTINGS_UI)->row();
    }


    /**
 * Get all questions assigned to a specific test with ordering
 * 
 * @param int $test_id Test ID
 * @return array Array of test questions with details
 */
public function get_test_questions($test_id) {
    $this->db->select('tq.id as test_question_id, q.id, q.question_title as title, q.score, qt.type, tq.question_order, tq.section_id')
             ->from('test_questions tq')
             ->join('question_bank q', 'q.id = tq.question_id', 'left')
             ->join('question_types qt', 'qt.id = q.type', 'left')
             ->where('tq.test_id', $test_id)
             ->where('tq.is_active', 1)
             ->order_by('tq.question_order', 'ASC');
    
    $result = $this->db->get()->result_array();
    
    return $result;
}

/**
 * Get all available questions that are not in the specified test
 * 
 * @param int $test_id Test ID
 * @return array Array of available questions
 */
public function get_available_questions($test_id,$conditions = []) {
    // First get all questions that are already in the test
    $subquery = $this->db->select('question_id')
                         ->from(TABLE_TEST_QUESTIONS)
                         ->where('test_id', $test_id)
                         ->where('is_active', 1)
                         ->get_compiled_select();
    
    // Get all active questions that are not in the test
    $this->db->select('q.id, q.question_title as title, q.score, qt.type, qdl.level,q.tags as topics')
             ->from('question_bank q')
             ->join('question_types qt', 'qt.id = q.type', 'left')
             ->join('question_difficulty_level as qdl','q.difficulty_level = qdl.id','left')
             ->where('q.is_active', 1)             
             ->where_not_in('q.id', $subquery, false)
             ->order_by('q.id', 'DESC');

    if (!empty($conditions)) {
        foreach ($conditions as $key => $value) {
            $this->db->where($key, $value);
        }
    }
    
    $result = $this->db->get()->result_array();
    
    return $result;
}

/**
 * Get distinct question types for filtering
 * 
 * @return array Array of question types
 */
public function get_question_types() {
    $this->db->select('type')
             ->from(TABLE_QUESTION_TYPES)
             ->where('is_active', 1)
             ->order_by('type', 'ASC');
    
    $result = $this->db->get()->result_array();
    
    // Extract just the type values
    $types = array_column($result, 'type');
    
    return $types;
}

/**
 * Get the maximum order number for questions in a test
 * 
 * @param int $test_id Test ID
 * @return int Maximum order number or 0 if no questions
 */
public function get_max_question_order($test_id) {
    $this->db->select_max('question_order')
             ->from(TABLE_TEST_QUESTIONS)
             ->where('test_id', $test_id);
    
    $result = $this->db->get()->row();
    
    return $result ? $result->question_order : 0;
}

/**
 * Check if a question is already in a test
 * 
 * @param int $test_id Test ID
 * @param int $question_id Question ID
 * @return bool True if question is already in test, false otherwise
 */
public function is_question_in_test($test_id, $question_id) {
    $this->db->from(TABLE_TEST_QUESTIONS)
             ->where('test_id', $test_id)
             ->where('question_id', $question_id)
             ->where('is_active', 1);
    
    $count = $this->db->count_all_results();
    
    return $count > 0;
}

/**
 * Get total count of questions in a test
 * 
 * @param int $test_id Test ID
 * @return int Number of questions
 */
public function get_test_question_count($test_id) {
    $this->db->from(TABLE_TEST_QUESTIONS)
             ->where('test_id', $test_id)
             ->where('is_active', 1);
    
    return $this->db->count_all_results();
}

/**
 * Calculate total score of all questions in a test
 * 
 * @param int $test_id Test ID
 * @return int Total score
 */
public function calculate_test_total_score($test_id) {
    $this->db->select_sum('q.score')
             ->from('test_questions tq')
             ->join('question_bank q', 'q.id = tq.question_id', 'left')
             ->where('tq.test_id', $test_id)
             ->where('tq.is_active', 1);
    
    $result = $this->db->get()->row();
    
    return $result ? $result->score : 0;
}

/**
 * Get detailed question information
 * 
 * @param int $question_id Question ID
 * @return object Question details
 */
public function get_question_details($question_id) {
    $this->db->select('q.*, qt.type')
             ->from('question_bank q')
             ->join('question_types qt', 'qt.id = q.type', 'left')
             ->where('q.id', $question_id);
    
    return $this->db->get()->row();
}

/**
 * Update test questions ordering
 * 
 * @param int $test_id Test ID
 * @param array $question_orders Array of [test_question_id => order]
 * @param int $user_id User making the update
 * @return bool Success status
 */
public function update_question_orders($test_id, $question_orders, $user_id) {
    $this->db->trans_start();
    
    foreach ($question_orders as $test_question_id => $order) {
        $this->db->update(TABLE_TEST_QUESTIONS, 
            [
                'question_order' => $order,
                'updated_by' => $user_id
            ],
            [
                'id' => $test_question_id,
                'test_id' => $test_id
            ]
        );
    }
    
    $this->db->trans_complete();
    
    return $this->db->trans_status();
}

/**
 * Add a question to a test
 * 
 * @param int $test_id Test ID
 * @param int $question_id Question ID
 * @param int $user_id User adding the question
 * @return int|bool New test_question_id or false on failure
 */
public function add_question_to_test($test_id, $question_id, $user_id) {
    // Check if question is already in test
    if ($this->is_question_in_test($test_id, $question_id)) {
        return false;
    }
    
    // Get the next order number
    $max_order = $this->get_max_question_order($test_id);
    
    $data = [
        'test_id' => $test_id,
        'question_id' => $question_id,
        'question_order' => $max_order + 1,
        'is_active' => 1,
        'created_by' => $user_id
    ];
    
    $this->db->insert(TABLE_TEST_QUESTIONS, $data);
    
    return $this->db->insert_id();
}

/**
 * Remove a question from a test
 * 
 * @param int $test_question_id Test Question ID
 * @return bool Success status
 */
public function remove_question_from_test($test_question_id) {
    // Get test ID for later reordering
    $test_question = $this->db->get_where(TABLE_TEST_QUESTIONS, ['id' => $test_question_id])->row();
    
    if (!$test_question) {
        return false;
    }
    
    $test_id = $test_question->test_id;
    
    // Delete the question
    $result = $this->db->delete(TABLE_TEST_QUESTIONS, ['id' => $test_question_id]);
    
    if ($result) {
        // Reorder the remaining questions
        $this->reorder_test_questions($test_id);
        return true;
    }
    
    return false;
}

/**
 * Reorder questions after deletion
 * 
 * @param int $test_id Test ID
 * @return bool Success status
 */
private function reorder_test_questions($test_id) {
    // Get all questions for the test in current order
    $questions = $this->db->select('id')
                          ->from(TABLE_TEST_QUESTIONS)
                          ->where('test_id', $test_id)
                          ->where('is_active', 1)
                          ->order_by('question_order', 'ASC')
                          ->get()
                          ->result();
    
    $this->db->trans_start();
    
    $order = 1;
    foreach ($questions as $question) {
        $this->db->update(TABLE_TEST_QUESTIONS, 
            ['question_order' => $order], 
            ['id' => $question->id]
        );
        $order++;
    }
    
    $this->db->trans_complete();
    
    return $this->db->trans_status();
}

public function get_attempted_test_count($student_id, $test_id, $course_id, $module_id)
{
    // Get test info
    $test = $this->db_model->get_row("tests", ["id" => $test_id, 'is_active' => 1], "no_of_attempts");

    $max_attempts = (int)$test['no_of_attempts'];

    // Check if student already submitted
    $submission = $this->db_model->get_row("student_test_submission", [
        "student_id" => $student_id,
        "test_id" => $test_id,
        "course_id" => $course_id,
        "module_id" => $module_id
    ]);

    // If no previous submission → first attempt
    if (!$submission) {
        return [
            'status' => 'success',
            'message' => 'First attempt allowed',
            'delete' => false,
            'max_attempts' => $max_attempts,
            'attempted_count' => 0,
            'challenge_id' => null,
            'user_id' => null,
        ];
    }

    $current_attempts = (int)$submission['attempts'];

    // If reattempts allowed
    if ($current_attempts <= $max_attempts) {
        return [
            'status' => 'success',
            'message' => 'Re-attempt allowed',
            'delete' => true,
            'challenge_id' => $submission['challenge_id'],
            'user_id' => $submission['challenge_user_id'],
            'max_attempts' => $max_attempts,
            'attempted_count' => $current_attempts
        ];
    }

    // Re-attempts exceeded
    return [
        'status' => 'error',
        'message' => 'Maximum attempts reached',
        'delete' => false,
        'max_attempts' => $max_attempts,
        'attempted_count' => $current_attempts
    ];
}

/**
 * Section Management Methods
 */

/**
 * Get all sections for a test
 * 
 * @param int $test_id Test ID
 * @return array Array of sections
 */
public function get_test_sections($test_id) {
    // Get all active sections for this test
    // Since test_id was removed from test_sections, we can only find sections via question assignments
    
    // Get section IDs that have questions assigned to this test
    // Use GROUP BY instead of DISTINCT for better performance
    $this->db->select('section_id');
    $this->db->from(TABLE_TEST_QUESTIONS);
    $this->db->where('test_id', $test_id);
    $this->db->where('section_id IS NOT NULL');
    $this->db->group_by('section_id');
    $assigned_query = $this->db->get();
    
    // Use array_column to extract section IDs directly (no need to filter since query already excludes NULL)
    $assigned_section_ids = $assigned_query->num_rows() > 0 
        ? array_values(array_unique(array_column($assigned_query->result_array(), 'section_id')))
        : array();
    
    // Now get all sections that match our criteria
    $this->db->select('*');
    $this->db->from(TABLE_TEST_SECTIONS);
    $this->db->where('is_active', 1);
    
    // Build WHERE conditions: sections with questions OR default section
    $this->db->group_start();
    if (!empty($assigned_section_ids)) {
        $this->db->where_in('id', $assigned_section_ids);
    }
    $this->db->or_where('id', DEFAULT_SECTION_ID);
    $this->db->group_end();
    
    // Order by: default section first (id = 1), then others
    $this->db->order_by('id', 'ASC');
    
    return $this->db->get()->result_array();
}

/**
 * Get a specific section by ID
 * 
 * @param int $section_id Section ID
 * @return object Section details
 */
public function get_section($section_id) {
    $this->db->where('id', $section_id);
    // Note: Remove is_active check here to allow fetching default section even if inactive
    // Will check is_active when needed
    return $this->db->get(TABLE_TEST_SECTIONS)->row();
}

/**
 * Get test_id for a section by getting it from test_questions
 * 
 * @param int $section_id Section ID
 * @return int|null Test ID
 */
public function get_test_id_for_section($section_id) {
    $this->db->select('test_id');
    $this->db->from(TABLE_TEST_QUESTIONS);
    $this->db->where('section_id', $section_id);
    $this->db->limit(1);
    $result = $this->db->get()->row();
    return $result ? $result->test_id : null;
}

/**
 * Create a new section
 * 
 * @param array $data Section data (must include 'section_name')
 * @param int $user_id User creating the section
 * @return int|bool Section ID or false
 */
public function create_section($data, $user_id) {
    $section_data = [
        'section_name' => $data['section_name']
    ];
    
    $this->db->insert(TABLE_TEST_SECTIONS, $section_data);
    return $this->db->insert_id();
}

/**
 * Create default unassigned section for a test
 * This is automatically created when a test is created or when sections are enabled
 * 
 * @param int $test_id Test ID
 * @return int|bool Section ID or false
 */
public function create_default_unassigned_section($test_id) {
    // Check if default section already exists (id = 1)
    $existing = $this->db->where('id', DEFAULT_SECTION_ID)
        ->get(TABLE_TEST_SECTIONS)
        ->row();
    
    if ($existing) {
        return $existing->id;
    }
    
    // Create default section with id = 1
    // Note: This assumes the default section with id = 1 already exists in DB
    // If not, it will be created automatically by the database
    $section_data = [
        'section_name' => '$@#UNASSIGNED_QAS#@$'
    ];
    
    // Try to insert with id = 1, but if it fails, the section already exists
    $this->db->set('id', DEFAULT_SECTION_ID);
    $this->db->set($section_data);
    $this->db->insert(TABLE_TEST_SECTIONS);
    
    // If insert fails due to duplicate key, just return the existing id
    if ($this->db->insert_id() == 0) {
        return DEFAULT_SECTION_ID;
    }
    
    return $this->db->insert_id();
}

/**
 * Check if a section is the default unassigned section
 * 
 * @param mixed $section Section array, object, or section_id
 * @return bool True if default unassigned section
 */
public function is_default_unassigned_section($section) {
    // Accept either section array/object or section_id
    if (is_array($section)) {
        return isset($section['id']) && $section['id'] == DEFAULT_SECTION_ID;
    } elseif (is_object($section)) {
        return isset($section->id) && $section->id == DEFAULT_SECTION_ID;
    } elseif (is_numeric($section)) {
        // If section_id is provided, check if it equals DEFAULT_SECTION_ID
        return $section == DEFAULT_SECTION_ID;
    }
    return false;
}

/**
 * Update a section
 * 
 * @param int $section_id Section ID
 * @param array $data Section data
 * @param int $user_id User updating the section
 * @return bool Success status
 */
public function update_section($section_id, $data, $user_id) {
    $update_data = [
        'section_name' => $data['section_name']
    ];
    
    $this->db->where('id', $section_id);
    return $this->db->update(TABLE_TEST_SECTIONS, $update_data);
}

/**
 * Delete a section (soft delete)
 * 
 * @param int $section_id Section ID
 * @return bool Success status
 */
public function delete_section($section_id) {
    $this->db->trans_start();
    
    // Verify section exists
    $section = $this->db->get_where(TABLE_TEST_SECTIONS, ['id' => $section_id])->row();
    if (!$section) {
        $this->db->trans_complete();
        return false;
    }
    
    // Get default unassigned section (id = 1)
    $default_section = $this->db->where('id', DEFAULT_SECTION_ID)
        ->get(TABLE_TEST_SECTIONS)
        ->row();
    
    // Reassign questions to default section; do NOT delete section record
    if ($default_section) {
        $this->db->where('section_id', $section_id);
        $this->db->update(TABLE_TEST_QUESTIONS, ['section_id' => $default_section->id]);
    } else {
        // Fallback: set to NULL if no default section exists (shouldn't happen)
        $this->db->where('section_id', $section_id);
        $this->db->update(TABLE_TEST_QUESTIONS, ['section_id' => NULL]);
    }
    
    $this->db->trans_complete();
    return $this->db->trans_status();
}

/**
 * Get questions in a specific section
 * 
 * @param int $section_id Section ID
 * @return array Array of questions
 */
public function get_section_questions($section_id) {
    $this->db->select('tq.id as test_question_id, tq.question_id, q.question_title, q.score, tq.question_order');
    $this->db->from(TABLE_TEST_QUESTIONS . ' tq');
    $this->db->join(TABLE_QUESTION_BANK . ' q', 'q.id = tq.question_id', 'left');
    $this->db->where('tq.section_id', $section_id);
    $this->db->where('tq.is_active', 1);
    $this->db->order_by('tq.question_order', 'ASC');
    
    return $this->db->get()->result_array();
}

/**
 * Assign question to section
 * 
 * @param int $test_question_id Test Question ID
 * @param int $section_id Section ID
 * @param int $user_id User making the assignment
 * @return bool Success status
 */
public function assign_question_to_section($test_question_id, $section_id, $user_id) {
    $update_data = [
        'section_id' => $section_id,
        'updated_by' => $user_id
    ];
    
    $this->db->where('id', $test_question_id);
    return $this->db->update(TABLE_TEST_QUESTIONS, $update_data);
}

/**
 * Remove question from section
 * 
 * @param int $test_question_id Test Question ID
 * @param int $user_id User making the change
 * @return bool Success status
 */
public function remove_question_from_section($test_question_id, $user_id) {
    // Verify question exists
    $question = $this->db->get_where(TABLE_TEST_QUESTIONS, ['id' => $test_question_id])->row();
    if (!$question) {
        return false;
    }
    
    // Get default unassigned section (there's only one global default section)
    $default_section = $this->db->where('id', DEFAULT_SECTION_ID)
        ->get(TABLE_TEST_SECTIONS)
        ->row();
    
    $update_data = [
        'updated_by' => $user_id
    ];
    
    // Reassign to default section if it exists
    if ($default_section) {
        $update_data['section_id'] = $default_section->id;
    } else {
        $update_data['section_id'] = NULL;
    }
    
    $this->db->where('id', $test_question_id);
    return $this->db->update(TABLE_TEST_QUESTIONS, $update_data);
}

}
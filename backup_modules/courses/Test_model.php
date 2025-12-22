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
        $this->db->from(TABLE_PRIVATE_TESTS . ' as t');
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
        $this->db->from(TABLE_PRIVATE_TESTS);
        $this->db->order_by('created_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    public function get_test($id) {
        $this->db->where('id', $id);
        return $this->db->get(TABLE_PRIVATE_TESTS)->row();
    }
    
    public function create_test($data,$created_by) {
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
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'created_by' => $created_by,
        ];
        
        $this->db->insert(TABLE_PRIVATE_TESTS, $test_data);
        $test_id = $this->db->insert_id();
        
        if ($test_id) {
            // Insert navigation settings
            $nav_data = [
                'test_id' => $test_id,
                'next_test_id' => !empty($data['next_test_id']) ? $data['next_test_id'] : NULL,
                'prev_test_id' => !empty($data['prev_test_id']) ? $data['prev_test_id'] : NULL,
                'enable_finish_button' => isset($data['enable_finish_button']) ? 1 : 0,
                'show_next_on_last_question' => isset($data['show_next_on_last_question']) ? 1 : 0
            ];
            $this->db->insert(TABLE_PRIVATE_TEST_SETTINGS_NAVIGATION, $nav_data);
            
            // Insert security settings
            $security_data = [
                'test_id' => $test_id,
                'disable_copy_paste' => isset($data['disable_copy_paste']) ? 1 : 0,
                'shuffle_questions' => isset($data['shuffle_questions']) ? 1 : 0,
                'plagiarism_check' => isset($data['plagiarism_check']) ? 1 : 0,
                'force_full_screen' => isset($data['force_full_screen']) ? 1 : 0
            ];
            $this->db->insert(TABLE_PRIVATE_TEST_SETTINGS_SECURITY, $security_data);
            
            // Insert monitoring settings
            $monitoring_data = [
                'test_id' => $test_id,
                'capture_tab_change' => isset($data['capture_tab_change']) ? 1 : 0,
                'capture_user_image' => isset($data['capture_user_image']) ? 1 : 0,
                'enable_time_tracking' => isset($data['enable_time_tracking']) ? 1 : 0,
                'record_user_session' => isset($data['record_user_session']) ? 1 : 0
            ];
            $this->db->insert(TABLE_PRIVATE_TEST_SETTINGS_MONITORING, $monitoring_data);
            
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
                'hide_problem_menu' => isset($data['hide_problem_menu']) ? 1 : 0
            ];
            $this->db->insert(TABLE_PRIVATE_TEST_SETTINGS_UI, $ui_data);
        }
        
        // Complete transaction
        $this->db->trans_complete();
        
        return $this->db->trans_status() ? $test_id : false;
    }
    
    public function update_test($id, $data) {
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
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'updated_by' => $created_by
        ];
        
        $this->db->where('id', $id);
        $this->db->update(TABLE_PRIVATE_TESTS, $test_data);
        
        // Update navigation settings
        $nav_data = [
            'next_test_id' => !empty($data['next_test_id']) ? $data['next_test_id'] : NULL,
            'prev_test_id' => !empty($data['prev_test_id']) ? $data['prev_test_id'] : NULL,
            'enable_finish_button' => isset($data['enable_finish_button']) ? 1 : 0,
            'show_next_on_last_question' => isset($data['show_next_on_last_question']) ? 1 : 0
        ];
        $this->db->where('test_id', $id);
        $this->db->update(TABLE_PRIVATE_TEST_SETTINGS_NAVIGATION, $nav_data);
        
        // Update security settings
        $security_data = [
            'disable_copy_paste' => isset($data['disable_copy_paste']) ? 1 : 0,
            'shuffle_questions' => isset($data['shuffle_questions']) ? 1 : 0,
            'plagiarism_check' => isset($data['plagiarism_check']) ? 1 : 0,
            'force_full_screen' => isset($data['force_full_screen']) ? 1 : 0
        ];
        $this->db->where('test_id', $id);
        $this->db->update(TABLE_PRIVATE_TEST_SETTINGS_SECURITY, $security_data);
        
        // Update monitoring settings
        $monitoring_data = [
            'capture_tab_change' => isset($data['capture_tab_change']) ? 1 : 0,
            'capture_user_image' => isset($data['capture_user_image']) ? 1 : 0,
            'enable_time_tracking' => isset($data['enable_time_tracking']) ? 1 : 0,
            'record_user_session' => isset($data['record_user_session']) ? 1 : 0
        ];
        $this->db->where('test_id', $id);
        $this->db->update(TABLE_PRIVATE_TEST_SETTINGS_MONITORING, $monitoring_data);
        
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
            'hide_problem_menu' => isset($data['hide_problem_menu']) ? 1 : 0
        ];
        $this->db->where('test_id', $id);
        $this->db->update(TABLE_PRIVATE_TEST_SETTINGS_UI, $ui_data);
        
        // Complete transaction
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    public function delete_test($id) {
        // Start transaction
        $this->db->trans_start();
        
        // Delete test settings
        $this->db->where('test_id', $id);
        $this->db->delete(TABLE_PRIVATE_TEST_SETTINGS_NAVIGATION);
        
        $this->db->where('test_id', $id);
        $this->db->delete(TABLE_PRIVATE_TEST_SETTINGS_SECURITY);
        
        $this->db->where('test_id', $id);
        $this->db->delete(TABLE_PRIVATE_TEST_SETTINGS_MONITORING);
        
        $this->db->where('test_id', $id);
        $this->db->delete(TABLE_PRIVATE_TEST_SETTINGS_UI);
        
        // Delete test
        $this->db->where('id', $id);
        $this->db->delete(TABLE_PRIVATE_TESTS);
        
        // Complete transaction
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    public function get_test_navigation($id) {
        $this->db->where('test_id', $id);
        return $this->db->get(TABLE_PRIVATE_TEST_SETTINGS_NAVIGATION)->row();
    }
    
    public function get_test_security($id) {
        $this->db->where('test_id', $id);
        return $this->db->get(TABLE_PRIVATE_TEST_SETTINGS_SECURITY)->row();
    }
    
    public function get_test_monitoring($id) {
        $this->db->where('test_id', $id);
        return $this->db->get(TABLE_PRIVATE_TEST_SETTINGS_MONITORING)->row();
    }
    
    public function get_test_ui($id) {
        $this->db->where('test_id', $id);
        return $this->db->get(TABLE_PRIVATE_TEST_SETTINGS_UI)->row();
    }


    /**
 * Get all questions assigned to a specific test with ordering
 * 
 * @param int $test_id Test ID
 * @return array Array of test questions with details
 */
public function get_test_questions($test_id) {
    $this->db->select('tq.id as test_question_id, q.id, q.question_title as title, q.score, qt.type, tq.question_order')
             ->from('private_test_questions tq')
             ->join('private_question_bank q', 'q.id = tq.question_id', 'left')
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
public function get_available_questions($test_id) {
    // First get all questions that are already in the test
    $subquery = $this->db->select('question_id')
                         ->from(TABLE_PRIVATE_TEST_QUESTIONS)
                         ->where('test_id', $test_id)
                         ->where('is_active', 1)
                         ->get_compiled_select();
    
    // Get all active questions that are not in the test
    $this->db->select('q.id, q.question_title as title, q.score, qt.type')
             ->from('private_question_bank q')
             ->join('question_types qt', 'qt.id = q.type', 'left')
             ->where('q.is_active', 1)
             ->where_not_in('q.id', $subquery, false)
             ->order_by('q.id', 'DESC');
    
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
             ->from(TABLE_QUESTION_TYPE)
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
             ->from(TABLE_PRIVATE_TEST_QUESTIONS)
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
    $this->db->from(TABLE_PRIVATE_TEST_QUESTIONS)
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
    $this->db->from(TABLE_PRIVATE_TEST_QUESTIONS)
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
             ->from('private_test_questions tq')
             ->join('private_question_bank q', 'q.id = tq.question_id', 'left')
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
             ->from('private_question_bank q')
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
        $this->db->update(TABLE_PRIVATE_TEST_QUESTIONS, 
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
    
    $this->db->insert(TABLE_PRIVATE_TEST_QUESTIONS, $data);
    
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
    $test_question = $this->db->get_where(TABLE_PRIVATE_TEST_QUESTIONS, ['id' => $test_question_id])->row();
    
    if (!$test_question) {
        return false;
    }
    
    $test_id = $test_question->test_id;
    
    // Delete the question
    $result = $this->db->delete(TABLE_PRIVATE_TEST_QUESTIONS, ['id' => $test_question_id]);
    
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
                          ->from(TABLE_PRIVATE_TEST_QUESTIONS)
                          ->where('test_id', $test_id)
                          ->where('is_active', 1)
                          ->order_by('question_order', 'ASC')
                          ->get()
                          ->result();
    
    $this->db->trans_start();
    
    $order = 1;
    foreach ($questions as $question) {
        $this->db->update(TABLE_PRIVATE_TEST_QUESTIONS, 
            ['question_order' => $order], 
            ['id' => $question->id]
        );
        $order++;
    }
    
    $this->db->trans_complete();
    
    return $this->db->trans_status();
}

}
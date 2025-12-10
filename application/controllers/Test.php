<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->common->check_user_session();
        $this->user_session = $this->session->userdata( 'owner' );
        $this->load->model('test_model');
        $this->load->model('db_model');
 
    }
    
    public function view() {
        // Get filter parameters from URL
        $module_id = $this->input->get('module');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        
        // Build conditions array for filtering
        $conditions = [];
        
        if (!empty($module_id)) {
            $conditions['module_id'] = $module_id;
        }
        
        if (!empty($start_date) && !empty($end_date)) {
            // Adding date range filter - format dates as needed for your database
            $conditions['created_at >='] = date('Y-m-d 00:00:00', strtotime($start_date));
            $conditions['created_at <='] = date('Y-m-d 23:59:59', strtotime($end_date));
        }
        
        // Pass conditions to model
        $data['tests'] = $this->test_model->get_filtered_tests($conditions);
        $data['title'] = 'Manage Tests';
        $data['active_menu'] = 'tests';
        
        // Get all modules for the dropdown
        $data['modules'] = $this->db_model->get_all(TABLE_TEST_MODULES, ['is_active' => 1]);
        
        $this->common->load_view('test/view', $data);
    }
    
 
    
    public function create() {
        $data['title'] = 'Create New Test';
        $data['active_menu'] = 'tests';
        $data['modules'] = $this->db_model->get_all(TABLE_TEST_MODULES, ['is_active' => 1]);
        
        if ($this->input->post()) {
            $this->form_validation->set_rules('title', 'Title', 'required');
            $this->form_validation->set_rules('instructions', 'Instructions', 'required');
            $this->form_validation->set_rules('duration', 'Duration', 'required|numeric|greater_than_equal_to[0]');
            
            if ($this->form_validation->run() === TRUE) {
                $test_id = $this->test_model->create_test($this->input->post(),$this->user_session['id']);
                
                if ($test_id) {
                    $this->session->set_flashdata('message', ['success', 'Test created successfully']);
                    redirect('test/view');
                } else {
                    $this->session->set_flashdata('message', ['danger', 'Error creating test']);
                }
            }
        }
        
        $this->common->load_view('test/add', $data);
    }
    
    public function edit($id) {
        $data['title'] = 'Edit Test';
        $data['active_menu'] = 'tests';
        $data['modules'] = $this->db_model->get_all(TABLE_TEST_MODULES, ['is_active' => 1]);
        
        $data['test'] = $this->test_model->get_test($id);
        
        if (!$data['test']) {
            $this->session->set_flashdata('message', ['danger', 'Test not found']);
            redirect('test');
        }
        
        // Get test settings and merge them into the test data
        $settings = [
            'navigation' => $this->test_model->get_test_navigation($id),
            'security' => $this->test_model->get_test_security($id),
            'monitoring' => $this->test_model->get_test_monitoring($id),
            'ui' => $this->test_model->get_test_ui($id)
        ];
        
        // Merge settings into the main test data
        foreach ($settings as $key => $value) {
            if ($value) {
                foreach ($value as $k => $v) {
                    $data['test']->{$k} = $v;
                }
            }
        }
    
        if ($this->input->post()) {
            $this->form_validation->set_rules('title', 'Title', 'required');
            $this->form_validation->set_rules('instructions', 'Instructions', 'required');
            $this->form_validation->set_rules('duration', 'Duration', 'required|numeric|greater_than_equal_to[0]');
            
            if ($this->form_validation->run() === TRUE) {
                $success = $this->test_model->update_test($id, $this->input->post(), $this->user_session['id']);
                
                if ($success) {
                    $this->session->set_flashdata('message', ['success', 'Test updated successfully']);
                    redirect('test/view');
                } else {
                    $this->session->set_flashdata('message', ['danger', 'Error updating test']);
                }
            }
        }
    
        $this->common->load_view('test/add', $data);
    }
    public function delete($id) {
        if (!$id) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid test ID'
            ];
            echo json_encode($response);
            return;
        }
        
        // Check if test exists
        $test = $this->test_model->get_test($id);
        if (!$test) {
            $response = [
                'status' => 'error',
                'message' => 'Test not found'
            ];
            echo json_encode($response);
            return;
        }
        
        // Delete test and its related settings
        $result = $this->test_model->delete_test($id);
        
        if ($result) {
            $response = [
                'status' => 'success',
                'message' => 'Test deleted successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error deleting test'
            ];
        }
        
        echo json_encode($response);
    }

    public function questions($test_id) {
        // Check if test exists
        $test = $this->test_model->get_test($test_id);
        if (!$test) {
            $this->session->set_flashdata('message', ['danger', 'Test not found']);
            redirect('test/view');
        }
        
        // Get test questions with order
        $test_questions = $this->test_model->get_test_questions($test_id);
        
        // Get all available questions that are not in the test
        $available_questions = $this->test_model->get_available_questions($test_id);
        
        // Get question types for filtering
        $question_types = $this->test_model->get_question_types();
        
        $data = [
            'title' => 'Test Questions',
            'active_menu' => 'tests',
            'test' => $test,
            'test_questions' => $test_questions,
            'available_questions' => $available_questions,
            'question_types' => $question_types
        ];
        
        $this->common->load_view('test/questions', $data);
    }
    
    public function add_question() {
        // Check if this is an AJAX request
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        
        $test_id = $this->input->post('test_id');
        $question_id = $this->input->post('question_id');
        
        // Validate inputs
        if (!$test_id || !$question_id) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ];
            echo json_encode($response);
            return;
        }
        
        // Check if test exists
        $test = $this->test_model->get_test($test_id);
        if (!$test) {
            $response = [
                'status' => 'error',
                'message' => 'Test not found'
            ];
            echo json_encode($response);
            return;
        }
        
        // Check if question exists
        $question = $this->db_model->get_where(TABLE_QUESTION_BANK, ['id' => $question_id]);
        if (!$question) {
            $response = [
                'status' => 'error',
                'message' => 'Question not found'
            ];
            echo json_encode($response);
            return;
        }
        
        // Check if question is already added to the test
        $existing = $this->db_model->get_where(TABLE_PRIVATE_TEST_QUESTIONS, [
            'test_id' => $test_id,
            'question_id' => $question_id
        ]);
        
        if ($existing) {
            $response = [
                'status' => 'error',
                'message' => 'Question is already added to this test'
            ];
            echo json_encode($response);
            return;
        }
        
        // Get the current highest order
        $max_order = $this->db_model->get_max(TABLE_PRIVATE_TEST_QUESTIONS, 'question_order', ['test_id' => $test_id]);
        $new_order = $max_order ? $max_order + 1 : 1;
        
        // Add question to test
        $data = [
            'test_id' => $test_id,
            'question_id' => $question_id,
            'question_order' => $new_order,
            'is_active' => 1,
            'created_by' => $this->user_session['id']
        ];
        
        $result = $this->db_model->insert(TABLE_PRIVATE_TEST_QUESTIONS, $data);
        
        if ($result) {
            $response = [
                'status' => 'success',
                'message' => 'Question added successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to add question'
            ];
        }
        
        echo json_encode($response);
    }
    
    public function add_questions() {
        // Check if this is an AJAX request
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        
        $test_id = $this->input->post('test_id');
        $question_ids = $this->input->post('question_ids');
        
        // Validate inputs
        if (!$test_id || !$question_ids || !is_array($question_ids)) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ];
            echo json_encode($response);
            return;
        }
        
        // Check if test exists
        $test = $this->test_model->get_test($test_id);
        if (!$test) {
            $response = [
                'status' => 'error',
                'message' => 'Test not found'
            ];
            echo json_encode($response);
            return;
        }
        
        // Get the current highest order
        $max_order = $this->db_model->get_max(TABLE_PRIVATE_TEST_QUESTIONS, 'question_order', ['test_id' => $test_id]);
        $current_order = $max_order ? $max_order + 1 : 1;
        
        // Start transaction
        $this->db->trans_start();
        
        $success_count = 0;
        $already_added = 0;
        
        foreach ($question_ids as $question_id) {
            // Check if question exists
            $question = $this->db_model->get_where(TABLE_QUESTION_BANK, ['id' => $question_id]);
            if (!$question) {
                continue;
            }
            
            // Check if question is already added to the test
            $existing = $this->db_model->get_where(TABLE_PRIVATE_TEST_QUESTIONS, [
                'test_id' => $test_id,
                'question_id' => $question_id
            ]);
            
            if ($existing) {
                $already_added++;
                continue;
            }
            
            // Add question to test
            $data = [
                'test_id' => $test_id,
                'question_id' => $question_id,
                'question_order' => $current_order,
                'is_active' => 1,
                'created_by' => $this->user_session['id']
            ];
            
            $result = $this->db_model->insert(TABLE_PRIVATE_TEST_QUESTIONS, $data);
            
            if ($result) {
                $success_count++;
                $current_order++;
            }
        }
        
        // Complete transaction
        $this->db->trans_complete();
        
        if ($this->db->trans_status() && $success_count > 0) {
            $message = $success_count . ' question(s) added successfully';
            if ($already_added > 0) {
                $message .= '. ' . $already_added . ' question(s) were already in the test.';
            }
            
            $response = [
                'status' => 'success',
                'message' => $message
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to add questions'
            ];
        }
        
        echo json_encode($response);
    }
    
    public function remove_question() {
        // Check if this is an AJAX request
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        
        $test_question_id = $this->input->post('test_question_id');
        
        // Validate inputs
        if (!$test_question_id) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ];
            echo json_encode($response);
            return;
        }
        
        // Get the test_id directly with a safe query approach
        $this->db->select('test_id');
        $this->db->where('id', $test_question_id);
        $query = $this->db->get(TABLE_PRIVATE_TEST_QUESTIONS);
        $test_question_row = $query->row();
        
        if (!$test_question_row) {
            $response = [
                'status' => 'error',
                'message' => 'Question not found in test'
            ];
            echo json_encode($response);
            return;
        }
        
        // Store the test_id
        $test_id = $test_question_row->test_id;
        
        // Remove question from test
        $result = $this->db_model->delete(TABLE_PRIVATE_TEST_QUESTIONS, ['id' => $test_question_id]);
        
        if ($result) {
            // Reorder remaining questions
            $this->reorder_test_questions($test_id);
            
            $response = [
                'status' => 'success',
                'message' => 'Question removed successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to remove question'
            ];
        }
        
        echo json_encode($response);
    }
    public function save_question_order() {
        // Check if this is an AJAX request
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        
        $test_id = $this->input->post('test_id');
        $question_order = json_decode($this->input->post('question_order'), true);
        
        // Validate inputs
        if (!$test_id || !$question_order || !is_array($question_order)) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ];
            echo json_encode($response);
            return;
        }
        
        // Check if test exists
        $test = $this->test_model->get_test($test_id);
        if (!$test) {
            $response = [
                'status' => 'error',
                'message' => 'Test not found'
            ];
            echo json_encode($response);
            return;
        }
        
        // Start transaction
        $this->db->trans_start();
        
        $success_count = 0;
        
        foreach ($question_order as $item) {
            $test_question_id = $item['test_question_id'];
            $order = $item['order'];
            
            // Update question order
            $result = $this->db_model->update(
                TABLE_PRIVATE_TEST_QUESTIONS, 
                ['question_order' => $order, 'updated_by' => $this->user_session['id']], 
                ['id' => $test_question_id, 'test_id' => $test_id]
            );
            
            if ($result) {
                $success_count++;
            }
        }
        
        // Complete transaction
        $this->db->trans_complete();
        
        if ($this->db->trans_status() && $success_count > 0) {
            $response = [
                'status' => 'success',
                'message' => 'Question order saved successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to update question order'
            ];
        }
        
        echo json_encode($response);
    }

    /**
 * Helper method to reorder questions after removal
 *
 * @param int $test_id Test ID
 * @return bool Success status
 */
private function reorder_test_questions($test_id) {
    // Get all questions for this test, ordered by current order
    $questions = $this->db->select('id')
                        ->from(TABLE_PRIVATE_TEST_QUESTIONS)
                        ->where('test_id', $test_id)
                        ->where('is_active', 1)
                        ->order_by('question_order', 'ASC')
                        ->get()
                        ->result();
    
    // Reorder questions
    $order = 1;
    foreach ($questions as $question) {
        $this->db_model->update(
            TABLE_PRIVATE_TEST_QUESTIONS,
            ['question_order' => $order, 'updated_by' => $this->user_session['id']],
            ['id' => $question->id]
        );
        $order++;
    }
    
    return true;
}
}
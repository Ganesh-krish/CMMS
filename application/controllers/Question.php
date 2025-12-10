<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Question extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->common->check_user_session();
        $this->user_session = $this->session->userdata( 'owner' );
    }




    public function upload_image() {
        // Check if this is an AJAX request
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Direct access not allowed']);
            return;
        }
        
        // Set up the upload configuration
        $config['upload_path'] = './uploads/question_images/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png|svg';
        $config['max_size'] = 2048; // 2MB max
        $config['encrypt_name'] = TRUE; // For security, rename the file
        
        // Make sure the upload directory exists
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }
        
        // Load the upload library
        $this->load->library('upload', $config);
        
        // Attempt to upload the file
        if (!$this->upload->do_upload('image')) {
            // Upload failed
            $error = $this->upload->display_errors('', '');
            echo json_encode(['success' => false, 'message' => $error]);
        } else {
            // Upload successful
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];
            
            // Create the URL for the uploaded file
            $file_url = base_url('uploads/question_images/' . $file_name);
            
            // Return the success response with the file URL
            echo json_encode([
                'success' => true,
                'file_url' => $file_url,
                'file_name' => $file_name
            ]);
        }
    }


    public function view() {

        $difficulty_level = $this->input->get( 'difficulty_level' );
        $tags = $this->input->get( 'tags' );

        $conditions = [
            'is_active' => true
        ];

        if ( !empty( $difficulty_level ) ) {
            $conditions[ 'difficulty_level' ] = $difficulty_level;
        }
        if ( !empty( $tags ) ) {
            $conditions[ 'tags LIKE' ] = '%' . $tags . '%';
        }



        $data[ 'questions' ] = $this->db_model->get_all(
            TABLE_QUESTION_BANK,
            $conditions
        );
                // Get all question types
        $data['question_types'] = $this->db_model->get_all(
            TABLE_QUESTION_TYPE,
            ['is_active' => 1]
        );
        
        // Get all question subtypes
        $data['question_sub_types'] = $this->db_model->get_all(
            TABLE_QUESTION_SUB_TYPES,
            ['is_active' => 1]
        );
        
        // Get difficulty levels
        $data['difficulty_levels'] = $this->db_model->get_all(
            TABLE_QUESTION_DIFFICULTY_LEVEL,
            ['is_active' => 1]
        );

        $difficulty_levels = $data['difficulty_levels'];

        $difficulty_level_map = [];

        foreach ($difficulty_levels as $difficulty_level) {
            $difficulty_level_map[$difficulty_level['id']] = $difficulty_level['level'];
        }

        

        foreach ( $data[ 'questions' ] as &$question ) {
            $question['difficulty_level'] = $difficulty_level_map[$question['difficulty_level']];
            $question[ 'options' ] = $this->db_model->get_all(
                TABLE_ANSWER_OPTIONS,
                [
                    'question_id' => $question[ 'id' ],
                    'is_active' => true
                ]
            );
        }

        $tags_result = $this->db_model->get_all( TABLE_QUESTION_BANK, [ 'is_active' => true ] );
        $all_tags = [];

        foreach ( $tags_result as $row ) {
            if ( !empty( $row[ 'tags' ] ) ) {
                $tags_array = explode( ',', $row[ 'tags' ] );
                $all_tags = array_merge( $all_tags, $tags_array );
            }
        }

        $data[ 'tags' ] = array_values( array_unique( array_filter( $all_tags ) ) );
        $data[ 'question_types' ] = $this->db_model->get_all( TABLE_QUESTION_TYPE, [ 'is_active' => true ] );
  
        // $data[ 'topics' ] = $this->db_model->get_distinct( TABLE_QUESTION_BANK, 'topic', [ 'is_active' => true,  ] );
        $data[ 'url' ] = $this->uri->segment( 1 );
        $data['samplePdfurl'] = $this->uri->segment(3) . '/SampleCsv/download_sample_csv_questions';
        $this->common->load_view( 'question_bank/view', $data );
    }

    public function add($id = null) {
        $data = array();
        
        if (!empty($id)) {
            // Edit mode - fetch existing question data
            $question = $this->db_model->get_by_id(TABLE_QUESTION_BANK, $id);
            
            if (!$question) {
                $this->session->set_flashdata('message', array('danger', 'Question not found!'));
                redirect($this->uri->segment(1).'/view');
            }
            
            $question['options'] = $this->db_model->get_all(
                TABLE_ANSWER_OPTIONS,
                [
                    'question_id' => $id,
                    'is_active' => 1
                ]
            );
            
            // Fetch test cases if it's a CODE question
            if ($question['type'] == 2) {
                $question['test_cases'] = $this->db_model->get_all(
                    TABLE_QUESTION_TEST_CASES,
                    [
                        'question_id' => $id
                    ]
                );
            }
            
            $data['question'] = $question;
        }
        
        // Get all question types
        $data['question_types'] = $this->db_model->get_all(
            TABLE_QUESTION_TYPE,
            ['is_active' => 1]
        );
        
        // Get all question subtypes
        $data['question_sub_types'] = $this->db_model->get_all(
            TABLE_QUESTION_SUB_TYPES,
            ['is_active' => 1]
        );
        
        // Get difficulty levels
        $data['difficulty_levels'] = $this->db_model->get_all(
            TABLE_QUESTION_DIFFICULTY_LEVEL,
            ['is_active' => 1]
        );
        
        // Get tags
        $tags_result = $this->db_model->get_all(TABLE_QUESTION_BANK, ['is_active' => 1]);
        $all_tags = [];
        
        foreach ($tags_result as $row) {
            if (!empty($row['tags'])) {
                $tags_array = explode(',', $row['tags']);
                $all_tags = array_merge($all_tags, $tags_array);
            }
        }
        
        $data['tags'] = array_values(array_unique(array_filter($all_tags)));
        $data['url'] = $this->uri->segment(1);
        
        $this->common->load_view('question_bank/add', $data);
    }

    public function edit( $question_id = null ) {

        if ( !$question_id ) {

            echo json_encode( array( 'status'=> 'error', 'message' => 'Question not found' ) );
            return;
        }

        $question = $this->db_model->get_row(
            TABLE_QUESTION_BANK,
            [
                'id' => $question_id
            ],
        );

        $options = $this->db_model->get_all(
            TABLE_ANSWER_OPTIONS,
            [
                'question_id' => $question_id,
                'is_active' => true
            ]
        );

        $data = [
            'question' => $question
        ];

        $data[ 'question' ]['test_cases'] = $this->db_model->get_all(
            TABLE_QUESTION_TEST_CASES,
            [
                'question_id' => $question_id
                ]
        );

        // Get all question types
        $data['question_types'] = $this->db_model->get_all(
            TABLE_QUESTION_TYPE,
            ['is_active' => 1]
        );
        
        // Get all question subtypes
        $data['question_sub_types'] = $this->db_model->get_all(
            TABLE_QUESTION_SUB_TYPES,
            ['is_active' => 1]
        );
        

        $data[ 'question' ][ 'options' ] = $options;

        $data[ 'url' ] = $this->uri->segment( 1 );
        $data[ 'question_types' ] = $this->db_model->get_all( TABLE_QUESTION_TYPE );

        $this->common->load_view( 'question_bank/add', $data );
    }

/**
 * Create a new question
 */
public function create() {
    // Check if it's an AJAX request
    if (!$this->input->is_ajax_request()) {
        show_error('No direct script access allowed');
        return;
    }
    
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (empty($data)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data received']);
        return;
    }
    
    // Start transaction
    $this->db->trans_start();
    
    // Insert question
    $question_data = [
        'question_title' => $data['question']['question_title'],
        'question_content' => $data['question']['question_content'],
        'type' => $data['question']['type'],
        'sub_type' => $data['question']['sub_type'],
        'score' => $data['question']['score'],
        'difficulty_level' => $data['question']['difficulty_level'],
        'tags' => $data['question']['tags'],
        'created_by'   => $this->user_session['id'],
        'is_active' => 1
    ];
    
    // If it's a CODE question, save selected languages
    if ($data['question']['type'] == 2 && !empty($data['question']['selected_languages'])) {
        $question_data['selected_languages'] = $data['question']['selected_languages'];
    }
    
    // If it's a CODE question, save ignore_case flag
    if ($data['question']['type'] == 2 && isset($data['question']['ignore_case'])) {
        $question_data['ignore_case'] = $data['question']['ignore_case'];
    }
    
    // If it's a FILL IN THE BLANK question, save answer
    if ($data['question']['type'] == 3 && !empty($data['question']['fill_blank_answer'])) {
        $question_data['fill_blank_answer'] = $data['question']['fill_blank_answer'];
    }
    
    $question_id = $this->db_model->insert(TABLE_QUESTION_BANK, $question_data);
    
    // Insert options for MCQ
    if ($data['question']['type'] == 1 && !empty($data['options'])) {
        foreach ($data['options'] as $option) {
            $option_data = [
                'question_id' => $question_id,
                'option_text' => $option['option_text'],
                'is_correct' => $option['is_correct'],
                'is_active' => 1
            ];
            
            $this->db_model->insert(TABLE_ANSWER_OPTIONS, $option_data);
        }
    }
    
    // Insert test cases for CODE questions
    if ($data['question']['type'] == 2 && !empty($data['test_cases'])) {
        foreach ($data['test_cases'] as $test_case) {
            $test_case_data = [
                'question_id' => $question_id,
                'input' => $test_case['input'],
                'output' => $test_case['output'],
                'visibility' => $test_case['visibility']
            ];
            
            $this->db_model->insert(TABLE_QUESTION_TEST_CASES, $test_case_data);
        }
    }
    
    // Complete transaction
    $this->db->trans_complete();
    
    if ($this->db->trans_status() === FALSE) {
        echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Question saved successfully!']);
    }
}

/**
 * Update an existing question
 */
public function update($id) {
    // Check if it's an AJAX request
    if (!$this->input->is_ajax_request()) {
        show_error('No direct script access allowed');
        return;
    }
    
    // Check if question exists
    $question = $this->db_model->get_where(TABLE_QUESTION_BANK, [
        'id' => $id]
    );
    if (!$question) {
        echo json_encode(['status' => 'error', 'message' => 'Question not found']);
        return;
    }
    
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (empty($data)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data received']);
        return;
    }
    
    // Start transaction
    $this->db->trans_start();
    
    // Update question
    $question_data = [
        'question_title' => $data['question']['question_title'],
        'question_content' => $data['question']['question_content'],
        'type' => $data['question']['type'],
        'sub_type' => $data['question']['sub_type'],
        'score' => $data['question']['score'],
        'difficulty_level' => $data['question']['difficulty_level'],
        'tags' => $data['question']['tags']
    ];
    
    // If it's a CODE question, update selected languages
    if ($data['question']['type'] == 2 && isset($data['question']['selected_languages'])) {
        $question_data['selected_languages'] = $data['question']['selected_languages'];
    }
    
    // If it's a CODE question, update ignore_case flag
    if ($data['question']['type'] == 2 && isset($data['question']['ignore_case'])) {
        $question_data['ignore_case'] = $data['question']['ignore_case'];
    }
    
    // If it's a FILL IN THE BLANK question, update answer
    if ($data['question']['type'] == 3 && isset($data['question']['fill_blank_answer'])) {
        $question_data['fill_blank_answer'] = $data['question']['fill_blank_answer'];
    }
    
    $this->db_model->update(TABLE_QUESTION_BANK, $question_data, ['id' => $id]);
    
    // Handle options for MCQ
    if ($data['question']['type'] == 1) {
        // Delete existing options
        $this->db_model->delete(TABLE_ANSWER_OPTIONS, ['question_id' => $id]);
        
        // Insert new options
        if (!empty($data['options'])) {
            foreach ($data['options'] as $option) {
                $option_data = [
                    'question_id' => $id,
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'],
                    'is_active' => 1
                ];
                
                $this->db_model->insert(TABLE_ANSWER_OPTIONS, $option_data);
            }
        }
    }
    
    // Handle test cases for CODE questions
    if ($data['question']['type'] == 2) {
        // Delete existing test cases
        $this->db_model->delete(TABLE_QUESTION_TEST_CASES, ['question_id' => $id]);
        
        // Insert new test cases
        if (!empty($data['test_cases'])) {
            foreach ($data['test_cases'] as $test_case) {
                $test_case_data = [
                    'question_id' => $id,
                    'input' => $test_case['input'],
                    'output' => $test_case['output'],
                    'visibility' => $test_case['visibility']
                ];
                
                $this->db_model->insert(TABLE_QUESTION_TEST_CASES, $test_case_data);
            }
        }
    }
    
    // Complete transaction
    $this->db->trans_complete();
    
    if ($this->db->trans_status() === FALSE) {
        echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Question updated successfully!']);
    }
}


    public function delete( $question_id ) {
        if ( !$this->input->is_ajax_request() ) {
            show_404();
        }

        if ( empty( $question_id ) ) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid question ID'
            ];
            echo json_encode( $response );
            return;
        }

        $this->db->trans_begin();

        $this->db_model->update(
            TABLE_QUESTION_BANK,
            [ 'is_active' => false ],
            [ 'id' => $question_id ],
        );

        if ( $this->db->affected_rows() === -1 ) {
            $this->db->trans_rollback();
            $response = [
                'status' => 'error',
                'message' => 'Failed to delete question'
            ];
            echo json_encode( $response );
            return;
        }

        if ( $this->db->trans_status() === FALSE ) {
            $this->db->trans_rollback();
            $response = [
                'status' => 'error',
                'message' => 'Database transaction failed'
            ];
        } else {
            $this->db->trans_commit();
            $response = [
                'status' => 'success',
                'message' => 'Question deleted successfully'
            ];
        }

        echo json_encode( $response );
    }

    public function bulk_add_questions()
    {
        $file_path = $this->common->upload_csv();

        if (!$file_path) {
            echo json_encode(["status" => "error", "message" => "File upload failed!"]);
            exit();
        }

        $csv_result = $this->common->read_csv(FCPATH . "assets/uploads/CSV/$file_path");

        $header = $csv_result['headers'];
        $csv_data = $csv_result['values'];

        $total_rows = count($csv_data);
        $successful_inserts = 0;
        $failed_rows = [];
        
        $batch_size = 1000;
        $batches = array_chunk($csv_data, $batch_size);
        
        foreach ($batches as $batch_index => $batch) {
            $this->db->trans_begin();
            
            foreach ($batch as $row_index => $row) {
                try {
                    $absolute_row = ($batch_index * $batch_size) + $row_index + 2; 
                    
                    if (count($row) < count($header)) {
                        throw new Exception("Row {$absolute_row} has insufficient columns");
                    }

                    // Create associative array mapping headers to values
                    $row_data = array_combine($header, $row);
                    
                    // Extract and validate question data
                    $question_content = trim($row_data['question_content']);
                    if (empty($question_content)) {
                        throw new Exception("Question content cannot be empty");
                    }
                    
                    // Get question type - should be 1 for MCQ in this case
                    $type = (int)$row_data['type'];
                    if ($type !== 1) {
                        throw new Exception("Only MCQ questions (type=1) are supported for bulk upload");
                    }
                    
                    // Process tags if present
                    $tags = isset($row_data['tags']) ? $row_data['tags'] : '';
                    $tags_array = array_map('trim', explode(',', $tags));
                    $tags = implode(',', array_unique(array_filter($tags_array)));
                    
                    // Prepare question data using the same structure as create()
                    $question_data = [
                        'question_content' => $question_content,
                        'question_title' => $row_data['question_title'] ?? substr($question_content, 0, 100), // Use first 100 chars as title if not provided
                        'type' => $type,
                        'sub_type' => isset($row_data['sub_type']) ? (int)$row_data['sub_type'] : null,
                        'score' => isset($row_data['score']) ? (int)$row_data['score'] : 1, // Default score to 1 if not provided
                        'difficulty_level' => $row_data['difficulty_level'] ?? 1, // Default difficulty to 1 if not provided
                        'tags' => $tags,
                        'is_active' => 1,
                        'created_by' => $this->user_session['id'],
                    ];
                    
                    // Insert question
                    $question_id = $this->db_model->insert(TABLE_QUESTION_BANK, $question_data);
                    
                    if (!$question_id) {
                        throw new Exception("Failed to insert question");
                    }
                    
                    // Process options
                    $options = [];
                    for ($i = 1; $i <= 4; $i++) {
                        if (isset($row_data["option_$i"]) && !empty(trim($row_data["option_$i"]))) {
                            $options[] = [
                                'option_text' => trim($row_data["option_$i"]),
                                'is_correct' => isset($row_data["is_correct_$i"]) ? (int)$row_data["is_correct_$i"] : 0,
                            ];
                        }
                    }
                    
                    // Validate options
                    if (count($options) < 2) {
                        throw new Exception("MCQ questions must have at least 2 options");
                    }
                    
                    $correct_count = 0;
                    foreach ($options as $option) {
                        if ($option['is_correct']) {
                            $correct_count++;
                        }
                    }
                    
                    if ($correct_count < 1) {
                        throw new Exception("MCQ questions must have at least one correct option");
                    }
                    
                    // Insert options
                    foreach ($options as $option) {
                        $option_data = [
                            'question_id' => $question_id,
                            'option_text' => $option['option_text'],
                            'is_correct' => $option['is_correct'],
                            'is_active' => 1
                        ];
                        
                        $option_id = $this->db_model->insert(TABLE_ANSWER_OPTIONS, $option_data);
                        
                        if (!$option_id) {
                            throw new Exception("Failed to insert option");
                        }
                    }
                    
                    $successful_inserts++;
                    
                } catch (Exception $e) {
                    $failed_rows[] = [
                        'row' => $absolute_row,
                        'question' => isset($row_data['question_content']) ? substr($row_data['question_content'], 0, 50) . '...' : 'Unknown',
                        'error' => $e->getMessage()
                    ];
                    
                    if (isset($question_id) && $question_id) {
                        // Clean up any partially created question
                        $this->db_model->delete(TABLE_ANSWER_OPTIONS, ['question_id' => $question_id]);
                        $this->db_model->delete(TABLE_QUESTION_BANK, ['id' => $question_id]);
                    }
                }
            }
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $response = [
                    'status' => 'error',
                    'message' => 'Database transaction failed for batch ' . ($batch_index + 1)
                ];
                echo json_encode($response);
                return;
            } else {
                $this->db->trans_commit();
            }
        }
        
        if ($successful_inserts == $total_rows) {
            $response = [
                'status' => 'success',
                'message' => "All {$successful_inserts} questions were added successfully."
            ];
        } else {
            $response = [
                'status' => 'partial',
                'message' => "{$successful_inserts} out of {$total_rows} questions were added successfully.",
                'failed_rows' => $failed_rows
            ];
        }
        
        echo json_encode($response);
    }
}


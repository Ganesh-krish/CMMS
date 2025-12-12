<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Question extends CI_Controller
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

    }


    
    private function check_permisson ($id){


        $conditions = [
            "college_id" => $this->college[ 'id' ],
            'is_active' => 1,
        ];

       if ($this->permissions["read"] !== "all") {
            if (is_array($this->permissions["read"])) {
                $this->db->where_in("created_by", $this->permissions["read"]);
            } else {
                $conditions["created_by"] = $this->permissions["read"];
            }
        }

        $questions = $this->db_model->get_all(
            TABLE_QUESTION_BANK, 
            $conditions
        );

        $question_ids = array_column($questions,"id");

       return in_array($id,$question_ids);


    }

    public function view($question_id) {
        // Check if it's an AJAX request
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }
    
        // Get question details
        $question = $this->db_model->get_row(TABLE_QUESTION_BANK, ['id' => $question_id]);
        if (!$question) {
            echo json_encode(['status' => 'error', 'message' => 'Question not found']);
            return;
        }
    
        // Get question type name
        $question_type = $this->db_model->get_row(TABLE_QUESTION_TYPES, ['id' => $question['type']]);
        $question['type_name'] = $question_type ? $question_type['type'] : 'Unknown';
    
        // Get question subtype if exists
        if ($question['sub_type']) {
            $sub_type = $this->db_model->get_row(TABLE_QUESTION_SUB_TYPES, ['id' => $question['sub_type']]);
            $question['sub_type_name'] = $sub_type ? $sub_type['sub_type'] : '';
        } else {
            $question['sub_type_name'] = '';
        }
    
        // Get difficulty level name
        $difficulty_level = $this->db_model->get_row(TABLE_QUESTION_DIFFICULTY_LEVEL, ['id' => $question['difficulty_level']]);
        $question['difficulty_name'] = $difficulty_level ? $difficulty_level['level'] : 'Unknown';
    
        // Get options if MCQ
        if ($question['type'] == 1) {
            $question['options'] = $this->db_model->get_all(
                TABLE_ANSWER_OPTIONS,
                ['question_id' => $question_id, 'is_active' => 1]
            );
        }
    
        // Get test cases if CODE
        if ($question['type'] == 2) {
            $question['test_cases'] = $this->db_model->get_all(
                TABLE_QUESTION_TEST_CASES,
                ['question_id' => $question_id]
            );
        }
    
        // Format tags as array
        $question['tags_array'] = !empty($question['tags']) ? explode(',', $question['tags']) : [];
    
        // Return the question data
        echo json_encode(['status' => 'success', 'question' => $question]);
    }

    public function questions()
    {
        $data["url"] = $this->url;
        $class["classname"] = "questions";
        $class["url"] = $this->url;
        $class["college_id"] = $this->college['id'];
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[$this->session_data['designation']] ?? 'hod';
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
    
        $difficulty_level = $this->input->get('difficulty_level');
        $topic = $this->input->get('topic');
        $tags = $this->input->get('tags');
        $types = $this->input->get('types');

        $conditions = [
            "is_active" => true,
            "college_id" => $this->college['id']
        ];

        $principal = $this->db_model->get_all(TABLE_STAFF, [
            'college_id' => $this->college['id'],
            'designation' => DESIGNATION_PRINCIPAL
        ]);

        $principal_ids = array_column($principal, 'id');

        if($this->permissions['read'] !== 'all') {
            if (is_array($this->permissions['read'])) {
                $this->db->where_in('created_by', array_merge($this->permissions['read'], $principal_ids));
            } 
            else {
                $conditions['created_by'] = array_merge($this->permissions['read'], $principal_ids);
            }
        }

        if ( !empty( $difficulty_level ) ) {
            $conditions[ 'difficulty_level' ] = $difficulty_level;
        }
        if ( !empty( $tags ) ) {
            $conditions[ 'tags LIKE' ] = '%' . $tags . '%';
        }

        if(!empty($types)) {
            $conditions[ 'type' ] = $types;
        }

        $data[ 'questions' ] = $this->db_model->get_all(
            TABLE_QUESTION_BANK,
            $conditions
        );
        // Get all question types
        $data['question_types'] = $this->db_model->get_all(
            TABLE_QUESTION_TYPES,
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

        $tags_result = $this->db_model->get_all( TABLE_QUESTION_BANK, [ 'is_active' => true,'college_id' => $this->college['id'] ] );
        $all_tags = [];

        foreach ( $tags_result as $row ) {
            if ( !empty( $row[ 'tags' ] ) ) {
                $tags_array = explode( ',', $row[ 'tags' ] );
                $all_tags = array_merge( $all_tags, $tags_array );
            }
        }

        $data[ 'tags' ] = array_values( array_unique( array_filter( $all_tags ) ) );
        $data[ 'question_types' ] = $this->db_model->get_all( TABLE_QUESTION_TYPES, [ 'is_active' => true ] );
  
        // $data[ 'topics' ] = $this->db_model->get_distinct( TABLE_QUESTION_BANK, 'topic', [ 'is_active' => true,  ] );
        $data['samplePdfurl'] = $this->uri->segment(3) . '/SampleCsv/download_sample_csv_questions';
        $this->load->view('faculty/sidebar', $class);
        $this->load->view('question_bank/view', $data);
        $this->load->view('faculty/footer');
    }


    public function add($id = null) {

        // if(!$this->check_permisson($id)){

        //     redirect( base_url( $this->url . '/question' ) );

        // }

        $data["url"] = $this->url;
        $class["classname"] = "questions";
        $class["url"] = $this->url;
        $class["college_id"] = $this->college['id'];
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[$this->session_data['designation']] ?? 'hod';
        $class["sidebar_href"] = base_url($this->url . "/" . $path);
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
            TABLE_QUESTION_TYPES,
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
        $tags_result = $this->db_model->get_all(TABLE_QUESTION_BANK, ['is_active' => 1, 'college_id' => $this->college['id']]);
        $all_tags = [];
        
        foreach ($tags_result as $row) {
            if (!empty($row['tags'])) {
                $tags_array = explode(',', $row['tags']);
                $all_tags = array_merge($all_tags, $tags_array);
            }
        }
        
        $data['tags'] = array_values(array_unique(array_filter($all_tags)));
        
        $this->load->view('faculty/sidebar', $class);
        $this->load->view('question_bank/add', $data);
        $this->load->view('faculty/footer');
    }



    public function edit( $question_id = null ) {

        $data["url"] = $this->url;
        $class["classname"] = "questions";
        $class["url"] = $this->url;
        $class["college_id"] = $this->college['id'];
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[$this->session_data['designation']] ?? 'hod';
        $class["sidebar_href"] = base_url($this->url . "/" . $path);

        if ( !$question_id ) {

            echo json_encode( array( 'status'=> 'error', 'message' => 'Question not found' ) );
            return;
        }

        if(!$this->check_permisson($question_id)){

            redirect( base_url( $this->url . '/question' ) );

        }




        $question = $this->db_model->get_row(
            TABLE_QUESTION_BANK,
            [
                'id' => $question_id
            ],
        );



        if (!in_array($question['created_by'], $this->permissions['modify'])) {
            $this->session->set_flashdata('message', array('danger', 'You are not allowed to edit this question.'));
            redirect(base_url($this->url . "/question"));
        }  


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
            TABLE_QUESTION_TYPES,
            ['is_active' => 1]
        );
        
        // Get all question subtypes
        $data['question_sub_types'] = $this->db_model->get_all(
            TABLE_QUESTION_SUB_TYPES,
            ['is_active' => 1]
        );
        

        $data[ 'question' ][ 'options' ] = $options;

        $data[ 'question_types' ] = $this->db_model->get_all( TABLE_QUESTION_TYPES );


        $tags_result = $this->db_model->get_all(TABLE_QUESTION_BANK, ['is_active' => 1, 'college_id' => $this->college['id']]);
        $all_tags = [];
        
        foreach ($tags_result as $row) {
            if (!empty($row['tags'])) {
                $tags_array = explode(',', $row['tags']);
                $all_tags = array_merge($all_tags, $tags_array);
            }
        }
        
        $data['tags'] = array_values(array_unique(array_filter($all_tags)));

         
        $this->load->view('faculty/sidebar', $class);
        $this->load->view('question_bank/add', $data);
        $this->load->view('faculty/footer');
        
    }

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
        

        $question_title = $data['question']['question_title'];

        $is_duplicate = $this->db_model->get_row(TABLE_QUESTION_BANK, [
            'question_title' => $question_title,
            'college_id' => $this->college['id'],
            'is_active' => 1
        ]);

        if($is_duplicate){
            echo json_encode(['status' => 'error', 'message' => 'Question already exists']);
            return;
        }
        // Insert question
        $question_data = [
            'question_title' => $question_title,
            'question_content' => $data['question']['question_content'],
            'type' => $data['question']['type'],
            'sub_type' => $data['question']['sub_type'],
            'score' => $data['question']['score'],
            'difficulty_level' => $data['question']['difficulty_level'],
            'tags' => $data['question']['tags'],
            'created_by'   => $this->session_data['id'],
            'college_id' => $this->college['id'],
            'is_active' => 1,
            'explanation' => $data['question']['explanation']
        ];
        
        // If it's a CODE question, save selected languages
        if ($data['question']['type'] == 2 && !empty($data['question']['selected_languages'])) {

            $programming_languages = ['Java', 'Python', 'C', 'C++', 'NodeJS', 'JavaScript', 'Groovy', 'JShell', 'Haskell', 'Tcl', 
                                        'Lua', 'Ada', 'CommonLisp', 'D', 'Elixir', 'Erlang', 'F#', 'Fortran', 'Assembly', 'Scala', 
                                        'PHP', 'Python2', 'C#', 'Perl', 'Ruby', 'Go', 'R', 'Racket', 'OCaml', 'Visual Basic', 
                                        'Basic', 'Bash', 'Clojure', 'TypeScript', 'Cobol', 'Kotlin', 'Pascal', 'Prolog', 'Rust', 
                                        'Swift', 'Objective-C', 'Octave', 'Text', 'BrainFK', 'CoffeeScript', 'EJS', 'Dart', 'Deno', 'Bun'];


            $web_languages = ['HTML', 'Materialize', 'Bootstrap', 'JQuery', 'Foundation', 'Bulma', 'UIkit', 'Semantic UI','Skeleton', 'Milligram', 'PaperCSS', 'BackboneJS', 'React', 'Vue', 'Angular'];

            $databases = ['MySQL', 'Oracle Database', 'PostgreSQL', 'MongoDB', 'SQLite', 'Redis', 'MariaDB','Cassandra', 'Oracle PL/SQL', 'Microsoft SQL Server'];


            if($data['question']['sub_type'] == 3){
                // programming_languages
                $allowed_languages = array_map('strtolower', $programming_languages);
                $selected_languages = is_array($data['question']['selected_languages']) ? 
                    $data['question']['selected_languages'] : 
                    array_map('trim', explode(',', $data['question']['selected_languages']));
                $selected_languages = array_map('strtolower', $selected_languages);
                $question_data['selected_languages'] = implode(',', array_intersect($selected_languages, $allowed_languages));
            }

            if($data['question']['sub_type'] == 4){ 
                // web_languages
                $allowed_languages = array_map('strtolower', $web_languages);
                $selected_languages = is_array($data['question']['selected_languages']) ? 
                    $data['question']['selected_languages'] : 
                    array_map('trim', explode(',', $data['question']['selected_languages']));
                $selected_languages = array_map('strtolower', $selected_languages);
                $question_data['selected_languages'] = implode(',', array_intersect($selected_languages, $allowed_languages));
            }

            if ($data['question']['sub_type'] == 5) {
                // databases
                $allowed_languages = array_map('strtolower', $databases);
                $selected_languages = is_array($data['question']['selected_languages']) ? 
                    $data['question']['selected_languages'] : 
                    array_map('trim', explode(',', $data['question']['selected_languages']));
                $selected_languages = array_map('strtolower', $selected_languages);
                $question_data['selected_languages'] = implode(',', array_intersect($selected_languages, $allowed_languages));
            }
            

            
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
                    'college_id' => $this->college['id'],
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


    public function update($id) {

        
        if(!$this->check_permisson($id)){

            redirect( base_url( $this->url . '/question' ) );

        }

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

        $is_duplicate = $this->db_model->get_row(TABLE_QUESTION_BANK, [
            'question_title' => $data['question']['question_title'],
            'college_id' => $this->college['id'],
            'is_active' => 1,
            'id !=' => $id
        ]);

        if($is_duplicate){
            echo json_encode(['status' => 'error', 'message' => 'Question already exists']);
            return;
        }
        
        // Start transaction
        $this->db->trans_start();
        
        $question_title = $data['question']['question_title'] ;
        
        // Update question
        $question_data = [
            'question_title' => $question_title,
            'question_content' => $data['question']['question_content'],
            'type' => $data['question']['type'],
            'sub_type' => $data['question']['sub_type'],
            'score' => $data['question']['score'],
            'difficulty_level' => $data['question']['difficulty_level'],
            'tags' => $data['question']['tags'],
            'explanation' => $data['question']['explanation']
        ];
        
        // If it's a CODE question, update selected languages
        if ($data['question']['type'] == 2 && isset($data['question']['selected_languages'])) {


            $programming_languages = ['Java', 'Python', 'C', 'C++', 'NodeJS', 'JavaScript', 'Groovy', 'JShell', 'Haskell', 'Tcl', 
            'Lua', 'Ada', 'CommonLisp', 'D', 'Elixir', 'Erlang', 'F#', 'Fortran', 'Assembly', 'Scala', 
            'PHP', 'Python2', 'C#', 'Perl', 'Ruby', 'Go', 'R', 'Racket', 'OCaml', 'Visual Basic', 
            'Basic', 'Bash', 'Clojure', 'TypeScript', 'Cobol', 'Kotlin', 'Pascal', 'Prolog', 'Rust', 
            'Swift', 'Objective-C', 'Octave', 'Text', 'BrainFK', 'CoffeeScript', 'EJS', 'Dart', 'Deno', 'Bun'];


            $web_languages = ['HTML', 'Materialize', 'Bootstrap', 'JQuery', 'Foundation', 'Bulma', 'UIkit', 'Semantic UI','Skeleton', 'Milligram', 'PaperCSS', 'BackboneJS', 'React', 'Vue', 'Angular'];

            $databases = ['MySQL', 'Oracle Database', 'PostgreSQL', 'MongoDB', 'SQLite', 'Redis', 'MariaDB','Cassandra', 'Oracle PL/SQL', 'Microsoft SQL Server'];


            if($data['question']['sub_type'] == 3){
            // programming_languages
                $allowed_languages = array_map('strtolower', $programming_languages);
                $selected_languages = is_array($data['question']['selected_languages']) ? 
                    $data['question']['selected_languages'] : 
                    array_map('trim', explode(',', $data['question']['selected_languages']));
                $selected_languages = array_map('strtolower', $selected_languages);
                $question_data['selected_languages'] = implode(',', array_intersect($selected_languages, $allowed_languages));
            }

            if($data['question']['sub_type'] == 4){ 
                // web_languages
                $allowed_languages = array_map('strtolower', $web_languages);
                $selected_languages = is_array($data['question']['selected_languages']) ? 
                    $data['question']['selected_languages'] : 
                    array_map('trim', explode(',', $data['question']['selected_languages']));
                $selected_languages = array_map('strtolower', $selected_languages);
                $question_data['selected_languages'] = implode(',', array_intersect($selected_languages, $allowed_languages));
            }

            if ($data['question']['sub_type'] == 5) {
                // databases
                $allowed_languages = array_map('strtolower', $databases);
                $selected_languages = is_array($data['question']['selected_languages']) ? 
                    $data['question']['selected_languages'] : 
                    array_map('trim', explode(',', $data['question']['selected_languages']));
                $selected_languages = array_map('strtolower', $selected_languages);
                $question_data['selected_languages'] = implode(',', array_intersect($selected_languages, $allowed_languages));
            }

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

        
        if(!$this->check_permisson($question_id)){

            redirect( base_url( $this->url . '/question' ) );

        }


        if ( empty( $question_id ) ) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid question ID'
            ];
            echo json_encode( $response );
            return;
        }

        $question = $this->db_model->get_row(TABLE_QUESTION_BANK,[ 'id' => $question_id ]);

        if ( !$question ) {
            $response = [
                'status' => 'error',
                'message' => 'Question not found'
            ];
            echo json_encode( $response );
            return;
        }

   

        if (!in_array($question["created_by"], $this->permissions['modify'])) {
            http_response_code(200);
            echo json_encode(['status' => 'error', 'message' => 'You do not have permission to delete this question']);
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
        error_log("Starting bulk_add_questions method");
        
        $file_path = $this->common->upload_csv();
        error_log("File path: " . $file_path);

        if (!$file_path) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(["status" => "error", "message" => "File upload failed!"]);
            } else {
                $this->session->set_flashdata('message', ['danger', 'File upload failed!']);
                redirect(base_url($this->url . '/question'));
            }
            return;
        }

        $csv_result = $this->common->read_csv(FCPATH . "application/uploads/$file_path");
        error_log("CSV headers: " . print_r($csv_result['headers'], true));
        error_log("First row of CSV data: " . print_r($csv_result['values'][0] ?? [], true));

        // Remove BOM and normalize header names to lowercase
        $header = array_map(function($h) {
            // Remove BOM and any other invisible characters
            $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h);
            return strtolower(trim($h));
        }, $csv_result['headers']);
        error_log("Normalized headers: " . print_r($header, true));
        $csv_data = $csv_result['values'];

    

        $total_rows = count($csv_data);
        $successful_inserts = 0;
        $failed_rows = [];
        $duplicate_rows = [];
        
        error_log("Total rows to process: " . $total_rows);
        
        $batch_size = 1000;
        $batches = array_chunk($csv_data, $batch_size);

       

        $types = $this->db_model->get_all(TABLE_QUESTION_TYPES);
        $sub_types = $this->db_model->get_all(TABLE_QUESTION_SUB_TYPES,['type_id'=>1]);
        $difficulty_levels = $this->db_model->get_all(TABLE_QUESTION_DIFFICULTY_LEVEL);

        $type_mapper = [];
        $sub_type_mapper = [];
        $difficulty_level_mapper = [];

        foreach ($types as $type) {
            $type_mapper[strtolower(trim($type['type']))] = $type['id'];
        }

        foreach ($sub_types as $sub_type) {
            $sub_type_mapper[strtolower(trim(str_replace('MCQ-', '', $sub_type['sub_type'])))] = $sub_type['id'];
        }

        foreach ($difficulty_levels as $difficulty_level) {
            $difficulty_level_mapper[strtolower(trim($difficulty_level['level']))] = $difficulty_level['id'];
        }

        foreach ($batches as $batch_index => $batch) {
            error_log("Processing batch " . ($batch_index + 1));
            $this->db->trans_begin();
            
            foreach ($batch as $row_index => $row) {
                try {
                    $absolute_row = ($batch_index * $batch_size) + $row_index + 2; 
                    error_log("Processing row " . $absolute_row);

                    if (count($row) < count($header)) {
                        throw new Exception("Row {$absolute_row} has insufficient columns");
                    }

                    $row_data = array_combine($header, $row);
                    error_log("Row data after array_combine: " . print_r($row_data, true));
                    error_log("Question title from row data: " . ($row_data['question_title'] ?? 'not set'));

                    $question_title = $row_data['question_title'];
                    error_log("Question title after assignment: " . $question_title);

                    $row_data['type'] = $type_mapper[strtolower(trim($row_data['type']))];
                    $row_data['sub_type'] = $sub_type_mapper[strtolower(trim(str_replace('MCQ-', '', $row_data['sub_type'])))];
                    $row_data['difficulty_level'] = $difficulty_level_mapper[strtolower(trim($row_data['difficulty_level']))];


                    

                    $question_content = $row_data['question_content'];
                    if (empty($question_content)) {
                        throw new Exception("Question content cannot be empty");
                    }
                    
                    $type = (int)$row_data['type'];
                    if ($type !== 1) {
                        throw new Exception("Only MCQ questions (type=1) are supported for bulk upload");
                    }
                    
                    // Process tags if present
                    $tags = isset($row_data['tags']) ? $row_data['tags'] : '';
                    $tags_array = array_map('trim', explode(',', $tags));
                    $tags = implode(',', array_unique(array_filter($tags_array)));
                
                    if (!isset($row_data['question_title']) || empty(trim($row_data['question_title']))) {
                        throw new Exception("Question title is required");
                    }
                    
                    $question_title = $row_data['question_title'];

                    
                    
                    $is_duplicate = $this->db_model->get_row(TABLE_QUESTION_BANK, [
                        'question_title' => $question_title,
                        'college_id' => $this->college['id'],
                        'is_active' => 1
                    ]);

                    if($is_duplicate){
                        error_log("Found duplicate question: " . $question_title);
                        $duplicate_rows[] = [
                            'row' => $absolute_row,
                            'question' => $question_title,
                            'error' => 'Question already exists'
                        ];
                        continue;
                    }

                    $question_data = [
                        'question_content' => $question_content,
                        'question_title' => $question_title,
                        'score' => isset($row_data['score']) ? (int)$row_data['score'] : 1,
                        'type' => $type,
                        'sub_type' => isset($row_data['sub_type']) ? (int)$row_data['sub_type'] : null,
                        'difficulty_level' => $row_data['difficulty_level'] ?? 1,
                        'tags' => $tags,
                        'is_active' => 1,
                        'created_by' => $this->session_data['id'],
                        'college_id' => $this->college['id'],
                        'explanation' => $row_data['explanation'],
                        'file_path' => $file_path
                    ];
                    
                    $question_id = $this->db_model->insert(TABLE_QUESTION_BANK, $question_data);
                    
                    if (!$question_id) {
                        throw new Exception("Failed to insert question");
                    }
                    
                    $options = [];
                    for ($i = 1; $i <= 5; $i++) {
                        if (isset($row_data["option_$i"]) && trim($row_data["option_$i"])!=='' ) {
                            $options[] = [
                                'option_text' => trim($row_data["option_$i"]),
                                'is_correct' => isset($row_data["is_correct_$i"]) ? (int)$row_data["is_correct_$i"] : 0,
                                'college_id' => $this->college['id'],
                            ];
                        }
                    }
                    
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
                    error_log("Error processing row " . $absolute_row . ": " . $e->getMessage());
                    $failed_rows[] = [
                        'row' => $absolute_row,
                        'question' => isset($row_data['question_content']) ? substr($row_data['question_content'], 0, 50) . '...' : 'Unknown',
                        'error' => $e->getMessage()
                    ];
                    
                    if (isset($question_id) && $question_id) {
                        $this->db_model->delete(TABLE_ANSWER_OPTIONS, ['question_id' => $question_id]);
                        $this->db_model->delete(TABLE_QUESTION_BANK, ['id' => $question_id]);
                    }
                }
            }
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                error_log("Transaction failed for batch " . ($batch_index + 1));
                $response = [
                    'status' => 'error',
                    'message' => 'Database transaction failed for batch ' . ($batch_index + 1)
                ];
                echo json_encode($response);
                return;
            } else {
                $this->db->trans_commit();
                error_log("Transaction committed for batch " . ($batch_index + 1));
            }
        }

        // Prepare the results array
        $results = [
            'total_rows' => $total_rows,
            'successful_inserts' => $successful_inserts,
            'failed_rows' => $failed_rows,
            'duplicate_rows' => $duplicate_rows
        ];

        error_log("Final results: " . print_r($results, true));

        // Store results in session
        $this->session->set_userdata('bulk_upload_results', $results);
        error_log("Session data after setting: " . print_r($this->session->userdata('bulk_upload_results'), true));



        if (empty($failed_rows) && empty($duplicate_rows)) {
            $this->session->set_flashdata('message', ['success', "Successfully uploaded {$successful_inserts} questions"]);
            echo json_encode([
                'status' => 'success',
                'message' => "Successfully uploaded {$successful_inserts} questions"
            ]);
            // redirect(base_url($this->url . '/question'));
            return;
        } 

        // For AJAX requests, return success status
        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Upload completed successfully'
            ]);
            return;
        }

            echo json_encode([
                'status' => 'success',
                'message' => 'Not uploaded'
            ]);

        // redirect(base_url($this->url . '/question/upload_report'));
    }

    public function upload_report()
    {
        error_log("Starting upload_report method");
        
        $data["url"] = $this->url;
        $class["classname"] = "questions";
        $class["url"] = $this->url;
        $class["college_id"] = $this->college['id'];
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[$this->session_data['designation']] ?? 'hod';
        $class["sidebar_href"] = base_url($this->url . "/" . $path);

        // Get results from session
        $results = $this->session->userdata('bulk_upload_results');
        error_log("Session data in upload_report: " . print_r($results, true));
        
        if (!$results) {
            error_log("No results found in session - redirecting to questions page");
            redirect(base_url($this->url . '/question'));
            return;
        }

        // Pass data to view
        $data['total_rows'] = $results['total_rows'];
        $data['successful_inserts'] = $results['successful_inserts'];
        $data['failed_rows'] = $results['failed_rows'];
        $data['duplicate_rows'] = $results['duplicate_rows'];

        error_log("Data being passed to view: " . print_r($data, true));

        // Clear the session data after retrieving it
        $this->session->unset_userdata('bulk_upload_results');
        error_log("Session data cleared");

        $this->load->view('faculty/sidebar', $class);
        $this->load->view('question_bank/upload_report', $data);
        $this->load->view('faculty/footer');
    }

    public function uploadQuestionImage()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // Check if file was uploaded
        if (empty($_FILES['image']['name'])) {
            echo json_encode([
                'success' => false,
                'message' => 'No image file uploaded'
            ]);
            return;
        }

        $image_url = $this->common->upload_to_cloudinary('image', 'question_images'); // Using 'image' as field name and specific folder

        if ($image_url) {
            $response = [
                'success' => true,  // Changed from 'status' to 'success' to match your view
                'file_url' => $image_url  // Changed from 'url' to 'file_url' to match your view
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to upload image'
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }

    public function download_mcq() {
        // Get all MCQ questions


        $tags = $this->input->get('tags');


        $conditions =  [
            'type' => 1, // MCQ type
            'is_active' => 1,
            'college_id' => $this->college['id']
        ];


        if ( !empty( $tags ) ) {
            $conditions[ 'tags LIKE' ] = '%' . $tags . '%';
        }

        $questions = $this->db_model->get_all(
            TABLE_QUESTION_BANK,
            $conditions
        );

        // Get all question subtypes for mapping
        $sub_types = $this->db_model->get_all(
            TABLE_QUESTION_SUB_TYPES,
            ['is_active' => 1]
        );

        // Create sub-type mapping
        $sub_type_map = [];
        foreach ($sub_types as $sub_type) {
            $sub_type_map[$sub_type['id']] = $sub_type['sub_type'];
        }

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="mcq_questions.csv"');

        // Create output stream
        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM for proper Excel encoding
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // CSV Headers
        $headers = [
            'question_title',
            'question_content',
            'type',
            'sub_type',
            'score',
            'difficulty_level',
            'option_1',
            'option_2',
            'option_3',
            'option_4',
            'option_5',
            'is_correct_1',
            'is_correct_2',
            'is_correct_3',
            'is_correct_4',
            'is_correct_5',
            'tags',
            'explanation'
        ];

        // Write headers
        fputcsv($output, $headers);

        // Write data rows
        foreach ($questions as $question) {
            // Get options for this question
            $options = $this->db_model->get_all(
                TABLE_ANSWER_OPTIONS,
                [
                    'question_id' => $question['id'],
                    'is_active' => 1
                ]
            );

            // Prepare options array
            $option_texts = array_fill(0, 5, ''); // Initialize with empty strings
            $is_correct = array_fill(0, 5, '0'); // Initialize with 0s

            // Fill in actual options
            foreach ($options as $index => $option) {
                if ($index < 5) { // Only process up to 5 options
                    $option_texts[$index] = $option['option_text'];
                    $is_correct[$index] = $option['is_correct'];
                }
            }

            // Get difficulty level name
            $difficulty_level = $this->db_model->get_row(
                TABLE_QUESTION_DIFFICULTY_LEVEL,
                ['id' => $question['difficulty_level']]
            );

            // Get sub-type name
            $sub_type_name = isset($sub_type_map[$question['sub_type']]) ? $sub_type_map[$question['sub_type']] : '';

            // Prepare row data
            $row = [
                $question['question_title'],
                $question['question_content'],
                'MCQ', // Type is always MCQ
                $sub_type_name, // Use actual sub-type from mapping
                $question['score'],
                $difficulty_level ? $difficulty_level['level'] : '',
                $option_texts[0],
                $option_texts[1],
                $option_texts[2],
                $option_texts[3],
                $option_texts[4],
                $is_correct[0],
                $is_correct[1],
                $is_correct[2],
                $is_correct[3],
                $is_correct[4],
                $question['tags'],
                $question['explanation']
            ];

            // Write row
            fputcsv($output, $row);
        }

        // Close the output stream
        fclose($output);
        exit;
    }
        
}

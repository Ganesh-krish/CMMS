<?php
defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Test extends CI_Controller
 {
    private $url;
    private $college;
    private $session_data;

    function __construct()
    {
        parent::__construct();
        $this->load->model('common', 'common');
        $this->load->model('faculty/db_model', 'db_model');
        $this->load->model('faculty/test_model', 'test_model');
        $this->url = $this->uri->segment( 1 );
        $this->common->check_user_session( $this->url );
        $this->college = $this->common->get_default_college();
        $this->session_data = $this->session->userdata( $this->url );
        $this->permissions = $this->common->get_access_permissions( $this->session_data );

    }

    private function check_permisson ($test_id){

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


        $tests = $this->db_model->get_all(
            "tests", 
            $conditions
        );

        $test_ids = array_column($tests,"id");

       return in_array($test_id,$test_ids);


    }

    public function view() {

        $data[ 'url' ] = $this->url;
        $class[ 'classname' ] = 'test';
        $class[ 'url' ] = $this->url;
        $class[ 'college_id' ] = $this->college[ 'id' ];
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[ $this->session_data[ 'designation' ] ] ?? 'hod';
        $class[ 'sidebar_href' ] = base_url( $this->url . '/' . $path );
        // Get filter parameters from URL
        $module_id = $this->input->get( 'module' );
        $start_date = $this->input->get( 'start_date' );
        $end_date = $this->input->get( 'end_date' );

        // Build conditions array for filtering
        $conditions = [
            'college_id' => $this->college[ 'id' ],
            'is_active' => 1,
        ];

        if ( !empty( $module_id ) ) {
            $conditions[ 'module_id' ] = $module_id;
        }

        if ( !empty( $start_date ) && !empty( $end_date ) ) {
            // Adding date range filter - format dates as needed for your database
            $conditions[ 'start_date >=' ] = date( 'Y-m-d 00:00:00', strtotime( $start_date ) );
            $conditions[ 'end_date <=' ] = date( 'Y-m-d 23:59:59', strtotime( $end_date ) );
        }

        if ( $this->permissions[ 'read' ] !== 'all' ) {
            if ( is_array( $this->permissions[ 'read' ] ) ) {
                $this->db->where_in( 'created_by', $this->permissions[ 'read' ] );
            } else {
                $conditions[ 'created_by' ] = $this->permissions[ 'read' ];
            }
        }

        // Pass conditions to model
        $data[ 'tests' ] = $this->test_model->get_filtered_tests( $conditions );
        $data[ 'title' ] = 'Manage Tests';
        $data[ 'active_menu' ] = 'tests';

        // Get all modules for the dropdown
        $data[ 'modules' ] = $this->db_model->get_all( TABLE_TEST_MODULES, [ 'is_active' => 1 ] );
        $this->load->view( 'faculty/sidebar', $class );
        $this->load->view( 'test/view', $data );
        $this->load->view( 'faculty/footer' );
    }


    public function delete_challenge($challenge_id) {
        if (empty($challenge_id)) {
            return false;
        }
    
        $api_token = ONE_COMPILER_API_KEY;
        if (empty($api_token)) {
            log_message('error', 'OneCompiler API Error: API token not configured');
            return false;
        }
    
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://onecompiler.com/api/v1/challenges/' . $challenge_id . '?access_token=' . $api_token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);
    
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        curl_close($curl);
    
        if ($err) {
            return false;
        }
    
        $result = json_decode($response, true);
    
        if ($http_code >= 200 && $http_code < 300) {
            return true;
        } else if (isset($result['status']) && $result['status'] === 'success') {
            return true;
        } else {
            $error_msg = isset($result['message']) ? $result['message'] : 'Unknown error occurred (HTTP Code: ' . $http_code . ')';
            return false;
        }
    }

    public function create_challenge( $test_id ) {
        if ( !$test_id ) {
            $this->session->set_flashdata( 'message', [ 'danger', 'Invalid test ID' ] );
            redirect( base_url( $this->url . '/test' ) );
            return;
        }

        $test = $this->test_model->get_test( $test_id );
        if ( !$test ) {
            $this->session->set_flashdata( 'message', [ 'danger', 'Test not found' ] );
            redirect( base_url( $this->url . '/test' ) );
            return;
        }

        // Prevent duplicate challenges on OneCompiler: if a challenge already exists
        // for this test, delete it before creating a fresh one.
        if (!empty($test->challenge_id)) {
            $this->delete_challenge($test->challenge_id);
        }

        $navigation = $this->db->get_where( TABLE_TEST_SETTINGS_NAVIGATION, [ 'test_id' => $test_id ] )->row();
        $security = $this->db->get_where( TABLE_TEST_SETTINGS_SECURITY, [ 'test_id' => $test_id ] )->row();
        $monitoring = $this->db->get_where( TABLE_TEST_SETTINGS_MONITORING, [ 'test_id' => $test_id ] )->row();
        $ui = $this->db->get_where( TABLE_TEST_SETTINGS_UI, [ 'test_id' => $test_id ] )->row();

    
        $this->db->select( 'tq.*, qb.question_title, qb.question_content, qb.score, qb.difficulty_level, qb.type,qb.sub_type,qb.selected_languages,qb.tags,qb.fill_blank_answer,qb.ignore_case' );
        $this->db->from( TABLE_TEST_QUESTIONS . ' tq' );
        $this->db->join( TABLE_QUESTION_BANK . ' qb', 'tq.question_id = qb.id' );
        $this->db->where( 'tq.test_id', $test_id );
        $this->db->where( 'tq.is_active', 1 );
        $this->db->order_by( 'tq.question_order', 'ASC' );
        $questions = $this->db->get()->result();

        if ( empty( $questions ) ) {
            $this->session->set_flashdata( 'message', [ 'warning', 'No questions found. Please add questions to the test first.' ] );
            redirect( base_url( $this->url . '/test/questions/' . $test_id ) );
            return;
        }

    
        $properties = [
            'captureUser' => $monitoring && $monitoring->capture_user_image ? 'yes' : 'no',
            'captureTabChanges' => $monitoring && $monitoring->capture_tab_change ? 'yes' : 'no',
            'enableFinish' => $navigation && $navigation->enable_finish_button ? 'yes' : 'no',
            'enableTimeTracking' => $monitoring && $monitoring->enable_time_tracking ? 'yes' : 'no',
            'timeLimit' => $test->duration ? intval( $test->duration ) : 60,
            'disableCopyPaste' => $security && $security->disable_copy_paste ? true : false,
            'showInstructionsOnStart' => $ui && $ui->show_instructions_on_start ? true : false,
            'shuffleProblemOrder' => $security && $security->shuffle_questions ? true : false,
            'shuffleMCQOptions' => $security && $security->shuffle_answer_options ? true : false, // shuffle question options
            // 'hideScoreAfterProblemTitle' => $ui && $ui->hide_score_after_title ? true : false,
            'hideChallengeEndTime' => $ui && $ui->hide_test_end_time ? true : false,
            'hideProblemsMenu' => $ui && $ui->hide_problem_menu ? true : false,
            // 'showNextChallengeOnLastProblemOnly' => $navigation && $navigation->show_next_on_last_question ? true : false,
            'showTabChangeWarning' => $ui && $ui->show_tab_change_warning ? true : false,
            'showReportAfterFinish' => $ui && $ui->show_report_on_finish ? true : false,
            'enableSections' => $ui && $ui->enable_sections ? true : false,
            'forceFullscreen' => $security && $security->force_full_screen ? true : false,
            'plagiarismChecker' => $security && $security->plagiarism_check ? true : false,  
            'autoSubmitMCQs' => $ui && $ui->auto_submit ? true : false,
            //negative marking config
            'enableNegativeMarking' => $test->negative_mark_value && (float)$test->negative_mark_value != 0 ? true : false,
            'negativeMarkingValue'   => $test->negative_mark_value ? (float)$test->negative_mark_value : 0,

        ];

        if ( $ui && $ui->close_after_tab_change ) {
            $properties[ 'maxTabSwitches' ] = $ui->close_after_tab_count ? intval( $ui->close_after_tab_count ) : 5;
            $properties[ 'closeAfterTabSwitches' ] = true;
        }

        if ( $monitoring && $monitoring->record_user_session ) {
            $properties[ 'enableSessionRecording' ] = true;
        }

        if($navigation->prev_test_id){
            $prev_test =  $this->test_model->get_test(   $navigation->prev_test_id );
            $properties[
                'linkedChallengeIdPrevious' 
            ] = $prev_test->challenge_id;
        }

        if( $navigation->next_test_id ){

            $next_test =  $this->test_model->get_test(   $navigation->next_test_id );
            $properties[
                'linkedChallengeId' 
            ] = $next_test->challenge_id;
        }
        $problems = [];

        foreach ( $questions as $question ) {

            $problem_type_mapper = [
                '1' => 'multipleChoice',
                '2' => 'code',
                '3' => 'fillInTheBlank'
            ];


            $problem_sub_type_mapper = [
                '1' => 'singleSelect',
                '2' => 'multiSelect',
                '3' => 'programmingLanguages',
                '4' => 'webLanguages',
                '5' => 'databases',
            ];

            $difficulty_map = [
                'easy' => 'easy',
                'medium' => 'medium',
                'hard' => 'hard',
                '1' => 'easy',
                '2' => 'medium',
                '3' => 'hard'
            ];

            if ( $question->type == 2 ) {

                $this->db->select( 'input,output,visibility' );
                $this->db->from( 'question_test_cases' );
                $this->db->where( 'question_id', $question->question_id );
                $test_cases = $this->db->get()->result();

                $validations = [];
                foreach ( $test_cases as $index => $case ) {
                    $validations[] = [
                        'id' => $index + 1,
                        'input' => $case->input,
                        'output' => $case->output,
                        'visibility' => $case->visibility ? "public" : "private"
                    ];
                }

                if ( empty( $validations ) ) {
                    $validations[] = [
                        'id' => 1,
                        'input' => 'Sample Input',
                        'output' => 'Sample Output',
                        'visibility' => "public" 
                    ];
                }

                // $supported_languages = $question->selected_languages ? explode( ',', $question->selected_languages ) : [ 'C', 'C++', 'Java', 'Python' ];

            }

    
            $difficulty = isset( $difficulty_map[ strtolower( $question->difficulty_level ) ] ) ?
            $difficulty_map[ strtolower( $question->difficulty_level ) ] : 'medium';

            $options = [];


            if ( $question->type == 1 ) { 
                
                $answer_options = $this->db_model->get_all(TABLE_ANSWER_OPTIONS, [ 'question_id' => $question->question_id]);
                
                $answers = [];

                $correct_answers = [];

                $index = 1;
                foreach ($answer_options as $answer_option) { 
                    $answers[] = [
                        'id' => $index,
                        'text' => $answer_option['option_text'],
                    ];

                    if ( $answer_option["is_correct"] ) {
                        $correct_answers[] = $index;
                    }

                    $index = $index + 1;
                }

                $options = [ 
                    'multipleChoice' => [
                        'options' => $answers,
                        'select' => $problem_sub_type_mapper[$question->sub_type],
                        'answer' => $correct_answers
                    ]
                ];
            }
        

            if ( $question->type == 2 ) {
                $options = [
                    'code' => [
                        'supportedLanguages' => $question->selected_languages ?  explode(',', $question->selected_languages) : null,
                        'validations' => $validations,
                        'problemCategory' => $problem_sub_type_mapper[ $question->sub_type ],
                        'ignoreCase' => $question->ignore_case ? true : false,
                        "complimentaryScore"=> 0
                    ]
                ];
            };

            if ( $question->type == 3 ) { 
                $options = [ 
                    'fillInTheBlank' => [ 
                        'answer'=> $question->fill_blank_answer,
                    ]
                ];
            }
    

            $problems[] =  $question->type == 2 ?  [
                'title' => $question->question_title,
                'markdown' => $question->question_content,
                'properties' => [
                    'problemType' => $problem_type_mapper[ $question->type ],
                    'score' => intval( $question->score ),
                    'difficultyLevel' => $difficulty,
                    'options' => $options,
                    'partialScore' => true
                ]
            ] :
            [
                'title' => $question->question_title,
                'markdown' => $question->question_content,
                'properties' => [
                    'problemType' => $problem_type_mapper[ $question->type ],
                    'score' => intval( $question->score ),
                    'difficultyLevel' => $difficulty,
                    'options' => $options
                ]
                ];
        }


        if ( empty( $problems ) ) {
            $this->session->set_flashdata( 'message', [ 'warning', 'No programming questions found in this test. Only programming questions can be used in challenges.' ] );
            redirect( base_url( $this->url . '/test/questions/' . $test_id ) );
            return;
        }

        $challenge = [
            'challenge' => [
                'title' => $test->title,
                'markdown' => $test->instructions,
                // 'tags' => $test->tags ? explode( ',', $test->tags ) : [],
                'visibility' => 'unlisted',
                'properties' => $properties
            ],
            'problems' => $problems
        ];

        $api_token = ONE_COMPILER_API_KEY;
        if ( empty( $api_token ) ) {
            $this->session->set_flashdata( 'message', [ 'danger', 'API token not configured. Please contact the administrator.' ] );
            redirect( base_url( $this->url . '/test/edit/' . $test_id ) );
            return;
        }

        $curl = curl_init();
        curl_setopt_array( $curl, [
            CURLOPT_URL => 'https://onecompiler.com/api/v1/challenges/create?access_token=' . $api_token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode( $challenge ),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ] );

        $response = curl_exec( $curl );
        $err = curl_error( $curl );
        curl_close( $curl );

        if ( $err ) {
            $this->session->set_flashdata( 'message', [ 'danger', 'Error connecting to API: ' . $err ] );
            redirect( base_url( $this->url . '/test/edit/' . $test_id ) );
            return;
        }

        $result = json_decode( $response, true );

        if ( isset( $result[ 'status' ] ) && $result[ 'status' ] === 'success' && isset( $result[ 'doc' ][ '_id' ] ) ) {
            $update_data = [
                'challenge_id' => $result[ 'doc' ][ '_id' ],
                'challenge_link' =>  $result[ 'doc' ][ 'link' ],
            ];
            $this->db->where( 'id', $test_id );
            $this->db->update( TABLE_TESTS, $update_data );

            }
        }


    

    /**
     * Update challenge with sections using OneCompiler UPDATE API
     * This is separate from create_challenge and only updates section data
     *
     * @param int $test_id Test ID
     * @return bool Success status
     */
    public function update_challenge_with_sections($test_id) {
        if (!$test_id) {
            log_message('error', 'Section Update: Invalid test ID');
            return false;
        }

        $test = $this->test_model->get_test($test_id);
        if (!$test || !$test->challenge_id) {
            log_message('error', 'Section Update: Test or challenge_id not found for test_id: ' . $test_id);
            return false;
        }

        // Check if sections are enabled
        $ui = $this->db->get_where(TABLE_TEST_SETTINGS_UI, ['test_id' => $test_id])->row();
        if (!$ui || !$ui->enable_sections) {
            return false; // Sections not enabled
        }

        // Note: We use UPDATE API which preserves existing problems and properties, 
        // so we don't rebuild them here - only update problemIds and sections
        $api_token = ONE_COMPILER_API_KEY;
        if (empty($api_token)) {
            log_message('error', 'OneCompiler API Error: API token not configured');
            return false;
        }

        // STEP 1: Read existing challenge from OneCompiler to get _id, user._id, etc.
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
        
        // Expect the OneCompiler GET challenge API shape: { challenge: {...}, problems: [...] }
        if (isset($challenge_response['challenge'])) {
            $existing_challenge = $challenge_response['challenge'];
            $existing_problems = $challenge_response['problems'] ?? [];
        } else {
            log_message('error', 'OneCompiler API Error: Unexpected response structure (missing challenge)');
            return false;
        }

        // Verify required fields exist
        if (!isset($existing_challenge['_id']) || !isset($existing_challenge['user'])) {
            log_message('error', 'OneCompiler API Error: Challenge missing required fields (_id or user)');
            return false;
        }

        // STEP 2: Patch the challenge with sections data
        // Get problemIds from existing problems to match with sections
        $problem_id_map = []; // Maps question_title to OneCompiler problem _id
        foreach ($existing_problems as $prob) {
            if (isset($prob['title']) && isset($prob['_id'])) {
                $problem_id_map[$prob['title']] = $prob['_id'];
            }
        }
        // Build sections with OneCompiler problem IDs (skip default unassigned section)
        // Optimized: Query excludes default section (ID = 1) directly in WHERE clause
        $sections_data_with_ids = [];

        // Get all section questions with question titles and section names in one query
        // Exclude default section (ID = 1) directly in the query
        // JOIN test_sections to get section_name directly - eliminates need for separate query and loop
        // Use GROUP BY to prevent duplicate questions per section
        $this->db->select('tq.section_id, qb.question_title, ts.section_name, MIN(tq.question_order) as min_order');
        $this->db->from(TABLE_TEST_QUESTIONS . ' tq');
        $this->db->join(TABLE_QUESTION_BANK . ' qb', 'qb.id = tq.question_id', 'left');
        $this->db->join(TABLE_TEST_SECTIONS . ' ts', 'ts.id = tq.section_id', 'left');
        $this->db->where('tq.test_id', $test_id);
        $this->db->where('tq.is_active', 1);
        $this->db->where('tq.section_id !=', DEFAULT_SECTION_ID);
        $this->db->group_by('tq.section_id, qb.question_title, ts.section_name');
        $this->db->order_by('tq.section_id', 'ASC');
        $this->db->order_by('min_order', 'ASC');
        $section_questions_data = $this->db->get()->result_array();

        // Check if we have any section questions (replaces empty sections check)
        if (empty($section_questions_data)) {
            log_message('info', 'Section Update: No section questions found for test_id: ' . $test_id);
            return false; // No sections with questions to update
        }

        // Build sections_data_with_ids as indexed array directly in one loop
        // Use helper map to track section_id -> array index mapping
        $sections_data_with_ids = [];
        $section_index_map = []; // Maps section_id to index in $sections_data_with_ids
        
        foreach ($section_questions_data as $sq_data) {
            $section_id = $sq_data['section_id'];
            
            // Initialize section if not exists
            if (!isset($section_index_map[$section_id])) {
                $section_index_map[$section_id] = count($sections_data_with_ids); // Get next index
                $sections_data_with_ids[] = [  // Add as indexed array directly
                    'name' => $sq_data['section_name'],
                    'problemIds' => []
                ];
            }
            
            // Add problem ID if exists in map
            if (isset($problem_id_map[$sq_data['question_title']])) {
                $prob_id = $problem_id_map[$sq_data['question_title']];
                $index = $section_index_map[$section_id];
                $sections_data_with_ids[$index]['problemIds'][] = $prob_id;
            }
        }
        
        // Get all problem IDs in order - ONLY from non-default sections
        // Build from sections_data_with_ids to maintain order
        $all_problem_ids = [];
        foreach ($sections_data_with_ids as $section_data) {
            $all_problem_ids = array_merge($all_problem_ids, $section_data['problemIds']);
        }  
        
        
        // Update challenge properties with sections
        if (!isset($existing_challenge['properties'])) {
            $existing_challenge['properties'] = [];
        }
        $existing_challenge['properties']['problemIds'] = $all_problem_ids;
        $existing_challenge['properties']['sections'] = $sections_data_with_ids;


        $challenge = [
            'challenge' => $existing_challenge,
            'problems' => $existing_problems  // Use directly, no copy needed
        ];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://onecompiler.com/api/v1/challenges/update?access_token=' . $api_token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($challenge),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            log_message('error', 'OneCompiler API Error: ' . $err);
            return false;
        }

        $result = json_decode($response, true);
        
        if (isset($result['status']) && $result['status'] === 'success') {
            log_message('info', 'Challenge updated with sections successfully for test_id: ' . $test_id);
            return true;
        } else {
            log_message('error', 'OneCompiler API Error (Update): ' . json_encode($result));
            return false;
        }
    }

    public function create() {

        $data[ 'url' ] = $this->url;
        $class[ 'classname' ] = 'test';
        $class[ 'url' ] = $this->url;
        $class[ 'college_id' ] = $this->college[ 'id' ];
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[ $this->session_data[ 'designation' ] ] ?? 'hod';
        $class[ 'sidebar_href' ] = base_url( $this->url . '/' . $path );

        $data[ 'title' ] = 'Create New Test';
        $data[ 'active_menu' ] = 'tests';
        $data[ 'modules' ] = $this->db_model->get_all( TABLE_TEST_MODULES, [ 'is_active' => 1 ] );
        $data['tests'] = $this->test_model->get_all_tests($this->college['id']);

        if ( $this->input->post() ) {
            $this->form_validation->set_rules( 'title', 'Title', 'required' );
            $this->form_validation->set_rules( 'instructions', 'Instructions', 'required' );
            $this->form_validation->set_rules( 'duration', 'Duration', 'required|numeric|greater_than[0]' );

        $post_data = $this->input->post();
        // print_r($post_data);
        // exit;
        $max_attempts = $post_data['maxattempts'];      
        $negative_value = $post_data['negative_mark_value'];
        $is_new_tab = $post_data['is_new_tab'];
        
        // If value is not null/empty
        if ($max_attempts !== null && $max_attempts !== '') {
            // Convert to integer and make it positive
            $max_attempts = abs((int) $max_attempts);
            // If it's 0 after conversion, default to 1
            if ($max_attempts < 1) {
                $max_attempts = 1;
            }
            // Override POST with corrected value
            $post_data['maxattempts'] = $max_attempts;

            // Apply validation rule
            $this->form_validation->set_rules(
                'maxattempts',
                'Attempts',
                'required|numeric|greater_than_equal_to[1]'
            );
        } else {
            // If empty, default to 1
            $post_data['maxattempts'] = 1;
        }

        // If value is not null/empty
        if ($negative_value !== null && $negative_value !== '') {
            // Convert to float
            $negative_value = (float) $negative_value;

            // If value is positive, convert it to negative
            if ($negative_value > 0) {
                $negative_value = -1 * abs($negative_value);
                $post_data['negative_mark_value'] = $negative_value;
            }

            // Now apply validation
            $this->form_validation->set_rules(
                'negative_mark_value',
                'Negative Mark',
                'required|numeric|less_than_equal_to[0]|greater_than_equal_to[-100]'
            );
        } else {
            // Set default value to 0 if not given
            $post_data['negative_mark_value'] = 0;
        }


            if ( $this->form_validation->run() === TRUE ) {
                $test_id = $this->test_model->create_test( $post_data, $this->session_data[ 'id' ], $this->college[ 'id' ] );

        
                if ( $test_id ) {
                    // create challenge 
                    $this->create_challenge( $test_id );
                    $this->session->set_flashdata( 'message', [ 'success', 'Test created successfully' ] );
                    redirect( $this->url.'/test' );
                } else {
                    // show error 
                    

                    $this->session->set_flashdata( 'message', [ 'danger', 'Error creating test' ] );
                }
            }
        }

        $this->load->view( 'faculty/sidebar', $class );
        $this->load->view( 'test/add', $data );
        $this->load->view( 'faculty/footer' );
    }

    public function edit( $id ) {

        // print_r($id);
        // exit;
        if(!$this->check_permisson($id)){

            redirect( base_url( $this->url . '/test' ) );

        }


        $data[ 'url' ] = $this->url;
        $class[ 'classname' ] = 'test';
        $class[ 'url' ] = $this->url;
        $class[ 'college_id' ] = $this->college[ 'id' ];
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[ $this->session_data[ 'designation' ] ] ?? 'hod';
        $class[ 'sidebar_href' ] = base_url( $this->url . '/' . $path );

        $data[ 'title' ] = 'Edit Test';
        $data[ 'active_menu' ] = 'tests';
        $data[ 'modules' ] = $this->db_model->get_all( TABLE_TEST_MODULES, [ 'is_active' => 1 ] );

        $data[ 'test' ] = $this->test_model->get_test( $id );
        $data['tests'] = $this->test_model->get_all_tests($this->college['id']);


        if ( !$data[ 'test' ] ) {
            $this->session->set_flashdata( 'message', [ 'danger', 'Test not found' ] );
            redirect( base_url( $this->url . '/test' ) );
        }

        // Get test settings and merge them into the test data
        $settings = [
            'navigation' => $this->test_model->get_test_navigation( $id ),
            'security' => $this->test_model->get_test_security( $id ),
            'monitoring' => $this->test_model->get_test_monitoring( $id ),
            'ui' => $this->test_model->get_test_ui( $id )
        ];

        // Merge settings into the main test data
        foreach ( $settings as $key => $value ) {
            if ( $value ) {
                foreach ( $value as $k => $v ) {
                    if ($k === 'id' || $k === 'test_id') {
                        continue; // skip these keys
                    }
                    $data[ 'test' ]-> {
                        $k}
                        = $v;
                    }
                }
            }

            if ( $this->input->post() ) {
                $this->form_validation->set_rules( 'title', 'Title', 'required' );
                $this->form_validation->set_rules( 'instructions', 'Instructions', 'required' );
                $this->form_validation->set_rules( 'duration', 'Duration', 'required|numeric|greater_than[0]' );
                    
                    $edit_post_data = $this->input->post();
                    $max_attempts = $edit_post_data['maxattempts'];      
                    $negative_value = $edit_post_data['negative_mark_value'];
                    $is_new_tab = $edit_post_data['is_new_tab'];
                    // If value is not null/empty
                    if ($max_attempts !== null && $max_attempts !== '') {
                        // Convert to integer and make it positive
                        $max_attempts = abs((int) $max_attempts);
                        // If it's 0 after conversion, default to 1
                        if ($max_attempts < 1) {
                            $max_attempts = 1;
                        }
                        // Override POST with corrected value
                        $edit_post_data['maxattempts'] = $max_attempts;

                        // Apply validation rule
                        $this->form_validation->set_rules(
                            'maxattempts',
                            'Attempts',
                            'required|numeric|greater_than_equal_to[1]'
                        );
                    } else {
                        // If empty, default to 1
                        $edit_post_data['maxattempts'] = 1;
                    }

                    // If value is not null/empty
                    if ($negative_value !== null && $negative_value !== '') {
                        // Convert to float
                        $negative_value = (float) $negative_value;

                        // If value is positive, convert it to negative
                        if ($negative_value > 0) {
                            $negative_value = -1 * abs($negative_value);
                            $edit_post_data['negative_mark_value'] = $negative_value;
                        }

                        // Now apply validation
                        $this->form_validation->set_rules(
                            'negative_mark_value',
                            'Negative Mark',
                            'required|numeric|less_than_equal_to[0]|greater_than_equal_to[-100]'
                        );
                    } else {
                        // Set default value to 0 if not given
                        $edit_post_data['negative_mark_value'] = 0;
                    }

                if ( $this->form_validation->run() === TRUE ) {
                    // Check if enable_sections is being enabled (toggled from false to true)
                    $old_ui = $this->test_model->get_test_ui($id);
                    $old_enable_sections = $old_ui ? ($old_ui->enable_sections ?? 0) : 0;
                    $new_enable_sections = isset($edit_post_data['enable_sections']) ? 1 : 0;
                    
                    // If sections are being enabled AND this is an update (row exists in test_settings_ui)
                    if ($new_enable_sections && !$old_enable_sections && $old_ui) {
                        // Get default unassigned section (id = 1)
                        $default_section = $this->db->where('id', DEFAULT_SECTION_ID)
                            ->get(TABLE_TEST_SECTIONS)
                            ->row();
                        
                        // Assign all existing unassigned questions to the default section
                        if ($default_section) {
                            $this->db->where('test_id', $id);
                            $this->db->where('section_id IS NULL');
                            $this->db->update(TABLE_TEST_QUESTIONS, ['section_id' => $default_section->id]);
                        }
                        
                        // Update OneCompiler with empty arrays for problems and sections
                        $this->common->update_onecompiler_for_sections_enable($id);
                    }
                    
                    $success = $this->test_model->update_test( $id, $edit_post_data, $this->session_data[ 'id' ] );

                    $this->create_challenge( $id );

                    if ( $success ) {
                        $this->session->set_flashdata( 'message', [ 'success', 'Test updated successfully' ] );
                        
                        // Check if sections are enabled - redirect to questions page if true
                        $updated_ui = $this->test_model->get_test_ui($id);
                        if ($updated_ui && $updated_ui->enable_sections) {
                            redirect( base_url( $this->url . '/test/questions/' . $id ) );
                        } else {
                        redirect( base_url( $this->url . '/test' ) );
                        }
                    } else {
                        $this->session->set_flashdata( 'message', [ 'danger', 'Error updating test' ] );
                    }
                }
            }

            $this->load->view( 'faculty/sidebar', $class );
            $this->load->view( 'test/add', $data );
            $this->load->view( 'faculty/footer' );
        }

    public function delete( $id ) {


            if(!$this->check_permisson($id)){

                redirect( base_url( $this->url . '/test' ) );
    
            }

            if ( !$id ) {
                $response = [
                    'status' => 'error',
                    'message' => 'Invalid test ID'
                ];
                echo json_encode( $response );
                return;
            }

            // Check if test exists
            $test = $this->test_model->get_test( $id );
            if ( !$test ) {
                $response = [
                    'status' => 'error',
                    'message' => 'Test not found'
                ];
                echo json_encode( $response );
                return;
            }

            // Delete test and its related settings
            $result = $this->test_model->delete_test( $id );

            if ( $result ) {
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

            echo json_encode( $response );
        }

    public function questions( $test_id ) {

        if(!$this->check_permisson($test_id)){

            redirect( base_url( $this->url . '/test' ) );

        }

        $data[ 'url' ] = $this->url;
        $class[ 'classname' ] = 'test';
        $class[ 'url' ] = $this->url;
        $class[ 'college_id' ] = $this->college[ 'id' ];
        $map = [
            DESIGNATION_STAFF => 'staff',
            DESIGNATION_PRINCIPAL => 'principal'
        ];
        $path = $map[ $this->session_data[ 'designation' ] ] ?? 'hod';
        $class[ 'sidebar_href' ] = base_url( $this->url . '/' . $path );
        // Check if test exists
        $test = $this->test_model->get_test( $test_id );
        if ( !$test ) {
            $this->session->set_flashdata( 'message', [ 'danger', 'Test not found' ] );
            redirect( 'test/view' );
        }

        // Get test questions with order
        $test_questions = $this->test_model->get_test_questions( $test_id );

        $conditions = [
            'q.college_id' => $this->college[ 'id' ],
            'q.is_active' => 1,
        ];

        if ( $this->permissions[ 'read' ] !== 'all' ) {
            if ( is_array( $this->permissions[ 'read' ] ) ) {
                $this->db->where_in( 'q.created_by', $this->permissions[ 'read' ] );
            } else {
                $conditions[ 'q.created_by' ] = $this->permissions[ 'read' ];
            }
        }

        // Get all available questions that are not in the test
        $available_questions = $this->test_model->get_available_questions( $test_id, $conditions );

        // Get question types for filtering
        $question_types = $this->test_model->get_question_types();

        $tags_result = $this->db_model->get_all( TABLE_QUESTION_BANK, [ 'is_active' => true,'college_id' => $this->college['id'] ] );
        $all_tags = [];

        foreach ( $tags_result as $row ) {
            if ( !empty( $row[ 'tags' ] ) ) {
                $tags_array = explode( ',', $row[ 'tags' ] );
                $all_tags = array_merge( $all_tags, $tags_array );
            }
        }

   

        $topics = array_values( array_unique( array_filter( $all_tags ) ) );

        $data['difficulty_levels'] = $this->db_model->get_all(
            TABLE_QUESTION_DIFFICULTY_LEVEL,
            ['is_active' => 1]
        );

        $difficulty_levels = $data['difficulty_levels'];

        // Get sections if sections are enabled
        $sections = [];
        $sections_enabled = false;
        $ui_settings = $this->test_model->get_test_ui($test_id);
        if ($ui_settings && $ui_settings->enable_sections) {
            $sections_enabled = true;
            $sections = $this->test_model->get_test_sections($test_id);
        }

        $data = [
            'title' => 'Test Questions',
            'active_menu' => 'tests',
            'test' => $test,
            'test_questions' => $test_questions,
            'available_questions' => $available_questions,
            'question_types' => $question_types,
            'topics' => $topics,
            'difficulty_levels' => $difficulty_levels,
            'sections' => $sections,
            'sections_enabled' => $sections_enabled

        ];

        $this->load->view( 'faculty/sidebar', $class );
        $this->load->view( 'test/questions', $data );
        $this->load->view( 'faculty/footer' );
    }

    public function add_question() {
        // Check if this is an AJAX request
        if ( !$this->input->is_ajax_request() ) {
            exit( 'No direct script access allowed' );
        }

        $test_id = $this->input->post( 'test_id' );
        $question_id = $this->input->post( 'question_id' );

        if(!$this->check_permisson($test_id)){

            redirect( base_url( $this->url . '/test' ) );

        }

        // Validate inputs
        if ( !$test_id || !$question_id ) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ];
            echo json_encode( $response );
            return;
        }

        // Check if test exists
        $test = $this->test_model->get_test( $test_id );
        if ( !$test ) {
            $response = [
                'status' => 'error',
                'message' => 'Test not found'
            ];
            echo json_encode( $response );
            return;
        }

        // Check if question exists
        $question = $this->db_model->get_where( TABLE_QUESTION_BANK, [ 'id' => $question_id ] );
        if ( !$question ) {
            $response = [
                'status' => 'error',
                'message' => 'Question not found'
            ];
            echo json_encode( $response );
            return;
        }

        // Check if question is already added to the test
        $existing = $this->db_model->get_where( TABLE_TEST_QUESTIONS, [
            'test_id' => $test_id,
            'question_id' => $question_id
        ] );

        if ( $existing ) {
            $response = [
                'status' => 'error',
                'message' => 'Question is already added to this test'
            ];
            echo json_encode( $response );
            return;
        }

        // Get the current highest order
        $max_order = $this->db_model->get_max( TABLE_TEST_QUESTIONS, 'question_order', [ 'test_id' => $test_id ] );
        $new_order = $max_order ? $max_order + 1 : 1;

        // Get section_id if provided and validate it
        $section_id = $this->input->post( 'section_id' );
        if ($section_id && $section_id !== '') {
            // Validate that section exists and is active
            $section = $this->db_model->get_where(TABLE_TEST_SECTIONS, [
                'id' => $section_id,
                'is_active' => 1
            ]);
            
            if (!$section) {
                // Section doesn't exist or is not active
                $section_id = null;
            }
        } else {
            $section_id = null;
        }

        // Add question to test
        $data = [
            'test_id' => $test_id,
            'question_id' => $question_id,
            'question_order' => $new_order,
            'section_id' => $section_id,  // Already validated above
            'is_active' => 1,
            'created_by' => $this->session_data[ 'id' ]
        ];

        $result = $this->db_model->insert( TABLE_TEST_QUESTIONS, $data );

        if ( $result ) {
            $this->create_challenge( $test_id );
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

        echo json_encode( $response );
    }

    public function add_questions() {
        // Check if this is an AJAX request
        if ( !$this->input->is_ajax_request() ) {
            exit( 'No direct script access allowed' );
        }

        $test_id = $this->input->post( 'test_id' );
        $question_ids = $this->input->post( 'question_ids' );
        
        if(!$this->check_permisson($test_id)){

            redirect( base_url( $this->url . '/test' ) );

        }

        // Validate inputs
        if ( !$test_id || !$question_ids || !is_array( $question_ids ) ) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ];
            echo json_encode( $response );
            return;
        }

        // Check if test exists
        $test = $this->test_model->get_test( $test_id );
        if ( !$test ) {
            $response = [
                'status' => 'error',
                'message' => 'Test not found'
            ];
            echo json_encode( $response );
            return;
        }

        // Get section_id if provided and validate it (must be active if provided)
        $section_id = $this->input->post('section_id');
        if ($section_id && $section_id !== '') {
            $section = $this->db_model->get_where(TABLE_TEST_SECTIONS, [
                'id' => $section_id,
                'is_active' => 1
            ]);
            if (!$section) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid section selected.'
                ]);
                return;
            }
        } else {
            $section_id = null;
        }

        // Pre-validate ALL questions first; if any fail, abort without inserting
        $invalid_ids = [];
        $duplicate_ids = [];
        $valid_ids = [];

        // Build a set of provided IDs for faster checks
        $all_ids = array_values(array_unique(array_map('intval', $question_ids)));

        // Check existence for all questions in one query
        $this->db->select('id');
        $this->db->from(TABLE_QUESTION_BANK);
        $this->db->where_in('id', $all_ids);
        $existing_questions = array_column($this->db->get()->result_array(), 'id');
        $existing_set = array_flip($existing_questions);

        foreach ($all_ids as $qid) {
            if (!isset($existing_set[$qid])) {
                $invalid_ids[] = $qid;
            }
        }

        if (!empty($invalid_ids)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Some questions do not exist: ' . implode(',', $invalid_ids)
            ]);
            return;
        }

        // Check duplicates already in test in one query
        $this->db->select('question_id');
        $this->db->from(TABLE_TEST_QUESTIONS);
        $this->db->where('test_id', $test_id);
        $this->db->where_in('question_id', $all_ids);
        $already_rows = array_column($this->db->get()->result_array(), 'question_id');
        $already_set = array_flip($already_rows);
        foreach ($all_ids as $qid) {
            if (isset($already_set[$qid])) {
                $duplicate_ids[] = $qid;
            } else {
                $valid_ids[] = $qid;
            }
        }

        if (!empty($duplicate_ids)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'These questions are already in the test: ' . implode(',', $duplicate_ids)
            ]);
            return;
        }

        // Compute starting order only once
        $max_order = $this->db_model->get_max(TABLE_TEST_QUESTIONS, 'question_order', ['test_id' => $test_id]);
        $start_order = $max_order ? ($max_order + 1) : 1;
        $batch_rows = [];
        foreach ($valid_ids as $qid) {
            $batch_rows[] = [
                'test_id' => $test_id,
                'question_id' => $qid,
                'question_order' => $start_order++,
                'section_id' => $section_id,
                'is_active' => 1,
                'created_by' => $this->session_data['id']
            ];
        }
        // Perform batch insert inside a transaction
        $this->db->trans_start();
        $ok = !empty($batch_rows) ? $this->db_model->bulk_insert(TABLE_TEST_QUESTIONS, $batch_rows) : false;
        $this->db->trans_complete();

        if ($ok && $this->db->trans_status()) {
            $this->create_challenge($test_id);
            echo json_encode([
                'status' => 'success',
                'message' => count($batch_rows) . ' question(s) added successfully'
            ]);
            return;
        }

        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add questions'
        ]);
    }

    public function remove_question() {
            // Check if this is an AJAX request
            if ( !$this->input->is_ajax_request() ) {
                exit( 'No direct script access allowed' );
            }

            $test_question_id = $this->input->post( 'test_question_id' );

            // Validate inputs
            if ( !$test_question_id ) {
                $response = [
                    'status' => 'error',
                    'message' => 'Invalid input parameters'
                ];
                echo json_encode( $response );
                return;
            }

            // Get the test_id directly with a safe query approach
            $this->db->select( 'test_id' );
            $this->db->where( 'id', $test_question_id );
            $query = $this->db->get( TABLE_TEST_QUESTIONS );
            $test_question_row = $query->row();

            if ( !$test_question_row ) {
                $response = [
                    'status' => 'error',
                    'message' => 'Question not found in test'
                ];
                echo json_encode( $response );
                return;
            }

            // Store the test_id
            $test_id = $test_question_row->test_id;

            
            if(!$this->check_permisson($test_id)){

                redirect( base_url( $this->url . '/test' ) );

            }

            // Remove question from test
            $result = $this->db_model->delete( TABLE_TEST_QUESTIONS, [ 'id' => $test_question_id ] );

            if ( $result ) {
                // Reorder remaining questions
                $this->reorder_test_questions( $test_id );

                // $this->create_challenge( $test_id );

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

            echo json_encode( $response );
        }

    public function save_question_order() {
        // Check if this is an AJAX request
        if ( !$this->input->is_ajax_request() ) {
            exit( 'No direct script access allowed' );
        }

        $test_id = $this->input->post( 'test_id' );
        $question_order = json_decode( $this->input->post( 'question_order' ), true );

        // Validate inputs
        if ( !$test_id || !$question_order || !is_array( $question_order ) ) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ];
            echo json_encode( $response );
            return;
        }

        // Check if test exists
        $test = $this->test_model->get_test( $test_id );
        if ( !$test ) {
            $response = [
                'status' => 'error',
                'message' => 'Test not found'
            ];
            echo json_encode( $response );
            return;
        }

        // Start transaction
        $this->db->trans_start();

        $success_count = 0;

        foreach ( $question_order as $item ) {
            $test_question_id = $item[ 'test_question_id' ];
            $order = $item[ 'order' ];

            // Update question order
            $result = $this->db_model->update(
                TABLE_TEST_QUESTIONS,
                [ 'question_order' => $order, 'updated_by' => $this->session_data[ 'id' ] ],
                [ 'id' => $test_question_id, 'test_id' => $test_id ]
            );

            if ( $result ) {
                $success_count++;
            }
        }

        // Complete transaction
        $this->db->trans_complete();

        if ( $this->db->trans_status() && $success_count > 0 ) {
            // Always call create_challenge first
            $this->create_challenge( $test_id );
            
            // Then check if sections are enabled for this test
            $ui = $this->db->get_where( TABLE_TEST_SETTINGS_UI, [ 'test_id' => $test_id ] )->row();
            
            if ( $ui && $ui->enable_sections ) {
                // If sections are enabled, call update_challenge_with_sections
                $this->update_challenge_with_sections( $test_id );
            }
            
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

        echo json_encode( $response );
    }

    private function reorder_test_questions( $test_id ) {
        // Get all questions for this test, ordered by current order
        $questions = $this->db->select( 'id' )
        ->from( TABLE_TEST_QUESTIONS )
        ->where( 'test_id', $test_id )
        ->where( 'is_active', 1 )
        ->order_by( 'question_order', 'ASC' )
        ->get()
        ->result();

        // Reorder questions
        $order = 1;
        foreach ( $questions as $question ) {
            $this->db_model->update(
                TABLE_TEST_QUESTIONS,
                [ 'question_order' => $order, 'updated_by' => $this->session_data[ 'id' ] ],
                [ 'id' => $question->id ]
            );
            $order++;
        }

        return true;
    }

    public function upload_image()
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

        $image_url = $this->common->upload_to_cloudinary('image', 'test_images');

        if ($image_url) {
            $response = [
                'success' => true,  
                'file_url' => $image_url  
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

    public function remove_all_questions() {
        // Check if this is an AJAX request
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $test_id = $this->input->post('test_id');

        
        if(!$this->check_permisson($test_id)){

            redirect( base_url( $this->url . '/test' ) );

        }

        // Validate inputs
        if (!$test_id) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid test ID'
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

        // Remove all questions from test (wrap in transaction for atomicity)
        $this->db->trans_start();
        $this->db_model->delete(TABLE_TEST_QUESTIONS, ['test_id' => $test_id]);
        $this->db->trans_complete();
        $result = $this->db->trans_status();

        if ($result) {
            // $this->create_challenge($test_id);
            $response = [
                'status' => 'success',
                'message' => 'All questions removed successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to remove questions'
            ];
        }

        echo json_encode($response);
    }

    /**
     * Section Management Endpoints
     */

    public function get_sections($test_id) {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        if (!$this->check_permisson($test_id)) {
            $response = [
                'status' => 'error',
                'message' => 'Permission denied'
            ];
            echo json_encode($response);
            return;
        }

        $sections = $this->test_model->get_test_sections($test_id);
        
        // Get sections with question counts using LEFT JOIN
        if (!empty($sections)) {
            $this->db->select('s.id, s.section_name, s.is_active, COUNT(tq.id) as question_count');
            $this->db->from(TABLE_TEST_SECTIONS . ' s');
            $this->db->join(TABLE_TEST_QUESTIONS . ' tq', 's.id = tq.section_id AND tq.test_id = ' . $this->db->escape($test_id) . ' AND tq.is_active = 1', 'left');
            $this->db->where_in('s.id', array_column($sections, 'id'));
            $this->db->group_by('s.id, s.section_name, s.is_active');
            $sections = $this->db->get()->result_array();
        }
        $response = [
            'status' => 'success',
            'sections' => $sections
        ];

        echo json_encode($response);
    }

    public function create_section() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $test_id = $this->input->post('test_id');
        $section_name = $this->input->post('section_name');
        // print_r($test_id);
        // print_r($section_name);
        // exit;
        if (!$test_id || !$section_name) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ];
            echo json_encode($response);
            return;
        }

        if (!$this->check_permisson($test_id)) {
            $response = [
                'status' => 'error',
                'message' => 'Permission denied'
            ];
            echo json_encode($response);
            return;
        }

        // Check if section name already exists globally in test_sections table
        $trimmed_name = trim($section_name);
        $existing_sections = $this->db_model->get_where(TABLE_TEST_SECTIONS, [
            'section_name' => $trimmed_name,
            'is_active' => 1
        ]);
        
        if (!empty($existing_sections) && is_array($existing_sections)) {
            // Section already exists, return its ID
            $section_id = $existing_sections[0]['id'];
        } else {
            // Create new section
            $data = [
                'section_name' => $trimmed_name
            ];
            $section_id = $this->test_model->create_section($data, $this->session_data['id']);
        }

        if ($section_id) {
            log_message('info', 'Section created - Test ID: ' . $test_id . ', Section ID: ' . $section_id . ', Section Name: ' . $section_name);
            // $this->update_challenge_with_sections($test_id);
            $response = [
                'status' => 'success',
                'message' => 'Section created successfully',
                'section_id' => $section_id,
                'section_name' => $trimmed_name
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to create section'
            ];
        }

        echo json_encode($response);
    }

    public function update_section() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $section_id = $this->input->post('section_id');
        $section_name = $this->input->post('section_name');

        if (!$section_id || !$section_name) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ];
            echo json_encode($response);
            return;
        }

        // Get section and check permissions
        $section = $this->test_model->get_section($section_id);
        if (!$section) {
            $response = [
                'status' => 'error',
                'message' => 'Section not found'
            ];
            echo json_encode($response);
            return;
        }
        
        // Get test_id for this section to check permissions
        $test_id = $this->test_model->get_test_id_for_section($section_id);
        if (!$test_id || !$this->check_permisson($test_id)) {
            $response = [
                'status' => 'error',
                'message' => 'Permission denied'
            ];
            echo json_encode($response);
            return;
        }
        
        // Prevent editing the default unassigned section
        if ($this->test_model->is_default_unassigned_section($section)) {
            $response = [
                'status' => 'error',
                'message' => 'Cannot modify the default unassigned section'
            ];
            echo json_encode($response);
            return;
        }

        $data = [
            'section_name' => trim($section_name)
        ];

        $result = $this->test_model->update_section($section_id, $data, $this->session_data['id']);

        if ($result) {
            log_message('info', 'Section updated - Section ID: ' . $section_id . ', New Name: ' . $section_name . ', Test ID: ' . $test_id);
            // $this->update_challenge_with_sections($test_id);
            $response = [
                'status' => 'success',
                'message' => 'Section updated successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to update section'
            ];
        }

        echo json_encode($response);
    }

    public function delete_section() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $section_id = $this->input->post('section_id');

        if (!$section_id) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid section ID'
            ];
            echo json_encode($response);
            return;
        }

        // Get section and check permissions
        $section = $this->test_model->get_section($section_id);
        if (!$section) {
            $response = [
                'status' => 'error',
                'message' => 'Section not found'
            ];
            echo json_encode($response);
            return;
        }
        
        // Get test_id for this section to check permissions
        $test_id = $this->test_model->get_test_id_for_section($section_id);
        if (!$test_id || !$this->check_permisson($test_id)) {
            $response = [
                'status' => 'error',
                'message' => 'Permission denied'
            ];
            echo json_encode($response);
            return;
        }
        
        // Prevent deleting the default unassigned section
        if ($this->test_model->is_default_unassigned_section($section)) {
            $response = [
                'status' => 'error',
                'message' => 'Cannot delete the default unassigned section'
            ];
            echo json_encode($response);
            return;
        }

        $result = $this->test_model->delete_section($section_id);

        if ($result) {
            log_message('info', 'Section deleted - Section ID: ' . $section_id . ', Test ID: ' . $test_id);
            // $this->update_challenge_with_sections($test_id);
            $response = [
                'status' => 'success',
                'message' => 'Section deleted successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to delete section'
            ];
        }

        echo json_encode($response);
    }

    public function assign_question_to_section() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $test_question_id = $this->input->post('test_question_id');
        $section_id = $this->input->post('section_id');

        if (!$test_question_id) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ];
            echo json_encode($response);
            return;
        }

        // Get test_question to check test_id
        $this->db->select('test_id');
        $this->db->where('id', $test_question_id);
        $query = $this->db->get(TABLE_TEST_QUESTIONS);
        $test_question = $query->row();

        if (!$test_question || !$this->check_permisson($test_question->test_id)) {
            $response = [
                'status' => 'error',
                'message' => 'Question not found or permission denied'
            ];
            echo json_encode($response);
            return;
        }

        $result = $this->test_model->assign_question_to_section($test_question_id, $section_id, $this->session_data['id']);

        if ($result) {
            log_message('info', 'Question assigned to section - Test Question ID: ' . $test_question_id . ', Section ID: ' . $section_id . ', Test ID: ' . $test_question->test_id);
            // $this->update_challenge_with_sections($test_question->test_id);
            $response = [
                'status' => 'success',
                'message' => 'Question assigned to section successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to assign question to section'
            ];
        }

        echo json_encode($response);
    }

    public function remove_question_from_section() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $test_question_id = $this->input->post('test_question_id');

        if (!$test_question_id) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ];
            echo json_encode($response);
            return;
        }

        // Get test_question to check test_id
        $this->db->select('test_id');
        $this->db->where('id', $test_question_id);
        $query = $this->db->get(TABLE_TEST_QUESTIONS);
        $test_question = $query->row();

        if (!$test_question || !$this->check_permisson($test_question->test_id)) {
            $response = [
                'status' => 'error',
                'message' => 'Question not found or permission denied'
            ];
            echo json_encode($response);
            return;
        }

        $result = $this->test_model->remove_question_from_section($test_question_id, $this->session_data['id']);

        if ($result) {
            log_message('info', 'Question removed from section - Test Question ID: ' . $test_question_id . ', Test ID: ' . $test_question->test_id);
            $this->update_challenge_with_sections($test_question->test_id);
            $response = [
                'status' => 'success',
                'message' => 'Question removed from section successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to remove question from section'
            ];
        }

        echo json_encode($response);
    }

    }

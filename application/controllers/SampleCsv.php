<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SampleCsv extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->common->check_user_session();
    }

    public function download_sample_csv_principal() {
        // Define CSV headers
        $header = [
            'name', 'email', 'phone_number', 'password','joining_date'
        ];

        // Open output stream
        $filename = 'sample.csv';
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen('php://output', 'w');

        // Insert headers
        fputcsv($output, $header);

        // Close the stream
        fclose($output);
        exit;
    }
    public function download_sample_csv_hod() {
        // Define CSV headers
        $header = [
            'name', 'department','other_department','email', 'phone_number', 'password','joining_date'
        ];

        // Open output stream
        $filename = 'sample.csv';
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen('php://output', 'w');

        // Insert headers
        fputcsv($output, $header);

        // Close the stream
        fclose($output);
        exit;
    }
    public function download_sample_csv_faculty() {
        // Define CSV headers
        $header = [
            'name', 'department','email', 'phone_number', 'password','joining_date'
        ];

        // Open output stream
        $filename = 'sample.csv';
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen('php://output', 'w');

        // Insert headers
        fputcsv($output, $header);

        // Close the stream
        fclose($output);
        exit;
    }
    public function download_sample_csv_student() {
        // Define CSV headers
        $header = [
            'name', 'department','email', 'phone_number', 'password','joining_date',"expire_date","batch","registration_number"
        ];

        // Open output stream
        $filename = 'sample.csv';
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen('php://output', 'w');

        // Insert headers
        fputcsv($output, $header);

        // Close the stream
        fclose($output);
        exit;
    }

    public function download_sample_csv_questions() { 
        $header = [
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
            'is_correct_1',
            'is_correct_2',
            'is_correct_3',
            'is_correct_4',
            'tags'
        ];
    
        // Open output stream
        $filename = 'sample_questions.csv';
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        $output = fopen('php://output', 'w');
    
        fputcsv($output, $header);
        
        // $sample_row = [
        //     'What is the capital of France?', // question_content
        //     '1',                              // type (1 for single answer, 2 for multiple)
        //     '1',                              // is_public (1 for public, 0 for private)
        //     'Paris',                          // option_1
        //     'London',                         // option_2
        //     'Berlin',                         // option_3
        //     'Madrid',                         // option_4
        //     '1',                              // is_correct_1 (1 for correct, 0 for incorrect)
        //     '0',                              // is_correct_2
        //     '0',                              // is_correct_3
        //     '0',                              // is_correct_4
        //     'Paris is the capital of France', // explanation_1
        //     '',                               // explanation_2
        //     '',                               // explanation_3
        //     ''                                // explanation_4
        // ];
        
        // Close the stream
        fclose($output);
        exit;
    }
}
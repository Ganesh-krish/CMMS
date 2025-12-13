<?php
defined('BASEPATH') or exit('No direct script access allowed');

class College extends CI_Controller
{
    public function __construct()
    {
        
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
        $this->load->model('Db_model', 'db_model');
        $this->load->model('common', 'common');
        
     
    }

    public function index($id = null)
    {
        $college = $this->db_model->get_row(TABLE_COLLEGE, ["is_active" => 1, "id" => SINGLE_COLLEGE_ID]);
    
        if (!$college) {
                $this->_send_response(404, [
                    'status' => 'error',
                    'message' => 'College not found'
                ]);
                return;
        }

        $response = [
            'name' => $college['name'],
            'logo' => $college['logo'],
            'banner' =>$college['banner']
        ];

        $this->_send_response(200, [
                'status' => 'success',
                'data' => $response
        ]);
        
    }

    /**
     * Update college logo or banner
     * POST /college/update_image
     */
    public function update_image()
    {
        if ($this->input->method() !== 'post') {
            $this->_send_response(405, [
                'status' => 'error',
                'message' => 'Method not allowed'
            ]);
            return;
        }

        $college_id = $this->input->post('college_id');
        $image_type = $this->input->post('image_type'); // 'logo' or 'banner'

        if (!$college_id || !in_array($image_type, ['logo', 'banner'])) {
            $this->_send_response(400, [
                'status' => 'error',
                'message' => 'Invalid parameters'
            ]);
            return;
        }

        // Upload image to Cloudinary
        $image_url = $this->common->upload_to_cloudinary('image', 'college/' . $image_type);
        
        if (!$image_url) {
            $this->_send_response(500, [
                'status' => 'error',
                'message' => 'Failed to upload image'
            ]);
            return;
        }

        // Update college record with new image URL
        $update_data = [
            $image_type => $image_url
        ];

        if ($this->db_model->update(TABLE_COLLEGE, $update_data, ['id' => $college_id])) {
            $this->_send_response(200, [
                'status' => 'success',
                'message' => ucfirst($image_type) . ' updated successfully',
                'data' => [
                    'url' => $image_url
                ]
            ]);
        } else {
            $this->_send_response(500, [
                'status' => 'error',
                'message' => 'Failed to update college record'
            ]);
        }
    }

    /**
     * Helper method to send JSON response
     */
    private function _send_response($status_code, $data)
    {
        header('Content-Type: application/json');
        http_response_code($status_code);
        echo json_encode($data);
        exit;
    }
}

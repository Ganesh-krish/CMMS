<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Batches extends CI_Controller
{
    private $url;
    private $session_data;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('common', 'common');
        $this->load->model('faculty/db_model', 'db_model');
        $this->load->model('Batch_model', 'batches');

        $this->url = $this->uri->segment(1);
        $this->session_data = $this->rbac->require_faculty($this->url, [
            DESIGNATION_PRINCIPAL,
            DESIGNATION_HOD,
            DESIGNATION_STAFF
        ]);
    }

    private function json_response($status, $data = [], $http_code = 200)
    {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => $status, 'data' => $data]));
    }

    public function index($course_id)
    {
        $rows = $this->batches->list_by_course($course_id);
        return $this->json_response('success', $rows);
    }

    public function create()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $course_id = (int)$this->input->post('course_id');
        $name = $this->input->post('name');
        if (!$course_id || !$name) {
            return $this->json_response('error', 'course_id and name are required', 400);
        }

        $payload = [
            'course_id' => $course_id,
            'name' => $name,
            'schedule_text' => $this->input->post('schedule_text'),
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
            'created_by' => $this->session_data['id'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $id = $this->batches->create_batch($payload);
        return $this->json_response('success', ['id' => $id], 201);
    }

    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $payload = array_filter([
            'name' => $this->input->post('name'),
            'schedule_text' => $this->input->post('schedule_text'),
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
        ], static function ($value) {
            return $value !== null && $value !== '';
        });

        $this->batches->update_batch($id, $payload);
        return $this->json_response('success', ['id' => $id]);
    }

    public function schedules($batch_id)
    {
        $rows = $this->batches->list_schedules($batch_id);
        return $this->json_response('success', $rows);
    }

    public function add_schedule()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $batch_id = (int)$this->input->post('batch_id');
        $module_id = (int)$this->input->post('module_id');
        if (!$batch_id || !$module_id) {
            return $this->json_response('error', 'batch_id and module_id are required', 400);
        }

        $payload = [
            'batch_id' => $batch_id,
            'module_id' => $module_id,
            'teacher_id' => $this->input->post('teacher_id'),
            'start_at' => $this->input->post('start_at'),
            'end_at' => $this->input->post('end_at'),
            'room' => $this->input->post('room'),
            'recurrence' => $this->input->post('recurrence'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $id = $this->batches->add_schedule($payload);
        return $this->json_response('success', ['id' => $id], 201);
    }
}



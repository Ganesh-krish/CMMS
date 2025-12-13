<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lessons extends CI_Controller
{
    private $url;
    private $college;
    private $session_data;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('common', 'common');
        $this->load->model('faculty/db_model', 'db_model');
        $this->load->model('Lesson_model', 'lessons');
        $this->url = $this->uri->segment(1);
        $this->session_data = $this->rbac->require_faculty($this->url, [
            DESIGNATION_PRINCIPAL,
            DESIGNATION_HOD,
            DESIGNATION_STAFF
        ]);
        $this->college = $this->common->get_default_college();
    }

    private function respond($status, $data = [], $code = 200)
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => $status, 'data' => $data]));
    }

    public function index()
    {
        $module_id = (int)$this->input->get('module_id');
        if (!$module_id) {
            return $this->respond('error', 'module_id is required', 400);
        }
        $rows = $this->lessons->list_by_module($module_id);
        return $this->respond('success', $rows);
    }

    public function by_module($module_id)
    {
        $rows = $this->lessons->list_by_module($module_id);
        return $this->respond('success', $rows);
    }

    public function create()
    {
        if ($this->input->method() !== 'post') {
            return $this->respond('error', 'Invalid method', 405);
        }
        $module_id = (int)$this->input->post('module_id');
        $title = $this->input->post('title');
        if (!$module_id || !$title) {
            return $this->respond('error', 'module_id and title are required', 400);
        }

        $payload = [
            'module_id' => $module_id,
            'title' => $title,
            'body_text' => $this->input->post('body_text'),
            'video_url' => $this->input->post('video_url'),
            'attachment_url' => $this->input->post('attachment_url'),
            'sort_order' => (int)$this->input->post('sort_order'),
            'is_published' => $this->input->post('is_published') !== null ? (int)$this->input->post('is_published') : 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $id = $this->lessons->create($payload);
        return $this->respond('success', ['id' => $id], 201);
    }

    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->respond('error', 'Invalid method', 405);
        }

        $payload = array_filter([
            'title' => $this->input->post('title'),
            'body_text' => $this->input->post('body_text'),
            'video_url' => $this->input->post('video_url'),
            'attachment_url' => $this->input->post('attachment_url'),
            'sort_order' => $this->input->post('sort_order'),
            'is_published' => $this->input->post('is_published')
        ], static function($v) {
            return $v !== null && $v !== '';
        });

        $this->lessons->update($id, $payload);
        return $this->respond('success', ['id' => $id]);
    }

    public function delete($id)
    {
        $this->lessons->delete($id);
        return $this->respond('success', ['id' => $id]);
    }

    public function upload_attachment()
    {
        if (empty($_FILES['file']['name'])) {
            return $this->respond('error', 'No file uploaded', 400);
        }

        $config['upload_path'] = 'application/uploads/lessons';
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }
        $config['allowed_types'] = 'pdf|doc|docx|ppt|pptx|jpg|jpeg|png';
        $config['max_size'] = 10240; // 10MB
        $config['file_name'] = uniqid('lesson_', true);

        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('file')) {
            return $this->respond('error', $this->upload->display_errors('', ''), 400);
        }
        $data = $this->upload->data();
        $url = base_url($config['upload_path'] . '/' . $data['file_name']);
        return $this->respond('success', ['attachment_url' => $url], 201);
    }
}


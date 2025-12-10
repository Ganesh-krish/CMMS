<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends CI_Controller
{
    private $url;
    private $college;
    private $session_data;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('faculty/common', 'common');
        $this->load->model('faculty/db_model', 'db_model');
        $this->load->model('Inventory_model', 'inventory');

        $this->url = $this->uri->segment(1);
        $this->session_data = $this->rbac->require_faculty($this->url, [
            DESIGNATION_PRINCIPAL,
            DESIGNATION_HOD,
            DESIGNATION_STAFF
        ]);
        $this->college = $this->common->get_college_by_url($this->url);
    }

    private function json_response($status, $data = [], $http_code = 200)
    {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => $status, 'data' => $data]));
    }

    public function index()
    {
        $filters = [
            'college_id' => $this->college['id'] ?? null,
            'availability_status' => $this->input->get('status'),
            'category' => $this->input->get('category'),
            'search' => $this->input->get('search'),
        ];

        $items = $this->inventory->list_instruments($filters);
        return $this->json_response('success', $items);
    }

    public function create()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $payload = [
            'name' => $this->input->post('name'),
            'category' => $this->input->post('category'),
            'serial_no' => $this->input->post('serial_no'),
            'condition_notes' => $this->input->post('condition_notes'),
            'purchase_date' => $this->input->post('purchase_date'),
            'location' => $this->input->post('location'),
            'availability_status' => $this->input->post('availability_status') ?? 'available',
            'notes' => $this->input->post('notes'),
            'college_id' => $this->college['id'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (empty($payload['name'])) {
            return $this->json_response('error', 'Instrument name is required', 400);
        }

        $id = $this->inventory->create_instrument($payload);
        return $this->json_response('success', ['id' => $id], 201);
    }

    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $payload = array_filter([
            'name' => $this->input->post('name'),
            'category' => $this->input->post('category'),
            'serial_no' => $this->input->post('serial_no'),
            'condition_notes' => $this->input->post('condition_notes'),
            'purchase_date' => $this->input->post('purchase_date'),
            'location' => $this->input->post('location'),
            'availability_status' => $this->input->post('availability_status'),
            'notes' => $this->input->post('notes'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], static function ($value) {
            return $value !== null && $value !== '';
        });

        $instrument = $this->inventory->get_instrument($id);
        if (!$instrument) {
            return $this->json_response('error', 'Instrument not found', 404);
        }

        $this->inventory->update_instrument($id, $payload);
        return $this->json_response('success', ['id' => $id]);
    }

    public function issue()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $instrument_id = (int)$this->input->post('instrument_id');
        $issued_to_student_id = $this->input->post('issued_to_student_id');
        $issued_to_staff_id = $this->input->post('issued_to_staff_id');

        if (!$instrument_id || (!$issued_to_student_id && !$issued_to_staff_id)) {
            return $this->json_response('error', 'Instrument and recipient are required', 400);
        }

        $payload = [
            'college_id' => $this->college['id'] ?? null,
            'instrument_id' => $instrument_id,
            'issued_to_student_id' => $issued_to_student_id,
            'issued_to_staff_id' => $issued_to_staff_id,
            'issued_by_staff_id' => $this->session_data['id'] ?? null,
            'issue_date' => date('Y-m-d H:i:s'),
            'due_date' => $this->input->post('due_date'),
            'status' => 'issued',
            'remarks' => $this->input->post('remarks'),
        ];

        $transaction_id = $this->inventory->issue_instrument($payload);
        if (!$transaction_id) {
            return $this->json_response('error', 'Unable to issue instrument', 500);
        }

        return $this->json_response('success', ['transaction_id' => $transaction_id], 201);
    }

    public function return_item()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $transaction_id = (int)$this->input->post('transaction_id');
        $instrument_id = (int)$this->input->post('instrument_id');
        if (!$transaction_id || !$instrument_id) {
            return $this->json_response('error', 'Transaction and instrument are required', 400);
        }

        $data = [
            'status' => 'returned',
            'return_date' => date('Y-m-d'),
            'condition_on_return' => $this->input->post('condition_on_return'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $ok = $this->inventory->return_instrument($transaction_id, $instrument_id, $data);
        if (!$ok) {
            return $this->json_response('error', 'Unable to close transaction', 500);
        }

        return $this->json_response('success', ['transaction_id' => $transaction_id]);
    }

    public function maintenance()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $instrument_id = (int)$this->input->post('instrument_id');
        if (!$instrument_id) {
            return $this->json_response('error', 'Instrument is required', 400);
        }

        $payload = [
            'college_id' => $this->college['id'] ?? null,
            'instrument_id' => $instrument_id,
            'type' => $this->input->post('type'),
            'description' => $this->input->post('description'),
            'status' => $this->input->post('status') ?: 'open',
            'cost' => $this->input->post('cost'),
            'started_at' => $this->input->post('started_at'),
            'completed_at' => $this->input->post('completed_at'),
            'technician' => $this->input->post('technician'),
            'next_due_date' => $this->input->post('next_due_date'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $id = $this->inventory->log_maintenance($payload);
        return $this->json_response('success', ['id' => $id], 201);
    }
}


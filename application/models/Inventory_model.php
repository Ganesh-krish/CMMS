<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ============================
    // INSTRUMENT MANAGEMENT
    // ============================

    public function list_instruments($filters = [])
    {
        $this->db->from(TABLE_INSTRUMENTS);

        if (!empty($filters['college_id'])) {
            $this->db->where('college_id', $filters['college_id']);
        }
        if (!empty($filters['availability_status'])) {
            $this->db->where('availability_status', $filters['availability_status']);
        }
        if (!empty($filters['category'])) {
            $this->db->where('category', $filters['category']);
        }
        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('name', $filters['search'])
                ->or_like('serial_no', $filters['search'])
                ->or_like('model', $filters['search'])
                ->group_end();
        }

        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function create_instrument($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert(TABLE_INSTRUMENTS, $data);
        return $this->db->insert_id();
    }

    public function update_instrument($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update(TABLE_INSTRUMENTS, $data);
    }

    public function get_instrument($id)
    {
        return $this->db->get_where(TABLE_INSTRUMENTS, ['id' => $id])->row_array();
    }

    public function delete_instrument($id)
    {
        $this->db->where('id', $id);
        return $this->db->update(TABLE_INSTRUMENTS, [
            'is_active' => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    // ============================
    // INSTRUMENT ISSUE/RETURN
    // ============================

    public function issue_instrument($data)
    {
        $this->db->trans_start();

        // Insert issue record
        $issue_data = [
            'instrument_id' => $data['instrument_id'],
            'issued_to' => $data['issued_to'],
            'issued_by' => $data['issued_by'],
            'issue_date' => $data['issue_date'] ?? date('Y-m-d H:i:s'),
            'expected_return_date' => $data['expected_return_date'],
            'purpose' => $data['purpose'] ?? null,
            'status' => 'issued',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert(TABLE_INSTRUMENT_ISSUES, $issue_data);
        $issue_id = $this->db->insert_id();

        // Update instrument status
        $this->db->where('id', $data['instrument_id'])
            ->update(TABLE_INSTRUMENTS, [
                'availability_status' => 'issued',
                'current_issue_id' => $issue_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->db->trans_complete();
        return $this->db->trans_status() ? $issue_id : false;
    }

    public function return_instrument($issue_id, $return_data = [])
    {
        $this->db->trans_start();

        // Get issue details
        $issue = $this->db->get_where(TABLE_INSTRUMENT_ISSUES, ['id' => $issue_id])->row_array();

        if (!$issue) {
            $this->db->trans_rollback();
            return false;
        }

        // Update issue record with return details
        $update_data = [
            'return_date' => $return_data['return_date'] ?? date('Y-m-d H:i:s'),
            'received_by' => $return_data['received_by'] ?? null,
            'condition_on_return' => $return_data['condition_on_return'] ?? null,
            'notes' => $return_data['notes'] ?? null,
            'status' => 'returned',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $issue_id)->update(TABLE_INSTRUMENT_ISSUES, $update_data);

        // Update instrument status
        $this->db->where('id', $issue['instrument_id'])
            ->update(TABLE_INSTRUMENTS, [
                'availability_status' => 'available',
                'current_issue_id' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_instrument_issues($instrument_id = null, $filters = [])
    {
        $this->db->from(TABLE_INSTRUMENT_ISSUES);

        if ($instrument_id) {
            $this->db->where('instrument_id', $instrument_id);
        }

        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        if (!empty($filters['issued_to'])) {
            $this->db->where('issued_to', $filters['issued_to']);
        }

        $this->db->order_by('issue_date', 'DESC');
        return $this->db->get()->result_array();
    }

    // ============================
    // MAINTENANCE LOG
    // ============================

    public function log_maintenance($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(TABLE_INSTRUMENT_MAINTENANCE, $data);
        return $this->db->insert_id();
    }

    public function get_maintenance_logs($instrument_id = null, $filters = [])
    {
        $this->db->from(TABLE_INSTRUMENT_MAINTENANCE);

        if ($instrument_id) {
            $this->db->where('instrument_id', $instrument_id);
        }

        if (!empty($filters['type'])) {
            $this->db->where('maintenance_type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function update_maintenance_status($maintenance_id, $status, $notes = null)
    {
        $data = [
            'status' => $status,
            'completion_notes' => $notes,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status === 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('id', $maintenance_id);
        return $this->db->update(TABLE_INSTRUMENT_MAINTENANCE, $data);
    }

    // ============================
    // DASHBOARD & REPORTS
    // ============================

    public function get_inventory_stats($college_id)
    {
        $stats = [];

        // Total instruments
        $stats['total_instruments'] = $this->db->where('college_id', $college_id)
                                              ->where('is_active', 1)
                                              ->count_all_results(TABLE_INSTRUMENTS);

        // Available instruments
        $stats['available_instruments'] = $this->db->where('college_id', $college_id)
                                                  ->where('is_active', 1)
                                                  ->where('availability_status', 'available')
                                                  ->count_all_results(TABLE_INSTRUMENTS);

        // Issued instruments
        $stats['issued_instruments'] = $this->db->where('college_id', $college_id)
                                               ->where('is_active', 1)
                                               ->where('availability_status', 'issued')
                                               ->count_all_results(TABLE_INSTRUMENTS);

        // Under maintenance
        $stats['maintenance_instruments'] = $this->db->where('college_id', $college_id)
                                                    ->where('is_active', 1)
                                                    ->where('availability_status', 'maintenance')
                                                    ->count_all_results(TABLE_INSTRUMENTS);

        // Current issues
        $stats['active_issues'] = $this->db->where('status', 'issued')
                                          ->join(TABLE_INSTRUMENTS . ' i', 'i.id = ' . TABLE_INSTRUMENT_ISSUES . '.instrument_id')
                                          ->where('i.college_id', $college_id)
                                          ->count_all_results(TABLE_INSTRUMENT_ISSUES);

        // Pending maintenance
        $stats['pending_maintenance'] = $this->db->where('status !=', 'completed')
                                                ->join(TABLE_INSTRUMENTS . ' i', 'i.id = ' . TABLE_INSTRUMENT_MAINTENANCE . '.instrument_id')
                                                ->where('i.college_id', $college_id)
                                                ->count_all_results(TABLE_INSTRUMENT_MAINTENANCE);

        return $stats;
    }

    public function get_availability_report($college_id, $filters = [])
    {
        $this->db->select('
            i.*,
            CASE
                WHEN i.availability_status = "issued" THEN CONCAT("Issued to: ", ii.issued_to)
                WHEN i.availability_status = "maintenance" THEN "Under Maintenance"
                ELSE "Available"
            END as status_details
        ')
        ->from(TABLE_INSTRUMENTS . ' i')
        ->join(TABLE_INSTRUMENT_ISSUES . ' ii', 'ii.id = i.current_issue_id AND ii.status = "issued"', 'left')
        ->where('i.college_id', $college_id)
        ->where('i.is_active', 1);

        if (!empty($filters['status'])) {
            $this->db->where('i.availability_status', $filters['status']);
        }

        if (!empty($filters['category'])) {
            $this->db->where('i.category', $filters['category']);
        }

        $this->db->order_by('i.category', 'ASC')
                ->order_by('i.name', 'ASC');

        return $this->db->get()->result_array();
    }

    public function get_overdue_returns($college_id)
    {
        return $this->db->select('ii.*, i.name as instrument_name, i.serial_no')
                       ->from(TABLE_INSTRUMENT_ISSUES . ' ii')
                       ->join(TABLE_INSTRUMENTS . ' i', 'i.id = ii.instrument_id')
                       ->where('ii.status', 'issued')
                       ->where('i.college_id', $college_id)
                       ->where('ii.expected_return_date <', date('Y-m-d'))
                       ->order_by('ii.expected_return_date', 'ASC')
                       ->get()
                       ->result_array();
    }

    // ============================
    // UTILITY METHODS
    // ============================

    public function get_instrument_categories()
    {
        return [
            'string' => 'String Instruments',
            'percussion' => 'Percussion Instruments',
            'wind' => 'Wind Instruments',
            'keyboard' => 'Keyboard Instruments',
            'electronic' => 'Electronic Instruments'
        ];
    }

    public function get_common_instruments()
    {
        return [
            'guitar' => 'Guitar',
            'piano' => 'Piano',
            'violin' => 'Violin',
            'tabla' => 'Tabla',
            'drums' => 'Drums',
            'flute' => 'Flute',
            'saxophone' => 'Saxophone',
            'trumpet' => 'Trumpet',
            'keyboard' => 'Keyboard',
            'harmonium' => 'Harmonium',
            'sitar' => 'Sitar',
            'tambourine' => 'Tambourine',
            'bongos' => 'Bongos',
            'ukulele' => 'Ukulele',
            'cello' => 'Cello'
        ];
    }
}


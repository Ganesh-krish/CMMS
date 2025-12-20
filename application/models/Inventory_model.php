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
        return $this->db->get_where(TABLE_INSTRUMENTS, ['id' => $id, 'is_active' => 1])->row_array();
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
            'student_id' => $data['student_id'] ?? null,
            'faculty_id' => $data['faculty_id'] ?? null,
            'issued_by' => $data['issued_by'],
            'issue_date' => $data['issue_date'] ?? date('Y-m-d H:i:s'),
            'expected_return_date' => $data['expected_return_date'],
            'condition_on_issue' => $data['condition_on_issue'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => INSTRUMENT_ISSUE_STATUS_ISSUED,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert(TABLE_INSTRUMENT_ISSUES, $issue_data);
        $issue_id = $this->db->insert_id();

        // Update instrument status
        $this->db->where('id', $data['instrument_id'])
            ->update(TABLE_INSTRUMENTS, [
                'availability_status' => INSTRUMENT_STATUS_ISSUED,
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
            'actual_return_date' => $return_data['actual_return_date'] ?? date('Y-m-d H:i:s'),
            'condition_on_return' => $return_data['condition_on_return'] ?? null,
            'notes' => $return_data['notes'] ?? null,
            'status' => INSTRUMENT_ISSUE_STATUS_RETURNED,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $issue_id)->update(TABLE_INSTRUMENT_ISSUES, $update_data);

        // Update instrument status
        $this->db->where('id', $issue['instrument_id'])
            ->update(TABLE_INSTRUMENTS, [
                'availability_status' => INSTRUMENT_STATUS_AVAILABLE,
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

        if (!empty($filters['student_id'])) {
            $this->db->where('student_id', $filters['student_id']);
        }
        if (!empty($filters['faculty_id'])) {
            $this->db->where('faculty_id', $filters['faculty_id']);
        }

        $this->db->order_by('issue_date', 'DESC');
        return $this->db->get()->result_array();
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
                                                  ->where('availability_status', INSTRUMENT_STATUS_AVAILABLE)
                                                  ->count_all_results(TABLE_INSTRUMENTS);

        // Issued instruments
        $stats['issued_instruments'] = $this->db->where('college_id', $college_id)
                                               ->where('is_active', 1)
                                               ->where('availability_status', INSTRUMENT_STATUS_ISSUED)
                                               ->count_all_results(TABLE_INSTRUMENTS);

        // Under maintenance
        $stats['maintenance_instruments'] = $this->db->where('college_id', $college_id)
                                                    ->where('is_active', 1)
                                                    ->where('availability_status', INSTRUMENT_STATUS_MAINTENANCE)
                                                    ->count_all_results(TABLE_INSTRUMENTS);

        // Current issues
        $stats['active_issues'] = $this->db->where('status', INSTRUMENT_ISSUE_STATUS_ISSUED)
                                          ->join(TABLE_INSTRUMENTS . ' i', 'i.id = ' . TABLE_INSTRUMENT_ISSUES . '.instrument_id')
                                          ->where('i.college_id', $college_id)
                                          ->count_all_results(TABLE_INSTRUMENT_ISSUES);

        return $stats;
    }

    public function get_availability_report($college_id, $filters = [])
    {
        $this->db->select('
            i.*,
            CASE
                WHEN i.availability_status = "' . INSTRUMENT_STATUS_ISSUED . '" THEN CONCAT("Issued to student/faculty")
                WHEN i.availability_status = "' . INSTRUMENT_STATUS_MAINTENANCE . '" THEN "Under Maintenance"
                ELSE "Available"
            END as status_details
        ')
        ->from(TABLE_INSTRUMENTS . ' i')
        ->join(TABLE_INSTRUMENT_ISSUES . ' ii', 'ii.instrument_id = i.id AND ii.status = "' . INSTRUMENT_ISSUE_STATUS_ISSUED . '"', 'left')
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
                       ->where('ii.status', INSTRUMENT_ISSUE_STATUS_ISSUED)
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


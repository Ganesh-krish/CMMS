<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

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
                ->group_end();
        }

        return $this->db->get()->result_array();
    }

    public function create_instrument($data)
    {
        $this->db->insert(TABLE_INSTRUMENTS, $data);
        return $this->db->insert_id();
    }

    public function update_instrument($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update(TABLE_INSTRUMENTS, $data);
    }

    public function get_instrument($id)
    {
        return $this->db->get_where(TABLE_INSTRUMENTS, ['id' => $id])->row_array();
    }

    public function issue_instrument($payload)
    {
        $this->db->trans_start();

        $this->db->insert(TABLE_INSTRUMENT_TRANSACTIONS, $payload);
        $transaction_id = $this->db->insert_id();

        $this->db->where('id', $payload['instrument_id'])
            ->update(TABLE_INSTRUMENTS, [
                'availability_status' => 'issued',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->db->trans_complete();
        return $this->db->trans_status() ? $transaction_id : false;
    }

    public function return_instrument($transaction_id, $instrument_id, $data = [])
    {
        $this->db->trans_start();

        $this->db->where('id', $transaction_id)
            ->update(TABLE_INSTRUMENT_TRANSACTIONS, $data);

        $this->db->where('id', $instrument_id)
            ->update(TABLE_INSTRUMENTS, [
                'availability_status' => 'available',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function log_maintenance($data)
    {
        $this->db->insert(TABLE_INSTRUMENT_MAINTENANCE, $data);
        return $this->db->insert_id();
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Batch_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function list_by_course($course_id)
    {
        return $this->db
            ->where('course_id', $course_id)
            ->order_by('start_date', 'ASC')
            ->get(TABLE_BATCHES)
            ->result_array();
    }

    public function create_batch($data)
    {
        $this->db->insert(TABLE_BATCHES, $data);
        return $this->db->insert_id();
    }

    public function update_batch($id, $data)
    {
        return $this->db->where('id', $id)->update(TABLE_BATCHES, $data);
    }

    public function list_schedules($batch_id)
    {
        return $this->db
            ->where('batch_id', $batch_id)
            ->order_by('start_at', 'ASC')
            ->get(TABLE_MODULE_SCHEDULES)
            ->result_array();
    }

    public function add_schedule($data)
    {
        $this->db->insert(TABLE_MODULE_SCHEDULES, $data);
        return $this->db->insert_id();
    }
}



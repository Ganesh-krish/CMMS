<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lesson_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function list_by_module($module_id)
    {
        return $this->db
            ->where('module_id', $module_id)
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get('lessons')
            ->result_array();
    }

    public function get($id)
    {
        return $this->db->get_where('lessons', ['id' => $id])->row_array();
    }

    public function create($data)
    {
        $this->db->insert('lessons', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update('lessons', $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('lessons');
    }
}


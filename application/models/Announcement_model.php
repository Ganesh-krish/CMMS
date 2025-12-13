<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Announcement_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ============================
    // ANNOUNCEMENT MANAGEMENT
    // ============================

    public function create_announcement($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert(TABLE_ANNOUNCEMENTS, $data);
        return $this->db->insert_id();
    }

    public function update_announcement($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update(TABLE_ANNOUNCEMENTS, $data);
    }

    public function delete_announcement($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete(TABLE_ANNOUNCEMENTS);
    }

    public function get_announcements($filters = [])
    {
        $this->db->select('a.*, f.name as sender_name, f.role as sender_role, d.name as department_name');
        $this->db->from(TABLE_ANNOUNCEMENTS . ' a');
        $this->db->join(TABLE_FACULTY . ' f', 'f.id = a.sender_id', 'left');
        $this->db->join(TABLE_DEPARTMENT . ' d', 'd.id = a.department_id', 'left');

        // Apply visibility filters based on user role and department
        if (!empty($filters['user_role'])) {
            $user_role = $filters['user_role'];
            $user_department = $filters['user_department'] ?? null;

            if ($user_role == ROLE_SUPERADMIN || $user_role == ROLE_VICE_PRINCIPAL) {
                // SuperAdmin and Admin can see all announcements
            } elseif ($user_role == ROLE_HOD) {
                // HOD can see all announcements + their department's announcements
                $this->db->where("(a.visibility = 'all' OR (a.visibility = 'department' AND a.department_id = {$user_department}))");
            } elseif ($user_role == ROLE_STAFF) {
                // Staff can see all announcements + their department's announcements
                $this->db->where("(a.visibility = 'all' OR (a.visibility = 'department' AND a.department_id = {$user_department}))");
            } else {
                // Students can only see announcements for their department or all announcements
                $this->db->where("(a.visibility = 'all' OR (a.visibility = 'department' AND a.department_id = {$user_department}))");
            }
        }

        if (!empty($filters['college_id'])) {
            $this->db->where('a.college_id', $filters['college_id']);
        }

        if (!empty($filters['is_active'])) {
            $this->db->where('a.is_active', $filters['is_active']);
        }

        if (!empty($filters['sender_id'])) {
            $this->db->where('a.sender_id', $filters['sender_id']);
        }

        if (!empty($filters['visibility'])) {
            $this->db->where('a.visibility', $filters['visibility']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('a.title', $filters['search'])
                ->or_like('a.message', $filters['search'])
                ->group_end();
        }

        $this->db->order_by('a.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_announcement($id)
    {
        $this->db->select('a.*, f.name as sender_name, f.role as sender_role, d.name as department_name');
        $this->db->from(TABLE_ANNOUNCEMENTS . ' a');
        $this->db->join(TABLE_FACULTY . ' f', 'f.id = a.sender_id', 'left');
        $this->db->join(TABLE_DEPARTMENT . ' d', 'd.id = a.department_id', 'left');
        $this->db->where('a.id', $id);
        return $this->db->get()->row_array();
    }

    public function get_announcement_stats($college_id)
    {
        // Total announcements
        $total = $this->db->where('college_id', $college_id)
                         ->where('is_active', 1)
                         ->count_all_results(TABLE_ANNOUNCEMENTS);

        // Announcements this month
        $this_month = $this->db->where('college_id', $college_id)
                              ->where('is_active', 1)
                              ->where('created_at >=', date('Y-m-01'))
                              ->count_all_results(TABLE_ANNOUNCEMENTS);

        // Announcements by visibility
        $public_count = $this->db->where('college_id', $college_id)
                                ->where('is_active', 1)
                                ->where('visibility', 'all')
                                ->count_all_results(TABLE_ANNOUNCEMENTS);

        $department_count = $this->db->where('college_id', $college_id)
                                    ->where('is_active', 1)
                                    ->where('visibility', 'department')
                                    ->count_all_results(TABLE_ANNOUNCEMENTS);

        return [
            'total_announcements' => $total,
            'this_month' => $this_month,
            'public_announcements' => $public_count,
            'department_announcements' => $department_count
        ];
    }

    public function get_user_visible_announcements($user_id, $user_role, $user_department, $college_id)
    {
        $filters = [
            'user_role' => $user_role,
            'user_department' => $user_department,
            'college_id' => $college_id,
            'is_active' => 1
        ];

        return $this->get_announcements($filters);
    }

    public function mark_as_read($announcement_id, $user_id)
    {
        // This would require an announcement_reads table for tracking read status
        // For now, we'll just return true as announcements are meant to be visible
        return true;
    }

    public function get_unread_count($user_id, $user_role, $user_department, $college_id)
    {
        // This would require an announcement_reads table
        // For now, return 0 as we don't have read tracking
        return 0;
    }
}

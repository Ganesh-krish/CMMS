<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificate_request_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('Db_model', 'db_model');
    }

    /**
     * Create a certificate request
     */
    public function create_request($enrollment_id, $course_id, $student_id) {
        // Check if request already exists
        $existing = $this->get_request_by_enrollment($enrollment_id);
        if ($existing) {
            return false; // Request already exists
        }
        
        $data = [
            'enrollment_id' => $enrollment_id,
            'course_id' => $course_id,
            'student_id' => $student_id,
            'status' => 'pending'
        ];
        
        return $this->db_model->insert(TABLE_CERTIFICATE_REQUESTS, $data);
    }

    /**
     * Get request by enrollment ID
     */
    public function get_request_by_enrollment($enrollment_id) {
        return $this->db_model->get_row(TABLE_CERTIFICATE_REQUESTS, [
            'enrollment_id' => $enrollment_id
        ]);
    }

    /**
     * Get request by ID
     */
    public function get_request($request_id) {
        return $this->db_model->get_row(TABLE_CERTIFICATE_REQUESTS, [
            'id' => $request_id
        ]);
    }

    /**
     * Get all pending requests
     */
    public function get_pending_requests() {
        $this->db->select('cr.*, 
            c.name as course_name, 
            c.course_code,
            s.name as student_name, 
            s.email as student_email,
            s.roll_no,
            ce.enrolled_at,
            ce.progress_percentage,
            f.name as reviewer_name');
        $this->db->from(TABLE_CERTIFICATE_REQUESTS . ' cr');
        $this->db->join(TABLE_COURCES . ' c', 'c.id = cr.course_id', 'inner');
        $this->db->join(TABLE_STUDENT . ' s', 's.id = cr.student_id', 'inner');
        $this->db->join(TABLE_COURSE_ENROLLMENTS . ' ce', 'ce.id = cr.enrollment_id', 'inner');
        $this->db->join(TABLE_FACULTY . ' f', 'f.id = cr.reviewed_by', 'left');
        $this->db->where('cr.status', 'pending');
        $this->db->order_by('cr.requested_at', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Get all requests (with filters)
     */
    public function get_all_requests($status = null) {
        $this->db->select('cr.*, 
            c.name as course_name, 
            c.course_code,
            s.name as student_name, 
            s.email as student_email,
            s.roll_no,
            ce.enrolled_at,
            ce.progress_percentage,
            f.name as reviewer_name');
        $this->db->from(TABLE_CERTIFICATE_REQUESTS . ' cr');
        $this->db->join(TABLE_COURCES . ' c', 'c.id = cr.course_id', 'inner');
        $this->db->join(TABLE_STUDENT . ' s', 's.id = cr.student_id', 'inner');
        $this->db->join(TABLE_COURSE_ENROLLMENTS . ' ce', 'ce.id = cr.enrollment_id', 'inner');
        $this->db->join(TABLE_FACULTY . ' f', 'f.id = cr.reviewed_by', 'left');
        
        if ($status) {
            $this->db->where('cr.status', $status);
        }
        
        $this->db->order_by('cr.requested_at', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Approve certificate request
     */
    public function approve_request($request_id, $reviewed_by, $notes = null) {
        $data = [
            'status' => 'approved',
            'reviewed_by' => $reviewed_by,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'notes' => $notes
        ];
        
        return $this->db_model->update(TABLE_CERTIFICATE_REQUESTS, $data, [
            'id' => $request_id
        ]);
    }

    /**
     * Reject certificate request
     */
    public function reject_request($request_id, $reviewed_by, $rejection_reason, $notes = null) {
        $data = [
            'status' => 'rejected',
            'reviewed_by' => $reviewed_by,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => $rejection_reason,
            'notes' => $notes
        ];
        
        return $this->db_model->update(TABLE_CERTIFICATE_REQUESTS, $data, [
            'id' => $request_id
        ]);
    }

    /**
     * Get requests for a student
     */
    public function get_student_requests($student_id) {
        $this->db->select('cr.*, 
            c.name as course_name, 
            c.course_code,
            f.name as reviewer_name');
        $this->db->from(TABLE_CERTIFICATE_REQUESTS . ' cr');
        $this->db->join(TABLE_COURCES . ' c', 'c.id = cr.course_id', 'inner');
        $this->db->join(TABLE_FACULTY . ' f', 'f.id = cr.reviewed_by', 'left');
        $this->db->where('cr.student_id', $student_id);
        $this->db->order_by('cr.requested_at', 'DESC');
        return $this->db->get()->result_array();
    }
}

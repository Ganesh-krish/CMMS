<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificate_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('Db_model', 'db_model');
    }

    /**
     * Get certificate by ID
     */
    public function get_certificate($certificate_id) {
        return $this->db_model->get_row(TABLE_COURSE_CERTIFICATES, ['id' => $certificate_id]);
    }

    /**
     * Get certificate by enrollment ID
     */
    public function get_certificate_by_enrollment($enrollment_id) {
        $result = $this->db_model->get_row(TABLE_COURSE_CERTIFICATES, [
            'enrollment_id' => $enrollment_id
        ]);
        // Return false if not found or if is_active is 0
        if (!$result || (isset($result['is_active']) && $result['is_active'] == 0)) {
            return false;
        }
        return $result;
    }

    /**
     * Get certificate by certificate number
     */
    public function get_certificate_by_number($certificate_number) {
        return $this->db_model->get_row(TABLE_COURSE_CERTIFICATES, [
            'certificate_number' => $certificate_number,
            'is_active' => 1
        ]);
    }

    /**
     * Get all certificates for a student
     */
    public function get_student_certificates($student_id) {
        $this->db->select('cc.*, c.name as course_name, c.course_code, ce.completed_at, s.name as student_name');
        $this->db->from(TABLE_COURSE_CERTIFICATES . ' cc');
        $this->db->join(TABLE_COURSE_ENROLLMENTS . ' ce', 'ce.id = cc.enrollment_id', 'inner');
        $this->db->join(TABLE_COURCES . ' c', 'c.id = cc.course_id', 'inner');
        $this->db->join(TABLE_STUDENT . ' s', 's.id = cc.student_id', 'inner');
        $this->db->where('cc.student_id', $student_id);
        $this->db->where('cc.is_active', 1);
        $this->db->order_by('cc.issued_at', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Create a new certificate
     */
    public function create_certificate($data) {
        if (isset($data['id'])) {
            // Update existing certificate
            $id = $data['id'];
            unset($data['id']);
            return $this->db_model->update(TABLE_COURSE_CERTIFICATES, $data, ['id' => $id]);
        } else {
            // Insert new certificate
            return $this->db_model->insert(TABLE_COURSE_CERTIFICATES, $data);
        }
    }

    /**
     * Generate unique certificate number
     */
    public function generate_certificate_number($course_id, $student_id) {
        $prefix = 'CERT';
        $course_code = $this->db->select('course_code')
            ->from(TABLE_COURCES)
            ->where('id', $course_id)
            ->get()
            ->row();
        
        $course_prefix = $course_code ? substr(strtoupper($course_code->course_code), 0, 4) : 'COUR';
        $timestamp = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        
        $cert_number = $prefix . '-' . $course_prefix . '-' . $timestamp . '-' . $random;
        
        // Ensure uniqueness
        $exists = $this->get_certificate_by_number($cert_number);
        if ($exists) {
            $cert_number = $cert_number . '-' . rand(100, 999);
        }
        
        return $cert_number;
    }

    /**
     * Check if certificate exists for enrollment
     */
    public function certificate_exists($enrollment_id) {
        $cert = $this->get_certificate_by_enrollment($enrollment_id);
        return ($cert !== false && $cert !== null && !empty($cert));
    }
}

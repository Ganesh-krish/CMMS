<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lesson_progress_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('Db_model', 'db_model');
    }

    /**
     * Get lesson progress for a student enrollment
     */
    public function get_lesson_progress($enrollment_id, $lesson_id) {
        return $this->db_model->get_row(TABLE_STUDENT_LESSON_PROGRESS, [
            'enrollment_id' => $enrollment_id,
            'lesson_id' => $lesson_id
        ]);
    }

    /**
     * Get all lesson progress for an enrollment
     */
    public function get_enrollment_progress($enrollment_id) {
        return $this->db_model->get_all(TABLE_STUDENT_LESSON_PROGRESS, [
            'enrollment_id' => $enrollment_id
        ], '*', 'lesson_id', 'ASC');
    }

    /**
     * Mark lesson as in-progress (when student views it)
     */
    public function mark_lesson_in_progress($enrollment_id, $lesson_id, $module_id, $course_id, $student_id) {
        // Check if progress record exists
        $existing = $this->get_lesson_progress($enrollment_id, $lesson_id);
        
        if ($existing) {
            // Update to in-progress if not already completed
            if ($existing['status'] !== 'completed') {
                $update_data = [
                    'status' => 'in_progress',
                    'started_at' => date('Y-m-d H:i:s')
                ];
                // Only update started_at if it's null
                if (empty($existing['started_at'])) {
                    $update_data['started_at'] = date('Y-m-d H:i:s');
                }
                return $this->db_model->update(TABLE_STUDENT_LESSON_PROGRESS, $update_data, [
                    'enrollment_id' => $enrollment_id,
                    'lesson_id' => $lesson_id
                ]);
            }
            return true; // Already completed, no update needed
        } else {
            // Create new progress record
            $data = [
                'enrollment_id' => $enrollment_id,
                'lesson_id' => $lesson_id,
                'module_id' => $module_id,
                'course_id' => $course_id,
                'student_id' => $student_id,
                'status' => 'in_progress',
                'started_at' => date('Y-m-d H:i:s')
            ];
            return $this->db_model->insert(TABLE_STUDENT_LESSON_PROGRESS, $data);
        }
    }

    /**
     * Mark lesson as completed
     */
    public function mark_lesson_completed($enrollment_id, $lesson_id) {
        $update_data = [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s')
        ];
        
        // Ensure started_at is set if it wasn't before
        $existing = $this->get_lesson_progress($enrollment_id, $lesson_id);
        if ($existing && empty($existing['started_at'])) {
            $update_data['started_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db_model->update(TABLE_STUDENT_LESSON_PROGRESS, $update_data, [
            'enrollment_id' => $enrollment_id,
            'lesson_id' => $lesson_id
        ]);
    }

    /**
     * Get completion statistics for an enrollment
     */
    public function get_completion_stats($enrollment_id) {
        $this->db->select('
            COUNT(*) as total_lessons,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_lessons,
            SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress_lessons,
            SUM(CASE WHEN status = "not_started" THEN 1 ELSE 0 END) as not_started_lessons
        ');
        $this->db->from(TABLE_STUDENT_LESSON_PROGRESS);
        $this->db->where('enrollment_id', $enrollment_id);
        $result = $this->db->get()->row_array();
        
        if ($result && $result['total_lessons'] > 0) {
            $result['completion_percentage'] = ($result['completed_lessons'] / $result['total_lessons']) * 100;
        } else {
            $result = [
                'total_lessons' => 0,
                'completed_lessons' => 0,
                'in_progress_lessons' => 0,
                'not_started_lessons' => 0,
                'completion_percentage' => 0
            ];
        }
        
        return $result;
    }

    /**
     * Check if all lessons in a course are completed
     */
    public function are_all_lessons_completed($enrollment_id, $course_id) {
        // Get total lessons in course
        $this->db->select('COUNT(*) as total');
        $this->db->from(TABLE_COURSE_MODULE_LESSONS . ' l');
        $this->db->join(TABLE_COURSE_MODULES . ' m', 'm.id = l.module_id');
        $this->db->where('m.course_id', $course_id);
        $this->db->where('l.is_active', 1);
        $total_lessons = $this->db->get()->row()->total;
        
        // Get completed lessons for this enrollment
        $this->db->select('COUNT(*) as completed');
        $this->db->from(TABLE_STUDENT_LESSON_PROGRESS);
        $this->db->where('enrollment_id', $enrollment_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('status', 'completed');
        $completed_lessons = $this->db->get()->row()->completed;
        
        return ($total_lessons > 0 && $completed_lessons == $total_lessons);
    }
}

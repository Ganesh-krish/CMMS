<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Init_cmms extends CI_Migration
{
    public function up()
    {
        // Ensure DB Forge is loaded
        $this->load->dbforge();

        // Extend courses with musical attributes
        $courseFields = [
            'level' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'default' => 'Beginner',
                'null' => TRUE,
            ],
            'instrument_focus' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => TRUE,
            ],
            'capacity' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ],
        ];
        foreach ($courseFields as $name => $definition) {
            if (!$this->db->field_exists($name, 'courses')) {
                $this->dbforge->add_column('courses', [$name => $definition]);
            }
        }

        // Batches
        if (!$this->db->table_exists('batches')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'course_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'college_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => FALSE],
                'schedule_text' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'start_date' => ['type' => 'DATE', 'null' => TRUE],
                'end_date' => ['type' => 'DATE', 'null' => TRUE],
                'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'created_at' => ['type' => 'DATETIME', 'null' => TRUE, 'default' => NULL],
                'updated_at' => ['type' => 'DATETIME', 'null' => TRUE, 'default' => NULL],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('batches', TRUE);
        }

        // Module schedules
        if (!$this->db->table_exists('module_schedules')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'module_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'batch_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'teacher_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'start_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'end_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'room' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => TRUE],
                'recurrence' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('module_id');
            $this->dbforge->create_table('module_schedules', TRUE);
        }

        // Enrollments
        if (!$this->db->table_exists('enrollments')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'student_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'course_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'college_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'batch_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'status' => ['type' => 'VARCHAR', 'constraint' => 32, 'default' => 'active'],
                'enrolled_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key(['student_id', 'course_id']);
            $this->dbforge->create_table('enrollments', TRUE);
        }

        // Progress logs
        if (!$this->db->table_exists('progress_logs')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'student_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'course_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'module_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
                'score' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => TRUE],
                'notes' => ['type' => 'TEXT', 'null' => TRUE],
                'recorded_at' => ['type' => 'DATETIME', 'null' => TRUE],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key(['student_id', 'course_id']);
            $this->dbforge->create_table('progress_logs', TRUE);
        }

        // Instruments
        if (!$this->db->table_exists('instruments')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'college_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'name' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => FALSE],
                'category' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => TRUE],
                'serial_no' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => TRUE],
                'condition_notes' => ['type' => 'TEXT', 'null' => TRUE],
                'purchase_date' => ['type' => 'DATE', 'null' => TRUE],
                'location' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => TRUE],
                'availability_status' => ['type' => 'VARCHAR', 'constraint' => 32, 'default' => 'available'],
                'notes' => ['type' => 'TEXT', 'null' => TRUE],
                'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('serial_no');
            $this->dbforge->create_table('instruments', TRUE);
        }

        // Instrument transactions (issue/return)
        if (!$this->db->table_exists('instrument_transactions')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'college_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'instrument_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'issued_to_student_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'issued_to_staff_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'issued_by_staff_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'issue_date' => ['type' => 'DATETIME', 'null' => TRUE],
                'due_date' => ['type' => 'DATE', 'null' => TRUE],
                'return_date' => ['type' => 'DATE', 'null' => TRUE],
                'condition_on_issue' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => TRUE],
                'condition_on_return' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => TRUE],
                'status' => ['type' => 'VARCHAR', 'constraint' => 32, 'default' => 'issued'],
                'remarks' => ['type' => 'TEXT', 'null' => TRUE],
                'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('instrument_id');
            $this->dbforge->add_key('status');
            $this->dbforge->create_table('instrument_transactions', TRUE);
        }

        // Instrument maintenance
        if (!$this->db->table_exists('instrument_maintenance')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'college_id' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'instrument_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'type' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => TRUE],
                'description' => ['type' => 'TEXT', 'null' => TRUE],
                'status' => ['type' => 'VARCHAR', 'constraint' => 32, 'default' => 'open'],
                'cost' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => TRUE],
                'started_at' => ['type' => 'DATE', 'null' => TRUE],
                'completed_at' => ['type' => 'DATE', 'null' => TRUE],
                'technician' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => TRUE],
                'next_due_date' => ['type' => 'DATE', 'null' => TRUE],
                'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('instrument_id');
            $this->dbforge->create_table('instrument_maintenance', TRUE);
        }

        // Notifications
        if (!$this->db->table_exists('notifications')) {
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'user_id' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
                'type' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => FALSE],
                'payload' => ['type' => 'TEXT', 'null' => TRUE],
                'read_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key(['user_id', 'type']);
            $this->dbforge->create_table('notifications', TRUE);
        }
    }

    public function down()
    {
        $this->load->dbforge();

        $dropTables = [
            'notifications',
            'instrument_maintenance',
            'instrument_transactions',
            'instruments',
            'progress_logs',
            'enrollments',
            'module_schedules',
            'batches',
        ];

        foreach ($dropTables as $table) {
            if ($this->db->table_exists($table)) {
                $this->dbforge->drop_table($table, TRUE);
            }
        }

        $courseColumns = ['level', 'instrument_focus', 'capacity'];
        foreach ($courseColumns as $column) {
            if ($this->db->field_exists($column, 'courses')) {
                $this->dbforge->drop_column('courses', $column);
            }
        }
    }
}


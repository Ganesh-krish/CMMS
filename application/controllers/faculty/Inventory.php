<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends CI_Controller
{
    private $url;
    private $college;
    private $session_data;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('common', 'faculty_common');
        $this->load->model('faculty/db_model', 'db_model');
        $this->load->model('Inventory_model', 'inventory');

        $this->url = $this->uri->segment(1);
        $this->faculty_common->check_user_session($this->url);
        $this->college = $this->faculty_common->get_default_college();
        $this->session_data = $this->session->userdata($this->url);

        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if(!in_array($role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF], true)){
            $this->faculty_common->redirect_route($role,$this->url);
        }
    }

    public function index()
    {
        $data["url"] = $this->url;
        $class["classname"] = "inventory";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/inventory");

        // Get filters from URL
        $filters = [
            'college_id' => $this->college['id'],
            'availability_status' => $this->input->get('status'),
            'category' => $this->input->get('category'),
            'search' => $this->input->get('search'),
        ];

        $data["instruments"] = $this->inventory->list_instruments($filters);
        $data["stats"] = $this->inventory->get_inventory_stats($this->college['id']);
        $data["categories"] = $this->inventory->get_instrument_categories();

        $this->load->view('faculty/faculty/sidebar', $class);
        $this->load->view('faculty/inventory/index', $data);
        $this->load->view('faculty/inventory/modals');
        $this->load->view('faculty/faculty/footer');
    }

    public function create()
    {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Instrument Name', 'trim|required');
            $this->form_validation->set_rules('category', 'Category', 'trim|required');
            $this->form_validation->set_rules('serial_no', 'Serial Number', 'trim|required');
            $this->form_validation->set_rules('condition', 'Condition', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                // Handle custom name for "other" instruments
                $instrument_name = $this->input->post('name');
                if ($instrument_name === 'other') {
                    $instrument_name = $this->input->post('custom_name');
                }

                $data = array(
                    'name' => $instrument_name,
                    'category' => $this->input->post('category'),
                    'serial_no' => $this->input->post('serial_no'),
                    'model' => $this->input->post('model'),
                    'description' => $this->input->post('description'),
                    'condition' => $this->input->post('condition'),
                    'purchase_date' => $this->input->post('purchase_date'),
                    'purchase_cost' => $this->input->post('purchase_cost'),
                    'availability_status' => 'available',
                    'college_id' => $this->college['id'],
                    'is_active' => 1
                );

                if ($this->inventory->create_instrument($data)) {
                    $this->session->set_flashdata('message', array('success', "Instrument Created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create instrument."));
                }
                redirect(base_url($this->url . "/inventory"));
            }
        }
    }

    public function update()
    {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Instrument Name', 'trim|required');
            $this->form_validation->set_rules('category', 'Category', 'trim|required');
            $this->form_validation->set_rules('serial_no', 'Serial Number', 'trim|required');
            $this->form_validation->set_rules('condition', 'Condition', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $instrument_name = $this->input->post('name');
                if ($instrument_name === 'other') {
                    $instrument_name = $this->input->post('custom_name');
                }

                $data = array(
                    'name' => $instrument_name,
                    'category' => $this->input->post('category'),
                    'serial_no' => $this->input->post('serial_no'),
                    'model' => $this->input->post('model'),
                    'description' => $this->input->post('description'),
                    'condition' => $this->input->post('condition'),
                    'purchase_date' => $this->input->post('purchase_date'),
                    'purchase_cost' => $this->input->post('purchase_cost')
                );

                if ($this->inventory->update_instrument($post['id'], $data)) {
                    $this->session->set_flashdata('message', array('success', "Instrument Updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update instrument."));
                }
                redirect(base_url($this->url . "/inventory"));
            }
        }
    }

    public function get_instrument($id)
    {
        $instrument = $this->inventory->get_instrument($id);
        if ($instrument) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'data' => $instrument]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Instrument not found']));
        }
    }

    public function issue()
    {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('instrument_id', 'Instrument', 'trim|required');
            $this->form_validation->set_rules('issued_to_type', 'Issue To Type', 'trim|required');
            $this->form_validation->set_rules('issued_to_id', 'Issue To ID', 'trim|required');
            $this->form_validation->set_rules('expected_return_date', 'Expected Return Date', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $issue_data = array(
                    'instrument_id' => $this->input->post('instrument_id'),
                    'issued_to' => $this->input->post('issued_to_type') . ': ' . $this->input->post('issued_to_id'),
                    'issued_by' => $this->session_data['id'],
                    'issue_date' => $this->input->post('issue_date') ?: date('Y-m-d H:i:s'),
                    'expected_return_date' => $this->input->post('expected_return_date'),
                    'purpose' => $this->input->post('purpose')
                );

                if ($this->inventory->issue_instrument($issue_data)) {
                    $this->session->set_flashdata('message', array('success', "Instrument Issued successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to issue instrument."));
                }
                redirect(base_url($this->url . "/inventory"));
            }
        }
    }

    public function return_item()
    {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('instrument_id', 'Instrument', 'trim|required');
            $this->form_validation->set_rules('return_date', 'Return Date', 'trim|required');
            $this->form_validation->set_rules('condition_on_return', 'Condition on Return', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $return_data = array(
                    'return_date' => $this->input->post('return_date'),
                    'received_by' => $this->session_data['id'],
                    'condition_on_return' => $this->input->post('condition_on_return'),
                    'notes' => $this->input->post('notes')
                );

                if ($this->inventory->return_instrument($post['instrument_id'], $return_data)) {
                    $this->session->set_flashdata('message', array('success', "Instrument Returned successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to return instrument."));
                }
                redirect(base_url($this->url . "/inventory"));
            }
        }
    }

    public function maintenance()
    {
        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('instrument_id', 'Instrument', 'trim|required');
            $this->form_validation->set_rules('maintenance_type', 'Maintenance Type', 'trim|required');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect($_SERVER['HTTP_REFERER']);
            } else {
                $maintenance_data = array(
                    'instrument_id' => $this->input->post('instrument_id'),
                    'maintenance_type' => $this->input->post('maintenance_type'),
                    'description' => $this->input->post('description'),
                    'priority' => $this->input->post('priority'),
                    'scheduled_date' => $this->input->post('scheduled_date'),
                    'estimated_cost' => $this->input->post('estimated_cost'),
                    'assigned_to' => $this->input->post('assigned_to'),
                    'status' => 'pending',
                    'logged_by' => $this->session_data['id']
                );

                if ($this->inventory->log_maintenance($maintenance_data)) {
                    // Update instrument status to maintenance
                    $this->inventory->update_instrument($post['instrument_id'], ['availability_status' => 'maintenance']);
                    $this->session->set_flashdata('message', array('success', "Maintenance logged successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to log maintenance."));
                }
                redirect(base_url($this->url . "/inventory"));
            }
        }
    }

    public function issues()
    {
        $data["url"] = $this->url;
        $class["classname"] = "inventory_issues";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/inventory");

        $filters = [
            'status' => $this->input->get('status'),
            'issued_to' => $this->input->get('issued_to')
        ];

        $data["issues"] = $this->inventory->get_instrument_issues(null, $filters);

        $this->load->view('faculty/faculty/sidebar', $class);
        $this->load->view('faculty/inventory/issues', $data);
        $this->load->view('faculty/faculty/footer');
    }

    public function maintenance_logs()
    {
        $data["url"] = $this->url;
        $class["classname"] = "inventory_maintenance";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/inventory");

        $filters = [
            'type' => $this->input->get('type'),
            'status' => $this->input->get('status')
        ];

        $data["maintenance_logs"] = $this->inventory->get_maintenance_logs(null, $filters);

        $this->load->view('faculty/faculty/sidebar', $class);
        $this->load->view('faculty/inventory/maintenance', $data);
        $this->load->view('faculty/faculty/footer');
    }

    public function reports()
    {
        $data["url"] = $this->url;
        $class["classname"] = "inventory_reports";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/inventory");

        $filters = [
            'status' => $this->input->get('status'),
            'category' => $this->input->get('category')
        ];

        $data["stats"] = $this->inventory->get_inventory_stats($this->college['id']);
        $data["availability_report"] = $this->inventory->get_availability_report($this->college['id'], $filters);
        $data["overdue_returns"] = $this->inventory->get_overdue_returns($this->college['id']);
        $data["categories"] = $this->inventory->get_instrument_categories();

        // Additional dashboard stats
        $data["total_students"] = $this->db_model->count_rows(TABLE_STUDENT, ["college_id" => $this->college['id'], "is_active" => 1]);
        $data["total_staff"] = $this->db_model->count_rows(TABLE_FACULTY, ["college_id" => $this->college['id'], "is_active" => 1]);

        // Student performance metrics
        $data["student_performance"] = $this->get_student_performance_stats($this->college['id']);

        $this->load->view('faculty/faculty/sidebar', $class);
        $this->load->view('faculty/inventory/reports', $data);
        $this->load->view('faculty/faculty/footer');
    }

    private function get_student_performance_stats($college_id) {
        // Get average test scores for students
        $this->db->select('AVG(sts.earned_score) as avg_score, COUNT(DISTINCT sts.student_id) as total_students')
                 ->from(TABLE_STUDENT_TEST_SUBMISSION . ' sts')
                 ->join(TABLE_TESTS . ' t', 't.id = sts.test_id')
                 ->join(TABLE_STUDENT . ' s', 's.id = sts.student_id')
                 ->where('s.college_id', $college_id)
                 ->where('s.is_active', 1)
                 ->where('sts.status', 'completed');

        $performance = $this->db->get()->row_array();

        // Get pass/fail statistics (assuming 40% is passing score)
        $this->db->select('COUNT(*) as total_tests,
                          SUM(CASE WHEN sts.earned_score >= (t.total_marks * 0.4) THEN 1 ELSE 0 END) as passed_tests')
                 ->from(TABLE_STUDENT_TEST_SUBMISSION . ' sts')
                 ->join(TABLE_TESTS . ' t', 't.id = sts.test_id')
                 ->join(TABLE_STUDENT . ' s', 's.id = sts.student_id')
                 ->where('s.college_id', $college_id)
                 ->where('s.is_active', 1)
                 ->where('sts.status', 'completed');

        $pass_stats = $this->db->get()->row_array();

        // Get course completion statistics (using course_enrollments table)
        $this->db->select('COUNT(DISTINCT ce.student_id) as enrolled_students,
                          SUM(CASE WHEN ce.progress_percentage >= 80 THEN 1 ELSE 0 END) as completed_courses')
                 ->from(TABLE_COURSE_ENROLLMENTS . ' ce')
                 ->join(TABLE_COURCES . ' c', 'c.id = ce.course_id')
                 ->join(TABLE_STUDENT . ' s', 's.id = ce.student_id')
                 ->where('s.college_id', $college_id)
                 ->where('s.is_active', 1)
                 ->where('c.is_active', 1);

        $completion_stats = $this->db->get()->row_array();

        return [
            'average_score' => round($performance['avg_score'] ?? 0, 1),
            'total_active_students' => $performance['total_students'] ?? 0,
            'pass_rate' => $pass_stats['total_tests'] > 0 ?
                          round(($pass_stats['passed_tests'] / $pass_stats['total_tests']) * 100, 1) : 0,
            'course_completion_rate' => $completion_stats['enrolled_students'] > 0 ?
                                       round(($completion_stats['completed_courses'] / $completion_stats['enrolled_students']) * 100, 1) : 0,
            'total_tests_taken' => $pass_stats['total_tests'] ?? 0
        ];
    }

        $payload = [
            'name' => $this->input->post('name'),
            'category' => $this->input->post('category'),
            'serial_no' => $this->input->post('serial_no'),
            'condition_notes' => $this->input->post('condition_notes'),
            'purchase_date' => $this->input->post('purchase_date'),
            'location' => $this->input->post('location'),
            'availability_status' => $this->input->post('availability_status') ?? 'available',
            'notes' => $this->input->post('notes'),
            'college_id' => $this->college['id'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (empty($payload['name'])) {
            return $this->json_response('error', 'Instrument name is required', 400);
        }

        $id = $this->inventory->create_instrument($payload);
        return $this->json_response('success', ['id' => $id], 201);
    }

    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $payload = array_filter([
            'name' => $this->input->post('name'),
            'category' => $this->input->post('category'),
            'serial_no' => $this->input->post('serial_no'),
            'condition_notes' => $this->input->post('condition_notes'),
            'purchase_date' => $this->input->post('purchase_date'),
            'location' => $this->input->post('location'),
            'availability_status' => $this->input->post('availability_status'),
            'notes' => $this->input->post('notes'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], static function ($value) {
            return $value !== null && $value !== '';
        });

        $instrument = $this->inventory->get_instrument($id);
        if (!$instrument) {
            return $this->json_response('error', 'Instrument not found', 404);
        }

        $this->inventory->update_instrument($id, $payload);
        return $this->json_response('success', ['id' => $id]);
    }

    public function issue()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $instrument_id = (int)$this->input->post('instrument_id');
        $issued_to_student_id = $this->input->post('issued_to_student_id');
        $issued_to_staff_id = $this->input->post('issued_to_staff_id');

        if (!$instrument_id || (!$issued_to_student_id && !$issued_to_staff_id)) {
            return $this->json_response('error', 'Instrument and recipient are required', 400);
        }

        $payload = [
            'college_id' => $this->college['id'] ?? null,
            'instrument_id' => $instrument_id,
            'issued_to_student_id' => $issued_to_student_id,
            'issued_to_staff_id' => $issued_to_staff_id,
            'issued_by_staff_id' => $this->session_data['id'] ?? null,
            'issue_date' => date('Y-m-d H:i:s'),
            'due_date' => $this->input->post('due_date'),
            'status' => 'issued',
            'remarks' => $this->input->post('remarks'),
        ];

        $transaction_id = $this->inventory->issue_instrument($payload);
        if (!$transaction_id) {
            return $this->json_response('error', 'Unable to issue instrument', 500);
        }

        return $this->json_response('success', ['transaction_id' => $transaction_id], 201);
    }

    public function return_item()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $transaction_id = (int)$this->input->post('transaction_id');
        $instrument_id = (int)$this->input->post('instrument_id');
        if (!$transaction_id || !$instrument_id) {
            return $this->json_response('error', 'Transaction and instrument are required', 400);
        }

        $data = [
            'status' => 'returned',
            'return_date' => date('Y-m-d'),
            'condition_on_return' => $this->input->post('condition_on_return'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $ok = $this->inventory->return_instrument($transaction_id, $instrument_id, $data);
        if (!$ok) {
            return $this->json_response('error', 'Unable to close transaction', 500);
        }

        return $this->json_response('success', ['transaction_id' => $transaction_id]);
    }

    public function maintenance()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $instrument_id = (int)$this->input->post('instrument_id');
        if (!$instrument_id) {
            return $this->json_response('error', 'Instrument is required', 400);
        }

        $payload = [
            'college_id' => $this->college['id'] ?? null,
            'instrument_id' => $instrument_id,
            'type' => $this->input->post('type'),
            'description' => $this->input->post('description'),
            'status' => $this->input->post('status') ?: 'open',
            'cost' => $this->input->post('cost'),
            'started_at' => $this->input->post('started_at'),
            'completed_at' => $this->input->post('completed_at'),
            'technician' => $this->input->post('technician'),
            'next_due_date' => $this->input->post('next_due_date'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $id = $this->inventory->log_maintenance($payload);
        return $this->json_response('success', ['id' => $id], 201);
    }
}


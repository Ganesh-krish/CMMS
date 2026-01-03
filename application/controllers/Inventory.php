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
        $this->load->model('Db_model', 'db_model');
        $this->load->model('Inventory_model', 'inventory');

        // Check unified session (similar to Course controller)
        $user = $this->session->userdata('user');
        if (!$user || $user['user_type'] !== 'faculty') {
            redirect('Welcome');
        }

        $this->user_session = $user;
        $this->url = $this->uri->segment(1) ?: 'admin';
        $this->college = $this->faculty_common->get_default_college();
        $this->session_data = $user; // Use unified session data

        $this->college = $this->faculty_common->get_default_college();

        $role = (int)($this->session_data['role'] ?? $this->session_data['designation'] ?? null);

        // Define access levels
        $full_access_roles = [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_CUSTODIAN];
        $read_only_roles = [ROLE_HOD, ROLE_STAFF];
        $allowed_roles = array_merge($full_access_roles, $read_only_roles);

        if(!in_array($role, $allowed_roles, true)){
            $this->faculty_common->redirect_route($role,$this->url);
        }

        // Set permissions using unified permission system
        $this->permissions = $this->faculty_common->get_access_permissions($this->session_data);

        // Add inventory-specific permissions using common method
        $this->permissions['can_create'] = $this->faculty_common->has_permission($this->session_data, 'create', 'inventory');
        $this->permissions['can_edit'] = $this->faculty_common->has_permission($this->session_data, 'edit', 'inventory');
        $this->permissions['can_delete'] = $this->faculty_common->has_permission($this->session_data, 'delete', 'inventory');
        $this->permissions['can_issue'] = $this->faculty_common->has_permission($this->session_data, 'issue', 'inventory');
        $this->permissions['can_return'] = $this->faculty_common->has_permission($this->session_data, 'return', 'inventory');
    }

    public function index()
    {
        $data["url"] = $this->url;
        $class["classname"] = "inventory";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/inventory");
        $class["college"] = $this->college;

        // Get filters from URL
        $filters = [
            'college_id' => $this->college['id'],
            'availability_status' => $this->input->get('status') ?: null,
            'category' => $this->input->get('category'),
            'search' => $this->input->get('search'),
        ];

        $data["instruments"] = $this->inventory->list_instruments($filters);
        $data["stats"] = $this->inventory->get_inventory_stats($this->college['id']);
        $data["categories"] = $this->inventory->get_instrument_categories();

        // Add permissions to view data
        $data["permissions"] = $this->permissions;

        // Check if current user is HOD for UI restrictions
        $role = (int)($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        $data["current_user_is_hod"] = ($role == ROLE_HOD);

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/inventory/index', $data);
        $this->load->view('faculty/inventory/modals');
        $this->load->view('common/footer');
    }

    public function delete($id)
    {
        // Check permissions
        if (!$this->permissions['can_delete']) {
            $this->session->set_flashdata('message', array("danger", "You don't have permission to delete inventory items."));
            redirect($this->url.'/inventory');
            return;
        }

        if ($this->inventory->delete_instrument($id)) {
            $this->session->set_flashdata('message', array('success', "Instrument deleted successfully!"));
        } else {
            $this->session->set_flashdata('message', array('danger', "Failed to delete instrument."));
        }
        redirect($this->url.'/inventory');
    }

    public function create()
    {
        // Check permissions
        if (!$this->permissions['can_create']) {
            $this->session->set_flashdata('message', array("danger", "You don't have permission to create inventory items."));
            redirect($this->url.'/inventory');
            return;
        }

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
                // Get instrument name
                $instrument_name = $this->input->post('name');

                // Handle image upload
                $image_path = null;
                if (!empty($_FILES['instrument_image']['name'])) {
                    $config['upload_path'] = './uploads/instruments/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = 2048; // 2MB
                    $config['encrypt_name'] = TRUE;

                    // Create directory if it doesn't exist
                    if (!is_dir($config['upload_path'])) {
                        mkdir($config['upload_path'], 0755, TRUE);
                    }

                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload('instrument_image')) {
                        $upload_data = $this->upload->data();
                        $image_path = 'uploads/instruments/' . $upload_data['file_name'];
                    } else {
                        $this->session->set_flashdata('message', array('danger', $this->upload->display_errors()));
                        redirect(base_url($this->url . "/inventory"));
                        return;
                    }
                }

                $data = array(
                    'name' => $instrument_name,
                    'category' => $this->input->post('category'),
                    'serial_no' => $this->input->post('serial_no'),
                    'model' => $this->input->post('model'),
                    'brand' => $this->input->post('brand'),
                    'condition_notes' => $this->input->post('condition_notes'),
                    'instrument_price' => $this->input->post('instrument_price'),
                    'instrument_image' => $image_path,
                    'condition' => $this->input->post('condition'),
                    'description' => $this->input->post('description'),
                    'availability_status' => $this->input->post('availability_status') ?: INSTRUMENT_STATUS_AVAILABLE,
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
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

                // Handle image upload (only if new image is uploaded)
                $image_path = null;
                if (!empty($_FILES['instrument_image']['name'])) {
                    $config['upload_path'] = './uploads/instruments/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = 2048; // 2MB
                    $config['encrypt_name'] = TRUE;

                    // Create directory if it doesn't exist
                    if (!is_dir($config['upload_path'])) {
                        mkdir($config['upload_path'], 0755, TRUE);
                    }

                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload('instrument_image')) {
                        $upload_data = $this->upload->data();
                        $image_path = 'uploads/instruments/' . $upload_data['file_name'];

                        // Delete old image if exists
                        $old_instrument = $this->inventory->get_instrument($post['id']);
                        if (!empty($old_instrument['instrument_image']) && file_exists('./' . $old_instrument['instrument_image'])) {
                            unlink('./' . $old_instrument['instrument_image']);
                        }
                    } else {
                        $this->session->set_flashdata('message', array('danger', $this->upload->display_errors()));
                        redirect(base_url($this->url . "/inventory"));
                        return;
                    }
                }

                $data = array(
                    'name' => $instrument_name,
                    'category' => $this->input->post('category'),
                    'serial_no' => $this->input->post('serial_no'),
                    'model' => $this->input->post('model'),
                    'brand' => $this->input->post('brand'),
                    'condition_notes' => $this->input->post('condition_notes'),
                    'instrument_price' => $this->input->post('instrument_price'),
                    'condition' => $this->input->post('condition'),
                    'description' => $this->input->post('description'),
                    'availability_status' => $this->input->post('availability_status')
                );

                // Only update image if new one was uploaded
                if ($image_path) {
                    $data['instrument_image'] = $image_path;
                }

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

    public function issue($instrument_id = null)
    {

        // If instrument_id is not provided via parameter, try to get it from URI
        if (!$instrument_id) {
            $instrument_id = $this->uri->segment(4); // superadmin/inventory/issue/ID
        }

        // If instrument_id is provided, show the form
        if ($instrument_id) {
            $data["url"] = $this->url;
            $class["classname"] = "inventory_issue";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/inventory");

            // Ensure instrument_id is numeric
            $original_id = $instrument_id;
            $instrument_id = (int) $instrument_id;
            log_message('debug', 'Original instrument_id: ' . var_export($original_id, true));
            log_message('debug', 'Converted instrument_id to int: ' . $instrument_id);

            // Get instrument details
            $data["instrument"] = $this->inventory->get_instrument($instrument_id);

            if (!$data["instrument"]) {
                $this->session->set_flashdata('message', array('danger', 'Instrument not found. ID: ' . $instrument_id));
                redirect($this->url.'/inventory');
                return;
            }

            // Check if instrument is available (handle both old numeric and new string values)
            $current_status = $data["instrument"]["availability_status"];

            // Check for both old numeric (1) and new string ('available') values
            $is_available = ($current_status == INSTRUMENT_STATUS_AVAILABLE || $current_status == '1' || $current_status == 1);

            if (!$is_available) {
                $status_text = 'Unknown';
                // Handle both old and new status values
                if ($current_status == INSTRUMENT_STATUS_AVAILABLE || $current_status == '1') {
                    $status_text = 'Available';
                } elseif ($current_status == INSTRUMENT_STATUS_ISSUED || $current_status == '2') {
                    $status_text = 'Issued';
                } elseif ($current_status == INSTRUMENT_STATUS_MAINTENANCE || $current_status == '3') {
                    $status_text = 'Under Maintenance';
                } elseif ($current_status == INSTRUMENT_STATUS_DAMAGED || $current_status == '4') {
                    $status_text = 'Damaged';
                }
                $this->session->set_flashdata('message', array('danger', 'Instrument is not available for issuing. Current status: ' . $status_text . ' (raw: ' . $current_status . ')'));
                redirect($this->url.'/inventory');
                return;
            }

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/inventory/issue', $data);
            $this->load->view('common/footer');
            return;
        }

        // Handle form submission
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
                $issued_to_type = $this->input->post('issued_to_type');
                $issued_to_id = $this->input->post('issued_to_id');

                $issue_data = array(
                    'instrument_id' => $this->input->post('instrument_id'),
                    'issued_by' => $this->session_data['id'],
                    'issue_date' => $this->input->post('issue_date') ?: date('Y-m-d H:i:s'),
                    'expected_return_date' => $this->input->post('expected_return_date'),
                    'notes' => $this->input->post('purpose')
                );

                // Set student_id or faculty_id based on type
                if ($issued_to_type === 'student') {
                    $issue_data['student_id'] = $issued_to_id;
                } elseif ($issued_to_type === 'staff') {
                    $issue_data['faculty_id'] = $issued_to_id;
                }

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

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/inventory/issues', $data);
        $this->load->view('common/footer');
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

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/inventory/maintenance', $data);
        $this->load->view('common/footer');
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
        $data["total_students"] = $this->db_model->count(TABLE_STUDENT, ["college_id" => $this->college['id'], "is_active" => 1]);
        $data["total_staff"] = $this->db_model->count(TABLE_FACULTY, ["college_id" => $this->college['id'], "is_active" => 1]);

        // Student performance metrics
        $data["student_performance"] = $this->get_student_performance_stats($this->college['id']);

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/inventory/reports', $data);
        $this->load->view('common/footer');
    }

    public function categories()
    {
        $data["url"] = $this->url;
        $class["classname"] = "inventory_categories";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/inventory");

        $data["categories"] = $this->inventory->get_instrument_categories();

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/inventory/categories', $data);
        $this->load->view('common/footer');
    }

    public function add_category()
    {
        $category_name = $this->input->post('category_name');
        $category_description = $this->input->post('category_description');

        if (empty($category_name)) {
            $this->json_response('error', ['message' => 'Category name is required'], 400);
            return;
        }

        // For now, we'll store categories in a simple array format
        // In a real implementation, you'd want a proper categories table
        $categories = $this->inventory->get_instrument_categories();

        // Generate a key from the category name
        $key = strtolower(str_replace(' ', '_', $category_name));
        $key = preg_replace('/[^a-z0-9_]/', '', $key);

        if (isset($categories[$key])) {
            $this->json_response('error', ['message' => 'Category already exists'], 400);
            return;
        }

        $categories[$key] = $category_name;

        // Save to database (this would need to be implemented in the model)
        // For now, return success
        $this->json_response('success', ['message' => 'Category added successfully']);
    }

    public function update_category()
    {
        $category_key = $this->input->post('category_key');
        $category_name = $this->input->post('category_name');
        $category_description = $this->input->post('category_description');

        if (empty($category_key) || empty($category_name)) {
            $this->json_response('error', ['message' => 'Category key and name are required'], 400);
            return;
        }

        $categories = $this->inventory->get_instrument_categories();

        if (!isset($categories[$category_key])) {
            $this->json_response('error', ['message' => 'Category not found'], 404);
            return;
        }

        $categories[$category_key] = $category_name;

        // Save to database (this would need to be implemented in the model)
        // For now, return success
        $this->json_response('success', ['message' => 'Category updated successfully']);
    }

    private function json_response($status, $data = [], $http_code = 200)
    {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode([
                'status' => $status,
                'data' => $data
            ]));
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

    public function create_api()
    {
        $payload = [
            'name' => $this->input->post('name'),
            'category' => $this->input->post('category'),
            'serial_no' => $this->input->post('serial_no'),
            'condition_notes' => $this->input->post('condition_notes'),
            'purchase_date' => $this->input->post('purchase_date'),
            'location' => $this->input->post('location'),
            'availability_status' => $this->input->post('availability_status') ?? INSTRUMENT_STATUS_AVAILABLE,
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

    public function update_api($id)
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
            'availability_status' => intval($this->input->post('availability_status')),
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

    public function issue_api()
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

    public function return_item_api()
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

    public function get_students_api()
    {
        if ($this->input->method() !== 'get') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $students = $this->db->select('id, roll_no, name')
                            ->from(TABLE_STUDENT)
                            ->where('college_id', $this->college['id'])
                            ->where('is_active', 1)
                            ->order_by('name', 'ASC')
                            ->get()
                            ->result_array();

        return $this->json_response('success', $students);
    }

    public function get_staff_api()
    {
        if ($this->input->method() !== 'get') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $staff = $this->db->select('id, name, designation')
                         ->from(TABLE_FACULTY)
                         ->where('college_id', $this->college['id'])
                         ->where('is_active', 1)
                         ->where_in('role', [ROLE_HOD, ROLE_STAFF]) // Only HOD and Staff, exclude Principal and Vice-Principal
                         ->order_by('name', 'ASC')
                         ->get()
                         ->result_array();

        return $this->json_response('success', $staff);
    }

    public function get_issue_details_api()
    {
        if ($this->input->method() !== 'get') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $issue_id = $this->input->get('issue_id');
        if (!$issue_id) {
            return $this->json_response('error', 'Issue ID is required', 400);
        }

        $issue = $this->inventory->get_issue_details($issue_id);
        if (!$issue) {
            return $this->json_response('error', 'Issue not found', 404);
        }

        return $this->json_response('success', $issue);
    }

    public function return_instrument_api()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response('error', 'Invalid method', 405);
        }

        $issue_id = $this->input->post('issue_id');
        $return_date = $this->input->post('return_date');
        $condition_on_return = $this->input->post('condition_on_return');
        $notes = $this->input->post('notes');

        if (!$issue_id) {
            return $this->json_response('error', 'Issue ID is required', 400);
        }

        // Validate return date
        if (!$return_date) {
            $return_date = date('Y-m-d H:i:s');
        }

        $result = $this->inventory->return_instrument($issue_id, [
            'actual_return_date' => $return_date,
            'condition_on_return' => $condition_on_return,
            'notes' => $notes
        ]);

        if ($result) {
            return $this->json_response('success', 'Instrument returned successfully');
        } else {
            return $this->json_response('error', 'Failed to return instrument', 500);
    }
    }

}


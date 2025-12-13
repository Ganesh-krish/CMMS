<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Announcement extends CI_Controller
{
    private $url;
    private $college;
    private $session_data;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('common', 'faculty_common');
        $this->load->model('Db_model', 'db_model');
        $this->load->model('Announcement_model', 'announcement');

        $this->url = $this->uri->segment(1);
        $this->faculty_common->check_user_session($this->url);
        $this->college = $this->faculty_common->get_default_college();
        $this->session_data = $this->session->userdata($this->url);

        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;

        // Only SuperAdmin, Admin (Vice-Principal), and HOD can access announcements
        $allowed_roles = [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD];
        if (!in_array($role, $allowed_roles, true)) {
            $this->faculty_common->redirect_route($role, $this->url);
        }
    }

    public function index()
    {
        $data["url"] = $this->url;
        $class["classname"] = "announcements";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/announcements");

        $role = $this->session_data['role'] ?? $this->session_data['designation'];
        $department = $this->session_data['department'] ?? null;

        // Get filters
        $filters = [
            'college_id' => $this->college['id'],
            'is_active' => 1,
            'user_role' => $role,
            'user_department' => $department
        ];

        if ($this->input->get('visibility')) {
            $filters['visibility'] = $this->input->get('visibility');
        }

        if ($this->input->get('search')) {
            $filters['search'] = $this->input->get('search');
        }

        $data["announcements"] = $this->announcement->get_announcements($filters);
        $data["stats"] = $this->announcement->get_announcement_stats($this->college['id']);
        $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["college_id" => $this->college['id'], "is_active" => 1]);

        // Check if user can create announcements
        $data["can_create"] = in_array($role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD]);

        $this->load->view('faculty/faculty/sidebar', $class);
        $this->load->view('faculty/announcements/index', $data);
        $this->load->view('faculty/announcements/modals');
        $this->load->view('faculty/faculty/footer');
    }

    public function create()
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'];

        // Check permissions
        if (!in_array($role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD])) {
            $this->session->set_flashdata('message', [0, 'You do not have permission to create announcements.']);
            redirect($this->url.'/announcements');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('title', 'Title', 'required|trim');
            $this->form_validation->set_rules('message', 'Message', 'required|trim');
            $this->form_validation->set_rules('visibility', 'Visibility', 'required|in_list[all,department]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('message', [0, validation_errors()]);
                redirect($this->url.'/announcements');
            }

            $visibility = $this->input->post('visibility');
            $department_id = null;

            if ($visibility === 'department') {
                $department_id = $this->input->post('department_id');
                // For HOD, force their own department
                if ($role == ROLE_HOD) {
                    $department_id = $this->session_data['department'];
                }
                if (empty($department_id)) {
                    $this->session->set_flashdata('message', [0, 'Department is required for department announcements.']);
                    redirect($this->url.'/announcements');
                }
            }

            $data = [
                'title' => $this->input->post('title'),
                'message' => $this->input->post('message'),
                'visibility' => $visibility,
                'department_id' => $department_id,
                'sender_id' => $this->session_data['id'],
                'college_id' => $this->college['id'],
                'is_active' => 1,
                'priority' => $this->input->post('priority') ?? 'normal'
            ];

            $id = $this->announcement->create_announcement($data);

            if ($id) {
                $this->session->set_flashdata('message', [1, 'Announcement created successfully.']);
            } else {
                $this->session->set_flashdata('message', [0, 'Failed to create announcement.']);
            }

            redirect($this->url.'/announcements');
        }

        // For GET request, show create form
        $data["url"] = $this->url;
        $class["classname"] = "announcements";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/announcements");

        $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["college_id" => $this->college['id'], "is_active" => 1]);

        $this->load->view('faculty/faculty/sidebar', $class);
        $this->load->view('faculty/announcements/create', $data);
        $this->load->view('faculty/faculty/footer');
    }

    public function edit($id)
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'];
        $user_id = $this->session_data['id'];

        // Get announcement
        $announcement = $this->announcement->get_announcement($id);

        if (!$announcement) {
            $this->session->set_flashdata('message', [0, 'Announcement not found.']);
            redirect($this->url.'/announcements');
        }

        // Check permissions - only sender can edit their own announcements
        if ($announcement['sender_id'] != $user_id) {
            $this->session->set_flashdata('message', [0, 'You can only edit your own announcements.']);
            redirect($this->url.'/announcements');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('title', 'Title', 'required|trim');
            $this->form_validation->set_rules('message', 'Message', 'required|trim');
            $this->form_validation->set_rules('visibility', 'Visibility', 'required|in_list[all,department]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('message', [0, validation_errors()]);
                redirect($this->url.'/announcements/edit/'.$id);
            }

            $visibility = $this->input->post('visibility');
            $department_id = null;

            if ($visibility === 'department') {
                $department_id = $this->input->post('department_id');
                // For HOD, force their own department
                if ($role == ROLE_HOD) {
                    $department_id = $this->session_data['department'];
                }
                if (empty($department_id)) {
                    $this->session->set_flashdata('message', [0, 'Department is required for department announcements.']);
                    redirect($this->url.'/announcements/edit/'.$id);
                }
            }

            $data = [
                'title' => $this->input->post('title'),
                'message' => $this->input->post('message'),
                'visibility' => $visibility,
                'department_id' => $department_id,
                'priority' => $this->input->post('priority') ?? 'normal'
            ];

            if ($this->announcement->update_announcement($id, $data)) {
                $this->session->set_flashdata('message', [1, 'Announcement updated successfully.']);
            } else {
                $this->session->set_flashdata('message', [0, 'Failed to update announcement.']);
            }

            redirect($this->url.'/announcements');
        }

        // For GET request, show edit form
        $data["url"] = $this->url;
        $class["classname"] = "announcements";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/announcements");

        $data["announcement"] = $announcement;
        $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT, ["college_id" => $this->college['id'], "is_active" => 1]);

        $this->load->view('faculty/faculty/sidebar', $class);
        $this->load->view('faculty/announcements/edit', $data);
        $this->load->view('faculty/faculty/footer');
    }

    public function delete($id)
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'];
        $user_id = $this->session_data['id'];

        // Get announcement
        $announcement = $this->announcement->get_announcement($id);

        if (!$announcement) {
            $this->session->set_flashdata('message', [0, 'Announcement not found.']);
            redirect($this->url.'/announcements');
        }

        // Check permissions - only sender or SuperAdmin can delete
        if ($announcement['sender_id'] != $user_id && $role != ROLE_SUPERADMIN) {
            $this->session->set_flashdata('message', [0, 'You do not have permission to delete this announcement.']);
            redirect($this->url.'/announcements');
        }

        if ($this->announcement->delete_announcement($id)) {
            $this->session->set_flashdata('message', [1, 'Announcement deleted successfully.']);
        } else {
            $this->session->set_flashdata('message', [0, 'Failed to delete announcement.']);
        }

        redirect($this->url.'/announcements');
    }

    public function view($id)
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'];
        $department = $this->session_data['department'] ?? null;

        $announcement = $this->announcement->get_announcement($id);

        if (!$announcement) {
            $this->session->set_flashdata('message', [0, 'Announcement not found.']);
            redirect($this->url.'/announcements');
        }

        // Check visibility permissions
        $can_view = false;
        if ($announcement['visibility'] === 'all') {
            $can_view = true;
        } elseif ($announcement['visibility'] === 'department' && $announcement['department_id'] == $department) {
            $can_view = true;
        } elseif (in_array($role, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL])) {
            $can_view = true;
        }

        if (!$can_view) {
            $this->session->set_flashdata('message', [0, 'You do not have permission to view this announcement.']);
            redirect($this->url.'/announcements');
        }

        $data["url"] = $this->url;
        $class["classname"] = "announcements";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/announcements");

        $data["announcement"] = $announcement;

        $this->load->view('faculty/faculty/sidebar', $class);
        $this->load->view('faculty/announcements/view', $data);
        $this->load->view('faculty/faculty/footer');
    }

    // API endpoint for getting user announcements (for students and other users)
    public function get_user_announcements()
    {
        $role = $this->session_data['role'] ?? $this->session_data['designation'];
        $department = $this->session_data['department'] ?? null;
        $user_id = $this->session_data['id'];

        $announcements = $this->announcement->get_user_visible_announcements(
            $user_id,
            $role,
            $department,
            $this->college['id']
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'data' => $announcements]));
    }
}

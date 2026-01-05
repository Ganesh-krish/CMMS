<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class College extends CI_Controller {

    private $url;
    private $college;
    private $session_data;
    private $permissions;

    function __construct() {
        parent::__construct();

        $this->load->model('common', 'faculty_common');
        $this->load->model('Db_model', 'db_model');
        $this->load->model('Test_model', 'test_model');

        $this->url = $this->uri->segment(1);

        // Use unified session approach
        $unified_user = $this->session->userdata('user');
        if ($unified_user && isset($unified_user['user_type']) && $unified_user['user_type'] === 'faculty') {
            // Unified session access
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $unified_user;
        } else {
            // Fallback for legacy access
            $this->faculty_common->check_user_session($this->url);
            $this->college = $this->faculty_common->get_default_college();
            $this->session_data = $this->session->userdata($this->url);
        }

        $this->permissions = $this->faculty_common->get_access_permissions($this->session_data);

        // Role-based access control for college management
        $role = (int)($this->session_data['role'] ?? $this->session_data['designation'] ?? null);

        // Allow appropriate roles to access college view:
        // - Principal: Full access to college management
        // - Vice Principal: Can view college information
        // - HOD: Can view college information
        $allowed_roles = [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD];

        if (!in_array($role, $allowed_roles, true)) {
            redirect($this->url.'/dashboard');
        }
    }

    public function index() {
        return redirect($this->url.'/college/view');
    }

    public function add() {
        $this->session->set_flashdata('message', array('warning',"Single-college mode enabled. College cannot be created."));
        return redirect($this->url.'/college/view');
    }

    public function changeStatus($id,$status){
        $this->session->set_flashdata('message', array('warning',"Single-college mode enabled. Status changes are disabled."));
        return redirect($this->url.'/college/view');
    }

    public function edit($id){
        if ((int)$id !== (int)SINGLE_COLLEGE_ID) {
            $this->session->set_flashdata('message', array('warning',"Single-college mode enabled. Only the default college can be managed."));
            return redirect($this->url.'/college/view');
        }

        $post = $this->input->post();
        if($post){
            $data = array(
                'name' => $this->input->post('name'),
                'established_year' => $this->input->post('established_year'),
                'address' => $this->input->post('address'),
                'correspondent' => $this->input->post('correspondent'),
                'vice_correspondent' => $this->input->post('vice_correspondent'),
                'our_vision' => $this->input->post('our_vision'),
                'our_mission' => $this->input->post('our_mission'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'created_by' => $this->session_data['id']
            );

            // Handle logo upload
            if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0){
                $logo_path = $this->_upload_file('logo', 'college');
                if($logo_path){
                    $data['logo'] = $logo_path;
                }
            }

            // Handle banner upload
            if(isset($_FILES['banner']) && $_FILES['banner']['error'] == 0){
                $banner_path = $this->_upload_file('banner', 'college');
                if($banner_path){
                    $data['banner'] = $banner_path;
                }
            }

            $this->db_model->update(TABLE_COLLEGE,$data,["id"=>$id]);
            $this->session->set_flashdata('message', array('success',"College updated."));
            return redirect($this->url.'/college/view');
        }else{
            $data["college"] = $this->db_model->get_row(TABLE_COLLEGE,["id"=>$id,"is_active"=>1]);

            $data["url"] = $this->url;
            $class["classname"] = "college";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/college/view");

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/college/edit', $data);
            $this->load->view('common/footer');
        }
    }

    public function view(){
        $college = $this->db_model->get_row(TABLE_COLLEGE,["id"=>SINGLE_COLLEGE_ID]);
        $data["college"] = $college;

        $data["url"] = $this->url;
        $class["classname"] = "college";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/college/view");

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/college/view', $data);
        $this->load->view('common/footer');
    }

    private function _upload_file($field_name, $folder) {
        // Create upload directory if it doesn't exist
        $upload_path = './uploads/' . $folder . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = $field_name == 'logo' ? 2048 : 5120; // 2MB for logo, 5MB for banner
        $config['file_name'] = $field_name . '_' . time() . '_' . rand(1000, 9999);

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($field_name)) {
            $this->session->set_flashdata('message', array('danger', $this->upload->display_errors()));
            return false;
        } else {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }
    }
}
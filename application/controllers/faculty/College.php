<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class College extends CI_Controller {

    private $url;
    private $college;
    private $session_data;
    private $permissions;

    function __construct() {
        parent::__construct();

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

        // Only SuperAdmin can manage colleges
        $role = $this->session_data['role'] ?? $this->session_data['designation'] ?? null;
        if ($role !== ROLE_SUPERADMIN) {
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
                'description' => $this->input->post('description'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'phone_number' => $this->input->post('phone_number'),
                'email' => $this->input->post('email'),
                'created_by' => $this->session_data['id']
            );
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
            $this->load->view('faculty/college/view', $data);
            $this->load->view('common/footer');
        }
    }

    public function view(){
        $college = $this->db_model->get_row(TABLE_COLLEGE,["id"=>SINGLE_COLLEGE_ID]);
        if ($college) {
            $college["subscription_count"] = $this->db_model->get_subscription_count($college["id"]);
            $data["colleges"] = [$college];
        } else {
            $data["colleges"] = [];
        }

        $data["url"] = $this->url;
        $class["classname"] = "college";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url."/college/view");

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/college/view', $data);
        $this->load->view('common/footer');
    }
}
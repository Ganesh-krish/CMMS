<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Groups extends CI_Controller
{
    private $url;
    private $college;
    private $session_data;
    function __construct()
    {
        parent::__construct();
        $this->load->model('common', 'common');
        $this->load->model('Db_model', 'db_model');
        $this->load->model('Test_model', 'test_model');
        $this->url = $this->uri->segment(1);

        // Handle both faculty and admin sessions
        if ($this->url === 'admin' || empty($this->session->userdata($this->url))) {
            // Admin session - use owner session
            $this->common->check_user_session(); // Validate session
            $admin_session = $this->session->userdata('owner');
            $this->session_data = [
                'id' => $admin_session['id'] ?? null,
                'name' => $admin_session['name'] ?? 'Admin',
                'role' => ROLE_SUPERADMIN,
                'designation' => DESIGNATION_PRINCIPAL,
                'department' => null // Admin can see all departments
            ];
        } else {
            // Faculty session
            $this->common->check_user_session($this->url);
            $this->session_data = $this->session->userdata($this->url);
        }

        $this->college = $this->common->get_default_college();
    }
    public function index($cource_id=null)
    {
        // var_dump($cource_id);die;
        $data["url"] = $this->url;
        $class["classname"] = "cources";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url . "/staff");
        $data["college_id"] = $this->college['id'];
        $data["cource_id"] = $cource_id;
        $data["department"] = $this->session_data['department'];
        $data["tests"] = $this->db_model->get_all(TABLE_TESTS,["is_active"=>1,"cource_id"=>$cource_id,"college_id"=>$this->college['id'],"created_by"=>$this->session_data['id']]);

        $this->load->view('common/sidebar', $class);
        $this->load->view('test/view', $data);
        $this->load->view('common/footer');
    }
  
    public function view_students($cource_id=null,$test_id=null)
    {
        // var_dump($cource_id,$test_id);die;
        $data["url"] = $this->url;
        $class["classname"] = "cources";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url . "/staff");
        $data["college_id"] = $this->college['id'];
        $data["department"] = $this->session_data['department'];
        $data["cource_id"] = $cource_id;
        $data["test_id"] = $test_id;
        $data["questions"] = $this->db_model->get_all(TABLE_QUESTIONS, ["is_active" => true, "college_id" => $this->college['id']]);
        $data["tests"] = $this->db_model->get_row(TABLE_TESTS,["id"=>$test_id,"is_active"=>1,"college_id"=>$this->college['id'],"created_by"=>$this->session_data['id']]);
        $this->load->view('common/sidebar', $class);
        $this->load->view('test/view_students', $data);
        $this->load->view('common/footer');
    }
    public function add()
    {
        $post = $this->input->post();
        if ($post) {
            $this->form_validation->set_rules('group_name', 'Group Name', 'trim|required|min_length[3]|max_length[50]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect(base_url($this->url.'/groups/add'));
            } else {
                $group_name = trim($this->input->post('group_name'));

                // Check if group with same name already exists
                $existing_group = $this->db_model->get_row(TABLE_GROUPS, [
                    'name' => $group_name,
                    'college_id' => $this->college['id'],
                    'is_active' => 1
                ]);

                if ($existing_group) {
                    $this->session->set_flashdata('message', array('info', "Group '{$group_name}' already exists. Using existing group."));
                } else {
                    $data = [
                        'name' => $group_name,
                        'created_by' => $this->session_data['id'],
                        'college_id' => $this->college['id'],
                    ];
                    $this->db_model->insert(TABLE_GROUPS, $data);
                    $this->session->set_flashdata('message', array('success', "Group created successfully!"));
                }

                // Redirect based on user role
                if ($this->session_data['role'] === ROLE_SUPERADMIN) {
                    // Admin user - redirect to students page
                    redirect(base_url('Dashboard/students'));
                } else {
                    // Faculty user - redirect based on designation
                    $map = [
                        DESIGNATION_STAFF => "staff",
                        DESIGNATION_PRINCIPAL => "principal",
                    ];
                    $path = $map[$this->session_data["designation"]] ?? "hod";
                    redirect(base_url($this->url.'/'.$path .'/students'));
                }
            }
        } else {
            $data["url"] = $this->url;
            $class["classname"] = "students";
            $class["url"] =  $this->url;
            $data["title"] = "Add Group";
            $data["college_id"] = $this->college['id'];
            $data["department"] = $this->session_data['department'];

            // Set sidebar href based on user role
            if ($this->session_data['role'] === ROLE_SUPERADMIN) {
                // Admin user
                $class["sidebar_href"] = base_url('Dashboard/students');
            } else {
                // Faculty user
                $map = [
                    DESIGNATION_STAFF => "staff",
                    DESIGNATION_PRINCIPAL => "principal",
                ];
                $path = $map[$this->session_data["designation"]] ?? "hod";
                $class["sidebar_href"] = base_url($this->url . "/" . $path);
            }

            $desination_mapper = [
                DESIGNATION_STAFF => 'staff',
                DESIGNATION_HOD => 'hod',
                DESIGNATION_PRINCIPAL => 'principal'
            ];

            $data['designation'] = $desination_mapper[$this->session_data['designation']] ?? 'principal';
            $conditions = ["is_active" => true, 
            "college_id" => $this->college['id'],
            ];
            
            // if ($this->session_data['designation'] != DESIGNATION_PRINCIPAL) {
            //     $conditions['department'] = $this->session_data['department'];
            // };



            // $data["staff"] = $this->db_model->get_all(TABLE_STUDENT,$conditions);
            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/groups/add',$data);
            $this->load->view('common/footer');
        }
    }
    public function edit($group_id)
    {   
        $post = $this->input->post();
        if ($post) {
            $this->form_validation->set_rules('group_name', 'Group Name', 'trim|required|min_length[3]|max_length[50]');
        
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array("danger", validation_errors()));
                return redirect(base_url($this->url.'/groups/add'));
            } else {
                $data = array(
                    'name' => $this->input->post('group_name'),
                    'updated_by' => $this->session_data['id'],
                );

                // var_dump($data);die;
                
                if ($this->db_model->update(TABLE_GROUPS, $data, ["id" => $group_id,'college_id' => $this->college['id'],"is_active" => 1])) {
                    $this->session->set_flashdata('message', array('success', "Group updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create the group."));
                }
                redirect(base_url($this->url.'/staff/students'));
            }
        } else {
            $data["url"] = $this->url;
            $class["classname"] = "groups";
            $class["url"] =  $this->url;
            $data["title" ] = "Edit Group";
            $class["sidebar_href"] = base_url($this->url . "/staff");
            $data["college_id"] = $this->college['id'];
            $data["department"] = $this->session_data['department'];

            $desination_mapper = [
                DESIGNATION_STAFF => 'staff',
                DESIGNATION_HOD => 'hod',
                DESIGNATION_PRINCIPAL => 'principal'
            ];
                
            $data['designation'] = $desination_mapper[$this->session_data['designation']];

            $data["groups"] = $this->db_model->get_row(TABLE_GROUPS,['id'=>$group_id,"is_active"=>1,"college_id"=>$this->college['id']]);
            // $data["group_members"] = $this->db_model->get_all(TABLE_MEMGROUPS, ["group_id" => $group_id, "college_id" => $this->college['id']]);
            // $studentIds = array_column($data["group_members"], 'student_id');
            // $data["staff"] = $this->db_model->get_all(TABLE_STUDENT, ["is_active" => true, "college_id" => $this->college['id'], "department" => $this->session_data['department']]);
            // $data["studentIds"] = $studentIds;

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/groups/add',$data);
            $this->load->view('common/footer');
        }
    }

    public function group_students($group_id) {
        $data["url"] = $this->url;
        $class["classname"] = "groups";
        $class["url"] = $this->url;
        $class["sidebar_href"] = base_url($this->url . "/staff");
        

        $data["group"] = $this->db_model->get_row(TABLE_GROUPS, ["id" => $group_id, "college_id" => $this->college['id']]);

        if (!$data["group"]) {
            $this->session->set_flashdata('message', array('danger', 'Group not found'));
            redirect($this->url . '/staff/students');
        }

        $group_members = $this->db_model->get_all(TABLE_MEMGROUPS, ["group_id" => $group_id]);
        $student_ids = array_column($group_members, 'student_id');

        $desination_mapper = [
                DESIGNATION_STAFF => 'staff',
                DESIGNATION_HOD => 'hod',
                DESIGNATION_PRINCIPAL => 'principal'
        ];

        $data['designation'] = $desination_mapper[$this->session_data['designation']];
        
        if (!empty($student_ids)) {
            $data["students"] = $this->db_model->get_all(TABLE_STUDENT, ["id IN (" . implode(',', $student_ids) . ")" => null]);
        } else {
            $data["students"] = [];
        }
        
        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/groups/group_students', $data);
        $this->load->view('common/footer');
    }

    public function addMemberstoGroup() {

        $post = $this->input->post();


        if ($post) {
            $this->form_validation->set_rules('group_id', 'Group ID', 'required|numeric');
            if ($this->form_validation->run() == FALSE) {
                $response = [
                    'status' => 'error',
                    'message' => strip_tags(validation_errors())
                ];

                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }

            $student_ids = $this->input->post('student_ids');
            $group_id = $this->input->post('group_id');


            if (empty($student_ids) || !is_array($student_ids)) {
                $response = [
                    'status' => 'error',
                    'message' => "No students selected or invalid format."
                ];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }

            foreach ($student_ids as $student_id) {
                $insertData = [
                    'group_id'   => $group_id,
                    'student_id' => $student_id,
                    'college_id'     => $this->college['id'],
                    'created_by'   => $this->session_data['id'],
                ];

            $exists =  $this->db_model->get_row(TABLE_MEMGROUPS, ['group_id' => $group_id, 'student_id' => $student_id]);

            if(!$exists){
                $insert = $this->db_model->insert(TABLE_MEMGROUPS, $insertData);
            }

            }

            $response = [
                'status' => 'success',
                'message' => "Members Added successfully!"
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => 'error',
                'message' => "Invalid request. No data received."
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    public function deleteGroup()
    {
        $post = $this->input->post();
        $groupId = $post['id'];
        if ($post) {
            $this->form_validation->set_rules('id', 'Group ID', 'required|numeric');
            if ($this->form_validation->run() == FALSE) {
                $response = [
                    'status' => 'error',
                    'message' => strip_tags(validation_errors())
                ];

                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
            $conditions = [
                'id' => $groupId,
                'is_active' => 1,
                'college_id' =>$this->college['id']
            ];
            $group_exists = $this->db_model->get_row(TABLE_GROUPS,$conditions);
            if($group_exists){
                
                $this->db_model->update(TABLE_GROUPS, ['is_active' => 0], ['id' => $groupId]);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Group deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Group not found or already inactive'
                ]);
            }

        }

    }
}

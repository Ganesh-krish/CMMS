<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Groups extends CI_Controller
{
    function __construct() {
        parent::__construct();

        $this->load->model('common', 'faculty_common');
        $this->load->model('Db_model', 'db_model');

        $this->url = $this->uri->segment(1);

        // Use unified session approach
        $unified_user = $this->session->userdata('user');
        // print_r($unified_user);
         
        // Convert object to array if needed (session serialization can change data type)
        if (is_object($unified_user)) {
            $unified_user = (array) $unified_user;
        }

        // print_r($unified_user);
           
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
        // print_r($this->session_data);
           
        $this->permissions = $this->faculty_common->get_access_permissions($this->session_data);
        // print_r($this->permissions);
          
        // Role-based access control for groups management
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);

        // Allow appropriate roles to access groups management:
        // - Principal: Full access to all group management
        // - Vice Principal: Can manage groups
        // - HOD: Can manage groups in their department
        // - Staff: Can view groups in their department
        $allowed_roles = [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF];

        if (!in_array($role, $allowed_roles, true)) {
            redirect($this->url.'/dashboard');
        }
    }

    public function index($cource_id=null)
    {
        // var_dump($cource_id);die;
        $data["url"] = $this->url;
        $class["classname"] = "cources";
        $class["url"] =  $this->url;
        $class["sidebar_href"] = base_url($this->url . "/staff");
        $class["college"] = $this->college;
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
        $class["college"] = $this->college;
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
                if ($this->session_data['role'] === ROLE_PRINCIPAL) {
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
            $class["college"] = $this->college;
            $data["title"] = "Add Group";
            $data["college_id"] = $this->college['id'];
            $data["department"] = $this->session_data['department'];

            // Set sidebar href based on user role
            if ($this->session_data['role'] === ROLE_PRINCIPAL) {
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
        $class["college"] = $this->college;
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
                
                $this->db_model->delete(TABLE_GROUPS, ['id' => $groupId]);
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

    // ============ MUSIC GROUPS MANAGEMENT METHODS ============

    public function groups() {
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);

        // Base conditions
        $conditions = [
            "is_active" => 1,
            "college_id" => $this->college['id']
        ];

        // Filter by department for HODs and Staff (only if department column exists)
        if ($role == ROLE_HOD || $role == ROLE_STAFF) {
            if ($this->db->field_exists('department', TABLE_GROUPS)) {
                $conditions["department"] = $this->session_data['department'];
            }
            // If no department column, allow access to all groups (they can still only manage students from their department)
        }

        $data["groups"] = $this->db_model->get_all(TABLE_GROUPS, $conditions);

        // Get student count for each group
        $total_students_in_groups = 0;
        foreach ($data["groups"] as &$group) {
            $group['student_count'] = count($this->db_model->get_all(TABLE_MEMGROUPS, [
                "group_id" => $group['id'],
                "is_active" => 1
            ]));
            $total_students_in_groups += $group['student_count'];
        }

        // Statistics for music groups
        $data["stats"] = array(
            "total_groups" => count($data["groups"]),
            "total_students_in_groups" => $total_students_in_groups
        );

        $data["url"] = $this->url;
        $data["current_user_role"] = $role; // Pass current user role to view
        $class["classname"] = "groups";
        $class["url"] = $this->url;
        $class["college"] = $this->college;

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/groups/view', $data);
        $this->load->view('common/footer');
    }

    public function add_group() {
        // Check permissions - only Principal, Vice Principal, and HOD can create groups
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        if (!in_array($role, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD], true)) {
            $this->session->set_flashdata('message', array('danger', "You don't have permission to create groups."));
            redirect($this->url.'/groups');
            return;
        }

        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Group Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/groups');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'college_id' => $this->college['id'],
                    'created_by' => $this->session_data['id'],
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                );

                if ($this->db_model->insert(TABLE_GROUPS, $data)) {
                    $this->session->set_flashdata('message', array('success', "Music Group created successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to create music group."));
                }
                redirect($this->url.'/groups');
            }
        } else {
            $data["url"] = $this->url;
            $class["classname"] = "groups";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/groups");
            $class["college"] = $this->college;

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/groups/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function edit_group($id) {
        // Check permissions - only Principal, Vice Principal, and HOD can edit groups
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        if (!in_array($role, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD], true)) {
            $this->session->set_flashdata('message', array('danger', "You don't have permission to edit groups."));
            redirect($this->url.'/groups');
            return;
        }

        $post = $this->input->post();
        if($post){
            $this->form_validation->set_rules('name', 'Group Name', 'trim|required|min_length[2]|max_length[100]');
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', array('danger', validation_errors()));
                redirect($this->url.'/groups');
            } else {
                $data = array(
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'updated_by' => $this->session_data['id'],
                    'updated_at' => date('Y-m-d H:i:s')
                );

                if ($this->db_model->update(TABLE_GROUPS, $data, ["id" => $id])) {
                    $this->session->set_flashdata('message', array('success', "Music Group updated successfully!"));
                } else {
                    $this->session->set_flashdata('message', array('danger', "Failed to update music group."));
                }
                redirect($this->url.'/groups');
            }
        } else {
            $data["group"] = $this->db_model->get_row(TABLE_GROUPS, ["id" => $id, "is_active" => 1]);
            if (!$data["group"]) {
                $this->session->set_flashdata('message', array('danger', "Music Group not found."));
                redirect($this->url.'/groups');
            }

            $data["url"] = $this->url;
            $class["classname"] = "groups";
            $class["url"] = $this->url;
            $class["sidebar_href"] = base_url($this->url."/groups");
            $class["college"] = $this->college;

            $this->load->view('common/sidebar', $class);
            $this->load->view('faculty/groups/add', $data);
            $this->load->view('common/footer');
        }
    }

    public function delete_group($id) {
        // Check permissions - only Principal, Vice Principal, and HOD can delete groups
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        if (!in_array($role, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD], true)) {
            $this->session->set_flashdata('message', array('danger', "You don't have permission to delete groups."));
            redirect($this->url.'/groups');
            return;
        }

        $result = $this->db_model->delete(TABLE_GROUPS, ["id" => $id]);
        $message = array('success', "Music Group deleted successfully!");
        if (!$result) {
            $message = array('danger', "Failed to delete music group.");
        }
        $this->session->set_flashdata('message', $message);
        redirect($this->url.'/groups');
    }

    public function group_students($group_id) {
        $data["group"] = $this->db_model->get_row(TABLE_GROUPS, ["id" => $group_id, "is_active" => 1]);
        if (!$data["group"]) {
            $this->session->set_flashdata('message', array('danger', "Music Group not found."));
            redirect($this->url.'/groups');
        }

        // Get students in this group
        $group_members = $this->db_model->get_all(TABLE_MEMGROUPS, [
            'group_id' => $group_id,
            'is_active' => 1
        ]);

        $student_ids = array_column($group_members, 'student_id');

        if (!empty($student_ids)) {
            $data["group_students"] = $this->db_model->get_all(TABLE_STUDENT, [
                "id IN (" . implode(',', $student_ids) . ")" => null,
                "is_active" => 1
            ]);
        } else {
            $data["group_students"] = [];
        }

        // Get all students not in this group for adding
        $existing_student_ids = array_column($data["group_students"], 'id');
        $conditions = ["is_active" => 1, "college_id" => $this->college['id']];

        // Filter by department for HODs and Staff
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        if ($role == ROLE_HOD || $role == ROLE_STAFF) {
            $dept_id = $this->session_data['department'];
            $conditions["department"] = $dept_id;
        }

        if (!empty($existing_student_ids)) {
            $this->db_model->db->where_not_in('id', $existing_student_ids);
        }
        $data["available_students"] = $this->db_model->get_all(TABLE_STUDENT, $conditions);

        $data["url"] = $this->url;
        $data["current_user_role"] = $role; // Pass current user role to view
        $class["classname"] = "groups";
        $class["url"] = $this->url;
        $class["college"] = $this->college;

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/groups/group_students', $data);
        $this->load->view('common/footer');
    }

    public function add_students_to_group($group_id) {
        // Check permissions - only Principal, Vice Principal, and HOD can manage group membership
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        if (!in_array($role, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD], true)) {
            $this->session->set_flashdata('message', array('danger', "You don't have permission to manage group membership."));
            redirect($this->url.'/groups/group_students/'.$group_id);
            return;
        }

        $post = $this->input->post();
        if($post){
            $student_ids = $this->input->post('student_ids');
            $group = $this->db_model->get_row(TABLE_GROUPS, ["id" => $group_id, "is_active" => 1]);

            if (!$group) {
                $this->session->set_flashdata('message', array('danger', 'Music Group not found.'));
                redirect($this->url.'/groups');
                return;
            }

            if (empty($student_ids) || !is_array($student_ids)) {
                $this->session->set_flashdata('message', array('danger', "No students selected."));
                redirect($this->url.'/groups/group_students/'.$group_id);
                return;
            }

            $success_count = 0;
            foreach ($student_ids as $student_id) {
                // Check if student is already in the group
                $exists = $this->db_model->get_row(TABLE_MEMGROUPS, ['group_id' => $group_id, 'student_id' => $student_id]);
                if (!$exists) {
                    $data = array(
                        'group_id' => $group_id,
                        'student_id' => $student_id,
                        'college_id' => $this->college['id'],
                        'created_by' => $this->session_data['id']
                    );
                    if ($this->db_model->insert(TABLE_MEMGROUPS, $data)) {
                        $success_count++;
                    }
                }
            }

            if ($success_count > 0) {
                $this->session->set_flashdata('message', array('success', "$success_count student(s) added to music group successfully!"));
            } else {
                $this->session->set_flashdata('message', array('warning', "Selected students are already in this music group."));
            }
            redirect($this->url.'/groups/group_students/'.$group_id);
        } else {
            redirect($this->url.'/groups');
        }
    }

    public function add_students_page($group_id) {
        // Check permissions - only Principal, Vice Principal, and HOD can manage group membership
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        if (!in_array($role, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD], true)) {
            $this->session->set_flashdata('message', array('danger', "You don't have permission to manage group membership."));
            redirect($this->url.'/groups');
            return;
        }

        $data["group"] = $this->db_model->get_row(TABLE_GROUPS, ["id" => $group_id, "is_active" => 1]);
        if (!$data["group"]) {
            $this->session->set_flashdata('message', array('danger', "Music Group not found."));
            redirect($this->url.'/groups');
        }

        // Get students in this group
        $group_members = $this->db_model->get_all(TABLE_MEMGROUPS, [
            'group_id' => $group_id,
            'is_active' => 1
        ]);

        $existing_student_ids = array_column($group_members, 'student_id');

        // Get all students not in this group for adding
        $conditions = ["is_active" => 1, "college_id" => $this->college['id']];

        // Filter by department for HODs and Staff
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        if ($role == ROLE_HOD || $role == ROLE_STAFF) {
            $dept_id = $this->session_data['department'];
            $conditions["department"] = $dept_id;
        }

        if (!empty($existing_student_ids)) {
            $this->db_model->db->where_not_in('id', $existing_student_ids);
        }
        $data["available_students"] = $this->db_model->get_all(TABLE_STUDENT, $conditions);

        $data["url"] = $this->url;
        $class["classname"] = "groups";
        $class["url"] = $this->url;
        $class["college"] = $this->college;

        $this->load->view('common/sidebar', $class);
        $this->load->view('faculty/groups/add_students', $data);
        $this->load->view('common/footer');
    }

    public function remove_student_from_group($group_id, $student_id) {
        // Check permissions - only Principal, Vice Principal, and HOD can manage group membership
        $role = (int) ($this->session_data['role'] ?? $this->session_data['designation'] ?? null);
        if (!in_array($role, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD], true)) {
            $this->session->set_flashdata('message', array('danger', "You don't have permission to manage group membership."));
            redirect($this->url.'/groups/group_students/'.$group_id);
            return;
        }

        $result = $this->db_model->delete(TABLE_MEMGROUPS,
            ["group_id" => $group_id, "student_id" => $student_id]
        );

        if ($result) {
            $this->session->set_flashdata('message', array('success', "Student removed from music group successfully!"));
        } else {
            $this->session->set_flashdata('message', array('danger', "Failed to remove student from music group."));
        }
        redirect($this->url.'/groups/group_students/'.$group_id);
    }
}

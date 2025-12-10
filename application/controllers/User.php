<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    private $user_session;
	function __construct() {
        parent::__construct();
        $this->common->check_user_session();
		$this->user_session= $this->session->userdata('owner');
    }
	public function index()
	{
		$this->load->view('user/view');
	}

    public function view($college_id) {


        $batch_dept_filter = $this->input->get('department_batch');

        $data['departments'] = $this->common->get_departments($college_id,$batch_dept_filter);
        $data["college_id"] = $college_id;
        $data['college'] = $this->db_model->get_row(TABLE_COLLEGE, ['is_active' => true, 'id' => $college_id]);
        $data["principals"] = $this->common->get_all_with_department(TABLE_STAFF, ["college_id" => $college_id, "is_active" => 1, "designation" => DESIGNATION_PRINCIPAL]);
        $data["hods"] = $this->common->get_all_with_department(TABLE_STAFF, ["college_id" => $college_id, "is_active" => 1, "designation" => DESIGNATION_HOD]);
        $data["facultys"] = $this->common->get_all_with_department(TABLE_STAFF, ["college_id" => $college_id, "is_active" => 1, "designation" => DESIGNATION_STAFF]);
        
        // Get batch filter from URL parameter
        $batch_filter = $this->input->get('batch');
        
        // Prepare where condition for students
        $where_students = ["college_id" => $college_id, "is_active" => 1];
        
        // Add batch filter if selected
        if (!empty($batch_filter)) {
            $where_students["batch"] = $batch_filter;
            $data['selected_batch'] = $batch_filter; 
        }
        
        // Get filtered students
        $data["students"] = $this->common->get_all_with_department(TABLE_STUDENT, $where_students);
        
        // Get all batches for dropdown
        $batches = $this->db_model->get_all(TABLE_STUDENT, ["college_id" => $college_id, "is_active" => 1]);
        $data["batches"] = array_unique(array_column($batches, 'batch'));
        $data["login_url"] = STUDENT_PORTAL_BASE_URL . '/' . $data['college']['site_url'] . "/login";
        $data['selected_student_batch'] = $this->input->get('batch');
        $data['selected_dept_batch'] = $this->input->get('department_batch');

         	$batch_years = $this->db->select('batch')
            ->distinct()
            ->from(TABLE_STUDENT)
            ->where('college_id', $college_id)
            ->where('is_active', 1)
            ->order_by('batch', 'DESC') // Show most recent batches first
            ->get()
            ->result_array();

        // Extract just the years from the results
        $years = array_column($batch_years, 'batch');
        // If you want to limit to specific years (e.g., last 5 years)
        // $years = array_slice($years, 0, 5);

        // Get student counts by department and batch year
        $departments = $this->db->select('id, name')
            ->from(TABLE_DEPARTMENT)
            ->where('college_id', $college_id)
            ->where('is_active', 1)
            ->get()
            ->result_array();

        $department_batch_counts = [];

        foreach ($departments as $dept) {
            $dept_data = [
                'name' => $dept['name'],
                'batches' => [],
                'total' => 0
            ];
            
            foreach ($years as $year) {
                $count = $this->db->where('college_id', $college_id)
                    ->where('department', $dept['id'])
                    ->where('batch', $year)
                    ->where('is_active', 1)
                    ->count_all_results(TABLE_STUDENT);
                    
                $dept_data['batches'][$year] = $count;
                $dept_data['total'] += $count;
            }
            
            $department_batch_counts[] = $dept_data;
        }

        $data['department_batch_table'] = [
            'years' => $years,
            'departments' => $department_batch_counts
        ];
        $this->common->load_view("user/view", $data);
    }

    public function add_principal($college_id){
        $post = $this->input->post(); 
        if(($post)){
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|numeric');  
            $this->form_validation->set_rules('joining_date', 'Joining Date', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
                return redirect(base_url("user/add_principal/$college_id"));
            } else {
                $staff_data = [
                    'name'         => $this->input->post('name'),
                    'email'        => $this->input->post('email'),
                    'phone_number' => $this->input->post('phone_number'),
                    'password' => $this->input->post('password'),
                    'designation'  => DESIGNATION_PRINCIPAL,
                    'department'=> $this->input->post('department'),
                    'joining_date' => $this->input->post('joining_date'),
                    'created_by'   => $this->user_session['id'],
                    'college_id'   => $college_id
                ];

                if ($this->db_model->insert(TABLE_STAFF,$staff_data)) {
                    $this->session->set_flashdata('success', array("success",'Principal added successfully!'));
                } else {
                    $this->session->set_flashdata('error', array("danger",'Failed to add Principal.'));
                }
                return redirect(base_url("user/view/$college_id"));
            }
        }else{
            $data["college_id"] = $college_id; 
            $this->common->load_view("user/add_principal",$data); 
        }

    }

    public function add_hod($college_id){
        $post = $this->input->post(); 
        if(($post)){
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|numeric'); 
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('joining_date', 'Joining Date', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
                return redirect(base_url("user/add_hod/$college_id"));
            } else {
                $staff_data = [
                    'name'         => $this->input->post('name'),
                    'email'        => $this->input->post('email'),
                    'phone_number' => $this->input->post('phone_number'),
                    'password' => $this->input->post('password'),
                    'designation'  => DESIGNATION_HOD,
                    'department'=> $this->input->post('department'),
                    'other_department' => $this->input->post('other_department') ? implode(',', $this->input->post('other_department')) : null,
                    'joining_date' => $this->input->post('joining_date'),
                    'created_by'   => $this->user_session['id'],
                    'college_id'   => $college_id
                ];

                if ($this->db_model->insert(TABLE_STAFF,$staff_data)) {
                    $this->session->set_flashdata('success', array("success",'HOD added successfully!'));
                } else {
                    $this->session->set_flashdata('error', array("danger",'Failed to add HOD.'));
                }
                return redirect(base_url("user/view/$college_id"));
            }
        }else{
            $data["college_id"] = $college_id; 
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT,["is_active"=>1,"college_id"=> $college_id]);
            $this->common->load_view("user/add_hod",$data); 
        }

    }

    public function add_faculty($college_id){
        $post = $this->input->post(); 
        if(($post)){
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|numeric'); 
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('joining_date', 'Joining Date', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
                return redirect(base_url("user/add_faculty/$college_id"));
            } else {
                $staff_data = [
                    'name'         => $this->input->post('name'),
                    'email'        => $this->input->post('email'),
                    'phone_number' => $this->input->post('phone_number'),
                    'password' => $this->input->post('password'),
                    'designation'  => DESIGNATION_STAFF,
                    'department'=> $this->input->post('department'),
                    'joining_date' => $this->input->post('joining_date'),
                    'created_by'   => $this->user_session['id'],
                    'college_id'   => $college_id
                ];

                if ($this->db_model->insert(TABLE_STAFF,$staff_data)) {
                    $this->session->set_flashdata('success', array("success",'Faculty added successfully!'));
                } else {
                    $this->session->set_flashdata('error', array("danger",'Failed to add Faculty.'));
                }
                return redirect(base_url("user/view/$college_id"));
            }
        }else{
            $data["college_id"] = $college_id; 
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT,["is_active"=>1,"college_id"=> $college_id]);

            $this->common->load_view("user/add_faculty",$data); 
        }

    }

    public function add_student($college_id){
        $post = $this->input->post(); 
        if(($post)){
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|numeric'); 
            $this->form_validation->set_rules('department', 'Department', 'trim|required');
            $this->form_validation->set_rules('joining_date', 'Joining Date', 'trim|required');
            $this->form_validation->set_rules('expire_date', 'Expire Date', 'trim|required');
            $this->form_validation->set_rules('batch', 'Batch', 'trim|required');
            $this->form_validation->set_rules('registration_number', 'Registration Number', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
                return redirect(base_url("user/add_student/$college_id"));
            } else {
                $staff_data = [
                    'name'         => $this->input->post('name'),
                    'email'        => $this->input->post('email'),
                    'phone_number' => $this->input->post('phone_number'),
                    'password' => $this->input->post('password'), 
                    'department'=> $this->input->post('department'),
                    'joining_date' => $this->input->post('joining_date'),
                    'expire_date' => $this->input->post('expire_date'),
                    'batch'=> $this->input->post('batch'),
                    'created_by'   => $this->user_session['id'],
                    'registration_number' => $this->input->post('registration_number'),
                    'college_id'   => $college_id
                ];

                if ($this->db_model->insert(TABLE_STUDENT,$staff_data)) {
                    $this->session->set_flashdata('success', array("success",'Student added successfully!'));
                } else {
                    $this->session->set_flashdata('error', array("danger",'Failed to add Student.'));
                }
                return redirect(base_url("user/view/$college_id"));
            }
        }else{
            $data["college_id"] = $college_id; 
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT,["is_active"=>1,"college_id"=> $college_id]);

            $this->common->load_view("user/add_student",$data); 
        }

    }

    public function edit_principal($college_id,$id){
        $post = $this->input->post(); 
        if(($post)){
            $this->form_validation->set_rules('name', 'Name', 'trim');
            $this->form_validation->set_rules('password', 'Password', 'trim');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|numeric'); 
            $this->form_validation->set_rules('department', 'Department', 'trim');
            $this->form_validation->set_rules('joining_date', 'Joining Date', 'trim');

            if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
                return redirect(base_url("user/view/$college_id"));
            } else {
                $staff_data = [
                    'name'         => $this->input->post('name'),
                    'email'        => $this->input->post('email'),
                    'phone_number' => $this->input->post('phone_number'),
                    'password' => $this->input->post('password'),
                    'designation'  => DESIGNATION_PRINCIPAL,
                    'department'=> $this->input->post('department'),
                    'joining_date' => $this->input->post('joining_date'),
                    'updated_by'   => $this->user_session['id']
                ];

                if ($this->db_model->update(TABLE_STAFF,$staff_data,["id"=>$id,"college_id"=>$college_id,"is_active"=>1])) {
                    $this->session->set_flashdata('success', array("success",'Principal Updated successfully!'));
                } else {
                    $this->session->set_flashdata('error', array("danger",'Failed to add Principal.'));
                }
                return redirect(base_url("user/view/$college_id"));
            }
        }else{
            $data["college_id"] = $college_id; 
            $data["staff"] = $this->db_model->get_row(TABLE_STAFF,["designation"=>DESIGNATION_PRINCIPAL,"is_active"=>1,"college_id"=>$college_id,"id"=>$id]);
            $this->common->load_view("user/add_principal",$data); 
        }

    }

    public function edit_hod($college_id,$id){
        $post = $this->input->post(); 
        if(($post)){
            $this->form_validation->set_rules('name', 'Name', 'trim');
            $this->form_validation->set_rules('password', 'Password', 'trim');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|numeric'); 
            $this->form_validation->set_rules('department', 'Department', 'trim');
            $this->form_validation->set_rules('joining_date', 'Joining Date', 'trim');

            if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
                return redirect(base_url("user/view/$college_id"));
            } else {
                $staff_data = [
                    'name'         => $this->input->post('name'),
                    'email'        => $this->input->post('email'),
                    'phone_number' => $this->input->post('phone_number'),
                    'password' => $this->input->post('password'),
                    'designation'  => DESIGNATION_HOD,
                    'department'=> $this->input->post('department'),
                    'other_department' => $this->input->post('other_department') ? implode(',', $this->input->post('other_department')) : null,
                    'joining_date' => $this->input->post('joining_date'),
                    'updated_by'   => $this->user_session['id']
                ];

                if ($this->db_model->update(TABLE_STAFF,$staff_data,["id"=>$id,"college_id"=>$college_id,"is_active"=>1])) {
                    $this->session->set_flashdata('success', array("success",'Principal Updated successfully!'));
                } else {
                    $this->session->set_flashdata('error', array("danger",'Failed to add Principal.'));
                }
                return redirect(base_url("user/view/$college_id"));
            }
        }else{
            $data["college_id"] = $college_id; 
            $data["staff"] = $this->db_model->get_row(TABLE_STAFF,["designation"=>DESIGNATION_HOD,"is_active"=>1,"college_id"=>$college_id,"id"=>$id]);
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT,["is_active"=>1,"college_id"=> $college_id]);
            $this->common->load_view("user/add_hod",$data); 
        }

    }

    public function edit_faculty($college_id,$id){
        $post = $this->input->post(); 
        if(($post)){
            $this->form_validation->set_rules('name', 'Name', 'trim');
            $this->form_validation->set_rules('password', 'Password', 'trim');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|numeric'); 
            $this->form_validation->set_rules('department', 'Department', 'trim');
            $this->form_validation->set_rules('joining_date', 'Joining Date', 'trim');

            if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
                return redirect(base_url("user/view/$college_id"));
            } else {
                $staff_data = [
                    'name'         => $this->input->post('name'),
                    'email'        => $this->input->post('email'),
                    'phone_number' => $this->input->post('phone_number'),
                    'password' => $this->input->post('password'),
                    'designation'  => DESIGNATION_STAFF,
                    'department'=> $this->input->post('department'),
                    'joining_date' => $this->input->post('joining_date'),
                    'updated_by'   => $this->user_session['id']
                ];

                if ($this->db_model->update(TABLE_STAFF,$staff_data,["id"=>$id,"college_id"=>$college_id,"is_active"=>1])) {
                    $this->session->set_flashdata('success', array("success",'Faculty Updated successfully!'));
                } else {
                    $this->session->set_flashdata('error', array("danger",'Failed to add Faculty.'));
                }
                return redirect(base_url("user/view/$college_id"));
            }
        }else{
            $data["college_id"] = $college_id; 
            $data["staff"] = $this->db_model->get_row(TABLE_STAFF,["designation"=>DESIGNATION_STAFF,"is_active"=>1,"college_id"=>$college_id,"id"=>$id]);
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT,["is_active"=>1,"college_id"=> $college_id]);
            $this->common->load_view("user/add_faculty",$data); 
        }

    }

    public function edit_student($college_id,$id){
        $post = $this->input->post(); 
        if(($post)){
            $this->form_validation->set_rules('name', 'Name', 'trim');
            $this->form_validation->set_rules('password', 'Password', 'trim');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|numeric'); 
            $this->form_validation->set_rules('department', 'Department', 'trim');
            $this->form_validation->set_rules('joining_date', 'Joining Date', 'trim');
            $this->form_validation->set_rules('exipre_date', 'Expire Date', 'trim');
            $this->form_validation->set_rules('batch', 'Batch', 'trim');
            $this->form_validation->set_rules('registration_number', 'Registration Number', 'trim');

            if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('message',array("danger",validation_errors())); 
                return redirect(base_url("user/view/$college_id"));
            } else {
                $staff_data = [
                    'name'         => $this->input->post('name'),
                    'email'        => $this->input->post('email'),
                    'phone_number' => $this->input->post('phone_number'),
                    'password' => $this->input->post('password'), 
                    'department'=> $this->input->post('department'),
                    'joining_date' => $this->input->post('joining_date'),
                    'expire_date' => $this->input->post('expire_date'),
                    'batch' => $this->input->post('batch'),
                    'registration_number' => $this->input->post('registration_number'),
                    'updated_by'   => $this->user_session['id']
                ];

                if ($this->db_model->update(TABLE_STUDENT,$staff_data,["id"=>$id,"college_id"=>$college_id,"is_active"=>1])) {
                    $this->session->set_flashdata('success', array("success",'Student Updated successfully!'));
                } else {
                    $this->session->set_flashdata('error', array("danger",'Failed to add Student.'));
                }
                return redirect(base_url("user/view/$college_id"));
            }
        }else{
            $data["college_id"] = $college_id; 
            $data["staff"] = $this->db_model->get_row(TABLE_STUDENT,["is_active"=>1,"college_id"=>$college_id,"id"=>$id]);
            $data["departments"] = $this->db_model->get_all(TABLE_DEPARTMENT,["is_active"=>1,"college_id"=> $college_id]);
            $this->common->load_view("user/add_student",$data); 
        }

    }

    public function delete_principal($college_id,$id){
        $result=$this->db_model->update(TABLE_STAFF,["is_active"=>0],["designation"=>DESIGNATION_PRINCIPAL,"id"=>$id,"college_id"=>$college_id]);
		$message = array('success',"Principal Deleted SuccessFully");
		if(!$result){
			$message = array('danger',"Something went wrong");
		}
		$this->session->set_flashdata('message', $message); 
		redirect(base_url("user/view/$college_id"));
    }

    public function delete_hod($college_id,$id){
        $result=$this->db_model->update(TABLE_STAFF,["is_active"=>0],["designation"=>DESIGNATION_HOD,"id"=>$id,"college_id"=>$college_id]);
		$message = array('success',"HOD Deleted SuccessFully");
		if(!$result){
			$message = array('danger',"Something went wrong");
		}
		$this->session->set_flashdata('message', $message); 
		redirect(base_url("user/view/$college_id"));
    }

    public function delete_faculty($college_id,$id){
        $result=$this->db_model->update(TABLE_STAFF,["is_active"=>0],["designation"=>DESIGNATION_STAFF,"id"=>$id,"college_id"=>$college_id]);
		$message = array('success',"Faculty Deleted SuccessFully");
		if(!$result){
			$message = array('danger',"Something went wrong");
		}
		$this->session->set_flashdata('message', $message); 
		redirect(base_url("user/view/$college_id"));
    }

    public function delete_student($college_id,$id){
        $result=$this->db_model->update(TABLE_STUDENT,["is_active"=>0],["id"=>$id,"college_id"=>$college_id]);
		$message = array('success',"Student Deleted SuccessFully");
		if(!$result){
			$message = array('danger',"Something went wrong");
		}
		$this->session->set_flashdata('message', $message); 
		redirect(base_url("user/view/$college_id"));
    }

    public function bulk_add_principal($college_id){ 
            $file_path= $this->common->upload_csv();  
            if(isset($file_path)){  
                $csvData = $this->common->read_csv(base_url("assets/uploads/CSV/$file_path")); 
                $data= $this->mapCsvToTableColumns($csvData,['name', 'email', 'phone_number', 'password']);
                foreach ($data as &$row) {
                    $row['college_id'] = $college_id;      // Add college_id
                    $row['created_by'] = $this->user_session["id"];  // Add created_by
                    $row['designation'] = DESIGNATION_PRINCIPAL;    // Add designation
                    $row['file_path'] = $file_path;
                } 
                $result=$this->db_model->bulk_insert(TABLE_STAFF,$data);
                if($result){
                    echo "success";
                }else{
                    echo "error";
                }
            } 
    } 

    public function bulk_add_users($college_id, $user_type) {
        $file_path = $this->common->upload_csv();  
        if(isset($file_path)) {  
            $config = [
                'student' => [
                    'table' => TABLE_STUDENT,
                    'columns' => ['name', 'department', 'email', 'phone_number', 'password', 'joining_date', "expire_date","batch","registration_number"],
                    'extra_data' => ['college_id' => $college_id, 'created_by' => $this->user_session["id"], 'file_path' => $file_path],
                    'operation' => 'bulk_upsert',
                    'unique_fields' => ['email'],
                    'designation' => null
                ],
                'hod' => [
                    'table' => TABLE_STAFF,
                    'columns' => ['name', 'department', 'other_department', 'email', 'phone_number', 'password', 'joining_date'],
                    'extra_data' => ['college_id' => $college_id, 'created_by' => $this->user_session["id"], 'file_path' => $file_path, 'designation' => DESIGNATION_HOD],
                    'operation' => 'bulk_upsert',
                    'unique_fields' => ['email'],
                    'designation' => DESIGNATION_HOD
                ],
                'faculty' => [
                    'table' => TABLE_STAFF,
                    'columns' => ['name', 'department', 'email', 'phone_number', 'password', 'joining_date'],
                    'extra_data' => ['college_id' => $college_id, 'created_by' => $this->user_session["id"], 'file_path' => $file_path, 'designation' => DESIGNATION_STAFF],
                    'operation' => 'bulk_upsert',
                    'unique_fields' => ['email'],
                    'designation' => DESIGNATION_STAFF
                ],
                'principal' => [ 
                    'table'=> TABLE_STAFF,
                    'columns'=> ['name', 'email', 'phone_number', 'password','joining_date'],
                    'extra_data'=> ['college_id' => $college_id, 'created_by' => $this->user_session["id"], 'file_path' => $file_path, 'designation' => DESIGNATION_PRINCIPAL],
                    'operation'=> 'bulk_upsert',
                    'unique_fields'=> ['email'],
                    'designation' => DESIGNATION_PRINCIPAL
                ]
            ];
    
            if (!isset($config[$user_type])) {
                echo json_encode(["message" => "error", "error" => "Invalid user type"]);
                return;
            }
    
            $current_config = $config[$user_type];
            
            
            $csvData = $this->common->read_csv(base_url("assets/uploads/CSV/$file_path")); 

            if (empty($csvData['headers']) || empty($csvData['values'])) {
                echo json_encode([
                    "message" => "error",
                    "error" => "Empty or invalid CSV file"
                ]);
                return;
            }

            $missingColumns = array_diff($current_config['columns'], $csvData['headers']);
            
            if (!empty($missingColumns)) {
                echo json_encode([
                    "message" => "error",
                    "error" => "Missing required columns: " . implode(', ', $missingColumns)
                ]);
                return;
            }

            $data = $this->mapCsvToTableColumns($csvData, $current_config['columns']);
            
            foreach ($data as &$row) {
                $row = array_merge($row, $current_config['extra_data']);
            }
            unset($row);

            $success_rows = [];
            $failed_rows = [];
    
            // Special handling for principal vs. other user types
            if ($current_config['designation'] !== DESIGNATION_PRINCIPAL) {
                // Department handling for non-principal users
                $departments = $this->db_model->get_all(TABLE_DEPARTMENT, ["is_active" => 1, "college_id" => $college_id]);
                $dept_map = [];
                foreach ($departments as $dept) {
                    $dept_map[strtolower(trim($dept['name']))] = $dept['id'];
                    $dept_map[$dept['id']] = $dept['id'];
                }
    
                foreach ($data as $index => $row) {
                    // Check if department exists in this row
                    if (isset($row['department'])) {
                        $dept_input = strtolower(trim($row['department']));
                        
                        if (isset($dept_map[$dept_input])) {
                            $row['department'] = $dept_map[$dept_input];
                            
                            // Process other_department if it exists in this row
                            if (isset($row['other_department']) && !empty($row['other_department'])) {
                                $other_department = strtolower(trim($row['other_department']));
                                // convert string to array
                                $other_department = explode(",", $other_department);
                                // map department names to ids
                                $other_department = array_map(function($dept) use ($dept_map) {
                                    $dept = trim($dept);
                                    return isset($dept_map[$dept]) ? $dept_map[$dept] : null;
                                }, $other_department);
                                // remove null values
                                $other_department = array_filter($other_department);
                                // convert array to string
                                $other_department = implode(",", $other_department);    
                                $row['other_department'] = $other_department;
                            }
                            
                            $success_rows[] = $row;
                        } else {
                            $row['error'] = "Invalid department: " . $row['department'];
                            $failed_rows[] = $row;
                        }
                    } else {
                        $row['error'] = "Department is required";
                        $failed_rows[] = $row;
                    }
                }
            } else {
                // For principals, no department validation needed
                $success_rows = $data;
            }
            
            $report = [
                'total_rows' => count($data),
                'success_rows' => count($success_rows),
                'failed_rows' => count($failed_rows),
                'failed_data' => $failed_rows,
                'file_path' => $file_path
            ];
            
            $this->session->set_flashdata('bulk_upload_report', $report);
            
            if (!empty($success_rows)) {
                $result = $this->db_model->{$current_config['operation']}(
                    $current_config['table'], 
                    $success_rows,
                    $current_config['unique_fields']
                );
            }
            
            echo json_encode([
                "message" => !empty($failed_rows) ? "partial" : "success",
                "error_count" => count($failed_rows),
                "report_url" => base_url("user/upload_report"),
            ]);
            return;
        } 
    }
    
    public function upload_report() {
        $data['report'] = $this->session->flashdata('bulk_upload_report');
        $this->load->view('user/upload_report', $data);
    }


    private function mapCsvToTableColumns($csvData, $columnArray) { 
        $csvHeaders = $csvData["headers"];
        $csvHeaders =array_map('strtolower', $csvHeaders); 
        $mappedValues = []; 
        foreach ($csvData["values"] as $row) { 
    
            foreach ($columnArray as $column) { 
                $columnIndex = array_search($column, $csvHeaders);
    
                if ($columnIndex !== false) { 
                    $mappedRow[$column] = $row[$columnIndex];
                } else {
                    $mappedRow[$column] = null;
                }
            } 
            $mappedValues[] = $mappedRow;
        }
    
        return $mappedValues;
    }
}

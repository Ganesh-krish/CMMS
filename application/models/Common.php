<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class common extends CI_Model {

    private $user_session;

	function __construct() { 
        $this->load->model("db_model");
        $this->user_session = $this->session->userdata("owner");
         
    }

    public function load_view($view,$data=[]){ 
        // Default loader; views that need sidebar vars should pass them via $data
        $this->load->view("faculty/faculty/sidebar",$data);
        $this->load->view($view,$data);
        $this->load->view("faculty/faculty/footer");
    }

	public function check_user_session()
	{  
        if(empty($this->user_session)){ 
            $this->session->unset_userdata('owner'); 
            redirect(base_url("OAuth"));
        }
        $user=$this->db_model->get_row(TABLE_OWNER,['id'=>$this->user_session['id'],"is_active"=>1]);  

        if(empty($user)) {
            $this->session->unset_userdata('owner'); 
            redirect(base_url("OAuth/access_denied"));
        }
        $this->session->unset_userdata('owner'); 
        $this->session->set_userdata('owner', $user);
	}

    public function upload()
    {
        if (!empty($_FILES['image']['name'])) {
            $config['upload_path'] = 'assets/uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|svg|';
            $config['max_size'] = 2000;
            $config['file_name'] = md5($_FILES['image']['name'] . date("dmYHis"));

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            $uploadFile = $this->upload->do_upload('image');
            if ($uploadFile) {
                $uploadData = $this->upload->data();
                $picture = $uploadData['file_name'];
            }
            // else {
            // 	$this->session->set_flashdata('message', array('warning', $this->upload->display_errors()));
            // }
            return $picture;
        }
    }

    public function upload_csv() {
        // Set upload configuration
        $config['upload_path'] = 'assets/uploads/CSV'; // Path where files will be stored
        $config['allowed_types'] = 'csv';     // Only allow CSV files
        $config['max_size'] = 2048;            // Max file size (in KB)
        $config['file_name'] = md5($_FILES['csvFile']['name'] . date("dmYHis"));
        
        // Initialize upload library with config
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload('csvFile')) {
            // File uploaded successfully
            $data = $this->upload->data();
            return $data['file_name'];
        } else { 
            return false;
        }
    }

    public function read_csv($filePath) { 
        $headers = [];
        $values = [];
        
        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            // Read the first row for headers
            $headers = fgetcsv($handle, 1000, ',');
            
            // Read remaining rows for values
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $values[] = $data; // Add each row of data to the values array
            }
            fclose($handle); // Close the file after reading
        }

        return ['headers' => $headers, 'values' => $values];
    } 

    public function display_date($date){
        $formatted_date = date("M d, Y", strtotime($date));
        echo $formatted_date;
    }

    public function get_department($id){
        $data = $this->db_model->get_row(TABLE_DEPARTMENT,["id"=>$id,"is_active"=>1]);
        return $data;
    }
    // public function get_departments($id){
    //     $query = $this->db->query("
    //     SELECT 
    //         d.department,
    //         COALESCE(s.student_count, 0) AS student_count,
    //         COALESCE(h.hod_count, 0) AS hod_count,
    //         COALESCE(t.staff_count, 0) AS staff_count
    //     FROM (
    //         -- Get unique department names from both tables
    //         SELECT DISTINCT department FROM staffs WHERE is_active = 1 AND college_id = ? AND department IS NOT NULL
    //         UNION
    //         SELECT DISTINCT department FROM students WHERE is_active = 1 AND college_id = ? AND department IS NOT NULL
    //     ) AS d
    //     LEFT JOIN (
    //         -- Count active students per department
    //         SELECT department, COUNT(id) AS student_count
    //         FROM students
    //         WHERE is_active = 1 AND college_id = ?
    //         GROUP BY department
    //     ) AS s ON d.department = s.department
    //     LEFT JOIN (
    //         -- Count active HODs per department
    //         SELECT department, COUNT(id) AS hod_count
    //         FROM staffs
    //         WHERE is_active = 1 AND college_id = ? AND designation = ?
    //         GROUP BY department
    //     ) AS h ON d.department = h.department
    //     LEFT JOIN (
    //         -- Count active staff per department
    //         SELECT department, COUNT(id) AS staff_count
    //         FROM staffs
    //         WHERE is_active = 1 AND college_id = ? AND designation = ?
    //         GROUP BY department
    //     ) AS t ON d.department = t.department
    // ", [$id, $id, $id, $id,DESIGNATION_HOD,$id,DESIGNATION_STAFF]);
    // $result = $query->result_array();
    // return $result;
    // }

    public function get_departments($college_id, $batch = null) {
        // Base SQL query
        $sql = "
            SELECT 
                d.id AS id,
                d.name AS department,
                COALESCE(s.student_count, 0) AS student_count,
                COALESCE(h.hod_count, 0) AS hod_count,
                COALESCE(t.staff_count, 0) AS staff_count
            FROM 
                departments d
            LEFT JOIN (
                -- Count active students per department
                SELECT department, COUNT(id) AS student_count
                FROM students
                WHERE is_active = 1 AND college_id = ?";
        
        // Parameters for the query
        $params = [$college_id];
        
        // Add batch filter if present
        if ($batch) {
            $sql .= " AND batch = ?";
            $params[] = $batch;
        }
        
        // Continue with the rest of the query
        $sql .= "
                GROUP BY department
            ) AS s ON d.id = s.department
            LEFT JOIN (
                -- Count active HODs per department
                SELECT department, COUNT(id) AS hod_count
                FROM staffs
                WHERE is_active = 1 AND college_id = ? AND designation = ?
                GROUP BY department
            ) AS h ON d.id = h.department
            LEFT JOIN (
                -- Count active staff per department
                SELECT department, COUNT(id) AS staff_count
                FROM staffs
                WHERE is_active = 1 AND college_id = ? AND designation = ?
                GROUP BY department
            ) AS t ON d.id = t.department
            WHERE 
                d.college_id = ? AND d.is_active = 1
            ORDER BY 
                d.name ASC
        ";
        
        // Add remaining parameters
        $params = array_merge($params, [
            $college_id, 
            DESIGNATION_HOD, 
            $college_id, 
            DESIGNATION_STAFF, 
            $college_id
        ]);
        
        $query = $this->db->query($sql, $params);
        
        if ($query === false) {
            $error = $this->db->error();
            log_message('error', 'Database error: ' . $error['message']);
            return [];
        }
    
        $result = $query->result_array();
        return !empty($result) ? $result : [];
    }

    public function get_all_with_department($table, $where = []) {
        $query = $this->db->select($table.'.*, d.name as department')
        ->from($table)
        ->join('departments d', $table.'.department = d.id', 'left');
                foreach ($where as $key => $value) {
            $query->where($table.'.'.$key, $value);
        }
        return $query->get()->result_array();
    }


}

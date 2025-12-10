<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends CI_Controller {

    private $user_session;

    function __construct() {
        parent::__construct();
        $this->common->check_user_session();
        $this->user_session = $this->session->userdata('owner');
    }

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $limit = $this->input->get('length') ?? 50;
            $offset = $this->input->get('start')?? 0;
            $search_value = $this->input->get('search')['value'];
            $college_filter = $this->input->get('college_id');

            $joins = [
                TABLE_COLLEGE . ' AS c' => 'c.id = co.college_id'
            ];

            $select = "co.id, co.course_code, co.name, co.description, co.tag, co.course_mode,co.course_type,co.created_at,co.college_id AS creator_college_id, c.name AS college_name";

            $conditions = ['co.is_active' => 1];
            if (!empty($college_filter)) {
                $college_ids = explode(',', $college_filter); // split string into array
            $this->db->where_in('co.college_id', $college_ids);
                // $conditions['co.college_id'] = $college_filter;
            }

            if (!empty($search_value)) {
                $this->db->group_start()
                    ->like('co.course_code', $search_value)
                    ->or_like('co.name', $search_value)
                    ->or_like('co.description', $search_value)
                    ->or_like('co.tag', $search_value)
                    ->or_like('c.name', $search_value)
                    ->group_end();
            }

            $data = $this->db_model->get_with_joins(
                TABLE_COURCES . ' AS co',
                $select,
                $joins,
                $conditions,
                'co.id',
                'DESC',
                null,
                $limit,
                $offset
            );

            $total = $this->db_model->get_with_joins(
                TABLE_COURCES . ' AS co',
                'COUNT(co.id) as count',
                $joins,
                $conditions
            );

             // Decode constants into arrays with IDs as keys for faster lookup
        $courseTypes = json_decode(COURSE_TYPES, true);
        $courseModes = json_decode(COURSE_MODES, true);
        
        foreach ($data as &$row) {
            $row['created_at'] = date("M d, Y", strtotime($row['created_at']));

            // Map Course Type
            if (!empty($row['course_type']) && isset($courseTypes[$row['course_type']])) {
                $row['course_type_name']  = $courseTypes[$row['course_type']]['name'];
                $row['course_type_color'] = $courseTypes[$row['course_type']]['color'];
            } else {
                $row['course_type_name']  = null;
                $row['course_type_color'] = null;
            }

            // Map Course Mode
            if (!empty($row['course_mode']) && isset($courseModes[$row['course_mode']])) {
                $row['course_mode_name']  = $courseModes[$row['course_mode']]['name'];
                $row['course_mode_color'] = $courseModes[$row['course_mode']]['color'];
            } else {
                $row['course_mode_name']  = null;
                $row['course_mode_color'] = null;
            }
        }

            echo json_encode([
                'draw' => intval($this->input->get('draw')),
                'recordsTotal' => $total[0]['count'],
                'recordsFiltered' => $total[0]['count'],
                'data' => $data
            ]);
            return;
        }

        $this->common->load_view("course/view");
    }

    public function add_colleges($course_id)
    {
        // $data['course_id'] = $course_id; 
        $course_details = $this->db_model->get_row(
            TABLE_COURCES,
            ['id'=>$course_id,'is_active' => 1],
            'id,course_code,name,description,college_id'
        );

        $owned_college = null;

        if (!empty($course_details) && !empty($course_details['college_id'])) {
            $owned_college = $this->db_model->get_row(
                TABLE_COLLEGE,
                ['id' => $course_details['college_id'],'is_active'=>1],
                'id,name'
            );
        }
        $data['course'] = $course_details;
        $data['creator'] = $owned_college;

        $this->common->load_view('course/add_colleges', $data);
    }


    /**
     * AJAX endpoint: all colleges
     */
    public function get_colleges()
    {
        $colleges = $this->db_model->get_all(
            TABLE_COLLEGE,
            ['is_active' => 1],
            'id, name, email, logo, created_at'
        );
        echo json_encode($colleges);
    }

    /**
     * AJAX endpoint: shared colleges for a course
     */
    public function get_shared_colleges($course_id)
    {   
        $joins = [
            TABLE_COLLEGE . ' AS c' => 'c.id = sc.to_college_id'
        ];
        $conditions = [
            'sc.course_id' => $course_id,
            'sc.is_active' => 1
        ];
        $select = "sc.id, sc.to_college_id, c.name AS college_name, c.email, c.logo, sc.created_at";

         $shared_colleges = $this->db_model->get_with_joins(
            TABLE_SPECIAL_COURCES . ' AS sc',
            $select,
            $joins,
            $conditions
        );
       
        echo json_encode($shared_colleges);
    }


    public function assign_course()
    {
        $course_id = $this->input->post('course_id');
        $college_id = $this->input->post('college_id'); // single college
        
        $creator = $this->user_session['id'];

        if (!$course_id || !$college_id) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        // Get course info
        $course = $this->db_model->get_row('courses', ['id' => $course_id,'is_active'=>1]);
      
        //  Check if the college already owns the course
        if (!empty($course) && $course['college_id'] == $college_id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Course already owned by this college'
            ]);
            return;
        }

        //  Check if course was already shared with this college
        $shared_condition = [
            'course_id' => $course_id,
            'to_college_id' => $college_id,
            'is_active' => 1
        ];
        // print_r($shared_condition);
        // exit;
        $shared = $this->db_model->get_row(TABLE_SPECIAL_COURCES, $shared_condition);
        if (!empty($shared)) {
            $college = $this->db_model->get_row(TABLE_COLLEGE, ['id' => $college_id,'is_active'=>1]);
            echo json_encode([
                'status' => 'error',
                'message' => 'Course already assigned to: ' . ($college['name'] ?? "College ID: $college_id")
            ]);
            return;
        }

        //  Assign course to the college
        $assign_data = [
            'course_id' => $course_id,
            'to_college_id' => $college_id,
            'shared_by_owner_id' => $creator
        ];

        $this->db_model->insert(TABLE_SPECIAL_COURCES, $assign_data);

        echo json_encode(['status' => 'success', 'message' => 'Course assigned successfully']);
    }

    public function remove_course_assign($course_id, $college_id)
    {
        // Check if the course was shared with this college
        $shared = $this->db_model->get_row(
            TABLE_SPECIAL_COURCES,
            ['course_id' => $course_id, 'to_college_id' => $college_id, 'is_active' => 1],
        );

        if (empty($shared)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No active shared record found for this college'
            ]);
            return;
        }

        // Update is_active to 0
        $updated = $this->db_model->update(
            TABLE_SPECIAL_COURCES,
            ['is_active' => 0],
            ['id' => $shared['id']]
        );

        if ($updated) {
            echo json_encode([
                'status' => 'success',
                'message' => 'College removed successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to remove college'
            ]);
        }
    }


public function get_exclude_colleges($course_id, $creator_college_id)
{
    $query = "
        SELECT c.id,c.name,c.email,c.created_at
        FROM " . TABLE_COLLEGE . " c
        WHERE c.is_active = 1
          AND c.id != ?
          AND c.id NOT IN (
              SELECT sc.to_college_id
              FROM special_courses sc
              WHERE sc.course_id = ? AND sc.is_active = 1
          )
    ";

    $colleges = $this->db_model->custom_query($query, [
        $creator_college_id, // exclude creator/owner
        $course_id           // exclude already shared colleges
    ]);
    // return $colleges;
    // $this->load->view('course/add_colleges', $colleges);
    echo json_encode($colleges);
}







}
